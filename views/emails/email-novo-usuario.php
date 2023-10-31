<?php

$hr = date(" H ");
if($hr >= 12 && $hr<18) {
$Saudacao = "Boa tarde!";}
else if ($hr >= 0 && $hr <12 ){
$Saudacao = "Bom dia!";}
else {
$Saudacao = "Boa noite!";}

$data_envio = date('d/m/Y');
$hora_envio = date('H:i:s');

echo '<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.5; font-weight: normal; font-size: 15px; color: #2F3044; margin: 0; padding: 0; width: 100%; background-color: #edf2f7;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin: 0 auto; padding: 0; max-width: 600px; border-radius: 6px; background-color: #EDEDED;">
        <tbody>
            <tr>
                <td align="center" valign="center" style="text-align: center; padding: 40px;">
                    <a href="https://step.eco.br" rel="noopener" target="_blank">
                        <img alt="Logo" src="https://step.eco.br/tema/dist/assets/media/logos/logo-4.png" width="100"/>
                    </a>
                    <a href="https://grupoep.com.br/ep-tech/" rel="noopener" target="_blank">
                        <img alt="Logo" src="https://step.eco.br/tema/dist/assets/media/logos/grupo-ep-wg.png" width="100"/>
                    </a>
                    <h2 style="color: #4B8333; font-weight: bold;">Sistema de Tratamento EP</h2>
                </td>
            </tr>
            <tr>
                <td align="left" valign="center" style="text-align: left; margin: 0 20px; padding: 40px; background-color: #ffffff; border-radius: 6px;">
                    <div style="padding-bottom: 30px; font-size: 15px;">
                        <strong>' . $Saudacao . '</strong>, ' . $nome_usuario . '.<br><br>
                        Bem-vindo ao STEP!
                    </div>
                    <div style="padding-bottom: 30px; padding-top: 10px; font-size: 14px; border: 2px solid #4B8333; border-radius: 8px; background-color: #f9f9f9; padding: 15px;">
                        <p style="font-weight: bold; color: #4B8333; font-size: 16px; margin-top: 0;">Dados Importantes:</p>
                        <p style="margin-bottom: 10px;"><span style="font-weight: bold; color: #2F3044;">Nome:</span> ' . $nome_usuario . '</p>
                        <p style="margin-bottom: 10px;"><span style="font-weight: bold; color: #2F3044;">E-mail:</span> ' . $email_usuario . '</p>
                        <p style="margin-bottom: 10px;"><span style="font-weight: bold; color: #2F3044;">Senha Inicial:</span> <span style="background-color: #FFE7E7; color: #D93025; padding: 5px; border-radius: 4px; font-weight: bold; color: #D93025;">Grupoep123</span></p>
                        <p style="margin-bottom: 10px;"><span style="font-weight: bold; color: #2F3044;">Nível de Acesso:</span> ' . $nivel_acesso . '</p>
                    </div>
                    <div style="padding-bottom: 20px; padding-top: 20px; text-align:center;">
                    <a href="https://step.eco.br/" rel="noopener" class="btn" target="_blank" style="text-decoration: none; display: inline-block; text-align: center; padding: 0.75575rem 1.3rem; font-size: 0.925rem; line-height: 1.5; border-radius: 0.35rem; color: #ffffff; background-color: #4B8333; border: 0px; margin-right: 20px; font-weight: 600; outline: none; vertical-align: middle;">Acessar STEP</a>
                    <a href="https://webmail.step.eco.br/" rel="noopener" class="btn" target="_blank" style="text-decoration: none; display: inline-block; text-align: center; padding: 0.75575rem 1.3rem; font-size: 0.925rem; line-height: 1.5; border-radius: 0.35rem; color: #ffffff; background-color: #4B8333; border: 0px; font-weight: 600; outline: none; vertical-align: middle;">Acessar WebMail</a>
                </div>
                
                    </td>
            </tr>
            <tr>
                <td align="center" valign="center" style="font-size: 10px; text-align: center; padding: 20px; color: #6d6e7c;">
                    <p>STEP - Sistema de Tratamento EP</p>
                    '.$chave_unica.'
                    <p>Copyright © <a href="https://step.eco.br" rel="noopener" target="_blank">STEP</a>.</p>
                    <p style="color: #6d6e7c; font-size: 13px">Este é um e-mail confidencial e está sujeito às leis de proteção de dados, incluindo a LGPD. Se você não é o destinatário deste e-mail, por favor, notifique imediatamente o remetente e apague todas as cópias deste e-mail.</p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>';

?>
