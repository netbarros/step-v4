<?php
require_once '../../conexao.php';
header("Content-Type: application/json");
date_default_timezone_set('America/Sao_Paulo');
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}




// pega os dados do formuário da OBRA


    //$id = (isset($_POST["id"]) && $_POST["id"] != ?) ? $_POST["id"] : "";
    $id_plcode_atual    = trim(isset($_POST['id_plcode_atual'])) ? $_POST['id_plcode_atual'] : '';

    $id = trim(isset($_POST['id'])) ? $_POST['id'] : '';

    $id_ponto = trim(isset($_POST['id_plcode'])) ? $_POST['id_plcode'] : '';

    $id_estacao = trim(isset($_POST["id_estacao"])) ? $_POST["id_estacao"] : "";

    $id_sensor_iot = trim(isset($_POST["id_sensor_iot"])) ? $_POST["id_sensor_iot"] : "";


    $acao = trim(isset($_POST['acao'])) ? $_POST['acao'] : '';

    $ponto = trim(isset($_POST['id_plcode'])) ? $_POST['id_plcode'] : '';

    $gera_grafico = trim(isset($_POST['gera_grafico'])) ? $_POST['gera_grafico']  : '';

    $nome_parametro = trim(isset($_POST['nome_parametro'])) ? $_POST['nome_parametro'] : '';
    $origem_leitura_parametro = (isset($_POST['origem_leitura_parametro'])) ? $_POST['origem_leitura_parametro'] : '';
    $controle_concentracao = trim(isset($_POST['controle_concentracao'])) ? $_POST['controle_concentracao'] : '';
    $unidade_medida = trim(isset($_POST['unidade_medida'])) ? $_POST['unidade_medida'] : '';

    $status_parametro = trim(isset($_POST['status_parametro'])) ? $_POST['status_parametro'] : '';


    $estacao = trim(isset($_POST['estacao'])) ? $_POST['estacao'] : '';


    $data_cadastro = date_create()->format('Y-m-d H:i:s');


    $concen_min = isset($_POST['concen_min']) ? trim($_POST['concen_min']) : '';
    $concen_min = $concen_min === '' ? NULL : $concen_min;
    
    $concen_max = isset($_POST['concen_max']) ? trim($_POST['concen_max']) : '';
    $concen_max = $concen_max === '' ? NULL : $concen_max;
    

    if($concen_min==0){

        $concen_min='0.00';
    }

     
    if($concen_max==0){

        $concen_max='0.00';
    }

//============[ Parâmetro de Tratamento  do PLCode]=================================== 


