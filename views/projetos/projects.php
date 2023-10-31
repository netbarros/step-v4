<?php
 // buffer de saída de dados do php]
// Instancia Conexão PDO
if (!isset($_SESSION)) session_start();	
require_once $_SERVER['DOCUMENT_ROOT'].'/conexao.php';
$conexao = Conexao::getInstance();

require_once $_SERVER['DOCUMENT_ROOT'].'/crud/login/verifica_sessao.php';


$_SESSION['pagina_atual'] = 'Dashboard Projetos';

$id_projeto = $_GET['id_projeto'] ?? '';

$projeto_user = '';

if (isset($_GET['user'])) {
    $projeto_user = $_GET['user'];
} elseif (isset($_COOKIE['id_usuario_sessao'])) {
    $projeto_user = $_COOKIE['id_usuario_sessao'];
}


$nivel_acesso_user_sessao = $_COOKIE['nivel_acesso_usuario'] ?? '';


if($nivel_acesso_user_sessao=='supervisor'){
    $_SESSION['error'] = "Falha ao Acessar a Página de Projetos, seu Nível de acesso não permite!";
    header("Location: /views/dashboard.php");
    exit;
}

if(empty($projeto_user) || empty($nivel_acesso_user_sessao)){
    // Armazenar a mensagem de erro em uma variável de sessão
    $_SESSION['error'] = "Falha ao Acessar a Página de Projetos, através do seu Nível de Acesso e ID de Usuário! <br> Reporte ao Suporte!";

    // Redirecionar para o dashboard
    header("Location: /views/dashboard.php");
    exit;
} 


$sql_personalizado_projeto = '';
$complemento_sql ='';


if($nivel_acesso_user_sessao!='admin'){

    $sql_personalizado_projeto = " AND  up.id_usuario = '$projeto_user' ";
  
} else {
    $sql_personalizado_projeto = "";
}






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
        <title>STEP &amp; GrupoEP </title>
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
        <style>
  .badge:hover {
    border-color: red;
    color: red;
  }
