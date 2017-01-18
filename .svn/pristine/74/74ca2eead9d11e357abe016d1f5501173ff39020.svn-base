<?php
ini_set("memory_limit","512M");
$PageSecurity = 2;
ini_set("display_errors",0);
include('includes/session.inc');

$title = _('Aged Supplier Balances/Overdues Report');
//include('includes/header.inc');
include('XMLFacturacionElectronica/utils/Php.php');
require_once 'includes/dompdf-master/dompdf_config.inc.php';
//require_once 'includes/dompdf-master/dompdf_config.inc.php';
function nf($cantidad){
	return number_format($cantidad ,2);
}

 if(!function_exists("pre_var_dump")){
 function pre_var_dump($variable){
 	echo "<pre>";
 	var_dump($variable);
 	echo "</pre>";
 }
 }
// pre_var_dump($_REQUEST);

 /**
  * Filtros
  */
/* echo <<<table
 <center>
<form action ='AgedDebtorshtml2.php' method='post' id='forma'>
<table>
	<tr><td>Del Proveedor C&oacute;digo:</td><td><input type ='text' name='proveedorDe' id='proveedorDe'/></td></tr>
    <tr><td>Al Proveedor C&oacute;digo:</td><td><input type ='text' name='proveedorAl' id='proveedorAl'/></td></tr>
    <tr><td>Todos los saldos o solo atrasados:</td><td><select name='cualesSaldos' id='cualesSaldos' ><option value='conSaldo'>Todos los clietes con saldo</option><option value='conMora'>Sólo cuentas en mora</option></select></td></tr>
    <tr><td>Resumen o informe detallado:</td><td><select name='resumenDetallado' id='resumenDetallado' ><option value='resumen'>Resumen</option><option value='detalle'>Informe Detallado</option></select></td></tr>
    <tr><td>Solo Facturas: <br />
			Nota: Incluyen facturas <br />
			posiblemente pagadas no asignadas
       		</td><td><input type ='checkbox'  name='facturas' name='facturas' id='facturas' value='1'/></td></tr>
   <tr><td colspan='2' align='center'><input type='submit' id='PDF' name='PDF' value='Imprime PDF'/><input type='submit' id='XLS' name='XLS' value='Excel'/><input type='submit' id='ver' name='ver' value='VER'/></td></tr>
	</tr>
</table>
</form>
 </center>
table;*/

 if(isset($_GET['ver']) || isset($_GET['XLS']) || isset($_GET['PDF']))
 {

 	$betweenSupp = "";
 	/**
 	 * Cuando se hace un filtro entre clave y clave de proveedor
 	 */
 	if(isset($_GET['proveedorDe']) && !empty($_GET['proveedorDe']) && isset($_GET['proveedorAl']) && !empty($_GET['proveedorAl']))
 	{
 		$proveedorDe = mysql_real_escape_string($_GET['proveedorDe']);
 		$proveedorAl = mysql_real_escape_string($_GET['proveedorAl']);
 		$betweenSupp = " AND d.debtorno between '{$proveedorDe}' and '{$proveedorAl}' ";
 	}

 	/**
 	 * Cuando se selecciona entre Clientes con mora y clientes con saldo
 	 */
 	$soloMora = "";
 	if(isset($_GET['cualesSaldos']) && !empty($_GET['cualesSaldos'])){
 		$soloMora = "  HAVING porVencer is NULL ";

 	}




 /***
  *Query Principal
  *
  */
 $SQL = "select d.debtorno,
	d.name,
	d.paymentterms,
	sum(t.ovamount+t.ovgst - alloc) AS saldo,

  (SELECT sum(ovamount+ovgst - alloc) AS vener
   FROM debtortrans
   WHERE (TO_DAYS(trandate) + d.paymentterms)> TO_DAYS(from_unixtime(unix_timestamp()))
     AND debtorno =t.debtorno
	 AND settled = 0) AS porVencer,

  (SELECT sum(ovamount+ovgst - alloc)
   FROM debtortrans
   WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (TO_DAYS(trandate) + d.paymentterms)) BETWEEN 1 AND 29
     AND debtorno =t.debtorno
     AND settled = 0) AS v1a30,

  (SELECT sum(ovamount+ovgst - alloc)
   FROM debtortrans
   WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (TO_DAYS(trandate) + d.paymentterms)) BETWEEN 30 AND 59
     AND debtorno =t.debtorno
     AND settled = 0) AS v31a60,

  (SELECT sum(ovamount+ovgst - alloc)
   FROM debtortrans
   WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (TO_DAYS(trandate) + d.paymentterms)) BETWEEN 60 AND 89
     AND debtorno =t.debtorno
     AND settled = 0) AS v61a90,

  (SELECT sum(ovamount+ovgst - alloc)
   FROM debtortrans
   WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (TO_DAYS(trandate) + d.paymentterms)) BETWEEN 90 AND 119
     AND debtorno =t.debtorno
     AND settled = 0) AS v91a120,

  (SELECT sum(ovamount+ovgst - alloc)
   FROM debtortrans
   WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (TO_DAYS(trandate) + d.paymentterms)) >= 120
     AND debtorno =t.debtorno
     AND settled = 0) AS v91aMas

