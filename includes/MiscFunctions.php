<?php
/**
 * El arreglo se puede definir en cualquier otro lado, se agrega aqui para las secciones que aun no lo tienen
 */
if(!isset($ServidoresdeCorreos)){
	$ServidoresdeCorreos=array();
	
	$ServidoresdeCorreos[] = array(
            'Host' => 'smtp.armedica.com.mx',
            'SMTPSecure' => "tls",
            'Port' => 587,
            'SMTPAuth' => true,
            'Username' => 'atencionclientes@armedica.com.mx',
            'Password' => '$_@R_%ReF_0003%_@r&',
        );
	}
if(false)
{//Se comentan los correos alternos
	$ServidoresdeCorreos[]=array(
	'Host' => 'ssl://smtp.gmail.com',
	'Port' => 465,
	'SMTPAuth' => true,
	'Username' => "envio@realhost.com.mx",
	'Password' => "47V94669",
	);
	$ServidoresdeCorreos[]=array(
	'Host' => 'ssl://smtp.gmail.com',
	'Port' => 465,
	'SMTPAuth' => true,
	'Username' => "envio2@realhost.com.mx",
	'Password' => "47V94669",
	);
	$ServidoresdeCorreos[]=array(
	'Host' => 'ssl://smtp.gmail.com',
	'Port' => 465,
	'SMTPAuth' => true,
	'Username' => "envio3@realhost.com.mx",
	'Password' => "47V94669",
	);
	$ServidoresdeCorreos[]=array(
	'Host' => 'ssl://smtp.gmail.com',
	'Port' => 465,
	'SMTPAuth' => true,
	'Username' => "envio4@realhost.com.mx",
	'Password' => "47V94669",
	);
	$ServidoresdeCorreos[]=array(
	'Host' => 'ssl://ssr6.supercp.com',
	'Port' => 465,
	'SMTPAuth' => true,
	'Username' => "envio@realhost.mx",
	'Password' => "GTrfg54ref",
	);	
	$ServidoresdeCorreos[]=array(
	'Host' => 'ssl://mail.realhost.mx',
	'Port' => 465,
	'SMTPAuth' => true,
	'Username' => "envio@realhost.mx",
	'Password' => "RRFt543erdw",
	);
}

/********************************************/
/** STANDARD MESSAGE HANDLING & FORMATTING **/
/********************************************/

function prnMsg($Msg, $Type = 'info', $Prefix = '') {

    echo getMsg($Msg, $Type, $Prefix);

}//prnMsg

function getMsg($Msg,$Type='info',$Prefix=''){
	global $MensajesGlobales;
	$File='';
	if(function_exists('debug_backtrace')){
		$deb=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,1);
		$id=$deb[0]['line'];
		$File=$deb[0]['file'];
	}else $id=rand(0,10000);
	if(!isset($MensajesGlobales))
		$MensajesGlobales=array();
	
	$Colour='';
	switch($Type){
		case 'error':
			$Class = 'error';
			$Prefix = $Prefix ? $Prefix : _('ERROR') . ' ' ._('Message Report');
			break;
		case 'warn':
			$Class = 'warn';
			$Prefix = $Prefix ? $Prefix : _('WARNING') . ' ' . _('Message Report');
			break;
		case 'success':
			$Class = 'success';
			$Prefix = $Prefix ? $Prefix : _('SUCCESS') . ' ' . _('Report');
			break;
		case 'info':
		default:
			$Prefix = $Prefix ? $Prefix : _('INFORMATION') . ' ' ._('Message');
			$Class = 'info';
	}
	$MensajesGlobales[]=array(
			'File'=>$File,
			'id'=>$id,
			'Msg'=>$Msg,
			'Type'=>$Type,
			'Prefix'=>$Prefix,
			'class'=>$Class
	);
	return '<DIV class="'.$Class.'"><B>' . $Prefix . '</B> : ' .$Msg . '</DIV>';
}//getMsg

