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
    error_log('[SIGA] ' . $e->getMessage());
    flashSession('Error consultando SIGA');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// =========================
// UPDATE DB (FORMA SEGURA)
// =========================
try {
    $db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

    $db->makeQuery("
        UPDATE form_answers
        SET 
            is_graduated = " . (int)$isGraduated . ",
            updated_at = NOW()
        WHERE id = " . $id . "
    ");

} catch (Throwable $e) {
    error_log('[DB UPDATE] ' . $e->getMessage());
    flashSession('Error actualizando el registro en la base de datos');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// =========================
// FEEDBACK REAL
// =========================
flashSession($isGraduated === 1 ? 'El usuario ha sido migrado exitosamente' : 'El usuario aún no se encuentra migrado en el SIGA');


header("Location: " . $_SERVER['HTTP_REFERER']);
exit;


// =========================
// FUNCIÓN SIGA
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

    return (int) $decoded['data']; // 0 | 1
}
