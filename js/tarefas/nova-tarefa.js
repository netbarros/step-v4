$(document).ready(function() {

    
// Init Select2 --- more info: https://select2.org/

    // Inicialize o Select2
    $('#tipo_tarefa').select2({
   
    });
    
    $('#ciclo_leitura').select2({
       
    });
    
    $('#dia_semana_nova_tarefa').select2({
       
    });
    
    $('#plcode_tarefa').select2({
       
    });
    
    $('#indicador_tarefa').select2({
       
    });
    
    $('#usuario_tarefa').select2({
       
    });
    
    $('#modo_checkin').select2({
       
    });



// Define form_nova_tarefa element
var form_nova_tarefa = document.getElementById('form_modal_nova_tarefa');


var tags = document.querySelector("#tags");

// Initialize Tagify script on the above inputs
new Tagify(tags, {
    whitelist: ["Importante", "Urgente", "Alta", "Média", "Baixa", "Controle de Qualidade", "Inspeção"],
    maxTags: 5,
    dropdown: {
        maxItems: 20,           // <- mixumum allowed rendered suggestions
        classname: "tagify__inline__suggestions", // <- custom classname for this dropdown, so it could be targeted
        enabled: 0,             // <- show suggestions on focus
        closeOnSelect: !1 ,   // <- do not hide the suggestions dropdown once an item has been selected
        placeholder: "Digite Algo...",
    enforceWhitelist: true
    }
});

$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip()
  })



  $("#data_tarefa").flatpickr({
    enableTime: 0,
    altInput: true,
    altFormat: "F j, Y",
    dateFormat: "Y-m-d",
    minDate: "today",
    locale: "pt" 
});



// Init form_nova_tarefa validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
var validator = FormValidation.formValidation(
    form_nova_tarefa,
    {
        fields: {
            'titulo_tarefa': {
                validators: {
                    notEmpty: {
                        message: 'O Nome de Identificaçáo é Obrigatório'
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
var submitButton_nova_tarefa = document.getElementById('bt_nova_tarefa');
submitButton_nova_tarefa.addEventListener('click', function (e) {
    // Prevent default button action
    e.preventDefault();

    // Validate form_nova_tarefa before submit
    if (validator) {
        validator.validate().then(function (status) {
            console.log('validated!');

            if (status == 'Valid') {
                // Exibir loading indication
                submitButton_nova_tarefa.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitButton_nova_tarefa.disabled = true;

                // Simulate form_nova_tarefa submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/


             

                var dados = $("#form_modal_nova_tarefa").serialize();
                $.ajax({
                    type: 'POST',
                    url: '../../crud/tarefas/action-tarefas.php',
                    dataType: 'json',
                    data: dados,
                    beforeSend: function(){
                        // Disable button to avoid multiple click 
                submitButton_nova_tarefa.disabled = true;

                // Exibir loading indication
                submitButton_nova_tarefa.setAttribute('data-kt-indicator', 'on');

                    },					
                    success: function(retorno){
                        

                         // Iterar sobre todas as respostas
        retorno.forEach(function(resposta) {

                        if (resposta.codigo == '1') {
                            // Seu código para lidar com sucesso
                            Swal.fire({
                                icon: 'success',
                                html: resposta.mensagem,
                                timer: 2000,
                                showConfirmButton: false,
                            }).then(function() {
                                KTUtil.scrollTop();
                                $("#kt_modal_new_target").modal("hide");
                                    // Hide loading indication
                                    submitButton_nova_tarefa.removeAttribute('data-kt-indicator');

                                    // Enable button
                                    submitButton_nova_tarefa.disabled = false;

                                    createMetronicToast('Retorno',resposta.mensagem, 5000, 'success', 'bi bi-check2-square');
                                    setTimeout(() => {
                    

                                        location.reload();
                                    }, 3000);
                            });
                            
                            console.log("Código de sucesso: ", resposta.codigo);
                        } else if (resposta.codigo == '0') {
                            // Seu código para lidar com falha
                            Swal.fire({
                                icon: 'error',
                                html: resposta.mensagem,
                                timer: 2000,
                                showConfirmButton: false,
                            }).then(function() {
                                KTUtil.scrollTop();
                                    // Hide loading indication
                                    submitButton_nova_tarefa.removeAttribute('data-kt-indicator');

                                    // Enable button
                                    submitButton_nova_tarefa.disabled = false;
                            });

                            console.log("Código de falha: ", resposta.codigo);
                        }

        })// retorno.forEach success

        },	error: function(retorno){
                        Swal.fire({
                            text: "Foram encontrados erros no Formulário, por favor, verifique.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, farei isso!",
                            customClass: {
                                confirmButton: "btn btn-light"
                            }
                        }).then(function () {
                            KTUtil.scrollTop();
                        });

                         // Hide loading indication
                     submitButton_nova_tarefa.removeAttribute('data-kt-indicator');

                     // Enable button
                     submitButton_nova_tarefa.disabled = false;
                     
                        console.log(retorno.codigo);
                    }
                });

            
        
        









}})}})})       
