                  function Totais_Widget_27() {

                        $.getJSON('../../crud/dashboard/consulta-widget-27.php?modelo_consulta=2&periodo=7', function (object) {

                            // objeto JSON
                            const valor_total_widget_27 = object.valor_total_widget_27;
                            const porcentagem_total_widget_27 = object.porcentagem_total_widget_27;
                            const classe_widget_27 = object.classe_widget_27;
                            const classe_badge_widget_27 = object.classe_badge_widget_27;
                            const icone_widget_27 = object.icone_widget_27;
                        
                            // Atualizar os elementos HTML diretamente com os valores das propriedades do objeto JSON
                            $("#valor_total_widget_27").html(valor_total_widget_27);
                            $("#porcentagem_total_widget_27").html(porcentagem_total_widget_27);
                            $("#classe_widget_27").addClass(classe_widget_27);
                            $("#classe_badge_widget_27").addClass(classe_badge_widget_27);
                            $("#icone_widget_27").toggleClass(icone_widget_27);
                        
                        });
                        
                        
                        
                        


                    }

                    function Totais_Widget_28() {

                        $.getJSON('../../crud/dashboard/consulta-widget-28.php?modelo_consulta=2&periodo=180', function (objects) {

                            // var element = document.getElementsByTagName('#step_header');
                            const valor_total_widget_28 = [];
                            const porcentagem_total_widget_28 = [];
                            const classe_widget_28 = [];
                            const classe_badge_widget_28 = [];
                            const icone_widget_28 = [];

                            for (let obj of objects) {
                                if (!valor_total_widget_28.includes(obj.valor_total_widget_28)) {
                                    valor_total_widget_28.push(obj.valor_total_widget_28);

                                    $("#valor_total_widget_28").html(valor_total_widget_28);
                                }

                                if (!porcentagem_total_widget_28.includes(obj.porcentagem_total_widget_28)) {
                                    porcentagem_total_widget_28.push(obj.porcentagem_total_widget_28);

                                    $("#porcentagem_total_widget_28").html(porcentagem_total_widget_28);
                                }

                                if (!classe_widget_28.includes(obj.classe_widget_28)) {
                                    classe_widget_28.push(obj.classe_widget_28);

                                    $("#classe_widget_28").addClass(classe_widget_28);
                                }

                                if (!classe_badge_widget_28.includes(obj.classe_badge_widget_28)) {
                                    classe_badge_widget_28.push(obj.classe_badge_widget_28);

                                    $("#classe_badge_widget_28").addClass(classe_badge_widget_28);
                                }

                                if (!icone_widget_28.includes(obj.icone_widget_28)) {
                                    icone_widget_28.push(obj.icone_widget_28);

                                    $("#icone_widget_28").toggleClass(icone_widget_28);
                                }


                            }


                        });


                    }
                    
                    
                    
                    $(".gera_relatorio").click(function () {

                        console.log("Intenção de Impressão");
                    
                        var id = $(this).attr("data-id");
                        var titulo = $(this).attr("data-titulo");
                        var content = document.getElementById(id);

                        printJS({
                            documentTitle: 'STEP: '+titulo, // o novo título que deseja definir para o documento de impressão
                            printable: content,
                            type: 'html',
                           
                            css: './assets/css/style.bundle.css',
                            scanStyles: false,
                            showModal: true,
                            modalMessage: 'Gerando a Impressão, Por favor, aguarde...' })
                    })    
                        

                
                    // grafico checkin dashboard:    

                
