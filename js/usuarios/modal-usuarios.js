
function nivel(){	

    
					
    $('input[type="checkbox"]:checked').each(function (event) {    // $(':checkbox:checked')

            let id_user = this.value;
                                // $(this).val()

                var seletor = $("#select_lista_nivel_" + id_user);
                    
            
                // Disable #x
                seletor.prop("disabled", false);
                
                seletor.disabled = false;


// Define o background-color do select2
//seletor.next('.select2-container').find('.select2-selection').css('background-color', '#198754');

// Define a borda do select2
seletor.next('.select2-container').find('.select2-selection').css({
    'border': '1px solid #009ef7',
    'border-radius': '3px'
});

            console.log(seletor);


            

       

  
			
        });
}
                            
// Define form element
var form_usuario_nucleo = document.getElementById('form_inclui_usuarios_nucleo');



// Submit button handler
var submitButton_usuario_nucleo = document.getElementById('kt_modal_usuarios_nucleo_submit');
submitButton_usuario_nucleo.addEventListener('click', function (e) {
    // Prevent default button action
    e.preventDefault();


                // Exibir loading indication
                submitButton_usuario_nucleo.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitButton_usuario_nucleo.disabled = true;

                // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/


             

                var dados = $("#form_inclui_usuarios_nucleo").serialize();
                $.ajax({
                    type: 'POST',
                    url: '../../crud/projetos/action-projetos.php',
                    dataType: 'json',
                    data: dados,
                    beforeSend: function(){
                        // Disable button to avoid multiple click 
                submitButton_usuario_nucleo.disabled = true;

                // Exibir loading indication
                submitButton_usuario_nucleo.setAttribute('data-kt-indicator', 'on');

                    },					
                    success: function(retorno){
                        

                        // A $( document ).ready() block.


                        if(retorno.codigo=='1'){

                            KTUtil.scrollTop();

                            createMetronicToast('Usuários selecionados Incluídos:', retorno.mensagem, 5000, 'success', 'bi bi-check2-square');
                            $("#kt_modal_users_search").modal("hide");

                            setTimeout(function() {

                                location.reload();



                            }, 3000);


                                
                            console.log(retorno.codigo);

                        // Hide loading indication
                    submitButton_usuario_nucleo.removeAttribute('data-kt-indicator');

                    // Enable button
                    submitButton_usuario_nucleo.disabled = false;
                    

                           
                  

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

                          // Hide loading indication
                          submitButton_usuario_nucleo.removeAttribute('data-kt-indicator');

                        // Enable button
                        submitButton_usuario_nucleo.disabled = false;

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


                           // Hide loading indication
                           submitButton_usuario_nucleo.removeAttribute('data-kt-indicator');

                        // Enable button
                        submitButton_usuario_nucleo.disabled = false;

                    }
                });


              
    
});