<?php

require_once '../../../conexao.php';
// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();


$projeto_id = isset($_GET['id']) ? $_GET['id'] : (isset($_COOKIE['projeto_atual']) ? $_COOKIE['projeto_atual'] : '');


$sql_lista_proj = $conexao->query("SELECT * FROM arquivos_projeto aq WHERE aq.id_obra='$projeto_id' ");

$conta_lista_proj = $sql_lista_proj->rowCount();

if ($conta_lista_proj > 0) {

    $rlista_proj = $sql_lista_proj->fetchALL(PDO::FETCH_ASSOC);

    foreach ($rlista_proj as $r) {


        $data_cadastro = $r['data_cadastro_doc'];
        $data_hoje = date_create()->format('Y-m-d');


        $data_inicio = new DateTime($data_cadastro);
        $data_fim = new DateTime($data_hoje);

        // Resgata diferença entre as datas
        $dateInterval = $data_inicio->diff($data_fim);
        $tempo_de_vida =  $dateInterval->days;


        $arquivo_path = $_SERVER['DOCUMENT_ROOT'] . '/arquivo-projeto/' . $r['arquivo_doc'];

if (file_exists($arquivo_path)) {
    $extensao = mime_content_type($arquivo_path);
} else {
    // Trate o erro conforme necessário (por exemplo, definindo a extensão como desconhecida ou exibindo uma mensagem de erro)
    $extensao = 'unknown';
}

if($extensao!='unknown'){   

switch ($extensao) {
    case "text/plain":
        $icone='<img src="assets/media/svg/files/doc.svg" class="theme-light-show" alt="" />
        <img src="assets/media/svg/files/doc-dark.svg" class="theme-dark-show" alt="" />';
        break;

        case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
            $icone='<img src="assets/media/svg/files/excel-file-icon.svg" class="theme-light-show" alt="" />
            <img src="assets/media/svg/files/excel-file-icon.svg" class="theme-dark-show" alt="" />';
            break;

    case "image/png":
        $icone='<img src="assets/media/svg/files/download-png-icon.svg" class="theme-light-show" alt="" />
        <img src="assets/media/svg/files/download-png-icon.svg" class="theme-dark-show" alt="" />';
        break;

    case "image/gif":
        $icone='<img src="assets/media/svg/files/gif.svg" class="theme-light-show" alt="" />
        <img src="assets/media/svg/files/gif.svg" class="theme-dark-show" alt="" />';
        break;

    case "image/jpeg":
        $icone='<img src="assets/media/svg/files/jpeg-file-icon.svg" class="theme-light-show" alt="" />
        <img src="assets/media/svg/files/jpeg-file-icon.svg" class="theme-dark-show" alt="" />';
        break;

    case "application/vnd.ms-powerpoint":
        $icone='<img src="assets/media/svg/files/doc.svg" class="theme-light-show" alt="" />
        <img src="assets/media/svg/files/doc-dark.svg" class="theme-dark-show" alt="" />';
        break;

    case "application/pdf": 
        $icone='<img src="assets/media/svg/files/pdf.svg" class="theme-light-show" alt="" />
        <img src="assets/media/svg/files/pdf-dark.svg" class="theme-dark-show" alt="" />';
        break;

    case "application/xml": 
        $icone='<img src="assets/media/svg/files/xml.svg" class="theme-light-show" alt="" />
        <img src="assets/media/svg/files/xml-dark.svg" class="theme-dark-show" alt="" />';
        break;

    case "audio/mpeg": 
        $icone='<img src="assets/media/svg/files/doc.svg" class="theme-light-show" alt="" />
        <img src="assets/media/svg/files/doc-dark.svg" class="theme-dark-show" alt="" />';
        break;

    default:
    $icone='<img src="assets/media/svg/files/folder-document.svg" class="theme-light-show" alt="" />
        <img src="assets/media/svg/files/folder-document-dark.svg" class="theme-dark-show" alt="" />';
    }

} else {

    $icone='<img src="assets/media/svg/files/folder-document.svg" class="theme-light-show" alt="" />
    <img src="assets/media/svg/files/folder-document-dark.svg" class="theme-dark-show" alt="" />';

}

?>

        <!--begin::Col-->
        <div class="col-md-6 col-lg-4 col-xl-3">

            <!--begin::Card-->
            <div class="card h-100">
                <!--begin::Card body-->
                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                    <!--begin::Name-->
                    <a href="/arquivo-projeto/<?= $r['arquivo_doc']; ?>" target="_blank" class="text-gray-800 text-hover-primary d-flex flex-column">
                        <!--begin::Image-->
                        <div class="symbol symbol-60px mb-5">
                            <?=$icone;?>
                        </div>
                        <!--end::Image-->
                        <!--begin::Title-->
                        <div class="fs-5 fw-bold mb-2"><?= $r['nome_doc']; ?></div>
                        <!--end::Title-->
                    </a>
                    <!--end::Name-->
                    <!--begin::Description-->
                    <div class="fs-7 fw-semibold text-gray-400"><?= $tempo_de_vida; ?> dias atrás <?=$extensao ;?></div>
                    <!--end::Description-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->

        </div>
        <!--end::Col-->
<?php  }
} ?>

<!--begin::Col Upload Arquivo-->
<div class="col-md-6 col-lg-4 col-xl-3">
    <!--begin::Card-->
    <form class="card h-100 flex-center bg-light-primary  p-8" action="#" method="post">
        <!--begin::Input group-->
        <div class="fv-row ">
            <!--begin::Dropzone-->
            <div class="dropzone" id="kt_dropzonejs_arquivos_projeto">
                <!--begin::Message-->
                <div class="dz-message needsclick">
                    <!--begin::Icon-->
                    <img src="../../tema/dist/assets/media/svg/files/upload.svg" class="mb-5" alt="">
                    <!--end::Icon-->

                    <!--begin::Info-->
                    <div class="ms-4">
                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">Arraste e Solte os arquivos ou Clique Aqui para enviar.</h3>
                        <span class="fs-7 fw-semibold text-gray-400">Upload máximo de 10 arquivos</span>
                    </div>
                    <!--end::Info-->
                </div>
            </div>
            <!--end::Dropzone-->
        </div>
        <!--end::Input group-->
    </form>
    <!--end::Form-->
    <!--end::Card-->
</div>
<!--end::Col Upload Arquivo-->


