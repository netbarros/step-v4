<?php
require_once 'conexao.php';
header("Content-Type: application/json");
date_default_timezone_set('America/Sao_Paulo');
// Atribui uma conexão PDO
     $conexao = Conexao::getInstance();
     if (session_status() === PHP_SESSION_NONE) {
    session_start();
}	

//data e hora do log
   $datahora = date("Y-m-d H:i:s");   
   
   //ip do log   
   $ip = $_SERVER['REMOTE_ADDR'];  
   
   
   // Núcleo Atual de Operação
   $id_estacao = trim(isset($_POST['log_id_estacao'])) ? $_POST['log_id_estacao'] : '';

   //ação do log(Acesso, Exclusão, Atualização, Inserção)
   $acao = trim(isset($_POST['log_acao'])) ? $_POST['log_acao'] : '';   
   
   //Rotina acessada
   $rotina = trim(isset($_POST['log_rotina'])) ? $_POST['log_rotina'] : '';   

   // nivel de acesso do usuario que efetuou a acao
   $nivel = trim(isset($_POST['log_nivel_usuario'])) ? $_POST['log_nivel_usuario'] : '';   
   
   //historico, O que alterou, excluiu, ou acessou...   
   $historico = trim(isset($_POST['log_historico'])) ? $_POST['log_historico'] : '';   
   
   // Usuario Atual Registrado na Sessão
   $usuario = trim(isset($_POST['log_usuario'])) ? $_POST['log_usuario'] : '';   
   
   //Insere o log no banco de dados 
   
   if($acao==""){ $conexao=null; exit;}   
   
try{
$query_teste = $conexao->prepare('INSERT INTO log_sistema (
				
				datahora,
				ip,
				acao,
				rotina,
				historico,
				usuario,
				nivel,
                id_estacao
				) VALUES (				
				:datahora,
				:ip,
				:acao,
				:rotina,
				:historico,
				:usuario,
				:nivel,
                :id_estacao
				)');
				
$query_teste->bindValue(':datahora',$datahora,PDO::PARAM_STR);
$query_teste->bindValue(':ip',$ip,PDO::PARAM_STR);
$query_teste->bindValue(':acao',$acao,PDO::PARAM_STR);
$query_teste->bindValue(':rotina',$rotina,PDO::PARAM_STR);
$query_teste->bindValue(':historico',$historico,PDO::PARAM_STR);
$query_teste->bindValue(':usuario',$usuario,PDO::PARAM_STR);
$query_teste->bindValue(':nivel',$nivel,PDO::PARAM_STR);
$query_teste->bindValue(':id_estacao',$id_estacao,PDO::PARAM_STR);
$query_teste->execute();

//echo 'Cadastro com sucesso!';

}catch (PDOexception $e){
  echo 'Erro ao Gravar o Log do Sistema'.$e->getMessage();
}   

if($query_teste){


    echo json_encode("[Log Geral Atualizado]");
}



?>