<?php
require __DIR__ . '/app/controllers/autoloader.php';

use eftec\bladeone\BladeOne;
use Ospina\EasySQL\EasySQL;

//Check if is auth
verifyIsAuthenticated();


//create db object
$notGraduatedAnswersConnection = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));
$rejectedAnswers = $notGraduatedAnswersConnection->table('form_answers')->select(['*'])
    ->where('is_denied','=',1)
    ->where('is_deleted','=',0)
    ->get();

$blade = new BladeOne();
try {
    $isPending = $_SESSION['pending'] ?? false;
    if ($isPending) {
        //Almacenar variable
        $message = $_SESSION['message'];

        //Limpiar variables antes de renderizar
        $_SESSION['message'] = '';
        $_SESSION['pending'] = false;
        echo $blade->run("rejected", compact('rejectedAnswers', 'message'));
    } else {
        echo $blade->run("rejected", compact('rejectedAnswers'));
    }

} catch (Exception $e) {
    echo 'Ha ocurrido un error';
}

