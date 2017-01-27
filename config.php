<?php
$demo_text="";
/* $Revision: 399 $ */

	/*--------------------------------------------------\
	| 		|               | config.php        |
	|---------------------------------------------------|
	| Web-ERP - http://web-erp.sourceforge.net          |
	| by Logic Works Ltd                                |
	|---------------------------------------------------|
	|                                                   |
	\--------------------------------------------------*/

//Jaime, configuracion Ws Cfd
/*function getWs(){
    $wsWsdl = 'http://184.106.216.217:8080/cfd_9/cfdService?wsdl';
    $wsUser = 'ORGANIZACION RG HERMANOS SA DE CV';
    $wsPass = '1234';
    if(true)
        ini_set("soap.wsdl_cache_enabled", "0");
    $soapClient = new SoapClient($wsWsdl);
    $soapClient->login(array('user' => $wsUser, 'password' => $wsPass));
    return $soapClient;
}  */
//Termina Jaime, configuracion Ws Cfd

// User configurable variables
//---------------------------------------------------

// Sept 2006 RealHost bowikaxu 
// variable para autorizar precios mas bajo que el puesto en la orden al facturar y/o remisionar
// 1 - Si 0 - No

$passwordAdjustment='ar8933';

$PriceLessThanOrder = 1;
$ExtraComment = "Cliente Satisfecho - "; // comentario extra en la orden si aplico precio especial

// ACCOUNTS TO BE USED ON THE ACCOUNTS REPORTS
$rh_Accounts = array ('110100001', '110100002', '110101005', '110101008');

// USERS that can change the sales man on the customer branches
$rh_SMAuth = array ('gerardo');

// FLAG - disable F5 and BackSpace keys
$rh_disableKeys = 0;

//ACCOUNTS that can cancel invoices
$rh_AllowCancelInvoice = array('alicia','gerardo');

// bowikaxu realhost - Feb 2008
// Secciones de Cuentas para Estado de Resultados
$Seccion_Ventas=41;
$Seccion_Cto_Ventas = 51;
$Seccion_Gto_Admin = 61;
$Seccion_Gto_Venta = 62;
$Seccion_Res_Int_Financiamiento = 68;

// umbral de asignacion
$rh_umbral_asignacion = 2;

// bowikaxu realhost Feb 2008
$rh_dbsolover_user='root';
$rh_dbsolover_pass='root';
$rh_solover_role = '9';

//DefaultLanguage to use for the login screen and the setup of new users - the users language selection will override
$DefaultLanguage ='es_MX';

// Whether to display the demo login and password or not on the login screen
$allow_demo_mode = False;

// webERP version

$Version = '3.08';

// The timezone of the business - this allows the possibility of having
// the web-server on a overseas machine but record local time
// this is not necessary if you have your own server locally
// putenv('TZ=Europe/London');
// putenv('Australia/Melbourne');
// putenv('Australia/Sydney');
// putenv('TZ=Pacific/Auckland');

// Connection information for the database
// $host is the computer ip address or name where the database is located
// assuming that the web server is also the sql server
// $host = 'localhost';
// $host = '10.183.7.232';
//$host = '10.208.128.149'; //nuevo server DB Aug 22, 2015
$host = '192.168.10.11'; //nuevo server DB Aug 22, 2015


//The type of db server being used - currently only postgres or mysql
$dbType = 'mysql';
//$dbType = 'postgres' - DEPRECIATED;
//$dbType = 'mysql';
//$dbType = 'mysqli'; for PHP 5 and mysql > 4.1

// sql user & password
$dbuser = 'erp_armedica';
$dbpassword = 'H3GH7tfde5PGx53Z';
//It would probably be inappropraite to allow selection of the company in a hosted envionment so this option can be turned off with this parameter
$AllowCompanySelectionBox = true;

//If $AllowCompanySelectionBox = false above then the $DefaultCompany string is entered in the login screen as a default - otherwise the user is expected to know the name of the company to log into.
$DefaultCompany = 'armedica_erp_001';

//The maximum time that a login session can be idle before automatic logout
//time is in seconds  3600 seconds in an hour
$SessionLifeTime = 3600;

//The maximum time that a script can execute for before the web-server should terminate it
$MaximumExecutionTime =120;

//The path to which session files should be stored in the server - useful for some multi-host web servers
//this can be left commented out
//$SessionSavePath = dirname(__FILE__).'/tmp/';

$WebERP=explode('/',substr($_SERVER['REQUEST_URI'],1));
//$SessionSavePath = '/var/www/armedica/tmp';
$SessionSavePath = dirname(__FILE__).'/tmp/'.$WebERP[0];

if(!is_dir($SessionSavePath))
	mkdir($SessionSavePath,0777);


// which encryption function should be used
//$CryptFunction = "md5"; // MD5 Hash
$CryptFunction = "sha1"; // SHA1 Hash
//$CryptFunction = ""; // Plain Text

//Setting to 12 or 24 determines the format of the clock display at the end of all screens
$DefaultClock = 12;
//$DefaultClock = 24;



// END OF USER CONFIGURABLE VARIABLES



/*The $rootpath is used in most scripts to tell the script the installation details of the files.

NOTE: In some windows installation this command doesn't work and the administrator must set this to the path of the installation manually:
eg. if the files are under the webserver root directory then rootpath =''; if they are under weberp then weberp is the rootpath - notice no additional slashes are necessary.
*/

$rootpath = dirname($_SERVER['PHP_SELF']);
if (isset($DirectoryLevelsDeep)){
	for ($i=0;$i<$DirectoryLevelsDeep;$i++){
		$rootpath = substr($rootpath,0, strrpos($rootpath,'/'));
	}
}

if ($rootpath == "/" OR $rootpath == "\\") {
	$rootpath = "";
}
//$rootpath = '/web-erp';

/* Report all errors except E_NOTICE
This is the default value set in php.ini for most installations but just to be sure it is forced here
turning on NOTICES destroys things */

error_reporting (E_ALL & ~E_NOTICE);
error_reporting (0);
/*Make sure there is nothing - not even spaces after this last ?> */

