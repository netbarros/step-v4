<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';
header("content-type: application/json");

$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';

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
}

$Data_Periodo_Inicio = date('Y-m-d ', strtotime('-7 days', strtotime($Data_Atual_Periodo)));
$Data_Periodo_Fim = date('Y-m-d', strtotime('-14 days', strtotime($Data_Atual_Periodo)));




$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';


if ($projeto_atual != '') {


    $filtro = "AND o.id_obra = ' $projeto_atual' GROUP BY p.id_ponto";
} else {

    $filtro = 'GROUP BY o.id_obra';
}

$sql_personalizado = '';

$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';
if ($nivel_acesso_user_sessao == 'supervisor') {

    $sql_personalizado = "AND (e.supervisor = '$id_BD_Colaborador' OR up.id_usuario ='$id_usuario_sessao')";
}

if ($nivel_acesso_user_sessao == 'ro') {

    $sql_personalizado = "AND (e.ro = '$id_BD_Colaborador' OR up.id_usuario ='$id_usuario_sessao')";
}

if ($nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado = "AND ( up.id_usuario ='$id_usuario_sessao')";
}

//=====[ inicia modelo 1 ] ======
if ($modelo_grafico == '1') { //(grafico crescimento organico das leituras por obras no período)

    $sql_model = $conexao->query("SELECT  
    pr.nome_parametro,
    p.nome_ponto,
    pr.id_parametro,
    o.id_obra,
    o.nome_obra,
    e.nome_estacao,
    (
        SELECT MAX(rmm.data_leitura) 
        FROM rmm 
        WHERE rmm.id_parametro = pr.id_parametro
    ) AS ultima_data_leitura
FROM 
    obras o 
INNER JOIN 
    estacoes e ON e.id_obra = o.id_obra
INNER JOIN 
    pontos_estacao p ON p.id_obra = o.id_obra
INNER JOIN 
    parametros_ponto pr ON pr.id_ponto = p.id_ponto
LEFT JOIN 
    usuarios_projeto up ON up.id_obra = o.id_obra
WHERE 
    pr.id_parametro NOT IN (
        SELECT 
            rmm.id_parametro 
        FROM 
            rmm
        WHERE 
            rmm.data_leitura BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND CURDATE()
    ) 
    AND 
    o.status_cadastro='1' AND pr.status_parametro!='3' AND p.status_ponto!='3'
    $sql_personalizado
    $filtro
ORDER BY 
    o.nome_obra ASC


");

    $conta = $sql_model->rowCount();


    if ($conta > 0) {

        $row = $sql_model->fetchALL(PDO::FETCH_ASSOC);

        $dados = array();

        $leitura = array();



        // $data = array('data' => $Leitura);


        foreach ($row as $r) {


            $data_leitura = $r['ultima_data_leitura'] ? date('d/m/Y H:i', strtotime($r['ultima_data_leitura'])) : 'N/A';


            $id_obra = $r['id_obra'];
            $nome_obra = $r['nome_obra'];
            $nome_estacao = $r['nome_estacao'];
            $nome_ponto = $r['nome_ponto'];
            $nome_parametro = $r['nome_parametro'];
           

            $dados[] = array(

                'id_obra' =>$id_obra,
                'nome_obra' => $nome_obra,
                'nome_estacao' => $nome_estacao,
                'nome_ponto' => $nome_ponto,
                'nome_parametro' => $nome_parametro,
                'data_leitura' => $data_leitura

            );
        }


        echo json_encode($dados, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $conexao=null;
    } else {


        $dados[] = array(

            'sem_dados' => '0',

        );
        echo json_encode($dados, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        $conexao=null;
    }
}

//=== [ finaliza modelo 1 ]====


