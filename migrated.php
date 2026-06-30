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
// PAGINACIÓN
// =========================
$page   = max((int) ($_GET['page'] ?? 1), 1);
$limit  = 20;
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
$where = "is_migrated = 1 AND is_deleted = 0";

if ($search !== '') {
    $s = addslashes($search);
    $where .= "
        AND (
            identification_number LIKE '%$s%'
            OR name               LIKE '%$s%'
            OR last_name          LIKE '%$s%'
            OR email              LIKE '%$s%'
            OR mobile_phone       LIKE '%$s%'
            OR city               LIKE '%$s%'
        )
    ";
}

// =========================
// COUNT
// =========================
$total = (int) $db->makeQuery("
    SELECT COUNT(*) AS total FROM form_answers WHERE $where
")->fetch_assoc()['total'];

$totalPages = max((int) ceil($total / $limit), 1);

// =========================
// DATA
// =========================
$migratedAnswers = $db->makeQuery("
    SELECT
        fa.id,
        fa.identification_number,
        fa.name,
        fa.last_name,
        fa.email,
        fa.mobile_phone,
        fa.alternative_mobile_phone,
        fa.city,
        fa.is_graduated,
        fa.is_denied,
        fa.updated_at,
        u.username AS migrated_by_name
    FROM form_answers fa
    LEFT JOIN users u ON u.id = fa.migrated_by
    WHERE $where
    ORDER BY fa.updated_at DESC
    LIMIT $limit OFFSET $offset
")->fetch_all(MYSQLI_ASSOC);

// =========================
// FLASH
// =========================
$message = $_SESSION['message'] ?? null;
$error   = $_SESSION['error']   ?? null;
unset($_SESSION['message'], $_SESSION['error']);

// =========================
// BLADE
// =========================
$blade = new BladeOne(__DIR__ . '/views', __DIR__ . '/cache', BladeOne::MODE_AUTO);

echo $blade->run('migrated', [
    'migratedAnswers' => $migratedAnswers,
    'page'            => $page,
    'totalPages'      => $totalPages,
    'total'           => $total,
    'search'          => $search,
    'message'         => $message,
    'error'           => $error,
]);
