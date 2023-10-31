"use strict";

// Class definition
var KTChartsWidget15 = function () {
    var chart = {
        self: null,
        rendered: false
    };
    // Private methods
    var initChart = function (chart) {
        var element = document.getElementById("kt_charts_widget_15_chart");

        if (!element) {
            return;
        }

        $.getJSON('../../crud/dashboard/consulta-widget-15.php?modelo_grafico=1&periodo=7', function (objects) {

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

            chart.self = new ApexCharts(element, options);





            // Set timeout to properly get the parent elements width
            setTimeout(function () {
                chart.self.render();
                chart.rendered = true;
            }, 200);


            setTimeout(() => {
                if ($('#kt_charts_widget_15_chart').is(':empty')) {


                    //seu codigo
                    console.log('kt_charts_widget_15_chart sem dados');
                    $('#kt_charts_widget_15_chart').html('\<div class=\'alert alert-warning d-flex align-items-center p-15 mb-20\'><!--begin::Svg Icon | path: icons/duotune/general/gen048.svg--><span class=\'svg-icon svg-icon-2hx svg-icon-warning me-4\'><svg width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'><path opacity=\'0.3\' d=\'M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z\' fill=\'currentColor\'></path><path d=\'M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z\' fill=\'currentColor\'></path></svg></span><!--end::Svg Icon--><div class=\'d-flex flex-column\'><h4 class=\'mb-1 text-warning\'>Sem dados para exibir</h4><span>Os dados selecionados para este gráfico, não foram localizados no período selecionado.</span></div></div>');

                }

            }, 1000);
        });//fecha gtjson data    
    }

    // Public methods
    return {
        init: function () {
            initChart(chart);

            // Update chart on theme mode change
            KTThemeMode.on("kt.thememode.change", function () {
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
    module.exports = KTChartsWidget15;
}

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTChartsWidget15.init();
});


