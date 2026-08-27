<?php

require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');

// Red de seguridad: si ocurre un error fatal de PHP que ningún try/catch
// puede atrapar (por ejemplo, un problema de esquema en la base de datos),
// el navegador debe recibir JSON válido en vez de una respuesta vacía
// ("Unexpected end of JSON input").
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        registroGraduadosSheetsLog('Error fatal en save-survey.php: ' . $error['message'] . ' en ' . $error['file'] . ':' . $error['line'], 'ERROR');
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
        }
        echo json_encode([
            'success' => false,
            'message' => 'Ocurrió un error inesperado al guardar la encuesta. Por favor intenta de nuevo.',
        ]);
    }
});

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
if ($surveyType === 'registrograduados') {
    // Edición del encuentro a la que pertenece este registro. No se toma del
    // navegador para evitar que el cliente pueda alterar el año del evento;
    // se usa el año activo configurado por el administrador.
    $storedAnswers['_encuentro_anio'] = obtenerAnioEncuentroActivo();
}
$storedAnswers['_survey_question_types'] = is_array($input['question_types'] ?? null)
    ? $input['question_types']
    : [];
$storedAnswers['_survey_completed_at'] = date('Y-m-d H:i:s');
$jsonAnswers = json_encode($storedAnswers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($jsonAnswers === false) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'No se pudieron serializar las respuestas']);
    exit;
}

try {
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

    if ($surveyType === 'registrograduados') {
        try {
            marcarAsistenciaRegistroGraduados($name, $lastName, $phone, $email);
        } catch (Throwable $e) {
            registroGraduadosSheetsLog('Error marcando asistencia en Sheets: ' . $e->getMessage(), 'ERROR');
        }
    }

    $sigaResponse = sendSurveyDataToSiga(
        $identificationNumber,
        $name,
        $lastName,
        $email,
        $phone,
        $city
    );

    if ($sigaResponse->ok) {
        $db->makeQuery("UPDATE form_answers SET is_migrated = 1, modificated_at = NOW()
            WHERE identification_number = '$identSafe' AND is_deleted = 0");

        echo json_encode([
            'success' => true,
            'message' => 'Encuesta guardada y enviada a SIGA correctamente',
            'siga_sent' => true,
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Encuesta guardada, pero no se pudo enviar a SIGA: ' . $sigaResponse->error,
        'siga_sent' => false,
    ]);
} catch (Throwable $e) {
    registroGraduadosSheetsLog('Error guardando encuesta: ' . $e->getMessage(), 'ERROR');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ocurrió un problema al guardar la encuesta. Por favor intenta de nuevo en unos minutos.',
    ]);
}

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

function sendSurveyDataToSiga(
    string $identificationNumber,
    string $name,
    string $lastName,
    string $email,
    string $phone,
    string $city
): object {
    $data = [
        'consulta' => 'Modificar',
        'documento' => $identificationNumber,
        'nombres' => $name,
        'apellidos' => $lastName,
        'correo' => $email,
        'telefono' => $phone,
        'ciudad' => normalizeCityForSiga($city),
        'token' => md5($identificationNumber) . getenv('SECURE_TOKEN'),
    ];

    try {
        if (function_exists('curl_init')) {
            $curl = new \Ospina\CurlCobain\CurlCobain(
                'https://academia.unibague.edu.co/atlante/actualiza_graduados.php'
            );
            $curl->setQueryParamsAsArray($data);
            $response = trim((string) $curl->makeRequest());
        } else {
            $url = 'https://academia.unibague.edu.co/atlante/actualiza_graduados.php?'
                . http_build_query($data);
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 20,
                    'ignore_errors' => true,
                ],
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                ],
            ]);
            $response = trim((string) file_get_contents($url, false, $context));
        }
    } catch (Throwable $exception) {
        return (object) [
            'ok' => false,
            'error' => 'No se pudo conectar con SIGA: ' . $exception->getMessage(),
        ];
    }

    if ($response === '') {
        return (object) [
            'ok' => false,
            'error' => 'SIGA no devolvió respuesta',
        ];
    }

    $jsonPayload = extractJsonObject($response);
    $decoded = $jsonPayload !== null ? json_decode($jsonPayload, false) : null;

    if (is_object($decoded)) {
        if (isset($decoded->error) && $decoded->error) {
            return (object) [
                'ok' => false,
                'error' => (string) $decoded->error,
            ];
        }

        if (isset($decoded->success)) {
            return (object) [
                'ok' => $decoded->success !== false,
                'error' => $decoded->success === false
                    ? 'SIGA rechazó la actualización'
                    : '',
            ];
        }
    }

    $normalized = strtolower($response);
    $success = str_contains($normalized, 'actualiz')
        || str_contains($normalized, 'modific')
        || str_contains($normalized, 'exito')
        || str_contains($normalized, 'ok');

    return (object) [
        'ok' => $success,
        'error' => $success ? '' : 'Respuesta no válida de SIGA',
    ];
}

