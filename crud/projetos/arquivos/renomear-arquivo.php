<?php
 // buffer de saída de dados do php]
// Instancia Conexão PDO
require_once "../../../conexao.php";
$conexao = Conexao::getInstance();
require_once "../../../crud/login/verifica_sessao.php";

$projeto_id = isset($_POST['projeto']) ? $_POST['projeto'] : '';
$projeto_nome = isset($_POST['nome']) ? $_POST['nome'] : '';




if($projeto_id!='' && $projeto_nome!=''){

    $sql = "UPDATE arquivos_projeto SET nome_doc=? WHERE id_doc=?";
$conexao->prepare($sql)->execute([$projeto_nome, $projeto_id]);



if($sql){

    $retorno = array('codigo' => 1, 'retorno' => 'Nome do Arquivo alterado com Sucesso!');

    echo json_encode($retorno);

    exit;
}
}