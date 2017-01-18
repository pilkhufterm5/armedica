<?php

/* webERP 3.05 Revision: 1.15 $ */
/* webERP 3.08 Revision: 1.16 $ */
/**
 * REALHOST 2008
 * $LastChangedDate$
 * $Rev$
 */

define ('LIKE','LIKE');

// bowikaxu realhost Feb 2008 - verify acces level.
if($_SESSION['AccessLevel']==$rh_solover_role){
	$db = mysql_connect($host , $rh_dbsolover_user, $rh_dbsolover_pass);
}else {
	$db = mysql_connect($host , $dbuser, $dbpassword);
}

if ( !$db ) {
	echo '<BR>' . _('The configuration in the file config.php for the database user name and password do not provide the information required to connect to the database server');
	exit;
}

if (! mysql_select_db($_SESSION['DatabaseName'],$db)) {
	echo '<BR>' . _('The company name entered does not correspond to a database on the database server specified in the config.php configuration file. Try logging in with a different company name');
	echo '<BR><A HREF="index.php">' . _('Back to login page') . '</A>';
	unset ($_SESSION['DatabaseName']);
	exit;
}

require_once ($PathPrefix .'includes/MiscFunctions.php');

function pr($obj)
{
      echo "<pre><font color=red>";
      if(is_object($obj) || is_array($obj)) {
          print_r ($obj);
      } else {
         echo $obj;
      } 
      echo "</font></pre>";
}

function sql_dq($query)
{
    global $db;
	
	return mysql_query($query,$db);
}
function da_xcess($type,$data,$deep=false,$processing=null,$return="data") {

		if(is_array($data)) {

		if(!$deep)
		 $data=array();

		foreach($data as $k=>$v) {

			$tmp=da_xcess($type,$v,false,$processing,$return);

			if($tmp!==null)
			 $data[$k]=$tmp;
			else 
			 unset($data[$k]);

		}

		// ...

		$cond=count($data) ? true : false ;

		// undo conversion?

		if(!$deep or !$deep[1])
		 $data=implode($deep[0],$data);

	}
	else {

		// data type validation

		$type=is_array($type) ? $type : explode(",",$type);
		$cond=-1;

		foreach($type as $type_tmp) {

			// basic check

			if(is_null($data))
			break;

			// practical processing

			if($type_tmp=="time" and mb_strlen($data)==5) // sample, "12:20" is converted to "12:20:00"
			$data.=":00";
			if($type_tmp=="timestamp" and mb_strlen($data)==10) // sample, "2009-12-04" is converted to "2009-12-04 00:00:00"
			$data.=" 00:00:00";
			if($type_tmp=="timestamp" and mb_strlen($data)==16) // sample, "2009-12-04 00:00" is converted to "2009-12-04 00:00:00"
			$data.=":00";
			if($type_tmp=="timestamp" and mb_strlen($data)>19) // sample, "0000-00-00T00:00:00.00" is converted to "0000-00-00T00:00:00"
			$data=mb_substr($data,0,19);
			if($type_tmp=="timestamp" and $data{10}=="T") // sample, "0000-00-00T00:00:00" is converted to "0000-00-00 00:00:00"
			$data{10}=" ";

			// do validation

			$cond=(

				($type_tmp=="boolean" and in_array($data,array(0,1,"0","1",false,true),true))
			or	($type_tmp=="numeric" and is_numeric($data))
			or	($type_tmp=="fill" and mb_strlen($data)>0 and is_scalar($data))
			or	($type_tmp=="string" and is_scalar($data))
			or	($type_tmp=="date" and mb_strlen(($data_tmp=str_replace("-","",$data)))==8 and is_numeric($data_tmp))
			or	($type_tmp=="time" and mb_strlen(($data_tmp=str_replace(":","",$data)))==6 and is_numeric($data_tmp))
			or	($type_tmp=="timestamp" and mb_strlen(($data_tmp=str_replace(array("-"," ",":"),"",$data)))==14 and is_numeric($data_tmp))
			or	($type_tmp=="timestamp_partial" and is_numeric(($data_tmp=str_replace(array("-"," ",":","_","%"),"",$data)))) // sample :: "2014-01-%"
			or	($type_tmp=="none")

				) ? true : false ;

			if(!$cond)
			break;

		}

		if($cond===-1)
		$cond=false;

		// data processing?

		if($cond) {

			if(!is_array($processing))
			 $processing=explode(",",$processing);

			foreach($processing as $v) {

				if(!$v)
				 continue;

				// refering to a php function?

				if(is_string($v)) {

					if(function_exists($v))
					 { eval(" \$data=".$v."(\$data); "); }
					else
					 $data=null;

				}

			}

		}

	}

	if($return=="data")
	 return $cond ? ( !is_array($data) ? $data : array_merge($data) ) : null ;
	else
	 return $cond;

}

