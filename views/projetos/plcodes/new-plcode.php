<?php
 // buffer de saída de dados do php]
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';



//$id_projeto = isset($_GET['id']) ? $_GET['id'] : '';

$id_projeto = isset($_GET['id_projeto']) ? $_GET['id_projeto'] : '';

$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';

if ($nivel_acesso_user_sessao == "" or  $nivel_acesso_user_sessao == 'undefined' or $id_projeto == '') {


    $value = 'Sentimos muito! <br/>Sentimos muito! <br/>O STEP Não Conseguiu Validar seu Login Ativo na Sessão, Por gentileza, refaça seu Login.';

    $_SESSION['error'] =  $value;

    header("Location: /views/login/sign-in.php");

    exit;
}

if($nivel_acesso_user_sessao=='cliente'){

    header("Location: /views/dashboard.php");
}

if($nivel_acesso_user_sessao=='supervisor' ){

    header("Location: /views/dashboard.php");
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

if($id_projeto){

// consulta Projeto
$sql_projeto = $conexao->query("SELECT 
o.id_obra,
o.nome_obra,
o.status_cadastro,
o.data_cadastro,
cli.nome_fantasia,
ct.nome as nome_contato,
ct.sobrenome as sobrenome_contato 
FROM obras o
INNER JOIN clientes cli ON cli.id_cliente = o.id_cliente
LEFT JOIN contatos ct ON ct.id_cliente= cli.id_cliente
WHERE o.id_obra='$id_projeto' ");

$conta_projeto = $sql_projeto->rowCount();

if($conta_projeto ==null){

    
    $value = 'Sentimos muito! <br/>O STEP detectou uma tentativa de acesso incorreta.';

    $_SESSION['error'] =  $value;
   
    header("Location: /views/login/sign-in.php");
    exit;
} else {

    $_SESSION['error'] =  '';

}

$r_proj = $sql_projeto->fetch(PDO::FETCH_ASSOC);

$status_cadastro = $r_proj['status_cadastro'];

$brev_nome_projeto = substr($r_proj['nome_obra'], 0, 11);

$_SESSION['pagina_atual'] = 'Novo PLCode para o Projeto: &nbsp; <span class="text-success">'.$r_proj['nome_obra'].'</span>';

if ($status_cadastro == '1') {

    $nome_status = 'Projeto Ativo';
    $classe_status = 'success';
} elseif ($status_cadastro == '2') {

    $nome_status = 'Projeto em Alerta';
    $classe_status = 'danger';
} else {

    $nome_status = 'Projeto Inativo';
    $classe_status = 'secundary';
}

$sql_personalizado = '';

$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';

if ($nivel_acesso_user_sessao == 'supervisor') {

    $sql_personalizado = "AND (e.supervisor = '$id_BD_Colaborador'  OR up.id_usuario  = '$id_usuario_sessao')";
}

if ($nivel_acesso_user_sessao == 'ro') {

    $sql_personalizado = "AND (e.ro = '$id_BD_Colaborador'  OR up.id_usuario  = '$id_usuario_sessao')";
}

if ($nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado = "AND ( up.id_usuario  = '$id_usuario_sessao')";
}
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
        <base href="../../../tema/dist/">
        <title>STEP &amp; GrupoEP</title>
        <meta charset="utf-8" />
        <meta name="description" content="Sistema de Tratamento Grupo EP - Iot - Tratamento de Efluentes" />
        <meta name="keywords"
            content="STEP, GrupoEP, EP, Tratamento de Efluentes, iot, Sistema, Controle, Tratamento Inteligente, água, osmose, osmose reversa" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="article" />
        <meta property="og:title"
            content="STEP, GrupoEP, EP, Tratamento de Efluentes, iot, Sistema, Controle, Tratamento Inteligente, água, osmose, osmose revers" />
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
        <div class="d-flex flex-column flex-root">
            <!--begin::Page-->
            <div class="page d-flex flex-row flex-column-fluid">
                <!--begin::Wrapper-->
                <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                    <!--begin::Header-->
                    <?php include_once '../../header.php'; ?>
                    <!--end::Header-->


                    <!--begin::Toolbar-->
                    <?php include_once '../../toolbar.php'; ?>
                    <!--end::Toolbar-->
                    <!--begin::Container-->
                    <div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
                        <!--begin::Post-->
                        <div class="content flex-row-fluid" id="kt_content">


                        <!--:Início do novo cockpit -->
                        <div class="card card-bordered mb-10 d-none" id="div_draggable_cockpit">
												<!--begin::Card header-->
												<div class="card-header">
													<div class="card-title">
														<h3 class="d-flex text-white fw-bold my-1 fs-4">Cockpit - Gestão Online de Dados em Tempo Real</h3>
													</div>
												</div>
												<!--end::Card header-->

												<!--begin::Card body-->
												<div class="card-body">
													<!--begin::Row-->
													<div class="row row-cols-lg-3 g-10 min-h-200px draggable-zone" tabindex="0" id="div_cockpit">
														<!--begin::Col-->
														
													
														<!--end::Col-->
													</div>
													<!--end::Row-->
												</div>
												<!--end::Card body-->
										</div>
                            <!--:Fim do novo cockpit -->

                            <!--begin::Navbar-->
                            <div class="card mb-6 mb-xl-9">
                                <?php


                                ?>
                                <div class="card-body pt-9 pb-0">
                                    <!--begin::Details-->
                                    <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                                        <!--begin::Image-->
                                        <div
                                            class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4 bg-light-danger">
                                            <span
                                                class="symbol-label fs-1 bg-light-danger text-danger"><?= $brev_nome_projeto; ?></span>

                                        </div>
                                        <!--end::Image-->
                                        <!--begin::Wrapper-->
                                        <div class="flex-grow-1">
                                            <!--begin::Head-->
                                            <div
                                                class="d-flex justify-content-between align-items-start flex-wrap mb-2">

                                                <!--begin::Details-->
                                                <div class="d-flex flex-column">
                                                    <!--begin::Status-->
                                                    <div class="d-flex align-items-center mb-1">
                                                        <a href="javascript:;"
                                                            class="text-gray-800 text-hover-primary fs-2 fw-bold me-3"><?php echo $r_proj['nome_obra']; ?></a>
                                                        <span
                                                            class="badge badge-light-<?= $classe_status; ?> me-auto"><?= $nome_status; ?></span>
                                                    </div>
                                                    <!--end::Status-->
                                                    <!--begin::Description-->
                                                    <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400">#
                                                        <?php echo $r_proj['nome_contato'] ?? 'Contato não Informado.'; ?>
                                                        <?php echo $r_proj['sobrenome_contato'] ?? 'Sobre nome não informado.'; ?>
                                                    </div>
                                                    <!--end::Description-->
                                                </div>
                                                <!--end::Details-->

                                               
                                            </div>
                                            <!--end::Head-->
                                            <!--begin::Info-->
                                            <div class="d-flex flex-wrap justify-content-start">
                                                <!--begin::Stats-->
                                                <div class="d-flex flex-wrap">
                                                    <!--begin::Stat-->
                                                    <div
                                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                        <!--begin::Number-->
                                                        <div class="d-flex align-items-center">
                                                            <div class="fs-4 fw-bold">
                                                                <?php
                                                                $data_cadastro = new DateTime($r_proj['data_cadastro']);
                                                                echo $data_cadastro->format('d/m/Y ');
                                                                ?>

                                                            </div>
                                                        </div>
                                                        <!--end::Number-->
                                                        <!--begin::Label-->
                                                        <div class="fw-semibold fs-6 text-gray-400">Projeto Criado</div>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Stat-->
                                                    <!--begin::Stat-->
                                                    <div
                                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                        <!--begin::Number-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                                                            <span class="svg-icon svg-icon-3 svg-icon-danger me-2">
                                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <rect opacity="0.5" x="11" y="18" width="13"
                                                                        height="2" rx="1" transform="rotate(-90 11 18)"
                                                                        fill="currentColor" />
                                                                    <path
                                                                        d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z"
                                                                        fill="currentColor" />
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->

                                                            <?php
                                                            // connsulta tarefas


                                                            ?>

                                                            <div class="fs-4 fw-bold" data-kt-countup="true"
                                                                data-kt-countup-value="0">0</div>
                                                        </div>
                                                        <!--end::Number-->
                                                        <!--begin::Label-->
                                                        <div class="fw-semibold fs-6 text-gray-400">Tarefas em Aberto
                                                        </div>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Stat-->
                                                    <!--begin::Stat-->
                                                    <div
                                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                        <!--begin::Number-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                                            <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <rect opacity="0.5" x="13" y="6" width="13"
                                                                        height="2" rx="1" transform="rotate(90 13 6)"
                                                                        fill="currentColor" />
                                                                    <path
                                                                        d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                                                                        fill="currentColor" />
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->
                                                            <?php
                                                            // connsulta Orçamento


                                                            ?>
                                                            <div class="fs-4 fw-bold" data-kt-countup="true"
                                                                data-kt-countup-value="0,00"
                                                                data-kt-countup-prefix="R$ "> 0,00</div>
                                                        </div>
                                                        <!--end::Number-->
                                                        <!--begin::Label-->
                                                        <div class="fw-semibold fs-6 text-gray-400">Orçamento Utilizado
                                                        </div>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Stat-->
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Users-->
                                                <div class="symbol-group symbol-hover mb-3">

                                                    <?php

                                                    $sql_conta_colab = $conexao->query("SELECT COUNT(DISTINCT r.id_operador) as Total_usuarios,
                                                    u.id, u.nome, u.foto, u.email, u.nivel
                                            FROM rmm r 
                                            INNER JOIN pontos_estacao p ON p.id_ponto = r.id_ponto
                                           
                                            LEFT JOIN usuarios u ON r.id_operador = u.id

                                            WHERE u.status='1' AND p.id_obra='$id_projeto' GROUP BY r.id_operador 
                                                                                       
                                            
                                            ");

                                                    $conta = $sql_conta_colab->rowCount();



                                                    if ($conta > 0) {

                                                        $row = $sql_conta_colab->fetchALL(PDO::FETCH_ASSOC);

                                                        foreach ($row as $r_proj) {

                                                            $Total_usuarios = $r_proj['Total_usuarios'];
                                                            $foto_user = $r_proj['foto'];
                                                            $id_user = $r_proj['id'];
                                                            $nome_user = $r_proj['nome'];


                                                            $brev_nome_user = substr($nome_user, 0, 1);

                                                            if ($id_user % 2 == 0) {
                                                                //echo "Numero Par"; 
                                                                $classe = 'info';
                                                            } else {
                                                                $classe = 'primary';
                                                                //echo "Numero Impar"; }
                                                            }

                                                            if ($foto_user != '') {
                                                                echo '<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="' . $nome_user . '">
                                                                <img alt="Pic" alt="Foto Usuário" src="/foto-perfil/' . $foto_user . '" >
                                                            </div>';
                                                            } else {
                                                                echo '<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="' . $nome_user . '">
                                                                <span class="symbol-label bg-light-' . $classe . ' text-inverse-' . $classe . ' fw-bold">' . $brev_nome_user . '</span>
                                                            </div>';
                                                            }
                                                        } ?>

                                                    <!--end::User-->

                                                    <!--begin::All users-->
                                                    <a href="javascript:;" class="symbol symbol-35px symbol-circle"
                                                        data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                                                        <span
                                                            class="symbol-label bg-dark text-inverse-dark fs-8 fw-bold"
                                                            data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                            title="Ver mais Usuários">+<?= $Total_usuarios; ?></span>
                                                    </a>
                                                    <!--end::All users-->
                                                    <?php } ?>
                                                </div>
                                                <!--end::Users-->
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Wrapper-->


                                        <a href="../../views/projetos/projects.php"
                                            class="btn   btn-active-color-success btn-flex h-40px border-0 fw-bold px-4 px-lg-6 ms-2 ms-lg-3">

                                            <!--begin::Svg Icon | path: C:/wamp64/www/keenthemes/core/html/src/media/icons/duotune/communication/com007.svg-->
                                            <span class="svg-icon svg-icon-muted svg-icon-2hx">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3"
                                                        d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z"
                                                        fill="currentColor"></path>
                                                    <path
                                                        d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <span class='text-dark'> Listar Projetos</span>
                                        </a>
                                    </div>
                                    <!--end::Details-->
                                    <div class="separator"></div>
                                    <!--begin::Nav-->
                                    <ul
                                        class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 "
                                                href="../../views/projetos/view-project.php?id=<?php echo $id_projeto;?>">Overview</a>
                                        </li>
                                        <!--end::Nav item-->
                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6"
                                                href="../../views/projetos/tarefas/tarefas.php?id=<?php echo $id_projeto;?>">Tarefas</a>
                                        </li>
                                        <!--end::Nav item-->

                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 "
                                                href="../../views/projetos/nucleos/nucleos.php?id=<?php echo $id_projeto;?>"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'PLC ';}else{ echo 'Núcleos';}; ?></a>
                                        </li>
                                        <!--end::Nav item-->


                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 active"
                                                href="../../views/projetos/plcodes/plcodes.php?id=<?php echo $id_projeto;?>"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Sensores ';}else{ echo 'Indicadores';}; ?></a>
                                        </li>
                                        <!--end::Nav item-->




                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6"
                                                href="../../views/projetos/usuarios/usuarios.php?id=<?php echo $id_projeto;?>">Usuários</a>
                                        </li>
                                        <!--end::Nav item-->
                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6"
                                                href="../../views/projetos/arquivos/files-project.php?id=<?php echo $id_projeto;?>">Arquivos</a>
                                        </li>
                                        <!--end::Nav item-->
                                        <!--begin::Nav item-
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6" href="../../views/projetos/atividades.php?id=">Atividades</a>
                                        </li>
                                        end::Nav item-->

                                        <!--begin::Configurações
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6" href="../../tema/dist/apps/projetos/settings.html">Configurações</a>
                                        </li>
                                      -end::Configurações-->

                                        <!--begin::ABA Orçamentos -- 
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6" href="../../views/projetos/orcamento.php?id=<?php echo $id_projeto;?>">Orçamentos</a>
                                        </li>
                                        end::ABA Orçamentos-->

                                    </ul>
                                    <!--end::Nav-->
                                </div>
                            </div>
                            <!--end::Navbar-->

                            <!--begin::Container-->
                            <div id="div_plcode" class="d-flex flex-column-fluid align-items-start container-xxl">
                                <!--begin::Post-->
                                <div class="content flex-row-fluid" id="kt_content_plcode">
                                    <!--begin::Form-->
                                    <form id="kt_add_plcode_form"  class="form d-flex flex-column flex-lg-row"
                                        data-kt-redirect="../../views/projetos/plcodes/plcode.php" >
                                        <!--begin::Aside column-->
                                        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                                            <!--begin::Thumbnail settings-->
                                            <div class="card card-flush py-4">
                                                <!--begin::Card header-->
                                                <div class="card-header ">
                                                    <!--begin::Card title-->
                                                    <div class="card-title">
                                                        <h2><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?> de Identificação</h2>
                                                    </div>
                                                    <!--end::Card title-->
                                                </div>
                                                <!--end::Card header-->
                                                <!--begin::Card body-->
                                                <div class="card-body text-center pt-0">
                                                    <!--begin::Image input-->
                                                    <!--begin::Image input placeholder-->
                                                    <style>
                                                    .image-input-placeholder {
                                                        background-image: url('assets/media/svg/files/blank-image.svg');
                                                    }

                                                    [data-theme="dark"] .image-input-placeholder {
                                                        background-image: url('assets/media/svg/files/blank-image-dark.svg');
                                                    }
                                                    </style>
                                                    <!--end::Image input placeholder-->
                                                    
                                                    <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3 "
                                                        data-kt-image-input="true">
                                                        <!--begin::Preview existing avatar-->
                                                        <img class="image-input-wrapper w-150px h-150px" id='imageQRCode'></img>
                                                        <!--end::Preview existing avatar-->
                                                        <!--begin::Label-->

                                                    </div>
                                                    <!--end::Image input-->
                                                    <!--begin::Description-->
                                                    <div class="text-muted fs-7"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?> gerado automáticamente ao Salvar
                                                        os Dados do Ponto de Leitura.</div>
                                                    <!--end::Description-->
                                                </div>
                                                <!--end::Card body-->
                                            </div>
                                            <!--end::Thumbnail settings-->
                                            <!--begin::Status-->
                                            <div class="card card-flush py-4">
                                                <!--begin::Card header-->
                                                <div class="card-header">
                                                    <!--begin::Card title-->
                                                    <div class="card-title">
                                                        <h2>Status</h2>
                                                    </div>
                                                    <!--end::Card title-->
                                                    <!--begin::Card toolbar-->
                                                    <div class="card-toolbar">
                                                        <div class="rounded-circle bg-success w-15px h-15px"
                                                            id="kt_add_plcode_status"></div>
                                                    </div>
                                                    <!--begin::Card toolbar-->
                                                </div>
                                                <!--end::Card header-->
                                                <!--begin::Card body-->
                                                <div class="card-body pt-0">
                                                    <!--begin::Select2-->
                                                    <select class="form-select mb-2" data-control="select2"
                                                        data-hide-search="true" data-placeholder="Selecione uma Opção"
                                                        id="kt_add_plcode_status_select" name='kt_add_plcode_status_select'>
                                                        <option></option>
                                                        <option value="1" selected="selected">Ativo</option>
                                                       <!-- <option value="2">Em Alerta</option>-->
                                                        <option value="3">Inativo</option>

                                                    </select>
                                                    <!--end::Select2-->
                                                    <!--begin::Description-->
                                                    <div class="text-muted fs-7">Defina o Status do <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?>.</div>
                                                    <!--end::Description-->
                                               
                                                </div>
                                                <!--end::Card body-->
                                            </div>
                                            <!--end::Status-->
                                            <!--begin::Category & tags-->
                                            <div class="card card-flush py-4">
                                                <!--begin::Card header-->
                                                <div class="card-header">
                                                    <!--begin::Card title-->
                                                    <div class="card-title">
                                                        <h2>Detalhes</h2>
                                                    </div>
                                                    <!--end::Card title-->
                                                </div>
                                                <!--end::Card header-->
                                                <!--begin::Card body-->
                                                <div class="card-body pt-0">
                                                    <!--begin::Input group-->
                                                    <!--begin::Label-->
                                                    <label class="form-label required "><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'PLC ';}else{ echo 'Núcleo';}; ?> de Operação</label>
                                                    <!--end::Label-->
                                                    <!--begin::Select2-->
                                                    <select class="form-select mb-2" data-control="select2"
                                                        data-placeholder="Selecione uma Opção" data-allow-clear="true" name="nucleo_plcode" id='nucleo_plcode'>
                                                        <option value="">Selecione
                                                                                        </option>
                                                        <?php 
                                                        
                                                        $sql = $conexao->query("SELECT id_estacao,nome_estacao FROM estacoes WHERE id_obra='$id_projeto' AND status_estacao='1'");

                                                        $conta = $sql->rowCount();

                                                        if ($conta > 0) {

                                                            $row = $sql->fetchALL(PDO::FETCH_ASSOC);
    
                                                            foreach ($row as $rn) {

                                                                $id_nucleo = $rn['id_estacao'];
                                                                $nome_nucleo = $rn['nome_estacao'];

                                                    echo "<option value='$id_nucleo'>$nome_nucleo</option>";

                                                            }
                                                        }
                                                        
                                                        ;?>
                                                        <option></option>
                                                       
                                                       
                                                    </select>
                                                    <!--end::Select2-->
                                                    <!--begin::Description-->
                                                    <div class="text-muted fs-7 mb-7">Defina o <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'PLC ';}else{ echo 'Núcleos';}; ?> onde o <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumentos ';}else{ echo 'PlCodes';}; ?> será aplicado.
                                                    </div>
                                                    <!--end::Description-->
                                                   
                                                </div>
                                                <!--end::Card body-->
                                            </div>
                                            <!--end::Category & tags-->
                                            <!--begin::Weekly sales-->
                                            <div class="card card-flush py-4">
                                                <!--begin::Card header-->
                                                <div class="card-header">
                                                    <!--begin::Card title-->
                                                    <div class="card-title">
                                                        <h2>Tarefas Agendadas</h2>
                                                    </div>
                                                    <!--end::Card title-->
                                                </div>
                                                <!--end::Card header-->
                                                <!--begin::Card body-->
                                                <div class="card-body pt-0">
                                                    <span class="text-muted">Não há dados disponíveis.Os dados das
                                                        tarefas vinculadas ao <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?>, serão exibidas quando houverem
                                                        tarefas criadas.</span>
                                                </div>
                                                <!--end::Card body-->
                                            </div>
                                            <!--end::Weekly sales-->
                                            <!--begin::Template settings-->

                                            <!--end::Template settings-->
                                        </div>
                                        <!--end::Aside column-->
                                        <!--begin::Main column-->
                                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                                            <!--begin:::Tabs-->
                                            <ul
                                                class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-n2">
                                                <!--begin:::Tab item-->
                                                <li class="nav-item">
                                                    <a class="nav-link text-active-primary pb-4 active"
                                                        data-bs-toggle="tab" href="#kt_add_plcode_general">Geral</a>
                                                </li>
                                                <!--end:::Tab item-->
                                                <!--begin:::Tab item-->
                                                <li class="nav-item">
                                                    <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab"
                                                        href="#kt_add_plcode_indicadores" id="aba_kt_add_plcode_indicadores"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Sensores ';}else{ echo 'Indicadores';}; ?></a>
                                                </li>
                                                <!--end:::Tab item-->

                                                
                                            </ul>
                                            <!--end:::Tabs-->

                                            <!--begin::Tab content-->
                                            <div class="tab-content">
                                                <!--begin::Tab pane-->
                                                <div class="tab-pane fade show active" id="kt_add_plcode_general"
                                                    role="tab-panel">
                                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                                        <!--begin::General options-->
                                                        <div class="card card-flush py-4">
                                                            <!--begin::Card header-->
                                                            <div class="card-header">
                                                                <div class="card-title">
                                                                    <h2>Nome <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?></h2>
                                                                </div>
                                                            </div>
                                                            <!--end::Card header-->
                                                            <!--begin::Card body-->
                                                            <div class="card-body pt-0">
                                                                <!--begin::Input group-->
                                                                <div class="mb-10 fv-row">
                                                                    <!--begin::Label-->
                                                                    <label class="required form-label">Nome
                                                                    <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?></label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="text" name="plcode_nome" id='plcode_nome'
                                                                        class="form-control mb-2"
                                                                        placeholder="Nome de Identificação" value="" />
                                                                    <!--end::Input-->
                                                                    <!--begin::Description-->
                                                                    <div class="text-muted fs-7">O Nome é obrigatório e
                                                                        recomendamos que seja único.</div>
                                                                    <!--end::Description-->
                                                                </div>
                                                                <!--end::Input group-->

                                                            </div>
                                                            <!--end::Card header-->
                                                        </div>
                                                        <!--end::General options-->
                                                        <!--begin::Media-->
                                                        <div class="card card-flush py-4">
                                                            <!--begin::Card header-->
                                                            <div class="card-header">
                                                                <div class="card-title">
                                                                    <h2>Definições Básicas</h2>
                                                                </div>
                                                            </div>
                                                            <!--end::Card header-->
                                                            <!--begin::Card body-->
                                                            <div class="card-body pt-0">
                                                                <!--begin::Input group-->
                                                                <div class="fv-row mb-2">
                                                                    <!--begin::Label-->
                                                                    <label class="required form-label">Objetivo do
                                                                    <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?></label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="text" name="objetivo_plcode" id='objetivo_plcode'
                                                                        class="form-control mb-2" placeholder="Objetivo"
                                                                        value="" />
                                                                </div>
                                                                <!--end::Input group-->
                                                                <!--begin::Description-->
                                                                <div class="text-muted fs-7 mb-2">Defina a Ação e Resultados
                                                                    Esperados</div>
                                                                <!--end::Description-->


                                                                <div class="row mb-2">
                                                                <!--begin::Input group-->
                                                                <div class="col">
                                                                    <!--begin::Label-->
                                                                    <label class="required form-label">Latitude</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="text" name="latitude_plcode" id='latitude_plcode'
                                                                        class="form-control mb-2" placeholder="Latitude do PLCode"
                                                                        value="" />


                                                                      
                                                                </div>
                                                                <!--end::Input group-->

                                                                <div class="col">
                                                                <label class="required form-label">Longitude</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="text" name="longitude_plcode" id='longitude_plcode'
                                                                        class="form-control mb-2" placeholder="Longitude do PLCode"
                                                                        value="" />
                                                                <!--begin::Description-->
                                                                </div>


                                                                <div class="text-muted fs-7">Defina a Ação e Resultado
                                                                    Esperados</div>
                                                                <!--end::Description-->
                                                                </div>

                                                               
                                                                <!--begin::Input group-->
                                                                <div class='py-4'>
                                                                    <!--begin::Label-->
                                                                    <label class="required form-label">Instrução
                                                                        Operacional</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Editor-->
                                                                    <div id="inclui_instrucao_operacional_plcode"
                                                                        name="instrucao_operacional"
                                                                        class="min-h-200px mb-2"></div>
                                                                    <textarea name="texto_instrucao_operacional_plcode"
                                                                        style="display:none"
                                                                        id="texto_instrucao_operacional_plcode"></textarea>
                                                                    <!--end::Editor-->
                                                                    <!--begin::Description-->
                                                                    <div class="text-muted fs-7">Defina a Instrução
                                                                        Operacional do <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?>.</div>
                                                                    <!--end::Description-->
                                                                </div>
                                                                <!--end::Input group-->

                                                            </div>
                                                            <!--end::Card header-->
                                                        </div>
                                                        <!--end::Media-->


                                                        <!--begin::Pricing-->
                                                        <div class="card card-flush py-4">
                                                            <!--begin::Card header-->
                                                            <div class="card-header">
                                                                <div class="card-title">
                                                                    <h2>Definições Avançadas</h2>
                                                                </div>
                                                            </div>
                                                            <!--end::Card header-->
                                                            <!--begin::Card body-->
                                                            <div class="card-body pt-0">
                                                                <!--begin::Input group-->
                                                                <div class="mb-10 fv-row">
                                                                    <!--begin::Label-->
                                                                    <label class="required form-label"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?>
                                                                        Pai</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <select class="form-select mb-2" name="plcode_anterior" id='id_plcode_anterior'
                                                                        data-control="select2" data-hide-search="true"
                                                                        data-placeholder="Antes, defina o Núcleo (PLC) de Operação">


                                                                        <option></option>
                                                                        
                                                                    </select>
                                                                    <!--end::Input-->
                                                                    <!--begin::Description-->
                                                                    <div class="text-muted fs-7">Defina a Conexão deste
                                                                    <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?></div>
                                                                    <!--end::Description-->
                                                                </div>
                                                                <!--end::Input group-->
                                                                <!--begin::Input group-->
                                                                <div class="fv-row mb-10">
                                                                    <!--begin::Label-->
                                                                    <label class="fs-6 fw-semibold mb-2">Tipo de <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?>
                                                                        <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                                            data-bs-toggle="tooltip"
                                                                            title="Defina o tipo de Atuação Principal"></i></label>
                                                                    <!--End::Label-->
                                                                    <!--begin::Row-->
                                                                    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-1 row-cols-xl-3 g-9"
                                                                        data-kt-buttons="true"
                                                                        data-kt-buttons-target="[data-kt-button='true']">
                                                                        <!--begin::Col-->
                                                                        <div class="col">
                                                                            <!--begin::Option-->
                                                                            <label
                                                                                class="btn btn-outline btn-outline-dashed btn-active-light-primary active d-flex text-start p-6"
                                                                                data-kt-button="true">
                                                                                <!--begin::Radio-->
                                                                                <span
                                                                                    class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                                    <input class="tipo_plcode form-check-input"
                                                                                        type="radio"
                                                                                        name="tipo_plcode" value="1"
                                                                                       />
                                                                                </span>
                                                                                <!--end::Radio-->
                                                                                <!--begin::Info-->
                                                                                <span class="ms-5">
                                                                                    <span
                                                                                        class="fs-4 fw-bold text-gray-800 d-block">Tanque
                                                                                        de Tratamento</span>
                                                                                </span>
                                                                                <!--end::Info-->
                                                                            </label>
                                                                            <!--end::Option-->
                                                                        </div>
                                                                        <!--end::Col-->
                                                                        <!--begin::Col-->
                                                                        <div class="col">
                                                                            <!--begin::Option-->
                                                                            <label
                                                                                class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex text-start p-6"
                                                                                data-kt-button="true">
                                                                                <!--begin::Radio-->
                                                                                <span
                                                                                    class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                                    <input class="tipo_plcode form-check-input"
                                                                                        type="radio"
                                                                                        name="tipo_plcode"
                                                                                        value="2"  />
                                                                                </span>
                                                                                <!--end::Radio-->
                                                                                <!--begin::Info-->
                                                                                <span class="ms-5">
                                                                                    <span
                                                                                        class="fs-4 fw-bold text-gray-800 d-block">Equipamento</span>
                                                                                </span>
                                                                                <!--end::Info-->
                                                                            </label>
                                                                            <!--end::Option-->
                                                                        </div>
                                                                        <!--end::Col-->
                                                                        <!--begin::Col-->
                                                                        <div class="col">
                                                                            <!--begin::Option-->
                                                                            <label
                                                                                class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex text-start p-6"
                                                                                data-kt-button="true">
                                                                                <!--begin::Radio-->
                                                                                <span
                                                                                    class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                                    <input class="tipo_plcode form-check-input"
                                                                                        type="radio"
                                                                                        name="tipo_plcode"
                                                                                        value="3"   />
                                                                                </span>
                                                                                <!--end::Radio-->
                                                                                <!--begin::Info-->
                                                                                <span class="ms-5">
                                                                                    <span
                                                                                        class="fs-4 fw-bold text-gray-800 d-block">Instrumento
                                                                                        de Medição</span>
                                                                                </span>
                                                                                <!--end::Info-->
                                                                            </label>
                                                                            <!--end::Option-->
                                                                        </div>
                                                                        <!--end::Col-->
                                                                    </div>
                                                                    <!--end::Row-->
                                                                </div>
                                                                <!--end::Input group-->
                                                              
                                                              
                                                                <!-- incluir  dinamicamente o formulario de cada tipo de plcode -->

                                                                <div id="plcode_tipo_instrumento" class="row d-none">

                                                                    <div
                                                                        class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit">
                                                                    </div>
                                                                    <h3 class="kt-portlet__head-title">
                                                                        <i class="flaticon2-safe kt-font-brand"></i>
                                                                        <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?> para <small>| Instrumento de Medição</small>
                                                                    </h3>

                                                                    <hr>
                                                                    <div class="row">
                                                                        <!-- Início do Conteudo da Função -->

                                                                        <div class="col-xl-12">

                                                                            <div class="form-group">

                                                                                <label class='required form-label'>Função do Instrumento:</label>
                                                                                <div
                                                                                    class="">
                                                                                    <select name="id_tipo_instrumento"
                                                                                        id="id_tipo_instrumento"
                                                                                        class="form-select mb-2" 
                                                                                        data-size="4" data-control="select2"
                                                                                        >
                                                                                        <option value="">Selecione
                                                                                        </option>
                                                                                        <?php

// executa consulta à tabela
$stmt = $conexao->prepare("SELECT * from tipo_instrumento WHERE status_cadastro='1'");
$stmt->execute();

while ($laco = $stmt->fetch(PDO::FETCH_OBJ)) {

    if (isset($dados->id_tipo_instrumento) == $laco->id_tipo_instrumento) :
        echo "<option value=" . $laco->id_tipo_instrumento . " selected>" . $laco->nome_tipo_instrumento . "</option>";



    else :
        echo "<option value=" . $laco->id_tipo_instrumento . ">" . $laco->nome_tipo_instrumento . "</option> ";

    endif;
}


?>
                                                                                    </select>
                                                                                    
                                                                                  
                                                                                </div>


                                                                                <span class="form-text text-muted">

                                                                                    <span>
                                                                                        Função do Instrumento na
                                                                                        Operação
                                                                                        <i class="la la-question-circle"
                                                                                        data-bs-toggle="tooltip" 
                                                                                            title="Selecione a Função deste Instrumento na Operação"
                                                                                          ></i>
                                                                                    </span>
                                                                                </span>

                                                                            </div>
                                                                        </div>


                                                                        <div class="fv-row mb-10">
                                                                            <div class="form-group">
                                                                                <label class="required form-label">
                                                                                    ID do Instrumento:
                                                                                </label>
                                                                                <input type="text" class="form-control "
                                                                                    placeholder="Identificação Única deste Instrumento"
                                                                                    name="nome_instrumento"
                                                                                    id="nome_instrumento"
                                                                                    >
                                                                                <span class="form-text text-muted">Nº de
                                                                                    Série ou Código de
                                                                                    Identificação</span>
                                                                            </div>
                                                                        </div>



                                                                        <div class="fv-row mb-10">

                                                                            <div class="form-group">
                                                                                <label class="required form-label">Capacidade:</label>
                                                                                <input type="text" class="form-control"
                                                                                    placeholder="Capacidade"
                                                                                    name="capacidade_instrumento"
                                                                                   >
                                                                                <span class="form-text text-muted">
                                                                                    Capacidade
                                                                                    <span>

                                                                                        <i class="la la-question-circle"
                                                                                        data-bs-toggle="tooltip" 
                                                                                       
                                                                                    data-bs-html="true"
                                                                                            title="Capacidade Máxima de Operação <code> Capacidade de Placas</code> <code> Pressão Total</code> <code> Faixa de PH</code>"></i>
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        </div>




                                                                        <div class="col-xl-12">
                                                                            <div class="form-group">
                                                                                <label class="required form-label">Instruções de Uso:</label>
                                                                                <textarea class="form-control"
                                                                                    placeholder="Características"
                                                                                    name="carac_instrumento"
                                                                                    rows="5"></textarea>
                                                                                <span class="form-text text-muted">

                                                                                    <span>
                                                                                        Forma de Uso
                                                                                        <i class="la la-question-circle"
                                                                                        data-bs-toggle="tooltip" 
                                                                                          title="Se Houver Instruções Técnicas de Uso deste Instrumento, para esta Operação específica, informe:"></i>
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        </div>




                                                                        <div class="fv-row mb-10">
                                                                            <div class="form-group">
                                                                                <label class="required form-label">
                                                                                    Status do Instrumento:
                                                                                </label>
                                                                                <div class="form-check form-check-custom form-check-solid">

                                                                                    <label
                                                                                        class="form-check-label">
                                                                                        <input type="radio" value="3"
                                                                                            name="status_instrumento"
                                                                                            class="form-check-input" >
                                                                                        Em Manutenção
                                                                                        <span></span>
                                                                                    </label>
                                                                                    <label
                                                                                        class="form-check-label">

                                                                                        <input type="radio" value="1"
                                                                                            name="status_instrumento"
                                                                                            checked="checked"
                                                                                            class="form-check-input" >
                                                                                        Em Operação
                                                                                        <span></span>
                                                                                    </label>
                                                                                </div>
                                                                                <span class="form-text text-muted">
                                                                                    Status do Instrumento
                                                                                    <span>
                                                                                        <i class="la la-question-circle"
                                                                                        data-bs-toggle="tooltip" 
                                                                                            title="Define a Disponibilidade do Instrumento no Ponto de Operação."></i>
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        </div>








                                                                        <!-- Final do Conteudo da Função -->
                                                                    </div>

                                                                </div>




                                                                <div id="plcode_tipo_equipamento" class="row d-none">

                                                                    <div
                                                                        class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit">
                                                                    </div>




                                                                    <h3 class="kt-portlet__head-title">
                                                                        <i class="flaticon2-safe kt-font-brand"></i>
                                                                        <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?> para <small>| Equipamento</small>
                                                                    </h3>


                                                                    <hr>


                                                                    <div class="row">
                                                                        <div class="col-xl-12">

                                                                            <div class="form-group">

                                                                                <label class="required form-label">Função do Equipamento:</label>

                                                                                <div
                                                                                    class="">
                                                                                    <select name="id_tipo_equipamento"
                                                                                        id="id_tipo_equipamento"
                                                                                        class="form-select mb-2" data-control="select2"
                                                                                        data-size="4" 
                                                                                       >

                                                                                        <option value="">Selecione
                                                                                        </option>

                                                                                        <?php

// executa consulta à tabela
$stmt = $conexao->prepare("SELECT * from tipo_equipamento WHERE status_cadastro='1'");
$stmt->execute();

while ($laco = $stmt->fetch(PDO::FETCH_OBJ)) {

    if (isset($dados->id_tipo_equipamento) == $laco->id_tipo_equipamento) :
        echo "<option value=" . $laco->id_tipo_equipamento . " selected>" . $laco->nome_tipo_equipamento . "</option>";



    else :
        echo "<option value=" . $laco->id_tipo_equipamento . ">" . $laco->nome_tipo_equipamento . "</option> ";

    endif;
}


?>
                                                                                    </select>
                                                                                    
                                                                                 
                                                                                </div>

                                                                                <span class="form-text text-muted">

                                                                                    <span>
                                                                                        Função do Equipamento na
                                                                                        Operação
                                                                                        <i class="la la-question-circle"
                                                                                            data-bs-toggle="tooltip"
                                                                                            title="Selecione a Função deste Equipamento na Operação"></i>
                                                                                    </span>
                                                                                </span>

                                                                            </div>



                                                                        </div>





                                                                        <div class="fv-row mb-10">
                                                                            <div class="form-group">
                                                                                <label class="required form-label">
                                                                                    ID do Equipamento:
                                                                                </label>
                                                                                <input type="text" class="form-control "
                                                                                    placeholder="Identificação Única deste Equipamento"
                                                                                    name="nome_equipamento"
                                                                                    id="nome_equipamento"
                                                                                   >
                                                                                <span class="form-text text-muted">Nº de
                                                                                    Série ou Código de
                                                                                    Identificação</span>
                                                                            </div>
                                                                        </div>



                                                                        <div class="fv-row mb-10">

                                                                            <div class="form-group">
                                                                                <label class="required form-label">Capacidade:</label>
                                                                                <input type="text" class="form-control"
                                                                                    placeholder="Capacidade"
                                                                                    name="capacidade_equipamento"
                                                                                   >
                                                                                <span class="form-text text-muted">

                                                                                    <span>
                                                                                        Capacidade
                                                                                        <i class="la la-question-circle"
                                                                                        data-bs-toggle="tooltip"
                                                                                    data-bs-html="true"
                                                                                    
                                                                                    title="Capacidade Máxima de Operação <code> Capacidade de Placas</code> <code> Pressão Total</code> <code> Faixa de PH</code>"></i>
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        </div>




                                                                        <div class="col-xl-12">
                                                                            <div class="form-group">
                                                                                <label class="required form-label">Instruções de Uso:</label>
                                                                                <textarea class="form-control"
                                                                                    placeholder="Características"
                                                                                    name="carac_equipamento"
                                                                                    rows="5"></textarea>
                                                                                <span class="form-text text-muted">

                                                                                    <span>
                                                                                        Forma de Uso
                                                                                        <i class="la la-question-circle"
                                                                                        data-bs-toggle="tooltip"   
                                                                                        title="Se Houver Instruções Técnicas de Uso deste Equipamento, para esta Operação específica, informe:"></i>
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>



                                                                    <div class="fv-row mb-10">
                                                                        <div class="form-group">
                                                                            <label class="required form-label">
                                                                                Status do Equipamento:
                                                                            </label>
                                                                            <div class="form-check form-check-custom form-check-solid">

                                                                                <label
                                                                                    class="form-check-label">
                                                                                    <input type="radio" value="3"
                                                                                        name="status_equipamento"
                                                                                        class="form-check-input"
                                                                                     >
                                                                                    Em Manutenção
                                                                                    <span></span>
                                                                                </label>
                                                                                <label
                                                                                    class="form-check-label">

                                                                                    <input type="radio" value="1"
                                                                                    class="form-check-input"
                                                                                        name="status_equipamento"
                                                                                        checked="checked"
                                                                                        >
                                                                                    Em Operação
                                                                                    <span></span>
                                                                                </label>
                                                                            </div>
                                                                            <span class="form-text text-muted">
                                                                                Status do Equipamento
                                                                                <span>
                                                                                    <i class="la la-question-circle"
                                                                                    data-bs-toggle="tooltip"   
                                                                                    title="Define a Disponibilidade do Equipamento no Ponto de Operação."></i>
                                                                                </span>
                                                                            </span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="fv-row mb-10">
                                                                        <div class="form-group">
                                                                            <label class="required form-label">
                                                                                Posição Inicial:
                                                                            </label>



                                                                        <!--begin::Switch-->
    <label class="form-check form-switch form-check-custom form-check-solid">
        <input class="form-check-input" type="checkbox" value="1" checked="checked" name="posicao_inicial" />
        <span class="form-check-label fw-semibold text-muted">
            Ligado
        </span>
    </label>
    <!--end::Switch-->



                                                                            <span class="form-text text-muted">

                                                                                <span>
                                                                                    Posição
                                                                                    <i class="la la-question-circle"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-html="true"

                                                                                       title="<b>ON</b>= <code>Ligado ou em Operação</code> <b>OFF</b>= <code>Desligado ou Fechado</code>"></i>
                                                                                </span>
                                                                            </span>
                                                                        </div>

                                                                    </div>







                                                                </div>




                                                                <div id="plcode_tipo_tanque" class="row d-none">

                                                                    <div
                                                                        class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit">
                                                                    </div>




                                                                    <h3 class="kt-portlet__head-title">
                                                                        <i class="flaticon2-safe kt-font-brand"></i>
                                                                        <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?> para <small>| Tanque de Tratamento</small>
                                                                    </h3>


                                                                    <hr>

                                                               

                                                                    <div class="fv-row mb-10">
                                                                        <div class="form-group">
                                                                            <label class='required form-label'>Função do Tanque:</label>


                                                                            <div
                                                                                class="">
                                                                                <select name="tipo_tanque"
                                                                                    id="id_tipo_tanque" data-control="select2" 
                                                                                    class="form-select mb-2 "
                                                                                    data-size="4" 
                                                                                    >
                                                                                    <option value="">Selecione</option>

                                                                                    <?php

// executa consulta à tabela
$stmt = $conexao->prepare("SELECT * from tipo_tanque WHERE status_cadastro='1'");
$stmt->execute();

while ($laco = $stmt->fetch(PDO::FETCH_OBJ)) {

    if (isset($dados->id_tipo_tanque) == $laco->id_tipo_tanque) :
        echo "<option value=" . $laco->id_tipo_tanque . " selected>" . $laco->nome_tipo_tanque . "</option>";



    else :
        echo "<option value=" . $laco->id_tipo_tanque . ">" . $laco->nome_tipo_tanque . "</option> ";

    endif;
}


?>
                                                                                </select>
                                                                                
                                                                              
                                                                            </div>

                                                                            <span class="form-text text-muted"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento ';}else{ echo 'PlCode';}; ?> para
                                                                                Tanque na Operação</span>

                                                                        </div>


                                                                    </div>


                                                                    <div class="fv-row mb-10">
                                                                        <div class="form-group">
                                                                            <label class='required form-label'>
                                                                                Volume Total:
                                                                            </label>
                                                                            <input type="text" class="form-control "
                                                                                placeholder="Volume m3"
                                                                                name="volume_tanque" id="volume_tanque"
                                                                                
                                                                                im-insert="true">
                                                                            <span class="form-text text-muted">Volume
                                                                                total em m3</span>
                                                                        </div>
                                                                    </div>







                                                                    <div class="fv-row mb-10">
                                                                        <div class="form-group">
                                                                            <label class='required form-label'>Linha de Entrada:</label>
                                                                            <input type="text" class="form-control"
                                                                                placeholder="Linha A-01"
                                                                                name="linha_entrada"
                                                                                >
                                                                            <span class="form-text text-muted">

                                                                                <span>
                                                                                    Linha de Entrada
                                                                                    <i class="la la-question-circle"
                                                                                    data-bs-toggle="tooltip" 
                                                                                     title="Identificação da Rede de Entrada do Tanque"></i>
                                                                                </span>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="fv-row mb-10">
                                                                        <div class="form-group">
                                                                            <label class='required form-label'>Linha de Saída:</label>
                                                                            <input type="text" class="form-control"
                                                                                placeholder="Linha A-01"
                                                                                name="linha_saida"
                                                                                wtx-context="811C8B94-A0FD-4F87-8FD4-3549C7C6CD2F">
                                                                            <span class="form-text text-muted">

                                                                                <span>
                                                                                    Linha de Saída
                                                                                    <i class="la la-question-circle"
                                                                                    data-bs-toggle="tooltip" 
                                                                                       title="Identificação da Rede de Saída do Tanque."></i>
                                                                                </span>
                                                                            </span>
                                                                        </div>
                                                                    </div>




                                                                    <div class="fv-row mb-10">
                                                                        <div class="form-group">
                                                                            <label class='required form-label'>
                                                                                Status do Tanque:
                                                                            </label>

                                                                            <div class="form-check form-check-custom form-check-solid">

                                                                                <label
                                                                                    class="form-check-label">
                                                                                    <input type="radio" value="3"
                                                                                        name="status_tanque"
                                                                                        class="form-check-input"
                                                                                      >
                                                                                    Em Manutenção
                                                                                    <span></span>
                                                                                </label>
                                                                                <label
                                                                                    class="form-check-label">

                                                                                    <input type="radio" value="1"
                                                                                    class="form-check-input"
                                                                                        name="status_tanque"
                                                                                        checked="checked"
                                                                                       >
                                                                                    Em Operação
                                                                                    <span></span>
                                                                                </label>
                                                                            </div>






                                                                            <span class="form-text text-muted">
                                                                                Status do Tanque
                                                                                <span>
                                                                                    <i class="la la-question-circle"
                                                                                    data-bs-toggle="tooltip" 
                                                                                        title="Define a Disponibilidade do Tanque no Ponto de Operação."></i>
                                                                                </span>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="fv-row mb-10">
                                                                        <div class="form-group">
                                                                            <label class='required form-label'>
                                                                                Controlar Volume Tratado?
                                                                            </label>
                                                                            <div class="col-lg-9 col-md-9 col-sm-12">

 <!--begin::Switch-->
 <label class="form-check form-switch form-check-custom form-check-solid">
        <input class="form-check-input" type="checkbox" value="1" checked="checked" name="controla_volume"/>
        <span class="form-check-label fw-semibold text-muted">
           Controlar
        </span>
    </label>
    <!--end::Switch-->


                                                                            </div>
                                                                            <span class="form-text text-muted">

                                                                                <span>
                                                                                    Posição
                                                                                    <i class="la la-question-circle"
                                                                                    data-bs-toggle="tooltip" data-bs-html="true"
                                                                                    title="<b>ON</b>= <code>Controlar Volume Tratado</code> <b>OFF</b>= <code>Não Controlar Volume Tratado</code>"></i>
                                                                                </span>
                                                                            </span>
                                                                        </div>

                                                                    </div>







                                                                </div>



                                                                <!-- incluir  dinamicamente o formulario de cada tipo de plcode -->
                                                            </div>
                                                            <!--end::Card header-->
                                                        </div>
                                                        <!--end::Pricing-->
                                                    </div>
                                                </div>
                                                <!--end::Tab pane-->
                                                <!--begin::Tab pane-->
                                                <div class="tab-pane fade" id="kt_add_plcode_indicadores"
                                                    role="tab-panel">
                                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                                                            <!--begin::Header-->
                                                            <div class="card-header border-0 pt-5">
                                                                <h3 class="card-title align-items-start flex-column">
                                                                    <span
                                                                        class="card-label fw-bold fs-3 mb-1"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Sensores';}else{ echo 'Indicadores';}; ?>
                                                                        Monitorados</span>
                                                                    <span
                                                                        class="text-muted mt-1 fw-semibold fs-7">Através
                                                                        dos parâmetros de controle</span>
                                                                </h3>
                                                                <div class="card-toolbar" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-trigger="hover"
                                                                    title="Click para incluir um novo Indicador">
                                                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#modal_novo_registro" id="bt_novo_parametro"
                                                                        class="btn btn-sm btn-light btn-active-primary" >
                                                                        <!--end::Svg Icon-->Novo <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Sensor';}else{ echo 'Indicador';}; ?>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <!--end::Header-->
                                                            <!--begin::Body-->
                                                            <div class="card-body py-3">
                                                                <!--begin::Table container-->
                                                                <div class="table-responsive"  id="div_modulo_indicadores">
                                                                    <!--begin::Table-->
                                                                    <div class="alert alert-warning d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-warning me-4">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-warning"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento';}else{ echo 'PlCode';}; ?> não localizado.</h4>
														<span>Finalize o Cadastro do <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento';}else{ echo 'PlCode';}; ?> para poder administar os <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Sensores';}else{ echo 'Indicadores';}; ?> deste <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumento';}else{ echo 'PlCode';}; ?>.</span>
													</div>
												</div>
                                                                    <!--end::Table-->
                                                                </div>
                                                                <!--end::Table container-->
                                                            </div>
                                                            <!--begin::Body-->
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end::Tab pane-->


                                                <!--begin::Tab pane-->
                                                <div class="tab-pane fade" id="kt_add_plcode_iot" role="tab-panel">
                                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                                                            <!--begin::Header-->
                                                            <div class="card-header border-0 pt-5">
                                                                <h3 class="card-title align-items-start flex-column">
                                                                    <span class="card-label fw-bold fs-3 mb-1">IoT's
                                                                        Conectados</span>
                                                                    <span
                                                                        class="text-muted mt-1 fw-semibold fs-7">Centrais
                                                                        IoT instalados neste PLCode</span>
                                                                </h3>
                                                                <div class="card-toolbar" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-trigger="hover"
                                                                    title="Click para incluir um novo IoT">
                                                                    <a href="javascript:;"
                                                                        class="btn btn-sm btn-light btn-active-primary"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#kt_modal_invite_friends">
                                                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                                                        <span class="svg-icon svg-icon-3">
                                                                            <svg width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <rect opacity="0.5" x="11.364"
                                                                                    y="20.364" width="16" height="2"
                                                                                    rx="1"
                                                                                    transform="rotate(-90 11.364 20.364)"
                                                                                    fill="currentColor" />
                                                                                <rect x="4.36396" y="11.364" width="16"
                                                                                    height="2" rx="1"
                                                                                    fill="currentColor" />
                                                                            </svg>
                                                                        </span>
                                                                        <!--end::Svg Icon-->Novo IoT
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <!--end::Header-->
                                                            <!--begin::Body-->
                                                            <div class="card-body py-3">
                                                                <!--begin::Table container-->
                                                                <div class="table-responsive">
                                                                    <!--begin::Table-->
                                                                    <div class="alert alert-warning d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-warning me-4">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-warning">PLCode não localizado.</h4>
														<span>Finalize o Cadastro do PLCode para poder incluir uma nova Central IoT para monitoramento de Sensores IoT's deste PLCode.</span>
													</div>
												</div>
                                                                    <!--end::Table-->
                                                                </div>
                                                                <!--end::Table container-->
                                                            </div>
                                                            <!--begin::Body-->
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end::Tab pane-->


                                                <!--begin::Tab pane-->
                                                <div class="tab-pane fade" id="kt_add_plcode_iot_sensores"
                                                    role="tab-panel">
                                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                                                            <!--begin::Header-->
                                                            <div class="card-header border-0 pt-5">
                                                                <h3 class="card-title align-items-start flex-column">
                                                                    <span class="card-label fw-bold fs-3 mb-1">IoT's -
                                                                        Sensores Conectados</span>
                                                                    <span
                                                                        class="text-muted mt-1 fw-semibold fs-7">Sensores
                                                                        Instalados nas Centrais IoT deste PLCode</span>
                                                                </h3>
                                                                <div class="card-toolbar" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-trigger="hover"
                                                                    title="Click para incluir um novo IoT">
                                                                    <a href="javascript:;"
                                                                        class="btn btn-sm btn-light btn-active-primary"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#kt_modal_invite_friends">
                                                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                                                        <span class="svg-icon svg-icon-3">
                                                                            <svg width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <rect opacity="0.5" x="11.364"
                                                                                    y="20.364" width="16" height="2"
                                                                                    rx="1"
                                                                                    transform="rotate(-90 11.364 20.364)"
                                                                                    fill="currentColor" />
                                                                                <rect x="4.36396" y="11.364" width="16"
                                                                                    height="2" rx="1"
                                                                                    fill="currentColor" />
                                                                            </svg>
                                                                        </span>
                                                                        <!--end::Svg Icon-->Novo Sensor
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <!--end::Header-->
                                                            <!--begin::Body-->
                                                            <div class="card-body py-3">
                                                                <!--begin::Table container-->
                                                                <div class="table-responsive">
                                                                    <!--begin::Table-->
                                                                    <div class="alert alert-warning d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-warning me-4">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-warning">PLCode não localizado.</h4>
														<span>Finalize o Cadastro do PLCode para poder gerenciar os Sensores de Iot Conectados à Central IoT deste PLCode.</span>
													</div>
												</div>
                                                                    <!--end::Table-->
                                                                </div>
                                                                <!--end::Table container-->
                                                            </div>
                                                            <!--begin::Body-->
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end::Tab pane-->
                                            </div>
                                            <!--end::Tab content-->
                                            <div class="d-flex justify-content-end">
                                                <!--begin::Button-->
                                                <a href="../../views/projetos/plcodes/plcodes.php?id=<?=$id_projeto;?>"
                                                    id="kt_add_plcode_cancel" class="btn btn-light me-5">Cancelar</a>
                                                <!--end::Button-->
                                                <!--begin::Button-->
                                                <button type="submit" id="kt_add_plcode_submit" class="btn btn-primary">
                                                    <span class="indicator-label">Salvar Definições Básicas</span>
                                                    <span class="indicator-progress">Por favor, aguarde...
                                                        <span
                                                            class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                </button>
                                                <!--end::Button-->
                                            </div>
                                        </div>
                                        <!--end::Main column-->
                                    <input type="hidden" name="acao" id="acao_cadastro" value="novo_plcode_passo_1">

                                    <input type="hidden" name="id_projeto" value="<?=$id_projeto;?>">

                                    <input type="hidden" name="id_plcode_atual" id="id_plcode_atual" value="">
                                    </form>
                                    <!--end::Form-->
                                </div>
                                <!--end::Post-->
                            </div>
                            <!--end::Container-->


                        </div>
                        <!--end::Post-->
                    </div>
                    <!--end::Container-->
                    <!--begin::Footer-->
                    <?php include '../../../views/footer.php'; ?>
                    <!--end::Footer-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Page-->
        </div>
        <!--end::Root-->
        <!--begin::Drawers-->
    <!--begin::Activities drawer-->
    <?php include './../../../views/conta-usuario/atividade-usuario.php'; ?>
    <!--end::Activities drawer-->
        <!--end::Activities drawer-->
 <!--begin::Chat drawer-->
 <?php include './../../../views/chat/chat-usuario.php'; ?>
   
    
   <!--end::Chat drawer-->
        <!--end::Drawers-->
        <!--end::Main-->


        <!--begin::Scrolltop-->
        <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
            <span class="svg-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)"
                        fill="currentColor" />
                    <path
                        d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                        fill="currentColor" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Scrolltop-->
        <!--begin::Modals-->



        <div class="modal fade" tabindex="-1" id="modal_gps">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Geo Localização do PLCode</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <span class="svg-icon svg-icon-1"></span>
                        </div>
                        <!--end::Close-->
                    </div>

                    <div class="modal-body">
                        <p>mapa</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>

                    </div>
                </div>
            </div>
        </div>


        
        

       
  <!--inicio::Modal - Busca Usuários-->      
  <div class="modal fade" id="kt_modal_users_search" tabindex="-1" aria-hidden="true">
