<?php
 // buffer de saída de dados do php]
// Instancia Conexão PDO
require_once "../../conexao.php";
$conexao = Conexao::getInstance();
require_once "./../../crud/login/verifica_sessao.php";

$_SESSION['pagina_atual'] = 'Logs da Conta';

$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';

if ($nivel_acesso_user_sessao == "" or  $nivel_acesso_user_sessao == 'undefined') {


    $value = 'Sentimos muito! <br/>Sentimos muito! <br/>O STEP Não Conseguiu Validar seu Login Ativo na Sessão, Por gentileza, refaça seu Login.';

    $_SESSION['error'] =  $value;
   
    header("Location: /views/login/sign-in.php");
    exit;
}



$hr = date(" H ");
if ($hr >= 12 && $hr < 18) {
    $saudacao = "Boa tarde!";
} else if ($hr >= 0 && $hr < 12) {
    $saudacao = "Bom dia!";
} else {
    $saudacao = "Boa noite!";
}

//tratativa caso o cliente seja o acesso solicitado do Dashboard:
// localizo o cliente atraves do id_usuario_sessao: id do bd
//nivel_acesso_usuario: cliente




function mintohora($minutos)
{
    $hora = floor($minutos / 60);
    $resto = $minutos % 60;
    return $hora . ':' . $resto;
}

$_SESSION['pagina_atual'] = 'Logs Conta';

$id_usuario = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {

    $id_usuario = trim($_GET['id']);

} elseif (isset($_COOKIE['id_usuario_sessao']) && is_numeric($_COOKIE['id_usuario_sessao'])) {

    $id_usuario = trim($_COOKIE['id_usuario_sessao']);

}



if($id_usuario === '' || $id_usuario === null){
    $value = 'Sentimos muito! <br/>O STEP Não Conseguiu Validar o usuário da Consulta do LOG, caso o Erro Persista, por gentileza entre em contato com o Suporte.';

    $_SESSION['error'] =  $value;
   
    header("Location: /views/dashboard.php");
    exit;
}



