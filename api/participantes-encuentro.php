<?php
require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$spreadsheetId = '1LockLyDz0texEzDypaRyhqL1uniy4Fpus_FPCPOv2Ec';
$targetGid     = 1419700379;

try {
    $client = new Google_Client();
    $client->setAuthConfig(getenv('GOOGLE_CREDENTIALS_PATH'));
    $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);

    $service = new Google_Service_Sheets($client);

    // Obtener nombre de la hoja por su gid
    $spreadsheet = $service->spreadsheets->get($spreadsheetId);
    $sheetName   = null;
    foreach ($spreadsheet->getSheets() as $sheet) {
        if ((int) $sheet->getProperties()->getSheetId() === $targetGid) {
            $sheetName = $sheet->getProperties()->getTitle();
            break;
        }
    }

    if (!$sheetName) {
        throw new RuntimeException('Hoja no encontrada en el Sheet.');
    }

    // Leer columnas A (Nombre) y B (¿Asistió?) desde fila 4 en adelante
    $response = $service->spreadsheets_values->get(
        $spreadsheetId,
        $sheetName . '!A4:B'
    );
    $filas = $response->getValues() ?? [];

    $participantes = [];
    foreach ($filas as $fila) {
        $nombre  = trim($fila[0] ?? '');
        $asistio = mb_strtolower(trim($fila[1] ?? ''), 'UTF-8');

        // Solo incluir si columna B dice "sí" o "si"
        if ($nombre !== '' && in_array($asistio, ['sí', 'si'], true)) {
            $participantes[] = $nombre;
        }
    }

    echo json_encode(['participantes' => $participantes, 'fuente' => 'sheet']);

} catch (Throwable $e) {
    // Fallback: leer de la base de datos (registrados con asistencia = 'si')
    error_log('[participantes-encuentro] Sheets falló: ' . $e->getMessage() . ' — usando BD.');

    $db     = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
    $result = $db->makeQuery("
        SELECT nombres, apellidos
        FROM encuentro_2026
        WHERE asistencia = 'si'
        ORDER BY created_at ASC
    ");

    $participantes = [];
    while ($row = $result->fetch_assoc()) {
        $participantes[] = trim($row['nombres'] . ' ' . $row['apellidos']);
    }

    echo json_encode(['participantes' => $participantes, 'fuente' => 'bd_fallback']);
}
