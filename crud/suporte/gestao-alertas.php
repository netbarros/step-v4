<?php
/**
 * Gestão de Alertas
 * 
 * @package CRUD/Alertas
 * @category Controller
 * @version 1.0.3
 * @since 06/06/2023
 * 
 * Vai trabalhar independente, mas sempre será chamado por outro arquivo, que vai passar os parâmetros necessários para o funcionamento.
 */
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
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// ... variáveis ​​de ambiente


$twilioAccountSid = $_ENV['TWILIO_ACCOUNT_SID'];
$twilioAuthToken = $_ENV['TWILIO_AUTH_TOKEN'];
$twilioFromNumber = $_ENV['TWILIO_FROM_NUMBER'];
$totalVoiceToken = $_ENV['TOTAL_VOICE_TOKEN'];


/* Gestão Alertas nunca trabalha sozinho, é sempre uma dependência que será chamada para gerir e enviar os alertas, sempre de acordo
 com o Projeto (id_obra) e categoria_suporte (id_tipo_suporte) que são passados como parâmetros.
 Assim como $mensagem_alerta e $assunto, que são passados como parâmetros.
 Os usuários precisam ter Coleções de Notificação Criadas e Participarem do Projeto que originou o alerta.
CAMPOS OBIGATÓRIOS para chamar Gestão Alertas: (Estas variáveis devem estar antes do requere_once gestao_alertasp.php no arquivo de origem.)
>>>
 $id_obra = '38';
 $categoria_suporte = '112';
 $mensagem_alerta = 'Olá, o STEP alerta: Teste de envio, favor desconsiderar esta mensagem.';
 $assunto = 'Alerta de Suporte'; 
 $retorno_alerta = 'Retorno Alerta Completo: Olá, o STEP alerta: Teste de envio, favor desconsiderar esta mensagem.';	
 <<<
*/


function enviarNotificacoes($destinatario, $nome_destinatario, $retorno_alerta,
 $alerta_email, $alerta_sms, $alerta_whats, $mensagem_alerta, $assunto, $telefone_usuario_alerta,$tipo_suporte,$categoria_suporte)
{
    // As credenciais são definidas aqui, então não há necessidade de passá-las como argumentos para a função
    $twilioAccountSid = $_ENV['TWILIO_ACCOUNT_SID'];
    $twilioAuthToken = $_ENV['TWILIO_AUTH_TOKEN'];
    $twilioFromNumber = $_ENV['TWILIO_FROM_NUMBER'];
    $totalVoiceToken = $_ENV['TOTAL_VOICE_TOKEN'];

  


  
        $alerta_email = (int)$alerta_email;
        $tipo_suporte = (int)$tipo_suporte;
        $categoria_suporte = (int)$categoria_suporte;
    
        if ($alerta_email === 1 && $tipo_suporte === $categoria_suporte) {
            $nome_usuario = $nome_destinatario;
            $email_usuario = $destinatario;
            $mensagem_email = $mensagem_alerta; 
            $mensagem_email .= $retorno_alerta; 
            $assunto = $assunto;
            $horario_completo_agora = microtime();
            $chave_unica = bin2hex(random_bytes(33) . $horario_completo_agora);
            $template_email = '/views/emails/email-padrao.php';
           

            require_once  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';
          
    
            


        } else {

        $responseMessage = 'Erro ao enviar notificações via E-mail:  ' . $destinatario . ' - ' . $tipo_suporte . ' - ' . $categoria_suporte ;
        error_log("Erro ao validar Tipo de Suporte do Usuario  {$nome_destinatario} com o Tipo de Suporte :{$tipo_suporte} e email: {$destinatario} e categoria_suporte:{$categoria_suporte}" );
        }
   

    if ($alerta_sms=== 1 && $tipo_suporte===$categoria_suporte) {

        $client = new TotalVoiceClient($totalVoiceToken);
        $numero_destinatario = $telefone_usuario_alerta;

        $mensagem_alerta= $mensagem_alerta; // mensagem sem tags html

        $mensagem_alerta_sem_tags = strip_tags($mensagem_alerta);

        $mensagem_alerta_resumida = mb_substr($mensagem_alerta_sem_tags, 0, 160, 'UTF-8');

            $response = $client->sms->enviar($telefone_usuario_alerta, $mensagem_alerta_resumida);
                       
        
            if($response->getStatusCode() == 200) {
                
                $responseMessage = 'SMS Enviado com sucesso: '.$telefone_usuario_alerta.' - ' . $response->getContent();
    
                // Write the message to the PHP error log
                error_log($responseMessage);

            } else {
                $responseMessage = 'Erro ao Enviar SMS: '.$telefone_usuario_alerta.' - ' . $response->getContent();
    
                // Write the message to the PHP error log
                error_log($responseMessage);
            }
        
    }

    if ($alerta_whats=== 1 && $tipo_suporte===$categoria_suporte) {
        $twilio = new Client($twilioAccountSid, $twilioAuthToken);
        $numero_destinatario = $telefone_usuario_alerta;

        $mensagem_alerta= $mensagem_alerta; // mensagem sem tags html

        $mensagem_alerta_sem_tags = strip_tags($mensagem_alerta);

        $mensagem_alerta_resumida = mb_substr($mensagem_alerta_sem_tags, 0, 1500, 'UTF-8');

        try {
            $message = $twilio->messages
            ->create( "whatsapp:+5515981745522", //.$numero_destino (+14066923119 numero virtual sandbox Twilio -  para o número do usuário) +14155238886
            array(
                "from" => "whatsapp:+14155238886", //$twilioFromNumber",
                "body" => $mensagem_alerta
            )
            );

            if($message->sid) {
                $responseMessage = 'whatsapp Enviado com sucesso para o Destinatário Sistema : '.$telefone_usuario_alerta.' Destinatário Twilio: '.$message->to.' - ' . $message->sid." - ".$message->body.".";

                // Write the message to the PHP error log
                error_log($responseMessage);
            } else {
                    $responseMessage = 'Erro ao enviar whatsapp: '.$telefone_usuario_alerta.". Mensagem Erro Twilio: " . $message->error_message . ". Error Twilio:" . $message->error_code;
    
                    // Write the message to the PHP error log
                    error_log($responseMessage);
            }

            
        // print($message->sid); // id de retorno da mensagem do twilio, caso abra conversa com o cliente, dá inicio a outra api twilio para gerenciar conversas Whatsapp (não utilizada no momento - modo sendbox.)

        } catch (Exception $e) {
            // Trate o erro de forma adequada
            error_log('Erro ao enviar a mensagem do WhatsApp: ' . $e->getMessage());
        }
    }


}

