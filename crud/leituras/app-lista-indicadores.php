<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('America/Sao_Paulo');

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/conexao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/valida-acesso-app.php';

$conexao = Conexao::getInstance();
//validateHeader();
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}


$id_plcode_atual = isset($_POST['id_plcode_informado']) ? trim($_POST['id_plcode_informado']) : '';
$id_usuario = isset($_POST['id_usuario']) ? trim($_POST['id_usuario']) : '';


// Verifique se as variáveis estão vazias
if (empty($id_plcode_atual) || empty($id_usuario)) {
    // Crie um array com a mensagem de erro
    $response = [
        'success' => false,
        'message' => 'Os campos plcode e id_usuario são obrigatórios'
    ];
    
    // Codifique o array em JSON e envie a resposta
    echo json_encode($response);
    exit;
}


//  Válida os dados dos indicadores
$sql = "SELECT 
pr.id_parametro,
p.id_ponto,
pr.nome_parametro,
u.nome_unidade_medida,
pr.concen_min,
pr.concen_max,
pr.controle_concentracao,
pr.status_parametro,
s.tipo_suporte,
s.id_suporte,
s.status_suporte
FROM parametros_ponto pr
INNER JOIN pontos_estacao p ON p.id_ponto = pr.id_ponto 
INNER JOIN unidade_medida u ON u.id_unidade_medida = pr.unidade_medida
LEFT JOIN periodo_ponto periodo ON periodo.id_parametro = pr.id_parametro 
LEFT JOIN suporte s ON s.parametro = pr.id_parametro 
WHERE pr.id_ponto = :id_ponto AND periodo.id_parametro IS NULL
AND pr.status_parametro != '3' 
AND (s.status_suporte != 4 OR s.status_suporte IS NULL)
GROUP BY pr.id_parametro 
ORDER BY pr.nome_parametro ASC;
";

$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id_ponto', $id_plcode_atual, PDO::PARAM_INT);
$stmt->execute();

$count = $stmt->rowCount();


if($count > 0){

    $json_data = $stmt->fetchAll(PDO::FETCH_ASSOC);


    echo json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);



$conexao=null;
exit;

 } else {


    echo json_encode('dados não localizados', JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    $conexao=null;
    exit;



 }



     ?>

                                          