</style>

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
                                    <div class="card h-100 widget_projeto" >
                                        <!--begin::Card body-->
                                        <div class="card-body p-9">
                                            <!--begin::Heading-->

                                            <?php
                                                        $query_projeto1 = $conexao->prepare(" SELECT 
                                                            COUNT(DISTINCT o.id_obra) as Total_Projeto,status_cadastro
                                                        FROM 
                                                            obras o 
                                                            LEFT JOIN estacoes e ON e.id_obra = o.id_obra
                                                            LEFT JOIN usuarios_projeto up ON up.id_obra = o.id_obra
                                                            WHERE 
                                   
                                     o.status_cadastro = '1' AND
                                     e.status_estacao = '1' " . $sql_personalizado_projeto. " ");
      
                                     $query_projeto1->execute();

                                                        
                                                       






                                            $conta = $query_projeto1->rowCount();
//print_r($sql_projeto);


                                            if ($conta > 0) {

                                                $row = $query_projeto1->fetch(PDO::FETCH_ASSOC);

                                                $total = $row['Total_Projeto'];

                                                

                                                echo "<div class='fs-2hx fw-bold'>$total</div>";
                                            }
                                            ?>

                                            <div class="fs-4 fw-semibold text-gray-400 mb-7">Total de Projetos Cadastrados</div>
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
                                                                <span
                                                                    class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path opacity="0.3"
                                                                            d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z"
                                                                            fill="currentColor"></path>
                                                                        <path
                                                                            d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z"
                                                                            fill="currentColor"></path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <!--end::Symbol-->
                                                        <!--begin::Title-->
                                                        <div>
                                                        <a href="javascript:;" data-bs-toggle="tooltip" data-bs-placement="right"  title="Acesse a Listagem Completa dos Projetos Ativos" class="fs-6 text-gray-800 text-hover-primary fw-bold" onclick="document.getElementById('kt_tabela_projetos').scrollIntoView(); return false;">Ativos</a>

                                                          
                                                            <div class="fs-7 text-muted fw-semibold mt-1">Projetos em Acompanhamento</div>
                                                        </div>
                                                        <!--end::Title-->
                                                    </div>
                                                    <!--end::Section-->
                                                    <?php


$query_projeto2 = "SELECT 
    COUNT(DISTINCT o.id_obra) as Total_Projeto
FROM 
    obras o
    LEFT JOIN estacoes e ON e.id_obra = o.id_obra
    INNER JOIN usuarios_projeto up ON up.id_obra = o.id_obra
WHERE 
    o.status_cadastro = '1' " . $sql_personalizado_projeto;

$stmt_projeto = $conexao->prepare($query_projeto2);

$params = [];



if(strpos($sql_personalizado_projeto, ':projeto_user') !== false){
    $params[':projeto_user'] = $projeto_user;
}

$stmt_projeto->execute($params);



                                                    $conta = $stmt_projeto->rowCount();


                                                    if ($conta > 0) {

                                                        $row = $stmt_projeto->fetch(PDO::FETCH_ASSOC);
                                                        $total = $row['Total_Projeto'];

                                                       


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
                                                                <span
                                                                    class="svg-icon svg-icon-3x svg-icon-danger d-block my-2">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path opacity="0.3"
                                                                            d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z"
                                                                            fill="currentColor"></path>
                                                                        <path
                                                                            d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z"
                                                                            fill="currentColor"></path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <!--end::Symbol-->
                                                        <!--begin::Title-->
                                                        <div>
                                                            <a href="javascript:;"
                                                                class="fs-6 text-gray-800 text-hover-primary fw-bold" data-bs-toggle="tooltip" data-bs-placement="right"
                                                                                            title="Acompanhe ao Lado, através dos Projetos & Núcleos com Tickets, não Solucionados." >Em Alerta</a>
                                                            <div class="fs-7 text-muted fw-semibold mt-1">Projetos com Tickets não Solucionados</div>
                                                        </div>
                                                        <!--end::Title-->
                                                    </div>
                                                    <!--end::Section-->
                                                    <?php
                                 $query_projeto3 = $conexao->prepare("
                                 SELECT 
                                     COUNT(DISTINCT o.id_obra) as Total_Estacao_Alerta
                                 FROM 
                                     suporte s
                                     INNER JOIN estacoes e ON e.id_estacao = s.estacao
                                     INNER JOIN obras o ON o.id_obra = e.id_obra
                                     INNER JOIN usuarios_projeto up ON up.id_obra = o.id_obra
                                 WHERE 
                                     s.status_suporte <> '4' AND
                                     o.status_cadastro = '1' AND
                                     e.status_estacao = '1' " . $sql_personalizado_projeto."");
                                     
                                     
                                     $query_projeto3->execute();
                                                        
                               
                                                    $conta = $query_projeto3->rowCount();


                                                    if ($conta > 0) {

                                                        $row = $query_projeto3->fetch(PDO::FETCH_ASSOC);
                                                        $total = $row['Total_Estacao_Alerta'];

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
                                                                <span
                                                                    class="svg-icon svg-icon-3x svg-icon-secundary d-block my-2">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path opacity="0.3"
                                                                            d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z"
                                                                            fill="currentColor"></path>
                                                                        <path
                                                                            d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z"
                                                                            fill="currentColor"></path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <!--end::Symbol-->
                                                        <!--begin::Title-->
                                                        <div>
                                                            <a href="javascript:;"
                                                                class="fs-6 text-gray-800 text-hover-primary fw-bold" data-bs-toggle="tooltip" data-bs-placement="right"
                                                                                            title="Projetos Criados e não mais Monitorados">Inativos</a>
                                                            <div class="fs-7 text-muted fw-semibold mt-1">Projetos
                                                                Inativados no Sistema</div>
                                                        </div>
                                                        <!--end::Title-->
                                                    </div>
                                                    <!--end::Section-->

                                                    <?php
                                                 $query_projeto4 = "SELECT 
                                                 COUNT(DISTINCT o.id_obra) as Total_Projeto
                                             FROM 
                                                 obras o
                                                 LEFT JOIN estacoes e ON e.id_obra = o.id_obra
                                                 INNER JOIN usuarios_projeto up ON up.id_obra = o.id_obra
                                             WHERE 
                                                 o.status_cadastro = '3' " . $sql_personalizado_projeto;
                                             
                                             $stm = $conexao->prepare($query_projeto4);
                                             
                                             $params = [];

                                             if(strpos($sql_personalizado_projeto, ':id_BD_Colaborador') !== false){
                                                 $params[':id_BD_Colaborador'] = $id_BD_Colaborador;
                                             }
                                             
                                             if(strpos($sql_personalizado_projeto, ':projeto_user') !== false){
                                                 $params[':projeto_user'] = $projeto_user;
                                             }
                                             
                                             $stm->execute($params);

                                           
                                             
                                             
                                             $conta = $stm->rowCount();


                                                    if ($conta > 0) {

                                                        $result = $stm->fetchAll(PDO::FETCH_ASSOC);
                                                      

                                                     $total = $result[0]['Total_Projeto'];

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

                                <div class="col-lg-8 col-xxl-6">
                                    <!--begin::Budget-->
                                    <div class="card h-100 widget_projeto" >
                                        <div class="card-body p-9">

                                            <?php

$query_projeto5 = "
    SELECT 
        COUNT(DISTINCT s.estacao) as Total_suporte_Projeto
    FROM 
        suporte s
        INNER JOIN estacoes e ON e.id_estacao = s.estacao
        INNER JOIN obras o ON o.id_obra = e.id_obra
        INNER JOIN usuarios_projeto up ON up.id_obra = o.id_obra
    WHERE 
        s.status_suporte <> '4' AND
        o.status_cadastro = '1' AND
        e.status_estacao = '1' " . $sql_personalizado_projeto;








$stmt_projeto4 = $conexao->prepare($query_projeto5);

$params = [];



if(strpos($sql_personalizado_projeto, ':projeto_user') !== false){
    $params[':projeto_user'] = $projeto_user;
}

$stmt_projeto4->execute($params);

                                            $conta = $stmt_projeto4->rowCount();



                                            if ($conta > 0) {

                                                $row = $stmt_projeto4->fetch(PDO::FETCH_ASSOC);

                                                $total = $row['Total_suporte_Projeto'];

                                                echo "<div class='fs-2hx fw-bold'>$total</div>";
                                            }
                                            ?>
                                            <div class="fs-4 fw-semibold text-gray-400 mb-7">Projetos & Núcleos com Tickets, <span class='fw-bold text-warning'>não  Solucionados.</span></div>

                                            <!--begin::Wrapper-->
                                            <div class="mt-5 hover-scroll-overlay-y h-200px ">
                                                

                                                <?php

$query_tickets_aberto = $conexao->prepare("SELECT 
   s.*, e.nome_estacao, o.nome_obra,
    COUNT(s.id_suporte) as total_suporte,
    CONCAT(
        TIMESTAMPDIFF(DAY, s.data_open, NOW()), ' days ',
        MOD(TIMESTAMPDIFF(HOUR, s.data_open, NOW()), 24), ' hours') as time_open
FROM 
    suporte s
    INNER JOIN estacoes e ON e.id_estacao = s.estacao
    INNER JOIN obras o ON o.id_obra = e.id_obra
    LEFT JOIN usuarios_projeto up ON up.id_obra = o.id_obra

WHERE 
    s.status_suporte <> '4' 
    $sql_personalizado_projeto 
    

GROUP BY s.estacao ");


$query_tickets_aberto->execute();


                                               
                                             $conta = $query_tickets_aberto->rowCount();



                                                if ($conta > 0) {

                                                    $row = $query_tickets_aberto->fetchALL(PDO::FETCH_ASSOC);


                                                   // echo $conta;
                                                   $contador = 1;

                                                   echo '<div class="table-responsive">
                                                   <table class="table table-striped gy-2 gs-2">
                                                       <thead>
                                                       <tr class="fw-semibold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                                       <th class="min-w-50px">ID</th>
                                                       <th class="min-w-100px">Projeto</th>
                                                       <th class="min-w-150px">Núcleo</th>
                                                       <th class="min-w-50px">Tickets</th>
                                                       <th class="min-w-150px">Espera Média</th>
                                                   
                                                      
                                                              
                                                           </tr>
                                                       </thead>
                                                       <tbody>';

                                                    foreach ($row as $r) {

                                                        $nome_projeto =  $r['nome_obra'] ?? 'Não Informado';
                                                        $nome_estacao = $r['nome_estacao'];
                                                        $total_suporte= $r['total_suporte'] ?? '--';


                                                        $time_open = $r['time_open']; // '1210 days 5 hours'
                                                        $time_open_ptbr = str_replace(['days', 'hours'], ['dias', 'horas'], $time_open);
                                                        



                                                        switch ($r['status_suporte']) {
                                                            case '1':
                                                                $nome_status = 'em aberto';
                                                                $css_status='danger';
                                                                break;
                                                    
                                                                case '2':
                                                                    $nome_status = 'em previsão';
                                                                    $css_status='warning';
                                                                    break;
                                                    
                                                                    case '3':
                                                                        $nome_status = 'sem previsão';
                                                                        $css_status='dark';
                                                                        break;
                                                    
                                                                        case '4':
                                                                            $nome_status = 'finalizado';
                                                                            $css_status='success';
                                                                            break;
                                                    
                                                                            case '6':
                                                                                $nome_status = 'indicador revogado';
                                                                                $css_status='info';
                                                                                break;
                                                    
                                                                                case '7':
                                                                                    $nome_status = 'indicador liberado';
                                                                                    $css_status='primary';
                                                                                    break;

                                                                                            default:
                                                                                        $nome_status = 'sem status';
                                                                                        $css_status='light';
                                                                                        break;
                                                                                }

                                                                                echo '
                                                                                        <tr>
                                                                                            <td>
                                                                                                <a href="javascript:;" 
                                                                                                data-id="'.$r['estacao'].'" data-nome_nucleo = "'.$nome_estacao.'"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#kt_modal_detalhe_nucleo"
                                                                                                class="fs-6 text-gray-800 text-hover-primary fw-bold"><span class="badge badge-circle badge-outline badge-primary">'.$contador.'</span>
                                                                                                </a>
                                                                                            </td>
                                                                                            <td>'.$nome_projeto.'</td>

                                                                                            <td> '.$nome_estacao.' </td>

                                                                                            <td style="text-align:center;"> 
                                                                                            <span class="badge badge-light-'.$css_status.'" style="white-space: pre-wrap;">'.$total_suporte.'</span>
                                                                                        </td>
                                                                                        
                                                                                            <td>'.$time_open_ptbr.'</td>
                                                                                           
                                                                                           
                                                                                        </tr>
                                                                                       
                                                                                    </tbody>';
                                                                                

                                                                               
                                                                                

                                                // Incrementa o contador
                                                $contador++;
                                                    }


                                                    echo '</table>
                                                    </div>';

                                                } else {

                                                    echo '<div class="d-flex align-items-center rounded py-5 px-4 bg-light-info">
                                                  
                                                    <!--begin::Description-->
                                                    <div class=" "><span class="fw-bold text-success fs-4 lh-lg"> Parabéns! </span> <p >Não existem Tickets não Solucionados, dentro do(s) Projeto(s) que você participa.</p>
                                                    <p>Caso sinta falta de alguma informação, lembre-se: Você precisa ser incluído em algum Projeto, para ter acesso aos Dados da Operação.</p>
                                                    <p>Você pode acessar o Suporte da Aplicação, através do menu Superior em <span class="text-warning fw-bold fs-5 lh-lg"> HELP -> Suporte ao STEP. </span><br>E solicitar a inclusão de seu usuário em algum Projeto, que sinta falta.</p>
                                                    </div>
                                                    <!--end::Description-->
                                                </div>';


                                                    
                                                }
                                                ?>



                                            </div>
                                            <!--end::Wrapper-->
                                        </div>
                                    </div>
                                    <!--end::Budget-->
                                </div>


                                <div class="col-lg-4 col-xxl-2">
    <!--begin::Clients-->
    <div class="card h-100 widget_projeto">
        <div class="card-body p-9">
            <div class="d-flex flex-column align-items-center justify-content-center">
               
                <div class="d-flex align-items-center justify-content-center fs-4 fw-semibold text-gray-400 mb-7">Usuários com Leituras Registradas</div>

                <!--end::Heading-->

                <!--begin::Users group-->
                <div id="mensagem-aguarde-operadores_ativos_projeto" class='d-none'>
                    <div class="blockui-message"><span class="spinner-border text-primary"></span> Buscando Usuários...
                    </div>
                </div>

                <div class="symbol-group symbol-hover mb-9" id='div_operadores_ativos_projeto'>

                </div>

                <!--end::Users group-->

                <!--begin::Actions-->
                <div class="d-flex">
                    <a href="javascript:;" class="btn btn-primary btn-sm me-3" data-bs-toggle="modal"
                       data-bs-target="#kt_modal_view_users">Ver Todos</a>
                </div>
                <!--end::Actions-->
            </div>
        </div>
    </div>
    <!--end::Clients-->
</div>

                              


                            </div>
                            <!--end::Stats-->
                            <!--begin::Toolbar-->
                            <div class="d-flex flex-wrap flex-stack my-5">
                                <!--begin::Heading-->
                                <h2 class="fs-2 fw-semibold my-2">Projetos
                                    <span class="fs-6 text-gray-400 ms-1">por Listagem</span>
                                </h2>
                                <!--end::Heading-->

                            </div>
                            <!--end::Toolbar-->


                            <!--begin::Card-->
                            <div class="card widget_projeto" >
                                <!--begin::Card header-->
                                <div class="card-header border-0 pt-6">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <!--begin::Buscar-->
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                                        height="2" rx="1" transform="rotate(45 17.0365 15.1223)"
                                                        fill="currentColor" />
                                                    <path
                                                        d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <input type="text" data-kt-customer-table-filter="search"
                                                class="form-control form-control-solid w-250px ps-15"
                                                placeholder="Buscar Projetos" />
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
                                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px"
                                                data-kt-menu="true" id="kt-toolbar-filter">
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
                                                            Projeto:</label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <select class="form-select form-select-solid fw-bold"
                                                            data-kt-select2="true"
                                                            data-placeholder="Selecione uma opção"
                                                            data-allow-clear="true"
                                                            data-kt-customer-table-filter="status"
                                                            data-dropdown-parent="#kt-toolbar-filter">
                                                            <option></option>
                                                            <option value="1">Ativo</option>
                                                            <option value="3">Inativo</option>

                                                        </select>
                                                        <!--end::Input-->
                                                    </div>
                                                    <!--end::Input group-->

                                                    <!--begin::Actions-->
                                                    <div class="d-flex justify-content-end">
                                                        <button type="reset"
                                                            class="btn btn-light btn-active-light-primary me-2"
                                                            data-kt-menu-dismiss="true"
                                                            data-kt-customer-table-filter="reset">Limpar</button>
                                                        <button type="submit" class="btn btn-primary"
                                                            data-kt-menu-dismiss="true"
                                                            data-kt-customer-table-filter="filter">Aplicar</button>
                                                    </div>
                                                    <!--end::Actions-->
                                                </div>
                                                <!--end::Content-->
                                            </div>
                                            <!--end::Menu 1-->
                                            <!--end::Filter-->
                                            <!--begin::Export-->
                                            <button type="button" class="btn btn-light-primary me-3 gera_relatorio"
                                                data-titulo='Projetos' data-id='kt_tabela_projetos'
                                                id='exportar_tabela_projetos'>
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                                <span class="svg-icon svg-icon-2">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2"
                                                            rx="1" transform="rotate(90 12.75 4.25)"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z"
                                                            fill="currentColor" />
                                                        <path opacity="0.3"
                                                            d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->Exportar
                                            </button>
                                            <!--end::Export-->
                                            <!--begin::Add customer-->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_novo_projeto"  >Novo Projeto</button>
                                            <!--end::Add customer-->
                                        </div>
                                        <!--end::Toolbar-->
                                        <!--begin::Group actions-->
                                        <div class="d-flex justify-content-end align-items-center d-none"
                                            data-kt-customer-table-toolbar="selected">
                                            <div class="fw-bold me-5">
                                                <span class="me-2"
                                                    data-kt-customer-table-select="selected_count"></span>Selecionado
                                            </div>
                                            <button type="button" class="btn btn-danger"
                                                data-kt-customer-table-select="delete_selected">Inativar
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
                                    <table class="table align-middle table-row-dashed fs-6 gy-5"
                                        id="kt_tabela_projetos">
                                        <!--begin::Table head-->
                                        <thead>
                                            <!--begin::Table row-->
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="w-10px pe-2">
                                                    <div
                                                        class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            data-kt-check="true"
                                                            data-kt-check-target="#kt_tabela_projetos .form-check-input"
                                                            value="1" />
                                                    </div>
                                                </th>
                                                <th class="min-w-20px">CÓDIGO</th>
                                                <th class="min-w-200px">PROJETO</th>
                                                <th class="min-w-150px">CONTATO PRINCIPAL</th>
                                                <th class="min-w-150px">RO</th>
                                                <th class="min-w-150px">SUPERVISOR</th>
                                                
                                                <th class="text-center min-w-50px">STATUS</th>
                                                <th class="text-center min-w-100px">Ações</th>
                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="fw-semibold text-gray-600">

                                            <?php

                                            $usuario_ativo = $_SESSION['id'];
                                            
    $sql_estacoes = "SELECT 
    o.id_obra,
    o.codigo_obra,
    o.nome_obra,
    o.status_cadastro,

    cli.nome_fantasia,
    c.nome as nome_contato,
    up_ro.id_usuario as id_ro,
    up_su.id_usuario as id_su,
    u_su.id as id_su,
    u_ct.id as id_contato,
    up.id_obra as Projeto_ID,
    up.id_usuario as Usuario_Projeto_ID,
    up.nivel as nivel_projeto,
    up.responsavel as responsavel_projeto,
    u_ro.nome as nome_usuario_ro,
    u_su.nome as nome_usuario_su
	
FROM 
    obras o 
    INNER JOIN clientes cli ON cli.id_cliente = o.id_cliente
    INNER JOIN usuarios_projeto up ON up.id_obra = o.id_obra
    LEFT JOIN contatos c ON c.id_cliente = cli.id_cliente
    LEFT JOIN pontos_estacao p ON p.id_obra = o.id_obra
    LEFT JOIN estacoes e ON e.id_estacao = p.id_estacao
    LEFT JOIN usuarios_projeto up_su ON up_su.id_usuario = e.supervisor
    LEFT JOIN usuarios_projeto up_ro ON up_ro.id_usuario = e.ro
    LEFT JOIN usuarios u_ro ON u_ro.id = up_ro.id_usuario
    LEFT JOIN usuarios u_su ON u_su.id = up_su.id_usuario
    LEFT JOIN usuarios u_ct ON u_ct.id = c.email_corporativo
    
 
    $sql_personalizado_projeto Group BY o.id_obra";

$stm = $conexao->prepare($sql_estacoes);


$params = [];



if(strpos($sql_personalizado_projeto, ':projeto_user') !== false){
    $params[':projeto_user'] = $projeto_user;
}

$stm->execute($params);
  $conta = $stm->rowCount();

                                            if ($conta > 0) {

                                                $row = $stm->fetchAll(PDO::FETCH_ASSOC);

                                            //views/projetos/usuarios/usuarios.php?id=64
                                                foreach ($row as $r) {

                                                    $id_projeto = $r['id_obra'] ?? '';
                                                    $codigo_obra = $r['codigo_obra'] ?? '<span class="badge badge-light-warning">ausente</span>';
                                                    $total_plcodes = '';
                                                    $total_Estacoes = '';

                                                    $nome_cliente = $r['nome_fantasia'] ?? '<span class="badge badge-light-warning">ausente</span>';
                                                    $nome_contato = $r['nome_contato'] ?? '<span class="badge badge-light-warning">ausente</span>';




                                                    $nome_projeto = $r['nome_obra'] ?? '';
                                                    
                                                    $nome_ro = $r['nome_usuario_ro'] ?? '<span class="badge badge-light-warning">ausente</span>';
                                                    $id_ro = $r['id_ro'] ?? '';
                                                    $id_su = $r['id_su'] ?? '';
                                                    $id_contato = $r['id_contato'] ?? '';
                                                    $nome_su = $r['nome_usuario_su'] ?? '<span class="badge badge-light-warning">ausente</span>';

                                                    $Projeto_ID = $r['Projeto_ID'] ??'';

                                                    $responsavel_projeto = $r['responsavel_projeto'] ?? '';
                                                    $nivel_projeto = $r['nivel_projeto'] ?? '';
/* 
                                                    if($nome_projeto==''){

                                                        $sql_projeto = "SELECT *.obras,
                                                         usuarios.nome,
                                                          usuarios_projeto
                                                          uro.nome as nome_usuario_ro,
                                                          uro.id as id_usuario_ro,
                                                           usu.nome as nome_usuario_su,
                                                           usu.id as id_usuario_su,
                                                           up.nivel,
                                                            up.responsavel,
                                                             up.id_usuario as id_usuario_projeto,
                                                              up.id_obra
                                                              
                                                              

                                                           FROM obras o
                                                        INNER JOIN
                                                        usuarios_projeto  up ON up.id_obra = o.id_obra
                                                        LEFT JOIN usuarios uro ON uro.id = up.id_usuario
                                                        LEFT JOIN usuarios usu ON usu.id = up.id_usuario
                                                         WHERE up.id_obra = :id_projeto AND up.nivel = :nivel_projeto AND up.responsavel = :responsavel AND Projeto_ID = :projeto_user";
                                                        $stm_projeto = $conexao->prepare($sql_projeto);
                                                        $stm_projeto->bindValue(':id_projeto', $id_projeto);
                                                        $stm_projeto->bindValue(':nivel_projeto', $nivel_projeto);
                                                        $stm_projeto->bindValue(':responsavel', $responsavel_projeto);
                                                        $stm_projeto ->bindValue(':projeto_user', $Projeto_ID);
                    
                                                        $stm_projeto->execute();
                                                        $row_projeto = $stm_projeto->fetch(PDO::FETCH_ASSOC);

                                                        $nome_projeto = $row_projeto['nome_obra'] ?? '<span class="badge badge-light-warning">ausente</span>';
                                                        $nome_ro = $row_projeto['nome_usuario_ro'] ?? '<span class="badge badge-light-warning">ausente</span>';
                                                        $nome_su = $row_projeto['nome_usuario_su'] ?? '<span class="badge badge-light-warning">ausente</span>';
                                                        $id_ro = $row_projeto['id_usuario_ro'];
                                                         $id_su = $row_projeto['id_usuario_su'];
                                                         $status_projeto = $row_projeto['status_cadastro'];
                                                    } */
                                                        




                                                    $status_projeto = $r['status_cadastro'];




                                                    if($status_projeto=='1'){

                                                        $nome_status = 'ativo';
                                                        $css_status='success';
                                                    }
                                                   
                                                    
                                                    if ($status_projeto=='3'){
                                            
                                                        $nome_status = 'inativo';
                                                        $css_status='dark';
                                            
                                                    }

                                            ?>
                                            <tr>
                                                <!--begin::Checkbox-->
                                                <td>
                                                    <div
                                                        class="form-check form-check-sm form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="radio" name="id_projeto" id="id_projeto"  for="id_projeto"
                                                            value="<?= $id_projeto; ?>" />
                                                    </div>
                                                </td>
                                                <!--end::Checkbox-->
                                                 <!--begin::PROJETO=-->
                                                 <td>
                                                    <a href="../../views/projetos/view-project.php?id=<?= $id_projeto; ?>&projeto=<?=$nome_projeto; ?>"
                                                        class="text-gray-800 text-hover-primary mb-1"><?= $codigo_obra; ?></a>
                                                </td>
                                                <!--end::PROJETO=-->
                                                <!--begin::PROJETO=-->
                                                <td>
                                                    <a href="../../views/projetos/view-project.php?id=<?= $id_projeto; ?>&projeto=<?=$nome_projeto; ?>"
                                                        class="text-gray-800 text-hover-primary mb-1"><?= $nome_projeto; ?></a>
                                                </td>
                                                <!--end::PROJETO=-->
                                               
                                                <!--begin::CONTATO=-->
                                                <td>
                                                    <a href="../../views/conta-usuario/overview.php?id=<?= $id_contato; ?>"
                                                        class="text-gray-600 text-hover-primary mb-1"><?= $nome_contato; ?></a>
                                                </td>
                                                <!--end::CONTATO=-->
                                                <!--begin::RO=-->
                                                <td>
                                                    <a href="/views/projetos/usuarios/usuarios.php?id=<?= $id_projeto; ?>"
                                                        class="text-gray-600 text-hover-primary mb-1"><?= $nome_ro; ?></a>
                                                </td>
                                                <!--end::RO=-->

                                                 <!--begin::SU=-->
                                                 <td>
                                                    <a href="/views/projetos/usuarios/usuarios.php?id=<?= $id_projeto; ?>"
                                                        class="text-gray-600 text-hover-primary mb-1"><?= $nome_su; ?></a>
                                                </td>
                                                <!--end::SU=-->
                                               
                                                <!--begin::Status=-->
                                                <td data-filter="status"><span
                                                        class="badge py-3 px-4 fs-7 badge-light-<?= $css_status; ?>"><?= $nome_status; ?></span>
                                                </td>
                                                <!--end::Status=-->
                                                <!--begin::Ações=-->
                                                <td class="text-end">
                                                    <a href="javascript:;"
                                                        class="btn btn-sm btn-light btn-active-light-primary"
                                                        data-kt-menu-trigger="click"
                                                        data-kt-menu-placement="bottom-end">Ações
                                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                                        <span class="svg-icon svg-icon-5 m-0">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </a>
                                                    <!--begin::Menu-->
                                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                        data-kt-menu="true">
                                                        <!--begin::Menu item-->
                                                        <div class="menu-item px-3">
                                                            <a href="/views/projetos/view-project.php?id=<?=$id_projeto; ?>&projeto=<?=$nome_projeto; ?>"
                                                                class="menu-link px-3">Visualizar</a>
                                                        </div>
                                                        <!--end::Menu item-->
                                                        <div class="menu-item px-3">
    <!-- AQUI ESTA O BOTÃO PARA EDITAR OS PROJETOS -->
    <a href="javascript:;" data-id='<?=$id_projeto; ?>' data-bs-toggle="modal" data-bs-target="#kt_modal_edita_projeto" class="menu-link px-3">Editar</a>
</div>
                                                        <!--begin::Menu item-->
                                                        <div class="menu-item px-3">
                                                            <a href="javascript:;" class="menu-link px-3 "
                                                                data-kt-customer-table-filter="delete_row">Inativar</a>
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

        <!--begin::Modal - View Users-->
        <?php include_once "../../views/usuarios/modal-view-users.php"; ?>
        <!--end::Modal - View Users-->


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


        <!--begin::Modal - Create App Cockpit-->
        <?php include_once "../../views/cockpit/modal-app-cockpit.php"; ?>
        <!--end::Modal - Create App Cockpit-->
        <!--begin::Modal - Users Buscar-->

        <!--end::Modal - Users Buscar-->
        <!--begin::Modal - Novo Projeto-->
        <div class="modal fade" id="kt_modal_novo_projeto" tabindex="-1" aria-hidden="true" role="dialog" >
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-900px" id='conteudo_modal_novo_projeto'>
                <!--begin::Modal content-->
                <!--begin::Page loading(append to body)-->
                <div class="alert alert-primary d-flex align-items-center p-5 mb-10" id="aguardar_novo_projeto_carregar">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
                    <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.3"
                                d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z"
                                fill="currentColor"></path>
                            <path
                                d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z"
                                fill="currentColor"></path>
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

        <!--end::Modal - Novo Projeto-->


        <div class="modal fade" id="kt_modal_edita_projeto" tabindex="-1" aria-hidden="true" role="dialog" >
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-900px" id='conteudo_modal_edita_projeto'>
                <!--begin::Modal content-->
                <!--begin::Page loading(append to body)-->
                <div class="alert alert-primary d-flex align-items-center p-5 mb-10" id="aguardar_projeto_carregar">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
                    <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.3"
                                d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z"
                                fill="currentColor"></path>
                            <path
                                d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z"
                                fill="currentColor"></path>
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


        


        <div class="modal fade" tabindex="-1" id="kt_modal_detalhe_nucleo" >
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-600px" id='conteudo_modal_detalhe_nucleo'>
                <!--begin::Modal content-->
                <!--begin::Page loading(append to body)-->
                <div class="alert alert-primary d-flex align-items-center p-5 mb-10" id="aguardar_detalhe_nucleo">
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
        <!--end::Modals-->

        <!--begin::Javascript-->
        <script>
        var hostUrl = "assets/";
        </script>
        <!--begin::Global Javascript Bundle(used by all pages)-->
        <script src="assets/plugins/global/plugins.bundle.js"></script>
        <script src="assets/js/scripts.bundle.js"></script>
        <!--end::Global Javascript Bundle-->

        <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
        

<!--begin::JavaScript-->
<script>
  function loadScripts() {
    const scripts = [
      'assets/js/custom/apps/projects/list/list.js',    
      '../../js/suportes/chat/chat.js',
      'assets/js/custom/utilities/modals/upgrade-plan.js',
      "https://code.highcharts.com/highcharts.js",
               "https://code.highcharts.com/highcharts-more.js",
               "https://code.highcharts.com/modules/annotations.js",
                "https://code.highcharts.com/modules/data.js",
                "https://code.highcharts.com/modules/series-label.js",
                "https://code.highcharts.com/modules/exporting.js",
                "https://code.highcharts.com/modules/export-data.js",
                "https://code.highcharts.com/modules/accessibility.js",
     
      '../../js/dashboard/step-js.js',
      '../../js/tabelas/basic.js',
      '../../js/cockpit/create-cockpit.js',
      '../../node_modules/print-js/dist/print.js',
      '../../js/projetos/grafico-widget-01.js',
      'assets/plugins/custom/prismjs/prismjs.bundle.js',
      'assets/plugins/custom/draggable/draggable.bundle.js',
      'assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js',
      '../../js/projetos/listagem_projetos.js',
      '../../js/projetos/projetos.js',
      'assets/js/custom/documentation/general/draggable/multiple-containers.js'
      
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


<script>
if (KTCookie.get("projeto_atual") != null) {
  KTCookie.remove("projeto_atual");
}

if (KTCookie.get("plcode_atual") != null) {
  KTCookie.remove("plcode_atual");
}



    
</script>


    </body>
    <!--end::Body-->

    </html>