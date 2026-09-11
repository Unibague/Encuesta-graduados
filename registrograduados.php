<?php

require __DIR__ . '/app/controllers/autoloader.php';

use Dotenv\Dotenv;
use eftec\bladeone\BladeOne;

// =========================
// ENV
// =========================
$dotenv = Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

// =========================
// LOG DE ACCESO (para poder ver quién abrió el formulario, desde qué
// dispositivo/navegador y cuándo, útil para depurar problemas como el de
// los celulares que se quedaban "trabados" al escanear el QR)
// =========================
registrarAccesoFormulario('registrograduados');

// =========================
// BLADE ONE
// =========================
$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
$blade = new BladeOne($views, $cache, BladeOne::MODE_DEBUG);

// =========================
// RENDER
// =========================
try {
    echo $blade->run('registrograduados');
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}

function registrarAccesoFormulario(string $formulario): void
{
    $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '')[0]);
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';

    $line = '[' . date('Y-m-d H:i:s') . "] ip={$ip} referer=\"{$referer}\" ua=\"{$userAgent}\"" . PHP_EOL;

    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) mkdir($logDir, 0777, true);

    file_put_contents($logDir . "/{$formulario}_accesos.log", $line, FILE_APPEND);
}
