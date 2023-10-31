<?php
 // buffer de saída de dados do php]
// Instancia Conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$_SESSION['pagina_atual'] = 'Visão geral da Conta do Usuário';

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


// pega hora atual php
$hora_atual = date('H:i');

function intervalo($entrada, $saida)
{
    $entrada = explode(':', $entrada);
    $saida = explode(':', $saida);
    $minutos = ($saida[0] - $entrada[0]) * 60 + $saida[1] - $entrada[1];
    if ($minutos < 0) $minutos += 24 * 60;
    return sprintf('%d:%d', $minutos / 60, $minutos % 60);
}


$id_usuario = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {

    $id_usuario = trim($_GET['id']);

} elseif (isset($_COOKIE['id_usuario_sessao']) && is_numeric($_COOKIE['id_usuario_sessao'])) {

    $id_usuario = trim($_COOKIE['id_usuario_sessao']);

}



if($id_usuario === '' || $id_usuario === null){
    $value = 'Sentimos muito! <br/>O STEP Não Conseguiu Validar o usuário da Consulta do Perfil, caso o Erro Persista, por gentileza entre em contato com o Suporte.';

    $_SESSION['error'] =  $value;
   
    header("Location: /views/dashboard.php");
    exit;
}




$sql_user = $conexao->query("SELECT * FROM usuarios WHERE id='$id_usuario'");

$verifica = $sql_user->rowCount();