if ($acao == "novo_indicador") {


    if($unidade_medida==''){

        $retorno = array('codigo' => 0, 'mensagem' => 'Informe a Unidade de Medida do Indicador');

        echo json_encode($retorno, true);
exit;

    }



    if($controle_concentracao==''){

        $retorno = array('codigo' => 0, 'mensagem' => 'Informe o Controle de Origem dos Parâmetros');

        echo json_encode($retorno, true);
exit;

    }


    $stmt = $conexao->prepare('INSERT INTO  parametros_ponto (

                nome_parametro, 
                origem_leitura_parametro,
                controle_concentracao,
                unidade_medida,
                status_parametro,
                data_cadastro,
                id_ponto,
                concen_min,
                concen_max,
                gera_grafico,
                id_sensor_iot
              
              
              ) VALUES(
                :nome_parametro, 
                :origem_leitura_parametro,
                :controle_concentracao,
                :unidade_medida,
                :status_parametro,
                :data_cadastro,
                :id_ponto,
                :concen_min,
                :concen_max,
                :gera_grafico,
                :id_sensor_iot
                  
                  
                  )');
    $stmt->execute(array(
        ':nome_parametro' => $nome_parametro,
        ':origem_leitura_parametro' => $origem_leitura_parametro,
        ':controle_concentracao' => $controle_concentracao,
        ':unidade_medida' => $unidade_medida,
        ':status_parametro' => $status_parametro,
        ':data_cadastro' => $data_cadastro,
        ':id_ponto' => $id_ponto,
        ':concen_min' => $concen_min,
        ':concen_max' => $concen_max,
        ':gera_grafico' => $gera_grafico,
        ':id_sensor_iot' => $id_sensor_iot

    ));





    if ($stmt) {


        $ultimo_id = $conexao->lastInsertId();



        //==================[ LOG do Sistema ] =======================//
        

        //ação do log(Acesso, Exclusão, Atualização, Inserção)
        $log_acao = $acao;
        //Rotina acessada
        $log_rotina = "Módulo: " . $_SESSION['modulo'] ;
        //historico, O que alterou, excluiu, ou acessou...

        $log_nivel_usuario = isset($_COOKIE['nivel_acesso_usuario']) ? trim($_COOKIE['nivel_acesso_usuario']) : '';

        $pega_nome_cliente = $conexao->query("Select pontos_estacao.nome_ponto, estacoes.nome_estacao, clientes.nome_fantasia From clientes INNER JOIN pontos_estacao ON clientes.id_cliente = pontos_estacao.id_cliente INNER JOIN estacoes ON estacoes.id_estacao=pontos_estacao.id_estacao WHERE pontos_estacao.id_ponto=" . $ponto . "");
        $row = $pega_nome_cliente->fetch(PDO::FETCH_OBJ);

        //var_dump($row);

        $log_historico = "O Usuário:" . $_SESSION['nome'] . ", efetuou o cadastro do Parâmetro: " . $nome_parametro . ", para o PLCode: " . $row->nome_ponto . ", da Estação: " . $row->nome_estacao . " e Cliente: " . $row->nome_fantasia . " ";
        // Nome do Usuário Registrado na Sessão

        $log_usuario = $_SESSION['id'];

        require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/log/log_sistema.php';

        //==================[ Finaliza Log Sistema ]==================//


        $retorno = array('codigo' => 1, 'id_ponto' => $ponto, 'mensagem' => 'Parâmetro:' . $nome_parametro . '</br> Incluído com Sucesso!', 'id_retorno' => $ultimo_id);


        echo json_encode($retorno, true);
    } else {
        $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao tentar efetivar cadastro!');

        echo json_encode($retorno, true);
    }



    $conexao = null;
} // finaliza Novo Parâmetro de Tratamento do PLCode




