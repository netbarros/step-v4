<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('America/Sao_Paulo');

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/conexao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/valida-acesso-app.php';

/* id_plcode, id_suporte, motivo, id_usuario  saida do json CODIGO, MENSAGENS*/

$conexao = Conexao::getInstance();
//validateHeader();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$id_estacao = isset($_POST['estacao']) ? trim($_POST['estacao']) : '';
$acao = isset($_POST['acao']) ? trim($_POST['acao']) : '';


if ($id_estacao == '') {

    $retorno = ['codigo' => 0, 'mensagem' => 'Núcleo ausente na consulta.'];
    echo json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}


        
function intervalo($entrada, $saida)
{
    $entrada = explode(':', $entrada);
    $saida   = explode(':', $saida);
    
    // Verificar se ambos os arrays têm pelo menos dois elementos
    if (isset($entrada[0], $entrada[1], $saida[0], $saida[1])) {
        $minutos = ((int) $saida[0] - (int) $entrada[0]) * 60 + ((int) $saida[1] - (int) $entrada[1]);
        
        if ($minutos < 0) {
            $minutos += 24 * 60;
        }
        
        return sprintf('%dh%dmin', $minutos / 60, $minutos % 60);
    } else {
        // Retornar algum valor padrão ou lançar uma exceção
        return "Não aplicável";
    }
}


function processTags($tagsJson) {
    $tagsArray = json_decode($tagsJson, true);
    return $tagsArray ? implode(',', array_column($tagsArray, 'value')) : '';
}

$diasemana_numero = date('w');

