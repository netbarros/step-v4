<?php

// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';
$_SESSION['modulo'] = 'Novo Indicador';
?>


<form class="form" id="form_indicador" name="form_indicador">



	<div class="card-body pt-9 pb-0">
		


		<div class="mb-10 fv-row">

			<div class="col-lg-12 form-group">
				<label class="required form-label">Nome do Indicador:</label>
				<input type="text" class="form-control " placeholder="Nome ou Identificação do Parâmetro" name="nome_parametro" maxlength="150" value="">
				<span class="fs-7 text-muted"></span>
			</div>
		</div>

		<div class="row">
		<div class="col-md-6">
				<label class="required form-label">
					Controle de Leitura do Parâmetro:
				</label>
				<div class="col-lg-12 ">
					<select class="form-select mb-2 " data-control="select2" title="Selecione a Origem ..." name="origem_leitura_parametro" data-dropdown-parent="#modal_novo_registro" id="origem_leitura_parametro_add">
				
					<option value="1">
							Entrada
						</option>
						<option value="2">
							Saída
						</option>


					</select>
				</div>

			</div>

			<div class="col-md-6" id="div_controla_concentracao_add">
				<label class="required form-label">
					Controle de Concentrações Permitidas:
				</label>
				<div class="col-lg-12 ">
					<select class="form-select mb-2" data-control="select2" title="Selecione o Controle de Concentração" id="controle_concentracao_add" data-dropdown-parent="#modal_novo_registro" name="controle_concentracao">
					
						<option value="1">
							Concentração Mínima
						</option>
						<option value="2">
							Concentração Máxima
						</option>
						<option value="3">

							Concentração Mínima e Máxima
						</option>
					</select>
				</div>

			</div>
		</div>

		
		<div class="row">
  <div class="col-md-4">
    <div id="div_controle_concentracao_1_add" class="d-flex flex-column d-none">
      <label class="required form-label">Concentração Mínima:</label>
      <div class="col-lg-8">
        <input type="text" class="form-control" id="concen_min_par" name="concen_min" placeholder="Mínima" value="">
      </div>
      <span class="fs-7 text-muted">
        Concentração Mínima Permitda.
      </span>
    </div>
  </div>
  <div class="col-md-4">
    <div id="div_controle_concentracao_2_add" class="d-flex flex-column d-none">
      <label class="required form-label">Concentração Máxima:</label>
      <div class="col-lg-8">
        <input type="text" class="form-control" id="concen_max_par" name="concen_max" placeholder="Máxima" value="">
      </div>
      <span class="fs-7 text-muted">
        Concentração Máxima Permitda:.
      </span>
    </div>
  </div>

  <div class="col-lg-4 " id="div_unidade_medida_add">
				<label class="required form-label">Unidade de Medida</label>

				<div class="input-group d-flex align-items-right">
					<select name="unidade_medida" id="unidade_medida_add" class="form-select mb-2" data-control="select2" data-live-search="false" data-dropdown-parent="#modal_novo_registro" data-size="4">
						<option value="">Selecione</option>

						<?php

						// executa consulta à tabela
						$stmt = $conexao->prepare("SELECT * from unidade_medida ORDER BY nome_unidade_medida DESC");
						$stmt->execute();

						while ($laco = $stmt->fetch(PDO::FETCH_OBJ)) {


							echo "<option value=" . $laco->id_unidade_medida . " >" . $laco->nome_unidade_medida . "</option>";
						}


						?>

					</select>
					<div class="input-group-append d-flex align-items-right">
						<button type="reset" class="input-group-text btn btn-light-primary" id="exibe_unidade_medida_add">
							<i class="bi bi-bookmark-plus fs-1x"></i>
						</button>

					</div>

				</div>
				<span class="fs-7 text-muted">
					Unidade de Medida da Concentração do Parâmetro

				</span>

			</div>


			<div class="col-lg-12 form-group d-none  alert alert-warning  align-items-center p-5" id="div_nova_unidade_medida_add">
				<label class="form-label">Cadastre a Nova Unidade de Medida:</label>
				<div class="input-group">

					<input type="text" class="form-control" name="nova_unidade_medida" id="nova_unidade_medida_add" maxlength="10" placeholder="Unidade de Medida">
					<div class="input-group-prepend">
						<button type="reset" class="btn btn-warning" id="inclui_unidade_medida_add">
							Incluir
						</button>

					</div>
					

				</div>
			</div>


			<div class="col-md-4">
    <div id="div_controle_concentracao_2_add" class="d-flex flex-column">
      <label class="required form-label">Id do Sensor IoT</label>
      <div class="col-lg-8">
        <input type="text" class="form-control" id="id_sensor_iot" name="id_sensor_iot" placeholder="id_sensor_iot" value="">
      </div>
      <span class="fs-7 text-muted">
        ID do Equipamento IoT.
      </span>
    </div>
  </div>
