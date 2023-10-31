"use strict";

// Class definition
var NovoNucleo = function () {
	// Elements
	var modal;	
	var modalEl;

	var stepper;
	var form;
	var formSubmitButton;
	var formContinueButton;

	// Variables
	var stepperObj;
	var validations = [];

	// Private Functions
	var initStepper = function () {
		// Initialize Stepper
		stepperObj = new KTStepper(stepper);

		// Stepper change event(handle hiding submit button for the last step)
		stepperObj.on('kt.stepper.changed', function (stepper) {
			if (stepperObj.getCurrentStepIndex() === 4) {
				formSubmitButton.classList.remove('d-none');
				formSubmitButton.classList.add('d-inline-block');
				formContinueButton.classList.add('d-none');
			} else if (stepperObj.getCurrentStepIndex() === 5) {
				formSubmitButton.classList.add('d-none');
				formContinueButton.classList.add('d-none');
			} else {
				formSubmitButton.classList.remove('d-inline-block');
				formSubmitButton.classList.remove('d-none');
				formContinueButton.classList.remove('d-none');
			}
		});

		// Validation before going to next page
		stepperObj.on('kt.stepper.next', function (stepper) {
			console.log('stepper.next');

			// Validate form before change stepper step
			var validator = validations[stepper.getCurrentStepIndex() - 1]; // get validator for currnt step

			if (validator) {
				validator.validate().then(function (status) {
					console.log('validated!');

					if (status == 'Valid') {
						stepper.goNext();

						//KTUtil.scrollTop();
					} else {
						// Exibir error message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
						Swal.fire({
							text: "Desculpe, parece que alguns erros foram detectados, tente novamente.",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Ok, farei isso!",
							customClass: {
								confirmButton: "btn btn-light"
							}
						}).then(function () {
							//KTUtil.scrollTop();
						});
					}
				});
			} else {
				stepper.goNext();

				KTUtil.scrollTop();
			}
		});

		// Prev event
		stepperObj.on('kt.stepper.previous', function (stepper) {
			console.log('stepper.previous');

			stepper.goPrevious();
			KTUtil.scrollTop();
		});

		formSubmitButton.addEventListener('click', function (e) {
			// Validate form before change stepper step
			var validator = 'validated'; // get validator for last form

		
				console.log(validator);

				if (validator == 'validated') {
					// Prevent default button action
					e.preventDefault();
                    var dados = $("#kt_modal_novo_nucleo_form").serialize();
                    $.ajax({
						type: 'POST',
						url: '../../crud/nucleos/action-nucleos.php',
						dataType: 'json',
						data: dados,
						beforeSend: function(){
							// Disable button to avoid multiple click 
					formSubmitButton.disabled = true;

					// Exibir loading indication
					formSubmitButton.setAttribute('data-kt-indicator', 'on');

						},					
						success: function(resposta){

							if(resposta.codigo===1){
							// Hide loading indication
						formSubmitButton.removeAttribute('data-kt-indicator');

						// Enable button
						formSubmitButton.disabled = false;

						//stepperObj.goNext();
						KTUtil.scrollTop();

						createMetronicToast('Incluído com Sucesso', resposta.mensagem, 5000, 'success', 'bi bi-check2-square');

						
					
						setTimeout(() => {
							$('#kt_modal_novo_nucleo').modal('hide');				
							location.reload();
						}, 6000);
												
						//$("#tabela_nucleos").ajax.reload();

					}else {

						Swal.fire({
							text: "Desculpe, parece que alguns erros foram detectados, tente novamente",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Ok, farei isso!",
							customClass: {
								confirmButton: "btn btn-light"
							}
						}).then(function () {
							KTUtil.scrollTop();
						});

						console.log(resposta.retorno);
					}

						},	error: function(resposta){
							Swal.fire({
								text: "Desculpe, parece que alguns erros foram detectados, entre em contato com o suporte",
								icon: "error",
								buttonsStyling: false,
								confirmButtonText: "Ok, farei isso!",
								customClass: {
									confirmButton: "btn btn-light"
								}
							}).then(function () {
								KTUtil.scrollTop();
							});

							console.log(resposta.retorno);
						}
					});




				} else {
					Swal.fire({
						text: "Desculpe, parece que alguns erros foram detectados, entre em contato com o suporte.",
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Ok, farei isso!",
						customClass: {
							confirmButton: "btn btn-light"
						}
					}).then(function () {
						KTUtil.scrollTop();
					});
				}
			
		});
	}



	var initValidation = function () {
		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		// Step 1
		validations.push(FormValidation.formValidation(
			form,
			{
				fields: {
					nome_nucleo: {
						validators: {
							notEmpty: {
								message: 'Nome de Identificação do Núcleo'
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
		));

		
	}

	return {
		// Public Functions
		init: function () {
			// Elements
			modalEl = document.querySelector('#kt_modal_novo_nucleo');

			if (!modalEl) {
				return;
			}

			modal = new bootstrap.Modal(modalEl);

			stepper = document.querySelector('#kt_modal_novo_nucleo_stepper');
			form = document.querySelector('#kt_modal_novo_nucleo_form');
			formSubmitButton = stepper.querySelector('[data-kt-stepper-action="submit"]');
			formContinueButton = stepper.querySelector('[data-kt-stepper-action="next"]');

			initStepper();

			initValidation();
		}
	};
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    NovoNucleo.init();
});





$('#kt_modal_novo_nucleo').on('shown.bs.modal', function () {


    var id = $("#abre_modal_novo_nucleo").data('id');

    var projeto_nucleo = $("#abre_modal_novo_nucleo").data('nome');


    $("#id_projeto_nucleo").val(id);

    $("#projeto_nucleo").val(projeto_nucleo);

    
	
    


  })


  

$("#kt_modal_novo_nucleo").on('hidden.bs.modal', function (e) {

	setTimeout(() => {
		location.reload();
	}, 1000);


	


});



