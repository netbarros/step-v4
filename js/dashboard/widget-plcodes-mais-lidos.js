"use strict";

// Class definition
var Grafico_PLCode_Mais_Lido = function () {
	var chart_PLCode_Mais_Lido = {
		self: null,
		rendered: false
	};
	// Private methods
	var initChart = function (chart_PLCode_Mais_Lido) {
		var element = document.getElementById("tabela_plcodes_lidos");

		if (!element) {
			return;
		}


		// Seleciona o elemento da mensagem de aguarde


		  // Adicionando uma mensagem de carregamento ao container do gráfico
		  var loadingMessage = document.createElement("div");
		  loadingMessage.id = 'loadingMessage_PLCode_Mais_Lido';
  
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


		$.getJSON('../../crud/dashboard/consulta-tabela-plcodes-lidos.php', function (objects) {


			
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
    const total_plcode = [];
    const nome_obra = [];
    const nome_ponto = [];
    const nome_parametro = [];

    for (let obj of objects) {
        
            total_plcode.push(obj.total_plcode);
    
        if (!nome_parametro.includes(obj.nome_parametro)) {
            nome_parametro.push(obj.nome_parametro);
        }
        
        
            nome_obra.push(obj.nome_obra);
        
        
            nome_ponto.push(obj.nome_ponto);
        
    }

    var cookie_projeto = KTCookie.get('projeto_atual');
    var nome_projeto = KTCookie.get("nome_projeto")

    if (cookie_projeto != undefined) { // se há projeto selecionado, uso o subfiltro das estacoes do projeto

        var nome_exibicao = nome_ponto;

    } else { // se não há projeto selecionado, exibo por projetos

        var nome_exibicao = nome_obra;
    }

var height = parseInt(KTUtil.css(element, 'height'));
var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');
var baseprimaryColor = KTUtil.getCssVariableValue('--kt-primary');
var lightprimaryColor = KTUtil.getCssVariableValue('--kt-primary');
var basesuccessColor = KTUtil.getCssVariableValue('--kt-success');
var lightsuccessColor = KTUtil.getCssVariableValue('--kt-success');
var colors = ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA', '#ffa500', '#0000ff', '#00FF00', '#D2691E', '#C71585', '#F0E68C', '#FF4500', '#D8BFD8', '#FF0000', '#800000', '#7B68EE', '#4B0082', '#A0522D', '#556B2F'];

			;

            var options = {
				series: [{
					name: 'Total Lido:',
					data: total_plcode
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
						columnWidth: '30%',
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
								thousand: ''
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

			chart_PLCode_Mais_Lido.self = new ApexCharts(element, options);
			chart_PLCode_Mais_Lido.self.render();
            chart_PLCode_Mais_Lido.rendered = true;

		

			}


        });//fecha gtjson data   


		
		

				
				
		
		

	}

	// Public methods
	return {
		init: function () {
			initChart(chart_PLCode_Mais_Lido);

			// Update chart_PLCode_Mais_Lido on theme mode change
			KTThemeMode.on("kt.thememode.change", function () {
				if (chart_PLCode_Mais_Lido.rendered) {
					chart_PLCode_Mais_Lido.self.destroy();
				}

				initChart(chart_PLCode_Mais_Lido);

			

			});
		}
	}
}();

// Webpack support
if (typeof module !== 'undefined') {
	module.exports = Grafico_PLCode_Mais_Lido;
}

// On document ready
KTUtil.onDOMContentLoaded(function () {
	Grafico_PLCode_Mais_Lido.init();
	//

});




