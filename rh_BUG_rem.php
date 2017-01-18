<?php

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */

$PageSecurity = 2;

include('includes/DefineCartClass2.php');
include('includes/DefineSerialItems.php');
include('includes/session.inc');

$title = _('Ver Remisiones');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
include('includes/FreightCalculation.inc');
include('includes/GetSalesTransGLCodes.inc');


DB_query("BEGIN",$db);

DB_query("DELETE FROM rh_remdetails WHERE transno IN (SELECT Shipment FROM rh_InvoiceShipment WHERE Facturado =0)",$db);

$sql = "SELECT Shipment
		FROM rh_InvoiceShipment
		WHERE Facturado = 0";

$res = DB_query($sql,$db);

while($rem = DB_fetch_array($res)){
	
	
	$sql = "SELECT stockmoves.stockid,
			-stockmoves.qty AS qty,
			stockmoves.price,
			stockmoves.loccode,
			debtortrans.trandate,
			stockmoves.standardcost,
			debtortrans.debtorno,
			debtortrans.order_,
			salesorderdetails.orderlineno,
			debtortrans.branchcode
			FROM stockmoves, debtortrans, salesorderdetails
			WHERE
			stockmoves.type=20000
			AND debtortrans.type = 20000
			AND salesorderdetails.stkcode = stockmoves.stockid
			AND salesorderdetails.orderno = debtortrans.order_
			AND stockmoves.transno = ".$rem['Shipment']."
			AND stockmoves.reference = debtortrans.order_
			AND debtortrans.transno = ".$rem['Shipment']."
			GROUP BY stockmoves.stockid";
	
	$res2 = DB_query($sql,$db);
	
	while ($ins = DB_fetch_array($res2)){
		
		$sql = "INSERT INTO rh_remdetails(stockid, qty, transno, debtorno, branchcode, price, standardcost, loccode, line)
				VALUES 
				( '".$ins['stockid']."', ".$ins['qty'].", ".$rem['Shipment'].", '".$ins['debtorno']."', '".$ins['branchcode']."', ".$ins['price'].", ".$ins['standardcost'].",'".$ins['loccode']."', ".$ins['orderlineno'].")";
		echo $sql;		
		DB_query($sql, $db,'ERROR rem '.$rem['Shipment'],'',true);
	}
	
}
DB_query("COMMIT",$db);
include('includes/footer.inc');

?>