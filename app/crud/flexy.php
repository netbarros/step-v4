<?php
 date_default_timezone_set('America/Sao_Paulo');




$url="https://data.talk2m.com/getdata?t2mdevid=3cdaf03f-3038-4194-87af-42fa2529c6f3&t2maccount=eptech&t2musername=admin&t2mpassword=eptech311277";

//  Initiate curl
$ch = curl_init();

	
	
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Set the url
curl_setopt ($ch, CURLOPT_URL, $url);
	

// Execute
$result=curl_exec($ch);

if ($result === false) {
	echo "Falha de Conexão com a Nuvem Talk2M". curl_error ( $ch );

	exit;
} 


// Closing
curl_close($ch);




//$result = file_get_contents($url);


// Will dump a beauty json :3
$retorno = json_decode($result, true);


//print_r ($dados);

$flexy_id = $retorno['ewons'];




?>

<table>




<?php

echo "<b>COMUNICADORES</b><br/>";
foreach($retorno as $dados) 
{

	if (is_array($dados) || is_object($dados))
{
	
//print_r($dados);
	foreach($dados as $flexy) {
        
			echo "<b>id</b>: " . $flexy['id'] . "<br/>";
			echo "<b>Nome Flexy</b>: " . $flexy['name'] . "<br/>";
			
			echo "<br/>";
			
			echo "<b>TAGS</b>";
			echo "<br/>";
			echo "<br/>";

			foreach($flexy['tags'] as $tags) {
        
				echo "<b>id</b>: " . $tags['id'] . "<br/>";
				echo "<b>Nome Tag</b>: " . $tags['name'] . "<br/>";
				echo "<b>Tipo de Entrada</b>: " . $tags['dataType'] . "<br/>";
				echo "<b>Descrição</b>: " . $tags['description'] . "<br/>";
				echo "<b>Alarme</b>: " . $tags['alarmHint'] . "<br/>";
				echo "<b>Qualidade do Sinal da Tag</b>: " . $tags['quality'] . "<br/>";
				echo "<b>Flexy Tag Id</b>: " . $tags['ewonTagId'] . "<br/><br/>";
				echo ">> <b> Leitura mais Recente:</b> ".$tags['value'] ."<br/>";
				echo ">> <b> Data Leitura mais Recente:</b> ".data($flexy['lastSynchroDate']) ."<br/>";

				echo "<br/>";
	
			
			
				}

				echo "<b>Leituras:</b><br/>";

				foreach($tags['history'] as $hist) {
        
					echo "<b>Data Leitura</b>: " . data($hist['date']) . "<br/>";

					if(isset($hist['quality'])){
					echo "<b>Qualidade</b>: " . $hist['quality'] . "<br/>";
					}
					echo "<b>Tipo de Entrada</b>: " . $hist['dataType'] . "<br/>";
					echo "<b>Valor Leitura</b>: " . $hist['value'] . "<br/>";
						
					echo "<br/>";
		
				
				
					}
        
	}
	
} 

}


function data($data){
	return date("d/m/Y H:i", strtotime($data));
	}
	// exemplo de utilização:
	

?>


