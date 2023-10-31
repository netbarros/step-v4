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

  $retorno_sem_leitura_hoje="";

function intervalo( $entrada, $saida ) {
    $entrada = explode( ':', $entrada );
    $saida   = explode( ':', $saida );
    $minutos = ( $saida[0] - $entrada[0] ) * 60 + $saida[1] - $entrada[1];
    if( $minutos < 0 ) $minutos += 24 * 60;
    return sprintf( '%dh%dmin', $minutos / 60, $minutos % 60 );
 }

if($acao=="consulta_checkin_presencial"){

    $data_atual_periodo= date_create()->format('Y-m-d');
    $data_intervalo_periodo=date('Y-m-d', strtotime('-1 days', strtotime($data_atual_periodo)));

    
    $sql_periodo = $conexao->query("SELECT periodo_ponto.*,
        pontos_estacao.nome_ponto,
        pontos_estacao.id_ponto,
        periodo_ponto.tipo_checkin,
        pontos_estacao.controla_periodo_ponto,
        pontos_estacao.status_ponto,
        parametros_ponto.id_parametro,
        parametros_ponto.nome_parametro,
        periodo_dia_ponto.dia_semana,
        dia_semana.nome_dia_semana,
        checkin.data_cadastro_checkin
        
         FROM periodo_ponto
    INNER JOIN pontos_estacao ON pontos_estacao.id_ponto = periodo_ponto.id_ponto
    LEFT JOIN checkin ON checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto 
    LEFT JOIN periodo_dia_ponto ON periodo_dia_ponto.id_periodo_ponto = periodo_ponto.id_periodo_ponto
    LEFT JOIN dia_semana ON dia_semana.representa_php=periodo_dia_ponto.dia_semana
    LEFT JOIN parametros_ponto on parametros_ponto.id_parametro =periodo_ponto.id_parametro Where
    periodo_ponto.id_periodo_ponto not in  ( SELECT  checkin.id_periodo_ponto FROM  checkin WHERE checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto AND DATE_FORMAT(checkin.data_cadastro_checkin, '%Y-%m-%d')  > '$data_intervalo_periodo') 
    AND periodo_ponto.tipo_checkin = 'ponto_plcode' AND periodo_ponto.id_estacao ='$id_estacao ' AND periodo_ponto.status_periodo='1'  GROUP BY periodo_ponto.id_periodo_ponto ORDER BY periodo_ponto.hora_leitura ASC"  );
    
    
    //===> exceto se constar o id_periodo_ponto na tabela checkin, este não traz, pq já foi feito o check
    
    
    $total = $sql_periodo->rowCount ();
    
      
       // $registro = $sql_periodo->fetch(PDO::FETCH_OBJ);
        
        //var_dump($total);


            if($total>0){

            $tabela= '<div class="table-responsive-sm ">
<table class="table table-striped gy-2 gs-2">
  <thead>
  <tr class="fw-bold fs-6 text-gray-800 border-bottom-2 border-gray-200 text-uppercase">
    <th class="w-1 me-0">#</th>
    <th class="w-20 p-0">PLCode</th>
   </tr>
  </thead>
  <tbody>
     ';

        while ($res = $sql_periodo->fetch(PDO::FETCH_ASSOC)) {

            
$tipo_check='';
        

            $dias_semana_periodo_="";

          
               $nome_dia_semana='';

                $nome_dia_semana_periodo = "";
               
                $diasemana_numero = date('w', time());

                    $ciclo_leitura = $res['ciclo_leitura'];

            if($ciclo_leitura=='1'){
                $ciclo="diário";
                $hoje_tem="sim";
                $dias_semana_periodo="";
                

            } else { $ciclo="semanal";  $dias_semana_periodo="<b>Dias:</b> ";}


           
            
            if($ciclo_leitura =="2"){

                $id_periodo_lista = $res['id_periodo_ponto'];
      

                $consulta = $conexao->query("SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana FROM periodo_dia_ponto
                INNER JOIN dia_semana ON periodo_dia_ponto.dia_semana = dia_semana.representa_php WHERE periodo_dia_ponto.id_periodo_ponto ='$id_periodo_lista' AND periodo_dia_ponto.dia_semana='$diasemana_numero'");
                $json_data = $consulta->fetchAll(PDO::FETCH_ASSOC);


             if($json_data){
                
               
                foreach($json_data as $item){

                   $dias_semana_periodo.= $item['representa_php'].' ';

                   $nome_dia_semana_periodo .=  '<span class="badge badge-light-primary ms-2 fs-7">'.$item['nome_dia_semana'].'</span>'; 
                   
                   $hoje_tem="sim";


                }
            } else { $nome_dia_semana_periodo = "<div class='separator my-2'></div><span class='badge badge-light me-2'>Hoje Não</span>";  $hoje_tem="nao";}


            }
             

            $controla_periodo = $res['modo_checkin_periodo'];


            if($controla_periodo=="1"){ // sem controle de horario





                $ciclo_leitura = $res['ciclo_leitura']; 

                if($ciclo_leitura=='1'){
                    $ciclo="diário";
                    $hoje_tem="sim";
    
                } else { $ciclo="semanal";}
    
    
            if($hoje_tem=="sim"){
           
                $tabela.='<tr>
                <td>
                 <div class="form-check form-check-custom form-check-solid form-check-sm ">

                   <label class="form-check-label" id="id_periodo_ponto_checkin_agendado'.$res['id_periodo_ponto'].'">

                    <input class="form-check-input link-check " type="radio"  data-tipo_checkin="'.$res['tipo_checkin'].'"
                     data-modo_checkin="'.$res['modo_checkin_periodo'].'"  data-hora_leitura ="'.$res['hora_leitura'].'" 
                      data-nome_plcode_checkin_selecionado="'.$res['nome_ponto'].'" name="id_periodo_ponto_checkin_agendado"
                       data-id_plcode_checkin_selecionado ="'.$res['id_ponto'].'"  data-id_periodo_checkin_ponto="'.$res['id_periodo_ponto'].'" 
                       data-parametro_checkin="'.$res['id_parametro'].'" onclick="destaque_checkin(this);"   value="'.$res['nome_ponto'].'"/>
                    </label>

                    </div>
                </td>

                <td>
                   <div class=" bg-light-primary hoverable"><span class="badge badge-light"><i class="bi bi-alarm-fill text-primary fs-2 fw-bold" ></i> Horário Livre</span></div>

                <span class="fw-bolder fw-bold me-2 fs-5">
                '.$res['nome_ponto'].'</span>
                </br>
                <span class="badge badge-light-primary ms-2 fs-7"> Presencial</span> 

                <span class="badge badge-info">'. $ciclo.' </span></div>'.$nome_dia_semana_periodo.'

                <div class="separator my-2"></div>

                <span class="badge badge-light-success ms-2 fs-7">Livre</span>
                </td>
                
            </tr>';

        }
        if($hoje_tem=="nao"){


            
        $parametro_periodo_lista = $res['nome_parametro'];

        if($parametro_periodo_lista==''){ $tipo_check = "Presencial"; } else { $tipo_check = $res['nome_parametro'];}


                                  if($nome_dia_semana_periodo!=''){ $retorna_dia_semana = '<span class="text-danger">'.$nome_dia_semana_periodo.'</span>';} else {

                   $retorna_dia_semana = $nome_dia_semana_periodo;
                }     

             $retorno_sem_leitura_hoje .=  '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-dark">   '.$res['nome_ponto'].'.</h4>
                                                         <span class="badge badge-light-primary ms-2 fs-7">  '.$tipo_check.'</span> 

														<span>Agendamento: '.$retorna_dia_semana.'</span>
                                                        
													</div>
												</div>';

        }


            }


            if($controla_periodo=="2"){ // com controle de horario

                $hora_leitura = $res['hora_leitura'];

                try {
                    if (preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $hora_leitura)) {
                        $leitura = new DateTime($hora_leitura);
                        $minima = new DateTime($hora_leitura);
                        $minima->sub(new DateInterval('PT1M'));
                        $now = new DateTime('now');
                        
                        if ($now > $leitura) {
                            $status = "<span class='badge badge-light-danger'><i class='bi bi-alarm-fill text-danger fs-2 fw-bold' ></i> Prazo Expirado</span>";
                            $css_status = "danger";
                        } elseif ($now >= $minima && $now < $leitura) {
                            $status = "<span class='badge badge-light-success'><i class='bi bi-alarm-fill text-success fs-2 fw-bold' ></i> Dentro do Prazo</span>";
                            $css_status = "success";
                        } else {
                            $status = "<span class='badge badge-light-warning'><i class='bi bi-alarm-fill text-warning fs-2 fw-bold' ></i> Próximo do Prazo</span>";
                            $css_status = "warning";
                        }
                    } else {
                        echo "Formato de hora inválido.";
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                
    
    
                 
    
               
    
    
                $saida =  substr($hora_leitura, 0,5);
                $entrada   = substr($hora_atual, 0,5);
            
                $prazo= intervalo( $entrada, $saida ) ;
            
                
               
                if($hoje_tem=="sim" ){
    
            $tabela.='<tr>
                    <td> 
                    <div class="form-check form-check-custom form-check-solid form-check-sm">

                       <label class="form-check-label" id="id_periodo_ponto_checkin_agendado'.$res['id_periodo_ponto'].'">

                        <input class="form-check-input link-check" type="radio" data-tipo_checkin="'.$res['tipo_checkin'].'"
                         data-modo_checkin="'.$res['modo_checkin_periodo'].'"  data-hora_leitura ="'.$res['hora_leitura'].'" 
                          data-nome_plcode_checkin_selecionado="'.$res['nome_ponto'].'" name="id_periodo_ponto_checkin_agendado" 
                          data-id_plcode_checkin_selecionado ="'.$res['id_ponto'].'"  data-id_periodo_checkin_ponto="'.$res['id_periodo_ponto'].'"
                           data-parametro_checkin="'.$res['id_parametro'].'" onclick="destaque_checkin(this);"  
                            value="'.$res['nome_ponto'].'"/>
                        </label>

                        </div>
                    </td>

                    <td> 
                    <div class=" bg-light-'.$css_status.' hoverable">'.$status.'</i> <span class="m-2 fw-bold fs-5"> '.$saida.'</span></div>
                   

                      <div class="separator my-2"></div>
                            <span class="fw-bolder fw-bold me-2 fs-5">'.$res['nome_ponto'].'</span> 

                           </br>

                            <span class="badge badge-light-primary ms-2 fs-7">  Presencial</span> 

                            <strong>'.$saida.'</strong>  <span class="badge-light-'.$css_status.'">'. $ciclo.' </span></div> '.$nome_dia_semana_periodo.' 
                           <div class="separator my-2"></div><div class="separator my-2"></div>

                            <span class="align-items-center text-'.$css_status.' fw-bold">Próximo em '.$prazo.'</span>

                            </td>
               
                    
                </tr>';

            }

            if($hoje_tem=="nao"){

                      if($nome_dia_semana_periodo!=''){ $retorna_dia_semana = '<span class="text-danger">'.$nome_dia_semana_periodo.'</span>';} else {

                   $retorna_dia_semana = "<strong>Não há programação agendada para este PLCode.</strong>";
                }     

           $retorno_sem_leitura_hoje.=  '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-dark">   '.$res['nome_ponto'].'.</h4>

                                                         <span class="badge badge-light-primary ms-2 fs-7">  '.$tipo_check.'</span> 

														<span>Agendamento: '.$retorna_dia_semana.'</span>
                                                        
													</div>
												</div>';

            }
            
        }

        



    

        }

if(isset($retorno_sem_leitura_hoje)){
        echo  $retorno_sem_leitura_hoje;
}

        echo $tabela;


                exit;


            } else {
            

                echo  '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-dark">Sem Programação de Checkin.</h4>

														<span>Não Há Checkin Presencial Agendado até o momento.</span>
                                                        
													</div>
												</div>';
exit;           

}

}


//=========================================================


if($acao=="consulta_checkin_leitura"){


//======>>>>>>>>>>>>>>>>>> Não exibir os Checkins já existentes na tabela checkin (Where Not In Checkin)

$data_atual_periodo= date_create()->format('Y-m-d H:m:s');
$data_intervalo_periodo=date('Y-m-d', strtotime('-1 days', strtotime($data_atual_periodo)));

    
    $sql_periodo = $conexao->query("SELECT periodo_ponto.*,
        pontos_estacao.nome_ponto,
        pontos_estacao.id_ponto,
        periodo_ponto.tipo_checkin,
        pontos_estacao.controla_periodo_ponto,
        pontos_estacao.status_ponto,
        parametros_ponto.id_parametro,
        parametros_ponto.nome_parametro,
        periodo_dia_ponto.dia_semana,
        dia_semana.nome_dia_semana


        FROM periodo_ponto 

    INNER JOIN pontos_estacao ON pontos_estacao.id_ponto = periodo_ponto.id_ponto 
   INNER JOIN parametros_ponto on periodo_ponto.id_parametro = parametros_ponto.id_parametro
    LEFT JOIN periodo_dia_ponto ON periodo_ponto.id_periodo_ponto = periodo_dia_ponto.id_periodo_ponto
    LEFT JOIN dia_semana ON periodo_dia_ponto.dia_semana = dia_semana.representa_php
   

    Where periodo_ponto.id_periodo_ponto not in  ( SELECT  checkin.id_periodo_ponto FROM  checkin WHERE checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto AND DATE_FORMAT(checkin.data_cadastro_checkin, '%Y-%m-%d')  > '$data_intervalo_periodo') 
    AND periodo_ponto.tipo_checkin = 'ponto_parametro' AND periodo_ponto.id_estacao ='$id_estacao' AND periodo_ponto.status_periodo='1'  GROUP BY periodo_ponto.id_periodo_ponto ORDER BY periodo_ponto.hora_leitura ASC"   );
    
    
    //===> exceto se constar o id_periodo_ponto na tabela checkin, este não traz, pq já foi feito o check
    
    
    $total = $sql_periodo->rowCount ();
    
      
       // $registro = $sql_periodo->fetch(PDO::FETCH_OBJ);
        
       // print_r($sql_periodo);

        //exit;


            if($total>0){

            $tabela= '<div class="table-responsive-sm ">
<table class="table table-striped gy-1 gs-1">
  <thead>
  <tr class="fw-bold fs-6 text-gray-800 border-bottom-2 border-gray-400 text-uppercase">
    <th class="w-1 me-0">#</th>
    <th class="w-20 p-0">PLCode e Agendamentos</th>
  
   </tr>
  </thead>
  <tbody>';


        while ($res = $sql_periodo->fetch(PDO::FETCH_ASSOC)) {

            $id_periodo_lista = $res['id_periodo_ponto'];

            $parametro_periodo_lista = $res['nome_parametro'];

            $controla_periodo = $res['modo_checkin_periodo'];

            $ciclo_leitura = $res['ciclo_leitura'];

            $hora_leitura = $res['hora_leitura'];            

            $dias_semana_periodo_="";

            $nome_dia_semana_periodo = "";

            $diasemana_numero = date('w', time());

            if($ciclo_leitura=='1'){

                $ciclo="diário";

                 $hoje_tem="sim";

            } else { 
                
                $ciclo="semanal"; 

                $dias_semana_periodo="<b>Dias:</b> ";
            }


           
            
            if($ciclo_leitura =="2"){

 
                $consulta = $conexao->query("SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana FROM periodo_dia_ponto
                INNER JOIN dia_semana ON periodo_dia_ponto.dia_semana = dia_semana.representa_php 
                WHERE periodo_dia_ponto.id_periodo_ponto ='$id_periodo_lista' 
                AND periodo_dia_ponto.dia_semana='$diasemana_numero' GROUP BY periodo_dia_ponto.dia_semana");

                $json_data = $consulta->fetch(PDO::FETCH_ASSOC);


                        if($json_data){

                            $hoje_tem="sim";// encontrou o checkin para o mesmo dia da semana
           $nome_dia_semana_periodo =  '<span class="badge badge-light-danger ms-2 fs-7">Hoje: '.$json_data['nome_dia_semana'].'</span> <div class="separator my-1"></div>';                   
                        
          
                $consulta2 = $conexao->query("SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana,periodo_dia_ponto.id_periodo_ponto FROM periodo_dia_ponto
                LEFT JOIN dia_semana ON dia_semana.representa_php = periodo_dia_ponto.dia_semana
                 WHERE 
                periodo_dia_ponto.id_periodo_ponto ='$id_periodo_lista' 
                GROUP BY periodo_dia_ponto.dia_semana
                 ORDER BY dia_semana.representa_php ASC");

                $json_data2 = $consulta2->fetchAll(PDO::FETCH_ASSOC);

 foreach($json_data2 as $item2){

 $nome_dia_semana_periodo .=  '<span class="badge badge-light-primary ms-2 fs-7">'.$item2['nome_dia_semana'].'</span>'; 

 }            

                        } else { 

                            $hoje_tem="nao";// não encontrou o checkin para o mesmo dia da semana

$nome_dia_semana_periodo .=  '<span class="badge badge-light-dark ms-2 fs-7">Hoje Não</span>'; 


  $consulta2 = $conexao->query("SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana,periodo_dia_ponto.id_periodo_ponto FROM periodo_dia_ponto
                LEFT JOIN dia_semana ON dia_semana.representa_php = periodo_dia_ponto.dia_semana
                 WHERE 
                periodo_dia_ponto.id_periodo_ponto ='$id_periodo_lista' 
                GROUP BY periodo_dia_ponto.dia_semana
                 ORDER BY dia_semana.representa_php ASC");

                $json_data2 = $consulta2->fetchAll(PDO::FETCH_ASSOC);


                                if($json_data2){

                                     foreach($json_data2 as $item2){
                                    
$nome_dia_semana_periodo .=  '<span class="badge badge-light-primary ms-2 fs-7">'.$item2['nome_dia_semana'].'</span>'; 
                                            } 
                                        }          

                            }

                      }    

           


            if($controla_periodo=="1"){ // sem controle de horario

    
    
                    if($hoje_tem=="sim"){
                
                        $tabela.='<tr>
                                <td>
                                    <div class="form-check form-check-custom form-check-solid form-check-sm">
                                        <label class="form-check-label" id="id_periodo_ponto_checkin_agendado'.$res['id_periodo_ponto'].'">

                                        <input class="form-check-input link-check" type="radio" data-tipo_checkin="'.$res['tipo_checkin'].'"
                                        data-modo_checkin="'.$res['modo_checkin_periodo'].'" data-hora_leitura ="'.$res['hora_leitura'].'" 
                                        data-nome_plcode_checkin_selecionado="'.$res['nome_ponto'].'" name="id_periodo_ponto_checkin_agendado"
                                        data-id_plcode_checkin_selecionado ="'.$res['id_ponto'].'"  data-id_periodo_checkin_ponto="'.$res['id_periodo_ponto'].'" 
                                        data-parametro_checkin="'.$res['id_parametro'].'" onclick="destaque_checkin(this);"   value="'.$res['nome_ponto'].'"/>

                                        </label>
                                    </div>

                                </td>

                                <td>
                                    <div class=" bg-light-primary hoverable"><span class="badge badge-light"><i class="bi bi-alarm-fill text-primary fs-2 fw-bold" ></i> Horário Livre</span></div>
                                    
                                <span class="fw-bolder fw-bold me-2 fs-5">'.$res['nome_ponto'].'</span> 

                                        </br>
                                        <span class="badge badge-light-primary ms-2 fs-7">  '.$res['nome_parametro'].'</span> 

                                        <span class="badge badge-light-success">Horário Livre</span> <div class="separator my-2"></div>

                                        <span class="badge badge-light-info">'.$ciclo.' </span> '.$nome_dia_semana_periodo.'   

                                </td>



                                </tr>';

                }



    if($parametro_periodo_lista==''){ 

        $tipo_check = "Presencial"; 

    } else {

    $tipo_check = $res['nome_parametro'];
    
    }

                 if($hoje_tem=="nao"){

                            if($nome_dia_semana_periodo!=''){ 
                                
                                $retorna_dia_semana = '<span class="text-danger">'.$nome_dia_semana_periodo.'</span>';

                            } else {

                            $retorna_dia_semana = "<strong>Não há programação agendada para este PLCode.</strong>";

                            }     

                            $retorno_sem_leitura_hoje.=  '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
                                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
                                                                    <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                            <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
                                                                            <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->
                                                                    <div class="d-flex flex-column">
                                                                        <h4 class="mb-1 text-dark">   '.$res['nome_ponto'].'.</h4>
                                                                        <span class="badge badge-light-primary ms-2 fs-7">  '.$tipo_check.'</span> 


                                                                        <span>Agendamento: '.$retorna_dia_semana.'</span>
                                                                        
                                                                    </div>
                                                                </div>';

                            }


            }
  

            if($controla_periodo=="2"){ // com controle de horario
	
  
               
                if($hoje_tem=="sim" ){

                            $leitura = new DateTime($hora_leitura);
                            $minima = new DateTime($hora_leitura);
                            $minima->sub(new DateInterval('PT1M')); // subtrai 30 minutos do periodo da leitura
                            $now = new DateTime('now');
                
                            if ( $now <= $minima || $now < $leitura  ) { 
                            $status = "<span class='badge badge-light-warning'><i class='bi bi-alarm-fill text-warning fs-2 fw-bold' ></i> Próximo do Prazo</span>";
                                $css_status="warning";
                            }  
                            if ( $now < $leitura  || $minima < $now ) { 
                                $status = "<span class='badge badge-light-success'><i class='bi bi-alarm-fill text-success fs-2 fw-bold' ></i> Dentro do Prazo</span>";
                                $css_status="success";
                                }
                
                                if ( $now > $leitura ) { 
                                $status = "<span class='badge badge-light-danger'><i class='bi bi-alarm-fill text-danger fs-2 fw-bold' ></i> Prazo Expirado</span>";
                                    $css_status="danger";
                                }
                

                            $saida =  substr($hora_leitura, 0,5);
                            $entrada   = substr($hora_atual, 0,5);
                        
                            $prazo= intervalo( $entrada, $saida ) ;
            
                                    
    
                                $tabela.='<tr>
                                        <td>

                                        
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                        
                                        <label class="form-check-label " id="id_periodo_ponto_checkin_agendado'.$res['id_periodo_ponto'].'">

                                            <input class="form-check-input link-check " type="radio" data-tipo_checkin="'.$res['tipo_checkin'].'" 
                                            data-modo_checkin="'.$res['modo_checkin_periodo'].'"  data-nome_plcode_checkin_selecionado="'.$res['nome_ponto'].'" 
                                            name="id_periodo_ponto_checkin_agendado" data-id_plcode_checkin_selecionado ="'.$res['id_ponto'].'" 
                                            data-id_periodo_checkin_ponto="'.$res['id_periodo_ponto'].'" data-parametro_checkin="'.$res['id_parametro'].'" 
                                            onclick="destaque_checkin(this);"   data-hora_leitura ="'.$res['hora_leitura'].'"  value="'.$res['nome_ponto'].'"/>

                                            </label>
                                            </div>
                                            
                                        </td>
                                        <td>
                                        <div class=" bg-light-'.$css_status.' hoverable">'.$status.' <span class="m-2 fw-bold fs-5"> '.$saida.'</span> </div>

                                        <div class="separator my-2"></div>

                                            <span class="fw-bolder fw-bold me-2 fs-5">

                                                    '.$res['nome_ponto'].'</span>
                                                    
                                                    </br>

                                <span class="badge badge-light-primary ms-2 fs-7">  '.$res['nome_parametro'].'</span> 

                                <div class="separator my-2"></div> <span class="badge badge-light-'.$css_status.'">'. $ciclo.' </span> '.$nome_dia_semana_periodo.' 


                                        <span class="align-items-right text-'.$css_status.' ">Próximo em '.$prazo.' </span>
                                        </td>
                                        
                                    
                                    </tr>';

                                }

             if($hoje_tem=="nao"){

                   
                        if($parametro_periodo_lista==''){ 

                        $tipo_check = "Presencial"; 

                          } else {

                        $tipo_check = $res['nome_parametro'];

                        }

                        if($nome_dia_semana_periodo!=''){
                            
                            $retorna_dia_semana = '<span class="text-danger">'.$nome_dia_semana_periodo.'</span>';

                           } else {

                        $retorna_dia_semana = "<strong>Não há programação agendada para este PLCode.</strong>";

                        }     

                $retorno_sem_leitura_hoje.= '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-dark">   '.$res['nome_ponto'].'.</h4>

                                                    <span class="badge badge-light-primary ms-2 fs-7">  '.$tipo_check.'</span> 


														<span>Agendamento: '.$retorna_dia_semana.'</span>
                                                        
													</div>
												</div>';

            }
            
        } // fecha controla periodo 2


       

        }


                  $tabela.= '</tbody>
                        </table>
                        </div>';

if(isset($retorno_sem_leitura_hoje)){

        echo  $retorno_sem_leitura_hoje;

}

        echo $tabela;


                exit;
    
        
        } else { 

                       echo  '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-dark">Sem Programação de Checkin.</h4>

														<span>Não Há Checkin de Leitura de Indicadores (Parâmetros), Agendados até o momento.</span>
                                                        
													</div>
												</div>';

            exit;

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