if($verifica <= 0){

    $value = 'Sentimos muito! <br/>O STEP detectou uma tentativa de acesso incorreta.';

    $_SESSION['error'] =  $value;
   
    header("Location: /views/login/sign-in.php");
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
        <!--begin::Main-->
        <!--begin::Root-->
        <!--begin::loader-->
        <div class="page-loader flex-column">
            <img alt="Logo" class="max-h-75px" src="assets/media/logos/logo-4.png" />
            <div class="d-flex align-items-center mt-5">
                <span class="spinner-border text-primary" role="status"></span>
                <span class="text-muted fs-6 fw-semibold ms-5">Carregando Componentes... <br>Por favor, aguarde.</span>
            </div>
        </div>
        <!--end::Loader-->
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
                            <?php include_once "../../views/conta-usuario/topo-perfil.php"; ?>
                            <!--begin::details View-->
                            <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
                                <!--begin::Card header-->
                                <div class="card-header cursor-pointer">
                                    <!--begin::Card title-->
                                    <div class="card-title m-0">
                                        <h3 class="fw-bold m-0">Detalhes do Perfil</h3>
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Action-->
                                    <a href="../../views/conta-usuario/settings.php?id=<?= $id_usuario; ?>" class="btn btn-primary align-self-center">Editar Perfil</a>
                                    <!--end::Action-->
                                </div>
                                <!--begin::Card header-->
                                <?php

                                $sql_usuario = $conexao->query("SELECT 
u.nome as nome_usuario,
u.nivel,
u.bd_nome,
u.bd_id,
u.status,
u.telefone_verificado,
u.mensagem_user,
u.telefone AS telefone_usuario,
colab.nome as nome_colaborador,
colab.sobrenome as sobrenome_colaborador,
cont.nome as nome_contato,
cont.sobrenome as sobrenome_contato,
colab.cel_corporativo as tel_colaborador,
cont.cel_corporativo as tel_contato,
cli_colab.nome_fantasia as cliente_colab,
cli_cont.nome_fantasia as cliente_contato,
al.*

 FROM usuarios u
LEFT JOIN colaboradores colab ON colab.id_colaborador=u.bd_id
LEFT JOIN contatos cont ON cont.id_contato = u.bd_id
 LEFT JOIN clientes cli_colab ON cli_colab.id_cliente=colab.filial
LEFT JOIN clientes cli_cont ON cli_cont.id_cliente = cont.id_cliente
LEFT JOIN notificacoes_usuario al ON u.id = al.id_usuario
WHERE u.id='$id_usuario'
");

                                $conta_user = $sql_usuario->rowCount();


                                if ($conta_user > 0) {

                                    $r = $sql_usuario->fetch(PDO::FETCH_ASSOC);

                                    $tel_colaborador = $r['telefone_usuario'];
                                    $telefone = $r['telefone_usuario'];

                                    

                                    $cliente_colab = $r['cliente_colab'];
                                    $cliente_contato = $r['cliente_contato'];

                                    if ($r['bd_nome'] != 'contatos') {
                                        $cliente = $cliente_colab;
                                        $nome_usuario = $r['nome_colaborador'];
                                        $sobrenome_usuario = $r['sobrenome_colaborador'] ;
                                    } else {
                                        $cliente = $cliente_contato;
                                        $nome_usuario = $r['nome_contato'];
                                        $sobrenome_usuario = $r['sobrenome_contato'] ;
                                    }

                                 
                                    $mensagem_user = $r['mensagem_user'];

                                  

                                    $alerta_email = isset($r['alerta_email']) ? 'E-mail' : 'E-mail Desabilitado';
                                    $alerta_sms = isset($r['alerta_sms']) ? 'SMS' : 'SMS Desabilitado';
                                    $alerta_whats = isset($r['alerta_whats']) ? 'Whatsapp' : 'WhatsApp Desabilitado';


                                    $status_acesso_usuario = $r['status'];

                                    if($status_acesso_usuario=='1'){

                                        $status_acesso = 'Ativo';
                                        $css_status_acesso = 'success';

                                    }else{
                                        $status_acesso = 'Inativo';
                                        $css_status_acesso = 'danger';
                                    }



                                    $status_telefone_user = $r['telefone_verificado'] ;

                                    if($status_telefone_user=='1'){

                                        $status_telefone = 'Verificado';

                                        $css_status_telefone = 'success';

                                    } else {

                                        $status_telefone = 'Não Verificado';

                                        $css_status_telefone = 'danger';
                                    }
                                   


                                ?>
                                    <!--begin::Card body-->
                                    <div class="card-body p-9">
                                        <!--begin::Row-->
                                        <div class="row mb-7">
                                            <!--begin::Label-->
                                            <label class="col-lg-4 fw-semibold text-muted">Nome Completo</label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8">
                                                <span class="fw-bold fs-6 text-gray-800"><?= $nome_usuario; ?> <?= $sobrenome_usuario; ?></span>
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->
                                        <!--begin::Input group-->
                                        <div class="row mb-7">
                                            <!--begin::Label-->
                                            <label class="col-lg-4 fw-semibold text-muted">Cliente</label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <span class="fw-semibold text-gray-800 fs-6"><?= $cliente; ?></span>
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Input group-->
                                        <div class="row mb-7">
                                            <!--begin::Label-->
                                            <label class="col-lg-4 fw-semibold text-muted">WhatsApp
                                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Número de Telefone Ativo, Obrigatório Conta do Whatsapp"></i></label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 d-flex align-items-center">
                                                <span class="fw-bold fs-6 text-gray-800 me-2"><?= $telefone; ?></span>
                                                <span class="badge badge-<?= $css_status_telefone; ?>"><?= $status_telefone; ?></span>
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Input group-->

                                       
                                        <!--begin::Input group-->
                                        <div class="row mb-7">
                                                <!--begin::Label-->
                                                <label class="col-lg-4 col-form-label  fw-semibold fs-6">Comunicações</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-8 fv-row">
                                                    <!--begin::Options-->
                                                    <div class="d-flex align-items-center mt-3 text-gray-800">
                                                       As Notificações do STEP são personalizadas! Cada usuário poderá escolher como deseja receber as notificações do sistema.
                                                    </div>
                                                    <!--end::Options-->
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Projetos que Participa-->
                              <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                    <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M13.0079 2.6L15.7079 7.2L21.0079 8.4C21.9079 8.6 22.3079 9.7 21.7079 10.4L18.1079 14.4L18.6079 19.8C18.7079 20.7 17.7079 21.4 16.9079 21L12.0079 18.8L7.10785 21C6.20785 21.4 5.30786 20.7 5.40786 19.8L5.90786 14.4L2.30785 10.4C1.70785 9.7 2.00786 8.6 3.00786 8.4L8.30785 7.2L11.0079 2.6C11.3079 1.8 12.5079 1.8 13.0079 2.6Z" fill="currentColor"/>
</svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <label class="col-lg-4 col-form-label  fw-semibold fs-6">Projetos que Participa</label>
                                  

                                </div>
                                <!--end::Number-->
                                <?php
$sql = "SELECT up.*, o.nome_obra, u.nome
        FROM usuarios_projeto up
        INNER JOIN obras o ON up.id_obra = o.id_obra
        INNER JOIN usuarios u ON up.id_usuario = u.id
        WHERE up.id_usuario = :id_usuario";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->execute();
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_projetos = count($projetos);
echo '<div class="scroll-y mh-300px mh-lg-150px">';
foreach($projetos as $rproj) {
    echo '<div class="fw-semibold fs-6 text-gray-500 py-2">Projeto: <span class="fw-semibold fs-6 text-gray-700">' . $rproj['nome_obra'] . '</span></div>';
    echo '<div class="fw-semibold fs-6 text-gray-500" py-2>Usuário: <span class="fw-semibold fs-6 text-gray-700">' . $rproj['nome'] . '</span></div>';
    // A coluna 'nivel' não foi selecionada na consulta, então esta linha foi comentada
    echo '<div class="fw-semibold fs-6 text-gray-500 py-2">Nível no Projeto: <span class="fw-semibold fs-6 text-gray-700">' . $rproj['nivel'] . '</span></div>';
    echo '<div class="separator separator-dashed my-6 text-gray-800"></div>';
}
echo '</div>';
?>

                                <!--begin::Label-->
                                
                                <!--end::Label-->
                            </div>
                            <!--end::Projetos que Participa-->


                                        <?php
                                        if ($mensagem_user != '') {
                                        ?>
                                            <!--begin::Notice-->
                                            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                                                <!--begin::Icon-->
                                                <!--begin::Svg Icon | path: icons/duotune/general/gen044.svg-->
                                                <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor" />
                                                        <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="currentColor" />
                                                        <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                                <!--end::Icon-->
                                                <!--begin::Wrapper-->
                                                <div class="d-flex flex-stack flex-grow-1">
                                                    <!--begin::Content-->
                                                    <div class="fw-semibold">
                                                        <h4 class="text-gray-900 fw-bold">O STEP Solicita sua Atenção!</h4>
                                                        <div class="fs-6 text-gray-700"><?= $mensagem_user; ?>

                                                        </div>
                                                    </div>
                                                    <!--end::Content-->
                                                </div>
                                                <!--end::Wrapper-->
                                            </div>
                                            <!--end::Notice-->
                                        <?php
                                        }
                                        ?>

                                    </div>
                                    <!--end::Card body-->
                                <?php } ?>
                            </div>
                            <!--end::details View-->
                            <!--begin::Row-->
                            <div class="row gy-5 g-xl-10">
                                <!--begin::Col-->
                                <div class="col-xl-8 mb-xl-10">
                                    <!--begin::Chart widget 5-->
                                    <div class="card card-flush h-lg-100">
                                        <!--begin::Header-->
                                        <div class="card-header flex-nowrap pt-5">
                                            <!--begin::Title-->
                                            <h3 class="card-title align-items-start flex-column">
                                                <span class="card-label fw-bold text-dark">Abertura de Suporte por Categorias</span>
                                                <?php
                                                $sql_ticket = $conexao->query("SELECT  COUNT(s.id_suporte) as total_suporte
                                                                            FROM  suporte s
                                                                            WHERE s.quem_abriu='$id_usuario'
                                                                        ");

                                                $conta_ticket = $sql_ticket->rowCount();

                                                if ($conta_ticket > 0) {

                                                    $r = $sql_ticket->fetch(PDO::FETCH_ASSOC);
                                                    $total_suporte = $r['total_suporte'];
                                                    echo '<span class="text-gray-400 pt-2 fw-semibold fs-6">' . $total_suporte . ' tickets gerados</span>';
                                                }

                                                ?>

                                            </h3>
                                            <!--end::Title-->

                                        </div>
                                        <!--end::Header-->
                                        <!--begin::Body-->
                                        <div class="card-body pt-5 ps-6">
                                            <div id="kt_charts_widget_5" class="min-h-auto"></div>
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::Chart widget 5-->
                                </div>
                                <!--end::Col-->


                                <!--begin::Col-->
                                <div class="col-xl-4">
                                    <!--begin::List widget 5-->
                                    <div class="card card-flush h-xl-100">
                                        <!--begin::Header-->
                                        <div class="card-header pt-7">
                                            <!--begin::Title-->
                                            <h3 class="card-title align-items-start flex-column">
                                                <span class="card-label fw-bold text-dark">EPI's Utilizados</span>
                                                <span class="text-gray-400 mt-1 fw-semibold fs-6">Extrato de EPI's do Usuário</span>
                                            </h3>
                                            <!--end::Title-->
                                            <!--begin::Toolbar-->
                                            <div class="card-toolbar">
                                                <a href="javascript:;" class="btn btn-sm btn-light">Detalhes</a>
                                            </div>
                                            <!--end::Toolbar-->
                                        </div>
                                        <!--end::Header-->
                                        <!--begin::Body-->
                                        <div class="card-body">
                                            <!--begin::Scroll-->
                                            <div class="hover-scroll-overlay-y pe-6 me-n6" style="height: 415px">

                                                <?php

                                                $sql_epi = $conexao->query("SELECT * FROM epis_ca e_ca
                                                INNER JOIN epi_usuario e_user On e_user.id_epi = e_ca.id_epi
                                                WHERE e_user.id_usuario='$id_usuario' ");

                                                $conta_epi = $sql_epi->rowCount();


                                                if ($conta_epi > 0) {

                                                    $row = $sql_epi->fetchALL(PDO::FETCH_ASSOC);

                                                    foreach ($row as $rp) {

                                                        $nome_epi_ca = $rp['nome_epi_ca'];


                                                ?>
                                                        <!--begin::Item-->
                                                        <div class="border border-dashed border-gray-300 rounded px-7 py-3 mb-6">
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-stack mb-3">
                                                                <!--begin::Wrapper-->
                                                                <div class="me-3">
                                                                    <!--begin::Icon-->
                                                                    <img src="assets/media/stock/ecommerce/210.gif" class="w-50px ms-n1 me-1" alt="" />
                                                                    <!--end::Icon-->
                                                                    <!--begin::Title-->
                                                                    <a href="../../tema/dist/apps/ecommerce/catalog/edit-product.php?id=<?= $rp['id_epi_usuario']; ?>" class="text-gray-800 text-hover-primary fw-bold">Elephant 1802</a>
                                                                    <!--end::Title-->
                                                                </div>
                                                                <!--end::Wrapper-->
                                                                <!--begin::Action-->
                                                                <div class="m-0">
                                                                    <!--begin::Menu-->
                                                                    <button class="btn btn-icon btn-color-gray-400 btn-active-color-primary justify-content-end" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-overflow="true">
                                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen023.svg-->
                                                                        <span class="svg-icon svg-icon-1">
                                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="4" fill="currentColor" />
                                                                                <rect x="11" y="11" width="2.6" height="2.6" rx="1.3" fill="currentColor" />
                                                                                <rect x="15" y="11" width="2.6" height="2.6" rx="1.3" fill="currentColor" />
                                                                                <rect x="7" y="11" width="2.6" height="2.6" rx="1.3" fill="currentColor" />
                                                                            </svg>
                                                                        </span>
                                                                        <!--end::Svg Icon-->
                                                                    </button>
                                                                    <!--begin::Menu 2-->
                                                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px" data-kt-menu="true">
                                                                        <!--begin::Menu item-->
                                                                        <div class="menu-item px-3">
                                                                            <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">Ações Rápidas</div>
                                                                        </div>
                                                                        <!--end::Menu item-->
                                                                        <!--begin::Menu separator-->
                                                                        <div class="separator mb-3 opacity-75"></div>
                                                                        <!--end::Menu separator-->
                                                                        <!--begin::Menu item-->
                                                                        <div class="menu-item px-3">
                                                                            <a href="javascript:;" class="menu-link px-3">New Ticket</a>
                                                                        </div>
                                                                        <!--end::Menu item-->
                                                                        <!--begin::Menu item-->
                                                                        <div class="menu-item px-3">
                                                                            <a href="javascript:;" class="menu-link px-3">New Customer</a>
                                                                        </div>
                                                                        <!--end::Menu item-->
                                                                        <!--begin::Menu item-->
                                                                        <div class="menu-item px-3" data-kt-menu-trigger="hover" data-kt-menu-placement="right-start">
                                                                            <!--begin::Menu item-->
                                                                            <a href="javascript:;" class="menu-link px-3">
                                                                                <span class="menu-title">New Group</span>
                                                                                <span class="menu-arrow"></span>
                                                                            </a>
                                                                            <!--end::Menu item-->
                                                                            <!--begin::Menu sub-->
                                                                            <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                                                <!--begin::Menu item-->
                                                                                <div class="menu-item px-3">
                                                                                    <a href="javascript:;" class="menu-link px-3">Admin Group</a>
                                                                                </div>
                                                                                <!--end::Menu item-->
                                                                                <!--begin::Menu item-->
                                                                                <div class="menu-item px-3">
                                                                                    <a href="javascript:;" class="menu-link px-3">Staff Group</a>
                                                                                </div>
                                                                                <!--end::Menu item-->
                                                                                <!--begin::Menu item-->
                                                                                <div class="menu-item px-3">
                                                                                    <a href="javascript:;" class="menu-link px-3">Member Group</a>
                                                                                </div>
                                                                                <!--end::Menu item-->
                                                                            </div>
                                                                            <!--end::Menu sub-->
                                                                        </div>
                                                                        <!--end::Menu item-->
                                                                        <!--begin::Menu item-->
                                                                        <div class="menu-item px-3">
                                                                            <a href="javascript:;" class="menu-link px-3">New Contact</a>
                                                                        </div>
                                                                        <!--end::Menu item-->
                                                                        <!--begin::Menu separator-->
                                                                        <div class="separator mt-3 opacity-75"></div>
                                                                        <!--end::Menu separator-->
                                                                        <!--begin::Menu item-->
                                                                        <div class="menu-item px-3">
                                                                            <div class="menu-content px-3 py-3">
                                                                                <a class="btn btn-primary btn-sm px-4" href="javascript:;">Generate Reports</a>
                                                                            </div>
                                                                        </div>
                                                                        <!--end::Menu item-->
                                                                    </div>
                                                                    <!--end::Menu 2-->
                                                                    <!--end::Menu-->
                                                                </div>
                                                                <!--end::Action-->
                                                            </div>
                                                            <!--end::Info-->
                                                            <!--begin::Customer-->
                                                            <div class="d-flex flex-stack">
                                                                <!--begin::Name-->
                                                                <span class="text-gray-400 fw-bold">To:
                                                                    <a href="../../tema/dist/apps/ecommerce/sales/details.php" class="text-gray-800 text-hover-primary fw-bold">Jason Bourne</a></span>
                                                                <!--end::Name-->
                                                                <!--begin::Label-->
                                                                <span class="badge badge-light-success">Delivered</span>
                                                                <!--end::Label-->
                                                            </div>
                                                            <!--end::Customer-->
                                                        </div>
                                                        <!--end::Item-->
                                                <?php

                                                    }
                                                } else {


                                                    echo  '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-primary">EPI</h4>
														<span>Não foram localizados EPIs, fornecidos para este Usuário.</span>
													</div>
												</div>';
                                                } ?>



                                            </div>
                                            <!--end::Scroll-->
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::List widget 5-->
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->

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
        <!--end::Activities drawer-->
 <!--begin::Chat drawer-->
 <?php include '../../views/chat/chat-usuario.php'; ?>
 
  
        <!--end::Engage drawers-->

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
        <script src="../../node_modules/print-js/dist/print.js"></script>

        <script src="../../js/dashboard/step-js.js"></script>
        <script src="../../js/suportes/chat/chat.js"></script>
        
        
        <script src="assets/js/custom/utilities/modals/users-search.js"></script>
        <script src="../../js/usuarios/widget-5.js"></script>
        <!--end::Custom Javascript-->
        <!--end::Javascript-->
    </body>
    <!--end::Body-->

    </hmtl>