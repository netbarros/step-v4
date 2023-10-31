<?php
header("content-type: application/json");
// Path: crud\tarefas\action-tarefas.php
// Compare this snippet from crud\suporte\gestao-alertas.php:

require $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$acao = (isset($_POST['acao'])) ? $_POST['acao'] : '';

$id_tarefa = (isset($_POST['id_tarefa'])) ? $_POST['id_tarefa'] : '';

$titulo_tarefa = (isset($_POST['titulo_tarefa'])) ? $_POST['titulo_tarefa'] : '';
$projeto_tarefa = (isset($_POST['projeto_tarefa'])) ? $_POST['projeto_tarefa'] : '';
$id_ponto = isset($_POST['plcode_tarefa']) ? (int)$_POST['plcode_tarefa'] : 0;
$id_parametro = isset($_POST['indicador_tarefa']) ? (int)$_POST['indicador_tarefa'] : 0;
$hora_leitura = (isset($_POST['horario_tarefa'])) ? $_POST['horario_tarefa'] : '';
$ciclo_leitura = (isset($_POST['recorrencia_tarefa'])) ? $_POST['recorrencia_tarefa'] : '';
$tipo_checkin = (isset($_POST['tipo_tarefa'])) ? $_POST['tipo_tarefa'] : '';
$status_periodo = (isset($_POST['status_tarefa'])) ? $_POST['status_tarefa'] : 1; //	status_tarefa = 1 (ativo, (2) inativa, (3) concluída	
$modo_checkin_periodo = (isset($_POST['agendamento_tarefa'])) ? $_POST['agendamento_tarefa'] : '';
$usuario_tarefa = (isset($_POST['usuario_tarefa'])) ? $_POST['usuario_tarefa'] : NULL;
$data_cadastro = date_create()->format('Y-m-d H:i:s');
$data_tarefa = (isset($_POST['due_date'])) ? $_POST['due_date'] : '';
$detalhes_tarefa = (isset($_POST['detalhes_tarefa'])) ? $_POST['detalhes_tarefa'] : '';
$monitora_tarefa = trim(isset($_POST['monitora_tarefa'])) ? $_POST['monitora_tarefa'] : '';
$dia_semana = trim(isset($_POST['dia_semana'])) ? $_POST['dia_semana'] : '';
$tags = trim(isset($_POST['tags'])) ? $_POST['tags'] : '';
$communication = trim(isset($_POST['communication'])) ? $_POST['communication'] : '';
$alerta_email = trim(isset($_POST['alerta_email'])) ? $_POST['alerta_email'] : '0';
$alerta_sms = trim(isset($_POST['alerta_sms'])) ? $_POST['alerta_sms'] : '0';
$alerta_whats = trim(isset($_POST['alerta_whats'])) ? $_POST['alerta_whats'] : '0';


$usuario_solicitante = trim(filter_input(INPUT_POST, 'usuario_solicitante')) ?: ($_COOKIE['nome_usuario'] ?? '');


$nome_solicitante = trim(isset($_SESSION['nome'])) ? $_SESSION['nome'] : 'Indefinido na Sessão';


$latitude = trim(isset($_POST['latitude'])) ? $_POST['latitude'] : '';
$longitude = trim(isset($_POST['longitude'])) ? $_POST['longitude'] : '';

//===[ CHAVE ÚNICA da SESSAO] a cada sessao, o step registra uma codificação única.
// Obtenha a data atual no formato dd-mm-yyyy
$data_chave = date("d-m-Y");

// Obtenha a hora atual no formato hh:mm
$hora_chave = date("H:i");

$pagina_ativa_chave = $_SESSION['pagina_atual'] ?? 'gerado_automatico';

// Acrescenta o hífen na concatenação
$usuario_sessao_chave = $_SESSION['nome'] . '-' . ($_SESSION['pagina_atual'] ?? 'gerado_automatico');

$id_usuario_sessao_chave = $_SESSION['id'];

// Crie a chave única
$chave_unica = $_COOKIE['CHAVE_UNICA_SESSAO_ATUAL'] ?? md5($data_chave . $hora_chave . $pagina_ativa_chave . $usuario_sessao_chave . $id_usuario_sessao_chave);
/*===[ CHAVE ÚNICA da SESSAO]==== */

$periodo_verificacao = "5_minutos"; // como monitorará a tarefa, o periodo de verificação será de 5 minutos (real time)

