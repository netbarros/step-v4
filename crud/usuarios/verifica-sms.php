<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/conexao.php';

// Atribui uma conexão PDOcolab
$conexao = Conexao::getInstance();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/total-voice/autoload.php';
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

use TotalVoice\Client as TotalVoiceClient;


$acao = $_POST['acao'] ?? '';
$mobile = $_POST['mobile'] ?? '';
$mobile_normalized = preg_replace("/[^0-9]/", "", $mobile);

$codigo = $_POST['codigo'] ?? ''; // Adicionei esta linha para pegar o código
$totalVoiceToken = "9cdba05b550e2727ca1b4245c2004942"; // Token TotalVoice

if ($acao == 'verifica-sms' && !empty($mobile_normalized)) {
    // Normalizar o número de telefone fornecido
   

    $stmt = $conexao->prepare("SELECT telefone,email FROM usuarios WHERE telefone = :mobile");
    $stmt->bindParam(':mobile', $mobile_normalized, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $telefone_verificado = $row['telefone'];
    $email = trim($row['email']);
    // Normalizar o número de telefone verificado
    $telefone_verificado_normalized = preg_replace("/[^0-9]/", "", $telefone_verificado);

    if ($telefone_verificado_normalized != $mobile_normalized) {
        $retorno = array('codigo' => 0, 'mensagem' => "O telefone informado {$mobile_normalized}, não é o mesmo cadastrado no Sistema. {$telefone_verificado_normalized}");
        echo json_encode($retorno);
        exit;
    }  else {

   
            
        $chave_unica = rand(100000, 999999);
 
            $mobile_normalized = preg_replace("/[^0-9]/", "", $mobile);


            $stmt = $conexao->prepare("UPDATE usuarios SET chave = :chave, telefone = :mobile WHERE email = :email");
            $stmt->bindParam(':chave', $chave_unica, PDO::PARAM_INT);
            $stmt->bindParam(':mobile', $mobile_normalized, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
                       
           

                //pega todos os dados do usuario

                $sql = "SELECT * FROM usuarios WHERE telefone = :mobile";
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(':mobile', $mobile_normalized, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $nome_usuario = $row['nome'];
                $email_usuario = $row['email'];
                $telefone_usuario = $row['telefone'];


                // envia sms
                $mensagem_inicio = "Validação do STEP:  Olá {$nome_usuario}, este é seu código SMS: {$chave_unica}, digite este código no campo de validação do STEP.";
                $mensagem_alerta_sem_tags = strip_tags($mensagem_inicio); // remove tags html
                $client = new TotalVoiceClient($totalVoiceToken);

                $telefone_usuario_alerta = "+55" . preg_replace('/[^0-9]/', '', trim($mobile));
                
                $mensagem_alerta_resumida = mb_substr($mensagem_alerta_sem_tags, 0, 160, 'UTF-8');
        
                $response = $client->sms->enviar($telefone_usuario_alerta, $mensagem_alerta_resumida);
        
                if ($response->getStatusCode() == 200) {

                    $retorno = array('codigo' => 1, 'mensagem' => 'SMS Enviado com sucesso, aguarde alguns instantes e verifique seu celular e valide o SMS');

                    echo json_encode($retorno, true);
                    $responseMessage = 'SMS Enviado com sucesso: '.$nome_usuario.' -- ' . $telefone_usuario_alerta . ' - ' . $response->getContent();
                    error_log($responseMessage);
                    exit;

                } else {

                    $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao Enviar SMS, tente novamente mais tarde');

                    echo json_encode($retorno, true);
                    $responseMessage = 'Erro ao Enviar SMS: ' . $telefone_usuario_alerta . ' - ' . $response->getContent();
                    error_log($responseMessage);

                    exit;
                }
               
                
           
            
            }
        }
  


   




if($acao=='valida-sms'){
    $mobile = $_POST['mobile'] ?? '';
    $mobile_normalized = preg_replace("/[^0-9]/", "", $mobile);


    $sms = trim($_POST['code']) ?? '';

    $sql = "SELECT * FROM usuarios WHERE telefone=:telefone ";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':telefone', $mobile_normalized, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$row){

    $retorno = array('codigo' => 0, 'mensagem' => 'Não localizamos seu usuário ou o código SMS informado está incorreto, tente novamente');

              echo json_encode($retorno, true);
              exit;

}

    $nome_usuario = $row['nome'];
    $email_usuario = $row['email'];
    $telefone_usuario = $row['telefone'];
    $chave_usuario = trim($row['chave']);


    if ($chave_usuario == $sms) {

        $mobile_normalized = preg_replace("/[^0-9]/", "", $mobile);

        $stmt = $conexao->prepare("UPDATE usuarios SET telefone_verificado = '1'  WHERE telefone = :mobile");
        $stmt->bindParam(':mobile', $mobile_normalized, PDO::PARAM_STR);
          $stmt->execute();


          // envia sms
          $mensagem_inicio = "Validação do STEP:  Olá {$nome_usuario}, Seu número de telefone foi Validado com Sucesso!, agora  via STEP, poderá receber notificações via SMS.";
          $mensagem_alerta_sem_tags = strip_tags($mensagem_inicio); // remove tags html
          $client = new TotalVoiceClient($totalVoiceToken);

          $telefone_usuario_alerta = "+55" . preg_replace('/[^0-9]/', '', trim($telefone_usuario));
          
          $mensagem_alerta_resumida = mb_substr($mensagem_alerta_sem_tags, 0, 160, 'UTF-8');
  
          $response = $client->sms->enviar($telefone_usuario_alerta, $mensagem_alerta_resumida);
  
          if ($response->getStatusCode() == 200) {

              $retorno = array('codigo' => 1, 'mensagem' => 'SMS Validado com Sucesso');

              echo json_encode($retorno, true);
              $responseMessage = 'SMS Enviado com sucesso: '.$nome_usuario.' -- ' . $telefone_usuario . ' - ' . $response->getContent();
              error_log($responseMessage);
              exit;

          } else {

              $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao Validar SMS, tente novamente mais tarde');

              $responseMessage = 'Erro ao Enviar SMS: ' . $telefone_usuario . ' - ' . $response->getContent();
              error_log($responseMessage);

              $retorno = array('codigo' => 1, 'mensagem' => 'SMS Validado com sucesso, agora poderá receber notificações via SMS!');
                echo json_encode($retorno, true);
                exit;
          }

       
    } else {
        $retorno = array('codigo' => 0, 'mensagem' => 'o Código SMS informado, não é o mesmo enviado para o seu celular, tente novamente.');
        echo json_encode($retorno);
    }




}

?>