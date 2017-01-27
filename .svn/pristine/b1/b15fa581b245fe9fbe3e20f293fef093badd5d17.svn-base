<?php
$AllowAnyone=1;
global $dbuser, $dbpassword;
chdir(dirname(__FILE__).'/../');
if(!function_exists('CryptPass'))
	include_once(dirname(__FILE__).'/../includes/session.inc');
$db_=$db;
/*if (isset($SessionSavePath)){
	session_save_path($SessionSavePath);
}
session_start();/**/
$PageSecurity=1;/**/

    if ((!in_array($PageSecurity, $_SESSION['AllowedPageSecurityTokens']) OR !isset($PageSecurity))) {
        echo "<center><strong>Error 0s00001: El usuario no tiene permisos</strong></center>";
        exit;
	}

chdir(dirname(__FILE__));
$server=$host;//"localhost";
$db=$_SESSION['DatabaseName'];//$DefaultCompany;//////"mangueras_erp_001";
$user=$dbuser;//"root";
$pass=$dbpassword;//"";
$version="0.6";

