<?php
require_once '../../conexao.php';
// Atribui uma conexão PDOcolab
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();

$acao = trim(isset($_POST['acao'])) ? $_POST['acao'] : '';

$avatar = trim(isset($_POST['avatar'])) ? $_POST['avatar'] : '';


$id_usuario = trim(isset($_POST['id'])) ? $_POST['id'] : '';



if($acao=='cadastrar'){

    /* Get the name of the uploaded file */
$filename = $_FILES['avatar']['name'];

// Store the file extension or type
$type = $_FILES["avatar"]["type"];

// Store the file size
$size = $_FILES["avatar"]["size"];

/* Choose where to save the uploaded file */
$dir = $_SERVER['DOCUMENT_ROOT'] . '/foto-perfil/';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$location = $dir . $filename;

/* Save the uploaded file to the local filesystem */
if (move_uploaded_file($_FILES['avatar']['tmp_name'], $location)) {
    $sql = $conexao->prepare("UPDATE usuarios SET foto=:filename WHERE id=:id_usuario");
    $sql->bindParam(':filename', $filename);
    $sql->bindParam(':id_usuario', $id_usuario);

    if ($sql->execute()) {
        $retorno = array('codigo' => 1, 'mensagem' => 'Avatar incluído com Sucesso.', 'avatar' => $filename);
    } else {
        $retorno = array('codigo' => 0, 'mensagem' => 'Falha ao incluir o Avatar.');
    }
} else {
    $retorno = array('codigo' => 0, 'mensagem' => 'Falha ao mover o arquivo.');
}

echo json_encode($retorno);
exit;


}


if($acao=='apagar'){



  $sql=$conexao->query("DELETE foto FROM usuarios WHERE id='$id_usuario'");

  if($sql){

    $retorno = array('codigo' => 1, 'retorno' => 'Avatar excluído com Sucesso.');

    echo json_encode($retorno);

    exit;


  }else{

    $retorno = array('codigo' => 0, 'retorno' => 'Falha ao excluir o Avatar.');

    echo json_encode($retorno);

    exit;

  }




}