<!--begin::Modal dialog-->
<div class="modal-dialog modal-dialog-centered mw-900px" id='conteudo_modal_dinamico_usuarios'>
    <!--begin::Modal content-->
   <!--begin::Page loading(append to body)-->
        <div class="alert alert-primary d-flex align-items-center p-5 mb-10" id="aguardar_modal_carregar_usuarios">
            <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
            <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
                    <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
                </svg>
            </span>
            <!--end::Svg Icon-->
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-primary">Por favor, aguarde.</h4>
                <span class="spinner-border text-primary" role="status"></span>
                <span class="text-gray-800 fs-6 fw-semibold mt-5">Carregando...</span>
            </div>
        </div>

<!--end::Page loading-->
</div>
<!--end::Modal dialog-->

</div>
<!--end::Modal - Busca Usuários-->

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
        <script src="assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
        <!--end::Vendors Javascript-->
        <!--begin::Custom Javascript(used by this page)-->
        <script src="../../../js/dashboard/step-js.js"></script>
        <script src="../../../node_modules/print-js/dist/print.js"></script>

        <script src="assets/js/widgets.bundle.js"></script>
        <script src="assets/js/custom/widgets.js"></script>
        <script src="../../js/suportes/chat/chat.js"></script>

        <script src="../../../js/tarefas/controladores.js"></script>
        <script src="../../../js/tarefas/nova-tarefa.js"></script>
        <script src="../../../js/plcodes/plcodes.js"></script>
        <script src="../../js/cockpit/create-cockpit.js"></script>
       

        <!--end::Custom Javascript-->
        <!--end::Javascript-->
    </body>
    <!--end::Body-->

    </html>