function IsEmailAddress($TestEmailAddress) {

    /*thanks to Gavin Sharp for this regular expression to test validity of email addresses */

    if (function_exists('preg_match')) {
        if (preg_match("/^(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}$/", $TestEmailAddress)) {
            return true;
        } else {
            return false;
        }
    } else {
        if (strlen($TestEmailAddress)>5 AND strstr($TestEmailAddress, '@')>2 AND (strstr($TestEmailAddress, '.co')>3 OR strstr($TestEmailAddress, '.org')>3 OR strstr($TestEmailAddress, '.net')>3 OR strstr($TestEmailAddress, '.edu')>3 OR strstr($TestEmailAddress, '.biz')>3)) {
            return true;
        } else {
            return false;
        }
    }
}

Function ContainsIllegalCharacters($CheckVariable) {

    if (strstr($CheckVariable, "'") OR strstr($CheckVariable, '+') OR strstr($CheckVariable, "\"") OR strstr($CheckVariable, '&') OR strstr($CheckVariable, "\\") OR strstr($CheckVariable, '"')) {

        return true;
    } else {
        return false;
    }
}

function pre_var_dump(&$var) {
    echo "<div align=left><pre>";
    var_dump($var);
    echo "</pre></div>";
}

class XmlElement {
    var $name;
    var $attributes;
    var $content;
    var $children;
};

function GetECBCurrencyRates() {
    /* See http://www.ecb.int/stats/exchange/eurofxref/html/index.en.html
     for detail of the European Central Bank rates - published daily */
    $xml = file_get_contents('http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml');
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, $xml, $tags);
    xml_parser_free($parser);

    $elements = array();
    // the currently filling [child] XmlElement array
    $stack = array();
    foreach ($tags as $tag) {
        $index = count($elements);
        if ($tag['type']=="complete" || $tag['type']=="open") {
            $elements[$index] = new XmlElement;
            $elements[$index]->name = $tag['tag'];
            $elements[$index]->attributes = $tag['attributes'];
            $elements[$index]->content = $tag['value'];
            if ($tag['type']=="open") {// push
                $elements[$index]->children = array();
                $stack[count($stack)] = &$elements;
                $elements = &$elements[$index]->children;
            }
        }
        if ($tag['type']=="close") {// pop
            $elements = &$stack[count($stack) - 1];
            unset($stack[count($stack) - 1]);
        }
    }

    $Currencies = array();
    foreach ($elements[0]->children[2]->children[0]->children as $CurrencyDetails) {
        $Currencies[$CurrencyDetails->attributes['currency']] = $CurrencyDetails->attributes['rate'];
    }
    $Currencies['EUR'] = 1;
    //ECB delivers no rate for Euro
    //return an array of the currencies and rates
    return $Currencies;
}

function GetCurrencyRate($CurrCode, $CurrenciesArray) {
    if ((!isset($CurrenciesArray[$CurrCode]) or !isset($CurrenciesArray[$_SESSION['CompanyRecord']['currencydefault']]))) {
        return quote_oanda_currency($CurrCode);
    } elseif ($CurrCode=='EUR') {
        if ($CurrenciesArray[$_SESSION['CompanyRecord']['currencydefault']]==0) {
            return 0;
        } else {
            return 1 / $CurrenciesArray[$_SESSION['CompanyRecord']['currencydefault']];
        }
    } else {
        if ($CurrenciesArray[$_SESSION['CompanyRecord']['currencydefault']]==0) {
            return 0;
        } else {
            return $CurrenciesArray[$CurrCode] / $CurrenciesArray[$_SESSION['CompanyRecord']['currencydefault']];
        }
    }
}

function quote_oanda_currency($CurrCode) {
    $page = file('http://www.oanda.com/convert/fxdaily?value=1&redirected=1&exch=' . $CurrCode . '&format=CSV&dest=Get+Table&sel_list=' . $_SESSION['CompanyRecord']['currencydefault']);
    $match = array();
    preg_match('/(.+),(\w{3}),([0-9.]+),([0-9.]+)/i', implode('', $page), $match);

    if (sizeof($match)>0) {
        return $match[3];
    } else {
        return false;
    }
}

function AddCarriageReturns($str) {
    return str_replace('\r\n', chr(10), $str);
}

