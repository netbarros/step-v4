<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

date_default_timezone_set('America/Sao_Paulo');
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();

$id_usuario =  trim(isset($_COOKIE['id_usuario_logado'])) ? $_COOKIE['id_usuario_logado'] : '';
$estacao_atual = trim(isset($_COOKIE['estacao_atual'])) ? $_COOKIE['estacao_atual'] : '';

$plcode_lido = trim(isset($_COOKIE['plcode_lido'])) ? $_COOKIE['plcode_lido'] : '';

$texto_motivo_suporte = trim(isset($_POST['texto_motivo_suporte'])) ? $_POST['texto_motivo_suporte'] : '';
$acao = trim(isset($_POST['acao'])) ? $_POST['acao'] : '';
$tipo_suporte = trim(isset($_POST['tipo_suporte'])) ? $_POST['tipo_suporte'] : '';

$origem_suporte =  trim(isset($_POST['origem_suporte'])) ? $_POST['origem_suporte'] : ''; //plcode ou estação

$ID_Plcode_Suporte =  trim(isset($_POST['suporte_plcode'])) ? $_POST['suporte_plcode'] : ''; //plcode enviado, caso a origem seja por PLCODE

if(isset($_COOKIE['CHAVE_UNICA_SESSAO_ATUAL'])){
   $chave_unica_sessao = trim($_COOKIE['CHAVE_UNICA_SESSAO_ATUAL']);
}

if($chave_unica_sessao == ''){
   $retorno = array('codigo' => 0, 'retorno' => "Sua chave de acesso única Expirou, por favor, leia um PLCode da sua Estação ou Refaça seu login.");
   echo json_encode($retorno);
   exit;
}

if($origem_suporte=='0' || $origem_suporte=='' || $tipo_suporte=='' ){

     $retorno = array('codigo' => 0, 'retorno' => "Precisamos do Formulário Preenchido, para prosseguirmos com sua Solicitação. Verifique a Categoria do Suporte.");

echo json_encode($retorno);


   exit;
}

if($origem_suporte=='1' && $ID_Plcode_Suporte==''){

     $retorno = array('codigo' => 0, 'retorno' => "Precisamos do Formulário Preenchido, para prosseguirmos com sua Solicitação. Verifique o PLCode Selecionado.");

echo json_encode($retorno);


   exit;
}

if($texto_motivo_suporte==''){

    $retorno = array('codigo' => 0, 'retorno' => "Precisamos saber o Motivo da sua Solicitação de Suporte.");

echo json_encode($retorno);


   exit;
}



if($acao=='novo_suporte'  && $id_usuario!='' && $estacao_atual!='' ){


   if($ID_Plcode_Suporte==''){
      $ID_Plcode_Suporte='0';
   }


 $status_suporte='1';

$sql_suporte = "INSERT INTO suporte (
tipo_suporte,
motivo_suporte, 
estacao,
plcode,
chave_unica,
quem_abriu,
status_suporte
   ) 
VALUES (
      ?,
      ?,
      ?,
      ?,
      ?,
      ?,
      ?
      )";
$conexao->prepare($sql_suporte)->execute([
    $tipo_suporte,
    $texto_motivo_suporte,
    $estacao_atual,
    $ID_Plcode_Suporte,
    $chave_unica_sessao,
    $id_usuario,
    $status_suporte  // em aberto (novo suporte)
]);
 //  faço a gravação do log


  if($sql_suporte){

   if($tipo_suporte=='91'){ // sistema parado

      $sql = "UPDATE estacoes SET status_estacao = '2' WHERE id_estacao = ? ";
      $conexao->prepare($sql)->execute([$estacao_atual]);



   } else if($tipo_suporte=='93'){ // PLCode com Problemas

      $sql = "UPDATE pontos_estacao SET status_ponto = '2' WHERE id_ponto = ? ";
      $conexao->prepare($sql)->execute([$ID_Plcode_Suporte]);




   } else if($tipo_suporte=='88' && $ID_Plcode_Suporte!=''){ // Tubulação com Problemas


      $sql = "UPDATE pontos_estacao SET status_ponto = '2' WHERE id_ponto = ? ";
      $conexao->prepare($sql)->execute([$ID_Plcode_Suporte]);


   } else{
      // não faz nada
   }

      
//  faço a gravação do log
$acao_log = "Suporte";
$tipo_log = '34'; // Novo SUporte

$sql_log = "INSERT INTO log_leitura (
  chave_unica,
   id_usuario, 
   acao_log,
   estacao_logada,
   tipo_log) 
VALUES (
      ?,
      ?,
      ?,
      ?,
      ?
      )";
$conexao->prepare($sql_log)->execute([
    $chave_unica_sessao,
    $id_usuario,
    $acao_log,
    $estacao_atual,
    $tipo_log ]);
 //  faço a gravação do log



    $retorno = array('codigo' => 1, 'retorno' => "Suporte Solicitado com Sucesso!");

    echo json_encode($retorno, TRUE);


 } else {

 $retorno = array('codigo' => 0, 'retorno' => "Não foi possível Realizar a Abertura do Suporte, no momento. \n\n ".print_r($sql_suporte)."");

echo json_encode($retorno, TRUE);

 // gera log da atividade do usuario



    
 }

} else { // se não tiver varivaris mínimas para cadastro, para e retorna:

    $retorno = array('codigo' => 0, 'retorno' => "Precisamos do Formulário Preenchido, para prosseguirmos com sua Solicitação.");

echo json_encode($retorno, TRUE);

}
