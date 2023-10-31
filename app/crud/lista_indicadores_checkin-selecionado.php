<?php 	 
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
date_default_timezone_set('America/Sao_Paulo');


     $id_plcode_atual = trim(isset($_POST['id_plcode_informado'])) ? $_POST['id_plcode_informado'] : '';

 $id_periodo_checkin_ponto_selecionado = trim(isset($_POST['id_periodo_checkin_ponto_selecionado'])) ? $_POST['id_periodo_checkin_ponto_selecionado'] : '';
     
//===[ CHAVE ÚNICA da SESSAO] a cada acesso, o step registra uma codificação única de 32bits, encriptografada, de acesso unico para o login do usuário e logout do mesmo.
/* para cada nova leitura, é gerada uma chave_unica_sessao_atual, está é para mapearmos a rota desde a leitura do plcode e o que o usuário fez em sequência, 
checkin, abriu suporte, fez envio normal da leitura do plcode lido, enviou imagens nas leituras ou no suporte e ou reabertura de plcode, a chave_unica_sessao,
vinculará cada rotina do usuário, desde o início da etapa até a sua conclusão e leitura do próximo plcode, onde uma nova chave será gerada para o novo acompanhamento
da nova rotina do plcode lido, que se iniciará.
*/
$horario_completo_agora = microtime();
/* INÍCIO: Crio a Chave unica da Sessao para armazenamento e resgate das leituras e imagens que serão enviadas */          
$chave_unica = bin2hex(random_bytes(33).$horario_completo_agora); 
/* Gerar strings aleatórias criptograficamente seguras, usamos 24 caracteres não repetitivos e aleatórios com criptografia nativa do PHP";
 serve como id referencial para salvar a midia e após salvar as leituras por essa chave que tbem constará na tb rmm, vinculo o id_rmm na tb midia_leitura,
  com a mesma chave unica (para controlar cada leitura enviada individualmente e não misturar as imagens enviadas) assim tbem poderemos vincular as midias 
  enviadas com um novo suporte gerado pelo painel da leitura e ver a relação entre elas, por imagens*/

$cookie_name = "CHAVE_UNICA_SESSAO_ATUAL";
$cookie_value = $chave_unica;
  
// 86400 = 1 day
setcookie($cookie_name, $cookie_value, time() + (86400 * 15), "/"); 


// Dica 5 - Válida os dados do usuário com o banco de dados
$sql = "SELECT p.nome_ponto, periodo.*, pr.*, u.*
FROM periodo_ponto periodo
INNER JOIN pontos_estacao p ON p.id_ponto = periodo.id_ponto
INNER JOIN parametros_ponto pr ON pr.id_parametro = periodo.id_parametro 
INNER JOIN unidade_medida u ON u.id_unidade_medida = pr.unidade_medida
WHERE periodo.id_periodo_ponto = '$id_periodo_checkin_ponto_selecionado' AND periodo.status_periodo='1' GROUP BY periodo.id_periodo_ponto HAVING periodo.id_periodo_ponto='$id_periodo_checkin_ponto_selecionado'";


$stm = $conexao->prepare($sql);


$stm->execute();

$row = $stm->fetch(PDO::FETCH_ASSOC);


