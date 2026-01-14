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
 * CONSULTAR SIGA (CLAVE 🔑)
 * =========================
 */
try {
    $isGraduated = verifyIfIsGraduated($identificationNumber); // 0 o 1
} catch (Exception $e) {
    error_log('SIGA error: ' . $e->getMessage());
    $isGraduated = 0; // fallback seguro
}

/**
 * =========================
 * DUPLICATE CHECK
 * =========================
 */
$dbRead = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

$exists = $dbRead->table('form_answers')
    ->select(['id'])
    ->where('identification_number', '=', $identificationNumber)
    ->get();

if (!empty($exists)) {
    echo json_encode([
        'status' => 'skipped_duplicate',
        'identification_number' => $identificationNumber,
        'is_graduated' => (int) $isGraduated
    ]);
    exit;
}

/**
 * =========================
 * PREPARE DATA
 * =========================
 */
$data = [
    'email'                     => $answers['Dirección de correo electrónico'][0] ?? null,
    'identification_number'     => $identificationNumber,
    'name'                      => $answers['Nombres'][0] ?? null,
    'last_name'                 => $answers['Apellidos'][0] ?? null,
    'mobile_phone'              => $answers['Teléfono de contacto'][0] ?? null,
    'alternative_mobile_phone'  => $answers['Teléfono alterno de contacto'][0] ?? null,
    'address'                   => $answers['Dirección de correspondencia'][0] ?? null,
    'country'                   => $answers['País'][0] ?? null,
    'city'                      => $answers['Ciudad'][0] ?? null,
    'is_graduated'              => (int) $isGraduated,   // 👈 AQUÍ
    'answers'                   => json_encode($answers, JSON_UNESCAPED_UNICODE),
    'imported'                  => !empty($payload['imported']) ? 1 : 0,
    'created_at'                => date('Y-m-d H:i:s'),
    'updated_at'                => date('Y-m-d H:i:s'),
];

/**
 * =========================
 * INSERT
 * =========================
 */
$dbWrite = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
$dbWrite->table('form_answers')->insert($data);

/**
 * =========================
 * RESPONSE
 * =========================
 */
echo json_encode([
    'status' => 'ok',
    'identification_number' => $identificationNumber,
    'is_graduated' => (int) $isGraduated
]);

exit;

/**
 * =========================
 * FUNCTION SIGA
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

    return (int) $decoded['data']; // 0 = NO graduado, 1 = graduado
}
