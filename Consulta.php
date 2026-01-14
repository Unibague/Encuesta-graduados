<?php

require __DIR__ . '/app/controllers/autoloader.php';

use Ospina\CurlCobain\CurlCobain;

const GOOGLE_FORM_URL =
    'https://docs.google.com/forms/d/e/1FAIpQLSf1ijgwz_RiQDP9zzI6YeM6n_Hl2Gnad03wu_mimLZ1PxPn-A/viewform';

/**
 * =========================
 * REQUEST
 * =========================
 */
$request = $_GET;

/**
 * =========================
 * VALIDACIÓN BÁSICA
 * =========================
 */
if (
    empty($request['identification_number']) ||
    empty($request['day']) ||
    empty($request['month'])
) {
    respondJson(
        ['error' => true, 'msg' => 'Por favor ingresa tu documento y fecha de nacimiento'],
        400
    );
}

/**
 * =========================
 * CONSULTAR ATLANTE
 * =========================
 */
$result = searchUser($request);

if (empty($result) || !is_array($result)) {
    respondJson(
        ['error' => true, 'msg' => 'No fue posible obtener la información del egresado'],
        502
    );
}

/**
 * =========================
 * FORMATEAR DATA
 * =========================
 */
$formattedData = formatData($result);

/**
 * =========================
 * REDIRECT AL FORM
 * =========================
 */
$finalUrl = GOOGLE_FORM_URL . '?' . http_build_query($formattedData);

header('Location: ' . $finalUrl);
exit;

/**
 * =========================
 * FUNCTIONS
 * =========================
 */

function searchUser(array $request): array
{
    $endpoint = 'https://academia.unibague.edu.co/atlante/graduados_sia.php';

    $curl = new CurlCobain($endpoint);

    $curl->setQueryParamsAsArray([
        'consulta'  => 'Consultar',
        'documento' => $request['identification_number'],
        'dia'       => $request['day'],
        'mes'       => $request['month'],
        'token'     => md5($request['identification_number']) . getenv('SECURE_TOKEN'),
    ]);

    $response = $curl->makeRequest();

    $decoded = json_decode($response, true);

    if (!is_array($decoded)) {
        error_log('Respuesta inválida Atlante: ' . $response);
        return [];
    }

    return $decoded;
}

function formatData(array $result): array
{
    $formItems = getFormItems();
    $final     = [];

    foreach ($formItems as $key => $formItem) {
        $final[$formItem['googleFormId']] = $result[$key] ?? '';
    }

    return $final;
}

function getFormItems(): array
{
    return [
        "Nombres"                         => ["googleFormId" => "entry.98280260"],
        "Apellidos"                       => ["googleFormId" => "entry.275477632"],
        "Numero de identificacion"        => ["googleFormId" => "entry.315622645"],
        "Telefono alterno"                => ["googleFormId" => "entry.60355407"],
        "Telefono de contacto"            => ["googleFormId" => "entry.42357451"],
        "Annio de graduacion"             => ["googleFormId" => "entry.1060955643"],
        "Direccion de correspondencia"    => ["googleFormId" => "entry.1600532275"],
        "Programa del cual es egresado"   => ["googleFormId" => "entry.666708053"],
        "Nivel académico alcanzado"       => ["googleFormId" => "entry.865564300"],
        "Correo"                          => ["googleFormId" => "emailAddress"],
    ];
}

function respondJson(array $content, int $status): void
{
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($status);
    echo json_encode($content, JSON_UNESCAPED_UNICODE);
    exit;
}
