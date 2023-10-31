<?php 
header("Content-Type: multipart/form-data; charset=utf-8");
//====[ monta tabela dos checkins não realizados no dia verificado]====
$seleciona_estacao = $conexao->query("SELECT e.id_estacao,e.nome_estacao,o.nome_obra FROM estacoes e

INNER JOIN obras o ON o.id_obra = e.id_obra
INNER JOIN periodo_ponto p ON e.id_estacao =  p.id_estacao

WHERE e.status_estacao='1' Group By e.id_estacao");

$dados_estacao= $seleciona_estacao->fetchAll(PDO::FETCH_ASSOC);
$total_estacao = $seleciona_estacao->rowCount ();

//echo $total_estacao;




if($total_estacao>0){

$hora_lida="";
$check='';
$tabela="";     


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
  WHERE e.id_estacao = '$id_estacao'
  
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
   
    $sql_periodo1 = $conexao->query("SELECT periodo_ponto.*,
        pontos_estacao.nome_ponto,
        pontos_estacao.id_ponto,
        periodo_ponto.tipo_checkin,
        pontos_estacao.controla_periodo_ponto, 
        pontos_estacao.status_ponto,
        parametros_ponto.id_parametro,
        parametros_ponto.nome_parametro,
        periodo_dia_ponto.dia_semana,
        dia_semana.nome_dia_semana,
        checkin.hora_lida,
        checkin.hora_leitura,
        checkin.status_checkin,
        checkin.chave_unica,
        checkin.data_cadastro_checkin
     
      FROM periodo_ponto
    INNER JOIN pontos_estacao ON pontos_estacao.id_ponto = periodo_ponto.id_ponto
    LEFT JOIN checkin ON checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto 
    LEFT JOIN periodo_dia_ponto ON periodo_dia_ponto.id_ponto = periodo_ponto.id_ponto
    LEFT JOIN dia_semana ON dia_semana.representa_php=periodo_dia_ponto.dia_semana
    LEFT JOIN parametros_ponto on parametros_ponto.id_parametro =periodo_ponto.id_parametro Where
    periodo_ponto.id_periodo_ponto not in  ( SELECT  checkin.id_periodo_ponto FROM  checkin WHERE checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto
     AND DATE_FORMAT(checkin.data_cadastro_checkin, '%Y-%m-%d' )  >= '$Data_Intervalo_Periodo') 
    AND periodo_ponto.tipo_checkin = 'ponto_plcode' AND periodo_ponto.id_estacao ='$id_estacao' GROUP BY periodo_ponto.id_periodo_ponto ORDER BY periodo_ponto.hora_leitura ASC"  );
    
    
    //===> exceto se constar o id_periodo_ponto na tabela checkin, este não traz, pq já foi feito o check
    
    
    $total = $sql_periodo1->rowCount ();
    
      
       // $registro = $sql_periodo->fetch(PDO::FETCH_OBJ);
        
       



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


            $hora_lida = $res['hora_lida'] ? $res['hora_lida'] : '';

            $hora_leitura = $res['hora_leitura'] ? $res['hora_leitura'] : '';

	
            $leitura = new DateTime($hora_leitura);
            $minima = new DateTime($hora_leitura);
            $minima->sub(new DateInterval('PT1M')); // subtrai 30 minutos do periodo da leitura
            $now = new DateTime($hora_lida);

         if(isset($hora_leitura)){
	
              $leitura = new DateTime($hora_leitura);
              $minima = new DateTime($hora_leitura);
              $minima->sub(new DateInterval('PT1M')); // subtrai 30 minutos do periodo da leitura
              $now = new DateTime($hora_lida);
  
              if ( $minima < $now  ) { 
                $status = "Não Realizado";
                $check.='1;';
                $css_status='style="color: rgb(217, 127, 77);"';
            }   if ( $minima > $now  ) {  $status = "em tempo"; $css_status='style="color: rgb(118, 172, 152);"';}
  
            
            $saida =  substr($hora_leitura, 0,5);
            $entrada   = substr($hora_lida, 0,5);
  
          }


            $tabela.='<tr> <td   align="left" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
            <singleline label="list-1"> '.$res['nome_ponto'].' </singleline>
            </td>
            <td    align="left" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
            <singleline label="list-2">'.$res['nome_parametro'].'</singleline>
            </td>
            <td   align="center" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
            <singleline label="list-3"> <b>'.$saida.'</b>  '. $ciclo.' </singleline>
            </td>
           
            <td    align="right" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size: 14px;  line-height: 26px; font-weight: bold;">
            <singleline label="list-4" '.$css_status.'>'. $status .'</singleline>
            </td>
            </tr>';

            $tabela.='<tr>
            <td  height="5" style="border-bottom:1px solid #ecf0f1;"></td>
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
          $entrada   = substr($hora_lida, 0,5);
      
         // $prazo= intervalo($entrada, $saida) ;
      
         

        } else { $status = "Não Realizado"; $css_status='style="color: rgb(217, 127, 77);"'; $check.='1;';}

            $ciclo_leitura = $res['ciclo_leitura']; 

            if($ciclo_leitura=='1'){
                $ciclo="<br/>Diário";

            } else { $ciclo="Semanal";}


            $tabela.='<tr> <td   align="left" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
                    <singleline label="list-1"> '.$res['nome_ponto']. ' </singleline>
                    </td>
                    <td    align="left" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
                    <singleline label="list-2"> -- </singleline>
                    </td>
                    <td   align="center" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
                    <singleline label="list-3">'. $ciclo.' <span style="color: rgb(55,110,00); font-weight: bold;"> <span style="color: rgb(55,110,00); font-weight: bold;"> Presencial </span> </span></singleline>
                    </td>
                   
                    <td    align="right" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size: 14px; color: rgb(213, 78, 72); line-height: 26px; font-weight: bold;">
                    <singleline label="list-4"  "><span '.$css_status.'>'. $status .'</span></singleline>
                    </td>
                    </tr>';

                    $tabela.='<tr>
                    <td  height="5" style="border-bottom:1px solid #ecf0f1;"></td>
                  </tr>';


           

        }// fecha lista check in presencial sem controle de periodo
    
    }


}



