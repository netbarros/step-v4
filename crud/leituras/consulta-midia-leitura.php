<?php
// Atribui uma conexão PDO
include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$chave_unica = isset($_POST['chave_unica']) ? $_POST['chave_unica'] : '';
$id_suporte = isset($_POST['id']) ? $_POST['id'] : '';

if($chave_unica!=''){
    $ticket_suporte='';
    if($id_suporte!=''){

$ticket_suporte='<div class="border border-gray-300 border-dashed rounded mw-180px py-2 px-10 me-2 mb-1">
<!--begin::Number-->
<div class="d-flex align-items-center">Ticket nº  '.$id_suporte.'</div></div>';

    }





    $stmt = $conexao->prepare("SELECT md.*, pt.nome_ponto, pr.nome_parametro, pr.concen_min, pr.concen_max FROM midia_leitura md
    LEFT JOIN pontos_estacao pt ON pt.id_ponto = md.id_plcode 
    LEFT JOIN parametros_ponto pr ON pr.id_parametro = md.id_parametro WHERE md.chave_unica = :chave_unica");
    $stmt->execute([':chave_unica' => $chave_unica]);



echo '<script src="assets/plugins/custom/fslightbox/fslightbox.bundle.js"></script>';

echo '<div class="modal-content rounded position-absolute">
<!--begin::Modal header-->
<div class="modal-header">
    <!--begin::Close-->
    <h3 class="modal-title">Coleção de Mídias no mesmo envio</h3> 
    '.$ticket_suporte.' 
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
<!--begin::Modal header-->';

echo '<div class="modal-body scroll-y px-10 px-lg-10 pt-0 pb-10">';

echo '<div class="py-5">
<!--begin::Wrapper-->
<div class="rounded border p-5 p-lg-5">
    <!--begin::Row-->
    <div class="row">';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

$id_midia = $row['id_midia'];
// Converter o timestamp para um formato legível em português
$data_envio = new DateTime($row['data_envio']);
$data_envio_formatada = $data_envio->format('d/m/Y - H:i');

$concen_min = isset($row['concen_min']) ? '<span class="text-muted me-2"> Mínima: </span>'.$row['concen_min'] : '';
$concen_max = isset($row['concen_max']) ? '<span class="text-muted me-2"> Máxima: </span>'.$row['concen_max'] : '';

$nome_ponto = isset($row['nome_ponto']) ? $row['nome_ponto'] : 'N/A';

$nome_parametro = isset($row['nome_parametro']) ? $row['nome_parametro'] : 'N/A';

echo '';

//echo $row['nome_midia'];
//../../crud/leituras/

$filename = $_SERVER['DOCUMENT_ROOT'] . '/app/midias_leitura/'.$row['nome_midia'];


if (file_exists($filename)) {

echo '
        <!--begin::Col-->
        <div class="col-lg-4 me-5 min-w-200px py-2">
        
            <!--begin::Overlay-->
            <a class="d-block overlay min-w-200px" data-fslightbox="lightbox-basic" href="https://step.eco.br/app/midias_leitura/'.$row['nome_midia'].'">
                <!--begin::Image-->
                <div class="overlay-wrapper bgi-no-repeat bgi-position-center bgi-size-cover card-rounded min-h-200px min-w-200px" id="'.$id_midia.'" style=\'background-image:url("https://step.eco.br/app/midias_leitura/'.$row['nome_midia'].'")\'></div>
                <!--end::Image-->
                <!--begin::Action-->
                <div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow">
                    <i class="bi bi-eye-fill text-white fs-3x"></i> <br>
                    
                </div>
                <!--end::Action-->

                <div class="border border-gray-300 text-gray-700 border-dashed rounded mw-200px py-2 ">
                <!--begin::Number-->
                <div class="d-block align-items-center "><span class="text-muted me-2">PLCode: </span>'.$nome_ponto.'</div>
                <div class="d-block align-items-center "><span class="text-muted me-2">Indicador: </span>'.$nome_parametro.'</div>
                <div class="d-block align-items-center"> '.$concen_min.' '.$concen_max.'</div>
                <div class="d-block align-items-center"><span class="text-muted me-2">Momento: </span>'.$data_envio_formatada.'</div>
                
                </div>
               
            </a>
            <!--end::Overlay-->
            
            <a href="javascript:;" id="botao-rotacionar-'.$id_midia.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Rotacionar Imagem" class="d-block mw-200px btn btn-sm btn-light">
            <i class="bi bi-arrow-clockwise"></i>
            </a>
           
        </div>
        <!--end::Col-->
 ';


 echo '<script>';
echo "document.getElementById('botao-rotacionar-$id_midia').addEventListener('click', function(e) {
    e.preventDefault();
    var image = document.getElementById('$id_midia');
    var currentRotation = parseInt(image.style.transform.replace('rotate(', '').replace('deg)', '')) || 0;
    var newRotation = currentRotation + 90;
    image.style.transform = 'rotate(' + newRotation + 'deg)';
  });";
  echo '</script>';
}



}

echo '</div>
<!--end::Row-->
</div>
<!--end::Wrapper-->
</div>';// fecha estrutura imagens

echo '</div>';// fecha body

echo '</div>'; // fecha content




}

