"use strict";
// Class definition

var KTDatatableJsonRemoteDemo = function () {
    // Private functions

    // basic demo
    var demo = function () {

        var datatable = $('.kt-datatable').KTDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: './dashboard/graficos/consultas/consulta-cockpit-usuario.php',
                pageSize: 10,
            },

            // layout definition
            layout: {
                scroll: true, // enable/disable datatable scroll both horizontal and vertical when needed.
                footer: true // display/hide footer
            },

            // column sorting
            sortable: true,

            pagination: true,

            responsive: true,
            /* 
                        search: {
                            input: $('#generalSearch')
                        }, */

            // columns definition
            columns: [

                {
                    field: 'nome_regra',
                    title: 'Nome Item',
                }, {
                    field: 'nome_obra',
                    title: 'Local',
                    template: function (row) {
                        return row.nome_obra + ' <i class="fa fa-angle-double-right"></i> ' + row.nome_estacao;
                    },
                }, {
                    field: 'modelo_grafico',
                    title: 'Modelo',
                    autoHide: false,
                    // callback function support for column rendering
                    template: function (row) {
                        var status = {
                            1: { 'title': 'Modelo 01', 'state': 'brand' },
                            2: { 'title': 'Modelo 02', 'state': 'dark' },
                            3: { 'title': 'Modelo 03', 'state': 'success' },
                            4: { 'title': 'Modelo 04', 'state': 'warning' },
                            5: { 'title': 'Modelo 05', 'state': 'primary' }

                        };
                        return '<span class="kt-badge kt-badge--' + status[row.modelo_grafico].state + ' kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-' + status[row.modelo_grafico].state + '">' +
                            status[row.modelo_grafico].title + '</span>';
                    },
                }, {
                    field: 'status_cockpit',
                    title: 'Status',
                    // callback function support for column rendering
                    template: function (row) {
                        var status = {
                            1: { 'title': 'Ativo', 'class': 'kt-badge--brand' },
                            2: { 'title': 'Inativo', 'class': ' kt-badge--warning' }

                        };
                        return '<span class="kt-badge ' + status[row.status_cockpit].class + ' kt-badge--inline kt-badge--pill">' + status[row.status_cockpit].title + '</span>';
                    },
                }, {
                    field: 'Actions',
                    title: 'Ações',
                    sortable: false,
                    width: 110,
                    autoHide: false,
                    overflow: 'visible',
                    template: function (row) {
                        return '\
						<a href="javascript:;"  class="btn btn-sm btn-clean btn-icon btn-icon-md edita_item" data-id=\"'+ row.id_cockpit + '\"  data-nome_regra=\"' + row.nome_regra + '\"title="Editar">\
							<i class="la la-edit"></i>\
						</a>\
						<a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md apaga_item"  data-nome_regra=\"' + row.nome_regra + '\" data-id=\"' + row.id_cockpit + '\" title="Apagar" >\
							<i class="la la-trash"></i>\
						</a>\
					';
                    },
                }],

        });





        $('#kt_form_status').on('change', function () {
            datatable.search($(this).val().toLowerCase(), 'status_cockpit');
        });

        $('#kt_form_type').on('change', function () {
            datatable.search($(this).val().toLowerCase(), 'modelo_grafico');
        });

        $('#kt_form_status,#kt_form_type').selectpicker();

    };

    return {
        // public functions
        init: function () {
            demo();
        }
    };
}();

jQuery(document).ready(function () {
    KTDatatableJsonRemoteDemo.init();


});


//==== pega id clicado na listagem da tabela ===//
jQuery(document).on("click", ".edita_item", function (e) {

    e.preventDefault();

    Cookies.set('acao_cockpit', 'editar');


    var id_cockpit_selecionado = $(this).data('id');

    var cockpit_nome = $(this).data("nome_regra");

    $("#div_edita_cockpit").removeClass("kt-hide").fadeIn();

    $("#div_edita_cockpit").load('./dashboard/views/regra-acompanhamento.php?acao=editar&id_cockpit=' + id_cockpit_selecionado);
    e.stopImmediatePropagation();

    console.log("Cockpit:" + cockpit_nome + " ID: " + id_cockpit_selecionado);
});
//==== pega id clicado na listagem da tabela ===//





//==== pega id clicado na listagem da tabela ===//
jQuery(document).on("click", ".apaga_item", function (e) {

    e.preventDefault();

    var id_cockpit_selecionado = $(this).data('id');

    var cockpit_nome = $(this).data("nome_regra");



    swal.fire({
        title: 'Você têm certeza?',
        html: "Esta ação não poderá ser desfeita! <br> Item Cockpit: <b>" + cockpit_nome + '</b>',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, Apague!',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (result.value) {

            $.ajax({
                url: './dashboard/crud/action-item-regra-cockpit.php',
                type: 'post',
                data: 'acao=apagar&id_cockpit=' + id_cockpit_selecionado,
                processData: true,
                success: function (response) {
                    if (response != 0) {
                        swal.fire(
                            'Apagado!',
                            'Seu Item de Cockpit foi Apagado com Sucesso!',
                            'success'
                        )


                        $('#retorno_lista_cockpit').load("./dashboard/views/regra-acompanhamento.php?acao=listar");

                    } else {
                        alert('Falha ao Apagar Item');
                    }
                },
            });




        }
    });


    e.stopImmediatePropagation();

    console.log("Cockpit:" + cockpit_nome + " ID: " + id_cockpit_selecionado);
});
        //==== pega id clicado na listagem da tabela ===//






