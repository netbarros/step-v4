<?php
 require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
//ini_set("session.cookie_secure", 1);
if (!isset($_SESSION)) session_start();



    $sql = "INSERT INTO suporte(
        tipo_suporte,
        motivo_suporte,
        estacao,
        plcode,
        parametro,
        quem_abriu,
        status_suporte
        ) VALUES(
        :tipo_suporte,
        :motivo_suporte,
        :estacao,
        :plcode,
        :parametro,
        :quem_abriu,
        :status_suporte
            )";
    $stmt = $conexao->prepare( $sql );
    $stmt->bindParam( ':tipo_suporte', $tipo_suporte );
    $stmt->bindParam( ':motivo_suporte', $desc_motivo_suporte );
    $stmt->bindParam( ':estacao', $estacao );
    $stmt->bindParam( ':plcode', $plcode_suporte );
    $stmt->bindParam( ':parametro', $parametro_suporte );
    $stmt->bindParam( ':quem_abriu', $quem_abriu );
    $stmt->bindParam( ':status_suporte', $status_suporte );
    
    $result = $stmt->execute();

    $ultimo_id_suporte = $conexao->lastInsertId();      
    
    if ( ! $result )
    {
        var_dump( $stmt->errorInfo() );
        exit;
    } else{


//        $retorno = array('codigo' => 1, 'retorno' => "Suporte Gerado com Sucesso");


    }



