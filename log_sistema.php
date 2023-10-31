<?php
 date_default_timezone_set('America/Sao_Paulo');
 setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

//data e hora do log
   $datahora = date("Y-m-d H:i:s");   
   
   //ip do log   
   $ip = $_SERVER['REMOTE_ADDR'];   
   
   //ação do log(Acesso, Exclusão, Atualização, Inserção)
   $acao = $log_acao;   
   
   //Rotina acessada
   $rotina = $_SESSION['pagina_atual'] ?? 'Sem_pagina'; // rotina sequencia de tela ou acoes do usuario

   // nivel de acesso do usuario que efetuou a acao
   $nivel = $log_nivel_usuario;
   
   //historico, O que alterou, excluiu, ou acessou...   
   $historico = $log_historico;
   
   // Usuario Atual Registrado na Sessão
   $usuario = $log_usuario;
   
   //Insere o log no banco de dados 

   if(empty($id_estacao)){

	$estacao_atual = null;
   }else{

	$estacao_atual = $id_estacao;
   }
   
   
   
try{
$query_teste = $conexao->prepare('INSERT INTO log_sistema (
				
				id_estacao,
				datahora,
				ip,
				acao,
				rotina,
				historico,
				usuario,
				nivel
				) VALUES (	
				:id_estacao,				
				:datahora,
				:ip,
				:acao,
				:rotina,
				:historico,
				:usuario,
				:nivel
				)');

$query_teste->bindValue(':id_estacao',$estacao_atual,PDO::PARAM_STR);
$query_teste->bindValue(':datahora',$datahora,PDO::PARAM_STR);
$query_teste->bindValue(':ip',$ip,PDO::PARAM_STR);
$query_teste->bindValue(':acao',$acao,PDO::PARAM_STR);
$query_teste->bindValue(':rotina',$rotina,PDO::PARAM_STR);
$query_teste->bindValue(':historico',$historico,PDO::PARAM_STR);
$query_teste->bindValue(':usuario',$usuario,PDO::PARAM_STR);
$query_teste->bindValue(':nivel',$nivel,PDO::PARAM_STR);
$query_teste->execute();

//echo 'Cadastro com sucesso!';

}catch (PDOexception $e){
  echo 'Erro ao Gravar o Log do Sistema'.$e->getMessage();
}   



?>