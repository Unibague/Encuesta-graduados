<?php

require __DIR__ . '/app/controllers/autoloader.php';

use Dotenv\Dotenv;
use eftec\bladeone\BladeOne;
use Ospina\EasySQL\EasySQL;

verifyIsAuthenticated();

// Esta vista carga y decodifica el JSON completo de form_answers en memoria
// para poder filtrar por preguntas dinámicas; con el volumen actual de
// registros eso supera el límite por defecto de PHP.
ini_set('memory_limit', '1024M');

$dotenv = Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

$page  = max((int) ($_GET['page'] ?? 1), 1);
$limit = 20;

$search = trim($_GET['search'] ?? '');
$filters = isset($_GET['filter']) && is_array($_GET['filter'])
    ? $_GET['filter']
    : [];

$db = new EasySQL(
    'encuesta_graduados',
    getenv('ENVIRONMENT')
);

$where = "fa.is_deleted = 0
    AND COALESCE(
        JSON_UNQUOTE(JSON_EXTRACT(fa.answers, '$._survey_type')),
        ''
    ) <> 'registrograduados'";

if ($search !== '') {
    $s = addslashes($search);

    $where .= "
        AND (
            fa.identification_number LIKE '%$s%'
            OR fa.name               LIKE '%$s%'
            OR fa.last_name          LIKE '%$s%'
            OR fa.email              LIKE '%$s%'
            OR fa.mobile_phone       LIKE '%$s%'
            OR fa.city               LIKE '%$s%'
        )
    ";
}

$allRows = $db->makeQuery("
    SELECT
        fa.*,
        migrator.username AS migrated_by_name,
        denier.username   AS denied_by_name,
        deleter.username  AS deleted_by_name
    FROM form_answers fa
    LEFT JOIN users migrator
        ON migrator.id = fa.migrated_by
    LEFT JOIN users denier
        ON denier.id = fa.denied_by
    LEFT JOIN users deleter
        ON deleter.id = fa.deleted_by
    WHERE $where
    ORDER BY fa.updated_at DESC
")->fetch_all(MYSQLI_ASSOC);

function normalize_key(string $s): string
{
    $s = trim($s);
    $s = mb_strtolower($s, 'UTF-8');

    $converted = iconv(
        'UTF-8',
        'ASCII//TRANSLIT//IGNORE',
        $s
    );

    if ($converted !== false) {
        $s = $converted;
    }

    $s = preg_replace('/[^a-z0-9]+/', ' ', $s);
    $s = preg_replace('/\s+/', ' ', trim($s));

    return $s;
}

function normalize_key_compact(string $s): string
{
    return str_replace(' ', '', normalize_key($s));
}

function getMeaningfulWords(string $text): array
{
    $normalized = normalize_key($text);

    $words = preg_split(
        '/\s+/',
        $normalized,
        -1,
        PREG_SPLIT_NO_EMPTY
    );

    $stopWords = [
        'el', 'la', 'los', 'las', 'un', 'una', 'unos', 'unas',
        'de', 'del', 'al', 'en', 'por', 'para', 'que', 'y', 'o',
        'u', 'su', 'sus', 'se', 'le', 'lo', 'con', 'como', 'es',
        'son', 'ha', 'haya', 'si', 'caso', 'porfavor'
    ];

    $words = array_filter(
        $words,
        function ($word) use ($stopWords) {
            return strlen($word) >= 3
                && !in_array($word, $stopWords, true);
        }
    );

    return array_values(array_unique($words));
}

function questionSimilarity(
    string $questionA,
    string $questionB
): float {
    $a = normalize_key_compact($questionA);
    $b = normalize_key_compact($questionB);

    if ($a === $b) {
        return 1.0;
    }

    similar_text($a, $b, $percent);
    $textSimilarity = $percent / 100;

    $wordsA = getMeaningfulWords($questionA);
    $wordsB = getMeaningfulWords($questionB);

    if (empty($wordsA) || empty($wordsB)) {
        return $textSimilarity;
    }

    $matchedA = 0;
    foreach ($wordsA as $word) {
        if (
            in_array($word, $wordsB, true)
            || strpos($b, $word) !== false
        ) {
            $matchedA++;
        }
    }

    $matchedB = 0;
    foreach ($wordsB as $word) {
        if (
            in_array($word, $wordsA, true)
            || strpos($a, $word) !== false
        ) {
            $matchedB++;
        }
    }

    $wordSimilarity =
        ($matchedA + $matchedB)
        / (count($wordsA) + count($wordsB));

    return ($textSimilarity * 0.45)
        + ($wordSimilarity * 0.55);
}

function areDuplicateQuestions(
    string $questionA,
    string $questionB
): bool {
    return questionSimilarity($questionA, $questionB) >= 0.70;
}

function stringifyAnswer($value): string
{
    if (is_array($value)) {
        $items = [];

        foreach ($value as $item) {
            if (is_array($item)) {
                $item = json_encode(
                    $item,
                    JSON_UNESCAPED_UNICODE
                );
            }

            $item = trim((string) $item);

            if ($item !== '') {
                $items[] = $item;
            }
        }

        return implode(', ', $items);
    }

    return trim((string) ($value ?? ''));
}

function getDynamicColumnValue(array $row, array $column): string
{
    $answers = $row['extra_answers'] ?? [];
    $values = [];

    foreach ($column['keys'] as $keyVariant) {
        if (!array_key_exists($keyVariant, $answers)) {
            continue;
        }

        $candidate = stringifyAnswer($answers[$keyVariant]);

        if ($candidate !== '' && !in_array($candidate, $values, true)) {
            $values[] = $candidate;
        }
    }

    return implode(' | ', $values);
}

function getBaseColumnValue(array $row, string $field): string
{
    $value = $row[$field] ?? '';

    if ($field === 'updated_at' && $value === '') {
        $value = $row['modificated_at'] ?? '';
    }

    if ($field === 'is_graduated') {
        return ((int) $value === 1) ? '1' : '0';
    }

    if ($field === 'name' || $field === 'last_name') {
        return normalizePersonName((string) $value);
    }

    if ($field === 'mobile_phone' || $field === 'alternative_mobile_phone') {
        return normalizeColombianPhone((string) $value);
    }

    if ($field === 'updated_at' && $value !== '') {
        $timestamp = strtotime((string) $value);

        if ($timestamp !== false) {
            return date('d/m/Y H:i', $timestamp);
        }
    }

    return stringifyAnswer($value);
}

function normalizePersonName(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }

    $value = strtr($value, [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
        'ñ' => 'n', 'Ñ' => 'N',
    ]);
    $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    if ($converted !== false) {
        $value = $converted;
    }

    return mb_convert_case(
        mb_strtolower($value, 'UTF-8'),
        MB_CASE_TITLE,
        'UTF-8'
    );
}

