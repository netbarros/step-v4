<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO  $conexao = Conexao::getInstance();
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION)) session_start();

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



setcookie('acceptCookies', 'true',time() + (86400 * 365), "/"); // 86400 = 1 day


$email = trim(isset($_POST['email'])) ? $_POST['email'] : '' ;
$senha = trim(isset($_POST['password'])) ? $_POST['password'] : '' ;


//===[ CHAVE ÚNICA da SESSAO] a cada acesso, o step registra uma codificação única de 32bits, encriptografada, de acesso unico para o login do usuário e logout do mesmo.
/* para cada nova leitura, é gerada uma chave_unica_sessao_atual, está é para mapearmos a rota desde a leitura do plcode e o que o usuário fez em sequência, 
checkin, abriu suporte, fez envio normal da leitura do plcode lido, enviou imagens nas leituras ou no suporte e ou reabertura de plcode, a chave_unica_sessao,
vinculará cada rotina do usuário, desde o início da etapa até a sua conclusão e leitura do próximo plcode, onde uma nova chave será gerada para o novo acompanhamento
da nova rotina do plcode lido, que se iniciará.
*/
$horario_completo_agora = microtime();
/* INÍCIO: Crio a Chave unica da Sessao para armazenamento e resgate das leituras e imagens que serão enviadas */          
$chave_unica = bin2hex(random_bytes(33).$horario_completo_agora);  // generates 64 characters long string /^[0-9a-f]{64}$/ + microtime
/* Gerar strings aleatórias criptograficamente seguras, usamos 24 caracteres não repetitivos e aleatórios com criptografia nativa do PHP";
 serve como id referencial para salvar a midia e após salvar as leituras por essa chave que tbem constará na tb rmm, vinculo o id_rmm na tb midia_leitura,
  com a mesma chave unica (para controlar cada leitura enviada individualmente e não misturar as imagens enviadas) assim tbem poderemos vincular as midias 
  enviadas com um novo suporte gerado pelo painel da leitura e ver a relação entre elas, por imagens*/

$cookie_name = "CHAVE_UNICA_SESSAO_ATUAL";
$cookie_value = $chave_unica;

// 86400 = 1 day
setcookie($cookie_name, $cookie_value, time() + (86400), "/"); 


$estacao_atual='0';



// Dica 5 - Válida os dados do usuário com o banco de dados
$sql = 'SELECT * FROM usuarios WHERE email = ? LIMIT 1';
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $email);
$stm->execute();
$consulta = $stm->fetch(PDO::FETCH_OBJ);

if(!empty($consulta) && password_verify($senha, $consulta->senha)){

	$id_usuario= $consulta->id;
  	$nome= $consulta->nome;
	$email = $consulta->email;
   	$nivel = $consulta->nivel;
	$foto= $consulta->foto;
	$status= $consulta->status;  
    
  $_SESSION['id'] = $id_usuario;



    if(password_verify($senha, $consulta->senha)){

$data_acesso = date_create()->format('Y-m-d H:i:s'); 
$sql = $conexao->query("UPDATE usuarios SET ultimo_acesso='$data_acesso' WHERE id='$id_usuario'");
	

  //  faço a gravação do log
 $acao_log = "Novo Login";
$tipo_log = '27'; // Acessou o Sistema

$sql_log = "INSERT INTO log_leitura (
  chave_unica,
   id_usuario, 
   acao_log,
   estacao_logada,
   tipo_log) 
VALUES (
      ?,
      ?,
      ?,
      ?,
      ?
      )";
$conexao->prepare($sql_log)->execute([
    $chave_unica,
    $id_usuario,
    $acao_log,
    $estacao_atual,
    $tipo_log ]);
 //  faço a gravação do log  
 
 
 $_SESSION['id'] = $id_usuario;


//echo "O navegador usado é ".$navegador.", versão ".$versao;
// email para os membros do projeto



                $retorno = array('codigo' => 1, 'id_usuario'=> $_SESSION['id'], 'nome'=> $nome, 'email' =>  $email, 'nivel' => $nivel, 'foto'=> $foto, 'status'=> $status);

                echo json_encode($retorno);


              

                } else {

                $retorno = array('codigo' => 0, 'mensagem' => 'senha inválida');

                echo json_encode($retorno);
               
                }

               
} else {

            $retorno = array('codigo' => '0','mensagem'=>'Login Inválido!');
             echo json_encode($retorno);
             exit();

             
}