<?php

$projeto_id = isset($_GET['id']) && is_numeric($_GET['id']) 
                ? intval($_GET['id']) 
                : $_COOKIE['projeto_atual'];
?>
 <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                                        <!--begin::Image-->
                                        <div class="d-flex align-items-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4 bg-light-<?= $classe_status; ?>">
                                            <span class="symbol-label fs-1 bg-light-<?= $classe_status; ?> text-<?= $classe_status; ?>"><?= $brev_nome_projeto; ?></span>

                                        </div>
                                        <!--end::Image-->
                                        <!--begin::Wrapper-->
                                        <div class="flex-grow-1">
                                            <!--begin::Head-->
                                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">

                                                <!--begin::Details-->
                                                <div class="d-flex flex-column">
                                                    <!--begin::Status-->
                                                    <div class="d-flex align-items-center mb-2">
                                                        <a href="javascript:;" class="text-gray-800 text-hover-primary fs-2 fw-bold me-3 "><?php echo $r_proj['nome_obra']; ?></a>
                                                        <span class="badge badge-light-<?= $classe_status; ?> me-auto"><?= $nome_status; ?></span>
                                                    </div>
                                                    <!--end::Status-->
                                                    <!--begin::Description-->
                                                    <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400"># <?php echo $r_proj['nome_contato'] ?? 'Contato não Informado.'; ?> <?php echo $r_proj['sobrenome_contato'] ?? 'Sobrenome não informado.'; ?></div>
                                                    <!--end::Description-->
                                                </div>
                                                <!--end::Details-->

                                                
                                            </div>
                                            <!--end::Head-->
                                            <!--begin::Info-->
                                            <div class="d-flex flex-wrap justify-content-start">
                                                <!--begin::Stats-->
                                                <div class="d-flex flex-wrap">
                                                    <!--begin::Stat-->
                                                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                        <!--begin::Number-->
                                                        <div class="d-flex align-items-center">
                                                            <div class="fs-4 fw-bold">
                                                                <?php
                                                                $data_cadastro = new DateTime($r_proj['data_cadastro']);
                                                                echo $data_cadastro->format('d/m/Y ');
                                                                ?>

                                                            </div>
                                                        </div>
                                                        <!--end::Number-->
                                                        <!--begin::Label-->
                                                        <div class="fw-semibold fs-6 text-gray-400">Projeto Criado</div>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Stat-->
                                                    <!--begin::Stat-->
                                                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                        <!--begin::Number-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                                                            <span class="svg-icon svg-icon-3 svg-icon-danger me-2">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor" />
                                                                    <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor" />
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->

                                                            <?php
                                                            // connsulta tarefas


                                                            ?>

                                                            <div class="fs-4 fw-bold" data-kt-countup="true" data-kt-countup-value="0">0</div>
                                                        </div>
                                                        <!--end::Number-->
                                                        <!--begin::Label-->
                                                        <div class="fw-semibold fs-6 text-gray-400">Tarefas em Aberto</div>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Stat-->
                                                    <!--begin::Stat-->
                                                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                        <!--begin::Number-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                                            <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor" />
                                                                    <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor" />
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->
                                                            <?php
                                                            // connsulta Orçamento


                                                            ?>
                                                            <div class="fs-4 fw-bold" data-kt-countup="true" data-kt-countup-value="0,00" data-kt-countup-prefix="R$ "> 0,00</div>
                                                        </div>
                                                        <!--end::Number-->
                                                        <!--begin::Label-->
                                                        <div class="fw-semibold fs-6 text-gray-400">Orçamento Utilizado</div>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Stat-->
                                                </div>
                                                <!--end::Stats-->

                                                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-3">
                                                <!--begin::Wrapper-->
                                                <div class="d-flex flex-stack flex-grow-1">
                                                    <!--begin::Content-->
                                                    <div class="fw-semibold">
                                                        <div class="fs-6 text-gray-700">
                                                            <a href="javascript:;" class="fw-bold me-1" data-bs-toggle="modal" data-bs-target="#kt_modal_users_search" data-id="<?php echo $projeto_id; ?>">Vincular Usuários</a>
                                                        </div>
                                                    </div>
                                                    <!--end::Content-->
                                                </div>
                                                <!--end::Wrapper-->
                                            </div>

                                            
                                                <!--begin::Users-->
                                                <div class="symbol-group symbol-hover mb-3  p-6 mb-3">

                                                    <?php
                            $Data_Atual_Periodo = date_create()->format('Y-m-d ');
                            // == a data está pegando desde 2019, assim que o módulo for liberado, alterar a data de invervalo para -7 dias entre -14 dias
                            $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-90 days', strtotime($Data_Atual_Periodo)));


                                                    $sql_conta_colab = $conexao->query("SELECT COUNT(DISTINCT r.id_operador) as Total_usuarios,
                                                    u.id, u.nome, u.foto, u.email, u.nivel
                                            FROM rmm r 
                                            INNER JOIN pontos_estacao p ON p.id_ponto = r.id_ponto
                                           INNER JOIN estacoes e ON e.id_estacao = p.id_estacao
                                           INNER JOIN usuarios u ON r.id_operador = u.id

                                            WHERE p.id_obra='$projeto_atual' AND r.data_leitura >= '$Data_Intervalo_Periodo'  
                                            
                                            GROUP BY u.nome  ");



                                                   
                           

                                                    $conta = $sql_conta_colab->rowCount();



                                                    if ($conta > 0) {

                                                        $row = $sql_conta_colab->fetchALL(PDO::FETCH_ASSOC);

                                                        foreach ($row as $r_proj) {

                                                            $Total_usuarios = $r_proj['Total_usuarios'];
                                                            $foto_user = $r_proj['foto'];
                                                            $id_user = $r_proj['id'];
                                                            $nome_user = $r_proj['nome'];


                                                            $brev_nome_user = substr($nome_user, 0, 1);

                                                            if ($id_user % 2 == 0) {
                                                                //echo "Numero Par"; 
                                                                $classe = 'info';
                                                            } else {
                                                                $classe = 'primary';
                                                                //echo "Numero Impar"; }
                                                            }

                                                           $filename = '/foto-perfil/' . $foto_user ;

                                    if (file_exists($filename)) {

                                        echo '<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="' . $nome_user . '">
                                        <img alt="Foto Usuário" src="/foto-perfil/'. $foto_user . '" />
                                    </div>';

                                    } else {

                                        echo '<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="' . $nome_user . '">
                                        <span class="symbol-label bg-' . $classe . ' text-inverse-' . $classe . ' fw-bold">' . $brev_nome_user . '</span>
                                    </div>';
                                    }
                                       
                                                        } ?>

                                                        <!--end::User-->

                                                        <!--begin::All users-->
                                                        <a href="javascript:;" class="symbol symbol-35px symbol-circle" data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                                                            <span class="symbol-label bg-dark text-inverse-dark fs-8 fw-bold" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Ver mais Usuários">+<?= $Total_usuarios; ?></span>
                                                        </a>
                                                        <!--end::All users-->
                                                    <?php } ?>
                                                </div>
                                                <!--end::Users-->

                                                
                                            </div>
                                            <!--end::Info-->
                                            
                                        </div>
                                        
                                                        <a href="../../views/projetos/projects.php" class="btn   btn-active-color-success btn-flex h-40px border-0 fw-bold px-4 px-lg-6 ms-2 ms-lg-3" >
                                                        
                                                    <!--begin::Svg Icon | path: C:/wamp64/www/keenthemes/core/html/src/media/icons/duotune/communication/com007.svg-->
                                                    <span class="svg-icon svg-icon-muted svg-icon-2hx">
                                                       <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.3" d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z" fill="currentColor"></path>
                                                            <path d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                   <span class='text-dark'> Listar Projetos</span></a>
                                        <!--end::Wrapper-->

                                        
                                                    </div>
                                                    