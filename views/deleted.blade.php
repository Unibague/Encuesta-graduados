@component('templates.main')

    @slot('title')
        Registros borrados
    @endslot

    {{-- HEADER SLOT --}}
    @slot('header')
        <style>
            .page-scroll {
                overflow-x: auto;
                width: 100%;
            }

            table {
                min-width: 1400px;
            }

            th, td {
                white-space: nowrap;
                text-align: center;
                vertical-align: middle;
                height: 50px;
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

    <div class="page-scroll">

        <h1 class="text-center mb-2">Registros borrados</h1>
        <p class="text-center text-muted mb-4">
            Estos registros fueron eliminados y pueden ser restaurados si es necesario.
        </p>

        <table class="table table-striped table-hover">
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
                        <form action="/app/controllers/undelete.php"
                              method="POST"
                              onsubmit="return confirm('¿Deseas restaurar este registro?')">
                            <input type="hidden" name="id" value="{{ $answer['id'] }}">
                            <button class="btn btn-success btn-sm">
                                Restaurar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center text-muted">
                        No hay registros borrados
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{-- PAGINACIÓN --}}
        <nav class="d-flex justify-content-center mt-4">
            <ul class="pagination">
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

    {{-- =========================
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
