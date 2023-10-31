<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

$_SESSION['pagina_atual'] = 'Relatório de Clientes';

$tipo_relatorio = trim(isset($_POST['tipo_relatorio'])) ? $_POST['tipo_relatorio'] : '';

$Periodo_Inicial = trim(isset($_POST['Periodo_Inicial'])) ? $_POST['Periodo_Inicial'] : '';
$Periodo_Final = trim(isset($_POST['Periodo_Final'])) ? $_POST['Periodo_Final'] : '';

$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';

$projeto_atual = trim(isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';

$id_estacao = $_POST['estacao_plcode'] ?? '';

if($tipo_relatorio=='listagem_qrcode'){


	$stmt = $conexao->prepare("SELECT p.id_ponto, p.nome_ponto, o.nome_obra, e.nome_estacao FROM
   obras o 
   INNER JOIN
   estacoes e ON e.id_obra = o.id_obra
   INNER JOIN
   pontos_estacao p ON p.id_obra = o.id_obra
   
   WHERE e.id_estacao='$id_estacao' AND (o.status_cadastro!='2' AND e.status_estacao!='2' AND p.status_ponto!='2') ");
$stmt->execute();

$conta = $stmt->rowCount();
   if($conta > 0){

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Início da lista de QR Codes com moldura usando Bootstrap
echo '<table class="container" >';
echo '<h2 class="text-center mb-4" id="texto_listagem_plcode">Listagem de QR Codes</h2>';
echo '<div class="row" id="listagem_qrcode_impressao">';

// Loop através dos resultados da consulta
foreach ($result as $row) {
  // URL do QR Code dinâmico
  $conteudo= "https://step.eco.br/?p=".$row['id_ponto'];
  $qr_code_url = 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=' . $conteudo;

  // Exibição do QR Code com moldura usando Bootstrap

  echo '<div class="col-md-3 py-0 px-0 me-1 mb-2 mw-300px " id="PLCode_'.$row['id_ponto'].'">';
  echo' <a href="javascript:;" id="Click_PLCode_'.$row['id_ponto'].'" data-id="PLCode_'.$row['id_ponto'].'"  data-titulo="Núcleo: '.$row['nome_estacao'].' &rarr; PLCode '.$row['nome_ponto'].' " class="col-md-12 border border-gray-200 border-dashed rounded py-0 px-0 me-0 mb-2 mw-300px d-inline-flex align-items-center justify-content-center overlay card-rounded gera_impressao_QRCODE" >';
  echo '<div class="card border-0 text-center">';
  echo '<div class="header-logo  flex-grow-1 mh-10px py-2">
  
      
  <img alt="Logo" src="assets/media/logos/logo-4.png" class="logo-sticky h-35px">

</div>';
  echo '<div class="card-body ">';
  echo '<span class="badge badge-dark mt-3 ">' . $row['nome_obra'] . '</span>';
  echo '<br>';
  echo '<span class="badge badge-primary mt-3 py-2"> <span class="text-dark"> Núcleo';
  echo '
  <span class="svg-icon svg-icon-4 svg-icon-light">
  <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
   <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"/>
      <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
      <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
      <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"/>
  </g>
</svg>
  </span>';
  echo '</span> ' . $row['nome_estacao'] . '</span>';
  echo '<h5 class="card-title mt-3 " >' . $row['nome_ponto'] . '</h5>';
  echo '<img src="' . $qr_code_url . '" />';
 
  echo '</div>';
  echo '</div>';
  echo '     <div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow">
  <i class="bi bi-printer-fill text-white fs-3x"></i>
</div>
</a>';
  echo '</div>';

  
echo"<script>
// Verifica se a página foi carregada completamente

   

    function gerarRelatorio() {
        console.log('Intenção de Impressão');
    
        var id = this.getAttribute('data-id');
        var titulo = this.getAttribute('data-titulo');
        var content = document.getElementById(id);
    
        printJS({
            documentTitle: titulo,
            printable: content,
            type: 'html',
            header: 'PLCode STEP',
            css: './assets/css/style.bundle.css',
            scanStyles: false,
            showModal: true,
            modalMessage: 'Gerando a Impressão, Por favor, aguarde...'
        });
    }
    
    document.querySelector('#Click_PLCode_$row[id_ponto]').addEventListener('click', gerarRelatorio);
</script>";


}

// Fim da lista de QR Codes com moldura usando Bootstrap
echo '</div>';
echo '</table>';
}else{


  echo'<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
  <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
  <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
      <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
    </svg>
  </span>
  <!--end::Svg Icon-->
                            <div class="fw-semibold">
                            <h4 class="text-gray-900 fw-bold">Não foi localizado nenhum PLCode para este Núcleo de Operação.</h4>
    <div class="fs-6 text-gray-700">Verifique se os PLCodes estão ativos.</div>
  </div>
</div>';
}


// Encerramento da conexão com o banco de dados
$conexao = null;

} // fecha tipo_relatorio= leituras realizadas

?>