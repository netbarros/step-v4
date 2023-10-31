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


$Chave_Unica_CRON = $chave_unica;
// Define o Período da Busca dos Dados
try {
    
//**** verifica o usuarios usuarios das Coleções de Notificações  
// Precisa validar o periodo que cada usuario escolheu ser notificado para gerar a consulta baseada no periodo escolhido
// A Consulta precisa recuperar estes dados para enviar as notificações.
// vou fazer uma consulta principal com 5_min, com 1 hora e com 1 dia
// cada consulta vai ter um periodo diferente para enviar as notificações
// cada script será uma api diferente para enviar as notificações de acordo
// com o periodo escolhido pelo usuario
// 5_min, 1_hora, 1_dia (periodo de verificação que o usuario poderá escolher e o que determinará as 3 CRON, uma para cada período)

$periodo_verificacao_mapping = [ // mapping paa atualizar automtic, serviu de referencia das opcoes de atualizacao para cada api CRON de leitura
    '5_minutos' => '-5 minutes',
    'hora' => '-1 hour',
    'dia' => '-24 hours'
];

$periodo_verificacao = '-24 hours';//'-5 minutes';

// Agora, podemos usar a string de tempo relativo para obter o timestamp do período de verificação:
$Data_Atual_Periodo = date_create()->format('Y-m-d H:i:s');
$Periodo_Verificacao = date('Y-m-d H:i:s', strtotime($periodo_verificacao, strtotime($Data_Atual_Periodo)));


    $sql = "SELECT 
            r.id_rmm,
            p.id_ponto,
            p.nome_ponto,
            p.objetivo_ponto,
            p.latitude_p AS Latitude_Ponto,
            p.longitude_p AS Longitude_Ponto,
            r.latitude_user AS Latitude_Operador,
            r.longitude_user AS Longitude_Operador,
            pr.id_parametro,
            pr.nome_parametro,
            pr.concen_min,
            pr.concen_max,
            pr.origem_leitura_parametro,
            pr.controle_concentracao,
            r.leitura_entrada,
            r.leitura_saida,
            u.nome_unidade_medida,
            r.data_leitura AS Data_Rmm,
            r.chave_unica AS Chave_Unica_Rmm,
            r.status_leitura,
            o.nome_obra,
            o.id_obra,
            e.nome_estacao,
            e.id_estacao,
            uop.id AS Id_Operador,
            uop.nome AS Nome_Operador
            
        FROM
            rmm r
                INNER JOIN
            pontos_estacao p ON p.id_ponto = r.id_ponto
                INNER JOIN
            parametros_ponto pr ON pr.id_parametro = r.id_parametro
                INNER JOIN
            unidade_medida u ON pr.unidade_medida = u.id_unidade_medida
                INNER JOIN
            obras o ON o.id_obra = p.id_obra
                INNER JOIN
            estacoes e ON e.id_estacao = p.id_estacao
                INNER JOIN
            usuarios uop ON uop.id = r.Id_Operador
                INNER JOIN
            usuarios_projeto up ON up.id_obra = o.id_obra
                
        
        WHERE r.status_leitura = '5' AND r.id_operador='1' AND
        DATE_FORMAT(r.data_leitura, '%Y-%m-%d H:i:s') > :Periodo_Verificacao
        GROUP BY r.id_rmm
        ORDER BY r.data_Leitura ASC";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':Periodo_Verificacao', $Periodo_Verificacao, PDO::PARAM_STR);
    $stmt->execute();

    $total = $stmt->rowCount();

} catch (PDOException $e) {
    echo 'Erro: ' . $e->getMessage();
    $total = 0; // definindo $total como 0 no caso de uma exceção
}


/* status = 5. status automaticamente gerado a cada nova leitura, são aquelas que estão aguardando a validação da API para agora 
    verificar se cada leitura com status 5 (aguardando análise), está dentro ou fora do parâmetro informado (concen_min e concen_max)
    r.status_leitura = '5'  */


