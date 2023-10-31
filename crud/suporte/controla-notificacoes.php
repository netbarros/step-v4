<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';
header("content-type: application/json");
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');


//===[ CHAVE ÚNICA da SESSAO] a cada sessao, o step registra uma codificação única.
// Obtenha a data atual no formato dd-mm-yyyy
$data_chave = date("d-m-Y");

// Obtenha a hora atual no formato hh:mm
$hora_chave = date("H:i");

$pagina_ativa_chave = $_SESSION['pagina_atual'] ?? 'gerado_automatico';

// Acrescenta o hífen na concatenação
$usuario_sessao_chave = $_SESSION['nome'] . '-' . ($_SESSION['pagina_atual'] ?? 'gerado_automatico');

$id_usuario_sessao_chave = $_SESSION['id'];

// Crie a chave única
$chave_unica = $_COOKIE['CHAVE_UNICA_SESSAO_ATUAL'] ?? md5($data_chave . $hora_chave . $pagina_ativa_chave . $usuario_sessao_chave . $id_usuario_sessao_chave);
/*===[ CHAVE ÚNICA da SESSAO]==== */


$acao = trim($_POST['acao']) ?? '';
$idProjeto = trim($_POST['idProjeto']) ?? '';
$NomeProjeto = trim($_POST['NomeProjeto']) ?? '';
$idTipoSuporte = trim($_POST['idTipoSuporte']) ?? '';
$NomeTipoSuporte = trim($_POST['NomeTipoSuporte']) ?? '';
$TipoNotificacao = trim($_POST['TipoNotificacao']) ?? '';


// Define uma lista de nomes de colunas permitidos para os TipoNotificacoes
$colunasPermitidas = ['alerta_email', 'alerta_sms', 'alerta_whats','periodo_verificacao'];
// Verifique se $TipoNotificacao está na lista de colunas permitidas
if (!in_array($TipoNotificacao, $colunasPermitidas)) {
    // Retorne um erro se $TipoNotificacao não for válido
    $retorno = array('codigo' => 0, 'mensagem' => "Tipo de Notificação Inválido");
    echo json_encode($retorno);
    exit;
}


