<!doctype html>
<html lang="es" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{!! $title !!} - Unibagué</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <script src="/tablefilter/tablefilter.js"></script>

    {{--SCRIPTS AND CSS--}}
    {!! $header !!}
</head>
<body class="d-flex flex-column justify-content-between h-100">
<header>
    <nav class="navbar navbar-dark bg-dark navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="https://www.unibague.edu.co/images/2022/Unibague-4.0.png"
                     alt="">
                <span class="h3 ms-2">Encuesta de egresados</span>
            </a>

            @if(auth())
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link" href="/ready.php">Listos para actualizar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pending.php">No están en SIGA</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/rejected.php">Rechazados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/deleted.php">Borrados</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <form action="/app/controllers/logout.php" method="POST">
                        <button type="submit" class="btn btn-dark">Cerrar sesión</button>
                    </form>
                </div>

            @endif
        </div>
    </nav>
</header>
<main class="flex-grow-1 py-3">
    <div class="container h-100">
        {!! $slot !!}
    </div>
</main>

<footer class="footer mt-auto py-3 bg-dark">
    <div class="container">
        <span class="h3 text-white">Universidad de Ibagué ©</span>
    </div>
</footer>

{{--extra scripts--}}

{!! $scripts !!}
{{--BOOTSTRAP JS--}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa"
        crossorigin="anonymous"></script>
</body>
</html>
