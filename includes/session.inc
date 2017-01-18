<?php
/* webERP 3.05 Revision: 1.48 $ */

/* webERP 3.08 Revision: 1.53 $ */
if(!isset($PageSecurity))$PageSecurity=1;
if (!isset($PathPrefix)) {
	$PathPrefix='';
}

include($PathPrefix . 'config.php');

if (isset($SessionSavePath)){
	session_save_path($SessionSavePath);
}

ini_set('session.gc_Maxlifetime',$SessionLifeTime);
ini_set('max_execution_time',$MaximumExecutionTime);

session_start();

if(!function_exists('session_register')){
	function session_register($data){
		$_SESSION[$data]=true;
	}
}

if(!function_exists('addToIncludePath')){
	function addToIncludePath($path){
		$pathList=explode(PATH_SEPARATOR,get_include_path());
		$base=array_shift($pathList);
		array_unshift($pathList,$base,$path);
		$pathList=implode(PATH_SEPARATOR,$pathList);
		set_include_path($pathList);
		return $pathList;
	}
}
addToIncludePath(dirname(realpath(__FILE__)));
addToIncludePath(realpath(dirname(realpath(__FILE__)).'/..'));
if($PathPrefix!=''&&$PathPrefix!='.') addToIncludePath($PathPrefix);

/* Realhost 2013-07-27 Rafael Rojas
 * Se agrega archivo de configuracion extra para cada compa�ia, para poder modificar los paramentros de config, inclusive la conexion a la base de datos.
*
*/
if (!isset($_SESSION['DatabaseName'])&&isset($_POST['CompanyNameField'])){
	if(is_file($PathPrefix.'companies/'.$_POST['CompanyNameField'].'/config.php'))
		$_SESSION['DatabaseName']=$_POST['CompanyNameField'];
}else if(isset($DatabaseName)){
	if(is_file($PathPrefix.'companies/'.$DatabaseName.'/config.php'))
		$_SESSION['DatabaseName']=$DatabaseName;
}
if(isset($_SESSION['DatabaseName'])){
	if(is_file($PathPrefix.'companies/'.$_SESSION['DatabaseName'].'/config.php')){
		include_once $PathPrefix.'companies/'.$_SESSION['DatabaseName'].'/config.php';
		addToIncludePath($PathPrefix.'companies/'.$_SESSION['DatabaseName'].'/');
		addToIncludePath(dirname($PathPrefix.'companies/'.$_SESSION['DatabaseName'].'/'));
		if(is_dir($PathPrefix.'companies/'.$_SESSION['DatabaseName'].'/override'))
			addToIncludePath(dirname($PathPrefix.'companies/'.$_SESSION['DatabaseName'].'/override/'));
	}
}

include($PathPrefix . 'includes/LanguageSetup.php');
include($PathPrefix . 'includes/ConnectDB.inc');
include($PathPrefix . 'includes/DateFunctions.inc');

// Un comment to turn off attempts counter
//$_SESSION['AttemptsCounter'] = 0;

if (!isset($_SESSION['AttemptsCounter'])){
	$_SESSION['AttemptsCounter'] = 0;
}

/*foreach ($_POST as $key => $value) {
	if (gettype($value) != "array") {
	   	$_POST[$key] = DB_escape_string($value);
	} else {
		foreach ($value as $key1 => $value1) {
			$value[$key1] = DB_escape_string($value1);
		}
	}
}

foreach ($_GET as $key => $value) {
	if (gettype($value) != "array") {
		$_GET[$key] = DB_escape_string($value);
	}
} */

