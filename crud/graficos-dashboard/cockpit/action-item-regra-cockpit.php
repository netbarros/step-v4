<?php
require_once '../../../conexao.php';
// Atribui uma conexão PDO

$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();

$id_usuario = isset($_COOKIE['id_usuario_sessao']) ? $_COOKIE['id_usuario_sessao'] : '';

$id_estacao = isset($_POST['projeto_cockpit']) ? $_POST['projeto_cockpit'] : '';

$acao = isset($_POST['acao']) ? $_POST['acao'] : '';

$id_cockpit = isset($_POST['id_cockpit']) ? $_POST['id_cockpit'] : '';

$id_item = isset($_POST['id_item']) ? $_POST['id_item'] : '';

$nome_cockpit = isset($_POST['nome_cockpit']) ? $_POST['nome_cockpit'] : '';

$modelo_grafico = isset($_POST['modelo_grafico']) ? $_POST['modelo_grafico'] : '';

$indicadores_cockpit = isset($_POST['indicadores_cockpit']) ? $_POST['indicadores_cockpit'] : '';

$periodo_analise_regra = isset($_POST['periodo_analise_regra']) ? $_POST['periodo_analise_regra'] : '0';

$status_cockpit = isset($_POST['status_cockpit']) ? $_POST['status_cockpit'] : '1';


if ($modelo_grafico == '' && $acao != 'apagar') {

    $retorno = array('codigo' => '0', 'retorno' => "Impossível Prosseguir, não foi seleiconado o Modelo de Gráfico para Acompanhamento!");

    echo json_encode($retorno);

    $conexao = null;
    exit;
}


if ($acao == 'novo_item') {


    // inclui o novo item do CockPit

    $stmt = $conexao->prepare('INSERT INTO  cockpit (
        id_usuario,
        estacao_selecionada_regra,
        nome_regra, 
        modelo_grafico,
        periodo_analise_regra,
         status_cockpit
         ) VALUES(
        :id_usuario,
        :estacao_selecionada_regra,
        :nome_cockpit,
        :modelo_grafico,
        :periodo_analise_regra,
        :status_cockpit)');
    $stmt->execute(array(

        ':id_usuario' => $id_usuario,
        ':estacao_selecionada_regra' => $id_estacao,
        ':nome_cockpit' => $nome_cockpit,
        ':modelo_grafico' => $modelo_grafico,
        ':periodo_analise_regra' => $periodo_analise_regra,
        ':status_cockpit' => $status_cockpit // Aguardando Análise (tudo que depende da API verificar para disparar os alertas de email e sms, terão status 5, seja suporte, leitura ou checkin)

    ));

    $count = $stmt->rowCount();

    $ultimo_id_cockpit = $conexao->lastInsertId();



    if ($count > 0) {

        // verifica se há mais indicadores para serem controlados, de acordo com o modelo do grafico, todos menos o modelo 5, envolve a possibilidade de mais de um indicador

        $retorno = '';


        if($modelo_grafico!='4' AND $modelo_grafico!='5'){
            foreach ($_POST['indicadores_cockpit'] as $value) {

                $id_indicador = $value;

                $sql = $conexao->query(" INSERT INTO  cockpit_lista_indicadores 
                    
                                                            (id_cockpit,id_indicador) 

                                                            VALUES 

                                                            ('$ultimo_id_cockpit','$id_indicador')")

                    or die(print_r($conexao->errorInfo(), true));


                if ($sql) {

                    $retorno = 'ok';
                }

            }// fecha o foreach
            // incluo no foreach o laco para inclusao de todos os indicadores relacionados na tabela cockpit_lista_indicadores:

        } // fecha modelos diferentes de 4 e 5

        else{


             foreach ($_POST['indicadores_cockpit'] as $value) {


                 $id_indicador = $value;

            $sql = $conexao->query("UPDATE cockpit  SET indicador_unico_regra='$id_indicador' WHERE id_cockpit='$ultimo_id_cockpit'");

            if ($sql) {

                    $retorno = 'ok';
                }

            }
            
        }

        if ($retorno == 'ok') {

            $retorno = array('codigo' => '1', 'retorno' => "Cockpit Cadastrado com Sucesso!");

            echo json_encode($retorno);

            $conexao = null;
            exit;
        } else {

            $retorno = array('codigo' => '0', 'retorno' => "Falha ao Salvar os Indicadores de Monitoramento!");

            echo json_encode($retorno);

            $conexao = null;
            exit;
        }
    }
}


// Elimina Cockpit

if ($acao == 'apagar') {


    $apaga_cockpit = $conexao->query("DELETE FROM cockpit WHERE id_cockpit=$id_cockpit");


    $sql_confirma = $conexao->query("SELECT id_cockpit FROM cockpit_lista_indicadores WHERE id_cockpit=$id_cockpit");

    if ($sql_confirma) {

        $apaga_indicadores_cockpit = $conexao->query("DELETE FROM cockpit_lista_indicadores WHERE id_cockpit=$id_cockpit");
    }

    $retorno = array('codigo' => '1', 'retorno' => "Item de Monitoramento do Cockpit, apagado com Sucesso!");

    echo json_encode($retorno);

    $conexao = null;
    exit;
}
