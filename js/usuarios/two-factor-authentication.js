"use strict";

// Class definition
var KTModalTwoFactorAuthentication = function () {
    // Private variables
    var modal;
    var modalObject;

    var optionsWrapper;
    var optionsSelectButton;

    var smsWrapper;
    var smsForm;
    var smsSubmitButton;
    var smsCancelButton;
    var smsValidator;

    var appsWrapper;
    var appsForm;
    var appsSubmitButton;
    var appsCancelButton;
    var appsValidator;

    // Private functions
    var handleOptionsForm = function() {
        // Handle options selection
        optionsSelectButton.addEventListener('click', function (e) {
            e.preventDefault();
            var option = optionsWrapper.querySelector('[name="auth_option"]:checked');

            optionsWrapper.classList.add('d-none');

            if (option.value == 'sms') {
                smsWrapper.classList.remove('d-none');
            } else {
                appsWrapper.classList.remove('d-none');
            }
        });
    }

	var showOptionsForm = function() {
		optionsWrapper.classList.remove('d-none');
		smsWrapper.classList.add('d-none');
		appsWrapper.classList.add('d-none');
    }

    var handleSMSForm = function() {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		smsValidator = FormValidation.formValidation(
			smsForm,
			{
				fields: {
					'mobile': {
						validators: {
							notEmpty: {
								message: 'Seu Telefone é obrigatório para validarmos sua identidade'
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
			}
		);

        // Handle apps submition
        smsSubmitButton.addEventListener('click', function (e) {
            e.preventDefault();

			// Validate form before submit
			if (smsValidator) {
				smsValidator.validate().then(function (status) {
					console.log('validated!');

					if (status == 'Valid') {
						// Exibir loading indication
						smsSubmitButton.setAttribute('data-kt-indicator', 'on');

						// Disable button to avoid multiple click 
						smsSubmitButton.disabled = true;						

						var mobile = $("#mobile").val();

						$.ajax({
							url: "/crud/usuarios/verifica-sms.php", // URL do arquivo PHP que irá processar a requisição
							type: "POST", // Método da requisição
							data: { mobile: mobile, acao: "verifica-sms" }, // Dados a serem enviados
							beforeSend: function() {
							// Código para executar antes de enviar a requisição
								// Exibir loading indication
								smsSubmitButton.setAttribute('data-kt-indicator', 'on');
		
								// Disable button to avoid multiple click 
								smsSubmitButton.disabled = true;	
		
							console.log("Preparando para enviar a requisição de validacao de telefone...");
							},
							success: function(response) {
							// Código para lidar com a resposta
		
							// Remove loading indication
							smsSubmitButton.removeAttribute('data-kt-indicator');
		
							// Enable button
							smsSubmitButton.disabled = false;
							console.log("Resposta recebida:", response);
		
							// Ocultar o elemento com data-kt-element="sms"
							$('[data-kt-element="sms"]').addClass('d-none');
							// Exibir o elemento com data-kt-element="apps"
							$('[data-kt-element="apps"]').removeClass('d-none');
		
							var jsonResponse = typeof response === 'string' ? JSON.parse(response) : response;
							if (jsonResponse.codigo === 1) {
		
								Swal.fire({
									text: jsonResponse.mensagem,
									icon: "success",
									buttonsStyling: false,
									confirmButtonText: "Ok!",
									customClass: {
										confirmButton: "btn btn-primary"
									}
								});
						
								console.log("Resposta recebida:", response);
							}	
							
		
							if (jsonResponse.codigo === 0) {
		
							Swal.fire({
									text: jsonResponse.mensagem,
									icon: "error",
									buttonsStyling: false,
									confirmButtonText: "Ok!",
									customClass: {
										confirmButton: "btn btn-primary"
									}
								});
		
							console.log("Resposta recebida:", response);
							}},
							error: function(jqXHR, textStatus, errorThrown) {
							// Código para lidar com erros na requisição
							Swal.fire({
								text: "Desculpe, parece que alguns erros foram detectados. Tente novamente.",
								icon: "error",
								buttonsStyling: false,
								confirmButtonText: "Ok, farei isso!",
								customClass: {
									confirmButton: "btn btn-primary"
								}
							});
							console.log("Erro na requisição:", textStatus, errorThrown);
							}
						});						
					} else {
						// Exibir error message.
						Swal.fire({
							text: "Sorry, looks like there are some errors detected, please try again.",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Ok, got it!",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						});
					}
				});
			}
        });

        // Handle sms cancelation
        smsCancelButton.addEventListener('click', function (e) {
            e.preventDefault();
            var option = optionsWrapper.querySelector('[name="auth_option"]:checked');

            optionsWrapper.classList.remove('d-none');
            smsWrapper.classList.add('d-none');
        });
    }

    var handleAppsForm = function() {
		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		appsValidator = FormValidation.formValidation(
			appsForm,
			{
				fields: {
					'code': {
						validators: {
							notEmpty: {
								message: 'SMS recebido é necessário para validarmos seu número de telefone.'
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
			}
		);

        // Handle apps submition
        appsSubmitButton.addEventListener('click', function (e) {
            e.preventDefault();

			// Validate form before submit
			if (appsValidator) {
				appsValidator.validate().then(function (status) {
					console.log('validação appsValidator!');

					if (status == 'Valid') {
						appsSubmitButton.setAttribute('data-kt-indicator', 'on');

						// Disable button to avoid multiple click 
						appsSubmitButton.disabled = true;

						var code = $("#code").val();
						var mobile = $("#mobile").val();

						console.log('comeca o ajax!');
						console.log("mobile:", mobile);
							console.log("code:", code);
							console.log("appsSubmitButton:", appsSubmitButton);

							

						$.ajax({
							url: "/crud/usuarios/verifica-sms.php",
							type: "POST",
							data: { mobile: mobile, acao: "valida-sms", code: code },
							beforeSend: function() {
								appsSubmitButton.setAttribute('data-kt-indicator', 'on');
								appsSubmitButton.disabled = true;
								console.log("Preparando para enviar a requisição de validacao de telefone...");
							},
							success: function(response) {
								var jsonResponse = typeof response === 'string' ? JSON.parse(response) : response;
						
								var mensagem = jsonResponse.mensagem;
								var icon = jsonResponse.codigo === 1 ? "success" : "error";

								
						
								var title = "Telefone e SMS validados com sucesso!";
								
					
								createMetronicToast(title, mensagem, 5000, icon, 'bi bi-check2-square');

								// Ocultar o elemento com data-kt-element="apps"
								$('[data-kt-element="apps"]').addClass('d-none');
								// Exibir o elemento com data-kt-element="sms"
								$('[data-kt-element="sms"]').removeClass('d-none');
								// Ocultar o modal
								setTimeout(function() {
									$("#kt_modal_two_factor_authentication").modal('hide'); // Fechar a modal
									$("#apps-form")[0].reset(); // Resetar o formulário com ID "apps-form"
									

								}, 3000);
						
								console.log("Resposta recebida:", response);
								appsSubmitButton.removeAttribute('data-kt-indicator');
								appsSubmitButton.disabled = false;
							},
							error: function(jqXHR, textStatus, errorThrown) {
								Swal.fire({
									text: "Erro ao realizar a requisição.", // Aqui, a mensagem precisa ser definida de acordo com seu contexto
									icon: "error",
									buttonsStyling: false,
									confirmButtonText: "Ok, farei isso!",
									customClass: {
										confirmButton: "btn btn-primary"
									}
								});
								
								appsSubmitButton.removeAttribute('data-kt-indicator');
								appsSubmitButton.disabled = false;
								console.log("Erro na requisição:", textStatus, errorThrown);
							}
						});
						
						
						console.log('termina o ajax!');
					} else {

						appsSubmitButton.removeAttribute('data-kt-indicator');
								appsSubmitButton.disabled = false;
						// Exibir error message.
						Swal.fire({
							text: "Desculpe, parece que alguns erros foram detectados. Tente novamente;",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Ok, got it!",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						});

						
					}

					
				});

				
			}
        });

        // Handle apps cancelation
        appsCancelButton.addEventListener('click', function (e) {
            e.preventDefault();
            var option = optionsWrapper.querySelector('[name="auth_option"]:checked');

            optionsWrapper.classList.remove('d-none');
            appsWrapper.classList.add('d-none');
        });
    }

    // Public methods
    return {
        init: function () {
            // Elements
            modal = document.querySelector('#kt_modal_two_factor_authentication');

			if (!modal) {
				return;
			}

            modalObject = new bootstrap.Modal(modal);

            optionsWrapper = modal.querySelector('[data-kt-element="options"]');
            optionsSelectButton = modal.querySelector('[data-kt-element="options-select"]');

            smsWrapper = modal.querySelector('[data-kt-element="sms"]');
            smsForm = modal.querySelector('[data-kt-element="sms-form"]');
            smsSubmitButton = modal.querySelector('[data-kt-element="sms-submit"]');
            smsCancelButton = modal.querySelector('[data-kt-element="sms-cancel"]');

            appsWrapper = modal.querySelector('[data-kt-element="apps"]');
            appsForm = modal.querySelector('[data-kt-element="apps-form"]');
            appsSubmitButton = modal.querySelector('[data-kt-element="apps-submit"]');
            appsCancelButton = modal.querySelector('[data-kt-element="apps-cancel"]');

            // Handle forms
            handleOptionsForm();
            handleSMSForm();
            handleAppsForm();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTModalTwoFactorAuthentication.init();
});

