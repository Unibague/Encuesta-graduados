<?php

require __DIR__ . '/autoloader.php';

verifyIsAuthenticated();

$redirect = '/encuentro_resultados.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirect);
    exit;
}

// El switch no pide escribir el año: siempre sincroniza con el año calendario
// actual del servidor, calculado dinámicamente en cada activación.
$anio = (int) date('Y');

establecerAnioEncuentroActivo($anio);

flashSession("El Encuentro de Graduados $anio quedó activo. Los nuevos registros y el filtro de resultados usarán este año.");
header('Location: ' . $redirect . '?anio=' . $anio);
exit;
