<?php

const ENCUENTRO_SHEET_ID            = '1LockLyDz0texEzDypaRyhqL1uniy4Fpus_FPCPOv2Ec';
const ENCUENTRO_SHEET_ASISTENTES_GID = 1419700379;

/**
 * Devuelve los nombres completos de quienes confirmaron asistencia ("Sí")
 * en el Google Sheet del Encuentro (la fuente de la verdad para el cupo y
 * los juegos), o null si no fue posible consultarlo, para que el llamador
 * pueda usar la base de datos como respaldo.
 */
function obtenerAsistentesConfirmadosSheet(): ?array
{
    try {
        $client = new Google_Client();
        $client->setAuthConfig(googleCredentialsPath());
        $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);

        $service = new Google_Service_Sheets($client);

        $spreadsheet = $service->spreadsheets->get(ENCUENTRO_SHEET_ID);
        $sheetName   = null;
        foreach ($spreadsheet->getSheets() as $sheet) {
            if ((int) $sheet->getProperties()->getSheetId() === ENCUENTRO_SHEET_ASISTENTES_GID) {
                $sheetName = $sheet->getProperties()->getTitle();
                break;
            }
        }

        if (!$sheetName) {
            return null;
        }

        $response = $service->spreadsheets_values->get(ENCUENTRO_SHEET_ID, "{$sheetName}!A4:E");
        $filas    = $response->getValues() ?? [];

        $nombres = [];
        foreach ($filas as $fila) {
            $nombre  = trim($fila[0] ?? '');
            $asistio = mb_strtolower(trim($fila[4] ?? ''), 'UTF-8');

            if ($nombre !== '' && in_array($asistio, ['sí', 'si'], true)) {
                $nombres[] = $nombre;
            }
        }

        return $nombres;
    } catch (Throwable $e) {
        error_log('[sheets-asistentes] Error consultando el Sheet: ' . $e->getMessage());
        return null;
    }
}

/**
 * Usa la ruta configurada en el servidor y, al trabajar en localhost, recurre
 * al credentials.json ubicado en la raíz del proyecto.
 */
function googleCredentialsPath(): string
{
    $configuredPath = trim((string) getenv('GOOGLE_CREDENTIALS_PATH'));

    if ($configuredPath !== '' && is_file($configuredPath)) {
        return $configuredPath;
    }

    $localPath = dirname(__DIR__) . '/credentials.json';
    if (is_file($localPath)) {
        return $localPath;
    }

    throw new RuntimeException(
        'No se encontró el archivo de credenciales de Google Sheets.'
    );
}
