<?php 	 
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();

$estacao_atual = trim(isset($_COOKIE['estacao_atual'])) ? $_COOKIE['estacao_atual'] : '';


$nome_estacao = trim(isset($_COOKIE['nome_Estacao_Atual'])) ? $_COOKIE['nome_Estacao_Atual'] : '';

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
   tipo.nome_tipo_log,
   md.chave_unica as Midia_Chave_Unica,
   logl.data as data_log,
   logl.chave_unica as Log_Chave_Unica,
   e.id_estacao,
   p.id_ponto,
   e.nome_estacao,
   p.nome_ponto,
   usuarios.nome as Nome_Usuario,
   pr.nome_parametro,
   r.leitura_entrada,
   r.data_leitura as data_leitura,
   r.chave_unica as RMM_Chave_Unica,
   u.nome_unidade_medida,
   pr.concen_max,
   pr.concen_min

    
    From log_leitura logl
    INNER JOIN tipo_log tipo ON tipo.id_tipo_log = logl.tipo_log
    INNER JOIN  usuarios on usuarios.id = logl.id_usuario 
    INNER JOIN estacoes e ON e.id_estacao = logl.estacao_logada
    LEFT JOIN pontos_estacao p ON p.id_estacao = e.id_estacao
    LEFT JOIN rmm r ON r.chave_unica = logl.chave_unica
    LEFT JOIN parametros_ponto pr ON pr.id_parametro = r.id_parametro
    LEFT JOIN unidade_medida u ON u.id_unidade_medida = pr.unidade_medida
    LEFT JOIN midia_leitura md ON md.chave_unica  = logl.chave_unica
  
    Where (usuarios.id = logl.id_usuario) AND (logl.estacao_logada ='$estacao_atual') AND DATE_FORMAT(logl.data, '%Y-%m-%d')  > '$data_intervalo_periodo' GROUP BY logl.id_log_leitura ORDER BY logl.data DESC ");
  
    $total = $sql->rowCount ();




   // var_dump($total);
  
    
    
   
    
    //resgata os dados na tabela
    if ($total > 0) { 
    
    $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);    
    
    $retorno="";
    $possui_midia="";
    $css_dinamico ="";
    $retorno_leitura='';


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

$nome_estacao = trim(isset($res['nome_estacao'])) ? $res['nome_estacao'] : '';  

$id_estacao = trim(isset($res['id_estacao'])) ? $res['id_estacao'] : 'Não realizou a leitura de nenhum PLCode'; 

$id_ponto = trim(isset($res['id_ponto'])) ? $res['id_ponto'] : ''; 

$nome_tipo_log = trim(isset($res['nome_tipo_log'])) ? $res['nome_tipo_log'] : ''; 

if (mb_strtolower($res['Log_Chave_Unica']) === mb_strtolower($res['Midia_Chave_Unica'])) {

$possui_midia = "Sim";
$css_dinamico = "text-success";


} else {

$possui_midia = "Não";
$css_dinamico = "text-danger";

}


if (mb_strtolower($res['Log_Chave_Unica']) === mb_strtolower($res['Midia_Chave_Unica']) && mb_strtolower($res['RMM_Chave_Unica'])=== mb_strtolower($res['Log_Chave_Unica'])){

$retorno_leitura = '</br><b>Parâmetro:</b> '.$res['nome_parametro'].',</br> <b>Leitura: </b> '.$res['leitura_entrada'].' '.$res['nome_unidade_medida'].'</br> <b>Limites aceitos:</b>  '.$res['concen_min'].' à  '.$res['concen_max'].' '; 

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

                  Atividade: <span class="text-primary fw-bold">'.$nome_tipo_log.'</span>
                   <div class="separator my-2"></div>
                  Quem: <b>'.$nome_usuario.'</b>
                   <div class="separator my-2"></div>
                  Estação: <b> '.$nome_estacao.'</b>
                   <div class="separator my-2"></div>
                  <b>  Ação :</b> '.$tabela_log.' 
                    '.$retorno_leitura.'
                
                   <div class="separator my-2"></div>
                  Mídia Enviada: <span class="'.$css_dinamico.'"> '.$possui_midia.'</span>
                  | Dia: <b>'.$dia_format.'</b>


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

														<span>Nenhuma Atividade Recente gerada nas últimas 24 horas, para a Estação: <strong>'.$nome_estacao.'</strong>.</span>
                                                        
													</div>
												</div>';
         
      
    }

    $conexao=null;

    exit;
    
    
    } 
    //====[ conclui consulta para atividades no painel]===<<



    




