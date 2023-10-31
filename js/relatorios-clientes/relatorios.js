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
    { data: 'id' },
    { data: 'data' },
    { data: 'cnpj' },
    { data: 'razao_social' },
    { data: 'nome_fantasia' },
    { data: 'email_geral' },
    { data: 'telefone' },
    { data: 'status' }
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
        }
});

    }

    // Init daterangepicker
    var initDaterangepicker = () => {
        var start = moment().subtract(5, "years");
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


   
 
    var Grafico_Leitura_Realizada = function(start, end){

        var tipo_relatorio = "listagem-clientes";
        
        var Periodo_Inicial = start.format('YYYY-MM-DD');
        var Periodo_Final = end.format('YYYY-MM-DD');
        
        
        $.ajax({
            url: '../../crud/relatorios-clientes/monta-relatorios.php',
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

             
        
             // $("#dados_relatorio_leituras_realizadas").html(data);
                //('Recebeu o dado de volta');
        
           
            
            },
            error: function(data) {
        
                Swal.fire(data);
             
            }
          });
        
        }

        
          
        
    

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



var imagemModal = document.getElementById('imagemModal');
  imagemModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var imagemURL = button.getAttribute('href');
    var modalImage = imagemModal.querySelector('.modal-body img');
    modalImage.src = imagemURL;
  })





  var myModal = document.getElementById('kt_modal_edita_cliente');


  myModal.addEventListener('shown.bs.modal', function(event) {


      var button = $(event.relatedTarget);

      var recipientId = button.data('id');

      var modal = $(this);

      //modal.find('#minhaId').html(recipientId);


      $.ajax({
          type: 'POST',
          url: '../../views/clientes/modal-edita-cliente.php?id='+recipientId,
          dataType: 'html',
          data: {
              id: recipientId
          },
          beforeSend: function() {
              $("#aguardar_cliente_carregar").removeClass("d-none");
          },
          success: function(retorno) {

              $("#aguardar_cliente_carregar").addClass("d-none");

              $("#conteudo_modal_edita_cliente").html(retorno);
          },
          error: function() {
              alert("Falha ao coletar dados !!!");
          }
      });


      //$("#conteudo_modal_edita_cliente" ).load( "../../views/clientes/modal-edita-cliente.php?id="+recipientId );



  })



  myModal.addEventListener('hidden.bs.modal', function(event) {

      location.reload();

  })


