<?php
 // buffer de saída de dados do php]
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

 // buffer de saída de dados do php]
header("content-type: application/json");




//===[ CHAVE ÚNICA da SESSAO] a cada sessao, o step registra uma codificação única.
// Obtenha a data atual no formato dd-mm-yyyy
$data_chave = date("d-m-Y");

// Obtenha a hora atual no formato hh:mm
$hora_chave = date("H:i");

$pagina_ativa_chave = $_SESSION['pagina_atual'] ?? 'gerado_automatico';

// Acrescenta o hífen na concatenação
$usuario_sessao_chave = $_SESSION['nome'] . '-' . ($_SESSION['pagina_atual'] ?? 'gerado_automatico');

$id_usuario_sessao_chave = $_SESSION['id'];

// Crie a chave única
$chave_unica = $_COOKIE['CHAVE_UNICA_SESSAO_ATUAL'] ?? md5($data_chave . $hora_chave . $pagina_ativa_chave . $usuario_sessao_chave . $id_usuario_sessao_chave);
/*===[ CHAVE ÚNICA da SESSAO]==== */

$acao = (isset($_POST['acao'])) ? $_POST['acao'] : '';

$projeto_atual = $_COOKIE['projeto_atual'] ??'';


if (isset($_COOKIE['nivel_acesso_usuario'])) {
    $nivelAcesso = $_COOKIE['nivel_acesso_usuario'];
  
} else {
    $retorno = array('codigo' => 0, 'mensagem' => 'Cookie do seu Nível de acesso, não foi encontrado.');
    echo json_encode($retorno);
    exit();

}



$id_usuario_responsavel = (isset($_POST['id_usuario_responsavel'])) ? $_POST['id_usuario_responsavel'] : '';

$id_projeto_busca_usuario =  (isset($_GET['id_projeto_busca_usuario'])) ? $_GET['id_projeto_busca_usuario'] : '';

$nome_projeto = (isset($_POST['nome_projeto'])) ? $_POST['nome_projeto'] : '';
$cliente_projeto = (isset($_POST['cliente_projeto'])) ? $_POST['cliente_projeto'] : '';
$periodo_contrato = (isset($_POST['periodo_contrato'])) ? $_POST['periodo_contrato'] : '';
$status_projeto = $_POST['status_projeto'] ?? '3';
$foto = (isset($_POST['foto'])) ? $_POST['foto'] : '';
$obs_projeto = (isset($_POST['obs_projeto'])) ? $_POST['obs_projeto'] : '';
$codigo_obra = (isset($_POST['codigo_obra'])) ? $_POST['codigo_obra'] : '';


// dados para novo cliente

$razao_social = $_POST['razao_social'] ?? '';
$nome_fantasia= $_POST['nome_fantasia'] ?? '';
$cnpj= $_POST['cnpj'] ?? '';
$telefone= $_POST['telefone'] ?? '';
$site_cliente= $_POST['site_cliente'] ?? '';
$email_geral= $_POST['email'] ?? '';
$email_nfe= $_POST['email_nfe'] ?? '';
$gestao_step= $_POST['gestao_step'] ?? '';
//==========




