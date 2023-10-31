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

// pega os dados do formuário da OBRA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $id_plcode_atual  = trim(isset($_COOKIE['plcode_lido'])) ? $_COOKIE['plcode_lido'] : '';
  $id_usuario = trim(isset($_COOKIE['id_usuario_logado'])) ? $_COOKIE['id_usuario_logado'] : '';

  $latitude_user = trim(isset($_COOKIE['latitude_user'])) ? $_COOKIE['latitude_user'] : '';

  $longitude_user = trim(isset($_COOKIE['longitude_user'])) ? $_COOKIE['longitude_user'] : '';


  if ($latitude_user == "" || $longitude_user == "") {

    $retorno = array('codigo' => 0, 'retorno' => 'Coordenadas GPS <strong>Ausentes!</strong>, Por favor, você precisa habilitar a Localização no seu Navegador, para a aplicação do STEP.');

    echo json_encode($retorno);

    exit;
  }



  $estacao_atual = (isset($_COOKIE['estacao_atual'])) ? $_COOKIE['estacao_atual'] : '';

  $data_leitura = date_create()->format('Y-m-d H:i:s');

  $chave_unica_sessao = $_COOKIE['CHAVE_UNICA_SESSAO_ATUAL']; // recupera a chave global da sessao, para vinculo entre tb rmm e tb midia_leitura (Chave Estrangeira)

  // $chave_unica_sessao = Chave_Unica_Sessao_Atual; // para recuperar o valor do defined na Constant no PHP


  // Faz loop pelo array dos numeros
  $count = '';
  foreach ($_POST as $keyPost => $valuePost) {

    if ($valuePost === '') {

      $retorno = array('codigo' => 0, 'retorno' => 'Dados de <strong>Leituras em Branco não são Aceitas!</strong>, caso o sistema esteja parado, <strong>Acione o Aviso do Parâmetro em questão.<strong>');

      echo json_encode($retorno);

      exit;
    }



    if ($keyPost === "" || $keyPost === '0') {



      $retorno = array('codigo' => 0, 'retorno' => 'Falha ao Salvar os Dados da Leitura, não foram detectados todos os Indicadores e suas Leituras Enviadas. Por favor, atualize seu navegador Google Chrome, habilite os KTCookie e tente novamente.');

      echo json_encode($retorno);



      exit;
    }



    $stmt = $conexao->prepare('INSERT INTO  rmm (
        chave_unica ,
        id_ponto, 
        id_parametro,
        leitura_entrada, 
        id_operador,
        latitude_user,
        longitude_user,
        data_leitura,
         status_leitura
         ) VALUES(
        :chave_unica ,
        :id_ponto,
        :id_parametro,
        :leitura_entrada,
        :id_operador,
        :latitude_user,
        :longitude_user,        
        :data_leitura,
        :status_leitura)');
    $stmt->execute(array(

      ':chave_unica' => $chave_unica_sessao,
      ':id_ponto' => $id_plcode_atual,
      ':id_parametro' => $keyPost,
      ':leitura_entrada' => $valuePost,
      ':id_operador' => $id_usuario,
      ':latitude_user' => $latitude_user,
      ':longitude_user' => $longitude_user,
      ':data_leitura' => $data_leitura,
      ':status_leitura' => '5' // Aguardando Análise (tudo que depende da API verificar para disparar os alertas de email e sms, terão status 5, seja suporte, leitura ou checkin)


    ));
  } // fecha o foreach

  $count = $stmt->rowCount();



  if ($count > 0) {


    //  faço a gravação do log
    $acao_log = "Leitura Avulsa";
    $tipo_log = '1'; // Nova Leitura

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

    $retorno = array('codigo' => 1, 'retorno' => "Dados da Leitura Armazenados com Sucesso!");

    echo json_encode($retorno);


    $conexao = null;
    exit;
  } else {

    $retorno = array('codigo' => 0, 'retorno' => "Nenhuma Ação Ocorreu, SQL Não Respondeu. Por favor, comunique o Suporte.");

    echo json_encode($retorno);


    $conexao = null;
    exit;
  }
}