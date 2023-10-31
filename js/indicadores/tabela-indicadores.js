"use strict";

// Class definition
var KTIndicadores = function () {

    var initDatatable = function () {
        const table = document.getElementById('tabela_indicadores');

        // set date data order
        const tableRows = table.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            const realDate = moment(dateRow[1].innerHTML, "MMM D, YYYY").format();
            dateRow[1].setAttribute('data-order', realDate);
        });

        // init datatable --- more info on datatables: https://datatables.net/manual/
        const datatable = $(table).DataTable({
            "language": {
                "lengthMenu": "Exibir _MENU_",
               },
            "info": false,
            'order': [],
            "paging": true,
            "dom":
  "<'row'" +
  "<'col-sm-6 d-flex align-items-center justify-conten-start'l>" +
  "<'col-sm-6 d-flex align-items-center justify-content-end'f>" +
  ">" +

  "<'table-responsive'tr>" +

  "<'row'" +
  "<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
  "<'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
  ">"
        });

    }

    // Public methods
    return {
        init: function () {
            initDatatable();
        }
    }
}();


// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTIndicadores.init();
});





//===[ inclui registro ]=====






$('#modal_novo_registro').on('show.bs.modal', function (event) {
    //alert("btn event");
    
    var button = $(event.relatedTarget) // Button triggered the modal
    var id_registro =  $("#id").val();
	

   var plcode_atual =  KTCookie.get("plcode_atual");

	//====<<
     
    var formulario = "../../views/projetos/plcodes/indicadores/inclui-indicador.php"
  
      $.ajax({
          async: true,
           type : 'get', 
           url:formulario,
           data :  'id='+ id_registro, 
           success : function(data){
			 $('#retorno-dados-novo').html(data);
             //$('#titulo_modal_cadastro-novo').html("para o Indicador: "+id_registro);
             $('#id_plcode_cadastro_indicador').val(plcode_atual);
             
           } 
           
         });
  
   
  })

  $('#modal_novo_registro').on('hidden.bs.modal', function (event) {
    //alert("btn event");
    

    $("#div_modulo_indicadores").load('../../views/projetos/plcodes/indicadores/tabela-indicadores.php');
   
  })


  $('#modal_altera_registro').on('hidden.bs.modal', function (event) {
    //alert("btn event");
    

    $("#div_modulo_indicadores").load('../../views/projetos/plcodes/indicadores/tabela-indicadores.php');
   
  })






//===[ altera registro]====


$('#modal_altera_registro').on('show.bs.modal', function (event) {
    //alert("btn event");
    
    var button = $(event.relatedTarget); // Button triggered the modal
    var id_registro = button.data('id');
	var nome_cadastro = button.data('nome');


	//====<<
     
      var formulario = "../../views/projetos/plcodes/indicadores/edita-indicador.php?id="+id_registro;
  
      $.ajax({
          async: true,
           type : 'get', 
           url:formulario,
           data :  'id='+ id_registro, 
           success : function(data){
			 $('#retorno-dados').html(data);
             $('#titulo_modal_cadastro').html(nome_cadastro);
             $('#id_indicador_atual').val(id_registro);
           } 
           
         });
  
   
  })


  $('#modal_altera_registro').on('hidden.bs.modal', function (event) {
    //alert("btn event");
    

    $("#div_modulo_indicadores").load('../../views/projetos/plcodes/indicadores/tabela-indicadores.php');
   
  })





  

$(document).on("click",".exclui_indicador", function (e) {
    var id_indicador = $(this).data('id'); // or var clickedBtnID = this.id



    swal.fire({
        title: 'Eliminar este Indicador?',
        text: "Esta ação não poderá ser desfeita!",
        html:"<b>ID: "+id_indicador+"</b> <br/> O STEP irá primeiro verificar as dependências de leituras ativas e checkin`s realizados <span class='kt-font-info'> antes da Eliminação do Cadastro deste Indicador.</span> <br/><span class='kt-font-danger'>Esta ação não poderá ser desfeita!</span>",
        type: 'warning',
        showCancelButton: true,
        icon: "info",
        confirmButtonText: "Sim Eliminar!",
        cancelButtonText: "Não, Cancelar",
        cancelButtonClass: "btn btn-default",
        confirmButtonClass: "btn btn-brand",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: 'btn btn-danger'
        }
    }).then(function(result) {
        if (result.value) {



            var formulario = "../../crud/indicadores/action-indicadores.php"
  
            $.ajax({
               
                 type : 'post', 
                 dataType:"json",
                 url:formulario,
                 data :  'id='+ id_indicador+'&acao=apaga_indicador', 
                 success : function(data){


                    if(data.codigo=='1'){

                        $("#div_modulo_indicadores").load("../../views/projetos/plcodes/indicadores/tabela-indicadores.php");

                                     
                        Swal.fire({
                            icon: 'success',
                            html:  'ID: <b>'+id_indicador+'</b> | '+data.mensagem,
                            timer: 2000, // tempo em milissegundos
                            showConfirmButton: false, // não exibir botão de confirmação
                          }).then(function(){
                            // código para fechar a janela
                            window.close(); // fecha a janela atual

                          
                          });

                       
                    }


                    if(data.codigo=='0'){

                        swal.fire({
                            title: "Proteção dos Dados",
                            html:  data.mensagem,
                            type: "error",
                            confirmButtonClass: "btn btn-success"
                        })

                        $("#div_modulo_indicadores").load("../../views/projetos/plcodes/indicadores/tabela-indicadores.php");

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

                                            $("#div_modulo_indicadores").load("../../views/projetos/plcodes/indicadores/tabela-indicadores.php");
                       
                    
                }

                 
               });


            
        } else {  $("#div_modulo_indicadores").load(".../../views/projetos/plcodes/indicadores/tabela-indicadores.php"); }
    });



   // alert('you clicked on button #' + clickedBtnID);
 });

