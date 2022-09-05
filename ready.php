<?php
session_start();
require 'vendor/autoload.php';
require 'Helpers/Auth.php';

use eftec\bladeone\BladeOne;
use Ospina\EasySQL\EasySQL;
use Dotenv\Dotenv;

//Check if is auth
verifyIsAuthenticated();
//Prepare dotenv
$dotenv = Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

//create db object
$graduatedAnswersConnection = new EasySQL('encuesta_graduados', 'local');
$graduatedAnswers = $graduatedAnswersConnection->table('form_answers')->select(['*'])
    ->where('is_graduated', '=', 1)
    ->where('is_migrated', '=', 0)
    ->where('is_denied', '=', 0)
    ->where('is_deleted', '=', 0)
    ->get();

$graduatedAnswers = array_slice($graduatedAnswers, 0, 10);
//Get data from siga
foreach ($graduatedAnswers as $key => $answer) {
    $graduatedAnswers[$key]['official_answers'] = getUserData($answer['identification_number']);
}


$blade = new BladeOne();
try {
    $isPending = $_SESSION['pending'] ?? false;
    if ($isPending) {
        //Almacenar variable
        $message = $_SESSION['message'];

        //Limpiar variables antes de renderizar
        $_SESSION['message'] = '';
        $_SESSION['pending'] = false;
        echo $blade->run("ready", compact('graduatedAnswers', 'message'));
    } else {
        echo $blade->run("ready", compact('graduatedAnswers'));
    }

} catch (Exception $e) {
    echo 'Ha ocurrido un error';
}

function getUserData(string $identification_number)
{
    $endpoint = 'https://academia.unibague.edu.co/atlante/grad_dat_siga.php';
    $curl = new \Ospina\CurlCobain\CurlCobain($endpoint);
    $curl->setQueryParamsAsArray([
        'consulta' => 'Consultar',
        'documento' => $identification_number,
        'dia' => 'N.A',
        'mes' => 'N.A',
        'token' => md5($identification_number) . getenv('SECURE_TOKEN')
    ]);
    $response = $curl->makeRequest();

    return json_decode($response, true);
}

function dd($var)
{
    header('Content-Type: application/json;charset=utf-8');
    echo json_encode($var);
    die();
}