<?php 
date_default_timezone_set('America/Sao_Paulo');
require_once $_SERVER['DOCUMENT_ROOT'].'/app/conexao.php';
 	// Atribui uma conexão PDO
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();	


$acao = trim(isset($_POST['acao'])) ? $_POST['acao'] : '';




$seleciona_estacao = $conexao->query("SELECT e.id_estacao,e.nome_estacao,o.nome_obra FROM estacoes e

INNER JOIN obras o ON o.id_obra = e.id_obra
INNER JOIN periodo_ponto p ON e.id_estacao =  p.id_estacao

WHERE e.status_estacao='1' Group By e.id_estacao");

$dados_estacao= $seleciona_estacao->fetchAll(PDO::FETCH_ASSOC);
$total_estacao = $seleciona_estacao->rowCount ();

//echo $total_estacao;




if($total_estacao>0){

  $email_ro="";
  $email_su="";
  $nome_ro="";
  $nome_su="";

  foreach ($dados_estacao as $key => $valor) {

    $id_estacao = $valor['id_estacao'];
    $obra = $valor['nome_obra'];
    $estacao = $valor['nome_estacao'];

   // echo $id_estacao;

  $busca_responsaveis = $conexao->query("SELECT
  
  csu.nome as nome_supervisor,
  csu.email_corporativo as email_supervisor,
  cro.email_corporativo as email_ro,
  cro.nome as nome_ro

  FROM
  estacoes e
  INNER JOIN colaboradores csu ON csu.id_colaborador = e.supervisor
  INNER JOIN colaboradores cro ON cro.id_colaborador = e.ro
  WHERE e.id_estacao = '$id_estacao '
  
  ");
$dados_resp = $busca_responsaveis->fetch(PDO::FETCH_OBJ);

$email_ro = $dados_resp ->email_ro;

$email_su = $dados_resp->email_supervisor;


$nome_ro = $dados_resp->nome_ro;

$nome_su = $dados_resp->nome_supervisor;

//$id_parametro = trim(isset($_POST['id_parametro'])) ? $_POST['id_parametro'] : '';

// pega hora atual php
$hora_atual = date('H:i');
$data_atual = date('d/m/Y');


//consulta_checkin_presencial

    $data_atual_periodo= date_create()->format('Y-m-d');
    $data_intervalo_periodo=date('Y-m-d', strtotime('-1 day', strtotime($data_atual_periodo)));

   
    $sql_periodo1 = $conexao->query("SELECT periodo_ponto.*,pontos_estacao.nome_ponto, pontos_estacao.id_ponto, periodo_ponto.tipo_checkin,pontos_estacao.controla_periodo_ponto, pontos_estacao.status_ponto,parametros_ponto.id_parametro,parametros_ponto.nome_parametro,periodo_dia_ponto.dia_semana,dia_semana.nome_dia_semana, checkin.data_cadastro_checkin FROM periodo_ponto
    INNER JOIN pontos_estacao ON pontos_estacao.id_ponto = periodo_ponto.id_ponto
    LEFT JOIN checkin ON checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto 
    LEFT JOIN periodo_dia_ponto ON periodo_dia_ponto.id_ponto = periodo_ponto.id_ponto
    LEFT JOIN dia_semana ON dia_semana.representa_php=periodo_dia_ponto.dia_semana
    LEFT JOIN parametros_ponto on parametros_ponto.id_parametro =periodo_ponto.id_parametro Where
    periodo_ponto.id_periodo_ponto not in  ( SELECT  checkin.id_periodo_ponto FROM  checkin WHERE checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto AND DATE_FORMAT(checkin.data_cadastro_checkin, '%Y-%m-%d' )  = '$data_intervalo_periodo') 
    AND periodo_ponto.tipo_checkin = 'ponto_plcode' AND periodo_ponto.id_estacao ='$id_estacao ' GROUP BY periodo_ponto.id_periodo_ponto ORDER BY periodo_ponto.hora_leitura ASC"  );
    
    
    //===> exceto se constar o id_periodo_ponto na tabela checkin, este não traz, pq já foi feito o check
    
    
    $total = $sql_periodo1->rowCount ();
    
      
       // $registro = $sql_periodo->fetch(PDO::FETCH_OBJ);
        
       

       
        $hora_lida="";
        $check='';
        $tabela="";
            if($total>0){


        while ($res = $sql_periodo1->fetch(PDO::FETCH_ASSOC)) {
         // print_r($res);
        //  exit;
//           // Conta os resultados no total da query
// $strCount_1 = mysql_query("SELECT COUNT(*) AS 'total_lido_presencial' WHERE id_periodo_ponto = $res[id_periodo_ponto]");
// $row_1 = $strCount_1->fetchObject();

// $total_lido_presencial = $row_1->total_lido_presencial;

       
          
$controla_periodo = $res['modo_checkin_periodo'];

if($controla_periodo!='2'){
            $ciclo_leitura = $res['ciclo_leitura']; 

            if($ciclo_leitura=='1'){
                $ciclo='<br/>Diário';

            } else { $ciclo="semanal";}



            $hora_leitura = $res['hora_leitura'] ? $res['hora_leitura'] : '';

	
            $leitura = new DateTime($hora_leitura);
            $minima = new DateTime($hora_leitura);
            $minima->sub(new DateInterval('PT1M')); // subtrai 30 minutos do periodo da leitura
            $now = new DateTime('now');

         if(isset($hora_leitura)){
	
              $leitura = new DateTime($hora_leitura);
              $minima = new DateTime($hora_leitura);
              $minima->sub(new DateInterval('PT1M')); // subtrai 30 minutos do periodo da leitura
              $now = new DateTime('now');
  
              if ( $minima < $now  ) { 
                $status = "Não Realizado";
                $check.='1;';
                $css_status='style="color: rgb(217, 127, 77);"';
            }   if ( $minima > $now  ) {  $status = "em tempo"; $css_status='style="color: rgb(118, 172, 152);"';}
  
            
            $saida =  substr($hora_leitura, 0,5);
            $entrada   = substr($hora_atual, 0,5);
        
           
        
        
  
          }
           


     

            $tabela.='<tr> <td data-size="list" data-color="list" mc:edit="invoice-'.$res['id_periodo_ponto'].'" width="200" align="left" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
            <singleline label="list-1"> '.$res['nome_ponto'].' </singleline>
            </td>
            <td data-size="list" data-color="list" mc:edit="invoice-2" width="263" align="left" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
            <singleline label="list-2">'.$res['nome_parametro'].'</singleline>
            </td>
            <td data-size="list" data-color="list" mc:edit="invoice-13" width="74" align="center" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
            <singleline label="list-3"> <b>'.$saida.'</b>  '. $ciclo.' </singleline>
            </td>
           
            <td data-size="list" data-color="list" mc:edit="invoice-15" width="87" align="right" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size: 14px;  line-height: 26px; font-weight: bold;">
            <singleline label="list-4" '.$css_status.'>'. $status .'</singleline>
            </td>
            </tr>';

            $tabela.='<tr>
            <td data-border-size="List Border" data-border-color="List Border" height="5" style="border-bottom:1px solid #ecf0f1;"></td>
          </tr>';
     
            

        }// fecha lista check in <span style="color: rgb(55,110,00); font-weight: bold;"> Presencial </span> com controle de periodo
    
        if($controla_periodo==='2'){

          $hora_leitura = $res['hora_leitura'] ? $res['hora_leitura'] : $status='' | $css_status = '';

       if(isset($hora_leitura)){
	
            $leitura = new DateTime($hora_leitura);
            $minima = new DateTime($hora_leitura);
            $minima->sub(new DateInterval('PT1M')); // subtrai 30 minutos do periodo da leitura
            $now = new DateTime('now');

            if ( $minima < $now  ) { 
              $status = "Não Realizado";
              $check.='1;';
              $css_status='style="color: rgb(217, 127, 77);"';
          }   if ( $minima > $now  ) {  $status = "em tempo"; $css_status='style="color: rgb(118, 172, 152);"';}

         
          $saida =  substr($hora_leitura, 0,5);
          $entrada   = substr($hora_atual, 0,5);
      
         // $prazo= intervalo($entrada, $saida) ;
      
         

        } else { $status = "Não Realizado"; $css_status='style="color: rgb(217, 127, 77);"'; $check.='1;';}

            $ciclo_leitura = $res['ciclo_leitura']; 

            if($ciclo_leitura=='1'){
                $ciclo="<br/>Diário";

            } else { $ciclo="Semanal";}


            $tabela.='<tr> <td data-size="list" data-color="list" mc:edit="invoice-'.$res['id_periodo_ponto'].'" width="200" align="left" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
                    <singleline label="list-1"> '.$res['nome_ponto']. ' </singleline>
                    </td>
                    <td data-size="list" data-color="list" mc:edit="invoice-2" width="263" align="left" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
                    <singleline label="list-2"> -- </singleline>
                    </td>
                    <td data-size="list" data-color="list" mc:edit="invoice-13" width="74" align="center" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
                    <singleline label="list-3">'. $ciclo.' <span style="color: rgb(55,110,00); font-weight: bold;"> <span style="color: rgb(55,110,00); font-weight: bold;"> Presencial </span> </span></singleline>
                    </td>
                   
                    <td data-size="list" data-color="list" mc:edit="invoice-15" width="87" align="right" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size: 14px; color: rgb(213, 78, 72); line-height: 26px; font-weight: bold;">
                    <singleline label="list-4"  "><span '.$css_status.'>'. $status .'</span></singleline>
                    </td>
                    </tr>';

                    $tabela.='<tr>
                    <td data-border-size="List Border" data-border-color="List Border" height="5" style="border-bottom:1px solid #ecf0f1;"></td>
                  </tr>';


           

        }// fecha lista check in presencial sem controle de periodo
    
    }


}



//=========================================================


//consulta_checkin_leitura

//======>>>>>>>>>>>>>>>>>> Não exibir os Checkins já existentes na tabela checkin (Where Not In Checkin)

$data_atual_periodo= date_create()->format('Y-m-d H:m:s');
$data_intervalo_periodo=date('Y-m-d', strtotime('-1 days', strtotime($data_atual_periodo)));

    $conexao = Conexao::getInstance();
    $sql_periodo2 = $conexao->query("SELECT periodo_ponto.*,pontos_estacao.nome_ponto, pontos_estacao.id_ponto, periodo_ponto.tipo_checkin,pontos_estacao.controla_periodo_ponto, pontos_estacao.status_ponto,parametros_ponto.id_parametro,parametros_ponto.nome_parametro,periodo_dia_ponto.dia_semana,dia_semana.nome_dia_semana, checkin.data_cadastro_checkin FROM periodo_ponto
    INNER JOIN pontos_estacao ON pontos_estacao.id_ponto = periodo_ponto.id_ponto
    LEFT JOIN checkin ON checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto 
    LEFT JOIN periodo_dia_ponto ON periodo_dia_ponto.id_ponto = periodo_ponto.id_ponto
    LEFT JOIN dia_semana ON dia_semana.representa_php=periodo_dia_ponto.dia_semana
    LEFT JOIN parametros_ponto on parametros_ponto.id_parametro =periodo_ponto.id_parametro Where
    periodo_ponto.id_periodo_ponto not in  ( SELECT  checkin.id_periodo_ponto FROM  checkin WHERE checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto AND DATE_FORMAT(checkin.data_cadastro_checkin, '%Y-%m-%d')  > '$data_intervalo_periodo') 
    AND periodo_ponto.tipo_checkin = 'ponto_parametro' AND periodo_ponto.id_estacao ='$id_estacao' GROUP BY periodo_ponto.id_periodo_ponto ORDER BY periodo_ponto.hora_leitura ASC"   );
    
    
    //===> exceto se constar o id_periodo_ponto na tabela checkin, este não traz, pq já foi feito o check
    
    
    $total2 = $sql_periodo2->rowCount ();
    
      
       // $registro = $sql_periodo->fetch(PDO::FETCH_OBJ);
        
     


            if($total2>0){




        while ($res = $sql_periodo2->fetch(PDO::FETCH_ASSOC)) {

          // var_dump($res);




            $ciclo_leitura = $res['ciclo_leitura'];

            $dias_semana_periodo_="";
               
                $nome_dia_semana_periodo = "";
               
                $diasemana_numero = date('w', time());

            if($ciclo_leitura=='1'){
                $ciclo="<br/>Diário";

                $dias_semana_periodo="";
                

            } else { $ciclo="Semanal";  $dias_semana_periodo="<b>Dias:</b> ";}


           
            
            if($ciclo_leitura =="2"){

                $id_par_busca = $res['id_parametro'];
      

                $consulta = $conexao->query("SELECT periodo_dia_ponto.dia_semana, dia_semana.representa_php, dia_semana.nome_dia_semana FROM periodo_dia_ponto
                INNER JOIN dia_semana ON periodo_dia_ponto.dia_semana = dia_semana.representa_php WHERE periodo_dia_ponto.id_parametro ='$id_par_busca' AND periodo_dia_ponto.dia_semana='$diasemana_numero'");
                $json_data = $consulta->fetchAll(PDO::FETCH_ASSOC);


             if($json_data){
                
               
                foreach($json_data as $item){

                   $dias_semana_periodo.= $item['representa_php'].' ';

                   $nome_dia_semana_periodo .=  "<span class='kt-badge kt-badge--inline kt-badge--brand'>".$item['nome_dia_semana']."</span>";  


                }
            } else { $nome_dia_semana_periodo = '<span style="color: rgb(221,217,229); font-weight: bold;"> Hoje Não</span>';  }


            }
            
            $hora_leitura = $res['hora_leitura'] ? $res['hora_leitura'] : '';


            if(isset($hora_leitura)){
	
            $leitura = new DateTime($hora_leitura);
            $minima = new DateTime($hora_leitura);
            $minima->sub(new DateInterval('PT1M')); // subtrai 30 minutos do periodo da leitura
            $now = new DateTime('now');

            if ( $minima < $now  ) { 
              $status = "Não Realizado";
              $check.='1;';
              $css_status='style="color: rgb(217, 127, 77);"';
          }   if ( $minima > $now  ) {  $status = "em tempo"; $css_status='style="color: rgb(118, 172, 152);"';}

          //$prazo="";

          $saida =  substr($hora_leitura, 0,5);
          $entrada   = substr($hora_atual, 0,5);
      
         // $prazo= intervalo( $entrada, $saida ) ;
      
         

        }
           
          //$hora_lidax = $res['hora_lida'] ? $res['hora_lida'] : '';

         
           
    
            $tabela.='<tr> <td data-size="list" data-color="list" mc:edit="invoice-'.$res['id_periodo_ponto'].'" width="200" align="left" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
            <singleline label="list-1"> '.$res['nome_ponto'].' </singleline>
            </td>
            <td data-size="list" data-color="list" mc:edit="invoice-2" width="263" align="left" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
            <singleline label="list-2">'.$res['nome_parametro'].'</singleline>
            </td>
            <td data-size="list" data-color="list" mc:edit="invoice-13" width="74" align="center" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
            <singleline label="list-3"><strong>'.$saida.'</strong>  <span '.$css_status.'>'. $ciclo.' </span> '.$nome_dia_semana_periodo.'</singleline>
            </td>
           
            <td data-size="list" data-color="list" mc:edit="invoice-15" width="87" align="right" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size: 14px; color: rgb(213, 78, 72); line-height: 26px; font-weight: bold;">
            <singleline label="list-4" "><span '.$css_status.'>'. $status .'</span> </singleline>
            </td>
            </tr>';   
            
            $tabela.='<tr>
            <td data-border-size="List Border" data-border-color="List Border" height="5" style="border-bottom:1px solid #ecf0f1;"></td>
          </tr>';


      

        }



 }



$array = explode(';', $check);
$check_total = array_sum($array);

$mensagem =  '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>

<title>relatorio-email-checkin</title>



  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <!--[if !mso]><!-->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!--<![endif]-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>*|MC:SUBJECT|*</title>
  <style type="text/css">
.ReadMsgBody { width: 100%; background-color: #ffffff; }
.ExternalClass { width: 100%; background-color: #ffffff; }
.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
html { width: 100%; }
body { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; font-family: "Open Sans", Arial, Sans-serif !important; }
table { border-spacing: 0; table-layout: auto; margin: 0 auto; }
img { display: block !important; overflow: hidden !important; }
.yshortcuts a { border-bottom: none !important; }
img:hover { opacity: 0.9 !important; }
a { color: #f95759; text-decoration: none; }
.textbutton a { font-family: "Open Sans", arial, sans-serif !important;}
.btn-link a { color:#FFFFFF !important;}

/*Responsive*/
@media only screen and (max-width: 640px) {
body { margin: 0px; width: auto !important; font-family: "Open Sans", Arial, Sans-serif !important;}
.table-inner { width: 90% !important;  max-width: 90%!important;}
.table-full { width: 100%!important; max-width: 100%!important; text-align: center !important;}
}

@media only screen and (max-width: 479px) {
body { width: auto !important; font-family: "Open Sans", Arial, Sans-serif !important;}
.table-inner{ width: 90% !important; text-align: center !important;}
.table-full { width: 100%!important; max-width: 100%!important; text-align: center !important;}
/*gmail*/
u + .body .full { width:100% !important; width:100vw !important;}
}
</style>


</head>
<body marginwidth="0" marginheight="0" style="margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;" offset="0" topmargin="0" leftmargin="0">




  
  <!--[if !mso]><!-->
  
  <!--<![endif]-->
  
  
  



<table class="full" data-group="Invoice" data-thumbnail="http://www.stampready.net/dashboard/editor/user_uploads/zip_uploads/2019/12/17/Dg7YjPZcVKJS0Wko5vFmU6yq/All-in-one-1/thumbnails/invoice_header.jpg" data-module="invoice_header" data-bgcolor="Header BG" bgcolor="#f8f8f8" width="100%" border="0" align="center" cellpadding="0" cellspacing="0"><tr>
  <td align="center">
    <table width="700" style="max-width: 700px" class="table-full" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td align="center">
          <table width="200" class="table-full" align="left" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td data-bgcolor="Logo BG" bgcolor="#607e9d" align="center" style="background-color: rgb(34, 139, 34);">
                <table width="80%" class="table-inner" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td height="50"></td>
                  </tr>
                  <!-- logo -->
                  <tr>
                    <td align="center" style="line-height:0px;"><img editable="" label="image" mc:edit="invoice-1" style="display: block; font-size: 0px; border: 0px; line-height: 0px; max-width: 100%; max-height: 100%;" src="https://step.eco.br/images/logo-light.png" alt="logo"></td>
                  </tr>
                  <!-- end logo -->
                  <tr>
                    <td height="40" align="center" style="color: rgb(255, 255, 255);"><br><br><b style="font-family: "Open Sans", Arial, sans-serif; font-size:16px; color:#FFFFFF; line-height:26px;">Sistema de Tratamento EP Engenharia</b><br><br></td>
                  </tr>
                  <!-- company name -->
                  <tr>
                    <td data-color="Company Text" data-size="Title" mc:edit="invoice-2" style="font-size: 16px; color: rgb(255, 255, 255); line-height: 26px; font-weight: bold;">
                      <singleline label="company name"></singleline>
                    </td>
                  </tr>
                  <!-- end company name -->
                  <tr>
                    <td height="5"></td>
                  </tr>
                  <!-- address -->
                  <tr>
                    <td data-color="Company Text" data-size="Address" mc:edit="invoice-3" style="font-family: "Open Sans", Arial, sans-serif; font-size:13px; color:#FFFFFF; line-height:26px;">
                      <multiline label="address"></multiline>
                    </td>
                  </tr>
                  <!-- end address -->
                  <tr>
                    <td height="25"></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <!--[if (gte mso 9)|(IE)]></td><td><![endif]-->
          <table width="500" class="table-full" border="0" align="right" cellpadding="0" cellspacing="0">
            <tr>
              <td align="center">
                <table width="90%" class="table-inner" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td height="50"></td>
                  </tr>
                  <!-- title -->
                  <tr>
                  <td data-size="Invoice" data-color="Invoice" mc:edit="invoice-4" align="right" style="font-family: "Open Sans", Arial, sans-serif; font-size:30px; color:#3b3b3b; line-height:30px;">
                  <singleline label="title"><b class="selected-element">RELATÓRIO DE CHECKIN </b></singleline>
                </td>
                  </tr>
                  <!-- end title -->
                  <tr>
                    <td height="25"></td>
                  </tr>
                  <!--dash-->
                  <tr>
                    <td align="right">
                      <table data-width="Dash" align="right" width="50" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                          <td data-bgcolor="Dash" bgcolor="#ff646a" height="3"></td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <!--end dash-->
                  <tr>
                    <td height="15"></td>
                  </tr>
                  <!-- company name -->
                  <tr>
                    <td data-color="Customer Text" data-size="Customer Title" mc:edit="invoice-5" align="right" style="font-family: "Open Sans", Arial, sans-serif; font-size:16px; color:#3b3b3b; line-height:26px; font-weight: bold;">
                      <singleline label="company name">Não Relizados</singleline>
                    </td>
                  </tr>
                  <!-- end company name -->
                  <tr>
                    <td height="5"></td>
                  </tr>
                  <!-- address -->
                  <tr>
                    <td data-color="Customer Text" data-size="Address" mc:edit="invoice-6" align="left" style="font-family: "Open Sans", Arial, sans-serif; font-size:13px; color:#7f8c8d; line-height:26px;">
                      <multiline label="address">
						<span style="color: rgb(31, 31, 31); line-height: 26px; font-weight: bold; text-transform: uppercase;">Obra</span>: <span style="color: rgb(115, 115, 115); line-height: 26px;">'.$obra.'&nbsp;</span><br> 
						<span style="color: rgb(31, 31, 31); line-height: 26px; font-weight: bold; text-transform: uppercase;">Estação</span>: <span style="color: rgb(115, 115, 115); line-height: 26px;">'.$estacao.' </span><br>
						<span style="color: rgb(31, 31, 31); line-height: 26px; font-weight: bold; text-transform: uppercase;">Data de Análise: </span><span style="color:#3b3b3b"> <strong style="color: rgb(115, 115, 115);">'.$data_atual.'</strong> </span>
					  </multiline>
                    </td>
                  </tr>
                  <!-- end address -->
                  <tr>
                    <td height="25"></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </td>
</tr>
  </table><table class="full" data-group="Invoice" data-thumbnail="http://www.stampready.net/dashboard/editor/user_uploads/zip_uploads/2019/12/17/Dg7YjPZcVKJS0Wko5vFmU6yq/All-in-one-1/thumbnails/invoice_title.jpg" data-module="invoice_title" data-bgcolor="Main BG" mc:repeatable="layout" mc:hideable="" mc:variant="title" width="100%" align="center" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="background-color: rgb(248, 248, 248);">
    <tr>
      <td align="center">
        <table align="center" width="700" style="max-width:700px;" class="table-full" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="50"></td>
          </tr>
          <!-- header -->
          <tr>
            <td>
              <table class="table-inner" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td data-size="Title" data-color="Title" mc:edit="invoice-7" width="200" align="left" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:13px; color:#3b3b3b; line-height:26px; text-transform:uppercase;">
                    <singleline label="title-1"><b>PLCode</b></singleline>
                  </td>
                  <td data-size="Title" data-color="Title" mc:edit="invoice-8" width="163" align="left" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:13px; color:#3b3b3b; line-height:26px; text-transform:uppercase;">
                    <singleline label="title-2"><b>Parâmetro</b></singleline>
                  </td>
                  <td data-size="Title" data-color="Title" mc:edit="invoice-9" width="74" align="center" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:13px; color:#3b3b3b; line-height:26px; text-transform:uppercase;">
                    <singleline label="title-3"><b>Previsto</b></singleline>
                  </td>
				  
                  <td data-size="Title" data-color="Title" mc:edit="invoice-10" width="80" align="right" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:13px; color:#3b3b3b; line-height:26px; text-transform:uppercase;">
                    <singleline label="title-4"><b>Status</b></singleline>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <!-- end header -->
          <tr>
            <td data-border-size="Title Underline" data-border-color="Title Underline" height="10" style="border-bottom:3px solid #bcbcbc;"></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

<table class="full" data-group="Invoice" data-thumbnail="http://www.stampready.net/dashboard/editor/user_uploads/zip_uploads/2019/12/17/Dg7YjPZcVKJS0Wko5vFmU6yq/All-in-one-1/thumbnails/invoice_list.jpg" data-module="invoice_list" data-bgcolor="Main BG" mc:repeatable="layout" mc:hideable="" mc:variant="list" align="center" width="100%" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="background-color: rgb(248, 248, 248);">
    <tr>
      <td align="center">
        <table width="700" style="max-width: 700px; height: 62px;" class="table-full" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="35"></td>
          </tr>
          <tr>
            <td align="center">
              <table width="100%" class="table-inner" border="0" cellspacing="0" cellpadding="0">
               '.$tabela.'
              </table>
            </td>
          </tr>
          <tr>
            <td data-border-size="List Border" data-border-color="List Border" height="5" style="border-bottom:1px solid #ecf0f1;"></td>
          </tr>
          <tr>
            <td height="5"></td>
          </tr>
          <!-- detail -->
          <tr>
            <td align="center">
              <table class="table-inner" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td data-size="list" data-color="list" mc:edit="invoice-15" align="left" style="font-family: "Open Sans", Arial, sans-serif; font-size:12px; color:#7f8c8d; line-height:26px;"></td>
                </tr>
              </table>
            </td>
          </tr>
          <!-- end detail -->
        </table>
      </td>
    </tr>
  </table><table class="full" data-group="Invoice" data-thumbnail="http://www.stampready.net/dashboard/editor/user_uploads/zip_uploads/2019/12/17/Dg7YjPZcVKJS0Wko5vFmU6yq/All-in-one-1/thumbnails/invoice_list.jpg" data-module="invoice_list" data-bgcolor="Main BG" mc:repeatable="layout" mc:hideable="" mc:variant="list" align="center" width="100%" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="background-color: rgb(248, 248, 248);">
   
  </table><table class="full" data-group="Invoice" data-thumbnail="http://www.stampready.net/dashboard/editor/user_uploads/zip_uploads/2019/12/17/Dg7YjPZcVKJS0Wko5vFmU6yq/All-in-one-1/thumbnails/invoice_total.jpg" data-module="invoice_total" data-bgcolor="Main BG" mc:repeatable="layout" mc:hideable="" mc:variant="total" align="center" width="100%" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="background-color: rgb(248, 248, 248);">
    <tr>
      <td align="center">
        <table width="700" class="table-full" style="max-width: 700px; height: 8px;" align="center" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td data-border-size="Total Border" data-border-color="Total Border" height="40" style="border-bottom:3px solid #3b3b3b;"></td>
          </tr>
        </table>
        <table align="center" width="700" style="max-width: 700px;" class="table-full" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="15"></td>
          </tr>
          <tr>
            <td align="center">
              <table width="400" class="table-full" align="left" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td data-bgcolor="Texs BG" bgcolor="#f8f8f8" align="center">
                    <table class="table-inner" align="center" width="90%" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td height="10"></td>
                      </tr>
                      <tr>
                        <td data-size="Texs Title" data-color="Texs Text" mc:edit="invoice-16" style="font-family: "Open Sans", Arial, sans-serif; font-size:12px; color:#3b3b3b; line-height:26px; text-transform:uppercase;line-height:24px;">
                          <singleline label="title">Responsáveis:</singleline>
                        </td>
                      </tr>
                      <tr>
                        <td data-size="Texs Total" data-color="Texs Text" mc:edit="invoice-17" style="font-family: "Open Sans", Arial, sans-serif; font-size:24px; color:#3b3b3b;  font-weight: bold;">
                          <singleline label="price"><b>RO:</b> '.$nome_ro.' <br>  <b>SU:</b> '.$nome_su.' </singleline></td>
                          <td data-size="Texs Total" data-color="Texs Text" mc:edit="invoice-17" style="font-family: "Open Sans", Arial, sans-serif; font-size:24px; color:#3b3b3b;  font-weight: bold;">
                          <singleline label="price">Os PLCodes apontados neste relatório se referem aos Checkins não Realizados no dia, pois existe o agendamento, porém não consta no sistema, estes mesmos checkins realizados no dia de hoje até o horário da emissão deste relatório.</singleline></td>
                      </tr>
                      <tr>
                        <td height="15"></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <!--[if (gte mso 9)|(IE)]></td><td><![endif]-->
              <table width="200" class="table-full" border="0" align="right" cellpadding="0" cellspacing="0">
                <tr>
                  <td data-bgcolor="Total BG" align="center" bgcolor="#e1e6e7">
                    <table class="table-inner" width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
                      <tr>
                        <td height="10"></td>
                      </tr>
                      <tr>
                        <td data-size="Total Title" data-color="Total Text" mc:edit="invoice-18" style="font-family: "Open Sans", Arial, sans-serif; font-size:12px; color:#3b3b3b; line-height:26px; text-transform:uppercase;line-height:24px;">
                          <singleline label="title"><b>Total de Checkins</b></singleline>
                        </td>
                      </tr>
                      <tr height="30">
                    <td align="left">
                      <table data-width="Dash" align="left" width="50"  border="0" cellpadding="0" cellspacing="0">
                        <tbody><tr>
                          <td data-bgcolor="Dash" bgcolor="rgb(217, 127, 77)" height="3"></td>
                        </tr>
                      </tbody></table>
                    </td>
                  </tr>
                      <tr>
                        <td data-size="Total Sum" data-color="Total Text" mc:edit="invoice-19" style="font-family: "Open Sans", Arial, sans-serif; font-size:24px; color:#3b3b3b;  font-weight: bold;">
                          <singleline label="price"><b>'.$check_total.'</b> <span style="color: rgb(217, 127, 77); line-height: normal;">Não Realizados</span></singleline>
                        </td>
                      </tr>
                      <tr>
                        <td height="15"></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td height="15"></td>
          </tr>
        </table>
      </td>
    </tr>
  </table><table class="full" data-group="Invoice" data-thumbnail="http://www.stampready.net/dashboard/editor/user_uploads/zip_uploads/2019/12/17/Dg7YjPZcVKJS0Wko5vFmU6yq/All-in-one-1/thumbnails/invoice_footer.jpg" data-module="invoice_footer" data-bgcolor="Main BG" align="center" width="100%" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" style="background-color: rgb(248, 248, 248);">
    <tr>
      <td data-border-size="Footer Border" data-border-color="Footer Border" align="center" style="border-bottom:10px solid #ecf0f1;">
        <table width="700" style="max-width: 700px;" class="table-full" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td height="25"></td>
          </tr>
          <tr>
            <td>
              <!--left-->
              <table width="180" class="table-full" align="left" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center">
                    <table width="90%" align="center" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td align="center" style="line-height:0px;"><img data-color="Phone icon " editable="" label="image" mc:edit="invoice-22" style="display:block;font-size:0px; border:0px; line-height:0px;" src="http://www.stampready.net/dashboard/editor/user_uploads/zip_uploads/2019/12/17/Dg7YjPZcVKJS0Wko5vFmU6yq/All-in-one-1/images/phone.png" alt="img"></td>
                        <td data-size="Footer" data-color="Footer" mc:edit="invoice-23" style="font-family: "Open Sans", Arial, sans-serif; font-size:13px; color:#3b3b3b; line-height:26px; padding-left:15px;">
                          <singleline label="detail">+55 11 2463.7700</singleline>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <!--end left-->
              <!--[if (gte mso 9)|(IE)]></td><td><![endif]-->
              <table width="25" align="left" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="15"></td>
                </tr>
              </table>
              <!--[if (gte mso 9)|(IE)]></td><td><![endif]-->
              <!--middle-->
              <table width="180" class="table-full" align="left" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center">
                    <table width="90%" align="center" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td align="center" style="line-height:0px;"><img data-color="Mail icon" editable="" label="image" mc:edit="invoice-24" style="display:block;font-size:0px; border:0px; line-height:0px;" src="http://www.stampready.net/dashboard/editor/user_uploads/zip_uploads/2019/12/17/Dg7YjPZcVKJS0Wko5vFmU6yq/All-in-one-1/images/mail.png" alt="img"></td>
                        <td data-size="Footer" data-color="Footer" mc:edit="invoice-25" style="font-family: "Open Sans", Arial, sans-serif; font-size:13px; color:#3b3b3b; line-height:26px;padding-left: 15px;">
                          <singleline label="detail">falecom@step.eco.br</singleline>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <!--end middle-->
              <!--[if (gte mso 9)|(IE)]></td><td><![endif]-->
              <table width="25" align="left" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="15"></td>
                </tr>
              </table>
              <!--[if (gte mso 9)|(IE)]></td><td><![endif]-->
              <!--right-->
              <table width="180" class="table-full" align="right" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center">
                    <table class="table-inner" width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td data-size="Footer" data-color="Footer" mc:edit="invoice-26" class="btn-link-2" style="font-family: "Open Sans", Arial, sans-serif; font-size:13px; color:#3b3b3b; line-height:26px;">
                          <webversion>'.$data_atual.'</webversion> <span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                          <unsubscribe>'.$hora_atual.' hs</unsubscribe>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <!--end right-->
            </td>
          </tr>
          <tr>
            <td height="25"></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

</body>
</html>';




  
  //enviar
  
  // emails para quem será enviado o formulário
  $emailenviar = "falecom@step.eco.br";
  
  $destino = $email_ro. ', ';
  $destino .= $email_su;
  
  //$destino .='fabiano.barros@grupoep.com.br';
  $assunto = "Relatório de Checkin da Obra: $obra e Estação: $estacao";
  
  // É necessário indicar que o formato do e-mail é html
  $headers  = 'MIME-Version: 1.1' . "\r\n";
      $headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
      $headers .= 'From: STEP | GrupoEP <'.$emailenviar.'>';
      // para enviar a mensagem em prioridade máxima
  $headers .= "X-Priority: 1\n";
  
     // $headers .= "Bcc:fabiano.barros@grupoep.com.br"."\r\n";
  
  $enviaremail = mail($destino, $assunto, $mensagem, $headers);

  if($enviaremail){


    echo "email enviado para a Obra: <b>$obra</b> e Estação:  <b>$estacao</b> $destino\n <br/>";
  } else {


    echo "erro ao enviar email para a Obra: <b>$obra</b> e Estação:  <b>$estacao</b> $destino \n <br/> $";
  }

  

} // finaliza o foreach do envio em loop para cada estacao ativa


sleep(3);

function intervalo( $entrada, $saida ) {
  $entrada = explode( ':', $entrada );
  $saida   = explode( ':', $saida );
  $minutos = ( $saida[0] - $entrada[0] ) * 60 + $saida[1] - $entrada[1];
  if( $minutos < 0 ) $minutos += 24 * 60;
  return sprintf( '%d:%d', $minutos / 60, $minutos % 60 );
}

function mintohora($minutos)
{
$hora = floor($minutos/60);
$resto = $minutos%60;
return $hora.':'.$resto;
}



} //fecha o bloco do email se houver estacao
$conexao = null;













// function duas_casas(numero){
//     if (numero <= 9){
//         numero = "0"+numero;
//     }
//     return numero;
// }