//=========[ cockpit ======================================================================]
                    $(".bt_exibe_cockpit").click(function () {
                         

                        $(".bt_exibe_cockpit").toggleClass('bt_oculta_cockpit');

                        $("#div_draggable_cockpit").toggleClass('d-none');

                        $(".icone_bt_exibe_cockpit").toggleClass('fa-door-open');


                        $.ajax({
                            url: "/crud/dashboard/cockpit/consulta-itens-cockpit-usuario.php",
                            dataType: 'html',
                            beforeSend: function () {

                                
                                if (!localStorage.getItem('cockpit_carregado')) {

                                let timerInterval
                                Swal.fire({
                                title: 'Carregando seu Cockpit pela 1ª vez.',
                                text:'Estamos preparando seus Dados!',
                                html: 'Eles estarão prontos em <b></b> segundos.',
                                timer: 5000,
                                icon: "warning",
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading()
                                    const b = Swal.getHtmlContainer().querySelector('b')
                                    timerInterval = setInterval(() => {
                                        b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
                                    }, 100)
                                },
                                willClose: () => {
                                    clearInterval(timerInterval)
                                }
                                }).then((result) => {
                                /* Read more about handling dismissals below */
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    console.log('I was closed by the timer')
                                    
                                }
                                }) 

                            }

                                $("#div_cockpit").html(`<div class="col-lg-12 alert alert-warning d-flex align-items-center p-5 mb-10">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
                                <span class="svg-icon svg-icon-2hx svg-icon-warning me-4">
                                    <svg width="14" height="21" viewBox="0 0 14 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 6.20001V1.20001H2V6.20001C2 6.50001 2.1 6.70001 2.3 6.90001L5.6 10.2L2.3 13.5C2.1 13.7 2 13.9 2 14.2V19.2H12V14.2C12 13.9 11.9 13.7 11.7 13.5L8.4 10.2L11.7 6.90001C11.9 6.70001 12 6.50001 12 6.20001Z" fill="currentColor"/>
                                    <path d="M13 2.20001H1C0.4 2.20001 0 1.80001 0 1.20001C0 0.600012 0.4 0.200012 1 0.200012H13C13.6 0.200012 14 0.600012 14 1.20001C14 1.80001 13.6 2.20001 13 2.20001ZM13 18.2H10V16.2L7.7 13.9C7.3 13.5 6.7 13.5 6.3 13.9L4 16.2V18.2H1C0.4 18.2 0 18.6 0 19.2C0 19.8 0.4 20.2 1 20.2H13C13.6 20.2 14 19.8 14 19.2C14 18.6 13.6 18.2 13 18.2ZM4.4 6.20001L6.3 8.10001C6.7 8.50001 7.3 8.50001 7.7 8.10001L9.6 6.20001H4.4Z" fill="currentColor"/>
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-warning">Buscando seus Gráficos em Cockpit</h4>
                                    <span>Por favor, aguarde...</span>
                                </div>
                            </div>`);



                            },
                            success: function (data) {


                                 // var containers = document.querySelector("#div_cockpit");

                                $("#div_cockpit").html(data);


                                


                    console.log("Cockpit carregado com sucesso.");


                            localStorage.setItem('cockpit_carregado', 'true');



                            },
                            error: function () {

                                toastr.error("Falha na Construção do Cockpit.", "Cockpit");  
                            
                            }, complete: function (){


                                const containers = document.querySelectorAll("#div_cockpit .min-h-200px draggable-zone");

                                if (containers.length === 0) {

                                    console.log("não carregou a opção d remover janelas do cokpit."+containers.length)
                                    return false;
                                }
                                
                                
                                const swappable = new Sortable.default(containers, {
                                    draggable: ".draggable",
                                    handle: ".draggable .draggable-handle",
                                    mirror: {
                                        //appendTo: selector,
                                        appendTo: "body",
                                        constrainDimensions: true
                                    }
                                });


                            console.log("Cockpit carregado com sucesso.");


                            }
                        });



                    });

                    // finaliza grafico checkin dashboard


                    //==== pega id clicado na listagem da tabela ===//
                    jQuery(document).on("click", ".apaga_cockpit", function (e) {

                        e.preventDefault();

                        var id_cockpit_selecionado = $(this).data('id');

                        var cockpit_nome = $(this).data("nome_regra");



                        swal.fire({
                            title: 'Você têm certeza?',
                            html: "Esta ação não poderá ser desfeita! <br> Item Cockpit: <b>" + cockpit_nome + '</b>',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sim, Apague!',
                            cancelButtonText: 'Cancelar'
                        }).then(function (result) {
                            if (result.value) {

                                $.ajax({
                                    url: '../../crud/dashboard/cockpit/action-item-regra-cockpit.php',
                                    type: 'post',
                                    data: 'acao=apagar&id_cockpit=' + id_cockpit_selecionado,
                                    processData: true,
                                    success: function (response) {
                                        if (response != 0) {
                                            swal.fire(
                                                'Apagado!',
                                                'Seu Item de Cockpit foi Apagado com Sucesso!',
                                                'success'
                                            )


                                            //$(".bt_exibe_cockpit").trigger("click");

                                        } else {
                                            alert('Falha ao Apagar Item');
                                        }
                                    },complete: function (){
                                       
                                        $("#div_cockpit").load("../../crud/dashboard/cockpit/consulta-itens-cockpit-usuario.php", function() {
                                           
                                        });

                                    }
                                });




                            }
                        });


                        e.stopImmediatePropagation();

                        console.log("Cockpit:" + cockpit_nome + " ID: " + id_cockpit_selecionado);
                    });
                        //==== pega id clicado na listagem da tabela ===//

