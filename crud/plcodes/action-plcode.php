<?php

require_once '../../conexao.php';
 	// Atribui uma conexão PDO
     $conexao = Conexao::getInstance();
     if (!isset($_SESSION)) session_start();	
     date_default_timezone_set('America/Sao_Paulo');
     
// pega os dados do formuário da OBRA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

$id_obra = trim(isset($_POST["id_projeto"])) ? $_POST["id_projeto"] : "";
$id_plcode_atual    = trim(isset($_POST['id_plcode_atual'])) ? $_POST['id_plcode_atual'] : '';

$tipo_ponto = isset($_POST['tipo_plcode']) ? $_POST['tipo_plcode'] :'';

$latitude_p =isset($_POST['latitude_plcode']) ? str_replace('_', '', $_POST['latitude_plcode']) :'';

$longitude_p =isset($_POST['longitude_plcode']) ? str_replace('_', '', $_POST['longitude_plcode']) :'';

$instrucao_operacional =isset($_POST['texto_instrucao_operacional_plcode']) ? $_POST['texto_instrucao_operacional_plcode'] :'';

$nome_plcode= isset($_POST['plcode_nome']) ? $_POST['plcode_nome'] :'';

$id_estacao=trim(isset($_POST["nucleo_plcode"])) ? $_POST["nucleo_plcode"] : "";

$id_ponto_anterior = trim(isset($_POST['plcode_anterior'])) ? $_POST['plcode_anterior'] : '';

$id_tipo_tanque = trim(isset($_POST['id_tipo_tanque'])) ? $_POST['id_tipo_tanque'] : '';
$id_tipo_equipamento = trim(isset($_POST['id_tipo_equipamento'])) ? $_POST['id_tipo_equipamento'] : '';
$id_tipo_instrumento = trim(isset($_POST['id_tipo_instrumento'])) ? $_POST['id_tipo_instrumento'] : '';

$objetivo_plcode    = (isset($_POST['objetivo_plcode'])) ? $_POST['objetivo_plcode'] : '';


$acao= trim(isset($_POST['acao'])) ? $_POST['acao'] : '';

$data_cadastro = date_create()->format('Y-m-d H:i:s');

$status_plcode = (isset($_POST['kt_add_plcode_status_select'])) ? $_POST['kt_add_plcode_status_select'] : '';


}


if($acao == "novo_plcode_passo_1"){


    try {

        $rs = $conexao->prepare('INSERT INTO pontos_estacao (id_estacao, id_obra, id_ponto_anterior,tipo_ponto, nome_ponto, objetivo_ponto, status_ponto, data_cadastro) VALUES (:id_estacao, :id_obra, :id_ponto_anterior, :tipo_ponto, :nome_ponto, :objetivo_ponto, :status_ponto, :data_cadastro )');  
        $rs->bindParam(':id_estacao', $id_estacao, PDO::PARAM_INT);  
        $rs->bindParam(':id_obra', $id_obra, PDO::PARAM_INT);   
        $rs->bindParam(':id_ponto_anterior', $id_ponto_anterior, PDO::PARAM_INT); 
        $rs->bindParam(':tipo_ponto', $tipo_ponto, PDO::PARAM_INT);  
        $rs->bindParam(':nome_ponto', $nome_plcode, PDO::PARAM_STR);  
        $rs->bindParam(':objetivo_ponto', $objetivo_plcode, PDO::PARAM_STR);  
        $rs->bindParam(':status_ponto', $status_plcode, PDO::PARAM_INT);  
        $rs->bindParam(':data_cadastro', $data_cadastro, PDO::PARAM_STR);  
   
        $rs->execute();

        $ultimo_id = $conexao->lastInsertId();


       

         if($rs){

       
              $retorno = array('codigo' => 1, 'mensagem' => "Os dados Básicos do novo PLCode: '{$nome_plcode}'</br> forma salvos com sucesso!<br>Prossiga com as Definições Avançadas!",'id_novo_plcode'=> "{$ultimo_id}", 'nome_novo_plcode'=> "{$nome_plcode}");
    
              echo json_encode($retorno);
              exit;

            } else {
                $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao tentar Cadastrar o PLCode!');

                echo json_encode($retorno);
                exit;

            }


    } catch (PDOException $erro) {
        echo "Erro: " . $erro->getMessage();
    }




}



//===[ 2 etapa da finalizacao do cadastro do plcode]====///



