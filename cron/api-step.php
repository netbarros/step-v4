<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header("Access-Control-Max-Age: 3600");
ini_set('memory_limit', '-1');

// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'].'/conexao.php';
$conexao = Conexao::getInstance();

if (!ini_get('date.timezone')) {
    date_default_timezone_set('America/Sao_Paulo');
}


//===[ CHAVE ÚNICA da SESSAO] a cada acesso, o step registra uma codificação única de 32bits, 
$horario_completo_agora = microtime();
/* INÍCIO: Crio a Chave unica da Sessao para armazenamento e resgate das leituras e imagens que serão enviadas */
$chave_unica = bin2hex(random_bytes(33) . $horario_completo_agora);
/* Gerar strings aleatórias criptograficamente seguras, usamos 24 caracteres não repetitivos e aleatórios com criptografia nativa do PHP";*/
 

$Chave_Unica_CRON = $chave_unica;

// Define o Período da Busca dos Dados
$Data_Atual_Periodo = date('Y-m-d H:i'); // Retorna a data e a hora atuais
 
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-1 day')); // Retorna a data de 2 meses atrás

$dia_tarefa_cron = date('d');

//1ª FASE >> ===[ Valida as Leituras de RMM se estão OK ou fora de parâmetro ]===========>>
#VERITICA INTEGRIDADE DOS DADOS
//$controle_bd = $conexao->query("DELETE FROM rmm WHERE id_parametro='0' OR leitura_entrada='10101.00'");


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

/****** Função para Criar o Ticket de Suporte ***********/


function cria_suporte($leitura, $id_tipo_suporte, $nome_suporte, $ID_RMM, $id_ponto, $id_estacao, $id_obra, $id_parametro, $ID_SU , $ID_OP, $chave_unica, $data_leitura, $status_suporte, $conexao)
{
    try {
        $sql = "INSERT INTO suporte (leitura_suporte, tipo_suporte, motivo_suporte, id_rmm_suporte , plcode, estacao , obra, parametro ,  quem_abriu , chave_unica, data_open , status_suporte)
            VALUES (:leitura_suporte, :tipo_suporte, :motivo_suporte, :id_rmm_suporte ,:plcode, :estacao , :obra, :parametro ,  :quem_abriu , :chave_unica, :data_open , :status_suporte)
            ON DUPLICATE KEY UPDATE
            id_rmm_suporte = VALUES(id_rmm_suporte),
            quem_abriu  = VALUES(quem_abriu),
            tipo_suporte  = VALUES(tipo_suporte),
            motivo_suporte = VALUES(motivo_suporte),
            plcode = VALUES(plcode),
            estacao  = VALUES(estacao),
            obra = VALUES(obra),
            parametro  = VALUES(parametro),
            leitura_suporte = VALUES(leitura_suporte),
            data_open = VALUES(data_open),
            chave_unica = VALUES(chave_unica),
            status_suporte = VALUES(status_suporte)";
    
        $stmt = $conexao->prepare($sql);
        $stmt->bindValue(':id_rmm_suporte', $ID_RMM, PDO::PARAM_STR);
        $stmt->bindValue(':leitura_suporte', $leitura, PDO::PARAM_STR);
        $stmt->bindValue(':tipo_suporte', $id_tipo_suporte, PDO::PARAM_STR);
        $stmt->bindValue(':motivo_suporte', $nome_suporte, PDO::PARAM_STR);
        $stmt->bindValue(':plcode', $id_ponto, PDO::PARAM_INT);
        $stmt->bindValue(':estacao', $id_estacao, PDO::PARAM_INT);
        $stmt->bindValue(':obra', $id_obra, PDO::PARAM_INT);
        $stmt->bindValue(':parametro', $id_parametro, PDO::PARAM_INT);
        $stmt->bindValue(':quem_abriu', $ID_OP, PDO::PARAM_INT);
        $stmt->bindValue(':chave_unica', $chave_unica, PDO::PARAM_STR);
        $stmt->bindValue(':data_open', $data_leitura, PDO::PARAM_STR);
        $stmt->bindValue(':status_suporte', $status_suporte, PDO::PARAM_INT);
        $stmt->execute();
    
        // Pega o último ID inserido
        global $ultimo_id_suporte;
        $ultimo_id_suporte = $conexao->lastInsertId();
    
        insere_conversa($conexao, $ultimo_id_suporte, $nome_suporte, $ID_OP, $ID_SU, $chave_unica, $id_estacao);
    } catch (Exception $e) {
        error_log($e->getMessage());
        error_log($e->getTraceAsString());
        error_log("Falha ao criar o ticket de suporte");
        return false;
    }
    
}
function insere_conversa($conexao, $ultimo_id_suporte, $nome_suporte, $ID_OP, $ID_SU, $chave_unica, $id_estacao) {

    try {
        $resumo_suporte = limita_caracteres($nome_suporte, 600);

        $sql = "INSERT INTO suporte_conversas (id_suporte, id_remetente, destinatario_direto, conversa)
                VALUES (:id_suporte, :id_remetente, :destinatario_direto, :conversa)";
                
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
            $sql_log = "INSERT INTO log_leitura (chave_unica, id_usuario, acao_log, id_acao_log, estacao_logada, acao, tipo_log) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
            $acao_log .= "Conversa iniciada automaticamente, através da criação do Ticket $nome_suporte | ID: $ultimo_id_suporte";

            $conexao->prepare($sql_log)->execute([$chave_unica, $ID_SU, $acao_log, $ultimo_id_suporte, $id_estacao, $acao_log, $tipo_log]);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        error_log($e->getTraceAsString());
        error_log("Falha ao inserir conversa no suporte");
        return false;
    }
}


  

