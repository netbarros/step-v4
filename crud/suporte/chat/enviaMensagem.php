<?php
header('Content-Type: application/json');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
// define o cabeçalho da resposta como JSON

// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';


$mensagem = $_GET['message'] ?? '';

$id_usuario_ativo = $_COOKIE['id_usuario_sessao'] ?? '';

// Parâmetros da query de inserção
$id_conversa = $_COOKIE['chat_id_conversa_suporte'] ?? ''; // exemplo
$id_suporte = $_COOKIE['chat_id_suporte_conversa'] ?? ''; // exemplo
$id_remetente = $id_usuario_ativo; // quem envia a mensagem
$destinatario_direto = $_COOKIE['chat_usuario_conversa'] ?? ''; // destinatario da mensagem

if($id_conversa=='undefined' || $id_conversa==''){

    $retorno = array('codigo' => 0, 'mensagem' => 'Você precisa Acessar um Ticket de Suporte, Selecionar o Usuário entre os envolvidos do Ticket, para dar início a um Chat entre os usuários relacionados.');
     
      echo json_encode($retorno);
      $conexao = null;
      exit;
}

if($destinatario_direto=='undefined' || $destinatario_direto==''){
    $retorno = array('codigo' => 0, 'mensagem' => 'Não foi possível enviar sua mensagem, verifique corretamente o Usuário selecionado.');
          echo json_encode($retorno);
          $conexao = null;
          exit;

}

if($mensagem!='' && $id_usuario_ativo!=''){

//**** status_log_conversa = 1 = nova | 2 = recebida | 3 = lida */

    // salva a mensagem no log da conversa do chat do suporte

 
// Preparando a query de inserção
$sql = 'INSERT INTO log_conversa (id_conversa,id_suporte, id_remetente, id_destinatario, conversa, status_log_conversa) VALUES (?, ?, ?, ?, ?, ?)';
$stmt = $conexao->prepare($sql);



$data_chat = date('Y-m-d H:i:s'); // exemplo: data atual
$status_log_conversa = 1; // 1 = nova | 2 = recebida | 3 = lida

// Executando a query de inserção
$stmt->execute([$id_conversa,$id_suporte, $id_remetente, $destinatario_direto, $mensagem, $status_log_conversa]);

$ultimo_id = $conexao->lastInsertId();

$sql = $conexao->query("SELECT nome FROM usuarios WHERE id='$destinatario_direto'");

if ($sql) {
    $result = $sql->fetch(PDO::FETCH_OBJ);

    if ($result) {
        $nome_destinatario_direto = $result->nome;
    } else {
        $nome_destinatario_direto = $destinatario_direto;
    }
} else {
    $nome_destinatario_direto = 'Erro ao consultar o banco de dados';
}


$retorno = array('codigo' => 1, 'mensagem' => "Sua mensagem foi enviada com sucesso para: $nome_destinatario_direto. Por favor, aguarde a resposta do usuário.");
echo json_encode($retorno);
$conexao = null;
exit;

}
