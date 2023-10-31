
var isCheckingAtualizarNotificacoes = false;
function atualizarNotificacoes() {

     if (isCheckingAtualizarNotificacoes) {
        return;
    }
isCheckingAtualizarNotificacoes = true;
                                                
    var qtdNotificacoes = 0;
    $.ajax({
      url: "../../crud/dashboard/consulta-quantidade-notificacoes.php",
      type: "GET",
      dataType: "json",
      success: function(response) {
        qtdNotificacoes = response.qtdNotificacoes;

        if(qtdNotificacoes>0){
        $('#qtdade_notificacoes_header').html(qtdNotificacoes + ' mensagens');
        $("#pisca_alerta_notificacoes").removeClass('d-none');
    }else {
        $('#qtdade_notificacoes_header').html('0 mensagens');
        $("#pisca_alerta_notificacoes").addClass('d-none');

    }
        console.log('Total de notificações: ' + qtdNotificacoes);


       

      },
     error: function(xhr, status, error) {
       console.log("Erro ao verificar Quantidade de mensagens: " + error);
      },
        complete: function() {
            isCheckingAtualizarNotificacoes = false;
        }
    });
}

setInterval(atualizarNotificacoes, 5000);// atualiza a cada 5 segundos (5000 milissegundos)



var isCheckingatualizarDiv_Notificacoes_Alertas = false;                   
function atualizarDiv_Notificacoes_Alertas() {

    if (isCheckingatualizarDiv_Notificacoes_Alertas) {
        return;
    }
    isCheckingatualizarDiv_Notificacoes_Alertas = true;

    $("#div_notificacoes_alertas").load("../../crud/dashboard/consulta-notificacoes-alertas.php", function() {
        isCheckingatualizarDiv_Notificacoes_Alertas = false;
    });
}

setInterval(function() {
atualizarDiv_Notificacoes_Alertas();
}, 6000); // Atualiza a cada 5 segundos



var isCheckingatualizarDiv_Notificacoes_Suportes = false;  
function atualizarDiv_Notificacoes_Suportes() {

    if (isCheckingatualizarDiv_Notificacoes_Suportes) {
        return;
    }
    isCheckingatualizarDiv_Notificacoes_Suportes = true;

$("#div_notificacoes_suporte").load("../../crud/dashboard/consulta-notificacoes-suportes.php", function() {
    isCheckingatualizarDiv_Notificacoes_Suportes = false;
});
                  
}

setInterval(function() {
atualizarDiv_Notificacoes_Suportes();
}, 7000); // Atualiza a cada 10 segundos


var isCheckingatualizarDiv_Notificacoes_Estrutural = false;  
function atualizarDiv_Notificacoes_Estrutural() {


    if (isCheckingatualizarDiv_Notificacoes_Estrutural) {
        return;
    }
    isCheckingatualizarDiv_Notificacoes_Estrutural = true;

$("#div_notificacoes_estrutural").load("../../crud/dashboard/consulta-notificacoes-estrutural.php", function() {
    isCheckingatualizarDiv_Notificacoes_Estrutural = false;
});

}

setInterval(function() {
atualizarDiv_Notificacoes_Estrutural();
}, 8000); // Atualiza a cada 12 segundos



function atualizarDiv_Log_Atividades() {
$("#aguarde_log_carregar").removeClass("d-none");

$("#div_log_atividades").load("../../crud/dashboard/consulta-log-atividades.php", function() {
    $("#aguarde_log_carregar").addClass("d-none");
});
}


