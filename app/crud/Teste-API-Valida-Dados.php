<?php 
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

 function getConnection() {
    $dbhost="162.241.99.91";
    $dbuser="step_root";
    $dbpass="F@087913";
     $dbname="step_teste";
     $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
     $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}


 if (! ini_get('date.timezone')) {
     date_default_timezone_set('America/Sao_Paulo');
 }
ini_set('memory_limit', '-1');
// Atribui uma conexão PDO
date_default_timezone_set('America/Sao_Paulo');

$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();


require $_SERVER['DOCUMENT_ROOT'].'/v2/total-voice/autoload.php';
use TotalVoice\Client as TotalVoiceClient;

$hr = date(" H ");
if($hr >= 12 && $hr<18) {
$Saudacao = "Boa tarde!";}
else if ($hr >= 0 && $hr <12 ){
$Saudacao = "Bom dia!";}
else {
$Saudacao = "Boa noite!";}

// Define o Período da Busca dos Dados
$Data_Atual_Periodo= date_create()->format('Y-m-d');
$Data_Intervalo_Periodo=date('Y-m-d', strtotime('-10 days', strtotime($Data_Atual_Periodo)));
    

       

//1ª FASE >> ===[ Valida as Leituras de RMM se estão OK ou fora de parâmetro ]===========>>
#VERITICA INTEGRIDADE DOS DADOS
$controle_bd = $conexao->query("DELETE FROM rmm WHERE id_parametro='0' OR leitura_entrada='10101.00'");


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
    colabOP.email_corporativo AS Email_Operador,
    colabSU.email_corporativo AS Email_Supervisor,
    colabRO.email_corporativo AS Email_RO,
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
        LEFT JOIN
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

WHERE
  
      DATE_FORMAT(r.data_leitura, '%Y-%m-%d') > '$Data_Intervalo_Periodo'
GROUP BY r.id_rmm
ORDER BY r.data_Leitura ASC"); 
    /* status = 5. status automaticamente gerado a cada nova leitura, são aquelas que estão aguardando a validação da API para agora 
    verificar se cada leitura com status 5 (aguardando análise), está dentro ou fora do parâmetro informado (concen_min e concen_max)
    r.status_leitura = '5'  */
    $total = $sql->rowCount ();

