<?php

require 'autoloader.php';

use Ospina\EasySQL\EasySQL;

$request = parseRequest();

$response = updateUserData($request->identification_number, $request);


$easySQL = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
$easySQL->table('form_answers')->where('ID', '=', $request->id)->update(
    [
        'is_migrated' => 1,
        'migrated_by' => user()->id
    ]
);
flashSession('Se ha migrado el registro a siga exitosamente');
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
        'correo' => $request->email,
        'ciudad' => $request->city,
        'direccion' => $request->address,
        'telefono' => $request->mobile_phone,
        'tel_alterno' => $request->alternative_mobile_phone
    ];

    $curl->setQueryParamsAsArray($data);

    $response = $curl->makeRequest();
    return json_decode($response, true);
}

function parseRequest()
{
    return (object)$_REQUEST;
}



