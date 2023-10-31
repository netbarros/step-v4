<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();

// Verifica se a sessão foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Definir o idioma e a codificação a serem usados pela função strftime()
// Definir a localidade para português brasileiro
setlocale(LC_TIME, 'pt_BR.utf-8');

// Definir o fuso horário para Brasília/São Paulo
date_default_timezone_set('America/Sao_Paulo');



// Recebe os dados do formulário
$email = trim(isset($_POST['email'])) ? $_POST['email'] : '' ;
$senha = trim(isset($_POST['password'])) ? $_POST['password'] : '' ;

$cookie_expiration = time() + (86400 * 1); // Define o tempo de expiração do cookie para 1 dia
$cookie_path = "/"; // Define o caminho do cookie
$cookie_domain = ""; // Insira seu domínio aqui, se necessário
// Verifica se está no ambiente de desenvolvimento local
$is_localhost = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

// Ajusta as configurações do cookie com base no ambiente
$cookie_secure = $is_localhost ? false : true; // Desabilita o atributo "secure" no ambiente local
$cookie_httponly = true; // Define o atributo "httponly"


// Dica 2 - Validações de preenchimento e-mail e senha se foi preenchido o e-mail
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
	$_SESSION['id'] = $retorno->id;
	$_SESSION['nome'] = $retorno->nome;
	$_SESSION['email'] = $retorno->email;
	$_SESSION['nivel'] = $retorno->nivel;
	$_SESSION['foto'] = $retorno->foto;
	$_SESSION['bd_nome'] = $retorno->bd_nome;
	$_SESSION['bd_id'] = $retorno->bd_id;
	$_SESSION['tentativas'] = 0;
	$_SESSION['logado'] = 'SIM';
	$_SESSION['id_usuario_sessao'] = $retorno->id;  
    $_SESSION['pagina_atual'] = 'login-classico'; // página inicial do usuário	
    $_SESSION['login_google'] = 'nao'; // página inicial do usuário

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
  enviadas com um novo suporte gerado pelo painel da leitura e ver a relação entre elas, por imagens


  Estrutura de Cookies Seguros:
    https://www.php.net/manual/pt_BR/function.setcookie.php

Política SameSite: A política SameSite pode ajudar a proteger contra ataques de cross-site request forgery (CSRF). 
Existem três opções: Strict, Lax, e None. 
Strict é o mais seguro, mas pode interferir com a usabilidade do site. 
Lax é uma boa opção intermediária que fornece um bom equilíbrio entre segurança e usabilidade.
Em PHP, você pode definir um cookie com a política SameSite da seguinte maneira:

*/

$cookie_name = "CHAVE_UNICA_SESSAO_ATUAL";
$cookie_value = $chave_unica;
setcookie($cookie_name, $cookie_value, [
    'expires' => $cookie_expiration,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // or 'Strict' or 'None'
]);

$estacao_atual='0';

  //  faço a gravação do log
  $acao_log = "Novo Login Dashboard";
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
	setcookie('seguranca');
	setcookie('ativacao'); // Como foi validada a sessão, se houver alerta de segurança anterior, o histórico do browser será apagado.

	$ponto_lido = (isset($_SESSION['ponto_lido'])) ? $_SESSION['ponto_lido'] : '';

	$data_acesso = date_create()->format('Y-m-d H:i:s'); 

	$sql = $conexao->query("UPDATE usuarios SET ultimo_acesso='$data_acesso' WHERE id='$_SESSION[id]'");
// Armazenamento das Variáveis Globais de quem está logando:



// Registro o ID do Usuário pela Tabela de Usuários do Sistema

$cookie_name = 'id_usuario_sessao';
$cookie_value = $retorno->id;


setcookie($cookie_name, $cookie_value, [
    'expires' => $cookie_expiration,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // or 'Strict' or 'None' // Adiciona o atributo 'SameSite' para maior segurança
]);


// Nível de acesso do usuário:
$cookie_name = 'nivel_acesso_usuario';
$cookie_value = $retorno->nivel;
setcookie($cookie_name, $cookie_value, [
    'expires' => $cookie_expiration,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // or 'Strict' or 'None'
]);

// Tabela de cadastro do usuário:
$cookie_name = 'bd_nome';
$cookie_value = $retorno->bd_nome;
setcookie($cookie_name, $cookie_value, [
    'expires' => $cookie_expiration,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // or 'Strict' or 'None'
]);

// ID de cadastro na tabela de origem do usuário (bd_nome):
$cookie_name = 'bd_id';
$cookie_value = $retorno->bd_id;
setcookie($cookie_name, $cookie_value, [
    'expires' => $cookie_expiration,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // or 'Strict' or 'None'
]);

// Nome do usuário:
$cookie_name = 'nome_usuario';
$cookie_value = $retorno->nome;
setcookie($cookie_name, $cookie_value, [
    'expires' => $cookie_expiration,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // or 'Strict' or 'None'
]);

// Se houver foto cadastrada, gravo o binário da foto para exibição estratégica:
$cookie_name = 'foto_usuario';
$cookie_value = $retorno->foto;
setcookie($cookie_name, $cookie_value, [
    'expires' => $cookie_expiration,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // or 'Strict' or 'None'
]);

//** Condição caso o usuário seja acesso de cliente (contato de cliente) */

$estacao="";


