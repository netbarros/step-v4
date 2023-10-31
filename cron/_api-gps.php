<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header("Access-Control-Max-Age: 3600");
ini_set('memory_limit', '-1');

include_once $_SERVER['DOCUMENT_ROOT'].'/conexao.php';
$conexao = Conexao::getInstance();

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

if (!ini_get('date.timezone')) {
    date_default_timezone_set('America/Sao_Paulo');
}

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
/* INÍCIO: Crio a Chave unica da Sessao para da CRON Única Gerada no sistema */
/* Gerar strings aleatórias criptograficamente seguras, usamos 24 caracteres não repetitivos e aleatórios com criptografia nativa do PHP";*/
$chave_unica = bin2hex(random_bytes(33) . $horario_completo_agora);
/* FIM: Crio a Chave unica da Sessao para da CRON Única Gerada no sistema */


$Chave_Unica_CRON = $chave_unica;
// Define o Período da Busca dos Dados
 
//******************* Inicio: Funções do GPS  ***************************/

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
     // buffer de saída de dados do php]
    return $file_contents;
}



// funcao para calcular a distancia entre dois pontos    
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
// fim da funcao para calcular a distancia entre dois pontos

//******************* Fim: Funções do GPS  ***************************/


//** inicia a validação das leituras encontradas */
$sql = $conexao->query("SELECT * FROM estacoes WHERE id_estacao = 1");

 
if ($total > 0) { // inicia a validação das leituras encontradas

 

    $resultado = $sql->fetchALL(PDO::FETCH_ASSOC);

    $retorno_alerta="";
    
// função para fisparar os alertas //

    function enviarAlerta($id_estacao, $conexao, $assunto_email,$nome_suporte, $id_tipo_suporte, $retorno_alerta, $chave_unica) {
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
        $nome_suporte = $nome_suporte;

//**** modulo de gestao de alertas <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 

        // Invoca Gestão de Alertas, para conferir os usuários envolvidos no Projeto e suas regras de alerta personalizadas.
            $id_obra = $id_Projeto_Suporte;
            $chave_unica = $chave_unica;
            $categoria_suporte=$id_tipo_suporte; // não há categoria de suporte neste caso, por ser uma tarefa concluída
            $nome_suporte = $nome_suporte;
            $mensagem_alerta = "Aviso STEP:\n\r<br>Novo Ticket de Suporte: $nome_suporte,\n\r<br>O Projeto: $nome_projeto, Núcleo $nome_estacao, favor verificar.";
            $assunto = $assunto_email;
            $retorno_alerta = $categoria_suporte . ' <p> ' . $assunto . '</p> <p>'. $mensagem_alerta . '</p> <br><br><br> '. $chave_unica .' - ' . $id_obra;
       
            require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/suporte/gestao-alertas.php';
        //**** modulo de gestao de alertas <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< */ 


    } // fim funcao disparar alertas

    /* Finaliza: */



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
  
    $total_suporte =  $stmt->rowCount();
  
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
      $tipo_log = '41'; // gps fora de área
      $sql_log = "INSERT INTO log_leitura (chave_unica,id_usuario,acao_log,id_acao_log,estacao_logada,acao,tipo_log) VALUES (?,?,?,?,?,?,?)";
      $acao_log = "Conversa iniciada automaticamente, através da criação do Ticket $nome_suporte | ID: $ultimo_id_suporte";
      $conexao->prepare($sql_log)->execute([$chave_unica, $ID_SU, $acao_log, $ultimo_id_suporte, $id_estacao, $acao_log, $tipo_log]);
    }
  }
  

// funcao para criar a 1 conversa do ticket gerado




/**********************[ Funçõoes da Página ] ******************/