//====finaliza cockpit ==============================================================================================

                    // select obras e estacoes dimanimco do cockpit que irá trazer os plcodes e indicadores interligados.

                    function signOut() {
                        
                        var auth2 = gapi.auth2.getAuthInstance();

                        auth2.signOut().then(function () {
                            console.log('User signed out.');

                            location.href = '../../crud/login/logout.php';
                        });
                    }


                    // inicia o carregamento dos módulos (funções)

                    Totais_Widget_27();

                    Totais_Widget_28();




                    /* 
                    Coloca um loading temporário na div com a classe mencionada, para dar o aguarde da exibição dos dados
                    (function() {
                        var targets = document.querySelectorAll(".widget_dashboard");
                        var blockUIs = [];
                    
                        
                        
                        // Criar um objeto KTBlockUI para cada target
                        targets.forEach(function(target) {
                            blockUIs.push(new KTBlockUI(target));
                        });
                        
                        // Bloquear todos os targets
                        blockUIs.forEach(function(blockUI) {
                        
                            blockUI.block();
                        });
                    
                        // Liberar todos os targets após um segundo
                        setTimeout(() => {
                            blockUIs.forEach(function(blockUI) {
                                blockUI.release();
                            });
                        }, 6000);
                    })(); */

                    





                $("#kt_activities_toggle").click(function(){
                    atualizarDiv_Log_Atividades();
                });

                /* setInterval(function() {
                    atualizarDiv_Log_Atividades();
                }, 60000); // Atualiza a cada 6 segundos */



                var Modal_Novo_Cockpit = document.getElementById('kt_modal_create_cockpit');

                if (Modal_Novo_Cockpit) {
                    Modal_Novo_Cockpit.addEventListener('shown.bs.modal', function () {
                
                        // Format options
                        var optionFormat = function (item) {
                            if (!item.id) {
                                return item.text;
                            }
                
                            var span = document.createElement('span');
                            var imgUrl = item.element.getAttribute('data-kt-select2-user');
                            var template = '';
                
                            template += '<span class="badge badge-light-success">' + imgUrl + '</span> &nbsp';
                            template += item.text;
                
                            span.innerHTML = template;
                
                            return $(span);
                        }
                
                        // Init Select2 --- more info: https://select2.org/
                        $('#projeto_cockpit').select2({
                            templateSelection: optionFormat,
                            templateResult: optionFormat
                        });
                    });
                }
                

            
            
