<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');



$_SESSION['pagina_atual'] = 'Dashboard de Suportes';

$tipo_relatorio = trim(isset($_POST['tipo_relatorio'])) ? $_POST['tipo_relatorio'] : '';

$Periodo_Inicial = trim(isset($_POST['Periodo_Inicial'])) ? $_POST['Periodo_Inicial'] : '';
$Periodo_Final = trim(isset($_POST['Periodo_Final'])) ? $_POST['Periodo_Final'] : '';

$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';

$projeto_atual = trim(isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';


$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';



$filtro = "";
$filtro_data="";

if ($projeto_atual != '') {

    $filtro = "AND o.id_obra ='$projeto_atual ' ";
} else {

    $filtro = "";
}

$sql_personalizado = '';


if ($nivel_acesso_user_sessao == 'supervisor') {

    $sql_personalizado = "AND (e.supervisor = '$id_BD_Colaborador'  OR up.id_usuario  = '$id_usuario_sessao')";
}

if ($nivel_acesso_user_sessao == 'ro') {

    $sql_personalizado = "AND (e.ro = '$id_BD_Colaborador'  OR up.id_usuario  = '$id_usuario_sessao')";
}

if ($nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado = "AND (o.id_cliente = '$id_tabela_cliente_sessao'  OR up.id_usuario  = '$id_usuario_sessao')";
}



if($tipo_relatorio=='acompanhamento_suporte'){


$sql = "SELECT s.*,u.nome, u.id, u.nivel,t.nome_suporte, o.nome_obra,o.id_obra,e.id_estacao,e.nome_estacao, md.nome_midia FROM suporte s
INNER JOIN estacoes e ON e.id_estacao = s.estacao
INNER JOIN obras o ON o.id_obra = e.id_obra
LEFT JOIN usuarios_projeto up ON up.id_obra = o.id_obra
INNER JOIN usuarios u ON u.id = s.quem_abriu
INNER JOIN tipo_suporte t ON t.id_tipo_suporte = s.tipo_suporte
LEFT JOIN midia_leitura md ON md.chave_unica = s.chave_unica
WHERE DATE_FORMAT(s.data_open, '%Y-%m-%d') BETWEEN '$Periodo_Inicial' AND '$Periodo_Final' $sql_personalizado $filtro GROUP BY s.id_suporte ORDER BY s.data_open DESC";
$stm = $conexao->prepare($sql);

$stm->execute();


$count = $stm->rowCount();

//print_r($sql);
//array_push($json_data, "acoes");

if($count> 0){
	$data = array();


	function getStatusLeitura($status_leitura) {
		switch ($status_leitura) {
			case 1:
				return '<span class="badge badge-exclusive badge-light-danger fw-bold fs-9 px-2 py-1 ms-1">Aguardando Atendimento</span>';
				break;
			case 2:
				return '<span class="badge badge-exclusive badge-light-primary fw-bold fs-9 px-2 py-1 ms-1">Em Atendimento</span>';
				break;
			case 3:
				return '<span class="badge badge-exclusive badge-light-info fw-bold fs-9 px-2 py-1 ms-1">Dependendo de Terceiros</span>';
				break;
				case 4:
					return '<span class="badge badge-exclusive badge-light-success fw-bold fs-9 px-2 py-1 ms-1">Finalizado</span>';
					break;

					case 5:
						return '<span class="badge badge-exclusive badge-light-warning fw-bold fs-9 px-2 py-1 ms-1">Com Previsão</span>';
						break;

					case 6:
						return '<span class="badge badge-exclusive badge-light-dark fw-bold fs-9 px-2 py-1 ms-1">Leitura Revogada</span>';
						break;

						case 7:
							return '<span class="badge badge-exclusive badge-light-warning fw-bold fs-9 px-2 py-1 ms-1">Leitura Liberada</span>';
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

	function verificarMidia($midia,$chave_unica,$id_suporte) {

		if(isset($midia)){


				//../../crud/leituras/
				$filename = $_SERVER['DOCUMENT_ROOT'] . '/app/midias_leitura/'.$midia;
				
				if (file_exists($filename)) {
				   
				
					return '<a class="overlay"  href="https://step.eco.br/app/midias_leitura/'.$midia.'" data-chave_unica="'.$chave_unica.'" data-id="'.$id_suporte.'" data-bs-toggle="modal" data-bs-target="#imagemModal">
					
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

	
//'midia' => $row['nome_midia'] ?? '',
$id_suporte = "<a  href='javascript:;' class='btn btn-sm btn-active-light-dark mb-1' data-id_suporte='$row[id_suporte]' data-kt-drawer-show='true' data-kt-drawer-target='#drawer_Suporte'  onclick='storeDataAttributesJanelaSuporte(this)'>$row[id_suporte]</a>";
$projeto = "<a target='_blank' href='../../views/projetos/view-project.php?id=$row[id_obra]&amp;projeto=$row[nome_obra]' class='text-gray-800 text-hover-primary mb-1'>$row[nome_obra]</a>";
$nome = "<a target='_blank' href='../../views/conta-usuario/overview.php?id=$row[id]&amp;nome=$row[nome]' class='text-gray-800 text-hover-primary mb-1'>$row[nome]</a>";
$tem_midia = $row['nome_midia'] ;
$midia ='';

$data_prevista = $row['data_prevista'] ? (new DateTime($row['data_prevista']))->format('d/m/y H:i') : '';

if($data_prevista!=''){
	$data_prevista_retorno = "Data Prevista: ".$data_prevista;
}else {

	$data_prevista_retorno='';
}


		$data[] = array(
			'id_suporte' =>($id_suporte),
			'midia'		=>verificarMidia($row['nome_midia'],$row['chave_unica'],$row['id_suporte']),
			'status' => getStatusLeitura($row['status_suporte']),
			'projeto' => ($projeto),
			'nucleo' => $row['nome_estacao'] ?? '',
			'nome_suporte' => $row['nome_suporte'] ?? '',
			'motivo_suporte' => ($row['motivo_suporte'] ?? ''),
			'nome_usuario' => ($nome).' '. (getNivelUsuario($row['nivel'])),
			'data_open' => $row['data_open'] ? (new DateTime($row['data_open']))->format('d/m/y H:i') : '',
			'data_close' => ($row['data_close'] ? (new DateTime($row['data_close']))->format('d/m/y H:i') : '') . ' ' . $data_prevista_retorno
			
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

} 
// Verificação e atribuição das variáveis




if($tipo_relatorio=='colecoes_suporte_email'){
// Dados que virão do ticke enviado no alerta

$id_tipo_suporte_ticket = isset($_COOKIE['id_tipo_suporte_ticket']) ? trim($_COOKIE['id_tipo_suporte_ticket']) : '';
$usuario_ticket = isset($_COOKIE['usuario_ticket']) ? trim($_COOKIE['usuario_ticket']) : '';
$nome_relatorio = isset($_POST['nome_relatorio']) ? trim($_POST['nome_relatorio']) : '';
$projeto_ticket = isset($_COOKIE['projeto_ticket']) ? trim($_COOKIE['projeto_ticket']) : '';

// Verifique se os cookies estão definidos e não estão vazios
if (empty($id_tipo_suporte_ticket) || empty($usuario_ticket) || empty($nome_relatorio) || empty($projeto_ticket)) {
    // Manipule o erro aqui, por exemplo, redirecionando de volta ao formulário com uma mensagem de erro.
    die("Variáveis da Coleção de Suporte não definidas");
} else {
	

	
/* consulto para saber qual obra e ticket estamos falando */

$sql = "SELECT s.*,
u.nome as nome_quem_abriu,
 u.id, u.nivel as nivel_quem_abriu,
	t.nome_suporte as nome_suporte_ticket,
	 o.nome_obra,
	  s.obra as obra_ticket_suporte,
	   s.estacao,
		e.nome_estacao,
		 md.nome_midia,
		 unp.nome as nome_usuario_ticket,
		 unp.id as id_usuario_notificacao
		 
	   FROM suporte s
	   INNER JOIN estacoes e ON e.id_estacao = s.estacao
        INNER JOIN obras o ON o.id_obra = s.obra
        INNER JOIN notificacoes_usuario nu ON nu.id_obra = s.obra
        INNER JOIN usuarios u ON u.id = s.quem_abriu
        INNER JOIN usuarios unp ON unp.id = nu.id_usuario
        INNER JOIN tipo_suporte t ON t.id_tipo_suporte = s.tipo_suporte
        LEFT JOIN midia_leitura md ON md.chave_unica = s.chave_unica
        WHERE nu.id_usuario = :usuario
		AND s.obra =:obra AND DATE_FORMAT(s.data_open, '%Y-%m-%d') BETWEEN :Periodo_Inicial AND :Periodo_Final
		   AND s.tipo_suporte = :id_tipo_suporte  AND s.status_suporte != '4'
		 GROUP BY s.id_suporte ORDER BY s.data_open DESC";

$stmt = $conexao->prepare($sql);

// Vinculando parâmetros
$stmt->bindParam(':obra', $projeto_ticket, PDO::PARAM_INT);
$stmt->bindParam(':usuario', $usuario_ticket, PDO::PARAM_INT);
$stmt->bindParam(':id_tipo_suporte', $id_tipo_suporte_ticket, PDO::PARAM_INT);
$stmt->bindParam(':Periodo_Inicial', $Periodo_Inicial, PDO::PARAM_STR);
$stmt->bindParam(':Periodo_Final', $Periodo_Final, PDO::PARAM_STR);

// Aqui é onde exibimos a consulta preparada
//$stm->debugDumpParams();

// Executando a consulta
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
//var_dump($rows[0]); erxibe a 1 linha do array

if (!$stmt->execute()) { //verificando se a consulta foi executada
    print_r($stmt->errorInfo()); //exibindo o erro
}

$total = $stmt->rowCount();

// Obtendo os resultados
if ($total > 0) {

	$data = array(); 

	function getStatusLeitura($status_leitura) {
		switch ($status_leitura) {
			case 1:
				return '<span class="badge badge-exclusive badge-light-danger fw-bold fs-9 px-2 py-1 ms-1">Aguardando Atendimento</span>';
				break;
			case 2:
				return '<span class="badge badge-exclusive badge-light-primary fw-bold fs-9 px-2 py-1 ms-1">Em Atendimento</span>';
				break;
			case 3:
				return '<span class="badge badge-exclusive badge-light-info fw-bold fs-9 px-2 py-1 ms-1">Dependendo de Terceiros</span>';
				break;
				case 4:
					return '<span class="badge badge-exclusive badge-light-success fw-bold fs-9 px-2 py-1 ms-1">Finalizado</span>';
					break;

					case 5:
						return '<span class="badge badge-exclusive badge-light-warning fw-bold fs-9 px-2 py-1 ms-1">Com Previsão</span>';
						break;

					case 6:
						return '<span class="badge badge-exclusive badge-light-dark fw-bold fs-9 px-2 py-1 ms-1">Leitura Revogada</span>';
						break;

						case 7:
							return '<span class="badge badge-exclusive badge-light-warning fw-bold fs-9 px-2 py-1 ms-1">Leitura Liberada</span>';
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
						return '<span class="badge badge-exclusive badge-light-primary fw-bold fs-9 px-2 py-1 ms-1">Engenheiro IoT</span>';
						break;
			default:
				return $nivel_usuario;
				break;
		}
	}

	function verificarMidia($midia,$chave_unica,$id_suporte) {

		if(isset($midia)){


				//../../crud/leituras/
				$filename = $_SERVER['DOCUMENT_ROOT'] . '/app/midias_leitura/'.$midia;
				
				if (file_exists($filename)) {
				   
				
					return '<a class="overlay"  href="https://step.eco.br/app/midias_leitura/'.$midia.'" data-chave_unica="'.$chave_unica.'" data-id="'.$id_suporte.'" data-bs-toggle="modal" data-bs-target="#imagemModal">
					
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

foreach ($rows as $row) {

	

			
//'midia' => $row['nome_midia'] ?? '',
$id_suporte = "<a  href='javascript:;' class='btn btn-sm btn-active-light-dark mb-1' data-id_suporte='$row[id_suporte]' data-kt-drawer-show='true' data-kt-drawer-target='#drawer_Suporte'  onclick='storeDataAttributesJanelaSuporte(this)'>$row[id_suporte]</a>";
$projeto = "<a target='_blank' href='../../views/projetos/view-project.php?id=$row[obra_ticket_suporte]&amp;projeto=$row[nome_obra]' class='text-gray-800 text-hover-primary mb-1'>$row[nome_obra]</a>";
$nome = "<a target='_blank' href='../../views/conta-usuario/overview.php?id=$row[id]&amp;nome=$row[nome_quem_abriu]' class='text-gray-800 text-hover-primary mb-1'>$row[nome_quem_abriu]</a>";
$tem_midia = $row['nome_midia'] ;
$midia ='';

$data_prevista = $row['data_prevista'] ? (new DateTime($row['data_prevista']))->format('d/m/y H:i') : '';

if($data_prevista!=''){
	$data_prevista_retorno = "Data Prevista: ".$data_prevista;
}else {

	$data_prevista_retorno='';
}


		$data[] = array(
			'id_suporte' =>($id_suporte),
			'midia'		=>verificarMidia($row['nome_midia'],$row['chave_unica'],$row['id_suporte']),
			'status' => getStatusLeitura($row['status_suporte']),
			'projeto' => ($projeto),
			'nucleo' => $row['nome_estacao'] ?? '',
			'nome_suporte' => $row['nome_suporte_ticket'] ?? '',
			'motivo_suporte' => ($row['motivo_suporte'] ?? ''),
			'nome_usuario' => ($nome).' '. (getNivelUsuario($row['nivel_quem_abriu'])),
			'data_open' => $row['data_open'] ? (new DateTime($row['data_open']))->format('d/m/y H:i') : '',
			'data_close' => ($row['data_close'] ? (new DateTime($row['data_close']))->format('d/m/y H:i') : '') . ' ' . $data_prevista_retorno
			
			
		);
	}

	
	
	echo json_encode($data , JSON_PRETTY_PRINT| JSON_UNESCAPED_SLASHES);





  }// se encontrar registro// $count

  if ($total <= 0) {

  

	$retorno = array('codigo' => 0, 'mensagem' => 'Nenhum Ticket encontrado');

	

	echo json_encode($retorno , JSON_PRETTY_PRINT| JSON_UNESCAPED_SLASHES);

	
  }

  $conexao=null;

  exit;

} 

}// fecha tipo_relatorio= tickets via email