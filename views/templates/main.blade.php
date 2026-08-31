<!doctype html>
<html lang="es" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{!! $title !!} - Unibagué</title>
    <link rel="icon" type="image/svg+xml" href="/images/favicon.svg">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx"
        crossorigin="anonymous"
    >

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="/tablefilter/tablefilter.js"></script>

    <style>
        :root {
            --primary: #1A3A6B;
            --primary-dark: #0F2747;
            --primary-light: #E8EEF7;
            --success: #16a672;
            --success-light: #e4f7f0;
            --danger: #ef4444;
            --danger-light: #fef2f2;
            --text: #1f2333;
            --text-muted: #7c8093;
            --border: #e6e5f1;
            --shadow: 0 20px 45px -20px rgba(26, 58, 107, 0.35);
        }

        html, body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 12% 8%, rgba(26, 58, 107, 0.10), transparent 40%),
                radial-gradient(circle at 88% 92%, rgba(22, 166, 114, 0.08), transparent 42%),
                #f6f6fb;
        }

        /* ===== Navbar ===== */
        .navbar {
            width: 100%;
            min-width: 0;
            background: var(--text) !important;
            box-shadow: 0 4px 18px -6px rgba(31, 35, 51, 0.35);
        }

        .navbar > .container {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            max-width: 100%;
        }

        .navbar-brand img {
            height: 42px;
            width: auto;
        }

        .navbar-brand span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-weight: 700;
            color: #fff;
        }

        .navbar-nav {
            gap: 0.25rem;
        }

        .navbar-nav .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            font-weight: 600;
            white-space: nowrap;
            border-radius: 10px;
            color: #fff !important;
            transition: background-color 0.2s, color 0.2s;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(26, 58, 107, 0.14);
            color: #fff !important;
        }

        .navbar-nav .nav-link.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff !important;
            font-weight: 700;
        }

        .navbar .btn-logout {
            margin-left: 0.5rem;
            white-space: nowrap;
        }

        .navbar .btn-logout .btn {
            border-radius: 10px;
            font-weight: 700;
            border-width: 2px;
            border-color: var(--primary);
            color: var(--primary);
            background: #fff;
            transition: background-color 0.2s, color 0.2s;
        }

        .navbar .btn-logout .btn:hover {
            background: var(--primary-light);
            color: var(--primary-dark);
        }

        .navbar-toggler {
            border-color: rgba(26, 58, 107, 0.4);
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 4px rgba(26, 58, 107, 0.25);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'><path stroke='rgba(26, 58, 107,0.9)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/></svg>");
        }

        /* ===== Main content ===== */
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

        /* Tablas administrativas con el mismo lenguaje visual */
        .table-responsive,
        .page-scroll,
        .table-wrap {
            overflow-x: auto;
            border-radius: 16px;
            -webkit-overflow-scrolling: touch;
        }

        main .table:not(.migrated-full-table),
        main .ready-table,
        main .rejected-table {
            min-width: 1100px;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            color: var(--text);
            font-family: inherit;
        }

        main .table:not(.migrated-full-table) thead th,
        main .ready-table thead th,
        main .rejected-table thead th {
            background: var(--primary) !important;
            color: #fff !important;
            border: 0;
            border-bottom: 2px solid rgba(26, 58, 107, 0.25) !important;
            padding: 13px 16px;
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
        }

        main .table:not(.migrated-full-table) tbody td,
        main .ready-table tbody td,
        main .rejected-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f7;
            border-right: 0;
            background: #fff;
            color: var(--text);
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
        }

        main .table:not(.migrated-full-table) tbody tr:hover td,
        main .ready-table tbody tr:hover td,
        main .rejected-table tbody tr:hover td {
            background: #fafafd;
        }

        main .table:not(.migrated-full-table) thead th:first-child,
        main .ready-table thead th:first-child,
        main .rejected-table thead th:first-child {
            border-radius: 12px 0 0 0;
        }

        main .table:not(.migrated-full-table) thead th:last-child,
        main .ready-table thead th:last-child,
        main .rejected-table thead th:last-child {
            border-radius: 0 12px 0 0;
        }

        /* ===== Footer ===== */
        footer {
            width: 100%;
            min-width: 0;
            background: var(--text) !important;
        }

        footer .container {
            width: 100%;
            max-width: 100%;
        }

        footer .text-white {
            color: rgba(255, 255, 255, 0.75) !important;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.02em;
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

                        <!-- <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'ready.php' ? 'active' : '' }}"
                               href="/ready.php">
                                Listos para actualizar
                            </a>
                        </li> -->

                        <!-- <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'pending.php' ? 'active' : '' }}"
                               href="/pending.php">
                                No están en SIGA
                            </a>
                        </li> -->

                        <!-- <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'not_graduated.php' ? 'active' : '' }}"
                               href="/not_graduated.php">
                                Egresados
                            </a>
                        </li> -->

                        <!-- <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'migrated.php' ? 'active' : '' }}"
                               href="/migrated.php">
                                Historial SIGA
                            </a>
                        </li> -->

                        <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'migrated_full.php' ? 'active' : '' }}"
                               href="/migrated_full.php">
                                Registros Actualizados
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'encuentro_resultados.php' ? 'active' : '' }}"
                               href="/encuentro_resultados.php">
                                Encuentro graduados
                            </a>
                        </li>

                        <!-- <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'rejected.php' ? 'active' : '' }}"
                               href="/rejected.php">
                                Rechazados
                            </a>
                        </li> -->

                        <!-- <li class="nav-item">
                            <a class="nav-link {{ basename($_SERVER['PHP_SELF']) === 'deleted.php' ? 'active' : '' }}"
                               href="/deleted.php">
                                Borrados
                            </a>
                        </li> -->

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