/*  print_r($sql);


 echo "\n\r".$total;

 exit; */

  


    if ($total > 0) {// inicia a validação das leituras encontradas
       
   // $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);  

   $sth = $conexao->query("SELECT *  FROM tipo_suporte ts
   INNER JOIN 
    tipo_suporte_alertas tps ON tps.tipo_suporte = ts.id_tipo_suporte

    WHERE ts.id_tipo_suporte= '1'
   "); // chekin em atraso

$result = $sth->fetch(PDO::FETCH_OBJ);

   
                $tipo_suporte_alerta = $result->tipo_suporte;
                $envia_email = $result->tipo_email;
                $envia_sms = $result->tipo_sms;
                $liga = $result->tipo_liga;
                $nome_tipo_suporte = $result->nome_suporte;


                
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC); 
    
 foreach ($resultado as $res) {

       //=====[ busca e armazena as regras de alertas de acordo com o tipo de suporte ]=============//<<
    $status_leitura='';
    $tipo_suporte_leitura='1';
    $retorno_alerta='';
    $leitura="";
    $GPS=''; // irá armazenar se a geolocalização durante a Leitura está dentro ou fora da área do PLCode
    $Grupo_EP_Celular = '11937191079';
    $Chave_TOTAL_VOICE = 'd87dde571d00c6a6505c7ed00d60805c';
    $mensagem_alerta="";
    $variacao_leitura="";
 $nome_controle_concentracao="";
           

//=====[ busca e armazena as regras de alertas de acordo com o tipo de suporte ]=============//>>
  
               


                

//=====[FIM busca e armazena as regras de alertas de acordo com o tipo de suporte ]=============//<<   

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
                $origem_Leitura = trim(isset($res['origem_leitura_parametro'])) ? $res['origem_leitura_parametro'] : ''; 

                if($origem_Leitura=='1'){
                $leitura = $res['leitura_entrada'];
                }

                if($origem_Leitura=='2'){
                $leitura = $res['leitura_saida'];

                }



                 $Nome_Contato_Cliente = trim(isset($res['Nome_Contato_Cliente'])) ? $res['Nome_Contato_Cliente'] : '';
                $Sobrenome_Contato_Cliente = trim(isset($res['Sobrenome_Contato_Cliente'])) ? $res['Sobrenome_Contato_Cliente'] : '';

                $email_Contato_Cliente = trim(isset($res['email_Contato_Cliente'])) ? $res['email_Contato_Cliente'] : '';
                $celular_Contato_Cliente = trim(isset($res['celular_Contato_Cliente'])) ? $res['celular_Contato_Cliente'] : '';

                $objetivo_ponto = trim(isset($res['objetivo_ponto'])) ? $res['objetivo_ponto'] : '';

                $ID_RMM = trim(isset($res['id_rmm'])) ? $res['id_rmm'] : '';

                $Chave_Unica_Rmm = trim(isset($res['Chave_Unica_Rmm'])) ? $res['Chave_Unica_Rmm'] : '';

                $controle_concentracao = trim(isset($res['controle_concentracao'])) ? $res['controle_concentracao'] : '';

                $concen_min = trim(isset($res['concen_min'])) ? $res['concen_min'] : '';

                $concen_max = trim(isset($res['concen_max'])) ? $res['concen_max'] : '';

                $unidade_medida_lida = trim(isset($res['nome_unidade_medida'])) ? $res['nome_unidade_medida'] : ''; 

                $nome_plcode = $res['nome_ponto'];

                $id_ponto = $res['id_ponto'];

                $id_operador = $res['id_operador'];

                $id_parametro = trim(isset($res['id_parametro'])) ? $res['id_parametro'] : ''; 

                $nome_parametro = trim(isset($res['nome_parametro'])) ? $res['nome_parametro'] : ''; 

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
                $nome_RO= trim(isset($res['Nome_RO'])) ? $res['Nome_RO'] : '';
                $email_RO = trim(isset($res['Email_RO'])) ? $res['Email_RO'] : '';
                $Tel_Ro = trim(isset($res['Tel_Ro'])) ? $res['Tel_Ro'] : '';



                

//===============[ como essa validação é para as leituras recebidas das operações, em leituras livres,
// sem checkin, vamos verificar se estão dentro ou fora dos parâmetros aceitáveis]=== >>


            if($controle_concentracao=="1") {// controla a leitura minima apenas

                 $nome_controle_concentracao = "Mínima";
                
                if($leitura < $concen_min){

                   
                    $status_leitura = "3";// leitura fora = 3 (leitura dentro = 1)  Leitura 5 = aguardando analise da API

                    $retorno_alerta = 'Indicador com a Leitura Informada, inferior ao parâmetro Mínimo Ideal'; 

                  

                }

                if($leitura > $concen_min){


                    $status_leitura = "1";
                    $retorno_alerta = 'Indicador com a Leitura Informada, Dentro do parâmetro Mínimo Ideal'; 
                }

            }

            if($controle_concentracao=="2") {// controla a leitura máxima apenas

                  $nome_controle_concentracao = "Máxima";
                
                if($leitura > $concen_max){

                
                    $status_leitura = "3";// leitura fora = 3 (leitura dentro = 1)  Leitura 5 = aguardando analise da API

                    $retorno_alerta = 'Indicador com a Leitura Informada, superior ao parâmetro Máximo Ideal'; 
                    

                }

                if($leitura < $concen_max){
                       
  
                    $status_leitura = "1";
                    $retorno_alerta = 'Indicador com a Leitura Informada, Dentro do parâmetro Máximo Ideal'; 
                }

            } 
            
            if($controle_concentracao=="3") {// controla a leitura mínima e máxima

                   $nome_controle_concentracao = "Mínima e Máxima";
                
                if($leitura > $concen_max || $leitura < $concen_min){
                    
                              
                  
                    $status_leitura = "3";// leitura fora = 3 (leitura dentro = 1)  Leitura 5 = aguardando analise da API

                    $retorno_alerta = 'Indicador com a Leitura Informada, Fora do parâmetro Mínimo e Máximo Ideal'; 

                    

                }

                if($leitura < $concen_max || $leitura > $concen_min){
                      

                    $status_leitura = "1";
                    $retorno_alerta = 'Indicador com a Leitura Informada, Dentro do parâmetro Mínimo e Máximo Ideal'; 
                }

            }

            if($controle_concentracao=="0") {// controla a leitura mínima e máxima

                  $nome_controle_concentracao = "Mínima e Máxima";
                
                if($leitura > $concen_max || $leitura < $concen_min){
          

                    $status_leitura = "3";// leitura fora = 3 (leitura dentro = 1)  Leitura 5 = aguardando analise da API

                    $retorno_alerta = 'Indicador com a Leitura Informada, Fora do parâmetro Mínimo e Máximo Ideal'; 

                   

                }

                if($leitura < $concen_max || $leitura > $concen_min){


                    $status_leitura = "1";
                    $retorno_alerta = 'Indicador com a Leitura Informada, Dentro do parâmetro Mínimo e Máximo Ideal'; 
                }

            }            
            

    // aproveitamos para validar tbem a geolocalização enviada na leitura com a geolocalização do PLCode
    // se todas as coordenadas estiverem disponíveis realizamos o cálculo de distancia do ponto de leitura
    if($Latitude_Ponto!="" && $Longitude_Ponto!="" && $Latitude_Operador!=""  && $Longitude_Operador!=""){

    $Endereco_Origem = '';
    $Status_Gps = '';



   
    // Script API pega endereço Google Maps >>>>
   
    $url= "https://maps.googleapis.com/maps/api/geocode/json?latlng=$Latitude_Operador,$Longitude_Operador&key=AIzaSyB0w-dRBF9x2Dc4oQt_TNZB6BGTaJMkRKs";
    $dadosSite = my_file_get_contents($url);
    $data = json_decode($dadosSite, true);
    $Endereco_Origem = '<b>Endereço Captado na Leitura</b>: '. $data['results'][1]['formatted_address'];



    require_once 'calcula-distancia-gps.php'; // funcao para calculo de distancia entre coordenadas GPS
    /*
    $distance = getDistance($longitude1, $latitude1, $longitude2, $latitude2, 1); para distancia em metros
    echo $distance.' m'; // 2342.38m
    //$GPS (variavel que irá armazenar a distancia em metros
    */

    $distancia = distancia($Latitude_Ponto, $Longitude_Ponto, $Latitude_Operador, $Longitude_Operador);




    if($distancia > 0.90){ // mnargem de erro de escala do algoritimo do gps de 90 metros

            $Status_Gps = '2'; //Leitura fora de área
            

           $GPS = "A Distância entre o PLCode e a Origem de Leitura, em Linha Reta é de: $distancia Km ";


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

                 $ultimo_id_gps_fora = $conexao->lastInsertId();  


           //  Gera Log da Leiitura fora do GPS
            $acao_log = "GPS";
            $tipo_log = '41'; // GPS Fora de Área
            $id_acao_log= $ultimo_id_gps_fora;


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
                $nome_suporte,
                $acao_log,
                $id_acao_log,
                $id_estacao,
                $tipo_log ]);
 // Fecha Log <<<<<<<


          } else {

              $Status_Gps = '1'; //Leitura Dentro da Área


               $GPS = "A Distância entre o PLCode e a Origem de Leitura, em Linha Reta é de: $distancia Km ";


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
} // encerra a validação do GPS e distancia de leitura



/*===============[ Validação Concluída das leituras dos indicadores, se estão dentro ou fora dos parâmetros ideais] temos o retorno:
    $status_leitura -> O Suporte será gerado sempre, caso o status_leitura = 3 (Leitura Fora)
    $retorno_leitura -> caso seja gerado suporte e quando for enviado alerta, essa informação irá junto.
    $tipo_suporte_leitura -> de acordo com o $tipo_suporte_leitura, definimos se há alerta para ser enviado e quais são, para que sejam disparados:
*/

// Primeiro vamos gerar o Suporte das Leituras Fora de Parâmetro:
 //echo '<pre>'.$ID_RMM.'</pre>';

 

  
if($status_leitura=='3'){ // quando estiver fora dos parametros

    // update status_leitura na tabela RMM



    $sql_atualiza_status_rmm = $conexao->query("UPDATE rmm SET status_leitura='3' WHERE id_rmm='$ID_RMM' "); // status = 3 (Leitura Fora)


// abre o chamado de suporte:
$status_suporte = '1';

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
    $stmt = $conexao->prepare( $sql );
    $stmt->bindParam( ':tipo_suporte', $tipo_suporte_leitura );
    $stmt->bindParam( ':motivo_suporte', $retorno_alerta );
     $stmt->bindParam( ':id_rmm_suporte', $ID_RMM );
      $stmt->bindParam( ':leitura_suporte', $leitura );
    $stmt->bindParam( ':estacao', $id_estacao);
    $stmt->bindParam( ':plcode', $id_ponto);
    $stmt->bindParam( ':parametro', $id_parametro);
    $stmt->bindParam( ':quem_abriu', $id_operador);
    $stmt->bindParam( ':chave_unica', $Chave_Unica_Rmm);
    $stmt->bindParam( ':status_suporte', $status_suporte);
    
    $result = $stmt->execute();

    $ultimo_id_suporte = $conexao->lastInsertId();  

  

 //  Gera Log do Novo Chamado de Suporte
            $acao_log = "RMM";
            $tipo_log = '40'; // Novo Suporte por Leitura de RMM


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
                $Chave_Unica_Rmm,
                $id_operador,
                $acao_log,
                $ultimo_id_suporte,
                $id_estacao,
                 "Leitura Fora dos Parâmetros, gerado suporte ID: $ultimo_id_suporte",
                $tipo_log ]);



 // Fecha Log <<<<<<<

 // Envia direct ao supervisor responsável
 
if($sql_log){

    $sql = "INSERT INTO suporte_conversas(
        id_suporte,id_remetente,destinatario_direto,conversa
        ) VALUES(
			:id_suporte,
		:id_remetente,
        :destinatario_direto,
        :conversa

            )";
    $stmt = $conexao->prepare( $sql );
    $stmt->bindParam( ':id_suporte', $ultimo_id_suporte );
    $stmt->bindParam( ':id_remetente', $id_operador );
    $stmt->bindParam( ':destinatario_direto', $ID_SU );
    $stmt->bindParam( ':conversa', $retorno_alerta );
 
    
    $result = $stmt->execute();
    
    $ultimo_id = $conexao->lastInsertId();
	
	$total=	 $stmt->rowCount();

    
    if ($result )
    {

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
                $Chave_Unica_Rmm,
                $ID_SU,
                $acao_log,
                $ultimo_id_suporte,
                $id_estacao,
                 "Chat Direct Enviado referente ao suporte ID: $ultimo_id_suporte",
                $tipo_log ]);
 // Fecha Log <<<<<<<
      
    }
}