function sql_qb_NEW($data,$concat_mode=null,$parenthesis=null,&$QUERY_COND=null,&$URL_QUERY=null,&$FIELDS_AFFECTED=null) {

if(count($data)>1 aND !$concat_mode)
 return false;

$query_cond=array();
$url_query=array();
$fields_affected=array();

	foreach($data as $k=>$v) {

		if(!is_array($v) or $v[2]===null)
		 continue;

		// processing... 
		// - focus on mantain easy-to-understand fo anybody on da future
		// - use underscores on vars name to avoid conflict with possible overwriting of function params { like $concat_mode }

		if(!is_array($v[3])) $v[3]=array();
		if(!is_array($v[4])) $v[4]=array();

		$_name=!is_array($v[0]) ? array("field"=>$v[0],"url_parameter"=>$v[0]) : array("field"=>$v[0][0],"url_parameter"=>$v[0][1]) ;
		$_comparison_operator=$v[1];
		$_value=$v[2];
		$_comparison_operator_config=array("valid"=>$v[3][1],"url_parameter"=>$v[3][2]);
		$_value_config=array("multiple"=>array("enabled"=>( $v[4][0] ? true : false ),"separator"=>( (is_scalar($v[4][0]) and !is_bool($v[4][0])) ? $v[4][0] : "" )),"delimiter_start"=>$v[4][1],"delimiter_end"=>$v[4][2],"quotes_enclosure"=>( !isset($v[4][3]) ? true : $v[4][3] ),"quotes"=>( (!isset($v[4][3]) or $v[4][3]) ? "'" : "" ));
		$_concat_mode=$v[5];
		$_parenthesis=$v[6];

		// validation

		if( !$_name["field"] or !$_name["url_parameter"]
			or ( !$_comparison_operator or ($_comparison_operator_config["valid"] and (!is_array($_comparison_operator_config["valid"]) or !in_array($_comparison_operator,$_comparison_operator_config["valid"]))) ) )
		 continue;

		// d0!

		$tmp=array();

		if($_value_config["multiple"]["enabled"]) {

			if(!$_concat_mode)
			 continue;

			if(!is_array($_value))
			 $_value=$_value_config["multiple"]["separator"] ? explode($_value_config["multiple"]["separator"],$_value) : array($_value) ;

			if(!count($_value))
			 continue;

			if(!is_array($_comparison_operator) or count($_comparison_operator) < count($_value))
			 $_comparison_operator=array_fill(count($_comparison_operator)-1,count($_value),$_comparison_operator);

			foreach($_value AS $k2=>$v2) {

				$tmp[]="{$_name["field"]} ".$_comparison_operator[$k2]." ".$_value_config["quotes"]."{$_value_config["delimiter_start"]}$v2{$_value_config["delimiter_end"]}".$_value_config["quotes"];
				$url_query[]="{$_name["url_parameter"]}[]=$v2";

			 }

		}
		else {

			if(is_array($_value))
			 { /*if(isset($_value[0])) $_value=$_value[0];  else */continue; } // the commented area was intentional

			$tmp[]="{$_name["field"]} {$_comparison_operator} ".$_value_config["quotes"]."{$_value_config["delimiter_start"]}$_value{$_value_config["delimiter_end"]}".$_value_config["quotes"];
			$url_query[]="{$_name["url_parameter"]}=$_value";

		}

		// ...join
		
		$nt=count($tmp);
		
		if($nt OR ($nt>1 aND $_concat_mode)) {

			$query_cond[]=(!$_parenthesis or $nt < 2) ? implode(" $_concat_mode ",$tmp) : "(".implode(" $_concat_mode ",$tmp).")" ;
			$fields_affected[$_name["field"]]=$nt;

			if(!$_comparison_operator_config["url_parameter"])
			 {}
			else if(!is_array($_comparison_operator))
			 $url_query[]="{$_comparison_operator_config["url_parameter"]}={$_comparison_operator}";
			else
			 foreach($_comparison_operator as $k2=>$v2) { $url_query[]="{$_comparison_operator_config["url_parameter"]}[$k2]={$v2}"; }

		}

	}

	// finish
  
	if($query_cond) {

		$query_cond=(!$parenthesis OR count($query_cond) < 2) ? implode(" $concat_mode ",$query_cond) : "(".implode(" $concat_mode ",$query_cond).")" ;
		$url_query=implode("&",$url_query);

		if($QUERY_COND!==null) !is_array($QUERY_COND) ? $QUERY_COND.=$query_cond : $QUERY_COND[]=$query_cond ;
		if($URL_QUERY!==null) !is_array($URL_QUERY) ? $URL_QUERY.=$url_query : $URL_QUERY[]=$url_query ;
		if($FIELDS_AFFECTED!==null) !is_array($FIELDS_AFFECTED) ? $fields_affected : $FIELDS_AFFECTED=array_merge($FIELDS_AFFECTED,$fields_affected) ;

		return ($QUERY_COND!==null Or $URL_QUERY!==null oR $FIELDS_AFFECTED!==null) ? true : array($query_cond,$url_query,$fields_affected) ;
	}else{

	 return false;
	}

}
function comisiones($i){
	
	if($i > 6500 and $i < 8499):
		echo "20% de comision";
	elseif($i > 8500 and $i < 9999):
		echo "50% de comision";
	elseif($i > 10000 and $i < 11999):
		echo "80% de comision";
	elseif($i > 12000):
		echo "100% de comision";
	endif;
	
}

