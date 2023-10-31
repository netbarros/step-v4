<?php
header('Content-Type: application/json');
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
// define o cabeçalho da resposta como JSON

// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';



//$mensagem = $_GET['message'];
$id_usuario_teste = isset($_COOKIE['chat_usuario_conversa']) && strcmp($_COOKIE['chat_usuario_conversa'], 'undefined') !== 0 ? $_COOKIE['chat_usuario_conversa'] : '';
$id_suporte = isset($_COOKIE['chat_id_suporte_conversa']) && strcmp($_COOKIE['chat_id_suporte_conversa'], 'undefined') !== 0 ? $_COOKIE['chat_id_suporte_conversa'] : '';
$id_conversa = isset($_COOKIE['chat_id_conversa_suporte']) && strcmp($_COOKIE['chat_id_conversa_suporte'], 'undefined') !== 0 ? $_COOKIE['chat_id_conversa_suporte'] : '';


$data_chat = date('H:i:s');

$id_usuario = null;

if (isset($id_usuario_teste) && is_numeric(isset($id_usuario_teste))) {

    $id_usuario = trim(isset($_COOKIE['chat_usuario_conversa']));

} elseif (isset($_COOKIE['id_usuario_sessao']) && is_numeric($_COOKIE['id_usuario_sessao'])) {

    $id_usuario = trim($_COOKIE['id_usuario_sessao']);

}



if($id_usuario === '' || $id_usuario === null){
    $value = 'Sentimos muito! <br/>O STEP Não Conseguiu Validar o usuário para Verificar Novas Mensagens de Chat, caso o Erro Persista, por gentileza entre em contato com o Suporte.';

    $_SESSION['error'] =  $value;
   
    header("Location: /views/dashboard.php");
    exit;
}


