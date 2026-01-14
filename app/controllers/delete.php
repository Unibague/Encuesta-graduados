<?php
require __DIR__ . '/autoloader.php';

use Ospina\EasySQL\EasySQL;
use Dotenv\Dotenv;

// =========================
// ENV
// =========================
$dotenv = Dotenv::createUnsafeImmutable(dirname(__DIR__, 2));
$dotenv->load();

// =========================
// AUTH
// =========================
verifyIsAuthenticated();

// =========================
// REQUEST
// =========================
$request = (object) $_REQUEST;

// =========================
// DB
// =========================
$easySQL = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

$easySQL
    ->table('form_answers')
    ->where('id', '=', $request->id)
    ->update([
        'is_deleted' => 1,
        'deleted_by' => user()->id,
        'deleted_at' => date('Y-m-d H:i:s')
    ]);

// =========================
// FLASH + REDIRECT
// =========================
flashSession('Se ha rechazado el registro exitosamente');
header("Location: ".$_SERVER['HTTP_REFERER']);
exit;

