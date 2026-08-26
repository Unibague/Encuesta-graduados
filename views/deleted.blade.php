@component('templates.main')

    @slot('title')
        Registros borrados
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

        .deleted-page {
            min-height: 100vh;
            padding: 40px 16px 60px;
            background:
                radial-gradient(circle at 12% 8%, rgba(252, 189, 0, 0.18), transparent 40%),
                radial-gradient(circle at 88% 92%, rgba(22, 166, 114, 0.14), transparent 42%),
                var(--background);
            color: var(--text);
            font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .deleted-container {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
        }

        .deleted-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .deleted-header h1 {
            color: var(--text);
            font-size: 1.9rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0 0 8px;
        }

        .deleted-header p {
            color: var(--text-muted) !important;
            margin: 0;
            font-size: 0.95rem;
        }

        .deleted-card {
            position: relative;
            overflow: hidden;
            background: var(--surface);
            border: 1px solid rgba(252, 189, 0, 0.12);
            border-radius: 26px;
            padding: 28px;
            box-shadow: var(--shadow);
        }

        .deleted-card::before {
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

        .deleted-table-wrap {
            position: relative;
            z-index: 1;
            overflow-x: auto;
            width: 100%;
            border-radius: 16px;
            -webkit-overflow-scrolling: touch;
        }

        .deleted-table {
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

        .deleted-table thead th {
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

        .deleted-table thead tr:first-child th:first-child {
            border-radius: 12px 0 0 0;
        }

        .deleted-table thead tr:first-child th:last-child {
            border-radius: 0 12px 0 0;
        }

        .deleted-table tbody td {
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

        .deleted-table tbody tr:hover td {
            background: #fafafd;
        }

        .deleted-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        .deleted-table strong {
            font-weight: 800;
        }

        .deleted-table .btn-restore {
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--success), #0f8a5f);
            color: #fff;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 700;
            font-family: inherit;
            box-shadow: 0 6px 14px -6px rgba(22, 166, 114, 0.5);
            transition: background 0.15s, transform 0.1s;
        }

        .deleted-table .btn-restore:hover {
            background: linear-gradient(135deg, #1bb87f, #0d7a54);
            color: #fff;
            transform: translateY(-1px);
        }

        .deleted-pagination {
            position: relative;
            z-index: 1;
            margin-top: 28px;
        }

        .deleted-pagination .pagination {
            display: flex;
            justify-content: center;
            gap: 4px;
            margin: 0;
        }

        .deleted-pagination .page-link {
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

        .deleted-pagination .page-link:hover {
            border-color: var(--primary);
            color: var(--primary-dark);
            background: var(--primary-light);
        }

        .deleted-pagination .page-item.active .page-link {
            border-color: transparent;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #1f2333;
            box-shadow: 0 6px 14px -6px rgba(252, 189, 0, 0.5);
        }

        .deleted-pagination .page-item.disabled .page-link {
            opacity: 0.4;
            pointer-events: none;
        }

        .deleted-toast {
            border-radius: 14px !important;
            box-shadow: 0 15px 35px -15px rgba(31, 35, 51, 0.35);
            overflow: hidden;
            font-family: 'Manrope', sans-serif;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .deleted-page {
                padding: 28px 12px 40px;
            }

            .deleted-header h1 {
                font-size: 1.5rem;
            }

            .deleted-card {
                padding: 18px;
                border-radius: 20px;
            }
        }

        @media (max-width: 560px) {
            .deleted-page {
                padding: 22px 8px 32px;
            }

            .deleted-card {
                padding: 14px;
                border-radius: 18px;
            }

            .deleted-header h1 {
                font-size: 1.35rem;
            }

            .deleted-header p {
                font-size: 0.85rem;
            }
        }
        </style>
    @endslot

    @if(!empty($message))
        <div class="toast deleted-toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-3"
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">{{ $message }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    @if(!empty($error))
        <div class="toast deleted-toast align-items-center text-bg-danger border-0 position-fixed top-0 end-0 m-3"
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">{{ $error }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    <div class="deleted-page">
    <div class="deleted-container">

        <div class="deleted-header">
            <h1>Registros borrados</h1>
            <p>Estos registros fueron eliminados y pueden ser restaurados si es necesario.</p>
        </div>

        <div class="deleted-card">

            <div class="deleted-table-wrap">
                <table class="deleted-table">
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
                    @forelse($deletedAnswers as $answer)
                        <tr>
                            <td><strong>{{ $answer['id'] }}</strong></td>
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
                                <form action="/app/controllers/undelete.php"
                                      method="POST"
                                      onsubmit="return confirm('¿Deseas restaurar este registro?')">
                                    <input type="hidden" name="id" value="{{ $answer['id'] }}">
                                    <button type="submit" class="btn-restore">
                                        Restaurar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                No hay registros borrados
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if(($totalPages ?? 1) > 1)
                <nav class="deleted-pagination">
                    <ul class="pagination">
                        <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                            <a class="page-link" href="?page={{ $page - 1 }}">«</a>
                        </li>

                        @for($i = max(1, $page - 3); $i <= min($totalPages, $page + 3); $i++)
                            <li class="page-item {{ $i == $page ? 'active' : '' }}">
                                <a class="page-link" href="?page={{ $i }}">{{ $i }}</a>
                            </li>
                        @endfor

                        <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                            <a class="page-link" href="?page={{ $page + 1 }}">»</a>
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