function buscaTarefasPorDia($conexao, $id, $diasemana_numero) {
    // Remove o filtro de dia da semana na query
    $stmt = $conexao->prepare("SELECT periodo_dia_ponto.dia_semana,
                                      dia_semana.nome_dia_semana
                                FROM periodo_dia_ponto 
                                INNER JOIN dia_semana ON periodo_dia_ponto.dia_semana = dia_semana.representa_php 
                                WHERE periodo_dia_ponto.id_periodo_ponto = :id_periodo_Tarefa");

    $stmt->bindParam(':id_periodo_Tarefa', $id, PDO::PARAM_INT);
    $stmt->execute();

    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar se houve algum retorno
    if (empty($resultado)) {
        return [array("nome_dia_semana" => "Dia da Semana não encontrado")];
    }

    // Destacar o dia da semana atual, se estiver na lista
    foreach ($resultado as &$dia) {
        if ($dia['dia_semana'] == $diasemana_numero) {
            $dia['hoje'] = true;

        } else {
            $dia['hoje'] = false;
        }
    }

    return $resultado;
}


/*
Primeiro setamos que tipo de tarefa será analisada (tarefa_agendada (Tarefa Delegada), ponto_plcode (Tarefa Presencial), ponto_parametro (Tarefa de Leitura))
Sequencia é saber o ciclo de leitura se é unico, diário ou semanal
e depois se é com ou sem controle de horário
e depois se é com ou sem Plcode
e depois se é com ou sem parametro
e depois se é com ou sem sensor iot

            $tipo_check = '';      
            $dias_semana_periodo = '';
            $nome_dia_semana = '';
            status_tarefa = (1) ativo, (2) inativa, (3) concluída
*/




if ($acao == "ponto_plcode") { // presencial com plcode


    $sql_periodo = "SELECT 
    periodo_ponto.*,
    pontos_estacao.nome_ponto,
    periodo_ponto.id_ponto,
    periodo_ponto.tipo_checkin,
    pontos_estacao.controla_periodo_ponto,
    pontos_estacao.status_ponto,
    periodo_ponto.id_parametro,
    periodo_dia_ponto.dia_semana,
    dia_semana.nome_dia_semana,
    periodo_ponto.tags,
    checkin.id_colaborador
  
FROM periodo_ponto
INNER JOIN pontos_estacao ON pontos_estacao.id_ponto = periodo_ponto.id_ponto
LEFT JOIN checkin ON checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto
LEFT JOIN periodo_dia_ponto ON periodo_dia_ponto.id_periodo_ponto = periodo_ponto.id_periodo_ponto
LEFT JOIN dia_semana ON dia_semana.representa_php = periodo_dia_ponto.dia_semana

WHERE periodo_ponto.tipo_checkin = 'ponto_plcode'
AND periodo_ponto.id_estacao = :id_estacao AND pontos_estacao.status_ponto != '3' AND periodo_ponto.status_periodo != '3'
GROUP BY periodo_ponto.id_periodo_ponto ORDER BY periodo_ponto.id_periodo_ponto ASC

";
//AND periodo_ponto.status_periodo != '3'

    $stmt = $conexao->prepare($sql_periodo);
    $stmt->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);
    $stmt->execute();

    $total = $stmt->rowCount();

    if ($total > 0) {
// pega hora atual php
$hora_atual = date('H:i');
$data_atual_periodo = date_create()->format('Y-m-d');

// declara a array que será incrementada com os dados da tarefa
$taskDetailsArray = [];
$addedIds = [];
$hoje = '';

        foreach ($stmt as $res) {

            $id = $res['id_periodo_ponto'];

            
            $ciclo_leitura = $res['ciclo_leitura'];
            $controla_periodo = $res['modo_checkin_periodo'];
            $diasemana_numero = date('w');

            $data_tarefa_agendada = $res['data_tarefa'];

            if ($data_tarefa_agendada !== '') {
                $dateTimeObj = date_create($data_tarefa_agendada);
                
                if ($dateTimeObj === false) {
                    // Registro de erro ou alguma ação
                    error_log("Falha ao converter a data: $data_tarefa_agendada");
                } else {
                    $data_tarefa_agendada = date_format($dateTimeObj, 'd/m/Y');
                }
            }

            
$diasemana_numero = date('w');

// Uso da função
$nome_dia_semana_periodo = buscaTarefasPorDia($conexao, $id, $diasemana_numero);

// acessando os dias da semana pelo periodo informado, retornado pela chamada da funcao buscarTatefasPorDia
if (isset($nome_dia_semana_periodo[0]['nome_dia_semana'])) {
    $nome_dia_semana = $nome_dia_semana_periodo[0]['nome_dia_semana'];
    } else {
    $nome_dia_semana = array('Dia da semana não informado');
    }

    // Verifique se hoje é o dia certo para essa tarefa
    $hoje = '';
    $hoje = false;
    foreach ($nome_dia_semana_periodo as $dia) {
        if ($dia['hoje']) {
            $hoje = true;
            break;
        }
    }

            // trata o nome do usuário que realizou a tarefa ou que a recebeu como agendada
            $id_nome_user_check = $res['usuario_tarefa'] ?? '';
            $nome_user_check = '';
            
            if ($id_nome_user_check != '') {
                // Preparar a consulta SQL
                $stmt = $conexao->prepare("SELECT nome FROM usuarios WHERE id = :id");
                
                // Vincular parâmetros
                $stmt->bindParam(':id', $id_nome_user_check, PDO::PARAM_STR);
                
                // Executar a consulta
                if ($stmt->execute()) {
                    // Buscar resultados
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Verificar se algum resultado foi retornado
                    if ($result !== false) {
                        $nome_user_check = $result['nome'];
                    }
                } else {
                    // Aqui você pode lidar com erros na execução da consulta
                }
            } else {
                $nome_user_check = '';
            }


            // trata o usuário solicitante
                    $usuario_solicitante = $res['usuario_solicitante'] ?? '';
                    $nome_usuario_solicitante = '';

                    if ($usuario_solicitante != '') {
                        // Preparar a consulta SQL
                        $stmt_solicitante = $conexao->prepare("SELECT nome FROM usuarios WHERE id = :id");
                        
                        // Vincular parâmetros
                        $stmt_solicitante->bindParam(':id', $usuario_solicitante, PDO::PARAM_STR);
                        
                        // Executar a consulta
                        if ($stmt_solicitante->execute()) {
                            // Buscar resultados
                            $result_solicitante = $stmt_solicitante->fetch(PDO::FETCH_ASSOC);
                            
                            // Verificar se algum resultado foi retornado
                            if ($result_solicitante !== false) {
                                $nome_usuario_solicitante = $result_solicitante['nome'];
                            }
                        } else {
                            // Aqui você pode lidar com erros na execução da consulta
                        }
                    } else {
                        $nome_usuario_solicitante = '';
                    }


                // trata o usuário que realizou o checkin
                    $usuario_chekcin = $res['id_colaborador'] ?? '';
                    $nome_usuario_chekcin = '';

                    if ($usuario_chekcin != '') {
                        // Preparar a consulta SQL
                        $stmt_usuario_chekcin = $conexao->prepare("SELECT nome FROM usuarios WHERE id = :id");
                        
                        // Vincular parâmetros
                        $stmt_usuario_chekcin->bindParam(':id', $usuario_chekcin, PDO::PARAM_STR);
                        
                        // Executar a consulta
                        if ($stmt_usuario_chekcin->execute()) {
                            // Buscar resultados
                            $result_solicitante = $stmt_usuario_chekcin->fetch(PDO::FETCH_ASSOC);
                            
                            // Verificar se algum resultado foi retornado
                            if ($result_solicitante !== false) {
                                $nome_usuario_chekcin = $result_solicitante['nome'];
                            }
                        } else {
                            // Aqui você pode lidar com erros na execução da consulta
                        }
                    } else {
                        $nome_usuario_chekcin = '';
                    }
           

                    // trabalho com a funcao para que separe item a item das tags
                    $valorTag = processTags($res['tags'] ?? NULL);


        if ($ciclo_leitura == '0') {
            $ciclo = "único";


            if ($controla_periodo == "1") {  // livre sem controle de horario


                $sql_checa_checkin = "SELECT 
                checkin.id_periodo_ponto,
                checkin.id_colaborador,
                checkin.id_estacao,
                checkin.id_ponto,
                checkin.status_checkin,
                checkin.modo_checkin,
                checkin.hora_leitura,
                checkin.hora_lida,
                usuarios.nome
    
                FROM checkin
                INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
                WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
               
                AND checkin.id_estacao = :id_estacao 
                AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
                ";
                $sql_checa = $conexao->prepare($sql_checa_checkin);
                $sql_checa->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
                $sql_checa->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);
    
                $sql_checa->execute();
    
                $total = $sql_checa->rowCount();


    
                if ($total > 0) {

                    $busca_hora_lida = $sql_checa_c->fetch(PDO::FETCH_ASSOC);
                    $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
    
                    $status_checkin = $busca_hora_lida['status_checkin'] ?? '';
    
                        switch ($status_checkin) {
                            case '1':
                                $nome_status_chekcin ='Tarefa dentro do prazo';
                                break;
    
                                case '2':
                                    $nome_status_chekcin ='Tarefa em atraso';
                                    break;
    
                                    case '3':
                                        $nome_status_chekcin ='Tarefa fora do prazo';
                                        break;
                                        case '5':
                                            $nome_status_chekcin = 'Aguardando validação de API';
                            
                            default:
                            $nome_status_chekcin ='Chekin sem controle de Horário';
                                break;
                        }
                        if (!in_array($id, $addedIds)) {

                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "sim",
                        'informe_Tarefa' => 'Este checkin é uma Tarefa Única, sendo já realizada hoje por: Usuário: ' . $nome_usuario_chekcin . ' Hora Leitura: ' . $res['hora_leitura'] . ' Hora Lida: ' . $res['hora_lida'] . ' Status_Checkin: ' . $nome_status_chekcin,
                        'Tarefa' => "nao",
                        'periodo' => 'livre',
                        'ciclo' => 'unica',
                        'hora_leitura' => $res['hora_leitura'] ?? '',
                        'data_tarefa_agendada' => $data_tarefa_agendada,
                        'nome_ponto' => $res['nome_ponto'],
                        'id_ponto' => $res['id_ponto'],
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'hora_lida' => $hora_lida,
                        'usuario_tarefa' => $nome_user_check,
                        'usuario_solicitante' => $nome_usuario_solicitante,
                        'valorTag' => $valorTag,
                    ];

                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }
    
                        } 
                    
                   
                } elseif (!in_array($id, $addedIds)) {
                   
                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "nao",
                        'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                        'Tarefa' => "sim",
                        'periodo' => 'livre',
                        'ciclo' => 'unica',
                        'hora_leitura' => $res['hora_leitura'] ?? '',
                        'data_tarefa_agendada' => $data_tarefa_agendada,
                        'nome_ponto' => $res['nome_ponto'],
                        'id_ponto' => $res['id_ponto'],
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'usuario_tarefa' => $nome_user_check,
                        'usuario_solicitante' => $nome_usuario_solicitante,
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];

                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }
    
                        } 
                              
            }

            if ($controla_periodo == "2") { // com controle de horario

                 
                try {
                    if (preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $res['hora_leitura'])) {
                        $leitura = new DateTime($res['hora_leitura']);
                        $minima = new DateTime($res['hora_leitura']);
                        $minima->sub(new DateInterval('PT1M'));
                        $now = new DateTime('now');

                        if ($now > $leitura) {
                            $status_hora_leitura = "Prazo Expirado";
                           
                        } elseif ($now >= $minima && $now < $leitura) {
                            $status_hora_leitura = "Dentro do Prazo";
                          
                        } else {
                            $status_hora_leitura = "Próximo do Prazo";
                         
                        }
                    } else {
                        $status_hora_leitura= "Livre, sem prazo e horário de execução.";
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                $saida =  substr($res['hora_leitura'], 0, 5);
                $entrada   = substr($hora_atual, 0, 5);
                $prazo = intervalo($entrada, $saida);

                
            $sql_checa_checkin = "SELECT 
            checkin.id_periodo_ponto,
            checkin.id_colaborador,
            checkin.id_estacao,
            checkin.id_ponto,
            checkin.status_checkin,
            checkin.modo_checkin,
            checkin.hora_leitura,
            checkin.hora_lida,
            usuarios.nome

            FROM checkin
            INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
            WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
           
            AND checkin.id_estacao = :id_estacao 
            AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
            ";
            $sql_checa_c = $conexao->prepare($sql_checa_checkin);
            $sql_checa_c->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
            $sql_checa_c->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);

            $sql_checa_c->execute();

            $total = $sql_checa_c->rowCount();

           

            if ($total > 0) {
                $busca_hora_lida = $sql_checa_c->fetch(PDO::FETCH_ASSOC);
                $hora_lida = $busca_hora_lida['hora_lida'] ?? '';

                $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;
                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }
                    if (!in_array($id, $addedIds)) {
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "sim",
                    'informe_Tarefa' => 'Este checkin é uma Tarefa Única, sendo já realizada hoje por: Usuário: ' . $nome_usuario_chekcin . ' Hora Leitura: ' . $res['hora_leitura'] . ' Hora Lida: ' . $res['hora_lida'] . ' Status_Checkin: ' . $nome_status_chekcin,
                    'Tarefa' => "nao",
                    'periodo' => 'controlado',
                    'ciclo' => 'unico',
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'data_tarefa_agendada' => $data_tarefa_agendada,
                    'nome_ponto' => $res['nome_ponto'],
                    'id_ponto' => $res['id_ponto'],
                    'hora_lida' => $hora_lida,
                    'usuario_tarefa' => $nome_user_check,
                    'usuario_solicitante' => $nome_usuario_solicitante,
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura,
                    'valorTag' => $valorTag,
                ];
               
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 

                
               
            } else if (!in_array($id, $addedIds)) {

                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "nao",
                    'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                    'Tarefa' => "sim",
                    'periodo' => 'controlado',
                    'ciclo' => 'unico',
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'data_tarefa_agendada' => $data_tarefa_agendada,
                    'nome_ponto' => $res['nome_ponto'],
                    'id_ponto' => $res['id_ponto'],
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                    'usuario_tarefa' => $nome_user_check,
                    'usuario_solicitante' => $nome_usuario_solicitante,
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 

            }

        
        } // ciclo fecha tarefa única


        if ($ciclo_leitura == '1') {
            $ciclo = "diário";


            if ($controla_periodo == "1") {  // livre sem controle de horario


                $sql_checa_checkin = "SELECT 
                checkin.id_periodo_ponto,
                checkin.id_colaborador,
                checkin.id_estacao,
                checkin.id_ponto,
                checkin.status_checkin,
                checkin.modo_checkin,
                checkin.hora_leitura,
                checkin.hora_lida,
                usuarios.nome
    
                FROM checkin
                INNER JOIN usuarios ON usuarios.id =checkin.id_colaborador
                WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
               
                AND checkin.id_estacao = :id_estacao 
                AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
                ";
              $sql_checa_c1 = $conexao->prepare($sql_checa_checkin);
              $sql_checa_c1->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
              $sql_checa_c1->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);
    
              $sql_checa_c1->execute();
    
                $total = $sql_checa_c1->rowCount();

                try {
                    if (preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $res['hora_leitura'])) {
                        $leitura = new DateTime($res['hora_leitura']);
                        $minima = new DateTime($res['hora_leitura']);
                        $minima->sub(new DateInterval('PT1M'));
                        $now = new DateTime('now');

                        if ($now > $leitura) {
                            $status_hora_leitura = "Prazo Expirado";
                           
                        } elseif ($now >= $minima && $now < $leitura) {
                            $status_hora_leitura = " Dentro do Prazo";
                          
                        } else {
                            $status_hora_leitura = "Próximo do Prazo";
                         
                        }
                    } else {
                        $status_hora_leitura= "Prazo não Calculado, Leitura com horário Livre.";
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                $saida =  substr($res['hora_leitura'], 0, 5);
                $entrada   = substr($hora_atual, 0, 5);
                $prazo = intervalo($entrada, $saida);
    
                if ($total > 0) {

                    $busca_hora_lida = $sql_checa_c1->fetch(PDO::FETCH_ASSOC);
                    $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
                    $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;
                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }
                   
                    if (!in_array($id, $addedIds)) {
                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "sim", // alguem ja fez antes dele
                        'informe_Tarefa' => 'Este checkin é Diário e já foi realizado Hoje, sendo seu último registro: Usuário: ' . $nome_usuario_chekcin . ' Hora Leitura: ' . $res['hora_leitura'] . ' Hora Lida: ' .  $hora_lida .' Status_Checkin: ' . $nome_status_chekcin,
                        'Tarefa' => "nao",
                        'periodo' => 'livre',
                        'ciclo' => 'diário',
                        'hora_leitura' => $res['hora_leitura'] ?? '',
                        'hora_lida' => $hora_lida,
                        'nome_ponto' => $res['nome_ponto'],
                        'id_ponto' => $res['id_ponto'],
                        'data_tarefa_agendada' => $data_tarefa_agendada,
                        'usuario_tarefa' => $nome_user_check, // usuario em que a tarefa foi desegnada
                        'usuario_solicitante' => $nome_usuario_solicitante, // usuario que solicitou/criou a tarefa
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];
                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }
    
                        } 
                   
                } elseif (!in_array($id, $addedIds)) {
                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "nao", // ninguem fez ainda no dia de hoje
                        'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                        'Tarefa' => "sim",
                        'periodo' => 'livre',
                        'ciclo' => 'diario',
                        'hora_leitura' => $res['hora_leitura'] ?? '',
                        'data_tarefa_agendada' => $data_tarefa_agendada,
                        'nome_ponto' => $res['nome_ponto'],
                        'id_ponto' => $res['id_ponto'],        
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'usuario_tarefa' => $nome_user_check, // usuario em que a tarefa foi designada
                        'usuario_solicitante' => $nome_usuario_solicitante, // usuario que solicitou/criou a tarefa
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];
                    $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 
               

            }

            if ($controla_periodo == "2") { // com controle de horario


             
                try {
                    if (preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $res['hora_leitura'])) {
                        $leitura = new DateTime($res['hora_leitura']);
                        $minima = new DateTime($res['hora_leitura']);
                        $minima->sub(new DateInterval('PT1M'));
                        $now = new DateTime('now');

                        if ($now > $leitura) {
                            $status_hora_leitura = "Prazo Expirado";
                           
                        } elseif ($now >= $minima && $now < $leitura) {
                            $status_hora_leitura = " Dentro do Prazo";
                          
                        } else {
                            $status_hora_leitura = "Próximo do Prazo";
                         
                        }
                    } else {
                        $status_hora_leitura= "Livre, sem prazo e horário de execução.";
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                $saida =  substr($res['hora_leitura'], 0, 5);
                $entrada   = substr($hora_atual, 0, 5);
                $prazo = intervalo($entrada, $saida);



                
            $sql_checa_checkin = "SELECT 
            checkin.id_periodo_ponto,
            checkin.id_colaborador,
            checkin.id_estacao,
            checkin.id_ponto,
            checkin.status_checkin,
            checkin.modo_checkin,
            checkin.hora_leitura,
            checkin.hora_lida,
            usuarios.nome

            FROM checkin
            INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
            WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
            
            AND checkin.id_estacao = :id_estacao 
            AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
            ";
          $sql_checa_c12 = $conexao->prepare($sql_checa_checkin);
          $sql_checa_c12->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
          $sql_checa_c12->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);

            $sql_checa_c12->execute();

            $total = $sql_checa_c12->rowCount();

            if ($total > 0) {
                $busca_hora_lida = $sql_checa_c12->fetch(PDO::FETCH_ASSOC);
                $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
                $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;
                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }
                
                    if (!in_array($id, $addedIds)) {
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "sim", // ninguem fez ainda no dia de hoje
                    'informe_Tarefa' => 'Este checkin é Diário, sua última realização foi realizada por: Usuário: ' . $nome_usuario_chekcin . ' Hora Leitura: ' . $res['hora_leitura'] . ' Hora Lida: ' . $hora_lida . 'Status_Checkin: ' . $nome_status_chekcin,
                    'Tarefa' => "nao",
                    'periodo' => 'controle',
                    'ciclo' => 'diario',
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'hora_lida' => $hora_lida,
                    'data_tarefa_agendada' => $data_tarefa_agendada,
                    'nome_ponto' => $res['nome_ponto'],
                    'id_ponto' => $res['id_ponto'],
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                    'usuario_tarefa' => $nome_user_check, // usuario em que a tarefa foi desegnada
                    'usuario_solicitante' => $nome_usuario_solicitante, // usuario que solicitou/criou a tarefa
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                
               
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 
                
               
            } elseif (!in_array($id, $addedIds)) {
               
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "nao", // ninguém fez ainda no dia de hoje
                    'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                    'Tarefa' => "sim",
                    'periodo' => 'controle',
                    'ciclo' => 'diario',
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'data_tarefa_agendada' => $data_tarefa_agendada,
                    'nome_ponto' => $res['nome_ponto'],
                    'id_ponto' => $res['id_ponto'],
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                    'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                    'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 
        }
   } // fecha ciclo de leitura diario


        if ($ciclo_leitura == "2") {
            $ciclo = "semanal";


         
            if ($controla_periodo == "1") {  // livre sem controle de horario
             // Declaração das variáveis antes do loop

                $sql_checa_checkin = "SELECT 
                checkin.id_periodo_ponto,
                checkin.id_colaborador,
                checkin.id_estacao,
                checkin.id_ponto,
                checkin.status_checkin,
                checkin.modo_checkin,
                checkin.hora_leitura,
                checkin.hora_lida,
                usuarios.nome
    
                FROM checkin
                INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
                WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
              
                AND checkin.id_estacao = :id_estacao AND 
                AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
                ";
                $sql_checa_c23 = $conexao->prepare($sql_checa_checkin);
                $sql_checa_c23->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
                $sql_checa_c23->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);
    
                $sql_checa_c23->execute();
    
                $total =  $sql_checa_c23->rowCount();
    
                if ($total > 0) {
                    $busca_hora_lida = $sql_checa_c23->fetch(PDO::FETCH_ASSOC);
                    $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
                    $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;
                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }
                    
                    if (!in_array($id, $addedIds)) {      
                    
                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "sim", // ninguém fez ainda no dia de hoje
                        'informe_Tarefa' => 'Este checkin é Semanal, e já foi realizado hoje através do '. $nome_usuario_chekcin.' às: ' . $hora_lida . ', no horário ' . $hora_lida . '  Próximo será em: ' . intervalo($entrada, $saida),
                        'Tarefa' => "nao",
                        'periodo' => 'livre',
                        'ciclo' => 'semanal',
                        'dias_da_semana' => $nome_dia_semana_periodo,
                        'hora_leitura' => $res['hora_leitura'] ?? '',
                        'hora_lida' => $hora_lida,
                        'data_tarefa_agendada' => $data_tarefa_agendada,
                        'nome_ponto' => $res['nome_ponto'],
                        'id_ponto' => $res['id_ponto'],
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                        'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];
                    
                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }
    
                        } 
                
                    
                   
                    } elseif (!in_array($id, $addedIds)) {
            
                 
                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "nao", // ninguém fez ainda no dia de hoje
                        'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                        'Tarefa' => $Tarefa,
                        'periodo' => 'livre',
                        'ciclo' => 'semanal',
                        'dias_da_semana' => $nome_dia_semana_periodo,
                        'hora_leitura' => $res['hora_leitura'] ?? '',
                        'data_tarefa_agendada' => $data_tarefa_agendada,
                        'nome_ponto' => $res['nome_ponto'],
                        'id_ponto' => $res['id_ponto'],
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                        'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];
                    
                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }
    
                        } 

            }

            if ($controla_periodo == "2") { // com controle de horario

                            
                try {
                    if (preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $res['hora_leitura'])) {
                        $leitura = new DateTime($res['hora_leitura']);
                        $minima = new DateTime($res['hora_leitura']);
                        $minima->sub(new DateInterval('PT1M'));
                        $now = new DateTime('now');

                        if ($now > $leitura) {
                            $status_hora_leitura = "Prazo Expirado";
                           
                        } elseif ($now >= $minima && $now < $leitura) {
                            $status_hora_leitura = " Dentro do Prazo";
                          
                        } else {
                            $status_hora_leitura = "Próximo do Prazo";
                         
                        }
                    } else {
                        $status_hora_leitura= "Formato de hora inválido.";
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                $saida =  substr($res['hora_leitura'], 0, 5);
                $entrada   = substr($hora_atual, 0, 5);
                $prazo = intervalo($entrada, $saida);

              


            $sql_checa_checkin = "SELECT 
            checkin.id_periodo_ponto,
            checkin.id_colaborador,
            checkin.id_estacao,
            checkin.id_ponto,
            checkin.status_checkin,
            checkin.modo_checkin,
            checkin.hora_leitura,
            checkin.hora_lida,
            usuarios.nome

            FROM checkin
            INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
            WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
           
            AND checkin.id_estacao = :id_estacao 
            AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
            ";
            $sql_checa_c24 = $conexao->prepare($sql_checa_checkin);
            $sql_checa_c24->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
            $sql_checa_c24->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);

            $sql_checa_c24->execute();

            $total =  $sql_checa_c24->rowCount();

            if ($total > 0) {

                $busca_hora_lida = $sql_checa_c24->fetch(PDO::FETCH_ASSOC);
                    $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
                    $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;
                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }

                         
                    if (!in_array($id, $addedIds)) {    
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "sim", // ninguém fez ainda no dia de hoje
                    'informe_Tarefa' => 'Este checkin é Semanal, e já foi realizado hoje através do '. $nome_usuario_chekcin.' às: ' . $hora_lida . ', no horário ' . $hora_lida . '  Próximo será em: ' . intervalo($entrada, $saida),
                    'Tarefa' =>  "nao",
                    'periodo' => 'controle',
                    'ciclo' => 'semanal',
                    'dias_da_semana' => $nome_dia_semana_periodo,
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'hora_lida' =>  $hora_lida,
                    'data_tarefa_agendada' => $data_tarefa_agendada,
                    'nome_ponto' => $res['nome_ponto'],
                    'id_ponto' => $res['id_ponto'],
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                    'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                    'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                
               
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 
               
                           
               
            } elseif (!in_array($id, $addedIds)) {     
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "nao", // ninguém fez ainda no dia de hoje
                    'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                    'Tarefa' =>  $Tarefa,
                    'periodo' => 'controle',
                    'ciclo' => 'semanal',
                    'dias_da_semana' => $nome_dia_semana_periodo,
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'data_tarefa_agendada' => $data_tarefa_agendada,
                    'nome_ponto' => $res['nome_ponto'],
                    'id_ponto' => $res['id_ponto'],
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                    'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                    'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 
                } // fecha com controle de horario   
            }  // fecha ciclo de leitura semanal 

           
       

        } // fecha for principal


        $jsonResponse = json_encode($taskDetailsArray);

        // Configura o cabeçalho da resposta para indicar que o conteúdo é JSON
        header('Content-Type: application/json');
        
        // Envia a resposta
        echo $jsonResponse;


      }

 
    
    
} // Fecha ação Tarefa Presencial, ponto_plcode



