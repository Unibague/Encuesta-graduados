<?php

/**
 * Lista manual de graduados ("IngresoGraduados" en Google Sheets). El
 * administrador la usa para agregar, a mano, a las personas que valida como
 * graduados o egresados pero que ni SIGA ni el endpoint alterno reconocen.
 * Esta funcion solo consulta esa hoja; el registro en ella lo hace el
 * administrador directamente en Google Sheets.
 */
function consultarListaManualGraduados(
    string $identificationNumber,
    string $spreadsheetId = '1D7lXrJadn6m7KJH10vHYxrvs5FqQr194s4jFccxPaoA'
): object {
    $targetGid = 0;

    try {
        $client = new Google_Client();
        $client->setAuthConfig(listaManualGraduadosCredentialsPath());
        $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);

        $service = new Google_Service_Sheets($client);
        $spreadsheet = $service->spreadsheets->get($spreadsheetId);
        $sheetName = null;

        foreach ($spreadsheet->getSheets() as $sheet) {
            if ((int) $sheet->getProperties()->getSheetId() === $targetGid) {
                $sheetName = $sheet->getProperties()->getTitle();
                break;
            }
        }

        if (!$sheetName) {
            return (object) [
                'available' => false,
                'eligible' => false,
                'data' => [],
                'error' => "No se encontró la hoja con gid={$targetGid}",
            ];
        }

        $response = $service->spreadsheets_values->get($spreadsheetId, "{$sheetName}!A1:Z");
        $filas = $response->getValues() ?? [];

        foreach ($filas as $i => $fila) {
            if ($i === 0) continue; // encabezado

            // La cedula puede estar en cualquier columna de la fila.
            $celdas = array_map(fn ($celda) => preg_replace('/\D+/', '', (string) $celda), $fila);
            if (in_array($identificationNumber, $celdas, true)) {
                return (object) [
                    'available' => true,
                    'eligible' => true,
                    // La columna A solo se toma como nombre si no es la cedula.
                    'data' => $celdas[0] === $identificationNumber
                        ? []
                        : array_filter(['nombres' => trim((string) ($fila[0] ?? ''))]),
                    'error' => '',
                ];
            }
        }

        return (object) ['available' => true, 'eligible' => false, 'data' => [], 'error' => ''];
    } catch (Throwable $e) {
        return (object) [
            'available' => false,
            'eligible' => false,
            'data' => [],
            'error' => $e->getMessage(),
        ];
    }
}

function listaManualGraduadosCredentialsPath(): string
{
    $configuredPath = trim((string) getenv('GOOGLE_CREDENTIALS_PATH'));

    if ($configuredPath !== '' && is_file($configuredPath)) {
        return $configuredPath;
    }

    $localPath = dirname(__DIR__) . '/credentials.json';
    if (is_file($localPath)) {
        return $localPath;
    }

    throw new RuntimeException('No se encontró el archivo de credenciales de Google Sheets.');
}
