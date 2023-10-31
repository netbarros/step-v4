<?php
//header("Content-Type: multipart/form-data; charset=utf-8");

// Estilos CSS para o e-mail
$styles = "
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>

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
	.btn {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #ffffff;
        background-color: #009EF7;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn:hover {
        background-color: #007BCE;
    }
</style>
<head>
";

// Conteúdo do e-mail
$content = "
<div style='font-family:Arial,Helvetica,sans-serif; line-height: 1.5; font-weight: normal; font-size: 15px; color: #2F3044; min-height: 100%; margin:0; padding:0; width:100%; background-color:#edf2f7'>
    <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='border-collapse:collapse;margin:0 auto; padding:0; max-width:600px'>
        <tbody>
		<tr>
		<td align='center' valign='center' class='email-logo'>
			<a href='https://step.eco.br' rel='noopener' target='_blank'>
				<img alt='Logo' src='https://step.eco.br/tema/dist/assets/media/logos/logo-4.png' width='100'/>
			</a>
			<a href='https://grupoep.com.br/ep-tech/' rel='noopener' target='_blank'>
				<img alt='Logo' src='https://step.eco.br/tema/dist/assets/media/logos/grupo-ep-wg.png' width='100'/>
			</a>
			<h2 style='color: #4B8333; font-weight: bold;'>Sistema de Tratamento EP</h2>
		</td>
	</tr>
            <tr>
                <td align='left' valign='center'>
                    <div style='text-align:left; margin: 20px; padding: 40px; background-color:#ffffff; border-radius: 6px'>
                        <div style='padding-bottom: 30px; font-size: 15px;'>
                            {$mensagem_email}
                        </div>
                        <div style='padding-bottom: 30px'>
                           Siga a próxima etapa. Para prosseguir com a redefinição de senha, clique no botão abaixo:
                        </div>
                        <div style='padding-bottom: 40px; text-align:center;'>
						<a href='https://step.eco.br/views/login/new-password.php?i={$id_usuario}&c={$chave_unica}' rel='noopener' class='btn' target='_blank'>Cadastrar Nova Senha</a>

                        </div>
                        <div style='padding-bottom: 30px'>
                            Este link de redefinição de senha expirará em 60 minutos. Se você não solicitou uma redefinição de senha, nenhuma ação adicional é necessária.
                        </div>
                        <div style='border-bottom: 1px solid #eeeeee; margin: 15px 0'></div>
                        <div style='padding-bottom: 50px; word-wrap: break-all;'>
                            <p style='margin-bottom: 10px;'>Botão não funciona? Tente colar este URL no seu navegador:</p>
                            <a href='https://step.eco.br/views/login/new-password.php?i={$id_usuario}&c={$chave_unica}' rel='noopener' target='_blank' style='text-decoration:none;color: #009EF7'>https://step.eco.br/views/login/new-password.php?i={$id_usuario}&c={$chave_unica}</a>
                        </div>
                       
                    </div>
                </td>
            </tr>
            <tr>
			<td align='center' valign='center' class='email-footer'>
			<p>STEP - Sistema de Tratamento EP</p>
			'.$chave_unica.'
			<p>Copyright © <a href='https://step.eco.br' rel='noopener' target='_blank'>STEP</a>.</p>
			<p style='color: #6d6e7c; font-size: 13px'>Este é um e-mail confidencial e está sujeito às leis de proteção de dados, incluindo a LGPD. Se você não é o destinatário deste e-mail, por favor, notifique imediatamente o remetente e apague todas as cópias deste e-mail.</p>
		</td>
            </tr>
        </tbody>
    </table>
</div>
";

echo $styles . $content;
?>
