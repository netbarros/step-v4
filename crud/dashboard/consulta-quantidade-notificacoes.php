<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$_SESSION['pagina_atual'] = 'Dashboard Usuários';

$usuario_sessao = isset($_COOKIE['id_usuario_sessao']) ? trim($_COOKIE['id_usuario_sessao']) : null;

$usuario_sessao = $conexao->quote($usuario_sessao);  // Proteger contra SQL injection

//Erro de Digitação na Leitura 87
//Revogação de Indicador Durante a Leitura 92
//Indicador Liberado para Leitura 95

if($usuario_sessao != null && $usuario_sessao != ''){

    $usuario_sessao = $conexao->quote($usuario_sessao);  // Proteger contra SQL injection
$sql = $conexao->query("SELECT COUNT(DISTINCT s.id_suporte) as qtdNotificacoes FROM suporte s
INNER JOIN suporte_conversas sc ON sc.id_suporte = s.id_suporte
LEFT JOIN notificacoes_usuario nf ON nf.id_obra = s.obra
WHERE s.status_suporte != '4'   AND (sc.destinatario_direto = $usuario_sessao OR nf.id_usuario=$usuario_sessao) GROUP BY s.id_suporte");

                                                    $conta = $sql->rowCount();


                                                    if ($conta > 0) {

                                                        $row = $sql->fetch(PDO::FETCH_ASSOC);


                                                        $qtdNotificacoes =    $row['qtdNotificacoes'];


                                                       
// Depois, retorne o resultado em formato JSON:
    $resultado = array('qtdNotificacoes' => $qtdNotificacoes);
    echo json_encode($resultado, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
                                                       
} else {

    $resultado = array('qtdNotificacoes' => 0);
    echo json_encode($resultado, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);

}

} else {

    $resultado = array('qtdNotificacoes' => 0);
    echo json_encode($resultado, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);

}

?>