//==================[ controla drawer chat na instância geral] =========================

            var drawerEl = document.querySelector("#kt_drawer_chat");
            var drawer = KTDrawer.getInstance(drawerEl);

            if (drawer) {
                // A instância já foi iniciada, execute a ação desejada


                drawer.on("kt.drawer.show", function() {
 
                    var id_suporte = window.id_suporte;
                    var id_usuario = window.id_usuario;
                    var id_conversa = window.id_conversa;
                    
                    console.log("Aberto Chat Suporte");
                    console.log("ID Suporte: " + window.id_suporte);
                    console.log("ID Conversa: " + window.id_conversa);
                    console.log("ID Usuário Remetente: " + window.id_usuario);
                    
                    var date = new Date(Date.now() + 3 * 60 * 60 * 1000); // +3 hours from now
                    var options = { expires: date };
                    KTCookie.set("chat_usuario_conversa", id_usuario, options);
                    KTCookie.set("chat_id_suporte_conversa", id_suporte, options);
                    KTCookie.set("chat_id_conversa_suporte", id_conversa, options);
             
                      
                    drawer.update();
                   // drawer.toggle();
              
                });
              
              
                drawer.on("kt.drawer.after.hidden", function() {

                    console.log("Fechado o Chat do Suporte");

                    // Para interromper a execução limpo o KTCookie da sessao do suporte e usuarios selecionados anteriormente
                 
                    KTCookie.remove("chat_usuario_conversa");
                    KTCookie.remove("chat_id_suporte_conversa");
                    KTCookie.remove("chat_id_conversa_suporte");
                 
                    const mensagemBody = document.querySelector('#message-container');
                    // Redefine o conteúdo do corpo da mensagem para uma string vazia
                    mensagemBody.innerHTML = '';
                 
                    drawer.update();
              
              });



              
              } else {
                // A instância ainda não foi iniciada

                var drawerEl = document.querySelector("#kt_drawer_chat");
                const drawer = KTDrawer.createInstances();
              }


            KTCookie.remove("chat_usuario_conversa");
            KTCookie.remove("chat_id_suporte_conversa");
            KTCookie.remove("chat_id_conversa_suporte");


//===========[ controla drawer janela suporte na instância geral]================

            var drawer_Suporte_EL = document.querySelector("#drawer_Suporte");
            var drawer_Suporte = KTDrawer.getInstance(drawer_Suporte_EL);

            if (drawer_Suporte) {

              
            drawer_Suporte.on("kt.drawer.show", function() { // quando começa abrir

                console.log("Janela Suporte quando começa a de abrir");

                          

                console.log("ID Suporte: " + window.id_suporte);
                console.log("ID Conversa: " + window.id_conversa);

                var id_suporte = window.id_suporte;


                $.ajax({
                    type: 'GET',
                    url: '../../views/suportes/modal-suporte.php',
                    dataType: 'html',
                    data: {
                        id: id_suporte
                    },
                    success: function(retorno) {
            
                     
                          // obtenha a referência à div com o ID "kt_drawer_chat_toggle"
               var drawerEl = document.querySelector("#drawer_Suporte");
               var drawer = KTDrawer.getInstance(drawerEl);
               drawer.update();
            
                        $("#div_conteudo_ticket").html(retorno);
                        $("#info_id_suporte").html('Ticket nº: '+id_suporte);
                        
                        drawer.update();
                     
                              
            
                        
                    },
                    error: function() {
                        alert("Falha ao coletar dados !!!");
                    }
                });


            });

            drawer_Suporte.on("kt.drawer.shown", function() { // quando termina de abrir

              
                 console.log("Janela Suporte quando termina de abrir");
            });


            drawer_Suporte.on("kt.drawer.after.hidden", function() {
                console.log("Fechada Janela Suporte");


            });
              
            }
                else {
                // A instância ainda não foi iniciada

                var drawer_Suporte_EL = document.querySelector("#drawer_Suporte");
                const drawer_Suporte = KTDrawer.createInstances();
              }

//:Fim===========[ controla drawer janela suporte na instância geral]================
              
//===========[ controla drawer janela Tarefas]================
         
var drawer_Tarefas_EL = document.querySelector("#drawer_Tarefas");
var drawer_Tarefas = KTDrawer.getInstance(drawer_Tarefas_EL);

