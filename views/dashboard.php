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

// Set the locale to Portuguese
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

$_SESSION['pagina_atual'] = 'Dashboard Principal';

$nivel_acesso_user_sessao = isset($_COOKIE['nivel_acesso_usuario']) ? trim($_COOKIE['nivel_acesso_usuario']) : '';
$id_tabela_cliente_sessao = isset($_COOKIE['id_tabela_cliente']) ? trim($_COOKIE['id_tabela_cliente']) : '';
$id_BD_Colaborador = isset($_SESSION['bd_id']) ? trim($_SESSION['bd_id']) : '';

if ($nivel_acesso_user_sessao == "" or  $nivel_acesso_user_sessao == 'undefined') {


    $value = 'Sentimos muito! <br/>O STEP Não Conseguiu Validar seu Login Ativo na Sessão, Por gentileza, refaça seu Login.';
    $_SESSION['error'] =  $value;

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

$projeto_atual = !empty($_COOKIE['projeto_atual']) ? $_COOKIE['projeto_atual'] : '';

if ($projeto_atual != '') {


    $filtro = "AND o.id_obra ='$projeto_atual ' GROUP BY p.id_ponto";
} else {

    $filtro = "GROUP BY o.id_obra";
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

    $sql_personalizado = "AND (up.id_usuario  = '$id_usuario_sessao')";
}


/*
salvo e resgato os itens no coockie para poder personalisar os graficos e informações espscíficas do cliente.

*/
// Data consulta global do dashboard //
$Data_atual_Hoje = date_create()->format('Y-m-d ');
$Data_atual_Hoje_BR = date('d/m/Y', strtotime($Data_atual_Hoje));
// == a data está pegando desde 2019, assim que o módulo for liberado, alterar a data de invervalo para -7 dias entre -14 dias
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-7 days', strtotime($Data_atual_Hoje)));

// Transforme a data para o formato 'd-m-Y'
$Data_Intervalo_Periodo_BR = date('d/m/Y', strtotime($Data_Intervalo_Periodo));

$Data_dia_Presente = date('Y-m-d ', strtotime('-1 days', strtotime($Data_atual_Hoje)));
$Data_dia_Passado = date('Y-m-d', strtotime('-2 days', strtotime($Data_atual_Hoje)));

$Data_7_dias_antes = date('Y-m-d ', strtotime('-7 days', strtotime($Data_atual_Hoje)));
$Data_14_dias_antes = date('Y-m-d', strtotime('-14 days', strtotime($Data_atual_Hoje)));
//Elimina o cookie pro path raiz
//unsetcookie('meucookie', '/');

//Elimina o cookie de um domínio quando estiver em um subdomínio por exemplo: bar.foo.com
//unsetcookie('meucookie', '/', 'foo.com');




?>
<!DOCTYPE html>
<!--
Author: Fabiano Barros
Product Name: STEP Sistema de Tratamento EP
Purchase: https://step.eco.br
Website: http://step.eco.br 
Contact: dev@grupoep.com.br
Versão: 1.1.4
-->
<html lang="pt-br">

<head>
    <base href="../tema/dist/">
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" async />
    <!--end::Fonts-->
    <!--begin::Vendor Stylesheets(used by this page)-->
    <link href="assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/custom/vis-timeline/vis-timeline.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/custom/prismjs/prismjs.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" async />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" async />
    <link rel="stylesheet" type="text/css" href="../../node_modules/print-js/dist/print.css">
    <!--end::Global Stylesheets Bundle-->


    <style>
    .my-chat {
        position: relative;
        min-width: 350px;
        height: 700px;
    }
    @import "https://code.highcharts.com/css/highcharts.css";
    @import "https://code.highcharts.com/css/highcharts-dark.css";

    .highcharts-figure,
.highcharts-data-table table {
    min-width: 320px;
    max-width: 800px;
    margin: 1em auto;
}

.highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
}

.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}

.highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
}

.highcharts-data-table td,
.highcharts-data-table th,
.highcharts-data-table caption {
    padding: 0.5em;
}

.highcharts-data-table thead tr,
.highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}

.highcharts-data-table tr:hover {
    background: #f1f7ff;
}


</style>

</head>
<!--end::Head-->
<!--begin::Body-->

