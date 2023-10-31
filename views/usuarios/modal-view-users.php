<?php 
// Instancia Conexão PDO
if (!isset($_SESSION)) session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
include_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';


$usuario_sessao = isset($_SESSION['id']) ?? '';


$_SESSION['pagina_atual'] = 'Busca Usuários Projeto';

$projeto_atual = $_GET['id'] ?? '';


$projeto_user = $_GET['user'] ??'';

$Data_Atual_Periodo = date_create()->format('Y-m-d ');
// == a data está pegando desde 2019, assim que o módulo for liberado, alterar a data de invervalo para -7 dias entre -14 dias
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-90 days', strtotime($Data_Atual_Periodo)));

$nivel_acesso_user_sessao =  isset($_COOKIE['nivel_acesso_usuario']) ? trim($_COOKIE['nivel_acesso_usuario']) : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';
$id_usuario_sessao = trim(isset($_SESSION['id'])) ? $_SESSION['id'] : '';

$sql_personalizado_view_users = '';
$incremento_sql ='';

if($projeto_user!=''){

    $incremento_sql = "OR (up.id_usuario = '$id_usuario_sessao')";
}


if ($nivel_acesso_user_sessao == 'supervisor') {

    $sql_personalizado_view_users = " AND e.supervisor = '$id_BD_Colaborador' $incremento_sql ";
}

if ($nivel_acesso_user_sessao == 'ro') {

    $sql_personalizado_view_users = " AND e.ro = '$id_BD_Colaborador' $incremento_sql";
}

if ($nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado_view_users = " AND up.id_usuario = '$id_usuario_sessao'";
}

