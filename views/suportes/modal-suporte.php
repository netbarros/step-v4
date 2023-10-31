<?php
 // buffer de saída de dados do php]
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$_SESSION['pagina_atual'] = 'Gerencia Suporte';

$id_suporte = $_GET['id'] ??'';


if($id_suporte==''){

    echo '<div class="alert alert-danger" role="alert">
    <h4 class="alert-heading">Erro!</h4>
    <p>Erro ao carregar suporte.</p>
    <hr>
    <p class="mb-0">Tente novamente.</p>
  </div>';

  exit();
}


if($id_suporte!=''){

   $sql=$conexao->query("SELECT s.*,

   u.id as id_quem_abriu,
   u.nome as nome_quem_abriu,
   u.nivel as nivel_quem_abriu,
   u.foto as foto_quem_abriu,
   uro.nome as nome_ro,
   uro.foto as foto_ro,
   uro.id as id_ro,
   uro.nivel as nivel_ro,
   usu.nome as nome_supervisor,
   usu.foto as foto_supervisor,
   usu.id as id_supervisor,
   usu.nivel as nivel_supervisor,
    sc.id_conversa,
   colab_ro.nome as nome_ro,
   colab_su.nome as nome_supervisor,
   un.nome_unidade_medida,
   o.nome_obra,
   o.id_obra,
   e.nome_estacao,
   e.id_estacao,
   pr.nome_parametro,
   pr.concen_min,
   pr.concen_max,
   pr.controle_concentracao,
   pr.origem_leitura_parametro,
   p.nome_ponto,
   p.latitude_p,
   p.longitude_p,
   c.nome as nome_contato,
   c.email_corporativo as email_contato,
   c.cel_corporativo as telefone_contato,
   uc.foto as foto_contato,
   uc.id as id_contato,
   uc.nivel as nivel_contato,
   tp.nome_suporte
   
   
   FROM suporte s
   INNER JOIN usuarios u ON u.id = s.quem_abriu
   LEFT JOIN suporte_conversas sc ON sc.id_suporte = s.id_suporte
   INNER JOIN tipo_suporte tp ON tp.id_tipo_suporte = s.tipo_suporte
   INNER JOIN estacoes e ON e.id_estacao = s.estacao
   INNER JOIN obras o ON o.id_obra = e.id_obra
   LEFT JOIN contatos c ON c.id_cliente = o.id_cliente
   LEFT JOIN pontos_estacao p ON p.id_ponto = s.plcode
   LEFT JOIN parametros_ponto pr ON pr.id_parametro = s.parametro
   LEFT JOIN unidade_medida un ON un.id_unidade_medida = pr.unidade_medida
   LEFT JOIN colaboradores colab_ro ON colab_ro.id_colaborador = e.ro
   LEFT JOIN colaboradores colab_su ON colab_su.id_colaborador = e.supervisor
   LEFT JOIN usuarios uro ON uro.bd_id = e.ro
   LEFT JOIN usuarios usu ON usu.bd_id = e.supervisor
   LEFT JOIN usuarios uc ON uc.email = c.email_corporativo
   WHERE s.id_suporte='$id_suporte'");

   $conta = $sql->rowCount();

   if($conta>0){


   $row = $sql->fetch(PDO::FETCH_ASSOC);

   
// dados contato cliente

$nome_contato =   substr($row['nome_contato'] ?? 'Ausente', 0, 15) ;  
$telefone_contato = $row['telefone_contato'] ?? 'Ausente';
$email_contato = $row['email_contato'] ?? 'Ausente';
$nivel_contato = $row['nivel_contato'] ?? 'Ausente';

$foto_contato = $row['foto_contato'] ?? '';

$brev_nome_contato = substr($nome_contato, 0, 1);

$retorno_foto_contato='';

if($foto_contato){

$filename = '/foto-perfil/' . $foto_contato;

if (file_exists($filename)) {

    $retorno_foto_contato = ' <img src="'.$filename.'" class="h-50 align-self-center" alt=""> ';
} else {

    $retorno_foto_contato = ' <span class="symbol-label bg-primary fs-2 text-inverse-primary fw-bold">' . $brev_nome_contato . '</span>';
}

}else{

$retorno_foto_contato = ' <span class="symbol-label bg-danger text-inverse-primary fs-2 fw-bold">' . $brev_nome_contato . '</span>';
}

// dados quem abriu
        $foto_user = $row['foto_quem_abriu'] ?? '';

        $nome_user = strlen($row['nome_quem_abriu']) > 10 ? substr($row['nome_quem_abriu'], 0, 15) . "." : $row['nome_quem_abriu'];  
        $brev_nome_user = substr($nome_user, 0, 1);

        $retorno_foto_quem_abriu='';

if($foto_user){

    $filename = '/foto-perfil/' . $foto_user;

      

        if (file_exists($filename)) {

            $retorno_foto_quem_abriu = ' <img src="'.$filename.'" class="h-50 align-self-center" alt=""> ';
        } else {

            $retorno_foto_quem_abriu = ' <span class="symbol-label bg-primary fs-2 text-inverse-primary fw-bold">' . $brev_nome_user . '</span>';
        }

    }else{

        $retorno_foto_quem_abriu = ' <span class="symbol-label bg-warning text-inverse-primary fs-2 fw-bold">' . $brev_nome_user . '</span>';
    }


// dados ro

  $foto_ro = $row['foto_ro'] ?? '';

    

    $nome_ro = strlen($row['nome_ro']) > 10 ? substr($row['nome_ro'], 0, 15) . "." : $row['nome_ro']; 
    $brev_nome_ro = substr($nome_ro, 0, 1);

    $retorno_foto_ro='';

if($foto_ro){

   

    $filename = '/foto-perfil/' . $foto_ro;

    if (file_exists($filename)) {

        $retorno_foto_ro = ' <img src="'.$filename.'" class="h-50 align-self-center" alt=""> ';
    } else {

        $retorno_foto_ro = ' <span class="symbol-label bg-primary fs-2 text-inverse-primary fw-bold">' . $brev_nome_ro . '</span>';
    }

}else{

    $retorno_foto_ro = ' <span class="symbol-label bg-warning text-inverse-primary fs-2 fw-bold">' . $brev_nome_ro . '</span>';
}


// dados supervisor


// dados ro

    
$foto_supervisor = $row['foto_supervisor'] ?? '';


$nome_supervisor = strlen($row['nome_supervisor']) > 9 ? substr($row['nome_supervisor'], 0, 15) . "." : $row['nome_supervisor']; 
$brev_nome_supervisor = substr($nome_supervisor, 0, 1);

$retorno_foto_supervisor='';

if($foto_supervisor){



$filename = '/foto-perfil/' . $foto_supervisor;

if (file_exists($filename)) {

    $retorno_foto_supervisor = ' <img src="'.$filename.'" class="h-50 align-self-center" alt=""> ';
} else {

    $retorno_foto_supervisor = ' <span class="symbol-label bg-primary fs-2 text-inverse-primary fw-bold">' . $brev_nome_supervisor . '</span>';
}

}else{

$retorno_foto_supervisor = ' <span class="symbol-label bg-warning text-inverse-primary fs-2 fw-bold">' . $brev_nome_supervisor . '</span>';
}



$controla_Conc =$row['controle_concentracao'];
$simbolo_indicador ='';
$controle_concentracao='';
if ($controla_Conc == '1') {

    $controle_concentracao = "Controla Somente Mínima";
    $simbolo_indicador = '>';
}

if ($controla_Conc == '2') {

    $controle_concentracao = "Controla Somente Máxima";
    $simbolo_indicador = '<';
}

if ($controla_Conc == '3') {

    $controle_concentracao = "Controla o valor Min e Max do Indicador";
    $simbolo_indicador = '<';
}


$leitura_suporte = $row['leitura_suporte'] ?? '';

$data_hoje = date('Y-m-d H:i:s');
$data_previsao = $row['data_prevista'] ?? '';

$status_suporte = $row['status_suporte'];

switch ($status_suporte) {
    case 1:
        $nome_status = 'Em aberto';
        $css_suporte = 'text-danger';
        break;

    case 2:
        $nome_status = 'Suporte em Andamento';
        $css_suporte = 'text-warning';
        break;

    case 3:
        
        $data_previsao_saida = date('d/m/Y H:i:s', strtotime($row['data_prevista']));

        if ($data_hoje > $data_previsao) {
        $nome_status = 'Dependendo de Terceiros, em Atraso, previsão: '.$data_previsao_saida;
        $css_suporte = 'text-danger';
        }else {

            $nome_status = 'Dependendo de Terceiros, previsto para: '.$data_previsao_saida;
            $css_suporte = 'text-warning';

        }
        break;

    case 4:
        $nome_status = 'Ticket de Suporte Finalizado';
        $css_suporte = 'text-success';
        break;

    case 5:
    
        $data_previsao_saida = date('d/m/Y H:i', strtotime($row['data_prevista']));

        if ($data_hoje > $data_previsao) {
        $nome_status = 'Prazo Informado, em Atraso, data prevista informada: <span class="text-light"> '.$data_previsao_saida.'</span>';
        $css_suporte = 'text-danger';

        }else {
        
            $nome_status = 'Ticket com prazo previsto para Finalizar em: <span class="text-light">'.$data_previsao_saida.'</span>';
            $css_suporte = 'text-warning';
        
        }
        break;

    case 6:
        $nome_status = 'Indicador Revogado';
        $css_suporte = 'text-info';
        break;

    case 7:
        $nome_status = 'Indicador Liberado';
        $css_suporte = 'text-primary';
        break;


        
}

?>

<script src="../../js/suportes/gerencia-suporte.js"></script>



    <!--begin::Modal content-->
    <div class="" >
        <!--begin::Modal header-->
        
        <div class="card m-4">
                            <div class="border border-warning border-dashed rounded w-580px py-3 px-4  ">
                                <!--begin::Label-->
                                    <div class="fw-semibold fs-6 text-warning">Razão da abertura do Ticket de Suporte:</div>
                                                <!--end::Label-->
                                    <!--begin::Number-->
                                    <div class="d-flex align-items-center text-gray-700">
                                    <?php $motivo_suporte = $row['motivo_suporte'] ? $row['motivo_suporte'] : 'Motivo Não Informado'; echo $motivo_suporte;?>
                                    </div>
                                    <!--end::Number-->  
                                    
                                    <div class="d-flex justify-content-end">
                <span class="<?= $css_suporte ?> fw-bold fs-6 me-1"><?= $nome_status ?></span>

            </div>
                            </div>
           

    </div>
     <!--end: card Supervisor -->
     
        <!--begin::Modal body-->
        <div class="fv-row py-lg-10 px-lg-10 d-flex flex-row ">

        
        
      
<!-- Lateral Esquerda:: usuarios relacionados -->
<div class="fv-row w-100 " style="min-width: 270px;">
    <!--begin::Header-->
    <div class="card-header border-0 pt-2 w-100">
        <h3 class="card-title align-items-start flex-column w-100">
			<span class="card-label fw-bold text-dark">Usuários Envolvidos:</span>

			<span class="text-muted mt-1 fw-bold fs-7">Data de Abertura: <span class="text-gray-600"><?php  echo $row['data_open'] ? (new DateTime($row['data_open']))->format('d/m/y H:i') : '' ?></span></span>
		</h3>

    
    </div>
    <!--end::Header-->

    <!--begin::Body-->
    <div class="pt-5 w-100">
                <!--begin::Item Contato-->
                <div class="d-flex flex-stack w-100">  
                <!--begin::Symbol-->
                <div class="symbol symbol-40px me-4">
                <?=$retorno_foto_contato;?>                        
                </div>
                <!--end::Symbol-->

                <!--begin::Section-->
                <div class="d-flex align-items-right flex-row-fluid flex-wrap w-100">
                    <!--begin:Author-->                    
                    <div class="flex-grow-1 me-2 w-100">
                    <a href="../../views/conta-usuario/overview.php?id=<?$row['id_contato'];?>" target="_blank" class="text-gray-800 text-hover-primary fs-6 fw-bold"><?=$nome_contato;?></a>
                        
                    <span class="badge badge-light-secondary">Contato Cliente</span>
                                
                        <span class="text-muted fw-semibold d-block fs-7"><?=$nivel_contato;?></span>
                    </div>
                    <!--end:Author-->                      
                  
                </div>
                <!--end::Section-->
                <a href="javascript:;" class="btn btn-sm btn-light btn-active-light-primary" style="width: 30px; height: 30px; display: flex; justify-content: center; align-items: center; text-align: center; padding: 0;"  onclick="storeDataAttributesChatSuporte(this)"  data-kt-drawer-show="true" data-kt-drawer-target="#kt_drawer_chat" data-id_suporte='<?=$row['id_suporte'];?>' data-id_conversa='<?php echo $row['id_conversa'] ?? '';?>' data-id_usuario='<?php echo $row['id_contato'] ?? '';?>'>
                <span class="svg-icon svg-icon-muted svg-icon-2 px-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"/>
                        <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"/>
                        <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"/>
                    </svg>
                </span>
            <!--end::Svg Icon-->
            </a>
            </div>
            <!--end::Item-->

                            <!--begin::Separator-->
                <div class="separator separator-dashed my-4 w-100"></div>
                <!--end::Separator-->

                        <!--begin::Item RO-->

                        <div class="d-flex flex-stack w-100">  
                <!--begin::Symbol-->
                <div class="symbol symbol-40px me-5">
                <?=$retorno_foto_ro;?>                     
                </div>
                <!--end::Symbol-->

                <!--begin::Section-->
                <div class="d-flex align-items-center flex-row-fluid flex-wrap w-100">
                    <!--begin:Author-->                    
                    <div class="flex-grow-1 me-2">
                    <a href="../../views/conta-usuario/overview.php?id=<?=$row['id_ro'];?>" target="_blank" class="text-gray-800 text-hover-primary fs-6 fw-bold"><?=$nome_ro;?></a>
                    <span class="badge badge-light-secondary">RO Direto</span>
                        <span class="text-muted fw-semibold d-block fs-7">RO</span>
                    </div>
                    <!--end:Author-->                      
                    
                      
                </div>
                <a href="javascript:;" class="btn btn-sm btn-light btn-active-light-primary" style="width: 30px; height: 30px; display: flex; justify-content: center; align-items: center; text-align: center; padding: 0;"  onclick="storeDataAttributesChatSuporte(this)"  data-kt-drawer-show="true" data-kt-drawer-target="#kt_drawer_chat" data-id_suporte='<?=$row['id_suporte'];?>' data-id_conversa='<?php echo $row['id_conversa'] ?? '';?>' data-id_usuario='<?php echo $row['id_ro'] ?? '';?>'>
                <span class="svg-icon svg-icon-muted svg-icon-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"/>
                        <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"/>
                        <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"/>
                    </svg>
                </span>
            <!--end::Svg Icon-->
            </a>
                <!--end::Section-->
            </div>
            <!--end::Item-->

    

                            <!--begin::Separator-->
                <div class="separator separator-dashed my-4"></div>
                <!--end::Separator-->

                    <!--begin::Item Supervisor-->
            <div class="d-flex flex-stack w-100">  
                <!--begin::Symbol-->
                <div class="symbol symbol-40px me-4">
                <?=$retorno_foto_supervisor;?>                        
                </div>
                <!--end::Symbol-->

                <!--begin::Section-->
                <div class="d-flex align-items-center flex-row-fluid flex-wrap w-100">
                    <!--begin:Author-->                    
                    <div class="flex-grow-1 me-2">
                    <a href="../../views/conta-usuario/overview.php?id=<?=$row['id_supervisor'];?>" target="_blank" class="text-gray-800 text-hover-primary fs-6 fw-bold"><?=$nome_supervisor;?></a>
                        
                    <span class="badge badge-light-secondary">SU Direto</span>
                        <span class="text-muted fw-semibold d-block fs-7"><?=$row['nivel_supervisor'];?></span>
                    </div>
                    <!--end:Author-->                      
                    
                    
                </div>
                <a href="javascript:;" class="btn btn-sm btn-light btn-active-light-primary" style="width: 30px; height: 30px; display: flex; justify-content: center; align-items: center; text-align: center; padding: 0;"  onclick="storeDataAttributesChatSuporte(this)"  data-kt-drawer-show="true" data-kt-drawer-target="#kt_drawer_chat" data-id_suporte='<?=$row['id_suporte'];?>' data-id_conversa='<?php echo $row['id_conversa'] ?? '';?>' data-id_usuario='<?php echo $row['id_supervisor'] ?? '';?>'>
                <span class="svg-icon svg-icon-muted svg-icon-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"/>
                        <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"/>
                        <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"/>
                    </svg>
                </span>
            <!--end::Svg Icon-->
            </a>
                <!--end::Section-->
            </div>
            <!--end::Item-->

                            <!--begin::Separator-->
                <div class="separator separator-dashed my-4"></div>
                <!--end::Separator-->
              
                    <!--begin::Item Quem abriu-->
       
                    <div class="d-flex flex-stack my-10 w-100">  
                <!--begin::Symbol-->
                <div class="symbol symbol-40px me-4">
                <?=$retorno_foto_quem_abriu;?>                        
                </div>
                <!--end::Symbol-->

                <!--begin::Section-->
                <div class="d-flex align-items-center flex-row-fluid flex-wrap w-100">
                    <!--begin:Author-->                    
                    <div class="flex-grow-1 me-2">
                    <a href="../../views/conta-usuario/overview.php?id=<?=$row['id_quem_abriu'];?>" target="_blank" class="text-gray-800 text-hover-primary fs-6 fw-bold"><?=$nome_user;?></a>
                        
                    <span class="badge badge-light-secondary">Usuário Responsável</span>
                        <span class="text-muted fw-semibold d-block fs-7"><?=$row['nivel_quem_abriu'];?></span>
                    </div>
                    <!--end:Author-->                      
                    
                        
                </div>
                <a href="javascript:;" class="btn btn-sm btn-light btn-active-light-primary" style="width: 30px; height: 30px; display: flex; justify-content: center; align-items: center; text-align: center; padding: 0;"  onclick="storeDataAttributesChatSuporte(this)"  data-kt-drawer-show="true" data-kt-drawer-target="#kt_drawer_chat" data-id_suporte='<?=$row['id_suporte'];?>' data-id_conversa='<?php echo $row['id_conversa'] ?? '';?>' data-id_usuario='<?php echo $row['id_quem_abriu'] ?? '';?>'>
                <span class="svg-icon svg-icon-muted svg-icon-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"/>
                        <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"/>
                        <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"/>
                    </svg>
                </span>
            <!--end::Svg Icon-->
            </a>
                <!--end::Section-->
            </div>
            <!--end::Item-->
     
            <div class="card shadow-sm card-flush h-lg-50 d-flex flex-row-auto flex-center d-grid gap-3 mx-auto w-100">  
                            <!--begin::Header-->
                            <div class="card-header pt-5 w-100">
                                <!--begin::Title-->
                                <h3 class="card-title text-gray-800 fw-bold">Informações</h3>
                                <!--end::Title-->

                            </div>
                            <!--end::Header-->

                            <!--begin::Body-->
                            <div class="card-body pt-2 w-100">                 
                                            <!--begin::Item-->
                                    <div class="d-flex flex-stack">




                                        <!--begin::Section-->
                                        <span  class="text-primary fw-semibold fs-7 me-2">
                                            <?php

                                                if ($row['status_suporte'] == '4') {
                                                    $data_atual = new \DateTime($row['data_close']);
                                                } else {
                                                    $data_atual = new \DateTime(date('Y-m-d H:i'));
                                                }

                                                date_default_timezone_set('America/Sao_Paulo');

                                                $dateStart = new \DateTime($row['data_open']);
                                                $dateNow   = $data_atual;

                                                $dateDiff = $dateStart->diff($dateNow);

                                                $result = ' <li class="d-flex align-items-center py-2">
                                                <span class="bullet bullet-vertical bg-primary me-5"></span> <span class="text-gray-700 me-1"> '.$dateDiff->d . ' </span> <span class="me-1">dias e</span> <span class="text-gray-700 me-1"> ' . $dateDiff->h . ' h </span> ' . $dateDiff->i . ' min
                                            </li>'; 

                                               

                                                echo $result;

                                            ?>

                                        </span>                   
                                        <!--end::Section--> 


                                        
                                    <!--begin::Action-->
                                    <button type="button" class="btn btn-icon btn-sm h-auto btn-color-gray-400 btn-active-color-primary justify-content-end" data-bs-toggle="tooltip" data-bs-placement="top" title="Tempo em Aberto">
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr095.svg-->
                        <span class="svg-icon svg-icon-2x">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.3" d="M20.9 12.9C20.3 12.9 19.9 12.5 19.9 11.9C19.9 11.3 20.3 10.9 20.9 10.9H21.8C21.3 6.2 17.6 2.4 12.9 2V2.9C12.9 3.5 12.5 3.9 11.9 3.9C11.3 3.9 10.9 3.5 10.9 2.9V2C6.19999 2.5 2.4 6.2 2 10.9H2.89999C3.49999 10.9 3.89999 11.3 3.89999 11.9C3.89999 12.5 3.49999 12.9 2.89999 12.9H2C2.5 17.6 6.19999 21.4 10.9 21.8V20.9C10.9 20.3 11.3 19.9 11.9 19.9C12.5 19.9 12.9 20.3 12.9 20.9V21.8C17.6 21.3 21.4 17.6 21.8 12.9H20.9Z" fill="currentColor"/>
                        <path d="M16.9 10.9H13.6C13.4 10.6 13.2 10.4 12.9 10.2V5.90002C12.9 5.30002 12.5 4.90002 11.9 4.90002C11.3 4.90002 10.9 5.30002 10.9 5.90002V10.2C10.6 10.4 10.4 10.6 10.2 10.9H9.89999C9.29999 10.9 8.89999 11.3 8.89999 11.9C8.89999 12.5 9.29999 12.9 9.89999 12.9H10.2C10.4 13.2 10.6 13.4 10.9 13.6V13.9C10.9 14.5 11.3 14.9 11.9 14.9C12.5 14.9 12.9 14.5 12.9 13.9V13.6C13.2 13.4 13.4 13.2 13.6 12.9H16.9C17.5 12.9 17.9 12.5 17.9 11.9C17.9 11.3 17.5 10.9 16.9 10.9Z" fill="currentColor"/>
                        </svg>
                        </span>
                        <!--end::Svg Icon-->               
                     </button>                
                                        <!--end::Action-->
                                        
                                        
                                    </div>
                                    <!--end::Item-->

                                                    <!--begin::Separator-->
                                        <div class="separator separator-dashed my-3"></div>
                                        <!--end::Separator-->
                                    
                                    


<?php  if($leitura_suporte!=''){




             


            $busca_leitura = $conexao->query("SELECT leitura_entrada, leitura_saida, id_rmm FROM rmm WHERE id_rmm='$row[id_rmm_suporte]'");

            $verifica = $busca_leitura->rowCount();

            if($verifica > 0){

                
echo '        <!--begin::Item-->
<div class="d-flex flex-stack">
    <!--begin::Section-->
    <span  class="text-primary fw-semibold fs-6 me-2">';


                $row_leitura = $busca_leitura->fetch(PDO::FETCH_ASSOC);


               // Verificando se $row_leitura é um array antes de acessar a chave 'leitura_entrada'
            if (is_array($row_leitura) && isset($row_leitura['leitura_entrada'])) {
                $leitura_A = $row_leitura['leitura_entrada'];
            } else {
                $leitura_A = null; // ou atribua um valor padrão caso desejar
            }
               


                // Verificando se $row_leitura é um array antes de acessar a chave 'leitura_entrada'
            if (is_array($row_leitura) && isset($row_leitura['leitura_saida'])) {
                $leitura_B = $row_leitura['leitura_saida'];
            } else {
                $leitura_B = null; // ou atribua um valor padrão caso desejar
            }

                $leitura_rmm ='';
                if ($row['origem_leitura_parametro']== '1') {


                    $leitura_rmm = $leitura_A;
                }

                if ($row['origem_leitura_parametro'] == '2') {


                    $leitura_rmm = $leitura_A + $leitura_B;
                }



                if ($row['origem_leitura_parametro'] == '3') {

                    $leitura_rmm = $leitura_A . ' e ' . $leitura_B;
                }

              
                echo ' <li class="d-flex align-items-center py-2">
                <span class="bullet bullet-vertical bg-primary me-5"></span> <span class="text-gray-700 ">' . $controle_concentracao . '</span>
            </li>';


                if ($leitura_rmm != '') {

                    //$leitura_rmm_x = $row['id_rmm_suporte'].'<br><span class="text-light">Valor Informado pela Operação:</span><span class="text-warning">  ' . $leitura_rmm . '   ' . $row['nome_unidade_medida'] . '</span>';



                echo ' <li class="d-flex align-items-center py-2">
                <span class="bullet bullet-vertical bg-primary me-5"></span> <span class="text-gray-700 me-2">ID Leitura:</span> ' . $row['id_rmm_suporte'].'
            </li>';



                

                 
              echo ' <li class="d-flex align-items-center py-2">
              <span class="bullet bullet-vertical bg-danger me-5"></span> <span class="text-danger me-2">INFORMADO:</span> <span class="badge badge-light-primary  badge-lg" ><strong class="text-gray-800 fs-2">  ' . $leitura_rmm . '  ' . $row['nome_unidade_medida']  . '</strong></span> 
          </li>'; 



               
                }


                echo '</span>                   
                <!--end::Section--> 
                
                <!--begin::Action-->
                <button type="button" class="btn btn-icon btn-sm h-auto btn-color-gray-400 btn-active-color-primary justify-content-end" data-bs-toggle="tooltip" data-bs-placement="top" title="Informações da Leitura">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr095.svg-->
<span class="svg-icon svg-icon-2x">
<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"/>
<rect x="11" y="17" width="7" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"/>
<rect x="11" y="9" width="2" height="2" rx="1" transform="rotate(-90 11 9)" fill="currentColor"/>
</svg>
</span>
<!--end::Svg Icon-->                
</button>                
                <!--end::Action-->
            </div>
        
                         <!--begin::Separator-->
                         <div class="separator separator-dashed my-3"></div>
                <!--end::Separator-->';


               


            }else { echo 'Leitura Rmm não localizada.';}

                     
   

$sql_regra = null;
} ?>
  <!--end::Item-->


  <?php

if($row['controle_concentracao']!=''){                                    
                                    
    echo '          <!--begin::Item-->
                                    <div class="d-flex flex-stack">
                                        <!--begin::Section-->
                                        <a href="javascript:;" class="text-primary fw-semibold fs-7 me-2">




    <li class="d-flex align-items-center py-2">
    <span class="bullet bullet-vertical bg-danger me-5"></span><span class="text-warning me-2"> '.($row['concen_min'] ?? '').'</span><span class="text-gray-700 me-2"> '.$simbolo_indicador.' </span><span class="text-danger me-2">'.($row['concen_max'] ?? '').' </span> '.($row['nome_unidade_medida'] ?? '').'
</li>'; 

echo '</a>                   
<!--end::Section--> 

<!--begin::Action-->
<button type="button" class="btn btn-icon btn-sm h-auto btn-color-gray-400 btn-active-color-primary justify-content-end" data-bs-toggle="tooltip" data-bs-placement="top" title="Parâmetros de Controle do Indicador" >
    <!--begin::Svg Icon | path: icons/duotune/arrows/arr095.svg-->
<span class="svg-icon svg-icon-2x">
<svg width="25" height="28" viewBox="0 0 25 28" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M7.40094 3.4401V2.10677C7.40033 1.76658 7.52977 1.43903 7.76277 1.19116C7.99577 0.943285 8.3147 0.793848 8.65427 0.773438H16.0009C16.3381 0.797099 16.6538 0.947953 16.884 1.19548C17.1142 1.44301 17.2418 1.76874 17.2409 2.10677V3.4401C17.2418 3.77813 17.1142 4.10386 16.884 4.35139C16.6538 4.59892 16.3381 4.74978 16.0009 4.77344H8.65427C8.3147 4.75303 7.99577 4.60359 7.76277 4.35572C7.52977 4.10785 7.40033 3.78029 7.40094 3.4401ZM24.0009 10.3068V19.9334C24.0168 20.8755 23.847 21.8114 23.5011 22.6878C23.1552 23.5642 22.6401 24.3639 21.9851 25.0412C21.3301 25.7185 20.5481 26.2601 19.6837 26.6351C18.8194 27.0101 17.8897 27.2111 16.9476 27.2268H7.08094C5.18074 27.2022 3.36795 26.4246 2.04054 25.0646C0.713134 23.7047 -0.0204132 21.8737 0.000939745 19.9734V10.3468C-0.0270515 8.61371 0.571086 6.92871 1.68552 5.60118C2.79996 4.27366 4.35589 3.39271 6.06761 3.1201V3.4401C6.06729 4.1337 6.33724 4.80013 6.82016 5.29799C7.30309 5.79585 7.96099 6.08596 8.65427 6.10677H16.0009C16.6919 6.08257 17.3464 5.79097 17.8265 5.29348C18.3066 4.79598 18.5747 4.13147 18.5743 3.4401V3.21344C20.1405 3.62092 21.5255 4.54049 22.5088 5.82584C23.4921 7.1112 24.0174 8.68849 24.0009 10.3068ZM7.80094 15.4134L10.9743 19.3334C11.0666 19.4457 11.1826 19.5362 11.314 19.5985C11.4454 19.6607 11.5889 19.6931 11.7343 19.6934C11.8856 19.6924 12.0345 19.6557 12.1689 19.5861C12.3033 19.5166 12.4193 19.4163 12.5076 19.2934L16.8543 13.4401C16.9828 13.2317 17.0357 12.9855 17.0041 12.7427C16.9726 12.4999 16.8585 12.2754 16.6809 12.1068C16.4748 11.9647 16.2224 11.9064 15.9748 11.9435C15.7272 11.9806 15.503 12.1105 15.3476 12.3068L11.7476 17.1468L9.33427 14.1601C9.17326 13.9701 8.94633 13.8479 8.69903 13.8183C8.45172 13.7886 8.20234 13.8536 8.00094 14.0001C7.79628 14.1676 7.66209 14.4059 7.62504 14.6677C7.58799 14.9296 7.65078 15.1958 7.80094 15.4134Z" fill="currentColor"/>
</svg>
</span>
<!--end::Svg Icon-->                </button>                
<!--end::Action-->
</div>
<!--end::Item-->
';

   // echo ($row['concen_min'] ?? '').'<span class="text-gray-700"> < > </span>'.($row['concen_max'] ?? '').' '.($row['nome_unidade_medida'] ?? '');
}

?>

                                        
                                    
                                    
                            </div>
                            <!--end::Body-->
                        </div>

              
            
    </div>
    <!--end::Body-->
</div>



     <!-- Conteúdo Central:: dados suporte -->
     <div class="fv-row bg-light shadow-sm m-4 min-w-650px ">



     
<div class="card-header ">
<div class="py-2 ">
<ul class="nav nav-pills nav-pills-custom mb-2 " role="tablist">
                            <!--begin::Item--> 
                <li class="nav-item mb-3 me-3 me-lg-6" role="presentation">
                    <!--begin::Link--> 
                    <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-primary flex-column overflow-hidden w-80px h-85px pt-5 pb-2 active"
                    id="suporte_aba_projeto" data-bs-toggle="pill" href="#suporte_aba_projeto_tab_1" aria-selected="true" role="tab">
                        <!--begin::Icon-->
                        <div class="nav-icon mb-3">  
                        <i class="fas fa-solid fa-building-shield fs-1 p-0"></i>    
                                                                                                                                                                                           
                        </div>
                        <!--end::Icon-->
                        
                        <!--begin::Title-->
                        <span class="nav-text text-gray-800 fw-bold fs-6 lh-1">
                            Projeto                        </span> 
                        <!--end::Title-->
                        
                        <!--begin::Bullet-->
                        <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-primary"></span>
                        <!--end::Bullet-->
                    </a>
                    <!--end::Link-->
                </li>
                <!--end::Item--> 
                            <!--begin::Item--> 
                <li class="nav-item mb-3 me-3 me-lg-6" role="presentation">
                    <!--begin::Link--> 
                    <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-primary flex-column overflow-hidden w-80px h-85px pt-5 pb-2"
                     id="suporte_aba_nucleo" data-bs-toggle="pill" href="#suporte_aba_nucleo_tab_1" aria-selected="false" tabindex="-1" role="tab">
                        <!--begin::Icon-->
                        <div class="nav-icon mb-3">        
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  fill="currentColor" class="bi bi-ubuntu" viewBox="0 0 16 16">
                        <path d="M2.273 9.53a2.273 2.273 0 1 0 0-4.546 2.273 2.273 0 0 0 0 4.547Zm9.467-4.984a2.273 2.273 0 1 0 0-4.546 2.273 2.273 0 0 0 0 4.546ZM7.4 13.108a5.535 5.535 0 0 1-3.775-2.88 3.273 3.273 0 0 1-1.944.24 7.4 7.4 0 0 0 5.328 4.465c.53.113 1.072.169 1.614.166a3.253 3.253 0 0 1-.666-1.9 5.639 5.639 0 0 1-.557-.091Zm3.828 2.285a2.273 2.273 0 1 0 0-4.546 2.273 2.273 0 0 0 0 4.546Zm3.163-3.108a7.436 7.436 0 0 0 .373-8.726 3.276 3.276 0 0 1-1.278 1.498 5.573 5.573 0 0 1-.183 5.535 3.26 3.26 0 0 1 1.088 1.693ZM2.098 3.998a3.28 3.28 0 0 1 1.897.486 5.544 5.544 0 0 1 4.464-2.388c.037-.67.277-1.313.69-1.843a7.472 7.472 0 0 0-7.051 3.745Z"/>
                        </svg>                                                                                                                                                          
                        </div>
                        <!--end::Icon-->
                        
                        <!--begin::Title-->
                        <span class="nav-text text-gray-800 fw-bold fs-6 lh-1">
                            Núcleo                        </span> 
                        <!--end::Title-->
                        
                        <!--begin::Bullet-->
                        <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-primary"></span>
                        <!--end::Bullet-->
                    </a>
                    <!--end::Link-->
                </li>
                <!--end::Item--> 
                            <!--begin::Item--> 
                <li class="nav-item mb-3 me-3 me-lg-6" role="presentation">
                    <!--begin::Link--> 
                    <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-primary flex-column overflow-hidden w-80px h-85px pt-5 pb-2"
                     id="suporte_aba_plcode" data-bs-toggle="pill" href="#suporte_aba_plcode_tab_1" aria-selected="false" tabindex="-1" role="tab">
                        <!--begin::Icon-->
                        <div class="nav-icon mb-3">        
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  fill="currentColor" class="bi bi-qr-code-scan" viewBox="0 0 16 16">
                    <path d="M0 .5A.5.5 0 0 1 .5 0h3a.5.5 0 0 1 0 1H1v2.5a.5.5 0 0 1-1 0v-3Zm12 0a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0V1h-2.5a.5.5 0 0 1-.5-.5ZM.5 12a.5.5 0 0 1 .5.5V15h2.5a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5Zm15 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H15v-2.5a.5.5 0 0 1 .5-.5ZM4 4h1v1H4V4Z"/>
                    <path d="M7 2H2v5h5V2ZM3 3h3v3H3V3Zm2 8H4v1h1v-1Z"/>
                    <path d="M7 9H2v5h5V9Zm-4 1h3v3H3v-3Zm8-6h1v1h-1V4Z"/>
                    <path d="M9 2h5v5H9V2Zm1 1v3h3V3h-3ZM8 8v2h1v1H8v1h2v-2h1v2h1v-1h2v-1h-3V8H8Zm2 2H9V9h1v1Zm4 2h-1v1h-2v1h3v-2Zm-4 2v-1H8v1h2Z"/>
                    <path d="M12 9h2V8h-2v1Z"/>
                    </svg>                                                                                                                                                                     
                        </div>
                        <!--end::Icon-->
                        
                        <!--begin::Title-->
                        <span class="nav-text text-gray-800 fw-bold fs-6 lh-1">
                            PLCode                        </span> 
                        <!--end::Title-->
                        
                        <!--begin::Bullet-->
                        <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-primary"></span>
                        <!--end::Bullet-->
                    </a>
                    <!--end::Link-->
                </li>
                <!--end::Item--> 
                            <!--begin::Item--> 
                <li class="nav-item mb-3 me-3 me-lg-6" role="presentation">
                    <!--begin::Link--> 
                    <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-primary flex-column overflow-hidden w-80px h-85px pt-5 pb-2"
                     id="suporte_aba_indicador" data-bs-toggle="pill" href="#suporte_aba_indicador_tab_1" aria-selected="false" tabindex="-1" role="tab">
                        <!--begin::Icon-->
                      <div class="nav-icon mb-3">         
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  fill="currentColor" class="bi bi-moisture " viewBox="0 0 16 16">
  <path d="M13.5 0a.5.5 0 0 0 0 1H15v2.75h-.5a.5.5 0 0 0 0 1h.5V7.5h-1.5a.5.5 0 0 0 0 1H15v2.75h-.5a.5.5 0 0 0 0 1h.5V15h-1.5a.5.5 0 0 0 0 1h2a.5.5 0 0 0 .5-.5V.5a.5.5 0 0 0-.5-.5h-2zM7 1.5l.364-.343a.5.5 0 0 0-.728 0l-.002.002-.006.007-.022.023-.08.088a28.458 28.458 0 0 0-1.274 1.517c-.769.983-1.714 2.325-2.385 3.727C2.368 7.564 2 8.682 2 9.733 2 12.614 4.212 15 7 15s5-2.386 5-5.267c0-1.05-.368-2.169-.867-3.212-.671-1.402-1.616-2.744-2.385-3.727a28.458 28.458 0 0 0-1.354-1.605l-.022-.023-.006-.007-.002-.001L7 1.5zm0 0-.364-.343L7 1.5zm-.016.766L7 2.247l.016.019c.24.274.572.667.944 1.144.611.781 1.32 1.776 1.901 2.827H4.14c.58-1.051 1.29-2.046 1.9-2.827.373-.477.706-.87.945-1.144zM3 9.733c0-.755.244-1.612.638-2.496h6.724c.395.884.638 1.741.638 2.496C11 12.117 9.182 14 7 14s-4-1.883-4-4.267z"/>
</svg>                                                                                                                                                                 
                        </div>
                        <!--end::Icon-->
                        
                        <!--begin::Title-->
                        <span class="nav-text text-gray-800 fw-bold fs-6 lh-1">
                            Indicador                        </span> 
                        <!--end::Title-->
                        
                        <!--begin::Bullet-->
                        <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-primary"></span>
                        <!--end::Bullet-->
                    </a>
                    <!--end::Link-->
                </li>
                <!--end::Item--> 
                            <!--begin::Item--> 
                <li class="nav-item mb-3 me-3 me-lg-6" role="presentation">
                    <!--begin::Link--> 
                    <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-primary flex-column overflow-hidden w-80px h-85px pt-5 pb-2"
                     id="suporte_aba_categoria" data-bs-toggle="pill" href="#suporte_aba_categoria_tab_1" aria-selected="false" tabindex="-1" role="tab">
                        <!--begin::Icon-->
                        <div class="nav-icon mb-3">        
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bookmark-check" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
  <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5V2zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1H4z"/>
</svg>                                                                                                                                                                    
                        </div>
                        <!--end::Icon-->
                        
                        <!--begin::Title-->
                        <span class="nav-text text-gray-800 fw-bold fs-6 lh-1">
                            Categoria                        </span> 
                        <!--end::Title-->
                        
                        <!--begin::Bullet-->
                        <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-primary"></span>
                        <!--end::Bullet-->
                    </a>
                    <!--end::Link-->
                </li>
                <!--end::Item--> 



           <!--begin::Item--> 
             <li class="nav-item mb-3 me-3 me-lg-6" role="presentation">
                    <!--begin::Link--> 
                    <a class="nav-link btn btn-outline-warning btn-flex btn-color-muted btn-active-color-warning flex-column overflow-hidden w-70px h-85px pt-5 pb-2"
                     id="suporte_aba_finaliza" data-bs-toggle="pill" href="#suporte_aba_finaliza_tab_1" aria-selected="false" tabindex="-1" role="tab">
                        <!--begin::Icon-->
                        <div class="nav-icon mb-3">        
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-megaphone-fill" viewBox="0 0 16 16">
                        <path d="M13 2.5a1.5 1.5 0 0 1 3 0v11a1.5 1.5 0 0 1-3 0v-11zm-1 .724c-2.067.95-4.539 1.481-7 1.656v6.237a25.222 25.222 0 0 1 1.088.085c2.053.204 4.038.668 5.912 1.56V3.224zm-8 7.841V4.934c-.68.027-1.399.043-2.008.053A2.02 2.02 0 0 0 0 7v2c0 1.106.896 1.996 1.994 2.009a68.14 68.14 0 0 1 .496.008 64 64 0 0 1 1.51.048zm1.39 1.081c.285.021.569.047.85.078l.253 1.69a1 1 0 0 1-.983 1.187h-.548a1 1 0 0 1-.916-.599l-1.314-2.48a65.81 65.81 0 0 1 1.692.064c.327.017.65.037.966.06z"/>
                        </svg>                                                                                                                                                                 
                        </div>
                        <!--end::Icon-->
                        
                        <!--begin::Title-->
                        <span class="nav-text text-gray-800 fw-bold fs-6 lh-1">
                        Atender                        </span> 
                        <!--end::Title-->
                        
                        <!--begin::Bullet-->
                        <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-warning"></span>
                        <!--end::Bullet-->
                    </a>
                    <!--end::Link-->
                </li>
                <!--end::Item--> 
                        
        </ul>


        <div class="tab-content" id="myTabContent">

        <!-- aba projeto -->

    <div class="tab-pane  fade show active" id="suporte_aba_projeto_tab_1" role="tabpanel">
    

        <span class="badge badge-lg badge-light-primary fs-2"><?=$row['nome_obra'];?></span>

        <div class="card-header border-0 pt-2 w-100">
        <h3 class="card-title align-items-start flex-column w-100">
			<span class="card-label fw-bold text-dark">Tratativas Realizadas <small>No Ticket atual</small></span>

			
		</h3>

    
    </div>

        <?php  
        
      // Prepare e execute a consulta SQL  - listando Alterações do Suporte (LOG)
        $stmt = $conexao->prepare("SELECT * FROM log_suporte lg
        INNER JOIN usuarios u ON u.id = lg.id_usuario
        WHERE lg.id_suporte = :id_suporte");
        $stmt->execute([':id_suporte' => $row['id_suporte']]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
        ?>

        <!-- Verifique se a consulta retornou resultados -->
<?php if (!empty($result)): ?>
    <!-- Tabela responsiva do Bootstrap -->
    <div class="table-responsive  border rounded border-gray-300 hover-scroll h-300px px-2">
        <table class="table table-rounded table-striped border gy-7 gs-7">
            <thead>
            <tr class="fw-semibold fs-7 text-primary border-bottom border-gray-200 py-4">
                    <th>ID</th>
                    <th>Responsável</th>
                    <th>Motivo</th>
                    <th>Data</th>
                    <th>Status</th>
                    <!-- Adicione mais colunas de acordo com sua tabela -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $log_suporte):
                    
                    $status_suporte = $log_suporte['status_suporte'];
        switch ($status_suporte) {
            case 1:
                $nome_status = 'Em aberto';
                $css_suporte = 'danger';
                break;

            case 2:
                $nome_status = 'Em andamento';
                $css_suporte = 'warning';
                break;
            case 3:
                $nome_status = 'Terceirizado';
                $css_suporte = 'info';
                break;
            case 4:
                $nome_status = 'Finalizado';
                $css_suporte = 'success';
                break;

                case 5:
                    $nome_status = 'Com Previsão';
                    $css_suporte = 'warning';
                    break;

            case 6:
                $nome_status = 'Ind. revogado';
                $css_suporte = 'info';
                break;
            case 7:
                $nome_status = 'Ind. liberado';
                $css_suporte = 'primary';
                break;


                default:
                # code...

                $nome_status = '##';
                $css_suporte = 'info';
                break;
        }
        
        ?>
                    <tr >
                        <td><?php echo $log_suporte['id_log_suporte']; ?></td>
                        <td><?php echo $log_suporte['nome']; ?></td>
                        <td><?php echo $log_suporte['motivo_suporte_log']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($log_suporte['data_log'])); ?></td>
                        <td><?php echo ' <span class="badge badge-exclusive badge-light-' . $css_suporte . ' fw-bold fs-7 px-2 py-1 ms-1">' . $nome_status . '</span>'; ?></td>
                        
                        <!-- Adicione mais colunas de acordo com sua tabela -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <!-- Alerta do Bootstrap informando que não há conversas em andamento -->
    <div class="alert alert-warning" role="alert">
        Não há histórico de Tratativas para este Suporte.
    </div>
<?php endif; ?>




<div class="card-header border-0 pt-2 w-100">
        <h3 class="card-title align-items-start flex-column w-100">
			<span class="card-label fw-bold text-dark">Histórico do Chat <small>No Ticket atual</small></span>

			
		</h3>

    
    </div>



<?php  
        
        // Prepare e execute a consulta SQL  - listando (conversas) CHAT deste Suporte
          $stmt = $conexao->prepare("SELECT lg.* ,
          ur.nome as remetente,
          ud.nome as destinatario

          FROM log_conversa lg
          INNER JOIN usuarios ur ON ur.id = lg.id_remetente
          INNER JOIN usuarios ud ON ud.id = lg.id_destinatario
          WHERE lg.id_suporte = :id_suporte");
          $stmt->execute([':id_suporte' => $row['id_suporte']]);
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
          
          ?>
  
          <!-- Verifique se a consulta retornou resultados -->
  <?php if (!empty($result)): ?>
      <!-- Tabela responsiva do Bootstrap -->
      <div class="table-responsive  border rounded border-gray-300 hover-scroll h-300px px-2">
          <table class="table table-rounded table-striped border gy-7 gs-7">
              <thead>
              <tr class="fw-semibold fs-7 text-primary border-bottom border-gray-200 py-4">
                      <th>ID</th>
                      <th>Remetente</th>
                      <th>Destinatário</th>
                      <th>Chat</th>
                      <th>Data</th>
                      <th>Status</th>
                      <!-- Adicione mais colunas de acordo com sua tabela -->
                  </tr>
              </thead>
              <tbody>
                  <?php foreach ($result as $log_suporte):
                      
                      $status_suporte = $log_suporte['status_log_conversa'];
          switch ($status_suporte) {
              case 1:
                  $nome_status = 'Nova';
                  $css_suporte = 'warning';
                  break;
  
              case 2:
                  $nome_status = 'Recebida';
                  $css_suporte = 'warning';
                  break;
              case 3:
                  $nome_status = 'Lida';
                  $css_suporte = 'success';
                  break;

                  default:
                  # code...

                  $nome_status = '##';
                  $css_suporte = 'info';
                  break;
             
          }

            
          ?>
                      <tr >
                          <td><?php echo $log_suporte['id_log_conversa']; ?></td>
                          <td><?php echo $log_suporte['remetente']; ?></td>
                          <td><?php echo $log_suporte['destinatario']; ?></td>
                          <td><?php echo $log_suporte['conversa']; ?></td>
                          <td><?php echo date('d/m/Y H:i', strtotime($log_suporte['data_envio'])); ?></td>
                          <td><?php echo ' <span class="badge badge-exclusive badge-light-' . $css_suporte . ' fw-bold fs-7 px-2 py-1 ms-1">' . $nome_status . '</span>'; ?></td>
                          
                          <!-- Adicione mais colunas de acordo com sua tabela -->
                      </tr>
                  <?php endforeach; ?>
              </tbody>
          </table>
      </div>
  <?php else: ?>
      <!-- Alerta do Bootstrap informando que não há conversas em andamento -->
      <div class="alert alert-warning" role="alert">
          Nenhum Chat iniciado para este Ticket, até o momento.
      </div>
  <?php endif; ?>


    
    </div>




    <div class="tab-pane fade " id="suporte_aba_nucleo_tab_1" role="tabpanel">
   
    <span class="badge badge-lg badge-light-primary fs-2">   <?=$row['nome_estacao'] ?? 'Núcleo não Informado.' ;?></span>

    <?php if($row['estacao']!=''){
                                // conta o total de tickets criados com esta categoria(tipo de suporte)
                                $sql = "SELECT count(*) as t FROM suporte WHERE estacao= '$row[id_estacao]' AND tipo_suporte='$row[tipo_suporte]'";
                                $sql = $conexao->query($sql);
                                $sql = $sql->fetch();
                                $total = $sql['t'];

                                if ($total == 1) {

                                    echo '<div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-3 w-550px">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <!--begin::Content-->
                                        <div class="fw-semibold">
                                            <div class="fs-6 text-gray-700">Houve um total de <span class="text-warning">'.$total.'</span> solicitações (também chamadas de Tickets) 
                                            geradas para o Núcleo <span class="text-gray-800 fw-bold">'.$row['nome_estacao'].'</span>, para lidar com a Categoria "<span class="text-warning">'.$row['nome_suporte'].'</span>".
                                           
                                          
                                            </div>
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>';
                                }
                                if ($total == 0) {

                                    echo "Nenhum Ticket existente.";
                                }
                                if ($total > 1) {

                                   
                                    echo '<div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-3 w-550px">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <!--begin::Content-->
                                        <div class="fw-semibold">
                                            <div class="fs-6 text-gray-700">Houve um total de <span class="text-warning">'.$total.'</span> solicitações (também chamadas de Tickets) 
                                            geradas para o Núcleo <span class="text-gray-800 fw-bold">'.$row['nome_estacao'].'</span>, para lidar com a Categoria "<span class="text-warning">'.$row['nome_suporte'].'</span>".
                                           
                                          
                                            </div>
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>';
                                }
                            }
                                ?>


    </div>

    <div class="tab-pane  fade" id="suporte_aba_plcode_tab_1" role="tabpanel">
   
    <span class="badge badge-lg badge-light-primary fs-2"> <?=$row['nome_ponto'] ? $row['nome_ponto'] : 'PLCode não utilizado.';?> </span>

    <div id="mapa"></div>
    
    <?php 
  
    
    if($row['plcode']!=''){
                                // conta o total de tickets criados com esta categoria(tipo de suporte)
                                $sql = "SELECT count(*) as t FROM suporte WHERE plcode='$row[plcode]' AND estacao= $row[id_estacao]";
                                $sql = $conexao->query($sql);
                                $sql = $sql->fetch();
                                $total = $sql['t'];

                                if ($total == 1) {

                                    echo '<div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-3 w-550px">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <!--begin::Content-->
                                        <div class="fw-semibold">
                                            <div class="fs-6 text-gray-700">Houve um total de <span class="text-warning">'.$total.'</span> solicitações (também chamadas de Tickets) 
                                            geradas no Núcleo <span class="text-gray-800 fw-bold">'.$row['nome_estacao'].'</span>, para lidar com o PLCode "<span class="text-warning">'.$row['nome_ponto'].'</span>".
                                           
                                          
                                            </div>
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>';
                                }
                                if ($total == 0) {

                                    echo "Nenhum Ticket existente.";
                                }
                                if ($total > 1) {

                                   
                                    echo '<div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-3 w-550px">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <!--begin::Content-->
                                        <div class="fw-semibold">
                                            <div class="fs-6 text-gray-700">Houve um total de <span class="text-warning">'.$total.'</span> solicitações (também chamadas de Tickets) 
                                            geradas no Núcleo <span class="text-gray-800 fw-bold">'.$row['nome_estacao'].'</span>, para lidar com o PLCode "<span class="text-warning">'.$row['nome_ponto'].'</span>".
                                           
                                          
                                            </div>
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>';
                                }


                                echo"
                                

                                <script>

                                // Substitua a latitude e a longitude pelas coordenadas que deseja usar
                                var latitude = '$row[latitude_p]';
                                var longitude = '$row[longitude_p]';

                               // Carregue a API do Google Maps
                            
                                </script>";
                            }
                                ?>




    </div>

    <div class="tab-pane fade " id="suporte_aba_indicador_tab_1" role="tabpanel">
        
   
                        


    <span class="badge badge-lg badge-light-primary fs-2 ">  <?=$row['nome_parametro'] ?? 'Leitura não Informada.' ;?></span>
    
    <?php if($row['parametro']!=''){
                                // conta o total de tickets criados com esta categoria(tipo de suporte)
                                $sql = "SELECT count(*) as t FROM suporte WHERE parametro='$row[parametro]' AND estacao= $row[id_estacao]";
                                $sql = $conexao->query($sql);
                                $sql = $sql->fetch();
                                $total = $sql['t'];

                                if ($total == 1) {

                                    echo '<div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-3 w-550px">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <!--begin::Content-->
                                        <div class="fw-semibold">
                                            <div class="fs-6 text-gray-700">Houve um total de <span class="text-warning">'.$total.'</span> solicitações (também chamadas de Tickets) 
                                            geradas no PLCode <span class="text-gray-800 fw-bold">'.$row['nome_ponto'].'</span>, para lidar com o indicador "<span class="text-warning">'.$row['nome_parametro'].'</span>".
                                           
                                          
                                            </div>
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>';
                                }
                                if ($total == 0) {

                                    echo "Nenhum Ticket existente.";
                                }
                                if ($total > 1) {

                                   
                                    echo '<div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-3 w-550px">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <!--begin::Content-->
                                        <div class="fw-semibold">
                                            <div class="fs-6 text-gray-700">Houve um total de <span class="text-warning">'.$total.'</span> solicitações (também chamadas de Tickets) 
                                            geradas no PLCode <span class="text-gray-800 fw-bold">'.$row['nome_ponto'].'</span>, para lidar com o indicador "<span class="text-warning">'.$row['nome_parametro'].'</span>".
                                           
                                          
                                            </div>
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>';
                                }
                            }
                                ?>


    </div>

    <div class="tab-pane  fade" id="suporte_aba_categoria_tab_1" role="tabpanel">
   
   

    <span class="badge badge-lg badge-light-primary fs-2">   <?=$row['nome_suporte'] ?? 'Categoria não Informada.' ;?></span>

    <?php if($row['tipo_suporte']!=''){
                                // conta o total de tickets criados com esta categoria(tipo de suporte)
                                $sql = "SELECT count(*) as t FROM suporte WHERE tipo_suporte='$row[tipo_suporte]' AND estacao= $row[id_estacao]";
                                $sql = $conexao->query($sql);
                                $sql = $sql->fetch();
                                $total = $sql['t'];

                                if ($total == 1) {

                                    echo '<div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-3 w-550px">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <!--begin::Content-->
                                        <div class="fw-semibold">
                                            <div class="fs-6 text-gray-700">Houve um total de <span class="text-warning">'.$total.'</span> solicitações (também chamadas de Tickets) 
                                            geradas no Núcleo <span class="text-gray-800 fw-bold">'.$row['nome_estacao'].'</span> para lidar com a categoria "<span class="text-warning">'.$row['nome_suporte'].'</span>".
                                           
                                          
                                            </div>
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>';
                                }
                                if ($total == 0) {

                                    echo "Nenhum Ticket existente.";
                                }
                                if ($total > 1) {

                                   
                                    echo '<div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-3 w-550px">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <!--begin::Content-->
                                        <div class="fw-semibold">
                                            <div class="fs-6 text-gray-700">Houve um total de <span class="text-warning">'.$total.'</span> solicitações (também chamadas de Tickets) 
                                            geradas no Núcleo <span class="text-gray-800 fw-bold">'.$row['nome_estacao'].'</span> para lidar com a categoria "<span class="text-warning">'.$row['nome_suporte'].'</span>".
                                           
                                          
                                            </div>
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>';
                                }
                            }
                                ?>
    </div>

    <div class="tab-pane  fade" id="suporte_aba_finaliza_tab_1" role="tabpanel">
    

 <!--begin::Input 
    <span class="badge badge-lg badge-light-primary fs-2">Qual ação deseja tomar?</span>
group-->
                   <form id="form_suporte_ticket" class="form"    >
    <div class="mb-10">
    <label for="atender_suporte_imput" class="required form-label">Qual ação deseja tomar?</label>
<select class="form-select form-select-lg form-select-solid w-500px" data-control="select2" data-dropdown-css-class="w-500px"  id='atender_suporte' name='atender_suporte' data-placeholder="Indique a Ação à ser Tomada" data-dropdown-parent="#drawer_Suporte">
    <option></option>
    <option value="2">Atender Ticket</option>
    <option value="5">Indicar Prazo de Finalização</option>
    <option value="3">Indicar Terceiros</option>
    <option value="4">Encerrar Ticket</option>
</select>
</div>


                    <div class="mb-10 d-none" id="div_suporte_atende_ticket">            

                         <!--begin::Input group-->
                         <div class="form-floating w-500px">
                            <textarea class="form-control w-500px" placeholder="Informe qual foi a Resolução/Tratativa, para resolver o problema." id="motivo_resolutiva" name="motivo_resolutiva" style="height: 100px"></textarea>
                            <label for="floatingTextarea2" id="texto_suporte_motivo_atendimento">Resolução/Tratativa:</label>
                        </div>
                        <!--end::Input group-->
                        
                    </div> 


                    <div class="mb-10 d-none" id="div_suporte_data_prevista_ticket">
                        <label for="suporte_data_prevista_ticket" class="required form-label">Qual Data Prevista?</label>

                       
                    <input class="form-control form-control-solid w-500px" placeholder="Data Prevista" id="data_previsao_suporte" name="data_previsao_suporte"/>
                       
                        
                    </div> 

                    <div class="mb-10 d-none" id="div_suporte_controla_ticket">
                    <button id="bt_valida_suporte_ticket" type="button" class="btn btn btn-light-warning">Encerrar Ticket</button>
                    </div> 
<input type="hidden" name="acao" value="altera_ticket">
<input type="hidden" name="id_suporte" value="<?=$id_suporte;?>">
<input type="hidden" name="tipo_suporte" value="<?=$row['tipo_suporte'];?>">
<input type="hidden" name="nome_tipo_suporte" value="<?=$row['nome_suporte'];?>">
<input type="hidden" name="nucleo_projeto" value="<?=$row['estacao'] ?? '';?>">
<input type="hidden" name="status_suporte" value="<?=$status_suporte;?>">
</form>          
    </div>
    

</div>


    </div>
    </div>


               


    
</div>

                        
        
        </div>
        <!--end::Modal body-->



    </div>
    <!--end::Modal content-->

<script>
                        

</script>

<?php

function mask($val, $mask)
{
    $maskared = '';
    $k = 0;
    for ($i = 0; $i <= strlen($mask) - 1; ++$i) {
        if ($mask[$i] == '#') {
            if (isset($val[$k])) {
                $maskared .= $val[$k++];
            }
        } else {
            if (isset($mask[$i])) {
                $maskared .= $mask[$i];
            }
        }
    }

    return $maskared;
}

$cnpj = '11222333000199';
$cpf = '00100200300';
$cep = '08665110';
$data = '10102010';
$hora = '021050';
/* 
echo mask($cnpj, '##.###.###/####-##').'<br>';
echo mask($cpf, '###.###.###-##').'<br>';
echo mask($cep, '#####-###').'<br>';
echo mask($data, '##/##/####').'<br>';
echo mask($data, '##/##/####').'<br>';
echo mask($data, '[##][##][####]').'<br>';
echo mask($data, '(##)(##)(####)').'<br>';
echo mask($hora, 'Agora são ## horas ## minutos e ## segundos').'<br>';
echo mask($hora, '##:##:##'); */


$conexao=null;


}else{

    echo '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
    <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
    <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
            <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
        </svg>
    </span>
    <!--end::Svg Icon-->
    <div class="d-flex flex-column">
        <h4 class="mb-1 text-primary">Tickets de Suporte</h4>
        <span>Não foi localizado Ticket para o Suporte >> '.$id_suporte.'</span>
    </div>
</div>';

$conexao=null;
}

}else{

    echo '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
    <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
    <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
            <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
        </svg>
    </span>
    <!--end::Svg Icon-->
    <div class="d-flex flex-column">
        <h4 class="mb-1 text-primary">Erro ao Validar</h4>
        <span>O ID do Ticket não foi Reconhecido.</span>
    </div>
</div>';
}

?>


<script>
    //** */== Herda controle drawer do Chat do Suporte:



function storeDataAttributesChatSuporte(element) {
  window.id_suporte = element.getAttribute('data-id_suporte');
  window.id_conversa = element.getAttribute('data-id_conversa');
  window.id_usuario = element.getAttribute('data-id_usuario');
}
</script>