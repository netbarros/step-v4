<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header("Access-Control-Max-Age: 3600");
ini_set('memory_limit', '-1');

// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'].'/conexao.php';
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
$Data_Atual_Periodo = date_create()->format('Y-m-d');
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-24 hours', strtotime($Data_Atual_Periodo)));
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
function insere_ou_atualiza_suporte($conexao, $id_tipo_suporte, $nome_suporte, $ID_RMM, $leitura, $id_obra, $id_estacao, $id_ponto, $id_parametro,$ID_SU, $ID_OP, $chave_unica,$data_consulta_suporte) {

    // Verifica se o suporte já existe pela chave única, assumindo que ela seja única para cada suporte
    $sql = "SELECT * FROM suporte WHERE data_open = :data_open AND id_rmm_suporte=:id_rmm_suporte";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':data_open', $data_consulta_suporte);
    $stmt->bindParam(':id_rmm_suporte', $ID_RMM);
    $stmt->execute();

   print_r($stmt->rowCount());



    $status_suporte_inicial = '1'; // status inicial do ticket de suporte, 1 = aberto
    if ($stmt->rowCount() > 0) { // Se existir, atualiza
        $sql = "UPDATE suporte SET 
                    tipo_suporte = :tipo_suporte,
                    motivo_suporte = :motivo_suporte,
                    id_rmm_suporte = :id_rmm_suporte,
                    leitura_suporte = :leitura_suporte,
                    obra = :obra,        
                    estacao = :estacao,
                    plcode = :plcode,
                    parametro = :parametro,
                    quem_abriu = :quem_abriu,
                    status_suporte = :status_suporte
                WHERE id_rmm_suporte = :id_rmm_suporte";
    } else { // Se não existir, insere
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
    }

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':tipo_suporte', $id_tipo_suporte);
    $stmt->bindParam(':motivo_suporte', $nome_suporte);
    $stmt->bindParam(':id_rmm_suporte', $ID_RMM);
    $stmt->bindParam(':leitura_suporte', $leitura);
    $stmt->bindParam(':obra', $id_obra);
    $stmt->bindParam(':estacao', $id_estacao);
    $stmt->bindParam(':plcode', $id_ponto);
    $stmt->bindParam(':parametro', $id_parametro);
    $stmt->bindParam(':quem_abriu', $ID_OP);
    $stmt->bindParam(':chave_unica', $chave_unica);
    $stmt->bindParam(':status_suporte', $status_suporte_inicial);

    $result = $stmt->execute();

    // Se for um novo suporte, insere a conversa
    if ($result && $stmt->rowCount() === 1) {
        insere_conversa($conexao, $id_tipo_suporte, $nome_suporte, $ID_OP, $ID_SU, $chave_unica,$id_estacao);
    }
}

  
  

