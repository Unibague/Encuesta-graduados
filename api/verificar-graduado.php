<?php

require __DIR__ . '/../app/controllers/autoloader.php';
require_once __DIR__ . '/../Helpers/SigaGraduado.php';
require_once __DIR__ . '/../Helpers/EndpointAlternoGraduado.php';
require_once __DIR__ . '/../Helpers/ListaManualGraduados.php';

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

// Nivel 1: SIGA
$siga = consultarElegibilidadGraduadoSiga($cedula);
if ($siga->eligible) {
    verificarGraduadoLog($cedula, 'graduado', true, $inicio);
    echo json_encode([
        'success' => true,
        'status' => 'graduado',
        'eligible' => true,
        'graduated' => $siga->graduated,
        'data' => $siga->data,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Nivel 2: endpoint alterno (aun sin definir; ver Helpers/EndpointAlternoGraduado.php)
$alterno = consultarGraduadoEndpointAlterno($cedula);
if ($alterno->eligible) {
    verificarGraduadoLog($cedula, 'alterno', true, $inicio);
    echo json_encode([
        'success' => true,
        'status' => 'alterno',
        'eligible' => true,
        'graduated' => false,
        'data' => $alterno->data,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Nivel 3: lista manual "IngresoGraduados" en Google Sheets, donde el
// administrador agrega a mano a quienes valido como graduados.
$lista = consultarListaManualGraduados($cedula);
if ($lista->eligible) {
    verificarGraduadoLog($cedula, 'lista_manual', true, $inicio);
    echo json_encode([
        'success' => true,
        'status' => 'lista_manual',
        'eligible' => true,
        'graduated' => false,
        'data' => $lista->data,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// No aparece en ningun sistema: se le pide comunicarse con el administrador
// para validar su condicion de graduado antes de continuar.
verificarGraduadoLog($cedula, 'no_encontrado', false, $inicio);
echo json_encode([
    'success' => true,
    'status' => 'no_encontrado',
    'eligible' => false,
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