function normalizeColombianPhone(string $value): string
{
    $digits = preg_replace('/\D+/', '', trim($value));
    if ($digits === null || $digits === '') {
        return '';
    }

    if (str_starts_with($digits, '0057')) {
        $digits = substr($digits, 2);
    }

    if (str_starts_with($digits, '57')) {
        return $digits;
    }

    return '57' . $digits;
}

function isSexOrGenderLabel(string $label): bool
{
    $normalized = normalize_key($label);

    return preg_match(
        '/(^| )(sexo|genero)( |$)/u',
        $normalized
    ) === 1;
}

function sexFilterLabel(string $value): string
{
    $normalized = normalize_key($value);

    if (str_starts_with($normalized, 'f')) {
        return 'Femenino';
    }

    if (str_starts_with($normalized, 'm')) {
        return 'Masculino';
    }

    return $value;
}

$alreadyShownKeys = [
    'id',
    'email',
    'Correo',
    'Número de identificación',
    'Numero de identificacion',
    'Nombres',
    'Apellidos',
    'Teléfono de contacto',
    'Teléfono alterno de contacto',
    'Dirección de correspondencia',
    'Ciudad',
    'País',
    'is_graduated',
    'updated_at',
    'modificated_at',
];

$alreadyShownKeysNormalized = array_map(
    'normalize_key_compact',
    $alreadyShownKeys
);


foreach ($allRows as $key => $row) {
    $decoded = json_decode($row['answers'] ?? '', true);

    $allRows[$key]['extra_answers'] =
        is_array($decoded) ? $decoded : [];

    unset($allRows[$key]['answers']);
}

$allFormKeys = [];

foreach ($allRows as $row) {
    foreach (($row['extra_answers'] ?? []) as $key => $_value) {
        if (
            in_array(
                normalize_key_compact((string) $key),
                $alreadyShownKeysNormalized,
                true
            )
        ) {
            continue;
        }

        $key = trim((string) $key);

        if ($key !== '') {
            $allFormKeys[$key] = true;
        }
    }
}

