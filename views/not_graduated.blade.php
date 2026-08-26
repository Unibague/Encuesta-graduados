@component('templates.main')

    @slot('title')
        Registros en SIGA (No graduados)
    @endslot

    {{-- HEADER SLOT --}}
    @slot('header')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            :root {
                --primary: #1A3A6B;
                --primary-dark: #0F2747;
                --primary-light: #E8EEF7;
                --success: #16a672;
                --success-light: #e4f7f0;
                --danger: #ef4444;
                --danger-light: #fef2f2;
                --text: #1f2333;
                --text-muted: #7c8093;
                --border: #e6e5f1;
                --shadow: 0 20px 45px -20px rgba(26, 58, 107, 0.25);
            }

            .ready-page {
                font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
                color: var(--text);
                padding: 10px 0 40px;
            }

            .ready-page h1 {
                font-size: 1.75rem;
                font-weight: 800;
                letter-spacing: -0.02em;
                margin: 0 0 6px;
                text-align: center;
            }

            .ready-subtitle {
                text-align: center;
                color: var(--text-muted);
                font-size: 0.95rem;
                margin-bottom: 28px;
                max-width: 560px;
                margin-left: auto;
                margin-right: auto;
            }

            .ready-card {
                background: #ffffff;
                border-radius: 22px;
                padding: 28px 24px;
                box-shadow: var(--shadow);
                border: 1px solid rgba(26, 58, 107, 0.12);
            }

            .search-wrap {
                display: flex;
                justify-content: center;
                gap: 10px;
                margin-bottom: 24px;
                flex-wrap: wrap;
            }

            .search-wrap input {
                max-width: 420px;
                width: 100%;
                padding: 12px 16px;
                border: 2px solid var(--border);
                border-radius: 14px;
                font-size: 0.95rem;
                font-family: inherit;
                background: #fbfbfe;
                color: var(--text);
                transition: border-color 0.2s, box-shadow 0.2s;
            }

            .search-wrap input:focus {
                outline: none;
                border-color: var(--primary);
                background: #fff;
                box-shadow: 0 0 0 4px rgba(26, 58, 107, 0.18);
            }

            .search-wrap input::placeholder {
                color: #b3b4c6;
            }

            .btn-search {
                background: linear-gradient(135deg, var(--primary), var(--primary-dark));
                color: #1f2333;
                border: none;
                border-radius: 13px;
                padding: 12px 22px;
                font-weight: 700;
                font-size: 14px;
                font-family: inherit;
                cursor: pointer;
                box-shadow: 0 8px 18px -8px rgba(26, 58, 107, 0.5);
                transition: filter 0.2s, transform 0.1s;
            }

            .btn-search:hover {
                filter: brightness(1.05);
            }

            .btn-search:active {
                transform: scale(0.97);
            }

            /* Tabla */
            .page-scroll {
                overflow-x: auto;
                width: 100%;
                border-radius: 16px;
            }

            .ready-table {
                min-width: 1500px;
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                font-size: 0.88rem;
            }

            .ready-table thead th {
                background: var(--primary-light);
                color: var(--primary-dark);
                font-weight: 800;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                padding: 14px 12px;
                border-bottom: 2px solid rgba(26, 58, 107, 0.25);
                white-space: nowrap;
                text-align: center;
                vertical-align: middle;
            }

            .ready-table thead th:first-child {
                border-radius: 12px 0 0 0;
            }

            .ready-table thead th:last-child {
                border-radius: 0 12px 0 0;
            }

            .ready-table tbody td {
                padding: 14px 12px;
                border-bottom: 1px solid #f0f0f7;
                white-space: nowrap;
                text-align: center;
                vertical-align: middle;
                height: auto;
                color: var(--text);
            }

            .ready-table tbody tr:hover {
                background: #fafafd;
            }

            .ready-table tbody tr:last-child td {
                border-bottom: none;
            }

            .cell-check {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 6px;
            }

            .cell-check span {
                font-weight: 600;
                color: var(--text);
                white-space: normal;
                word-break: break-word;
            }

            .ready-table input[type="checkbox"] {
                accent-color: var(--primary);
                width: 16px;
                height: 16px;
                cursor: pointer;
            }

            .btn-approve {
                background: linear-gradient(135deg, var(--success), #12855e) !important;
                color: #fff !important;
                border: none !important;
                border-radius: 10px !important;
                padding: 8px 14px !important;
                font-weight: 700 !important;
                font-size: 12.5px !important;
                font-family: inherit !important;
                box-shadow: 0 6px 14px -6px rgba(22, 166, 114, 0.45);
                transition: filter 0.2s, transform 0.1s;
            }

            .btn-approve:hover {
                filter: brightness(1.06);
            }

            .btn-approve:disabled {
                opacity: 0.75;
                cursor: not-allowed;
            }

            .btn-deny {
                background: #fff !important;
                color: var(--danger) !important;
                border: 2px solid #fecaca !important;
                border-radius: 10px !important;
                padding: 7px 14px !important;
                font-weight: 700 !important;
                font-size: 12.5px !important;
                font-family: inherit !important;
                transition: background 0.2s, border-color 0.2s;
            }

            .btn-deny:hover {
                background: var(--danger-light) !important;
                border-color: var(--danger) !important;
            }

            .actions-cell {
                display: flex;
                gap: 8px;
                justify-content: center;
                align-items: center;
                flex-wrap: nowrap;
            }

            .empty-state {
                text-align: center;
                padding: 48px 20px !important;
                color: var(--text-muted) !important;
                font-weight: 600;
                font-size: 0.95rem;
            }

            .ready-pagination {
                display: flex;
                justify-content: center;
                margin-top: 28px;
                gap: 4px;
                list-style: none;
                padding: 0;
            }

            .ready-pagination .page-item .page-link {
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 38px;
                height: 38px;
                padding: 0 12px;
                border-radius: 11px;
                border: 2px solid var(--border);
                background: #fff;
                color: var(--text);
                font-weight: 700;
                font-size: 13.5px;
                text-decoration: none;
                transition: all 0.15s;
                font-family: inherit;
            }

            .ready-pagination .page-item .page-link:hover {
                border-color: var(--primary);
                color: var(--primary-dark);
                background: var(--primary-light);
            }

            .ready-pagination .page-item.active .page-link {
                background: linear-gradient(135deg, var(--primary), var(--primary-dark));
                border-color: transparent;
                color: #1f2333;
                box-shadow: 0 6px 14px -6px rgba(26, 58, 107, 0.5);
            }

            .ready-pagination .page-item.disabled .page-link {
                opacity: 0.4;
                pointer-events: none;
            }

            .toast.text-bg-success {
                background: var(--success) !important;
                border-radius: 14px !important;
                font-family: 'Manrope', sans-serif;
                font-weight: 600;
            }

            .toast.text-bg-danger {
                background: var(--danger) !important;
                border-radius: 14px !important;
                font-family: 'Manrope', sans-serif;
                font-weight: 600;
            }
        </style>
    @endslot

    {{-- TOASTS --}}
    @if(!empty($message))
        <div class="toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-3">
            <div class="d-flex">
                <div class="toast-body">{{ $message }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    <div class="ready-page">

        <h1>Registros en SIGA (No graduados)</h1>

        <p class="ready-subtitle">
            Estos registros existen en SIGA, pero no están marcados como graduados.
            Puedes actualizarlos o rechazarlos.
        </p>

        <div class="ready-card">

            {{-- BUSCADOR --}}
            <form method="GET" class="search-wrap">
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Buscar por cédula, nombre, correo, ciudad...">
                <button type="submit" class="btn-search">Buscar</button>
            </form>

            <div class="page-scroll">
                <table class="ready-table">
                    <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Teléfono alterno</th>
                        <th>Ciudad</th>
                        <th>Dirección</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($graduatedAnswers as $answer)
                        <tr>
                            <td>{{ $answer['id'] }}</td>
                            <td>{{ $answer['identification_number'] }}</td>
                            <td>{{ $answer['name'] }}</td>
                            <td>{{ $answer['last_name'] }}</td>

                            <td>
                                <div class="cell-check">
                                    <span>{{ $answer['email'] }}</span>
                                    <input type="checkbox" class="select" name="email"
                                           value="{{ $answer['email'] }}"
                                           data-row="{{ $answer['id'] }}" checked>
                                </div>
                            </td>

                            <td>
                                <div class="cell-check">
                                    <span>{{ $answer['mobile_phone'] }}</span>
                                    <input type="checkbox" class="select" name="mobile_phone"
                                           value="{{ $answer['mobile_phone'] }}"
                                           data-row="{{ $answer['id'] }}" checked>
                                </div>
                            </td>

                            <td>
                                <div class="cell-check">
                                    <span>{{ $answer['alternative_mobile_phone'] ?: '—' }}</span>
                                    <input type="checkbox" class="select" name="alternative_mobile_phone"
                                           value="{{ $answer['alternative_mobile_phone'] }}"
                                           data-row="{{ $answer['id'] }}" checked>
                                </div>
                            </td>

                            <td>
                                <div class="cell-check">
                                    <span>{{ $answer['city'] }}</span>
                                    <input type="checkbox" class="select" name="city"
                                           value="{{ $answer['city'] }}"
                                           data-row="{{ $answer['id'] }}" checked>
                                </div>
                            </td>

                            <td>
                                <div class="cell-check">
                                    <span>{{ $answer['address'] }}</span>
                                    <input type="checkbox" class="select" name="address"
                                           value="{{ $answer['address'] }}"
                                           data-row="{{ $answer['id'] }}" checked>
                                </div>
                            </td>

                            <td>{{ $answer['created_at'] }}</td>

                            <td>
                                <div class="actions-cell">
                                    {{-- ACTUALIZAR --}}
                                    <form action="/app/controllers/approve.php"
                                          method="POST"
                                          onsubmit="return approve({{ $answer['id'] }})"
                                          id="form-{{ $answer['id'] }}">
                                        <input type="hidden" name="id" value="{{ $answer['id'] }}">
                                        <input type="hidden" name="identification_number"
                                               value="{{ $answer['identification_number'] }}">
                                        <button
                                            type="submit"
                                            class="btn btn-approve"
                                            data-loading-text="Actualizando...">
                                            <span class="btn-text">Actualizar</span>
                                            <span class="spinner-border spinner-border-sm d-none ms-1"
                                                  role="status"
                                                  aria-hidden="true"></span>
                                        </button>
                                    </form>

                                    {{-- RECHAZAR --}}
                                    <form action="/app/controllers/deny.php"
                                          method="POST"
                                          onsubmit="return confirm('¿Deseas rechazar este registro?')">
                                        <input type="hidden" name="id" value="{{ $answer['id'] }}">
                                        <button class="btn btn-deny">
                                            Rechazar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="empty-state">
                                No hay registros para mostrar
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}
            <nav class="d-flex justify-content-center mt-4">
                <ul class="pagination ready-pagination">
                    <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="?page={{ $page - 1 }}&search={{ $search }}">«</a>
                    </li>

                    @for($i = max(1,$page-3); $i <= min($totalPages,$page+3); $i++)
                        <li class="page-item {{ $i == $page ? 'active' : '' }}">
                            <a class="page-link" href="?page={{ $i }}&search={{ $search }}">{{ $i }}</a>
                        </li>
                    @endfor

                    <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="?page={{ $page + 1 }}&search={{ $search }}">»</a>
                    </li>
                </ul>
            </nav>

        </div>

    </div>

  @slot('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // TOASTS
        document.querySelectorAll('.toast').forEach(el => {
            new bootstrap.Toast(el, { delay: 5000 }).show();
        });

        // LOADING PARA ACTUALIZAR
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function () {

                const btn = form.querySelector('.btn-approve');
                if (!btn) return; 
                btn.disabled = true;

                btn.querySelector('.btn-text').textContent =
                    btn.dataset.loadingText || 'Cargando...';

                btn.querySelector('.spinner-border').classList.remove('d-none');
            });
        });
    });

    function approve(id) {
        const checks = [...document.getElementsByClassName('select')]
            .filter(c => c.dataset.row == id && c.checked);

        const form = document.getElementById('form-' + id);

        checks.forEach(c => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = c.name;
            input.value = c.value;
            form.appendChild(input);
        });

        return true;
    }
</script>
@endslot

@endcomponent