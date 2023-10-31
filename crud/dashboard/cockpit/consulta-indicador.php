<?php
require_once '../../../conexao.php';
// Atribui uma conexão PDO

$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();

$id_estacao = trim(isset($_COOKIE['estacao_operador'])) ? $_COOKIE['estacao_operador'] : $_GET['id_estacao'];

$acao = trim(isset($_GET['acao'])) ? $_GET['acao'] : '';


$sql = $conexao->query("SELECT p.nome_ponto, pr.nome_parametro, pr.id_parametro, pr.status_parametro 
FROM parametros_ponto pr
INNER JOIN pontos_estacao p ON pr.id_ponto = p.id_ponto
 WHERE pr.status_parametro='1'

 AND p.id_estacao='$id_estacao' ORDER BY p.nome_ponto ASC");

$sql->execute();
$json_data = $sql->fetchAll(PDO::FETCH_ASSOC);
$count = $sql->rowCount();

//print_r($json_data);



if ($count > 0) {


                $retorna_dados = "";
               
                foreach ($json_data as $value) {

                      


                        $retorna_dados.= '<div class="form-check form-check-custom form-check-solid form-check-lg ">
                                        <input class="form-check-input" type="checkbox" value="'. $value['id_parametro']  . '" name="indicadores_cockpit[]"   onclick="limitarSelecao()" data-nome=" ' . $value['nome_ponto'] . ' > ' . $value['nome_parametro'] . '" />
                                        <label class="form-check-label" for="flexCheckboxSm">
                                            ' . $value['nome_ponto'] . ' > ' . $value['nome_parametro'] . '
                                        </label>
                                        </div>
                                         <div class="separator mb-3 opacity-75"></div>
                                         ';
                }

                echo $retorna_dados;

        

   
} // =====[ Final do Tipo de Consulta para Suporte Pendente ]====<<<<<
else {

        echo '<div class="alert alert-secondary" role="alert">
                            <div class="alert-icon"><i class="flaticon-warning-sign"></i></div>
                            <div class="alert-text">Não foram localizados Indicadores Ativos para o Projeto Selecionado.</div>
                        </div>';
}
