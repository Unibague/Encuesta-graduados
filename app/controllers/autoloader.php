<?php

//Autoload the real autoloader

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../Helpers/Sessions.php';
require_once __DIR__ . '/../../Helpers/Auth.php';

session_start();
use Dotenv\Dotenv;

//Prepare dotenv
$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../../');
$dotenv->load();

function dd($var)
{
    header('Content-Type: application/json;charset=utf-8');
    echo json_encode($var);
    die();
}