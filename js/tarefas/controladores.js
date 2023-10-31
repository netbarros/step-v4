

$('#tipo_tarefa').on('select2:select', function (e) {
    e.preventDefault();

    var tipo_tarefa = e.target.value;

    var projeto_tarefa = $("#projeto_tarefa").val();

    
    

    //alert($(this).val());
    // alert(tipo_checkin);
   

    if (tipo_tarefa == "" || tipo_tarefa == null) {

        swal.fire("Erro!", "Falha ao Criar o Select! Não foi possível verificar o Tipo de Checkin Desejado.", "error");

        return false

    }


    if(projeto_tarefa==''){

        swal.fire("Erro!", "Selecione o Projeto para prosseguir.", "error");
        return false
    }

    /*
1 = Presencial
2 = Leitura
3 = Tarefa Agendada

    */
    
   
    
    if (tipo_tarefa == 'ponto_plcode') {

        console.log("tipo_tarefa = " + tipo_tarefa);

        $("#div_atribuir_tarefa").addClass("d-none");

// busca os plcodes relacionados ao projeto, para leitura presencial.
        
        var projeto_tarefa = $("#projeto_tarefa").val();

        

        $('#div_indicador_tarefa').addClass("d-none");
        $('#indicador_tarefa').addClass("d-none");

        
        $('#div_tarefas_checkin').removeClass("d-none");
        $('#plcode_tarefa').removeClass("d-none");

       
        // faço a busca e preencho o select do plcode composto:
        let dropdown_parametro_checkin = $('#plcode_tarefa');

        $.ajax({
            type: "GET",
            url: '../../crud/tarefas/monta_selects.php?montar=checkin_plcode&id=' + projeto_tarefa,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data) {

                options = "<option " + "value='0'>Selecione o <small>PLCode</small></option>');";


                $.each(data, function (i, v) {

                    console.log(v);
                    options += "<option " + "value='" + v.id_ponto + "'>"+ v.nome_estacao +' - '+ v.nome_ponto +'-'+ v.id_ponto + "</option>');";

                   

                });

                // atualizo o conteudo do select:
                $('#plcode_tarefa').html(options);

         
                // Append that to the DropDownList.
                


                
               

            },
            failure: function () {
                swal.fire("Erro!", "Falha ao Criar o Select! Não foi possível verificar se há PLCode existente.", "error");

              
            }
        });
        

                          // Classe para Select Bootstrap Dinâmico ==<<

        

    }

    if (tipo_tarefa == 'ponto_parametro') {
        $("#div_atribuir_tarefa").addClass("d-none");

        var projeto_tarefa = $("#projeto_tarefa").val();

        console.log("tipo_tarefa = " + tipo_tarefa);

        $('#div_indicador_tarefa').removeClass("d-none");
        $('#indicador_tarefa').removeClass("d-none");


        // faço a busca e preencho o select do plcode composto:
        let dropdown_parametro_checkin = $('#plcode_tarefa');

        $.ajax({
            type: "GET",
            url: '../../crud/tarefas/monta_selects.php?montar=checkin_plcode&id=' + projeto_tarefa,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data) {

                options = "<option " + "value='0'>Selecione o <small>PLCode</small></option>');";


                $.each(data, function (i, v) {

                    console.log(v);
                    options += "<option " + "value='" + v.id_ponto + "'>" + v.nome_estacao + ' - ' + v.nome_ponto + "</option>');";



                });

                // atualizo o conteudo do select:
                $('#plcode_tarefa').html(options);

                $('#div_tarefas_checkin').removeClass("d-none");
                $('#plcode_tarefa').removeClass("d-none");

                // Append that to the DropDownList.






            },
            failure: function () {
                swal.fire("Erro!", "Falha ao Criar o Select! Não foi possível verificar se há PLCode existente.", "error");

                
            }
        });
    }

    if (tipo_tarefa == 'tarefa_agendada') {
        $("#div_data_tarefa").removeClass("d-none");
        $("#div_atribuir_tarefa").removeClass("d-none");
      
        $("#div_tarefas_checkin").addClass("d-none");
    }
    
})