$allFormKeys = array_keys($allFormKeys);

$formGroups = [];

foreach ($allFormKeys as $currentKey) {
    $assigned = false;

    foreach ($formGroups as $groupIndex => $group) {
        foreach ($group['keys'] as $existingKey) {
            if (areDuplicateQuestions($currentKey, $existingKey)) {
                $formGroups[$groupIndex]['keys'][] = $currentKey;
                $assigned = true;
                break;
            }
        }

        if ($assigned) {
            break;
        }
    }

    if (!$assigned) {
        $formGroups[] = [
            'label' => $currentKey,
            'keys'  => [$currentKey],
        ];
    }
}

$formColumns = [];

foreach ($formGroups as $group) {
    $formColumns[] = [
        'norm' => normalize_key_compact($group['label']),
        'label' => $group['label'],
        'keys' => array_values(array_unique($group['keys'])),
        'isSex' => isSexOrGenderLabel($group['label']),
        'filterable' => false,
        'options' => [],
        'forced' => false,
    ];
}


$excludedFilterQuestions = [
    normalize_key_compact(
        'Antigüedad laboral en su empleo actual o en su último empleo si es pensionado'
    ),
    normalize_key_compact(
        'En caso que haya recibido distinciones y/o reconocimientos significativos por su desempeño en la disciplina, profesión, ocupación u oficio correspondiente, por favor descríbalas brevemente'
    ),
];

