<?php
require __DIR__ . '/../app/controllers/autoloader.php';
use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

$result = $db->makeQuery("
    SELECT nombres, apellidos
    FROM encuentro_2026
    WHERE asistencia = 'si'
    ORDER BY created_at ASC
");

$participantes = [];
while ($row = $result->fetch_assoc()) {
    $participantes[] = trim($row['nombres'] . ' ' . $row['apellidos']);
}

echo json_encode(['participantes' => $participantes]);
