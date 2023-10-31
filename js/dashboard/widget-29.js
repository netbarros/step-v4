"use strict";

//Projetos com (-)Leituras

// Class definition
var KTChartsWidget29 = function () {
    var chart_Widget29 = {
        self: null,
        rendered: true
    };
    // Private methods
    var initChart = function(chart_Widget29) {
        var element = document.getElementById("kt_charts_widget_29"); 

        if (!element) {
            return;S
        }

              // Adicionando uma mensagem de carregamento ao container do gráfico
              var loadingMessage = document.createElement("div");
              loadingMessage.id = 'loadingMessage_widget_29';
      
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
        
        $.getJSON('../../crud/dashboard/consulta-widget-29.php?modelo_grafico=1&periodo=30', function (objects) {



            // Verificando se os dados retornados estão vazios ou contêm a propriedade 'sem_dados' com valor 0
            if (objects.length === 0 || (objects[0].hasOwnProperty('sem_dados') && objects[0].sem_dados === 0)) {
                loadingMessage.innerHTML =  `
                <div class="d-flex align-items-center">
                <i class="bi bi-activity fs-4 text-active-danger"></i>
                <div class="me-10"><span class="text-gray-700 px-3">Gráfico sem Dados para exibir no período Selecionado</span></div>
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
            console.log(nome_obra, nome_exibicao, total_leitura)

            var height = parseInt(KTUtil.css(element, 'height'));
            var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');   
            var borderColor = KTUtil.getCssVariableValue('--kt-gray-200');
            var colors = ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA', '#ffa500', '#0000ff', '#00FF00', '#D2691E', '#C71585', '#F0E68C', '#FF4500', '#D8BFD8', '#FF0000', '#800000', '#7B68EE', '#4B0082', '#A0522D', '#556B2F'];
            var baseColor = KTUtil.getCssVariableValue('--kt-warning');
            var secondaryColor = KTUtil.getCssVariableValue('--kt-gray-300');
            var maxValue = maximo;
        
        var options = {
            series: [{
                name: 'Leituras',
                data: total_leitura                                                                                                             
            }],           
            chart: {
                fontFamily: 'inherit',
                type: 'bar',
                height: 550,
                toolbar: {
                    show: false
                }                             
            },                    
            plotOptions: {
                bar: {
                    horizontal: true,
                    s̶t̶a̶r̶t̶i̶n̶g̶S̶h̶a̶p̶e̶: 'flat',
                    e̶n̶d̶i̶n̶g̶S̶h̶a̶p̶e̶: 'flat',
                    borderRadius: 0,
                    columnWidth: '70%',
                    barHeight: '70%',
                    distributed: false,
                    rangeBarOverlap: true,
                    rangeBarGroupRows: false,
                    colors: {
                        ranges: [{
                            from: 0,
                            to: 0,
                            color: colors
                        }],
                        backgroundBarColors: [],
                        backgroundBarOpacity: 1,
                        backgroundBarRadius: 0,
                    },
                    dataLabels: {
                        position: 'top',
                        maxItems: 100,
                        hideOverflowingLabels: true
                    }
                }
            },

            dataLabels: {
                enabled: true
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
            },
            tooltip: {
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
          
        chart_Widget29.self = new ApexCharts(element, options);
           
            // Set timeout to properly get the parent elements width
            setTimeout(function () {
                chart_Widget29.self.render();
                chart_Widget29.rendered = true;
            }, 200);



 
       

            }

        });//fecha gtjson data    
    }

    // Public methods
    return {
        init: function () {
            initChart(chart_Widget29);

            // Update chart on theme mode change
            KTThemeMode.on("kt.thememode.change", function() {                
                if (chart_Widget29.rendered) {
                    chart_Widget29.self.destroy();
                }

            
            });
        }   
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = KTChartsWidget29;
}



// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTChartsWidget29.init();
});






 