<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Turn ON error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


$redirectUrl = "./views/dashboard.php"; // URL padrão

$id_tipo_suporte_ticket = $_GET['t'] ?? '';
$usuario_ticket = $_GET['u'] ?? '';
$projeto_ticket = $_GET['p'] ?? '';

if ($id_tipo_suporte_ticket !== '' && $usuario_ticket !== '' && $projeto_ticket !== '') {
    $horario_completo_agora = microtime();
    $mailkey_ticket = bin2hex(random_bytes(33).$horario_completo_agora);

    $cookie_options = [
        'expires' => time() + (86400 * 1), // Expira em 1 dia
        'path' => "/", 
        'domain' => "", 
        'secure' => !in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']), 
        'httponly' => false,
        'samesite' => 'Lax' // ou 'Strict' ou 'None'
    ];

    setcookie('id_tipo_suporte_ticket', $id_tipo_suporte_ticket, $cookie_options);
    setcookie('usuario_ticket', $usuario_ticket, $cookie_options);
    setcookie('mailkey_ticket', $mailkey_ticket, $cookie_options);
    setcookie('projeto_ticket', $projeto_ticket, $cookie_options);

    $redirectUrl = "./views/relatorios/relatorios-suportes.php?$mailkey_ticket";
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
<!--begin::Head-->

<head>
    <base href="/">
    <title>STEP &amp; GrupoEP</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="pt_BR" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="STEP &amp; GrupoEP" />
    <meta property="og:url" content="https://grupoep.com/eptech" />
    <meta property="og:site_name" content="STEP | GrupoEP" />
    <link rel="canonical" href="https://step.eco.br" />
    <link rel="shortcut icon" href="./tema/dist/assets/media/logos/favicon.ico" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="./tema/dist/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="./tema/dist/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->

    
	<noscript>
    <p>Javascript DESABILITADO no navegador!</p>
    <meta http-equiv="refresh" content="2; https://support.google.com/adsense/answer/12654?hl=pt-BR">
  </noscript>
  
    
    <script src="https://accounts.google.com/gsi/client" async defer></script>

   
 
</head>
<!--end::Head-->
<!--begin::Body-->


<body data-kt-name="metronic" id="kt_body" class="app-blank bgi-size-cover bgi-position-center bgi-no-repeat">
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
        <!--begin::Page bg image-->
        <style>
            body {
                background-image: url('./tema/dist/assets/media/auth/bg4.jpg');
            }

            [data-theme="dark"] body {
                background-image: url('./tema/dist/assets/media/auth/bg4-dark.jpg');
            }
        </style>
        <!--end::Page bg image-->
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-column-fluid flex-lg-row">
            <!--begin::Aside-->
            <div class="d-flex flex-center w-lg-50 pt-15 pt-lg-0 px-10">
                <!--begin::Aside-->
                <div class="d-flex flex-column">
                    <!--begin::Logo-->
                    <a href="javascript:;" class="mb-7">
                        <img alt="Logo" src="./tema/dist/assets/media/logos/logo-4.png" />
                    </a>
                    <!--end::Logo-->
                    <!--begin::Title-->
                    <h2 class="text-white fw-normal m-0">Sistema de Tratamento EP</h2>
                   
                    <!--end::Title-->
                    
                </div>
                <!--begin::Aside-->
            </div>
            <!--begin::Aside-->
            <!--begin::Body-->
            <div class="d-flex flex-center w-lg-50 p-10">
                <!--begin::Card-->
                <div class="card rounded-3 w-md-550px">
                    <!--begin::Card body-->
                    <div class="card-body p-10 p-lg-20">
                        <!--begin::Form-->

                        <!--begin::Heading-->
                        <div class="text-center mb-11">

                        
                            

                        <?php
                        if(isset($_SESSION['error']) && $_SESSION['error'] != ''){
                            echo '<div class="alert alert-danger" role="alert">';
                            echo $_SESSION['error'];
                            echo '</div>';
                            unset($_SESSION['error']);
                        }
                        ?>

                            <!--begin::Title-->
                            <h1 class="text-dark fw-bolder mb-3">Entrar </h1>
                            <!--end::Title-->
                            <!--begin::Subtitle-->
                            <div class="text-gray-500 fw-semibold fs-6">Com Suas Redes Sociais </div>
                            <!--end::Subtitle=-->
                        </div>
                        <!--begin::Heading-->
                        <!--begin::Login options-->
                        <div class="row g-3 mb-9">
                            <!--begin::Col-->
                            <div class="col-md-12">


<div id="g_id_onload"
     data-client_id="534372017429-5age2233q5jrop847ac6tciend6r2c32.apps.googleusercontent.com"
     data-callback="handleCredentialResponse"
     data-context="signin"
     data-ux_mode="popup"
     data-login_uri="https://step.eco.br/views/login/sign-in.php"
     data-nonce=""
     data-auto_prompt="false">
</div>

<div class="g_id_signin"
     data-type="standard"
     data-shape="rectangular"
     data-theme="outline"
     data-text="signin_with."
     data-size="large"
     data-logo_alignment="left">
</div>
                            <!--end::Col-->

<!--  <div class="g_id_signout"><button id="signout_button">Sign Out Google</button></div>
    <script>
       const button = document.getElementById('signout_button');
    button.onclick = () => {
      google.accounts.id.disableAutoSelect();
    }
    </script> -->



                        </div>
                        <!--end::Login options-->
                        <!--begin::Separator-->
                        <div class="separator separator-content my-14">
                            <span class="w-125px text-gray-500 fw-semibold fs-7">Ou com o Seu Email</span>
                        </div>
                        <!--end::Separator-->
                        <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" data-kt-redirect-url="<?php echo $redirectUrl; ?>" action="#">
                            <!--begin::Input group=-->
                            <div class="fv-row mb-8">
                                <!--begin::Email-->
                                <input type="text" placeholder="Email" name="email" autocomplete="off" class="form-control bg-transparent" />
                                <!--end::Email-->
                            </div>
                            <!--end::Input group=-->
                            <div class="fv-row mb-3" data-kt-password-meter="true">
                                <!--begin::Password-->
                                <input type="password" placeholder="Password" name="password" autocomplete="off" class="form-control bg-transparent" />
                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                    <i class="bi bi-eye-slash fs-2"></i>
                                    <i class="bi bi-eye fs-2 d-none"></i>
                                </span>
                                <!--end::Password-->
                            </div>
                            <!--end::Input group=-->
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                <div></div>
                                <!--begin::Link-->
                                <a href="./views/login/reset-password.html" class="link-primary">Esqueceu? </a>
                                <!--end::Link-->
                            </div>
                            <!--end::Wrapper-->
                            <!--begin::Submit button-->
                            <div class="d-grid mb-10">
                                <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label">Entrar </span>
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">Aguarde ...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    <!--end::Indicator progress-->
                                </button>

                              
                            </div>
                            <!--end::Submit button-->
                            <!--begin::Sign up-->
                            <div class="text-gray-500 text-center fw-semibold fs-6">Não possui acesso?
                                <a href="./views/login/sign-up.html" class="link-primary">Solicitar
                                </a>
                            </div>
                            <!--end::Sign up-->

                            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                            <input type="hidden" name="action" value="login">

                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Body-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
    <!--end::Root-->
    <!--end::Main-->
   
    <!--begin::Javascript-->
    <script>
        var hostUrl = "assets/";
    </script>
    <!--begin::Global Javascript Bundle(used by all pages)-->
    <script src="./tema/dist/assets/plugins/global/plugins.bundle.js"></script>
    <script src="./tema/dist/assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Custom Javascript(used by this page)-->
    <script src="./js/login/sign-in/general.js"></script>

    <script src="./js/login/google/login-google.js"></script>

    <script> 

    KTCookie.remove("plcode_lido");
    KTCookie.remove("plcode");
    </script>


<script>
    
  setTimeout(function(){         
           
    function getCookie(name) {
    let cookies = document.cookie.split(';');
    for(let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i];
        let [key, value] = cookie.split('=').map(c => c.trim());
        if (key === name) {
            return decodeURIComponent(value);
        }
    }
    return null;
}


                                  // Recuperar valores dos cookies
let ticket = getCookie('id_tipo_suporte_ticket');
let usuario = getCookie('usuario_ticket');
let mailkey = getCookie('mailkey_ticket');
let projeto_ticket = getCookie('projeto_ticket');

                                // Validar os valores dos cookies
                                if (
                                    ticket !== null && 
                                    usuario !== null && 
                                    mailkey !== null && 
                                    projeto_ticket !== null
                                ) {  
    console.log("Variáveis dos cookies válidas. Redirecionando para a página de destino.");
    console.log("Cookies tipo_suporte e usuario são válidos.");
    console.log("tipo_suporte:", ticket);
    console.log("Usuario:", usuario);
} else {
    console.log("Acesso direto ao Dashboard");
}

  }, 1000);


          
        
   

</script>

    <!--end::Custom Javascript-->
    <!--end::Javascript chave do site 6LdOpFYlAAAAAFCjPSYM0IrfeTp48hWNZsU200DS // chave secreta 6LdOpFYlAAAAALlC5-V1526EL0hzevomT4N1Hy60--> 
</body>
<!--end::Body-->

</html>