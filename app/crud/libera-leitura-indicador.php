<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();


$id_indicador_liberado = trim(isset($_GET['id_indicador_liberado'])) ? $_GET['id_indicador_liberado'] : '';

$motivo = trim(isset($_GET['motivo'])) ? $_GET['motivo'] : '';


if ($motivo == '') {


   $retorno = array('codigo' => 0, 'mensagem' => 'Você precisa relatar o Motivo, para podermos prosseguir com a liberação da leitura deste indicador.');

   echo json_encode($retorno, TRUE);

   exit;
}

$chave_unica_sessao = $_COOKIE['CHAVE_UNICA_SESSAO_ATUAL']; // recupera a chave global da sessao, para vinculo entre tb rmm e tb midia_leitura (Chave Estrangeira)
$estacao_atual = (isset($_COOKIE['estacao_atual'])) ? $_COOKIE['estacao_atual'] : '';
$id_plcode_atual  = trim(isset($_COOKIE['plcode_lido'])) ? $_COOKIE['plcode_lido'] : '';
$id_usuario = trim(isset($_COOKIE['id_usuario_logado'])) ? $_COOKIE['id_usuario_logado'] : '';

if ($id_indicador_liberado) {


   if ($chave_unica_sessao == '' || $chave_unica_sessao == 'undefined') {


      $retorno = array('codigo' => 0, 'mensagem' => 'Sua Chave Única de Acesso foi expirada após o último envio de Leituras, por favor Leia novamente o PLCode desejado para restabelecer a segurança dos dados.');

      echo json_encode($retorno, TRUE);

      exit;
   }

   $sql_atualiza_parametro =  $conexao->query("UPDATE parametros_ponto SET status_parametro='1' WHERE id_parametro='$id_indicador_liberado' ");

   $sql_atualiza_suporte =  $conexao->query("UPDATE suporte SET status_suporte='7', tipo_suporte='95',  motivo_resolutiva='$motivo' WHERE parametro='$id_indicador_liberado'");
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
      $chave_unica_sessao,
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
