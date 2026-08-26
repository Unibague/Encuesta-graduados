@component('templates.main')
    @slot('title')
        Iniciar sesión
    @endslot
    @slot('header')
<<<<<<< Updated upstream
=======
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            :root {
                --primary: #1A3A6B;
                --primary-dark: #0F2747;
                --primary-light: #E8EEF7;
                --danger: #ef4444;
                --danger-light: #fef2f2;
                --text: #1f2333;
                --text-muted: #7c8093;
                --border: #e6e5f1;
                --shadow: 0 20px 45px -20px rgba(26, 58, 107, 0.35);
            }

            body {
                font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
                background: #f6f6fb;
                color: var(--text);
            }

            .login-shell {
                width: 100%;
                flex: 1;
                min-height: 0;
                box-sizing: border-box;

                padding: 40px 16px;

                background:
                    radial-gradient(circle at 12% 8%, rgba(26, 58, 107, 0.18), transparent 40%),
                    radial-gradient(circle at 88% 92%, rgba(22, 166, 114, 0.14), transparent 42%),
                    #f6f6fb;

                display: flex;
                align-items: center;
                justify-content: center;
            }



            .login-card {
                background: #ffffff;
                border-radius: 26px;
                padding: 40px 34px 34px;
                box-shadow: var(--shadow);
                border: 1px solid rgba(26, 58, 107, 0.12);
                max-width: 400px;
                width: 100%;
                position: relative;
                overflow: hidden;
            }

            .login-card::before {
                content: '';
                position: absolute;
                top: -60px;
                right: -60px;
                width: 160px;
                height: 160px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(26, 58, 107,0.12), transparent 70%);
                pointer-events: none;
            }

            @media (max-width: 420px) {
                .login-card { padding: 30px 22px 26px; border-radius: 20px; }
            }

            .login-title {
                text-align: center;
                margin-bottom: 28px;
                position: relative;
            }

            .login-title h1 {
                font-size: 1.6rem;
                font-weight: 800;
                margin: 0 0 6px;
                letter-spacing: -0.02em;
            }

            .login-title p {
                color: var(--text-muted);
                font-size: 0.9rem;
                margin: 0;
            }

            .login-form {
                position: relative;
            }

            .form-field-login {
                margin-bottom: 18px;
            }

            .field-label-login {
                display: block;
                font-weight: 700;
                font-size: 0.9rem;
                margin-bottom: 8px;
                color: var(--text);
            }

            .input-field {
                width: 100%;
                padding: 13px 15px;
                border: 2px solid var(--border);
                border-radius: 14px;
                font-size: 0.98rem;
                font-family: inherit;
                color: var(--text);
                background: #fbfbfe;
                transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            }

            .input-field::placeholder { color: #b3b4c6; }

            .input-field:focus {
                outline: none;
                border-color: var(--primary);
                background: #ffffff;
                box-shadow: 0 0 0 4px rgba(26, 58, 107, 0.18);
            }

            .btn-login {
                width: 100%;
                border: none;
                border-radius: 13px;
                font-size: 15px;
                font-weight: 700;
                cursor: pointer;
                font-family: inherit;
                background: linear-gradient(135deg, var(--primary), var(--primary-dark));
                color: #1f2333;
                padding: 13px 26px;
                box-shadow: 0 10px 20px -8px rgba(26, 58, 107, 0.55);
                margin-top: 8px;
                transition: filter 0.2s, transform 0.1s, box-shadow 0.2s;
            }

            .btn-login:hover { filter: brightness(1.05); }
            .btn-login:active { transform: scale(0.97); }

            #liveToast {
                border: none !important;
                border-radius: 14px !important;
                background: var(--danger) !important;
                box-shadow: 0 12px 24px -10px rgba(239, 68, 68, 0.5);
            }
        </style>
>>>>>>> Stashed changes
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