// Envia direct ao supervisor responsável

//Verifico se o tipo de suporte informado na validação dos dados é o mesmo do loop dos tipos de suporte
// assim trago os alertas disponiveis caso houver para realizar o disparo

     //   if($alerta_tipo_suporte_leitura==$tipo_suporte_alerta){
           //   echo "ID RMM=".$ID_RMM;
       


                if($envia_email=='1'){

                    if($email_Supervisor!=""){ //email para o Supervisor

                    $email_para = $email_Supervisor;
                    $nome_para = $nome_Supervisor;



                  $mensagem_alerta = "Olá ".$nome_Supervisor." . ".$retorno_alerta."!\n\r<br><b> Estação:</b> ".$nome_estacao." \n\r<br><b>PLCODE:</b> ".$nome_plcode.".\n\r<br>
                  <b>Operador:</b> " . $nome_Operador . ".";

                    

                  
//=====[ Inicio da classe envia email]=====================<<
  
include  $_SERVER['DOCUMENT_ROOT'].'/app/crud/enviar-email.php';    
//=====[ final da classe envia email]=====================<<


                    } // finaliza envio de email para Supervisor

                    sleep(2);

                    if($email_RO!=""){ //inicia email para o RO

                  $mensagem_alerta = "Olá ".$nome_RO." . ".$retorno_alerta."!\n\r<br> <b>Estação:</b> ".$nome_estacao." \n\r<br> <b>PLCODE:</b> ".$nome_plcode.".\n\r<br>
                  <b>Operador:</b> " . $nome_Operador . ".";                         
    
                    $email_para = $email_RO;
                    $nome_para = $nome_RO;

                   

      //=====[ Inicio da classe envia email]=====================<<
  
include  $_SERVER['DOCUMENT_ROOT'].'/app/crud/enviar-email.php';    
//=====[ final da classe envia email]=====================<<


                    } // finaliza envio de email para RO
 sleep(2);

                    if($email_Operador!=""){ //inicia email para o Operador
    
                    $email_para = $email_Operador;
                    $nome_para = $nome_Operador;

                  $mensagem_alerta = "Olá ".$nome_Operador." . ".$retorno_alerta."!\n\r<br><b>Estação:</b> ".$nome_estacao." \n\r<br><b>PLCODE:</b> ".$nome_plcode.".\n\r<br>
                  <b>Operador:</b> " . $nome_Operador . ".";                    
//=====[ Inicio da classe envia email]=====================<<
  
include  $_SERVER['DOCUMENT_ROOT'].'/app/crud/enviar-email.php';    
//=====[ final da classe envia email]=====================<<

                    } // finaliza envio de email para Operador


                     sleep(3);

                }


                if($envia_sms=='1'){
                    

                    if($Tel_SU!=""){ //inicia a ligação Supervisor

                        $mensagem_alerta = limita_caracteres("Olá ".$nome_Supervisor." . ".$retorno_alerta."! Estação: ".$nome_estacao." - PLCODE: ".$nome_plcode.".
                        Operador: " . $nome_Operador . ".",160,false);
 
                        
                        $client = new TotalVoiceClient("d87dde571d00c6a6505c7ed00d60805c");

                        $numeroDestino =str_replace(' ', '', $Tel_SU); 
                        $response = $client->sms->enviar($numeroDestino, $mensagem_alerta);
                        $response->getContent(); // {}

                          

                        $valida_retorno = $response->getStatusCode();

                            if($valida_retorno==200){

                            // grava o log de retorno da API do SMS enviado:
                            $data_criacao = date_create()->format('Y-m-d H:i:s');
                                                    
                                //  Gera Log da Envio do SMS
                                            $acao_log = "Alerta SMS";
                                            $tipo_log = '45'; // Envio Automático de SMS para o Suporte, SMS enviado com Sucesso!

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
                                                'SMS Enviado com sucesso para o '.$nome_Supervisor.', para o Número: '.$Tel_SU.' ',
                                                $acao_log,
                                                $ID_SU,
                                                $id_estacao,
                                                $tipo_log ]);
                                //  Gera Log da Envio do SMS                            

                                        } // fecha gravação do log de retorno da API do SMS enviado<<       
                                        else {



                                                                                                        
                                    //  Gera Log da Envio do SMS
                                                $acao_log = "Alerta SMS";
                                                $tipo_log = '46'; // Envio Automático de SMS para o Suporte, SMS enviado com Sucesso!

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
                                                    "Falha $valida_retorno , ao Enviar SMS para o $nome_Supervisor, para o Número: $numeroDestino ",
                                                    $acao_log,
                                                    $ID_SU,
                                                    $id_estacao,
                                                    $tipo_log ]);
                                    //  Gera Log da Envio do SMS
                                        }             

                            } // fecha ligação Supervisor


                     sleep(3);

                    if($Tel_Ro!=""){ //inicia a ligação RO

                        $mensagem_alerta =   $mensagem_alerta = limita_caracteres("Olá ".$nome_RO." . ".$retorno_alerta."! Estação: ".$nome_estacao." - PLCODE: ".$nome_plcode.".
                        Operador: " . $nome_Operador . ".",160,false);
                        
            
            
                         $client = new TotalVoiceClient("d87dde571d00c6a6505c7ed00d60805c");
            
                        $numeroDestino =str_replace(' ', '', $Tel_Ro); 
                        $response = $client->sms->enviar($numeroDestino, $mensagem_alerta);
                        $response->getContent(); // {}

                       

                            $valida_retorno = $response->getStatusCode();

                            if($valida_retorno==200){
                                                            
                                    //  Gera Log da Envio do SMS
                                                $acao_log = "Alerta SMS";
                                                $tipo_log = '45'; // Envio Automático deSMS para o Suporte, SMS enviado com Sucesso!

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
                                                    'SMS Enviado com sucesso para o '.$nome_RO.', para o Número: '.$numeroDestino.' ',
                                                    $acao_log,
                                                    $ID_RO,
                                                    $id_estacao,
                                                    $tipo_log ]);
                                    //  Gera Log da Envio do SMS

                                        } // fecha gravação do log de retorno da API do SMS enviado<<                           
                                else {


                                                            
                                //  Gera Log da Envio do SMS
                                            $acao_log = "Alerta SMS";
                                            $tipo_log = '46'; // Envio Automático de SMS para o Suporte, SMS enviado com Sucesso!

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
                                                "Falha $valida_retorno , ao Enviar SMS para o $nome_RO, para o Número: $numeroDestino ",
                                                $acao_log,
                                                $ID_RO,
                                                $id_estacao,
                                                $tipo_log ]);
                                //  Gera Log da Envio do SMS
}
                          } // fecha ligação RO 

                     sleep(3);        

                    if($Tel_OP!=""){ //inicia a ligação Operador

                        $mensagem_alerta =   $mensagem_alerta = limita_caracteres("Olá ".$nome_Operador." . ".$retorno_alerta."! Estação: ".$nome_estacao." - PLCODE: ".$nome_plcode.".
                        Suporte Gerado.",160,false);
                        
            
                        $client = new TotalVoiceClient("d87dde571d00c6a6505c7ed00d60805c");
                        $numeroDestino =str_replace(' ', '', $Tel_OP); 
                        $response = $client->sms->enviar($numeroDestino, $mensagem_alerta);
                        $response->getContent(); // {}


                        $valida_retorno = $response->getStatusCode();

                            if($valida_retorno==200){
                                                            
                                            //  Gera Log da Envio do SMS
                                                        $acao_log = "Alerta SMS";
                                                        $tipo_log = '45'; // Envio Automático de E-mail para o Suporte, Email enviado com Sucesso!

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
                                                            'SMS Enviado com sucesso para o '.$nome_Operador.', para o Número: '.$numeroDestino.' ',
                                                            $acao_log,
                                                            $ID_RO,
                                                            $id_estacao,
                                                            $tipo_log ]);
                                            //  Gera Log da Envio do SMS

                                        } // fecha gravação do log de retorno da API do SMS enviado<<                          
                                    else {

                                                               
                                        //  Gera Log da Envio do SMS
                                                    $acao_log = "Alerta SMS";
                                                    $tipo_log = '46'; // Envio Automático de E-mail para o Suporte, Email enviado com Sucesso!

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
                                                        "Falha $valida_retorno , ao Enviar SMS para o $nome_Operador, para o Número: $numeroDestino ",
                                                        $acao_log,
                                                        $ultimo_id_suporte,
                                                        $ID_OP,
                                                        $tipo_log ]);
                                        //  Gera Log da Envio do SMS
                                        }
                            } // fecha ligação Operador    
                     sleep(2);
                }

                if($liga=='1'){

                     if($Tel_SU!=""){ //inicia a ligação Supervisor

                    $mensagem_alerta = "Olá ".$nome_Supervisor." . ".$retorno_alerta."! Estação: ".$nome_estacao." - PLCODE: ".$nome_plcode.".
                        Suporte Gerado.";

                         $client = new TotalVoiceClient("d87dde571d00c6a6505c7ed00d60805c");

                        $numeroDestino =str_replace(' ', '', $Tel_SU); 
                        $response = $client->tts->enviar($numeroDestino, $mensagem_alerta);
                        $response->getContent(); // {}
                    
                        }

                     if($Tel_Ro!=""){ //inicia a ligação Supervisor

                        $mensagem_alerta = "Olá ".$nome_RO." . ".$retorno_alerta."! Estação: ".$nome_estacao." - PLCODE: ".$nome_plcode.".
                        Suporte Gerado.";

                         $client = new TotalVoiceClient("d87dde571d00c6a6505c7ed00d60805c");

                        $numeroDestino =str_replace(' ', '', $Tel_Ro); 
                        $response = $client->tts->enviar($numeroDestino, $mensagem_alerta);
                        $response->getContent(); // {}
                    
                        }
                        
                     if($Tel_OP!=""){ //inicia a ligação Supervisor

                        $mensagem_alerta = "Olá ".$nome_Operador." . ".$retorno_alerta."! Estação: ".$nome_estacao." - PLCODE: ".$nome_plcode.".
                        Suporte Gerado.";

                          $client = new TotalVoiceClient("d87dde571d00c6a6505c7ed00d60805c");
         
                        $numeroDestino =str_replace(' ', '', $Tel_OP); 
                        $response = $client->tts->enviar($numeroDestino, $mensagem_alerta);
                        $response->getContent(); // {}
                    
                        }                        

                }


           // } // finaliza o envio dos alertas para as leituras analisadas


        } // fecha validação para leitura fora, status_leitura = 3
        else { // ou seja, se o status da leitura não for 3 (fora de parâmetro), atualizamos ela para Leitura OK (1)

             

         $sql_atualiza_status_rmm = $conexao->query("UPDATE rmm SET status_leitura='1' WHERE id_rmm='$ID_RMM' "); // status = 3 (Leitura Fora)

           
        }

     }// fecha o foreach do laço dos comparativos das variáveis armazenadas






}// fecha a a consulta com resultado para leituras encontradas no periodo com status 5 (aguardando análise)

