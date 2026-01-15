<?php

require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');

/* =========================
 * SECURITY
 * ========================= */
$receivedToken = $_SERVER['HTTP_X_API_TOKEN'] ?? '';
$expectedToken = getenv('FORM_WEBHOOK_TOKEN');

if (!$expectedToken || $receivedToken !== $expectedToken) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

/* =========================
 * INPUT
 * ========================= */
$payload = json_decode(file_get_contents('php://input'), true);

if (!$payload || empty($payload['answers'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$answers = $payload['answers'];
$identificationNumber = $answers['Número de identificación'][0] ?? null;

if (!$identificationNumber) {
    http_response_code(422);
    echo json_encode(['error' => 'Missing identification number']);
    exit;
}

/* =========================
 * SIGA
 * ========================= */
try {
    $isGraduated = verifyIfIsGraduated($identificationNumber);
} catch (Throwable $e) {
    error_log('[SIGA] ' . $e->getMessage());
    $isGraduated = 0;
}

/* =========================
 * DB
 * ========================= */
$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

/* =========================
 * CHECK EXISTING (ID REAL)
 * ========================= */
$result = $db->makeQuery("
    SELECT id
    FROM form_answers
    WHERE identification_number = '" . addslashes($identificationNumber) . "'
    ORDER BY created_at DESC
    LIMIT 1
");

$row = $result->fetch_assoc();

/* =========================
 * DATA NORMALIZADA
 * ========================= */
$email   = $answers['Dirección de correo electrónico'][0] ?? null;
$name    = $answers['Nombres'][0] ?? null;
$last    = $answers['Apellidos'][0] ?? null;
$phone   = $answers['Teléfono de contacto'][0] ?? null;
$alt     = $answers['Teléfono alterno de contacto'][0] ?? null;
$city    = $answers['Ciudad'][0] ?? null;
$country = $answers['País'][0] ?? null;
$address = $answers['Dirección de correspondencia'][0] ?? null;
$now     = date('Y-m-d H:i:s');

/* =========================
 * UPDATE O INSERT
 * ========================= */
if ($row) {

    // UPDATE SEGURO POR ID
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
            is_graduated = " . (int)$isGraduated . ",
            is_migrated = 0,
            is_denied = 0,
            is_deleted = 0,
            updated_at = '$now'
        WHERE id = {$row['id']}
    ");

    echo json_encode([
        'status' => 'updated',
        'id' => $row['id'],
        'is_graduated' => $isGraduated
    ]);
    exit;
}

/* =========================
 * INSERT NUEVO
 * ========================= */
$db->makeQuery("
    INSERT INTO form_answers (
        identification_number, email, name, last_name,
        mobile_phone, alternative_mobile_phone,
        city, country, address,
        answers, is_graduated, is_migrated,
        is_denied, is_deleted, created_at, updated_at
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
        " . (int)$isGraduated . ",
        0, 0, 0,
        '$now', '$now'
    )
");

echo json_encode([
    'status' => 'inserted',
    'is_graduated' => $isGraduated
]);
exit;

/* =========================
 * SIGA
 * ========================= */
function verifyIfIsGraduated(string $identification_number): int
{
    $curl = new \Ospina\CurlCobain\CurlCobain(
        'https://academia.unibague.edu.co/atlante/grad_ver_siga.php'
    );

    $curl->setQueryParamsAsArray([
        'consulta' => 'Consultar',
        'documento' => $identification_number,
    ]);

    $response = $curl->makeRequest();
    $decoded  = json_decode($response, true);

    if (!isset($decoded['data'])) {
        throw new Exception('Respuesta inválida de SIGA');
    }

    return (int) $decoded['data'];
}
