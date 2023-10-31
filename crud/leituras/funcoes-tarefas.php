<?php

         
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

function getHoraLeituraAgendada($conexao, $id_tarefa) {
    $stmt = $conexao->prepare("SELECT hora_leitura FROM periodo_ponto WHERE id_periodo_ponto = :id_tarefa");
    $stmt->bindParam(':id_tarefa', $id_tarefa, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['hora_leitura'];
    } else {
        return false;
    }
}


function getModoCheckinPeriodo($conexao, $id_tarefa) {
    $stmt = $conexao->prepare('SELECT modo_checkin_periodo FROM periodo_ponto WHERE id_periodo_ponto = :id_tarefa');
    $stmt->bindParam(':id_tarefa', $id_tarefa, PDO::PARAM_INT);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        return $row['modo_checkin_periodo'];
    } else {
        return null; // ou outra valor que indique que não foi encontrado
    }
}

function fetchRoAndSupervisor($conexao, $plcode_lido) {
    // Primeiro, tente buscar ro e supervisor da tabela estacoes
    $sql_estacoes = "SELECT e.ro, e.supervisor FROM estacoes e 
                     INNER JOIN pontos_estacao pt ON pt.id_ponto = ? 
                     WHERE e.id_estacao = pt.id_estacao";
    $stmt = $conexao->prepare($sql_estacoes);
    $stmt->execute([$plcode_lido]);
    $result_estacoes = ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;

    // Se ro ou supervisor for NULL, então busque da tabela usuarios_projeto
    if (!$result_estacoes || is_null($result_estacoes['ro']) || is_null($result_estacoes['supervisor'])) {
        $sql_usuarios_projeto = "SELECT up.nivel FROM usuarios_projeto up
                                  INNER JOIN estacoes e ON e.id_obra = up.id_obra
                                  INNER JOIN pontos_estacao pt ON pt.id_estacao = e.id_estacao
                                  WHERE pt.id_ponto = ? AND up.responsavel = 1";
        $stmt = $conexao->prepare($sql_usuarios_projeto);
        $stmt->execute([$plcode_lido]);
        $result_usuarios_projeto = ($stmt->rowCount() > 0) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        foreach ($result_usuarios_projeto as $row) {
            if ($row['nivel'] === 'ro' && is_null($result_estacoes['ro'])) {
                $result_estacoes['ro'] = $row['nivel'];
            }

            if ($row['nivel'] === 'supervisor' && is_null($result_estacoes['supervisor'])) {
                $result_estacoes['supervisor'] = $row['nivel'];
            }
        }
    }

    return $result_estacoes ? $result_estacoes : false;
}
//** tratar erro de retorno na tentativa de inclusao da leitura e do log  *//
function insertIntoLogLeitura($conexao, $usuario_tarefa, $chave_unica, $id_estacao, $acao_log) {
    $tipo_log = '1';
    $sql_log = "INSERT INTO log_leitura (
            chave_unica,
          id_usuario, 
          acao_log,
          estacao_logada,
          tipo_log) 
        VALUES (
            :chave_unica,
            :usuario_tarefa,
            :acao_log,
            :estacao_logada,
            :tipo_log
            )";
    $conexao->prepare($sql_log)->execute([
        ':chave_unica' => $chave_unica,
        ':usuario_tarefa' => $usuario_tarefa,
        ':acao_log' => $acao_log,
        ':estacao_logada' => $id_estacao,
        ':tipo_log' => $tipo_log
    ]);


  }
// insere na tabela rmm

