<?php

$pagina_atual = basename($_SERVER['PHP_SELF'], '.php');

$pag_ativa_overview = '';
$pag_ativa_settings = '';
$pag_ativa_logs = '';

if ($pagina_atual == 'overview') {

    $pag_ativa_overview = 'active';
}
if ($pagina_atual == 'settings') {

    $pag_ativa_settings = 'active';
}
if ($pagina_atual == 'logs') {

    $pag_ativa_logs = 'active';
}




?>

<!--begin::Navbar-->
<div class="card mb-5 mb-xl-10">
    <div class="card-body pt-9 pb-0">
        <!--begin::Details-->
        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
            <!--begin: Pic-->
            <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">

                    <?php



                    $sql_foto_user = $conexao->query("SELECT * FROM usuarios WHERE id='$id_usuario'");

                    $conta_foto_user = $sql_foto_user->rowCount();


                    if ($conta_foto_user > 0) {

                        $ruf = $sql_foto_user->fetch(PDO::FETCH_ASSOC);

                        $nome_user = $ruf['nome'];
                        $email_user = $ruf['email'];
                        $nivel_user = $ruf['nivel'];
                        $foto_user_consulta = $ruf['foto'];
                        $brev_nome_user = substr($nome_user, 0, 11);


                        if (isset($_COOKIE['imagem_avatar_usuario']) && !is_null($_COOKIE['imagem_avatar_usuario'])) {
                            // Abre uma função aqui
                            echo '<img src="'.$_COOKIE['imagem_avatar_usuario'].'" alt="image" class="imagem_avatar_usuario" />';
                            
                        } else if ($ruf['foto'] != '') {


                            if (file_exists('/foto-perfil/'. $foto_user_consulta)) {
                                    
                                echo '<img src="/foto-perfil/' .  $foto_user_consulta . '" alt="image" class="h-100px w-100px rounded imagem_avatar_usuario" />';

                            } else {
                                echo ' <img src="/foto-perfil/avatar.png" alt="image" class="h-100px w-100px rounded imagem_avatar_usuario" />';
                            }

                  

                    }else{

                        echo '<img class="h-100px w-100px rounded imagem_avatar_usuario" src="/foto-perfil/avatar.png" alt="' . $nome_user . '"  />';
                    }


                    
                        if ($ruf['status'] == '1') {
                            echo '<div data-bs-toggle="tooltip" title="O acesso do usuário: '.$nome_user.', está liberado no Sistema." class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>';
                        } else {
                            echo '<div data-bs-toggle="tooltip" title="O acesso do usuário: '.$nome_user.', se encontra bloqueado no Sistema."  class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-danger rounded-circle border border-4 border-body h-20px w-20px"></div>';
                        }
                   

                    ?>




                </div>
            </div>
            <!--end::Pic-->
            <!--begin::Info-->
            <div class="flex-grow-1">
                <!--begin::Title-->
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <!--begin::User-->
                    <div class="d-flex flex-column">
                        <!--begin::Name-->
                        <div class="d-flex align-items-center mb-2">
                            <a href="javascript:;" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1"><?= $nome_user; ?></a>
                            <?php if($nivel_user=='cliente'){ ?>
                            <a href="javascript:;">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen026.svg-->
                                <span class="svg-icon svg-icon-1 svg-icon-primary" data-bs-toggle="tooltip" title="Brasão de Usuário Cliente">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                        <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="currentColor" />
                                        <path d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </a>
                            <?php } ?>
                            <a href="javascript:;" class="btn btn-sm btn-light-success fw-bold ms-2 fs-8 py-1 px-3 text-uppercase" data-bs-toggle="tooltip" title="Nível de Acesso deste Usuário"><?= $nivel_user; ?></a>
                        </div>
                        <!--end::Name-->
                        <!--begin::Info-->
                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">


                            <a href="mailto:<?= $email_user; ?>?subject=STEP&&bcc=webmaster@step.eco.br&body=E-mail gerado automaticamente através do Sistema STEP." class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                                <!--begin::Svg Icon | path: icons/duotune/communication/com011.svg-->
                                <span class="svg-icon svg-icon-4 me-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19Z" fill="currentColor" />
                                        <path d="M21 5H2.99999C2.69999 5 2.49999 5.10005 2.29999 5.30005L11.2 13.3C11.7 13.7 12.4 13.7 12.8 13.3L21.7 5.30005C21.5 5.10005 21.3 5 21 5Z" fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon--><?= $email_user; ?>
                            </a>
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::User-->
<?php  }
?>
                </div>
                <!--end::Title-->
                <!--begin::Stats-->
                <div class="d-flex flex-wrap flex-stack">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column flex-grow-1 pe-8">
                        <!--begin::Stats-->
                        <div class="d-flex flex-wrap">
                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                    <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor" />
                                            <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <?php
                                    $sql_conta_leitura_user = $conexao->query("SELECT COUNT(DISTINCT r.id_rmm) as Total_Leitura_User FROM rmm r WHERE r.id_operador='$id_usuario' ");
                                    $conta_leitura_user = $sql_conta_leitura_user->rowCount();

                                    if ($conta_leitura_user > 0) {
                                        $rcls = $sql_conta_leitura_user->fetch(PDO::FETCH_ASSOC);

                                        $Total_Leitura_User = $rcls['Total_Leitura_User'];

                                        echo '<div class="fs-2 fw-bold" data-kt-countup="true" data-kt-countup-separator="."  data-kt-countup-value="' . $Total_Leitura_User . '">' . $Total_Leitura_User . '</div>';
                                    }

                                    ?>

                                </div>
                                <!--end::Number-->
                                <!--begin::Label-->
                                <div class="fw-semibold fs-6 text-gray-400">Leituras</div>
                                <!--end::Label-->
                            </div>
                            <!--end::Stat-->
                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                                    <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                            <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <?php
                                    $sql_conta_projetos_user = $conexao->query("SELECT COUNT(DISTINCT pt.id_obra) as Total_projetos_User 
                                                                FROM rmm r
                                                                INNER JOIN pontos_estacao pt ON pt.id_ponto = r.id_ponto
                                                                LEFT JOIN
                                                                usuarios_projeto up ON up.id_obra = pt.id_obra
                                                                 WHERE (r.id_operador='$id_usuario' OR up.id_usuario ='$id_usuario')  GROUP BY pt.id_obra
                                                                ");
                                    $conta_projetos_user = $sql_conta_projetos_user->rowCount();

                                    if ($conta_projetos_user > 0) {
                                        $rcls = $sql_conta_projetos_user->fetch(PDO::FETCH_ASSOC);

                                        $Total_projetos_User = $rcls['Total_projetos_User'];

                                        echo '<div class="fs-2 fw-bold" data-kt-countup="true" data-kt-countup-separator="."  data-kt-countup-value="' . $Total_projetos_User . '">' . $Total_projetos_User . '</div>';
                                    } else {

                                        echo '<div class="fs-2 fw-bold" data-kt-countup="true" data-kt-countup-separator="."  data-kt-countup-value="0">0</div>';
                                    }

                                    ?>

                                </div>
                                <!--end::Number-->
                                <!--begin::Label-->
                                <div class="fw-semibold fs-6 text-gray-400">Projetos</div>
                                <!--end::Label-->
                            </div>
                            <!--end::Stat-->

                              <!--begin::Stat-->
                              <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                                    <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                            <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <?php
                                    $sql_conta_projetos_user = $conexao->query("SELECT COUNT(DISTINCT s.id_suporte) as Total_suporte_User 
                                                                FROM suporte s
                                                                INNER JOIN rmm r On r.id_operador = s.quem_abriu
                                                                INNER JOIN pontos_estacao pt ON pt.id_ponto = r.id_ponto
                                                                 WHERE r.id_operador='$id_usuario' GROUP BY pt.id_obra
                                                                ");
                                    $conta_projetos_user = $sql_conta_projetos_user->rowCount();

                                    if ($conta_projetos_user > 0) {
                                        $rcls = $sql_conta_projetos_user->fetch(PDO::FETCH_ASSOC);

                                        $Total_suporte_User = $rcls['Total_suporte_User'];

                                        echo '<div class="fs-2 fw-bold" data-kt-countup="true" data-kt-countup-separator="."  data-kt-countup-value="' . $Total_suporte_User . '">' . $Total_suporte_User . '</div>';
                                    }else {

                                        echo '<div class="fs-2 fw-bold" data-kt-countup="true" data-kt-countup-separator="."  data-kt-countup-value="0">0</div>';
                                    }

                                    ?>

                                </div>
                                <!--end::Number-->
                                <!--begin::Label-->
                                <div class="fw-semibold fs-6 text-gray-400">Suportes</div>
                                <!--end::Label-->
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">

                            <?php
/* 
Primeiro, fazemos uma consulta para obter o total de suportes criados pelo usuário;

Verificamos se o usuário criou suportes e, caso positivo, obtemos o total de suportes;

Em seguida, fazemos uma consulta para obter o número de leituras bem-sucedidas realizadas pelo usuário;

Verificamos se o usuário realizou leituras bem-sucedidas e, caso positivo, obtemos o número de leituras bem-sucedidas;

Calculamos o número de leituras bem-sucedidas por suporte;

Fazemos uma consulta para obter o número total de leituras realizadas pelo usuário;

Verificamos se o usuário realizou leituras e, caso positivo, obtemos o número total de leituras;

Calculamos o número total de leituras bem-sucedidas;

Calculamos a taxa de sucesso em porcentagem e, caso o número total de leituras seja zero, a taxa de sucesso é definida como zero;

Definimos a variável $variacao_leitura_user como a taxa de sucesso em porcentagem, formatando-a com duas casas decimais e adicionando o símbolo de porcentagem (%).

 */// Consulta para obter o total de suportes criados pelo usuário
$sql_conta_suporte_user = $conexao->query("SELECT COUNT(DISTINCT s.id_suporte) as Total_Suporte_User
FROM suporte s
WHERE s.quem_abriu='$id_usuario' AND s.data_open > '2022-01-01'");

// Verifica se o usuário criou suportes
if ($sql_conta_suporte_user->rowCount() > 0) {
    // Obtém o total de suportes criados pelo usuário
$rcss = $sql_conta_suporte_user->fetch(PDO::FETCH_ASSOC);
$total_suportes = $rcss['Total_Suporte_User'];

// Consulta para obter o número de leituras bem-sucedidas realizadas pelo usuário
$sql_conta_leituras_ok = $conexao->query("SELECT COUNT(DISTINCT r.id_rmm) as Total_Leitura_OK 
                                          FROM rmm r 
                                          WHERE r.id_operador='$id_usuario' AND r.data_leitura > '2022-01-01' AND status_leitura='1'");

// Verifica se o usuário realizou leituras bem-sucedidas
if ($sql_conta_leituras_ok->rowCount() > 0) {

    // Obtém o número de leituras bem-sucedidas realizadas pelo usuário
    $rcl = $sql_conta_leituras_ok->fetch(PDO::FETCH_ASSOC);
    $leituras_bem_sucedidas = $rcl['Total_Leitura_OK'];

    // Verifica se o total de suportes é diferente de zero
    if ($total_suportes != 0) {
        // Calcula o número de leituras bem-sucedidas por suporte
        $leituras_bem_sucedidas_por_suporte = $leituras_bem_sucedidas / $total_suportes;
    } else {
        $leituras_bem_sucedidas_por_suporte = 0;
    }

    // Consulta para obter o número total de leituras realizadas pelo usuário
    $sql_conta_leituras = $conexao->query("SELECT COUNT(DISTINCT r.id_rmm) as Total_Leitura_User 
                                           FROM rmm r 
                                           WHERE r.id_operador='$id_usuario' AND r.data_leitura > '2019-01-01'");

    // Verifica se o usuário realizou leituras
    if ($sql_conta_leituras->rowCount() > 0) {

        // Obtém o número total de leituras realizadas pelo usuário
        $rclu = $sql_conta_leituras->fetch(PDO::FETCH_ASSOC);
        $total_leituras = $rclu['Total_Leitura_User'];

        // Calcula o número total de leituras bem-sucedidas
        $total_leituras_bem_sucedidas = $total_suportes * $leituras_bem_sucedidas_por_suporte;

        // Calcula a taxa de sucesso em porcentagem
        $taxa_sucesso = $total_leituras != 0 ? ($total_leituras_bem_sucedidas / $total_leituras) * 100 : 0;

        // Define a variável $variacao_leitura_user como a taxa de sucesso em porcentagem
        $variacao_leitura_user = number_format($taxa_sucesso, 2) . '%';

    } else {

        $variacao_leitura_user = '#';
    }

} else {
    $variacao_leitura_user = '#';
}
} else {
    $variacao_leitura_user = '#';
    }

?>

                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                    <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor" />
                                            <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <div class="fs-2 fw-bold" data-kt-countup="true" data-kt-countup-decimal-places="0" data-kt-countup-separator="." data-kt-countup-value="<?= $variacao_leitura_user; ?>" data-kt-countup-prefix="%"><?= $variacao_leitura_user; ?></div>
                                </div>
                                <!--end::Number-->
                                <!--begin::Label-->
                                <div class="fw-semibold fs-6 text-gray-400">Taxa de Sucesso Leitura </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Stat-->


                     
                        </div>
                        <!--end::Stats-->
                    </div>
                    <!--end::Wrapper-->

                    

                </div>
                <!--end::Stats-->
            </div>
            <!--end::Info-->
        </div>
        <!--end::Details-->
        <!--begin::Navs-->
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
            <!--begin::Nav item-->
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 <?= $pag_ativa_overview; ?>" href="../../views/conta-usuario/overview.php?id=<?= $id_usuario; ?>">Visão Geral</a>
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item-->
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 <?= $pag_ativa_settings; ?>" href="../../views/conta-usuario/settings.php?id=<?= $id_usuario; ?>">Configurações da Conta</a>
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item-->
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 <?= $pag_ativa_logs; ?>" href="../../views/conta-usuario/logs.php?id=<?= $id_usuario; ?>">Logs</a>
            </li>
            <!--end::Nav item-->
        </ul>
        <!--begin::Navs-->
    </div>
</div>
<!--end::Navbar-->