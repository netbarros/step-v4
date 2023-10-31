<?php
//header("Content-Type: multipart/form-data; charset=utf-8");
echo "<html>

<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    <style>
        html,
        body {
            padding: 0;
            margin: 0;
        }
        div{
        font-size: 14px;
      font-family:Arial,Helvetica,Verdana,sans-serif;
        }
    </style>
</head>
<div
    style='font-family:Arial,Helvetica,Verdana,sans-serif line-height: 1.5; font-weight: normal; font-size: 14px; color: #2F3044; min-height: 100%; margin:0; padding:0; width:100%; background-color:#edf2f7'>
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
                            <strong>$Saudacao </strong><br><br>
                            Olá $nome_usuario, o STEP têm uma nova notificação:
                        </div>

                        <div style='padding-bottom: 10px; padding-top: 30px; font-size: 14px; font-family:Arial,Helvetica,Verdana,sans-serif; line-height: 1.5; font-weight: normal;'><b>Nº Checkin:</b> $ID_Checkin</div>

                         <div style='padding-bottom: 10px; padding-top: 10px; font-size: 14px; font-family:Arial,Helvetica,Verdana,sans-serif; line-height: 1.5; font-weight: normal;'>$nome_parametro  $unidade_medida_lida</div>
<br>
                        <div style='text-align:center; margin: 0 20px; padding: 40px; background-color:#edf2f7; border-radius: 6px; padding-bottom: 30px; padding-top: 20px; font-size: 14px;'>$mensagem_alerta</div>
    
                        <div style='padding-bottom: 40px;  padding-top: 30px; text-align:center;'>
                            <a href='https://step.eco.br/' rel='noopener'
                                style='text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle'
                                target='_blank'>Acessar STEP</a>
                        </div> <br>
                       
                        <br>
                        <div style='padding-bottom: 30px; font-size: 14px;'>$GPS <br><p>$Endereco_Origem</p></div>
                        <div style='border-bottom: 1px solid #eeeeee; margin: 15px 0'></div>
                        <div style='padding-bottom: 50px; word-wrap: break-all;'background-color:#ffffff;  min-height: 100%; margin:0; padding:0; width:100%; background-color:#edf2f7' >
                        $dados_controla_prazo
                             <p style='padding-bottom: 30px; padding-top: 20px; font-size: 12px;'><b>Leitura:</b> $dia_mes_ano às $hora_min</p>
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
                    <p>Chave Única de Controle do Checkin: $Chave_Unica_Checkin
                        <a href='https://step.eco.br' rel='noopener' target='_blank'>STEP</a>.
                    </p>
                </td>
            </tr><br>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</div>

</html>";
