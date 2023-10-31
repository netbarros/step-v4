
<?php
 // buffer de saída de dados do php]
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';


if (isset($_GET["id_plcode"]) && is_numeric($_GET["id_plcode"])) {
    $id_plcode = (int)$_GET["id_plcode"];
} elseif (isset($_COOKIE["plcode_atual"]) && is_numeric($_COOKIE["plcode_atual"])) {
    $id_plcode = (int)$_COOKIE["plcode_atual"];
}

if ($id_plcode === null) {
    // Manipule o erro aqui, como redirecionar para outra página ou mostrar uma mensagem de erro
    echo "Erro ao receber PLCode Atual!";
    exit();
}



?>
<!--begin::Table container-->
<div class="table-responsive"  id="div_modulo_indicadores">
<table id="tabela_indicadores" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
<thead>
                                                                            <tr class="fw-bold text-muted">
                                                                          
                                                                                <th class="min-w-200px">Nome Indicador
                                                                                </th>
                                                                                <th class="min-w-150px">Parâmetros</th>
                                                                                <th class="min-w-150px">Origem</th>
                                                                                <th class="min-w-100px text-end">Ações
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
    <tbody>

	<?php

$sql = $conexao->query("SELECT * FROM parametros_ponto pr
INNER JOIN
pontos_estacao p ON p.id_ponto = pr.id_ponto
INNER JOIN
unidade_medida un ON un.id_unidade_medida = pr.unidade_medida
 WHERE pr.id_ponto='$id_plcode'");


$conta = $sql->rowCount();


if($conta>0){




	$row = $sql->fetchALL(PDO::FETCH_ASSOC);

	foreach ($row as $r) {

	$origem = $r['origem_leitura_parametro'];

    $gera_grafico = $r['gera_grafico'];

	$controle_concentracao = $r['controle_concentracao'];

    $status_indicador= $r['status_parametro'];
    $id_sensor_iot= $r['id_sensor_iot'] ?? '';
	$status_Iot='';	

	switch ($origem) {
		case 1:
			$origem_val = 'Entrada';
			break;
		case 2:
			$origem_val = 'Saída';
			break;
			case 3:
				$origem_val = 'Entrada e Saída';
				break;
		default:
		$origem_val = 'Não Informado';
			break;
	}


	switch ($controle_concentracao) {
		case 1:
			$controle_val = 'Mínima';
            $valor = $r['concen_min'];
			break;
		case 2:
			$controle_val = 'Máxima';
            $valor = $r['concen_max'];
			break;
			case 3:
				$controle_val = 'Mínima e Máxima';
                $valor = $r['concen_min'] .' <> '.$r['concen_max'];
				break;
		default:
		$controle_val = 'Não Informado';
			break;
	}





	switch ($gera_grafico) {
		case 1:
			$grafico = 'Disponível para o Cockpit';
			break;
		case 0:
			$grafico = 'Indisponível para o Cockpit';
			break;
			
		default:
		$origem_val = 'Não Informado';
			break;
	}


    switch ($status_indicador) {
        case 1:
           $status='  <span class="symbol-badge badge badge-circle bg-success start-50" data-bs-toggle="tooltip" title="Indicador Ativo">On</span>';
            break;
            case 2:
                $status='  <span class="symbol-badge badge badge-circle bg-danger start-50" data-bs-toggle="tooltip" title="Indicador em Alerta!">!</span>';
                 break;
                 case 3:
                    $status='  <span class="symbol-badge badge badge-circle bg-white start-50" data-bs-toggle="tooltip" title="Indicador Inativo">Off</span>';
                     break;
        default:
            # code...
            $status='  <span class="symbol-badge badge badge-circle bg-info start-50"></span>';
            break;
    }

if($id_sensor_iot!='' && $id_sensor_iot!=null){

    $status_Iot='<span class="symbol-badge badge badge-circle bg-primary start-50" data-bs-toggle="tooltip" data-bs-placement="top"  title="IoT  utilizado neste indicador">IoT</span>';

}

  

?>
                                                                            <tr>
                                                                             
                                                                                <td>  <?=$status_Iot;?>
                                                                                    <div class="d-flex align-items-center">
                                                                                   
                                                                                        <div
                                                                                            class="d-flex justify-content-start flex-column">
                                                                                            <a href="javascript:;"
                                                                                                class="text-dark fw-bold text-hover-primary fs-6" data-bs-toggle="modal" data-bs-target="#modal_altera_registro" data-id="<?=$r['id_parametro'];?>" data-nome="<?=$r['nome_parametro'];?>"><?=$r['nome_parametro'];?></a>
                                                                                            <span
                                                                                                class="text-muted fw-semibold text-muted d-block fs-7"  data-bs-toggle="tooltip" data-bs-placement="top" title="Unidade de Medida do Indicador"><?=$r['nome_unidade_medida'];?></span>
                                                                                        </div>
                                                                                      <?=$status;?>
                                                                                        
                                                                                    </div>
                                                                                   
                                                                                </td>
                                                                                <td>
                                                                                    <a href="javascript:;"
                                                                                        class="text-dark fw-bold text-hover-primary d-block fs-6" data-bs-toggle="tooltip" data-bs-placement="top" title="Controle de Parâmetros">
                                                                                        <?=$valor;?>
                                                                                    </a>
                                                                                    <span
                                                                                        class="text-muted fw-semibold text-muted d-block fs-7"><?=$controle_val;?></span>
                                                                                </td>
                                                                                <td class="text-end">
                                                                                    <div
                                                                                        class="d-flex align-items-center">

                                                                                        <div
                                                                                            class="d-flex justify-content-start flex-column">
                                                                                            <a href="javascript:;"
                                                                                                class="text-dark fw-bold text-hover-primary fs-6"><?=$origem_val;?></a>
                                                                                            <span
                                                                                                class="text-muted fw-semibold text-muted d-block fs-7">  <?=$grafico;?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div
                                                                                        class="d-flex justify-content-end flex-shrink-0">
                                                                                        <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kt_modal_new_target" data-id="<?=$r['id_parametro'];?>" data-nome="<?=$r['nome_parametro'];?>"
                                                                                            class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                                                            <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                                                                            <span
                                                                                                class="svg-icon svg-icon-3"  data-bs-toggle="tooltip" data-bs-placement="bottom" title="Nova Tarefa para este Indicador">
                                                                                                <svg width="24"
                                                                                                    height="24"
                                                                                                    viewBox="0 0 24 24"
                                                                                                    fill="none"
                                                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                                                    <path
                                                                                                        d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z"
                                                                                                        fill="currentColor" />
                                                                                                    <path opacity="0.3"
                                                                                                        d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z"
                                                                                                        fill="currentColor" />
                                                                                                </svg>
                                                                                            </span>
                                                                                            <!--end::Svg Icon-->
                                                                                        </a>
                                                                                        <a href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#modal_altera_registro" data-id="<?=$r['id_parametro'];?>" data-nome="<?=$r['nome_parametro'];?>"
                                                                                            class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                                                            <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                                                                                            <span
                                                                                                class="svg-icon svg-icon-3"  data-bs-toggle="tooltip" data-bs-placement="bottom" title="Alterar Indicador">
                                                                                                <svg width="24"
                                                                                                    height="24"
                                                                                                    viewBox="0 0 24 24"
                                                                                                    fill="none"
                                                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                                                    <path opacity="0.3"
                                                                                                        d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                                                                        fill="currentColor" />
                                                                                                    <path
                                                                                                        d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                                                                        fill="currentColor" />
                                                                                                </svg>
                                                                                            </span>
                                                                                            <!--end::Svg Icon-->
                                                                                        </a>
                                                                                        <a href="javascript:;" 
                                                                                            class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm exclui_indicador" data-id="<?=$r['id_parametro'];?>" data-nome="<?=$r['nome_parametro'];?>">
                                                                                            <!--begin::Svg Icon | path: icons/duotune/general/gen027.svg-->
                                                                                            <span
                                                                                                class="svg-icon svg-icon-3"  data-bs-toggle="tooltip" data-bs-placement="bottom" title="Exluir Indicador">
                                                                                                <svg width="24"
                                                                                                    height="24"
                                                                                                    viewBox="0 0 24 24"
                                                                                                    fill="none"
                                                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                                                    <path
                                                                                                        d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z"
                                                                                                        fill="currentColor" />
                                                                                                    <path opacity="0.5"
                                                                                                        d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z"
                                                                                                        fill="currentColor" />
                                                                                                    <path opacity="0.5"
                                                                                                        d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z"
                                                                                                        fill="currentColor" />
                                                                                                </svg>
                                                                                            </span>
                                                                                            <!--end::Svg Icon-->
                                                                                        </a>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
<?php
	}
}

?>
                                                                        </tbody>
</table>
</div>
                                                               



				
        <!-- begin::Global Config(global config for global JS sciprts) -->
        <script>
            var KTAppOptions = {"colors":{"state":{"brand":"#366cf3","light":"#ffffff","dark":"#282a3c","primary":"#5867dd","success":"#34bfa3","info":"#36a3f7","warning":"#ffb822","danger":"#fd3995"},"base":{"label":["#c5cbe3","#a1a8c3","#3d4465","#3e4466"],"shape":["#f0f3ff","#d9dffa","#afb4d4","#646c9a"]}}};

            var caminhoAtual = window.location.pathname;
        </script>
        <!-- end::Global Config -->

<script src="../../js/indicadores/tabela-indicadores.js" type="text/javascript"></script>	    	   

<!--begin::Page Scripts(used by this page) -->
	
	<script src="../../js/indicadores/crud-indicador.js" type="text/javascript"></script>						
                     

<!-- Modal Default Alteração de Registro  -->                        

<div class="modal fade" id="modal_altera_registro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" >
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                                    <!--begin::Underline-->
<span class="d-flex position-relative">
    <!--begin::Label-->
    <span class="d-inline-block mb-2 fs-2x fw-bold text-dark" id="titulo_modal_cadastro">
        Sample Text
    </span>
    <!--end::Label-->

    <!--begin::Line-->
    <span class="d-inline-block position-absolute h-8px bottom-0 end-0 start-0 bg-primary translate rounded"></span>
    <!--end::Line-->
</span>
<!--end::Underline-->
                <button type="button"  class="btn btn-outline-brand btn-icon btn-circle" data-bs-dismiss="modal" aria-label="Close">
                <i class="fa fa-undo"></i></button>
            </div>
            <div class="modal-body" id="retorno-dados">
                <p>
               Conexão de Internet, Lenta! Por favor aguarde o Carregando do Módulo  de Indicadores/Prâmetros do PLCode
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
                <h5 class="modal-title kt-font-brand" id="">
				
					
				 	<span  class="kt-font-success">Cadastro de Indicador </span> <span id="titulo_modal_cadastro-novo" class="kt-font-transform-u kt-label-font-color-2"> </span></h5>
                <button type="button"  class="btn btn-outline-brand btn-icon btn-circle" data-bs-dismiss="modal" aria-label="Close">
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