//DB wrapper functions to change only once for whole application

function DB_query ($SQL,
		&$Conn,
		$ErrorMessage='',
		$DebugMessage= '',
		$Transaction=false,
		$TrapErrors=true,$Charset='utf8'){

	global $debug;
	global $PathPrefix;
	
	// bowikaxu realhost Feb 2008 - get global var $rh_solover_role
	global $rh_solover_role;
	global $SQL_MENSAJE,$Charsets;
        if(!isset($SQL_MENSAJE)){
                $SQL_MENSAJE=array();
                class Mensajero__SQL{
                        private $activo=false;
                        function __construct($Activo){
                                $this->activo=$Activo;
                        }
                        function __destruct(){
                                global $SQL_MENSAJE;
				
                                if($this->activo&&$SQL_MENSAJE!=null){
                                        echo "<pre>";
                                        var_dump($SQL_MENSAJE);
                                        echo "</pre>";
                                }
                        }
                }
                global $P;
                $P=new Mensajero__SQL(isset($_COOKIE['debug']));
        }
	if(isset($_COOKIE['debug']))
        $SQL_MENSAJE[]=$SQL;
	//echo $SQL."<BR><BR>";Se omite esta linea, es mejor usar la linea de arriba para mostrar todo al final
	//Agregado cambio de charset
	if(!isset($Charsets)){
		$Charsets=array();//Buscamos los soportados por mysql
		$result=mysql_query("SHOW CHARACTER SET",$Conn);
		if($result)
		while($Char=mysql_fetch_assoc($result))
			$Charsets[]=$Char['Charset'];
	}
	if(in_array($Charset,$Charsets)===false)
		$Charset='utf8';
    mysql_set_charset($Charset,$Conn);
    //mysql_set_charset('set names utf8',$Conn);
    //mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $Conn);
	$result=mysql_query($SQL,$Conn);
	$_SESSION['LastInsertId'] = mysql_insert_id($Conn);
	
	if ($DebugMessage == '') {
		$DebugMessage = _('The SQL that failed was');
	}

	if (DB_error_no($Conn) != 0 AND $TrapErrors==true){

		// bowikaxu realhost Feb 2008 - rol de permiso solo ver
		if($_SESSION['AccessLevel']==$rh_solover_role){
			prnMsg(_('Sorry, you are not authorized to perform that action'),'error',_('Database Error'));
			include($PathPrefix . 'includes/footer.inc');
			exit;
		}else {
			$error=DB_error_msg($Conn);
			if ($TrapErrors){
				require_once($PathPrefix . 'includes/header.inc');
			}
			prnMsg($ErrorMessage.'<BR>' . DB_error_msg($Conn),'error', _('Database Error'));
			if ($debug==1){
				prnMsg($DebugMessage. "<BR>$SQL<BR>",'error',_('Database SQL Failure'));
			}
			if ($Transaction){
				$SQL = 'rollback';
				$Result = DB_query($SQL,$Conn);
				if (DB_error_no($Conn) !=0){
					prnMsg(_('Error Rolling Back Transaction'), '', _('Database Rollback Error') );
				}
			}
			$debug=debug_backtrace();
			array_unshift($debug,$error);
			EnviaMensajeError($debug,$Conn);
			if ($TrapErrors){
				include($PathPrefix . 'includes/footer.inc');
				exit;
			}
		}
	}elseif (DB_error_no($Conn)==0 AND $_SESSION['MonthsAuditTrail']>0){
		
		$SQLArray = explode(' ', $SQL);
	
		if (($SQLArray[0] == 'INSERT') 
			OR ($SQLArray[0] == 'UPDATE') 
			OR ($SQLArray[0] == 'DELETE')) {
			
			if ($SQLArray[2]!='audittrail'){ // to ensure the auto delete of audit trail history is not logged
/****************************************************************************************************************************
* Jorge Garcia
* 30/Ene/2009 Guardar la IP y archivo de donde se origino el cambio
****************************************************************************************************************************/
/*				$AuditSQL = "INSERT INTO audittrail (transactiondate,
									userid,
									querystring,
								rh_ip, rh_file)
						VALUES('" . Date('Y-m-d H:i:s') . "',
							'" . trim($_SESSION['UserID']) . "',
							'" . DB_escape_string($SQL) . "',
							'".$_SERVER['REMOTE_ADDR']."','".basename($_SERVER['SCRIPT_NAME'])."')";
 *
 */

                                /*
                                 * rleal
                                 * Jul 21 2010
                                 * Se quita el logueo de la tabla de chartdetails
                                 */
                                $string_a_quitar = "chartdetails";
                                $string_a_quitar2 = "UPDATE gltrans SET posted";
                                $compara=strpos(DB_escape_string($SQL), $string_a_quitar);
                                $compara2=strpos(DB_escape_string($SQL), $string_a_quitar2);
                                //echo "||".$compara."//////".$compara2."\\\\\<hr>";
                                if  ($compara===false)  {
                                        if  ($compara2===false) {
                                $AuditSQL = "INSERT INTO audittrail (transactiondate,
                                                                        userid,
                                                                        querystring,
                                                                rh_ip, rh_file)
                                                VALUES('" . Date('Y-m-d H:i:s') . "',
                                                        '" . trim($_SESSION['UserID']) . "',
                                                        '" . DB_escape_string($SQL) . "',
                                                        '".$_SERVER['REMOTE_ADDR']."','".basename($_SERVER['SCRIPT_NAME'])."')";
                                }
                                }

/****************************************************************************************************************************
* Jorge Garcia Fin Modificacion
****************************************************************************************************************************/
				$AuditResult = mysql_query($AuditSQL,$Conn);
			}
		}
	}
	return $result;

}

