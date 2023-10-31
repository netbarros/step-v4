<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';
// Set the JSON header ==[ Cálculo de Indicadores ]=====
header('Content-Type: application/json; charset=utf-8');

//==================================================================================================================================================


$id_usuario_sessao = isset($_COOKIE['id_usuario_sessao']) ? $_COOKIE['id_usuario_sessao'] : '';

$projeto_selecionado = isset($_COOKIE['projeto_atual']) ? $_COOKIE['projeto_atual'] : '';


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
}else{
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

    //print_r($sql);

    $retorna_dados = '';
    $retorna_dados_g = '';

    if ($count > 0) {


        $Data_Atual_Periodo = date_create()->format('Y-m-d');


        foreach ($sql as $res) {

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
            $modelo_grafico = $res['modelo_grafico'];
            $indicador_unico_regra  = $res['indicador_unico_regra'];
            $periodo_analise_regra = $res['periodo_analise_regra'];

            // print_r($res['id_cockpit']);

   
            switch ($periodo_analise_regra) {
                case 0:
                    $nome_periodo = 'Último Valor Informado';
                    $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-1 days', strtotime($Data_Atual_Periodo)));

                    break;

                case 7:
                    $nome_periodo = 'Dados dos Últimos 7 dias';
                    $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-7 days', strtotime($Data_Atual_Periodo)));
                
                    break;

                    case 15:
                        $nome_periodo = 'Dados dos Últimos 15 dias';
                        $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-15 days', strtotime($Data_Atual_Periodo)));
                       
                        break;

                case 30:
                    $nome_periodo = 'Dados dos Últimos 30 dias';
                    $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-30 days', strtotime($Data_Atual_Periodo)));
                   
                    break;

                    default: 
                    $nome_periodo = "Padrão - Últimos 30 Dias";
                    $Data_Intervalo_Periodo = date('Y-m-d', strtotime('-30 days', strtotime($Data_Atual_Periodo)));
                    break;
            }




            //=========[ Inicio Gráficos Dinâmicos ]=====================================

            if ($modelo_grafico == '1') { // leitura com média de leitura/dia)


                $retorna_dados_g .= " 

                                  <!--begin::Gráfico Modelo 01-->
                                            <div class='col draggable' tabindex='0' id='id_cockpit_$id_cockpit'>

                                            <div class='card bg-light-secondary'>
                
                                                <div class='card-header'>
                                                    <div class='card-title'>
                                                        <h3 class='card-label'> $nome_regra - id $id_cockpit</h3>
                                                    
                                                    </div>
                
                
                                                        <div class='card-toolbar aling-itens-right'>
                                                        <button class='btn  btn-sm btn-link btn-color-info btn-active-color-primary me-5 mb-2 apaga_cockpit' data-nome_regra='$nome_regra' data-id='$id_cockpit' data-kt-menu-trigger='click' data-kt-menu-placement='bottom-end' data-bs-toggle='tooltip' data-bs-placement='top' title='Clique para Eliminar este Monitoramento'>
                                                        <i class='bi bi-x-square fs-3'></i>
                                                    </button>
                                                            <a href='#' class='btn btn-sm btn-link btn-color-muted btn-active-color-primary me-5 mb-2 draggable-handle' data-bs-toggle='tooltip' data-bs-placement='top' title='Clique para Mover este Item'>
                                                                <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                                                                <span class='svg-icon svg-icon-2x'>
                                                                <svg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                                <path opacity='0.3' d='M2 9.09998V3C2 2.4 2.4 2 3 2H9.10001L2 9.09998ZM22 9.09998V3C22 2.4 21.6 2 21 2H14.9L22 9.09998ZM2 14.9V21C2 21.6 2.4 22 3 22H9.10001L2 14.9ZM14.9 22H21C21.6 22 22 21.6 22 21V14.9L14.9 22Z' fill='currentColor'/>
                                                                <path d='M19.2 17.8L13.4 12L19.2 6.20001L17.8 4.79999L12 10.6L6.2 4.79999L4.8 6.20001L10.6 12L4.8 17.8L6.2 19.2L12 13.4L17.8 19.2L19.2 17.8Z' fill='currentColor'/>
                                                                </svg>
                                                                </span>
                                                                <!--end::Svg Icon-->
                                                            </a>
                                                        </div>
                
                
                                                </div>
                
                                                    <!--begin::Body-->
                                                      <!--begin::Menu-->
                                                                    
                                                    <div  class='card-body'>
                                                        <!--begin::Hidden-->
                                                      
                                                        <!--end::Hidden-->
                                                        <!--begin::Chart-->
                                                        <div class='mixed-widget-10-chart modelo_grafico' data-kt-color='primary'
                                                            style='height: 175px; min-height: 183px;' id='modelo_grafico_$id_cockpit'>
                
                                                        </div>
                                                        <!--end::Chart-->
                                                    </div>
                
                                                    <div class='card-footer'>
                                                   
                                                    <span class='fw-bold fs-8 text-primary'>$nome_obra - $nome_estacao</span>
                                                    <span class='text-gray-700 fs-8  fw-bold'>$nome_periodo</span>
                                                </div>
                
                                                </div>
                
                
                                            </div>
                            <!--end::Gráfico Modelo 01-->";	


                            $retorna_dados_g .= " 

 <script type='text/javascript'>



function item_$id_cockpit() {

    $.getJSON('../../crud/dashboard/cockpit/consulta-dados-itens-cockpit-usuario.php?id_cockpit=$id_cockpit&modelo_grafico=1&periodo=$periodo_analise_regra', function (objects) {

//construir primeiro todas as datas de leitura (categories) existentes
const categories = [];
for (let obj of objects){ 
    if (!categories.includes(obj.categories)){
        categories.push(obj.categories);
    }
}

//agrupar os valores para as series com reduce
const series = objects.reduce((acc, val) => {
    let index = acc.map((o) => o.name).indexOf(val.name); //posicao do Indicador
    let categoryIndex = categories.indexOf(val.categories); //posicao da data leitura

    if (index === -1){ //se não existe 
        let newSeries = {
            name: val.name,
            data: new Array(categories.length).fill().map(() => Array(0))
            
        }; //novo objeto já com um array de data todo zerados
        
        //coloca o valor na posição correspondente à categoria/data-leitura
        newSeries.data[categoryIndex] = val.data; 
 
        acc.push(newSeries); //e adiciona o novo objeto à serie
    }
    else { 
        acc[index].data[categoryIndex] = val.data; //troca só o valor da data
        
    }

    return acc;
}, []); //inicia o reduce com array vazio

//console.log(series, categories);


              
// Class definition

let chart_$id_cockpit = function () {
    var chart = {
        self: null,
        rendered: false
    };

    // Private methods
    var initChart = function(chart) {
        var element = document.getElementById('modelo_grafico_$id_cockpit');

        if (!element) {
            return;
        }
        
        var height = parseInt(KTUtil.css(element, 'height'));
        var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
        var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');
        var baseprimaryColor = KTUtil.getCssVariableValue('--kt-primary');
        var lightprimaryColor = KTUtil.getCssVariableValue('--kt-primary');
        var basesuccessColor = KTUtil.getCssVariableValue('--kt-success');
        var lightsuccessColor = KTUtil.getCssVariableValue('--kt-success');
        var colors = ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA', '#ffa500', '#0000ff', '#00FF00', '#D2691E', '#C71585', '#F0E68C', '#FF4500', '#D8BFD8', '#FF0000', '#800000', '#7B68EE', '#4B0082', '#A0522D', '#556B2F'];

        var options = {
            series: series,
            chart: {
                fontFamily: 'inherit',
                type: 'area',
                height: 250,
                toolbar: {
                    show: true
                }
            },
            plotOptions: {

            },
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.2,
                    stops: [15, 120, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 3,
                colors: colors
            },
            xaxis: {
                categories: categories,
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: true
                },
                tickAmount: 5,
                labels: {
                    rotate: 0,
                    rotateAlways: true,
                    style: {
                        colors: labelColor,
                        fontSize: '8px'
                    }
                },
                crosshairs: {
                    position: 'front',
                    stroke: {
                        color: colors,
                        width: 1,
                        dashArray: 3
                    }
                },
                tooltip: {
                    enabled: true,
                    formatter: undefined,
                    offsetY: 0,
                    style: {
                        fontSize: '12px'
                    },    marker: {
                        show: true,
                    }
                }
            },
            yaxis: {
                
                tickAmount: 6,
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '10px'
                    } 
                }
            },
            states: {
                normal: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                hover: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                active: {
                    allowMultipleDataPointsSelection: false,
                    filter: {
                        type: 'none',
                        value: 0
                    }
                }
            },
            tooltip: {
                
                style: {
                    fontSize: '12px'
                },
            },
            colors: colors,
         grid: {
                        borderColor: borderColor,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        },
                        yaxis: {
                            lines: {
                                show: true
                            }
                        },
                        strokeDashArray: 2
                    } ,
            markers: {
                strokeColor: colors,
                strokeWidth: 3
            }
        };

        chart.self = new ApexCharts(element, options);

        // Set timeout to properly get the parent elements width
        setTimeout(function() {
            chart.self.render();
            chart.rendered = true;
        }, 200);      
    }

    // Public methods
    return {
        init: function () {
            initChart(chart);

            // Update chart on theme mode change
            KTThemeMode.on('kt.thememode.change', function() {                
                if (chart.rendered) {
                    chart.self.destroy();
                }

                initChart(chart);
            });
        }   
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = chart_$id_cockpit;
}

// On document ready
KTUtil.onDOMContentLoaded(function() {
    chart_$id_cockpit.init();
});

        });//fecha gtjson data
        
        
} // fecha function

  item_$id_cockpit();
/*
 var nIntervId;

 if (!nIntervId) {
    nIntervId = setInterval(item_$id_cockpit, 5000);
  }


  

  setInterval(function () {
          item_$id_cockpit();
        }, 50000); // verifica a cada 5 segundos

        */

setTimeout(() => {
        if ($('#modelo_grafico_$id_cockpit').is(':empty')){


  //seu codigo
  console.log('sem dados');
  $('#modelo_grafico_$id_cockpit').html('\<div class=\'alert alert-warning d-flex align-items-center p-15 mb-20\'><!--begin::Svg Icon | path: icons/duotune/general/gen048.svg--><span class=\'svg-icon svg-icon-2hx svg-icon-warning me-4\'><svg width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'><path opacity=\'0.3\' d=\'M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z\' fill=\'currentColor\'></path><path d=\'M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z\' fill=\'currentColor\'></path></svg></span><!--end::Svg Icon--><div class=\'d-flex flex-column\'><h4 class=\'mb-1 text-warning\'>Sem dados para exibir</h4><span>Os dados selecionados para este gráfico, não foram localizados no período selecionado.</span></div></div>');

}
  
}, 300);



 </script>";
            } // fecha foreach modelo grafico tipo 1




            if ($modelo_grafico == '2') { // leitura completa com dia, mês e horário detalhado)



                $retorna_dados_g .= " 
                       
                                             <!--begin::Gráfico Modelo 01-->
                                             <div class='col draggable' tabindex='0' id='id_cockpit_$id_cockpit'>

                                             <div class='card bg-light-secondary'>
                 
                                                 <div class='card-header'>
                                                     <div class='card-title'>
                                                     <h3 class='card-label'> $nome_regra - id $id_cockpit</h3>
                                                     
                                                     </div>
                 
                 
                                                         <div class='card-toolbar aling-itens-right'>
                                                         <button class='btn  btn-sm btn-link btn-color-info btn-active-color-primary me-5 mb-2 apaga_cockpit' data-nome_regra='$nome_regra' data-id='$id_cockpit' data-kt-menu-trigger='click' data-kt-menu-placement='bottom-end' data-bs-toggle='tooltip' data-bs-placement='top' title='Clique para Eliminar este Monitoramento'>
                                                         <i class='bi bi-x-square fs-3'></i>
                                                     </button>
                                                             <a href='#' class='btn btn-sm btn-link btn-color-muted btn-active-color-primary me-5 mb-2 draggable-handle' data-bs-toggle='tooltip' data-bs-placement='top' title='Clique para Mover este Item'>
                                                                 <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                                                                 <span class='svg-icon svg-icon-2x'>
                                                                 <svg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                                 <path opacity='0.3' d='M2 9.09998V3C2 2.4 2.4 2 3 2H9.10001L2 9.09998ZM22 9.09998V3C22 2.4 21.6 2 21 2H14.9L22 9.09998ZM2 14.9V21C2 21.6 2.4 22 3 22H9.10001L2 14.9ZM14.9 22H21C21.6 22 22 21.6 22 21V14.9L14.9 22Z' fill='currentColor'/>
                                                                 <path d='M19.2 17.8L13.4 12L19.2 6.20001L17.8 4.79999L12 10.6L6.2 4.79999L4.8 6.20001L10.6 12L4.8 17.8L6.2 19.2L12 13.4L17.8 19.2L19.2 17.8Z' fill='currentColor'/>
                                                                 </svg>
                                                                 </span>
                                                                 <!--end::Svg Icon-->
                                                             </a>
                                                         </div>
                 
                 
                                                 </div>
                 
                                                     <!--begin::Body-->
                                                       <!--begin::Menu-->
                                                                     
                                                     <div  class='card-body'>
                                                         <!--begin::Hidden-->
                                                       
                                                         <!--end::Hidden-->
                                                         <!--begin::Chart-->
                                                         <div class='mixed-widget-10-chart modelo_grafico' data-kt-color='primary'
                                                             style='height: 175px; min-height: 183px;' id='modelo_grafico_$id_cockpit'>
                 
                                                         </div>
                                                         <!--end::Chart-->
                                                     </div>
                 
                                                     <div class='card-footer'>
                                                    
                                                     <span class='fw-bold fs-8 text-primary'>$nome_obra - $nome_estacao</span>
                                                     <span class='text-gray-700 fs-8  fw-bold'>$nome_periodo</span>
                                                 </div>
                 
                                                 </div>
                 
                 
                                             </div>
                            <!--end::Gráfico Modelo 02-->";

                            $retorna_dados_g .= " 

<script type='text/javascript'>


function item_$id_cockpit() {

    $.getJSON('../../crud/dashboard/cockpit/consulta-dados-itens-cockpit-usuario.php?id_cockpit=$id_cockpit&modelo_grafico=2&periodo=$periodo_analise_regra', function (objects) {

        var minLimit = objects.min_limit;
        var maxLimit = objects.max_limit;

//construir primeiro todas as datas de leitura (categories) existentes
var categories = [];
for (let obj of objects){ 
    if (!categories.includes(obj.categories)){
        categories.push(obj.categories);
    }
}


var minLimit = [];
for (let obj of objects){ 
    if (!minLimit.includes(obj.min_limit)){
        minLimit.push(obj.min_limit);
    }
}


var maxLimit = [];
for (let obj of objects){ 
    if (!maxLimit.includes(obj.max_limit)){
        maxLimit.push(obj.max_limit);
    }
}




//agrupar os valores para as series com reduce
var series = objects.reduce((acc, val) => {
    let index = acc.map((o) => o.name).indexOf(val.name); //posicao do Indicador
    let categoryIndex = categories.indexOf(val.categories); //posicao da data leitura

    if (index === -1){ //se não existe 
        let newSeries = {
            name: val.name,
            data: new Array(categories.length).fill().map(() => Array(0))
        }; //novo objeto já com um array de data todo zerados
        
        //coloca o valor na posição correspondente à categoria/data-leitura
        newSeries.data[categoryIndex] = val.data; 
        acc.push(newSeries); //e adiciona o novo objeto à serie
    }
    else { 
        acc[index].data[categoryIndex] = val.data; //troca só o valor da data
    }

    return acc;
}, []); //inicia o reduce com array vazio

console.log('limite min'+ minLimit);
console.log(maxLimit)




          
// Class definition

let chart_$id_cockpit = function () {
    var chart = {
        self: null,
        rendered: false
    };

    // Private methods
    var initChart = function(chart) {
        var element = document.getElementById('modelo_grafico_$id_cockpit');

        if (!element) {
            return;
        }
        
        var height = parseInt(KTUtil.css(element, 'height'));
        var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
        var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');
        var baseprimaryColor = KTUtil.getCssVariableValue('--kt-primary');
        var lightprimaryColor = KTUtil.getCssVariableValue('--kt-primary');
        var basesuccessColor = KTUtil.getCssVariableValue('--kt-success');
        var lightsuccessColor = KTUtil.getCssVariableValue('--kt-success');
        var colors = ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA', '#ffa500', '#0000ff', '#00FF00', '#D2691E', '#C71585', '#F0E68C', '#FF4500', '#D8BFD8', '#FF0000', '#800000', '#7B68EE', '#4B0082', '#A0522D', '#556B2F'];

        var options = {
            series: series,
            chart: {
              height: 250,
              type: 'line',
              toolbar: {
                show: true
              }
            },
            colors: colors,
            dataLabels: {
              enabled: true,
            },
            stroke: {
              curve: 'smooth'
            },
            legend: {
              show: false
            },
            xaxis: {
              categories: categories,
              axisBorder: {
                show: false,
              },
              axisTicks: {
                show: true
              },
              tickAmount: 5,
              labels: {
                rotate: 0,
                rotateAlways: true,
                style: {
                  colors: labelColor,
                  fontSize: '8px'
                }
              },
              crosshairs: {
                position: 'front',
                stroke: {
                  color: colors,
                  width: 1,
                  dashArray: 3
                }
              },
              tooltip: {
                enabled: true,
                formatter: undefined,
                offsetY: 0,
                fillSeriesColor: true,
                style: {
                  fontSize: '12px'
                },
                onDatasetHover: {
                  highlightDataSeries: true,
                },
                marker: {
                  show: true,
                }
              }
            },
            annotations: {
              yaxis: [
                {
                  y: maxLimit,
                  borderColor: '#00E396',
                  offsetX: 30,
                     offsetY: -3,
                  label: {
                    borderColor: '#00E396',
                    position: 'right',
                    offsetX: 9,
                    offsetY: -3,
                    
                    style: {
                      color: '#fff',
                      background: '#00E396'
                    },
                    text: 'Máximo: '+maxLimit
                  }
                },
                {
                  y: minLimit,
                  borderColor: '#E91E63',
                  offsetX: 15,
                    offsetY: -3,
                  label: {
                    borderColor: '#E91E63',
                    position: 'left',
                    offsetX: 15,
                    offsetY: -3,
                    style: {
                      color: '#fff',
                      background: '#E91E63'
                    },
                    text: 'Mínimo: '+minLimit
                  }
                }
              ]
            },
            grid: {
              borderColor: borderColor,
              xaxis: {
                lines: {
                  show: true
                }
              },
              yaxis: {
                lines: {
                  show: true
                }
              },
              strokeDashArray: 2
            },
            markers: {
              size: 1
            }
          };

        chart.self = new ApexCharts(element, options);

        // Set timeout to properly get the parent elements width
        setTimeout(function() {
            chart.self.render();
            chart.rendered = true;
        }, 200);      
    }

    // Public methods
    return {
        init: function () {
            initChart(chart);

            // Update chart on theme mode change
            KTThemeMode.on('kt.thememode.change', function() {                
                if (chart.rendered) {
                    chart.self.destroy();
                }

                initChart(chart);
            });
        }   
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = chart_$id_cockpit;
}

// On document ready
KTUtil.onDOMContentLoaded(function() {
    chart_$id_cockpit.init();
});

        });//fecha gtjson data
        





        
} // fecha function

 item_$id_cockpit();

/*
 var nIntervId;

 if (!nIntervId) {
    nIntervId = setInterval(item_$id_cockpit, 5000);
  }
*/

setTimeout(() => {
        if ($('#modelo_grafico_$id_cockpit').is(':empty')){


  //seu codigo
  console.log('sem dados');
  $('#modelo_grafico_$id_cockpit').html('\<div class=\'alert alert-warning d-flex align-items-center p-15 mb-20\'><!--begin::Svg Icon | path: icons/duotune/general/gen048.svg--><span class=\'svg-icon svg-icon-2hx svg-icon-warning me-4\'><svg width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'><path opacity=\'0.3\' d=\'M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z\' fill=\'currentColor\'></path><path d=\'M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z\' fill=\'currentColor\'></path></svg></span><!--end::Svg Icon--><div class=\'d-flex flex-column\'><h4 class=\'mb-1 text-warning\'>Sem dados para exibir</h4><span>Os dados selecionados para este gráfico, não foram localizados no período selecionado.</span></div></div>');

}
  
}, 300);

    </script>";
            } // fecha foreach modelo grafico tipo 2


            if ($modelo_grafico == '3') {

                $retorna_dados_g .= " 
                      
                                             <!--begin::Gráfico Modelo 01-->
                                             <div class='col draggable' tabindex='0' id='id_cockpit_$id_cockpit'>

                                             <div class='card bg-light-secondary'>
                 
                                                 <div class='card-header'>
                                                     <div class='card-title'>
                                                     <h3 class='card-label'> $nome_regra - id $id_cockpit</h3>
                                                     
                                                     </div>
                 
                 
                                                         <div class='card-toolbar aling-itens-right'>
                                                         <button class='btn  btn-sm btn-link btn-color-info btn-active-color-primary me-5 mb-2 apaga_cockpit' data-nome_regra='$nome_regra' data-id='$id_cockpit' data-kt-menu-trigger='click' data-kt-menu-placement='bottom-end' data-bs-toggle='tooltip' data-bs-placement='top' title='Clique para Eliminar este Monitoramento'>
                                                         <i class='bi bi-x-square fs-3'></i>
                                                     </button>
                                                             <a href='#' class='btn btn-sm btn-link btn-color-muted btn-active-color-primary me-5 mb-2 draggable-handle' data-bs-toggle='tooltip' data-bs-placement='top' title='Clique para Mover este Item'>
                                                                 <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                                                                 <span class='svg-icon svg-icon-2x'>
                                                                 <svg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                                 <path opacity='0.3' d='M2 9.09998V3C2 2.4 2.4 2 3 2H9.10001L2 9.09998ZM22 9.09998V3C22 2.4 21.6 2 21 2H14.9L22 9.09998ZM2 14.9V21C2 21.6 2.4 22 3 22H9.10001L2 14.9ZM14.9 22H21C21.6 22 22 21.6 22 21V14.9L14.9 22Z' fill='currentColor'/>
                                                                 <path d='M19.2 17.8L13.4 12L19.2 6.20001L17.8 4.79999L12 10.6L6.2 4.79999L4.8 6.20001L10.6 12L4.8 17.8L6.2 19.2L12 13.4L17.8 19.2L19.2 17.8Z' fill='currentColor'/>
                                                                 </svg>
                                                                 </span>
                                                                 <!--end::Svg Icon-->
                                                             </a>
                                                         </div>
                 
                 
                                                 </div>
                 
                                                     <!--begin::Body-->
                                                       <!--begin::Menu-->
                                                                     
                                                     <div  class='card-body'>
                                                         <!--begin::Hidden-->
                                                       
                                                         <!--end::Hidden-->
                                                         <!--begin::Chart-->
                                                         <div class='mixed-widget-10-chart modelo_grafico' data-kt-color='primary'
                                                             style='height: 175px; min-height: 183px;' id='modelo_grafico_$id_cockpit'>
                 
                                                         </div>
                                                         <!--end::Chart-->
                                                     </div>
                 
                                                     <div class='card-footer'>
                                                    
                                                     <span class='fw-bold fs-8 text-primary'>$nome_obra - $nome_estacao</span>
                                                     <span class='text-gray-700 fs-8  fw-bold'>$nome_periodo</span>
                                                 </div>
                 
                                                 </div>
                 
                 
                                             </div>
                            <!--end::Gráfico Modelo 03-->";


                            $retorna_dados_g .= " 


<script type='text/javascript'>


function item_$id_cockpit() {

    $.getJSON('../../crud/dashboard/cockpit/consulta-dados-itens-cockpit-usuario.php?id_cockpit=$id_cockpit&modelo_grafico=3&periodo=$periodo_analise_regra', function (objects) {


//construir primeiro todas as datas de leitura (categories) existentes
const categories = [];
for (let obj of objects){ 
    if (!categories.includes(obj.categories)){
        categories.push(obj.categories);
    }
}

//agrupar os valores para as series com reduce
const series = objects.reduce((acc, val) => {
    let index = acc.map((o) => o.name).indexOf(val.name); //posicao do Indicador
    let categoryIndex = categories.indexOf(val.categories); //posicao da data leitura

    if (index === -1){ //se não existe 
        let newSeries = {
            name: val.name,
            data: new Array(categories.length).fill().map(() => Array(0))
        }; //novo objeto já com um array de data todo zerados
        
        //coloca o valor na posição correspondente à categoria/data-leitura
        newSeries.data[categoryIndex] = val.data; 
        acc.push(newSeries); //e adiciona o novo objeto à serie
    }
    else { 
        acc[index].data[categoryIndex] = val.data; //troca só o valor da data
    }

    return acc;
}, []); //inicia o reduce com array vazio

//console.log(series, categories);
//console.log(series)


// Class definition

let chart_$id_cockpit = function () {
    var chart = {
        self: null,
        rendered: false
    };

    // Private methods
    var initChart = function(chart) {
        var element = document.getElementById('modelo_grafico_$id_cockpit');

        if (!element) {
            return;
        }
        
      var height = parseInt(KTUtil.css(element, 'height'));
        var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
        var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');
        var baseprimaryColor = KTUtil.getCssVariableValue('--kt-primary');
        var lightprimaryColor = KTUtil.getCssVariableValue('--kt-primary');
        var basesuccessColor = KTUtil.getCssVariableValue('--kt-success');
        var lightsuccessColor = KTUtil.getCssVariableValue('--kt-success');
        var colors = ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA', '#ffa500', '#0000ff', '#00FF00', '#D2691E', '#C71585', '#F0E68C', '#FF4500', '#D8BFD8', '#FF0000', '#800000', '#7B68EE', '#4B0082', '#A0522D', '#556B2F'];

        var options = {
          series:series,
          chart: {
          height: 250,
          type: 'bar'
        },
        colors: colors,
        dataLabels: {
          enabled: false,
        },
        stroke: {
          curve: 'smooth'
        },           
         legend: {
                show: false
            },
     				plotOptions: {
					bar: {
						borderRadius: 4,
						columnWidth: '50%',
					}
				},
            xaxis: {
                categories: categories,
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: true
                },
                tickAmount: 5,
                labels: {
                    rotate: 0,
                    rotateAlways: true,
                    style: {
                        colors: labelColor,
                        fontSize: '8px'
                    }
                },
                crosshairs: {
                    position: 'front',
                    stroke: {
                        color: colors,
                        width: 1,
                        dashArray: 3
                    }
                },
               tooltip: {
                    enabled: true,
                    formatter: undefined,
                    offsetY: 0,
                    style: {
                        fontSize: '12px'
                    }
                } ,   marker: {
                        show: true,
                    }
            },
                   yaxis: {
                
                tickAmount: 6,
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '10px'
                    } 
                }
            },
                 grid: {
                        borderColor: borderColor,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        },
                        yaxis: {
                            lines: {
                                show: true
                            }
                        },
                        strokeDashArray: 2
                    } ,
        markers: {
          size: 1
        }
       
        
        };

        chart.self = new ApexCharts(element, options);

        // Set timeout to properly get the parent elements width
        setTimeout(function() {
            chart.self.render();
            chart.rendered = true;
        }, 200);      
    }

    // Public methods
    return {
        init: function () {
            initChart(chart);

            // Update chart on theme mode change
            KTThemeMode.on('kt.thememode.change', function() {                
                if (chart.rendered) {
                    chart.self.destroy();
                }

                initChart(chart);
            });
        }   
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = chart_$id_cockpit;
}

// On document ready
KTUtil.onDOMContentLoaded(function() {
    chart_$id_cockpit.init();
});



        });//fecha gtjson data
        





        
} // fecha function

  item_$id_cockpit();
