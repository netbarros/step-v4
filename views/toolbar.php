<?php if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();    ?>
<div class="toolbar py-5 py-lg-15" id="kt_toolbar">
    <!--begin::Container-->
    <div id="kt_toolbar_container" class="container-xxl d-flex flex-stack flex-wrap">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column me-3 " id="ProjetoSelecionado">
            <!--begin::Title-->
            <h1 class="d-flex text-white fw-bold my-1 fs-3" id="pagina_atual_usuario_sessao"><?php echo $_SESSION['pagina_atual']; ?></h1>
            <div class="border border-gray-300 border-dashed rounded align-center min-w-125px py-3 px-4 me-6 mb-3 d-none" id='div_nome_projeto'>
                <!--begin::Number-->
                <div class="fs-6 fw-bold  text-gray-400">Projeto Selecionado</div>
                <div class="d-flex align-items-center">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                    <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.3" d="M7 20.5L2 17.6V11.8L7 8.90002L12 11.8V17.6L7 20.5ZM21 20.8V18.5L19 17.3L17 18.5V20.8L19 22L21 20.8Z" fill="currentColor"></path>
                            <path d="M22 14.1V6L15 2L8 6V14.1L15 18.2L22 14.1Z" fill="currentColor"></path>
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <div class="fs-1 fw-bold counted text-gray-800" id='nome_projeto_filtro'><?php echo  $_COOKIE['projeto_atual'] ?? '<small>Nenhum Projeto selecionado</small>'; ?></div>
                </div>
                <!--end::Number-->
                <!--begin::Label-->

                <!--end::Label-->
            </div>



            <!--end::Title-->
        </div>
        <!--end::Page title-->


        



        <!--begin::Actions-->
        <div class="d-flex align-items-center py-3 py-md-1">
            <!--begin::Wrapper-->
            <div class="me-4">
                <!--begin::Back to folders-->
                <button type="button" onclick="history.back()" class="btn btn-icon btn-light-primary me-3 " data-bs-toggle="tooltip" data-bs-placement="top" title="Voltar à Página Anterior">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                    <span class="svg-icon svg-icon-2">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="currentColor" />
                            <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </button>
                <!--end::Back to folders-->
                <!--begin::Menu-->
                <a href="javascript:;" class="btn btn-custom btn-active-white btn-flex btn-color-white btn-active-color-primary fw-bold" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-bs-toggle="tooltip" data-bs-placement="top" title="Filtre o Dashboard por Projeto">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                    <span class="svg-icon svg-icon-5 svg-icon-gray-500 me-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->Filtrar
                </a>
                <!--begin::Menu 1-->
                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_62cfb9f2472af">
                    <!--begin::Header-->
                    <div class="px-7 py-5">
                        <div class="fs-5 text-dark fw-bold">Opções</div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Menu separator-->
                    <div class="separator border-gray-200"></div>
                    <!--end::Menu separator-->
                    <!--begin::Form-->
                    <div class="px-7 py-5">
                        <!--begin::Input group-->
                        <div class="mb-10">
                            <!--begin::Label-->
                            <label class="form-label fw-semibold">Projeto:</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <div>
                                <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Selecione uma opção" data-dropdown-parent="#kt_menu_62cfb9f2472af" id='select_filtro_projeto' data-allow-clear="true">
                                    <option></option>
                                    <?php



                                    $nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';

                                    $nome_projeto = trim(isset($_COOKIE['nome_projeto'])) ? $_COOKIE['nome_projeto'] : '';

                                    $projeto_atual = trim(isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';

                                    $sql_personalizado_projeto_dashboard = '';



                                    $id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';



                                    if ($nivel_acesso_user_sessao != 'admin') {

                                        $sql_personalizado_projeto_dashboard = "AND ( up.id_usuario  = '$id_usuario_sessao')";
                                    } else {

                                        $sql_personalizado_projeto_dashboard = "";
                                    }


                                    $sql_filtro_projeto_dashboard = $conexao->query("SELECT o.id_obra, o.nome_obra, up.nivel as nivel_projeto, u.nivel as nivel_usuario FROM
                                        usuarios_projeto up
                                        INNER JOIN obras o  ON o.id_obra = up.id_obra
                                        INNER JOIN estacoes e ON e.id_obra = o.id_obra
                                       INNER JOIN usuarios u ON u.id = up.id_usuario
                                       WHERE    o.status_cadastro='1' $sql_personalizado_projeto_dashboard  GROUP BY o.id_obra
                                       ORDER BY o.nome_obra ASC");

                                    //print_r($sql_filtro_projeto_dashboard);
                                    $conta_projeto = $sql_filtro_projeto_dashboard->rowCount();

                                    if ($conta_projeto > 0) {

                                        $row = $sql_filtro_projeto_dashboard->fetchALL(PDO::FETCH_ASSOC);





                                        // print_r($row );


                                        if ($nome_projeto != 'undefined') {

                                            $nome_projeto_filtro = $nome_projeto;
                                            $id_projeto_filtro = $projeto_atual;
                                            $nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';


                                            echo ' <option value="' . $id_projeto_filtro . '" selected data-nivelprojeto ="' . $nivel_acesso_user_sessao . '">' . $nome_projeto_filtro . ' </option>';

                                            foreach ($row as $r) {
                                                $nivel_acesso_consulta =  $r['nivel_projeto'] ?? '';


                                                echo ' <option value="' . $r['id_obra'] . '" data-nivelprojeto ="' . $nivel_acesso_consulta . '" >' . $r['nome_obra'] . ' </option>';
                                            }
                                        } else {

                                            foreach ($row as $r) {
                                                $nivel_acesso_consulta =  trim(isset($r['nivel_projeto'])) ? $row['nivel_projeto'] : '';

                                                echo ' <option value="' . $r['id_obra'] . '" data-nivelprojeto ="' . $nivel_acesso_consulta . '">' . $r['nome_obra'] . ' </option>';
                                            }
                                        }
                                    }
                                    ?>


                                </select>
                            </div>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Actions-->
                        <div class="d-flex justify-content-end">

                            <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" id="limpa_filtro_projeto">
                                <span class="indicator-label">
                                    Limpar Filtro por Projeto
                                </span>
                                <span class="indicator-progress">
                                    Aguarde... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>



                        </div>
                        <!--end::Actions-->
                    </div>
                    <!--end::Form-->


                </div>
                <!--end::Menu 1-->
                <!--end::Menu-->
            </div>
            <!--end::Wrapper-->
            <!--begin::Button-->
            <a href="javascript:;" data-theme="light" class="btn btn-bg-white btn-active-color-primary bt_exibe_cockpit" data-bs-toggle="tooltip" data-bs-placement="top" title="Exiba o Monitoramento Detalhado">Cockpit &nbsp;<i class='fa fa-door-closed icone_bt_exibe_cockpit'></i></a>
            <!--end::Button-->


        </div>
        <!--end::Actions-->
    </div>
    <!--end::Container-->
</div>

