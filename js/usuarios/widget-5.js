"use strict";

// Class definition
var KTChartsWidget5 = function () {
    var chart = {
        self: null,
        rendered: false
    };

    // Private methods
    var initChart = function(chart) {
        var element = document.getElementById("kt_charts_widget_5"); 

        if (!element) {
            return;
        }

        $.getJSON('../../crud/usuarios/consulta-widget-05.php?modelo_grafico=1&periodo=7', function (objects) {

            //construir primeiro todas as datas de leitura (categories) existentes
            const total_suporte = [];
            const nome_suporte = [];
            const maximo = [];

            for (let obj of objects) {

                nome_suporte.push(obj.nome_suporte);
                total_suporte.push(obj.total_suporte);

                if (!maximo.includes(obj.maximo)) {
                    maximo.push(obj.maximo);
                }

            }        
        
        var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');
        
        var options = {
            series: [{
                data: total_suporte,
                show: false                                                                              
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }                             
            },                    
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                    distributed: true,
                    barHeight: 23                   
                }
            },
            dataLabels: {
                enabled: true                               
            },             
            legend: {
                show: false
            },                               
            colors: ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA', '#50CDCD', '#3F4254'],                                                                      
            xaxis: {
                categories: nome_suporte,
                labels: {
                    formatter: function (val) {
                      return val + " Tickets"
                    },
                    style: {
                        colors: KTUtil.getCssVariableValue('--kt-gray-400'),
                        fontSize: '14px',
                        fontWeight: '600',
                        align: 'left'                                              
                    }                  
                },
                axisBorder: {
					show: false
				}                         
            },
            yaxis: {
                labels: {                   
                    style: {
                        colors: KTUtil.getCssVariableValue('--kt-gray-800'),
                        fontSize: '14px',
                        fontWeight: '600'                                                                 
                    },
                    offsetY: 2,
                    align: 'left' 
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
                        show: false  
                    }
                },
                strokeDashArray: 4              
            }                                 
        };  
          
        chart.self = new ApexCharts(element, options);

        // Set timeout to properly get the parent elements width
        setTimeout(function() {
            chart.self.render();
            chart.rendered = true;
        }, 200); 
        });//fecha gtjson data   
    }

    // Public methods
    return {
        init: function () {
            initChart(chart);

            // Update chart on theme mode change
            KTThemeMode.on("kt.thememode.change", function() {                
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
    module.exports = KTChartsWidget5;
}

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTChartsWidget5.init();
});


 