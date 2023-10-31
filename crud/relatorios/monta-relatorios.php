<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

$_SESSION['pagina_atual'] = 'Relatório de Leituras Realizadas';

$tipo_relatorio = trim(isset($_POST['tipo_relatorio'])) ? $_POST['tipo_relatorio'] : '';

$Periodo_Inicial = trim(isset($_POST['Periodo_Inicial'])) ? $_POST['Periodo_Inicial'] : '';
$Periodo_Final = trim(isset($_POST['Periodo_Final'])) ? $_POST['Periodo_Final'] : '';

$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';

$projeto_atual = trim(isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';


$filtro = "";
$filtro_data="";

if ($projeto_atual != '') {


    $filtro = "AND o.id_obra ='$projeto_atual ' ";
} else {

    $filtro = "";
}

$sql_personalizado = '';


$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';
if ($nivel_acesso_user_sessao == 'supervisor') {

    $sql_personalizado = "AND (g.supervisor = '$id_BD_Colaborador'  OR up.id_usuario  = '$id_usuario_sessao')";
}

if ($nivel_acesso_user_sessao == 'ro') {

    $sql_personalizado = "AND (g.ro = '$id_BD_Colaborador'  OR up.id_usuario  = '$id_usuario_sessao')";
}

if ($nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado = "AND (o.id_cliente = '$id_tabela_cliente_sessao'  OR up.id_usuario  = '$id_usuario_sessao')";
}



if($tipo_relatorio=='leitura-realizada'){


$sql = "SELECT 	a.id_ponto,	a.id_estacao,	a.id_obra,
	a.nome_ponto,
	a.tipo_ponto,
	a.objetivo_ponto,
	b.nome_tipo,
	o.nome_obra,
	g.nome_estacao,
	h.leitura_entrada,
	h.leitura_saida,
	h.id_rmm,
	h.id_parametro,
	h.data_leitura,
	h.status_leitura,
	h.chave_unica,
	i.nome_parametro,
    i.concen_min,
    i.concen_max,
    i.origem_leitura_parametro,
	u.nome_unidade_medida,
	us.id as id_usuario,
	us.nome,
	us.nivel,
	prp.tipo_checkin,
	us.nome as nome_usuario,
	md.nome_midia

  From rmm h 
INNER JOIN pontos_estacao a ON a.id_ponto = h.id_ponto
INNER JOIN obras o ON o.id_obra = a.id_obra
LEFT JOIN tipo_ponto b ON  b.id_tipo_ponto = a.tipo_ponto
LEFT JOIN midia_leitura md ON md.chave_unica = h.chave_unica
LEFT JOIN estacoes g ON g.id_estacao = a.id_estacao
LEFT JOIN usuarios us ON us.id = h.id_operador
LEFT JOIN parametros_ponto i ON i.id_parametro = h.id_parametro
LEFT JOIN periodo_ponto prp ON prp.id_parametro = i.id_parametro
LEFT JOIN unidade_medida u ON u.id_unidade_medida= i.unidade_medida
LEFT JOIN usuarios_projeto up ON up.id_usuario = $_SESSION[id]
 WHERE  (h.id_parametro<>'0') 
 AND  DATE_FORMAT(h.data_leitura, '%Y-%m-%d') BETWEEN '$Periodo_Inicial' AND '$Periodo_Final'  $sql_personalizado $filtro GROUP BY h.id_rmm ORDER BY h.data_leitura DESC";
$stm = $conexao->prepare($sql);

$stm->execute();


$count = $stm->rowCount();


//print_r($sql);
//array_push($json_data, "acoes");

if($count> 0){
	$data = array();


	

	function getStatusLeitura($status_leitura) {
		switch ($status_leitura) {
			case 0:
				return '<span class="badge badge-exclusive badge-light-success fw-bold fs-9 px-2 py-1 ms-1">Em Validação</span>';
				break;
			case 1:
				return '<span class="badge badge-exclusive badge-light-success fw-bold fs-9 px-2 py-1 ms-1">Leitura OK</span>';
				break;
			case 3:
				return '<span class="badge badge-exclusive badge-light-danger fw-bold fs-9 px-2 py-1 ms-1">Fora do Padrão</span>';
				break;
			case 5:
				return '<span class="badge badge-exclusive badge-light-warning fw-bold fs-9 px-2 py-1 ms-1">Validando</span>';
				break;
			default:
				return $status_leitura;
				break;
		}
	}

	function getNivelUsuario($nivel_usuario) {
		switch ($nivel_usuario) {
			case 'admin':
				return '<span class="badge badge-exclusive badge-light-success fw-bold fs-9 px-2 py-1 ms-1">Admin</span>';
				break;
			case 'ro':
				return '<span class="badge badge-exclusive badge-light-success fw-bold fs-9 px-2 py-1 ms-1">RO</span>';
				break;
			case 'supervisor':
				return '<span class="badge badge-exclusive badge-light-success fw-bold fs-9 px-2 py-1 ms-1">Supervisor</span>';
				break;

				case 'operador':
					return '<span class="badge badge-exclusive badge-light-success fw-bold fs-9 px-2 py-1 ms-1">Operador</span>';
					break;
					
				case 'cliente':
					return '<span class="badge badge-exclusive badge-light-success fw-bold fs-9 px-2 py-1 ms-1">Cliente</span>';
					break;


						
				case 'engenheiro':
					return '<span class="badge badge-exclusive badge-light-success fw-bold fs-9 px-2 py-1 ms-1">Engenheiro IoT</span>';
					break;
			default:
				return $nivel_usuario;
				break;
		}
	}



	function verificarMidia($midia,$chave_unica) {

		if(isset($midia)){


				//../../crud/leituras/
				$filename = $_SERVER['DOCUMENT_ROOT'] . '/app/midias_leitura/'.$midia;
				
				if (file_exists($filename)) {
				   

					//* Colocar um atratativa  de varidar se o link da imagem veio do firebase do google ou se veio do timestamp da versão antiga do APP*/
				
					return '<a class="overlay"  href="https://step.eco.br/app/midias_leitura/'.$midia.'"  data-chave_unica="'.$chave_unica.'" data-bs-toggle="modal" data-bs-target="#imagemModal">
					
					<span class="svg-icon svg-icon-1 svg-icon-primary">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path opacity="0.3" d="M22 5V19C22 19.6 21.6 20 21 20H19.5L11.9 12.4C11.5 12 10.9 12 10.5 12.4L3 20C2.5 20 2 19.5 2 19V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5ZM7.5 7C6.7 7 6 7.7 6 8.5C6 9.3 6.7 10 7.5 10C8.3 10 9 9.3 9 8.5C9 7.7 8.3 7 7.5 7Z" fill="currentColor"/>
					<path d="M19.1 10C18.7 9.60001 18.1 9.60001 17.7 10L10.7 17H2V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V12.9L19.1 10Z" fill="currentColor"/>
					</svg>
					</span>
					
					
					
						<!--begin::Action-->
						<div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow">
							<i class="bi bi-eye-fill text-white fs-3x"></i>
						</div>
						<!--end::Action-->
					</a>';
				
				
				} else {
					return '<a class="overlay"  href="javascript:;" >
					
					<span class="svg-icon svg-icon-1 svg-icon-warning">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/>
				<rect x="9" y="13.0283" width="7.3536" height="1.2256" rx="0.6128" transform="rotate(-45 9 13.0283)" fill="currentColor"/>
				<rect x="9.86664" y="7.93359" width="7.3536" height="1.2256" rx="0.6128" transform="rotate(45 9.86664 7.93359)" fill="currentColor"/>
				</svg>
					</span>
					
					
					
						<!--begin::Action-->
						<div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow">
							<i class="bi bi-eye-slash-fill text-white fs-3x"></i>
						</div>
						<!--end::Action-->
					</a>';
				
				
				
				}
		

		}else {

			return '<a class="overlay"  href="javascript:;" >
					
					<span class="svg-icon svg-icon-1 svg-icon-warning">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/>
				<rect x="9" y="13.0283" width="7.3536" height="1.2256" rx="0.6128" transform="rotate(-45 9 13.0283)" fill="currentColor"/>
				<rect x="9.86664" y="7.93359" width="7.3536" height="1.2256" rx="0.6128" transform="rotate(45 9.86664 7.93359)" fill="currentColor"/>
				</svg>
					</span>
					
					
					
						<!--begin::Action-->
						<div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow">
							<i class="bi bi-eye-slash-fill text-white fs-3x"></i>
						</div>
						<!--end::Action-->
					</a>';
				




		}



}	

	while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {

	if($row['nome_usuario']==''){
		$row['nome_usuario']= $row['nome_obra']."<span class='badge badge-exclusive badge-light-primary fw-bold fs-9 px-2 py-1 ms-1'>PLC</span>";	
	}else {
		$row['nome_usuario']=$row['nome_usuario'];
	}
//'midia' => $row['nome_midia'] ?? '',

$projeto = "<a target='_blank' href='../../views/projetos/view-project.php?id=$row[id_obra]&amp;projeto=$row[nome_obra]' class='text-gray-800 text-hover-primary mb-1'>$row[nome_obra]</a>";
$nome = "<span  class='text-gray-800 text-hover-primary mb-1'>$row[nome_usuario]</span>";
$tem_midia = $row['nome_midia'] ;
$midia ='';
$id_leitura='';





		$data[] = array(
			'data' => $row['data_leitura'] ? (new DateTime($row['data_leitura']))->format('d/m/y H:i') : '',
			'status' => getStatusLeitura($row['status_leitura']),
			'midia'		=>verificarMidia($row['nome_midia'],$row['chave_unica']),
			'nome_usuario' => ($nome).' '. (getNivelUsuario($row['nivel'])),
			'indicador' => ($row['nome_parametro'] ?? '') ,
			'parametros' => ($row['concen_min'] ?? '') . ' <> ' . ($row['concen_max'] ?? ''),
			'leitura' => (isset($row['leitura_entrada']) ? $row['leitura_entrada'] : $row['leitura_saida']) . ' '. ($row['nome_unidade_medida'] ?? ''),	
			'projeto' => $projeto,
			'nucleo' => $row['nome_estacao'] ?? '',
			'plcode' => $row['nome_ponto'] ?? '',
			'caracteristica' => ($row['nome_tipo'] ?? ''),
			'grafico_leitura' => ($row['leitura_entrada'] ? $row['leitura_entrada'] : $row['leitura_saida']),
			'grafico_unidade_medida' => ($row['nome_unidade_medida'] ?? ''),
			'grafico_projeto' => $projeto,
			'grafico_usuario_leitura'=>($row['nome_usuario'] ?? ''),
			'grafico_param_min' => ($row['concen_min'] ?? ''),
			'grafico_param_max' => ($row['concen_max'] ?? ''),
			/* 
			'id_ponto' => $row['id_ponto'] ?? '',
			'id_estacao' => $row['id_estacao'] ?? '',
			'id_obra' => $row['id_obra'] ?? '',
			'id_rmm' => $row['id_rmm'] ?? '',
			'id_parametro' => $row['id_parametro'] ?? '',
			'chave_unica' => $row['chave_unica'] ?? '',
			*/
		);
	}
	
	echo json_encode($data , JSON_PRETTY_PRINT| JSON_UNESCAPED_SLASHES);





  }// se encontrar registro// $count

  else {

  

	$retorno = array('codigo' => 0, 'mensagem' => 'Nenhum registro encontrado!');

	

	echo json_encode($retorno , JSON_PRETTY_PRINT| JSON_UNESCAPED_SLASHES);
  }

} // fecha tipo_relatorio= leituras realizadas

?>