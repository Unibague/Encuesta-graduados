<?php

require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => true, 'message' => 'Método no permitido']);
    exit;
}

$raw   = file_get_contents('php://input');
$input = json_decode($raw, true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'Datos inválidos']);
    exit;
}

/* =========================
 * SANITIZAR
 * ========================= */
$cedula     = trim(preg_replace('/[^0-9]/', '', $input['cedula']       ?? ''));
$correoRaw  = trim($input['correo']     ?? '');
$correo     = filter_var($correoRaw, FILTER_VALIDATE_EMAIL) ? $correoRaw : null;
$celular    = normalizarCelular($input['celular']     ?? '');
$celularAlt = normalizarCelular($input['celular_alt'] ?? '');
$ciudad     = trim(strip_tags($input['ciudad']    ?? '')) ?: null;
$direccion  = trim(strip_tags($input['direccion'] ?? '')) ?: null;

if (!$cedula) {
    http_response_code(422);
    echo json_encode(['error' => true, 'message' => 'Cédula requerida']);
    exit;
}

/* =========================
 * VERIFICAR QUE SE REGISTRÓ
 * ========================= */
$db      = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
$idSafe  = addslashes($cedula);

$reg = $db->makeQuery("
    SELECT id FROM encuentro_2026
    WHERE identificacion = '$idSafe'
    LIMIT 1
")->fetch_assoc();

if (!$reg) {
    http_response_code(403);
    echo json_encode(['error' => true, 'message' => 'No se encontró un registro para esta cédula']);
    exit;
}

/* =========================
 * ACTUALIZAR SIGA
 * ========================= */
$response = enviarASiga($cedula, $correo, $celular, $celularAlt, $ciudad, $direccion);

if (!$response || !($response->ok ?? false)) {
    encuentroLog('Fallo SIGA para documento ' . $cedula . ' | ' . ($response->error ?? 'sin detalle'), 'ERROR');
    http_response_code(502);
    echo json_encode([
        'error'   => true,
        'message' => $response->error ?? 'Error al comunicarse con SIGA. Intenta de nuevo.',
    ]);
    exit;
}

/* =========================
 * ACTUALIZAR form_answers (si existe)
 * ========================= */
try {
    $sets = [];
    if ($correo)     $sets[] = "email = '"                       . addslashes($correo)     . "'";
    if ($celular)    $sets[] = "mobile_phone = '"                . addslashes($celular)    . "'";
    if ($celularAlt) $sets[] = "alternative_mobile_phone = '"   . addslashes($celularAlt) . "'";
    if ($ciudad)     $sets[] = "city = '"                        . addslashes($ciudad)     . "'";
    if ($direccion)  $sets[] = "address = '"                     . addslashes($direccion)  . "'";

    if ($sets) {
        $db->makeQuery("
            UPDATE form_answers
            SET " . implode(', ', $sets) . ", updated_at = NOW()
            WHERE identification_number = '$idSafe'
        ");
    }
} catch (Throwable $e) {
    encuentroLog('Error actualizando form_answers: ' . $e->getMessage(), 'WARNING');
}

encuentroLog('Datos actualizados en SIGA para documento ' . $cedula);

echo json_encode(['success' => true, 'message' => 'Datos actualizados correctamente en SIGA']);
exit;

/* =========================
 * FUNCTIONS
 * ========================= */

function enviarASiga(
    string  $cedula,
    ?string $correo,
    ?string $telefono,
    ?string $telAlterno,
    ?string $ciudad,
    ?string $direccion
): ?object {
    $endpoint = 'https://academia.unibague.edu.co/atlante/actualiza_graduados.php';
    $curl = new \Ospina\CurlCobain\CurlCobain($endpoint);

    $data = [
        'consulta'  => 'Modificar',
        'documento' => $cedula,
        'token'     => md5($cedula) . getenv('SECURE_TOKEN'),
    ];

    if ($correo)    $data['correo']      = $correo;
    if ($telefono)  $data['telefono']    = $telefono;
    if ($telAlterno)$data['tel_alterno'] = $telAlterno;
    if ($direccion) $data['direccion']   = $direccion;

    if ($ciudad) {
        $ciudadNorm = normalizarCiudad($ciudad);
        if ($ciudadNorm) $data['ciudad'] = $ciudadNorm;
    }

    encuentroLog('Payload SIGA documento ' . $cedula . ' | ' . json_encode($data, JSON_UNESCAPED_UNICODE));

    $curl->setQueryParamsAsArray($data);
    $response = trim((string) $curl->makeRequest());

    encuentroLog('Respuesta SIGA documento ' . $cedula . ' | ' . substr($response, 0, 600));

    if ($response === '') {
        return (object)['ok' => false, 'error' => 'SIGA no devolvió contenido'];
    }

    // Extraer JSON de la respuesta
    $start = strpos($response, '{');
    $end   = strrpos($response, '}');

    if ($start !== false && $end !== false && $end >= $start) {
        $decoded = json_decode(substr($response, $start, $end - $start + 1), false);

        if (json_last_error() === JSON_ERROR_NONE && is_object($decoded)) {
            if (isset($decoded->error) && $decoded->error) {
                $decoded->ok = false;
                return $decoded;
            }
            if (isset($decoded->success)) {
                $decoded->ok = ($decoded->success !== false);
                return $decoded;
            }
            if (isset($decoded->status)) {
                $s = strtoupper((string)$decoded->status);
                $decoded->ok = !in_array($s, ['ERROR', 'FAIL', 'FAILED'], true);
                return $decoded;
            }
            $decoded->ok = true;
            return $decoded;
        }
    }

    $lower = mb_strtolower($response, 'UTF-8');
    $ok    = str_contains($lower, 'actualizado')
          || str_contains($lower, 'modificado')
          || str_contains($lower, 'exito')
          || str_contains($lower, 'ok');

    return (object)[
        'ok'      => $ok,
        'message' => $response,
        'error'   => $ok ? null : 'Respuesta no válida de SIGA',
    ];
}

function normalizarCelular(string $celular): ?string
{
    $celular = preg_replace('/[^0-9]/', '', $celular);
    if (strlen($celular) === 12 && substr($celular, 0, 2) === '57') {
        $celular = substr($celular, 2);
    }
    return $celular ?: null;
}

function normalizarCiudad(string $city): ?string
{
    $city = strtoupper(trim($city));
    $city = strtr($city, [
        'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U',
        'á'=>'A','é'=>'E','í'=>'I','ó'=>'O','ú'=>'U',
        'Ñ'=>'N','ñ'=>'N',
    ]);
    $city = preg_split('/[-,]/', $city)[0];
    $city = preg_replace('/\s+D\.?C\.?/', '', $city);
    $city = trim($city);

    $allowed = [
        'BOGOTA','MEDELLIN','CALI','BARRANQUILLA','CARTAGENA','SOACHA','CUCUTA','SOLEDAD',
        'BUCARAMANGA','BELLO','VALLEDUPAR','VILLAVICENCIO','SANTA MARTA','IBAGUE','MONTERIA',
        'PEREIRA','MANIZALES','PASTO','NEIVA','PALMIRA','POPAYAN','BUENAVENTURA','ARMENIA',
        'FLORIDABLANCA','SINCELEJO','ITAGUI','TUMACO','ENVIGADO','DOSQUEBRADAS','TULUA',
        'BARRANCABERMEJA','RIOHACHA','URIBIA','MAICAO','PIEDECUESTA','TUNJA','YOPAL',
        'FLORENCIA','GIRON','FACATATIVA','JAMUNDI','FUSAGASUGA','MOSQUERA','CHIA',
        'ZIPAQUIRA','RIONEGRO','MALAMBO','MAGANGUE','MADRID','CARTAGO','TURBO','QUIBDO',
        'APARTADO','SOGAMOSO','OCANA','PITALITO','BUGA','DUITAMA','CIENAGA','AGUACHICA',
        'GIRARDOT','LORICA','TURBACO','IPIALES','FUNZA','SANTANDER DE QUILICHAO',
        'VILLA DEL ROSARIO','SAHAGUN','YUMBO','CERETE','SABANALARGA','CAJICA','ARAUCA',
        'CAUCASIA','LOS PATIOS','MANAURE','TIERRALTA','CANDELARIA','ACACIAS','SABANETA',
        'MONTELIBANO','CALDAS','COPACABANA','CUMARIBO','SANTA ROSA DE CABAL','LA ESTRELLA',
        'CALARCA','ZONA BANANERA','ARJONA','LA DORADA','GARZON','EL CARMEN DE BOLIVAR',
        'COROZAL','FUNDACION','GRANADA','EL BANCO','LA CEJA','ESPINAL','MARINILLA',
        'PUERTO ASIS','BARANOA','GALAPA','VILLAMARIA','AGUSTIN CODAZZI','PLATO',
        'PLANETA RICA','SARAVENA','EL CARMEN DE VIBORAL','LA PLATA','CHIGORODO',
        'SAN MARCOS','CIENAGA DE ORO','MOCOA','SAN GIL','GUARNE','TIBU',
        'SAN JOSE DEL GUAVIARE','SAN ANDRES','FLORIDA','CHIQUINQUIRA','ARAUQUITA',
        'EL CERRITO','GIRARDOTA','BARBOSA','BARBACOAS','EL BAGRE','TUCHIN','PUERTO COLOMBIA',
    ];

    return in_array($city, $allowed, true) ? $city : null;
}

function encuentroLog(string $message, string $level = 'INFO'): void
{
    $date   = date('Y-m-d H:i:s');
    $line   = "[$date][$level] $message" . PHP_EOL;
    $logDir = dirname(__DIR__) . '/logs';

    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    file_put_contents($logDir . '/encuentro.log', $line, FILE_APPEND);
}
