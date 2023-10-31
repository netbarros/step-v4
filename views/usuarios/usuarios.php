<?php
ob_end_clean();
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$_SESSION['pagina_atual'] = 'Dashboard Usuários';

$projeto_user = '';

if (isset($_GET['user'])) {
    $projeto_user = $_GET['user'];
} elseif (isset($_COOKIE['id_usuario_sessao'])) {
    $projeto_user = $_COOKIE['id_usuario_sessao'];
}


$tipo_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';
$id_usuario_sessao = trim(isset($_SESSION['id'])) ? $_SESSION['id'] : '';

if ($tipo_user_sessao == "" or  $tipo_user_sessao == 'undefined' or $tipo_user_sessao == null) {


    $value = 'Sentimos muito! <br/>O STEP Não Conseguiu Validar seu Nível de Acesso, caso o Erro Persista, por gentileza entre em contato com o Suporte.';
    $_SESSION['seguranca'] =  $value;

    header("Location: ../../views/login/sign-in.php");
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

if($tipo_user_sessao=='supervisor' && $projeto_user ==''){

    header("Location: ../../views/dashboard.php");
}

if($tipo_user_sessao=='cliente' && $projeto_user ==''){

    header("Location: ../../views/dashboard.php");
}


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


$sql_personalizado = '';

if ($tipo_user_sessao == 'supervisor') {

    $sql_personalizado = "WHERE e.supervisor = '$id_BD_Colaborador' OR up.id_usuario = '$projeto_user' ";
}

if ($tipo_user_sessao == 'ro') {

    $sql_personalizado = "WHERE e.ro = '$id_BD_Colaborador' OR up.id_usuario = '$projeto_user'";
}

if ($tipo_user_sessao == 'cliente') {

    $sql_personalizado = "WHERE e.id_cliente= '$id_tabela_cliente_sessao'";
}

if ($tipo_user_sessao == 'admin') {

    $sql_personalizado = "";
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



                            <!--begin::Stats-->
                            <div class="row g-6 g-xl-9">
                                <div class="col-lg-6 col-xxl-4">
                                    <!--begin::Card-->
                                    <div class="card h-100">
                                        <!--begin::Card body-->
                                        <div class="card-body p-9">
                                            <!--begin::Heading-->

                                            <?php

                                                $sql_projeto = $conexao->query("SELECT COUNT(DISTINCT u.id) as Total_Projeto FROM usuarios u
                                                ;
                                                ");
                                            $conta = $sql_projeto->rowCount();



                                            if ($conta > 0) {

                                                $row = $sql_projeto->fetch(PDO::FETCH_ASSOC);

                                                $total = $row['Total_Projeto'];

                                                echo "<div class='fs-2hx fw-bold'>$total</div>";
                                            }
                                            ?>

                                            <div class="fs-4 fw-semibold text-gray-400 mb-7">Grupos de Usuários</div>
                                            <!--end::Heading-->
                                            <!--begin::Wrapper-->
                                            <div class="mt-5">


                                                <!--begin::Item-->
                                                <div class="d-flex flex-stack mb-5">
                                                    <!--begin::Section-->
                                                    <div class="d-flex align-items-center me-2">
                                                        <!--begin::Symbol-->
                                                        <div class="symbol symbol-50px me-3">
                                                            <div class="symbol-label bg-light">
                                                            <i class="fas fa-duotone fa-users fs-2qx text-success"></i>
                                                            </div>
                                                        </div>
                                                        <!--end::Symbol-->
                                                        <!--begin::Title-->
                                                        <div>
                                                            <a href="javascript:;" class="fs-6 text-gray-800 text-hover-primary fw-bold">Contatos</a>
                                                            <div class="fs-7 text-muted fw-semibold mt-1">Clientes em Contato</div>
                                                        </div>
                                                        <!--end::Title-->
                                                    </div>
                                                    <!--end::Section-->
                                                    <?php

$sql_projeto = $conexao->query("SELECT COUNT(DISTINCT u.id) as Total_Contatos FROM usuarios u
WHERE bd_nome='contatos'


");
                                                    $conta = $sql_projeto->rowCount();


                                                    if ($conta > 0) {

                                                        $row = $sql_projeto->fetch(PDO::FETCH_ASSOC);
                                                        $total = $row['Total_Contatos'];


                                                        echo "<div class='badge badge-light fw-semibold py-4 px-3'>$total</div>";
                                                    }
                                                    ?>
                                                    <!--begin::Label-->

                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Item-->

                                                <!--begin::Item-->
                                                <div class="d-flex flex-stack mb-5">
                                                    <!--begin::Section-->
                                                    <div class="d-flex align-items-center me-2">
                                                        <!--begin::Symbol-->
                                                        <div class="symbol symbol-50px me-3">
                                                            <div class="symbol-label bg-light">
                                                            <i class="fas fa-duotone fa-users fs-2qx text-primary"></i>
                                                            </div>
                                                        </div>
                                                        <!--end::Symbol-->
                                                        <!--begin::Title-->
                                                        <div>
                                                            <a href="javascript:;" class="fs-6 text-gray-800 text-hover-primary fw-bold">RO's
                                                            </a>
                                                            <div class="fs-7 text-muted fw-semibold mt-1">Responsáveis por Obras</div>
                                                        </div>
                                                        <!--end::Title-->
                                                    </div>
                                                    <!--end::Section-->
                                                    <?php
                                 
                                        $sql_projeto3 = $conexao->query("SELECT COUNT(DISTINCT u.id) as Total_RO FROM usuarios u
                                        WHERE  nivel='ro' AND(bd_nome='colaboradores' OR bd_nome='contatos')");



                                                    $conta = $sql_projeto3->rowCount();


                                                    if ($conta > 0) {

                                                        $row = $sql_projeto3->fetch(PDO::FETCH_ASSOC);
                                                        $total = $row['Total_RO'];

                                                        echo "<div class='badge badge-light fw-semibold py-4 px-3'>$total</div>";
                                                    }
                                                    ?>
                                                    <!--begin::Label-->

                                                    <!--end::Label-->
                                                </div>

                                                <!--end::Item-->

                                                <!--begin::Item-->
                                                <div class="d-flex flex-stack mb-5">
                                                    <!--begin::Section-->
                                                    <div class="d-flex align-items-center me-2">
                                                        <!--begin::Symbol-->
                                                        <div class="symbol symbol-50px me-3">
                                                            <div class="symbol-label bg-light">
                                                            <i class="fas fa-duotone fa-users fs-2qx text-info"></i>
                                                            </div>
                                                        </div>
                                                        <!--end::Symbol-->
                                                        <!--begin::Title-->
                                                        <div>
                                                            <a href="javascript:;" class="fs-6 text-gray-800 text-hover-primary fw-bold">Supervisores</a>
                                                            <div class="fs-7 text-muted fw-semibold mt-1">Supervisores de Operações
                                                            </div>
                                                        </div>
                                                        <!--end::Title-->
                                                    </div>
                                                    <!--end::Section-->

                                                    <?php
                                              $sql_projeto3 = $conexao->query("SELECT COUNT(DISTINCT u.id) as Total_SU FROM usuarios u
                                              WHERE  nivel='supervisor' AND(bd_nome='colaboradores' OR bd_nome='contatos')");
                                                    $conta = $sql_projeto3->rowCount();


                                                    if ($conta > 0) {

                                                        $row = $sql_projeto3->fetch(PDO::FETCH_ASSOC);

                                                        $total = $row['Total_SU'];

                                                        echo "<div class='badge badge-light fw-semibold py-4 px-3'>$total</div>";
                                                    }
                                                    ?>
                                                    <!--begin::Label-->

                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Item-->




                                                <!--end::Item-->

                                                <!--begin::Item-->
                                                <div class="d-flex flex-stack mb-5">
                                                    <!--begin::Section-->
                                                    <div class="d-flex align-items-center me-2">
                                                        <!--begin::Symbol-->
                                                        <div class="symbol symbol-50px me-3">
                                                            <div class="symbol-label bg-light">
                                                            <i class="fas fa-duotone fa-users fs-2qx text-warning"></i>
                                                            </div>
                                                        </div>
                                                        <!--end::Symbol-->
                                                        <!--begin::Title-->
                                                        <div>
                                                            <a href="javascript:;" class="fs-6 text-gray-800 text-hover-primary fw-bold">Operadores</a>
                                                            <div class="fs-7 text-muted fw-semibold mt-1">Operadores de Tratamento
                                                            </div>
                                                        </div>
                                                        <!--end::Title-->
                                                    </div>
                                                    <!--end::Section-->

                                                    <?php
                                                $sql_projeto3 = $conexao->query("SELECT COUNT(DISTINCT u.id) as Total_OP FROM usuarios u
                                                WHERE  nivel='operador' AND(bd_nome='colaboradores' OR bd_nome='contatos')");
        
                                                    $conta = $sql_projeto3->rowCount();


                                                    if ($conta > 0) {

                                                        $row = $sql_projeto3->fetch(PDO::FETCH_ASSOC);

                                                        $total = $row['Total_OP'];

                                                        echo "<div class='badge badge-light fw-semibold py-4 px-3'>$total</div>";
                                                    }
                                                    ?>
                                                    <!--begin::Label-->

                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Item-->







                                            </div>
                                            <!--end::Wrapper-->

                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                </div>

                                <div class="col-lg-6 col-xxl-4">
                                    <!--begin::Budget-->
                                    <div class="card h-100">
                                        <div class="card-body p-9">

                                            <?php

                                $sql_projeto = $conexao->query("SELECT COUNT(DISTINCT s.quem_abriu) as Total_Estacao_Alerta FROM suporte s
                                                                        
                                WHERE s.status_suporte!='4' ");




                                            $conta = $sql_projeto->rowCount();



                                            if ($conta > 0) {

                                                $row = $sql_projeto->fetch(PDO::FETCH_ASSOC);

                                                $total = $row['Total_Estacao_Alerta'];

                                                echo "<div class='fs-2hx fw-bold'>$total</div>";
                                            }
                                            ?>
                                            <div class="fs-4 fw-semibold text-gray-400 mb-7">Usuários com Suporte em Aberto</div>

                                            <!--begin::Wrapper-->
                                            <div class="mt-5 hover-scroll-overlay-y h-200px px-8">

                                                <?php

                                                if ($projeto_user != '') {

                                                    $sql_estacoes = $conexao->query("SELECT u.nome, e.nome_estacao,u.id FROM suporte s
                                                        INNER JOIN usuarios u ON u.id = s.quem_abriu
                                                        INNER JOIN estacoes e ON e.id_estacao = s.estacao
                                                        WHERE s.status_suporte!='4' group by u.id

                                                        ");
                                                } else {

                                                    $sql_estacoes = $conexao->query("SELECT u.nome,e.nome_estacao, u.id FROM suporte s
                                                    INNER JOIN estacoes e ON e.id_estacao = s.estacao
                                                    INNER JOIN usuarios u ON u.id = s.quem_abriu
                                                 
                                                 WHERE s.status_suporte!='4'  group by u.id

");
                                                }





                                                $conta = $sql_estacoes->rowCount();



                                                if ($conta > 0) {

                                                   // echo $conta;

                                                    $row = $sql_estacoes->fetchALL(PDO::FETCH_ASSOC);

                                                    foreach ($row as $r) {
                                                        $nome_usuario = $r['nome'];
                                                        $nome_estacao = $r['nome_estacao'];
                                                        $id_usuario = $r['id'];

                                                        echo '<!--begin::Item-->
                                                <div class="d-flex flex-stack mb-5">
                                                    <!--begin::Section-->
                                                    <div class="d-flex align-items-center me-2">
                                                        <!--begin::Symbol-->
                                                        <div class="symbol symbol-50px me-3">
                                                            <div class="symbol-label bg-light">
                                                                <span class="svg-icon svg-icon-3x svg-icon-danger d-block my-2">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-person-fill-exclamation" viewBox="0 0 16 16">
                                                                <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm-9 8c0 1 1 1 1 1h5.256A4.493 4.493 0 0 1 8 12.5a4.49 4.49 0 0 1 1.544-3.393C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4Z"/>
                                                                <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm-3.5-2a.5.5 0 0 0-.5.5v1.5a.5.5 0 0 0 1 0V11a.5.5 0 0 0-.5-.5Zm0 4a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1Z"/>
                                                              </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <!--end::Symbol-->
                                                        <!--begin::Title-->
                                                        <div>
                                                            <a href="../../views/conta-usuario/overview.php?id=' . $id_usuario . '&nome=' . $nome_usuario . '" class="fs-6 text-gray-800 text-hover-primary fw-bold">' . $nome_usuario . '</a>
                                                            <div class="fs-7 text-muted fw-semibold mt-1">Núcleo de Origem: ' . $nome_estacao . '</div>
                                                        </div>
                                                        <!--end::Title-->
                                                    </div>
                                                    <!--end::Section-->


                                                    <div class="badge badge-light fw-semibold py-4 px-3"></div>
                                                    <!--begin::Label-->

                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Item-->';
                                                    }
                                                }
                                                ?>



                                            </div>
                                            <!--end::Wrapper-->
                                        </div>
                                    </div>
                                    <!--end::Budget-->
                                </div>
                                <div class="col-lg-6 col-xxl-4">
                                    <!--begin::Clients-->
                                    <div class="card h-100">
                                        <div class="card-body p-9">
                                            <!--begin::Heading-->
                                            <?php


                                            if ($projeto_user != '') {


                                                $sql_conta_colab = $conexao->query("SELECT COUNT(DISTINCT r.id_operador) as total_operador
    FROM rmm r 
    INNER JOIN pontos_estacao p ON p.id_ponto = r.id_ponto
    INNER JOIN obras o ON o.id_obra = p.id_obra
    INNER JOIN estacoes e ON e.id_estacao = o.id_obra
    INNER JOIN usuarios u ON u.id = r.id_operador

    
    WHERE u.status='1'
                                               
    
    ");
                                            } else {

                                                $sql_conta_colab = $conexao->query("SELECT COUNT(DISTINCT r.id_operador) as total_operador
    FROM rmm r 
    INNER JOIN pontos_estacao p ON p.id_ponto = r.id_ponto
    INNER JOIN obras o ON o.id_obra = p.id_obra
    INNER JOIN usuarios u ON u.id = r.id_operador
    WHERE u.status='1'                       
    
    ");
                                            }


                                            $conta = $sql_conta_colab->rowCount();


                                            $total_colab = '';
                                            if ($conta > 0) {

                                                $row = $sql_conta_colab->fetch(PDO::FETCH_ASSOC);

                                                $total_colab = $row['total_operador'];
                                                $total_list_colab = $row['total_operador'] - 8;
                                                echo ' <div class="fs-2hx fw-bold">' . $total_colab . '</div>';
                                            }

                                            ?>

                                            <div class="fs-4 fw-semibold text-gray-400 mb-7">Usuários com Leituras</div>
                                            <!--end::Heading-->
                                            <!--begin::Users group-->
                                            <div class="symbol-group symbol-hover mb-9">

                                                <?php

                                                if ($projeto_user != '') {

                                                    $sql_conta_colab = $conexao->query("SELECT u.foto, u.nome, u.id
    FROM rmm r 
    INNER JOIN pontos_estacao p ON p.id_ponto = r.id_ponto
    INNER JOIN obras o ON o.id_obra = p.id_obra
    INNER JOIN estacoes e ON e.id_estacao = o.id_obra
    INNER JOIN usuarios u ON u.id = r.id_operador
    WHERE u.status='1'
                        GROUP BY u.id  ORDER BY u.nome DESC LIMIT 0,8                   
    
    ");
                                                } else {


                                                    $sql_conta_colab = $conexao->query("SELECT u.foto, u.nome, u.id
    FROM rmm r 
    INNER JOIN pontos_estacao p ON p.id_ponto = r.id_ponto
    INNER JOIN obras o ON o.id_obra = p.id_obra
    INNER JOIN usuarios u ON u.id = r.id_operador
    WHERE u.status='1'
                        GROUP BY u.id  ORDER BY u.nome DESC LIMIT 0,8                   
    
    ");
                                                }


                                                $conta = $sql_conta_colab->rowCount();



                                                if ($conta > 0) {

                                                    $row = $sql_conta_colab->fetchALL(PDO::FETCH_ASSOC);

                                                    foreach ($row as $r) {



                                                        $id_user = $r['id'];
                                                        $nome_user = $r['nome'];
                                                        $brev_nome_user = substr($nome_user, 0, 1);
                                                        $foto_user = $r['foto'];


                                                        if ($id_user % 2 == 0) {
                                                            //echo "Numero Par"; 
                                                            $classe = 'light-info';
                                                        } else {
                                                            $classe = 'light-warning';
                                                            //echo "Numero Impar"; }
                                                        }

                                                        $folder = '/foto-perfil/';
                                                        $filename =  $foto_user;
                            
                                                        // remove todos os caracteres não alfanuméricos e substitui por um '_'
                                                        $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
                            
                                                        // verifica se $filename é um nome de arquivo válido
                                                        if ($filename != "" && !preg_match('/\.\./', $filename) && !preg_match('/\//', $filename) && !preg_match('/\\\/', $filename)) {
                                                            $filePath = $folder . $filename;
                            
                                                            if(file_exists($filePath)) {

                                                                $retorno_foto = '<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="' . $nome_user . '">
                                                                <img alt="Foto Usuário" src="' . $filePath . '" />
                                                            </div>';
                            
                                                              
                            
                                                            } else {

                                                                $retorno_foto = '<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="' . $nome_user . '">
                                                                <span class="symbol-label bg-' . $classe . ' text-inverse-' . $classe . ' fw-bold">' . $brev_nome_user . '</span>
                                                            </div>';
                            
                                                               
                            
                                                            }

                                                        } else {
                                                             $retorno_foto = '<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="' . $nome_user . '">
                                                                <span class="symbol-label bg-' . $classe . ' text-inverse-' . $classe . ' fw-bold">' . $brev_nome_user . '</span>
                                                            </div>';
                                                        }
                                                       
                                                        echo $retorno_foto;
                                                    }
                                                }

                                                ?>



                                                <a href="javascript:;" class="symbol symbol-35px symbol-circle" data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                                                    <span class="symbol-label bg-dark text-gray-300 fs-8 fw-bold">+<?= $total_list_colab; ?></span>
                                                </a>
                                            </div>
                                            <!--end::Users group-->
                                            <!--begin::Actions-->
                                            <div class="d-flex">
                                                <a href="javascript:;" class="btn btn-primary btn-sm me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">Todos
                                                    os Operadores</a>

                                            </div>
                                            <!--end::Actions-->
                                        </div>
                                    </div>
                                    <!--end::Clients-->
                                </div>
                            </div>
                            <!--end::Stats-->
                            <!--begin::Toolbar-->
                            <div class="d-flex flex-wrap flex-stack my-5">
                                <!--begin::Heading-->
                                <h2 class="fs-2 fw-semibold my-2">Usuários
                                    <span class="fs-6 text-gray-400 ms-1">por Listagem</span>
                                </h2>
                                <!--end::Heading-->

                            </div>
                            <!--end::Toolbar-->


                            <!--begin::Card-->
                            <div class="card">
                                <!--begin::Card header-->
                                <div class="card-header border-0 pt-6">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <!--begin::Buscar-->
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Buscar Usuário" />
                                        </div>
                                        <!--end::Buscar-->
                                    </div>
                                    <!--begin::Card title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <!--begin::Toolbar-->
                                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                            <!--begin::Filter-->

                                            <!--begin::Menu 1-->
                                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true" id="kt-toolbar-filter">
                                                <!--begin::Header-->
                                                <div class="px-7 py-5">
                                                    <div class="fs-4 text-dark fw-bold">Opções de Filtro</div>
                                                </div>
                                                <!--end::Header-->
                                                <!--begin::Separator-->
                                                <div class="separator border-gray-200"></div>
                                                <!--end::Separator-->
                                                <!--begin::Content-->
                                                <div class="px-7 py-5">
                                                    <!--begin::Input group-->
                                                    <div class="mb-10">
                                                        <!--begin::Label-->
                                                        <label class="form-label fs-5 fw-semibold mb-3">Status do
                                                            Usuário:</label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <select class="form-select form-select-solid fw-bold" data-kt-select2="true" data-placeholder="Selecione uma opção" data-allow-clear="true" data-kt-customer-table-filter="status" data-dropdown-parent="#kt-toolbar-filter">
                                                            <option></option>
                                                            <option value="1">Ativo</option>
                                                            <option value="2">Aguardando Ativação</option>
                                                            <option value="3">Inativo</option>

                                                        </select>
                                                        <!--end::Input-->
                                                    </div>
                                                    <!--end::Input group-->

                                                    <!--begin::Actions-->
                                                    <div class="d-flex justify-content-end">
                                                        <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-customer-table-filter="reset">Limpar</button>
                                                        <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-customer-table-filter="filter">Aplicar</button>
                                                    </div>
                                                    <!--end::Actions-->
                                                </div>
                                                <!--end::Content-->
                                            </div>
                                            <!--end::Menu 1-->
                                            <!--end::Filter-->
                                            <!--begin::Export-->
                                            <button type="button" class="btn btn-light-primary me-3 gera_relatorio" data-titulo='Usuarios' data-id='kt_tabela_usuarios' id='exportar_tabela_usuarios'>
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                                <span class="svg-icon svg-icon-2">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="currentColor" />
                                                        <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="currentColor" />
                                                        <path opacity="0.3" d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->Exportar
                                            </button>
                                            <!--end::Export-->
                                            <!--begin::Add customer-->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_novo_usuario">Novo Usuário</button>
                                            <!--end::Add customer-->
                                        </div>
                                        <!--end::Toolbar-->
                                        <!--begin::Group actions-->
                                        <div class="d-flex justify-content-end align-items-center d-none" data-kt-customer-table-toolbar="selected">
                                            <div class="fw-bold me-5">
                                                <span class="me-2" data-kt-customer-table-select="selected_count"></span>Selecionado
                                            </div>
                                            <button type="button" class="btn btn-danger" data-kt-customer-table-select="delete_selected">Inativar
                                                Selecionado</button>
                                        </div>
                                        <!--end::Group actions-->
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-0">

                                    <!--begin::Table Projetos e Núcleos-->
                                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_tabela_usuarios">
                                        <!--begin::Table head-->
                                        <thead>
                                            <!--begin::Table row-->
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="w-10px pe-2">
                                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_tabela_usuarios .form-check-input" value="1" />
                                                    </div>
                                                </th>
                                                <th class="min-w-100px">Nome</th>
                                               
                                                <th class="min-w-50px">E-mail</th>
                                                <th class="min-w-50px">Nível</th>
                                              
                                               
                                                <th class="min-w-150px">Último Acesso</th>
                                                <th class="min-w-30px">STATUS</th>
                                                <th class="text-end min-w-100px">Ações</th>
                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="fw-semibold text-gray-600">

                                            <?php



                                            $sql_usuarios = "SELECT
*
 
   FROM usuarios u 
  


    ORDER BY u.nome ASC

";

$stm = $conexao->prepare($sql_usuarios);





                                            if ($stm->execute()) {

                                                //$row_user = $sql_usuarios->fetchALL(PDO::FETCH_ASSOC);

                                               
                                                function getStatusUsuario($status) {
                                                    switch ($status) {
                                                        case 1:
                                                            return '<span class="badge badge-exclusive badge-light-success fw-bold fs-9 px-2 py-1 ms-1">Ativo</span>';
                                                            break;
                                                        case 2:
                                                            return '<span class="badge badge-exclusive badge-light-danger fw-bold fs-9 px-2 py-1 ms-1">Inativo</span>';
                                                            break;
                                                        case 3:
                                                            return '<span class="badge badge-exclusive badge-light-warning fw-bold fs-9 px-2 py-1 ms-1">Aguardando Ativação</span>';
                                                            break;
                                                        default:
                                                            return $status;
                                                            break;
                                                    }
                                                }



                                                    while ($r_user = $stm->fetch(PDO::FETCH_ASSOC)) {

                                                    $id_usuario = $r_user['id'];
                                                    $nivel_usuario = $r_user['nivel'];
                                                   

                                                  $nome_usuario = $r_user['nome'];
                                                            
                                                
                                                  $hoje = new DateTime();
                                                  $ultimo_acesso = new DateTime($r_user['ultimo_acesso']);
                                                  
                                                  $intervalo = $ultimo_acesso->diff($hoje);
                                                  
                                                  $intervaloEmDias = (int)$intervalo->format('%a');
                                                  
                                                  $status = '';
                                                  $tooltip = '';
                                                  
                                                  if($intervaloEmDias >= 60){
                                                      $status = '<span class="badge badge-exclusive badge-light-dark fw-bold fs-9 px-2 py-1 ms-1">Acesso Expirado</span>';
                                                      $tooltip = 'O acesso do usuário ultrapassa 60 dias e por este motivo seu acesso está temporariamente bloqueado, só sendo liberado por solicitação via ticket, com descrição do motivo.';
                                                  }else if($intervaloEmDias >= 50 && $intervaloEmDias < 60){
                                                      $status = '<span class="badge badge-exclusive badge-light-warning fw-bold fs-9 px-2 py-1 ms-1">A Expirar</span>';
                                                      $tooltip = 'O acesso do usuário está próximo de vencer em menos de 10, verifique o motivo, solitice um novo acesso logo que possível, para continuar acessando sem problemas.';
                                                  }

                                                 


                                            ?>
                                                    <tr>
                                                        <!--begin::Checkbox-->
                                                        <td>
                                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                                <input class="form-check-input" type="radio" name="id_usuario" id="id_usuario"  for="id_usuario" value="<?= $id_usuario; ?>" />
                                                            </div>
                                                        </td>
                                                        <!--end::Checkbox-->
                                                        <!--begin::PROJETO=-->
                                                        <td>
                                                            <a href="../../views/conta-usuario/overview.php?id=<?= $id_usuario; ?>&nome=<?= $nome_usuario; ?>" class="text-gray-800 text-hover-primary mb-1"><?= $nome_usuario; ?></a>
                                                        </td>
                                                        <!--end::PROJETO=-->
                                                        
                                                        <!--begin::CONTATO=-->
                                                        <td>
                                                            <a href="../../views/conta-usuario/overview.php?id=<?= $id_usuario; ?>" class="text-gray-600 text-hover-primary mb-1"><?= $r_user['email']; ?></a>
                                                        </td>
                                                        <!--end::CONTATO=-->
                                                        <!--begin::RO=-->
                                                        <td>
                                                            <a href="../../views/conta-usuario/overview.php?id=<?= $id_usuario; ?>" class="text-gray-600 text-hover-primary mb-1"><?= $r_user['nivel']; ?></a>
                                                        </td>
                                                        <!--end::RO=-->
                                                        

                                                        
                                                        <!--begin::PLCodes=-->
                                                        <td>
                                                            <a href="../../views/conta-usuario/logs.php?id=<?= $id_usuario; ?>" class="text-gray-600 text-hover-primary mb-1"><?php echo $ultimo_acesso->format('d/m/Y H:i:s'); ?></a>
                                                        </td>
                                                        <!--end::PLCodes=-->
                                                        <!--begin::Status=-->
                                                        <td><?=getStatusUsuario($r_user['status']); ?> <br>
                                                        <span  class="btn-sm btn-light btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $tooltip ?>">
                                                            <?php echo $status ?>
                                                        </span>
                                                        </td>
                                                        <!--end::Status=-->
                                                        <!--begin::Ações=-->
                                                        <td class="text-end">
                                                            <a href="javascript:;" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Ações
                                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                                                <span class="svg-icon svg-icon-5 m-0">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
                                                                    </svg>
                                                                </span>
                                                                <!--end::Svg Icon-->
                                                            </a>
                                                            <!--begin::Menu-->
                                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                                <!--begin::Menu item-->
                                                                <div class="menu-item px-3">
                                                                    <a href="../../views/conta-usuario/overview.php?id=<?= $id_usuario; ?>&nome=<?= $nome_usuario; ?>" class="menu-link px-3">Visualizar</a>
                                                                </div>
                                                                <!--end::Menu item-->
                                                                <div class="menu-item px-3">
                                                                    <a  href="../../views/conta-usuario/settings.php?id=<?= $id_usuario; ?>&nome=<?= $nome_usuario; ?>"  class="menu-link px-3">Editar</a>
                                                                </div>
                                                                <!--begin::Menu item-->
                                                                <div class="menu-item px-3">
                                                                    <a href="javascript:;" class="menu-link px-3 " data-kt-customer-table-filter="delete_row">Inativar</a>
                                                                </div>
                                                                <!--end::Menu item-->
                                                            </div>
                                                            <!--end::Menu-->
                                                        </td>
                                                        <!--end::Ações=-->
                                                    </tr>

                                            <?php    }
                                            } ?>

                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table Projetos e Núcleos-->
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


 <!--begin::Modal - View Users-->
 <?php include_once "../../views/usuarios/modal-view-users.php"; ?>
        <!--end::Modal - View Users-->


        <!--begin::Modal - Create App Cockpit-->
        <?php include_once "./../../views/cockpit/modal-app-cockpit.php"; ?>
        <!--end::Modal - Create App Cockpit-->
        <!--begin::Modal - Users Buscar-->

        <!--end::Modal - Users Buscar-->






        <div class="modal fade" id="kt_modal_novo_usuario" tabindex="-1" aria-hidden="true" role="dialog" data-bs-backdrop="static">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-900px" id='conteudo_modal_novo_usuario'>
                <!--begin::Modal content-->
                <!--begin::Page loading(append to body)-->
                <div class="alert alert-primary d-flex align-items-center p-5 mb-10" id="aguardar_modal_novo_usuario_carregar">
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
        <!--end::Modal - Edita Projeto-->


        <!--end::Modal - Edita Projeto-->
        <!--end::Modals-->

        <!--begin::Javascript-->
        <script>
            var hostUrl = "assets/";
        </script>
        <!--begin::Global Javascript Bundle(used by all pages)-->
        <script src="assets/plugins/global/plugins.bundle.js"></script>
        <script src="assets/js/scripts.bundle.js"></script>
        <!--end::Global Javascript Bundle-->

       
        <!--begin::Custom Javascript(used by this page)-->
        <script src="assets/js/widgets.bundle.js"></script>
        <script src="assets/js/custom/widgets.js"></script>
        <script src="../../js/suportes/chat/chat.js"></script>
    

        <script src="../../js/usuarios/users-search.js"></script>
        <!--end::Custom Javascript-->
        <!--end::Javascript-->


        <!--begin::Custom Javascript(used by this page)-->

        <script src="../../js/dashboard/step-js.js"></script>
        <script src="../../js/tabelas/basic.js"></script>
        <script src="../../js/cockpit/create-cockpit.js"></script>
        <script src="../../node_modules/print-js/dist/print.js"></script>
        <!--end::Custom Javascript-->

        <script src="../../js/projetos/grafico-widget-01.js"></script>

        <script src="assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
        <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>


        <script src="assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js"></script>

        <script src="../../js/usuarios/listagem_usuarios.js"></script>

        <script src="assets/plugins/custom/draggable/draggable.bundle.js"></script>

        <script src="assets/js/custom/documentation/general/draggable/multiple-containers.js"></script>



       
        


<script>
            var kt_modal_novo_usuario = document.getElementById('kt_modal_novo_usuario');


            kt_modal_novo_usuario.addEventListener('shown.bs.modal', function(event) {


                var button = $(event.relatedTarget);

                var recipientId = button.data('id');

                var modal = $(this);

                //modal.find('#minhaId').html(recipientId);


                $.ajax({
                    type: 'POST',
                    url: '../../views/usuarios/modal-novo-usuario.php',
                    dataType: 'html',
                    data: {
                        id: recipientId
                    },
                    beforeSend: function() {
                        $("#aguardar_modal_novo_usuario_carregar").removeClass("d-none");
                    },
                    success: function(retorno) {

                        

                        $("#aguardar_modal_novo_usuario_carregar").addClass("d-none");

                        $("#conteudo_modal_novo_usuario").html(retorno);
                    },
                    error: function() {
                        alert("Falha ao coletar dados !!!");
                    }
                });


                //$("#conteudo_modal_edita_projeto" ).load( "../../views/projetos/modal-edita-projeto.php?id="+recipientId );



            })



            kt_modal_novo_usuario.addEventListener('hidden.bs.modal', function(event) {

                $("#conteudo_modal_novo_usuario" ).load( "/views/usuarios/modal-novo-usuario.php" );

            })
        </script>





    </body>
    <!--end::Body-->

    </html>