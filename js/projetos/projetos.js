


(function () {
    var targets = document.querySelectorAll(".widget_projeto");
    var blockUIs = [];



    // Criar um objeto KTBlockUI para cada target
    targets.forEach(function (target) {
        blockUIs.push(new KTBlockUI(target));
    });

    // Bloquear todos os targets
    blockUIs.forEach(function (blockUI) {

        blockUI.block();
    });

    // Liberar todos os targets após um segundo
    setTimeout(() => {
        blockUIs.forEach(function (blockUI) {
            blockUI.release();
        });
    }, 2000);
})();



var Modal_Novo_Projeto = document.getElementById('kt_modal_novo_projeto')


Modal_Novo_Projeto.addEventListener('shown.bs.modal', function (event) {

    event.preventDefault();
    var modal = $(this);

// Format options



   

    $.ajax({
        type: 'GET',
        url: '/views/projetos/modal-novo-projeto.php',
        dataType: 'html',
        
        beforeSend: function () {
            // $("#aguardar_projeto_carregar").removeClass("d-none");
        },
        success: function (retorno) {

            console.log("chegou dados");

            $("#aguardar_projeto_carregar").addClass("d-none");

            
            modal.find('#conteudo_modal_novo_projeto').html(retorno);
           // Init Select2 --- more info: https://select2.org/
$('#cliente_projeto').select2({
    templateSelection: optionFormat,
    templateResult: optionFormat
});


$("#periodo_contrato").daterangepicker({
    startDate: moment().startOf("day"),
    locale: {
        format: "D/M/Y"
    }
});

        },
        error: function () {
            alert("Falha ao coletar dados !!!");
        }
    });


// Init Select2 --- more info: https://select2.org/


})

var optionFormat = function(item) {
    if ( !item.id ) {
        return item.text;
    }

    var span = document.createElement('span');
    var imgUrl = item.element.getAttribute('data-kt-select2-user');
    var template = '';

    template += '<span class="badge badge-light-success">'+imgUrl+'</span> &nbsp';
    template += item.text;

    span.innerHTML = template;

    return $(span);
}



var Modal_Edita_Projeto = document.getElementById('kt_modal_edita_projeto');


$('#kt_modal_edita_projeto').on('shown.bs.modal', function (event) {

    event.preventDefault();

    var button = $(event.relatedTarget);

    var recipientId = button.data('id');

    var modal = $(this);

    //





    $.ajax({
        type: 'POST',
        url: '../../views/projetos/modal-edita-projeto.php',
        dataType: 'html',
        data: {
            id: recipientId
        },
        beforeSend: function () {
            // $("#aguardar_projeto_carregar").removeClass("d-none");
        },
        success: function (retorno) {

            console.log("chegou dados");

            $("#aguardar_projeto_carregar").addClass("d-none");

            $("#conteudo_modal_edita_projeto").html('teste');
            modal.find('#conteudo_modal_edita_projeto').html(retorno);
        },
        error: function () {
            alert("Falha ao coletar dados !!!");
        }
    });


    //$("#conteudo_modal_edita_projeto" ).load( "../../views/projetos/modal-edita-projeto.php?id="+recipientId );



})



Modal_Edita_Projeto.addEventListener('hidden.bs.modal', function (event) {

    location.reload();

})



var modal_detalhe_nucleo = document.getElementById('kt_modal_detalhe_nucleo');


modal_detalhe_nucleo.addEventListener('shown.bs.modal', function (event) {




    // Make the DIV element draggable:
    var element = document.querySelector('#kt_modal_detalhe_nucleo');





    // funcao para mover a janela===================
    dragElement(element);

    function dragElement(elmnt) {
        var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
        if (elmnt.querySelector('.modal-content')) {
            // if present, the header is where you move the DIV from:
            elmnt.querySelector('.modal-content').onmousedown = dragMouseDown;
        } else {
            // otherwise, move the DIV from anywhere inside the DIV:
            elmnt.onmousedown = dragMouseDown;
        }

        function dragMouseDown(e) {
            e = e || window.event;
            e.preventDefault();
            // get the mouse cursor position at startup:
            pos3 = e.clientX;
            pos4 = e.clientY;
            document.onmouseup = closeDragElement;
            // call a function whenever the cursor moves:
            document.onmousemove = elementDrag;
        }

        function elementDrag(e) {
            e = e || window.event;
            e.preventDefault();
            // calculate the new cursor position:
            pos1 = pos3 - e.clientX;
            pos2 = pos4 - e.clientY;
            pos3 = e.clientX;
            pos4 = e.clientY;
            // set the element's new position:
            elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
            elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
        }

        function closeDragElement() {
            // stop moving when mouse button is released:
            document.onmouseup = null;
            document.onmousemove = null;
        }
    }

    // finaliza funcao para mover janela
    var button = $(event.relatedTarget);

    var recipientId = button.data('id');

    var nome_nucleo = button.data('nome_nucleo');

    var modal = $(this);

    //modal.find('#minhaId').html(recipientId);


    $.ajax({
        type: 'POST',
        url: '../../views/projetos/nucleos/modal-detalha-nucleo.php',
        dataType: 'html',
        data: {
            id: recipientId,
            nome_nucleo: nome_nucleo
        },
        beforeSend: function () {
            $("#aguardar_detalhe_nucleo").removeClass("d-none");
        },
        success: function (retorno) {

            $("#aguardar_detalhe_nucleo").addClass("d-none");

            $("#conteudo_modal_detalhe_nucleo").html(retorno);
        },
        error: function () {
            alert("Falha ao coletar dados !!!");
        }
    });


    //$("#conteudo_modal_edita_projeto" ).load( "../../views/projetos/modal-edita-projeto.php?id="+recipientId );



})



modal_detalhe_nucleo.addEventListener('hidden.bs.modal', function (event) {

    //location.reload();

})




function atualizarDiv_Usuarios_Projeto() { //mensagem-aguarde-operadores_ativos_projeto

    document.getElementById("mensagem-aguarde-operadores_ativos_projeto").classList.remove("d-none");
    document.getElementById("mensagem-aguarde-operadores_ativos_projeto").classList.add("d-block");

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("div_operadores_ativos_projeto").innerHTML = this.responseText;

            // Remove a mensagem de aguarde
            document.getElementById("mensagem-aguarde-operadores_ativos_projeto").classList.add("d-none");


        }
    };
    xhttp.open("GET", "../../crud/projetos/consulta-usuarios-projeto.php", true);
    xhttp.send();
}

setTimeout(function () {
    atualizarDiv_Usuarios_Projeto();
}, 500); // Atualiza a cada 60 segundos



