<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('America/Sao_Paulo');

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/conexao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/valida-acesso-app.php';

$conexao = Conexao::getInstance();
//validateHeader();
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

$id_indicador = trim(isset($_POST['id_indicador'])) ? $_POST['id_indicador'] : '';

$motivo = trim(isset($_POST['motivo'])) ? $_POST['motivo'] : '';

$chave_unica =trim(isset($_POST['chave_unica'])) ? $_POST['chave_unica'] : '';
$estacao_atual = trim(isset($_POST['estacao_atual'])) ? $_POST['estacao_atual'] : '';
$plcode_lido  = trim(isset($_POST['plcode_lido'])) ? $_POST['plcode_lido'] : '';
$id_usuario = trim(isset($_POST['id_usuario'])) ? $_POST['id_usuario'] : '';

if ($motivo == '') {


   $retorno = array('codigo' => 0, 'mensagem' => 'Você precisa relatar o Motivo, para podermos prosseguir com a liberação da leitura deste indicador.');

   echo json_encode($retorno, TRUE);

   exit;
}



if ($id_indicador) {


   if ($chave_unica == '' || $chave_unica == 'undefined') {


      $retorno = array('codigo' => 0, 'mensagem' => 'Sua Chave Única de Acesso foi expirada após o último envio de Leituras, por favor Leia novamente o PLCode desejado para restabelecer a segurança dos dados.');

      echo json_encode($retorno, TRUE);

      exit;
   }
   

   $sql_atualiza_parametro =  $conexao->query("UPDATE parametros_ponto SET status_parametro='1' WHERE id_parametro='$id_indicador' ");


   $sql_atualiza_suporte =  $conexao->query("UPDATE suporte SET status_suporte='7', tipo_suporte='95',  motivo_resolutiva='$motivo' WHERE parametro='$id_indicador'");
   //  faço a gravação do log
   $acao_log = "Libera Indicador na Leitura";
   $tipo_log = '51'; // Indicador (Parâmetro) Liberado

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
      $chave_unica,
      $id_usuario,
      $acao_log,
      $estacao_atual,
      $tipo_log
   ]);
   //  faço a gravação do log



   $retorno = array('codigo' => 1, 'mensagem' => 'Indicador Liberado para Registro de Leitura! O Suporte foi Atualizado.');

   echo json_encode($retorno, TRUE);
} else {

   $retorno = array('codigo' => 0, 'mensagem' => 'Falha na Solicitação, Por favor tente mais tarde, não pudemos realizar sua Solicitação no Momento.');

   echo json_encode($retorno, TRUE);
}


