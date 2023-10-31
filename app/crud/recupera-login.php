<?php
 require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
//ini_set("session.cookie_secure", 1);
if (!isset($_SESSION)) session_start();
//================ RECUREPARAÇÂO DE SENHA POR EMAIL ======

$recupera=(isset($_POST['recupera'])) ? $_POST['recupera'] : '' ;


$hr = date(" H ");
if($hr >= 12 && $hr<18) {
$Saudacao = "Boa tarde!";}
else if ($hr >= 0 && $hr <12 ){
$Saudacao = "Bom dia!";}
else {
$Saudacao = "Boa noite!";}




$email = trim(isset($_POST['email'])) ? $_POST['email'] : '' ;


// Dica 3 - Verifica se o formato do e-mail é válido
if (!filter_var($email, FILTER_VALIDATE_EMAIL)):
    $retorno = array('codigo' => 0, 'mensagem' => 'Formato de e-mail inválido!');
	echo json_encode($retorno);
	exit();
endif;



    
$consulta =$conexao->query(" SELECT id, nome, email, senha, nivel  FROM usuarios WHERE email = '$email' ORDER BY email ASC"); 



if ($consulta->rowCount () ==0) { 
           
	$retorno = array('codigo' => '0', 'mensagem' => 'E-mail de Usuário, não localizado no Sistema!');
	echo json_encode($retorno);
	exit();

}else{
   
	$row = $consulta->fetch(PDO::FETCH_OBJ);
	
// == classe envia email 
$senha_inicial_user = password_hash("grupoep123",PASSWORD_DEFAULT);

$sql = "UPDATE usuarios SET senha=? WHERE id=?";
$stmt=$conexao->prepare($sql);
$stmt->execute([$senha_inicial_user, $row->id]);



$data_envio = date('d/m/Y');
$hora_envio = date('H:i:s');




//===[ CHAVE ÚNICA da SESSAO] a cada acesso, o step registra uma codificação única de 32bits, encriptografada, de acesso unico para o login do usuário e logout do mesmo.
/* para cada nova leitura, é gerada uma chave_unica_sessao_atual, está é para mapearmos a rota desde a leitura do plcode e o que o usuário fez em sequência, 
checkin, abriu suporte, fez envio normal da leitura do plcode lido, enviou imagens nas leituras ou no suporte e ou reabertura de plcode, a chave_unica_sessao,
vinculará cada rotina do usuário, desde o início da etapa até a sua conclusão e leitura do próximo plcode, onde uma nova chave será gerada para o novo acompanhamento
da nova rotina do plcode lido, que se iniciará.
*/
$horario_completo_agora = microtime();
/* INÍCIO: Crio a Chave unica da Sessao para armazenamento e resgate das leituras e imagens que serão enviadas */          
$chave_unica = bin2hex(random_bytes(33).$horario_completo_agora); 
/* Gerar strings aleatórias criptograficamente seguras, usamos 24 caracteres não repetitivos e aleatórios com criptografia nativa do PHP";
 serve como id referencial para salvar a midia e após salvar as leituras por essa chave que tbem constará na tb rmm, vinculo o id_rmm na tb midia_leitura,
  com a mesma chave unica (para controlar cada leitura enviada individualmente e não misturar as imagens enviadas) assim tbem poderemos vincular as midias 
  enviadas com um novo suporte gerado pelo painel da leitura e ver a relação entre elas, por imagens*/



// Chama o script para envio de email com os dados de acesso

    
	
                    if($stmt){

                                if($row->email!=''){
                                    $email_para = $row->email;
                                    $nome_para = $row->nome;
                                    $id_operador =  $row->id;
                                    $nivel_acesso = $row->nivel;
                                
                                                            
                                //=====================[] envia email ]===========
        $chave_unica = $chave_unica;        

        $id_usuario=$id_operador;
        $email_usuario =$email_para; // destinatário padrão
        $nome_usuario = $nome_para;
        $nome_projeto = $r_email->nome_obra;
        $nivel_acesso = strtoupper($nivel_acesso);

        $mensagem_email = "Você foi incluído no Projeto: <strong>".$nome_projeto."</strong>.<br>Com o nível de acesso: <strong>".$nivel_maiusculo."</strong>";
        $assunto = 'Inclusão em Novo Projeto';
        $template_email = '/views/emails/email-inclusao-projeto.php';
        require_once  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';

      

        
        exit();
     
      
//=====================[] envia email ]===========
                                }

                              
                        }
	



}

//================ RECUREPARAÇÂO DE SENHA POR EMAIL ======