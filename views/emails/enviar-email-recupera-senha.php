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

echo "<html>

<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    <style>
        html,
        body {
            padding: 0;
            margin: 0;
        },
        div{
        font-size: 14px;
        font-family:Arial,Helvetica,sans-serif;
        }
    </style>
</head>
<div
    style='font-family:Arial,Helvetica,sans-serif; line-height: 1.5; font-weight: normal; font-size: 14px; color: #2F3044; min-height: 100%; margin:0; padding:0; width:100%; background-color:#edf2f7'>
    <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%'
        style='border-collapse:collapse;margin:0 auto; padding:0; max-width:700px' role='presentation'>
        <tbody>
            <tr>
                <td align='center' valign='center' style='text-align:center; padding: 40px'>
                    <a href='https://step.eco.br' rel='noopener' target='_blank'>
                        <img alt='Logo' src='https://step.eco.br/tema/dist/assets/media/logos/logo-4.png' />
                    </a>
                </td>
            </tr>
            <tr>
                <td align='left' valign='center'>
                    <div
                        style='text-align:left; margin: 0 20px; padding: 40px; background-color:#ffffff; border-radius: 6px'>
                        <!--begin:Email content-->
                        <div style='padding-bottom: 30px; font-size: 15px;'>
                            <strong>$Saudacao </strong>, $nome_usuario.<br>
                           E-mail para Recuperação de senha:
                        </div><br>
                      

                        <div style='padding-bottom: 30px; padding-top: 10px; font-size: 14px;'>
                        <b>Nome:</b> $nome_usuario <br>
                        <br>
                        <b>E-mail:</b> $email_usuario<br>
                        <br>
                         <b>Senha Inicial:</b> grupoep123 <br>
                        <br>
                        <b>Nível de Acesso</b>: $nivel_acesso <br>
                        </div>
    
                        <div style='padding-bottom: 40px;  padding-top: 30px; text-align:center;'>
                            <a href='https://step.eco.br/' rel='noopener'
                                style='text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle'
                                target='_blank'>Acessar STEP</a>
                        </div> <br>


                        <div style='padding-bottom: 40px;  padding-top: 30px; text-align:center;'>
                            <a href='https://webmail.step.eco.br/' rel='noopener'
                                style='text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle'
                                target='_blank'>Acessar WebMail</a>
                        </div> <br>
                       
                        <br>
                        
                        <div style='border-bottom: 1px solid #eeeeee; margin: 15px 0'></div>
                        <div style='padding-bottom: 50px; word-wrap: break-all;'>
                            <p style='margin-bottom: 10px; padding-top: 30px; font-size: 14px;'>
                            Este e-mail é confidencial segundo as políticas de Privacidade LGPD e Grupo EP.
                            </p>
                            
                           
                            <a href='https://step.eco.br/' rel='noopener'
                                target='_blank'
                                style='text-decoration:none;color: #009EF7'>https://step.eco.br/</a>
                        </div>
                       
                              <!--end:Email content--><br>
                        <div style='padding-bottom: 10px; font-size: 12px;text-decoration:none;color: #008000'>Atenciosamente, <br>
                              <a href='https://step.eco.br' rel='noopener' target='_blank'>
                        <img alt='Logo' src='https://step.eco.br/app/assets-v2/media/logos/logo-4-sm.png' />
                    </a>

            <tr>
                <td align='center' valign='center'
                    style='font-size: 13px; text-align:center;padding: 20px; color: #6d6e7c;'>
                Enviado: $data_envio as $hora_envio
                        <a href='https://step.eco.br' rel='noopener' target='_blank'>STEP</a>.
                    </p>
                </td>
            </tr>
             <tr>
                <td align='center' valign='center'
                    style='font-size: 13px; text-align:center;padding: 20px; color: #6d6e7c;'>
                    <p>STEP - Sistema de Tratamento EP</p>
                    <p>Chave Única de Controle do Suporte: $chave_unica</p>
                        <a href='https://step.eco.br' rel='noopener' target='_blank'>STEP</a>.
                    </p>
                    <p style='color: #6d6e7c; font-size: 13px'>Este é um e-mail confidencial e está sujeito às leis de proteção de dados, incluindo a LGPD. Se você não é o destinatário deste e-mail, por favor, notifique imediatamente o remetente e apague todas as cópias deste e-mail.</p>
                </td>
            </tr><br><br>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</div>

</html>";
