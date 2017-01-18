<?php

/* $Revision: 1.12 $ */

$PageSecurity = 15; /*viewing possible with inquiries but not mods */

include('includes/session.inc');
$title = _('Maintenance of Minium Price');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if (isset($_GET['StockID'])){
	$StockID = trim(strtoupper($_GET['StockID']));
} elseif (isset($_POST['StockID'])){
	$StockID =trim(strtoupper($_POST['StockID']));
}

echo "<a href='" . $rootpath . '/SelectProduct.php?' . SID . "'>" . _('Back to Items') . '</a><BR>';

if(isset($_POST['UpdateData'])){
	if($_POST['MiniumPrice'] != 0 OR !empty($_POST['MiniumPrice']) OR $_POST['MiniumPrice'] > 0){
		if($_POST['OldMiniumPrice'] != $_POST['MiniumPrice']){
			$_POST['LastMiniumPrice'] = $_POST['OldMiniumPrice'];
		}
		$SQL = "UPDATE stockmaster SET rh_miniumprice = '".$_POST['MiniumPrice']."', rh_lastminiumprice = '".$_POST['LastMiniumPrice']."' WHERE stockid = '".$StockID."'";
		$ErrMsg = _('The minium price for the stock item could not be updated because');
		$DbgMsg = _('The SQL that failed was');
		$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
	}
}

$ErrMsg = _('The cost details for the stock item could not be retrieved because');
$DbgMsg = _('The SQL that failed was');

$result = DB_query("SELECT description, units, lastcost, actualcost, materialcost, labourcost, overheadcost, mbflag, sum(quantity) as totalqoh, rh_miniumprice, rh_lastminiumprice FROM stockmaster INNER JOIN locstock ON stockmaster.stockid=locstock.stockid WHERE stockmaster.stockid = '".$StockID."' GROUP BY description, units, lastcost, actualcost, materialcost, labourcost, overheadcost, mbflag", $db,$ErrMsg,$DbgMsg);
$myrow = DB_fetch_array($result);

echo "<BR><FONT COLOR=BLUE SIZE=3><B>" . $StockID . " - " . $myrow['description'] . '</B> - ' . _('Total Quantity On Hand') . ': ' . $myrow['totalqoh'] . " " . $myrow['units'] ."</FONT>";

echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . "?". SID ."' METHOD=POST>";
echo _('Stock Code') . ":<input type=text name='StockID' value='".$StockID."' 1 maxlength=20>";

echo " <INPUT TYPE=SUBMIT NAME='Show' VALUE='" . _('Show') . "'><HR>";

echo "<INPUT TYPE=HIDDEN NAME=LastMiniumPrice VALUE='".$myrow['rh_lastminiumprice']."'>";
echo "<INPUT TYPE=HIDDEN NAME=OldMiniumPrice VALUE='".$myrow['rh_miniumprice']."'>";

echo "<CENTER><TABLE CELLPADDING=2 BORDER=2>";
echo "<TR>";
echo "<TD>"._('Cost').":</TD>";

/*
 * Juan Mtz 0.o
 * realhost
 * 26-Agosto-2009
 *
 * Query utilizado para verificar si el usuario tiene acceso a datos relacionados con costos
 */

//$sqlShowCost = "SELECT rh_show_cost FROM www_users WHERE userid = '".$_SESSION['UserID']."'";

//$fieldShowCost = DB_query($sqlShowCost,$db);

//$show = DB_fetch_array($fieldShowCost);
/*
if ($show['rh_show_cost'])
    echo "<TD ALIGN=RIGHT>".number_format($myrow['materialcost'] + $myrow['labourcost'] + $myrow['overheadcost'],2)."</TD>";
else
    echo "<TD ALIGN=RIGHT>***</TD>";
*/
echo "</TR>";
echo "<TR>";
echo "<TD>"._('Last Minium Price').":</TD>";
echo "<TD ALIGN=RIGHT>".number_format($myrow['rh_lastminiumprice'],2)."</TD>";
echo "</TR>";
echo "<TR>";
echo "<TD>"._('Minium Price').":</TD>";
echo "<TD><INPUT TYPE=TEXT NAME=MiniumPrice SIZE=8 MAXLENGTH=10 VALUE='".$myrow['rh_miniumprice']."'></TD>";
echo "</TR>";
echo "</TABLE>";
echo "<INPUT TYPE=SUBMIT NAME='UpdateData' VALUE='" . _('Update') . "'><HR>";

echo "<A HREF='$rootpath/StockStatus.php?" . SID . "&StockID=$StockID'>" . _('Show Stock Status') . '</A>';
echo "<BR><A HREF='$rootpath/StockMovements.php?" . SID . "&StockID=$StockID'>" . _('Show Stock Movements') . '</A>';
echo "<BR><A HREF='$rootpath/StockUsage.php?" . SID . "&StockID=$StockID'>" . _('Show Stock Usage')  .'</A>';
echo "<BR><A HREF='$rootpath/SelectSalesOrder.php?" . SID . "&SelectedStockItem=$StockID'>" . _('Search Outstanding Sales Orders') . '</A>';
echo "<BR><A HREF='$rootpath/SelectCompletedOrder.php?" . SID . "&SelectedStockItem=$StockID'>" . _('Search Completed Sales Orders') . '</A>';
echo "</CENTER>";

echo '</FORM>';
include('includes/footer.inc');
?>