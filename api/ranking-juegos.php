<?php

require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$anioActivo = obtenerAnioEncuentroActivo();
$ranking    = [];

try {
    $db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
    crearTablaJuegosPuntajes($db);

    $filas = $db->makeQuery("
        SELECT nombre_completo, juego1, juego2, juego3,
               (COALESCE(juego1,0) + COALESCE(juego2,0) + COALESCE(juego3,0)) AS total
        FROM juegos_puntajes_2026
        WHERE encuentro_anio = $anioActivo
        ORDER BY total DESC, updated_at ASC
        LIMIT 10
    ")->fetch_all(MYSQLI_ASSOC);

    foreach ($filas as $fila) {
        $ranking[] = [
            'nombre' => trim($fila['nombre_completo']),
            'juego1' => $fila['juego1'] !== null ? (int) $fila['juego1'] : null,
            'juego2' => $fila['juego2'] !== null ? (int) $fila['juego2'] : null,
            'juego3' => $fila['juego3'] !== null ? (int) $fila['juego3'] : null,
            'total'  => (int) $fila['total'],
        ];
    }
} catch (Throwable $e) {
    error_log('[ranking-juegos] Error consultando ranking: ' . $e->getMessage());
}

echo json_encode(['ranking' => $ranking]);
