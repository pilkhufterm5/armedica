<?php
////prueba
//$cert = file_get_contents('../fact_elec/Cer_Sello/aaa010101aaa_CSD_01.key.pem');
//$ssl = openssl_x509_parse($cert);
//print_r($ssl);
//$serie=$ssl['subject']['serialNumber'];
//echo "serie = $serie\n";
//return;
////termina prueba

include 'XMLFacturacionElectronica/utils/Console.php';
include 'XMLFacturacionElectronica/utils/File.php';
include 'XMLFacturacionElectronica/utils/FacturaElectronica.php';
include 'XMLFacturacionElectronica/utils/Converter.php';
include 'XMLFacturacionElectronica/utils/Openssl.php';
include 'XMLFacturacionElectronica/utils/Comprobante.php';

//Para obtener los valores adecuados dependiendo el contribuyente
$idFolio = $_POST['selectIdFolio'];
$sql = "select f.id, f.folio_actual, f.serie, f.no_aprobacion, f.ano_aprobacion, f.rfc, c.certificado_no_certificado from rh_factura_electronica_folio f, rh_factura_electronica_certificado c where f.id_rh_factura_electronica_certificado = c.id and serie = (select serie from rh_factura_electronica_folio where id = $idFolio limit 1) and rfc = (select rfc from rh_factura_electronica_folio where id = $idFolio limit 1) and folio_final >= folio_actual order by folio_inicial limit 1";
$result = DB_query($sql,$db,'','',false,false);
if(mysql_errno($db) || mysql_num_rows($result)!=1){
    throw new Exception('Se acabaron los folios para esta serie', 1);
}
$row = mysql_fetch_array($result, MYSQL_ASSOC);
$idFolio = $row['id'];
$siguienteFolio = $row['folio_actual'];
$serie = $row['serie'];
$noAprobacion = $row['no_aprobacion'];
$anoAprobacion = $row['ano_aprobacion'];
$rfc = $row['rfc'];
$noCertificado = $row['certificado_no_certificado'];

//Actualizamos el folio usado
$sql = "update rh_factura_electronica_folio set folio_actual = (folio_actual + 1) where id = $idFolio limit 1";
$result = DB_query($sql,$db,'','',false,false);
if(mysql_errno($db) || mysql_affected_rows($db)!=1) {
    throw new Exception('No se pudo actualizar el estado del siguiente folio', 1);
}
//Termina Actualizamos el folio usado
//Termina Para obtener los valores adecuados dependiendo el contribuyente
$xmlFilePath = 'XMLFacturacionElectronica/userSatFiles/xml.xml';
$xsdFilePath = 'XMLFacturacionElectronica/satFiles/cfdv2.xsd';
$xslFilePath = 'XMLFacturacionElectronica/satFiles/cadenaoriginal_2_0.xslt';
$keyFilePath = "XMLFacturacionElectronica/sellos/$noCertificado/key.key";
$passwordFilePath = "XMLFacturacionElectronica/sellos/$noCertificado/password.password";
$cerFilePath = "XMLFacturacionElectronica/sellos/$noCertificado/cer.cer";
$tmpPemFilePath='XMLFacturacionElectronica/tmpSatFiles/pem.pem';

        //transno declarado en ConfirmDispatch_Invoice.php
        $idFacturaElectronica = $transno;
//        try{
            //Crear .XML, aqui se hacen los queries correspondientes para contruir el objeto que crea la Factura Electronica
            Comprobante::create($xmlFilePath, $idFacturaElectronica, $idFolio, $siguienteFolio, $serie, $noCertificado, $noAprobacion, $anoAprobacion, $rfc, $cerFilePath, $tmpPemFilePath);
            //Termina Crear .XML

            if(!(@$facturaElectronica = simplexml_load_file($xmlFilePath)))
                throw new Exception('El .XML no esta bien formado');
            FacturaElectronica::validateCrearFactura($tmpPemFilePath, $xmlFilePath, $xsdFilePath, $cerFilePath);
            //$xslFilePath debe ser siempre el mismo archivo (nos permite transformar el xml a cadena original)
            $cadenaOriginal = FacturaElectronica::calculateCadenaOriginal($xslFilePath, $xmlFilePath);
            //el .key es la llave privada del proveedor, el .password es su password
            $selloDigital = FacturaElectronica::calculateSelloDigital($cerFilePath, $tmpPemFilePath, $xslFilePath, $xmlFilePath, $keyFilePath, $passwordFilePath);
            FacturaElectronica::writeSelloDigitalInXml($xmlFilePath, $selloDigital);
            //creamos el directorio donde se guardaran las facturas electronicas
            if(!file_exists("XMLFacturacionElectronica/facturasElectronicas/$noCertificado/"))
                if(!mkdir("XMLFacturacionElectronica/facturasElectronicas/$noCertificado"))
                    throw new Exception('No se pudo crear el directorio donde se guardaran los certificados');
            //Evita problemas de sincronizacion
            $sql = "select folio_actual, folio_final from rh_factura_electronica_folio where id = $idFolio limit 1";
            $result = DB_query($sql,$db,'','',false,false);
            if(mysql_errno($db) || mysql_num_rows($result)!=1) {
                throw new Exception('Se acabaron los folios para esta serie', 1);
            }
            $row = mysql_fetch_array($result);
            if($row['folio_actual']>$row['folio_final']){
                throw new Exception('Se acaban de terminar los folios de esta serie');
            }
            //Termina Evita problemas de sincronizacion
            //Se guardan todos los datos del CFD en la db
            FacturaElectronica::guardarDatosParaReporteMensual($idFacturaElectronica, $xmlFilePath, $xslFilePath);
            //Termina Se guardan todos los datos del CFD en la db
            //movemos la factura electronica final...
            if(!rename($xmlFilePath, "XMLFacturacionElectronica/facturasElectronicas/$noCertificado/$serie$siguienteFolio-$idFacturaElectronica.xml")){
                throw new Exception('No se pudo mover el .XML');
            }
            //Termina movemos la factura electronica final...
?>
