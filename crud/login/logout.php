<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();

date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}



$user_logado = $_SESSION['logado'] ?? 'NAO';

if ($user_logado == 'SIM') {
    displayErrorMessageAndRedirect();
    exit;
}
if($user_logado == 'NAO'){
    header("Location: /views/login/sign-in.php");
    
    $value = 'Sentimos muito! <br/>O STEP Não Conseguiu Validar seu Login Ativo na Sessão, Por gentileza, refaça seu Login.';
    $_SESSION['error'] =  $value;
    exit;
}

$projectId = $_COOKIE['projeto_atual'] ?? '0';
$activePage = $_SESSION['pagina_atual'] ?? 'Sem_pagina';
$action = 'LOGOUT';
$history = "O usuário {$userName}, Saiu do Sistema";
$ipAddress = $_SERVER['REMOTE_ADDR'];

if (!empty($userId) && !empty($userLevel) && !empty($uniqueKey)) {
    try {
        insertLog($conexao, $userId, $projectId, $userLevel, $ipAddress, $action, $activePage, $history, $uniqueKey);
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
        exit;
    }

    clearSessionAndCookies();
}

header("Location: /");
exit;

function generateUniqueKey($userName, $userId)
{
    $currentDate = date("d-m-Y");
    $currentTime = date("H:i");
    return md5($currentDate . $currentTime . $userName . $userId);
}

function displayErrorMessageAndRedirect()
{
    echo <<<EOD
<script>
    setTimeout(() => {
        window.location.replace("../../views/login/sign-in.php");
    }, 5000);

    var counter = 5;
    var countdown = setInterval(function() {
        document.getElementById("countdown").innerText = "Redirecionando em " + counter + " segundos...";
        counter--;
        if (counter < 0) {
            clearInterval(countdown);
        }
    }, 1000);
</script>
<div>Prezado Usuário, Você saiu do sistema antes que todos seus dados fossem armazenados em sua sessão. Você será redirecionado para a tela de login.</div>
<div id="countdown">Redirecionando em 5 segundos...</div>
EOD;
}

function insertLog($conexao, $userId, $projectId, $userLevel, $ipAddress, $action, $activePage, $history, $uniqueKey)
{
    $sql = "INSERT INTO log_sistema (usuario, id_obra, id_estacao, nivel, ip, acao, rotina, historico, chave_unica) 
            VALUES (:usuario, :id_obra, :id_estacao, :nivel, :ip, :acao, :rotina, :historico, :chave_unica)";
    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(':usuario', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':id_estacao', $projectId, PDO::PARAM_STR);
    $stmt->bindValue(':id_obra', $projectId, PDO::PARAM_INT);
    $stmt->bindValue(':nivel', $userLevel, PDO::PARAM_STR);
    $stmt->bindValue(':ip', $ipAddress, PDO::PARAM_STR);
    $stmt->bindValue(':acao', $action, PDO::PARAM_STR);
    $stmt->bindValue(':rotina', $activePage, PDO::PARAM_STR);
    $stmt->bindValue(':historico', $history, PDO::PARAM_STR);
    $stmt->bindValue(':chave_unica', $uniqueKey, PDO::PARAM_STR);
    $stmt->execute();
}
function clearSessionAndCookies()
{
    session_destroy();

    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach ($cookies as $cookie) {
            $eqPos = strpos($cookie, '=');
            if ($eqPos !== false) {
                $name = trim(substr($cookie, 0, $eqPos));
                setcookie($name, '', time() - 1000);
                setcookie($name, '', time() - 1000, '/');
            }
        }
    }
}

?>
