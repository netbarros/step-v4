<?php
header('Content-Type: text/html; charset=utf-8');
require_once '../../conexao.php';
 	// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();	

$id = isset($_GET['id']) ? $_GET['id'] : '';
$montar = isset($_GET['montar']) ? $_GET['montar'] : '';





//============================================================================================

if($montar==="produto_quimico_select"){
	// executa consulta à tabela
	
	// executa consulta à tabela
	$stmt = $conexao->prepare("SELECT * from produtos_quimicos  ORDER BY  id_produto_quimico DESC");
	$stmt->execute();
	
	while($laco = $stmt->fetch(PDO::FETCH_OBJ)){
	
	echo "<option value=".$laco->id_produto_quimico.">".$laco->nome_produto."</option> ";
	
	}
	
	
		}
	
	//============================================================================================	

//============================================================================================

if($montar==="unidade_medida_select"){
	// executa consulta à tabela
	
	// executa consulta à tabela
	$stmt = $conexao->prepare("SELECT * from unidade_medida  ORDER BY  id_unidade_medida DESC");
	$stmt->execute();
	
	while($laco = $stmt->fetch(PDO::FETCH_OBJ)){
	
	echo "<option value=".$laco->id_unidade_medida.">".$laco->nome_unidade_medida."</option> ";
	
	}
	
	
		}
	
	//============================================================================================	

//============================================================================================

if($montar==="tipo_instrumento_select"){
	// executa consulta à tabela
	
	// executa consulta à tabela
	$stmt = $conexao->prepare("SELECT * from tipo_instrumento  ORDER BY  id_tipo_instrumento DESC");
	$stmt->execute();
	
	while($laco = $stmt->fetch(PDO::FETCH_OBJ)){
	
	echo "<option value=".$laco->id_tipo_instrumento.">".$laco->nome_tipo_instrumento."</option> ";
	
	}
	
	
		}
	
	//============================================================================================	

//============================================================================================

if($montar==="tipo_equipamento_select"){
	// executa consulta à tabela
	
	// executa consulta à tabela
	$stmt = $conexao->prepare("SELECT * from tipo_equipamento  ORDER BY  id_tipo_equipamento DESC");
	$stmt->execute();
	
	while($laco = $stmt->fetch(PDO::FETCH_OBJ)){
	

		echo "<option value=".$laco->id_tipo_equipamento.">".$laco->nome_tipo_equipamento."</option>";


	
	}
	
	
		}
	
	//============================================================================================	

	//============================================================================================

if($montar==="tipo_tanque_select"){
	// executa consulta à tabela
	
	// executa consulta à tabela
	$sql = "SELECT id_tipo_tanque, nome_tipo_tanque from tipo_tanque  ORDER BY  id_tipo_tanque DESC";
	$stm = $conexao->prepare($sql);

	$stm->execute();
	
	
	$return = $stm->fetchAll(PDO::FETCH_ASSOC);

	$retorna_dados=array();
	


foreach ($return as $value) {

	$id = $value['id_tipo_tanque'];

	$text = $value['nome_tipo_tanque'];

	$retorna_dados[] = array('id'=> $id, 'text'=> $text);
	
}



	
	$retorno = json_encode($retorna_dados);

	echo $retorno;

	
	
	
		}
	
	//============================================================================================	

