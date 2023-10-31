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

// Definir o número de dias para a consulta (no seu caso, 7)
// Define o Período da Busca dos Dados
$Data_Atual_Periodo = date_create()->format('Y-m-d');
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-7 days', strtotime($Data_Atual_Periodo)));
$dias='7';
// Preparar a consulta SQL
//$sql = "SELECT COUNT(id_suporte) AS total FROM suporte WHERE status_suporte = 1 AND data_open >= :data_inicio";

//$sql = "SELECT status_suporte, data_open FROM suporte WHERE data_open >= :data_inicio AND status_suporte = 1";

$sql = "SELECT COUNT(DISTINCT r.id_rmm) as total_leitura FROM rmm r WHERE r.data_leitura >= :data_inicio ";
$stmt = $conexao->prepare($sql);

// Vincular o valor da data de início à consulta
$stmt->bindValue(':data_inicio', $Data_Intervalo_Periodo);


// Executar a consulta  
$stmt->execute();

// Obter o resultado da consulta
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

$count = $stmt->rowCount();
$total_leitura = $resultado['total_leitura'];
// Imprimir o resultado
echo "O total de leituras, nos últimos $dias dias iniciando na data: ($Data_Intervalo_Periodo) até hoje ($Data_Atual_Periodo) e o Total é: " . $total_leitura;
?>
