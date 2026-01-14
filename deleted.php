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
// ENV (FALTA ESTO)
// =========================
$dotenv = Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

// =========================
// DB
// =========================
$deletedConnection = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

$deletedAnswers = $deletedConnection
    ->table('form_answers')
    ->select(['*'])
    ->where('is_deleted', '=', 1)
    ->get();

// =========================
// BLADE
// =========================
$viewsPath = __DIR__ . '/views';
$cachePath = __DIR__ . '/cache';

$blade = new BladeOne($viewsPath, $cachePath, BladeOne::MODE_AUTO);

// =========================
// RENDER
// =========================
$isPending = $_SESSION['pending'] ?? false;

if ($isPending) {
    $message = $_SESSION['message'] ?? null;
    $_SESSION['message'] = null;
    $_SESSION['pending'] = false;

    echo $blade->run("deleted", compact('deletedAnswers', 'message'));
} else {
    echo $blade->run("deleted", compact('deletedAnswers'));
}
