<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header("Access-Control-Max-Age: 3600");
ini_set('memory_limit', '-1');

include_once $_SERVER['DOCUMENT_ROOT'].'/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/total-voice/autoload.php';
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

use PHPMailer\PHPMailer\Exception;
use TotalVoice\Client as TotalVoiceClient;
use Twilio\Rest\Client;

// Carregue o arquivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// ... carregue as variáveis ​​de ambiente

$twilioAccountSid = $_ENV['TWILIO_ACCOUNT_SID'];
$twilioAuthToken = $_ENV['TWILIO_AUTH_TOKEN'];
$twilioFromNumber = $_ENV['TWILIO_FROM_NUMBER'];
$totalVoiceToken = $_ENV['TOTAL_VOICE_TOKEN'];

//===[ CHAVE ÚNICA da SESSAO] a cada acesso, o step registra uma codificação única de 32bits, 
$horario_completo_agora = microtime();
/* INÍCIO: Crio a Chave unica da Sessao para da CRON Única Gerada no sistema */
/* Gerar strings aleatórias criptograficamente seguras, usamos 24 caracteres não repetitivos e aleatórios com criptografia nativa do PHP";*/
$chave_unica = bin2hex(random_bytes(33) . $horario_completo_agora);
/* FIM: Crio a Chave unica da Sessao para da CRON Única Gerada no sistema */

// Define o Período da Busca dos Dados

