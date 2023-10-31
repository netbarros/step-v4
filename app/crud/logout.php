<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
 }

// unset cookies
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
}

//$chave_unica_sessao = defined('Chave_Unica_Sessao_Atual');



session_destroy();

setcookie('acceptCookies', 'true',time() + (86400 * 365), "/"); // 86400 = 1 day

//unset($chave_unica_sessao); //unset() destrói a variável especificada. (variável local será destruída)

   // clearstatcache(); // serve para limpar o cache do php onde a chave da leitura em vigor está instanciada

header("Location: /");