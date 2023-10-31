<?php
 // buffer de saída de dados do php]
if (!isset($_SESSION)) session_start();	
// Instancia Conexão PDO
require_once "../../../conexao.php";
$conn = Conexao::getInstance();
require_once "../../../crud/login/verifica_sessao.php";
// Consulta a tabela qrcode
$stmt = $conn->prepare("SELECT id_ponto FROM pontos_estacao");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Início da lista de QR Codes
echo '<div style="display:flex;flex-wrap:wrap;">';

// Loop através dos resultados da consulta
foreach ($result as $row) {
  // URL do QR Code dinâmico
  $qr_code_url = 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=' . $row['id_ponto'];

  // Exibição do QR Code
  echo '<div style="margin:10px;">';
  echo '<img src="' . $qr_code_url . '" />';
  echo '<br />';
  echo 'ID: ' . $row['id'];
  echo '</div>';
}

// Fim da lista de QR Codes
echo '</div>';

// Encerramento da conexão com o banco de dados
$conn = null;
?>