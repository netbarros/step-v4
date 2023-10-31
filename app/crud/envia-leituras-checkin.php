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

  // presencial
  $Texto_Checkin_Motivo_PLcode_Presencial_Alerta = trim(isset($_POST['Texto_Checkin_Motivo_PLcode_Presencial_Alerta'])) ? $_POST['Texto_Checkin_Motivo_PLcode_Presencial_Alerta'] : '';
  $valor_checkin_presencial =  trim(isset($_POST['valor_checkin_presencial'])) ? $_POST['valor_checkin_presencial'] : '';
  $tipo_suporte =  trim(isset($_POST['Categoria_Suporte_Checkin_Presencial'])) ? $_POST['Categoria_Suporte_Checkin_Presencial'] : '';

  if ($tipo_suporte == 0 || $tipo_suporte == '') {

    $tipo_suporte = '93';
  }
  // presencial


  $modo_checkin = trim(isset($_COOKIE['modo_checkin'])) ? $_COOKIE['modo_checkin'] : '';
  $periodo_ponto_checkin = trim(isset($_COOKIE['periodo_ponto_checkin'])) ? $_COOKIE['periodo_ponto_checkin'] : '';
  $id_ponto    = trim(isset($_COOKIE['plcode_lido'])) ? trim($_COOKIE['plcode_lido']) : '';
  $tipo_checkin_atual    = trim(isset($_COOKIE['tipo_checkin_atual'])) ? $_COOKIE['tipo_checkin_atual'] : '';
  $id_usuario = trim(isset($_COOKIE['id_usuario_logado'])) ? $_COOKIE['id_usuario_logado'] : '';
  $estacao_atual = trim(isset($_COOKIE['estacao_atual'])) ? $_COOKIE['estacao_atual'] : '';
  $origem_parametro = trim(isset($_COOKIE['origem_parametro'])) ? $_COOKIE['origem_parametro'] : '';
  $hora_leitura = trim(isset($_COOKIE['hora_leitura_checkin'])) ? $_COOKIE['hora_leitura_checkin'] : '';

  $data_leitura = date_create()->format('Y-m-d H:i:s');

  $latitude_user = trim(isset($_COOKIE['latitude_user'])) ? $_COOKIE['latitude_user'] : '';

  $longitude_user = trim(isset($_COOKIE['longitude_user'])) ? $_COOKIE['longitude_user'] : '';


  $hora_lida = date_create()->format('H:i:s');

  $chave_unica_sessao = $_COOKIE['CHAVE_UNICA_SESSAO_ATUAL']; // recupera a chave global da sessao, para vinculo entre tb rmm e tb midia_leitura (Chave Estrangeira)

  $status_leitura_inicial = '5'; // Nova Leitura, com status= "Em análise", aguardando validação pela API

  $status_checkin_inicial = '5'; // Nova Leitura, com status= "Em análise", aguardando validação pela API

  $id_parametro = '';
  $valor_leitura = '';

  foreach ($_POST as $keyPost => $valuePost) {

    $id_parametro = $keyPost;
    $valor_leitura =  $valuePost;
  }

  if ($tipo_checkin_atual == 'ponto_parametro') { // tipo_checkin valor: 2 // leitura rmm


   

    if ($id_parametro == "" || $id_parametro == 0 || $id_parametro == null) {



      $retorno = array('codigo' => 0, 'retorno' => 'Falha ao Salvar os Dados da Leitura, não foi detectado o Indicador Selecionado. Por favor, atualize seu navegador Google Chrome, habilite os Cookies antes de prosseguir. Caso o Erro Persista, entre em contato com o Suporte do STEP. Obrigado!');

      echo $retorno;


      exit;
    }

    $busca_obra = "SELECT id_obra FROM pontos_estacao WHERE id_ponto = :id_ponto";
    $stmt_busca_obra = $conexao->prepare($busca_obra);
    $stmt_busca_obra->bindParam(':id_ponto', $id_ponto, PDO::PARAM_INT);
    $stmt_busca_obra->execute();
    $result_busca_obra = $stmt_busca_obra->fetchAll(PDO::FETCH_ASSOC);
    $id_obra = $result_busca_obra[0]['id_obra'];

    $sql_grava_rmm = "INSERT INTO rmm(
      id_obra,
      chave_unica,
      id_ponto,
      id_parametro,
      leitura_entrada,
      volume_tratado,
      id_operador,
      status_leitura,
      data_leitura
      
        ) VALUES(
      :id_obra,
      :chave_unica,
      :id_ponto,
      :id_parametro,
      :leitura_entrada,
      :volume_tratado,
      :id_operador,
      :status_leitura,
      :data_leitura

            )";
    $stmt = $conexao->prepare($sql_grava_rmm);
    $stmt->bindParam(':id_obra', $id_obra, PDO::PARAM_STR);
    $stmt->bindParam(':chave_unica', $chave_unica_sessao, PDO::PARAM_STR);
    $stmt->bindParam(':id_ponto', $id_ponto, PDO::PARAM_INT);
    $stmt->bindParam(':id_parametro', $id_parametro, PDO::PARAM_INT);
    $stmt->bindParam(':leitura_entrada', $valor_leitura, PDO::PARAM_STR);
    $stmt->bindParam(':volume_tratado', $volume_tratado, PDO::PARAM_STR);
    $stmt->bindParam(':id_operador', $id_usuario, PDO::PARAM_INT);
    $stmt->bindParam(':status_leitura', $status_leitura_inicial, PDO::PARAM_INT); // nova leitura sempre status 5, "em análise" até a API verificar os valores
    $stmt->bindParam(':data_leitura', $data_leitura, PDO::PARAM_STR);

    $result_rmm = $stmt->execute();

    $counta_res_rmm = $stmt->rowCount();

    $ultimo_id_rmm = $conexao->lastInsertId();





    if ($counta_res_rmm > 0 && $ultimo_id_rmm != '') {



      //  faço a gravação do log
      $acao_log = "Leitura de Indicadores";
      $tipo_log = '2'; // Nova Leitura Checkin
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

      $tipo_checkin = '2';
      
      $sql_grava_checkin = "INSERT INTO checkin(
      tipo_checkin,
      id_periodo_ponto,
      modo_checkin,
      id_rmm,
      id_obra,
      id_estacao,
      id_ponto,
      id_parametro,
      id_colaborador,
      hora_leitura,
      hora_lida,
      status_checkin,
      chave_unica,
      latitude_operador,
      longitude_operador,
      prazo_decorrido,
      data_cadastro_checkin

        ) VALUES(
      :tipo_checkin,
      :id_periodo_ponto,
      :modo_checkin,
      :id_rmm,
      :id_obra,
      :id_estacao,
      :id_ponto,
      :id_parametro,
      :id_colaborador,
      :hora_leitura,
      :hora_lida,
      :status_checkin,
      :chave_unica,
      :latitude_operador,
      :longitude_operador,
      :prazo_decorrido,
      :data_cadastro_checkin
            )";
      $stmt = $conexao->prepare($sql_grava_checkin);
      $stmt->bindParam(':tipo_checkin', $tipo_checkin);
      $stmt->bindParam(':id_periodo_ponto', $periodo_ponto_checkin);
      $stmt->bindParam(':modo_checkin', $modo_checkin);
      $stmt->bindParam(':id_rmm', $ultimo_id_rmm);
      $stmt->bindParam(':id_estacao', $estacao_atual);
      $stmt->bindParam(':id_obra', $id_obra);
      $stmt->bindParam(':id_ponto', $id_ponto);
      $stmt->bindParam(':id_parametro', $id_parametro);
      $stmt->bindParam(':id_colaborador', $id_usuario);
      $stmt->bindParam(':hora_leitura', $hora_leitura);
      $stmt->bindParam(':hora_lida', $hora_lida);
      $stmt->bindParam(':status_checkin', $status_checkin_inicial);
      $stmt->bindParam(':chave_unica', $chave_unica_sessao);
      $stmt->bindParam(':latitude_operador', $latitude_user);
      $stmt->bindParam(':longitude_operador', $longitude_user);
      $stmt->bindParam(':prazo_decorrido', $prazo_decorrido);
      $stmt->bindParam(':data_cadastro_checkin', $data_leitura);

      $result_check = $stmt->execute();

      $counta_check_rmm = $stmt->rowCount();



      if ($counta_check_rmm > 0) {

        //  faço a gravação do log leitura de check realizado 

        //  faço a gravação do log
        $acao_log = "Checkin Realizado!";
        $tipo_log = '2'; // Nova Leitura Checkin

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

      }
    } // fecho insert novo checkin




    if ($counta_res_rmm > 0) { // caso a leitura seja salva com sucesso



      $retorno = array('codigo' => 1, 'retorno' => "Checkin do  ID: " . $periodo_ponto_checkin . " => Armazenado com Sucesso!");

      echo json_encode($retorno);

      //unset($chave_unica_sessao);  //unset() destrói a variável especificada. (variável local será destruída)

      // clearstatcache(); // serve para limpar o cache do php onde a chave da leitura em vigor está instanciada


    } else {


      //  faço a gravação do log
      $acao_log = "Checkin NÃO Realizado!";
      $tipo_log = '2'; // Nova Leitura Checkin

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




      $retorno = array('codigo' => 0, 'retorno' => 'Falha SQL ao Salvar os Dados da Leitura do PLCode = ' . $periodo_ponto_checkin . '');

      echo $retorno;
    }

    $conexao = null;
  } // fecha tipo de checkin para leitura de indicadores (parâmetros)

  //======== [ Inicio do Checkin Presencial ]=============


  if ($tipo_checkin_atual == 'ponto_plcode') { // tipo_checkin valor: 1 presencial

    $tipo_checkin = '1';

    $busca_obra = "SELECT id_obra FROM pontos_estacao WHERE id_ponto = :id_ponto";
    $stmt_busca_obra = $conexao->prepare($busca_obra);
    $stmt_busca_obra->bindParam(':id_ponto', $id_ponto, PDO::PARAM_INT);
    $stmt_busca_obra->execute();
    $result_busca_obra = $stmt_busca_obra->fetchAll(PDO::FETCH_ASSOC);
    $id_obra = $result_busca_obra[0]['id_obra'];

    $sql_grava_checkin = "INSERT INTO checkin(
      tipo_checkin,
      id_periodo_ponto,
      modo_checkin,
      id_obra,
      id_estacao,
      id_ponto,
      id_colaborador,
      hora_leitura,
      hora_lida,
      chave_unica,
      status_checkin,
      latitude_operador,
      longitude_operador,
      prazo_decorrido

        ) VALUES(
      :tipo_checkin,
      :id_periodo_ponto,
      :modo_checkin,
      :id_obra,
      :id_estacao,
      :id_ponto,
      :id_colaborador,
      :hora_leitura,
      :hora_lida,
      :chave_unica,
      :status_checkin,
      :latitude_operador,
      :longitude_operador,
      :prazo_decorrido
            )";
    $stmt = $conexao->prepare($sql_grava_checkin);
    $stmt->bindParam(':tipo_checkin', $tipo_checkin);
    $stmt->bindParam(':id_periodo_ponto', $periodo_ponto_checkin);
    $stmt->bindParam(':modo_checkin', $modo_checkin);
    $stmt->bindParam(':id_obra', $id_obra);
    $stmt->bindParam(':id_estacao', $estacao_atual);
    $stmt->bindParam(':id_ponto', $id_ponto);
    $stmt->bindParam(':id_colaborador', $id_usuario);
    $stmt->bindParam(':hora_leitura', $hora_leitura);
    $stmt->bindParam(':hora_lida', $hora_lida);
    $stmt->bindParam(':chave_unica', $chave_unica_sessao);
    $stmt->bindParam(':status_checkin', $status_checkin_inicial);
    $stmt->bindParam(':latitude_operador', $latitude_user);
    $stmt->bindParam(':longitude_operador', $longitude_user);
    $stmt->bindParam(':prazo_decorrido', $prazo_decorrido);

    $result_check = $stmt->execute();

    $counta_check_rmm = $stmt->rowCount();

    if ($result_check) {



      // gera log da atividade do usuario

      //  faço a gravação do log
      $acao_log = "Checkin Presencial";
      $tipo_log = '2'; // Novo Checkin

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



      if ($valor_checkin_presencial == '2') { // PLCode com Problemas


        $status_suporte = '1'; // novo suporte


        $sql = "INSERT INTO suporte(
        tipo_suporte,
        motivo_suporte,
        obra,
        estacao,
        plcode,
        quem_abriu,
        chave_unica,
        status_suporte
        ) VALUES(
        :tipo_suporte,
        :motivo_suporte,
        :estacao,
        :plcode,
        :quem_abriu,
        :chave_unica,
        :status_suporte
            )";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':tipo_suporte', $tipo_suporte);
        $stmt->bindParam(':motivo_suporte', $Texto_Checkin_Motivo_PLcode_Presencial_Alerta);
        $stmt->bindParam(':obra', $id_obra);
        $stmt->bindParam(':estacao', $estacao_atual);
        $stmt->bindParam(':plcode', $id_ponto);
        $stmt->bindParam(':quem_abriu', $id_usuario);
        $stmt->bindParam(':chave_unica', $chave_unica_sessao);
        $stmt->bindParam(':status_suporte', $status_suporte);

        $result = $stmt->execute();


        if ($result) {
          $atualiza_status_PLCode = $conexao->query("UPDATE pontos_estacao SET status_ponto='3' WHERE id_ponto='$id_ponto'");


          //  faço a gravação do log
          $acao_log = "Suporte";
          $tipo_log = '34'; // Novo Suporte


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


        } else {

          $retorno = array('codigo' => 0, 'retorno' => "Falha ao acionar o Alerta do PLCode, neste momento, por favor, tente em alguns instantes.");

          echo json_encode($retorno);
        }
      }



      // finaliza log atividade usuario

      $retorno = array('codigo' => 1, 'retorno' => "Checkin Presencial do  ID: " . $periodo_ponto_checkin . " => Armazenado com Sucesso!");

      echo json_encode($retorno);


      //  faço a gravação do log leitura de check realizado 

    } else {

      $retorno = array('codigo' => 0, 'retorno' => 'Falha SQL ao Salvar os Dados da Leitura do PLCode = ' . $periodo_ponto_checkin . '');
    }

    $conexao = null;
  } // fecha tipo de checkin por validação presencial


  // tarefa agendada (delegada)

} // fecha a validação do recebimento das variaveis via post para prosseguir
