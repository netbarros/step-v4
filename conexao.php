<?php
/**
 * Sistema de Tratamento EP - STEP
 *
 * Sistema de Gestão de Operações para Tratamento de Efluentes.
 *
 * PHP version 8.0.26
 *
 * @category Sistema_de_Gestão_De_Operações_Para_Tratamento_de_Efluentes
 * @package  STEP
 * @author   "Fabiano Barros <fabiano.barros@grupoep.com.br>"
 * @license  fornecida por contratação de Criação e Desenvolvimento 
 * @link     step.eco.br
 * @since    1.4.1
 * @version  GIT: 1.4.0
 * @return   description
 * @see      arquivo de conexão do banco de dados
 */


 
  /*
GCP:

define('SGBD', 'mysql');
 define('HOST', '172.25.2.3'); //localhost
 define('DBNAME', 'step_bd'); //step
 define('CHARSET', 'utf8');
 define('USER', 'step_root');
 define('PASSWORD', 'F@087913');
 define('SERVER', 'linux');
 define('PORT', '3306'); 




Localhost:
 */
define('HOST', 'localhost');
define('DBNAME', 'step_bd');
define('CHARSET', 'utf8');
define('USER', 'root');
define('PASSWORD', '');
define('PORT', '3306');


 /*
GCP:


 define('SGBD', 'mysql');
 define('HOST', '172.25.2.3'); //localhost
 define('DBNAME', 'step_bd'); //step
 define('CHARSET', 'utf8');
 define('USER', 'root');
 define('PASSWORD', 'EnG2389*ep$28');
 define('SERVER', 'linux');
 define('PORT', '3306');

 */


class Conexao
{
    private static $pdo;

        /**
         * Escondendo o construtor da classe
         */
    private function __construct()
    {
        //
    }

    /*
    * Método estático para retornar uma conexão válida
    * Verifica se já existe uma instância da conexão, caso não, configura uma nova conexão
    */
    public static function getInstance()
    {
        if (!isset(self::$pdo)) {
            try {
                $opcoes = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8', PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
                self::$pdo = new PDO("mysql:host=" . HOST . "; port=" . PORT . "; dbname=" . DBNAME . "; charset=" . CHARSET . ";", USER, PASSWORD, $opcoes);
            } catch (PDOException $e) {
                print "Erro: " . $e->getMessage();
            }
        }
        return self::$pdo;
    }
}

if (! ini_get('date.timezone')) {
    date_default_timezone_set('America/Sao_Paulo');
}


// Adicione os cabeçalhos de segurança
// Content-Security-Policy: Para prevenir ataques de injeção de conteúdo.
//header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://unpkg.com https://cdn.amcharts.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; connect-src 'self'; frame-src 'self'; object-src 'none'; form-action 'self'; base-uri 'self'; report-uri /report.php;");

//X-Content-Type-Options: Para prevenir ataques baseados em MIME-sniffing.
header("X-Content-Type-Options: nosniff");
//X-Frame-Options: Para prevenir ataques de clickjacking.
header("X-Frame-Options: SAMEORIGIN");
//X-XSS-Protection: Para prevenir ataques de XSS.
header("X-XSS-Protection: 1; mode=block");
//Strict-Transport-Security: Para forçar a comunicação segura via HTTPS e prevenir ataques de downgrade.
// header("Strict-Transport-Security: max-age=31536000; includeSubDomains"); /* desabilitar se localhost em htts */
//X-Permitted-Cross-Domain-Policies: Para controlar a política de uso de recursos entre domínios.
header("X-Permitted-Cross-Domain-Policies: none");




//$usuario_sessao = $conexao->quote($usuario_sessao);  // Incluir em arquivos php que náo tiver conexao PDO, com query direta, isso serve para Proteger contra SQL injection só utilizado se houver ocnexão sem PDO, com query chamada diretamente nno sql



