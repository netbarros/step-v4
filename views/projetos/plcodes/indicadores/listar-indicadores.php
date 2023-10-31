<div class="row">
    <!--Begin::Seção Principal-->
    <div class="kt-portlet">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand flaticon2-heart-rate-monitor"></i>
                </span>
                <h3 class="kt-portlet__head-title">
                    Listagem Geral <small>de Parâmetros / Indicadores</small>
                </h3>
			</div>
			<div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-wrapper">
	<div class="kt-portlet__head-actions">
		
		<a href="javascript:;" class="btn btn-brand btn-elevate btn-icon-sm" data-toggle="modal" data-target="#modal_novo_registro" id="bt_novo_parametro" data-html="true" data-offset="20px 20px" data-toggle="kt-tooltip" data-placement="top" title="" data-skin="brand" data-original-title="Clique para incluir um Novo Parâmetro/ Indicador, para este PLCode." id="tooltip_novo_parametro">
			<i class="la la-plus"></i>
			Parâmetro
		</a>
	</div>	
</div>		</div>
            
        </div>

        <div class="kt-portlet__body">

            <!--begin: Datatable -->
            <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_parametros_full">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                         <th>
                            Obra
                        </th>
                        <th>
                            Estação
                        </th>
                         <th>
                            PLCode
                        </th>
                        <th>
                            Parâmetro
                        </th>
                        <th>
                            UN
                        </th>

                        <th>
                          Limite
						</th>
						
                        <th>
                            Checkin
						</th>
						<th>
                            Gráfico?
						</th>
						<th>
                            Status
                        </th>

                        <th>
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>

            </table>
            <!--end: Datatable -->





            <!--End::Seção Principal-->
        </div>
    </div>
</div>

</div>
</div>

<script src="./painel-operacao/js/datatables.bundle.js" type="text/javascript"></script>

<!--end::Page Vendors -->


<!--begin::Page Scripts(used by this page) -->
<script src="./plcodes/parametros/js-parametros/listar-indicadores.js" type="text/javascript"></script>



<!-- Modal Default Alteração de Registro  -->                        

<div class="modal fade" id="modal_altera_registro"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" >
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title kt-font-brand" id="exampleModalLongTitle">
				
				
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--success">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon id="Bound" points="0 0 24 0 24 24 0 24"></polygon>
                        <rect id="bound" x="0" y="0" width="24" height="24"/>
        <path d="M4.5,21 L21.5,21 C22.3284271,21 23,20.3284271 23,19.5 L23,8.5 C23,7.67157288 22.3284271,7 21.5,7 L11,7 L8.43933983,4.43933983 C8.15803526,4.15803526 7.77650439,4 7.37867966,4 L4.5,4 C3.67157288,4 3,4.67157288 3,5.5 L3,19.5 C3,20.3284271 3.67157288,21 4.5,21 Z" id="Combined-Shape" fill="#000000" opacity="0.3"/>
        <path d="M2.5,19 L19.5,19 C20.3284271,19 21,18.3284271 21,17.5 L21,6.5 C21,5.67157288 20.3284271,5 19.5,5 L9,5 L6.43933983,2.43933983 C6.15803526,2.15803526 5.77650439,2 5.37867966,2 L2.5,2 C1.67157288,2 1,2.67157288 1,3.5 L1,17.5 C1,18.3284271 1.67157288,19 2.5,19 Z" id="Combined-Shape-Copy" fill="#000000"/>
                    </g>
                </svg>				
			
				
				 	<span  class="kt-font-success">Alterar Parâmetro:</span> <span id="titulo_modal_cadastro" class="kt-font-transform-u kt-label-font-color-2"> </span></h5>
                <button type="button"  class="btn btn-outline-brand btn-icon btn-circle" data-dismiss="modal" aria-label="Close">
                <i class="fa fa-undo"></i></button>
            </div>
            <div class="modal-body" id="retorno-dados">
                <p>
             Por favor, aguarde o Carregando do Módulo  de Indicadores/Parâmetros do PLCode
                <span class="kt-spinner kt-spinner--v2 kt-spinner--lg kt-spinner--danger"></span>
                </p>
              
            </div>
          
        </div>
    </div>
    </div>
			    
<!-- Modal Default Alteração de Registro  --> 



<!-- Modal Default Alteração de Registro  -->                        

<div class="modal fade" id="modal_novo_registro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" >
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title kt-font-brand" id="exampleModalLongTitle">
				
				
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--success">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon id="Bound" points="0 0 24 0 24 24 0 24"></polygon>
                        <rect id="bound" x="0" y="0" width="24" height="24"/>
        <path d="M4.5,21 L21.5,21 C22.3284271,21 23,20.3284271 23,19.5 L23,8.5 C23,7.67157288 22.3284271,7 21.5,7 L11,7 L8.43933983,4.43933983 C8.15803526,4.15803526 7.77650439,4 7.37867966,4 L4.5,4 C3.67157288,4 3,4.67157288 3,5.5 L3,19.5 C3,20.3284271 3.67157288,21 4.5,21 Z" id="Combined-Shape" fill="#000000" opacity="0.3"/>
        <path d="M2.5,19 L19.5,19 C20.3284271,19 21,18.3284271 21,17.5 L21,6.5 C21,5.67157288 20.3284271,5 19.5,5 L9,5 L6.43933983,2.43933983 C6.15803526,2.15803526 5.77650439,2 5.37867966,2 L2.5,2 C1.67157288,2 1,2.67157288 1,3.5 L1,17.5 C1,18.3284271 1.67157288,19 2.5,19 Z" id="Combined-Shape-Copy" fill="#000000"/>
                    </g>
                </svg>				
			
				
				 	<span  class="kt-font-success">Novo Parâmetro:</span> <span id="titulo_modal_cadastro-novo" class="kt-font-transform-u kt-label-font-color-2"> </span></h5>
                <button type="button"  class="btn btn-outline-brand btn-icon btn-circle" data-dismiss="modal" aria-label="Close">
                <i class="fa fa-undo"></i></button>
            </div>
            <div class="modal-body" id="retorno-dados-novo">
                <p>
               Conexão de Internet, Lenta! Por favor aguarde o Carregando do Módulo de Indicadores/Prâmetros do PLCode
                <span class="kt-spinner kt-spinner--v2 kt-spinner--lg kt-spinner--danger"></span>
                </p>
              
            </div>
          
        </div>
    </div>
    </div>
			    
<!-- Modal Default Alteração de Registro  --> 


				
        <!-- begin::Global Config(global config for global JS sciprts) -->
        <script>
            var KTAppOptions = {"colors":{"state":{"brand":"#366cf3","light":"#ffffff","dark":"#282a3c","primary":"#5867dd","success":"#34bfa3","info":"#36a3f7","warning":"#ffb822","danger":"#fd3995"},"base":{"label":["#c5cbe3","#a1a8c3","#3d4465","#3e4466"],"shape":["#f0f3ff","#d9dffa","#afb4d4","#646c9a"]}}};
        </script>
        <!-- end::Global Config -->

    	    	   
        
                     
