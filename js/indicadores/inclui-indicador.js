// Define form_indicador element
var form_indicador = document.getElementById('form_indicador');

// Init form_indicador validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
var validator = FormValidation.formValidation(
    form_indicador,
    {
        fields: {
            'nome_parametro': {
                validators: {
                    notEmpty: {
                        message: 'O Nome do Indicador é Obrigatório!'
                    }
                }
            },
        },

        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap: new FormValidation.plugins.Bootstrap5({
                rowSelector: '.fv-row',
                eleInvalidClass: '',
                eleValidClass: ''
            })
        }
    }
);

// Submit button handler
var submitButton_Indicador = document.getElementById('bt_novo_indicador');
submitButton_Indicador.addEventListener('click', function (e) {
    // Prevent default button action
    e.preventDefault();

    // Validate form_indicador before submit
    if (validator) {
        validator.validate().then(function (status) {
            console.log('validated!');

            if (status == 'Valid') {
                // Exibir loading indication
                submitButton_Indicador.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitButton_Indicador.disabled = true;

                // Simulate form_indicador submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/


                var frm = $('#form_indicador').serialize(); //(se não houver multipart utilizo o serialize, caso contrário o FormData)
       
                //var formdata = new FormData($("form_indicador[name='form_indicador']")[0]);
     
             $("#bt_novo_indicador").addClass("kt-spinner kt-spinner--sm kt-spinner--brand").attr("disabled", !0),
             $("#bt_novo_indicador").html('Enviando ...');
     
             $.ajax({
                 type: "POST",
                 url: "/crud/indicadores/action-indicadores.php",
                 data: frm,
                 dataType:"json",
                 success: function (data) {
                     console.log('Cadastro do Parâmetro, Enviado ao Servidor com Sucesso.');
                   
                     console.log(data.codigo);
             if(data.codigo == 1){

                createMetronicToast('Novo Indicador', data.mensagem, 5000, 'success', 'bi bi-check2-square');
     
                   $('#modal_novo_registro').modal('hide'); 
     
                   $("#div_modulo_indicadores").load("/views/projetos/plcodes/indicadores/tabela-indicadores.php?id_plcode="+data.id_ponto);
     
                
     
                   $('.modal-backdrop').remove();
                 
             }
             if(data.codigo == 0){
     
                              
              
                 swal.fire("Atenção!", data.mensagem, "warning");
     
                 
     
                 $("#bt_novo_indicador").removeClass("kt-spinner kt-spinner--sm kt-spinner--brand").attr("disabled", !1),
                 $("#bt_novo_indicador").html('Cadastrar');
               
             }
     
     
         },
         error: function (data) {
             console.log('Falha no Processamento dos Dados.');
             console.log(data);
     
             swal.fire("Falha!", data.mensagem, "error");
     
             $("#bt_novo_indicador").removeClass("kt-spinner kt-spinner--sm kt-spinner--brand").attr("disabled", !1),
             $("#bt_novo_indicador").html('Cadastrar');
     
           
            }
        })     



            }
        });
    }
});


Inputmask('decimal', { rightAlign: false }).mask("#concen_min_par");

Inputmask('decimal', { rightAlign: false }).mask("#concen_max_par");


$('#origem_leitura_parametro_add').select2( {
    minimumResultsForSearch: Infinity
  });
$('#controle_concentracao').select2({
    minimumResultsForSearch: Infinity
  });
$('#unidade_medida_add').select2({
   
  });

$('#controle_concentracao_add').select2({
    minimumResultsForSearch: Infinity
  });

