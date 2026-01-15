<?php

require __DIR__ . '/autoloader.php';

use Ospina\EasySQL\EasySQL;

// =========================
// VALIDAR INPUT
// =========================
if (!isset($_POST['id'], $_POST['identification_number'])) {
    flashSession('Datos incompletos para sincronizar');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

$id = (int) $_POST['id'];
$identificationNumber = trim($_POST['identification_number']);

// =========================
// CONSULTAR SIGA
// =========================
try {
    $isGraduated = verifyIfIsGraduated($identificationNumber);
} catch (Exception $e) {
    error_log('SIGA error: ' . $e->getMessage());
    flashSession('Error consultando SIGA');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// =========================
// UPDATE (INSTANCIA NUEVA)
// =========================
$dbUpdate = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

$dbUpdate->table('form_answers')
    ->where('id', '=', $id)
    ->update([
        'is_graduated' => (int) $isGraduated,
        'updated_at'   => date('Y-m-d H:i:s')
    ]);

// =========================
// FEEDBACK
// =========================
flashSession($isGraduated === 1 ? 'El usuario ha sido migrado exitosamente' : 'El usuario aún no se encuentra migrado en el SIGA');


header("Location: " . $_SERVER['HTTP_REFERER']);
exit;


// =========================
// FUNCTION SIGA
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

    if (!isset($decoded['data'])) {
        throw new Exception('Respuesta inválida de SIGA');
    }

    return (int) $decoded['data'];
}
