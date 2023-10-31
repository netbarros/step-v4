<?php
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
                    <div style="padding-bottom: 30px; font-size: 17px;">
                        <strong>'.$Saudacao.'</strong> '.$nome_usuario.'<br>
                        Relação dos Check-ins não realizados na data '.$data_atual.'.
                    </div>
                    <div style="padding-bottom: 30px; font-size: 14px;">'.$mensagem_alerta.'</div>
                    <div style="font-family:Arial,Helvetica,sans-serif; line-height: 1.5; font-weight: normal; font-size: 14px; color: #2F3044; min-height: 100%; margin:0; padding:10px; width:100%; background-color:#edf2f7">
                        '.$tabela.'
                    </div>
                    <div style="padding-bottom: 40px; padding-top: 40px; text-align:center;">
                        <a href="https://step.eco.br/" rel="noopener" style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle" target="_blank">Acessar STEP</a>
                    </div>
                    <div style="padding-bottom: 30px; font-size: 14px;">Total não realizados: '.$check_total.'<br><p style="margin-bottom: 10px;">'.$Endereco_Origem.'</p></div>
                    <div style="border-bottom: 1px solid #eeeeee; margin: 15px 0"></div>
                    <div style="padding-bottom: 50px; word-wrap: break-all;">
                        <p style="padding-bottom: 30px; padding-top: 20px; font-size: 12px;"><b>Leitura:</b> '.$dia_mes_ano.' às '.$hora_min.'</p>
                        <a href="https://step.eco.br/" rel="noopener" target="_blank" style="text-decoration:none;color: #009EF7">https://step.eco.br/</a>
                    </div>
                </td>
            </tr>
            <tr>
                <td align="center" valign="center" style="font-size: 10px; text-align: center; padding: 20px; color: #6d6e7c;">
                    <p>STEP - Sistema de Tratamento EP</p>
                    '.$chave_unica_email.'
                    <p>Copyright © <a href="https://step.eco.br" rel="noopener" target="_blank">STEP</a>.</p>
                    <p style="color: #6d6e7c; font-size: 13px">Este é um e-mail confidencial e está sujeito às leis de proteção de dados, incluindo a LGPD. Se você não é o destinatário deste e-mail, por favor, notifique imediatamente o remetente e apague todas as cópias deste e-mail.</p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>';

?>
