<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
require 'vendor/autoload.php'; // Para carregar a biblioteca TCPDF
use TCPDF;

// Coletar valores do formulário
$valor1 = $_POST['valor1'];
$valor2 = $_POST['valor2'];
$operacao = $_POST['operacao'];



// Fazer uma consulta SQL como exemplo
$stmt = $conn->prepare("SELECT * FROM tabela WHERE condicao = :condicao");
$stmt->execute(['condicao' => $valor1]); // Exemplo
$resultado_sql = $stmt->fetchAll();



// Realizar cálculos
$resultado_calculo = ($operacao === 'soma') ? $valor1 + $valor2 : $valor1 - $valor2;




// Gerar PDF com TCPDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('Helvetica', '', 16);
$pdf->Write(40, "Resultado do Cálculo: $resultado_calculo");
$pdf->Output('relatorio.pdf', 'I');
?>
