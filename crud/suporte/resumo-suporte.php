<?php
 // buffer de saída de dados do php]
// Instancia Conexão PDO
require_once "../../conexao.php";
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';
?>
<!DOCTYPE html>
<!--
Author: Fabiano Barros
Product Name: STEP Sistema de Tratamento EP
Purchase: https://step.eco.br
Website: http://step.eco.br 
Contact: dev@grupoep.com.br
Versão: 3.01
-->
<html lang="pt-br">

<head>
    <base href="/tema/dist/">
    <title>STEP &amp; GrupoEP</title>
    <meta charset="utf-8" />
    <meta name="description" content="Sistema de Tratamento Grupo EP - Iot - Tratamento de Efluentes" />
    <meta name="keywords" content="STEP, GrupoEP, EP, Tratamento de Efluentes, iot, Sistema, Controle, Tratamento Inteligente, água, osmose, osmose reversa" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="STEP, GrupoEP, EP, Tratamento de Efluentes, iot, Sistema, Controle, Tratamento Inteligente, água, osmose, osmose revers" />
    <meta property="og:url" content="https://grupoep.com.br/eptech" />
    <meta property="og:site_name" content="STEP | GrupoEP" />
    <link rel="canonical" href="https://step.eco.br" />
    <link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" async />
    <!--end::Fonts-->
    <!--begin::Vendor Stylesheets(used by this page)-->
    <link href="assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/custom/vis-timeline/vis-timeline.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/custom/prismjs/prismjs.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" async />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" async />
    <link rel="stylesheet" type="text/css" href="../../node_modules/print-js/dist/print.css">
    <!--end::Global Stylesheets Bundle-->




</head>
<!--end::Head-->
<!--begin::Body-->

