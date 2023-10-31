$(document).ready(function() {

          $("#data_previsao_suporte").flatpickr({
              enableTime: !0,
              altInput: true,
              altFormat: "F j, Y H:i",
              dateFormat: "Y-m-d H:i",
              minDate: "today",
              locale: "pt" 
          });



          $('#atender_suporte').select2({
            
          });

          // seleciona o elemento select2
          //var select2_suporte = $('#atender_suporte').select2();

          // adiciona um listener para quando o valor for alterado

          $('#atender_suporte').on('select2:select', function (e) {
            // obtém o valor selecionado
            var valorSelecionado = $(this).val();
            
            // faz uma ação diferente com base no valor selecionado
            switch (valorSelecionado) {

              case '1':
              $("#div_suporte_atende_ticket").removeClass("d-none");
              $("#div_suporte_controla_ticket").removeClass("d-none"); 
              $("#texto_suporte_motivo_atendimento").html('O Ticket Voltará a Aguardar Atendimento, Explique o Motivo:');
                $("#bt_valida_suporte_ticket").html("Ticket em Aberto");
                $("#bt_valida_suporte_ticket").removeClass("btn-light-success").addClass("btn-light-warning");
                $("#div_suporte_data_prevista_ticket").addClass("d-none");
                console.log('ticket em aberto');
                break;

              case '2':
                  $("#div_suporte_controla_ticket").removeClass("d-none"); 
                  $("#div_suporte_atende_ticket").removeClass("d-none");
                $("#texto_suporte_motivo_atendimento").html('Informe a Ação Inicial que será Tomada:');
                $("#bt_valida_suporte_ticket").html("Atender Chamado");
                $("#bt_valida_suporte_ticket").removeClass("btn-light-warning").addClass("btn-light-success");
                $("#div_suporte_data_prevista_ticket").addClass("d-none");
                console.log('Atende Chamado');
                break;

              case '3':
                  $("#div_suporte_controla_ticket").removeClass("d-none"); 
                  $("#div_suporte_atende_ticket").removeClass("d-none");
              $("#texto_suporte_motivo_atendimento").html('Identifique o Terceirizado e o Motivo de Terceirizar:');
                $("#bt_valida_suporte_ticket").html("Indicar Terceiros");
                $("#bt_valida_suporte_ticket").removeClass("btn-light-success").addClass("btn-light-warning");
                $("#div_suporte_data_prevista_ticket").removeClass("d-none");
                console.log('Opção indica terceiros');
                break;

              case '4':
              $("#div_suporte_controla_ticket").removeClass("d-none");  
              $("#div_suporte_atende_ticket").removeClass("d-none");     
              $("#texto_suporte_motivo_atendimento").html('Informe a Tratativa Final que encerrou o Suporte:');
              $("#bt_valida_suporte_ticket").html("Encerrar Ticket");
              $("#bt_valida_suporte_ticket").removeClass("btn-light-warning").addClass("btn-light-success");
              $("#div_suporte_data_prevista_ticket").addClass("d-none");
                console.log('Opção 4 finaliza chamado');
                $status = 'Fechado';
                break;


                case '5':
                  $("#div_suporte_controla_ticket").removeClass("d-none");  
                  $("#div_suporte_atende_ticket").removeClass("d-none");     
                  $("#texto_suporte_motivo_atendimento").html('Informe o Motivo que precisou indicar um prazo para a Resolutiva:');
                  $("#bt_valida_suporte_ticket").html("Informar Prazo");
                  $("#bt_valida_suporte_ticket").removeClass("btn-light-success").addClass("btn-light-warning");
                  $("#div_suporte_data_prevista_ticket").removeClass("d-none");
                    console.log('Opção 5 Prazo Informado');
                    break;

              default:
                $("#bt_valida_suporte_ticket").html(" **ERRO**");
                console.log('Opção inválida selecionada');
                break;
            }
          });

          // Substitua a chave da API pelo valor da sua chave
          //const apiKey = 'AIzaSyAQsOKlWz3MbMeQHMrfAEtVR7ajrSj9274';

          $("#bt_valida_suporte_ticket").click(function() {

        

          var dados = $("#form_suporte_ticket").serialize();
               

          $.ajax({
            type: 'POST',
            url: '../../crud/suporte/action-suporte.php',
            dataType: 'json',
            data: dados,
            beforeSend: function(){

              createMetronicToast('Controle do Suporte Informa:', 'Estamos enviando sua solicitação, aguarde por favor.', delay = 4000);
            
             
            },
            success: function(retorno){

              if(retorno.codigo === 1){

                 // Verifica se o drawer está aberto
                 var drawerEl = document.querySelector("#drawer_Suporte");
               var drawer = KTDrawer.getInstance(drawerEl);
               drawer.hide();
             

              setTimeout(function() {
             
                KTUtil.scrollTop();
              
              }, 2000);

                createMetronicToast('Controle do Suporte Informa:', retorno.mensagem, delay = 5000);
                
              }


              if(retorno.codigo == 0){

                setTimeout(function() {
  
                  Swal.fire({
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
               
                }, 3000);
  
                  createMetronicToast('Controle do Suporte Informa:', 'Informação da Devolutiva na janela aberta.', delay = 5000);
                }
                  
                
            },
            error: function(retorno){
               
              Swal.fire({
                icon: 'error',
                html: retorno.mensagem,
                timer: 2000, // tempo em milissegundos
                showConfirmButton: false, // não exibir botão de confirmação
              }).then(function(){
                // código para fechar a janela
                KTUtil.scrollTop();
                //window.close(); // fecha a janela atual
                
                
              
              });
            }
          });

        });




 });           



 
        

  


