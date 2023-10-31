<?php
header("content-type: application/json");
//require_once '../../conexao.php';
// Atribui uma conexão PDO

require '../../conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();



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


// Verifica se os dados foram enviados por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recebe as variáveis via POST
    $acao = isset($_POST['acao']) ? $_POST['acao'] : null;
    $id_cliente = isset($_POST['id_cliente']) ? $_POST['id_cliente'] : null;
    $razao_social = isset($_POST['razao_social']) ? $_POST['razao_social'] : null;
    $nome_fantasia = isset($_POST['nome_fantasia']) ? $_POST['nome_fantasia'] : null;
    $cnpj = isset($_POST['cnpj']) ? $_POST['cnpj'] : null;
    $telefone = isset($_POST['telefone']) ? $_POST['telefone'] : null;
    $email_geral = isset($_POST['email_geral']) ? $_POST['email_geral'] : null;
    $email_nfe = isset($_POST['email_nfe']) ? $_POST['email_nfe'] : null;
    $site_cliente = isset($_POST['site_cliente']) ? $_POST['site_cliente'] : null;
    $status_cadastro = isset($_POST['status_cadastro']) ? $_POST['status_cadastro'] : null;

    // Executa a ação correspondente
    switch ($acao) {
        case 'inserir':
            // Aqui vai o código para inserir os dados no banco de dados


              // Verifica se já existe o mesmo email em uso no sistema
            if(isset($cnpj)):
                $consulta_cnpj = $conexao->query("SELECT count(id_cliente) FROM clientes where cnpj='$cnpj'")->fetchColumn();
                $mensagem = '';   

                if ($consulta_cnpj > 0) {
                //usuário está registrado
                //faça o insert aquis
                $mensagem='<h3> Integridade dos Dados </h3>';
                $mensagem .="<p>O CNPJ: <b>".$cnpj." </b>, Já está em uso no Sistema!</p>";

                    if ($mensagem != ''):
                        $mensagem = "$mensagem";
                    
                        $retorno = array('codigo' => 0, 'mensagem' => $mensagem);
                                
                        echo json_encode($retorno);
                    endif;
                exit;

                }
            endif;


                try {
                    $stmt = $conexao->prepare("INSERT INTO clientes ( 
                        nome_fantasia,
                        razao_social,
                        cnpj,
                        site_cliente,
                        email_nfe,
                        email_geral,
                        gestao_step,
                        status_cadastro,
                        telefone
                        ) 
                    VALUES (?, ?,?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bindParam(1, $nome_fantasia);
                    $stmt->bindParam(2, $razao_social);
                    $stmt->bindParam(3, $cnpj);
                    $stmt->bindParam(4, $site_cliente);
                    $stmt->bindParam(5, $email_nfe);
                    $stmt->bindParam(6, $email_geral);
                    $stmt->bindParam(7, $gestao_step);
                    $stmt->bindParam(8, $status_cadastro);
                    $stmt->bindParam(9, $telefone);

                
                    
                    
                    if ($stmt->execute()) {
                        

                        if ($stmt->rowCount() > 0) {

                            $id_cliente = $conexao->lastInsertId();

                            $retorno = array('codigo' => 1,  'mensagem' => 'Novo Cliente, cadastrado com Sucesso!', 'id'=>$ultimo_id);


                            echo json_encode($retorno);

                            exit;



                        }else {


                            $retorno = array('codigo' => 0,  'mensagem' => 'Falha ao Gravar o Cliente!');


                            echo json_encode($retorno);

                            exit;

                        }

                    }


                } catch (PDOException $erro) {
                    echo "Erro: " . $erro->getMessage();
                    }
            break;

        case 'atualizar':
            // Aqui vai o código para atualizar os dados no banco de dados

            
                // Verifica se já existe o mesmo email em uso no sistema
            if(isset($cnpj)):
                $consulta_cnpj = $conexao->query("SELECT count(id_cliente) FROM clientes where cnpj='$cnpj'")->fetchColumn();
                $mensagem = '';   
            
                if ($consulta_cnpj > 1) {
                //usuário está registrado
                //faça o insert aquis
                $mensagem='<h3> Integridade dos Dados </h3>';
                $mensagem .="<p>O CNPJ: <b>".$cnpj." </b>, Já está em uso no Sistema!</p>";
            
                    if ($mensagem != ''):
                        $mensagem = "$mensagem";
                    
                        $retorno = array('codigo' => 0, 'mensagem' => $mensagem);
                                
                        echo json_encode($retorno);
                    endif;
                exit;
            
                }
            endif;
            
            
            try {
                $stmt = $conexao->prepare("UPDATE clientes SET 
                    nome_fantasia = ?,
                    razao_social = ?,
                    cnpj = ?,
                    site_cliente = ?,
                    email_nfe = ?,
                    email_geral = ?,
                    gestao_step = ?,
                    status_cadastro = ?,
                    telefone = ?
                    WHERE id_cliente = ?");
                
                $stmt->bindParam(1, $nome_fantasia);
                $stmt->bindParam(2, $razao_social);
                $stmt->bindParam(3, $cnpj);
                $stmt->bindParam(4, $site_cliente);
                $stmt->bindParam(5, $email_nfe);
                $stmt->bindParam(6, $email_geral);
                $stmt->bindParam(7, $gestao_step);
                $stmt->bindParam(8, $status_cadastro);
                $stmt->bindParam(9, $telefone);
                $stmt->bindParam(10, $id_cliente);
            
                if ($stmt->execute()) {
                    $num_rows = $stmt->rowCount();
                    if ($num_rows > 0) {
                        $retorno = array('codigo' => 1, 'mensagem' => 'Cliente atualizado com sucesso!');
                        echo json_encode($retorno);
                        exit;
                    } else {
                        $retorno = array('codigo' => 0, 'mensagem' => 'Nenhum registro atualizado!');
                        echo json_encode($retorno);
                        exit;
                    }
                }
            } catch (PDOException $erro) {
                echo "Erro: " . $erro->getMessage();
            }

            
            break;
        case 'excluir':
            // Aqui vai o código para excluir os dados no banco de dados
            break;
        default:
            // Ação inválida
            echo "Ação inválida";
            break;
    }
}






?>