if($montar==="estacao_select_plcode_anterior"){
	// executa consulta à tabela

	$sql = "SELECT id_ponto,nome_ponto,objetivo_ponto from pontos_estacao WHERE id_estacao='$id' ORDER BY id_ponto DESC";
	$stm = $conexao->prepare($sql);

	$stm->execute();
	$json_data = $stm->fetchAll(PDO::FETCH_OBJ);



	$count = $stm->rowCount();


	$retorno = json_encode($json_data);

	echo $retorno;


}
	
	//============================================================================================

	

	if($montar==="estacao_select_plcode_checkin"){
		// executa consulta à tabela
	
		$id = isset($_GET['id']) ? $_GET['id'] : '';
		
		
		// executa consulta à tabela
		$stmt = $conexao->prepare("SELECT * from periodo_ponto 
		INNER JOIN pontos_estacao ON pontos_estacao.id_ponto = periodo_ponto.id_ponto 
		WHERE periodo_ponto.id_estacao=$id GROUP BY periodo_ponto.id_ponto ORDER BY periodo_ponto.id_ponto");
		$stmt->execute();
		
	
		if ($stmt->rowCount () < 1) { 
			echo "<option value='0' selected>Nenhum PLCode Localizado</option>";
	
			exit;
	
		}
		echo "<option value='0' selected>Selecione o Check-In via PLCode</option>";
		while($laco = $stmt->fetch(PDO::FETCH_OBJ)){
		
			
			echo "<option value=".$laco->id_ponto.">".$laco->nome_ponto." - ".$laco->objetivo_ponto."</option> ";
		
		
		}
		
		
			}
		
		//============================================================================================


if($montar==="obra_select_estacao_checkin"){
	// executa consulta à tabela

	$id = isset($_GET['id']) ? $_GET['id'] : '';
	
	
	// executa consulta à tabela
	$stmt = $conexao->prepare("SELECT id_estacao,nome_estacao from estacoes WHERE id_obra=$id ORDER BY nome_estacao ASC");
	$stmt->execute();
	

	if ($stmt->rowCount () < 1) { 
		echo "<option value='0' selected>Nenhuma Obra Localizada</option>";

		exit;

	}
	echo "<option value='0' selected>Selecione a Obra</option>";
	while($laco = $stmt->fetch(PDO::FETCH_OBJ)){
	
		
		echo "<option value=".$laco->id_estacao.">".$laco->nome_estacao."</option> ";
	
	
	}
	
	
		}
	
	//============================================================================================

	
	if($montar==="plcode_select_operador_checkin"){
		// executa consulta à tabela
	
		$id = isset($_GET['id']) ? $_GET['id'] : '';
		
		
		// executa consulta à tabela
		$stmt = $conexao->prepare("SELECT * from checkin 
		INNER JOIN colaboradores ON colaboradores.id_colaborador = checkin.id_colaborador 
		WHERE checkin.id_ponto=$id GROUP BY checkin.id_colaborador ");
		$stmt->execute();
		
	
		if ($stmt->rowCount () < 1) { 
			echo "<option value='0' selected>Nenhum Operador Fez Check-In neste PLCode</option>";
	
			exit;
	
		}
		echo "<option value='' selected>Selecione o Operador</option>";
		while($laco = $stmt->fetch(PDO::FETCH_OBJ)){
		
			
			echo "<option value=".$laco->id_colaborador.">".$laco->nome." - ".$laco->sobrenome."</option> ";
		
		
		}
		
		
			}
		
		//============================================================================================


if($montar==="obra_select_estacao"){
	// executa consulta à tabela



	$sql = "SELECT id_estacao,nome_estacao from estacoes WHERE id_obra='$id' ORDER BY nome_estacao DESC";
$stm = $conexao->prepare($sql);

$stm->execute();
$json_data = $stm->fetchAll(PDO::FETCH_OBJ);



$count = $stm->rowCount();


$retorno = json_encode($json_data);

echo $retorno;


	
	
		}
	
	//============================================================================================
	

if($montar==="cliente_select_obra"){
	// executa consulta à tabela

	$sql = "SELECT id_obra,nome_obra from obras WHERE id_cliente='$id' ORDER BY nome_obra DESC";
$stm = $conexao->prepare($sql);

$stm->execute();
$json_data = $stm->fetchAll(PDO::FETCH_OBJ);



$count = $stm->rowCount();


$retorno = json_encode($json_data);

echo $retorno;
	
	
		}
	
	//============================================================================================

if($montar==="cargo_select"){
// executa consulta à tabela

// executa consulta à tabela

$stmt = $conexao->prepare("SELECT * from cargos  ORDER BY  id_cargo DESC");
$stmt->execute();

while($laco = $stmt->fetch(PDO::FETCH_OBJ)){

	echo "<option value=".$laco->id_cargo.">".$laco->nome_cargo."</option> ";

}

	}

//============================================================================================	

	if($montar==="departamento_select"){
		// executa consulta à tabela
		
		// executa consulta à tabela
		$stmt = $conexao->prepare("SELECT * from departamentos  ORDER BY  id_dep DESC");
		$stmt->execute();
		

while($laco = $stmt->fetch( PDO::FETCH_ASSOC )){ 
		
		
			echo "<option value=".$laco['id_dep'].">".$laco['nome_dep']."</option> ";
		
	
		}
		
}

//==============================================================================================
//============================================================================================	

	if($montar==="tipo_facilite_select"){
		// executa consulta à tabela
		
		// executa consulta à tabela
		$stmt = $conexao->prepare("SELECT * from tipo_facilite  ORDER BY  id_tipo_facilite DESC");
		$stmt->execute();
		

while($laco = $stmt->fetch( PDO::FETCH_ASSOC )){ 
		
		
			echo "<option value=".$laco['id_tipo_facilite'].">".$laco['nome_tipo_facilite']."</option> ";
		
	
		}
		
}

//==============================================================================================

if($montar==="tipo_efluente_select"){
	// executa consulta à tabela
	
	// executa consulta à tabela
	$stmt = $conexao->prepare("SELECT * from efluentes_tipos  ORDER BY  id_tipo_efluente DESC");
	$stmt->execute();
	$count = $stmt->rowCount();

	
	while ($laco = $stmt->fetch()) {

		if($id==$laco['id_tipo_efluente']):
			echo "<option value=".$laco['id_tipo_efluente']." selected>".$laco['nome_tipo_efluente']."</option>";
		
		else:
	
		
			echo "<option value=".$laco['id_tipo_efluente'].">".$laco['nome_tipo_efluente']."</option> ";
		
		endif;

	



	}


	
}



//============================================================================================	

if($montar==="categoria_doc_select"){
	// executa consulta à tabela
	
	// executa consulta à tabela
	$stmt = $conexao->prepare("SELECT * from categoria_pasta  ORDER BY  id_categoria_pasta ASC");
	$stmt->execute();
	

while($laco = $stmt->fetch( PDO::FETCH_ASSOC )){ 
	
	
		echo "<option value=".$laco['id_categoria_pasta']." selected>".$laco['nome_categoria_pasta']."</option> ";
	

	}
	
}

//==============================================================================================



if($montar==="checkin_plcode"){
	// executa consulta à tabela

	$sql = "SELECT 
	p.id_ponto,
	p.nome_ponto,
	e.nome_estacao,
	e.id_estacao
	
	from pontos_estacao p
	INNER JOIN estacoes e ON e.id_obra = p.id_obra
	WHERE p.id_obra='$id' AND p.status_ponto!='3' GROUP BY p.id_ponto ORDER BY e.nome_estacao ASC";
	$stm = $conexao->prepare($sql);

	$stm->execute();
	$json_data = $stm->fetchAll(PDO::FETCH_OBJ);



	$count = $stm->rowCount();


	$retorno = json_encode($json_data);

	echo $retorno;


}
	
	//============================================================================================


if($montar=== "checkin_indicador"){
	// executa consulta à tabela

	$sql = "SELECT id_parametro,nome_parametro from parametros_ponto WHERE id_ponto='$id' ORDER BY nome_parametro ASC";
	$stm = $conexao->prepare($sql);

	$stm->execute();
	$json_data = $stm->fetchAll(PDO::FETCH_OBJ);



	$count = $stm->rowCount();


	$retorno = json_encode($json_data);

	echo $retorno;


}


if ($montar === "dias_tarefa") {


	
    try {
		// Preparando a primeira consulta
		$sql1 = $conexao->prepare("SELECT * FROM periodo_dia_ponto pr 
								   INNER JOIN dia_semana d ON d.id_dia_semana = pr.dia_semana 
								   WHERE pr.id_periodo_ponto=:id 
								   GROUP BY d.id_dia_semana");
		$sql1->bindParam(':id', $id, PDO::PARAM_INT);
		$sql1->execute();
	
		// Verificando se há resultados
		if ($sql1->rowCount() > 0) {
			$rUnidade = $sql1->fetchAll(PDO::FETCH_ASSOC);
			echo gerarHtml($rUnidade, $id);
		} else {
			// Preparando a segunda consulta para listar todos os dias da semana
			$sql2 = $conexao->prepare("SELECT * FROM dia_semana");
			$sql2->execute();
	
			// Verificando se há resultados
			if ($sql2->rowCount() > 0) {
				$rUnidade = $sql2->fetchAll(PDO::FETCH_ASSOC);
	
				// Gerar HTML para os <option>
				echo "<select name='dia_semana'>";
				foreach ($rUnidade as $value) {
					echo "<option value=\"{$value['id_dia_semana']}\">{$value['nome_dia_semana']}</option>";
				}
				echo "</select>";
			} else {
				echo "Não há dias da semana disponíveis";
			}
		}
	} catch (PDOException $e) {
		echo "Erro: " . $e->getMessage();
	}
	
}




// Função para gerar HTML
function gerarHtml($rUnidade, $id) {
    $html  = '';
   

    foreach ($rUnidade as $value) {
        $selected = ($value['id_periodo_ponto'] == $id) ? "selected" : "";
        $html .= "<option value=\"{$value['id_dia_semana']}\" $selected> {$value['nome_dia_semana']} </option>";
		
    }



    return $html;
}





	
	//============================================================================================