/** Inicio da Consulta das Coleções de Notificações dos usuarios da obra (projeto) e tipo de suporte */

try {
   

    // Definir a consulta SQL
    $sql = "SELECT nu.id_notificacao_usuario, nu.alerta_sms, nu.alerta_email, nu.alerta_whats, u.id as id_usuario, u.nome, u.email, u.telefone, nu.id_tipo_suporte,nu.status_notificacao_usuario, nome_suporte
    FROM notificacoes_usuario nu
    INNER JOIN usuarios u ON u.id = nu.id_usuario
    INNER JOIN usuarios_projeto up ON up.id_usuario = u.id
    INNER JOIN tipo_suporte ts ON ts.id_tipo_suporte = nu.id_tipo_suporte
    WHERE u.status = 1 AND up.id_obra = :id_obra AND nu.id_obra = :id_obra AND nu.id_tipo_suporte = :categoria_suporte
    GROUP BY nu.id_notificacao_usuario, u.id;
    ";

    // Preparar a consulta
    $stmt = $conexao->prepare($sql);

    // Vincular os parâmetros
    $stmt->bindParam(':id_obra', $id_obra, PDO::PARAM_INT);
    $stmt->bindParam(':categoria_suporte', $categoria_suporte, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Buscar os resultados
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count= $stmt->rowCount();
    

    if($count==0) {
        $responseMessage = 'Nenhuma Coleção de Notificações criadas para receber notificações, Projeto: ' . $id_obra . ' Tipo de Suporte: ' . $categoria_suporte ;
       error_log("Nenhuma Coleção de Notificações criada para receber notificações para o Projeto: {$id_obra} com o Tipo de Suporte da Notificacao: {$categoria_suporte}" );

       $retorno = array('codigo' => 1,  'mensagem' => "Feito! Porém nenhuma Coleção de Notificações foi criada para receber notificações para o Projeto: {$id_obra}\n\r com o Tipo de Suporte da Notificacao: {$categoria_suporte}! ");
         echo json_encode($retorno);
         
           
    }else{

    // Processar os resultados
    foreach ($results as $row) {
        $status_notificacao_usuario = $row['status_notificacao_usuario'];
        if ($status_notificacao_usuario) { // Verifica se o usuário deseja receber notificações
            $id_notificacao_usuario = $row['id_notificacao_usuario'];
            $alerta_sms = $row['alerta_sms'];
            $alerta_email = $row['alerta_email'];
            $alerta_whats = $row['alerta_whats'];
            $id_usuario_alerta = trim($row['id_usuario']);
            $nome_destinatario = trim($row['nome']);
            $destinatario = preg_replace('/\s+/', ' ', trim($row['email']));
            $telefone_usuario_alerta = "+55" . preg_replace('/[^0-9]/', '', trim($row['telefone']));
            $tipo_suporte = $row['id_tipo_suporte'];

            

            // Chama a função enviarNotificacoes
            enviarNotificacoes($destinatario, $nome_destinatario, $retorno_alerta,
            $alerta_email, $alerta_sms, $alerta_whats, $mensagem_alerta, $assunto, $telefone_usuario_alerta,$tipo_suporte,$categoria_suporte);
        }

       /*  echo "ID Notificação: " . $id_notificacao_usuario . "<br>";
        echo "Usuário: " . $row['nome'] . " - " . $row['email'] . " - " . $row['telefone'] . " - " . $row['status_notificacao_usuario'] . " Nivel: ".$row['nivel']."<br>";	
        echo "Alerta SMS: " . $alerta_sms . " - Alerta Email: " . $alerta_email . " - Alerta Whatsapp: " . $alerta_whats . "<br><br><br>";
        echo "Mensagem: " . $mensagem_alerta . "<br>"; */
        
        	
    } 

    $retorno = array('codigo' => 1,  'mensagem' => "Feito! <br> E suas Notificações foram enviadas com sucesso! com o assunto: {$assunto}");
    echo json_encode($retorno);
    error_log("Suas Notificações foram enviadas com sucesso!." );
   
}

}catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

?>