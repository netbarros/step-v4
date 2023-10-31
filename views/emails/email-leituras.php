<?php 
echo '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        html, body {
            padding: 0;
            margin: 0;
        }
        body {
            font-family: Arial, Helvetica, Verdana, sans-serif;
            line-height: 1.5;
            font-weight: normal;
            font-size: 15px;
            color: #2F3044;
            min-height: 100%;
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: #edf2f7;
        }
        table {
            border-collapse: collapse;
            margin: 0 auto;
            padding: 0;
            max-width: 600px;
        }
        .header-logo {
            text-align: center;
            padding: 40px;
        }
        .content {
            text-align: left;
            margin: 0 20px;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 6px;
        }
        .info-message {
            padding-bottom: 30px;
            font-size: 15px;
        }
        .alert-message {
            text-align: center;
            margin: 0 20px;
            padding: 40px;
            background-color: #edf2f7;
            border-radius: 6px;
            padding-bottom: 30px;
            font-size: 14px;
        }
        .data-table {
            margin: 0 auto;
            padding: 0;
            max-width: 600px;
        }
        .data-table thead th {
            width: 200px;
            text-align: left;
            padding: 15px 0;
            font-weight: bold;
            color: #2F3044;
            border-bottom: 2px solid #eeeeee;
        }
        .data-table tbody td {
            font-size: 13px;
            text-align: center;
            padding: 20px;
            color: #000000;
        }
        .access-btn {
            text-decoration: none;
            display: inline-block;
            text-align: center;
            padding: 0.75575rem 1.3rem;
            font-size: 0.925rem;
            line-height: 1.5;
            border-radius: 0.35rem;
            color: #ffffff;
            background-color: #009EF7;
            border: 0px;
            margin-right: 0.75rem !important;
            font-weight: 600 !important;
            outline: none !important;
            vertical-align: middle;
        }
        .email-footer {
            font-size: 13px;
            text-align: center;
            padding: 20px;
            color: #6d6e7c;
        }
    </style>
</head>
<body>
    <table>
        <tbody>
            <tr>
                <td class="header-logo">
                    <a href="https://step.eco.br" rel="noopener" target="_blank">
                        <img alt="Logo" src="https://step.eco.br/tema/dist/assets/media/logos/logo-4.png" />
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="content">
                        <div class="info-message">
                            <strong>' . $Saudacao . '</strong><br><br>
                            Olá ' . $nome_usuario . ', o STEP têm uma nova notificação:
                        </div>
                        <br>
                        <br>
                        <div class="alert-message">' . $mensagem_alerta . '</div>
                        <br>
                        <div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Indicador</th>
                                        <th>Parâmetro</th>
                                        <th>Leitura</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>' . $nome_parametro . '</td>
                                        <td>' . $concen_min . ' <> ' . $concen_max . '</td>
                                        <td>' . $leitura . ' ' . $unidade_medida_lida . '</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div style="padding-bottom: 40px; padding-top: 40px; text-align:center;">
                            <a href="https://step.eco.br/" rel="noopener" class="access-btn" target="_blank">Acessar STEP</a>
                        </div>
                        <div style="padding-bottom: 30px; font-size: 14px;">' . $GPS . ' <br><p style="margin-bottom: 10px;">' . $Endereco_Origem . '
                            </p></div>
                        <div style="border-bottom: 1px solid #eeeeee; margin: 15px 0"></div>
                        <div style="padding-bottom: 50px; word-wrap: break-all;">
                            <p style="padding-bottom: 30px; padding-top: 20px; font-size: 12px;"><b>Leitura:</b> ' . $dia_mes_ano . ' às ' . $hora_min . '</p>
                            <a href="https://step.eco.br/" rel="noopener" target="_blank" style="text-decoration:none;color: #009EF7">https://step.eco.br/</a>
                        </div>
                        <div style="padding-bottom: 10px; font-size: 12px;text-decoration:none;color: #008000">Atenciosamente,
                            <br>
                            <a href="https://step.eco.br" rel="noopener" target="_blank">
                                <img alt="Logo" src="https://step.eco.br/app/assets-v2/media/logos/logo-4-sm.png" />
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="email-footer">
                    <p>Chave Única de Controle da Operação: ' . $Chave_Unica_Rmm . '
                        <a href="https://step.eco.br" rel="noopener" target="_blank">STEP</a>.
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>';

