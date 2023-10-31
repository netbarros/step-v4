<?php 
date_default_timezone_set('America/Sao_Paulo');
require_once '../../conexao.php';
 	// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();	


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

        // exibo o flex 

        $sql_flex ="SELECT * FROM flex_un fu
        INNER JOIN flex_tags ft ON  ft.id_auto_flex = fu.id_auto_flex  WHERE fu.status_cadastro_flex='1'";
        $stm = $conexao->query($sql_flex);

        $dados_flex = $stm->fetchALL(PDO::FETCH_ASSOC);
        
        foreach($dados_flex as $row) {
        //print_r($row);

        // verifico se o dado do flex é igual do step

        $id_flex_un = $row['id_flex_base'];
        $id_flex_tag = $row['id_tag_flex'];

        $id_flex_ewon = $flexy['id'];

        echo "<br/>";
        echo "Flex STEP".$id_flex_un;
echo "<br/>";
        echo "Flex EWON".$id_flex_ewon;
        echo "<br/>";
        echo "TAG STEP" . $id_flex_tag;

        

        if($id_flex_un==$id_flex_ewon){ // se encontrar na tabela de flex um id igual que vem da talk2m (significa que o flex foi encontrado pelo step) já localizado, salvo os dados vindos dele


            foreach($flexy['tags'] as $tags) { // pego os valores da tag vindas do flex já localizado pelo STEP


//===[gravo as tags de leitura ]====

if($id_flex_tag==$tags['id'] ){ // se o id da tag for a mesma tag atrelada ao flex do sistema, gravo as tags

$data_leitura_tag = data($flexy['lastSynchroDate']);
$grava_leitura = $conexao->query("INSERT INTO flex_plcode_leitura (id_flex_base,id_tag_flex,datatype_tag_flex,qualidade_sinal_tag,valor_leitura_tag,data_leitura_tag) 
VALUES('$flexy[id]','$tags[id]','$tags[dataType]','$tags[quality] ','$tags[value]','$data_leitura_tag' ) ");


if($grava_leitura){

	echo "<br/><h3><b>Dados do FLEX</b>: Armazenados com Sucesso!</h3><br/>";

} else{

	echo "<br/><h3><b>Dados do FLEX</b>: NÃO FORAM ARMAZENADOS!</h3><br/>";

}

} else{
    

	echo "<br/><h3><b>Análise dos Dados do FLEX</b>: O ID da TAG Flex não é o mesmo da TAG Flex do Sistema!</h3><br/>";

}







//===[gravo as tags de leitura ]====
            }


        } else {


            echo "<br/><h3><b>Análise dos Dados do FLEX</b>: O ID da Unidade Flex não é o mesmo do Flex do Sistema!</h3><br/>";
            echo "flex sistema:".$id_flex_un;
            echo"<br/>";
            echo "flex EWON:".$flexy['id'];
            echo"<br/>";

        }
        
			echo "<b>id</b>: " . $flexy['id'] . "<br/>";
			echo "<b>Nome Flexy</b>: " . $flexy['name'] . "<br/>";
			
			echo "<br/>";
			
			echo "<b>TAGS</b>";
			echo "<br/>";
            echo "<br/>";
            



			foreach($flexy['tags'] as $tags) {
            
            // pego os dados da tag de leitura                
        
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


                //===[begin: historico de leituras anteriores]===/
				// echo "<b>Leituras:</b><br/>";

				// foreach($tags['history'] as $hist) {
        
				// 	echo "<b>Data Leitura</b>: " . data($hist['date']) . "<br/>";

				// 	if(isset($hist['quality'])){
				// 	echo "<b>Qualidade</b>: " . $hist['quality'] . "<br/>";
				// 	}
				// 	echo "<b>Tipo de Entrada</b>: " . $hist['dataType'] . "<br/>";
				// 	echo "<b>Valor Leitura</b>: " . $hist['value'] . "<br/>";
						
				// 	echo "<br/>";

                // 	}
                 //===[ end: historico de leituras anteriores]===/
                }
	}
	
} 

}


function data($data){
	return date("Y-m-d H:i:s", strtotime($data));
	}
	// exemplo de utilização:
	

?>