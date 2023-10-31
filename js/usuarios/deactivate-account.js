"use strict";

// Class definition
var KTAccountSettingsDeactivateAccount = function () {
    // Private variables
    var form;
    var validation;
    var submitButton;

    // Private functions
    var initValidation = function () {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validation = FormValidation.formValidation(
            form,
            {
                fields: {
                    deactivate: {
                        validators: {
                            notEmpty: {
                                message: 'Por favor, confirme para prosseguir com a alteração do Status da conta.'
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    //defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );
    }

    var handleForm = function () {
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            validation.validate().then(function (status) {
                if (status == 'Valid') {

                    swal.fire({
                        text: "Você têm certeza que gostaria de alterar o acesso à esta conta?",
                        icon: "warning",
                        buttonsStyling: false,
                        showDenyButton: true,
                        confirmButtonText: "Sim",
                        denyButtonText: 'Não',
                        customClass: {
                            confirmButton: "btn btn-light-primary",
                            denyButton: "btn btn-danger"
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {


                            var dados = $("#kt_account_deactivate_form").serialize();
                            $.ajax({
                                type: "POST",
                                url: "../../crud/usuarios/action-usuarios.php",
                                data: dados,
                                dataType: "json",
                                cache: false,
                                beforeSend: function () {

                                    // Exibir loading indication
                                    submitButton.setAttribute('data-kt-indicator', 'on');

                                    // Disable button to avoid multiple click
                                    submitButton.disabled = true;


                                },

                                success: function (data) {

                                    console.log('Status do usuário atualizado com Sucesso.');

                                    console.log(data);

                                    console.log(data.codigo);
                                    // Remove loading indication
                                    submitButton.removeAttribute('data-kt-indicator');

                                    // Enable button
                                    submitButton.disabled = false;


                                    if (data.codigo == 1) {

                                     

                                        Swal.fire({
                                            icon: 'success',
                                            html: data.mensagem,
                                            timer: 2000, // tempo em milissegundos
                                            showConfirmButton: false, // não exibir botão de confirmação
                                          }).then(function(){
                                            // código para fechar a janela
                                            //window.close(); // fecha a janela atual
            
                                            location.reload();
                                          });
                                        


                                        

                                        $("#acao_desativa_conta").val("reativar_conta");

                                        $("#kt_account_deactivate_account_submit").html("Alterar Acesso à Conta");


                                        $("#kt_account_deactivate_account_submit").removeClass("btn-danger");

                                        $("#kt_account_deactivate_account_submit").addClass("btn-warning");

                                    }
                                    if (data.codigo == 0) {


                                        Swal.fire({
                                            html: data.retorno,
                                            icon: 'info',
                                            confirmButtonText: "Ok",
                                            buttonsStyling: false,
                                            customClass: {
                                                confirmButton: "btn btn-light-primary"
                                            }
                                        })


                                    }
                                    e.stopImmediatePropagation();

                                }

                            });


                           
                        } else if (result.isDenied) {
                            Swal.fire({
                                html: data.retorno,
                                icon: 'info',
                                confirmButtonText: "Ok",
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: "btn btn-light-primary"
                                }
                            })
                        }
                    });

                } else {
                    swal.fire({
                        title:'Atenção Necessária',
                        html: 'Campos Obrigatórios, não informados!',
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, farei isso!",
                        customClass: {
                            confirmButton: "btn btn-light-primary"
                        }
                    });
                }
            });
        });
    }

    // Public methods
    return {
        init: function () {
            form = document.querySelector('#kt_account_deactivate_form');

            if (!form) {
                return;
            }
            
            submitButton = document.querySelector('#kt_account_deactivate_account_submit');

            initValidation();
            handleForm();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTAccountSettingsDeactivateAccount.init();
});
