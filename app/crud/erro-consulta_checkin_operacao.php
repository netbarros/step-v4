<?php 
date_default_timezone_set('America/Sao_Paulo');

// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();

$estacao_atual = trim(isset($_COOKIE['estacao_atual'])) ? $_COOKIE['estacao_atual'] : '';

$id_plcode_atual    = trim(isset($_COOKIE['plcode_lido'])) ? $_COOKIE['plcode_lido'] : '';


// pega hora atual php
$hora_atual = date('H:i');

function intervalo( $entrada, $saida ) {
    $entrada = explode( ':', $entrada );
    $saida   = explode( ':', $saida );
    $minutos = ( $saida[0] - $entrada[0] ) * 60 + $saida[1] - $entrada[1];
    if( $minutos < 0 ) $minutos += 24 * 60;
    return sprintf( '%d:%d', $minutos / 60, $minutos % 60 );
 }

$CHAVE_UNICA_SESSAO_ATUAL = $_COOKIE["CHAVE_UNICA_SESSAO_ATUAL"];

if($CHAVE_UNICA_SESSAO_ATUAL!=""){


// Trago todos os checkins agendados na Tabela Periodo Ponto (Agendamento de Checkin) depois no select seguint, filtro por semana ou diario
// periodo_ponto.$id_plcode_atual

$data_Leitura_Hoje= date_create()->format('d/m/Y H:m');
$data_atual_periodo= date_create()->format('Y-m-d H:m:s');
$data_intervalo_periodo=date('Y-m-d', strtotime('-7 days', strtotime($data_atual_periodo)));

   //
/* $sql_quantidade_checkin_agendado = $conexao->query("SELECT  COUNT(DISTINCT periodo_ponto.id_periodo_ponto) AS conta_id_periodo_ponto
    FROM periodo_ponto WHERE periodo_ponto.id_estacao = $estacao_atual");


  $conta_id_periodo_ponto = $sql_quantidade_checkin_agendado->rowCount ();

if($total_checkin_agendado==$conta_id_periodo_ponto){
break;
} */

    $sql_checkin_agendado = $conexao->query("SELECT periodo_ponto.*,
    pontos_estacao.nome_ponto,
    pontos_estacao.id_ponto,
    periodo_ponto.tipo_checkin,
    pontos_estacao.controla_periodo_ponto,
    pontos_estacao.status_ponto,
    parametros_ponto.id_parametro,
    parametros_ponto.nome_parametro,
    periodo_dia_ponto.dia_semana,
    dia_semana.nome_dia_semana,
    checkin.data_cadastro_checkin FROM periodo_ponto 

    INNER JOIN pontos_estacao ON pontos_estacao.id_ponto = periodo_ponto.id_ponto 
    LEFT JOIN checkin ON checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto 
    LEFT JOIN periodo_dia_ponto ON periodo_dia_ponto.id_periodo_ponto = periodo_ponto.id_periodo_ponto 
    LEFT JOIN dia_semana ON dia_semana.representa_php=periodo_dia_ponto.dia_semana 
    LEFT JOIN parametros_ponto on parametros_ponto.id_parametro =periodo_ponto.id_parametro 

    Where periodo_ponto.id_periodo_ponto not in  ( SELECT  checkin.id_periodo_ponto FROM  checkin WHERE checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto AND DATE_FORMAT(checkin.data_cadastro_checkin, '%Y-%m-%d')  > '$data_intervalo_periodo') 
    AND periodo_ponto.id_estacao ='$estacao_atual'   GROUP BY periodo_ponto.id_periodo_ponto ORDER BY periodo_ponto.hora_leitura ASC"   );
    
    
    //===> exceto se constar o id_periodo_ponto na tabela checkin, este não traz, pq já foi feito o check
    
    
     
    
    $total_checkin_agendado = $sql_checkin_agendado->rowCount ();
    
       //var_dump($sql_checkin_agendado);


if($total_checkin_agendado>0){

           

$inicio_div_tabela = '<div data-kt-buttons="true">';

$fim_div_tabela = '</div>';


/* Informações de Alertas de acordo com controle ou não de horários pré-definidos:

$div_alerta_agenda_controlada = '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-primary">Programação agendada com Horário Controlado</h4>
														<span>Por favor, verifique os agendamentos e programe-se para cumprir os horários pré-determinados do check-in.</span>
													</div>
												</div>';



$div_alerta_agenda_livre = '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-primary">Programação agendada com Horário Livre</h4>
														<span>Por favor, fique atento às Leituras de todos os PLCodes, conforme sua rotina e Instrução Operacional da Estação.</span>
													</div>
												</div>';
*/


//====>> Informações globais de qualquer tipo de checkin quye precisam ser recuperadas a baixo dentro dos foreach's que estão dentro do laçc do While:


        while ($res = $sql_checkin_agendado->fetch(PDO::FETCH_ASSOC)) {

$nome_dia_semana_periodo = "";


$id_Periodo_Checkin_Ponto =  trim(isset($res['id_periodo_ponto'])) ? $res['id_periodo_ponto'] : ''; 

$id_PLCode_Checkin_Agendado =  trim(isset($res['id_ponto'])) ? $res['id_ponto'] : '';

$Nome_PLCode_Checkin_Agendado =  trim(isset($res['nome_ponto'])) ? $res['nome_ponto'] : '';

$hora_leitura = trim(isset($res['hora_leitura'])) ? $res['hora_leitura'] : '';

$tipo_checkin =  trim(isset($res['tipo_checkin'])) ? $res['tipo_checkin'] : ''; //  'ponto_parametro',  'ponto_plcode'

$ciclo_leitura = trim(isset($res['ciclo_leitura'])) ? $res['ciclo_leitura'] : '';  // 1 = diário, 2 = semanal

$controla_periodo = trim(isset($res['modo_checkin_periodo'])) ? $res['modo_checkin_periodo'] : '';  // 1 = Livre , 2 = Horário Controlado (Agendado)



$dias_semana_periodo="";


$diasemana_numero = date('w', time());


   if($ciclo_leitura =="2"){ // semanal
 
                $id_par_busca = $res['id_periodo_ponto'];
      

                $consulta = $conexao->query("SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana FROM periodo_dia_ponto
                INNER JOIN dia_semana ON periodo_dia_ponto.dia_semana = dia_semana.representa_php WHERE periodo_dia_ponto.id_periodo_ponto ='$id_par_busca' AND periodo_dia_ponto.dia_semana='$diasemana_numero'");
                $json_data = $consulta->fetchAll(PDO::FETCH_ASSOC);


             if($json_data){
                
               
                foreach($json_data as $res){

                   $dias_semana_periodo.= $res['representa_php'].' ';

                   $nome_dia_semana_periodo .=  "<span class='badge badge-success'>".$res['nome_dia_semana']."</span>"; 

                                    
                   $hoje_tem="sim";


                }
            } else { 


               $id_plcode_atual    = trim(isset($_COOKIE['plcode_lido'])) ? $_COOKIE['plcode_lido'] : '';

                 $consulta_hoje_nao = $conexao->query("SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana FROM periodo_dia_ponto
                INNER JOIN dia_semana ON periodo_dia_ponto.dia_semana = dia_semana.representa_php WHERE periodo_dia_ponto.id_ponto ='$id_plcode_atual'");
                $json_data_hoje_nao = $consulta_hoje_nao->fetchAll(PDO::FETCH_ASSOC);
                
                
                  foreach($json_data_hoje_nao as $item_hoje_nao){

                 

                  $nome_dia_semana_periodo .= "<span class='badge badge-primary'>".$item_hoje_nao['nome_dia_semana']." </span>"; 
                  
                   
                }

               $hoje_tem="nao"; 
             }
                
                


            } // fecha se for semanal, pega os dias da semana

         
            

//=== TENTATIVA laço de todos os agendamentos para serem realizados caso haja algum retornado de id_periodo_ponto ==>



            if($controla_periodo=="1"){ // sem controle de horario

                
               
                if($ciclo_leitura=='1'){
                    $ciclo="diário";
                    $hoje_tem="sim";
    
                } else { $ciclo="semanal";}

 
                if($hoje_tem=="sim"){

  
        echo $inicio_div_tabela;              

            $tabela_resultado= '

                <!--begin::Radio button-->
                <label class="btn btn-outline btn-outline-dashed d-flex flex-stack text-start p-6 mb-5 " id="id_periodo_ponto_checkin_agendado_'.$res['id_periodo_ponto'].'">
                    <!--end::Description-->
                    <div class="d-flex align-items-center me-2">
                        <!--begin::Radio-->
                        <div class="form-check form-check-custom form-check-solid form-check-primary me-6">
                           <input class="form-check-input link-check" type="radio"  name="id_periodo_ponto_checkin_agendado"  data-id_plcode_checkin_selecionado ="'.$res['id_ponto'].'"  data-id_periodo_checkin_ponto="'.$res['id_periodo_ponto'].'" data-parametro_checkin="'.$res['id_parametro'].'" onclick="destaque_checkin(this);"   value="'.$res['nome_ponto'].'"/>
                        </div>
                        <!--end::Radio-->

                        <!--begin::Info-->
                        <div class="flex-grow-1">
                            <h2 class="d-flex align-items-center fs-3 fw-bolder flex-wrap">
                              '.$res['nome_ponto'].'
                                <span class="badge badge-light-success ms-2 fs-7">'.$res['nome_parametro'].'</span>
                            </h2>
                            <div class="fw-bold ">
                            <span class="badge badge-info">Semanal</span>
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::Description-->

                    <!--begin::Price-->
                    <div class="ms-5">
                       
                        <span class="badge badge-primary opacity-30"> Horário Livre 
                        </span>
                        <span class="fs-7 ">
                            <span data-kt-element="period">'.$nome_dia_semana_periodo.'</span>
                        </span>
                    </div>
                    <!--end::Price-->
                </label>
                <!--end::Radio button-->
            '; 
           


        echo $tabela_resultado;

      

    
            } // fecha hoje tem => SIM
    
  echo $fim_div_tabela;

        if($hoje_tem=="nao"){

                  if($nome_dia_semana_periodo!=''){ $retorna_dia_semana = '<span class="text-danger">'.$nome_dia_semana_periodo.'</span>';} else {

                   $retorna_dia_semana = "<strong>Não há programação agendada para este PLCode.</strong>";
                }


        echo '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-dark">Sem Checkin Agendados</h4>
                                                        <h5 class="mb-1 text-dark">  '.$res['nome_ponto'].' -  '.$res['nome_parametro'].'</h5>
														<span>Parabéns, hoje você apenas precisa seguir a sua Rotina de Operação.</span>
                                                        </br>
                                                         <p>Próximo Check-in: '.$retorna_dia_semana.'</p>
													</div>
												</div>';

                                               

        } // fecha hoje tem => NÃO

  



            } // fecha SEM controle de horário


            if($controla_periodo=="2"){ // inicia COM controle de horario

                
	
                $hora_leitura_agendada = new DateTime($hora_leitura);
                $minima = new DateTime($hora_leitura);
                $minima->sub(new DateInterval('PT1M')); // subtrai 30 minutos do periodo da leitura
                $now = new DateTime('now');
    
                if ( $now <= $minima || $now < $hora_leitura_agendada  ) { 
                    $status = "expirando";
                    $css_status="warning";
                }  
                 if ( $now < $hora_leitura_agendada  || $minima < $now ) { 
                      $status = "em tempo"; 
                      $css_status="primary";
                    }
    
                if ( $now > $hora_leitura_agendada ) { 
                    $status = "expirado";
                    $css_status="danger";
                    }
               
    
                // Faz o Calculo entre o horario que era para ser realizada a leitura e o horario atual
                $saida =  substr($hora_leitura, 0,5);
                $entrada   = substr($hora_atual, 0,5);

                $prazo= intervalo( $entrada, $saida );
               

             if($hoje_tem=="sim" ){  // hoje tem com controle de horario

                        echo $inicio_div_tabela;
                    
                $tabela_resultado= '

                    <!--begin::Radio button-->
                    <label class="btn btn-outline btn-outline-dashed d-flex flex-stack text-start p-6 mb-5 " id="id_periodo_ponto_checkin_agendado_'.$res['id_periodo_ponto'].'">
                        <!--end::Description-->
                        <div class="d-flex align-items-center me-2">
                            <!--begin::Radio-->
                            <div class="form-check form-check-custom form-check-solid form-check-primary me-6">
                              <input class="form-check-input link-check" type="radio"  name="id_periodo_ponto_checkin_agendado" data-id_plcode_checkin_selecionado ="'.$res['id_ponto'].'"  data-id_periodo_checkin_ponto="'.$res['id_periodo_ponto'].'" data-parametro_checkin="'.$res['id_parametro'].'" onclick="destaque_checkin(this);"   value="'.$res['nome_ponto'].'"/>
                            </div>
                            <!--end::Radio-->

                            <!--begin::Info-->
                            <div class="flex-grow-1">
                                <h2 class="d-flex align-items-center fs-3 fw-bolder flex-wrap">
                                  '.$res['nome_ponto'].'
                                    <span class="badge badge-light-success ms-2 fs-7">  '.$res['nome_parametro'].'</span>
                                </h2>
                                <div class="fw-bold opacity-50">
                                <span class="badge badge-'.$css_status.'">Semanal</span> <span class="mb-1"> '.$nome_dia_semana_periodo.' </span>
                                </div>
                            </div>
                            <!--end::Info-->
                        </div>
                        <!--end::Description-->

                        <!--begin::Price-->
                        <div class="ms-5">
                            <span class="mb-2"><span class="badge badge-'.$css_status.'">'. $status .'</span></span>
                            <span class="fs-2x fw-bolder">
                            <strong>'.$saida.'</strong>  
                            </span>
                            <span class="fs-7 opacity-50">
                            <span data-kt-element="period"><span class="text-'.$css_status.'"><b>Próxima:</b> '.$prazo.' H:m </span></span>
                            </span>
                        </div>
                        <!--end::Price-->
                    </label>
                    <!--end::Radio button-->
                ';    
                
           



        echo $tabela_resultado;

        echo $fim_div_tabela;
                
           

        } // fecha hoje tem SIM com controle de horario



             if($hoje_tem=="nao" ){  

                      if($nome_dia_semana_periodo!=''){ $retorna_dia_semana = '<span class="text-danger">'.$nome_dia_semana_periodo.'</span>';} else {

                   $retorna_dia_semana = "<strong>Não há programação agendada para este PLCode.</strong>";
                }     

              echo '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-dark">   '.$res['nome_ponto'].'| Sem Checkin Agendados.</h4>

														<span>Próximo Check-in: '.$retorna_dia_semana.'</span>
                                                        
													</div>
												</div>';

                                               


                            } // fecha com controle de horario, hoje tem NAO

                
                            
                        } // fecha com controle de horario!




} //fecha o While Inicial
            
}// fecha o total de checkin encontrado na 1 consulta do 1 select

    if($total_checkin_agendado==0){ 
                
                // caso não haja checkin localizado na tabela 'periodo_ponto' (onde se cria os agendamentos de checkin) retorna:


        echo '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
                <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
                <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
                        <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
                    </svg>
                </span>
                <!--end::Svg Icon-->
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-dark">Estação sem Programação de Check-in!</h4>
                    <span>Caso precise realizar o agendamento de check-in, envie um email para: <b>suporte.op@grupoep.com.br</b>, informando o PLCode, Parâmetros, horários e ou ciclo de leitura (diário ou semanal), para novo cadastro de Check-in para sua Estação.</span>
                </div>
            </div>';

   

    } // se passar pela validação da CHAVE_UNICA_SESSAO_ATUAL presente na sessão do COOKIE no servidor PHP -> FAZ a Consulta [ MAS ], não foi localizado nenhum Checkin com Agendamento para esta Estação atual.

    } else { // Ultima validação, caso não encontre o COOKIE gerado pelo PHP em valida-plcode.php  'CHAVE_UNICA_SESSAO_ATUAL', não executa nada e retorna o aviso, solicitando nova Leitura do PLCode ou Logout
 
        echo '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
                <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
                <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
                        <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
                    </svg>
                </span>
                <!--end::Svg Icon-->
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-dark">Erro de Segurança!</h4>
                    <span>Por favor, refaça a Leitura do PLCode ou Saia do Sistema e entre novamente, para Efetuar a Leitura de Algum PLCode da Estação, para visualizar seus Chekins Agendados.</span>
                    <p>Caso o Problema Persista, envie um email para: <b>dev@grupoep.com.br</b>, informando sua Estação de Operação.</p>
                </div>
            </div>';


}





function mintohora($minutos)
{
$hora = floor($minutos/60);
$resto = $minutos%60;
return $hora.':'.$resto;
}




// function duas_casas(numero){
//     if (numero <= 9){
//         numero = "0"+numero;
//     }
//     return numero;
// }