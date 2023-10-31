<?php 	 
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
date_default_timezone_set('America/Sao_Paulo');

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


$id_plcode_atual = trim(isset($_POST['id_plcode_informado'])) ? $_POST['id_plcode_informado'] : '';

$id_periodo_checkin_ponto_selecionado = trim(isset($_POST['id_periodo_checkin_ponto_selecionado'])) ? $_POST['id_periodo_checkin_ponto_selecionado'] : '';
     



// Dica 5 - Válida os dados do usuário com o banco de dados
$sql = "SELECT p.nome_ponto, periodo.*
FROM periodo_ponto periodo
INNER JOIN pontos_estacao p ON p.id_ponto = periodo.id_ponto
WHERE periodo.id_periodo_ponto = '$id_periodo_checkin_ponto_selecionado' GROUP BY periodo.id_periodo_ponto HAVING periodo.id_periodo_ponto='$id_periodo_checkin_ponto_selecionado'";



$stm = $conexao->prepare($sql);


$stm->execute();

$valor = $stm->fetch(PDO::FETCH_ASSOC);

$count = $stm->rowCount();


if($count > 0){


    echo '
    <div class="card card-flush shadow-sm" id="Div_Realiza_Checkin_Presencial">
        <div class="card-header" id="Div_Titulo_Realiza_Checkin_Presencial">
            <h6 class="text-primary f-7" id="Texto_Div_Titulo_CheckIn_Presencial">O <span class="fs-5 text-primary">'.$valor['nome_ponto'].'
                    </span> se
                    encontra em boas condições de Operação?</h6>

                 
                <div class="card-toolbar d-grid gap-3 d-flex justify-content-center" id="div_Botoes_Status_PLCode_Presencial">
                <a href="javascript:;" class="btn btn-success btn-lg p-2 bt_Plcode_Checkin_Presencial_OK " data-bs-toggle="button" autocomplete="off" aria-pressed="true"
                    onclick="Plcode_Checkin_Presencial_OK($(this))" id="bt_Plcode_Checkin_Presencial_OK"
                    data-modo_checkin="'.$valor['modo_checkin_periodo'].'"
                    data-periodo_ponto="'.$id_periodo_checkin_ponto_selecionado.'"
                    data-hora_leitura="'.$valor['hora_leitura'].'" data-plcode="'.$id_plcode_atual.'"
                    data-nome_plcode_checkin_selecionado="'.$valor['nome_ponto'].'">
                    PLCode dentro do Esperado!
                </a>


                <input type="hidden" name="valor_checkin_presencial" id="valor_checkin_presencial" value="" />

                <a href="javascript:;" class="btn btn-danger btn-lg p-2 bt_Plcode_Checkin_Presencial_NOK" data-bs-toggle="button" autocomplete="off" aria-pressed="true"
                    onclick="Plcode_Checkin_Presencial_NOK($(this))" id="bt_Plcode_Checkin_Presencial_NOK"
                    data-modo_checkin="'.$valor['modo_checkin_periodo'].'"
                    data-periodo_ponto="'.$id_periodo_checkin_ponto_selecionado.'"
                    data-hora_leitura="'.$valor['hora_leitura'].'" data-plcode="'.$id_plcode_atual.'"
                    data-nome_plcode_checkin_selecionado="'.$valor['nome_ponto'].'">
                    PLCode com Problemas?!
                </a>
            </div>

            

              <div class="separator my-3"></div>



        <div id="Explica_Checkin_Presencial">

        <!--begin::Card-->
        <div class="card card-bordered">
            <div class="card-header ribbon ribbon-end">
                <div class="ribbon-label bg-primary">Dica</div>
                <div class="card-title">Instrução Presencial</div>
            </div>

            <div class="card-body">
                <p> Caso haja alguma anormalidade, basta Clicar em: <span class="fs-5 text-danger">"PLCode com
                                Problemas"</span>. Será necessário informar o Motivo, para finalizar o Checkin. </p>

                        <p> Será exibida uma área para que você possa expor o Motivo.</p>

                        <p> Não se preocupe, O Suporte será acionado automaticamente.
                            Em breve seu Supervisor ou RO Entrarão em contato. </p>
                    </div>
            </div>
            <!--end::Card-->


         
        </div>


   </div>


<div class="form-floating d-none" id="Motivo_PLCode_Presencial" >
    <textarea class="form-control" placeholder="Descreva o Motivo" id="Texto_Checkin_Motivo_PLcode_Presencial_Alerta" name="Texto_Checkin_Motivo_PLcode_Presencial_Alerta" style="height: 200px"></textarea>
    <label for="floatingTextarea2">Descreva o Motivo...</label>
</div>


 <label class="btn btn-outline btn-outline-dashed btn-outline-default p-7 d-flex align-items-center d-none" id="DIV_abre_modal_imagem_checkin" for="abre_modal_imagem_checkin">
												        <!--begin::Icon-->
                                                <span class="symbol symbol-50px me-4">
                                                    <span class="symbol-label " >

<a href="javascript:;" class="btn btn-icon btn-success me-2"  data-indicador ="'.$id_periodo_checkin_ponto_selecionado.'" data-nome_indicador ="'.$valor['nome_ponto'].'" data-bs-toggle="modal" data-bs-target="#modal_midia_evento"><i class="bi bi-camera  fs-4x "></i></a>
                                                                         
                                                                                </span>
                                                                            </span>
                                                                            <!--end::Icon-->
														<span class="d-block fw-bold text-start">
															<span class="text-dark fw-bolder d-block fs-3">Enviar Mídia?</span>
															<span class="text-muted fw-bold fs-6">Caso seja permitido em sua Estação, por favor, faça o envio de uma foto do PLCode, deste momento.</span>
														</span>
													</label>

<input type="hidden" name="tipo_suporte" value="93">
        
    </div>';




 }

