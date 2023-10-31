<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/total-voice/autoload.php';
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');


use PHPMailer\PHPMailer\Exception;
use TotalVoice\Client as TotalVoiceClient;
use Twilio\Rest\Client;


// Carregue o arquivo .env
// Carregue o arquivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// ... carregue as variáveis ​​de ambiente



/** Gestão Alertas nunca trabalha sozinho, é sempre uma dependência que será chamada para gerir e enviar os alertas, sempre de acordo
 * com o Projeto (id_obra) e categoria_suporte (id_tipo_suporte) que são passados como parâmetros.
 * Assim como $mensagem_alerta e $assunto, que são passados como parâmetros.
 * Os usuários precisam ter Coleções de Notificação Criadas e Participarem do Projeto que originou o alerta.
*
* $id_obra = '38';
* $categoria_suporte = '24';
* $mensagem_alerta = 'Olá, o STEP alerta: Teste de envio, favor desconsiderar esta mensagem.';
* $assunto = 'Alerta de Suporte'; */


function enviarNotificacoes($email_usuario_alerta, $nome_usuario_alerta, $retorno_alerta,
 $alerta_email, $alerta_sms, $alerta_whats, $mensagem_alerta, $assunto, $telefone_usuario_alerta)
{
    // As credenciais são definidas aqui, então não há necessidade de passá-las como argumentos para a função
    $twilioAccountSid = $_ENV['TWILIO_ACCOUNT_SID'];
    $twilioAuthToken = $_ENV['TWILIO_AUTH_TOKEN'];
    $twilioFromNumber = $_ENV['TWILIO_FROM_NUMBER'];
    $totalVoiceToken = $_ENV['TOTAL_VOICE_TOKEN'];

  


    if ($alerta_email) {
        $nome_usuario = $nome_usuario_alerta;
        $email_usuario = $email_usuario_alerta;
        $mensagem_email = $retorno_alerta; // mensagem completa com tags html
        $mensagem_alerta= $mensagem_alerta; // mensagem sem tags html
        $assunto = $assunto;
        $template_email = '/views/emails/email-api.php';

        include $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';
    }

    if ($alerta_sms) {

        $client = new TotalVoiceClient($totalVoiceToken);
        $numero_destinatario = $telefone_usuario_alerta;

      
        $mensagem_alerta_sem_tags = strip_tags($mensagem_alerta);

        $mensagem_alerta_resumida = mb_substr($mensagem_alerta_sem_tags, 0, 160, 'UTF-8');



        try {
            $response = $client->sms->enviar($telefone_usuario_alerta, $mensagem_alerta_resumida);
            // Adiciona a mensagem de sucessi no arquivo de log
            $logFile = 'error_log_total_voice.txt';
            $retorno = $response->getContent();
            file_put_contents($logFile, $retorno, FILE_APPEND);
            // print_r($response); // id de retorno da mensagem do totalvoice
            
        } catch (Exception $e) {
            // Trate o erro de forma adequada
            $logFile = 'error_log_total_voice.txt';
            $errorMessage = "Erro ao enviar SMS: " . $e->getMessage() . " - " . date('Y-m-d H:i:s') . PHP_EOL;
            $retorno = $response->getContent();
            // Adiciona a mensagem de erro no arquivo de log
            file_put_contents($logFile, $errorMessage, $retorno, FILE_APPEND);
        }
    }

    if ($alerta_whats) {
        $twilio = new Client($twilioAccountSid, $twilioAuthToken);
        $numero_destinatario = $telefone_usuario_alerta;

        try {
            $message = $twilio->messages
            ->create("whatsapp:".$numero_destinatario, // to
            array(
                "from" => "whatsapp:$twilioFromNumber",
                "body" => $mensagem_alerta
            )
            );

            
        // print($message->sid); // id de retorno da mensagem do twilio, caso abra conversa com o cliente, dá inicio a outra api twilio para gerenciar conversas Whatsapp (não utilizada no momento - modo sendbox.)

        } catch (Exception $e) {
            // Trate o erro de forma adequada

             // Trate o erro de forma adequada
        $logFile = 'error_log_twilio.txt';
        $errorMessage = "Erro ao enviar mensagem no WhatsApp: " . $e->getMessage() . " - " . date('Y-m-d H:i:s') . PHP_EOL;

        // Adiciona a mensagem de erro no arquivo de log
        file_put_contents($logFile, $errorMessage, FILE_APPEND);
        }
    }


}

/** Inicio da Consulta das Coleções dos usuarios da obra (projeto) e tipo de suporte */


$sql = "SELECT nu.*, u.nome, u.email, u.telefone, up.id_obra as projeto_user, up.id_usuario, nu.status_notificacao_usuario FROM notificacoes_usuario nu
INNER JOIN usuarios u ON u.id = nu.id_usuario
INNER JOIN usuarios_projeto up ON up.id_usuario = u.id
WHERE u.status = 1 AND up.id_obra = :id_obra  AND nu.id_obra = :id_obra AND nu.id_tipo_suporte = :categoria_suporte;  ";

$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id_obra', $id_obra, PDO::PARAM_INT);
$stmt->bindParam(':categoria_suporte', $categoria_suporte, PDO::PARAM_INT);
$stmt->execute();

$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

if($resultado){
    foreach ($resultado as $row) {
        $status_notificacao_usuario = $row['status_notificacao_usuario'];
        if ($status_notificacao_usuario) { // Verifica se o usuário deseja receber notificações
            $alerta_sms = $row['alerta_sms'];
            $alerta_email = $row['alerta_email'];
            $alerta_whats = $row['alerta_whats'];
            $id_usuario_alerta = trim($row['id_usuario']);
            $nome_usuario_alerta = trim($row['nome']);
            $email_usuario_alerta = preg_replace('/\s+/', ' ', trim($row['email']));
            $telefone_usuario_alerta = "+55" . preg_replace('/\s+/', '', trim($row['telefone']));

            // Chama a função enviarNotificacoes
            enviarNotificacoes($email_usuario_alerta, $nome_usuario_alerta, $retorno_alerta, $alerta_email, $alerta_sms, $alerta_whats, $mensagem_alerta, $assunto, $telefone_usuario_alerta);
        }
    } 
}