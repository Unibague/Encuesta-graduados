<!doctype html>
<html lang="es" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{!! $title !!} - Unibagué</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx"
        crossorigin="anonymous"
    >

    <script src="/tablefilter/tablefilter.js"></script>

    <style>
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .navbar-brand img {
            height: 42px;
            width: auto;
        }

        /* Evita que cualquier elemento hijo provoque scroll horizontal */
        html,
        body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        .navbar {
            width: 100%;
            min-width: 0;
        }

        .navbar > .container {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        .navbar-brand {
            min-width: 0;
            max-width: 100%;
        }

        .navbar-brand span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .navbar-nav {
            gap: 0.25rem;
        }

        .navbar-nav .nav-link {
            padding: 0.5rem 0.6rem;
            font-size: 0.9rem;
            white-space: nowrap;
            border-radius: 6px;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        .navbar .btn-logout {
            margin-left: 0.5rem;
            white-space: nowrap;
        }

        main {
            width: 100%;
            min-width: 0;
            overflow-x: hidden;
        }

        main > .container {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        footer {
            width: 100%;
            min-width: 0;
        }

        footer .container {
            width: 100%;
            max-width: 100%;
        }

        /*
         * Bootstrap expande el navbar desde 992px. En este proyecto hay
         * suficientes opciones de menú para que ese ancho todavía sea
         * pequeño y el header termine desbordándose.
         *
         * A partir de 1200px dejamos el menú horizontal.
         * Por debajo de 1200px usamos el botón hamburguesa.
         */
        @media (max-width: 1199.98px) {
            .navbar-expand-lg .navbar-toggler {
                display: block;
            }

            .navbar-expand-lg .navbar-collapse {
                display: none !important;
            }

            .navbar-expand-lg .navbar-collapse.show {
                display: block !important;
            }

            .navbar-expand-lg .navbar-nav {
                flex-direction: column;
                align-items: stretch !important;
                gap: 0.15rem;
                margin-top: 0.75rem;
                width: 100%;
            }

            .navbar-expand-lg .navbar-nav .nav-link {
                width: 100%;
                padding: 0.65rem 0.75rem;
            }

            .navbar-expand-lg .btn-logout {
                margin: 0.75rem 0 0;
                width: 100%;
            }

            .navbar-expand-lg .btn-logout .btn {
                width: 100%;
            }
        }

        @media (min-width: 1200px) {
            .navbar-expand-lg .navbar-toggler {
                display: none;
            }

            .navbar-expand-lg .navbar-collapse {
                display: flex !important;
            }
        }

        /* Móviles pequeños */
        @media (max-width: 575.98px) {
            .navbar > .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .navbar-brand {
                gap: 7px;
            }

            .navbar-brand img {
                height: 34px;
            }

            .navbar-brand span {
                font-size: 0.95rem;
            }

            main {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            main > .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            footer .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
        }
    </style>

    {!! $header ?? '' !!}
</head>

<body class="d-flex flex-column min-vh-100 w-100">

<header>
    <nav class="navbar navbar-dark bg-dark navbar-expand-lg">
        <div class="container">

            <a class="navbar-brand" href="/">
                <img
                    src="https://www.unibague.edu.co/images/2022/Unibague-4.0.png"
                    alt="Unibagué"
                >
                <span class="h5 mb-0">Encuesta de egresados</span>
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar"
                aria-controls="mainNavbar"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            @if(auth())
                <div class="collapse navbar-collapse" id="mainNavbar">

                    <ul class="navbar-nav ms-auto align-items-lg-center">

                        <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'ready.php' ? 'active' : '' }}"
                               href="/ready.php">
                                Listos para actualizar
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'pending.php' ? 'active' : '' }}"
                               href="/pending.php">
                                No están en SIGA
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'not_graduated.php' ? 'active' : '' }}"
                               href="/not_graduated.php">
                                Egresados
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'migrated.php' ? 'active' : '' }}"
                               href="/migrated.php">
                                Historial SIGA
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'migrated_full.php' ? 'active' : '' }}"
                               href="/migrated_full.php">
                                Datos
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'rejected.php' ? 'active' : '' }}"
                               href="/rejected.php">
                                Rechazados
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'deleted.php' ? 'active' : '' }}"
                               href="/deleted.php">
                                Borrados
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'graphic.php' ? 'active' : '' }}"
                               href="/graphic.php">
                                Gráficos
                            </a>
                        </li>

                    </ul>

                    <form
                        action="/app/controllers/logout.php"
                        method="POST"
                        class="btn-logout"
                    >
                        <button
                            type="submit"
                            class="btn btn-outline-light btn-sm"
                        >
                            Cerrar sesión
                        </button>
                    </form>

                </div>
            @endif

        </div>
    </nav>
</header>

<main class="flex-grow-1 py-3 w-100">
    <div class="container">
        {!! $slot !!}
    </div>
</main>

<footer class="footer mt-auto py-3 bg-dark w-100">
    <div class="container text-center">
        <span class="text-white">
            Universidad de Ibagué ©
        </span>
    </div>
</footer>

{!! $scripts ?? '' !!}

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa"
    crossorigin="anonymous">
</script>

</body>
</html>
