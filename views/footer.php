   <div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
       <!--begin::Container-->
       <div class="container-xxl d-flex flex-column flex-md-row align-items-center justify-content-between">
           <!--begin::Copyright-->
           <div class="text-dark order-2 order-md-1">
               <span class="text-muted fw-semibold me-1">2023©</span>
               <a href="https://grupoep.com.br/eptech" target="_blank" class="text-gray-800 text-hover-primary">STEP - Grupo EP</a>
           </div>
           <!--end::Copyright-->
           <!--begin::Menu-->
           <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
               <li class="menu-item">
                   <a href="javascript:;" target="_blank" class="menu-link px-2">Sobre</a>
               </li>
               <li class="menu-item">
                   <a href="javascript:;" title="Suporte do Sistema. Aprenda &amp; Inspire-se." data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="top" id="kt_help_toggle" class="menu-link px-2">Suporte</a>
               </li>
               <li class="menu-item">
                   <a href="javascript:;" target="_blank" class="menu-link px-2">Documentação</a>
               </li>
               <li class="menu-item">
                   <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#modalPolitica" class="menu-link px-2">Política de Privacidade</a>
               </li>
           </ul>
           <!--end::Menu-->
       </div>
       <!--end::Container-->
   </div>




<!-- Botão para abrir a modal -->

   <!--inicio::Modal - Relatorio Cliente  data-bs-backdrop="static" -->
   <div class="modal fade" id="modal_relatorio_cliente" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
       <!--begin::Modal dialog-->
       <div class="modal-dialog modal-dialog-centered mw-650px" id='novo_form_relatorio_cliente_dinamico'>
           <!--begin::Modal content-->
           <!--begin::Page loading(append to body)-->
           <div class="alert alert-primary d-flex align-items-center p-5 mb-10" id="aguarde_novo_form_relatorio_cliente">
               <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
               <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                   <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                       <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
                       <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
                   </svg>
               </span>
               <!--end::Svg Icon-->
               <div class="d-flex flex-column">
                   <h4 class="mb-1 text-primary">Por favor, aguarde.</h4>
                   <span class="spinner-border text-primary" role="status"></span>
                   <span class="text-gray-800 fs-6 fw-semibold mt-5">Carregando...</span>
               </div>
           </div>

           <!--end::Page loading-->
       </div>
       <!--end::Modal dialog-->
   </div>

  

   <!--end::Modal - Relatorio CLiente-->


    <!--inicio::Modal - Nova Tarefa-->
    <div class="modal fade" id="kt_modal_new_target" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
       <!--begin::Modal dialog-->
       <div class="modal-dialog modal-dialog-centered mw-650px" id='nova_tarefa_conteudo_modal_dinamico'>
           <!--begin::Modal content-->
           <!--begin::Page loading(append to body)-->
           <div class="alert alert-primary d-flex align-items-center p-5 mb-10" id="nova_tarefa_aguardar_modal_carregar">
               <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
               <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                   <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                       <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
                       <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
                   </svg>
               </span>
               <!--end::Svg Icon-->
               <div class="d-flex flex-column">
                   <h4 class="mb-1 text-primary">Por favor, aguarde.</h4>
                   <span class="spinner-border text-primary" role="status"></span>
                   <span class="text-gray-800 fs-6 fw-semibold mt-5">Carregando...</span>
               </div>
           </div>

           <!--end::Page loading-->
       </div>
       <!--end::Modal dialog-->
   </div>
   <!--end::Modal - Tarefas-->





     <!--inicio::Modal - modal_nova_colecao_notificacao-->
     <div class="modal fade" id="modal_nova_colecao_notificacao" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
       <!--begin::Modal dialog-->
       <div class="modal-dialog modal-dialog-centered mw-950px" id='nova_colecao_notificacao_conteudo_modal_dinamico'>
           <!--begin::Modal content-->
           <!--begin::Page loading(append to body)-->
           <div class="alert alert-primary d-flex align-items-center p-5 mb-10" id="nova_colecao_notificacao_aguardar_modal_carregar">
               <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
               <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                   <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                       <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
                       <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
                   </svg>
               </span>
               <!--end::Svg Icon-->
               <div class="d-flex flex-column">
                   <h4 class="mb-1 text-primary">Por favor, aguarde.</h4>
                   <span class="spinner-border text-primary" role="status"></span>
                   <span class="text-gray-800 fs-6 fw-semibold mt-5">Carregando...</span>
               </div>
           </div>

           <!--end::Page loading-->
       </div>
       <!--end::Modal dialog-->
   </div>
   <!--end::Modal - modal_nova_colecao_notificacao-->





       <!--inicio::Modal - config step-->
       <div class="modal fade" id="modal_config_step" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
       <!--begin::Modal dialog-->
       <div class="modal-dialog modal-dialog-centered mw-950px" id='config_step_conteudo_modal_dinamico'>
           <!--begin::Modal content-->
           <!--begin::Page loading(append to body)-->
           <div class="alert alert-primary d-flex align-items-center p-5 mb-10" id="config_step_aguardar_modal_carregar">
               <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
               <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                   <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                       <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
                       <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
                   </svg>
               </span>
               <!--end::Svg Icon-->
               <div class="d-flex flex-column">
                   <h4 class="mb-1 text-primary">Por favor, aguarde.</h4>
                   <span class="spinner-border text-primary" role="status"></span>
                   <span class="text-gray-800 fs-6 fw-semibold mt-5">Carregando...</span>
               </div>
           </div>

           <!--end::Page loading-->
       </div>
       <!--end::Modal dialog-->
   </div>
   <!--end::Modal - modal_nova_colecao_notificacao-->

   
  





   <div class="modal fade" tabindex="-1" id="modalPolitica">
       <div class="modal-dialog modal-lg modal-dialog-scrollable">
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title">Política de Privacidade do Sistema STEP</h5>

                   <!--begin::Close-->
                   <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                       <span class="svg-icon svg-icon-2x"></span>
                   </div>
                   <!--end::Close-->
               </div>

               <div class="modal-body" style="min-height: 500px">
                   <p>A Empresa <span class="fs-5 text-success fw-bold"><a href="https://grupoep.com.br" target="_b
            ">EP Engenharia do Processo LTDA</a></span>, proprietária do Sistema STEP, está comprometida em proteger a privacidade e a segurança dos dados pessoais de seus usuários, sejam eles funcionários ou clientes. Esta Política de Privacidade descreve como coletamos, usamos, armazenamos e compartilhamos informações pessoais, em conformidade com a Lei Geral de Proteção de Dados (LGPD) e outras legislações aplicáveis.</p>
                   <p>Ao utilizar o Sistema STEP, você concorda com a coleta e o uso de suas informações pessoais conforme descrito nesta Política de Privacidade. Caso não concorde com esta política, por favor, não utilize o sistema.</p>
                   <h2 class="mb-3 mt-4">1. Coleta de Informações Pessoais</h2>
                   <p>O Sistema STEP coleta informações pessoais necessárias para fornecer e melhorar nossos serviços, bem como para cumprir obrigações legais e contratuais. As informações pessoais que coletamos podem incluir, mas não se limitam a:</p>
                   <ul>
                       <li>Nome completo;</li>
                       <li>CPF ou CNPJ;</li>
                       <li>Data de nascimento;</li>
                       <li>Endereço de e-mail;</li>
                       <li>Telefone de contato;</li>
                       <li>Endereço residencial ou comercial;</li>
                       <li>Informações de acesso e uso do sistema, incluindo registros de leituras, gráficos e relatórios.</li>
                   </ul>
                   <h2 class="mb-3 mt-4">2. Uso das Informações Pessoais</h2>
                   <p>Utilizamos suas informações pessoais para:</p>
                   <ul>
                       <li>Fornecer e melhorar nossos serviços;</li>
                       <li>Personalizar sua experiência no Sistema STEP;</li>
                       <li>Comunicar-se com você sobre atualizações, suporte ou informações relevantes;</li>
                       <li>Cumprir obrigações legais e regulatórias;</li>
                       <li>Proteger a segurança e a integridade do Sistema STEP e de nossos usuários.</li>
                   </ul>
                   <h2 class="mb-3 mt-4">3. Compartilhamento de Informações Pessoais</h2>
                   <p>Não compartilhamos suas informações pessoais com terceiros, exceto nos casos em que:</p>
                   <ul>
                       <li>Seja necessário para cumprir uma obrigação legal ou regulatória;</li>
                       <li>Seja necessário para proteger nossos direitos, propriedade ou segurança, bem como a de nossos usuários;</li>
                       <li>Haja consentimento expresso do usuário para o compartilhamento de informações específicas.</li>
                   </ul>
                   <h2 class="mb-3 mt-4">4. Armazenamento e Segurança das Informações Pessoais</h2>
                   <p>Armazenamos suas informações pessoais em servidores seguros e protegidos por medidas de segurança apropriadas, incluindo criptografia e controle de acesso.
                       Retemos suas informações pelo tempo necessário para cumprir as finalidades descritas nesta Política de Privacidade, a menos que a legislação exija ou permita
                       um período de retenção maior.</p>

                   <h2 class="mb-3 mt-4">5. Seus Direitos</h2>
                   <p>De acordo com a LGPD, você tem o direito de:</p>
                   <ul>
                       <li>Acessar suas informações pessoais;</li>
                       <li>Solicitar a correção de informações pessoais incompletas, inexatas ou desatualizadas;</li>
                       <li>Solicitar a eliminação de informações pessoais desnecessárias ou que não estejam sendo tratadas em conformidade com a LGPD;</li>
                       <li>Solicitar a portabilidade de suas informações pessoais a outro fornecedor de serviço ou produto;</li>
                       <li>Revogar o consentimento para o tratamento de suas informações pessoais, quando aplicável.</li>
                   </ul>
                   <p>Para exercer qualquer um desses direitos, entre em contato conosco através do e-mail <span class="fs-6 text-primary fw-bold">privacidade@grupoep.com.br</span></p>
                   <h2 class="mb-3 mt-4">6. Alterações na Política de Privacidade</h2>
                   <p>Podemos atualizar nossa Política de Privacidade periodicamente. Recomendamos que você revise esta política regularmente para se manter informado sobre nossas práticas de privacidade e proteção de dados.</p>
               </div>



               <div class="modal-footer">
                   <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>

               </div>
           </div>
       </div>
   </div>


   <!--begin::Help drawer-->
   <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/views/suportes/janela-aprenda.php'; ?>
   <!--end::Help drawer-->


   <!--begin::Drawer Suporte-->
   <div id="drawer_Suporte" class="bg-dark" data-kt-drawer="true" data-kt-drawer-activate="true" data-kt-drawer-toggle="#kt_drawer_example_permanent_toggle" data-kt-drawer-close="#kt_drawer_example_permanent_close" data-kt-drawer-overlay="true" data-kt-drawer-permanent="true" data-kt-drawer-width="{default:'900px', 'md': '1000px'}" data-kt-drawer-direction="start">
       <!--begin::Card-->
       <div class="card rounded-0 w-100">
           <!--begin::Card header-->
           <div class="card-header pe-5">
               <!--begin::Title-->
               <div class="card-title">
                   Ticket de Suporte

               </div>
               <!--end::Title-->

               <!--begin::Card toolbar-->
               <div class="card-toolbar">

                   <div class="border border-gray-300 border-dashed rounded min-w-60px py-2 px-20 me-6 mb-1">
                       <!--begin::Number-->
                       <div class="d-flex align-items-center" id='info_id_suporte'> </div>
                   </div>
                   <!--begin::Close-->
                   <div class="btn btn-sm btn-icon btn-active-light-primary" id="kt_drawer_example_permanent_close">
                       <span class="svg-icon fs-1">
                           <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http: //www.w3.org/2000/svg">
                               <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                               <rect x="7" y="15.3137" width="12" height="2" rx="1" transform="rotate(-45 7 15.3137)" fill="currentColor" />
                               <rect x="8.41422" y="7" width="12" height="2" rx="1" transform="rotate(45 8.41422 7)" fill="currentColor" />
                           </svg>
                       </span>
                   </div>
                   <!--end::Close-->
               </div>
               <!--end::Card toolbar-->
           </div>
           <!--end::Card header-->

           <!--begin::Card body-->
           <div class="card-body hover-scroll-overlay-y" id='div_conteudo_ticket'>
               Por favor, aguarde...
           </div>
           <!--end::Card body-->
       </div>
       <!--end::Card-->
   </div>
   <!--end::Drawer Suporte-->




   <!--begin::Drawer Tarefas-->
   <div id="drawer_Tarefas" class="bg-dark" data-kt-drawer="true" data-kt-drawer-activate="true" data-kt-drawer-toggle="#kt_drawer_example_permanent_toggle" data-kt-drawer-close="#kt_drawer_example_permanent_close" data-kt-drawer-overlay="true" data-kt-drawer-permanent="true" data-kt-drawer-width="{default:'450px', 'md': '500px'}" data-kt-drawer-direction="start">
       <!--begin::Card-->
       <div class="card rounded-0 w-100">
           <!--begin::Card header-->
           <div class="card-header pe-5">
               <!--begin::Title-->
               <div class="card-title">
                   Minhas Tarefas

               </div>
               <!--end::Title-->

               <!--begin::Card toolbar-->
               <div class="card-toolbar">

                   <div class="border border-gray-300 border-dashed rounded min-w-60px py-2 px-20 me-6 mb-1">
                       <!--begin::Number-->
                       <div class="d-flex align-items-center"> Acompanhe as Tarefas Recebidas </div>
                   </div>
                   <!--begin::Close-->
                   <div class="btn btn-sm btn-icon btn-active-light-primary" id="kt_drawer_example_permanent_close">
                       <span class="svg-icon fs-1">
                           <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http: //www.w3.org/2000/svg">
                               <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                               <rect x="7" y="15.3137" width="12" height="2" rx="1" transform="rotate(-45 7 15.3137)" fill="currentColor" />
                               <rect x="8.41422" y="7" width="12" height="2" rx="1" transform="rotate(45 8.41422 7)" fill="currentColor" />
                           </svg>
                       </span>
                   </div>
                   <!--end::Close-->
               </div>
               <!--end::Card toolbar-->
           </div>
           <!--end::Card header-->

           <!--begin::Card body-->
           <div class="card-body hover-scroll-overlay-y" id='div_conteudo_Tarefas'>
               Por favor, aguarde...
           </div>
           <!--end::Card body-->
       </div>
       <!--end::Card-->
   </div>
   <!--end::Drawer Tarefas-->



   <script>


