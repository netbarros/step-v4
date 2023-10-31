"use strict";

// Class Definition
var KTAuthNewPassword = function() {
    // Elements
    var form;
    var submitButton;
    var validator;
    var passwordMeter;

    var handleForm = function(e) {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
			form,
			{
				fields: {					 
                    'newpassword': {
                        validators: {
                            notEmpty: {
                                message: 'A Senha é obrigatória!'
                            },
                            callback: {
                                message: 'Por favor, informe uma senha válida.',
                                callback: function(input) {
                                    if (input.value.length > 0) {        
                                        return validatePassword();
                                    }
                                }
                            }
                        }
                    },
                    'confirm-password': {
                        validators: {
                            notEmpty: {
                                message: 'A confirmação de senha é necessário.'
                            },
                            identical: {
                                compare: function() {
                                    return form.querySelector('[name="currentpassword"]').value;
                                },
                                message: 'A Senha informada não confere, por favor, verifique.'
                            }
                        }
                    },
                    'toc': {
                        validators: {
                            notEmpty: {
                                message: 'Você precisa aceitar os termos e condições.'
                            }
                        }
                    }
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger({
                        event: {
                            password: false
                        }  
                    }),
					bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',  // comment to enable invalid state icons
                        eleValidClass: '' // comment to enable valid state icons
                    })
				}
			}
		);

        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            validator.revalidateField('newpassword');

            validator.validate().then(function(status) {
		        if (status == 'Valid') {
                    // Exibir loading indication
                    submitButton.setAttribute('data-kt-indicator', 'on');

                    // Disable button to avoid multiple click 
                    submitButton.disabled = true;


                    var dados = $("#kt_new_password_form").serialize();
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

                            console.log('Senha do Usuário atualizada com Sucesso.');

                            console.log(data);

                            console.log(data.codigo);
                            // Remove loading indication
                            submitButton.removeAttribute('data-kt-indicator');

                            // Enable button
                            submitButton.disabled = false;


                            if (data.codigo == 1) {

                           
                                // Exibir message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                                Swal.fire({
                                    text: "Sua nova senha foi salva com sucesso!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, irei armazenar com segurança!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function (result) {
                                    if (result.isConfirmed) {
                                        form.querySelector('[name="newpassword"]').value = "";
                                        form.querySelector('[name="confirm-password"]').value = "";
                                        passwordMeter.reset();  // reset password meter
                                        //form.submit();

                                        var redirectUrl = form.getAttribute('data-kt-redirect-url');
                                        if (redirectUrl) {
                                            location.href = redirectUrl;
                                        }
                                    }
                                });

                            }
                            if (data.codigo == 0) {


                                swal.fire("Ops!", data.retorno, "warning");


                            }
                            e.stopImmediatePropagation();

                        },
                        error: function (data) {

                            swal.fire("Erro!", "Não foi Possível Prosseguir!" + data.retorno, "error");

                            console.log('Falha no Processamento dos Dados.');
                            // Remove loading indication
                            submitButton.removeAttribute('data-kt-indicator');

                            // Enable button
                            submitButton.disabled = false;

                            e.stopImmediatePropagation();


                        }

                    });


                } else {
                    // Exibir error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    Swal.fire({
                        text: "Desculpe, parece que alguns erros foram detectados. Tente novamente.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, irei verificar!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
		    });
        });

        form.querySelector('input[name="newpassword"]').addEventListener('input', function() {
            if (this.value.length > 0) {
                validator.updateFieldStatus('newpassword', 'NotValidated');
            }
        });
    }

    var validatePassword = function() {
        return  (passwordMeter.getScore() === 100);
    }

    // Public Functions
    return {
        // public functions
        init: function() {
            form = document.querySelector('#kt_new_password_form');
            submitButton = document.querySelector('#kt_new_password_submit');
            passwordMeter = KTPasswordMeter.getInstance(form.querySelector('[data-kt-password-meter="true"]'));

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTAuthNewPassword.init();
});