function insertIntoRmm($conexao, $id_obra, $chave_unica, $plcode_lido, $usuario_tarefa, $latitude_user, $longitude_user, $parametro_lido, $valorLido, $ro, $supervisor) {
    $status_leitura_inicial = '5';
    $stmt = $conexao->prepare('INSERT INTO  rmm (
    id_obra,
    chave_unica,
    id_ponto, 
    id_parametro,
    leitura_entrada, 
    id_operador,
    id_ro,
    id_supervisor,
    latitude_user,
    longitude_user,
    status_leitura
    ) VALUES(
     :id_obra,
    :chave_unica ,
    :id_ponto,
    :id_parametro,
    :leitura_entrada,
    :id_operador,
    :id_supervisor, 
    :id_ro,
    :latitude_user,
    :longitude_user,        
    :status_leitura)');
    $stmt->execute([
        ':id_obra' => $id_obra,
        ':chave_unica' => $chave_unica,
        ':id_ponto' => $plcode_lido,
        ':id_parametro' => $parametro_lido,
        ':leitura_entrada' => $valorLido,
        ':id_operador' => $usuario_tarefa,
        ':id_ro' => $ro,
        ':id_supervisor' => $supervisor,
        ':latitude_user' => $latitude_user,
        ':longitude_user' => $longitude_user,
        ':status_leitura' => $status_leitura_inicial
    ]);

    if ($stmt->rowCount() > 0) {
        return $conexao->lastInsertId();
    } else {
        return false;
    }
}

 
  // insere na tabela checkin as leituras quando for Tarefa Leitura
  function insertIntoCheckin($conexao, 
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
                            $chave_unica) {
    $status_checkin_inicial = '5';
    $stmt = $conexao->prepare('INSERT INTO checkin (
        tipo_checkin,
        modo_checkin,
        id_rmm,
        id_periodo_ponto,
        latitude_operador,
        longitude_operador,
        id_obra,
        id_estacao,
        id_ponto,
        id_parametro,
        id_colaborador,
        hora_leitura,
        hora_lida,
        chave_unica,
        status_checkin
    ) VALUES (
        :tipo_checkin,
        :modo_checkin,
        :id_rmm,
        :id_periodo_ponto,
        :latitude_operador,
        :longitude_operador,
        :id_obra,
        :id_estacao,
        :id_ponto,
        :id_parametro,
        :id_colaborador,
        :hora_leitura,
        :hora_lida,
        :chave_unica,
        :status_checkin
    )');
    
    $stmt->execute([
        ':tipo_checkin' => $tipo_checkin,
        ':modo_checkin' => $modo_checkin_periodo,
        ':id_rmm' => $lastInsertedId,
        ':id_periodo_ponto' => $id_tarefa,
        ':latitude_operador' => $latitude_user,
        ':longitude_operador' => $longitude_user,
        ':id_obra' => $id_obra,
        ':id_estacao' => $id_estacao,
        ':id_ponto' => $plcode_lido,
        ':id_parametro' => $parametro_lido,
        ':id_colaborador' => $usuario_tarefa,
        ':hora_leitura' => $hora_leitura_agendada,
        ':hora_lida' => $hora_lida,
        ':chave_unica' => $chave_unica,
        ':status_checkin' => $status_checkin_inicial
    ]);
  
    return $stmt->rowCount() > 0;
}



// insere os dados em checkin quando a Tarefa for Presencial
function gravarCheckin($conexao, $tipo_checkin, $modo_checkin_periodo, $id_tarefa, $latitude_user, $longitude_user, $id_obra, $id_estacao, $plcode_lido, $usuario_tarefa, $hora_leitura_agendada, $hora_lida, $chave_unica) {
    $status_checkin_inicial = '5';
    $sql_grava_checkin = "INSERT INTO checkin(
      tipo_checkin,
      id_periodo_ponto,
      modo_checkin,
      id_obra,
      id_estacao,
      id_ponto,
      id_colaborador,
      hora_leitura,
      hora_lida,
      chave_unica,
      status_checkin,
      latitude_operador,
      longitude_operador
    ) VALUES(
      :tipo_checkin,
      :id_periodo_ponto,
      :modo_checkin,
      :id_obra,
      :id_estacao,
      :id_ponto,
      :id_colaborador,
      :hora_leitura,
      :hora_lida,
      :chave_unica,
      :status_checkin,
      :latitude_operador,
      :longitude_operador
    )";

    $stmt = $conexao->prepare($sql_grava_checkin);
    $stmt->bindParam(':tipo_checkin', $tipo_checkin);
    $stmt->bindParam(':id_periodo_ponto', $id_tarefa);
    $stmt->bindParam(':modo_checkin', $modo_checkin_periodo);
    $stmt->bindParam(':id_obra', $id_obra);
    $stmt->bindParam(':id_estacao', $id_estacao);
    $stmt->bindParam(':id_ponto', $plcode_lido);
    $stmt->bindParam(':id_colaborador', $usuario_tarefa);
    $stmt->bindParam(':hora_leitura', $hora_leitura_agendada);
    $stmt->bindParam(':hora_lida', $hora_lida);
    $stmt->bindParam(':chave_unica', $chave_unica);
    $stmt->bindParam(':status_checkin', $status_checkin_inicial);
    $stmt->bindParam(':latitude_operador', $latitude_user);
    $stmt->bindParam(':longitude_operador', $longitude_user);

    return $stmt->execute(); // Retorna true se for bem-sucedido, false caso contrário
}



?>