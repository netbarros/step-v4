<?php
//** Arquivo de Recebimento do callback de todas as ações realizadas pelo STEP via Total-Voice - CRUD */

//https://totalvoice.github.io/totalvoice-docs/#envio-de-sms

//https://voice-app.zenvia.com/doc/#/

//https://github.com/totalvoice

/*

$dir = $_SERVER['DOCUMENT_ROOT'];

require_once $_SERVER['DOCUMENT_ROOT'].'/v2/conexao.php';
 	// Atribui uma conexão PDO
     $conexao  = getConnection();
     if (!isset($_SESSION)) session_start();

if($retorno_api!=''){
                            
  
      $id_suporte_open = isset($id_suporte_open) ? $id_suporte_open : '0';
      

   $status =  $retorno_api->status;
   $sucesso =  $retorno_api->sucesso;
   $motivo =  $retorno_api->motivo;
   $mensagem =  $retorno_api->mensagem;
   $dados = $retorno_api->dados;



   $sql = $conexao->prepare("INSERT INTO totalvoice_sms(
       aviso_sms,
       status,
       sucesso,
       motivo,
       mensagem,
       numero_destino,
       id_suporte,
       id_usuario,
       id_estacao,
       dados
   )
   VALUE (
   '$mensagem',
   '$status',
   '$sucesso',
   '$motivo',
   '$mensagem',
   '$numero_user',
   '$id_suporte_open',
   '$id_responsavel_regra',
   '$estacao',
   '$dados' )");

$rs->execute();


   //echo "Deu certo a inclusão!";
   

}


*/