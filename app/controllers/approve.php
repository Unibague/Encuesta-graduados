<?php

require 'autoloader.php';

use Ospina\EasySQL\EasySQL;

$request = parseRequest();
$response = updateUserData($request->identification_number, $request);
if (isset($response->error)) {
    flashSession('Ha ocurrido el siguiente error: ' . $response->error . '. Favor comunicarse con el administrador del sistema');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    die();
}

$easySQL = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
$easySQL->table('form_answers')->where('ID', '=', $request->id)->update(
    [
        'is_migrated' => 1,
        'migrated_by' => user()->id
    ]
);
flashSession('Se ha migrado el registro a SIGA exitosamente');
header("Location: " . $_SERVER['HTTP_REFERER']);
die();

function updateUserData(string $identification_number, object $request)
{
    $endpoint = 'https://academia.unibague.edu.co/atlante/actualiza_graduados.php';
    $curl = new \Ospina\CurlCobain\CurlCobain($endpoint);
    $data = [
        'consulta' => 'Modificar',
        'documento' => $identification_number,
        'token' => md5($identification_number) . getenv('SECURE_TOKEN'),
    ];

    if ($request->email) {
        $data['correo'] = $request->email;
    }
    if ($request->city) {
        $data['ciudad'] = $request->city;
    }
    if ($request->address) {
        $data['direccion'] = $request->address;
    }
    if ($request->mobile_phone) {
        $data['telefono'] = $request->mobile_phone;
    }
    if ($request->alternative_mobile_phone) {
        $data['tel_alterno'] = $request->alternative_mobile_phone;
    }

    $curl->setQueryParamsAsArray($data);

    $response = $curl->makeRequest();
    return json_decode($response, false);
}

function parseRequest()
{
    return (object)$_REQUEST;
}



