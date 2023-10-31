
<?php require_once '../../conexao.php';
 	// Atribui uma conexão PDO
    $conexao = Conexao::getInstance();
     if (!isset($_SESSION)) session_start();


$id_plcode_atual    = trim(isset($_POST['id_plcode_lido'])) ? $_POST['id_plcode_lido'] : '';
$id_estacao_atual    = trim(isset($_COOKIE['estacao_atual'])) ? $_COOKIE['estacao_atual'] : '';

$nome_Estacao_Atual    = trim(isset($_COOKIE['nome_Estacao_Atual'])) ? $_COOKIE['nome_Estacao_Atual'] : '';
$nome_PLCode_Lido    = trim(isset($_COOKIE['nome_PLCode_Lido'])) ? $_COOKIE['nome_PLCode_Lido'] : '';
$nome_Cliente_Atual    = trim(isset($_COOKIE['nome_Cliente_Atual'])) ? $_COOKIE['nome_Cliente_Atual'] : '';
//$id_plcode_atual = "63";

if($id_plcode_atual==''){

	echo  '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h5 class="mb-1 text-dark">Instrução Operacional Não Encontrada!</h5>

														<div class="separator my-2"></div>
														<h6 >Para o PLCode: </br>
														<span class="fw-bold fs-5 text-danger"> Nenhum PLCode Lido </span> </h6>

														<div class="separator my-2"></div>
														
                                                        
													</div>
												</div>';

$conexao=null;
exit;

}	


    $sql=$conexao->query("SELECT instrucao_operacional FROM pontos_estacao WHERE id_ponto = '$id_plcode_atual'");
    $retorno = $sql->fetch(PDO::FETCH_ASSOC);

if($retorno){

if($retorno['instrucao_operacional']!=''){

echo $retorno['instrucao_operacional'];

} else {


echo  '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h5 class="mb-1 text-dark">Instrução Operacional Não Cadastrada!</h5>

														<div class="separator my-2"></div>
														<h6 >Para o PLCode: </br>
														<span class="fw-bold fs-5 text-danger">'.$nome_PLCode_Lido.'</span> </h6>

														<div class="separator my-2"></div>
														
                                                        
													</div>
												</div>';


}

$conexao=null;
exit;

} else {

 echo  '<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
													<!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
													<span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
															<path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="d-flex flex-column">
														<h5 class="mb-1 text-dark">Instrução Operacional Não Encontrada!</h5>

														<div class="separator my-2"></div>
														<h6 >Para o PLCode: </br>
														<span class="fw-bold fs-5 text-danger">'.$nome_PLCode_Lido.'</span> </h6>

														<div class="separator my-2"></div>
														
                                                        
													</div>
												</div>';



$conexao=null;
exit;
	}
	
	


?>
