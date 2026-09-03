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

if (strlen($cedula) < 6 || strlen($cedula) > 15) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Ingresa una cedula valida']);
    exit;
}

// Nivel 1: SIGA
$siga = consultarElegibilidadGraduadoSiga($cedula);
if ($siga->eligible) {
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
echo json_encode([
    'success' => true,
    'status' => 'no_encontrado',
    'eligible' => false,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
