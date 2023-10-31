document.addEventListener('DOMContentLoaded', function() {
$('#estacao_plcode').on('select2:select', function (e) { 
    e.preventDefault();

    var data = e.target.value;

    console.log(data);

    Gera_PLCode_Impressao(); // Chame a sua função aqui

    console.log("Selecionou o nucleo");
  });



  function Gera_PLCode_Impressao() {
 



    var tipo_relatorio = "listagem_qrcode";
    var estacao_plcode = $("#estacao_plcode").val();

    $("#texto_listagem_plcode").html("Por favor, aguarde...");
    $('#loader').removeClass('d-none');
    
    $('#progresso_relatorio').css('width', '10%');
   
    
    $.ajax({
        url: '../../crud/relatorios-plcodes/monta-relatorios.php',
        method: 'POST',
        data: {
            tipo_relatorio: tipo_relatorio,
            estacao_plcode: estacao_plcode
        },
        dataType: 'html',
        beforeSend: function(){
          
         // Exibe o elemento de "aguarde"
    $('#loader').removeClass('d-none');


    $('#progresso_relatorio').css('width', '50%');

    $("#texto_listagem_plcode").html("Gerando PLCode's...");

    
    
   

        },
        success: function(data) {

            $('#progresso_relatorio').css('width', '100%');
           
           
            $("#texto_listagem_plcode").html("Listagem de QR Codes");

            $('#listagem_qrcode').html(data);

         setTimeout(() => {
            $('#loader').addClass('d-none');
         }, 500);
    
         // $("#dados_relatorio_leituras_realizadas").html(data);
            //('Recebeu o dado de volta');
    
       
        
        },
        error: function(data) {
    
            Swal.fire(data);
         
        }
      });



}



    });


    
function exibirPopover() {
    // Inicializa o popover
    var popover = new bootstrap.Popover(document.querySelector('[data-bs-toggle="popover"]'));

    // Exibe o popover automaticamente ao abrir a página
    popover.show();

    // Oculta o popover após 3 segundos
    setTimeout(function() {
        popover.hide();
    }, 3000);
}

// Executa a função exibirPopover quando a página for carregada
window.onload = function() {
    exibirPopover();
};



