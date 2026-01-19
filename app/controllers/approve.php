<?php

require 'autoloader.php';

use Ospina\EasySQL\EasySQL;
use Dotenv\Dotenv;

// =========================
// AUTH + ENV
// =========================
verifyIsAuthenticated();

// Cargar .env desde la raíz del proyecto
$dotenv = Dotenv::createUnsafeImmutable(dirname(__DIR__, 2));
$dotenv->safeLoad();

// =========================
// PARSE REQUEST
// =========================
$request = parseRequest();

if (
    empty($request->id) ||
    empty($request->identification_number)
) {
    flashSession('Datos incompletos para actualizar');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// =========================
// ACTUALIZAR EN SIGA
// =========================
$response = updateUserData($request->identification_number, $request);

if (
    !$response ||
    isset($response->error) ||
    (isset($response->success) && $response->success === false)
) {
    flashSession(
        'Error al actualizar SIGA. ' .
        ($response->error ?? 'Respuesta no válida del servicio')
    );
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// =========================
// UPDATE DB (SQL PLANO, SEGURO)
// =========================
$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

$id     = (int) $request->id;
$userId = (int) user()->id;

$sql = "
    UPDATE form_answers
    SET
        is_migrated = 1,
        migrated_by = $userId,
        updated_at  = NOW()
    WHERE id = $id
";

$affected = $db->makeQuery($sql);

if (!$affected) {
    flashSession('No se pudo actualizar el registro');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// =========================
// OK
// =========================
flashSession('El registro se actualizó correctamente a SIGA');
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;

// =========================
// FUNCTIONS
// =========================

function updateUserData(string $identification_number, object $request): ?object
{
    $endpoint = 'https://academia.unibague.edu.co/atlante/actualiza_graduados.php';
    $curl = new \Ospina\CurlCobain\CurlCobain($endpoint);

    $data = [
        'consulta'  => 'Modificar',
        'documento' => $identification_number,
        'token'     => md5($identification_number) . getenv('SECURE_TOKEN'),
    ];

    if (!empty($request->email)) {
        $data['correo'] = $request->email;
    }
if (!empty($request->city)) {
    $ciudad = normalizarCiudadParaSiga($request->city);

    if ($ciudad !== null) {
        $data['ciudad'] = $ciudad;
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

    $curl->setQueryParamsAsArray($data);
    $response = $curl->makeRequest();

    return $response ? json_decode($response, false) : null;
}

function convertir(string $text): string
{
    $text = strtoupper(trim($text));

    return strtr($text, [
        'Á' => 'A', 'É' => 'E', 'Í' => 'I',
        'Ó' => 'O', 'Ú' => 'U',
        'á' => 'A', 'é' => 'E', 'í' => 'I',
        'ó' => 'O', 'ú' => 'U',
        'ñ' => 'N', 'Ñ' => 'N',
    ]);
}

function normalizarCiudadParaSiga(string $city): ?string
{
    // 1. Normalizar texto base
    $city = strtoupper(trim($city));

    // 2. Eliminar tildes y caracteres especiales
    $city = strtr($city, [
        'Á' => 'A', 'É' => 'E', 'Í' => 'I',
        'Ó' => 'O', 'Ú' => 'U',
        'Ñ' => 'N'
    ]);

    // 3. Eliminar texto adicional (departamento, país, etc.)
    // Ej: "BOGOTA, D.C." → "BOGOTA"
    // Ej: "IBAGUE - TOLIMA" → "IBAGUE"
    $city = preg_split('/[-,]/', $city)[0];
    $city = preg_replace('/\s+D\.?C\.?/','',$city);
    $city = trim($city);

    // 4. Catálogo de ciudades aceptadas por SIGA
    $allowed = [
        'BOGOTA',
        'MEDELLIN',
        'CALI',
        'BARRANQUILLA',
        'CARTAGENA',
        'SOACHA',
        'CUCUTA',
        'SOLEDAD',
        'BUCARAMANGA',
        'BELLO',
        'VALLEDUPAR',
        'VILLAVICENCIO',
        'SANTA MARTA',
        'IBAGUE',
        'MONTERIA',
        'PEREIRA',
        'MANIZALES',
        'PASTO',
        'NEIVA',
        'PALMIRA',
        'POPAYAN',
        'BUENAVENTURA',
        'ARMENIA',
        'FLORIDABLANCA',
        'SINCELEJO',
        'ITAGUI',
        'TUMACO',
        'ENVIGADO',
        'DOSQUEBRADAS',
        'TULUA',
        'BARRANCABERMEJA',
        'RIOHACHA',
        'URIBIA',
        'MAICAO',
        'PIEDECUESTA',
        'TUNJA',
        'YOPAL',
        'FLORENCIA',
        'GIRON',
        'FACATATIVA',
        'JAMUNDI',
        'FUSAGASUGA',
        'MOSQUERA',
        'CHIA',
        'ZIPAQUIRA',
        'RIONEGRO',
        'MALAMBO',
        'MAGANGUE',
        'MADRID',
        'CARTAGO',
        'TURBO',
        'QUIBDO',
        'APARTADO',
        'SOGAMOSO',
        'OCANA',
        'PITALITO',
        'BUGA',
        'DUITAMA',
        'CIENAGA',
        'AGUACHICA',
        'GIRARDOT',
        'LORICA',
        'TURBACO',
        'IPIALES',
        'FUNZA',
        'SANTANDER DE QUILICHAO',
        'VILLA DEL ROSARIO',
        'SAHAGUN',
        'YUMBO',
        'CERETE',
        'SABANALARGA',
        'CAJICA',
        'ARAUCA',
        'CAUCASIA',
        'LOS PATIOS',
        'MANAURE',
        'TIERRALTA',
        'CANDELARIA',
        'ACACIAS',
        'SABANETA',
        'MONTELIBANO',
        'CALDAS',
        'COPACABANA',
        'CUMARIBO',
        'SANTA ROSA DE CABAL',
        'LA ESTRELLA',
        'CALARCA',
        'ZONA BANANERA',
        'ARJONA',
        'LA DORADA',
        'GARZON',
        'EL CARMEN DE BOLIVAR',
        'COROZAL',
        'FUNDACION',
        'GRANADA',
        'EL BANCO',
        'LA CEJA',
        'ESPINAL',
        'MARINILLA',
        'PUERTO ASIS',
        'BARANOA',
        'GALAPA',
        'VILLAMARIA',
        'AGUSTIN CODAZZI',
        'PLATO',
        'PLANETA RICA',
        'SARAVENA',
        'EL CARMEN DE VIBORAL',
        'LA PLATA',
        'CHIGORODO',
        'SAN MARCOS',
        'CIENAGA DE ORO',
        'MOCOA',
        'SAN GIL',
        'GUARNE',
        'TIBU',
        'SAN JOSE DEL GUAVIARE',
        'SAN ANDRES',
        'FLORIDA',
        'CHIQUINQUIRA',
        'ARAUQUITA',
        'EL CERRITO',
        'GIRARDOTA',
        'BARBOSA',
        'BARBACOAS',
        'EL BAGRE',
        'TUCHIN',
        'PUERTO COLOMBIA'
    ];

    // 5. Validación final
    return in_array($city, $allowed, true) ? $city : null;
}


function parseRequest(): object
{
    return (object) $_REQUEST;
}