if (drawer_Tarefas) {

  
drawer_Tarefas.on("kt.drawer.show", function() { // quando começa abrir

    console.log("Janela Tarefa quando começa a de abrir");

              


    $.ajax({
        type: 'GET',
        url: '../../views/projetos/tarefas/minhas-tarefas.php',
        dataType: 'html',
        success: function(retorno) {

         
              // obtenha a referência à div com o ID "kt_drawer_chat_toggle"
   var drawerEl = document.querySelector("#drawer_Tarefas");
   var drawer = KTDrawer.getInstance(drawerEl);
   drawer.update();

            $("#div_conteudo_Tarefas").html(retorno);
          
            
            drawer.update();
         
                  

            
        },
        error: function() {
            alert("Falha ao coletar dados !!!");
        }
    });


});

drawer_Tarefas.on("kt.drawer.shown", function() { // quando termina de abrir

  
     console.log("Janela Tarefa quando termina de abrir");
});


drawer_Tarefas.on("kt.drawer.after.hidden", function() {
    console.log("Fechada Janela Tarefa");


});
  
}
    else {
    // A instância ainda não foi iniciada

    var drawer_Tarefas_EL = document.querySelector("#drawer_Tarefas");
    const drawer_Tarefas = KTDrawer.createInstances();

    console.log("Drawer Tarefas Pronto para uso.")
  }


//:Fim===========[ controla drawer janela Tarefas]================


// controlar as notificações e coleçÕes de notificações por usuário, essa funcao sera executada numa modal:


  // Select all handler
  const handleSelectAll = () =>{
    // Define variables
    const selectAll = form.querySelector('#Seleciona_todos_Alertas');
    const allCheckboxes = form.querySelectorAll('[type="checkbox"]');

    // Handle check state
    selectAll.addEventListener('change', e => {

        // Apply check state to all checkboxes
        allCheckboxes.forEach(c => {
            c.checked = e.target.checked;
        });
    });
}




//<<<<<<<<<<<<<<<<<<<<<<<<<




function loadScripts(url) {
    return new Promise((resolve, reject) => {
      $.ajax({
        url: url,
        dataType: 'script',
        success: resolve,
        error: reject,
        cache: true
      });
    });
  }

  loadScripts('../../js/dashboard/notificacoes-dashboard.js', '../../js/flatpickr/pt.js')
  .then(() => {
    console.log('DOM fully loaded and parsed - Scripts carregados com sucesso.');
    // Inicialize a funcionalidade do script notificacoes-dashboard.js aqui
  })
  .catch((error) => {
    console.error('Erro ao carregar o script:', error);
  });



  // com relação as coleções de notificação coloquei controler para validar se o projeto já existe ou não na janela que o user abriu
  // pq elas serão redefinidas quando a modal abrir, qando ela fecha os cookies ja saem, mas se o usuario atualizar a pagina o cookie permanece, 
  //entao essa tratativa resolve isso:

  if (KTCookie.get('id_obra_colecao')) {
    KTCookie.remove('id_obra_colecao');
}

if (KTCookie.get('nome_obra_colecao')) {
    KTCookie.remove('nome_obra_colecao');
}


  // funcao para chamar a biblioteca toast de notificaçÕes em tela em qualquer instancia
//******************** */ Função para criar toasts *************** exemplo:  createMetronicToast_Unico('Título', 'Mensagem', 5000, 'success', 'bi bi-check2-square'); 
/* O parâmetro tempoExibicao é em milissegundos, então 5000 é igual a 5 segundos. tipoNotificacao deve ser 'success' ou 'error'. icone é opcional, e se não for fornecido,
ele será definido com base no tipo de notificação.
*/