/*
 var nIntervId;

 if (!nIntervId) {
    nIntervId = setInterval(item_$id_cockpit, 5000);
  }
*/

setTimeout(() => {
        if ($('#modelo_grafico_$id_cockpit').is(':empty')){


  //seu codigo
  console.log('sem dados');
  $('#modelo_grafico_$id_cockpit').html('\<div class=\'alert alert-warning d-flex align-items-center p-15 mb-20\'><!--begin::Svg Icon | path: icons/duotune/general/gen048.svg--><span class=\'svg-icon svg-icon-2hx svg-icon-warning me-4\'><svg width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'><path opacity=\'0.3\' d=\'M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z\' fill=\'currentColor\'></path><path d=\'M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z\' fill=\'currentColor\'></path></svg></span><!--end::Svg Icon--><div class=\'d-flex flex-column\'><h4 class=\'mb-1 text-warning\'>Sem dados para exibir</h4><span>Os dados selecionados para este gráfico, não foram localizados no período selecionado.</span></div></div>');

}
  
}, 300);
    </script>";
            } // fecha modelo grafico tipo 3



            // inicia modelo 4

            if ($modelo_grafico == '4') {

                $retorna_dados_g .= " 
                      
                                            <!--begin::Gráfico Modelo 01-->
                                            <div class='col draggable' tabindex='0' id='id_cockpit_$id_cockpit'>

                                            <div class='card bg-light-secondary'>
                
                                                <div class='card-header'>
                                                    <div class='card-title'>
                                                    <h3 class='card-label'> $nome_regra - id $id_cockpit</h3>
                                                    
                                                    </div>
                
                
                                                        <div class='card-toolbar aling-itens-right'>
                                                        <button class='btn  btn-sm btn-link btn-color-info btn-active-color-primary me-5 mb-2 apaga_cockpit' data-nome_regra='$nome_regra' data-id='$id_cockpit' data-kt-menu-trigger='click' data-kt-menu-placement='bottom-end' data-bs-toggle='tooltip' data-bs-placement='top' title='Clique para Eliminar este Monitoramento'>
                                                        <i class='bi bi-x-square fs-3'></i>
                                                    </button>
                                                            <a href='#' class='btn btn-sm btn-link btn-color-muted btn-active-color-primary me-5 mb-2 draggable-handle' data-bs-toggle='tooltip' data-bs-placement='top' title='Clique para Mover este Item'>
                                                                <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                                                                <span class='svg-icon svg-icon-2x'>
                                                                <svg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                                <path opacity='0.3' d='M2 9.09998V3C2 2.4 2.4 2 3 2H9.10001L2 9.09998ZM22 9.09998V3C22 2.4 21.6 2 21 2H14.9L22 9.09998ZM2 14.9V21C2 21.6 2.4 22 3 22H9.10001L2 14.9ZM14.9 22H21C21.6 22 22 21.6 22 21V14.9L14.9 22Z' fill='currentColor'/>
                                                                <path d='M19.2 17.8L13.4 12L19.2 6.20001L17.8 4.79999L12 10.6L6.2 4.79999L4.8 6.20001L10.6 12L4.8 17.8L6.2 19.2L12 13.4L17.8 19.2L19.2 17.8Z' fill='currentColor'/>
                                                                </svg>
                                                                </span>
                                                                <!--end::Svg Icon-->
                                                            </a>
                                                        </div>
                
                
                                                </div>
                
                                                    <!--begin::Body-->
                                                      <!--begin::Menu-->
                                                                    
                                                    <div  class='card-body'>
                                                        <!--begin::Hidden-->
                                                      
                                                        <!--end::Hidden-->
                                                        <!--begin::Chart-->
                                                        <div class='mixed-widget-10-chart modelo_grafico' data-kt-color='primary'
                                                            style='height: 175px; min-height: 183px;' id='modelo_grafico_$id_cockpit'>
                
                                                        </div>
                                                        <!--end::Chart-->
                                                    </div>
                
                                                    <div class='card-footer'>
                                                   
                                                    <span class='fw-bold fs-8 text-primary'>$nome_obra - $nome_estacao</span>
                                                    <span class='text-gray-700 fs-8  fw-bold'>$nome_periodo</span>
                                                </div>
                
                                                </div>
                
                
                                            </div>
                            <!--end::Gráfico Modelo 04-->";


                            $retorna_dados_g .= " 

<script type='text/javascript'>


function item_$id_cockpit() {

    $.getJSON('../../crud/dashboard/cockpit/consulta-dados-itens-cockpit-usuario.php?id_cockpit=$id_cockpit&modelo_grafico=4&periodo=$periodo_analise_regra', function (objects) {


//construir primeiro todas as datas de leitura (categories) existentes
const categories = [];
for (let obj of objects){ 
    if (!categories.includes(obj.categories)){
        categories.push(obj.categories);
    }
}

const acumulado = [];
for (let obj of objects){ 
    if (!acumulado.includes(obj.acumulado)){
        acumulado.push(obj.acumulado);
    }
}

//agrupar os valores para as series com reduce
const series = objects.reduce((acc, val) => {
    let index = acc.map((o) => o.name).indexOf(val.name); //posicao do Indicador
    let categoryIndex = categories.indexOf(val.categories); //posicao da data leitura

    if (index === -1){ //se não existe 
        let newSeries = {
            name: val.name,
            data: new Array(categories.length).fill().map(() => Array(0))
        }; //novo objeto já com um array de data todo zerados
        
        //coloca o valor na posição correspondente à categoria/data-leitura
        newSeries.data[categoryIndex] = val.data; 
        acc.push(newSeries); //e adiciona o novo objeto à serie
    }
    else { 
        acc[index].data[categoryIndex] = val.data; //troca só o valor da data
    }

    return acc;
}, []); //inicia o reduce com array vazio

//console.log(series, categories);
console.log(series)


// Class definition 

let chart_$id_cockpit = function () {
    var chart = {
        self: null,
        rendered: false
    };

    // Private methods
    var initChart = function(chart) {
        var element = document.getElementById('modelo_grafico_$id_cockpit');

        if (!element) {
            return;
        }
        
      var height = parseInt(KTUtil.css(element, 'height'));
        var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
        var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');
        var baseprimaryColor = KTUtil.getCssVariableValue('--kt-primary');
        var lightprimaryColor = KTUtil.getCssVariableValue('--kt-primary');
        var basesuccessColor = KTUtil.getCssVariableValue('--kt-success');
        var lightsuccessColor = KTUtil.getCssVariableValue('--kt-success');
        var colors = ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA', '#ffa500', '#0000ff', '#00FF00', '#D2691E', '#C71585', '#F0E68C', '#FF4500', '#D8BFD8', '#FF0000', '#800000', '#7B68EE', '#4B0082', '#A0522D', '#556B2F'];

        var options = {
          series:series,
          chart: {
          height: 250,
          type: 'line',
          
          toolbar: {
            show: true
          }
        },
        colors: colors,
        dataLabels: {
          enabled: true,
        },
        noData: {
  text: 'Sem Dados',
  align: 'center',
  verticalAlign: 'middle',
  offsetX: 0,
  offsetY: 0,
  
  style: {
   color: '#b6b6b6',
    fontSize: '14px',
    fontFamily: 'Helvetica, Arial, sans-serif',
    fontWeight: 'bold',
  }
},
        stroke: {
          curve: 'smooth'
        },            legend: {
                show: false
            },
     
            xaxis: {                
                 title: {
          text: 'Total Acumulado no Período: ' + acumulado ,
          offsetX: 0,
          offsetY: 0,
           style: {
              color: '#b6b6b6',
              fontSize: '14px',
              fontFamily: 'Helvetica, Arial, sans-serif',
              fontWeight: 'bold',
              cssClass: 'apexcharts-xaxis-title',
          },
        },
                categories: categories,
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: true
                },
                tickAmount: 5,
                labels: {
                    rotate: 0,
                    rotateAlways: true,
                    style: {
                        colors: labelColor,
                        fontSize: '8px'
                    }
                },
                crosshairs: {
                    position: 'front',
                    stroke: {
                        color: colors,
                        width: 1,
                        dashArray: 3
                    }
                },
               tooltip: {
                    enabled: true,
                    formatter: undefined,
                    offsetY: 0,
                    style: {
                        fontSize: '12px'
                    }
                } ,  
                 marker: {
                        show: true,
                    }
            },
                   yaxis: {
                
                tickAmount: 6,
    
                labels: {
                    
                    style: {
                        colors: labelColor,
                        fontSize: '10px'
                    } 
                }
            },
                 grid: {
                        borderColor: borderColor,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        },
                        yaxis: {
                            lines: {
                                show: true
                            }
                        },
                        strokeDashArray: 2
                    } ,
        markers: {
          size: 2
        }
       
        
        };

        chart.self = new ApexCharts(element, options);

        // Set timeout to properly get the parent elements width
        setTimeout(function() {
            chart.self.render();
            chart.rendered = true;
        }, 200);      
    }

    // Public methods
    return {
        init: function () {
            initChart(chart);

            // Update chart on theme mode change
            KTThemeMode.on('kt.thememode.change', function() {                
                if (chart.rendered) {
                    chart.self.destroy();
                }

                initChart(chart);
            });
        }   
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = chart_$id_cockpit;
}

// On document ready
KTUtil.onDOMContentLoaded(function() {
    chart_$id_cockpit.init();
});



        });//fecha gtjson data
        





        
} // fecha function

  item_$id_cockpit();
