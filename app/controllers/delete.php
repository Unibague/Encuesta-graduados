<?php

require 'autoloader.php';

use Ospina\EasySQL\EasySQL;

// =========================
// AUTH
// =========================
verifyIsAuthenticated();

// =========================
// INPUT
// =========================
$id = (int) ($_POST['id'] ?? 0);

if (!$id) {
    flashSession('ID inválido');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// =========================
// DB
// =========================
$db = new EasySQL('encuesta_graduados', getenv('ENVIRONMENT'));

$now = date('Y-m-d H:i:s');

// =========================
// UPDATE (SQL SEGURO)
// =========================
$sql = "
    UPDATE form_answers
    SET
        is_deleted = 1,
        updated_at = '$now'
    WHERE id = $id
";

$db->makeQuery($sql);

// =========================
// OK
// =========================
flashSession('Registro borrado correctamente');
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
