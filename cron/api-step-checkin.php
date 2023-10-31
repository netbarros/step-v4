<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header("Access-Control-Max-Age: 3600");


// Considero que já existe um autoloader compatível com a PSR-4 registrado
//header("Content-Type: application/json");

/*
 define('SGBD', 'mysql');
 define('HOST', '162.241.99.91'); //localhost
 define('DBNAME', 'step_bd'); //step
 define('CHARSET', 'utf8');
 define('USER', 'step_root');
 define('PASSWORD', 'F@087913');
 define('SERVER', 'linux');
 define('PORT', '3306');


 define('HOST', 'localhost');
define('DBNAME', 'step_bd');
define('CHARSET', 'utf8');
define('USER', 'root');
define('PASSWORD', '');
define('PORT', '3306');
 */

ini_set('memory_limit', '-1');
require $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';

if (!ini_get('date.timezone')) {
    date_default_timezone_set('America/Sao_Paulo');
}

$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();


$hr = date(" H ");
if ($hr >= 12 && $hr < 18) {
    $Saudacao = "Boa tarde!";
} else if ($hr >= 0 && $hr < 12) {
    $Saudacao = "Bom dia!";
} else {
    $Saudacao = "Boa noite!";
}



//===[ CHAVE ÚNICA da SESSAO] a cada acesso, o step registra uma codificação única de 32bits, encriptografada, de acesso unico para o login do usuário e logout do mesmo.
/* para cada nova leitura, é gerada uma chave_unica_sessao_atual, está é para mapearmos a rota desde a leitura do plcode e o que o usuário fez em sequência, 
checkin, abriu suporte, fez envio normal da leitura do plcode lido, enviou imagens nas leituras ou no suporte e ou reabertura de plcode, a chave_unica_sessao,
vinculará cada rotina do usuário, desde o início da etapa até a sua conclusão e leitura do próximo plcode, onde uma nova chave será gerada para o novo acompanhamento
da nova rotina do plcode lido, que se iniciará.
*/
$horario_completo_agora = microtime();
/* INÍCIO: Crio a Chave unica da Sessao para armazenamento e resgate das leituras e imagens que serão enviadas */
$chave_unica = bin2hex(random_bytes(33) . $horario_completo_agora);
/* Gerar strings aleatórias criptograficamente seguras, usamos 24 caracteres não repetitivos e aleatórios com criptografia nativa do PHP";
 serve como id referencial para salvar a midia e após salvar as leituras por essa chave que tbem constará na tb rmm, vinculo o id_rmm na tb midia_leitura,
  com a mesma chave unica (para controlar cada leitura enviada individualmente e não misturar as imagens enviadas) assim tbem poderemos vincular as midias 
  enviadas com um novo suporte gerado pelo painel da leitura e ver a relação entre elas, por imagens*/


$Chave_Unica_CRON = $chave_unica;

// Define o Período da Busca dos Dados
$Data_Atual_Periodo = date_create()->format('Y-m-d');
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-24 hours', strtotime($Data_Atual_Periodo)));
$dia_tarefa_cron = date('d');




//1ª FASE >> ===[ Valida as Leituras de RMM se estão OK ou fora de parâmetro ]===========>>

// seleção das novas leituras em RMM nas ultimas 24 horas 
//(validando os parametros de concentração min e max e alterando o status da leitura conforme ou não conforme, caso não conforme, gera suporte, dispara alertas (sms e email))

// INÍCIO DA 2 º FASE >> ===[Validação dos Checkins ]===========================================>>>>>>>>>

/* seleção dos ultimos checkins nas ultimas 24 horas (irá analisar o tempo da hora agendada com a hora lida e alterar o 
status e comparará o chekcin agendado com o realizado, caso false algum e o horário já tenha sido alcançado, irá gerar o alerta e comunicação) */
function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Raio da Terra em km

    // Converte as coordenadas de graus para radianos
    $lat1 = deg2rad(floatval($lat1));
    $lon1 = deg2rad(floatval($lon1));
    $lat2 = deg2rad(floatval($lat2));
    $lon2 = deg2rad(floatval($lon2));

    // Aplica a fórmula de Haversine
    $latDelta = $lat2 - $lat1;
    $lonDelta = $lon2 - $lon1;
    $a = sin($latDelta / 2) * sin($latDelta / 2) + cos($lat1) * cos($lat2) * sin($lonDelta / 2) * sin($lonDelta / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Calcula a distância em metros
    $distancia_calculada = $earthRadius * $c * 1000;

    return $distancia_calculada;
}
// fim da funcao para calcular a distancia entre dois pontos




/****** Função para Criar o Ticket de Suporte ***********/
function insere_suporte($conexao, $ID_SU, $id_tipo_suporte, $nome_suporte, $ID_Checkin, $leitura_rmm_checkin, $id_obra, $id_estacao, $id_ponto, $id_parametro, $ID_OP, $chave_unica) {

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
    $stmt->bindParam(':id_rmm_suporte', $ID_Checkin);
    $stmt->bindParam(':leitura_suporte', $leitura_rmm_checkin);
    $stmt->bindParam(':obra', $id_obra);
    $stmt->bindParam(':estacao', $id_estacao);
    $stmt->bindParam(':plcode', $id_ponto);
    $stmt->bindParam(':parametro', $id_parametro);
    $stmt->bindParam(':quem_abriu', $ID_OP);
    $stmt->bindParam(':chave_unica', $chave_unica);
    $stmt->bindParam(':status_suporte', $status_suporte_inicial);
  
    $result = $stmt->execute();
  
    $ultimo_id_suporte = $conexao->lastInsertId();
  
    
    // Insere conversa
    if ($result) {

      insere_conversa($conexao, $ultimo_id_suporte, $nome_suporte, $ID_OP, $ID_SU, $chave_unica,$id_estacao);
    }
  }
  
  

