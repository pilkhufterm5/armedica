<?php

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
/**
 * 
 * bowikaxu realhost
 * january 2008
 * poner el inventario a ceros de cierta sucursal (bodega)
 * 
 */

include('includes/DefineStockAdjustment.php');
include('includes/DefineSerialItems.php');

$PageSecurity = 11;
include('includes/session.inc');
$title = _('Stock Adjustments Compare');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

$sql = "SELECT locstock.*, SUM(stockmoves.qty) AS movesqty FROM locstock, stockmoves
		WHERE locstock.stockid = stockmoves.stockid
			AND stockmoves.prd <= 5
			AND locstock.loccode = stockmoves.loccode
			AND locstock.loccode = 'CAN'
			GROUP BY stockmoves.stockid
		HAVING movesqty != locstock.quantity";

$sql = "SELECT rh_locstocktmp.* FROM rh_locstocktmp WHERE quantity != 0";
$res = DB_query($sql,$db);

while($loc_item = DB_fetch_array($res)){
	//prd <= 5 AND 
	$sql = "SELECT SUM(qty) as movesqty, stockmoves.stockid FROM stockmoves WHERE stockid = '".$loc_item['stockid']."' AND loccode = '".$loc_item['loccode']."' group by stockid";
	$res2 = DB_query($sql,$db);
	$moves = DB_fetch_array($res2);
	if($loc_item['quantity']!=$moves['movesqty']){
		//echo "<STRONG>ITEM: ".$loc_item['stockid']."  LOC: ".$loc_item['loccode']."  LOC QTY: ".$loc_item['quantity']."  MOVES QTY: ".$moves['movesqty']."</STRONG><BR><BR>";	
		$AdjustmentNumber = GetNextTransNo(17,$db);
		$SQL = "INSERT INTO stockmoves (
				stockid,
				type,
				transno,
				loccode,
				trandate,
				prd,
				reference,
				qty,
				newqoh)
			VALUES (
				'" . $loc_item['stockid'] . "',
				17,
				" . $AdjustmentNumber . ",
				'" . $loc_item['loccode'] . "',
				'2008-01-03',
				6,
				'" . DB_escape_string("Ajuste de Inventario 2008") ."',
				" . (0-$moves['movesqty']) . ",
				" . (0) . "
			)";
		echo $SQL.";<BR><BR>";	
	
	}else {
		//echo "ITEM: ".$loc_item['stockid']."  LOC: ".$loc_item['loccode']."  LOC QTY: ".$loc_item['quantity']."  MOVES QTY: ".$moves['movesqty']."<BR><BR>";
		$AdjustmentNumber = GetNextTransNo(17,$db);
		$SQL = "INSERT INTO stockmoves (
				stockid,
				type,
				transno,
				loccode,
				trandate,
				prd,
				reference,
				qty,
				newqoh)
			VALUES (
				'" . $loc_item['stockid'] . "',
				17,
				" . $AdjustmentNumber . ",
				'" . $loc_item['loccode'] . "',
				'2008-01-03',
				6,
				'" . DB_escape_string("Ajuste de Inventario 2008") ."',
				" . (0-$moves['movesqty']) . ",
				" . (0) . "
			)";
		//echo $SQL.";<BR><BR>";
	}
	
	/*
	$AdjustmentNumber = GetNextTransNo(17,$db);
	
	$SQL = "INSERT INTO stockmoves (
				stockid,
				type,
				transno,
				loccode,
				trandate,
				prd,
				reference,
				qty,
				newqoh)
			VALUES (
				'" . $_SESSION['Adjustment']->StockID . "',
				17,
				" . $AdjustmentNumber . ",
				'" . $loc_item['loccode'] . "',
				'2007-12-31',
				5,
				'" . DB_escape_string("Ajuste de Inventario 2008") ."',
				" . (0-$loc_item['movesqty']) . ",
				" . ($QtyOnHandPrior + $_SESSION['Adjustment']->Quantity) . "
			)";


		$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock movement record cannot be inserted because');
		$DbgMsg =  _('The following SQL to insert the stock movement record was used');
		$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
	
	if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND $_SESSION['Adjustment']->StandardCost > 0){

			$StockGLCodes = GetStockGLCode($loc_item['stockid'],$db);

			$SQL = "INSERT INTO gltrans (type, 
							typeno, 
							trandate, 
							periodno, 
							account, 
							amount, 
							narrative) 
					VALUES (17,
						" .$AdjustmentNumber . ", 
						'2007-12-31', 
						5, 
						" .  $StockGLCodes['adjglact'] . ", 
						" . $_SESSION['Adjustment']->StandardCost * -($_SESSION['Adjustment']->Quantity) . ", 
						'" . $loc_item['stockid'] . " x " . (0-$loc_item['movesqty']) . " @ " . $_SESSION['Adjustment']->StandardCost . " " . DB_escape_string($_SESSION['Adjustment']->Narrative) . "')";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The general ledger transaction entries could not be added because');
			$DbgMsg = _('The following SQL to insert the GL entries was used');
			$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg, true);

			$SQL = "INSERT INTO gltrans (type, 
							typeno, 
							trandate, 
							periodno, 
							account, 
							amount, 
							narrative) 
					VALUES (17,
						" .$AdjustmentNumber . ", 
						'" . $SQLAdjustmentDate . "', 
						" . $PeriodNo . ", 
						" .  $StockGLCodes['stockact'] . ", 
						" . $_SESSION['Adjustment']->StandardCost * $_SESSION['Adjustment']->Quantity . ", 
						'" . $_SESSION['Adjustment']->StockID . " x " . $_SESSION['Adjustment']->Quantity . " @ " . $_SESSION['Adjustment']->StandardCost . " " . DB_escape_string($_SESSION['Adjustment']->Narrative) . "')";

			$Errmsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The general ledger transaction entries could not be added because');
			$DbgMsg = _('The following SQL to insert the GL entries was used');
			$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg,true);
	}
	
	$SQL = "UPDATE locstock SET quantity = quantity + " . $_SESSION['Adjustment']->Quantity . " 
				WHERE stockid='" . $_SESSION['Adjustment']->StockID . "' 
				AND loccode='" . $_SESSION['Adjustment']->StockLocation . "'";

		$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' ._('The location stock record could not be updated because');
		$DbgMsg = _('The following SQL to update the stock record was used');
		//$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
		*/
}

include ('includes/footer.inc');
?>