if ($total > 0) { // inicia a validação das leituras encontradas

    $resultado = $stmt->fetchALL(PDO::FETCH_ASSOC);

   
// Funcao para atualizar a tabela RMM de leituras como status validado
    function update_status_rmm($conexao, $status_leitura, $ID_RMM)
    {
        return $conexao->query("UPDATE rmm SET status_leitura='{$status_leitura}' WHERE id_rmm='{$ID_RMM}' ");
    }
// Fim da Funcao para atualizar a tabela RMM de leituras como status validado

// função para disparar os alertas //



    class AlertaService {

        public $mensagem_alerta;
        private $conexao;
        private $twilioAccountSid;
        private $twilioAuthToken;
        private $twilioFromNumber;
        private $totalVoiceToken;
    
        public function __construct(PDO $conexao, $twilioAccountSid, $twilioAuthToken, $twilioFromNumber, $totalVoiceToken) {
            
            $this->conexao = $conexao;
            $this->twilioAccountSid = $twilioAccountSid;
            $this->twilioAuthToken = $twilioAuthToken;
            $this->twilioFromNumber = $twilioFromNumber;
            $this->totalVoiceToken = $totalVoiceToken;
        }
    
       public function enviarAlerta($id_obra, $id_tipo_suporte, $retorno_alerta,$nome_estacao, $nome_obra, $assunto_notificacao) {
        // Seu código atual...
        
            $usuarios = $this->getUsuarios($id_obra, $id_tipo_suporte,$nome_estacao, $nome_obra);

            print_r($usuarios);
    
            foreach ($usuarios as $usuario) {
                if ($usuario['status_notificacao_usuario']) { // Verifica se o usuário deseja receber notificações
                    $telefone_usuario_alerta = "+55" . preg_replace('/\s+/', '', trim($usuario['telefone']));
                    $mensagem_alerta = $this->construirMensagemAlerta($usuario,$nome_estacao, $nome_obra, $assunto_notificacao);
                    $assunto = $assunto_notificacao;
                    $this->enviarNotificacoes($usuario['email'], $usuario['nome'], $retorno_alerta, $usuario['alerta_email'], $usuario['alerta_sms'], $usuario['alerta_whats'], $mensagem_alerta, $assunto_notificacao, $telefone_usuario_alerta);
                }

                
             // Armazena a mensagem de alerta na propriedade da classe
        $this->mensagem_alerta = $mensagem_alerta;

        // Agora $this->mensagem_alerta está disponível para a função construirMensagemAlerta
        $mensagem_alerta = $this->construirMensagemAlerta($usuario,$nome_estacao, $nome_obra, $assunto_notificacao, $this->mensagem_alerta);
            }

        }
    
        private function getUsuarios($id_obra, $id_tipo_suporte) {
            $sql = "SELECT nu.*, u.nome, u.email, u.telefone, up.id_obra as projeto_user, up.id_usuario, nu.status_notificacao_usuario, up.nivel FROM notificacoes_usuario nu
            INNER JOIN usuarios u ON u.id = nu.id_usuario
            INNER JOIN usuarios_projeto up ON up.id_usuario = u.id
            WHERE u.status = 1 AND up.id_obra = :id_obra AND nu.id_obra = :id_obra AND nu.id_tipo_suporte = :id_tipo_suporte;";
    
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':id_obra', $id_obra, PDO::PARAM_INT);
            $stmt->bindParam(':id_tipo_suporte', $id_tipo_suporte, PDO::PARAM_INT);
            $stmt->execute();
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        }

        
    
        private function construirMensagemAlerta($usuario, $nome_estacao, $nome_obra, $assunto_notificacao) {
            return "Olá {$usuario['nome']}, \n\r {$usuario['nivel']} \n\r$assunto_notificacao \n\r {$this->mensagem_alerta}\n\rProjeto: $nome_obra\n\rNúcleo $nome_estacao\n\r. Favor verificar.";
        }
    
        private function enviarNotificacoes($email_usuario_alerta, $nome_usuario_alerta, $retorno_alerta, $alerta_email, $alerta_sms, $alerta_whats, $assunto_notificacao, $nome_suporte, $telefone_usuario_alerta) {
            // Envio de email
            if (isset($alerta_email) && $alerta_email) {
                $this->enviarEmail($email_usuario_alerta, $nome_usuario_alerta, $this->mensagem_alerta, $retorno_alerta,$assunto_notificacao);              
            }
    
            // Envio de SMS
            if (isset($alerta_sms) && $alerta_sms) {  
                $this->enviarSms($telefone_usuario_alerta, $this->mensagem_alerta);
            }
    
            // Envio de Whatsapp
            if (isset($alerta_whats) && $alerta_whats) { 
                $this->enviarWhatsapp($telefone_usuario_alerta, $this->mensagem_alerta);
            }
        }
    
        private function enviarEmail($email_usuario_alerta, $nome_usuario_alerta, $retorno_alerta,$assunto_notificacao) {
            // Código para envio de email aqui
        
            $nome_usuario = $nome_usuario_alerta;
            $email_usuario = $email_usuario_alerta;
            $mensagem_email = $this->mensagem_alerta; // mensagem sem tags html
            $assunto = $assunto_notificacao;
            $mensagem_email .= $retorno_alerta;
            $template_email = '/views/emails/email-api.php';
        
            $chave_unica = '';

            require_once  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';
        
          
        }
    
        private function enviarSms($telefone_usuario_alerta) {
            // Código para envio de SMS aqui

            $client = new TotalVoiceClient($this->totalVoiceToken);
            $numero_destinatario = $telefone_usuario_alerta;
     
          
            $mensagem_alerta_sem_tags = strip_tags($this->mensagem_alerta);
     
            $mensagem_alerta_resumida = mb_substr($mensagem_alerta_sem_tags, 0, 160, 'UTF-8');
     
     
     
            try {
                $response = $client->sms->enviar($telefone_usuario_alerta, $mensagem_alerta_resumida);
                // Adiciona a mensagem de sucessi no arquivo de log
                $logFile = 'error_log_total_voice.txt';
                $retorno = $response->getContent();
                file_put_contents($logFile, $retorno, FILE_APPEND);
                // print_r($response); // id de retorno da mensagem do totalvoice
                
            } catch (Exception $e) {
                // Trate o erro de forma adequada
                $logFile = 'error_log_total_voice.txt';
                $errorMessage = "Erro ao enviar SMS: " . $e->getMessage() . " - " . date('Y-m-d H:i:s') . PHP_EOL;
                $retorno = $response->getContent();
                // Adiciona a mensagem de erro no arquivo de log
                file_put_contents($logFile, $errorMessage, $retorno, FILE_APPEND);
            }
        }
    
        private function enviarWhatsapp($telefone_usuario_alerta) {
            $twilio = new Client($this->twilioAccountSid, $this->twilioAuthToken);
        
        
            try {
                $message = $twilio->messages
                ->create(
                    "whatsapp:+5515981745522", //.$para (+14066923119 numero virtual sandbox Twilio -  para o número do usuário) +14155238886
                    array(
                        "from" => "whatsapp:+14155238886",//whatsapp:".$this->twilioFromNumber, // .$this->twilioFromNumber 
                        "body" => $this->mensagem_alerta
                    )
                );
            } catch (Exception $e) {
                $logFile = 'error_log_twilio.txt';
                $errorMessage = "Erro ao enviar mensagem no WhatsApp: " . $e->getMessage() . " - " . date('Y-m-d H:i:s') . PHP_EOL;
        
                // Adiciona a mensagem de erro no arquivo de log
                file_put_contents($logFile, $errorMessage, FILE_APPEND);
            }
        }
 

    }
    

