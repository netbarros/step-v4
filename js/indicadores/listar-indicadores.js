"use strict";

var Relatorio_STEP = function() {

	$.fn.dataTable.Api.register('column().title()', function() {
		return $(this.header()).text().trim();
	});

	var initTable1 = function() {
		// begin first table
		var table = $('#kt_table_parametros_full').DataTable({
            responsive: true,
            retrieve: true,
			// Pagination settings
            dom: `<'row'<'col-sm-6 text-left'f><'col-sm-6 text-right'B>>
			<'row'<'col-sm-12'tr>>
			
			<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
			//https://datatables.net/examples/basic_init/dom.html
			// read more: https://datatables.net/examples/basic_init/dom.html

			lengthMenu: [5, 10, 25, 50,100,200],

			pageLength: 25,

			language: {
				'lengthMenu': 'Exibir _MENU_',
			},

			searchDelay: 500,
			processing: false,
			serverSide: false,
			colReorder: true,
			ajax: {
				url: './plcodes/parametros/consultas/consulta-lista-parametros.php',
				type: 'POST',
				data: {
					// parameters for custom backend script demo
                    columnsDef: [
                        "id_parametro" ,
                        "nome_parametro" ,
                        "nome_unidade_medida",
                         "nome_estacao",
                         "nome_ponto",
                         "concen_min",
                         "concen_max",
                         "tipo_checkin",
                         "gera_grafico",
                        "status_parametro",
                         "acoes"
                        ],
				},
			},
			columns: [
			
                { data: 'id_parametro' },
                { data: 'nome_obra' },
                { data: 'nome_estacao' },
                { data: 'nome_ponto' },
                {data: 'nome_parametro'},	
                { data: null, render: function (data, type, row) {
                        // Combine the first and last names into a single table field
                   

                        if (data.nome_unidade_medida != null) { // minima
                            var retorno = data.nome_unidade_medida;
							return retorno
                        } else {

                            var retorno = 'Ausente';
							return retorno

                        }
    				}
                },
               
              
                {data: null, render: function (data, type, row) {
                        // Combine the first and last names into a single table field
                   
                        if (data.controle_concentracao === '0') { // minima
                            var retorno = "--";
							return retorno
                        } 
                        if (data.controle_concentracao === '1') { // minima
                            var retorno = data.concen_min;
							return retorno
                        } 
                        
                        if (data.controle_concentracao === '2') { // maxima
                            var retorno = data.concen_max;
							return retorno
                        } 
                        
                        if (data.controle_concentracao === '3') { // maxima
                            var retorno = data.concen_min+' à '+data.concen_max;
							return retorno
						} 

					}
				},          
                {data: null, render: function (data, type, row) {
                        // Combine the first and last names into a single table field

                        if (data.tipo_checkin === null) { // minima
                            var retorno = "Sem Checkin";
                            return retorno
                        }
                        if (data.tipo_checkin === 'ponto_parametro') { // minima
                            var retorno = "Por Indicador";
                            return retorno
                        }

                        if (data.tipo_checkin === 'ponto_plcode') { // maxima
                            var retorno = "Por PLCode";
                            return retorno
                        }

                        if (data.tipo_checkin === 'tarefa_agendada') { // maxima
                            var retorno = "Tarefa Delegada";
                            return retorno
                        }

                        }
                },
                {data: 'gera_grafico'},
                {data: 'status_parametro'},
                {data: 'acoes'},
			],

			initComplete: function() {
				var api = this.api();
				api.$('td').click( function () {
					api.search( this.innerHTML ).draw();
				} );
				this.api().columns().every(function() {
					var column = this;
				

					
				});
            },
            buttons: [
                {
                    extend: 'print',
                    text: "<i class='kt-font-brand flaticon2-printer'></i>",
                    title: 'STEP - Listagem Parâmetros/ Indicadores',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [  0, 1, 2, 3,4,5,6,7,8]
                    },
                   
                    messageBottom: null,
                    orientation: 'landscape',
                    customize: function ( win ) {
                        $(win.document.body)
                            .css( 'font-size', '10pt' )
                            .prepend(
                                '<img src="https://step.eco.br/v2/assets/media/logos/logo-4-sm.png" style="position:absolute; widht:92px; height:40px; top:5px; left:90%;" />'
                            );
     
                        $(win.document.body).find( 'table' )
                            .addClass( 'compact' )
                            .css( 'font-size', 'inherit' );
                    }
                }, 
                {
                    extend: 'copyHtml5',
                    title: 'STEP - Listagem Parâmetros/ Indicadores',
                    text: "<i class='kt-font-dark flaticon2-copy'></i>",
                    exportOptions: {
                        modifier: {
                            page: 'current'
                        }
                    }
				}, {
                    extend: 'excelHtml5',
                    title: 'STEP - Listagem Parâmetros/ Indicadores',
                    text: "<i class='kt-font-success fa fa-file-excel'></i>",
                    exportOptions: {
                        modifier: {
                            page: 'current'
                        }
                    }
				},{
                    extend: 'csvHtml5',
                    title: 'STEP - Listagem Parâmetros/ Indicadores',
                    text: "<i class='kt-shape-font-color-4 fa fa-file-csv'></i>",
                    exportOptions: {
                        modifier: {
                            page: 'current'
                        }
                    }
				},
                    {
                    extend: 'pdfHtml5',
                    title: 'STEP - Listagem Parâmetros/ Indicadores',
					text: "<i class='kt-font-danger fa fa-file-pdf'></i>",
                   // orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [  0, 1, 2, 3,4,5,6,7,8 ]
                    },
                    customize: function ( doc ) {
                        doc.content.splice( 0, 0, {
                            margin: [ 0, 0, -30, 5 ],
                            alignment: 'left',
                            image: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFwAAAAoCAYAAABzXJ2PAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA3BpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQ1IDc5LjE2MzQ5OSwgMjAxOC8wOC8xMy0xNjo0MDoyMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDo1MTQ2MjJlOC05Zjc0LTQzNzYtYjMyNy1lNDRhMTVkY2Y1YjgiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTNFMDU4NUFDNUI5MTFFOTlBMzhGQ0M3NTI4Q0FCNkMiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTNFMDU4NTlDNUI5MTFFOTlBMzhGQ0M3NTI4Q0FCNkMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTkgKE1hY2ludG9zaCkiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpBRThGMEQwNkFDQkIxMUU5QThDRUVGNTFEM0ZFQjcxOCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpBRThGMEQwN0FDQkIxMUU5QThDRUVGNTFEM0ZFQjcxOCIvPiA8L3Jk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'
                        } );
                    }
                }],

			columnDefs: [{
                targets: -1,
                width: '80px',
                title: "Ações",
                orderable: !1,
                render: function(a, e, t, n) {
                    return ' \n <button type="button" title="Editar Indicador" class="btn btn-sm btn-clean btn-icon btn-icon-md abre_registro" data-nome="'+t.nome_parametro+'" data-toggle="modal" data-id="'+t.id_parametro+'" data-target="#modal_altera_registro"><i class="la la-edit kt-shape-font-color-3"></i></button> \n <button type="button" title="Eliminar Indicador" class="btn btn-sm btn-clean btn-icon btn-icon-md apaga_registro_parametro" data-id="'+t.id_parametro+'" >   <i class="la la-trash"></i> </button>'
                }
            }, {
                targets: 8,
                render: function(data, type, full, meta) {
                    var status = {
                        null: {'title': 'Não', 'state': 'warning'},
                        0: {'title': 'Não', 'state': 'warning'},
                        1: {'title': 'Sim', 'state': 'primary'},
                        3: {'title': 'Inativo', 'state': 'danger'},
                    };
                   
                    return '<span class="kt-badge kt-badge--' + status[data].state + ' kt-badge--dot"></span>&nbsp;' +
                        '<span class="kt-font-bold kt-font-' + status[data].state + '">' + status[data].title + '</span>';
                },
            }, {
                targets: 9,
                searchable: false,
                render: function(t, e, a, n) {
                    var s = {
                        1: {
                            title: "Ativo",
                            class: "kt-badge--success"
                        },
                        3: {
                            title: "Inativo",
                            class: "kt-badge--warning"
                        },
                        2: {
                            title: "Alerta",
                            class: "kt-badge--danger"
                        }
                    };
                    return void 0 === s[t] ? t : '<span class="kt-badge ' + s[t].class + ' kt-badge--inline kt-badge--pill kt-badge--rounded">' + s[t].title + "</span>"                    
                }
            }
            ]
            
            ,
		});

		var filter = function() {
			var val = $.fn.dataTable.util.escapeRegex($(this).val());
			table.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
		};

		var asdasd = function(value, index) {
			var val = $.fn.dataTable.util.escapeRegex(value);
			table.column(index).search(val ? val : '', false, true);
		};

		$('#kt_search').on('click', function(e) {
			e.preventDefault();
			var params = {};
			$('.kt-input').each(function() {
				var i = $(this).data('col-index');
				if (params[i]) {
					params[i] += '|' + $(this).val();
				}
				else {
					params[i] = $(this).val();
				}

				
						

				
			});
			
			
			$.each(params, function(i, val) {
				// apply search params to datatable

				var datapura = val.split('|',2);
	
							var data_entrada  = datapura.slice(0,1);
							//var data_entrada_desmonta  = data_entrada.split('/');
						//var data_entrada_format = data_entrada[2] + '/' + data_entrada[1] + '/' + data_entrada[0];

						

							var data_saida  = datapura.slice(1);
							//var data_saida_desmonta  = data_saida.split('/');
							//var data_saida_format = data_saida[2] + '/' + data_saida[1] + '/' + data_saida[0];

							

							console.log('data entrada='+data_entrada);
							document.cookie = "data_entrada="+data_entrada;

							console.log('data saida='+data_saida);
							document.cookie = "data_saida="+data_saida;

							document.cookie = "busca_periodo=sim";

							
							
				table.column(i).search(val ? val : '', false, false);
			});
			table.table().draw();
		});

		$('#kt_reset').on('click', function(e) {
			e.preventDefault();
			$('.kt-input').each(function() {
				document.cookie = "busca_periodo=nao";
				$(this).val('');
				table.column($(this).data('col-index')).search('', false, false);
			});
			table.table().draw();
		});

		$('#kt_datepicker').datepicker({
			todayHighlight: true,
			templates: {
				leftArrow: '<i class="la la-angle-left"></i>',
				rightArrow: '<i class="la la-angle-right"></i>',
			},
		});

	};

	return {

		//main function to initiate the module
		init: function() {
			initTable1();
		},

	};

}();

 jQuery(document).ready(function() {
 	Relatorio_STEP.init();
 });




//===[ inclui registro ]=====






$('#modal_novo_registro').on('show.bs.modal', function (event) {
    //alert("btn event");
    
    var button = $(event.relatedTarget) // Button triggered the modal
    var id_registro =  $("#id_plcode_atual").val();
	

	//====<<
     
      var formulario = "./plcodes/parametros/views/inclui-parametro-full.php"
  
      $.ajax({
          async: true,
           type : 'get', 
           url:formulario,
           data :  'id='+ id_registro, 
           success : function(data){
			 $('#retorno-dados-novo').html(data);
             $('#titulo_modal_cadastro-novo').html("Cadastro Integral");
           } 
           
         });
  
   
  })


  $('#modal_altera_registro').on('hidden.bs.modal', function (event) {
    //alert("btn event");
    

    $("#div_conteudo_menu").load('/plcodes/parametros/views/listar-parametros.php');
   
  })






//===[ altera registro]====



$('#modal_altera_registro').on('show.bs.modal', function (event) {
    //alert("btn event");


    
    var button = $(event.relatedTarget) // Button triggered the modal
    var id_registro = button.data('id')
	var nome_cadastro = button.data('nome');

	//====<<
     
      var formulario = "/plcodes/parametros/views/edita-parametro.php"
  
      $.ajax({
          async: true,
           type : 'get', 
           url:formulario,
           data :  'id='+ id_registro, 
           success : function(data){
			 $('#retorno-dados').html(data);
             $('#titulo_modal_cadastro').html(nome_cadastro);
           } 
           
         });
  
   
  })


  $('#modal_altera_registro').on('hidden.bs.modal', function (event) {
    //alert("btn event");
    

    $("#div_conteudo_menu").load('/plcodes/parametros/views/listar-parametros.php');
   
  })


$('#bt_cancela_edicao_categoria').click(function (e) {
    e.preventDefault();

    $('#modal_altera_registro').modal('hide');

});

$('#bt_add_nova_categoria').click(function (e) {
    e.preventDefault();
    
    var postData = $("#form_nova_categoria").serialize();

    var $nome_suporte =  $("#nome_suporte").val();

    if($nome_suporte==''){


        swal.fire("Erro!","Informe o Nome da Nova Categoria!", "error");

        return false
    }


 //=====
 $.ajax({
    data:postData,
    dataType: 'json',
    type: 'post',
    url: './plcodes/parametros/crud-parametros/crud-parametro.php'
    }).done(function(data) {

        if(data.codigo=='1'){

           // swal.fire("Sucesso!", data.retorno, "success");

           swal.fire("Sucesso!",data.retorno, "success");


           createMetronicToast('Nova Categoria Criada, com Sucesso! '+data.retorno, 5000, 'success', 'bi bi-check2-square');
           KTUtil.scrollTop();
			
           

           $('#modal_altera_registro').modal('hide');
         
setTimeout(function() {
           // $("#div_chat_suporte").load("./suporte/consultas/lista-chat-suporte.php");
           $("#div_conteudo_menu").load('./plcodes/parametros/views/listar-parametros.php');      

}, 2000);

          

           KTUtil.scrollTop();

           // $("#destinatario_direto").val('').trigger('change')

           // $("#nome_suporte").empty().trigger('change')

        }

        if(data.codigo=='0'){
            swal.fire("Erro!", data.retorno, "error");

        }
    

    console.log(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        // If fail
        console.log(textStatus + ': ' + errorThrown);
        swal.fire("Erro!", textStatus, "error");
    });

//=====





});





$(document).on("click",".apaga_registro_parametro", function (e) {
    var id_periodo_checkin = $(this).data('id'); // or var clickedBtnID = this.id



    swal.fire({
        title: 'Eliminar este Indicador?',
        text: "Esta ação não poderá ser desfeita!",
        html:"<b>ID: "+id_periodo_checkin+"</b> <br/> Esta ação não poderá ser desfeita!",
        type: 'warning',
        showCancelButton: true,
        
        confirmButtonText: "Sim Eliminar!",
        cancelButtonText: "Não, Cancelar",
        cancelButtonClass: "btn btn-default",
        confirmButtonClass: "btn btn-brand"
    }).then(function(result) {
        if (result.value) {



            var formulario = "/plcodes/parametros/crud-parametros/crud-parametro.php"
  
            $.ajax({
               
                 type : 'post', 
                 dataType:"json",
                 url:formulario,
                 data :  'id='+ id_periodo_checkin+'&acao=apaga_parametro', 
                 success : function(data){


                    if(data.codigo=='1'){
                        createMetronicToast('Inativação de Parâmetro! '+data.mensagem, 5000, 'success', 'bi bi-check2-square');

                        $("#div_conteudo_menu").load("./plcodes/parametros/views/listar-parametros.php");

                        
                     
							
                        KTUtil.scrollTop();
                       

                    }


                    if(data.codigo=='0'){

                        swal.fire({
                            title: "Proteção dos Dados",
                            html:  data.mensagem,
                            type: "error",
                            confirmButtonClass: "btn btn-success"
                        })

                        $("#div_conteudo_menu").load("/plcodes/parametros/views/listar-parametros.php");

                    }
    

                 },         
                 error: function (data) {
                                        console.log('Falha no Processamento dos Dados.');
                                        console.log(data);
                        
                                            swal.fire(
                                                'Erro!',
                                                data.responseText,
                                                'error'
                                            )

                                            $("#div_conteudo_menu").load("/plcodes/parametros/views/listar-parametros.php");
                       
                    
                }

                 
               });


            
        } else {  $("#div_conteudo_menu").load("/plcodes/parametros/views/listar-parametros.php"); }
    });



   // alert('you clicked on button #' + clickedBtnID);
 });
