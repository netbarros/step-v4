<?php
//header("Content-Type: multipart/form-data; charset=utf-8");
/**
 * This example shows sending a message using PHP's mail() function.
 */
 require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
//ini_set("session.cookie_secure", 1);
if (!isset($_SESSION)) session_start();
date_default_timezone_set('America/Sao_Paulo');


//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

//Create a new PHPMailer instance
$mail = new PHPMailer();

$mail->SMTPDebug = 0;

//$mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str";};
//$mail->Debugoutput = 'html';
//Set who the message is to be sent from
$mail->setFrom('webmaster@step.eco.br', 'STEP');
//Set an alternative reply-to address

//$mail->addReplyTo('webmaster@step.eco.br');
$mail->addReplyTo('webmaster@step.eco.br');
//Set who the message is to be sent to
$mail->addAddress(''.$email_para.'');
//Set the subject line
$mail->addAddress('webmaster@step.eco.br');//Add a more recipient
$mail->Subject = 'Bem vindo ao STEP!';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body

ob_start();
include($_SERVER['DOCUMENT_ROOT'].'/app/template_email/email-novo-usuario.php');

$message = ob_get_contents();

$mail->msgHTML($message);

//$mail->msgHTML(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/app/template_email/email-checkin.php'), __DIR__);
//Replace the plain text body with one created manually
$mail->AltBody = 'Bem vindo ao STEP!';
$mail->isHTML(true);
//Attach an image file
$mail->addAttachment($_SERVER['DOCUMENT_ROOT'].'/v2/assets/media/logos/logo-4-sm.png');

//send the message, check for errors
if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;

    echo 'Email Enviado!';
    echo "\n\r";

    echo "Para:".$nome_para."no email:".$email_para;

    //  Gera Log da Envio do Email
            $acao_log = "LOG Falha ao Enviar Email Boas Vindas";
            $tipo_log = '43'; // Envio Automático de E-mail para o Suporte, Falha no Envio do Email!

            $sql_log = "INSERT INTO log_leitura (
            chave_unica,
            id_usuario, 
            acao,
            acao_log,
            id_acao_log,
            estacao_logada,
            tipo_log) 
            VALUES (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
                )";
            $conexao->prepare($sql_log)->execute([
                $chave_unica_suporte,
                $ultimo_id_usuario,
                "Falha ao Enviar E-mail  para o $nome_para, no email: $email_para".' Mailer Error: ' . $mail->ErrorInfo,
                $acao_log,
                $log_usuario,
                $filial,
                $tipo_log ]);
//  Gera Log da Envio do Email


} else {
    echo 'Email Enviado!';
    echo "\n\r";

    echo "Para:".$nome_para."no email:".$email_para;


    //  Gera Log da Envio do Email
            $acao_log = "LOG Email enviado com Sucesso Boas Vindas";
            $tipo_log = '42'; // Envio Automático de E-mail para o Suporte, Email enviado com Sucesso!

            $sql_log = "INSERT INTO log_leitura (
            chave_unica,
            id_usuario, 
            acao,
            acao_log,
            id_acao_log,
            estacao_logada,
            tipo_log) 
            VALUES (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
                )";
            $conexao->prepare($sql_log)->execute([
                $chave_unica_suporte,
                $ultimo_id_usuario,
                "E-mail enviado com sucesso para o $nome_para, no email: $email_para",
                $acao_log,
               $log_usuario,
                $filial,
                $tipo_log ]);
//  Gera Log da Envio do Email


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

