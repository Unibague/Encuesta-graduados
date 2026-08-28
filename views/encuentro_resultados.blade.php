@component('templates.main')

    @slot('title')
        Encuentro de Graduados
    @endslot

    @slot('header')
        <style>
            .encuentro-page {
                padding: 10px 0 30px;
            }

            .encuentro-page h1 {
                font-size: 1.75rem;
                font-weight: 800;
                margin-bottom: 6px;
                text-align: center;
            }

            .encuentro-subtitle {
                color: var(--text-muted);
                margin-bottom: 24px;
                text-align: center;
            }

            .encuentro-card {
                background: #fff;
                border: 1px solid rgba(26, 58, 107, .12);
                border-radius: 16px;
                box-shadow: var(--shadow);
                padding: 24px;
            }

            .encuentro-search {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                justify-content: center;
                margin-bottom: 22px;
            }

            .encuentro-search input {
                max-width: 480px;
            }

            .encuentro-table {
                min-width: 700px !important;
            }

            .answers-modal .modal-content {
                border: 0;
                border-radius: 18px;
                overflow: hidden;
            }

            .answers-modal .modal-header {
                background: var(--primary);
                color: #fff;
            }

            .answers-modal .btn-close {
                filter: brightness(0) invert(1);
            }

            .answers-modal .modal-body {
                background: #f6f8fc;
                max-height: 70vh;
                overflow-y: auto;
            }

            .answers-list {
                display: grid;
                gap: 12px;
            }

            .answer-item {
                background: #fff;
                border: 1px solid var(--border);
                border-radius: 12px;
                padding: 14px 16px;
            }

            .answer-question {
                color: var(--primary-dark);
                font-size: .8rem;
                font-weight: 800;
                margin-bottom: 4px;
            }

            .answer-value {
                color: var(--text);
                overflow-wrap: anywhere;
                white-space: pre-wrap;
            }

            .encuentro-config-bar {
                align-items: center;
                background: var(--primary-light);
                border: 1px solid rgba(26, 58, 107, .18);
                border-radius: 14px;
                display: flex;
                flex-wrap: wrap;
                gap: 14px;
                justify-content: space-between;
                margin-bottom: 20px;
                padding: 14px 18px;
            }

            .encuentro-config-info {
                align-items: center;
                display: flex;
                gap: 10px;
            }

            .encuentro-config-label {
                color: var(--primary-dark);
                font-size: .85rem;
                font-weight: 700;
            }

            .encuentro-config-badge {
                background: var(--primary);
                border-radius: 999px;
                color: #fff;
                font-size: .95rem;
                font-weight: 800;
                padding: 4px 14px;
            }

            .encuentro-config-form {
                align-items: center;
                display: flex;
                gap: 10px;
            }

            .encuentro-config-hint {
                color: var(--primary-dark);
                font-size: .85rem;
                font-weight: 600;
            }

            .encuentro-switch {
                display: inline-block;
                flex-shrink: 0;
                height: 26px;
                position: relative;
                width: 46px;
            }

            .encuentro-switch input {
                height: 0;
                opacity: 0;
                width: 0;
            }

            .encuentro-switch-slider {
                background: #c7cede;
                border-radius: 999px;
                cursor: pointer;
                inset: 0;
                position: absolute;
                transition: .2s;
            }

            .encuentro-switch-slider::before {
                background: #fff;
                border-radius: 50%;
                bottom: 3px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .25);
                content: '';
                height: 20px;
                left: 3px;
                position: absolute;
                transition: .2s;
                width: 20px;
            }

            .encuentro-switch input:checked + .encuentro-switch-slider {
                background: var(--success);
            }

            .encuentro-switch input:checked + .encuentro-switch-slider::before {
                transform: translateX(20px);
            }

            .encuentro-pagination {
                display: flex;
                gap: 8px;
                justify-content: center;
                margin: 22px 0 0;
            }
        </style>
    @endslot

    @if(!empty($message))
        <div class="alert alert-success" role="alert">{{ $message }}</div>
    @endif

    @if(!empty($error))
        <div class="alert alert-danger" role="alert">{{ $error }}</div>
    @endif

    <div class="encuentro-page">
        <h1>Encuentro de Graduados</h1>
        <p class="encuentro-subtitle">
            {{ number_format($total) }} registro(s) guardado(s) en esta encuesta.
        </p>

        <div class="encuentro-card">
            <div class="encuentro-config-bar">
                <div class="encuentro-config-info">
                    <span class="encuentro-config-label">Año activo del Encuentro</span>
                    <span class="encuentro-config-badge">{{ $anioActivo }}</span>
                </div>
                <form method="POST" action="/app/controllers/set-encuentro-anio.php" class="encuentro-config-form">
                    <label class="encuentro-switch" title="Sincronizar con el año actual ({{ date('Y') }})">
                        <input type="checkbox" onchange="this.form.submit()"
                               {{ (int) $anioActivo === (int) date('Y') ? 'checked' : '' }}>
                        <span class="encuentro-switch-slider"></span>
                    </label>
                    <span class="encuentro-config-hint">Usar año actual ({{ date('Y') }})</span>
                </form>
            </div>

            <form method="GET" class="encuentro-search">
                <input type="text" name="search" value="{{ $search }}"
                       class="form-control"
                       placeholder="Buscar por cédula o nombre">
                <select name="anio" class="form-control" style="max-width: 160px;">
                    <option value="">Todos los años</option>
                    @foreach($anios as $anioOption)
                        <option value="{{ $anioOption }}" {{ $anio === $anioOption ? 'selected' : '' }}>
                            {{ $anioOption }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a class="btn btn-success"
                   href="?export=excel&amp;search={{ urlencode($search) }}&amp;anio={{ urlencode($anio) }}">
                    Descargar Excel
                </a>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm encuentro-table">
                    <thead>
                    <tr>
                        @foreach($primaryColumns as $column)
                            <th>{{ $column['label'] }}</th>
                        @endforeach
                        <th>Ver respuestas</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($encuentroAnswers as $answer)
                        <tr>
                            @foreach($primaryColumns as $column)
                                <td>{{ $answer['display_answers'][$column['key']] ?? '' }}</td>
                            @endforeach
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#answersModal{{ $answer['row_key'] }}">
                                    Ver respuestas
                                </button>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="{{ count($primaryColumns) + 1 }}" class="text-center text-muted py-4">
                                No hay registros de graduados ni acompañantes.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @foreach($encuentroAnswers as $answer)
                 <div class="modal fade answers-modal" id="answersModal{{ $answer['row_key'] }}" tabindex="-1"
                     aria-labelledby="answersModalLabel{{ $answer['row_key'] }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="answersModalLabel{{ $answer['row_key'] }}">
                                    Respuestas de {{ $answer['display_answers']['nombres'] ?? '' }}
                                    {{ $answer['display_answers']['apellidos'] ?? '' }}
                                    ({{ $answer['source_type'] }})
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="answers-list">
                                    @foreach($questionColumns as $column)
                                        @if(array_key_exists($column['key'], $answer['survey_answers']))
                                            <div class="answer-item">
                                                <div class="answer-question">{{ $column['label'] }}</div>
                                                <div class="answer-value">
                                                    {{ $answer['display_answers'][$column['key']] ?? '' ?: 'Sin respuesta' }}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($totalPages > 1)
                <nav aria-label="Paginación de resultados">
                    <div class="encuentro-pagination">
                        @if($page > 1)
                            <a class="btn btn-outline-primary btn-sm"
                               href="?page={{ $page - 1 }}&search={{ urlencode($search) }}&anio={{ urlencode($anio) }}">Anterior</a>
                        @endif
                        <span class="align-self-center text-muted small">
                            Página {{ $page }} de {{ $totalPages }}
                        </span>
                        @if($page < $totalPages)
                            <a class="btn btn-outline-primary btn-sm"
                               href="?page={{ $page + 1 }}&search={{ urlencode($search) }}&anio={{ urlencode($anio) }}">Siguiente</a>
                        @endif
                    </div>
                </nav>
            @endif
        </div>
    </div>

@endcomponent
