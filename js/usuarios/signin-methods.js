"use strict";

// Class definition
var KTAccountSettingsSigninMethods = function () {
    // Private functions
    var initSettings = function () {
        // UI elements
        var signInMainEl = document.getElementById('kt_signin_email');

        if (!signInMainEl) {
            return;
        }

        var signInEditEl = document.getElementById('kt_signin_email_edit');
        var passwordMainEl = document.getElementById('kt_signin_password');
        var passwordEditEl = document.getElementById('kt_signin_password_edit');

        // button elements
        var signInChangeEmail = document.getElementById('kt_signin_email_button');
        var signInCancelEmail = document.getElementById('kt_signin_cancel');
        var passwordChange = document.getElementById('kt_signin_password_button');
        var passwordCancel = document.getElementById('kt_password_cancel');

        // toggle UI
        signInChangeEmail.querySelector('button').addEventListener('click', function () {
            toggleChangeEmail();
        });

        signInCancelEmail.addEventListener('click', function () {
            toggleChangeEmail();
        });

        passwordChange.querySelector('button').addEventListener('click', function () {
            toggleChangePassword();
        });

        passwordCancel.addEventListener('click', function () {
            toggleChangePassword();
        });

        var toggleChangeEmail = function () {
            signInMainEl.classList.toggle('d-none');
            signInChangeEmail.classList.toggle('d-none');
            signInEditEl.classList.toggle('d-none');
        }

        var toggleChangePassword = function () {
            passwordMainEl.classList.toggle('d-none');
            passwordChange.classList.toggle('d-none');
            passwordEditEl.classList.toggle('d-none');
        }
    }

    // muda email usuário:
    var handleChangeEmail = function (e) {
        var validation;

        // form elements
        var signInForm = document.getElementById('kt_signin_change_email');

        if (!signInForm) {
            return;
        }

        validation = FormValidation.formValidation(
            signInForm,
            {
                fields: {
                    EmailAtual: {
                        validators: {
                            notEmpty: {
                                message: 'É necessário, confirmar o E-mail atual.'
                            },
                            EmailAtual: {
                                message: 'O E-mail informado não parece válido.'
                            },
                            remote: {
                                message: 'Este e-mail já existe no sistema',
                                url: '/crud/usuarios/verifica-email.php',
                                type: 'POST',
                                // Envia o valor do campo {emailaddress} como parâmetro email para o seu arquivo PHP
                                data: function(validator) {
                                    return {
                                        email: validator.getFieldElements('EmailAtual').val(),
                                        modo: 'altera'
                                    };
                                },
                                // Suas regras de tratamento do retorno do PHP
                                onSuccess: function(e, data) {
                                    if (data.exists) {
                                        return {
                                            valid: false
                                        };
                                    }
        
                                    return {
                                        valid: true
                                    };
                                }
                            }
                        }
                    },
        
                    NovoEmail: {
                        validators: {
                            notEmpty: {
                                message: 'É necessário, informar seu novo e-mail de contato.'
                            }
                        }
                    }
                },
        
                plugins: { //Learn more: https://formvalidation.io/guide/plugins
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row'
                    })
                }
            }
        );
        

        const submitButton = document.getElementById('kt_signin_submit');
        signInForm.querySelector('#kt_signin_submit').addEventListener('click', function (e) {
            e.preventDefault();
            console.log('click');

            validation.validate().then(function (status) {
                if (status == 'Valid') {


                    var dados = $("#kt_signin_change_email").serialize();
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

                            console.log('Email do Usuário Atualizado com Sucesso.');

                            console.log(data);

                            console.log(data.codigo);
                            // Remove loading indication
                           
                            submitButton.removeAttribute('data-kt-indicator');

                            // Enable button
                            submitButton.disabled = false;

                            if (data.codigo == 1) {

                                swal.fire({
                                    title: "Feito!",
                                    html: 'Aguarde que estamos atualizando o email do usuário em nossos servidores, logo ele receberá um email confirmando a operação realizada.',
                                    text: data.retorno,
                                    icon: "success",
                                    buttonsStyling: false,
                                    showConfirmButton: false, // Esconde o botão de confirmação
                                    timer: 5000, // Fecha a SweetAlert após 2 segundos (2000 ms)
                                    customClass: {
                                        confirmButton: "btn font-weight-bold btn-light-primary"
                                    },
                                    willClose: () => {
                                       // signInForm.reset();
                                      //  validation.resetForm();

                                        location.reload();
                                    }
                                })
                                


                            }
                            if (data.codigo == 0) {
                              

                                swal.fire("Ops!", data.retorno, "warning");


                            }
                            e.stopImmediatePropagation();

                        },
                        error: function (data) {

                            swal.fire("Erro!", "Por favor, informe ao Informe ao Suporte, não foi Possível Prosseguir!" + data.retorno, "error");

                            console.log('Falha no Processamento dos Dados.');
                            // Remove loading indication
                          // Exibir loading indication
                          submitButton.setAttribute('data-kt-indicator', 'on');

                          // Disable button to avoid multiple click
                          submitButton.disabled = true;

                            e.stopImmediatePropagation();


                        }

                    });
                  
                } else {
                    swal.fire({
                        text: "Desculpe, parece que alguns erros foram detectados. Tente novamente.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, farei isso!",
                        customClass: {
                            confirmButton: "btn font-weight-bold btn-light-primary"
                        }
                    });

                    submitButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    submitButton.disabled = false;
                }
            });
        });
    }
