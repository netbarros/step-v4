<?php
 // buffer de saída de dados do php]
if (!isset($_SESSION)) session_start();	
// Instancia Conexão PDO
require_once "../../conexao.php";
$conn = Conexao::getInstance();
require_once "../../crud/login/verifica_sessao.php";
// Consulta a tabela qrcode
$stmt = $conn->prepare("SELECT id_ponto FROM pontos_estacao");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Início da lista de QR Codes com moldura usando Bootstrap
echo '<div class="container">';
echo '<h2 class="text-center mb-4">Lista de QR Codes</h2>';
echo '<div class="row">';

// Loop através dos resultados da consulta
foreach ($result as $row) {
  // URL do QR Code dinâmico
  $qr_code_url = 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=' . $row['id_ponto'];

  // Exibição do QR Code com moldura usando Bootstrap
  echo '<div class="col-md-3">';
  echo '<div class="card border-0 text-center">';
  echo '<div class="card-body">';
  echo '<img src="' . $qr_code_url . '" />';
  echo '<h5 class="card-title mt-3">ID: ' . $row['id_ponto'] . '</h5>';
  echo '</div>';
  echo '</div>';
  echo '</div>';
}

// Fim da lista de QR Codes com moldura usando Bootstrap
echo '</div>';
echo '</div>';

// Encerramento da conexão com o banco de dados
$conn = null;
?>