<?php

/**
 * Consulta si un documento corresponde a un graduado o egresado y recupera
 * los datos de contacto disponibles en SIGA.
 */
function consultarElegibilidadGraduadoSiga(string $identificationNumber): object
{
    $identificationNumber = trim(preg_replace('/\D+/', '', $identificationNumber));
    if ($identificationNumber === '') {
        return (object) [
            'available' => true,
            'eligible' => false,
            'graduated' => false,
            'data' => [],
            'error' => 'Cedula invalida',
        ];
    }

    $graduated = false;
    $eligibleByStatus = false;
    $graduateData = [];
    $successfulQueries = 0;
    $errors = [];

    try {
        $curl = new \Ospina\CurlCobain\CurlCobain(
            'https://academia.unibague.edu.co/atlante/grad_ver_siga.php'
        );
        $curl->setCurlOption(CURLOPT_CONNECTTIMEOUT, 5);
        $curl->setCurlOption(CURLOPT_TIMEOUT, 15);
        $curl->setQueryParamsAsArray([
            'consulta' => 'Consultar',
            'documento' => $identificationNumber,
        ]);

        $response = trim((string) $curl->makeRequest());
        $decoded = decodificarRespuestaJsonSiga($response);
        if (is_array($decoded) && array_key_exists('data', $decoded)) {
            $status = (int) $decoded['data'];
            $graduated = $status === 1;
            $eligibleByStatus = $status > 0;
            $successfulQueries++;
        } else {
            $errors[] = 'SIGA no devolvio el estado de graduacion';
        }
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }

    try {
        $curl = new \Ospina\CurlCobain\CurlCobain(
            'https://academia.unibague.edu.co/atlante/grad_dat_siga.php'
        );
        $curl->setCurlOption(CURLOPT_CONNECTTIMEOUT, 5);
        $curl->setCurlOption(CURLOPT_TIMEOUT, 15);
        $curl->setQueryParamsAsArray([
            'consulta' => 'Consultar',
            'documento' => $identificationNumber,
            'dia' => 'N.A',
            'mes' => 'N.A',
            'token' => md5($identificationNumber) . getenv('SECURE_TOKEN'),
        ]);

        $response = trim((string) $curl->makeRequest());
        $decoded = decodificarRespuestaJsonSiga($response);
        if (is_array($decoded)) {
            $successfulQueries++;
            $candidate = isset($decoded['data']) && is_array($decoded['data'])
                ? $decoded['data']
                : $decoded;

            if (!isset($candidate['error'])) {
                $graduateData = normalizarDatosGraduadoSiga($candidate);
            }
        } else {
            $errors[] = 'SIGA no devolvio los datos del egresado';
        }
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }

    $hasGraduateData = count(array_filter(
        $graduateData,
        static fn ($value): bool => trim((string) $value) !== ''
    )) > 0;

    return (object) [
        'available' => $successfulQueries > 0,
        'eligible' => $eligibleByStatus || $hasGraduateData,
        'graduated' => $graduated,
        'data' => $graduateData,
        'error' => implode(' | ', array_filter($errors)),
    ];
}

function decodificarRespuestaJsonSiga(string $response): ?array
{
    $start = strpos($response, '{');
    $end = strrpos($response, '}');
    if ($start === false || $end === false || $end < $start) {
        return null;
    }

    $decoded = json_decode(substr($response, $start, $end - $start + 1), true);
    return json_last_error() === JSON_ERROR_NONE && is_array($decoded)
        ? $decoded
        : null;
}

function normalizarDatosGraduadoSiga(array $data): array
{
    return array_filter([
        'nombres' => $data['Nombres'] ?? $data['nombres'] ?? '',
        'apellidos' => $data['Apellidos'] ?? $data['apellidos'] ?? '',
        'email' => $data['Correo'] ?? $data['correo'] ?? '',
        'numero_celular' => $data['Telefono de contacto'] ?? $data['telefono'] ?? '',
        'numero_alterno' => $data['Telefono alterno de contacto'] ?? $data['tel_alterno'] ?? '',
        'direccion' => $data['Direccion de correspondencia'] ?? $data['direccion'] ?? '',
        'ciudad' => $data['Ciudad residencia'] ?? $data['ciudad'] ?? '',
    ], static fn ($value): bool => trim((string) $value) !== '');
}