// muda email do usuário:
    var handleChangePassword = function (e) {
        var validation;

        // form elements
        var passwordForm = document.getElementById('kt_signin_change_password');

        if (!passwordForm) {
            return;
        }

        validation = FormValidation.formValidation(
            passwordForm,
            {
                fields: {
                    currentpassword: {
                        validators: {
                            notEmpty: {
                                message: 'É necessário, confirmar a senha atual.'
                            }
                        }
                    },

                    newpassword: {
                        validators: {
                            notEmpty: {
                                message: 'É necessário, confirmar a nova senha.'
                            }
                        }
                    },

                    confirmpassword: {
                        validators: {
                            notEmpty: {
                                message: 'É necessário, confirmar a senha.'
                            },
                            identical: {
                                compare: function() {
                                    return passwordForm.querySelector('[name="newpassword"]').value;
                                },
                                message: 'A senha e sua confirmação não são iguais'
                            }
                        }
                    },
                },

                plugins: { //Learn more: https://formvalidation.io/guide/plugins
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row'
                    })
                }
            }
        );

        const submitButton_kt_password_submit = document.getElementById('kt_password_submit');
        passwordForm.querySelector('#kt_password_submit').addEventListener('click', function (e) {
            e.preventDefault();
            console.log('click');

            validation.validate().then(function (status) {
                if (status == 'Valid') {


                    var dados = $("#kt_signin_change_password").serialize();
                    $.ajax({
                        type: "POST",
                        url: "../../crud/usuarios/action-usuarios.php",
                        data: dados,
                        dataType: "json",
                        cache: false,
                        beforeSend: function () {

                            // Exibir loading indication
                            submitButton_kt_password_submit.setAttribute('data-kt-indicator', 'on');

                            // Disable button to avoid multiple click
                            submitButton_kt_password_submit.disabled = true;


                        },

                        success: function (data) {

                            console.log('Senha do Usuário atualizada com Sucesso.');

                            console.log(data);

                            console.log(data.codigo);
                            // Remove loading indication
                            submitButton_kt_password_submit.removeAttribute('data-kt-indicator');

                            // Enable button
                            submitButton_kt_password_submit.disabled = false;


                            if (data.codigo == 1) {

                                swal.fire({
                                    title: "Feito!",
                                    html: data.retorno,
                                    icon: "success",
                                    buttonsStyling: false,
                                    showConfirmButton: false, // Esconde o botão de confirmação
                                    timer: 5000, // Fecha a SweetAlert após 2 segundos (2000 ms)
                                    customClass: {
                                        confirmButton: "btn font-weight-bold btn-light-primary"
                                    },
                                    willClose: () => {
                                        signInForm.reset();
                                        validation.resetForm();
                                    }
                                })


                            }
                            if (data.codigo == 0) {


                                swal.fire("Ops!", data.retorno, "warning");


                            }
                            e.stopImmediatePropagation();

                        },
                        error: function (data) {

                            swal.fire("Erro!", "Falha ao tentar enviar o email com nova senha!" + data, "error");

                            console.log('Falha no Processamento dos Dados.');
                            // Remove loading indication
                            submitButton_kt_password_submit.removeAttribute('data-kt-indicator');

                            // Enable button
                            submitButton_kt_password_submit.disabled = false;

                            e.stopImmediatePropagation();


                        }

                    });
                    
                    swal.fire({
                        title: "Feito!",
                        html: data.retorno,    
                        buttonsStyling: false,
                        confirmButtonText: "Ok, farei isso!",
                        customClass: {
                            confirmButton: "btn font-weight-bold btn-light-primary"
                        }
                    }).then(function(){
                        passwordForm.reset();
                        validation.resetForm(); // Reset formvalidation --- more info: https://formvalidation.io/guide/api/reset-form/
                    });
                } else {
                    swal.fire({
                        title: "Falha!",
                        html: data.retorno,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, farei isso!",
                        customClass: {
                            confirmButton: "btn font-weight-bold btn-light-primary"
                        }
                    });
                }
            });
        });
    }

    // Public methods
    return {
        init: function () {
            initSettings();
            handleChangeEmail();
            handleChangePassword();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTAccountSettingsSigninMethods.init();
});
