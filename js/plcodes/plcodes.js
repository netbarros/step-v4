"use strict";

function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}


//-----------------------------------------------------


var plcode_atual = null;

// Obter o parâmetro id_plcode da URL
var urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('id_plcode') && !isNaN(urlParams.get('id_plcode'))) {
    plcode_atual = parseInt(urlParams.get('id_plcode'), 10);
} else {
    // Obter o cookie plcode_atual
    var plcode_atual = document.cookie.split('; ').find(row => row.startsWith('plcode_atual='));
    if (plcode_atual && !isNaN(plcode_atual.split('=')[1])) {
        plcode_atual = parseInt(plcode_atual.split('=')[1], 10);
    }
}

if (plcode_atual === null) {
    // Manipule o erro aqui, como redirecionar para outra página ou mostrar uma mensagem de erro
    console.error("Erro ao receber PLCode Atual!");
    // Você pode usar o Metronic para exibir uma notificação ou modal de erro, se desejar
} else {

    $('#div_modulo_indicadores').load('../../views/projetos/plcodes/indicadores/tabela-indicadores.php?id_plcode=' + plcode_atual);

}


//-----------------------------------------------------

var quill = new Quill('#inclui_instrucao_operacional_plcode', {
	modules: {
		toolbar: [
			[{
				header: [1, 2, false]
			}],
			['bold', 'italic', 'underline'],
			['image', 'code-block']
		]
	},
	placeholder: 'Observações...',
	theme: 'snow' // or 'bubble'
});



        $("#kt_add_plcode_submit").click(function (e) {


         

            e.preventDefault();

            if( $("#plcode_nome").val()=='' || $("#nucleo_plcode").val()==''|| $("#kt_add_plcode_status_select").val()==''|| $("#objetivo_plcode").val()==''){

                swal.fire("Ops!", 'Campos Obrigatórios não preenchidos! Verifique seu formulário.', "warning");


                return false
            }

            $("#texto_instrucao_operacional_plcode").val($("#inclui_instrucao_operacional_plcode").html());

        var dados = $("#kt_add_plcode_form").serialize();

        var caminho = "/crud/plcodes/action-plcode.php";
        $.ajax({
            type: "POST",
            url: caminho,
            data: dados,
            dataType: "json",
            cache: false,
        
        
            success: function (data) {
        
                console.log(data);
        
                console.log(data.codigo)


                if (data.codigo == 'tipo_tanque') {

                    swal.fire("Ops!", data.mensagem, "warning");

                    return false;
                }

                
        
        
                if (data.codigo == 1) {
        
        
                    createMetronicToast('Novo PlCode',' Validando a 2ª Etapa ', 5000, 'success', 'bi bi-check2-square');
							
                    

                    Swal.fire({
                        icon: 'success',
                        html: data.mensagem,
                        timer: 2000, // tempo em milissegundos
                        showConfirmButton: false, // não exibir botão de confirmação
                      }).then(function(){
                        // código para fechar a janela
                       // KTUtil.scrollDown();
                        window.close(); // fecha a janela atual

                      
                      });


                    $("#acao_cadastro").val('novo_plcode_passo_2');

                    $("#kt_add_plcode_submit").html('Salvar Definições Avançadas');

                    $("#id_plcode_atual").val(data.id_novo_plcode);

                    var plcode_atual = data.id_novo_plcode;

                    var date = new Date(Date.now() +  1 * (60 * 60 * 1000) ); // 1 hora
                    var options = { expires: date };
                    KTCookie.set("plcode_atual", plcode_atual, options);


                    $('#div_modulo_indicadores').load('/views/projetos/plcodes/indicadores/tabela-indicadores.php?id_plcode='+plcode_atual );
        
        
                }

                if (data.codigo == 11) {
        
        
                  

                    createMetronicToast('Finalizando Cadastro do PlCode: ', data.mensagem, 5000, 'success', 'bi bi-check2-square');
							
                    KTUtil.scrollTop();


                    //swal.fire("Indicadores!", "Cadastro de Indicadores, liberado!", "success");

                    $("#acao_cadastro").val('novo_plcode_passo_2');

                    $("#acao_cadastro").val('novo_plcode_finaliza');

                    $("#kt_add_plcode_submit").html('Finalizar Cadastro');

                    $("#id_plcode_atual").val(data.id_novo_plcode);

                    var id_plcode = data.id_novo_plcode;
                    
                    var date = new Date(Date.now() + 1 * 24 * 60 * 60 * 1000); // +2 day from now
                    var options = { expires: date };
                    KTCookie.set("plcode_atual", id_plcode, options);
                    
                    const element_indicadores = document.querySelector('#aba_kt_add_plcode_indicadores');
                    element_indicadores.click();

                    $('#div_modulo_indicadores').load('/views/projetos/plcodes/indicadores/tabela-indicadores.php?id_plcode='+id_plcode);


                    function GeraQRCode()		
                    {
                        var plcode = id_plcode;
                        var conteudo= "https://step.eco.br/?p="+plcode;
                    var GoogleCharts = 'https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=';
                    var imagemQRCode = GoogleCharts + conteudo;
                    document.getElementById('imageQRCode').src = imagemQRCode;

                    var imagemQRCodetooltip = document.getElementById('imageQRCode');
              // Adicione um tooltip Bootstrap ao elemento
            $(imagemQRCodetooltip).tooltip({
                title: 'Este QRCode foi gerado automaticamente ao Salvar suas Definições Avançadas, para gerar sua Impressão entre na Edição ou em Relatórios de PLCodes'
            });

                    console.log(imagemQRCode);
                    
                    }

                    GeraQRCode();
        
        
                }
                if (data.codigo == 0) {
        
                    swal.fire("Ops!", data.mensagem, "warning");

                    return false
        
                }

                if (data.codigo == 10) {
        
                    swal.fire("Ops!", data.mensagem, "warning");

                    return false
        
                }

                if (data.codigo == 33) {


                    createMetronicToast('Novo PlCode: ', data.mensagem, 5000, 'success', 'bi bi-check2-square');
							
                    KTUtil.scrollTop();
                      
                     
                    

                   setTimeout(() => {

                    KTCookie.remove("plcode_atual");
                    location.reload();
                    
                    
                   }, 2000);
        
                }
        
                e.stopImmediatePropagation();
        
        
            },
            error: function (data) {
        
                swal.fire("Erro!", "Não foi Possível Prosseguir!" + data.mensagem, "error");
        
                console.log('Falha no Processamento dos Dados.');
        
        
                e.stopImmediatePropagation();
        
                return false
            }
        
        });
    })