function getPermisosPagina($usuario = '', $pagina = '') {
    global $db;
    $Resultado = array();
    if ($pagina=='') {
        $d = debug_backtrace();
        $d = explode("/", $d[0]["file"]);
        $pagina = array_pop($d);
    }
    if ($usuario=='')
        $usuario = $_SESSION["UserID"];

    $SQL = "SELECT * FROM rh_usuario_permiso where userid='" . $usuario . "' AND filename='" . $pagina . "'";
    $res = DB_query($SQL, $db, '', '', 0, 0);
    if ($res)
        if (DB_num_rows($res)>0) {
            $fila = DB_fetch_assoc($res);
            $Resultado = unserialize($fila['prohibit']);
            $Resultado['id'] = $fila['id'];
        }
    /*
     create table rh_usuario_permiso(
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `userid` varchar(20) NOT NULL DEFAULT '',
     `filename` varchar(50) NOT NULL DEFAULT '',
     `prohibit` text,
     PRIMARY KEY (`id`),
     KEY `userid_f` (`userid`)
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
     */
    return $Resultado;
}

function setPermisosPagina($Resultado = array(), $usuario = '', $pagina = '') {
    global $db;
    if (!is_array($Resultado))
        $Resultado = array();
    if ($pagina=='') {
        $d = debug_backtrace();
        $d = explode("/", $d[0]["file"]);
        $pagina = array_pop($d);
    }
    if ($usuario=='')
        $usuario = $_SESSION["UserID"];

    $SQL = "SELECT * FROM rh_usuario_permiso where userid='" . $usuario . "' AND filename='" . $pagina . "'";
    $res = DB_query($SQL, $db, '', '', 0, 0);

    if ($res) {
        if (DB_num_rows($res)>0) {
            $fila = DB_fetch_assoc($res);
            $SQL = "UPDATE rh_usuario_permiso set prohibit='" . serialize($Resultado) . "' where id='" . $fila['id'] . "'";
        } else {
            $SQL = "INSERT INTO rh_usuario_permiso set userid='" . $usuario . "', filename='" . $pagina . "', prohibit='" . serialize($Resultado) . "'";
        }
        $res = DB_query($SQL, $db, '', '', 0, 0);
    }
    return $Resultado;
}

function SaveConfigUser($Arreglo, $contexto = '', $usuario = '') {
    global $SessionSavePath;

    if ($usuario=='')
        $usuario = $_SESSION["UserID"];
    if ($contexto=='') {
        $d = debug_backtrace();
        $d = explode("/", $d[0]["file"]);
        $contexto = array_pop($d);
    }
    if (!is_array($Arreglo)) {
        $Arreglo = array($Arreglo);
    }
    $Configuracion = $SessionSavePath;
    if ($Configuracion=="" || !is_writable($Configuracion))
        $Configuracion = realpath(dirname(__FILE__) . '/../tmp');
    $Configuracion .= "/Config_" . $usuario;
    if (!is_dir($Configuracion))
        mkdir($Configuracion);
    file_put_contents($Configuracion . "/" . $contexto, serialize($Arreglo));
}

function LoadConfigUser($contexto = '', $usuario = '') {
    global $SessionSavePath;

    if ($usuario=='')
        $usuario = $_SESSION["UserID"];
    if ($contexto=='') {
        $d = debug_backtrace();
        $d = explode("/", $d[0]["file"]);
        $contexto = array_pop($d);
    }
    if (!is_array($Arreglo)) {
        $Arreglo = array($Arreglo);
    }
    $Configuracion = $SessionSavePath;
    if ($Configuracion=="" || !is_writable($Configuracion))
        $Configuracion = realpath(dirname(__FILE__) . '/../tmp');
    $Configuracion .= "/Config_" . $usuario;
    if (!is_dir($Configuracion))
        mkdir($Configuracion);
    if (is_file($Configuracion . "/" . $contexto)) {
        $Arreglo = file_get_contents($Configuracion . "/" . $contexto);
        if ($Arreglo!='') {
            return (unserialize($Arreglo));
        }
    }
    return array();
}

