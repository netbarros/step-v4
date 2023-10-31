<?php
 // buffer de saída de dados do php]
// Instancia Conexão PDO
if (!isset($_SESSION)) session_start();
require_once "../../conexao.php";
$conexao = Conexao::getInstance();
require_once "./../../crud/login/verifica_sessao.php";

$_SESSION['pagina_atual'] = 'Dashboard Usuários';

$usuario_sessao = isset($_SESSION['id']) ?? '';


$projeto_user = '';

if (isset($_GET['user'])) {
    $projeto_user = $_GET['user'];
} elseif (isset($_COOKIE['id_usuario_sessao'])) {
    $projeto_user = $_COOKIE['id_usuario_sessao'];
}


$nivel_acesso_user_sessao =  isset($_COOKIE['nivel_acesso_usuario']) ? trim($_COOKIE['nivel_acesso_usuario']) : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';
$id_usuario_sessao = trim(isset($_SESSION['id'])) ? $_SESSION['id'] : '';


if ($nivel_acesso_user_sessao != 'admin') {
    $sql_consulta = " AND (es.supervisor = '$id_BD_Colaborador' OR es.ro = '$id_BD_Colaborador' OR up.id_usuario = '$id_usuario_sessao')";
} else {
    $sql_consulta = "";
}




$query = sprintf("
    SELECT 
        COUNT(rmm.id_operador) AS total_operador,
        u.nome,
        u.id,
        u.foto
    FROM 
        rmm AS rmm
        INNER JOIN pontos_estacao AS pe ON pe.id_ponto = rmm.id_ponto
        INNER JOIN obras AS ob ON ob.id_obra = pe.id_obra
        INNER JOIN estacoes AS es ON es.id_estacao = ob.id_obra
        LEFT JOIN usuarios AS u ON u.id = rmm.id_operador
        LEFT JOIN usuarios_projeto AS up ON up.id_obra = ob.id_obra
    WHERE
    rmm.status_leitura <>'5' %s
    GROUP BY 
        rmm.id_operador,
        u.nome,
        u.id,
        u.foto
    ORDER BY 
        u.id DESC
    LIMIT 
        0, 10
", $sql_consulta);

$sql_conta_colab = $conexao->query($query);

                                               
                                                $conta = $sql_conta_colab->rowCount();

//print_r($sql_conta_colab); up.id_usuario = $id_usuario_sessao 

                                                if ($conta > 0) {

                                                    $row = $sql_conta_colab->fetchALL(PDO::FETCH_ASSOC);

                                                    $total_colab='';

                                                   

                                                    foreach ($row as $r) {

                                                        $nome_user = $r['nome'] ?? '';

                                                        $nome_user = (string) $nome_user;
                                                        $brev_nome_user = substr($nome_user, 0, 1);

                                                        if ($nome_user !== null) {
                                                            $brev_nome_user = substr($nome_user, 0, 1);
                                                        } else {
                                                            // Trate o caso em que $nome_user é nulo.
                                                            // Por exemplo, atribua um valor padrão a $brev_nome_user ou exiba uma mensagem de erro.

                                                            $nome_user = 'John Doe';
                                                            $brev_nome_user = substr($nome_user, 0, 1);
                                                        }


                                                        $id_user = $r['id'];
                                                       
                                                        $foto_user = $r['foto'];
                                                        $total_colab = $r['total_operador'];
                                                        
                                                        if ($id_user % 2 == 0) {
                                                            //echo "Numero Par"; 
                                                            $classe = 'light-info';
                                                        } else {
                                                            $classe = 'light-warning';
                                                            //echo "Numero Impar"; }
                                                        }


                                                        $filename = '/foto-perfil/' . $foto_user;
                                                        
                                                        
                                                        if (file_exists($filename)) {

                                                            $retorno_foto= '<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="' . $nome_user . '">
                                                            <img alt="Foto Usuário" src="' . $foto_user . '" />
                                                        </div>';

                                                        } else {

                                                            $retorno_foto= '<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="' . $nome_user . '">
                                                            <span class="symbol-label bg-' . $classe . ' text-inverse-' . $classe . ' fw-bold">' . $brev_nome_user . '</span>
                                                        </div>';
                                                        }





                                                        echo $retorno_foto;


                                                        

                                                      
                                                    }


                                                    echo'
                                                        <a href="javascript:;" class="symbol symbol-35px symbol-circle"
                                                            data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                                                            <span
                                                                class="symbol-label  fs-8 fw-bold">
                                                                
<!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/kt-products/docs/metronic/html/releases/2023-01-30-131017/core/html/src/media/icons/duotune/general/gen052.svg-->
<span class="svg-icon svg-icon-info svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect x="10" y="10" width="4" height="4" rx="2" fill="currentColor"/>
<rect x="17" y="10" width="4" height="4" rx="2" fill="currentColor"/>
<rect x="3" y="10" width="4" height="4" rx="2" fill="currentColor"/>
</svg>
</span>
<!--end::Svg Icon-->

                                                                </span>
                                                        </a>';

                                                        $conexao=null;
                                                }else{


                                                }




                                
                                            
?>