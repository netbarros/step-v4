<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';
header("content-type: application/json");




//widget 28 - Total de Leituras Realizadas

$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';

$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';

$projeto_atual = (isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';

$modelo_grafico = (isset($_GET['modelo_grafico'])) ? $_GET['modelo_grafico'] : '';


$periodo = trim($_GET['periodo']) ? $_GET['periodo'] : '';

$Data_Atual_Periodo = date_create()->format('Y-m-d');

switch ($periodo) {
    case 0:
        $nome_periodo = 'Último Valor Informado';
        $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-1 days', strtotime($Data_Atual_Periodo)));

        break;
    case 7:
        $nome_periodo = 'Média dos Últimos 7 dias';
        $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-7 days', strtotime($Data_Atual_Periodo)));
        $calcula_media = "AVG(r.leitura_entrada) as media_leitura_periodo,";
        break;
    case 30:
        $nome_periodo = 'Média dos Últimos 30 dias';
        $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-30 days', strtotime($Data_Atual_Periodo)));
        $calcula_media = "AVG(r.leitura_entrada) as media_leitura_periodo,";
        break;

    case 180:
        $nome_periodo = 'Média dos Últimos 6 Meses';
        $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-180 days', strtotime($Data_Atual_Periodo)));
        $calcula_media = "AVG(r.leitura_entrada) as media_leitura_periodo,";
        break;
}


$Data_1_6_meses = date('Y-m-d H:i:s', strtotime('-180 days', strtotime($Data_Atual_Periodo)));
$Data_2_6_meses = date('Y-m-d H:i:s', strtotime('-180 days', strtotime($Data_1_6_meses)));



if($projeto_atual!=''){


    $filtro = "GROUP BY r.id_ponto";
    $filtro_select="AND o.id_obra = '$projeto_atual'";

} else{

    $filtro = 'GROUP BY e.id_obra';
    $filtro_select='';
}



//=====[ inicia modelo 1 ] ======
if ($modelo_grafico == '1') { //(Total de Leituras Realizadas no período)

    $sql_model = $conexao->query("SELECT date_format(r.data_leitura, '%M') as datax, 
    COUNT(r.id_rmm) as total_leitura,r.data_leitura
              FROM  rmm r
              INNER JOIN pontos_estacao pt ON pt.id_ponto = r.id_ponto
              INNER JOIN obras o ON o.id_obra = pt.id_obra
                    WHERE 
                     o.status_cadastro='1'  AND
                    DATE_FORMAT(r.data_leitura, '%Y-%m-%d') >= '$Data_Intervalo_Periodo' 
                    $filtro_select
 GROUP BY date_format(r.data_leitura, '%M') ORDER BY r.data_leitura ASC
");

    $conta = $sql_model->rowCount();


    // print_r($sql_model);


    if ($conta > 0) {

        $row = $sql_model->fetchALL(PDO::FETCH_ASSOC);

        $dados = array();


       
        
        // $data = array('data' => $Leitura);


        foreach ($row as $r) {
            $cal = $r['datax'];
            //$theDate    = new DateTime($r['data_leitura']);
            $data_leitura = $cal;
            //$data_leitura_X =  strtotime($r['data_leitura']) * 1000;
            //$hora_min =  date('H:i', strtotime($value['data_leitura']));
            //$data_leitura =  date('d/m', strtotime($r['data_leitura']));
           
            $datetime = new DateTime($r['datax']);
                       
           // $data_leitura = $formatter->format($datetime);


            $total_leitura = $r['total_leitura'];

            $dados[] = array(

                'data_leitura' => $data_leitura,
                'total_leitura' => $total_leitura

            );
        }






        echo json_encode($dados, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        $conexao=null;
    } else {


        $dados[] = array(

            'sem_dados' => 0,

        );

        echo json_encode($dados, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        $conexao=null;
    }
}

//=== [ finaliza modelo 1 ]====






//=====[ inicia modelo 2 ] ======
if ($modelo_grafico == '2') { //(header do grafico com totalizadores)

    $sql_model = $conexao->query("SELECT  COUNT(r.id_rmm) as total_leitura_inicial, 
                                    pr.nome_parametro AS 'nome_parametro',
                                        p.nome_ponto,
                                        r.id_parametro,
                                        o.nome_obra,
                                        e.nome_estacao

                                FROM  rmm r

                           
                            INNER JOIN 
                            parametros_ponto pr ON pr.id_parametro = r.id_parametro

                            INNER JOIN
                            pontos_estacao p ON p.id_ponto = pr.id_ponto

                            INNER JOIN 
                            obras o ON o.id_obra = p.id_obra

                            INNER JOIN 
                            estacoes e ON e.id_estacao = p.id_estacao                          

                     
                    WHERE  o.status_cadastro='1'  AND r.data_leitura >= '$Data_1_6_meses' 
                     
                     
$filtro
ORDER BY total_leitura_inicial DESC LIMIT 0,1");

    //print_r($sql_model);


    $conta = $sql_model->rowCount();


    if ($conta > 0) {

        $r = $sql_model->fetch(PDO::FETCH_ASSOC);

        $dados = array();


        // $data = array('data' => $Leitura);

        $sql_model_2 = $conexao->query("SELECT  COUNT(r.id_rmm) as total_leitura_final, 
                                    pr.nome_parametro AS 'nome_parametro',
                                        p.nome_ponto,
                                        r.id_parametro,
                                        o.nome_obra,
                                        e.nome_estacao,
                                        e.id_estacao

                                FROM  rmm r

                           
                            INNER JOIN 
                            parametros_ponto pr ON pr.id_parametro = r.id_parametro

                            INNER JOIN
                            pontos_estacao p ON p.id_ponto = pr.id_ponto

                            INNER JOIN 
                            obras o ON o.id_obra = p.id_obra

                            INNER JOIN 
                            estacoes e ON e.id_estacao = p.id_estacao                          

                     
                    WHERE  o.status_cadastro='1'  AND r.data_leitura BETWEEN '$Data_2_6_meses' AND '$Data_1_6_meses'
                     
                     
 GROUP BY e.id_estacao
ORDER BY total_leitura_final DESC LIMIT 0,2");

        $conta_2 = $sql_model_2->rowCount();

        //print_r($sql_model_2);

        if ($conta_2 > 0) {

            $r_2 = $sql_model_2->fetch(PDO::FETCH_ASSOC);


/*===========================================================================//

Para calcular a variação percentual entre o valor final e o inicial em porcentagem, você pode utilizar a seguinte fórmula:

Variação Percentual = ((Valor Final - Valor Inicial) / Valor Inicial) * 100

Para exibir a diferença em porcentagem, basta modificar a fórmula para subtrair o valor inicial do valor final ao calcular a variação percentual, assim:

Diferença Percentual = ((Valor Final - Valor Inicial) / Valor Final) * 100

 ==============================================================================*/


             $total_leitura_inicial = $r['total_leitura_inicial'];
             $total_leitura_final = $r_2['total_leitura_final'];
             
             $porcentagem_total_widget_28 = '';
            $diferenca = $total_leitura_final - $total_leitura_inicial;
            
            if ($total_leitura_inicial != 0) {
                $variacao_percentual = (($diferenca >= 0) ? $diferenca : abs($diferenca)) / $total_leitura_inicial * 100;
                $porcentagem_total_widget_28 = number_format($variacao_percentual, 2);
            } else {
                $porcentagem_total_widget_28 = '#';
            }
            
            if ($diferenca < 0) {
                $diferenca = abs($diferenca);
            }

            $valor_total_widget_28 = $total_leitura_final - $total_leitura_inicial;

           
            if ($valor_total_widget_28 < 0) {
                $valor_total_widget_28 = abs($valor_total_widget_28);
            }

          
            // Regra para exibir
            if ($total_leitura_inicial > $total_leitura_final) {

         
            $nome_classe = 'svg-icon-danger';
            $classe_badge_widget_28 = 'badge-light-danger';
            $icone_widget_28 = 'bi bi-arrow-down-short text-danger';
          

            } else  {

           
            $nome_classe = 'svg-icon-success';
            $classe_badge_widget_28 = 'badge-light-success';
            $icone_widget_28 = 'bi bi-arrow-up-short text-success';

            }


            $dados[] = array(

                'valor_total_widget_28' => $valor_total_widget_28,
                'porcentagem_total_widget_28' => $porcentagem_total_widget_28,
                'classe_widget_28' => $nome_classe,
                'classe_badge_widget_28' => $classe_badge_widget_28,
                'icone_widget_28' => $icone_widget_28,
                'leitura_inicial' => $total_leitura_inicial,
                'leitura_final' => $total_leitura_final
            );
        }


        echo json_encode($dados, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        $conexao=null;
    } else {


        $dados[] = array(

            'sem_dados' => 0,

        );

       echo json_encode($dados, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        $conexao=null;
    }
}

//=== [ finaliza modelo 2 ]====