<?php
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/total-voice/autoload.php';

use Twilio\Rest\Client;

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
header("content-type: application/json");

date_default_timezone_set('America/Sao_Paulo');

include_once $_SERVER['DOCUMENT_ROOT'] . '/conexao.php';
$conexao = Conexao::getInstance();
if (!isset($_SESSION)) session_start();


$sid = $_REQUEST['MessageSid'];
$status = $_REQUEST['MessageStatus'];

openlog("myMessageLog", LOG_PID | LOG_PERROR, LOG_USER);
syslog(LOG_INFO, "SID: $sid, Status: $status");
closelog();