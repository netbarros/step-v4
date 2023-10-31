<?php
header("content-type: application/json");
//require_once '../../../conexao.php';
// Atribui uma conexão PDO

require '../../conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();


$projeto_atual = (isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';

$usuario_consulta = (isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';

$modelo_grafico = (isset($_GET['modelo_grafico'])) ? $_GET['modelo_grafico'] : '';


$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';



//=====[ inicia modelo 1 ] ======
if ($modelo_grafico == '1') { //(grafico crescimento organico das leituras por obras no período)

    $sql_model = $conexao->query("SELECT  COUNT(s.id_suporte) as total_suporte, 
                                    ts.nome_suporte

                                FROM  suporte s

                           
                            INNER JOIN 
                            tipo_suporte ts ON ts.id_tipo_suporte = s.tipo_suporte

                            WHERE s.quem_abriu='$usuario_consulta'
                           
                  ");

    $conta = $sql_model->rowCount();
//print_r($sql_model);

    if ($conta > 0) {

        $row = $sql_model->fetchALL(PDO::FETCH_ASSOC);

        $dados = array();

        $leitura = array();



        // $data = array('data' => $Leitura);


        foreach ($row as $r) {



            $nome_suporte = $r['nome_suporte'];
            $total_suporte = $r['total_suporte'];


            $dados[] = array(

                'total_suporte' => $total_suporte,
                'nome_suporte' => $nome_suporte

            );
        }


        echo json_encode($dados, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
    } else {


        $dados[] = array(

            'sem_dados' => 0,

        );
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

                     
                    WHERE  o.status_cadastro='1'  AND r.data_leitura BETWEEN '$Data_Periodo_Fim' AND '$Data_Periodo_Inicio'
                     
   $sql_personalizado                   
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

                     
                    WHERE r.data_leitura >= '$Data_Periodo_Inicio'
                     
                     
 $sql_personalizado 
  
 $filtro
ORDER BY total_leitura_final DESC LIMIT 0,1");

        $conta_2 = $sql_model_2->rowCount();

        if ($conta_2 > 0) {

            $r_2 = $sql_model_2->fetch(PDO::FETCH_ASSOC);



            $total_leitura_inicial = $r['total_leitura_inicial'];
            $total_leitura_final = $r_2['total_leitura_final'];

            //Variação Percentual = (VF/VI - 1) × 100

              //Variação Percentual = (Vmaior-Vmenor/Vmenor) × 100


              $valor_total_widget_27 = $total_leitura_final - $total_leitura_inicial;

              $porcentagem_total_widget_27 = (($total_leitura_final - $total_leitura_inicial)/ $total_leitura_inicial) *100;
            /*   

            $valor_total_widget_27 = $total_leitura_final - $total_leitura_inicial;

            $porcentagem_total_widget_27 = ($total_leitura_final / $total_leitura_inicial - 1)*100; */
            

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


        echo json_encode($dados, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
    } else {


        $dados[] = array(

            'sem_dados' => 0,

        );

        echo json_encode($dados, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
    }
}

//=== [ finaliza modelo 2 ]====