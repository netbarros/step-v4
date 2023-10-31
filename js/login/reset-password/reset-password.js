"use strict";

var KTAuthResetPassword = (function() {
    var form = document.querySelector('#kt_password_reset_form');
    var submitButton = document.querySelector('#kt_password_reset_submit');
    var validator;

    function showAlert(message, iconType) {
        Swal.fire({
            text: message,
            icon: iconType,
            buttonsStyling: false,
            confirmButtonText: "Ok, irei verificar!",
            customClass: {
                confirmButton: "btn btn-primary"
            }
        });
    }

    function handleFormSubmission(e) {
        e.preventDefault();

        validator.validate().then(function(status) {
            if (status !== 'Valid') {
                showAlert("Desculpe, parece que alguns erros foram detectados. Tente novamente.", "error");
                return;
            }

           

            var formData = $("#kt_password_reset_form").serialize();

            $.ajax({
                type: "POST",
                url: "./crud/usuarios/action-usuarios.php",
                data: formData,
                beforesend: function() {


                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;
                },
              
                success: function(response) {

                  

                    var mensagem = response.retorno;
                    var icon = response.codigo === 1 ? "success" : "error";

                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;
            
                    // Verificar se a resposta contém o campo 'codigo'
                   
                       
                           Swal.fire({
                                text: mensagem,
                                icon: icon,
                                buttonsStyling: false,
                                confirmButtonText: "Ok!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });

                       
                            console.log("Resposta recebida:", response);
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                   
                        console.error("Resposta do servidor:", jqXHR.responseText);
                        showAlert("Houve um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.", "error");
                  
                    
                    var errorMessage = "Houve Erro Interno ao enviar o Comando para Reiniciar a sua Senha - já criamos um log no servidor, para que nossos analistas possam estudar o motivo.";
                    
                    // Se houver uma mensagem de erro específica, anexe-a à mensagem
                    if (textStatus && errorThrown) {
                        errorMessage += " Detalhes do erro: " + textStatus + ": " + errorThrown;
                    }
            
                    showAlert(errorMessage, "warning");
                    console.error("Erro AJAX:", textStatus, errorThrown);

                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;
                }
            });
            
        });
    }

    function initValidation() {
        validator = FormValidation.formValidation(form, {
            fields: {
                'email': {
                    validators: {
                        regexp: {
                            regexp: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                            message: 'O valor não é um endereço de e-mail válido',
                        },
                        notEmpty: {
                            message: 'O Email é necessário.'
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                    rowSelector: '.fv-row',
                    eleInvalidClass: '',
                    eleValidClass: ''
                })
            }
        });
    }

    return {
        init: function() {
            initValidation();
            submitButton.addEventListener('click', handleFormSubmission);
        }
    };
})();

document.addEventListener("DOMContentLoaded", function() {
    KTAuthResetPassword.init();
});