function getTaxesForSO($Orderno) {
    global $db;
    $Impuestos = array();
    $SQL="select ".
        "salesorderdetails.orderlineno, ".
        "salesorderdetails.stkcode, ".
        "salesorderdetails.unitprice, ".
        "salesorderdetails.quantity, ".
        "salesorderdetails.unitprice*salesorderdetails.quantity Total, ".
        "taxgrouptaxes.calculationorder, ".
        "taxauthorities.description, ".
        "taxgrouptaxes.taxauthid, ".
        "taxauthorities.taxglcode, ".
        "taxgrouptaxes.taxontax, ".
        "taxauthrates.taxrate ".
    "from ".
        "salesorderdetails  ".
        "left join stockmaster on stockmaster.stockid=salesorderdetails.stkcode ".
        "left join salesorders on salesorders.orderno=salesorderdetails.orderno ".
        "left join custbranch on salesorders.branchcode = custbranch.branchcode and salesorders.debtorno = custbranch.debtorno ".
        "left join locations on salesorders.fromstkloc=locations.loccode ".
        "left join taxauthrates on taxauthrates.dispatchtaxprovince=locations.taxprovinceid AND taxauthrates.taxcatid = stockmaster.taxcatid ".
        "INNER JOIN taxgrouptaxes ON taxauthrates.taxauthority=taxgrouptaxes.taxauthid and taxgrouptaxes.taxgroupid=custbranch.taxgroupid ".
        "INNER JOIN taxauthorities ON taxauthrates.taxauthority=taxauthorities.taxid ".
    "where ".
    "salesorderdetails.orderno='".DB_escape_string($Orderno)."'".
    " ORDER BY taxgrouptaxes.calculationorder ";
    $query = DB_query($SQL, $db, '', '', 0, 0);
    while ($f = DB_fetch_assoc($query)) {
        $Impuestos[] = $f;
    }
    return $Impuestos;
}

function getTaxTotalLinesForSO($Orderno) {
    $impuestos = getTaxesForSO($Orderno);
    $TaxTotal = array();
    foreach ($impuestos as $impuesto) {
        if (!isset($TaxTotal[$impuesto['orderlineno']]))
            $TaxTotal[$impuesto['orderlineno']] = 0;
        $TaxTotal[$impuesto['orderlineno']] += $impuesto['taxrate'] * ($impuesto['Total'] + $TaxTotal[$impuesto['orderlineno']] * $impuesto['taxontax']);
    }
    return $TaxTotal;
}

function getTaxTotalForSO($Orderno) {
    return array_sum(getTaxTotalLinesForSO($Orderno));
}

/**
 * @param $From Desde donde se esta enviando, puede ser "nombre<direccion@correo.com>"
 * @param $To a donde lo enviaremos, puede ser array o separados por punto y coma ; 
 * @param $Subject el tituylo del mensaje
 * @param $Mensaje El cuerpo del mensaje
 * @param $adjuntos un array asociativo con los archivos adjuntos este contiene 3 principales indices ruta, nombre, archivo
 * 		La ruta es la ruta fisica desde el directorio del sistema $fileBasePath, 
 * 		El nombre, es el nombre que se le dara al archivo para descargar, si se da el parametro anterior y no este, toma el del path.
 * 		archivo, es el archivo en a ser adjunto
 */
