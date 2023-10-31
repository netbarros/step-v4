


$(document).ready(function () {

    $('.kt-selectpicker').selectpicker();

    $('#collapseEstacao').on('shown.bs.collapse', function () {

        $('.selectpicker').selectpicker('show');


        //$("#estacao_selecionada_regra .kt-selectpicker").selectpicker('show');

    })

    $(".fecha_form_atualiza_regra").click(function (e) {
        e.preventDefault();

        $("#div_edita_cockpit").addClass("kt-hide").fadeOut('1000');
    })

    //===== atualiza regra/cockpit selecionado ==>
    $("#bt_salva_cockpit").click(function (e) {

        e.preventDefault();

        var dados = $("#kt_modal_create_cockpit_form").serialize();

        var estacao_selecionada_regra = $('#estacao_cockpit').val();

        var nome_regra = $('#nome_regra').val();

        var modelo_grafico = $("input[name='modelo_grafico']").val();

        var indicador_lista_regra = $('#indicador_lista_regra').val();

        var indicador_unico_regra = $('#indicador_unico_regra').val();



        if (estacao_selecionada_regra == '' || nome_regra == '' || modelo_grafico == '') {

            swal.fire("Dados Incompletos!", "Dados Básicos ausentes, por favor, verifique o Formulário.", "error");

            return false

        }

        if (modelo_grafico == '5') {

            if (indicador_unico_regra == '') {

                swal.fire("Dados Incompletos!", "Selecione Corretamente o Indicador Desejado.", "error");

                return false

            }
        }
        if (modelo_grafico != '5' && modelo_grafico == '') {

            if (indicador_lista_regra == '') {

                swal.fire("Dados Incompletos!", "Verifique o Modelo de Gráfico Desejado e os Indicadores para Acompanhamento.", "error");

                return false


            }

        }


        $.ajax({
            type: "POST",
            url: "./dashboard/crud/action-item-regra-cockpit.php",
            data: dados,
            dataType: "json",
            cache: false,
            beforeSend: function () {

                KTApp.block('#form_edita_regra_acompanhamento', {
                    overlayColor: '#000000',
                    type: 'v2',
                    state: 'success',
                    message: 'Atualizando Dados ...'
                });


            },

            success: function (data) {

                console.log('Item CockPit Atualizado com Sucesso.');

                console.log(data);

                console.log(data.codigo)


                if (data.codigo == 1) {



                    swal.fire("Parabéns!", data.retorno, "success");

                    $("#div_edita_cockpit_form").addClass("kt-hide").fadeOut();

                    $("#div_edita_cockpit").addClass("kt-hide");

                    KTApp.unblock('#form_edita_regra_acompanhamento');




                    $('#retorno_lista_cockpit').load("./dashboard/views/regra-acompanhamento.php?acao=listar");



                }
                if (data.codigo == 0) {
                    KTApp.unblock('#form_edita_regra_acompanhamento');


                    swal.fire("Ops!", data.retorno, "warning");



                }
                e.stopImmediatePropagation();

            },
            error: function (data) {

                swal.fire("Erro!", "Não foi Possível Prosseguir!" + data.retorno, "error");

                console.log('Falha no Processamento dos Dados.');


                e.stopImmediatePropagation();


            }

        });


    })

    //====salva regra, novo item do cockpit ---->		
    $("#bt_salva_regra").click(function (e) {
        e.preventDefault();

        var dados = $("#form_nova_regra_acompanhamento").serialize();

        var estacao_selecionada_regra = $('#estacao_selecionada_regra').val();

        var nome_regra = $('#nome_regra').val();

        var modelo_grafico = $("input[name='modelo_grafico']").val();

        var indicador_lista_regra = $('#indicador_lista_regra').val();

        var indicador_unico_regra = $('#indicador_unico_regra').val();



        if (estacao_selecionada_regra == '' || nome_regra == '' || modelo_grafico == '') {

            swal.fire("Dados Incompletos!", "Dados Básicos ausentes, por favor, verifique o Formulário.", "error");

            return false

        }

        if (modelo_grafico == '5') {

            if (indicador_unico_regra == '') {

                swal.fire("Dados Incompletos!", "Selecione Corretamente o Indicador Desejado.", "error");

                return false

            }
        }
        if (modelo_grafico != '5' && modelo_grafico == '') {

            if (indicador_lista_regra == '') {

                swal.fire("Dados Incompletos!", "Verifique o Modelo de Gráfico Desejado e os Indicadores para Acompanhamento.", "error");

                return false


            }

        }


        $.ajax({
            type: "POST",
            url: "./dashboard/crud/action-item-regra-cockpit.php",
            data: dados,
            dataType: "json",
            cache: false,
            beforeSend: function () {

                KTApp.block('#form_nova_regra_acompanhamento', {
                    overlayColor: '#000000',
                    type: 'v2',
                    state: 'success',
                    message: 'Salvando Dados ...'
                });


            },

            success: function (data) {

                console.log('Novo Item CockPit Criado com Sucesso.');

                console.log(data);

                console.log(data.codigo)


                if (data.codigo == 1) {

                    KTApp.unblock('#form_nova_regra_acompanhamento');

                    swal.fire("Parabéns!", data.retorno, "success");

                    $("#modal_regra").modal("hide");




                }
                if (data.codigo == 0) {
                    KTApp.unblock('#form_nova_regra_acompanhamento');
                    $("#modal_regra").modal("hide");

                    swal.fire("Ops!", data.retorno, "warning");



                }

                e.stopImmediatePropagation();


            },
            error: function (data) {

                swal.fire("Erro!", "Não foi Possível Prosseguir!" + data.retorno, "error");

                console.log('Falha no Processamento dos Dados.');


                e.stopImmediatePropagation();


            }

        });


    })



    $("#estacao_selecionada_regra").change(function () {

        var nome_estacao = $("#estacao_selecionada_regra option:selected").text();

        var id_estacao = $("#estacao_selecionada_regra option:selected").val();

        Cookies.set('estacao_operador', id_estacao);

        Cookies.set('nome_estacao_operador', nome_estacao);

        $('.collapse').collapse('hide');



        $("#nome_estacao_selecionada_regra").html('<i class="flaticon-pie-chart-1"></i> ' + nome_estacao);

        //$('#indicador_unico_regra').load("./dashboard/consultas/consulta-indicador.php");






        $('#div_conteudo_regra').removeClass("kt-hide");

    });







    $("#bt_altera_indicador").click(function () {



        $('#div_indicador_unico_selecionado').addClass("kt-hide");

        $('#div_lista_indicadores_regra').addClass("kt-hide");

        $('#div_indicador_regra').removeClass("kt-hide");

        //	$('#indicador_unico_regra').selectpicker('render');

        $('#periodo_analise_regra').selectpicker('val', '0');

        $('#periodo_analise_regra').prop('disabled', false);
        $('#periodo_analise_regra').selectpicker('refresh');


        $('#div_select_indicador_unico_regra').load("./dashboard/consultas/consulta-indicador.php?acao=unico&id_estacao=" + id_temp_estacao);


    });



    $("#modelo_grafico-1").click(function () {

        $('#div_indicador_regra').addClass("kt-hide");

        $('#div_lista_indicadores_regra').removeClass("kt-hide");

        $('#modelo_grafico_alterado').val('1');




        $('#div_select_lista_indicadores_regra').load("./dashboard/consultas/consulta-indicador.php?acao=lista&id_estacao=" + id_temp_estacao);

        $('#div_select_lista_indicadores_regra').focus();

        $('#periodo_analise_regra').prop('disabled', false);
        $('#periodo_analise_regra').selectpicker('refresh');
    })

    $("#modelo_grafico-2").click(function () {


        $('#modelo_grafico_alterado').val('2');

        $('#div_indicador_regra').addClass("kt-hide");
        $('#div_lista_indicadores_regra').removeClass("kt-hide");


        $('#div_select_lista_indicadores_regra').load("./dashboard/consultas/consulta-indicador.php?acao=lista&id_estacao=" + id_temp_estacao);

        $('#periodo_analise_regra').prop('disabled', false);
        $('#periodo_analise_regra').selectpicker('refresh');

    })

    $("#modelo_grafico-3").click(function () {


        $('#modelo_grafico_alterado').val('3');

        $('#div_indicador_regra').addClass("kt-hide");
        $('#div_lista_indicadores_regra').removeClass("kt-hide");

        $('#div_select_lista_indicadores_regra').load("./dashboard/consultas/consulta-indicador.php?acao=lista&id_estacao=" + id_temp_estacao);

        $('#periodo_analise_regra').prop('disabled', false);
        $('#periodo_analise_regra').selectpicker('refresh');

    })

    $("#modelo_grafico-4").click(function () {

        $('#modelo_grafico_alterado').val('4');

        $('#div_indicador_regra').addClass("kt-hide");


        $('#div_lista_indicadores_regra').removeClass("kt-hide");


        $('#div_select_lista_indicadores_regra').load("./dashboard/consultas/consulta-indicador.php?acao=lista&id_estacao=" + id_temp_estacao);

        $('#periodo_analise_regra').prop('disabled', false);
        $('#periodo_analise_regra').selectpicker('refresh');

    })


    $("#modelo_grafico-5").click(function () {

        $('#modelo_grafico_alterado').val('5');

        $('#div_lista_indicadores_regra').addClass("kt-hide");

        $('#div_indicador_regra').removeClass("kt-hide");

        //	$('#indicador_unico_regra').selectpicker('render');

        $('#periodo_analise_regra').selectpicker('val', '0');

        $('#periodo_analise_regra').prop('disabled', false);
        $('#periodo_analise_regra').selectpicker('refresh');


        $('#div_select_indicador_unico_regra').load("./dashboard/consultas/consulta-indicador.php?acao=unico&id_estacao=" + id_temp_estacao);





    })





});

	//Chave API Geral STEP do Google: AIzaSyB0w-dRBF9x2Dc4oQt_TNZB6BGTaJMkRKs

	// nova AIzaSyAQsOKlWz3MbMeQHMrfAEtVR7ajrSj9274



    

   
    
        

  