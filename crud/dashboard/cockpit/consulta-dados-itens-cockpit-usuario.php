<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';
// Set the JSON header ==[ Cálculo de Indicadores ]=====
header('Content-Type: application/json; charset=utf-8');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');


// pega os dados do formuário da OBRA
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $id_cockpit = (isset($_GET['id_cockpit'])) ? trim($_GET['id_cockpit']) : '';


    $modelo_consulta = (isset($_GET['modelo_consulta'])) ? trim($_GET['modelo_consulta']) : '';


    $periodo_dados = (isset($_GET['periodo_dados'])) ? trim($_GET['periodo_dados']) : '';

    $Data_Atual_Periodo = date_create()->format('Y-m-d');

    switch ($periodo_dados) {
        case 0:
            $nome_periodo = 'Último Valor Informado';
            $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-1 days', strtotime($Data_Atual_Periodo)));
            break;

        case 7:
            $nome_periodo = 'Média dos Últimos 7 dias';
            $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-7 days', strtotime($Data_Atual_Periodo)));
            $calcula_media = "AVG(r.leitura_entrada) as media_leitura_periodo,";
            break;

        case 15:
            $nome_periodo = 'Média dos Últimos 15 dias';
            $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-15 days', strtotime($Data_Atual_Periodo)));
            $calcula_media = "AVG(r.leitura_entrada) as media_leitura_periodo,";
            break;

        case 30:
            $nome_periodo = 'Média dos Últimos 30 dias';
            $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-30 days', strtotime($Data_Atual_Periodo)));
            $calcula_media = "AVG(r.leitura_entrada) as media_leitura_periodo,";
            break;

        default:
            $nome_periodo = "Padrão - Últimos 30 Dias";
            $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-30 days', strtotime($Data_Atual_Periodo)));
            break;
    }






    //=====[ inicia modelo 1 ] ======
    if ($id_cockpit != '' && $modelo_consulta != 4 &&   $modelo_consulta != 5) { //(com dia e mês e média de leitura por dia)

$sql_model = $conexao->query("SELECT     
r.id_rmm,
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

FROM  rmm r

INNER JOIN 
cockpit_lista_indicadores cl ON r.id_parametro = cl.id_indicador

INNER JOIN 
parametros_ponto pr ON pr.id_parametro = cl.id_indicador
INNER JOIN
unidade_medida u ON u.id_unidade_medida = pr.unidade_medida
INNER JOIN
pontos_estacao p ON p.id_ponto = pr.id_ponto
INNER JOIN
estacoes e ON e.id_estacao = p.id_estacao 
INNER JOIN
obras o ON o.id_obra = e.id_obra


WHERE 
DATE_FORMAT(r.data_leitura, '%Y-%m-%d') >= '$Data_Intervalo_Periodo' 
AND cl.id_cockpit = '$id_cockpit' 
 
ORDER BY r.data_leitura ASC ;");

        $conta = $sql_model->rowCount();


        if ($conta > 0) {

            $row = $sql_model->fetchALL(PDO::FETCH_ASSOC);

            // Após o fetchALL:
            //print_r($row);
            //echo '<hr>';

            //print_r($sql_model);

            //exit;


            // Inicialize um array para armazenar os dados agrupados
            $groupedData = [];

            foreach ($row as $r) {

                $indicatorName = $r['nome_parametro'];

                $nome_ponto = $r['nome_ponto'];

                $data_leitura =  strtotime($r['data_leitura']) * 1000;

                $Leitura = floatval($r['leitura_entrada']);

                //$Leitura_porcentagem = floatval(round(($r['leitura_entrada'] / $r['concen_max']) * 100, 2));

                $concen_min = trim(isset($r['concen_min'])) ? $r['concen_min'] : '0';
                $concen_max = trim(isset($r['concen_max'])) ? $r['concen_max'] : '100';

                $nome_unidade_medida =  $r['nome_unidade_medida'];

                $nome_obra = $r['nome_obra'];
                $nome_ponto = $r['nome_ponto'];
                $nome_estacao = $r['nome_estacao'];


                if (!isset($groupedData[$indicatorName])) {
                    $groupedData[$indicatorName] = [
                        'name' => $indicatorName,
                        'data' => [],

                    ];
                }

                $groupedData[$indicatorName]['data'][] = [$data_leitura, $Leitura];
                $groupedData[$indicatorName]['nome_unidade_medida'] = $nome_unidade_medida;
                $groupedData[$indicatorName]['concen_min'] = $concen_min;
                $groupedData[$indicatorName]['concen_max'] = $concen_max;
                $groupedData[$indicatorName]['nome_ponto'] = $nome_ponto;
            }

            // Agora $groupedData contém os dados agrupados por indicador
            $jsonData = array_values($groupedData);
            echo json_encode($jsonData, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        } else {

            $dados[] = array(

                'sem_dados' => 0,

            );

            echo json_encode($dados,  JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        }

        $conexao = null;
        //=====[ finaliza modelo 1 a 3 com multiplos indicadores ] ======
    } else   if ($id_cockpit != '' && $modelo_consulta == 4 ||   $modelo_consulta == 5) {




        $sql_model = $conexao->query("SELECT     
        r.id_rmm,
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
        
        FROM  rmm r
        
        INNER JOIN 
        cockpit c ON r.id_parametro = c.indicador_unico_regra 
        
        INNER JOIN 
        parametros_ponto pr ON pr.id_parametro = r.id_parametro
        INNER JOIN
        unidade_medida u ON u.id_unidade_medida = pr.unidade_medida
        INNER JOIN
        pontos_estacao p ON p.id_ponto = r.id_ponto
        INNER JOIN
        estacoes e ON e.id_estacao = p.id_estacao 
        INNER JOIN
        obras o ON o.id_obra = e.id_obra
        
        
        WHERE 
        DATE_FORMAT(r.data_leitura, '%Y-%m-%d') >= '$Data_Intervalo_Periodo' 
        AND c.id_cockpit = '$id_cockpit' 
         
        ORDER BY r.data_leitura ASC ;");
        
                $conta = $sql_model->rowCount();
        
        
                if ($conta > 0) {
        
                    $row = $sql_model->fetchALL(PDO::FETCH_ASSOC);
        
                    // Após o fetchALL:
                    //print_r($row);
                    //echo '<hr>';
        
                    //print_r($sql_model);
        
                    //exit;
        
        
                    // Inicialize um array para armazenar os dados agrupados
                    $groupedData = [];
        
                    foreach ($row as $r) {
        
                        $indicatorName = $r['nome_parametro'];
        
                        $nome_ponto = $r['nome_ponto'];
        
                        $data_leitura =  strtotime($r['data_leitura']) * 1000;
        
                        $Leitura = floatval($r['leitura_entrada']);
        
                        //$Leitura_porcentagem = floatval(round(($r['leitura_entrada'] / $r['concen_max']) * 100, 2));
        
                        $concen_min = trim(isset($r['concen_min'])) ? $r['concen_min'] : '0';
                        $concen_max = trim(isset($r['concen_max'])) ? $r['concen_max'] : '100';
        
                        $nome_unidade_medida =  $r['nome_unidade_medida'];
        
                        $nome_obra = $r['nome_obra'];
                        $nome_ponto = $r['nome_ponto'];
                        $nome_estacao = $r['nome_estacao'];
        
        
                        if (!isset($groupedData[$indicatorName])) {
                            $groupedData[$indicatorName] = [
                                'name' => $indicatorName,
                                'data' => [],
        
                            ];
                        }
        
                        $groupedData[$indicatorName]['data'][] = [$data_leitura, $Leitura];
                        $groupedData[$indicatorName]['nome_unidade_medida'] = $nome_unidade_medida;
                        $groupedData[$indicatorName]['concen_min'] = $concen_min;
                        $groupedData[$indicatorName]['concen_max'] = $concen_max;
                        $groupedData[$indicatorName]['nome_ponto'] = $nome_ponto;
                    }
        
                    // Agora $groupedData contém os dados agrupados por indicador
                    $jsonData = array_values($groupedData);
                    echo json_encode($jsonData, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);

                } else {
        
                    $dados[] = array(
        
                        'sem_dados' => 0,
        
                    );
        
                    echo json_encode($dados,  JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
                }
        
                $conexao = null;



       
    }
     //=====[ finaliza modelo 4 e 5 com indicadores unicos ] ====== 





   
} else {

    $dados[] = array(

        'sem_dados' => 'Variáveis não encontradas!',

    );

    echo json_encode($dados,  JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
}
//=== [ finaliza modelo 5 ]====