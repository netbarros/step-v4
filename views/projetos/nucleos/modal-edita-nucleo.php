
    
     
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

$_SESSION['pagina_atual'] = 'Edição de Núcleo';

$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';


$id = (isset($_POST['id'])) ? $_POST['id'] : '';

$sql = $conexao->query("SELECT * FROM estacoes e

WHERE e.id_estacao = '$id'");

$conta = $sql->rowCount();





if ($conta > 0) {

    $rd = $sql->fetch(PDO::FETCH_ASSOC);

    $id_projeto = $rd['id_obra'];

    $id_ro = $rd['ro'];
    $id_supervisor = $rd['supervisor'];

   

?>




    <!--begin::Modal content-->
    <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header">
            <!--begin::Modal title-->
            <h2>Edição do <?php echo ($nivel_acesso_user_sessao == 'engenheiro') ? 'PLC do Projeto' : 'Núcleos do Projeto'; ?> </h2> <span id="minhaId"></span>
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
            <!--begin::Stepper-->
            <div class="stepper stepper-pills stepper-column d-flex flex-column flex-xl-row flex-row-fluid" id="kt_modal_novo_nucleo_stepper">
                <!--begin::Aside-->
                               <!--begin::Aside-->
                <!--begin::Content-->
                <div class="flex-row-fluid py-lg-5 px-lg-15">
                    <!--begin::Form-->
                    <form class="form" novalidate="novalidate" id="kt_modal_edita_nucleo_form">
                        <!--begin::Step 1-->
                        <div class="current" element="content">
                             <!--begin::Label-->
                             <label class="d-flex align-items-center fs-5 fw-semibold mb-4">
                                        <span class="required">Identificação</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Identificação Geral do Núcleo"></i>
                                    </label>
                                    <!--end::Label-->
                            <div class="w-100">
                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                        <span class="required">Nome do Núcleo</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Especifique um nome único para este Núcleo"></i>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" class="form-control form-control-lg form-control-solid" name="nome_nucleo" id='edita_nome_nucleo'  placeholder="Defina o Nome deste Núcleo" value="<?=$rd['nome_estacao'];?>" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-5 fw-semibold mb-4">
                                        <span class="required">Projeto do Núcleo</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Vincule o Projeto à este Núcleo"></i>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin:Options-->
                                   
                                        <!--begin::Col-->
                        <div class="col-md-12 fv-row">
                           
                            <select class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_edita_nucleo"  data-placeholder="Selecione o Projeto" name="id_projeto" id="edita_projeto_nucleo" disabled>
                                <option value="">Selecione o Projeto...</option>
                                <?php
                                $sql_cliente_edita_nucleo = $conexao->query("SELECT nome_obra, id_obra FROM obras
                                
                                WHERE status_cadastro='1' ORDER BY nome_obra ASC");

                                $conta_cliente_nucleo = $sql_cliente_edita_nucleo->rowCount();

                                if ($conta_cliente_nucleo > 0) {

                                    $row = $sql_cliente_edita_nucleo->fetchALL(PDO::FETCH_ASSOC);



                                    foreach ($row as $r) {

                                       
                                        if($r['id_obra']==$id_projeto){

                                          echo ' <option value="' . $id_projeto . '" selected>' . $r['nome_obra'] . ' </option>';
                                        } else {
                                            echo ' <option  value="' . $r['id_obra'] . '">' . $r['nome_obra'] . ' </option>';

                                        }

                                        
                                    }
                                } else {

                                    echo '<option value="">Não há Projetos Disponíveis</option>';
                                }

                                ?>


                            </select>
                        </div>
                        <!--end::Col-->
                                    
                                    <!--end:Options-->
                                </div>
                                <!--end::Input group-->
                            </div>
                        </div>
                        <!--end::Step 1-->
                        <!--begin::Step 2-->
                        <div data-kt-stepper-element="content">
                            <div class="w-100">
                            <label class="d-flex align-items-center fs-5 fw-semibold mb-4">
                                        <span class="required">Serão Inclusos na aba <b>Usuários</b> do Projeto</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Serão Incluídos em Usuários do Projeto"></i>
                                    </label>

                              
                            </div>

                        </div>
                        <!--end::Step 2-->
 <!--begin::Label-->
 <label class="d-flex align-items-center fs-5 fw-semibold mb-4">
                                        <span class="required">Ge-Localização</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Informe a Ge-Localização do Núcleo"></i>
                                    </label>
                                    <!--end::Label-->
                        <div element="content">
                            <div class="w-100">
                                <!--begin::Input group-->
                                <div class="d-flex flex-column mb-7 fv-row">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Latitude</span>
                                       
                                    </label>
                                    <!--end::Label-->
                                   
                                    <input type="text" class="form-control form-control-lg form-control-solid" name="latitude_nucleo" id='edita_latitude'  placeholder="Defina a Latitude deste Núcleo" value="<?=$rd['latitude'];?>" />
                                </div>
                                <!--end::Input group-->
                           
                            </div>


                            <div class="w-100">
                                <!--begin::Input group-->
                                <div class="d-flex flex-column mb-7 fv-row">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Longitude</span>
                                     
                                    </label>
                                    <!--end::Label-->
                                   
                                    <input type="text" class="form-control form-control-lg form-control-solid" name="longitude_nucleo" id='edita_longitude'  placeholder="Defina a Latitude deste Núcleo" value="<?=$rd['longitude'];?>" />
                                </div>
                                <!--end::Input group-->
                           
                            </div>
                        </div>

                       
                        <!--begin::Step 3-->
                        <div element="content">
                            <div class="w-100">
                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <!--begin::Label-->
                                    <label class="required d-flex align-items-center fs-5 fw-semibold mb-2">Status Núcleo</label>
                                    <!--end::Label-->
                                    
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row">
                                    <!--begin::Label-->
                                                            <!--begin::Input group-->
                          <div class="d-flex flex-stack mb-8">
                            <!--begin::Label-->
                            <div class="me-5">
                                <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">Libere o acesso ao Núcleo</label>
                                <div class="fs-7 fw-semibold text-muted">Determine sua Atividade</div>
                            </div>
                            <!--end::Label-->
                            <!--begin::Switch-->
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" checked="checked" name="status_nucleo"/>
                                <span class="form-check-label fw-semibold text-muted">Ativo</span>
                            </label>
                            <!--end::Switch-->
                        </div>
                        <!--end::Input group-->
                                    <!--end::Option-->
                                </div>
                                <!--end::Input group-->
                            </div>
                        </div>
                        <!--end::Step 3-->
                        <!--begin::Step 4-->
          
                        <!--end::Step 4-->
                 
                        <!--begin::Actions-->
                        <div class="d-flex flex-stack pt-10">
                            
                            <!--begin::Wrapper-->
                            <div>
                                <a type="submit" class="btn btn-lg btn-primary"  id='bt_atualiza_nucleo'>
                                    <span class="indicator-label">Atualizar
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-3 ms-2 me-0">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </span>
                                    <span class="indicator-progress">Por favor aguarde...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </a>
                           
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Actions-->
                        <input type="hidden" name="acao" value="alterar">
                        <input type="hidden" name="id" value="<?=$id;?>">
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Stepper-->
        </div>
        <!--end::Modal body-->
    </div>
    <!--end::Modal content-->

    <script src="../../../js/nucleos/edita-nucleo.js"> </script>

<?php }else { echo "Falha na Integtridade da Consulta! ".$id;}?>

