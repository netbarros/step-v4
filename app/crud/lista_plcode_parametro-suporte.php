<?php 	 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('America/Sao_Paulo');

require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();


$id_plcode_atual = trim(isset($_COOKIE['plcode_lido'])) ? $_COOKIE['plcode_lido'] : '';

$estacao_atual = trim(isset($_COOKIE['estacao_atual'])) ? $_COOKIE['estacao_atual'] : '';




// Dica 5 - Válida os dados do usuário com o banco de dados
$sql = "SELECT p.id_ponto, p.nome_ponto  FROM pontos_estacao p
WHERE p.id_estacao = '$estacao_atual' AND p.status_ponto!='2' ORDER BY p.nome_ponto ASC ";



$stm = $conexao->prepare($sql);


$stm->execute();

$json_data = $stm->fetchAll(PDO::FETCH_ASSOC);

$count = $stm->rowCount();


if($count > 0){

// Formatação para saida do dropdown do select2 automático via json, quando solicitado
$data = array();
foreach( $json_data as $row){ 
  $data[] = array("id"=>$row['id_ponto'], "text"=>$row['nome_ponto']);
}
echo json_encode($data);

// Formatação para saida do dropdown do select2 automático via json, quando solicitado
    
    $conexao=null;
    exit;


 }

