<?php
header("content-type: application/json");
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';
$conexao = Conexao::getInstance();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//===[ CHAVE ÚNICA da SESSAO]===
$data_chave = date("d-m-Y");
$hora_chave = date("H:i");
$pagina_ativa_chave = $_SESSION['pagina_atual'] ?? 'gerado_automatico';
$usuario_sessao_chave = $_SESSION['nome'] . '-' . ($_SESSION['pagina_atual'] ?? 'gerado_automatico');
$id_usuario_sessao_chave = $_SESSION['id'];
$chave_unica = $_COOKIE['CHAVE_UNICA_SESSAO_ATUAL'] ?? md5(sprintf("%s%s%s%s%s", $data_chave, $hora_chave, $pagina_ativa_chave, $usuario_sessao_chave, $id_usuario_sessao_chave));
/*===[ CHAVE ÚNICA da SESSAO]==== */

$status_suporte = $_POST['status_suporte'] ?? '';
// Recebe as variáveis via POST
$atender_suporte = $_POST['atender_suporte'] ?? '';
$acao = $_POST['acao'] ?? '';
$id_suporte = $_POST['id_suporte'] ?? '';
$tipo_suporte = $_POST['tipo_suporte'] ?? '';
$nome_suporte = $_POST['nome_tipo_suporte'] ?? '';
$nucleo_projeto = $_POST['nucleo_projeto'] ?? '';
$motivo_resolutiva = $_POST['motivo_resolutiva'] ?? '';
$data_prevista_post = isset($_POST['data_previsao_suporte']) ? trim($_POST['data_previsao_suporte']) : '';
$quem_atendeu = $_COOKIE['id_usuario_sessao'] ?? '';
$quem_fechou = $_COOKIE['id_usuario_sessao'] ?? '';
$data_close = date('Y-m-d H:i:s');
// Verifica se a variável está vazia

if (empty($data_prevista_post)) {
    // Cria um objeto DateTime com o valor padrão de 00:00:00
   
// Formata a data e hora no formato "Y-m-d H:i:s"
    $data_prevista =  date('Y-m-d H:i:s');

} else { $data_prevista = $data_prevista_post;}