if($row){

//===[ CHAVE ÚNICA da SESSAO] a cada acesso, o step registra uma codificação única de 32bits, encriptografada, de acesso unico para o login do usuário e logout do mesmo.
/* para cada nova leitura, é gerada uma chave_unica_sessao_atual, está é para mapearmos a rota desde a leitura do plcode e o que o usuário fez em sequência, 
checkin, abriu suporte, fez envio normal da leitura do plcode lido, enviou imagens nas leituras ou no suporte e ou reabertura de plcode, a chave_unica_sessao,
amarrará a rotina do usuário, desde o início da etapa até a sua ocnclusão e leitura do prõximo plcode, onde uma nova chave é gerada, para outra nova rotina que se iniciará.
*/
$horario_completo_agora = microtime();
/* INÍCIO: Crio a Chave unica da Sessao para armazenamento e resgate das leituras e imagens que serão enviadas */          
$chave_unica = bin2hex(random_bytes(33).$horario_completo_agora); // Gerar strings aleatórias criptograficamente seguras, usamos 24 caracteres não repetitivos e aleatórios com criptografia nativa do PHP"; serve como id referencial para salvar a midia e após saolvar as leituras por essa chave que tbem constará na tb rmm, vinculo o id_rmm na tb midia_leitura, com a mesma chave unica (para controlar cada leitura enviada individualmente e não misturar as imagens enviadas)

$cookie_name = "CHAVE_UNICA_SESSAO_ATUAL";
$cookie_value = $chave_unica;
  
// 86400 = 1 day
setcookie($cookie_name, $cookie_value, time() + (86400 * 15), "/"); 



    

 echo '
 
 <label class="d-flex flex-stack mb-4 cursor-pointer" id="label_'.$row['id_parametro'].'">
                                    <!--begin:Label-->
                                            <span class="d-flex align-items-center me-2">
                                                <!--begin::Icon-->
                                                <div class="col-auto">
                                                <span class="symbol symbol-40px me-4">
                                                    <span class="symbol-label d-none" id="abre_modal_imagem_'.$row['id_parametro'].'">

<a href="javascript:;" class="btn btn-icon btn-success me-2"  data-indicador ="'.$row['id_parametro'].'" data-nome_indicador ="'.$row['nome_parametro'].'" data-bs-toggle="modal" data-bs-target="#modal_midia_evento"><i class="bi bi-camera  fs-4x "></i></a>
                                                                         
                                                                                </span>
                                                                            </span>
                                                                            </div>
                                                                            <!--end::Icon-->
<div class="col-auto">  
                                                                            <!--begin::Description-->
                                                                            <span class="d-flex flex-column">
                                                                                <span
                                                                                    class="fw-bolder text-gray-900 fs-5" id="Texto_Nome_Parametro_Digitado_'.$row['id_parametro'].'">'.$row['nome_parametro'].'</span>
                                                                                <span class="fs-5 fw-bold text-muted">
                                                                                    <!--begin::Input group-->
                                                                                     
                                                                                     <div class="form-floating mb-4 d-flex w-140px ">
                                                                                        <input type="number" 
                                                                                            class="form-control leitura_captada form-control-solid"  id="indicador_'.$row['id_parametro'].'"
                                                                                             data-indicador ="'.$row['id_parametro'].'" data-modo_checkin="'.$row['modo_checkin_periodo'].'"   data-min="'.$row['concen_min'].'" data-max="'.$row['concen_max'].'" data-periodo_ponto="'.$id_periodo_checkin_ponto_selecionado.'" data-tipo_checkin="'.$row['tipo_checkin'].'"
                                                                                           onkeyup="Valida_Indicador($(this))" data-hora_leitura="'.$row['hora_leitura'].'" name="'.$row['id_parametro'].'" data-nome_indicador ="'.$row['nome_parametro'].'" data-origem_parametro = "'.$row['origem_leitura_parametro'].'" data-plcode="'.$id_plcode_atual.'" data-nome_plcode_checkin_selecionado="'.$row['nome_ponto'].'" value=""/>
                                                                                        <label
                                                                                            for="floatingInput">'.$row['concen_min'].' até '.$row['concen_max'].'
                                                                                            </label>
                                                                                    </div>
                                                                                    </div>
                                                                                     <div class="col-auto">
                                                                                    <!--end::Input group-->
                                                                                       <div class=" m-1  mt-n1 form-check form-check-custom form-check-solid form-check-sm">
                                                                                        <input class="form-check-input" type="radio" value="" data-id="'.$row['id_parametro'].'" id="'.$row['id_parametro'].'" onclick="Revoga_Indicador($(this))" data-nome_indicador ="'.$row['nome_parametro'].'"  data-plcode="'.$id_plcode_atual.'" />
                                                                                        </div>
                                                                                    </div>
                                                                                </span>
                                                                            </span>
                                                                            <!--end:Description-->

                                                                        </span>
                                                                        <!--end:Label-->


                                                                    </label>
                                                        
                                                                    <!--end::Option- Dinâmico-->';
       

                                                                         
$conexao=null;
exit;


 }

