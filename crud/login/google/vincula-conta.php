<?php
header("content-type: application/json");
//require_once '../../conexao.php';
// Atribui uma conexão PDO

require_once $_SERVER['DOCUMENT_ROOT'].'/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();


$id_usuario = $_POST['id_usuario'] ?? '';

$email_google = $_POST['email_google'] ?? '';