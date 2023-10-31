<?php 
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header("Access-Control-Max-Age: 3600");


// Considero que já existe um autoloader compatível com a PSR-4 registrado
//header("Content-Type: application/json");

  /*
 define('SGBD', 'mysql');
 define('HOST', '162.241.99.91'); //localhost
 define('DBNAME', 'step_bd'); //step
 define('CHARSET', 'utf8');
 define('USER', 'step_root');
 define('PASSWORD', 'F@087913');
 define('SERVER', 'linux');
 define('PORT', '3306');


 define('HOST', 'localhost');
define('DBNAME', 'step_bd');
define('CHARSET', 'utf8');
define('USER', 'root');
define('PASSWORD', '');
define('PORT', '3306');
 */

 function getConnection() {
    $dbhost="172.25.2.3";
    $dbuser="root";
    $dbpass="v3irfF1hG9pLm8r8";
     $dbname="step_bd";
     $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
     $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}

ini_set('memory_limit', '-1');
// Atribui uma conexão PDO
date_default_timezone_set('America/Sao_Paulo');

$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();



// pega hora atual php
$hora_atual = date('H:i');
$data_atual = date('d/m/Y');

$hr = date(" H ");
if($hr >= 12 && $hr<18) {
$Saudacao = "Boa tarde!";}
else if ($hr >= 0 && $hr <12 ){
$Saudacao = "Bom dia!";}
else {
$Saudacao = "Boa noite!";}



