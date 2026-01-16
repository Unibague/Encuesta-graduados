@component('templates.main')

    @slot('title')
        Registros listos para actualizar 
    @endslot

    {{-- HEADER SLOT --}}
    @slot('header')
        <style>
            .page-scroll {
                overflow-x: auto;
                width: 100%;
            }

            table {
                min-width: 1600px;
            }

            th, td {
                white-space: nowrap;
                text-align: center;
                vertical-align: middle;
                height: 50px;
            }
        </style>
    @endslot

    {{-- TOASTS --}}
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

    @if(isset($error))
        <div class="toast align-items-center text-bg-danger border-0 position-fixed top-0 end-0 m-2"
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">{{ $error }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    <div class="page-scroll">

        <h1 class="text-center mb-4">Registros listos para actualizar</h1>

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
                <th>Correo electrónico</th>
                <th>Teléfono</th>
                <th>Teléfono alterno</th>
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

                    <td>
                        <p>{{ $answer['identification_number'] }}</p>
                        <hr>
                        <p>{{ $answer['official_answers']['Numero de identificacion'] ?? '' }}</p>
                    </td>

                    <td>
                        <p>{{ $answer['name'] }}</p>
                        <hr>
                        <p>{{ $answer['official_answers']['Nombres'] ?? '' }}</p>
                    </td>

                    <td>
                        <p>{{ $answer['last_name'] }}</p>
                        <hr>
                        <p>{{ $answer['official_answers']['Apellidos'] ?? '' }}</p>
                    </td>

                    <td>
                        <p>{{ $answer['email'] }}</p>
                        <hr>
                        <p>{{ $answer['official_answers']['Correo'] ?? '' }}</p>
                        <input type="checkbox" class="select" name="email"
                               value="{{ $answer['email'] }}" data-row="{{ $answer['id'] }}" checked>
                    </td>

                    <td>
                        <p>{{ $answer['mobile_phone'] }}</p>
                        <hr>
                        <p>{{ $answer['official_answers']['Telefono de contacto'] ?? '' }}</p>
                        <input type="checkbox" class="select" name="mobile_phone"
                               value="{{ $answer['mobile_phone'] }}" data-row="{{ $answer['id'] }}" checked>
                    </td>

                    <td>
                        <p>{{ $answer['alternative_mobile_phone'] ?: 'No proporcionado' }}</p>
                        <hr>
                        <p>{{ $answer['official_answers']['Telefono alterno'] ?? '' }}</p>
                        <input type="checkbox" class="select" name="alternative_mobile_phone"
                               value="{{ $answer['alternative_mobile_phone'] }}" data-row="{{ $answer['id'] }}" checked>
                    </td>

                    <td>
                        <p>{{ $answer['city'] }}</p>
                        <hr>
                        <p>{{ $answer['official_answers']['Ciudad residencia'] ?? '' }}</p>
                        <input type="checkbox" class="select" name="city"
                               value="{{ $answer['city'] }}" data-row="{{ $answer['id'] }}" checked>
                    </td>

                    <td>
                        <p>{{ $answer['address'] }}</p>
                        <hr>
                        <p>{{ $answer['official_answers']['Direccion de correspondencia'] ?? '' }}</p>
                        <input type="checkbox" class="select" name="address"
                               value="{{ $answer['address'] }}" data-row="{{ $answer['id'] }}" checked>
                    </td>

                    <td>{{ $answer['created_at'] }}</td>

                    <td>
                        <div class="d-flex gap-2">
                            <form action="/app/controllers/approve.php"
                                  method="POST"
                                  onsubmit="return approve({{ $answer['id'] }})"
                                  id="form-{{ $answer['id'] }}">
                                <input type="hidden" name="id" value="{{ $answer['id'] }}">
                                <input type="hidden" name="identification_number"
                                       value="{{ $answer['identification_number'] }}">
                                <button class="btn btn-success btn-sm">Aprobar</button>
                            </form>

                            <form action="/app/controllers/deny.php"
                                  method="POST"
                                  onsubmit="return confirm('¿Estás seguro de rechazar este registro?')">
                                <input type="hidden" name="id" value="{{ $answer['id'] }}">
                                <button class="btn btn-danger btn-sm">Rechazar</button>
                            </form>
                        </div>
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

    @slot('scripts')
        <script>
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