function MontaIndicadores($id_indicador,$conexao) {
    $stmt = $conexao->prepare("SELECT pr.nome_parametro,pr.concen_min,pr.concen_max,un.nome_unidade_medida FROM parametros_ponto pr
    INNER JOIN unidade_medida un ON un.id_unidade_medida = pr.unidade_medida  WHERE pr.id_parametro = :id_parametro");
    $stmt->bindParam(':id_parametro', $id_indicador, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function buscarUsuariosProjeto($id_obra,$conexao) {
    $stmt = $conexao->prepare("SELECT up.id_usuario as id_usuario_projeto,u.telefone,u.nome,u.email FROM usuarios_projeto up
    INNER JOIN usuarios u ON  u.id = up.id_usuario WHERE up.id_obra = :id_obra");
    $stmt->bindParam(':id_obra', $id_obra, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function buscarNotificacoesUsuario($id_obra, $id_usuario, $id_tipo_suporte,$conexao) {
    $stmt = $conexao->prepare("SELECT alerta_email,alerta_sms,alerta_whats,id_tipo_suporte, id_usuario as id_usuario_notificacao FROM notificacoes_usuario WHERE id_obra = :id_obra AND id_usuario = :id_usuario AND id_tipo_suporte = :id_tipo_suporte");
    $stmt->bindParam(':id_obra', $id_obra, PDO::PARAM_INT);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->bindParam(':id_tipo_suporte', $id_tipo_suporte, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Definindo funções separadas para cada tipo de notificação para reutilização
function enviarEmail($email_destino, $assunto, $nome_destino, $mensagem_alerta,$retorno_alerta) {
            $nome_usuario = $nome_destino;
            $email_usuario = $email_destino;
            $mensagem_email = $mensagem_alerta; // mensagem sem tags html
            $assunto = $assunto;
            $mensagem_email .= $retorno_alerta;
            $horario_completo_agora = microtime();
            $chave_unica = bin2hex(random_bytes(33) . $horario_completo_agora);
            $template_email = '/views/emails/email-padrao.php';
            

            require_once  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';
        
          
}


function enviarSMS($numero_destino, $mensagem_curta, $totalVoiceToken) {

    $mensagem_alerta_sem_tags = strip_tags($mensagem_curta);
     
    $mensagem_alerta_resumida = mb_substr($mensagem_alerta_sem_tags, 0, 160, 'UTF-8');

    $client = new TotalVoiceClient($totalVoiceToken);
    $response = $client->sms->enviar($numero_destino, $mensagem_alerta_resumida);

    if($response->getStatusCode() == 200) {
        echo 'SMS enviado com sucesso';
    } else {

        echo 'Falha ao enviar SMS';
        error_log('Erro ao enviar SMS: '.$numero_destino.' - ' . $response->getContent());
    }
    
}

function enviarWhatsApp($numero_destino, $mensagem_curta, $twilioAccountSid, $twilioAuthToken, $twilioFromNumber) {
    $client = new Client($twilioAccountSid, $twilioAuthToken);

    $mensagem_alerta_resumida = mb_substr($mensagem_curta, 0, 160, 'UTF-8');

    try {
        $message = $client->messages->create(
            "whatsapp:+5515981745522", //.$numero_destino (+14066923119 numero virtual sandbox Twilio -  para o número do usuário) +14155238886
            [
                "from" => "whatsapp:+14155238886",//whatsapp:".twilioFromNumber, // $twilioFromNumber 
                'body' => $mensagem_alerta_resumida
            ]
        );

        if($message->sid) {
            echo 'Mensagem do WhatsApp enviada com sucesso';
        } else {
            echo 'Erro ao enviar a mensagem do WhatsApp';
            error_log('Erro ao enviar a mensagem do WhatsApp');
        }
    } catch (Exception $e) {
        echo 'Exceção capturada ao enviar a mensagem do WhatsApp: ' . $e->getMessage();
        error_log($e->getMessage()); 
    } catch (Error $e) {
        echo 'Erro capturado ao enviar a mensagem do WhatsApp: ' . $e->getMessage();
        error_log($e->getMessage());
    }
}



   
// 5_min, 1_hora, 1_dia (periodo de verificação que o usuario poderá escolher e o que determinará as 3 CRON, uma para cada período)

$periodo_verificacao_mapping = [ // mapping paa atualizar automtic, serviu de referencia das opcoes de atualizacao para cada api CRON de leitura
    '5_minutos' => '-5 minutes',
    'hora' => '-1 hour',
    'dia' => '-24 hours'
];

$periodo_verificacao = '-5 minutes';//'-5 minutes';

// Agora, podemos usar a string de tempo relativo para obter o timestamp do período de verificação:
$Data_Atual_Periodo = date_create()->format('Y-m-d H:i:s');
$Periodo_Verificacao = date('Y-m-d H:i:s', strtotime($periodo_verificacao, strtotime($Data_Atual_Periodo)));
$dia_tarefa_cron = date('d');

$sql = "SELECT s.*,
u_quem_abriu.nome AS Nome_Quem_Abriu,
u_quem_abriu.id AS Id_Quem_Abriu,
u_quem_atendeu.nome AS Nome_Quem_Atendeu,
u_quem_atendeu.id AS Id_Quem_Atendeu,
u_quem_fechou.nome AS Nome_Quem_Fechou,
u_quem_fechou.id AS Id_Quem_Fechou,
o.nome_obra AS Nome_Obra,
o.id_obra AS Id_Obra,
e.nome_estacao AS Nome_Estacao,
e.id_estacao AS Id_Estacao,
ts.nome_suporte AS Nome_Tipo_Suporte,
ts.id_tipo_suporte AS Id_Tipo_Suporte,
pe.nome_ponto AS Nome_Ponto,
pe.id_ponto AS Id_Ponto,
pp.nome_parametro AS Nome_Parametro,
pp.id_parametro AS Id_Parametro,
pp.concen_min AS Concentracao_Minima,
pp.concen_max AS Concentracao_Maxima,
um.nome_unidade_medida AS Nome_Unidade_Medida

FROM suporte s 
INNER JOIN
tipo_suporte ts ON ts.id_tipo_suporte = s.tipo_suporte
INNER JOIN 
obras o ON o.id_obra = s.obra
LEFT JOIN
estacoes e ON e.id_estacao = s.estacao
LEFT JOIN
pontos_estacao pe ON pe.id_ponto = s.plcode
LEFT JOIN
parametros_ponto pp ON pp.id_parametro = s.parametro
LEFT JOIN
unidade_medida um ON um.id_unidade_medida = pp.unidade_medida
INNER JOIN
usuarios u_quem_abriu ON u_quem_abriu.id = s.quem_abriu
LEFT JOIN
usuarios u_quem_atendeu ON u_quem_atendeu.id = s.quem_atendeu
LEFT JOIN
usuarios u_quem_fechou ON u_quem_fechou.id = s.quem_fechou

WHERE s.status_suporte != '4' AND s.data_open >= :Periodo_Verificacao
GROUP BY s.id_suporte

ORDER BY s.data_open DESC";


    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':Periodo_Verificacao', $Periodo_Verificacao, PDO::PARAM_STR);
   

    if ($stmt->execute()) {
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $contador = 0;
        $mensagem_alerta = '';
        $mensagem_curta = '';
        $url_curta = '';
        $assunto = '';
    
        $agrupados = [];
        foreach ($rows as $row) {
            $idTipoSuporte = $row['Id_Tipo_Suporte'];
            $idObra = $row['Id_Obra'];
    
            if (!isset($agrupados[$idTipoSuporte])) {
                $agrupados[$idTipoSuporte] = [];
            }
            if (!isset($agrupados[$idTipoSuporte][$idObra])) {
                $agrupados[$idTipoSuporte][$idObra] = [];
            }
            $agrupados[$idTipoSuporte][$idObra][] = $row;
        }
    
        foreach ($agrupados as $idTipoSuporte => $obras) {

            foreach ($obras as $idObra => $linhas) {
                $usuarios_projeto = buscarUsuariosProjeto($idObra, $conexao);
    
                foreach ($usuarios_projeto as $usuario_projeto) {
                    $notificacoes_usuario = buscarNotificacoesUsuario($idObra, $usuario_projeto['id_usuario_projeto'], $idTipoSuporte, $conexao);
                    // Processamento das notificações do usuário
                    
                        // Estrutura da Tabela de Dados de Retorno do Alerta:
                        $retorno_alerta =  "<table class='table-responsive' style='width: 100%; max-width: 600px; margin: 0 auto; background-color: #f1f1f1; border-radius: 10px;'>";
                        $retorno_alerta .= "<tr>
                            <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>Ticket ID</th>
                            <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>Núcleo</th>
                            <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>PLCode</th>
                            <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>Responsável</th>
                            <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>Descrição</th>
                            <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>Abertura</th>
                            <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>Status</th>
                            
                            </tr>";
    
                    foreach ($linhas as $linha) {
                        $status = '';
    
                        switch ($linha['status_suporte']) {
                            case '1':
                                $status = 'Aberto';
                                break;
                            case '2':
                                $status = 'Em Atendimento';
                                break;
                            case '3':
                                $status = 'Em Previsão';
                                break;
                            case '4':
                                $status = 'Fechado';
                                break;
                            case '6':
                                $status = 'Indicador Revogado';
                                break;
                            case '7':
                                $status = 'Indicador Liberado';
                                break;
                            default:
                                $status = 'Status não encontrado';
                                break;
                        }
    
                        $data_open = DateTime::createFromFormat('Y-m-d H:i:s', $linha['data_open']);
                        $formatter = new IntlDateFormatter(
                            'pt_BR',
                            IntlDateFormatter::SHORT,
                            IntlDateFormatter::SHORT,
                            'America/Sao_Paulo',
                            IntlDateFormatter::GREGORIAN
                        );
    
                        $data_abertura = $data_open->format('d/m/Y H:i:s');
                        $descricao = '';
    
                        if($linha['id_rmm_suporte']!=0 && $linha['id_rmm_suporte']!=NULL){
                            $indicadores = MontaIndicadores($linha['parametro'], $conexao);
                            foreach ($indicadores as $indicador) {

                                $descricao .= $linha['Nome_Tipo_Suporte'].'<br>';
                                $leitura = $linha['leitura_suporte'];
                                $min = $indicador['concen_min'] ?? '';
                                $max = $indicador['concen_max'] ?? '';
                                $variancia = 0;

                                if($leitura != 0 && $leitura != NULL && $leitura != '' && $min != 0 && $min != NULL && $min != '' && $max != 0 && $max != NULL && $max != ''){

                                if ($leitura < $min) {
                                    $variancia = (($min - $leitura) / $min) * 100;
                                    $descricao . "A leitura está " . abs($variancia) . "% abaixo do mínimo esperado.<br>";
                                } else if ($leitura > $max) {
                                    $variancia = (($leitura - $max) / $max) * 100;
                                    $descricao . "A leitura está " . abs($variancia) . "% acima do máximo esperado.<br>";
                                } else {
                                    $descricao . "A leitura está dentro do intervalo esperado.<br>";
                                }

                                $descricao .= ' Indicador: ' . $indicador['nome_parametro'].'<br>';
                                $descricao .= ' > ' . $indicador['concen_min'];
                                $descricao .= ' < ' . $indicador['concen_max'];
                                $descricao .= ' ' . $indicador['nome_unidade_medida'].'<br>';
                                $descricao .= ' Valor: ' . $linha['leitura_suporte'].'<br>';
    
                            }
     
                            }
                        } else {
                            $descricao = $linha['Nome_Tipo_Suporte'].'<br>'.$linha['motivo_suporte'];
                        }

                        $retorno_alerta .= "<tr>
                        <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>{$linha['id_suporte']}</td>
                        <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>{$linha['Nome_Estacao']}</td>
                        <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>{$linha['Nome_Ponto']}</td>
                        <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>{$linha['Nome_Quem_Abriu']}</td>
                        <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$descricao</td>
                        <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$data_abertura</td>
                        <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$status</td>
                        </tr>";


                        $assunto = "Alerta de Notificação - {$linha['Nome_Obra']} - {$linha['Nome_Tipo_Suporte']}";
                        $url_curta = "Acesse <a url='https://step.eco.br/index.php?t={$linha['Id_Tipo_Suporte']}&u={$usuario_projeto['id_usuario_projeto']}&p={$linha['Id_Obra']}' alt='Acessar Ticket' title='Acessar Ticket' target='_blank'>este link</a> para acessar o ticket.<br><br>
                        Ou <strong>Copie e Cole em seu navegador:</strong> https://step.eco.br/index.php?t={$linha['Id_Tipo_Suporte']}&u={$usuario_projeto['id_usuario_projeto']}&p={$linha['Id_Obra']}	";

                        $mensagem_alerta = "<div style='font-family: Arial, sans-serif; padding: 20px; border-radius: 10px; background-color: #f2f2f2; color: #737373; line-height: 1.5; font-size: 11px;'>
                        <p>Notificação de Suporte,</p>
                        <p>Este é um Alerta do Projeto: <strong>{$linha['Nome_Obra']}</strong>.</p>
                        <p>Novo Ticket para: <strong>{$linha['Nome_Tipo_Suporte']}</strong>.</p>
                        <p>{$url_curta}</p></div>"; 


                        
                        $mensagem_curta = "Olá {$usuario_projeto['nome']},\nAlerta STEP, Projeto: {$linha['Nome_Obra']}.\n
                        {$linha['Nome_Tipo_Suporte']}.\n\rAcesse o STEP!";

                     

                       
                    }

                    $retorno_alerta .= "</table>";


                    $retorno_alerta .= "<div style='font-family: Arial, sans-serif; padding: 20px; border-radius: 10px; background-color: #f2f2f2; color: #737373; line-height: 1.5; font-size: 11px;'>
                    <p>Você está recebendo este e-mail porque faz parte do Projeto:<strong> {$linha['Nome_Obra']} </strong>.<br>Possuindo Coleção Ativa de Notificação Personalizada <strong>{$linha['Nome_Tipo_Suporte']}</strong>, para este projeto.</p>
                    <p>Para não receber mais este tipo de e-mail, acesse suas Coleções de Notificações no Sistema e Personalize!</p>
                </div>";

                    $retorno_alerta .= "<div style='font-family: Arial, sans-serif; padding: 20px; border-radius: 10px; background-color: #f2f2f2; color: #737373; line-height: 1.5; font-size: 11px;'>
                    <p>Este e-mail foi enviado automaticamente pelo Sistema de Gestão de Qualidade da EP Engenharia.</p>
                    <p>Para mais informações, entre em contato com a equipe de Qualidade da EP Engenharia - STEP.</p></div>";

                    $retorno_alerta .= "<br>\n\r<p><b>Autênticação para Rastreabilidade e Auditoria:</b> ".$chave_unica."<br></p>\n\r";

                    foreach ($notificacoes_usuario as $notificacao) {

                        $telefone_usuario_alerta = "+55" . preg_replace('/[^0-9]/', '', trim($usuario_projeto['telefone']));


                        if ($notificacao['alerta_whats'] == 1) {

                            enviarWhatsApp($telefone_usuario_alerta, $mensagem_alerta, $twilioAccountSid, $twilioAuthToken, $twilioFromNumber);
                        }



                        if ($notificacao['alerta_email'] == 1) {
                            enviarEmail(trim($usuario_projeto['email']), $assunto, $usuario_projeto['nome'], $mensagem_alerta, $retorno_alerta);
                        }

                        if ($notificacao['alerta_sms'] == 1) {
                            enviarSMS($telefone_usuario_alerta, $mensagem_curta, $totalVoiceToken);
                        }


                    }

                    print $retorno_alerta."<br><br>\n\r####### Mensagem Alerta: $mensagem_alerta ]#####[ Novo Bloco de Dados - E-mail: {$usuario_projeto['email']} ]#######\n\r<br><br>";

                }
                
            }
            $contador++;
        }

       


         //  Gera Log da Leiitura fora do GPS
         $acao_log = "CRON Suporte Periodo 5 Minutos - Tempo Real ";
         $tipo_log = '50'; // Tarefa CRON Realizada com Sucesso
         $id_acao_log = $dia_tarefa_cron;
 
 
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
             '1',
             'Tarefa CRON Realizada com Sucesso',
             $acao_log,
             $id_acao_log,
             '0',
             $tipo_log
         ]);
         // Fecha Log <<<<<<<

         echo "Houveram <b>".$contador."</b> Suportes, analisados e com Notificações enviadas de acordo com As Coleções criadas por usuário x projeto.";

         $conexao =null;
    } else {
        error_log($stmt->errorInfo());
    }