if ($acao == "ponto_parametro") { // Tarefa com Leitura de Indicador


    $sql_periodo = "SELECT 
    periodo_ponto.*,
    pontos_estacao.nome_ponto,
    pontos_estacao.controla_periodo_ponto,
    pontos_estacao.status_ponto,
    parametros_ponto.nome_parametro,
    parametros_ponto.status_parametro,
    parametros_ponto.concen_min,
    parametros_ponto.concen_max,
    parametros_ponto.controle_concentracao,
    parametros_ponto.id_sensor_iot,
    periodo_dia_ponto.dia_semana,
    dia_semana.nome_dia_semana,
    checkin.id_colaborador,
    u.nome_unidade_medida
  
FROM periodo_ponto
INNER JOIN pontos_estacao ON pontos_estacao.id_ponto = periodo_ponto.id_ponto
INNER JOIN parametros_ponto ON parametros_ponto.id_parametro = periodo_ponto.id_parametro
INNER JOIN unidade_medida u ON u.id_unidade_medida = parametros_ponto.unidade_medida
LEFT JOIN checkin ON checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto
LEFT JOIN periodo_dia_ponto ON periodo_dia_ponto.id_periodo_ponto = periodo_ponto.id_periodo_ponto
LEFT JOIN dia_semana ON dia_semana.representa_php = periodo_dia_ponto.dia_semana

WHERE periodo_ponto.tipo_checkin = 'ponto_parametro' AND periodo_ponto.status_periodo != '3' AND parametros_ponto.status_parametro = '1'
AND periodo_ponto.id_estacao = :id_estacao
GROUP BY periodo_ponto.id_periodo_ponto ORDER BY periodo_ponto.id_periodo_ponto ASC

";
//AND periodo_ponto.status_periodo != '3'

    $stmt = $conexao->prepare($sql_periodo);
    $stmt->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);
    $stmt->execute();

    $total = $stmt->rowCount();

    if ($total > 0) {
// pega hora atual php
$hora_atual = date('H:i');
$data_atual_periodo = date_create()->format('Y-m-d');

// declara a array que será incrementada com os dados da tarefa

$taskDetailsArray = [];
$addedIds = [];
$hoje = '';

        foreach ($stmt as $res) {

            $id = $res['id_periodo_ponto'];
          
            $ciclo_leitura = $res['ciclo_leitura'];
            $controla_periodo = $res['modo_checkin_periodo'];
            $diasemana_numero = date('w');
            $nome_dia_semana_periodo='';


            $controle_concentracao = $res['controle_concentracao'] ?? '';

            $nome_unidade_medida = $res['nome_unidade_medida'] ?? 'N/I';

            $data_tarefa_agendada = $res['data_tarefa'] ?? '';

            if ($data_tarefa_agendada !== '') {
                $dateTimeObj = date_create($data_tarefa_agendada);
                
                if ($dateTimeObj === false) {
                    // Registro de erro ou alguma ação
                    error_log("Falha ao converter a data: $data_tarefa_agendada");
                } else {
                    $data_tarefa_agendada = date_format($dateTimeObj, 'd/m/Y');
                }
            }



            $diasemana_numero = date('w');

            $nome_dia_semana_periodo = buscaTarefasPorDia($conexao, $id, $diasemana_numero);

            // acessando os dias da semana pelo periodo informado, retornado pela chamada da funcao buscarTatefasPorDia
            if (isset($nome_dia_semana_periodo[0]['nome_dia_semana'])) {
                $nome_dia_semana = $nome_dia_semana_periodo[0]['nome_dia_semana'];
                } else {
                $nome_dia_semana = array('Dia da semana não informado');
                }
            
                // Verifique se hoje é o dia certo para essa tarefa
               
                $hoje = false;
                foreach ($nome_dia_semana_periodo as $dia) {
                    if ($dia['hoje']) {
                        $hoje = true;
                        break;
                    }
                }
            
                // Atualize o valor da variável $Tarefa com base na variável $hoje
                $Tarefa = $hoje ? "sim" : "nao";


            
            


            // trata o nome do usuário que realizou a tarefa ou que a recebeu como agendada
            $id_nome_user_check = $res['usuario_tarefa'] ?? '';
            $nome_user_check = '';
            
            if ($id_nome_user_check != '') {
                // Preparar a consulta SQL
                $stmt = $conexao->prepare("SELECT nome FROM usuarios WHERE id = :id");
                
                // Vincular parâmetros
                $stmt->bindParam(':id', $id_nome_user_check, PDO::PARAM_STR);
                
                // Executar a consulta
                if ($stmt->execute()) {
                    // Buscar resultados
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Verificar se algum resultado foi retornado
                    if ($result !== false) {
                        $nome_user_check = $result['nome'];
                    }
                } else {
                    // Aqui você pode lidar com erros na execução da consulta
                }
            } else {
                $nome_user_check = '';
            }


            // trata o usuário solicitante
                    $usuario_solicitante = $res['usuario_solicitante'] ?? '';
                    $nome_usuario_solicitante = '';

                    if ($usuario_solicitante != '') {
                        // Preparar a consulta SQL
                        $stmt_solicitante = $conexao->prepare("SELECT nome FROM usuarios WHERE id = :id");
                        
                        // Vincular parâmetros
                        $stmt_solicitante->bindParam(':id', $usuario_solicitante, PDO::PARAM_STR);
                        
                        // Executar a consulta
                        if ($stmt_solicitante->execute()) {
                            // Buscar resultados
                            $result_solicitante = $stmt_solicitante->fetch(PDO::FETCH_ASSOC);
                            
                            // Verificar se algum resultado foi retornado
                            if ($result_solicitante !== false) {
                                $nome_usuario_solicitante = $result_solicitante['nome'];
                            }
                        } else {
                            // Aqui você pode lidar com erros na execução da consulta
                        }
                    } else {
                        $nome_usuario_solicitante = '';
                    }


                // trata o usuário que realizou o checkin
                    $usuario_chekcin = $res['id_colaborador'] ?? '';
                    $nome_usuario_chekcin = '';

                    if ($usuario_chekcin != '') {
                        // Preparar a consulta SQL
                        $stmt_usuario_chekcin = $conexao->prepare("SELECT nome FROM usuarios WHERE id = :id");
                        
                        // Vincular parâmetros
                        $stmt_usuario_chekcin->bindParam(':id', $usuario_chekcin, PDO::PARAM_STR);
                        
                        // Executar a consulta
                        if ($stmt_usuario_chekcin->execute()) {
                            // Buscar resultados
                            $result_solicitante = $stmt_usuario_chekcin->fetch(PDO::FETCH_ASSOC);
                            
                            // Verificar se algum resultado foi retornado
                            if ($result_solicitante !== false) {
                                $nome_usuario_chekcin = $result_solicitante['nome'];
                            }
                        } else {
                            // Aqui você pode lidar com erros na execução da consulta
                        }
                    } else {
                        $nome_usuario_chekcin = '';
                    }
           

                    // trabalho com a funcao para que separe item a item das tags
                    $valorTag = processTags($res['tags'] ?? NULL);


        if ($ciclo_leitura == '0') {
            $ciclo = "único";


            if ($controla_periodo == "1") {  // livre sem controle de horario


                $sql_checa_checkin = "SELECT 
                checkin.id_periodo_ponto,
                checkin.id_colaborador,
                checkin.id_estacao,
                checkin.id_ponto,
                checkin.status_checkin,
                checkin.modo_checkin,
                checkin.hora_leitura,
                checkin.hora_lida,
                usuarios.nome
    
                FROM checkin
                INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
                WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
               
                AND checkin.id_estacao = :id_estacao 
                AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
                ";
                $sql_checa = $conexao->prepare($sql_checa_checkin);
                $sql_checa->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
                $sql_checa->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);
    
                $sql_checa->execute();
    
                $total = $sql_checa->rowCount();


    
                if ($total > 0) {

                    $busca_hora_lida = $sql_checa_c->fetch(PDO::FETCH_ASSOC);
                    $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
    
                    $status_checkin = $busca_hora_lida['status_checkin'] ?? '';
    
                        switch ($status_checkin) {
                            case '1':
                                $nome_status_chekcin ='Tarefa dentro do prazo';
                                break;
    
                                case '2':
                                    $nome_status_chekcin ='Tarefa em atraso';
                                    break;
    
                                    case '3':
                                        $nome_status_chekcin ='Tarefa fora do prazo';
                                        break;
                                        case '5':
                                            $nome_status_chekcin = 'Aguardando validação de API';
                            
                            default:
                            $nome_status_chekcin ='Chekcin sem controle de prazo';
                                break;
                        }

                     
                        if (!in_array($id, $addedIds)) {
                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "sim",
                        'informe_Tarefa' => 'Este checkin é uma Tarefa Única, sendo já realizada hoje por: Usuário: ' . $nome_usuario_chekcin . ' Hora Leitura: ' . $res['hora_leitura'] . ' Hora Lida: ' . $res['hora_lida'] . ' Status_Checkin: ' . $nome_status_chekcin,
                        'Tarefa' => "nao",
                        'periodo' => 'livre',
                        'ciclo' => 'unica',                       
                        'hora_leitura' => $res['hora_leitura'] ?? '',
                        'data_tarefa_agendada' => $data_tarefa_agendada,
                        'nome_ponto' => $res['nome_ponto'],
                        'id_ponto' => $res['id_ponto'],
                        'nome_parametro' => $res['nome_parametro'],
                        'nome_unidade_medida' => $nome_unidade_medida,
                        'id_parametro' => $res['id_parametro'],
                        'concen_min' => floatval($res['concen_min']) ?? 'não informado',
                        'concen_max' => floatval($res['concen_max']) ?? 'não informado',
                        'controla_concentracao' => $controle_concentracao,
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'hora_lida' => $hora_lida,                       
                        'usuario_tarefa' => $nome_user_check,
                        'usuario_solicitante' => $nome_usuario_solicitante,
                        "status_hora_leitura"=> $nome_status_chekcin ?? '',
                        'valorTag' => $valorTag,
                    ];

                   
                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }

                        }    
                   
                } else {

                    if (!in_array($id, $addedIds)) {
                   
                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "nao",
                        'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                        'Tarefa' => "sim",
                        'periodo' => 'livre',
                        'ciclo' => 'unica',                        
                        'hora_leitura' => $res['hora_leitura'] ?? '',
                        'data_tarefa_agendada' => $data_tarefa_agendada,
                        'nome_ponto' => $res['nome_ponto'],
                        'id_ponto' => $res['id_ponto'],
                        'nome_parametro' => $res['nome_parametro'],
                        'nome_unidade_medida' => $nome_unidade_medida,
                        'id_parametro' => $res['id_parametro'],
                        'concen_min' => floatval($res['concen_min']) ?? 'não informado',
                        'concen_max' => floatval($res['concen_max']) ?? 'não informado',
                        'controla_concentracao' => $controle_concentracao,
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'usuario_tarefa' => $nome_user_check,
                        'usuario_solicitante' => $nome_usuario_solicitante,
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];

                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }

                        } 

                                                    
                       
                }
                              
            }

            if ($controla_periodo == "2") { // com controle de horario

                 
                try {
                    if (preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $res['hora_leitura'])) {
                        $leitura = new DateTime($res['hora_leitura']);
                        $minima = new DateTime($res['hora_leitura']);
                        $minima->sub(new DateInterval('PT1M'));
                        $now = new DateTime('now');

                        if ($now > $leitura) {
                            $status_hora_leitura = "Prazo Expirado";
                           
                        } elseif ($now >= $minima && $now < $leitura) {
                            $status_hora_leitura = " Dentro do Prazo";
                          
                        } else {
                            $status_hora_leitura = "Próximo do Prazo";
                         
                        }
                    } else {
                        $status_hora_leitura= "Livre, sem prazo e horário de execução.";
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                $saida =  substr($res['hora_leitura'], 0, 5);
                $entrada   = substr($hora_atual, 0, 5);
                $prazo = intervalo($entrada, $saida);

                
            $sql_checa_checkin = "SELECT 
            checkin.id_periodo_ponto,
            checkin.id_colaborador,
            checkin.id_estacao,
            checkin.id_ponto,
            checkin.status_checkin,
            checkin.modo_checkin,
            checkin.hora_leitura,
            checkin.hora_lida,
            usuarios.nome

            FROM checkin
            INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
            WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
           
            AND checkin.id_estacao = :id_estacao 
            AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
            ";
            $sql_checa_c = $conexao->prepare($sql_checa_checkin);
            $sql_checa_c->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
            $sql_checa_c->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);

            $sql_checa_c->execute();

            $total = $sql_checa_c->rowCount();

           

            if ($total > 0) {
                $busca_hora_lida = $sql_checa_c->fetch(PDO::FETCH_ASSOC);
                $hora_lida = $busca_hora_lida['hora_lida'] ?? '';

                $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;
                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }

                    if (!in_array($id, $addedIds)) {
                
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "sim",
                    'informe_Tarefa' => 'Esta Tarefa já foi realizada Hoje, sendo seu último registro: Usuário: ' . $nome_usuario_chekcin . ' Hora Leitura: ' .  $res['hora_leitura'] . ' Hora Lida: ' . $hora_lida . 'Status_Checkin: ' . $nome_status_chekcin,
                    'Tarefa' => "nao",
                    'periodo' => 'controlado',
                    'ciclo' => 'unico',                    
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'data_tarefa_agendada' => $data_tarefa_agendada,
                    'nome_ponto' => $res['nome_ponto'],
                    'id_ponto' => $res['id_ponto'],
                    'nome_parametro' => $res['nome_parametro'],
                    'nome_unidade_medida' => $nome_unidade_medida,
                    'id_parametro' => $res['id_parametro'],
                    'concen_min' => floatval($res['concen_min']) ?? 'não informado',
                    'concen_max' => floatval($res['concen_max']) ?? 'não informado',
                    'controla_concentracao' => $controle_concentracao,
                    'hora_lida' => $hora_lida,
                    'usuario_tarefa' => $nome_user_check,
                    'usuario_solicitante' => $nome_usuario_solicitante,
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 
                
               
            } else {
                if (!in_array($id, $addedIds)) {
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "nao",
                    'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                    'Tarefa' => "sim",
                    'periodo' => 'controlado',
                    'ciclo' => 'unico',                   
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'data_tarefa_agendada' => $data_tarefa_agendada,
                    'nome_ponto' => $res['nome_ponto'],
                    'id_ponto' => $res['id_ponto'],
                    'nome_parametro' => $res['nome_parametro'],
                    'nome_unidade_medida' => $nome_unidade_medida,
                    'id_parametro' => $res['id_parametro'],
                    'concen_min' => floatval($res['concen_min']) ?? 'não informado',
                    'concen_max' => floatval($res['concen_max']) ?? 'não informado',
                    'controla_concentracao' => $controle_concentracao,
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'],
                    'usuario_tarefa' => $nome_user_check,
                    'usuario_solicitante' => $nome_usuario_solicitante,
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 
                  
                  
            }

            }

        
        } // ciclo fecha tarefa única


        if ($ciclo_leitura == '1') {
            $ciclo = "diário";


            if ($controla_periodo == "1") {  // livre sem controle de horario


                $sql_checa_checkin = "SELECT 
                checkin.id_periodo_ponto,
                checkin.id_colaborador,
                checkin.id_estacao,
                checkin.id_ponto,
                checkin.status_checkin,
                checkin.modo_checkin,
                checkin.hora_leitura,
                checkin.hora_lida,
                usuarios.nome
    
                FROM checkin
                INNER JOIN usuarios ON usuarios.id =checkin.id_colaborador
                WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
               
                AND checkin.id_estacao = :id_estacao 
                AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
                ";
              $sql_checa_c1 = $conexao->prepare($sql_checa_checkin);
              $sql_checa_c1->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
              $sql_checa_c1->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);
    
              $sql_checa_c1->execute();
    
                $total = $sql_checa_c1->rowCount();

                try {
                    if (preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $res['hora_leitura'])) {
                        $leitura = new DateTime($res['hora_leitura']);
                        $minima = new DateTime($res['hora_leitura']);
                        $minima->sub(new DateInterval('PT1M'));
                        $now = new DateTime('now');

                        if ($now > $leitura) {
                            $status_hora_leitura = "Prazo Expirado";
                           
                        } elseif ($now >= $minima && $now < $leitura) {
                            $status_hora_leitura = " Dentro do Prazo";
                          
                        } else {
                            $status_hora_leitura = "Próximo do Prazo";
                         
                        }
                    } else {
                        $status_hora_leitura= "Prazo não Calculado, Leitura com horário Livre.";
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                $saida =  substr($res['hora_leitura'], 0, 5);
                $entrada   = substr($hora_atual, 0, 5);
                $prazo = intervalo($entrada, $saida);
    
                if ($total > 0) {

                    $busca_hora_lida = $sql_checa_c1->fetch(PDO::FETCH_ASSOC);
                    $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
                    $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;
                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }
                   
                    if (!in_array($id, $addedIds)) {

                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "sim", // alguem ja fez antes dele
                        'informe_Tarefa' => 'Este checkin é Diário e já foi realizado Hoje, sendo seu último registro às '.$hora_lida.' através do Usuário: ' . $nome_usuario_chekcin,
                        'Tarefa' => "nao",
                        'periodo' => 'livre',
                        'ciclo' => 'diário',                        
                        'hora_leitura' => $res['hora_leitura']  ?? '',
                        'hora_lida' => $hora_lida,
                        'data_tarefa_agendada' => $data_tarefa_agendada,
                        'nome_ponto' => $res['nome_ponto'],
                        'id_ponto' => $res['id_ponto'],
                        'nome_parametro' => $res['nome_parametro'],
                        'nome_unidade_medida' => $nome_unidade_medida,
                        'id_parametro' => $res['id_parametro'],
                        'concen_min' => floatval($res['concen_min']) ?? 'não informado',
                        'concen_max' => floatval($res['concen_max']) ?? 'não informado',
                        'controla_concentracao' => $controle_concentracao,
                        'usuario_tarefa' => $nome_user_check, // usuario em que a tarefa foi desegnada
                        'usuario_solicitante' => $nome_usuario_solicitante, // usuario que solicitou/criou a tarefa
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];
                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }

                        } 
                
                   
                } else {
                    if (!in_array($id, $addedIds)) {
                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "nao", // ninguem fez ainda no dia de hoje
                        'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                        'Tarefa' => "sim",
                        'periodo' => 'livre',
                        'ciclo' => 'diario',                        
                        'hora_leitura' => $res['hora_leitura']  ?? '',
                        'data_tarefa_agendada' => $data_tarefa_agendada ?? '',
                        'nome_ponto' => $res['nome_ponto'],
                        'id_ponto' => $res['id_ponto'],
                        'nome_parametro' => $res['nome_parametro'],
                        'nome_unidade_medida' => $nome_unidade_medida,
                        'id_parametro' => $res['id_parametro'],
                        'concen_min' => floatval($res['concen_min']) ?? 'não informado',
                        'concen_max' => floatval($res['concen_max']) ?? 'não informado',
                        'controla_concentracao' => $controle_concentracao,
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'usuario_tarefa' => $nome_user_check, // usuario em que a tarefa foi designada
                        'usuario_solicitante' => $nome_usuario_solicitante, // usuario que solicitou/criou a tarefa
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];
                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }

                        } 
                        
                           

                      
                }
               

            }

            if ($controla_periodo == "2") { // com controle de horario


             
                try {
                    if (preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $res['hora_leitura'])) {
                        $leitura = new DateTime($res['hora_leitura']);
                        $minima = new DateTime($res['hora_leitura']);
                        $minima->sub(new DateInterval('PT1M'));
                        $now = new DateTime('now');

                        if ($now > $leitura) {
                            $status_hora_leitura = "Prazo Expirado";
                           
                        } elseif ($now >= $minima && $now < $leitura) {
                            $status_hora_leitura = " Dentro do Prazo";
                          
                        } else {
                            $status_hora_leitura = "Próximo do Prazo";
                         
                        }
                    } else {
                        $status_hora_leitura= "Livre, sem prazo e horário de execução.";
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                $saida =  substr($res['hora_leitura'], 0, 5);
                $entrada   = substr($hora_atual, 0, 5);
                $prazo = intervalo($entrada, $saida);



                
            $sql_checa_checkin = "SELECT 
            checkin.id_periodo_ponto,
            checkin.id_colaborador,
            checkin.id_estacao,
            checkin.id_ponto,
            checkin.status_checkin,
            checkin.modo_checkin,
            checkin.hora_leitura,
            checkin.hora_lida,
            usuarios.nome

            FROM checkin
            INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
            WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
            
            AND checkin.id_estacao = :id_estacao 
            AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
            ";
          $sql_checa_c12 = $conexao->prepare($sql_checa_checkin);
          $sql_checa_c12->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
          $sql_checa_c12->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);

            $sql_checa_c12->execute();

            $total = $sql_checa_c12->rowCount();

            if ($total > 0) {
                $busca_hora_lida = $sql_checa_c12->fetch(PDO::FETCH_ASSOC);
                $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
                $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;

                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }
                
                    if (!in_array($id, $addedIds)) {
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "sim", // ninguem fez ainda no dia de hoje
                    'informe_Tarefa' => 'Este checkin é Diário, sua última realização foi realizada ás '.$hora_lida.' através do Usuário: ' . $nome_usuario_chekcin ,
                    'Tarefa' => "nao",
                    'periodo' => 'controle',
                    'ciclo' => 'diario',                    
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'hora_lida' => $hora_lida,
                    'data_tarefa_agendada' => $data_tarefa_agendada ?? '',
                    'nome_ponto' => $res['nome_ponto'],
                    'id_ponto' => $res['id_ponto'],
                    'nome_parametro' => $res['nome_parametro'],
                    'nome_unidade_medida' => $nome_unidade_medida,
                    'id_parametro' => $res['id_parametro'],
                    'concen_min' => floatval($res['concen_min']) ?? 'não informado',
                    'concen_max' => floatval($res['concen_max']) ?? 'não informado',
                    'controla_concentracao' => $controle_concentracao,
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                    'usuario_tarefa' => $nome_user_check, // usuario em que a tarefa foi desegnada
                    'usuario_solicitante' => $nome_usuario_solicitante, // usuario que solicitou/criou a tarefa
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 

                
               
            } else {
                if (!in_array($id, $addedIds)) {
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "nao", // ninguém fez ainda no dia de hoje
                    'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                    'Tarefa' => "sim",
                    'periodo' => 'controle',
                    'ciclo' => 'diario',                 
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'data_tarefa_agendada' => $data_tarefa_agendada ?? '',
                    'nome_ponto' => $res['nome_ponto'],
                    'id_ponto' => $res['id_ponto'],
                    'nome_parametro' => $res['nome_parametro'],
                    'nome_unidade_medida' => $nome_unidade_medida,
                    'id_parametro' => $res['id_parametro'],
                    'concen_min' => floatval($res['concen_min']) ?? 'não informado',
                    'concen_max' => floatval($res['concen_max']) ?? 'não informado',
                    'controla_concentracao' => $controle_concentracao,
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                    'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                    'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 

                 
                  
            }
        }
   } // fecha ciclo de leitura diario


        if ($ciclo_leitura == "2") {
            $ciclo = "semanal";


          

            if ($controla_periodo == "1") {  // livre sem controle de horario
                                // Declaração das variáveis antes do loop

                                try {
                                    if (preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $res['hora_leitura'])) {
                                        $leitura = new DateTime($res['hora_leitura']);
                                        $minima = new DateTime($res['hora_leitura']);
                                        $minima->sub(new DateInterval('PT1M'));
                                        $now = new DateTime('now');
                
                                        if ($now > $leitura) {
                                            $status_hora_leitura = "Prazo Expirado";
                                           
                                        } elseif ($now >= $minima && $now < $leitura) {
                                            $status_hora_leitura = " Dentro do Prazo";
                                          
                                        } else {
                                            $status_hora_leitura = "Próximo do Prazo";
                                         
                                        }
                                    } else {
                                        $status_hora_leitura= "Livre, sem prazo e horário de execução.";
                                    }
                                } catch (Exception $e) {
                                    echo $e->getMessage();
                                }
                
                                $saida =  substr($res['hora_leitura'], 0, 5);
                                $entrada   = substr($hora_atual, 0, 5);
                                $prazo = intervalo($entrada, $saida);

                $sql_checa_checkin = "SELECT 
                checkin.id_periodo_ponto,
                checkin.id_colaborador,
                checkin.id_estacao,
                checkin.id_ponto,
                checkin.status_checkin,
                checkin.modo_checkin,
                checkin.hora_leitura,
                checkin.hora_lida,
                usuarios.nome
    
                FROM checkin
                INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
                WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
                AND checkin.id_estacao = :id_estacao 
                AND DATE_FORMAT(checkin.data_cadastro_checkin, '%Y-%m-%d')  = '$data_atual_periodo'
                ";
                $sql_checa_c23 = $conexao->prepare($sql_checa_checkin);
                $sql_checa_c23->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
                $sql_checa_c23->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);
    
                $sql_checa_c23->execute();
    
                $total =  $sql_checa_c23->rowCount();
    
                if ($total > 0) {
                    $busca_hora_lida = $sql_checa_c23->fetch(PDO::FETCH_ASSOC);
                    $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
                    $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;
                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }
                    
                   
                    if (!in_array($id, $addedIds)) {
                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "sim", // ninguém fez ainda no dia de hoje
                        'informe_Tarefa' => 'Este checkin é Semanal, e já foi realizado hoje através do '. $nome_usuario_chekcin.' às: ' . $hora_lida . ', no horário ' . $hora_lida . '  Próximo será em: ' . intervalo($entrada, $saida),
                        'Tarefa' => "nao",
                        'periodo' => 'livre',
                        'ciclo' => 'semanal',
                        'dias_da_semana' => $nome_dia_semana_periodo,                        
                        'hora_leitura' => $res['hora_leitura'] ?? '',
                        'hora_lida' => $hora_lida,
                        'data_tarefa_agendada' => $data_tarefa_agendada,
                        'nome_ponto' => $res['nome_ponto'],
                        'id_ponto' => $res['id_ponto'],
                        'nome_parametro' => $res['nome_parametro'],
                        'nome_unidade_medida' => $nome_unidade_medida,
                        'id_parametro' => $res['id_parametro'],
                        'concen_min' => floatval($res['concen_min']) ?? 'não informado',
                        'concen_max' => floatval($res['concen_max']) ?? 'não informado',
                        'controla_concentracao' => $controle_concentracao,
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                        'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];
                    
                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }

                        } 

                
                    
                   
                } else {

                    if (!in_array($id, $addedIds)) {
                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "nao", // ninguém fez ainda no dia de hoje
                        'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                        'Tarefa' => $Tarefa,
                        'periodo' => 'livre',
                        'ciclo' => 'semanal',                      
                        'dias_da_semana' => $nome_dia_semana_periodo,
                        'hora_leitura' => $res['hora_leitura'] ?? '',
                        'data_tarefa_agendada' => $data_tarefa_agendada,
                        'nome_ponto' => $res['nome_ponto'],
                        'id_ponto' => $res['id_ponto'],
                        'nome_parametro' => $res['nome_parametro'],
                        'nome_unidade_medida' => $nome_unidade_medida,
                        'id_parametro' => $res['id_parametro'],
                        'concen_min' => floatval($res['concen_min']) ?? 'não informado',
                        'concen_max' => floatval($res['concen_max']) ?? 'não informado',
                        'controla_concentracao' => $controle_concentracao,
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                        'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];
                     
                    $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 
  

                }
                

            }

            if ($controla_periodo == "2") { // com controle de horario

                            
                try {
                    if (preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $res['hora_leitura'])) {
                        $leitura = new DateTime($res['hora_leitura']);
                        $minima = new DateTime($res['hora_leitura']);
                        $minima->sub(new DateInterval('PT1M'));
                        $now = new DateTime('now');

                        if ($now > $leitura) {
                            $status_hora_leitura = "Prazo Expirado";
                           
                        } elseif ($now >= $minima && $now < $leitura) {
                            $status_hora_leitura = " Dentro do Prazo";
                          
                        } else {
                            $status_hora_leitura = "Próximo do Prazo";
                         
                        }
                    } else {
                        $status_hora_leitura= "Formato de hora inválido.";
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                $saida =  substr($res['hora_leitura'], 0, 5);
                $entrada   = substr($hora_atual, 0, 5);
                $prazo = intervalo($entrada, $saida);

    


            $sql_checa_checkin = "SELECT 
            checkin.id_periodo_ponto,
            checkin.id_colaborador,
            checkin.id_estacao,
            checkin.id_ponto,
            checkin.status_checkin,
            checkin.modo_checkin,
            checkin.hora_leitura,
            checkin.hora_lida,
            usuarios.nome

            FROM checkin
            INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
            WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
           
            AND checkin.id_estacao = :id_estacao 
            AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
            ";
            $sql_checa_c24 = $conexao->prepare($sql_checa_checkin);
            $sql_checa_c24->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
            $sql_checa_c24->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);

            $sql_checa_c24->execute();

            $total =  $sql_checa_c24->rowCount();

            if ($total > 0) {

                $busca_hora_lida = $sql_checa_c24->fetch(PDO::FETCH_ASSOC);
                    $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
                    $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;
                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }
              
                
                    if (!in_array($id, $addedIds)) {
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "sim", // ninguém fez ainda no dia de hoje
                    'informe_Tarefa' => 'Este checkin é Semanal, e já foi realizado hoje através do '. $nome_usuario_chekcin.' às: ' . $hora_lida . ', no horário ' . $hora_lida . '  Próximo será em: ' . intervalo($entrada, $saida),
                    'Tarefa' =>  "nao",
                    'periodo' => 'controle',
                    'ciclo' => 'semanal',                    
                    'dias_da_semana' => $nome_dia_semana_periodo,
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'hora_lida' =>  $hora_lida,
                    'data_tarefa_agendada' => $data_tarefa_agendada,
                    'nome_ponto' => $res['nome_ponto'],
                    'id_ponto' => $res['id_ponto'],
                    'nome_parametro' => $res['nome_parametro'],
                    'nome_unidade_medida' => $nome_unidade_medida,
                    'id_parametro' => $res['id_parametro'],
                    'concen_min' => floatval($res['concen_min']) ?? 'não informado',
                    'concen_max' => floatval($res['concen_max']) ?? 'não informado',
                    'controla_concentracao' => $controle_concentracao,
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                    'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                    'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                
               
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 

               
                           
               
            } else {
                if (!in_array($id, $addedIds)) {
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "nao", // ninguém fez ainda no dia de hoje
                    'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                    'Tarefa' => $Tarefa,
                    'periodo' => 'controle',
                    'ciclo' => 'semanal',                   
                    'dias_da_semana' => $nome_dia_semana_periodo,
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'data_tarefa_agendada' => $data_tarefa_agendada,
                    'nome_ponto' => $res['nome_ponto'],
                    'id_ponto' => $res['id_ponto'],
                    'nome_parametro' => $res['nome_parametro'],
                    'nome_unidade_medida' => $nome_unidade_medida,
                    'id_parametro' => $res['id_parametro'],
                    'concen_min' => floatval($res['concen_min']) ?? 'não informado',
                    'concen_max' => floatval($res['concen_max']) ?? 'não informado',
                    'controla_concentracao' => $controle_concentracao,
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                    'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                    'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 
                    }
                } // fecha com controle de horario   
            }  // fecha ciclo de leitura semanal 

           
       

        } // fecha for principal

