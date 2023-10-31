<?php
echo '<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { 
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
            font-weight: normal;
            font-size: 15px;
            color: #2F3044;
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: #edf2f7;
        }
        .email-container {
            border-collapse: collapse;
            margin: 0 auto;
            padding: 0;
            max-width: 600px;
            border-radius: 6px;
            background-color: #EDEDED;
        }
        .email-logo {
            text-align: center;
            padding: 40px;
        }
        .email-content {
            text-align: left;
            margin: 0 20px;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 6px;
        }
        .message-highlight {
            background-color: #DDDDDD;
            border-radius: 10px;
            padding: 20px;
            font-size: 16px;
            font-weight: bold;
        }
        .email-footer {
            font-size: 10px;
            text-align: center;
            padding: 20px;
            color: #6d6e7c;
        }
    </style>
</head>
<body>
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="email-container">
        <tbody>
            <tr>
                <td align="center" valign="center" class="email-logo">
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
                <td align="left" valign="center">
                    <div class="email-content">
                        <div style="padding-bottom: 30px; font-size: 17px;">
                            <strong>Olá, ' . $nome_usuario . '!</strong>
                        </div>
                        <div class="message-highlight">' . $mensagem_email . '</div>
                        <div style="padding-top: 20px; padding-bottom: 10px">Atenciosamente,
                            <br>Equipe STEP.
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td align="center" valign="center" class="email-footer">
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
