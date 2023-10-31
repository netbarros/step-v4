<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

$_SESSION['pagina_atual'] = 'Relatório de Clientes';

$tipo_relatorio = trim(isset($_POST['tipo_relatorio'])) ? $_POST['tipo_relatorio'] : '';

$Periodo_Inicial = trim(isset($_POST['Periodo_Inicial'])) ? $_POST['Periodo_Inicial'] : '';
$Periodo_Final = trim(isset($_POST['Periodo_Final'])) ? $_POST['Periodo_Final'] : '';

$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';

$projeto_atual = trim(isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';


$sql_personalizado = '';


$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';
if ($nivel_acesso_user_sessao == 'supervisor') {

    $sql_personalizado1= "WHERE e.supervisor = '$id_BD_Colaborador' OR up.id_usuario  = '$id_usuario_sessao' AND";
}

if ($nivel_acesso_user_sessao == 'ro') {

    $sql_personalizado1 = "WHERE e.ro = '$id_BD_Colaborador' OR up.id_usuario  = '$id_usuario_sessao' AND";
}

if ($nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado1 = "WHERE o.id_cliente = '$id_tabela_cliente_sessao' OR up.id_usuario  = '$id_usuario_sessao' AND";
}

if($nivel_acesso_user_sessao=="admin"){

	$sql_personalizado1 = 'WHERE ';
}

if($tipo_relatorio=='listagem-clientes'){


$sql = "SELECT 	c.*, c.data_cadastro as datacadastro_cliente FROM clientes c
INNER JOIN obras o ON o.id_obra = c.id_cliente
INNER JOIN estacoes e ON e.id_obra = o.id_obra
LEFT JOIN usuarios_projeto up ON up.id_usuario = $id_usuario_sessao
$sql_personalizado1
DATE_FORMAT(c.data_cadastro, '%Y-%m-%d') BETWEEN '$Periodo_Inicial' AND '$Periodo_Final'  GROUP BY c.id_cliente";
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
				return '<span class="badge badge-exclusive badge-light-success fw-bold fs-9 px-2 py-1 ms-1">Ativo</span>';
				break;
			case 3:
				return '<span class="badge badge-exclusive badge-light-dark fw-bold fs-9 px-2 py-1 ms-1">Inativo</span>';
				break;
			case 2:
				return '<span class="badge badge-exclusive badge-light-warning fw-bold fs-9 px-2 py-1 ms-1">Pendente</span>';
				break;
			default:
				return $status_leitura;
				break;
		}
	}


	function mask($val, $mask)
	{
		$maskared = '';
		$k = 0;
		for ($i = 0; $i <= strlen($mask) - 1; ++$i) {
			if ($mask[$i] == '#') {
				if (isset($val[$k])) {
					$maskared .= $val[$k++];
				}
			} else {
				if (isset($mask[$i])) {
					$maskared .= $mask[$i];
				}
			}
		}
	
		return $maskared;
	}
	
	
	/* 
	echo mask($cnpj, '##.###.###/####-##').'<br>';
	echo mask($cpf, '###.###.###-##').'<br>';
	echo mask($cep, '#####-###').'<br>';
	echo mask($data, '##/##/####').'<br>';
	echo mask($data, '##/##/####').'<br>';
	echo mask($data, '[##][##][####]').'<br>';
	echo mask($data, '(##)(##)(####)').'<br>';
	echo mask($hora, 'Agora são ## horas ## minutos e ## segundos').'<br>';
	echo mask($hora, '##:##:##'); */

	

	while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {

		$cnpj_entrada = $row['cnpj'] ?? '';
		$cnpj_saida = mask($cnpj_entrada, '##.###.###/####-##');

	
//'midia' => $row['nome_midia'] ?? '',

$id_cliente = "<a  href='javascript:;' class='text-gray-800 text-hover-primary mb-1' data-bs-toggle='modal' data-id='$row[id_cliente]' data-bs-target='#kt_modal_edita_cliente'>$row[id_cliente]</a>";

		$data[] = array(
			'id' => $id_cliente,
			'data' => $row['datacadastro_cliente'] ? (new DateTime($row['datacadastro_cliente']))->format('d/m/y') : '',
			'cnpj' => ($cnpj_saida),
			'razao_social' => $row['razao_social'] ?? '',
			'nome_fantasia' => $row['nome_fantasia'] ?? '',
			'email_geral' => $row['email_geral'] ?? '',
			'telefone' => $row['telefone'] ?? '',
			'status' => getStatusLeitura($row['status_cadastro'])
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