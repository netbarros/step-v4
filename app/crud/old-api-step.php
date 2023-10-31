<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header("Access-Control-Max-Age: 3600");
ini_set('memory_limit', '-1');

include_once $_SERVER['DOCUMENT_ROOT'].'/conexao.php';
$conexao = Conexao::getInstance();

if (!ini_get('date.timezone')) {
    date_default_timezone_set('America/Sao_Paulo');
}

// Atribui uma conexão PDO

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


//===[ CHAVE ÚNICA da SESSAO] a cada acesso, o step registra uma codificação única de 32bits, 
$horario_completo_agora = microtime();
/* INÍCIO: Crio a Chave unica da Sessao para armazenamento e resgate das leituras e imagens que serão enviadas */
$chave_unica = bin2hex(random_bytes(33) . $horario_completo_agora);
/* Gerar strings aleatórias criptograficamente seguras, usamos 24 caracteres não repetitivos e aleatórios com criptografia nativa do PHP";*/
 

$Chave_Unica_CRON = $chave_unica;

// Define o Período da Busca dos Dados
$Data_Atual_Periodo = date_create()->format('Y-m-d');
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-1  days', strtotime($Data_Atual_Periodo)));
$dia_tarefa_cron = date('d');

//1ª FASE >> ===[ Valida as Leituras de RMM se estão OK ou fora de parâmetro ]===========>>
#VERITICA INTEGRIDADE DOS DADOS
$controle_bd = $conexao->query("DELETE FROM rmm WHERE id_parametro='0' OR leitura_entrada='10101.00'");

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
    $distancia = $earthRadius * $c * 1000;

    return $distancia;
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
    uro.id AS ID_RO

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
        INNER JOIN
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

WHERE r.status_leitura = '5' AND
  
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

    $retorno_alerta="";
    
