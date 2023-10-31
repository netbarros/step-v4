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



if($tipo_relatorio=='tarefa-realizada'){


$sql = "SELECT 
ch.id_checkin,
pr.tipo_checkin,
ch.modo_checkin,
ch.id_periodo_ponto,
colab.nome,
colab.id,
ch.hora_leitura,
ch.hora_lida,
ch.status_checkin,
ch.prazo_decorrido,
ch.data_cadastro_checkin,
ch.chave_unica,
pr.modo_checkin_periodo,
md.nome_midia,

    a.id_ponto,
	a.id_estacao,
	a.id_obra,
	a.nome_ponto,
	a.tipo_ponto,
	b.nome_tipo,
	o.nome_obra,
	g.nome_estacao,
	i.nome_parametro 

    FROM checkin ch
INNER JOIN periodo_ponto pr ON pr.id_periodo_ponto = ch.id_periodo_ponto
LEFT JOIN midia_leitura md ON md.chave_unica  = ch.chave_unica
LEFT JOIN usuarios colab ON colab.id=ch.id_colaborador
LEFT JOIN pontos_estacao a ON a.id_ponto = pr.id_ponto
LEFT JOIN parametros_ponto i ON i.id_parametro = ch.id_parametro
LEFT JOIN estacoes g ON g.id_estacao = pr.id_estacao
LEFT JOIN tipo_ponto b ON  b.id_tipo_ponto = a.tipo_ponto
LEFT JOIN obras o ON o.id_obra = pr.id_obra
LEFT JOIN contatos c ON c.id_cliente = o.id_cliente
LEFT JOIN usuarios_projeto up ON up.id_usuario = $_SESSION[id]
 WHERE DATE_FORMAT(ch.data_cadastro_checkin, '%Y-%m-%d') BETWEEN '$Periodo_Inicial' AND '$Periodo_Final' $sql_personalizado $filtro ORDER BY ch.data_cadastro_checkin DESC";
$stm = $conexao->prepare($sql);

$stm->execute();


$count = $stm->rowCount();

//print_r($sql);
//array_push($json_data, "acoes");

if($count> 0){
	$data = array();



	
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


	function getStatusCheckin($status_checkin) {
		switch ($status_checkin) {
			case 1:
				return '<span class="badge badge-exclusive badge-light-success fw-bold fs-8 px-2 py-1 ms-1">Seguro</span>';
				break;
			case 2:
				return '<span class="badge badge-exclusive badge-light-primary fw-bold fs-8 px-2 py-1 ms-1">No Prazo</span>';
				break;
			
				case 3:
					return '<span class="badge badge-exclusive badge-light-danger fw-bold fs-8 px-2 py-1 ms-1">Fora do Prazo</span>';
					break;
					
					case 5:
						return '<span class="badge badge-exclusive badge-light-warning fw-bold fs-8 px-2 py-1 ms-1">Em Análise</span>';
						break;
			default:
				return $status_checkin;
				break;
		}
	}

	function getTipoCheckin($tipo_checkin, $id_tarefa) {
		switch ($tipo_checkin) {
			case 'ponto_plcode': //plcode
				return '<a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kt_modal_edita_target" data-id_tarefa="'.$id_tarefa.'"> <span class="badge badge-exclusive badge-light-info fw-bold fs-8 px-2 py-1 ms-1" >Presencial</span></a>';
				break;
			case 'ponto_parametro': //indicador
				return '<a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kt_modal_edita_target" data-id_tarefa="'.$id_tarefa.'"> <span class="badge badge-exclusive badge-light-success fw-bold fs-8 px-2 py-1 ms-1" >Leitura</span></a>';
				break;
			case 'tarefa_agendada': // tarefa agendada
				return '<a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kt_modal_edita_target" data-id_tarefa="'.$id_tarefa.'"> <span class="badge badge-exclusive badge-light-primary fw-bold fs-8 px-2 py-1 ms-1" >Tarefa Delegada</span></a>';
				break;

				
			default:
				return $tipo_checkin;
				break;
		}
	}



	function getModoCheckin($modo_checkin_periodo) {
		switch ($modo_checkin_periodo) {
			case 1: //plcode
				return '<span class="badge badge-exclusive badge-light-success fw-bold fs-8 px-2 py-1 ms-1">Horário Livre</span>';
				break;
			case 2: //indicador
				return '<span class="badge badge-exclusive badge-light-warning fw-bold fs-8 px-2 py-1 ms-1">Horário Agendado</span>';
				break;
			
			default:
				return $modo_checkin_periodo;
				break;
		}
	}

	function getPrazo($prazo_decorrido) {


		if (!is_null($prazo_decorrido)) {

			return $prazo_decorrido = '<span class="badge badge-exclusive badge-light-warning fw-bold fs-8 px-2 py-1 ms-1 min-h-10px" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Tempo em Minutos entre o Horário Previsto e o Executado">  ' .$prazo_decorrido.' 
			
			<span class="svg-icon svg-icon-6 svg-icon-warning ">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path opacity="0.3" d="M20.9 12.9C20.3 12.9 19.9 12.5 19.9 11.9C19.9 11.3 20.3 10.9 20.9 10.9H21.8C21.3 6.2 17.6 2.4 12.9 2V2.9C12.9 3.5 12.5 3.9 11.9 3.9C11.3 3.9 10.9 3.5 10.9 2.9V2C6.19999 2.5 2.4 6.2 2 10.9H2.89999C3.49999 10.9 3.89999 11.3 3.89999 11.9C3.89999 12.5 3.49999 12.9 2.89999 12.9H2C2.5 17.6 6.19999 21.4 10.9 21.8V20.9C10.9 20.3 11.3 19.9 11.9 19.9C12.5 19.9 12.9 20.3 12.9 20.9V21.8C17.6 21.3 21.4 17.6 21.8 12.9H20.9Z" fill="currentColor"/>
			<path d="M16.9 10.9H13.6C13.4 10.6 13.2 10.4 12.9 10.2V5.90002C12.9 5.30002 12.5 4.90002 11.9 4.90002C11.3 4.90002 10.9 5.30002 10.9 5.90002V10.2C10.6 10.4 10.4 10.6 10.2 10.9H9.89999C9.29999 10.9 8.89999 11.3 8.89999 11.9C8.89999 12.5 9.29999 12.9 9.89999 12.9H10.2C10.4 13.2 10.6 13.4 10.9 13.6V13.9C10.9 14.5 11.3 14.9 11.9 14.9C12.5 14.9 12.9 14.5 12.9 13.9V13.6C13.2 13.4 13.4 13.2 13.6 12.9H16.9C17.5 12.9 17.9 12.5 17.9 11.9C17.9 11.3 17.5 10.9 16.9 10.9Z" fill="currentColor"/>
			</svg>
			</span>
			</span>';
		}else{

			return $prazo_decorrido='<span class="svg-icon svg-icon-6 svg-icon-success " data-bs-toggle="tooltip" data-bs-placement="bottom" title="Tempo de Tarefa não Controlado">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path opacity="0.3" d="M20.9 12.9C20.3 12.9 19.9 12.5 19.9 11.9C19.9 11.3 20.3 10.9 20.9 10.9H21.8C21.3 6.2 17.6 2.4 12.9 2V2.9C12.9 3.5 12.5 3.9 11.9 3.9C11.3 3.9 10.9 3.5 10.9 2.9V2C6.19999 2.5 2.4 6.2 2 10.9H2.89999C3.49999 10.9 3.89999 11.3 3.89999 11.9C3.89999 12.5 3.49999 12.9 2.89999 12.9H2C2.5 17.6 6.19999 21.4 10.9 21.8V20.9C10.9 20.3 11.3 19.9 11.9 19.9C12.5 19.9 12.9 20.3 12.9 20.9V21.8C17.6 21.3 21.4 17.6 21.8 12.9H20.9Z" fill="currentColor"/>
			<path d="M16.9 10.9H13.6C13.4 10.6 13.2 10.4 12.9 10.2V5.90002C12.9 5.30002 12.5 4.90002 11.9 4.90002C11.3 4.90002 10.9 5.30002 10.9 5.90002V10.2C10.6 10.4 10.4 10.6 10.2 10.9H9.89999C9.29999 10.9 8.89999 11.3 8.89999 11.9C8.89999 12.5 9.29999 12.9 9.89999 12.9H10.2C10.4 13.2 10.6 13.4 10.9 13.6V13.9C10.9 14.5 11.3 14.9 11.9 14.9C12.5 14.9 12.9 14.5 12.9 13.9V13.6C13.2 13.4 13.4 13.2 13.6 12.9H16.9C17.5 12.9 17.9 12.5 17.9 11.9C17.9 11.3 17.5 10.9 16.9 10.9Z" fill="currentColor"/>
			</svg>
			</span>
			</span>';
		}


	}


	

	while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {

		
$id_tarefa = $row['id_periodo_ponto'];
		
		$hora_prevista_bd = $row['hora_leitura'];
		$hora_prevista = date('H:i', strtotime($hora_prevista_bd));


		$hora_lida_bd = $row['hora_lida'];
		$hora_lida = date('H:i', strtotime($hora_lida_bd));

//'midia' => $row['nome_midia'] ?? '',

//

$projeto = "<a target='_blank' href='../../views/projetos/view-project.php?id=$row[id_obra]&amp;projeto=$row[nome_obra]' class='text-gray-800 text-hover-primary mb-1'>$row[nome_obra]</a>";
$nome = "<a target='_blank' href='../../views/conta-usuario/overview.php?id=$row[id]&amp;nome=$row[nome]' class='text-gray-800 text-hover-primary mb-1'>$row[nome]</a>";
$tem_midia = $row['nome_midia'] ;
$midia ='';

//../../crud/leituras/
$filename = "/app/midias_leitura/".$tem_midia;

if (file_exists($filename)) {
   

	$midia ='<a class="overlay"  href="/app/midias_leitura/'.$tem_midia.'" data-bs-toggle="modal" data-bs-target="#imagemModal">
	
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
	$midia ='<a class="overlay"  href="javascript:;" >
	
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





		$data[] = array(
			'data' => $row['data_cadastro_checkin'] ? (new DateTime($row['data_cadastro_checkin']))->format('d/m/y H:i') : '',
			'tipo_checkin' => (getTipoCheckin($row['tipo_checkin'],$id_tarefa )),
			'modo_checkin_periodo' => (getModoCheckin($row['modo_checkin_periodo'])),
			'id_usuario' => ($nome),
			'nome_obra' => ($row['nome_obra'] ?? 'N/A'),
			'nome_estacao' => ($row['nome_estacao'] ?? ''),
			'nome_ponto' => ($row['nome_ponto'] ?? '') ,
			'nome_parametro' => ($row['nome_parametro'] ?? ''),
			'hora_leitura' => ($hora_prevista),	
			'hora_lida' => ($hora_lida) . ' ' . (getPrazo($row['prazo_decorrido'])),	
			'midia'		=>verificarMidia($row['nome_midia'],$row['chave_unica']),
			'status' => getStatusCheckin($row['status_checkin'])
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