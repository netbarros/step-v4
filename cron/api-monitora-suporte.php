
<?php

//INÍCIO DA 3ª FASE >> ===[ Suportes Aguardando ]===========================================>>>>>>>>>


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

ini_set('memory_limit', '-1');
require $_SERVER['DOCUMENT_ROOT'] . '/app/conexao.php';

if (!ini_get('date.timezone')) {
    date_default_timezone_set('America/Sao_Paulo');
}

$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();


$hr = date(" H ");
if ($hr >= 12 && $hr < 18) {
    $Saudacao = "Boa tarde!";
} else if ($hr >= 0 && $hr < 12) {
    $Saudacao = "Bom dia!";
} else {
    $Saudacao = "Boa noite!";
}

// Define o Período da Busca dos Dados
$Data_Atual_Periodo = date_create()->format('Y-m-d');
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-30 days', strtotime($Data_Atual_Periodo)));


$sql_consulta_suporte = $conexao->query("SELECT s.*, 

o.nome_obra,
e.id_estacao,
e.nome_estacao,
p.nome_ponto,
s.motivo_suporte,
s.id_rmm_suporte as id_rmm_suporte,
tps.nome_suporte,
colabSU.nome AS Nome_Supervisor,
colabRO.nome AS Nome_RO,
colabOP.nome AS Nome_Operador,
colabOP.cel_corporativo AS Tel_OP,
colabOP.email_corporativo AS Email_Operador,

colabSU.email_corporativo AS Email_Supervisor,
colabSU.cel_corporativo AS Tel_SU,

colabRO.email_corporativo AS Email_RO,
colabRO.cel_corporativo AS Tel_Ro,

uop.id AS ID_OP,
usu.id AS ID_SU,
uro.id AS ID_RO,
un.nome_unidade_medida,
pr.nome_parametro,
pr.concen_max,
pr.concen_min



FROM  suporte s

INNER JOIN
tipo_suporte tps ON tps.id_tipo_suporte = s.tipo_suporte
      INNER JOIN
      tipo_suporte_alertas tpas ON tpas.tipo_suporte = tps.id_tipo_suporte
      INNER JOIN 
    pontos_estacao p ON p.id_ponto = s.plcode
        LEFT JOIN 
        parametros_ponto pr ON pr.id_parametro = s.parametro
        LEFT JOIN 
        unidade_medida un ON un.id_unidade_medida = pr.unidade_medida
        INNER JOIN
    obras o ON o.id_obra = p.id_obra
        INNER JOIN
    estacoes e ON e.id_estacao = s.estacao
        INNER JOIN
    usuarios uop ON uop.id = s.quem_abriu
        INNER JOIN
    usuarios usu ON usu.bd_id = e.supervisor
        INNER JOIN
    usuarios uro ON uro.bd_id = e.ro
        LEFT JOIN
    colaboradores colabOP ON colabOP.id_colaborador = uop.bd_id
        LEFT JOIN
    colaboradores colabSU ON colabSU.id_colaborador = e.supervisor
        LEFT JOIN
    colaboradores colabRO ON colabRO.id_colaborador = e.ro
        LEFT JOIN
    log_leitura logl ON logl.chave_unica = s.chave_unica
        LEFT JOIN
    tipo_log tpl ON tpl.id_tipo_log = logl.tipo_log

WHERE s.status_suporte!='4' AND
DATE_FORMAT(s.data_open, '%Y-%m-%d') > '$Data_Intervalo_Periodo'
GROUP BY s.id_suporte
ORDER BY s.data_open ASC
");

$total_suporte = $sql_consulta_suporte->rowCount();

if ($total_suporte > 0) { // inicia a validação Suporte

    $resultado_suporte = $sql_consulta_suporte->fetchAll(PDO::FETCH_ASSOC);

    foreach ($resultado_suporte as $res) {



        $id_suporte = $res['id_suporte'];

        $tipo_suporte = $res['tipo_suporte'];

        $data_prevista = $res['data_prevista'];
        $data_suporte = $res['data_open'];
        $data_close = $res['data_close'];


        $hora_min =  date('H:i', strtotime($data_suporte));
        $dia_mes_ano =  date('d/m/Y', strtotime($data_suporte));

        $nome_obra = $res['nome_obra'];

        $nome_estacao = $res['nome_estacao'];


        $id_estacao = $res['id_estacao'];


        $nome_plcode = $res['nome_ponto'];

        $ID_OP = trim(isset($res['ID_OP'])) ? $res['ID_OP'] : '';

        $ID_RMM = trim(isset($res['id_rmm_suporte'])) ? $res['id_rmm_suporte'] : '';



        $nome_Operador = trim(isset($res['Nome_Operador'])) ? $res['Nome_Operador'] : '';
        $email_Operador = trim(isset($res['Email_Operador'])) ? $res['Email_Operador'] : '';
        $Tel_OP = trim(isset($res['Tel_OP'])) ? $res['Tel_OP'] : '';

        $ID_SU = trim(isset($res['ID_SU'])) ? $res['ID_SU'] : '';
        $nome_Supervisor = trim(isset($res['Nome_Supervisor'])) ? $res['Nome_Supervisor'] : '';
        $email_Supervisor = trim(isset($res['Email_Supervisor'])) ? $res['Email_Supervisor'] : '';
        $Tel_SU = trim(isset($res['Tel_SU'])) ? $res['Tel_SU'] : '';

        $ID_RO = trim(isset($res['ID_RO'])) ? $res['ID_RO'] : '';
        $nome_RO = trim(isset($res['Nome_RO'])) ? $res['Nome_RO'] : '';
        $email_RO = trim(isset($res['Email_RO'])) ? $res['Email_RO'] : '';
        $Tel_Ro = trim(isset($res['Tel_Ro'])) ? $res['Tel_Ro'] : '';

        $Tel_Ro = trim(isset($res['Tel_Ro'])) ? $res['Tel_Ro'] : '';

        $chave_unica_suporte = trim(isset($res['chave_unica'])) ? $res['chave_unica'] : '';


        $motivo_resolutiva = trim(isset($res['motivo_resolutiva'])) ? $res['motivo_resolutiva'] : '';

        $nome_suporte = $res['nome_suporte'];

        $status_suporte = $res['status_suporte'];

        $recebe_motivo_suporte = $res['motivo_suporte'];

        $Complemento_motivo = '';
        $nome_status_suporte = '';
        $envia_email = '';
        $leitura_suporte = '';



        if ($tipo_suporte == '1') {

            $ID_RMM = trim(isset($res['id_rmm_suporte'])) ? $res['id_rmm_suporte'] : '';

            if ($ID_RMM != '') {

                $pega_leitura = $conexao->query("SELECT leitura_entrada FROM rmm WHERE id_rmm='$ID_RMM'");
                $total_leituras_pegas = $pega_leitura->rowCount();
                $resultado_leituras_pegas = $pega_leitura->fetch(PDO::FETCH_OBJ);


                $leitura_suporte = trim(isset($resultado_leituras_pegas->leitura_entrada)) ? $resultado_leituras_pegas->leitura_entrada : '';
                $nome_parametro = trim(isset($res['nome_parametro'])) ? $res['nome_parametro'] : '';
                $nome_unidade_medida = trim(isset($res['nome_unidade_medida'])) ? $res['nome_unidade_medida'] : '';
                $concen_min = trim(isset($res['concen_min'])) ? $res['concen_min'] : '';
                $concen_max = trim(isset($res['concen_max'])) ? $res['concen_max'] : '';

                if ($total_leituras_pegas > 0) {
                    $Complemento_motivo .= "<br> <div
            style='font-family:Arial,Helvetica,sans-serif; line-height: 1.5; font-weight: normal; font-size: 14px; color: #2F3044; min-height: 100%; margin:0; padding:10px; width:100%; background-color:#edf2f7'>
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%'
                style='border-collapse:collapse;margin:0 auto; padding:0; max-width:600px'>
                                                        <thead>
                                                            <tr align='left' valign='center' style='text-align:center; padding: 40px'>
                                                                <th style='width='200px'>Indicador</th>
                                                                <th style='width='120px'>Parâmetro</th>
                                                                <th style='width='100px'>Leitura</th>
                                                            
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                            <tr >
                                                                <td align='left' valign='center'>
                                                                    <i
                                                                        style='border-bottom: 2px solid #eeeeee; margin: 2px 0'></i>$nome_parametro
                                                                </td>
                                                                <td style='font-size: 13px; text-align:center;padding: 20px; color: #00000;'>$concen_min <> $concen_max</td>
                                                                <td style='font-size: 13px; text-align:center;padding: 20px; color: #00000;'>$leitura_suporte  $nome_unidade_medida</td>
                                                                
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div><br>";
                }
            }
        }
        if ($tipo_suporte == '92') {


            if ($data_prevista != '') {

                $dtEntrega = date("Y-m-d", strtotime($data_prevista));
                $today = date("Y-m-d");

                if ($today >= $dtEntrega) {

                    $Complemento_motivo .= "Previsão da Liberação: " . date('d/m/Y H:i', strtotime($data_prevista));

                    $nome_status_suporte = "Em Atraso";
                } else {

                    $Complemento_motivo .= "Previsão da Liberação: " . date('d/m/Y H:i', strtotime($data_prevista));

                    $nome_status_suporte = "Em Andamento";
                }
            } else if ($status_suporte == '6') {

                $Complemento_motivo .= " O Indicador foi Revogado, porém ainda não foi dado previsão para a liberção do Suporte e da Leitura do Indicador, Favor Verificar o Motivo da Revogação";

                $nome_status_suporte = "Em Atraso";
            } else {

                $Complemento_motivo .= " O Operador Realizou a liberação do Indicador através da seguente resolutiva: " . $motivo_resolutiva;

                $nome_status_suporte = "Indicador Liberado pelo Operador";
            }
        }


        if ($status_suporte == "4") {
            $Complemento_motivo .=  date('d/m/Y H:i', strtotime($data_close));
            $nome_status_suporte = "Fechado";
        }

        if ($status_suporte == "1") {
            $envia_email = 'sim';

            $nome_status_suporte = "Em Aberto, aguardando análise do Supervisor.";
        }



        if ($status_suporte == '2') {
            $envia_email = 'sim';

            $dtEntrega = date("Y-m-d", strtotime($data_prevista));
            $today = date("Y-m-d");

            if ($today >= $dtEntrega) {

                $Complemento_motivo .= "Previsão: " . date('d/m/Y H:i', strtotime($data_prevista));

                $nome_status_suporte = "Em Atraso";
            } else {

                $Complemento_motivo .= "Previsão: " . date('d/m/Y H:i', strtotime($data_prevista));

                $nome_status_suporte = "Em Andamento";
            }
        }

        $motivo_suporte = $recebe_motivo_suporte . '<br>' . $Complemento_motivo;



        $checkTime = strtotime($data_suporte);
        $loginTime = strtotime($Data_Atual_Periodo);

        // $diff = $checkTime - $loginTime; // diferença entre hora do checkin e hora da leitura efetiva -> em segundos!

        $totalSecondsDiff = abs($checkTime - $loginTime);
        /*

$totalMinutesDiff = $totalSecondsDiff/60; //710003.75
$totalHoursDiff   = $totalSecondsDiff/60/60;//11833.39
$totalDaysDiff    = $totalSecondsDiff/60/60/24; //493.05
$totalMonthsDiff  = $totalSecondsDiff/60/60/24/30; //16.43
$totalYearsDiff   = $totalSecondsDiff/60/60/24/365; //1.35


outro
*/
        $prazo_decorrido = round($totalSecondsDiff / 60, 2); //710003.75
        $prazo_decorrido_horas =  round($totalSecondsDiff / 60 / 60, 2);

        $totalDaysDiff = $totalSecondsDiff / 60 / 60 / 24; //493.05


        if ($envia_email == 'sim') {

            if ($email_Supervisor != '') {
                
                $email_para = $email_Supervisor;
                $nome_para = $nome_Supervisor;
                $assunto= "Suporte em Aberto - $nome_suporte ";
                $email_usuario= $email_Supervisor;
                $nome_obra = $nome_obra;
                $nome_estacao = $nome_estacao;
                $nome_plcode = $nome_plcode;
                $nome_Operador = $nome_Operador;
                $dia_mes_ano = $dia_mes_ano;
                $hora_min = $hora_min;
                $mensagem_email = $motivo_suporte;
                $mensagem_email .= "<br>Detalhes:<br><br> Tipo de Suporte: $nome_suporte <br>Projeto: $nome_obra <br>Núcleo de Operação: $nome_estacao <br>Plcode: $nome_plcode <br>Operador: $nome_Operador <br><br> $dia_mes_ano às $hora_min";
                
                $nome_suporte = $nome_suporte;
                $nome_status_suporte = $nome_status_suporte;
                $chave_unica = $chave_unica_suporte;
             //=====[ Inicio da classe envia email]=====================<<
             $template_email = '/views/emails/email-padrao.php';
    
             require_once  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';
         
           
             //=====[ final da classe envia email]=====================<<
                //=====[ final da classe envia email]=====================<<
            }

            if ($email_RO != '') {

                $email_para = $email_RO;
                $nome_para = $nome_RO;
                $assunto= "Suporte em Aberto - $nome_suporte";
                $mensagem_email = "Detalhes:<br><br> Tipo de Suporte: $nome_suporte <br>Projeto: $nome_obra <br>Núcleo de Operação: $nome_estacao <br>Plcode: $nome_plcode <br>Operador: $nome_Operador <br><br> $dia_mes_ano às $hora_min";
                $email_usuario= $email_RO;
                $nome_obra = $nome_obra;
                $nome_estacao = $nome_estacao;
                $nome_plcode = $nome_plcode;
                $nome_usuario = $nome_RO;
                $dia_mes_ano = $dia_mes_ano;
                $hora_min = $hora_min;
                $mensagem_email .= $motivo_suporte;
                $nome_suporte = $nome_suporte;
                $nome_status_suporte = $nome_status_suporte;
                $chave_unica = $chave_unica_suporte;

                 //=====[ Inicio da classe envia email]=====================<<
                $template_email = '/views/emails/email-padrao.php';
    
                require_once  $_SERVER['DOCUMENT_ROOT'] . '/cron/envia-email.php';
            
               
                //=====[ final da classe envia email]=====================<<
            }
        }
    } // fecha foreach suporte

} //fecha consulta


//FIM DA 3ª FASE >> ===[ Suportes Aguardando ]===========================================<<<<<<