$('#projeto_tarefa').on('select2:select', function (e) {

    $("#div_atribuir_tarefa").addClass("d-none");

    var projeto_tarefa = $("#projeto_tarefa").val();

    console.log("tipo_tarefa = " + tipo_tarefa);

    $('#div_indicador_tarefa').addClass("d-none");
    $('#indicador_tarefa').addClass("d-none");


    // faço a busca e preencho o select do plcode composto:
    let dropdown_parametro_checkin = $('#plcode_tarefa');

    $.ajax({
        type: "GET",
        url: '../../crud/tarefas/monta_selects.php?montar=checkin_plcode&id=' + projeto_tarefa,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (data) {


            if(data.length === 0) {


                console.log('A resposta da requisição está vazia');

                options = "<option " + "value='0'>Nenhum PLCode cadastrado para este Projeto.</option>');";

            } else {

                

            options = "<option " + "value='0'>Selecione o <small>PLCode</small></option>');";


            $.each(data, function (i, v) {

                console.log(v);
                options += "<option " + "value='" + v.id_ponto + "'>" + v.nome_estacao + ' - ' + v.nome_ponto + "</option>');";



            });

         
            // Append that to the DropDownList.


                console.log('A resposta da requisição contém dados');


            }


               // atualizo o conteudo do select:
               $('#plcode_tarefa').html(options);

            





        },
        failure: function () {
            swal.fire("Erro!", "Falha ao Criar o Select! Não foi possível verificar se há PLCode existente.", "error");

           
        }
    });
})

$('#plcode_tarefa').on('select2:select', function (e) {
    e.preventDefault();

    var plcode_tarefa = e.target.value;

    //alert($(this).val());
    // alert(tipo_checkin);
    console.log("plcode_tarefa = " + plcode_tarefa);

    if (plcode_tarefa == "" || plcode_tarefa == null) {

        swal.fire("Erro!", "Falha ao Criar o Select! Não foi possível verificar o Tipo de Checkin Desejado.", "error");

        return false

    }

    /*
1 = Presencial
2 = Leitura
3 = Tarefa Agendada

    */

    if (plcode_tarefa!= '') {

        // busca os plcodes relacionados ao projeto, para leitura presencial.

        var plcode_tarefa = $("#plcode_tarefa").val();


        // faço a busca e preencho o select do plcode composto:
        let dropdown_parametro_checkin = $('#indicador_tarefa');

        $.ajax({
            type: "GET",
            url: '../../crud/tarefas/monta_selects.php?montar=checkin_indicador&id=' + plcode_tarefa,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data) {


                if(data.length === 0) {

                    options = "<option " + "value='0'>Nenhum indicador cadastrado para o PLCode selecionado.</option>');";


                } else {

                    options = "<option " + "value='0'>Selecione o <small>Indicador</small></option>');";


                    $.each(data, function (i, v) {
    
                        console.log(v);
                        options += "<option " + "value='" + v.id_parametro + "'>" + v.nome_parametro + "</option>');";
    
    
    
                    });


                }

              

              


                let tipo_tarefa = $("#tipo_tarefa").val();

                console.log("Tipo de tarefa:"+tipo_tarefa);

                if (tipo_tarefa == "ponto_parametro") {
                    
                    $('#div_indicador_tarefa').removeClass("d-none");
                    $('#indicador_tarefa').removeClass("d-none");

                      // atualizo o conteudo do select:
                $('#indicador_tarefa').html(options);

                }

              //  $('#div_indicador_tarefa').removeClass("d-none");
              //  $('#indicador_tarefa').removeClass("d-none");

                // Append that to the DropDownList.






            },
            failure: function () {
                swal.fire("Erro!", "Falha ao Criar o Select! Não foi possível verificar se há PLCode existente.", "error");

                $('#indicador_tarefa').selectpicker('setStyle', 'btn-success');
            }
        });


        // Classe para Select Bootstrap Dinâmico ==<<



    }


})


ciclo_leitura_valida_edição = $('#ciclo_leitura').val();

if(ciclo_leitura_valida_edição == '2'){
    var id_periodo_tarefa = $("#periodo_pontoxTarefa").data("id_periodo_tarefa");

    $('#dia_semana').select2({
       
    });

    $("#div_tarefas_periodo").removeClass("d-none");
    
    $("#div_dia_realizacao_tarefa").removeClass("d-none");

    $("#dia_semana").removeClass("d-none");
    

    $("#div_data_tarefa").addClass("d-none");
    $("#div_atribuir_tarefa").addClass("d-none");

    var projeto_tarefa = $("#projeto_tarefa").val();
    // Para obter o valor do atributo data-id_periodo_tarefa
   var id_periodo_tarefa = $("#periodo_pontoxTarefa").data("id_periodo_tarefa");

    
    $.ajax({
        url: '/crud/tarefas/monta_selects.php?montar=dias_tarefa&id=' + id_periodo_tarefa,
        type: 'GET',
        dataType: 'html',  // Altere 'text/html' para 'html'
        success: function(data) {
            console.log('Data recebida:', data);
            $("#dia_semana").html(data);
            $("#dia_semana").select2();
        },
        error: function(xhr, status, error) {
            console.log("Erro na requisição: ", error);
        }
    });

}