if (!isset($AllowAnyone)){ /* only do security checks if AllowAnyone is not true */

	if (!isset($_SESSION['AccessLevel']) OR $_SESSION['AccessLevel'] == '' OR
		(isset($_POST['UserNameEntryField']) AND $_POST['UserNameEntryField'] != '')) {

	/* if not logged in */

		$_SESSION['AccessLevel'] = '';
		$_SESSION['CustomerID'] = '';
		$_SESSION['UserBranch'] = '';
		$_SESSION['Module'] = '';
		$_SESSION['PageSize'] = '';
		$_SESSION['UserStockLocation'] = '';
		$_SESSION['AttemptsCounter']++;


		$theme = 'professional';

		// Show login screen
		if (!isset($_POST['UserNameEntryField']) or $_POST['UserNameEntryField'] == '') {
			include($PathPrefix . 'includes/Login.php');
			exit;
		}


		$sql = "SELECT www_users.fullaccess,
				www_users.customerid,
				www_users.lastvisitdate,
				www_users.pagesize,
				www_users.defaultlocation,
				www_users.branchcode,
				www_users.modulesallowed,
				www_users.blocked,
				www_users.realname,
				www_users.theme,
				www_users.displayrecordsmax,
				www_users.userid,
				www_users.language,
				www_users.rh_updatecost,
              www_users.rh_permitionlocation,
              www_users.rh_pagelock,
              www_users.rh_adminafil
			FROM www_users
			WHERE www_users.userid='" . DB_escape_string($_POST['UserNameEntryField']) . "'
			AND (www_users.password='" . CryptPass(DB_escape_string($_POST['Password'])) . "'
			OR  www_users.password='" . DB_escape_string($_POST['Password']) . "')";
		$Auth_Result = DB_query($sql, $db);

		// Populate session variables with data base results
		if (DB_num_rows($Auth_Result) > 0) {
                        // include('rh_sql.php');
			$myrow = DB_fetch_array($Auth_Result);
			
			if ($myrow[7]==1){
			//the account is blocked
				die(include($PathPrefix . 'includes/FailedLogin.php'));
			}
			$_SESSION['BlockingPages'] = explode(',',$myrow['rh_pagelock']);
			
		$sqluser = "SELECT * FROM rh_blockuser WHERE user = '".$myrow[11]."'";
		$resultuser = DB_query($sqluser, $db);
		if(DB_num_rows($resultuser) > 0){
		    
			/*reset the attempts counter on successful login */
			$_SESSION['AttemptsCounter'] = 0;
			$_SESSION['AccessLevel'] = $myrow[0];
			$_SESSION['CustomerID'] = $myrow[1];
			$_SESSION['UserBranch'] = $myrow[5];
			$_SESSION['DefaultPageSize'] = $myrow[3];
			$_SESSION['UserStockLocation'] = $myrow[4];
			$_SESSION['ModulesEnabled'] = explode(",", $myrow[6]);
			$_SESSION['UsersRealName'] = $myrow[8];
			$_SESSION['Theme'] = $myrow['theme'];
			$_SESSION['UserID'] = $myrow[11];
			$_SESSION['Language'] = $myrow[12];
			$_SESSION['rh_updatecost'] = $myrow[13];
            $_SESSION['rh_AdminAfil'] = $myrow['rh_adminafil'];
            $locations=explode(',',$myrow[14]);
            
            /*
            echo "ÑOOO<PRE>";
            print_r($_SESSION);
            echo "</PRE>"; */
            
            foreach ($locations as $value){
                $sqlLoc="SELECT loccode, locationname FROM locations where loccode='".$value."'";
                $rsLoc=DB_query($sqlLoc,$db);
                if($rowLoc=DB_fetch_array($rsLoc)){
                    $_SESSION['rh_permitionlocation'][$value]=$rowLoc[1];
                }
            }
            
			if ($myrow[10] > 0) {
				$_SESSION['DisplayRecordsMax'] = $myrow[10];
			} else {
				$_SESSION['DisplayRecordsMax'] = $_SESSION['DefaultDisplayRecordsMax'];  // default comes from config.php
			}

			$sql = "UPDATE www_users SET lastvisitdate='". date("Y-m-d H:i:s") ."'
					WHERE www_users.userid='" . DB_escape_string($_POST['UserNameEntryField']) . "'
					AND www_users.password='" . CryptPass(DB_escape_string($_POST['Password'])) ."'";
			$Auth_Result = DB_query($sql, $db);

			/*get the security tokens that the user has access to */
			$sql = 'SELECT tokenid FROM securitygroups
					WHERE secroleid =  ' . $_SESSION['AccessLevel'];
			$Sec_Result = DB_query($sql, $db);

			$_SESSION['AllowedPageSecurityTokens'] = array();
			if (DB_num_rows($Sec_Result)==0){
				$title = _('Account Error Report');
				include($PathPrefix . 'includes/header.inc');
				echo '<BR><BR><BR>';
				prnMsg(_('Your user role does not have any access defined for webERP. There is an error in the security setup for this user account'),'error');
				include($PathPrefix . 'includes/footer.inc');
				exit;
			} else {
				$i=0;
				while ($myrow = DB_fetch_row($Sec_Result)){
					$_SESSION['AllowedPageSecurityTokens'][$i] = $myrow[0];
					$i++;
				}
			}

			$sqlsessin = "SELECT session FROM rh_session WHERE session = 'sess_".session_id()."'";
			$resultsessin = DB_query($sqlsessin, $db);
			if(DB_num_rows($resultsessin) == 0){
				$sqlsess = "INSERT INTO rh_session (session , usuario) VALUES ('sess_".session_id()."','".$_SESSION['UserID']."')";
				$resultsess = DB_query($sqlsess, $db);
			}
			{//Borramos los archivos que tienen mas de 7 dias de inactividad, mantenimiento automatico
				$dir=$SessionSavePath;
				//foreach(glob(dirname(__FILE__).'/../tmp/*') as $dir)
				{
			        if(is_dir($dir))
					        foreach(glob($dir.'/*') as $file){
					                if(is_file($file)&&(time() - filemtime($file)) > 7*24*3600)
					                {
					                 @unlink($file);
					                }
					        }
					}
			}

			echo "<META HTTP-EQUIV='refresh' CONTENT='0; URL=" . $_SERVER['PHP_SELF'] . "?" . SID . "'>";
			exit;
		} else {
			$sqlip = "SELECT * FROM rh_blockip WHERE ip = '".$_SERVER['REMOTE_ADDR']."'";
			$resultip = DB_query($sqlip, $db);
			if(DB_num_rows($resultip) > 0){
				/*reset the attempts counter on successful login */
				$_SESSION['AttemptsCounter'] = 0;
				$_SESSION['AccessLevel'] = $myrow[0];
				$_SESSION['CustomerID'] = $myrow[1];
				$_SESSION['UserBranch'] = $myrow[5];
				$_SESSION['DefaultPageSize'] = $myrow[3];
				$_SESSION['UserStockLocation'] = $myrow[4];
				$_SESSION['ModulesEnabled'] = explode(",", $myrow[6]);
				$_SESSION['UsersRealName'] = $myrow[8];
				$_SESSION['Theme'] = $myrow['theme'];
				$_SESSION['UserID'] = $myrow[11];
				$_SESSION['Language'] = $myrow[12];
				$_SESSION['rh_updatecost'] = $myrow[13];
                $locations=explode(',',$myrow[14]);
                foreach ($locations as $value){
                    $sqlLoc="SELECT loccode, locationname FROM locations where loccode='".$value."'";
                    $rsLoc=DB_query($sqlLoc,$db);
                    if($rowLoc=DB_fetch_array($rsLoc)){
                        $_SESSION['rh_permitionlocation'][$value]=$rowLoc[1];
                    }
                }
				if ($myrow[10] > 0) {
					$_SESSION['DisplayRecordsMax'] = $myrow[10];
				} else {
					$_SESSION['DisplayRecordsMax'] = $_SESSION['DefaultDisplayRecordsMax'];  // default comes from config.php
				}

				$sql = "UPDATE www_users SET lastvisitdate='". date("Y-m-d H:i:s") ."'
						WHERE www_users.userid='" . DB_escape_string($_POST['UserNameEntryField']) . "'
						AND www_users.password='" . CryptPass(DB_escape_string($_POST['Password'])) ."'";
				$Auth_Result = DB_query($sql, $db);

				/*get the security tokens that the user has access to */
				$sql = 'SELECT tokenid FROM securitygroups
						WHERE secroleid =  ' . $_SESSION['AccessLevel'];
				$Sec_Result = DB_query($sql, $db);

				$_SESSION['AllowedPageSecurityTokens'] = array();
				if (DB_num_rows($Sec_Result)==0){
					$title = _('Account Error Report');
					include($PathPrefix . 'includes/header.inc');
					echo '<BR><BR><BR>';
					prnMsg(_('Your user role does not have any access defined for webERP. There is an error in the security setup for this user account'),'error');
					include($PathPrefix . 'includes/footer.inc');
					exit;
				} else {
					$i=0;
					while ($myrow = DB_fetch_row($Sec_Result)){
						$_SESSION['AllowedPageSecurityTokens'][$i] = $myrow[0];
						$i++;
					}
				}
				$sqlsessin = "SELECT session FROM rh_session WHERE session = 'sess_".session_id()."'";
				$resultsessin = DB_query($sqlsessin, $db);
				if(DB_num_rows($resultsessin) == 0){
					$sqlsess = "INSERT INTO rh_session (session , usuario) VALUES ('sess_".session_id()."','".$_SESSION['UserID']."')";
					$resultsess = DB_query($sqlsess, $db);
				}

				echo "<META HTTP-EQUIV='refresh' CONTENT='0; URL=" . $_SERVER['PHP_SELF'] . "?" . SID . "'>";
			}else{
				$demo_text = '<FONT SIZE="3" COLOR="red"><b>' .  _('Access Denied') . '</B></FONT><BR>';
				die(include($PathPrefix . 'includes/Login.php'));
			}
		}
			exit;
		} else {
		     // Incorrect password
			// 5 login attempts, show failed login screen
			if (!isset($_SESSION['AttemptsCounter'])) {
				$_SESSION['AttemptsCounter'] = 0;
			} elseif ($_SESSION['AttemptsCounter'] >= 5 AND isset($_POST['UserNameEntryField']) AND false) {
				/*User blocked from future accesses until sysadmin releases */
				$sql = "UPDATE www_users
						SET blocked=0
					WHERE www_users.userid='" . $_POST['UserNameEntryField'] . "'";
				$Auth_Result = DB_query($sql, $db);
				die(include($PathPrefix . 'includes/FailedLogin.php'));
			}
			$demo_text = '<FONT SIZE="3" COLOR="red"><b>' .  _('incorrect password') . '</B></FONT><BR><B>' . _('The user/password combination') . '<BR>' . _('is not a valid user of the system') . '</B>';
			die(include($PathPrefix . 'includes/Login.php'));
		}
	}		// End of userid/password check
	// Run with debugging messages for the system administrator(s) but not anyone else
	//$_SESSION['CashBase']=1;//Caja base siempre debe de ser true, 
	if (in_array(15, $_SESSION['AllowedPageSecurityTokens'])) {
		$debug = 1;
	} else {
		$debug = 0;
	}

} /* only do security checks if AllowAnyone is not true */

