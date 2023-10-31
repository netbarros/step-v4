
    <script src="../../js/projetos/edita-projeto.js">
	
    </script>
     
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
require '../../conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();

$_SESSION['pagina_atual'] = 'Edição de Projeto';


$id = (isset($_POST['id'])) ? $_POST['id'] : '';

$sql = $conexao->query("SELECT o.*, c.id_cliente, c.nome_fantasia FROM obras o
INNER JOIN clientes c On c.id_cliente = o.id_cliente
WHERE o.id_obra = '$id'");

$conta = $sql->rowCount();





if ($conta > 0) {

    $rd = $sql->fetch(PDO::FETCH_ASSOC);

    $id_cliente = $rd['id_cliente'];

    $periodo_inicialx =  $rd['periodo_inicial'];
    $periodo_finalx =  $rd['periodo_final'];


   $periodo_inicial =  date('d/m/Y', strtotime($periodo_inicialx));

   $periodo_final =  date('d/m/Y', strtotime($periodo_finalx));



?>




    <!--begin::Modal content-->
    <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header">
            <!--begin::Modal title-->
            <h2>Edição do Projeto</h2> <span id="minhaId"></span>
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
            <div class="stepper stepper-pills stepper-column d-flex flex-column flex-xl-row flex-row-fluid" id="kt_modal_novo_projeto_stepper">
                <!--begin::Aside-->
                               <!--begin::Aside-->
                <!--begin::Content-->
                <div class="flex-row-fluid py-lg-5 px-lg-15">
                    <!--begin::Form-->
                    <form class="form" novalidate="novalidate" id="kt_modal_edita_projeto_form">
                        <!--begin::Step 1-->
                        <div class="current" element="content">
                            <div class="w-100">
                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                        <span class="required">Nome do Projeto</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Especifique um nome único para este Projeto"></i>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" class="form-control form-control-lg form-control-solid" name="nome_projeto" id='edita_nome_projeto' placeholder="Defina o Nome deste Projeto" value="<?=$rd['nome_obra'];?>" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->


                                    <!--begin::Input group-->
                                    <div class="fv-row mb-10">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                        <span class="required">Código do Projeto</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Informe o Código Interno do Projeto"></i>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" class="form-control form-control-lg form-control-solid" name="codigo_obra" id='codigo_obra' maxlength="10" placeholder="Informe o Código Interno do Projeto" value="<?=$rd['codigo_obra'];?>" />
                                    <!--end::Input--> <span class="fs-6 text-muted">Permitido até 10 caracteres</span>
                                </div>
                                <!--end::Input group-->




                                <!--begin::Input group-->
                                <div class="fv-row">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-5 fw-semibold mb-4">
                                        <span class="required">Cliente</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Vincule o Cliente à este Projeto"></i>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin:Options-->
                                   
                                        <!--begin::Col-->
                        <div class="col-md-12 fv-row">
                           
                            <select class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_edita_projeto"  data-placeholder="Selecione o Cliente" name="cliente_projeto" id="edita_cliente_projeto">
                                <option value="">Selecione o Cliente...</option>
                                <?php
                                $sql_cliente_edita_projeto = $conexao->query("SELECT c.nome_fantasia, c.cnpj, c.id_cliente, c.gestao_step FROM clientes c
                                
                                WHERE c.status_cadastro='1' ORDER BY c.nome_fantasia ASC");

                                $conta_cliente_projeto = $sql_cliente_edita_projeto->rowCount();

                                if ($conta_cliente_projeto > 0) {

                                    $row = $sql_cliente_edita_projeto->fetchALL(PDO::FETCH_ASSOC);



                                    foreach ($row as $r) {

                                        $gestao_step_ = $r['gestao_step'];

                                        if($gestao_step_=='gestao_ep'){
                                            $gestao_step = 'Gestão do GrupoEP';
                                        }

                                        
                                        if($gestao_step_=='gestao_cliente'){
                                            $gestao_step = 'Gestão do Cliente'; 
                                        }
                                        
                                        
                                        if($gestao_step_=='gestao_filial'){
                                            $gestao_step = 'Filial GrupoEP'; 
                                        }

                                        $cnpj = mask_edita($r['cnpj'], '##.###.###/####-##');

                                        if($r['id_cliente']==$id_cliente){

                                          echo ' <option data-kt-select2-user="'.$gestao_step.'" value="' . $id_cliente . '" selected>' . $rd['nome_fantasia'] . ' </option>';
                                        } else {
                                            echo ' <option data-kt-select2-user="'.$gestao_step.'" value="' . $r['id_cliente'] . '">' . $r['nome_fantasia'] . ' </option>';

                                        }

                                        
                                    }
                                } else {

                                    echo '<option value="">Não há Clientes Disponíveis</option>';
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
                        <div element="content">
                            <div class="w-100">
                                <!--begin::Input group-->
                                <div class="fv-row">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-5 fw-semibold mb-4">
                                        <span class="required">Período de Contrato</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Informe o Período Contratado"></i>
                                    </label>
                                    <!--end::Label-->
                                 <!--begin::Input-->
                                 <div class="position-relative d-flex align-items-center">
                                    <!--begin::Icon-->
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                    <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
                                            <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
                                            <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <!--end::Icon-->
                                    <!--begin::Datepicker-->
                                    <input class="form-control form-control-solid ps-12" placeholder="Informe o Período" name="periodo_contrato" id="edita_periodo_contrato" value='<?php echo $periodo_inicial.' - '.$periodo_final;?>' readonly  />
                                    <!--end::Datepicker-->
                                </div>
                                <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                            </div>
                        </div>
                        <!--end::Step 2-->
                        <!--begin::Step 3-->
                        <div element="content">
                            <div class="w-100">
                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <!--begin::Label-->
                                    <label class="required fs-5 fw-semibold mb-2">Status Projeto</label>
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
                                <label class="fs-6 fw-semibold">Libere o acesso ao Projeto</label>
                                <div class="fs-7 fw-semibold text-muted">Determine sua Atividade</div>
                            </div>
                            <!--end::Label-->
                            <!--begin::Switch-->
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" <?php if($rd['status_cadastro']=='1'){ echo 'checked="checked"';} ?> name="status_projeto"/>
                                <span class="form-check-label fw-semibold text-muted">Ativos</span>
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
                        <div element="content">
                            <div class="w-100">
                                <!--begin::Input group-->
                                <div class="d-flex flex-column mb-7 fv-row">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Observações do Projeto</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Inclua observações internas relevantes à este Projeto."></i>
                                    </label>
                                    <!--end::Label-->
                                    <div  id="quill_edita_obs_projeto"> <?=$rd['obs_interna'];?></div>
                                    <textarea name="obs_projeto" style="display:none" id="edita_obs_projeto"></textarea>
                                </div>
                                <!--end::Input group-->
                           
                            </div>
                        </div>
                        <!--end::Step 4-->
                 
                        <!--begin::Actions-->
                        <div class="d-flex flex-stack pt-10">
                            
                            <!--begin::Wrapper-->
                            <div>
                                <a type="submit" class="btn btn-lg btn-primary"  id='bt_atualiza_projeto'>
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



<?php }else { echo "Falha na Integtridade da Consulta! ".$id;}?>

