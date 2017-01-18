<?php
//Php
$PageSecurity = 2;
include('includes/session.inc');
include('XMLFacturacionElectronica/utils/Comprobante.php');
include('XMLFacturacionElectronica/utils/Console.php');
include('XMLFacturacionElectronica/utils/Converter.php');
include('XMLFacturacionElectronica/utils/FacturaElectronica.php');
include('XMLFacturacionElectronica/utils/File.php');
include('XMLFacturacionElectronica/utils/Json.php');
include('XMLFacturacionElectronica/utils/Openssl.php');
include('XMLFacturacionElectronica/utils/Php.php');
require_once('Numbers/Words.php');
$arreglo = array();
switch($_POST["request"]){
    case "altaDeSello":
        try{
            if ( $_FILES['inputFileCertificado']['error'] != UPLOAD_ERR_OK )
                throw new Exception('No se subio el Certificado (.cer) correctamente');
            if ( $_FILES['inputFileLlavePrivada']['error'] != UPLOAD_ERR_OK )
                throw new Exception('No se subio la Llave Privada (.key) correctamente');
            $tmpCerPath = $_FILES['inputFileCertificado']['tmp_name'];
            $keyFilePath = $_FILES['inputFileLlavePrivada']['tmp_name'];
            $contrasenaDeLlavePrivada = $_POST['inputPasswordContrasenaDeLlavePrivada'];
            $cer = fread(fopen($tmpCerPath, "r"), filesize($tmpCerPath));
            $key = fread(fopen($keyFilePath, "r"), filesize($keyFilePath));
            $keyPass = $contrasenaDeLlavePrivada;
            $soapClient = getWs();
            $soapClient->csd(array('cer' => $cer, 'key' => $key, 'keyPass' => $keyPass));
            Php::relativeRedirect("rh_j_globalFacturacionElectronica.html.php?page=altaDeSello&msgType=success&msgAltaDeSello=Se dio de alta el Sello");
        }
        catch(Exception $e){
            Php::relativeRedirect("rh_j_globalFacturacionElectronica.html.php?page=altaDeSello&msgType=error&msgAltaDeSello=" . $e->getMessage());
        }
    break;
    case 'loadTableSello':
        $csds = getWs()->csds()->return;
        for($i = 0; $i < count($csds); $i++)
            $arreglo[] = $csds[$i]->item;
    break;
    default:
        echo("Consulta invalida");
        return;
    break;
}
print json_encode($arreglo);
?>