<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('America/Sao_Paulo');

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/conexao.php';
$conexao = Conexao::getInstance();
//validateHeader();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtenção da ação
$acao = $_POST['tipo_tarefa'] ?? 'default';

require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/leituras/funcoes-tarefas.php';
// Validação de dados
if (!validarAcao($acao)) {
    // Tratar erro
}

// Execução com base na ação
switch ($acao) {
    case 'ponto_plcode':
        executarPontoPlcode($conexao);
        break;
    case 'ponto_parametro':
        executarPontoParametro($conexao);
        break;
    case 'tarefa_agendada':
        executarTarefaAgendada($conexao);
        break;
    default:
        // Código para ação desconhecida ou padrão
        break;
}

// Funções
function executarPontoPlcode($conexao)
{
    // Código específico para ponto_plcode


    // Inicialize as variáveis com valores padrão
    $id_obra = $plcode_lido = $tipo_tarefa = $id_tarefa = $id_estacao = $usuario_tarefa = $chave_unica = $latitude_user = $longitude_user = null;


    // Inicializar um array para armazenar mensagens de erro
    $erros = [];

    // Array com os nomes das variáveis POST que são obrigatórias
    $variaveisObrigatorias = [
        'id_obra', 'plcode_lido', 'tipo_tarefa',
        'id_tarefa', 'id_estacao', 'usuario_tarefa', 'chave_unica',
        'latitude_user', 'longitude_user', 'texto_tarefa_presencial'
    ];

    // Loop para verificar cada variável
    foreach ($variaveisObrigatorias as $variavel) {
        if (!isset($_POST[$variavel]) || trim($_POST[$variavel]) === '') {
            // Adicionar uma mensagem de erro personalizada para a variável ausente ou vazia
            $erros[] = "O campo $variavel é obrigatório.";
        }
    }

    // Verificar se há erros
    if (!empty($erros)) {
        // Retornar os erros como um JSON
        echo json_encode(['status' => 'erro', 'mensagem' => $erros]);
        exit();
    }




    // Se não houver erros, processo o formulário e retorno uma mensagem de sucesso
    $id_obra = $_POST['id_obra'];
    $plcode_lido = $_POST['plcode_lido'];
    $tipo_tarefa = $_POST['tipo_tarefa'];
    $id_tarefa = $_POST['id_tarefa'];
    $id_estacao = $_POST['id_estacao'];
    $usuario_tarefa = $_POST['usuario_tarefa'];
    $chave_unica = $_POST['chave_unica'];
    $latitude_user = $_POST['latitude_user'];
    $longitude_user = $_POST['longitude_user'];
    $texto_tarefa_presencial = $_POST['texto_tarefa_presencial'] ?? 'não informado';
    $hora_lida = date('H:i:s');
    $parametro_lido = '';
    //===================================================================================================
    //** tratar erro de retorno na tentativa de inclusao da leitura e do log  *//

    //** Modo Checkin	1 = Livre , 2 = Horário Controlado (Agendado) *//
    $modo_checkin_periodo = getModoCheckinPeriodo($conexao, $id_tarefa);

    if (is_null($modo_checkin_periodo)) {
        $retorno  = array('status' => 0, 'mensagem' => "Não foi possível buscar o modo de checkin e Tarefa do período.");
        echo json_encode($retorno);
        exit;
    }

    $hora_leitura_agendada = getHoraLeituraAgendada($conexao, $id_tarefa);

    if ($hora_leitura_agendada !== false) {
        // "Hora de leitura agendada é: $hora_leitura_agendada";
    } else {
        $hora_leitura_agendada = '';
    }



    $tipo_checkin = '1'; //	0 livre 1 para presencial e 2 para leitura de rmm, 3 tarefa agendada	

    $result = gravarCheckin(
        $conexao,
        $tipo_checkin,
        $modo_checkin_periodo,
        $id_tarefa,
        $latitude_user,
        $longitude_user,
        $id_obra,
        $id_estacao,
        $plcode_lido,
        $usuario_tarefa,
        $hora_leitura_agendada,
        $hora_lida,
        $chave_unica,
        $texto_tarefa_presencial
    );

    // Verificar o resultado da inserção
    if ($result) {

        $acao_log = 'Tarefa Presencial Completada com Sucesso - Tarefa ID: ' . $id_tarefa . ' Texto Presencial: ' . $texto_tarefa_presencial;

        insertIntoLogLeitura($conexao, $usuario_tarefa, $chave_unica, $id_estacao, $acao_log);

        $retorno  = array('status' => 1, 'mensagem' => "Sua Tarefa Presencial, foi Concluída com Sucesso!");
        echo json_encode($retorno);

        exit;
    } else {

        $retorno  = array('status' => 0, 'mensagem' => "Falha na gravação da Tarefa!");
        echo json_encode($retorno);
        exit;
    }
}


