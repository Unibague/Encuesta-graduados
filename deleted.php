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
// DB
// =========================
$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

// =========================
// TOTAL REGISTROS
// =========================
$countResult = $db->makeQuery("
    SELECT COUNT(*) AS total
    FROM form_answers
    WHERE is_deleted = 1
");

$totalRow   = $countResult->fetch_assoc();
$total      = (int) ($totalRow['total'] ?? 0);
$totalPages = (int) ceil($total / $limit);

// =========================
// DATOS
// =========================
$deletedAnswers = $db->makeQuery("
    SELECT *
    FROM form_answers
    WHERE is_deleted = 1
    ORDER BY created_at DESC
    LIMIT $limit OFFSET $offset
")->fetch_all(MYSQLI_ASSOC);

// =========================
// FLASH MESSAGE
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
echo $blade->run('deleted', [
    'deletedAnswers' => $deletedAnswers,
    'page'           => $page,
    'totalPages'     => $totalPages,
    'message'        => $message,
    'error'          => $error,
]);
