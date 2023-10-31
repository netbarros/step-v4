<?php
 // buffer de saída de dados do php]
// Instancia Conexão PDO
require_once "../../../conexao.php";
$conexao = Conexao::getInstance();
require_once "../../../crud/login/verifica_sessao.php";



$projeto_id = isset($_GET['id']) && is_numeric($_GET['id']) 
                ? intval($_GET['id']) 
                : $_COOKIE['projeto_atual'];



$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';


$_SESSION['pagina_atual'] = ($nivel_acesso_user_sessao == 'engenheiro') ? 'PLC do Projeto' : 'Núcleos do Projeto';

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



if($nivel_acesso_user_sessao=='cliente'){
    $_SESSION['error'] = "Falha ao Acessar a Página de Projetos, seu Nível de acesso não permite!";
    header("Location: /views/dashboard.php");
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
                                            <a class="nav-link text-active-primary py-5 me-6" href="../../views/projetos/tarefas/tarefas.php?id=<?php echo $projeto_id;?>">Tarefas</a>
                                        </li>
                                        <!--end::Nav item-->

                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 active" href="../../views/projetos/nucleos/nucleos.php?id=<?php echo $projeto_id;?>"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'PLC';}else{ echo 'Núcleos';}    ?></a>
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
                                            <a class="nav-link text-active-primary py-5 me-6" href="../../views/projetos/orcamento.php?id=<?php echo $projeto_id;?>">Orçamentos</a>
                                        </li>
                                        end::ABA Orçamentos-->

                                    </ul>
                                    <!--end::Nav-->
                                </div>
                            </div>
                            <!--end::Navbar-->
                 
                            <!--begin::Table-->
                            <div class="card card-flush mt-6 mt-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header mt-5">
                                    <!--begin::Card title-->
                                    <div class="card-title flex-column">


                                        <h3 class="fw-bold mb-1"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'PLC Implementados no Projeto';}else{ echo 'Núcleos Implementados no Projeto';}    ?></h3>

                                        <?php

                                        // total de leituras realizadas

                                        $conta_leitura_projeto = $conexao->query("SELECT COUNT(DISTINCT r.id_rmm) as Total_Leitura
                                        FROM rmm r
                                        LEFT JOIN pontos_estacao pt ON pt.id_ponto = r.id_ponto                                      
                                        WHERE pt.id_obra='$projeto_id'
                                        
                                        ");

                                        $conta = $conta_leitura_projeto->rowCount();

                                        if($conta>0){

                                            $r=$conta_leitura_projeto->fetch(PDO::FETCH_ASSOC);

                                            $total=$r['Total_Leitura'];
                                           $total_leitura =  number_format($total, 0, '', '.');

                                            echo '<div class="fs-6 text-gray-400">Total '.$total_leitura.' Leituras Monitoradas</div>';

                                        }else{

                                            echo '<div class="fs-6 text-gray-400">Nenhuma Leitura Encontrada para este Projeto.</div>';

                                        }

                                        ?>
                                       
                                    </div>
                                    <!--begin::Card title-->
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
<a href="javascript:;" class="btn btn-primary btn-flex h-40px border-0 fw-bold px-4 px-lg-6 ms-2 ms-lg-3" data-bs-toggle="modal" data-id='<?=$projeto_id;?>' data-nome='<?=$nome_obra;?>' id='abre_modal_novo_nucleo' data-bs-target="#kt_modal_novo_nucleo"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Novo PLC';}else{ echo 'Novo Núcleo';}?></a>
</div>
                               
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-0">
                                    <!--begin::Table container-->
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table id="tabela_nucleos" class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
                                            <!--begin::Head-->
                                            <thead class="fs-7 text-gray-400 text-uppercase">
                                                <tr>
                                                    <th class="min-w-150px"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'PLC';}else{ echo 'Núcleos';}?> </th>
                                                    <th class="min-w-90px"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Instrumentos';}else{ echo 'PlCode';}?> </th>
                                                    <th class="min-w-90px"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Sensores IoT';}else{ echo 'Indicadores';}?></th>
                                                    <th class="min-w-90px">Status</th>
                                                    <th class="min-w-50px text-end">Detalhes</th>
                                                </tr>
                                            </thead>
                                            <!--end::Head-->
                                            <!--begin::Body-->
                                            <tbody class="fs-6">

                                            <?php

//estacoes

