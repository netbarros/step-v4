<?php
header("Content-Type: application/json");
require $_SERVER['DOCUMENT_ROOT'].'/v2/total-voice/autoload.php';
use TotalVoice\Client as TotalVoiceClient;

 function getConnection() {
    $dbhost="162.241.99.91";
    $dbuser="step_root";
    $dbpass="F@087913";
     $dbname="step_teste";
     $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
     $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}


 if (! ini_get('date.timezone')) {
     date_default_timezone_set('America/Sao_Paulo');
 }
ini_set('memory_limit', '-1');
// Atribui uma conexão PDO
date_default_timezone_set('America/Sao_Paulo');

$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();



$Grupo_EP_Celular='15981745522';
$retorno_alerta= 'teste de envio';

$Tel_SU='15 981745522';

 $Chave_TOTAL_VOICE = 'd87dde571d00c6a6505c7ed00d60805c';

$mensagem_alerta = "Olá Fabiano . ".$retorno_alerta."! Estação: tal - PLCODE: tal.
                        Usuário: Fabiano.";
 
                        


   $client = new TotalVoiceClient("d87dde571d00c6a6505c7ed00d60805c");
            
                        $numeroDestino =str_replace(' ', '', $Tel_SU); 
                        $response = $client->sms->enviar($numeroDestino, $mensagem_alerta);
                        $response->getContent(); // {}

//echo $response->getContent(); // {}
$retorno_api = $response->getContent();


$retorno_api_final = json_decode($retorno_api);


echo $retorno_api->status;

exit;
                            if($retorno_api_final){

                            // grava o log de retorno da API do SMS enviado:
                            $data_criacao = date_create()->format('Y-m-d H:i:s');

                            $status =  $retorno_api_final['status'];
                            $sucesso =  $response->sucesso;
                            $motivo =  $response->motivo;
                            $mensagem =  $response->mensagem;


echo  $status;
echo '\n';
echo  $sucesso;
echo '\n';
echo $motivo;
echo '\n';
echo $mensagem;
echo '\n';

         
                            }

?>