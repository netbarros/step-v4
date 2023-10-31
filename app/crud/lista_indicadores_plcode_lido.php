<?php 	 
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
date_default_timezone_set('America/Sao_Paulo');


  $id_plcode_atual = (isset($_POST['id_plcode_informado'])) ? trim($_POST['id_plcode_informado']) : '';

  $id_usuario = trim(isset($_POST['id_usuario'])) ? trim($_POST['id_usuario']) : '';

  if($id_plcode_atual==''){
    $retorno = array('codigo' => 0, 'retorno' => "Nenhum PLCode informado.");
    echo json_encode($retorno, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES);
    exit;
  }

  if($id_usuario==''){
    $retorno = array('codigo' => 0, 'retorno' => "Nenhum usuário informado.");
    echo json_encode($retorno, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES);
    exit;
  }

  try {
   

    $sql = "
        SELECT pr.*, 
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
        ORDER BY pr.status_parametro = '1' DESC
    ";

    $stm = $conexao->prepare($sql);
    $stm->bindParam(':id_plcode_atual', $id_plcode_atual, PDO::PARAM_INT);
    $stm->execute();

    $json_data = $stm->fetchAll(PDO::FETCH_ASSOC);

    if ($stm->rowCount() > 0) {


    foreach ($json_data as $valor){


$motivo_checkin="";

$nome_dia_semana_periodo = "";

$diasemana_numero = date('w', time());

$dias_semana_periodo='';

$desabilita_leitura='';

$busca_suporte_indicador='';
$acao_revoga ='';
$nome_modo_periodo_checkin='';
$dias_da_semana_agendado='';

  $texto_indicador_suspenso='';
$cor_indicador_suspenso='';   
        
$status_parametro = $valor['status_parametro'];

$modo_checkin_periodo = $valor['modo_checkin_periodo'];

$status_periodo_checkin = $valor['status_periodo'];

if($modo_checkin_periodo=='1'){

$nome_modo_periodo_checkin = "Horário Livre";

}

if($modo_checkin_periodo=='2'){


$nome_modo_periodo_checkin ="<span class='y-2'> Próxima Leitura: ".substr($valor['hora_leitura'], 0, -3)."</span> ";

    
}
            
if($status_parametro=='2'){

$desabilita_leitura = "disabled";

$cor_indicador_suspenso='btn btn-outline btn-outline-dashed d-flex flex-stack text-start text-warning p-6 mb-5 active';

$texto_indicador_suspenso='<span class="badge badge-light-warning ms-2 fs-7">Leitura Suspensa</span>';


$acao_revoga ='onclick="Libera_Indicador($(this))"';

}else {

$desabilita_leitura='';

$cor_indicador_suspenso='d-flex flex-stack mb-6 cursor-pointer';

$acao_revoga ='onclick="Revoga_Indicador($(this))"';
$texto_indicador_suspenso='';
}


                $ciclo_leitura = $valor['ciclo_leitura'];


                if($ciclo_leitura=='1'){
                    $ciclo="Diário";
                    $hoje_tem="sim";
    
                } else { $ciclo="Semanal";}


                 if($ciclo_leitura =="2"){

                $id_Busca_Periodo_Checkin_Agendado = $valor['id_periodo_ponto'];
      

                $consulta = $conexao->query("SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana FROM periodo_dia_ponto
                LEFT JOIN dia_semana ON dia_semana.representa_php = periodo_dia_ponto.dia_semana 
                WHERE 
                periodo_dia_ponto.id_periodo_ponto ='$id_Busca_Periodo_Checkin_Agendado' 
                AND periodo_dia_ponto.dia_semana='$diasemana_numero' ORDER BY dia_semana.representa_php ASC");

                $json_data = $consulta->fetch(PDO::FETCH_ASSOC);


             if($json_data){

               $nome_dia_semana_periodo =  '<span class="badge badge-light-danger ms-2 fs-7">Hoje: '.$json_data['nome_dia_semana'].'</span> <div class="separator my-1"></div>'; 

     
               
                $consulta2 = $conexao->query("SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana,periodo_dia_ponto.id_periodo_ponto FROM periodo_dia_ponto
                LEFT JOIN dia_semana ON dia_semana.representa_php = periodo_dia_ponto.dia_semana
                 WHERE 
                periodo_dia_ponto.id_periodo_ponto ='$id_Busca_Periodo_Checkin_Agendado' 
                GROUP BY periodo_dia_ponto.dia_semana
                 ORDER BY dia_semana.representa_php ASC");

                $json_data2 = $consulta2->fetchAll(PDO::FETCH_ASSOC);

 foreach($json_data2 as $item2){

 $nome_dia_semana_periodo .=  '<span class="badge badge-light-primary ms-2 fs-7">'.$item2['nome_dia_semana'].'</span>'; 

 }

                            } else {

                            $hoje_tem="nao";

                                 $id_Busca_Periodo_Checkin_Agendado = $valor['id_periodo_ponto'];
      
 $nome_dia_semana_periodo .=  '<span class="badge badge-light-dark ms-2 fs-7">Hoje Não</span>'; 


  $consulta2 = $conexao->query("SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana,periodo_dia_ponto.id_periodo_ponto FROM periodo_dia_ponto
                LEFT JOIN dia_semana ON dia_semana.representa_php = periodo_dia_ponto.dia_semana
                 WHERE 
                periodo_dia_ponto.id_periodo_ponto ='$id_Busca_Periodo_Checkin_Agendado' 
                GROUP BY periodo_dia_ponto.dia_semana
                 ORDER BY dia_semana.representa_php ASC");

                $json_data2 = $consulta2->fetchAll(PDO::FETCH_ASSOC);

 foreach($json_data2 as $item2){

 $nome_dia_semana_periodo .=  '<span class="badge badge-light-primary ms-2 fs-7">'.$item2['nome_dia_semana'].'</span>'; 

 }
 

                       }
                          }


        $nome_ponto = $valor['nome_ponto'];

$nome_parametro = $valor['nome_parametro'];


        $tipo_checkin = $valor['tipo_checkin'];

        if($tipo_checkin=="ponto_plcode"){

             $motivo_checkin= '<span class="align-items-center  fs-7"><b>Presencial por PLCode</b></span> <span class="fs-5 align-items-center text-primary"><b>'.$nome_ponto.' </b></span>';
        }


        if($tipo_checkin=="ponto_parametro"){

            $motivo_checkin= '<span class="align-items-center fs-7"><b>Indicador</b></span><br> <span class="fs-5 align-items-center text-primary"><b>'.$valor['nome_parametro'].'</b></span>';
        }


   if($valor['id_periodo_ponto']=='') {

   
            echo '<label class="'.$cor_indicador_suspenso.'" id="label_'.$valor['id_parametro'].' ">
                                                                                    <!--begin:Label-->
                                                                                    <span class="d-flex align-items-center me-1">
                                                                                        <!--begin::Icon-->
                                                                                        <span class="symbol symbol-40px me-3">
                                                                                            <span class="symbol-label d-none" id="abre_modal_imagem_'.$valor['id_parametro'].'">

            <a href="javascript:;" class="btn btn-icon btn-success me-2"  data-indicador ="'.$valor['id_parametro'].'" data-nome_indicador ="'.$valor['nome_parametro'].'" data-bs-toggle="modal" data-bs-target="#modal_midia_evento"><i class="bi bi-camera  fs-4x "></i></a>


                                                                             
                                                                                </span>
                                                                            </span>
                                                                            <!--end::Icon-->

                                                                            <!--begin::Description-->
                                                                            <span class="d-flex flex-column ">
                                                                                <span
                                                                                    class="fw-bolder text-gray-900 fs-5" id="Texto_Nome_Parametro_Digitado_'.$valor['id_parametro'].'">'.$texto_indicador_suspenso.' '.$valor['nome_parametro'].'</span>
                                                                                <span class="fs-5 fw-bold text-muted input-group ">
                                                                                    <!--begin::Input group-->
                                                                                    <div class="form-floating mb-4 d-flex w-140px ">
                                                                                        <input type="number"  id="indicador_'.$valor['id_parametro'].'"
                                                                                            class="form-control leitura_captada form-control-solid "
                                                                                             data-indicador ="'.$valor['id_parametro'].'" data-min="'.$valor['concen_min'].'" data-max="'.$valor['concen_max'].'"
                                                                                           onkeyup="Valida_Indicador($(this))" name="'.$valor['id_parametro'].'" data-origem_parametro = "'.$valor['origem_leitura_parametro'].'" data-nome_indicador ="'.$valor['nome_parametro'].'" data-plcode="'.$id_plcode_atual.'" value=""  '.$desabilita_leitura .' />
                                                                                        <label
                                                                                            for="floatingInput">'.$valor['concen_min'].' até '.$valor['concen_max'].'
                                                                                            </label>
                                                                                            
                                                                                    </div>
                                                                                    
                                                                                    <!--end::Input group-->
                                                                                    <div class=" m-2  mt-n1 form-check form-check-custom form-check-solid form-check-sm">
                                                                                        <input class="form-check-input" type="radio" value="" data-id="'.$valor['id_parametro'].'" id="'.$valor['id_parametro'].'"  '.$acao_revoga.'  data-nome_indicador ="'.$valor['nome_parametro'].'"  data-plcode="'.$id_plcode_atual.'" />
                                                                                        
                                                                                    </div>
                                                                                </span>
                                                                            </span>
                                                                            <!--end:Description-->

                                                                        </span>
                                                                        <!--end:Label-->


                                                                    </label>
                                                                    <!--end::Option- Dinâmico-->';
                                                                    

        } else if($status_periodo_checkin!='3') {
               



echo '<!--begin::Alert-->
    <div class="alert alert-primary">

   
        <div class="d-flex flex-column align-items-center">
        <i class="bi bi-alarm fs-2x text-primary text-bold"></i>
         <div class="separator my-1"></div>
        <span class="badgebadge-light-success text-uppercase text-bold align-items-center">'.$motivo_checkin.'</span>
       <div class="separator my-1"></div> 
        <span class="badge badge-success w-auto text-uppercase fs-5  align-items-center">'.$nome_modo_periodo_checkin .'</span>

        <br>
        
        <div class="separator my-1"></div> 
        <span class="fs-7 w-auto h-auto">'.$nome_dia_semana_periodo.'</span>
 <div class="separator my-1"></div>
        <span class="badge badge-light-primary w-auto text-uppercase align-items-center"><span class="text-success">Ciclo:</span> '. $ciclo.' </span> 

        </div>


    </div>
    <!--end::Alert-->';



   }


        // verifica se há checkin para o plcode informado e se o id do parametro consta em agendamento
        if(trim($valor['id_ponto'])!==$id_plcode_atual ) {

echo '<!--begin::Alert-->
<div class="alert alert-primary">
    <!--begin::Icon-->
    <span class="svg-icon svg-icon-2hx svg-icon-primary me-3"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-octagon-fill" viewBox="0 0 16 16">
  <path d="M11.46.146A.5.5 0 0 0 11.107 0H4.893a.5.5 0 0 0-.353.146L.146 4.54A.5.5 0 0 0 0 4.893v6.214a.5.5 0 0 0 .146.353l4.394 4.394a.5.5 0 0 0 .353.146h6.214a.5.5 0 0 0 .353-.146l4.394-4.394a.5.5 0 0 0 .146-.353V4.893a.5.5 0 0 0-.146-.353L11.46.146zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
</svg>
</span>
    <!--end::Icon-->

    <!--begin::Wrapper-->
    <div class="d-flex flex-column">
        <!--begin::Title-->
        <h4 class="mb-1 text-dark">Você não está no mesmo PLCode Lido</h4>
        <!--end::Title-->
        <!--begin::Content-->
        <span>Por favor dirija-se ao PLCode desejado e faça a leitura do mesmo, para prosseguir.</span>
        <!--end::Content-->
    </div>
    <!--end::Wrapper-->
</div>
<!--end::Alert-->';

exit;

        }  // verifica se há checkin para o plcode informado e se o id do parametro consta em agendamento        



        

    }// fecha o foreach




$conexao=null;
exit;

}else{

    
echo '<!--begin::Alert-->
<div class="alert alert-primary">
    <!--begin::Icon-->
    <span class="svg-icon svg-icon-2hx svg-icon-primary me-3"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-octagon-fill" viewBox="0 0 16 16">
  <path d="M11.46.146A.5.5 0 0 0 11.107 0H4.893a.5.5 0 0 0-.353.146L.146 4.54A.5.5 0 0 0 0 4.893v6.214a.5.5 0 0 0 .146.353l4.394 4.394a.5.5 0 0 0 .353.146h6.214a.5.5 0 0 0 .353-.146l4.394-4.394a.5.5 0 0 0 .146-.353V4.893a.5.5 0 0 0-.146-.353L11.46.146zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
</svg>
</span>
    <!--end::Icon-->

    <!--begin::Wrapper-->
    <div class="d-flex flex-column">
        <!--begin::Title-->
        <h4 class="mb-1 text-dark">Não Há Indicadores para este PLCode</h4>
        <!--end::Title-->
        <!--begin::Content-->
        <span> Cadastre os Indicadores e Parâmetros para que possa efetuar as leituras.</span>
        <!--end::Content-->
    </div>
    <!--end::Wrapper-->
</div>
<!--end::Alert-->';

$conexao=null;
exit;
}
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
} 






     ?>

     
     
  


 
 <!--begin:Option Dinâmico-->
                                                                    