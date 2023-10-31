<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';
// Set the JSON header ==[ Cálculo de Indicadores ]=====
header('Content-Type: application/json; charset=utf-8');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

//==================================================================================================================================================


$id_usuario_sessao = isset($_COOKIE['id_usuario_sessao']) ? $_COOKIE['id_usuario_sessao'] : '';

$projeto_selecionado = isset($_COOKIE['projeto_atual']) ? $_COOKIE['projeto_atual'] : '';

$Data_Atual_Periodo = date_create()->format('Y-m-d');

// Verifico se ha algum cálculo Dinâmico de Monitoramento, Cadastrado e Ativo para a estação selecionada    
if ($id_usuario_sessao != '') {

if($projeto_selecionado!=''){
    $sql = $conexao->prepare(" SELECT c.*,
                                o.codigo_obra,
                                o.nome_obra,
                                p.nome_ponto,
                                e.nome_estacao

                            FROM cockpit  c

                          INNER JOIN 
                          estacoes e ON e.id_estacao = c.estacao_selecionada_regra
                         INNER JOIN 
                        pontos_estacao p ON p.id_estacao = c.estacao_selecionada_regra
                        INNER JOIN
                        obras o ON o.id_obra = p.id_obra
                         
     WHERE c.id_usuario ='$id_usuario_sessao' AND c.status_cockpit='1' AND e.id_obra = '$projeto_selecionado' group by c.id_cockpit");

} else {
    $sql = $conexao->prepare(" SELECT c.*,
                            o.codigo_obra,
                            o.nome_obra,
                            p.nome_ponto,
                            e.nome_estacao

                            FROM cockpit  c

                            INNER JOIN 
                            estacoes e ON e.id_estacao = c.estacao_selecionada_regra
                            INNER JOIN 
                            pontos_estacao p ON p.id_estacao = c.estacao_selecionada_regra
                            INNER JOIN
                            obras o ON o.id_obra = p.id_obra

                            WHERE c.id_usuario ='$id_usuario_sessao' AND c.status_cockpit='1'  group by c.id_cockpit");
}
    $sql->execute();

    $count = $sql->rowCount();

    if ($count > 0) {

?>

<script>
    
function createChart(id_cockpit, tipo_grafico, modelo_consulta, periodo_analise_regra, nome_regra, nome_obra, nome_estacao) {
    const url = `/crud/dashboard/cockpit/consulta-dados-itens-cockpit-usuario.php?id_cockpit=${id_cockpit}&modelo_consulta=${modelo_consulta}&periodo_dados=${periodo_analise_regra}`;

    var chartContainer = document.querySelector(`#modelo_grafico_${id_cockpit}`);

    // Adicionando uma mensagem de carregamento ao container do gráfico
      // Adicionando uma mensagem de carregamento ao container do gráfico
      let loadingMessage = document.createElement("div");
    loadingMessage.id = 'loadingMessage_' + id_cockpit;

    // Criação de spinner e mensagem de carregamento
    loadingMessage.innerHTML = `
    <div class="d-flex align-items-center">
        <div class="spinner-border text-primary " role="status">
        <span class="visually-hidden ">Loading...</span>
        </div>
        <div class="me-10"><span class="text-light-primary px-3">Criando seu gráfico...</span></div>
    </div>
    `;

chartContainer.appendChild(loadingMessage);


    return new Promise((resolve, reject) => {
        fetch(url)
            .then(response => {
                if (!response.ok) { throw response }
                return response.json()
            })
            .then(data => {

                             
                // Removendo a mensagem de carregamento
                let loadingMessage = document.querySelector("#loadingMessage_"+id_cockpit);
                if (loadingMessage) {
                    chartContainer.removeChild(loadingMessage);
                }

     
                Highcharts.setOptions({
                    lang: {
                        months: [
                            'Janeiro', 'Fevereiro', 'Março', 'Abril',
                            'Maio', 'Junho', 'Julho', 'Agosto',
                            'Setembro', 'Outubro', 'Novembro', 'Dezembro'
                        ],
                        weekdays: [
                            'Domingo', 'Segunda', 'Terça', 'Quarta',
                            'Quinta', 'Sexta', 'Sábado'
                        ],
                        shortMonths: [
                            'Jan', 'Fev', 'Mar', 'Abr',
                            'Mai', 'Jun', 'Jul', 'Ago',
                            'Set', 'Out', 'Nov', 'Dez'
                        ],
                        viewFullscreen:'Ver em Tela Cheia',
                        exitFullscreen:'Sair da Tela Cheia',
                        decimalPoint: ',',
                        thousandsSep: '.',
                        downloadCSV: 'Baixar CSV',
                        printChart: 'Imprimir gráfico',
                        viewData: 'Visualizar dados',
                        hideData: 'Recolher dados',
                        rangeSelectorFrom: 'De',
                        rangeSelectorTo: 'Até',
                        rangeSelectorZoom: 'Zoom',
                        resetZoom: 'Redefinir zoom',
                        resetZoomTitle: 'Redefinir zoom para nível 1:1',
                        loading: 'Carregando...',
                        contextButtonTitle: 'Menu do gráfico',
                        decimalPoint: ',',
                        printButtonTitle: 'Imprimir gráfico',
                        exportButtonTitle: 'Exportar gráfico',
                        drillUpText: 'Voltar para {series.name}',
                        noData: 'Sem dados para exibir',
                    
                    }
                });

                

                // Configurar o gráfico
                var chart = Highcharts.chart('modelo_grafico_' + id_cockpit, {

                    exporting: {
                        buttons: {
                            contextButton: {
                            menuItems: ["viewFullscreen", "separator", "downloadPNG", "downloadJPEG", "downloadPDF", "downloadSVG", "separator", "downloadCSV", "downloadXLS", "viewData", "openInCloud" ]
                            }
                        },
                    
                    },

                      chart: {
                            styledMode: false,
                            type: tipo_grafico,
                            height: 350, // Defina a altura desejada aqui
                            zoomType: 'x',
                            panning: true,
                            panKey: 'shift'
                        },
                    
                    title: {
                        text: nome_regra + ' - ' + nome_obra + ' - ' + nome_estacao,
                    },
                    subtitle: {
                    text: nome_obra + ' - ' + nome_estacao,
                    
                },
                    xAxis: {
                        className: 'highcharts-color-1',
                        type: 'datetime',
                        tickInterval: 1 * 24 * 3600 * 1000, // one week
                        tickWidth: 1,
                        gridLineWidth: 1,
                            labels: {
                                align: 'left',
                                x: 1,
                                y: 33
                            },
                    },


                    yAxis: [{ // left y axis
                        className: 'highcharts-color-0',
                            title: {
                                text: null
                            },
                            labels: {
                                align: 'left',
                                x: 3,
                                y: 16,
                                format: '{value:.,0f}'
                            },
                            showFirstLabel: false
                        }, { // right y axis
                            linkedTo: 0,
                            gridLineWidth: 0,
                            opposite: true,
                            title: {
                                text: null
                            },
                            labels: {
                                align: 'right',
                                x: -3,
                                y: 16,
                                format: '{value:.,0f}'
                            },
                            showFirstLabel: false,

                            plotLines: [] // Inicialmente, sem linhas de limite
                        }],
                  
                    loading: {
                        hideDuration: 1000,
                        showDuration: 1000,
                        labelStyle: { visibility: 'visible' },
                        style: { backgroundColor: 'transparent' },
                        labelStyle: { fontWeight: 'bold', position: 'relative', top: '45%', zIndex: 10 },
                        labelText: 'Carregando...'
                    },
                    
                     plotOptions: {
                        series: {
                            column: {
                            borderRadius: 5
                        },
                            point: {
                                events: {
                                    click: function() {
                                        var series = this.series.options;
                                         // Quando um ponto da série for clicado, atualizar o título do eixo Y
                                     
                                        chart.yAxis[0].update({
                                            title: {
                                                text: this.series.options.nome_ponto + ' - ' + this.series.name + ' ' + this.series.options.nome_unidade_medida,
                                            },
                                            plotLines: [
                                                {
                                                    color: 'red',
                                                    width: 2,
                                                    value: series.concen_min,
                                                    label: {
                                                        text: 'Limite mínimo: ' + series.concen_min + ' ' + series.nome_unidade_medida,
                                                        align: 'left'
                                                    }
                                                },
                                                {
                                                    color: 'green',
                                                    width: 2,
                                                    value: series.concen_max,
                                                    label: {
                                                        text: 'Limite máximo: ' + series.concen_max + ' ' + series.nome_unidade_medida	,
                                                       align: 'right'
                                                    }
                                                }
                                            ]
                                        });
                                    }
                                }
                            }
                        }
                    },
                    tooltip: {
                    shared: true,
                    crosshairs: true,
                    pointFormat: '{series.name}: <b>{point.y}</b> {series.options.nome_unidade_medida} <br/><b>PLCode:</b> {series.options.nome_ponto}<br/><br/>'
                },
                    series: seriesData,
                    responsive: {
                         rules: [{
                            condition: {
                                maxWidth: 500
                            },
                            chartOptions: {
                                legend: {
                                    align: 'center',
                                    verticalAlign: 'bottom',
                                    layout: 'horizontal'
                                },
                                xAxis: {
                        className: 'highcharts-color-1',
                        type: 'datetime',
                        tickInterval: 1 * 24 * 3600 * 1000, // one week
                        tickWidth: 1,
                        gridLineWidth: 1,
                            labels: {
                                align: 'left',
                                x: 1,
                                y: 33
                            },
                    },
                                yAxis: {
                                    labels: {
                                        align: 'left',
                                        x: 0,
                                        y: -5
                                    },
                                    title: {
                                        text: 'Selecine um dado para atualizar'
                                    }
                                },
                                subtitle: {
                                    text: null
                                },
                                credits: {
                                    enabled: false
                                }
                            }
                        }]
                    }
                });

                chart.showLoading(`
                <div class="d-flex align-items-center">
        <div class="spinner-border text-primary " role="status">
        <span class="visually-hidden ">Loading...</span>
        </div>
        <div class="me-10"><span class="text-light-primary px-3">Criando seu gráfico...</span></div>
    </div>
            `);
            

                chart.showLoading(); // Mostrar o carregamento enquanto os dados estão sendo buscados

                // Se não houver dados, exibir uma mensagem
                if (data.length === 0 || (data[0].hasOwnProperty('sem_dados') && data[0].sem_dados === 0)) {
                    console.log("Sem dados no período solicitado id cockpit: " + id_cockpit);
                    chartContainer.innerHTML = '<p style="text-align:center; margin-top: 50px;" class="text-gray-500">Sem dados para exibir no período<br>Solicitado de '+ periodo_analise_regra+' dias.<br>Para o Cockpit: <b>ID#'+id_cockpit+' Título: ' +nome_regra+ '</b></p>';
                    chart.hideLoading();
                    return;
                }

                if (!Array.isArray(data) || data.length === 0) {
                    throw new Error("Dados inválidos recebidos da API");
                }

                // Criar as séries de dados
                var seriesData = data.map(function(item) {
                    var dataPoints = item.data.map(function(dataPoint) {
                        return [dataPoint[0], dataPoint[1]];
                    });

                    return {
                        name: item.name,
                        data: dataPoints,
                        nome_unidade_medida: item.nome_unidade_medida,
                        concen_min: item.concen_min,
                        concen_max: item.concen_max,
                        nome_ponto: item.nome_ponto,
                     
                    };
                });

                // Atualizar as séries do gráfico com os dados
              // Adicionar as séries ao gráfico
                seriesData.forEach(function(series) {
                    chart.addSeries(series, false);
                });

                chart.hideLoading(); // Esconder o carregamento agora que os dados estão carregados

                chart.redraw(); // Redesenha o gráfico para exibir as novas séries

                resolve();

            })
            .catch(error => {
                console.error('Erro na API:', error);
                let loadingMessage = document.querySelector("#loadingMessage_"+id_cockpit);
                if (loadingMessage) {
                    chartContainer.removeChild(loadingMessage);
                }
                chart.hideLoading();
                reject(error);
            });
    });
}


</script>

<?php

        foreach ($sql as $res) {

            //... código para atribuir valores às variáveis ...
           
            $codigo_obra = $res['codigo_obra'] ?? '';
            $id_cockpit = $res['id_cockpit'];
            $nome_obra = '<span class="badge badge-light-success">'.$codigo_obra.'</span> '.$res['nome_obra'];
           // $nome_obra = substr($nome_obra_full, 0, 10) . ".";

            $nome_estacao_full = $res['nome_estacao'];
            $nome_estacao = substr($nome_estacao_full, 0, 10) . "";
            
            $nome_ponto = $res['nome_ponto'];
            $id_usuario = $res['id_usuario'];
            $estacao_selecionada_regra  = $res['estacao_selecionada_regra'];
            $nome_regra = $res['nome_regra'];
            $modelo_consulta = $res['modelo_grafico'];
            $indicador_unico_regra  = $res['indicador_unico_regra'];
            $periodo_analise_regra = $res['periodo_analise_regra'];

            // print_r($res['id_cockpit']);

            $periodo_dados='';	

   
            switch ($periodo_analise_regra) {
                case 0:
                    $nome_periodo = 'Último Valor Informado';
                    $periodo_dados = date('Y-m-d', strtotime('-1 days', strtotime($Data_Atual_Periodo)));

                    break;

                case 7:
                    $nome_periodo = 'Dados dos Últimos 7 dias';
                    $periodo_dados = date('Y-m-d', strtotime('-7 days', strtotime($Data_Atual_Periodo)));
                
                    break;

                    case 15:
                        $nome_periodo = 'Dados dos Últimos 15 dias';
                        $periodo_dados = date('Y-m-d', strtotime('-15 days', strtotime($Data_Atual_Periodo)));
                       
                        break;

                case 30:
                    $nome_periodo = 'Dados dos Últimos 30 dias';
                    $periodo_dados = date('Y-m-d', strtotime('-30 days', strtotime($Data_Atual_Periodo)));
                   
                    break;

                    default: 
                    $nome_periodo = "Padrão - Últimos 30 Dias";
                    $periodo_dados = date('Y-m-d', strtotime('-30 days', strtotime($Data_Atual_Periodo)));
                    break;
            }

            switch ($modelo_consulta) {
                case 1:
                    $nome_modelo_grafico = 'Este é semelhante ao gráfico de área, mas com uma linha suavizada';
                    $tipo_grafico = 'areaspline';
                    break;

                    case 2:
                        $nome_modelo_grafico = 'Este tipo de gráfico descreve uma linha que passa por todos os pontos de dados. Pode ser usado para exibir tendências ao longo do tempo';
                        $tipo_grafico = 'area';

                    case 3:
                    $nome_modelo_grafico = 'Este tipo de gráfico é semelhante ao gráfico de linha, mas a área sob a linha é preenchida em colunas verticais. É útil para exibir alterações de valor ao longo do tempo ou para ilustrar comparações entre itens.';
                    $tipo_grafico = 'column';

                    case 4:
                    $nome_modelo_grafico = 'Este tipo de gráfico exibe pontos de dados individuais. A posição de cada ponto corresponde ao seu valor.';
                    $tipo_grafico = 'scatter';

                    case 5:
                        $nome_modelo_grafico = 'Este tipo de gráfico representa cada ponto de dados como uma Linha individual. As alturas das linhas correspondem aos valores dos pontos de dados.';
                        $tipo_grafico = 'column';

                
                default:
                    # code...
                    break;
            }


            
        //Imprima uma div com um identificador único para cada gráfico e um indicador de carregamento
     


        echo " <!--begin::Gráfico $id_cockpit-->
                                            <div class='col draggable' tabindex='0' id='id_cockpit_$id_cockpit'>

                                            <div class='card bg-light-secondary'>
                
                                            <div class='card-header py-0'>
                                            <div class='card-toolbar align-items-right' style='max-height: 20px;'>
                                                <button class='btn btn-sm btn-link btn-color-muted btn-active-color-danger me-5 mb-2 apaga_cockpit' data-nome_regra='$nome_regra' data-id='$id_cockpit' data-kt-menu-trigger='click' data-kt-menu-placement='bottom-end' data-bs-toggle='tooltip' data-bs-placement='top' title='Clique para Eliminar este Monitoramento'>
                                                    <i class='bi bi-x-square fs-3'></i> Eliminar Cockpit
                                                </button>
                                                <a href='#' class='btn btn-sm btn-link btn-color-muted btn-active-color-primary me-5 mb-2 draggable-handle' data-bs-toggle='tooltip' data-bs-placement='top' title='Clique para Mover este Item'>
                                                <i class='bi bi-arrows-move'></i> Mover Cockpit
                                                </a>
                                            </div>
                                        </div>
                                        
                
                                                    
                                                    <!--begin::Body-->            
                                                     <div  class='card-body'>
                                                      
                                                            <!--begin::Chart-->
                                                                <div class='mixed-widget-10-chart modelo_consulta chart-container' data-kt-color='primary'
                                                                    style='height: 350px; min-height: 300px;' id='modelo_grafico_$id_cockpit'>

                        
                                                                    </div>

                                                                </div>
                                                            <!--end::Chart-->

                                                            <button class='btn btn-sm btn-light-primary' type='button' data-bs-toggle='collapse' data-bs-target='#collapseExample' aria-expanded='false' aria-controls='collapseExample'>
                                                            Detalhes do Cockpit
                                                          </button>
                                                          <div class='collapse' id='collapseExample'>
                                                            <div class='card card-body'>
                                                                <div class='d-flex flex-column'>
                                                                    <li class='d-flex align-items-center py-2'>
                                                                        <span class='bullet bullet-dot bg-info me-5'></span> <span class='text-gray-500'>$nome_periodo</span> 
                                                                    </li>

                                                                    <li class='d-flex align-items-center py-2'>
                                                                        <span class='bullet bullet-dot bg-primary me-5'></span> <span class='text-gray-600'>$nome_modelo_grafico</span>
                                                                    </li>


                                                                    <!--begin::Example-->
                                                                <div class='separator separator-dotted separator-content border-success my-10'>
                                                                    <i class='ki-duotone ki-check-square fs-6 text-success'> Dicas de Uso</i>
                                                                </div>
                                                                <!--end::Example-->

                                                                    <li class='d-flex align-items-center py-2'>
                                                                    <span class='bullet bullet-dot bg-success me-5'></span> <span class='text-gray-600 fs-6'>Clique sobre o gráfico e saiba <b>instantaneamente</b>, os Limites de Concentrações Permitidas sobre a linha do indicador que você clicar.</span>
                                                                </li>

                                                                <li class='d-flex align-items-center py-2'>
                                                                    <span class='bullet bullet-dot bg-success me-5'></span> <span class='text-gray-600 fs-6'>Quando selecionar um indicador no gráfico, a <b>linha Y</b> será atualizada com o <b>Plcode e Indicador </b> selecionados e sua respectiva Unidade de Medida.</span>
                                                                </li>

                                                                <li class='d-flex align-items-center py-2'>
                                                                <span class='bullet bullet-dot bg-success me-5'></span> <span class='text-gray-600 fs-6'>Lembre-se que você também poderá acessar o <b><i class='bi bi-list text-dark fs-5'></i> Menu do Gráfico</b> para acessar Recursos Avançados, como: Visualizar em <b>Tela Cheia</b> e opções de Exportação dos Dados.</span>
                                                            </li>

                                                                    

                                                                </div>
                                                            </div>
                                                          </div>
                                                          

                                                     </div>
                                                    <!--end::Body-->


                
                                                   
                
                                                </div>
                
                
                                            </div>
                            <!--end::Gráfico $id_cockpit -->";	
                           
// echo "<script>createChart($dado[id_cockpit], '$dado[modelo_consulta]', '$dado[periodo_analise_regra]', '$dado[nome_regra]', '$dado[nome_obra]', '$dado[nome_estacao]');</script>"; 
?>

<script>
    createChart('<?php echo $id_cockpit; ?>', '<?php echo  $tipo_grafico; ?>', '<?php echo $modelo_consulta; ?>', '<?php echo $periodo_analise_regra; ?>', '<?php echo  $nome_regra; ?>', '<?php echo $nome_obra; ?>', '<?php echo $nome_estacao; ?>');
</script>

<?php
            

        } // fecha foreach principal



    } else {


        if($projeto_selecionado==''){

            // aqui ainda colocar a nova funcao

        echo '                            <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
                                <span class="svg-icon svg-icon-2hx svg-icon-success me-4" >
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M1.34375 3.9463V15.2178C1.34375 16.119 2.08105 16.8563 2.98219 16.8563H8.65093V19.4594H6.15702C5.38853 19.4594 4.75981 19.9617 4.75981 20.5757V21.6921H19.2403V20.5757C19.2403 19.9617 18.6116 19.4594 17.8431 19.4594H15.3492V16.8563H21.0179C21.919 16.8563 22.6562 16.119 22.6562 15.2178V3.9463C22.6562 3.04516 21.9189 2.30786 21.0179 2.30786H2.98219C2.08105 2.30786 1.34375 3.04516 1.34375 3.9463ZM12.9034 9.9016C13.241 9.98792 13.5597 10.1216 13.852 10.2949L15.0393 9.4353L15.9893 10.3853L15.1297 11.5727C15.303 11.865 15.4366 12.1837 15.523 12.5212L16.97 12.7528V13.4089H13.9851C13.9766 12.3198 13.0912 11.4394 12 11.4394C10.9089 11.4394 10.0235 12.3198 10.015 13.4089H7.03006V12.7528L8.47712 12.5211C8.56345 12.1836 8.69703 11.8649 8.87037 11.5727L8.0107 10.3853L8.96078 9.4353L10.148 10.2949C10.4404 10.1215 10.759 9.98788 11.0966 9.9016L11.3282 8.45467H12.6718L12.9034 9.9016ZM16.1353 7.93758C15.6779 7.93758 15.3071 7.56681 15.3071 7.1094C15.3071 6.652 15.6779 6.28122 16.1353 6.28122C16.5926 6.28122 16.9634 6.652 16.9634 7.1094C16.9634 7.56681 16.5926 7.93758 16.1353 7.93758ZM2.71385 14.0964V3.90518C2.71385 3.78023 2.81612 3.67796 2.94107 3.67796H21.0589C21.1839 3.67796 21.2861 3.78023 21.2861 3.90518V14.0964C15.0954 14.0964 8.90462 14.0964 2.71385 14.0964Z" fill="currentColor"/>
</svg>
                               
                                </span>
                                <!--end::Svg Icon-->
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-success">Cockpit - Ausente</h4>
                                    <span><p>Você ainda não criou nenhum Cockpit para monitoramento.</p> <p>Lembre-se que você após criar Cockpit`s para vários Projetos, poderá filtrar a exibição deles ao selecionar o Projeto em seu Dashboard.</p> <p>Para criar seu 1º Cockpit acesse o Menu ou <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kt_modal_create_cockpit"  class="btn btn-sm btn-light-primary "> Clique Aqui
                                        </a></p></span>
                                </div>
                            </div>';
                        } else {

                            echo '  <div class="col-lg-12 alert alert-success d-flex align-items-center p-5 mb-10">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
                            <span class="svg-icon svg-icon-2hx svg-icon-success me-4" >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M1.34375 3.9463V15.2178C1.34375 16.119 2.08105 16.8563 2.98219 16.8563H8.65093V19.4594H6.15702C5.38853 19.4594 4.75981 19.9617 4.75981 20.5757V21.6921H19.2403V20.5757C19.2403 19.9617 18.6116 19.4594 17.8431 19.4594H15.3492V16.8563H21.0179C21.919 16.8563 22.6562 16.119 22.6562 15.2178V3.9463C22.6562 3.04516 21.9189 2.30786 21.0179 2.30786H2.98219C2.08105 2.30786 1.34375 3.04516 1.34375 3.9463ZM12.9034 9.9016C13.241 9.98792 13.5597 10.1216 13.852 10.2949L15.0393 9.4353L15.9893 10.3853L15.1297 11.5727C15.303 11.865 15.4366 12.1837 15.523 12.5212L16.97 12.7528V13.4089H13.9851C13.9766 12.3198 13.0912 11.4394 12 11.4394C10.9089 11.4394 10.0235 12.3198 10.015 13.4089H7.03006V12.7528L8.47712 12.5211C8.56345 12.1836 8.69703 11.8649 8.87037 11.5727L8.0107 10.3853L8.96078 9.4353L10.148 10.2949C10.4404 10.1215 10.759 9.98788 11.0966 9.9016L11.3282 8.45467H12.6718L12.9034 9.9016ZM16.1353 7.93758C15.6779 7.93758 15.3071 7.56681 15.3071 7.1094C15.3071 6.652 15.6779 6.28122 16.1353 6.28122C16.5926 6.28122 16.9634 6.652 16.9634 7.1094C16.9634 7.56681 16.5926 7.93758 16.1353 7.93758ZM2.71385 14.0964V3.90518C2.71385 3.78023 2.81612 3.67796 2.94107 3.67796H21.0589C21.1839 3.67796 21.2861 3.78023 21.2861 3.90518V14.0964C15.0954 14.0964 8.90462 14.0964 2.71385 14.0964Z" fill="currentColor"/>
</svg>
                           
                            </span>
                            <!--end::Svg Icon-->
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-success">Cockpit - Ausente no Projeto</h4>
                                <span>O Projeto atual não possui Cockpit de monitoramento, para criar seu 1º Cockpit  acesse o Menu ou <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kt_modal_create_cockpit"  class="fw-bold btn btn-sm btn-success"> Clique Aqui
                                    </a></span>
                            </div>
                        </div>';

                        }
    }





    print " <script>


    
      /** @type {object} colors State colors **/
    var colors = {};

     var el = $(el);

    function initTooltip(el) {
        var skin = el.data('skin') ? 'tooltip-' + el.data('skin') : '';
        var width = el.data('width') == 'auto' ? 'tooltop-auto-width' : '';
        var triggerValue = el.data('trigger') ? el.data('trigger') : 'hover';
        var placement = el.data('placement') ? el.data('placement') : 'left';

        el.tooltip({
            trigger: triggerValue,
            template: '<div class=\"tooltip ' + skin + ' ' + width + '\" role=\"tooltip\">\
                <div class=\"arrow\"></div>\
                <div class=\"tooltip-inner\"></div>\
            </div>'
        });
    }

    function initTooltips() {
        // init bootstrap tooltips
        $('[data-toggle=\"kt-tooltip\"]').each(function() {
            initTooltip($(this));
        });
    }


   initTooltips();
 
    initTooltip(el);
    
    
     
   </script>";

    $conexao = null;

    exit;
} else {


    print "id do usuário da sessão não reconhecido";
   
    die("id do usuário não localizado ao tentar buscar os cockpits criados por ele");
}