// função para fisparar os alertas //

    function enviarAlerta($id_estacao, $conexao, $ID_RMM, $assunto_email,$nome_suporte, $id_tipo_suporte, $retorno_alerta, $chave_unica) {
        // Seu código de envio de alerta aqui

        
        //**** modulo de gestao de alertas >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 

        $sql_projeto = $conexao->query("SELECT e.nome_estacao, o.nome_obra, o.id_obra FROM obras o
        INNER JOIN 
        estacoes e 
        ON e.id_obra = o.id_obra
        INNER JOIN 
        suporte s 
        ON s.estacao = e.id_estacao
        WHERE e.id_estacao ='$id_estacao'");
        $res_projeto = $sql_projeto->fetch(PDO::FETCH_ASSOC);
        $nome_projeto = $res_projeto['nome_obra'];
        $nome_estacao = $res_projeto['nome_estacao'];
        $id_Projeto_Suporte = $res_projeto['id_obra'];



        // Invoca Gestão de Alertas, para conferir os usuários envolvidos no Projeto e suas regras de alerta personalizadas.
            $id_obra = $id_Projeto_Suporte;
            $chave_unica = $chave_unica;
            $categoria_suporte=$id_tipo_suporte; // não há categoria de suporte neste caso, por ser uma tarefa concluída
            $nome_suporte = $nome_suporte;
            $mensagem_alerta =$retorno_alerta;
            $mensagem_SMS = "Aviso STEP:\n\r<br>Novo Ticket de Suporte: $nome_suporte,\n\r<br>O Projeto: $nome_projeto, Núcleo $nome_estacao, favor verificar.";
            $assunto_email = $assunto_email;
            $ID_RMM = $ID_RMM;
       
            //require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/suporte/gestao-alertas.php';
        //**** modulo de gestao de alertas <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< */ 


    } // fim funcao disparar alertas

    

/****** Função para Criar o Ticket de Suporte ***********/
function insere_suporte($conexao, $ID_SU, $id_tipo_suporte, $nome_suporte, $ID_RMM, $leitura, $id_estacao, $retorno_alerta, $id_ponto, $id_parametro, $ID_OP, $chave_unica) {

    $status_suporte_inicial = '1'; // status inicial do ticket de suporte, 1 = aberto
    $sql = "INSERT INTO suporte(
        tipo_suporte,
        motivo_suporte,
        id_rmm_suporte,
        leitura_suporte,        
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
    $stmt->bindParam(':plcode', $id_ponto);
    $stmt->bindParam(':parametro', $id_parametro);
    $stmt->bindParam(':quem_abriu', $ID_OP);
    $stmt->bindParam(':chave_unica', $chave_unica);
    $stmt->bindParam(':status_suporte', $status_suporte_inicial);
  
    $result = $stmt->execute();
  
    $ultimo_id_suporte = $conexao->lastInsertId();
  
    
    // Insere conversa
    if ($result) {

      insere_conversa($conexao, $ultimo_id_suporte, $nome_suporte, $ID_OP, $ID_SU, $retorno_alerta, $chave_unica,$id_estacao);
    }
  }
  
  

// função para criar o ticket de suporte



 // funcao para criar a 1 covnersa do suporte ticket gerado

 function insere_conversa($conexao, $ultimo_id_suporte,$nome_suporte, $ID_OP, $ID_SU, $retorno_alerta,$chave_unica,$id_estacao) {
    $sql = "INSERT INTO suporte_conversas (id_suporte,id_remetente,destinatario_direto,conversa) VALUES (:id_suporte, :id_remetente, :destinatario_direto, :conversa)";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id_suporte', $ultimo_id_suporte);
    $stmt->bindParam(':id_remetente', $ID_OP);
    $stmt->bindParam(':destinatario_direto', $ID_SU);
    $stmt->bindParam(':conversa', $retorno_alerta);
  
    $result = $stmt->execute();
    $ultimo_id_conversa = $conexao->lastInsertId();
    $total_conversa_suporte = $stmt->rowCount();
  
    if ($result) {
      $acao_log = "Chat Direct";
      $tipo_log = '48';
      $sql_log = "INSERT INTO log_leitura (chave_unica,id_usuario,acao_log,id_acao_log,estacao_logada,acao,tipo_log) VALUES (?,?,?,?,?,?,?)";
      $acao_log = "Conversa iniciada automaticamente, através da criação do Ticket $nome_suporte | ID: $ultimo_id_suporte";
      $conexao->prepare($sql_log)->execute([$chave_unica, $ID_SU, $acao_log, $ultimo_id_suporte, $id_estacao, $acao_log, $tipo_log]);
    }
  }
  

// funcao para criar a 1 conversa do ticket gerado

   


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
   

/*     function update_leitura($conexao, $ID_RMM) {
        // Atualize a tabela 'leitura' com base no valor de $ID_RMM
        // Implemente a lógica de atualização aqui
        return  $conexao->query("UPDATE rmm SET status_leitura='3' WHERE id_rmm='$ID_RMM' "); // status = 3 (Leitura Fora)
   
    } */
    

    function update_status_rmm($conexao, $status_leitura, $ID_RMM)
    {
        return $conexao->query("UPDATE rmm SET status_leitura='{$status_leitura}' WHERE id_rmm='{$ID_RMM}' ");
    }



    
    function delete_suporte($conexao, $ID_RMM)
    {
        return $conexao->query("DELETE FROM suporte WHERE id_rmm_suporte='{$ID_RMM}'");
    }
    

   
    
    foreach ($resultado as $res) {

        $Nome_Contato_Cliente = trim(isset($res['Nome_Contato_Cliente'])) ? $res['Nome_Contato_Cliente'] : '';
        $Sobrenome_Contato_Cliente = trim(isset($res['Sobrenome_Contato_Cliente'])) ? $res['Sobrenome_Contato_Cliente'] : '';

        $email_Contato_Cliente = trim(isset($res['email_Contato_Cliente'])) ? $res['email_Contato_Cliente'] : '';
        $celular_Contato_Cliente = trim(isset($res['celular_Contato_Cliente'])) ? $res['celular_Contato_Cliente'] : '';

        $objetivo_ponto = trim(isset($res['objetivo_ponto'])) ? $res['objetivo_ponto'] : '';

        $ID_RMM = isset($res['id_rmm']) ? $res['id_rmm'] : '';

        $Chave_Unica_Rmm = trim(isset($res['Chave_Unica_Rmm'])) ? $res['Chave_Unica_Rmm'] : '';

        $unidade_medida_lida = trim(isset($res['nome_unidade_medida'])) ? $res['nome_unidade_medida'] : '';

        $nome_plcode = $res['nome_ponto'];

        $id_ponto = $res['id_ponto'];

        $id_operador = $res['id_operador'];

        $id_parametro = trim(isset($res['id_parametro'])) ? $res['id_parametro'] : '0';

        $nome_parametro = isset($res['nome_parametro']) ? $res['nome_parametro'] : '';

        $nome_obra = $res['nome_obra'];

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
        //====<< 

        // trata e separa a leitura em colunia unica, inpependente se é entrada ou saida, para amostragem
        // Operadores Ternários para Parametros e seus derivados acontece, por haver leitura de RMM/Checkin apenas com o PLCode.
        $concen_min = isset($res['concen_min']) ? doubleval(trim($res['concen_min'])) : NULL;
        $concen_max = isset($res['concen_max']) ? doubleval(trim($res['concen_max'])) : NULL;
        $leitura = doubleval(trim($res['leitura_entrada']));
        $controle_concentracao = $res['controle_concentracao'];


        $controle_concentracao = $res['controle_concentracao'];
        $status_leitura = "";
        $retorno_alerta = "";
        $total_suporte='0';
        $total_ok = '0';
        $total_gps_fora = '0';
        $total_email = '0';
        $total_normalizado = '0';
        $assunto_email = '';
        $nome_suporte = '0';
        $id_tipo_suporte ='0';
    
        if ($controle_concentracao == "1") {
            $nome_controle_concentracao = "Mínima";
    
            if ($leitura < $concen_min) {

                $status_leitura = "3";

                $assunto_email .= "Leitura acima do Permitida";
                $nome_suporte = "Leitura abaixo do permitido" ;  
                $id_tipo_suporte = '96';
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Projeto: </b> $nome_obra</p>";
                $retorno_alerta .= "</div>";
                
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Estação:</b>  $nome_estacao</p>";
                $retorno_alerta .= "</div>";
                
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>PLCode:</b>  $nome_plcode</p>";
                $retorno_alerta .= "</div>";
                
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Operador:</b> $nome_Operador</p>";
                $retorno_alerta .= "</div>";
             
                $retorno_alerta .= "<p><b>Indicador:</b>$nome_parametro,<b>inferior ao Mínimo Ideal</b></p> ";

                $retorno_alerta .= "<div style='background-color: #f2f2f2; border-radius: 10px; padding: 20px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Leitura Informada:</b> $leitura $unidade_medida_lida</p>";
                $retorno_alerta .= "</div>";

                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px;' margin-bottom: 15px;>";
                $retorno_alerta .= "<p style='background-color: #333; color: #fff; padding: 10px; border-radius: 10px 10px 0 0;'><b>Controla Parâmetros de Concentrações Mínima em $unidade_medida_lida</b></p>";
                $retorno_alerta .= "<p><b>Mínimo Permitido:</b> $concen_min $unidade_medida_lida</p>";
                $retorno_alerta .= "<p><b>Máximo Permitido:</b> $concen_max $unidade_medida_lida</p>";
                $retorno_alerta .= "</div>";

                

                
            } else {
                $status_leitura = "1";

                $assunto_email .= "Leitura dentro da Mínima Permitida";
                $nome_suporte = "Leitura dentro do permitido" ;  
                $id_tipo_suporte = '0';
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Projeto: </b> $nome_obra</p>";
                $retorno_alerta .= "</div>";
                
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Estação:</b>  $nome_estacao</p>";
                $retorno_alerta .= "</div>";
                
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>PLCode:</b>  $nome_plcode</p>";
                $retorno_alerta .= "</div>";
                
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Operador:</b> $nome_Operador</p>";
                $retorno_alerta .= "</div>";
                
                $retorno_alerta .= "<div style='background-color: #f2f2f2; border-radius: 10px; padding: 20px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Leitura Informada:</b> $leitura $unidade_medida_lida</p>";
                $retorno_alerta .= "</div>";

                $retorno_alerta .= "<p><b>Indicador: </b>$nome_parametro,<b>dentro dos limites Ideais</b></p>";
                $retorno_alerta .= "<div style='background-color: #f2f2f2; border-radius: 10px; padding: 20px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Mínimo Permitido:</b> $concen_min $unidade_medida_lida</p>";
                $retorno_alerta .= "<p><b>Máximo Permitido:</b> $concen_max $unidade_medida_lida</p>";
                $retorno_alerta .= "</div>";
                
            }
        } elseif ($controle_concentracao == "2") {

            $nome_controle_concentracao = "Máxima";
    
            if ($leitura > $concen_max) {

                $status_leitura = "3";

                $assunto_email .= "Leitura superior ao Permitido";
                $nome_suporte = "Leitura acima do permitido" ;  
                $id_tipo_suporte = '97';
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Projeto: </b> $nome_obra</p>";
                $retorno_alerta .= "</div>";
                
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Estação:</b>  $nome_estacao</p>";
                $retorno_alerta .= "</div>";
                
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>PLCode:</b>  $nome_plcode</p>";
                $retorno_alerta .= "</div>";
                
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Operador:</b> $nome_Operador</p>";
                $retorno_alerta .= "</div>";

                $retorno_alerta .= "<p><b>Indicador:</b> $nome_parametro,<b>superior ao limite Ideal</b></p>";

                $retorno_alerta .= "<div style='background-color: #f2f2f2; border-radius: 10px; padding: 20px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Leitura Informada:</b> $leitura $unidade_medida_lida</p>";
                $retorno_alerta .= "</div>";
                

                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px;' margin-bottom: 15px;>";
                $retorno_alerta .= "<p style='background-color: #333; color: #fff; padding: 10px; border-radius: 10px 10px 0 0;'><b>Controla Parâmetros de Concentrações Máxima em $unidade_medida_lida</b></p>";
                $retorno_alerta .= "<p><b>Mínimo Permitido:</b> $concen_min $unidade_medida_lida</p>";
                $retorno_alerta .= "<p><b>Máximo Permitido:</b> $concen_max $unidade_medida_lida</p>";
                $retorno_alerta .= "</div>";
            
                
            } else {

                $status_leitura = "1";

                $assunto_email .= "Leitura dentro ao limite permitido";
                $nome_suporte = "Leitura dentro do permitido" ;  
                $id_tipo_suporte = '0';
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Projeto: </b> $nome_obra</p>";
                $retorno_alerta .= "</div>";
                
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Estação:</b>  $nome_estacao</p>";
                $retorno_alerta .= "</div>";
                
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>PLCode:</b>  $nome_plcode</p>";
                $retorno_alerta .= "</div>";
                
                $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Operador:</b> $nome_Operador</p>";
                $retorno_alerta .= "</div>";
                $retorno_alerta .= "<p><b>Indicador: </b>$nome_parametro,<b>dentro dos limites Ideais</b></p>";

                $retorno_alerta .= "<div style='background-color: #f2f2f2; border-radius: 10px; padding: 20px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Leitura Informada:</b> $leitura $unidade_medida_lida</p>";
                $retorno_alerta .= "</div>";

                $retorno_alerta .= "<div style='background-color: #f2f2f2; border-radius: 10px; padding: 20px; margin-bottom: 15px;'>";
                $retorno_alerta .= "<p><b>Mínimo Permitido:</b> $concen_min $unidade_medida_lida</p>";
                $retorno_alerta .= "<p><b>Máximo Permitido:</b> $concen_max $unidade_medida_lida</p>";
                $retorno_alerta .= "</div>";

                
                }
             } elseif ($controle_concentracao == "3") {

                $nome_controle_concentracao = "Mínima e Máxima";
                if ($leitura >= $concen_min && $leitura <= $concen_max) {

                    $status_leitura = "1";

                    $assunto_email .= "Leitura dentro do limite permitido";
                    $nome_suporte = "Leitura dentro do permitido" ;  
                    $id_tipo_suporte = '0';
                    $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                    $retorno_alerta .= "<p><b>Projeto: </b> $nome_obra</p>";
                    $retorno_alerta .= "</div>";
                    
                    $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                    $retorno_alerta .= "<p><b>Estação:</b>  $nome_estacao</p>";
                    $retorno_alerta .= "</div>";
                    
                    $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                    $retorno_alerta .= "<p><b>PLCode:</b>  $nome_plcode</p>";
                    $retorno_alerta .= "</div>";
                    
                    $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                    $retorno_alerta .= "<p><b>Operador:</b> $nome_Operador</p>";
                    $retorno_alerta .= "</div>";
                    $retorno_alerta .= "<p><b>Indicador: </b>$nome_parametro,<b>dentro dos limites Permitidos</b></p>";
                    
                    $retorno_alerta .= "<div style='background-color: #f2f2f2; border-radius: 10px; padding: 20px; margin-bottom: 15px;'>";
                    $retorno_alerta .= "<p><b>Leitura Informada:</b> $leitura $unidade_medida_lida</p>";
                    $retorno_alerta .= "</div>";

                    $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px;' margin-bottom: 15px;>";
                    $retorno_alerta .= "<p style='background-color: #333; color: #fff; padding: 10px; border-radius: 10px 10px 0 0;'><b>Controla Parâmetros de Concentrações Mínima e Máxima em $unidade_medida_lida</b></p>";
                    $retorno_alerta .= "<p><b>Mínimo Permitido:</b> $concen_min $unidade_medida_lida</p>";
                    $retorno_alerta .= "<p><b>Máximo Permitido:</b> $concen_max $unidade_medida_lida</p>";
                    $retorno_alerta .= "</div>";

                    
                } else {

                    $status_leitura = "3";

                    $assunto_email .= "Leitura Fora dos limites Permitidos";
                    $nome_suporte = "Leitura fora dos parâmetros mínimo e máximo, permitido" ;  
                    $id_tipo_suporte = '98';

                    $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                    $retorno_alerta .= "<p><b>Projeto: </b> $nome_obra</p>";
                    $retorno_alerta .= "</div>";
                    
                    $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                    $retorno_alerta .= "<p><b>Estação:</b>  $nome_estacao</p>";
                    $retorno_alerta .= "</div>";
                    
                    $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                    $retorno_alerta .= "<p><b>PLCode:</b>  $nome_plcode</p>";
                    $retorno_alerta .= "</div>";
                    
                    $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                    $retorno_alerta .= "<p><b>Operador:</b> $nome_Operador</p>";
                    $retorno_alerta .= "</div>";
                    
                    $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px; margin-bottom: 15px;'>";
                    $retorno_alerta .= "<p><b>Indicador:</b> $nome_parametro - <b>Fora dos limites Permitidos</b></p>";
                    $retorno_alerta .= "</div>";
                    
                    $retorno_alerta .= "<div style='background-color: #f2f2f2; border-radius: 10px; padding: 20px; margin-bottom: 15px;'>";
                    $retorno_alerta .= "<p><b>Leitura Informada:</b> $leitura $unidade_medida_lida</p>";
                    $retorno_alerta .= "</div>";

                    $retorno_alerta .= "<div style='border-radius: 10px; background-color: #eee; padding: 10px;' margin-bottom: 15px;>";
                    $retorno_alerta .= "<p style='background-color: #333; color: #fff; padding: 10px; border-radius: 10px 10px 0 0;'><b>Controla Parâmetros de Concentrações Mínima e Máxima em $unidade_medida_lida</b></p>";
                    $retorno_alerta .= "<p><b>Mínimo Permitido:</b> $concen_min $unidade_medida_lida</p>";
                    $retorno_alerta .= "<p><b>Máximo Permitido:</b> $concen_max $unidade_medida_lida</p>";
                    $retorno_alerta .= "</div>";

                   
                }
            }
        

        // Inicia os Alertas para leitura fora 
        if ($status_leitura == "3") {

            enviarAlerta($id_estacao, $conexao, $ID_RMM, $assunto_email, $nome_suporte, $id_tipo_suporte, $retorno_alerta, $chave_unica);
            
           
            insere_suporte($conexao, $ID_SU, $id_tipo_suporte, $nome_suporte, $ID_RMM, $leitura, $id_estacao, $retorno_alerta, $id_ponto, $id_parametro, $ID_OP, $chave_unica);

                  }

                  update_status_rmm($conexao, $status_leitura, $ID_RMM);


      


} // fecha o laço principal da consulta de leituras

// Fecha a conexão com o banco de dados
$conexao = null;
}