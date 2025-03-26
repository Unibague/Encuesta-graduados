<?php
use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();

$googleFormUrl = $_ENV['GOOGLE_FORM_URL'];
define('GOOGLE_SHEET_ID', $_ENV['GOOGLE_SHEET_ID']);
define('SHEET_RANGE', $_ENV['SHEET_RANGE']);

// Google Client Setup
$client = new Google_Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);

$redirectUri = ($_ENV['APP_ENV'] === 'production')
    ? $_ENV['GOOGLE_REDIRECT_URI_PROD']
    : $_ENV['GOOGLE_REDIRECT_URI_DEV'];
$client->setRedirectUri($redirectUri);


$client->addScope("email");
$client->addScope("profile");

// Handle authorization response
if (!isset($_GET['code'])) {
    die("Error: No authorization code provided.");
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$client->setAccessToken($token);

$oauth = new Google_Service_Oauth2($client);
$userInfo = $oauth->userinfo->get();

$email = $userInfo->email;
$name = $userInfo->name;

// Extract username (assuming email format like 1005712934@unibague.edu.co)
$username = extractUsername($email);

// Fetch active programs
$activePrograms = checkActivePrograms($email);

// If no active programs, return HTML error page
if (empty($activePrograms)) {
    returnErrorPage("No tienes programas activos registrados.");
}

// Check answered programs
$answeredPrograms = getAnsweredPrograms($email);
// Find non-answered programs
$nonAnsweredPrograms = array_diff($activePrograms, $answeredPrograms);

// If all programs are answered, return HTML error page
if (empty($nonAnsweredPrograms)) {
    returnErrorPage("Ya has respondido la encuesta para todos tus programas activos.");
}

// Redirect to Google Form with prefilled documentNumber and program
$formData = [
    "entry.891611921" => reset($nonAnsweredPrograms) // Prefill first non-answered program
];
$finalUrl = $googleFormUrl . '?' . http_build_query($formData, '', '&', PHP_QUERY_RFC3986);

header("Location: $finalUrl");
exit;

// ---------------------- Helper Functions ---------------------- //

function extractUsername($email)
{
    return explode("@", $email)[0]; // Extract username from email
}

// Function to check active programs via API
function checkActivePrograms($email)
{
    $apiUrl = 'http://integra.unibague.edu.co/studentInfo?api_token=$2y$42$s/9xFMDieYOEvYD/gfPqFAeFzvWXt13feXyterJzQ9rZKrbLpBYUqo&code_user=&type=';

    $response = file_get_contents($apiUrl);
    if (!$response) {
        return []; // Return empty if API fails
    }

    $data = json_decode($response, true);
    if (!is_array($data)) {
        return [];
    }

    // Filter by matching email and active status
    $filteredPrograms = array_filter($data, function ($student) use ($email) {
        return isset($student["email"], $student["status"]) &&
            $student["email"] === $email &&
            $student["status"] === "Activo";
    });

    return array_column($filteredPrograms, "program");
}

function getAnsweredPrograms($username)
{
    $client = new Google_Client();
    $client->setAuthConfig('credentials.json');
    $client->setScopes([Google_Service_Sheets::SPREADSHEETS_READONLY]);

    $service = new Google_Service_Sheets($client);
    $response = $service->spreadsheets_values->get(GOOGLE_SHEET_ID, SHEET_RANGE);
    $values = $response->getValues();

    $answeredPrograms = [];

    foreach ($values as $row) {
        if ($row[1] === $username) { // Assuming second column contains ID
            $answeredPrograms[] = $row[2]; // Assuming third column contains program name
        }
    }
    return $answeredPrograms;
}

function returnErrorPage($message) {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Encuesta Unibagué</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background-color: white; color: white; }
            .container { max-width: 600px; margin: auto; padding: 20px; background-color: #0036b3; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
            h2 { color: white; }
            p { font-size: 18px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>$message</h2>
        </div>
    </body>
    </html>";
    exit;
}