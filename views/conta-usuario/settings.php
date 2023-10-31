<?php
 // buffer de saída de dados do php]
// Instancia Conexão PDO
require_once "../../conexao.php";
$conexao = Conexao::getInstance();
require_once "./../../crud/login/verifica_sessao.php";

$_SESSION['pagina_atual'] = 'Configurações da Conta';

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
    $value = 'Sentimos muito! <br/>O STEP Não Conseguiu Validar o usuário da Consulta das Configurações do Perfil, caso o Erro Persista, por gentileza entre em contato com o Suporte.';

    $_SESSION['error'] =  $value;
   
    header("Location: /views/dashboard.php");
    exit;
}


$sql_usuario = $conexao->query("SELECT * FROM usuarios WHERE id='$id_usuario'");

                                                        $conta_user = $sql_usuario->rowCount();


                                                       if($conta_user > 0){

                                                            $r = $sql_usuario->fetch(PDO::FETCH_ASSOC);

                                                            $nome_user = $r['nome'];
                                                            $brev_nome_user = substr($nome_user, 0, 1);


                                                            $bd_nome = $r['bd_nome'];

                                                            $nivel_user = $r['nivel'];

                                                            $foto = $r['foto'];

                                                         
                                                            $status_usuario = $r['status'];
                                
                                                            switch ($status_usuario) {
                                                                case 1:
                                                                    # code...
                                                                    $css = 'danger';
                                                                    $texto ='Desativar';
                                                                    $acao_usuario='desativa_conta';
                                                                    break;
                            
                                                                    case 2:
                                                                        # code...
                                                                        $css = 'success';
                                                                        $texto ='Ativar';
                                                                        $acao_usuario='reativar_conta';
                                                                        break;
                            
                                                                        case 3:
                                                                            # code...
                                                                            $css = 'warning';
                                                                            $texto ='Aguardando Ativação';
                                                                            $acao_usuario='reativar_conta';
                                                                            break;
                                                                
                                                                default:
                                                                    # code...
                                                                    break;
                                                            }

                                                            $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

                                                            $url_avatar = $actual_link.'\/foto-perfil\/'.$foto;

                                                        


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
                            <?php include_once "../../views/conta-usuario/topo-perfil.php"; ?>
                            <!--end::Navbar-->
                            <!--begin::Basic info-->
                            <div class="card mb-5 mb-xl-10">
                                <!--begin::Card header-->
                                <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
                                    <!--begin::Card title-->
                                    <div class="card-title m-0">
                                        <h3 class="fw-bold m-0">Detalhes do Perfil <?=$nivel_user;?></h3>
                                    </div>
                                    <!--end::Card title-->
                                </div>
                                <!--begin::Card header-->
                                <!--begin::Content-->
                                <div id="kt_account_settings_profile_details" class="collapse show">
                                    <!--begin::Form-->
                                    <form id="kt_form_usuario" class="form"  autocomplete="off" >
                                        <div class="card-body border-top p-9">
                                            <!--begin::Input group-->
                                            <div class="row mb-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Avatar</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-8">
                                                    <!--begin::Image input-->
                                                    <div class="image-input image-input-empty image-input-placeholder image-input-outline" id="upload_avatar" data-kt-image-input="true" style="background-image: url('assets/media/svg/avatars/blank-dark.svg')">
                                                        <!--begin::Preview existing avatar-->
                                                        
                                                        <?php

                                                        


                         
                                                            if (isset($_COOKIE['imagem_avatar_usuario']) && !is_null($_COOKIE['imagem_avatar_usuario'])) {
                                                                // Abre uma função aqui
                                                                echo '<img src="'.$_COOKIE['imagem_avatar_usuario'].'" alt="image" class="image-input-wrapper w-125px h-125px imagem_avatar_usuario" />';
                                                                
                                                            } else if ($ruf['foto'] != '') {
                                    
                                                                if (file_exists('/foto-perfil/'.$ruf['foto'])) {
                                    
                                                                    echo '<img src="/foto-perfil/'. $ruf['foto'] . '" alt="image" class="image-input-wrapper w-125px h-125px imagem_avatar_usuario" />';

                                                                } else {
                                                                    echo ' <img src="/foto-perfil/avatar.png" alt="image" class="image-input-wrapper w-125px h-125px imagem_avatar_usuario" />';
                                                                }
                                                            }
                                                            
                                    
                                    
                                                            if ($ruf['status'] == '1') {
                                                                echo '<div data-bs-toggle="tooltip" title="O acesso do usuário: '.$nome_user.', está liberado no Sistema." class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>';
                                                            } else {
                                                                echo '<div data-bs-toggle="tooltip" title="O acesso do usuário: '.$nome_user.', se encontra bloqueado no Sistema."  class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-danger rounded-circle border border-4 border-body h-20px w-20px"></div>';
                                                            }
                                                       


                                                       


                                                        if ($nivel_user == 'cliente' && $bd_nome='contatos') {

                                                            $sql_dados_user = $conexao->query("SELECT * FROM contatos 
                                                            INNER JOIN clientes ON clientes.id_cliente = contatos.id_cliente
                                                            INNER JOIN usuarios ON usuarios.bd_id = contatos.id_contato
                                                           
                                                            WHERE usuarios.id='$id_usuario'");
                                                           
                                                        }elseif ($nivel_user == 'cliente' && $bd_nome=='colaboradores'){

                                                                $sql_dados_user = $conexao->query("SELECT * FROM colaboradores 
                                                                LEFT JOIN clientes ON clientes.id_cliente = colaboradores.filial
                                                                INNER JOIN usuarios ON usuarios.bd_id = colaboradores.id_colaborador
                                                                LEFT JOIN notificacoes_usuario ON notificacoes_usuario.id_usuario = usuarios.id
                                                                WHERE usuarios.id='$id_usuario'");


                                                            }else{
                                                                $sql_dados_user = $conexao->query("SELECT * FROM colaboradores 
                                                                LEFT JOIN clientes ON clientes.id_cliente = colaboradores.filial
                                                                INNER JOIN usuarios ON usuarios.bd_id = colaboradores.id_colaborador
                                                                LEFT JOIN notificacoes_usuario ON notificacoes_usuario.id_usuario = usuarios.id
                                                                WHERE usuarios.id='$id_usuario'");
                                                            }
                                                            
                                                   




                                                        $conta_dados_user = $sql_dados_user->rowCount();



                                                        if ($conta_dados_user > 0) {

                                                            $rdu = $sql_dados_user->fetch(PDO::FETCH_ASSOC);

                                                        } else { echo "Falha na consulta dos dados do usuário"; }


                                                        ?>

                                                            <!--end::Preview existing avatar-->
                                                            <!--begin::Label-->
                                                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Selecionar avatar">
                                                                <i class="bi bi-pencil-fill fs-7"></i>
                                                                <!--begin::Inputs-->
                                                                <input type="file" name="avatar" id="avatar" accept=".png, .jpg, .jpeg" />
                                                                <input type="hidden" name="avatar_remove" />
                                                                <!--end::Inputs-->
                                                            </label>
                                                            <!--end::Label-->
                                                            <!--begin::Cancel-->
                                                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancelar avatar">
                                                                <i class="bi bi-x fs-2"></i>
                                                            </span>
                                                            <!--end::Cancel-->
                                                            <!--begin::Remove-->
                                                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remover avatar">
                                                                <i class="bi bi-x fs-2"></i>
                                                            </span>
                                                            <!--end::Remove-->

                                                    </div>
                                                    <!--end::Image input-->
                                                    <!--begin::Hint-->
                                                    <div class="form-text">Arquivos Permitidos: png, jpg, jpeg.</div>
                                                    <!--end::Hint-->
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->
                                                     
                                            <!--begin::Input group-->
                                            <div class="row mb-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Nome Completo</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-8">
                                                    <!--begin::Row-->
                                                    <div class="row">
                                                        <!--begin::Col-->
                                                        <div class="col-lg-6 fv-row">
                                                            <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Primeiro Nome" value="<?= $rdu['nome']; ?>" />
                                                        </div>
                                                        <!--end::Col-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-6 fv-row">
                                                            <input type="text" name="lname" class="form-control form-control-lg form-control-solid" placeholder="Último Nome" value="<?= $rdu['sobrenome']; ?>" />
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Row-->
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="row mb-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Empresa</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-8 fv-row">
                                                    <select class="form-select form-select-solid" data-control="select2" data-placeholder="Especifique o Cliente (Empresa), que este Usuário representa." name='company'>
                                                        <option></option>
                                                        <?php

                                                            $sql_company = $conexao->query("SELECT cli.nome_fantasia, cli.id_cliente FROM clientes cli WHERE cli.status_cadastro='1' ORDER BY nome_fantasia ASC ");
                                                            $conta_company = $sql_company->rowCount();

                                                            if ($conta_company > 0) {

                                                                foreach ($sql_company as $r) {

                                                                    if ($r['id_cliente'] == $rdu['id_cliente']) {

                                                                        echo '<option value="' . $r['id_cliente'] . '" selected>' . $r['nome_fantasia'] . '</option>';
                                                                    }

                                                                    echo '<option value="' . $r['id_cliente'] . '">' . $r['nome_fantasia'] . '</option>';
                                                                }
                                                            }
                                                        ?>

                                                    </select>

                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="row mb-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                    <span class="required">Telefone</span>
                                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="O número de Telefone precisa estar verificado."></i>
                                                </label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-8 fv-row">
                                                    <input type="text" name="phone" id="telefone_usuario_perfil" class="form-control form-control-lg form-control-solid" placeholder="Telefone Celular/WhatsApp" value="<?= $rdu['cel_corporativo']; ?>" required/>
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_two_factor_authentication">
                                                Verificar Telefone
                                                </button> 
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="row mb-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Site Empresa</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-8 fv-row">
                                                    <input type="text" name="website" class="form-control form-control-lg form-control-solid" disabled placeholder="Site Empresa" value="<?= $rdu['site_cliente']; ?>" />
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Input group-->


                                            <!--begin::Input group-->
                                            <div class="row mb-6">
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
                              <div class="row mb-6 border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
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

                                        </div>
                                        <!--end::Card body-->
                                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                                            <!--begin::Actions-->
                                            <button type="reset" class="btn btn-light btn-active-light-primary me-2">Descartar</button>
                                            <button id="kt_envia_form_usuario" class="btn btn-primary">
                                                <span class="indicator-label">
                                                    Salvar Alterações
                                                </span>
                                                <span class="indicator-progress">
                                                    Aguarde... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                            <!--end::Actions-->


                                        </div>



                                        <input type="hidden" name="acao" value="altera_usuario" />
                                        <input type="hidden" name="id" id="id_usuario_edicao" value="<?= $id_usuario; ?>" />
                                        <input type="hidden" name="bd_nome" value="<?= $rdu['bd_nome']; ?>" />
                                        <input type="hidden" name="bd_id" value="<?= $rdu['bd_id']; ?>" />
                                        <!--end::Actions-->

                                    </form>
                                    <!--end::Form-->
                                </div>
                                <!--end::Content-->
                            </div>
                            <!--end::Basic info-->
                            <!--begin::Sign-in Method-->
                            <div class="card mb-5 mb-xl-10">
                                <!--begin::Card header-->
                                <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_signin_method">
                                    <div class="card-title m-0">
                                        <h3 class="fw-bold m-0">Método de Login</h3>
                                    </div>
                                </div>
                                <!--end::Card header-->
                                <!--begin::Content-->
                                <div id="kt_account_settings_signin_method" class="collapse show">
                                    <!--begin::Card body-->
                                    <div class="card-body border-top p-9">
                                        <!--begin::Email Address-->
                                        <div class="d-flex flex-wrap align-items-center">
                                            <!--begin::Label-->
                                            <div id="kt_signin_email">
                                                <div class="fs-6 fw-bold mb-1">Endereço de E-mail</div>
                                                <div class="fw-semibold text-gray-600"><?= $rdu['email']; ?></div>
                                            </div>
                                            <!--end::Label-->
                                            <!--begin::Edit-->
                                            <div id="kt_signin_email_edit" class="flex-row-fluid d-none">
                                                <!--begin::Form-->
                                                <form id="kt_signin_change_email" class="form" novalidate="novalidate">
                                                    <div class="row mb-6">
                                                        <div class="col-lg-6 mb-4 mb-lg-0">
                                                            <div class="fv-row mb-0">
                                                                <label for="EmailAtual" class="form-label fs-6 fw-bold mb-3">Informe o E-mail Atual:</label>
                                                                <input type="email" class="form-control form-control-lg form-control-solid" id="EmailAtual" placeholder="Endereço de E-mail" name="EmailAtual" value="<?= $rdu['email']; ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="fv-row mb-0">
                                                                <label for="NovoEmail" class="form-label fs-6 fw-bold mb-3">Informe o novo E-mail:</label>
                                                                <input type="email" class="form-control form-control-lg form-control-solid" name="NovoEmail" id="NovoEmail" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex">
                                                        <button id="kt_signin_submit" type="button" class="btn btn-primary me-2 px-6">Atualizar E-mail</button>
                                                        <button id="kt_signin_cancel" type="button" class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancelar</button>
                                                    </div>

                                                    <input type="hidden" name="id" value="<?= $id_usuario; ?>" />
                                                    <input type="hidden" name="acao" value="atualiza_email" />
                                                </form>
                                                <!--end::Form-->
                                            </div>
                                            <!--end::Edit-->
                                            <!--begin::Action-->
                                            <div id="kt_signin_email_button" class="ms-auto">
                                                <button class="btn btn-light btn-active-light-primary">Alterar E-mail</button>
                                            </div>
                                            <!--end::Action-->
                                        </div>
                                        <!--end::Email Address-->
                                        <!--begin::Separator-->
                                        <div class="separator separator-dashed my-6"></div>
                                        <!--end::Separator-->
                                        <!--begin::Password-->
                                        <div class="d-flex flex-wrap align-items-center mb-10">
                                            <!--begin::Label-->
                                            <div id="kt_signin_password">
                                                <div class="fs-6 fw-bold mb-1">Senha</div>
                                                <div class="fw-semibold text-gray-600">************</div>
                                            </div>
                                            <!--end::Label-->
                                            <!--begin::Edit-->
                                            <div id="kt_signin_password_edit" class="flex-row-fluid d-none">
                                                <!--begin::Form-->
                                                <div class="form-text text-gray-700 mb-5">Caso o usuário não saiba a senha atual, o mesmo poderá solicitar o Reset da Senha, no momento do Login, na opção <strong>"Esqueceu?"</strong></div>
                                                <form id="kt_signin_change_password" class="form" novalidate="novalidate">
                                                    <div class="row mb-1">
                                                        <div class="col-lg-4">
                                                            <div class="fv-row mb-0">
                                                                <label for="currentpassword" class="form-label fs-6 fw-bold mb-3">Senha Atual</label>
                                                                <input type="password" autocomplete="current-password" class="form-control form-control-lg form-control-solid" name="currentpassword" id="currentpassword" />
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="fv-row mb-0">
                                                                <label for="newpassword" class="form-label fs-6 fw-bold mb-3">Nova Senha</label>
                                                                <input type="password"  autocomplete="new-password" class="form-control form-control-lg form-control-solid" name="newpassword" id="newpassword" />
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="fv-row mb-0">
                                                                <label for="confirmpassword" class="form-label fs-6 fw-bold mb-3">Confirme a Nova Senha</label>
                                                                <input type="password" autocomplete="confirm-password" class="form-control form-control-lg form-control-solid" name="confirmpassword" id="confirmpassword" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-text mb-5">A Senha deve conter no mínimo 8 caracteres e conter símbolos.</div>
                                                    <div class="d-flex">
                                                        <button id="kt_password_submit" type="button" class="btn btn-primary me-2 px-6">Alterar Senha</button>
                                                        <button id="kt_password_cancel" type="button" class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancelar</button>
                                                    </div>

                                                    <input type="hidden" name="id" value="<?= $id_usuario; ?>" />
                                                    <input type="hidden" name="acao" value="atualiza_senha" />
                                                </form>
                                                <!--end::Form-->
                                            </div>
                                            <!--end::Edit-->
                                            <!--begin::Action-->
                                            <div id="kt_signin_password_button" class="ms-auto">
                                                <button class="btn btn-light btn-active-light-primary">Alterar Senha</button>
                                            </div>
                                            <!--end::Action-->
                                        </div>
                                        <!--end::Password-->
                                        <!--begin::Notice-->
                                        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 d-none">
                                            <!--begin::Icon-->
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
                                            <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor" />
                                                    <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <!--end::Icon-->
                                            <!--begin::Wrapper-->
                                            <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                                <!--begin::Content-->
                                                <div class="mb-3 mb-md-0 fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">Segurança da Conta</h4>
                                                    <div class="fs-6 text-gray-700 pe-7">A autenticação de dois fatores adiciona uma camada extra de segurança à sua conta. Para fazer login, além disso, você precisará fornecer um código de 6 dígitos</div>
                                                </div>
                                                <!--end::Content-->
                                                <!--begin::Action-->
                                                <a href="javascript:;" class="btn btn-primary px-6 align-self-center text-nowrap" data-bs-toggle="modal" data-bs-target="#kt_modal_two_factor_authentication">Habilitar</a>
                                                <!--end::Action-->
                                            </div>
                                            <!--end::Wrapper-->
                                        </div>
                                        <!--end::Notice-->
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Content-->
                            </div>
                            <!--end::Sign-in Method-->
                            <!--begin::Connected Accounts-->
                            <div class="card mb-5 mb-xl-10">
                                <!--begin::Card header-->
                                <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_connected_accounts" aria-expanded="true" aria-controls="kt_account_connected_accounts">
                                    <div class="card-title m-0">
                                        <h3 class="fw-bold m-0">Contas Conectadas</h3>
                                    </div>
                                </div>
                                <!--end::Card header-->
                                <!--begin::Content-->
                                <div id="kt_account_settings_connected_accounts" class="collapse show">
                                    <!--begin::Card body-->
                                    <div class="card-body border-top p-9">
                                        <!--begin::Notice-->
                                        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                                            <!--begin::Icon-->
                                            <!--begin::Svg Icon | path: icons/duotune/art/art006.svg-->
                                            <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3" d="M22 19V17C22 16.4 21.6 16 21 16H8V3C8 2.4 7.6 2 7 2H5C4.4 2 4 2.4 4 3V19C4 19.6 4.4 20 5 20H21C21.6 20 22 19.6 22 19Z" fill="currentColor" />
                                                    <path d="M20 5V21C20 21.6 19.6 22 19 22H17C16.4 22 16 21.6 16 21V8H8V4H19C19.6 4 20 4.4 20 5ZM3 8H4V4H3C2.4 4 2 4.4 2 5V7C2 7.6 2.4 8 3 8Z" fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <!--end::Icon-->
                                            <!--begin::Wrapper-->
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <!--begin::Content-->
                                                <div class="fw-semibold">
                                                    <div class="fs-6 text-gray-700">Contas Conectadas, permitem o Login mais rápido, sem a necessidade de informar e-mail e senha no momento do acesso. O processo final de ativação ocorrerá quando o usuário realizar o primeiro acesso com a conta Google.
                                                        <a href="javascript:;" class="fw-bold" data-bs-toggle="modal" data-bs-target="#alerta_login_google">Saiba Mais</a>
                                                    </div>
                                                </div>
                                                <!--end::Content-->
                                            </div>
                                            <!--end::Wrapper-->
                                        </div>
                                        <!--end::Notice-->
                                        <!--begin::Items-->
                                        <div class="py-2">


                                        	<!--begin::Edit-->
											<div id="div_email_google_usuario" class="flex-row-fluid d-none">
												<!--begin::Form-->
												<form id="form_vincula_email_google" class="form" novalidate="novalidate">
													<div class="row mb-6">
														<div class="col-lg-6 mb-4 mb-lg-0">
                                                        
															<div class="fv-row mb-0">
																<label for="emailGoogle" class="form-label fs-6 fw-bold mb-3">E-mail da Conta Google</label>
																<input type="text" class="form-control form-control-lg form-control-solid" id="emailaddress" placeholder="@gmail.com" name="emailGoogle" value="" />
															</div>
														</div>
														<div class="col-lg-6">
															<div class="fv-row mb-0">
																<label for="confirmaEmailGoogle" class="form-label fs-6 fw-bold mb-3">Confirme o E-mail</label>
																<input type="text" class="form-control form-control-lg form-control-solid" name="confirmaEmailGoogle" id="confirmaEmailGoogle" />
															</div>
														</div>
													</div>
													<div class="d-flex ">
														<button id="bt_vincula_conta_google" type="button" class="btn btn-color-gray-400 btn-active-light-primary px-6 me-2">
                                                        <img src="assets/media/svg/brand-logos/google-icon.svg" class="w-30px me-6" alt="" />Vincular Conta Google</button>
														<button id="bt_cancela_vincula_conta_google" type="button" class="btn btn-color-gray-400 btn-active-light-dark px-6">Cancelar</button>
													</div>

                                                    <input type="hidden" name="id" value="<?= $id_usuario; ?>">
                                        <input type="hidden" name="acao"  value="vincula_conta_google">
												</form>
												<!--end::Form-->
											</div>
											<!--end::Edit-->

                                            <!--begin::Item-->
                                            <div class="d-flex flex-stack" id="div_vincula_conta_google">
                                                <div class="d-flex">
                                                    <img src="assets/media/svg/brand-logos/google-icon.svg" class="w-30px me-6" alt="" />
                                                    <div class="d-flex flex-column">
                                                        <a href="javascript:;" class="fs-5 text-dark text-hover-primary fw-bold">Google</a>
                                                        <div class="fs-6 fw-semibold text-gray-400">
                                                            Planeje adequadamente seu fluxo de trabalho</div>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <div class="form-check form-check-solid form-switch">
                                                        <input class="form-check-input w-45px h-30px" type="checkbox"  data-email_google="<?php echo $rdu['email_google'] ?? '';?>" data-id_usuario="<?=$id_usuario;?>" id="googleswitch" <?php if ($rdu['email_google']!='' || $rdu['email_google']!=NULL) { echo 'checked="checked"' ;} ?>  />
                                                        <label class="form-check-label" for="googleswitch"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Item-->
                                            <!--  -->
                                        </div>
                                        <!--end::Items-->
                                    </div>
                                    <!--end::Card body-->
                                   
                                </div>
                                <!--end::Content-->
                            </div>
                            <!--end::Connected Accounts-->

                            
                            <?php if($_COOKIE['nivel_acesso_usuario']=='admin'){




                                ?>
                            <!--begin::Desativar Conta-->
                            <div class="card">
                                <!--begin::Card header-->
                                <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_deactivate" aria-expanded="true" aria-controls="kt_account_deactivate">
                                    <div class="card-title m-0">
                                        <h3 class="fw-bold m-0">Acesso à Conta</h3>
                                    </div>
                                </div>
                                <!--end::Card header-->


                                <div id="div_nivel_acesso_perfil" class="card-body border-top p-9">
                                    <form id="form_nivel_acesso_perfil" class="form">

                                                <div class="row mb-6">
                                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">Nícel do Acesso</label>
                                                    <div class="col-lg-8 fv-row">
                                                    <select class="form-select form-select-solid" data-control="select2"  data-placeholder="Selecione uma Opção"   name="nivel_acesso" id='nivel_usuario' data-allow-clear="true"  data-hide-search="true">
                                                        
                                                        <option value="operador" <?php if($rdu['nivel']=='operador'){ echo "selected";} ?>>Operador</option>
                                                        <option value="supervisor" <?php if($rdu['nivel']=='supervisor'){ echo "selected";} ?>>Supervisor</option>
                                                        <option value="ro" <?php if($rdu['nivel']=='ro'){ echo "selected";} ?>>RO</option>
                                                        <option value="engenheiro" <?php if($rdu['nivel']=='engenheiro'){ echo "selected";} ?>>Engenharia IoT</option>
                                                        <option value="cliente" <?php if($rdu['nivel']=='cliente'){ echo "selected";} ?>>Cliente</option>
                                                        <?php if($_COOKIE['nivel_acesso_usuario']=='admin'){

                                                        echo '<option value="admin"  selected> Administrador</option>';
                                                        } ?>
                                                    
                                                    </select>
                                                    </div>
                                                </div>

                                                 <!--begin::Card footer-->
                                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                                        
                                        <button class="btn btn-light btn-active-light-primary " id="bt_altera_nivel_usuario_perfil">Atualizar Nível</button>
                                    </div>
                                    <!--end::Card footer-->

                                    <input type="hidden" name="id" value="<?= $id_usuario; ?>">
                                    <input type="hidden" name="email_usuario" value="<?= $rdu['email']; ?>">
                                        <input type="hidden" name="acao" value="altera_nivel_acesso" >
                                    </form>
                               </div>

                                <!--begin::Content-->
                                <div id="kt_account_settings_deactivate" class="collapse show">
                                    <!--begin::Form-->
                                    <form id="kt_account_deactivate_form" class="form">
                                        <!--begin::Card body-->
                                        <div class="card-body border-top p-9">
                                            <!--begin::Notice-->
                                            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
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
                                                        <h4 class="text-gray-900 fw-bold">Caso esta conta seja desativada:</h4>
                                                        <div class="fs-6 text-gray-700">Lembre-se que ao Desativar a Conta do usuário, o mesmo não poderá acessar o sistema, porém todos os logs e movimentações de Dados permanecem ativos para consulta.
                                                            <br />
                                                            <a class="fw-bold" href="javascript:;" data-bs-toggle="modal" data-bs-target="#alerta_desativa_conta">Leia mais</a>
                                                        </div>
                                                    </div>
                                                    <!--end::Content-->
                                                </div>
                                                <!--end::Wrapper-->
                                            </div>
                                            <!--end::Notice-->
                                            <!--begin::Form input row-->
                                            <div class="form-check form-check-solid fv-row">
                                                <input name="deactivate" class="form-check-input" type="checkbox" value="" id="deactivate" />
                                                <label class="form-check-label fw-semibold ps-2 fs-6" for="deactivate">Eu confirmo a alteração do acesso desta Conta.</label>
                                            </div>
                                            <!--end::Form input row-->
                                        </div>
                                        <!--end::Card body-->
                                        <!--begin::Card footer-->
                                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                                            <button id="kt_account_deactivate_account_submit" type="submit" class="btn btn-<?=$css;?> fw-semibold"><?=$texto;?></button>
                                        </div>
                                        <!--end::Card footer-->
                                        <input type="hidden" name="id" value="<?= $id_usuario; ?>">
                                        <input type="hidden" name="acao" value="<?=$acao_usuario;?>" >

                                    </form>
                                    <!--end::Form-->
                               
                                </div>
                                <!--end::Content-->
                            </div>
                            <!--end::Desativar Conta-->

                            <?php } ?>
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

            <div class="modal fade" tabindex="-1" id="alerta_desativa_conta">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Desativação de Conta</h5>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
                <!--end::Close-->
            </div>

            <div class="modal-body">
                <p>Ao realizar a desativação da conta de um usuário, é importante lembrar que o acesso ao sistema será negado a essa pessoa. Contudo, é fundamental destacar que todas as informações relacionadas às atividades e movimentações de dados realizadas por esse usuário permanecerão armazenadas no sistema e estarão disponíveis para consulta.

Por isso, é essencial que a desativação da conta seja realizada com responsabilidade e justificada por motivos legítimos, a fim de evitar possíveis problemas ou violações de segurança que possam afetar a integridade dos dados e informações do sistema.

Caso seja necessário realizar a desativação da conta de um usuário, certifique-se de que todas as informações relevantes foram previamente salvas e devidamente registradas. Além disso, é importante informar o usuário sobre a desativação da sua conta e fornecer todas as informações necessárias para que ele possa tomar as medidas necessárias, como a recuperação dos seus dados, caso necessário.

Por fim, reforçamos que a desativação da conta de um usuário deve ser realizada com cautela e apenas em situações em que isso seja realmente necessário para garantir a segurança e a integridade do sistema e das informações contidas nele.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Sair</button>
                
            </div>
        </div>
    </div>
</div>



<div class="modal fade" tabindex="-1" id="alerta_login_google">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vincular Conta Google</h5>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
                <!--end::Close-->
            </div>

            <div class="modal-body">
                <span class="d-flex flex-stack flex-grow-1">O sistema agora oferece a possibilidade de realizar o login utilizando a conta do Google, o que permite um acesso mais rápido e simplificado ao sistema, sem a necessidade de informar o e-mail e senha cadastrados inicialmente. O processo final de ativação dessa opção ocorrerá automaticamente quando o usuário realizar o primeiro acesso utilizando sua conta do Google.

É importante lembrar que, caso o usuário opte por utilizar a sua conta do Google para acessar o sistema, ele ainda terá a opção de utilizar seu e-mail e senha cadastrados inicialmente a qualquer momento. Dessa forma, o usuário pode escolher qual forma de acesso é mais conveniente para ele em determinado momento.

Além disso, caso o usuário queira revogar o acesso concedido ao login com a conta do Google, basta acessar o seu perfil no sistema e realizar essa alteração. É importante lembrar que essa ação não afetará o acesso ao sistema, mas apenas a forma de login utilizada.

Por fim, é importante destacar que a utilização da conta do Google como forma de login oferece uma alternativa mais simples e rápida para acessar o sistema, além de garantir uma camada adicional de segurança em relação ao cadastro de senha.
</span>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Sair</button>
                
            </div>
        </div>
    </div>
</div>

        </div>
        <!--end::Root-->
        <!--begin::Drawers-->
        <!--begin::Activities drawer-->
        <?php include '../../views/conta-usuario/atividade-usuario.php'; ?>
        <!--end::Activities drawer-->
        <!--end::Activities drawer-->
 <!--begin::Chat drawer-->
 <?php include '../../views/chat/chat-usuario.php'; ?>
   
             <!--begin::Modal - Create App Cockpit-->
             <?php include_once "../../views/cockpit/modal-app-cockpit.php"; ?>
        <!--end::Modal - Create App Cockpit-->
   <!--end::Chat drawer-->
        <!--end::Drawers-->
        <!--end::Main-->
        <!--begin::Engage drawers-->
       
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
        <!--begin::Modals-->
      
        <!--end::Modal - Upgrade plan-->
       
        <!--end::Modal - Create App-->
      
        <!--begin::Modal - Two-factor authentication-->
        <div class="modal fade" id="kt_modal_two_factor_authentication" tabindex="-1" aria-hidden="true">
            <!--begin::Modal header-->
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header flex-stack">
                        <!--begin::Title-->
                        <h2>Valide seu número de telefone para habilitar o envio de SMS pelo Step</h2>
                        <!--end::Title-->
                        <!--begin::Close-->
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
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
                    <!--begin::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body scroll-y pt-10 pb-15 px-lg-17">
                        <!--begin::Options-->
                        <div data-kt-element="options">
                            <!--begin::Notice-->
                            <p class="text-muted fs-5 fw-semibold mb-10">Aceitando nossos termos de envio de sms, clique primeiro sobre a opção "Para qual número enviaremos.." e depois clique em "Continuar".</p>
                            <!--end::Notice-->
                            <!--begin::Wrapper-->
                            <div class="pb-10">
                            
                                <!--begin::Option-->
                                <input type="radio" class="btn-check" name="auth_option" value="sms" id="kt_modal_two_factor_authentication_option_2" />
                                <label class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center" for="kt_modal_two_factor_authentication_option_2">
                                    <!--begin::Svg Icon | path: icons/duotune/communication/com003.svg-->
                                    <span class="svg-icon svg-icon-4x me-4">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M2 4V16C2 16.6 2.4 17 3 17H13L16.6 20.6C17.1 21.1 18 20.8 18 20V17H21C21.6 17 22 16.6 22 16V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4Z" fill="currentColor" />
                                            <path d="M18 9H6C5.4 9 5 8.6 5 8C5 7.4 5.4 7 6 7H18C18.6 7 19 7.4 19 8C19 8.6 18.6 9 18 9ZM16 12C16 11.4 15.6 11 15 11H6C5.4 11 5 11.4 5 12C5 12.6 5.4 13 6 13H15C15.6 13 16 12.6 16 12Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <span class="d-block fw-semibold text-start">
                                        <span class="text-dark fw-bold d-block fs-3">Para qual número enviaremos as mensagens de Whatsapp e SMS?</span>
                                        <span class="text-muted fw-semibold fs-6"><b>Aceite</b>: Respeitando as normas da LGPD, estou consciente que o Sistema usará este recurso para notificações do sistema.</span>
                                    </span>
                                </label>
                                <!--end::Option-->
                            </div>
                            <!--end::Options-->
                            <!--begin::Action-->
                            <button class="btn btn-primary w-100" data-kt-element="options-select">Continuar</button>
                            <!--end::Action-->
                        </div>
                        <!--end::Options-->
                        <!--begin::Apps-->

                        <!--begin::SMS-->
                        <div class="d-none" data-kt-element="sms">
                            <!--begin::Heading-->
                            <h3 class="text-dark fw-bold fs-3 mb-5">SMS: Verifique seu número de celular</h3>
                            <!--end::Heading-->
                            <!--begin::Notice-->
                            <div class="text-muted fw-semibold mb-10">Digite o número do seu celular <b>(Preferêncialmente deve ser seu número do Perfil. e que deve ter whatsapp neste número)</b>, com o prefixo do seu estado e nós lhe enviaremos um código de para verificação .</div>
                            <!--end::Notice-->
                            <!--begin::Form-->
                            <form data-kt-element="sms-form" class="form" action="#">
                                <!--begin::Input group-->
                                <div class="mb-10 fv-row">
                                    <input type="text" class="form-control form-control-lg form-control-solid"  placeholder="Número de celular com o prefixo do seu estado (15) 9999..." name="mobile" id="mobile" />
                                </div>
                                <!--end::Input group-->
                                <!--begin::Actions-->
                                <div class="d-flex flex-center">
                                    <button type="reset" data-kt-element="sms-cancel" class="btn btn-light me-3">Cancelar</button>
                                    <button type="submit" data-kt-element="sms-submit" class="btn btn-primary">
                                        <span class="indicator-label">Enviar</span>
                                        <span class="indicator-progress">Por favor, aguarde...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                </div>
                                <!--end::Actions-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::SMS-->
                      
                        <!--begin::Apps-->
                        
                        
                        	<!--begin::Form-->
							<div class="d-none" data-kt-element="apps">
									<!--begin::Content-->
									<div class="fw-semibold">
										<div class="fs-6 text-gray-700">Informe o SMS recebdio no seu celular</div>
										<div class="fw-bold text-dark pt-2"></div></div>
									
									<!--end::Content-->
								
                                    <form data-kt-element="apps-form" class="form" action="#" name="apps-form" id="apps-form">
								<!--begin::Input group-->
								<div class="mb-10 fv-row">
									<input type="text" class="form-control form-control-lg form-control-solid" placeholder="Informe o SMS recebido" name="code" id='code' />
								</div>
								<!--end::Input group-->
								<!--begin::Actions-->
								<div class="d-flex flex-center">
									<button type="reset" data-kt-element="apps-cancel" class="btn btn-light me-3">Cancelar</button>
									<button type="submit" data-kt-element="apps-submit" id = "valida-sms" class="btn btn-primary">
										<span class="indicator-label">Validar SMS</span>
										<span class="indicator-progress">Por favor, aguarde...
										<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
									</button>
								</div>
								<!--end::Actions-->
							</form>
							<!--end::Form-->
						</div>
								<!--end::Apps-->
                                     
							</div>
							<!--end::Notice-->
						
                      
                        
                    </div>
                    <!--begin::Modal body-->
                </div>
                <!--end::Modal content-->
            </div>
            <!--end::Modal header-->
        </div>
        <!--end::Modal - Two-factor authentication-->


  



<?php } ?>
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
        <script src="/js/usuarios/two-factor-authentication.js"></script>
        <script src="../../js/dashboard/step-js.js"></script>
        <script src="../../js/usuarios/signin-methods.js"></script>
        
        <script src="../../js/usuarios/profile-details.js"></script>
        <script src="../../js/usuarios/deactivate-account.js"></script>
        <script src="assets/js/widgets.bundle.js"></script>
        <script src="assets/js/custom/widgets.js"></script>
        <script src="../../js/suportes/chat/chat.js"></script>
        

        
<script>

    // Phone
Inputmask({
    "mask" : "(99) 99999-9999"
}).mask("#mobile");

</script>

       
    


    </body>
    <!--end::Body-->

    </hmtl>