function createMetronicToast_Unico(title, mensagem, tempoExibicao, tipoNotificacao, icone) {
    // Obtém a data e a hora atuais
    var now = new Date();

    // Formata a data e a hora no padrão pt-BR
    var formattedDateTime = now.toLocaleString('pt-BR');

    // Defina a cor e o ícone com base no tipo de notificação
    var corIcone = tipoNotificacao === 'success' ? 'text-success' : 'text-danger';
    var icone = icone ?? (tipoNotificacao === 'success' ? 'bi bi-check2-square' : 'bi bi-x-square');

    var toastHTML = `
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${tempoExibicao}">
            <div class="toast-header">
                <i class="${icone} fs-2 ${corIcone} me-3">
                    <span class="path1"></span><span class="path2"></span>
                </i>
                <strong class="me-auto">${title}</strong>
                <small>${formattedDateTime}</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${mensagem}
            </div>
        </div>`;

    var containerId = 'kt_docs_toast_stack_container';
    var container = document.getElementById(containerId);

    // Se o contêiner ainda não existir, cria um novo
    if (!container) {
        container = document.createElement('div');
        container.id = containerId;
        container.className = "toast-container position-fixed top-0 end-0 p-3 z-index-1050";
        document.body.appendChild(container);
    }

    // Cria um elemento DOM a partir do string HTML
    var template = document.createElement('template');
    template.innerHTML = toastHTML.trim();
    var newToast = template.content.firstChild;

    container.appendChild(newToast);

    // Cria uma nova instância do toast
    var toast = new bootstrap.Toast(newToast, { delay: delay });
    toast.show();
    /* var toast = bootstrap.Toast.getOrCreateInstance(newToast);
    toast.show(); */
}


// finaliza funcao de toast unico

/******************[ Funcao Multiplas Notificações do STEP ao Usuário ] **********************/

// Função para criar toasts
function createMetronicToast(title, mensagem, delay = 5000) {
    // Obtém a data e a hora atuais
    var now = new Date();

    // Formata a data e a hora no padrão pt-BR
    var formattedDateTime = now.toLocaleString('pt-BR');

    var toastHTML = `
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-check2-square fs-2 fs-2 text-success me-3">
                    <span class="path1"></span><span class="path2"></span>
                </i>
                <strong class="me-auto">${title}</strong>
                <small>${formattedDateTime}</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${mensagem}
            </div>
        </div>`;

    var containerId = 'kt_docs_toast_stack_container';
    var container = document.getElementById(containerId);

    // Se o contêiner ainda não existir, cria um novo
    if (!container) {
        container = document.createElement('div');
        container.id = containerId;
        container.className = "toast-container position-fixed top-0 end-0 p-3 z-index-1050";
        document.body.appendChild(container);
    }

    // Cria um elemento DOM a partir do string HTML
    var template = document.createElement('template');
    template.innerHTML = toastHTML.trim();
    var newToast = template.content.firstChild;

    container.appendChild(newToast);

    
   // Cria uma nova instância do toast com a opção delay
   var toast = new bootstrap.Toast(newToast, { delay: delay });
   toast.show();
}




//================================[ Monitora Filtro de Projeto para o Dashboard ]==========================================

