<?php

require __DIR__ . '/app/controllers/autoloader.php';

use eftec\bladeone\BladeOne;
use Ospina\EasySQL\EasySQL;

verifyIsAuthenticated();

$page = max((int) ($_GET['page'] ?? 1), 1);
$limit = 50;
$search = trim($_GET['search'] ?? '');
$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

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

$total = 0;
$encuentroAnswers = [];
$questionColumns = [];
$graduados = [];
$acompanantes = [];

try {
    $graduados = $db->makeQuery("\n        SELECT fa.id, fa.answers, fa.name, fa.last_name,
               fa.identification_number, fa.created_at, fa.updated_at
        FROM form_answers fa
        WHERE $where
        ORDER BY created_at DESC
    ")->fetch_all(MYSQLI_ASSOC);

    foreach ($graduados as $key => $answer) {
        $answers = json_decode($answer['answers'] ?? '', true);
        $answers = is_array($answers) ? $answers : [];
        $graduados[$key]['survey_answers'] = $answers;
        $graduados[$key]['source_type'] = 'Graduado';
        $graduados[$key]['source_table'] = 'form_answers';
        $graduados[$key]['row_key'] = 'graduado_' . $answer['id'];
    }

    $acompanantesWhere = '1 = 1';
    if ($search !== '') {
        $searchSafe = addslashes($search);
        $acompanantesWhere = "(
            cedula LIKE '%$searchSafe%'
            OR nombres LIKE '%$searchSafe%'
            OR apellidos LIKE '%$searchSafe%'
        )";
    }

    try {
        $acompanantes = $db->makeQuery("\n            SELECT id, cedula, nombres, apellidos, created_at, updated_at
            FROM registroacom_2026
            WHERE $acompanantesWhere
            ORDER BY created_at DESC
        ")->fetch_all(MYSQLI_ASSOC);
    } catch (Throwable $e) {
        encuentroResultadosLog('La tabla registroacom_2026 aún no está disponible: ' . $e->getMessage(), 'WARNING');
    }

    foreach ($acompanantes as $key => $answer) {
        $acompanantes[$key]['survey_answers'] = [
            'id' => $answer['cedula'],
            'nombres' => $answer['nombres'],
            'apellidos' => $answer['apellidos'],
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
        'autorizacion_datos',
        '_survey_type',
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
            'label' => $key === 'id' ? 'Cédula' : $key,
        ],
        array_keys($questionKeys)
    );

    $primaryKeys = ['id', 'nombres', 'apellidos', 'email', 'programa'];
    $primaryColumns = array_map(
        static fn (string $key): array => [
            'key' => $key,
            'label' => $key === 'id' ? 'Cédula' : ucfirst($key),
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
