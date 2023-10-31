"use strict";

var chatApp = function() {
var messageInput = $('#message-input');
var messageTemplateOut = $('#message-template-out');
var messageTemplateIn = $('#message-template-in');
var messageContainer = $('#message-container');


function sendMessage() {
    var idConversa = KTCookie.get("chat_id_conversa_suporte");
    var usuarioConversa = KTCookie.get("chat_usuario_conversa");

    if (messageInput.val().length !== 0) {
        var messageOut = messageTemplateOut.clone().removeClass('d-none');
        messageOut.find('[data-kt-element="message-text"]').text(messageInput.val());
        messageContainer.append(messageOut);
        messageContainer.scrollTop(messageContainer.prop('scrollHeight'));

       

            $("#mensagem_padrao_chat").html("Sua mensagem foi enviada com sucesso, aguardando a resposta do usuário.");

            $.ajax({
                url: '../../crud/suporte/chat/enviaMensagem.php',
                type: 'GET',
                data: { message: messageInput.val() },
                dataType: 'json',
                success: function(response) {

                    
                    var messageIn = messageTemplateIn.clone().removeClass('d-none');
                    messageIn.find('[data-kt-element="message-text"]').text(response.mensagem);
                    messageContainer.append(messageIn);
                    messageContainer.scrollTop(messageContainer.prop('scrollHeight'));


                    console.log("resposta do envio: "+response.mensagem)
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
            

            messageInput.val('');
        

    }
}

var isCheckingForNewMessages = false;

function checkForNewMessages() {

    if (isCheckingForNewMessages) {
        return;
    }

    isCheckingForNewMessages = true;

    $.ajax({
        url: '../../crud/suporte/chat/verificaMensagem.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.codigo=='1') {    //
                $("#pisca_alerta_chat").removeClass("d-none");
                var messageIn = messageTemplateIn.clone().removeClass('d-none');
                // inclui a mensagem
                messageIn.find('[data-kt-element="message-text"]').text(response.mensagem);
                messageContainer.append(messageIn);

                // inclui o horario da mensagem recebida
                messageIn.find('[data-kt-element="hora-mensagem"]').text(response.hora);
                messageContainer.append(messageIn);

                // inclui o nome do usuário que enviou a mensagem
                messageIn.find('[data-kt-element="usuario-direto"]').text(response.remetente);
                messageContainer.append(messageIn);


              // inclui a foto do usuário que enviou a mensagem
                messageIn.find('[data-kt-element="foto-remetente"]').attr('src', response.foto);
                messageContainer.append(messageIn);

                var date = new Date(Date.now() + 3 * 60 * 60 * 1000); // +3 hours from now
                var options = { expires: date };
                KTCookie.set("chat_usuario_conversa", response.id_remetente, options);
                KTCookie.set("chat_id_conversa_suporte", response.id_conversa, options);
                KTCookie.set("chat_id_suporte_conversa", response.id_suporte, options);

                messageContainer.scrollTop(messageContainer.prop('scrollHeight'));

                console.log("Chat recebido:" + response.mensagem + ' iD conversa: '+response.id_conversa+' E ID Suporte: '+response.id_suporte);
                
            } if (response.codigo=='0') {

                var messageIn = messageTemplateIn.clone().removeClass('d-none');
                // inclui a mensagem
                messageIn.find('[data-kt-element="message-text"]').text(response.mensagem);
                messageContainer.append(messageIn);

                $("#pisca_alerta_chat").addClass("d-none");
            }

            if (response.codigo=='2') {


                $("#pisca_alerta_chat").addClass("d-none");
            }
        },
        error: function(xhr, status, error) {
            console.log("Erro ao verificar novas mensagens: " + error);
        },
        complete: function() {
            isCheckingForNewMessages = false;
        }
    });

    
}

 setInterval(checkForNewMessages, 3000);






return {
    init: function() {
        messageInput.keydown(function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                sendMessage();
            }
        });

        $('[data-kt-element="send"]').click(function() {
            sendMessage();
            
        });
    }
}


}();

$(document).ready(function() {
chatApp.init();

});
