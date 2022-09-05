<?php
session_start();
require 'vendor/autoload.php';
require 'Helpers/Auth.php';

use eftec\bladeone\BladeOne;
use Ospina\EasySQL\EasySQL;

//Check if is auth
verifyIsAuthenticated();

//create db object
$deletedConnection = new EasySQL('encuesta_graduados', 'local');
$deletedAnswers = $deletedConnection->table('form_answers')->select(['*'])
    ->where('is_deleted', '=', 1)
    ->get();

$blade = new BladeOne();
try {
    $isPending = $_SESSION['pending'] ?? false;
    if ($isPending) {
        //Almacenar variable
        $message = $_SESSION['message'];

        //Limpiar variables antes de renderizar
        $_SESSION['message'] = '';
        $_SESSION['pending'] = false;
        echo $blade->run("deleted", compact('deletedAnswers', 'message'));
    } else {
        echo $blade->run("deleted", compact('deletedAnswers'));
    }

} catch (Exception $e) {
    echo 'Ha ocurrido un error';
}

function dd($var)
{
    header('Content-Type: application/json;charset=utf-8');
    echo json_encode($var);
    die();
}