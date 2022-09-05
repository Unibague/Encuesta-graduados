<?php
require 'vendor/autoload.php';
require 'Helpers/OspinaMysqlHelper.php';

use eftec\bladeone\BladeOne;
use Ospina\SmartQueryBuilder\SmartQueryBuilder;

/* 1) Recibir el ID de la URL (parametro get)*/
$id = getIdFromRequest();


/*2) Consultar ese ID en la DB*/
$mysql = OspinaMysqlHelper::newMysqlObject('encuesta_graduados', 'local');

//building the query
$queryBuilder = SmartQueryBuilder::table('form_answers');
$query = $queryBuilder->select(['*'])
    ->where('id', '=', $id);
$finalQuery = $query->getQuery();
//Execute query
$result = $mysql->makeQuery($finalQuery);
$finalResult = $result->fetch_all(MYSQLI_ASSOC);

//dd($finalResult);
/*3) Obtener los campos necesarios (cedula, correo, respuestas)*/
$finalResult = $finalResult[0];

$email = $finalResult['email'];
$identificationNumber = $finalResult['identification_number'];
$answers = $finalResult['answers'];

/*4) Parsear respuestas*/
$answersAsObject = json_decode($answers, true, 512, JSON_THROW_ON_ERROR);


/*5) Construir front*/
//Esta en result.blade

/*6) Enviar datos al front para renderizar*/
$blade = new BladeOne(); // MODE_DEBUG allows to pinpoint troubles.

try {
    echo $blade->run("result", compact('email', 'identificationNumber', 'answersAsObject'));
} catch (Exception $e) {
    echo 'Ha ocurrido un error';
}

function getIdFromRequest()
{
    return $_GET['id'];
}

function dd($var)
{
    header('Content-Type: application/json;charset=utf-8');
    echo json_encode($var);
    die();
}