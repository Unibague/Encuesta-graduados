<?php

require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => true, 'message' => 'Método no permitido']);
    exit;
}

$raw   = file_get_contents('php://input');
$input = json_decode($raw, true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'Datos inválidos']);
    exit;
}

/* =========================
 * SANITIZAR
 * ========================= */
$nombre = trim(strip_tags($input['nombre'] ?? ''));
$juego1 = sanitizarPuntaje($input['juego1'] ?? null);
$juego2 = sanitizarPuntaje($input['juego2'] ?? null);
$juego3 = sanitizarPuntaje($input['juego3'] ?? null);

if ($nombre === '' || mb_strlen($nombre, 'UTF-8') > 150) {
    http_response_code(422);
    echo json_encode(['error' => true, 'message' => 'Debes seleccionar un graduado de la lista.']);
    exit;
}

if ($juego1 === false || $juego2 === false || $juego3 === false) {
    http_response_code(422);
    echo json_encode(['error' => true, 'message' => 'Los puntajes deben ser números entre 0 y 100000.']);
    exit;
}

/* =========================
 * DB
 * ========================= */
$anioActivo = obtenerAnioEncuentroActivo();
$db         = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

crearTablaJuegosPuntajes($db);

$nombreSafe = addslashes($nombre);
$now        = date('Y-m-d H:i:s');

$existente = $db->makeQuery("
    SELECT id FROM juegos_puntajes_2026
    WHERE nombre_completo = '$nombreSafe' AND encuentro_anio = $anioActivo
    LIMIT 1
")->fetch_assoc();

$j1 = $juego1 === null ? 'NULL' : (int) $juego1;
$j2 = $juego2 === null ? 'NULL' : (int) $juego2;
$j3 = $juego3 === null ? 'NULL' : (int) $juego3;

if ($existente) {
    $db->makeQuery("
        UPDATE juegos_puntajes_2026 SET
            juego1     = $j1,
            juego2     = $j2,
            juego3     = $j3,
            updated_at = '$now'
        WHERE id = {$existente['id']}
    ");
} else {
    $db->makeQuery("
        INSERT INTO juegos_puntajes_2026
            (nombre_completo, juego1, juego2, juego3, encuentro_anio, created_at, updated_at)
        VALUES (
            '$nombreSafe', $j1, $j2, $j3, $anioActivo, '$now', '$now'
        )
    ");
}

echo json_encode([
    'success' => true,
    'message' => 'Puntaje guardado',
    'total'   => (int) ($juego1 ?? 0) + (int) ($juego2 ?? 0) + (int) ($juego3 ?? 0),
]);
exit;

/**
 * Devuelve el puntaje como entero (0-100000), null si viene vacío,
 * o false si el valor no es un número. Los valores fuera de rango se
 * ajustan al límite más cercano en vez de rechazarse, para no bloquear
 * el guardado por un typo o un clic de más en el spinner del campo.
 */
function sanitizarPuntaje($valor)
{
    if ($valor === null || $valor === '') {
        return null;
    }

    if (is_string($valor)) {
        $valor = str_replace(',', '.', trim($valor));
    }

    if (!is_numeric($valor)) {
        return false;
    }

    $numero = (int) round((float) $valor);

    return max(0, min(100000, $numero));
}
