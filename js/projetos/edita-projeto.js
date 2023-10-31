// Define form_edita_Projeto element
const form_edita_Projeto = document.getElementById('kt_modal_edita_projeto_form');

// Init form_edita_Projeto validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
var validator = FormValidation.formValidation(
    form_edita_Projeto,
    {
        fields: {
            'edita_nome_projeto': {
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
const submitButton_edita_Projeto = document.getElementById('bt_atualiza_projeto');
submitButton_edita_Projeto.addEventListener('click', function (e) {
    // Prevent default button action
    e.preventDefault();

    // Validate form_edita_Projeto before submit
    if (validator) {
        validator.validate().then(function (status) {
            console.log('validated!');

            if (status == 'Valid') {
                // Exibir loading indication
                submitButton_edita_Projeto.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitButton_edita_Projeto.disabled = true;

                // Simulate form_edita_Projeto submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/


                $("#edita_obs_projeto").val($("#quill_edita_obs_projeto").html());

                var dados = $("#kt_modal_edita_projeto_form").serialize();
                $.ajax({
                    type: 'POST',
                    url: '/crud/projetos/action-projetos.php',
                    dataType: 'json',
                    data: dados,
                    beforeSend: function () {
                        // Disable button to avoid multiple click 
                        submitButton_edita_Projeto.disabled = true;

                        // Exibir loading indication
                        submitButton_edita_Projeto.setAttribute('data-kt-indicator', 'on');

                    },
                    success: function (retorno) {


                        // A $( document ).ready() block.


                        if (retorno.codigo == 1) {

                            submitButton_edita_Projeto.removeAttribute('data-kt-indicator');

                            // Enable button
                            submitButton_edita_Projeto.disabled = false;

                            createMetronicToast('Edição de Projeto', retorno.mensagem, 5000, 'success', 'bi bi-check2-square');
                            //createMetronicToast('Edição de Projeto: ' + retorno.mensagem, 5000, 'success', 'bi bi-check2-square');
                           
                           
                            
                            KTUtil.scrollTop();
                            createMetronicToast('Atualizando seu Dashboard', 'Por favor, aguarde...', 5000, 'success', 'bi bi-check2-square');
                            
                            setTimeout(function() {
                                $("#kt_modal_edita_projeto").modal("hide");
                            location.reload();
                            }, 5000);
                          
                            
                            console.log(retorno.codigo);

                            // Hide loading indication



                        } else {

                            Swal.fire({
                                title: "Erro ao editar Projeto!",
                                html: retorno.mensagem,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, farei isso!",
                                customClass: {
                                    confirmButton: "btn btn-light"
                                }
                            }).then(function () {
                                KTUtil.scrollTop();
                            });
                            
                            submitButton_edita_Projeto.removeAttribute('data-kt-indicator');

                            // Enable button
                            submitButton_edita_Projeto.disabled = false;

                            console.log(retorno.codigo);
                        }

                    }, error: function (retorno) {
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
                            submitButton_edita_Projeto.removeAttribute('data-kt-indicator');

                            // Enable button
                            submitButton_edita_Projeto.disabled = false;
                        });

                        console.log(retorno.codigo);
                    }
                });


            }
        });
    }
});



var quill = new Quill('#quill_edita_obs_projeto', {
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




// Format options
var optionFormat = function (item) {
    if (!item.id) {
        return item.text;
    }

    var span = document.createElement('span');
    var imgUrl = item.element.getAttribute('data-kt-select2-user');
    var template = '';

    template += '<span class="badge badge-light-success">' + imgUrl + '</span> &nbsp';
    template += item.text;

    span.innerHTML = template;

    return $(span);
}

// Init Select2 --- more info: https://select2.org/
$('#edita_cliente_projeto').select2({
    templateSelection: optionFormat,
    templateResult: optionFormat
});


$("#edita_periodo_contrato").daterangepicker({
    autoUpdateInput: true,
    locale: {
        format: "DD/MM/YYYY"
    }
})


$('#edita_periodo_contrato').on('apply.daterangepicker', function (ev, picker) {
    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

    console.log(picker.startDate.format('DD/MM/YYYY'));
    console.log(picker.endDate.format('DD/MM/YYYY'));
});





$('#codigo_obra').maxlength({
    threshold: 10,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});