<?php

require '../conexao.php';
	// Atribui uma conexão PDO
		$conexao = Conexao::getInstance();

		
        $cnpj   = (isset($_GET['cnpj'])) ? str_replace(array('.','-','_','/'), '', $_GET['cnpj']): '';

	
     $modo_existente= isset($_GET['modo']) ? $_GET['modo'] : '';

     

   $consulta = $conexao->query(" SELECT cnpj  FROM clientes WHERE cnpj = '$cnpj' ORDER BY cnpj ASC"); 
   //$row = $rs->fetch(PDO::FETCH_OBJ);
  
   if($modo_existente==="altera"){
   //contagem de registros
    if ($consulta->rowCount () >1) { 
   
      
   
       echo(json_encode(false));
   }
   else {
   
       echo(json_encode(true));
   }
   
} else {

    if ($consulta->rowCount () >0) { 
   
   
        echo(json_encode(false));
    }
    else {
    
        echo(json_encode(true));
    }


}
   




?>