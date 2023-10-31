<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('America/Sao_Paulo');

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/conexao.php';

$conexao = Conexao::getInstance();
//validateHeader();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
 }



// Recebe os dados do formulário
$email = trim(isset($_POST['email'])) ? $_POST['email'] : '' ;
$senha = trim(isset($_POST['password'])) ? $_POST['password'] : '' ;

// Validações de preenchimento e-mail e senha se foi preenchido o e-mail

if (empty($email)):
	$retorno = array('codigo' => 0, 'mensagem' => 'Preencha seu e-mail!');
	echo json_encode($retorno);
	exit();
endif;

if (empty($senha)):
	$retorno = array('codigo' => 0, 'mensagem' => 'Preencha sua senha!');
	echo json_encode($retorno);
	exit();
endif;


//  Verifica se o formato do e-mail é válido
if (!filter_var($email, FILTER_VALIDATE_EMAIL)):
    $retorno = array('codigo' => 0, 'mensagem' => 'Formato de e-mail inválido!');
	echo json_encode($retorno);
	exit();
endif;

// Functions
function validateHeader() {
    if ($_SERVER['HTTP_X_MY_CUSTOM_HEADER'] !== 'Valor_Seguro') {
        // Acesso negado
        exit;
    }
}


//  Válida os dados do usuário com o banco de dados
$sql = 'SELECT * FROM usuarios WHERE email = ?  LIMIT 1';
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $email);
$stm->execute();
$retorno = $stm->fetch(PDO::FETCH_OBJ);

if(empty($retorno)){

	$retorno = array('codigo' => 0, 'mensagem' => 'Os Dados Informados são inválidos, por gentileza, verifique suas credenciais.');
	echo json_encode($retorno);
	exit();

}

if($retorno->status=='2'){ // inativo

    $retorno = array('codigo' => 0, 'mensagem' => 'Infelizmente seu acesso ao Sistema está Bloqueado no momento.<br><br>Caso tenha dúvidas, por gentileza, entre em contato com o Suporte.');
	echo json_encode($retorno);
	exit();
}



if($retorno->status=='3'){ // Aguardando Ativação

    $retorno = array('codigo' => 0, 'mensagem' => "Prezado, $retorno->nome. Você ainda não verificou seu e-mail de cadastro para ativar seu acesso inicial. <br><br>Caso tenha dúvidas, solicite a recuperação de senha e em seguida, verifique seu e-mail <span class='text-primary'> $retorno->email</span>, e procure por: <span class='text-warning'> webmaster@step.eco.br</span>.<br><br><small class='text-gray-500'> Prezamos sempre pela segurança e privacidade dos seus Dados. </small>");
	echo json_encode($retorno);
	exit();
}
$data_atual = date('Y-m-d H:i:s');
$timestamp_atual = strtotime($data_atual);
$timestamp_ultimo_acesso = strtotime($retorno->ultimo_acesso);

// Obter a data e hora por extenso em português
$data_hora_extenso = date('d/m/Y H:i:s', $timestamp_ultimo_acesso);

// Calcular a diferença entre as datas em segundos
$diferenca_segundos = $timestamp_atual - $timestamp_ultimo_acesso;

// Verificar se a diferença em segundos é maior que 60 dias (60 * 24 * 60 * 60 segundos)
if ($diferenca_segundos > (60 * 24 * 60 * 60)) {
  // Faça alguma ação aqui se a diferença entre a data atual e a data de último acesso for maior que 60 dias

  $retorno = array('codigo' => 0, 'mensagem' => "Prezado, $retorno->nome. Seu último acesso ao STEP foi em <span class='text-warning'>$data_hora_extenso</span>.<br><br>Por questões de segurança, seu acesso está <span class='text-danger'> bloqueado</span> no Sistema, caso tenha dúvidas, entre em contato com: <span class='text-warning'> webmaster@step.eco.br</span>.<br><br><small class='text-gray-500'> Prezamos sempre pela segurança e privacidade dos seus Dados. </small>");
  echo json_encode($retorno);
  exit();

}