if($periodo_contrato!=''){

    $array = explode('-', $periodo_contrato);

    //print_r($array);

    $periodo_inicialx =  trim($array[0]);
    $periodo_finalx =  trim($array[1]);


   $periodo_inicial =  date("Y-m-d",strtotime(str_replace('/','-',$periodo_inicialx)));

   $periodo_final =  date("Y-m-d",strtotime(str_replace('/','-',$periodo_finalx)));

}
if($acao=="cadastrar"){



    if($cliente_projeto!=''){

                        $carimbo = $conexao->prepare("INSERT INTO obras (
                        codigo_obra,
                        nome_obra,
                        id_cliente,
                        periodo_inicial,
                        periodo_final,
                        status_cadastro,
                        foto,
                        obs_interna) VALUES (?,?,?,?,?,?,?,?)");

                        $carimbo->bindValue(1, $codigo_obra, PDO::PARAM_STR);
                        $carimbo->bindValue(2, $nome_projeto, PDO::PARAM_STR);
                        $carimbo->bindValue(3, $cliente_projeto, PDO::PARAM_STR);
                        $carimbo->bindValue(4, $periodo_inicial, PDO::PARAM_STR);
                        $carimbo->bindValue(5, $periodo_final, PDO::PARAM_STR);
                        $carimbo->bindValue(6, $status_projeto, PDO::PARAM_STR);
                        $carimbo->bindValue(7, $foto, PDO::PARAM_STR);
                        $carimbo->bindValue(8, $obs_projeto, PDO::PARAM_STR);

                        $carimbo->execute();

                        $ultimo_id = $conexao->lastInsertId();


                    if($carimbo){

                                                        
                                // gravo quem criou o projeto em usuarios_projetos como responsável.
                                try {
                                    $status_responsavel=1;
                                    $sql = $conexao->prepare("INSERT INTO usuarios_projeto (id_obra, id_usuario, nivel, responsavel) VALUES (:id_obra, :id_usuario, :nivel, :responsavel)");

                                    // Supondo que $id_obra, $id_usuario e $nivel são as variáveis que contêm os valores a serem inseridos
                                    $sql->bindParam(':id_obra', $ultimo_id, PDO::PARAM_INT);
                                    $sql->bindParam(':id_usuario', $id_usuario_responsavel, PDO::PARAM_INT);
                                    $sql->bindParam(':nivel', $nivelAcesso, PDO::PARAM_INT); // Supondo que 'nivel' é um inteiro
                                    $sql->bindParam(':responsavel', $status_responsavel, PDO::PARAM_INT);

                                    $sql->execute();

                                  error_log("Usuário Responsável pelo  Projeto, foi incluido em Usuarios do Projeto, inserido com sucesso: " . $conexao->lastInsertId()) ;

                                    

                                    
                                } catch (PDOException $e) {
                                    error_log("Erro ao inserir registro: " . $e->getMessage()) ;
                                }
                                // gravo quem criou o projeto em usuarios_projetos como responsável.
                       
                        $retorno = array('codigo' => 1,  'mensagem' => 'Projeto  cadastrado com Sucesso!', 'id'=>$ultimo_id);


                        echo json_encode($retorno);

                        exit;
                    } else {
                        $retorno = array('codigo' => 0,  'mensagem' => 'Falha ao Gravar o Projeto!');


                        echo json_encode($retorno);

                        exit;

                    }

    }else {


        // cadastra o cliente primeiro:

           // Verifica se já existe o mesmo email em uso no sistema
if(isset($cnpj)):
    $consulta_cnpj = $conexao->query("SELECT count(id_cliente) FROM clientes where cnpj='$cnpj'")->fetchColumn();
    $mensagem = '';   

    if ($consulta_cnpj > 0) {
    //usuário está registrado
    //faça o insert aquis
    $mensagem='<h3> Integridade dos Dados </h3>';
    $mensagem .="<p>O CNPJ: <b>".$cnpj." </b>, Já está em uso no Sistema!</p>";

        if ($mensagem != ''):
            $mensagem = "$mensagem";
        
            $retorno = array('codigo' => 0, 'mensagem' => $mensagem);
                    
            echo json_encode($retorno);
        endif;
    exit;

    }
endif;


    try {
        $stmt = $conexao->prepare("INSERT INTO clientes ( 
            nome_fantasia,
            razao_social,
            cnpj,
            site_cliente,
            email_nfe,
            email_geral,
            gestao_step,
            status_cadastro,
            telefone
              ) 
        VALUES (?, ?,?, ?, ?, ?, ?, ?, ?)");
        $stmt->bindParam(1, $nome_fantasia);
        $stmt->bindParam(2, $razao_social);
        $stmt->bindParam(3, $cnpj);
        $stmt->bindParam(4, $site_cliente);
        $stmt->bindParam(5, $email_nfe);
        $stmt->bindParam(6, $email_geral);
        $stmt->bindParam(7, $gestao_step);
        $stmt->bindParam(8, $status_cadastro);
        $stmt->bindParam(9, $telefone);

      
         
         
        if ($stmt->execute()) {
            

            if ($stmt->rowCount() > 0) {

                $id_cliente = $conexao->lastInsertId();


                
                // cliente já incluido, realizo a inclusão da Obra (Projeto)
              
                $carimbo = $conexao->prepare("codigo_obra,
                nome_obra,
                id_cliente,
                periodo_inicial,
                periodo_final,
                status_cadastro,
                foto,
                obs_interna) VALUES (?,?,?,?,?,?,?,?)");

                $carimbo->bindValue(1, $codigo_obra, PDO::PARAM_STR);
                $carimbo->bindValue(2, $nome_projeto, PDO::PARAM_STR);
                $carimbo->bindValue(3, $cliente_projeto, PDO::PARAM_STR);
                $carimbo->bindValue(4, $periodo_inicial, PDO::PARAM_STR);
                $carimbo->bindValue(5, $periodo_final, PDO::PARAM_STR);
                $carimbo->bindValue(6, $status_projeto, PDO::PARAM_STR);
                $carimbo->bindValue(7, $foto, PDO::PARAM_STR);
                $carimbo->bindValue(8, $obs_projeto, PDO::PARAM_STR);

                    $carimbo->execute();

                    $ultimo_id = $conexao->lastInsertId();


                if($carimbo){

                    
                    $retorno = array('codigo' => 1,  'mensagem' => 'Projeto e Novo Cliente, cadastrados com Sucesso!', 'id'=>$ultimo_id);


                    echo json_encode($retorno);

                    exit;
                } else {
                    $retorno = array('codigo' => 0,  'mensagem' => 'Falha ao Gravar o Projeto!');


                    echo json_encode($retorno);

                    exit;

                }
                
            } 
            else 
            { // caso não consiga incluir o cliente, não irá incluir o projeto e retorna o erro de cadastro:

                $retorno = array('codigo' => 0, 'mensagem' => 'Não foi possível incluir o novo Cliente, por isso o cadastro do Proejto não foi concluído, tente novamente, caso o erro persista, informe a tela do erro ao Suporte!');
               
                echo json_encode($retorno);
              
            }

        } else {
               throw new PDOException("Erro: Não foi possível executar a declaração sql");
        }
    } catch (PDOException $erro) {
        echo "Erro: " . $erro->getMessage();
    }




    }




}