</div>




			


		

<div class="row">

		<div class="col-lg-6">
				<label class="required form-label">
					Status do Parâmetro de Leitura:
				</label>
				<div class="form-check form-check-custom form-check-solid">
				<label class="form-check-label" for="">
						<input type="radio" value="3" class="form-check-input" name="status_parametro">
						Inativo
						<span></span>
					</label>
					<label class="form-check-label" for="">
						<?php // Status 1 = Ativo || 2 = em alerta || 3 Inativo - 
						?>
						<input type="radio" value="1" class="form-check-input" name="status_parametro" checked>
						Ativo
						<span></span>
					</label>
				</div>
				
			</div>


			

			<div class="col-lg-6">
				<label class="required form-label"  > 
					Habilitar Indicador no Cockpit
				</label>
				<div class="form-check form-check-custom form-check-solid">
				<label class="form-check-label" for="">
						<input type="radio" value="0" class="form-check-input" name="gera_grafico" checked>
						Não
						<span></span>
					</label>
					<label class="form-check-label" for="">
						<?php 
						?>
						<input type="radio" value="1" class="form-check-input" name="gera_grafico">
						Sim
						<span></span>
					</label>
				</div>
				<span class="form-text text-muted">
                                                                                    Habilitar Cockpit para o Indicador
                                                                                    <span>
                                                                                        <i class="la la-question-circle"
																						data-bs-toggle="tooltip"   title="O Indicador será listado para criação de Gráficos de Acompanhamento em seu Cockpit do Dashboard."></i>
                                                                                    </span>
                                                                                </span>
				
			</div>


			</div>



			<div class="text-center py-10">
			<a href="javascript:;" data-bs-dismiss="modal" class="btn btn-light me-3">Cancelar</a>
                        <button type="submit" id="bt_novo_indicador" class="btn btn-primary">
                            <span class="indicator-label">Cadastrar</span>
                            <span class="indicator-progress">Por favor, aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>


		<input name="acao" type="hidden" value="novo_indicador">
		<input name="id_plcode"  type="hidden" value="" id='id_plcode_cadastro_indicador'>





	</div>
</form>
<!--end::Form-->

<script src="/js/indicadores/inclui-indicador.js" type="text/javascript"></script>
<!--end::Form-->