if ($acao == "altera_indicador") {


   


        $rs = $conexao->prepare("UPDATE parametros_ponto SET 
            id_ponto=:id_ponto,
            nome_parametro=:nome_parametro,
            origem_leitura_parametro=:origem_leitura_parametro,
            controle_concentracao=:controle_concentracao, 
            concen_min=:concen_min, 
            concen_max=:concen_max,
            unidade_medida=:unidade_medida,
            gera_grafico=:gera_grafico, 
            status_parametro=:status_parametro 

             WHERE id_parametro = '$id'");

        $rs->bindParam(":id_ponto", $id_ponto, PDO::PARAM_INT);
        $rs->bindParam(":nome_parametro", $nome_parametro, PDO::PARAM_STR);
        $rs->bindParam(":origem_leitura_parametro", $origem_leitura_parametro, PDO::PARAM_STR);
        $rs->bindParam(":controle_concentracao", $controle_concentracao, PDO::PARAM_STR);
        $rs->bindParam(":concen_min", $concen_min, PDO::PARAM_STR);
        $rs->bindParam(":concen_max", $concen_max, PDO::PARAM_STR);
        $rs->bindParam(":unidade_medida", $unidade_medida, PDO::PARAM_INT);
        $rs->bindParam(":status_parametro", $status_parametro, PDO::PARAM_INT);
        $rs->bindParam(":gera_grafico", $gera_grafico, PDO::PARAM_INT);
        $rs->execute();



        if ($rs) {




            //==================[ LOG do Sistema ] =======================//


            //ação do log(Acesso, Exclusão, Atualização, Inserção)
            $log_acao = $acao;
            //Rotina acessada
            $log_rotina = "Módulo: Indicadores (Parâmetros) -> SubMódulo: Alteração de Indicador";
            //historico, O que alterou, excluiu, ou acessou... $nivel_acesso_user_sessao =  isset($_COOKIE['nivel_acesso_usuario']) ? trim($_COOKIE['nivel_acesso_usuario']) : '';

            $log_nivel_usuario =  isset($_COOKIE['nivel_acesso_usuario']) ? trim($_COOKIE['nivel_acesso_usuario']) : '';

            $pega_nome_cliente = $conexao->query("SELECT clientes.nome_fantasia, pontos_estacao.nome_ponto, estacoes.nome_estacao From clientes 
            INNER JOIN pontos_estacao ON clientes.id_cliente = pontos_estacao.id_cliente 
            INNER JOIN estacoes ON estacoes.id_estacao=pontos_estacao.id_estacao WHERE pontos_estacao.id_ponto='$id_ponto'");
            $row = $pega_nome_cliente->fetch(PDO::FETCH_OBJ);

            //print_r($pega_nome_cliente);




            $log_historico = "O Usuário:" . $_SESSION['nome'] . ", efetuou o alteração do Indicador: " . $nome_parametro . ", para o PLCode: " . $row->nome_ponto . ", da Estação: " . $row->nome_estacao . " e Cliente: " . $row->nome_fantasia . " ";
            // Nome do Usuário Registrado na Sessão

            $log_usuario = $_SESSION['id'];



            require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/log/log_sistema.php';





            //==================[ Finaliza Log Sistema ]==================//

            $retorno = array('codigo' => 1, 'id_ponto' => $id_ponto, 'mensagem' => 'Indicador:' . $nome_parametro . '</br> Alterado com Sucesso!', 'id_retorno' => $id_ponto);


            echo json_encode($retorno);
        } else {
            $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao tentar efetivar cadastro!');

            echo json_encode($retorno);
        }
   
} // finaliza Altera Parâmetro de Tratamento do PLCode





//======================[ Fecha Parâmetros de Tratamento ] =============================


if ($acao == "apaga_indicador") {

    
    if($_COOKIE['nivel_acesso_usuario']!='admin'){

        $retorno = array('codigo' => 0, 'mensagem' => "<span class='text-danger f4'>Ação não Permitida!</span><p>Esta operação é permitida somente para administradores do sistema.</p>");
        echo json_encode($retorno);
        $conexao = null;
        exit;
    
    }

    $sql = $conexao->query("SELECT id_parametro FROM rmm WHERE id_parametro = $id");
    $data = $sql->fetch(PDO::FETCH_OBJ);

    $conta = $sql->rowCount();


    

    if ($conta > 0) {

        
        $sql_valida = $conexao->query("UPDATE parametros_ponto SET status_parametro='3' WHERE id_parametro='$id'");
        
            $retorno = array('codigo' => 0,  'mensagem' => 'Este Indicador possui Dados de Monitoramento, não sendo possível Excluí-lo, '.$conta.' apenas Inativá-lo.');

        echo json_encode($retorno);
        exit;
    } else {

        try {
            $stmt = $conexao->prepare("DELETE FROM parametros_ponto WHERE id_parametro = ?");
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                // echo "Registo foi excluído com êxito";


                $retorno = array('codigo' => 1,  'mensagem' => 'Indicador excluído com Sucesso!');


                echo json_encode($retorno);
                // $id = ?;
            } else {
                throw new PDOException("Erro: Não foi possível executar a declaração sql");
            }
        } catch (PDOException $erro) {
            echo "Erro: " . $erro->getMessage();
        }
    }

    $conexao = null;
}


?>