//consulta_checkin_presencial

    $data_atual_periodo= date_create()->format('Y-m-d');
    $data_intervalo_periodo=date('Y-m-d', strtotime('-1 day', strtotime($data_atual_periodo)));

   
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

       checkin.data_cadastro_checkin FROM periodo_ponto

    INNER JOIN 
    pontos_estacao ON pontos_estacao.id_ponto = periodo_ponto.id_ponto
    
    INNER JOIN    
    pontos_estacao p ON p.id_ponto = ch.id_ponto
        INNER JOIN
    obras o ON o.id_obra = p.id_obra
        INNER JOIN
    estacoes e ON e.id_estacao = ch.id_estacao

            INNER JOIN
    usuarios uop ON uop.id = ch.id_colaborador
        LEFT JOIN
    usuarios usu ON usu.bd_id = e.supervisor
        INNER JOIN
    usuarios uro ON uro.bd_id = e.ro
    LEFT JOIN 
    checkin ON checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto 
    LEFT JOIN 
    periodo_dia_ponto ON periodo_dia_ponto.id_ponto = periodo_ponto.id_ponto
    LEFT JOIN 
    dia_semana ON dia_semana.representa_php=periodo_dia_ponto.dia_semana
    LEFT JOIN 
    parametros_ponto on parametros_ponto.id_parametro =periodo_ponto.id_parametro Where
    periodo_ponto.id_periodo_ponto not in  ( SELECT  checkin.id_periodo_ponto FROM  checkin WHERE checkin.id_periodo_ponto = periodo_ponto.id_periodo_ponto 
    AND DATE_FORMAT(checkin.data_cadastro_checkin, '%Y-%m-%d' )  = '$data_intervalo_periodo') 
   AND periodo_ponto.id_estacao ='$id_estacao ' GROUP BY periodo_ponto.id_periodo_ponto ORDER BY periodo_ponto.hora_leitura ASC"  );
    
    
    //===> exceto se constar o id_periodo_ponto na tabela checkin, este não traz, pq já foi feito o check
    
    
    $total = $sql_periodo1->rowCount ();
    
      
       // $registro = $sql_periodo->fetch(PDO::FETCH_OBJ);
        
       

       
       
            if($total>0){


        $tabela="";
        $retorno_alerta='';


                    foreach ($sql_periodo1 as $res) {


                $ID_Checkin = trim(isset($res['ID_Checkin'])) ? $res['ID_Checkin'] : '';

                $ID_RMM_Checkin = trim(isset($res['ID_Checkin'])) ? $res['ID_RMM_Checkin'] : '';

                $Chave_Unica_Rmm = trim(isset($res['Chave_Unica_Rmm'])) ? $res['Chave_Unica_Rmm'] : '';

                $Chave_Unica_Checkin = trim(isset($res['Chave_Unica_Checkin'])) ? $res['Chave_Unica_Checkin'] : '';

                $ID_Perido_Checkin = trim(isset($res['ID_Perido_Checkin'])) ? $res['ID_Perido_Checkin'] : '';

                $Hora_Checkin_Realizado = trim(isset($res['Hora_Checkin_Realizado'])) ? $res['Hora_Checkin_Realizado'] : '';

                $Hora_Leitura_Agendada = trim(isset($res['Hora_Leitura_Agendada'])) ? $res['Hora_Leitura_Agendada'] : '';

                $Data_Checkin = trim(isset($res['Data_Checkin'])) ? $res['Data_Checkin'] : '';

                $unidade_medida_lida = trim(isset($res['nome_unidade_medida'])) ? $res['nome_unidade_medida'] : ''; 

                $nome_plcode = $res['nome_ponto'];

                $id_ponto = $res['id_ponto'];

                $id_operador = $res['ID_Operador_Checkin'];

                $id_parametro = trim(isset($res['id_parametro'])) ? $res['id_parametro'] : '0'; 

                $nome_parametro = trim(isset($res['nome_parametro'])) ? $res['nome_parametro'] : ''; 

                $nome_obra = $res['nome_obra'];
                
                $nome_estacao = $res['nome_estacao'];

                $id_estacao = $res['id_estacao'];

                $Latitude_Ponto = trim(isset($res['Latitude_Ponto'])) ? $res['Latitude_Ponto'] : ''; 

                $Longitude_Ponto = trim(isset($res['Longitude_Ponto'])) ? $res['Longitude_Ponto'] : ''; 

                $Latitude_Operador_Checkin = trim(isset($res['Latitude_Operador_Checkin'])) ? $res['Latitude_Operador_Checkin'] : ''; 

                $Longitude_Operador_Checkin = trim(isset($res['Longitude_Operador_Checkin'])) ? $res['Longitude_Operador_Checkin'] : ''; 
                
                

                 $origem_Leitura = trim(isset($res['origem_leitura_parametro'])) ? $res['origem_leitura_parametro'] : ''; 

                $tipo_checkin =  trim(isset($res['tipo_checkin'])) ? $res['tipo_checkin'] : ''; //  'ponto_parametro',  'ponto_plcode'

                $ciclo_leitura = trim(isset($res['ciclo_leitura'])) ? $res['ciclo_leitura'] : '';  // 1 = diário, 2 = semanal

                 $modo_checkin_periodo = trim(isset($res['Modo_Checkin_Agendado'])) ? $res['Modo_Checkin_Agendado'] : '';  // 1 = Livre , 2 = Horário Controlado (Agendado)

                $ID_OP = trim(isset($res['ID_OP'])) ? $res['ID_OP'] : '';
                $nome_Operador = trim(isset($res['Nome_Operador'])) ? $res['Nome_Operador'] : '';
                $email_Operador = trim(isset($res['Email_Operador'])) ? $res['Email_Operador'] : '';
                $Tel_OP = trim(isset($res['Tel_OP'])) ? $res['Tel_OP'] : '';

                $ID_SU = trim(isset($res['ID_SU'])) ? $res['ID_SU'] : '';
                $nome_Supervisor = trim(isset($res['Nome_Supervisor'])) ? $res['Nome_Supervisor'] : '';
                $email_Supervisor = trim(isset($res['Email_Supervisor'])) ? $res['Email_Supervisor'] : '';
                $Tel_SU = trim(isset($res['Tel_SU'])) ? $res['Tel_SU'] : '';

                $ID_RO = trim(isset($res['ID_RO'])) ? $res['ID_RO'] : '';
                $nome_RO= trim(isset($res['Nome_RO'])) ? $res['Nome_RO'] : '';
                $email_RO = trim(isset($res['Email_RO'])) ? $res['Email_RO'] : '';
                $Tel_Ro = trim(isset($res['Tel_Ro'])) ? $res['Tel_Ro'] : '';

                  // Reserva as variaveis apra comparação e preenchimento do envio dos alertas, caso seja necessário
                // trata e desmembra a data da leitura //
                $data_leitura = $res['Data_Checkin'];
                //$data_leitura =  strtotime($phpdate) * 1000;
                $hora_min =  date('H:i', strtotime($data_leitura));
                $dia_mes_ano =  date('d/m/Y', strtotime($data_leitura));
                //====<< 

                // trata e separa a leitura em colunia unica, inpependente se é entrada ou saida, para amostragem
                // Operadores Ternários para Parametros e seus derivados acontece, por haver leitura de RMM/Checkin apenas com o PLCode.
               
                    $dias_semana_periodo_="";

                    $nome_dia_semana_periodo = "";

                    $diasemana_numero = date('w', time());

                    

                    
                 if($tipo_checkin=='2'){

                if($origem_Leitura=='1'){
                $leitura_rmm_checkin = $res['leitura_entrada'];
                }

                if($origem_Leitura=='2'){
                $leitura_rmm_checkin = $res['leitura_entrada'];

                }
             if($origem_Leitura!='1' && $origem_Leitura!='2'){

                $leitura_rmm_checkin = $res['leitura_entrada'];

                }

                } else  {

                     $leitura_rmm_checkin = "Checkin Presencial";
                }


 if($envia_email=='1'){

                    if($email_Supervisor!=""){ //email para o Supervisor

                       $mensagem_alerta = "Olá ".$nome_Supervisor." . <br> ".$retorno_alerta." <br> <br> <b>Obra:</b> ".$nome_obra." <br><b>Estação:</b> ".$nome_estacao."<br><b> PLCODE:</b> ".$nome_plcode.".<br>
                        <b>Operador:</b> " . $nome_Operador . ".";

                    $email_para = $email_Supervisor;
                    $nome_para = $nome_Supervisor;

                    // Chama a function que mont aos checkins não realizados para o dia agendado //
                  
/*  //=====[ Inicio da tabela de checkin]=====================<<

 
  
include  $_SERVER['DOCUMENT_ROOT'].'/app/crud/tabela-checkin-nao-realizado.php';     
//=====[ final da tabela de checkin]=====================<< */

                    // fecha functiuon
                  
//=====[ Inicio da classe envia email]=====================<<
  
require_once  $_SERVER['DOCUMENT_ROOT'].'/app/crud/enviar-email-checkin.php';     
//=====[ final da classe envia email]=====================<<


                    } // finaliza envio de email para Supervisor

                    

                    if($email_RO!=""){ //inicia email para o RO


                        $mensagem_alerta = "Olá ".$nome_RO." . <br> ".$retorno_alerta."<br> <br><b>Obra:</b> ".$nome_obra."<br> <b>Estação:</b> ".$nome_estacao." <br><b>PLCODE:</b> ".$nome_plcode.".<br>
                        <b>Operador:</b> " . $nome_Operador . ".";

    
                    $email_para = $email_RO;
                    $nome_para = $nome_RO;

                  
/*  //=====[ Inicio da tabela de checkin]=====================<<

 
  
include  $_SERVER['DOCUMENT_ROOT'].'/app/crud/tabela-checkin-nao-realizado.php';     
//=====[ final da tabela de checkin]=====================<< */
                    // fecha functiuon            

      //=====[ Inicio da classe envia email]=====================<<
  
require_once  $_SERVER['DOCUMENT_ROOT'].'/app/crud/enviar-email-checkin.php';    
//=====[ final da classe envia email]=====================<<


                    } // finaliza envio de email para RO
 

                    if($email_Operador!=""){ //inicia email para o Operador


                   
                        $mensagem_alerta = "Olá ".$nome_Operador." . <br> ".$retorno_alerta." <br><br><b>Obra:</b> ".$nome_obra." <br><b>Estação:</b> ".$nome_estacao." <br> <b>PLCODE:<b> ".$nome_plcode.".<br>
                        <b>Operador:</b> " . $nome_Operador . ".";

        
                    $email_para = $email_Operador;
                    $nome_para = $nome_Operador;

                               
/*  //=====[ Inicio da tabela de checkin]=====================<<

 
  
include  $_SERVER['DOCUMENT_ROOT'].'/app/crud/tabela-checkin-nao-realizado.php';     
//=====[ final da tabela de checkin]=====================<< */

                    // fecha functiuon

//=====[ Inicio da classe envia email]=====================<<
  
require_once  $_SERVER['DOCUMENT_ROOT'].'/app/crud/enviar-email-checkin.php';    
//=====[ final da classe envia email]=====================<<

                    } // finaliza envio de email para Operador


                     

                }




                    }// fecha o foreach 




            }

 // finaliza o foreach do envio em loop para cada estacao ativa




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














// function duas_casas(numero){
//     if (numero <= 9){
//         numero = "0"+numero;
//     }
//     return numero;
// }