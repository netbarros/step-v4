<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('America/Sao_Paulo');
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// pega os dados do formuário da OBRA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $id_plcode_atual = isset($_COOKIE['plcode_lido']) ? trim($_COOKIE['plcode_lido']) : '';
  
   $estacao_atual = (isset($_COOKIE['estacao_atual'])) ? $_COOKIE['estacao_atual'] : '';

   // Recupera a chave única da sessão para vinculo entre tb rmm e tb midia_leitura (Chave Estrangeira)
  $chave_unica_sessao = $_COOKIE['CHAVE_UNICA_SESSAO_ATUAL'] ?? $_POST['chave_unica'] ?? '';

  $estacao_atual = (isset($_COOKIE['estacao_atual'])) ? $_COOKIE['estacao_atual'] : '';

  $data_leitura = date_create()->format('Y-m-d H:i:s');


  if($id_plcode_atual == ''){
    $retorno = array('codigo' => 0, 'retorno' => 'O PLCode não foi lido, atualize a página e refaça a operação.');
    echo json_encode($retorno);
    exit;
}


$latitude_user = $_COOKIE['latitude_user'] ?? '';
$longitude_user = $_COOKIE['longitude_user'] ?? '';

$empty_values = ["", "undefined", "0", "0.0", "0.00", NULL];

if (in_array($latitude_user, $empty_values, true) || in_array($longitude_user, $empty_values, true)) { 
    $retorno = [
        'codigo' => 0, 
        'retorno' => 'Coordenadas GPS <strong>Ausentes!</strong>, Por favor, você precisa habilitar a Localização no seu Navegador, para o uso da aplicação do STEP, se necessário  atualize seu navegador Google Chrome, habilite os Cookies E Localização e tente novamente.'
    ];

    echo json_encode($retorno);
    exit;
}


$id_usuario = $_COOKIE['id_usuario_logado'] ?? '';

$empty_values = ["", "undefined", "0", NULL];

if (in_array($id_usuario, $empty_values, true)) { 
    $retorno = [
        'codigo' => 0, 
        'retorno' => 'Usuário <strong>Ausente!</strong>, Por favor, atualize seu navegador Google Chrome, habilite os Cookies e tente novamente.'
    ];

    echo json_encode($retorno);
    exit;
}


// Verifica se a chave única da sessão está vazia ou indefinida
if (empty($chave_unica_sessao) || $chave_unica_sessao === "undefined") {
    $retorno = [
        'codigo' => 0,
        'retorno' => 'Refaça a Operação, Chave de Acesso Expirada, para sua segurança pedidos que refaça seu Login.'
    ];

    header('Content-Type: application/json');
    echo json_encode($retorno);
    exit;
}

// Verifica se os indicadores liberados para leitura foram enviados corretamente
function ensureDecimal($value, $default = 0.00) {
  if (is_numeric($value)) {
      return number_format((float)$value, 2, '.', '');
  } else {
      return number_format((float)$default, 2, '.', '');
  }
}






  foreach ($_POST as $keyPost => $valuePost) {

    $valuePost = str_replace(',', '.', $valuePost);

    $valuePost = ensureDecimal($valuePost);

   // Verifique se o valor POST não está vazio
   if ($valuePost === '') {
    $retorno = array('codigo' => 0, 'retorno' => 'Dados de <strong>Leituras em Branco não são Aceitas!</strong>, caso o sistema esteja parado, <strong>Acione o Aviso do Parâmetro em questão.<strong>');
    echo json_encode($retorno);
    exit;
}

     // Verifique se o nome POST não está vazio ou igual a zero
     if ($keyPost === "" || $keyPost === '0') {
      $retorno = array('codigo' => 0, 'retorno' => 'Falha ao Salvar os Dados da Leitura, não foram detectados todos os Indicadores e suas Leituras Enviadas. Por favor, atualize seu navegador Google Chrome, habilite os KTCookie e tente novamente.');
      echo json_encode($retorno);
      exit;
  }


// fecha validação de indicadores recebidos


// faz a consulta dos ros e superisors das estacoes

    $sql = "SELECT e.ro, e.supervisor,e.id_obra FROM estacoes e 
    INNER JOIN pontos_estacao pt ON pt.id_ponto = ? 
    WHERE e.id_estacao = pt.id_estacao";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$id_plcode_atual]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_ro = $row['ro'];
    $id_supervisor = $row['supervisor'];
    $id_obra = $row['id_obra'];

    

// prepara o rmm - tabela das leituras

    $stmt = $conexao->prepare('INSERT INTO  rmm (
        id_obra,
        chave_unica,
        id_ponto, 
        id_parametro,
        leitura_entrada, 
        id_operador,
        id_ro,
        id_supervisor,
        latitude_user,
        longitude_user,
        data_leitura,
         status_leitura
         ) VALUES(
          :id_obra,
        :chave_unica ,
        :id_ponto,
        :id_parametro,
        :leitura_entrada,
        :id_operador,
        :id_supervisor, 
        :id_ro,
        :latitude_user,
        :longitude_user,        
        :data_leitura,
        :status_leitura)');
    $stmt->execute(array(
      ':id_obra' => $id_obra,
      ':chave_unica' => $chave_unica_sessao,
      ':id_ponto' => $id_plcode_atual,
      ':id_parametro' => $keyPost,
      ':leitura_entrada' => $valuePost,
      ':id_operador' => $id_usuario,
      ':id_ro' => $id_ro,
      ':id_supervisor' => $id_supervisor,
      ':latitude_user' => $latitude_user,
      ':longitude_user' => $longitude_user,
      ':data_leitura' => $data_leitura,
      ':status_leitura' => '5'


    ));
    
  } // fecha o foreach

  $count = $stmt->rowCount();



  if ($count > 0) {


    //  faço a gravação do log se a leitura ocorreu corretamente
    $acao_log = "Nova Leitura Livre";
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
    //  fecho a gravação do log se a leitura ocorreu corretamente

    $retorno = array('codigo' => 1, 'retorno' => "Dados da Leitura Armazenados com Sucesso!"); // retorno positivo para o ajax

    echo json_encode($retorno);


    $conexao = null;
    exit;

  } else {

    $retorno = array('codigo' => 0, 'retorno' => "Nenhuma Ação Ocorreu, SQL Não Respondeu. Por favor, comunique o Suporte."); // retorno positivo para o ajax

    echo json_encode($retorno);


    $conexao = null;
    exit;
  }
}