foreach ($resultado as $res) {



/***** Inicia a Verificação das Leituras Dentro da Área Permitida - GPS **/
   // aproveitamos para validar tbem a geolocalização enviada na leitura com a geolocalização do PLCode
        // se todas as coordenadas estiverem disponíveis realizamos o cálculo de distancia do ponto de leitura
        if ($Latitude_Ponto != "" && $Longitude_Ponto != "" && $Latitude_Operador != ""  && $Longitude_Operador != "") {


            // Script API pega endereço Google Maps >>>>
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$Latitude_Operador,$Longitude_Operador&key=AIzaSyBSIkQdqUjEKXybmAFvFEKCLxkNlFA_aJ8";
        $dadosSite = my_file_get_contents($url);

        if ($dadosSite !== false) {
            $data = json_decode($dadosSite, true);

            if (isset($data['results'][1]['formatted_address'])) {
                $Endereco_Origem = 'Endereço Captado na Leitura: ' . $data['results'][1]['formatted_address'];
            } else {
                $Endereco_Origem = 'Endereço não encontrado';
            }
        } else {
            $Endereco_Origem = 'Erro ao acessar a API do Google Maps';
        }

        // Calcula a distância entre as coordenadas
        $distancia = calcularDistancia($Latitude_Ponto, $Longitude_Ponto, $Latitude_Operador, $Longitude_Operador);


    if ($distancia > 90) { // mnargem de erro de escala do algoritimo do gps de 90 metros

        $status_leitura = "3"; // leitura fora de area
        $assunto_email .= "GPS Fora da Geo-Localização permitida";
        $nome_suporte = "Leitura fora da Geo-Localização permitida" ;  
        $id_tipo_suporte = '99';
        $retorno_alerta .= "\n\r<p><b>Projeto: </b> $nome_obra</p>\n\r";
        $retorno_alerta .= "\n\r<p><b>Estação:</b>  $nome_estacao</p>\n\r";
        $retorno_alerta .= "\n\r<p><b>PLCode:</b>  $nome_plcode</p>\n\r";
        $retorno_alerta .= "\n\r<p><b>Operador:</b> $nome_Operador</p>\n\r";
        $retorno_alerta .= "\n\r<p>Indicador: <b>$nome_parametro</b>\n\r,<b>Fora dos limites Permitidos</b>\n\r";
        $retorno_alerta .= "\n\r<p>Leitura: $leitura</p>\n\r";
        $retorno_alerta.= "\n\r<p>Controla Concentrações Mínima e Máxima</p>\n\r";
        $retorno_alerta .= "\n\r<p>Mínimo: $concen_min</p>\n\r";
        $retorno_alerta .= "\n\<p>Máxima: $concen_max</p>\n\r";

        
        $total_gps_fora += 1;

        $Status_Gps = '2'; //Leitura fora de área

        $distancia_arredondada = round($distancia, 2);

        $retorno_alerta .= "<h3 style='color:red;'><b>Aviso de Alerta de Leitura fora da área Permitida</b></h3>";
        $retorno_alerta .= "\n\r<p><b>Endereço Captado na Leitura:</b> $Endereco_Origem</p>\n\r";
        $retorno_alerta .= "\n\r<p><b>Latitude na Leitura:</b> $Latitude_Operador</p>\n\r";
        $retorno_alerta .= "\n\r<p><b>Longitude na Leitura:</b> $Longitude_Operador</p>\n\r";
        $retorno_alerta .= "\n\r<p><b>Latitude do PLCode:</b> $Latitude_Ponto</p>\n\r";
        $retorno_alerta .= "\n\r<p><b>Longitude do PLCode:</b> $Longitude_Ponto</p>\n\r";

        $retorno_alerta .= "\n\r<p>A Distância entre o PLCode e a Origem de Leitura, em Linha Reta:\n\rFoi de: <b>$distancia_arredondada</b> Metros</p>\n\r ";


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
            $Latitude_Operador,
            $Longitude_Operador,
            $Endereco_Origem,
            $distancia_arredondada,
            $Status_Gps,
            $Chave_Unica_Rmm

        ]);

        $ultimo_id_gps_fora = $conexao->lastInsertId();


        //  Gera Log da Leiitura fora do GPS
        $acao_log = "GPS";
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
        // Fecha Log <<<<<<<


        list($envia_alerta, $envia_email, $retorno_alerta) = handle_status($conexao,$assunto_email, $nome_suporte, $id_tipo_suporte,  $total_suporte, $total_ok, $total_normalizado, $ID_RMM, $status_leitura, 1, 1, $nome_parametro, $retorno_alerta);
      

    }  else if ($distancia < 90){ // mnargem de erro de escala do algoritimo do gps de 90 metros


        $status_leitura = '0'; // não envia alerta

        $Status_Gps = '1'; //Leitura dentro da Área

        $distancia_arredondada = round($distancia, 2);
       

        $retorno_alerta .= "\n\r<p><b>GPS Coordenadas OK!</b>\n\r A Distância entre o PLCode e a Origem de Leitura, <b>Está Segura</b> Reta é de: <b>$distancia_arredondada</b> Metros</p>\n\r ";

        $sql_gps = "INSERT INTO gps_fora (
                    id_estacao_gps,
                    id_plcode_gps, 
                    id_operador,
                    lat_ponto,
                    long_ponto,
                    lat_cap,
                    long_cap,
                    endereco_cap,
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
                        ?
                        )";
        $conexao->prepare($sql_gps)->execute([
            $id_estacao,
            $id_ponto,
            $id_operador,
            $Latitude_Ponto,
            $Longitude_Ponto,
            $Latitude_Operador,
            $Longitude_Operador,
            $Endereco_Origem,
            $Status_Gps,
            $Chave_Unica_Rmm

        ]);
    }
} 
// encerra a validação do GPS e distancia de leitura  

   // Inicia os Alertas para leitura fora 
        if ($status_leitura == "3") {

            enviarAlerta($id_estacao, $conexao, $assunto_email, $nome_suporte, $id_tipo_suporte, $retorno_alerta, $chave_unica);
            
           
            insere_suporte($conexao, $ID_SU, $id_tipo_suporte, $nome_suporte, $ID_RMM, $leitura, $id_estacao, $retorno_alerta, $id_ponto, $id_parametro, $ID_OP, $chave_unica);

                  }


    


} // fecha o foeeach de do gps 


// Fecha a conexão com o banco de dados
$conexao = null;  

}