if ($acao == "cadastrar") {

// Validações das variáveis recebidas via POST -->

    if($titulo_tarefa =='' || $titulo_tarefa == null){
        $retorno = array('codigo' => 0, 'mensagem' => 'Você precisa definir o Título da Tarefa.');
        echo json_encode($retorno);
        exit;
    }

    if($tipo_checkin=='tarefa_agendada'){
       
        if($usuario_tarefa =='' || $usuario_tarefa == null){

            $retorno = array('codigo' => 0, 'mensagem' => 'Você precisa definir o Usuário para a tarefa.');

           
           // Codificar a resposta final como JSON e envolvê-la em um array
            $json_final = json_encode(array($retorno));

            // Agora você pode enviar $json_final como sua resposta JSON
            echo $json_final;
            exit;
        }
    }


     if($projeto_tarefa =='' || $projeto_tarefa == null){
          $retorno = array('codigo' => 0, 'mensagem' => 'Você precisa definir o Projeto para a tarefa.');
           // Codificar a resposta final como JSON e envolvê-la em um array
           $json_final = json_encode(array($retorno));

           // Agora você pode enviar $json_final como sua resposta JSON
           echo $json_final;
          exit;
     }

if($modo_checkin_periodo =='' || $modo_checkin_periodo == null){

    $retorno = array('codigo' => 0, 'mensagem' => 'Você precisa definir o Agendamento para a tarefa.');
     // Codificar a resposta final como JSON e envolvê-la em um array
     $json_final = json_encode(array($retorno));

     // Agora você pode enviar $json_final como sua resposta JSON
     echo $json_final;
    
            exit;
    
    }
    
    if($ciclo_leitura =='' || $ciclo_leitura == null){
        $retorno = array('codigo' => 0, 'mensagem' => 'Você precisa definir a Recorrência (Ciclo de Leitura), para esta tarefa.');
        // Codificar a resposta final como JSON e envolvê-la em um array
        $json_final = json_encode(array($retorno));

        // Agora você pode enviar $json_final como sua resposta JSON
        echo $json_final;
        exit;
    }

    if($tipo_checkin =='' || $tipo_checkin == null){
        $retorno = array('codigo' => 0, 'mensagem' => 'Você precisa definir o Tipo de Tarefa.');
        // Codificar a resposta final como JSON e envolvê-la em um array
        $json_final = json_encode(array($retorno));

        // Agora você pode enviar $json_final como sua resposta JSON
        echo $json_final;
        exit;
    }

    // inclui a estação/núcleo para a Tarefa

    try {
        $sql_estacao = $conexao->prepare("SELECT id_estacao FROM estacoes WHERE id_obra=:id_obra");
        $sql_estacao->bindParam(':id_obra', $projeto_tarefa, PDO::PARAM_INT);
        $sql_estacao->execute();
    
        $count = $sql_estacao->rowCount();
        $rt = $sql_estacao->fetch(PDO::FETCH_OBJ);
        $id_estacao = $rt->id_estacao ?? null;
    
        if (empty($id_estacao)) {
            $retorno = array('codigo' => 0, 'mensagem' => 'Você precisa ter ao menos 1 Núcleo cadastrado. para poder criar qualquer Tarefa.');
             // Codificar a resposta final como JSON e envolvê-la em um array
             $json_final = json_encode(array($retorno));

             // Agora você pode enviar $json_final como sua resposta JSON
             echo $json_final;
            exit;
        }
    
    } catch (PDOException $e) {
        $retorno = array('codigo' => 0, 'mensagem' => 'Banco de Dados Inacessível no momento: ' . $e->getMessage());
        // Codificar a resposta final como JSON e envolvê-la em um array
        $json_final = json_encode(array($retorno));

        // Agora você pode enviar $json_final como sua resposta JSON
        echo $json_final;
        error_log("Erro ao consultar o banco de dados para id_estacao em Tarefas: " . $e->getMessage());
        exit;
    }
    


if($data_tarefa==''){
    $data_tarefa = date_create()->format('Y-m-d');
}
// Verifica se $usuario_tarefa é null e substitua por um valor padrão, se necessário
$usuario_tarefa = isset($usuario_tarefa) ?? '01' ;



    // inclui nova Tarefa
$sql = $conexao->prepare('INSERT INTO periodo_ponto (
    titulo_tarefa, id_obra, id_estacao, id_ponto, id_parametro, 
    hora_leitura, ciclo_leitura, tipo_checkin, status_periodo, 
    usuario_tarefa, usuario_solicitante, modo_checkin_periodo, 
    detalhes_tarefa, monitora_tarefa, tags, data_tarefa, data_cadastro
) VALUES(
    :titulo_tarefa, :id_obra, :id_estacao, :id_ponto, :id_parametro,
    :hora_leitura, :ciclo_leitura, :tipo_checkin, :status_periodo,
    :usuario_tarefa, :usuario_solicitante, :modo_checkin_periodo,
    :detalhes_tarefa, :monitora_tarefa, :tags, :data_tarefa, :data_cadastro
)');
$sql->execute(array(
    ':titulo_tarefa' => $titulo_tarefa,
    ':id_obra' => $projeto_tarefa,
    ':id_estacao' => $id_estacao,
    ':id_ponto' => $id_ponto,
    ':id_parametro' => $id_parametro,
    ':hora_leitura' => $hora_leitura,
    ':ciclo_leitura' => $ciclo_leitura,
    ':tipo_checkin' => $tipo_checkin,
    ':status_periodo' => $status_periodo,
    ':usuario_tarefa' => $usuario_tarefa,
    ':usuario_solicitante' => $usuario_solicitante,
    ':modo_checkin_periodo' => $modo_checkin_periodo,
    ':detalhes_tarefa' => $detalhes_tarefa,
    ':monitora_tarefa' => $monitora_tarefa,
    ':tags' => $tags,
    ':data_tarefa' => $data_tarefa,
    ':data_cadastro' => $data_cadastro
));

$count = $sql->rowCount();

$ultimo_id_tarefa = $conexao->lastInsertId();

if ($count > 0) {


        $id_tipo_suporte = '111'; // id_tipo_suporte = 111 (Tarefa Delegada em Andamento) - nova tarefa
        $json_externo='';
        $mensagem_externa='';
        


        if ($monitora_tarefa == '1') {


            if ($alerta_email == "" && $alerta_sms == '' && $alerta_whats == '') { // valida a variavel 

                $mensagem = array('codigo' => 0, 'mensagem' => 'Você pediu para Monitorar a Tarefa, mas, não escolheu os meios de comunicação!');

                echo json_encode($mensagem);

                exit;
            }

// procura para saber se o usuário já possui uma coleção de notificações para esta tarefa, caso sim, atualiza, caso não, cria uma nova coleção de notificações para o usuário
            $sql_alerta = $conexao->query("SELECT id_notificacao_usuario FROM notificacoes_usuario WHERE id_usuario ='$usuario_solicitante' AND id_obra = '$projeto_tarefa' AND id_tipo_suporte = '$id_tipo_suporte'");
            $conta_alerta = $sql_alerta->rowCount();

            $alertas = '';
            if ($conta_alerta > 0) {

                $row = $sql_alerta->fetch(PDO::FETCH_OBJ);
                $id_notificacao_usuario = $row->id_notificacao_usuario;
              

                $sql_altera_alerta = $conexao->query("UPDATE notificacoes_usuario  
            SET alerta_email='$alerta_email', alerta_sms='$alerta_sms', alerta_whats='$alerta_whats', status_notificacao_usuario='$monitora_tarefa'
            WHERE id_notificacao_usuario ='$id_notificacao_usuario'");

                if ($sql_altera_alerta) {
                    $alertas .= '<br><br> Acompanhamento da Tarefa para : <strong>' . $nome_solicitante . '</strong>:';

                    if($alerta_email == '1'){
                        $alertas .= '<br>  <small>Alerta por E-mail: <strong>Ativo</strong></small>';
                    }else{                      
                        $alertas .= '<br>  <small>Alerta por E-mail: <strong>Inativo</strong></small>';
                    }
                    if($alerta_sms == '1'){
                        $alertas .= '<br>  <small>Alerta por SMS: <strong>Ativo</strong></small>';
                    }else{
                        $alertas .= '<br>  <small>Alerta por SMS: <strong>Inativo</strong></small>';
                    }
                    if($alerta_whats == '1'){
                        $alertas .= '<br>  <small>Alerta por WhatsApp: <strong>Ativo</strong></small>';
                    }else{
                        $alertas .= '<br>  <small>Alerta por WhatsApp: <strong>Inativo</strong></small>';
                    }

                    $alertas .= '<br>  <small>Monitoramento da Tarefa: <strong>Ativo</strong></small>';

                    $alertas .= '<br>  <small>Periodo de Verificação: <strong>5 minutos</strong></small>';

                    $alertas .= '<br>  <small>Alertas Atualizados com sucesso!</small>';

                }
            } else {

                try {
                    $sql_novo_alerta = "INSERT INTO notificacoes_usuario 
                    (id_usuario, id_obra, id_tipo_suporte, alerta_email, alerta_sms, alerta_whats, periodo_verificacao, status_notificacao_usuario) 
                    VALUES (:usuario_solicitante, :projeto_tarefa, :id_tipo_suporte, :alerta_email, :alerta_sms, :alerta_whats, :periodo_verificacao, :monitora_tarefa)";
                
                    $stmt = $conexao->prepare($sql_novo_alerta);
                
                    $stmt->bindParam(':usuario_solicitante', $usuario_solicitante);
                    $stmt->bindParam(':projeto_tarefa', $projeto_tarefa);
                    $stmt->bindParam(':id_tipo_suporte', $id_tipo_suporte);
                    $stmt->bindParam(':alerta_email', $alerta_email);
                    $stmt->bindParam(':alerta_sms', $alerta_sms);
                    $stmt->bindParam(':alerta_whats', $alerta_whats);
                    $stmt->bindParam(':periodo_verificacao', $periodo_verificacao);
                    $stmt->bindParam(':monitora_tarefa', $monitora_tarefa);
                
                    $stmt->execute();
                
                    $alertas .= '<br> Alertas Incluídos com sucesso!';
                } catch(PDOException $e) {
                    error_log("Erro ao inserir novo alerta: " . $e->getMessage());
                    throw new Exception('Erro ao inserir novo alerta.');
                }
                
            }
        }

// conclui a inclusão da tarefa, e gera os alertas para o usuário solicitante        

        if ($ciclo_leitura == "2") { // ciclo semanal

            $dia_semana_loop = $dia_semana;

            if ($dia_semana_loop == "") { // valida a variavel do dia da semana

                $mensagem = array('codigo' => 0, 'mensagem' => 'A Recorrência é Semanal, mas, você ainda não selecionou os dias da Semana que será realizada a Tarefa!');

                echo json_encode($mensagem);

                exit;
            }


            // pego os dias da semana e salvo
            for ($i = 0; $i < count($dia_semana_loop); $i++) {

                $carimbo = $conexao->prepare("INSERT INTO periodo_dia_ponto (id_periodo_ponto,id_ponto, id_parametro, dia_semana) VALUES (?,?,?,?)");
                $carimbo->bindValue(1, $ultimo_id_tarefa, PDO::PARAM_STR);
                $carimbo->bindValue(2, $id_ponto, PDO::PARAM_STR);
                $carimbo->bindValue(3, $id_parametro, PDO::PARAM_STR);
                $carimbo->bindValue(4, $dia_semana_loop[$i], PDO::PARAM_STR);

                $carimbo->execute();
            }
        } //== fecha loop dos dias da semana, caso haja ciclo semanal

if($tipo_checkin=="tarefa_agendada"){//delegada

    $sql_projeto = $conexao->query("SELECT nome_obra FROM obras WHERE id_obra ='$projeto_tarefa'");
    $res_projeto = $sql_projeto->fetch(PDO::FETCH_ASSOC);
    $nome_projeto = $res_projeto['nome_obra'];
    


// caso seja uma tarefa delegada, notifica o usuário que irá realiza-la:
        $id_obra = $projeto_tarefa;
        $chave_unica = $chave_unica;
        $categoria_suporte = $id_tipo_suporte; // não há categoria de suporte neste caso, por ser uma tarefa concluída
        $mensagem_alerta = "Olá, aqui é o STEP. Uma nova Tarefa, foi criada para o Projeto: <b>$nome_projeto</b>:<br><br>Título: <b>$titulo_tarefa</b>.<br> Com os detalhes de Monitoramento ativos para o usuário solicitante:<b>$nome_solicitante </b><br><br> Sua Coleção de Alertas, foi ativada para esta Tarefa: <br>$alertas <br>";
        $assunto = 'STEP - Nova Tarefa Delegada foi Criada!';
        $retorno_alerta = "<br>Acompanhe e Monitore os detalhes em seu Dashboard!";	

        ob_start(); 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/suporte/gestao-alertas.php';
        $json_externo = ob_get_clean(); // Captura do buffer de saída
        // Decodificar o JSON para um array PHP
    $mensagem_externa = json_decode($json_externo);
   
    }elseif($tipo_checkin=="ponto_plcode"){

        
    $sql_projeto = $conexao->query("SELECT nome_obra FROM obras WHERE id_obra ='$projeto_tarefa'");
    $res_projeto = $sql_projeto->fetch(PDO::FETCH_ASSOC);
    $nome_projeto = $res_projeto['nome_obra'];


        $sql_plcode = $conexao->query("SELECT nome_ponto FROM pontos_estacao WHERE id_ponto ='$id_ponto'");
        $res_plcode = $sql_plcode->fetch(PDO::FETCH_ASSOC);
        $nome_ponto = $res_plcode['nome_ponto'];

             
// notificação se for Tarefa para Plcodes/Instrumentos:
    $id_obra = $projeto_tarefa;
    $chave_unica = $chave_unica;
    $categoria_suporte = $id_tipo_suporte; // não há categoria de suporte neste caso, por ser uma tarefa concluída
    $mensagem_alerta = "Olá, aqui é o STEP. Uma nova Tarefa Presencial, foi criada para o Projeto: <b>$nome_projeto</b>:<br>Título: <b>$titulo_tarefa</b>.<br> Com os detalhes de Monitoramento ativos para o usuário solicitante:<b>$nome_solicitante </b><br><br> Coleção de Alertas para esta Tarefa: <br><br>$alertas <br><br>";
    $mensagem_alerta .= "<br> O Ponto de Leitura: <b>$nome_ponto</b> foi Designado para esta Tarefa!<br><br>Acompanhe os detalhes em seu Dashboard!<br>";
    $assunto = 'STEP - Nova Tarefa por PlCode!';
    $retorno_alerta = "<br>Monitore e Acompanhe a Tarefa em seu Dashboard.";	

    ob_start(); 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/suporte/gestao-alertas.php';
    $json_externo = ob_get_clean(); // Captura do buffer de saída
    // Decodificar o JSON para um array PHP
$mensagem_externa = json_decode($json_externo);

    }elseif($tipo_checkin=="ponto_parametro"){

                
    $sql_projeto = $conexao->query("SELECT nome_obra FROM obras WHERE id_obra ='$projeto_tarefa'");
    $res_projeto = $sql_projeto->fetch(PDO::FETCH_ASSOC);
    $nome_projeto_email = $res_projeto['nome_obra'];

    $sql_plcode = $conexao->query("SELECT nome_ponto FROM pontos_estacao WHERE id_ponto ='$id_ponto'");
    $res_plcode = $sql_plcode->fetch(PDO::FETCH_ASSOC);
    $nome_ponto = $res_plcode['nome_ponto'];



        $sql_indicador = $conexao->query("SELECT nome_parametro FROM parametros_ponto WHERE id_parametro ='$id_parametro'");
        $res_indicador = $sql_indicador->fetch(PDO::FETCH_ASSOC);

        $nome_parametro = $res_indicador['nome_parametro'];

 // notificação se for Tarefa para ponto_parametros/Indicadores:
    $id_obra = $projeto_tarefa;
    $chave_unica = $chave_unica;
    $categoria_suporte = $id_tipo_suporte; // não há categoria de suporte neste caso, por ser uma tarefa concluída
    $mensagem_alerta = "Olá, aqui é o STEP.<br> Uma nova Tarefa com controle de PlCode e Indicador (Parâmetro), <br>foi criada para o Projeto: <b>$nome_projeto_email</b>:<br>Título: <b>$titulo_tarefa</b>.<br><br> O Ponto de Leitura: <b>$nome_ponto</b><br><br>O Indicador para leitura:<b> $nome_parametro </b>,<br> foram Designados para esta Tarefa.<br><br> Os detalhes de Monitoramento foram ativados para o usuário solicitante:<b>$nome_solicitante </b><br><br> Coleção de Alertas, para esta Tarefa:<br> <br>$alertas <br><br>";
    $mensagem_alerta .= "<br> O Ponto de Leitura: <b>$nome_ponto</b><br> <br>Acompanhe os detalhes em seu Dashboard!<br>";
    $assunto = 'STEP - Nova Tarefa por Indicador!';
    $retorno_alerta = "<br>Monitore e Acompanhe a Tarefa em seu Dashboard.";	
    ob_start(); 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/suporte/gestao-alertas.php';
    $json_externo = ob_get_clean();
    // Decodificar o JSON para um array PHP
$mensagem_externa = json_decode($json_externo);

    }

// O primeiro retorno já é uma string JSON


// O segundo retorno também já é uma string JSON
$retorno = array('codigo' => 1, 'mensagem' => 'Tarefa Criada com Sucesso!');
$retorno_Tarefa = json_encode($retorno);

// Decodificá-los para arrays PHP
$array1 = json_decode($json_externo, true);  // Decodifica para um array associativo
$array2 = json_decode($retorno_Tarefa, true);  // Decodifica para um array associativo

// Agregar os dois arrays em um único array
$resposta_agregada = array(
     $array1,
    $array2
);

// Codificar o array agregado como JSON
$json_agregado = json_encode($resposta_agregada);

// Agora você pode enviar $json_agregado como sua resposta JSON



// Agora, você pode enviar $json_agregado como sua resposta JSON
//echo $json_agregado;

// Enviar como um único objeto JSON
echo json_encode($resposta_agregada);


        exit;
    } else {

        $mensagem = array('codigo' => 0,  'mensagem' => 'Falha SQL ao Tentar Criar a nova Tarefa');


        echo json_encode($mensagem);

        exit;
    }
}





if ($acao == "alterar") {


    if($titulo_tarefa =='' || $titulo_tarefa == null){
        $retorno = array('codigo' => 0, 'mensagem' => 'Você precisa definir o Título da Tarefa.');
         // Codificar a resposta final como JSON e envolvê-la em um array
         $json_final = json_encode(array($retorno));
    
         // Agora você pode enviar $json_final como sua resposta JSON
         echo $json_final;
        exit;
    }

    $stmt = $conexao->prepare("SELECT id_estacao FROM pontos_estacao WHERE id_obra=:projeto_tarefa");
    $stmt->execute([':projeto_tarefa' => $projeto_tarefa]);
    
    $rt = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_estacao = $rt['id_estacao'] ?? '';
    
    
    if ($id_estacao!="") {

        $mensagem_externa ='';
        $json_externo='';

        $alertas = '';

        $id_tipo_suporte = '108'; // id_tipo_suporte = 111 (Tarefa Delegada em Andamento) - nova tarefa

        if ($monitora_tarefa == '1') {


            if ($alerta_email == "" && $alerta_sms == '' && $alerta_whats == '') { // valida a variavel 

                $mensagem = array('codigo' => 0, 'mensagem' => 'Você pediu para Monitorar a Tarefa, mas, não escolheu os meios de comunicação!');

                echo json_encode($mensagem);

                exit;
            }


            $alertas = '';

            $sql_alerta = $conexao->query("SELECT id_notificacao_usuario FROM notificacoes_usuario 
            WHERE id_usuario ='$usuario_solicitante' AND id_obra = '$projeto_tarefa' AND id_tipo_suporte = '$id_tipo_suporte'");
            $conta_alerta = $sql_alerta->rowCount();

            if ($conta_alerta > 0) {

                $row = $sql_alerta->fetch(PDO::FETCH_OBJ);
                $id_notificacao_usuario = $row->id_notificacao_usuario;
              

                $sql_altera_alerta = $conexao->query("UPDATE notificacoes_usuario  
            SET alerta_email='$alerta_email', alerta_sms='$alerta_sms', alerta_whats='$alerta_whats', status_notificacao_usuario='$monitora_tarefa'
            WHERE id_notificacao_usuario ='$id_notificacao_usuario'");

                if ($sql_altera_alerta) {
                    $alertas .= '<br><br> Acompanhamento da Tarefa para : <strong>' . $nome_solicitante . '</strong>:';

                    if($alerta_email == '1'){
                        $alertas .= '<br>  <small>Alerta por E-mail: <strong>Ativo</strong></small>';
                    }else{                      
                        $alertas .= '<br>  <small>Alerta por E-mail: <strong>Inativo</strong></small>';
                    }
                    if($alerta_sms == '1'){
                        $alertas .= '<br>  <small>Alerta por SMS: <strong>Ativo</strong></small>';
                    }else{
                        $alertas .= '<br>  <small>Alerta por SMS: <strong>Inativo</strong></small>';
                    }
                    if($alerta_whats == '1'){
                        $alertas .= '<br>  <small>Alerta por WhatsApp: <strong>Ativo</strong></small>';
                    }else{
                        $alertas .= '<br>  <small>Alerta por WhatsApp: <strong>Inativo</strong></small>';
                    }

                    $alertas .= '<br>  <small>Monitoramento da Tarefa: <strong>Ativo</strong></small>';

                    $alertas .= '<br>  <small>Periodo de Verificação: <strong>5 minutos</strong></small>';

                    $alertas .= '<br>  <small>Alertas Atualizados com sucesso!</small>';

                }
            } else {

                try {
                    $sql_novo_alerta = "INSERT INTO notificacoes_usuario 
                    (id_usuario, id_obra, id_tipo_suporte, alerta_email, alerta_sms, alerta_whats, periodo_verificacao, status_notificacao_usuario) 
                    VALUES (:usuario_solicitante, :projeto_tarefa, :id_tipo_suporte, :alerta_email, :alerta_sms, :alerta_whats, :periodo_verificacao, :monitora_tarefa)";
                
                    $stmt = $conexao->prepare($sql_novo_alerta);
                
                    $stmt->bindParam(':usuario_solicitante', $usuario_solicitante);
                    $stmt->bindParam(':projeto_tarefa', $projeto_tarefa);
                    $stmt->bindParam(':id_tipo_suporte', $id_tipo_suporte);
                    $stmt->bindParam(':alerta_email', $alerta_email);
                    $stmt->bindParam(':alerta_sms', $alerta_sms);
                    $stmt->bindParam(':alerta_whats', $alerta_whats);
                    $stmt->bindParam(':periodo_verificacao', $periodo_verificacao);
                    $stmt->bindParam(':monitora_tarefa', $monitora_tarefa);
                
                    $stmt->execute();
                
                    $alertas .= '<br> Alertas Incluídos com sucesso!';
                } catch(PDOException $e) {
                    error_log("Erro ao inserir novo alerta: " . $e->getMessage());
                    throw new Exception('Erro ao inserir novo alerta.');
                }
                
            }
        }



        
// caso seja uma tarefa delegada, notifica o usuário que irá realiza-la:
//**** modulo de gestao de alertas >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 

$json_externo='';
    function executeGestaoAlertas(
        int $id_obra,
        string $categoria_suporte,
        string $mensagem_alerta,
        string $assunto,
        string $retorno_alerta
    ): void {
        // Tornando as variáveis acessíveis para o arquivo incluído
        global $id_obra, $categoria_suporte, $mensagem_alerta, $assunto, $retorno_alerta;
    
        // Incluindo o arquivo
        ob_start(); 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/suporte/gestao-alertas.php';
        $json_externo = ob_get_clean(); // Captura do buffer de saída
        // Decodificar o JSON para um array PHP
         $mensagem_externa = json_decode($json_externo);
    }

// Invoca Gestão de Alertas, para conferir os usuários envolvidos no Projeto e suas regras de alerta personalizadas.
        $id_obra = $projeto_tarefa;
        $chave_unica = $chave_unica;
        $categoria_suporte = $id_tipo_suporte; // não há categoria de suporte neste caso, por ser uma tarefa concluída
        $mensagem_alerta = "Olá, aqui é o STEP,<br> te avisando que a Tarefa com Título: <b>$titulo_tarefa</b> foi alterada! Confira detalhes acessando o Sistema.";
        $assunto = "A Tarefa {$titulo_tarefa}, foi alterada!";
        $retorno_alerta = $chave_unica;

        executeGestaoAlertas($id_obra, $categoria_suporte, $mensagem_alerta, $assunto, $retorno_alerta);


//**** fim modulo de gestao de alertas <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 



// Preparar a consulta SQL para atualizar a Tarefa
$sql = "UPDATE `periodo_ponto` SET 
        `titulo_tarefa` = :titulo_tarefa, 
        `id_obra` = :id_obra, 
        `usuario_tarefa` = :usuario_tarefa, 
        `usuario_solicitante` = :usuario_solicitante, 
        `detalhes_tarefa` = :detalhes_tarefa, 
        `monitora_tarefa` = :monitora_tarefa,
        `tags` = :tags,
        `data_tarefa` = :data_tarefa,
        `id_estacao` = :id_estacao,
        `id_ponto` = :id_ponto,
        `id_parametro` = :id_parametro,
        `hora_leitura` = :hora_leitura,
        `ciclo_leitura` = :ciclo_leitura,
        `tipo_checkin` = :tipo_checkin,
        `status_periodo` = :status_periodo,
        `modo_checkin_periodo` = :modo_checkin_periodo       
        WHERE `id_periodo_ponto` = :id_periodo_ponto";

// Preparar o statement
$stmt = $conexao->prepare($sql);

// Vincular parâmetros
$stmt->bindParam(':id_periodo_ponto', $id_tarefa);
$stmt->bindParam(':titulo_tarefa', $titulo_tarefa);
$stmt->bindParam(':id_obra', $id_obra);
$stmt->bindParam(':usuario_tarefa', $usuario_tarefa);
$stmt->bindParam(':usuario_solicitante', $usuario_solicitante);
$stmt->bindParam(':detalhes_tarefa', $detalhes_tarefa);
$stmt->bindParam(':monitora_tarefa', $monitora_tarefa);
$stmt->bindParam(':tags', $tags);
$stmt->bindParam(':data_tarefa', $data_tarefa);
$stmt->bindParam(':id_estacao', $id_estacao);
$stmt->bindParam(':id_ponto', $id_ponto);
$stmt->bindParam(':id_parametro', $id_parametro);
$stmt->bindParam(':hora_leitura', $hora_leitura);
$stmt->bindParam(':ciclo_leitura', $ciclo_leitura);
$stmt->bindParam(':tipo_checkin', $tipo_checkin);
$stmt->bindParam(':status_periodo', $status_periodo);
$stmt->bindParam(':modo_checkin_periodo', $modo_checkin_periodo);

// Executar a consulta
if ($stmt->execute()) {


$json_externo = $json_externo;

// Inicializa um array vazio para a resposta agregada
$resposta_agregada = array();

$retorno = array('codigo' => 1, 'mensagem' => 'Tarefa Alterada com Sucesso!');
$retorno_Tarefa = json_encode($retorno);

// Decodificar os objetos JSON para arrays PHP
$array1 = json_decode($json_externo, true);  // Decodifica para um array associativo
$array2 = json_decode($retorno_Tarefa, true);  // Decodifica para um array associativo

// Inicializar uma variável para a resposta final
$resposta_final = null;

// Verificar se os arrays são null e atribuir à resposta final
if ($array1 !== null) {
    $resposta_final = $array1;
}
if ($array2 !== null) {
    $resposta_final = $array2;
}

// Se ambos forem null, você pode definir uma resposta padrão ou manter como null
if ($resposta_final === null) {
    $resposta_final = array('codigo'=>'0','mensagem' => 'Nenhuma resposta válida recebida');
}

$json_final = json_encode(array($resposta_final));

// Codificar a resposta final como JSON
//$json_final = json_encode($resposta_final);

// Agora você pode enviar $json_final como sua resposta JSON
echo $json_final;
 exit;
}else{  $mensagem = array('codigo' => 0,  'mensagem' => 'Existem Campos Obrigatórios em seu formulário que não foram completamente preenchidos, revise e tente novamente, por gentileza.');


    echo json_encode($mensagem);

    exit; }
    } else {

        $mensagem = array('codigo' => 0,  'mensagem' => 'Falha SQL ao Tentar Criar a nova Tarefa');


        echo json_encode($mensagem);

        exit;
    }
}


if ($acao == "conclui_tarefa") {

    $latitude_operador = $_POST['latitude'];
    $longitude_operador = $_POST['longitude'];

    $id_tipo_suporte = '110';

    $sql_dados_tarefa = $conexao->query("SELECT * FROM periodo_ponto WHERE id_periodo_ponto ='$id_tarefa'");
    $rt = $sql_dados_tarefa->fetch(PDO::FETCH_ASSOC);

    $titulo_tarefa = $rt['titulo_tarefa'] ?? 'Tarefa sem Identificação';
    $tipo_checkin = $rt['tipo_checkin'];
    $modo_checkin_periodo = $rt['modo_checkin_periodo'];
    $usuario_tarefa = $rt['usuario_tarefa'];
    $data_tarefa = $rt['data_tarefa'];
    $id_obra = $rt['id_obra'];
    $id_estacao = $rt['id_estacao'];
    $id_ponto = $rt['id_ponto'];
    $id_parametro = $rt['id_parametro'];
   

    $hora_leitura = $rt['hora_leitura'];
    $ciclo_leitura = $rt['ciclo_leitura'];

    $monitora_tarefa = $rt['monitora_tarefa'];

    $hora_lida = date('H:i:s'); // Hora atual no formato 'HH:mm:ss'
    $data_cadastro_checkin = date('Y-m-d H:i:s'); // Data atual no formato 'YYYY-MM-DD HH:mm:ss'
    $status_checkin = '5'; // 5 = Em Análise (será validado pela API de checkin)


    $sql = $conexao->query("UPDATE periodo_ponto SET status_periodo='3' WHERE id_periodo_ponto ='$id_tarefa'");  //	status_tarefa = (1) ativo, (2) inativa, (3) concluída

    $count = $sql_dados_tarefa->rowCount();

    if ($count > 0) {


        // Preparando a consulta SQL
        $sql = "INSERT INTO checkin (tipo_checkin, modo_checkin,  id_periodo_ponto, latitude_operador, longitude_operador,  id_obra, id_estacao, id_ponto, id_parametro, id_colaborador, hora_leitura, hora_lida, chave_unica, status_checkin, data_cadastro_checkin)
VALUES (:tipo_checkin, :modo_checkin,  :id_periodo_ponto, :latitude_operador, :longitude_operador,  :id_obra, :id_estacao, :id_ponto, :id_parametro, :id_colaborador, :hora_leitura, :hora_lida, :chave_unica, :status_checkin, :data_cadastro_checkin)";

        // Preparando a consulta com o objeto PDO
        $stmt = $conexao->prepare($sql);

        // Vinculando os valores às variáveis



switch ($tipo_checkin) {
   case "ponto_plcode":
      $tipo_checkin = "1";
      break;
   case "ponto_parametro":
      $tipo_checkin = "2";
      break;
    case "tarefa_agendada":
       $tipo_checkin = "3";
      break;
   default:
      $tipo_checkin = "0";
      break;
}




        $stmt->bindParam(':tipo_checkin', $tipo_checkin);
        $stmt->bindParam(':modo_checkin', $modo_checkin_periodo);
        $stmt->bindParam(':id_periodo_ponto', $id_tarefa);
        $stmt->bindParam(':latitude_operador', $latitude_operador);
        $stmt->bindParam(':longitude_operador', $longitude_operador);
        $stmt->bindParam(':id_obra', $id_obra);
        $stmt->bindParam(':id_estacao', $id_estacao);
        $stmt->bindParam(':id_ponto', $id_ponto);
        $stmt->bindParam(':id_parametro', $id_parametro);
        $stmt->bindParam(':id_colaborador', $usuario_tarefa);
        $stmt->bindParam(':hora_leitura', $hora_leitura);
        $stmt->bindParam(':hora_lida', $hora_lida);
        $stmt->bindParam(':chave_unica', $chave_unica);
        $stmt->bindParam(':status_checkin', $status_checkin);
        $stmt->bindParam(':data_cadastro_checkin', $data_cadastro_checkin);

        $ultimo_id_checkin = $conexao->lastInsertId();


        // Executando a consulta
        if ($stmt->execute()) {


              
                            /* >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Gera Log */
                            $acao_log = "Tarefa";
                            $tipo_log = '53'; // Tarefa Concluída
                            $id_acao_log = $ultimo_id_checkin;
                            $acao_completa = "O Usuário " . $_SESSION['nome'] . " Concluiu a Tarefa " . $titulo_tarefa . " com sucesso!";

                            $sql_log = "INSERT INTO log_leitura
                            (chave_unica,
                            id_usuario,
                            acao,
                            acao_log,
                            id_acao_log,
                            estacao_logada,
                            tipo_log
                            ) VALUES (
                            :chave_unica,
                            :id_usuario,
                            :acao,
                            :acao_log,
                            :id_acao_log,
                            :estacao_logada,
                            :tipo_log)";
                            $stmt = $conexao->prepare($sql_log);
                            $stmt->bindParam(':chave_unica', $chave_unica, PDO::PARAM_STR);
                            $stmt->bindParam(':id_usuario', $_SESSION['id'], PDO::PARAM_INT);
                            $stmt->bindParam(':acao', $acao_completa, PDO::PARAM_STR);
                            $stmt->bindParam(':acao_log', $acao_log, PDO::PARAM_STR);
                            $stmt->bindParam(':id_acao_log', $id_acao_log, PDO::PARAM_INT);
                            $stmt->bindParam(':estacao_logada', $id_estacao, PDO::PARAM_INT);
                            $stmt->bindParam(':tipo_log', $tipo_log, PDO::PARAM_INT);
                            $stmt->execute();

                            /* Fecha Log <<<<<<<<<<<<<<<<<<<<<<<  Gera Log */                                             
   
    
                         
            // verifica se o usuário pediu para ser notificado, caso sim, chama gestao alertas personalizado:

            if ($monitora_tarefa == '1') {

                $id_obra = $id_obra;
                $chave_unica = $chave_unica;
                $categoria_suporte = $id_tipo_suporte; // não há categoria de suporte neste caso, por ser uma tarefa concluída
                $mensagem_alerta = "Olá, aqui é o STEP, te avisando que a Tarefa <b>$titulo_tarefa</b>, foi concluída com sucesso!";
                $retorno_alerta = "Projeto: $nome_projeto <br> Título: $titulo_tarefa <br><br> Com os detalhes de Monitoramento ativos para o usuário solicitante:<b>$nome_solicitante </b><br> <br> <br> <a href='https://step.eco.br/'>Acesse o STEP</a> para acompanhar os detalhes da Tarefa!";	
                $assunto = 'Tarefa Concluída';
                ob_start(); 
                require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/suporte/gestao-alertas.php';
                $json_externo = ob_get_clean(); // Captura do buffer de saída
                // Decodificar o JSON para um array PHP
                 $mensagem_externa = json_decode($json_externo);

            }

           

$json_externo = $json_externo;

// Inicializa um array vazio para a resposta agregada
$resposta_agregada = array();

$retorno = array('codigo' => 1, 'mensagem' => 'Tarefa Concluída com Sucesso!');
$retorno_Tarefa = json_encode($retorno);

// Decodificar os objetos JSON para arrays PHP
$array1 = json_decode($json_externo, true);  // Decodifica para um array associativo
$array2 = json_decode($retorno_Tarefa, true);  // Decodifica para um array associativo

// Inicializar uma variável para a resposta final
$resposta_final = null;

// Verificar se os arrays são null e atribuir à resposta final
if ($array1 !== null) {
    $resposta_final = $array1;
}
if ($array2 !== null) {
    $resposta_final = $array2;
}

// Se ambos forem null, você pode definir uma resposta padrão ou manter como null
if ($resposta_final === null) {
    $resposta_final = array('codigo'=>'0','mensagem' => 'Nenhuma resposta válida recebida');
}

$json_final = json_encode($resposta_final);

// Codificar a resposta final como JSON
//$json_final = json_encode($resposta_final);

// Agora você pode enviar $json_final como sua resposta JSON
echo $json_final;
$conexao = null;
exit;
 } else {
            $mensagem = array('codigo' => 0,  'mensagem' => 'Falha SQL ao Tentar Salvar sua Tarefa');

            $json_final = json_encode(array($mensagem));
            echo json_encode($json_final);
           
            $conexao = null;
            exit;
        }
    }else{
        $mensagem = array('codigo' => 0,  'mensagem' => 'Houve um Erro ao Localizar a Tarefa SOlicitada - SQL Error');
        $json_final = json_encode(array($mensagem));
        echo json_encode($json_final);
        $conexao = null;
        exit;


    }
}
