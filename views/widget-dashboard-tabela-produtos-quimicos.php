    <div class="col-xxl-4 mb-5 mb-xl-10">
                                <!--begin::List widget 9-->
                                <div class="card card-flush h-xl-100" id='tabela-resumo-produtos-quimicos'>
                                    <!--begin::Header-->
                                    <div class="card-header py-7">
                                        <!--begin::Statistics-->
                                        <div class="m-0">
                                            <!--begin::Heading-->
                                            <div class="d-flex align-items-center mb-2">
                                                <!--begin::Title-->
                                                <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2">

                                                    <?php


                                                    $sql_prod_7_dias = $conexao->query(
                                                        "SELECT ROUND(SUM(prod_r.qtdade_utilizada),2) as qtdade_presente
                                        FROM produto_rmm prod_r 
                                        INNER JOIN rmm r ON r.id_rmm = prod_r.id_rmm 
                                         INNER JOIN produtos p ON p.id_produto = prod_r.id_produto
                                            WHERE p.id_categoria_produto ='1' AND  DATE_FORMAT(r.data_leitura, '%Y-%m-%d') >= '$Data_presente' 
                                                                      
                                                                        ORDER BY qtdade_presente DESC "
                                                    );

                                                    $conta_prod = $sql_prod_7_dias->rowCount();



                                                    $row = $sql_prod_7_dias->fetch(PDO::FETCH_ASSOC);

                                                    if ($conta_prod > 0) {

                                                        $qtdade_presente = $row['qtdade_presente'];

                                                        echo $qtdade_presente;
                                                    }
                                                    ?>

                                                </span>
                                                <!--end::Title-->
                                                <!--begin::Label-->

                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->

                                                <?php

                                                $sql_prod_14_dias = $conexao->query(
                                                    "SELECT ROUND(SUM(prod_r.qtdade_utilizada),2) as qtdade_passado
                                        FROM produto_rmm prod_r 
                                         INNER JOIN rmm r ON r.id_rmm = prod_r.id_rmm 
                                         INNER JOIN produtos prod ON prod.id_produto = prod_r.id_produto
                                         INNER JOIN produto_ponto prod_p ON prod_p.id_produto = prod.id_produto
                                         INNER JOIN pontos_estacao p ON p.id_ponto = prod_p.id_ponto
                                         INNER JOIN obras o ON o.id_obra = p.id_obra
                                         INNER JOIN estacoes e ON e.id_estacao = p.id_estacao
                                          LEFT JOIN
                            usuarios_projeto up ON up.id_obra = e.id_obra
                                            WHERE prod.status_produto ='1' 
                                            AND DATE_FORMAT(r.data_leitura, '%Y-%m-%d') >= '$Data_passado' 
                                            $sql_personalizado 
  
                                            $filtro
                                                                        
                                                                        ORDER BY qtdade_passado DESC LIMIT 0,1"
                                                );

                                                $conta_prod = $sql_prod_14_dias->rowCount();



                                                $row2 = $sql_prod_14_dias->fetch(PDO::FETCH_ASSOC);

                                                if ($conta_prod > 0) {

                                                    $qtdade_passado = $row2['qtdade_passado'];

                                                    $porcentagem_crescimento_periodo = $qtdade_passado / $qtdade_presente -  1 * $qtdade_passado;



                                                    if ($porcentagem_crescimento_periodo > 0) {

                                                        $classe_seta = 'bi bi-arrow-up-short text-success';
                                                        $badge_seta = 'svg-icon-success';
                                                    } else {

                                                        $classe_seta = 'bi bi-arrow-down-short text-danger';
                                                        $badge_seta = 'svg-icon-danger';
                                                    }

                                                    echo '  <span class="badge badge-light-success fs-base">
                                                        <span class="svg-icon svg-icon-5 ' . $badge_seta . ' ms-n1">
                                                        <i class="' . $classe_seta . '" ></i>

                                                        ' . round($porcentagem_crescimento_periodo, 2) . ' %
                                                    </span> <!--end::Svg Icon-->
                                                     </span> <!--end::Label-->';
                                                    // $porcentagem_crescimento_periodo = $x * 100;


                                                }
                                                ?>





                                            </div>
                                            <!--end::Heading-->
                                            <!--begin::Description-->
                                            <span class="fs-6 fw-semibold text-gray-400">Produtos Químicos</span>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Statistics-->
                                        <!--begin::Toolbar-->
                                        <div class="card-toolbar">
                                            <!--begin::Menu-->
                                            <button
                                                class="btn btn-icon btn-color-gray-400 btn-active-color-primary justify-content-end"
                                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                                data-kt-menu-overflow="true">
                                                <!--begin::Svg Icon | path: icons/duotune/general/gen023.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-gray-300 me-n1">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="4"
                                                            fill="currentColor" />
                                                        <rect x="11" y="11" width="2.6" height="2.6" rx="1.3"
                                                            fill="currentColor" />
                                                        <rect x="15" y="11" width="2.6" height="2.6" rx="1.3"
                                                            fill="currentColor" />
                                                        <rect x="7" y="11" width="2.6" height="2.6" rx="1.3"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </button>
                                            <!--begin::Menu 2-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px"
                                                data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">Ações
                                                        Rápidas
                                                    </div>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu separator-->
                                                <div class="separator mb-3 opacity-75"></div>
                                                <!--end::Menu separator-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="javascript:;" class="menu-link px-3">Novo Produto</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="javascript:;" class="menu-link px-3">Nova Categoria</a>
                                                </div>
                                                <!--end::Menu item-->

                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="javascript:;" class="menu-link px-3">Listar
                                                        Produtos</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu separator-->
                                                <div class="separator mt-3 opacity-75"></div>
                                                <!--end::Menu separator-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <div class="menu-content px-3 py-3">
                                                        <a class="btn btn-primary btn-sm px-4 gera_relatorio"
                                                            href="javascript:;"
                                                            data-id='tabela-resumo-produtos-quimicos'
                                                            data-titulo='Produtos Químicos'>PDF Report</a>
                                                    </div>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu 2-->
                                            <!--end::Menu-->
                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card-body card-body d-flex justify-content-between flex-column pt-3">

                                        <?php



                                        $sql_prod = $conexao->query(
                                            "SELECT ROUND(SUM(prod_r.qtdade_utilizada),2) as qtdade_utilizada,
                                            prod.nome_produto,c.nome_categoria_produto, u.nome_unidade_medida, prod.id_produto, o.id_obra
                                        FROM produto_rmm prod_r 
                                        INNER JOIN rmm r ON r.id_rmm = prod_r.id_rmm 
                                        INNER JOIN produtos prod ON prod.id_produto = prod_r.id_produto
                                        INNER JOIN produto_ponto pt On pt.id_produto = prod_r.id_produto
                                        INNER JOIN pontos_estacao p ON p.id_ponto = r.id_ponto
                                        INNER JOIN obras o ON o.id_obra = p.id_obra
                                        INNER JOIN estacoes e ON e.id_estacao = p.id_estacao
                                        LEFT JOIN categoria_produtos c ON c.id_categoria_produto = prod.id_categoria_produto
                                        LEFT JOIN unidade_medida u ON u.id_unidade_medida = prod.id_unidade_medida
 LEFT JOIN
                            usuarios_projeto up ON up.id_obra = e.id_obra
                                        WHERE prod.status_produto ='1' AND  prod_r.qtdade_utilizada IS NOT NULL AND r.data_leitura >= '$Data_passado'
                                        $sql_personalizado  $filtro
                                                                       
                                                                       
                                                                        ORDER BY prod_r.qtdade_utilizada DESC "
                                        );

                                        $conta = $sql_prod->rowCount();




                                        if ($conta > 0) {

                                            $sql_prod_7_dias = $conexao->query(
                                                "SELECT ROUND(SUM(prod_r.qtdade_utilizada),2) as qtdade_utilizada_periodo,
                                                prod.nome_produto,c.nome_categoria_produto,
                                                 u.nome_unidade_medida, prod.id_produto
                                        FROM produto_rmm prod_r 
                                        INNER JOIN rmm r ON r.id_rmm = prod_r.id_rmm 
                                        INNER JOIN produtos prod ON prod.id_produto = prod_r.id_produto
                                        INNER JOIN produto_ponto pt On pt.id_produto = prod_r.id_produto
                                        INNER JOIN pontos_estacao p ON p.id_ponto = pt.id_ponto
                                        INNER JOIN obras o ON o.id_obra = p.id_obra
                                        INNER JOIN estacoes e ON e.id_estacao = p.id_estacao
                                        LEFT JOIN categoria_produtos c ON c.id_categoria_produto = prod.id_categoria_produto
                                        LEFT JOIN unidade_medida u ON u.id_unidade_medida = prod.id_unidade_medida
                                         LEFT JOIN
                            usuarios_projeto up ON up.id_obra = e.id_obra
                                            WHERE prod.status_produto ='1' 
                                            AND  prod_r.qtdade_utilizada IS NOT NULL 
                                            AND DATE_FORMAT(r.data_leitura, '%Y-%m-%d') >= '$Data_Intervalo_Periodo' 
                                            $sql_personalizado
                                                                        GROUP BY prod.id_produto
                                                                        ORDER BY qtdade_utilizada_periodo ASC "
                                            );

                                            $conta_prod = $sql_prod_7_dias->rowCount();



                                            $row = $sql_prod->fetchALL(PDO::FETCH_ASSOC);




                                            foreach ($row as $r) {







                                                $qtdade_utilizada_periodo = '';

                                                $rp = $sql_prod_7_dias->fetchALL(PDO::FETCH_ASSOC);

                                                foreach ($rp as $r_7) {

                                                    $id_produto = $r_7['id_produto'];
                                                    $nome_produto = $r_7['nome_produto'];

                                                    $categoria_produto = $r_7['nome_categoria_produto'];
                                                    $qtdade_utilizada = $r['qtdade_utilizada'];
                                                    $nome_unidade_medida = $r_7['nome_unidade_medida'];


                                                    $qtdade_utilizada_periodo = $r_7['qtdade_utilizada_periodo'];
                                                    //Variação Percentual = (Vmaior-Vmenor/Vmenor) × 100


                                                    $dividend = $qtdade_utilizada_periodo - $qtdade_utilizada;
                                                    $divisor = $qtdade_utilizada;

                                                   if (is_numeric($divisor) && $divisor !== 0) {

                                                    $result = $dividend / $divisor;
                                                    $porcentagem_crescimento_util_periodo = (($qtdade_utilizada_periodo - $qtdade_utilizada / $qtdade_utilizada) * 100);

                                                    
                                                    


                                                    if ($porcentagem_crescimento_util_periodo > 0) {

                                                        $classe_seta_util = 'bi bi-arrow-up-short text-success';
                                                        $badge_seta_util = 'svg-icon-success';
                                                    } else {

                                                        $classe_seta_util = 'bi bi-arrow-down-short text-danger';
                                                        $badge_seta_util = 'svg-icon-danger';
                                                    }

                                                    $saldo_Prod = '';



                                                    echo '
                                                <!--begin::Item-->
                                         <div class="d-flex flex-stack">
                                            <!--begin::Flag-->
                                            
                                           <!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/kt-products/docs/metronic/html/releases/2022-08-29-071832/core/html/src/media/icons/duotune/medicine/med005.svg-->
<span class="svg-icon svg-icon-muted svg-icon-2hx me-4 w-30px style="border-radius: 4px" ><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path opacity="0.3" d="M17.9061 13H11.2061C11.2061 12.4 10.8061 12 10.2061 12C9.60605 12 9.20605 12.4 9.20605 13H6.50606L9.20605 8.40002V4C8.60605 4 8.20605 3.6 8.20605 3C8.20605 2.4 8.60605 2 9.20605 2H15.2061C15.8061 2 16.2061 2.4 16.2061 3C16.2061 3.6 15.8061 4 15.2061 4V8.40002L17.9061 13ZM13.2061 9C12.6061 9 12.2061 9.4 12.2061 10C12.2061 10.6 12.6061 11 13.2061 11C13.8061 11 14.2061 10.6 14.2061 10C14.2061 9.4 13.8061 9 13.2061 9Z" fill="currentColor"/>
<path d="M18.9061 22H5.40605C3.60605 22 2.40606 20 3.30606 18.4L6.40605 13H9.10605C9.10605 13.6 9.50605 14 10.106 14C10.706 14 11.106 13.6 11.106 13H17.8061L20.9061 18.4C21.9061 20 20.8061 22 18.9061 22ZM14.2061 15C13.1061 15 12.2061 15.9 12.2061 17C12.2061 18.1 13.1061 19 14.2061 19C15.3061 19 16.2061 18.1 16.2061 17C16.2061 15.9 15.3061 15 14.2061 15Z" fill="currentColor"/>
</svg>
</span>
<!--end::Svg Icon-->
                                            <!--end::Flag-->
                                            <!--begin::Section-->
                                            <div class="d-flex align-items-center flex-stack flex-wrap flex-row-fluid d-grid gap-2">
                                                <!--begin::Content-->
                                                <div class="me-5">
                                                    <!--begin::Title-->
                                                    <a href="javascript:;" class="text-gray-800 fw-bold text-hover-primary fs-6"> ' . $nome_produto . '</a>
                                                    <!--end::Title-->
                                                    <!--begin::Desc-->
                                                    <span class="text-gray-400 fw-semibold fs-7 d-block text-start ps-0">' . $categoria_produto . ' </span>
                                                    <!--end::Desc-->
                                                </div>
                                                <!--end::Content-->
                                                <!--begin::Wrapper-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Number-->
                                                    <span class="text-gray-800 fw-bold fs-7 me-3">' . $qtdade_utilizada_periodo . ' ' . $nome_unidade_medida . '</span>
                                                    <!--end::Number-->
                                                    <!--begin::Info-->
                                                    <div class="m-0">
                                                        <!--begin::Label-->
                                                        <span class="badge badge-light-success fs-base">
                                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                                       <span class="svg-icon svg-icon-5 ' . $badge_seta_util . ' ms-n1">
                                                        <i class="' . $classe_seta_util . '" ></i>

                                                        ' . round($porcentagem_crescimento_util_periodo, 2) . ' %
                                                    </span>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::Wrapper-->
                                            </div>
                                            <!--end::Section-->
                                        </div>
                                        <!--end::Item-->

                                        <!--begin::Separator-->
                                        <div class="separator separator-dashed my-3"></div>
                                        <!--end::Separator-->

                                                ';

                                                    } else {
                                                    $result = "Divisão por zero";
                                                    }

                                                    

                                                }
                                            }
                                        } else {


                                            echo  '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h4 class="mb-1 text-primary">Produtos Químicos</h4>
														<span>Não foram localizados Produtos Químicos, utilizados neste Projeto.</span>
													</div>
												</div>';
                                        }


                                        ?>





                                    </div>
                                    <!--end::Body-->
                                </div>
                                <!--end::List widget 9-->
                            </div>