let myModal_relatorio_cliente = document.getElementById('modal_relatorio_cliente');

       myModal_relatorio_cliente.addEventListener('shown.bs.modal', function(event) {

           event.preventDefault();

           var button = $(event.relatedTarget);

           var recipientId = button.data('projeto_atual');

           var modal = $(this);
  

           $.ajax({
               type: 'POST',
               url: '../../views/relatorios/modal-relatorio.php',
               dataType: 'html',
               data: {
                   projeto_atual: recipientId
               },
               beforeSend: function() {
                   $("#aguarde_novo_form_relatorio_cliente").removeClass("d-none");
               },
               success: function(retorno) {

                   $("#aguarde_novo_form_relatorio_cliente").addClass("d-none");

                   $("#novo_form_relatorio_cliente_dinamico").html(retorno);

               },
               error: function() {
                   alert("Falha ao coletar dados !!!");
               }
           });

       })

       myModal_relatorio_cliente.addEventListener('hidden.bs.modal', function(event) {

        createMetronicToast('Formulário para Geração de Relatório para Cliente', 'retorno.mensagem', 5000, 'success', 'bi bi-check2-square');
       })

   </script>

   <script>
       let modal_nova_colecao_notificacao = document.getElementById('modal_nova_colecao_notificacao');


       modal_nova_colecao_notificacao.addEventListener('shown.bs.modal', function(event) {

           event.preventDefault();

           var button = $(event.relatedTarget);

           var recipientId = button.data('projeto_atual');

           var modal = $(this);

           //modal.find('#minhaId').html(recipientId);


           $.ajax({
               type: 'POST',
               url: '/views/suportes/notificacoes/modal_nova_colecao_notificacao.php',
               dataType: 'html',
               data: {
                   projeto_atual: recipientId
               },
               beforeSend: function() {
                   $("#nova_colecao_notificacao_aguardar_modal_carregar").removeClass("d-none");
               },
               success: function(retorno) {

                   $("#nova_colecao_notificacao_aguardar_modal_carregar").addClass("d-none");

                   $("#nova_colecao_notificacao_conteudo_modal_dinamico").html(retorno);

                 

               },
               error: function() {
                   alert("Falha ao coletar dados !!!");
               }
           });


           //$("#conteudo_modal_dinamico" ).load( "../../views/projetos/modal-edita-projeto.php?id="+recipientId );



       })



       modal_nova_colecao_notificacao.addEventListener('hidden.bs.modal', function(event) {

           //location.reload();

           KTCookie.remove('id_obra_colecao');
             KTCookie.remove('nome_obra_colecao');

       })
   </script>





