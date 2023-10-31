<?php

// Functions
function validateHeader() {
    if (!array_key_exists('HTTP_X_MY_CUSTOM_HEADER', $_SERVER) ) {
        // Acesso negado

        $retorno = array('codigo' => '0', 'mensagem' => 'Acesso Negado! valor do HTTP_X_MY_CUSTOM_HEADER não foi recuperado no Envio.');
			echo json_encode($retorno);

		
        exit;

    } elseif ($_SERVER['HTTP_X_MY_CUSTOM_HEADER'] !== 'Valor_Seguro'){

        $retorno = array('codigo' => '0', 'mensagem' => 'Acesso Negado! valor do HTTP_X_MY_CUSTOM_HEADER não corresponde ao valor esperado.');
        echo json_encode($retorno);

    }else{
        // Acesso permitido

    }
}