$('#select_filtro_projeto').on('select2:select', function (e) {
    e.preventDefault();
  
    var id_projeto = e.params.data.id;  // Pega o valor do option selecionado
    var nome_Projeto = e.params.data.text;  // Pega o texto do option selecionado
    var NivelProjeto = $(e.params.data.element).attr('data-nivelprojeto');  // Pega o valor do atributo data-nivelprojeto do option selecionado

    console.log('Nivel Projeto Seleção : '+ NivelProjeto);


    KTCookie.set("nome_projeto", nome_Projeto );
    KTCookie.set("projeto_atual", id_projeto );
    

    // Faz um pedido AJAX para o script PHP
    $.ajax({
        url: "/crud/dashboard/altera-nivel-acesso-filtro-projeto.php",
        type: 'GET',
        data: {
            "acao": "altera_nivel_projeto",
            "projeto_atual": id_projeto,
            "nome_projeto": nome_Projeto,
            "nivel_acesso_usuario": NivelProjeto
        },
        beforeSend: function() {
            $("#limpa_filtro_projeto").prop("disabled", true);
            
            $('#overlay').fadeIn();
            
            var title = "Filtro de Projeto";
            var mensagem = '<span class="text-gray-700 fs-6" >Novo Projeto Selecionado:</span> <span class="fw-bold text-warning fs-6" >'+nome_Projeto+'</span>, <span class="text-gray-700 fs-6" >e acessará com o Nível de Acesso do Projeto:</span> <span class="text-success  fw-bold fs-4" >'+NivelProjeto+'</span>.<br>Estamos atualizando seus dados.<br>Por favor, aguarde...';

            createMetronicToast(title, mensagem, 5000, 'success', 'bi bi-check2-square');

        },
        success: function(data) {
            console.log(data);

            $('#overlay').fadeOut();
            createMetronicToast('Projeto Selecionado: '+nome_Projeto, 'Estamos atualizando seu Dashboard, por favor, aguarde...', 5000, 'success', 'bi bi-check2-square');

            setTimeout(() => {
               

                location.reload();
            }, 5000);
          
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Aqui você pode tratar os erros da requisição, se houver
            console.error('Erro na requisição: ', textStatus, errorThrown);
        }
    });
});


                     
$("#limpa_filtro_projeto").click(function () {

    var filtro = KTCookie.get("projeto_atual");
  
    if (filtro != undefined) {
     
  
        $("#limpa_filtro_projeto").attr("data-kt-indicator", "on");
       

    
       // Faz um pedido AJAX para o script PHP
    $.ajax({
        url: "/crud/dashboard/altera-nivel-acesso-filtro-projeto.php",
        type: 'GET',
        data: {
            "acao": "limpa_filtro_projeto",
            "projeto_atual": filtro
            
        },
        beforeSend: function() {
            $('#overlay').fadeIn();

            $("#limpa_filtro_projeto").prop("disabled", true);
            
            KTCookie.remove("nome_projeto");
             KTCookie.remove("projeto_atual");
            
            var title = "Limpar Filtro de Projeto";
            var mensagem = '<span class="text-gray-700 fw-bold fs-6" >Atualizando Projeto Atual</span>, Estamos atualizando seus dados.<br>Por favor, aguarde...';

            createMetronicToast(title, mensagem, 5000, 'success', 'bi bi-check2-square');

        },
        success: function(data) {
            console.log(data);


            $("#limpa_filtro_projeto").removeAttr("data-kt-indicator");

            $('#overlay').fadeOut();
            
            createMetronicToast('Atualizando Dashboard', 'Estamos atualizando seu Dashboard, por favor, aguarde...', 5000, 'success', 'bi bi-check2-square');

            setTimeout(() => {
                location.reload();
            }, 5000);
          
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Aqui você pode tratar os erros da requisição, se houver
            console.error('Erro na requisição: ', textStatus, errorThrown);
        }
    });


  
       
       
    } else {
      swal.fire("Filtro Ausente!", 'Não há filtro de Projeto aplicado.', "warning");
    }
  });
  
  var filtro = KTCookie.get('projeto_atual');
  var nome_projeto = KTCookie.get('nome_projeto');
  
  if (filtro !== null && filtro !== '' && filtro !== undefined) {
   // $("#ProjetoSelecionado").removeClass('d-none'); 
    $("#div_nome_projeto").removeClass('d-none');
    $("#nome_projeto_filtro").removeClass('d-none');
    if (nome_projeto !== null && nome_projeto !== '' && nome_projeto !== undefined)  {
      $("#nome_projeto_filtro").html(nome_projeto);
    }
  } else {
    //$("#ProjetoSelecionado").addClass('d-none'); 
    $("#div_nome_projeto").addClass('d-none');
    $("#nome_projeto_filtro").addClass('d-none');
  }

//================================[ FIM: Monitora Filtro de Projeto para o Dashboard ]==========================================



//================================[ Monitora Atividade do Usuário no Sistema ]==========================================
var tempoInatividade = 60 * 60 * 1000; // 1 hora
var tempoAviso = 60 * 1000; // 60 segundos

var tempoDeslogar;

function iniciarContagem() {
    localStorage.setItem('ultimoMovimento', Date.now().toString());
    tempoDeslogar = setTimeout(mostrarAviso, tempoInatividade);
}

function reiniciarContagem() {
    clearTimeout(tempoDeslogar);
    iniciarContagem();
}

$(document).on('mousemove keydown', reiniciarContagem);

