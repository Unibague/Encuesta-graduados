<?php

require __DIR__ . '/app/controllers/autoloader.php';

use eftec\bladeone\BladeOne;
use Ospina\EasySQL\EasySQL;

verifyIsAuthenticated();

$anioActivo = obtenerAnioEncuentroActivo();

$page = max((int) ($_GET['page'] ?? 1), 1);
$limit = 50;
$search = trim($_GET['search'] ?? '');
$anio = trim($_GET['anio'] ?? (string) $anioActivo);
if ($anio !== '' && !preg_match('/^\d{4}$/', $anio)) {
    $anio = (string) $anioActivo;
}
$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
$encuentroAnioExpression = "COALESCE(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(fa.answers, '$._encuentro_anio')), ''), '{$anioActivo}')";

$where = "fa.is_deleted = 0
    AND JSON_UNQUOTE(JSON_EXTRACT(fa.answers, '$._survey_type')) = 'registrograduados'";
if ($search !== '') {
    $searchSafe = addslashes($search);
    $where .= "
        AND (
            fa.identification_number LIKE '%$searchSafe%'
            OR fa.name LIKE '%$searchSafe%'
            OR fa.last_name LIKE '%$searchSafe%'
            OR fa.answers LIKE '%$searchSafe%'
        )
    ";
}
if ($anio !== '') {
    $anioSafe = addslashes($anio);
    $where .= "
        AND $encuentroAnioExpression = '$anioSafe'
    ";
}

$total = 0;
$encuentroAnswers = [];
$questionColumns = [];
$graduados = [];
$acompanantes = [];
$anios = [];

