<?php

use Ospina\EasySQL\EasySQL;

function obtenerAnioEncuentroActivo(): int
{
    try {
        $db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
        crearTablaEncuentroConfig($db);

        $row = $db->makeQuery("SELECT anio_activo FROM encuentro_config WHERE id = 1 LIMIT 1")->fetch_assoc();

        if (!$row) {
            $db->makeQuery("INSERT INTO encuentro_config (id, anio_activo) VALUES (1, 2026)");
            return 2026;
        }

        return (int) $row['anio_activo'];
    } catch (Throwable $e) {
        error_log('[encuentro_config] Error obteniendo año activo: ' . $e->getMessage());
        return 2026;
    }
}

function establecerAnioEncuentroActivo(int $anio): void
{
    $db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
    crearTablaEncuentroConfig($db);

    $anioSafe = (int) $anio;
    $db->makeQuery("
        INSERT INTO encuentro_config (id, anio_activo)
        VALUES (1, $anioSafe)
        ON DUPLICATE KEY UPDATE anio_activo = $anioSafe, updated_at = NOW()
    ");
}

function crearTablaEncuentroConfig(\Ospina\EasySQL\EasySQL $db): void
{
    $db->makeQuery("
        CREATE TABLE IF NOT EXISTS encuentro_config (
            id TINYINT UNSIGNED NOT NULL PRIMARY KEY DEFAULT 1,
            anio_activo SMALLINT UNSIGNED NOT NULL DEFAULT 2026,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}
