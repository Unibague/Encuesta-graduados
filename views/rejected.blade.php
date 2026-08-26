@component('templates.main')

    @slot('title')
        Registros rechazados
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
            --shadow: 0 20px 45px -20px rgba(26, 58, 107, 0.35);
        }

        .rejected-shell {
            min-height: 100vh;
            padding: 40px 16px 60px;
            background:
                radial-gradient(circle at 12% 8%, rgba(26, 58, 107, 0.18), transparent 40%),
                radial-gradient(circle at 88% 92%, rgba(22, 166, 114, 0.14), transparent 42%),
                #f6f6fb;
            display: flex;
            justify-content: center;
        }

        .rejected-container {
            max-width: 1500px;
            width: 100%;
        }

        .rejected-intro {
            text-align: center;
            margin-bottom: 28px;
        }

        .rejected-intro h1 {
            font-size: 1.9rem;
            font-weight: 800;
            margin: 0 0 8px;
            letter-spacing: -0.02em;
            color: var(--text);
        }

        .rejected-intro p {
            color: var(--text-muted);
            margin: 0;
            font-size: 0.95rem;
        }

        .rejected-card {
            background: #ffffff;
            border-radius: 26px;
            padding: 32px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(26, 58, 107, 0.12);
            position: relative;
            overflow: hidden;
        }

        .rejected-card::before {
            content: '';
            position: absolute;
            top: -70px;
            right: -70px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(26, 58, 107, 0.12), transparent 70%);
            pointer-events: none;
        }

        .table-wrap {
            position: relative;
            z-index: 1;
            overflow-x: auto;
            width: 100%;
            border-radius: 16px;
        }

        .rejected-table {
            min-width: 1500px;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.88rem;
        }

        .rejected-table thead th {
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

        .rejected-table thead th:first-child {
            border-radius: 12px 0 0 0;
        }

        .rejected-table thead th:last-child {
            border-radius: 0 12px 0 0;
        }

        .rejected-table tbody td {
            padding: 14px 12px;
            border-bottom: 1px solid #f0f0f7;
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
            color: var(--text);
        }

        .rejected-table tbody tr {
            transition: background 0.15s ease;
        }

        .rejected-table tbody tr:hover {
            background: #fafafd;
        }

        .rejected-table tbody tr:last-child td {
            border-bottom: none;
        }

        .rejected-table tbody td:nth-child(2) {
            font-weight: 800;
        }

        .actions-cell {
            display: flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
            flex-wrap: nowrap;
        }

        .btn-action {
            border: none;
            border-radius: 10px;
            padding: 9px 14px;
            font-weight: 700;
            font-size: 12.5px;
            font-family: inherit;
            cursor: pointer;
            transition: filter 0.2s, transform 0.1s, box-shadow 0.2s;
        }

        .btn-action:active {
            transform: scale(0.97);
        }

        .btn-reactivate {
            background: linear-gradient(135deg, var(--success), #12855e);
            color: #fff;
            box-shadow: 0 6px 14px -6px rgba(22, 166, 114, 0.45);
        }

        .btn-reactivate:hover {
            filter: brightness(1.06);
        }

        .btn-delete {
            background: #fff;
            color: var(--danger);
            border: 2px solid #fecaca;
        }

        .btn-delete:hover {
            background: var(--danger-light);
            border-color: var(--danger);
        }

        .empty-state {
            text-align: center !important;
            padding: 48px 20px !important;
            color: var(--text-muted) !important;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .rejected-pagination {
            display: flex;
            justify-content: center;
            margin-top: 28px;
            gap: 4px;
            list-style: none;
            padding: 0;
        }

        .rejected-pagination .page-link {
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

        .rejected-pagination .page-link:hover {
            border-color: var(--primary);
            color: var(--primary-dark);
            background: var(--primary-light);
        }

        .rejected-pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-color: transparent;
            color: #1f2333;
            box-shadow: 0 6px 14px -6px rgba(26, 58, 107, 0.5);
        }

        .rejected-pagination .page-item.disabled .page-link {
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

        @media (max-width: 700px) {
            .rejected-shell {
                padding: 28px 12px 40px;
            }

            .rejected-card {
                padding: 22px 16px;
                border-radius: 20px;
            }

            .rejected-intro h1 {
                font-size: 1.6rem;
            }
        }
        </style>
    @endslot

    {{-- =========================
         TOASTS
         ========================= --}}
    @if(!empty($message))
        <div class="toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-3"
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ $message }}
                </div>
                <button type="button"
                        class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    @if(!empty($error))
        <div class="toast align-items-center text-bg-danger border-0 position-fixed top-0 end-0 m-3"
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ $error }}
                </div>
                <button type="button"
                        class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    <div class="rejected-shell">
        <div class="rejected-container">

            <div class="rejected-intro">
                <h1>Registros rechazados</h1>
                <p>Estos registros fueron rechazados y pueden ser reactivados o eliminados.</p>
            </div>

            <div class="rejected-card">
                <div class="table-wrap">
                    <table class="rejected-table">
                        <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Correo electrónico</th>
                            <th>Teléfono</th>
                            <th>Teléfono alterno</th>
                            <th>País</th>
                            <th>Ciudad</th>
                            <th>Dirección</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($rejectedAnswers as $answer)
                            <tr>
                                <td>{{ $answer['id'] }}</td>
                                <td>{{ $answer['identification_number'] }}</td>
                                <td>{{ $answer['name'] }}</td>
                                <td>{{ $answer['last_name'] }}</td>
                                <td>{{ $answer['email'] }}</td>
                                <td>{{ $answer['mobile_phone'] }}</td>
                                <td>{{ $answer['alternative_mobile_phone'] ?: '—' }}</td>
                                <td>{{ $answer['country'] }}</td>
                                <td>{{ $answer['city'] }}</td>
                                <td>{{ $answer['address'] }}</td>
                                <td>{{ $answer['created_at'] }}</td>
                                <td>
                                    <div class="actions-cell">

                                        {{-- REACTIVAR --}}
                                        <form action="/app/controllers/reactive.php"
                                              method="POST"
                                              onsubmit="return confirm('¿Deseas reactivar este registro?')">
                                            <input type="hidden" name="id" value="{{ $answer['id'] }}">
                                            <button class="btn-action btn-reactivate">
                                                Reactivar
                                            </button>
                                        </form>

                                        {{-- BORRAR --}}
                                        <form action="/app/controllers/delete.php"
                                              method="POST"
                                              onsubmit="return confirm('Este registro será borrado definitivamente')">
                                            <input type="hidden" name="id" value="{{ $answer['id'] }}">
                                            <button class="btn-action btn-delete">
                                                Borrar
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="empty-state">
                                    No hay registros rechazados
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINACIÓN --}}
                <nav>
                    <ul class="rejected-pagination">
                        <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                            <a class="page-link" href="?page={{ $page - 1 }}">«</a>
                        </li>

                        @for($i = max(1,$page-3); $i <= min($totalPages,$page+3); $i++)
                            <li class="page-item {{ $i == $page ? 'active' : '' }}">
                                <a class="page-link" href="?page={{ $i }}">{{ $i }}</a>
                            </li>
                        @endfor

                        <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                            <a class="page-link" href="?page={{ $page + 1 }}">»</a>
                        </li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>


         SCRIPTS
         ========================= --}}
    @slot('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.toast').forEach(el => {
                    new bootstrap.Toast(el, { delay: 5000 }).show();
                });
            });
        </script>
    @endslot

@endcomponent