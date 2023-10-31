<?php
 // buffer de saída de dados do php]
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$_SESSION['pagina_atual'] = 'Visualizando Projeto';

$projeto_id = isset($_GET['id']) && is_numeric($_GET['id']) 
                ? intval($_GET['id']) 
                : $_COOKIE['projeto_atual'];



$nivel_acesso_user_sessao = $_COOKIE['nivel_acesso_usuario'] ?? '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';
$id_usuario_sessao = $_COOKIE['id_usuario_sessao'] ?? '';

if ($nivel_acesso_user_sessao == "" or  $nivel_acesso_user_sessao == 'undefined' or $projeto_id == '') {


    $value = 'Sentimos muito! <br/>Sentimos muito! <br/>O STEP Não Conseguiu Validar seu Login Ativo na Sessão, Por gentileza, refaça seu Login.';

    setcookie("seguranca", $value, [
    'expires' => time() + 3600,
    'path' => '/',
    'domain' => 'step.eco.br',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);  /* expira em 1 hora */
    header("Location: ../../crud/login/logout.php");
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
LEFT JOIN contatos ct ON ct.id_cliente = o.id_cliente
LEFT JOIN usuarios_projeto up ON up.id_obra = o.id_obra
WHERE o.id_obra='$projeto_id' ");

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

$brev_nome_projeto = substr($r_proj['nome_obra'], 0, 22);



if ($status_cadastro == '1') {

    $nome_status = 'Projeto Ativo';
    $classe_status = 'success';
} elseif ($status_cadastro == '2') {

    $nome_status = 'Projeto em Alerta';
    $classe_status = 'danger';
} else {

    $nome_status = 'Projeto Inativo';
    $classe_status = 'secondary';
}

$sql_personalizado = '';

if ($nivel_acesso_user_sessao == 'supervisor') {

    $sql_personalizado = "WHERE e.supervisor = '$id_BD_Colaborador' OR up.id_usuario ='$id_usuario_sessao'";
}

if ($nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado = "WHERE  up.id_usuario ='$id_usuario_sessao'";
}

if ($nivel_acesso_user_sessao == 'ro') {

    $sql_personalizado = "WHERE e.ro= '$id_BD_Colaborador' OR up.id_usuario  = '$id_usuario_sessao' ";
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

                            <!--begin::Navbar-->
                            <div class="card mb-6 mb-xl-9">
                                <?php


                                ?>
                                <div class="card-body pt-9 pb-0">
                                    <!--begin::Details-->
                                    <?php include_once "../../views/projetos/topo-projeto.php";?>
                                    <!--end::Details-->
                                    <div class="separator"></div>
                                    <!--begin::Nav-->
                                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 active" href="../../views/projetos/view-project.php?id=<?php echo $projeto_id;?>">Overview</a>
                                        </li>
                                        <!--end::Nav item-->
                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6" href="../../views/projetos/tarefas/tarefas.php?id=<?php echo $projeto_id;?>">Tarefas</a>
                                        </li>
                                        <!--end::Nav item-->

                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6" href="../../views/projetos/nucleos/nucleos.php?id=<?php echo $projeto_id;?>"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'PLC';}else{ echo 'Núcleos';}?></a>
                                        </li>
                                        <!--end::Nav item-->


                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6" href="../../views/projetos/plcodes/plcodes.php?id=<?php echo $projeto_id;?>"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumentos';}else{ echo 'PlCodes';}?></a>
                                        </li>
                                        <!--end::Nav item-->

                                      

                                      
                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6" href="../../views/projetos/usuarios/usuarios.php?id=<?php echo $projeto_id;?>">Usuários</a>
                                        </li>
                                        <!--end::Nav item-->
                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6" href="../../views/projetos/arquivos/files-project.php?id=<?php echo $projeto_id;?>">Arquivos</a>
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
                                            <a class="nav-link text-active-primary py-5 me-6" href="../../views/projetos/orcamento.php?id=">Orçamentos</a>
                                        </li>
                                        end::ABA Orçamentos-->

                                    </ul>
                                    <!--end::Nav-->
                                </div>
                            </div>
                            <!--end::Navbar-->
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9">
                                <!--begin::Col-->
                                <div class="col-lg-6">
                                    <!--begin::Summary-->
                                    <div class="card card-flush h-lg-100">
                                        <!--begin::Card header-->
                                        <div class="card-header mt-6">
                                            <!--begin::Card title-->
                                            <div class="card-title flex-column">
                                                <h3 class="fw-bold mb-1">Sumário de Tarefas</h3>
                                               
                                               

                                                <?php
                                                // consultar as tarefas personalizadas
                                                $sql=$conexao->query("SELECT COUNT(DISTINCT pp.id_periodo_ponto) as Total_Tarefa,pt.id_obra
                                                FROM periodo_ponto pp 
                                                INNER JOIN pontos_estacao pt ON pt.id_ponto = pp.id_ponto
                                                WHERE pp.tipo_checkin like '%ponto_parametro%'  
                                                AND pt.id_obra='$projeto_id'
                                                 GROUP BY pp.tipo_checkin");
                                                  
                                                $r=$sql->rowCount();
                                                
                                                
                                                if($r>0){
                                                
                                                    $rtow = $sql->fetch(PDO::FETCH_ASSOC);
                                                
                                                    $total = $rtow['Total_Tarefa'];
                                                
                                                    echo '<div class="fs-6 fw-semibold text-gray-400">'.$total.' Tarefas de Checkin por Leitura</div>';
                                                
                                                } else {
                                                
                                                    echo '<div class="fs-6 fw-semibold text-gray-400">0 Tarefas de Checkin por Leitura</div>';
                                                }
                                                                                                ?>


                                                
                                            </div>
                                            <!--end::Card title-->
                                            <!--begin::Card toolbar-->
                                            <div class="card-toolbar">
                                                <a href="../../views/projetos/tarefas/tarefas.php?id=<?=$projeto_id;?>"  class="btn btn-light btn-sm">Ver Tarefas</a>
                                            </div>
                                            <!--end::Card toolbar-->
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body p-9 pt-5">
                                            <!--begin::Wrapper-->
                                            <div class="d-flex flex-wrap">
                                                <!--begin::Chart-->
                                                <div class="position-relative d-flex flex-center h-175px w-175px me-15 mb-7">
                                                    <div class=" px-2 rounded  position-absolute translate-middle start-50 top-50 d-flex flex-column flex-center bg-light-info symbol-label fs-3  text-info me-2">
                                                    <?php
                                                // consultar as tarefas personalizadas
                                                $sql=$conexao->query("SELECT COUNT(DISTINCT pp.id_periodo_ponto) as Total_Tarefa
                                                FROM periodo_ponto pp 
                                                INNER JOIN pontos_estacao pt ON pt.id_ponto = pp.id_ponto
                                              WHERE pt.id_obra='$projeto_id'
                                                ");
                                                  
                                                $r=$sql->rowCount();


                                                
                                                
                                                
                                                if($r>0){
                                                
                                                    $rtow = $sql->fetch(PDO::FETCH_ASSOC);
                                                
                                                    $total = $rtow['Total_Tarefa'];
                                                
                                                    echo '<span class="fs-2qx fw-bold">'.$total.'</span>';
                                                
                                                } else {
                                                
                                                    echo '<span class="fs-2qx fw-bold">0</span>';
                                                }
                                                                                                ?>
                                                        
                                                        <span class="d-flex fs-8 fw-bold text-gray-600 me-2">Total</span>
                                                    </div>
                                                  

                                                  
                                                </div>
                                                <!--end::Chart-->
                                                <!--begin::Labels-->
                                                <div class="d-flex flex-column justify-content-center flex-row-fluid pe-11 mb-5">
                                                    <!--begin::Label-->
                                                    <div class="d-flex fs-6 fw-semibold align-items-center mb-3">
                                                        <div class="bullet bg-primary me-3"></div>
                                                        <div class="text-gray-400">Checkin por Leitura</div>
                                                        <?php
                                                // consultar as tarefas personalizadas
                                                $sql=$conexao->query("SELECT COUNT(DISTINCT pp.id_periodo_ponto) as Total_Tarefa
                                                FROM periodo_ponto pp 
                                                INNER JOIN pontos_estacao pt ON pt.id_ponto = pp.id_ponto
                                                WHERE pp.tipo_checkin like '%ponto_parametro%'  
                                                AND pt.id_obra='$projeto_id'
                                                 GROUP BY pp.tipo_checkin");
                                                  
                                                $r=$sql->rowCount();
                                                
                                                
                                                if($r>0){
                                                
                                                    $rtow = $sql->fetch(PDO::FETCH_ASSOC);
                                                
                                                    $total = $rtow['Total_Tarefa'];
                                                
                                                    echo '<div class="ms-auto fw-bold text-gray-700">'.$total.'</div>';
                                                
                                                } else {
                                                
                                                    echo '<div class="ms-auto fw-bold text-gray-700">0</div>';
                                                }
                                                                                                ?>
                                                       
                                                    </div>
                                                    <!--end::Label-->
                                                    <!--begin::Label-->
                                                    <div class="d-flex fs-6 fw-semibold align-items-center mb-3">
                                                        <div class="bullet bg-success me-3"></div>
                                                        <div class="text-gray-400">Checkin Presencial</div>
                                                        <?php
                                                // consultar as tarefas personalizadas
                                                $sql=$conexao->query("SELECT COUNT(DISTINCT pp.id_periodo_ponto) as Total_Tarefa
                                                FROM periodo_ponto pp 
                                                INNER JOIN pontos_estacao pt ON pt.id_ponto = pp.id_ponto
                                                WHERE pp.tipo_checkin like '%ponto_plcode%'
                                                AND pt.id_obra='$projeto_id'
                                                 GROUP BY pp.tipo_checkin");
                                                  
                                                $r=$sql->rowCount();
                                                
                                                
                                                if($r>0){
                                                
                                                    $rtow = $sql->fetch(PDO::FETCH_ASSOC);
                                                
                                                    $total = $rtow['Total_Tarefa'];
                                                
                                                    echo '<div class="ms-auto fw-bold text-gray-700">'.$total.'</div>';
                                                
                                                } else {
                                                
                                                    echo '<div class="ms-auto fw-bold text-gray-700">0</div>';
                                                }
                                                                                                ?>
                                                    </div>
                                                    <!--end::Label-->
                                                    <!--begin::Label-->
                                                    <div class="d-flex fs-6 fw-semibold align-items-center mb-3">
                                                        <div class="bullet bg-warning me-3"></div>
                                                        <div class="text-gray-400">Tarefas Agendadas</div>
                                                        <?php
                                                // consultar as tarefas personalizadas
                                                $sql=$conexao->query("SELECT COUNT(DISTINCT pp.id_periodo_ponto) as Total_Tarefa
                                                FROM periodo_ponto pp 
                                                INNER JOIN pontos_estacao pt ON pt.id_ponto = pp.id_ponto
                                                WHERE pp.tipo_checkin like '%tarefa_agendada%'
                                                AND pt.id_obra='$projeto_id'
                                                 GROUP BY pp.tipo_checkin");
                                                  
                                                $r=$sql->rowCount();
                                                
                                                
                                                if($r>0){
                                                
                                                    $rtow = $sql->fetch(PDO::FETCH_ASSOC);
                                                
                                                    $total = $rtow['Total_Tarefa'];
                                                
                                                    echo '<div class="ms-auto fw-bold text-gray-700">'.$total.'</div>';
                                                
                                                } else {
                                                
                                                    echo '<div class="ms-auto fw-bold text-gray-700">0</div>';
                                                }
                                                                                                ?>
                                                    </div>
                                                    <!--end::Label-->
                                                
                                                </div>
                                                <!--end::Labels-->
                                            </div>
                                            <!--end::Wrapper-->
                                            <!--begin::Notice-->
                                            <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                                                <!--begin::Wrapper-->
                                                <div class="d-flex flex-stack flex-grow-1">
                                                    <!--begin::Content-->
                                                    <div class="fw-semibold">
                                                        <div class="fs-6 text-gray-700">
                                                        <a href="javascript:;" class="fw-bold me-1" data-bs-toggle="modal" data-bs-target="#kt_modal_users_search" data-id="<?php echo $projeto_id; ?>">Convide Colaboradores </a>para este Projeto
                                                        </div>
                                                    </div>
                                                    <!--end::Content-->
                                                </div>
                                                <!--end::Wrapper-->
                                            </div>
                                            <!--end::Notice-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Summary-->
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-lg-6">
                                    <!--begin::Graph-->
                                    <div class="card card-flush h-lg-100">
                                        <!--begin::Card header-->
                                        <div class="card-header mt-6">
                                            <!--begin::Card title-->
                                            <div class="card-title flex-column">
                                                <h3 class="fw-bold mb-1">Tarefas por Período</h3>
                                                <!--begin::Labels-->
                                                <div class="fs-6 d-flex text-gray-400 fs-6 fw-semibold">
                                                    <!--begin::Label-->
                                                    <div class="d-flex align-items-center me-6">
                                                        <span class="menu-bullet d-flex align-items-center me-2">
                                                            <span class="bullet bg-primary"></span>
                                                        </span>Leitura
                                                    </div>
                                                    <!--end::Label-->
                                                    <!--begin::Label-->
                                                    <div class="d-flex align-items-center me-6">
                                                        <span class="menu-bullet d-flex align-items-center me-2">
                                                            <span class="bullet bg-success"></span>
                                                        </span>Presencial
                                                    </div>
                                                    <!--end::Label-->

                                                     <!--begin::Label-->
                                                     <div class="d-flex align-items-center">
                                                        <span class="menu-bullet d-flex align-items-center me-2">
                                                            <span class="bullet bg-warning"></span>
                                                        </span>Agendada
                                                    </div>
                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Labels-->
                                            </div>
                                            <!--end::Card title-->
                                          
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body pt-10 pb-0 px-5">

                                        <div id="mensagem-aguarde-kt_widget_tarefas" class='d-none'>
                                                <div class="blockui-message"><span class="spinner-border text-primary"></span> Carregando...</div>
                                            </div>
                                            
                                            <!--begin::Chart-->
                                            <div id="kt_widget_tarefas" class="card-rounded-bottom" style="height: 300px"></div>
                                            <!--end::Chart-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Graph-->
                                </div>
                                <!--end::Col-->

                                <!--begin::Col-->
                                <div class="col-lg-6">
                                    <!--begin::Card-->
                                    <div class="card card-flush h-lg-100">
                                        <!--begin::Card header-->
                                        <div class="card-header mt-6">
                                            <!--begin::Card title-->
                                            <div class="card-title flex-column">
                                                <h3 class="fw-bold mb-1">Arquivos Recentes </h3>
                                                <?php

                                                // conta qtdade arquivos

                                                // total de leituras realizadas
        
                                                $conta_arquivo_projeto = $conexao->query("SELECT COUNT(DISTINCT aq.id_doc ) as Total_Arquivo, size
                                                 FROM arquivos_projeto aq 
                                                 WHERE aq.id_obra='$projeto_id'
                                                
                                                ");
        
                                                $conta = $conta_arquivo_projeto->rowCount();
        
                                                if($conta>0){
        
                                                    $r=$conta_arquivo_projeto->fetch(PDO::FETCH_ASSOC);
        
                                                    $total=$r['Total_Arquivo'];

                                                    $size = $r['size']/1000;
                                                   $total_arquivo =  number_format($total, 0, '', '.');
        
                                                    echo '<div class="fs-6 text-gray-400">Total '.$total_arquivo.' arquivos, '.$size.' MB espaço usado</div>';
        
                                                }else{
        
                                                    echo '<div class="fs-6 text-gray-400">Total 0 arquivos, 0 GB espaço usado</div>';
        
                                                }
        
                                                ?>

                                                
                                                
                                            </div>
                                            <!--end::Card title-->
                                            <!--begin::Card toolbar-->
                                            <div class="card-toolbar">
                                                <?php
                                                echo "<a href='../../views/projetos/arquivos/files-project.php?id=$projeto_id' class='btn btn-bg-light btn-active-color-primary btn-sm'>Ver Todos</a>";
                                                ?>
                                            </div>
                                            <!--end::Card toolbar-->
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body p-9 pt-3">
                                            <!--begin::Files-->
                                            <div class="d-flex flex-column mb-9">

                                                <?php

                                                $sql_doc = $conexao->query("SELECT * FROM arquivos_projeto aq
                                                INNER JOIN obras o On o.id_obra = aq.id_obra
                                                INNER JOIN usuarios u ON u.id = aq.id_usuario
                                                WHERE o.id_obra='$projeto_id'");

$conta_doc = $sql_doc->rowCount();
        
if($conta_doc>0){

    $r_doc=$sql_doc->fetchALL(PDO::FETCH_ASSOC);

    foreach($r_doc as $r){

        $data_cadastro = $r['data_cadastro_doc'];
        $data_hoje = date_create()->format('Y-m-d');


        $data_inicio = new DateTime($data_cadastro);
        $data_fim = new DateTime($data_hoje);
    
        // Resgata diferença entre as datas
        $dateInterval = $data_inicio->diff($data_fim);
        $tempo_de_vida =  $dateInterval->days;

?>

                                                <!--begin::File-->
                                                <div class="d-flex align-items-center mb-5">
                                                    <!--begin::Icon-->
                                                    <div class="symbol symbol-30px me-5">
                                                        <img alt="Icon" src="assets/media/svg/files/folder-document-dark.svg" />
                                                    </div>
                                                    <!--end::Icon-->
                                                    <!--begin::Details-->
                                                    <div class="fw-semibold">
                                                        <a target="_blank" class="fs-6 fw-bold text-dark text-hover-primary" href="/arquivo-projeto/<?=$r['arquivo_doc'];?>"><?=$r['nome_doc'];?></a>
                                                        <div class="text-gray-400"><?=$tempo_de_vida;?> dias atrás, por: 
                                                            <a href="../../views/conta-usuario/overview.php?id=<?=$r['id'];?>"><?=$r['nome'];?></a>
                                                        </div>
                                                    </div>
                                                    <!--end::Details-->
                                                    <!--begin::Menu-->
                                                    <button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-primary btn-active-light-primary ms-auto" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor" />
                                                                    <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3" />
                                                                    <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3" />
                                                                    <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3" />
                                                                </g>
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </button>

                                                    <!--end::Menu-->
                                                </div>
                                                <!--end::File-->

<?php

    }

} else {    echo  '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
    <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
    <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
    <svg width="67" height="67" viewBox="0 0 67 67" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.25" d="M8.375 11.167C8.375 6.54161 12.1246 2.79199 16.75 2.79199H43.9893C46.2105 2.79199 48.3407 3.67436 49.9113 5.24497L56.172 11.5057C57.7426 13.0763 58.625 15.2065 58.625 17.4277V55.8337C58.625 60.459 54.8754 64.2087 50.25 64.2087H16.75C12.1246 64.2087 8.375 60.459 8.375 55.8337V11.167Z" fill="#00A3FF" />
                                                        <path d="M41.875 5.28162C41.875 3.90663 42.9896 2.79199 44.3646 2.79199V2.79199C46.3455 2.79199 48.2452 3.57889 49.6459 4.97957L56.4374 11.7711C57.8381 13.1718 58.625 15.0715 58.625 17.0524V17.0524C58.625 18.4274 57.5104 19.542 56.1354 19.542H44.6667C43.1249 19.542 41.875 18.2921 41.875 16.7503V5.28162Z" fill="#00A3FF" />
                                                        <path d="M32.4311 25.3368C32.1018 25.4731 31.7933 25.675 31.5257 25.9427L23.1507 34.3177C22.0605 35.4079 22.0605 37.1755 23.1507 38.2657C24.2409 39.3559 26.0085 39.3559 27.0987 38.2657L30.708 34.6563V47.4583C30.708 49.0001 31.9579 50.25 33.4997 50.25C35.0415 50.25 36.2913 49.0001 36.2913 47.4583V34.6563L39.9007 38.2657C40.9909 39.3559 42.7585 39.3559 43.8487 38.2657C44.9389 37.1755 44.9389 35.4079 43.8487 34.3177L35.4737 25.9427C34.6511 25.1201 33.443 24.9182 32.4311 25.3368Z" fill="#00A3FF" />
                                                    </svg>
    </span>
    <!--end::Svg Icon-->
    <div class="fw-semibold">
        <h4 class="text-gray-900 fw-bold">Arquivos do Projeto</h4>
        <div class="fs-6 text-gray-700">Não foi localizado nenhum Arquivo para este Projeto.</div>
    </div>
</div>'; } ?>

                                               



                                            </div>
                                            <!--end::Files-->
                                            <!--begin::Notice-->
                                           <!--begin::Form-->
<form class="form" action="#" method="post">
    <!--begin::Input group-->
    <div class="fv-row">
        <!--begin::Dropzone-->
        <div class="dropzone" id="kt_dropzonejs_arquivos_projeto">
            <!--begin::Message-->
            <div class="dz-message needsclick">
                <!--begin::Icon-->
                <i class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>
                <!--end::Icon-->

                <!--begin::Info-->
                <div class="ms-4">
                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">Arraste e Solte os arquivos ou Clique Aqui para enviar.</h3>
                    <span class="fs-7 fw-semibold text-gray-400">Upload máximo de 10 arquivos</span>
                </div>
                <!--end::Info-->
            </div>
        </div>
        <!--end::Dropzone-->
    </div>
    <!--end::Input group-->
</form>
<!--end::Form-->
                                            <!--end::Notice-->
                                        </div>
                                        <!--end::Card body -->
                                    </div>
                                    <!--end::Card-->
                                </div>
                                <!--end::Col-->

                                <!--begin::Col-->
                                <div class="col-lg-6">
                                    <!--begin::Tasks-->
                                    <div class="card card-flush h-lg-100">
                                        <!--begin::Card header-->
                                        <div class="card-header mt-6">
                                            <!--begin::Card title-->
                                            <div class="card-title flex-column">
                                                <h3 class="fw-bold mb-1">Minhas Tarefas</h3>
                                                <?php
// consultar as tarefas personalizadas
$sql=$conexao->query("SELECT COUNT(DISTINCT pp.id_periodo_ponto) as Total_Tarefa
FROM periodo_ponto pp
INNER JOIN estacoes e ON e.id_estacao = pp.id_estacao
INNER JOIN obras o ON o.id_obra = e.id_obra
 WHERE pp.usuario_tarefa='$_SESSION[id]' AND o.id_obra='$projeto_id' GROUP BY pp.usuario_tarefa");
  
$r=$sql->rowCount();


if($r>0){

    $rtow = $sql->fetch(PDO::FETCH_ASSOC);

    $total = $rtow['Total_Tarefa'];

    echo '<div class="fs-6 text-gray-400">'.$total.' Tarefas em backlog</div>';

} else {

    echo '<div class="fs-6 text-gray-400">0 Tarefas em backlog</div>';
}
                                                ?>
                                                
                                            </div>
                                            <!--end::Card title-->
                                            <!--begin::Card toolbar-->
                                            <div class="card-toolbar">
                                                <a href="../../views/projetos/tarefas/tarefas.php?id=<?=$projeto_id;?>" class="btn btn-bg-light btn-active-color-primary btn-sm">Ver Todas</a>
                                            </div>
                                            <!--end::Card toolbar-->
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body d-flex flex-column mb-9 p-9 pt-3">


                                            <?php // listagem das tarefas 
if($projeto_id!=''){
                                            // consultar as tarefas personalizadas
$sql=$conexao->query("SELECT * FROM periodo_ponto pp 
INNER JOIN estacoes e ON e.id_estacao = pp.id_estacao
INNER JOIN obras o ON o.id_obra = e.id_obra
LEFT JOIN pontos_estacao pt On pt.id_ponto = pp.id_ponto
LEFT JOIN parametros_ponto pr On pr.id_parametro = pp.id_parametro
WHERE pp.usuario_tarefa='$_SESSION[id]' AND o.id_obra='$projeto_id' GROUP BY  pp.id_periodo_ponto");

  } else {

    $sql=$conexao->query("SELECT * FROM periodo_ponto pp 
    INNER JOIN estacoes e ON e.id_estacao = pp.id_estacao
    INNER JOIN obras o ON o.id_obra = e.id_obra
    LEFT JOIN pontos_estacao pt On pt.id_ponto = pp.id_ponto
    LEFT JOIN parametros_ponto pr On pr.id_parametro = pp.id_parametro
   WHERE pp.usuario_tarefa='$_SESSION[id]' GROUP BY  pp.id_periodo_ponto");

  }
$r=$sql->rowCount();
if($r>0){

    $rtow = $sql->fetchALL(PDO::FETCH_ASSOC);



    foreach($rtow as $r){

        $titulo_tarefa = $r['titulo_tarefa'] ?? 'Título não Informado - ';

        $ciclo_leitura = $r['ciclo_leitura'];

        $css_tarefa_r = $r['status_periodo'];

        $nome_plcode = $r['nome_ponto'] ?? '';

        $nome_indicador = $r['nome_parametro'] ?? '';

        if($css_tarefa_r=='1'){ // ativo

            $css_tarefa='success';
            $texto_css = 'A Tarefa está Ativa';
        }

        
        if($css_tarefa_r=='3'){ //inativo

            $css_tarefa='warning';
            $texto_css = 'A Tarefa está Inativa';
        }

        if($css_tarefa_r=='0'){ //nao executado

            $css_tarefa='danger';
            $texto_css = 'A Tarefa não foi Executada';
        }

        if($ciclo_leitura=='0'){

            $ciclo="Tarefa Única";

        }

        if($ciclo_leitura=='1'){

            $ciclo="Tarefa Diária";
            
        }

        if($ciclo_leitura=='2'){

            $ciclo="Tarefa Semanal";
        }

        $tipo= $r['tipo_checkin'];

        if($tipo=='ponto_parametro'){

            $tipo_checkin="Checkin Leitura";
            
        }

        $nome='';

        if($tipo=='ponto_plcode'){

            $tipo_checkin="Checkin Presencial";

          

        }

        if($tipo=='tarefa_agendada'){

            $tipo_checkin="Tarefa Agendada";
           
        }


                                            ?>

<div class="d-flex align-items-center mb-8">
												<!--begin::Bullet-->
												<span class="bullet bullet-vertical h-40px bg-secundary"></span>
												<!--end::Bullet-->
												<!--begin::Checkbox-->
												<div class="form-check form-check-custom form-check-solid mx-5">
													<input class="form-check-input" type="checkbox" value="">
												</div>
												<!--end::Checkbox-->
												<!--begin::Description-->
												<div class="flex-grow-1">
													<a href="javascript:;" class="text-gray-800 text-hover-primary fw-bold fs-6"><?=$titulo_tarefa;?> <?=$r['nome_estacao'];?>  </a>
													<span class="text-muted fw-semibold d-block"><span class="badge badge-light-info fs-8 fw-bold"><?=$ciclo;?></span> <?=$nome_plcode;?> <?=$nome_indicador;?></span>
												</div>
												<!--end::Description-->
												<span class="badge badge-light-<?=$css_tarefa;?> fs-8 fw-bold" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-dismiss="click" title="<?=$texto_css;?>" ><?=$tipo_checkin;?></span>
											</div>
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
                                                    <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">Tarefas Agendas</h4>
														<div class="fs-6 text-gray-700">Você não possui nenhuma Tarefa Delegada, até o momento.</div>
													</div>
												</div>';
                                      
} 

?>

                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Tasks-->
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
   
    
   <!--end::Chat drawer-->
        <!--end::Drawers-->
        <!--end::Main-->

        
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



        <!--begin::Modal - View Users-->
        <?php include_once "../../views/usuarios/modal-view-users.php"; ?>
        <!--end::Modal - View Users-->
       
 <!--begin::Modal - Create App Cockpit-->
 <?php include_once "../../views/cockpit/modal-app-cockpit.php"; ?>
        <!--end::Modal - Create App Cockpit-->
        <!--end::Modals-->
        <!--begin::Javascript-->
        <script>
            var hostUrl = "assets/";
        </script>
        <!--begin::Global Javascript Bundle(used by all pages)-->
        <script src="assets/plugins/global/plugins.bundle.js"></script>
        <script src="assets/js/scripts.bundle.js"></script>
      
<!--begin::JavaScript-->
<script>
  function loadScripts() {
    const scripts = [
      'assets/plugins/custom/datatables/datatables.bundle.js',
      "https://code.highcharts.com/highcharts.js",
               "https://code.highcharts.com/highcharts-more.js",
               "https://code.highcharts.com/modules/annotations.js",
                "https://code.highcharts.com/modules/data.js",
                "https://code.highcharts.com/modules/series-label.js",
                "https://code.highcharts.com/modules/exporting.js",
                "https://code.highcharts.com/modules/export-data.js",
                "https://code.highcharts.com/modules/accessibility.js",
      '../../js/dashboard/step-js.js',
      '../../node_modules/print-js/dist/print.js',
      '../../js/projetos/project.js',
      'assets/js/widgets.bundle.js',
      'assets/js/custom/widgets.js',
      '../../js/suportes/chat/chat.js',
      '../../js/usuarios/users-search.js',
      '../../js/cockpit/create-cockpit.js',
      'assets/plugins/custom/draggable/draggable.bundle.js',
            'assets/js/custom/documentation/general/draggable/multiple-containers.js',

    ];

    let loadedScripts = 0;
    scripts.forEach((src) => {
      const script = document.createElement('script');
      script.src = src;
      script.async = false;
      script.onload = () => {
        loadedScripts++;
        if (loadedScripts === scripts.length) {
          $('#overlay').fadeOut();
        }
      };
      document.body.appendChild(script);
    });
  }

  window.addEventListener('DOMContentLoaded', function () {
    $('#overlay').fadeIn();
    loadScripts();
  });
</script>
<!--end::JavaScript-->

<!--       <script>
      
      // Obtém os parâmetros da URL
var params = new URLSearchParams(window.location.search);

// Recupera o valor da variável "nome"
var id_projeto = params.get('id');

//alert(id_projeto);

if(id_projeto!=''){

    KTCookie.set("projeto_atual", id_projeto);

    // Exibe o valor da variável "nome" no console

}

console.log('id do projeto:'+id_projeto);



</script> -->

        <!--end::Javascript-->
    </body>
    <!--end::Body-->

    </html>