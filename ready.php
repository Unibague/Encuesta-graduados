<?php

require __DIR__ . '/app/controllers/autoloader.php';

use Dotenv\Dotenv;
use eftec\bladeone\BladeOne;
use Ospina\EasySQL\EasySQL;

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
// PAGINATION
// =========================
$page = max((int) ($_GET['page'] ?? 1), 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// =========================
// SEARCH
// =========================
$search = trim($_GET['search'] ?? '');

// =========================
// DB
// =========================
$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

// =========================
// BASE WHERE
// =========================
$where = "
    is_graduated = 1
    AND is_migrated = 0
    AND is_denied = 0
    AND is_deleted = 0
";

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
// COUNT
// =========================
$countResult = $db->makeQuery("
    SELECT COUNT(*) AS total
    FROM form_answers
    WHERE $where
");

$totalRow = $countResult->fetch_assoc();
$total = (int) ($totalRow['total'] ?? 0);
$totalPages = (int) ceil($total / $limit);

// =========================
// DATA
// =========================
$graduatedAnswers = $db->makeQuery("
    SELECT *
    FROM form_answers
    WHERE $where
    ORDER BY created_at DESC
    LIMIT $limit OFFSET $offset
")->fetch_all(MYSQLI_ASSOC);

// =========================
// OFFICIAL DATA FROM SIGA
// =========================
foreach ($graduatedAnswers as $key => $answer) {
    try {
        $graduatedAnswers[$key]['official_answers'] = getUserData($answer['identification_number']);
    } catch (Throwable $e) {
        readyLog(
            'Fallo consultando SIGA para documento ' . $answer['identification_number'] .
            ' | error=' . $e->getMessage(),
            'ERROR'
        );
        $graduatedAnswers[$key]['official_answers'] = [];
    }
}

// =========================
// FLASH MESSAGE
// =========================
$message = $_SESSION['message'] ?? null;
$error = $_SESSION['error'] ?? null;

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
    'page' => $page,
    'totalPages' => $totalPages,
    'search' => $search,
    'message' => $message,
    'error' => $error,
]);

// =========================
// FUNCTIONS
// =========================
function getUserData(string $identification_number): array
{
    $endpoint = 'https://academia.unibague.edu.co/atlante/grad_dat_siga.php';

    $curl = new \Ospina\CurlCobain\CurlCobain($endpoint);
    $curl->setQueryParamsAsArray([
        'consulta' => 'Consultar',
        'documento' => $identification_number,
        'dia' => 'N.A',
        'mes' => 'N.A',
        'token' => md5($identification_number) . getenv('SECURE_TOKEN'),
    ]);

    $response = trim((string) $curl->makeRequest());

    readyLog(
        'Respuesta grad_dat_siga documento ' . $identification_number . ' | body=' .
        substr(str_replace(["\r", "\n"], ['\\r', '\\n'], $response), 0, 1200)
    );

    if ($response === '') {
        return [];
    }

    $jsonPayload = extractJsonObject($response);
    if ($jsonPayload === null) {
        return [];
    }

    $decoded = json_decode($jsonPayload, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
        readyLog(
            'JSON invalido grad_dat_siga documento ' . $identification_number .
            ' | json=' . $jsonPayload,
            'ERROR'
        );
        return [];
    }

    if (isset($decoded['data']) && is_array($decoded['data'])) {
        return $decoded['data'];
    }

    return $decoded;
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

function readyLog(string $message, string $level = 'INFO'): void
{
    $date = date('Y-m-d H:i:s');
    $line = "[$date][$level] $message" . PHP_EOL;
    $logDir = __DIR__ . '/logs';

    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    file_put_contents($logDir . '/ready.log', $line, FILE_APPEND);
}
