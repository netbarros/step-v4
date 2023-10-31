<?php
//header("Content-Type: multipart/form-data; charset=utf-8");
//header("Content-Type: text/html; charset=utf-8");
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
        font-size: 16px;
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
    .email-container-rounded {
        border-radius: 10px;
        background-color: #E4E4E4;
        padding: 10px;
    }
    .email-footer {
        font-size: 10px;
        text-align: center;
        padding: 20px;
        color: #6d6e7c;
    }
    @media screen and (max-width: 600px) {
        .responsive-table {
            width: 100% !important;
        }

        .responsive-table td {
            display: block;
            width: 100% !important;
            text-align: center !important;
        }
    }

    </style>
</head>
<body>
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="email-container" style="font-family: Arial, sans-serif; line-height: 1.5; font-size: 16px; border-radius: 6px; background-color: #EDEDED;">
        <tbody>
            
<tr>
    <td align="center" valign="center" class="email-logo">
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tbody>
                <tr>
                    <td align="center" valign="center">
                        <a href="https://step.eco.br" rel="noopener" target="_blank">
                            <img alt="Logo" src="https://step.eco.br/tema/dist/assets/media/logos/logo-4.png" width="100"/>
                        </a>
                    </td>
                    <td align="center" valign="center">
                        <a href="https://grupoep.com.br/ep-tech/" rel="noopener" target="_blank">
                            <img alt="Logo" src="https://step.eco.br/tema/dist/assets/media/logos/grupo-ep-wg.png" width="100"/>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td  align="center" valign="center">
                        <h3 style="color: #55863F; font-weight: bold;">Sistema de Tratamento EP</h3>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr>
<td align="left" valign="center">
  <div class="email-content" style="font-family: Arial, sans-serif; padding:  5px 5px 5px 5px; margin-left: 5px; margin-top: 5px; border-radius: 6px; background-color: #E4E4E4; color: #737373; line-height: 1.5; font-size: 11px;">
    <div style="background-color: #E4E4E4; border-radius: 10px; padding: 20px;">
      <h3 style="color: #2F3044; font-weight: bold;">Olá, ' . $nome_usuario . '!</h3>
      <span style="background-color:#41424A; style="color: #ECECEC; font-size: 13px; margin-top: 5px;">' . $mensagem_alerta . '</span>
    </div>
  
  </div>
</td>
</tr>

            <tr>
            <td align="left" valign="center">
            <div class="email-content" style="font-family: Arial, sans-serif; padding:  5px 5px 5px 5px; margin-left: 5px; margin-top: 5px; border-radius: 6px; background-color: #E4E4E4; color: #737373; line-height: 1.5; font-size: 11px;">
                <div style="background-color: #DAE3D7; border-radius: 6px; padding: 20px;>
                    <!-- conteúdo da mensagem -->
                    <span style="color: #ECF1EB;">' . $retorno_alerta . '</span>
                </div>
                </div>
            </td>
        </tr>

            <tr>
                <td align="center" valign="center" class="email-footer">
                    <p>STEP - Sistema de Tratamento EP</p>
                    <p>Copyright © <a href="https://step.eco.br" rel="noopener" target="_blank">STEP</a>.</p>
                    <p style="color: #6d6e7c; font-size: 13px">Este é um e-mail confidencial e está sujeito às leis de proteção de dados, incluindo a LGPD. Se você não é o destinatário deste e-mail, por favor, notifique imediatamente o remetente e apague todas as cópias deste e-mail.</p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
';

