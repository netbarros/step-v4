<?php 
header("Content-Type: application/json");
require '../../total-voice/autoload.php';

use TotalVoice\Client as TotalVoiceClient;

require_once '../../conexao.php';
 	// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();	
date_default_timezone_set('America/Sao_Paulo');

$acao = trim(isset($_POST['novo_suporte'])) ? $_POST['novo_suporte'] : '';
$estacao = trim(isset($_COOKIE['estacao_operador'])) ? $_COOKIE['estacao_operador'] : '';
$quem_abriu = trim(isset($_SESSION['id'])) ? $_SESSION['id'] : '';

$tipo_suporte = isset($_POST['tipo_suporte']) ? $_POST['tipo_suporte'] : '';
$desc_motivo_suporte = isset($_POST['desc_motivo_suporte']) ? $_POST['desc_motivo_suporte'] : '';
$plcode_suporte = isset($_POST['plcode_suporte']) ? $_POST['plcode_suporte'] : '0';
$parametro_suporte = isset($_POST['parametro_suporte']) ? $_POST['parametro_suporte'] : '0';

$status_suporte='1'; // novo suporte



if($tipo_suporte=='1'){ // Categoria/Tipo de Suporte Padrão no sistema > Leitura Fora de Parâmetro (Então)

    
    $retorno = array('codigo' => 0, 'retorno' => "Categoria Padrão do Sistema, só permitida através de uma Leitura de PLCode. Selecione uma Categoria de Suporte Correta, para seu Chamado e tente novamente.");

    echo json_encode($retorno);

    exit;



}

if($estacao==null){

    $retorno = array('codigo' => 0, 'retorno' => "Não há Estação Selecionada");

    echo json_encode($retorno);

    exit;

} else {

    $pega_Obra = $conexao->query("SELECT id_obra FROM estacoes WHERE estacao='$estacao'");
    $pega_Obra->execute();
    $row_Obra = $pega_Obra->fetch(PDO::FETCH_ASSOC);
    $obra = $row_Obra['id_obra'];


    $sql = "INSERT INTO suporte(
        tipo_suporte,
        motivo_suporte,
        obra,
        estacao,
        plcode,
        parametro,
        quem_abriu,
        status_suporte
        ) VALUES(
        :tipo_suporte,
        :motivo_suporte,
        :obra,
        :estacao,
        :plcode,
        :parametro,
        :quem_abriu,
        :status_suporte
            )";
    $stmt = $conexao->prepare( $sql );
    $stmt->bindParam( ':tipo_suporte', $tipo_suporte );
    $stmt->bindParam( ':motivo_suporte', $desc_motivo_suporte );
    $stmt->bindParam( ':obra', $obra );
    $stmt->bindParam( ':estacao', $estacao );
    $stmt->bindParam( ':plcode', $plcode_suporte );
    $stmt->bindParam( ':parametro', $parametro_suporte );
    $stmt->bindParam( ':quem_abriu', $quem_abriu );
    $stmt->bindParam( ':status_suporte', $status_suporte );
    
    $result = $stmt->execute();
    
    if ( ! $result )
    {
        var_dump( $stmt->errorInfo() );
        exit;
    } else{

        
        $retorno = array('codigo' => 1, 'retorno' => "Suporte Gerado com Sucesso");

        echo json_encode($retorno);
    
        exit;

}// Final da rotina dos alertas, Caso esteja habilitado alertas para esta categoria  ===<<

// =======[ FIM dos Alertas de Acordo com o Tipo de Suporte (Categoria)]====>>>

$conexao = null;

    }



?>