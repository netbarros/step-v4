<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$_SESSION['pagina_atual'] = 'Dashboard Usuários';

$usuario_sessao = isset($_SESSION['id']) ?? '';
$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';

$projeto_atual = (isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';

if ($projeto_atual != '') {


    $filtro = "AND o.id_obra ='$projeto_atual ' GROUP BY p.id_ponto";
} else {

    $filtro = "GROUP BY e.id_estacao";
}

$sql_personalizado = '';


$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';
if ($nivel_acesso_user_sessao == 'supervisor') {

    $sql_personalizado = "AND (e.supervisor = '$id_BD_Colaborador'  OR up.id_obra  = 'o.id_obra')";
}

if ($nivel_acesso_user_sessao == 'ro') {

    $sql_personalizado = "AND (e.ro = '$id_BD_Colaborador'  OR up.id_usuario  = '$id_usuario_sessao')";
}

if ($nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado = "AND (o.id_cliente = '$id_tabela_cliente_sessao'  OR up.id_usuario  = '$id_usuario_sessao')";
}


/*
salvo e resgato os itens no coockie para poder personalisar os graficos e informações espscíficas do cliente.

*/
// Data consulta global do dashboard //
$Data_Atual_Periodo = date_create()->format('Y-m-d ');
// == a data está pegando desde 2019, assim que o módulo for liberado, alterar a data de invervalo para -7 dias entre -14 dias
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-24 hours', strtotime($Data_Atual_Periodo)));

$Data_7_dias_antes = date('Y-m-d ', strtotime('-7 days', strtotime($Data_Atual_Periodo)));
$Data_14_dias_antes = date('Y-m-d', strtotime('-14 days', strtotime($Data_Atual_Periodo)));


function intervalo($entrada, $saida)
{
    $entrada = explode(':', $entrada);
    $saida   = explode(':', $saida);
    $minutos = ($saida[0] - $entrada[0]) * 60 + $saida[1] - $entrada[1];
    if ($minutos < 0) $minutos += 24 * 60;
    return sprintf('%d:%d', $minutos / 60, $minutos % 60);
}

function mintohora($minutos)
{
    $hora = floor($minutos / 60);
    $resto = $minutos % 60;
    return $hora . ':' . $resto;
}

$consulta_checkin = "SELECT periodo_ponto.*,
p.nome_ponto,
o.nome_obra,
o.id_obra, 
e.nome_estacao,
e.status_estacao, 
periodo_ponto.id_ponto, 
periodo_ponto.tipo_checkin,
p.controla_periodo_ponto,
p.status_ponto,
parametros_ponto.id_parametro,
parametros_ponto.nome_parametro,
periodo_dia_ponto.dia_semana,
dia_semana.nome_dia_semana, 
checkin.data_cadastro_checkin 
FROM periodo_ponto 
INNER JOIN pontos_estacao p ON p.id_ponto = periodo_ponto.id_ponto 
INNER JOIN obras o ON o.id_obra = p.id_obra
INNER JOIN estacoes e ON e.id_estacao = periodo_ponto.id_estacao
LEFT JOIN checkin ON checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto AND DATE_FORMAT(checkin.data_cadastro_checkin, '%Y-%m-%d')  >= :Data_Atual_Periodo
LEFT JOIN periodo_dia_ponto ON periodo_dia_ponto.id_periodo_ponto = periodo_ponto.id_periodo_ponto 
LEFT JOIN dia_semana ON dia_semana.representa_php=periodo_dia_ponto.dia_semana 
LEFT JOIN parametros_ponto on parametros_ponto.id_parametro =periodo_ponto.id_parametro 
LEFT JOIN usuarios_projeto up ON up.id_obra = o.id_obra
WHERE checkin.id_periodo_ponto IS NULL AND o.status_cadastro='1'
$sql_personalizado
$filtro  
ORDER BY periodo_ponto.hora_leitura ASC 
LIMIT 0,10";

$sql_periodo = $conexao->prepare($consulta_checkin);
$sql_periodo->bindParam(':Data_Atual_Periodo', $Data_Intervalo_Periodo);
$sql_periodo->execute();



$total = $sql_periodo->rowCount();

if ($total > 0) {


                                            $tabela = '';

                                            while ($res = $sql_periodo->fetch(PDO::FETCH_ASSOC)) {

                                                $ciclo_leitura = $res['ciclo_leitura'];

                                                $dias_semana_periodo_ = "";

                                                $nome_dia_semana_periodo = "";



                                                $diasemana_numero = date('w', time());

                                                if ($ciclo_leitura == '1') {
                                                    $ciclo = "diário";
                                                    $hoje_tem = "sim";
                                                    $dias_semana_periodo = "";
                                                } else {
                                                    $ciclo = "semanal";
                                                    $dias_semana_periodo = "<b>Dias:</b> ";
                                                }




                                                if ($ciclo_leitura == "2") {

                                                    $id_par_busca = $res['id_periodo_ponto'];


                                                    $consulta = $conexao->query("SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana FROM periodo_dia_ponto
                INNER JOIN dia_semana ON periodo_dia_ponto.dia_semana = dia_semana.representa_php WHERE periodo_dia_ponto.id_periodo_ponto ='$id_par_busca' AND periodo_dia_ponto.dia_semana='$diasemana_numero'");
                                                    $json_data = $consulta->fetchAll(PDO::FETCH_ASSOC);


                                                    if ($json_data) {


                                                        foreach ($json_data as $item) {

                                                            $dias_semana_periodo .= $item['representa_php'] . ' ';

                                                            $nome_dia_semana_periodo .=  "<span class='kt-badge kt-badge--inline kt-badge--success'>" . $item['nome_dia_semana'] . "</span>";

                                                            $hoje_tem = "sim";
                                                        }
                                                    } else {
                                                        $nome_dia_semana_periodo = "<span class='kt-badge kt-badge--inline kt-shape-bg-color-2'>Hoje Não</span>";
                                                        $hoje_tem = "nao";
                                                    }
                                                }

                                                $controla_periodo = $res['modo_checkin_periodo'];

                                                
                                                    if ($ciclo_leitura == '1') {
                                                        $ciclo = "diário";
                                                        $hoje_tem = "sim";
                                                    } else {
                                                        $ciclo = "semanal";
                                                         $hoje_tem = "nao";
                                                    }



                                                if ($controla_periodo == "1") { // sem controle de horario





                                                    $ciclo_leitura = $res['ciclo_leitura'];

                                                    if ($ciclo_leitura == '1') {
                                                        $ciclo = "diário";
                                                        $hoje_tem = "sim";
                                                    } else {
                                                        $ciclo = "semanal";
                                                         $hoje_tem = "nao";
                                                    }


                                                    if ($hoje_tem == "sim") {

                                                        '<!--begin::Item-->
                                    <div class="d-flex align-items-center mb-8">
                                        <!--begin::Bullet-->
                                        <span class="bullet bullet-vertical h-40px bg-success"></span>
                                        <!--end::Bullet-->
                                        <!--begin::Checkbox-->
                                        <div class="form-check form-check-custom form-check-solid mx-5">
                                        <a href="javascript:;" target="_blank" class="text-info text-hover-success fw-bold fs-4"> <span class="text-info"> ' . $nome_dia_semana_periodo . '</a>
                                        </div>
                                        <!--end::Checkbox-->
                                        <!--begin::Description-->
                                        <div class="flex-grow-1">
                                            <a href="javascript:;" class="text-info text-hover-success fw-bold fs-4"> <span class="text-info">' . $res['nome_obra'] .
                                                            ' ' . $res['nome_estacao'] . '</span> <br>
                                                         ' . $res['nome_ponto'] . '  ' . $res['nome_parametro'] . '</a>
                                        
                                          
                                     
                                            <span class="text-muted fw-semibold d-block">' . $nome_dia_semana_periodo . '</span>
                                        </div>
                                        <!--end::Description-->
                                        <span class="badge badge-light-success fs-8 fw-bold">' . $ciclo . '</span>
                                       
                                    </div>
                                    <!--end:Item--> 
                                    ';
                                                    }
                                                }


                                                if ($controla_periodo == "2") { // com controle de horario

                                                    $hora_leitura = $res['hora_leitura'];

                                                   // Pega a hora, minuto e segundo de $hora_leitura
                                                        list($hour, $minute, $second) = explode(':', $hora_leitura);

                                                        // Cria um objeto DateTime para "agora"
                                                        $now = new DateTime('now');

                                                        // Clona o objeto para criar outros pontos no tempo
                                                        $leitura = clone $now;
                                                        $minima = clone $now;

                                                        // Ajusta a hora, minuto e segundo de $leitura e $minima
                                                        $leitura->setTime($hour, $minute, $second);
                                                        $minima->setTime($hour, $minute, $second)->sub(new DateInterval('PT1M'));  // subtrai 1 minuto


                                                    if ($now <= $minima || $now < $leitura) {
                                                        $status = "expirando";
                                                        $css_status = "warning";
                                                    }
                                                    if ($now < $leitura  || $minima < $now) {
                                                        $status = "em curso";
                                                        $css_status = "primary";
                                                    }

                                                    if ($now > $leitura) {
                                                        $status = "expirado";
                                                        $css_status = "danger";
                                                    }





                                                    $hora_atual = date('H:i');

                                                    $saida =  substr($hora_leitura, 0, 5);
                                                    $entrada   = substr($hora_atual, 0, 5);

                                                    $prazo = intervalo($entrada, $saida);



                                                    if ($hoje_tem == "sim") {

                                                        $tabela .=
                                                            '<!--begin::Item-->
                                    <div class="d-flex align-items-center mb-8">
                                        <!--begin::Bullet-->
                                        <span class="bullet bullet-vertical h-40px bg-' . $css_status . '"></span>
                                        <!--end::Bullet-->
                                        <!--begin::Checkbox-->
                                        <div class="timeline-label fw-bold text-gray-800 fs-6 mx-5 gx-3">
                                        <a href="http://lotus/views/projetos/tarefas/tarefas.php?id='.$res['id_obra'].'" target="_blank" class="text-info text-hover-success fw-bold fs-4"> <span class="text-info">   ' . $saida . '</a>
                                        
                                        </div>
                                        <!--end::Checkbox-->
                                        <!--begin::Description-->
                                        <div class="flex-grow-1">
                                            <a href="javascript:;" class="text-gray-600 text-hover-' . $css_status . ' fw-bold fs-4"><span class="text-success">' . $res['nome_obra'] . ' </span>  ' . $res['nome_estacao'] . ' <br> ' . $res['nome_ponto'] . ' - ' . $res['nome_parametro'] . '</a>
                                            <span class="text-muted fw-semibold d-block"> <span class="badge badge-light-dark fs-6 fw-bold">' . $ciclo . '</span> Próximo em ' . $prazo . ' Horas </span>
                                           
                                        </div>


                                        <!--end::Description-->
                                        <span class="badge badge-light-' . $css_status . ' fs-8 fw-bold">' . $status . '</span>
                                    </div>
                                    <!--end:Item--> 
                                    ';
                                                    }
                                                }
                                            }



                                            echo $tabela;

                                           
                                        } else {

                                            $retorno =  '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-primary">Agendamento de Checkin</h4>
														<span>Não há Checkins agendados para o dia de hoje.</span>
													</div>
												</div>';

                                                echo $retorno;
                                               

                                                
                                        }
                                        
$conexao=null;                                   
?>



