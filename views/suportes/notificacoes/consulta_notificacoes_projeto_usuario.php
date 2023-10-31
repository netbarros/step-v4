<?php
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$_SESSION['pagina_atual'] = 'Consulta Notificações do Projeto por Usuário';

$acao = trim($_POST['acao']) ?? '';

$projeto_atual = isset($_POST['projeto_atual']) ? $_POST['projeto_atual'] : (isset($_COOKIE['projeto_atual']) ? $_COOKIE['projeto_atual'] : '');

$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';

$nome_projeto = trim(isset($_COOKIE['nome_projeto'])) ? $_COOKIE['nome_projeto'] : '';

$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : (isset($_SESSION['id']) ? $_SESSION['id'] : '');




if($acao=='atualizar_lista_notificacoes_projeto_usuario'){
?> 



                            <tbody class="text-gray-600 fw-semibold">
                                        <!--begin::Table row-->
                                       
                                        
                                          
                                        <!--begin::Table row-->
                                        <?php /* trazemos os tipos de suporte que o STEP gerencia */

$sql_tipo_suporte = $conexao->prepare("
    SELECT 
        ts.id_tipo_suporte,
        ts.nome_suporte,
        nu.id_notificacao_usuario,
        nu.id_usuario,
        nu.id_obra,
        nu.alerta_email,
        nu.alerta_sms,
        nu.alerta_whats,
        nu.periodo_verificacao,
        nu.data_atualizacao,
        nu.status_notificacao_usuario
    FROM
        tipo_suporte ts
    LEFT JOIN notificacoes_usuario nu 
        ON ts.id_tipo_suporte = nu.id_tipo_suporte 
        AND nu.id_usuario = ? 
        AND nu.id_obra = ?
    WHERE
        ts.status_tipo_suporte = '1' ORDER BY ts.nome_suporte ASC
") or die($conexao->error);

$sql_tipo_suporte->execute([$id_usuario_sessao, $projeto_atual]);
$total_tipo_suporte = $sql_tipo_suporte->rowCount();
$row_tipo_suporte = $sql_tipo_suporte->fetchAll(PDO::FETCH_ASSOC);

if($total_tipo_suporte > 0){


                                                foreach($row_tipo_suporte as $r_tipo_suporte){

                                                   

                                           // ...
// trato do css dos tipos de suporte que j[a fazem parte da colection	do user]
    $email_set = $r_tipo_suporte['alerta_email'] == '1';
    $checked_email = $email_set ? 'checked' : '';
    
    $sms_set = $r_tipo_suporte['alerta_sms'] == '1';
    $checked_sms = $sms_set ? 'checked' : '';
    
    $whats_set = $r_tipo_suporte['alerta_whats'] == '1';
    $checked_whats = $whats_set ? 'checked' : '';
    
    $periodo_verificacao = $r_tipo_suporte['periodo_verificacao'] ?? '';

    // Se qualquer uma das variáveis estiver setada, adiciona a classe e o ícone
    $addIconAndClass = $email_set || $sms_set || $whats_set || $periodo_verificacao;

    $tdClass = $addIconAndClass ? 'text-primary' : 'text-gray-600';
    $icon = $addIconAndClass ? '<i class="fa fa-check text-success  icone-adicionado"></i>' : '';

// trato o a validacao do periodo de notificacao selecionado pelo usuario para o tipo de suporte especifico de cada linha de dados
    $selected_5_minutos = $periodo_verificacao == '5_minutos' ? 'selected' : '';
    $selected_hora = $periodo_verificacao == 'hora' ? 'selected' : '';
    $selected_dia = $periodo_verificacao == 'dia' ? 'selected' : '';



                                                                echo '<tr>
                                                                <!--begin::Label-->
                                                                <td class="' . $tdClass . ' me-0 me-lg-20 titulo_nome_tipo_suporte ">'.$icon.$r_tipo_suporte['nome_suporte'].'</td>
                                                                <!--end::Label-->
                    
                                                                <!--begin::Input group-->
                                                                <td>
                                                                    <!--begin::Wrapper-->
                                                                    <div class="d-flex">
                                                                        <!--begin::Checkbox-->
                                                                        <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                            <input class="form-check-input notification-link" type="checkbox" '.$checked_email.' data-nome_suporte="'.$r_tipo_suporte['nome_suporte'].'" data-id_tipo_suporte="'.$r_tipo_suporte['id_tipo_suporte'].'"  value="1" name="alerta_email" data-tipo_notificacao="alerta_email" >
                                                                            <span class="form-check-label me-3">
                                                                                Email
                                                                            </span>
                                                                        </label>
                                                                        <!--end::Checkbox-->
                    
                                                                        <!--begin::Checkbox-->
                                                                        <label class="form-check form-check-custom form-check-solid me-3 me-lg-20">
                                                                            <input class="form-check-input notification-link" type="checkbox" value="1" '.$checked_sms.' data-nome_suporte="'.$r_tipo_suporte['nome_suporte'].'" data-id_tipo_suporte="'.$r_tipo_suporte['id_tipo_suporte'].'"  name="alerta_sms" data-tipo_notificacao="alerta_sms" >
                                                                            <span class="form-check-label">
                                                                            SMS
                                                                            </span>
                                                                        </label>
                                                                        <!--end::Checkbox-->
                    
                                                                        <!--begin::Checkbox-->
                                                                        <label class="form-check form-check-custom form-check-solid me-1 me-lg-10">
                                                                            <input class="form-check-input notification-link" type="checkbox" value="1" '.$checked_whats.' data-nome_suporte="'.$r_tipo_suporte['nome_suporte'].'" data-id_tipo_suporte="'.$r_tipo_suporte['id_tipo_suporte'].'"  name="alerta_whats" data-tipo_notificacao="alerta_whats" o>
                                                                            <span class="form-check-label">
                                                                            Whatsapp
                                                                            </span>
                                                                        </label>
                                                                        <!--end::Checkbox-->
                                                                    </div>
                                                                    <!--end::Wrapper-->
                                                                </td>

                                                                <td> <td> 
                                                                <select class="form-select form-select-solid  select-periodo" data-kt-select2="true" data-placeholder="Escolha o Período" data-dropdown-parent="#modal_nova_colecao_notificacao"  id="periodo_notficacao_'.$r_tipo_suporte['id_tipo_suporte'].'"  data-nome_suporte="'.$r_tipo_suporte['nome_suporte'].'" data-id_tipo_suporte="'.$r_tipo_suporte['id_tipo_suporte'].'"  name="periodo_verificacao"  data-tipo_notificacao="periodo_verificacao">
                                                                    <option value="0">Defina o Período</option>
                                                                    <option value="5_minutos" ' . $selected_5_minutos . '>Em Tempo Real</option>
                                                                    <option value="hora" ' . $selected_hora . '>A cada Hora</option>
                                                                    <option value="dia" ' . $selected_dia . '>Uma vez ao Dia</option>
                                                                </select>
                                                            </td>
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


  <?php
} else {
    echo "Acao nao executada";
}

?>

<script>  

$('.select-periodo').select2({
    minimumResultsForSearch: Infinity
});

$('.select-periodo').select2().on('change', function() {

  //...
  var tdElement = $(this).closest('tr').find('.titulo_nome_tipo_suporte');
    tdElement.removeClass('text-gray-600').addClass('text-primary');
    
    // Verifica se o ícone já existe
    if (tdElement.find('.icone-adicionado').length === 0) {
        // Adiciona o ícone
        tdElement.prepend('<i class="fa fa-check text-success  icone-adicionado"></i> ');
    }
    //...

    var PeriodoNotificacao = $(this).val();
    var dataSelecionada = $(this).select2('data');
    
    if(dataSelecionada.length) {
        var NomePeriodo = dataSelecionada[0].text;
        console.log(NomePeriodo);
    }
    
    var idTipoSuporte = $(this).attr('data-id_tipo_suporte');
    var NomeTipoSuporte = $(this).attr('data-nome_suporte');
    var TipoNotificacao = $(this).attr('data-tipo_notificacao');

    var IdProjetoColecao = KTCookie.get('id_obra_colecao') ?? null;
    var NomeProjetoColecao = KTCookie.get('nome_obra_colecao') ?? null;

    //$('select[name="periodo_verificacao"]').prop('checked', false); // altero a classe da cor do select2 ao ser selecionado

   
    

            $.ajax({
                url: "/crud/suporte/controla-notificacoes.php",
                type: "POST",
                data: {
                    acao: 'update_periodo_notificacao',
                    idProjeto: IdProjetoColecao,
                    NomeProjeto: NomeProjetoColecao,
                    idTipoSuporte: idTipoSuporte,
                    NomeTipoSuporte: NomeTipoSuporte,
                    TipoNotificacao: TipoNotificacao,
                    Periodo_Verificacao: PeriodoNotificacao,
                    Nome_Periodo: NomePeriodo
                   
                },
                success: function(response) {
                    if(response.codigo==1){

                        createMetronicToast('Retorno STEP', response.mensagem, 10000, 'success', 'bi bi-check2-square');

                       
    
                       
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
       

    });


</script>
