<?php

require 'autoloader.php';

use Dotenv\Dotenv;
use Ospina\EasySQL\EasySQL;

// =========================
// AUTH + ENV
// =========================
verifyIsAuthenticated();

$dotenv = Dotenv::createUnsafeImmutable(dirname(__DIR__, 2));
$dotenv->safeLoad();

// =========================
// PARSE REQUEST
// =========================
$request = parseRequest();

if (empty($request->id) || empty($request->identification_number)) {
    flashSession('Datos incompletos para actualizar');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if (!hasUpdateData($request)) {
    $db     = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
    $userId = (int) user()->id;
    $identSafe = addslashes(trim($request->identification_number));
    $db->makeQuery("UPDATE form_answers SET is_migrated = 1, migrated_by = $userId, updated_at = NOW() WHERE identification_number = '$identSafe'");
    flashSession('El registro no tenía datos de contacto para enviar a SIGA y fue marcado como procesado.');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// =========================
// ACTUALIZAR EN SIGA
// =========================
$response = updateUserData($request->identification_number, $request);

if (
    !$response ||
    isset($response->error) ||
    (isset($response->success) && $response->success === false) ||
    ($response->ok ?? false) !== true
) {
    approveLog(
        'Fallo al actualizar SIGA para documento ' . $request->identification_number .
        ' | detalle=' . ($response->debug ?? 'sin detalle'),
        'ERROR'
    );

    flashSession(
        'Error al actualizar SIGA. ' .
        ($response->error ?? 'Respuesta no valida del servicio')
    );
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// =========================
// UPDATE DB
// =========================
$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

$userId    = (int) user()->id;
$identSafe = addslashes(trim($request->identification_number));

$sql = "
    UPDATE form_answers
    SET
        is_migrated = 1,
        migrated_by = $userId,
        updated_at  = NOW()
    WHERE identification_number = '$identSafe'
";

$affected = $db->makeQuery($sql);

if (!$affected) {
    flashSession('No se pudo actualizar el registro');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

$successMessage = 'El registro se actualizo correctamente a SIGA.';
if (($response->warning ?? null) !== null) {
    approveLog(
        'SIGA actualizo con advertencia para documento ' . $request->identification_number .
        ' | warning=' . $response->warning,
        'WARNING_REMOTE'
    );
}

flashSession($successMessage);
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

// =========================
// FUNCTIONS
// =========================

function approveLog(string $message, string $level = 'INFO'): void
{
    $date = date('Y-m-d H:i:s');
    $line = "[$date][$level] $message" . PHP_EOL;
    $logDir = dirname(__DIR__, 2) . '/logs';

    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    file_put_contents($logDir . '/approve.log', $line, FILE_APPEND);
}

function updateUserData(string $identification_number, object $request): ?object
{
    $endpoint = 'https://academia.unibague.edu.co/atlante/actualiza_graduados.php';
    $curl = new \Ospina\CurlCobain\CurlCobain($endpoint);

    $data = [
        'consulta' => 'Modificar',
        'documento' => $identification_number,
        'token' => md5($identification_number) . getenv('SECURE_TOKEN'),
    ];

    if (!empty($request->email)) {
        $data['correo'] = $request->email;
    }

    if (!empty($request->city)) {
        $city = normalizarCiudadParaSiga($request->city);
        if ($city !== null) {
            $data['ciudad'] = $city;
        }
    }

    if (!empty($request->address)) {
        $data['direccion'] = $request->address;
    }

    if (!empty($request->mobile_phone)) {
        $data['telefono'] = $request->mobile_phone;
    }

    if (!empty($request->alternative_mobile_phone)) {
        $data['tel_alterno'] = $request->alternative_mobile_phone;
    }

    approveLog(
        'Payload SIGA documento ' . $identification_number . ' | data=' .
        json_encode($data, JSON_UNESCAPED_UNICODE)
    );

    $curl->setQueryParamsAsArray($data);
    $response = trim((string) $curl->makeRequest());
    $warning = extractWarningText($response);

    approveLog(
        'Respuesta SIGA documento ' . $identification_number . ' | body=' .
        substr(str_replace(["\r", "\n"], ['\\r', '\\n'], $response), 0, 1200)
    );

    if ($response === '') {
        return (object) [
            'ok' => false,
            'error' => 'El servicio SIGA no devolvio contenido',
            'debug' => 'respuesta vacia',
        ];
    }

    $jsonPayload = extractJsonObject($response);
    $decoded = $jsonPayload !== null ? json_decode($jsonPayload, false) : null;

    if (json_last_error() === JSON_ERROR_NONE && is_object($decoded)) {
        if (isset($decoded->error) && $decoded->error) {
            $decoded->ok = false;
            $decoded->debug = $response;
            $decoded->warning = $warning;
            return $decoded;
        }

        if (isset($decoded->success)) {
            $decoded->ok = $decoded->success !== false;
            $decoded->debug = $response;
            $decoded->warning = $warning;
            return $decoded;
        }

        if (isset($decoded->status)) {
            $status = strtoupper((string) $decoded->status);
            $decoded->ok = !in_array($status, ['ERROR', 'FAIL', 'FAILED'], true);
            $decoded->debug = $response;
            $decoded->warning = $warning;
            return $decoded;
        }

        if (isset($decoded->message) || isset($decoded->mensaje) || isset($decoded->data)) {
            $decoded->ok = true;
            $decoded->debug = $response;
            $decoded->warning = $warning;
            return $decoded;
        }
    }

    $normalized = mb_strtolower($response, 'UTF-8');
    $hasPositiveSignal = str_contains($normalized, 'actualizado')
        || str_contains($normalized, 'actualizada')
        || str_contains($normalized, 'modificado')
        || str_contains($normalized, 'modificada')
        || str_contains($normalized, 'exito')
        || str_contains($normalized, 'ok');

    if ($hasPositiveSignal) {
        return (object) [
            'ok' => true,
            'message' => $response,
            'debug' => $response,
            'warning' => $warning,
        ];
    }

    return (object) [
        'ok' => false,
        'error' => 'Respuesta no valida del servicio',
        'debug' => $response,
        'warning' => $warning,
    ];
}

function hasUpdateData(object $request): bool
{
    foreach (['email', 'city', 'address', 'mobile_phone', 'alternative_mobile_phone'] as $field) {
        if (!empty($request->{$field})) {
            return true;
        }
    }

    return false;
}

function normalizarCiudadParaSiga(string $city): ?string
{
    $city = strtoupper(trim($city));
    $city = removeAccents($city);
    $city = preg_split('/[-,]/', $city)[0];
    $city = preg_replace('/\s+D\.?C\.?/', '', $city);
    $city = trim($city);

    $allowed = [
        'BOGOTA', 'MEDELLIN', 'CALI', 'BARRANQUILLA', 'CARTAGENA', 'SOACHA',
        'CUCUTA', 'SOLEDAD', 'BUCARAMANGA', 'BELLO', 'VALLEDUPAR', 'VILLAVICENCIO',
        'SANTA MARTA', 'IBAGUE', 'MONTERIA', 'PEREIRA', 'MANIZALES', 'PASTO',
        'NEIVA', 'PALMIRA', 'POPAYAN', 'BUENAVENTURA', 'ARMENIA', 'FLORIDABLANCA',
        'SINCELEJO', 'ITAGUI', 'TUMACO', 'ENVIGADO', 'DOSQUEBRADAS', 'TULUA',
        'BARRANCABERMEJA', 'RIOHACHA', 'URIBIA', 'MAICAO', 'PIEDECUESTA', 'TUNJA',
        'YOPAL', 'FLORENCIA', 'GIRON', 'FACATATIVA', 'JAMUNDI', 'FUSAGASUGA',
        'MOSQUERA', 'CHIA', 'ZIPAQUIRA', 'RIONEGRO', 'MALAMBO', 'MAGANGUE',
        'MADRID', 'CARTAGO', 'TURBO', 'QUIBDO', 'APARTADO', 'SOGAMOSO', 'OCANA',
        'PITALITO', 'BUGA', 'DUITAMA', 'CIENAGA', 'AGUACHICA', 'GIRARDOT',
        'LORICA', 'TURBACO', 'IPIALES', 'FUNZA', 'SANTANDER DE QUILICHAO',
        'VILLA DEL ROSARIO', 'SAHAGUN', 'YUMBO', 'CERETE', 'SABANALARGA', 'CAJICA',
        'ARAUCA', 'CAUCASIA', 'LOS PATIOS', 'MANAURE', 'TIERRALTA', 'CANDELARIA',
        'ACACIAS', 'SABANETA', 'MONTELIBANO', 'CALDAS', 'COPACABANA', 'CUMARIBO',
        'SANTA ROSA DE CABAL', 'LA ESTRELLA', 'CALARCA', 'ZONA BANANERA', 'ARJONA',
        'LA DORADA', 'GARZON', 'EL CARMEN DE BOLIVAR', 'COROZAL', 'FUNDACION',
        'GRANADA', 'EL BANCO', 'LA CEJA', 'ESPINAL', 'MARINILLA', 'PUERTO ASIS',
        'BARANOA', 'GALAPA', 'VILLAMARIA', 'AGUSTIN CODAZZI', 'PLATO', 'PLANETA RICA',
        'SARAVENA', 'EL CARMEN DE VIBORAL', 'LA PLATA', 'CHIGORODO', 'SAN MARCOS',
        'CIENAGA DE ORO', 'MOCOA', 'SAN GIL', 'GUARNE', 'TIBU', 'SAN JOSE DEL GUAVIARE',
        'SAN ANDRES', 'FLORIDA', 'CHIQUINQUIRA', 'ARAUQUITA', 'EL CERRITO',
        'GIRARDOTA', 'BARBOSA', 'BARBACOAS', 'EL BAGRE', 'TUCHIN', 'PUERTO COLOMBIA',
    ];

    return in_array($city, $allowed, true) ? $city : null;
}

function removeAccents(string $text): string
{
    return strtr($text, [
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
        'á' => 'A', 'é' => 'E', 'í' => 'I', 'ó' => 'O', 'ú' => 'U',
        'Ñ' => 'N', 'ñ' => 'N',
    ]);
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

function extractWarningText(string $response): ?string
{
    $jsonStart = strpos($response, '{');
    $prefix = $jsonStart === false ? $response : substr($response, 0, $jsonStart);
    $prefix = trim($prefix);

    return $prefix !== '' ? $prefix : null;
}

function parseRequest(): object
{
    return (object) $_REQUEST;
}
