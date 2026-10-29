<?php

require __DIR__ . '/../app/controllers/autoloader.php';
require_once __DIR__ . '/../Helpers/VerificacionGraduado.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metodo no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$cedula = trim(preg_replace('/\D+/', '', (string) ($input['cedula'] ?? '')));

$inicio = microtime(true);

if (strlen($cedula) < 6 || strlen($cedula) > 15) {
    verificarGraduadoLog($cedula, 'cedula_invalida', false, $inicio);
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Ingresa una cedula valida']);
    exit;
}

// SIGA -> endpoint alterno.
// Si no aparece en ningun sistema se le pide comunicarse con el
// administrador para validar su condicion de graduado antes de continuar.
$result = verificarElegibilidadGraduado($cedula);
verificarGraduadoLog($cedula, $result->status, $result->eligible, $inicio);

echo json_encode([
    'success' => true,
    'status' => $result->status,
    'eligible' => $result->eligible,
    'graduated' => $result->graduated,
    'data' => $result->data,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

/**
 * Registra cada verificación de cédula: quién la pidió (IP, navegador),
 * qué resultado dio y cuánto tardó. Sirve para comparar, por ejemplo, si a
 * un dispositivo (iPhone) la petición le llega al servidor y qué responde,
 * contra otro (Android) que sí funciona.
 */
function verificarGraduadoLog(string $cedula, string $status, bool $eligible, float $inicio): void
{
    $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '')[0]);
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $duracionMs = (int) round((microtime(true) - $inicio) * 1000);

    $line = '[' . date('Y-m-d H:i:s') . "] cedula={$cedula} status={$status} eligible=" . ($eligible ? 'si' : 'no')
        . " duracion_ms={$duracionMs} ip={$ip} ua=\"{$userAgent}\"" . PHP_EOL;

    $logDir = dirname(__DIR__) . '/logs';
    if (!is_dir($logDir)) mkdir($logDir, 0777, true);

    file_put_contents($logDir . '/verificar-graduado.log', $line, FILE_APPEND);
}