//$senhax=password_verify($senha, $retorno->senha);
//var_dump($retorno);
// Dica 6 - Válida a senha utlizando a API Password Hash
if(!empty($retorno) && password_verify($senha, $retorno->senha)):

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
  enviadas com um novo suporte gerado pelo painel da leitura e ver a relação entre elas, por <imagens></imagens> */


  
$estacao_atual='0';

//  faço a gravação do log
$acao_log = "Acesso Sistema via APP";
$tipo_log = '54'; // Acesso Sistema via APP

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
    $retorno->id,
    $acao_log,
    $estacao_atual,
    $tipo_log ]);
 //  faço a gravação do log    	

else:
	$_SESSION['logado'] = 'NAO';

endif;


// Se logado envia código 1, senão retorna mensagem de erro para o login
if ($_SESSION['logado'] == 'SIM'){
	

	$ponto_lido = (isset($_SESSION['ponto_lido'])) ? $_SESSION['ponto_lido'] : '';

	$data_acesso = date_create()->format('Y-m-d H:i:s'); 

	$sql = $conexao->query("UPDATE usuarios SET ultimo_acesso='$data_acesso' WHERE id='$retorno->id'");
// Armazenamento das Variáveis Globais de quem está logando:



$lista_navegadores = array("Edge", "Trident", "OPR", "Edg", "Chrome", "CriOS", "FxiOS", "Firefox", "MSIE", "Safari", "Opera", "Mozilla", "AppleWebKit", "Silk", "SamsungBrowser", "UCBrowser", "YaBrowser", "UCWEB");

$navegador_usado = $_SERVER["HTTP_USER_AGENT"];
$navegador = "Desconhecido";
$versao = "Desconhecida";

foreach ($lista_navegadores as $valor_verificar) {
    if (strpos($navegador_usado, $valor_verificar) !== false) {
        $navegador = $valor_verificar;

        // Use uma expressão regular para encontrar a versão
        $pattern = '#(' . preg_quote($navegador) . ')[/ ]+([0-9.]+)#i';
        if (preg_match($pattern, $navegador_usado, $matches)) {
            $versao = $matches[2];
        }

        break;  // Termina o loop quando encontrar um navegador
    }
}


	   
//==================[ LOG do Sistema ] =======================//


//ação do log(Acesso, Exclusão, Atualização, Inserção)
$log_acao = "LOGIN";
//Rotina acessada
            $log_rotina = "Módulo: LOGIN -> SubMódulo: APP ";
 //historico, O que alterou, excluiu, ou acessou...
            $log_nivel_usuario = $retorno->nivel;
//var_dump($row);         
            $log_historico = "O Usuário:" . $retorno->nome . ", Efetuou o login, através do IP: ".$_SERVER['REMOTE_ADDR']." ";
// Nome do Usuário Registrado na Sessão
            $log_usuario = $retorno->id;
// IP do Usuário
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/log/log_sistema.php';
        
//==================[ Finaliza Log Sistema ]==================//

$avatar_usuario = $retorno->foto ?? 'default.png';

$retorno = array('codigo' => 1, 'id_usuario'=> $retorno->id, 'nome'=> $retorno->nome, 'email' =>  $retorno->email, 'nivel' => $retorno->nivel, 'foto'=> $avatar_usuario, 'status'=> $retorno->status, 'navegador'=>$navegador, 'versao'=>$versao);

echo json_encode($retorno);

$conexao = null;
	exit();

	}
		else{
	$retorno = array('codigo' => '0', 'mensagem' => 'Dados informados não correspondem.<br>Por favor, verifique se seu email e senha informados, estão corretos ao digitar.<br> Caso tenha esquecido sua senha, poderá solicitar uma nova. Persistindo o erro, entre em contato com o Suporte, para saber se seu cadastro está liberado para acesso.');
			echo json_encode($retorno);
            $conexao = null;

			exit();

		}





