<?php

require '../conexao.php';
	// Atribui uma conexão PDO
		$conexao = Conexao::getInstance();

		
$email = (isset($_GET['email'])) ? $_GET['email'] : '';

$modo_existente= isset($_GET['modo']) ? $_GET['modo'] : '';
	
         

   $consulta = $conexao->query(" SELECT email  FROM usuarios WHERE email = '$email' ORDER BY email ASC"); 
   //$row = $rs->fetch(PDO::FETCH_OBJ);

   if($modo_existente==="altera"){

    
    //contagem de registros
     if ($consulta->rowCount () >1) { 
    
        
    
        echo (json_encode(false));
    }
    else {
    
        echo (json_encode(true));
    }
    
 } else {
 
     if ($consulta->rowCount () >0) { 
    
    
         echo (json_encode(false));
     }
     else {
     
         echo (json_encode(true));
     }
 
 
 }
   




?>