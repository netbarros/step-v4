<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';
header("content-type: application/json");

$projeto_atual = (isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';

$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';

$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';

$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';

$modelo_grafico = (isset($_GET['modelo_grafico'])) ? $_GET['modelo_grafico'] : '';


$periodo = trim($_GET['periodo']) ? $_GET['periodo'] : '';

$Data_Atual_Periodo = date_create()->format('Y-m-d');
$Periodo_consulta = date('Y-m-d', strtotime('-7 days', strtotime($Data_Atual_Periodo)));


$filtro = '';
if ($projeto_atual != '') {


    $filtro = "AND e.id_obra = '$projeto_atual' GROUP BY ts.nome_suporte;";
} else {

    $filtro = ' GROUP BY ts.nome_suporte;';
}

$sql_personalizado = '';

$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';

if ($nivel_acesso_user_sessao == 'supervisor') {

    $sql_personalizado = "AND (e.supervisor = '$id_BD_Colaborador'  OR up.id_usuario  = '$id_usuario_sessao')";
}

if ($nivel_acesso_user_sessao == 'ro') {

    $sql_personalizado = "AND (e.ro = '$id_BD_Colaborador'  OR up.id_usuario  = '$id_usuario_sessao')";
}

if ($nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado = "AND ( up.id_usuario  = '$id_usuario_sessao')";
}


//=====[ inicia modelo 1 ] ======
if ($modelo_grafico == '1') { //(grafico crescimento organico das leituras por obras no período)

    $sql_model = $conexao->query("SELECT COUNT(s.id_suporte) as total_suporte_pendente,
    ts.nome_suporte
FROM suporte s
INNER JOIN estacoes e ON e.id_estacao = s.estacao
INNER JOIN tipo_suporte ts On ts.id_tipo_suporte = s.tipo_suporte
LEFT JOIN usuarios_projeto up ON up.id_obra = e.id_obra
WHERE s.data_open >= '$Periodo_consulta' AND s.status_suporte != '4' 

$sql_personalizado

$filtro
 
  ");

  

    $conta = $sql_model->rowCount();


    if ($conta > 0) {

    
        // $data = array('data' => $Leitura);

        
//print_r($sql_model);

        $lista = array();

        $row = $sql_model->fetchALL(PDO::FETCH_ASSOC);

        foreach ($row as $r) {



            $lista[] = array(

            'qtdade_suporte' => ($r['total_suporte_pendente']),
                'categoria_suporte' => ($r['nome_suporte'])
              
            // $data_open = strtotime($r['data_open']);
            //$status_suporte = $r['status_suporte'];
            );
        }


        echo json_encode($lista, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
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