$sql_estacoes = $conexao->query("SELECT e.nome_estacao,
 e.id_estacao,
  o.nome_obra, 
  o.id_obra,
   e.status_estacao,
   e.latitude,
    e.longitude
FROM estacoes e
INNER JOIN obras o On o.id_obra = e.id_obra
INNER JOIN clientes c ON c.id_cliente = o.id_cliente

WHERE o.id_obra='$projeto_id'
                                      

 ORDER BY e.nome_estacao ASC
");



$r=$sql_estacoes->rowCount();
if($r>0){

    $row = $sql_estacoes->fetchALL(PDO::FETCH_ASSOC);

    foreach($row as $r){
        
        $brev_nome_projeto = substr($r['nome_obra'], 0, 1);

      

        $status_cadastro = $r['status_estacao'];

        if($status_cadastro=='1'){

            $status='ativa';
            $css_status='success';
        }
        if ($status_cadastro=='2'){

            $status='em alerta';
            $css_status='danger';

        } 
        
        if ($status_cadastro=='3'){

            $status='inativa';
            $css_status='dark';

        }

// conta plcode estacao
        $sql_conta_plcode = $conexao->query("SELECT COUNT(DISTINCT r.id_ponto) as Total_Plcode FROM rmm r 
        INNER JOIN pontos_estacao pt ON pt.id_ponto=r.id_ponto
         WHERE pt.id_estacao='$r[id_estacao]'");

         $rPt=$sql_conta_plcode->rowCount();
         if($rPt>0){

            $rPtL = $sql_conta_plcode->fetch(PDO::FETCH_ASSOC);
            $total_plcode = $rPtL['Total_Plcode'];
         } else{

            $total_plcode ='0';

         }

// conta indicadores estacao

$sql_conta_indicador = $conexao->query("SELECT COUNT(DISTINCT r.id_parametro) as total_indicador FROM rmm r 
INNER JOIN pontos_estacao pt ON pt.id_ponto=r.id_ponto
 WHERE pt.id_estacao='$r[id_estacao]'");

 $rIt=$sql_conta_indicador->rowCount();
 if($rIt>0){

    $rItL = $sql_conta_indicador->fetch(PDO::FETCH_ASSOC);
    $total_indicador = $rItL['total_indicador'];
 } else{

    $total_indicador ='0';

 }



/*  // conta Iot estacao*/ 
$sql_conta_iot = $conexao->query("SELECT COUNT(DISTINCT pr.id_parametro) as Total_Iot FROM parametros_ponto  pr
INNER JOIN pontos_estacao pt ON pt.id_ponto=pr.id_ponto
 WHERE pt.id_obra='$projeto_id' AND pr.id_sensor_iot IS NOT NULL");

 $rIot=$sql_conta_iot->rowCount();
 if($rIot>0){

    $rIot = $sql_conta_iot->fetch(PDO::FETCH_ASSOC);
    $total_iot = $rIot['Total_Iot'];
 } else{

    $total_iot ='0';

 } 
?>
                                               
                                               <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <span class="symbol-label bg-light-<?=$css_status;?> text-<?=$css_status;?> fw-semibold"><?=$brev_nome_projeto;?></span>
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::Online-->
                                                                <div class="bg-success position-absolute h-8px w-8px rounded-circle translate-middle start-100 top-100 ms-n1 mt-n1"></div>
                                                                <!--end::Online-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kt_modal_edita_nucleo" data-id='<?=$r['id_estacao'];?>' class="fs-6 text-gray-800 text-hover-<?=$css_status;?>"><?=$r['nome_estacao'];?></a>
                                                                <div class="fw-semibold text-gray-400"><?=$r['nome_obra'];?> </div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                  <!--   <td>
                                                    
                                                    <span class="badge badge-light-primary fw-bold px-4 py-3"><?=$total_iot;?> </span>   
                                                                    </td> -->
                                                    
                                                                 
                                                    <td>
                                                        <span class="badge badge-light-info fw-bold px-4 py-3"><?=$total_plcode;?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light-warning fw-bold px-4 py-3"><?=$total_indicador;?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light-<?=$css_status;?> fw-bold px-4 py-3"><?=$status;?></span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kt_modal_edita_nucleo" data-id='<?=$r['id_estacao'];?>' class="btn btn-light btn-sm"> Ver</a>
                                                    </td>
                                                </tr>

<?php

    } 
}
?>
                                                

                                            </tbody>
                                            <!--end::Body-->
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Table container-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
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
                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor" />
                    <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Scrolltop-->
        <!--begin::Modals-->

        <!--begin::Modal - Núcleos -->
        <?php require "../../../views/projetos/nucleos/modal-novo-nucleo.php"; ?>
      
        <!--end::Modal - Núcleos -->

 <!--begin::Modal - Create App Cockpit-->
 <?php include_once "../../../views/cockpit/modal-app-cockpit.php"; ?>
        <!--end::Modal - Create App Cockpit-->

 



        
  <!--inicio::Modal - Edita Nucleo-->      
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
<!--end::Modal - Edita Nucleo-->


  <!--inicio::Modal - Edita Nucleo-->      
<div class="modal fade" id="kt_modal_edita_nucleo" tabindex="-1" aria-hidden="true" role="dialog" data-bs-backdrop="static">
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
<!--end::Modal - Edita Nucleo-->




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
        <script src="../../js/dashboard/step-js.js"></script>
        <script src="../../node_modules/print-js/dist/print.js"></script>
       
        <script src="assets/js/widgets.bundle.js"></script>
        <script src="assets/js/custom/widgets.js"></script>
        <script src="../../js/suportes/chat/chat.js"></script>
        <script src="../../js/usuarios/users-search.js"></script>
        <script src="../../js/tarefas/controladores.js"></script>
        <script src="../../js/tarefas/nova-tarefa.js"></script>
        <script src="../../js/nucleos/novo-nucleo.js"></script>

        <script src="../../js/cockpit/create-cockpit.js"></script>
     
        <script src="../../js/nucleos/tabela-nucleos.js"></script>
        <!--end::Custom Javascript-->
     
        <script src="assets/plugins/custom/draggable/draggable.bundle.js"></script>

        <script src="assets/js/custom/documentation/general/draggable/multiple-containers.js"></script>

        
        <!--end::Javascript  AIzaSyAQsOKlWz3MbMeQHMrfAEtVR7ajrSj9274Y -->
        <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

        <script>

var myModal = document.getElementById('kt_modal_edita_nucleo');


myModal.addEventListener('shown.bs.modal', function (event) {



    var button = $(event.relatedTarget);

    var recipientId    = button.data('id');    

    var modal = $(this);

    //modal.find('#minhaId').html(recipientId);


    $.ajax({
        type: 'POST',
        url: '../../views/projetos/nucleos/modal-edita-nucleo.php',
        dataType: 'html',
        data: {
            id: recipientId 
        },
        beforeSend: function(){
            $("#aguardar_modal_carregar" ).removeClass("d-none");
        },
        success: function(retorno){

            $("#aguardar_modal_carregar" ).addClass("d-none");



$("#conteudo_modal_dinamico" ).html(retorno);
        },
        error: function(){
            alert("Falha ao coletar dados !!!");
        }
    });

    	
    //$("#conteudo_modal_dinamico" ).load( "../../views/projetos/modal-edita-projeto.php?id="+recipientId );
 
 

})



myModal.addEventListener('hidden.bs.modal', function (event) {

    location.reload();
    
})  
    
    
    </script>







<script>

var myModal_usuarios = document.getElementById('kt_modal_users_search');


myModal_usuarios.addEventListener('shown.bs.modal', function (event) {


    var button = $(event.relatedTarget);

    var recipientId    = button.data('id');    

    var modal = $(this);

    //modal.find('#minhaId').html(recipientId);


    $.ajax({
        type: 'POST',
        url: '../../views/usuarios/modal-user-search.php',
        dataType: 'html',
        data: {
            id: recipientId 
        },
        beforeSend: function(){
            $("#aguardar_modal_carregar_usuarios" ).removeClass("d-none");
        },
        success: function(retorno){

            $("#aguardar_modal_carregar_usuarios" ).addClass("d-none");

            $("#conteudo_modal_dinamico_usuarios" ).html(retorno);
        },
        error: function(){
            alert("Falha ao coletar dados !!!");
        }
    });

    	
    //$("#conteudo_modal_dinamico" ).load( "../../views/projetos/modal-edita-projeto.php?id="+recipientId );
 
 

})



myModal_usuarios.addEventListener('hidden.bs.modal', function (event) {

    location.reload();
    
})  
    
    
    </script>
        
    </body>
    <!--end::Body-->

    </html>