FROM debtorsmaster d,
	     debtortrans t
	WHERE d.debtorno = t.debtorno
	AND (t.ovamount+t.ovgst - alloc) > 1
	AND t.settled = 0
  {$betweenSupp}
GROUP BY d.debtorno
{$soloMora}
ORDER BY d.debtorno DESC";
  //echo $SQL;

 $rs = DB_query($SQL,$db,'','',False,False);
 $HeadingLine1 = _('Aged Supplier Balances For Customers from') . ' ' . $_GET['FromCriteria'] . ' ' .  _('to') . ' ' . $_GET['ToCriteria'];


 $nomTabla="";
 if($_GET['resumenDetallado']!="detalle"){
 	$nomTabla ="myTable";
 }


 $html = "<table id='{$nomTabla}'  width='100%' cellpadding='3' border='1' cellspacing='0' style='font-size:7px'>";
 $html .= "<thead><tr><td colspan='6'>{$HeadingLine1}</td><td colspan='3'>"._('Printed').": ".Date("d M Y")." </td></tr>";
 $html .= "<tr style='background-color:#cccce5; color:#330000; font-weight:bold; cursor:pointer;'><th><b>Cliente</b></th><th><!--<b>Plazos de pago</b>//--></th><th><b>Saldo</b></th><th><!--<b>Por Vencer</b>//--></th><th><b>Por Vencer</b></th><th><b>> 30 dias</b></th><th><b>> 60 dias</b></th><th><b>> 90 dias</b></th><th><b>> 120 dias</b></th></tr></thead><tbody>";
 $plazos ="";
 $bg="CCC";
 $totals= array();
 while(($row = DB_fetch_assoc($rs,$db))){
$bg=($bg=="CCC")?"EEE":"CCC";
if($row['paymentterms']==0 || $row['paymentterms']=="CA")
	$plazos = "CONTADO";
else
	$plazos = $row['paymentterms']." DIAS";




	$totals['saldo'] += $row['saldo'];
	$totals['porVencer'] += $row['porVencer'];
	$totals['v1a30'] += $row['v1a30'];
	$totals['v31a60'] += $row['v31a60'];
	$totals['v61a90'] += $row['v61a90'];
	$totals['v91a120'] += $row['v91a120'];
	$totals['v91aMas'] += $row['v91aMas'];

		$html .= "<tr bgcolor='#{$bg}'><td>&nbsp;{$row['debtorno']} - {$row['name']}</td><td>&nbsp;<!--{$plazos}//--></td><td align='right'>&nbsp;".nf($row['saldo'])."</td><td align='right'>&nbsp;<!--".nf($row['porVencer'])."//--></td><td align='right'>&nbsp;".nf($row['v1a30'])."</td><td align='right'>&nbsp;".nf($row['v31a60'])."</td><td align='right'>&nbsp;".nf($row['v61a90'])."</td><td align='right'>&nbsp;".nf($row['v91a120'])."</td><td align='right'>&nbsp;".nf($row['v91aMas'])."</td></tr>";

	/**
	 * Resumen detallado
	 */
	//pre_var_dump($_POST['resumenDetallado']);
	if($_GET['resumenDetallado']=="detalle"){

	$SQL2 = "SELECT d.debtorno,
       t.transno,
       d.name,
       d.paymentterms,
       (t.ovamount+t.ovgst - alloc) AS saldo,
       (TO_DAYS(from_unixtime(unix_timestamp())) - (d.paymentterms + TO_DAYS(t.trandate))) AS DIAS,
        concat('<table width=\'100%\' height=\'100%\' ><tr><td>',st.typename,'</td><td> ',t.reference,'</td><td>(',t.transno,')</td><td> ',t.trandate,'</td></tr></table>') AS des,

  (SELECT sum(ovamount+ovgst - alloc) AS vener
   FROM debtortrans
   WHERE (TO_DAYS(trandate) + d.paymentterms)> TO_DAYS(from_unixtime(unix_timestamp()))
     AND transno =t.transno
     AND settled = 0) AS porVencer,

  (SELECT sum(ovamount+ovgst - alloc)
   FROM debtortrans
   WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (d.paymentterms + TO_DAYS(trandate))) BETWEEN 1 AND 29
     AND transno =t.transno
     AND settled = 0) AS v1a30,

  (SELECT sum(ovamount+ovgst - alloc)
   FROM debtortrans
   WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (d.paymentterms + TO_DAYS(trandate))) BETWEEN 30 AND 59
     AND transno =t.transno
     AND settled = 0) AS v31a60,

  (SELECT sum(ovamount+ovgst - alloc)
   FROM debtortrans
   WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (d.paymentterms + TO_DAYS(trandate))) BETWEEN 60 AND 89
     AND transno =t.transno
     AND settled = 0) AS v61a90,

  (SELECT sum(ovamount+ovgst - alloc)
   FROM debtortrans
   WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (d.paymentterms + TO_DAYS(trandate))) BETWEEN 90 AND 119
     AND transno =t.transno
     AND settled = 0) AS v91a120,

  (SELECT sum(ovamount+ovgst - alloc)
   FROM debtortrans
   WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (d.paymentterms + TO_DAYS(trandate))) >= 120
     AND transno =t.transno
     AND settled = 0) AS v91aMas

