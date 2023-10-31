<?php
// Atribui uma conexão PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/crud/login/verifica_sessao.php';

$_SESSION['pagina_atual'] = 'Dashboard Usuários';

$usuario_sessao = isset($_SESSION['id']) ?? '';
$nivel_acesso_user_sessao = trim(isset($_COOKIE['nivel_acesso_usuario'])) ? $_COOKIE['nivel_acesso_usuario'] : '';
$id_tabela_cliente_sessao = trim(isset($_COOKIE['id_tabela_cliente'])) ? $_COOKIE['id_tabela_cliente'] : '';
$id_BD_Colaborador = trim(isset($_SESSION['bd_id'])) ? $_SESSION['bd_id'] : '';


$projeto_atual = (isset($_COOKIE['projeto_atual'])) ? $_COOKIE['projeto_atual'] : '';

if ($projeto_atual != '') {


    $filtro = "AND o.id_obra ='$projeto_atual ' GROUP BY p.id_ponto";
} else {

    $filtro = "GROUP BY o.id_obra";
}

$sql_personalizado = '';


$id_usuario_sessao = trim(isset($_COOKIE['id_usuario_sessao'])) ? $_COOKIE['id_usuario_sessao'] : '';
if ($nivel_acesso_user_sessao == 'supervisor') {

    $sql_personalizado = "AND (e.supervisor = '$id_BD_Colaborador'  OR up.id_usuario  = '$id_usuario_sessao')";
}

if ($nivel_acesso_user_sessao == 'ro') {

    $sql_personalizado = "AND (e.ro = '$id_BD_Colaborador'  OR up.id_usuario  = '$id_usuario_sessao')";
}

if ($nivel_acesso_user_sessao == 'cliente') {

    $sql_personalizado = "AND (o.id_cliente = '$id_tabela_cliente_sessao'  OR up.id_usuario  = '$id_usuario_sessao')";
}


/*
salvo e resgato os itens no coockie para poder personalisar os graficos e informações espscíficas do cliente.

*/
// Data consulta global do dashboard //
$Data_Atual_Periodo = date_create()->format('Y-m-d ');
// == a data está pegando desde 2019, assim que o módulo for liberado, alterar a data de invervalo para -7 dias entre -14 dias
$Data_Intervalo_Periodo = date('Y-m-d', strtotime('-190 days', strtotime($Data_Atual_Periodo)));
$Data_presente = date('Y-m-d ', strtotime('24 hours', strtotime($Data_Atual_Periodo)));
$Data_passado = date('Y-m-d', strtotime('-1 days', strtotime($Data_Atual_Periodo)));

$Data_7_dias_antes = date('Y-m-d ', strtotime('-7 days', strtotime($Data_Atual_Periodo)));
$Data_14_dias_antes = date('Y-m-d', strtotime('-14 days', strtotime($Data_Atual_Periodo)));


$sql_tab_suporte = $conexao->query("SELECT s.*, o.id_obra,o.nome_obra, e.nome_estacao, ts.nome_suporte FROM suporte s
                                            INNER JOIN
                                            tipo_suporte ts ON ts.id_tipo_suporte = s.tipo_suporte
                                            LEFT JOIN estacoes e ON e.id_estacao = s.estacao
                                            INNER JOIN obras o ON o.id_obra = e.id_obra
                                            LEFT JOIN pontos_estacao p ON p.id_ponto = s.plcode
                                    LEFT JOIN
                                           usuarios_projeto up ON up.id_obra = e.id_obra
                                            WHERE s.status_suporte != '4'
                                            $sql_personalizado

                                            $filtro


                                            ORDER BY s.data_open ASC LIMIT 0,10

                                            ");

$conta_suporte = $sql_tab_suporte->rowCount();

if ($conta_suporte > 0) {

    echo '<!--begin::Table-->
    <table class="table table-row-dashed align-middle gs-0 gy-4">';


    $row = $sql_tab_suporte->fetchALL(PDO::FETCH_ASSOC);

    foreach ($row as $r) {

        $data_abertura_suporte = date('d/m/y H:i', strtotime($r['data_open']));
        $status_suporte = $r['status_suporte'];
        switch ($status_suporte) {
            case 1:
                $nome_status = 'Em aberto';
                $css_suporte = 'danger';
                break;

            case 2:
                $nome_status = 'Em andamento';
                $css_suporte = 'warning';
                break;
            case 3:
                $nome_status = 'Terceirizado';
                $css_suporte = 'info';
                break;
            case 4:
                $nome_status = 'Finalizado';
                $css_suporte = 'success';
                break;

                case 5:
                    $nome_status = 'Com Previsão';
                    $css_suporte = 'warning';
                    break;

            case 6:
                $nome_status = 'Ind. revogado';
                $css_suporte = 'info';
                break;
            case 7:
                $nome_status = 'Ind. liberado';
                $css_suporte = 'primary';
                break;
        }

     
        echo '<tr>
        <td class="" colspan="2">
        <span class="bullet bullet-vertical h-9px bg-' . $css_suporte . '"></span>
            <a href="javascript:;" data-id_suporte="' . $r['id_suporte'] . '" data-kt-drawer-show="true" data-kt-drawer-target="#drawer_Suporte"  onclick="storeDataAttributesJanelaSuporte(this)" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">' . $r['nome_obra'] . '</a>
        </td>
        <td class="pe-0" colspan="2">
            <div class="d-flex justify-content-end">
                <span class="text-gray-800 fw-bold fs-6 me-1">' . $r['nome_suporte'] . '</span>

            </div>
        </td>
        <td class="" colspan="2">
            <div class="d-flex justify-content-end">
                <span class="text-dark fw-bold fs-6 me-3">' . $data_abertura_suporte . '</span>
                
            </div>
        </td>
                                                        <td class="pe-0" colspan="2">
            <div class="d-flex justify-content-end">

            <span class="badge badge-exclusive badge-light-' . $css_suporte . ' fw-bold fs-7 px-2 py-1 ms-1">' . $nome_status . '</span>
         

            </div>
        </td>
    </tr>';
    }

    echo '</tbody>
    <!--end::Table body-->
    </table>
    <!--end::Table-->';

    }else {


    echo  '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
    <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
    <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"></path>
            <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"></path>
        </svg>
    </span>
    <!--end::Svg Icon-->
    <div class="d-flex flex-column">
        <h4 class="mb-1 text-primary">Tickets de Suporte</h4>
        <span>Não há Tickets (Chamados) de Suporte, localizados para este Projeto.</span>
    </div>
</div>';


} ?>



