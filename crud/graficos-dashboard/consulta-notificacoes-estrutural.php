<?php
ob_end_clean();
// Instancia Conexão PDO
if (!isset($_SESSION)) session_start();
require_once "../../conexao.php";
$conexao = Conexao::getInstance();
require_once "./../../crud/login/verifica_sessao.php";

$_SESSION['pagina_atual'] = 'Dashboard Usuários';

$usuario_sessao = isset($_SESSION['id']) ?? '';

//Tubulação com Problemas 88
//Equipamento da Operação Danificado 89
//Instrumento de Medição Danificado 90


$sql = $conexao->query("SELECT s.*, ts.nome_suporte,p.nome_ponto, e.nome_estacao FROM suporte s
INNER JOIN estacoes e ON e.id_estacao = s.estacao
LEFT JOIN pontos_estacao p ON p.id_ponto = s.plcode
INNER JOIN tipo_suporte ts ON ts.id_tipo_suporte = s.tipo_suporte
INNER JOIN suporte_conversas sc ON sc.id_suporte = s.id_suporte
LEFT JOIN suporte_destinatarios sd ON sd.id_suporte_conversas = sc.id_conversa 
WHERE  s.status_suporte!='4' AND ( s.tipo_suporte='88' OR s.tipo_suporte='89' OR s.tipo_suporte='90') AND (sc.destinatario_direto = '$usuario_sessao' OR sd.id_destinatario= '$usuario_sessao')

");
                                                    $conta = $sql->rowCount();


                                                    if ($conta > 0) {

                                                        $row = $sql->fetchALL(PDO::FETCH_ASSOC);


                                                       

                                                        foreach($row as $r){


                                                            $status_suporte=$r['status_suporte'];


                                                            switch ($status_suporte) {
                                                                case '1':
                                                                   
                                                                    $css = 'danger';
                                                                    break;

                                                                case '2':
                                                               
                                                                $css = 'warning';
                                                                break;

                                                                case '3':
                                                              
                                                                    $css = 'info';
                                                                    break;

                                                                case '4':
                                                              

                                                                    $css = 'success';
                                                                    break;

                                                                    case '6':
                                                              

                                                                        $css = 'dark';
                                                                        break;

                                                                        case '7':
                                                              

                                                                            $css = 'primary';
                                                                            break;

                                                                default:
                                                                    # code...
                                                                    $css = 'dark';
                                                                    break;
                                                            }

                                                            
// Obtém a data atual
$data_atual = new DateTime();

// Obtém a data da variável "data_open"
$data_open = new DateTime($r['data_open']); // Supondo que a variável "data_open" já tenha sido definida

// Calcula a diferença entre as datas
$diferenca = date_diff($data_atual, $data_open);

// Exibe a diferença em horas
$tempo_em_aberto =  $diferenca->format('%a dias, %h horas');



?>

   <!--begin::Item-->
   <div class="d-flex flex-stack py-4">
                                        <!--begin::Section-->
                                        <div class="d-flex align-items-center">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-35px me-4">
                                                <span class="symbol-label bg-light-<?=$css;?>">
                                                    <!--begin::Svg Icon | path: icons/duotune/technology/teh008.svg-->
                                                    <span class="svg-icon svg-icon-2 svg-icon-<?=$css;?>">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.3" d="M11 6.5C11 9 9 11 6.5 11C4 11 2 9 2 6.5C2 4 4 2 6.5 2C9 2 11 4 11 6.5ZM17.5 2C15 2 13 4 13 6.5C13 9 15 11 17.5 11C20 11 22 9 22 6.5C22 4 20 2 17.5 2ZM6.5 13C4 13 2 15 2 17.5C2 20 4 22 6.5 22C9 22 11 20 11 17.5C11 15 9 13 6.5 13ZM17.5 13C15 13 13 15 13 17.5C13 20 15 22 17.5 22C20 22 22 20 22 17.5C22 15 20 13 17.5 13Z" fill="currentColor" />
                                                            <path d="M17.5 16C17.5 16 17.4 16 17.5 16L16.7 15.3C16.1 14.7 15.7 13.9 15.6 13.1C15.5 12.4 15.5 11.6 15.6 10.8C15.7 9.99999 16.1 9.19998 16.7 8.59998L17.4 7.90002H17.5C18.3 7.90002 19 7.20002 19 6.40002C19 5.60002 18.3 4.90002 17.5 4.90002C16.7 4.90002 16 5.60002 16 6.40002V6.5L15.3 7.20001C14.7 7.80001 13.9 8.19999 13.1 8.29999C12.4 8.39999 11.6 8.39999 10.8 8.29999C9.99999 8.19999 9.20001 7.80001 8.60001 7.20001L7.89999 6.5V6.40002C7.89999 5.60002 7.19999 4.90002 6.39999 4.90002C5.59999 4.90002 4.89999 5.60002 4.89999 6.40002C4.89999 7.20002 5.59999 7.90002 6.39999 7.90002H6.5L7.20001 8.59998C7.80001 9.19998 8.19999 9.99999 8.29999 10.8C8.39999 11.5 8.39999 12.3 8.29999 13.1C8.19999 13.9 7.80001 14.7 7.20001 15.3L6.5 16H6.39999C5.59999 16 4.89999 16.7 4.89999 17.5C4.89999 18.3 5.59999 19 6.39999 19C7.19999 19 7.89999 18.3 7.89999 17.5V17.4L8.60001 16.7C9.20001 16.1 9.99999 15.7 10.8 15.6C11.5 15.5 12.3 15.5 13.1 15.6C13.9 15.7 14.7 16.1 15.3 16.7L16 17.4V17.5C16 18.3 16.7 19 17.5 19C18.3 19 19 18.3 19 17.5C19 16.7 18.3 16 17.5 16Z" fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </span>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Title-->
                                            <div class="mb-0 me-2">
                                                <a href="javascript:;" class="fs-6 text-gray-800 text-hover-<?=$css;?> fw-bold"><?=$r['nome_suporte']; ?> </a>
                                                <div class="text-gray-400 fs-7"><?=$r['nome_estacao'];?> | <?=$r['nome_ponto']??'';?>
                                                </div>
                                            </div>
                                            <!--end::Title-->
                                        </div>
                                        <!--end::Section-->
                                        <!--begin::Label-->
                                        <span class="badge badge-light fs-8"><?=$tempo_em_aberto;?></span>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Item-->


<?php
 }


                                                       
}else {


    echo '<div class="d-flex flex-stack py-4">
    <!--begin::Section-->
    <div class="d-flex align-items-center"><b>Nenhuma Notificação de Estrutural para:<br><br>
    - Tubulação com Problemas <br>
    - Equipamento da Operação Danificado <br>
    - Instrumento de Medição Danificado <br></b>
    </div></div>';
}

?>