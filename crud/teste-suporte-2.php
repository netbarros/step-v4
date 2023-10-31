<?php
//header("content-type: application/json");
//require_once '../../../conexao.php';
// Atribui uma conexão PDO

include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Consulta SQL
$sql = "SELECT COUNT(DISTINCT r.id_rmm) as total_leitura, r.data_leitura,
        COUNT(DISTINCT s.id_suporte) as total_chamados_suporte,
        COUNT(DISTINCT s.status_suporte = '1') as chamados_suporte_aberto,
        COUNT(DISTINCT s.status_suporte = '4') as chamados_suporte_fechado
        FROM suporte s
        INNER JOIN rmm r ON r.id_parametro = s.parametro
        INNER JOIN parametros_ponto pr ON pr.id_parametro = s.parametro
        INNER JOIN pontos_estacao p ON p.id_ponto = s.plcode
        INNER JOIN obras o ON o.id_obra = p.id_obra
        INNER JOIN estacoes e ON e.id_estacao = s.estacao 
        WHERE s.data_open >= '2023-07-11'
        AND  r.id_parametro = s.parametro 
        ORDER BY s.data_open ASC";

// Executar a consulta
$stmt = $pdo->query($sql);

// Fetch the results into an array
$result = $stmt->fetch();

// Imprimir os resultados
echo "Total de Leituras: " . $result['total_leitura'] . "\n";
echo "Total de Chamados de Suporte: " . $result['total_chamados_suporte'] . "\n";
echo "Chamados de Suporte Abertos: " . $result['chamados_suporte_aberto'] . "\n";
echo "Chamados de Suporte Fechados: " . $result['chamados_suporte_fechado'] . "\n";
?>