// Converte o array em uma string JSON  //echo json_encode($taskDetailsArray);

$jsonResponse = json_encode($taskDetailsArray);

// Configura o cabeçalho da resposta para indicar que o conteúdo é JSON
header('Content-Type: application/json');

// Envia a resposta
echo $jsonResponse;


      }

 
    
    
}// Fecha ação tarefa Leitura Parametro/Indicadores
    
if ($acao == "tarefa_agendada") { // Tarefa Agendada


    $sql_periodo = "SELECT 
    periodo_ponto.*,
    periodo_ponto.tipo_checkin,
    periodo_dia_ponto.dia_semana,
    dia_semana.nome_dia_semana,
    periodo_ponto.tags,
    checkin.id_colaborador
  
FROM periodo_ponto
LEFT JOIN checkin ON checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto
LEFT JOIN periodo_dia_ponto ON periodo_dia_ponto.id_periodo_ponto = periodo_ponto.id_periodo_ponto
LEFT JOIN dia_semana ON dia_semana.representa_php = periodo_dia_ponto.dia_semana

WHERE periodo_ponto.tipo_checkin = 'tarefa_agendada' AND periodo_ponto.status_periodo != '3'
AND periodo_ponto.id_estacao = :id_estacao
GROUP BY periodo_ponto.id_periodo_ponto ORDER BY periodo_ponto.id_periodo_ponto ASC

";
//AND periodo_ponto.status_periodo != '3'

    $stmt = $conexao->prepare($sql_periodo);
    $stmt->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);
    $stmt->execute();

    $total = $stmt->rowCount();

    if ($total > 0) {
// pega hora atual php
$hora_atual = date('H:i');
$data_atual_periodo = date_create()->format('Y-m-d');

// declara a array que será incrementada com os dados da tarefa
$taskDetailsArray = [];
$addedIds = [];
$hoje='';

        foreach ($stmt as $res) {

            // pega os dados do periodo
            $id = $res['id_periodo_ponto'];
            $ciclo_leitura = $res['ciclo_leitura'];
            $controla_periodo = $res['modo_checkin_periodo'];
            $diasemana_numero = date('w');
            $nome_dia_semana_periodo='';

            // trabalho com a funcao para que separe item a item das tags
            $data_tarefa_agendada = $res['data_tarefa'] ?? '';

            if ($data_tarefa_agendada !== '') {
                $dateTimeObj = date_create($data_tarefa_agendada);
                
                if ($dateTimeObj === false) {
                    // Registro de erro ou alguma ação
                    error_log("Falha ao converter a data: $data_tarefa_agendada");
                } else {
                    $data_tarefa_agendada = date_format($dateTimeObj, 'd/m/Y');
                }
            }

            $diasemana_numero = date('w');

            $nome_dia_semana_periodo = buscaTarefasPorDia($conexao, $id, $diasemana_numero);

            // acessando os dias da semana pelo periodo informado, retornado pela chamada da funcao buscarTatefasPorDia
            if (isset($nome_dia_semana_periodo[0]['nome_dia_semana'])) {
                $nome_dia_semana = $nome_dia_semana_periodo[0]['nome_dia_semana'];
                } else {
                $nome_dia_semana = array('Dia da semana não informado');
                }
            
                // Verifique se hoje é o dia certo para essa tarefa
                $hoje = '';
                $hoje = false;
                foreach ($nome_dia_semana_periodo as $dia) {
                    if ($dia['hoje']) {
                        $hoje = true;
                        break;
                    }
                }


            // trata o nome do usuário que realizou a tarefa ou que a recebeu como agendada
            $id_nome_user_check = $res['usuario_tarefa'] ?? '';
            $nome_user_check = '';
            
            if ($id_nome_user_check != '') {
                // Preparar a consulta SQL
                $stmt = $conexao->prepare("SELECT nome FROM usuarios WHERE id = :id");
                
                // Vincular parâmetros
                $stmt->bindParam(':id', $id_nome_user_check, PDO::PARAM_STR);
                
                // Executar a consulta
                if ($stmt->execute()) {
                    // Buscar resultados
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Verificar se algum resultado foi retornado
                    if ($result !== false) {
                        $nome_user_check = $result['nome'];
                    }
                } else {
                    // Aqui você pode lidar com erros na execução da consulta
                }
            } else {
                $nome_user_check = '';
            }


            // trata o usuário solicitante
                    $usuario_solicitante = $res['usuario_solicitante'] ?? '';
                    $nome_usuario_solicitante = '';

                    if ($usuario_solicitante != '') {
                        // Preparar a consulta SQL
                        $stmt_solicitante = $conexao->prepare("SELECT nome FROM usuarios WHERE id = :id");
                        
                        // Vincular parâmetros
                        $stmt_solicitante->bindParam(':id', $usuario_solicitante, PDO::PARAM_STR);
                        
                        // Executar a consulta
                        if ($stmt_solicitante->execute()) {
                            // Buscar resultados
                            $result_solicitante = $stmt_solicitante->fetch(PDO::FETCH_ASSOC);
                            
                            // Verificar se algum resultado foi retornado
                            if ($result_solicitante !== false) {
                                $nome_usuario_solicitante = $result_solicitante['nome'];
                            }
                        } else {
                            // Aqui você pode lidar com erros na execução da consulta
                        }
                    } else {
                        $nome_usuario_solicitante = '';
                    }


                // trata o usuário que realizou o checkin
                    $usuario_chekcin = $res['id_colaborador'] ?? '';
                    $nome_usuario_chekcin = '';

                    if ($usuario_chekcin != '') {
                        // Preparar a consulta SQL
                        $stmt_usuario_chekcin = $conexao->prepare("SELECT nome FROM usuarios WHERE id = :id");
                        
                        // Vincular parâmetros
                        $stmt_usuario_chekcin->bindParam(':id', $usuario_chekcin, PDO::PARAM_STR);
                        
                        // Executar a consulta
                        if ($stmt_usuario_chekcin->execute()) {
                            // Buscar resultados
                            $result_solicitante = $stmt_usuario_chekcin->fetch(PDO::FETCH_ASSOC);
                            
                            // Verificar se algum resultado foi retornado
                            if ($result_solicitante !== false) {
                                $nome_usuario_chekcin = $result_solicitante['nome'];
                            }
                        } else {
                            // Aqui você pode lidar com erros na execução da consulta
                        }
                    } else {
                        $nome_usuario_chekcin = '';
                    }
           

                    // trabalho com a funcao para que separe item a item das tags
                    $valorTag = processTags($res['tags'] ?? NULL);


        if ($ciclo_leitura == '0') {
            $ciclo = "único";


            if ($controla_periodo == "1") {  // livre sem controle de horario


                $sql_checa_checkin = "SELECT 
                checkin.id_periodo_ponto,
                checkin.id_colaborador,
                checkin.id_estacao,
                checkin.status_checkin,
                checkin.modo_checkin,
                checkin.hora_leitura,
                checkin.hora_lida,
                usuarios.nome
    
                FROM checkin
                INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
                WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
               
                AND checkin.id_estacao = :id_estacao 
                AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
                ";
                $sql_checa = $conexao->prepare($sql_checa_checkin);
                $sql_checa->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
                $sql_checa->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);
    
                $sql_checa->execute();
    
                $total = $sql_checa->rowCount();


    
                if ($total > 0) {

                    $busca_hora_lida = $sql_checa_c->fetch(PDO::FETCH_ASSOC);
                    $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
    
                    $status_checkin = $busca_hora_lida['status_checkin'] ?? '';
    
                        switch ($status_checkin) {
                            case '1':
                                $nome_status_chekcin ='Tarefa dentro do prazo';
                                break;
    
                                case '2':
                                    $nome_status_chekcin ='Tarefa em atraso';
                                    break;
    
                                    case '3':
                                        $nome_status_chekcin ='Tarefa fora do prazo';
                                        break;
                                        case '5':
                                            $nome_status_chekcin = 'Aguardando validação de API';
                            
                            default:
                            $nome_status_chekcin ='Chekcin sem controle de Horário';
                                break;
                        }

                        if (!in_array($id, $addedIds)) {

                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "sim",
                        'informe_Tarefa' => 'Este checkin é uma Tarefa Única, sendo já realizada hoje por: Usuário: ' . $nome_usuario_chekcin . ' Hora Leitura: ' . $res['hora_leitura'] . ' Hora Lida: ' . $res['hora_lida'] . ' Status_Checkin: ' . $nome_status_chekcin,
                        'Tarefa' => "nao",
                        'periodo' => 'livre',
                        'ciclo' => 'unica',
                        'hora_leitura' => $res['hora_leitura'] ?? '',
                        'data_tarefa_agendada' => $data_tarefa_agendada,
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'hora_lida' => $hora_lida,
                        'usuario_tarefa' => $nome_user_check,
                        'usuario_solicitante' => $nome_usuario_solicitante,
                        "status_hora_leitura"=> $nome_status_chekcin ?? '',
                        'valorTag' => $valorTag,
                    ];

                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }
    
                        } 

                    
                   
                } elseif (!in_array($id, $addedIds)) {
                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "nao",
                        'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                        'Tarefa' => "sim",
                        'periodo' => 'livre',
                        'ciclo' => 'unica',
                        'hora_leitura' => $res['hora_leitura'] ?? '',
                        'data_tarefa_agendada' => $data_tarefa_agendada,
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'usuario_tarefa' => $nome_user_check,
                        'usuario_solicitante' => $nome_usuario_solicitante,
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];

                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }
    
                        } 
                              
            }

            if ($controla_periodo == "2") { // com controle de horario

                 
                try {
                    if (preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $res['hora_leitura'])) {
                        $leitura = new DateTime($res['hora_leitura']);
                        $minima = new DateTime($res['hora_leitura']);
                        $minima->sub(new DateInterval('PT1M'));
                        $now = new DateTime('now');

                        if ($now > $leitura) {
                            $status_hora_leitura = "Prazo Expirado";
                           
                        } elseif ($now >= $minima && $now < $leitura) {
                            $status_hora_leitura = " Dentro do Prazo";
                          
                        } else {
                            $status_hora_leitura = "Próximo do Prazo";
                         
                        }
                    } else {
                        $status_hora_leitura= "Livre, sem prazo e horário de execução.";
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                $saida =  substr($res['hora_leitura'], 0, 5);
                $entrada   = substr($hora_atual, 0, 5);
                $prazo = intervalo($entrada, $saida);

                
            $sql_checa_checkin = "SELECT 
            checkin.id_periodo_ponto,
            checkin.id_colaborador,
            checkin.id_estacao,
            checkin.status_checkin,
            checkin.modo_checkin,
            checkin.hora_leitura,
            checkin.hora_lida,
            usuarios.nome

            FROM checkin
            INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
            WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
           
            AND checkin.id_estacao = :id_estacao 
            AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
            ";
            $sql_checa_c = $conexao->prepare($sql_checa_checkin);
            $sql_checa_c->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
            $sql_checa_c->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);

            $sql_checa_c->execute();

            $total = $sql_checa_c->rowCount();

           

            if ($total > 0) {
                $busca_hora_lida = $sql_checa_c->fetch(PDO::FETCH_ASSOC);
                $hora_lida = $busca_hora_lida['hora_lida'] ?? '';

                $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;
                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }
                    if (!in_array($id, $addedIds)) { 
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "sim",
                    'informe_Tarefa' => 'Esta Tarefa já foi realizada Hoje, sendo seu último registro: Usuário: ' . $nome_usuario_chekcin . ' Hora Leitura: ' .  $res['hora_leitura'] . ' Hora Lida: ' . $hora_lida . 'Status_Checkin: ' . $nome_status_chekcin,
                    'Tarefa' => "nao",
                    'periodo' => 'controlado',
                    'ciclo' => 'unico',
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'data_tarefa_agendada' => $data_tarefa_agendada,
                    'hora_lida' => $hora_lida,
                    'usuario_tarefa' => $nome_user_check,
                    'usuario_solicitante' => $nome_usuario_solicitante,
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 

                
               
            } elseif (!in_array($id, $addedIds)) {

                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "nao",
                    'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                    'Tarefa' => "sim",
                    'periodo' => 'controlado',
                    'ciclo' => 'unico',
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'data_tarefa_agendada' => $data_tarefa_agendada,
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'],
                    'usuario_tarefa' => $nome_user_check,
                    'usuario_solicitante' => $nome_usuario_solicitante,
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 

            }

        
        } // ciclo fecha tarefa única


        if ($ciclo_leitura == '1') {
            $ciclo = "diário";


            if ($controla_periodo == "1") {  // livre sem controle de horario


                $sql_checa_checkin = "SELECT 
                checkin.id_periodo_ponto,
                checkin.id_colaborador,
                checkin.id_estacao,
                checkin.status_checkin,
                checkin.modo_checkin,
                checkin.hora_leitura,
                checkin.hora_lida,
                usuarios.nome
    
                FROM checkin
                INNER JOIN usuarios ON usuarios.id =checkin.id_colaborador
                WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
               
                AND checkin.id_estacao = :id_estacao 
                AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
                ";
              $sql_checa_c1 = $conexao->prepare($sql_checa_checkin);
              $sql_checa_c1->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
              $sql_checa_c1->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);
    
              $sql_checa_c1->execute();
    
                $total = $sql_checa_c1->rowCount();

                try {
                    if (preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $res['hora_leitura'])) {
                        $leitura = new DateTime($res['hora_leitura']);
                        $minima = new DateTime($res['hora_leitura']);
                        $minima->sub(new DateInterval('PT1M'));
                        $now = new DateTime('now');

                        if ($now > $leitura) {
                            $status_hora_leitura = "Prazo Expirado";
                           
                        } elseif ($now >= $minima && $now < $leitura) {
                            $status_hora_leitura = " Dentro do Prazo";
                          
                        } else {
                            $status_hora_leitura = "Próximo do Prazo";
                         
                        }
                    } else {
                        $status_hora_leitura= "Prazo não Calculado, Leitura com horário Livre.";
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                $saida =  substr($res['hora_leitura'], 0, 5);
                $entrada   = substr($hora_atual, 0, 5);
                $prazo = intervalo($entrada, $saida);
    
                if ($total > 0) {

                    $busca_hora_lida = $sql_checa_c1->fetch(PDO::FETCH_ASSOC);
                    $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
                    $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;
                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }
                   
                    if (!in_array($id, $addedIds)) {
                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "sim", // alguem ja fez antes dele
                        'informe_Tarefa' => 'Este checkin é Diário e já foi realizado Hoje, sendo seu último registro: Usuário: ' . $nome_usuario_chekcin . ' Hora Leitura: ' . $res['hora_leitura'] . ' Hora Lida: ' .  $hora_lida .' Status_Checkin: ' . $nome_status_chekcin,
                        'Tarefa' => "nao",
                        'periodo' => 'livre',
                        'ciclo' => 'diário',
                        'hora_leitura' => $res['hora_leitura']  ?? '',
                        'hora_lida' => $hora_lida,
                        'data_tarefa_agendada' => $data_tarefa_agendada,                       
                        'usuario_tarefa' => $nome_user_check, // usuario em que a tarefa foi desegnada
                        'usuario_solicitante' => $nome_usuario_solicitante, // usuario que solicitou/criou a tarefa
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];
                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }
    
                        } 
                   
                } elseif (!in_array($id, $addedIds)) {
                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "nao", // ninguem fez ainda no dia de hoje
                        'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                        'Tarefa' => "sim",
                        'periodo' => 'livre',
                        'ciclo' => 'diario',
                        'hora_leitura' => $res['hora_leitura']  ?? '',
                        'data_tarefa_agendada' => $data_tarefa_agendada ?? '',
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'usuario_tarefa' => $nome_user_check, // usuario em que a tarefa foi designada
                        'usuario_solicitante' => $nome_usuario_solicitante, // usuario que solicitou/criou a tarefa
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];
                    $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 
               

            }

            if ($controla_periodo == "2") { // com controle de horario


             
                try {
                    if (preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $res['hora_leitura'])) {
                        $leitura = new DateTime($res['hora_leitura']);
                        $minima = new DateTime($res['hora_leitura']);
                        $minima->sub(new DateInterval('PT1M'));
                        $now = new DateTime('now');

                        if ($now > $leitura) {
                            $status_hora_leitura = "Prazo Expirado";
                           
                        } elseif ($now >= $minima && $now < $leitura) {
                            $status_hora_leitura = " Dentro do Prazo";
                          
                        } else {
                            $status_hora_leitura = "Próximo do Prazo";
                         
                        }
                    } else {
                        $status_hora_leitura= "Livre, sem prazo e horário de execução.";
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                $saida =  substr($res['hora_leitura'], 0, 5);
                $entrada   = substr($hora_atual, 0, 5);
                $prazo = intervalo($entrada, $saida);



                
            $sql_checa_checkin = "SELECT 
            checkin.id_periodo_ponto,
            checkin.id_colaborador,
            checkin.id_estacao,
            checkin.status_checkin,
            checkin.modo_checkin,
            checkin.hora_leitura,
            checkin.hora_lida,
            usuarios.nome

            FROM checkin
            INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
            WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
            
            AND checkin.id_estacao = :id_estacao 
            AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
            ";
          $sql_checa_c12 = $conexao->prepare($sql_checa_checkin);
          $sql_checa_c12->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
          $sql_checa_c12->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);

            $sql_checa_c12->execute();

            $total = $sql_checa_c12->rowCount();

            if ($total > 0) {
                $busca_hora_lida = $sql_checa_c12->fetch(PDO::FETCH_ASSOC);
                $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
                $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;

                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }
                
                    if (!in_array($id, $addedIds)) {
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "sim", // ninguem fez ainda no dia de hoje
                    'informe_Tarefa' => 'Este checkin é Diário, sua última realização foi realizada por: Usuário: ' . $nome_usuario_chekcin . ' Hora Leitura: ' . $res['hora_leitura'] . ' Hora Lida: ' . $hora_lida . 'Status_Checkin: ' . $nome_status_chekcin,
                    'Tarefa' => "nao",
                    'periodo' => 'controle',
                    'ciclo' => 'diario',
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'hora_lida' => $hora_lida,
                    'data_tarefa_agendada' => $data_tarefa_agendada ?? '',
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                    'usuario_tarefa' => $nome_user_check, // usuario em que a tarefa foi desegnada
                    'usuario_solicitante' => $nome_usuario_solicitante, // usuario que solicitou/criou a tarefa
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 

                
               
            } elseif (!in_array($id, $addedIds)) {
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "nao", // ninguém fez ainda no dia de hoje
                    'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                    'Tarefa' => "sim",
                    'periodo' => 'controle',
                    'ciclo' => 'diario',
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'data_tarefa_agendada' => $data_tarefa_agendada ?? '',                   
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                    'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                    'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 
        }
   } // fecha ciclo de leitura diario


        if ($ciclo_leitura == "2") {
            $ciclo = "semanal";


       

            if ($controla_periodo == "1") {  // livre sem controle de horario
                                // Declaração das variáveis antes do loop

                $sql_checa_checkin = "SELECT 
                checkin.id_periodo_ponto,
                checkin.id_colaborador,
                checkin.id_estacao,
                checkin.status_checkin,
                checkin.modo_checkin,
                checkin.hora_leitura,
                checkin.hora_lida,
                usuarios.nome
    
                FROM checkin
                INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
                WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
                AND checkin.id_estacao = :id_estacao 
                AND DATE_FORMAT(checkin.data_cadastro_checkin, '%Y-%m-%d')  = '$data_atual_periodo'
                ";
                $sql_checa_c23 = $conexao->prepare($sql_checa_checkin);
                $sql_checa_c23->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
                $sql_checa_c23->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);
    
                $sql_checa_c23->execute();
    
                $total =  $sql_checa_c23->rowCount();
    
                if ($total > 0) {
                    $busca_hora_lida = $sql_checa_c23->fetch(PDO::FETCH_ASSOC);
                    $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
                    $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;
                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }
                    
          
                    if (!in_array($id, $addedIds)) { 
                    $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => "sim", // ninguém fez ainda no dia de hoje
                        'informe_Tarefa' => 'Este checkin é Semanal, e já foi realizado hoje através do '. $nome_usuario_chekcin.' às: ' . $hora_lida . ', no horário ' . $hora_lida . '  Próximo será em: ' . intervalo($entrada, $saida),
                        'Tarefa' => "nao",
                        'periodo' => 'livre',
                        'ciclo' => 'semanal',
                        'dias_da_semana' => $nome_dia_semana_periodo,
                        'hora_leitura' => $res['hora_leitura'] ?? '',
                        'hora_lida' => $hora_lida,
                        'data_tarefa_agendada' => $data_tarefa_agendada,                        
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                        'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];
                    
                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }
    
                        } 

                
                    
                   
                } elseif (!in_array($id, $addedIds)) {
                       $taskDetails = [
                        'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                        'ja_foi_hoje' => $Tarefa, // ninguém fez ainda no dia de hoje
                        'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                        'Tarefa' =>  $Tarefa,
                        'periodo' => 'livre',
                        'ciclo' => 'semanal',
                        'dias_da_semana' => $nome_dia_semana_periodo,
                        'hora_leitura' => $res['hora_leitura'] ?? '',
                        'data_tarefa_agendada' => $data_tarefa_agendada,            
                        'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                        'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                        'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                        'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                        'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                        'status_hora_leitura' => $status_hora_leitura ?? '',
                        'valorTag' => $valorTag,
                    ];
                    
                    $id = $res['id_periodo_ponto'];
                    if (!array_key_exists($id, $taskDetailsArray)) {
                        $taskDetailsArray[$id] = $taskDetails;
                    }
    
                        } 
                

            }

            if ($controla_periodo == "2") { // com controle de horario

                            
                try {
                    if (preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $res['hora_leitura'])) {
                        $leitura = new DateTime($res['hora_leitura']);
                        $minima = new DateTime($res['hora_leitura']);
                        $minima->sub(new DateInterval('PT1M'));
                        $now = new DateTime('now');

                        if ($now > $leitura) {
                            $status_hora_leitura = "Prazo Expirado";
                           
                        } elseif ($now >= $minima && $now < $leitura) {
                            $status_hora_leitura = " Dentro do Prazo";
                          
                        } else {
                            $status_hora_leitura = "Próximo do Prazo";
                         
                        }
                    } else {
                        $status_hora_leitura= "Formato de hora inválido.";
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                $saida =  substr($res['hora_leitura'], 0, 5);
                $entrada   = substr($hora_atual, 0, 5);
                $prazo = intervalo($entrada, $saida);



            $sql_checa_checkin = "SELECT 
            checkin.id_periodo_ponto,
            checkin.id_colaborador,
            checkin.id_estacao,
            checkin.status_checkin,
            checkin.modo_checkin,
            checkin.hora_leitura,
            checkin.hora_lida,
            usuarios.nome

            FROM checkin
            INNER JOIN usuarios ON checkin.id_colaborador = usuarios.id
            WHERE checkin.id_periodo_ponto = :id_periodo_ponto 
           
            AND checkin.id_estacao = :id_estacao 
            AND DATE_FORMAT(checkin.data_cadastro_checkin , '%Y-%m-%d')  = '$data_atual_periodo'
            ";
            $sql_checa_c24 = $conexao->prepare($sql_checa_checkin);
            $sql_checa_c24->bindParam(':id_periodo_ponto', $id, PDO::PARAM_STR);
            $sql_checa_c24->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);

            $sql_checa_c24->execute();

            $total =  $sql_checa_c24->rowCount();

            if ($total > 0) {

                $busca_hora_lida = $sql_checa_c24->fetch(PDO::FETCH_ASSOC);
                    $hora_lida = $busca_hora_lida['hora_lida'] ?? '';
                    $status_checkin = $busca_hora_lida['status_checkin'] ?? '';

                    switch ($status_checkin) {
                        case '1':
                            $nome_status_chekcin ='Tarefa dentro do prazo';
                            break;

                            case '2':
                                $nome_status_chekcin ='Tarefa em atraso';
                                break;

                                case '3':
                                    $nome_status_chekcin ='Tarefa fora do prazo';
                                    break;
                                    case '5':
                                        $nome_status_chekcin = 'Aguardando validação de API';
                        
                        default:
                        $nome_status_chekcin ='Aguardando Validação da API';
                            break;
                    }
                    if (!in_array($id, $addedIds)) {
              
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "sim", // ninguém fez ainda no dia de hoje
                    'informe_Tarefa' => 'Este checkin é Semanal, e já foi realizado hoje através do '. $nome_usuario_chekcin.' às: ' . $hora_lida . ', no horário ' . $hora_lida . '  Próximo será em: ' . intervalo($entrada, $saida),
                    'Tarefa' => "nao",
                    'periodo' => 'controle',
                    'ciclo' => 'semanal',
                    'dias_da_semana' => $nome_dia_semana_periodo,
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'hora_lida' =>  $hora_lida,
                    'data_tarefa_agendada' => $data_tarefa_agendada,                   
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                    'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                    'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                
               
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 

               
                           
               
            } elseif (!in_array($id, $addedIds)) {
               
                $taskDetails = [
                    'id_periodo_Tarefa' => $res['id_periodo_ponto'],
                    'ja_foi_hoje' => "nao", // ninguém fez ainda no dia de hoje
                    'informe_Tarefa' => "Esta será a 1ª vez que você fará este checkin hoje.",
                    'Tarefa' => $Tarefa,
                    'periodo' => 'controle',
                    'ciclo' => 'semanal',
                    'dias_da_semana' => $nome_dia_semana_periodo,
                    'hora_leitura' => $res['hora_leitura'] ?? '',
                    'data_tarefa_agendada' => $data_tarefa_agendada,    
                    'titulo_tarefa' => $res['titulo_tarefa'] ?? 'Tarefa sem Título',
                    'detalhes_tarefa' => $res['detalhes_tarefa'] ?? '',
                    'usuario_tarefa' => $nome_user_check, // usuário em que a tarefa foi designada
                    'usuario_solicitante' => $nome_usuario_solicitante, // usuário que solicitou/criou a tarefa
                    'prazo' => 'Próximo em: ' . intervalo($entrada, $saida),
                    'status_hora_leitura' => $status_hora_leitura ?? '',
                    'valorTag' => $valorTag,
                ];
                
                $id = $res['id_periodo_ponto'];
                if (!array_key_exists($id, $taskDetailsArray)) {
                    $taskDetailsArray[$id] = $taskDetails;
                }

                    } 
                } // fecha com controle de horario   
            }  // fecha ciclo de leitura semanal 

           
       

        } // fecha for principal de tarefa agendada

        $jsonResponse = json_encode($taskDetailsArray);

        // Configura o cabeçalho da resposta para indicar que o conteúdo é JSON
        header('Content-Type: application/json');
        
        // Envia a resposta
        echo $jsonResponse;

      }
    
} // fecha tarefa agendada