if($acao=='update_notificacao'){

// Verificar se a variável de sessão ou o cookie estão definidos e não vazios
$usuario_ativo = !empty($_COOKIE['id_usuario_sessao']) ? $_COOKIE['id_usuario_sessao'] : '';

$session_id = !empty($_SESSION['id']) ? $_SESSION['id'] : '';

// Atribuir o valor da sessão caso o cookie esteja vazio
if ($usuario_ativo === '') {
    $usuario_ativo = $session_id;
}

// Verificar se o usuário ativo ainda está vazio e retornar um erro, se necessário
if ($usuario_ativo === '') {
    $retorno = array('codigo' => 0, 'mensagem' => "Usuário Solicitando não Encontrado - SQL ERROR");
    echo json_encode($retorno);
    exit;
}


try {
 // Consulta para verificar a notificação atual
 $stmt = $conexao->prepare("SELECT id_notificacao_usuario, $TipoNotificacao FROM notificacoes_usuario WHERE id_usuario = :usuario_ativo AND id_obra = :idProjeto AND id_tipo_suporte = :idTipoSuporte");
 $stmt->bindParam(':usuario_ativo', $usuario_ativo);
 $stmt->bindParam(':idProjeto', $idProjeto);
 $stmt->bindParam(':idTipoSuporte', $idTipoSuporte);
 $stmt->execute();

} catch(PDOException $e) {
    echo "Erro ao selecionar a Coleção existente do usuário: " . $e->getMessage();
}

// Se o usuário existir, atualize o valor da coluna 'TipoNotificacao'
if ($stmt->rowCount() > 0) {
    $resultado_notificacao = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica o status atual da notificação
    $TipoNotificacao_novo = ($resultado_notificacao[$TipoNotificacao] == 1) ? 0 : 1;
    $mensagem_inicio = ($TipoNotificacao_novo == 1) ? '<span class="text-success fs-4" >Parabéns</span>, você já está recebendo notificações <span class="text-warning fs-4" >'.$TipoNotificacao.'</span>, para tickets de <span class="text-warning fs-4" >'.$NomeTipoSuporte.'</span> sobre este projeto: <span class="text-success  fw-bold fs-4" >'.$NomeProjeto.'</span>!' 
    : 'Você está <span class="text-danger fw-bold fs-4" >desabilitando</span> notificações por <span class="text-warning fs-4" >'.$TipoNotificacao.',</span> para tickets de <span class="text-warning fs-4" >'.$NomeTipoSuporte.' </span>, sobre este projeto: <span class="text-danger fs-4" >'.$NomeProjeto.'</span>!';

    // Atualiza o status da notificação
    $stmt = $conexao->prepare("UPDATE notificacoes_usuario SET $TipoNotificacao = $TipoNotificacao_novo WHERE id_usuario = :usuario_ativo AND id_obra = :idProjeto AND id_tipo_suporte = :idTipoSuporte");
    $stmt->bindParam(':usuario_ativo', $usuario_ativo);
    $stmt->bindParam(':idProjeto', $idProjeto);
    $stmt->bindParam(':idTipoSuporte', $idTipoSuporte);
    $stmt->execute();

    $retorno = array('codigo' => 1, 'mensagem' => $mensagem_inicio);
    echo json_encode($retorno);
    exit;
    
} else {
        // Caso o usuário não exista na tabela notificacoes_usuario, insere o usuário e o tipo de notificacao
        // Seu código de inserção aqui

         // Caso o usuário não exista na tabela notificacoes_usuario, insere o usuário e o tipo de notificacao
    try {
        // Inserir um novo registro
        $TipoNotificacao_novo = 1; // Suponho que você queira habilitar a notificação por padrão
        $stmt = $conexao->prepare("INSERT INTO notificacoes_usuario (id_usuario, id_obra, id_tipo_suporte, $TipoNotificacao, data_atualizacao, status_notificacao_usuario) VALUES (:usuario_ativo, :idProjeto, :idTipoSuporte, :TipoNotificacao_novo, NOW(), 1)");
        $stmt->bindParam(':usuario_ativo', $usuario_ativo);
        $stmt->bindParam(':idProjeto', $idProjeto);
        $stmt->bindParam(':idTipoSuporte', $idTipoSuporte);
        $stmt->bindParam(':TipoNotificacao_novo', $TipoNotificacao_novo);
        $stmt->execute();
        $mensagem_inicio = '<span class="text-success fs-4" >Parabéns</span>, você está agora recebendo notificações por <span class="text-warning fs-4" >'.$TipoNotificacao.'</span>, para tickets de <span class="text-primary fs-4" >'.$NomeTipoSuporte.'</span> sobre este projeto: <span class="text-success fs-4" >'.$NomeProjeto.'</span>!';

        $retorno = array('codigo' => 1, 'mensagem' => $mensagem_inicio);
        echo json_encode($retorno);

    } catch (PDOException $e) {
        echo "Erro na criação de nova Coleção: " . $e->getMessage();
    }




    }





} // finaliza update notificacoes nas colecoes do ususario


