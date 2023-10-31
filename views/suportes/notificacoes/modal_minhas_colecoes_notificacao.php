<?php
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$_SESSION['pagina_atual'] = 'Nova Coleção de Notificações';

$projeto_atual = isset($_POST['projeto_atual']) ? $_POST['projeto_atual'] : (isset($_COOKIE['projeto_atual']) ? $_COOKIE['projeto_atual'] : '');

$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';

$nome_projeto = trim(isset($_COOKIE['nome_projeto'])) ? $_COOKIE['nome_projeto'] : '';

$projeto_atual = trim(isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';


$sql_personalizado_alertas_projeto = '';

$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';

if ($nivel_acesso_user_sessao == 'supervisor' || $nivel_acesso_user_sessao == 'ro' || $nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado_alertas_projeto = "AND (up.id_usuario  = '$id_usuario_sessao')";

} else if ($nivel_acesso_user_sessao == 'admin') {

    $sql_personalizado_alertas_projeto = "";
}

?> 
 
 <!--begin::Modal dialog-->
 <div class="modal-dialog modal-dialog-centered mw-850px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h2 class="fw-bold fs-4">Confira ou Altere suas Coleções de acordo com o Projeto selecionado.</h2>
                <!--end::Modal title-->

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-roles-modal-action="close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->

            <!--begin::Modal body-->
            <div class="modal-body scroll-y mx-5 my-7">
                <!--begin::Form-->
                <form id="kt_modal_update_role_form" class="form" action="#">
                    <!--begin::Scroll-->
                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_update_role_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#modal_minhas_colecoes_notificacao" data-kt-scroll-wrappers="#kt_modal_update_role_scroll" data-kt-scroll-offset="300px">
                        <!--begin::Input group-->
                        <div class="fv-row mb-10">
                            <!--begin::Label-->
                            <label class="fs-5 fw-bold form-label mb-2">
                                <span class="required">Seus Projetos</span>
                            </label>
                            <!--end::Label-->

                            <!--begin::Input-->
                            <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Selecione uma opção" data-dropdown-parent="#modal_minhas_colecoes_notificacao" id='select_filtro_projeto_alterta_edicao' data-allow-clear="true">
                                        <option></option>
                                       <?php

                                       

    $nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';

    $nome_projeto = trim(isset($_COOKIE['nome_projeto'])) ? $_COOKIE['nome_projeto'] : '';
  
    $projeto_atual = trim(isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';

    $sql_personalizado_alertas_projeto = '';


    if ($nivel_acesso_user_sessao == 'supervisor' || $nivel_acesso_user_sessao == 'ro' || $nivel_acesso_user_sessao == 'cliente') {

        $sql_personalizado_alertas_projeto = "AND (up.id_usuario  = '$id_usuario_sessao')";
    
    } else if ($nivel_acesso_user_sessao == 'admin') {
    
        $sql_personalizado_alertas_projeto = "";
    }


                                        $sql_filtro_projeto_dashboard = $conexao->query("SELECT o.id_obra, o.nome_obra FROM obras o  
                                       INNER JOIN estacoes e ON e.id_obra = o.id_obra
                                       INNER JOIN usuarios_projeto up ON up.id_obra = o.id_obra                                     
                                        WHERE    o.status_cadastro='1' $sql_personalizado_alertas_projeto  GROUP BY o.id_obra
                                       ORDER BY o.nome_obra ASC");

//print_r($sql_filtro_projeto_dashboard);
                                        $conta_projeto = $sql_filtro_projeto_dashboard->rowCount();

                                        if ($conta_projeto > 0) {

                                            $row = $sql_filtro_projeto_dashboard->fetchALL(PDO::FETCH_ASSOC);


                                           // print_r($row );


                                            if ($nome_projeto != 'undefined') {

                                                $nome_projeto_filtro = $nome_projeto;
                                                $id_projeto_filtro = $projeto_atual;


                                                echo ' <option value="' . $id_projeto_filtro . '" selected>' . $nome_projeto_filtro . '</option>';

                                                foreach ($row as $r) {

                                                    echo ' <option value="' . $r['id_obra'] . '">' . $r['nome_obra'] . '</option>';
                                                }
                                            } else {

                                                foreach ($row as $r) {

                                                    echo ' <option value="' . $r['id_obra'] . '">' . $r['nome_obra'] . '</option>';
                                                }
                                            }
                                        }
                                        ?>


                                   </select>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Permissions-->
                        <div class="fv-row">
                            <!--begin::Label-->
                            <label class="fs-5 fw-bold form-label mb-2">Tipos de Suporte</label>
                            <!--end::Label-->

                            <!--begin::Table wrapper-->
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table align-middle table-row-dashed fs-6 gy-5">
                                    <!--begin::Table body-->
                                    <tbody class="text-gray-600 fw-semibold">
                                        <!--begin::Table row-->
                                        <tr>
                                            <td class="text-gray-800">
                                                Quero saber de Tudo!

                                                
<span class="ms-1"  data-bs-toggle="tooltip" title="Allows a full access to the system" >
	<i class="ki-duotone ki-information-5 text-gray-500 fs-6"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i></span>                                            </td>
                                            <td>
                                                <!--begin::Checkbox-->
                                                <label class="form-check form-check-sm form-check-custom form-check-solid me-9">
                                                    <input class="form-check-input" type="checkbox" value="" id="kt_roles_select_all" />
                                                    <span class="form-check-label" for="kt_roles_select_all">
                                                        Selecionar todos
                                                    </span>
                                                </label>
                                                <!--end::Checkbox-->
                                            </td>
                                        </tr>
                                        <!--end::Table row-->
                                          
                                        <!--begin::Table row-->
                                        <?php /* trazemos os tipos de suporte que o STEP gerencia */

                                        $sql_tipo_suporte = $conexao->prepare("SELECT * FROM usuarios_projeto up
                                        INNER JOIN notificacoes_usuario au ON au.id_usuario = up.id_usuario
                                        LEFT JOIN tipo_suporte_alertas tsa ON tsa.id_usuario = au.id_usuario
                                        LEFT JOIN tipo_suporte ts ON ts.id_tipo_suporte = tsa.id_tipo_suporte

                                        WHERE up.id_usuario = '$id_usuario_sessao' 
                                        AND up.id_obra = '$projeto_atual' 
                                        AND ts.status_tipo_suporte='1' 
                                        ORDER BY ts.nome_suporte ASC") 
                                        or die($conexao->error);
                                       
                                        $sql_tipo_suporte->execute();
                                        $total_tipo_suporte = $sql_tipo_suporte->rowCount();
                                        $row_tipo_suporte = $sql_tipo_suporte->fetchALL(PDO::FETCH_ASSOC);


                                        if($total_tipo_suporte > 0){

                                        foreach($row_tipo_suporte as $r_tipo_suporte){

                                            echo '<tr>
                                            <!--begin::Label-->
                                            <td class="text-gray-800">Nome da Categoria do Suporte</td>
                                            <!--end::Label-->

                                            <!--begin::Input group-->
                                            <td>
                                                <!--begin::Wrapper-->
                                                <div class="d-flex">
                                                    <!--begin::Checkbox-->
                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                        <input class="form-check-input" type="checkbox" value="" name="user_management_read">
                                                        <span class="form-check-label">
                                                            E-mail
                                                        </span>
                                                    </label>
                                                    <!--end::Checkbox-->

                                                    <!--begin::Checkbox-->
                                                    <label class="form-check form-check-custom form-check-solid me-5 me-lg-20">
                                                        <input class="form-check-input" type="checkbox" value="" name="user_management_write">
                                                        <span class="form-check-label">
                                                           SMS
                                                        </span>
                                                    </label>
                                                    <!--end::Checkbox-->

                                                    <!--begin::Checkbox-->
                                                    <label class="form-check form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="checkbox" value="" name="user_management_create">
                                                        <span class="form-check-label">
                                                           Whatsapp
                                                        </span>
                                                    </label>
                                                    <!--end::Checkbox-->
                                                </div>
                                                <!--end::Wrapper-->
                                            </td>
                                            <!--end::Input group-->
                                        </tr>';


                                        }
                                    }else{
                                        echo '<tr>
                                        <!--begin::Label-->
                                        <td class="text-gray-600">Não foram localizadas Categorias de Suporte, definidas para gestão de alertas, até o momento.</td>
                                        <!--end::Label-->

                                        </tr>';
                                    
                                    }


                                        ?>
                                            


                                          </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table wrapper-->
                        </div>
                        <!--end::Permissions-->
                    </div>
                    <!--end::Scroll-->

                    <!--begin::Actions-->
                    <div class="text-center pt-15">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Fechar</button>

                     
                       
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->