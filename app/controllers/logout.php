<?php
require 'autoloader.php';

$_SESSION['auth'] = false;
$_SESSION['username'] = '';
header("Location: /index.php");
