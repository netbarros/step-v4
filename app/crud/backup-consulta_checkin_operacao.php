<?php 
date_default_timezone_set('America/Sao_Paulo');

// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();

$id_estacao = trim(isset($_POST['estacao'])) ? $_POST['estacao'] : '';



$acao = trim(isset($_POST['acao'])) ? $_POST['acao'] : '';

//$id_parametro = trim(isset($_POST['id_parametro'])) ? $_POST['id_parametro'] : '';

// pega hora atual php
$hora_atual = date('H:i');

function intervalo( $entrada, $saida ) {
    $entrada = explode( ':', $entrada );
    $saida   = explode( ':', $saida );
    $minutos = ( $saida[0] - $entrada[0] ) * 60 + $saida[1] - $entrada[1];
    if( $minutos < 0 ) $minutos += 24 * 60;
    return sprintf( '%d:%d', $minutos / 60, $minutos % 60 );
 }



 /*

if($acao=="consulta_checkin_presencial"){

    $data_atual_periodo= date_create()->format('Y-m-d');
    $data_intervalo_periodo=date('Y-m-d', strtotime('-1 days', strtotime($data_atual_periodo)));

   
    $sql_periodo = $conexao->query("SELECT periodo_ponto.*,pontos_estacao.nome_ponto, pontos_estacao.id_ponto, periodo_ponto.tipo_checkin,pontos_estacao.controla_periodo_ponto, pontos_estacao.status_ponto,parametros_ponto.id_parametro,parametros_ponto.nome_parametro,periodo_dia_ponto.dia_semana,dia_semana.nome_dia_semana, checkin.data_cadastro_checkin FROM periodo_ponto
    INNER JOIN pontos_estacao ON pontos_estacao.id_ponto = periodo_ponto.id_ponto
    LEFT JOIN checkin ON checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto 
    LEFT JOIN periodo_dia_ponto ON periodo_dia_ponto.id_periodo_ponto = periodo_ponto.id_periodo_ponto
    LEFT JOIN dia_semana ON dia_semana.representa_php=periodo_dia_ponto.dia_semana
    LEFT JOIN parametros_ponto on parametros_ponto.id_parametro =periodo_ponto.id_parametro Where
    periodo_ponto.id_periodo_ponto not in  ( SELECT  checkin.id_periodo_ponto FROM  checkin WHERE checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto AND DATE_FORMAT(checkin.data_cadastro_checkin, '%Y-%m-%d')  > '$data_intervalo_periodo') 
    AND periodo_ponto.tipo_checkin = 'ponto_plcode' AND periodo_ponto.id_estacao ='$id_estacao '  GROUP BY periodo_ponto.id_periodo_ponto ORDER BY periodo_ponto.hora_leitura ASC"  );
    
    
    //===> exceto se constar o id_periodo_ponto na tabela checkin, este não traz, pq já foi feito o check
    
    
    $total = $sql_periodo->rowCount ();
    
      
       // $registro = $sql_periodo->fetch(PDO::FETCH_OBJ);
        
        //var_dump($total);


            if($total>0){

           

        while ($res = $sql_periodo->fetch(PDO::FETCH_ASSOC)) {

            $ciclo_leitura = $res['ciclo_leitura'];

            $dias_semana_periodo_="";
               
                $nome_dia_semana_periodo = "";
               
                $diasemana_numero = date('w', time());

            if($ciclo_leitura=='1'){
                $ciclo="diário";
                $hoje_tem="sim";
                $dias_semana_periodo="";
                

            } else { $ciclo="semanal";  $dias_semana_periodo="<b>Dias:</b> ";}


           
            
            if($ciclo_leitura =="2"){

                $id_par_busca = $res['id_periodo_ponto'];
      

                $consulta = $conexao->query("SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana FROM periodo_dia_ponto
                INNER JOIN dia_semana ON periodo_dia_ponto.dia_semana = dia_semana.representa_php WHERE periodo_dia_ponto.id_periodo_ponto ='$id_par_busca' AND periodo_dia_ponto.dia_semana='$diasemana_numero'");
                $json_data = $consulta->fetchAll(PDO::FETCH_ASSOC);


             if($json_data){
                
               
                foreach($json_data as $item){

                   $dias_semana_periodo.= $item['representa_php'].' ';

                   $nome_dia_semana_periodo .=  "<span class='kt-badge kt-badge--inline kt-badge--success'>".$item['nome_dia_semana']."</span>"; 
                   
                   $hoje_tem="sim";


                }
            } else { $nome_dia_semana_periodo = "<span class='kt-badge kt-badge--inline kt-shape-bg-color-2'>Hoje Não</span>"; $hoje_tem="nao"; }


            }

            $controla_periodo = $res['modo_checkin_periodo'];


            if($controla_periodo=="1"){ // sem controle de horario





                $ciclo_leitura = $res['ciclo_leitura']; 

                if($ciclo_leitura=='1'){
                    $ciclo="diário";
                    $hoje_tem="sim";
    
                } else { $ciclo="semanal";}
    
    
            if($hoje_tem=="sim"){

                 $tabela= '<thead>
            <tr>
                <td style="width:1%">#</td>
                <td style="width:35%">PLCode</td>
                <td style="width:24%">Horário</td>
                <td style="width:35%">Status</td>
                <td style="width:5%" class="kt-align-right">Prazo H/m</td>
            </tr>
        </thead>
        <tbody >';
           
                $tabela.='<tr>
                <td>
                    <label class="kt-checkbox kt-checkbox--single">
                    <input type="checkbox" name="id_periodo" value="'.$res['id_periodo_ponto'].'" onclick="destaque(this);" class="link-check"><span></span>
                    </label>
                </td>
                <td>
                    <span class="kt-widget11__title"> '.$res['nome_ponto'].'</span>
                    <span class="kt-widget11__sub">'.$res['nome_parametro'].'</span>
                </td>
                <td><strong>livre</strong> | <span class="kt-badge kt-badge--inline kt-badge--brand">'. $ciclo.' </span> '.$nome_dia_semana_periodo.' </td>
            
                <td><span class="kt-badge kt-badge--inline kt-badge--success">Livre</span></td>
                <td class="kt-align-right kt-font-brand kt-font-bold"> livre </td>
            </tr>';

             $tabela.= '</tbody>';

              echo $tabela;

        }
        if($hoje_tem=="nao"){

        }


            }


            if($controla_periodo=="2"){ // com controle de horario

                $hora_leitura = $res['hora_leitura'];
	
                $hora_leitura_agendada = new DateTime($hora_leitura);
                $minima = new DateTime($hora_leitura);
                $minima->sub(new DateInterval('PT1M')); // subtrai 30 minutos do periodo da leitura
                $now = new DateTime('now');
    
                if ( $now <= $minima || $now < $hora_leitura_agendada  ) { 
                    $status = "expirando";
                    $css_status="warning";
                }  
                 if ( $now < $hora_leitura_agendada  || $minima < $now ) { 
                      $status = "em curso"; 
                      $css_status="brand";
                    }
    
                    if ( $now > $hora_leitura_agendada ) { 
                        $status = "expirado";
                        $css_status="danger";
                      }
    
    
                 
    
               
    
    
                $saida =  substr($hora_leitura, 0,5);
                $entrada   = substr($hora_atual, 0,5);
            
                $prazo= intervalo( $entrada, $saida ) ;
            
                
               
                if($hoje_tem=="sim" ){

                     $tabela= '<thead>
            <tr>
                <td style="width:1%">#</td>
                <td style="width:35%">PLCode</td>
                <td style="width:24%">Horário</td>
                <td style="width:35%">Status</td>
                <td style="width:5%" class="kt-align-right">Prazo H/m</td>
            </tr>
        </thead>
        <tbody >';
    
            $tabela.='<tr>
                    <td>
                        <label class="kt-checkbox kt-checkbox--single">
                        <input type="checkbox" name="id_periodo" value="'.$res['id_periodo_ponto'].'" onclick="destaque(this);" class="link-check"><span></span>
                        </label>
                    </td>
                    <td>
                        <span class="kt-widget11__title"> '.$res['nome_ponto'].'</span>
                        <span class="kt-widget11__sub">'.$res['nome_parametro'].'</span>
                    </td>
                    <td><strong>'.$saida.'</strong> | <span class="kt-badge kt-badge--inline kt-badge--'.$css_status.'">'. $ciclo.' </span> '.$nome_dia_semana_periodo.' </td>
                
                    <td><span class="kt-badge kt-badge--inline kt-badge--'.$css_status.'">'. $status .'</span></td>
                    <td class="kt-align-right kt-font-'.$css_status.' kt-font-bold">'.$prazo.' </td>
                </tr>';

                 $tabela.= '</tbody>';

                  echo $tabela;

            }
            
        }

        }


            
       


       


                exit;


            } else {


               echo "Não há Checkin para Hoje agendado até o momento.";
            }

}


//=========================================================

*/

