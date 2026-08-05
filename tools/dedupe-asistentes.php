<?php
/**
 * Elimina filas duplicadas en el Sheet del Encuentro de Graduados 2026,
 * dejando solo la última fila (la más reciente) de cada persona.
 * A diferencia del .gs, este script imprime nombre/correo de cada fila
 * que borra, no solo el número de fila.
 *
 * Uso:
 *   php tools/dedupe-asistentes.php --dry-run   (solo muestra qué borraría)
 *   php tools/dedupe-asistentes.php             (borra de verdad)
 */

require __DIR__ . '/../vendor/autoload.php';

$spreadsheetId = '1LockLyDz0texEzDypaRyhqL1uniy4Fpus_FPCPOv2Ec';
$firstDataRow  = 4;
$dryRun        = in_array('--dry-run', $argv, true);

$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/../credentials.json');
$client->addScope(Google_Service_Sheets::SPREADSHEETS);
$service = new Google_Service_Sheets($client);

$spreadsheet = $service->spreadsheets->get($spreadsheetId);

$sheetAsistentes = null;
$sheetNoAsistentes = null;
foreach ($spreadsheet->getSheets() as $sheet) {
    $props = $sheet->getProperties();
    if ((int) $props->getSheetId() === 1419700379) {
        $sheetAsistentes = $props;
    }
    if (mb_strtolower($props->getTitle(), 'UTF-8') === 'no asistentes') {
        $sheetNoAsistentes = $props;
    }
}

if ($sheetAsistentes) {
    procesarHoja($service, $spreadsheetId, $sheetAsistentes->getSheetId(), $sheetAsistentes->getTitle(), $firstDataRow, 5, $dryRun);
} else {
    echo "No se encontró la hoja de asistentes (gid 1419700379).\n";
}

if ($sheetNoAsistentes) {
    procesarHoja($service, $spreadsheetId, $sheetNoAsistentes->getSheetId(), $sheetNoAsistentes->getTitle(), $firstDataRow, 3, $dryRun);
} else {
    echo "No se encontró la hoja \"No asistentes\".\n";
}

function procesarHoja($service, $spreadsheetId, $sheetId, $sheetName, $firstDataRow, $numCols, $dryRun)
{
    $lastColLetter = range('A', 'Z')[$numCols - 1];
    $range = "{$sheetName}!A{$firstDataRow}:{$lastColLetter}";
    $resp  = $service->spreadsheets_values->get($spreadsheetId, $range);
    $rows  = $resp->getValues() ?? [];

    if (!$rows) {
        echo "[{$sheetName}] sin datos.\n\n";
        return;
    }

    $ultimaFilaPorClave = [];
    $dataPorFila = [];
    foreach ($rows as $i => $fila) {
        $numeroFila = $firstDataRow + $i;
        $dataPorFila[$numeroFila] = $fila;
        $clave = normalizarClave($fila);
        if ($clave === '') continue;
        $ultimaFilaPorClave[$clave] = $numeroFila; // se sobreescribe -> queda la última aparición
    }

    $filasABorrar = [];
    foreach ($rows as $i => $fila) {
        $numeroFila = $firstDataRow + $i;
        $clave = normalizarClave($fila);
        if ($clave === '') continue;
        if ($ultimaFilaPorClave[$clave] !== $numeroFila) {
            $filasABorrar[] = $numeroFila;
        }
    }

    if (!$filasABorrar) {
        echo "[{$sheetName}] no se encontraron duplicados.\n\n";
        return;
    }

    echo "[{$sheetName}] " . count($filasABorrar) . " fila(s) duplicada(s):\n";
    foreach ($filasABorrar as $numeroFila) {
        $fila   = $dataPorFila[$numeroFila];
        $nombre = $fila[0] ?? '';
        $correo = $fila[2] ?? '';
        echo "  - Fila {$numeroFila}: {$nombre} | {$correo}\n";
    }

    if ($dryRun) {
        echo "[{$sheetName}] (dry-run) no se eliminó nada.\n\n";
        return;
    }

    rsort($filasABorrar);
    $requests = [];
    foreach ($filasABorrar as $numeroFila) {
        $requests[] = new Google_Service_Sheets_Request([
            'deleteDimension' => [
                'range' => [
                    'sheetId'    => $sheetId,
                    'dimension'  => 'ROWS',
                    'startIndex' => $numeroFila - 1,
                    'endIndex'   => $numeroFila,
                ],
            ],
        ]);
    }

    $body = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(['requests' => $requests]);
    $service->spreadsheets->batchUpdate($spreadsheetId, $body);

    echo "[{$sheetName}] eliminadas.\n\n";
}

function normalizarClave(array $fila): string
{
    $correo = mb_strtolower(trim($fila[2] ?? ''), 'UTF-8');
    if ($correo !== '') return 'correo:' . $correo;

    $nombre = mb_strtolower(trim($fila[0] ?? ''), 'UTF-8');
    $nombre = preg_replace('/\s+/', ' ', $nombre);
    return $nombre !== '' ? 'nombre:' . $nombre : '';
}