/****** Inicia - Função para Criar o Ticket de Suporte >>>>>>>>*/
function insere_suporte($conexao, $nome_obra, $nome_estacao, $id_tipo_suporte, $nome_suporte, $ID_RMM, $leitura, $id_obra, $id_estacao, $id_ponto, $id_parametro, $Id_Operador, $chave_unica) {

    $status_suporte_inicial = '1'; // status inicial do ticket de suporte, 1 = aberto
    $sql = "INSERT INTO suporte(
        tipo_suporte,
        motivo_suporte,
        id_rmm_suporte,
        leitura_suporte,
        obra,        
        estacao,
        plcode,
        parametro,
        quem_abriu,
        chave_unica,
        status_suporte
        ) VALUES(
        :tipo_suporte,
        :motivo_suporte,
        :id_rmm_suporte,
        :leitura_suporte,   
        :obra,     
        :estacao,
        :plcode,
        :parametro,
        :quem_abriu,
        :chave_unica,
        :status_suporte
            )";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':tipo_suporte', $id_tipo_suporte);
    $stmt->bindParam(':motivo_suporte', $nome_suporte);
    $stmt->bindParam(':id_rmm_suporte', $ID_RMM);
    $stmt->bindParam(':leitura_suporte', $leitura);
    $stmt->bindParam(':estacao', $id_estacao);
    $stmt->bindParam(':obra', $id_obra);
    $stmt->bindParam(':plcode', $id_ponto);
    $stmt->bindParam(':parametro', $id_parametro);
    $stmt->bindParam(':quem_abriu', $Id_Operador);
    $stmt->bindParam(':chave_unica', $chave_unica);
    $stmt->bindParam(':status_suporte', $status_suporte_inicial);
  
    $result = $stmt->execute();
  
    $ultimo_id_suporte = $conexao->lastInsertId();
  
    
    // Insere conversa
    if ($result) {

      insere_conversa($conexao, $nome_obra, $nome_estacao, $ultimo_id_suporte,$id_tipo_suporte, $id_obra, $id_estacao, $nome_suporte, $Id_Operador, $chave_unica);
    }
  }
  
