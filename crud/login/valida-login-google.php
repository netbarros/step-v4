<?php
// Instancia Conexão PDO
require_once "../../conexao.php";
$conexao = Conexao::getInstance();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Recebe os dados do formulário
$acao = trim(isset($_POST['acao'])) ? $_POST['acao'] : '' ;
$id_google = trim(isset($_POST['id'])) ? $_POST['id'] : '' ;
$nome_completo = trim(isset($_POST['nome_completo'])) ? $_POST['nome_completo'] : '' ;
$primeiro_nome = trim(isset($_POST['primeiro_nome'])) ? $_POST['primeiro_nome'] : '' ;
$sobre_nome = trim(isset($_POST['sobre_nome'])) ? $_POST['sobre_nome'] : '' ;
$foto = trim(isset($_POST['foto'])) ? $_POST['foto'] : '' ;
$email_google = trim(isset($_POST['email'])) ? $_POST['email'] : '' ;


$cookie_expiration = time() + (86400 * 1); // Define o tempo de expiração do cookie para 30 dias
$cookie_path = "/"; // Define o caminho do cookie
$cookie_domain = ""; // Insira seu domínio aqui, se necessário
// Verifica se está no ambiente de desenvolvimento local
$is_localhost = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

// Ajusta as configurações do cookie com base no ambiente
$cookie_secure = true; // Desabilita o atributo "secure" no ambiente local
$cookie_httponly = true; // Define o atributo "httponly"



