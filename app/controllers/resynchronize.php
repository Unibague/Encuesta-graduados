<?php

require 'autoloader.php';

use Ospina\EasySQL\EasySQL;

verifyIsAuthenticated();

// =========================
// INPUT
// =========================
if (!isset($_POST['id'], $_POST['identification_number'])) {
    flashSession('Datos incompletos');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

$id = $_POST['id'];
$identificationNumber = $_POST['identification_number'];

// =========================
// SIGA
// =========================
try {
    $isGraduated = verifyIfIsGraduated($identificationNumber);
} catch (Exception $e) {
    error_log($e->getMessage());
    $isGraduated = 0;
}

// =========================
// DB UPDATE
// =========================
$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

$db->table('form_answers')
    ->where('id', '=', $id)
    ->update([
        'is_graduated' => $isGraduated,
        'updated_at'   => date('Y-m-d H:i:s')
    ]);

// =========================
// FLASH
// =========================
flashSession($isGraduated === 1 ? 'El usuario ha sido migrado exitosamente' : 'El usuario aún no se encuentra migrado en el SIGA');


header("Location: " . $_SERVER['HTTP_REFERER']);
exit;

// =========================
// SIGA FUNCTION
// =========================
function verifyIfIsGraduated(string $identification_number): int
{
    $endpoint = 'https://academia.unibague.edu.co/atlante/grad_ver_siga.php';

    $curl = new \Ospina\CurlCobain\CurlCobain($endpoint);
    $curl->setQueryParamsAsArray([
        'consulta'  => 'Consultar',
        'documento' => $identification_number,
    ]);

    $response = $curl->makeRequest();
    $decoded  = json_decode($response, true);

    return (int)($decoded['data'] ?? 0);
}