if ($nivel_acesso_user_sessao == 'admin') {

    $sql_personalizado_view_users = " ";
}
                          
                     
                          
                          ?>
  <div class="modal fade" id="kt_modal_view_users" tabindex="-1" aria-hidden="true">
      <!--begin::Modal dialog-->
      <div class="modal-dialog mw-650px">
          <!--begin::Modal content-->
          <div class="modal-content">
              <!--begin::Modal header-->
              <div class="modal-header pb-0 border-0 justify-content-end">
                  <!--begin::Close-->
                  <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                      <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                      <span class="svg-icon svg-icon-1">
                          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                              <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                          </svg>
                      </span>
                      <!--end::Svg Icon-->
                  </div>
                  <!--end::Close-->
              </div>
              <!--begin::Modal header-->
              <!--begin::Modal body-->
              <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                  <!--begin::Heading-->
                  <div class="text-center mb-13">
                      <!--begin::Title-->
                      <h1 class="mb-3">Operadores Ativos</h1>
                      <!--end::Title-->
                      <!--begin::Description-->
                      <div class="text-muted fw-semibold fs-5">Usuários com + Leituras nos
                          <a href="javascript:;" class="link-primary fw-bold"> Últimos 90 Dias</a>.
                      </div>
                      <!--end::Description-->
                  </div>
                  <!--end::Heading-->
                  <!--begin::Users-->
                  <div class="mb-15">
                      <!--begin::List-->
                      <div class="mh-375px scroll-y me-n7 pe-7">


                      <?php


                          if($projeto_atual!=''){

                            

                           

                            $sql_conta_colab = $conexao->query("SELECT COUNT( r.id_operador) as Total_leituras,
                            u.id, u.nome, u.foto, u.email, u.nivel
                            FROM rmm r 
                            INNER JOIN pontos_estacao p ON p.id_ponto = r.id_ponto
                            INNER JOIN estacoes e ON e.id_estacao = p.id_estacao
                            INNER JOIN usuarios u ON r.id_operador = u.id

                            LEFT JOIN usuarios_projeto up On up.id_obra = e.id_obra

                            WHERE  p.id_obra='$projeto_atual' AND r.data_leitura >= '$Data_Intervalo_Periodo'
                            $sql_personalizado_view_users
                            GROUP BY u.id ORDER BY Total_leituras DESC
                                                                                       
                                            
                                            ");

//print_r($sql_conta_colab);



                          } else {

                            $sql_conta_colab = $conexao->query("SELECT COUNT( r.id_operador) as Total_leituras,
                            u.id, u.nome, u.foto, u.email, u.nivel
                            FROM rmm r 
                            INNER JOIN pontos_estacao p ON p.id_ponto = r.id_ponto
                            INNER JOIN estacoes e ON e.id_estacao = p.id_estacao
                            INNER JOIN usuarios u ON r.id_operador = u.id

                            LEFT JOIN usuarios_projeto up On up.id_obra = e.id_obra
                            WHERE r.data_leitura >= '$Data_Intervalo_Periodo'  
                            $sql_personalizado_view_users
                            GROUP BY u.id ORDER BY Total_leituras DESC
                                                               
                    
                    ");

                    }

                            $conta = $sql_conta_colab->rowCount();


                           


                            if ($conta > 0) {

                                $row = $sql_conta_colab->fetchALL(PDO::FETCH_ASSOC);

                                foreach ($row as $r) {

                                    $Total_leituras = $r['Total_leituras'];
                                   
                                    $id_user = $r['id'];

                                    $nome_user =$r['nome'];
                                    $brev_nome_user = substr($r['nome'], 0, 1);

                                    $foto_user = $r['foto'];

                                                        
                                    if ($id_user % 2 == 0) {
                                        //echo "Numero Par"; 
                                        $classe = 'info';
                                    } else {
                                        $classe = 'warning';
                                        //echo "Numero Impar"; }
                                    }


                                    $filename = '/foto-perfil/' . $foto_user;
                                    
                                    if (file_exists($filename)) {

                                        $retorno_foto= '<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="' . $nome_user . '">
                                        <img alt="Foto Usuário" /foto-perfil/' . $foto_user . '" />
                                    </div>';

                                    } else {

                                        $retorno_foto= '<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="' . $nome_user . '">
                                        <span class="symbol-label bg-' . $classe . ' text-inverse-' . $classe . ' fw-bold">' . $brev_nome_user . '</span>
                                    </div>';
                                    }



                                    echo ' <!--begin::User-->
                                                    <div class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Avatar-->
                                                           ' . $retorno_foto . '
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-6">
                                                                <!--begin::Name-->
                                                             <a href="../../views/conta-usuario/overview.php?id=' . $r['id'] . '" class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">' . $r['nome'] . '
                                                                    <span class="badge badge-light fs-8 fw-semibold ms-2">' . $r['nivel'] . '</span></a>
                                                                <!--end::Name-->
                                                                <!--begin::Email-->
                                                                <div class="fw-semibold text-muted">' . $r['email'] . '</div>
                                                                <!--end::Email-->
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Stats-->
                                                        <div class="d-flex">
                                                            <!--begin::Leituras-->
                                                            <div class="text-end">
                                                                <div class="fs-5 fw-bold text-dark">' . $r['Total_leituras'] . '</div>
                                                                <div class="fs-7 text-muted">Leituras</div>
                                                            </div>
                                                            <!--end::Leituras-->
                                                        </div>
                                                        <!--end::Stats-->
                                                    </div>
                                                    <!--end::User-->';
                                }
                            }

                            ?>




                      </div>
                      <!--end::List-->
                  </div>
                  <!--end::Users-->
                  <!--begin::Notice-->
                  <div class="d-flex justify-content-between">
                      <!--begin::Label-->
                      <div class="fw-semibold">
                          <label class="fs-6">Listagem de Operadores</label>
                          <div class="fs-7 text-muted">Usuários que registraram Leituras no Sistema e ou Projeto Selecionado.</div>
                      </div>
                      <!--end::Label-->

                  </div>
                  <!--end::Notice-->
              </div>
              <!--end::Modal body-->
          </div>
          <!--end::Modal content-->
      </div>
      <!--end::Modal dialog-->
  </div>