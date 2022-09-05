<?php

require 'autoloader.php';

use Ospina\EasySQL\EasySQL;


$request = parseRequest();

$easySQL = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
$easySQL->table('form_answers')->where('ID', '=', $request->id)->update(
    [
        'is_denied' => 1,
        'denied_by' => user()->id,
    ]
);
flashSession('Se ha rechazado el registro exitosamente');
header("Location: " . $_SERVER['HTTP_REFERER']);
die();

function parseRequest()
{
    return (object)$_REQUEST;
}



