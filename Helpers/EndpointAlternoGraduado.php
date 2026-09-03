<?php

/**
 * Segunda fuente de verificacion de graduados, para usar cuando SIGA no
 * reconoce el documento. Consulta ver_graduados_bd.php, que recibe la
 * cedula en el parametro "document" (OJO: no "documento" -- con ese nombre
 * el endpoint ignora el filtro y devuelve el listado completo) y responde
 * con un arreglo JSON de coincidencias, p. ej.:
 * [{"base":"POSGRADOS","name":"LUZ ENID HERNANDEZ AROS","documento":"65750539","program":"2201","nom_programa":"ESP. EN TELEINFORMATICA"}]
 * o [] cuando no hay coincidencias.
 */
function consultarGraduadoEndpointAlterno(string $identificationNumber): object
{
    $endpointUrl = trim((string) getenv('GRADUADOS_ENDPOINT_ALTERNO_URL'));

    if ($endpointUrl === '') {
        return (object) [
            'available' => false,
            'eligible' => false,
            'data' => [],
            'error' => 'Endpoint alterno no configurado',
        ];
    }

    try {
        $curl = new \Ospina\CurlCobain\CurlCobain($endpointUrl);
        $curl->setCurlOption(CURLOPT_CONNECTTIMEOUT, 5);
        $curl->setCurlOption(CURLOPT_TIMEOUT, 15);
        $curl->setQueryParamsAsArray([
            'document' => $identificationNumber,
        ]);

        $response = trim((string) $curl->makeRequest());
        $decoded = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return (object) [
                'available' => false,
                'eligible' => false,
                'data' => [],
                'error' => 'Respuesta invalida del endpoint alterno',
            ];
        }

        $match = $decoded[0] ?? null;

        return (object) [
            'available' => true,
            'eligible' => is_array($match),
            'data' => is_array($match) ? array_filter(['nombres' => trim((string) ($match['name'] ?? ''))]) : [],
            'error' => '',
        ];
    } catch (Throwable $e) {
        return (object) [
            'available' => false,
            'eligible' => false,
            'data' => [],
            'error' => $e->getMessage(),
        ];
    }
}
