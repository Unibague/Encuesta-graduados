<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../Helpers/Sessions.php';
require_once __DIR__ . '/../../Helpers/Auth.php';

use Dotenv\Dotenv;

// Iniciar sesión UNA sola vez
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar variables de entorno
$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../../');
$dotenv->load();

// Helper debug
function dd($var)
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($var, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    die();
}