// FINAL DA 1ª FASE >> ===[ Valida as Leituras de RMM se estão OK ou fora de parâmetro ]===========<<



// INÍCIO DA 2 º FASE >> ===[Validação dos Checkins ]===========================================>>>>>>>>>

/* seleção dos ultimos checkins nas ultimas 24 horas (irá analisar o tempo da hora agendada com a hora lida e alterar o 
status e comparará o chekcin agendado com o realizado, caso false algum e o horário já tenha sido alcançado, irá gerar o alerta e comunicação) */





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

    pr.id_parametro,
    pr.nome_parametro,

    u.nome_unidade_medida,
    r.id_rmm as ID_RMM_Leitura,
    r.status_leitura,
    r.id_operador as ID_Operador_RMM,    
    r.leitura_entrada,
    r.leitura_saida,
    r.chave_unica AS Chave_Unica_Rmm,
  
    logl.chave_unica AS Chave_Unica_Log,
   
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
    uro.id AS ID_RO


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
    parametros_ponto pr ON pr.id_parametro = ch.id_parametro
        LEFT JOIN
    unidade_medida u ON pr.unidade_medida = u.id_unidade_medida
        LEFT JOIN
    rmm r ON r.id_rmm = ch.id_rmm
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

 

WHERE
    ch.status_checkin = '5' 
    AND DATE_FORMAT(ch.data_cadastro_checkin, '%Y-%m-%d') > '$Data_Intervalo_Periodo'
       GROUP BY ch.id_checkin
