// Define form_novo_usuario element
var form_novo_usuario = document.getElementById('kt_form_novo_usuario');

// Init form_novo_usuario validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
var validator_form_novo_usuario = FormValidation.formValidation(
    form_novo_usuario,
    {
        fields: {
            'fname': {
                validators: {
                    notEmpty: {
                        message: 'Campo obrigatório'
                    }
                }
            },
			'lname': {
                validators: {
                    notEmpty: {
                        message: 'Campo obrigatório'
                    }
                }
            },
			'company': {
                validators: {
                    notEmpty: {
                        message: 'Campo obrigatório'
                    }
                }
            },
		
			'email': {
                validators: {
                    notEmpty: {
                        message: 'Campo obrigatório'
                    }
                }
            },
			'nivel_usuario': {
                validators: {
                    notEmpty: {
                        message: 'Campo obrigatório'
                    }
                }
            },
			'perfil_usuario': {
                validators: {
                    notEmpty: {
                        message: 'Campo obrigatório'
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
var submitButton_form_novo_usuario = document.getElementById('bt_novo_usuario');
submitButton_form_novo_usuario.addEventListener('click', function (e) {
    // Prevent default button action
    e.preventDefault();

    // Validate form_novo_usuario before submit
    if (validator_form_novo_usuario) {
        validator_form_novo_usuario.validate().then(function (status) {
            console.log('validated!');

            if (status == 'Valid') {
                // Exibir loading indication
                submitButton_form_novo_usuario.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitButton_form_novo_usuario.disabled = true;

                // Simulate form_novo_usuario submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/


             

                var dados = $("#kt_form_novo_usuario").serialize();
                $.ajax({
                    type: 'POST',
                    url: '../../crud/usuarios/action-usuarios.php',
                    dataType: 'json',
                    data: dados,
                    beforeSend: function(){
                        // Disable button to avoid multiple click 
                submitButton_form_novo_usuario.disabled = true;

                // Exibir loading indication
                submitButton_form_novo_usuario.setAttribute('data-kt-indicator', 'on');

                    },					
                    success: function(retorno){
                        

                        // A $( document ).ready() block.


                        if(retorno.codigo=='1'){


                            Swal.fire({
                                icon: 'success',
                                html: retorno.mensagem,
                                timer: 2000, // tempo em milissegundos
                                showConfirmButton: false, // não exibir botão de confirmação
                              }).then(function(){
                                // código para fechar a janela
                                KTUtil.scrollTop();
                                //window.close(); // fecha a janela atual
    
                                $("#kt_modal_novo_usuario").modal("hide");
                              
                              });
        
                            console.log(retorno.codigo);

                        // Hide loading indication
                    submitButton_form_novo_usuario.removeAttribute('data-kt-indicator');

                    // Enable button
                    submitButton_form_novo_usuario.disabled = false;
                    

                    setTimeout(() => {
                        
                        location.reload();
                    }, 4000);

                  

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

					      // Hide loading indication
						  submitButton_form_novo_usuario.removeAttribute('data-kt-indicator');

						  // Enable button
						  submitButton_form_novo_usuario.disabled = false;

                    console.log(retorno.codigo);
                }

                    },	error: function(retorno){
                        Swal.fire({
                            title: "Retorno do Sistema",
                            html:retorno,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, farei isso!",
                            customClass: {
                                confirmButton: "btn btn-light"
                            }
                        }).then(function () {
                            KTUtil.scrollTop();
                        });

						      // Hide loading indication
							  submitButton_form_novo_usuario.removeAttribute('data-kt-indicator');

							  // Enable button
							  submitButton_form_novo_usuario.disabled = false;

                        console.log(retorno.codigo);
                    }
                });


            }
        });
    }
});




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











// Init Select2 --- more info: https://select2.org/

$('#projeto_usuario').select2({ });

$('#perfil_usuario').select2({ });

$('#nivel_usuario').select2({ });



// Phone
Inputmask({
    "mask" : "(99) 999-999999"
}).mask("#telefone_corporativo_usuario");





// Email address
Inputmask({
    mask: "*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
    greedy: false,
    onBeforePaste: function (pastedValue, opts) {
        pastedValue = pastedValue.toLowerCase();
        return pastedValue.replace("mailto:", "");
    },
    definitions: {
        "*": {
            validator: '[0-9A-Za-z!#$%&"*+/=?^_`{|}~\-]',
            cardinality: 1,
            casing: "lower"
        }
    }
}).mask("#email_corporativo_usuario");



$('#perfil_usuario').on('select2:select', function (e) {
    e.preventDefault();
    // o ciclo de leitura é a recorrência da tarefa, se for semanal, abre a seleção para escolher os dias da semana.
    var data = e.target.value;

    console.log(data);


	if (data === 'colaboradores') { // 

       
        $("#div_matricula_usuario").removeClass("d-none");

    } else { // horário agendado (controlado)

        $("#div_matricula_usuario").addClass("d-none");

       
    }
});