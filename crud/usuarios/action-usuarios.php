<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/conexao.php';


// Atribui uma conexão PDOcolab
$conexao = Conexao::getInstance();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$acao = $_POST['acao'] ?? $_GET['acao'];

$buscar = (isset($_GET['data'])) ? $_GET['data'] : '';

$emailGoogle=$_POST['emailGoogle'] ?? '';

$confirmaEmailGoogle=$_POST['confirmaEmailGoogle'] ?? '';

$valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp', 'pdf', 'doc', 'ppt'); // valid extensions
$path = $_SERVER['DOCUMENT_ROOT'] . '/foto-perfil/'; // upload directory

$senha_atual_user = trim(isset($_POST['currentpassword'])) ? $_POST['currentpassword'] : '';

$nova_senha_user = trim(isset($_POST['newpassword'])) ? $_POST['newpassword'] : '';

$email_usuario = trim(isset($_POST['email'])) ? $_POST['email'] : '';
$email_usuario = preg_replace('/\s+/', ' ', $email_usuario);  

$perfil_usuario = trim(isset($_POST['perfil_usuario'])) ? $_POST['perfil_usuario'] : ''; 

$nivel_acesso = trim(isset($_POST['nivel_acesso'])) ? $_POST['nivel_acesso'] : ''; 

$matricula = trim(isset($_POST['matricula'])) ? $_POST['matricula'] : ''; 


$fname = trim(isset($_POST['fname'])) ? $_POST['fname'] : '';
$lname = trim(isset($_POST['lname'])) ? $_POST['lname'] : '';
$company = trim(isset($_POST['company'])) ? $_POST['company'] : '';



$website = trim(isset($_POST['website'])) ? $_POST['website'] : '';

$id = trim(isset($_POST['id'])) ? $_POST['id'] : '';

// Limpa e valida os e-mails
$EmailAtual = trim(isset($_POST['EmailAtual']) ? $_POST['EmailAtual'] : '');
$NovoEmail = trim(isset($_POST['NovoEmail']) ? $_POST['NovoEmail'] : '');
$NovoEmail = preg_replace('/\s+/', ' ', $NovoEmail); 



$bd_nome = trim(isset($_POST['bd_nome'])) ? $_POST['bd_nome'] : '';
$bd_id = trim(isset($_POST['bd_id'])) ? $_POST['bd_id'] : '';

$senha = password_hash("Grupoep123", PASSWORD_DEFAULT);


//===[ CHAVE ÚNICA da SESSAO] a cada sessao, o step registra uma codificação única.
// Obtenha a data atual no formato dd-mm-yyyy
$data_chave = date("d-m-Y");

// Obtenha a hora atual no formato hh:mm
$hora_chave = date("H:i");

$pagina_ativa_chave = $_SESSION['pagina_atual'] ?? 'gerado_automatico';

// Acrescenta o hífen na concatenação
$usuario_sessao_chave = $_SESSION['nome'] . '-' . ($_SESSION['pagina_atual'] ?? 'gerado_automatico');

$id_usuario_sessao_chave = $_SESSION['id'] ?? $_COOKIE['id_usuario_sessao'] ?? '';


// Crie a chave única
$chave_unica = $_COOKIE['CHAVE_UNICA_SESSAO_ATUAL'] ?? md5($data_chave . $hora_chave . $pagina_ativa_chave . $usuario_sessao_chave . $id_usuario_sessao_chave);
/*===[ CHAVE ÚNICA da SESSAO]==== */

$data_cadastro = date('Y-m-d H:i:s');
$data_envio = date('d/m/Y H:i');
$hr = date(" H ");
if ($hr >= 12 && $hr < 18) {
    $Saudacao = "Boa tarde!";
} else if ($hr >= 0 && $hr < 12) {
    $Saudacao = "Bom dia!";
} else {
    $Saudacao = "Boa noite!";
}
    