if ($id_conversa != '' && $id_usuario != '' && $id_suporte != '') {
    //******* status_conversa
  // 1 nova conversa, 2 conversa em andamento, conversa finalizada	

  //**** status_log_conversa = 1 = nova | 2 = recebida | 3 = lida */

 
  try {
    
    // Preparando a consulta SQL
$stmt = $conexao->prepare("SELECT lg.*, u.nome, u.foto FROM log_conversa lg
INNER JOIN usuarios u ON u.id = lg.id_remetente WHERE lg.id_conversa = :id_conversa 
AND lg.status_log_conversa = 1 AND lg.id_destinatario = :id_destinatario GROUP BY lg.id_conversa");

$stmt->bindParam(':id_conversa', $id_conversa);
$stmt->bindParam(':id_destinatario', $_SESSION['id']);

// Executando a consulta
$stmt->execute();

     

// Armazenando o resultado em um array
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificando se há um resultado
if ($result) {
  $remetente = $result['nome'];
  $id_remetente = $result['id_remetente'];
  $foto = $result['foto'];
  if($foto){

      $saida_foto = '/foto-perfil/'.$foto;
  }else{

      $saida_foto ='/foto-perfil/avatar.png';

  }
  $data_chat = date('d/m/Y H:i:s', strtotime($result['data_envio']));
  $mensagem = $result['conversa'];
  $id_conversa = $result['id_conversa'];
  $id_suporte = $result['id_suporte'];
  

  // Construindo o retorno com as informações desejadas
  $retorno = array(
      'codigo' => 1,
      'id_suporte' => $id_suporte,
      'id_conversa' =>$id_conversa,
      'remetente' => $remetente,
      'id_remetente' => $id_remetente,
      'foto' => $saida_foto ,
      'mensagem' => $mensagem,
      'hora' => $data_chat
  );

  echo json_encode($retorno);

} else {
  $retorno = array('codigo' => 2, 'mensagem' => 'Nenhum resultado encontrado para chat sem id suporte');
  echo json_encode($retorno);
}


      // Atualizando o status da conversa do suporte selecionado para 2 // recebida
      if (!empty($result)) {
          $update_stmt = $conexao->prepare("UPDATE log_conversa SET status_log_conversa = 2 WHERE id_conversa = :id_conversa");
          $update_stmt->bindParam(':id_conversa', $id_conversa);
         
          if ($update_stmt->execute()) {
              $update_stmt = $conexao->prepare("UPDATE  suporte_conversas SET status_conversa = 2 WHERE id_conversa = :id_conversa");
              $update_stmt->bindParam(':id_conversa', $id_conversa);
              $update_stmt->execute();
          }
      }

     

  } catch (PDOException $e) {
      echo "Erro: " . $e->getMessage();
  }

  // Fechando a conexão com o banco de dados
  $conexao = null;
  exit;
} else if

 ($id_conversa != '' && $id_usuario != '' && $id_suporte == '') {
      //******* status_conversa
    // 1 nova conversa, 2 conversa em andamento, conversa finalizada	

    //**** status_log_conversa = 1 = nova | 2 = recebida | 3 = lida */

   
    try {
      
      // Preparando a consulta SQL
$stmt = $conexao->prepare("SELECT lg.*, u.nome, u.foto FROM log_conversa lg
INNER JOIN usuarios u ON u.id = lg.id_remetente WHERE lg.id_conversa = :id_conversa 
AND lg.status_log_conversa = 1 AND lg.id_destinatario = :id_destinatario GROUP BY lg.id_conversa");

$stmt->bindParam(':id_conversa', $id_conversa);
$stmt->bindParam(':id_destinatario', $_SESSION['id']);

// Executando a consulta
$stmt->execute();

       

// Armazenando o resultado em um array
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificando se há um resultado
if ($result) {
    $remetente = $result['nome'];
    $id_remetente = $result['id_remetente'];
    $foto = $result['foto'];
    if($foto){

        $saida_foto = '/foto-perfil/'.$foto;
    }else{

        $saida_foto ='/foto-perfil/avatar.png';

    }
    $data_chat = date('d/m/Y H:i:s', strtotime($result['data_envio']));
    $mensagem = $result['conversa'];
    $id_conversa = $result['id_conversa'];
    $id_suporte = $result['id_suporte'];
    

    // Construindo o retorno com as informações desejadas
    $retorno = array(
        'codigo' => 1,
        'id_suporte' => $id_suporte,
        'id_conversa' =>$id_conversa,
        'remetente' => $remetente,
        'id_remetente' => $id_remetente,
        'foto' => $saida_foto ,
        'mensagem' => $mensagem,
        'hora' => $data_chat
    );
    echo json_encode($retorno);
} else {
    $retorno = array('codigo' => 2, 'mensagem' => 'Nenhum resultado encontrado para chat sem id suporte');
    echo json_encode($retorno);
}


        // Atualizando o status da conversa do suporte selecionado para 2 // recebida
        if (!empty($result)) {
            $update_stmt = $conexao->prepare("UPDATE log_conversa SET status_log_conversa = 2 WHERE id_conversa = :id_conversa");
            $update_stmt->bindParam(':id_conversa', $id_conversa);
           
            if ($update_stmt->execute()) {
                $update_stmt = $conexao->prepare("UPDATE  suporte_conversas SET status_conversa = 2 WHERE id_conversa = :id_conversa");
                $update_stmt->bindParam(':id_conversa', $id_conversa);
                $update_stmt->execute();
            }
        }

        


    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }

    // Fechando a conexão com o banco de dados
    $conexao = null;
    exit;
    
} else if ($id_conversa == '' && $id_usuario == '' && $id_suporte == '') {
    //******* status_conversa
  // 1 nova conversa, 2 conversa em andamento, conversa finalizada	

  //**** status_log_conversa = 1 = nova | 2 = recebida | 3 = lida */

 
  try {
    
    // Preparando a consulta SQL
    $stmt = $conexao->prepare("SELECT lg.*, u.nome, u.foto FROM log_conversa lg
    INNER JOIN usuarios u ON u.id = lg.id_remetente WHERE
    lg.status_log_conversa = 1 AND lg.id_destinatario = :id_destinatario ");
    
$stmt->bindParam(':id_destinatario', $_SESSION['id']);

// Executando a consulta
$stmt->execute();

     

// Armazenando o resultado em um array
$result = $stmt->fetch(PDO::FETCH_ASSOC);




$conta = $stmt->rowCount();

// Verificando se há um resultado
if ($conta > 0) {
  $remetente = $result['nome'];
  $id_remetente = $result['id_remetente'];
  $foto = $result['foto'];
  if($foto){

      $saida_foto = '/foto-perfil/'.$foto;
  }else{

      $saida_foto ='/foto-perfil/avatar.png';

  }
  $data_chat = date('d/m/Y H:i:s', strtotime($result['data_envio']));
  $mensagem = $result['conversa'];
  $id_conversa = $result['id_conversa'];
  $id_suporte = $result['id_suporte'];
  

  // Construindo o retorno com as informações desejadas
  $retorno = array(
      'codigo' => 1,
      'id_suporte' => $id_suporte,
      'id_conversa' =>$id_conversa,
      'remetente' => $remetente,
      'id_remetente' => $id_remetente,
      'foto' => $saida_foto ,
      'mensagem' => $mensagem,
      'hora' => $data_chat
  );

  echo json_encode($retorno);

} else {
  $retorno = array('codigo' => 2, 'mensagem' => 'Nenhum resultado encontrado para chat Livre');

   echo json_encode($retorno);
}


      // Atualizando o status da conversa do suporte selecionado para 2 // recebida
      if (!empty($result)) {
          $update_stmt = $conexao->prepare("UPDATE log_conversa SET status_log_conversa = 2 WHERE id_conversa = :id_conversa");
          $update_stmt->bindParam(':id_conversa', $id_conversa);
         
          if ($update_stmt->execute()) {
              $update_stmt = $conexao->prepare("UPDATE  suporte_conversas SET status_conversa = 2 WHERE id_conversa = :id_conversa");
              $update_stmt->bindParam(':id_conversa', $id_conversa);
              $update_stmt->execute();
          }
      }

      

     

  } catch (PDOException $e) {
      echo "Erro: " . $e->getMessage();
  }

  // Fechando a conexão com o banco de dados
  $conexao = null;
  exit;
} else {

    $retorno = array('codigo' => 2, 'mensagem' => 'Chat Não disponível');
    echo json_encode($retorno);
}



 