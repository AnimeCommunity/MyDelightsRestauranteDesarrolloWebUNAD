<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    echo "Acceso denegado";
    exit();
}

?>
