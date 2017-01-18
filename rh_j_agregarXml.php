<?php
$PageSecurity = 12;
include('includes/session.inc');
include('XMLFacturacionElectronica/utils/Comprobante.php');
include('XMLFacturacionElectronica/utils/Console.php');
include('XMLFacturacionElectronica/utils/Converter.php');
include('XMLFacturacionElectronica/utils/FacturaElectronica.php');
include('XMLFacturacionElectronica/utils/File.php');
include('XMLFacturacionElectronica/utils/Json.php');
include('XMLFacturacionElectronica/utils/Openssl.php');
include('XMLFacturacionElectronica/utils/Php.php');
$xslFilePath = 'XMLFacturacionElectronica/satFiles/cadenaoriginal_2_0.xslt';
//$dir = '/opt/lampp/htdocs/facturacionElectronicaSVN/REALHOST_ERP308/XMLFacturacionElectronica/facturasElectronicas/00001000000101270479';
$dir = 'XMLFacturacionElectronica/facturasElectronicas/00001000000101270479';
try{
    if(!DB_query('begin',$db,'','',false,false)){
        throw new Exception('Error al efectuar el begin' , 1);
    }
    if ($handle = opendir($dir)) {
    //    echo "Directory handle: $handle\n";
    //    echo "Files:\n";

        /* This is the correct way to loop over the directory. */
        //obtenemos el folio mas grande y actualizamos el siguiente folio por el folio mas grande mas 1
        $folioMasGrande = 0;
        $hayArchivos = false;
        while (false !== ($file = readdir($handle))) {
            if(!preg_match('/^\..*/', $file)){
                $hayArchivos = true;
                echo 'archivo->' . $file . '</br>';
                $a = array();
                preg_match('/([A-Z]+)([0-9]+)\-([^\.]+).*/', $file, $a);
                echo 'serie->' . $a[1] . '</br>';
                echo 'folio->' . $a[2] . '</br>';
                echo 'transno->' . $a[3] . '</br>';
                $serie = $a[1];
                $folio = $a[2];
                $transno = $a[3];
                FacturaElectronica::guardarDatosParaReporteMensual($transno, "$dir/$file", $xslFilePath);
                if($folioMasGrande<$folio)
                    $folioMasGrande = $folio;
                echo 'guardado...</br></br>';
            }
        }
        if(!$hayArchivos){
            throw new Exception('El directorio no contiene archivos');
        }
        closedir($handle);
        $folioMasGrande++;
        $sql = "update rh_factura_electronica_folio set folio_actual = $folioMasGrande where rfc = 'REA080108FS0'";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_affected_rows($db)!=1){
            throw new Exception('Error al actualizar el folio ' . $sql , 1);
        }
        if(!DB_query('commit',$db,'','',false,false)){
            throw new Exception('Error al efectuar el commit' , 1);
        }
    }
}
catch(Exception $exception){
    $msg = $exception->getMessage();
    if($exception->getCode()==1){
        $error = mysql_error();
        $msg .= ' (SQL' . ($error?': ' . $error:'') . ')';
    }
    if(!DB_query('rollback',$db,'','',false,false)){
        $msg .= ' (Error al efectuar el rollback)';
    }
    throw new Exception($msg);
    return;
}

//    /* This is the WRONG way to loop over the directory. */
//    while ($file = readdir($handle)) {
//        echo "$file";
//    }
//FacturaElectronica::guardarDatosParaReporteMensual($transno, $xmlFilePath, $xslFilePath);
//include('includes/footer.inc');
?>