/**
 * SIGA rechaza la actualizacion completa (correo, telefono, tel_alterno,
 * direccion) cuando la ciudad enviada no esta en su catalogo. Por eso solo
 * se envia 'ciudad' cuando coincide con este listado; si no coincide se
 * omite el campo en vez de bloquear la actualizacion de los demas datos.
 */
function normalizarCiudadParaSiga(string $city): ?string
{
    $city = strtoupper(trim($city));
    $city = removeAccentsSiga($city);
    $city = preg_split('/[-,]/', $city)[0];
    $city = preg_replace('/\s+D\.?C\.?/', '', $city);
    $city = trim($city);

    $allowed = [
        'BOGOTA', 'MEDELLIN', 'CALI', 'BARRANQUILLA', 'CARTAGENA', 'SOACHA',
        'CUCUTA', 'SOLEDAD', 'BUCARAMANGA', 'BELLO', 'VALLEDUPAR', 'VILLAVICENCIO',
        'SANTA MARTA', 'IBAGUE', 'MONTERIA', 'PEREIRA', 'MANIZALES', 'PASTO',
        'NEIVA', 'PALMIRA', 'POPAYAN', 'BUENAVENTURA', 'ARMENIA', 'FLORIDABLANCA',
        'SINCELEJO', 'ITAGUI', 'TUMACO', 'ENVIGADO', 'DOSQUEBRADAS', 'TULUA',
        'BARRANCABERMEJA', 'RIOHACHA', 'URIBIA', 'MAICAO', 'PIEDECUESTA', 'TUNJA',
        'YOPAL', 'FLORENCIA', 'GIRON', 'FACATATIVA', 'JAMUNDI', 'FUSAGASUGA',
        'MOSQUERA', 'CHIA', 'ZIPAQUIRA', 'RIONEGRO', 'MALAMBO', 'MAGANGUE',
        'MADRID', 'CARTAGO', 'TURBO', 'QUIBDO', 'APARTADO', 'SOGAMOSO', 'OCANA',
        'PITALITO', 'BUGA', 'DUITAMA', 'CIENAGA', 'AGUACHICA', 'GIRARDOT',
        'LORICA', 'TURBACO', 'IPIALES', 'FUNZA', 'SANTANDER DE QUILICHAO',
        'VILLA DEL ROSARIO', 'SAHAGUN', 'YUMBO', 'CERETE', 'SABANALARGA', 'CAJICA',
        'ARAUCA', 'CAUCASIA', 'LOS PATIOS', 'MANAURE', 'TIERRALTA', 'CANDELARIA',
        'ACACIAS', 'SABANETA', 'MONTELIBANO', 'CALDAS', 'COPACABANA', 'CUMARIBO',
        'SANTA ROSA DE CABAL', 'LA ESTRELLA', 'CALARCA', 'ZONA BANANERA', 'ARJONA',
        'LA DORADA', 'GARZON', 'EL CARMEN DE BOLIVAR', 'COROZAL', 'FUNDACION',
        'GRANADA', 'EL BANCO', 'LA CEJA', 'ESPINAL', 'MARINILLA', 'PUERTO ASIS',
        'BARANOA', 'GALAPA', 'VILLAMARIA', 'AGUSTIN CODAZZI', 'PLATO', 'PLANETA RICA',
        'SARAVENA', 'EL CARMEN DE VIBORAL', 'LA PLATA', 'CHIGORODO', 'SAN MARCOS',
        'CIENAGA DE ORO', 'MOCOA', 'SAN GIL', 'GUARNE', 'TIBU', 'SAN JOSE DEL GUAVIARE',
        'SAN ANDRES', 'FLORIDA', 'CHIQUINQUIRA', 'ARAUQUITA', 'EL CERRITO',
        'GIRARDOTA', 'BARBOSA', 'BARBACOAS', 'EL BAGRE', 'TUCHIN', 'PUERTO COLOMBIA',
    ];

    return in_array($city, $allowed, true) ? $city : null;
}

function removeAccentsSiga(string $text): string
{
    return strtr($text, [
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
        'á' => 'A', 'é' => 'E', 'í' => 'I', 'ó' => 'O', 'ú' => 'U',
        'Ñ' => 'N', 'ñ' => 'N',
    ]);
}
