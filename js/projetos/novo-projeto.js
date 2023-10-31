"use strict";

// Class definition
var NovoProjeto = function () {
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
							text: "Desculpe, parece que alguns erros foram detectados, tente novamente",
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

					$("#obs_projeto").val($("#quill_obs_projeto").html());

                    var dados = $("#kt_modal_novo_projeto_form").serialize();
                    $.ajax({
						type: 'POST',
						url: '../../crud/projetos/action-projetos.php',
						dataType: 'json',
						data: dados,
						beforeSend: function(){
							// Disable button to avoid multiple click 
					formSubmitButton.disabled = true;

					// Exibir loading indication
					formSubmitButton.setAttribute('data-kt-indicator', 'on');

						},					
						success: function(resposta){
							

							// A $( document ).ready() block.


							if(resposta.codigo=='1'){

								$("#kt_modal_novo_projeto").modal("hide");
								createMetronicToast('Novo Projeto Incluído', resposta.mensagem, 5000, 'success', 'bi bi-check2-square');
								
								KTUtil.scrollTop();
								
								setTimeout(function() {
								location.reload();
								}, 2000);

							// Hide loading indication
						formSubmitButton.removeAttribute('data-kt-indicator');

						// Enable button
						formSubmitButton.disabled = false;

						//stepperObj.goNext();

					}

					if(resposta.codigo=='0'){
						Swal.fire({
							text: codigo.mensagem,
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Ok, farei isso!",
							customClass: {
								confirmButton: "btn btn-light"
							}
						}).then(function () {
							KTUtil.scrollTop();
						});

						console.log(resposta.codigo);
						formSubmitButton.removeAttribute('data-kt-indicator');

						// Enable button
						formSubmitButton.disabled = false;


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

							console.log(resposta.codigo);
							formSubmitButton.removeAttribute('data-kt-indicator');

							// Enable button
							formSubmitButton.disabled = false;
						}
					});




				} else {
					Swal.fire({
						text: "Desculpe,o Formulário de cadastro não foi validado, tente novamente",
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



		
$("#bt_criar_novo_projeto").click(function() {


   
	$("#kt_modal_novo_projeto_form")[0].reset();
	stepperObj.goFirst();
	quill.update();


$("#kt_modal_novo_projeto").modal("hide");


setTimeout(() => {

	$("#kt_modal_novo_projeto").modal("show");
	
}, 300);



});



$("#kt_modal_novo_projeto").on('hidden.bs.modal', function (e) {

	$("#kt_modal_novo_projeto_form")[0].reset();
	stepperObj.goFirst();
	quill.update();
	


});

	}



	var initValidation = function () {
		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		// Step 1
		validations.push(FormValidation.formValidation(
			form,
			{
				fields: {
					nome_projeto: {
						validators: {
							notEmpty: {
								message: 'Defina o Nome para este Projeto.'
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
			modalEl = document.querySelector('#kt_modal_novo_projeto');

			if (!modalEl) {
				return;
			}

			modal = new bootstrap.Modal(modalEl);

			stepper = document.querySelector('#kt_modal_novo_projeto_stepper');
			form = document.querySelector('#kt_modal_novo_projeto_form');
			formSubmitButton = stepper.querySelector('[data-kt-stepper-action="submit"]');
			formContinueButton = stepper.querySelector('[data-kt-stepper-action="next"]');

			initStepper();
		
			initValidation();
		}
	};



}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    NovoProjeto.init();
});


var quill = new Quill('#quill_obs_projeto', {
	modules: {
		toolbar: [
			[{
				header: [1, 2, false]
			}],
			['bold', 'italic', 'underline'],
			['image', 'code-block']
		]
	},
	placeholder: 'Observações...',
	theme: 'snow' // or 'bubble'
});

var Modal_Novo_Projeto = document.getElementById('kt_modal_novo_projeto')


Modal_Novo_Projeto.addEventListener('shown.bs.modal', function () {


	$('#cliente_projeto').select2({
		templateSelection: optionFormat,
		templateResult: optionFormat
	});
	
	$("#periodo_contrato").daterangepicker({
		startDate: moment().startOf("day"),
		locale: {
			format: "D/M/Y"
		}
	})
 

// Format options
var optionFormat = function(item) {
    if ( !item.id ) {
        return item.text;
    }

    var span = document.createElement('span');
    var imgUrl = item.element.getAttribute('data-kt-select2-user');
    var template = '';

    template += '<span class="badge badge-light-success">'+imgUrl+'</span> &nbsp';
    template += item.text;

    span.innerHTML = template;

    return $(span);
}





});






$("#bt_cadastro_cliente").click(function(e) {

	e.preventDefault();

	console.log("Form novo cliente");


	
	$("#div_select_cliente_projeto").toggleClass("d-none");

$("#cadastro_cliente").toggleClass("d-none");


})





$('#codigo_obra').maxlength({
    threshold: 10,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});



