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
$answers = normalizeAnswers($payload['answers']);

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

function normalizeAnswers(array $rawAnswers): array
{
    $normalized = [];

    foreach ($rawAnswers as $key => $value) {
        $normalized[mb_strtolower(trim((string) $key), 'UTF-8')] = is_array($value)
            ? $value
            : [$value];
    }

    return $normalized;
}

function keepExistingIfMissing(?string $incomingValue, ?array $existingRow, string $column): string
{
    if ($incomingValue !== null) {
        return $incomingValue;
    }

    return (string) ($existingRow[$column] ?? '');
}

function extractNormalizedFields(array $answers): array
{
    return [
        'email' => getAnswer($answers, [
            'email address',
            "correo electr\u{00F3}nico",
            'correo electronico',
            'email'
        ]),
        'name' => getAnswer($answers, ['nombres', 'nombre']),
        'last' => getAnswer($answers, ['apellidos', 'apellido']),
        'phone' => getAnswer($answers, ["tel\u{00E9}fono de contacto", 'telefono']),
        'alt' => getAnswer($answers, ["tel\u{00E9}fono alterno de contacto"]),
        'city' => getAnswer($answers, ['ciudad']),
        'country' => getAnswer($answers, ["pa\u{00ED}s", 'pais']),
        'address' => getAnswer($answers, ["direcci\u{00F3}n de correspondencia", 'direccion']),
    ];
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
$fields = extractNormalizedFields($answers);
$email   = $fields['email'];
$name    = $fields['name'];
$last    = $fields['last'];
$phone   = $fields['phone'];
$alt     = $fields['alt'];
$city    = $fields['city'];
$country = $fields['country'];
$address = $fields['address'];

$now = date('Y-m-d H:i:s');

/* =========================
 * DB
 * ========================= */
$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

/* =========================
 * BUSCAR REGISTRO EXISTENTE
 * ========================= */
$result = $db->makeQuery("
    SELECT
        id,
        is_graduated,
        email,
        name,
        last_name,
        mobile_phone,
        alternative_mobile_phone,
        city,
        country,
        address,
        answers
    FROM form_answers
    WHERE identification_number = '" . addslashes($identificationNumber) . "'
    LIMIT 1
");

$row = $result->fetch_assoc();

$currentIsGraduated = ($row && $row['is_graduated'] !== null) ? (int)$row['is_graduated'] : null;
$isDocumentOnlyPayload = count($answers) === 1;

if ($row && !$isDocumentOnlyPayload && !empty($row['answers'])) {
    $storedAnswers = json_decode((string) $row['answers'], true);

    if (is_array($storedAnswers)) {
        $answers = array_replace(normalizeAnswers($storedAnswers), $answers);
    }
}

$fields = extractNormalizedFields($answers);
$email   = $fields['email'];
$name    = $fields['name'];
$last    = $fields['last'];
$phone   = $fields['phone'];
$alt     = $fields['alt'];
$city    = $fields['city'];
$country = $fields['country'];
$address = $fields['address'];

if ($row) {
    $email   = keepExistingIfMissing($email, $row, 'email');
    $name    = keepExistingIfMissing($name, $row, 'name');
    $last    = keepExistingIfMissing($last, $row, 'last_name');
    $phone   = keepExistingIfMissing($phone, $row, 'mobile_phone');
    $alt     = keepExistingIfMissing($alt, $row, 'alternative_mobile_phone');
    $city    = keepExistingIfMissing($city, $row, 'city');
    $country = keepExistingIfMissing($country, $row, 'country');
    $address = keepExistingIfMissing($address, $row, 'address');
}

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
} elseif ($sigaGraduated === 0) {
    $finalIsGraduated = 0;
} else {
    $finalIsGraduated = $currentIsGraduated;
}

/* =========================
 * UPDATE
 * ========================= */
if ($row) {

    if ($isDocumentOnlyPayload) {
        appLog("RESINCRONIZANDO ID {$row['id']} sin sobrescribir datos | is_graduated={$finalIsGraduated}");

        $db->makeQuery("
            UPDATE form_answers SET
                is_graduated = " . ($finalIsGraduated === null ? 'NULL' : (int)$finalIsGraduated) . ",
                updated_at = '$now'
            WHERE id = {$row['id']}
        ");

        echo json_encode([
            'status' => 'resynced',
            'id' => $row['id'],
            'is_graduated' => $finalIsGraduated
        ]);
        exit;
    }

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
if ($isDocumentOnlyPayload) {
    appLog("OMITIDO documento {$identificationNumber}: payload minimo sin registro existente", 'WARNING');
    http_response_code(202);
    echo json_encode([
        'status' => 'skipped',
        'reason' => 'document-only payload cannot create a complete record',
        'is_graduated' => $finalIsGraduated
    ]);
    exit;
}

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