if($retorno->nivel=='supervisor'){

$bd_id = $retorno->bd_id; // pego o id da tabela de contatos do cliente (com isso já saberei quais clientes tem este contato )
$bd_nome = $retorno->bd_nome; // já saberei o nome da tabela do id do usuario


                        $sql=$conexao->query("SELECT * FROM colaboradores tb
                         INNER JOIN clientes cl ON cl.id_cliente = tb.filial                         
                        INNER JOIN usuarios_projeto up ON up.id_usuario = '$retorno->id'
                        INNER JOIN estacoes es ON es.id_obra = up.id_obra
                        WHERE tb.email_corporativo='$email'");

                       
$conta = $sql->rowCount();




		if ($conta >0) {


			$res=$sql->fetch(PDO::FETCH_OBJ);
			
				//ID da Núcleo do Supervisor
				$cookie_name = 'estacao_operador';
                $cookie_value = $res->id_estacao;
                setcookie($cookie_name, $cookie_value, [
                    'expires' => $cookie_expiration,
                    'path' => $cookie_path,
                    'domain' => $cookie_domain,
                    'secure' => $cookie_secure,
                    'httponly' => $cookie_httponly,
                    'samesite' => 'Lax', // or 'Strict' or 'None'
                ]);



				// ID do CLiente na tabela cliente
			    $cookie_name = 'id_tabela_cliente';
                $cookie_value = $res->id_cliente;
                setcookie($cookie_name, $cookie_value, [
                    'expires' => $cookie_expiration,
                    'path' => $cookie_path,
                    'domain' => $cookie_domain,
                    'secure' => $cookie_secure,
                    'httponly' => $cookie_httponly,
                    'samesite' => 'Lax', // or 'Strict' or 'None'
                ]);

						}
						else{


			$retorno = array('codigo' => 0, 'mensagem' => 'Caro Supervisor, por gentileza entre em contato com o Suporte de Operações, para que vinculem seu acesso ao Projeto ou Núcleo designado.');
			echo json_encode($retorno);
			exit();
	



						}
					}



if($retorno->nivel=='cliente'){


$bd_id = $retorno->bd_id; // pego o id da tabela de contatos do cliente (com isso já saberei quais clientes tem este contato )
$bd_nome = $retorno->bd_nome; // já saberei o nome da tabela do id do usuario


                        $sql=$conexao->query("SELECT * FROM contatos c
                        INNER JOIN clientes cl ON cl.id_cliente = c.id_cliente
                        INNER JOIN usuarios_projeto up ON up.id_usuario = '$retorno->id'
                        WHERE c.email_corporativo='$email'");

                        $res=$sql->fetch(PDO::FETCH_OBJ);

                        $status_atual = $res->status_cadastro;

                        if($status_atual=="1"){


							$estacao =  $res->id_estacao ?? '' ; 



		// ID do CLiente na tabela cliente
        $cookie_name = 'id_tabela_cliente';
        $cookie_value = $res->id_cliente;
        setcookie($cookie_name, $cookie_value, [
            'expires' => $cookie_expiration,
            'path' => $cookie_path,
            'domain' => $cookie_domain,
            'secure' => $cookie_secure,
            'httponly' => $cookie_httponly,
            'samesite' => 'Lax', // or 'Strict' or 'None'
        ]);




// Registro o Nome da Empresa do Clinete/Contato
$cookie_name = 'nome_cliente';
$cookie_value = $res->nome_fantasia;
setcookie($cookie_name, $cookie_value, [
    'expires' => $cookie_expiration,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // or 'Strict' or 'None'
]);

// Registro o Nome CNPJ

$cookie_name = 'cnpj_cliente';
$cookie_value = $res->cnpj;
setcookie($cookie_name, $cookie_value, [
    'expires' => $cookie_expiration,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // or 'Strict' or 'None'
]);


// Registro Nível de Contrato de Serviço e Acesso do STEP

$cookie_name = 'gestao_step';
$cookie_value = $res->gestao_step;
setcookie($cookie_name, $cookie_value, [
    'expires' => $cookie_expiration,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // or 'Strict' or 'None'
]);
//Registro o Email geral para acoes em tempo real no dashboard do cliente, sem haver nva consulta no BD


$cookie_name = 'email_principal_cliente';
$cookie_value = $res->email_geral;
setcookie($cookie_name, $cookie_value, [
    'expires' => $cookie_expiration,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // or 'Strict' or 'None'
]);


						}
}

//** Fecha condiçao contato cliente */
	     
//==================[ LOG do Sistema ] =======================//


//ação do log(Acesso, Exclusão, Atualização, Inserção)
$log_acao = "LOGIN";
//Rotina acessada
            $log_rotina = "Módulo: LOGIN -> SubMódulo: Dashboard ";
 //historico, O que alterou, excluiu, ou acessou...
            $log_nivel_usuario = $_SESSION['nivel'];
//var_dump($row);         
            $log_historico = "O Usuário:" . $_SESSION['nome'] . ", Efetuou o login, através do IP: ".$_SERVER['REMOTE_ADDR']." ";
// Nome do Usuário Registrado na Sessão
            $log_usuario = $_SESSION['id'];
// IP do Usuário
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/log/log_sistema.php';
        
//==================[ Finaliza Log Sistema ]==================//





	$retorno = array('codigo' => 1, 'ponto_lido_operador' => $ponto_lido, 'nivel'=>$_SESSION['nivel'], 'mensagem' => 'Logado com sucesso!','estacao'=>$estacao);


	echo json_encode($retorno);


	exit();
	}
		else{
	$retorno = array('codigo' => '0', 'mensagem' => 'Dados informados não correspondem.<br>Por favor, verifique se seu email e senha informados, estão corretos ao digitar.<br> Caso tenha esquecido sua senha, poderá solicitar uma nova. Persistindo o erro, entre em contato com o Suporte, para saber se seu cadastro está liberado para acesso.');
			echo json_encode($retorno);

			exit();

		}



