<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();


     $id_indicador_revogado = trim(isset($_GET['id_indicador_revogado'])) ? $_GET['id_indicador_revogado'] : '';


    $motivo = trim(isset($_GET['motivo'])) ? $_GET['motivo'] : '';


    if($motivo==''){

      
 $retorno = array('codigo' => 0, 'mensagem' => 'Você precisa relatar o Motivo, para podermos prosseguir com a liberação da leitura deste indicador.');

echo json_encode($retorno, TRUE);

exit;



    }

    $chave_unica_sessao = $_COOKIE['CHAVE_UNICA_SESSAO_ATUAL']; // recupera a chave global da sessao, para vinculo entre tb rmm e tb midia_leitura (Chave Estrangeira)
    $estacao_atual = (isset($_COOKIE['estacao_atual'])) ? $_COOKIE['estacao_atual'] : '';
    $id_plcode_atual  = trim(isset($_COOKIE['plcode_lido'])) ? $_COOKIE['plcode_lido'] : '';
    $id_usuario = trim(isset($_COOKIE['id_usuario_logado'])) ? $_COOKIE['id_usuario_logado'] : '';




     if($id_indicador_revogado){


        if($chave_unica_sessao=='' || $chave_unica_sessao=='undefined' ){


 $retorno = array('codigo' => 0, 'mensagem' => 'Sua Chave Única de Acesso foi expirada após o último envio de Leituras, por favor Leia novamente o PLCode desejado para restabelecer a segurança dos dados.');

echo json_encode($retorno, TRUE);

exit;


        }

$sql1=$conexao->query("SELECT 

      e.supervisor,
      e.ro,
      usu.id AS ID_SU,
      uro.id AS ID_RO,
      colabSU.nome AS Nome_Supervisor,
    colabRO.nome AS Nome_RO
    
    FROM estacoes e 

        LEFT JOIN
    usuarios usu ON usu.bd_id = e.supervisor
        INNER JOIN
    usuarios uro ON uro.bd_id = e.ro
            LEFT JOIN
    colaboradores colabSU ON colabSU.id_colaborador = e.supervisor
        LEFT JOIN
    colaboradores colabRO ON colabRO.id_colaborador = e.ro

WHERE e.id_estacao ='$estacao_atual' ");

$result1 = $sql1->fetch(PDO::FETCH_OBJ);

         $ID_SU = $result1->ID_SU;
         $ID_RO = $result1->ID_RO;


      $stmt = $conexao->prepare('INSERT INTO  suporte (
        tipo_suporte,
        motivo_suporte, 
        estacao,
        plcode, 
        parametro, 
        quem_abriu,
        status_suporte,
        chave_unica
         ) VALUES(
        :tipo_suporte,
        :motivo_suporte, 
        :estacao,
        :plcode, 
        :parametro, 
        :quem_abriu,
        :status_suporte,
        :chave_unica
        
        )');
  $stmt->execute(array(

    ':tipo_suporte' => '92', // id_tipo_suporte= 92 (fixo) (Revogação de Indicador Durante a Leitura)
    ':motivo_suporte' => $motivo,
    ':estacao' => $estacao_atual,
    ':plcode' => $id_plcode_atual,
    ':parametro' => $id_indicador_revogado,
    ':quem_abriu' => $id_usuario,
    ':status_suporte'=>'6', // revogado pelo operador - Aguardando Análise (tudo que depende da API verificar para disparar os alertas de email e sms, terão status 5, seja suporte, leitura ou checkin)
    ':chave_unica' => $chave_unica_sessao
    
  ));


  $count = $stmt->rowCount();

$ultimo_id_suporte = $conexao->lastInsertId();

if($count > 0){

   $sql_su = "INSERT INTO suporte_conversas(
        id_suporte,id_remetente,destinatario_direto,conversa
        ) VALUES(
			:id_suporte,
		:id_remetente,
        :destinatario_direto,
        :conversa

            )";
    $stmt_su = $conexao->prepare( $sql_su );
    $stmt_su->bindParam( ':id_suporte', $ultimo_id_suporte );
    $stmt_su->bindParam( ':id_remetente', $id_usuario );
    $stmt_su->bindParam( ':destinatario_direto', $ID_SU );
    $stmt_su->bindParam( ':conversa', $motivo );
 
    
    $result_su = $stmt_su->execute();



       $sql_ro = "INSERT INTO suporte_conversas(
        id_suporte,
        id_remetente,
        destinatario_direto,
        conversa
        ) VALUES(
			:id_suporte,
		:id_remetente,
        :destinatario_direto,
        :conversa

            )";
    $stmt_ro = $conexao->prepare( $sql_ro );
    $stmt_ro->bindParam( ':id_suporte', $ultimo_id_suporte );
    $stmt_ro->bindParam( ':id_remetente', $id_usuario );
    $stmt_ro->bindParam( ':destinatario_direto', $ID_RO );
    $stmt_ro->bindParam( ':conversa', $motivo );
 
    
    $result_ro = $stmt_ro->execute();

     

$sql_atualiza_parametro =  $conexao->query("UPDATE parametros_ponto SET status_parametro='2' WHERE id_parametro='$id_indicador_revogado' ");

              
//  faço a gravação do log
$acao_log = "Revoga Indicador na Leitura";
$tipo_log = '39'; // Excluir (Revoga a necessidade de informar a Leitura do parâmetro Selecionado) Indicador durante a Leitura

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
    $chave_unica_sessao,
    $id_usuario,
    $acao_log,
    $estacao_atual,
    $tipo_log ]);
 //  faço a gravação do log



 $retorno = array('codigo' => 1, 'mensagem' => 'Revogação para a Leitura atual deste Indicador, Realizada com Sucesso!');

echo json_encode($retorno, TRUE);


} else{

 $retorno = array('codigo' => 0, 'mensagem' => 'Falha na Solicitação, Por favor tente mais tarde, não pudemos realizar sua Solicitação no Momento.');

echo json_encode($retorno, TRUE);

}


     }


