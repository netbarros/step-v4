<?php
// Configurações de timezone e locale
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');


// Importar a classe PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

// Inicializar PHPMailer
$mail = new PHPMailer(true);

try {
     // Configurações de e-mail
     $mail->CharSet = 'UTF-8';
     $mail->clearAddresses();
     $mail->clearAttachments();
     $mail->isHTML(true);
     $mail->setFrom('noreply@step.eco.br', 'STEP');
     $mail->addReplyTo('webmaster@step.eco.br');
     $mail->addAddress($email_usuario);
     $mail->Subject = $assunto;
 
     // Configurações SMTP
     $mail->isSMTP(); // Use SMTP
     $mail->Host = 'mail.step.eco.br'; // Set the SMTP server to send through
     $mail->SMTPAuth = true; // Enable SMTP authentication
     $mail->Username = 'noreply@step.eco.br'; // SMTP username
     $mail->Password = 'Step#2023'; // SMTP password (change this)
     $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
     $mail->Port = 465; // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above ENCRYPTION_STARTTLS
 
    

    // Incluir template de e-mail
    ob_start();
    include($_SERVER['DOCUMENT_ROOT'] . $template_email);
    $message = ob_get_contents();
    ob_end_clean();

    // Definir o corpo da mensagem
    $mail->msgHTML($message);
    $mail->AltBody = $assunto;
    $mail->addAttachment($_SERVER['DOCUMENT_ROOT'] . '/tema/dist/assets/media/logos/logo-4-sm.png');

    // Enviar e-mail usando a função mail() do PHP
    //$mail->isMail(); // Use a função mail() do PHP em vez de SMTP
    $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
    if($mail->send()){
       // echo json_encode(['codigo' => '1', 'mensagem' => "E-mail enviado com sucesso para: {$email_usuario}"]);
    error_log("E-mail enviado com sucesso para: {$email_usuario}");
    error_log("Assunto: " . $assunto);
    }

} catch (Exception $e) {
    // Log de erros
    error_log("Erro ao enviar o e-mail para: {$email_usuario} - " . $mail->ErrorInfo);
    error_log("Exceção: " . $e->getMessage());
    echo json_encode(['codigo' => '0', 'mensagem' => "Ocorreu um erro ao enviar o e-mail: {$email_usuario} - " . $mail->ErrorInfo]);
    exit;
}
?>