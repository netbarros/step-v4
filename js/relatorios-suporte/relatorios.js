"use strict";


 // Função para obter o valor de um cookie pelo nome
 function getCookie(name) {
    let cookies = document.cookie.split(';');
    for(let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i];
        let [key, value] = cookie.split('=').map(c => c.trim());
        if (key === name) {
            return decodeURIComponent(value);
        }
    }
    return null;
}

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
    { data: 'id_suporte' },
    { data: 'midia' },
    { data: 'status' },
    { data: 'projeto' },
    { data: 'nucleo' },
    { data: 'nome_suporte' },
    { data: 'motivo_suporte' },
    { data: 'nome_usuario' },
    { data: 'data_open' },
    { data: 'data_close' }

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
        var start = moment().subtract(7, "days");
        var end = moment();
        var input = $("#relatorio_leituras_daterangepicker");

        function cb(start, end) {
            input.html(start.format("DD,MM,YYYY") + " - " + end.format("DD,MM,YYYY"));
            Gera_Relatorio_Suporte(start, end);
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
        // Modificação para busca por palavras compostas
        value = value.trim(); // remover espaços no início e no final da string
        if (value !== '') {
            value = value.replace(/\s+/g, ' '); // substituir espaços consecutivos por um único espaço
            value = value.split(' ').join('|'); // separar palavras com '|'
            value = `${value}`; // envolver palavras em parênteses para tratar como um grupo
        }
        datatable.search(value, true, false).draw();
        console.log("status selecionado = "+value);
    });
}



    // Hook export buttons
    var exportButtons = () => {
        const documentTitle = 'STEP - Acompanhamento de Suporte';
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


   
 
    var Gera_Relatorio_Suporte = function(start, end) {


        let ticket = getCookie('id_tipo_suporte_ticket');
        let usuario = getCookie('usuario_ticket');
        let mailkey = getCookie('mailkey_ticket');
        let projeto_ticket = getCookie('projeto_ticket');
           
        
           
        var complemento = "";
        var nome_relatorio = "";
        var tipo_relatorio = "";
        if (
            ticket !== null && 
            usuario !== null && 
            mailkey !== null && 
            projeto_ticket !== null
        ) {         

            console.log("Bloco Tickets por Coleção de Notificações executado");
            usuario = usuario.split('?')[0];
            nome_relatorio = "Suporte por Coleção de Notificações";
            tipo_relatorio = "colecoes_suporte_email";
            complemento = "?mail=1&" + mailkey ;
        
            Gera_Relatorio_Email_Suporte();
        
        }  else {
            console.log("Bloco de Tickets por Coleção de Notificações não encontrado");
            nome_relatorio = "Call Center | <small>Acompanhamento de Suportes</small>";
            tipo_relatorio = "acompanhamento_suporte";
            complemento = "?direct=1";
        }
        
        
    
        let Periodo_Inicial = start.format('YYYY-MM-DD');
        let Periodo_Final = end.format('YYYY-MM-DD');
    
        $.ajax({
            url: '../../crud/relatorios-suporte/monta-relatorios.php' + complemento,
            method: 'POST',
            data: {
                nome_relatorio: nome_relatorio,
                tipo_relatorio: tipo_relatorio,
                Periodo_Inicial: Periodo_Inicial,
                Periodo_Final: Periodo_Final
            },
            dataType: 'json',
            success: function(data) {


                if(data.codigo == 0) {

                    $('#loader').addClass('d-none');

                    Swal.fire({
                        title: 'Coleção de Notifiacões Ausente!',
                        text: 'Não localizamos Tickets para o período selecionado!',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok, entendi!',
                        customClass: {
                            confirmButton: 'btn btn-light-primary'
                        }
                    }).then(function() {

                        $("#pagina_atual_usuario_sessao").html(nome_relatorio);

                        KTUtil.scrollTop();
                    });

                } else {


                    if (
                        ticket !== null && 
                        usuario !== null && 
                        mailkey !== null && 
                        projeto_ticket !== null
                    ) {  

                    var html = '<div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-3">';
                        html += '<div class="d-flex flex-stack flex-grow-1">';
                        html += '<div class="fw-semibold">';
                        html += '<span class="fs-4 fw-bolder text-gray-900">Acesso Direto aos Suportes por Coleção de Notificações</span>';
                        html += '<div class="separator mt-1 opacity-75 text-info"> </div>';
                        html += '<br><span class="fs-6 fw-bolder text-gray-800 mb-5">Você está acessando uma página exclusiva para monitorar o Tipo de Suporte direcionado pela Notificação recebida.</span>';
                        html += '<div class="fs-6 text-gray-700 py-2">';
                        html += '<a href="javascript:;" class="fw-bold me-2 mb-5"> Tipo de Suporte:</a>' + (data[0] && data[0].nome_suporte ? data[0].nome_suporte : '') + '<br>';
                        html += '<a href="javascript:;" class="fw-bold me-2 mb-5"> Quem está Monitorando:</a>' + (data[0] && data[0].nome_usuario ? data[0].nome_usuario : '') + '<br>';
                        html += '<a href="javascript:;" class="fw-bold me-2 mb-5"> Projeto: </a>' + (data[0] && data[0].projeto ? data[0].projeto : '') + '<br>';
                        html += '</div>';
                        html += '<br><span class="fs-7 text-gray-700 mb-5 me-2">Seu Dashboard está temporariamente programado para buscar apenas o Tipo de Suporte da Notificação recebida,<br> caso queira ampliar o período do relatório.</span>';
                        html +='</div></div></div>';
                    } else {
                        var html = '<div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-3">';
                        html += '<div class="d-flex flex-stack flex-grow-1">';
                        html += '<div class="fw-semibold">';
                        html += '<span class="fs-4 fw-bolder text-gray-900">Acesso Direto ao Acompanhamento de Suportes</span>';
                        html += '<div class="separator mt-1 opacity-75 text-info"> </div>';
                        html += '<br><span class="fs-6 fw-bolder text-gray-800 mb-5">Você está acessando uma página exclusiva para monitorar os Suportes abertos no Call Center.</span>';
                        html += '<div class="fs-6 text-gray-700 py-2">';
                    }
            
                $("#pagina_atual_usuario_sessao").html(html);
            
                const table = $('#tabela_relatorio_leitura').DataTable();
                table.clear().rows.add(data).draw();
            
                setTimeout(function() {   
                    KTUtil.scrollTop();
                }, 500);
            }
            
        }, error: function(request, status, error) {
                Swal.fire('Error: ' + error + '. Status: ' + status + '. Response: ' + request.responseText);
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






 
function Gera_Relatorio_Email_Suporte () {   
    
                                 // Recuperar valores dos cookies
                                                             // Recuperar valores dos cookies
                                let ticket = getCookie('id_tipo_suporte_ticket');
                                let usuario = getCookie('usuario_ticket');
                                let mailkey = getCookie('mailkey_ticket');
                                let projeto_ticket = getCookie('projeto_ticket');
                                let nome_usuario_ticket = getCookie('nome_usuario');

                                // Validar os valores dos cookies
                                if (
                                    ticket !== null && 
                                    usuario !== null && 
                                    mailkey !== null && 
                                    projeto_ticket !== null
                                ) { 
       

    let timerInterval_suporte
    Swal.fire({
    title: 'Suporte por Coleção de Notificações',
    html: '<strong>Bem vindo!\n\n' + nome_usuario_ticket + '</strong><br><br>Estamos buscando os Tickets Relacionados ao seu Alerta Recebido.<br><br>Eles estarão prontos em <b></b> segundos.',
    timer: 5000,
    icon: "warning",
    timerProgressBar: true,
    didOpen: () => {
        Swal.showLoading()
        const b = Swal.getHtmlContainer().querySelector('b')
        timerInterval_suporte = setInterval(() => {
            b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
        }, 100)
    },
    willClose: () => {
        clearInterval(timerInterval_suporte)
    }
    }).then((result) => {
    /* Read more about handling dismissals below */
    if (result.dismiss === Swal.DismissReason.timer) {
        console.log('I was closed by the timer')
        
    }
    }) 

 }
}




/* window.onbeforeunload = function() {
    // Define os cookies para expirar no passado, deletando-os
    document.cookie = "id_tipo_suporte_ticket=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "usuario_ticket=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "mailkey_ticket=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "projeto_ticket=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

    // Define o cookie 'suporte' com o valor '1'
    document.cookie = "suporte=1; path=/;";
} */





