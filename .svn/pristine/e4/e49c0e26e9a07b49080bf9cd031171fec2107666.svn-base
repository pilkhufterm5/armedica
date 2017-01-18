<?php

global $host, $dbuser, $dbpassword;
if($_REQUEST["r"]=='stockmoves/bajaporconsumows'){
    $AllowAnyone=1;
    $PageSecurity=1;
    $DatabaseName='sme_erp_001';
}
$PageSecurity = 1;
$Local=realpath(dirname(realpath(__FILE__))."/..");
set_include_path(get_include_path() . PATH_SEPARATOR . $Local);
global $rootpath;

$Ruta=$_SERVER["QUERY_STRING"];
$Ruta=explode('=',$Ruta);
set_time_limit(0);
ini_set('memory_limit', '5000M');

//$Ruta=explode('/',$Ruta[1]);
//ini_set('display_errors','off');
//error_reporting(E_All ~E_NOTICE);

error_reporting(E_WARNING | E_PARSE);
error_reporting (0);


$_SERVER['LocalERP_path']=$Local;

$uri=$_SERVER['REQUEST_URI'];
$dat=explode("/",$_SERVER['SCRIPT_NAME']);
$po=array_pop($dat);
$dat=implode("/",$dat);
$uri_=$uri;
$URI= str_replace($dat."/","",$uri);
$URI= str_replace($URI,"",$_SERVER['REQUEST_URI']);
$URI=rtrim($URI,"/");
$URI=explode("/",$URI);
array_pop($URI);

$URI=implode("/",$URI);
$rootpath=
$_SERVER["UrlERP_BASE"]=$URI;


chdir($Local);
$PathPrefix='';

include_once('includes/session.inc');

include_once('includes/SQL_CommonFunctions.inc');
include_once('includes/GetPrice.inc');
include_once('includes/DateFunctions.inc');

$rootpath=rtrim($URI,'/');
$DebugMode=$_SERVER["SERVER_ADDR"]=='::1' || $_SERVER["SERVER_ADDR"]=='127.0.0.1';

if(!$DebugMode){
    $DebugMode=true;
    $data=explode(".",$_SERVER["SERVER_NAME"]);
    if(count($data)==4){
        foreach($data as $valor){
            $DebugMode=$DebugMode&&is_numeric($valor);
        }
    }
}
//echo $_SESSION['DatabaseName'];
// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();
