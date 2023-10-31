<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';
header("content-type: application/json");

$email = isset($_GET['email']) ? $_GET['email'] : '';
$modo_existente = isset($_GET['modo']) ? $_GET['modo'] : '';

$consulta = $conexao->prepare("SELECT email FROM usuarios WHERE email = :email");
$consulta->execute([':email' => $email]);

if($modo_existente === "altera"){
    // contagem de registros
    $response = $consulta->rowCount() > 1 ? false : true;
} else {
    $response = $consulta->rowCount() > 0 ? false : true;
}

echo json_encode($response);
?>
