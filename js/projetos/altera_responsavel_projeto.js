// Criação: 20/06/2021

    $(".troca_responsavel_projeto").click(function() {
        var id_usuario_Projeto = $(this).data("id"); // Pega o valor do data-id
        var id_Projeto_usuario = $(this).data("id_obra"); // Pega o valor do data-id_obra
        var Nome_usuario_Projeto = $(this).data("nome_user"); // Pega o valor do data-id_obra
        var nivel_user_projeto = $(this).data("nivel_user_projeto"); // Pega o valor do data-id_obra
        var row = $(this).closest("tr"); // Recupera a linha da tabela que contém o elemento clicado

        // Realiza uma chamada AJAX para o endpoint action_projetos.php
        $.ajax({
            url: "/crud/projetos/action-projetos.php",
            type: "POST",
            data: {
                id_usuario_Projeto: id_usuario_Projeto,
                id_Projeto_usuario: id_Projeto_usuario,
                nivel_user_projeto: nivel_user_projeto,
                acao: "troca_responsavel_projeto"
            },
            success: function(response) {
                // Aqui você pode tratar a resposta do servidor. Para este exemplo,
                // assumi que o servidor retorna um JSON com um campo "success".
               
                if (response.codigo==1) {

                    createMetronicToast('Troca de Usuário:', 'Validando o Responsável Direto deste Projeto.', 5000, 'success', 'bi bi-check2-square');

                    createMetronicToast('Atualizando seu Dashboard', 'Por favor, aguarde...', 5000, 'success', 'bi bi-check2-square');
                  
                      // Se a operação for bem-sucedida, remove a linha da tabela
                     
								
							
                      setTimeout(function() {                                   

                   
                    location.reload();
                    }, 2000);

                  
                } else {
                   // Se algo deu errado, você pode alertar o usuário aqui
                   createMetronicToast('Remoção de Usuário: '+ Nome_usuario_Projeto, response.mensagem, 5000, 'error', 'bi bi-check2-square');  
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Caso ocorra algum erro na chamada AJAX, você pode tratá-lo aqui
                Swal.fire({
                    title: "Erro ao promover usuario Responsável do Projeto!",
                    html:'Para este nível de Usuário não é permitido torna-lo Responsável!',
                    text: jqXHR, textStatus, errorThrown,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, farei isso!",
                    customClass: {
                        confirmButton: "btn btn-light"
                    }
                }).then(function () {
                    KTUtil.scrollTop();
                });

              
                console.error(textStatus, errorThrown);
            }
        });
    });




    $(".remove_responsavel_projeto").click(function() {

        var id_usuario_Projeto = $(this).data("id"); // Pega o valor do data-id
        var id_Projeto_usuario = $(this).data("id_obra"); // Pega o valor do data-id_obra
        var Nome_usuario_Projeto = $(this).data("nome_user"); // Pega o valor do data-id_obra
        var row = $(this).closest("tr"); // Recupera a linha da tabela que contém o elemento clicado

        // Realiza uma chamada AJAX para o endpoint action_projetos.php
        $.ajax({
            url: "/crud/projetos/action-projetos.php",
            type: "POST",
            data: {
                id_usuario_Projeto: id_usuario_Projeto,
                id_Projeto_usuario: id_Projeto_usuario,
                acao: "remove_responsavel_projeto"
            },
            beforeSend: function () {
                // Disable button to avoid multiple click 
                createMetronicToast('Processando sua Solicitação', 'Por favor, aguarde...', 5000, 'success', 'bi bi-check2-square');

            },
            success: function(response) {
                // Aqui você pode tratar a resposta do servidor. Para este exemplo,
                // assumi que o servidor retorna um JSON com um campo "success".
               
                if (response.codigo==1) {

                    createMetronicToast('Removendo Responsável:', 'Alterando permissões deste Usuário.', 5000, 'success', 'bi bi-check2-square');

                    createMetronicToast('Atualizando seu Dashboard', 'Por favor, aguarde...', 5000, 'success', 'bi bi-check2-square');
                  
                      // Se a operação for bem-sucedida, remove a linha da tabela
                     
								
							
                      setTimeout(function() {                                   

                   
                    location.reload();
                    }, 3000);

                  
                }   
                if (response.codigo==0) {

                   // Se algo deu errado, você pode alertar o usuário aqui
                   createMetronicToast('Remoção de Usuário: '+ Nome_usuario_Projeto, response.mensagem, 5000, 'error', 'bi bi-bug'); 
                   
                    
                   setTimeout(function() {                                   

                   
                    location.reload();
                    }, 3000);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Caso ocorra algum erro na chamada AJAX, você pode tratá-lo aqui
                Swal.fire({
                    title: "Erro ao promover usuario Responsável do Projeto!",
                    text: jqXHR, textStatus, errorThrown,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, farei isso!",
                    customClass: {
                        confirmButton: "btn btn-light"
                    }
                }).then(function () {
                    
                    
                    setTimeout(function() {                                   

                   
                        location.reload();
                        }, 2000);
                });

               
                console.error(textStatus, errorThrown);
            }
        });
    });     

