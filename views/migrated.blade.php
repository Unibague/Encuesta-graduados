@component('templates.main')

    @slot('title')
        Historial de actualizaciones SIGA
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
            }
            .badge-graduado   { background: #198754; color: #fff; }
            .badge-nograduado { background: #6c757d; color: #fff; }
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
