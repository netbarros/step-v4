<?php
header("Content-Type: multipart/form-data; charset=utf-8");
echo "<!DOCTYPE html>

<html lang='pt-BR'>

<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    <base href='https://step.eco.br/app/'>
    <title>STEP</title>
    <meta charset='utf-8' />
    <meta property='og:locale' content='pt_BR' />
    <meta property='og:type' content='article' />
    <meta property='og:title' content='STEP Comunica' />
    <meta property='og:url' content='https://step.eco.br' />
    <meta property='og:site_name' content='STEP | 2022' />
    <link rel='canonical' href='https://step.eco.br' />
    <link rel='shortcut icon' href='assets-v2/media/logos/favicon.ico' />
    <!--begin::Fonts-->
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700' />
    <!--end::Fonts-->
    <!--begin::Page Vendor Stylesheets(used by this page)-->
    <link href='assets-v2/plugins/custom/datatables/datatables.bundle.css' rel='stylesheet' type='text/css' />
    <!--end::Page Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href='assets-v2/plugins/global/plugins.bundle.css' rel='stylesheet' type='text/css' />
    <link href='assets-v2/css/style.bundle.css' rel='stylesheet' type='text/css' />
    <!--end::Global Stylesheets Bundle-->
    <style>
        html,
        body {
            padding: 0;
            margin: 0;
        }
    </style>
</head>
<!--end::Head-->
<!--begin::Body-->

