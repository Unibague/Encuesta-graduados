<?php

require __DIR__ . '/app/controllers/autoloader.php';

use eftec\bladeone\BladeOne;
use Ospina\EasySQL\EasySQL;
use Dotenv\Dotenv;

// =========================
// AUTH
// =========================
verifyIsAuthenticated();

// =========================
// ENV
// =========================
$dotenv = Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

// =========================
// PAGINACIÓN
// =========================
$page   = max((int)($_GET['page'] ?? 1), 1);
$limit  = 50;
$offset = ($page - 1) * $limit;

// =========================
// BUSCADOR
// =========================
$search = trim($_GET['search'] ?? '');

// =========================
// DB
// =========================
$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

// =========================
// WHERE BASE
// =========================
$where = "
    is_graduated = 1
    AND is_migrated = 0
    AND is_denied = 0
    AND is_deleted = 0
";

// =========================
// BUSCADOR GLOBAL
// =========================
if ($search !== '') {
    $searchSafe = addslashes($search);

    $where .= "
        AND (
            identification_number LIKE '%$searchSafe%'
            OR name LIKE '%$searchSafe%'
            OR last_name LIKE '%$searchSafe%'
            OR email LIKE '%$searchSafe%'
            OR mobile_phone LIKE '%$searchSafe%'
            OR alternative_mobile_phone LIKE '%$searchSafe%'
            OR city LIKE '%$searchSafe%'
            OR address LIKE '%$searchSafe%'
        )
    ";
}

// =========================
// TOTAL REGISTROS
// =========================
$countResult = $db->makeQuery("
    SELECT COUNT(*) AS total
    FROM form_answers
    WHERE $where
");

$totalRow   = $countResult->fetch_assoc();
$total      = (int) ($totalRow['total'] ?? 0);
$totalPages = (int) ceil($total / $limit);

// =========================
// DATOS
// =========================
$graduatedAnswers = $db->makeQuery("
    SELECT *
    FROM form_answers
    WHERE $where
    ORDER BY created_at DESC
    LIMIT $limit OFFSET $offset
")->fetch_all(MYSQLI_ASSOC);

// =========================
// SIGA (INFO OFICIAL)
// =========================
foreach ($graduatedAnswers as $key => $answer) {
    try {
        $graduatedAnswers[$key]['official_answers'] =
            getUserData($answer['identification_number']);
    } catch (Exception $e) {
        $graduatedAnswers[$key]['official_answers'] = [];
    }
}

// =========================
// FLASH MESSAGE (CLAVE)
// =========================
$message = $_SESSION['message'] ?? null;
$error   = $_SESSION['error'] ?? null;

unset($_SESSION['message'], $_SESSION['error']);

// =========================
// BLADE
// =========================
$blade = new BladeOne(
    __DIR__ . '/views',
    __DIR__ . '/cache',
    BladeOne::MODE_AUTO
);

// =========================
// RENDER
// =========================
echo $blade->run('ready', [
    'graduatedAnswers' => $graduatedAnswers,
    'page'             => $page,
    'totalPages'       => $totalPages,
    'search'           => $search,
    'message'          => $message,
    'error'            => $error,
]);

// =========================
// FUNCTIONS
// =========================
function getUserData(string $identification_number): array
{
    $endpoint = 'https://academia.unibague.edu.co/atlante/grad_dat_siga.php';

    $curl = new \Ospina\CurlCobain\CurlCobain($endpoint);
    $curl->setQueryParamsAsArray([
        'consulta'  => 'Consultar',
        'documento' => $identification_number,
        'dia'       => 'N.A',
        'mes'       => 'N.A',
        'token'     => md5($identification_number) . getenv('SECURE_TOKEN'),
    ]);

    return json_decode($curl->makeRequest(), true) ?? [];
}