function mostrarAviso() {
    // Se houve atividade em alguma outra aba, reiniciar a contagem
    if (Date.now() - localStorage.getItem('ultimoMovimento') < tempoInatividade) {
        reiniciarContagem();
        return;
    }

    let timerInterval;
    Swal.fire({
        title: 'Inatividade detectada',
        html: 'Você será desconectado em <b></b> segundos se não houver mais atividade.',
        icon: 'warning',
        timer: tempoAviso,
        imageUrl: '/tema/src/media/svg/illustrations/landing.svg',
        imageWidth: 400,
        showConfirmButton: false,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
            const b = Swal.getHtmlContainer().querySelector('b');
            timerInterval = setInterval(() => {
                b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
            }, 100);
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
            window.location.href = "/crud/login/logout.php";
        }
    });

    // Reinicia a contagem se o usuário interage com o aviso
    $(document).one('mousemove keydown', function() {
        Swal.close();
        reiniciarContagem();
    });
}

iniciarContagem();

// Verifica se o usuário mudou a aba do navegador
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        // A aba está inativa, apenas reiniciar a contagem
        reiniciarContagem();
    } else {
        // A aba está ativa, retomar monitoramento de inatividade
        Swal.close();
        $(document).off('mousemove keydown');
        reiniciarContagem();
        $(document).on('mousemove keydown', reiniciarContagem);
    }
});
//================================[ FIM: Monitora Atividade do Usuário no Sistema ]==========================================

// verifica se as variaveis armazenadas vindas do email de alerta estão vazias

 // Função para obter o valor de um cookie pelo nome
 function getCookie(name) {
    let cookies = document.cookie.split(';');
    for(let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i];
        let [key, value] = cookie.split('=').map(c => c.trim());
        if (key === name) {
            return decodeURIComponent(value);
        }
    }
    return null;
}

function verifica_cookies() {

    console.log('verifica_cookies');

    let ticket = getCookie('id_tipo_suporte_ticket');
    let usuario = getCookie('usuario_ticket');
    let mailkey = getCookie('mailkey_ticket');
    let projeto_ticket = getCookie('projeto_ticket');

    if (
        ticket !== null && 
        usuario !== null && 
        mailkey !== null && 
        projeto_ticket !== null
    ) {  
        let acao = "limpa_filtro_projeto";
        let projeto_atual = 'filtro';
        let url = `../../crud/dashboard/altera-nivel-acesso-filtro-projeto.php?acao=${encodeURIComponent(acao)}&projeto_atual=${encodeURIComponent(projeto_atual)}`;
        

        fetch(url)
    .then(response => {
        if (response.status === 200) {
            console.log("Requisição bem-sucedida");
            
            Swal.fire({
                title: 'Suporte por Coleção de Notificações',
                html: `<strong>Até breve!\n\n${getCookie('nome_usuario', '')}</strong><br><br>Os Tickets Relacionados ao seu Alerta Recebido foram removidos.<br><br>Caso queira acessar sua relação de tickets da Notificação recebida, acesse a URL recebida pela Notificação enviada.<br><br>Redirecionando para a página de Suporte...`,
                icon: "warning",
                timer: 5000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading()
                    let b = Swal.getHtmlContainer().querySelector('b')
                    let timerInterval_suporte_saida = setInterval(() => {
                        b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
                    }, 100)
                },
                willClose: () => {
                    clearInterval(timerInterval_suporte_saida)
                    redirect();
                }
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.timer) {
                    console.log('Direcionado após o tempo acabar')
                    redirect();
                }
            })
        } else {
            console.error(`Erro na requisição: ${response.status}`);
        }
    })
    .catch(error => console.error('Erro na Fetch:', error));


        
        
}

function redirect() {
    window.location.href = "/views/relatorios/relatorios-suportes.php?tipo_relatorio=acompanhamento_suporte&titulo_relatorio=Acompanhamento de Suportes&suporte=0";
}

}




//================================[ Monitora Filtro de Projeto para o Dashboard ]==========================================



       