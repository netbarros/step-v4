<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('America/Sao_Paulo');
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();

$estacao_atual = isset($_COOKIE['estacao_atual']) ? trim($_COOKIE['estacao_atual']) : '';
$plcode_lido = isset($_COOKIE['plcode_lido']) ? trim($_COOKIE['plcode_lido']) : '';
$id_suporte = isset($_COOKIE['id_suporte']) ? trim($_COOKIE['id_suporte']) : '';

if(empty($estacao_atual) || empty($plcode_lido) || empty($id_suporte)){
  echo json_encode(['codigo' => 0, 'retorno' => "Sua chave de acesso única Expirou, por favor, leia um PLCode da sua Estação ou Refaça seu login."]);
  exit;
}

$nome_plcode = trim(isset($_COOKIE['nome_PLCode_Lido'])) ? $_COOKIE['nome_PLCode_Lido'] : '';
$nome_obra = trim(isset($_COOKIE['nome_Cliente_Atual'])) ? $_COOKIE['nome_Cliente_Atual'] : '';
$nome_estacao = trim(isset($_COOKIE['nome_Estacao_Atual'])) ? $_COOKIE['nome_Estacao_Atual'] : '';


$id_usuario_realizou_liberacao =  trim(isset($_COOKIE['id_usuario_logado'])) ? $_COOKIE['id_usuario_logado'] : '';  
$nome_usuario_logado =  trim(isset($_COOKIE['nome_usuario_logado'])) ? $_COOKIE['nome_usuario_logado'] : '';
$email_usuario_logado =  trim(isset($_COOKIE['email_usuario_logado'])) ? $_COOKIE['email_usuario_logado'] : '';  
$Texto_Motivo_Libera_PLCODE = trim(isset($_POST['Texto_Motivo_Libera_PLCODE'])) ? $_POST['Texto_Motivo_Libera_PLCODE'] : '';
$Texto_Motivo_Libera_Estacao = trim(isset($_POST['Texto_Motivo_Libera_PLCODE'])) ? $_POST['Texto_Motivo_Libera_PLCODE'] : ''; 

$status_plcode_atual = trim(isset($_COOKIE['status_plcode_atual'])) ? $_COOKIE['status_plcode_atual'] : ''; 
$status_estacao_atual = trim(isset($_COOKIE['status_estacao_atual'])) ? $_COOKIE['status_estacao_atual'] : ''; 

$chave_unica_suporte = $_COOKIE['CHAVE_UNICA_SESSAO_ATUAL']; // recupera a chave global da sessao, para vinculo entre tb rmm e tb midia_leitura (Chave Estrangeira)

$tipo_suporte='';
if($status_plcode_atual=='1'){
$tipo_suporte ='19'; // PLCode Reativado ou 6 = Estação Reativada
} else {

 $tipo_suporte = '6';
}



$motivo_resolutiva ='';
if($Texto_Motivo_Libera_PLCODE!=''){
$motivo_resolutiva = $Texto_Motivo_Libera_PLCODE ?? 'PLCode Liberado através do usuário '.$nome_usuario_logado.'';

}

if($Texto_Motivo_Libera_Estacao!=''){
$motivo_resolutiva = $Texto_Motivo_Libera_Estacao ?? 'Estação Liberada através do usuário '.$nome_usuario_logado.'';

}

if(!empty($id_suporte)){

    $verifica_plcode = $conexao->query("SELECT s.plcode,p.id_ponto,p.nome_ponto, p.id_estacao FROM suporte s 
    INNER JOIN pontos_estacao p ON p.id_ponto = s.plcode WHERE id_suporte='$id_suporte'");
  
    if($verifica_plcode){
      $row = $verifica_plcode->fetchObject();

            $sql_libera_plcode = $conexao->query("UPDATE pontos_estacao SET status_ponto='1' WHERE id_ponto='$plcode_lido'");

              $sql_libera_estacao = $conexao->query("UPDATE estacoes SET status_estacao='1' WHERE id_estacao='$estacao_atual'");

            
           
                    if($sql_libera_plcode){

                      

                            $verifica_suporte = $conexao->query("SELECT s.status_suporte,s.id_suporte FROM suporte s
                            WHERE id_suporte='$id_suporte' ");

                            $row = $verifica_suporte->fetchObject();

                            if ($verifica_suporte->rowCount() > 0) {

                                 $data_hora_atual = date_create()->format('Y-m-d H:i:s'); 

                                 $sql_finaliza_suporte = $conexao->query("UPDATE suporte SET 
                                                                    status_suporte='4',
                                                                    data_close='$data_hora_atual',
                                                                    quem_fechou='$id_usuario_realizou_liberacao',
                                                                    motivo_resolutiva ='$motivo_resolutiva'

                                                                    WHERE id_suporte='$id_suporte'
                                                                 ");

                                    if($sql_finaliza_suporte){

                                        $email_Supervisor = '';


                                        $retorno = array('codigo' => 1, 'retorno' => "Chamado de Suporte Finalizado com Sucesso!");

                                        echo json_encode($retorno);

//  faço a gravação do log
$acao_log = "PLCode Liberado";
$tipo_log = '35'; // Alterou o Suporte

$sql_log = "INSERT INTO log_leitura (
  chave_unica,
   id_usuario, 
   acao_log,
   estacao_logada,
   tipo_log) 
VALUES (
      ?,
      ?,
      ?,
      ?,
      ?
      )";
$conexao->prepare($sql_log)->execute([
    $chave_unica_suporte,
    $id_usuario_realizou_liberacao,
    $acao_log,
    $estacao_atual,
    $tipo_log ]);
 //  faço a gravação do log


if($email_Supervisor!=''){
    $email_para = $email_usuario_logado;
    $nome_para = $nome_usuario_logado;
    $motivo_suporte = $motivo_resolutiva;
                               
//=====[ Inicio da classe envia email]=====================<<
  
include  $_SERVER['DOCUMENT_ROOT'].'/app/crud/enviar-email-suporte-plcode.php';     
//=====[ final da classe envia email]=====================<<
}



                                

                                    } else {

                                        $retorno = array('codigo' => 0, 'retorno' => "Não foi possível alterar o Status deste Suporte. \n\n ".print_r($sql_finaliza_suporte)."");

                                        echo json_encode($retorno);

                                        // gera log da atividade do usuario

                                
                                  


                                    }

                            } else {

                            $retorno = array('codigo' => 0, 'retorno' => "Não foi possível Localizar este Suporte. SQL Down. \n\n ".print_r($row)."");

                            echo json_encode($retorno);

                            // gera log da atividade do usuario
                        
                           


                            }

                    
                    } else {

                        $retorno = array('codigo' => 0, 'retorno' => "Não foi possível Liberar o PLCode $row->nome_ponto. SQL Down. \n\n ".print_r($row)."");

                        echo json_encode($retorno);
                    
                    
                        
                    }

            } else {
                echo json_encode(['codigo' => 0, 'retorno' => "Não é possível Liberar um PLCode que se encontre Inativo!"]);
                exit;
              }
            } else {
                echo json_encode(['codigo' => 0, 'retorno' => "Não foi possível Localizar este Suporte."]);
                exit;
              }

