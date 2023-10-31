<?php
session_start();

sleep(2);

// Obtém os valores dos parâmetros 't' e 'u' da URL
$id_tipo_suporte_ticket = (isset($_GET['t']) && !empty($_GET['t'])) ? $_GET['t'] : '';
$usuario_ticket =  (isset($_GET['u']) && !empty($_GET['u'])) ? $_GET['u'] : '';
$projeto_ticket =  (isset($_GET['p']) && !empty($_GET['p'])) ? $_GET['p'] : '';

$redirectUrl = "/views/login/sign-in.php";

$redirectParams = [];
if($id_tipo_suporte_ticket != '' && $usuario_ticket != ''){
    $redirectParams = ['suporte' => 1, 't' => $id_tipo_suporte_ticket, 'u' => $usuario_ticket , 'p' => $projeto_ticket];
} else {
    $redirectParams = ['direct' => 1];
}


?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof(Storage) === "undefined" || !navigator.cookieEnabled) {
        document.write("<p>Javascript DESABILITADO no navegador!</p>");
        setTimeout(() => {
            window.location.href = "https://support.google.com/adsense/answer/12654?hl=pt-BR";
        }, 2000);
    } else {
        // Verificar se os cookies estão habilitados
        document.cookie = "Cookies Habilitados";
        var cookiesEnabled = document.cookie.indexOf("Cookies Habilitados") != -1;
        let redirectParams = <?php echo json_encode($redirectParams); ?>;
        const queryString = new URLSearchParams(redirectParams).toString();
        window.location.replace("<?php echo $redirectUrl; ?>?" + queryString);
    }
});
</script>



