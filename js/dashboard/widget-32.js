var isCheckingAtualizaProjetosSemLeitura = false;
function atualizarProjetosSemLeitura() {

     if (isCheckingAtualizaProjetosSemLeitura) {
        return;
    }
isCheckingAtualizaProjetosSemLeitura = true;
                                                
    $.ajax({
        url: '../../crud/dashboard/consulta-widget-32.php?modelo_grafico=1&periodo=30',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            if (data.length == 0) {
                var alertDiv = `
                <div class="alert alert-danger" role="alert">
                    Nenhum resultado encontrado.
                </div>`;
                $('#kt_charts_widget_32').html(alertDiv);
            } else {
                var table = $('#kt_charts_widget_32').find('table');
                $.each(data, function (index, value) {
                    var row = '<tr>';
                    row += '<td><span class="bullet bullet-vertical h-9px bg-light"></span><a href="../../views/projetos/view-project.php?id=' + value.id_obra + '&projeto=' + value.nome_obra + '" target="_blank">' + value.nome_obra + '</a></td>';
                    row += '<td>' + value.nome_estacao + '</td>';
                    row += '<td>' + value.nome_ponto + '</td>';
                    row += '<td>' + value.nome_parametro + '</td>';
                    row += '<td>' + value.data_leitura + '</td>';
                    row += '</tr>';
                    table.append(row);
                });

                console.log('Contruída pelo retorno, a Tabela Projetos Sem Leitura, Total de Linhas: '+data.length);
            }

           
        },
        error: function(xhr, status, error) {
          console.log("Erro ao verificar Quantidade de mensagens: " + error);
         },
           complete: function() {
               isCheckingAtualizaProjetosSemLeitura = false;
           }
    });

}

setInterval(atualizarNotificacoes, 5000);// atualiza a cada 5 segundos (5000 milissegundos)

