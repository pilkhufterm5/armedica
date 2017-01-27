<?php
global $CarpetasXML;
$CarpetasXML=array();
$CFDI=0;
$CFD=1;
$CarpetasXML[$CFDI]="/XMLFacturacionElectronica/xmlbycfdi/";
$CarpetasXML[$CFD]="/XMLFacturacionElectronica/facturasElectronicas/";
$CarpetasXML[]="/";

$PageSecurity =1;
include('includes/session.inc');

// place this code inside a php file and call it f.e. "download.php"
$fullPath = trim($_GET['downloadPath'],'.'); // change the path to fit your websites document structure
$fullPath=str_replace('\\','/',$fullPath);
$data=explode("/",$fullPath);
$UUID=array_pop($data);
$info=array_pop($data);
$no_certificado=0;
if($info!='xmlbycfdi'){
	$no_certificado=$info;
	$UUID=$info.'/'.$UUID;
}
foreach($CarpetasXML as $directorio)
if(is_file(dirname(__FILE__).$directorio.$UUID)){
	$fullPath=dirname(__FILE__).$directorio.$UUID;
}
if(!is_file($fullPath)){
	if($no_certificado){
		list($folioser,$transno)=explode('-',substr($UUID,0,-4));
		$SQL="select xml,uuid from rh_cfd__cfd  where no_certificado='".DB_escape_string($no_certificado)."' and fk_transno='".DB_escape_string($transno)."'";
	}else
		$SQL="select xml, uuid from rh_cfd__cfd  where uuid='".DB_escape_string(substr($UUID,0,-4))."'";
	$res=DB_query($SQL,$db,'','',0,0);

	if(DB_num_rows($res)>0){
		$fila=DB_fetch_assoc($res);
		if(strlen(trim($fila['xml']))>0){
			if(strpos($fila['xml'],'cfdi'))
				$fullPath=dirname(__FILE__).$CarpetasXML[$CFDI].$fila['uuid'].'.xml';
			else
				$fullPath=dirname(__FILE__).$CarpetasXML[$CFD].$UUID;
			file_put_contents($fullPath,$fila['xml']);
		}
	}
}
$bom = "";

if ($fd = fopen ($fullPath, "r")) {
	
	
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);
    switch ($ext) {
        case "pdf":
        header("Content-type: application/pdf"); // add here more headers for diff. extensions
        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
        break;
        default;
	//$bom = "\xEF\xBB\xBF";
        header("Content-type: application/octet-stream");
        header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
    }
    //header("Content-length: $fsize");
    header("Cache-control: private"); //use this to open files directly
    //echo $bom;
    if(substr($fullPath,-4)=='.php'){
    	ob_start();
    	include_once $fullPath;
    	$xml = ob_get_contents();
    	ob_end_flush();
    }else
   		$xml = file_get_contents($fullPath);

    $xml = utf8_decode($xml);
    if($_SESSION['CFDIVersion']==32){
        if(strpos($xml,'xsi:schemaLocation')===false){
            $xml = str_replace('xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"', 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/cfd/2 http://www.sat.gob.mx/sitio_internet/cfd/2/cfdv22.xsd"',$xml);
        }
    }
    $xml = utf8_encode($xml);

    echo $xml;
}
fclose ($fd);

// example: place this kind of link into the document where the file download is offered:
// <a href="download.php?download_file=some_file.pdf">Download here</a>