ORDER BY ch.data_cadastro_checkin ASC"); 
    /*  ch.status_checkin = '5' AND  ----   status = 5. status automaticamente gerado a cada nova leitura, são aquelas que estão aguardando a validação da API para agora 
    verificar se cada leitura com status 5 (aguardando análise), está dentro ou fora do parâmetro informado (concen_min e concen_max)
   */
    $total_checkin = $sql_checkin->rowCount ();

 //print_r($sql);

 //exit;

  


    if ($total_checkin > 0) {// inicia a validação dos checkins agendados e realizados
       
   // $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);  

   $sth = $conexao->query("SELECT 
*
   
   FROM tipo_suporte ts
   INNER JOIN 
    tipo_suporte_alertas tps ON tps.tipo_suporte = ts.id_tipo_suporte

    WHERE ts.id_tipo_suporte= '94'
   "); // tipo_suporte=94 (Checkin com Atraso)

$result = $sth->fetch(PDO::FETCH_OBJ);

   
                $tipo_suporte_alerta = $result->tipo_suporte;
                $envia_email = $result->tipo_email;
                $envia_sms = $result->tipo_sms;
                $liga = $result->tipo_liga;
                $nome_tipo_suporte = $result->nome_suporte;


                
$resultado_checkin = $sql_checkin->fetchAll(PDO::FETCH_ASSOC); 
    
 foreach ($resultado_checkin as $res) {

       //=====[ busca e armazena as regras de alertas de acordo com o tipo de suporte ]=============//<<
    $status_leitura='';
    $tipo_suporte_leitura='94'; 
    $retorno_alerta='';
    $leitura_rmm_checkin="";
    $GPS=''; // irá armazenar se a geolocalização durante a Leitura está dentro ou fora da área do PLCode
    $Grupo_EP_Celular = '11937191079';
    $Chave_TOTAL_VOICE = 'd87dde571d00c6a6505c7ed00d60805c';
    $mensagem_alerta="";
 
           

//=====[ busca e armazena as regras de alertas de acordo com o tipo de suporte ]=============//>>
  
               


                

//=====[FIM busca e armazena as regras de alertas de acordo com o tipo de suporte ]=============//<<   

//====[ Finaliza a Coleta dos Dados para serem analisados e distribuídos, agora na segunda faze de acordo com o 
//resultado de cada análise, seu alerta, conforme categoria do Suporte] <<<<


                // Reserva as variaveis apra comparação e preenchimento do envio dos alertas, caso seja necessário
                // trata e desmembra a data da leitura //
                $data_leitura = $res['Data_Checkin'];
                //$data_leitura =  strtotime($phpdate) * 1000;
                $hora_min =  date('H:i', strtotime($data_leitura));
                $dia_mes_ano =  date('d/m/Y', strtotime($data_leitura));
                //====<< 

                // trata e separa a leitura em colunia unica, inpependente se é entrada ou saida, para amostragem
                // Operadores Ternários para Parametros e seus derivados acontece, por haver leitura de RMM/Checkin apenas com o PLCode.
                $origem_Leitura = trim(isset($res['origem_leitura_parametro'])) ? $res['origem_leitura_parametro'] : ''; 


                 $tipo_checkin = trim(isset($res['tipo_checkin'])) ? $res['tipo_checkin'] : ''; 

                 if($tipo_checkin=='2'){

                if($origem_Leitura=='1'){
                $leitura_rmm_checkin = $res['leitura_entrada'];
                }

                if($origem_Leitura=='2'){
                $leitura_rmm_checkin = $res['leitura_saida'];

                }
                } else {

                     $leitura_rmm_checkin = "Checkin Presencial";
                }

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

                $id_parametro = trim(isset($res['id_parametro'])) ? $res['id_parametro'] : ''; 

                $nome_parametro = trim(isset($res['nome_parametro'])) ? $res['nome_parametro'] : ''; 

                $nome_obra = $res['nome_obra'];
                
                $nome_estacao = $res['nome_estacao'];

                $id_estacao = $res['id_estacao'];

                $Latitude_Ponto = trim(isset($res['Latitude_Ponto'])) ? $res['Latitude_Ponto'] : ''; 

                $Longitude_Ponto = trim(isset($res['Longitude_Ponto'])) ? $res['Longitude_Ponto'] : ''; 

                $Latitude_Operador_Checkin = trim(isset($res['Latitude_Operador_Checkin'])) ? $res['Latitude_Operador_Checkin'] : ''; 

                $Longitude_Operador_Checkin = trim(isset($res['Longitude_Operador_Checkin'])) ? $res['Longitude_Operador_Checkin'] : ''; 


                $ID_OP = trim(isset($res['ID_OP'])) ? $res['ID_OP'] : '';
                $nome_Operador = trim(isset($res['Nome_Operador'])) ? $res['Nome_Operador'] : '';
                $email_Operador = trim(isset($res['Email_Operador'])) ? $res['Email_Operador'] : '';
                $Tel_OP = trim(isset($res['Tel_OP'])) ? $res['Tel_OP'] : '';

                $ID_SU = trim(isset($res['ID_SU'])) ? $res['ID_SU'] : '';
                $nome_Supervisor = trim(isset($res['Nome_Supervisor'])) ? $res['Nome_Supervisor'] : '';
                $email_Supervisor = trim(isset($res['Email_Supervisor'])) ? $res['Email_Supervisor'] : '';
                $Tel_SU = trim(isset($res['Tel_SU'])) ? $res['Tel_SU'] : '';

                $ID_RO = trim(isset($res['ID_RO'])) ? $res['ID_RO'] : '';
                $nome_RO= trim(isset($res['Nome_RO'])) ? $res['Nome_RO'] : '';
                $email_RO = trim(isset($res['Email_RO'])) ? $res['Email_RO'] : '';
                $Tel_Ro = trim(isset($res['Tel_Ro'])) ? $res['Tel_Ro'] : '';


//====== INÍCIO valida a diferença de hora agendada e hora da leitura, para por status do checkin em tempo ou em atraso== se em atraso irá abrir o suporte===<<                

/*
Precisa retornar estas informações depois da validação, pq irão para Log Leitura, Checkin, SMS, Suporte, Ligação e Email, os mesmos dados:

$status_checkin = "1";// dentro do Horário = 1 | Leitura em atraso = 2 | Leitura 5 = aguardando analise da API

$retorno_alerta = 'Leitura realizada com atraso de ' ' minutos'; 

$retorno_alerta = 'Leitura realizada dentro do prazo com ' ' minutos' de antecedência ; 

$alerta_tipo_suporte_leitura = '94'; // nome do suporte= Checkin com Atraso (TB tipo_suporte)

*/

 date_default_timezone_set('America/Sao_Paulo');
 
                  $checkTime = strtotime($Hora_Leitura_Agendada);
                  $loginTime = strtotime($Hora_Checkin_Realizado);

                 // $diff = $checkTime - $loginTime; // diferença entre hora do checkin e hora da leitura efetiva -> em segundos!

                  $totalSecondsDiff= abs($checkTime - $loginTime);
/*

$totalMinutesDiff = $totalSecondsDiff/60; //710003.75
$totalHoursDiff   = $totalSecondsDiff/60/60;//11833.39
$totalDaysDiff    = $totalSecondsDiff/60/60/24; //493.05
$totalMonthsDiff  = $totalSecondsDiff/60/60/24/30; //16.43
$totalYearsDiff   = $totalSecondsDiff/60/60/24/365; //1.35


outro
*/






                  $prazo_decorrido = round($totalSecondsDiff/60, 2); //710003.75
                   $prazo_decorrido_horas =  round($totalSecondsDiff/60/60, 2);

                 
                    if($prazo_decorrido <='30'){

                    $status_checkin='1'; // leitura dentro do prazo
                    $retorno_alerta = "<br> Checkin Adiantado com $prazo_decorrido_horas  H/m de antecedência."; 
                    }

                     if($prazo_decorrido > '30' || $prazo_decorrido < '35' ){

                    $status_checkin='2'; // checkin dentro da carencia de 35 min.
                    $retorno_alerta = "<br> Checkin dentro do  Prazo de 35 minutos de carência, com $prazo_decorrido_horas  H/m de diferença.\n"; 

                        if($ID_RMM_Checkin!='' && $tipo_checkin=='2'){

                           $retorno_alerta.="<br> Estação: $nome_estacao <br> PLCode: $nome_plcode <br> Leitura do Indicador $nome_parametro, valor: $leitura_rmm_checkin $unidade_medida_lida";
                          
                        } else {
                            $retorno_alerta.="<br> <br> <b>Checkin Presencial</b>";

                        }
                    }

                    
                    if($prazo_decorrido >'35'){

                    $status_checkin='3'; //Checkin Fora do Prazo
                    $retorno_alerta = "<br> Checkin com diferença de $prazo_decorrido_horas H/m em relação ao Horário agendado: $Hora_Leitura_Agendada.\n"; 

                         if($ID_RMM_Checkin!='' && $tipo_checkin=='2'){

                           $retorno_alerta.="<br> Estação: $nome_estacao <br> PLCode: $nome_plcode <br> Leitura do Indicador $nome_parametro, valor: $leitura_rmm_checkin $unidade_medida_lida";
                          
                        } else {
                            $retorno_alerta.="<br><br> <b>Checkin Presencial</b>";

                        }

                    }

                    if($prazo_decorrido==''){

                      $status_checkin='0'; //Checkin Fora do Prazo
                         $retorno_alerta = "<br> Checkin impreciso, Horário  agendado: $Hora_Leitura_Agendada . Horário Lido: $Hora_Checkin_Realizado\n "; 

                              if($ID_RMM_Checkin!='' && $tipo_checkin=='2'){

                           $retorno_alerta.="Estação: $nome_estacao <br> PLCode: $nome_plcode <br> Leitura do Indicador $nome_parametro, valor: $leitura_rmm_checkin $unidade_medida_lida";
                          
                        } else {
                            $retorno_alerta.="<br><br> <b>Checkin Presencial</b>";

                        }
                     
                    }


// gravo o tempo decorrido entre o agendado e a hora lida de cada checkin (evento)
$sql_update_tempo_check= $conexao->query("UPDATE checkin SET prazo_decorrido='$prazo_decorrido' WHERE id_checkin='$ID_Checkin'");





//====== FIM valida a diferença de hora agendada e hora da leitura, para por status do checkin em tempo ou em atraso== se em atraso irá abrir o suporte===>>>




//====== INÎCIO validar tbem a geolocalização enviada na leitura do Checkin com a geolocalização do PLCode===>>>
    // se todas as coordenadas estiverem disponíveis realizamos o cálculo de distancia do ponto de leitura

    if($Latitude_Ponto!="" && $Longitude_Ponto!="" && $Latitude_Operador_Checkin!=""  && $Longitude_Operador_Checkin!=""){

    $Endereco_Origem = '';
    $Status_Gps = '';
   
    // Script API pega endereço Google Maps >>>>
    $url= "https://maps.googleapis.com/maps/api/geocode/json?latlng=$Latitude_Operador_Checkin,$Longitude_Operador_Checkin&key=AIzaSyB0w-dRBF9x2Dc4oQt_TNZB6BGTaJMkRKs";
   $dadosSite = my_file_get_contents($url);
    $data = json_decode($dadosSite, true);
    $Endereco_Origem = '<b>Endereço Captado na Leitura</b>: '. $data['results'][1]['formatted_address'];
    // Script API pega endereço Google Maps <<<<


    require_once 'calcula-distancia-gps.php'; // funcao para calculo de distancia entre coordenadas GPS
    /*
    $distance = getDistance($longitude1, $latitude1, $longitude2, $latitude2, 1); para distancia em metros
    echo $distance.' m'; // 2342.38m
    //$GPS (variavel que irá armazenar a distancia em metros
    */

    $distancia = distancia($Latitude_Ponto, $Longitude_Ponto, $Latitude_Operador_Checkin, $Longitude_Operador_Checkin);




    if($distancia > 0.90){ // mnargem de erro de escala do algoritimo do gps de 90 metros

            $Status_Gps = '2'; //Leitura fora de área
            

           $GPS = "A Distância entre o PLCode e a Origem de Leitura, em Linha Reta é de: $distancia Km ";


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
                    $Latitude_Operador_Checkin,
                    $Longitude_Operador_Checkin,
                   $Endereco_Origem,
                    $Status_Gps,
                    $Chave_Unica_Checkin
                
                ]);

                 $ultimo_id_gps_fora = $conexao->lastInsertId();  


           //  Gera Log da Leiitura fora do GPS
            $acao_log = "GPS";
            $tipo_log = '41'; // GPS Fora de Área
            $id_acao_log= $ultimo_id_gps_fora;


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
                $Chave_Unica_Checkin,
                $id_operador,
                $Endereco_Origem,
                $acao_log,
                $id_acao_log,
                $Status_Gps,
                $tipo_log ]);
 // Fecha Log <<<<<<<


          } else {

              $Status_Gps = '1'; //Leitura Dentro da Área


               $GPS = "A Distância entre o PLCode e a Origem de Leitura, em Linha Reta é de: $distancia Km ";


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
                                $ID_OP,
                                $Latitude_Ponto,
                                $Longitude_Ponto,
                                $Latitude_Operador_Checkin,
                                $Longitude_Operador_Checkin,
                               $Endereco_Origem,
                                $Status_Gps,
                                $Chave_Unica_Checkin
                            
                            ]);


    }
} // encerra a validação do GPS e distancia de leitura