$forcedFilters = [
    'sexo' => [
        'Hombre'        => 'Hombre',
        'mujer'         => 'mujer',
        'intersexual'   => 'intersexual',
        'indeterminado' => 'indeterminado',
    ],
    'genero' => [
        'Femenino'    => 'Femenino',
        'Masculino'   => 'Masculino',
        'transgenero' => 'transgenero',
        'no binario'  => 'no binario',
    ],
    'programaacademico' => [
        'TECNOLOGIA EN CONTABILIDAD Y COSTOS' => 'TECNOLOGIA EN CONTABILIDAD Y COSTOS',
        'TECNOLOGIA EN ENTRENAMIENTO DEPORTIVO EN FUTBOL' => 'TECNOLOGIA EN ENTRENAMIENTO DEPORTIVO EN FUTBOL',
        'ESPECIALIZACION EN DERECHO ADMINISTRATIVO' => 'ESPECIALIZACION EN DERECHO ADMINISTRATIVO',
        'ESPECIALIZACION EN GESTION EMPRESARIAL' => 'ESPECIALIZACION EN GESTION EMPRESARIAL',
        'MAESTRIA EN ADMINISTRACION DE NEGOCIOS' => 'MAESTRIA EN ADMINISTRACION DE NEGOCIOS',
        'ESPECIALIZACION EN GESTION Y CONTROL DE CALIDAD' => 'ESPECIALIZACION EN GESTION Y CONTROL DE CALIDAD',
        'INGENIERIA DE SISTEMAS' => 'INGENIERIA DE SISTEMAS',
        'ESPECIALIZACION EN INTERVENCION PSICOSOCIAL' => 'ESPECIALIZACION EN INTERVENCION PSICOSOCIAL',
        'CONTADURIA PUBLICA' => 'CONTADURIA PUBLICA',
        'TECNOLOGIA EN SEGURIDAD E HIGIENE INDUSTRIAL' => 'TECNOLOGIA EN SEGURIDAD E HIGIENE INDUSTRIAL',
        'TECNOLOGIA EN GESTION DE TIC' => 'TECNOLOGIA EN GESTION DE TIC',
        'MAESTRIA EN DERECHO CON ENFASIS EN DERECHO PUBLICO Y DERECHO PRIVADO' => 'MAESTRIA EN DERECHO CON ENFASIS EN DERECHO PUBLICO Y DERECHO PRIVADO',
        'TECNOLOGIA EN INVESTIGACION CRIMINAL Y JUDICIAL' => 'TECNOLOGIA EN INVESTIGACION CRIMINAL Y JUDICIAL',
        'INGENIERÍA EN ANALÍTICA DE DATOS' => 'INGENIERÍA EN ANALÍTICA DE DATOS',
        'MAESTRIA EN GESTION TERRITORIAL, AUTONOMIA Y SOSTENIBILIDAD' => 'MAESTRIA EN GESTION TERRITORIAL, AUTONOMIA Y SOSTENIBILIDAD',
        'TECNOLOGIA INDUSTRIAL' => 'TECNOLOGIA INDUSTRIAL',
        'ESPECIALIZACION EN DERECHO PENAL' => 'ESPECIALIZACION EN DERECHO PENAL',
        'ARQUITECTURA' => 'ARQUITECTURA',
        'BIOLOGIA AMBIENTAL' => 'BIOLOGIA AMBIENTAL',
        'INGENIERIA INDUSTRIAL' => 'INGENIERIA INDUSTRIAL',
        'TECNOLOGIA MECANICA' => 'TECNOLOGIA MECANICA',
        'INGENIERIA CIVIL' => 'INGENIERIA CIVIL',
        'MAESTRIA EN ANALITICA DE DATOS PARA LA TOMA DE DECISIONES' => 'MAESTRIA EN ANALITICA DE DATOS PARA LA TOMA DE DECISIONES',
        'MAESTRIA EN INGENIERIA DE CONTROL' => 'MAESTRIA EN INGENIERIA DE CONTROL',
        'COMUNICACION SOCIAL Y PERIODISMO' => 'COMUNICACION SOCIAL Y PERIODISMO',
        'INGENIERIA MECANICA' => 'INGENIERIA MECANICA',
        'MAESTRIA EN GERENCIA DE LA CALIDAD' => 'MAESTRIA EN GERENCIA DE LA CALIDAD',
        'PSICOLOGIA' => 'PSICOLOGIA',
        'ESP. EN GESTION DE OPERACIONES Y LOGISTA' => 'ESP. EN GESTION DE OPERACIONES Y LOGISTA',
        'TECNOLOGIA EN REDES Y COMUNICACIONES' => 'TECNOLOGIA EN REDES Y COMUNICACIONES',
        'TECNOLOGIA EN SISTEMAS' => 'TECNOLOGIA EN SISTEMAS',
        'INGENIERIA ELECTRONICA' => 'INGENIERIA ELECTRONICA',
        'MAESTRIA EN GESTION INDUSTRIAL' => 'MAESTRIA EN GESTION INDUSTRIAL',
        'TECNOLOGIA EN MANTENIMIENTO INDUSTRIAL' => 'TECNOLOGIA EN MANTENIMIENTO INDUSTRIAL',
        'TECNOLOGIA EN MERCADEO Y VENTAS' => 'TECNOLOGIA EN MERCADEO Y VENTAS',
        'TECNOLOGIA EN LOGISTICA' => 'TECNOLOGIA EN LOGISTICA',
        'DISEÑO' => 'DISEÑO',
        'DERECHO' => 'DERECHO',
        'ADMINISTRACION DE EMPRESAS' => 'ADMINISTRACION DE EMPRESAS',
        'TECNOLOGIA EN ELECTRONICA' => 'TECNOLOGIA EN ELECTRONICA',
        'ADMINISTRACION DE NEGOCIOS INTERNACIONALES' => 'ADMINISTRACION DE NEGOCIOS INTERNACIONALES',
        'ECONOMIA' => 'ECONOMIA',
        'ESPECIALIZACION EN DERECHO CIVIL' => 'ESPECIALIZACION EN DERECHO CIVIL',
        'MERCADEO' => 'MERCADEO',
    ],
    'situacionlaboral' => [
        'Empleado'      => 'Empleado',
        'Empresario'    => 'Empresario',
        'Pensionado'    => 'Pensionado',
        'Desempleado'   => 'Desempleado',
        'Emprendedor'   => 'Emprendedor',
        'Independiente' => 'Independiente',
    ],
    'sectordeempresa' => [
        'Industrial'        => 'Industrial',
        'Comercial'         => 'Comercial',
        'servicios'         => 'servicios',
        'financiero'        => 'financiero',
        'Agrario'           => 'Agrario',
        'Educativo'         => 'Educativo',
        'Salud'             => 'Salud',
        'Fuerzas Militares' => 'Fuerzas Militares',
        'ONG'               => 'ONG',
        'No aplica'         => 'No aplica',
    ],
    'aniodegraduacion' => array_combine(
        range((int) date('Y'), 1960),
        range((int) date('Y'), 1960)
    ),
    'maximonivelacademico' => [
        'Técnico' => 'Técnico',
        'Tecnológico' => 'Tecnológico',
        'Universitario' => 'Universitario',
        'Especialización' => 'Especialización',
        'Maestría' => 'Maestría',
        'Doctorado' => 'Doctorado',
    ],
];

