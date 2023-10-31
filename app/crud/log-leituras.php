<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
date_default_timezone_set('America/Sao_Paulo');

  $chave_unica_sessao = $_COOKIE['CHAVE_UNICA_SESSAO_ATUAL']; // recupera a chave global da sessao, para vinculo entre tb rmm e tb midia_leitura (Chave Estrangeira)
  $id_usuario = $_COOKIE['id_usuario_logado'];
  $estacao_logada = $_COOKIE['estacao_atual'];

  $acao = trim(isset($_GET['acao'])) ? $_GET['acao'] : '';

if($chave_unica_sessao!="" && $acao=='gps_bloqueado'){


    $acao="Verificado Bloqueio do acesso ao GPS no aparelho do usuário";
$acao_log="gps_fora";
$tipo_log='44';

    $sql = "INSERT INTO log_leitura(
        id_usuario,
        acao_log,
        chave_unica,
        acao,
        estacao_logada,
        tipo_log    
       ) VALUES(
        :id_usuario, 
        :acao_log,
        :chave_unica,
        :acao,
        :estacao_logada,
        :tipo_log
            )";
    $stmt = $conexao->prepare( $sql );
    $stmt->bindParam( ':id_usuario',$id_usuario);
    $stmt->bindParam( ':acao_log', $acao_log );
    $stmt->bindParam( ':estacao_logada', $estacao_logada );
    $stmt->bindParam( ':chave_unica', $chave_unica_sessao );
     $stmt->bindParam( ':acao', $acao );
     $stmt->bindParam( ':tipo_log', $tipo_log );
   
   
    $result = $stmt->execute();
    
    if ( ! $result )
    {
$resultado_log = 0;

        $conexao=null;
        exit;

    } 
    else
    {

$resultado_log = 1;



    $conexao=null;
    exit;
        }


}