<!doctype html>
<html lang="es" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resultado individual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <style>
        table thead th {
            background-color: #1A3A6B !important;
            color: #fff !important;
        }
    </style>
</head>
<body class="d-flex flex-column justify-content-between h-100">
<header>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="https://www.unibague.edu.co/images/2022/Unibague-4.0.png"
                     alt="">
                <span class="h3 ms-2">
            Encuesta de egresados
        </span>
            </a>
        </div>
    </nav>
</header>

<div class="h-100 container d-flex flex-column justify-content-between py-4">
    <div>
        <h1 class="text-center mb-4">
            Viendo resultados de {{$email}}({{$identificationNumber}})
        </h1>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th scope="col">Pregunta</th>
                    <th scope="col">Respuesta</th>
                </tr>
                </thead>
                <tbody>
                @foreach($answersAsObject as $key=>$answer)
                    <tr>
                        <td>{{$key}}</td>
                        <td>{{$answer}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <div>
            <a href="/pending.php" class="btn btn-primary">Ir Atrás</a>
        </div>
        <div>
            <button type="button" class="btn btn-success">Aprobar</button>
            <button type="button" class="btn btn-danger">Aprobar</button>
        </div>
    </div>
</div>


<footer class="footer mt-auto py-3 bg-dark">
    <div class="container">
        <span class="h3 text-white">Universidad de Ibagué ©</span>
    </div>
</footer>
{{--BOOTSTRAP JS--}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa"
        crossorigin="anonymous"></script>
</body>
</html>
