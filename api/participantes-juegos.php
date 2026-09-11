<?php

require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$anioActivo = obtenerAnioEncuentroActivo();

/* Nombres confirmados: el Google Sheet es la fuente principal (igual que la
   ruleta); si falla, se usa la base de datos como respaldo. */
$nombres = obtenerAsistentesConfirmadosSheet();

$dbDisponible = false;
$db = null;

try {
    $db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
    $dbDisponible = true;
} catch (Throwable $e) {
    error_log('[participantes-juegos] BD no disponible: ' . $e->getMessage());
}

if ($nombres === null) {
    $nombres = [];

    if ($dbDisponible) {
        try {
            $filas = $db->makeQuery("
                SELECT nombres, apellidos FROM encuentro_2026
                WHERE asistencia = 'si' AND encuentro_anio = $anioActivo
            ")->fetch_all(MYSQLI_ASSOC);

            foreach ($filas as $fila) {
                $nombres[] = trim($fila['nombres'] . ' ' . $fila['apellidos']);
            }
        } catch (Throwable $e) {
            error_log('[participantes-juegos] Error consultando participantes en BD: ' . $e->getMessage());
        }
    }
}

/* Acompañantes registrados (registroacom.php): igual que los graduados, la
   hoja "Acompañantes" del Sheet es la fuente principal; si falla, se usa la
   base de datos (registroacom_2026) como respaldo. Solo se necesita su
   nombre para que también aparezcan en la búsqueda y el ranking de juegos. */
$nombresAcom = obtenerAcompanantesConfirmadosSheet();

if ($nombresAcom === null && $dbDisponible) {
    $nombresAcom = [];

    try {
        $filasAcom = $db->makeQuery("
            SELECT nombres, apellidos FROM registroacom_2026
            WHERE encuentro_anio = $anioActivo
        ")->fetch_all(MYSQLI_ASSOC);

        foreach ($filasAcom as $fila) {
            $nombresAcom[] = trim($fila['nombres'] . ' ' . $fila['apellidos']);
        }
    } catch (Throwable $e) {
        error_log('[participantes-juegos] Error consultando acompañantes en BD: ' . $e->getMessage());
    }
}

$nombres = array_merge($nombres, $nombresAcom ?? []);

$nombres = array_values(array_unique(array_filter(array_map('trim', $nombres))));
sort($nombres, SORT_STRING | SORT_FLAG_CASE);

/* Puntajes ya guardados, para prellenar la vista de juegos.php */
$puntajesPorNombre = [];

if ($dbDisponible) {
    try {
        crearTablaJuegosPuntajes($db);

        $filas = $db->makeQuery("
            SELECT nombre_completo, juego1, juego2, juego3
            FROM juegos_puntajes_2026
            WHERE encuentro_anio = $anioActivo
        ")->fetch_all(MYSQLI_ASSOC);

        foreach ($filas as $fila) {
            $clave = mb_strtolower(trim($fila['nombre_completo']), 'UTF-8');
            $puntajesPorNombre[$clave] = [
                'juego1' => $fila['juego1'] !== null ? (int) $fila['juego1'] : null,
                'juego2' => $fila['juego2'] !== null ? (int) $fila['juego2'] : null,
                'juego3' => $fila['juego3'] !== null ? (int) $fila['juego3'] : null,
            ];
        }
    } catch (Throwable $e) {
        error_log('[participantes-juegos] Error consultando puntajes: ' . $e->getMessage());
    }
}

$participantes = array_map(static function (string $nombre) use ($puntajesPorNombre): array {
    $clave = mb_strtolower($nombre, 'UTF-8');
    $p     = $puntajesPorNombre[$clave] ?? ['juego1' => null, 'juego2' => null, 'juego3' => null];

    return array_merge(['nombre' => $nombre], $p);
}, $nombres);

echo json_encode(['participantes' => $participantes]);
