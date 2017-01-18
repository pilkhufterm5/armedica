<?php
$PageSecurity = 2;

include ('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
include('XMLFacturacionElectronica/utils/File.php');  
$title = _ ( 'Refirma de XML' );
include ('includes/header.inc');
require_once('CFD22Control.php');
require_once('CFD22Manager.php');

function libxml_display_error($error){
    $return = "<br/>\n";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "<b>Warning $error->code</b>: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "<b>Error $error->code</b>: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "<b>Fatal Error $error->code</b>: ";
            break;
    }
    $return .= trim($error->message);
    if ($error->file) {
        $return .=    " in <b>$error->file</b>";
    }
    $return .= " on line <b>$error->line</b>\n";
    return $return;
}

function libxml_display_errors() {
    $Errors="";
    $errors = libxml_get_errors();
    foreach ($errors as $error) {
       $Errors .= libxml_display_error($error);
    }
    libxml_clear_errors();
    return $Errors;
}

try{

    if(isset($_FILES['xml'])){
      /*  $flag=false;
        if(mkdir('importfiles',0777)){
            $flag=true;
        } else if (chdir('importfiles')){
            $flag=true;
        }else{
            throw new Exception(_('No se tienen permisos de escritura, asegurece de tener permisos suficientes'));
        }
        if($flag){
            $p=chdir('importfiles');
            $path=realpath($p);
            $path = str_replace('\\','/',$path);
        }

      if (!move_uploaded_file($_FILES['im']['tmp_name'],$path.'/'.$_FILES['im']['name'])){
            throw new Exception("No es posible Cargar el Archivo");
      }       */
    libxml_use_internal_errors(true);
    $xml = new DOMDocument();
    $xml->load($_FILES['xml']['tmp_name']);
    if (!$xml->schemaValidate('cfdv22.xsd')) {
        $OnError=true;
        throw new Exception(libxml_display_errors());
    }
    $Comprobantes = $xml->getElementsByTagName("Comprobante");
    foreach($Comprobantes as $Comprobante){
      $Certificado =  $Comprobante->getAttribute('noCertificado');
    }
    //var_dump($Certificado);
    //base64_encode($xml->saveXML());
    //echo ;
        $CFDControl = CFD22Control::getInstance();
        $CFDManager = CFD22Manager::getInstance();
        $MyXML = $CFDControl->ResignXML($Certificado,base64_encode($xml->saveXML()));
        $XMLSerie=$CFDControl->getXMLSerie();
        $XMLFolio=$CFDControl->getXMLFolio();
        $Sello = $CFDControl->getXMLSello();
        $OString = $CFDManager->getCadena($XMLSerie,$XMLFolio);



        $SQL = "select id,fk_transno from rh_cfd__cfd where serie ='$XMLSerie' and folio='$XMLFolio'";
        $rs = DB_query($SQL,$db);
        if($rw = DB_fetch_array($rs)){
            $ID=$rw['id'];
            $Transno = $rw['fk_transno'];
        }else{
            throw new Exception('Imposible Localizar Transaccion');
        }

        if(!file_exists("XMLFacturacionElectronica/facturasElectronicas/$Certificado/"))
            if(!mkdir("XMLFacturacionElectronica/facturasElectronicas/$Certificado"))
                throw new Exception('No se pudo crear el directorio donde se guardara el CFD');

        $filename = "XMLFacturacionElectronica/facturasElectronicas/$Certificado/$XMLSerie$XMLFolio-$Transno.xml";
        File::createFile($MyXML, $filename);

        $sqlInsert = "update rh_cfd__cfd set cadena_original='".mysql_real_escape_string($OString)."' , sello = '$Sello', xml = '".mysql_real_escape_string($MyXML)."' where id='$ID'";

        $result = DB_query($sqlInsert,$db);
        if(DB_error_no($db)){
            throw new Exception('No se pudieron insertar los datos del CFD en la base de datos local.');
        }

         echo '<br/><A target="_blank" HREF="'.$rootpath."/rh_j_downloadFacturaElectronicaXML.php?downloadPath=XMLFacturacionElectronica/facturasElectronicas/" . $Certificado . "/" . $XMLSerie . $XMLFolio . '-' . $Transno .'.xml'. SID . '">'._('Descargar Factura Electronica en formato XML').'</A><BR>';

    }
}catch(Exception $e){
    echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' . $e . '<p></div>';
}
?>

<form name="Form" method="POST" enctype="multipart/form-data"
	style="width: 100%;" action="">

<center>
<table cellpadding="0" cellspacing="2" borde="0" width="35%">
	<tr>
		<td colspan="2" align="center"><b>Retimbrado de XML</b></td>
	</tr>
	<tr>
		<td>XML a Timbrar:</td>
		<td><input type="file" name="xml" style="width:100%;" />
        </td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<input type="submit" name="Resign" value="Refirmar XML" style="width:100%;display:inline;float:left" />
		</td>
    </tr>
</table>
</center>
</form>
<br />
<br />
<?
include ('includes/footer.inc');
?>