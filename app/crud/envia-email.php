<?php
//ini_set("session.cookie_secure", 1);
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

$id_estacao='';

$ultimo_id_suporte = $ultimo_id_suporte ?? '0';
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$mail = new PHPMailer();
$mail->SMTPDebug = 0;
$mail->CharSet = "UTF-8";
$mail->Debugoutput = 'html';

$mail->setFrom('webmaster@step.eco.br', 'STEP');
$mail->addReplyTo('webmaster@step.eco.br');

// Limpa todos os endereços e anexos
//$mail->clearAddresses();
//$mail->clearAttachments();

$mail->addAddress('' . $email_usuario . '');
$mail->addAddress('webmaster@step.eco.br');

$mail->Subject = $assunto;
$mail->isHTML(true);
ob_start();

include($_SERVER['DOCUMENT_ROOT'] . $template_email);

$message = ob_get_contents();
ob_end_clean();  

$mail->msgHTML($message);
$mail->AltBody = $assunto;
$mail->addAttachment('https://step.eco.br/tema/dist/assets/media/logos/logo-4-sm.png');


try {
    $mail->send();
    // não precisa retornar nada se o e-mail for enviado com sucesso
} catch (Throwable $e) {
    echo json_encode(['codigo' => '0', 'mensagem' => 'Ocorreu um erro ao enviar o e-mail: ' . $mail->ErrorInfo]);
    exit;
}



/*

$mail->IsSMTP();
$mail->Host = "smtp.example.com";

// optional
// used only when SMTP requires authentication  
$mail->SMTPAuth = true;
$mail->Username = 'smtp_username';
$mail->Password = 'smtp_password';

*/

?>
