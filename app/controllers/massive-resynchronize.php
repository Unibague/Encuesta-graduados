<?php
require 'autoloader.php';

use Ospina\EasySQL\EasySQL;

$pendingAnswersConnection = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
$pendingAnswers = $pendingAnswersConnection->table('form_answers')->select(['identification_number','id'])
    ->where('is_graduated', '=', 0)
    ->where('is_migrated', '=', 0)
    ->where('is_denied', '=', 0)
    ->where('is_deleted', '=', 0)
    ->get();


foreach ($pendingAnswers as $answer) {
    try {
        $isGraduated = verifyIfIsGraduated($answer['identification_number']);
        //Actualizar DataBase
        $mysql = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
        $result = $mysql->table('form_answers')
            ->where('id', '=', $answer['id'])
            ->update([
                'is_graduated' => $isGraduated
            ]);


    } catch (JsonException $e) {

    }
}

//---------------- Functions-------------------


/**
 * @param string $identification_number
 * @return int
 * @throws JsonException
 */
function verifyIfIsGraduated(string $identification_number): int
{
    $endpoint = 'https://academia.unibague.edu.co/atlante/graduados_ver_siga.php';
    $curl = new \Ospina\CurlCobain\CurlCobain($endpoint);
    $curl->setQueryParamsAsArray([
        'consulta' => 'Consultar',
        'documento' => $identification_number,
    ]);
    $response = $curl->makeRequest();
    return json_decode($response, true, 512, JSON_THROW_ON_ERROR)['data'];
}

/**
 * @param $var
 * @return void
 */