/*===============[ Validação Concluída das leituras dos indicadores, se estão dentro ou fora dos parâmetros ideais] temos o retorno:
    $status_leitura -> O Suporte será gerado sempre, caso o status_leitura = 3 (Leitura Fora)
    $retorno_leitura -> caso seja gerado suporte e quando for enviado alerta, essa informação irá junto.
    $tipo_suporte_leitura -> de acordo com o $tipo_suporte_leitura, definimos se há alerta para ser enviado e quais são, para que sejam disparados:
*/

// Primeiro vamos gerar o Suporte das Leituras Fora de Parâmetro:
 //echo '<pre>'.$ID_RMM.'</pre>';

 

  
if($status_checkin=='3'){ // 1 no prazo, 2 dentro do prazo 3 atrasado 5 aguardando, 0 indefinido

    // update status_leitura na tabela Checkin

$sql_atualiza_status_checkin = $conexao->query("UPDATE checkin SET status_checkin='3' WHERE id_checkin='$ID_Checkin' "); // status = 3 (Leitura Fora)


// abre o chamado de suporte:
$status_suporte = '1';

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
    $stmt = $conexao->prepare( $sql );
    $stmt->bindParam( ':tipo_suporte', $tipo_suporte_leitura );
    $stmt->bindParam( ':motivo_suporte', $retorno_alerta );
     $stmt->bindParam( ':id_rmm_suporte', $ID_RMM_Checkin );
      $stmt->bindParam( ':leitura_suporte', $leitura_rmm_checkin);
    $stmt->bindParam( ':estacao', $id_estacao);
    $stmt->bindParam( ':plcode', $id_ponto);
    $stmt->bindParam( ':parametro', $id_parametro);
    $stmt->bindParam( ':quem_abriu', $ID_OP);
    $stmt->bindParam( ':chave_unica', $Chave_Unica_Checkin);
    $stmt->bindParam( ':status_suporte', $status_suporte);
    
    $result = $stmt->execute();

    $ultimo_id_suporte = $conexao->lastInsertId();  

  

 //  Gera Log do Novo Chamado de Suporte
            $acao_log = "Checkin";
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
                $Chave_Unica_Rmm,
                $id_operador,
                $acao_log,
                $ultimo_id_suporte,
                $id_estacao,
                 "Checkin em Atraso, gerado suporte ID: $ultimo_id_suporte",
                $tipo_log ]);
 // Fecha Log <<<<<<<

 
 // Envia direct ao supervisor responsável
 
if($sql_log){

    $sql = "INSERT INTO suporte_conversas(
        id_suporte,id_remetente,destinatario_direto,conversa
        ) VALUES(
			:id_suporte,
		:id_remetente,
        :destinatario_direto,
        :conversa

            )";
    $stmt = $conexao->prepare( $sql );
    $stmt->bindParam( ':id_suporte', $ultimo_id_suporte );
    $stmt->bindParam( ':id_remetente', $id_operador );
    $stmt->bindParam( ':destinatario_direto', $ID_SU );
    $stmt->bindParam( ':conversa', $retorno_alerta );
 
    
    $result = $stmt->execute();
    
    $ultimo_id = $conexao->lastInsertId();
	
	$total=	 $stmt->rowCount();

    
    if ($result )
    {

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
                $Chave_Unica_Rmm,
                $ID_SU,
                $acao_log,
                $ultimo_id_suporte,
                $id_estacao,
                 "Chat Direct Enviado referente ao Checkin no suporte ID: $ultimo_id_suporte",
                $tipo_log ]);
 // Fecha Log <<<<<<<
      
    }
}