if($acao=='buscar'){



    $sql=$conexao->query("SELECT * FROM usuarios u WHERE u.nome LIKE '%$buscar%' OR u.email LIKE '%$buscar%' AND u.status='1' GROUP BY u.nome ORDER BY u.nome ASC");

    $conta = $sql->rowCount();



    if ($conta > 0) {

        $row = $sql->fetchALL(PDO::FETCH_ASSOC);

        foreach ($row as $r) {

            $nivel_user = $r['nivel'];
            
            $id_user = $r['id'];


            $brev_nome_user = substr($r['nome'], 0, 1);
            $foto_user = $r['foto'];

            $nome_user = $r['nome'];

                    
            if ($id_user % 2 == 0) {
                //echo "Numero Par"; 
                $classe = 'info';
            } else {
                $classe = 'warning';
                //echo "Numero Impar"; }
            }


            $filename = '/foto-perfil/'. $foto_user;

            if (file_exists($filename)) {

                $retorno_foto= '<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="' . $nome_user . '">
                <img alt="Foto Usuário" src="' . $foto_user . '" />
            </div>';

            } else {

                $retorno_foto= '<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="' . $nome_user . '">
                <span class="symbol-label bg-' . $classe . ' text-inverse-' . $classe . ' fw-bold">' . $brev_nome_user . '</span>
            </div>';
            }





            

            $nivel_selecionado = '';
            
            if ($nivel_user == 'engenheiro') {

                $nivel_selecionado = 'selected = "selected"';
            }
            if ($nivel_user == 'ro') {

                $nivel_selecionado = 'selected = "selected"';
            }
            if ($nivel_user == 'supervisor') {

                $nivel_selecionado = 'selected = "selected"';
            }
            if ($nivel_user == 'colaborador') {

                $nivel_selecionado = 'selected = "selected"';
            }


            echo '<!--begin::User-->
                        <div class="rounded d-flex flex-stack bg-active-lighten p-4"  >
                            <!--begin::Details-->
                            <div class="d-flex align-items-center">
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-5">
                                    <input class="form-check-input" type="checkbox" name="users[]" data-kt-check="true"  value="' . $r['id'] . '" data-id="' . $r['id'] . '" onClick="nivel()"/>
                                </label>
                                <!--end::Checkbox-->
                                <!--begin::Avatar-->
                                ' . $retorno_foto . '
                                <!--end::Avatar-->
                                <!--begin::Details-->
                                <div class="ms-5">
                                    <a href="../../views/conta-usuario/overview.php?id=' . $r['id'] . '" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">' . $r['nome'] . '</a>
                                    <div class="fw-semibold text-muted">' . $r['email'] . '</div>
                                </div>
                                <!--end::Details-->
                            </div>
                            <!--end::Details-->
                            <!--begin::Access menu-->
                            <div class="ms-2 w-100px " >
                                <select class="form-select form-select-solid form-select-sm " data-control="select2" data-hide-search="false" data-allow-clear="true" data-dropdown-parent="#kt_modal_users_search" disabled id="select_lista_nivel_' . $r['id'] . '" name="nivel_usuario_projeto[]"  >
                                    <option value="ro" ' . $nivel_selecionado . ' selected>RO</option>
                                    <option value="supervisor" ' . $nivel_selecionado . '>Supervisor</option>
                                    <option value="engenheiro" ' . $nivel_selecionado . '>Engenheiro IoT</option>
                                    <option value="operador" ' . $nivel_selecionado . '>Operador</option>
                                    <option value="cliente" ' . $nivel_selecionado . '>Cliente</option>
                                </select>
                            </div>
                            <!--end::Access menu-->
                        </div>
                        <!--end::User-->



                        <!--begin::Separator-->
                        <div class="border-bottom border-gray-300 border-bottom-dashed"></div>
                        <!--end::Separator-->';
        }
    } else {

        echo '     <!--begin::Empty-->
        <div data-kt-search-element="empty" class="text-center">
            <!--begin::Message-->
            <div class="fw-semibold py-10">
                <div class="text-gray-600 fs-3 mb-2">Nenhum usuário encontrado</div>
                <div class="text-muted fs-6">Tente buscar por Nome, Nome Completo ou E-mail...</div>
            </div>
            <!--end::Message-->
            <!--begin::Illustration-->
            <div class="text-center px-5">
                <img src="assets/media/illustrations/sigma-1/1.png" alt="" class="w-100 h-200px h-sm-325px" />
            </div>
            <!--end::Illustration-->
        </div>
        <!--end::Empty-->';    }


}
               
 
if ($acao == "altera_usuario" && $id!="") {

$phone = trim($_POST['phone']) ?? '';
$phone = preg_replace('/\D/', '', $phone);  // remove todos os caracteres não numéricos


    if (isset($_FILES['image'])) {
        $img = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        // get uploaded file's extension
        $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
        // can upload same image using rand function
        $final_image = rand(1000, 1000000) . $img;
        // check's valid format
        if (in_array($ext, $valid_extensions)) {
            $path = $path . strtolower($final_image);
            if (move_uploaded_file($tmp, $path)) {
               
                $insert = $conexao->query("UPDATE usuarios SET foto='$path' WHERE id='$id'");
                //echo $insert?'ok':'err';
            }
        } else {
            echo 'invalid';
        }
    }

  
// atualiza os dados direto na base de usuarios
    $sql_update_usuario = $conexao->query("UPDATE usuarios SET nome='$fname', telefone ='$phone' WHERE id='$id' ");


// separa o cadastro entre colaboradores e contatos:

    if($bd_nome=== 'colaboradores'){

        $sql = $conexao->query("UPDATE colaboradores  
        SET nome='$fname', sobrenome='$lname', filial='$company', cel_corporativo='$phone'
        WHERE id_colaborador='$bd_id'");

        if($sql){


             //=====================[] envia email ]===========

$sql = $conexao->query("SELECT * FROM usuarios u WHERE u.id='$id' ");
$r_email = $sql->fetch(PDO::FETCH_OBJ);

$chave_unica = $chave_unica;        

$id_usuario=$id;
$email_usuario =$r_email->email; // destinatário padrão
$nome_usuario = $r_email->nome;
$nivel_acesso = $r_email->nivel;
$assunto = 'Alteração de Cadastro de Usuário';
$mensagem_email = "Olá $nome_usuario, seu cadastro no STEP foi alterado, na data: <b>$data_envio</b>, através do usuário: <b>$_SESSION[nome]</b> <p>Caso não concorde com alguma infomação por favor, entre em contato o mais breve possível com o Suporte do STEP, através do Whatsapp e Chat Online: <b>+55 11 2463-7700.</b></p>";
$template_email = '/views/emails/email-padrao.php';
include  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';



//=====================[] envia email ]===========

            $retorno = array('codigo' => 1, 'retorno' => "O Cadastro do usuário: $fname, <br> foi atualizado, com sucesso!");
        }else{

            $retorno = array('codigo' => 0, 'retorno' => "Falha ao Alterar os dados do usuário: $fname!");
        }

    }else{

        $sql = $conexao->query("UPDATE contatos  SET nome='$fname', sobrenome='$lname', id_cliente='$company', cel_corporativo='$phone'
        WHERE id_contato='$bd_id'");

        if ($sql) {


            
             //=====================[] envia email ]===========

$sql = $conexao->query("SELECT * FROM usuarios u WHERE u.id='$id' ");
$r_email = $sql->fetch(PDO::FETCH_OBJ);

$chave_unica = $chave_unica;        

$id_usuario=$id;
$email_usuario =$r_email->email; // destinatário padrão
$nome_usuario = $r_email->nome;
$nivel_acesso = $r_email->nivel;
$assunto = 'Alteração de Cadastro de Usuário';
$mensagem_email = "Olá $nome_usuario, cadastro no STEP foi alterado, na data: <b>$data_envio</b>, através do usuário: <b>$_SESSION[nome]</b> <p>Caso não concorde com alguma infomação por favor, entre em contato o mais breve possível com o Suporte do STEP, através do Whatsapp e Chat Online: <b>+55 11 2463-7700.</b></p>";
$template_email = '/views/emails/email-padrao.php';
include  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';



//=====================[] envia email ]===========



            $retorno = array('codigo' => 1, 'retorno' => "O Cadastro do usuário: $fname, foi atualizado, com sucesso!");
        } else {

            $retorno = array('codigo' => 0, 'retorno' => "Falha ao Alterar os dados do usuário: $fname!");
        }

    }


    echo json_encode($retorno);

    $conexao = null;
    

    


}

if ($acao == "atualiza_senha" && $id!="") {

    $chave_nova_senha = $_POST['chave_nova_senha'] ?? '';

    if($chave_nova_senha==''){

       $retorno = array('codigo' => 0, 'retorno' => "A chave de segurança não foi identificada!");
         echo json_encode($retorno);
            exit();
    }


    $sql = $conexao->query("SELECT * FROM usuarios WHERE id = '$id' ");
    $retorno = $sql->fetch(PDO::FETCH_OBJ);

    $conta = $sql->rowCount();

    if ($conta > 0) { // se a senha atual for igual ao BD

        if($chave_nova_senha==$retorno->chave){


        $nova_senha = password_hash($nova_senha_user, PASSWORD_DEFAULT);


        $sql_refresh = $conexao->query("UPDATE usuarios SET senha='$nova_senha' WHERE id='$id'");



//=====================[] envia email ]===========

        $chave_unica = $chave_nova_senha;        

        $id_usuario=$id;
        $email_usuario =$retorno->email; // destinatário padrão
        $nome_usuario = $retorno->nome;
        $assunto = 'Senha de Acesso Alterada - STEP';
        $mensagem_email ="$Saudacao, $nome_usuario, você está recebendo este e-mail de confirmação que sua senha foi alterada com sucesso, na data: <b>$data_envio</b> <br><br> Caso não tenha sido você, entre em contato com o Suporte do STEP, através do Whatsapp e Chat Online: <b>+55 11 2463-7700.</b>";
        $mensagem_email .= "<br><br> Sua Nova Senha, Armazene com segurança:<br><br> Nova Senha: <b>$nova_senha_user</b> <br> ";
        $nivel_acesso = $retorno->nivel;
        $template_email = '/views/emails/email-padrao.php';

        include  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';
        
     
      
//=====================[] envia email ]===========

        $retorno = array('codigo' => 1, 'retorno' => "Senha alterada, com sucesso!");

        } else {
           
        $nova_senha = password_hash($nova_senha_user, PASSWORD_DEFAULT);


        $sql_refresh = $conexao->query("UPDATE usuarios SET senha='$nova_senha' WHERE id='$id'");



//=====================[] envia email ]===========

        $chave_unica = $chave_nova_senha;        

        $id_usuario=$id;
        $email_usuario =$retorno->email; // destinatário padrão
        $nome_usuario = $retorno->nome;
        $nivel_acesso = $retorno->nivel;
        $assunto = 'Senha de Acesso Alterada - STEP';
        $mensagem_email="$Saudacao, $nome_usuario, você está recebendo este e-mail de confirmação que sua senha foi alterada com sucesso, na data: <b>$data_envio</b> ";
        $template_email = '/views/emails/email-padrao.php';
        include  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';

       
     
      
//=====================[] envia email ]===========

        $retorno = array('codigo' => 1, 'retorno' => "Senha alterada, com sucesso!");

        }
    } else { // caso náo confira  a senha atual for igual ao BD

        $retorno = array('codigo' => 0, 'retorno' => "A Senha atual, não confere!");
    }


    echo json_encode($retorno);

    $conexao = null;

    exit;
}


if ($acao == "reset_senha" && $email_usuario!="") {
    header('Content-Type: application/json');



    $sql = $conexao->query("SELECT * FROM usuarios WHERE email = '$email_usuario' ");
    $retorno = $sql->fetch(PDO::FETCH_OBJ);

    $conta = $sql->rowCount();

    if($conta==0){

        $retorno = array('codigo' => 0, 'retorno' => "O E-mail informado não foi localizado em nosso sistema!");
        echo json_encode($retorno, true);
        exit();
    }

    if ($conta > 0) {

   

    if ($email_usuario == $retorno->email) {


        $sql_chave = $conexao->query("UPDATE usuarios SET chave='$chave_unica' WHERE id='$retorno->id'");

        $chave_unica = $chave_unica;        

        $id_usuario=$retorno->id;
        $email_usuario =$email_usuario; // destinatário padrão
        $nome_usuario = $retorno->nome;
        $nivel_acesso = $retorno->nivel;

        $assunto = 'Recuperação de Senha';
        $mensagem_email = "$Saudacao, $nome_usuario, você está recebendo este e-mail pois solicitou a recuperação da mesma na data: <b>$data_envio</b>, confira as instruções abaixo: ";
        $template_email = '/views/emails/email-nova-senha.php';

        include  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';

       
     
     
        //=====[ final da classe envia email]=====================<<


        $retorno = array('codigo' => 1, 'retorno' => "Confira o E-mail enviado, com os próximos passos.");
        echo json_encode($retorno );
        exit();


    } else {

        $retorno = array('codigo' => 0, 'retorno' => "Seu e-mail não confere com o e-mail cadastrado no STEP.");
        echo json_encode($retorno );
        exit();

    }
    } else {

        $retorno = array('codigo' => 0, 'retorno' => "E-mail não localizado na Base de Dados.");
        echo json_encode($retorno);
        exit();
    }


   

    $conexao = null;

    exit;
}

if ($acao == "atualiza_email" && $id!="") {


    // Verifica se os e-mails são válidos
if (!filter_var($EmailAtual, FILTER_VALIDATE_EMAIL) || !filter_var($NovoEmail, FILTER_VALIDATE_EMAIL)) {
    // Um dos e-mails não é válido, então retorne um erro e interrompa o script
    echo json_encode(['codigo'=>0, 'retorno'=> 'Um ou ambos os e-mails informados não parecem válidos.']);
    exit;
}

// Verifica se os e-mails são iguais
if ($EmailAtual == $NovoEmail) {
    // Os e-mails são iguais, então retorne um erro e interrompa o script
    echo json_encode(['codigo'=>0, 'retorno' => 'O e-mail atual e o novo e-mail são iguais.'], true , JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES, JSON_PRETTY_PRINT);
    exit;
}




    $sql = $conexao->query("SELECT * FROM usuarios WHERE id = $id");

    $r = $sql->fetch(PDO::FETCH_ASSOC);
    $bd_nome = $r['bd_nome'];
    $bd_id = $r['bd_id'];
    $nome_usuario = $r['nome'];




    if (!empty($r)) { // se a senha atual for igual ao BD


        $sql_refresh = $conexao->query("UPDATE usuarios SET email='$NovoEmail' WHERE id='$id'");

        if($bd_nome=='colaboradores'){

        $sql_refresh = $conexao->query("UPDATE colaboradores SET email_corporativo='$NovoEmail' WHERE id_colaborador='$bd_id'");

        if($sql_refresh){

                $retorno = array('codigo' => 1, 'retorno' => "E-mail do colaborador $nome_usuario, atualizado!");
        }


        }
        if ($bd_nome == 'contatos') {

            $sql_refresh = $conexao->query("UPDATE contatos SET email_corporativo='$NovoEmail' WHERE id_contato='$bd_id'");

            if ($sql_refresh) {

                $retorno = array('codigo' => 1, 'retorno' => "E-mail do contato $nome_usuario, atualizado com sucesso para $NovoEmail!");
            }
        }


    } else { // caso náo confira  a senha atual for igual ao BD

        $retorno = array('codigo' => 0, 'retorno' => "Usuário não localizado!");
    }


    echo json_encode($retorno);


     //=====================[] envia email ]===========

$sql = $conexao->query("SELECT * FROM usuarios u WHERE u.id='$id' ");
$r_email = $sql->fetch(PDO::FETCH_OBJ);

$chave_unica = $chave_unica;        

$id_usuario=$id;
$email_usuario =$r_email->email; // destinatário padrão
$nome_usuario = $r_email->nome;
$nivel_acesso = $r_email->nivel;
$assunto = 'Solicitação de Alteração de E-mail';
$mensagem_email = "Olá $nome_usuario, seu email de acesso no sistema STEP foi alterado para: <b>$NovoEmail</b>, na data: <b>$data_envio</b>, através do usuário: <b>$_SESSION[nome]</b> <p>Caso não concorde com alguma infomação por favor, entre em contato o mais breve possível com o Suporte do STEP, através do Whatsapp e Chat Online: <b>+55 11 2463-7700.</b></p>";
$template_email = '/views/emails/email-padrao.php';

include  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';



//=====================[] envia email ]===========


    $conexao = null;

    exit;
}


if ($acao == "desativa_conta" && $id!="") {


    try {
        // supondo que $id já foi definida anteriormente com um valor seguro 2 =// inativo
        $stmt = $conexao->prepare("UPDATE usuarios SET status = '2' WHERE id = :id");
        $stmt->bindParam(':id', $id);
    
        if ($stmt->execute()) {
           

 //=====================[] envia email ]===========

$sql = $conexao->query("SELECT * FROM usuarios u WHERE u.id='$id' ");
$r_email = $sql->fetch(PDO::FETCH_OBJ);

$chave_unica = $chave_unica;        

$id_usuario=$id;
$email_usuario =$r_email->email; // destinatário padrão
$nome_usuario = $r_email->nome;
$nivel_acesso = $r_email->nivel;
$assunto = 'Conta Desativada';
$mensagem_email = "Olá $nome_usuario, sua conta foi <b>Desativada</b> no sistema.<br> Caso tenha alguma dúvida, por favor, entre em contato com o Suporte do STEP.";
$template_email = '/views/emails/email-padrao.php';


include  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';



//=====================[] envia email ]===========


        $retorno = array('codigo' => 1, 'mensagem' => "A conta de $nome_usuario, foi desativada com sucesso!");

    

  
    echo json_encode($retorno);

  
    
        }
    } catch(PDOException $e) {
        $retorno = array('codigo' => 0, 'mensagem' => "Erro ao executar a consulta: " . $e->getMessage());
    }
    $conexao = null;

    exit;

    
}




if ($acao == "reativar_conta" && $id!='') {



    $sql=$conexao->query("UPDATE usuarios SET status='1' WHERE id='$id'");

    if($sql){

        $retorno = array('codigo' => 1, 'mensagem' => "A conta do Usuário foi reativada com sucesso!");


        //=====================[] envia email ]===========

$sql = $conexao->query("SELECT * FROM usuarios u WHERE u.id='$id' ");
$r_email = $sql->fetch(PDO::FETCH_OBJ);

$chave_unica = $chave_unica;        

$id_usuario=$id;
$email_usuario =$r_email->email; // destinatário padrão
$nome_usuario = $r_email->nome;
$nivel_acesso = $r_email->nivel;
$assunto = 'Conta Reativada';
$mensagem_email = "Olá $nome_usuario, sua conta foi <b>Reativada</b> no sistema.<br> Caso tenha alguma dúvida, por favor, entre em contato com o Suporte do STEP.";
$template_email = '/views/emails/email-padrao.php';

include  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';



//=====================[] envia email ]===========


    } else{

        $retorno = array('codigo' => 0, 'mensagem' => "Impossível Reativar a Conta de Usuário neste Momento! [SQL ERROR]");

    }

   





    echo json_encode($retorno);

    $conexao = null;

    exit;
}





if ($acao == "cadastrar_novo_usuario") {

//print_r($_POST);

$status_cadastro = '1';

$phone = trim($_POST['phone']) ?? '';
$phone = preg_replace('/\D/', '', $phone);  // remove todos os caracteres não numéricos


                        if($perfil_usuario=='colaboradores' && empty($matricula)){


                            $retorno = array('codigo' => 0, 'mensagem' => "A Matrícula do Colaborador é a mesma utilizada no RH do GrupoEP e é obrigatória!");


                            echo json_encode($retorno);

                            $conexao = null;

                            exit;




                        }


                    if($email_usuario==''){


                        $retorno = array('codigo' => 0, 'mensagem' => "O E-mail é Obrigatório!");


                        echo json_encode($retorno);

                        $conexao = null;

                        exit;



                    }


                    if($perfil_usuario=='colaboradores'){

                                // valida email único:



                                $sql_valida = $conexao->query("SELECT u.email,u.nome FROM usuarios u 
                                
                                INNER JOIN colaboradores c ON c.email_corporativo  = u.email
                                
                                WHERE email='$email_usuario'
                                ");

                                $conta_v = $sql_valida->rowCount();



                                if($conta_v > 0){

                                    $r = $sql_valida->fetch(PDO::FETCH_ASSOC);

                                    $retorno = array('codigo' => 0, 'mensagem' => "O E-mail informado já está em uso para o usuário: ".$r['nome']);


                                    echo json_encode($retorno);

                                    $conexao = null;

                                    exit;

                                }


// inclui o usuário se for colaborador ************



                                try {


                                    // Verifique se a matricula já existe na tabela
                                    $sql_selectM = "SELECT matricula FROM colaboradores WHERE matricula=:matricula";
                                    $stmt_selectM = $conexao->prepare($sql_selectM);
                                    $stmt_selectM->bindParam(':matricula', $matricula);
                                    $stmt_selectM->execute();
                                  
                                    if ($stmt_selectM->rowCount() > 0) {


                                        $retorno = array('codigo' => 0, 'mensagem' => 'A Matrícula informada: '.$matricula.' , já está em uso no sistema!');


                                        echo json_encode($retorno);
                
                                        $conexao = null;
                
                                        exit;



                                    }


                              
                           
                           
                               // Defina o comando SQL para inserir os dados na tabela
                               $sql = "INSERT INTO colaboradores (nome, sobrenome, filial, cel_corporativo, email_corporativo,  matricula, status_cadastro, data_cadastro) 
                                       VALUES (:nome, :sobrenome, :filial, :cel_corporativo, :email_corporativo,  :matricula, :status_cadastro, :data_cadastro)";
                           
                               // Prepare o comando SQL para executá-lo com os valores definidos
                               $stmt = $conexao->prepare($sql);
                               $stmt->bindParam(':nome', $fname);
                               $stmt->bindParam(':sobrenome', $lname);
                               $stmt->bindParam(':filial', $company);
                               $stmt->bindParam(':cel_corporativo', $phone);
                               $stmt->bindParam(':email_corporativo', $email_usuario);
                               $stmt->bindParam(':matricula', $matricula);
                               $stmt->bindParam(':status_cadastro', $status_cadastro);
                               $stmt->bindParam(':data_cadastro', $data_cadastro);
                           
                               // Execute o comando SQL preparado
                               $stmt->execute();
                           
                               // Exiba uma mensagem de sucesso se a inserção foi bem-sucedida
                              // echo "Dados inseridos com sucesso!";


                                // Obtenha o ID do último registro inserido
                                       $last_id = $conexao->lastInsertId();



                               try {
                                  
                              
                                 
                                   // Verifique se o e-mail já existe na tabela
                                   $sql_select = "SELECT * FROM usuarios WHERE email=:email";
                                   $stmt_select = $conexao->prepare($sql_select);
                                   $stmt_select->bindParam(':email', $email_usuario);
                                   $stmt_select->execute();
                                 
                                   if ($stmt_select->rowCount() > 0) {
                                     // Se o e-mail já existe, retorne uma mensagem de erro em formato JSON
                                     $retorno = array('codigo' => 0, 'mensagem' =>  'O e-mail já está cadastrado no sistema!');
                                     echo json_encode($retorno);
                                   } else {
                                     // Se o e-mail não existe, insira os dados na tabela
                                     $sql_insert = "INSERT INTO usuarios ( nivel, nome, email, telefone,  senha, bd_id, bd_nome, status, chave) 
                                                    VALUES (:nivel, :nome, :email, :telefone, :senha, :bd_id, :bd_nome, :status, :chave)";
                                     $stmt_insert = $conexao->prepare($sql_insert);
                                     $stmt_insert->bindParam(':nivel', $nivel_acesso);
                                     $stmt_insert->bindParam(':nome', $fname);
                                     $stmt_insert->bindParam(':email', $email_usuario);
                                     $stmt_insert->bindParam(':telefone', $phone);
                                     $stmt_insert->bindParam(':senha', $senha);
                                     $stmt_insert->bindParam(':bd_id', $last_id);
                                     $stmt_insert->bindParam(':bd_nome', $perfil_usuario);
                                     $stmt_insert->bindParam(':status', $status_cadastro);
                                     $stmt_insert->bindParam(':chave', $chave_unica);
                                     $stmt_insert->execute();


                                     $novo_id_usuario = $conexao->lastInsertId();



                                                                                    
                                                //=====================[] envia email ]===========

                                                        $chave_unica = $chave_unica;        

                                                        $id_usuario=$novo_id_usuario;
                                                        $email_usuario =$email_usuario; // destinatário padrão
                                                        $nome_usuario = $fname;
                                                        $nivel_acesso = $nivel_acesso;
                                                        $assunto = 'Nova Conta de Usuário';
                                                        $mensagem_email = 'Olá '.$nome_usuario.',<br><br>Seu cadastro foi realizado com sucesso!<br><br>Para acessar o sistema, clique no link abaixo:<br><br><a href="https://www.step.eco.br">https://www.step.eco.br</a><br><br>Atenciosamente,<br><br>Equipe de Suporte Técnico<br><br>STEP';
                                                        $template_email = '/views/emails/email-novo-usuario.php';

                                                        include  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';
                                                       
                                                        
                                                    
                                                    
                                                //=====================[] envia email ]===========

                                 
                                     // Exiba uma mensagem de sucesso em formato JSON
                                     $retorno = array('codigo' => 1, 'mensagem' => 'Novo Usuário:'.$fname.', cadastrado com Sucesso.');
                                     echo json_encode($retorno);
                                   }
                                 } catch(PDOException $e) {
                                   // Exiba uma mensagem de erro em formato JSON se houver problemas com a inserção
                                   $retorno = array('codigo' => 0, 'mensagem' =>'Erro ao inserir dados: ' . $e->getMessage());
                                   echo json_encode($retorno);
                                 }



                           } catch(PDOException $e) {
                               // Exiba uma mensagem de erro se houver problemas com a inserção
                               $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao inserir dados: ' . $e->getMessage());
                                   echo json_encode($retorno);
                           }

// FIM: inclui o usuário se for colaborador    


                             }else if($perfil_usuario=='contatos'){



                               

                                                        // valida email único:


                                                        $sql_valida = $conexao->query("SELECT u.email,u.nome FROM usuarios u 
                                                                                        
                                                        INNER JOIN contatos c ON c.email_corporativo  = u.email
                                                        
                                                        WHERE email='$email_usuario'
                                                        ");

                                                        $conta_v = $sql_valida->rowCount();



                                                        if($conta_v > 0){

                                                            $r = $sql_valida->fetch(PDO::FETCH_ASSOC);

                                                            $retorno = array('codigo' => 0, 'mensagem' => "O E-mail informado já está em uso para o usuário: ".$r['nome']);


                                                            echo json_encode($retorno);

                                                            $conexao = null;

                                                            exit;

                                                        }


// crud de inclusão do contato na tabela contato e na tabela de usuarios, verificando antes de o email do usuario já existe nas tabelas contatos e usuarios, pois é uma chave estrangeira de conexão unica no sistema


    // inclui o usuário se for contato



    try {



   


   // Defina o comando SQL para inserir os dados na tabela
   $sql = "INSERT INTO contatos ( id_cliente, nome, sobrenome, cel_corporativo, email_corporativo, status_cadastro) 
           VALUES ( :id_cliente, :nome, :sobrenome, :cel_corporativo, :email_corporativo, :status_cadastro)";

   // Prepare o comando SQL para executá-lo com os valores definidos
   $stmt = $conexao->prepare($sql);
   $stmt->bindParam(':id_cliente', $company);
   $stmt->bindParam(':nome', $fname);
   $stmt->bindParam(':sobrenome', $lname);
   $stmt->bindParam(':cel_corporativo', $phone);
   $stmt->bindParam(':email_corporativo', $email_usuario);
   $stmt->bindParam(':status_cadastro', $status_cadastro);

   // Execute o comando SQL preparado
   $stmt->execute();

   // Exiba uma mensagem de sucesso se a inserção foi bem-sucedida
  // echo "Dados inseridos com sucesso!";


    // Obtenha o ID do último registro inserido
           $last_id = $conexao->lastInsertId();



   try {
      
  
     
       // Verifique se o e-mail já existe na tabela
       $sql_select = "SELECT * FROM usuarios WHERE email=:email";
       $stmt_select = $conexao->prepare($sql_select);
       $stmt_select->bindParam(':email', $email_usuario);
       $stmt_select->execute();
     
       if ($stmt_select->rowCount() > 0) {
         // Se o e-mail já existe, retorne uma mensagem de erro em formato JSON
         $retorno = array('codigo' => 0, 'mensagem' =>  'O e-mail já está cadastrado no sistema!');
         echo json_encode($retorno);
       } else {
         // Se o e-mail não existe, insira os dados na tabela
         $sql_insert = "INSERT INTO usuarios ( nivel, nome, email, telefone, senha, bd_id, bd_nome, status, chave) 
                        VALUES (:nivel, :nome, :email, :telefone, :senha, :bd_id, :bd_nome, :status, :chave)";
         $stmt_insert = $conexao->prepare($sql_insert);
         $stmt_insert->bindParam(':nivel', $nivel_acesso);
         $stmt_insert->bindParam(':nome', $fname);
         $stmt_insert->bindParam(':email', $email_usuario);
         $stmt_insert->bindParam(':telefone', $phone);
         $stmt_insert->bindParam(':senha', $senha);
         $stmt_insert->bindParam(':bd_id', $last_id);
         $stmt_insert->bindParam(':bd_nome', $perfil_usuario);
         $stmt_insert->bindParam(':status', $status_cadastro);
         $stmt_insert->bindParam(':chave', $chave_unica);
         $stmt_insert->execute();

         // Obtenha o ID do último registro inserido
         $novo_id_usuario = $conexao->lastInsertId();



        
            //=====================[] envia email ]===========

            $chave_unica = $chave_unica;        

            $url_sistema = 'https://step.eco.br';
            $id_usuario=$novo_id_usuario;
            $email_usuario =$email_usuario; // destinatário padrão
            $nome_usuario = $fname;
            $nivel_acesso = $nivel_acesso;
            $assunto = 'Nova Conta de Usuário';
            $mensagem_email = 'Olá '.$nome_usuario.',<br><br>Seja bem-vindo(a) ao sistema de Gestão STEP, do GrupoEP.<br><br>Para acessar o sistema, clique no link abaixo e faça o login com os dados abaixo:<br><br>Usuário: '.$email_usuario.'<br>Senha: '.$senha.'<br><br>Link de acesso: <a href="'.$url_sistema.'">'.$url_sistema.'</a><br><br>Atenciosamente,<br><br>Equipe STEP.';
            $template_email = '/views/emails/email-novo-usuario.php';

            include  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';
           
                                                        
        
        
    //=====================[] envia email ]===========
     
          // Exiba uma mensagem de sucesso em formato JSON
          $retorno = array('codigo' => 1, 'mensagem' => 'Novo Usuário:'.$fname.', cadastrado com Sucesso.');
          echo json_encode($retorno);
       }
     } catch(PDOException $e) {
       // Exiba uma mensagem de erro em formato JSON se houver problemas com a inserção
       $retorno = array('codigo' => 0, 'mensagem' =>'Erro ao inserir dados: ' . $e->getMessage());
       echo json_encode($retorno);
     }



} catch(PDOException $e) {
   // Exiba uma mensagem de erro se houver problemas com a inserção
   $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao inserir dados: ' . $e->getMessage());
       echo json_encode($retorno);
}

// FIM: inclui o usuário se for contato 




                         }else if($perfil_usuario=='engenheiro'){


                            $sql_valida = $conexao->query("SELECT u.email,u.nome FROM usuarios u 
                                
                            INNER JOIN colaboradores c ON c.email_corporativo  = u.email
                            
                            WHERE email='$email_usuario'
                            ");

                            $conta_v = $sql_valida->rowCount();



                            if($conta_v > 0){

                                $r = $sql_valida->fetch(PDO::FETCH_ASSOC);

                                $retorno = array('codigo' => 0, 'mensagem' => "O E-mail informado já está em uso para o usuário: ".$r['nome']);


                                echo json_encode($retorno);

                                $conexao = null;

                                exit;

                            }


// inclui o usuário se for colaborador ************



                            try {


                                // Verifique se a matricula já existe na tabela
                                $sql_selectM = "SELECT matricula FROM colaboradores WHERE matricula=:matricula";
                                $stmt_selectM = $conexao->prepare($sql_selectM);
                                $stmt_selectM->bindParam(':matricula', $matricula);
                                $stmt_selectM->execute();
                              
                                if ($stmt_selectM->rowCount() > 0) {


                                    $retorno = array('codigo' => 0, 'mensagem' => 'A Matrícula informada: '.$matricula.' , já está em uso no sistema!');


                                    echo json_encode($retorno);
            
                                    $conexao = null;
            
                                    exit;



                                }


                          
                       
                       
                           // Defina o comando SQL para inserir os dados na tabela
                           $sql = "INSERT INTO colaboradores (nome, sobrenome, filial, cel_corporativo, email_corporativo,  matricula, status_cadastro, data_cadastro) 
                                   VALUES (:nome, :sobrenome, :filial, :cel_corporativo, :email_corporativo,  :matricula, :status_cadastro, :data_cadastro)";
                       
                           // Prepare o comando SQL para executá-lo com os valores definidos
                           $stmt = $conexao->prepare($sql);
                           $stmt->bindParam(':nome', $fname);
                           $stmt->bindParam(':sobrenome', $lname);
                           $stmt->bindParam(':filial', $company);
                           $stmt->bindParam(':cel_corporativo', $phone);
                           $stmt->bindParam(':email_corporativo', $email_usuario);
                           $stmt->bindParam(':matricula', $matricula);
                           $stmt->bindParam(':status_cadastro', $status_cadastro);
                           $stmt->bindParam(':data_cadastro', $data_cadastro);
                       
                           // Execute o comando SQL preparado
                           $stmt->execute();
                       
                           // Exiba uma mensagem de sucesso se a inserção foi bem-sucedida
                          // echo "Dados inseridos com sucesso!";


                            // Obtenha o ID do último registro inserido
                                   $last_id = $conexao->lastInsertId();



                           try {
                              
                          
                             
                               // Verifique se o e-mail já existe na tabela
                               $sql_select = "SELECT * FROM usuarios WHERE email=:email";
                               $stmt_select = $conexao->prepare($sql_select);
                               $stmt_select->bindParam(':email', $email_usuario);
                               $stmt_select->execute();
                             
                               if ($stmt_select->rowCount() > 0) {
                                 // Se o e-mail já existe, retorne uma mensagem de erro em formato JSON
                                 $retorno = array('codigo' => 0, 'mensagem' =>  'O e-mail já está cadastrado no sistema!');
                                 echo json_encode($retorno);
                               } else {
                                 // Se o e-mail não existe, insira os dados na tabela
                                 $sql_insert = "INSERT INTO usuarios ( nivel, nome, email, telefone,  senha, bd_id, bd_nome, status, chave) 
                                                VALUES (:nivel, :nome, :email, :telefone, :senha, :bd_id, :bd_nome, :status, :chave)";
                                 $stmt_insert = $conexao->prepare($sql_insert);
                                 $stmt_insert->bindParam(':nivel', $nivel_acesso);
                                 $stmt_insert->bindParam(':nome', $fname);
                                 $stmt_insert->bindParam(':email', $email_usuario);
                                 $stmt_insert->bindParam(':telefone', $phone);
                                 $stmt_insert->bindParam(':senha', $senha);
                                 $stmt_insert->bindParam(':bd_id', $last_id);
                                 $stmt_insert->bindParam(':bd_nome', $perfil_usuario);
                                 $stmt_insert->bindParam(':status', $status_cadastro);
                                 $stmt_insert->bindParam(':chave', $chave_unica);
                                 $stmt_insert->execute();


                                 $novo_id_usuario = $conexao->lastInsertId();



                                                                                
                                            //=====================[] envia email ]===========

                                                    $chave_unica = $chave_unica;        

                                                    $id_usuario=$novo_id_usuario;
                                                    $email_usuario =$email_usuario; // destinatário padrão
                                                    $nome_usuario = $fname;
                                                    $nivel_acesso = $nivel_acesso;
                                                    $assunto = 'Nova Conta de Engenheiro - STEP';
                                                    $mensagem_email = 'Olá '.$nome_usuario.',<br><br>Seu cadastro foi realizado com sucesso!<br><br>Para acessar o sistema, clique no link abaixo:<br><br><a href="https://www.step.eco.br">https://www.step.eco.br</a><br><br>Atenciosamente,<br><br>Equipe de Suporte Técnico<br><br>STEP';
                                                    $template_email = '/views/emails/email-novo-usuario.php';

                                                    include  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';
                                                   
                                                    
                                                
                                                
                                            //=====================[] envia email ]===========

                             
                                 // Exiba uma mensagem de sucesso em formato JSON
                                 $retorno = array('codigo' => 1, 'mensagem' => 'Novo Usuário:'.$fname.', cadastrado com Sucesso.');
                                 echo json_encode($retorno);
                               }
                             } catch(PDOException $e) {
                               // Exiba uma mensagem de erro em formato JSON se houver problemas com a inserção
                               $retorno = array('codigo' => 0, 'mensagem' =>'Erro ao inserir dados: ' . $e->getMessage());
                               echo json_encode($retorno);
                             }



                       } catch(PDOException $e) {
                           // Exiba uma mensagem de erro se houver problemas com a inserção
                           $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao inserir dados: ' . $e->getMessage());
                               echo json_encode($retorno);
                       }

                          
                         }


                                                 


 }


if($acao=='altera_nivel_acesso' && $id!=''){

$sql=$conexao->query("UPDATE usuarios SET nivel='$nivel_acesso' WHERE id='$id'");



if($sql){


     
//=====================[] envia email ]===========

$sql = $conexao->query("SELECT * FROM usuarios u WHERE u.id='$id' ");
$r_email = $sql->fetch(PDO::FETCH_OBJ);

$chave_unica = $chave_unica;        

$id_usuario=$id;
$email_usuario =$r_email->email; // destinatário padrão
$nome_usuario = $r_email->nome;
$nivel_acesso = $r_email->nivel;
$assunto = 'Nível de Acesso Alterado';
$mensagem_email = "Seu nível de acesso foi alterado para: ".$nivel_acesso;
$template_email = '/views/emails/email-padrao.php';

include  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';

                                           




//=====================[] envia email ]===========

    $retorno = array('codigo' => 1, 'mensagem' => 'Nível de acesso alterado com Sucesso.');
    echo json_encode($retorno);
}else{


    $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao inserir dados: ' . $sql->getMessage());
       echo json_encode($retorno);
}

$conexao=null;

}



if($acao=='vincula_conta_google' && $id!=''){


    if (!filter_var($emailGoogle, FILTER_VALIDATE_EMAIL)) {
        $erro = array('mensagem' => 'O email fornecido não é válido.');
        echo json_encode($erro);
        exit();
    }
    
    if ($emailGoogle !== $confirmaEmailGoogle) {
        $erro = array('mensagem' => 'Os emails fornecidos não são iguais.');
        echo json_encode($erro);
        exit();
    }

    $sql=$conexao->query("UPDATE usuarios SET email_google='$emailGoogle' WHERE id='$id'");
    
    
    if($sql){

        //=====================[] envia email ]===========

        $chave_unica = $chave_unica;        

        $id_usuario=$id;
        $email_usuario =$email_usuario; // destinatário padrão
        $nome_usuario = $fname;
        $nivel_acesso = $nivel_acesso;
        $assunto = 'Conta Google Vinculada com Sucesso';
        $mensagem_email = "Olá você está recebendo este email, pois sua conta Google foi 
        vinculada ao seu login do STEP, agora você poderá realizar o login, de forma mais rápida e fácil. <br>Caso prefira, poderá utilizar seus dados de email e senha, normalmente.<br>Lembrando que esta opçõa é gerenciada em seu Perfil de usuário, dentro do STEP.";
        $template_email = '/views/emails/email-padrao.php';

       
        include  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';

   
    
    
//=====================[] envia email ]===========
        
    
        $retorno = array('codigo' => 1, 'mensagem' => 'Conta Google, vinculada com Sucesso.');
        echo json_encode($retorno);
    }else{
    
    
        $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao inserir dados: ' . $sql->getMessage());
           echo json_encode($retorno);
    }
    
    $conexao=null;
    
    }





    
if($acao=='cancela_vincula_conta_google' && $id!=''){

    $sql=$conexao->query("UPDATE usuarios SET email_google='' WHERE id='$id'");


    if($sql){

        //=====================[] envia email ]===========

        $chave_unica = $chave_unica;        

        $id_usuario=$id;
        $email_usuario =$email_usuario; // destinatário padrão
        $nome_usuario = $fname;
        $nivel_acesso = $nivel_acesso;
        $assunto = 'Conta Google Desvinculada com Sucesso';
        $mensagem_email = "Olá você está recebendo este email, pois sua conta Google foi Desvinculada ao seu login do STEP. <br>Acesse normalmente através de seu login e senha e lembre-se caso tenha esquecido a senha, você poderá solicitar a alteração de senha durante o acesso.";
        $template_email = '/views/emails/email-padrao.php';


        include  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';
       
           
    
    
//=====================[] envia email ]===========
        
    
        $retorno = array('codigo' => 1, 'mensagem' => 'Conta Google, Desvinculada com Sucesso.');
        echo json_encode($retorno);
    }else{
    
    
        $retorno = array('codigo' => 0, 'mensagem' => 'Erro ao inserir dados: ' . $sql->getMessage());
           echo json_encode($retorno);
    }
    
    $conexao=null;
    
    }









    ?>
  
