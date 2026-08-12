@component('templates.main')

    @slot('title')
        Historial completo SIGA
    @endslot

    @slot('header')
        <style>
            .page-scroll { overflow-x: auto; width: 100%; }
            table { min-width: 1200px; }
            th, td {
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
            height: 46px;
            border-right: 1px solid #dee2e6;
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
        </form>

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
            });
        </script>
    @endslot

@endcomponent
