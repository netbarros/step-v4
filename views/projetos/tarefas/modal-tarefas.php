<?php
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();

$_SESSION['pagina_atual'] = 'Nova Tarefa';

$projeto_atual = isset($_POST['projeto_atual']) ? $_POST['projeto_atual'] : (isset($_COOKIE['projeto_atual']) ? $_COOKIE['projeto_atual'] : '');



?>


<!--begin::Modal - New Target-->

        <div class="modal-content rounded">
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
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <!--begin:Form-->
                <form id="form_modal_nova_tarefa" class="form" action="#">
                    <!--begin::Heading-->
                    <div class="mb-13 text-center">
                        <!--begin::Title-->
                        <h1 class="mb-3">Inicie uma nova Tarefa <?php if(isset($projeto_atual)){ echo "| <small>Projeto ID: $projeto_atual </small>";};?></h1>
                        <!--end::Title-->
                        <!--begin::Description-->
                        <div class="text-muted fw-semibold fs-5">Caso precise de mais informações, visite
                            <a href="javascript:;" class="fw-bold link-primary">Tarefas para Projetos</a>.
                        </div>
                        <!--end::Description-->
                    </div>
                    <!--end::Heading-->
                    <!--begin::Input group-->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <!--begin::Label-->
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Título da Tarefa</span>
                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Especifique um nome para referência futura."></i>
                        </label>
                        <!--end::Label-->
                        <input type="text" class="form-control form-control-solid" placeholder="Informe o Título da Tarefa" name="titulo_tarefa" id="titulo_tarefa"/>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row g-9 mb-8">
                        <!--begin::Col-->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Projeto</label>
                            <select class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_new_target" data-placeholder="Selecione o Projeto" name="projeto_tarefa" id="projeto_tarefa">
                                <option value="">Selecione o Projeto...</option>
                                <?php
                               $sql_nova_tarefa_projeto = $conexao->query("SELECT * FROM obras o WHERE o.status_cadastro='1' ORDER BY o.nome_obra ASC");

                               $conta_projeto_tarefa = $sql_nova_tarefa_projeto->rowCount();
                               
                               if ($conta_projeto_tarefa > 0) {
                                   $row = $sql_nova_tarefa_projeto->fetchALL(PDO::FETCH_ASSOC);
                               
                                   foreach ($row as $ro) {
                                       // Verifica se o id_obra é igual ao projeto_atual
                                       if ($projeto_atual == $ro['id_obra']) {
                                           // Se forem iguais, define a opção como selecionada
                                           echo ' <option value="' . $ro['id_obra'] . '" selected>' . $ro['nome_obra'] . ' </option>';
                                       } else {
                                           // Caso contrário, apenas exibe a opção normalmente
                                           echo ' <option value="' . $ro['id_obra'] . '">' . $ro['nome_obra'] .' </option>';
                                       }
                                   }
                               } else {
                                   echo '<option value="">Não há Projetos Disponíveis</option>';
                               }
                               

                                ?>


                            </select>
                        </div>
                        <!--end::Col-->
                     
                        <!--begin::Col-->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Tipo de Tarefa</label>
                            <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Escolha o Tipo de Tarefa" name="tipo_tarefa" id="tipo_tarefa" data-dropdown-parent="#kt_modal_new_target">
                                <option value="">Qual Tarefa?...</option>
                                <option value="ponto_plcode">Checkin Presencial</option>
                                <option value="ponto_parametro">Checkin Leitura</option>
                                <option value="tarefa_agendada">Tarefa Delegada</option>
                            </select>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->


                    <!-- Ciclos e Horários -->

                    <div class="row g-9 mb-8">
                        <!--begin::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Agendamento</label>
                            <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Selecione o Tipo de Agendamento" name="agendamento_tarefa" id="modo_checkin" data-dropdown-parent="#kt_modal_new_target">
                                <option value="">Selecione o Tipo de Agendamento</option>
                                <option value="1">Horário Livre</option>
                                <option value="2">Horário Agendado</option>
                            </select>
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Recorrência</label>
                            <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Selecione a Recorrência da Tarefa" name="recorrencia_tarefa" id="ciclo_leitura" data-dropdown-parent="#kt_modal_new_target">
                                <option value="">Selecione a Recorrência da Tarefa</option>
                                <option value="0">Tarefa Única</option>
                                <option value="1">Tarefa Diária</option>
                                <option value="2">Tarefa Semanal</option>

                            </select>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!-- Ciclos e Horários -->

                    <!--begin::Tarefas Período-->
                    <div id='div_tarefas_periodo' class="d-none">

                        <div class="row g-9 mb-8">

                            <!--begin::Col-->
                            <div class="col-md-6 fv-row d-none" id="div_horario_realizacao_tarefa">
                                <label class="required fs-6 fw-semibold mb-2">Horário Realização</label>
                                <!--begin::Input-->
                                <div class="position-relative d-flex align-items-center">
                                    <!--begin::Icon-->
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                    <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M20.9 12.9C20.3 12.9 19.9 12.5 19.9 11.9C19.9 11.3 20.3 10.9 20.9 10.9H21.8C21.3 6.2 17.6 2.4 12.9 2V2.9C12.9 3.5 12.5 3.9 11.9 3.9C11.3 3.9 10.9 3.5 10.9 2.9V2C6.19999 2.5 2.4 6.2 2 10.9H2.89999C3.49999 10.9 3.89999 11.3 3.89999 11.9C3.89999 12.5 3.49999 12.9 2.89999 12.9H2C2.5 17.6 6.19999 21.4 10.9 21.8V20.9C10.9 20.3 11.3 19.9 11.9 19.9C12.5 19.9 12.9 20.3 12.9 20.9V21.8C17.6 21.3 21.4 17.6 21.8 12.9H20.9Z" fill="currentColor" />
                                            <path d="M16.9 10.9H13.6C13.4 10.6 13.2 10.4 12.9 10.2V5.90002C12.9 5.30002 12.5 4.90002 11.9 4.90002C11.3 4.90002 10.9 5.30002 10.9 5.90002V10.2C10.6 10.4 10.4 10.6 10.2 10.9H9.89999C9.29999 10.9 8.89999 11.3 8.89999 11.9C8.89999 12.5 9.29999 12.9 9.89999 12.9H10.2C10.4 13.2 10.6 13.4 10.9 13.6V13.9C10.9 14.5 11.3 14.9 11.9 14.9C12.5 14.9 12.9 14.5 12.9 13.9V13.6C13.2 13.4 13.4 13.2 13.6 12.9H16.9C17.5 12.9 17.9 12.5 17.9 11.9C17.9 11.3 17.5 10.9 16.9 10.9Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <!--end::Icon-->
                                    <!--begin::Datepicker-->
                                    <input class="form-control form-control-solid ps-12" placeholder="Informe o Horário" name="horario_tarefa" id="horario_tarefa" />
                                    <!--end::Datepicker-->
                                </div>
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->

                            <!--begin::Col-->
                            <div class="col-md-6 fv-row d-none" id="div_dia_realizacao_tarefa">
                                <label class="required fs-6 fw-semibold mb-2">Dias da Semana</label>
                                <select class="form-select form-select-solid" data-control="select2" data-close-on-select="false" data-allow-clear="true" multiple="multiple" data-hide-search="true" data-placeholder="Selecione..." name="dia_semana[]"  id='dia_semana_nova_tarefa' data-dropdown-parent="#kt_modal_new_target">
                                    <option value="">Selecione os Dias da Semana</option>
                                    <option value="1">
                                        Segunda-Feira
                                    </option>
                                    <option value="2">
                                        Terça-Feira
                                    </option>
                                    <option value="3">
                                        Quarta-Feira
                                    </option>
                                    <option value="4">
                                        Quinta-Feira
                                    </option>
                                    <option value="5">
                                        Sexta-Feira
                                    </option>
                                    <option value="6">
                                        Sábado
                                    </option>
                                    <option value="0">
                                        Domingo
                                    </option>
                                </select>

                            </div>
                            <!--end::Col-->

                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Tarefas Período-->

                    <!-- inicio:: Tarefa Checkin Leitura -->

                    <!--begin::Input group-->
                    <div id='div_tarefas_checkin' class="d-none">
                        <div class="row g-9 mb-8">
                            <!--begin::Col-->
                            <div class="col-md-12 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">PLCode</label>
                                <select class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_new_target"  data-placeholder="Selecione o PLCode" name="plcode_tarefa" id="plcode_tarefa">
                                    <option value="">Selecione o PLCode...</option>

                                </select>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-12 fv-row d-none" id='div_indicador_tarefa'>
                                <label class="required fs-6 fw-semibold mb-2">Indicador</label>
                                <select class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_new_target" data-placeholder="Selecione o Primeiro o PLCode" name="indicador_tarefa" id="indicador_tarefa">
                                    <option value="">Selecione o Indicador para Leitura</option>
                                    <option value="1">Checkin Presencial</option>
                                    <option value="2">Checkin Leitura</option>
                                    <option value="3">Tarefa Agendada</option>
                                </select>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!-- fim:: Tarefa Checkin Leitura -->




                    <!--begin::Tarefas Agendadas-->
                   
                        <div class="row g-9 mb-8" id='div_atribuir_tarefa' class="d-none">
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Atribuir</label>
                                <select class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#kt_modal_new_target"  data-placeholder="Selecione um Usuário" name="usuario_tarefa" id="usuario_tarefa">


                                    <option value="">Selecione o usuário...</option>

                                    <?php
                                    $sql_projeto_tarefa = $conexao->query("SELECT colab.nome, colab.sobrenome, u.id FROM usuarios u
                                INNER JOIN colaboradores colab ON colab.id_colaborador = u.bd_id
                                WHERE u.status='1'  ORDER BY u.nome ASC");

                                    $conta_projeto_tarefa = $sql_projeto_tarefa->rowCount();

                                    if ($conta_projeto_tarefa > 0) {

                                        $rowC = $sql_projeto_tarefa->fetchALL(PDO::FETCH_ASSOC);



                                        foreach ($rowC as $r) {

                                            echo ' <option  value="' . $r['id'] . '">' . strtoupper($r['nome'])  . ' ' . strtoupper($r['sobrenome']) . '</option>';
                                        }
                                    } else {

                                        echo '<option value="">Não há Usuários Disponíveis</option>';
                                    }

                                    ?>

                                </select>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row d-none" id="div_data_tarefa">
                                <label class="required fs-6 fw-semibold mb-2">Data de Realização</label>
                                <!--begin::Input-->
                                <div class="position-relative d-flex align-items-center">
                                    <!--begin::Icon-->
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                    <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
                                            <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
                                            <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <!--end::Icon-->
                                    <!--begin::Datepicker-->
                                    <input class="form-control form-control-solid ps-12" placeholder="Selecione a data" name="due_date" id="data_tarefa" />
                                    <!--end::Datepicker-->
                                </div>
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-8">
                            <label class="fs-6 fw-semibold mb-2">Detalhes da Tarefa</label>
                            <textarea class="form-control form-control-solid" rows="3" name="detalhes_tarefa" id="detalhes_tarefa" placeholder="Descreva os detalhes desta Tarefa"></textarea>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-8 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Tags</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Especifique a Prioridade"></i>
                            </label>
                            <!--end::Label-->
                            <input class="form-control form-control-solid" value="Importante, Urgente" name="tags" id='tags' />
                            
                           
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-stack mb-8">
                            <!--begin::Label-->
                            <div class="me-5">
                                <label class="fs-6 fw-semibold">Monitorar Tarefa</label>
                                <div class="fs-7 fw-semibold text-muted">Saiba quando esta Tarefa for Realizada</div>
                            </div>
                            <!--end::Label-->
                            <!--begin::Switch-->
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" checked="checked" name="monitora_tarefa"/>
                                <span class="form-check-label fw-semibold text-muted">Monitorar</span>
                            </label>
                            <!--end::Switch-->
                        </div>
                        <!--end::Input group-->
                   
                    <!--end::Tarefas Agendadas-->




                    <!--begin::Input group-->
                    <div class="mb-15 fv-row">
                        <!--begin::Wrapper-->
                        <div class="d-flex flex-stack">
                            <!--begin::Label-->
                            <div class="fw-semibold me-5">
                                <label class="fs-6">Notificações</label>
                                <div class="fs-7 text-muted">Escolha como deseja ser notificado quando a Tarefa for concluída:</div>
                            </div>
                            <!--end::Label-->
                            <!--begin::Checkboxes-->
                            <div class="d-flex align-items-center">
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="alerta_email" value="1" checked="checked" />
                                    <span class="form-check-label fw-semibold">Email</span>
                                </label>
                                <!--end::Checkbox-->
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="alerta_sms" value="1" />
                                    <span class="form-check-label fw-semibold">SMS</span>
                                </label>
                                <!--end::Checkbox-->

                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="alerta_whats" value="1" />
                                    <span class="form-check-label fw-semibold">Whatsapp</span>
                                </label>
                                <!--end::Checkbox-->
                            </div>
                            <!--end::Checkboxes-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Input group-->

                          <!--begin::Input group-->
                          <div class="d-flex flex-stack mb-8">
                            <!--begin::Label-->
                            <div class="me-5">
                                <label class="fs-6 fw-semibold">Status Tarefa</label>
                                <div class="fs-7 fw-semibold text-muted">Determine sua Atividade</div>
                            </div>
                            <!--end::Label-->
                            <!--begin::Switch-->
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" checked="checked" name="status_tarefa"/>
                                <span class="form-check-label fw-semibold text-muted">Ativa</span>
                            </label>
                            <!--end::Switch-->
                        </div>
                        <!--end::Input group-->

                    <!--begin::Actions-->
                    <div class="text-center">
                    <a href="javascript:;" data-bs-dismiss="modal" class="btn btn-light me-3">Cancelar</a>
                        <button type="submit" id="bt_nova_tarefa" class="btn btn-primary">
                            <span class="indicator-label">Enviar</span>
                            <span class="indicator-progress">Por favor, aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>

                    <input type="hidden" name="acao" value="cadastrar">
                    <input type="hidden" name="usuario_solicitante" value="<?=$_SESSION['id'];?>">
                    
                    <!--end::Actions-->
                </form>
                <!--end:Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
   
<!--end::Modal - New Target-->


<script src="../../../js/tarefas/nova-tarefa.js">
    
	
    </script>

<script src="../../js/tarefas/controladores.js"></script>