$forcedLabels = [
    'sexo'              => 'Sexo',
    'genero'            => 'Género',
    'programaacademico' => 'Programa académico',
    'situacionlaboral'  => 'Situación laboral',
    'sectordeempresa'   => 'Sector de la empresa',
    'aniodegraduacion'  => 'Año de graduación',
    'maximonivelacademico' => 'Máximo nivel académico',
];

$appliedForceKeys = [];

foreach ($formColumns as $index => $column) {
    $norm = $column['norm'];
    $labelLower = normalize_key($column['label']);
    $labelCompact = normalize_key_compact($column['label']);

    if (in_array($labelCompact, $excludedFilterQuestions, true)) {
        $formColumns[$index]['filterable'] = false;
        $formColumns[$index]['options'] = [];
        $formColumns[$index]['forced'] = false;
        continue;
    }

    $matchedForceKey = null;

    if (
        str_contains($norm, 'sexo')
        || str_contains($labelCompact, 'sexo')
        || preg_match('/sexo/u', $labelLower)
    ) {
        $matchedForceKey = 'sexo';
    } elseif (
        str_contains($norm, 'genero')
        || str_contains($labelCompact, 'genero')
        || preg_match('/genero/u', $labelLower)
    ) {
        $matchedForceKey = 'genero';
    } elseif (
        str_contains($norm, 'programa')
        || str_contains($norm, 'carrera')
        || str_contains($norm, 'titulo')
        || str_contains($norm, 'titulacion')
        || str_contains($labelCompact, 'programa')
        || str_contains($labelCompact, 'carrera')
        || preg_match('/(programa|carrera|titulo|titulacion)/u', $labelLower)
    ) {
        $matchedForceKey = 'programaacademico';
    } elseif (
        str_contains($norm, 'situacion')
        || str_contains($norm, 'ocupacion')
        || str_contains($norm, 'laboral')
        || str_contains($labelCompact, 'situacion')
        || str_contains($labelCompact, 'ocupacion')
        || preg_match('/(situacion|ocupacion|condicion).*?(laboral|actual)?|(laboral|ocupacion)/u', $labelLower)
    ) {
        $matchedForceKey = 'situacionlaboral';
    } elseif (
        (
            str_contains($norm, 'sectordeempresa')
            || str_contains($labelCompact, 'sectordeempresa')
            || (
                str_contains($labelCompact, 'sector')
                && (
                    str_contains($labelCompact, 'empresa')
                    || str_contains($labelCompact, 'empleador')
                )
            )
            || preg_match('/\bsector\s+de\s+la\s+empresa\b/u', $labelLower)
        )
        && !in_array($labelCompact, $excludedFilterQuestions, true)
    ) {
        $matchedForceKey = 'sectordeempresa';
    } elseif (
        str_contains($norm, 'maximonivelacademico')
        || str_contains($labelCompact, 'maximonivelacademico')
        || (
            str_contains($labelCompact, 'maximo')
            && str_contains($labelCompact, 'nivel')
            && str_contains($labelCompact, 'academico')
        )
        || (
            str_contains($labelCompact, 'maximo')
            && str_contains($labelCompact, 'nivel')
            && str_contains($labelCompact, 'educativo')
        )
    ) {
        $matchedForceKey = 'maximonivelacademico';
    } elseif (
        (
            (
                str_contains($labelCompact, 'ano')
                || str_contains($labelCompact, 'anio')
            )
            && str_contains($labelCompact, 'graduacion')
        )
        || str_contains($norm, 'aniodegraduacion')
        || str_contains($labelCompact, 'aniodegraduacion')
        || preg_match('/\b(año|ano|anio)\s*(de)?\s*graduaci[oó]n\b/u', $labelLower)
    ) {
        $matchedForceKey = 'aniodegraduacion';
    }

    if ($matchedForceKey !== null && isset($forcedFilters[$matchedForceKey])) {
        $formColumns[$index]['filterable'] = true;
        $formColumns[$index]['options'] = $forcedFilters[$matchedForceKey];
        $formColumns[$index]['forced'] = true;
        $formColumns[$index]['isSex'] = false;
        $formColumns[$index]['filterType'] = $matchedForceKey;
        $appliedForceKeys[$matchedForceKey] = true;
    }
}

