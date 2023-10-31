    
 <?php

function mask_edita($val, $mask_edita)
{
    $maskared = '';
    $k = 0;
    for ($i = 0; $i <= strlen($mask_edita) - 1; ++$i) {
        if ($mask_edita[$i] == '#') {
            if (isset($val[$k])) {
                $maskared .= $val[$k++];
            }
        } else {
            if (isset($mask_edita[$i])) {
                $maskared .= $mask_edita[$i];
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
echo mask_edita($cnpj, '##.###.###/####-##').'<br>';
echo mask_edita($cpf, '###.###.###-##').'<br>';
echo mask_edita($cep, '#####-###').'<br>';
echo mask_edita($data, '##/##/####').'<br>';
echo mask_edita($data, '##/##/####').'<br>';
echo mask_edita($data, '[##][##][####]').'<br>';
echo mask_edita($data, '(##)(##)(####)').'<br>';
echo mask_edita($hora, 'Agora são ## horas ## minutos e ## segundos').'<br>';
echo mask_edita($hora, '##:##:##'); */

?>
<?php
// Atribui uma conexão PDO
require '../../../conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();

$_SESSION['pagina_atual'] = 'Detalhes de Núcleos em Alerta';


$id = (isset($_POST['id'])) ? $_POST['id'] : ''; // id da estacao selecionada para busca de suportes

$nome_nucleo= $_POST['nome_nucleo'] ?? '';

$sql = $conexao->query("SELECT * FROM suporte s
 JOIN estacoes e ON e.id_estacao = s.estacao
 JOIN obras o ON o.id_obra = e.id_obra
 JOIN pontos_estacao p ON p.id_obra = e.id_obra
 JOIN tipo_suporte tp ON tp.id_tipo_suporte = s.tipo_suporte

WHERE s.estacao='$id' AND s.status_suporte <>'4'  GROUP BY s.id_suporte  ORDER BY s.data_open  ");

$conta = $sql->rowCount();





if ($conta > 0) {

    $rd = $sql->fetchALL(PDO::FETCH_ASSOC);

  


?>




    <!--begin::Modal content-->
    <div class="modal-content ">
        <!--begin::Modal header-->
        <div class="modal-header">
            <!--begin::Modal title-->
            <h3 class='text-inverse-primary  fs-4 px-2 ms-2 ms-2 text-uppercase'>Suportes não finalizados </h3> <span class="badge badge-danger fw-semibold fs-4 px-2 ms-2 cursor-default ms-2" >Núcleo <?=$nome_nucleo;?></span>
            <!--end::Modal title-->
            <!--begin::Close-->
            <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                <span class="svg-icon svg-icon-1">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                    </svg>
                </span>
                <!--end::Svg Icon-->
            </div>
            <!--end::Close-->
        </div>
        <!--end::Modal header-->
        <!--begin::Modal body-->
        <div class="modal-body py-lg-10 px-lg-10">
           
        <div class="card-body pt-2">

<?php 

foreach($rd as $r){

    $data_a = $r['data_open'] ??'';
    $data_p = $r['data_prevista'] ??'';
    $data_f = $r['data_close'] ??'';


    $data_aberto = date('d/m/Y H:m', strtotime($data_a));
    $data_previsao = date('d/m/Y H:m', strtotime($data_p));
    $data_fechado = date('d/m/Y H:m', strtotime($data_f));


    switch ($r['status_suporte']) {
        case '1':
            $nome_status = 'em aberto';
            $css_status='danger';
            break;

            case '2':
                $nome_status = 'em previsão';
                $css_status='warning';
                break;

                case '3':
                    $nome_status = 'sem previsão';
                    $css_status='dark';
                    break;

                    case '4':
                        $nome_status = 'finalizado';
                        $css_status='success';
                        break;

                        case '6':
                            $nome_status = 'indicador revogado';
                            $css_status='info';
                            break;

                            case '7':
                                $nome_status = 'indicador liberado';
                                $css_status='primary';
                                break;

                                
        
        default:
        $nome_status = 'em aberto';
        $css_status='warning';
            break;
    }


  

?>

        <!--begin::Item-->
            <div class="d-flex align-items-center mb-8">
            <a href="javascript:;" onclick="storeDataAttributesJanelaSuporteNucleo(this)" class="d-flex text-gray-800 text-hover-primary fw-bold fs-6" data-id_suporte='<?=$r['id_suporte'];?>' data-kt-drawer-show="true" data-kt-drawer-target="#drawer_Suporte" >
                <!--begin::Bullet-->
                <span class="bullet bullet-vertical h-40px bg-<?=$css_status;?>"></span>
                <!--end::Bullet--> <span class="badge badge-light-dark" data-bs-toggle="tooltip" data-bs-placement="left" title="Clique para acessar o Ticket" ><?=$r['id_suporte'];?> </span>
                 <!--begin::Checkbox-->
                 <div class="form-check form-check-custom form-check-solid mx-5">
                 <span class="badge badge-light-<?=$css_status;?>"><?=$nome_status;?></span>
                </div>
                <!--end::Checkbox-->
</a>
                <!--begin::Description-->
                <div class="flex-grow-1 me-2">
                    <a href="javascript:;" onclick="storeDataAttributesJanelaSuporteNucleo(this)" class="text-gray-800 text-hover-primary fw-bold fs-6" data-id_suporte='<?=$r['id_suporte'];?>' data-kt-drawer-show="true" data-kt-drawer-target="#drawer_Suporte" ><?=$r['nome_estacao'];?> </a>

                    <span class="text-muted fw-semibold d-block"><strong>Ponto: </strong><?=$r['nome_ponto'];?></span>
                </div>
                <!--end::Description-->



                <div class="timeline">                                 
                                <!--begin::Timeline item-->
                                <div class="timeline-item align-items-center mb-7">
                                    <!--begin::Timeline line-->
                                    <div class="timeline-line w-40px mt-6 mb-n12"></div>
                                    <!--end::Timeline line-->

                                    <!--begin::Timeline icon-->
                                    <div class="timeline-icon" style="margin-left: 11px">
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen015.svg-->
<span class="svg-icon svg-icon-2 svg-icon-danger"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z" fill="currentColor"></path>
<path d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z" fill="currentColor"></path>
</svg>
</span>
<!--end::Svg Icon-->                                   
                                    </div>
                                    <!--end::Timeline icon-->

                                    <!--begin::Timeline content-->
                                    <div class="timeline-content m-0">
                                        <!--begin::Title-->
                                        <span class="fs-6 text-gray-400 fw-semibold d-block"><?php echo $r['nome_suporte'];?></span>
                                        <!--end::Title-->   
                                        
                                        <!--begin::Title-->
                                        <span class="fs-6 fw-bold text-gray-800">Abertura em <?=$data_aberto;?></span>
                                        <!--end::Title-->    
                                    </div>
                                    <!--end::Timeline content-->                                  
                                </div>
                                <!--end::Timeline item-->  

                                <!--begin::Timeline item-->
                                <div class="timeline-item align-items-center">
                                    <!--begin::Timeline line-->
                                    <div class="timeline-line w-40px"></div>
                                    <!--end::Timeline line-->

                                    <!--begin::Timeline icon-->
                                    <div class="timeline-icon" style="margin-left: 11px">
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen018.svg-->
<span class="svg-icon svg-icon-2 svg-icon-<?=$css_status;?>"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="currentColor"></path>
<path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="currentColor"></path>
</svg>
</span>
<!--end::Svg Icon-->                                   
                                    </div>
                                    <!--end::Timeline icon-->

                                    <!--begin::Timeline content-->
                                    <div class="timeline-content m-0">
                                        <!--begin::Title-->


                                        
<?php   if($r['status_suporte']=='2') {?>

    <span class="fs-6 text-gray-400 fw-semibold d-block">Sem Previsão

<?php } ?>



<?php   if($r['status_suporte']=='3') {?>

    <span class="fs-6 text-gray-400 fw-semibold d-block">Previsão em <?=$data_previsao;?>

<?php } ?>

<?php   if($r['status_suporte']=='4') {?>

    <span class="fs-6 text-gray-400 fw-semibold d-block">Fechado em <?=$data_fechado;?>

<?php } ?>


                                      
                                        <!--end::Title-->   
                                        
                                        <!--begin::Title-->
                                        <span class="fs-6 fw-bold text-capitalize text-gray-800"><?=$nome_status;?></span>
                                        <!--end::Title-->    
                                    </div>
                                    <!--end::Timeline content-->                                  
                                </div>                                        
                                <!--end::Timeline item--> 
                            </div>



                
            </div>
       <!--end:Item-->


       <div class="separator separator-dashed mt-5 mb-4"></div>
        
<?php 
}

?>         

            
               
    </div>
        
        </div>
        <!--end::Modal body-->
    </div>
    <!--end::Modal content-->



<?php } else { 
    
    
    echo ' <div class="modal-content">
    <div class="modal-header">
        <h3 class="modal-title">Ticket em Andamento</h3>

        <!--begin::Close-->
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <span class="svg-icon svg-icon-1"></span>
        </div>
        <!--end::Close-->
    </div>

    <div class="modal-body">
        <p>O Ticket de Suporte, ID <span class="text-warning">'.$id.'</span> recebeu novas atualizações, acesse o Dashboard de Suporte para dar continuidade.</p>
    </div>

    <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar Janela</button>
                <a href="https://step.eco.br/views/relatorios/relatorios-suportes.php?tipo_relatorio=suportes_aberto&titulo_relatorio=Andamento%20dos%20Suportes" class="btn btn-primary">Acessar Dashboard de Suporte</a>
            </div>
</div>'; 

//echo "Falha na Integtridade da Consulta! ".$id;
}

$conexao=null;

?>


<script>

//===================================[Janela Suporte]========================================================= */

function storeDataAttributesJanelaSuporteNucleo(element) {
        window.id_suporte = element.getAttribute('data-id_suporte');
        window.id_conversa = element.getAttribute('data-id_conversa');


        $("#kt_modal_detalhe_nucleo").modal("hide");
    }

    
  //===================================================================================================== */


  </script>