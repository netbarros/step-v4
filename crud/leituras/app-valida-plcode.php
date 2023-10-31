<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('America/Sao_Paulo');

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/conexao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/valida-acesso-app.php';

/* id_plcode, id_suporte, motivo, id_usuario  saida do json CODIGO, MENSAGENS*/

$conexao = Conexao::getInstance();
//validateHeader();
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

$id_plcode_atual = isset($_POST['plcode']) ? trim($_POST['plcode']) : '';
$id_usuario = isset($_POST['id_usuario']) ? trim($_POST['id_usuario']) : '';

// Verifica se as variáveis estão vazias
if (empty($id_plcode_atual) || empty($id_usuario)) {
    // Crie um array com a mensagem de erro
    $response = [
        'success' => false,
        'message' => 'Os campos plcode e id_usuario são obrigatórios'
    ];
    
    // Codifique o array em JSON e envie a resposta
    echo json_encode($response);
    exit;
}


// você sabe que $id_usuario deve ser um número, você pode verificar isso.
if (!is_numeric($id_usuario)) {
    $response = [
        'success' => false,
        'message' => 'id_usuario deve ser um número'
    ];
    
    echo json_encode($response);
    exit;
}


// Valida se existe um id e se ele é numérico
if ($id_plcode_atual) {

     $sql = "SELECT COUNT(DISTINCT s.id_suporte) as Total_Suporte,
            p.id_ponto,
            p.nome_ponto,
            p.status_ponto,
            e.id_estacao,
            e.status_estacao,
            p.status_ponto,
            cli.id_cliente,
            cli.nome_fantasia,
            e.nome_estacao,
            cli.nome_fantasia,
           s.status_suporte,
           s.data_open,
           s.data_close,
           s.data_prevista,
           s.motivo_suporte,
           s.id_suporte,
           ts.id_tipo_suporte,
           ts.nome_suporte,
           o.nome_obra,
               o.id_obra

        FROM pontos_estacao p
        LEFT JOIN suporte s ON s.plcode  = p.id_ponto
        LEFT JOIN obras o ON o.id_obra = s.obra
        LEFT JOIN tipo_suporte ts ON ts.id_tipo_suporte = s.tipo_suporte
        INNER JOIN estacoes e ON e.id_estacao = p.id_estacao 
        INNER JOIN clientes cli ON cli.id_cliente = p.id_cliente
        WHERE p.id_ponto = :id_ponto AND s.status_suporte != '4'";

     $stm = $conexao->prepare($sql);
     $stm->bindParam(':id_ponto', $id_plcode_atual);

     $stm->execute();
     $registro = $stm->fetch(PDO::FETCH_OBJ);

     $count = $stm->rowCount();


     //  var_dump($registro);

     if ($count < 0) {

          $retorno = array('codigo' => 0, 'msg_erro' => 'PLCode Inválido');
          echo json_encode($retorno);



          //fAZER ALGO COM A VARIAVEL USUARIO


     } else { // dando tudo certo, prossegue:

//===[ CHAVE ÚNICA da SESSAO] a cada acesso, o step registra uma codificação única de 32bits, 
$horario_completo_agora = microtime();
/* INÍCIO: Crio a Chave unica da Sessao para armazenamento e resgate das leituras e imagens que serão enviadas */
$chave_unica = bin2hex(random_bytes(33) . $horario_completo_agora);
/* Gerar strings aleatórias criptograficamente seguras, usamos 24 caracteres não repetitivos e aleatórios com criptografia nativa do PHP";*/



          /* FIM:  Crio a Chave unica da Sessao para armazenamento e resgate das leituras e imagens que serão enviadas */
          $estacao_logada = '';

                  
          $retorno = array(
               'codigo' => 1,
               'chave_unica' => $chave_unica,
               'nome_cliente' => $registro->nome_fantasia,
               'nome_plcode' => $registro->nome_ponto,
               'status_plcode' => $registro->status_ponto,
               'id_obra' => $registro->id_obra, // id_cliente é o mesmo que id_obra
               'nome_obra' => $registro->nome_obra,
               'id_estacao' => $registro->id_estacao,
               'nome_estacao' => $registro->nome_estacao,
               'status_estacao'=> $registro->status_estacao,
               'id_plcode_lido' => $registro->id_ponto,
               'Qtdade_Suporte_PLCode' => $registro->Total_Suporte,
               'status_suporte' => $registro->status_suporte,
               'id_suporte' => $registro->id_suporte,
               'id_tipo_suporte' => $registro->id_tipo_suporte,
               'nome_tipo_suporte' => $registro->nome_suporte,
               'motivo_suporte' => $registro->motivo_suporte,
               'data_abertura' => $registro->data_open,
               'data_prevista' => $registro->data_prevista,
               'data_encerramento' => $registro->data_close
          );

  

          //  faço a gravação do log
          $acao_log = "Consulta de PLCode";
          $tipo_log = '36'; // Consulta de PLCode por Leitura de QRCode
          $estacao_logada =  $registro->id_estacao;

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
               $estacao_logada,
               $tipo_log
          ]);
          //  faço a gravação do log


          

          echo json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
     }
     $conexao = null;
     exit();
} else{

     $retorno = array('codigo' => 0, 'msg_erro' => 'PLCode Invalido');
     echo json_encode($retorno);

}


