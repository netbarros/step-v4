<?php

   if (!isset($_SESSION)) session_start();

  /*
VPS:

 define('SGBD', 'mysql');
 define('HOST', '162.241.99.91'); //localhost
 define('DBNAME', 'step_bd'); //step
 define('CHARSET', 'utf8');
 define('USER', 'step_root');
 define('PASSWORD', 'F@087913');
 define('SERVER', 'linux');
 define('PORT', '3306');




Localhost:

define('HOST', 'localhost');
define('DBNAME', 'step_bd');
define('CHARSET', 'utf8');
define('USER', 'root');
define('PASSWORD', '');
define('PORT', '3306');



GCP:
 */

 define('SGBD', 'mysql');
 define('HOST', '172.25.2.3'); //localhost
 define('DBNAME', 'step_bd'); //step
 define('CHARSET', 'utf8');
 define('USER', 'step_root');
 define('PASSWORD', 'F@087913');
 define('SERVER', 'linux');
 define('PORT', '3306');  




 class Conexao {  
 
   private static $pdo;

   /*  
    * Escondendo o construtor da classe  
    */ 
   private function __construct() {  
     //  
   } 
 
   /*  
    * Método estático para retornar uma conexão válida  
    * Verifica se já existe uma instância da conexão, caso não, configura uma nova conexão  
    */  
   public static function getInstance() {  
     if (!isset(self::$pdo)) {  
       try {  
         $opcoes = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8', PDO::ATTR_PERSISTENT => TRUE, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);  
         self::$pdo = new PDO("mysql:host=" . HOST . "; port=".PORT."; dbname=" . DBNAME . "; charset=" . CHARSET . ";", USER, PASSWORD, $opcoes);  
       } catch (PDOException $e) {  
         print "Erro: " . $e->getMessage();  
       }  
     }  
     return self::$pdo;  
   }  
 }

 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('America/Sao_Paulo');
}