<?php

require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

const ENCUENTRO_CUPO_MAXIMO = 100;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$anioActivo  = obtenerAnioEncuentroActivo();
$registrados = 0;

/* El Google Sheet es la fuente de la verdad; si falla, se cuenta desde la BD. */
$asistentesSheet = obtenerAsistentesConfirmadosSheet();

if ($asistentesSheet !== null) {
    $registrados = count($asistentesSheet);
} else {
    try {
        $db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
        $registrados = (int) ($db->makeQuery("
            SELECT COUNT(*) AS total FROM encuentro_2026
            WHERE asistencia = 'si' AND encuentro_anio = $anioActivo
        ")->fetch_assoc()['total'] ?? 0);
    } catch (Throwable $e) {
        error_log('[cupo-encuentro] Error contando confirmados en BD: ' . $e->getMessage());
    }
}

$disponibles = max(ENCUENTRO_CUPO_MAXIMO - $registrados, 0);

echo json_encode([
    'registrados' => $registrados,
    'capacidad'   => ENCUENTRO_CUPO_MAXIMO,
    'disponibles' => $disponibles,
    'agotado'     => $disponibles <= 0,
]);
