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
$celular        = trim(preg_replace('/[^0-9+\s\-]/', '', $input['celular']   ?? ''));
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
        created_at      DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at      DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_iden (identificacion)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

$now    = date('Y-m-d H:i:s');
$idSafe = addslashes($identificacion);

/* ¿Ya se registró con esta cédula? */
$existente = $db->makeQuery("
    SELECT id FROM encuentro_2026
    WHERE identificacion = '$idSafe'
    LIMIT 1
")->fetch_assoc();

if ($existente) {
    $db->makeQuery("
        UPDATE encuentro_2026 SET
            nombres      = '" . addslashes($nombres)    . "',
            apellidos    = '" . addslashes($apellidos)  . "',
            celular      = '" . addslashes($celular)    . "',
            correo       = '" . addslashes($correo)     . "',
            asistencia   = '" . addslashes($asistencia) . "',
            acompanantes = $acompanantes,
            updated_at   = '$now'
        WHERE id = {$existente['id']}
    ");
} else {
    $db->makeQuery("
        INSERT INTO encuentro_2026
            (identificacion, nombres, apellidos, celular, correo, asistencia, acompanantes, created_at, updated_at)
        VALUES (
            '$idSafe',
            '" . addslashes($nombres)    . "',
            '" . addslashes($apellidos)  . "',
            '" . addslashes($celular)    . "',
            '" . addslashes($correo)     . "',
            '" . addslashes($asistencia) . "',
            $acompanantes,
            '$now', '$now'
        )
    ");
}

/* =========================
 * DATOS ACTUALES EN form_answers
 * ========================= */
$datosActuales = [];

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

/* =========================
 * GOOGLE SHEETS (solo si asiste)
 * ========================= */
$sheetsError = null;

if ($asistencia === 'si') {
    try {
        registrarEnSheets($nombres, $apellidos, $identificacion, $celular, $correo, $asistencia, $acompanantes);
    } catch (Throwable $e) {
        $sheetsError = $e->getMessage();
        encuentroLog('Error Google Sheets: ' . $e->getMessage(), 'ERROR');
    }
}

echo json_encode([
    'success'        => true,
    'message'        => 'Registro exitoso',
    'datos_actuales' => $datosActuales,
    'sheets_error'   => $sheetsError, // null = OK, string = fallo no crítico
]);
exit;

/* =========================
 * FUNCTIONS
 * ========================= */

function registrarEnSheets(
    string $nombres,
    string $apellidos,
    string $identificacion,
    string $celular,
    string $correo,
    string $asistencia,
    int    $acompanantes
): void {
    $spreadsheetId = '1LockLyDz0texEzDypaRyhqL1uniy4Fpus_FPCPOv2Ec';
    $targetGid     = 1419700379;

    $client = new Google_Client();
    $client->setAuthConfig(getenv('GOOGLE_CREDENTIALS_PATH'));
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

    // Columna A: Nombre | Columna B: Acompañantes | Columna C: ¿Asistió?
    $body   = new Google_Service_Sheets_ValueRange(['values' => [[
        $nombres . ' ' . $apellidos,
        $acompanantes,
        $asistencia === 'si' ? 'Sí' : 'No',
    ]]]);
    $params = ['valueInputOption' => 'USER_ENTERED'];

    $service->spreadsheets_values->append($spreadsheetId, $sheetName, $body, $params);

    encuentroLog("Registrado en Sheets: {$nombres} {$apellidos} | {$identificacion}");
}

function encuentroLog(string $message, string $level = 'INFO'): void
{
    $line   = '[' . date('Y-m-d H:i:s') . "][{$level}] {$message}" . PHP_EOL;
    $logDir = dirname(__DIR__) . '/logs';

    if (!is_dir($logDir)) mkdir($logDir, 0777, true);

    file_put_contents($logDir . '/encuentro.log', $line, FILE_APPEND);
}
