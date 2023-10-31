<?php
header("content-type: application/json");
//require_once '../../../conexao.php';
// Atribui uma conexão PDO

include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$projeto_atual = (isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';
$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';


$modelo_grafico = (isset($_GET['modelo_grafico'])) ? $_GET['modelo_grafico'] : '';


$Data_Atual_Periodo = date_create()->format('Y-m-d');
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-7 days', strtotime($Data_Atual_Periodo)));

$tipo_user_sessao = 'admin'; //trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
//$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
//$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';
/* 
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
} */




if($projeto_atual!='' && $projeto_atual!='0' || $projeto_atual!='undefined' || $projeto_atual!='null'){


    $filtro = "AND o.id_obra = ' $projeto_atual' ";

} else{

    $filtro = "";
}








//=====[ inicia modelo 1 ] ======
if ($modelo_grafico == '1') { //(grafico crescimento organico das leituras por obras no período)

    $sql_model = $conexao->prepare("SELECT COUNT(DISTINCT r.id_rmm) as total_leitura, r.data_leitura,
                                    COUNT(DISTINCT s.id_suporte) as total_chamados_suporte,
                                    COUNT(DISTINCT s.status_suporte = '1'  ) as chamados_suporte_aberto,
                                    COUNT(DISTINCT s.status_suporte = '4' ) as chamados_suporte_fechado
                                    FROM suporte s
                                    INNER JOIN rmm r ON r.id_parametro = s.parametro
                                    INNER JOIN parametros_ponto pr ON pr.id_parametro = s.parametro
                                    INNER JOIN pontos_estacao p ON p.id_ponto = s.plcode
                                    INNER JOIN obras o ON o.id_obra = p.id_obra
                                    INNER JOIN estacoes e ON e.id_estacao = s.estacao 
                                    
                                    WHERE s.data_open >= '2023-07-11'
                                    AND  r.id_parametro = s.parametro 
                                                                            
                                    ORDER BY s.data_open ASC");

    $sql_model->bindParam(':data_inicio', $Data_Intervalo_Periodo);
        $sql_model->execute();

    $conta = $sql_model->rowCount();

    if ($conta > 0) {

        $row = $sql_model->fetchAll(PDO::FETCH_ASSOC);

        $dados = array();

        foreach ($row as $r) {
            $data_referencia = $r['data_leitura'];

            $date = DateTime::createFromFormat('Y-m-d H:i:s', $data_referencia);

            if ($date === false) {
                // O objeto DateTime não foi criado com sucesso.
                // Você pode adicionar código aqui para lidar com o erro, como
                // lançar uma exceção ou registrar o erro em um arquivo de log.
                throw new Exception('A data fornecida não é válida.');
            } else {
                $unixTimestamp = $date->getTimestamp();
            }

            if (isset($r['data_leitura'])) {
                $data_referencia = $r['data_leitura'];
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $data_referencia);
            
                if ($date === false) {
                    throw new Exception('A data fornecida não é válida.');
                } else {
                    $unixTimestamp = $date->getTimestamp();
                }
            } else {
                throw new Exception('A data de leitura não está definida.');
            }
            

            // Converte para um timestamp de JavaScript (milissegundos desde 1º de janeiro de 1970)
            $data_leitura = $unixTimestamp * 1000;

            $dados[] = array(
                'data_leitura' => $data_leitura,
                'total_leituras_registradas' => $r['total_leitura'],
                'total_chamados_suporte' => $r['total_chamados_suporte'],
                'chamados_suporte_fechado' => $r['chamados_suporte_fechado'],
                'chamados_suporte_aberto' => $r['chamados_suporte_aberto']
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
//=====[ finaliza modelo 1 ]====


//=== [ finaliza modelo 1 ]====


