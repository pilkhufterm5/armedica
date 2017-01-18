<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-07-18 08:20:59 -0500 (vie, 18 jul 2008) $
 * $Rev: 334 $
 */
/* 
bowikaxu - realhost
april 2007
ver el costo de un articulo sin la opcion de modificar
*/

$PageSecurity = 2; /*viewing possible with inquiries but not mods */

$UpdateSecurity =10;

include('includes/session.inc');
$title = _('Costo del Articulo');


include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

// bowikaxu realhost - 25 june 2008 - cost update
if($_SESSION['rh_updatecost']!=1){
	prnMsg('Usted no tiene acceso','error','ERROR');
	include('includes/footer.inc');
	exit;
}

if (isset($_GET['StockID'])){
	$StockID = trim(strtoupper($_GET['StockID']));
} elseif (isset($_POST['StockID'])){
	$StockID =trim(strtoupper($_POST['StockID']));
}

echo "<a href='" . $rootpath . '/SelectProduct.php?' . SID . "'>" . _('Regresar a Articulos') . '</a><BR>';

if(!empty($StockID)){
	

// ----------------------------------------------------------------------------------

	// bowikaxu - abril 2007
	// modify the parent material cost
	//UpdateCost($db,$StockID);
	// FIN DE ACTUALIZAR COSTO
	$result = DB_query("SELECT description,
			units,
			lastcost,
			actualcost,
			materialcost,
			labourcost,
			overheadcost,
			mbflag,
			sum(quantity) as totalqoh
		FROM stockmaster INNER JOIN locstock
			ON stockmaster.stockid=locstock.stockid
		WHERE stockmaster.stockid='$StockID'
		GROUP BY description,
			units,
			lastcost,
			actualcost,
			materialcost,
			labourcost,
			overheadcost,
			mbflag",
		$db,$ErrMsg,$DbgMsg);


$myrow = DB_fetch_array($result);
echo "<BR><FONT COLOR=BLUE SIZE=3><B>" . $StockID . " - " . $myrow['description'] . '</B> - ' . _('Total Quantity On Hand') . ': ' . $myrow['totalqoh'] . " " . $myrow['units'] ."</FONT>";
	echo '<CENTER><TABLE CELLPADDING=2 BORDER=2>';
	echo '<TR><TD>'.('Costo').':</TD><TD ALIGN=RIGHT>' . number_format($myrow['materialcost']+$myrow['labourcost']+$myrow['overheadcost'],2) . '</TD></TR></TABLE>';
	
}else{
	
	prnMsg('Esta pagina debe ser llamada con un articulo','info','ERROR: ');
	echo "<center><a href='" . $rootpath . '/SelectProduct.php?' . SID . "'>" . _('Regresar a Articulos') . '</a></center><BR>';
}

include('includes/footer.inc');
?>