if($acao == "novo_plcode_passo_2"){
  
    if($tipo_ponto==""){ 


        $mensagem ="<p><i class='flaticon-warning-sign'></i> Necessário informar o <b>Tipo de PLCode</b>.</p>";
        $retorno = array('codigo' => 0, 'mensagem' => $mensagem);
                     
          echo json_encode($retorno);
    
          exit;     
    
    }


    if($latitude_p==""){ 


        $mensagem ="<p><i class='flaticon-warning-sign'></i> Necessário informar a <b>Latitude do PLCode</b>.</p>";
        $retorno = array('codigo' => 0, 'mensagem' => $mensagem);
                     
          echo json_encode($retorno);
    
          exit;     
    
    }


    
    if($longitude_p==""){ 


        $mensagem ="<p><i class='flaticon-warning-sign'></i> Necessário informar a <b>Longitude do PLCode</b>.</p>";
        $retorno = array('codigo' => 0, 'mensagem' => $mensagem);
                     
          echo json_encode($retorno);
    
          exit;     
    
    }


    try {

        $rs = $conexao->prepare("UPDATE pontos_estacao SET 
        nome_ponto=:nome_ponto,
        id_estacao=:id_estacao,
        id_ponto_anterior=:id_ponto_anterior,
        latitude_p=:latitude_p,
        longitude_p=:longitude_p,
        tipo_ponto=:tipo_ponto,
        id_tipo_tanque=:id_tipo_tanque,
        id_tipo_equipamento=:id_tipo_equipamento,
        id_tipo_instrumento=:id_tipo_instrumento,
        objetivo_ponto=:objetivo_ponto,
        instrucao_operacional=:instrucao_operacional,
        status_ponto=:status_ponto 
        WHERE id_ponto = '$id_plcode_atual' ");  

        $rs->bindParam(':nome_ponto', $nome_plcode, PDO::PARAM_STR);  
        $rs->bindParam(':id_estacao', $id_estacao, PDO::PARAM_STR);  
        $rs->bindParam(':id_ponto_anterior', $id_ponto_anterior, PDO::PARAM_STR);  
        $rs->bindParam(':latitude_p', $latitude_p, PDO::PARAM_STR);  
        $rs->bindParam(':longitude_p', $longitude_p, PDO::PARAM_STR);  
        $rs->bindParam(':tipo_ponto', $tipo_ponto, PDO::PARAM_INT);  
        $rs->bindParam(':id_tipo_tanque', $id_tipo_tanque, PDO::PARAM_INT);  
        $rs->bindParam(':id_tipo_equipamento', $id_tipo_equipamento, PDO::PARAM_INT);  
        $rs->bindParam(':id_tipo_instrumento', $id_tipo_instrumento, PDO::PARAM_INT);  
        $rs->bindParam(':objetivo_ponto', $objetivo_plcode, PDO::PARAM_STR);  
        $rs->bindParam(':instrucao_operacional', $instrucao_operacional, PDO::PARAM_STR);  
        $rs->bindParam(':status_ponto', $status_plcode, PDO::PARAM_STR);  
 
   
        $rs->execute();


        //print_r($rs);

        if($rs){


          
      
         
           
   if($tipo_ponto=="1"){ // Tipo 1 = Indice Primário do Tipo de Ponto = TANQUE DE TRATAMENTO
   
       $volume_tanque=(isset($_POST["volume_tanque"])) ? $_POST["volume_tanque"] : "";
       $id_tipo_tanque=(isset($_POST["tipo_tanque"])) ? $_POST["tipo_tanque"] : "";
       $linha_entrada=(isset($_POST["linha_entrada"])) ? $_POST["linha_entrada"] : "";
       $linha_saida=(isset($_POST["linha_saida"])) ? $_POST["linha_saida"] : "";
       $status_tanque = (isset($_POST["status_tanque"])) ? $_POST["status_tanque"] : ""; // Ou o Tanque esta ativo ou com Problemas, Inativo só excluido para atualização por outro tipo de Ponto que não seja Tanque
       $controla_volume = (isset($_POST["controla_volume"])) ? $_POST["controla_volume"] : ""; // Ou o Tanque esta ativo ou com Problemas, Inativo só excluido para atualização por outro tipo de Ponto que não seja Tanque
   
   
   
   // verifico se já existe este "tipo de ponto" para o PLCode atual, se já houver este id_ponto atual em uma das tabelas (tanque, equipamento ou instrumento) com o mesmo tipo de equipamento, 
       // Faz as validações caso seja tipo de ponto = Tanque de Tratamento
       $mensagem='';
       if ($volume_tanque =="") {
     
      $mensagem .="<p><i class='flaticon-warning-sign'></i> Para cadastrar um Tanque é necessário informar o <b>Volume de Tratamento</b>.</p>";
      
      }  
   
      if ($status_tanque =="") {
          
       $mensagem .="<p><i class='flaticon-warning-sign'></i> Necessário informar o <b>Status do Tanque de Tratamento</b>.</p>";
       
       } 
   
      if ($id_tipo_tanque =="") {
     
          $mensagem .="<p><i class='flaticon-warning-sign'></i> Para cadastrar um Tanque é necessário informar o <b>Tipo de Operação no Tanque</b>.</p>";
          
          }  
   
      if ($mensagem != ''):
          $mensagem = "$mensagem";
         
          $retorno = array('codigo' => 'tipo_tanque', 'mensagem' => $mensagem);
                     
          echo json_encode($retorno);
   
          exit;      
      endif;
     
   //====================[ Finaliza as Validações do Tanque]=======================
   
   
   
       try {
   
   
   
           $consulta_existente=$conexao->query("SELECT id_ponto,id_tanque FROM tanques_ponto WHERE id_ponto='$id_plcode_atual'");
   
           $registro = $consulta_existente->fetch(PDO::FETCH_OBJ);
   
         
   
           $count = $consulta_existente->rowCount();
            //contagem de registros
           
            if($count > 0 ){ // caso já haja um tanque Incluído para este ponto, fazemos a alteração do mesmo...
   
               $id_consulta = $id_plcode_atual;
   
          // se o tanque já existir para este ponto, atualizo os dados do tanque
          
       $rs = $conexao->prepare("UPDATE tanques_ponto SET volume_tanque=:volume_tanque, id_tipo_tanque=:id_tipo_tanque, linha_entrada=:linha_entrada, linha_saida=:linha_saida, status_tanque=:status_tanque, controla_volume=:controla_volume WHERE id_ponto= $id_plcode_atual");
       $rs->bindParam(":volume_tanque", $volume_tanque, PDO::PARAM_STR);  
       $rs->bindParam(":id_tipo_tanque", $id_tipo_tanque, PDO::PARAM_STR);  
       $rs->bindParam(":linha_entrada", $linha_entrada, PDO::PARAM_STR);  
       $rs->bindParam(":linha_saida", $linha_saida, PDO::PARAM_STR);  
       $rs->bindParam(":status_tanque", $status_tanque, PDO::PARAM_STR);  
       $rs->bindParam(":controla_volume", $controla_volume, PDO::PARAM_STR);  
       $rs->execute(); 
             
   
   //var_dump($rs);

   //exit;

    $ultimo_id =$id_consulta;
           
              
   //sql para atualizar o id od tantue no ponto
               
            } else { // caso não haja tanque Incluído para este ponto, gravamos...
   
   
               $rs = $conexao->prepare("INSERT INTO  tanques_ponto (volume_tanque, id_tipo_tanque, linha_entrada, linha_saida,data_cadastro,status_tanque,controla_volume,id_ponto) VALUES (?,?,?,?,?,?,?,?)");
               $rs->bindValue(1, $volume_tanque, PDO::PARAM_STR);  
               $rs->bindValue(2, $id_tipo_tanque, PDO::PARAM_STR);  
               $rs->bindValue(3, $linha_entrada, PDO::PARAM_STR);  
               $rs->bindValue(4, $linha_saida, PDO::PARAM_STR); 
               $rs->bindValue(5, $data_cadastro, PDO::PARAM_STR);  
               $rs->bindValue(6, $status_tanque, PDO::PARAM_STR); 
               $rs->bindValue(7, $controla_volume, PDO::PARAM_STR);  
               $rs->bindValue(8, $id_plcode_atual, PDO::PARAM_STR);          
               $rs->execute();
               $ultimo_id = $conexao->lastInsertId();


               $sql_up = $conexao->query("UPDATE pontos_estacao SET id_tipo_tanque='$ultimo_id' WHERE id_ponto=''");
   
   
            } 
   
          
   
   
       } catch (PDOException $erro) {
           $retorno = array('codigo' => 0, 'mensagem' => "Erro: " . $erro->getMessage());
          // echo "Erro: " . $erro->getMessage();
          echo json_encode($retorno);

          exit;
       }
     
   } // finaliza inclusao se o tipo de ponto for tanque
   
   
   
   // prepara a inclusão se o tipo de ponto for equipamento
   
   if($tipo_ponto=="2" ){// Tipo 2 = Indice Primário do Tipo de Ponto = EQUIPAMENTO //tipo anterior = tanque  e  atual = equipamento
   
       $nome_equipamento=(isset($_POST["nome_equipamento"])) ? $_POST["nome_equipamento"] : "";
       $id_tipo_equipamento=(isset($_POST["id_tipo_equipamento"])) ? $_POST["id_tipo_equipamento"] : "";
       $carac_equipamento=(isset($_POST["carac_equipamento"])) ? $_POST["carac_equipamento"] : "";
       $capacidade_equipamento=(isset($_POST["capacidade_equipamento"])) ? $_POST["capacidade_equipamento"] : "";
       $posicao_inicial=(isset($_POST["posicao_inicial"])) ? $_POST["posicao_inicial"] : "";
       $status_equipamento = (isset($_POST["status_equipamento"])) ? $_POST["status_equipamento"] : ""; // Ou o Tanque esta ativo ou com Problemas, Inativo só excluido para atualização por outro tipo de Ponto que não seja Tanque
   
      
   
       // Faz as validações caso seja tipo de ponto = Tanque de Tratamento
               $mensagem="";
            if ($nome_equipamento =="") {
          
           $mensagem .="<p><i class='flaticon-warning-sign'></i> Para cadastrar um Equipamento é necessário informar a <b>Identificação do Equipamento</b>.</p>";
           
           }  
   
           if ($status_equipamento =="") {
          
               $mensagem .="<p><i class='flaticon-warning-sign'></i> Necessário informar o <b>Status do Equipamento</b>.</p>";
               
               } 
   
           if ($id_tipo_equipamento =="") {
          
               $mensagem .="<p><i class='flaticon-warning-sign'></i> Para cadastrar um Equipamento é necessário informar o <b>Tipo de Equipamento</b>.</p>";
               
               }  
   
           if ($mensagem != ''):
               $mensagem = "$mensagem";
              
               $retorno = array('codigo' => 0, 'mensagem' => $mensagem);
                          
               echo json_encode($retorno);
   
               exit;      
           endif;
          
   //====================[ Finaliza as Validações do Tanque]=======================
   
   
   
   try {
   
   
   $consulta_existente=$conexao->query("SELECT id_ponto,id_equipamento FROM equipamentos_ponto WHERE id_ponto='$id_plcode_atual'");
   
   $registro = $consulta_existente->fetch(PDO::FETCH_OBJ);
   $count = $consulta_existente->rowCount();
    //contagem de registros
   
    if($count > 0 ){
   
       $id_consulta = $registro->id_equipamento;
   
   
       // se o equipamento já existir para este ponto, atualizo os dados do equipamento
       $rs = $conexao->prepare("UPDATE equipamentos_ponto SET nome_equipamento=:nome_equipamento, id_tipo_equipamento=:id_tipo_equipamento, carac_equipamento=:carac_equipamento, capacidade_equipamento=:capacidade_equipamento, posicao_inicial=:posicao_inicial, status_equipamento=:status_equipamento WHERE id_ponto= $id_plcode_atual");
       $rs->bindParam(":nome_equipamento", $nome_equipamento, PDO::PARAM_STR);  
       $rs->bindParam(":id_tipo_equipamento", $id_tipo_equipamento, PDO::PARAM_STR);  
       $rs->bindParam(":carac_equipamento", $carac_equipamento, PDO::PARAM_STR);  
       $rs->bindParam(":capacidade_equipamento", $capacidade_equipamento, PDO::PARAM_STR);  
       $rs->bindParam(":posicao_inicial", $posicao_inicial, PDO::PARAM_STR);  
       $rs->bindParam(":status_equipamento", $status_equipamento, PDO::PARAM_STR);  
       $rs->execute(); 
   
   
       //var_dump($rs);
       $ultimo_id = $id_consulta;
   
       
   
   
   } else { // caso o equipamento não exista, gravo o mesmo.
   
   
       $rs = $conexao->prepare("INSERT INTO equipamentos_ponto (id_ponto, nome_equipamento, id_tipo_equipamento, carac_equipamento, capacidade_equipamento, posicao_inicial, data_cadastro, status_equipamento) VALUES(:id_ponto, :nome_equipamento, :id_tipo_equipamento, :carac_equipamento, :capacidade_equipamento, :posicao_inicial, :data_cadastro, :status_equipamento)");  
       $rs->bindParam(":id_ponto", $id_plcode_atual, PDO::PARAM_STR);  
       $rs->bindParam(":nome_equipamento", $nome_equipamento, PDO::PARAM_STR);  
       $rs->bindParam(":id_tipo_equipamento", $id_tipo_equipamento, PDO::PARAM_STR);  
       $rs->bindParam(":carac_equipamento", $carac_equipamento, PDO::PARAM_STR);  
       $rs->bindParam(":capacidade_equipamento", $capacidade_equipamento, PDO::PARAM_STR);  
       $rs->bindParam(":posicao_inicial", $posicao_inicial, PDO::PARAM_STR);  
       $rs->bindParam(":data_cadastro", $data_cadastro, PDO::PARAM_STR);  
       $rs->bindParam(":status_equipamento", $status_equipamento, PDO::PARAM_STR);      
       $rs->execute();
       $ultimo_id = $conexao->lastInsertId();
   
   
   }
   
           
           
   
       } catch (PDOException $erro) {
           $retorno = array('codigo' => 0, 'mensagem' => "Erro: " . $erro->getMessage());
          // echo "Erro: " . $erro->getMessage();
          echo json_encode($retorno);
          exit;
       }
   
     
   
       } // finaliza tipo de ponto equipamento
   
   
   
   
   // prepara a inclusão se o tipo de ponto for instrumento
   
   if($tipo_ponto=="3" ){// Tipo 3 = Indice Primário do Tipo de Ponto = INSTRUMENTO //
   
       $nome_instrumento=(isset($_POST["nome_instrumento"])) ? $_POST["nome_instrumento"] : "";
       $id_tipo_instrumento=(isset($_POST["id_tipo_instrumento"])) ? $_POST["id_tipo_instrumento"] : "";
       $carac_instrumento=(isset($_POST["carac_instrumento"])) ? $_POST["carac_instrumento"] : "";
       $capacidade_instrumento=(isset($_POST["capacidade_instrumento"])) ? $_POST["capacidade_instrumento"] : "";
       $status_instrumento = (isset($_POST["status_instrumento"])) ? $_POST["status_instrumento"] : ""; // Ou o Tanque esta ativo ou com Problemas, Inativo só excluido para atualização por outro tipo de Ponto que não seja Tanque
   
       $posicao_inicial=(isset($_POST["posicao_inicial"])) ? $_POST["posicao_inicial"] : "";
   
       // Faz as validações caso seja tipo de ponto = Tanque de Tratamento
               $mensagem="";
            if ($nome_instrumento =="") {
          
           $mensagem .="<p><i class='flaticon-warning-sign'></i> Para cadastrar um Instrumento é necessário informar a <b>Identificação do Instrumento</b>.</p>";
           
           }  
   
           if ($status_instrumento =="") {
          
               $mensagem .="<p><i class='flaticon-warning-sign'></i> Necessário informar o <b>Status do Instrumento</b>.</p>";
               
               }  
   
           if ($id_tipo_instrumento =="") {
          
               $mensagem .="<p><i class='flaticon-warning-sign'></i> Para cadastrar um Instrumento é necessário informar o <b>Tipo de Instrumento</b>.</p>";
               
               }  
   
           if ($mensagem != ''):
               $mensagem = "$mensagem";
              
               $retorno = array('codigo' => 0, 'mensagem' => $mensagem);
                          
               echo json_encode($retorno);
   
               exit;      
           endif;
          
   //====================[ Finaliza as Validações do Tanque]=======================
   
   
   
   try {
   
   
   
   
       $consulta_existente=$conexao->query("SELECT id_ponto,id_instrumento_ponto FROM instrumentos_ponto WHERE id_ponto='$id_plcode_atual'");
   
       $registro = $consulta_existente->fetch(PDO::FETCH_OBJ);
       $count = $consulta_existente->rowCount();
        //contagem de registros
       
        if($count > 0 ){
       
           $id_consulta = $registro->id_instrumento_ponto;
   
   
            // se o equipamento já existir para este ponto, atualizo os dados do equipamento
       $rs = $conexao->prepare("UPDATE instrumentos_ponto SET nome_instrumento=:nome_instrumento, id_tipo_instrumento=:id_tipo_instrumento, carac_instrumento=:carac_instrumento, capacidade_instrumento=:capacidade_instrumento, data_cadastro=:data_cadastro, status_instrumento=:status_instrumento WHERE id_ponto= $id_plcode_atual");
       $rs->bindParam(":nome_instrumento", $nome_instrumento, PDO::PARAM_STR);  
       $rs->bindParam(":id_tipo_instrumento", $id_tipo_instrumento, PDO::PARAM_STR);  
       $rs->bindParam(":carac_instrumento", $carac_instrumento, PDO::PARAM_STR);  
       $rs->bindParam(":capacidade_instrumento", $capacidade_instrumento, PDO::PARAM_STR);  
       $rs->bindParam(":data_cadastro", $data_cadastro, PDO::PARAM_STR);  
       $rs->bindParam(":status_instrumento", $status_instrumento, PDO::PARAM_STR);      
       $rs->execute();
             
       //var_dump($rs);
       $ultimo_id = $id_consulta;
   
   
   
       } else { // caso o equipamento não exista, gravo o mesmo.
   
   
       $rs = $conexao->prepare("INSERT INTO instrumentos_ponto (id_ponto, nome_instrumento, id_tipo_instrumento, carac_instrumento, capacidade_instrumento, data_cadastro, status_instrumento) VALUES(:id_ponto, :nome_instrumento, :id_tipo_instrumento, :carac_instrumento, :capacidade_instrumento, :data_cadastro, :status_instrumento)");  
       $rs->bindParam(":id_ponto", $id_plcode_atual, PDO::PARAM_STR);  
       $rs->bindParam(":nome_instrumento", $nome_instrumento, PDO::PARAM_STR);  
       $rs->bindParam(":id_tipo_instrumento", $id_tipo_instrumento, PDO::PARAM_STR);  
       $rs->bindParam(":carac_instrumento", $carac_instrumento, PDO::PARAM_STR);  
       $rs->bindParam(":capacidade_instrumento", $capacidade_instrumento, PDO::PARAM_STR);  
       $rs->bindParam(":data_cadastro", $data_cadastro, PDO::PARAM_STR);  
       $rs->bindParam(":status_instrumento", $status_instrumento, PDO::PARAM_STR);      
       $rs->execute();
       $ultimo_id = $conexao->lastInsertId();
   
       }      
           
          
   
       } catch (PDOException $erro) {
           $retorno = array('codigo' => 0, 'mensagem' => "Erro: " . $erro->getMessage());
          // echo "Erro: " . $erro->getMessage();
          echo json_encode($retorno);
          exit;
       }
   
    
   
       } // finaliza tipo de ponto instrumento 


       // prepara a inclusão se o tipo de ponto for facilite
   
   if($tipo_ponto=="5" ){// Tipo 5 = Indice Primário do Tipo de Ponto = FACILITE //
   
    $area_facilite=(isset($_POST["area_facilite"])) ? $_POST["area_facilite"] : "";
    $id_tipo_facilite=(isset($_POST["id_tipo_facilite"])) ? $_POST["id_tipo_facilite"] : "";
    $local_facilite=(isset($_POST["local_facilite"])) ? $_POST["local_facilite"] : "";
    
    $status_facilite = (isset($_POST["status_facilite"])) ? $_POST["status_facilite"] : ""; // Ou o Tanque esta ativo ou com Problemas, Inativo só excluido para atualização por outro tipo de Ponto que não seja Facilite



    // Faz as validações caso seja tipo de ponto = Tanque de Tratamento
            $mensagem="";
         if ($id_tipo_facilite =="") {
       
        $mensagem .="<p><i class='flaticon-warning-sign'></i> Para cadastrar uma Facilite é necessário Selecionar o <b>Tipo de Facilite</b>.</p>";
        
        }  

        if ($status_facilite =="") {
       
            $mensagem .="<p><i class='flaticon-warning-sign'></i> Necessário informar o <b>Status da Facilite</b>.</p>";
            
            }  

        if ($local_facilite =="") {
       
            $mensagem .="<p><i class='flaticon-warning-sign'></i> Para cadastrar uma Facilite é necessário informar o <b>Local da Facilite</b>.</p>";
            
            }  

        if ($mensagem != ''):
            $mensagem = "$mensagem";
           
            $retorno = array('codigo' => 0, 'mensagem' => $mensagem);
                       
            echo json_encode($retorno);

            exit;      
        endif;
       
//====================[ Finaliza as Validações do facilite]=======================



try {




    $consulta_existente=$conexao->query("SELECT id_ponto,id_facilite_ponto FROM facilite_ponto WHERE id_ponto='$id_plcode_atual'");

    $registro = $consulta_existente->fetch(PDO::FETCH_OBJ);
    $count = $consulta_existente->rowCount();
     //contagem de registros
    
     if($count > 0 OR $registro==true){
    
        $id_consulta = $registro->id_facilite_ponto;


         // se o equipamento já existir para este ponto, atualizo os dados do facilite
    $rs = $conexao->prepare("UPDATE facilite_ponto SET  id_tipo_facilite=:id_tipo_facilite, local_facilite=:local_facilite, area_facilite	=:area_facilite	, status_facilite=:status_facilite WHERE id_facilite_ponto= $id_consulta");
    $rs->bindParam(":id_tipo_facilite", $id_tipo_facilite, PDO::PARAM_STR);  
    $rs->bindParam(":local_facilite", $local_facilite, PDO::PARAM_STR);  
    $rs->bindParam(":area_facilite", $area_facilite, PDO::PARAM_STR);  
    $rs->bindParam(":status_facilite", $status_facilite, PDO::PARAM_STR);  
      
    $rs->execute();
          
    //var_dump($rs);
    $ultimo_id = $id_consulta;



    } else { // caso o equipamento não exista, gravo o mesmo.


    $rs = $conexao->prepare("INSERT INTO facilite_ponto (id_ponto, id_tipo_facilite, local_facilite, area_facilite, status_facilite, data_cadastro_facilite) VALUES(:id_ponto, :id_tipo_facilite, :local_facilite, :area_facilite, :status_facilite, :data_cadastro_facilite)");  
    $rs->bindParam(":id_ponto", $id_plcode_atual, PDO::PARAM_STR);  
    $rs->bindParam(":id_tipo_facilite", $id_tipo_facilite, PDO::PARAM_STR);  
    $rs->bindParam(":local_facilite", $local_facilite, PDO::PARAM_STR);  
    $rs->bindParam(":area_facilite", $area_facilite, PDO::PARAM_STR);  
    $rs->bindParam(":status_facilite", $status_facilite, PDO::PARAM_STR);  
    $rs->bindParam(":data_cadastro_facilite", $data_cadastro, PDO::PARAM_STR);  
    $rs->execute();
    $ultimo_id = $conexao->lastInsertId();

    }      
        
        


    } catch (PDOException $erro) {
        $retorno = array('codigo' => 0, 'mensagem' => "Erro: " . $erro->getMessage());
       // echo "Erro: " . $erro->getMessage();
       echo json_encode($retorno);
       exit;
    }

 

    } // finaliza tipo de ponto facilite 

      



              $retorno = array('codigo' => 11, 'mensagem' => "As Definições Avançadas do PLCode: '{$nome_plcode}',</br> foram atualizadas com sucesso!", 'id_novo_plcode' => $id_plcode_atual);
    
              echo json_encode($retorno);

            } else {
                $retorno = array('codigo' => 10, 'mensagem' => 'Erro ao tentar Alterar o PLCode!');

                echo json_encode($retorno);

            }


    } catch (PDOException $erro) {
        echo "Erro: " . $erro->getMessage();
    }


    $conexao=null;

}



//=====================[ Tanque ]========================= 


if($acao == "cadastrar-tipo_tanque"){

    $data_cadastro = date_create()->format('Y-m-d H:i:s');
    
    $status_cadastro=(isset($_POST["status_cadastro"])) ? $_POST["status_cadastro"] : "";

    $nome_tipo_tanque = (isset($_POST['nome_tipo_tanque'])) ? $_POST['nome_tipo_tanque'] : '';
    $descricao_longa = (isset($_POST['descricao_longa'])) ? $_POST['descricao_longa'] : '';

    try {
        $stmt = $conexao->prepare("INSERT INTO tipo_tanque ( 
                nome_tipo_tanque, 
                descricao_longa,
                data_cadastro,
                status_cadastro
                       ) 
    VALUES (?, ?,?,?)");
       
        $stmt->bindParam(1, $nome_tipo_tanque);
       
        $stmt->bindParam(2, $descricao_longa);

        $stmt->bindParam(3, $data_cadastro);

        $stmt->bindParam(4, $status_cadastro);
       
        $stmt->execute();

        $ultimo_id = $conexao->lastInsertId();

       

        if ($stmt) {

            $retorno = array('codigo' => 1, 'mensagem' => 'Tipo de Tanque -  Incluído com sucesso!','id_retorno'=>$ultimo_id);


            echo json_encode($retorno);

        } else {
            $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao tentar efetivar cadastro!');

            echo json_encode($retorno);

        }

    } catch (PDOException $erro) {
        echo "Erro: " . $erro->getMessage();
    }

    

} // finaliza tipo tanque


//===================[ Equipamento ]============================== 


if($acao == "cadastrar-tipo_equipamento"){

    $data_cadastro = date_create()->format('Y-m-d H:i:s');
    
    $status_cadastro=(isset($_POST["status_cadastro"])) ? $_POST["status_cadastro"] : "";

    $nome_tipo_equipamento = (isset($_POST['nome_tipo_equipamento'])) ? $_POST['nome_tipo_equipamento'] : '';
    $descricao_longa = (isset($_POST['descricao_longa'])) ? $_POST['descricao_longa'] : '';

    try {
        $stmt = $conexao->prepare("INSERT INTO tipo_equipamento ( 
                nome_tipo_equipamento, 
                descricao_longa,
                data_cadastro,
                status_cadastro
                       ) 
    VALUES (?, ?,?,?)");
       
        $stmt->bindParam(1, $nome_tipo_equipamento);
       
        $stmt->bindParam(2, $descricao_longa);

        $stmt->bindParam(3, $data_cadastro);

        $stmt->bindParam(4, $status_cadastro);
       
        $stmt->execute();

        $ultimo_id = $conexao->lastInsertId();

       

        if ($stmt) {

            $retorno = array('codigo' => 1, 'mensagem' => 'Tipo de Equipamento </br>  In com Sucesso!','id_retorno'=>$ultimo_id);


            echo json_encode($retorno);

        } else {
            $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao tentar efetivar cadastro!');

            echo json_encode($retorno);

        }

    } catch (PDOException $erro) {
        echo "Erro: " . $erro->getMessage();
    }

    

} // finaliza tipo Equipamento


//===================[ Equipamento ]============================== 


if($acao == "cadastrar-tipo_instrumento"){

    $data_cadastro = date_create()->format('Y-m-d H:i:s');
    
    $status_cadastro=(isset($_POST["status_cadastro"])) ? $_POST["status_cadastro"] : "";

    $nome_tipo_instrumento = (isset($_POST['nome_tipo_instrumento'])) ? $_POST['nome_tipo_instrumento'] : '';
    $descricao_longa = (isset($_POST['descricao_longa'])) ? $_POST['descricao_longa'] : '';

    try {
        $stmt = $conexao->prepare("INSERT INTO tipo_instrumento ( 
                nome_tipo_instrumento, 
                descricao_longa,
                data_cadastro,
                status_cadastro
                       ) 
    VALUES (?, ?,?,?)");
       
        $stmt->bindParam(1, $nome_tipo_instrumento);
       
        $stmt->bindParam(2, $descricao_longa);

        $stmt->bindParam(3, $data_cadastro);

        $stmt->bindParam(4, $status_cadastro);
       
        $stmt->execute();

        $ultimo_id = $conexao->lastInsertId();

       

        if ($stmt) {

            $retorno = array('codigo' => 1, 'mensagem' => 'Tipo de Instrumento -  Incluído com Sucesso!','id_retorno'=>$ultimo_id);


            echo json_encode($retorno);

        } else {
            $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao tentar efetivar cadastro!');

            echo json_encode($retorno);

        }

    } catch (PDOException $erro) {
        echo "Erro: " . $erro->getMessage();
    }

    

} // finaliza tipo Equipamento

//===============[ Dados Operacionais do Ponto ]========================================= 



if ($acao== "apaga_plcode") {


    if($_COOKIE['nivel_acesso_usuario']!='admin'){

        $retorno = array('codigo' => 0, 'mensagem' => "<span class='text-danger f4'>Ação não Permitida!</span><p>Esta operação é permitida somente para administradores do sistema.</p>");
        echo json_encode($retorno);
        $conexao = null;
        exit;
    
    }

    $sql = $conexao->query("SELECT id_ponto FROM rmm WHERE id_ponto = $id_plcode_atual");
    $data = $sql->fetch(PDO::FETCH_OBJ);
    
    $conta = $sql->rowCount();
    
    //======

    $sql_check = $conexao->query("SELECT id_ponto FROM checkin WHERE id_ponto = $id_plcode_atual");
    $data_check = $sql_check->fetch(PDO::FETCH_OBJ);
    
    $conta_check = $sql_check->rowCount();

    //======

    $sql_par = $conexao->query("SELECT id_ponto FROM parametros_ponto WHERE id_ponto = $id_plcode_atual");
    $data_par = $sql_par->fetch(PDO::FETCH_OBJ);
    
    $conta_par = $sql_par->rowCount();
    //======
    $add_msg  ="";
    if($conta > 0){

        if($conta_check>0){

            $add_msg  .="<br/><span class='kt-badge kt-badge--danger kt-badge--dot'></span> Localizado Checkin`s para este PLCode.";
        }

        if($conta_par>0){

            $add_msg  .="<br/><span class='kt-badge kt-badge--danger kt-badge--dot'></span> Localizado Parâmetros / Indicadores para este PLCode.";
        }
    
        $retorno = array('codigo' => 0,  'mensagem' => '<span class="kt-badge kt-badge--danger kt-badge--dot"></span> Localizado Leituras atribuídas à este PLCode. '.$add_msg.'<br/>');
    
        echo json_encode($retorno);
        exit;
    
    } else {
    
        try {
            $stmt = $conexao->prepare("DELETE FROM pontos_estacao WHERE id_ponto = ?");
            $stmt->bindParam(1, $id_plcode_atual, PDO::PARAM_INT);
            if ($stmt->execute()) {
               // echo "Registo foi excluído com êxito";
      
      
               $retorno = array('codigo' => 1,  'mensagem' => 'PLCode, eliminado com Sucesso!');
                
                
                echo json_encode($retorno);
               // $id = ?;
            } else {
                throw new PDOException("Erro: Não foi possível executar a declaração sql");
            }
        } catch (PDOException $erro) {
            echo "Erro: ".$erro->getMessage();
        }
      }
      
      $conexao= null;
    }
      
      

if($acao=='novo_plcode_finaliza'){


    unset($_COOKIE['plcode_atual']);
    $cookie_name = 'plcode_atual';
$cookie_path = '/'; // Define o caminho do cookie
$cookie_domain = ''; // Insira seu domínio aqui, se necessário
// Verifica se está no ambiente de desenvolvimento local
$is_localhost = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

// Ajusta as configurações do cookie com base no ambiente
$cookie_secure = $is_localhost ? false : true; // Desabilita o atributo "secure" no ambiente local
$cookie_httponly = true; // Define o atributo "httponly"


setcookie($cookie_name, '', [
    'expires' => time() - 3600, // Define a data de expiração para 1 hora no passado
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // Adiciona o atributo 'SameSite' para maior segurança
]);



    $retorno = array('codigo' => 33,  'mensagem' => 'Cadastro do PLCode, Finalizado com Sucesso!');
                
                
    echo json_encode($retorno);

}