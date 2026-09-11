<?php

const ENCUENTRO_SHEET_ID            = '1LockLyDz0texEzDypaRyhqL1uniy4Fpus_FPCPOv2Ec';
const ENCUENTRO_SHEET_ASISTENTES_GID = 1419700379;

/* Hoja aparte, solo para los juegos: quien esté aquí (nombre completo en la
   columna A) puede jugar, sin importar el cupo del Encuentro. */
const JUEGOS_SHEET_ID   = '1tG_qZfKJS586Jf0UTMDewnaLjDhydItompHhdaeCzhE';
const JUEGOS_SHEET_NAME = 'Sheet1';

/**
 * Cliente de Google configurado con un timeout. Sin esto, una llamada a la
 * API de Sheets que se queda esperando (red lenta o saturada, como puede
 * pasar con muchos celulares conectados a la vez en el Encuentro) deja la
 * petición del formulario colgada indefinidamente en vez de fallar rápido
 * y dejar que la persona reintente.
 */
function crearClienteGoogleSheets(): Google_Client
{
    $client = new Google_Client();
    $client->setAuthConfig(googleCredentialsPath());
    $client->setHttpClient(new \GuzzleHttp\Client([
        'timeout'         => 10,
        'connect_timeout' => 5,
    ]));

    return $client;
}

/**
 * Devuelve los nombres completos de quienes confirmaron asistencia ("Sí")
 * en el Google Sheet del Encuentro (la fuente de la verdad para el cupo y
 * los juegos), o null si no fue posible consultarlo, para que el llamador
 * pueda usar la base de datos como respaldo.
 */
function obtenerAsistentesConfirmadosSheet(): ?array
{
    try {
        $client = crearClienteGoogleSheets();
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
 * Devuelve los nombres completos registrados en la hoja "Acompañantes" del
 * Google Sheet del Encuentro, o null si no fue posible consultarla, para que
 * el llamador pueda usar la base de datos (registroacom_2026) como respaldo.
 */
function obtenerAcompanantesConfirmadosSheet(): ?array
{
    try {
        $client = crearClienteGoogleSheets();
        $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);

        $service = new Google_Service_Sheets($client);

        $spreadsheet = $service->spreadsheets->get(ENCUENTRO_SHEET_ID);
        $sheetName   = null;
        foreach ($spreadsheet->getSheets() as $sheet) {
            $titulo = trim($sheet->getProperties()->getTitle());
            if (mb_strtolower($titulo, 'UTF-8') === mb_strtolower('Acompañantes', 'UTF-8')) {
                $sheetName = $sheet->getProperties()->getTitle();
                break;
            }
        }

        if (!$sheetName) {
            return null;
        }

        $response = $service->spreadsheets_values->get(ENCUENTRO_SHEET_ID, "{$sheetName}!A4:A");
        $filas    = $response->getValues() ?? [];

        $nombres = [];
        foreach ($filas as $fila) {
            $nombre = trim($fila[0] ?? '');
            if ($nombre !== '') {
                $nombres[] = $nombre;
            }
        }

        return $nombres;
    } catch (Throwable $e) {
        error_log('[sheets-asistentes] Error consultando acompañantes en el Sheet: ' . $e->getMessage());
        return null;
    }
}

/**
 * Devuelve los nombres completos registrados en la hoja dedicada a los
 * juegos (spreadsheet aparte, sin límite de cupo), o null si no fue posible
 * consultarla, para que el llamador use la base de datos como respaldo.
 */
function obtenerParticipantesJuegosSheet(): ?array
{
    try {
        $client = crearClienteGoogleSheets();
        $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);

        $service = new Google_Service_Sheets($client);

        $response = $service->spreadsheets_values->get(JUEGOS_SHEET_ID, JUEGOS_SHEET_NAME . '!A2:A');
        $filas    = $response->getValues() ?? [];

        $nombres = [];
        foreach ($filas as $fila) {
            $nombre = trim($fila[0] ?? '');
            if ($nombre !== '') {
                $nombres[] = $nombre;
            }
        }

        return $nombres;
    } catch (Throwable $e) {
        error_log('[sheets-asistentes] Error consultando la hoja de juegos: ' . $e->getMessage());
        return null;
    }
}

/**
 * Agrega el nombre completo a la hoja dedicada a los juegos, si todavía no
 * está ahí (comparación sin distinguir mayúsculas/tildes exactas de más).
 */
function registrarNombreEnHojaJuegos(string $nombreCompleto): void
{
    $nombreCompleto = trim($nombreCompleto);
    if ($nombreCompleto === '') {
        return;
    }

    $client = crearClienteGoogleSheets();
    $client->addScope(Google_Service_Sheets::SPREADSHEETS);

    $service = new Google_Service_Sheets($client);

    $existentes = $service->spreadsheets_values->get(JUEGOS_SHEET_ID, JUEGOS_SHEET_NAME . '!A2:A');
    $filas      = $existentes->getValues() ?? [];

    foreach ($filas as $fila) {
        $nombreFila = trim($fila[0] ?? '');
        if ($nombreFila !== '' && mb_strtolower($nombreFila, 'UTF-8') === mb_strtolower($nombreCompleto, 'UTF-8')) {
            return; // ya estaba registrado
        }
    }

    $body = new Google_Service_Sheets_ValueRange(['values' => [[$nombreCompleto]]]);
    $service->spreadsheets_values->append(
        JUEGOS_SHEET_ID,
        JUEGOS_SHEET_NAME . '!A:A',
        $body,
        ['valueInputOption' => 'USER_ENTERED']
    );
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
