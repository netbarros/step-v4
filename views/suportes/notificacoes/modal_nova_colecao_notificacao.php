<?php
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$_SESSION['pagina_atual'] = 'Nova Coleção de Notificações';

$projeto_atual = isset($_POST['projeto_atual']) ? $_POST['projeto_atual'] : (isset($_COOKIE['projeto_atual']) ? $_COOKIE['projeto_atual'] : '');

$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';

$nome_projeto = trim(isset($_COOKIE['nome_projeto'])) ? $_COOKIE['nome_projeto'] : '';

$projeto_atual = trim(isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';


$sql_personalizado_alertas_projeto = '';

$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';

if ($nivel_acesso_user_sessao == 'supervisor' || $nivel_acesso_user_sessao == 'ro' || $nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado_alertas_projeto = "AND (up.id_usuario  = '$id_usuario_sessao')";

} else if ($nivel_acesso_user_sessao == 'admin') {

    $sql_personalizado_alertas_projeto = "";
}

?> 
 <!--begin::Modal dialog-->
 <div class="modal-dialog modal-dialog-centered mw-850px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h2 class="fw-bold fs-4"></h2>
                <!--end::Modal title-->

                <div class="py-0">
                        
                    <!--begin::Heading-->
                    <h1 class="fs-2 fw-bold text-warning me-2 lh-1 py-2" id="overview" data-kt-scroll-offset="50">
                       
                        
                        Coleções de Notificação de Suporte Personalizadas</h1>
                    <!--end::Heading-->


                        <!--begin::Block-->
                        <div class="py-0 text-gray-700">
                           Aqui você escolhe o <span class="text-gray-900 fw-bold">Projeto</span> que participa e poderá personalizar para cada <span class="text-gray-900 fw-bold">Tipo de Suporte</span>, a forma como gostaria de <span class="text-gray-900 fw-bold">receber suas notificações</span> e o <span class="text-gray-900 fw-bold">período que deseja ser notificado.</span>
                           Criando assim, <span class="text-gray-900 fw-bold">Coleções de Notificação de Suporte </span>Personalizadas.
                        </div>
                        <!--end::Block--> 

                       <!--begin::Accordion-->
                            <div class="accordion accordion-icon-collapse" id="kt_accordion_3">
                                <!--begin::Item-->
                                <div class="mb-5">
                                    <!--begin::Header-->
                                    <div class="accordion-header py-3 d-flex" data-bs-toggle="collapse" data-bs-target="#kt_accordion_3_item_1">
                                        <span class="accordion-icon">
                                            <i class="ki-duotone ki-plus-square fs-3 accordion-icon-off"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                            <i class="ki-duotone ki-minus-square fs-3 accordion-icon-on"><span class="path1"></span><span class="path2"></span></i>
                                        </span>
                                        <h3 class="fs-4 fw-bold mb-0 ms-4 text-warning">Lembre-se 
                                            <a href="javascript:;" class="btn btn-icon btn-light pulse pulse-warning">
                                            <i class="bi bi-question-square text-warning fs-1"></i>
                                            <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                        <span class="pulse-ring "></span>
                                        </a>
                                        </h3>
                                    </div>
                                    <!--end::Header-->

                                    <!--begin::Body-->
                                    <div id="kt_accordion_3_item_1" class="fs-6 collapse ps-10" data-bs-parent="#kt_accordion_3">
                                        <!--begin::Block-->
                                        <div class="py-0 text-gray-700">
                                            <span class="text-warning fw-semibold fs-6">O Que Preciso Saber?</span>

                                            <div class="d-flex flex-column">
                                                <li class="d-flex align-items-center py-2">
                                                <span class="bullet bg-success me-2"></span>  Você precisa estar incluso no Projeto, como Usuário com acesso Global ao Projeto, para poder criar uma Coleção de Notificações de Suporte Personalizada.
                                                </li>
                                                <li class="d-flex align-items-center py-2">
                                                    <span class="bullet bg-success me-2"></span>  Você precisa criar uma Coleção de Notificação de Suporte Personalizada para cada Projeto que participa, para cada Tipo de Suporte e Período que deseja receber notificações.
                                                </li>

                                                <li class="d-flex align-items-center py-2">
                                                    <span class="bullet bg-danger me-2"></span>   Caso Você Seja RO ou Supervisor direto de algum Núcleo (Estação), precisará também estar incluso no Projeto em que Participa, como Usuário com acesso Global.
                                                </li>

                                                <li class="d-flex align-items-center py-2">
                                                    <span class="bullet bg-danger me-2"></span>   Caso não haja sua participação no Projeto, ele não será listado para você.
                                                </li>

                                                <li class="d-flex align-items-center py-2">
                                                    <span class="bullet bg-danger me-2"></span>  Caso não crie Coleções de Notificações, você não receberá nenhuma notificação externa, apenas dentro do Sistema.
                                                </li>
         
                                            </div>
                                        </div>
                                        <!--end::Block--> 
                                    </div>
                                    <!--end::Body-->
                                </div>
                                <!--end::Item-->

                            </div>
                            <!--end::Accordion-->

                    </div>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-square fs-2x"><span class="path1"></span><span class="path2"></span></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->

            <!--begin::Modal body-->
            <div class="modal-body scroll-y mx-5 my-7">
                <!--begin::Form-->
                <form id="form_nova_colecao_notificacao" class="form" action="#">
                    <!--begin::Scroll-->
                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_update_role_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#modal_nova_colecao_notificacao" data-kt-scroll-wrappers="#kt_modal_update_role_scroll" data-kt-scroll-offset="300px">
                        <!--begin::Input group-->
                        <div class="fv-row mb-10">
                            <!--begin::Label-->
                            <label class="fs-5 fw-bold form-label mb-2">
                                <span class="required">Seus Projetos</span>
                            </label>
                            <!--end::Label-->

                            <!--begin::Input-->
                            <select class="form-select form-select-solid select2" data-kt-select2="true" data-placeholder="Selecione uma opção" data-dropdown-parent="#modal_nova_colecao_notificacao" id='IdProjetoColecao' data-allow-clear="true">
                                        <option></option>
                                       <?php

                                        $sql_filtro_projeto_dashboard = $conexao->query("SELECT o.id_obra, o.nome_obra FROM obras o  
                                       INNER JOIN estacoes e ON e.id_obra = o.id_obra
                                       INNER JOIN usuarios_projeto up ON up.id_obra = o.id_obra                                     
                                        WHERE    o.status_cadastro='1' $sql_personalizado_alertas_projeto  GROUP BY o.id_obra
                                       ORDER BY o.nome_obra ASC");

//print_r($sql_filtro_projeto_dashboard);
                                        $conta_projeto = $sql_filtro_projeto_dashboard->rowCount();

                                        if ($conta_projeto > 0) {

                                            $row = $sql_filtro_projeto_dashboard->fetchALL(PDO::FETCH_ASSOC);


                                           // print_r($row );


                                            if ($nome_projeto != 'undefined') {

                                                $nome_projeto_filtro = $nome_projeto;
                                                $id_projeto_filtro = $projeto_atual;


                                                echo ' <option value="' . $id_projeto_filtro . '" selected>' . $nome_projeto_filtro . '</option>';

                                                foreach ($row as $r) {

                                                    echo ' <option value="' . $r['id_obra'] . '">' . $r['nome_obra'] . '</option>';
                                                }
                                            } else {

                                                foreach ($row as $r) {

                                                    echo ' <option value="' . $r['id_obra'] . '">' . $r['nome_obra'] . '</option>';
                                                }
                                            }
                                        }
                                        ?>


                                   </select>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Permissions-->
                        <div class="fv-row">
                            <!--begin::Label-->
                            <label class="fs-5 fw-bold form-label mb-2">Tipos de Suporte</label>
                            <!--end::Label-->

                            <!--begin::Table wrapper-->
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table align-middle table-row-dashed fs-6 gy-5 scroll h-600px px-5" id="Tabela_Notificacoes_Usuario">
                                    <!--begin::Table body-->
                                    <tbody class="text-gray-600 fw-semibold">
                                        <!--begin::Table row-->
                                       
                                        
                                          
                                        <!--begin::Table row-->
                                        <?php /* trazemos os tipos de suporte que o STEP gerencia */

                                        $sql_tipo_suporte = $conexao->prepare("SELECT * FROM tipo_suporte ts 

                                        WHERE ts.status_tipo_suporte = '1' 
                                       
                                        ORDER BY ts.nome_suporte ASC") 
                                        or die($conexao->error);
                                       
                                        $sql_tipo_suporte->execute();
                                        $total_tipo_suporte = $sql_tipo_suporte->rowCount();
                                        $row_tipo_suporte = $sql_tipo_suporte->fetchALL(PDO::FETCH_ASSOC);


                                        if($total_tipo_suporte > 0){

                                        foreach($row_tipo_suporte as $r_tipo_suporte){



                                            echo '<tr>
                                            <!--begin::Label-->
                                            <td class="text-gray-600 titulo_nome_tipo_suporte">'.$r_tipo_suporte['nome_suporte'].'</td>
                                            <!--end::Label-->

                                            <!--begin::Input group-->
                                            <td>
                                                <!--begin::Wrapper-->
                                                <div class="d-flex">
                                                    <!--begin::Checkbox-->
                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                        <input class="form-check-input notification-link" type="checkbox" data-nome_suporte="'.$r_tipo_suporte['nome_suporte'].'" data-id_tipo_suporte="'.$r_tipo_suporte['id_tipo_suporte'].'"  value="1" name="alerta_email" data-tipo_notificacao="alerta_email">
                                                        <span class="form-check-label">
                                                            E-mail
                                                        </span>
                                                    </label>
                                                    <!--end::Checkbox-->

                                                    <!--begin::Checkbox-->
                                                    <label class="form-check form-check-custom form-check-solid me-5 me-lg-20">
                                                        <input class="form-check-input notification-link" type="checkbox" value="1" data-nome_suporte="'.$r_tipo_suporte['nome_suporte'].'" data-id_tipo_suporte="'.$r_tipo_suporte['id_tipo_suporte'].'"  name="alerta_sms" data-tipo_notificacao="alerta_sms">
                                                        <span class="form-check-label">
                                                           SMS
                                                        </span>
                                                    </label>
                                                    <!--end::Checkbox-->

                                                    <!--begin::Checkbox-->
                                                    <label class="form-check form-check-custom form-check-solid">
                                                        <input class="form-check-input notification-link" type="checkbox" value="1" data-nome_suporte="'.$r_tipo_suporte['nome_suporte'].'" data-id_tipo_suporte="'.$r_tipo_suporte['id_tipo_suporte'].'"  name="alerta_whats" data-tipo_notificacao="alerta_whats">
                                                        <span class="form-check-label">
                                                           Whatsapp
                                                        </span>
                                                    </label>
                                                    <!--end::Checkbox-->
                                                </div>
                                                <!--end::Wrapper-->
                                            </td>

                                            <td> <select class="form-select form-select-solid select-periodo" data-kt-select2="true" data-placeholder="Escolha o Período" data-dropdown-parent="#modal_nova_colecao_notificacao" id="periodo_notficacao_'.$r_tipo_suporte['id_tipo_suporte'].'"  data-nome_suporte="'.$r_tipo_suporte['nome_suporte'].'" data-id_tipo_suporte="'.$r_tipo_suporte['id_tipo_suporte'].'"  name="periodo_verificacao"  data-tipo_notificacao="periodo_verificacao">
                                           
                                            <option value="0">Defina o Período</option>
                                            <option value="5_minutos">Em Tempo Real</option>
                                            <option value="hora">A cada Hora</option>
                                            <option value="dia">Uma vez ao Dia</option>
                                        </select></td>
                                            <!--end::Input group-->
                                        </tr>';

                                        

                                        }
                                    }else{
                                        echo '<tr>
                                        <!--begin::Label-->
                                        <td class="text-gray-600">Não foram localizadas Categorias de Suporte, definidas para gestão de alertas, até o momento.</td>
                                        <!--end::Label-->

                                        </tr>';
                                    
                                    }


                                        ?>
                                            


                                          </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table wrapper-->
                        </div>
                        <!--end::Permissions-->
                    </div>
                    <!--end::Scroll-->

                    <!--begin::Actions-->
                    <div class="text-center pt-15">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Fechar</button>

                     
                       
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->


    <script>

 /* Controla as Notificações */


 
 $('.select-periodo').select2({
    minimumResultsForSearch: Infinity
});
 


function notificationLinkClickHandler() {

     // ...
     var linkElement = $(this);
    // ...

    var IdProjetoColecao = KTCookie.get('id_obra_colecao') ?? null;
    var NomeProjetoColecao = KTCookie.get('nome_obra_colecao') ?? null;

    //alert(IdProjetoColecao)

    if (IdProjetoColecao===null || NomeProjetoColecao===null) {

 Swal.fire({
    title: 'Selecione um Projeto',
    html: "Para poder receber notificações, você precisa selecionar um projeto.<br><br> Deseja selecionar um projeto agora?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sim, selecionar',
    cancelButtonText: 'Não, cancelar'
}).then((result) => {
    if (result.isConfirmed) {
        $("#IdProjetoColecao").select2().select2('open');
        $('html, body').animate({
            scrollTop: $("#IdProjetoColecao").offset().top
        }, 2000);
    } else if (result.isDismissed) {
        // Insira o comando para fechar a modal aqui.
        $(document).ready(function() {
            $('#modal_nova_colecao_notificacao').modal('hide');
            //location.reload();
            console.log("Modal de Coleções para as Notificações fechada");
        });
    }
});



        return false;
    } 

    console.log("Preparando para alterar notificação");

    var idProjeto = IdProjetoColecao;
    var idTipoSuporte = $(this).data('id_tipo_suporte');
    var NomeTipoSuporte = $(this).data('nome_suporte');
    var TipoNotificacao = $(this).data('tipo_notificacao');
    


 
            $.ajax({
                url: "/crud/suporte/controla-notificacoes.php",
                type: "POST",
                data: {
                    acao: 'update_notificacao',
                    idProjeto: idProjeto,
                    NomeProjeto: NomeProjetoColecao,
                    idTipoSuporte: idTipoSuporte,
                    NomeTipoSuporte: NomeTipoSuporte,
                    TipoNotificacao: TipoNotificacao,
                   
                },
                success: function(response) {
                    if(response.codigo==1){


                       
                         // Aqui está o bloco que altera a cor do elemento selecionado
                      var tdElement = linkElement.closest('tr').find('.titulo_nome_tipo_suporte');
                      tdElement.removeClass('text-gray-600').addClass('text-primary');
                    
                      // Verifica se o ícone já existe
                      if (tdElement.find('.icone-adicionado').length === 0) {
                          // Adiciona o ícone
                          tdElement.prepend('<i class="fa fa-check text-success  icone-adicionado"></i> ');
                      }
                      // Fim do bloco de código adicionado
                      createMetronicToast('STEP',  response.mensagem, 10000);
                        console.log("Notificação alterada com sucesso");
                     

                       
                    }else{
                        Swal.fire(
                            'Erro!',
                            response.mensagem,
                            'error'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire(
                        'Erro!',
                        'Ocorreu um erro ao alterar a notificação.',
                        'error'
                    );
                }
            });
        };

$('#IdProjetoColecao').select2().on('change', function(e) {

    var valorSelecionado = $(this).val();
    var dataSelecionada = $(this).select2('data');
    var textoSelecionado = dataSelecionada[0].text;

    KTCookie.set('id_obra_colecao', valorSelecionado);
    KTCookie.set('nome_obra_colecao', textoSelecionado);

    $('.notification-link').prop('checked', false);

    var url = "/views/suportes/notificacoes/consulta_notificacoes_projeto_usuario.php?projeto_atual=" + valorSelecionado + "&acao=atualizar_lista_notificacoes_projeto_usuario";

    $.ajax({
        url: url,
        type: "POST",
        data: {
            projeto_atual: valorSelecionado,
            acao:'atualizar_lista_notificacoes_projeto_usuario'
        },
        dataType: "html",
        beforeSend: function() {
            $("#Tabela_Notificacoes_Usuario").html('Aguarde, Carregando dados...');
            
        },
        success: function(data) {
            $("#Tabela_Notificacoes_Usuario").html(data);
            $(".notification-link").click(notificationLinkClickHandler);
            
        },
        error: function(xhr, status, error) {
            Swal.fire(
                'Erro!',
                'Ocorreu um erro ao atualizar a lista de notificações.',
                'error'
            );
        }
    });

    
});

notificationLinkClickHandler();


    </script>