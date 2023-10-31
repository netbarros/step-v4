<?php
// Set the JSON header ==[ Cálculo de Indicadores ]=====
header('Content-Type: application/json; charset=utf-8');
//require_once '../../../conexao.php';
// Atribui uma conexão PDO
require '../../../conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();

if (!ini_get('date.timezone')) {
    date_default_timezone_set('America/Sao_Paulo');
}

//==================================================================================================================================================


$id_usuario_sessao = isset($_COOKIE['id_usuario_sessao']) ? $_COOKIE['id_usuario_sessao'] : '';


// Verifico se ha algum cálculo Dinâmico de Monitoramento, Cadastrado e Ativo para a estação selecionada    
if ($id_usuario_sessao != '') {


    $sql = $conexao->prepare(" SELECT c.*,
                                o.nome_obra,
                            p.nome_ponto,
                            e.nome_estacao

                         FROM cockpit  c

                          INNER JOIN 
                          estacoes e ON e.id_estacao = c.estacao_selecionada_regra
                         INNER JOIN 
                        pontos_estacao p ON p.id_estacao = c.estacao_selecionada_regra
                        INNER JOIN
                        obras o ON o.id_obra = p.id_obra
                         
     WHERE c.id_usuario ='$id_usuario_sessao' AND c.status_cockpit='1' group by c.id_cockpit");

    $sql->execute();

    $count = $sql->rowCount();

    //print_r($sql);

    $retorna_dados = '';
    $retorna_dados_g = '';

    if ($count > 0) {


        $Data_Atual_Periodo = date_create()->format('Y-m-d');


        foreach ($sql as $res) {


            $id_cockpit = $res['id_cockpit'];
            $nome_obra = $res['nome_obra'];
            $nome_estacao = $res['nome_estacao'];
            $nome_ponto = $res['nome_ponto'];
            $id_usuario = $res['id_usuario'];
            $status_cockpit  = $res['status_cockpit'];
            $nome_regra = $res['nome_regra'];
            $modelo_grafico = $res['modelo_grafico'];
            $periodo_analise_regra = $res['periodo_analise_regra'];

            $dados[] = array(

                'id_cockpit' => $id_cockpit,
                'nome_regra' => $nome_regra,
                'nome_obra' => $nome_obra,
                'nome_estacao' => $nome_estacao,
                'nome_ponto' => $nome_ponto,
                'modelo_grafico' => $modelo_grafico,
                'status_cockpit' => $status_cockpit

            );
        }


        echo json_encode($dados,  JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        $conexao=null;
    }
}
