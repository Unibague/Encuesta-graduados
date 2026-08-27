<?php

require __DIR__ . '/autoloader.php';

use Ospina\EasySQL\EasySQL;

verifyIsAuthenticated();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}

$id = (int) ($_POST['id'] ?? 0);
$redirect = '/encuentro_resultados.php';

if ($id <= 0) {
    flashSession('ID de registro inválido');
    header('Location: ' . $redirect);
    exit;
}

$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
$db->makeQuery("DELETE FROM registroacom_2026 WHERE id = $id LIMIT 1");

flashSession('Registro de acompañante ocultado correctamente');
header('Location: ' . $redirect);
exit;