function DB_fetch_row (&$ResultIndex) {

	$RowPointer=mysql_fetch_row($ResultIndex);
	Return $RowPointer;
}

function DB_fetch_assoc (&$ResultIndex) {

	$RowPointer=mysql_fetch_assoc($ResultIndex);
	Return $RowPointer;
}

function DB_fetch_array (&$ResultIndex) {

	$RowPointer=mysql_fetch_array($ResultIndex);
	Return $RowPointer;
}

function DB_data_seek (&$ResultIndex,$Record) {
	mysql_data_seek($ResultIndex,$Record);
}

function DB_free_result (&$ResultIndex){
	mysql_free_result($ResultIndex);
}

function DB_num_rows (&$ResultIndex){
	return mysql_num_rows($ResultIndex);
}
// Added by MGT
function DB_affected_rows(&$ResultIndex){
	return mysql_affected_rows($ResultIndex);
}

function DB_error_no (&$Conn){
	return mysql_errno($Conn);
}

function DB_error_msg(&$Conn){
	return mysql_error($Conn);
}

function DB_Last_Insert_ID(&$Conn,$table, $fieldname){
//	return mysql_insert_id($Conn);
	if (isset($_SESSION['LastInsertId'])) {
		$Last_Insert_ID = $_SESSION['LastInsertId'];
	} else {
		$Last_Insert_ID = 0;
	}
//	unset($_SESSION['LastInsertId']);
	return $Last_Insert_ID;
}