function executarPontoParametro($conexao)
{
    // Código específico para ponto_parametro

    // Inicialize as variáveis com valores padrão
    $id_obra = $valorLido = $parametro_lido = $plcode_lido = $tipo_tarefa = $id_tarefa = $id_estacao = $usuario_tarefa = $chave_unica = $latitude_user = $longitude_user = null;


    // Inicializar um array para armazenar mensagens de erro
    $erros = [];

    // Array com os nomes das variáveis POST que são obrigatórias
    $variaveisObrigatorias = [
        'id_obra', 'valor_lido', 'parametro_lido', 'plcode_lido', 'tipo_tarefa',
        'id_tarefa', 'id_estacao', 'usuario_tarefa', 'chave_unica',
        'latitude_user', 'longitude_user'
    ];

    // Loop para verificar cada variável
    foreach ($variaveisObrigatorias as $variavel) {
        if (!isset($_POST[$variavel]) || trim($_POST[$variavel]) === '') {
            // Adicionar uma mensagem de erro personalizada para a variável ausente ou vazia
            $erros[] = "O campo $variavel é obrigatório.";
        }
    }

    // Validação adicional para o campo valor_lido
    if (isset($_POST['valor_lido']) && !filter_var($_POST['valor_lido'], FILTER_VALIDATE_FLOAT)) {
        $erros[] = "O campo valor_lido deve ser um número decimal válido.";
    }

    // Verificar se há erros
    if (!empty($erros)) {
        // Retornar os erros como um JSON
        echo json_encode(['status' => 'erro', 'mensagem' => $erros]);
        exit();
    }

    // Se não houver erros, processo o formulário e retorno uma mensagem de sucesso
    $valorLido = $_POST['valor_lido'];
    $parametro_lido = $_POST['parametro_lido'];
    $plcode_lido = $_POST['plcode_lido'];
    $tipo_tarefa = $_POST['tipo_tarefa'];
    $id_obra = $_POST['id_obra'];
    $id_tarefa = $_POST['id_tarefa'];
    $id_estacao = $_POST['id_estacao'];
    $usuario_tarefa = $_POST['usuario_tarefa'];
    $chave_unica = $_POST['chave_unica'];
    $latitude_user = $_POST['latitude_user'];
    $longitude_user = $_POST['longitude_user'];
    $hora_lida = date('H:i:s');
    //===================================================================================================
    //** tratar erro de retorno na tentativa de inclusao da leitura e do log  *//



    $hora_leitura_agendada = getHoraLeituraAgendada($conexao, $id_tarefa);

    if ($hora_leitura_agendada !== false) {
        // "Hora de leitura agendada é: $hora_leitura_agendada";
    } else {
        $hora_leitura_agendada = '';
    }

    $result = fetchRoAndSupervisor($conexao, $plcode_lido);
    $ro = '';
    $supervisor = '';
    // Verifica se a função retornou um resultado válido
    if ($result !== false) {
        $ro = $result['ro'];
        $supervisor = $result['supervisor'];

        // Agora você pode usar $ro e $supervisor como quiser
    } else {
        // A função retornou false, então você pode tratar isso como um erro ou situação especial
        // Por exemplo, você pode definir $ro e $supervisor como vazios
        $ro = '';
        $supervisor = '';
        error_log("Não foi possível buscar RO e Supervisor para o PLCode $plcode_lido");
    }

    $info = fetchRoAndSupervisor($conexao, $plcode_lido);

    if (!$info) {
        $retorno  = array('status' => 0, 'mensagem' => "Impossível Prosseguir, Não Foram Localizados RO e Supervisor, Responsáveis pelo Núcleo Atual.");
        echo json_encode($retorno);
        exit;
    }

    $inserted =  insertIntoRmm($conexao, $id_obra, $chave_unica, $plcode_lido, $usuario_tarefa, $latitude_user, $longitude_user, $parametro_lido, $valorLido, $ro, $supervisor);

    if ($inserted) {
        $acao_log = 'Nova Tarefa de Leitura realizada com Sucesso - Tarefa ID: ' . $id_tarefa;
        insertIntoLogLeitura($conexao, $acao_log, $usuario_tarefa, $chave_unica, $id_estacao);
    }

    if (!$inserted) {
        $retorno  = array('status' => 0, 'mensagem' => "Não foi possível inserir a leitura.");
        echo json_encode($retorno);
        exit;
    }
    //** Modo Checkin	1 = Livre , 2 = Horário Controlado (Agendado) *//
    $modo_checkin_periodo = getModoCheckinPeriodo($conexao, $id_tarefa);

    if (is_null($modo_checkin_periodo)) {
        $retorno  = array('status' => 0, 'mensagem' => "Não foi possível buscar o modo de checkin e Tarefa do período.");
        echo json_encode($retorno);
        exit;
    }

    // ultimo id inserido na tabela rmm
    $lastInsertedId = insertIntoRmm(
        $conexao,
        $id_obra,
        $chave_unica,
        $plcode_lido,
        $usuario_tarefa,
        $latitude_user,
        $longitude_user,
        $parametro_lido,
        $valorLido,
        $ro,
        $supervisor
    );

    if ($lastInsertedId !== false) {
        //echo "Inserção bem-sucedida. O último ID inserido é: $lastInsertedId";
    } else {
        // echo "Falha na inserção.";
    }

    $tipo_checkin = '2'; //	0 livre 1 para presencial e 2 para leitura de rmm, 3 tarefa agendada	
    $result = insertIntoCheckin(
        $conexao,
        $tipo_checkin,
        $modo_checkin_periodo,
        $lastInsertedId,
        $id_tarefa,
        $latitude_user,
        $longitude_user,
        $id_obra,
        $id_estacao,
        $plcode_lido,
        $parametro_lido,
        $usuario_tarefa,
        $hora_leitura_agendada,
        $hora_lida,
        $chave_unica
    );

    // Verificar o resultado da inserção
    if ($result) {

        $retorno  = array('status' => 1, 'mensagem' => "Sua Tarefa de Leitura, foi Concluída com Sucesso!");
        echo json_encode($retorno);

        exit;
    } else {

        $retorno  = array('status' => 0, 'mensagem' => "Falha na gravação da Tarefa!");
        echo json_encode($retorno);
        exit;
    }
}

function executarTarefaAgendada()
{
    // Código específico para tarefa_agendada
}

function validarAcao($acao)
{
    // Validação da ação
    return in_array($acao, ['ponto_plcode', 'ponto_parametro', 'tarefa_agendada']);
}
