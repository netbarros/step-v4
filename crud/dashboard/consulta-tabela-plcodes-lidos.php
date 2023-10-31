<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();

if (!isset($_SESSION)) session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$_SESSION['pagina_atual'] = 'Dashboard Usuários';

// Inicialização de variáveis
$idUsuarioSessao = trim($_COOKIE['id_usuario_sessao'] ?? '');
$tipoUserSessao = $_COOKIE['nivel_acesso_usuario'] ?? '';
$idBdColaborador = trim($_SESSION['bd_id'] ?? '');

// Geração de datas
$dataAtualPeriodo = new DateTime();
$data7DiasAntes = $dataAtualPeriodo->modify('-7 days')->format('Y-m-d');

// Preparação da consulta SQL base
$baseSql = "
    SELECT COUNT(r.id_rmm) as total_plcode, p.nome_ponto, o.nome_obra, e.nome_estacao, pr.nome_parametro, p.id_ponto, pr.nome_parametro
    FROM rmm r
    INNER JOIN pontos_estacao p ON p.id_ponto = r.id_ponto 
    INNER JOIN obras o ON o.id_obra = p.id_obra
    INNER JOIN estacoes e ON e.id_estacao = p.id_estacao
    INNER JOIN parametros_ponto pr ON pr.id_parametro = r.id_parametro
    LEFT JOIN usuarios_projeto up ON up.id_obra = o.id_obra
    WHERE r.data_leitura >= :data7DiasAntes
";

// Determinação da condição de tipo de usuário
$typeCondition = "";
$parameters = [':data7DiasAntes' => $data7DiasAntes];

switch ($tipoUserSessao) {
    case 'supervisor':
        $typeCondition = "AND (e.supervisor = :idBdColaborador OR up.id_usuario = :idUsuarioSessao)";
        $parameters[':idUsuarioSessao'] = $idUsuarioSessao;
        $parameters[':idBdColaborador'] = $idBdColaborador;
        break;
    case 'ro':
        $typeCondition = "AND (e.ro = :idBdColaborador OR up.id_usuario = :idUsuarioSessao)";
        $parameters[':idUsuarioSessao'] = $idUsuarioSessao;
        $parameters[':idBdColaborador'] = $idBdColaborador;
        break;
    case 'cliente':
        $typeCondition = "AND (up.id_usuario = :idUsuarioSessao)";
        $parameters[':idUsuarioSessao'] = $idUsuarioSessao;
        break;
    case 'admin':
        break;
}

// Completando a consulta SQL e executando-a
$sql = $baseSql . $typeCondition . " GROUP BY r.id_ponto ORDER BY total_plcode DESC LIMIT 0,10";
$stmt = $conexao->prepare($sql);

foreach ($parameters as $key => $value) {
    $stmt->bindValue($key, $value);
}

if ($stmt->execute()) {
    $contaPlcodeLido = $stmt->rowCount();
} else {
    echo "Erro na execução da consulta: ";
    print_r($stmt->errorInfo());
}

if ($contaPlcodeLido > 0) {
$lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
$dados = array();

foreach($lista as $row){
    $total_plcode = number_format($row['total_plcode'], 2, '.', '');


    $dados[] = array(
        'nome_obra' => ($row['nome_obra']). ': ' .($row['nome_ponto']).' [Indicador: '.($row['nome_parametro']).']',
        'nome_estacao' => ($row['nome_estacao']),
        'nome_ponto' => ($row['nome_ponto']).' '.($row['nome_parametro']),
        'id_ponto' => ($row['id_ponto']),
        'nome_parametro' => ($row['nome_parametro']),
        'total_plcode' => $total_plcode
    );
}

echo json_encode($dados, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
$conexao = null;
} else {
$dados[] = array(
    'sem_dados' => 0,
);
echo json_encode($dados, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
}

// Close connection regardless of whether or not there are data to process
$conexao = null;
?>
