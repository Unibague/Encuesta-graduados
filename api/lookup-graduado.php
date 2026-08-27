<?php

require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$cedula = trim(preg_replace('/[^0-9]/', '', (string) ($_GET['cedula'] ?? '')));

if (strlen($cedula) < 6) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Cédula inválida']);
    exit;
}

// Prioridad: lo que la persona registró para el Encuentro (la base del Excel de
// asistencia), luego una inscripción previa de esta misma encuesta, y por
// último los datos oficiales vigentes en SIGA para quienes nunca se han
// registrado antes.
$data = consultarDatosEncuentro($cedula);
$data = mergeDatosFaltantes($data, consultarDatosFormAnswers($cedula));

if (empty($data['nombres']) || empty($data['email'])) {
    $data = mergeDatosFaltantes($data, consultarDatosSiga($cedula));
}

echo json_encode([
    'success' => true,
    'found' => !empty(array_filter($data)),
    'data' => $data,
]);

function mergeDatosFaltantes(array $base, array $extra): array
{
    foreach ($extra as $key => $value) {
        if ($value !== '' && $value !== null && empty($base[$key])) {
            $base[$key] = $value;
        }
    }

    return $base;
}

function consultarDatosEncuentro(string $identificacion): array
{
    try {
        $db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
        $idSafe = addslashes($identificacion);

        $row = $db->makeQuery("
            SELECT nombres, apellidos, celular, correo
            FROM encuentro_2026
            WHERE identificacion = '$idSafe'
            LIMIT 1
        ")->fetch_assoc();

        if (!$row) {
            return [];
        }

        return array_filter([
            'nombres' => $row['nombres'] ?? '',
            'apellidos' => $row['apellidos'] ?? '',
            'email' => $row['correo'] ?? '',
            'numero_celular' => $row['celular'] ?? '',
        ]);
    } catch (Throwable $e) {
        error_log('[lookup-graduado] Error consultando encuentro_2026: ' . $e->getMessage());
        return [];
    }
}

function consultarDatosFormAnswers(string $identificacion): array
{
    try {
        $db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
        $idSafe = addslashes($identificacion);

        $row = $db->makeQuery("
            SELECT name, last_name, email, mobile_phone, address, country, city
            FROM form_answers
            WHERE identification_number = '$idSafe' AND is_deleted = 0
            ORDER BY id DESC LIMIT 1
        ")->fetch_assoc();

        if (!$row) {
            return [];
        }

        return array_filter([
            'nombres' => $row['name'] ?? '',
            'apellidos' => $row['last_name'] ?? '',
            'email' => $row['email'] ?? '',
            'numero_celular' => $row['mobile_phone'] ?? '',
            'direccion' => $row['address'] ?? '',
            'pais' => $row['country'] ?? '',
            'ciudad' => $row['city'] ?? '',
        ]);
    } catch (Throwable $e) {
        error_log('[lookup-graduado] Error consultando form_answers: ' . $e->getMessage());
        return [];
    }
}

function consultarDatosSiga(string $identificacion): array
{
    try {
        $curl = new \Ospina\CurlCobain\CurlCobain(
            'https://academia.unibague.edu.co/atlante/grad_dat_siga.php'
        );
        $curl->setQueryParamsAsArray([
            'consulta' => 'Consultar',
            'documento' => $identificacion,
            'dia' => 'N.A',
            'mes' => 'N.A',
            'token' => md5($identificacion) . getenv('SECURE_TOKEN'),
        ]);

        $response = trim((string) $curl->makeRequest());
        if ($response === '') {
            return [];
        }

        $start = strpos($response, '{');
        $end = strrpos($response, '}');
        if ($start === false || $end === false || $end < $start) {
            return [];
        }

        $decoded = json_decode(substr($response, $start, $end - $start + 1), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return [];
        }

        if (isset($decoded['data']) && is_array($decoded['data'])) {
            $decoded = $decoded['data'];
        }

        return array_filter([
            'nombres' => $decoded['Nombres'] ?? '',
            'apellidos' => $decoded['Apellidos'] ?? '',
            'email' => $decoded['Correo'] ?? '',
            'numero_celular' => $decoded['Telefono de contacto'] ?? '',
            'direccion' => $decoded['Direccion de correspondencia'] ?? '',
            'ciudad' => $decoded['Ciudad residencia'] ?? '',
        ]);
    } catch (Throwable $e) {
        error_log('[lookup-graduado] Error consultando SIGA: ' . $e->getMessage());
        return [];
    }
}
