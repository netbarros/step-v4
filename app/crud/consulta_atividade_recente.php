<?php 	 
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
ini_set('memory_limit', '-1');
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
date_default_timezone_set('America/Sao_Paulo');

$estacao_atual = trim(isset($_COOKIE['estacao_atual'])) ? $_COOKIE['estacao_atual'] : '';


$acao = trim(isset($_POST['acao'])) ? $_POST['acao'] : '';

// pega hora atual php
$hora_atual = date('H:i');

  $retorno_sem_leitura_hoje="";

function intervalo( $entrada, $saida ) {
    $entrada = explode( ':', $entrada );
    $saida   = explode( ':', $saida );
    $minutos = ( $saida[0] - $entrada[0] ) * 60 + $saida[1] - $entrada[1];
    if( $minutos < 0 ) $minutos += 24 * 60;
    return sprintf( '%d:%d', $minutos / 60, $minutos % 60 );
 } 
//====[ Inicia consulta para atividades no painel]===>>

if($acao=="consulta_atividades"){

    

$data_atual_periodo= date_create()->format('Y-m-d');
$data_intervalo_periodo=date('Y-m-d', strtotime('-1 days', strtotime($data_atual_periodo)));

    
  
    // executa consulta à tabela direta de todo movimento de rmm caso seja admin
    //==== [ ADMIN ]
    
    $sql = $conexao->query("SELECT   logl.*,
    tipo.id_tipo_log,
   tipo.nome_tipo_log,
   md.chave_unica as Midia_Chave_Unica,
   logl.data as data_log,
   logl.chave_unica as Log_Chave_Unica,
   p.nome_ponto,
   usuarios.nome as Nome_Usuario

    From log_leitura logl
    INNER JOIN tipo_log tipo ON tipo.id_tipo_log = logl.tipo_log
    INNER JOIN  usuarios on usuarios.id = logl.id_usuario 
    LEFT JOIN pontos_estacao p ON p.id_estacao = logl.estacao_logada
     LEFT JOIN midia_leitura md ON md.chave_unica  = logl.chave_unica
  
    Where (usuarios.id = logl.id_usuario) AND (logl.estacao_logada ='$estacao_atual') 
    AND DATE_FORMAT(logl.data, '%Y-%m-%d')  > '$data_intervalo_periodo' 
    GROUP BY logl.id_log_leitura ORDER BY logl.data DESC ");
  
    $total = $sql->rowCount ();



   // var_dump($total);
  
    
    
   
    
    //resgata os dados na tabela
    if ($total > 0) { 
    
    $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);    
    
    $retorno="";
    $possui_midia="";
    $css_dinamico ="";

    echo '<div class="card card-xl-stretch">
    <!--begin::Header-->
    <div class="card-header align-items-center border-0 mt-4">
        <h3 class="card-title align-items-start flex-column">
            <span class="fw-bolder mb-2 text-dark">Atividades Recentes</span>
            <span class="text-muted fw-bold fs-7">'.$total.' - Logs nas Últimas 24 horas.</span>
        </h3>

    </div>
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body pt-5">
        <!--begin::Timeline-->
        <div class="timeline-label">';
    
    foreach ($resultado as $res) {

        

        //var_dump($res);
      
        $data_log = $res['data'];
        $hora_format =  date('H:i', strtotime($data_log));
        $dia_format =  date('d/m/Y', strtotime($data_log));
        

$nome_usuario =  trim(isset($res['Nome_Usuario'])) ? $res['Nome_Usuario'] : ''; 

$tabela_log = trim(isset($res['acao_log'])) ? $res['acao_log'] : '';  

$id_estacao = trim(isset($res['id_estacao'])) ? $res['id_estacao'] : 'Não realizou a leitura de nenhum PLCode'; 

$id_ponto = trim(isset($res['id_ponto'])) ? $res['id_ponto'] : ''; 

$nome_ponto = trim(isset($res['nome_ponto'])) ? $res['nome_ponto'] : '';

$nome_tipo_log = trim(isset($res['nome_tipo_log'])) ? $res['nome_tipo_log'] : ''; 

$id_tipo_log = trim(isset($res['id_tipo_log'])) ? $res['id_tipo_log'] : ''; 

$Log_Chave_Unica = trim(isset($res['Log_Chave_Unica'])) ? $res['Log_Chave_Unica'] : ''; 


if (mb_strtolower($res['Log_Chave_Unica']) === mb_strtolower($res['Midia_Chave_Unica'])) {

$possui_midia = "Sim";
$css_dinamico = "text-success";


} else {

$possui_midia = "Não";
$css_dinamico = "text-danger";

}



  $retorno_leitura='';


if($id_tipo_log=='1' || $id_tipo_log== '2'){


    $sql_leitura = $conexao->query("SELECT r.id_rmm, r.leitura_entrada, pr.nome_parametro, pr.concen_max, pr.concen_min, pr.id_parametro, u.nome_unidade_medida FROM rmm r
    INNER JOIN parametros_ponto pr ON pr.id_parametro = r.id_parametro
    INNER JOIN unidade_medida u ON u.id_unidade_medida = pr.unidade_medida
    WHERE r.chave_unica = '$Log_Chave_Unica' 
    GROUP BY r.id_rmm ORDER BY r.id_rmm DESC
    ");

    $total_leitura = $sql_leitura->rowCount ();


                if($total_leitura>0) {

            $resultado_leitura = $sql_leitura->fetchAll(PDO::FETCH_ASSOC);  

           

             foreach ($resultado_leitura as $let) {


            $id_rmm_leitura =  trim(isset($let['id_rmm'])) ? $let['id_rmm'] : ''; 
            $id_parametro_lido =  trim(isset($let['id_parametro'])) ? $let['id_parametro'] : '';

            $nome_parametro =  trim(isset($let['nome_parametro'])) ? $let['nome_parametro'] : ''; 

            $concen_min =  trim(isset($let['concen_min'])) ? $let['concen_min'] : ''; 
            $concen_max =  trim(isset($let['concen_max'])) ? $let['concen_max'] : ''; 

            $retorno_leitura .= ' 
                                <div class="separator my-2"></div>
                            
                                <b>Indicador:</b> <span class="text-dark fw-bold text-uppercase"> '.$let['nome_parametro'].' </span> </br> 
                                <div class="separator my-2"></div>
                                <b>Parâmetros:</b> '.$concen_min.' <> '.$concen_max.' </br> 
                                <b>Leitura:</b> <span class="text-info fw-bold"> '.$let['leitura_entrada'].' '.$let['nome_unidade_medida'].' </span> </br> 
                                <div class="separator my-2"></div>
                            ';

             }// fecha foreach

            }// fecha leitura > 0

}
//===[ fim  da selecao dos icones para cada tipo de log de notificação]===<<

    
$retorno .=' <!--begin::Item-->
            <div class="timeline-item">
                <!--begin::Label-->
                <div class="timeline-label fw-bolder text-gray-800 fs-6">'.$hora_format.'</div>
                <!--end::Label-->
                <!--begin::Badge-->
                <div class="timeline-badge">
                    <i class="fa fa-genderless '.$css_dinamico.' fs-1"></i>
                </div>
                <!--end::Badge-->
                <!--begin::Text-->
                <div class="fw-mormal timeline-content text-muted ps-3">

                 <span class="text-primary fw-bold"> Atividade:</span> <b>'.$nome_tipo_log.'</b>
                   <div class="separator my-2"></div>
                  <b>Quem:</b> '.$nome_usuario.'
                   <div class="separator my-2"></div>
                   
                  <b>  Ação:</b> '.$tabela_log.' 
                   <div class="separator my-2"></div>
                    <b>PLCode:</b> <span class="text-dark fw-bold text-uppercase"> '.$nome_ponto.'</span>

                  '.$retorno_leitura.'
                   
                
                   <div class="separator my-2"></div>
                  <b>Mídia Enviada:</b> <span class="'.$css_dinamico.'"> '.$possui_midia.'</span>
                  | <b>Dia:</b> '.$dia_format.'


                </div>
                <!--end::Text-->
            </div>
            <!--end::Item-->';


}


 echo $retorno;



 echo ' </div>
        <!--end::Timeline-->
    </div>
    <!--end: Card Body-->
</div>';
    
   
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
														<h4 class="mb-1 text-dark">Atividades Recentes</h4>

														<span>Nenhuma Atividade Recente gerada nas últimas 24 horas para a Estação Logada.</span>
                                                        
													</div>
												</div>';
         
      
    }

    $conexao=null;

    exit;
    
    
    } 
    //====[ conclui consulta para atividades no painel]===<<



    