$('#ciclo_leitura').on('select2:select', function (e) {
    e.preventDefault();
// o ciclo de leitura é a recorrência da tarefa, se for semanal, abre a seleção para escolher os dias da semana.

    var ciclo_leitura = $(this).val();

    if (ciclo_leitura == '0') { // tarefa unica

        $("#div_dia_realizacao_tarefa").removeClass("d-none");
        $("#div_tarefas_periodo").addClass("d-none");
        $("#div_data_tarefa").removeClass("d-none");
       
        
        $("#div_atribuir_tarefa").removeClass("d-none");

        Swal.fire({
            title: "Recorrência: Tarefa Única",
            html: "Como você selecionou <strong>Tarefa Única</strong>, verifique o Agendamento para ter <span class='text-warning'>Horário Agendado</span> na seleção.",
            icon: "info",
            buttonsStyling: false,
            confirmButtonText: "Ok, farei isso!",
            customClass: {
                confirmButton: "btn btn-light"
            }
        }).then(function () {
            $("#div_dia_realizacao_tarefa").addClass("d-none");
        $("#div_tarefas_periodo").addClass("d-none");
        $('#dia_semana').addClass("d-none");
        
            KTUtil.scrollTop();
        });


    } else if (ciclo_leitura == '1') { // tarefa diária

        $("#div_data_tarefa").addClass("d-none");
       
        $("#div_atribuir_tarefa").addClass("d-none");
             
        $("#div_dia_realizacao_tarefa").addClass("d-none");
        $("#div_tarefas_periodo").addClass("d-none");
        $('#dia_semana').addClass("d-none");

    } else if (ciclo_leitura == '2'){ // tarefa semanal
        $('#dia_semana').select2({
       
        });

        $("#div_tarefas_periodo").removeClass("d-none");
        
        $("#div_dia_realizacao_tarefa").removeClass("d-none");

        $("#dia_semana").removeClass("d-none");
        

        $("#div_data_tarefa").addClass("d-none");
        $("#div_atribuir_tarefa").addClass("d-none");

        var projeto_tarefa = $("#projeto_tarefa").val();
        // Para obter o valor do atributo data-id_periodo_tarefa
       var id_periodo_tarefa = $("#periodo_pontoxTarefa").data("id_periodo_tarefa");

        
      
        $.ajax({
            url: '/crud/tarefas/monta_selects.php?montar=dias_tarefa&id=' + id_periodo_tarefa,
            type: 'GET',
            dataType: 'html',  // Altere 'text/html' para 'html'
            success: function(data) {
                console.log('Data recebida:', data);
                $("#dia_semana").html(data);
                $("#dia_semana").select2();
            },
            error: function(xhr, status, error) {
                console.log("Erro na requisição: ", error);
            }
        });
        
   

    

        
       
    }

})

$('#modo_checkin').on('select2:select', function (e) {
    e.preventDefault();
    // o ciclo de leitura é a recorrência da tarefa, se for semanal, abre a seleção para escolher os dias da semana.
    var modo_checkin = e.target.value;

    if (modo_checkin == '1') { // horário livre

        $("#div_data_tarefa").addClass("d-none");
        $("#div_tarefas_periodo").addClass("d-none");
       
        
        $("#div_horario_realizacao_tarefa").addClass("d-none");

    } else if (modo_checkin == '2') {  // horário agendado (controlado)
        $("#div_data_tarefa").removeClass("d-none");
       
        $("#div_tarefas_periodo").removeClass("d-none");

        
        $("#div_horario_realizacao_tarefa").removeClass("d-none");


    }

})


Inputmask({
    "mask": "99:99"
}).mask("#horario_tarefa");





// $('#kt_modal_new_target').on('hide.bs.modal', function () {
//   location.reload();
//   })