// função para criar o ticket de suporte

 // funcao para criar a 1 covnersa do suporte ticket gerado

/*  function insere_conversa($conexao, $ultimo_id_suporte,$nome_suporte, $ID_OP, $ID_SU, $chave_unica,$id_estacao) {

    
    $resumo_suporte = limita_caracteres($nome_suporte, 600);
    $sql = "INSERT INTO suporte_conversas (id_suporte, id_remetente, destinatario_direto, conversa)";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id_suporte', $ultimo_id_suporte);
    $stmt->bindParam(':id_remetente', $ID_OP);
    $stmt->bindParam(':destinatario_direto', $ID_SU);
    $stmt->bindParam(':conversa', $nome_suporte);
    $result = $stmt->execute();

  
    $result = $stmt->execute();
    $ultimo_id_conversa = $conexao->lastInsertId();
    $total_conversa_suporte = $stmt->rowCount();
  
    if ($result) {
      $acao_log = "Chat Direct";
      $tipo_log = '48';
      $sql_log = "INSERT INTO log_leitura (chave_unica, id_usuario, acao_log, id_acao_log,estacao_logada, acao, tipo_log) VALUES (?,?,?,?,?,?,?)";
      $acao_log .= "Conversa iniciada automaticamente, através da criação do Ticket $nome_suporte | ID: $ultimo_id_suporte";
      $conexao->prepare($sql_log)->execute([$chave_unica, $ID_SU, $acao_log, $ultimo_id_suporte, $id_estacao, $acao_log, $tipo_log]);
    }
  } */
  

// funcao para criar a 1 conversa do ticket gerado
    

    function update_status_rmm($conexao, $status_leitura, $ID_RMM)
    {
        return $conexao->query("UPDATE rmm SET status_leitura='{$status_leitura}' WHERE id_rmm='{$ID_RMM}' ");
    }

// seleção das novas leituras em RMM nas ultimas 24 horas 
//(validando os parametros de concentração min e max e alterando o status da leitura conforme ou não conforme, caso não conforme, gera suporte, dispara alertas (sms e email))

