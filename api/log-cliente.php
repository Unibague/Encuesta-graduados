<?php

require __DIR__ . '/../app/controllers/autoloader.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false]);
    exit;
}

$pagina = preg_replace('/[^a-z0-9_-]/i', '', (string) ($input['pagina'] ?? 'cliente'));
$pagina = $pagina !== '' ? $pagina : 'cliente';

$ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '')[0]);
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// Se guarda tal cual llega (sin asumir campos fijos), para poder loguear
// tanto errores de JS (mensaje/archivo/linea/stack) como eventos de
// diagnóstico puntuales (p. ej. "cédula verificada, avanzando a sección X").
$datos = json_encode($input, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$line = '[' . date('Y-m-d H:i:s') . "] ip={$ip} ua=\"{$userAgent}\" datos={$datos}" . PHP_EOL;

$logDir = dirname(__DIR__) . '/logs';
if (!is_dir($logDir)) mkdir($logDir, 0777, true);

file_put_contents($logDir . "/cliente_{$pagina}.log", $line, FILE_APPEND);

echo json_encode(['success' => true]);