<body id='kt_body'
    class='print-content-only header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed'
    style='--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px'>
    <!--begin::Main-->
    <div class='post d-flex flex-column-fluid' id='kt_post'>
        <!--begin::Container-->
        <div id='kt_content_container' class='container-xxl'>
            <!--begin::Invoice 2 main-->
            <div class='card'>
                <!--begin::Body-->
                <div class='card-body p-lg-20'>
                    <!--begin::Layout-->
                    <div class='d-flex flex-column flex-xl-row'>
                        <!--begin::Content-->
                        <div class='flex-lg-row-fluid me-xl-18 mb-10 mb-xl-0'>
                            <!--begin::Invoice 2 content-->
                            <div class='mt-n1'>
                                <!--begin::Top-->
                                <div class='d-flex flex-stack pb-10'>
                                    <!--begin::Logo-->
                                    <a href='#'>
                                        <img alt='Logo' src='https://step.eco.br/v2/assets-v2/media/logos/logo-4.png' />
                                    </a>
                                    <!--end::Logo-->
                                    <!--begin::Action-->
                                    <a href='#' class='btn btn-sm btn-success'>Acessar</a>
                                    <!--end::Action-->
                                </div>
                                <!--end::Top-->
                                <!--begin::Wrapper-->
                                <div class='m-0'>
                                    <!--begin::Label-->
                                    <div class='fw-bolder fs-3 text-gray-800 mb-8'>Suporte $ultimo_id_suporte</div>
                                    <!--end::Label-->
                                    <!--begin::Row-->
                                    <div class='row g-5 mb-11'>
                                        <!--end::Col-->
                                        <div class='col-sm-6'>
                                            <!--end::Label-->
                                            <div class='fw-bold fs-7 text-gray-600 mb-1'>Data Ocorrência</div>
                                            <!--end::Label-->
                                            <!--end::Col-->
                                            <div class='fw-bolder fs-6 text-gray-800'>$dia_mes_ano $hora_min</div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Col-->
                                        <!--end::Col-->
                                        <div class='col-sm-6'>
                                            <!--end::Label-->
                                            <div class='fw-bold fs-7 text-gray-600 mb-1'>Tempo em Aberto</div>
                                            <!--end::Label-->
                                            <!--end::Info-->
                                            <div
                                                class='fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap'>
                                                <span class='pe-2'>$Data_Intervalo_Periodo</span>
                                                <span class='fs-7 text-danger d-flex align-items-center'>
                                                    <span
                                                        class='bullet bullet-dot bg-danger me-2'></span>$dias_em_aberto_suporte</span>
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                    <!--begin::Row-->
                                    <div class='row g-5 mb-12'>
                                        <!--end::Col-->
                                        <div class='col-sm-6'>
                                            <!--end::Label-->
                                            <div class='fw-bold fs-7 text-gray-600 mb-1'>Enviado por:</div>
                                            <!--end::Label-->
                                            <!--end::Text-->
                                            <div class='fw-bolder fs-6 text-gray-800'>STEP GrupoEP.</div>
                                            <!--end::Text-->
                                            <!--end::Description-->
                                            <div class='fw-bold fs-7 md-2 text-gray-600'>Operador:
                                            </div>
                                            <div class='fw-bolder fs-7 text-gray-800 mb-1'>$nome_Operador</div>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Col-->
                                        <!--end::Col-->
                                        <div class='col-sm-6'>
                                            <!--end::Label-->
                                            <div class='fw-bold fs-7 text-gray-600 mb-1'>Ocorrência:</div>
                                            <!--end::Label-->
                                            <!--end::Text-->
                                            <div class='fw-bolder fs-7 text-gray-800'>$mensagem_alerta</div>
                                              <div class='fw-bolder fs-8 text-gray-800'>$retorno_alerta</div>
                                            <!--end::Text-->

                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                    <!--begin::Content-->
                                    <div class='flex-grow-1'>
                                        <!--begin::Table-->
                                        <div class='table-responsive border-bottom mb-9'>
                                            <table class='table mb-3'>
                                                <thead>
                                                    <tr class='border-bottom fs-6 fw-bolder text-muted'>
                                                        <th class='min-w-175px pb-2'>Indicador</th>
                                                        <th class='min-w-80px text-end pb-2'>Parâmetro</th>
                                                        <th class='min-w-70px text-end pb-2'>Leitura</th>
                                                        <th class='min-w-100px text-end pb-2'>Variação %</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <tr class='fw-bolder text-gray-700 fs-5 text-end'>
                                                        <td class='d-flex align-items-center pt-6'>
                                                            <i
                                                                class='fa fa-genderless text-danger fs-2 me-2'></i>$nome_parametro
                                                        </td>
                                                        <td class='pt-6'>$concen_min <> $concen_max</td>
                                                        <td class='pt-6'>$leitura  $unidade_medida_lida</td>
                                                        <td class='pt-6 text-dark fw-boldest'>$variacao_leitura</td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                        <!--end::Table-->
                                        <!--begin::Container-->
                                        <div class='d-flex justify-content-end'>
                                            <!--begin::Section-->
                                            <div class='mw-600px'>
                                                <!--begin::Item-->
                                                <div class='d-flex flex-stack mb-3'>
                                                    <!--begin::Accountname-->
                                                    <div class='fw-bold pe-10 text-gray-600 fs-7'>PLCode</div>
                                                    <!--end::Accountname-->
                                                    <!--begin::Label-->
                                                    <div class='text-end fw-bolder fs-6 text-gray-800'>$nome_ponto
                                                    </div>
                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Item-->
                                                <!--begin::Item-->
                                                <div class='d-flex flex-stack mb-3'>
                                                    <!--begin::Accountname-->
                                                    <div class='fw-bold pe-10 text-gray-600 fs-7'>Objetivo Ponto:</div>
                                                    <!--end::Accountname-->
                                                    <!--begin::Label-->
                                                    <div class='text-end fw-bolder fs-6  text-gray-800'>$objetivo_ponto
                                                    </div>
                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Item-->

                                                <!--begin::Item-->
                                                <div class='d-flex flex-stack'>
                                                    <!--begin::Code-->
                                                    <div class='fw-bold pe-10 text-gray-600 fs-7'>Coordenadas PLCode:</div>
                                                    <!--end::Code-->
                                                    <!--begin::Label-->
                                                    <div class='text-end fw-bolder fs-6  text-gray-800'>$Latitude_Ponto
                                                        $Longitude_Ponto</div>
                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Item-->

                                                   <!--begin::Item-->
                                                <div class='d-flex flex-stack'>
                                                    <!--begin::Code-->
                                                    <div class='fw-bold pe-10 text-gray-600 fs-7'>Coordenadas Operador:</div>
                                                    <!--end::Code-->
                                                    <!--begin::Label-->
                                                    <div class='text-end fw-bolder fs-6  text-gray-800'>$Latitude_Operador
                                                        $Longitude_Operador</div>
                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Item-->
                                            </div>
                                            <!--end::Section-->
                                        </div>
                                        <!--end::Container-->
                                    </div>
                                    <!--end::Content-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Invoice 2 content-->
                        </div>
                        <!--end::Content-->
                        <!--begin::Sidebar-->
                        <div class='m-0'>
                            <!--begin::Invoice 2 sidebar-->
                            <div
                                class='d-print-none border border-dashed border-gray-300 card-rounded h-lg-100 min-w-md-350px p-9 bg-lighten'>
                                <!--begin::Labels-->
                                <div class='mb-8'>
                                    <span class='badge badge-light-success me-2'>$status_leitura</span>
                                    <span class='badge badge-light-warning'>$nome_tipo_suporte</span>
                                </div>
                                <!--end::Labels-->
                                <!--begin::Title-->
                                <h6 class='mb-8 fw-boldest text-gray-600 text-hover-primary'>Cliente</h6>
                                <!--end::Title-->
                                <!--begin::Item-->
                                <div class='mb-6'>
                                    <div class='fw-bold text-gray-600 fs-7'>Contato do Cliente:</div>
                                    <div class='fw-bolder text-gray-800 fs-6'> $Nome_Contato_Cliente $Sobrenome_Contato_Cliente : $email_Contato_Cliente</div>
                                </div>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <div class='mb-6'>
                                    <div class='fw-bold text-gray-600 fs-7'>Celular do Contato:</div>
                                    <div class='fw-bolder text-gray-800 fs-6'>$celular_Contato_Cliente

                                    </div>
                                </div>
                                <!--end::Item-->
                                <!--begin::Title-->
                                <h6 class='mb-8 fw-boldest text-gray-600 text-hover-primary'>Responsáveis</h6>
                                <!--end::Title-->
                                <!--begin::Item-->
                                <div class='mb-6'>
                                    <div class='fw-bold text-gray-600 fs-7'>RO:</div>
                                    <div class='fw-bolder fs-6 text-gray-800 d-flex align-items-center'>$nome_RO

                                    </div>
                                    <div class='fw-bolder fs-6 text-gray-800 d-flex align-items-center'>
                                        $email_RO

                                    </div>
                                </div>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <div class='mb-6'>
                                    <div class='fw-bold text-gray-600 fs-7'>Supervisor:</div>
                                    <div class='fw-bolder fs-6 text-gray-800 d-flex align-items-center'>$nome_Supervisor

                                    </div>
                                    <div class='fw-bolder fs-6 text-gray-800 d-flex align-items-center'>
                                        $email_Supervisor

                                    </div>
                                </div>
                                <!--end::Item-->
                                <!--begin::Title-->
                                <h6 class='mb-8 fw-boldest text-gray-600 text-hover-primary'>OVERVIEW</h6>
                                <!--end::Title-->
                                <!--begin::Item-->
                                <div class='mb-6'>
                                    <div class='fw-bold text-gray-600 fs-7'>$Endereco_Origem</div>
                                    <div class='fw-bolder fs-6 text-gray-800'>$GPS
                                        <a href='#' class='link-primary ps-1'>Google Maps</a>
                                    </div>
                                </div>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <div class='mb-6'>
                                    <div class='fw-bold text-gray-600 fs-7'>Quando:</div>
                                    <div class='fw-bolder text-gray-800 fs-6'>$dia_mes_ano às $hora_min</div>
                                </div>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <div class='m-0'>
                                    <div class='fw-bold text-gray-600 fs-8'>CHAVE ÚNICA</div>
                                    <div class='fw-bolder fs-9 text-gray-800 d-flex align-items-center'>
                                        $Chave_Unica_Rmm

                                    </div>
                                </div>
                                <!--end::Item-->
                            </div>
                            <!--end::Invoice 2 sidebar-->
                        </div>
                        <!--end::Sidebar-->
                    </div>
                    <!--end::Layout-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Invoice 2 main-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
</body>

</html>";
