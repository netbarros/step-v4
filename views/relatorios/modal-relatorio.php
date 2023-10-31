<?php
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();

$_SESSION['pagina_atual'] = 'Relatório para o Cliente';
$projeto_atual = isset($_POST['projeto_atual']) ? trim($_POST['projeto_atual']) : '';

?>

<!--begin::Modal content-->
<div class="modal-content rounded">
            <!--begin::Modal header-->
            <div class="modal-header pb-0 border-0 justify-content-end">
                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </div>
                <!--end::Close-->
            </div>
            <!--begin::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">

<div class="card card-p-0 card-flush">

<!-- Certifique-se de que você incluiu Bootstrap CSS -->
<form method="post" class="form" id="form_monta_relatorio">
    <div class="mb-3">
        <label for="tabelas" class="form-label">Selecione até 3 tabelas:</label>
        <select name="tabelas[]" multiple class="form-select">
            <!-- O código PHP para popular esta lista seria inserido aqui -->
        </select>
    </div>
    
    <div class="mb-3">
        <label for="colunas" class="form-label">Selecione as colunas a serem exibidas:</label>
        <select name="colunas[]" multiple class="form-select">
            <option value="coluna1">Coluna 1</option>
            <option value="coluna2">Coluna 2</option>
            <option value="coluna3">Coluna 3</option>
        </select>
    </div>
    
    <div class="mb-3">
        <label for="formato" class="form-label">Selecione o formato de exportação:</label>
        <select name="formato" class="form-select">
            <option value="pdf">PDF</option>
            <option value="excel">Excel</option>
        </select>
    </div>
    
    <button type="submit" class="btn btn-primary" id="Gera_Relatorio">
        <span class="svg-icon svg-icon-2">
            <!-- Seu SVG aqui -->
        </span>
        Gerar relatório
    </button>
</form>


</div>
</div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Tarefas Período-->


<script>




document.getElementById("Gera_Relatorio").addEventListener("click", function(event){

        event.preventDefault(); // Evita que o formulário seja enviado

   

    var dados = $("#form_monta_relatorio").serialize();
    
    $.ajax({
        type: 'POST',
        url: './crud/relatorios/monta-relatorios.php',
        dataType: 'json',
        data: dados,
        beforeSend: function(){
            alert("Enviando ...");
        },
        error: function(){
            alert("Falha ao enviar dados !!!");
        },
        success: function(retorno){
            alert('Recebeu o dado de volta');
        }
    });
})


</script>