if($acao=="alterar"){

    


    $id_projeto= $_POST['id'] ?? '';
    


    $data = [
        'codigo_obra' => $codigo_obra,
        'nome_obra' => $nome_projeto,
        'id_cliente' => $cliente_projeto,
        'periodo_inicial' => $periodo_inicial,
        'periodo_final' => $periodo_final,
        'status_cadastro' => $status_projeto,
        'foto' => $foto,
        'obs_interna' => $obs_projeto ,
        'id' => $id_projeto,
    ];

    $sql = "UPDATE obras SET
    codigo_obra =:codigo_obra, 
    nome_obra= :nome_obra, 
    id_cliente= :id_cliente, 
    periodo_inicial= :periodo_inicial,
    periodo_final= :periodo_final,
    status_cadastro = :status_cadastro,
    foto = :foto,
    obs_interna = :obs_interna

    WHERE id_obra =:id";

    $conexao->prepare($sql)->execute($data);



    if($sql){

        $retorno = array('codigo' => 1,  'mensagem' => 'Projeto  alterado com Sucesso!');


    echo json_encode($retorno);

    exit;
    } else {


        $retorno = array('codigo' => 0,  'mensagem' => 'Falha ao Alterar o Projeto!');


        echo json_encode($retorno);
    
        exit;
    
    }




}