?>
<!DOCTYPE html>
<!--
Author: Fabiano Barros
Product Name: STEP Sistema de Tratamento EP
Purchase: https://step.eco.br
Website: http://step.eco.br
Contact: dev@grupoep.com.br
Versão: 3.01
-->
<html lang="pt-br">

    <head>
        <base href="../../tema/dist/">
        <title>STEP &amp; GrupoEP</title>
        <meta charset="utf-8" />
        <meta name="description" content="Sistema de Tratamento Grupo EP - Iot - Tratamento de Efluentes" />
        <meta name="keywords" content="STEP, GrupoEP, EP, Tratamento de Efluentes, iot, Sistema, Controle, Tratamento Inteligente, água, osmose, osmose reversa" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="article" />
        <meta property="og:title" content="STEP, GrupoEP, EP, Tratamento de Efluentes, iot, Sistema, Controle, Tratamento Inteligente, água, osmose, osmose revers" />
        <meta property="og:url" content="https://grupoep.com.br/eptech" />
        <meta property="og:site_name" content="STEP | GrupoEP" />
        <link rel="canonical" href="https://step.eco.br" />
        <link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
        <!--begin::Fonts-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
        <!--end::Fonts-->
        <!--begin::Vendor Stylesheets(used by this page)-->
        <link href="assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/custom/vis-timeline/vis-timeline.bundle.css" rel="stylesheet" type="text/css" />
        <!--end::Vendor Stylesheets-->
        <!--begin::Global Stylesheets Bundle(used by all pages)-->
        <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../../node_modules/print-js/dist/print.css">
        <!--end::Global Stylesheets Bundle-->
    </head>
    <!--end::Head-->
    <!--begin::Body-->

    <body data-kt-name="metronic" id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled">
        <!--begin::Theme mode setup on page load-->
        <script>
            if (document.documentElement) {
                 const defaultThemeMode = "dark";
                const name = document.body.getAttribute("data-kt-name");
                let themeMode = localStorage.getItem("kt_" + (name !== null ? name + "_" : "") + "theme_mode_value");
                if (themeMode === null) {
                    if (defaultThemeMode === "system") {
                        themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
                    } else {
                        themeMode = defaultThemeMode;
                    }
                }
                document.documentElement.setAttribute("data-theme", themeMode);
            }
        </script>
        <!--end::Theme mode setup on page load-->

        <!--begin::loader-->
        <div class="page-loader flex-column">
            <img alt="Logo" class="max-h-75px" src="assets/media/logos/logo-4.png" />
            <div class="d-flex align-items-center mt-5">
                <span class="spinner-border text-primary" role="status"></span>
                <span class="text-muted fs-6 fw-semibold ms-5">Carregando Componentes... <br>Por favor, aguarde.</span>
            </div>
        </div>
        <!--end::Loader-->
        <!--begin::Main-->
        <!--begin::Root-->
        <div class="d-flex flex-column flex-root">
            <!--begin::Page-->
            <div class="page d-flex flex-row flex-column-fluid">
                <!--begin::Wrapper-->
                <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                    <!--begin::Header-->
                    <?php include_once '../header.php'; ?>
                    <!--end::Header-->


                    <!--begin::Toolbar-->
                    <?php include_once '../toolbar.php'; ?>
                    <!--end::Toolbar-->
                    <!--begin::Container-->
                    <div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
                        <!--begin::Post-->
                        <div class="content flex-row-fluid" id="kt_content">
                            <!--begin::Navbar-->
                            <?php include_once "../../views/conta-usuario/topo-perfil.php"; ?>
                            <!--end::Navbar-->
                            <!--begin::Sessões de Login-->
                            <div class="card mb-5 mb-lg-10">
                                <!--begin::Card header-->
                                <div class="card-header">
                                    <!--begin::Heading-->
                                    <div class="card-title">
                                        <h3>Sessões de Login</h3>
                                    </div>
                                    <!--end::Heading-->

                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body p-0">
                                    <!--begin::Table wrapper-->
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table class="table table-flush align-middle table-row-bordered table-row-solid gy-4 gs-9">
                                            <!--begin::Thead-->
                                            <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                                                <tr>
                                                    <th class="min-w-250px">Rotina</th>
                                                    <th class="min-w-100px">Ação</th>
                                                    <th class="min-w-100px">Histórico</th>
                                                    <th class="min-w-150px">IP Address</th>
                                                    <th class="min-w-150px">Data</th>
                                                </tr>
                                            </thead>
                                            <!--end::Thead-->
                                            <!--begin::Tbody-->
                                            <tbody class="fw-6 fw-semibold text-gray-600">


                                                <?php


                                                $sql_tb_log = $conexao->query("SELECT * FROM log_sistema WHERE usuario='$id_usuario ' AND(acao like '%LOGIN%' OR acao like '%LOGOUT%') ORDER BY datahora DESC LIMIT 0,11");

                                                $conta_log = $sql_tb_log->rowCount();

                                                if ($conta_log > 0) {


                                                    foreach ($sql_tb_log as $r) {

                                                        $datahora = $r['datahora'];

                                                        echo '<tr>
                                                    <td>
                                                        <a href="javscript:;" class="text-hover-primary text-gray-600">' . $r['rotina'] . '</a>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light-success fs-7 fw-bold">' . $r['acao'] . '</span>
                                                    </td>
                                                    <td>' . $r['historico'] . '</td>
                                                    <td>' . $r['ip'] . '</td>
                                                    <td>' . date('d/m/Y H:i:s', strtotime($datahora)). '</td>
                                                </tr>';
                                                    }
                                                }

                                                ?>


                                            </tbody>
                                            <!--end::Tbody-->
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Table wrapper-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Sessões de Login-->
                            <!--begin::Card-->
                            <div class="card pt-4">
                                <!--begin::Card header-->
                                <div class="card-header border-0">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <h2>Logs</h2>
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <!--begin::Button-->
                                        <button type="button" class="btn btn-sm btn-light-primary gera_relatorio" data-id="tabela-log-usuario" data-titulo="Logs do Usuário">
                                            <!--begin::Svg Icon | path: icons/duotune/files/fil021.svg-->
                                            <span class="svg-icon svg-icon-3">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3" d="M19 15C20.7 15 22 13.7 22 12C22 10.3 20.7 9 19 9C18.9 9 18.9 9 18.8 9C18.9 8.7 19 8.3 19 8C19 6.3 17.7 5 16 5C15.4 5 14.8 5.2 14.3 5.5C13.4 4 11.8 3 10 3C7.2 3 5 5.2 5 8C5 8.3 5 8.7 5.1 9H5C3.3 9 2 10.3 2 12C2 13.7 3.3 15 5 15H19Z" fill="currentColor" />
                                                    <path d="M13 17.4V12C13 11.4 12.6 11 12 11C11.4 11 11 11.4 11 12V17.4H13Z" fill="currentColor" />
                                                    <path opacity="0.3" d="M8 17.4H16L12.7 20.7C12.3 21.1 11.7 21.1 11.3 20.7L8 17.4Z" fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->Download Report
                                        </button>
                                        <!--end::Button-->
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body py-0">
                                    <!--begin::Table wrapper-->
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table class="table align-middle table-row-dashed fw-semibold text-gray-600 fs-6 gy-5" id="tabela-log-usuario">
                                            <!--begin::Table body-->
                                            <tbody>
                                                <!--begin::Table row-->

                                                <?php


                                                $sql_tb_log = $conexao->query("SELECT * FROM log_sistema WHERE usuario='$id_usuario ' AND (acao not like '%LOGIN%') AND (acao not like '%LOGOUT%') ORDER BY datahora DESC LIMIT 0,11");

                                                $conta_log = $sql_tb_log->rowCount();

                                                if ($conta_log > 0) {


                                                    foreach ($sql_tb_log as $r) {

                                                        $datahora = $r['datahora'];

                                                        echo ' <tr>
                                                    <!--begin::Badge=-->
                                                    <td class="min-w-70px">
                                                        <div class="badge badge-light-success">' . $r['acao'] . '</div>
                                                    </td>
                                                    <!--end::Badge=-->
                                                    <!--begin::Status=-->
                                                    <td>' . $r['historico'] . '</td>
                                                    <!--end::Status=-->
                                                    <!--begin::Timestamp=-->
                                                    <td class="pe-0 text-end min-w-200px">' .date('d/m/Y H:i:s', strtotime($datahora)) . '</td>
                                                    <!--end::Timestamp=-->
                                                </tr>
                                                <!--end::Table row-->';
                                                    }
                                                } else {
                                                    echo '<tr>
                                                    <td colspan="3" class="text-center">Nenhum registro encontrado</td>
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
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Post-->
                    </div>
                    <!--end::Container-->
                    <!--begin::Footer-->
                    <?php include '../../views/footer.php'; ?>
                    <!--end::Footer-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Page-->
        </div>
        <!--end::Root-->
        <!--begin::Drawers-->
        <!--begin::Activities drawer-->
        <?php include '../../views/conta-usuario/atividade-usuario.php'; ?>

        <!--end::Activities drawer-->
 <!--begin::Chat drawer-->
 <?php include '../../views/chat/chat-usuario.php'; ?>
   
    
   <!--end::Chat drawer-->
        <!--end::Drawers-->
        <!--end::Main-->
      
        <!--begin::Scrolltop-->
        <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
            <span class="svg-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor" />
                    <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Scrolltop-->
        <!--begin::Modals-->
        
        <!--end::Modal - Upgrade plan-->
    
        <!--end::Modals-->
        <!--begin::Javascript-->
        <script>
            var hostUrl = "assets/";
        </script>
        <!--begin::Global Javascript Bundle(used by all pages)-->
        <script src="assets/plugins/global/plugins.bundle.js"></script>
        <script src="assets/js/scripts.bundle.js"></script>
        <!--end::Global Javascript Bundle-->
        <!--begin::Vendors Javascript(used by this page)-->
        <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
        <!--end::Vendors Javascript-->
        <!--begin::Custom Javascript(used by this page)-->
        <script src="assets/js/widgets.bundle.js"></script>
        <script src="assets/js/custom/widgets.js"></script>
        <script src="../../js/suportes/chat/chat.js"></script>
  
        <script src="assets/js/custom/utilities/modals/users-search.js"></script>
        <script src="../../js/dashboard/step-js.js"></script>
        <script src="../../node_modules/print-js/dist/print.js"></script>
        <!--end::Custom Javascript-->
        <!--end::Javascript-->
    </body>
    <!--end::Body-->

    </html>