// Envia direct ao supervisor responsável

//Verifico se o tipo de suporte informado na validação dos dados é o mesmo do loop dos tipos de suporte
// assim trago os alertas disponiveis caso houver para realizar o disparo

     //   if($alerta_tipo_suporte_leitura==$tipo_suporte_alerta){ // 94 checkin em atraso verifica regras
      // de alertas para esrte tipo de suporte
           //   echo "ID RMM=".$ID_RMM;

                if($envia_email=='1'){

                    if($email_Supervisor!=""){ //email para o Supervisor

                       $mensagem_alerta = "Olá ".$nome_Supervisor." . ".$retorno_alerta."!\n\r <br><b>Estação:</b> ".$nome_estacao." \n\r<br><b> PLCODE:</b> ".$nome_plcode.".\n\r<br>
                        <b>Operador:</b> " . $nome_Operador . ".";

                    $email_para = $email_Supervisor;
                    $nome_para = $nome_Supervisor;

                    // Chama a function que mont aos checkins não realizados para o dia agendado //
                  
/*  //=====[ Inicio da tabela de checkin]=====================<<

 
  
include  $_SERVER['DOCUMENT_ROOT'].'/app/crud/tabela-checkin-nao-realizado.php';     
//=====[ final da tabela de checkin]=====================<< */

                    // fecha functiuon
                  
//=====[ Inicio da classe envia email]=====================<<
  
include  $_SERVER['DOCUMENT_ROOT'].'/app/crud/enviar-email-checkin.php';     
//=====[ final da classe envia email]=====================<<


                    } // finaliza envio de email para Supervisor

                    sleep(2);

                    if($email_RO!=""){ //inicia email para o RO


                        $mensagem_alerta = "Olá ".$nome_RO." . ".$retorno_alerta."!\n\r<br><b>Estação:</b> ".$nome_estacao." \n\r<br><b>PLCODE:</b> ".$nome_plcode.".\n\r<br>
                        <b>Operador:</b> " . $nome_Operador . ".";

    
                    $email_para = $email_RO;
                    $nome_para = $nome_RO;

                  
/*  //=====[ Inicio da tabela de checkin]=====================<<

 
  
include  $_SERVER['DOCUMENT_ROOT'].'/app/crud/tabela-checkin-nao-realizado.php';     
//=====[ final da tabela de checkin]=====================<< */
                    // fecha functiuon            

      //=====[ Inicio da classe envia email]=====================<<
  
include  $_SERVER['DOCUMENT_ROOT'].'/app/crud/enviar-email-checkin.php';    
//=====[ final da classe envia email]=====================<<


                    } // finaliza envio de email para RO
 sleep(2);

                    if($email_Operador!=""){ //inicia email para o Operador


                   
                        $mensagem_alerta = "Olá ".$nome_Operador." . ".$retorno_alerta."!\n\r<br><b>Estação:</b> ".$nome_estacao." \n\r<br> <b>PLCODE:<b> ".$nome_plcode.".\n\r<br>
                        <b>Operador:</b> " . $nome_Operador . ".";

        
                    $email_para = $email_Operador;
                    $nome_para = $nome_Operador;

                               
/*  //=====[ Inicio da tabela de checkin]=====================<<

 
  
include  $_SERVER['DOCUMENT_ROOT'].'/app/crud/tabela-checkin-nao-realizado.php';     
//=====[ final da tabela de checkin]=====================<< */

                    // fecha functiuon

//=====[ Inicio da classe envia email]=====================<<
  
include  $_SERVER['DOCUMENT_ROOT'].'/app/crud/enviar-email-checkin.php';    
//=====[ final da classe envia email]=====================<<

                    } // finaliza envio de email para Operador


                     sleep(3);

                }


                if($envia_sms=='1'){
                    

                    if($Tel_SU!=""){ //inicia a ligação Supervisor

                        $mensagem_alerta = limita_caracteres("Olá ".$nome_Supervisor." . ".$retorno_alerta."! Estação: ".$nome_estacao." - PLCODE: ".$nome_plcode.".
                        Operador: " . $nome_Operador . ".",160,false);
 
                        
                        $client = new TotalVoiceClient("d87dde571d00c6a6505c7ed00d60805c");

                        $numeroDestino =str_replace(' ', '', $Tel_SU); 
                        $response = $client->sms->enviar($numeroDestino, $mensagem_alerta);
                        $response->getContent(); // {}

                          

                        $valida_retorno = $response->getStatusCode();

                            if($valida_retorno==200){

                            // grava o log de retorno da API do SMS enviado:
                            $data_criacao = date_create()->format('Y-m-d H:i:s');
                                                    
                                //  Gera Log da Envio do SMS
                                            $acao_log = "Alerta SMS";
                                            $tipo_log = '45'; // Envio Automático de SMS para o Suporte, SMS enviado com Sucesso!

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
                                                'SMS Enviado com sucesso para o '.$nome_Supervisor.', para o Número: '.$Tel_SU.' ',
                                                $acao_log,
                                                $ID_SU,
                                                $id_estacao,
                                                $tipo_log ]);
                                //  Gera Log da Envio do SMS                            

                                        } // fecha gravação do log de retorno da API do SMS enviado<<       
                                        else {



                                                                                                        
                                    //  Gera Log da Envio do SMS
                                                $acao_log = "Alerta SMS";
                                                $tipo_log = '46'; // Envio Automático de SMS para o Suporte, SMS enviado com Sucesso!

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
                                                    "Falha $valida_retorno , ao Enviar SMS para o $nome_Supervisor, para o Número: $numeroDestino ",
                                                    $acao_log,
                                                    $ID_SU,
                                                    $id_estacao,
                                                    $tipo_log ]);
                                    //  Gera Log da Envio do SMS
                                        }             

                            } // fecha ligação Supervisor


                     sleep(3);

                    if($Tel_Ro!=""){ //inicia a ligação RO

                       $mensagem_alerta = limita_caracteres("Olá ".$nome_RO." . ".$retorno_alerta."! Estação: ".$nome_estacao." - PLCODE: ".$nome_plcode.".
                        Operador: " . $nome_Operador . ".",160,false);
                        
            
            
                         $client = new TotalVoiceClient("d87dde571d00c6a6505c7ed00d60805c");
            
                        $numeroDestino =str_replace(' ', '', $Tel_Ro); 
                        $response = $client->sms->enviar($numeroDestino, $mensagem_alerta);
                        $response->getContent(); // {}

                       

                            $valida_retorno = $response->getStatusCode();

                            if($valida_retorno==200){
                                                            
                                    //  Gera Log da Envio do SMS
                                                $acao_log = "Alerta SMS";
                                                $tipo_log = '45'; // Envio Automático deSMS para o Suporte, SMS enviado com Sucesso!

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
                                                    'SMS Enviado com sucesso para o '.$nome_RO.', para o Número: '.$numeroDestino.' ',
                                                    $acao_log,
                                                    $ID_RO,
                                                    $id_estacao,
                                                    $tipo_log ]);
                                    //  Gera Log da Envio do SMS

                                        } // fecha gravação do log de retorno da API do SMS enviado<<                           
                                else {


                                                            
                                //  Gera Log da Envio do SMS
                                            $acao_log = "Alerta SMS";
                                            $tipo_log = '46'; // Envio Automático de SMS para o Suporte, SMS enviado com Sucesso!

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
                                                "Falha $valida_retorno , ao Enviar SMS para o $nome_RO, para o Número: $numeroDestino ",
                                                $acao_log,
                                                $ID_RO,
                                                $id_estacao,
                                                $tipo_log ]);
                                //  Gera Log da Envio do SMS
}
                          } // fecha ligação RO 

                     sleep(3);        

                    if($Tel_OP!=""){ //inicia a ligação Operador

                       $mensagem_alerta = limita_caracteres("Olá ".$nome_Operador." . ".$retorno_alerta."! Estação: ".$nome_estacao." - PLCODE: ".$nome_plcode.".
                        Suporte Gerado.",160,false);
                        
            
                        $client = new TotalVoiceClient("d87dde571d00c6a6505c7ed00d60805c");
                        $numeroDestino =str_replace(' ', '', $Tel_OP); 
                        $response = $client->sms->enviar($numeroDestino, $mensagem_alerta);
                        $response->getContent(); // {}


                        $valida_retorno = $response->getStatusCode();

                            if($valida_retorno==200){
                                                            
                                            //  Gera Log da Envio do SMS
                                                        $acao_log = "Alerta SMS";
                                                        $tipo_log = '45'; // Envio Automático de E-mail para o Suporte, Email enviado com Sucesso!

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
                                                            'SMS Enviado com sucesso para o '.$nome_Operador.', para o Número: '.$numeroDestino.' ',
                                                            $acao_log,
                                                            $ID_RO,
                                                            $id_estacao,
                                                            $tipo_log ]);
                                            //  Gera Log da Envio do SMS

                                        } // fecha gravação do log de retorno da API do SMS enviado<<                          
                                    else {

                                                               
                                        //  Gera Log da Envio do SMS
                                                    $acao_log = "Alerta SMS";
                                                    $tipo_log = '46'; // Envio Automático de E-mail para o Suporte, Email enviado com Sucesso!

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
                                                        "Falha $valida_retorno , ao Enviar SMS para o $nome_Operador, para o Número: $numeroDestino ",
                                                        $acao_log,
                                                        $ultimo_id_suporte,
                                                        $ID_OP,
                                                        $tipo_log ]);
                                        //  Gera Log da Envio do SMS
                                        }
                            } // fecha ligação Operador    
                     sleep(2);
                }

                if($liga=='1'){

                     if($Tel_SU!=""){ //inicia a ligação Supervisor

                    $mensagem_alerta = "Olá ".$nome_Supervisor." . ".$retorno_alerta."! Estação: ".$nome_estacao." - PLCODE: ".$nome_plcode.".
                        Suporte Gerado.";

                         $client = new TotalVoiceClient("d87dde571d00c6a6505c7ed00d60805c");

                        $numeroDestino =str_replace(' ', '', $Tel_SU); 
                        $response = $client->tts->enviar($numeroDestino, $mensagem_alerta);
                        $response->getContent(); // {}
                    
                        }

                     if($Tel_Ro!=""){ //inicia a ligação Supervisor

                        $mensagem_alerta = "Olá ".$nome_RO." . ".$retorno_alerta."! Estação: ".$nome_estacao." - PLCODE: ".$nome_plcode.".
                        Suporte Gerado.";

                         $client = new TotalVoiceClient("d87dde571d00c6a6505c7ed00d60805c");

                        $numeroDestino =str_replace(' ', '', $Tel_Ro); 
                        $response = $client->tts->enviar($numeroDestino, $mensagem_alerta);
                        $response->getContent(); // {}
                    
                        }
                        
                     if($Tel_OP!=""){ //inicia a ligação Supervisor

                        $mensagem_alerta = "Olá ".$nome_Operador." . ".$retorno_alerta."! Estação: ".$nome_estacao." - PLCODE: ".$nome_plcode.".
                        Suporte Gerado.";

                          $client = new TotalVoiceClient("d87dde571d00c6a6505c7ed00d60805c");
         
                        $numeroDestino =str_replace(' ', '', $Tel_OP); 
                        $response = $client->tts->enviar($numeroDestino, $mensagem_alerta);
                        $response->getContent(); // {}
                    
                        }                        

                }


           // } // finaliza o envio dos alertas para as leituras analisadas


        } // fecha validação para leitura fora, status_leitura = 3
        else { // ou seja, se o status da leitura não for 3 (fora de parâmetro), atualizamos ela para Leitura OK (1)

             

         $sql_atualiza_status_checkin = $conexao->query("UPDATE checkin SET status_checkin='$status_checkin' WHERE id_checkin='$ID_Checkin' "); // status = 3 (Leitura Fora)

           
        }
        

     }// fecha o foreach do laço dos comparativos das variáveis armazenadas

