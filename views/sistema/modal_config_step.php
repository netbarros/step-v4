<?php
 // buffer de saída de dados do php]
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$_SESSION['pagina_atual'] = "modal_config_step";



?>
        
        
        <div class="modal-content shadow-none">
            <div class="modal-header">
                <h5 class="modal-title">Configurações do STEP</h5>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-2x"><span class="path1"></span><span class="path2"></span></i>
                </div>
                <!--end::Close-->
            </div>

            <div class="modal-body">


                        <form id="form_config_step" class="form" action="#" autocomplete="off">
                            <div class="row">
                                <!--begin::Input wrapper-->
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label for="Cerca_virtual" class="fs-6 fw-semibold mb-2 required">
                                        Defina a Cerca Virtual <small>em Metros</small> <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="right" title="Defina a Cerca Virtual em metros. Isso afetará o Controle de GPS da Leitura do PLCode."></i>
                                    </label>
                                    <!--begin::Input-->
                                    <input type="number" id="Cerca_virtual" class="form-control form-control-solid" placeholder="Metros" name="Cerca_virtual" required>
                                    <!--end::Input-->

                                    <div class="form-text">
                           Para desabilitar o Controle Geral de GPS no Sistema, basta definir o valor como 0 (zero).
                        </div>
                                </div>
                                <!--end::Input wrapper-->

                                <!--begin::Input wrapper-->
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label for="SLA_Step" class="fs-6 fw-semibold mb-2 required">
                                        Defina o SLA <small>em horas</small> <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="right" title="Defina o SLA em Horas. Isso Afetará o prazo que o Responsável têm para poder Responder o Ticket de Suporte de forma Direta, logo após ser Notificado. Esse controle é para apontamento de atendimentos dentro de um prazo específico."></i>
                                    </label>
                                    <!--begin::Input-->
                                    <input type="number" id="SLA_Step" class="form-control form-control-solid" placeholder="Período" name="SLA_Step" required>
                                    <!--end::Input-->

                                    <div class="form-text">
                                        Para desabilitar o Controle por SLA de Atendimento ao Ticket, basta definir o valor como 0 (zero).
                                        </div>
                                </div>
                                <!--end::Input wrapper-->
                              
                            </div>

                            <!--begin::Actions-->
                            <div class="mt-3">
                                <button id="valida_alteracao_step" type="submit" class="btn btn-primary">
                                    <span class="indicator-label">
                                       Validar Alterações
                                    </span>
                                    <span class="indicator-progress">
                                       Por Favor, aguarde... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                                    <!--end::Actions-->

                                    <input type="hidden" name="acao" value="config_step">
                    </form>

        </div> <!--end::Body-->


        <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Sair</button>
               
            </div>


    </div>



    <script>
       $(function () {
    $('[data-bs-toggle="tooltip"]').tooltip()
  })


  // Quando o formulário for submetido...
$("#form_config_step").on('submit', function(e) {

// Previna a ação padrão de enviar o formulário
e.preventDefault();

// Defina os dados que você deseja enviar, neste caso, os dados do formulário
var dados = $(this).serialize();

// A URL para onde você deseja enviar a solicitação POST
var url = "/crud/sistema/action_step.php";

// Inicie a solicitação AJAX
$.ajax({
    type: "POST",
    url: url,
    data: dados,
    dataType: "json",
    cache: false,
    beforeSend: function() {
        // O que fazer antes de enviar (por exemplo, desativar o botão de envio ou iniciar uma animação de carregamento)
        $('#valida_alteracao_step').prop("disabled", true);
        $('#valida_alteracao_step .indicator-progress').show();
    },
    success: function(data) {
        // O que fazer quando a solicitação for bem-sucedida
        console.log(data);
        if (data.codigo == 1) {

          

            createMetronicToast('Configuração STEP', data.retorno, 5000, 'success', 'bi bi-check2-square');

            $('#modal_config_step').modal('hide');
        }
        if (data.codigo == 0) {
            swal.fire("Ops!", data.retorno, "warning");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        // O que fazer se a solicitação falhar
        swal.fire("Erro!", "Não foi possível prosseguir! Erro: " + textStatus, "error");
    },
    complete: function() {
        // O que fazer sempre, independentemente de a solicitação falhar ou ser bem-sucedida (por exemplo, reativar o botão de envio ou parar a animação de carregamento)
        $('#valida_alteracao_step').prop("disabled", false);
        $('#valida_alteracao_step .indicator-progress').hide();
    }
});
});

    </script>


    