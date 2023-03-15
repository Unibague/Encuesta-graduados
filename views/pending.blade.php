@component('templates.main')
    @slot('title')
        Registros pendientes de sincronización
    @endslot

    @slot('header')
        <script src="/tablefilter/tablefilter.js"></script>
        {{--DISABLING FILTER ON LAST COLUMN--}}
        <style>
            #flt11_table1 {
                display: none;
            }

            #flt11_table2 {
                display: none;
            }
        </style>
    @endslot

    @if(isset($message))
        <div class="toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-2" role="alert"
             aria-live="assertive" aria-atomic="true" id="messages">
            <div class="d-flex">
                <div class="toast-body">
                    {{$message}}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Cerrar"></button>
            </div>
        </div>
    @endif

    @if(isset($error))
        <div class="toast align-items-center text-bg-danger border-0 position-fixed top-0 end-0 m-2" role="alert"
             aria-live="assertive" aria-atomic="true" id="errors">
            <div class="d-flex">
                <div class="toast-body">
                    {{$error}}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Cerrar"></button>
            </div>
        </div>
    @endif
    <h1 class="text-center mb-4">
        Registros pendientes de sincronización
    </h1>


    <div class="table-responsive">
        <table class="table table-striped table-hover" id="table1">
            <thead>
            <tr>
                <th scope="col">#ID</th>
                <th scope="col">Cédula</th>
                <th scope="col">Nombre</th>
                <th scope="col">Apellido</th>
                <th scope="col">Correo electrónico</th>
                <th scope="col">Teléfono</th>
                <th scope="col">Teléfono alterno</th>
                <th scope="col">País</th>
                <th scope="col">Ciudad</th>
                <th scope="col">Dirección</th>
                <th scope="col">Fecha de recepción</th>
                <th scope="col">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($graduatedAnswers as $key=>$answer)
                <tr>
                    <th scope="row">{{$answer['id']}}</th>
                    <td>{{$answer['identification_number']}}</td>
                    <td>{{$answer['name']}}</td>
                    <td>{{$answer['last_name']}}</td>
                    <td>{{$answer['email']}}</td>
                    <td>{{$answer['mobile_phone']}}</td>
                    <td>{{$answer['alternative_mobile_phone']}}</td>
                    <td>{{$answer['country']}}</td>
                    <td>{{$answer['city']}}</td>
                    <td>{{$answer['address']}}</td>
                    <td>{{$answer['created_at']}}</td>
                    <td class="align-middle">
                        <div>
                            <form action="/app/controllers/resynchronize.php" method="POST">
                                <input type="text" name="id" value="{{$answer['id']}}" hidden>
                                <input type="text" name="identification_number"
                                       value="{{$answer['identification_number']}}" hidden>
                                <button type="submit" class="btn btn-primary d-block mb-2">Sincronizar</button>
                            </form>
                            <form action="/app/controllers/deny.php" method="POST"
                                  onsubmit="return confirm('¿Estás seguro que deseas rechazar este registro?')">
                                <input type="text" name="id" value="{{$answer['id']}}" hidden>
                                <button type="submit" class="btn btn-danger">Rechazar</button>
                            </form>
                        </div>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>

    @slot('scripts')
        <script>
            window.addEventListener('load', function () {
                //Toast
                @if(isset($error))

                const errorToast = document.getElementById('errors')

                const toast1 = new bootstrap.Toast(errorToast)
                toast1.show();
                @endif

                @if(isset($message))

                const messageToast = document.getElementById('messages')

                const toast2 = new bootstrap.Toast(messageToast)
                toast2.show();
                @endif

                //Tablefilter
                let tfConfig = {
                    paging: {
                        results_per_page: ['Resultados: ', [10, 25, 50, 100]]
                    },
                    base_path: 'tablefilter/',
                    alternate_rows: true,
                    btn_reset: true,
                    rows_counter: true,
                    loader: true,
                    status_bar: true,
                    mark_active_columns: {
                        highlight_column: true
                    },
                    highlight_keywords: true,
                    no_results_message: true,
                    extensions: [{
                        name: 'sort'
                    }],

                    /** Bootstrap integration */

                    // allows Bootstrap table styling
                    themes: [{
                        name: 'transparent'
                    }]
                };

                const tf = new TableFilter(document.querySelector('#table1'), tfConfig);
                tf.init();
            })
        </script>
    @endslot

@endcomponent