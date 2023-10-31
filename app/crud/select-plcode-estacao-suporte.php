<?php 
header("Content-Type: application/json");



require_once '../../conexao.php';
 	// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();	



$estacao_atual = trim(isset($_COOKIE['estacao_operador'])) ? $_COOKIE['estacao_operador'] : '';


if($estacao_atual==null){

    $retorno = array('id_ponto' => 0, 'nome_ponto' => "Não há Estação Selecionada");

    echo json_encode($retorno);

    exit;

} else {



$sql = $conexao->query("SELECT nome_ponto,id_ponto FROM pontos_estacao Where id_estacao = '$estacao_atual' AND status_ponto='1' ORDER BY nome_ponto ASC");
$dados = $sql->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($dados);



}