// finaliza = função para criar o ticket de suporte <<<<<<<<<<



 // Inicia = funcao para criar a 1 covnersa do suporte ticket gerado >>>

 function insere_conversa($conexao, $nome_obra, $nome_estacao, $ultimo_id_suporte, $id_obra, $id_estacao, $nome_suporte, $Id_Operador, $chave_unica) {
    $sql = "INSERT INTO suporte_conversas (id_suporte,id_remetente,destinatario_direto,conversa) VALUES (:id_suporte, :id_remetente, :destinatario_direto, :conversa)";
    $stmt = $conexao->prepare($sql);

     /* busca pelo supervisor do Projeto */
    $busca_supervisor = "SELECT id_usuario FROM usuarios_projeto WHERE id_obra = :id_obra AND nivel= 'supervisor'";
    $stmt2 = $conexao->prepare($busca_supervisor);
    $stmt2->bindParam(':id_obra', $id_obra);
    $stmt2->execute();
    $resultado_stmt2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    foreach ($resultado_stmt2 as $resultado_stmt2) {
    
            $ID_SU = $resultado_stmt2['id_usuario']; // busca pelos supervisores do Projeto
        
            $mensagem_Direta = "STEP:\n\rTicket ID: $ultimo_id_suporte,\n\r$nome_suporte,\n\rProjeto: $nome_obra,\n\rNúcleo: $nome_estacao\n\r.Favor verificar.";

            $stmt->bindParam(':id_suporte', $ultimo_id_suporte);
            $stmt->bindParam(':id_remetente', $Id_Operador);
            $stmt->bindParam(':destinatario_direto', $ID_SU);
            $stmt->bindParam(':conversa', $mensagem_Direta);
        
            $result = $stmt->execute();
            $ultimo_id_conversa = $conexao->lastInsertId(); // como esse id se passa junto, consigo abrir o chat automatico do usuario que receberá a mensagem

            // criar funcao para exibir popup de conversa aberta automaticamente
            
            if ($result) {
            $acao_log = "Chat Direct";
            $tipo_log = '48';
            $sql_log = "INSERT INTO log_leitura (chave_unica,id_usuario,acao_log,id_acao_log,estacao_logada,acao,tipo_log) VALUES (?,?,?,?,?,?,?)";
            $acao_log = "Conversa iniciada automaticamente, através da criação do Ticket ID: $ultimo_id_suporte, sobre $nome_suporte | ";
            $conexao->prepare($sql_log)->execute([$chave_unica, $ID_SU, $acao_log, $ultimo_id_suporte, $id_estacao, $acao_log, $tipo_log]);
            }

        }
  }
  
// finaliza =  funcao para criar a 1 conversa do ticket gerado <<<<<<<<<<


     // Estrutura da Tabela de Dados de Retorno do Alerta:
     $retorno_alerta =  "<table class='table-responsive' style='width: 100%; max-width: 600px; margin: 0 auto; background-color: #f1f1f1; border-radius: 10px;'>";
     $retorno_alerta .= "<tr>
         <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>Projeto</th>
         <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>Núcleo</th>
         <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>PLCode</th>
         <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>Operador</th>
         <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>Indicador</th>
         <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>Leitura</th>
         <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>Parâmetros</th>
         <th style='border: 1px solid #ddd; padding: 10px;  text-align: left; font-size: 12px;'>Data</th>
         </tr>";

 
