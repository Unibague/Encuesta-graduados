<?php

require 'Helpers/Auth.php';
//Check if is auth
verifyIsAuthenticated();

$redirectUri = '/pending.php';
header("Location: $redirectUri");
die();
