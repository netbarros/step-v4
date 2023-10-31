<?php
 require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
//ini_set("session.cookie_secure", 1);
if (!isset($_SESSION)) session_start();
date_default_timezone_set('America/Sao_Paulo');


$acao = trim(isset($_GET['acao'])) ? $_GET['acao'] : '';

$id = trim(isset($_GET['id'])) ? $_GET['id'] : '';
$id_usuario_sessao = trim(isset($_SESSION['id'])) ? $_SESSION['id'] : '';

if($acao=="libera_plcode"){


$verifica_plcode = $conexao->query("SELECT s.plcode,p.id_ponto,p.nome_ponto FROM suporte s
INNER JOIN pontos_estacao p ON p.id_ponto = s.plcode WHERE id_suporte='$id' AND p.status_ponto!='2'");



if($verifica_plcode){

    $row = $verifica_plcode->fetchObject();

    $sql_libera = $conexao->query("UPDATE pontos_estacao SET status_ponto='1' WHERE id_ponto='$row->id_ponto'");

    

    
            if($sql_libera){
                $retorno = array('codigo' => 1, 'retorno' => "PLCode Liberado com Sucesso!");

                echo json_encode($retorno);

                exit;
            }else {

                $retorno = array('codigo' => 0, 'retorno' => "Não foi possível Liberar o PLCode $row->nome_ponto. SQL Down");

                echo json_encode($retorno);
            
                exit;
            
                
            }

} else{

    $retorno = array('codigo' => 0, 'retorno' => "Não é possível Liberar um PLCode que se encontre Inativo!");

    echo json_encode($retorno);

    exit;


}

$conexao=null;

}



if($acao=="altera_cat_suporte"){
    
$id_nova_cat_suporte = trim(isset($_GET['id_nova_cat_suporte'])) ? $_GET['id_nova_cat_suporte'] : '';

$id_suporte = trim(isset($_GET['id_suporte'])) ? $_GET['id_suporte'] : '';

if($id_nova_cat_suporte!=""){


 $sql_cat_suporte = $conexao->query("UPDATE suporte SET tipo_suporte='$id_nova_cat_suporte' WHERE id_suporte='$id_suporte'");


  if($sql_cat_suporte){
                $retorno = array('codigo' => 1, 'retorno' => "Categoria Alterada com Sucesso!");

                echo json_encode($retorno);

                exit;
            }else {

                $retorno = array('codigo' => 0, 'retorno' => "Não foi possível Alterar a Categoria do Suporte ID $id_suporte. SQL Down");

                echo json_encode($retorno);
            
                exit;
            
                
            }
    
}

    
}

if($acao=="finaliza_chamado"){


    $verifica_suporte = $conexao->query("SELECT s.status_suporte,s.id_suporte,sup.id_conversa FROM suporte s
    LEFT JOIN suporte_conversas sup ON sup.id_suporte = s.id_suporte
     WHERE s.id_suporte='$id' ");
     $row = $verifica_suporte->fetchObject();
    
    
    if($row->status_suporte!='4'){


        if($row->id_conversa!=NULL){
    
        $data_hora_atual = date_create()->format('Y-m-d H:i:s'); 
    
        $sql_finaliza = $conexao->query("UPDATE suporte SET status_suporte='4', data_close='$data_hora_atual', quem_fechou='$id_usuario_sessao' WHERE id_suporte='$row->id_suporte'");

        $sql = $conexao->query("UPDATE suporte_conversas SET status_conversa='4' WHERE id_suporte='$id'");
        
                if($sql_finaliza){
                    $retorno = array('codigo' => 1, 'retorno' => "Chamado de Suporte Finalizado com Sucesso!");
    
                    echo json_encode($retorno);
    
                    exit;
                }else {
    
                    $retorno = array('codigo' => 0, 'retorno' => "Não foi possível Finalizar este Suporte. SQL Down");
    
                    echo json_encode($retorno);
                    $conexao=null;
                
                    exit;
                
                    
                }

            } else {    

                $retorno = array('codigo' => 0, 'retorno' => "Nenhum Chat Encontrado para este Suporte. Para poder finalizá-lo informe no chat a tratativa realizada.");
    
                echo json_encode($retorno);
                $conexao=null;
            
                exit;


            }
    
    } else{
    
        $retorno = array('codigo' => 0, 'retorno' => "Este Chamado de Suporte já foi Finalizado.");
    
        echo json_encode($retorno);
        $conexao=null;
    
        exit;
    
    
    }
    
    $conexao=null;
    
    }  //** encerrra finalizacao do chamado de suporte */





    if($acao=="reabre_suporte"){
    
    
        $verifica_suporte = $conexao->query("SELECT s.status_suporte,s.id_suporte FROM suporte s
         WHERE id_suporte='$id' ");
         $row = $verifica_suporte->fetchObject();
        
        
         if ($verifica_suporte->rowCount() > 0) {
        
            $data_hora_atual = date_create()->format('Y-m-d H:i:s'); 
        
            $sql_finaliza = $conexao->query("UPDATE suporte SET status_suporte='1', data_close=NULL WHERE id_suporte='$row->id_suporte'");
            
                    if($sql_finaliza){
                        $retorno = array('codigo' => 1, 'retorno' => "Status do Suporte ID: # '$row->id_suporte' alterado para Em Aberto com Sucesso!");
        
                        echo json_encode($retorno);
        
                        exit;
                    }else {
        
                        $retorno = array('codigo' => 0, 'retorno' => "Não foi possível alterar o Status deste Suporte. SQL Down");
        
                        echo json_encode($retorno);
                        $conexao=null;
                    
                        exit;
                    
                        
                    }
        
        } else{
        
            $retorno = array('codigo' => 0, 'retorno' => "Não foi possível alterar o Status deste Suporte. SQL Down");
        
            echo json_encode($retorno);
            $conexao=null;
        
            exit;
        
        
        }
        
        $conexao=null;
        
        } 