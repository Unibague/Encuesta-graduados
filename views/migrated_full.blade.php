@component('templates.main')

    @slot('title')
        Historial completo SIGA
    @endslot

    @slot('header')
        <style>

        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');

        :root {
            --primary: #FCBD00;
            --primary-dark: #D9A200;
            --primary-light: #FFF8E0;
            --success: #16a672;
            --success-light: #e4f7f0;
            --danger: #ef4444;
            --text: #1f2333;
            --text-muted: #7c8093;
            --border: #e6e5f1;
            --surface: #ffffff;
            --background: #f6f6fb;
            --shadow: 0 20px 45px -20px rgba(252, 189, 0, 0.35);
        }

        .migrated-full-page {
            min-height: 100vh;
            padding: 40px 16px 60px;
            background:
                radial-gradient(circle at 12% 8%, rgba(252, 189, 0, 0.18), transparent 40%),
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
            border: 1px solid rgba(252, 189, 0, 0.12);
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
            background: radial-gradient(circle, rgba(252,189,0,0.12), transparent 70%);
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
            box-shadow: 0 0 0 4px rgba(252, 189, 0, 0.18);
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
            box-shadow: 0 8px 18px -8px rgba(252, 189, 0, 0.5);
        }

        .filters-toolbar .btn-primary:hover {
            background: linear-gradient(135deg, #ffd04a, var(--primary-dark));
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
            background: var(--primary-light) !important;
            color: var(--primary-dark) !important;
            border: 0 !important;
            border-bottom: 2px solid rgba(252, 189, 0, 0.25) !important;
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
            box-shadow: 0 0 0 3px rgba(252, 189, 0, 0.15);
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
            box-shadow: 0 6px 14px -6px rgba(252, 189, 0, 0.5);
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
            }

            .migrated-full-header h1 {
                font-size: 1.5rem;
            }

            .migrated-full-card {
                padding: 18px;
                border-radius: 20px;
            }

            .filters-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .filters-toolbar input,
            .filters-toolbar .btn {
                width: 100% !important;
            }
        }

        @media (max-width: 560px) {
            .migrated-full-page {
                padding: 22px 8px 32px;
            }

            .migrated-full-card {
                padding: 14px;
                border-radius: 18px;
            }

            .migrated-full-header h1 {
                font-size: 1.35rem;
            }

            .migrated-full-header p {
                font-size: 0.85rem;
            }
        }
        </style>
    @endslot

    @if(!empty($message))
        <div class="toast migrated-full-toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-3"
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">{{ $message }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    <div class="migrated-full-page">
    <div class="migrated-full-container">

        <div class="migrated-full-header">
        <h1>Datos de egresados SIGA</h1>
        <p>
            {{ number_format($total) }} Dato(s) coinciden con la búsqueda / filtros.
        </p>
        </div>

        <div class="migrated-full-card">
        @php
            $allColumns = $formColumns ?? [];
            $filters = $filters ?? [];

            $baseColumns = $baseColumns ?? [
                'id'                       => 'ID',
                'identification_number'    => 'Cédula',
                'name'                     => 'Nombres',
                'last_name'                => 'Apellidos',
                'email'                   => 'Correo',
                'mobile_phone'             => 'Teléfono',
                'alternative_mobile_phone' => 'Teléfono alterno',
                'address'                  => 'Dirección',
                'city'                     => 'Ciudad',
                'country'                  => 'País',
                'is_graduated'             => '¿Graduado?',
                'updated_at'               => 'Última actualización',
            ];

            $baseFilterableColumns = $baseFilterableColumns ?? [];

            $totalColumns = count($baseColumns) + count($allColumns);
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

        <form method="GET" class="migrated-full-form">

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

            <div class="migrated-full-table-wrap">
            <table class="migrated-full-table">

                <thead class="table-dark">
                    <tr>
                        @foreach($baseColumns as $field => $title)
                            <th>{{ $title }}</th>
                        @endforeach

                        @forelse($allColumns as $column)
                            <th>
                                {{ $column['label'] }}

                                @if(!empty($column['isSex']))
                                    <span class="text-info">(Sexo)</span>
                                @endif
                            </th>
                        @empty
                            <th>Sin preguntas adicionales</th>
                        @endforelse
                    </tr>

                    {{-- Filtros generados automáticamente para cualquier columna
                         con <= $filterVariantLimit variantes no vacías. --}}
                    <tr class="filter-row">

                        @foreach($baseColumns as $field => $title)
                            <th>
                                @php
                                    $baseFilter = $baseFilterableColumns[$field] ?? null;
                                @endphp

                                @if(!empty($baseFilter['filterable']))
                                    <select name="filter[{{ $field }}]"
                                            class="form-select form-select-sm"
                                            onchange="this.form.submit()">

                                        <option value="">Todos</option>

                                        @foreach($baseFilter['options'] as $value => $optLabel)
                                            <option value="{{ $value }}"
                                                {{ ($filters[$field] ?? '') === (string) $value ? 'selected' : '' }}>
                                                {{ $optLabel }}
                                            </option>
                                        @endforeach

                                    </select>
                                @endif
                            </th>
                        @endforeach

                        @forelse($allColumns as $column)
                            <th>
                                @if(!empty($column['filterable']))
                                    <select name="filter[{{ $column['norm'] }}]"
                                            class="form-select form-select-sm"
                                            onchange="this.form.submit()">

                                        <option value="">Todos</option>

                                        @foreach($column['options'] as $value => $optLabel)
                                            <option value="{{ $value }}"
                                                {{ ($filters[$column['norm']] ?? '') === (string) $value ? 'selected' : '' }}>
                                                {{ $optLabel }}
                                            </option>
                                        @endforeach

                                    </select>
                                @endif
                            </th>
                        @empty
                            <th></th>
                        @endforelse

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

                                @if($field === 'is_graduated')
                                    @if($baseVal === '1')
                                        <span class="badge text-bg-success">Sí</span>
                                    @else
                                        <span class="badge text-bg-danger">No</span>
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

                        {{-- Respuestas dinámicas ya normalizadas en PHP --}}
                        @foreach($allColumns as $column)
                            <td>
                                @php
                                    $val = $row['dynamic_values'][$column['norm']] ?? '';
                                @endphp

                                @if(!empty($column['isSex']) && $val !== '')
                                    @php
                                        $sexNorm = mb_strtolower($val, 'UTF-8');
                                        $sexConverted = iconv(
                                            'UTF-8',
                                            'ASCII//TRANSLIT//IGNORE',
                                            $sexNorm
                                        );
                                        $sexNorm = $sexConverted !== false
                                            ? $sexConverted
                                            : $sexNorm;
                                    @endphp

                                    @if(str_starts_with($sexNorm, 'f'))
                                        <span class="badge badge-femenino">Femenino</span>

                                    @elseif(str_starts_with($sexNorm, 'm'))
                                        <span class="badge badge-masculino">Masculino</span>

                                    @else
                                        <div class="pre-wrap">{{ $val }}</div>
                                    @endif

                                @else
                                    <div class="pre-wrap">
                                        {{ $val !== '' ? $val : '—' }}
                                    </div>
                                @endif

                            </td>
                        @endforeach

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
        </div>
        </form>

        @if($totalPages > 1)
            <nav class="migrated-full-pagination">
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
    </div>
    </div>

    @slot('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.toast').forEach(function (el) {
                    new bootstrap.Toast(el, { delay: 5000 }).show();
                });
            });
        </script>
    @endslot

@endcomponent