if($acao=='valida-google'){



//  Válida os dados do usuário se já possio vinculo com o google e se esta ativo
$sql = 'SELECT * FROM usuarios WHERE email_google = ? LIMIT 1';
$stm = $conexao->prepare($sql);
$stm->bindValue(1, $email_google);
$stm->execute();


$conta = $stm->rowCount();

if($conta > 0){ // se o email do usuário já constar na base

    $r = $stm->fetch(PDO::FETCH_OBJ);

    if($r->status=='2'){

        $retorno = array('codigo' => 0, 'retorno' => 'Olá '.$primeiro_nome.', seu acesso ao Sistema está Bloqueado no momento!<br> Entre em contato com o Suporte.');
        echo json_encode($retorno);
        exit();

    }


    if($r->status=='3'){
 
        $retorno = array('codigo' => 0, 'retorno' => 'Olá '.$primeiro_nome.', seu acesso ao Sistema está aguardando ativação no momento!<br> Verifique seu e-mail de cadastro, ative sua conta e vincule sua conta Google para prosseguir.');
        echo json_encode($retorno);
        exit();
 
    }


    if($r->status=='1'){


        // atualiza os dados do Google na conta do usuário:

        $id_user= $r->id;

        $sql_update =$conexao->query("UPDATE usuarios SET 
            oauth_uid = '$id_google',
            first_name = '$primeiro_nome',
            last_name = '$sobre_nome',
            email_google = '$email_google',
            picture = '$foto' 
            WHERE id = '$id_user'");
            
  
       

        // salva os dados do login na sessão:

        $_SESSION['id'] = $r->id;
        $_SESSION['nome'] = $r->nome;
        $_SESSION['email'] = $r->email;
        $_SESSION['nivel'] = $r->nivel;
        $_SESSION['foto'] = $foto;
        $_SESSION['bd_nome'] = $r->bd_nome;
        $_SESSION['bd_id'] = $r->bd_id;
        $_SESSION['tentativas'] = 0;
        $_SESSION['logado'] = 'SIM';
        $_SESSION['id_usuario_sessao'] = $r->id; 
        $_SESSION['pagina_atual'] = 'login-google'; 
        $_SESSION['login_google'] = 'sim'; // página inicial do usuário
       



//===[ CHAVE ÚNICA da SESSAO] a cada sessao, o step registra uma codificação única.
// Obtenha a data atual no formato dd-mm-yyyy
$data_chave = date("d-m-Y");

// Obtenha a hora atual no formato hh:mm
$hora_chave = date("H:i");

$pagina_ativa_chave = $_SESSION['pagina_atual'] ?? 'gerado_automatico';

// Acrescenta o hífen na concatenação
$usuario_sessao_chave = $_SESSION['nome'] . '-' . ($_SESSION['pagina_atual'] ?? 'gerado_automatico');

$id_usuario_sessao_chave = $_SESSION['id'];

// Crie a chave única
$chave_unica = $_COOKIE['CHAVE_UNICA_SESSAO_ATUAL'] ?? md5($data_chave . $hora_chave . $pagina_ativa_chave . $usuario_sessao_chave . $id_usuario_sessao_chave);
/*===[ CHAVE ÚNICA da SESSAO]==== */

$cookie_name = "CHAVE_UNICA_SESSAO_ATUAL";
$cookie_value = $chave_unica;
setcookie($cookie_name, $cookie_value, [
    'expires' => $cookie_expiration,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // or 'Strict' or 'None' // Adiciona o atributo 'SameSite' para maior segurança
]);


$cookie_name = "login_google";
$cookie_value = "sim";
setcookie($cookie_name, $cookie_value, [
    'expires' => $cookie_expiration,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => 'Lax', // or 'Strict' or 'None' // Adiciona o atributo 'SameSite' para maior segurança
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
    $r->id,
    $acao_log,
    $estacao_atual,
    $tipo_log ]);
 //  faço a gravação do log    	




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
$cookie_value = $_SESSION['id'];
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
$cookie_value = $_SESSION['nivel'];
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
$cookie_value = $_SESSION['bd_nome'];
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
$cookie_value = $_SESSION['bd_id'];
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
$cookie_value = $_SESSION['nome'];
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
$cookie_value =  $_SESSION['foto'];
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


if($r->nivel=='supervisor'){

$bd_id = $r->bd_id; // pego o id da tabela de contatos do cliente (com isso já saberei quais clientes tem este contato )
$bd_nome = $r->bd_nome; // já saberei o nome da tabela do id do usuario


                        $sql=$conexao->query("SELECT * FROM colaboradores tb
                        
                        INNER JOIN clientes cl ON cl.id_cliente = tb.filial
                        INNER JOIN usuarios_projeto up ON up.id_usuario = '$r->id'
                                                    
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

                


$r->
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



if($r->nivel=='cliente'){


$bd_id = $r->bd_id; // pego o id da tabela de contatos do cliente (com isso já saberei quais clientes tem este contato )
$bd_nome = $r->bd_nome; // já saberei o nome da tabela do id do usuario


                        $sql=$conexao->query("SELECT * FROM  contatos tb_contato
                        
                        INNER JOIN clientes cli_contato ON tb_contato.id_cliente = cli_contato.id_cliente
						INNER JOIN obras o ON o.id_cliente = cli_contato.id_cliente
                        INNER JOIN clientes cl ON cl.id_cliente = c.id_cliente
                        INNER JOIN usuarios_projeto up ON up.id_usuario = '$r->id'                            
                        WHERE tb_contato.id_contato=$bd_id");

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
$log_acao = "LOGIN GOOGLE";
//Rotina acessada
            $log_rotina = "Módulo: LOGIN -> SubMódulo: Dashboard";
 //historico, O que alterou, excluiu, ou acessou...
            $log_nivel_usuario = $_COOKIE['nivel_acesso_usuario'] ?? '';
//var_dump($row);         
            $log_historico = "O Usuário:" . $_SESSION['nome'] . ", Efetuou o login, através do IP: ".$_SERVER['REMOTE_ADDR']." ";
// Nome do Usuário Registrado na Sessão
            $log_usuario = $_SESSION['id'];
            require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/log/log_sistema.php';
        
//==================[ Finaliza Log Sistema ]==================//



if ($stm->rowCount() > 0) {
    $nivel = $r->nivel;

    $retorno = array('codigo' => 1, 'retorno' => 'Olá '.$primeiro_nome.'!<br>Seu Login via Google foi realizado com Sucesso!', 'nivel'=>$nivel);
    echo json_encode($retorno);
    exit();


} else {

    $retorno = array('codigo' => 0, 'retorno' => 'Olá '.$primeiro_nome.'!<br>Não foi possível atualizar os dados da sua Conta do Google!');
    echo json_encode($retorno);
    exit();

}
      
    

    }

} 



} else {

    $retorno = array('codigo' => 0, 'retorno' => '
    <div class="card-body text-gray-800 mt-3 mb-5">
    Olá '.$primeiro_nome.'!
    <br> Suas credenciais do Google são Válidas!<br>Porém seu primeiro acesso com ela, precisa ser realizado <strong>dentro do Sistema</strong>,
    <br>em seu <strong>Perfil</strong>, selecione <span class="text-primary fs-6">Vincular Minha conta Google</span>
    <br> <br><div class="alert alert-primary d-flex align-items-center p-5 mb-10">
  
    <div class="d-flex flex-column">
        <h4 class="mb-1 text-primary fs-4">O que fazer?</h4>
        <span class="fs-6 text-gray-800">Até lá, acesse sua conta normalmente com os dados de acesso, fornecidos inicialmente.</span>
    </div>
</div></div>');
    echo json_encode($retorno);
    exit();
}


}


?>



