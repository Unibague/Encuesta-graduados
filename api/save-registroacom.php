<?php

require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$consentimiento = trim(strip_tags((string) ($input['consentimiento'] ?? '')));
$nombres = trim(strip_tags((string) ($input['nombres'] ?? '')));
$apellidos = trim(strip_tags((string) ($input['apellidos'] ?? '')));
$cedula = trim(preg_replace('/[^0-9]/', '', (string) ($input['cedula'] ?? '')));

if ($consentimiento !== 'Sí' || $nombres === '' || $apellidos === '' || $cedula === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Completa el consentimiento y todos los datos personales.']);
    exit;
}

$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
$db->makeQuery("
    CREATE TABLE IF NOT EXISTS registroacom_2026 (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        cedula VARCHAR(30) NOT NULL,
        nombres VARCHAR(100) NOT NULL,
        apellidos VARCHAR(100) NOT NULL,
        consentimiento ENUM('Sí') NOT NULL DEFAULT 'Sí',
        encuentro_anio SMALLINT UNSIGNED NOT NULL DEFAULT 2026,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_registroacom_cedula (cedula)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

// Compatibilidad con instalaciones donde la tabla se creó antes de agregar
// el año del encuentro.
$yearColumn = $db->makeQuery("SHOW COLUMNS FROM registroacom_2026 LIKE 'encuentro_anio'")->fetch_assoc();
if (!$yearColumn) {
    $db->makeQuery("ALTER TABLE registroacom_2026
        ADD COLUMN encuentro_anio SMALLINT UNSIGNED NOT NULL DEFAULT 2026 AFTER consentimiento");
}

$cedulaSafe = addslashes($cedula);
$nombresSafe = addslashes($nombres);
$apellidosSafe = addslashes($apellidos);

$existing = $db->makeQuery("SELECT id FROM registroacom_2026 WHERE cedula = '$cedulaSafe' LIMIT 1")->fetch_assoc();

if ($existing) {
    $db->makeQuery("UPDATE registroacom_2026 SET
        nombres = '$nombresSafe',
        apellidos = '$apellidosSafe',
        consentimiento = 'Sí',
        encuentro_anio = 2026,
        updated_at = NOW()
        WHERE id = " . (int) $existing['id']);
} else {
    $db->makeQuery("INSERT INTO registroacom_2026
        (cedula, nombres, apellidos, consentimiento, encuentro_anio, created_at, updated_at)
        VALUES ('$cedulaSafe', '$nombresSafe', '$apellidosSafe', 'Sí', 2026, NOW(), NOW())");
}

echo json_encode(['success' => true, 'message' => 'Registro guardado correctamente']);
