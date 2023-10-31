<?php
 // buffer de saída de dados do php]
// Instancia Conexão PDO
if (!isset($_SESSION)) session_start();
require_once "../../conexao.php";
$conexao = Conexao::getInstance();
require_once "../../crud/login/verifica_sessao.php";


$id = null;

if (isset($_GET['id']) && is_numeric(isset($_GET['id']))) {

    $id = trim(isset($_GET['id']));

} 



if($id === '' || $id === null){
    $value = 'Sentimos muito! <br/>O STEP Não Conseguiu Validar o Cliente para Edição, caso o Erro Persista, por gentileza entre em contato com o Suporte.';

    $_SESSION['error'] =  $value;
   
    header("Location: /views/dashboard.php");
    exit;
}

$_SESSION['pagina_atual'] = 'Editando Cadastro Cliente ID '.$id;



$sql = $conexao->query("SELECT * FROM clientes WHERE id_cliente='$id'");

$conta = $sql->rowCount();

if($conta>0){


    $row= $sql->fetch(PDO::FETCH_ASSOC);


?>

<script src="../../js/clientes/edita-cliente.js"></script>


    <!--begin::Modal content-->
    <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header">
            <!--begin::Modal title-->
            <h2>Cadastro de Cliente</h2>
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

                           <form id="kt_form_edita_cliente" class="form" autocomplete="off" >

                                 <input type="hidden" name="id_cliente" value="<?=$id;?>">

                                 <input type="hidden" name="acao" value="atualizar">        
                          
                                        <!--begin::Input group-->
                                        <div class="row mb-6">
                                               
                                                <!--begin::Col-->
                                                <div class="col-lg-12">
                                                    <!--begin::Row-->
                                                    <div class="row">
                                                        <!--begin::Col-->
                                                        <div class="col-lg-6 fv-row">
                                                             <!--begin::Label-->
                                                <label class="col-lg-6 col-form-label required fw-semibold fs-6">Razão Social</label>
                                                <!--end::Label-->
                                                            <input type="text" name="razao_social" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Razão Social" value="<?php echo $row['razao_social'] ?? '';?>" />
                                                        </div>
                                                        <!--end::Col-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-6 fv-row">
                                                             <!--begin::Label-->
                                                <label class="col-lg-6 col-form-label required fw-semibold fs-6">Nome Fantasia</label>
                                                <!--end::Label-->
                                                            <input type="text" name="nome_fantasia" class="form-control form-control-lg form-control-solid" placeholder="Nome Fantasia" value="<?php echo $row['nome_fantasia'] ?? '';?>" />
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Row-->
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->




                                                    <!--begin::Input group-->
                                                    <div class="row mb-6">
                                                
                                                <!--begin::Col-->
                                                <div class="col-lg-12">
                                                    <!--begin::Row-->
                                                    <div class="row">
                                                        <!--begin::Col-->
                                                        <div class="col-lg-6 fv-row">
                                                            <!--begin::Label-->
                                                <label class="col-lg-6 col-form-label required fw-semibold fs-6">CNPJ</label>
                                                <!--end::Label-->
                                                            <input type="text" name="cnpj" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="CNPJ" value="<?php echo $row['cnpj'] ?? '';?>" />
                                                        </div>
                                                        <!--end::Col-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-6 fv-row">
                                                            <!--begin::Label-->
                                                <label class="col-lg-6 col-form-label required fw-semibold fs-6">Telefone</label>
                                                <!--end::Label-->
                                                            <input type="text" name="telefone" class="form-control form-control-lg form-control-solid" placeholder="(11) 0000-0000" value="<?php echo $row['telefone'] ?? '';?>" />
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Row-->
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->


                                         <!--begin::Input group-->
                                         <div class="row mb-6">
                                                
                                                <!--begin::Col-->
                                                <div class="col-lg-12">
                                                    <!--begin::Row-->
                                                    <div class="row">
                                                        <!--begin::Col-->
                                                        <div class="col-lg-6 fv-row">
                                                            <!--begin::Label-->
                                                <label class="col-lg-6 col-form-label required fw-semibold fs-6">E-mail Geral</label>
                                                <!--end::Label-->
                                                            <input type="email" name="email_geral" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="E-mail Geral" value="<?php echo $row['email_geral'] ?? '';?>" />
                                                        </div>
                                                        <!--end::Col-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-6 fv-row">
                                                            <!--begin::Label-->
                                                <label class="col-lg-6 col-form-label  fw-semibold fs-6">E-mail Nfe</label>
                                                <!--end::Label-->
                                                            <input type="email" name="email_nfe" class="form-control form-control-lg form-control-solid" placeholder="E-mail Nfe" value="<?php echo $row['email_nfe'] ?? '';?>" />
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Row-->
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="row mb-6">
                                               
                                                <!--begin::Col-->
                                                <div class="col-lg-6 fv-row">
                                                     <!--begin::Label-->
                                                <label class="col-lg-6 col-form-label fw-semibold fs-6">Site Cliente</label>
                                                <!--end::Label-->
                                                    <input type="text" name="site_cliente" class="form-control form-control-lg form-control-solid" placeholder="Site Cliente" value="<?php echo $row['site_cliente'] ?? '';?>" />
                                                </div>
                                                <!--end::Col-->


                                                <div class="col-lg-6 fv-row">
                                            <label class="col-lg-6 col-form-label fw-semibold fs-6 required">Status do Cadastro</label>
                                            <select class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_edita_cliente" data-placeholder="Selecione uma Opção"   name="status_cadastro" id='status_cadastro' data-allow-clear="true"  data-hide-search="true">
                                                
                                                <option value="1" selected>Ativo</option>
                                                <option value="2">Inativo</option>
                                                
                                            
                                            </select>
                                            </div>

                                            </div>
                                            <!--end::Input group-->







                                      
            </div>

           


                                              <!--begin::Card footer-->
                                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                                        <button class="btn btn-light btn-active-light-primary me-2" data-bs-dismiss="modal">Descartar</button>
                                        <button class="btn btn-primary" id='bt_edita_cliente'>Alterar Cadastro</button>
                                    </div>
                                    <!--end::Card footer-->

                                          
                                 

                                  
        </form>
        
        </div>
        <!--end::Modal body-->
    </div>
    <!--end::Modal content-->

<?php } ?>