// Função para atualizar suporte
function updateSuporte($conexao, $params, $sql) {
    $stmt = $conexao->prepare($sql);
    foreach ($params as $key => &$value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->execute();
    return $stmt; // Retorne o objeto de declaração preparada
}



// Funções auxiliares
function getPlcode($conexao, $id_suporte) {
    $query = $conexao->prepare("SELECT plcode FROM suporte WHERE id_suporte = :id_suporte");
    $query->bindParam(':id_suporte', $id_suporte);
    $query->execute();
    $resultado = $query->fetch(PDO::FETCH_ASSOC);
    return $resultado['plcode'] ?? null;
}

function updateStatus($conexao, $sql, $param) {
    $conexao->prepare($sql)->execute([$param]);
}

function inserirLogSuporte($conexao, $id_suporte, $quem_atendeu, $status_suporte, $motivo_suporte) {
    try {
        $sql = "INSERT INTO log_suporte (id_suporte, id_usuario, status_suporte, motivo_suporte_log) 
                VALUES (:id_suporte, :id_usuario, :status_suporte, :motivo_suporte)";
        $stmt = $conexao->prepare($sql);

        $stmt->bindParam(':id_suporte', $id_suporte, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $quem_atendeu, PDO::PARAM_INT);
        $stmt->bindParam(':status_suporte', $status_suporte, PDO::PARAM_STR);
        $stmt->bindParam(':motivo_suporte', $motivo_suporte, PDO::PARAM_STR);

        $stmt->execute();

        $count = $stmt->rowCount();

        if ($count > 0) {
            $ultimo_log_tratativa = $conexao->lastInsertId();
            return $ultimo_log_tratativa; // sucesso
        } else {
            return false; // falha
        }
    } catch (PDOException $e) {
        return "Erro: " . $e->getMessage();
    }
}



if ($acao == 'altera_ticket') {
    
    
    if($motivo_resolutiva=='' || $motivo_resolutiva==null){
        $motivo_resolutiva='Não Informado';
        $retorno = array('codigo' => 0, 'mensagem' => 'O Motivo da Tratativa não informado!<br> <br> Você sempre precisará informar o motivo da tratativa, ao alterar o status do ticket de suporte. <br> <br> Por favor, informe o motivo da tratativa e tente novamente.');
        echo json_encode($retorno);
        exit;
    }

    // Executa a ação correspondente
    switch ($atender_suporte) {
        case '2':
            $nome_status = "Atendimento Iniciado";
             // ... (Código para Atender o suporte)
            $sql = "UPDATE suporte SET quem_atendeu=:quem_atendeu, status_suporte = :atender_suporte, tipo_suporte = :tipo_suporte, motivo_resolutiva = :motivo_resolutiva WHERE id_suporte = :id_suporte";
            $params = [':quem_atendeu' => $quem_atendeu, ':atender_suporte' => $atender_suporte, ':tipo_suporte' => $tipo_suporte, ':motivo_resolutiva' => $motivo_resolutiva, ':id_suporte' => $id_suporte];
            $stmt = updateSuporte($conexao, $params, $sql);
            // insere tratativa:
            $resultado = inserirLogSuporte($conexao, $id_suporte, $quem_atendeu, $status_suporte, $motivo_resolutiva);

            break;

            case '3':
                $nome_status = "Suporte repassado à Terceiros";
                // ... (consulta e parâmetros semelhantes)
                $sql = "UPDATE suporte SET data_prevista = :data_prevista, quem_atendeu=:quem_atendeu, status_suporte = :atender_suporte, tipo_suporte = :tipo_suporte, motivo_resolutiva = :motivo_resolutiva WHERE id_suporte = :id_suporte";
                $params = [':data_prevista' => $data_prevista,  ':quem_atendeu' => $quem_atendeu, ':atender_suporte' => $atender_suporte,':tipo_suporte' => $tipo_suporte, ':motivo_resolutiva' => $motivo_resolutiva, ':id_suporte' => $id_suporte];
                $stmt = updateSuporte($conexao, $params, $sql);
                $resultado = inserirLogSuporte($conexao, $id_suporte, $quem_atendeu, $status_suporte, $motivo_resolutiva);
                break;


                case '4':
                    $nome_status = "Ticket de Suporte Finalizado";
                    // ... (Código para Finalizar o suporte)
                    $sql = "UPDATE suporte SET data_close = :data_close, quem_fechou=:quem_fechou, status_suporte = :atender_suporte, tipo_suporte = :tipo_suporte, motivo_resolutiva = :motivo_resolutiva WHERE id_suporte = :id_suporte";
                    $params = [':data_close' => $data_close,  ':quem_fechou' => $quem_fechou, ':atender_suporte' => $atender_suporte,':tipo_suporte' => $tipo_suporte, ':motivo_resolutiva' => $motivo_resolutiva, ':id_suporte' => $id_suporte];
                    $stmt = updateSuporte($conexao, $params, $sql);
        
                    $ID_Plcode_Suporte = getPlcode($conexao, $id_suporte);
                    $resultado = inserirLogSuporte($conexao, $id_suporte, $quem_atendeu, $status_suporte, $motivo_resolutiva);
        
                    switch ($tipo_suporte) {
        
                        case '91': // sistema parado
                            updateStatus($conexao, "UPDATE estacoes SET status_estacao = '1' WHERE id_estacao = ?", $nucleo_projeto);
                            break;
        
                        case '93': // PLCode com Problemas
                            updateStatus($conexao, "UPDATE pontos_estacao SET status_ponto = '1' WHERE id_ponto = ?", $ID_Plcode_Suporte);
                            break;
        
                        case '88': // Tubulação com Problemas
                            if ($ID_Plcode_Suporte) {
                                updateStatus($conexao, "UPDATE pontos_estacao SET status_ponto = '1' WHERE id_ponto = ?", $ID_Plcode_Suporte);
                            }
                            break;
        
                        default:
                            updateStatus($conexao, "UPDATE pontos_estacao SET status_ponto = '1' WHERE id_ponto = ?", $ID_Plcode_Suporte);
                            updateStatus($conexao, "UPDATE estacoes SET status_estacao = '1' WHERE id_estacao = ?", $nucleo_projeto);
                            break;
                    }
        
                    // finaliza a conversa relacionada ao suporte
                    $update_stmt = $conexao->prepare("UPDATE suporte_conversas SET status_conversa = 3 WHERE id_suporte = :id_suporte");
                    $update_stmt->bindParam(':id_suporte', $id_suporte);
                    $update_stmt->execute();
        
                    break;

        case '5':
            $nome_status = "Indicado prazo de Finalização";
            // ... (consulta e parâmetros semelhantes)
            $sql = "UPDATE suporte SET data_prevista = :data_prevista, quem_atendeu=:quem_atendeu,status_suporte = :atender_suporte, tipo_suporte = :tipo_suporte, motivo_resolutiva = :motivo_resolutiva WHERE id_suporte = :id_suporte";
            $params = [':data_prevista' => $data_prevista, ':quem_atendeu' => $quem_atendeu,':atender_suporte' => $atender_suporte, ':tipo_suporte' => $tipo_suporte, ':motivo_resolutiva' => $motivo_resolutiva, ':id_suporte' => $id_suporte];
            $stmt = updateSuporte($conexao, $params, $sql);
            $resultado = inserirLogSuporte($conexao, $id_suporte, $quem_atendeu, $status_suporte, $motivo_resolutiva);

            break;

       
        default:
            $retorno = array('codigo' => 0,  'mensagem' => "Ação Inválida.");
            echo json_encode($retorno);
            error_log("Ação Inválida ao tentar alterar o status do suporte via switch case.");
            exit;
    }

    if ($stmt->execute()) {
        
        $retorno = array('codigo' => 1, 'mensagem' => "Ticket ID: <span class='text-success'>{$id_suporte}</span>, Tipo Suporte: {$nome_suporte}, alterado para: <span class='text-warning'>{$nome_status}!</span> ");
        echo json_encode($retorno);
        $conexao=null;
        exit;


    } else {
        $erro = $conexao->errorInfo();
        $retorno = array('codigo' => 0, 'mensagem' => "Erro ao executar a ação: " . $erro[2]);
        echo json_encode($retorno);
        $conexao=null;
        exit;
    }



}




