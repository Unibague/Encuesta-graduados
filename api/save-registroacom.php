<?php

require __DIR__ . '/../app/controllers/autoloader.php';

use Ospina\EasySQL\EasySQL;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$consentimiento = trim(strip_tags((string) ($input['consentimiento'] ?? '')));
$nombres = trim(strip_tags((string) ($input['nombres'] ?? '')));
$apellidos = trim(strip_tags((string) ($input['apellidos'] ?? '')));
$cedula = trim(preg_replace('/[^0-9]/', '', (string) ($input['cedula'] ?? '')));

if ($consentimiento !== 'Sí' || $nombres === '' || $apellidos === '' || $cedula === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Completa el consentimiento y todos los datos personales.']);
    exit;
}

$anioActivo = obtenerAnioEncuentroActivo();

$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
$db->makeQuery("
    CREATE TABLE IF NOT EXISTS registroacom_2026 (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        cedula VARCHAR(30) NOT NULL,
        nombres VARCHAR(100) NOT NULL,
        apellidos VARCHAR(100) NOT NULL,
        consentimiento ENUM('Sí') NOT NULL DEFAULT 'Sí',
        encuentro_anio SMALLINT UNSIGNED NOT NULL DEFAULT 2026,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_registroacom_cedula (cedula)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

// Compatibilidad con instalaciones donde la tabla se creó antes de agregar
// el año del encuentro.
$yearColumn = $db->makeQuery("SHOW COLUMNS FROM registroacom_2026 LIKE 'encuentro_anio'")->fetch_assoc();
if (!$yearColumn) {
    $db->makeQuery("ALTER TABLE registroacom_2026
        ADD COLUMN encuentro_anio SMALLINT UNSIGNED NOT NULL DEFAULT 2026 AFTER consentimiento");
}

$cedulaSafe = addslashes($cedula);
$nombresSafe = addslashes($nombres);
$apellidosSafe = addslashes($apellidos);

$existing = $db->makeQuery("SELECT id FROM registroacom_2026 WHERE cedula = '$cedulaSafe' LIMIT 1")->fetch_assoc();

if ($existing) {
    $db->makeQuery("UPDATE registroacom_2026 SET
        nombres = '$nombresSafe',
        apellidos = '$apellidosSafe',
        consentimiento = 'Sí',
        encuentro_anio = $anioActivo,
        updated_at = NOW()
        WHERE id = " . (int) $existing['id']);
} else {
    $db->makeQuery("INSERT INTO registroacom_2026
        (cedula, nombres, apellidos, consentimiento, encuentro_anio, created_at, updated_at)
        VALUES ('$cedulaSafe', '$nombresSafe', '$apellidosSafe', 'Sí', $anioActivo, NOW(), NOW())");
}

/* =========================
 * GOOGLE SHEETS (hoja "Acompañantes")
 * ========================= */
$sheetsError = null;

try {
    registrarAcompananteEnSheets($nombres, $apellidos, $cedula);
} catch (Throwable $e) {
    $sheetsError = $e->getMessage();
    error_log('[save-registroacom] Error Google Sheets: ' . $e->getMessage());
}

/* El registro ya quedó en la base de datos, así que un reintento no duplica
   nada: solo se informa error para que la persona vuelva a intentar y se
   repare la fila en la hoja de cálculo. */
if ($sheetsError !== null) {
    http_response_code(502);
    echo json_encode([
        'success' => false,
        'message' => 'Guardamos tu registro, pero no pudimos actualizar la hoja de acompañantes. Por favor, vuelve a presionar "Terminar registro".',
    ]);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Registro guardado correctamente']);
exit;

/* =========================
 * FUNCTIONS
 * ========================= */

/**
 * Registra (o actualiza) al acompañante en la hoja "Acompañantes" del
 * Google Sheet del Encuentro, igual que se hace con los graduados en la
 * hoja principal. La cédula es la clave para evitar filas duplicadas.
 */
function registrarAcompananteEnSheets(string $nombres, string $apellidos, string $cedula): void
{
    $spreadsheetId = ENCUENTRO_SHEET_ID;
    $targetTitle   = 'Acompañantes';

    $client = new Google_Client();
    $client->setAuthConfig(googleCredentialsPath());
    $client->addScope(Google_Service_Sheets::SPREADSHEETS);

    $service = new Google_Service_Sheets($client);

    $spreadsheet = $service->spreadsheets->get($spreadsheetId);
    $sheetName   = null;
    foreach ($spreadsheet->getSheets() as $sheet) {
        $titulo = trim($sheet->getProperties()->getTitle());
        if (mb_strtolower($titulo, 'UTF-8') === mb_strtolower($targetTitle, 'UTF-8')) {
            $sheetName = $sheet->getProperties()->getTitle();
            break;
        }
    }

    if (!$sheetName) {
        throw new RuntimeException("No se encontró la hoja '{$targetTitle}'");
    }

    $nombreCompleto = trim($nombres . ' ' . $apellidos);
    $params         = ['valueInputOption' => 'USER_ENTERED'];

    // Buscar si esta persona ya tiene fila, por cédula, para no duplicarla al reenviar el registro.
    $existentes = $service->spreadsheets_values->get($spreadsheetId, "{$sheetName}!A4:B");
    $filas      = $existentes->getValues() ?? [];

    $numeroFila = null;
    foreach ($filas as $i => $fila) {
        $cedulaFila = trim($fila[1] ?? '');
        if ($cedulaFila !== '' && $cedulaFila === $cedula) {
            $numeroFila = $i + 4; // A4 es la primera fila de datos
            break;
        }
    }

    // Columna A: Nombre | Columna B: Cédula
    $body = new Google_Service_Sheets_ValueRange(['values' => [[$nombreCompleto, $cedula]]]);

    if ($numeroFila !== null) {
        $service->spreadsheets_values->update($spreadsheetId, "{$sheetName}!A{$numeroFila}:B{$numeroFila}", $body, $params);
        return;
    }

    $service->spreadsheets_values->append($spreadsheetId, "{$sheetName}!A:B", $body, $params);
}
