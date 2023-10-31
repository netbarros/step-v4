"use strict";

localStorage.removeItem('ticket');
localStorage.removeItem('usuario');
localStorage.removeItem('cockpit_carregado');

// Class definition
var KTSigninGeneral = function() {
    // Elements
    var form;
    var submitButton;
    var validator;

    // Handle form
    var handleForm = function(e) {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
			form,
			{
				fields: {					
					'email': {
                        validators: {
                            regexp: {
                                regexp: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                                message: 'O Email informado não parece válido',
                            },
							notEmpty: {
                                message: 'Email obrigatório'
							}
						}
					},
                    'password': {
                        validators: {
                            notEmpty: {
                                message: 'O password é obrigatório'
                            }
                        }
                    } 
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',  // comment to enable invalid state icons
                        eleValidClass: '' // comment to enable valid state icons
                    })
				}
			}
		);		

        // Handle form submit
        submitButton.addEventListener('click', function (e) {
            // Prevent button default action
            e.preventDefault();

            // Validate form
            validator.validate().then(function (status) {
                if (status == 'Valid') {
                    // Exibir loading indication
                    submitButton.setAttribute('data-kt-indicator', 'on');

                    // Disable button to avoid multiple click 
                    submitButton.disabled = true;

                   
                    var data = $("#kt_sign_in_form").serialize();
                    
                    

                    $.ajax({
                        type: 'POST',
                        url: './crud/login/valida_login.php',
                        data: data,
                        dataType: 'json',
                        beforeSend: function () {

                            // Hide loading indication
                     submitButton.removeAttribute('data-kt-indicator');

                //         // Enable button
                      submitButton.disabled = false;


                            //alert("Checkin Presencial Enviado= "+periodo_lido);

                        },
                        success: function (response, status, xhr, $form) {
                            // similate 2s delay

                            form.querySelector('[name="email"]').value = "";
                            form.querySelector('[name="password"]').value= "";

       

                            if (response.codigo == "1") {

                               // $("#kt_login_signin_submit").html('Autenticando');
                                 $("#kt_login_signin_submit").attr("disabled", true);
                               localStorage.removeItem('cockpit_carregado');

                            
                               function getCookie(name) {
                                let cookies = document.cookie.split(';');
                                for(let i = 0; i < cookies.length; i++) {
                                    let cookie = cookies[i];
                                    let [key, value] = cookie.split('=').map(c => c.trim());
                                    if (key === name) {
                                        return decodeURIComponent(value);
                                    }
                                }
                                return null;
                            }

                                                             // Recuperar valores dos cookies
let ticket = getCookie('id_tipo_suporte_ticket');
let usuario = getCookie('usuario_ticket');
let mailkey = getCookie('mailkey_ticket');
let projeto_ticket = getCookie('projeto_ticket');

                                // Validar os valores dos cookies
                                if (
                                    ticket !== null && 
                                    usuario !== null && 
                                    mailkey !== null && 
                                    projeto_ticket !== null
                                ) { 
                                    console.log("Variáveis dos cookies válidas. Redirecionando para a página de destino.");
                                    console.log("id_tipo_suporte_ticket :", ticket);
                                    console.log("usuario_ticket:", usuario);
                                    console.log("Projeto_ticket:", projeto_ticket);

                                    window.location.href = "../../views/relatorios/relatorios-suportes.php?"+mailkey;

                                    return false;
                                } else {    
                                    console.log("Acesso às Coleções de Suporte não validado. Redirecionando para a página de Dashboard.");
                                }


                           
                            
                               

                               // finaliza a gravação do cookie

                                if (response.nivel != "operador") {
                                    window.location.href = "./views/dashboard.php"; //"../dashboard/home.php";

                                    console.log("entrou");
                                }
                               
                                if (response.nivel == "operador") {


                                    console.log("entrou operador");
                                    window.location.href = "./app/";

                                }
                               
                            }

                            else {
                                        Swal.fire({
                                            html: response.mensagem,
                                                            icon: "error",
                                                            buttonsStyling: false,
                                                            confirmButtonText: "Ok, Farei isso!",
                                                            customClass: {
                                                                confirmButton: "btn btn-primary"
                                                            }
                                                        });
                               
                              

                            }



                        }
                    });


                //     // Simulate ajax request
                //     setTimeout(function() {
                //         // Hide loading indication
                //         submitButton.removeAttribute('data-kt-indicator');

                //         // Enable button
                //         submitButton.disabled = false;

                //         // Exibir message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                //        
                //             text: "Credenciais validadas com Sucesso!",
                //             icon: "success",
                //             buttonsStyling: false,
                //             confirmButtonText: "Ok, Prosseguir!",
                //             customClass: {
                //                 confirmButton: "btn btn-primary"
                //             }
                //         }).then(function (result) {
                //             if (result.isConfirmed) {
                //                 form.querySelector('[name="email"]').value= "";
                //                 form.querySelector('[name="password"]').value= "";
                                                              
                //                 //form.submit(); // submit form
                //                 var redirectUrl = form.getAttribute('data-kt-redirect-url');
                //                 if (redirectUrl) {
                //                     location.href = redirectUrl;
                //                 }
                //             }
                //         });
                //     }, 2000);
                // } else {
                //     // Exibir error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                //    
                //         text: "Sentimos muito, alguns erros foram encontrados, por favor, revise as informações e tente novamente.",
                //         icon: "error",
                //         buttonsStyling: false,
                //         confirmButtonText: "Ok, Farei isso!",
                //         customClass: {
                //             confirmButton: "btn btn-primary"
                //         }
                //     });
                    
                    
                    
                 }
            });
		});
    }

    // Public functions
    return {
        // Initialization
        init: function() {
            form = document.querySelector('#kt_sign_in_form');
            submitButton = document.querySelector('#kt_sign_in_submit');
            
            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTSigninGeneral.init();
});
