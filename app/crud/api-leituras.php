<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header("Access-Control-Max-Age: 3600");
ini_set('memory_limit', '-1');

require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';

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
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-1  days', strtotime($Data_Atual_Periodo)));
$dia_tarefa_cron = date('d');

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

WHERE r.status_leitura='5' AND
  
      DATE_FORMAT(r.data_leitura, '%Y-%m-%d') > '$Data_Intervalo_Periodo'
GROUP BY r.id_rmm
ORDER BY r.data_Leitura ASC");
/* status = 5. status automaticamente gerado a cada nova leitura, são aquelas que estão aguardando a validação da API para agora 
    verificar se cada leitura com status 5 (aguardando análise), está dentro ou fora do parâmetro informado (concen_min e concen_max)
    r.status_leitura = '5'  */
$total = $sql->rowCount();

/*  print_r($sql);


 echo "\n\r".$total;

 exit; */

if ($total > 0) { // inicia a validação das leituras encontradas

    $total_suporte;
    $total_ok;
    $total_gps_fora;

    $resultado = $sql->fetchALL(PDO::FETCH_ASSOC);

    foreach ($resultado as $res) {

        $tipo_suporte_leitura = '1';
        $GPS; // irá armazenar se a geolocalização durante a Leitura está dentro ou fora da área do PLCode
        $Grupo_EP_Celular = '11937191079';
        $Chave_TOTAL_VOICE = 'd87dde571d00c6a6505c7ed00d60805c';
        $status_leitura;
        $retorno_alerta;
        $envia_alerta; // envia alerta
        $envia_email; // envia email

        $Nome_Contato_Cliente = trim(isset($res['Nome_Contato_Cliente'])) ? $res['Nome_Contato_Cliente'] : '';
        $Sobrenome_Contato_Cliente = trim(isset($res['Sobrenome_Contato_Cliente'])) ? $res['Sobrenome_Contato_Cliente'] : '';

        $email_Contato_Cliente = trim(isset($res['email_Contato_Cliente'])) ? $res['email_Contato_Cliente'] : '';
        $celular_Contato_Cliente = trim(isset($res['celular_Contato_Cliente'])) ? $res['celular_Contato_Cliente'] : '';

        $objetivo_ponto = trim(isset($res['objetivo_ponto'])) ? $res['objetivo_ponto'] : '';

        $ID_RMM = isset($res['id_rmm']) ? $res['id_rmm'] : '';

        $Chave_Unica_Rmm = trim(isset($res['Chave_Unica_Rmm'])) ? $res['Chave_Unica_Rmm'] : '';

        $controle_concentracao = $res['controle_concentracao'];

        $concen_min = trim(isset($res['concen_min'])) ? $res['concen_min'] : NULL;

        $concen_max = trim(isset($res['concen_max'])) ? $res['concen_max'] : NULL;

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
        $leitura = trim($res['leitura_entrada']);



        if ($controle_concentracao == "1") { // controla a leitura mínima

            $nome_controle_concentracao = "Mínima";
            echo $nome_controle_concentracao;

            if ($leitura < $concen_min) {

                $status_leitura = "3"; // leitura fora = 3 (leitura dentro = 1)  Leitura 5 = aguardando analise da API
                $retorno_alerta = "$nome_parametro com a Leitura Informada, inferior ao parâmetro Mínimo Ideal \n\r";

                echo "\n\r leitura minuma fora";
                // Inicia a gravação do suporte para status_leitura = 3 (leitura fora)

                $envia_alerta = '1'; // envia alerta
                $envia_email = '1'; // envia email

                //armazena o indexador para contar o status=3
                $total_suporte += 1;
                //atualiza a leitura em análise
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
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(':tipo_suporte', $tipo_suporte_leitura);
                $stmt->bindParam(':motivo_suporte', $retorno_alerta);
                $stmt->bindParam(':id_rmm_suporte', $ID_RMM);
                $stmt->bindParam(':leitura_suporte', $leitura);
                $stmt->bindParam(':estacao', $id_estacao);
                $stmt->bindParam(':plcode', $id_ponto);
                $stmt->bindParam(':parametro', $id_parametro);
                $stmt->bindParam(':quem_abriu', $id_operador);
                $stmt->bindParam(':chave_unica', $Chave_Unica_Rmm);
                $stmt->bindParam(':status_suporte', $status_suporte);

                $result = $stmt->execute();

                $ultimo_id_suporte = $conexao->lastInsertId();



                //  Gera Log do Novo Chamado de Suporte
                $acao_log = "RMM Suporte";
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
                    $tipo_log
                ]);
                // Fecha Log <<<<<<<

                // Envia direct ao supervisor responsável

                if ($sql_log) {

                    $conversa =  $nome_parametro . ' ' . $retorno_alerta;

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
                    $stmt->bindParam(':conversa', $conversa);


                    $result = $stmt->execute();

                    $ultimo_id = $conexao->lastInsertId();

                    $total =     $stmt->rowCount();


                    if ($result) {

                        //  Gera Log do Novo Chamado de Suporte
                        $acao_log = "Chat Suporte Direct Operação";
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
                            "Chat Suporte Direct Enviado referente ao suporte ID: $ultimo_id_suporte",
                            $tipo_log
                        ]);
                        // Fecha Log <<<<<<<

                    }
                } // fecha envio do direct



            }

            if ($leitura >= $concen_min) {

                echo "\n\r leitura minuma dentro";


                $status_leitura = "1";
                $retorno_alerta = "$nome_parametro com a Leitura Informada, Dentro do parâmetro Mínimo Ideal\n\r";
                $envia_alerta = '0'; // envia alerta
                $envia_email = '0'; // envia email

                //armazena o indexador para contar o status=3
                $total_ok += 1;
                //atualiza a leitura em análise
                $sql_atualiza_status_rmm = $conexao->query("UPDATE rmm SET status_leitura='1' WHERE id_rmm='$ID_RMM' "); // status = 1 (Leitura OK)

            }
        }




        if ($controle_concentracao == "2") { // controla a leitura máxima

            $nome_controle_concentracao = "Máxima";
            echo $nome_controle_concentracao;

            if ($leitura > $concen_max) {

                echo "\n\r leitura maxima fora";

                $status_leitura = "3"; // leitura fora = 3 (leitura dentro = 1)  Leitura 5 = aguardando analise da API
                $retorno_alerta = "$nome_parametro com a Leitura Informada, superior ao parâmetro Máximo Ideal\n\r";


                // Inicia a gravação do suporte para status_leitura = 3 (leitura fora)

                $envia_alerta = '1'; // envia alerta
                $envia_email = '1'; // envia email

                //armazena o indexador para contar o status=3
                $total_suporte += 1;
                //atualiza a leitura em análise
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
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(':tipo_suporte', $tipo_suporte_leitura);
                $stmt->bindParam(':motivo_suporte', $retorno_alerta);
                $stmt->bindParam(':id_rmm_suporte', $ID_RMM);
                $stmt->bindParam(':leitura_suporte', $leitura);
                $stmt->bindParam(':estacao', $id_estacao);
                $stmt->bindParam(':plcode', $id_ponto);
                $stmt->bindParam(':parametro', $id_parametro);
                $stmt->bindParam(':quem_abriu', $id_operador);
                $stmt->bindParam(':chave_unica', $Chave_Unica_Rmm);
                $stmt->bindParam(':status_suporte', $status_suporte);

                $result = $stmt->execute();

                $ultimo_id_suporte = $conexao->lastInsertId();



                //  Gera Log do Novo Chamado de Suporte
                $acao_log = "RMM Suporte";
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
                    $tipo_log
                ]);
                // Fecha Log <<<<<<<

                // Envia direct ao supervisor responsável

                if ($sql_log) {

                    $conversa =  $nome_parametro . ' ' . $retorno_alerta;

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
                    $stmt->bindParam(':conversa', $conversa);


                    $result = $stmt->execute();

                    $ultimo_id = $conexao->lastInsertId();

                    $total =     $stmt->rowCount();


                    if ($result) {

                        //  Gera Log do Novo Chamado de Suporte
                        $acao_log = "Chat Suporte Direct Operação";
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
                            "Chat Suporte Direct Enviado referente ao suporte ID: $ultimo_id_suporte",
                            $tipo_log
                        ]);
                        // Fecha Log <<<<<<<

                    }
                } // fecha envio do direct



            }

            if ($leitura <= $concen_max) {

                echo "\n\r leitura maxima dentro";

                $status_leitura = "1";
                $retorno_alerta = "$nome_parametro com a Leitura Informada, Dentro do parâmetro Máximo Ideal\n\r";

                $envia_alerta = '0'; // envia alerta
                $envia_email = '0'; // envia email

                //armazena o indexador para contar o status=3
                $total_ok += 1;
                //atualiza a leitura em análise
                $sql_atualiza_status_rmm = $conexao->query("UPDATE rmm SET status_leitura='1' WHERE id_rmm='$ID_RMM' "); // status = 1 (Leitura OK)

            }
        }



        if ($controle_concentracao == "3") { // controla a leitura mínima e máxima

            $nome_controle_concentracao = "Mínima e Máxima";

            echo $nome_controle_concentracao;

            if ($leitura >= $concen_min && $leitura <= $concen_max) {

                echo "\n\r leitura minima e maxima dentro";

                $status_leitura = "1";
                $retorno_alerta = "$nome_parametro com a Leitura Informada, Dentro do parâmetro Mínimo e Máximo Ideal\n\r";

                $envia_alerta = '0'; // envia alerta
                $envia_email = '0'; // envia email

                //armazena o indexador para contar o status=3
                $total_ok += 1;
                //atualiza a leitura em análise
                $sql_atualiza_status_rmm = $conexao->query("UPDATE rmm SET status_leitura='1' WHERE id_rmm='$ID_RMM' "); // status = 1 (Leitura OK)

            }

            if ($leitura < $concen_min && $leitura > $concen_max) {

                echo "\n\r leitura minima e maxima fora";

                $status_leitura = "3";
                $retorno_alerta = "$nome_parametro com a Leitura Informada, Fora do parâmetro Mínimo e Máximo Ideal\n\r";

                // Inicia a gravação do suporte para status_leitura = 3 (leitura fora)

                $envia_alerta = '1'; // envia alerta
                $envia_email = '1'; // envia email

                //armazena o indexador para contar o status=3
                $total_suporte += 1;
                //atualiza a leitura em análise
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
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(':tipo_suporte', $tipo_suporte_leitura);
                $stmt->bindParam(':motivo_suporte', $retorno_alerta);
                $stmt->bindParam(':id_rmm_suporte', $ID_RMM);
                $stmt->bindParam(':leitura_suporte', $leitura);
                $stmt->bindParam(':estacao', $id_estacao);
                $stmt->bindParam(':plcode', $id_ponto);
                $stmt->bindParam(':parametro', $id_parametro);
                $stmt->bindParam(':quem_abriu', $id_operador);
                $stmt->bindParam(':chave_unica', $Chave_Unica_Rmm);
                $stmt->bindParam(':status_suporte', $status_suporte);

                $result = $stmt->execute();

                $ultimo_id_suporte = $conexao->lastInsertId();



                //  Gera Log do Novo Chamado de Suporte
                $acao_log = "RMM Suporte";
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
                    $tipo_log
                ]);
                // Fecha Log <<<<<<<

                // Envia direct ao supervisor responsável

                if ($sql_log) {

                    $conversa =  $nome_parametro . ' ' . $retorno_alerta;

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
                    $stmt->bindParam(':conversa', $conversa);


                    $result = $stmt->execute();

                    $ultimo_id = $conexao->lastInsertId();

                    $total =     $stmt->rowCount();


                    if ($result) {

                        //  Gera Log do Novo Chamado de Suporte
                        $acao_log = "Chat Suporte Direct Operação";
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
                            "Chat Suporte Direct Enviado referente ao suporte ID: $ultimo_id_suporte",
                            $tipo_log
                        ]);
                        // Fecha Log <<<<<<<

                    }
                } // fecha envio do direct



            }
        }



        // aproveitamos para validar tbem a geolocalização enviada na leitura com a geolocalização do PLCode
        // se todas as coordenadas estiverem disponíveis realizamos o cálculo de distancia do ponto de leitura
        if ($Latitude_Ponto != "" && $Longitude_Ponto != "" && $Latitude_Operador != ""  && $Longitude_Operador != "") {


            // Script API pega endereço Google Maps >>>>

            $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$Latitude_Operador,$Longitude_Operador&key=AIzaSyAQsOKlWz3MbMeQHMrfAEtVR7ajrSj9274";

            $dadosSite = my_file_get_contents($url);
            $doc = new DOMDocument();
            $data = json_decode($dadosSite, true);
            $Endereco_Origem = '<b>Endereço Captado na Leitura</b>: ' . $data['results'][1]['formatted_address'];



            require_once 'calcula-distancia-gps.php'; // funcao para calculo de distancia entre coordenadas GPS
            /*
            $distance = getDistance($longitude1, $latitude1, $longitude2, $latitude2, 1); para distancia em metros
            echo $distance.' m'; // 2342.38m
            //$GPS (variavel que irá armazenar a distancia em metros
            */

            $distancia = distancia($Latitude_Ponto, $Longitude_Ponto, $Latitude_Operador, $Longitude_Operador);




            if ($distancia > 0.90) { // mnargem de erro de escala do algoritimo do gps de 90 metros

                $envia_alerta = '1'; // envia alerta
                $envia_email = '1'; // envia email

                $total_gps_fora += 1;

                $Status_Gps = '2'; //Leitura fora de área

                $distancia_check = $distancia * 1000;


                $GPS = "A Distância entre o PLCode e a Origem de Leitura, em Linha Reta é de: <b>$distancia_check</b> Metros ";

                $retorno_alerta .= "\n\r<p><b>ALERTA:</b> Coordenadas de Leitura <b>fora da área</b> de GPS do PLCode Lido.<p>\n\r";

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
                    $nome_tipo_suporte,
                    $acao_log,
                    $id_acao_log,
                    $id_estacao,
                    $tipo_log
                ]);
                // Fecha Log <<<<<<<

            } else {


                $envia_alerta = '0'; // envia alerta

                $Status_Gps = '1'; //Leitura Dentro da Área


                $distancia_check = $distancia * 1000;

                $GPS = "A Distância entre o PLCode e a Origem de Leitura, em Linha Reta é de: <b>$distancia_check</b> Metros ";

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


        // Inicia os Alertas para leitura fora e gps
        if ($envia_alerta == '1') {


            //Verifico se o tipo de suporte informado na validação dos dados é o mesmo do loop dos tipos de suporte
            // assim trago os alertas disponiveis caso houver para realizar o disparo

            //   if($alerta_tipo_suporte_leitura==$tipo_suporte_alerta){
            //   echo "ID RMM=".$ID_RMM;

            if ($envia_email == '1') {

                if ($email_Supervisor != "") { //email para o Supervisor

                    $email_para = $email_Supervisor;
                    $nome_para = $nome_Supervisor;

                    $mensagem_alerta = "Olá " . $nome_Supervisor . " .<br> " . $retorno_alerta . "<br><br><b> Obra:</b> " . $nome_obra . " <br> <b> Estação:</b> " . $nome_estacao . " <br><b>PLCODE:</b> " . $nome_plcode . ".<br>
                            <b>Operador:</b> " . $nome_Operador . ".";


                    //=====[ Inicio da classe envia email]=====================<<

                    include  $_SERVER['DOCUMENT_ROOT'] . '/app/crud/enviar-email.php';
                    //=====[ final da classe envia email]=====================<<


                } // finaliza envio de email para Supervisor



                if ($email_RO != "") { //inicia email para o RO

                    $mensagem_alerta = "Olá " . $nome_RO . " . <br>" . $retorno_alerta . "<br><br><b> Obra:</b> " . $nome_obra . " <br> <b>Estação:</b> " . $nome_estacao . " <br> <b>PLCODE:</b> " . $nome_plcode . ".<br>
                            <b>Operador:</b> " . $nome_Operador . ".";

                    $email_para = $email_RO;
                    $nome_para = $nome_RO;

                    //=====[ Inicio da classe envia email]=====================<<

                    include  $_SERVER['DOCUMENT_ROOT'] . '/app/crud/enviar-email.php';
                    //=====[ final da classe envia email]=====================<<

                } // finaliza envio de email para RO


                if ($email_Operador != "") { //inicia email para o Operador

                    $email_para = $email_Operador;
                    $nome_para = $nome_Operador;

                    $mensagem_alerta = "Olá " . $nome_Operador . " .<br> " . $retorno_alerta . "<br><b> Obra:</b> " . $nome_obra . " <br><b>Estação:</b> " . $nome_estacao . "<br><b>PLCODE:</b> " . $nome_plcode . ".<br>
                            <b>Operador:</b> " . $nome_Operador . ".";
                    //=====[ Inicio da classe envia email]=====================<<

                    include  $_SERVER['DOCUMENT_ROOT'] . '/app/crud/enviar-email.php';
                    //=====[ final da classe envia email]=====================<<

                } // finaliza envio de email para Operador



            }
        } // fecha envio de alertas

    } // fecha o laço principal da consulta de leituras



    // Início do Log CRON:
    //  Gera Log da Cron realizada
    $acao_log = "CRON Dados Leitura";
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
        'Tarefa CRON Realizada com Sucesso',
        $acao_log,
        $id_acao_log,
        '0',
        $tipo_log
    ]);
    // Fecha Log <<<<<<<
    // Fim do Log-  gravo o LOG se a CRON foi realizada com Sucesso

}
// fecha a a consulta com resultado para leituras encontradas no periodo com status 5 (aguardando análise)

echo "\n\r <br>";
echo "Total de Leituras Analisadas: " . $total;
echo "\n\r <br>";
echo "Total de Suporte Gerado: " . $total_suporte;
echo "\n\r <br>";
echo "Total de Leituras OK: " . $total_ok;
echo "\n\r <br>";
echo "Total de GPS Fora: " . $total_gps_fora;



// Funções para Tratamento de Variáveis:



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
