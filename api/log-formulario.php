<?php
/**
 * API para registrar logs del formulario de registro de graduados
 * Recibe eventos del cliente y los guarda en archivo de log
 */

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

// Información del cliente
$ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '')[0]);
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$timestamp = date('Y-m-d H:i:s');

// Construir línea de log
$logData = [
    'timestamp' => $timestamp,
    'ip' => $ip,
    'user_agent' => $userAgent,
    'event_type' => $input['type'] ?? 'unknown',
    'section' => $input['section'] ?? null,
    'field' => $input['field'] ?? null,
    'status' => $input['status'] ?? null,
    'message' => $input['message'] ?? null,
    'error' => $input['error'] ?? null,
    'device_info' => [
        'is_ios' => strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false,
        'is_android' => strpos($userAgent, 'Android') !== false,
        'browser' => getBrowser($userAgent)
    ]
];

// Guardar en archivo JSON (mejor para análisis)
$logFile = $logDir . '/formulario_eventos.jsonl';
$logLine = json_encode($logData) . "\n";
file_put_contents($logFile, $logLine, FILE_APPEND);

// Guardar resumen en texto también
$summaryFile = $logDir . '/formulario_eventos.log';
$summaryLine = sprintf(
    "[%s] %s | %s | Evento: %s | Sección: %s | %s\n",
    $timestamp,
    $ip,
    $logData['device_info']['is_ios'] ? 'iOS' : ($logData['device_info']['is_android'] ? 'Android' : 'Desktop'),
    $input['type'] ?? 'unknown',
    $input['section'] ?? '-',
    $input['message'] ?? $input['error'] ?? ''
);
file_put_contents($summaryFile, $summaryLine, FILE_APPEND);

http_response_code(200);
echo json_encode(['success' => true, 'message' => 'Log guardado']);

function getBrowser($userAgent) {
    if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
    if (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) return 'Safari';
    if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
    if (strpos($userAgent, 'Edge') !== false) return 'Edge';
    return 'Unknown';
}