/*
 var nIntervId;

 if (!nIntervId) {
    nIntervId = setInterval(item_$id_cockpit, 5000);
  }
*/


setTimeout(() => {
        if ($('#modelo_grafico_$id_cockpit').is(':empty')){


  //seu codigo
  console.log('sem dados');
  $('#modelo_grafico_$id_cockpit').html('\<div class=\'alert alert-warning d-flex align-items-center p-15 mb-20\'><!--begin::Svg Icon | path: icons/duotune/general/gen048.svg--><span class=\'svg-icon svg-icon-2hx svg-icon-warning me-4\'><svg width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'><path opacity=\'0.3\' d=\'M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z\' fill=\'currentColor\'></path><path d=\'M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z\' fill=\'currentColor\'></path></svg></span><!--end::Svg Icon--><div class=\'d-flex flex-column\'><h4 class=\'mb-1 text-warning\'>Sem dados para exibir</h4><span>Os dados selecionados para este gráfico, não foram localizados no período selecionado.</span></div></div>');

}
  
}, 300);

    </script>";
            } // fecha modelo grafico tipo 4 


            // inicia modelo 5

            if ($modelo_grafico == '5') {

                $retorna_dados_g .= " 
                      
                                             <!--begin::Gráfico Modelo 01-->
                            <div class='col draggable' tabindex='0' id='id_cockpit_$id_cockpit'>

                            <div class='card bg-light-secondary'>

                                <div class='card-header'>
                                    <div class='card-title'>
                                    <h3 class='card-label'> $nome_regra - id $id_cockpit</h3>
                                    
                                    </div>


                                        <div class='card-toolbar aling-itens-right'>
                                        <button class='btn  btn-sm btn-link btn-color-info btn-active-color-primary me-5 mb-2 apaga_cockpit' data-nome_regra='$nome_regra' data-id='$id_cockpit' data-kt-menu-trigger='click' data-kt-menu-placement='bottom-end' data-bs-toggle='tooltip' data-bs-placement='top' title='Clique para Eliminar este Monitoramento'>
                                        <i class='bi bi-x-square fs-3'></i>
                                    </button>
                                            <a href='#' class='btn btn-sm btn-link btn-color-muted btn-active-color-primary me-5 mb-2 draggable-handle' data-bs-toggle='tooltip' data-bs-placement='top' title='Clique para Mover este Item'>
                                                <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                                                <span class='svg-icon svg-icon-2x'>
                                                <svg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <path opacity='0.3' d='M2 9.09998V3C2 2.4 2.4 2 3 2H9.10001L2 9.09998ZM22 9.09998V3C22 2.4 21.6 2 21 2H14.9L22 9.09998ZM2 14.9V21C2 21.6 2.4 22 3 22H9.10001L2 14.9ZM14.9 22H21C21.6 22 22 21.6 22 21V14.9L14.9 22Z' fill='currentColor'/>
                                                <path d='M19.2 17.8L13.4 12L19.2 6.20001L17.8 4.79999L12 10.6L6.2 4.79999L4.8 6.20001L10.6 12L4.8 17.8L6.2 19.2L12 13.4L17.8 19.2L19.2 17.8Z' fill='currentColor'/>
                                                </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </a>
                                        </div>


                                </div>

                                    <!--begin::Body-->
                                      <!--begin::Menu-->
                                                    
                                    <div  class='card-body'>
                                        <!--begin::Hidden-->
                                      
                                        <!--end::Hidden-->
                                        <!--begin::Chart-->
                                        <div class='mixed-widget-10-chart modelo_grafico' data-kt-color='primary'
                                            style='height: 175px; min-height: 183px;' id='modelo_grafico_$id_cockpit'>

                                        </div>
                                        <!--end::Chart-->
                                    </div>

                                    <div class='card-footer'>
                                   
                                    <span class='fw-bold fs-8 text-primary'>$nome_obra - $nome_estacao</span>
                                    <span class='text-gray-700 fs-8  fw-bold'>$nome_periodo</span>
                                </div>

                                </div>


                            </div>
                            <!--end::Gráfico Modelo 05-->";


                            $retorna_dados_g .= " 

<script type='text/javascript'>


function item_$id_cockpit() {

    $.getJSON('../../crud/dashboard/cockpit/consulta-dados-itens-cockpit-usuario.php?id_cockpit=$id_cockpit&modelo_grafico=5&periodo=$periodo_analise_regra', function (objects) {

$.each(objects, function(index, valor) {


// Class definition

let chart_$id_cockpit = function () {
    var chart = {
        self: null,
        rendered: false
    };


    console.log('valor leitura='+valor.Leitura);
    
    // Private methods
    var initChart = function(chart) {
        var element = document.getElementById('modelo_grafico_$id_cockpit');

        if (!element) {
            return;
        }
        
      var height = parseInt(KTUtil.css(element, 'height'));
        var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
        var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');
        var baseprimaryColor = KTUtil.getCssVariableValue('--kt-primary');
        var lightprimaryColor = KTUtil.getCssVariableValue('--kt-primary');
        var basesuccessColor = KTUtil.getCssVariableValue('--kt-success');
        var lightsuccessColor = KTUtil.getCssVariableValue('--kt-success');
        var colors = ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA', '#ffa500', '#0000ff', '#00FF00', '#D2691E', '#C71585', '#F0E68C', '#FF4500', '#D8BFD8', '#FF0000', '#800000', '#7B68EE', '#4B0082', '#A0522D', '#556B2F'];

     


var label = valor.nome_Indicador ;
        var options = {
  chart: {
      height: 280,
      type: 'radialBar',
  },
 title: {
      text: label,
      align: 'center',
      margin: 25,
      offsetX: 0,
      offsetY: 0,
      floating: true,
      style: {
        fontSize:  '16px',
        fontWeight:  'bold',
       fontFamily: 'Helvetica, Arial, sans-serif',
        color:  '#9699a2'
      },
  },
  subtitle: {
      text: 'Faixa: '+valor.concen_min +' < > '+valor.concen_max + '\\n | Mais recente: '+valor.Leitura+' '+valor.nome_unidade_medida +' às '+valor.data_leitura,
      align: 'center',
      margin: 25,
      offsetX: 0,
      offsetY: 25,
      floating: true,
      style: {
        fontSize:  '12px',
        fontWeight:  'bold',
       fontFamily: 'Helvetica, Arial, sans-serif',
        color:  '#9699a2'
      },
  },
  series: [valor.Leitura_porcentagem],
  
 colors: ['#20E647'],
 labels:[''],
  plotOptions: {
    radialBar: {
        
      hollow: {
        margin: 0,
        size: '70%',
        background: '#293450'
      },
      track: {
        dropShadow: {
          enabled: true,
          top: 2,
          left: 0,
          blur: 4,
          opacity: 0.15
        }
      },
      responsive: [{
                breakpoint: 480,
                options: {
                  chart: {
                    width: 280
                  },
                  legend: {
                    position: 'bottom'
                  }
                }
              }],
      dataLabels: {
        showOn: 'always',
        distributed: true,
       
        name: {
          offsetY: -40,
          color: '#fff',
          fontSize: '12px'
        },
        value: {
            formatter: function (val) {
            return val +' %'
          },
          color: '#fff',
          fontSize: '32px',
           fontWeight:  'bold',
          show: true,
           offsetY: -0,
        }
      }
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'dark',
      type: 'vertical',
      gradientToColors: ['#ABE5A1'],
      stops: [valor.concen_min, valor.concen_max]
    }
  },

  stroke: {
    lineCap: 'round',
  },
}


        chart.self = new ApexCharts(element, options);

        // Set timeout to properly get the parent elements width
        setTimeout(function() {
            chart.self.render();
            chart.rendered = true;
        }, 200);      
    }

    // Public methods
    return {
        init: function () {
            initChart(chart);

            // Update chart on theme mode change
            KTThemeMode.on('kt.thememode.change', function() {                
                if (chart.rendered) {
                    chart.self.destroy();
                }

                initChart(chart);
            });
        }   
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = chart_$id_cockpit;
}

// On document ready
KTUtil.onDOMContentLoaded(function() {
    chart_$id_cockpit.init();
});


 });
        });//fecha gtjson data
        





        
} // fecha function

  item_$id_cockpit();
/*
 var nIntervId;

 if (!nIntervId) {
    nIntervId = setInterval(item_$id_cockpit, 5000);
  }
*/


setTimeout(() => {
        if ($('#modelo_grafico_$id_cockpit').is(':empty')){


  //seu codigo
  console.log('sem dados');
  $('#modelo_grafico_$id_cockpit').html('\<div class=\'alert alert-warning d-flex align-items-center p-15 mb-20\'><!--begin::Svg Icon | path: icons/duotune/general/gen048.svg--><span class=\'svg-icon svg-icon-2hx svg-icon-warning me-4\'><svg width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'><path opacity=\'0.3\' d=\'M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z\' fill=\'currentColor\'></path><path d=\'M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z\' fill=\'currentColor\'></path></svg></span><!--end::Svg Icon--><div class=\'d-flex flex-column\'><h4 class=\'mb-1 text-warning\'>Sem dados para exibir</h4><span>Os dados selecionados para este gráfico, não foram localizados no período selecionado.</span></div></div>');

}
  
}, 300);
    </script>";
            } // fecha modelo grafico tipo 5            


            //====================[ fim modelo gráfico 5 -> Indicador Único ]======================================

        } // fecha foreach principal



    } else {


        if($projeto_selecionado==''){

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

                            echo '  <div class="alert alert-success d-flex align-items-center p-5 mb-10">
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

    echo $retorna_dados_g;



    echo $retorna_dados;







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