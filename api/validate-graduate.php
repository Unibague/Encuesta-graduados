<?php

require __DIR__ . '/../app/controllers/autoloader.php';
require_once __DIR__ . '/../Helpers/SigaGraduado.php';

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

$result = consultarElegibilidadGraduadoSiga($cedula);
if (!$result->available) {
    http_response_code(503);
    echo json_encode([
        'success' => false,
        'message' => 'No fue posible consultar SIGA en este momento. Intenta nuevamente.',
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'eligible' => $result->eligible,
    'graduated' => $result->graduated,
    'data' => $result->data,
    'message' => $result->eligible
        ? 'Documento validado correctamente.'
        : 'La cedula no corresponde a un graduado o egresado de la Universidad de Ibague. Comunicate con desarrolladorg3@unibague.edu.co.',
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
