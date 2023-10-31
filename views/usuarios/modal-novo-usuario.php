<?php
 // buffer de saída de dados do php]
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$_SESSION['pagina_atual'] = 'Novo Usuário';


?>

<script src="../../js/usuarios/novo-usuario.js"></script>


    <!--begin::Modal content-->
    <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header">
            <!--begin::Modal title-->
            <h2>Novo Usuário</h2>
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

                           <form id="kt_form_novo_usuario" class="form" autocomplete="off" >
                                                
                           <input type="hidden" name="acao" value="cadastrar_novo_usuario" id="cadastrar_novo_usuario" />
                                        <!--begin::Input group-->
                                        <div class="row mb-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Nome Completo</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-8">
                                                    <!--begin::Row-->
                                                    <div class="row">
                                                        <!--begin::Col-->
                                                        <div class="col-lg-6 fv-row">
                                                            <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Primeiro Nome" value="" />
                                                        </div>
                                                        <!--end::Col-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-6 fv-row">
                                                            <input type="text" name="lname" class="form-control form-control-lg form-control-solid" placeholder="Último Nome" value="" />
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
                                                <!--begin::Label-->
                                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Empresa Vinculada:</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-8 fv-row">
                                                    <select class="form-select form-select-solid" data-control="select2" data-placeholder="Especifique o Cliente (Empresa), que este Usuário representa." name='company' 
                                                    id="projeto_usuario" data-dropdown-parent="#kt_modal_novo_usuario">
                                                        <option></option>
                                                        <?php

                                                            $sql_company = $conexao->query("SELECT cli.nome_fantasia, cli.id_cliente FROM clientes cli 
                                                            WHERE cli.status_cadastro='1' 
                                                            GROUP BY cli.id_cliente 
                                                            ORDER BY nome_fantasia ASC 
                                                            ");
                                                            $conta_company = $sql_company->rowCount();

                                                            if ($conta_company > 0) {

                                                                foreach ($sql_company as $r) {

                                                                    echo '<option value="' . $r['id_cliente'] . '">' . $r['nome_fantasia'] . '</option>';
                                                                }
                                                            }
                                                        ?>

                                                    </select>

                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->


                                            <!--begin::Input group-->
                                            <div class="row mb-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                    <span class="required">Telefone - Whatsapp</span>
                                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="O número de Telefone precisa estar verificado."></i>
                                                </label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-8 fv-row">
                                                    <input type="tel" name="phone" id="telefone_corporativo_usuario" class="form-control form-control-lg form-control-solid" placeholder="Telefone Celular/WhatsApp" value="" />
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="row mb-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-4 col-form-label fw-semibold fs-6">E-mail</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-8 fv-row">
                                                    <input type="text" name="email" class="form-control form-control-lg form-control-solid" id="email_corporativo_usuario" placeholder="E-mail Principal" value="" />
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->



                                            <div class="row mb-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Perfil do Usuário</label>
                                            <div class="col-lg-8 fv-row">
                                            <select class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_novo_usuario" data-placeholder="Selecione uma Opção"   name="perfil_usuario" id='perfil_usuario' data-allow-clear="true"  data-hide-search="true">
                                                
                                                <option value="colaboradores">Colaborador</option>
                                                <option value="contatos" selected>Contato Cliente</option>
                                            
                                            </select>
                                            </div>
                                        </div>


                                        <div class="row mb-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Nícel do Acesso</label>
                                            <div class="col-lg-8 fv-row">
                                            <select class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_novo_usuario" data-placeholder="Selecione uma Opção"   name="nivel_acesso" id='nivel_usuario' data-allow-clear="true"  data-hide-search="true">
                                                
                                            <option value="engenheiro">Engenheiro IoT</option>
                                                <option value="supervisor">Supervisor</option>
                                                <option value="ro">RO</option>
                                                <option value="operador">Operador</option>
                                                <option value="cliente" selected>Cliente</option>
                                                <option value="admin">Administrador</option>
                                            
                                            </select>
                                            </div>


                                           
                                        </div>


                                         <!--begin::Input group-->
                                         <div class="row mb-6 d-none" id="div_matricula_usuario">
                                                <!--begin::Label-->
                                                <label class="col-lg-4 col-form-label fw-semibold fs-6" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="bottom" title="A Matrícula é um Indentificador Único de Colaboradores do GrupoEP">
                                                    <span class="required">Informe a Matrícula do Colaborador</span>
                                                    <i class="fas fa-exclamation-circle ms-1 fs-7" ></i>
                                                </label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-8 fv-row">
                                                    <input type="text" name="matricula" id="matricula" class="form-control form-control-lg form-control-solid" placeholder="Matrícula do Colaborador" value="" />
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->
            </div>

           


                                              <!--begin::Card footer-->
                                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                                        <button class="btn btn-light btn-active-light-primary me-2" data-bs-dismiss="modal">Descartar</button>
                                        <button class="btn btn-primary" id='bt_novo_usuario'>Salvar Cadastro</button>
                                    </div>
                                    <!--end::Card footer-->

                                          
                                            

                                  
        </form>
        
        </div>
        <!--end::Modal body-->
    </div>
    <!--end::Modal content-->


