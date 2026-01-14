@component('templates.main')

    @slot('title')
        Registros pendientes de sincronización
    @endslot

    {{-- HEADER --}}
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
                vertical-align: middle;
                text-align: center;
            }
        </style>
    @endslot

    {{-- TOAST --}}
    @if(isset($message))
        <div class="toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-2"
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">{{ $message }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    <div class="page-scroll">

        <h1 class="text-center mb-4">Registros pendientes de sincronización</h1>

        {{-- BUSCADOR GLOBAL --}}
        <form method="GET" class="mb-4 d-flex justify-content-center">
            <input
                type="text"
                name="search"
                value="{{ $search ?? '' }}"
                class="form-control w-50"
                placeholder="Buscar por cédula, nombre, correo, ciudad..."
            >
            <button class="btn btn-primary ms-2">Buscar</button>
        </form>

        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Cédula</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Tel. alterno</th>
                <th>País</th>
                <th>Ciudad</th>
                <th>Dirección</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
            </thead>

            <tbody>
            @foreach($graduatedAnswers as $answer)
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
                        <form action="/app/controllers/resynchronize.php" method="POST" class="mb-1">
                            <input type="hidden" name="id" value="{{ $answer['id'] }}">
                            <input type="hidden" name="identification_number"
                                   value="{{ $answer['identification_number'] }}">
                            <button class="btn btn-primary btn-sm w-100">
                                Sincronizar
                            </button>
                        </form>

                        <form action="/app/controllers/deny.php"
                              method="POST"
                              onsubmit="return confirm('¿Estás seguro?')">
                            <input type="hidden" name="id" value="{{ $answer['id'] }}">
                            <button class="btn btn-danger btn-sm w-100">
                                Rechazar
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{-- PAGINACIÓN --}}
        <nav class="d-flex justify-content-center mt-4">
            <ul class="pagination">
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

@endcomponent
