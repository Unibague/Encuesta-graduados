<?php

function verifyIsAuthenticated()
{
    if (!auth()) {
        header("Location: /login.php");
        exit;
    }
}

function auth(): bool
{
    return isset($_SESSION['auth']) && $_SESSION['auth'] === true;
}

function user(): ?object
{
    if (!auth()) {
        return null;
    }

    return (object)[
        'username' => $_SESSION['username'],
        'id' => $_SESSION['id'],
    ];
}

function redirectToDefaultRoute()
{
    header("Location: /ready.php");
    exit;
}