//=========================================================


//consulta_checkin_leitura

//======>>>>>>>>>>>>>>>>>> Não exibir os Checkins já existentes na tabela checkin (Where Not In Checkin)

    
    $sql_periodo2 = $conexao->query("SELECT periodo_ponto.*,pontos_estacao.nome_ponto, pontos_estacao.id_ponto, periodo_ponto.tipo_checkin,pontos_estacao.controla_periodo_ponto, pontos_estacao.status_ponto,parametros_ponto.id_parametro,parametros_ponto.nome_parametro,periodo_dia_ponto.dia_semana,dia_semana.nome_dia_semana, checkin.data_cadastro_checkin FROM periodo_ponto
    INNER JOIN pontos_estacao ON pontos_estacao.id_ponto = periodo_ponto.id_ponto
    LEFT JOIN checkin ON checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto 
    LEFT JOIN periodo_dia_ponto ON periodo_dia_ponto.id_ponto = periodo_ponto.id_ponto
    LEFT JOIN dia_semana ON dia_semana.representa_php=periodo_dia_ponto.dia_semana
    LEFT JOIN parametros_ponto on parametros_ponto.id_parametro =periodo_ponto.id_parametro Where
    periodo_ponto.id_periodo_ponto not in  ( SELECT  checkin.id_periodo_ponto FROM  checkin WHERE checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto AND DATE_FORMAT(checkin.data_cadastro_checkin, '%Y-%m-%d')  > '$Data_Intervalo_Periodo') 
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

             $hora_lida = $res['hora_lida'] ? $res['hora_lida'] : '';


            if(isset($hora_leitura)){
	
            $leitura = new DateTime($hora_leitura);
            $minima = new DateTime($hora_leitura);
            $minima->sub(new DateInterval('PT1M')); // subtrai 30 minutos do periodo da leitura
            $now = new DateTime($hora_lida);

            if ( $minima < $now  ) { 
              $status = "Não Realizado";
              $check.='1;';
              $css_status='style="color: rgb(217, 127, 77);"';
          }   if ( $minima > $now  ) {  $status = "em tempo"; $css_status='style="color: rgb(118, 172, 152);"';}

          //$prazo="";

          $saida =  substr($hora_leitura, 0,5);
          $entrada   = substr($hora_lida, 0,5);
      
         // $prazo= intervalo( $entrada, $saida ) ;
      
         

        }
           
          //$hora_lidax = $res['hora_lida'] ? $res['hora_lida'] : '';

         
           
    
            $tabela.='<tr> <td   align="left" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
            <singleline label="list-1"> '.$res['nome_ponto'].' </singleline>
            </td>
            <td    align="left" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
            <singleline label="list-2">'.$res['nome_parametro'].'</singleline>
            </td>
            <td   align="center" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">
            <singleline label="list-3"><strong>'.$saida.'</strong>  <span '.$css_status.'>'. $ciclo.' </span> '.$nome_dia_semana_periodo.'</singleline>
            </td>
           
            <td    align="right" valign="top" style="font-family: "Open Sans", Arial, sans-serif; font-size: 14px; color: rgb(213, 78, 72); line-height: 26px; font-weight: bold;">
            <singleline label="list-4" "><span '.$css_status.'>'. $status .'</span> </singleline>
            </td>
            </tr>';   
            
            $tabela.='
            <td  height="5" style="border-bottom:1px solid #ecf0f1;"></td>
          ';

          $tabela.=' 
                        <td  style="font-family: "Open Sans", Arial, sans-serif; font-size:24px; color:#3b3b3b;  font-weight: bold;">
                          <singleline label="total não realizados"><b>'.$check_total.'</b> <span style="color: rgb(217, 127, 77); line-height: normal;">Não Realizados</span></singleline>
                        </td>
                      ;';


      

        }



 }


$array = explode(';', $check);
$check_total = array_sum($array);



// depois de montar o templete com as variaveis no arquivo enviar-email-checkin, coloco o include aqui e apago a estrutuda de html e email, pq já vira 

//=== $tabela (com o resumo dos dados dos chekcins não realizados no dia)

  

        } // finaliza o foreach do envio em loop para cada estacao ativa

        // resgata os dados e monta a tabela => echo $tabela;

    }// fecha a validação das estações ativas




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