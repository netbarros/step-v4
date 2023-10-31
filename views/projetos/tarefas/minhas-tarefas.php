<?php
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
// define o cabeçalho da resposta como JSON

// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';

// listagem das tarefas 
if ($id_usuario_sessao != '') {
    // consultar as tarefas personalizadas
    $sql = $conexao->query("SELECT * FROM periodo_ponto pp 
INNER JOIN obras o ON o.id_obra = pp.id_obra
LEFT JOIN estacoes e ON e.id_obra = o.id_obra
LEFT JOIN pontos_estacao pt On pt.id_ponto = pp.id_ponto
LEFT JOIN parametros_ponto pr On pr.id_parametro = pp.id_parametro
WHERE pp.usuario_tarefa='$id_usuario_sessao' AND pp.status_periodo='1' GROUP BY pp.id_periodo_ponto ORDER BY pp.data_cadastro DESC");
} else {

    echo 'Erro ao consultar as tarefas do usuário';
    $conexao = null;
    exit;
}
$r = $sql->rowCount();

if ($r > 0) {

    $rtow = $sql->fetchALL(PDO::FETCH_ASSOC);

    echo ' <!--begin::Col-->
<div class="card">
                               <!--begin::Tasks-->
                               <div class="card card-flush h-lg-100">
                                   <!--begin::Card header-->
                                   <div class="card-header mt-6">
                                       <!--begin::Card title-->
                                       <div class="card-title flex-column">
                                           <h3 class="fw-bold mb-1">Minhas Tarefas</h3>';




    // consultar as tarefas personalizadas
    $sq_tot_Tarefas = $conexao->query("SELECT COUNT(DISTINCT pp.id_periodo_ponto) as Total_Tarefa
                                           FROM periodo_ponto pp
                                           INNER JOIN obras o ON o.id_obra = pp.id_obra
                                           LEFT JOIN estacoes e ON e.id_obra = o.id_obra
                                           
                                            WHERE pp.usuario_tarefa='$id_usuario_sessao'  AND pp.status_periodo='1' ");

    $rtot_Tarefas = $sq_tot_Tarefas->rowCount();


    if ($rtot_Tarefas > 0) {

        $rtowc = $sq_tot_Tarefas->fetch(PDO::FETCH_ASSOC);

        $totalc = $rtowc['Total_Tarefa'];

        echo '<div class="fs-6 text-gray-400">' . $totalc . ' Tarefas em backlog</div>';
    } else {

        echo '<div class="fs-6 text-gray-400">0 Tarefas em backlog</div>';
    }


    foreach ($rtow as $r) {
        $titulo_tarefa = $r['titulo_tarefa'] ?? 'Título não Informado - ';
        $ciclo_leitura = $r['ciclo_leitura'];
        $css_tarefa_r = $r['status_periodo'];
        $nome_plcode = $r['nome_ponto'] ?? '';
        $nome_indicador = $r['nome_parametro'] ?? '';
    
        switch ($css_tarefa_r) {
            case '1': // ativo
                $css_tarefa = 'success';
                $texto_css = 'A Tarefa está Ativa';
                break;
    
            case '3': // inativo
                $css_tarefa = 'warning';
                $texto_css = 'A Tarefa está Inativa';
                break;
    
            default: // nao executado
                $css_tarefa = 'danger';
                $texto_css = 'A Tarefa não foi Executada';
                break;
        }
    
        switch ($ciclo_leitura) {
            case '0':
                $ciclo = "Tarefa Única";
                break;
    
            case '1':
                $ciclo = "Tarefa Diária";
                break;
    
            case '2':
                $ciclo = "Tarefa Semanal";
                break;

                default: // nao executado
                $ciclo  = 'não se aplica';
                
                break;
        }
    
        $tipo = $r['tipo_checkin'];
    
        switch ($tipo) {
            case 'ponto_parametro':
                $tipo_checkin = "Checkin Leitura";
                break;
    
            case 'ponto_plcode':
                $tipo_checkin = "Checkin Presencial";
                break;
    
            case 'tarefa_agendada':
                $tipo_checkin = "Tarefa Agendada";
                break;
        }
   
    
?>




        </div>
        <!--end::Card title-->

        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body d-flex flex-column mb-9 p-9 pt-3">



            <div class="d-flex align-items-center mb-8">
                <!--begin::Bullet-->
                <span class="bullet bullet-vertical h-40px bg-<?= $css_tarefa; ?>"></span>
                <!--end::Bullet-->
                <!--begin::Checkbox-->
                <div class="form-check form-check-custom form-check-solid mx-5">
                    <a class="btn btn-active-light-success btn-sm" href="javascript:;" data-id_tarefa="<?= $r['id_periodo_ponto']; ?>" data-detalhes_tarefa="<?php echo $r['detalhes_tarefa'] ?? 'Informações adcionais da Tarefa, ausentes.'; ?>" data-titulo_tarefa="<?= $titulo_tarefa; ?>" onclick="storeDataAttributesMinhasTarefas(this)">
                        <!--begin::Svg Icon | path: C:/wamp64/www/keenthemes/core/html/src/media/icons/duotune/general/gen037.svg-->
                        <span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                                <path d="M10.4343 12.4343L8.75 10.75C8.33579 10.3358 7.66421 10.3358 7.25 10.75C6.83579 11.1642 6.83579 11.8358 7.25 12.25L10.2929 15.2929C10.6834 15.6834 11.3166 15.6834 11.7071 15.2929L17.25 9.75C17.6642 9.33579 17.6642 8.66421 17.25 8.25C16.8358 7.83579 16.1642 7.83579 15.75 8.25L11.5657 12.4343C11.2533 12.7467 10.7467 12.7467 10.4343 12.4343Z" fill="currentColor" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </a>
                </div>
                <!--end::Checkbox-->
                <!--begin::Description-->
                <div class="flex-grow-1">
                    <a href="javascript:;" class="text-gray-800 text-hover-primary fw-bold fs-6"><?= $titulo_tarefa; ?> <?= $r['nome_estacao']; ?> </a>
                    <span class="text-muted fw-semibold d-block">
                        <span class="badge badge-light-info fs-8 fw-bold"><?= $ciclo; ?></span>

                        <?= $nome_plcode; ?> <?= $nome_indicador; ?></span>

                    <span class="text-muted fw-semibold d-block"> <?php echo $r['data_tarefa'] ?? ''; ?> às <?php echo $r['hora_leitura'] ?? ''; ?></span>
                </div>
                <!--end::Description-->
                <span class="badge badge-light-<?= $css_tarefa; ?> fs-8 fw-bold" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-dismiss="click" title="<?= $texto_css; ?>"><?= $tipo_checkin; ?></span>
            </div>
        </div>
        <!--end::Col-->

<?php



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
                                                    <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">Tarefas Agendas</h4>
														<div class="fs-6 text-gray-700">Você não possui nenhuma Tarefa Delegada, até o momento.</div>
													</div>
												</div>';
}

?>

</div>
<!--end::Card body-->
</div>
<!--end::Tasks-->

<input type="hidden" id="latitude_tarefa" name="latitude_tarefa" value="">
<input type="hidden" id="longitude_tarefa" name="longitude_tarefa" value="">
<input type="hidden" id='titulo_tarefa' name='titulo_tarefa' value= "<?= $titulo_tarefa; ?>">

<script>
    //===================================[Janela Tarefas]========================================================= */

    function storeDataAttributesMinhasTarefas(e) {

        
        function success(position) {
    const latitude = position.coords.latitude;
    const longitude = position.coords.longitude;
    console.log(`Latitude: ${latitude}, Longitude: ${longitude}`);
    document.getElementById('latitude_tarefa').value = latitude;
    document.getElementById('longitude_tarefa').value = longitude;
}

function error(err) {
    console.warn(`ERRO(${err.code}): ${err.message}`);
    Swal.fire({
        title: 'Não será possível concluir suas tarefas!',
        html: `Erro: ${err.message}`,
        icon: "warning",
        buttonsStyling: false,
        confirmButtonText: "Ok!",
        customClass: {
            confirmButton: "btn fw-bold btn-primary"
        }
    });
}

if ('geolocation' in navigator) {
    navigator.geolocation.getCurrentPosition(success, error, {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0
    });
} else {
    console.log('A Geolocalização não é suportada neste navegador.');
}


        window.id_minha_tarefa = e.getAttribute('data-id_tarefa');
        window.titulo_minha_tarefa = e.getAttribute('data-titulo_tarefa');
        window.detalhes_tarefa = e.getAttribute('data-detalhes_tarefa');



        window.latitude = $('#latitude_tarefa').val() || '';
        window.longitude = $('#longitude_tarefa').val() || '';


        var checkboxValue = window.id_minha_tarefa;

        Swal.fire({
            html: '<h3>Tarefa <span class="text-primary fw-bold f4">' + titulo_minha_tarefa + '</span></h3><br><p>Nesta Tarefa, <span class="text-warning fw-bold f6">sua missão é:</span> <div class="card border border-gray-600 border-dotted border-active active d-flex py-2"> ' + detalhes_tarefa + ' </div> <br> <br>Caso tenha concluído sua Tarefa,<br>confirme nesta Janela.<br><br>O STEP irá <span class="text-warning">Notificar</span> o usuário Solicitante e dar por encerrado sua Tarefa Delegada.<br> as Tarefas concluídas, podem ser acompanhadas, através do <b>Dashboard de Tarefas</b>' +
                '<br><br>Você têm certeza que deseja Prosseguir?',
            icon: "warning",
            showCancelButton: !0,
            buttonsStyling: !1,
            confirmButtonText: "Tarefa Realizada",
            cancelButtonText: "Não, Cancelar",
            customClass: {
                confirmButton: "btn fw-bold btn-warning",
                cancelButton: "btn fw-bold btn-active-light-warning"
            }
        }).then((function(e) {
            e.value ? $.ajax({
                type: 'POST',
                url: '../../crud/tarefas/action-tarefas.php',
                dataType: 'json',
                data: {
                    id_tarefa: checkboxValue,
                    latitude: window.latitude,
                    longitude: window.longitude,
                    acao: 'conclui_tarefa'
                },
                error: function(retorno) {
                    Swal.fire({
                        html: retorno.mensagem,
                        icon: "warning",
                        buttonsStyling: !1,
                        confirmButtonText: "Ok!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary"
                        }
                    });
                },
                success: function(retorno) {

                    if (retorno.codigo == '1') {

                        Swal.fire({
                            html: retorno.mensagem,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary"
                            },
                            timer: 3000, // Adicione esta linha para configurar o temporizador para 3 segundos (3000 milissegundos)
                            showConfirmButton: false // Adicione esta linha para ocultar o botão de confirmação
                        }).then((result) => {
                        // Opcional: você pode adicionar um tratamento adicional aqui, caso queira executar alguma ação quando o alerta for fechado
                        if (result.dismiss === Swal.DismissReason.timer) {
                            console.log('Alerta fechado após 3 segundos');


                            $.ajax({
                                type: 'GET',
                                url: '../../views/projetos/tarefas/minhas-tarefas.php',
                                dataType: 'html',
                                success: function(retorno) {

                                    // obtenha a referência à div com o ID "kt_drawer_chat_toggle"
                                    var drawerEl = document.querySelector("#drawer_Tarefas");
                                    var drawer = KTDrawer.getInstance(drawerEl);
                                    drawer.update();

                                    $("#div_conteudo_Tarefas").html(retorno);

                                    drawer.update();

                                },
                                error: function() {
                                    alert("Falha ao coletar dados !!!");
                                }
                            });
                       
    }
});

                       
                            


                    } else {

                        Swal.fire({
                            html: retorno.mensagem,
                            icon: "warning",
                            buttonsStyling: !1,
                            confirmButtonText: "Ok!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary"
                            }
                        });
                    }
                }
            }).then((function() {
                t.row($(o)).remove().draw()
            })) : "cancel" === e.dismiss && Swal.fire({
                html: 'A Tarefa ID: <b>' + checkboxValue + '</b> <span class="text-primary fw-bold f4">' + titulo_minha_tarefa + '</span>, não sofreu Alteração.',
                icon: "error",
                buttonsStyling: !1,
                confirmButtonText: "Ok",
                customClass: {
                    confirmButton: "btn fw-bold btn-primary"
                }
            })
        }))



    }


    //===================================================================================================== */
</script>

<script>
    
</script>