// função para criar o ticket de suporte

 // funcao para criar a 1 covnersa do suporte ticket gerado

 function insere_conversa($conexao, $ultimo_id_suporte,$nome_suporte, $ID_OP, $ID_SU, $chave_unica,$id_estacao) {
    $sql = "INSERT INTO suporte_conversas (id_suporte,id_remetente,destinatario_direto,conversa) VALUES (:id_suporte, :id_remetente, :destinatario_direto, :conversa)";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id_suporte', $ultimo_id_suporte);
    $stmt->bindParam(':id_remetente', $ID_OP);
    $stmt->bindParam(':destinatario_direto', $ID_SU);
    $stmt->bindParam(':conversa', $nome_suporte);
  
    $result = $stmt->execute();
    $ultimo_id_conversa = $conexao->lastInsertId();
    $total_conversa_suporte = $stmt->rowCount();
  
    if ($result) {
      $acao_log = "Chat Direct";
      $tipo_log = '48';
      $sql_log = "INSERT INTO log_leitura (chave_unica,id_usuario,acao_log,id_acao_log,estacao_logada,acao,tipo_log) VALUES (?,?,?,?,?,?,?)";
      $conexao->prepare($sql_log)->execute([$chave_unica, $ID_SU, $acao_log, $ultimo_id_suporte, $id_estacao, $acao_log, $tipo_log]);
    }
  }
  

// funcao para criar a 1 conversa do ticket gerado