foreach ($forcedFilters as $forceKey => $options) {
    if (isset($appliedForceKeys[$forceKey])) {
        continue;
    }

    $formColumns[] = [
        'norm'       => $forceKey,
        'label'      => $forcedLabels[$forceKey] ?? $forceKey,
        'keys'       => [],
        'isSex'      => false,
        'filterable' => true,
        'options'    => $options,
        'forced'     => true,
        'filterType' => $forceKey,
    ];
}

$dbComparable = [
    'identification_number',
    'name',
    'last_name',
    'email',
    'mobile_phone',
    'alternative_mobile_phone',
    'address',
    'city',
    'country',
];

$normalizedDb = array_map('normalize_key', $dbComparable);
$removed = [];

$totalRowsForComparison = max(1, count($allRows));

foreach ($formColumns as $index => $column) {
    $nonEmpty = 0;
    $conflicts = 0;
    $comparisons = 0;

    $matchedDbIndex = array_search(
        $column['norm'],
        $normalizedDb,
        true
    );

    $matchedDb = $matchedDbIndex !== false
        ? $dbComparable[$matchedDbIndex]
        : null;

    foreach ($allRows as $row) {
        $val = getDynamicColumnValue($row, $column);

        if ($val !== '') {
            $nonEmpty++;
        }

        if ($matchedDb && $val !== '') {
            $dbVal = stringifyAnswer($row[$matchedDb] ?? '');

            if ($dbVal !== '') {
                $comparisons++;

                if (
                    mb_strtolower($dbVal, 'UTF-8')
                    !== mb_strtolower($val, 'UTF-8')
                ) {
                    $conflicts++;
                }
            }
        }
    }

    // Nunca eliminar columnas de filtros forzados
    if (!empty($column['forced'])) {
        continue;
    }

    if (
        $nonEmpty === 0
        || (
            $comparisons > 0
            && ($conflicts / $comparisons) > 0.5
        )
    ) {
        $removed[] = $index;
    }
}

$formColumns = array_values(
    array_filter(
        $formColumns,
        function ($_column, $index) use ($removed) {
            return !in_array($index, $removed, true);
        },
        ARRAY_FILTER_USE_BOTH
    )
);

$baseColumns = [
    'updated_at' => 'Fecha actualización',
    'identification_number' => 'Cédula',
    'name' => 'Nombre',
    'last_name' => 'Apellido',
    'email' => 'Correo',
    'mobile_phone' => 'Teléfono',
    'city' => 'Ciudad',
];

foreach ($allRows as $rowIndex => $row) {
    foreach ($formColumns as $column) {
        $allRows[$rowIndex]['dynamic_values'][$column['norm']] =
            getDynamicColumnValue($row, $column);
    }

    foreach ($baseColumns as $field => $_label) {
        $allRows[$rowIndex]['base_values'][$field] =
            getBaseColumnValue($row, $field);
    }
}

$formColumns = array_values($formColumns);

$baseFilterableColumns = [];

foreach ($baseColumns as $field => $label) {
    $baseFilterableColumns[$field] = [
        'label' => $label,
        'filterable' => false,
        'options' => [],
        'isSex' => false,
    ];
}

/*
 * Aplicar TODOS los filtros en PHP sobre el conjunto completo.
 * Luego de esto se pagina.
 */
