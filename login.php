<?php
session_start();
require 'vendor/autoload.php';
require 'Helpers/Auth.php';

use eftec\bladeone\BladeOne;
use Ospina\EasyLDAP\EasyLDAP;
use Ospina\EasySQL\EasySQL;

if (auth()) {
    redirectToDefaultRoute();
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        handleGetRequest();
        break;
    case 'POST':
        handlePostRequest();
        break;
}
/**
 * @return void
 */
function handleGetRequest()
{
    parseLoginView();
}

/**
 * @return void
 * @throws JsonException
 */

function isValidUser(string $userName): bool
{
    $user = getUserId($userName);
    return count($user) !== 0;
}

function getUserId(string $userName)
{
    $mysql = new EasySQL('encuesta_graduados', 'local');
    try {
        $result = $mysql->table('users')->select(['id'])
            ->where('username', '=', $userName)
            ->get();

    } catch (RuntimeException $e) {
        return false;
    }
    return $result[0]['id'];
}

/**
 * @throws JsonException
 */
function handlePostRequest()
{
    try {
        $request = parseRequest();
    } catch (RuntimeException $e) {
        //Render the login view again.
        $error = $e->getMessage();
        parseLoginView($error);
    }

    $isValid = isValidUser($request->username);
    if ($isValid !== true) {
        $error = 'No estás autorizado para usar la plataforma';
        parseLoginView($error);
    }

    $auth = authenticate($request->username, $request->password);
    //Invalid credentials
    if ($auth === false) {
        $error = 'Los datos que ingresaste no coinciden con nuestro registros';
        parseLoginView($error);
    }

    setSessionObject($request->username, getUserId($request->username));
    redirectToDefaultRoute();
}

/**
 * @param $username
 * @param $password
 * @return bool
 * @throws RuntimeException
 */
function authenticate($username, $password): bool
{
    $easyLDAP = new EasyLDAP(false);
    $role = 1; //Functionary
    try {
        $easyLDAP->authenticate($username, $password, $role);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function parseLoginView($error = null)
{
    $blade = new BladeOne();
    try {
        if ($error === null) {
            echo $blade->run("login");
        } else {
            echo $blade->run("login", compact('error'));
        }
    } catch (Exception $e) {
        echo 'Ha ocurrido un error renderizando la vista';
    }
    die();
}

function setSessionObject($username, $id)
{
    $_SESSION['auth'] = true;
    $_SESSION['username'] = $username;
    $_SESSION['id'] = $id;
}

/**
 * @return object
 * @throws RuntimeException
 */
function parseRequest(): object
{
    if (!$_POST['username']) {
        throw new RuntimeException('Debes introducir un nombre de usuario');
    }
    if (!$_POST['password']) {
        throw new RuntimeException('Debes introducir una contraseña válida');
    }

    return (object)$_REQUEST;

}