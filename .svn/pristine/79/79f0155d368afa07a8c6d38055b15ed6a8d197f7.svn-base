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
$query;

switch($_POST["request"]){
    case 'loadTableCfd':
        $folioDe = $_POST['folioDe'];
        $folioA = $_POST['folioA'];
        $fechaDe = $_POST['fechaDe'];
        $fechaA = $_POST['fechaA'];
        $rfcDelReceptor = $_POST['rfcDelReceptor'];
        $rfcDelEmisor = $_POST['rfcDelEmisor'];
        $year = null;
        $month = null;
        if($_POST['yearAndMonth'] != null){
            $yearAndMonth = $_POST['yearAndMonth'];
            $year = explode('-', $yearAndMonth);
            $year = $year[0];
            $month = explode('-', $yearAndMonth);
            $month = $month[1];
        }
        $query = "select id, extra_fecha_y_hora_de_cancelacion, comprobante_folio, comprobante_serie, comprobante_emisor_rfc, comprobante_emisor_nombre, comprobante_fecha, comprobante_receptor_nombre, comprobante_receptor_rfc, comprobante_sub_total, comprobante_total, concat(comprobante_no_certificado,'/',comprobante_serie,comprobante_folio,'-',fk_rh_transaddress_transno) xml, fk_rh_transaddress_transno pdf from rh_factura_electronica_reporte_mensual_sat where 1=1" .
        ($folioDe != null?" and comprobante_folio " . ($folioA != null?"between $folioDe and $folioA":"= $folioDe"):"") .
        ($fechaDe != null?" and comprobante_fecha " . ($fechaA != null?"between '$fechaDe' and DATE_ADD('$fechaA', INTERVAL 1 DAY)":" like '$fechaDe%'"):"") .
        ($rfcDelReceptor != null?" and comprobante_receptor_rfc = '$rfcDelReceptor'":"") .
        ($rfcDelEmisor != null && $rfcDelEmisor != ''?" and comprobante_emisor_rfc = '$rfcDelEmisor'":"").
        ($fechaDe == null && $year != null?" and year(fecha_y_hora_de_expedicion) = $year and month(fecha_y_hora_de_expedicion) = $month":"");
    break;
    case 'loadSelectReporteMensual':
        $rfcEmisor = $_POST['rfcEmisor'];
        $query = "select distinct year(fecha_y_hora_de_expedicion) year, month(fecha_y_hora_de_expedicion) month from rh_factura_electronica_reporte_mensual_sat where comprobante_emisor_rfc = '$rfcEmisor'";
    break;
    case 'loadSelectRfcDelEmisor':
        $query = "select concat(c.comprobante_emisor_rfc,' - ',c.comprobante_emisor_nombre) rfcEmisor from rh_factura_electronica_reporte_mensual_sat c group by c.comprobante_emisor_rfc";
    break;
    default:
        echo "Consulta invalida";
        return;
    break;
}
$resultado = DB_query($query, $db);
$arreglo = array();
while($objeto = mysql_fetch_object($resultado))
    $arreglo[] = $objeto;
print json_encode($arreglo);
return;
?>