<?php require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
 	// Atribui uma conexão PDO
    $conexao = Conexao::getInstance();
     if (!isset($_SESSION)) session_start();
     
     $id_plcode_atual = trim(isset($_GET['plcode'])) ? $_GET['plcode'] : '';




     $sql= $conexao->query("SELECT id_parametro FROM parametros_ponto WHERE id_ponto = '$id_plcode_atual'");


   $consulta = $stm->fetch(PDO::FETCH_ASSOC);  
        
        
   $x = 0;

while($x<count($consulta)){

    echo $consulta[$x] . "\n";

    $x++;

}
  

