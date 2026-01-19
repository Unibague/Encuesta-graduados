<?php

require __DIR__ . '/app/controllers/autoloader.php';

use eftec\bladeone\BladeOne;
use Ospina\EasySQL\EasySQL;

// =========================
// AUTH
// =========================
verifyIsAuthenticated();

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
// 👉 EXISTE EN SIGA PERO NO ES GRADUADO
$where = "
    is_graduated = 0
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
$notGraduatedAnswers = $db->makeQuery("
    SELECT
        id,
        identification_number,
        name,
        last_name,
        email,
        mobile_phone,
        alternative_mobile_phone,
        country,
        city,
        address,
        created_at
    FROM form_answers
    WHERE $where
    ORDER BY created_at DESC
    LIMIT $limit OFFSET $offset
")->fetch_all(MYSQLI_ASSOC);

// =========================
// FLASH MESSAGE
// =========================
$message = null;
if (!empty($_SESSION['pending'])) {
    $message = $_SESSION['message'] ?? null;
    $_SESSION['pending'] = false;
    $_SESSION['message'] = null;
}

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
echo $blade->run('not_graduated', [
    'graduatedAnswers' => $notGraduatedAnswers, // reutilizamos la vista
    'page'             => $page,
    'totalPages'       => $totalPages,
    'search'           => $search,
    'message'          => $message
]);