<script>


       let modal_config_step = document.getElementById('modal_config_step');


       modal_config_step.addEventListener('shown.bs.modal', function(event) {

           event.preventDefault();

           var button = $(event.relatedTarget);

           var recipientId = button.data('projeto_atual');

           var modal = $(this);

     




           $.ajax({
               type: 'POST',
               url: '/views/sistema/modal_config_step.php',
               dataType: 'html',
               data: {
                   projeto_atual: recipientId
               },
               beforeSend: function() {
                   $("#config_step_aguardar_modal_carregar").removeClass("d-none");
               },
               success: function(retorno) {

                   $("#config_step_aguardar_modal_carregar").addClass("d-none");

                   $("#config_step_conteudo_modal_dinamico").html(retorno);

                      
                 

               },
               error: function() {
                   alert("Falha ao coletar dados !!!");
               }
           });


           //$("#conteudo_modal_dinamico" ).load( "../../views/projetos/modal-edita-projeto.php?id="+recipientId );



       })



       modal_config_step.addEventListener('hidden.bs.modal', function(event) {

           //location.reload();

          

       })
   </script>





<script>
       let myModal_nova_tarefa = document.getElementById('kt_modal_new_target');


       myModal_nova_tarefa.addEventListener('shown.bs.modal', function(event) {

           event.preventDefault();

           var button = $(event.relatedTarget);

           var recipientId = button.data('projeto_atual');

           var modal = $(this);

           //modal.find('#minhaId').html(recipientId);


           // alert(recipientId);




           $.ajax({
               type: 'POST',
               url: '../../views/projetos/tarefas/modal-tarefas.php',
               dataType: 'html',
               data: {
                   projeto_atual: recipientId
               },
               beforeSend: function() {
                   $("#nova_tarefa_aguardar_modal_carregar").removeClass("d-none");
               },
               success: function(retorno) {

                   $("#nova_tarefa_aguardar_modal_carregar").addClass("d-none");


                   $('#projeto_tarefa').select2();



                   $("#nova_tarefa_conteudo_modal_dinamico").html(retorno);

               },
               error: function() {
                   alert("Falha ao coletar dados !!!");
               }
           });


           //$("#conteudo_modal_dinamico" ).load( "../../views/projetos/modal-edita-projeto.php?id="+recipientId );



       })


       myModal_nova_tarefa.addEventListener('shown.bs.modal', function (evente){

        //alert('modal nova tarefa, foi aberta');

      

       })


       myModal_nova_tarefa.addEventListener('hidden.bs.modal', function(event) {

           location.reload();

       })
   </script>


   <script src="https://unpkg.com/blip-chat-widget" type="text/javascript"></script>
   <script>
       function openBlipChat() {
           new BlipChat()
               .withAppKey('cm90ZWFkb3JlcDpmZjI1ZTk5Zi03ODMyLTQ2MTMtOGQzMC0wMjc0ODBiYzViMWE=')
               .withButton({
                   "color": "#3B842A",
                   "icon": "https://blipmediastore.blob.core.windows.net/public-medias/Media_90feb249-413c-4869-b0e2-5664adf47eaa"
               })
               .withCustomCommonUrl('https://grupoep1.chat.blip.ai/')
               .withTarget('chat-blip') // Adicione esta linha para especificar a div 'teste' como alvo
               .build();

       }


       document.getElementById('blipchat-button').addEventListener('click', function(event) {
           event.preventDefault();
           openBlipChat();
           // FadeOut e FadeIn na div com o ID 'chat-blip'
           $("#chat-blip").fadeOut(400, function() {
               $(this).toggleClass('d-none').fadeIn(400);
           });

           // FadeOut e FadeIn na div com o ID 'botoes-janela-aprenda'
           $("#botoes-janela-aprenda").fadeOut(400, function() {
               $(this).toggleClass('d-none').fadeIn(400);
           });

           function toggleChat() {
               BlipChat.toggle();
           }
           toggleChat();
       });



   </script>


   <script src="assets/plugins/custom/fslightbox/fslightbox.bundle.js"></script>



   