"use strict";

var chatApp = function() {
var messageInput = $('#message-input');
var messageTemplateOut = $('#message-template-out');
var messageTemplateIn = $('#message-template-in');
var messageContainer = $('#message-container');


function sendMessage() {
    if (messageInput.val().length !== 0) {
        var messageOut = messageTemplateOut.clone().removeClass('d-none');
        messageOut.find('[data-kt-element="message-text"]').text(messageInput.val());
        messageContainer.append(messageOut);
        messageContainer.scrollTop(messageContainer.prop('scrollHeight'));

        $.get('../../crud/suporte/chat/enviaMensagem.php', { message: messageInput.val() }, function(response) {
            var messageIn = messageTemplateIn.clone().removeClass('d-none');
            messageIn.find('[data-kt-element="message-text"]').text(response.message);
            messageContainer.append(messageIn);
            messageContainer.scrollTop(messageContainer.prop('scrollHeight'));
        });

        messageInput.val('');
    }
}


function checkForNewMessages() {
    $.ajax({
        url: '../../crud/suporte/chat/verificaMensagem.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.mensagem) {
                var messageIn = messageTemplateIn.clone().removeClass('d-none');
                // inclui a mensagem
                messageIn.find('[data-kt-element="message-text"]').text(response.mensagem);
                messageContainer.append(messageIn);

                // inclui o horario da mensagem recebida
                messageIn.find('[data-kt-element="hora-mensagem"]').text(response.hora);
                messageContainer.append(messageIn);

                // inclui o nome do usuário que enviou a mensagem
                messageIn.find('[data-kt-element="usuario-direto"]').text(response.usuario);
                messageContainer.append(messageIn);


                messageContainer.scrollTop(messageContainer.prop('scrollHeight'));

                console.log("Chat recebido:" + response.mensagem);
            }
        },
        error: function(xhr, status, error) {
            console.log("Erro ao verificar novas mensagens: " + error);
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
