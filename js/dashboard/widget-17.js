// A point click event that uses the Renderer to draw a label next to the point
// On subsequent clicks, move the existing label instead of creating a new one.
Highcharts.addEvent(Highcharts.Point, 'click', function () {
    if (this.series.options.className.indexOf('popup-on-click') !== -1) {
        const chart = this.series.chart;
        const date = Highcharts.dateFormat('%A, %b %e, %Y', this.x);
        const text = `<b>${date}</b><br/>${this.y} ${this.series.name}`;

        const anchorX = this.plotX + this.series.xAxis.pos;
        const anchorY = this.plotY + this.series.yAxis.pos;
        const align = anchorX < chart.chartWidth - 200 ? 'left' : 'right';
        const x = align === 'left' ? anchorX + 10 : anchorX - 10;
        const y = anchorY - 30;
        if (!chart.sticky) {
            chart.sticky = chart.renderer
                .label(text, x, y, 'callout',  anchorX, anchorY)
                .attr({
                    align,
                    fill: 'rgba(0, 0, 0, 0.75)',
                    padding: 10,
                    zIndex: 7 // Above series, below tooltip
                })
                .css({
                    color: 'white'
                })
                .on('click', function () {
                    chart.sticky = chart.sticky.destroy();
                })
                .add();
        } else {
            chart.sticky
                .attr({ align, text })
                .animate({ anchorX, anchorY, x, y }, { duration: 250 });
        }
    }
});


Highcharts.setOptions({
    lang: {
        weekdays: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
        shortMonths: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dec'],
        thousandsSep: '.',
        decimalPoint: ','

    }
});

$.ajax({
    url: '/crud/graficos-dashboard/consulta-widget-17.php',
    data: {
        periodo: 7,
        modelo_grafico: 1
    },
    dataType: 'json',
    Post: 'GET',
    success: function(data) {
        var total_leituras_registradas = data.map(function(obj) {
            var date = new Date(obj.data_leitura);
            var timestamp = date.getTime();
            return [timestamp, obj.total_leituras_registradas];
        });

        var total_chamados_suporte = data.map(function(obj) {
            var date = new Date(obj.data_leitura);
            var timestamp = date.getTime();
            return [timestamp, obj.total_chamados_suporte];
        });

        var chamados_suporte_fechado = data.map(function(obj) {
            var date = new Date(obj.data_leitura);
            var timestamp = date.getTime();
            return [timestamp, obj.chamados_suporte_fechado];
        });

        var chamados_suporte_aberto = data.map(function(obj) {
            var date = new Date(obj.data_leitura);
            var timestamp = date.getTime();
            return [timestamp, obj.chamados_suporte_aberto];
        });

        var series = [
            { name: 'Total de Leituras Registradas', data: total_leituras_registradas },
            { name: 'Total de Chamados de Suporte', data: total_chamados_suporte },
            { name: 'Chamados de Suporte Fechados', data: chamados_suporte_fechado },
            { name: 'Chamados de Suporte Abertos', data: chamados_suporte_aberto }
        ];

        var chart = Highcharts.chart('grafico-resumo-semanal', {
            lang: {
                weekdays: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
                shortMonths: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dec'],
                thousandsSep: '.',
                decimalPoint: ','

            },
            chart: {
                scrollablePlotArea: {
                    minWidth: 550
                },
                events: {
                    redraw: function () {
                        var chart = this;
                        if (chart.customLabels) {
                            chart.customLabels.forEach(function (label) {
                                label.destroy();
                            });
                        }
                        chart.customLabels = [];
                        var y = chart.plotHeight - 220;
                        var x = chart.plotWidth - 260; // Coloca as etiquetas 200 pixels à esquerda do lado direito do gráfico
            
                        // Cria um retângulo de fundo
                        var box = chart.renderer.rect(x-10, y-83, 300, 20 * chart.series.length + 20, 5)
                            .attr({
                                'stroke-width': 2,
                                stroke: 'gray',
                                fill: 'rgba(173, 216, 230, 0.5)', // azul claro com 50% de opacidade
                                zIndex: 0
                            })
                            .add();
                        chart.customLabels.push(box);
            
                        chart.series.forEach(function (serie) {
                            var total = serie.data.reduce(function (acc, point) {
                                return acc + point.y;
                            }, 0);
                            var label = chart.renderer.text('<strong>'+serie.name + '</strong> Total: ' + total.toLocaleString('pt-BR'), x, y) // Aqui está o .toLocaleString()
                                .attr({
                                    zIndex: 1
                                })
                                .add();
                            chart.customLabels.push(label);
                            y -= 20; // Ajuste esta linha para alterar a distância entre as linhas
                        });
                        
                    }
                }
            },
            
            
            title: {
                text: 'Indicadores Scorecard',
                align: 'left'
            },
            subtitle: {
                text: 'Fonte: Base de Dados STEP',
                align: 'left'
            },
            xAxis: {
                type: 'datetime',
                gridLineWidth: 1,
                labels: {
                    align: 'left',
                    x: -3,
                    y: -3
                }
            },
            yAxis: [{ // left y axis
                title: {
                    text: 'Período Semanal'
                },
                labels: {
                    align: 'left',
                    x: 3,
                    y: 16,
                    format: '{value:.,0f}'
                },
                showFirstLabel: false
            }],
            legend: {
                align: 'left',
                verticalAlign: 'top',
                borderWidth: 0
            },
            tooltip: {
                shared: true,
                crosshairs: true
            },
            plotOptions: {
                series: {
                    cursor: 'pointer',
                    className: 'popup-on-click',
                    marker: {
                        lineWidth: 1
                    }
                }
            },
            series: series
        });

        // Chame chart.redraw() após a criação do gráfico
        chart.redraw();
    }
});
