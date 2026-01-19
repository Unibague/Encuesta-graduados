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

/* =========================
 * NORMALIZAR CLAVES
 * ========================= */
$answers = [];
foreach ($payload['answers'] as $key => $value) {
    $normalizedKey = mb_strtolower(trim($key), 'UTF-8');
    $answers[$normalizedKey] = $value;
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
 * IDENTIFICATION
 * ========================= */
$identificationNumber = getAnswer($answers, [
    'número de identificación',
    'documento de identidad',
    'documento',
    'cédula',
    'cedula'
]);

if (!$identificationNumber) {
    http_response_code(422);
    echo json_encode(['error' => 'Missing identification number']);
    exit;
}

/* =========================
 * NORMALIZED DATA
 * ========================= */
$email   = getAnswer($answers, ['dirección de correo electrónico', 'email address', 'correo electrónico', 'correo']);
$name    = getAnswer($answers, ['nombres', 'nombre']);
$last    = getAnswer($answers, ['apellidos', 'apellido']);
$phone   = getAnswer($answers, ['teléfono de contacto', 'telefono de contacto', 'teléfono']);
$alt     = getAnswer($answers, ['teléfono alterno de contacto', 'telefono alterno de contacto']);
$city    = getAnswer($answers, ['ciudad', 'city']);
$country = getAnswer($answers, ['país', 'pais', 'country']);
$address = getAnswer($answers, ['dirección de correspondencia', 'direccion de correspondencia', 'dirección', 'direccion']);

$now = date('Y-m-d H:i:s');

/* =========================
 * SIGA – LÓGICA CORRECTA
 * ========================= */
try {

    if (!existsInSiga($identificationNumber)) {
        // No existe en SIGA
        $isGraduated = null;
    } else {
        // Existe en SIGA → validar si es graduado
        $isGraduated = verifyIfIsGraduated($identificationNumber); // 0 o 1
    }

} catch (Throwable $e) {
    error_log('[SIGA] ' . $e->getMessage());
    $isGraduated = null;
}

/* =========================
 * DB
 * ========================= */
$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

/* =========================
 * CHECK EXISTING
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
 * UPDATE (EXISTE)
 * ========================= */
if ($row) {

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
            is_graduated = " . ($isGraduated === null ? 'NULL' : (int)$isGraduated) . ",
            is_migrated = 0,
            is_denied = 0,
            is_deleted = 0,
            created_at = '$now',
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
 * INSERT (NO EXISTE)
 * ========================= */
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
        " . ($isGraduated === null ? 'NULL' : (int)$isGraduated) . ",
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
 * FUNCTIONS
 * ========================= */

function existsInSiga(string $document): bool
{
    $url = "https://academia.unibague.edu.co/atlante/consulta_estudiante.php?code_user={$document}&type=I";
    $response = @file_get_contents($url);

    if (!$response) {
        return false;
    }

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

    return (int) $decoded['data']; // 0 | 1
}