try {
    $anios = $db->makeQuery("\n        SELECT DISTINCT $encuentroAnioExpression AS anio
        FROM form_answers fa
        WHERE fa.is_deleted = 0
            AND JSON_UNQUOTE(JSON_EXTRACT(fa.answers, '$._survey_type')) = 'registrograduados'
        ORDER BY anio DESC
    ")->fetch_all(MYSQLI_ASSOC);
    $anios = array_values(array_filter(array_map(
        static fn (array $row): string => trim((string) ($row['anio'] ?? '')),
        $anios
    ), static fn (string $value): bool => $value !== ''));
    if (!in_array((string) $anioActivo, $anios, true)) {
        $anios[] = (string) $anioActivo;
    }
    rsort($anios, SORT_STRING);

    $graduados = $db->makeQuery("\n        SELECT fa.id, fa.answers, fa.name, fa.last_name,
               fa.identification_number, fa.created_at, fa.updated_at
        FROM form_answers fa
        WHERE $where
        ORDER BY created_at DESC
    ")->fetch_all(MYSQLI_ASSOC);

    foreach ($graduados as $key => $answer) {
        $answers = json_decode($answer['answers'] ?? '', true);
        $answers = is_array($answers) ? $answers : [];
        $answers['encuentro_anio'] = trim((string) ($answers['_encuentro_anio'] ?? '')) ?: (string) $anioActivo;
        $graduados[$key]['survey_answers'] = $answers;
        $graduados[$key]['source_type'] = 'Graduado';
        $graduados[$key]['source_table'] = 'form_answers';
        $graduados[$key]['row_key'] = 'graduado_' . $answer['id'];
    }

    // La tabla de acompañantes ahora guarda el año del encuentro al que
    // pertenece cada registro, así que se filtra igual que los graduados.
    $acompanantesWhere = '1 = 1';
    if ($search !== '') {
        $searchSafe = addslashes($search);
        $acompanantesWhere = "(
            cedula LIKE '%$searchSafe%'
            OR nombres LIKE '%$searchSafe%'
            OR apellidos LIKE '%$searchSafe%'
        )";
    }
    if ($anio !== '') {
        $anioSafe = addslashes($anio);
        $acompanantesWhere .= " AND encuentro_anio = '$anioSafe'";
    }

    try {
        $acompanantes = $db->makeQuery("\n            SELECT id, cedula, nombres, apellidos, consentimiento, encuentro_anio, created_at, updated_at
            FROM registroacom_2026
            WHERE $acompanantesWhere
            ORDER BY created_at DESC
        ")->fetch_all(MYSQLI_ASSOC);
    } catch (Throwable $e) {
        encuentroResultadosLog('La tabla registroacom_2026 aún no está disponible: ' . $e->getMessage(), 'WARNING');
    }

    foreach ($acompanantes as $key => $answer) {
        $acompanantes[$key]['survey_answers'] = [
            'encuentro_anio' => (string) ($answer['encuentro_anio'] ?? $anioActivo),
            'id' => $answer['cedula'],
            'nombres' => $answer['nombres'],
            'apellidos' => $answer['apellidos'],
            'consentimiento' => $answer['consentimiento'] ?? '',
        ];
        $acompanantes[$key]['source_type'] = 'Acompañante';
        $acompanantes[$key]['source_table'] = 'registroacom_2026';
        $acompanantes[$key]['row_key'] = 'acompanante_' . $answer['id'];
    }

    $encuentroAnswers = array_merge($graduados, $acompanantes);
    usort($encuentroAnswers, static fn (array $first, array $second): int =>
        strcmp((string) $second['created_at'], (string) $first['created_at'])
    );

    $excludedKeys = [
        '_survey_type',
        '_encuentro_anio',
        '_survey_question_types',
        '_survey_completed_at',
    ];
    $questionKeys = [];

    foreach ($encuentroAnswers as $key => $answer) {
        $answers = $answer['survey_answers'];

        foreach ($answers as $answerKey => $_value) {
            if (!in_array($answerKey, $excludedKeys, true)) {
                $questionKeys[(string) $answerKey] = true;
            }
        }
    }

    $questionColumns = array_map(
        static fn (string $key): array => [
            'key' => $key,
            'label' => humanizeQuestionLabel($key),
        ],
        array_keys($questionKeys)
    );
    $consentKeys = ['autorizacion_datos', 'consentimiento'];
    $questionColumns = array_merge(
        array_values(array_filter(
            $questionColumns,
            static fn (array $column): bool => !in_array($column['key'], $consentKeys, true)
        )),
        array_values(array_filter(
            $questionColumns,
            static fn (array $column): bool => in_array($column['key'], $consentKeys, true)
        ))
    );

    $primaryKeys = ['encuentro_anio', 'id', 'nombres', 'apellidos', 'email', 'numero_celular'];
    $primaryColumns = array_map(
        static fn (string $key): array => [
            'key' => $key,
            'label' => match ($key) {
                'encuentro_anio' => 'Año encuentro',
                'id' => 'Cédula',
                'anio_graduacion' => 'Año graduación',
                'email' => 'Correo',
                'numero_celular' => 'Número celular',
                default => ucfirst($key),
            },
        ],
        $primaryKeys
    );
    $extraColumns = [];

    foreach ($questionColumns as $column) {
        if (!in_array($column['key'], $primaryKeys, true)) {
            $extraColumns[] = $column;
        }
    }

    foreach ($encuentroAnswers as $key => $answer) {
        $displayAnswers = [];

        foreach ($questionColumns as $column) {
            $value = $answer['survey_answers'][$column['key']] ?? '';
            $displayAnswers[$column['key']] = stringifyRegistroAnswer($value);
        }

        $encuentroAnswers[$key]['display_answers'] = $displayAnswers;
    }

    $total = count($encuentroAnswers);

    if (($_GET['export'] ?? '') === 'excel') {
        exportEncuentroCsv($encuentroAnswers, $primaryColumns, $extraColumns, $anio);
    }
} catch (Throwable $e) {
    encuentroResultadosLog('No fue posible consultar respuestas de graduados o acompañantes: ' . $e->getMessage(), 'WARNING');
}

$totalPages = max((int) ceil($total / $limit), 1);
$offset = ($page - 1) * $limit;
$encuentroAnswers = array_slice($encuentroAnswers, $offset, $limit);
$message = $_SESSION['message'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['message'], $_SESSION['error']);

$blade = new BladeOne(
    __DIR__ . '/views',
    __DIR__ . '/cache',
    BladeOne::MODE_AUTO
);

echo $blade->run('encuentro_resultados', [
    'encuentroAnswers' => $encuentroAnswers,
    'page' => $page,
    'total' => $total,
    'totalPages' => $totalPages,
    'search' => $search,
    'anio' => $anio,
    'anios' => $anios,
    'anioActivo' => $anioActivo,
    'message' => $message,
    'error' => $error,
    'questionColumns' => $questionColumns,
    'primaryColumns' => $primaryColumns,
    'extraColumns' => $extraColumns,
]);

function encuentroResultadosLog(string $message, string $level = 'INFO'): void
{
    error_log("[$level] $message");
}

function stringifyRegistroAnswer($value): string
{
    if (is_array($value)) {
        return implode(', ', array_map(
            static fn ($item): string => is_scalar($item)
                ? trim((string) $item)
                : json_encode($item, JSON_UNESCAPED_UNICODE),
            $value
        ));
    }

    return trim((string) ($value ?? ''));
}

function humanizeQuestionLabel(string $key): string
{
    $knownLabels = [
        'id' => 'Cédula',
        'encuentro_anio' => 'Año encuentro',
        'anio_graduacion' => 'Año graduación',
        'email' => 'Correo electrónico',
        'autorizacion_datos' => 'Autorización de datos',
        'consentimiento' => 'Consentimiento',
    ];

    if (isset($knownLabels[$key])) {
        return $knownLabels[$key];
    }

    $label = trim(preg_replace('/\s+/', ' ', str_replace('_', ' ', $key)));
    $label = mb_strtolower($label, 'UTF-8');

    if ($label === '') {
        return '';
    }

    return mb_strtoupper(mb_substr($label, 0, 1, 'UTF-8'), 'UTF-8')
        . mb_substr($label, 1, null, 'UTF-8');
}

/**
 * Exporta todos los resultados que coinciden con los filtros actuales en un
 * CSV UTF-8 compatible con Excel. También neutraliza valores que Excel podría
 * interpretar como fórmulas.
 */
function exportEncuentroCsv(array $answers, array $primaryColumns, array $extraColumns, string $anio): never
{
    $suffix = $anio !== '' ? $anio : 'todos-los-anios';
    $filename = "encuentro-graduados-{$suffix}.csv";

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-store, no-cache, must-revalidate');

    $output = fopen('php://output', 'wb');
    fwrite($output, "\xEF\xBB\xBF");

    $columns = array_merge($primaryColumns, $extraColumns);
    $headers = array_map(static fn (array $column): string => $column['label'], $columns);
    $headers[] = 'Tipo';
    $headers[] = 'Fecha de registro';
    fputcsv($output, $headers, ';', '"', '\\');

    foreach ($answers as $answer) {
        $row = [];
        foreach ($columns as $column) {
            $row[] = excelSafeValue((string) ($answer['display_answers'][$column['key']] ?? ''));
        }
        $row[] = excelSafeValue((string) ($answer['source_type'] ?? ''));
        $row[] = excelSafeValue((string) ($answer['created_at'] ?? ''));
        fputcsv($output, $row, ';', '"', '\\');
    }

    fclose($output);
    exit;
}

function excelSafeValue(string $value): string
{
    $value = trim($value);
    return preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;
}