// Inicia o Laço Principal que Busca e Valida as Leituras >>>  
 
 // variáveis de controle de alerta
 $status_leitura = '1';      
 $assunto_notificacao = "*[ Alerta STEP ]*\n\r";
 $nome_suporte = '';
 $id_tipo_suporte ='';
 $ultimo_id_suporte = '';
 
    foreach ($resultado as $res) {

        $ID_RMM = isset($res['id_rmm']) ? $res['id_rmm'] : '';
        $Chave_Unica_Rmm = trim(isset($res['Chave_Unica_Rmm'])) ? $res['Chave_Unica_Rmm'] : '';
        $Id_Operador = $res['Id_Operador'];
        $nome_Operador = $res['Nome_Operador'];
        $Latitude_Operador = trim(isset($res['Latitude_Operador'])) ? $res['Latitude_Operador'] : '';
        $Longitude_Operador = trim(isset($res['Longitude_Operador'])) ? $res['Longitude_Operador'] : '';

        $id_ponto = $res['id_ponto'];
        $nome_plcode = $res['nome_ponto'];
        $objetivo_ponto = trim(isset($res['objetivo_ponto'])) ? $res['objetivo_ponto'] : '';
        $Latitude_Ponto = trim(isset($res['Latitude_Ponto'])) ? $res['Latitude_Ponto'] : '';
        $Longitude_Ponto = trim(isset($res['Longitude_Ponto'])) ? $res['Longitude_Ponto'] : '';

        $id_parametro = trim(isset($res['id_parametro'])) ? $res['id_parametro'] : '';
        $nome_parametro = isset($res['nome_parametro']) ? $res['nome_parametro'] : '';
        $unidade_medida_lida = trim(isset($res['nome_unidade_medida'])) ? $res['nome_unidade_medida'] : '';
        
        $id_obra = $res['id_obra'];
        $nome_obra = $res['nome_obra'];
        $id_estacao = $res['id_estacao'];
        $nome_estacao = $res['nome_estacao'];

       

        /**** Validação dos dados para tratar as leituras analisadas -  Operadores Ternários para Parametros e seus derivados acontece, por haver leitura de RMM/Checkin apenas com o PLCode.*/
        $concen_min = isset($res['concen_min']) ? doubleval(trim($res['concen_min'])) : NULL;
        $concen_max = isset($res['concen_max']) ? doubleval(trim($res['concen_max'])) : NULL;
        $leitura = doubleval(trim($res['leitura_entrada']));
        $controle_concentracao = trim($res['controle_concentracao']);
        

        $data_RMM = DateTime::createFromFormat('Y-m-d H:i:s', $res['Data_Rmm']);

        $formatter = new IntlDateFormatter(
            'pt_BR',
            IntlDateFormatter::SHORT,
            IntlDateFormatter::SHORT,
            'America/Sao_Paulo',
            IntlDateFormatter::GREGORIAN
        );
        
        $data_leitura = $data_RMM->format('d/m/Y H:i:s');
        
        //$data_leitura = $formatter->format(new DateTime($data_RMM));
          

      
    
        if ($controle_concentracao == "1") {

            $nome_controle_concentracao = "Mínima";
    
                if ($leitura < $concen_min && $concen_min != NULL) {

                    $status_leitura = "3";
                    $assunto_notificacao .= "Leitura abaixo do Permitido.";

                    $nome_suporte = "Leitura abaixo do Permitido, pela regra que controla só Mínimo" ;  
                    $id_tipo_suporte = '96';
                  
                    $retorno_alerta .= "<tr>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_obra</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_estacao</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_plcode</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_Operador</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_parametro</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$leitura $unidade_medida_lida</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'> > $concen_min  </td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$data_leitura</td>
                    </tr>";
                

                    
                } else {
                    $status_leitura = "1";

                    $assunto_notificacao .= "Leitura dentro da Mínima Permitida";
                    $nome_suporte = "Leitura dentro da Mínima Permitida" ;  
                    $id_tipo_suporte = '0';
                                
                }
        } elseif ($controle_concentracao == "2") {

            $nome_controle_concentracao = "Máxima";
    
                if ($leitura > $concen_max && $concen_max != NULL) {

                    $status_leitura = "3";

                    $assunto_notificacao .= "Leitura acima do Permitido.";
                    $nome_suporte = "Leitura acima do Permitido, pela regra que controla só Máximo" ;  
                    $id_tipo_suporte = '97';

                    $retorno_alerta .= "<tr>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_obra</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_estacao</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_plcode</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_Operador</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_parametro</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$leitura $unidade_medida_lida</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'> $concen_max < </td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$data_leitura</td>
                    </tr>";
                            
                    
                } else {

                    $status_leitura = "1";

                   
                    $nome_suporte = "Leitura dentro ao limite permitido" ;  
                    $id_tipo_suporte = '0';
                    }

             } elseif ($controle_concentracao == "3") {

                $nome_controle_concentracao = "Mínima e Máxima";

                        if ($leitura >= $concen_min && $leitura <= $concen_max && $concen_min != NULL && $concen_max != NULL) {

                            $status_leitura = "1";

                           
                            $nome_suporte = "Leitura dentro ao limite permitido" ;  
                            $id_tipo_suporte = '0';

                        }else if($leitura < $concen_min && $leitura <= $concen_max){

                            $status_leitura = "3";

                            $assunto_notificacao .= "Leitura abaixo do Permitido.";
                            $nome_suporte = "Leitura abaixo do Permitido, pela regra que controla Mínimo e Máximo" ;  
                            $id_tipo_suporte = '98';

                            $retorno_alerta .= "<tr>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_obra</td>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_estacao</td>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_plcode</td>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_Operador</td>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_parametro</td>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$leitura $unidade_medida_lida</td>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$concen_min >< $concen_max</td>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$data_leitura</td>
                            </tr>";

                        }else if($leitura >= $concen_min && $leitura > $concen_max){

                            $status_leitura = "3";

                            $assunto_notificacao .= "Leitura acima do Permitido.";
                            $nome_suporte = "Leitura acima do Permitido, pela regra que controla Mínimo e Máximo" ;  
                            $id_tipo_suporte = '108';

                            $retorno_alerta .= "<tr>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_obra</td>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_estacao</td>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_plcode</td>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_Operador</td>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$nome_parametro</td>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$leitura $unidade_medida_lida</td>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$concen_min >< $concen_max</td>
                            <td style='border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px;'>$data_leitura</td>
                            </tr>";

                        }           
                    
                }

                update_status_rmm($conexao, $status_leitura, $ID_RMM);
                insere_suporte($conexao, $nome_obra, $nome_estacao, $id_tipo_suporte, $nome_suporte, $ID_RMM, $leitura, $id_obra, $id_estacao, $id_ponto, $id_parametro, $Id_Operador, $chave_unica);

            } // fecha o laço dos dados da leitura


            $retorno_alerta .= "</table>";


            $retorno_alerta .= "<div style='font-family: Arial, sans-serif; padding: 20px; border-radius: 10px; background-color: #f2f2f2; color: #737373; line-height: 1.5; font-size: 11px;'>
            <p>Você está recebendo este e-mail porque faz parte do Projeto:<strong> $nome_obra </strong> e possui Coleção Ativa de Notificação Personalizada para este projeto.</p>
            <p>Para não receber mais este tipo de e-mail, acesse suas Coleções de Notificações no Sistema e Personalize por Projeto.</p>
        </div>";

     
     // Inicia os Alertas para leitura fora 
  if ($status_leitura == "3") {

    
           // Criar uma instância da classe AlertaService
       $alertaService = new AlertaService($conexao, $twilioAccountSid, $twilioAuthToken, $twilioFromNumber, $totalVoiceToken);

       // E chamar enviarAlerta a partir dessa instância
       $alertaService->enviarAlerta($id_obra, $id_tipo_suporte, $retorno_alerta,$nome_estacao, $nome_obra, $assunto_notificacao);


       // enviarAlerta($conexao,$id_obra, $id_estacao, $nome_estacao, $nome_obra, $ID_RMM, $assunto_notificacao, $nome_suporte, $id_tipo_suporte, $retorno_alerta, $chave_unica);

       

   }

  
    
   

// Fecha a conexão com o banco de dados
$conexao = null;

} else {
    echo "Não há leituras para serem validadas";
}
 // fecha a validação do 1 select de leituras existentes para serem validadas