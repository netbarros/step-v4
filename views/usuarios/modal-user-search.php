<?php 
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$projeto_id = null; // valor padrão

if (isset($_POST['id']) && filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
    $projeto_id = intval($_POST['id']);
}




 if ($projeto_id === null) {

   

    echo'<div class="alert alert-info d-flex align-items-center p-5 mb-10">
    <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
<span class="svg-icon svg-icon-2hx svg-icon-info me-4"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
</svg>
</span>
<!--end::Svg Icon-->                    <div class="d-flex flex-column">
        <h4 class="mb-1 text-danger">Busca Indisponível no momento.</h4>
        <span>Caso esteja vendo esta mensagem, utilize o Filtro no topo do sistema, para selecionar o Projeto que deseja incluir o usuário, feito isso você poderá selecionar e incluir os usuários no Projeto</span>
    </div>
</div>';

    exit;
} 

?>
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header pb-0 border-0 justify-content-end">
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
                    <!--begin::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                        <!--begin::Content-->
                        <div class="text-center mb-13">
                            <h1 class="mb-3">Convidar Colaboradores</h1>
                            <div class="text-muted fw-semibold fs-5">Vincule Usuários ao Projeto Atual.</div>
                        </div>
                        <!--end::Content-->
                        <!--begin::Buscar-->
                        <div  data-kt-search-keypress="true" data-kt-search-min-length="2" data-kt-search-enter="enter" data-kt-search-layout="inline">
                            <!--begin::Form-->
                            <form data-kt-search-element="form" class="w-100 position-relative mb-5" autocomplete="on" name="form_inclui_usuarios_nucleo" id="form_inclui_usuarios_nucleo">
                                <!--begin::Hidden input(Added to disable form autocomplete)-->
                                <input type="hidden" />
                                <!--end::Hidden input-->
                                <!--begin::Icon-->
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-2 svg-icon-lg-1 svg-icon-gray-500 position-absolute top-50 ms-5 translate-middle-y">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <!--end::Icon-->
                                <!--begin::Input-->
                                <input type="text" class="form-control form-control-lg form-control-solid px-15" name="kt_busca_usuarios" id="kt_busca_usuarios" value="" placeholder="Busque por Nome, Nome Completo ou E-mail..." data-kt-search-element="input" />
                                <!--end::Input-->
                                <!--begin::Spinner-->
                                <span class="position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-5" data-kt-search-element="spinner" id="aguarde_spinner">
                                    <span class="spinner-border h-15px w-15px align-middle text-muted"></span>
                                </span>
                                <!--end::Spinner-->
                                <!--begin::Reset-->
                                <span class="btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 me-5 d-none" data-kt-search-element="clear">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                    <span class="svg-icon svg-icon-2 svg-icon-lg-1 me-0">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </span>
                                <!--end::Reset-->
                                <input type="hidden" name="id_projeto" id="id_projeto_busca_usuario" value="<?=$projeto_id;?>">

                                <input type="hidden" name="acao" value="usuarios_projeto">
                              

                                
                            
                            <!--begin::Wrapper-->
                            <div class="py-0">
                                <!--begin::Suggestions-->
                                <div data-kt-search-element="suggestions" >
                                    <!--begin::Heading-->
                                    <h3 class="fw-semibold mb-5" id="searchButtonHeader"></h3>
                                    <!--end::Heading-->

                                </div>
                                <!--end::Suggestions-->
                                <!--begin::Results(add d-none to below element to hide the users list by default)-->

                             
                                
                              
                                    <!--begin::Users-->
                                    <div class="mh-375px scroll-y me-n7 pe-7" id="searchResult">

                                    


                                    </div>
                                    <!--end::Users-->
                                    </form>
                                    <!--begin::Actions-->
                                    <div class="d-flex flex-center mt-15 d-none" id="div_acao_usuarios_nucleo">
                                        <button type="reset" id="kt_modal_users_search_reset" data-bs-dismiss="modal" class="btn btn-active-light me-3">Cancelar</button>
                                        <button type="submit" id="kt_modal_usuarios_nucleo_submit" class="btn btn-primary">Incluir Usuários Selecionados</button>
                                    </div>
                                    <!--end::Actions-->
                                 
                            <!--end::Form-->
                                </div>
                               
                                <!--end::Results-->
                           
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Buscar-->
                   




<script src="../../js/usuarios/users-search.js"></script>
<script src="../../js/usuarios/modal-usuarios.js"></script>