FROM debtorsmaster d,
     debtortrans t ,
     systypes st
WHERE d.debtorno = t.debtorno
  AND st.typeid = t.type
  and t.settled=0
  AND abs(t.ovamount+t.ovgst+t.ovfreight+t.ovdiscount-t.alloc) > 1
  AND d.debtorno = '{$row['debtorno']}'
  {$soloMora}
ORDER BY d.debtorno DESC";
//echo "<br>{$SQL2}<<<----2<br>";

	$rs2 = DB_query($SQL2,$db,'','',False,False);
	$plazos2 ="";
	while(($row2 = DB_fetch_assoc($rs2,$db))){
if($row2['paymentterms']==0 || $row2['paymentterms']=="CA")
	$plazos2 = "CONTADO";
else
	$plazos2 = $row2['paymentterms']." DIAS";

		$html .= "<tr style='font-size:10px;'><td style='padding:0 0 0 20px'>&nbsp;{$row2['des']}</td><td>&nbsp;<!--{$plazos2}//--></td><td align='right'>&nbsp;".nf($row2['saldo'])."</td><td align='right'>&nbsp;<!--".nf($row2['porVencer'])."//--></td><td align='right'>&nbsp;".nf($row2['v1a30'])."</td><td align='right'>&nbsp;".nf($row2['v31a60'])."</td><td align='right'>&nbsp;".nf($row2['v61a90'])."</td><td align='right'>&nbsp;".nf($row2['v91a120'])."</td><td align='right'>&nbsp;".nf($row2['v91aMas'])."</td></tr>";

}

}


 }
$html .= "</tbody><tr style='font-weight:bold'><td align='right'><b>Total Final</b></td><td></td><td align='right'>".nf($totals['saldo'])."</td><td align='right'><!--".nf($totals['porVencer'])."//--></td><td align='right'>".nf($totals['v1a30'])."</td><td align='right'>".nf($totals['v31a60'])."</td><td align='right'>".nf($totals['v61a90'])."</td><td align='right'>".nf($totals['v91a120'])."</td><td align='right'>".nf($totals['v91aMas'])."</td></tr>";
 $html .= "</table>";
 	//echo $html;
 	//$html = "<table><tr><td>RAZZEK</td></tr></table>";

  if(isset($_GET['PDF']))
 {
 	# Instanciamos un objeto de la clase DOMPDF.
 	$mipdf = new DOMPDF();

 	# Definimos el tamaño y orientación del papel que queremos.
 	# O por defecto cogerá el que está en el fichero de configuración.
 	$mipdf ->set_paper("A4", "portrait");

 	# Cargamos el contenido HTML.
 	$mipdf ->load_html($html);

 	# Renderizamos el documento PDF.
 	$mipdf ->render();

 	//pre_var_dump($mipdf);
 	//ob_clean();
 	# Enviamos el fichero PDF al navegador.
 	$mipdf ->stream('FicheroEjemplos.pdf');
 	exit();
 }

}
?>
