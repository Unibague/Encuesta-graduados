<?php

require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');

/* =========================
 * LOG
 * ========================= */
function appLog(string $message, string $level = 'INFO'): void
{
    $date = date('Y-m-d H:i:s');
    $line = "[$date][$level] $message" . PHP_EOL;

    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    file_put_contents($logDir . '/form.log', $line, FILE_APPEND);
}

/* =========================
 * SECURITY
 * ========================= */
$receivedToken = $_SERVER['HTTP_X_API_TOKEN'] ?? '';
$expectedToken = getenv('FORM_WEBHOOK_TOKEN');

if (!$expectedToken || $receivedToken !== $expectedToken) {
    appLog('Token inválido o ausente', 'ERROR');
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

/* =========================
 * INPUT
 * ========================= */
$payload = json_decode(file_get_contents('php://input'), true);

if (!$payload || empty($payload['answers'])) {
    appLog('Payload inválido', 'ERROR');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

appLog('Formulario recibido');

/* =========================
 * NORMALIZAR RESPUESTAS
 * ========================= */
$answers = [];
foreach ($payload['answers'] as $key => $value) {
    $answers[mb_strtolower(trim($key), 'UTF-8')] = $value;
}

/* =========================
 * HELPER
 * ========================= */
function getAnswer(array $answers, array $possibleNames): ?string
{
    foreach ($possibleNames as $name) {
        $key = mb_strtolower(trim($name), 'UTF-8');
        if (!empty($answers[$key][0])) {
            return trim((string)$answers[$key][0]);
        }
    }
    return null;
}

/* =========================
 * IDENTIFICACIÓN
 * ========================= */
$identificationNumber = getAnswer($answers, [
    'número de identificación',
    'documento de identidad',
    'documento',
    'cédula',
    'cedula'
]);

if (!$identificationNumber) {
    appLog('Formulario sin documento', 'ERROR');
    http_response_code(422);
    echo json_encode(['error' => 'Missing identification number']);
    exit;
}

appLog("Documento recibido: {$identificationNumber}");

/* =========================
 * DATOS NORMALIZADOS
 * ========================= */
$email = getAnswer($answers, [
    'email address',          
    'correo electrónico',
    'correo electronico',
    'email'
]);
$name    = getAnswer($answers, ['nombres', 'nombre']);
$last    = getAnswer($answers, ['apellidos', 'apellido']);
$phone   = getAnswer($answers, ['teléfono de contacto', 'telefono']);
$alt     = getAnswer($answers, ['teléfono alterno de contacto']);
$city    = getAnswer($answers, ['ciudad']);
$country = getAnswer($answers, ['país', 'pais']);
$address = getAnswer($answers, ['dirección de correspondencia', 'direccion']);

$now = date('Y-m-d H:i:s');

/* =========================
 * DB
 * ========================= */
$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

/* =========================
 * BUSCAR REGISTRO EXISTENTE
 * ========================= */
$result = $db->makeQuery("
    SELECT id, is_graduated
    FROM form_answers
    WHERE identification_number = '" . addslashes($identificationNumber) . "'
    LIMIT 1
");

$row = $result->fetch_assoc();

$currentIsGraduated = $row ? (int)$row['is_graduated'] : null;

/* =========================
 * SIGA
 * ========================= */
$sigaGraduated = null;

try {
    appLog("Consultando SIGA para {$identificationNumber}");

    if (existsInSiga($identificationNumber)) {
        $sigaGraduated = verifyIfIsGraduated($identificationNumber); // 0 | 1
    }
} catch (Throwable $e) {
    appLog("Error SIGA {$identificationNumber}: {$e->getMessage()}", 'ERROR');
}

/* =========================
 * REGLA DE NEGOCIO FINAL
 * ========================= */
if ($currentIsGraduated === 1) {
    $finalIsGraduated = 1;
} elseif ($sigaGraduated === 1) {
    $finalIsGraduated = 1;
} else {
    $finalIsGraduated = $currentIsGraduated;
}

/* =========================
 * UPDATE
 * ========================= */
if ($row) {

    appLog("ACTUALIZANDO ID {$row['id']} | is_graduated={$finalIsGraduated}");

    $db->makeQuery("
        UPDATE form_answers SET
            email = '" . addslashes($email) . "',
            name = '" . addslashes($name) . "',
            last_name = '" . addslashes($last) . "',
            mobile_phone = '" . addslashes($phone) . "',
            alternative_mobile_phone = '" . addslashes($alt) . "',
            city = '" . addslashes($city) . "',
            country = '" . addslashes($country) . "',
            address = '" . addslashes($address) . "',
            answers = '" . addslashes(json_encode($answers, JSON_UNESCAPED_UNICODE)) . "',
            is_graduated = " . ($finalIsGraduated === null ? 'NULL' : (int)$finalIsGraduated) . ",
            is_migrated = 0,
            is_denied = 0,
            is_deleted = 0,
            updated_at = '$now'
        WHERE id = {$row['id']}
    ");

    echo json_encode([
        'status' => 'updated',
        'id' => $row['id'],
        'is_graduated' => $finalIsGraduated
    ]);
    exit;
}

/* =========================
 * INSERT
 * ========================= */
appLog("INSERTANDO nuevo registro | Documento {$identificationNumber}");

$db->makeQuery("
    INSERT INTO form_answers (
        identification_number,
        email, name, last_name,
        mobile_phone, alternative_mobile_phone,
        city, country, address,
        answers,
        is_graduated,
        is_migrated, is_denied, is_deleted,
        created_at, updated_at
    ) VALUES (
        '" . addslashes($identificationNumber) . "',
        '" . addslashes($email) . "',
        '" . addslashes($name) . "',
        '" . addslashes($last) . "',
        '" . addslashes($phone) . "',
        '" . addslashes($alt) . "',
        '" . addslashes($city) . "',
        '" . addslashes($country) . "',
        '" . addslashes($address) . "',
        '" . addslashes(json_encode($answers, JSON_UNESCAPED_UNICODE)) . "',
        " . ($finalIsGraduated === null ? 'NULL' : (int)$finalIsGraduated) . ",
        0, 0, 0,
        '$now', '$now'
    )
");

echo json_encode([
    'status' => 'inserted',
    'is_graduated' => $finalIsGraduated
]);
exit;

/* =========================
 * FUNCTIONS
 * ========================= */

function existsInSiga(string $document): bool
{
    $url = "https://academia.unibague.edu.co/atlante/consulta_estudiante.php?code_user={$document}&type=I";
    $response = @file_get_contents($url);

    if (!$response) return false;

    $data = json_decode($response, true);
    return is_array($data) && count($data) > 0;
}

function verifyIfIsGraduated(string $identification_number): int
{
    $curl = new \Ospina\CurlCobain\CurlCobain(
        'https://academia.unibague.edu.co/atlante/grad_ver_siga.php'
    );

    $curl->setQueryParamsAsArray([
        'consulta'  => 'Consultar',
        'documento' => $identification_number,
    ]);

    $response = $curl->makeRequest();
    $decoded  = json_decode($response, true);

    if (!isset($decoded['data'])) {
        throw new Exception('Respuesta inválida de SIGA');
    }

    return (int)$decoded['data']; // 0 | 1
}
