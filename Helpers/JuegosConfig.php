<?php

function crearTablaJuegosPuntajes(\Ospina\EasySQL\EasySQL $db): void
{
    $db->makeQuery("
        CREATE TABLE IF NOT EXISTS juegos_puntajes_2026 (
            id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nombre_completo VARCHAR(150) NOT NULL,
            juego1          INT NULL,
            juego2          INT NULL,
            juego3          INT NULL,
            encuentro_anio  SMALLINT UNSIGNED NOT NULL DEFAULT 2026,
            created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_nombre_anio (nombre_completo, encuentro_anio)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    /* Compatibilidad con instalaciones donde la tabla se creó con el esquema
       anterior (basado en cédula), antes de pasar a usar el nombre del Sheet
       como identificador de cada graduado en los juegos. */
    $columna = $db->makeQuery("SHOW COLUMNS FROM juegos_puntajes_2026 LIKE 'nombre_completo'")->fetch_assoc();
    if (!$columna) {
        $db->makeQuery("ALTER TABLE juegos_puntajes_2026 ADD COLUMN nombre_completo VARCHAR(150) NOT NULL DEFAULT '' AFTER id");
        $db->makeQuery("UPDATE juegos_puntajes_2026 SET nombre_completo = TRIM(CONCAT(nombres, ' ', apellidos)) WHERE nombre_completo = ''");
    }

    /* Las columnas del esquema anterior (identificacion, nombres, apellidos)
       ya no se usan, pero si quedaron como NOT NULL sin valor por defecto
       rompen los INSERT nuevos. Se relajan para que no bloqueen la escritura. */
    foreach (['identificacion', 'nombres', 'apellidos'] as $columnaVieja) {
        $existe = $db->makeQuery("SHOW COLUMNS FROM juegos_puntajes_2026 LIKE '$columnaVieja'")->fetch_assoc();
        if ($existe && $existe['Null'] === 'NO') {
            $db->makeQuery("ALTER TABLE juegos_puntajes_2026 MODIFY COLUMN `$columnaVieja` VARCHAR(150) NULL DEFAULT NULL");
        }
    }
}
