<?php
header("content-type: application/json");
//require_once '../../../conexao.php';
// Atribui uma conexão PDO

require '../../conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
$tipo_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';

$projeto_atual = (isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';


$modelo_grafico = (isset($_GET['modelo_grafico'])) ? trim($_GET['modelo_grafico']) : '';


$periodo = isset($_GET['periodo']) ? trim($_GET['periodo']) : '';

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
}

$Data_Periodo_Inicio = date('Y-m-d ', strtotime('-7 days', strtotime($Data_Atual_Periodo)));
$Data_Periodo_Fim = date('Y-m-d', strtotime('-14 days', strtotime($Data_Atual_Periodo)));

$Periodo_consulta = date('Y-m-d', strtotime('-7 days', strtotime($Data_Atual_Periodo)));


$tipo_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';


if ($projeto_atual != '' || $projeto_atual != 0 || $projeto_atual != 'undefined' || $projeto_atual != null) {


    $filtro = "AND o.id_obra = ' $projeto_atual' GROUP BY o.nome_obra";
} else {

    $filtro = "GROUP BY o.nome_obra";
}

$sql_personalizado = '';

$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';
if ($tipo_user_sessao == 'supervisor') {

    $sql_personalizado = "AND (e.supervisor = '$id_BD_Colaborador' OR up.id_usuario='$id_usuario_sessao')";
}

if ($tipo_user_sessao == 'ro') {

    $sql_personalizado = "AND (e.ro = '$id_BD_Colaborador' OR up.id_usuario='$id_usuario_sessao')";
}

if ($tipo_user_sessao == 'cliente') {

    $sql_personalizado = "AND (up.id_usuario='$id_usuario_sessao')";
}

//=====[ inicia modelo 1 ] ======
if ($modelo_grafico == '1') { 
    $Data_Atual_Periodo = date_create()->format('Y-m-d');
    $Data_Periodo_Inicio = date('Y-m-d ', strtotime('-7 days', strtotime($Data_Atual_Periodo)));
    $Data_Periodo_Fim = date('Y-m-d', strtotime('-14 days', strtotime($Data_Atual_Periodo)));
    $Periodo_consulta = date('Y-m-d', strtotime('-7 days', strtotime($Data_Atual_Periodo)));

    try {
        $model_sql = ("SELECT COUNT(DISTINCT r.id_rmm) as total_leitura, 
        p.nome_ponto,
        o.nome_obra,
        e.nome_estacao
        FROM rmm r
        INNER JOIN obras o ON o.id_obra = r.id_obra
        INNER JOIN pontos_estacao p ON p.id_ponto = r.id_ponto
        INNER JOIN estacoes e ON e.id_estacao = p.id_estacao
        LEFT JOIN usuarios_projeto up ON up.id_obra = r.id_obra
        WHERE o.status_cadastro='1' AND r.data_leitura >= '{$Periodo_consulta}'
        GROUP BY o.nome_obra
        ");
        // rest of your code...

        
     // Prepare e execute a consulta SQL
     $stmt = $conexao->prepare($model_sql);
     $stmt->execute();
 
     // Recupere todos os resultados como um array associativo
     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
$count = $stmt->rowCount();

    

         // Codifique o resultado como JSON
    $json = json_encode($result, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);

    // Exibir a string JSON
    echo $json;

    } catch (\Exception $e) {
        // handle exception...
    }

                   
 



}

//=== [ finaliza modelo 1 ]====





//=====[ inicia modelo 2 ] ======
/* if ($modelo_grafico == '2') { //(header do grafico com totalizadores)

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

                     
                    WHERE   o.status_cadastro='1'  AND
                   r.data_leitura >='$Periodo_consulta' AND r.data_leitura <= '$Data_Atual_Periodo' 
                    
                    $sql_personalizado
                    $filtro
                    GROUP BY e.id_obra
                    ORDER BY total_leitura DESC");

//print_r($sql_model);
   

    $conta = $sql_model->rowCount();


    if ($conta > 0) {

        $r = $sql_model->fetch(PDO::FETCH_ASSOC);

        $dados = array();


        // $data = array('data' => $Leitura);

        $sql_model_2 = $conexao->query("SELECT  COUNT(DISTINCT r.id_rmm) as total_leitura_final, 
                                    pr.nome_parametro AS 'nome_parametro',
                                        p.nome_ponto,
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

                     
                    WHERE  o.status_cadastro='1'  AND
                   r.data_leitura >='$Periodo_consulta' AND r.data_leitura <= '$Data_Atual_Periodo' 
                    
                    $sql_personalizado
                    $filtro
                    GROUP BY e.id_obra
                    ORDER BY total_leitura DESC");

        $conta_2 = $sql_model_2->rowCount();

        if ($conta_2 > 0) {

            $r_2 = $sql_model_2->fetch(PDO::FETCH_ASSOC);



            $total_leitura_inicial = $r['total_leitura_inicial'];
            $total_leitura_final = $r_2['total_leitura_final'];

        //Variação Percentual = (Vmaior-Vmenor/Vmenor) × 100


            $valor_total_widget_27 = $total_leitura_final - $total_leitura_inicial;

            $porcentagem_total_widget_27 = (($total_leitura_final - $total_leitura_inicial)/ $total_leitura_inicial) *100;
            

            if ($total_leitura_final >= $total_leitura_inicial) {
                $nome_classe = 'svg-icon-success';
                $classe_badge_widget_27 = 'badge-light-success';
                $icone_widget_27 = 'bi bi-arrow-up-short text-success';
            } else {
                $nome_classe = 'svg-icon-danger';
                $classe_badge_widget_27 = 'badge-light-danger';
                $icone_widget_27 = 'bi bi-arrow-down-short text-danger';
            }


            $dados[] = array(

                'valor_total_widget_27' => $valor_total_widget_27,
                'porcentagem_total_widget_27' => round($porcentagem_total_widget_27, 2),
                'classe_widget_27' => $nome_classe,
                'classe_badge_widget_27' => $classe_badge_widget_27,
                'icone_widget_27' => $icone_widget_27,
                'leitura_inicial'=> $total_leitura_inicial,
                'leitura_final' => $total_leitura_final
            );
        }

        if (empty($dados)) {
            echo "Não há dados disponíveis";
        } else {
            // Chame a API ApexCharts aqui

            echo json_encode($dados, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        }

       
    } else {


        $dados='';

        echo json_encode($dados, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
    }
} */

//=== [ finaliza modelo 2 ]====