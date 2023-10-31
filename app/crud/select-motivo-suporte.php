<?php 
date_default_timezone_set('America/Sao_Paulo');

// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();



$sql = $conexao->query("SELECT nome_suporte,id_tipo_suporte FROM tipo_suporte Where status_tipo_suporte ='1' ORDER BY nome_suporte ASC");
$dados = $sql->fetchAll(PDO::FETCH_ASSOC);

if($dados){
    echo json_encode($dados, TRUE);

    $conexao=null;
    exit;



}