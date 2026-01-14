<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/controllers/autoloader.php';

use Dotenv\Dotenv;
use Google\Client;
use Google\Service\Sheets;
use Ospina\EasySQL\EasySQL;

// ==========================
// ENV
// ==========================
$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../');
$dotenv->load();

$spreadsheetId = getenv('GOOGLE_SHEET_ID');
$range = getenv('SHEET_RANGE'); // Ej: Respuestas!A:ZZ
$env = getenv('ENVIRONMENT') ?: 'local';

if (!$spreadsheetId || !$range) {
    die("❌ Falta GOOGLE_SHEET_ID o SHEET_RANGE\n");
}

// ==========================
// Google Sheets
// ==========================
$client = new Client();
$client->setApplicationName('Encuesta Graduados Import');
$client->setScopes([Sheets::SPREADSHEETS_READONLY]);
$client->setAuthConfig(__DIR__ . '/../credentials.json');

$service = new Sheets($client);
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

if (!$values || count($values) < 2) {
    die("❌ No hay datos\n");
}

// ==========================
// Header map
// ==========================
$header = array_map(fn($h) => trim($h), $values[0]);
$rows = array_slice($values, 1);

function col(array $header, string $name): int {
    foreach ($header as $i => $h) {
        if (mb_strtolower($h) === mb_strtolower($name)) {
            return $i;
        }
    }
    die("❌ No se encontró columna obligatoria: {$name}\n");
}

// Índices exactos
$ixTime    = col($header, 'Marca temporal');
$ixEmail   = col($header, 'Dirección de correo electrónico');
$ixName    = col($header, 'Nombres');
$ixLast    = col($header, 'Apellidos');
$ixId      = col($header, 'Número de identificación');
$ixPhone   = col($header, 'Teléfono de contacto');
$ixAlt     = col($header, 'Teléfono alterno de contacto');
$ixYear    = col($header, 'Año de graduación');
$ixAddress = col($header, 'Dirección de correspondencia');
$ixCountry = col($header, 'País');
$ixCity    = col($header, 'Ciudad');

// ==========================
// DB
// ==========================
$db = new EasySQL('encuesta_graduados', $env);

$imported = 0;
$skipped = 0;

foreach ($rows as $row) {
    $row = array_pad($row, count($header), '');

    $email = trim($row[$ixEmail]);
    $id    = trim($row[$ixId]);

    if ($email === '' || $id === '') {
        $skipped++;
        continue;
    }

    $createdAt = date('Y-m-d H:i:s', strtotime($row[$ixTime])) ?: date('Y-m-d H:i:s');

    $answers = json_encode([
        'anio_graduacion' => trim($row[$ixYear]),
        'origen' => 'google_forms_2022'
    ], JSON_UNESCAPED_UNICODE);

    try {
        $db->table('form_answers')->insert([
            'email' => $email,
            'identification_number' => $id,
            'name' => trim($row[$ixName]),
            'last_name' => trim($row[$ixLast]),
            'mobile_phone' => trim($row[$ixPhone]),
            'alternative_mobile_phone' => trim($row[$ixAlt]),
            'address' => trim($row[$ixAddress]),
            'country' => trim($row[$ixCountry]) ?: 'Colombia',
            'city' => trim($row[$ixCity]),
            'answers' => $answers,
            'is_graduated' => 1,
            'is_migrated' => 0,
            'is_denied' => 0,
            'is_deleted' => 0,
            'created_at' => $createdAt,
        ]);
        $imported++;
    } catch (Exception $e) {
        $skipped++;
        echo "⚠️ {$id}: {$e->getMessage()}\n";
    }
}

echo "✅ Importación finalizada\n";
echo "Importados: {$imported}\n";
echo "Omitidos: {$skipped}\n";
