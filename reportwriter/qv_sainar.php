<?php  
exit;	 
$username="LuisBorrego";
$password="RH543xc";
$database="sainar_erp_001";
chdir(dirname(dirname(__FILE__))) ;
$AllowAnyone=1;
$DatabaseName=$database;
$PageSecurity = 1;

include_once('includes/session.inc');
$dom = new DOMDocument("1.0");
$node = $dom->createElement("Datos");
$parnode = $dom->appendChild($node); 
 
// Opens a connection to a MySQL server
 
$connection=$db;
//if (!$connection) {  die('Not connected : ' . mysql_error());} 
 
// Set the active MySQL database
 
//$db_selected = mysql_select_db($database, $connection);
/*
if (!$db_selected) {
  die ('Can\'t use db : ' . mysql_error());
} /**/
 
// Select all the rows in the markers table
 
$query = "
SELECT 'true' as is_cfd,
                    (rh_titular.folio) as AfilNo,
                    CONCAT(rh_titular.name, ' ', rh_titular.apellidos) as AfilName,
                    (rh_titular.movimientos_afiliacion) as AfilStatus,
                    (rh_cobranza.cobrador) as AfilCobrador,
                    (rh_titular.asesor) as AfilAsesor,
                    (rh_cobranza.stockid) as AfilProduct,
                    (rh_cobranza.frecuencia_pago) as AfilFrecuenciaPago,
                    (rh_cobranza.paymentid) as AfilMetodoPago,
                    CONCAT(rh_cfd__cfd.serie, '', rh_cfd__cfd.folio) as FolioFactura,
                    (rh_cfd__cfd.fecha) as FechaGenera,
                    max(dtrans2.trandate) as FechaPago,
                    (debtortrans.tipo_factura) as TipoFactura,
                    (debtortrans.rh_status) as StatusFactura,
                    (debtortrans.alloc) as LOPAGADO,
                    (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount- debtortrans.alloc) as SALDO,
                    (rh_foliosasignados.tipo_membresia) as TipoFolio,
                    rh_cfd__cfd.no_certificado,
                    rh_cfd__cfd.fk_transno,
                    rh_cfd__cfd.uuid,
                    debtorsmaster.name,
                    debtortrans.trandate,
                    rh_cfd__cfd.id_debtortrans,
                    debtortrans.ovamount/debtortrans.rate as ovamount,
                    debtortrans.ovgst/debtortrans.rate as ovgst,
                    (debtortrans.ovamount+debtortrans.ovgst)/debtortrans.rate as total,
                    debtortrans.id,
                    debtortrans.type
                    FROM debtortrans
                    LEFT JOIN  rh_cfd__cfd ON rh_cfd__cfd.id_debtortrans = debtortrans.id
                    JOIN debtorsmaster ON debtortrans.debtorno = debtorsmaster.debtorno
                    LEFT JOIN custallocns ca1 on debtortrans.id = ca1.transid_allocto
                    LEFT JOIN debtortrans dtrans2 on ca1.transid_allocfrom = dtrans2.id
                    LEFT JOIN rh_titular ON debtortrans.debtorno = rh_titular.debtorno
                    LEFT JOIN rh_cobranza ON rh_titular.folio = rh_cobranza.folio
                    LEFT JOIN rh_foliosasignados on rh_foliosasignados.folio = rh_titular.folio
                WHERE debtortrans.type in(10)GROUP BY debtortrans.id
";
$result = DB_query($query,$db);
if (!$result) {  
  die('Invalid query: ' . mysql_error());
} 
 
header("Content-type: text/xml"); 
 
// Iterate through the rows, adding XML nodes for each
 
while ($row = @DB_fetch_assoc($result)){  
  // ADD TO XML DOCUMENT NODE  
  $node = $dom->createElement("Dato");
  $newnode = $parnode->appendChild($node);  
$newnode->setAttribute("is_cfd",$row['is_cfd']);
$newnode->setAttribute("AfilNo",$row['AfilNo']);
$newnode->setAttribute("AfilName",$row['AfilName']);
$newnode->setAttribute("AfilStatus",$row['AfilStatus']);
$newnode->setAttribute("AfilCobrador",$row['AfilCobrador']);
$newnode->setAttribute("AfilAsesor",$row['AfilAsesor']);
$newnode->setAttribute("AfilProduct",$row['AfilProduct']);
$newnode->setAttribute("AfilFrecuenciaPago",$row['AfilFrecuenciaPago']);
$newnode->setAttribute("AfilMetodoPago",$row['AfilMetodoPago']);
$newnode->setAttribute("FolioFactura",$row['FolioFactura']);
$newnode->setAttribute("FechaGenera",$row['FechaGenera']);
$newnode->setAttribute("FechaPago",$row['FechaPago']);
$newnode->setAttribute("TipoFactura",$row['TipoFactura']);
$newnode->setAttribute("StatusFactura",$row['StatusFactura']);
$newnode->setAttribute("LOPAGADO",$row['LOPAGADO']);
$newnode->setAttribute("SALDO",$row['SALDO']);
$newnode->setAttribute("TipoFolio",$row['TipoFolio']);
$newnode->setAttribute("no_certificado",$row['no_certificado']);
$newnode->setAttribute("fk_transno",$row['fk_transno']);
$newnode->setAttribute("uuid",$row['uuid']);
$newnode->setAttribute("name",$row['name']);
$newnode->setAttribute("trandate",$row['trandate']);
$newnode->setAttribute("id_debtortrans",$row['id_debtortrans']);
$newnode->setAttribute("ovamount",$row['ovamount']);
$newnode->setAttribute("ovgst",$row['ovgst']);
$newnode->setAttribute("total",$row['total']);
$newnode->setAttribute("id",$row['id']);
$newnode->setAttribute("type",$row['type']);

 
} 
 
echo $dom->saveXML();
