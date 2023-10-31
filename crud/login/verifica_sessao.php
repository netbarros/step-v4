<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logado'])) {
    header('Location: ../../crud/login/logout.php');
    exit(); // Importante para parar a execução do script
}


