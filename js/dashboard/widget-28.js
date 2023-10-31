"use strict";

// Class definition
var KTChartsWidget28 = function () {
    var chartWidget28 = {
        self: null,
        rendered: true
    };
    
    // Private methods
    var initChart = function(chartWidget28) {
        var element = document.getElementById("kt_charts_widget_28");

        if (!element) {
            if (chartWidget28.rendered) {
                chartWidget28.self.destroy();
              
            }
        }


             // Adicionando uma mensagem de carregamento ao container do gráfico
             var loadingMessage = document.createElement("div");
             loadingMessage.id = 'loadingMessage_widget_28';
     
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

        $.getJSON('../../crud/dashboard/consulta-widget-28.php?modelo_grafico=1&periodo=180', function (objects) {


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
            const data_leitura = [];
            const total_leitura = [];


            for (let obj of objects) {
              
                    total_leitura.push(obj.total_leitura);
               
              
                    data_leitura.push(obj.data_leitura);
               

            }

            console.log(data_leitura, total_leitura)

        var height = parseInt(KTUtil.css(element, 'height'));
        var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
        var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');
        var baseColor = KTUtil.getCssVariableValue('--kt-info');         

        var options = {
            series: [{
                name: 'Leituras',
                data: total_leitura
            }],            
            chart: {
                fontFamily: 'inherit',
                type: 'area',
                height: height,
                toolbar: {
                    show: false
                }
            },            
            legend: {
                show: true
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    var val = val;
                    var Format = wNumb({
                        //prefix: '$',
                        //suffix: ',-',
                        thousand: '.'
                    });
                    return Format.to(val);
                }
            },
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0,
                    stops: [0, 80, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 3,
                colors: [baseColor]
            },
            xaxis: {
                categories: data_leitura,
                axisBorder: {
                    show: true,
                },
               
                axisTicks: {
                    show: true
                },
                tickAmount: 6,
                labels: {
                    labels: {
                        formatter: function (val) {
                            var val = val;
                            var Format = wNumb({
                                //prefix: '$',
                                //suffix: ',-',
                                thousand: '.'
                            });
                            return Format.to(val);
                        },
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    }
                },
                    rotate: 0,
                    rotateAlways: false,
                    style: {
                        colors: labelColor,
                        fontSize: '12px'                        
                    }
                },
                crosshairs: {
                    position: 'front',
                    stroke: {
                        color: baseColor,
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
                }
            },
            yaxis: {
                tickAmount: 6,
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    },
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
            colors: [baseColor],
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            markers: {
                strokeColor: baseColor,
                strokeWidth: 3
            }
        };

        chartWidget28.self = new ApexCharts(element, options);

       

        // Set timeout to properly get the parent elements width
        setTimeout(function() {
            chartWidget28.self.render();
            chartWidget28.rendered = true;

            
            
        }, 200); 
        
        
    }  

        });//fecha gtjson data 
        
        
    }

    // Public methods    chart.updateOptions(options);
    return {
        init: function () {
            initChart(chartWidget28);

            // Update chart on theme mode change
            KTThemeMode.on("kt.thememode.change", function() {                
                if (chartWidget28.rendered) {
                    chartWidget28.self.destroy();
                  
                }

                initChart(chartWidget28);
            });
        }   
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = KTChartsWidget28;
}

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTChartsWidget28.init();
});