$sql_checkin = $conexao->query("SELECT 
    ch.id_checkin as ID_Checkin,
    ch.id_estacao,
    ch.id_rmm as ID_RMM_Checkin,
    ch.id_colaborador as ID_Operador_Checkin,
    ch.id_periodo_ponto as ID_Perido_Checkin,
    ch.hora_lida as Hora_Checkin_Realizado,
    ch.chave_unica AS Chave_Unica_Checkin,  
    ch.data_cadastro_checkin AS Data_Checkin,
    ch.latitude_operador AS Latitude_Operador_Checkin,
    ch.longitude_operador AS Longitude_Operador_Checkin,
    
    peri.tipo_checkin,
    peri.hora_leitura as Hora_Leitura_Agendada,
    peri.ciclo_leitura,
    peri.modo_checkin_periodo as Modo_Checkin_Agendado,
    peri.id_periodo_ponto as ID_Perido_Agendado,

    p.id_ponto,
    p.nome_ponto,
    p.latitude_p AS Latitude_Ponto,
    p.longitude_p AS Longitude_Ponto,

    ch.id_parametro,
    pr.nome_parametro,
    pr.concen_min,
    pr.concen_max,
    u.nome_unidade_medida,
    r.id_rmm as ID_RMM_Leitura,
    r.status_leitura,
    r.id_operador as ID_Operador_RMM,    
    r.leitura_entrada,
    r.leitura_saida,
    r.chave_unica AS Chave_Unica_Rmm,
  
    logl.chave_unica AS Chave_Unica_Log,
    o.id_obra as id_obra_projeto,
    o.nome_obra,
    e.nome_estacao,
    tpl.nome_tipo_log AS Tipo_Log_Rotina,

    colabOP.cel_corporativo AS Tel_OP,
    colabOP.email_corporativo AS Email_Operador,

    colabSU.email_corporativo AS Email_Supervisor,
    colabSU.cel_corporativo AS Tel_SU,

    colabRO.email_corporativo AS Email_RO,
    colabRO.cel_corporativo AS Tel_Ro,

    colabSU.nome AS Nome_Supervisor,
    colabRO.nome AS Nome_RO,
    colabOP.nome AS Nome_Operador,

    uop.id AS ID_OP,
    usu.id AS ID_SU,
    uro.id AS ID_RO,
    cs.gps_metros


FROM
    checkin ch
        INNER JOIN
        periodo_ponto peri ON peri.id_periodo_ponto = ch.id_periodo_ponto

    INNER JOIN    
    pontos_estacao p ON p.id_ponto = ch.id_ponto
        INNER JOIN
    obras o ON o.id_obra = p.id_obra
        INNER JOIN
    estacoes e ON e.id_estacao = ch.id_estacao
        INNER JOIN
    usuarios uop ON uop.id = ch.id_colaborador
        LEFT JOIN
    usuarios usu ON usu.bd_id = e.supervisor
        INNER JOIN
    usuarios uro ON uro.bd_id = e.ro
        LEFT JOIN
    parametros_ponto pr ON ch.id_parametro = pr.id_parametro
        LEFT JOIN
    unidade_medida u ON pr.unidade_medida = u.id_unidade_medida
        LEFT JOIN
    rmm r ON ch.id_rmm = r.id_rmm
    
        LEFT JOIN
    colaboradores colabOP ON colabOP.id_colaborador = uop.bd_id
        LEFT JOIN
    colaboradores colabSU ON colabSU.id_colaborador = e.supervisor
        LEFT JOIN
    colaboradores colabRO ON colabRO.id_colaborador = e.ro
        LEFT JOIN
    log_leitura logl ON logl.chave_unica = ch.chave_unica
        LEFT JOIN
    tipo_log tpl ON tpl.id_tipo_log = logl.tipo_log

    INNER JOIN
    step_config cs ON cs.id_step = '1'

 WHERE
    ch.status_checkin = '5'
    AND DATE_FORMAT(ch.data_cadastro_checkin, '%Y-%m-%d') > '$Data_Intervalo_Periodo'
       GROUP BY ch.id_checkin
ORDER BY ch.data_cadastro_checkin ASC");
/*  ch.status_checkin = '5' AND  ----   status = 5. status automaticamente gerado a cada nova leitura, são aquelas que estão aguardando a validação da API para agora 
    verificar se cada leitura com status 5 (aguardando análise), está dentro ou fora do parâmetro informado (concen_min e concen_max)
   */
$total_checkin = $sql_checkin->rowCount();

//print_r($sql);

//exit;




if ($total_checkin > 0) { // inicia a validação dos checkins agendados e realizados

  
    $resultado_checkin = $sql_checkin->fetchAll(PDO::FETCH_ASSOC);

    $contador = 0;
    foreach ($resultado_checkin as $res) {

        $tipo_suporte_leitura = '94';
        
       
        $dados_controla_prazo =''; // variável que armazena os dados de controle de prazo de cada checkin

        $status_checkin = '';
        //=====[ busca e armazena as regras de alertas de acordo com o tipo de suporte ]=============//>>

        //============ [fim das variáveis setadas e GPS validado]==========//          

        $ID_Checkin = trim(isset($res['ID_Checkin'])) ? $res['ID_Checkin'] : '';

        $ID_RMM_Checkin = trim(isset($res['ID_Checkin'])) ? $res['ID_RMM_Checkin'] : '';

        $Chave_Unica_Rmm = trim(isset($res['Chave_Unica_Rmm'])) ? $res['Chave_Unica_Rmm'] : '';

        $Chave_Unica_Checkin = trim(isset($res['Chave_Unica_Checkin'])) ? $res['Chave_Unica_Checkin'] : '';

        $ID_Perido_Checkin = trim(isset($res['ID_Perido_Checkin'])) ? $res['ID_Perido_Checkin'] : '';

        $Hora_Checkin_Realizado = trim(isset($res['Hora_Checkin_Realizado'])) ? $res['Hora_Checkin_Realizado'] : '';

        $Hora_Leitura_Agendada = trim(isset($res['Hora_Leitura_Agendada'])) ? $res['Hora_Leitura_Agendada'] : '';

        $Data_Checkin = trim(isset($res['Data_Checkin'])) ? $res['Data_Checkin'] : '';

        $unidade_medida_lida = trim(isset($res['nome_unidade_medida'])) ? $res['nome_unidade_medida'] : '';

        $nome_plcode = $res['nome_ponto'];

        $id_ponto = $res['id_ponto'];

        $id_operador = $res['ID_Operador_Checkin'];

        $id_parametro = trim(isset($res['id_parametro'])) ? $res['id_parametro'] : '0';

        $nome_parametro = trim(isset($res['nome_parametro'])) ? $res['nome_parametro'] : '';

        $id_obra = $res['id_obra_projeto'];

        $nome_obra = $res['nome_obra'];

        $nome_estacao = $res['nome_estacao'];

        $id_estacao = $res['id_estacao'];

        $Latitude_Ponto = trim(isset($res['Latitude_Ponto'])) ? $res['Latitude_Ponto'] : '';

        $Longitude_Ponto = trim(isset($res['Longitude_Ponto'])) ? $res['Longitude_Ponto'] : '';

        $Latitude_Operador_Checkin = trim(isset($res['Latitude_Operador_Checkin'])) ? $res['Latitude_Operador_Checkin'] : '';

        $Longitude_Operador_Checkin = trim(isset($res['Longitude_Operador_Checkin'])) ? $res['Longitude_Operador_Checkin'] : '';



        $origem_Leitura = trim(isset($res['origem_leitura_parametro'])) ? $res['origem_leitura_parametro'] : '';

        $tipo_checkin =  trim(isset($res['tipo_checkin'])) ? $res['tipo_checkin'] : ''; //  'ponto_parametro',  'ponto_plcode', 'tarefa_delegada' > implementação futura depois do apk

        $ciclo_leitura = trim(isset($res['ciclo_leitura'])) ? $res['ciclo_leitura'] : '';  // 1 = diário, 2 = semanal

        $modo_checkin_periodo = trim(isset($res['Modo_Checkin_Agendado'])) ? $res['Modo_Checkin_Agendado'] : '';  // 1 = Livre , 2 = Horário Controlado (Agendado)

        $ID_OP = trim(isset($res['ID_OP'])) ? $res['ID_OP'] : '';
        $nome_Operador = trim(isset($res['Nome_Operador'])) ? $res['Nome_Operador'] : '';
        $email_Operador = trim(isset($res['Email_Operador'])) ? $res['Email_Operador'] : '';
        $Tel_OP = trim(isset($res['Tel_OP'])) ? $res['Tel_OP'] : '';

        $ID_SU = trim(isset($res['ID_SU'])) ? $res['ID_SU'] : '';
        $nome_Supervisor = trim(isset($res['Nome_Supervisor'])) ? $res['Nome_Supervisor'] : '';
        $email_Supervisor = trim(isset($res['Email_Supervisor'])) ? $res['Email_Supervisor'] : '';
        $Tel_SU = trim(isset($res['Tel_SU'])) ? $res['Tel_SU'] : '';

        $ID_RO = trim(isset($res['ID_RO'])) ? $res['ID_RO'] : '';
        $nome_RO = trim(isset($res['Nome_RO'])) ? $res['Nome_RO'] : '';
        $email_RO = trim(isset($res['Email_RO'])) ? $res['Email_RO'] : '';
        $Tel_Ro = trim(isset($res['Tel_Ro'])) ? $res['Tel_Ro'] : '';

        // Reserva as variaveis apra comparação e preenchimento do envio dos alertas, caso seja necessário
        // trata e desmembra a data da leitura //
        $data_leitura = $res['Data_Checkin'];
        //$data_leitura =  strtotime($phpdate) * 1000;
        $hora_min =  date('H:i', strtotime($data_leitura));
        $dia_mes_ano =  date('d/m/Y', strtotime($data_leitura));
        //====<< 


        $STEP_Controla_GPS = trim(isset($res['gps_metros'])) ? trim($res['gps_metros']) : '';
        $STEP_Controla_GPS = isset($res['gps_metros']) ? doubleval(trim($res['gps_metros'])) : NULL;
       

        if(filter_var($STEP_Controla_GPS, FILTER_VALIDATE_FLOAT) === false) {
            // Lança uma exceção ou define um valor padrão para $controla_gps.
            throw new Exception("$STEP_Controla_GPS não é um valor decimal válido.");
            // ou
            // $controla_gps = '0.0'; // Um valor decimal padrão.
        }

        
        // trata e separa a leitura em colunia unica, inpependente se é entrada ou saida, para amostragem
        // Operadores Ternários para Parametros e seus derivados acontece, por haver leitura de RMM/Checkin apenas com o PLCode.

        $dias_semana_periodo = "";

        $nome_dia_semana_periodo = "";

        $diasemana_numero = date('w', time());

        $retorno_alerta = "Alerta Tarefa - STEP\n\r";

        $nome_suporte = "Checkin STEP\n\r";


        if ($tipo_checkin == '2') {

            if ($origem_Leitura == '1') {
                $leitura_rmm_checkin = $res['leitura_entrada'];
            }

            if ($origem_Leitura == '2') {
                $leitura_rmm_checkin = $res['leitura_entrada'];
            }
            if ($origem_Leitura != '1' && $origem_Leitura != '2') {

                $leitura_rmm_checkin = $res['leitura_entrada'];
            }
        } else {

            $leitura_rmm_checkin = "0";
        }




        //====== INÎCIO validar tbem a geolocalização enviada na leitura do Checkin com a geolocalização do PLCode===>>>
        // se todas as coordenadas estiverem disponíveis realizamos o cálculo de distancia do ponto de leitura
        if ($STEP_Controla_GPS!= 0 && $STEP_Controla_GPS !=NULL ) { // margem de erro de escala do algoritimo do gps de 90 metros


            $nome_suporte = "Alerta GPS\n\r";

            
            $id_tipo_suporte = '99';
            $distancia_arredondada = '';
           // se todas as coordenadas estiverem disponíveis realizamos o cálculo de distancia do ponto de leitura
                if ($Latitude_Ponto != "" && $Longitude_Ponto != "" && $Latitude_Operador_Checkin != ""  && $Longitude_Operador_Checkin != "") {


                    // Script API pega endereço Google Maps >>>>
                $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$Latitude_Operador_Checkin,$Longitude_Operador_Checkin&key=AIzaSyBSIkQdqUjEKXybmAFvFEKCLxkNlFA_aJ8";
                $dadosSite = my_file_get_contents($url);

                if ($dadosSite !== false) {
                    $data = json_decode($dadosSite, true);

                    if (isset($data['results'][1]['formatted_address'])) {
                        $Endereco_Origem = 'Local da Leitura: ' . $data['results'][1]['formatted_address'];
                    } else {
                        $Endereco_Origem = 'Endereço não encontrado';
                    }
                } else {
                    $Endereco_Origem = 'Erro ao acessar a API do Google Maps';
                }

                // Calcula a distância entre as coordenadas
                $distancia_calculada = calcularDistancia($Latitude_Ponto, $Longitude_Ponto, $Latitude_Operador_Checkin, $Longitude_Operador_Checkin);

                $distancia_arredondada = round($distancia_calculada, 2);
                
            } else {
                $distancia_arredondada = 'Dados de Geolocalização não disponíveis';
                $Endereco_Origem = 'Dados de Geolocalização não disponíveis';
            }


            //STEP_Controla_GPS

  //STEP_Controla_GPS
  if($distancia_arredondada!=NULL && $distancia_arredondada > $STEP_Controla_GPS){

       // $assunto_notificacao .= "Leitura Fora da Geo-Localização permitida";
       $nome_suporte .= "Leitura Fora da Geo-Localização permitida de até $STEP_Controla_GPS metros - Distância Detectada: $distancia_arredondada metros.<br><br>"; 
        $id_tipo_suporte = '99';

        $Status_Gps = '2'; //Leitura fora de área
       
        
 
        $nome_suporte .= "<div style='font-family: Arial, sans-serif; padding:  5px 5px 5px 5px; margin-left: 5px; margin-top: 5px; border-radius: 10px; background-color: #f2f2f2; color: #737373; line-height: 1.5; font-size: 11px;'>
        <p>Origem Coletada pela API do Google Maps:</p>
        <p><strong>$Endereco_Origem</strong></p></div>";

        $nome_suporte .= "<div style='font-family: Arial, sans-serif; padding:  5px 5px 5px 5px; margin-left: 5px; margin-top: 5px; border-radius: 10px; background-color: #e5e5e5; color: #737373; line-height: 1.5; font-size: 11px;'>
        <p>Coordenadas Comparativas:</p>
        <p><strong>$Latitude_Operador</strong></p>
        <p><strong>$Longitude_Operador</strong></p>
        <p>GPS do PLCode Cadastrado:</p>
        <p><strong>$Latitude_Ponto</strong></p>
        <p><strong>$Longitude_Ponto</strong></p></div>";

       

        insere_suporte($conexao, $ID_SU, $id_tipo_suporte, $nome_suporte, $ID_Checkin, $leitura_rmm_checkin, $id_obra, $id_estacao, $id_ponto, $id_parametro, $ID_OP, $chave_unica);

/// vou transformaar esse bloco numa funcao para ser chamda no final do script
        $sql_gps = "INSERT INTO gps_fora (
        id_estacao_gps,
        id_plcode_gps, 
        id_operador,
        lat_ponto,
        long_ponto,
        lat_cap,
        long_cap,
        endereco_cap,
        distancia,
        status_gps,
        chave_unica
        ) 
        VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
            )";
        $conexao->prepare($sql_gps)->execute([
            $id_estacao,
            $id_ponto,
            $id_operador,
            $Latitude_Ponto,
            $Longitude_Ponto,
            $Latitude_Operador_Checkin,
            $Longitude_Operador_Checkin,
            $Endereco_Origem,
            $distancia_arredondada,
            $Status_Gps,
            $Chave_Unica_Rmm

        ]);

        $ultimo_id_gps_fora = $conexao->lastInsertId();


        //  Gera Log da Leitura fora do GPS
        $acao_log = "GPS Fora de Área no Checkin";
        $tipo_log = '41'; // GPS Fora de Área
        $id_acao_log = $ultimo_id_gps_fora;


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
            $Chave_Unica_Rmm,
            $id_operador,
            'GPS Fora',
            $acao_log,
            $id_acao_log,
            $id_estacao,
            $tipo_log
        ]);
        // Fecha Log <<<<<<<  
        
    }// fecha if distancia_arredondada     
        
