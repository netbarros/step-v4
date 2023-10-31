<?php
header("content-type: application/json");
//require_once '../../../conexao.php';
// Atribui uma conexão PDO

require '../../../conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();




$id_cockpit = (isset($_GET['id_cockpit'])) ? $_GET['id_cockpit'] : '';


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
}






//=====[ inicia modelo 1 ] ======
if ($id_cockpit != '' && $modelo_grafico == '1') { //(com dia e mês e média de leitura por dia)

    $sql_model = $conexao->query("SELECT  pr.nome_parametro AS 'nome_parametro',
                                        r.leitura_entrada,
                                        r.data_leitura,
                                        pr.origem_leitura_parametro,
                                        pr.controle_concentracao,
                                        u.nome_unidade_medida,
                                        pr.concen_min, 
                                        pr.concen_max,
                                        p.nome_ponto,
                                        r.id_rmm,
                                        r.id_parametro

                                FROM  rmm r

                            INNER JOIN 
                            cockpit_lista_indicadores cl ON r.id_parametro = cl.id_indicador

                            INNER JOIN 
                            parametros_ponto pr ON pr.id_parametro = r.id_parametro

                            INNER JOIN
                            pontos_estacao p ON p.id_ponto = pr.id_ponto

                            INNER JOIN 
                            unidade_medida u ON u.id_unidade_medida = pr.unidade_medida

                    WHERE 
                    DATE_FORMAT(r.data_leitura, '%Y-%m-%d') >= '$Data_Intervalo_Periodo' 
                    AND cl.id_cockpit = '$id_cockpit' 
 
ORDER BY r.data_leitura ASC ;");

    $conta = $sql_model->rowCount();


    if ($conta > 0) {

        $row = $sql_model->fetchALL(PDO::FETCH_ASSOC);

//print_r($sql_model);

//exit;
       
        $dados = array();

        // $data = array('data' => $Leitura);




        foreach ($row as $r) {


            //$theDate    = new DateTime($r['data_leitura']);
            //$data_leitura = $theDate->format('Y-m-d H:i:s');
            $data_leitura_X =  strtotime($r['data_leitura']) * 1000;
            //$hora_min =  date('H:i', strtotime($value['data_leitura']));
            $data_leitura =  date('d/m H:i', strtotime($r['data_leitura']));
            $data_leitura_hora =  date('d/m H:i', strtotime($r['data_leitura']));
            //$data_leitura =  strtotime($r['data_leitura']) * 1000;
            $Origem_Leitura = $r['origem_leitura_parametro'];
            $parametro = $r['nome_parametro'];
            $nome_ponto = $r['nome_ponto'];

            $Leitura = $r['leitura_entrada'];

            $id_parametro = $r['id_parametro'];

            $nome_unidade_medida =  $r['nome_unidade_medida'];

            $dados[] = array(

                'data' => $Leitura,
                'name' => $nome_ponto . ' <br> ' . $parametro . ' <b>(' . $nome_unidade_medida . ') </b>',
                'categories' => $data_leitura

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
if ($id_cockpit != '' && $modelo_grafico == '2') { //(com dia, mês e hora de leitura detalhada)

    $sql_model = $conexao->query("SELECT  pr.nome_parametro AS 'nome_parametro',
                                        r.leitura_entrada,
                                        r.data_leitura,
                                        pr.origem_leitura_parametro,
                                        pr.controle_concentracao,
                                        u.nome_unidade_medida,
                                        pr.concen_min, 
                                        pr.concen_max,
                                        p.nome_ponto,
                                        r.id_rmm,
                                        r.id_parametro

                                FROM  rmm r

                            INNER JOIN 
                            cockpit_lista_indicadores cl ON r.id_parametro = cl.id_indicador

                            INNER JOIN 
                            parametros_ponto pr ON pr.id_parametro = r.id_parametro

                            INNER JOIN
                            pontos_estacao p ON p.id_ponto = pr.id_ponto

                            INNER JOIN 
                            unidade_medida u ON u.id_unidade_medida = pr.unidade_medida

                    WHERE 
                    DATE_FORMAT(r.data_leitura, '%Y-%m-%d') >= '$Data_Intervalo_Periodo' 
                    AND cl.id_cockpit = '$id_cockpit' 
 
ORDER BY r.data_leitura ASC ;");

    $conta = $sql_model->rowCount();


    if ($conta > 0) {

        $row = $sql_model->fetchALL(PDO::FETCH_ASSOC);

        $dados = array();


        // $data = array('data' => $Leitura);

        // laço para pegar o nome do indicador
        foreach ($row as $r) {


            //$theDate    = new DateTime($r['data_leitura']);
            //$data_leitura = $theDate->format('Y-m-d H:i:s');
            $data_leitura_X =  strtotime($r['data_leitura']) * 1000;
            //$hora_min =  date('H:i', strtotime($value['data_leitura']));
            $data_leitura =  date('d/m H:i', strtotime($r['data_leitura']));
            //$data_leitura =  strtotime($r['data_leitura']) * 1000;
            $Origem_Leitura = $r['origem_leitura_parametro'];
            $parametro = $r['nome_parametro'];
            $nome_ponto = $r['nome_ponto'];

            $Leitura = $r['leitura_entrada'];

            $id_parametro = $r['id_parametro'];

            $nome_unidade_medida =  $r['nome_unidade_medida'];

            $dados[] = array(

                'data' => $Leitura,
                'name' => $nome_ponto . ' <br> ' . $parametro . ' <b>(' . $nome_unidade_medida . ') </b>',
                'categories' => $data_leitura

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


//=== [ inicio modelo 3 ]===


if ($id_cockpit != '' && $modelo_grafico === '3') { // ??

    $sql_model = $conexao->query("SELECT 
                                        cl.id_indicador,
                                        pr.nome_parametro,
                                        r.leitura_entrada,
                                        r.data_leitura,
                                        pr.origem_leitura_parametro,
                                        pr.controle_concentracao,
                                        u.nome_unidade_medida,
                                        pr.concen_min, 
                                        pr.concen_max,
                                        p.nome_ponto,
                                        r.id_rmm

                                FROM cockpit_lista_indicadores cl

                            INNER JOIN 
                            rmm r ON r.id_parametro = cl.id_indicador 

                            INNER JOIN 
                            parametros_ponto pr ON pr.id_parametro = r.id_parametro

                            INNER JOIN
                            pontos_estacao p ON p.id_ponto = pr.id_ponto

                            INNER JOIN 
                            unidade_medida u ON u.id_unidade_medida = pr.unidade_medida

                    WHERE 
                    cl.id_cockpit = '$id_cockpit' 
                    AND  DATE_FORMAT(r.data_leitura, '%Y-%m-%d') >= '$Data_Intervalo_Periodo' 
                   
                   
ORDER BY r.data_leitura ASC");

    $conta = $sql_model->rowCount();



    if ($conta > 0) {

        $row = $sql_model->fetchALL(PDO::FETCH_ASSOC);

        $dados = array();
        $dados_leituras = array();
        $pega_data = array();


        foreach ($row as $r) {

            //$theDate    = new DateTime($r['data_leitura']);
            //$data_leitura = $theDate->format('Y-m-d H:i:s');
            //$data_leitura =  strtotime($r['data_leitura']) * 1000;
            //$hora_min =  date('H:i', strtotime($value['data_leitura']));
            $data_leitura =  date('d/m H:i', strtotime($r['data_leitura']));
            //$data_leitura =  strtotime($r['data_leitura']) * 1000;
            $Origem_Leitura = $r['origem_leitura_parametro'];
            $parametro = $r['nome_parametro'];
            $nome_ponto = $r['nome_ponto'];

            $nome_unidade_medida =  $r['nome_unidade_medida'];

            $Leitura = $r['leitura_entrada'];

            $dados[] = array(

                'data' => $Leitura,
                'name' => $nome_ponto . ' <br> ' . $parametro . ' <b><br>(' . $nome_unidade_medida . ') </b>',
                'categories' => $data_leitura

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

//===[  finaliza modelo 3 ]===


//=====[ inicia modelo 4 ] ======  // ultimo dado e totalizador diário
if ($modelo_grafico == '4') {

    $datetime = 'now';
    $theDate    = new DateTime($datetime);
    $data_atual = $theDate->format('Y-m-d');

    $sql_model = $conexao->query("SELECT SUM(r.leitura_entrada) as Leitura_Acumulada,
     r.leitura_entrada,
     r.leitura_saida,
     r.data_leitura, 
     pr.origem_leitura_parametro,
      pr.controle_concentracao,
      pr.nome_parametro,
       u.nome_unidade_medida,
       pr.concen_min, 
       pr.concen_max,
       e.nome_estacao,
       o.nome_obra,
       p.nome_ponto

    FROM rmm r
    INNER JOIN
cockpit c ON r.id_parametro = c.indicador_unico_regra
INNER JOIN 
parametros_ponto pr ON pr.id_parametro = c.indicador_unico_regra
INNER JOIN
estacoes e ON e.id_estacao = c.estacao_selecionada_regra 
INNER JOIN
obras o ON o.id_obra = e.id_obra
INNER JOIN
pontos_estacao p ON p.id_ponto = pr.id_ponto
INNER JOIN
unidade_medida u ON u.id_unidade_medida = pr.unidade_medida

WHERE 
c.id_cockpit='$id_cockpit' 
 AND r.id_parametro = c.indicador_unico_regra
 AND 
  DATE_FORMAT(r.data_leitura, '%Y-%m-%d') >= '$Data_Intervalo_Periodo' 
                  
GROUP BY r.data_leitura 
ORDER BY r.data_leitura ASC ");

    $conta = $sql_model->rowCount();
print_r($sql_model);

    if ($conta > 0) {

        $row = $sql_model->fetchALL(PDO::FETCH_ASSOC);

        $dados = array();


        $sql_acumulado = $conexao->query(
            "SELECT SUM(r.leitura_entrada) as Leitura_Acumulada
            FROM rmm r 
             INNER JOIN
cockpit c ON r.id_parametro = c.indicador_unico_regra
      
WHERE 
c.id_cockpit='$id_cockpit' 
 AND r.id_parametro = c.indicador_unico_regra
 AND 
  DATE_FORMAT(r.data_leitura, '%Y-%m-%d') >= '$Data_Intervalo_Periodo' 

ORDER BY r.data_leitura ASC"       
        );

        $conta_acumulado = $sql_acumulado->rowCount();
        $acumulado='';

        if($conta_acumulado>0){

            $r = $sql_acumulado->fetch(PDO::FETCH_ASSOC);

            $acumulado = $r['Leitura_Acumulada'];
        }

       // print_r($sql_acumulado);

        foreach ($row as $r) {

            //$theDate    = new DateTime($r['data_leitura']);
            //$data_leitura = $theDate->format('Y-m-d H:i:s');
            //$data_leitura =  strtotime($r['data_leitura']) * 1000;
            //$hora_min =  date('H:i', strtotime($value['data_leitura']));
            $data_leitura =  date('d/m H:i', strtotime($r['data_leitura']));
            //$data_leitura =  strtotime($r['data_leitura']) * 1000;
            $Origem_Leitura = $r['origem_leitura_parametro'];
            $parametro = $r['nome_parametro'];
            $nome_ponto = $r['nome_ponto'];

            $nome_unidade_medida =  $r['nome_unidade_medida'];

            $Leitura = $r['leitura_entrada'];

            $dados[] = array(

                'data' => $Leitura,
                'name' => $nome_ponto . ' <br> ' . $parametro . ' <b><br>(' . $nome_unidade_medida . ') </b>',
                'categories' => $data_leitura,
                'acumulado'=>$acumulado

            );
        }


        echo json_encode($dados,  JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        
    } else {


        $dados[] = array(

            'sem_dados' => 0,

        );

        echo json_encode($dados, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
    }
}

//=== [ finaliza modelo 4 ]====




//=====[ inicia modelo 5 ] ======  // consulta única a leitura mais recente
if ($id_cockpit != '' && $modelo_grafico == '5') {

    $sql_model = $conexao->query("SELECT 
     r.leitura_entrada,
     r.leitura_saida,
     r.data_leitura, 
     pr.origem_leitura_parametro,
      pr.controle_concentracao,
      pr.nome_parametro,
       u.nome_unidade_medida,
       pr.concen_min, 
       pr.concen_max,
       e.nome_estacao,
       o.nome_obra,
       p.nome_ponto

    FROM rmm r
    INNER JOIN
cockpit c ON r.id_parametro = c.indicador_unico_regra
INNER JOIN 
parametros_ponto pr ON pr.id_parametro = c.indicador_unico_regra
INNER JOIN
estacoes e ON e.id_estacao = c.estacao_selecionada_regra 
INNER JOIN
obras o ON o.id_obra = e.id_obra
INNER JOIN
pontos_estacao p ON p.id_ponto = pr.id_ponto
INNER JOIN
unidade_medida u ON u.id_unidade_medida = pr.unidade_medida

WHERE 
c.id_cockpit='$id_cockpit' 
 AND r.id_parametro = c.indicador_unico_regra

ORDER BY r.data_leitura DESC LIMIT 0,1 ");

    $conta = $sql_model->rowCount();

   // print_r($sql_model);

    if ($conta > 0) {

        $row = $sql_model->fetchALL(PDO::FETCH_ASSOC);

        $dados = array();


        $pega_leitura = array();

        $pega_data = array();

        $pega_nome_unidade_medida = array();

        // $data = array('data' => $Leitura);

        // laço para pegar o nome do indicador
        foreach ($row as $r) {



            //$data_leitura =  strtotime($r['data_leitura']);
            //$data_leitura =  strtotime($r['data_leitura']);
            //$hora_min =  date('H:i', strtotime($r['data_leitura']));
            $data_leitura =  date('d/m/Y H:i', strtotime($r['data_leitura']));

            $Origem_Leitura = $r['origem_leitura_parametro'];
            $parametro = $r['nome_parametro'];

            $nome_unidade_medida =  $r['nome_unidade_medida'];

            $nome_obra = $r['nome_obra'];
            $nome_ponto = $r['nome_ponto'];
            $nome_estacao = $r['nome_estacao'];

            $Leitura_porcentagem = round(($r['leitura_entrada']/$r['concen_max'])*100,2);

             $Leitura = $r['leitura_entrada'];


            $concen_min = trim(isset($r['concen_min'])) ? $r['concen_min'] : '0';
            $concen_max = trim(isset($r['concen_max'])) ? $r['concen_max'] : '100';


            $dados[] = array(

                'data_leitura' => $data_leitura,
                'Leitura' => $Leitura,
                 'Leitura_porcentagem' => $Leitura_porcentagem,
                'nome_Indicador' => $nome_ponto . ' -> ' . $parametro,
                'nome_unidade_medida' => $nome_unidade_medida,
                'concen_min' => $concen_min,
                'concen_max' => $concen_max,
                'nome_estacao' => $nome_estacao,
                'nome_obra' => $nome_obra
            );
        }


        echo json_encode($dados,  JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
    }
}

//=== [ finaliza modelo 5 ]====