// fecha total de checkin > 0 



// INICIO da verificação se o agendamento foi realizado como checkin no periodo, caso não conste, será gerado uma listagem que irá por email e alimentará a tabela de checkins não realizados por dia==>>


// verifico quandos checkins agendados (periodo ponto), não aparecem na tabela de checkin (realizados), então crio uma tabela dos itens por estação, para ser incluída no template de email
//que será enviado, para os supervisores e ro's, e tbem  preciso criar no Dashboard, uma listagem de checkins não realizados, hj temos os agendados e os realidados, os agedndados não realizados não temos.
//Essa lista pode alimentar essa tabela, por que registrará os checkins (agendamentos, de determinado dia, se foi realizado (consta na tb de checkin ou não))


 // FIM da verificação se o agendamento foi realizado como checkin no periodo, caso não conste, será gerado uma listagem que irá por email e alimentará a tabela de checkins não realizados por dia==<<
}

//FINAL DA 2ª FASE >> ===[ Validação dos Checkins ]===========================================>>>>>>>>>


// selecao dos ultimos suportes nas ultimas 24 horas (porque pode abrir chamado avulso)




 function my_file_get_contents( $site_url ){
      $ch = curl_init();
      $timeout = 5; // set to zero for no timeout
      curl_setopt ($ch, CURLOPT_URL, $site_url);
      curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
      ob_start();
      curl_exec($ch);
      curl_close($ch);
      $file_contents = ob_get_contents();
      ob_end_clean();
      return $file_contents;
    }



    function limita_caracteres($texto, $limite, $quebra = true){
   $tamanho = strlen($texto);
   if($tamanho <= $limite){ //Verifica se o tamanho do texto é menor ou igual ao limite
      $novo_texto = $texto;
   }else{ // Se o tamanho do texto for maior que o limite
      if($quebra == true){ // Verifica a opção de quebrar o texto
         $novo_texto = trim(substr($texto, 0, $limite))."...";
      }else{ // Se não, corta $texto na última palavra antes do limite
         $ultimo_espaco = strrpos(substr($texto, 0, $limite), " "); // Localiza o útlimo espaço antes de $limite
         $novo_texto = trim(substr($texto, 0, $ultimo_espaco))."..."; // Corta o $texto até a posição localizada
      }
   }
   return $novo_texto; // Retorna o valor formatado
}


function intervalo( $entrada, $saida ) {
  $entrada = explode( ':', $entrada );
  $saida   = explode( ':', $saida );
  $minutos = ( $saida[0] - $entrada[0] ) * 60 + $saida[1] - $entrada[1];
  if( $minutos < 0 ) $minutos += 24 * 60;
  return sprintf( '%d:%d', $minutos / 60, $minutos % 60 );
}

function mintohora($minutos)
{
$hora = floor($minutos/60);
$resto = $minutos%60;
return $hora.':'.$resto;
}