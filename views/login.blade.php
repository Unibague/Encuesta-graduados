@component('templates.main')
    @slot('title')
        Iniciar sesión
    @endslot
    @slot('header')
    @endslot

    @if(isset($error))
        <div class="toast align-items-center text-bg-danger border-0 position-fixed top-0 end-0 m-2" role="alert"
             aria-live="assertive" aria-atomic="true" id="liveToast">
            <div class="d-flex">
                <div class="toast-body">
                    {{$error}}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Cerrar"></button>
            </div>
        </div>
    @endif
    <form class="d-flex justify-content-center h-100" method="POST" action="/login.php">
        <div class="d-flex justify-content-center d-flex flex-column">
            <div class="mb-2">
                <label for="exampleInputEmail1">USUARIO Unibagué</label>
                <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="Usuario"
                       placeholder="Usuario Unibagué" name="username">
            </div>
            <label for="exampleInputPassword1">Contraseña</label>
            <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Contraseña"
                   name="password">
            <button style="background: #0f1f39; color: white" type="submit" class="mt-2 btn btn-lg btn-block">Iniciar
                Sesión
            </button>
        </div>
    </form>
    @slot('scripts')
        <script>
            window.addEventListener('load', function () {
                @if(isset($error))

                const toastLiveExample = document.getElementById('liveToast')

                const toast = new bootstrap.Toast(toastLiveExample)
                toast.show();
                @endif
            })
        </script>
    @endslot

@endcomponent