<body data-kt-name="metronic" id="kt_body" class="page-loading-enabled page-loading header-fixed header-tablet-and-mobile-fixed toolbar-enabled">
 <!--begin::Theme mode setup on page load-->
 <script>
        if (document.documentElement) {
            const defaultThemeMode = "system";
            const name = document.body.getAttribute("data-kt-name");
            let themeMode = localStorage.getItem("kt_" + (name !== null ? name + "_" : "") + "theme_mode_value");
            if (themeMode === null) {
                if (defaultThemeMode === "system") {
                    themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            document.documentElement.setAttribute("data-theme", themeMode);
        }
    </script>

    <script>
        var defaultThemeMode = "dark";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-theme-mode");
            } else {
                if (localStorage.getItem("data-theme") !== null) {
                    themeMode = localStorage.getItem("data-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-theme", themeMode);
        }


        // carrega o blip para comunicação 
    </script>
<?php
// Data consulta global do dashboard //
$Data_Atual_Periodo = date_create()->format('Y-m-d ');
// == a data está pegando desde 2019, assim que o módulo for liberado, alterar a data de invervalo para -7 dias entre -14 dias
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-7 days', strtotime($Data_Atual_Periodo)));

print "<h1>Últimos 7 Dias:</h1>";
//=== [ operador mais assiduo]===//
$sql_op_mais=$conexao->query("SELECT COUNT(r.id_rmm) as total_operador, 
u.nome,
o.nome_obra
FROM rmm r 
INNER JOIN 
pontos_estacao p ON p.id_ponto = r.id_ponto 
INNER JOIN 
obras o ON o.id_obra = p.id_obra 
INNER JOIN 
usuarios u ON u.id = r.id_operador 
WHERE o.status_cadastro='1' AND  r.data_leitura >= '$Data_Intervalo_Periodo'  AND o.id_obra != '38'
GROUP BY r.id_operador 
ORDER BY `total_operador` DESC LIMIT 0,7

 ");

$total_check = $sql_op_mais->rowCount();
$row1 = $sql_op_mais->fetchALL(PDO::FETCH_ASSOC);

print "<h3>Operador mais Assíduo - Últimos 7 Dias:</h3>";

    // Imprime o cabeçalho da tabela
    print "<table class='table table-striped border rounded gy-5 gs-7'>";
    print "<thead>";
    print "<tr class='text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0'>";
    print "<th>Nome</th>";
    print "<th>Obra</th>";
    print "<th>Leituras</th>";
    print "</tr>";
    print "</thead>";
    print "<tbody>";

   foreach($row1 as $r){
        print "<tr>";
        print "<td>{$r['nome']}</td>";
        print "<td>{$r['nome_obra']}</td>";
        print "<td>" . number_format($r['total_operador'], 0, ',', '.') . "</td>";
        print "</tr>";
    }
     // Fecha as tags da tabela
     print "</tbody>";
     print "</table>";

$sql_op_mais=null;
//=== [ fecha operador mais assiduo]===//



//=== [ obra mais assiduo]===//
$sql_obra_mais = $conexao->query("SELECT COUNT(r.id_rmm) as total_obra, 
o.nome_obra,u.nome, u.nivel
FROM rmm r 
INNER JOIN 
pontos_estacao p ON p.id_ponto = r.id_ponto 
INNER JOIN 
obras o ON o.id_obra = p.id_obra 
INNER JOIN
usuarios_projeto up ON up.id_obra = o.id_obra
INNER JOIN usuarios u ON u.id = up.id_usuario
 WHERE o.status_cadastro='1'  AND  r.data_leitura >= '$Data_Intervalo_Periodo' AND o.id_obra != '38' AND u.nivel = 'supervisor' AND up.responsavel='1'
GROUP BY p.id_obra
ORDER BY `total_obra` DESC LIMIT 0,7

 ");

$total_check = $sql_obra_mais->rowCount();
$row2 = $sql_obra_mais->fetchALL(PDO::FETCH_ASSOC);

print "<h3>Projetos mais Assíduo - Últimos 7 Dias:</h3>";
    // Imprime o cabeçalho da tabela
    print "<table class='table table-striped border rounded gy-5 gs-7'>";
    print "<thead>";
    print "<tr class='text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0'>";
    print "<th>Obra</th>";
    print "<th>RO</th>";
    print "<th>Leituras</th>";
    print "</tr>";
    print "</thead>";
    print "<tbody>";

foreach($row2 as $r){

    print "<tr>";
    print "<td>{$r['nome']}</td>";
    print "<td>{$r['nome_obra']}</td>";
    print "<td>" . number_format($r['total_obra'], 0, ',', '.') . "</td>";
    print "</tr>";

}
 // Fecha as tags da tabela
 print "</tbody>";
 print "</table>";

$sql_obra_mais = null;
//=== [ fecha obra mais assiduo]===//



//=== [ obra menos assiduo]===//
$sql_obra_menos = $conexao->query("SELECT COUNT(r.id_rmm) as total_obra, 
o.nome_obra,u.nome, u.nivel
FROM rmm r 
INNER JOIN 
pontos_estacao p ON p.id_ponto = r.id_ponto 
INNER JOIN 
obras o ON o.id_obra = p.id_obra 
INNER JOIN
usuarios_projeto up ON up.id_obra = o.id_obra
INNER JOIN usuarios u ON u.id = up.id_usuario
 WHERE o.status_cadastro='1'  AND  r.data_leitura >= '$Data_Intervalo_Periodo' AND o.id_obra != '38'  AND u.nivel = 'supervisor' AND up.responsavel='1'
GROUP BY p.id_obra
ORDER BY `total_obra` ASC LIMIT 0,7

 ");

$total_check = $sql_obra_menos->rowCount();
$row3 = $sql_obra_menos->fetchALL(PDO::FETCH_ASSOC);

print "<h3>Projetos menos Assíduo - Últimos 7 Dias:</h3>";
// Imprime o cabeçalho da tabela
print "<table class='table table-striped border rounded gy-5 gs-7'>";
print "<thead>";
print "<tr class='text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0'>";
print "<th>Nome</th>";
print "<th>Obra</th>";
print "<th>Leituras</th>";
print "</tr>";
print "</thead>";
print "<tbody>";
foreach($row3 as $r){

    print "<tr>";
    print "<td>{$r['nome']}</td>";
    print "<td>{$r['nome_obra']}</td>";
    print "<td>" . number_format($r['total_obra'], 0, ',', '.') . "</td>";
    print "</tr>";

}
// Fecha as tags da tabela
print "</tbody>";
print "</table>";

$sql_obra_menos = null;
//=== [ fecha obra menos assiduo]===//

print "<h1>Totalizadores:</h1>";
//=== [ Cock pits criados]===//
$sql_cockpit = $conexao->query("SELECT COUNT(c.id_cockpit) as total_cockpit
FROM cockpit c

WHERE c.status_cockpit='1'

ORDER BY `total_cockpit` DESC LIMIT 0,7

 ");

$total_check = $sql_cockpit->rowCount();
$row4 = $sql_cockpit->fetchALL(PDO::FETCH_ASSOC);

print "<h3>Cockpit's Criados:</h3>";

foreach($row4 as $r){
   

    echo '<p>';
    echo ' Total ->  ';

    echo '<b>' . number_format($r['total_cockpit'], 0, ',', '.') . '</b> <small>Cockpit (s)</small>';
    echo '</p>';

}


//=== [ fecha Cock pits criados]===//


//=== [ Supervisores/obras com cock pits]===//
$sql_cockpit = $conexao->query("SELECT COUNT(c.id_cockpit) as total_cockpit, 
o.nome_obra,
u.nome as nome_usuario,
e.nome_estacao

FROM cockpit c
INNER JOIN 
estacoes e ON e.id_estacao = c.estacao_selecionada_regra 
INNER JOIN 
obras o ON o.id_obra = e.id_obra
INNER JOIN
usuarios_projeto up ON up.id_obra = o.id_obra
INNER JOIN
usuarios u ON u.id = up.id_usuario

WHERE o.status_cadastro='1' 
AND e.status_estacao='1'
AND o.id_obra != '38'
AND up.nivel='supervisor'
AND up.responsavel='1'

GROUP BY o.id_obra
ORDER BY `total_cockpit` DESC

 ");

$total_check = $sql_cockpit->rowCount();
$row5 = $sql_cockpit->fetchALL(PDO::FETCH_ASSOC);

print "<h3>Supervisores/obras com Cockpit's Total:</h3>";
// Imprime o cabeçalho da tabela
print "<table class='table table-striped border rounded gy-5 gs-7'>";
print "<thead>";
print "<tr class='text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0'>";
print "<th>Obra</th>";
print "<th>Núcleo</th>";
print "<th>Supervisor</th>";
print "<th>Cockpits</th>";
print "</tr>";
print "</thead>";
print "<tbody>";
foreach($row5 as $r){

    print "<tr>";
    print "<td>{$r['nome_obra']}</td>";
    print "<td>{$r['nome_estacao']}</td>";
    print "<td>{$r['nome_usuario']}</td>";
    print "<td>".number_format($r['total_cockpit'], 0, ',', '.')."</td>";
    print "</tr>";
}
// Fecha as tags da tabela
print "</tbody>";
print "</table>";
$sql_cockpit = null;
//=== [ fecha Supervisores/obras com cock pits]===//





//=== [ Supervisores/obras SEM cock pits]===//
$sql_cockpit = $conexao->query("SELECT  
o.nome_obra,
e.nome_estacao,
u.nome


FROM obras o

INNER JOIN 
estacoes e ON e.id_obra = o.id_obra
JOIN
usuarios_projeto up ON up.id_obra = o.id_obra
INNER JOIN
usuarios u ON u.id = up.id_obra

WHERE e.id_estacao NOT IN (SELECT c.estacao_selecionada_regra FROM cockpit c WHERE c.estacao_selecionada_regra = e.id_estacao ) 
AND o.status_cadastro='1'  
AND e.status_estacao='1'
AND o.id_obra != '38'
AND up.nivel='supervisor'
AND up.responsavel='1'

GROUP BY e.id_estacao


 ");

$total_check = $sql_cockpit->rowCount();
$row6 = $sql_cockpit->fetchALL(PDO::FETCH_ASSOC);

if($total_check > 0){

print "<h3>Supervisores/obras sem Cockpit's Total:</h3>";
print "<table class='table table-striped border rounded gy-5 gs-7'>";
print "<thead>";
print "<tr class='text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0'>";
print "<th>Obra</th>";
print "<th>Núcleo</th>";
print "<th>Supervisor</th>";
print "</tr>";
print "</thead>";
print "<tbody>";
foreach ($row6 as $r) {
    print "<tr>";
    print "<td>{$r['nome_obra']}</td>";
    print "<td>{$r['nome_estacao']}</td>";
    print "<td>{$r['nome_usuario']}</td>";
    print "<td>".number_format($r['total_cockpit'], 0, ',', '.')."</td>";
    print "</tr>";
}
// Fecha as tags da tabela
print "</tbody>";
print "</table>";

}else{
    echo '<p> Não foi localizado Projetos e ou Núcleos sem ao menos 1 Cockpit Criado. </p>';

}
$sql_cockpit = null;
//=== [ fecha Supervisores/obras SEM cock pits]===//




//obra com menor prazo em tratativa de chamados

$sql_prazo_menor = $conexao->query("SELECT TIMEDIFF(s.data_open, s.data_close) as Menor_prazo, o.nome_obra,s.data_open,s.data_close,s.id_suporte FROM suporte s
INNER JOIN 
estacoes e ON e.id_estacao = s.estacao
INNER JOIN 
obras o ON o.id_obra = e.id_obra

WHERE s.status_suporte='4'
GROUP BY o.id_obra ORDER BY Menor_prazo DESC

");

$row7 = $sql_prazo_menor->fetchALL(PDO::FETCH_ASSOC);
print "<h3>Projetos com menor prazo em tratativa de chamados - Total:</h3>";

$DataAtual = new DateTime();

print "<table class='table table-striped border rounded gy-5 gs-7'>";
print "<thead>";
print "<tr class='text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0'>";
print "<th>Ticket ID</th>";
print "<th>Obra</th>";
print "<th>Abertura</th>";
print "<th>Fechamento</th>";
print "<th>Prazo</th>";
print "</tr>";
print "</thead>";
print "<tbody>";


foreach ($row7 as $r) {
    $data_open = new DateTime($r['data_open']);
    $data_close = new DateTime($r['data_close']);


    print "<tr>";
    print "<td>{$r['id_suporte']}</td>";
    print "<td>{$r['nome_obra']}</td>";
    print "<td>". $data_open->format('d/m/Y H:i')."</td>";
    print "<td>".$data_close->format('d/m/Y H:i')."</td>";
    print "<td>".$r['Menor_prazo']."</td>";
    print "</tr>";
}
// Fecha as tags da tabela
print "</tbody>";
print "</table>";
$sql_prazo_menor = null;
//FIM obra com menor prazo em tratativa de chamados





//obra com MAIOR prazo em tratativa de chamados

$sql_prazo_maior = $conexao->query("SELECT TIMEDIFF(s.data_open, s.data_close) as Maior_prazo, o.nome_obra,s.data_open,s.data_close,s.id_suporte FROM suporte s
INNER JOIN 
estacoes e ON e.id_estacao = s.estacao
INNER JOIN 
obras o ON o.id_obra = e.id_obra

WHERE s.status_suporte='4'
GROUP BY o.id_obra ORDER BY Maior_prazo ASC

");

$row8 = $sql_prazo_maior->fetchALL(PDO::FETCH_ASSOC);
print "<h3>Projetos com Maior prazo em Encerramento de Chamados - Total:</h3>";

$DataAtual = new DateTime();
print "<table class='table table-striped border rounded gy-5 gs-7'>";
print "<thead>";
print "<tr class='text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0'>";
print "<th>Ticket ID</th>";
print "<th>Obra</th>";
print "<th>Abertura</th>";
print "<th>Fechamento</th>";
print "<th>Prazo</th>";
print "</tr>";
print "</thead>";
print "<tbody>";
foreach ($row8 as $r) {
    $data_open = new DateTime($r['data_open']);
    $data_close = new DateTime($r['data_close']);

    print "<tr>";
    print "<td>{$r['id_suporte']}</td>";
    print "<td>{$r['nome_obra']}</td>";
    print "<td>". $data_open->format('d/m/Y H:i')."</td>";
    print "<td>".$data_close->format('d/m/Y H:i')."</td>";
    print "<td>".$r['Maior_prazo']."</td>";
    print "</tr>";
}
$sql_prazo_maior = null;
//FIM obra com MAIOR prazo em tratativa de chamados
// Fecha as tags da tabela
print "</tbody>";
print "</table>";



//obra com chamado mais antigo




$sql_suporte_antigo = $conexao->query("SELECT o.nome_obra,s.data_open,s.data_close, s.data_prevista,s.id_suporte FROM suporte s
INNER JOIN 
estacoes e ON e.id_estacao = s.estacao
INNER JOIN 
obras o ON o.id_obra = e.id_obra
WHERE s.status_suporte!='4' 
GROUP BY s.estacao
ORDER BY s.data_open ASC 
");


$row9 = $sql_suporte_antigo->fetchALL(PDO::FETCH_ASSOC);
print "<h3>Projetos com chamados não Finalizados - Total: </h3>";

$DataAtual = new DateTime();
print "<table class='table table-striped border rounded gy-5 gs-7'>";
print "<thead>";
print "<tr class='text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0'>";
print "<th>Ticket ID</th>";
print "<th>Obra</th>";
print "<th>Abertura</th>";
print "</tr>";
print "</thead>";
print "<tbody>";

foreach ($row9 as $r) {
    $data_open = new DateTime($r['data_open']);

    $data_prevista_='';
    if($r['data_prevista']!=''){
    $data_prevista = new DateTime($r['data_prevista']) ;
    $data_prevista_ = isset($data_prevista) ? $data_prevista->format('d/m/Y H:i') :'';
    }
    $data_open = new DateTime($r['data_open']);
   

    print "<tr>";
    print "<td>{$r['id_suporte']}</td>";
    print "<td>{$r['nome_obra']}</td>";
    print "<td>". $data_open->format('d/m/Y H:i')."</td>";
    print "</tr>";
}
// Fecha as tags da tabela
print "</tbody>";
print "</table>";
$sql_suporte_antigo=null;



//fim obra com chamado mais antigo
?>
 <!--begin::Javascript-->
  <!--begin::Javascript-->
  <script>
            var hostUrl = "assets/";
        </script>
        <!--begin::Global Javascript Bundle(used by all pages)-->
        <script src="assets/plugins/global/plugins.bundle.js"></script>
        
        <script src="assets/js/scripts.bundle.js"></script>
      <!--begin::JavaScript-->
<script>
  function loadScripts() {
    const scripts = [
      'assets/plugins/custom/prismjs/prismjs.bundle.js',
      'assets/plugins/custom/fslightbox/fslightbox.bundle.js',
      '../../js/dashboard/step-js.js',
      '../../js/suportes/chat/chat.js',
      'assets/js/widgets.bundle.js',
      'assets/js/custom/widgets.js',
      '../../js/cockpit/create-cockpit.js',
      '../../node_modules/print-js/dist/print.js',
      'assets/plugins/custom/datatables/datatables.bundle.js',
      '../../js/relatorios-suporte/relatorios.js',
      'assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js',
      '../../js/relatorios-suporte/graficos-relatorio.js',
      'assets/plugins/custom/draggable/draggable.bundle.js',
            'assets/js/custom/documentation/general/draggable/multiple-containers.js',
    ];

    let loadedScripts = 0;
    scripts.forEach((src) => {
      const script = document.createElement('script');
      script.src = src;
      script.async = false;
      script.onload = () => {
        loadedScripts++;
        if (loadedScripts === scripts.length) {
                    $('#overlay').fadeOut();
                }
      };
      document.body.appendChild(script);
    });
  }

  window.addEventListener('DOMContentLoaded', function () {
        $('#overlay').fadeIn();
        loadScripts();
    });
</script>
<!--end::JavaScript-->
    
</body>
</html>