$sql = $conexao->query("SELECT 
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
    r.data_revisao,
    r.chave_unica AS Chave_Unica_Rmm,
    logl.chave_unica AS Chave_Unica_Log,
    r.status_leitura,
    r.id_operador,
    o.id_obra,
    o.nome_obra,
    ct.nome as Nome_Contato_Cliente,
    ct.sobrenome as Sobrenome_Contato_Cliente,
    ct.cel_corporativo as celular_Contato_Cliente,
    ct.email_corporativo as email_Contato_Cliente,
    e.nome_estacao,
    e.id_estacao,
    tpl.nome_tipo_log AS Tipo_Log_Rotina,
    uop.email AS Email_Operador,
    usu.email AS Email_Supervisor,
    uro.email AS Email_RO,
    colabRO.cel_corporativo AS Tel_Ro,
    colabSU.cel_corporativo AS Tel_SU,
    colabOP.cel_corporativo AS Tel_OP,
    colabSU.nome AS Nome_Supervisor,
    colabRO.nome AS Nome_RO,
    colabOP.nome AS Nome_Operador,
    uop.id AS ID_OP,
    usu.id AS ID_SU,
    uro.id AS ID_RO,
    cs.gps_metros

FROM
    rmm r
        INNER JOIN
    pontos_estacao p ON p.id_ponto = r.id_ponto
        INNER JOIN
    obras o ON o.id_obra = p.id_obra
     INNER JOIN 
     clientes c ON c.id_cliente = o.id_cliente
        LEFT JOIN
        contatos ct ON ct.id_cliente = c.id_cliente 
        INNER JOIN
    estacoes e ON e.id_estacao = p.id_estacao
        INNER JOIN
    usuarios uop ON uop.id = r.id_operador
        INNER JOIN
    usuarios usu ON usu.bd_id = e.supervisor
        INNER JOIN
    usuarios uro ON uro.bd_id = e.ro
        INNER JOIN
    parametros_ponto pr ON pr.id_parametro = r.id_parametro
        LEFT JOIN
    unidade_medida u ON pr.unidade_medida = u.id_unidade_medida
        LEFT JOIN
    colaboradores colabOP ON colabOP.id_colaborador = uop.bd_id
        LEFT JOIN
    colaboradores colabSU ON colabSU.id_colaborador = e.supervisor
        LEFT JOIN
    colaboradores colabRO ON colabRO.id_colaborador = e.ro
        LEFT JOIN
    log_leitura logl ON logl.chave_unica = r.chave_unica
        LEFT JOIN
    tipo_log tpl ON tpl.id_tipo_log = logl.tipo_log
    INNER JOIN
    step_config cs ON cs.id_step = '1'

WHERE 
r.status_leitura = '5' AND

DATE_FORMAT(r.data_leitura, '%Y-%m-%d') > '$Data_Intervalo_Periodo'

GROUP BY r.id_rmm
ORDER BY r.data_Leitura ASC");

$total = $sql->rowCount();

/* status = 5. status automaticamente gerado a cada nova leitura, são aquelas que estão aguardando a validação da API para agora 
    verificar se cada leitura com status 5 (aguardando análise), está dentro ou fora do parâmetro informado (concen_min e concen_max)
    r.status_leitura = '5'  */ 

/*  print_r($sql);


 echo "".$total;

 exit; 
  */

 
if ($total > 0) { // inicia a validação das leituras encontradas

 $resultado = $sql->fetchALL(PDO::FETCH_ASSOC);
    
 $status_leitura = "0";
 $contador = 0;

 $status_leitura = "1";
 $nome_suporte = "Alerta STEP: \n\r";
 $id_tipo_suporte ='0';
 $assunto_email ='';

     foreach ($resultado as $res) {

        $STEP_Controla_GPS = trim(isset($res['gps_metros'])) ? trim($res['gps_metros']) : '';
        $STEP_Controla_GPS = isset($res['gps_metros']) ? doubleval(trim($res['gps_metros'])) : NULL;
       

        if(filter_var($STEP_Controla_GPS, FILTER_VALIDATE_FLOAT) === false) {
            // Lança uma exceção ou define um valor padrão para $controla_gps.
            throw new Exception("$STEP_Controla_GPS não é um valor decimal válido.");
            // ou
            $STEP_Controla_GPS = '0'; // Um valor decimal padrão.
        }


        $Nome_Contato_Cliente = trim(isset($res['Nome_Contato_Cliente'])) ? $res['Nome_Contato_Cliente'] : '';
        $Sobrenome_Contato_Cliente = trim(isset($res['Sobrenome_Contato_Cliente'])) ? $res['Sobrenome_Contato_Cliente'] : '';

        $email_Contato_Cliente = trim(isset($res['email_Contato_Cliente'])) ? $res['email_Contato_Cliente'] : '';
        $celular_Contato_Cliente = trim(isset($res['celular_Contato_Cliente'])) ? $res['celular_Contato_Cliente'] : '';

        $objetivo_ponto = trim(isset($res['objetivo_ponto'])) ? $res['objetivo_ponto'] : '';

        $ID_RMM = isset($res['id_rmm']) ? $res['id_rmm'] : '';

        $Chave_Unica_Rmm = trim(isset($res['Chave_Unica_Rmm'])) ? $res['Chave_Unica_Rmm'] : '';

        $unidade_medida_lida = trim(isset($res['nome_unidade_medida'])) ? $res['nome_unidade_medida'] : 'Ausente no Cadastro';

        $nome_plcode = $res['nome_ponto'];

        $id_ponto = $res['id_ponto'];

        $id_parametro = trim(isset($res['id_parametro'])) ? $res['id_parametro'] : '0';

        $nome_parametro = isset($res['nome_parametro']) ? $res['nome_parametro'] : '';

        $nome_obra = $res['nome_obra'];

        $id_obra = $res['id_obra'];

        $nome_estacao = $res['nome_estacao'];

        $id_estacao = $res['id_estacao'];

        $Latitude_Ponto = trim(isset($res['Latitude_Ponto'])) ? $res['Latitude_Ponto'] : '';

        $Longitude_Ponto = trim(isset($res['Longitude_Ponto'])) ? $res['Longitude_Ponto'] : '';

        $Latitude_Operador = trim(isset($res['Latitude_Operador'])) ? $res['Latitude_Operador'] : '';

        $Longitude_Operador = trim(isset($res['Longitude_Operador'])) ? $res['Longitude_Operador'] : '';

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

        //====[ Finaliza a Coleta dos Dados para serem analisados e distribuídos, agora na segunda faze de acordo com o 
        //resultado de cada análise, seu alerta, conforme categoria do Suporte] <<<<


        // Reserva as variaveis apra comparação e preenchimento do envio dos alertas, caso seja necessário
        // trata e desmembra a data da leitura //

        $data_leitura = $res['Data_Rmm'];
        //$data_leitura =  strtotime($phpdate) * 1000;
        $hora_min =  date('H:i', strtotime($data_leitura));
        $dia_mes_ano =  date('d/m/Y', strtotime($data_leitura));
        $data_consulta_suporte= date('Y-m-d H:i', strtotime($data_leitura));

        $horario_leitura = $dia_mes_ano . ' ' . $hora_min;
        //====<< 

        // trata e separa a leitura em colunia unica, inpependente se é entrada ou saida, para amostragem
        // Operadores Ternários para Parametros e seus derivados acontece, por haver leitura de RMM/Checkin apenas com o PLCode.
        $concen_min = isset($res['concen_min']) ? doubleval(trim($res['concen_min'])) : NULL;
        $concen_max = isset($res['concen_max']) ? doubleval(trim($res['concen_max'])) : NULL;
        $leitura = doubleval(trim($res['leitura_entrada']));
        $controle_concentracao = trim($res['controle_concentracao']);


       



       
    
        if ($controle_concentracao == "1") {

            $nome_controle_concentracao = "Mínima";
    
            if ($leitura < $concen_min && $concen_min != NULL) {

                $status_leitura = "3";
                $assunto_email = "Leitura Inferior ao Mínimo Permitido";
                // Montando a mensagem
                $nome_suporte = "
                <div style='font-family: Arial, sans-serif; padding:  5px 5px 5px 5px; margin-left: 5px; margin-top: 5px; border-radius: 10px; background-color: #f2f2f2; color: #737373; line-height: 1.5; font-size: 11px;'>
                <h5 class='fs-4 text-bold'> <span class='badge badge-danger'>Aviso</span> <span class='text-danger fs-5'> {$nome_parametro} </span>,<span class='badge badge-danger'>com a leitura inferior ao Mínimo Permitido</span></h5>
                <p>Leitura Informada: <span class='badge badge-danger'><strong>{$leitura} {$unidade_medida_lida}</strong></span> </p>
                <p>Mínimo Permitido: <span class='badge badge-primary'><strong>{$concen_min} {$unidade_medida_lida}</strong></span></p>
                   
                </div>
                ";
                $id_tipo_suporte = '96';
                $id_usuario = $ID_OP;
                
            } else {
                $status_leitura = "1";

            }
            
        } elseif ($controle_concentracao == "2") {

            $nome_controle_concentracao = "Máxima";
    
          if ($leitura > $concen_max && $concen_max != NULL)  {

                $status_leitura = "3";

                $assunto_email = "Leitura Superior ao Máximo Permitido"; $nome_suporte .= "Leitura Superior ao Máximo Permitido, pela regra que controla só Máximo" ;  
                $nome_suporte .= "
                <div style='font-family: Arial, sans-serif; padding:  5px 5px 5px 5px; margin-left: 5px; margin-top: 5px; border-radius: 10px; background-color: #f2f2f2; color: #737373; line-height: 1.5; font-size: 11px;'>
                <h5 class='fs-4 text-bold'> <span class='badge badge-danger'>Aviso</span> <span class='text-danger fs-5'> {$nome_parametro} </span>,<span class='badge badge-danger'>com a leitura superior ao Máximo Permitido</span></h5>
                <p>Leitura Informada: <span class='badge badge-danger'><strong>{$leitura} {$unidade_medida_lida}</strong></span> </p>
                    <p>Máximo Permitido: <span class='badge badge-primary'><strong>{$concen_max} {$unidade_medida_lida}</strong></span></p>
                   
                </div>
                ";

                $id_tipo_suporte = '97';
           
                $id_usuario = $ID_OP;
                
            } else {

                $status_leitura = "1";
            

                              
                }

             } elseif ($controle_concentracao == "3") {

                $nome_controle_concentracao = "Mínima e Máxima";

                if ($leitura >= $concen_min && $leitura <= $concen_max && $concen_min != NULL && $concen_max != NULL) {

                    $status_leitura = "1";

                    
                }else if($leitura < $concen_min && $leitura <= $concen_max){

                    $status_leitura = "3";

                    $assunto_email = "Aviso: Leitura Abaixo do Mínimo Permitido";
                   
                    $nome_suporte = "
                    <div style='font-family: Arial, sans-serif; padding:  5px 5px 5px 5px; margin-left: 5px; margin-top: 5px; border-radius: 10px; background-color: #f2f2f2; color: #737373; line-height: 1.5; font-size: 11px;'>
                    <h5 class='fs-4 text-bold'> <span class='badge badge-danger'>Aviso</span> <span class='text-danger fs-5'> {$nome_parametro} </span>,<span class='badge badge-danger'>com a leitura inferior ao Mínimo Permitido</span></h5>
                    <p>Leitura Informada: <span class='badge badge-danger'><strong>{$leitura} {$unidade_medida_lida}</strong></span> </p>
                    <p>Mínimo Permitido: <span class='badge badge-primary'><strong>{$concen_min} {$unidade_medida_lida}</strong></span></p>
                       
                    </div>
                    ";

                    $id_usuario = $ID_OP;
                    $id_tipo_suporte = '98';

                                      
                }else if($leitura >= $concen_min && $leitura > $concen_max){

                    $status_leitura = "3";

                    $assunto_email = "Aviso: Leitura Superior ao Máximo Permitido";
                   
                    $nome_suporte = "
                    <div style='font-family: Arial, sans-serif; padding:  5px 5px 5px 5px; margin-left: 5px; margin-top: 5px; border-radius: 10px; background-color: #f2f2f2; color: #737373; line-height: 1.5; font-size: 11px;'>
                    <h5 class='fs-4 text-bold'> <span class='badge badge-danger'>Aviso</span> <span class='text-danger fs-5'> {$nome_parametro} </span>,<span class='badge badge-danger'>com a leitura superior ao Máximo Permitido</span></h5>
                        <p>Leitura Informada: <span class='badge badge-danger'><strong>{$leitura} {$unidade_medida_lida}</strong></span> </p>
                        <p>Máximo Permitido: <span class='badge badge-primary'><strong>{$concen_max} {$unidade_medida_lida}</strong></span> </p>
                       
                    </div>
                    ";
                    $id_tipo_suporte = '112';
                    $id_usuario = $ID_OP;
                    

                } 
            }
        
       
        // Inicia os Alertas para leitura fora 
        if ($status_leitura == "3") {

            $status_suporte = '1';
            $leitura= $leitura;
            $id_tipo_suporte = $id_tipo_suporte;

            cria_suporte($leitura, $id_tipo_suporte, $nome_suporte, $ID_RMM, $id_ponto, $id_estacao, $id_obra, $id_parametro, $ID_SU , $ID_OP, $chave_unica, $data_leitura, $status_suporte, $conexao);
               
               
               
         }

// Verifica se o GPS está ativo e não é NULL
if (isset($STEP_Controla_GPS) && $STEP_Controla_GPS != 0) { 

    $assunto_email = " - Leitura Fora da Geo-Localização permitida de até $STEP_Controla_GPS metros. ";
    $id_tipo_suporte = '99';
    $distancia_arredondada = 'Dados de Geolocalização não disponíveis';
    $Endereco_Origem = 'Dados de Geolocalização não disponíveis';
    $nome_suporte = "Alerta GPS\n\r";
    

    // Verifica se as coordenadas estão disponíveis
    if (isset($Latitude_Ponto, $Longitude_Ponto, $Latitude_Operador, $Longitude_Operador)) {

        // Pega o endereço a partir das coordenadas usando a API do Google Maps
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$Latitude_Operador,$Longitude_Operador&key=AIzaSyBSIkQdqUjEKXybmAFvFEKCLxkNlFA_aJ8";
        $dadosSite = my_file_get_contents($url);

        if ($dadosSite !== false) {
            $data = json_decode($dadosSite, true);

            $Endereco_Origem = isset($data['results'][1]['formatted_address']) 
                ? 'Local da Leitura: ' . $data['results'][1]['formatted_address']
                : 'Endereço não encontrado';
        } else {
            $Endereco_Origem = 'Erro ao acessar a API do Google Maps';
        }

        // Calcula a distância entre as coordenadas
        $distancia_calculada = calcularDistancia($Latitude_Ponto, $Longitude_Ponto, $Latitude_Operador, $Longitude_Operador);

        // Arredonda a distância para duas casas decimais
        $distancia_arredondada = round($distancia_calculada, 2);
    }

    if (isset($distancia_arredondada) && $distancia_arredondada > $STEP_Controla_GPS) {
        // $assunto_notificacao .= "Leitura Fora da Geo-Localização permitida";
        $nome_suporte .= "Leitura Fora da Geo-Localização permitida de até $STEP_Controla_GPS metros - Distância Detectada: $distancia_arredondada metros.<br><br> "; 
        $id_tipo_suporte = '99';

        $Status_Gps = '2'; //Leitura fora de área
        $id_usuario = $ID_OP;

        $nome_suporte .= "<div style='font-family: Arial, sans-serif; padding:  5px 5px 5px 5px; margin-left: 5px; margin-top: 5px; border-radius: 10px; background-color: #f2f2f2; color: #737373; line-height: 1.5; font-size: 11px;'>
        <p>Origem Coletada pela API do Google Maps:</p>
        <p><strong>$Endereco_Origem</strong></p></div>";

        $nome_suporte .= "<div style='font-family: Arial, sans-serif; padding:  5px 5px 5px 5px; margin-left: 5px; margin-top: 5px; border-radius: 10px; background-color: #e5e5e5; color: #737373; line-height: 1.5; font-size: 11px;'>
        <p><span class='badge-light-danger badge-sm'>Coordenadas Comparativas:</span></p>
        <p><strong>$Latitude_Operador</strong></p>
        <p><strong>$Longitude_Operador</strong></p>
        <p>GPS do PLCode Cadastrado:</p>
        <p><strong>$Latitude_Ponto</strong></p>
        <p><strong>$Longitude_Ponto</strong></p></div>";

        $status_suporte = '1';

        cria_suporte($leitura, $id_tipo_suporte, $nome_suporte, $ID_RMM, $id_ponto, $id_estacao, $id_obra, $id_parametro, $ID_SU , $ID_OP, $chave_unica, $data_leitura, $status_suporte, $conexao);

        $contador++;
        error_log('API STEP GPS: Houveram: '.$contador.' de GPS fora do limite, para esta consulta no dia '.$Data_Atual_Periodo.'.');
               
    } 
}


         update_status_rmm($conexao, $status_leitura, $ID_RMM);

        
} // fecha o laço principal da consulta de leituras

if($status_leitura == "3"){
$contador++;

error_log('API STEP LEITURAS: Houveram: '.$contador.' de leituras Analisadas, fora dos parametros e com suporte criado, para esta consulta no dia '.$Data_Atual_Periodo.'.');
}
// Fecha Log <<<<<<<

// Fecha a conexão com o banco de dados
$conexao = null;      
}