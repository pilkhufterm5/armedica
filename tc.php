<?php
ob_start();
function TCBanxico(){
	$fecha_tc='';
	$tc='';
	$client = new SoapClient(null, array('location' => 'http://www.banxico.org.mx:80/DgieWSWeb/DgieWS?WSDL',
    	'uri' => 'http://DgieWSWeb/DgieWS?WSDL',
		'encoding' => 'ISO-8859-1', 'trace' => 1));
	try
	{
		$dom = new DomDocument();
		$dom->loadXML($client->tiposDeCambioBanxico());

		$xmlDatos = $dom->getElementsByTagName( "Obs" );
		$i=1;
		//for($i=0;$i<$xmlDatos->length;$i++)
		if($xmlDatos->length>1){
			$item = $xmlDatos->item($i);
			$fecha_tc = ($item->getAttribute('TIME_PERIOD'));
			$tc = $item->getAttribute('OBS_VALUE');
			return array($fecha_tc=>$tc);
		}
	}
	catch (SoapFault $exception)
	{
	}
	return array();
}
function TCDof($Finicio='',$Ffin=''){
	if($Finicio=='')
		$Finicio=date("d/m/Y",strtotime("-3 days"));
	else
		$Finicio=date("d/m/Y",strtotime($Finicio));
	if($Ffin=='')
		$Ffin=date("d/m/Y",time());
	else
		$Ffin=date("d/m/Y",strtotime($Ffin));
	$doc = new DOMDocument();
	$Url="http://dof.gob.mx/indicadores_detalle.php?cod_tipo_indicador=158&dfecha=".urlencode("$Finicio")."&hfecha=".urlencode("$Ffin");
	ob_start();
	$Contenido=file_get_contents($Url);
	if($Contenido===false)
		return TCBanxico();
	$doc->loadHTML($Contenido);
	unset($Contenido);
	ob_end_clean();
	$Nodo=null;
	$TazaDeCambio=array();
	foreach($doc->getElementsByTagName("table") as $tabla){
		for($i=0;$tabla->attributes->length>$i;$i++){
			if($tabla->attributes->item($i)->nodeName=='class' && $tabla->attributes->item($i)->nodeValue=='Tabla_borde'){
				$Fecha="";
				foreach($tabla->childNodes as $fila){
					foreach($fila->childNodes as $Celda){
						if($Celda->nodeName=='td'){
							if($Fecha=='')
								$Fecha=date("Ymd",strtotime($Celda->nodeValue));
							else{
								$valor= $Celda->nodeValue;
								if(((float)$valor)!=0)
									$TazaDeCambio[$Fecha]=$valor;
								$Fecha='';
							}
						}
					}
				}
			}
		}
	}
	return $TazaDeCambio;
}

$PathPrefix=dirname(realpath(__FILE__))."/";
include_once $PathPrefix."config.php";
if(isset($_SERVER['argv'])&&isset($_SERVER['argv'][1])){
	$DefaultCompany=$_SERVER['argv'][1];
	if(is_file($PathPrefix.'/companies/'.$DefaultCompany.'/config.php'))
		include_once $PathPrefix.'/companies/'.$DefaultCompany.'/config.php';

}
$_SESSION['DatabaseName']=$DefaultCompany;
include_once ($PathPrefix."includes/ConnectDB_" . $dbType . ".inc");
foreach(TCDof() as $fecha => $TC){
	if(((int)$TC)!=0){
		$id=0;
		$fecha=date("Y-m-d",strtotime($fecha));
		if(DB_num_rows($sql_id=DB_query("select * from rh_tipo_cambio where fecha='$fecha'", $db))!=0){
			$fila=DB_fetch_row($sql_id);
			$id=$fila[0];
		}
		DB_query("replace into rh_tipo_cambio(id, fecha, valor) values($id,'$fecha','$TC')", $db);
	}
}
header("location: index.php");
ob_end_clean();
