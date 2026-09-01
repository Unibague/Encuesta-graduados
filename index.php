<?php

require 'Helpers/Auth.php';
//Check if is auth
verifyIsAuthenticated();

$redirectUri = '/migrated_full.php';
header("Location: $redirectUri");
die();
