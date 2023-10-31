<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
//ini_set("session.cookie_secure", 1);
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
date_default_timezone_set('America/Sao_Paulo');

     
$id_plcode_atual = trim(isset($_COOKIE['plcode_lido']) ? $_COOKIE['plcode_lido'] : ($_POST['id_plcode'] ?? ''));
$id_indicador_atual = trim(isset($_COOKIE['indicador_evento']) ? $_COOKIE['indicador_evento'] : ($_POST['id_indicador'] ?? ''));
$estacao_atual = trim(isset($_COOKIE['estacao_atual']) ? $_COOKIE['estacao_atual'] : ($_POST['id_estacao'] ?? ''));
$chave_unica_sessao = trim(isset($_COOKIE['CHAVE_UNICA_SESSAO_ATUAL']) ? $_COOKIE['CHAVE_UNICA_SESSAO_ATUAL'] : ($_POST['chave_unica'] ?? ''));
$id_usuario = trim(isset($_COOKIE['id_usuario_logado']) ? $_COOKIE['id_usuario_logado'] : ($_POST['id_usuario'] ?? ''));

$nome_midia_app = trim(isset($_POST['nome_midia'])) ? $_POST['nome_midia'] : '';


// Verifica se a variável está vazia chave unica sessao
if($chave_unica_sessao==''){

  $retorno = array('codigo' => 0, 'retorno' => "Sua chave de acesso única Expirou, por favor, leia um PLCode da sua Estação ou Refaça seu login.");

  echo json_encode($retorno);

   exit;
}



if ($id_usuario == "" || $id_usuario == "undefined" || $id_usuario == "0" || $id_usuario ==  NULL) { 

  $retorno = array('codigo' => 0, 'retorno' => 'Usuário <strong>Ausente!</strong>, Por favor, atualize Aplicativo, Dê as Permissões Solicitadas no acesso e tente novamente.');

  echo json_encode($retorno);

  exit;
} 



// Verifica se a variável está vazia midia vinda pelo apk
if($nome_midia_app!=''){

  $nome_midia = $nome_midia_app;


        // Ação a ser executada quando o arquivo for movido com sucesso
    
        $stmt = $conexao->prepare('INSERT INTO  midia_leitura (nome_midia, chave_unica, id_usuario, id_parametro, id_plcode, id_estacao ) VALUES(:nome_midia, :chave_unica, :id_usuario, :id_parametro, :id_plcode, :id_estacao)');
        $stmt->execute(array(
          ':nome_midia' => $nome_midia,
          ':chave_unica' => $chave_unica_sessao,
          ':id_usuario' => $id_usuario,
          ':id_parametro' => $id_indicador_atual,
          ':id_plcode' => $id_plcode_atual,
          ':id_estacao' => $estacao_atual
        ));
         
       // $stmt->rowCount();
      
      
      
        if ($stmt->rowCount() > 0) {
      
      
      
      
          $retorno = array('codigo' => 1, 'retorno' => "Informações da Mídia do Indicador ID: ".$id_indicador_atual." - Salvas com Sucesso! ");
                  
          echo json_encode($retorno);
      
         
      
          
           //  faço a gravação do log
        $acao_log = "Mídia";
      $tipo_log = '38'; // Mídia Enviada
      
      $sql_log = "INSERT INTO log_leitura (
        chave_unica,
         id_usuario, 
         acao_log,
         estacao_logada,
         tipo_log) 
      VALUES (
            ?,
            ?,
            ?,
            ?,
            ?
            )";
      $conexao->prepare($sql_log)->execute([
          $chave_unica_sessao,
          $id_usuario,
          $acao_log,
          $estacao_atual,
          $tipo_log ]);
       //  faço a gravação do log
      
          exit;
      
      
        
      
      
      
        }else {
      
          $retorno = array('codigo' => 0, 'retorno' => "Não foi possível realizar a gravação da Mídia do Indicador ID: ".$id_indicador_atual." - Tente Novamente! ");
                    
            echo json_encode($retorno);
        
            exit;
      
      } // fim do else de verificação de sucesso de gravação da mídia


}else{

  $nome_midia = '';


  
//======>>>>
// Verifique se as variáveis estão vazias
//-- Tratamento Mídia e Salvamento da mesma -->
$date = new DateTime();
$time_unico_ref = $date->getTimestamp();

if (isset($_FILES['midia_leitura']) && !empty($_FILES['midia_leitura']['name'])) {
    $ext = explode('.', $_FILES['midia_leitura']['name']);
    $extension = $ext[1];
    $nome_midia = 'Midia_Leitura_TimeStamp_' . $time_unico_ref . '_ParametroID_' . $id_indicador_atual . '.' . $extension;

    $full_local_path = $_SERVER['DOCUMENT_ROOT'] . '/app/midias_leitura/' . $nome_midia;

    //-- Tratamento Mídia e Salvamento da mesma <--

    if (move_uploaded_file($_FILES['midia_leitura']['tmp_name'], $full_local_path)) {
        // Ação a ser executada quando o arquivo for movido com sucesso
    
  $stmt = $conexao->prepare('INSERT INTO  midia_leitura (nome_midia, chave_unica, id_usuario, id_parametro, id_plcode, id_estacao ) VALUES(:nome_midia, :chave_unica, :id_usuario, :id_parametro, :id_plcode, :id_estacao)');
  $stmt->execute(array(
    ':nome_midia' => $nome_midia,
    ':chave_unica' => $chave_unica_sessao,
    ':id_usuario' => $id_usuario,
    ':id_parametro' => $id_indicador_atual,
    ':id_plcode' => $id_plcode_atual,
    ':id_estacao' => $estacao_atual
  ));
   
 // $stmt->rowCount();



  if ($stmt->rowCount() > 0) {




    $retorno = array('codigo' => 1, 'retorno' => "Mídia do Parâmetro - Armazenado com Sucesso! ");
            
    echo json_encode($retorno);

   

    
     //  faço a gravação do log
  $acao_log = "Mídia";
$tipo_log = '38'; // Mídia Enviada

$sql_log = "INSERT INTO log_leitura (
  chave_unica,
   id_usuario, 
   acao_log,
   estacao_logada,
   tipo_log) 
VALUES (
      ?,
      ?,
      ?,
      ?,
      ?
      )";
$conexao->prepare($sql_log)->execute([
    $chave_unica_sessao,
    $id_usuario,
    $acao_log,
    $estacao_atual,
    $tipo_log ]);
 //  faço a gravação do log

    exit;


  



  }else {

    $retorno = array('codigo' => 0, 'retorno' => "Falha de Arquivo ao Salvar a Mídia Enviada, favor entrar em contato com o Suporte.");
              
      echo json_encode($retorno);
  
      exit;

} 

    } else {
        // Ação a ser executada quando o arquivo não for movido com sucesso
        $retorno = array('codigo' => 0, 'retorno' => "Falha de Arquivo ao Mover a Mídia Enviada, favor entrar em contato com o Suporte.");
              
        echo json_encode($retorno);
  
        exit;
    }
} else {
    $retorno = array('codigo' => 0, 'retorno' => "Falha, não foi detectado a Mídia Enviada, favor entrar em contato com o Suporte.");
              
    echo json_encode($retorno);
  
    exit;
}

}

  


 