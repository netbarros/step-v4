<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

$_SESSION['pagina_atual'] = 'Altera Nivel de Acesso do Projeto';

$acao = $_GET['acao'] ?? '';


$cookie_expiration = time() + (86400 * 1); // Define o tempo de expiração do cookie para 1 dia
$cookie_path = "/"; // Define o caminho do cookie
$cookie_domain = ""; // Insira seu domínio aqui, se necessário
// Verifica se está no ambiente de desenvolvimento local
$is_localhost = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

// Ajusta as configurações do cookie com base no ambiente
$cookie_secure = $is_localhost ? false : true; // Desabilita o atributo "secure" no ambiente local
$cookie_httponly = true; // Define o atributo "httponly"


if($acao = "altera_nivel_projeto" ){
// Nível de acesso do usuário atualizado:
$cookie_name = 'nivel_acesso_usuario';
$cookie_value = trim(isset($_GET['nivel_acesso_usuario'])) ? $_GET['nivel_acesso_usuario'] : ''; // Novo nível de acesso

setcookie($cookie_name, $cookie_value, [
    'expires' => $cookie_expiration,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // or 'Strict' or 'None'
]);

}   


if($acao = "limpa_filtro_projeto" ){
    // Nível de acesso do usuário atualizado:
    $cookie_name = 'nivel_acesso_usuario';
    $cookie_value = trim(isset($_SESSION['nivel'])) ? $_SESSION['nivel'] : ''; // Novo nível de acesso
    
    setcookie($cookie_name, $cookie_value, [
        'expires' => $cookie_expiration,
        'path' => $cookie_path,
        'domain' => $cookie_domain,
        'secure' => $cookie_secure,
        'httponly' => $cookie_httponly,
        'samesite' => 'Lax', // or 'Strict' or 'None'
    ]);

    $cookie_name = 'usuario_ticket';
    $cookie_value = ''; // Novo nível de acesso

    setcookie($cookie_name, $cookie_value, [
        'expires' => $cookie_expiration,
        'path' => $cookie_path,
        'domain' => $cookie_domain,
        'secure' => $cookie_secure,
        'httponly' => $cookie_httponly,
        'samesite' => 'Lax', // or 'Strict' or 'None'
    ]);


    $cookie_name = 'id_tipo_suporte_ticket';
    $cookie_value = ''; // Novo nível de acesso

    setcookie($cookie_name, $cookie_value, [
        'expires' => $cookie_expiration,
        'path' => $cookie_path,
        'domain' => $cookie_domain,
        'secure' => $cookie_secure,
        'httponly' => $cookie_httponly,
        'samesite' => 'Lax', // or 'Strict' or 'None'
    ]);


    $cookie_name = 'mailkey_ticket';
    $cookie_value = ''; // Novo nível de acesso

    setcookie($cookie_name, $cookie_value, [
        'expires' => $cookie_expiration,
        'path' => $cookie_path,
        'domain' => $cookie_domain,
        'secure' => $cookie_secure,
        'httponly' => $cookie_httponly,
        'samesite' => 'Lax', // or 'Strict' or 'None'
    ]);


    $cookie_name = 'projeto_ticket';
    $cookie_value = ''; // Novo nível de acesso

    setcookie($cookie_name, $cookie_value, [
        'expires' => $cookie_expiration,
        'path' => $cookie_path,
        'domain' => $cookie_domain,
        'secure' => $cookie_secure,
        'httponly' => $cookie_httponly,
        'samesite' => 'Lax', // or 'Strict' or 'None'
    ]);


    
    } 

