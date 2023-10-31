<?php

require_once '../../../conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();	
include_once "../../../_jquery/class-list-util.php";
// fazer consulta para exibir somente os pontos dos clientes que o colaborador é responsavel, no cadastro da estação do cliente:



$id_plcode_temp =  trim(isset($_COOKIE['id_plcode_temp'])) ? $_COOKIE['id_plcode_temp'] : '';


$complemento_sql="";
if($id_plcode_temp!='undefined' || $id_plcode_temp!=null || $id_plcode_temp==""){
$complemento_sql = "WHERE id_ponto='$id_plcode_temp'";
} else { echo json_encode("não ha id do plcode criado, no cookie", true);}



$sql = "SELECT * FROM parametros_ponto p
LEFT JOIN unidade_medida u ON u.id_unidade_medida = p.unidade_medida $complemento_sql ";


$stm = $conexao->prepare($sql);


$stm->execute();
$json_data = $stm->fetchAll(PDO::FETCH_ASSOC);

$count = $stm->rowCount();

//var_dump($json_data);
//array_push($json_data, "acoes");

$retorno = json_encode($json_data);


// get all raw data
$data = json_decode( $retorno, true );

$datatable = array_merge(array('pagination' => array(), 'sort' => array(), 'query' => array()), $_REQUEST);

// search filter by keywords
$filter = isset($datatable['query']['generalSearch']) && is_string($datatable['query']['generalSearch']) ? $datatable['query']['generalSearch'] : '';
if (!empty($filter)) {
    $data = array_filter($data, function ($a) use ($filter) {
        return (boolean)preg_grep("/$filter/i", (array)$a);
    });
    unset($datatable['query']['generalSearch']);
}

// filter by field query
$query = isset($datatable['query']) && is_array($datatable['query']) ? $datatable['query'] : null;
if (is_array($query)) {
    $query = array_filter($query);
    foreach ($query as $key => $val) {
        $data = list_filter($data, array($key => $val));
    }
}

$sort = !empty($datatable['sort']['sort']) ? $datatable['sort']['sort'] : 'asc';
$field = !empty($datatable['sort']['field']) ? $datatable['sort']['field'] : 'id_tipo_suporte';

$meta = array();
$page = !empty($datatable['pagination']['page']) ? (int)$datatable['pagination']['page'] : 1;
$perpage = !empty($datatable['pagination']['perpage']) ? (int)$datatable['pagination']['perpage'] : -1;

$pages = 1;
$total = count($data); // total items in array

// sort
usort($data, function ($a, $b) use ($sort, $field) {
    if (!isset($a->$field) || !isset($b->$field)) {
        return false;
    }

    if ($sort === 'asc') {
        return $a->$field > $b->$field ? true : false;
    }

    return $a->$field < $b->$field ? true : false;
});

// $perpage 0; get all data
if ($perpage > 0) {
    $pages = ceil($total / $perpage); // calculate total pages
    $page = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
    $page = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
    $offset = ($page - 1) * $perpage;
    if ($offset < 0) {
        $offset = 0;
    }

    $data = array_slice($data, $offset, $perpage, true);
}

$meta = array(
    'page' => $page,
    'pages' => $pages,
    'perpage' => $perpage,
    'total' => $total,
);

// if selected all records enabled, provide all the ids
if (isset($datatable['requestIds']) && filter_var($datatable['requestIds'], FILTER_VALIDATE_BOOLEAN)) {
    $meta['rowIds'] = array_map(function ($row) {
        foreach ($row as $first) break;
        return $first;
    }, $alldata);
}


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');

$result = array(
    'meta' => $meta + array(
            'sort' => $sort,
            'field' => $field,
        ),
    'data' => $data
);

echo json_encode($result, JSON_PRETTY_PRINT);