// função para criar o ticket de suporte

 // funcao para criar a 1 covnersa do suporte ticket gerado

 function insere_conversa($conexao, $ultimo_id_suporte,$nome_suporte, $ID_OP, $ID_SU, $chave_unica,$id_estacao) {

    
    $resumo_suporte = limita_caracteres($nome_suporte, 600);
    $sql = "INSERT INTO suporte_conversas (id_suporte,id_remetente,destinatario_direto,conversa) VALUES (:id_suporte, :id_remetente, :destinatario_direto, :conversa)";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id_suporte', $ultimo_id_suporte);
    $stmt->bindParam(':id_remetente', $ID_OP);
    $stmt->bindParam(':destinatario_direto', $ID_SU);
    $stmt->bindParam(':conversa', $resumo_suporte);
  
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


        $status_leitura = "1";
        $nome_suporte = "Alerta STEP\n\r";
        $id_tipo_suporte ='0';
        $assunto_email ='';



       
    
        if ($controle_concentracao == "1") {

            $nome_controle_concentracao = "Mínima";
    
            if ($leitura < $concen_min && $concen_min != NULL) {

                $status_leitura = "3";
                $assunto_email .= "Leitura abaixo do Permitido, pela regra que controla só Mínimo";
                 $nome_suporte .= "Leitura abaixo do Permitido, pela regra que controla só Mínimo" ;   
                $id_tipo_suporte = '96';

                
            } else {
                $status_leitura = "1";

                $assunto_email .= "Leitura dentro da Mínima Permitida";
                $nome_suporte .= "Leitura dentro da Mínima Permitida" ;  
                $id_tipo_suporte = '0';
              
                
            }
        } elseif ($controle_concentracao == "2") {

            $nome_controle_concentracao = "Máxima";
    
          if ($leitura > $concen_max && $concen_max != NULL) {

                $status_leitura = "3";

                $assunto_email .= "Leitura Superior ao Máximo Permitido";
                $nome_suporte .= "Leitura Superior ao Máximo Permitido, pela regra que controla só Máximo" ;  
                $id_tipo_suporte = '97';
           
            
                
            } else {

                $status_leitura = "1";
                $nome_suporte .= "Leitura dentro do permitido" ;  
                $id_tipo_suporte = '0';

                              
                }

             } elseif ($controle_concentracao == "3") {

                $nome_controle_concentracao = "Mínima e Máxima";

                if ($leitura >= $concen_min && $leitura <= $concen_max && $concen_min != NULL && $concen_max != NULL) {

                    $status_leitura = "1";

                    $assunto_email .= "Leitura dentro do limite permitido";
                    $nome_suporte .= "Leitura dentro do permitido" ;  
                    $id_tipo_suporte = '0';
                   

                    
                }else if($leitura < $concen_min && $leitura <= $concen_max){

                    $status_leitura = "3";

                    $assunto_email .= "Leitura Fora dos limites permitidos";
                    $nome_suporte .= "Leitura abaixo do permitido, pela regra que controla Mínimo e Máximo" ;  
                    $id_tipo_suporte = '98';

                                      
                }else if($leitura >= $concen_min && $leitura > $concen_max){

                    $status_leitura = "3";

                    $assunto_email .= "Leitura acima do Permitido.";
                    $nome_suporte .= "Leitura acima do Permitido, pela regra que controla Mínimo e Máximo" ;  
                    $id_tipo_suporte = '112';

                } 
            }
        

        // Inicia os Alertas para leitura fora 
        if ($status_leitura == "3") {

                
                insere_ou_atualiza_suporte($conexao, $id_tipo_suporte, $nome_suporte, $ID_RMM, $leitura, $id_obra, $id_estacao, $id_ponto, $id_parametro,$ID_SU, $ID_OP, $chave_unica,$data_leitura);
                $contador++;
         }

// Verifica se o GPS está ativo e não é NULL
if (isset($STEP_Controla_GPS) && $STEP_Controla_GPS != 0) { 

    $assunto_email .= " - Leitura Fora da Geo-Localização permitida de até $STEP_Controla_GPS metros - ";
    $id_tipo_suporte = '99';
    $distancia_arredondada = 'Dados de Geolocalização não disponíveis';
    $Endereco_Origem = 'Dados de Geolocalização não disponíveis';

    $nome_suporte .= "Alerta GPS\n\r";

    $nome_suporte .= " \n\r";

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
        $nome_suporte .= " - Leitura Fora da Geo-Localização permitida de até $STEP_Controla_GPS metros - Distância Detectada: $distancia_arredondada metros "; 
        $id_tipo_suporte = '99';

        $Status_Gps = '2'; //Leitura fora de área
        $nome_suporte .= " \n\r";
        $nome_suporte .= "<tr>
        <td style='border: 1px solid #ddd; padding: 10px;'>$Endereco_Origem</td>
        </tr>";


        insere_ou_atualiza_suporte($conexao, $id_tipo_suporte, $nome_suporte, $ID_RMM, $leitura, $id_obra, $id_estacao, $id_ponto, $id_parametro,$ID_SU, $ID_OP, $chave_unica,$data_consulta_suporte);

        // vou transformaar esse bloco numa funcao para ser chamda no final do script
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
                $ID_OP,
                $Latitude_Ponto,
                $Longitude_Ponto,
                $Latitude_Operador,
                $Longitude_Operador,
                $Endereco_Origem,
                $distancia_arredondada,
                $Status_Gps,
                $Chave_Unica_Rmm
    
            ]);
    
            $ultimo_id_gps_fora = $conexao->lastInsertId();
    
    
            //  Gera Log da Leitura fora do GPS
            $acao_log = "GPS Fora de Área na Leitura Livre";
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
                $ID_OP,
                'GPS Fora',
                $acao_log,
                $id_acao_log,
                $id_estacao,
                $tipo_log
            ]);
            // Fecha Log <<<<<<<  GPS Fora de Área <<<<<<<

    } 
}


         update_status_rmm($conexao, $status_leitura, $ID_RMM);


} // fecha o laço principal da consulta de leituras

echo "Houveram <b>".$contador."</b> Leituras Analisadas fora dos parâmetros e com suporte criado, para esta consulta.";

//  Gera Log da Leiitura fora do Checkin
$acao_log = "CRON Leituras Realizado com Sucesso";
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
'CRON Leituras Realizadas Finalizada.',
$acao_log,
$id_acao_log,
'0',
$tipo_log
]);
// Fecha Log <<<<<<<

// Fecha a conexão com o banco de dados
$conexao = null;
}