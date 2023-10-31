"use strict";

// Função para inicializar o gráfico
    // Class definition
    var initKTChartsSsuporte = function () {
        var chart_ChartsSsuporte = {
            self: null,
            rendered: false
        };
        // Private methods

	var initChart = function (chart_ChartsSsuporte) {
		var element = document.getElementById("tabela-grafico-suporte");

		if (!element) {
			
			
			return;
		}


// Parâmetros da consulta
const params = new URLSearchParams({
    modelo_grafico: 1,
    periodo: 7
});


 // Adicionando uma mensagem de carregamento ao container do gráfico
 var loadingMessage = document.createElement("div");
 loadingMessage.id = 'mensagem-aguarde-tabela_plcodes_lidos';

 // Criação de spinner e mensagem de carregamento
 loadingMessage.innerHTML = `
 <div class="d-flex align-items-center">
 <div class="spinner-border text-primary " role="status">
 <span class="visually-hidden ">Loading...</span>
 </div>
 <div class="me-10"><span class="text-gray-600 px-3">Bucando Tickets...</span></div>
</div>
 `;

 element.appendChild(loadingMessage);


// Realiza a consulta
$.getJSON(`../../crud/dashboard/consulta-widget-tabela-grafico-suporte.php?${params.toString()}`, function (objects) {


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
			const qtdade_suporte = [];
			const nome_obra = [];
			const nome_ponto = [];
			const nome_suporte = [];
			
			for (let obj of objects) {
				
					qtdade_suporte.push(obj.qtdade_suporte);
			
				
				
					nome_obra.push(obj.nome_obra);
				
				
					nome_ponto.push(obj.nome_ponto);


					nome_suporte.push(obj.nome_suporte);
				
			}

			var cookie_projeto = KTCookie.get('projeto_atual');

			if (cookie_projeto != undefined) { // se há projeto selecionado, uso o subfiltro das estacoes do projeto

				var nome_exibicao = nome_ponto;

			} else { // se não há projeto selecionado, exibo por projetos

				var nome_exibicao = nome_obra;
			}


			//console.log(series, categories);
			console.log(nome_obra, nome_suporte, qtdade_suporte)

			var height = parseInt(KTUtil.css(element, 'height'));
			var width = parseInt(KTUtil.css(element, 'width'));
			var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
			var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');
			var colors = ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA', '#ffa500', '#0000ff', '#00FF00', '#D2691E', '#C71585', '#F0E68C', '#FF4500', '#D8BFD8', '#FF0000', '#800000', '#7B68EE', '#4B0082', '#A0522D', '#556B2F'];
			var baseColor = KTUtil.getCssVariableValue('--kt-primary');
			var nome_projeto = KTCookie.get("nome_projeto");

			



			var options = {
				series: [{
					name: 'Tickets em Aberto',
					data: qtdade_suporte
				}],
				chart: {
					fontFamily: 'inherit',
					type: 'bar',
					height: height,
					toolbar: {
						show: false
					}
				}, noData: {
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
				plotOptions: {
					bar: {
						horizontal: true,
						borderRadius: 4,
						columnWidth: '20%',
						barHeight: '70%',
						endingShape: 'rounded',
						distributed: true,
						rangeBarOverlap: true,
						rangeBarGroupRows: true,
						dataLabels: {
							position: 'top',
							maxItems: 100,
							hideOverflowingLabels: true,
							orientation: 'horizontal'
						}
					},
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
					show: true,
					position: 'bottom',
					horizontalAlign: 'left',
					color : baseColor,
					itemMargin: {
						horizontal: 15,
						vertical: 10
					},
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

			chart_ChartsSsuporte.self = new ApexCharts(element, options);

          
			// Set timeout to properly get the parent elements width
			setTimeout(function () {
				chart_ChartsSsuporte.self.render();
				chart_ChartsSsuporte.rendered = true;
			}, 200);

		}

		});//fecha gtjson data  

	
	}

	 // Public methods
        return {
            init: function () {
                initChart(chart_ChartsSsuporte);

                // Update chart_ChartsSsuporte on theme mode change
                KTThemeMode.on("kt.thememode.change", function () {
                    if (chart_ChartsSsuporte.rendered) {
                        chart_ChartsSsuporte.self.destroy();
                    }

                    initChart(chart_ChartsSsuporte);

					
                });
            }
        }
    }();



	
// Webpack support
if (typeof module !== 'undefined') {
	module.exports = initKTChartsSsuporte;
}

// On document ready
KTUtil.onDOMContentLoaded(function () {
	initKTChartsSsuporte.init();
	//

});