// inicia do periodo para a colecao do usuario
if($acao=='update_periodo_notificacao'){

    $Periodo_Verificacao = trim($_POST['Periodo_Verificacao']) ?? '';
    $Nome_Periodo = trim($_POST['Nome_Periodo']) ?? '';

    if($Periodo_Verificacao == '' || $Periodo_Verificacao == '0'){
        $retorno = array('codigo' => 0, 'mensagem' => "Selecione o Periodo que deseja ser Notificado<br> para o <b>Tipo de Suporte:</b> <span class='text-warning'>".$NomeTipoSuporte."</span>.<br>Para Prosseguir!");
        echo json_encode($retorno);
        exit;
    }

    // Verificar se a variável de sessão ou o cookie estão definidos e não vazios
    $usuario_ativo = !empty($_COOKIE['id_usuario_sessao']) ? $_COOKIE['id_usuario_sessao'] : '';
    
    $session_id = !empty($_SESSION['id']) ? $_SESSION['id'] : '';
    
    // Atribuir o valor da sessão caso o cookie esteja vazio
    if ($usuario_ativo === '') {
        $usuario_ativo = $session_id;
    }
    
    // Verificar se o usuário ativo ainda está vazio e retornar um erro, se necessário
    if ($usuario_ativo === '') {
        $retorno = array('codigo' => 0, 'mensagem' => "Usuário Solicitando não Encontrado - SQL ERROR");
        echo json_encode($retorno);
        exit;
    }
    
    
    try {
     // Consulta para verificar a notificação atual
     $stmt = $conexao->prepare("SELECT id_notificacao_usuario FROM notificacoes_usuario WHERE id_usuario = :usuario_ativo AND id_obra = :idProjeto AND id_tipo_suporte = :idTipoSuporte");
     $stmt->bindParam(':usuario_ativo', $usuario_ativo);
     $stmt->bindParam(':idProjeto', $idProjeto);
     $stmt->bindParam(':idTipoSuporte', $idTipoSuporte);
     $stmt->execute();
    
    } catch(PDOException $e) {
        echo "Erro ao selecionar a Coleção do periodo do usuário: " . $e->getMessage();
    }
    
    // Se o usuário existir, atualize o valor da coluna 'TipoNotificacao'
    if ($stmt->rowCount() > 0) {

       

        $resultado_notificacao = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Verifica o status atual da notificação
        
        $mensagem_inicio = '<span class="text-success fs-4" >Parabéns</span>, seu Período de Verificação foi alterado para <span class="text-warning fs-4" >'.$Nome_Periodo.'</span>, para o Tipo de Suporte <span class="text-warning fs-4" >'.$NomeTipoSuporte.'</span> e o projeto: <span class="text-success  fw-bold fs-4" >'.$NomeProjeto.'</span> !';
    
    
        // Atualiza o status da notificação
        $stmt = $conexao->prepare("UPDATE notificacoes_usuario SET periodo_verificacao = :Periodo_Verificacao WHERE id_usuario = :usuario_ativo AND id_obra = :idProjeto AND id_tipo_suporte = :idTipoSuporte");
        $stmt->bindParam(':Periodo_Verificacao', $Periodo_Verificacao);
        $stmt->bindParam(':usuario_ativo', $usuario_ativo);
        $stmt->bindParam(':idProjeto', $idProjeto);
        $stmt->bindParam(':idTipoSuporte', $idTipoSuporte);
        

        $stmt->execute();
    
        $retorno = array('codigo' => 1, 'mensagem' => $mensagem_inicio);
        echo json_encode($retorno);
        exit;
        
    } else {
            // Caso o usuário não exista na tabela notificacoes_usuario, insere o usuário e o tipo de notificacao
            // Seu código de inserção aqui
    
             // Caso o usuário não exista na tabela notificacoes_usuario, insere o usuário e o tipo de notificacao
        try {
            // Inserir um novo registro
           
            $stmt = $conexao->prepare("INSERT INTO notificacoes_usuario (id_usuario, id_obra, id_tipo_suporte, periodo_verificacao, data_atualizacao, status_notificacao_usuario)
             VALUES (:usuario_ativo, :idProjeto, :idTipoSuporte, :Periodo_Verificacao,  NOW(), 1)");
            $stmt->bindParam(':usuario_ativo', $usuario_ativo);
            $stmt->bindParam(':idProjeto', $idProjeto);
            $stmt->bindParam(':idTipoSuporte', $idTipoSuporte);
            $stmt->bindParam(':Periodo_Verificacao', $Periodo_Verificacao);
            $stmt->execute();
            $mensagem_inicio = '<span class="text-success fs-4" >Parabéns</span>, Agora você receberá Notificações  para o Tipo de Suporte <span class="text-warning fs-4" >'.$NomeTipoSuporte.'</span> e o projeto: <span class="text-success  fw-bold fs-4" >'.$NomeProjeto.'</span>, no período de <span class="text-warning fs-4" >'.$Nome_Periodo.'</span>, !';
    
            $retorno = array('codigo' => 1, 'mensagem' => $mensagem_inicio);
            echo json_encode($retorno);
    
        } catch (PDOException $e) {
            echo "Erro na criação de da Coleção para incluir o Período o Tipo de Suporte selecionado. " . $e->getMessage();
        }
    
    
    
    
        }
    
    
    
    
    
    } // finaliza update do periodo para a colecao do usuario