if($acao=="consulta_checkin"){


//======>>>>>>>>>>>>>>>>>> Não exibir os Checkins já existentes na tabela checkin (Where Not In Checkin)
$data_Leitura_Hoje= date_create()->format('d/m/Y H:m');
$data_atual_periodo= date_create()->format('Y-m-d H:m:s');
$data_intervalo_periodo=date('Y-m-d', strtotime('-1 days', strtotime($data_atual_periodo)));

   
    $sql_periodo = $conexao->query("SELECT periodo_ponto.*,pontos_estacao.nome_ponto, pontos_estacao.id_ponto, periodo_ponto.tipo_checkin,pontos_estacao.controla_periodo_ponto, pontos_estacao.status_ponto,parametros_ponto.id_parametro,parametros_ponto.nome_parametro,periodo_dia_ponto.dia_semana,dia_semana.nome_dia_semana, checkin.data_cadastro_checkin FROM periodo_ponto 

    INNER JOIN pontos_estacao ON pontos_estacao.id_ponto = periodo_ponto.id_ponto 
    LEFT JOIN checkin ON checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto 
    LEFT JOIN periodo_dia_ponto ON periodo_dia_ponto.id_periodo_ponto = periodo_ponto.id_periodo_ponto 
    LEFT JOIN dia_semana ON dia_semana.representa_php=periodo_dia_ponto.dia_semana 
    LEFT JOIN parametros_ponto on parametros_ponto.id_parametro =periodo_ponto.id_parametro 

    Where periodo_ponto.id_periodo_ponto not in  ( SELECT  checkin.id_periodo_ponto FROM  checkin WHERE checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto AND DATE_FORMAT(checkin.data_cadastro_checkin, '%Y-%m-%d')  > '$data_intervalo_periodo') 
    AND periodo_ponto.id_estacao ='$id_estacao'   GROUP BY periodo_ponto.id_periodo_ponto ORDER BY periodo_ponto.hora_leitura ASC"   );
    
    
    //===> exceto se constar o id_periodo_ponto na tabela checkin, este não traz, pq já foi feito o check
    
    
    $total = $sql_periodo->rowCount ();
    
      
       // $registro = $sql_periodo->fetch(PDO::FETCH_OBJ);
        
    //   / print_r($sql_periodo);

if($total>0){

            


        while ($res = $sql_periodo->fetch(PDO::FETCH_ASSOC)) {



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



            $ciclo_leitura = $res['ciclo_leitura'];

            $dias_semana_periodo="";
               
                $nome_dia_semana_periodo = "";
                            
                $diasemana_numero = date('w', time());

            if($ciclo_leitura=='1'){ // diário
                $ciclo="diário";
                $hoje_tem="sim";
                $dias_semana_periodo="";
                

            } else { $ciclo="semanal";}


           
            
            if($ciclo_leitura =="2"){ // semanal
 
                $id_par_busca = $res['id_periodo_ponto'];
      

                $consulta = $conexao->query("SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana FROM periodo_dia_ponto
                INNER JOIN dia_semana ON periodo_dia_ponto.dia_semana = dia_semana.representa_php WHERE periodo_dia_ponto.id_periodo_ponto ='$id_par_busca' AND periodo_dia_ponto.dia_semana='$diasemana_numero'");
                $json_data = $consulta->fetchAll(PDO::FETCH_ASSOC);


             if($json_data){
                
               
                foreach($json_data as $item){

                   $dias_semana_periodo.= $item['representa_php'].' ';

                   $nome_dia_semana_periodo .=  "<span class='badge badge-success'>".$item['nome_dia_semana']."</span>"; 

                                    
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
                
                


            } // fecha ciclo de leitura 2

            $controla_periodo = $res['modo_checkin_periodo'];


            if($controla_periodo=="1"){ // sem controle de horario





                $ciclo_leitura = $res['ciclo_leitura']; 

                if($ciclo_leitura=='1'){
                    $ciclo="diário";
                    $hoje_tem="sim";
    
                } else { $ciclo="semanal";}
                


               
 
            if($hoje_tem=="sim"){

  $tabela= '<div class="table-responsive">
                
                <table class="table table-striped gy-7 gs-7">
                 <thead>
            <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                <th >#</th>
                <th>PLCode</th>
                <th >Horário</th>
                <th >Status</th>
                <th>Tempo</th>
            </tr>
        </thead>
        <tbody >';               
           
                $tabela.='<tr>
                <td>
                    <label class="kt-checkbox kt-checkbox--single">
                     <input class="form-check-input link-check" type="radio" name="id_periodo" value="'.$res['id_periodo_ponto'].'" data-parametro_checkin="'.$res['id_parametro'].'" onclick="destaque_checkin(this);" class="link-check"/>
                   
                    </label>
                </td>
                <td>
                    <span class="text-primary"> '.$res['nome_ponto'].'</span>
                    <span class="text-info">'.$res['nome_parametro'].'</span>
                </td>
                <td><strong>livre</strong> | <span class="badge badge-primary">'. $ciclo.' </span> '.$nome_dia_semana_periodo.' </td>
            
                <td><span class="badge badge-success">Livre</span></td>
                <td class="text-muted"> livre </td>
            </tr>';

             $tabela.= '</tbody> </table></div>';

 echo $tabela;
            

        }

    


        if($hoje_tem=="nao"){

             if($nome_dia_semana_periodo!=''){ $retorna_dia_semana = $nome_dia_semana_periodo;} else {

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
														<h4 class="mb-1 text-dark">Sem Checkin Agendados.</h4>
														<span>Parabéns, hoje você apenas precisa seguir a sua Rotina de Operação.</span>
                                                        </br>
                                                         <p>Próximo Check-in: '.$retorna_dia_semana.'</p>
													</div>
												</div>';

                                               

        }

    

            } // encerra sem controle de horário


            if($controla_periodo=="2"){ // com controle de horario

                $hora_leitura = $res['hora_leitura'];
	
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
    
    
                 
    
               
    
    
                $saida =  substr($hora_leitura, 0,5);
                $entrada   = substr($hora_atual, 0,5);
            
                $prazo= intervalo( $entrada, $saida ) ;


                
            
                
                 

                if($hoje_tem=="sim" ){  // hoje tem com controle de horario

                    
  $tabela= '<div class="table-responsive">
                
                <table class="table table-striped gy-7 gs-7">
                 <thead>
            <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                <th >#</th>
                <th>PLCode</th>
                <th >Horário</th>
                <th >Status</th>
                <th>Tempo</th>
            </tr>
        </thead>
        <tbody >';
                   
    
            $tabela.='<tr>
                    <td>
                        <label class="kt-checkbox kt-checkbox--single">
                        <input type="checkbox" name="id_periodo" value="'.$res['id_periodo_ponto'].'" data-parametro_checkin="'.$res['id_parametro'].'" onclick="destaque_checkin(this);" class="link-check"><span></span>
                        </label>
                    </td>
                    <td>
                        <span class="text-primary"> '.$res['nome_ponto'].'</span>
                        <span class="text-info">'.$res['nome_parametro'].'</span>
                    </td>
                    <td><strong>'.$saida.'</strong> | <span class="badge badge-'.$css_status.'">'. $ciclo.' </span> '.$nome_dia_semana_periodo.' </td>
                
                    <td><span class="badge badge-'.$css_status.'">'. $status .'</span></td>
                    <td><span class="text-'.$css_status.'">'.$prazo.' </span></td>
                </tr>';


                 $tabela.= '</tbody> </table></div>';

              
 echo $tabela;
              
            } 

           


             if($hoje_tem=="nao" ){  

                if($nome_dia_semana_periodo!=''){ $retorna_dia_semana = $nome_dia_semana_periodo;} else {

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
														<h4 class="mb-1 text-dark">Sem Checkin Agendados.</h4>

														<span>Parabéns, hoje você apenas precisa seguir a sua Rotina de Operação.</span>
                                                        </br>
                                                        <p> Próximo Check-in: '.$retorna_dia_semana.'</p>
													</div>
												</div>';

                                               


            }

            
            
        }

        

        }


            }  
            
            
            if($total==0){


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

                                                
            }
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