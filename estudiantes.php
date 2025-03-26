<?php

require __DIR__ . '/vendor/autoload.php'; // Ensure Google API client is included

// Enable CORS for local development (remove in production)
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


const GOOGLE_FORM_URL = 'https://docs.google.com/forms/d/e/1FAIpQLSfn-6vTXj7mhbSvoKr_81AhA5O3MxFLyv3aZkz5WhI2Da10RA/viewform';
const GOOGLE_SHEET_ID = '1pncYPwxuqoMKx1wneyA3QtphynhFATgxbF8whhS1Jkc';
const SHEET_RANGE = 'Respuestas!A:E'; // Adjust based on your sheet structure


$documentNumber = $_GET['identification_number'] ?? "";

if (!$documentNumber) {
    http_response_code(400);
    echo json_encode(["error" => "Número de identificación requerido"]);
    exit;
}

// Call the external API
$apiUrl = "https://academia.unibague.edu.co/atlante/consulta_estudiante.php?code_user={$documentNumber}&type=I";
$response = file_get_contents($apiUrl);

// Check if the API response is empty or invalid
if (!$response) {
    http_response_code(404);
    echo json_encode(["error" => "Invalid or empty response"]);
    exit;
}

// Decode JSON response
$data = json_decode($response, true);

if (!is_array($data) || empty($data)) {
    http_response_code(404);
    echo json_encode(["error" => "No hay información relacionada a la cédula ingresada"]);
    exit;
}

// Filter records with status "Activo"
$activePrograms = array_filter($data, function ($program) {
    return isset($program["status"]) && $program["status"] === "Activo";
});

// If no active programs found, return not found
if (empty($activePrograms)) {
    http_response_code(404);
    echo json_encode(["error" => "Usuario no tiene programas activos"]);
    exit;
}


//At this point the user has active programs, but we have to validate if he hasn't answered the form
$answeredPrograms = getAnsweredPrograms($documentNumber);

$nonAnsweredPrograms = [];

foreach ($activePrograms as $program) {
    if (!in_array($program["program"], $answeredPrograms)){
        $nonAnsweredPrograms [] = $program["program"];
    }
}

if (!empty($nonAnsweredPrograms)) {
    // Redirect to Google Form with prefilled documentNumber and program
    $formData = ["entry.68722145" => $documentNumber,
        'entry.194301267' => $nonAnsweredPrograms[0]];
    $finalUrl = GOOGLE_FORM_URL . '?' . http_build_query($formData, '', '&', PHP_QUERY_RFC3986);

    echo json_encode(["redirect_url" => $finalUrl], JSON_UNESCAPED_UNICODE);
}

else{
   echo json_encode(["error" => "Usuario ya contestó la encuesta para todos sus programas activos"]);
}


function getAnsweredPrograms($documentNumber) {
    $client = new Google_Client();
    $client->setAuthConfig('credentials.json');
    $client->setScopes([Google_Service_Sheets::SPREADSHEETS_READONLY]);

    $service = new Google_Service_Sheets($client);
    $response = $service->spreadsheets_values->get(GOOGLE_SHEET_ID, SHEET_RANGE);

    $values = $response->getValues();

    $answeredPrograms = [];

    foreach ($values as $row) {
        if ($row[1] === $documentNumber) { // Assuming second column contains ID
            $answeredPrograms[] = $row[2]; // Assuming third column contains program name
        }
    }
    return $answeredPrograms;
}