<script>
	$("#exibe_unidade_medida_add").click(function(e) {
		e.preventDefault();

		$("#div_nova_unidade_medida_add").slideToggle();

		$("#div_nova_unidade_medida_add").removeClass("fadeOut d-none");

	});


	$("#inclui_unidade_medida_add").click(function(e) {
		e.preventDefault();
		var nova_medida = $("#nova_unidade_medida_add").val();

		if (nova_medida != '') {


			$.ajax({
				type: "POST",
				url: "../../../../crud/indicadores/action-unidade-medida.php",
				data: "valor_input_nova_unidade=" + nova_medida + "&acao=inclui-nova-unidade", //frm.serialize(),
				dataType: "json",
				cache: false,

				success: function(data) {
					console.log('Cadastro do Parâmetro, Enviado ao Servidor com Sucesso.');
					console.log(data);




					console.log(data.codigo)


					if (data.codigo == 1) {


						$('#unidade_medida_add').load('../../../../crud/plcodes/monta_selects.php?id=' + data.id_retorno + '&montar=unidade_medida_select');

						$("#div_nova_unidade_medida_add").slideToggle();


						$("#div_nova_unidade_medida_add").addClass("animated fadeOut");
						$("#div_nova_unidade_medida_add").addClass("d-none");

						$("#nova_unidade_medida_add").val("");
						$("#form_help_nova_unidade_add").html("");




					}
					if (data.codigo == 0) {

						Swal.fire({
                        icon: 'error',
                        html: data.mensagem,
                        timer: 2000, // tempo em milissegundos
                        showConfirmButton: false, // não exibir botão de confirmação
                      }).then(function(){
                        // código para fechar a janela
                        KTUtil.scrollTop();
                        window.close(); // fecha a janela atual

                      
                      });


						//$("#nova_unidade_medida_add").addClass("d-none");
						//$("#div_nova_unidade_medida_add").addClass("animated fadeOut");



					}


				},
				error: function(data) {


					console.log('Falha no Processamento dos Dados.');
					console.log(data);

					$("#form_help_nova_unidade_add").html("<i class='text-danger bi bi-bookmark-plus fs-1x'></i> Erro no Valor da Unidade de Medida");




				}

			});

		} else {

			swal.fire({
				title: "O Valor da Unidade de Medida",
				text: "Não Pode Estar em Branco!",
				type: "error",
				confirmButtonClass: "btn btn-secondary kt-btn kt-btn--wide"
			})



		}




	});


	$(document).ready(function() {




		var controle_concentracao_add = $("#controle_concentracao_add").val();



		if (controle_concentracao_add == 1) {

			$("#div_controle_concentracao_1_add").removeClass("d-none");
			$("#div_controle_concentracao_1_add").addClass("animated fadeIn");

			$("#div_controle_concentracao_2_add").addClass("d-none");



		}





		if (controle_concentracao_add == 2) {

			$("#div_controle_concentracao_2_add").removeClass("d-none");
			$("#div_controle_concentracao_2_add").addClass("animated fadeIn");

			$("#div_controle_concentracao_1_add").addClass("d-none");


		}

		if (controle_concentracao_add == 3) {

			$("#div_controle_concentracao_2_add").removeClass("d-none");
			$("#div_controle_concentracao_1_add").removeClass("d-none");

			$("#div_controle_concentracao_1_add").addClass("animated fadeIn");
			$("#div_controle_concentracao_2_add").addClass("animated fadeIn");




		}



	});
</script>



<script>
	$("#div_controle_concentracao_2_add").removeClass("d-none");


	$("#origem_leitura_parametro_add").change(function() {

		var origem = $("#origem_leitura_parametro_add").val();



		if (origem == 4) {
			$("#div_controle_concentracao_1_add").addClass("d-none");
			$("#div_controle_concentracao_2_add").addClass("d-none");
			$("#div_unidade_medida_add").addClass("d-none");
			$("#div_controla_concentracao_add").addClass("d-none");

		} else {

			$("#div_controle_concentracao_1_add").removeClass("d-none");
			$("#div_controle_concentracao_2_add").removeClass("d-none");
			$("#div_unidade_medida_add").removeClass("d-none");
			$("#div_controla_concentracao_add").removeClass("d-none");

		}

	});

	$("#controle_concentracao_add").change(function() {

		var controle_concentracao_add = $("#controle_concentracao_add").val();

		if (controle_concentracao_add == 1) {

			$("#div_controle_concentracao_1_add").removeClass("d-none");
			$("#div_controle_concentracao_1_add").addClass("animated fadeIn");

			$("#div_controle_concentracao_2_add").addClass("d-none");



		}





		if (controle_concentracao_add == 2) {

			$("#div_controle_concentracao_2_add").removeClass("d-none");
			$("#div_controle_concentracao_2_add").addClass("animated fadeIn");

			$("#div_controle_concentracao_1_add").addClass("d-none");


		}

		if (controle_concentracao_add == 3) {

			$("#div_controle_concentracao_2_add").removeClass("d-none");
			$("#div_controle_concentracao_1_add").removeClass("d-none");

			$("#div_controle_concentracao_1_add").addClass("animated fadeIn");
			$("#div_controle_concentracao_2_add").addClass("animated fadeIn");




		}


	});
</script>




<!--end::Base Scripts============================== -->