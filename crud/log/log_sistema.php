<?php
 date_default_timezone_set('America/Sao_Paulo');	
 setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');			
//data e hora do log
   $datahora = date("Y-m-d H:i:s");   
   
   //ip do log   
  
   $ip = $_SERVER['REMOTE_ADDR']; 
   
   //ação do log(Acesso, Exclusão, Atualização, Inserção)
   $acao = $log_acao ?? 'Acesso';   
   
   //Rotina acessada
   $rotina = $log_rotina ?? 'Rotina não informada';

   // nivel de acesso do usuario que efetuou a acao
   $nivel = $_SESSION['nivel'] ?? null;
   
   //historico, O que alterou, excluiu, ou acessou...   
   $historico = $log_historico ?? 'Acesso ao sistema';
   
   // Usuario Atual Registrado na Sessão
   $usuario = $log_usuario ?? $_SESSION['id'];
   
   //Insere o log no banco de dados 

   if(empty($id_estacao)){

	$estacao_atual = null;
   
   }else{

	$estacao_atual = $id_estacao;
   }


   $projeto_sessao = $_COOKIE['projeto_atual'] ?? null; // Projeto selecinado obra no dashbaord

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



 
   
try{
$query_teste = $conexao->prepare('INSERT INTO log_sistema (
				
				id_obra,
				id_estacao,
				datahora,
				ip,
				acao,
				rotina,
				historico,
				usuario,
				nivel,
				chave_unica
				) VALUES (	
				:id_obra,
				:id_estacao,				
				:datahora,
				:ip,
				:acao,
				:rotina,
				:historico,
				:usuario,
				:nivel,
				:chave_unica
				)');
$query_teste->bindValue(':id_obra',$estacao_atual,PDO::PARAM_STR);
$query_teste->bindValue(':id_estacao',$estacao_atual,PDO::PARAM_STR);
$query_teste->bindValue(':datahora',$datahora,PDO::PARAM_STR);
$query_teste->bindValue(':ip',$ip,PDO::PARAM_STR);
$query_teste->bindValue(':acao',$acao,PDO::PARAM_STR);
$query_teste->bindValue(':rotina',$rotina,PDO::PARAM_STR);
$query_teste->bindValue(':historico',$historico,PDO::PARAM_STR);
$query_teste->bindValue(':usuario',$usuario,PDO::PARAM_STR);
$query_teste->bindValue(':nivel',$nivel,PDO::PARAM_STR);
$query_teste->bindValue(':chave_unica',$chave_unica,PDO::PARAM_STR);
$query_teste->execute();

//echo 'Cadastro com sucesso!';

}catch (PDOexception $e){
  echo 'Erro ao Gravar o Log do Sistema'.$e->getMessage();
}   



/* Modelo para inclusao de log nas paginas crud do sistema



                 //==================[ LOG do Sistema ] =======================//

                            //ação do log(Acesso, Exclusão, Atualização, Inserção)
                            $log_acao = $acao;
                            //Rotina acessada
                            $log_rotina = "Módulo: PLCode -> SubMódulo: Instrumento de Medição";
                             //historico, O que alterou, excluiu, ou acessou...

                            $log_nivel_usuario = $_COOKIE['nivel_acesso_usuario'] ?? null;
  
                            $log_historico = "O Usuário:" . $_SESSION['nome'] . ", Incluiu um Instrumento de Medição ID: $ultimo_id , para o PLCode ID:$id_plcode_atual   ";
                            
                            // Nome do Usuário Registrado na Sessão

                            $log_usuario = $_SESSION['id'];

                            require_once $_SERVER['DOCUMENT_ROOT'] . '/log_sistema.php';
  
                //==================[ Finaliza Log Sistema ]==================//


				*/

?>