<?php require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
date_default_timezone_set('America/Sao_Paulo');

$id_plcode_atual    = trim(isset($_COOKIE['plcode_lido'])) ? $_COOKIE['plcode_lido'] : '';



$estacao_atual = trim(isset($_COOKIE['estacao_atual'])) ? $_COOKIE['estacao_atual'] : '';


$id_usuario =  trim(isset($_COOKIE['id_usuario_logado'])) ? $_COOKIE['id_usuario_logado'] : '';


if ($id_usuario == '') {

     $retorno = array('codigo' => 0, 'msg_erro' => 'Não foi detectado usuário ativo nesta sessão, por favor, refaça seeu login.');
     echo json_encode($retorno);

     exit;
}


// Valida se existe um id e se ele é numérico
if (!empty($id_plcode_atual) && is_numeric($id_plcode_atual)) {

     $sql = 'SELECT 
            p.id_ponto,
            p.nome_ponto,
            e.id_estacao,
            p.status_ponto,
            cli.id_cliente,
            e.nome_estacao,
            e.status_estacao,
            cli.nome_fantasia,
           s.status_suporte,
           s.motivo_suporte,
           s.id_suporte

        FROM pontos_estacao p
        LEFT JOIN suporte s ON s.plcode = p.id_ponto
        INNER JOIN estacoes e ON e.id_estacao = p.id_estacao 
        INNER JOIN clientes cli ON cli.id_cliente = p.id_cliente
        WHERE p.id_ponto = :id_ponto ';

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

          //===[ CHAVE ÚNICA da SESSAO] a cada acesso, o step registra uma codificação única de 32bits, encriptografada, de acesso unico para o login do usuário e logout do mesmo.
          /* para cada nova leitura, é gerada uma chave_unica_sessao_atual, está é para mapearmos a rota desde a leitura do plcode e o que o usuário fez em sequência, 
checkin, abriu suporte, fez envio normal da leitura do plcode lido, enviou imagens nas leituras ou no suporte e ou reabertura de plcode, a chave_unica_sessao,
vinculará cada rotina do usuário, desde o início da etapa até a sua conclusão e leitura do próximo plcode, onde uma nova chave será gerada para o novo acompanhamento
da nova rotina do plcode lido, que se iniciará.
*/
          $horario_completo_agora = microtime();
          /* INÍCIO: Crio a Chave unica da Sessao para armazenamento e resgate das leituras e imagens que serão enviadas */
          $chave_unica = bin2hex(random_bytes(33) . $horario_completo_agora);
          /* Gerar strings aleatórias criptograficamente seguras, usamos 24 caracteres não repetitivos e aleatórios com criptografia nativa do PHP";
 serve como id referencial para salvar a midia e após salvar as leituras por essa chave que tbem constará na tb rmm, vinculo o id_rmm na tb midia_leitura,
  com a mesma chave unica (para controlar cada leitura enviada individualmente e não misturar as imagens enviadas) assim tbem poderemos vincular as midias 
  enviadas com um novo suporte gerado pelo painel da leitura e ver a relação entre elas, por imagens*/

          $cookie_name = "CHAVE_UNICA_SESSAO_ATUAL";
          $cookie_value = $chave_unica;

          // 86400 = 1 day
          setcookie($cookie_name, $cookie_value, time() + (86400 * 60), "/");






          /* FIM:  Crio a Chave unica da Sessao para armazenamento e resgate das leituras e imagens que serão enviadas */
          $estacao_logada = '';
          $retorno = array(
               'codigo' => 1,
               'nome_cliente' => $registro->nome_fantasia,
               'nome_plcode' => $registro->nome_ponto,
               'id_estacao' => $registro->id_estacao,
               'nome_estacao' => $registro->nome_estacao,
               'status_estacao' => $registro->status_estacao,
               'id_plcode_lido' => $registro->id_ponto,
               'status_plcode' => $registro->status_ponto,
               'id_suporte' => $registro->id_suporte,
               'status_suporte' => $registro->status_suporte,
               'motivo_suporte' => $registro->motivo_suporte
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


          echo json_encode($retorno);
     }
     $conexao = null;
     exit();
}
