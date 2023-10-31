<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header("Access-Control-Max-Age: 3600");
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

//$mail->Debugoutput = 'html';
//Set who the message is to be sent from
$mail->setFrom('webmaster@step.eco.br', 'STEP');
//Set an alternative reply-to address
//$mail->addReplyTo('webmaster@step.eco.br');
$mail->addReplyTo('webmaster@step.eco.br');
//Set who the message is to be sent to
$mail->addAddress(''.$email_para.'');
$mail->addAddress('webmaster@step.eco.br');
//$mail->addCC('webmaster@step.eco.br'); //Add a more recipient
//Set the subject line
$mail->Subject = 'STEP - Recupera de Senha';
//Read an HTML message body from an external file, convert referenced images to embedded,
$mail->isHTML(true);
ob_start();
include($_SERVER['DOCUMENT_ROOT'].'/app/template_email/enviar-email-recupera-senha.php');

$message = ob_get_contents();

$mail->msgHTML($message);
//convert HTML into a basic plain-text alternative body
//$mail->msgHTML(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/app/template_email/email-default.php'), __DIR__);
//Replace the plain text body with one created manually
$mail->AltBody = 'STEP - Nova Leitura!';
//Attach an image file
$mail->addAttachment($_SERVER['DOCUMENT_ROOT'].'/v2/assets/media/logos/logo-4-sm.png');

//send the message, check for errors
if (!$mail->send()) {


    //  Gera Log da Envio do Email
            $acao_log = "LOG Erro ao Enviar Email de Recuperação de Senha";
            $tipo_log = '52'; // Envio Automático de E-mail para o Suporte, Falha no Envio do Email!

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
                $chave_unica,
                $id_operador,
                "Falha ao Enviar E-mail  para o ".$nome_para.", no email: ".$email_para."Mailer Error: " . $mail->ErrorInfo,
                $acao_log,
                $id_operador,
                $id_operador,
                $tipo_log ]);
//  Gera Log da Envio do Email


} else {



    //  Gera Log da Envio do Email
            $acao_log = "LOG Email de Recuperação de Senha enviado com Sucesso";
            $tipo_log = '52'; // Envio Automático de E-mail para o de recuperação de senha
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
                $chave_unica,
                $id_operador,
                "E-mail enviado com sucesso para o ".$nome_para.", no email: ".$email_para."",
                $acao_log,
                $id_operador,
                $id_operador,
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

