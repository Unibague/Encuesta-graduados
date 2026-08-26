<?php

require __DIR__ . '/app/controllers/autoloader.php';

use Dotenv\Dotenv;
use eftec\bladeone\BladeOne;
use Ospina\EasySQL\EasySQL;

verifyIsAuthenticated();

$dotenv = Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

$search = trim($_GET['search'] ?? '');


$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));


$where = "is_deleted = 0";

if ($search !== '') {
    $s = addslashes($search);
    $where .= "
        AND (
            identification_number LIKE '%$s%'
            OR name               LIKE '%$s%'
            OR last_name          LIKE '%$s%'
            OR email              LIKE '%$s%'
            OR mobile_phone       LIKE '%$s%'
            OR city               LIKE '%$s%'
        )
    ";
}

$counts = $db->makeQuery("
    SELECT
        SUM(CASE WHEN is_graduated = 1 AND is_migrated = 1 THEN 1 ELSE 0 END) AS graduados_actualizados,
        SUM(CASE WHEN is_graduated = 1 AND (is_migrated = 0 OR is_migrated IS NULL) THEN 1 ELSE 0 END) AS graduados_no_actualizados,
        SUM(CASE WHEN is_graduated = 0 AND is_migrated = 1 THEN 1 ELSE 0 END) AS no_graduados_actualizados,
        SUM(CASE WHEN is_graduated = 0 AND (is_migrated = 0 OR is_migrated IS NULL) THEN 1 ELSE 0 END) AS no_graduados_no_actualizados
    FROM form_answers
    WHERE $where
")->fetch_assoc();

$graduadosActualizados      = (int) ($counts['graduados_actualizados'] ?? 0);
$graduadosNoActualizados    = (int) ($counts['graduados_no_actualizados'] ?? 0);
$noGraduadosActualizados    = (int) ($counts['no_graduados_actualizados'] ?? 0);
$noGraduadosNoActualizados  = (int) ($counts['no_graduados_no_actualizados'] ?? 0);

$total = $graduadosActualizados + $graduadosNoActualizados
       + $noGraduadosActualizados + $noGraduadosNoActualizados;

$surveyQuestionStats = [];
$surveyRows = $db->makeQuery("SELECT answers FROM form_answers WHERE $where")
    ->fetch_all(MYSQLI_ASSOC);

foreach ($surveyRows as $surveyRow) {
    $answers = json_decode($surveyRow['answers'] ?? '', true);

    if (!is_array($answers)) {
        continue;
    }

    foreach ($answers as $question => $value) {
        if (str_starts_with((string) $question, '_')) {
            continue;
        }

        $questionTypes = $answers['_survey_question_types'] ?? [];
        if (!in_array($questionTypes[$question] ?? '', ['radio', 'select'], true)) {
            continue;
        }

        $question = trim((string) $question);
        if ($question === '') {
            continue;
        }

        $values = flattenGraphicAnswers($value);
        foreach ($values as $answerValue) {
            if ($answerValue === '') {
                continue;
            }

            if (!isset($surveyQuestionStats[$question])) {
                $surveyQuestionStats[$question] = [];
            }

            $surveyQuestionStats[$question][$answerValue] =
                ($surveyQuestionStats[$question][$answerValue] ?? 0) + 1;
        }
    }
}

foreach ($surveyQuestionStats as $question => $values) {
    arsort($values);
    $surveyQuestionStats[$question] = $values;
}

$selectedQuestion = trim($_GET['question'] ?? '');
if (!isset($surveyQuestionStats[$selectedQuestion])) {
    $selectedQuestion = array_key_first($surveyQuestionStats) ?? '';
}

// =========================
// FLASH
// =========================
$message = $_SESSION['message'] ?? null;
$error   = $_SESSION['error']   ?? null;
unset($_SESSION['message'], $_SESSION['error']);

// =========================
// BLADE
// =========================
$blade = new BladeOne(__DIR__ . '/views', __DIR__ . '/cache', BladeOne::MODE_AUTO);

echo $blade->run('graphic', [
    'total'                      => $total,
    'search'                     => $search,
    'graduadosActualizados'      => $graduadosActualizados,
    'graduadosNoActualizados'    => $graduadosNoActualizados,
    'noGraduadosActualizados'    => $noGraduadosActualizados,
    'noGraduadosNoActualizados'  => $noGraduadosNoActualizados,
    'surveyQuestionStats'        => $surveyQuestionStats,
    'selectedQuestion'            => $selectedQuestion,
    'message'                    => $message,
    'error'                      => $error,
]);

function flattenGraphicAnswers($value): array
{
    if (!is_array($value)) {
        return [trim((string) ($value ?? ''))];
    }

    $values = [];
    foreach ($value as $key => $item) {
        if (is_array($item)) {
            $values = array_merge($values, flattenGraphicAnswers($item));
            continue;
        }

        $item = trim((string) $item);
        if ($item !== '') {
            $values[] = is_string($key) ? trim($key) . ': ' . $item : $item;
        }
    }

    return $values;
}