var urlParams = new URLSearchParams(window.location.search);

var projetoId;

if (urlParams.has('id') && !isNaN(urlParams.get('id'))) {
    projetoId = parseInt(urlParams.get('id'), 10);
} else {
    var cookieProjeto = Cookies.get('projeto_atual');
    projetoId = cookieProjeto ? cookieProjeto : null;
}

if (projetoId === null) {
    console.log('projetoId is null');



    kt_modal_users_search.modal('hide');

    Swal.fire({
        text: "Desculpe, parece que o ID do Seu Projeto não foi recuperado nesta ação, contate o Suporte.",
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: "Ok, farei isso!",
        customClass: {
            confirmButton: "btn btn-light"
        }
    }).then(function () {
        KTUtil.scrollTop();
    });


}



$("#kt_busca_usuarios").keyup(function(e){



    var q = "data=" + $("#kt_busca_usuarios").val();
    var busca = $("#kt_busca_usuarios").val();
    var id_projeto = projetoId;
    var sLength = q.length;
    if (sLength > 3){
    $.ajax({
        dataType: "html",
        url: "/crud/usuarios/action-usuarios.php?acao=buscar&"+ q+"&cliente_projeto="+projetoId,
        beforeSend: function(){
           $("#aguarde_spinner").removeClass("d-none");
         
          
        },
        success: function (data) {

          


            $("#id_projeto_busca_usuario").val(projetoId);


            $("#aguarde_spinner").addClass("d-none");

            
            $("#div_acao_usuarios_nucleo").removeClass("d-none");

      $("#searchButtonHeader").html("Resultados para <b>" + busca + "</b>");

    
        $("#searchResult").html(data);

       

        //console.log(data);

        

        // Init Select2 --- more info: https://select2.org/
$('[name="nivel_usuario_projeto[]"]').select2({
   
});
   
         
        },error: function(data){
            $("#div_acao_usuarios_nucleo").addClass("d-none");
            $("#searchResult").html(data);
        },

    });
}

});




var myModal_usuarios = document.getElementById('kt_modal_users_search');


myModal_usuarios.addEventListener('shown.bs.modal', function (event) {

    event.preventDefault();


    var button = $(event.relatedTarget);

    var recipientId    = button.data('id');    

    var modal = $(this);

    //modal.find('#minhaId').html(recipientId);


    
    $.ajax({
        type: 'POST',
        url: '/views/usuarios/modal-user-search.php',
        dataType: 'html',
        data: {
            id: recipientId 
        },
        beforeSend: function(){
            $("#aguardar_modal_carregar_usuarios" ).removeClass("d-none");
          
           
        },
        success: function(retorno){

           

            $("#aguardar_modal_carregar_usuarios" ).addClass("d-none");

            $("#conteudo_modal_dinamico_usuarios" ).html(retorno);

           

           

           
        },
        error: function(){
            alert("Falha ao coletar dados !!!");
        }
    });

    	
    //$("#conteudo_modal_dinamico" ).load( "../../views/projetos/modal-edita-projeto.php?id="+recipientId );
 
 

})



myModal_usuarios.addEventListener('hidden.bs.modal', function (event) {

   
    
})  
    




