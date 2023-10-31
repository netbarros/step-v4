"use strict";

// Class definition
var KTAppRelatorioLeituras = function () {
    // Shared variables
    var table;
    var datatable;
    const divElement_progresso_relatorio = document.querySelector('#progresso_relatorio'); // seleciona a div com id="minhaDiv"
    // Private functions
    var initDatatable = function () {

         // Exibe o elemento de "aguarde"
    $('#loader').removeClass('d-none');

  
    
    divElement_progresso_relatorio.style.width = '50%'; // altera a largura para 50%
    
    $('#tabela_relatorio_leitura').addClass('d-none');

    

        // Set date data order
        const tableRows = table.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            const realDate = moment(dateRow[3].innerHTML, "DD,MM,YYYY").format(); // select date from 4th column in table
            dateRow[3].setAttribute('data-order', realDate);
        });

        // Init datatable --- more info on datatables: https://datatables.net/manual/
      // Define as colunas da tabela
const columns = [
    { data: 'data' },
    { data: 'status' },
    { data: 'midia' },
    { data: 'nome_usuario' },
    { data: 'indicador' },
    { data: 'parametros' },
    { data: 'leitura' },
    { data: 'projeto' },
    { data: 'nucleo' },
    { data: 'plcode' },
    { data: 'caracteristica' },
    
];