function DB_escape_string($String){
	Return mysql_real_escape_string($String);
}

function DB_show_tables(&$Conn){
	$Result = DB_query('SHOW TABLES',$Conn);
	Return $Result;
}

function DB_show_fields($TableName, &$Conn){
	$Result = DB_query("DESCRIBE $TableName",$Conn);
	Return $Result;
}

function INTERVAL( $val, $Inter ){
		global $dbtype;
		return "\n".'INTERVAL ' . $val . ' '. $Inter."\n";
}

function DB_Maintenance($Conn){

	prnMsg(_('The system has just run the regular database administration and optimisation routine.'),'info');
	
	$TablesResult = DB_query('SHOW TABLES',$Conn);
	while ($myrow = DB_fetch_row($TablesResult)){
		$Result = DB_query('OPTIMIZE TABLE ' . $myrow[0],$Conn);
	}

	$Result = DB_query("UPDATE config 
				SET confvalue='" . Date('Y-m-d') . "' 
				WHERE confname='DB_Maintenance_LastRun'",
				$Conn);
}
function rh_impresion($String2){
	$String2 = str_replace('\r\n','
',$String2);
	$String2 = stripslashes(stripslashes($String2));
	Return $String2;
}

function DB_Txn_Begin($Conn){
	mysql_query('SET autocommit=0;',$Conn);
	mysql_query('START TRANSACTION',$Conn);
}

function DB_Txn_Commit($Conn){
	mysql_query('COMMIT',$Conn );
	mysql_query('SET autocommit=1;',$Conn);
}

function DB_Txn_Rollback($Conn){
    mysql_query('ROLLBACK', $Conn );
}


function EnviaMensajeError($Error,$db){
	$Correos='gerardo.delangel@armedica.com.mx;alicia.villarreal@armedica.com.mx;';
	ob_start();
	echo "GET:";
	var_dump($_GET);
	echo "POST:";
	var_dump($_POST);
	echo 'Usiario:';
	var_dump($_SESSION['UserID']);
	echo "Backtrace:";
	var_dump($Error);
	$d=ob_get_contents();
	ob_end_clean();
	// 'ENVELOPE' (U+2709)
	$d='<pre>'.
	$_SERVER["HTTP_HOST"].
	$_SERVER["REQUEST_URI"].
	"\n".html_entity_decode("&#9993;")."Error Numero ".DB_error_no($db).' '.DB_error_msg($db)." \n".$d.'</pre>';
	include_once('MiscFunctions.php');

	EnviarMail('Servicio de alerta',
		$Correos,'Problemas en: ' . $_SESSION['DatabaseName'],
		$d);
}