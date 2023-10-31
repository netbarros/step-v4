<?php
 // buffer de saída de dados do php]
// Instancia Conexão PDO
require_once "../../../conexao.php";
$conexao = Conexao::getInstance();
require_once "../../../crud/login/verifica_sessao.php";

$id_arquivo = isset($_POST['id']) ? $_POST['id'] : '';
$projeto_nome = isset($_POST['nome']) ? $_POST['nome'] : '';




if($id_arquivo!='' ){

    $sql = "DELETE FROM arquivos_projeto  WHERE id_doc=?";
$conexao->prepare($sql)->execute([$id_arquivo]);



if($sql){

    $retorno = array('codigo' => 1, 'retorno' => 'Arquivo apagado com Sucesso!');

    echo json_encode($retorno);

    exit;
}
}