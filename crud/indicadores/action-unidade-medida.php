<?php

require_once '../../conexao.php';
 	// Atribui uma conexão PDO
     $conexao = Conexao::getInstance();
     if (!isset($_SESSION)) session_start();	

// pega os dados do formuário da OBRA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

$acao    = (isset($_POST['acao'])) ? $_POST['acao'] : '';

$nome_unidade_medida= isset($_POST['valor_input_nova_unidade']) ? $_POST['valor_input_nova_unidade'] :'';
$data_cadastro = date_create()->format('Y-m-d H:i:s');


if($acao == "inclui-nova-unidade"){

$sql=$conexao->query("SELECT nome_unidade_medida FROM unidade_medida WHERE nome_unidade_medida LIKE '%$nome_unidade_medida%' ");

$verifica = $sql->rowCount();

if($verifica>0){

    $retorno = array('codigo' => 0, 'mensagem' => 'A Unidade de Medida já existe!');

    echo json_encode($retorno);

    exit;


}



try {
    $stmt = $conexao->prepare("INSERT INTO unidade_medida ( 
            nome_unidade_medida, 
               data_cadastro
                   ) 
VALUES (?, ?)");
   
    $stmt->bindParam(1, $nome_unidade_medida);
   
    $stmt->bindParam(2, $data_cadastro);

    
    $stmt->execute();

    $ultimo_id = $conexao->lastInsertId();

   

    if ($stmt) {

        $retorno = array('codigo' => 1, 'mensagem' => 'Unidade de Medida -  Cadastrada com Sucesso!','id_retorno'=>$ultimo_id);


        echo json_encode($retorno);

    } else {
        $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao tentar efetivar cadastro!');

        echo json_encode($retorno);

    }

} catch (PDOException $erro) {
    echo "Erro: " . $erro->getMessage();
}

$conexao=null;

} // finaliza tipo Equipamento

}