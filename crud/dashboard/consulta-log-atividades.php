<?php
require_once '../../conexao.php';
header("Content-Type: application/json");
date_default_timezone_set('America/Sao_Paulo');
// Atribui uma conexão PDO
     $conexao = Conexao::getInstance();
     if (!isset($_SESSION)) session_start();	

//data e hora do log

// pega hora atual php
$hora_atual = date('H:i');

$retorno_sem_leitura_hoje = "";

function intervalo($entrada, $saida)
{
    $entrada = explode(':', $entrada);
    $saida   = explode(':', $saida);
    $minutos = ($saida[0] - $entrada[0]) * 60 + $saida[1] - $entrada[1];
    if ($minutos < 0) $minutos += 24 * 60;
    return sprintf('%d:%d', $minutos / 60, $minutos % 60);
}
//====[ Inicia consulta para atividades no painel]===>>


 
   $nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';
   
$projeto_atual = (isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';
if($projeto_atual!=''){


    $filtro = "AND o.id_obra = ' $projeto_atual' GROUP BY p.id_ponto";

} else{

    $filtro = 'GROUP BY o.id_obra ';
}

$sql_personalizado = '';

$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';
if ($nivel_acesso_user_sessao == 'supervisor') {

    $sql_personalizado = "WHERE e.supervisor = '$id_BD_Colaborador' OR up.id_usuario  = '$id_usuario_sessao' ";
}

if ($nivel_acesso_user_sessao == 'ro') {

    $sql_personalizado = "WHERE e.ro = '$id_BD_Colaborador'  OR up.id_usuario ='$id_usuario_sessao'";
}

if ($nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado = "WHERE  up.id_usuario ='$id_usuario_sessao'" ;
}




   $sql = $conexao->query("SELECT log.*, e.nome_estacao, o.nome_obra, o.id_obra, tp.*, u.* FROM log_leitura log
   INNER JOIN tipo_log tp ON tp.id_tipo_log  = log.tipo_log
   INNER JOIN estacoes e ON e.id_estacao = log.estacao_logada
   INNER JOIN obras o ON o.id_obra = e.id_obra
   INNER JOIN usuarios u ON u.id = log.id_usuario
   LEFT JOIN usuarios_projeto up ON up.id_obra = o.id_obra      
   $sql_personalizado
   ORDER BY log.data DESC LIMIT 50");


$total = $sql->rowCount();

    //resgata os dados na tabela
    if ($total > 0) {

        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

   


        foreach ($resultado as $res) {


            //var_dump($res);

            $data_consulta = date('Y-m-d');

            $data_log = $res['data'];
            $hora_format =  date('H:i', strtotime($data_log));
            $dia_format =  date('d/m/Y', strtotime($data_log));

            $nome_user = $res['nome'];
            $brev_nome_user = substr($nome_user, 0, 1);


?>



 <!--begin::Timeline item Log de Tarefa-->
 <div class="timeline-item">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-40px"></div>
                                <!--end::Timeline line-->
                                <!--begin::Timeline icon-->
                                <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                                    <div class="symbol-label bg-light">
                                        <!--begin::Svg Icon | path: icons/duotune/communication/com003.svg-->
                                        <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3" d="M2 4V16C2 16.6 2.4 17 3 17H13L16.6 20.6C17.1 21.1 18 20.8 18 20V17H21C21.6 17 22 16.6 22 16V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4Z" fill="currentColor" />
                                                <path d="M18 9H6C5.4 9 5 8.6 5 8C5 7.4 5.4 7 6 7H18C18.6 7 19 7.4 19 8C19 8.6 18.6 9 18 9ZM16 12C16 11.4 15.6 11 15 11H6C5.4 11 5 11.4 5 12C5 12.6 5.4 13 6 13H15C15.6 13 16 12.6 16 12Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </div>
                                </div>
                                <!--end::Timeline icon-->
                                <!--begin::Timeline content-->
                                <div class="timeline-content mb-10 mt-n1">
                                    <!--begin::Timeline heading-->
                                    <div class="pe-1 mb-2">
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-semibold mb-2"><a data-bs-toggle="tooltip" data-bs-placement="top" title="Acesse o Dashboard do Usuário" href="../../views/conta-usuario/overview.php?id=<?=$res['id'];?>" class="fs-5 text-dark text-hover-primary fw-semibold w-375px min-w-200px"><?=$res['nome'];?></a></div>
                                        <!--end::Title-->
                                        <!--begin::Description-->
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                             <!--begin::User-->
                                             <div class="symbol symbol-circle symbol-25px" data-bs-toggle="tooltip" data-bs-boundary="window" data-bs-placement="top" title="<?=$res['nome'];?>">
                                              
                                              <?php
  
                                                  $filename = '/foto-perfil/'.$res['foto'];
  
                                           if ($res['foto']!='' && file_exists($filename)) {
  
                                                  $foto_perfil = ' <img class="h-30px w-30px rounded" src="/foto-perfil/' . $res['foto'] . '" alt="' . $brev_nome_user . '" />';
                                              } else {
                                                  $foto_perfil = ' <span class="align-items-center d-flex px-3 h-30px w-30px rounded symbol-label bg-success fs-4 text-inverse-primary fw-bold">' . $brev_nome_user . '</span>';
                                              }
  
                                              echo $foto_perfil;
  
                                              ?>
                                              
                                              
                                              </div>
                                              <!--end::User-->

                                            <!--begin::Info-->
                                            <div class="text-muted me-6 fs-7 px-4">Criado às <?=$hora_format;?>, do dia <?=$dia_format;?></div>
                                            <!--end::Info-->
                                           
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Timeline heading-->
                                    <!--begin::Timeline details-->
                                    <div class="overflow-auto ">
                                        <!--begin::Record-->
                                        <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-450px px-5 py-3 mb-5">
                                            <!--begin::Title-->
                                            <a href="javascript:;" class="fs-5 text-dark text-hover-primary fw-semibold w-250px min-w-150px"><?=$res['nome_tipo_log'];?></a>
                                            <!--end::Title-->
                                            <!--begin::Label-->
                                            <div class="min-w-100px pe-0">
                                                <span class="badge badge-light text-muted"><?=$res['nome_obra'];?></span>
                                            </div>
                                            <!--end::Label-->
                                            <!--begin::Users-->
                                            <div class="symbol-group symbol-hover flex-nowrap flex-grow-1 min-w-150px pe-1">
                                               <?=$res['nome_estacao'];?>
                                            </div>
                                            <!--end::Users-->
                                            
                                            <!--begin::Action-->
                                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="Acesse o Dashboard do Projeto" href="../../views/projetos/view-project.php?id=<?=$res['id_obra'];?>&projeto=<?=$res['nome_obra'];?>" class="btn btn-sm btn-light btn-active-light-primary">Ver</a>
                                            <!--end::Action-->
                                        </div>
                                        <!--end::Record-->
                                      
                                    </div>
                                    <!--end::Timeline details-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->


<?php }


$conexao=null;

}?>                            