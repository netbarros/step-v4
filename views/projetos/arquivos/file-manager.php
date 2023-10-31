<?php
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 // buffer de saída de dados do php]
// Instancia Conexão PDO
require_once "../../../conexao.php";
$conexao = Conexao::getInstance();
require_once "../../../crud/login/verifica_sessao.php";

$_SESSION['pagina_atual'] = 'Arquivos do Projeto';

$projeto_id = isset($_GET['id']) && is_numeric($_GET['id']) 
                ? intval($_GET['id']) 
                : $_COOKIE['projeto_atual'];



$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';

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
    header("Location: /crud/login/logout.php");
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
LEFT JOIN contatos ct ON ct.id_cliente= cli.id_cliente
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

$brev_nome_projeto = substr($r_proj['nome_obra'], 0, 11);


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

if ($nivel_acesso_user_sessao == 'supervisor') {

    $sql_personalizado = "WHERE e.supervisor = '$id_BD_Colaborador'";
}

if ($nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado = "WHERE e.id_cliente= '$id_tabela_cliente_sessao'";
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
							<!--begin::Card-->
							<div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-10" style="background-size: auto calc(100% + 10rem); background-position-x: 100%; background-image: url('assets/media/illustrations/sigma-1/4.png')">
								<!--begin::Card header-->
								<div class="card-header pt-10">
									<div class="d-flex align-items-center">
										<!--begin::Icon-->
										<div class="symbol symbol-circle me-5">
											<div class="symbol-label bg-transparent text-primary border border-secondary border-dashed">
												<!--begin::Svg Icon | path: icons/duotune/abstract/abs020.svg-->
												<span class="svg-icon svg-icon-2x svg-icon-primary">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M17.302 11.35L12.002 20.55H21.202C21.802 20.55 22.202 19.85 21.902 19.35L17.302 11.35Z" fill="currentColor" />
														<path opacity="0.3" d="M12.002 20.55H2.802C2.202 20.55 1.80202 19.85 2.10202 19.35L6.70203 11.45L12.002 20.55ZM11.302 3.45L6.70203 11.35H17.302L12.702 3.45C12.402 2.85 11.602 2.85 11.302 3.45Z" fill="currentColor" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</div>
										</div>
										<!--end::Icon-->
										<!--begin::Title-->
										<div class="d-flex flex-column">
											<h2 class="mb-1">Gerenciador de Arquivos</h2>
											<div class="text-muted fw-bold">
											<a href="../../views/projetos/view-project.php?id=<?=$projeto_id;?>"><?=$r_proj['nome_obra'];?></a>
											<span class="mx-3">|</span>
											<a href="../../views/projetos/arquivos/files-project.php?id=<?=$projeto_id;?>">Arquivos</a>

                                            <?php

                                            // conta qtdade arquivos

                                            // total de leituras realizadas

                                            $conta_arquivo_projeto = $conexao->query("SELECT COUNT(DISTINCT aq.id_doc ) as Total_Arquivo, SUM(aq.size) as Total_Tamanho
                                            FROM arquivos_projeto aq 
                                            WHERE aq.id_obra='$projeto_id'

                                            ");

                                            $conta = $conta_arquivo_projeto->rowCount();

                                            if($conta>0){

                                                $r=$conta_arquivo_projeto->fetch(PDO::FETCH_ASSOC);

                                                $total=$r['Total_Arquivo'];

                                                $tamanho= $r['Total_Tamanho'];

                                                $size = $r['Total_Tamanho']/1000;
                                            $total_arquivo =  number_format($total, 0, '', '.');
                                            $total_tamanho =  number_format($size, 0, '', '.');

                                                echo '<span class="mx-3">|</span>  '.$total_tamanho.' MB';
                                                echo '<span class="mx-3">|</span>  '.$total_arquivo.' itens';


                                            }else{

                                                echo '<span class="mx-3">|</span>Total 0 arquivos, 0 GB espaço usado';

                                            }

                                            ?>

											</div>
										</div>
										<!--end::Title-->
									</div>
								</div>
								<!--end::Card header-->
								<!--begin::Card body-->
								<div class="card-body pb-0">
									<!--begin::Navs-->
									<div class="d-flex overflow-auto h-55px">
										<ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-semibold flex-nowrap">
											<!--begin::Nav item-->
											<li class="nav-item">
												<a class="nav-link text-active-primary me-6 active" href="../../views/projetos/arquivos/file-manager.php?id=<?=$projeto_id;?>">Arquivos</a>
											</li>
											<!--end::Nav item-->
											<!--begin::Nav item
											<li class="nav-item">
												<a class="nav-link text-active-primary me-6" href="../../views/projetos/file-manager-settings.php?id=<?=$projeto_id;?>">Configurações</a>
											</li>
										end::Nav item-->
										</ul>
									</div>
									<!--begin::Navs-->
								</div>
								<!--end::Card body-->
							</div>
							<!--end::Card-->
							<!--begin::Card-->
							<div class="card card-flush">
								<!--begin::Card header-->
								<div class="card-header pt-8">
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
											<input type="text" data-kt-filemanager-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Buscar Aquivos" />
										</div>
										<!--end::Buscar-->
									</div>
									<!--begin::Card toolbar-->
									<div class="card-toolbar">
										<!--begin::Toolbar-->
										<div class="d-flex justify-content-end" data-kt-filemanager-table-toolbar="base">
											<!--begin::Back to folders-->
											<button type="button"  onclick="history.back()" class="btn btn-icon btn-light-primary me-3">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="currentColor" />
														<path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="currentColor" />
														<path opacity="0.3" d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="currentColor" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</button>
											<!--end::Back to folders-->
										
											<!--begin::Add customer-->
											<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_upload">
											<!--begin::Svg Icon | path: icons/duotune/files/fil018.svg-->
											<span class="svg-icon svg-icon-2">
												<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor" />
													<path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM16 11.6L12.7 8.29999C12.3 7.89999 11.7 7.89999 11.3 8.29999L8 11.6H11V17C11 17.6 11.4 18 12 18C12.6 18 13 17.6 13 17V11.6H16Z" fill="currentColor" />
													<path opacity="0.3" d="M11 11.6V17C11 17.6 11.4 18 12 18C12.6 18 13 17.6 13 17V11.6H11Z" fill="currentColor" />
												</svg>
											</span>
											<!--end::Svg Icon-->Enviar Arquivos</button>
											<!--end::Add customer-->
                                            
												
										</div>
										<!--end::Toolbar-->
										<!--begin::Group actions-->
										<div class="d-flex justify-content-end align-items-center d-none" data-kt-filemanager-table-toolbar="selected">
											<div class="fw-bold me-5">
											<span class="me-2" data-kt-filemanager-table-select="selected_count"></span>Selecionado</div>
											<button type="button" class="btn btn-danger" data-kt-filemanager-table-select="delete_selected">Apagar Selecionado</button>
										</div>
										<!--end::Group actions-->
									</div>
									<!--end::Card toolbar-->
								</div>
								<!--end::Card header-->
								<!--begin::Card body-->
								<div class="card-body">
								
									<!--begin::Table-->
									<table id="kt_file_manager_list" data-kt-filemanager-table="files" class="table align-middle table-row-dashed fs-6 gy-5">
										<!--begin::Table head-->
										<thead>
											<!--begin::Table row-->
											<tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
												<th class="w-10px pe-2">
													<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
														<input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_file_manager_list .form-check-input" value="1" />
													</div>
												</th>
												<th class="min-w-250px">Nome do Arquivo</th>
												<th class="min-w-10px">Tamanho</th>
												<th class="min-w-125px">Data de Envio</th>
												<th class="w-125px"></th>
											</tr>
											<!--end::Table row-->
										</thead>
										<!--end::Table head-->
										<!--begin::Table body-->
										<tbody class="fw-semibold text-gray-600">

                                        <?php 

                                        // SQL tabela listagem arquivos
$sql_listagem_projetos = $conexao->query("SELECT * FROM arquivos_projeto aq WHERE aq.id_obra='$projeto_id'");

$conta_listagem_projetos = $sql_listagem_projetos->rowCount();

if($conta_listagem_projetos>0){


    $rlistagem = $sql_listagem_projetos->fetchALL(PDO::FETCH_ASSOC);

    foreach ($rlistagem as $r) {

        $data_arquivo = $r['data_cadastro_doc'];

        $date = $r['data_cadastro_doc'];

        //converts date and time to seconds
        $sec = strtotime($date);
       
        //converts seconds into a specific format
        $newdate = date("d/m/Y H:i:s", $sec);


        $total_tamanho =  number_format($r['size']/1000, 2, ',', '.');

                                        ?>
											<tr>
												<!--begin::Checkbox-->
												<td>
													<div class="form-check form-check-sm form-check-custom form-check-solid ">
														<input class="form-check-input id_arquivo" name="id_arquivo[]" type="checkbox" value="<?=$r['id_doc'];?>" />
													</div>
												</td>
												<!--end::Checkbox-->
												<!--begin::Name=-->
												<td>
													<div class="d-flex align-items-center">
														<!--begin::Svg Icon | path: icons/duotune/files/fil003.svg-->
														<span class="svg-icon svg-icon-2x svg-icon-primary me-4">
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor" />
																<path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor" />
															</svg>
														</span>
														<!--end::Svg Icon-->
														<a href="../../arquivo-projeto/<?=$r['arquivo_doc'];?>" target="_blank" class="text-gray-800 text-hover-primary"><?=$r['nome_doc'];?> </a>
													</div>
												</td>
												<!--end::Name=-->
												<!--begin::Size-->
												<td><?=$total_tamanho;?> MB</td>
												<!--end::Size-->
												<!--begin::Last modified-->
												<td><?=$newdate;?></td>
												<!--end::Last modified-->
												<!--begin::Actions-->
												<td class="text-end" data-kt-filemanager-table="action_dropdown">
													<div class="d-flex justify-content-end">
														<!--begin::Share link-->
														<div class="ms-2" data-kt-filemanger-table="copy_link">
															<button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
																<!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
																<span class="svg-icon svg-icon-5 m-0">
																	<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<path opacity="0.3" d="M18.4 5.59998C18.7766 5.9772 18.9881 6.48846 18.9881 7.02148C18.9881 7.55451 18.7766 8.06577 18.4 8.44299L14.843 12C14.466 12.377 13.9547 12.5887 13.4215 12.5887C12.8883 12.5887 12.377 12.377 12 12C11.623 11.623 11.4112 11.1117 11.4112 10.5785C11.4112 10.0453 11.623 9.53399 12 9.15698L15.553 5.604C15.9302 5.22741 16.4415 5.01587 16.9745 5.01587C17.5075 5.01587 18.0188 5.22741 18.396 5.604L18.4 5.59998ZM20.528 3.47205C20.0614 3.00535 19.5074 2.63503 18.8977 2.38245C18.288 2.12987 17.6344 1.99988 16.9745 1.99988C16.3145 1.99988 15.661 2.12987 15.0513 2.38245C14.4416 2.63503 13.8876 3.00535 13.421 3.47205L9.86801 7.02502C9.40136 7.49168 9.03118 8.04568 8.77863 8.6554C8.52608 9.26511 8.39609 9.91855 8.39609 10.5785C8.39609 11.2384 8.52608 11.8919 8.77863 12.5016C9.03118 13.1113 9.40136 13.6653 9.86801 14.132C10.3347 14.5986 10.8886 14.9688 11.4984 15.2213C12.1081 15.4739 12.7616 15.6039 13.4215 15.6039C14.0815 15.6039 14.7349 15.4739 15.3446 15.2213C15.9543 14.9688 16.5084 14.5986 16.975 14.132L20.528 10.579C20.9947 10.1124 21.3649 9.55844 21.6175 8.94873C21.8701 8.33902 22.0001 7.68547 22.0001 7.02551C22.0001 6.36555 21.8701 5.71201 21.6175 5.10229C21.3649 4.49258 20.9947 3.93867 20.528 3.47205Z" fill="currentColor" />
																		<path d="M14.132 9.86804C13.6421 9.37931 13.0561 8.99749 12.411 8.74695L12 9.15698C11.6234 9.53421 11.4119 10.0455 11.4119 10.5785C11.4119 11.1115 11.6234 11.6228 12 12C12.3766 12.3772 12.5881 12.8885 12.5881 13.4215C12.5881 13.9545 12.3766 14.4658 12 14.843L8.44699 18.396C8.06999 18.773 7.55868 18.9849 7.02551 18.9849C6.49235 18.9849 5.98101 18.773 5.604 18.396C5.227 18.019 5.0152 17.5077 5.0152 16.9745C5.0152 16.4413 5.227 15.93 5.604 15.553L8.74701 12.411C8.28705 11.233 8.28705 9.92498 8.74701 8.74695C8.10159 8.99737 7.5152 9.37919 7.02499 9.86804L3.47198 13.421C2.52954 14.3635 2.00009 15.6417 2.00009 16.9745C2.00009 18.3073 2.52957 19.5855 3.47202 20.528C4.41446 21.4704 5.69269 21.9999 7.02551 21.9999C8.35833 21.9999 9.63656 21.4704 10.579 20.528L14.132 16.975C14.5987 16.5084 14.9689 15.9544 15.2215 15.3447C15.4741 14.735 15.6041 14.0815 15.6041 13.4215C15.6041 12.7615 15.4741 12.108 15.2215 11.4983C14.9689 10.8886 14.5987 10.3347 14.132 9.86804Z" fill="currentColor" />
																	</svg>
																</span>
																<!--end::Svg Icon-->
															</button>
															<!--begin::Menu-->
															<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-300px" data-kt-menu="true">
																<!--begin::Card-->
																<div class="card card-flush">
																	<div class="card-body p-5">
																		<!--begin::Loader-->
																		<div class="d-flex" data-kt-filemanger-table="copy_link_generator">
																			<!--begin::Spinner-->
																			<div class="me-5" data-kt-indicator="on">
																				<span class="indicator-progress">
																					<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
																				</span>
																			</div>
																			<!--end::Spinner-->
																			<!--begin::Label-->
																			<div class="fs-6 text-dark">Gerando Link ...</div>
																			<!--end::Label-->
																		</div>
																		<!--end::Loader-->
																		<!--begin::Link-->
																		<div class="d-flex flex-column text-start d-none" data-kt-filemanger-table="copy_link_result">
																			<div class="d-flex mb-3">
																				<!--begin::Svg Icon | path: icons/duotune/arrows/arr085.svg-->
																				<span class="svg-icon svg-icon-2 svg-icon-success me-3">
																					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																						<path d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z" fill="currentColor" />
																					</svg>
																				</span>
																				<!--end::Svg Icon-->
																				<div class="fs-6 text-dark">Link Gerado</div>
																			</div>
																			<input type="text" class="form-control form-control-sm" value="https://step.eco.br/arquivo-projeto/<?=$r['arquivo_doc'];?>" />
																			<div class="text-muted fw-normal mt-2 fs-8 px-3">Somente leitura.
																			<a href="../../views/projetos/file-manager-settings.php?id=<?=$projeto_id;?>" class="ms-2">Alterar permissões</a></div>
																		</div>
																		<!--end::Link-->
																	</div>
																</div>
																<!--end::Card-->
															</div>
															<!--end::Menu-->
														</div>
														<!--end::Share link-->
														<!--begin::More-->
														<div class="ms-2">
															<button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
																<!--begin::Svg Icon | path: icons/duotune/general/gen052.svg-->
																<span class="svg-icon svg-icon-5 m-0">
																	<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<rect x="10" y="10" width="4" height="4" rx="2" fill="currentColor" />
																		<rect x="17" y="10" width="4" height="4" rx="2" fill="currentColor" />
																		<rect x="3" y="10" width="4" height="4" rx="2" fill="currentColor" />
																	</svg>
																</span>
																<!--end::Svg Icon-->
															</button>
															<!--begin::Menu-->
															<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
																<!--begin::Menu item-->
																<div class="menu-item px-3">
																	<a target="_blank" href="../../arquivo-projeto/<?=$r['arquivo_doc'];?>" class="menu-link px-3">Download Arquivo</a>
																</div>
																<!--end::Menu item-->
																<!--begin::Menu item-->
																<div class="menu-item px-3">
																	<a href="#" class="menu-link px-3" data-kt-filemanager-table="rename">Renomear</a>
																</div>
																<!--end::Menu item-->
																
																<!--begin::Menu item-->
																<div class="menu-item px-3">
																	<a href="#" class="menu-link text-danger px-3" data-kt-filemanager-table-filter="delete_row">Apagar</a>
																</div>
																<!--end::Menu item-->
															</div>
															<!--end::Menu-->
														</div>
														<!--end::More-->
													</div>
												</td>
												<!--end::Actions-->
											</tr>
<?php   }


} ?>
										</tbody>
										<!--end::Table body-->
									</table>
									<!--end::Table-->
								</div>
								<!--end::Card body-->
							</div>
							<!--end::Card-->
							<!--begin::Upload template-->
							<table class="d-none">
								<tr id="kt_file_manager_new_folder_row" data-kt-filemanager-template="upload">
									<td></td>
									<td id="kt_file_manager_add_folder_form" class="fv-row">
										<div class="d-flex align-items-center">
											<!--begin::Folder icon-->
											<!--begin::Svg Icon | path: icons/duotune/files/fil012.svg-->
											<span class="svg-icon svg-icon-2x svg-icon-primary me-4">
												<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor" />
													<path d="M9.2 3H3C2.4 3 2 3.4 2 4V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V7C22 6.4 21.6 6 21 6H12L10.4 3.60001C10.2 3.20001 9.7 3 9.2 3Z" fill="currentColor" />
												</svg>
											</span>
											<!--end::Svg Icon-->
											<!--end::Folder icon-->
											<!--begin:Input-->
											<input type="text" name="new_folder_name" placeholder="Enter the folder name" class="form-control mw-250px me-3" />
											<!--end:Input-->
											<!--begin:Submit button-->
											<button class="btn btn-icon btn-light-primary me-3"  id="kt_file_manager_add_folder">
												<span class="indicator-label">
													<!--begin::Svg Icon | path: icons/duotune/arrows/arr085.svg-->
													<span class="svg-icon svg-icon-1">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z" fill="currentColor" />
														</svg>
													</span>
													<!--end::Svg Icon-->
												</span>
												<span class="indicator-progress">
													<span class="spinner-border spinner-border-sm align-middle"></span>
												</span>
											</button>
											<!--end:Submit button-->
											<!--begin:Cancel button-->
											<button class="btn btn-icon btn-light-danger" id="kt_file_manager_cancel_folder">
												<span class="indicator-label">
													<!--begin::Svg Icon | path: icons/duotune/arrows/arr088.svg-->
													<span class="svg-icon svg-icon-1">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<rect opacity="0.5" x="7.05025" y="15.5356" width="12" height="2" rx="1" transform="rotate(-45 7.05025 15.5356)" fill="currentColor" />
															<rect x="8.46447" y="7.05029" width="12" height="2" rx="1" transform="rotate(45 8.46447 7.05029)" fill="currentColor" />
														</svg>
													</span>
													<!--end::Svg Icon-->
												</span>
												<span class="indicator-progress">
													<span class="spinner-border spinner-border-sm align-middle"></span>
												</span>
											</button>
											<!--end:Cancel button-->
										</div>
									</td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</table>
							<!--end::Upload template-->
							<!--begin::Rename template-->
							<div class="d-none" data-kt-filemanager-template="rename">
								<div class="fv-row">
									<div class="d-flex align-items-center">
										<span id="kt_file_manager_rename_folder_icon"></span>
										<input type="text" id="kt_file_manager_rename_input" name="rename_folder_name" placeholder="Informe o nome de identificação" class="form-control mw-250px me-3" value="" />
										<button class="btn btn-icon btn-light-primary me-3"  id="kt_file_manager_rename_folder">
											<!--begin::Svg Icon | path: icons/duotune/arrows/arr085.svg-->
											<span class="svg-icon svg-icon-1">
												<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z" fill="currentColor" />
												</svg>
											</span>
											<!--end::Svg Icon-->
										</button>
										<button class="btn btn-icon btn-light-danger" id="kt_file_manager_rename_folder_cancel">
											<span class="indicator-label">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr088.svg-->
												<span class="svg-icon svg-icon-1">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<rect opacity="0.5" x="7.05025" y="15.5356" width="12" height="2" rx="1" transform="rotate(-45 7.05025 15.5356)" fill="currentColor" />
														<rect x="8.46447" y="7.05029" width="12" height="2" rx="1" transform="rotate(45 8.46447 7.05029)" fill="currentColor" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</span>
											<span class="indicator-progress">
												<span class="spinner-border spinner-border-sm align-middle"></span>
											</span>
										</button>
									</div>
								</div>
							</div>
							<!--end::Rename template-->
							<!--begin::Action template-->
							<div class="d-none" data-kt-filemanager-template="action">
								<div class="d-flex justify-content-end">
									<!--begin::Share link-->
									<div class="ms-2" data-kt-filemanger-table="copy_link">
										<button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
											<!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
											<span class="svg-icon svg-icon-5 m-0">
												<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path opacity="0.3" d="M18.4 5.59998C18.7766 5.9772 18.9881 6.48846 18.9881 7.02148C18.9881 7.55451 18.7766 8.06577 18.4 8.44299L14.843 12C14.466 12.377 13.9547 12.5887 13.4215 12.5887C12.8883 12.5887 12.377 12.377 12 12C11.623 11.623 11.4112 11.1117 11.4112 10.5785C11.4112 10.0453 11.623 9.53399 12 9.15698L15.553 5.604C15.9302 5.22741 16.4415 5.01587 16.9745 5.01587C17.5075 5.01587 18.0188 5.22741 18.396 5.604L18.4 5.59998ZM20.528 3.47205C20.0614 3.00535 19.5074 2.63503 18.8977 2.38245C18.288 2.12987 17.6344 1.99988 16.9745 1.99988C16.3145 1.99988 15.661 2.12987 15.0513 2.38245C14.4416 2.63503 13.8876 3.00535 13.421 3.47205L9.86801 7.02502C9.40136 7.49168 9.03118 8.04568 8.77863 8.6554C8.52608 9.26511 8.39609 9.91855 8.39609 10.5785C8.39609 11.2384 8.52608 11.8919 8.77863 12.5016C9.03118 13.1113 9.40136 13.6653 9.86801 14.132C10.3347 14.5986 10.8886 14.9688 11.4984 15.2213C12.1081 15.4739 12.7616 15.6039 13.4215 15.6039C14.0815 15.6039 14.7349 15.4739 15.3446 15.2213C15.9543 14.9688 16.5084 14.5986 16.975 14.132L20.528 10.579C20.9947 10.1124 21.3649 9.55844 21.6175 8.94873C21.8701 8.33902 22.0001 7.68547 22.0001 7.02551C22.0001 6.36555 21.8701 5.71201 21.6175 5.10229C21.3649 4.49258 20.9947 3.93867 20.528 3.47205Z" fill="currentColor" />
													<path d="M14.132 9.86804C13.6421 9.37931 13.0561 8.99749 12.411 8.74695L12 9.15698C11.6234 9.53421 11.4119 10.0455 11.4119 10.5785C11.4119 11.1115 11.6234 11.6228 12 12C12.3766 12.3772 12.5881 12.8885 12.5881 13.4215C12.5881 13.9545 12.3766 14.4658 12 14.843L8.44699 18.396C8.06999 18.773 7.55868 18.9849 7.02551 18.9849C6.49235 18.9849 5.98101 18.773 5.604 18.396C5.227 18.019 5.0152 17.5077 5.0152 16.9745C5.0152 16.4413 5.227 15.93 5.604 15.553L8.74701 12.411C8.28705 11.233 8.28705 9.92498 8.74701 8.74695C8.10159 8.99737 7.5152 9.37919 7.02499 9.86804L3.47198 13.421C2.52954 14.3635 2.00009 15.6417 2.00009 16.9745C2.00009 18.3073 2.52957 19.5855 3.47202 20.528C4.41446 21.4704 5.69269 21.9999 7.02551 21.9999C8.35833 21.9999 9.63656 21.4704 10.579 20.528L14.132 16.975C14.5987 16.5084 14.9689 15.9544 15.2215 15.3447C15.4741 14.735 15.6041 14.0815 15.6041 13.4215C15.6041 12.7615 15.4741 12.108 15.2215 11.4983C14.9689 10.8886 14.5987 10.3347 14.132 9.86804Z" fill="currentColor" />
												</svg>
											</span>
											<!--end::Svg Icon-->
										</button>
										<!--begin::Menu-->
										<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-300px" data-kt-menu="true">
											<!--begin::Card-->
											<div class="card card-flush">
												<div class="card-body p-5">
													<!--begin::Loader-->
													<div class="d-flex" data-kt-filemanger-table="copy_link_generator">
														<!--begin::Spinner-->
														<div class="me-5" data-kt-indicator="on">
															<span class="indicator-progress">
																<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
															</span>
														</div>
														<!--end::Spinner-->
														<!--begin::Label-->
														<div class="fs-6 text-dark">Generating Share Link...</div>
														<!--end::Label-->
													</div>
													<!--end::Loader-->
													<!--begin::Link-->
													<div class="d-flex flex-column text-start d-none" data-kt-filemanger-table="copy_link_result">
														<div class="d-flex mb-3">
															<!--begin::Svg Icon | path: icons/duotune/arrows/arr085.svg-->
															<span class="svg-icon svg-icon-2 svg-icon-success me-3">
																<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																	<path d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z" fill="currentColor" />
																</svg>
															</span>
															<!--end::Svg Icon-->
															<div class="fs-6 text-dark">Share Link Generated</div>
														</div>
														<input type="text" class="form-control form-control-sm" value="https://path/to/file/or/folder/" />
														<div class="text-muted fw-normal mt-2 fs-8 px-3">Read only.
														<a href="../../tema/dist/apps/file-manager/settings/.html" class="ms-2">Change permissions</a></div>
													</div>
													<!--end::Link-->
												</div>
											</div>
											<!--end::Card-->
										</div>
										<!--end::Menu-->
									</div>
									<!--end::Share link-->
									<!--begin::More-->
									<div class="ms-2">
										<button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
											<!--begin::Svg Icon | path: icons/duotune/general/gen052.svg-->
											<span class="svg-icon svg-icon-5 m-0">
												<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect x="10" y="10" width="4" height="4" rx="2" fill="currentColor" />
													<rect x="17" y="10" width="4" height="4" rx="2" fill="currentColor" />
													<rect x="3" y="10" width="4" height="4" rx="2" fill="currentColor" />
												</svg>
											</span>
											<!--end::Svg Icon-->
										</button>
										<!--begin::Menu-->
										<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<a href="#" class="menu-link px-3">Download File</a>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<a href="#" class="menu-link px-3" data-kt-filemanager-table="rename">Rename</a>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<a href="#" class="menu-link px-3" data-kt-filemanager-table-filter="move_row" data-bs-toggle="modal" data-bs-target="#kt_modal_move_to_folder">Move to folder</a>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<a href="#" class="menu-link text-danger px-3" data-kt-filemanager-table-filter="delete_row">Delete</a>
											</div>
											<!--end::Menu item-->
										</div>
										<!--end::Menu-->
									</div>
									<!--end::More-->
								</div>
							</div>
							<!--end::Action template-->
							<!--begin::Checkbox template-->
							<div class="d-none" data-kt-filemanager-template="checkbox">
								<div class="form-check form-check-sm form-check-custom form-check-solid">
									<input class="form-check-input" type="checkbox" value="1" />
								</div>
							</div>
							<!--end::Checkbox template-->
							<!--begin::Modals-->
							<!--begin::Modal - Upload File-->
							<div class="modal fade" id="kt_modal_upload" tabindex="-1" aria-hidden="true">
								<!--begin::Modal dialog-->
								<div class="modal-dialog modal-dialog-centered mw-650px">
									<!--begin::Modal content-->
									<div class="modal-content">
										<!--begin::Form-->
										<form class="form" action="none" >
											<!--begin::Modal header-->
											<div class="modal-header">
												<!--begin::Modal title-->
												<h2 class="fw-bold">Enviar Aquivos</h2>
												<!--end::Modal title-->
												<!--begin::Close-->
												<div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
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
											<div class="modal-body pt-10 pb-15 px-lg-17">
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



											</div>
											<!--end::Modal body-->
										</form>
										<!--end::Form-->
									</div>
								</div>
							</div>
							<!--end::Modal - Upload File-->
						
							<!--end::Modals-->
						</div>
						<!--end::Post-->
					</div>
					<!--end::Container-->
					<!--begin::Footer-->
					<!--begin::Footer-->
                    <?php  require_once $_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'; ?>
                    <!--end::Footer-->
					<!--end::Footer-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--end::Root-->
		<!--begin::Drawers-->
    <!--begin::Activities drawer-->
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/views/conta-usuario/atividade-usuario.php'; ?>
    <!--end::Activities drawer-->
        <!--end::Activities drawer-->
 <!--begin::Chat drawer-->
 <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/views/chat/chat-usuario.php'; ?>
   
    
   <!--end::Chat drawer-->
		<!--end::Drawers-->
		<!--end::Main-->

	
	
		<!--end::Scrolltop-->
		<!--begin::Modals-->
		
	
	
	
		<!--end::Modals-->
		<!--begin::Javascript-->
		<script>var hostUrl = "assets/";</script>
		<!--begin::Global Javascript Bundle(used by all pages)-->
		<script src="assets/plugins/global/plugins.bundle.js"></script>
		<script src="assets/js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
		<!--begin::Vendors Javascript(used by this page)-->
		<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
		<!--end::Vendors Javascript-->
		<!--begin::Custom Javascript(used by this page)-->
		<script src="../../js/projetos/list.js"></script>
		<script src="../../js/dashboard/step-js.js"></script>
		
		<script src="assets/js/widgets.bundle.js"></script>
		<script src="assets/js/custom/widgets.js"></script>
		<script src="../../js/suportes/chat/chat.js"></script>

		
<script>
      var projeto ="<?php echo $projeto_id; ?>";
      
if (projeto) {
        // Inicializa o Dropzone se o cookie existir
        var myDropzone = new Dropzone("#kt_dropzonejs_arquivos_projeto", {
            url: '../../crud/projetos/arquivos/upload.php?projeto=' + projeto, // Set the url for your upload script location
            paramName: "file", // The name that will be used to transfer the file
            maxFiles: 10,
            maxFilesize: 10, // MB
            addRemoveLinks: true,
            init: function () {
                this.on("success", function (file, responseText) {
                    // Handle the responseText here. For example, add the text to the preview element:
                    file.previewTemplate.appendChild(document.createTextNode(responseText.retorno));
                    console.log("arquivo enviado");

                    createMetronicToast('Enviando Documento', 'Assim que for Validado, sua sessão será atualizada, aguarde por favor...', 5000, 'success', 'bi bi-check2-square');

                    setTimeout(() => {
                    

                        location.reload();
                    }, 3000);

                });
                this.on("error", function (file, responseText) {
                    // Handle the responseText here. For example, add the text to the preview element:
                    file.previewTemplate.appendChild(document.createTextNode(responseText.retorno));
                    console.log("arquivo não enviado");
                });
            }
        });
    } else {
        console.log("Id do projeto que tem arquivos não foi lido.");
    }

</script>

		<!--end::Custom Javascript-->
		<!--end::Javascript-->
	</body>
	<!--end::Body-->
</html>