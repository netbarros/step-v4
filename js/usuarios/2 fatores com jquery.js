

"use strict";

// Class definition
 function KTModalTwoFactorAuthentication ( element, options) {
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
								message: 'O Número de Celular, preferêncialmente com conta do Whatsapp é necessário.'
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

				// ajax para enviar o sms
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

					if (response.codigo == '1') {

						Swal.fire({
							text: response.mensagem,
							icon: "success",
							buttonsStyling: false,
							confirmButtonText: "Ok!",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						});
				
						console.log("Resposta recebida:", response);
					}	
					

					if (response.codigo == '0') {

					Swal.fire({
							text: response.mensagem,
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
					

//+++++++++



        // Handle sms cancelation
        smsCancelButton.addEventListener('click', function (e) {
            e.preventDefault();
            var option = optionsWrapper.querySelector('[name="auth_option"]:checked');

            optionsWrapper.classList.remove('d-none');
            smsWrapper.classList.add('d-none');
        });


    }})}})};

    var handleAppsForm = function() {
		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		appsValidator = FormValidation.formValidation(
			appsForm,
			{
				fields: {
					'code': {
						validators: {
							notEmpty: {
								message: 'O Código enviado por SMS é obrigatório'
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

			
			var code = $("#code").val();

			// Validate form before submit
			if (appsValidator) {
				appsValidator.validate().then(function (status) {
					console.log('validated!');

					if (status == 'Valid') {

						// Ocultar o elemento com data-kt-element="sms"
					$('[data-kt-element="sms"]').addClass('d-none');
					// Exibir o elemento com data-kt-element="apps"
					$('[data-kt-element="apps"]').removeClass('d-none');

					$.ajax({
					url: "/crud/usuarios/verifica-sms.php", // URL do arquivo PHP que irá processar a requisição
					type: "POST", // Método da requisição
					data: { mobile: mobile, acao: "valida-sms", code: code }, // Dados a serem enviados
					beforeSend: function() {
					// Código para executar antes de enviar a requisição
					appsSubmitButton.setAttribute('data-kt-indicator', 'on');

					// Disable button to avoid multiple click 
					appsSubmitButton.disabled = true;
	

					console.log("Preparando para enviar a requisição de validacao de telefone...");
					},
					success: function(response) {
					// Código para lidar com a resposta
					
					if (response.codigo == '1') {
						Swal.fire({
						text: response.mensagem,
						icon: "success",
						buttonsStyling: false,
						confirmButtonText: "Ok!",
						customClass: {
							confirmButton: "btn btn-primary"
						}
					});
				
						console.log("Resposta recebida:", response);
						appsSubmitButton.removeAttribute('data-kt-indicator');

						// Enable button
						appsSubmitButton.disabled = false;

					}	
					

					if (response.codigo == '0') {

					Swal.fire({
						text: response.mensagem,
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Ok!",
						customClass: {
							confirmButton: "btn btn-primary"
						}
						});

					console.log("Resposta recebida:", response);
					appsSubmitButton.removeAttribute('data-kt-indicator');

						// Enable button
						appsSubmitButton.disabled = false;

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
				}),  						
					
					
					 // Handle apps cancelation
					 appsCancelButton.addEventListener('click', function (e) {
						e.preventDefault();
						var option = optionsWrapper.querySelector('[name="auth_option"]:checked');
			
						optionsWrapper.classList.remove('d-none');
						appsWrapper.classList.add('d-none');
					});
			
				}})}})};



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

       
    };