function EnviarMail($from='Servicio de Alertas',$To='gerardoangeln@gmail.com',$Subject='Prueba',$Mensaje='Mensaje prueba',$adjuntos=array(),$BCC='',$repplyTo=''){
	global $ServidoresdeCorreos, $db;
	$mail_error='';
	if(!class_exists('PHPMailer'))
		include_once("PHPMailer_v5.1/class.phpmailer.php");
	//$Correos=$ServidoresdeCorreos[0];
	foreach($ServidoresdeCorreos as $Correos){
		$mail = new PHPMailer();
		$mail->IsSMTP(); // send via SMTP
		$mail->Host = $Correos['Host'];
		$mail->Port = $Correos['Port'];
		$mail->SMTPAuth = isset($Correos['SMTPAuth']); // turn on SMTP authentication
		$mail->Username = $Correos['Username']; // SMTP username
		$mail->Password = $Correos['Password']; // SMTP password
		$mail->Subject = ($Subject);
		$mail->IsHTML(strpos(' '.$Mensaje,'<')&&strpos(' '.$Mensaje,'>'));
		$mail->Body =$Mensaje;
		
		$from=explode('<',trim($from,'>'));
		if(count($from)>1)
			$mail->AddReplyTo($from[1]);
		$from=$from[0];
		
		$mail->SetFrom($Correos['Username'], ($from));
		if(!is_array($To))
			$To=explode(';',str_replace(array(',','|'),';',$To));
		foreach ($To as $value){
			$mail->AddAddress($value);
		}
		
		if(!is_array($repplyTo))
			$repplyTo=explode(';',str_replace(array(',','|'),';',$repplyTo));
		if(count($repplyTo)>0)
			foreach ($repplyTo as $value){
				$mail->AddReplyTo($value);
			}
			
		if(!is_array($BCC))
			$BCC=explode(';',str_replace(array(',','|'),';',$BCC));
		if(count($BCC)>0)
			foreach ($BCC as $value){
				$mail->AddBCC($value);
			}
		
		if(count($adjuntos)>0)
			foreach($adjuntos as $Archivos){
				if(isset($Archivos['ruta'])&&is_file($Archivos['ruta'])){
					if(!isset($Archivos['nombre']))
						$Archivos['nombre']=array_pop(explode('/',$Archivos['ruta']));
					$mail->AddAttachment($Archivos['ruta'], $Archivos['nombre']); // attachment
				}
				if(isset($Archivos['archivo'])&&isset($Archivos['nombre']))
					$mail->AddStringAttachment($Archivos['archivo'],$Archivos['nombre']);
			}
		$mail_success = $mail->Send();
		if(!$mail_success){
		    echo $mail_error = $mail->ErrorInfo;
		}else{
			$mail = null;
			break;
		}
		$mail = null;
		echo '<br>';
	}
	if($mail_success)
		return '';
	return $mail_error;
}
if (!function_exists('Session_register')) {
    function Session_register($sesion) {
        if (!isset($_SESSION[$sesion]))
            $_SESSION[$sesion] = "";
    }

}

