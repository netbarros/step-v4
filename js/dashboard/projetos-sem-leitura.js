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
        beforeSend: function(){
            $("#kt_charts_widget_32").addClass("d-none");

            $("#mensagem-aguarde-tabela-projetos-sem-leitura").removeClass("d-none");
            
        },
        success: function (data) {

           

            if (data.length == 0) {
                var alertDiv = `
                <div class="alert alert-danger" role="alert">
                    Nenhum resultado encontrado.
                </div>`;
                $("#kt_charts_widget_32").removeClass("d-none");

                setTimeout(() => {
                    $('#kt_charts_widget_32').html(alertDiv);    
                }, 300);
                
            } else {
                $("#kt_charts_widget_32").removeClass("d-none");


                setTimeout(() => {
                    var table = $('#kt_charts_widget_32').find('table');
                $.each(data, function (index, value) {
                    var row = '<tr>';
                    row += '<td><a href="../../views/projetos/view-project.php?id=' + value.id_obra + '&projeto=' + value.nome_obra + '" target="_blank" class="text-gray-700 fw-bold text-hover-warning mb-1 fs-6"><span class="bullet bullet-vertical h-9px bg-light me-1 "></span>' + value.nome_obra + '</a></td>';
                    row += '<td>' + value.nome_estacao + '</td>';
                    row += '<td>' + value.nome_ponto + '</td>';
                    row += '<td>' + value.nome_parametro + '</td>';
                    row += '<td>' + value.data_leitura + '</td>';
                    row += '</tr>';
                    table.append(row);
                });  
                }, 300);

               
            }

            console.log('Contruída pelo retorno, a Tabela Projetos Sem Leitura, Total de Linhas: '+data.length);
        },
        error: function(xhr, status, error) {
          console.log("Erro ao verificar Tabela Projetos Sem Leitura: " + error);
         },
           complete: function() {
               isCheckingAtualizaProjetosSemLeitura = false;
               $("#mensagem-aguarde-tabela-projetos-sem-leitura").addClass("d-none");
           }
    });

}

setTimeout(atualizarProjetosSemLeitura, 300);//  (300 milissegundos)

