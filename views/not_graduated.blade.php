@component('templates.main')

    @slot('title')
        Registros en SIGA (No graduados)
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

    <div class="page-scroll">

        <h1 class="text-center mb-3">Registros en SIGA (No graduados)</h1>

        <p class="text-center text-muted mb-4">
            Estos registros existen en SIGA, pero no están marcados como graduados.
            Puedes actualizarlos o rechazarlos.
        </p>

        {{-- BUSCADOR --}}
        <form method="GET" class="mb-4 d-flex justify-content-center">
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   class="form-control w-50"
                   placeholder="Buscar por cédula, nombre, correo, ciudad...">
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
                        {{ $answer['email'] }}
                        <br>
                        <input type="checkbox" class="select" name="email"
                               value="{{ $answer['email'] }}"
                               data-row="{{ $answer['id'] }}" checked>
                    </td>

                    <td>
                        {{ $answer['mobile_phone'] }}
                        <br>
                        <input type="checkbox" class="select" name="mobile_phone"
                               value="{{ $answer['mobile_phone'] }}"
                               data-row="{{ $answer['id'] }}" checked>
                    </td>

                    <td>
                        {{ $answer['alternative_mobile_phone'] ?: '—' }}
                        <br>
                        <input type="checkbox" class="select" name="alternative_mobile_phone"
                               value="{{ $answer['alternative_mobile_phone'] }}"
                               data-row="{{ $answer['id'] }}" checked>
                    </td>

                    <td>
                        {{ $answer['city'] }}
                        <br>
                        <input type="checkbox" class="select" name="city"
                               value="{{ $answer['city'] }}"
                               data-row="{{ $answer['id'] }}" checked>
                    </td>

                    <td>
                        {{ $answer['address'] }}
                        <br>
                        <input type="checkbox" class="select" name="address"
                               value="{{ $answer['address'] }}"
                               data-row="{{ $answer['id'] }}" checked>
                    </td>

                    <td>{{ $answer['created_at'] }}</td>

                    <td>
                        <div class="d-flex gap-2">
                            {{-- ACTUALIZAR --}}
                            <form action="/app/controllers/approve.php"
                                  method="POST"
                                  onsubmit="return approve({{ $answer['id'] }})"
                                  id="form-{{ $answer['id'] }}">
                                <input type="hidden" name="id" value="{{ $answer['id'] }}">
                                <input type="hidden" name="identification_number"
                                       value="{{ $answer['identification_number'] }}">
                                <button class="btn btn-success btn-sm">
                                    Actualizar
                                </button>
                            </form>

                            {{-- RECHAZAR --}}
                            <form action="/app/controllers/deny.php"
                                  method="POST"
                                  onsubmit="return confirm('¿Deseas rechazar este registro?')">
                                <input type="hidden" name="id" value="{{ $answer['id'] }}">
                                <button class="btn btn-danger btn-sm">
                                    Rechazar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center text-muted">
                        No hay registros para mostrar
                    </td>
                </tr>
            @endforelse
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
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.toast').forEach(el => {
                    new bootstrap.Toast(el, { delay: 5000 }).show();
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