function UpdateSerieLocacion($StockID,$Serie='', $location='',$actualizarLocacion=true){
	global $db;
	$Seriex='';
	if(is_array($Serie)){
		if(is_array($Serie)&&count($Serie)>0){
		foreach($Serie as $locod=>$val)
			$Seriex[$locod]=DB_escape_string($Serie[$locod]);
		}else
			$Seriex=array($Serie);
		$Seriex=implode("', '",$Seriex);
	}
	$sql="select sum(sm.qty)quantity, sm.loccode, sm.stockid, ss.serialno from stockserialmoves ss join stockmoves sm on ss.stockmoveno=sm.stkmoveno ";
	if($Seriex!='')
		$sql.=" where ss.serialno in('".
			$Seriex.
		"')";
	else
		$sql.=" where ss.stockid in('".
			$StockID.
		"')";
	$sql.=" group by sm.loccode, sm.stockid, ss.serialno";
	$result_items = DB_query($sql,$db);
	while($fila=DB_fetch_assoc($result_items)){
		$sql="select count(*) t from stockserialitems where ";
		$set=
		$sep="";
		foreach($fila as $Llave=>$Valor)
			if($Llave!='quantity'){
			$set.=$sep.$Llave.'="'.DB_escape_string($Valor).'" ';
			$sep="and  ";
		}
		$res = DB_query($sql.$set,$db);
		$total=DB_fetch_assoc($res);
		if($total['t']==0){
			$Seriex2=DB_escape_string($Seriex);
			if($Seriex=='')$Seriex2=DB_escape_string($fila['serialno']);
			$sql="insert into stockserialitems select stockid, '".$fila['loccode']."', serialno, expirationdate, '".$fila['quantity']."', qualitytext from stockserialitems where serialno in('$Seriex');";
			DB_query($sql,$db);
		}
	}
	$SQL="select ";
	$SQL.=" stockserialmoves.stockid, ";
	$SQL.=" stockmoves.loccode, ";
	$SQL.=" stockserialmoves.serialno, ";
	$SQL.=" sum(stockserialmoves.moveqty) quantity ";
	$SQL.=" from ";
	$SQL.=" stockserialmoves join stockmoves on stockmoves.stkmoveno=stockserialmoves.stockmoveno ";
	$SQL.=" where ";
	$SQL.=" stockserialmoves.stockid = '".DB_escape_string($StockID)."' ";
	if(is_array($Serie)&&count($Serie)>0){
		foreach($Serie as $locod=>$val)
			$Serie[$locod]=DB_escape_string($Serie[$locod]);
		$SQL.=" AND stockserialmoves.serialno  in ('".implode("', '",$Serie)."') ";
	}else
	if(trim($Serie)!='')
		$SQL.=" AND stockserialmoves.serialno ='".DB_escape_string($Serie)."' ";
	if(is_array($location)&&count($location)>0){
		foreach($location as $locod=>$val)
			$location[$locod]=DB_escape_string($location[$locod]);
		$SQL.=" AND stockmoves.loccode in ('".implode("', '",$location)."') ";
	}else if(trim($location)!='')
		$SQL.=" AND stockmoves.loccode='".DB_escape_string($location)."' ";
	$SQL.=" group by stockserialmoves.stockid,stockserialmoves.serialno,stockmoves.loccode ";
	$result_items = DB_query($SQL,$db);
	while($fila=DB_fetch_assoc($result_items)){
		
		$SQL="select count(*)t from stockserialitems where stockid='".DB_escape_string($fila['stockid'])."' and loccode='".DB_escape_string($fila['loccode'])."' and serialno= '".DB_escape_string($fila['serialno'])."'";
		$stk= DB_query($SQL,$db);
		$filax=DB_fetch_assoc($stk);
		if($filax['t']==0){
			DB_query("insert into stockserialitems(stockid,loccode,serialno,quantity)value('".DB_escape_string($fila['stockid'])."','".DB_escape_string($fila['loccode'])."','".DB_escape_string($fila['serialno'])."','".DB_escape_string($fila['quantity'])."')",$db,'','',0,0);
		}else
			DB_query("update stockserialitems set quantity ='".DB_escape_string($fila['quantity'])."' where stockid='".DB_escape_string($fila['stockid'])."' and loccode ='".DB_escape_string($fila['loccode'])."' and serialno='".DB_escape_string($fila['serialno'])."'",$db,'','',0,0);
		if($actualizarLocacion)
			DB_query("call update_locstock('".DB_escape_string($fila['loccode'])."','".DB_escape_string($fila['stockid'])."');",$db,'','',0,0);
	}
}
function GetConfig($Configuracion){
	global $db;
	$SQL="select * from config where confname='".DB_escape_string($Configuracion)."'";
	$Auth_Result = DB_query($SQL, $db);
	$res=DB_fetch_assoc($Auth_Result);
	return $res['confvalue'];
}
function UpdateConfig($Configuracion,$Valor){
	global $db;
	$Config=GetConfig($Configuracion);
	if(is_null($Config)){
		$SQL="insert into config values('".DB_escape_string($Configuracion)."','".DB_escape_string($Valor)."') ";
	}else 
		$SQL="update config set confvalue='".DB_escape_string($Valor)."' where confname='".DB_escape_string($Configuracion)."' ";
	if($Config!=$Valor)
		$Auth_Result = DB_query($SQL, $db);
	return GetConfig($Configuracion);
}
if(!function_exists("AsignarMontoDocumentoProveedor")){
	function AsignarMontoDocumentoProveedor($reciboID,$facturaID,$monto=0){
			if($monto==0) return false;
			global $db;
			{
				$PreLlamada=1;
				$GET=$_GET;
				$REQUEST=$_REQUEST;
				$POST=$_POST;
				$_POS=$_GET=$_POST=array();
				
				ob_start();
				unset($_SESSION['AllocTrans']);
				unset($_SESSION['Alloc']);
				unset($_POST['AllocTrans']);
				$_POST['AllocTrans']=$_GET['AllocTrans']=$reciboID;
				//Pre llenado del carrito
				$error=include "SupplierAllocations.php";
				if($error){
					$Counter = 0;
					$RowCounter = 0;
			        $TotalAllocated = 0;
					//Asignacion de montos en el post
			        foreach ($_SESSION['Alloc']->Allocs as $id=>$AllocnItem) {
			        	$YetToAlloc = ($AllocnItem->TransAmount - $AllocnItem->PrevAlloc);
			        	$_POST['YetToAlloc'.$Counter]=$YetToAlloc;
			        	$_POST['Amt'.$Counter]= $AllocnItem->AllocAmt;
			        	$_POST['AllocID'.$Counter]= $AllocnItem->ID;
			        	if($AllocnItem->ID==$facturaID){
			        		$_SESSION['Alloc']->Allocs[$id]->AllocAmt=$monto;
			        		$_POST['Amt'.$Counter]+=$monto;
			        	}
			        	$Counter++;	
			        }
			        $_POST['TotalNumberOfAllocs']=$Counter;
			        $_POST['RefreshAllocTotal']=  _('Recalculate Total To Allocate');
					/////Actualizacion del carrito
					$error=include "SupplierAllocations.php";
					if($error){
						unset($_POST['RefreshAllocTotal']);
						///Procesado de la peticion
						$_POST['UpdateDatabase']= _('Process Allocations');
						$error=include "SupplierAllocations.php";
					}
				}
			};
			ob_clean();
			$_GET=$GET;
			$_REQUEST=$REQUEST;
			$_POST=$POST;
			$_POST=$POST;
	}
}
function AgregarImpresion($podetailitem,$grnno=0,$barcode='',$impresiones=1){
	global $db;
	if(trim($barcode)=='')return false;
	if($impresiones==0)$impresiones=1;
	$impresiones=abs($impresiones);
	if($podetailitem==0){
		$sql="select * from rh_etiquetas_impresion where barcode='".DB_escape_String($barcode)."' order by impreso desc limit 1";
		$res=DB_query($sql,$db);
		$fila=DB_fetch_assoc($res);
		$podetailitem=$fila['podetailitem'];
		$grnno=$fila['grnno'];
	}
	$sql="insert into rh_etiquetas_impresion(podetailitem, grnno, barcode, impresiones, creado)values(";
	$sql.="'".DB_escape_String($podetailitem)."',";
	$sql.="'".DB_escape_String($grnno)."',";
	$sql.="'".DB_escape_String($barcode)."',";
	$sql.="'".DB_escape_String($impresiones)."',";
	$sql.="now()";
	$sql.=")";
	DB_query($sql,$db);
	return DB_Last_Insert_ID($db);
	
}
function GenerarEtiqueta($id_agrupador,$stockid,$serie,$exp_date){
	global $db;
	$sql = "select count(*) r,barcode from rh_etiquetas where stockid='".DB_escape_String($stockid)."' and serialno = '".DB_escape_string(trim($serie))."'";
	$res = DB_query($sql,$db);	
	$resultado = DB_fetch_assoc($res);	

	if($resultado['r'] > 0)
	{
		return $resultado['barcode'];
	}
	
	$sql_insert = "insert into rh_etiquetas (id_agrupador,stockid,serialno, expirationdate) values('".DB_escape_string(trim($id_agrupador))."','".DB_escape_string(trim($stockid))."','".DB_escape_string(trim($serie))."','".DB_escape_string(trim($exp_date))."')";
	$resultado_insert = DB_query($sql_insert,$db);
	$id = DB_Last_Insert_ID($db,'rh_etiquetas','id');
	
	$gen_barcode = getbarcode($id,13);

	$sql_update = "update rh_etiquetas set barcode = '".DB_escape_string(trim($gen_barcode))."' where id = ".DB_escape_string($id)."";
	DB_query($sql_update,$db);
 	return $gen_barcode;
}
function getbarcode($id,$size)
{
	$size_2 = $size - 1;
	$new_code = str_pad($id, $size_2,"0",STR_PAD_LEFT);
	return $new_code.calculaDigito($new_code);
}
	
function calculaDigito($cadena){
        $segRaiz = $cadena; 
        $chrCaracter = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-_ "; 
        $intFactor = array(); 
        $lngSuma = 0.0; 
        $lngDigito = 0.0; 
        for($i=0; $i<strlen($cadena); $i++){ 
            for($j=0; $j<strlen($chrCharacter); $j++){ 
                if(substr($segRaiz,$i,1)==substr($chrCaracter,$j,1)){ 
                    $intFactor[$i]=$j; 
                } 
            } 
        } 
        for($k = 0; $k < strlen($cadena); $k++){ 
            $lngSuma= $lngSuma + (($intFactor[$k]) * (strlen($cadena)+1 - $k)); 
        } 
        $lngDigito= (10 - ($lngSuma % 10)); 
        if($lngDigito==10){ 
            $lngDigito=0; 
        } 
       return $lngDigito; 

}