// Init datatable --- more info on datatables: https://datatables.net/manual/
datatable = $(table).DataTable({
    "info": false,
    'order': [],
    'pageLength': 10,
     'columns': columns,
        // Quando os dados estiverem prontos, esconde o elemento de "aguarde"
        "drawCallback": function( settings ) {
            $('#loader').addClass('d-none');
            $('#tabela_relatorio_leitura').removeClass('d-none');
           // $('#div_grafico_relatorio').removeClass('d-none');
        }
});

    }

    // Init daterangepicker
    var initDaterangepicker = () => {
        var start = moment().subtract(7, "days");
        var end = moment();
        var input = $("#relatorio_leituras_daterangepicker");

        function cb(start, end) {
            input.html(start.format("DD,MM,YYYY") + " - " + end.format("DD,MM,YYYY"));
            Grafico_Leitura_Realizada(start, end);
            divElement_progresso_relatorio.style.width = '100%'; // altera a largura para 50%
            $('#loader').removeClass('d-none');
           
        }
        

        input.daterangepicker({
            showDropdowns: true,
            startDate: start,
            endDate: end,
            locale: {
                format: "DD/MM/YYYY",
                separator: " - ",
                applyLabel: "Aplicar",
                cancelLabel: "Cancelar",
                fromLabel: "De",
                toLabel: "Até",
                customRangeLabel: "Personalizado",
                weekLabel: "S",
                daysOfWeek: [
                    "Dom",
                    "Seg",
                    "Ter",
                    "Qua",
                    "Qui",
                    "Sex",
                    "Sáb"
                ],
                monthNames: [
                    "Janeiro",
                    "Fevereiro",
                    "Março",
                    "Abril",
                    "Maio",
                    "Junho",
                    "Julho",
                    "Agosto",
                    "Setembro",
                    "Outubro",
                    "Novembro",
                    "Dezembro"
                ],
                firstDay: 0
              },
            ranges: {
                "Hoje": [moment(), moment()],
                "Ontem": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Últimos 7 Dias": [moment().subtract(6, "days"), moment()],
                "Últimos 30 dias": [moment().subtract(29, "days"), moment()],
                "Este Mês": [moment().startOf("month"), moment().endOf("month")],
                "Mês Anterior": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
            },
        }, cb);
       

        cb(start, end);
      
    }

    // Handle status filter dropdown
    var handleStatusFilter = () => {
        const filterStatus = document.querySelector('[data-kt-relatorio_leituras-order-filter="status"]');
        $(filterStatus).on('change', e => {
            let value = e.target.value;
            if (value === 'all') {
                value = '';
            }
            // datatable.column(2).search(value).draw();
            datatable.search(value).draw();
            console.log("status selecionado = "+value);
        });
    }

    // Hook export buttons
    var exportButtons = () => {
        const documentTitle = 'STEP - Leituras Realizadas';
        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'copyHtml5',
                    title: documentTitle
                },
                {
                    extend: 'excelHtml5',
                    title: documentTitle
                },
                {
                    extend: 'csvHtml5',
                    title: documentTitle
                },
                {
                    extend: 'pdfHtml5',
                    title: documentTitle
                }
            ]
        }).container().appendTo($('#relatorio_leituras_export'));

        // Hook dropdown menu click event to datatable export buttons
        const exportButtons = document.querySelectorAll('#relatorio_leituras_export_menu [data-kt-relatorio_leituras-export]');
        exportButtons.forEach(exportButton => {
            exportButton.addEventListener('click', e => {
                e.preventDefault();

                // Get clicked export value
                const exportValue = e.target.getAttribute('data-kt-relatorio_leituras-export');
                const target = document.querySelector('.dt-buttons .buttons-' + exportValue);

                // Trigger click event on hidden datatable export buttons
                target.click();
            });
        });
    }


    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-relatorio_leituras-order-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }


    var Grafico_Leitura_Realizada = function(start, end) {

        var tipo_relatorio = "leitura-realizada";
        var Periodo_Inicial = start.format('YYYY-MM-DD');
        var Periodo_Final = end.format('YYYY-MM-DD');
      
        $.ajax({
          url: '../../crud/relatorios/monta-relatorios.php',
          method: 'POST',
          data: {
            tipo_relatorio: tipo_relatorio,
            Periodo_Inicial: Periodo_Inicial,
            Periodo_Final: Periodo_Final
          },
          dataType: 'json',
          success: function(data) {


            const table = $('#tabela_relatorio_leitura').DataTable();
            table.clear().rows.add(data).draw();


            table.draw();


            setTimeout(function() {


                
            // Função para criar o gráfico dinâmico
            function createDynamicChart(jsonData) {
                const chart = Highcharts.chart('dynamic-chart', {
                  chart: {
                    type: 'line'
                  },
                  title: {
                    text: jsonData.grafico_projeto
                  },
                  xAxis: {
                    type: 'datetime',
                    dateTimeLabelFormats: {
                      day: '%e/%b/%Y %H:%M'
                    },
                    title: {
                      text: 'Data'
                    }
                  },
                  yAxis: {
                    title: {
                      text: jsonData.plcode
                    }
                  },
                  tooltip: {
                    valueSuffix: ' ' + jsonData.grafico_unidade_medida,
                    valueDecimals: 2
                  },
                  series: [{
                    name: jsonData.indicador,
                    data: [[new Date(jsonData.data).getTime(), parseFloat(jsonData.grafico_leitura)]]
                  }]
                });
        
                if (jsonData.parametros) {
                  const minMaxValues = jsonData.parametros.split(" <> ");
                  if (minMaxValues.length === 2) {
                    chart.yAxis[0].update({
                      min: parseFloat(minMaxValues[0]),
                      max: parseFloat(minMaxValues[1])
                    });
                  }
                }
        
                return chart;
              }
        
              // Crie o gráfico dinâmico usando a função createDynamicChart
              const chart = createDynamicChart(data);
        
              // Atualize o gráfico com os novos dados
              function updateChartWithData(chart, newJsonData) {
                // Atualiza o título do gráfico
                chart.setTitle({
                  text: newJsonData.grafico_projeto
                });
        
                // Atualiza o título do eixo Y
                chart.yAxis[0].setTitle({
                  text: newJsonData.plcode
                });
        
                // Atualiza os limites do eixo Y, se necessário
                if (newJsonData.parametros) {
                  const minMaxValues = newJsonData.parametros.split(" <> ");
                  if (minMaxValues.length === 2) {
                    chart.yAxis[0].update({
                      min: parseFloat(minMaxValues[0]),
                      max: parseFloat(minMaxValues[1])
                    });
                  }
                }
            // Procura a série de dados com o mesmo nome do indicador
      let series = chart.series.find(s => s.name === newJsonData.indicador);
  
      // Se a série de dados não existir, cria uma nova série
      if (!series) {
          series = chart.addSeries({
              name: newJsonData.indicador,
              data: []
          });
      }
  
      // Atualiza os dados da série com o novo valor
      const newDataPoint = [new Date(newJsonData.data).getTime(), parseFloat(newJsonData.grafico_leitura)];
      series.addPoint(newDataPoint, true, false);
  }
  
  
  
  // Adicionar uma nova série de dados
  chart.addSeries({
      name: 'Nova série',
      data: [[new Date().getTime(), 7.2]]
  });
  
  // Remover a primeira série de dados
  chart.series[0].remove();
   
  updateChartWithData(chart, data);

            }, 1000);

          
            
},
error: function(data) {
  Swal.fire(data);
}
});
};        

    // Public methods
    return {
        init: function () {
            table = document.querySelector('#tabela_relatorio_leitura');
        

            if (!table) {
                return;
            }

            initDatatable();
            initDaterangepicker();
            exportButtons();
            handleSearchDatatable();
            handleStatusFilter();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTAppRelatorioLeituras.init();
});


