<?php

require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$answers = is_array($input['answers'] ?? null) ? $input['answers'] : [];
$surveyType = ($input['survey_type'] ?? '') === 'actualizaciongraduados'
    ? 'actualizaciongraduados'
    : 'registrograduados';

$identificationNumber = trim((string) firstAnswer($answers, [
    'id',
    'identification_number',
    'Número de identificación',
]));

if ($identificationNumber === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'La cédula es obligatoria']);
    exit;
}

$email = (string) firstAnswer($answers, ['email', 'Correo electrónico', 'Correo']);
$name = (string) firstAnswer($answers, ['nombres', 'Nombres']);
$lastName = (string) firstAnswer($answers, ['apellidos', 'Apellidos']);
$phone = (string) firstAnswer($answers, ['numero_celular', 'Teléfono de contacto', 'Telefono de contacto']);
$alternativePhone = (string) firstAnswer($answers, ['numero_alterno', 'Teléfono alterno de contacto', 'Telefono alterno de contacto']);
$address = (string) firstAnswer($answers, ['direccion', 'Dirección de correspondencia']);
$country = (string) firstAnswer($answers, ['pais', 'País']);
$city = (string) firstAnswer($answers, ['ciudad', 'Ciudad']);

$storedAnswers = $answers;
$storedAnswers['_survey_type'] = $surveyType;
$storedAnswers['_survey_completed_at'] = date('Y-m-d H:i:s');
$jsonAnswers = json_encode($storedAnswers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($jsonAnswers === false) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'No se pudieron serializar las respuestas']);
    exit;
}

$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
$identSafe = addslashes($identificationNumber);

$existing = $db->makeQuery("SELECT id FROM form_answers
    WHERE identification_number = '$identSafe' AND is_deleted = 0
    ORDER BY id DESC LIMIT 1")->fetch_assoc();

try {
    $isGraduated = verifyIfIsGraduated($identificationNumber);
} catch (Throwable $e) {
    $isGraduated = 0;
}

$fields = [
    "email = '" . addslashes($email) . "'",
    "name = '" . addslashes($name) . "'",
    "last_name = '" . addslashes($lastName) . "'",
    "mobile_phone = '" . addslashes($phone) . "'",
    "alternative_mobile_phone = '" . addslashes($alternativePhone) . "'",
    "address = '" . addslashes($address) . "'",
    "country = '" . addslashes($country) . "'",
    "city = '" . addslashes($city) . "'",
    "answers = '" . addslashes($jsonAnswers) . "'",
    'is_graduated = ' . (int) $isGraduated,
    'is_migrated = 0',
    'is_denied = 0',
    'is_deleted = 0',
    'has_error = 0',
    'modificated_at = NOW()',
];

if ($existing) {
    $db->makeQuery('UPDATE form_answers SET ' . implode(', ', $fields) . ' WHERE id = ' . (int) $existing['id']);
    $db->makeQuery("UPDATE form_answers SET is_deleted = 1
        WHERE identification_number = '$identSafe' AND id <> " . (int) $existing['id']);
} else {
    $db->makeQuery("INSERT INTO form_answers
        (email, identification_number, name, last_name, mobile_phone,
         alternative_mobile_phone, address, country, city, answers,
         is_graduated, is_migrated, is_denied, is_deleted, has_error,
         created_at, modificated_at)
        VALUES (
            '" . addslashes($email) . "', '$identSafe',
            '" . addslashes($name) . "', '" . addslashes($lastName) . "',
            '" . addslashes($phone) . "', '" . addslashes($alternativePhone) . "',
            '" . addslashes($address) . "', '" . addslashes($country) . "',
            '" . addslashes($city) . "', '" . addslashes($jsonAnswers) . "',
            " . (int) $isGraduated . ", 0, 0, 0, 0, NOW(), NOW()
        )");
}

echo json_encode(['success' => true, 'message' => 'Encuesta guardada correctamente']);

function firstAnswer(array $answers, array $keys): mixed
{
    foreach ($keys as $key) {
        if (isset($answers[$key]) && $answers[$key] !== '') {
            return $answers[$key];
        }
    }

    return '';
}

function verifyIfIsGraduated(string $identificationNumber): int
{
    $curl = new \Ospina\CurlCobain\CurlCobain(
        'https://academia.unibague.edu.co/atlante/grad_ver_siga.php'
    );
    $curl->setQueryParamsAsArray([
        'consulta' => 'Consultar',
        'documento' => $identificationNumber,
    ]);

    $response = json_decode((string) $curl->makeRequest(), true);
    return isset($response['data']) ? (int) $response['data'] : 0;
}