/*User is logged in so get configuration parameters  - save in session*/
include($PathPrefix . 'includes/GetConfig.php');

/****************************************************************************************************************************
* Jorge Garcia
* 20/Dic/2008 Guarda la variable de umbral
****************************************************************************************************************************/
$rh_umbral_asignacion = $_SESSION['CompanyRecord']['rh_umbral'];
/****************************************************************************************************************************
* Jorge Garcia Fin Modificacion
****************************************************************************************************************************/

if(isset($_SESSION['DB_Maintenance'])){
	if ($_SESSION['DB_Maintenance']!=0)  {
		if (DateDiff(Date($_SESSION['DefaultDateFormat']),
				ConvertSQLDate($_SESSION['DB_Maintenance_LastRun'])
				,'d')	> 	$_SESSION['DB_Maintenance']){

			/*Do the DB maintenance routing for the DB_type selected */
			DB_Maintenance($db);
			//purge the audit trail if necessary
			if (isset($_SESSION['MonthsAuditTrail'])){
				 $sql = "DELETE FROM audittrail
						WHERE  transactiondate <= '" . Date('Y-m-d', mktime(0,0,0, Date('m')-$_SESSION['MonthsAuditTrail'])) . "'";
				$ErrMsg = _('There was a problem deleting expired audit-trail history');
				$result = DB_query($sql,$db);
			}
			$_SESSION['DB_Maintenance_LastRun'] = Date('Y-m-d');
		}
	}
}

