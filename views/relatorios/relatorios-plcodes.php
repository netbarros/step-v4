<?php
 // buffer de saída de dados do php]
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';



$projeto_user = isset($_GET['user']) ? $_GET['user'] : '';


$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';
$id_usuario_sessao = trim(isset($_SESSION['id'])) ? $_SESSION['id'] : '';

$tipo_relatorio = $_GET['tipo_relatorio'] ??'';
$titulo_relatorio = $_GET['titulo_relatorio'] ??'';


$_SESSION['pagina_atual'] = 'Impressão de PLCodes';

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


$sql_personalizado = '';

if ($nivel_acesso_user_sessao == 'supervisor') {

    $sql_personalizado = "WHERE e.supervisor = '$id_BD_Colaborador'  OR up.id_usuario  = '$id_usuario_sessao' ";
}

if ($nivel_acesso_user_sessao == 'ro') {

    $sql_personalizado = "WHERE e.ro = '$id_BD_Colaborador'  OR up.id_usuario ='$id_usuario_sessao'";
}

if ($nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado = "WHERE  up.id_usuario ='$id_usuario_sessao'";
}

if ($nivel_acesso_user_sessao == 'admin') {

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



                            <!--begin::Grafico-->
                            <div class="row g-6 g-xl-12 d-none" id="div_grafico_relatorio">
                               
                               
                                <div class="col-lg-12 col-xxl-12">
                                    <!--begin::Grafico Relatorio-->
                                    
                                        <div class="card card-bordered">
                                            <div class="card-header align-items-center border-0 mt-4">
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="fw-bold mb-2 text-dark">Análise</span>            
                                                    <span class="text-muted fw-semibold fs-7">890,344 Sales</span>
                                                </h3>
                                            </div>
                                                <div class="card-body">
                                                    <div id="kt_apexcharts_5" style="height: 350px;"></div>
                                                </div>
                                        </div>

                                    <!--end::Grafico Relatorio-->
                                </div>


                            </div>
                            <!--end::Grafico-->



<!--begin::Products-->
<div class="card card-flush">
								<!--begin::Card header-->
								<div class="card-header align-items-center py-5 gap-2 gap-md-5">
									<!--begin::Card title-->
									<div class="card-title">
										<!--begin::Buscar-->
										<div class="d-flex align-items-center position-relative my-1">
												<!--begin::Filter-->
										<div class="w-350px"  data-bs-toggle="popover" data-bs-placement="top" title="Selecione o Núcleo de Operação" data-bs-content="Escolha o Núcleo desejado para poder gerar os PLCodes para impressão." >
											<!--begin::Select2-->
											
                                            <?php

                                            // Executa a consulta SQL
$resultado = $conexao->query("SELECT o.nome_obra, e.nome_estacao, e.id_estacao FROM obras o
INNER JOIN estacoes e ON e.id_obra = o.id_obra
LEFT JOIN usuarios_projeto up ON up.id_usuario = $id_usuario_sessao
$sql_personalizado AND o.status_cadastro='1' AND e.status_estacao!='2' GROUP BY e.id_estacao  ORDER BY o.nome_obra ASC;");

// Cria uma matriz associativa de produtos por categoria
$produtos_por_categoria = array();
foreach ($resultado as $produto) {
    $categoria = $produto['nome_obra'];
    unset($produto['nome_obra']);
    $produtos_por_categoria[$categoria][] = $produto;
}

// Cria o elemento select com optgroup
echo '<select class="form-select form-select-solid" data-control="select2" data-hide-search="false" data-allow-clear="true" data-placeholder="Núcleos de Operação" id="estacao_plcode" name="estacao_plcode" >';
echo '<option value="">Selecione o Núcleo Desejado</option>';
foreach ($produtos_por_categoria as $categoria => $produtos) {
    echo '<optgroup label="' . $categoria . '">';
    foreach ($produtos as $produto) {
        echo '<option value="' . $produto['id_estacao'] . '"> ' . strtoupper($produto['nome_estacao']). '</option>';
    }
    echo '</optgroup>';
}
echo '</select>';


                                    ?>                         
  
											</select>
											<!--end::Select2-->
										</div>
										<!--end::Filter-->
										</div>
										<!--end::Buscar-->
									</div>
									<!--end::Card title-->
									<!--begin::Card toolbar-->
									<div class="card-toolbar flex-row-fluid justify-content-end gap-5">
										<!--begin::Daterangepicker-->
										
										<!--end::Daterangepicker-->
									
										<!--begin::Export dropdown-->
										<button type="button" class="btn btn-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
										<!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
										<span class="svg-icon svg-icon-2">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="currentColor" />
												<path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="currentColor" />
												<path opacity="0.3" d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="currentColor" />
											</svg>
										</span>
										<!--end::Svg Icon-->Exportar Relatório</button>
										<!--begin::Menu-->
										<div id="relatorio_leituras_export_menu" class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4" data-kt-menu="true">
											

                                            <!--begin::Menu item--> 
											<div class="menu-item px-3">
												<a href="javascript:;" class="menu-link px-3 gera_relatorio" data-id="listagem_qrcode_impressao" data-titulo="Listagem de PLCodes">Exportar Listagem de PLCode's</a>
											</div>
											<!--end::Menu item-->
											
										</div>
										<!--end::Menu-->
										<!--end::Export dropdown-->
									</div>
									<!--end::Card toolbar-->
								</div>
								<!--end::Card header-->
								<!--begin::Card body-->
								<div class="card-body pt-0" id="listagem_qrcode">

                                <div  class="table table-striped border rounded gy-5 gs-7 d-none" id="loader">

                                    <div class="progress min-h-20px mb-2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated fs-6 bg-primary " style="width: 30%" id="progresso_relatorio">Buscando Dados...</div>
                                    <div class="progress-bar progress-bar-striped progress-bar-animated fs-6 bg-info" style="width: 10%" ><div class="text-gray-900"></div></div>
                                    </div>

                                </div>
                                <div class="flex-row-fluid justify-content-center row g-6 g-xl-12">
                                <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12"  align="center">
                                <h3 class="justify-content-center">Modelo de Impressão</h3>
            </div>
                                
                                <div class="col-md-3 border border-gray-200 border-dashed rounded min-w-100px  py-0 px-0 me-1 mb-2 mw-300px gera_impressao_QRCODE"><div class="card border-0 text-center"><div class="header-logo  flex-grow-1 mh-10px py-2">
                                
      
  <img alt="Logo" src="assets/media/logos/logo-4.png" class="logo-sticky h-35px">

</div><div class="card-body "><span class="badge badge-dark mt-3 ">Projeto Responsável</span><br><span class="badge badge-primary mt-3 py-2"> <span class="text-dark"> Núcleo  
  <span class="svg-icon svg-icon-4 svg-icon-light">
  <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
   <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"></rect>
      <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
      <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
      <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
  </g>
</svg>
  </span></span> de Operação</span><h5 class="card-title mt-3 ">Nome de Identificação</h5><img src="https://chart.googleapis.com/chart?chs=150x150&amp;cht=qr&amp;chl=421"></div></div></div>
                               
          </div>        
								
								</div>
								<!--end::Card body-->
							</div>
							<!--end::Products-->



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






        <!--begin::Modal - Create App Cockpit-->
        <?php include_once "./../../views/cockpit/modal-app-cockpit.php"; ?>
        <!--end::Modal - Create App Cockpit-->
        <!--begin::Modal - Users Buscar-->

        <!--end::Modal - Users Buscar-->


        <div class="modal fade" id="imagemModal" tabindex="-1" aria-labelledby="imagemModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imagemModalLabel">Imagem</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <img src="" class="img-fluid" alt="">
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="kt_modal_edita_cliente" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-900px" id='conteudo_modal_edita_cliente'>
                <!--begin::Modal content-->
                <!--begin::Page loading(append to body)-->
                <div class="alert alert-primary d-flex align-items-center p-5 mb-10" id="aguardar_cliente_carregar">
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
        <!--end::Modals-->

        <!--begin::Javascript-->
        <script>
            var hostUrl = "assets/";
        </script>
        <!--begin::Global Javascript Bundle(used by all pages)-->
        <script src="assets/plugins/global/plugins.bundle.js"></script>
        <script src="assets/js/scripts.bundle.js"></script>
        <!--end::Global Javascript Bundle-->
     
       
        <script src="assets/js/custom/widgets.js"></script>
        <script src="../../js/suportes/chat/chat.js"></script>
    

       
        <!--end::Custom Javascript-->
        <!--end::Javascript-->


        <!--begin::Custom Javascript(used by this page)-->

        <script src="../../js/dashboard/step-js.js"></script>
        <script src="../../js/cockpit/create-cockpit.js"></script>
        <script src="../../node_modules/print-js/dist/print.js" ></script>
        <!--end::Custom Javascript-->


        <script src="assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
        <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
        <script src="../../js/relatorios-plcodes/relatorios.js"></script>

      

        <script src="assets/plugins/custom/draggable/draggable.bundle.js"></script>

        <script src="assets/js/custom/documentation/general/draggable/multiple-containers.js"></script>
       

       






    </body>
    <!--end::Body-->

    </html>