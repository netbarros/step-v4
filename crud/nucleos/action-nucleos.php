<?php
header("content-type: application/json");
//require_once '../../../conexao.php';
// Atribui uma conexão PDO

require '../../conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();


$acao = (isset($_POST['acao'])) ? $_POST['acao'] : '';

$id_obra = (isset($_POST['id_projeto'])) ? $_POST['id_projeto'] : '';
$id_cliente = (isset($_POST['id_cliente'])) ? $_POST['id_cliente'] : '';
$nome_nucleo = (isset($_POST['nome_nucleo'])) ? $_POST['nome_nucleo'] : '';


$latitude = (isset($_POST['latitude_nucleo'])) ? $_POST['latitude_nucleo'] : '';
$longitude = (isset($_POST['longitude_nucleo'])) ? $_POST['longitude_nucleo'] : '';

$status_nucleo = $_POST['status_nucleo'] ?? '3';



$data_cadastro= date_create()->format('Y-m-d H:i:s'); 


if($acao=="cadastrar"){


    if($id_cliente==""){

        $sql = $conexao->prepare("SELECT id_cliente FROM obras WHERE id_obra = :id_obra");
        $sql->bindParam(':id_obra', $id_obra, PDO::PARAM_INT);
        $sql->execute();
        $id_cliente = $sql->fetchColumn();
        
    }

    $carimbo = $conexao->prepare("INSERT INTO estacoes (
      id_obra,
      id_cliente,
      nome_estacao,
      latitude,
      longitude,
      data_cadastro,
      status_estacao
      ) VALUES (?,?,?,?,?,?,?)");

    $carimbo->bindValue(1, $id_obra, PDO::PARAM_STR);
    $carimbo->bindValue(2, $id_cliente, PDO::PARAM_STR);
    $carimbo->bindValue(3, $nome_nucleo, PDO::PARAM_STR);
    $carimbo->bindValue(4, $latitude, PDO::PARAM_STR);
    $carimbo->bindValue(5, $longitude, PDO::PARAM_STR);
    $carimbo->bindValue(6, $data_cadastro, PDO::PARAM_STR);
    $carimbo->bindValue(7, $status_nucleo, PDO::PARAM_STR);

    $carimbo->execute();

    $ultimo_id = $conexao->lastInsertId();


if($carimbo){
  
    
    $retorno = array('codigo' => 1,  'mensagem' => 'Cadastro Realizado com Sucesso!<br> <span class="text-warning fs-6">Lembre-se Incluir: Supervisor e Ro, Em Vincular Usuários, assim como o responsável do Cliente, para acompanhamento.</span>', 'id'=>$ultimo_id);


    echo json_encode($retorno);

    exit;
} else {
    $retorno = array('codigo' => 0,  'mensagem' => 'Falha ao Gravar!');


    echo json_encode($retorno);

    exit;

}

}


if($acao=="alterar"){



    $id_nucleo=$_POST['id'];


    $data = [
        'nome_estacao' => $nome_nucleo,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'status_estacao'=> $status_nucleo,
        'id' => $id_nucleo
    ];

    $sql = "UPDATE estacoes SET 
    nome_estacao= :nome_estacao, 
    latitude= :latitude,
    longitude= :longitude,
    status_estacao = :status_estacao

    WHERE id_estacao =:id";

   // print_r($data);

    $conexao->prepare($sql)->execute($data);



    if($sql){

        $retorno = array('codigo' => 1,  'mensagem' => 'Alterado com Sucesso!<br> <span class="text-warning fs-6">Lembre-se Incluir: Supervisor e Ro, Em Vincular Usuários, assim como o responsável do Cliente, para acompanhamento.</span>');


    echo json_encode($retorno);

    exit;
    } else {


        $retorno = array('codigo' => 0,  'mensagem' => 'Falha executar a Alteração!');


        echo json_encode($retorno);
    
        exit;
    
    }




}