$filteredRows = array_values(
    array_filter(
        $allRows,
        function (array $row) use (
            $filters,
            $formColumns,
            $baseFilterableColumns
        ): bool {
            foreach ($filters as $filterKey => $filterValue) {
                if (is_array($filterValue)) {
                    continue;
                }

                $filterValue = trim((string) $filterValue);

                if ($filterValue === '') {
                    continue;
                }

                $currentValue = '';
                $currentFilterType = null;

                if (isset($baseFilterableColumns[$filterKey])) {
                    if (!$baseFilterableColumns[$filterKey]['filterable']) {
                        continue;
                    }

                    $field = $filterKey;
                    $currentValue =
                        $row['base_values'][$field] ?? '';
                } else {
                    foreach ($formColumns as $column) {
                        if ($column['norm'] === $filterKey) {
                            if (!$column['filterable']) {
                                continue 2;
                            }

                            $currentFilterType =
                                $column['filterType'] ?? null;

                            if ($currentFilterType === 'aniodegraduacion') {
                                $currentValue =
                                    $row['dynamic_values'][$filterKey] ?? '';

                                if ($currentValue === '') {
                                    foreach (($row['extra_answers'] ?? []) as $answer) {
                                        $answerText = stringifyAnswer($answer);

                                        if (
                                            preg_match(
                                                '/\b(19|20)\d{2}\b/',
                                                $answerText,
                                                $yearMatch
                                            )
                                        ) {
                                            $currentValue = $yearMatch[0];
                                            break;
                                        }
                                    }
                                }
                            } else {
                                $currentValue =
                                    $row['dynamic_values'][$filterKey] ?? '';
                            }

                            break;
                        }
                    }
                }

                /*
                 * Sector de la empresa:
                 * la opción seleccionada debe aparecer dentro del valor,
                 * aunque antes tenga "Privado", "Público", etc.
                 */
                $normalizedFilterKey = normalize_key($filterKey);

                if (
                    $currentFilterType === 'sectordeempresa'
                    || $filterKey === 'sectordeempresa'
                    || (
                        str_contains($normalizedFilterKey, 'sector')
                        && str_contains($normalizedFilterKey, 'empresa')
                    )
                ) {
                    $currentNormalized = normalize_key($currentValue);
                    $filterNormalized = normalize_key($filterValue);

                    if (
                        $filterNormalized === ''
                        || !str_contains($currentNormalized, $filterNormalized)
                    ) {
                        return false;
                    }
                } elseif (
                    $currentFilterType === 'aniodegraduacion'
                    || (
                        str_contains($normalizedFilterKey, 'ano')
                        && str_contains($normalizedFilterKey, 'graduacion')
                    )
                ) {
                    // Si la respuesta trae texto adicional, filtramos por el año.
                    if (
                        !preg_match(
                            '/\b(19|20)\d{2}\b/',
                            $currentValue,
                            $yearMatch
                        )
                        || $yearMatch[0] !== $filterValue
                    ) {
                        return false;
                    }
                } elseif ($currentValue !== $filterValue) {
                    return false;
                }
            }

            return true;
        }
    )
);

$total = count($filteredRows);
$totalPages = max((int) ceil($total / $limit), 1);

if (($_GET['export'] ?? '') === 'excel') {
    exportMigratedFullCsv($filteredRows, $baseColumns, $formColumns);
    exit;
}

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $limit;

$migratedAnswers = array_slice(
    $filteredRows,
    $offset,
    $limit
);

/*
 * Solo ahora se conserva la página solicitada.
 */
$message = $_SESSION['message'] ?? null;
$error = $_SESSION['error'] ?? null;

unset(
    $_SESSION['message'],
    $_SESSION['error']
);

$blade = new BladeOne(
    __DIR__ . '/views',
    __DIR__ . '/cache',
    BladeOne::MODE_AUTO
);

echo $blade->run(
    'migrated_full',
    [
        'migratedAnswers' => $migratedAnswers,
        'page' => $page,
        'totalPages' => $totalPages,
        'total' => $total,
        'search' => $search,
        'filters' => $filters,
        'message' => $message,
        'error' => $error,
        'formColumns' => $formColumns,
        'baseColumns' => $baseColumns,
        'baseFilterableColumns' => $baseFilterableColumns,
    ]
);

function exportMigratedFullCsv(
    array $rows,
    array $baseColumns,
    array $formColumns
): void {
    $filename = 'migrated_full_' . date('Y-m-d_H-i-s') . '.csv';

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'wb');
    fwrite($output, "\xEF\xBB\xBF");

    $headers = array_values($baseColumns);
    foreach ($formColumns as $column) {
        $headers[] = $column['label'];
    }

    fputcsv($output, $headers, ';');

    foreach ($rows as $row) {
        $values = [];

        foreach ($baseColumns as $field => $_label) {
            $values[] = excelSafeValue($row['base_values'][$field] ?? '');
        }

        foreach ($formColumns as $column) {
            $values[] = excelSafeValue(
                $row['dynamic_values'][$column['norm']] ?? ''
            );
        }

        fputcsv($output, $values, ';');
    }

    fclose($output);
}

function excelSafeValue($value): string
{
    $value = trim((string) ($value ?? ''));

    if ($value !== '' && preg_match('/^[=+\-@]/', $value)) {
        return "'" . $value;
    }

    return $value;
}
