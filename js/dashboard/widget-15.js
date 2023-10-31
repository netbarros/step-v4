"use strict";

// Class definition
var KTChartsWidget15 = function () {
    var chart_Widget15 = {
        self: null,
        rendered: false
    };
    // Private methods
    var initChart = function (chart_Widget15) {
        var element = document.getElementById("kt_charts_widget_15_chart");

        if (!element) {
            return;
        }

         // Adicionando uma mensagem de carregamento ao container do gráfico
         var loadingMessage = document.createElement("div");
         loadingMessage.id = 'loadingMessage_widget_15';
 
         // Criação de spinner e mensagem de carregamento
         loadingMessage.innerHTML = `
         <div class="d-flex align-items-center">
         <div class="spinner-border text-primary " role="status">
         <span class="visually-hidden ">Loading...</span>
         </div>
         <div class="me-10"><span class="text-gray-600 px-3">Criando seu gráfico...</span></div>
     </div>
         `;
 
         element.appendChild(loadingMessage);


        $.getJSON('../../crud/dashboard/consulta-widget-15.php?modelo_grafico=1&periodo=7', function (objects) {


              // Verificando se os dados retornados estão vazios ou contêm a propriedade 'sem_dados' com valor 0
              if (objects.length === 0 || (objects[0].hasOwnProperty('sem_dados') && objects[0].sem_dados === 0)) {
                loadingMessage.innerHTML =  `
                <div class="d-flex align-items-center">
                <i class="bi bi-activity fs-4 text-active-danger"></i>
                <div class="me-10"><span class="text-gray-700 px-3">Gráfico sem Dados registrados nos últimos 7 dias.</span></div>
            </div>
                `;
            } else {
              
             // Removendo a mensagem de carregamento após o recebimento dos dados
             loadingMessage.remove();


            //construir primeiro todas as datas de leitura (categories) existentes
            const total_leitura = [];
            const nome_obra = [];
            const nome_ponto = [];
            const maximo = [];

            for (let obj of objects) {
              
                    total_leitura.push(obj.total_leitura);
             
                if (!maximo.includes(obj.maximo)) {
                    maximo.push(obj.maximo);
                }
              
                    nome_obra.push(obj.nome_obra);
                
            
                    nome_ponto.push(obj.nome_ponto);
              
            }

            var cookie_projeto = KTCookie.get('projeto_atual');

            if (cookie_projeto != undefined) { // se há projeto selecionado, uso o subfiltro das estacoes do projeto

                var nome_exibicao = nome_ponto;

            } else { // se não há projeto selecionado, exibo por projetos

                var nome_exibicao = nome_obra;
            }

            //console.log(series, categories);
            console.log(nome_obra, nome_ponto, total_leitura)

            var height = parseInt(KTUtil.css(element, 'height'));
            var labelColor = KTUtil.getCssVariableValue('--kt-gray-800');
            var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');
            var maxValue = maximo;
            var colors = ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA', '#ffa500', '#0000ff', '#00FF00', '#D2691E', '#C71585', '#F0E68C', '#FF4500', '#D8BFD8', '#FF0000', '#800000', '#7B68EE', '#4B0082', '#A0522D','#556B2F'];

            var options = {
                series: [{
                    name: 'Total',
                    data: total_leitura
                }],
                chart: {
                    fontFamily: 'inherit',
                    height: height,
                    type: 'bar',
                    toolbar: {
                        show: true
                    }
                    
                },
                colors: colors,
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 4,
                        columnWidth: '70%',
                        barHeight: '70%',
                        endingShape: 'rounded',
                        distributed: true,
                        rangeBarOverlap: true,
                        rangeBarGroupRows: true,
                        dataLabels: {
                            position: 'top',
                            maxItems: 50,
                            hideOverflowingLabels: true,
                            orientation: 'horizontal'
                        }
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                legend: {
                    show: false
                },
                colors: colors, 
                xaxis: {
                    categories: nome_exibicao,
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '12px'
                        }

                    }
                },
                yaxis: {
                    forceNiceScale: true,
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '12px'
                        }
                    },

                },
                fill: {
                    opacity: 1
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
                }, tooltip: {
                    style: {
                        fontSize: '12px'
                    },
                    y: {
                        formatter: function (val) {
                            var val = val;
                            var Format = wNumb({
                                //prefix: '$',
                                //suffix: ',-',
                                thousand: '.'
                            });

                            return Format.to(val);
                        }
                    }
                },
                colors: colors,
                grid: {
                    borderColor: borderColor,
                    strokeDashArray: 4,
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                }
            };

            chart_Widget15.self = new ApexCharts(element, options);





            // Set timeout to properly get the parent elements width
            setTimeout(function () {
                chart_Widget15.self.render();
                chart_Widget15.rendered = true;
            }, 200);


            }

        });//fecha gtjson data    
    }

    // Public methods
    return {
        init: function () {
            initChart(chart_Widget15);

            // Update chart on theme mode change
            KTThemeMode.on("kt.thememode.change", function () {
                if (chart_Widget15.rendered) {
                    chart_Widget15.self.destroy();
                }

                initChart(chart_Widget15);
            });
        }
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = KTChartsWidget15;
}

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTChartsWidget15.init();
});


