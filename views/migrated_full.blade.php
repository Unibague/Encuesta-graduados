@component('templates.main')

    @slot('title')
        Historial completo SIGA
    @endslot

    @slot('header')
        <style>
<<<<<<< Updated upstream
            .page-scroll { overflow-x: auto; width: 100%; }
            table { min-width: 1200px; }
            th, td {
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
            height: 46px;
            border-right: 1px solid #dee2e6;
=======

        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');

        :root {
            --primary: #1A3A6B;
            --primary-dark: #0F2747;
            --primary-light: #E8EEF7;
            --success: #16a672;
            --success-light: #e4f7f0;
            --danger: #ef4444;
            --text: #1f2333;
            --text-muted: #7c8093;
            --border: #e6e5f1;
            --surface: #ffffff;
            --background: #f6f6fb;
            --shadow: 0 20px 45px -20px rgba(26, 58, 107, 0.35);
        }

        .migrated-full-page {
            min-height: 100vh;
            padding: 40px 16px 60px;
            background:
                radial-gradient(circle at 12% 8%, rgba(26, 58, 107, 0.18), transparent 40%),
                radial-gradient(circle at 88% 92%, rgba(22, 166, 114, 0.14), transparent 42%),
                var(--background);
            color: var(--text);
            font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .migrated-full-container {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
        }

        .migrated-full-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .migrated-full-header h1 {
            color: var(--text);
            font-size: 1.9rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0 0 8px;
        }

        .migrated-full-header p {
            color: var(--text-muted) !important;
            margin: 0;
            font-size: 0.95rem;
        }

        .migrated-full-card {
            position: relative;
            overflow: hidden;
            background: var(--surface);
            border: 1px solid rgba(26, 58, 107, 0.12);
            border-radius: 26px;
            padding: 28px;
            box-shadow: var(--shadow);
        }

        .migrated-full-card::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(26, 58, 107,0.12), transparent 70%);
            pointer-events: none;
        }

        .migrated-full-form {
            position: relative;
            z-index: 1;
        }

        .filters-toolbar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .filters-toolbar input {
            min-width: 280px !important;
            width: min(520px, 100%);
            padding: 13px 16px !important;
            border: 2px solid var(--border) !important;
            border-radius: 14px !important;
            font-size: 0.95rem;
            font-family: inherit;
            color: var(--text);
            background: #fbfbfe;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .filters-toolbar input:focus {
            outline: none;
            border-color: var(--primary) !important;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(26, 58, 107, 0.18);
        }

        .filters-toolbar .btn-primary {
            border: none;
            border-radius: 13px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #1f2333;
            padding: 13px 22px;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            box-shadow: 0 8px 18px -8px rgba(26, 58, 107, 0.5);
        }

        .filters-toolbar .btn-primary:hover {
            background: linear-gradient(135deg, #3B5B8C, var(--primary-dark));
            color: #1f2333;
        }

        .filters-toolbar .btn-outline-secondary {
            border: 2px solid var(--border);
            border-radius: 13px;
            background: #fff;
            color: var(--text-muted);
            padding: 11px 20px;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
        }

        .filters-toolbar .btn-outline-secondary:hover {
            border-color: var(--primary);
            background: var(--primary-light);
            color: var(--primary-dark);
        }

        .migrated-full-table-wrap {
            position: relative;
            z-index: 1;
            overflow-x: auto;
            width: 100%;
            border-radius: 16px;
            -webkit-overflow-scrolling: touch;
        }

        .migrated-full-table {
            width: max-content;
            min-width: 100%;
            margin: 0 !important;
            border-collapse: separate !important;
            border-spacing: 0;
            table-layout: auto;
            font-family: inherit;
            color: var(--text);
            font-size: 0.86rem;
        }

        .migrated-full-table thead th {
            background: var(--primary) !important;
            color: #fff !important;
            border: 0 !important;
            border-bottom: 2px solid rgba(26, 58, 107, 0.25) !important;
            padding: 13px 16px !important;
            min-height: 52px;
            min-width: 100px;
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
        }

        .migrated-full-table thead tr:first-child th:first-child {
            border-radius: 12px 0 0 0;
        }

        .migrated-full-table thead tr:first-child th:last-child {
            border-radius: 0 12px 0 0;
        }

        .migrated-full-table .filter-row th {
            background: #fffdf5 !important;
            padding: 7px 10px !important;
            height: auto;
            border-bottom: 1px solid #f0f0f7 !important;
            min-width: 100px;
        }

        .migrated-full-table .filter-row select {
            display: block;
            margin: 0 auto;
            text-align: center;
            font-size: 0.75rem;
            padding: 7px 8px;
            max-width: 180px;
            width: 100%;
            min-width: 0;
            border: 2px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            background: #fff;
            font-family: inherit;
        }

        .migrated-full-table .filter-row select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26, 58, 107, 0.15);
        }

        .migrated-full-table tbody td {
            min-height: 52px;
            min-width: 100px;
            padding: 12px 16px !important;
            border-bottom: 1px solid #f0f0f7 !important;
            border-right: 0 !important;
            background: #fff;
            color: var(--text);
            font-size: 13px;
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
        }

        .migrated-full-table tbody tr:hover td {
            background: #fafafd;
        }

        .migrated-full-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        .migrated-full-table strong {
            font-weight: 800;
        }

        .view-answers {
            white-space: nowrap;
            color: #fff !important;
        }

        .answers-modal .modal-content {
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(15, 39, 71, 0.25);
        }

        .answers-modal .modal-header {
            padding: 20px 24px;
            background: var(--primary);
            color: #fff;
            border-bottom: 0;
        }

        .answers-modal .modal-title {
            max-width: calc(100% - 40px);
            font-size: 1.05rem;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .answers-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .answers-modal .modal-body {
            padding: 20px;
            background: #f6f8fc;
        }

        .answers-list {
            display: grid;
            gap: 12px;
        }

        .answer-item {
            display: grid;
            grid-template-columns: minmax(180px, 0.8fr) minmax(0, 1.2fr);
            gap: 16px;
            padding: 16px;
            background: #fff;
            border: 1px solid #e5eaf2;
            border-radius: 12px;
        }

        .answer-question,
        .answer-value {
            min-width: 0;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .answer-question {
            color: var(--primary-dark);
            font-size: 0.82rem;
            font-weight: 800;
            line-height: 1.35;
        }

        .answer-value {
            color: var(--text);
            font-size: 0.9rem;
            line-height: 1.5;
            white-space: pre-wrap;
        }

        @media (max-width: 576px) {
            .answers-modal .modal-body {
                padding: 12px;
            }

            .answer-item {
                grid-template-columns: 1fr;
                gap: 6px;
                padding: 14px;
            }
        }

        /* Mantiene el texto en una sola línea para que las columnas se adapten al contenido */
        .migrated-full-table .pre-wrap {
            white-space: nowrap;
            word-break: normal;
            overflow-wrap: normal;
        }

        .migrated-full-table .text-bg-success {
            background: var(--success-light) !important;
            color: var(--success) !important;
        }

        .migrated-full-table .text-bg-danger {
            background: #fef2f2 !important;
            color: var(--danger) !important;
        }

        .badge-femenino,
        .badge-masculino,
        .migrated-full-table .text-bg-success,
        .migrated-full-table .text-bg-danger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 82px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-femenino {
            background: #fce7f3 !important;
            color: #be185d !important;
        }

        .badge-masculino {
            background: #e0edff !important;
            color: #2563eb !important;
        }

        .migrated-full-pagination {
            position: relative;
            z-index: 1;
            margin-top: 28px;
        }

        .migrated-full-pagination .pagination {
            display: flex;
            justify-content: center;
            gap: 4px;
            margin: 0;
        }

        .migrated-full-pagination .page-link {
            min-width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 12px;
            border: 2px solid var(--border);
            border-radius: 11px !important;
            background: #fff;
            color: var(--text);
            font-size: 13.5px;
            font-weight: 700;
            font-family: inherit;
            transition: all 0.15s;
        }

        .migrated-full-pagination .page-link:hover {
            border-color: var(--primary);
            color: var(--primary-dark);
            background: var(--primary-light);
        }

        .migrated-full-pagination .page-item.active .page-link {
            border-color: transparent;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #1f2333;
            box-shadow: 0 6px 14px -6px rgba(26, 58, 107, 0.5);
        }

        .migrated-full-pagination .page-item.disabled .page-link {
            opacity: 0.4;
            pointer-events: none;
        }

        .migrated-full-toast {
            border-radius: 14px !important;
            box-shadow: 0 15px 35px -15px rgba(31, 35, 51, 0.35);
            overflow: hidden;
            font-family: 'Manrope', sans-serif;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .migrated-full-page {
                padding: 28px 12px 40px;
>>>>>>> Stashed changes
            }

            th:last-child,
            td:last-child {
            border-right: none;
            }

            .badge-graduado   { background: #198754; color: #fff; }
            .badge-nograduado { background: #6c757d; color: #fff; }
            .badge-femenino   { background: #d63384; color: #fff; }
            .badge-masculino  { background: #0d6efd; color: #fff; }

            .filter-row th {
                text-align: center;
                vertical-align: middle;
                background: #f1f3f5;
                padding: 4px 6px;
                height: auto;
            }

            .filter-row select {
                display: block;
                margin: 0 auto;
                text-align: center;
                font-size: 0.75rem;
                padding: 2px 4px;
                max-width: 160px;
                width: 100%;
                box-sizing: border-box;
                min-width: 0;
            }

            .filters-toolbar {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 0.5rem;
                flex-wrap: wrap;
                margin-bottom: 0.5rem;
            }
        </style>
    @endslot

    @if(!empty($message))
        <div class="toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-3"
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">{{ $message }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    <div class="page-scroll">

        <h1 class="text-center mb-1">Datos de egresados SIGA</h1>
        <p class="text-center text-muted mb-4">
            {{ number_format($total) }} Dato(s) coinciden con la búsqueda / filtros.
        </p>

        @php
            $allColumns = [];
            $filters = $filters ?? [];

            $baseColumns = $baseColumns ?? [
                'updated_at'               => 'Fecha actualización',
                'identification_number'    => 'Cédula',
                'name'                     => 'Nombres',
                'last_name'                => 'Apellidos',
                'email'                   => 'Correo',
                'mobile_phone'             => 'Teléfono',
                'city'                     => 'Ciudad',
                'is_migrated'              => 'Estado SIGA',
            ];

            $baseFilterableColumns = $baseFilterableColumns ?? [];

            $totalColumns = count($baseColumns) + 1;
            $hasActiveFilters = !empty(array_filter($filters, function ($value) {
                return !is_array($value) && trim((string) $value) !== '';
            }));

             
            $baseQuery = [];

            if (($search ?? '') !== '') {
                $baseQuery['search'] = $search;
            }

            if (!empty($filters)) {
                $cleanFilters = [];

                foreach ($filters as $key => $value) {
                    if (!is_array($value) && trim((string) $value) !== '') {
                        $cleanFilters[$key] = $value;
                    }
                }

                if (!empty($cleanFilters)) {
                    $baseQuery['filter'] = $cleanFilters;
                }
            }

            if (!function_exists('migratedFullPageUrl')) {
                function migratedFullPageUrl(array $baseQuery, int $pageNumber): string
                {
                    $query = $baseQuery;
                    $query['page'] = $pageNumber;

                    return '?' . http_build_query($query);
                }
            }
        @endphp

        <form method="GET" class="mb-3">

            <div class="filters-toolbar">
                <input type="text"
                       name="search"
                       value="{{ $search ?? '' }}"
                       class="form-control w-auto"
                       style="min-width: 280px;"
                       placeholder="Buscar por cédula, nombre, correo, ciudad...">

                <button class="btn btn-primary">
                    Buscar / aplicar filtros
                </button>

                @if($hasActiveFilters || ($search ?? '') !== '')
                    <a href="?"
                       class="btn btn-outline-secondary">
                        Limpiar filtros
                    </a>
                @endif
            </div>

            <table class="table table-striped table-hover table-sm">

                <thead class="table-dark">
                    <tr>
                        @foreach($baseColumns as $field => $title)
                            <th>{{ $title }}</th>
                        @endforeach
                        <th>Respuestas</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($migratedAnswers as $row)

                    <tr>

                        @foreach($baseColumns as $field => $title)
                            <td>
                                @php
                                    $baseVal = $row['base_values'][$field] ?? '';
                                @endphp

                                @if($field === 'is_migrated')
                                    @if($baseVal === '1')
                                        <span class="badge text-bg-success">Actualizado</span>
                                    @else
                                        <span class="badge text-bg-danger">Pendiente</span>
                                    @endif
                                @elseif($field === 'identification_number')
                                    <div class="pre-wrap">
                                        <strong>{{ $baseVal !== '' ? $baseVal : '—' }}</strong>
                                    </div>

                                @else
                                    <div class="pre-wrap">
                                        {{ $baseVal !== '' ? $baseVal : '—' }}
                                    </div>
                                @endif

                            </td>
                        @endforeach

                        <td>
                            <button type="button" class="btn btn-primary btn-sm view-answers"
                                    data-answers="{{ htmlspecialchars(json_encode($row['extra_answers'] ?? [], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') }}"
                                    data-person="{{ $row['name'] }} {{ $row['last_name'] }}">
                                Ver respuestas
                            </button>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="{{ max(1, $totalColumns) }}"
                            class="text-center text-muted py-4">
                            No hay datos que coincidan con la búsqueda / filtros seleccionados.
                        </td>
                    </tr>

                @endforelse

                </tbody>
            </table>
        </form>

        <div class="modal fade answers-modal" id="answersModal" tabindex="-1" aria-labelledby="answersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="answersModalLabel">Respuestas de la encuesta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div id="answersList" class="answers-list"></div>
                    </div>
                </div>
            </div>
        </div>

        @if($totalPages > 1)
            <nav class="d-flex justify-content-center mt-4">
                <ul class="pagination">

                    <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                        <a class="page-link"
                           href="{{ migratedFullPageUrl($baseQuery, $page - 1) }}">
                            «
                        </a>
                    </li>

                    @for($i = max(1, $page - 3); $i <= min($totalPages, $page + 3); $i++)
                        <li class="page-item {{ $i == $page ? 'active' : '' }}">
                            <a class="page-link"
                               href="{{ migratedFullPageUrl($baseQuery, $i) }}">
                                {{ $i }}
                            </a>
                        </li>
                    @endfor

                    <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                        <a class="page-link"
                           href="{{ migratedFullPageUrl($baseQuery, $page + 1) }}">
                            »
                        </a>
                    </li>

                </ul>
            </nav>
        @endif

    </div>

    @slot('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.toast').forEach(function (el) {
                    new bootstrap.Toast(el, { delay: 5000 }).show();
                });

                const modal = new bootstrap.Modal(document.getElementById('answersModal'));
                const list = document.getElementById('answersList');
                const title = document.getElementById('answersModalLabel');

                document.querySelectorAll('.view-answers').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const answers = JSON.parse(button.dataset.answers || '{}');
                        title.textContent = 'Respuestas de ' + button.dataset.person;
                        list.replaceChildren();

                        Object.entries(answers).forEach(function ([question, value]) {
                            if (question.startsWith('_')) return;

                            const item = document.createElement('article');
                            item.className = 'answer-item';

                            const label = document.createElement('div');
                            label.className = 'answer-question';
                            label.textContent = question;

                            const answer = document.createElement('div');
                            answer.className = 'answer-value';
                            answer.textContent = Array.isArray(value)
                                ? value.join(', ')
                                : typeof value === 'object' && value !== null
                                    ? JSON.stringify(value)
                                    : String(value ?? '');

                            item.append(label, answer);
                            list.append(item);
                        });

                        modal.show();
                    });
                });
            });
        </script>
    @endslot

@endcomponent
