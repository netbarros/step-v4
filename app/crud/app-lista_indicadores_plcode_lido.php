<?php
// Definição de cabeçalhos
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Importação do arquivo de conexão
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';

// Verificar se a sessão está ativa, senão inicia a sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configurar o fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Verificação e atribuição das variáveis
$id_plcode_atual = filter_input(INPUT_POST, 'plcode', FILTER_SANITIZE_STRING);
$id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_SANITIZE_STRING);

// Query SQL
$sql = "SELECT pr.*, 
        u.nome_unidade_medida,
        p.nome_ponto, 
        p.id_ponto,
        periodo.id_periodo_ponto,
        periodo.id_ponto as plcode_periodo,
        periodo.id_parametro as parametro_periodo,
        periodo.tipo_checkin,
        periodo.hora_leitura,
        periodo.ciclo_leitura,
        periodo.status_periodo,
        periodo.modo_checkin_periodo,
        periodo_dia_ponto.dia_semana,
        dia_semana.nome_dia_semana
FROM pontos_estacao p
LEFT JOIN parametros_ponto pr ON pr.id_ponto = p.id_ponto 
LEFT JOIN unidade_medida u ON u.id_unidade_medida = pr.unidade_medida
LEFT JOIN periodo_ponto periodo ON periodo.id_parametro=  pr.id_parametro 
LEFT JOIN periodo_dia_ponto ON periodo.id_periodo_ponto = periodo_dia_ponto.id_periodo_ponto
LEFT JOIN dia_semana ON dia_semana.representa_php=periodo_dia_ponto.dia_semana
WHERE p.id_ponto = :id_plcode_atual AND pr.status_parametro != '3' 
GROUP BY pr.id_parametro 
ORDER BY pr.status_parametro = '1' DESC";

// Preparar a consulta
$stm = $conexao->prepare($sql);

// Substituir o valor de :id_plcode_atual no SQL com o valor real usando bindParam
$stm->bindParam(':id_plcode_atual', $id_plcode_atual, PDO::PARAM_STR);

// Executar a consulta
$stm->execute();

// Buscar todos os resultados
$json_data = $stm->fetchAll(PDO::FETCH_ASSOC);

// Contar os resultados
$count = $stm->rowCount();

if($count > 0) {
    // Se há resultados, retorna os dados em formato JSON
    echo json_encode($json_data);
} else {
    // Caso contrário, envia uma mensagem de erro personalizada
    // Note que a mensagem de erro foi removida do PHP e colocada em um arquivo separado de HTML para melhor manutenção
    include('error_message.html');
}

// Fechar a conexão
$conexao = null;
?>