/**** Encerra validacao se a leitura estiver fora dos padroes de GPS, caso nas configurações do STEP temos a distância máxima permitida para a leitura, caso for 0, não temos controle de distância. */
   
         }

        //============ [fim das variáveis setadas e GPS validado]=============<><><><><>



        //=====[ // horário livre , só valida GPS e verifica se é diário ou semanal, caso semanal exibe o dia da semana lido]=====
        if ($modo_checkin_periodo == '1') { // 1 livre


            if ($ciclo_leitura == '1') { //se a leitura é diária e sem controle de horário

                $status_checkin = '1';

                $nome_dia_semana_periodo .= "Diário";

                $hoje_tem = "sim";
            }




            if ($ciclo_leitura == '2') { // ciclo de leitua semanal, com horário livre

                $status_checkin = '1';


                $consulta = $conexao->query("SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana FROM periodo_dia_ponto
                INNER JOIN dia_semana ON periodo_dia_ponto.dia_semana = dia_semana.representa_php WHERE periodo_dia_ponto.id_periodo_ponto ='$ID_Perido_Checkin'");
                $json_data = $consulta->fetchAll(PDO::FETCH_ASSOC);


                if ($json_data) {


                    foreach ($json_data as $res) {

                        $dias_semana_periodo .= $res['representa_php'] . ' ';

                        if ($diasemana_numero == $dias_semana_periodo) {


                            $nome_dia_semana_periodo .=  "<span class='badge badge-success'>" . $res['nome_dia_semana'] . "</span>";

                            $hoje_tem = "sim";
                        }

                        if ($diasemana_numero != $dias_semana_periodo) {


                            $nome_dia_semana_periodo .=  "<span class='badge badge-dark'>" . $res['nome_dia_semana'] . "</span>";

                            $hoje_tem = "nao";
                        }
                    }
                }
            } // fecha leitura com ciclo semanal, e horário livre


        } // fecha // modo_checkin_periodo: horário livre , ciclo diário ou semanal - 


        //==========[ Fecha LOOP de validação do checkin diário ou semanal com horário livre]=======================




        //==========[ Abre LOOP de validação do checkin diário semanal com horário controlado]=======================

        if ($modo_checkin_periodo == '2') { // horário controlado 

            if ($ciclo_leitura == '1') { // diário con controle de horário



                date_default_timezone_set('America/Sao_Paulo');

                $entrada = new DateTime($Hora_Leitura_Agendada);
                $saida = new DateTime($Hora_Checkin_Realizado);
                
                $intervalo = $saida->diff($entrada);
                
                $tempo_decorrido = $intervalo->format('%h') . "h" . $intervalo->format('%i') . "min";
                
                $prazo_decorrido = $intervalo->i + $intervalo->h * 60; // em minutos
                $prazo_decorrido_horas = $intervalo->h + $intervalo->days * 24; // em horas
                
                if ($prazo_decorrido <= 30) {
                    $status_checkin = '1'; // leitura dentro do prazo
                    $retorno_alerta = "<br> Checkin Adiantado em $tempo_decorrido de antecedência.";
                }

                if ($prazo_decorrido > 30 && $prazo_decorrido < 35) {

                    
                    $status_checkin = '2'; // checkin dentro da carencia de 35 min.
                    $retorno_alerta = "<br> Checkin, com $tempo_decorrido de diferença.\n";

                    if ($ID_RMM_Checkin != '' && $tipo_checkin == '2') {

                        $retorno_alerta = "Leitura do Indicador $nome_parametro, valor: $leitura_rmm_checkin $unidade_medida_lida \n\r";
                    } else {
                        $retorno_alerta = "<br> <br> <b>Checkin Presencial</b>";
                    }
                }


                if ($prazo_decorrido > 35) {

                    $status_checkin = '3';
                   

                    if ($ID_RMM_Checkin != '' && $tipo_checkin == '2') {

                        //Checkin Fora do Prazo
                        $retorno_alerta = "\n\rCheckin com diferença de $tempo_decorrido em relação ao Horário agendado: $Hora_Leitura_Agendada.\n\r";


                        $retorno_alerta = "\n\rLeitura do Indicador $nome_parametro, valor: $leitura_rmm_checkin $unidade_medida_lida\n\r";

                        $retorno_alerta = "    <p style='margin-bottom: 10px; padding-top: 30px; font-size: 14px;'>
                            Horário Agendado: $Hora_Leitura_Agendada <br>
                            Horário Realizado: $Hora_Checkin_Realizado <br><br>
                            <b>Diferença de Prazo: </b> $tempo_decorrido 
                            </p>";
                    } else {
                        $retorno_alerta = "<p style='margin-bottom: 10px; padding-top: 30px; font-size: 14px;'>
                          \n\r <b>Checkin Presencial</b>\n\r
                            </p>";
                    }
                }
            } // fecha com controle de horário diário


            if ($ciclo_leitura == '2') { // semanal com controle de horário

// Prepara a consulta SQL para evitar a injeção de SQL.
$consulta = $conexao->prepare("
    SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana 
    FROM periodo_dia_ponto
    INNER JOIN dia_semana ON periodo_dia_ponto.dia_semana = dia_semana.representa_php 
    WHERE periodo_dia_ponto.id_periodo_ponto = :id_periodo_ponto
");

// Vincula o valor à consulta preparada.
$consulta->bindValue(':id_periodo_ponto', $ID_Perido_Checkin);

// Executa a consulta.
$consulta->execute();

// Obtém todos os resultados como um array associativo.
$json_data = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Conta o número de linhas retornadas.
$count = $consulta->rowCount();

// Verifica se algum resultado foi retornado.
if ($count > 0) {
    // Percorre cada resultado.
    foreach ($json_data as $res) {
        // Restante do seu código...
    


                        $dias_semana_periodo .= $res['representa_php'] . ' ';
                        $prazo_decorrido ='';

                        if ($diasemana_numero == $dias_semana_periodo) {


                            $nome_dia_semana_periodo .=  "<span class='badge badge-success'>" . $res['nome_dia_semana'] . "</span>\n\r";

                            $hoje_tem = "sim";
                        }

                        if ($diasemana_numero != $dias_semana_periodo) {


                            $nome_dia_semana_periodo .=  "<span class='badge badge-dark'>" . $res['nome_dia_semana'] . "</span>\n\r";

                            $hoje_tem = "nao";
                        }
                    }
                }

              
                date_default_timezone_set('America/Sao_Paulo');

                $entrada = new DateTime($Hora_Leitura_Agendada);
                $saida = new DateTime($Hora_Checkin_Realizado);
                
                $intervalo = $saida->diff($entrada);
                
                $tempo_decorrido = $intervalo->format('%h') . "h" . $intervalo->format('%i') . "min";
                
                $prazo_decorrido = $intervalo->i + $intervalo->h * 60; // em minutos
                $prazo_decorrido_horas = $intervalo->h + $intervalo->days * 24; // em horas


                if ($prazo_decorrido <= 30) {

                   
                    $status_checkin = '1'; // leitura dentro do prazo
                    $retorno_alerta = "\n\r Checkin Adiantado em $tempo_decorrido de antecedência.\n\r";
                }

                if ($prazo_decorrido > 30 && $prazo_decorrido < 35) {

                    $status_checkin = '2'; // checkin dentro da carencia de 35 min.
                    $retorno_alerta = "\n\r Checkin, com $tempo_decorrido de diferença.\n\r";
                     // n envia alerta


                    if ($ID_RMM_Checkin != '' && $tipo_checkin == '2') {

                        //Checkin Fora do Prazo
                        $retorno_alerta = "\n\rCheckin com diferença de $tempo_decorrido em relação ao Horário agendado: $Hora_Leitura_Agendada.\n\r";

                        $retorno_alerta = "\n\rLeitura do Indicador $nome_parametro, valor: $leitura_rmm_checkin $unidade_medida_lida\n\r";

                        $retorno_alerta = "    <p style='margin-bottom: 10px; padding-top: 30px; font-size: 14px;'>
                                Horário Agendado: $Hora_Leitura_Agendada <br>
                                Horário Realizado: $Hora_Checkin_Realizado <br><br>
                                <b>Diferença de Prazo: </b> $tempo_decorrido 
                                </p>";
                    } else {
                        $retorno_alerta = "<p style='margin-bottom: 10px; padding-top: 30px; font-size: 14px;'>
                                \n\r <b>Checkin Presencial</b>\n\r
                                    </p>";
                    }
                }


                if ($prazo_decorrido > 35) {

                   
                    $status_checkin = '3';

                    if ($ID_RMM_Checkin != '' && $tipo_checkin == '2') {

                        //Checkin Fora do Prazo
                        $retorno_alerta = "\n\rCheckin com diferença de $tempo_decorrido em relação ao Horário agendado: $Hora_Leitura_Agendada.\n\r";




                        $retorno_alerta = "\n\rLeitura do Indicador $nome_parametro, valor: $leitura_rmm_checkin $unidade_medida_lida\n\r";

                        $retorno_alerta = "    <p style='margin-bottom: 10px; padding-top: 30px; font-size: 14px;'>
                            Horário Agendado: $Hora_Leitura_Agendada <br>
                            Horário Realizado: $Hora_Checkin_Realizado <br><br>
                            <b>Diferença de Prazo: </b> $tempo_decorrido 
                            </p>";
                    } else {
                        $retorno_alerta = "<p style='margin-bottom: 10px; padding-top: 30px; font-size: 14px;'>
                          \n\r <b>Checkin Presencial</b>\n\r
                            </p>";
                    }
                }
            }
            //====== FIM valida a diferença de hora agendada e hora da leitura, para por status do checkin em tempo ou em atraso== se em atraso irá abrir o suporte===<<<

        } // modo_checkin='2' horário controlado , valida GPS e verifica se é diário ou semanal, caso semanal exibe o dia da semana lido


        //====[ Início da Validação do STETUS do Checkin -  Aqui se determina se envia ou não os alertas =========================

        if ($status_checkin == '3') { // 1 no prazo, 2 dentro do prazo 3 atrasado 5 aguardando, 0 indefinido

            // envia alerta
            $contador++;

            // gravo o tempo decorrido entre o agendado e a hora lida de cada checkin (evento)
            $sql_update_tempo_check = $conexao->query("UPDATE checkin SET prazo_decorrido='$prazo_decorrido', status_checkin='3' WHERE id_checkin='$ID_Checkin'");

            // abre o chamado de suporte:
            $status_suporte = '1';

            $valida_leitura_rmm_checkin = is_numeric($leitura_rmm_checkin) ? true : false;

            if ($valida_leitura_rmm_checkin == true) {

                $leitura_rmm_checkin = $leitura_rmm_checkin;
            } else {
                $leitura_rmm_checkin = '0000';
            }


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
            $stmt->bindParam(':tipo_suporte', $tipo_suporte_leitura);
            $stmt->bindParam(':motivo_suporte', $retorno_alerta);
            $stmt->bindParam(':id_rmm_suporte', $ID_RMM_Checkin);
            $stmt->bindParam(':leitura_suporte', $leitura_rmm_checkin);
            $stmt->bindParam(':obra', $id_obra);
            $stmt->bindParam(':estacao', $id_estacao);
            $stmt->bindParam(':plcode', $id_ponto);
            $stmt->bindParam(':parametro', $id_parametro);
            $stmt->bindParam(':quem_abriu', $ID_OP);
            $stmt->bindParam(':chave_unica', $Chave_Unica_Checkin);
            $stmt->bindParam(':status_suporte', $status_suporte);

            $result = $stmt->execute();

            $ultimo_id_suporte = $conexao->lastInsertId();



            //  Gera Log do Novo Chamado de Suporte
            $acao_log = "Checkin em Atraso, gerado suporte ID: $ultimo_id_suporte";
            $tipo_log = '47'; // Novo Suporte por Checkin em Atraso


            $sql_log = "INSERT INTO log_leitura (
            chave_unica,
            id_usuario, 
            acao_log,
            id_acao_log,
            estacao_logada,
            acao,
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
                $Chave_Unica_Checkin,
                $id_operador,
                $acao_log,
                $ultimo_id_suporte,
                $id_estacao,
                "Checkin em Atraso, gerado suporte ID: $ultimo_id_suporte",
                $tipo_log
            ]);
            // Fecha Log <<<<<<<


            // Envia direct ao supervisor responsável

            if ($sql_log) {

                $sql = "INSERT INTO suporte_conversas(
                id_suporte,id_remetente,destinatario_direto,conversa
                ) VALUES(
                    :id_suporte,
                :id_remetente,
                :destinatario_direto,
                :conversa

            )";
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(':id_suporte', $ultimo_id_suporte);
                $stmt->bindParam(':id_remetente', $id_operador);
                $stmt->bindParam(':destinatario_direto', $ID_SU);
                $stmt->bindParam(':conversa', $retorno_alerta);


                $result = $stmt->execute();

                $ultimo_id = $conexao->lastInsertId();

                $total_suporte =     $stmt->rowCount();


                if ($result) {

                    //  Gera Log do Novo Chamado de Suporte
                    $acao_log = "Chat Direct";
                    $tipo_log = '48'; //Novo Chat Direct


                    $sql_log = "INSERT INTO log_leitura (
            chave_unica,
            id_usuario, 
            acao_log,
            id_acao_log,
            estacao_logada,
            acao,
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
                        $Chave_Unica_Checkin,
                        $ID_SU,
                        $acao_log,
                        $ultimo_id_suporte,
                        $id_estacao,
                        "Chat Direct Enviado referente ao Checkin no suporte ID: $ultimo_id_suporte",
                        $tipo_log
                    ]);
                    // Fecha Log <<<<<<<

                }
            }
        } // fecha status checkin 3 (em alerta)


        if ($status_checkin == '2') {

          

            //hoje teve, não houve fora de horario e não houve atraso
            if ($modo_checkin_periodo == '2') { // controla horario
                // gravo o tempo decorrido entre o agendado e a hora lida de cada checkin (evento)
                $sql_update_tempo_check = $conexao->query("UPDATE checkin SET prazo_decorrido='$prazo_decorrido', status_checkin='2' WHERE id_checkin='$ID_Checkin'");

               
            }

            if ($modo_checkin_periodo == '1') { // não controla horario
                // gravo o tempo decorrido entre o agendado e a hora lida de cada checkin (evento)
                $sql_update_tempo_check = $conexao->query("UPDATE checkin SET  status_checkin='2' WHERE id_checkin='$ID_Checkin'");

               
            }
        }

        if ($status_checkin == '1') {

            //hoje teve, não houve fora de horario e não houve GPS fora
            if ($modo_checkin_periodo == '2') { // controla horario
                // gravo o tempo decorrido entre o agendado e a hora lida de cada checkin (evento)
                $sql_update_tempo_check = $conexao->query("UPDATE checkin SET prazo_decorrido='$prazo_decorrido', status_checkin='1' WHERE id_checkin='$ID_Checkin'");

                
            }

            if ($modo_checkin_periodo == '1') { // não controla horario
                // gravo o tempo decorrido entre o agendado e a hora lida de cada checkin (evento)
                $sql_update_tempo_check = $conexao->query("UPDATE checkin SET  status_checkin='1' WHERE id_checkin='$ID_Checkin'");

                 // não envia alerta
            }
        }



        //====[ FIM Aqui se determina se envia ou não os alertas =========================

        //=====[ Monitorado pela API de Suportes para checkins fora do prazo limite]=========

    
    } // fecha o foreach do laço dos comparativos das variáveis armazenadas



    sleep(3);

    if ($total_checkin > 0) { // gravo o LOG se a CRON do Checkin foi realizada com Sucesso


        //  Gera Log da Leiitura fora do Checkin
        $acao_log = "CRON Chekcin Leituras Finalizado com Sucesso";
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
            $Chave_Unica_CRON,
            '1',
            'CRON Chekcin Leituras Finalizado com Sucesso',
            $acao_log,
            $id_acao_log,
            '0',
            $tipo_log
        ]);
        // Fecha Log <<<<<<<
    } //// gravo o LOG se a CRON do Checkin foi realizada com Sucesso


    echo "Houveram <b>".$contador."</b> Check-ins, analisados e com suporte criado, para esta consulta.";

} // fecha total de checkin > 0 