/*Check to see if currency rates need to be updated */
unset($_SESSION['UpdateCurrencyRatesDaily']);
if (isset($_SESSION['UpdateCurrencyRatesDaily'])){
	if ($_SESSION['UpdateCurrencyRatesDaily']!=0)  {
		if (DateDiff(Date($_SESSION['DefaultDateFormat']),
				ConvertSQLDate($_SESSION['UpdateCurrencyRatesDaily'])
				,'d')> 0){

			$CurrencyRates = GetECBCurrencyRates(); // gets rates from ECB see includes/MiscFunctions.php
			/*Loop around the defined currencies and get the rate from ECB */
		  	$CurrenciesResult = DB_query('SELECT currabrev FROM currencies',$db);
			while ($CurrencyRow = DB_fetch_row($CurrenciesResult)){
				if ($CurrencyRow[0]!=$_SESSION['CompanyRecord']['currencydefault']){
					$UpdateCurrRateResult = DB_query('UPDATE currencies SET
											rate=' . GetCurrencyRate ($CurrencyRow[0],$CurrencyRates) . "
											WHERE currabrev='" . $CurrencyRow[0] . "'",$db);
				}
			}
			$_SESSION['UpdateCurrencyRatesDaily'] = Date('Y-m-d');
			$UpdateConfigResult = DB_query("UPDATE config SET confvalue = '" . Date('Y-m-d') . "' WHERE confname='UpdateCurrencyRatesDaily'",$db);
		}
	}
}


If (isset($_POST['Theme']) && ($_SESSION['UsersRealName'] == $_POST['RealName'])) {
	$_SESSION['Theme'] = $_POST['Theme'];
	$theme = $_POST['Theme'];
} elseif (!isset($_SESSION['Theme'])) {
	$theme = $_SESSION['DefaultTheme'];
	$_SESSION['Theme'] = $_SESSION['DefaultTheme'];

} else {
	$theme = $_SESSION['Theme'];
}

if ($_SESSION['HTTPS_Only']==1){
	if ($_SERVER['HTTPS']!='on'){
		prnMsg(_('webERP is configured to allow only secure socket connections. Pages must be called with https://') . ' .....','error');
		exit;
	}
}



// Now check that the user as logged in has access to the page being called. The $PageSecurity
// value must be set in the script before header.inc is included. $SecurityGroups is an array of
// arrays defining access for each group of users. These definitions can be modified by a system admin under setup
$Pages = explode('/',$_SERVER['PHP_SELF']);

$sql ="select title,pagesecurity,active from scripts where filename='".$Pages[count($Pages)-1]."';";
$rs = DB_query($sql,$db);
$Active=1;
if($rw = DB_fetch_array($rs)){
   $_SESSION['title']=$rw['title'];
   $PageSecurity=$rw['pagesecurity'];
   $Active=$rw['active'];
}
if($Active==0){
	$title = _('Modulo no Activo');
	include($PathPrefix . 'includes/header.inc');
	echo '<BR><BR><BR>';
	prnMsg(_('Esta Pagina esta bloqueada o esta depreciada. funcionalidad no activada.'),'error');
	echo '<iframe style="display:none" src="rh_permisosIndividuales.php?userid='.$_SESSION['UserID'].'"></iframe>';
	include($PathPrefix . 'includes/footer.inc');
	exit;
}


if (!is_array($_SESSION['AllowedPageSecurityTokens']) AND !isset($AllowAnyone)) {
	$title = _('Account Error Report');
	include($PathPrefix . 'includes/header.inc');
	echo '<BR><BR><BR>';
	prnMsg(_('Security settings have not been defined for your user account. Please advise your system administrator. It could also be that there is a session problem with your PHP web server'),'error');
	include($PathPrefix . 'includes/footer.inc');
	exit;
}

if (!isset($AllowAnyone)){
	if ((!in_array($PageSecurity, $_SESSION['AllowedPageSecurityTokens']) OR !isset($PageSecurity))) {
		$title = _('Security Permissions Problem');
		include($PathPrefix . 'includes/header.inc');
		echo '<TR>
			<TD CLASS="menu_group_items">
				<TABLE WIDTH="100%" CLASS="table_index">
					<TR><TD CLASS="menu_group_item">';
		echo '<B><FONT SIZE="+1"><CENTER>' . _('The security settings on your account do not permit you to access this function') . '</FONT></B></CENTER>';

		echo '</TD>
		</TR>
		</TABLE>
		</TD>';

		include($PathPrefix . 'includes/footer.inc');
		exit;
	}

 }
 //Realhsot rleal
 //Se agregÃ³ este codigo, apra que funcione el path de donde se crea el PDF del documento.
 $_SESSION['reports_dir'] = 'companies/'.$_SESSION['DatabaseName'].'/reports';
 
 
 /* 2013-02-01 Rafael Rojas
  * Agregado permiso para index
 */
 //if($PageSecurity==0)
 {
 	$Pages = explode('/',$_SERVER['PHP_SELF']);
 	$Actual=trim($Pages[count($Pages)-1]," \t\r\0\n\x0b?/");
 	if($Actual=='index.php')
 		$PageSecurity=1;
 }/*
 */
if((isset($_SESSION['Permisos'])&&$_SESSION['Permisos']==1)){
	if((isset($_SESSION['BlockingPages'])) &&(count($_SESSION['BlockingPages'])>0)){
		$BlockPage=false;
		//$Actual=$Pages[count($Pages)-1];
		foreach($_SESSION['BlockingPages'] as $Pagina)
			if(strpos($Pagina,'index.php')===false&&strpos($Pagina,$Actual)!==false)
			$BlockPage=true;
		if($BlockPage){
			$title = _('Security Permissions Problem');
			include($PathPrefix . 'includes/header.inc');
			echo '<tr>
			<td class="menu_group_items">
			<table width="100%" class="table_index">
			<tr><td class="menu_group_item">';
			echo '<b><font style="size:+1; text-align:center;">' . _('The security settings on your account do not permit you to access this function') . '</font></b>';
	
			echo '</td>
			</tr>
			</table>
			</td>
			</tr>';
	
			include($PathPrefix . 'includes/footer.inc');
			exit;
		}
	}elseif((isset($_SESSION['BlockingPages'])) &&(count($_SESSION['BlockingPages'])==0)){
	 
	 }else{
	 	//$_SESSION['BlockingPages']=$PageLocks;
	 }
}
function CryptPass( $Password ) {
    	global $CryptFunction;
    	if ( $CryptFunction == 'sha1' ) {
    		return sha1($Password);
    	} elseif ( $CryptFunction == 'md5' ) {
    		return md5($Password);
	} else {
    		return $Password;
    	}
 }
if($_SESSION['CFDIDEMO']==1){
    $ws22User = 'DEMO';
    $ws22Pass = 'DEMO';
}else if($_SESSION['CFDIVersion']==22){
    if(($_SESSION['DatabaseName'] == 'relsa_erp_001')){

    }else if ($_SESSION['DatabaseName'] == 'cmi_erp_001'){

    }else if ($_SESSION['DatabaseName'] == 'rea_erp_001'){

    }
}
if(!isset($PermisosIndividualesseccion))
$PermisosIndividualesseccion=false;
if(!isset($OcultarDescuentoPedido))
$OcultarDescuentoPedido=false;
//****Aqui se pone el Password por empresa y cualquier variable que se desee que sea por empresa.
if ($_SESSION['DatabaseName'] == 'thermo_erp_001'){
    $passwordAdjustment="pres8663";
    $OcultarDescuentoPedido=
    	$_SESSION['AccessLevel']!=8;
    $PermisosIndividualesseccion=true;
    // addToIncludePath(realpath(realpath(dirname(__FILE__)).'/../rh_chat'));
}
if($_SESSION['DatabaseName'] == 'mexcompany_cfdi_001'){
    $rh_AllowCancelInvoice[]='alozano';
}
//***********************************************************************************************


if(isset($_SESSION['TCambio'])&&($_SESSION['TCambio']==1)){
    $sql="select fecha,valor from rh_tipo_cambio where date(fecha) < date(now()) order by fecha DESC limit 2";
    $Rs = DB_query($sql,$db);
    $loop=false;
    while($Rw=DB_fetch_array($Rs)){
      if($loop){
        $_SESSION['TCPREVIUS']=$Rw['valor'];
      }else{
        $_SESSION['TCACTUAL']=$Rw['valor'];
        $sql="update currencies set rate= 1/".$_SESSION['TCACTUAL']." where currabrev='USD'";
        DB_query($sql,$db);
        $loop=true;
      }
    }
}else{
    $sql="select * from currencies where currabrev='USD'";
    $Rs = DB_query($sql,$db);
    if($Rw=DB_fetch_array($Rs)){
        $_SESSION['TCACTUAL']=1/$Rw['rate']; 
    }
}


if(!isset($_SESSION['CLIMA'])){
   $_SESSION['CLIMA']=array();
   $sql = "select weathercode from rh_weather_yahoo where view=1;";
   $Rs = DB_query($sql,$db);
   $x=1;
   while($Rw=DB_fetch_array($Rs)){
     $_SESSION['CLIMA'][$x] = $Rw['weathercode'];
     $x++;
   }
}
