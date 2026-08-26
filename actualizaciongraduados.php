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
// BLADE ONE
// =========================
$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
$blade = new BladeOne($views, $cache, BladeOne::MODE_DEBUG);

// =========================
// RENDER
// =========================
try {
    echo $blade->run('actualizaciongraduados');
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}
