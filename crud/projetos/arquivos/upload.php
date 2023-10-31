<?php
require_once '../../../conexao.php';
header("Content-Type: application/json");
date_default_timezone_set('America/Sao_Paulo');
// Atribui uma conexão PDO
     $conexao = Conexao::getInstance();
     if (!isset($_SESSION)) session_start();	

//data e hora do log
   $datahora = date("Y-m-d H:i:s");   


   $projeto = trim(isset($_GET['projeto'])) ? $_GET['projeto'] : '';   
   $file = trim(isset($_POST['file'])) ? $_POST['file'] : '';   
   $usuario_sessao = $_SESSION['id'];

if($projeto!=''){



/* Get the name of the uploaded file */
$filename = $_FILES['file']['name'];

$filename_size = $_FILES['file']['size'];

/* Choose where to save the uploaded file */
$location = $_SERVER['DOCUMENT_ROOT'] . '/arquivo-projeto/'. $filename;

/* Save the uploaded file to the local filesystem */
if ( move_uploaded_file($_FILES['file']['tmp_name'], $location) ) { 



   $sql = $conexao->query("INSERT INTO arquivos_projeto (id_obra, id_usuario, nome_doc, arquivo_doc,size) VALUES('$projeto','$usuario_sessao','$filename','$filename','$filename_size' )");
   
   $retorno = array('codigo' => 1, 'retorno' => 'Arquivo enviado com Sucesso!');

   echo json_encode($retorno);

   exit;
} else { 

   $retorno = array('codigo' => 0, 'retorno' => 'Falha ao enviar o Arquivo!');

   echo json_encode($retorno);

   exit;
}


    
}