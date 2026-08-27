<?php

require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => true, 'message' => 'Método no permitido']);
    exit;
}

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'Datos inválidos']);
    exit;
}

/* =========================
 * SANITIZAR
 * ========================= */
$nombres        = trim(strip_tags($input['nombres']        ?? ''));
$apellidos      = trim(strip_tags($input['apellidos']      ?? ''));
$identificacion = trim(preg_replace('/[^0-9]/', '', $input['identificacion'] ?? ''));
$celular        = preg_replace('/[^0-9]/', '', $input['celular'] ?? '');
if (strlen($celular) === 12 && substr($celular, 0, 2) === '57') {
    $celular = substr($celular, 2);
}
$correoRaw      = trim($input['correo'] ?? '');
$correo         = filter_var($correoRaw, FILTER_VALIDATE_EMAIL) ? $correoRaw : '';
$asistencia     = in_array($input['asistencia'] ?? '', ['si', 'no'], true) ? $input['asistencia'] : '';
$acompanantesRaw = (int)($input['acompanantes'] ?? -1);
$acompanantes   = in_array($acompanantesRaw, [0, 1, 2], true) ? $acompanantesRaw : -1;

/* =========================
 * VALIDAR
 * ========================= */
if (!$nombres || !$apellidos || !$identificacion || !$celular || !$correo || !$asistencia || $acompanantes === -1) {
    http_response_code(422);
    echo json_encode(['error' => true, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

/* =========================
 * DB
 * ========================= */
$anioActivo = obtenerAnioEncuentroActivo();

$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

/* Crear tabla si no existe */
$db->makeQuery("
    CREATE TABLE IF NOT EXISTS encuentro_2026 (
        id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        identificacion  VARCHAR(30)  NOT NULL,
        nombres         VARCHAR(100) NOT NULL,
        apellidos       VARCHAR(100) NOT NULL,
        celular         VARCHAR(30)  NOT NULL,
        correo          VARCHAR(150) NOT NULL,
        asistencia      ENUM('si','no') NOT NULL DEFAULT 'si',
        acompanantes    TINYINT     NOT NULL DEFAULT 0,
        encuentro_anio  SMALLINT UNSIGNED NOT NULL DEFAULT 2026,
        created_at      DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at      DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_iden (identificacion)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

/* Compatibilidad con instalaciones donde la tabla se creó antes de agregar
   el año del encuentro. */
$yearColumn = $db->makeQuery("SHOW COLUMNS FROM encuentro_2026 LIKE 'encuentro_anio'")->fetch_assoc();
if (!$yearColumn) {
    $db->makeQuery("ALTER TABLE encuentro_2026
        ADD COLUMN encuentro_anio SMALLINT UNSIGNED NOT NULL DEFAULT 2026 AFTER acompanantes");
}

$now    = date('Y-m-d H:i:s');
$idSafe = addslashes($identificacion);

/* ¿Ya se registró con esta cédula? */
$existente = $db->makeQuery("
    SELECT id, asistencia FROM encuentro_2026
    WHERE identificacion = '$idSafe'
    LIMIT 1
")->fetch_assoc();

/* No se bloquea una respuesta repetida: se vuelve a sincronizar con Google Sheets.
   Esto permite reparar una fila faltante si una sincronización anterior falló. */
$mismaRespuesta = $existente && $existente['asistencia'] === $asistencia;

if ($existente) {
    $db->makeQuery("
        UPDATE encuentro_2026 SET
            nombres      = '" . addslashes($nombres)    . "',
            apellidos    = '" . addslashes($apellidos)  . "',
            celular      = '" . addslashes($celular)    . "',
            correo       = '" . addslashes($correo)     . "',
            asistencia   = '" . addslashes($asistencia) . "',
            acompanantes = $acompanantes,
            encuentro_anio = $anioActivo,
            updated_at   = '$now'
        WHERE id = {$existente['id']}
    ");
} else {
    $db->makeQuery("
        INSERT INTO encuentro_2026
            (identificacion, nombres, apellidos, celular, correo, asistencia, acompanantes, encuentro_anio, created_at, updated_at)
        VALUES (
            '$idSafe',
            '" . addslashes($nombres)    . "',
            '" . addslashes($apellidos)  . "',
            '" . addslashes($celular)    . "',
            '" . addslashes($correo)     . "',
            '" . addslashes($asistencia) . "',
            $acompanantes,
            $anioActivo,
            '$now', '$now'
        )
    ");
}

/* =========================
 * DATOS ACTUALES (SIGA en vivo, con form_answers como respaldo)
 * ========================= */
$datosActuales = [];

try {
    $datosActuales = consultarSiga($identificacion);
} catch (Throwable $e) {
    encuentroLog('Error consultando SIGA para prefill: ' . $e->getMessage(), 'WARNING');
}

if (!array_filter($datosActuales)) {
    $row = $db->makeQuery("
        SELECT
            name, last_name, email,
            mobile_phone, alternative_mobile_phone,
            city, address
        FROM form_answers
        WHERE identification_number = '$idSafe'
        LIMIT 1
    ")->fetch_assoc();

    if ($row) {
        $datosActuales = $row;
    }
}

/* =========================
 * GOOGLE SHEETS (solo si asiste)
 * ========================= */
$sheetsError = null;

try {
    if ($asistencia === 'si') {
        registrarEnSheets($nombres, $apellidos, $identificacion, $celular, $correo, $acompanantes);
        eliminarDeHojaNoAsistentes($nombres, $apellidos, $identificacion, $correo);
    } else {
        registrarEnHojaNoAsistentes($nombres, $apellidos, $identificacion, $celular, $correo);
        eliminarDeHojaAsistentes($nombres, $apellidos, $identificacion, $correo);
    }
} catch (Throwable $e) {
    $sheetsError = $e->getMessage();
    encuentroLog('Error Google Sheets: ' . $e->getMessage(), 'ERROR');
}

/* La base de datos queda guardada para no perder la respuesta, pero no se informa
   éxito mientras Google Sheets siga pendiente. Al reenviar, la fila se repara. */
if ($sheetsError !== null) {
    http_response_code(502);
    echo json_encode([
        'error'   => true,
        'message' => 'Guardamos tu respuesta, pero no pudimos actualizar la lista de asistencia. Por favor, vuelve a presionar "Confirmar registro".',
    ]);
    exit;
}

/* Primero se verifican y reparan las hojas; luego se informa que la misma
   respuesta ya estaba registrada para que el formulario muestre el aviso. */
if ($mismaRespuesta) {
    http_response_code(409);
    echo json_encode([
        'error'   => true,
        'message' => $asistencia === 'si'
            ? 'Ya estás registrado para el Encuentro de Graduados 2026. Si necesitas actualizar tus datos de contacto, contacta al correo graduados@unibague.edu.co.'
            : 'Ya habías indicado que no podrás asistir. Si cambiaste de opinión, marca "Sí, asistiré" para registrarte.',
    ]);
    exit;
}

echo json_encode([
    'success'        => true,
    'message'        => 'Registro exitoso',
    'datos_actuales' => $datosActuales,
    'sheets_error'   => $sheetsError, // null = sincronización correcta
]);
exit;

/* =========================
 * FUNCTIONS
 * ========================= */

function consultarSiga(string $identificacion): array
{
    $endpoint = 'https://academia.unibague.edu.co/atlante/grad_dat_siga.php';

    $curl = new \Ospina\CurlCobain\CurlCobain($endpoint);
    $curl->setQueryParamsAsArray([
        'consulta'  => 'Consultar',
        'documento' => $identificacion,
        'dia'       => 'N.A',
        'mes'       => 'N.A',
        'token'     => md5($identificacion) . getenv('SECURE_TOKEN'),
    ]);

    $response = trim((string) $curl->makeRequest());

    encuentroLog('Respuesta grad_dat_siga documento ' . $identificacion . ' | body=' .
        substr(str_replace(["\r", "\n"], ['\\r', '\\n'], $response), 0, 1200));

    if ($response === '') {
        return [];
    }

    $start = strpos($response, '{');
    $end   = strrpos($response, '}');
    if ($start === false || $end === false || $end < $start) {
        return [];
    }

    $decoded = json_decode(substr($response, $start, $end - $start + 1), true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
        return [];
    }

    if (isset($decoded['data']) && is_array($decoded['data'])) {
        $decoded = $decoded['data'];
    }

    return [
        'name'                     => $decoded['Nombres']                       ?? '',
        'last_name'                => $decoded['Apellidos']                     ?? '',
        'email'                    => $decoded['Correo']                        ?? '',
        'mobile_phone'             => $decoded['Telefono de contacto']          ?? '',
        'alternative_mobile_phone' => $decoded['Telefono alterno']              ?? '',
        'city'                     => $decoded['Ciudad residencia']             ?? '',
        'address'                  => $decoded['Direccion de correspondencia']  ?? '',
    ];
}

function registrarEnSheets(
    string $nombres,
    string $apellidos,
    string $identificacion,
    string $celular,
    string $correo,
    int    $acompanantes
): void {
    $spreadsheetId = '1LockLyDz0texEzDypaRyhqL1uniy4Fpus_FPCPOv2Ec';
    $targetGid     = 1419700379;

    $client = new Google_Client();
    $client->setAuthConfig(googleCredentialsPath());
    $client->addScope(Google_Service_Sheets::SPREADSHEETS);

    $service = new Google_Service_Sheets($client);

    // Buscar nombre de la hoja por gid
    $spreadsheet = $service->spreadsheets->get($spreadsheetId);
    $sheetName   = null;
    foreach ($spreadsheet->getSheets() as $sheet) {
        if ((int) $sheet->getProperties()->getSheetId() === $targetGid) {
            $sheetName = $sheet->getProperties()->getTitle();
            break;
        }
    }

    if (!$sheetName) {
        throw new RuntimeException("No se encontró la hoja con gid={$targetGid}");
    }

    $nombreCompleto = trim($nombres . ' ' . $apellidos);
    $params         = ['valueInputOption' => 'USER_ENTERED'];

    // Buscar si esta persona ya tiene fila (evitar duplicados al reenviar el registro)
    $existentes = $service->spreadsheets_values->get($spreadsheetId, "{$sheetName}!A4:A");
    $filas      = $existentes->getValues() ?? [];

    $numeroFila = null;
    foreach ($filas as $i => $fila) {
        $nombreFila = trim($fila[0] ?? '');
        if ($nombreFila !== '' && mb_strtolower($nombreFila, 'UTF-8') === mb_strtolower($nombreCompleto, 'UTF-8')) {
            $numeroFila = $i + 4; // A4 es la primera fila de datos
            break;
        }
    }

    if ($numeroFila !== null) {
        // Ya existe: solo actualizar celular, correo y acompañantes (columnas B, C y D), sin tocar ¿Asistió?
        $body = new Google_Service_Sheets_ValueRange(['values' => [[$celular, $correo, $acompanantes]]]);
        $service->spreadsheets_values->update($spreadsheetId, "{$sheetName}!B{$numeroFila}:D{$numeroFila}", $body, $params);

        encuentroLog("Actualizado en Sheets (ya existía): {$nombreCompleto} | {$identificacion}");
        return;
    }

    // Columna A: Nombre | Columna B: Celular | Columna C: Correo | Columna D: Acompañantes
    $body = new Google_Service_Sheets_ValueRange(['values' => [[$nombreCompleto, $celular, $correo, $acompanantes]]]);
    $service->spreadsheets_values->append($spreadsheetId, "{$sheetName}!A:D", $body, $params);

    encuentroLog("Registrado en Sheets: {$nombreCompleto} | {$identificacion}");
}

function registrarEnHojaNoAsistentes(
    string $nombres,
    string $apellidos,
    string $identificacion,
    string $celular,
    string $correo
): void {
    $spreadsheetId = '1LockLyDz0texEzDypaRyhqL1uniy4Fpus_FPCPOv2Ec';
    $targetTitle   = 'No asistentes';

    $client = new Google_Client();
    $client->setAuthConfig(googleCredentialsPath());
    $client->addScope(Google_Service_Sheets::SPREADSHEETS);

    $service = new Google_Service_Sheets($client);

    // Buscar la hoja por título
    $spreadsheet = $service->spreadsheets->get($spreadsheetId);
    $sheetName   = null;
    foreach ($spreadsheet->getSheets() as $sheet) {
        if (mb_strtolower($sheet->getProperties()->getTitle(), 'UTF-8') === mb_strtolower($targetTitle, 'UTF-8')) {
            $sheetName = $sheet->getProperties()->getTitle();
            break;
        }
    }

    if (!$sheetName) {
        throw new RuntimeException("No se encontró la hoja '{$targetTitle}'");
    }

    $nombreCompleto = trim($nombres . ' ' . $apellidos);
    $params         = ['valueInputOption' => 'USER_ENTERED'];

    // Buscar si esta persona ya tiene fila (evitar duplicados al reenviar el registro)
    $existentes = $service->spreadsheets_values->get($spreadsheetId, "{$sheetName}!A4:A");
    $filas      = $existentes->getValues() ?? [];

    $numeroFila = null;
    foreach ($filas as $i => $fila) {
        $nombreFila = trim($fila[0] ?? '');
        if ($nombreFila !== '' && mb_strtolower($nombreFila, 'UTF-8') === mb_strtolower($nombreCompleto, 'UTF-8')) {
            $numeroFila = $i + 4; // A4 es la primera fila de datos
            break;
        }
    }

    if ($numeroFila !== null) {
        // Ya existe: solo actualizar celular y correo (columnas B y C)
        $body = new Google_Service_Sheets_ValueRange(['values' => [[$celular, $correo]]]);
        $service->spreadsheets_values->update($spreadsheetId, "{$sheetName}!B{$numeroFila}:C{$numeroFila}", $body, $params);

        encuentroLog("Actualizado en 'No asistentes': {$nombreCompleto} | {$identificacion}");
        return;
    }

    // Columna A: Nombre | Columna B: Celular | Columna C: Correo
    $body = new Google_Service_Sheets_ValueRange(['values' => [[$nombreCompleto, $celular, $correo]]]);
    $service->spreadsheets_values->append($spreadsheetId, "{$sheetName}!A:C", $body, $params);

    encuentroLog("Registrado en 'No asistentes': {$nombreCompleto} | {$identificacion}");
}

/**
 * Mantiene las dos listas como grupos excluyentes. Si la persona ahora asiste,
 * se retira de "No asistentes"; si ahora no asiste, se retira de "Asistentes".
 */
function eliminarDeHojaNoAsistentes(
    string $nombres,
    string $apellidos,
    string $identificacion,
    string $correo
): void {
    eliminarPersonaDeHoja(
        $nombres,
        $apellidos,
        $identificacion,
        $correo,
        null,
        'No asistentes'
    );
}

function eliminarDeHojaAsistentes(
    string $nombres,
    string $apellidos,
    string $identificacion,
    string $correo
): void {
    eliminarPersonaDeHoja(
        $nombres,
        $apellidos,
        $identificacion,
        $correo,
        1419700379,
        null
    );
}

function eliminarPersonaDeHoja(
    string $nombres,
    string $apellidos,
    string $identificacion,
    string $correo,
    ?int $targetGid,
    ?string $targetTitle
): void {
    $spreadsheetId = '1LockLyDz0texEzDypaRyhqL1uniy4Fpus_FPCPOv2Ec';

    $client = new Google_Client();
    $client->setAuthConfig(googleCredentialsPath());
    $client->addScope(Google_Service_Sheets::SPREADSHEETS);

    $service     = new Google_Service_Sheets($client);
    $spreadsheet = $service->spreadsheets->get($spreadsheetId);
    $sheetName   = null;
    $sheetId     = null;

    foreach ($spreadsheet->getSheets() as $sheet) {
        $properties = $sheet->getProperties();
        $coincideGid = $targetGid !== null && (int) $properties->getSheetId() === $targetGid;
        $coincideTitulo = $targetTitle !== null
            && mb_strtolower(trim($properties->getTitle()), 'UTF-8') === mb_strtolower(trim($targetTitle), 'UTF-8');

        if ($coincideGid || $coincideTitulo) {
            $sheetName = $properties->getTitle();
            $sheetId   = (int) $properties->getSheetId();
            break;
        }
    }

    if ($sheetName === null || $sheetId === null) {
        throw new RuntimeException('No se encontró la hoja opuesta para actualizar la asistencia.');
    }

    /* Se lee hasta C porque ambas hojas guardan Nombre, Celular y Correo. */
    $response = $service->spreadsheets_values->get($spreadsheetId, "{$sheetName}!A4:C");
    $filas    = $response->getValues() ?? [];

    $nombreCompleto = trim($nombres . ' ' . $apellidos);
    $nombreBuscado  = mb_strtolower(preg_replace('/\s+/', ' ', $nombreCompleto), 'UTF-8');
    $correoBuscado  = mb_strtolower(trim($correo), 'UTF-8');
    $filasABorrar   = [];

    foreach ($filas as $indice => $fila) {
        $nombreFila = mb_strtolower(preg_replace('/\s+/', ' ', trim($fila[0] ?? '')), 'UTF-8');
        $correoFila = mb_strtolower(trim($fila[2] ?? ''), 'UTF-8');

        /* El correo es la clave principal. El nombre solo respalda filas sin correo. */
        $coincide = $correoFila !== ''
            ? $correoFila === $correoBuscado
            : ($nombreFila !== '' && $nombreFila === $nombreBuscado);

        if ($coincide) {
            $filasABorrar[] = $indice + 4;
        }
    }

    if (!$filasABorrar) {
        encuentroLog("Sin coincidencias para retirar de '{$sheetName}': {$nombreCompleto} | {$identificacion}");
        return;
    }

    /* De abajo hacia arriba para que los índices no cambien durante el borrado. */
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

    encuentroLog(
        "Retirado de '{$sheetName}': {$nombreCompleto} | {$identificacion} | filas: " .
        implode(', ', $filasABorrar)
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

function encuentroLog(string $message, string $level = 'INFO'): void
{
    $line   = '[' . date('Y-m-d H:i:s') . "][{$level}] {$message}" . PHP_EOL;
    $logDir = dirname(__DIR__) . '/logs';

    if (!is_dir($logDir)) mkdir($logDir, 0777, true);

    file_put_contents($logDir . '/encuentro.log', $line, FILE_APPEND);
}
