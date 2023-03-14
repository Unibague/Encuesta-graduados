<?php

require __DIR__ . '/app/controllers/autoloader.php';

use Dotenv\Dotenv;
const GOOGLE_FORM_URL = 'https://docs.google.com/forms/d/e/1FAIpQLSf1ijgwz_RiQDP9zzI6YeM6n_Hl2Gnad03wu_mimLZ1PxPn-A/viewform';

//Get HTTP params
$request = parseRequest();
//Validate the request
validatedRequest($request);

//Search on DB
$result = searchUser($request);


//Format data
$formattedData = formatData($result);
//Build final form URL

$finalUrl = buildUrl($formattedData);

//dd($finalUrl);
header('Location: ' . $finalUrl);
function buildUrl($formattedData)
{
    return GOOGLE_FORM_URL . '?' . http_build_query($formattedData);
}

function validatedRequest($request)
{
    $verifiedRequest = verifyRequest($request);
    //Send error as responses

    if ($verifiedRequest['error'] === true) {
        response($verifiedRequest, 400);
    }
}

function formatData($result)
{
    $formItems = getFormItems();
    $finalResult = [];
    foreach ($formItems as $key => $formItem) {
        $finalResult[$formItem['googleFormId']] = $result[$key];
    }
    return $finalResult;
}

function searchUser(array $request)
{
    //$endpoint = 'https://academia.unibague.edu.co/atlante/graduados.php';
    $endpoint = 'https://academia.unibague.edu.co/atlante/graduados_sia.php';
    $curl = new \Ospina\CurlCobain\CurlCobain($endpoint);
    $curl->setQueryParamsAsArray([
        'consulta' => 'Consultar',
        'documento' => $request['identification_number'],
        'dia' => $request['day'],
        'mes' => $request['month'],
        'token' => md5($request['identification_number']) . getenv('SECURE_TOKEN')
    ]);
    $response = $curl->makeRequest();
    return json_decode($response, true);
}

function getFormItems()
{
    return [
        "Nombres" => ["answer" => null, "googleFormId" => "entry.98280260"],
        "Apellidos" => ["answer" => null, "googleFormId" => "entry.275477632"],
        "Numero de identificacion" => ["answer" => null, "googleFormId" => "entry.315622645"],
        "Telefono alterno" => ["answer" => null, "googleFormId" => "entry.60355407"],
        "Telefono de contacto" => ["answer" => null, "googleFormId" => "entry.42357451"],
        "Annio de graduacion" => ["answer" => null, "googleFormId" => "entry.1060955643"],
        "Direccion de correspondencia" => ["answer" => null, "googleFormId" => "entry.1600532275"],
        "Programa del cual es egresado" => ["answer" => null, "googleFormId" => "entry.666708053"],
        "Nivel académico alcanzado" => ["answer" => null, "googleFormId" => "entry.865564300"],
        "Correo" => ["answer" => null, "googleFormId" => "emailAddress"],
    ];
}

function response($content, $status)
{
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($content);
    die();
}

function parseRequest()
{
    return $_REQUEST;
}


function verifyRequest(array $request)
{
    if (empty($request['identification_number']) || empty($request['day']) || empty($request['month'])) {
        return ['error' => true, 'msg' => 'Por favor ingresa tu número de documento y fecha de nacimiento'];
    }
    return ['error' => false];

}
