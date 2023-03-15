@component('templates.main')
    @slot('title')
        Registros listos para migrar
    @endslot

    @slot('header')
        <script src="/tablefilter/tablefilter.js"></script>
        {{--DISABLING FILTER ON LAST COLUMN--}}
        <style>
            #flt10_table1 {
                display: none;
            }


            th {
                height: 50px;
                width: 50px;
                text-align: center;
                vertical-align: middle;
            }

            td {
                max-width: 100%;
                white-space: nowrap;
                height: 50px;
                width: 50px;
                text-align: center;
                vertical-align: middle;
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
        Registros listos para migrar
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
                <th scope="col">Ciudad residencia</th>
                <th scope="col">Dirección</th>
                <th scope="col">Fecha de recepción</th>
                <th scope="col">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($graduatedAnswers as $key=>$answer)

                <tr>
                    <th scope="row">{{$answer['id']}}</th>
                    <td>
                        <p>
                            {{$answer['identification_number']}}
                        </p>
                        <hr>
                        <p>
                            {{$answer['official_answers']['Numero de identificacion']}}
                        </p>
                        <hr>
                        <input type="checkbox" name="{{$key}}" value="1" class="select" disabled>

                    </td>
                    <td>
                        <p>
                            {{$answer['name']}}
                        </p>
                        <hr>
                        <p>
                            {{$answer['official_answers']['Nombres']}}
                        </p>
                        <hr>
                        <input type="checkbox" name="{{$key}}" value="1" class="select" disabled>

                    </td>
                    <td>
                        <p>
                            {{$answer['last_name']}}
                        </p>
                        <hr>
                        <p>
                            {{$answer['official_answers']['Apellidos']}}
                        </p>
                        <hr>
                        <input type="checkbox" name="{{$key}}" value="1" class="select" disabled>

                    </td>
                    <td>
                        <p>
                            {{$answer['email']}}
                        </p>
                        <hr>
                        <p>
                            {{$answer['official_answers']['Correo']}}
                        </p>
                        <hr>
                        <input type="checkbox" name="email" value="{{$answer['email']}}" class="select"
                               data-row="{{$answer['id']}}" checked>

                    </td>
                    <td>
                        <p>
                            {{$answer['mobile_phone']}}
                        </p>
                        <hr>
                        <p>
                            {{$answer['official_answers']['Telefono de contacto']}}
                        </p>
                        <hr>
                        <input type="checkbox" name="mobile_phone" value="{{$answer['mobile_phone']}}"
                               class="select" data-row="{{$answer['id']}}" checked>

                    </td>
                    <td>
                        <p>
                            {{$answer['alternative_mobile_phone'] === ''? 'No proporcionado': $answer['alternative_mobile_phone'] }}
                        </p>
                        <hr>
                        <p>
                            {{$answer['official_answers']['Telefono alterno']}}
                        </p>
                        <hr>
                        <input type="checkbox" name="alternative_mobile_phone"
                               value="{{$answer['alternative_mobile_phone']}}" class="select"
                               data-row="{{$answer['id']}}" checked>

                    </td>
                    <td>
                        <p>
                            {{$answer['city']}}
                        </p>
                        <hr>
                        <p>
                            {{$answer['official_answers']['Ciudad residencia']}}
                        </p>
                        <hr>
                        <input type="checkbox" name="city" value="{{$answer['city']}}" class="select"
                               data-row="{{$answer['id']}}" checked>

                    </td>
                    <td>
                        <p>
                            {{$answer['address']}}
                        </p>
                        <hr>
                        <p>
                            {{$answer['official_answers']['Direccion de correspondencia']}}
                        </p>
                        <hr>
                        <input type="checkbox" name="address" value="{{$answer['address']}}" class="select"
                               data-row="{{$answer['id']}}" checked>

                    </td>
                    <td>{{$answer['created_at']}}</td>
                    <td>
                        <div class="d-flex align-items-center">

                            <form action="/app/controllers/approve.php" method="POST"
                                  onsubmit="return approve({{$answer['id']}})" id="form-{{$answer['id']}}">
                                <input type="text" name="id" value="{{$answer['id']}}" hidden>
                                <input type="text" name="identification_number"
                                       value="{{$answer['identification_number']}}" hidden>
                                <div id="formInputs">

                                </div>
                                <button type="submit"
                                        class="btn btn-success d-block me-2">Aprobar
                                </button>
                            </form>


                            <form action="/app/controllers/deny.php" method="POST"
                                  onsubmit="return confirm('¿Estás seguro que deseas rechazar este registro? Este será eliminado permanentemente de esta pantalla.')">
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
            async function approve(id) {
                //Get all checked html elements
                const checks = [...document.getElementsByClassName('select')];
                //Filter only the clicked row
                const rowChecks = checks.filter((function (check) {
                    return check.dataset.row == id && check.checked
                }))

                //Build final object
                let finalObject = {};
                rowChecks.forEach(function (attribute) {
                    finalObject[attribute.name] = attribute.value

                });

                let form = document.getElementById('form-'+id);
                Object.entries(finalObject).forEach(function (propertyAndValue) {
                    let newInput = document.createElement("input");
                    newInput.name = propertyAndValue[0];
                    newInput.hidden = true;
                    newInput.value = propertyAndValue[1];
                    form.appendChild(newInput);
                })
                return true;
            }
        </script>
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