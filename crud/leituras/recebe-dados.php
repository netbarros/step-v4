<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('America/Sao_Paulo');

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/conexao.php';

$conexao = Conexao::getInstance();
//validateHeader();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
 }



$data = fetchPostData();

if (empty($data)) {
    responseJson(['status' => 0, 'mensagem' => "Algumas variáveis obrigatórias estão faltando."]);
    exit;
}

$parametros_lidos = parseParameters($data['parametros_lidos']);

if (!$parametros_lidos) {
    responseJson(['status' => 0, 'mensagem' => "Os indicadores enviados, não foram corretamente interpretados."]);

    echo json_encode($parametros_lidos);
    exit;
}

foreach ($parametros_lidos as $idParametro => $valorParametro) {
    if (!isDouble($valorParametro)) {
        responseJson(['status' => 0, 'mensagem' => "O valor do Indicador id: $idParametro, com valor $valorParametro, que foi informado, não está no formato correto."]);
        exit;
    }

    $info = fetchRoAndSupervisor($conexao, $data['plcode_lido']);

    if (!$info) {
        responseJson(['status' => 0, 'mensagem' => "Impossível Prosseguir, Não Foram Localizados RO e Supervisor, Responsáveis pelo Núcleo Atual."]);
        exit;
    }

    $inserted = insertIntoRmm($conexao, $data, $idParametro, $valorParametro, $info);

    if ($inserted) {
        insertIntoLogLeitura($conexao, $data);
    }
}

responseJson(['status' => 1, 'mensagem' => "Suas Leituras Foram Armazenadas com Sucesso!"]);
exit;

// Functions
function validateHeader() {
    if ($_SERVER['HTTP_X_MY_CUSTOM_HEADER'] !== 'Valor_Seguro') {
        // Acesso negado
        exit;
    }
}

function fetchPostData() {
  $keys = [
      'estacao_atual' => 'int',
      'plcode_lido' => 'int',
      'id_usuario_logado' => 'int',
      'chave_unica' => 'string',
      'latitude_user' => 'float',
      'longitude_user' => 'float',
      'parametros_lidos' => 'string'
  ];

  $data = [];
  foreach ($keys as $key => $type) {
      if (isset($_POST[$key])) {
          switch ($type) {
              case 'int':
                  $data[$key] = (int)$_POST[$key];
                  break;
              case 'float':
                  $data[$key] = (float)$_POST[$key];
                  break;
              case 'string':
                  $data[$key] = $_POST[$key];
                  break;
          }
      } else {
          $data[$key] = null;
      }
  }

  return $data;
}


function responseJson($data) {
    echo json_encode($data);
}

function parseParameters($paramString) {
  $paramArray = explode(',', $paramString);
  $result = [];
  foreach ($paramArray as $param) {
      list($key, $value) = explode(':', $param);
      $result[trim($key)] = trim($value);
  }
  return $result;
}

function isDouble($valor) {
    return preg_match('/^-?\d{1,8}(\.\d{1,2})?$/', $valor);
}

/* function fetchRoAndSupervisor($conexao, $plcode_lido) {
  $sql = "SELECT e.ro, e.supervisor FROM estacoes e 
          INNER JOIN pontos_estacao pt ON pt.id_ponto = ? 
          INNER JOIN usuarios_projeto up ON up.id_obra = e.id_obra
          WHERE e.id_estacao = pt.id_estacao";
  $stmt = $conexao->prepare($sql);
  $stmt->execute([$plcode_lido]);

  return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
} */


function fetchRoAndSupervisor($conexao, $plcode_lido) {
    // Primeiro, tente buscar ro e supervisor da tabela estacoes
    $sql_estacoes = "SELECT e.ro, e.supervisor FROM estacoes e 
                     INNER JOIN pontos_estacao pt ON pt.id_ponto = ? 
                     WHERE e.id_estacao = pt.id_estacao";
    $stmt = $conexao->prepare($sql_estacoes);
    $stmt->execute([$plcode_lido]);
    $result_estacoes = ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;

    // Se ro ou supervisor for NULL, então busque da tabela usuarios_projeto
    if (!$result_estacoes || is_null($result_estacoes['ro']) || is_null($result_estacoes['supervisor'])) {
        $sql_usuarios_projeto = "SELECT up.nivel FROM usuarios_projeto up
                                  INNER JOIN estacoes e ON e.id_obra = up.id_obra
                                  INNER JOIN pontos_estacao pt ON pt.id_estacao = e.id_estacao
                                  WHERE pt.id_ponto = ? AND up.responsavel = 1";
        $stmt = $conexao->prepare($sql_usuarios_projeto);
        $stmt->execute([$plcode_lido]);
        $result_usuarios_projeto = ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        foreach ($result_usuarios_projeto as $row) {
            if ($row['nivel'] === 'ro' && is_null($result_estacoes['ro'])) {
                $result_estacoes['ro'] = $row['nivel'];
            }

            if ($row['nivel'] === 'supervisor' && is_null($result_estacoes['supervisor'])) {
                $result_estacoes['supervisor'] = $row['nivel'];
            }
        }
    }

    return $result_estacoes ? $result_estacoes : false;
}

//** tratar erro de retorno na tentativa de inclusao da leitura e do log  *//

function insertIntoRmm($conexao, $data, $idParametro, $valorParametro, $info) {
  $status_leitura_inicial = '5';
  $stmt = $conexao->prepare('INSERT INTO  rmm (
  chave_unica ,
  id_ponto, 
  id_parametro,
  leitura_entrada, 
  id_operador,
  id_ro,
  id_supervisor,
  latitude_user,
  longitude_user,
  status_leitura
  ) VALUES(
  :chave_unica ,
  :id_ponto,
  :id_parametro,
  :leitura_entrada,
  :id_operador,
  :id_supervisor, 
  :id_ro,
  :latitude_user,
  :longitude_user,        
  :status_leitura)');
  $stmt->execute([
      ':chave_unica' => $data['chave_unica'],
      ':id_ponto' => $data['plcode_lido'],
      ':id_parametro' => $idParametro,
      ':leitura_entrada' => $valorParametro,
      ':id_operador' => $data['id_usuario_logado'],
      ':id_ro' => $info['ro'],
      ':id_supervisor' => $info['supervisor'],
      ':latitude_user' => $data['latitude_user'],
      ':longitude_user' => $data['longitude_user'],
      ':status_leitura' => $status_leitura_inicial
  ]);

  return $stmt->rowCount() > 0;
}

function insertIntoLogLeitura($conexao, $data) {
  $acao_log = "Nova Leitura Livre APP";
  $tipo_log = '1';
  $sql_log = "INSERT INTO log_leitura (
          chave_unica,
          id_usuario, 
          acao_log,
          estacao_logada,
          tipo_log) 
      VALUES (
          :chave_unica,
          :id_usuario,
          :acao_log,
          :estacao_logada,
          :tipo_log
          )";
  $conexao->prepare($sql_log)->execute([
      ':chave_unica' => $data['chave_unica'],
      ':id_usuario' => $data['id_usuario_logado'],
      ':acao_log' => $acao_log,
      ':estacao_logada' => $data['estacao_atual'],
      ':tipo_log' => $tipo_log
  ]);
}

?>