//=====================[ Select Estação ]=========================================================//

$('.tipo_plcode').on('change', function(e) {

    var tipo_escolhido = e.target.value;

if(tipo_escolhido=='1'){

    $("#plcode_tipo_tanque").removeClass("d-none");
    $("#plcode_tipo_equipamento").addClass("d-none");
    $("#plcode_tipo_instrumento").addClass("d-none");


}

if(tipo_escolhido=='2'){

    $("#plcode_tipo_equipamento").removeClass("d-none");
    $("#plcode_tipo_instrumento").addClass("d-none");
    $("#plcode_tipo_tanque").addClass("d-none");
    

}


if(tipo_escolhido=='3'){


   
    $("#plcode_tipo_instrumento").removeClass("d-none");
    $("#plcode_tipo_tanque").addClass("d-none");
    $("#plcode_tipo_equipamento").addClass("d-none");
    

}
    

})

$('#nucleo_plcode').on('change', function(e) {


    var estacao_escolhida = e.target.value;


   // let dropdown_estacao = $('#estacao_escolhida');




    if (estacao_escolhida == '') {


        swal.fire({
            "title": "Ops!",
            "text": "Informe a Estação de Operação",
            "type": "info",
            "confirmButtonClass": "btn btn-secondary"
        });


    } else {


        console.log("Selecionou a estacao= " + estacao_escolhida);

        /*
        // ja sabemos a estacao de operacao, vamos listar os plcodes existentes da mesma estacao, para que o usuario
        // possa informar a sequencia dos plcodes desta estacao de operacao:
        */

        let dropdown_plcode_anterior = $('#id_plcode_anterior'); // defino o select que receberá o retorno da consulta

        $.ajax({
            type: "GET",
            url: '../../crud/plcodes/monta_selects.php?montar=estacao_select_plcode_anterior&id=' + estacao_escolhida,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {

                let options = "<option " + "value='0'>PLcode Pai <small>(Direto)</small></option>');";
                
                if (jQuery.isEmptyObject(data))
                {
                   console.log("Empty Object");

                   console.log('serm plcode existente');

                   dropdown_plcode_anterior.html(options);
                   dropdown_plcode_anterior.trigger('change');

                }else {

                


                $.each(data, function(i, v) {

                    //console.log(v);
                    options += "<option " + "value='" + v.id_ponto + "'>" + v.nome_ponto + "</option>');";

                    dropdown_plcode_anterior.html(options);

                });

                // atualizo o conteudo do select:

                dropdown_plcode_anterior.trigger('change');
                console.log('com plcode existente');
            }
                
            },
            failure: function() {
                swal.fire("Erro!", "Falha ao Criar o Select! Não foi possível verificar há PLCode existente.", "error");
                alert("Falha ao Criar o Select! Não foi possível verificar a Estação deste Cliente.");
            }
        });

        // Classe para Select Bootstrap Dinâmico ==<<


        ///========<<


    }

    //return false;

});

//==end: select estação




// longitude_plcode '^(-?\d{1,2}.\d{6}),(\s*)(-?\d{1,3}.\d{6})$'
//Inputmask({ }).mask("#longitude_plcode");


Inputmask({ regex: '^(-?\\d{1,2}.\\d{2}).(\s*)(-?\\d{1,3}.\\d{6})$' }).mask("#longitude_plcode");

Inputmask({ regex: '^(-?\\d{1,2}.\\d{2}).(\s*)(-?\\d{1,3}.\\d{6})$' }).mask("#latitude_plcode");




// 
Inputmask('decimal', { rightAlign: false }).mask("#volume_tanque");


Inputmask('decimal', { rightAlign: false }).mask("#capacidade_equipamento");

Inputmask('decimal', { rightAlign: false }).mask("#capacidade_instrumento");
//$(selector).inputmask('decimal', { rightAlign: false });

$('#kt_add_plcode_status_select').on('change', function(e) {
    e.preventDefault();

    var select = e.target.value;

   // console.log(select);


switch (select) {
    case '1':
        $("#kt_add_plcode_status").addClass("bg-success");
        $("#kt_add_plcode_status").removeClass("bg-warning");
     //   console.log("foi");
        break;
    case '3':
        $("#kt_add_plcode_status").addClass("bg-warning");
        $("#kt_add_plcode_status").removeClass("bg-success");
        break;
    default:
        $("#kt_add_plcode_status").addClass("bg-success");
        break;
}


})


