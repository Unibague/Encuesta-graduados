<?php

require 'autoloader.php';

use Ospina\EasySQL\EasySQL;

$request = parseRequest();

$easySQL = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
$easySQL->table('form_answers')->where('ID', '=', $request->id)->update(
    [
        'is_deleted' => 0
    ]
);
flashSession('Se ha reactivado el registro exitosamente');
header("Location: " . $_SERVER['HTTP_REFERER']);
die();

function parseRequest()
{
    return (object)$_REQUEST;
}



