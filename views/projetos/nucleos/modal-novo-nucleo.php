<?php

if (!isset($_SESSION)) session_start();

$_SESSION['pagina_atual'] = 'Novo Núcleo de Projeto';


$id = (isset($_POST['id'])) ? $_POST['id'] : '';

?>

<div class="modal fade" id="kt_modal_novo_nucleo" tabindex="-1" aria-hidden="true">
<!--begin::Modal dialog-->
<div class="modal-dialog modal-dialog-centered mw-900px">
    <!--begin::Modal content-->
    <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header">
            <!--begin::Modal title-->
            <h2>Novo <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'PLC';}else{ echo 'Núcleo';}?> do Projeto</h2>
            <!--end::Modal title-->
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
        <!--end::Modal header-->
        <!--begin::Modal body-->
        <div class="modal-body py-lg-10 px-lg-10">
            <!--begin::Stepper-->
            <div class="stepper stepper-pills stepper-column d-flex flex-column flex-xl-row flex-row-fluid" id="kt_modal_novo_nucleo_stepper">
                <!--begin::Aside-->
                <div class="d-flex justify-content-center justify-content-xl-start flex-row-auto w-100 w-xl-300px">
                    <!--begin::Nav-->
                    <div class="stepper-nav ps-lg-10">
                        <!--begin::Step 1-->
                        <div class="stepper-item current" data-kt-stepper-element="nav">
                            <!--begin::Wrapper-->
                            <div class="stepper-wrapper">
                                <!--begin::Icon-->
                                <div class="stepper-icon w-40px h-40px">
                                    <i class="stepper-check fas fa-check"></i>
                                    <span class="stepper-number">1</span>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Label-->
                                <div class="stepper-label">
                                    <h3 class="stepper-title">Identificação</h3>
                                    <div class="stepper-desc"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Nome do PLC';}else{ echo 'Nome do Núcleo';}?></div>
                                </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Wrapper-->
                            <!--begin::Line-->
                            <div class="stepper-line h-40px"></div>
                            <!--end::Line-->
                        </div>
                        <!--end::Step 1-->
                                          
                        <!--begin::Step 4-->
                        <div class="stepper-item" data-kt-stepper-element="nav">
                            <!--begin::Wrapper-->
                            <div class="stepper-wrapper">
                                <!--begin::Icon-->
                                <div class="stepper-icon w-40px h-40px">
                                    <i class="stepper-check fas fa-check"></i>
                                    <span class="stepper-number">3</span>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Label-->
                                <div class="stepper-label">
                                    <h3 class="stepper-title">GPS</h3>
                                    <div class="stepper-desc">Geo-Localização</div>
                                </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Wrapper-->
                            <!--begin::Line-->
                            <div class="stepper-line h-40px"></div>
                            <!--end::Line-->
                        </div>
                        <!--end::Step 4-->
                        <!--begin::Step 3-->
                        <div class="stepper-item" data-kt-stepper-element="nav">
                            <!--begin::Wrapper-->
                            <div class="stepper-wrapper">
                                <!--begin::Icon-->
                                <div class="stepper-icon w-40px h-40px">
                                    <i class="stepper-check fas fa-check"></i>
                                    <span class="stepper-number">4</span>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Label-->
                                <div class="stepper-label">
                                    <h3 class="stepper-title">Status</h3>
                                    <div class="stepper-desc"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'PLC';}else{ echo 'Núcleo';}?></div>
                                </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Wrapper-->
                            <!--begin::Line-->
                            <div class="stepper-line h-40px"></div>
                            <!--end::Line-->
                        </div>
                        <!--end::Step 3-->
                        <!--begin::Step 5-->
                        <div class="stepper-item" data-kt-stepper-element="nav">
                            <!--begin::Wrapper-->
                            <div class="stepper-wrapper">
                                <!--begin::Icon-->
                                <div class="stepper-icon w-40px h-40px">
                                    <i class="stepper-check fas fa-check"></i>
                                    <span class="stepper-number">5</span>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Label-->
                                <div class="stepper-label">
                                    <h3 class="stepper-title">Completo</h3>
                                    <div class="stepper-desc">Salvar Cadastro</div>
                                </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Step 5-->
                    </div>
                    <!--end::Nav-->
                </div>
                <!--begin::Aside-->
                <!--begin::Content-->
                <div class="flex-row-fluid py-lg-5 px-lg-15">
                    <!--begin::Form-->
                    <form class="form" novalidate="novalidate" id="kt_modal_novo_nucleo_form">
                        <!--begin::Step 1-->
                        <div class="current" data-kt-stepper-element="content">
                            <div class="w-100">
                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                        <span class="required"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Nome do PLC';}else{ echo 'Nome do Núcleo';}?></span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Especifique um nome único para este Núcleo"></i>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" class="form-control form-control-lg form-control-solid" name="nome_nucleo" id='nome_nucleo' placeholder="" value="" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row">
                                   
                                    <!--begin:Options-->
                                   
                                        <!--begin::Col-->
                        <div class="col-md-12 fv-row">
                           
                       <!--begin::Label-->
                       <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                        <span class="required"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Projeto do PLC';}else{ echo 'Projeto do Núcleo';}?></span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Projeto que estará vinculado este Núcleo."></i>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" class="form-control form-control-lg form-control-solid" name="projeto_nucleo" id='projeto_nucleo' placeholder="" value="" disabled/>
                                    <!--end::Input-->
                        </div>
                        <!--end::Col-->
                                    
                                    <!--end:Options-->
                                </div>
                                <!--end::Input group-->
                            </div>
                        </div>
                        <!--end::Step 1-->
                      
                     
                        <!--begin::Step 3-->
                        <div data-kt-stepper-element="content">
                            <div class="w-100">
                                <div class="" id="mapa_nucleo"></div>
                            <label class="d-flex align-items-center fs-5 fw-semibold mb-4">
                                        <span class="required">Geo-Localização</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Inclua as Coordenadas GPS do Núcleo de Operação."></i>
                                    </label>
                                <!--begin::Input group-->
                                <div class="d-flex flex-column mb-7 fv-row">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Latitude</span>
                                       
                                    </label>
                                    <!--end::Label-->
                                    <input type="text" class="form-control form-control-lg form-control-solid" name="latitude_nucleo" id='latitude_nucleo' placeholder="00.0000" value="">
                                </div>
                                <!--end::Input group-->


                                    <!--begin::Input group-->
                                    <div class="d-flex flex-column mb-7 fv-row">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Longitude</span>
                                       
                                    </label>
                                    <!--end::Label-->
                                    <input type="text" class="form-control form-control-lg form-control-solid" name="longitude_nucleo" id='longitude_nucleo' placeholder="00.0000" value="">
                                </div>
                                <!--end::Input group-->
                           
                            </div>
                        </div>
                        <!--end::Step 3-->

                           <!--begin::Step 4-->
                           <div data-kt-stepper-element="content">
                            <div class="w-100">
                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <!--begin::Label-->
                                    <label class="required fs-5 fw-semibold mb-2"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Status do PLC';}else{ echo 'Status do Núcleo';}?></label>
                                    <!--end::Label-->
                                    
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row">
                                    <!--begin::Label-->
                                                            <!--begin::Input group-->
                          <div class="d-flex flex-stack mb-8">
                            <!--begin::Label-->
                            <div class="me-5">
                                <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'PLC';}else{ echo 'Núcleo';}?></label>
                                <div class="fs-7 fw-semibold text-muted">Determine sua Atividade</div>
                            </div>
                            <!--end::Label-->
                            <!--begin::Switch-->
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" checked="checked" name="status_nucleo"/>
                                <span class="form-check-label fw-semibold text-muted">Ativo</span>
                            </label>
                            <!--end::Switch-->
                        </div>
                        <!--end::Input group-->
                                    <!--end::Option-->
                                </div>
                                <!--end::Input group-->
                            </div>
                        </div>
                        <!--end::Step 4-->
                        <!--begin::Step 5-->
                        <div data-kt-stepper-element="content">
                            <div class="w-100 ">
                            <div class="pb-12 text-center">
                                <!--begin::Heading-->
                                <h1 class="fw-bold text-dark mb-3">Completo!</h1>
                                <!--end::Heading-->
                                <!--begin::Description-->
                                <div class="fw-semibold text-muted fs-4">Seu <b><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Novo PLC';}else{ echo 'Novo  Núcleo';}?></b> foi preparado para ser Salvo! Verifique o que você pode fazer a partir de agora e Salve o novo <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'PLC';}else{ echo 'Núcleo';}?> em seu Projeto. </div>
                                
                            </div>

                           
                                <h4 class="text-gray-700 fw-bold cursor-pointer mb-0 py-3">Lembre-se que agora você pode:</h4>
                                <!--end::Description-->
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
														<!--begin::Item-->
														<div class="mb-4">
															<!--begin::Item-->
															<div class="d-flex align-items-center ps-10 mb-n1">
																<!--begin::Bullet-->
																<span class="bullet me-3"></span>
																<!--end::Bullet-->
																<!--begin::Label-->
																<div class="text-gray-600 fw-semibold fs-6"><?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'Criar PLC de Coleta de Dados';}else{ echo 'Criar Núcleos de Coleta de Dados';}?></div>
																<!--end::Label-->
															</div>
															<!--end::Item-->
														</div>
														<!--end::Item-->
														<!--begin::Item-->
														<div class="mb-4">
															<!--begin::Item-->
															<div class="d-flex align-items-center ps-10 mb-n1">
																<!--begin::Bullet-->
																<span class="bullet me-3"></span>
																<!--end::Bullet-->
																<!--begin::Label-->
																<div class="text-gray-600 fw-semibold fs-6">Criar Parâmetros de Controle para Indicadores;</div>
																<!--end::Label-->
															</div>
															<!--end::Item-->
														</div>
														<!--end::Item-->
														<!--begin::Item-->
														<div class="mb-4">
															<!--begin::Item-->
															<div class="d-flex align-items-center ps-10 mb-n1">
																<!--begin::Bullet-->
																<span class="bullet me-3"></span>
																<!--end::Bullet-->
																<!--begin::Label-->
																<div class="text-gray-600 fw-semibold fs-6">Criar <?php if($nivel_acesso_user_sessao=='engenheiro'){ echo 'PLC ';}else{ echo 'Núcleos';}?> para Monitoramento;</div>
																<!--end::Label-->
															</div>
															<!--end::Item-->
														</div>
														<!--end::Item-->
														
														<!--begin::Item-->
														<div class="mb-4">
															<!--begin::Item-->
															<div class="d-flex align-items-center ps-10 mb-n1">
																<!--begin::Bullet-->
																<span class="bullet me-3"></span>
																<!--end::Bullet-->
																<!--begin::Label-->
																<div class="text-gray-600 fw-semibold fs-6">Criar seu Próprio Cockpit para Monitoramento dos Dados;</div>
																<!--end::Label-->
															</div>
															<!--end::Item-->
														</div>
														<!--end::Item-->


                                                        <!--begin::Item-->
														<div class="mb-4">
															<!--begin::Item-->
															<div class="d-flex align-items-center ps-10 mb-n1">
																<!--begin::Bullet-->
																<span class="bullet me-3"></span>
																<!--end::Bullet-->
																<!--begin::Label-->
																<div class="text-gray-600 fw-semibold fs-6">Criar suas próprias Coleções de Notificações por Projeto e Tipo de Suporte!</div>
																<!--end::Label-->
															</div>
															<!--end::Item-->
														</div>
														<!--end::Item-->



                                                        
														
													</div>

                            </div>
                        </div>
                        <!--end::Step 5-->
                        <!--begin::Actions-->
                        <div class="d-flex flex-stack pt-10">
                            <!--begin::Wrapper-->
                            <div class="me-2">
                                <button type="button" class="btn btn-lg btn-light-primary me-3" data-kt-stepper-action="previous">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr063.svg-->
                                    <span class="svg-icon svg-icon-3 me-1">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="6" y="11" width="13" height="2" rx="1" fill="currentColor" />
                                            <path d="M8.56569 11.4343L12.75 7.25C13.1642 6.83579 13.1642 6.16421 12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75L5.70711 11.2929C5.31658 11.6834 5.31658 12.3166 5.70711 12.7071L11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25C13.1642 17.8358 13.1642 17.1642 12.75 16.75L8.56569 12.5657C8.25327 12.2533 8.25327 11.7467 8.56569 11.4343Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->Voltar
                                </button>
                            </div>
                            <!--end::Wrapper-->
                            <!--begin::Wrapper-->
                            <div>
                                <button type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="submit">
                                    <span class="indicator-label">Salvar
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-3 ms-2 me-0">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </span>
                                    <span class="indicator-progress">Por favor aguarde...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                                <button type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="next">Continue
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                    <span class="svg-icon svg-icon-3 ms-1 me-0">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                            <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </button>
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Actions-->

                        <input type="hidden" name="acao" value="cadastrar">
                        <input type="hidden" name="id_projeto" id="id_projeto_nucleo" value="">
                        
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Stepper-->
        </div>
        <!--end::Modal body-->
    </div>
    <!--end::Modal content-->
</div>
<!--end::Modal dialog-->
</div>


<?php

function mask($val, $mask)
{
    $maskared = '';
    $k = 0;
    for ($i = 0; $i <= strlen($mask) - 1; ++$i) {
        if ($mask[$i] == '#') {
            if (isset($val[$k])) {
                $maskared .= $val[$k++];
            }
        } else {
            if (isset($mask[$i])) {
                $maskared .= $mask[$i];
            }
        }
    }

    return $maskared;
}

$cnpj = '11222333000199';
$cpf = '00100200300';
$cep = '08665110';
$data = '10102010';
$hora = '021050';
/* 
echo mask($cnpj, '##.###.###/####-##').'<br>';
echo mask($cpf, '###.###.###-##').'<br>';
echo mask($cep, '#####-###').'<br>';
echo mask($data, '##/##/####').'<br>';
echo mask($data, '##/##/####').'<br>';
echo mask($data, '[##][##][####]').'<br>';
echo mask($data, '(##)(##)(####)').'<br>';
echo mask($hora, 'Agora são ## horas ## minutos e ## segundos').'<br>';
echo mask($hora, '##:##:##'); */

?>