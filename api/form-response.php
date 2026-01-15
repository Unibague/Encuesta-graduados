<?php

require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');

/**
 * =========================
 * SECURITY
 * =========================
 */
$receivedToken = $_SERVER['HTTP_X_API_TOKEN'] ?? '';
$expectedToken = getenv('FORM_WEBHOOK_TOKEN');

if (!$expectedToken || $receivedToken !== $expectedToken) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

/**
 * =========================
 * INPUT
 * =========================
 */
$payload = json_decode(file_get_contents('php://input'), true);

if (!$payload || empty($payload['answers'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$answers = $payload['answers'];

/**
 * =========================
 * IDENTIFICATION
 * =========================
 */
$identificationNumber = $answers['Número de identificación'][0] ?? null;

if (!$identificationNumber) {
    http_response_code(422);
    echo json_encode(['error' => 'Missing identification number']);
    exit;
}

/**
 * =========================
 * CONSULTAR SIGA
 * =========================
 */
try {
    $isGraduated = verifyIfIsGraduated($identificationNumber);
} catch (Exception $e) {
    error_log('SIGA error: ' . $e->getMessage());
    $isGraduated = 0;
}

/**
 * =========================
 * DB READ (SOLO LECTURA)
 * =========================
 */
$dbRead = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

$existing = $dbRead
    ->table('form_answers')
    ->select(['id'])
    ->where('identification_number', '=', $identificationNumber)
    ->get();

/**
 * =========================
 * DATA
 * =========================
 */
$data = [
    'email'                    => $answers['Dirección de correo electrónico'][0] ?? null,
    'name'                     => $answers['Nombres'][0] ?? null,
    'last_name'                => $answers['Apellidos'][0] ?? null,
    'mobile_phone'             => $answers['Teléfono de contacto'][0] ?? null,
    'alternative_mobile_phone' => $answers['Teléfono alterno de contacto'][0] ?? null,
    'address'                  => $answers['Dirección de correspondencia'][0] ?? null,
    'country'                  => $answers['País'][0] ?? null,
    'city'                     => $answers['Ciudad'][0] ?? null,
    'answers'                  => json_encode($answers, JSON_UNESCAPED_UNICODE),
    'is_graduated'             => (int) $isGraduated,
    'is_migrated'              => 0,
    'is_denied'                => 0,
    'is_deleted'               => 0,
    'updated_at'               => date('Y-m-d H:i:s'),
];

/**
 * =========================
 * DB WRITE (INSERT / UPDATE)
 * =========================
 */
$dbWrite = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

if (!empty($existing)) {

    $dbWrite
        ->table('form_answers')
        ->where('identification_number', '=', $identificationNumber)
        ->update($data);

    echo json_encode([
        'status' => 'updated',
        'identification_number' => $identificationNumber,
        'is_graduated' => (int) $isGraduated
    ]);
    exit;
}

// INSERT
$data['identification_number'] = $identificationNumber;
$data['created_at'] = date('Y-m-d H:i:s');

$dbWrite
    ->table('form_answers')
    ->insert($data);

echo json_encode([
    'status' => 'inserted',
    'identification_number' => $identificationNumber,
    'is_graduated' => (int) $isGraduated
]);

exit;

/**
 * =========================
 * SIGA
 * =========================
 */
function verifyIfIsGraduated(string $identification_number): int
{
    $endpoint = 'https://academia.unibague.edu.co/atlante/grad_ver_siga.php';

    $curl = new \Ospina\CurlCobain\CurlCobain($endpoint);
    $curl->setQueryParamsAsArray([
        'consulta'  => 'Consultar',
        'documento' => $identification_number,
    ]);

    $response = $curl->makeRequest();
    $decoded  = json_decode($response, true);

    if (!isset($decoded['data'])) {
        throw new Exception('Respuesta inválida de SIGA');
    }

    return (int) $decoded['data'];
}
