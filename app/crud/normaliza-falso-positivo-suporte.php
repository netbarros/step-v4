<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header("Access-Control-Max-Age: 3600");
ini_set('memory_limit', '-1');

require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';

if (!ini_get('date.timezone')) {
    date_default_timezone_set('America/Sao_Paulo');
}

// Atribui uma conexão PDO

$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();



// normaliza leituras de controle só mínima que existam em suporte

$sql_min = $conexao->query("
SELECT s.id_suporte, s.id_rmm_suporte, s.leitura_suporte, s.data_open, pr.nome_parametro, pr.controle_concentracao, pr.concen_min, pr.concen_max ,r.leitura_entrada, s.status_suporte, r.status_leitura FROM suporte s 
INNER JOIN rmm r ON r.id_rmm = s.id_rmm_suporte
INNER JOIN parametros_ponto pr ON pr.id_parametro = s.parametro WHERE r.status_leitura='1';");

$total_mim = $sql_min->rowCount();

if ($total_mim > 0) {

    $resultado = $sql_min->fetchAll(PDO::FETCH_ASSOC);


    foreach ($resultado as $res) {

        $id_suporte = $res['id_suporte'];
        

        $sql_normaliza = $conexao->query("DELETE FROM suporte WHERE id_suporte = '$id_suporte'");
    }

    echo "Itens Normalizados: " . $total_mim;
}

