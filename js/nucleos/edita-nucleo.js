// Define form element
const form = document.getElementById('kt_modal_edita_nucleo_form');

// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
var validator = FormValidation.formValidation(
    form,
    {
        fields: {
            'nome_nucleo': {
                validators: {
                    notEmpty: {
                        message: 'O Nome de Identificaçáo do Núcleo é Obrigatório'
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
const submitButton = document.getElementById('bt_atualiza_nucleo');
submitButton.addEventListener('click', function (e) {
    // Prevent default button action
    e.preventDefault();

    // Validate form before submit
    if (validator) {
        validator.validate().then(function (status) {
            console.log('validated!');

            if (status == 'Valid') {
                // Exibir loading indication
                submitButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/


             

                var dados = $("#kt_modal_edita_nucleo_form").serialize();
                $.ajax({
                    type: 'POST',
                    url: '../../crud/nucleos/action-nucleos.php',
                    dataType: 'json',
                    data: dados,
                    beforeSend: function(){
                        // Disable button to avoid multiple click 
                submitButton.disabled = true;

                // Exibir loading indication
                submitButton.setAttribute('data-kt-indicator', 'on');

                    },					
                    success: function(retorno){
                        

                        // A $( document ).ready() block.


                        if(retorno.codigo=='1'){



                            //stepperObj.goNext();
						KTUtil.scrollTop();

						createMetronicToast('Incluído com Sucesso', retorno.mensagem, 6000, 'success', 'bi bi-check2-square');

							
					
						setTimeout(() => {
                            createMetronicToast('Aguarde estamos atualizando...', 'Preparando seus Dados Atualizados', 6000, 'success', 'bi bi-check2-square');
							$('#kt_modal_edita_nucleo').modal('hide');			
							location.reload();
						}, 6000);


                              
                            console.log(retorno.codigo);

                        // Hide loading indication
                    submitButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    submitButton.disabled = false;
                    

                   
                  

                }else {

                    Swal.fire({
                        text: retorno.mensagem,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, farei isso!",
                        customClass: {
                            confirmButton: "btn btn-light"
                        }
                    }).then(function () {
                        KTUtil.scrollTop();
                    });

                    console.log(retorno.codigo);
                }

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

                        console.log(retorno.codigo);
                    }
                });


            }
        });
    }
});



// Init Select2 --- more info: https://select2.org/


$('#edita_projeto_nucleo').select2({
   
});




