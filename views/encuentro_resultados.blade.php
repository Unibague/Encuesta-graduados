@component('templates.main')

    @slot('title')
        Resultados de Registro de Graduados
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
                gap: 10px;
                justify-content: center;
                margin-bottom: 22px;
            }

            .encuentro-search input {
                max-width: 480px;
            }

            .encuentro-table {
                min-width: 900px !important;
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

            .encuentro-delete {
                align-items: center;
                background: #fee2e2;
                border: 1px solid #fecaca;
                border-radius: 50%;
                color: #b91c1c;
                display: inline-flex;
                font-size: 1.1rem;
                font-weight: 800;
                height: 28px;
                justify-content: center;
                line-height: 1;
                padding: 0;
                width: 28px;
            }

            .encuentro-delete:hover {
                background: #b91c1c;
                color: #fff;
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
        <h1>Resultados de Registro de Graduados</h1>
        <p class="encuentro-subtitle">
            {{ number_format($total) }} registro(s) guardado(s) en esta encuesta.
        </p>

        <div class="encuentro-card">
            <form method="GET" class="encuentro-search">
                <input type="text" name="search" value="{{ $search }}"
                       class="form-control"
                       placeholder="Buscar por cédula o nombre">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm encuentro-table">
                    <thead>
                    <tr>
                        @foreach($primaryColumns as $column)
                            <th>{{ $column['label'] }}</th>
                        @endforeach
                        <th>Tipo</th>
                        <th>Más datos</th>
                        <th>Ocultar</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($encuentroAnswers as $answer)
                        <tr>
                            @foreach($primaryColumns as $column)
                                <td>{{ $answer['display_answers'][$column['key']] ?? '' }}</td>
                            @endforeach
                            <td>{{ $answer['source_type'] }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#answersModal{{ $answer['row_key'] }}">
                                    Ver respuestas
                                </button>
                            </td>
                            <td>
                                  <form method="POST"
                                      action="{{ $answer['source_table'] === 'registroacom_2026' ? '/app/controllers/delete-registroacom.php' : '/app/controllers/delete.php' }}"
                                      onsubmit="return confirm('¿Deseas ocultar este registro?');">
                                    <input type="hidden" name="id" value="{{ $answer['id'] }}">
                                    <button type="submit" class="encuentro-delete"
                                            title="Ocultar registro" aria-label="Ocultar registro">
                                        &times;
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="{{ count($primaryColumns) + 3 }}" class="text-center text-muted py-4">
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
                                        <div class="answer-item">
                                            <div class="answer-question">{{ $column['label'] }}</div>
                                            <div class="answer-value">
                                                {{ $answer['display_answers'][$column['key']] ?? '' ?: 'Sin respuesta' }}
                                            </div>
                                        </div>
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
                               href="?page={{ $page - 1 }}&search={{ urlencode($search) }}">Anterior</a>
                        @endif
                        <span class="align-self-center text-muted small">
                            Página {{ $page }} de {{ $totalPages }}
                        </span>
                        @if($page < $totalPages)
                            <a class="btn btn-outline-primary btn-sm"
                               href="?page={{ $page + 1 }}&search={{ urlencode($search) }}">Siguiente</a>
                        @endif
                    </div>
                </nav>
            @endif
        </div>
    </div>

@endcomponent
