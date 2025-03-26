<?php

require __DIR__ . '/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('489545266671-e2geupv9h7vu7n2tjp6quvef48l06bh0.apps.googleusercontent.com');
$client->setClientSecret('WoFrUntgtPN29bLnB83dkhuc');
$client->setRedirectUri('http://localhost:8000/estudiantesCallback.php');
$client->addScope("email");
$client->addScope("profile");

// Redirect to Google Auth
header('Location: ' . $client->createAuthUrl());
exit;