if($acao=='usuarios_projeto'){
        
        
    $id_projeto= $_POST['id_projeto'] ?? '';

    $users=$_POST['users'];


    if($_POST['users']=='' && $id_projeto!=''){

        $retorno = array('codigo' => '0', 'retorno' => "Não é possível prosseguir, verifique as informações.");

        echo json_encode($retorno);

        $conexao = null;
        exit;
    }
    
    $nivel = $_POST['nivel_usuario_projeto'];

   
    
    $retorno='';
    
 
        foreach(array_combine($_POST['users'] ,$_POST['nivel_usuario_projeto']) as $key=> $value)
       {



        $sql_atualiza = $conexao->query("SELECT id_obra FROM usuarios_projeto WHERE id_obra='$id_projeto' AND id_usuario='$key' ");

        if($sql_atualiza){

            $sql_executa = $conexao->query("DELETE FROM usuarios_projeto WHERE id_obra='$id_projeto' AND id_usuario='$key' ");

        }
               
                $sql = $conexao->query(" INSERT INTO  usuarios_projeto 
                    
                                                            (id_obra,id_usuario, nivel) 

                                                            VALUES 

                                                            ('$id_projeto','$key','$value')")

                    or die(print_r($conexao->errorInfo(), true));
                    
                    
                     $ultimo_id = $conexao->lastInsertId();


                if ($sql) {


                  //  $sql = $conexao->query("UPDATE usuarios SET nivel='$value' WHERE id='$key'");


                    
//=====================[] envia email ]===========

$sql = $conexao->query("SELECT u.nome, u.email, up.nivel, o.nome_obra, o.id_obra FROM usuarios u
LEFT JOIN usuarios_projeto up ON up.id_usuario = u.id 
INNER JOIN obras o ON o.id_obra = up.id_obra WHERE up.id_usuario = $key AND up.id_obra='$id_projeto' ");
$r_email = $sql->fetch(PDO::FETCH_OBJ);

        $chave_unica = $chave_unica;        
        $id_usuario=$ultimo_id;
        $email_usuario =$r_email->email; // destinatário padrão
        $nome_usuario = $r_email->nome;
        $nome_projeto = $r_email->nome_obra;
        $nivel_acesso = $r_email->nivel;

        $mensagem_email = "Você foi incluído no Projeto: <strong>".$nome_projeto."</strong>.<br>Com o nível de acesso: <strong>".$nivel_acesso."</strong>";
        $assunto = 'Inclusão em Novo Projeto';
        $template_email = '/views/emails/email-inclusao-projeto.php';
        require_once  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';



       
     
      
//=====================[] envia email ]===========
                    
                    
                     $retorno = array('codigo' => '1', 'mensagem' => "Usuários incluídos com sucesso ao Projeto.");

     
//ação do log(Acesso, Exclusão, Atualização, Inserção)
$log_acao = "Novo Usuário em Projeto";
//Rotina acessada
            $log_rotina = "Módulo: Projetos -> SubMódulo: Usuários do Projeto: ".$nome_projeto." [ ID: ".$id_projeto." ] ";
 //historico, O que alterou, excluiu, ou acessou...
            $log_nivel_usuario = $_SESSION['nivel'];
//var_dump($row);         
            $log_historico = "O Usuário:" . $_SESSION['nome'] . ", Fez a inclusão do  usuário " . $nome_usuario . " [ ".$email_usuario." ] , no Projeto: ".$nome_projeto." [ ID: ".$id_projeto." ] , com o nível de acesso: ".$nivel_acesso.".";
// Nome do Usuário Registrado na Sessão
            $log_usuario = $_SESSION['id'];
// IP do Usuário
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/log/log_sistema.php';
        
//==================[ Finaliza Log Sistema ]==================//       

         
                // incluo no foreach o laco para inclusao de todos os indicadores relacionados na tabela cockpit_lista_indicadores:
                }else {

                      
         $retorno = array('codigo' => '0', 'mensagem' => "Não foi possível realizar a operação no SGBD.");

       

      
                }
            } // fecha o foreach
   
    
            echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
            $conexao = null;
            exit;
    
}


if ($acao == "desativa_projeto" ) {
  

if($_COOKIE['nivel_acesso_usuario']!='admin'){

    $retorno = array('codigo' => 0, 'mensagem' => "<span class='text-danger f4'>Ação não Permitida!</span><p>Esta operação é permitida somente para administradores do sistema.</p>");
    echo json_encode($retorno);
    $conexao = null;
    exit;

}



    $mensagem ='';

    $id_projeto= $_POST['id'] ?? '';


    $verifica_leitura = $conexao->prepare("SELECT r.id_ponto FROM rmm r 
    INNER JOIN pontos_estacao p ON p.id_ponto = r.id_ponto WHERE p.id_obra = '$id_projeto' LIMIT 1");
$verifica_leitura->execute();

$conta = $verifica_leitura->rowCount();

if ($conta > 0) {

    $mensagem .= "<p class='d-flex py-2'>O Projeto não pode ser excluído por haver leituras realizadas para este Projeto.</p> No entanto foi possível inativá-lo para manter íntegro seu histórico de Dados no sistema.";
    //echo json_encode($retorno);
   

} else {


      //======

      $sql_check = $conexao->query("SELECT pr.id_obra FROM periodo_ponto
      pr INNER JOIN checkin ch ON ch.id_estacao = pr.id_estacao WHERE pr.id_obra = '$id_projeto'");
      $data_check = $sql_check->fetch(PDO::FETCH_OBJ);
      
      $conta_check = $sql_check->rowCount();

      if ($conta_check > 0) {

        $mensagem .= "<p class='d-flex py-2'>O Projeto não pode ser excluído por haver Tarefas Realizadas, vinculadas à este Projeto.</p> No entanto foi possível inativá-lo para manter íntegro seu histórico de Dados no sistema.";
        //echo json_encode($retorno);


      } else {
  
      //======


        $apaga_obra = $conexao->query("UPDATE obras  SET status_cadastro='3' WHERE id_obra = '$id_projeto'");

        if($apaga_obra){

            $mensagem .= "<p class='d-flex py-2'>O Projeto foi Inativado com sucesso.</p>";

            $apaga_estacao = $conexao->query("UPDATE  estacoes SET status_estacao='3' WHERE id_obra = '$id_projeto'");

           

            if($apaga_estacao){

                $mensagem .= "<p class='d-flex py-2'>Os Núcleos pertencentes ao Projeto, também foram inativados.</p>";

                $apaga_indicadores = $conexao->query("UPDATE parametros_ponto
                SET status_parametro ='3'
                INNER JOIN pontos_estacao ON pontos_estacao.id_ponto = parametros_ponto.id_ponto
                WHERE pontos_estacao.id_obra = '$id_projeto'");

            if($apaga_indicadores){

            $mensagem .= "<p class='d-flex py-2'>Os Indicadores pertencentes ao Projeto, também foram inativados.</p>";

            $apaga_plcode = $conexao->query("UPDATE pontos_estacao SET status_ponto='3' WHERE id_obra = '$id_projeto'");

            if($apaga_plcode){

                $mensagem .= "<p class='d-flex py-2'>Os PLCodes pertencentes ao Projeto, também foram inativados.</p>";
                    }

             }

            }
        

        }
    }


}

   

    try {
        // supondo que $id já foi definida anteriormente com um valor seguro
        $stmt = $conexao->prepare("UPDATE obras SET status_cadastro = '3' WHERE id_obra = :id_obra");
        $stmt->bindParam(':id_obra', $id_projeto);
           
        if ($stmt->execute()) {
 //=====================[] envia email ]===========

$sql = $conexao->query("SELECT o.nome_obra, u.email, u.nome, u.nivel, u.id, o.id_obra FROM usuarios_projeto up 
INNER JOIN obras o ON o.id_obra = up.id_obra
INNER JOIN estacoes e ON e.id_obra = o.id_obra
INNER JOIN usuarios u ON u.id = up.id_usuario WHERE up.id_obra='$id_projeto' ");

$conta = $sql->rowCount();

if($conta > 0 ){

    $rUsuarios = $sql->fetchALL(PDO::FETCH_ASSOC);

    
        foreach( $rUsuarios as $r){


            
$chave_unica = $chave_unica;        
$id_usuario=$r['id'];
$email_usuario =$r['email']; // destinatário padrão
$nome_usuario = $r['nome'];
$nivel_acesso = $r['nivel'];
$assunto = 'Conta Inativada';
$mensagem_email = "Olá $nome_usuario, você está sendo comunicado que o Projeto: <b> $r[id_obra] </b> - $r[nome_obra] <br> 
Foi Inativado no sistema, através do usuário: <b>$_SESSION[nome]</b> com e-mail de acesso: <b>$_SESSION[email]</b>, na data de hoje. <br>
Caso desconheça esta ação, entre em contato com a Gestão do STEP, através dos Canais de Suporte. <br><br><br>
<small>Lembrando que todos os dados permanecem íntegros e disponíveis para auditoria e consulta de log.</small>";
$template_email = '/views/emails/email-padrao.php';

require_once  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';


//=====================[] envia email ]===========



        }

        $mensagem .= "<p>A Operação foi concluída com sucesso!</p> Os usuários pertencentes ao Projeto, foram comunicados por e-mail.";    
        
//==================[ LOG do Sistema ] =======================//


//ação do log(Acesso, Exclusão, Atualização, Inserção)
$log_acao = "Detativar Projeto";
//Rotina acessada
            $log_rotina = "Módulo: Projetos -> SubMódulo: Projeto ".$r['nome_obra']." [ ID: ".$r['id_obra']." ] ";
 //historico, O que alterou, excluiu, ou acessou...
            $log_nivel_usuario = $_SESSION['nivel'];
//var_dump($row);         
            $log_historico = "O Usuário:" . $_SESSION['nome'] . ", Detavidou o Projeto: " . $r['nome_obra'] . " [ ID: ".$r['id_obra']." ] ";
// Nome do Usuário Registrado na Sessão
            $log_usuario = $_SESSION['id'];
// IP do Usuário
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/log/log_sistema.php';
        
//==================[ Finaliza Log Sistema ]==================//

}else {

    $mensagem .="<p>A Operação foi concluída com sucesso! </p> Porém não há membros neste Projeto, não houve e-mail de comunicação enviado.";
 
}



$retorno = array('codigo' => 1, 'mensagem' => $mensagem);

    

  
    echo json_encode($retorno);
  
    $conexao = null;

    exit;

        }
    } catch(PDOException $e) {
        $retorno = array('codigo' => 0, 'mensagem' => "Erro ao executar a consulta: " . $e->getMessage());
        echo json_encode($retorno);
        $conexao = null;
        exit;
    }

    $conexao = null;

    exit;
}




if($acao == "retira_usuario_projeto"){

    $id_usuario_Projeto = $_POST['id_usuario_Projeto'] ?? '';
    $id_Projeto_usuario = $_POST['id_Projeto_usuario'] ?? '';
    
    if($id_usuario_Projeto == '' || $id_Projeto_usuario == ''){
    
        $retorno = array('codigo' => 0, 'mensagem' => "Dados Enviados não foram reconhecidos");
        echo json_encode($retorno);
        $conexao = null;
        exit;
    
    }
    
    $sql = "DELETE usuarios_projeto FROM usuarios_projeto WHERE id_usuario = '$id_usuario_Projeto' AND id_obra = '$id_Projeto_usuario'";
    $apaga = $conexao->query($sql);
    
    if($apaga){

        $retorno = array('codigo' => 1, 'mensagem' => "Usuário foi removido com sucesso deste Projeto!");
        echo json_encode($retorno);
      

//=====================[] envia email ]===========

$sql = $conexao->query("SELECT o.nome_obra, u.email, u.nome, u.nivel, u.id, o.id_obra FROM usuarios_projeto up 
INNER JOIN obras o ON o.id_obra = up.id_obra
INNER JOIN estacoes e ON e.id_obra = o.id_obra
INNER JOIN usuarios u ON u.id = up.id_usuario WHERE up.id_obra='$id_Projeto_usuario' ");

$conta = $sql->rowCount();

if($conta > 0 ){

    $rUsuarios = $sql->fetchALL(PDO::FETCH_ASSOC);

    
        foreach( $rUsuarios as $r){


            
$chave_unica = $chave_unica;        

$id_usuario=$r['id'];
$email_usuario =$r['email']; // destinatário padrão
$nome_usuario = $r['nome'];
$nivel_acesso = $r['nivel'];
$assunto = 'Permissão Revogada em Projeto';
$mensagem_email = "Olá $nome_usuario, você está sendo comunicado que o Projeto: <b> $r[id_obra] </b> - $r[nome_obra] <br> 
foi retirada sua permissão.<br>Através do usuário: <b>$_SESSION[nome]</b> com e-mail de acesso: <b>$_SESSION[email]</b>, na data de hoje. <br>
Caso desconheça esta ação, entre em contato com a Gestão do STEP, através dos Canais de Suporte. <br><br><br>
<small>Lembrando que todos os dados permanecem íntegros e disponíveis para auditoria e consulta de log.</small>";
$template_email = '/views/emails/email-padrao.php';

require_once  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';


//=====================[] envia email ]===========
    
    }

    
   
    
    
    }}else{
    
        $retorno = array('codigo' => 0, 'mensagem' => "Erro ao remover usuário deste Projeto!");
        echo json_encode($retorno);
        $conexao = null;
        exit;
    
    }}




    if($acao == "troca_responsavel_projeto"){

        $id_usuario_Projeto = $_POST['id_usuario_Projeto'] ?? '';
        $id_Projeto_usuario = $_POST['id_Projeto_usuario'] ?? '';
        $nivel_user_projeto = $_POST['nivel_user_projeto'] ?? '';
        
        if($id_usuario_Projeto == '' || $id_Projeto_usuario == '' || $nivel_user_projeto == '' || $nivel_user_projeto==NULL || $id_Projeto_usuario==NULL ){
        
            $retorno = array('codigo' => 0, 'mensagem' => "Dados Enviados não foram reconhecidos");
            echo json_encode($retorno);
            $conexao = null;
            exit;
        
        }
        if($nivel_user_projeto=='cliente'){

            $retorno = array('codigo' => 0, 'mensagem' => "Acesso de nível Cliente, não é permitido torná-lo responsável pelo Projeto!");
            echo json_encode($retorno);
            $conexao = null;
            exit;


        }
        
        $sql = "UPDATE usuarios_projeto SET responsavel='1' WHERE id_usuario = '$id_usuario_Projeto' AND id_obra = '$id_Projeto_usuario' AND nivel='$nivel_user_projeto'";
        $atualiza = $conexao->query($sql);
        
        if($atualiza){

$sql =("UPDATE estacoes SET $nivel_user_projeto = '$id_usuario_Projeto' WHERE id_obra = '$id_Projeto_usuario'");

$renova_estacao = $conexao->query($sql);


/* if (!$renova_estacao || $renova_estacao->rowCount() == 0) {

    $retorno = array('codigo' => 0, 'mensagem' => "Para este nível de Usuário não é permitido torna-lo Responsável!");
    echo json_encode($retorno);
    $conexao = null;
    exit;

} */
        
if($renova_estacao){
            $retorno = array('codigo' => 1, 'mensagem' => "Usuário foi alterado com sucesso para este Projeto!");
            echo json_encode($retorno);
          
    
    //=====================[] envia email ]===========
    
    $sql = $conexao->query("SELECT o.nome_obra, u.email, u.nome, u.nivel, u.id, o.id_obra FROM usuarios_projeto up 
    INNER JOIN obras o ON o.id_obra = up.id_obra
    INNER JOIN estacoes e ON e.id_obra = o.id_obra
    INNER JOIN usuarios u ON u.id = up.id_usuario WHERE up.id_obra='$id_Projeto_usuario' ");
    
    $conta = $sql->rowCount();
    
    if($conta > 0 ){
    
        $rUsuarios = $sql->fetchALL(PDO::FETCH_ASSOC);
    
        
            foreach( $rUsuarios as $r){
    
    
                
    $chave_unica = $chave_unica;        
    
    $id_usuario=$r['id'];
    $email_usuario =$r['email']; // destinatário padrão
    $nome_usuario = $r['nome'];
    $nivel_acesso = $r['nivel'];
    $assunto = 'Usuário Responsável no Projeto: '.$r['nome_obra'].'' ;    
    $mensagem_email = "Olá $nome_usuario, você está sendo comunicado que o Projeto: <b> $r[id_obra] </b> - $r[nome_obra] <br> 
    Terá agora você como Responsável.<br><br>Através do usuário: <b>$_SESSION[nome]</b> com e-mail de acesso: <b>$_SESSION[email]</b>, na data de hoje. <br><br>

    Caso desconheça esta ação, entre em contato com a Gestão do STEP, através dos Canais de Suporte. <br><br><br>
    <small>Lembrando que todos os dados permanecem íntegros e disponíveis para auditoria e consulta de log.</small>";
    $template_email = '/views/emails/email-padrao.php';
    
    require_once  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';
    
    
    //=====================[] envia email ]===========
        
        }
    
        
       
        
        
        }}}else{
        
            $retorno = array('codigo' => 0, 'mensagem' => "Erro ao Tentar Mudar o Usuário para Responsável!");
            echo json_encode($retorno);
            $conexao = null;
            exit;
        
        }}




if($acao == "remove_responsavel_projeto"){


    $id_usuario_Projeto = $_POST['id_usuario_Projeto'] ?? '';
    $id_Projeto_usuario = $_POST['id_Projeto_usuario'] ?? '';
    
    if($id_usuario_Projeto == '' || $id_Projeto_usuario == ''){
    
        $retorno = array('codigo' => 0, 'mensagem' => "Dados Enviados não foram reconhecidos");
        echo json_encode($retorno);
        $conexao = null;
        exit;
    
    }
    
    $sql = "UPDATE usuarios_projeto SET responsavel='0' WHERE id_usuario = '$id_usuario_Projeto' AND id_obra = '$id_Projeto_usuario'";
    $apaga = $conexao->query($sql);
    
    if($apaga){

        $retorno = array('codigo' => 1, 'mensagem' => "O Usuário foi retirado como Responsável deste Projeto!");
        echo json_encode($retorno);
      

//=====================[] envia email ]===========

$sql = $conexao->query("SELECT o.nome_obra, u.email, u.nome, u.nivel, u.id, o.id_obra FROM usuarios_projeto up 
INNER JOIN obras o ON o.id_obra = up.id_obra
INNER JOIN estacoes e ON e.id_obra = o.id_obra
INNER JOIN usuarios u ON u.id = up.id_usuario WHERE up.id_obra='$id_Projeto_usuario' ");

$conta = $sql->rowCount();

if($conta > 0 ){

    $rUsuarios = $sql->fetchALL(PDO::FETCH_ASSOC);

    
        foreach( $rUsuarios as $r){


            
$chave_unica = $chave_unica;        

$id_usuario=$r['id'];
$email_usuario =$r['email']; // destinatário padrão
$nome_usuario = $r['nome'];
$nivel_acesso = $r['nivel'];
$assunto = 'Usuário Responsável no Projeto: '.$r['nome_obra'].'' ;    
$mensagem_email = "Olá, aviso do Sistema:<br><br> O <b>$nome_usuario</b>, você foi retirado como <b>Usuário Responsável</b>.<br>Projeto: <b> $r[id_obra] </b> - $r[nome_obra] <br> 
<br>Através do usuário: <b>$_SESSION[nome]</b> com e-mail de acesso: <b>$_SESSION[email]</b>, na data de hoje. <br><br>
";

$mensagem_email .= "<br>Caso desconheça esta ação, entre em contato com a Gestão do STEP, através dos Canais de Suporte. <br><br>
<small>Lembrando que todos os dados permanecem íntegros e disponíveis para auditoria e consulta de log.</small>";

$template_email = '/views/emails/email-padrao.php';

require_once  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';


//=====================[] envia email ]===========
    
    }

    
   
    
    
    }}else{
    
        $retorno = array('codigo' => 0, 'mensagem' => "Erro ao Tentar Mudar o Usuário para Responsável!");
        echo json_encode($retorno);
        $conexao = null;
        exit;
    
    }}