function normalizeCityForSiga(string $city): string
{
    $city = trim($city);
    $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $city);

    if ($converted !== false) {
        $city = $converted;
    }

    return strtoupper(trim($city));
}

function extractJsonObject(string $response): ?string
{
    $start = strpos($response, '{');
    $end = strrpos($response, '}');

    if ($start === false || $end === false || $end < $start) {
        return null;
    }

    return substr($response, $start, $end - $start + 1);
}

/**
 * Marca la columna E (¿Asistió?) como "Sí" para el graduado en la hoja
 * "Lista asistencia graduados" al completar la encuesta de registro.
 * Si la persona no tiene fila aún, se agrega una nueva ya marcada.
 */
function marcarAsistenciaRegistroGraduados(
    string $nombres,
    string $apellidos,
    string $celular,
    string $correo
): void {
    $spreadsheetId = '1LockLyDz0texEzDypaRyhqL1uniy4Fpus_FPCPOv2Ec';
    $targetGid = 1419700379;

    $client = new Google_Client();
    $client->setAuthConfig(registroGraduadosCredentialsPath());
    $client->addScope(Google_Service_Sheets::SPREADSHEETS);

    $service = new Google_Service_Sheets($client);
    $spreadsheet = $service->spreadsheets->get($spreadsheetId);
    $sheetName = null;

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
    $params = ['valueInputOption' => 'USER_ENTERED'];

    $existentes = $service->spreadsheets_values->get($spreadsheetId, "{$sheetName}!A4:C");
    $filas = $existentes->getValues() ?? [];

    $nombreBuscado = mb_strtolower(preg_replace('/\s+/', ' ', $nombreCompleto), 'UTF-8');
    $correoBuscado = mb_strtolower(trim($correo), 'UTF-8');

    $numeroFila = null;
    foreach ($filas as $i => $fila) {
        $nombreFila = mb_strtolower(preg_replace('/\s+/', ' ', trim($fila[0] ?? '')), 'UTF-8');
        $correoFila = mb_strtolower(trim($fila[2] ?? ''), 'UTF-8');

        $coincide = ($correoBuscado !== '' && $correoFila === $correoBuscado)
            || ($nombreFila !== '' && $nombreFila === $nombreBuscado);

        if ($coincide) {
            $numeroFila = $i + 4; // A4 es la primera fila de datos
            break;
        }
    }

    if ($numeroFila !== null) {
        $body = new Google_Service_Sheets_ValueRange(['values' => [['Sí']]]);
        $service->spreadsheets_values->update($spreadsheetId, "{$sheetName}!E{$numeroFila}", $body, $params);

        registroGraduadosSheetsLog("Asistencia marcada (fila existente): {$nombreCompleto}");
        return;
    }

    $body = new Google_Service_Sheets_ValueRange(['values' => [[$nombreCompleto, $celular, $correo, '', 'Sí']]]);
    $service->spreadsheets_values->append($spreadsheetId, "{$sheetName}!A:E", $body, $params);

    registroGraduadosSheetsLog("Asistencia marcada (fila nueva): {$nombreCompleto}");
}

function registroGraduadosCredentialsPath(): string
{
    $configuredPath = trim((string) getenv('GOOGLE_CREDENTIALS_PATH'));

    if ($configuredPath !== '' && is_file($configuredPath)) {
        return $configuredPath;
    }

    $localPath = dirname(__DIR__) . '/credentials.json';
    if (is_file($localPath)) {
        return $localPath;
    }

    throw new RuntimeException('No se encontró el archivo de credenciales de Google Sheets.');
}

function registroGraduadosSheetsLog(string $message, string $level = 'INFO'): void
{
    $line = '[' . date('Y-m-d H:i:s') . "][{$level}] {$message}" . PHP_EOL;
    $logDir = dirname(__DIR__) . '/logs';

    if (!is_dir($logDir)) mkdir($logDir, 0777, true);

    file_put_contents($logDir . '/registrograduados_sheets.log', $line, FILE_APPEND);
}
