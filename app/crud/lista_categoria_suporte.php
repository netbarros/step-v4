<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('America/Sao_Paulo');

// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();


$sql = "SELECT nome_suporte,id_tipo_suporte FROM tipo_suporte Where status_tipo_suporte ='1' ORDER BY nome_suporte ASC";
$stm = $conexao->prepare($sql);


$stm->execute();

$json_data = $stm->fetchAll(PDO::FETCH_ASSOC);

$count = $stm->rowCount();


if($count > 0){

// Formatação para saida do dropdown do select2 automático via json, quando solicitado
$data = array();
foreach( $json_data as $row){ 
  $data[] = array("id"=>$row['id_tipo_suporte'], "text"=>$row['nome_suporte']);
}
echo json_encode($data);

// Formatação para saida do dropdown do select2 automático via json, quando solicitado
    
    $conexao=null;
    exit;

} 