<body data-kt-name="metronic" id="kt_body" class="page-loading-enabled page-loading header-fixed header-tablet-and-mobile-fixed toolbar-enabled">
    <!--begin::Theme mode setup on page load-->
    <script>
        if (document.documentElement) {
            const defaultThemeMode = "system";
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

    <script>
        var defaultThemeMode = "dark";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-theme-mode");
            } else {
                if (localStorage.getItem("data-theme") !== null) {
                    themeMode = localStorage.getItem("data-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-theme", themeMode);
        }


        // carrega o blip para comunicação 
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
                <?php include_once 'header.php'; ?>
                <!--end::Header-->


                <!--begin::Toolbar-->
                <?php include_once 'toolbar.php'; ?>
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
                                    <h3 class="fs-2 fw-bold text-gray-800 me-2 lh-1">Cockpit - Gestão Online de Dados em Tempo Real</h3>
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




                        



                        <!--begin::Row-->
                        <?php
if(isset($_SESSION['error']) && $_SESSION['error'] != ''){
    echo '<div class="alert alert-danger" role="alert">';
    echo $_SESSION['error'];
    echo '</div>';
    unset($_SESSION['error']);
}
?>


<div class="row g-5 g-xl-8 ">

                                    <div class="col-xl-3">
								
                                        <?php // Definir o número de dias para a consulta (no seu caso, 7)

                                                // Data consulta global do dashboard //
                                                $Data_atual_Hoje = date_create()->format('Y-m-d ');
                                                $Data_atual_Hoje_BR = date('d/m/Y', strtotime($Data_atual_Hoje));
                                                // == a data está pegando desde 2019, assim que o módulo for liberado, alterar a data de invervalo para -7 dias entre -14 dias
                                                $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-7 days', strtotime($Data_atual_Hoje)));
                                                $Data_Intervalo_Periodo_BR = date('d/m/Y', strtotime($Data_Intervalo_Periodo));

                                                $dias='7';
                                                // Preparar a consulta SQL
                                                //$sql = "SELECT COUNT(id_suporte) AS total FROM suporte WHERE status_suporte = 1 AND data_open >= :data_inicio";

                                                //$sql = "SELECT status_suporte, data_open FROM suporte WHERE data_open >= :data_inicio AND status_suporte = 1";
                                                $projeto_atual = (isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';
                                                $nome_projeto = (isset($_COOKIE['nome_projeto'])) ? $_COOKIE['nome_projeto'] : '';
                                                if($projeto_atual!=''){


                                                    $filtro_Dados = "INNER JOIN usuarios_projeto up ON up.id_obra = r.id_obra";
                                                    $complemento_Dados = "AND up.id_obra=$projeto_atual";

                                                } else{

                                                    $filtro_Dados = '';
                                                    $complemento_Dados='';
                                                }
                                                $sql = "SELECT COUNT(DISTINCT r.id_rmm) as total_leitura FROM rmm r
                                                $filtro_Dados WHERE r.data_leitura >= :data_inicio $complemento_Dados";
                                                $stmt = $conexao->prepare($sql);

                                                // Vincular o valor da data de início à consulta
                                                $stmt->bindValue(':data_inicio', $Data_Intervalo_Periodo);


                                                // Executar a consulta  
                                                $stmt->execute();

                                                // Obter o resultado da consulta
                                                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

                                                $count = $stmt->rowCount();
                                                $total_leitura = $resultado['total_leitura'];
                                              


                                                // Imprimir o resultado ?>
											<!--begin::Svg Icon | path: icons/duotune/general/gen032.svg-->
				


                                            
<div class="card shadow-sm py-11 card-xl-stretch mb-2">
    <div class="card-header">
    <span class="text-gray-700 fw-bold fs-5 mb-2 "> 
         <span class="btn btn-gray-700 me-2 bg-light-gray-700 px-8 py-4 btn-lg  min-w-200 rounded-2">
    Últimos 7 dias <div class="badge badge-secondary  badge-square badge-lg  fs-4 ms-2" data-kt-countup="true" data-kt-countup-decimal="," data-kt-countup-separator="." data-kt-countup-value="<?php echo $total_leitura; ?>" >0</div>

</span> </span>
        <div class="card-toolbar">
        <span class="svg-icon svg-icon-3x svg-icon-success d-block my-2">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd" d="M2 4.63158C2 3.1782 3.1782 2 4.63158 2H13.47C14.0155 2 14.278 2.66919 13.8778 3.04006L12.4556 4.35821C11.9009 4.87228 11.1726 5.15789 10.4163 5.15789H7.1579C6.05333 5.15789 5.15789 6.05333 5.15789 7.1579V16.8421C5.15789 17.9467 6.05333 18.8421 7.1579 18.8421H16.8421C17.9467 18.8421 18.8421 17.9467 18.8421 16.8421V13.7518C18.8421 12.927 19.1817 12.1387 19.7809 11.572L20.9878 10.4308C21.3703 10.0691 22 10.3403 22 10.8668V19.3684C22 20.8218 20.8218 22 19.3684 22H4.63158C3.1782 22 2 20.8218 2 19.3684V4.63158Z" fill="currentColor"/>
<path d="M10.9256 11.1882C10.5351 10.7977 10.5351 10.1645 10.9256 9.77397L18.0669 2.6327C18.8479 1.85165 20.1143 1.85165 20.8953 2.6327L21.3665 3.10391C22.1476 3.88496 22.1476 5.15129 21.3665 5.93234L14.2252 13.0736C13.8347 13.4641 13.2016 13.4641 12.811 13.0736L10.9256 11.1882Z" fill="currentColor"/>
<path d="M8.82343 12.0064L8.08852 14.3348C7.8655 15.0414 8.46151 15.7366 9.19388 15.6242L11.8974 15.2092C12.4642 15.1222 12.6916 14.4278 12.2861 14.0223L9.98595 11.7221C9.61452 11.3507 8.98154 11.5055 8.82343 12.0064Z" fill="currentColor"/>
</svg>
</span>
        <span class="text-success fs-2 fw-bold">Leituras Registradas</span>
  
        </div>
    </div>
        <div class="card-body">
        <?php echo "Total Geral de Leituras no Período dos últimos $dias dias.<br>"  ?>
        </div>
        <div class="card-footer">
            <p><span class="text-gray-700 fw-bold fs-5 mb-2 ">Data Inicial:</span><span class="text-gray-800 fw-bold fs-5 mb-2 "> <?php echo $Data_Intervalo_Periodo_BR; ?></span></p>
            <p><span class="text-gray-700 fw-bold fs-5 mb-2 ">Data Final:</span><span class="text-gray-800 fw-bold fs-5 mb-2 "> <?php echo $Data_atual_Hoje_BR ; ?></span></p>
        <p> <?php if($nome_projeto!='' || $nome_projeto!=NULL){ echo "<span class='text-gray-800 fw-bold fs-5 mb-2 '>Dados referente ao Projeto:</span><br><span class='text-primary px-7 fw-bold fs-5 mb-2'> {$nome_projeto}</span>";}?></p>
        </div>
    </div>
</div>

											
										
										<!--end::Body-->
                                       
									<!--end::Statistics Widget 5-->
                                    <div class="col-xl-3">
								
                                <?php //Total Geral de Suporte no periodo

                                     // Define o Período da Busca dos Dados
$Data_Atual_Periodo = date_create()->format('Y-m-d');
$Data_atual_Hoje_BR = date('d/m/Y', strtotime($Data_Atual_Periodo)); 
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-7 days', strtotime($Data_Atual_Periodo)));
$Data_Intervalo_Periodo_BR = date('d/m/Y', strtotime($Data_Intervalo_Periodo));

$dias='7';
// Preparar a consulta SQL
//$sql = "SELECT COUNT(id_suporte) AS total FROM suporte WHERE status_suporte = 1 AND data_open >= :data_inicio";

//$sql = "SELECT status_suporte, data_open FROM suporte WHERE data_open >= :data_inicio AND status_suporte = 1";
$projeto_atual = (isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';
$nome_projeto = (isset($_COOKIE['nome_projeto'])) ? $_COOKIE['nome_projeto'] : '';
if($projeto_atual!=''){


$filtro_Dados = "INNER JOIN usuarios_projeto up ON up.id_obra = s.obra";
$complemento_Dados = "AND up.id_obra=$projeto_atual";

} else{

$filtro_Dados = '';
$complemento_Dados='';
}
$sql = "SELECT COUNT(DISTINCT s.id_suporte) as total_suporte FROM suporte s $filtro_Dados WHERE s.data_open >= :data_inicio  $complemento_Dados";
$stmt = $conexao->prepare($sql);

// Vincular o valor da data de início à consulta
$stmt->bindValue(':data_inicio', $Data_Intervalo_Periodo);


// Executar a consulta  
$stmt->execute();

// Obter o resultado da consulta
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

$count = $stmt->rowCount();
$total_suporte = $resultado['total_suporte'];

                                      


                                        // Imprimir o resultado ?>
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen032.svg-->
        


                                    
<div class="card shadow-sm py-11 card-xl-stretch mb-5">
<div class="card-header">
<span class="text-gray-700 fw-bold fs-5 mb-2 "> 
         <span class="btn btn-gray-700 me-2 bg-light-gray-700 px-8 py-4 btn-lg  min-w-200 rounded-2">
    Últimos 7 dias<div class="badge badge-secondary  badge-square badge-lg  fs-4 ms-2" data-kt-countup="true" data-kt-countup-decimal="," data-kt-countup-separator="." data-kt-countup-value="<?php echo $total_suporte; ?>" >0</div>

</span> </span>
<div class="card-toolbar">
<span class="svg-icon svg-icon-3x svg-icon-success d-block my-2">
    <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
<path opacity="0.3" d="M16 0.200012H4C1.8 0.200012 0 2.00001 0 4.20001V16.2C0 18.4 1.8 20.2 4 20.2H16C18.2 20.2 20 18.4 20 16.2V4.20001C20 2.00001 18.2 0.200012 16 0.200012ZM15 10.2C15 10.9 14.8 11.6 14.6 12.2H18V16.2C18 17.3 17.1 18.2 16 18.2H12V14.8C11.4 15.1 10.7 15.2 10 15.2C9.3 15.2 8.6 15 8 14.8V18.2H4C2.9 18.2 2 17.3 2 16.2V12.2H5.4C5.1 11.6 5 10.9 5 10.2C5 9.50001 5.2 8.80001 5.4 8.20001H2V4.20001C2 3.10001 2.9 2.20001 4 2.20001H8V5.60001C8.6 5.30001 9.3 5.20001 10 5.20001C10.7 5.20001 11.4 5.40001 12 5.60001V2.20001H16C17.1 2.20001 18 3.10001 18 4.20001V8.20001H14.6C14.8 8.80001 15 9.50001 15 10.2Z" fill="currentColor"/>
<path d="M12 1.40002C15.4 2.20002 18 4.80003 18.8 8.20003H14.6C14.1 7.00003 13.2 6.10003 12 5.60003V1.40002ZM5.40001 8.20003C5.90001 7.00003 6.80001 6.10003 8.00001 5.60003V1.40002C4.60001 2.20002 2.00001 4.80003 1.20001 8.20003H5.40001ZM14.6 12.2C14.1 13.4 13.2 14.3 12 14.8V19C15.4 18.2 18 15.6 18.8 12.2H14.6ZM8.00001 14.8C6.80001 14.3 5.90001 13.4 5.40001 12.2H1.20001C2.00001 15.6 4.60001 18.2 8.00001 19V14.8Z" fill="currentColor"/>
</svg>
</span>
<span class="text-success fs-2 fw-bold">Total de Tickets</span>

</div>
</div>
<div class="card-body">
<?php echo "Total Geral de Tickets Gerados nos últimos $dias dias.<br>"  ?>
</div>
<div class="card-footer">
            <p><span class="text-gray-700 fw-bold fs-5 mb-2 ">Data Inicial:</span><span class="text-gray-800 fw-bold fs-5 mb-2 "> <?php echo $Data_Intervalo_Periodo_BR; ?></span></p>
            <p><span class="text-gray-700 fw-bold fs-5 mb-2 ">Data Final:</span><span class="text-gray-800 fw-bold fs-5 mb-2 "> <?php echo $Data_atual_Hoje_BR ; ?></span></p>
            <p> <?php if($nome_projeto!='' || $nome_projeto!=NULL){ echo "<span class='text-gray-800 fw-bold fs-5 mb-2 '>Dados referente ao Projeto:</span><br><span class='text-primary px-7 fw-bold fs-5 mb-2'> {$nome_projeto}</span>";}?></p>
        </div>
</div>
</div>		



<div class="col-xl-3">
								
<?php //Tickets Fechados
// Define o Período da Busca dos Dados
$Data_Atual_Periodo = date_create()->format('Y-m-d');
$Data_atual_Hoje_BR = date('d/m/Y', strtotime($Data_Atual_Periodo));
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-7 days', strtotime($Data_Atual_Periodo)));
$Data_Intervalo_Periodo_BR = date('d/m/Y', strtotime($Data_Intervalo_Periodo));


$dias='7';
// Preparar a consulta SQL
//$sql = "SELECT COUNT(id_suporte) AS total FROM suporte WHERE status_suporte = 1 AND data_open >= :data_inicio";

//$sql = "SELECT status_suporte, data_open FROM suporte WHERE data_open >= :data_inicio AND status_suporte = 1";
$projeto_atual = (isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';
$nome_projeto = (isset($_COOKIE['nome_projeto'])) ? $_COOKIE['nome_projeto'] : '';
if($projeto_atual!=''){


$filtro_Dados = "INNER JOIN usuarios_projeto up ON up.id_obra = s.obra";
$complemento_Dados = "AND up.id_obra=$projeto_atual";

} else{

$filtro_Dados = '';
$complemento_Dados='';
}
$sql = "SELECT COUNT(DISTINCT s.id_suporte) as tickets_fechados FROM suporte s $filtro_Dados WHERE s.data_open >= :data_inicio  AND s.status_suporte = 4 $complemento_Dados";
$stmt = $conexao->prepare($sql);

// Vincular o valor da data de início à consulta
$stmt->bindValue(':data_inicio', $Data_Intervalo_Periodo);


// Executar a consulta  
$stmt->execute();

// Obter o resultado da consulta
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

$count = $stmt->rowCount();
$tickets_fechados = $resultado['tickets_fechados'];
 $tickets_fechados;

// Imprimir o resultado ?>
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen032.svg-->
        


                                    
<div class="card shadow-sm py-11 card-xl-stretch mb-5">
<div class="card-header">
<span class="text-gray-700 fw-bold fs-5 mb-2 "> 
         <span class="btn btn-gray-700 me-2 bg-light-gray-700 px-8 py-4 btn-lg  min-w-200 rounded-2">
    Últimos 7 dias <div class="badge badge-secondary  badge-square badge-lg  fs-4 ms-2" data-kt-countup="true" data-kt-countup-decimal="," data-kt-countup-separator="." data-kt-countup-value="<?php echo $tickets_fechados; ?>" >0</div>

</span> </span>
<div class="card-toolbar">
<span class="svg-icon svg-icon-3x svg-icon-success d-block my-2">
<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/>
<path d="M14.854 11.321C14.7568 11.2282 14.6388 11.1818 14.4998 11.1818H14.3333V10.2272C14.3333 9.61741 14.1041 9.09378 13.6458 8.65628C13.1875 8.21876 12.639 8 12 8C11.361 8 10.8124 8.21876 10.3541 8.65626C9.89574 9.09378 9.66663 9.61739 9.66663 10.2272V11.1818H9.49999C9.36115 11.1818 9.24306 11.2282 9.14583 11.321C9.0486 11.4138 9 11.5265 9 11.6591V14.5227C9 14.6553 9.04862 14.768 9.14583 14.8609C9.24306 14.9536 9.36115 15 9.49999 15H14.5C14.6389 15 14.7569 14.9536 14.8542 14.8609C14.9513 14.768 15 14.6553 15 14.5227V11.6591C15.0001 11.5265 14.9513 11.4138 14.854 11.321ZM13.3333 11.1818H10.6666V10.2272C10.6666 9.87594 10.7969 9.57597 11.0573 9.32743C11.3177 9.07886 11.6319 8.9546 12 8.9546C12.3681 8.9546 12.6823 9.07884 12.9427 9.32743C13.2031 9.57595 13.3333 9.87594 13.3333 10.2272V11.1818Z" fill="currentColor"/>
</svg>
</span>
<span class="text-success fs-2 fw-bold">Tickets Fechados</span>

</div>
</div>
<div class="card-body">
<?php echo "Total Geral de Tickets Fechados no Período dos últimos $dias dias.<br>"  ?>
</div>
<div class="card-footer">
            <p><span class="text-gray-700 fw-bold fs-5 mb-2 ">Data Inicial:</span><span class="text-gray-800 fw-bold fs-5 mb-2 "> <?php echo $Data_Intervalo_Periodo_BR; ?></span></p>
            <p><span class="text-gray-700 fw-bold fs-5 mb-2 ">Data Final:</span><span class="text-gray-800 fw-bold fs-5 mb-2 "> <?php echo $Data_atual_Hoje_BR ; ?></span></p>
            <p> <?php if($nome_projeto!='' || $nome_projeto!=NULL){ echo "<span class='text-gray-800 fw-bold fs-5 mb-2 '>Dados referente ao Projeto:</span><br><span class='text-primary px-7 fw-bold fs-5 mb-2'> {$nome_projeto}</span>";}?></p>
        </div>
</div>
</div>



<!-- Tickets Pendentes -->

<div class="col-xl-3">
								
<?php // Definir o número de dias para a consulta (no seu caso, 7)
// Define o Período da Busca dos Dados
$Data_Atual_Periodo = date_create()->format('Y-m-d');
$Data_atual_Hoje_BR = date('d/m/Y', strtotime($Data_Atual_Periodo));
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-7 days', strtotime($Data_Atual_Periodo)));
// Transforme a data para o formato 'd-m-Y'
$Data_Intervalo_Periodo_BR = date('d/m/Y', strtotime($Data_Intervalo_Periodo));
$dias='7';
// Preparar a consulta SQL
//$sql = "SELECT COUNT(id_suporte) AS total FROM suporte WHERE status_suporte = 1 AND data_open >= :data_inicio";

//$sql = "SELECT status_suporte, data_open FROM suporte WHERE data_open >= :data_inicio AND status_suporte = 1";
$projeto_atual = (isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';
$nome_projeto = (isset($_COOKIE['nome_projeto'])) ? $_COOKIE['nome_projeto'] : '';
if($projeto_atual!=''){


$filtro_Dados = "INNER JOIN usuarios_projeto up ON up.id_obra = s.obra";
$complemento_Dados = "AND up.id_obra=$projeto_atual";

} else{

$filtro_Dados = '';
$complemento_Dados='';
}
$sql = "SELECT COUNT(DISTINCT s.id_suporte) as tickets_pendente FROM suporte s $filtro_Dados WHERE s.data_open >= :data_inicio  AND s.status_suporte !=4  $complemento_Dados";
$stmt = $conexao->prepare($sql);

// Vincular o valor da data de início à consulta
$stmt->bindValue(':data_inicio', $Data_Intervalo_Periodo);


// Executar a consulta  
$stmt->execute();

// Obter o resultado da consulta
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

$count = $stmt->rowCount();  
$tickets_pendente = $resultado['tickets_pendente'];
// $tickets_pendente;

// Imprimir o resultado ?>
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen032.svg-->
        


                                    
<div class="card shadow-sm py-11 card-xl-stretch mb-5">
<div class="card-header">
<span class="text-gray-700 fw-bold fs-5 mb-2 "> 
         <span class="btn btn-gray-700 me-2 bg-light-gray-700 px-8 py-4 btn-lg  min-w-200 rounded-2">
    Últimos 7 dias <div class="badge badge-secondary  badge-square badge-lg  fs-4 ms-2" data-kt-countup="true" data-kt-countup-decimal="," data-kt-countup-separator="." data-kt-countup-value="<?php echo $tickets_pendente; ?>" >0</div>

</span> </span>
<div class="card-toolbar">
<span class="svg-icon svg-icon-3x svg-icon-success d-block my-2">
<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"/>
<path d="M11.276 13.654C11.276 13.2713 11.3367 12.9447 11.458 12.674C11.5887 12.394 11.738 12.1653 11.906 11.988C12.0833 11.8107 12.3167 11.61 12.606 11.386C12.942 11.1247 13.1893 10.896 13.348 10.7C13.5067 10.4947 13.586 10.2427 13.586 9.944C13.586 9.636 13.4833 9.356 13.278 9.104C13.082 8.84267 12.69 8.712 12.102 8.712C11.486 8.712 11.066 8.866 10.842 9.174C10.6273 9.482 10.52 9.82267 10.52 10.196L10.534 10.574H8.826C8.78867 10.3967 8.77 10.2333 8.77 10.084C8.77 9.552 8.90067 9.07133 9.162 8.642C9.42333 8.20333 9.81067 7.858 10.324 7.606C10.8467 7.354 11.4813 7.228 12.228 7.228C13.1987 7.228 13.9687 7.44733 14.538 7.886C15.1073 8.31533 15.392 8.92667 15.392 9.72C15.392 10.168 15.322 10.5507 15.182 10.868C15.042 11.1853 14.874 11.442 14.678 11.638C14.482 11.834 14.2253 12.0533 13.908 12.296C13.544 12.576 13.2733 12.8233 13.096 13.038C12.928 13.2527 12.844 13.528 12.844 13.864V14.326H11.276V13.654ZM11.192 15.222H12.928V17H11.192V15.222Z" fill="currentColor"/>
</svg>
</span>
<span class="text-success fs-2 fw-bold">Tickets Pendentes</span>

</div>
</div>
<div class="card-body">
<?php echo "Total Geral de Tickets Pendentes no Período dos últimos $dias dias.<br>"  ?>
</div>
<div class="card-footer">
            <p><span class="text-gray-700 fw-bold fs-5 mb-2 ">Data Inicial:</span><span class="text-gray-800 fw-bold fs-5 mb-2 "> <?php echo $Data_Intervalo_Periodo_BR; ?></span></p>
            <p><span class="text-gray-700 fw-bold fs-5 mb-2 ">Data Final:</span><span class="text-gray-800 fw-bold fs-5 mb-2 "> <?php echo $Data_atual_Hoje_BR ; ?></span></p>
            <p> <?php if($nome_projeto!='' || $nome_projeto!=NULL){ echo "<span class='text-gray-800 fw-bold fs-5 mb-2 '>Dados referente ao Projeto:</span><br><span class='text-primary px-7 fw-bold fs-5 mb-2'> {$nome_projeto}</span>";}?></p>
        </div>
</div>
</div>
<!-- Tickets Pendentes -->
						
                            </div>

                        <div class="row gx-5 gx-xl-10">

                        

                            <!--begin::Col-->
                            <div class="col-xxl-6 mb-5 mb-xl-10">
                                <!--begin::Chart widget 27-->
                                <div class="card card-flush h-xl-100 widget_dashboard">
                                    <!--begin::Header-->
                                    <div class="card-header py-7">
                                        <!--begin::Statistics-->
                                        <div class="m-0">
                                            <!--begin::Heading-->
                                          
                                            <!--end::Heading-->
                                            <!--begin::Description-->
                                            <span class="fs-6 fw-semibold text-gray-400">Crescimento Orgânico | Últ
                                                7
                                                dias</span>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Statistics-->
                                        <!--begin::Toolbar-->
                                        <div class="card-toolbar">
                                            <div class="my-0">
                                                <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                                                    <span class="svg-icon svg-icon-2"><svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"></rect>
                                                                <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                            </g>
                                                        </svg></span>
                                                    <!--end::Svg Icon--> </button>

                                                <!--begin::Menu 2-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px" data-kt-menu="true">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">Ações Rápidas</div>
                                                    </div>
                                                    <!--end::Menu item-->





                                                    <!--begin::Menu separator-->
                                                    <div class="separator mt-3 opacity-75"></div>
                                                    <!--end::Menu separator-->

                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content px-3 py-3">

                                                            <a href="javascript:;" class="btn btn-primary  btn-sm px-4 gera_relatorio" data-id='kt_charts_widget_27' data-titulo='Crescimento Orgânico dos últimos 7 dias'>PDF Report</a>

                                                        </div>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 2-->
                                            </div>

                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card">
                                        <div class="card-body">

                                        <div id="mensagem-aguarde-kt_charts_widget_27" class='d-none'>
                                                    <div class="blockui-message"><span class="spinner-border text-primary"></span> Carregando...</div>
                                                </div>
                                            <div id="kt_charts_widget_27" class="min-h-auto "></div>
                                        </div>
                                    </div>


                                    <!--end::Body-->
                                </div>
                                <!--end::Chart widget 27-->
                            </div>
                            <!--end::Col-->

                            <!--begin::Col-->
                            <div class="col-xxl-6 mb-5 mb-xl-10">
                                <!--begin::Chart widget 27-->
                                <div class="card card-flush h-xl-100 widget_dashboard">
                                    <!--begin::Header-->
                                    <div class="card-header py-7">
                                        <!--begin::Statistics-->
                                        <div class="m-0">
                                            <!--begin::Heading-->
                                            <div class="d-flex align-items-center mb-2">
                                                <!--begin::Title-->
                                                <span class="fs-2 fw-bold text-gray-800 me-2 lh-1" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-dismiss="click" data-kt-countup="true" data-kt-countup-separator="." title="Acompanhe o Crescimento" id="valor_total_widget_29">Projetos com (-)Leituras </span>
                                                <!--end::Title-->
                                                <!--begin::Label-->
                                                <span class="badge badge-light-success fs-base" id='classe_badge_widget_29'>
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                                    <span class="svg-icon svg-icon-5  ms-n1" id='classe_widget_29'>
                                                        <i class="" id='icone_widget_29'></i>
                                                    </span>
                                                    <!--end::Svg Icon--> <span id="porcentagem_total_widget_29" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-dismiss="click" title="Diferença Percentual entre a Semana Atual e a Anterior">Ranking Últimos 30 dias</span>
                                                </span>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Heading-->
                                            <!--begin::Description-->
                                            <span class="fs-6 fw-semibold text-gray-400">Projetos Ativos</span>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Statistics-->
                                        <!--begin::Toolbar-->
                                        <div class="card-toolbar">
                                            <div class="my-0">
                                                <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                                                    <span class="svg-icon svg-icon-2"><svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"></rect>
                                                                <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                            </g>
                                                        </svg></span>
                                                    <!--end::Svg Icon--> </button>

                                                <!--begin::Menu 2-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px" data-kt-menu="true">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">Ações Rápidas</div>
                                                    </div>
                                                    <!--end::Menu item-->





                                                    <!--begin::Menu separator-->
                                                    <div class="separator mt-3 opacity-75"></div>
                                                    <!--end::Menu separator-->

                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content px-3 py-3">

                                                            <a href="javascript:;" class="btn btn-primary  btn-sm px-4 gera_relatorio" data-id='kt_charts_widget_29' data-titulo='Projetos com (-)Leituras | Ranking Últimos 30 dias'>PDF Report</a>

                                                        </div>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 2-->
                                            </div>

                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card">
                                        <div class="card-body">

                                        <div id="mensagem-aguarde-kt_charts_widget_29" class='d-none'>
                                                <div class="blockui-message"><span class="spinner-border text-primary"></span> Carregando...</div>
                                            </div>
                                            
                                            <div id="kt_charts_widget_29" class="min-h-auto"></div>
                                        </div>
                                    </div>


                                    <!--end::Body-->
                                </div>
                                <!--end::Chart widget 27-->
                            </div>
                            <!--end::Col-->


                            <!--begin::Col-->
                            <div class="col-xxl-6 mb-5 mb-xl-10">
                                <!--begin::Chart widget 28-->
                                <div class="card card-flush h-xl-100 widget_dashboard widget_dashboard" id='gera_kt_charts_widget_28'>
                                    <!--begin::Header-->
                                    <div class="card-header py-7">
                                        <!--begin::Statistics-->
                                        <div class="m-0">
                                            <!--begin::Heading-->
                                            <div class="d-flex align-items-center mb-2">
                                                <!--begin::Title-->
                                                <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-dismiss="click" data-kt-countup="true" data-kt-countup-separator="." title="Diferença Total de Leituras em Relação ao Período Anterior" id="valor_total_widget_28">0</span>
                                                <!--end::Title-->
                                                <!--begin::Label-->
                                                <span class="badge badge-light-success fs-base" id='classe_badge_widget_28'>
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                                    <span class="svg-icon svg-icon-5  ms-n1" id='classe_widget_28'>
                                                        <i class="" id='icone_widget_28'></i>
                                                    </span>
                                                    <!--end::Svg Icon--> <span id="porcentagem_total_widget_28" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-dismiss="click" title="Porcentagem de Crescimento em Comparação ao Semestre Anterior">0.00</span>%
                                                </span>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Heading-->
                                            <!--begin::Description-->
                                            <span class="fs-6 fw-semibold text-gray-400">Leituras Monitoradas | Últ
                                                Semestre</span>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Statistics-->
                                        <!--begin::Toolbar-->
                                        <div class="card-toolbar">
                                            <div class="my-0">
                                                <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                                                    <span class="svg-icon svg-icon-2"><svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"></rect>
                                                                <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                            </g>
                                                        </svg></span>
                                                    <!--end::Svg Icon--> </button>

                                                <!--begin::Menu 2-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px" data-kt-menu="true">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">Ações Rápidas</div>
                                                    </div>
                                                    <!--end::Menu item-->




                                                    <!--begin::Menu separator-->
                                                    <div class="separator mt-3 opacity-75"></div>
                                                    <!--end::Menu separator-->

                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content px-3 py-3">

                                                            <a href="javascript:;" class="btn btn-primary  btn-sm px-4 gera_relatorio" data-id='kt_charts_widget_28' data-titulo='Leituras: Dados Coletados dos últimos 30 dias.'>PDF Report</a>

                                                        </div>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 2-->
                                            </div>

                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card ">
                                        <div class="card-body">
                                            <div id="kt_charts_widget_28" class="min-h-auto ps-4 pe-6 mb-3 h-550px"></div>
                                        </div>
                                    </div>


                                </div>
                                <!--end::Chart widget 28-->
                            </div>
                            <!--end::Col-->


                            <!--begin::Col-->
                            <div class="col-xxl-6 mb-5 mb-xl-10">
                                <!--begin::Chart widget 27-->
                                <div class="card card-flush h-xl-100 widget_dashboard">
                                    <!--begin::Header-->
                                    <div class="card-header py-7">
                                        <!--begin::Statistics-->
                                        <div class="m-0">

                                            <span class="fs-2 fw-bold text-gray-800 me-2 lh-1" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-dismiss="click" data-kt-countup="true" data-kt-countup-separator="." title="Acompanhe o Crescimento">Projetos sem Leituras </span>
                                            <span class="text-gray-400 pt-1 fw-semibold fs-6">| Com +30 dias</span>
                                        </div>
                                        <!--end::Statistics-->


                                        <!--begin::Toolbar-->
                                        <div class="card-toolbar">
                                            <div class="my-0">
                                                <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                                                    <span class="svg-icon svg-icon-2"><svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"></rect>
                                                                <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                            </g>
                                                        </svg></span>
                                                    <!--end::Svg Icon--> </button>

                                                <!--begin::Menu 2-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px" data-kt-menu="true">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">Ações Rápidas</div>
                                                    </div>
                                                    <!--end::Menu item-->





                                                    <!--begin::Menu separator-->
                                                    <div class="separator mt-3 opacity-75"></div>
                                                    <!--end::Menu separator-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="javascript:;" class="menu-link px-3" onclick="atualizarProjetosSemLeitura()">
                                                            Atualizar Dados
                                                        </a>
                                                    </div>

                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content px-3 py-3">

                                                            <a href="javascript:;" class="btn btn-primary  btn-sm px-4 gera_relatorio" data-id='kt_charts_widget_32_tabela' data-titulo='Projetos sem Leitura | Últimos 30 dias.'>PDF Report</a>

                                                        </div>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 2-->

                                            </div>


                                        </div>
                                        <!--end::Toolbar-->

                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card">

                                        <div class="card-body">

                                        <div class="d-flex align-items-center d-none" id="mensagem-aguarde-tabela-projetos-sem-leitura">
		  <div class="spinner-border text-primary " role="status"  >
		  <span class="visually-hidden ">Loading...</span>
		  </div>
		  <div class="me-10"><span class="text-gray-600 px-3">Buscando Dados...</span></div>
	  </div>


                                           

                                            <div id="kt_charts_widget_32" class="scroll h-550px d-none">

                                                <table class="table table-hover table-rounded table-striped border gy-7 gs-7" id="kt_charts_widget_32">
                                                    <thead>
                                                        <tr class="fw-semibold fs-6 text-gray-800 border-bottom-2 border-gray-200">

                                                            <th>Projeto</th>
                                                            <th>Núcleo</th>
                                                            <th>PLCode</th>
                                                            <th>Indicador</th>
                                                            <th>Último Registro</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>


                                            </div>
                                        </div>
                                    </div>


                                    <!--end::Body-->
                                </div>
                                <!--end::Chart widget 27-->
                            </div>
                            <!--end::Col-->

                            <!--begin::Col Tabela Produtos Químicos-->

                            <!--end::Col Tabela Produtos Químicos-->
                        </div>
                        <!--end::Row-->
                        <!--begin::Row-->
                        <div class="row g-5 g-xl-10 mb-xl-10">
                            <!--begin::Col-->
                            <div class="col-xl-6 mb-5 mb-xl-10">
                                <!--begin::Tables widget 9-->
                                <div class="card card-flush h-xl-100 widget_dashboard">
                                    <!--begin::Header-->
                                    <div class="card-header pt-5">
                                        <!--begin::Title-->
                                        <h3 class="fs-2 fw-bold text-gray-800 me-2 lh-1">
                                            <span class="card-label fw-bold text-gray-800">Central de Suporte</span>
                                            <span class="text-gray-400 pt-1 fw-semibold fs-6">Tickets em Aberto |
                                                10+
                                                antigos</span>
                                        </h3>
                                        <!--end::Title-->
                                        <!--begin::Toolbar-->
                                        <div class="card-toolbar">
                                            <div class="my-0">
                                                <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                                                    <span class="svg-icon svg-icon-2"><svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"></rect>
                                                                <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                            </g>
                                                        </svg></span>
                                                    <!--end::Svg Icon--> </button>

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
                                                        <a href="javascript:;" class="menu-link px-3" onclick="atualizarDiv_Tabela_Suporte()">
                                                            Atualizar Dados
                                                        </a>
                                                    </div>
                                                    <!--end::Menu item-->



                                                    <!--begin::Menu separator-->
                                                    <div class="separator mt-3 opacity-75"></div>
                                                    <!--end::Menu separator-->

                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content px-3 py-3">

                                                            <a href="javascript:;" class="btn btn-primary  btn-sm px-4 gera_relatorio" data-id='tabela-resumo-suporte' data-titulo='Relação de Suportes Pendentes'>PDF Report</a>

                                                        </div>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 2-->
                                            </div>

                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card-body py-3">
                                        <!--begin::Table container-->
                                        <div id="mensagem-aguarde-tabela-resumo-suporte" class='d-none'>
                                            <div class="blockui-message"><span class="spinner-border text-primary"></span> Carregando...</div>
                                        </div>
                                        <div class="table-responsive" id='tabela-resumo-suporte'>


                                        </div>
                                        <!--end::Table container-->
                                    </div>
                                    <!--end::Body-->
                                </div>
                                <!--end::Tables Widget 9-->
                            </div>
                            <!--end::Col-->


                            <div class="col-xl-6 mb-5 mb-xl-10">
                                <!--begin::Timeline widget 3-->
                                <div class="card h-md-100 widget_dashboard">
                                    <!--begin::Header-->
                                    <div class="card-header border-0 pt-5">
                                        <h3 class="fs-2 fw-bold text-gray-800 me-2 lh-1">
                                            <span class="card-label fw-bold text-dark">Tarefas não Realizadas</span>
                                            <?php
                                               $sql_check = $conexao->prepare("
                                               SELECT COUNT(DISTINCT periodo_ponto.id_periodo_ponto) 
                                               FROM periodo_ponto 
                                               INNER JOIN obras o ON o.id_obra = periodo_ponto.id_obra
                                               LEFT JOIN pontos_estacao p ON p.id_ponto = periodo_ponto.id_ponto 
                                               
                                               LEFT JOIN estacoes e ON e.id_estacao = periodo_ponto.id_estacao
                                               LEFT JOIN checkin ON checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto AND DATE_FORMAT(checkin.data_cadastro_checkin, '%Y-%m-%d')  >= :Data_atual_Hoje
                                               LEFT JOIN periodo_dia_ponto ON periodo_dia_ponto.id_periodo_ponto = periodo_ponto.id_periodo_ponto 
                                               LEFT JOIN dia_semana ON dia_semana.representa_php=periodo_dia_ponto.dia_semana 
                                               LEFT JOIN parametros_ponto on parametros_ponto.id_parametro =periodo_ponto.id_parametro 
                                               LEFT JOIN usuarios_projeto up ON up.id_obra = o.id_obra
                                               WHERE checkin.id_periodo_ponto IS NOT NULL AND o.status_cadastro='1'
                                               $sql_personalizado
                                               $filtro");
                                           
                                           $sql_check->bindParam(':Data_atual_Hoje', $Data_dia_Presente);
                                           $sql_check->execute();
                                         
                                          
                                           $totalcheck = $sql_check->fetchColumn();

                                          
                                         
                                           

            

                                          

                                            ?>
                                            <span class="text-muted mt-1 fw-semibold fs-7"><?php echo ($totalcheck)+0; ?>
                                                Realizados | nas últimas 24 horas.</span>
                                        </h3>


                                        <!--begin::Toolbar-->
                                        <div class="card-toolbar">
                                            <div class="my-0">
                                                <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                                                    <span class="svg-icon svg-icon-2"><svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"></rect>
                                                                <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                            </g>
                                                        </svg></span>
                                                    <!--end::Svg Icon--> </button>

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
                                                        <a href="javascript:;" class="menu-link px-3" onclick="atualizarDiv_Tabela_Checkin()">
                                                            Atualizar Dados
                                                        </a>
                                                    </div>
                                                    <!--end::Menu item-->



                                                    <!--begin::Menu separator-->
                                                    <div class="separator mt-3 opacity-75"></div>
                                                    <!--end::Menu separator-->

                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content px-3 py-3">

                                                            <a href="javascript:;" class="btn btn-primary  btn-sm px-4 gera_relatorio" data-id='tabela-chekins-realizados' data-titulo='Tarefas Não Realizadas'>PDF Report</a>

                                                        </div>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 2-->
                                            </div>

                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->

                                    <div class="d-flex align-items-center d-none px-10" id="mensagem-aguarde-tabela-chekins-realizados">
                                        <div class="spinner-border text-primary " role="status"  >
                                        <span class="visually-hidden ">Loading...</span>
                                        </div>
                                        <div class="me-10"><span class="text-gray-600 px-3">Buscando Dados...</span></div>
                                    </div>



                                    <div class="card-body py-0" id='tabela-chekins-realizados'></div>
                                    <!--end: Card Body-->
                                </div>
                                <!--end::Timeline widget 3-->

                            </div>

                        </div>
                        <!--end::Row-->

                        <!--begin::Row-->
                        <div class="row gx-5 gx-xl-10">
                            <!--begin::Col-->
                            <div class="col-xl-12 mb-5 mb-xl-10">
                                <!--begin::Chart widget 15-->
                                <div class="card card-flush h-xl-100 widget_dashboard">
                                    <!--begin::Header-->
                                    <div class="card-header pt-7">
                                        <!--begin::Title-->
                                        <h3 class="fs-2 fw-bold text-gray-800 me-2 lh-1">
                                            <span class="card-label fw-bold text-dark">Leituras Realizadas</span>
                                            <span class="text-gray-400 pt-2 fw-semibold fs-6">Totalizador por
                                                Projeto</span>
                                        </h3>
                                        <!--end::Title-->
                                        <!--begin::Toolbar-->
                                        <div class="card-toolbar">
                                            <div class="my-0">
                                                <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                                                    <span class="svg-icon svg-icon-2"><svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"></rect>
                                                                <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                            </g>
                                                        </svg></span>
                                                    <!--end::Svg Icon--> </button>

                                                <!--begin::Menu 2-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px" data-kt-menu="true">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">Ações Rápidas</div>
                                                    </div>
                                                    <!--end::Menu item-->



                                                    <!--begin::Menu separator-->
                                                    <div class="separator mt-3 opacity-75"></div>
                                                    <!--end::Menu separator-->

                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content px-3 py-3">

                                                            <a href="javascript:;" class="btn btn-primary  btn-sm px-4 gera_relatorio" data-id='tabela-leituras-realizadas' data-titulo='Total de Leituras Realizadas'>PDF Report</a>

                                                        </div>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 2-->
                                            </div>

                                        </div>
                                        <!--end::Toolbar-->



                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->




                                    <div class="card-body pt-5" id='tabela-leituras-realizadas'>
                                        <!--begin::Chart container-->
                                        <div id="kt_charts_widget_15_chart" class="min-h-auto ps-4 pe-6 mb-3 h-300px">
                                        </div>
                                        <!--end::Chart container-->
                                    </div>
                                    <!--end::Body-->
                                </div>
                                <!--end::Chart widget 15-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-xl-6 mb-5 mb-xl-10">

                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                        <!--begin::Row-->

                        <!--end::Row-->
                        <!--begin::Row-->
                        <div class="row gx-5 gx-xl-10">
                            <!--begin::Col-->
                            <div class="col-xl-4 mb-5 mb-xl-0">
                                <!--begin::List widget 12-->
                                <div class="card card-flush h-xl-100 widget_dashboard">
                                    <!--begin::Header-->
                                    <div class="card-header pt-7">
                                        <!--begin::Title-->
                                        <h3 class="fs-2 fw-bold text-gray-800 lh-1">
                                            <span class="card-label fw-bold text-gray-800">PLCodes + Lidos</span>
                                            <span class="text-gray-400 mt-1 fw-semibold fs-6">Os (10+) > 7 dias</span>
                                        </h3>
                                        <!--end::Title-->
                                        <!--begin::Toolbar-->
                                        <div class="card-toolbar">
                                            <div class="my-0">
                                                <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                                                    <span class="svg-icon svg-icon-2"><svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"></rect>
                                                                <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                            </g>
                                                        </svg></span>
                                                    <!--end::Svg Icon--> </button>

                                                <!--begin::Menu 2-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px" data-kt-menu="true">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">Ações Rápidas</div>
                                                    </div>
                                                    <!--end::Menu item-->

                                                  



                                                    <!--begin::Menu separator-->
                                                    <div class="separator mt-3 opacity-75"></div>
                                                    <!--end::Menu separator-->

                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content px-3 py-3">

                                                            <a href="javascript:;" class="btn btn-primary  btn-sm px-4 gera_relatorio" data-id='tabela_plcodes_lidos' data-titulo='PLCodes com + Leituras nos últimos 30 dias'>PDF Report</a>

                                                        </div>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 2-->
                                            </div>

                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card w-100">
                                        <div class="card-body">

                                        
                                    <div class="d-flex align-items-center d-none px-10" id="mensagem-aguarde-tabela_plcodes_lidos">
                                        <div class="spinner-border text-primary " role="status"  >
                                        <span class="visually-hidden ">Loading...</span>
                                        </div>
                                        <div class="me-10"><span class="text-gray-600 px-3">Buscando Dados...</span></div>
                                    </div>


                                       

                                            <div id="tabela_plcodes_lidos" style="height: 500px;"></div>
                                        </div>
                                    </div>
                                    <!--end::Body-->
                                </div>
                                <!--end::List widget 12-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-xl-4 mb-5 mb-xl-0" >
                                <!--begin::Chart widget 31-->
                                <div class="card card-flush h-xl-100 widget_dashboard">
                                    <!--begin::Header-->
                                    <div class="card-header pt-7 mb-7">
                                        <!--begin::Title-->
                                        <h3 class="fs-2 fw-bold text-gray-800 lh-1n">
                                            <span class="card-label fw-bold text-gray-800">Tickets não Finalizados</span>
                                            <span class="text-gray-400 mt-1 fw-semibold fs-6">> 7 dias</span>
                                        </h3>
                                        <!--end::Title-->
                                        <!--begin::Toolbar-->
                                        <div class="card-toolbar">
                                            <div class="my-0">
                                                <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                                                    <span class="svg-icon svg-icon-2"><svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"></rect>
                                                                <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                            </g>
                                                        </svg></span>
                                                    <!--end::Svg Icon--> </button>

                                                <!--begin::Menu 2-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px" data-kt-menu="true">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">Ações Rápidas</div>
                                                    </div>
                                                    <!--end::Menu item-->





                                                    <!--begin::Menu separator-->
                                                    <div class="separator mt-3 opacity-75"></div>
                                                    <!--end::Menu separator-->

                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content px-3 py-3">

                                                            <a href="javascript:;" class="btn btn-primary  btn-sm px-4 gera_relatorio" data-id='tabela-grafico-suporte' data-titulo='Suportes - Tickets em Aberto'>PDF Report</a>

                                                        </div>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 2-->
                                            </div>

                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                   

                                    <div class="card">
                                        <div class="card-body">
                                        <div id="mensagem-aguarde-tabela-grafico-suporte" class="d-none" >
                                                    <div class="blockui-message"><span class="spinner-border text-primary"></span> Carregando...</div>
                                                </div>

                                            <div  id='tabela-grafico-suporte' style="height: 500px;"></div>
                                        </div>
                                    </div>
                                    <!--end::Body-->
                                </div>
                                <!--end::Chart widget 31-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-xl-4 mb-5 mb-xl-0">
                            <div id="mensagem-aguarde-tabela-categoria-suporte" class='d-none'>
                                                    <div class="blockui-message"><span class="spinner-border text-primary"></span> Carregando...</div>
                                                </div>

                                <!--begin::Chart widget 30-->
                                <div class="card card-flush h-xl-100 widget_dashboard" id='tabela-categoria-suporte'>
                                    <!--begin::Header-->
                                    <div class="card-header pt-7 mb-7">
                                        <!--begin::Title-->
                                        <h3 class="fs-2 fw-bold text-gray-800  lh-1">
                                            <span class="card-label fw-bold text-gray-800">Suporte por
                                                Categoria</span>
                                            <span class="text-gray-400 mt-1 fw-semibold fs-6"> > 7 dias</span>
                                        </h3>
                                        <!--end::Title-->
                                        <!--begin::Toolbar-->
                                        <div class="card-toolbar">
                                            <div class="my-0">
                                                <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                                                    <span class="svg-icon svg-icon-2"><svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"></rect>
                                                                <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                                <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                                            </g>
                                                        </svg></span>
                                                    <!--end::Svg Icon--> </button>

                                                <!--begin::Menu 2-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px" data-kt-menu="true">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">Ações Rápidas</div>
                                                    </div>
                                                    <!--end::Menu item-->



                                                    <!--begin::Menu separator-->
                                                    <div class="separator mt-3 opacity-75"></div>
                                                    <!--end::Menu separator-->

                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content px-3 py-3">

                                                            <a href="javascript:;" class="btn btn-primary  btn-sm px-4 gera_relatorio" data-id='tabela-categoria-suporte' data-titulo='Suportes - Tickets por Categoria'>PDF Report</a>

                                                        </div>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 2-->
                                            </div>

                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card">
                                        <div class="card-body">
                                            <div id="kt_charts_widget_40_chart" style="height: 500px;"></div>
                                        </div>
                                    </div>


                                    <!--end::Body-->
                                </div>
                                <!--end::Chart widget 30-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end::Post-->
                </div>
                <!--end::Container-->
                <!--begin::Footer-->
                <?php include '../views/footer.php'; ?>
                <!--end::Footer-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Root-->
    <!--begin::Drawers-->
    <!--begin::Activities drawer-->
    <?php include '../views/conta-usuario/atividade-usuario.php'; ?>
    <!--end::Activities drawer-->

    <!--begin::Chat drawer-->
    <?php include '../views/chat/chat-usuario.php'; ?>


    <!--end::Chat drawer-->




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
    <?php include_once "../views/cockpit/modal-app-cockpit.php"; ?>
    <!--end::Modal - Create App Cockpit-->
    <!--begin::Modal - View Users-->

    <!--end::Modal - Users Buscar-->

    <!--end::Modals-->
    <!--begin::Javascript-->
    <script>
        var hostUrl = "../tema/assets/";
    </script>

    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
    

   
    <script>
        function loadScripts() {
            const scripts = [


                'assets/plugins/custom/prismjs/prismjs.bundle.js',
                'assets/plugins/custom/draggable/draggable.bundle.js',
                'assets/js/custom/documentation/general/draggable/multiple-containers.js',
                '../../js/dashboard/step-js.js',
               "https://code.highcharts.com/highcharts.js",
               "https://code.highcharts.com/highcharts-more.js",
               
               "https://code.highcharts.com/modules/annotations.js",
                "https://code.highcharts.com/modules/data.js",
                "https://code.highcharts.com/modules/series-label.js",
                "https://code.highcharts.com/modules/exporting.js",
                "https://code.highcharts.com/modules/export-data.js",
                "https://code.highcharts.com/modules/accessibility.js",
                '../../js/suportes/chat/chat.js',
                '../../js/cockpit/create-cockpit.js',
                '../../node_modules/print-js/dist/print.js',
                '../../js/dashboard/widget-15.js',
                '../../js/dashboard/widget-27.js',
                '../../js/dashboard/widget-28.js',
                '../../js/dashboard/widget-29.js',
                '../../js/dashboard/widget-30.js',
                '../../js/dashboard/widget-tabela-grafico-suporte.js',
                '../../js/dashboard/projetos-sem-leitura.js',
                '../../js/dashboard/widget-40.js',
                '../../js/dashboard/widget-plcodes-mais-lidos.js',

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

        // On document ready


        window.addEventListener('DOMContentLoaded', function() {
            $('#overlay').fadeIn();
            loadScripts();
           
        });
    </script>



    <script>
        function atualizarDiv_Tabela_Suporte() {
            // Exibe a mensagem de aguarde
            document.getElementById("mensagem-aguarde-tabela-resumo-suporte").classList.remove("d-none");
            document.getElementById("mensagem-aguarde-tabela-resumo-suporte").classList.add("d-block");

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Atualiza a tabela com o resultado da solicitação
                    document.getElementById("tabela-resumo-suporte").innerHTML = this.responseText;

                    // Remove a mensagem de aguarde
                    document.getElementById("mensagem-aguarde-tabela-resumo-suporte").classList.add("d-none");
                }
            };
            xhttp.open("GET", "../../crud/dashboard/consulta-tabela-resumo-suporte.php", true);
            xhttp.send();
        }

        // Define a função de atualização da tabela para ser executada a cada 10 segundos
        setTimeout(function() {
            atualizarDiv_Tabela_Suporte();
        }, 300);




        function atualizarDiv_Tabela_Checkin() {
    // Exibe a mensagem de aguarde
   
    setTimeout(() => {
        if ($('#tabela-resumo-suporte').is(':empty')) {
            console.log('Checkin sem dados');
        }
    }, 3500);



    $.ajax({
        url: "../../crud/dashboard/consulta-tabela-chekins-realizados.php",
        type: "GET",
        DataType: "html",
        beforeSend: function() {
            // Exibe a mensagem de aguarde
            $("#mensagem-aguarde-tabela-chekins-realizados").addClass("d-block").removeClass("d-none");
        },
        success: function(data) {

            $("#mensagem-aguarde-tabela-chekins-realizados").addClass("d-none").removeClass("d-block");
            


            if(data===''){
                console.log('Checkin sem dados');
                $("#tabela-chekins-realizados").html(data);

            }
            // Verifica se o tamanho do response é zero
          
            $("#tabela-chekins-realizados").html(data);
            


        }, error: function(data) {
                        // Remove a mensagem de aguarde
                        $("#mensagem-aguarde-tabela-chekins-realizados").addClass("d-none").removeClass("d-block");
                        $("#tabela-chekins-realizados").html(data);
            console.log(data);

        }, complete: function(data) {
            // Remove a mensagem de aguarde
            $("#mensagem-aguarde-tabela-chekins-realizados").addClass("d-none").removeClass("d-block");
        }
    });
}

// Define a função de atualização da tabela para ser executada a cada 15 segundos
setTimeout(function() {
    atualizarDiv_Tabela_Checkin();
}, 1000);


    </script>

</body>
<!--end::Body-->

</html>