function my_file_get_contents($site_url)
{
    $ch = curl_init();
    $timeout = 5; // set to zero for no timeout
    curl_setopt($ch, CURLOPT_URL, $site_url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    ob_start();
    curl_exec($ch);
    curl_close($ch);
    $file_contents = ob_get_contents();
    ob_end_clean();
    return $file_contents;
}



function limita_caracteres($texto, $limite, $quebra = true)
{
    $tamanho = strlen($texto);
    if ($tamanho <= $limite) { //Verifica se o tamanho do texto é menor ou igual ao limite
        $novo_texto = $texto;
    } else { // Se o tamanho do texto for maior que o limite
        if ($quebra == true) { // Verifica a opção de quebrar o texto
            $novo_texto = trim(substr($texto, 0, $limite)) . "...";
        } else { // Se não, corta $texto na última palavra antes do limite
            $ultimo_espaco = strrpos(substr($texto, 0, $limite), " "); // Localiza o útlimo espaço antes de $limite
            $novo_texto = trim(substr($texto, 0, $ultimo_espaco)) . "..."; // Corta o $texto até a posição localizada
        }
    }
    return $novo_texto; // Retorna o valor formatado
}


function intervalo($entrada, $saida)
{
    $entrada = explode(':', $entrada);
    $saida   = explode(':', $saida);
    $minutos = ($saida[0] - $entrada[0]) * 60 + $saida[1] - $entrada[1];
    if ($minutos < 0) $minutos += 24 * 60;
    return sprintf('%d:%d', $minutos / 60, $minutos % 60);
}

function mintohora($minutos)
{
    $hora = floor($minutos / 60);
    $resto = $minutos % 60;
    return $hora . ':' . $resto;
}
