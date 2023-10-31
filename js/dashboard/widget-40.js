"use strict";

// Class definition
var KTChartsWidget40 = function () {
	var chart_Widget40 = {
		self: null,
		rendered: false
	};
	// Private methods
	var initChart = function (chart_Widget40) {
		var element = document.getElementById("kt_charts_widget_40_chart");

		if (!element) {
			return;
		}


		  // Adicionando uma mensagem de carregamento ao container do gráfico
		  var loadingMessage = document.createElement("div");
		  loadingMessage.id = 'loadingMessage_widget_27';
  
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


		$.getJSON('../../crud/dashboard/consulta-widget-40.php?modelo_grafico=1&periodo=7', function (objects) {


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
			const categoria_suporte = [];
			

			for (let obj of objects) {
				
				qtdade_suporte.push(obj.qtdade_suporte);
			
				categoria_suporte.push(obj.categoria_suporte);
			
				
			}

			var cookie_projeto = KTCookie.get('projeto_atual');

			

			var height = parseInt(KTUtil.css(element, 'height'));
			var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
			var borderColor = KTUtil.getCssVariableValue('--kt-gray-200');
			var baseColor = KTUtil.getCssVariableValue('--kt-primary');
			var secondaryColor = KTUtil.getCssVariableValue('--kt-gray-300');

			var colors = ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA', '#ffa500', '#0000ff', '#00FF00', '#D2691E', '#C71585', '#F0E68C', '#FF4500', '#D8BFD8', '#FF0000', '#800000', '#7B68EE', '#4B0082', '#A0522D', '#556B2F'];
			

			var nome_projeto = KTCookie.get("nome_projeto");

			

			if (!element) {
				return;
			}

			var options = {
				series: [{
					name: 'Tickets por Categoria',
					data: qtdade_suporte
				}],
				chart: {
					fontFamily: 'inherit',
					type: 'bar',
					height: height,
					toolbar: {
						show: false
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
					categories: categoria_suporte,
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

			chart_Widget40.self = new ApexCharts(element, options);





			// Set timeout to properly get the parent elements width
			setTimeout(function () {
				chart_Widget40.self.render();
				chart_Widget40.rendered = true;
			}, 200);


		}
		});//fecha gtjson data    
	}

	// Public methods
	return {
		init: function () {
			initChart(chart_Widget40);

			// Update chart_Widget40 on theme mode change
			KTThemeMode.on("kt.thememode.change", function () {
				if (chart_Widget40.rendered) {
					chart_Widget40.self.destroy();
				}

				initChart(chart_Widget40);
			});
		}
	}
}();

// Webpack support
if (typeof module !== 'undefined') {
	module.exports = KTChartsWidget40;
}

// On document ready
KTUtil.onDOMContentLoaded(function () {
	KTChartsWidget40.init();
});

