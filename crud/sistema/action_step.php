<?php
 // buffer de saída de dados do php]
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conn = Conexao::getInstance();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
 }

require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

 // buffer de saída de dados do php]
header("content-type: application/json");




//===[ CHAVE ÚNICA da SESSAO] a cada sessao, o step registra uma codificação única.
$horario_completo_agora = microtime();
/* INÍCIO: Crio a Chave unica da Sessao para armazenamento e resgate das leituras e imagens que serão enviadas */
$chave_unica = bin2hex(random_bytes(33) . $horario_completo_agora);
/* Gerar strings aleatórias criptograficamente seguras, usamos 24 caracteres não repetitivos e aleatórios com criptografia nativa do PHP";
 serve como id referencial para salvar a midia e após salvar as leituras por essa chave que tbem constará na tb rmm, vinculo o id_rmm na tb midia_leitura,
  com a mesma chave unica (para controlar cada leitura enviada individualmente e não misturar as imagens enviadas) assim tbem poderemos vincular as midias 
  enviadas com um novo suporte gerado pelo painel da leitura e ver a relação entre elas, por imagens*/
/* FIM: Crio a Chave unica da Sessao para armazenamento e resgate das leituras e imagens que serão enviadas */


// Verifica se a ação é 'config_step'
if ($_POST['acao'] ?? '' === 'config_step') {

    $cerca_virtual = $_POST['Cerca_virtual'] ?? null;
    $sla_step = $_POST['SLA_Step'] ?? null;
    
    $cerca_virtual = filter_var($cerca_virtual, FILTER_VALIDATE_INT);
    $sla_step = filter_var($sla_step, FILTER_VALIDATE_INT);
    
    if ($cerca_virtual === false || $sla_step === false) {
        $response = [
            'codigo' => 0,
            'retorno' => 'Os campos Cerca_virtual e SLA_Step devem ser números inteiros'
        ];
        
        echo json_encode($response);
        exit;
    }

    // Obtém a data e hora atual
    $data_atualizacao = date("Y-m-d H:i:s");  // Configura a data e hora atual

    // Primeiro, verifica se já existe um registro na tabela
    $checkStmt = $conn->prepare("SELECT * FROM step_config");
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        // Se existir um registro, atualiza
        $sql = "UPDATE step_config SET sla_atendimento = :sla_atendimento, gps_metros = :gps_metros, data_atualizacao = :data_atualizacao, chave_unica = :chave_unica WHERE id_step = 1";
        $stmt = $conn->prepare($sql);
    } else {
        // Se não existir um registro, insere um novo
        $sql = "INSERT INTO step_config (sla_atendimento, gps_metros, data_atualizacao, chave_unica) VALUES (:sla_atendimento, :gps_metros, :data_atualizacao, :chave_unica)";
        $stmt = $conn->prepare($sql);
    }

    // Define parâmetros
    $stmt->bindParam(':sla_atendimento', $sla_step);
    $stmt->bindParam(':gps_metros', $cerca_virtual);
    $stmt->bindParam(':data_atualizacao', $data_atualizacao);
    $stmt->bindParam(':chave_unica', $chave_unica);

    // Executa a instrução preparada
    if ($stmt->execute()) {
        // Se a consulta foi bem-sucedida, retorna um JSON com o código 1
        echo json_encode(array("codigo" => 1, "retorno" => "Atualização bem-sucedida."));
    } else {
        // Se a consulta falhou, retorna um JSON com o código 0
        echo json_encode(array("codigo" => 0, "retorno" => "A atualização falhou."));
    }
} else {
    // Se a ação não for 'config_step', retorna um JSON com o código 0
    echo json_encode(array("codigo" => 0, "retorno" => "Ação inválida."));
}
?>