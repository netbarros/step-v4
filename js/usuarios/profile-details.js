// Define form element
const form = document.getElementById('kt_form_usuario');

// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
var validator = FormValidation.formValidation(
    form,
    {
        fields: {
            'fname': {
                validators: {
                    notEmpty: {
                        message: 'Campo obrigatório.'
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
document.getElementById("kt_envia_form_usuario").addEventListener("click", function(e) {

    // Prevent default button action
    e.preventDefault();


    const submitButton_envia_form_usuario = document.getElementById("kt_envia_form_usuario");

    // Validate form before submit
    if (validator) {
        validator.validate().then(function (status) {
            console.log('validated!');

            if (status == 'Valid') {
              

                // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                var dados = $("#kt_form_usuario").serialize();
                $.ajax({
                    type: "POST",
                    url: "../../crud/usuarios/action-usuarios.php",
                    data: dados,
                    dataType: 'json',
                    beforeSend: function () {

                        // Exibir loading indication
                        submitButton_envia_form_usuario.setAttribute('data-kt-indicator', 'on');

                        // Disable button to avoid multiple click
                        submitButton_envia_form_usuario.disabled = true;


                    },

                    success: function (data) {

                        console.log('Usuário Atualizado com Sucesso.');

                        console.log(data);

                        console.log(data.codigo);
                        // Remove loading indication
                        submitButton_envia_form_usuario.removeAttribute('data-kt-indicator');

                        // Enable button
                        submitButton_envia_form_usuario.disabled = false;


                        if (data.codigo == 1) {



                            createMetronicToast('Sucesso!', data.retorno);

                        

                        }
                        if (data.codigo == 0) {
                           


                            swal.fire("Ops!", data.retorno, "warning");


                        }
                        e.stopImmediatePropagation();

                    },
                    error: function (data) {

                        swal.fire("Erro!", "Não foi Possível Prosseguir!" + data.retorno, "error");

                        console.log('Falha no Processamento dos Dados.');
                        // Remove loading indication
                        submitButton_envia_form_usuario.removeAttribute('data-kt-indicator');

                        // Enable button
                        submitButton_envia_form_usuario.disabled = false;

                        e.stopImmediatePropagation();


                    }

                });
               //form.submit(); // Submit form
            }
        });
    }
});






var imageInputElement = document.querySelector("#upload_avatar");
var imageInput = new KTImageInput(imageInputElement);

// ações para imagem do avatar do usuário




imageInput.on("kt.imageinput.changed", function() {

    let formData = new FormData();           
    formData.append("avatar", avatar.files[0]);

    var id = $("#id_usuario_edicao").val();

    formData.append('id', id);


    formData.append('acao', 'cadastrar');
       
      

 $.ajax({
        type: 'POST',
        url: '/crud/usuarios/avatar-usuario.php',
        processData: false,
        contentType: false,
        cache: false,
        data: formData,
        beforeSend: function(){

            createMetronicToast('Por favor, Aguarde!', 'Enviando Imagem...');
        },
        success: function(resposta){

            var data = JSON.parse(resposta);

            if(data.codigo===1){


                if(data.avatar!=null){
                    var actual_link = window.location.protocol + "//" + window.location.hostname;

                    var url_avatar = actual_link + '/foto-perfil/' + data.avatar + '?v=' + new Date().getTime();

                
                    // Atualiza o atributo 'src' de todas as imagens com a classe 'imagem_avatar_usuario'
                    $('.imagem_avatar_usuario').attr('src', url_avatar);

                    KTCookie.set('imagem_avatar_usuario', url_avatar);
                }
                

                createMetronicToast('Sucesso!', 'Imagem Atualizada com Sucesso!');

            }

            if(data.codigo===0){

               Swal.fire({ 'title': 'Ops!', 'text': data.retorno, 'icon': 'warning' });
    
                }
                
        },
        error: function(){
            alert("Falha ao enviar dados !!!");
        }
    }); 

 


});




imageInput.on("kt.imageinput.canceled", function() {
     console.log("kt.imageinput.canceled event is fired");

     createMetronicToast('Cancelado pelo Usuário', 'O Avatar não foi Alterado!');

});






imageInput.on("kt.imageinput.removed", function() {
     console.log("kt.imageinput.removed event is fired");

     let formData = new FormData();   

     var id = $("#id_usuario_edicao").val();

     formData.append('id', id);
        
     formData.append('acao', 'remover');
 
  $.ajax({
         type: 'POST',
         url: '/crud/usuarios/avatar-usuario.php',
         processData: false,
         contentType: false,
         cache: false,
         data: formData,
         success: function(retorno){
 
 
            createMetronicToast('Remoção de Avatar', 'O Avatar foi Removido com Sucesso!');
         },
         error: function(){
             alert("Falha ao Executar o Comando SQL !");
         }
     }); 

});



// alteração da conta do acesso via google

$('#googleswitch').change(function() {


    if($(this).is(":checked")) {


        //var returnVal = confirm("Are you sure?");


        Swal.fire({
            title: 'Vincular Conta Google',
            html: `Informe o <strong>E-mail </strong> da <span class="badge badge-primary fs-6"> Conta Google</span> que deseja vincular ao Login deste Usuário.`,
            icon: "info",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "Sim, Prosseguir",
            cancelButtonText: 'Não, Cancelar',
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: 'btn btn-danger'
            },  
        }).then(function(result) {


            if (result.value) {

 // $(this).attr("checked", returnVal);

            $("#div_email_google_usuario").toggleClass("d-none");
            $("#div_vincula_conta_google").toggleClass("d-none");          


            }else{

                $("#div_email_google_usuario").toggleClass("d-none");
                $("#div_vincula_conta_google").toggleClass("d-none"); 
            }
        
        
        
        })


    } else{


        Swal.fire({
            html: `Você deseja  <strong>Desativar</strong>, o <span class="badge badge-primary"> Login do  Google</span> para este usuário?`,
            icon: "info",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "Sim, Desative!",
            cancelButtonText: 'Não, Cancelar',
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: 'btn btn-danger'
            }
        }).then(function(result) {


            if (result.value) {

                
    var email_google = $('#googleswitch').data('email_google');

    var id_usuario = $('#googleswitch').data('id_usuario');

    var  acao = 'cancela_vincula_conta_google'

    var dados = {acao: acao, emailGoogle: email_google, id: id_usuario};

    $.ajax({
        type: 'POST',
        url: '/crud/usuarios/action-usuarios.php',
        dataType: 'json',
        data: dados,
        beforeSend: function(){

        },
        error: function(retorno){

            Swal.fire(retorno.mensagem);
        },
        success: function(retorno){

            createMetronicToast('Cancelamento de Vinculação de Conta Google', retorno.mensagem);

        }
    });

    

            }
        
        
        
        })
    }


   // $('#textbox1').val($(this).is(':checked'));        
});


// Vincula conta google
$("#bt_vincula_conta_google").on("click", function(e){

    // Prevent default button action
    e.preventDefault();

    var dados = $("#form_vincula_email_google").serialize();


              
               $.ajax({
                   type: 'POST',
                   url: '../../crud/usuarios/action-usuarios.php',
                   dataType: 'json',
                   data: dados,
                   beforeSend: function(){

                 
                      
                    createMetronicToast("Por favor, aguarde", "Vinculando Conta do Usuário");

                   },
                   error: function(retorno){
                    Swal.fire(retorno.mensagem);
                   },
                   success: function(retorno){

                    createMetronicToast('Vinculação de Conta Google', retorno.mensagem)
                   

                    $("#div_email_google_usuario").toggleClass("d-none");
                    $("#div_vincula_conta_google").toggleClass("d-none"); 
                   }
               });

            }) 
      
            

// altera nivel


$("#bt_altera_nivel_usuario_perfil").click(function (e) {
    e.preventDefault();

    var dados = $("#form_nivel_acesso_perfil").serialize();

    $.ajax({
        type: 'POST',
        url: '../../crud/usuarios/action-usuarios.php',
        dataType: 'json',
        data: dados,
        beforeSend: function(){

            createMetronicToast("Por favor, aguarde", "Solicitando Alteração...");

        },
        error: function(retorno){
            Swal.fire(retorno.mensagem);
        },
        success: function(retorno){
            if (retorno.codigo == 1) {

                createMetronicToast('Alteração de Nível de Acesso', retorno.mensagem)

            }

            if (retorno.codigo == 0) {

                Swal.fire(retorno.mensagem);

                }


        }
    });


})




// Phone
Inputmask({
    "mask" : "(99) 99999-9999"
}).mask("#telefone_usuario_perfil");