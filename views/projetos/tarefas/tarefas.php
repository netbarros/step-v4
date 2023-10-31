<?php
 // buffer de saída de dados do php]
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$_SESSION['pagina_atual'] = 'Tarefas para o Projeto';

$projeto_id = isset($_GET['id']) && is_numeric($_GET['id']) 
                ? intval($_GET['id']) 
                : $_COOKIE['projeto_atual'];





$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';

if ($nivel_acesso_user_sessao == "" ||  $nivel_acesso_user_sessao == 'undefined' || $projeto_id == '' || $projeto_id == 'undefined' || $projeto_id ==NULL || $nivel_acesso_user_sessao ==NULL) {


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

if($nivel_acesso_user_sessao=='cliente'){

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


$nome_obra = $r_proj['nome_obra'];

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
                                    <?php include_once "../../../views/projetos/topo-projeto.php";?>
                                    <!--end::Details-->
                                    <div class="separator"></div>
                                    <!--begin::Nav-->
                                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 " href="../../views/projetos/view-project.php?id=<?php echo $projeto_id;?>">Overview</a>
                                        </li>
                                        <!--end::Nav item-->
                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 active" href="../../views/projetos/tarefas/tarefas.php?id=<?php echo $projeto_id;?>">Tarefas</a>
                                        </li>
                                        <!--end::Nav item-->

                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                        <a class="nav-link text-active-primary py-5 me-6" href="../../views/projetos/nucleos/nucleos.php?id=<?php echo $projeto_id;?>">Núcleos</a>
                                        </li>
                                        <!--end::Nav item-->


                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6" href="../../views/projetos/plcodes/plcodes.php?id=<?php echo $projeto_id;?>">PLCodes</a>
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
                                            <a class="nav-link text-active-primary py-5 me-6" href="../../views/projetos/orcamento.php?id=<?php echo $projeto_id;?>">Orçamentos</a>
                                        </li>
                                        end::ABA Orçamentos-->

                                    </ul>
                                    <!--end::Nav-->
                                </div>
                            </div>
                            <!--end::Navbar-->
                      	<!--begin::Toolbar-->
							<div class="d-flex flex-wrap flex-stack pt-10 pb-8">
								<!--begin::Heading-->
								<h3 class="fw-bold my-2">Tarefas 
								<span class="fs-6 text-gray-400 fw-semibold ms-1">por Mais Recentes ↓</span></h3>
								<!--end::Heading-->
								<!--begin::Controls-->
								<div class="d-flex flex-wrap my-1">
									<!--begin::Tab nav-->
									<ul class="nav nav-pills me-5">
										<li class="nav-item m-0">
											<a class="btn btn-sm btn-icon btn-light btn-color-muted btn-active-primary active me-3" data-bs-toggle="tab" href="#kt_project_targets_card_pane">
												<!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
												<span class="svg-icon svg-icon-1">
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
											</a>
										</li>
										<li class="nav-item m-0">
											<a class="btn btn-sm btn-icon btn-light btn-color-muted btn-active-primary" data-bs-toggle="tab" href="#kt_project_targets_table_pane">
												<!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="currentColor" />
														<path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="currentColor" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</li>
									</ul>
									<!--end::Tab nav-->
									<!--begin::Wrapper-->
									<div class="my-0">
                                    <div class="d-flex align-items-center">
                                        <!--begin::Back to folders-->
											<button type="button" onclick="history.back()" class="btn btn-icon btn-light-primary me-3 " data-bs-toggle="tooltip" data-bs-placement="top" data-kt-initialized="1">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
												<span class="svg-icon svg-icon-2">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="currentColor"></path>
<path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="currentColor"></path>
</svg>
												</span>
												<!--end::Svg Icon-->
											</button>
											<!--end::Back to folders-->

<a href="javascript:;" class="btn btn-primary btn-flex h-40px border-0 fw-bold px-4 px-lg-6 ms-2 ms-lg-3" data-bs-toggle="modal" data-bs-target="#kt_modal_new_target"  data-projeto_atual='<?=$projeto_id;?>' data-nome='<?=$nome_obra;?>'  >Nova Tarefa</a>

</div>
										<!--end::Select-->
									</div>
									<!--end::Wrapper-->
								</div>
								<!--end::Controls-->
							</div>
							<!--end::Toolbar-->
							<!--begin::Tab Content-->
							<div class="tab-content">
								<!--begin::Tab pane-->
								<div id="kt_project_targets_card_pane" class="tab-pane fade show active">
									<!--begin::Row-->
									<div class="row g-9">
										<!--begin::Col-->
										<div class="col-md-4 col-lg-12 col-xl-4">
											<!--begin::Col header-->
											<div class="mb-9">
												<div class="d-flex flex-stack">
													<div class="fw-bold fs-4">Tarefa por Leitura
                                                 
                                    <?php
                                    // consultar Checkin por Leitura
                                    $sql = $conexao->query("SELECT COUNT(DISTINCT pr.id_periodo_ponto) as Total_Tarefa_Leitura
                                                FROM periodo_ponto pr
                                                INNER JOIN estacoes e ON e.id_estacao = pr.id_estacao 
                                              
                                              WHERE pr.tipo_checkin LIKE '%ponto_parametro%' AND pr.id_obra='$projeto_id'  ORDER BY pr.id_periodo_ponto DESC
                                                ");

                                    $r = $sql->rowCount();


                                    if ($r > 0) {

                                        $rtow = $sql->fetch(PDO::FETCH_ASSOC);

                                        $total = $rtow['Total_Tarefa_Leitura'];

                                        echo '<span class="badge badge-light-warning">'.$total.'</span>';
                                    } else {

                                        echo '<span class="badge badge-light-danger">0</span>';
                                    }
                                    ?>
													</div>
											
												</div>
												<div class="h-3px w-100 bg-warning"></div>
											</div>
											<!--end::Col header-->

   <?php 
   
   $sql_tarefa_leitura = $conexao->query("SELECT * 
                                            FROM periodo_ponto pr 
                                            INNER JOIN
                                            pontos_estacao pt On pt.id_ponto = pr.id_ponto
                                            INNER JOIN
                                            parametros_ponto prt ON prt.id_parametro = pr.id_parametro  
                                            INNER JOIN 
                                            estacoes e ON e.id_estacao = pt.id_estacao 
                                                                                   
                                            WHERE pr.id_obra='$projeto_id' 

                                            AND pr.tipo_checkin 
                                            LIKE '%ponto_parametro%'   
                                          
                                            GROUP BY pr.id_periodo_ponto 

                                            ORDER BY pr.id_periodo_ponto DESC

                                          
 ");
    $retorno = $sql_tarefa_leitura->rowCount();


    if ($retorno > 0) {

        $row = $sql_tarefa_leitura->fetchALL(PDO::FETCH_ASSOC);

        foreach($row as $r){


            $titulo_tarefa = $r['titulo_tarefa'] ? $r['titulo_tarefa'] : 'Título Ausente';

            $detalhes_tarefa = $r['detalhes_tarefa'] ? $r['detalhes_tarefa'] : 'Detalhe Ausente';

            $agenda = $r['modo_checkin_periodo'] ? $r['modo_checkin_periodo'] : '';

            $horario_leitura = $r['hora_leitura'] ? $r['hora_leitura'] : '';
            switch ($agenda) {

                case "1":
                    $agendamento = "Horário Livre";
                    break;
                case "2":
                    $agendamento ="Leitura Agendada para às &nbsp <span class='text-primary'>".substr("$horario_leitura", 0,-3).' </span>';
                    break;                    
                default:
                $agendamento = "Agendamento não Informado.";

            }

            $recorr = $r['ciclo_leitura'];
            switch ($recorr) {

                case "0":
                    $recorrencia = "Única";
                    break;
                case "1":
                    $recorrencia ="Diária";
                    break;
                case "2":
                    $recorrencia ="Semanal";
                    break;                                           
                default:
                $recorrencia = "Recorrência não Informada.";

            }
            $status_tarefa = $r['status_periodo'];
            switch ($status_tarefa) {

                case "1":
                    $status_tarefa_ = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"/>
                    <path d="M10.4343 12.4343L8.75 10.75C8.33579 10.3358 7.66421 10.3358 7.25 10.75C6.83579 11.1642 6.83579 11.8358 7.25 12.25L10.2929 15.2929C10.6834 15.6834 11.3166 15.6834 11.7071 15.2929L17.25 9.75C17.6642 9.33579 17.6642 8.66421 17.25 8.25C16.8358 7.83579 16.1642 7.83579 15.75 8.25L11.5657 12.4343C11.2533 12.7467 10.7467 12.7467 10.4343 12.4343Z" fill="currentColor"/>
                    </svg>';
                    $texto_status ='Tarefa Ativa';
                    break;
                case "2":
                    $status_tarefa_ ='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"/>
                    <rect x="11" y="17" width="7" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"/>
                    <rect x="11" y="9" width="2" height="2" rx="1" transform="rotate(-90 11 9)" fill="currentColor"/>
                    </svg>';
                    $texto_status ='Tarefa Inativa';
                    break;                    
                default:
                $status_tarefa_ ='<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                 <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"/>
                    <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
                    <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
                    <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
                </g>
            </svg>';
            $texto_status ='Status Tarefa';

            }



            $plcode = $r['nome_ponto'] ?? 'Ponto não Informado';

            $id_periodo = $r['id_periodo_ponto'] ?? 'Período não Informado';
            
            $indicadores = $r['nome_parametro'] ?? 'Indicador não Informado';
            
            $nucleo = $r['nome_estacao'] ?? 'Núcleo não Informado';
            
            $andamento_tarefa='';
            $data_realizacao='';


?>                                       
											<!--begin::Card-->
											<div class="card mb-6 mb-xl-9">
												<!--begin::Card body-->
												<div class="card-body">
													<!--begin::Header-->
													<div class="col-lg-12 d-flex align-items-center flex-stack mb-6">
														<!--begin::Badge-->
                                                        
                                                        
<!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
<span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/>
<path d="M12.0006 11.1542C13.1434 11.1542 14.0777 10.22 14.0777 9.0771C14.0777 7.93424 13.1434 7 12.0006 7C10.8577 7 9.92348 7.93424 9.92348 9.0771C9.92348 10.22 10.8577 11.1542 12.0006 11.1542Z" fill="currentColor"/>
<path d="M15.5652 13.814C15.5108 13.6779 15.4382 13.551 15.3566 13.4331C14.9393 12.8163 14.2954 12.4081 13.5697 12.3083C13.479 12.2993 13.3793 12.3174 13.3067 12.3718C12.9257 12.653 12.4722 12.7981 12.0006 12.7981C11.5289 12.7981 11.0754 12.653 10.6944 12.3718C10.6219 12.3174 10.5221 12.2902 10.4314 12.3083C9.70578 12.4081 9.05272 12.8163 8.64456 13.4331C8.56293 13.551 8.49036 13.687 8.43595 13.814C8.40875 13.8684 8.41781 13.9319 8.44502 13.9864C8.51759 14.1133 8.60828 14.2403 8.68991 14.3492C8.81689 14.5215 8.95295 14.6757 9.10715 14.8208C9.23413 14.9478 9.37925 15.0657 9.52439 15.1836C10.2409 15.7188 11.1026 15.9999 11.9915 15.9999C12.8804 15.9999 13.7421 15.7188 14.4586 15.1836C14.6038 15.0748 14.7489 14.9478 14.8759 14.8208C15.021 14.6757 15.1661 14.5215 15.2931 14.3492C15.3838 14.2312 15.4655 14.1133 15.538 13.9864C15.5833 13.9319 15.5924 13.8684 15.5652 13.814Z" fill="currentColor"/>
</svg>
</span>
												<!--end::Svg Icon-->
														
                                                        
                                                        <button class="btn btn-light-primary me-5" data-bs-toggle="modal" data-bs-target="#kt_modal_edita_target" data-id_projeto_tarefa='<?=$projeto_id; ?>' data-id_tarefa='<?=$r['id_periodo_ponto'];?>'>
                                                        <?php
                                                        
                                                        
                                                        switch ($r['tipo_checkin']) {
                                                            case 'ponto_plcode':
                                                                # code...
                                                                echo mb_strtoupper('Checkin Presencial');
                                                                break;

                                                                case 'ponto_parametro':
                                                                    # code...
                                                                    echo mb_strtoupper('Checkin Leitura');
                                                                    break;

                                                                    case 'tarefa_agendada':
                                                                        # code...
                                                                        echo mb_strtoupper('Tarefa Delegada');
                                                                        break;
                                                            
                                                            default:
                                                                # code...
                                                                echo 'Tipo de Tarefa';
                                                                break;
                                                        }
                                                        
                                                        
                                                        $dias_semana='';
                                                        $recorr = $r['ciclo_leitura'];

                                                        if( $recorr=='2'){ //caso o ciclo seja semanal, lista os dias da semana:
                                                        
                                                        $sql_dias = $conexao->query("SELECT * FROM periodo_dia_ponto pd
                                                        INNER JOIN dia_semana d ON d.representa_php= pd.dia_semana WHERE pd.id_periodo_ponto ='$id_periodo ' GROUP BY d.nome_dia_semana " );
                                                        
                                                        $row_dias= $sql_dias->fetchALL(PDO::FETCH_ASSOC);
                                                        foreach( $row_dias as $rd){
                                                        
                                                           
                                                        
                                                            $dias_semana .=  '<div class="d-flex flex-stack mb-1 p-1"><span class="badge badge-dark badge-sm">'.mb_strtoupper($rd['nome_dia_semana']).'</span></div> '; 
                                                        }
                                                        
                                                        }    
                                                        
                                                        
                                                        ?>

                                                
</button>

														<!--end::Badge-->
													
													</div>
													<!--end::Header-->
                                                   
													<!--begin::Title-->
													<div class="mb-2">
														<a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kt_modal_edita_target" data-id_projeto_tarefa='<?=$projeto_id; ?>' data-id_tarefa='<?=$r['id_periodo_ponto'];?>' class="fs-4 fw-bold mb-1 text-gray-900 text-hover-primary"><?=$titulo_tarefa;?></a>
													</div>
													<!--begin::Content-->
                                                	<div class="card-body text-light fs-6 text-gray-600">
                                                    <div class="d-flex align-items-center flex-wrap mb-0">
																	<!--begin::Name-->
																	<a href="javascript:;" class="text-primary fw-bold me-6">Detalhes da Tarefa:</a>
																	<!--end::Name-->
																	
																</div>
                                                                <?=$detalhes_tarefa;?> </div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">Agendamento:</div> <span class="text-light fs-6 text-gray-600"><?=$agendamento;?></span> </div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">Recorrência:</div>  <span class="text-light fs-8 text-warning"><?=mb_strtoupper($recorrencia);?></span> <span class="text-light fs-6 text-gray-600"><?php if($dias_semana!=''){ echo $dias_semana;} else {echo 'Não Aplicável';};?></span></div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">Núcleo:</div>  <span class="text-light fs-6 text-gray-600"><?=$nucleo;?></span></div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">PLCode:</div>  <span class="text-light fs-6 text-gray-600"><?=$plcode;?></span></div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">Indicador:</div>  <span class="text-light fs-6 text-gray-600"><?=$indicadores;?></span></div>
                                                    <div class="separator separator-dashed my-3"></div>
													<!--end::Content-->

													<!--begin::Footer-->
													<div class="d-flex flex-stack flex-wrapr">
														<!--begin::Users-->
														<div class="symbol-group symbol-hover my-1">
<?php 

$total_tarefa_leitura_realizada = 0; // Inicialize a variável fora do bloco try

if( $r['ciclo_leitura']=='1' || $r['ciclo_leitura']=='2' || $r['ciclo_leitura']=='0' ){
   
   $id_periodo_tarefa_leitura = $r['id_periodo_ponto'];
    try {
        $stmt = $conexao->prepare("SELECT COUNT(id_periodo_ponto) as total_tarefa_leitura_realizada,data_cadastro_checkin  FROM checkin WHERE id_periodo_ponto = :id_periodo");
    $stmt->bindParam(':id_periodo', $id_periodo_tarefa_leitura, PDO::PARAM_INT);
    $stmt->execute();
    
       
        // Agora, $total_tarefa_realizada contém o número total de vezes que o id_periodo_ponto aparece.
   
        if ($stmt !== false) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_tarefa_leitura_realizada = $row['total_tarefa_leitura_realizada'];
           
            if( $r['ciclo_leitura']=='0' &&  $total_tarefa_leitura_realizada > 0 ){
            // Converte o timestamp para o formato de data brasileiro
            $data_cadastro_checkin = new DateTime($row['data_cadastro_checkin']);
            $data_realizacao = $data_cadastro_checkin->format('d/m/Y H:i');
            echo "<br><span class='badge badge-light-success fs-9 badge-square badge-sm'>Executada: $data_realizacao</span>";
            $andamento_tarefa = 'Tarefa Realizada';
            echo "<br><span class='badge badge-light-success fs-9 badge-square badge-sm'>$andamento_tarefa</span>";

        }elseif($r['ciclo_leitura']=='1' || $r['ciclo_leitura']=='2' &&  $total_tarefa_leitura_realizada > 0 ){
            // Converte o timestamp para o formato de data brasileiro
           
            $andamento_tarefa = 'Tarefa em Realização';
            echo "<br><span class='badge badge-light-success fs-9 badge-square badge-sm'>$andamento_tarefa</span>";
        }
            
        } else {
            $andamento_tarefa = 'Tarefa ainda não realizada';
            echo "<br><span class='badge badge-light-warning fs-9 badge-square badge-sm'>$andamento_tarefa</span>";
            $data_realizacao = 'Aguardando';  // ou você pode definir um valor padrão
            echo "<br><span class='badge badge-light-warning fs-9 badge-square badge-sm'>$data_realizacao</span>";
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
    


}




?>

														</div>
														<!--end::Users-->

														<!--begin::Stats-->
														<div class="d-flex my-1">
															<!--begin::Stat-->
                                                            <div class="border border-dashed border-gray-300 rounded py-2 px-5 mx-3" data-bs-toggle="tooltip" title="Total desta Tarefa já Realizada">
																<!--begin::Svg Icon | path: icons/duotune/communication/com008.svg-->
																<span class="svg-icon svg-icon-3 text-success">

                                                                <?=$total_tarefa_leitura_realizada;?>

                                            

																</span>
																<!--end::Svg Icon-->
															
															</div>

															<div class="border border-dashed border-gray-300 rounded py-2 px-5 " data-bs-toggle="tooltip" title="<?=$texto_status;?>">
																<!--begin::Svg Icon | path: icons/duotune/communication/com008.svg-->
																<span class="svg-icon svg-icon-3">

                                                                <?=$status_tarefa_;?>

																</span>
																<!--end::Svg Icon-->
															
															</div>
															<!--end::Stat-->
															<!--begin::Stat-->
															<div class="border border-dashed border-gray-300 rounded py-2 px-3 ms-3" data-bs-toggle="tooltip" title="Tarefa ID">
																<!--begin::Svg Icon | path: icons/duotune/communication/com012.svg-->
																<span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M16.0077 19.2901L12.9293 17.5311C12.3487 17.1993 11.6407 17.1796 11.0426 17.4787L6.89443 19.5528C5.56462 20.2177 4 19.2507 4 17.7639V5C4 3.89543 4.89543 3 6 3H17C18.1046 3 19 3.89543 19 5V17.5536C19 19.0893 17.341 20.052 16.0077 19.2901Z" fill="currentColor"/>
</svg>
																</span>
																<!--end::Svg Icon-->
																<span class="ms-1 fs-7 fw-bold text-gray-600"><?=$r['id_periodo_ponto'];?></span>
															</div>
															<!--end::Stat-->
														</div>
														<!--end::Stats-->
													</div>
													<!--end::Footer-->
												</div>
												<!--end::Card body-->
											</div>
											<!--end::Card-->
                                            <?php } } ?>      										
											<a href="#" class="btn btn-primary er w-100 fs-6 px-8 py-4" data-bs-toggle="modal" data-bs-target="#kt_modal_new_target" data-projeto_atual='<?=$projeto_id;?>'>Criar Nova Tarefa</a>
										</div>
										<!--end::Col-->
										<!--begin::Col-->
										<div class="col-md-4 col-lg-12 col-xl-4">
											<!--begin::Col header-->
											<div class="mb-9">
												<div class="d-flex flex-stack">
													<div class="fw-bold fs-4">Tarefa Presencial

                                                    
                                                    <?php
                                    // consultar Tarefa  Presencial
                                    $sql = $conexao->query("SELECT COUNT(DISTINCT pr.id_periodo_ponto) as Total_Tarefa_Presencial
                                                FROM periodo_ponto pr
                                                INNER JOIN estacoes e ON e.id_estacao = pr.id_estacao 
                                              
                                              WHERE pr.tipo_checkin LIKE '%ponto_plcode%'  AND pr.id_obra='$projeto_id'
                                                ");

                                    $r = $sql->rowCount();


                                    if ($r > 0) {

                                        $rtow = $sql->fetch(PDO::FETCH_ASSOC);

                                        $total = $rtow['Total_Tarefa_Presencial'];

                                        echo '<span class="badge badge-light-info">'.$total.'</span>';
                                    } else {

                                        echo '<span class="badge badge-light-warning">0</span>';
                                    }
                                    ?></div>
												
												</div>
												<div class="h-3px w-100 bg-info"></div>
											</div>
									<!--end::Col header-->

   <?php 


   
   $sql_tarefa_leitura = $conexao->query("SELECT * 
                                            FROM periodo_ponto pr 
                                            INNER JOIN
                                            pontos_estacao pt On pt.id_ponto = pr.id_ponto
                                            INNER JOIN 
                                            estacoes e ON e.id_estacao = pt.id_estacao                                        
                                            WHERE pr.id_obra='$projeto_id' 
                                            AND pr.tipo_checkin 
                                            LIKE '%ponto_plcode%'   
                                          
                                            GROUP BY pr.id_periodo_ponto 
                                            HAVING pr.id_estacao
 ");
    $retorno = $sql_tarefa_leitura->rowCount();


    if ($retorno > 0) {

        $row = $sql_tarefa_leitura->fetchALL(PDO::FETCH_ASSOC);

        foreach($row as $r){


            $titulo_tarefa = $r['titulo_tarefa'] ? $r['titulo_tarefa'] : 'Título Ausente';

            $detalhes_tarefa = $r['detalhes_tarefa'] ? $r['detalhes_tarefa'] : 'Detalhe Ausente';

            $agenda = $r['modo_checkin_periodo'] ? $r['modo_checkin_periodo'] : '';

            $horario_leitura = $r['hora_leitura'] ? $r['hora_leitura'] : '';
            switch ($agenda) {

                case "1":
                    $agendamento = "Horário Livre";
                    break;
                case "2":
                    $agendamento ="Leitura Agendada para às &nbsp <span class='text-primary'>".substr("$horario_leitura", 0,-3).'</span>';
                    break;                    
                default:
                $agendamento = "Agendamento não Informado.";

            }

            $recorr = $r['ciclo_leitura'];
            switch ($recorr) {

                case "0":
                    $recorrencia = "Única";
                    break;
                case "1":
                    $recorrencia ="Diária";
                    break;
                case "2":
                    $recorrencia =" Semanal";
                    break;                                           
                default:
                $recorrencia = "Recorrência não Informada.";

            }
            $status_tarefa = $r['status_periodo'];
            switch ($status_tarefa) {

                case "1":
                    $status_tarefa_ = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"/>
                    <path d="M10.4343 12.4343L8.75 10.75C8.33579 10.3358 7.66421 10.3358 7.25 10.75C6.83579 11.1642 6.83579 11.8358 7.25 12.25L10.2929 15.2929C10.6834 15.6834 11.3166 15.6834 11.7071 15.2929L17.25 9.75C17.6642 9.33579 17.6642 8.66421 17.25 8.25C16.8358 7.83579 16.1642 7.83579 15.75 8.25L11.5657 12.4343C11.2533 12.7467 10.7467 12.7467 10.4343 12.4343Z" fill="currentColor"/>
                    </svg>';
                    $texto_status ='Tarefa Ativa';
                    break;
                case "2":
                    $status_tarefa_ ='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"/>
                    <rect x="11" y="17" width="7" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"/>
                    <rect x="11" y="9" width="2" height="2" rx="1" transform="rotate(-90 11 9)" fill="currentColor"/>
                    </svg>';
                    $texto_status ='Tarefa Inativa';
                    break;                    
                default:
                $status_tarefa_ ='<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                 <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"/>
                    <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
                    <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
                    <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
                </g>
            </svg>';
            $texto_status ='Status Tarefa';

            }


            $plcode = $r['nome_ponto'] ?? 'Ponto não Informado';

            $id_periodo = $r['id_periodo_ponto'] ?? 'Período não Informado';
            
            $indicadores = $r['nome_parametro'] ?? 'Indicador não Informado';
            
            $nucleo = $r['nome_estacao'] ?? 'Núcleo não Informado';



           

   ?>                                         
											<!--begin::Card-->
											<div class="card mb-6 mb-xl-9">
												<!--begin::Card body-->
												<div class="card-body">
													<!--begin::Header-->
													<div class="d-flex flex-stack mb-3">
														<!--begin::Badge-->
														                  
                                                        
<!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
<span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/>
<path d="M12.0006 11.1542C13.1434 11.1542 14.0777 10.22 14.0777 9.0771C14.0777 7.93424 13.1434 7 12.0006 7C10.8577 7 9.92348 7.93424 9.92348 9.0771C9.92348 10.22 10.8577 11.1542 12.0006 11.1542Z" fill="currentColor"/>
<path d="M15.5652 13.814C15.5108 13.6779 15.4382 13.551 15.3566 13.4331C14.9393 12.8163 14.2954 12.4081 13.5697 12.3083C13.479 12.2993 13.3793 12.3174 13.3067 12.3718C12.9257 12.653 12.4722 12.7981 12.0006 12.7981C11.5289 12.7981 11.0754 12.653 10.6944 12.3718C10.6219 12.3174 10.5221 12.2902 10.4314 12.3083C9.70578 12.4081 9.05272 12.8163 8.64456 13.4331C8.56293 13.551 8.49036 13.687 8.43595 13.814C8.40875 13.8684 8.41781 13.9319 8.44502 13.9864C8.51759 14.1133 8.60828 14.2403 8.68991 14.3492C8.81689 14.5215 8.95295 14.6757 9.10715 14.8208C9.23413 14.9478 9.37925 15.0657 9.52439 15.1836C10.2409 15.7188 11.1026 15.9999 11.9915 15.9999C12.8804 15.9999 13.7421 15.7188 14.4586 15.1836C14.6038 15.0748 14.7489 14.9478 14.8759 14.8208C15.021 14.6757 15.1661 14.5215 15.2931 14.3492C15.3838 14.2312 15.4655 14.1133 15.538 13.9864C15.5833 13.9319 15.5924 13.8684 15.5652 13.814Z" fill="currentColor"/>
</svg>
</span>
												<!--end::Svg Icon-->
														
                                                        
                                                        <button class="btn btn-light-primary me-5" data-bs-toggle="modal" data-bs-target="#kt_modal_edita_target" data-id_projeto_tarefa='<?=$projeto_id; ?>' data-id_tarefa='<?=$r['id_periodo_ponto'];?>'>
                                                        <?php
                                                        
                                                        
                                                        switch ($r['tipo_checkin']) {
                                                            case 'ponto_plcode':
                                                                # code...
                                                                echo mb_strtoupper('Checkin Presencial');
                                                                break;

                                                                case 'ponto_parametro':
                                                                    # code...
                                                                    echo mb_strtoupper('Checkin Leitura');
                                                                    break;

                                                                    case 'tarefa_agendada':
                                                                        # code...
                                                                        echo mb_strtoupper('Tarefa Delegada');
                                                                        break;
                                                            
                                                            default:
                                                                # code...
                                                                echo 'Tipo de Tarefa';
                                                                break;
                                                        }
                                                        
                                                        
                                                        $dias_semana='';
                                                        $recorr = $r['ciclo_leitura'];

                                                        if( $recorr=='2'){ //caso o ciclo seja semanal, lista os dias da semana:
                                                        
                                                        $sql_dias = $conexao->query("SELECT * FROM periodo_dia_ponto pd
                                                        INNER JOIN dia_semana d ON d.representa_php= pd.dia_semana WHERE pd.id_periodo_ponto ='$id_periodo ' GROUP BY d.nome_dia_semana " );
                                                        
                                                        $row_dias= $sql_dias->fetchALL(PDO::FETCH_ASSOC);
                                                        foreach( $row_dias as $rd){
                                                        
                                                           
                                                        
                                                            $dias_semana .=  '<div class="d-flex flex-stack mb-1 p-1"><span class="badge badge-dark badge-sm">'.mb_strtoupper($rd['nome_dia_semana']).'</span></div> '; 
                                                        }
                                                        
                                                        } 
                                                        
                                                        
                                                        ?>

                                                
</button>

														<!--end::Badge-->
														<!--end::Badge-->
													
													</div>
													<!--end::Header-->
													<!--begin::Title-->
													<div class="mb-2">
														<a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kt_modal_edita_target" data-id_projeto_tarefa='<?=$projeto_id; ?>' data-id_tarefa='<?=$r['id_periodo_ponto'];?>' class="fs-4 fw-bold mb-1 text-gray-900 text-hover-primary"><?=$titulo_tarefa;?></a>
													</div>
													<!--end::Title-->
													<!--begin::Content-->
                                                    <div class="card-body text-light fs-6 text-gray-600">
                                                    <div class="d-flex align-items-center flex-wrap mb-0">
																	<!--begin::Name-->
																	<a href="javascript:;" class="text-primary fw-bold me-6">Detalhes da Tarefa:</a>
																	<!--end::Name-->
																	
																</div>
                                                                <?=$detalhes_tarefa;?> </div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">Agendamento:</div> <span class="text-light fs-6 text-gray-600"><?=$agendamento;?></span> </div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">Recorrência:</div>  <span class="text-light fs-8 text-warning"><?=mb_strtoupper($recorrencia);?></span> <span class="text-light fs-6 text-gray-600"><?php if($dias_semana!=''){ echo $dias_semana;} else {echo 'Não Aplicável';};?></span></div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">Núcleo:</div>  <span class="text-light fs-6 text-gray-600"><?=$nucleo;?></span></div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">PLCode:</div>  <span class="text-light fs-6 text-gray-600"><?=$plcode;?></span></div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">Indicador:</div>  <span class="text-light fs-6 text-gray-600"><?=$indicadores;?></span></div>
                                                    <div class="separator separator-dashed my-3"></div>
													<!--end::Content-->

													<!--begin::Footer-->
													<div class="d-flex flex-stack flex-wrapr">

											 <!--begin::Users-->
                                             <div class="symbol-group symbol-hover mb-3 col-md-3">

                                             <?php 

$total_tarefa_presencial_realizada = 0; // Inicialize a variável fora do bloco try

if( $r['ciclo_leitura']=='1' || $r['ciclo_leitura']=='2' || $r['ciclo_leitura']=='0' ){
   
   $id_periodo_tarefa_leitura = $r['id_periodo_ponto'];
    try {
        $stmt = $conexao->prepare("SELECT COUNT(id_periodo_ponto) as total_tarefa_presencial_realizada,data_cadastro_checkin  FROM checkin WHERE id_periodo_ponto = :id_periodo");
    $stmt->bindParam(':id_periodo', $id_periodo_tarefa_leitura, PDO::PARAM_INT);
    $stmt->execute();
    
       
        // Agora, $total_tarefa_realizada contém o número total de vezes que o id_periodo_ponto aparece.
   
        if ($stmt !== false) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_tarefa_presencial_realizada = $row['total_tarefa_presencial_realizada'];
            if( $r['ciclo_leitura']=='0' &&  $total_tarefa_presencial_realizada > 0 ){
            // Converte o timestamp para o formato de data brasileiro
            $data_cadastro_checkin = new DateTime($row['data_cadastro_checkin']);
            $data_realizacao = $data_cadastro_checkin->format('d/m/Y H:i');
            echo "<br><span class='badge badge-light-success fs-9 badge-square badge-sm'>Executada: $data_realizacao</span>";
            $andamento_tarefa = 'Tarefa Realizada';
            echo "<br><span class='badge badge-light-success badge-square badge-sm'>$andamento_tarefa</span>";

            }elseif($r['ciclo_leitura']=='1' || $r['ciclo_leitura']=='2' &&  $total_tarefa_presencial_realizada > 0 ){
                // Converte o timestamp para o formato de data brasileiro
               
                $andamento_tarefa = 'Tarefa em Realização';
                echo "<br><span class='badge badge-light-success fs-9 badge-square badge-sm'>$andamento_tarefa</span>";
            }
            
        } else {
            $andamento_tarefa = 'Tarefa ainda não realizada';
            echo "<br><span class='badge badge-light-warning fs-9 badge-square badge-sm'>$andamento_tarefa</span>";
            $data_realizacao = 'Aguardando';  // ou você pode definir um valor padrão
            echo "<br><span class='badge badge-light-warning fs-9 badge-square badge-sm'>$data_realizacao</span>";
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
    


}




?>
 

</div>
<!--end::Users-->

														<!--begin::Stats-->
														<div class="d-flex my-1">
                                                            <!--begin::Stat-->
															<div class="border border-dashed border-gray-300 rounded py-2 px-5 mx-3" data-bs-toggle="tooltip" title="Total desta Tarefa já Realizada">
																<!--begin::Svg Icon | path: icons/duotune/communication/com008.svg-->
																<span class="svg-icon svg-icon-3 text-success">

                                                                <?=$total_tarefa_presencial_realizada;?>

																</span>
																<!--end::Svg Icon-->
															
															</div>
															<!--end::Stat-->
															<!--begin::Stat-->
															<div class="border border-dashed border-gray-300 rounded py-2 px-3" data-bs-toggle="tooltip" title="<?=$texto_status;?>">
																<!--begin::Svg Icon | path: icons/duotune/communication/com008.svg-->
																<span class="svg-icon svg-icon-3">

                                                                <?=$status_tarefa_;?>

																</span>
																<!--end::Svg Icon-->
															
															</div>
															<!--end::Stat-->
															<!--begin::Stat-->
															<div class="border border-dashed border-gray-300 rounded py-2 px-3 ms-3" data-bs-toggle="tooltip" title="Tarefa ID">
																<!--begin::Svg Icon | path: icons/duotune/communication/com012.svg-->
																<span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M16.0077 19.2901L12.9293 17.5311C12.3487 17.1993 11.6407 17.1796 11.0426 17.4787L6.89443 19.5528C5.56462 20.2177 4 19.2507 4 17.7639V5C4 3.89543 4.89543 3 6 3H17C18.1046 3 19 3.89543 19 5V17.5536C19 19.0893 17.341 20.052 16.0077 19.2901Z" fill="currentColor"/>
</svg>
																</span>
																<!--end::Svg Icon-->
																<span class="ms-1 fs-7 fw-bold text-gray-600"><?=$r['id_periodo_ponto'];?></span>
															</div>
															<!--end::Stat-->
														</div>
														<!--end::Stats-->
													</div>
													<!--end::Footer-->
												</div>
												<!--end::Card body-->
											</div>
											<!--end::Card-->
                                            <?php } } ?>  
										
										</div>
										<!--end::Col-->
										<!--begin::Col-->
										<div class="col-md-4 col-lg-12 col-xl-4">
											<!--begin::Col header-->
											<div class="mb-9">
												<div class="d-flex flex-stack">
													<div class="fw-bold fs-4">Tarefa Agendada <small>|Ou Delegada</small>

                                                    

                                                    <?php
                                    // consultar Tarefa Delegada
                                    $sql = $conexao->query("SELECT COUNT(DISTINCT pr.id_periodo_ponto) as Total_Tarefa_Agendada
                                                FROM periodo_ponto pr
                                                LEFT JOIN estacoes e ON e.id_estacao = pr.id_estacao 
                                              
                                              WHERE pr.tipo_checkin LIKE '%tarefa_agendada%' AND pr.id_obra='$projeto_id' 
                                                ");

                                    $r = $sql->rowCount();


                                    if ($r > 0) {

                                        $rtow = $sql->fetch(PDO::FETCH_ASSOC);

                                        $total = $rtow['Total_Tarefa_Agendada'];

                                        echo '<span class="badge badge-light-primary">'.$total.'</span>';
                                    } else {

                                        echo '<span class="badge badge-light-warning">0</span>';
                                    }
                                    ?></div>
												
												</div>
												<div class="h-3px w-100 bg-primary"></div>
											</div>
									<!--end::Col header-->

   <?php 
   
   $sql_tarefa_leitura = $conexao->query("SELECT pr.*, e.*,pt.*,u.nome, prt.*
                                            FROM periodo_ponto pr 
                                            INNER JOIN usuarios u ON u.id = pr.usuario_tarefa
                                            LEFT JOIN 
                                            estacoes e ON e.id_estacao = pr.id_estacao 
                                            LEFT JOIN
                                            pontos_estacao pt On pt.id_ponto = pr.id_ponto
                                            LEFT JOIN
                                            parametros_ponto prt ON prt.id_parametro = pr.id_parametro
                                            WHERE pr.id_obra='$projeto_id' 
                                            AND pr.tipo_checkin 
                                            LIKE '%tarefa_agendada%'   
                                          
                                            GROUP BY pr.id_periodo_ponto 
                                            ORDER BY pr.id_periodo_ponto DESC
 ");
    $retorno = $sql_tarefa_leitura->rowCount();


    if ($retorno > 0) {

        $row = $sql_tarefa_leitura->fetchALL(PDO::FETCH_ASSOC);

        foreach($row as $r){

            $titulo_tarefa = $r['titulo_tarefa'] ? $r['titulo_tarefa'] : 'Título Ausente';

            $detalhes_tarefa = $r['detalhes_tarefa'] ? $r['detalhes_tarefa'] : 'Detalhe Ausente';

            $agenda = $r['modo_checkin_periodo'] ? $r['modo_checkin_periodo'] : '';

            $horario_leitura = $r['hora_leitura'] ? $r['hora_leitura'] : '';

    
            $usuario_tarefa = $r['nome'] ? $r['nome'] : 'Tarefa não Delegada!';

          
            switch ($agenda) {

                case "1":
                    $agendamento = "Horário Livre";
                    break;
                case "2":
                    $agendamento ="Leitura Agendada para às &nbsp <span class='text-primary'>".substr("$horario_leitura", 0,-3 ).' </span>';
                    break;                    
                default:
                $agendamento = "Agendamento não Informado.";

            }

            $recorr = $r['ciclo_leitura'];
            switch ($recorr) {

                case "0":
                    $recorrencia = "Única";
                    break;
                case "1":
                    $recorrencia ="Diária";
                    break;
                case "2":
                    $recorrencia =" Semanal";
                    break;                                           
                default:
                $recorrencia = "Recorrência não Informada.";

            }
            $status_tarefa = $r['status_periodo'];
            switch ($status_tarefa) {

                case "1":
                    $status_tarefa_ = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"/>
                    <path d="M10.4343 12.4343L8.75 10.75C8.33579 10.3358 7.66421 10.3358 7.25 10.75C6.83579 11.1642 6.83579 11.8358 7.25 12.25L10.2929 15.2929C10.6834 15.6834 11.3166 15.6834 11.7071 15.2929L17.25 9.75C17.6642 9.33579 17.6642 8.66421 17.25 8.25C16.8358 7.83579 16.1642 7.83579 15.75 8.25L11.5657 12.4343C11.2533 12.7467 10.7467 12.7467 10.4343 12.4343Z" fill="currentColor"/>
                    </svg>';
                    $texto_status ='Tarefa Ativa';
                    break;
                case "2":
                    $status_tarefa_ ='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"/>
                    <rect x="11" y="17" width="7" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"/>
                    <rect x="11" y="9" width="2" height="2" rx="1" transform="rotate(-90 11 9)" fill="currentColor"/>
                    </svg>';
                    $texto_status ='Tarefa Inativa';
                    break;                    
                default:
                $status_tarefa_ ='<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                 <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"/>
                    <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
                    <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
                    <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
                </g>
            </svg>';
            $texto_status ='Status Tarefa';

            }


$plcode = $r['nome_ponto'] ?? 'Ponto não Informado';

$id_periodo = $r['id_periodo_ponto'] ?? 'Período não Informado';

$indicadores = $r['nome_parametro'] ?? 'Indicador não Informado';

$nucleo = $r['nome_estacao'] ?? 'Núcleo não Informado';


$dias_semana='';

if( $recorr=='2'){ //caso o ciclo seja semanal, lista os dias da semana:

$sql_dias = $conexao->query("SELECT * FROM periodo_dia_ponto pd
INNER JOIN dia_semana d ON d.representa_php= pd.dia_semana WHERE pd.id_periodo_ponto ='$id_periodo ' GROUP BY d.nome_dia_semana " );

$row_dias= $sql_dias->fetchALL(PDO::FETCH_ASSOC);
foreach( $row_dias as $rd){

   

    $dias_semana .=  '<div class="d-flex flex-stack mb-1 p-1"><span class="badge badge-dark badge-sm">'.mb_strtoupper($rd['nome_dia_semana']).'</span></div> '; 
}

}


           

   ?>                                         
											<!--begin::Card-->
											<div class="card mb-6 mb-xl-9">
												<!--begin::Card body-->
												<div class="card-body">
													<!--begin::Header-->
													<div class="d-flex flex-stack mb-3">
														<!--begin::Badge-->
														                  
                                                        
<!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
<span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/>
<path d="M12.0006 11.1542C13.1434 11.1542 14.0777 10.22 14.0777 9.0771C14.0777 7.93424 13.1434 7 12.0006 7C10.8577 7 9.92348 7.93424 9.92348 9.0771C9.92348 10.22 10.8577 11.1542 12.0006 11.1542Z" fill="currentColor"/>
<path d="M15.5652 13.814C15.5108 13.6779 15.4382 13.551 15.3566 13.4331C14.9393 12.8163 14.2954 12.4081 13.5697 12.3083C13.479 12.2993 13.3793 12.3174 13.3067 12.3718C12.9257 12.653 12.4722 12.7981 12.0006 12.7981C11.5289 12.7981 11.0754 12.653 10.6944 12.3718C10.6219 12.3174 10.5221 12.2902 10.4314 12.3083C9.70578 12.4081 9.05272 12.8163 8.64456 13.4331C8.56293 13.551 8.49036 13.687 8.43595 13.814C8.40875 13.8684 8.41781 13.9319 8.44502 13.9864C8.51759 14.1133 8.60828 14.2403 8.68991 14.3492C8.81689 14.5215 8.95295 14.6757 9.10715 14.8208C9.23413 14.9478 9.37925 15.0657 9.52439 15.1836C10.2409 15.7188 11.1026 15.9999 11.9915 15.9999C12.8804 15.9999 13.7421 15.7188 14.4586 15.1836C14.6038 15.0748 14.7489 14.9478 14.8759 14.8208C15.021 14.6757 15.1661 14.5215 15.2931 14.3492C15.3838 14.2312 15.4655 14.1133 15.538 13.9864C15.5833 13.9319 15.5924 13.8684 15.5652 13.814Z" fill="currentColor"/>
</svg>
</span>
												<!--end::Svg Icon-->
														
                                                        
                                                        <button class="btn btn-light-primary me-5" data-bs-toggle="modal" data-bs-target="#kt_modal_edita_target" data-id_projeto_tarefa='<?=$projeto_id; ?>' data-id_tarefa='<?=$r['id_periodo_ponto'];?>'>
                                                        <?php
                                                        
                                                        
                                                        switch ($r['tipo_checkin']) {
                                                            case 'ponto_plcode':
                                                                # code...
                                                                echo mb_strtoupper('Checkin Presencial');
                                                                break;

                                                                case 'ponto_parametro':
                                                                    # code...
                                                                    echo mb_strtoupper('Checkin Leitura');
                                                                    break;

                                                                    case 'tarefa_agendada':
                                                                        # code...
                                                                        echo mb_strtoupper('Tarefa Delegada');
                                                                        break;
                                                            
                                                            default:
                                                                # code...
                                                                echo 'Tipo de Tarefa';
                                                                break;
                                                        }
                                                        
                                                        
                                                        $dias_semana='';
                                                        $recorr = $r['ciclo_leitura'];

                                                        if( $recorr=='2'){ //caso o ciclo seja semanal, lista os dias da semana:
                                                        
                                                        $sql_dias = $conexao->query("SELECT * FROM periodo_dia_ponto pd
                                                        INNER JOIN dia_semana d ON d.representa_php= pd.dia_semana WHERE pd.id_periodo_ponto ='$id_periodo ' GROUP BY d.nome_dia_semana " );
                                                        
                                                        $row_dias= $sql_dias->fetchALL(PDO::FETCH_ASSOC);
                                                        foreach( $row_dias as $rd){
                                                        
                                                           
                                                        
                                                            $dias_semana .=  '<div class="d-flex flex-stack mb-1 p-1"><span class="badge badge-dark badge-sm">'.mb_strtoupper($rd['nome_dia_semana']).'</span></div> '; 
                                                        }
                                                        
                                                        } 
                                                        
                                                        
                                                        ?>

                                                
</button>

														<!--end::Badge-->
														<!--end::Badge-->
													
													</div>
													<!--end::Header-->
                                                    <?php if(!empty($titulo_tarefa)){ ?>
													<!--begin::Title-->
													<div class="mb-2">
														<a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kt_modal_edita_target" data-id_projeto_tarefa='<?=$projeto_id; ?>' data-id_tarefa='<?=$r['id_periodo_ponto'];?>' class="fs-4 fw-bold mb-1 text-gray-900 text-hover-primary"><?=$titulo_tarefa;?></a>
													</div>
													<!--end::Title-->
                                                    <?php } ?>
													<!--begin::Content-->
													<div class="card-body text-light fs-6 text-gray-600">
                                                    <div class="d-flex align-items-center flex-wrap mb-0">
																	<!--begin::Name-->
																	<a href="javascript:;" class="text-primary fw-bold me-6">Detalhes da Tarefa:</a>
																	<!--end::Name-->
																	
																</div>
                                                                <?=$detalhes_tarefa;?> </div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">Agendamento:</div> <span class="text-light fs-6 text-gray-600"><?=$agendamento;?></span> </div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">Recorrência:</div>  <span class="text-light fs-8 text-warning"><?=mb_strtoupper($recorrencia);?></span> <span class="text-light fs-6 text-gray-600"><?php if($dias_semana!=''){ echo $dias_semana;} else {echo 'Não Aplicável';};?></span></div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">Núcleo:</div>  <span class="text-light fs-6 text-gray-600"><?=$nucleo;?></span></div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">PLCode:</div>  <span class="text-light fs-6 text-gray-600"><?=$plcode;?></span></div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                    <div class="d-flex flex-stack"><div class="badge badge-light-primary">Tarefa atribuída à:</div>  <span class="text-light fs-6 text-gray-600"><?=$usuario_tarefa;?></span></div>
                                                    <div class="separator separator-dashed my-3"></div>
                                                   
													<!--end::Content-->

													<!--begin::Footer-->
													<div class="d-flex flex-stack flex-wrapr">
														<!--begin::Users-->
														<div class="symbol-group symbol-hover my-1">
                                                        <?php 

$total_tarefa_agendada_realizada = 0; // Inicialize a variável fora do bloco try

if( $r['ciclo_leitura']=='1' || $r['ciclo_leitura']=='2' || $r['ciclo_leitura']=='0' ){
   
   $id_periodo_tarefa_leitura = $r['id_periodo_ponto'];
    try {
        $stmt = $conexao->prepare("SELECT COUNT(id_periodo_ponto) as total_tarefa_agendada_realizada,data_cadastro_checkin  FROM checkin WHERE id_periodo_ponto = :id_periodo");
    $stmt->bindParam(':id_periodo', $id_periodo_tarefa_leitura, PDO::PARAM_INT);
    $stmt->execute();
    
       
        // Agora, $total_tarefa_realizada contém o número total de vezes que o id_periodo_ponto aparece.
   
        if ($stmt !== false) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_tarefa_agendada_realizada = $row['total_tarefa_agendada_realizada'];
           
            if($r['ciclo_leitura']==0 &&  $total_tarefa_agendada_realizada > 0 ){
            // Converte o timestamp para o formato de data brasileiro
            $data_cadastro_checkin = new DateTime($row['data_cadastro_checkin']);
            $data_realizacao = $data_cadastro_checkin->format('d/m/Y H:i');
            echo "<br><span class='badge badge-light-success fs-9 badge-square badge-sm'>Executada: $data_realizacao</span>";
            $andamento_tarefa = 'Tarefa Realizada';
            echo "<br><span class='badge badge-light-success fs-9 badge-square badge-sm'>$andamento_tarefa</span>";

            } elseif($r['ciclo_leitura']=='1' || $r['ciclo_leitura']=='2' &&  $total_tarefa_agendada_realizada > 0 ){
                // Converte o timestamp para o formato de data brasileiro
               
                $andamento_tarefa = 'Tarefa em Realização';
                echo "<br><span class='badge badge-light-success fs-9 badge-square badge-sm'>$andamento_tarefa</span>";
            }
          
        } else {
            $andamento_tarefa = 'Tarefa ainda não realizada';
            echo "<br><span class='badge badge-light-warning fs-9'>$andamento_tarefa</span>";
            $data_realizacao = 'Aguardando';  // ou você pode definir um valor padrão
            echo "<br><span class='badge badge-light-warning fs-9 badge-square badge-sm'>$data_realizacao</span>";
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
    


}




?>

														</div>
														<!--end::Users-->

														<!--begin::Stats-->
														<div class="d-flex my-1">
                                                            <!--begin::Stat-->
															<div class="border border-dashed border-gray-300 rounded py-2 px-5 mx-3" data-bs-toggle="tooltip" title="Total desta Tarefa já Realizada">
																<!--begin::Svg Icon | path: icons/duotune/communication/com008.svg-->
																<span class="svg-icon svg-icon-3 text-success ">

                                                                <?=$total_tarefa_agendada_realizada;?>

																</span>
																<!--end::Svg Icon-->
															
															</div>
															<!--end::Stat-->
															<!--begin::Stat-->
															<div class="border border-dashed border-gray-300 rounded py-2 px-3" data-bs-toggle="tooltip" title="<?=$texto_status;?>">
																<!--begin::Svg Icon | path: icons/duotune/communication/com008.svg-->
																<span class="svg-icon svg-icon-3">

                                                                <?=$status_tarefa_;?>

																</span>
																<!--end::Svg Icon-->
															
															</div>
															<!--end::Stat-->
															<!--begin::Stat-->
															<div class="border border-dashed border-gray-300 rounded py-2 px-3 ms-3" data-bs-toggle="tooltip" title="Tarefa ID">
																<!--begin::Svg Icon | path: icons/duotune/communication/com012.svg-->
																<span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M16.0077 19.2901L12.9293 17.5311C12.3487 17.1993 11.6407 17.1796 11.0426 17.4787L6.89443 19.5528C5.56462 20.2177 4 19.2507 4 17.7639V5C4 3.89543 4.89543 3 6 3H17C18.1046 3 19 3.89543 19 5V17.5536C19 19.0893 17.341 20.052 16.0077 19.2901Z" fill="currentColor"/>
</svg>
																</span>
																<!--end::Svg Icon-->
																<span class="ms-1 fs-7 fw-bold text-gray-600"><?=$r['id_periodo_ponto'];?></span>
															</div>
															<!--end::Stat-->
														</div>
														<!--end::Stats-->
													</div>
													<!--end::Footer-->


												</div>
												<!--end::Card body-->
											</div>
											<!--end::Card-->
                                            <?php } } ?>  
									
										</div>
										<!--end::Col-->
									</div>
									<!--end::Row-->
								</div>
								<!--end::Tab pane-->
								<!--begin::Tab pane-->
								<div id="kt_project_targets_table_pane" class="tab-pane fade">
									<div class="card card-flush">
										<div class="card-body pt-3">
											<!--begin::Table-->
											<table id="kt_profile_overview_table" class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
												<!--begin::Table head-->
												<thead class="fs-7 text-gray-400 text-uppercase">
													<tr>
														<th class="min-w-250px">Tarefa</th>
														<th class="min-w-90px">Núcleo</th>
														<th class="min-w-150px">Data Realização</th>
														<th class="min-w-90px">Membros</th>
														<th class="min-w-90px">Recorrência</th>
														<th class="min-w-50px"></th>
													</tr>
												</thead>
												<!--end::Table head-->
												<!--begin::Table body-->
												<tbody class="fs-6">

                                                <?php
                                    // consultar Checkin por Leitura
                                    $sql = $conexao->query("SELECT COUNT(DISTINCT pr.id_periodo_ponto) as Total_Tarefa_Leitura
                                                FROM periodo_ponto pr
                                                INNER JOIN estacoes e ON e.id_estacao = pr.id_estacao 
                                              
                                                WHERE pr.tipo_checkin LIKE '%ponto_parametro%' AND pr.id_obra='$projeto_id'
                                                ");

                                    $r = $sql->rowCount();


                                    if ($r > 0) {

                                        $rtow = $sql->fetch(PDO::FETCH_ASSOC);

                                        $total = $rtow['Total_Tarefa_Leitura'];

                                        echo '<span class="fs-6 text-gray-400 ms-2">'.$total.'</span>';
                                    } else {

                                        echo '<span class="fs-6 text-gray-400 ms-2">0</span>';
                                    }
                                    ?>
													</div>
											
												</div>
												<div class="h-3px w-100 bg-warning"></div>
											</div>
											<!--end::Col header-->

   <?php 
   
   $sql_tarefa_leitura = $conexao->query("SELECT * 
                                            FROM periodo_ponto pr 
                                            INNER JOIN 
                                            estacoes e ON e.id_estacao = pr.id_estacao 
                                            LEFT JOIN
                                            pontos_estacao pt On pt.id_estacao = pr.id_estacao
                                            LEFT JOIN
                                            parametros_ponto prt ON prt.id_ponto = pr.id_ponto                                         
                                            WHERE pr.id_obra='$projeto_id' 
                                           
                                          
                                            GROUP BY pr.id_periodo_ponto 
                                            HAVING pr.id_estacao
 ");
    $retorno = $sql_tarefa_leitura->rowCount();


    if ($retorno > 0) {

        $row = $sql_tarefa_leitura->fetchALL(PDO::FETCH_ASSOC);

        foreach($row as $r){


            $titulo_tarefa = $r['titulo_tarefa'] ? $r['titulo_tarefa'] : 'Título Ausente';

            $detalhes_tarefa = $r['detalhes_tarefa'] ? $r['detalhes_tarefa'] : 'Detalhe Ausente';

            $agenda = $r['modo_checkin_periodo'] ? $r['modo_checkin_periodo'] : '';

            $horario_leitura = $r['hora_leitura'] ? $r['hora_leitura'] : '';
            switch ($agenda) {

                case "1":
                    $agendamento = "Horário Livre";
                    break;
                case "2":
                    $agendamento ="Leitura Agendada para às &nbsp <span class='text-primary'>".substr("$horario_leitura", 0,-3).' </span>';
                    break;                    
                default:
                $agendamento = "Agendamento não Informado.";

            }

            $recorr = $r['ciclo_leitura'];
            switch ($recorr) {

                case "0":
                    $recorrencia = "Única";
                    break;
                case "1":
                    $recorrencia ="Diária";
                    break;
                case "2":
                    $recorrencia ="Semanal";
                    break;                                           
                default:
                $recorrencia = "Recorrência não Informada.";

            }
            $status_tarefa = $r['status_periodo'];
            switch ($status_tarefa) {

                case "1":
                    $status_tarefa_ = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"/>
                    <path d="M10.4343 12.4343L8.75 10.75C8.33579 10.3358 7.66421 10.3358 7.25 10.75C6.83579 11.1642 6.83579 11.8358 7.25 12.25L10.2929 15.2929C10.6834 15.6834 11.3166 15.6834 11.7071 15.2929L17.25 9.75C17.6642 9.33579 17.6642 8.66421 17.25 8.25C16.8358 7.83579 16.1642 7.83579 15.75 8.25L11.5657 12.4343C11.2533 12.7467 10.7467 12.7467 10.4343 12.4343Z" fill="currentColor"/>
                    </svg>';
                    $texto_status ='Tarefa Ativa';
                    break;
                case "2":
                    $status_tarefa_ ='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"/>
                    <rect x="11" y="17" width="7" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"/>
                    <rect x="11" y="9" width="2" height="2" rx="1" transform="rotate(-90 11 9)" fill="currentColor"/>
                    </svg>';
                    $texto_status ='Tarefa Inativa';
                    break;                    
                default:
                $status_tarefa_ ='<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                 <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"/>
                    <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
                    <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
                    <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
                </g>
            </svg>';
            $texto_status ='Status Tarefa';

            }


$plcode = $r['nome_ponto'];

$indicadores = $r['nome_parametro'];




           

   ?>   
													<!--begin::Table row-->
													<tr>
														<td class="fw-bold">
															<a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kt_modal_edita_target"  data-id_projeto_tarefa='<?=$projeto_id; ?>' data-id_tarefa='<?=$r['id_periodo_ponto'];?>' class="text-gray-900 text-hover-primary"> <?=$titulo_tarefa;?> | <?=$plcode;?> <?=$indicadores;?></a>
														</td>
														<td>
															<span class="badge badge-light fw-semibold me-auto"><?=$r['nome_estacao'];?></span>
														</td>
														<td><?=$agendamento;?></td>
														<td>
														 <!--begin::Users-->
                                                <div class="symbol-group symbol-hover mb-3">

                                                    <?php

$sql_conta_colab = $conexao->query("SELECT COUNT(DISTINCT ch.id_colaborador ) as Total_usuarios,
u.id, u.nome, u.foto, u.email, u.nivel
FROM checkin ch 
LEFT JOIN rmm r ON r.id_rmm = ch.id_rmm
INNER JOIN pontos_estacao pt On pt.id_estacao = ch.id_estacao

INNER JOIN usuarios u ON u.id = ch.id_colaborador 

WHERE u.status='1' AND pt.id_obra='$projeto_id' AND ch.data_cadastro_checkin  > '2022-04-01' GROUP BY u.id
                                   

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
                                                        <a href="javascript:;" class="symbol symbol-35px symbol-circle" data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                                                            <span class="symbol-label bg-dark text-inverse-dark fs-8 fw-bold" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Ver mais Usuários">+<?= $Total_usuarios; ?></span>
                                                        </a>
                                                        <!--end::All users-->
                                                    <?php     
} else {

    echo '<span class="badge badge-light-warning">Tarefa ainda não realizada.</span>';

} ?>
                                                </div>
                                                <!--end::Users-->
														</td>
														<td>
															<span class="badge badge-light-primary fw-bold me-auto"><?=$recorrencia;?> </span>
														</td>
														<td class="text-end">
															<a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kt_modal_edita_target"  data-id_projeto_tarefa='<?=$projeto_id; ?>' data-id_tarefa='<?=$r['id_periodo_ponto'];?>'  class="btn btn-bg-light btn-active-color-primary btn-sm">Ver</a>
														</td>
													</tr>
													<!--end::Table row-->
 <?php } }?>												
                                                    
												</tbody>
												<!--end::Table body-->
											</table>
											<!--end::Table-->
										</div>
									</div>
								</div>
								<!--end::Tab pane-->
							</div>
							<!--end::Tab Content-->
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
                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor" />
                    <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Scrolltop-->
        <!--begin::Modals-->


        

          <!--begin::Modal - Tarefas -->


          <!--inicio::Modal - Nova Tarefa-->      
<div class="modal fade" id="modal_nova_tarefa" tabindex="-1" aria-hidden="true" role="dialog" data-bs-backdrop="static">
<!--begin::Modal dialog-->
<div class="modal-dialog modal-dialog-centered mw-650px" id='conteudo_modal_dinamico'>
    <!--begin::Modal content-->
   <!--begin::Page loading(append to body)-->
        <div class="alert alert-primary d-flex align-items-center p-5 mb-10" id="aguardar_modal_carregar">
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
<!--end::Modal - Nova Tarefa-->

       
  <!--inicio::Modal - Edita Tarefa-->      
<div class="modal fade" id="kt_modal_edita_target" tabindex="-1" aria-hidden="true" role="dialog" data-bs-backdrop="static">
<!--begin::Modal dialog-->
<div class="modal-dialog modal-dialog-centered mw-650px" id='edita_conteudo_modal_dinamico'>
    <!--begin::Modal content-->
   <!--begin::Page loading(append to body)-->
        <div class="alert alert-primary d-flex align-items-center p-5 mb-10" id="edita_aguardar_modal_carregar">
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
<!--end::Modal - Edita Tarefa-->


 

       
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
         <?php include_once "../../../views/cockpit/modal-app-cockpit.php"; ?>
        <!--end::Modal - Create App Cockpit-->

         <!--begin::Modal - View Users-->
         <?php include_once "../../../views/usuarios/modal-view-users.php"; ?>
        <!--end::Modal - View Users-->

            <!--begin::Activities drawer-->
    <?php include '../../../views/conta-usuario/atividade-usuario.php'; ?>
    <!--end::Activities drawer-->
    
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
       
        <script src="../../node_modules/print-js/dist/print.js"></script>
        <script src="../../js/dashboard/step-js.js"></script>
        <script src="assets/js/widgets.bundle.js"></script>
        <script src="assets/js/custom/widgets.js"></script>
        <script src="../../js/suportes/chat/chat.js"></script>
        <script src="../../js/usuarios/users-search.js"></script>
       
        <script src="../../js/cockpit/create-cockpit.js"></script>
        <!--end::Custom Javascript-->


        <script src="assets/plugins/custom/draggable/draggable.bundle.js"></script>

        <script src="assets/js/custom/documentation/general/draggable/multiple-containers.js"></script>
        
        <script>



const  Modal_Edita_Tarefa  = document.getElementById('kt_modal_edita_target');


Modal_Edita_Tarefa.addEventListener('shown.bs.modal', function (e) {

    e.preventDefault();

    var button = $(e.relatedTarget);

    var recipientId    = button.data('id_tarefa'); 
    
    var id_projeto_tarefa = button.data('id_projeto_tarefa');

    var modal = $(this);

    //modal.find('#minhaId').html(recipientId);


    $.ajax({
        type: 'POST',
        url: '../../views/projetos/tarefas/modal-edita-tarefas.php',
        dataType: 'html',
        data: {
            id: recipientId,
            id_projeto_tarefa: id_projeto_tarefa
        },
        beforeSend: function(){
            $("#edita_aguardar_modal_carregar" ).removeClass("d-none");
        },
        success: function(retorno){

            $("#edita_aguardar_modal_carregar" ).addClass("d-none");



$("#edita_conteudo_modal_dinamico" ).html(retorno);
        },
        error: function(){
            alert("Falha ao coletar dados !!!");
        }
    });

    	
    //$("#conteudo_modal_dinamico" ).load( "../../views/projetos/modal-edita-projeto.php?id="+recipientId );
 
 

})



Modal_Edita_Tarefa.addEventListener('hidden.bs.modal', function (e) {

    location.reload();
    
})  
    
    
    </script>
        <!--end::Javascript-->
    </body>
    <!--end::Body-->

    </html>