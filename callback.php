<?php

require __DIR__ . '/app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

/**
 * =========================
 * HEADERS API
 * =========================
 */
header('Content-Type: application/json; charset=utf-8');

/**
 * =========================
 * PARSE REQUEST JSON
 * =========================
 */
try {
    $request = parseJsonRequest();
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'JSON inválido'
    ]);
    exit;
}

/**
 * =========================
 * VALIDACIÓN BÁSICA
 * =========================
 */
if (
    empty($request->answers) ||
    empty($request->code_user)
) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Estructura de datos incompleta'
    ]);
    exit;
}

/**
 * =========================
 * EXTRAER DATOS
 * =========================
 */
$identificationNumber = getIdentificationNumberFromRequest($request);

if (empty($identificationNumber)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Número de identificación no encontrado'
    ]);
    exit;
}

$email        = $request->code_user;
$name         = getValue($request, 'Nombres');
$lastName     = getValue($request, 'Apellidos');
$mobilePhone  = getValue($request, 'Teléfono de contacto');
$altPhone     = getValue($request, 'Teléfono alterno de contacto');
$address      = getValue($request, 'Dirección de correspondencia');
$country      = getValue($request, 'País');
$city         = getValue($request, 'Ciudad');

/**
 * =========================
 * VERIFICAR SIGA
 * =========================
 */
try {
    $isGraduated = verifyIfIsGraduated($identificationNumber);
} catch (Exception $e) {
    error_log($e->getMessage());
    $isGraduated = 0;
}

/**
 * =========================
 * GUARDAR EN DB
 * =========================
 */
$data = [
    'email'                     => $email,
    'identification_number'     => $identificationNumber,
    'name'                      => $name,
    'last_name'                 => $lastName,
    'mobile_phone'              => $mobilePhone,
    'alternative_mobile_phone'  => $altPhone,
    'address'                   => $address,
    'country'                   => $country,
    'city'                      => $city,
    'is_graduated'              => (int) $isGraduated,
    'answers'                   => json_encode($request->answers, JSON_UNESCAPED_UNICODE),
    'created_at'                => date('Y-m-d H:i:s'),
];

$easySQL = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
$easySQL->table('form_answers')->insert($data);

/**
 * =========================
 * RESPONSE OK
 * =========================
 */
http_response_code(201);
echo json_encode([
    'success' => true,
    'message' => 'Registro creado exitosamente'
]);
exit;

/**
 * =========================
 * FUNCTIONS
 * =========================
 */

function parseJsonRequest(): object
{
    $data = file_get_contents('php://input');
    if (!$data) {
        throw new Exception('Empty body');
    }

    return json_decode($data, false, 512, JSON_THROW_ON_ERROR);
}

function getIdentificationNumberFromRequest(object $request): ?string
{
    $key = 'Número de identificación';
    return $request->answers->$key ?? null;
}

function getValue(object $request, string $key): string
{
    return $request->answers->$key ?? 'Campo no diligenciado';
}

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
