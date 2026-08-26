@component('templates.main')

    @slot('title')
        Historial de actualizaciones SIGA
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
            }
            .badge-graduado   { background: #198754; color: #fff; }
            .badge-nograduado { background: #6c757d; color: #fff; }
=======
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');

        :root {
            --primary:#1A3A6B; --primary-dark:#0F2747; --primary-light:#E8EEF7;
            --success:#16a672; --success-light:#e4f7f0; --danger:#ef4444;
            --text:#1f2333; --text-muted:#7c8093; --border:#e6e5f1;
            --surface:#fff; --background:#f6f6fb;
            --shadow:0 20px 45px -20px rgba(26, 58, 107,.35);
        }
        .migrated-page {
            min-height:100vh; padding:40px 16px 60px;
            background:radial-gradient(circle at 12% 8%,rgba(26, 58, 107,.18),transparent 40%),
                       radial-gradient(circle at 88% 92%,rgba(22,166,114,.14),transparent 42%),
                       var(--background);
            color:var(--text); font-family:'Manrope',-apple-system,BlinkMacSystemFont,sans-serif;
        }
        .migrated-container { width:100%; max-width:1500px; margin:0 auto; }
        .migrated-header { text-align:center; margin-bottom:28px; }
        .migrated-header h1 { color:var(--text); font-size:1.9rem; font-weight:800; letter-spacing:-.02em; margin:0 0 8px; }
        .migrated-header p { color:var(--text-muted)!important; margin:0; font-size:.95rem; }
        .migrated-card {
            position:relative; overflow:hidden; background:#fff;
            border:1px solid rgba(26, 58, 107,.12); border-radius:26px;
            padding:28px; box-shadow:var(--shadow);
        }
        .migrated-card:before {
            content:''; position:absolute; top:-60px; right:-60px; width:160px; height:160px;
            border-radius:50%; background:radial-gradient(circle,rgba(26, 58, 107,.12),transparent 70%);
            pointer-events:none;
        }
        .migrated-search {
            position:relative; z-index:1; display:flex; align-items:center; gap:10px;
            max-width:760px; margin:0 auto 24px;
        }
        .migrated-search input {
            flex:1; width:100%; min-width:0; padding:13px 16px;
            border:2px solid var(--border); border-radius:14px; font-size:.95rem;
            font-family:inherit; color:var(--text); background:#fbfbfe;
            transition:border-color .2s,box-shadow .2s,background .2s;
        }
        .migrated-search input:focus {
            outline:none; border-color:var(--primary); background:#fff;
            box-shadow:0 0 0 4px rgba(26, 58, 107,.18);
        }
        .migrated-search button {
            flex-shrink:0; border:none; border-radius:13px;
            background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            color:#fff; padding:13px 24px; font-size:14px; font-weight:700;
            font-family:inherit; cursor:pointer; box-shadow:0 8px 18px -8px rgba(26, 58, 107,.5);
            transition:filter .2s,transform .1s;
        }
        .migrated-search button:hover { filter:brightness(1.05); }
        .migrated-search button:active { transform:scale(.97); }
        .migrated-table-wrap { position:relative; z-index:1; overflow-x:auto; width:100%; border-radius:16px; }
        .migrated-table {
            width:100%; min-width:1200px; margin:0!important;
            border-collapse:separate!important; border-spacing:0; font-family:inherit;
            color:var(--text); font-size:.88rem;
        }
        .migrated-table thead th {
            background:var(--primary)!important; color:#fff!important;
            border:0!important; border-bottom:2px solid rgba(26, 58, 107,.25)!important;
            padding:14px 12px!important; font-size:12px; font-weight:800;
            text-transform:uppercase; letter-spacing:.03em; white-space:nowrap;
            text-align:center; vertical-align:middle;
        }
        .migrated-table thead th:first-child { border-radius:12px 0 0 0; }
        .migrated-table thead th:last-child { border-radius:0 12px 0 0; }
        .migrated-table tbody td {
            padding:14px 12px!important; border-bottom:1px solid #f0f0f7!important;
            border-right:0!important; background:#fff; color:var(--text);
            font-size:13px; white-space:nowrap; text-align:center; vertical-align:middle;
        }
        .migrated-table tbody tr:hover td { background:#fafafd; }
        .migrated-table tbody tr:last-child td { border-bottom:none!important; }
        .migrated-table .text-muted { color:var(--text-muted)!important; }
        .migrated-table strong { font-weight:800; }
        .badge-graduado,.badge-nograduado {
            display:inline-flex; align-items:center; justify-content:center; min-width:96px;
            padding:6px 12px; border-radius:999px; font-size:12px; font-weight:700;
        }
        .badge-graduado { background:var(--success-light)!important; color:var(--success)!important; }
        .badge-nograduado { background:#f1f1f8!important; color:var(--text-muted)!important; }
        .migrated-table .bg-warning {
            background:var(--primary-light)!important; color:var(--primary-dark)!important;
            border-radius:999px; padding:6px 12px; font-size:12px; font-weight:700;
        }
        .migrated-pagination { position:relative; z-index:1; margin-top:28px; }
        .migrated-pagination .pagination { display:flex; justify-content:center; gap:4px; margin:0; }
        .migrated-pagination .page-link {
            min-width:38px; height:38px; display:inline-flex; align-items:center; justify-content:center;
            padding:0 12px; border:2px solid var(--border); border-radius:11px!important;
            background:#fff; color:var(--text); font-size:13.5px; font-weight:700;
            font-family:inherit; box-shadow:none; transition:all .15s;
        }
        .migrated-pagination .page-link:hover {
            border-color:var(--primary); color:var(--primary-dark); background:var(--primary-light);
        }
        .migrated-pagination .page-item.active .page-link {
            border-color:transparent; background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            color:#1f2333; box-shadow:0 6px 14px -6px rgba(26, 58, 107,.5);
        }
        .migrated-pagination .page-item.disabled .page-link { opacity:.4; pointer-events:none; }
        .migrated-empty { color:var(--text-muted)!important; padding:48px 20px!important; font-size:14px; font-weight:600; }
        .migrated-toast {
            border-radius:14px!important; box-shadow:0 15px 35px -15px rgba(31,35,51,.35);
            overflow:hidden; font-family:'Manrope',sans-serif; font-weight:600;
        }
        @media(max-width:768px){
            .migrated-page{padding:28px 12px 40px}.migrated-header h1{font-size:1.5rem}
            .migrated-card{padding:18px;border-radius:20px}.migrated-search{flex-direction:column;align-items:stretch}
            .migrated-search button{width:100%}
        }
        @media(max-width:560px){
            .migrated-page{padding:22px 8px 32px}.migrated-card{padding:14px;border-radius:18px}
            .migrated-header h1{font-size:1.35rem}.migrated-header p{font-size:.85rem}
        }
>>>>>>> Stashed changes
        </style>
    @endslot

    {{-- TOAST --}}
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

        <h1 class="text-center mb-1">Historial de actualizaciones SIGA</h1>
        <p class="text-center text-muted mb-4">
            {{ number_format($total) }} registro(s) ya enviados a SIGA.
        </p>

        {{-- BUSCADOR --}}
        <form method="GET" class="mb-4 d-flex justify-content-center">
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   class="form-control w-50"
                   placeholder="Buscar por cédula, nombre, correo, ciudad...">
            <button class="btn btn-primary ms-2">Buscar</button>
        </form>

        <table class="table table-striped table-hover table-sm">
            <thead class="table-dark">
            <tr>
                <th>#ID</th>
                <th>Cédula</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Ciudad</th>
                <th>Estado SIGA</th>
                <th>Actualizado por</th>
                <th>Fecha actualización</th>
            </tr>
            </thead>

            <tbody>
            @forelse($migratedAnswers as $row)
                <tr>
                    <td class="text-muted small">{{ $row['id'] }}</td>
                    <td><strong>{{ $row['identification_number'] }}</strong></td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['last_name'] }}</td>
                    <td>{{ $row['email'] ?: '—' }}</td>
                    <td>{{ $row['mobile_phone'] ?: '—' }}</td>
                    <td>{{ $row['city'] ?: '—' }}</td>
                    <td>
                        @if($row['is_graduated'] == 1)
                            <span class="badge badge-graduado">Graduado</span>
                        @elseif($row['is_graduated'] == 0)
                            <span class="badge badge-nograduado">No graduado</span>
                        @else
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        @endif
                    </td>
                    <td>{{ $row['migrated_by_name'] ?: '—' }}</td>
                    <td>{{ $row['updated_at'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        No hay registros actualizados en SIGA aún.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{-- PAGINACIÓN --}}
        @if($totalPages > 1)
        <nav class="d-flex justify-content-center mt-4">
            <ul class="pagination">
                <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="?page={{ $page - 1 }}&search={{ $search }}">«</a>
                </li>

                @for($i = max(1, $page - 3); $i <= min($totalPages, $page + 3); $i++)
                    <li class="page-item {{ $i == $page ? 'active' : '' }}">
                        <a class="page-link" href="?page={{ $i }}&search={{ $search }}">{{ $i }}</a>
                    </li>
                @endfor

                <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                    <a class="page-link" href="?page={{ $page + 1 }}&search={{ $search }}">»</a>
                </li>
            </ul>
        </nav>
        @endif

    </div>

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
