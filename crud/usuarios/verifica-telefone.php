
<?php

// Conexão com o banco de dados
require_once $_SERVER['DOCUMENT_ROOT'].'/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/total-voice/autoload.php';

use TotalVoice\Client as TotalVoiceClient;

$phoneNumber = $_POST['phoneNumber'];
$verificationCode = $_POST['verificationCode'];
$acao = $_POST['acao'];



if($acao=='verifica-telefone'){
        // Verificação se o telefone já foi verificado
        $stmt = $conexao->prepare("SELECT * FROM usuarios WHERE telefone = ? AND telefone_verificado = 1");
        $stmt->execute([$phoneNumber]);
        $user = $stmt->fetch();

        if ($user) {
          
          echo 'already-verified';
          exit;

        } else {
      // Geração da chave aleatória
      $randomKey = rand(100000, 999999);

      // Gravação da chave na tabela de usuários
      $stmt = $conexao->prepare("UPDATE usuarios SET chave_sms = ? WHERE telefone = ?");
      $stmt->execute([$randomKey, $phoneNumber]);

      // Envio do SMS
      $client = new TotalVoiceClient('your_access_token');
      $message = "Seu código de verificação é: $randomKey";
      $client->sms->enviar($phoneNumber, $message);

      }
}

if($acao=='valida-sms'){
      // Verificação do código
      $stmt = $conexao->prepare("SELECT * FROM usuarios WHERE telefone = ? AND chave_sms = ?");
      $stmt->execute([$phoneNumber, $verificationCode]);
      $user = $stmt->fetch();

      if ($user) {
        // Atualização do status de verificação do telefone
        $stmt = $conexao->prepare("UPDATE usuarios SET telefone_verificado = 1 WHERE telefone = ?");
        $stmt->execute([$phoneNumber]);
        echo 'success';
      } else {
        echo 'failure';
      }
}
?>