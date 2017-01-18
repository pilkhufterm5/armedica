<?php

/* $Revision: 1.10 $ */

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
			debtortrans.branchcode
			FROM stockmoves, debtortrans
			WHERE
			stockmoves.type=20000
			AND debtortrans.type = 20000
			AND stockmoves.transno = ".$rem['Shipment']."
			AND debtortrans.transno = ".$rem['Shipment']."";
	
	$res2 = DB_query($sql,$db);
	
	while ($ins = DB_fetch_array($res2)){
		
		$sql = "INSERT INTO rh_remdetails(stockid, qty, transno, debtorno, branchcode, price, standardcost)
				VALUES 
				( '".$ins['stockid']."', ".$ins['qty'].", ".$res['Shipment'].", '".$ins['debtorno']."', '".$ins['branchcode']."', ".$ins['price'].", ".$ins['tandardcost'].")";
		
		DB_query($sql, $db,'ERROR rem '.$rem['Shipment'],'',true);
	}
	
}
DB_query("COMMIT");
include('includes/footer.inc');

?>