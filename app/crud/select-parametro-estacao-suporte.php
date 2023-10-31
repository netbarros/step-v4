<?php 
 require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
//ini_set("session.cookie_secure", 1);
if (!isset($_SESSION)) session_start();



$plcode_selecionado = trim(isset($_GET['plcode_selecionado'])) ? $_GET['plcode_selecionado'] : '';


if($plcode_selecionado==null){

    $retorno = array('id_parametro' => "0", 'nome_parametro' => "Não há PLCode Selecionado");

    echo json_encode($retorno);

    exit;

} else {



$sql = $conexao->query("SELECT nome_parametro,id_parametro FROM parametros_ponto Where id_ponto = '$plcode_selecionado' AND status_parametro ='1' ORDER BY nome_parametro ASC");
$dados = $sql->fetchAll(PDO::FETCH_ASSOC);

if($dados){
    echo json_encode($dados);

    $conexao=null;
    exit;

} else{


    $retorno = array('id_parametro' => "0", 'nome_parametro' => "Não há PLCode Selecionado");

    echo json_encode($retorno);

    

    exit;


}





}