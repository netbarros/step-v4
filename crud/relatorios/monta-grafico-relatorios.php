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

    $sql_personalizado = "AND (up.id_usuario  = '$id_usuario_sessao')";
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

if ($count > 0) {
    $data = [];

    $getStatusLeitura = function($status) {
        // Sua lógica aqui
        return $status;
    };

    $getNivelUsuario = function($nivel) {
        // Sua lógica aqui
        return $nivel;
    };

    while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
        $nome = $row['nome_usuario'] ?: "Sensor" . $row['nome_parametro'];
        $projeto = $row['nome_obra'];

        $data[] = [
            'data' => $row['data_leitura'] ? (new DateTime($row['data_leitura']))->format('d/m/y H:i') : '',
            'status' => $getStatusLeitura($row['status_leitura'] ?? ''),
            'nome_usuario' => $nome . ' ' . $getNivelUsuario($row['nivel'] ?? ''),
            'indicador' => $row['nome_parametro'] ?? '',
            'parametros_min' => $row['concen_min'] ?? 'n/a',
            'parametros_max' => $row['concen_max'] ?? 'n/a',
            'leitura' => ($row['leitura_entrada'] ?? $row['leitura_saida']) . ' ' . ($row['nome_unidade_medida'] ?? '0'),
            'projeto' => $projeto,
            'nucleo' => $row['nome_estacao'] ?? '',
            'plcode' => $row['nome_ponto'] ?? '',
            'caracteristica' => $row['nome_tipo'] ?? ''
        ];
    }

    echo json_encode($data, JSON_UNESCAPED_SLASHES);
} else {
    $retorno = ['codigo' => 0, 'mensagem' => 'Nenhum registro encontrado!'];
    echo json_encode($retorno, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

}
?>