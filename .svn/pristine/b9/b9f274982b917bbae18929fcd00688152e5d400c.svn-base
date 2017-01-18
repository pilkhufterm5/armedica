<?php
/* $Revision: 14 $ */
/* $Revision: 14 $ */
$PageSecurity = 2;
include('includes/session.inc');

If (isset($_POST['PrintPDF'])
AND isset($_POST['FromCriteria'])
AND strlen($_POST['FromCriteria'])>=1
AND isset($_POST['ToCriteria'])
AND strlen($_POST['ToCriteria'])>=1){

include('includes/PDFStarter.php');

$FontSize=9;
$pdf->addinfo('Title',_('Purchase Order').' '._('Inquiry'));
$pdf->addinfo('Subject',_('Purchase Order').' '._('Inquiry'));

$PageNumber=1;
$line_height=12;

if($_POST['Location']=='All'){

	$Location='';

}else {
	$Location=" AND purchorders.intostocklocation = '".$_POST['Location']."' ";
}

$TotCatqtyord = 0;
$TotCatamtord = 0;
$TotCatqtyrec = 0;
$TotCatamtrec = 0;
$TotCatqtyinv = 0;
$TotCatamtinv = 0;
/*Now figure out the inventory data to report for the category range under review */
if ($_POST['DetailedReport']=='Yes'){
$SQL = "SELECT stockcategory.categoryid,
				stockcategory.categorydescription,
				stockmaster.stockid,
				stockmaster.description,
				SUM(purchorderdetails.qtyinvoiced) AS qtyinvoiced,
				SUM(purchorderdetails.qtyinvoiced*purchorderdetails.unitprice) AS amtinvoiced,
				
				SUM(purchorderdetails.quantityord) AS quantityord,
				SUM(purchorderdetails.quantityord*purchorderdetails.unitprice) AS amtord,
				
				SUM(purchorderdetails.quantityrecd) AS quantityrecd,
				SUM(purchorderdetails.quantityrecd*purchorderdetails.unitprice) AS amtrecd
				FROM
					stockcategory,
					stockmaster,
					purchorders,
					purchorderdetails
				WHERE stockmaster.stockid = purchorderdetails.itemcode
					AND stockmaster.categoryid = stockcategory.categoryid
					AND purchorderdetails.orderno = purchorders.orderno
					AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
					AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
					AND purchorders.orddate >= '".ConvertSQLDate($_POST['FromDate'])."'
					AND purchorders.orddate <= '".ConvertSQLDate($_POST['ToDate'])."'
					".$Location."
					GROUP BY stockmaster.stockid,
						stockmaster.categoryid
					ORDER BY stockmaster.categoryid, stockmaster.stockid";

//echo $SQL."<br>";
$InventoryResult = DB_query($SQL,$db,'','',false,true);

if (DB_error_no($db) !=0) {
$title = _('Purchase Order').' '._('Inquiry') . ' - ' . _('Problem Report');
include('includes/header.inc');
prnMsg( _('The inquiry could not be retrieved by the SQL because') . ' '  . DB_error_msg($db),'error');
echo "<BR><A HREF='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</A>';
if ($debug==1){
echo "<BR>$SQL";
}
include('includes/footer.inc');
exit;
}

include ('includes/rh_PurchOrderInHeader.php');

$Tot_Val=0;
$Category = '';
$CatTot_Val=0;
$CatTot_Qty=0;

// totales por categoria (se usan en caso de ser detalle)
$Catqtyord = 0;
$Catamtord = 0;
$Catqtyrec = 0;
$Catamtrec = 0;
$Catqtyinv = 0;
$Catamtinv = 0;

While ($InventoryValn = DB_fetch_array($InventoryResult,$db)){

if ($Category!=$InventoryValn['categoryid']){
$FontSize=10;
if ($Category!=''){ /*Then it's NOT the first time round */

/* need to print the total of previous category */
$YPos -= (2*$line_height);
if ($YPos < $Bottom_Margin + (3*$line_height)){
	include('includes/rh_PurchOrderInHeader.php');
}
If ($_POST['DetailedReport']=='Yes'){
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,_('Total for').': '.$Category);
$LeftOvers = $pdf->addTextWrap(200,$YPos,60,$FontSize,number_format($Catqtyord,2), 'right');
$LeftOvers = $pdf->addTextWrap(260,$YPos,60,$FontSize,number_format($Catamtord,2), 'right');
$LeftOvers = $pdf->addTextWrap(320,$YPos,60,$FontSize,number_format($Catqtyrec,2), 'right');
$LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,number_format($Catamtrec,2), 'right');
$LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize,number_format($Catqtyinv,2), 'right');
$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,number_format($Catamtinv,2), 'right');

$TotCatqtyord += $Catqtyord;
$TotCatamtord += $Catamtord;
$TotCatqtyrec += $Catqtyrec;
$TotCatamtrec += $Catamtrec;
$TotCatqtyinv += $Catqtyinv;
$TotCatamtinv += $Catamtinv;

$YPos -=$line_height;
}else {
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,$InventoryValn['categoryid'] . ' - ' . $InventoryValn['categorydescription']);
$LeftOvers = $pdf->addTextWrap(200,$YPos,60,$FontSize,number_format($Catqtyord,2), 'right');
$LeftOvers = $pdf->addTextWrap(260,$YPos,60,$FontSize,number_format($Catamtord,2), 'right');
$LeftOvers = $pdf->addTextWrap(320,$YPos,60,$FontSize,number_format($Catqtyrec,2), 'right');
$LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,number_format($Catamtrec,2), 'right');
$LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize,number_format($Catqtyinv,2), 'right');
$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,number_format($Catamtinv,2), 'right');
$YPos -=$line_height;
}

If ($_POST['DetailedReport']=='Yes'){
/*draw a line under the CATEGORY TOTAL*/
$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
$YPos -=(2*$line_height);
}
$CatTot_Val=0;
$CatTot_Qty=0;
$Catqtyord = 0;
$Catamtord = 0;
$Catqtyrec = 0;
$Catamtrec = 0;
$Catqtyinv = 0;
$Catamtinv = 0;

}
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,$InventoryValn['categoryid'] . ' - ' . $InventoryValn['categorydescription']);
$Category = $InventoryValn['categoryid'];
$CategoryName = $InventoryValn['categorydescription'];
}

if ($_POST['DetailedReport']=='Yes'){
$YPos -=$line_height;
$FontSize=8;
}
$Tot_Val += $InventoryValn['itemtotal'];
$CatTot_Val += $InventoryValn['itemtotal'];
$CatTot_Qty += $InventoryValn['qtyonhand'];

$Catqtyord += $InventoryValn['quantityord'] ;
$Catamtord += $InventoryValn['amtord'] ;
$Catqtyrec += $InventoryValn['quantityrecd'] ;
$Catamtrec += $InventoryValn['amtrecd'] ;
$Catqtyinv += $InventoryValn['qtyinvoiced'] ;
$Catamtinv += $InventoryValn['amtinvoiced'] ;
if ($_POST['DetailedReport']=='Yes'){
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,195-$Left_Margin,$FontSize-1,$InventoryValn['stockid'].' '.$InventoryValn['description']);
$LeftOvers = $pdf->addTextWrap(200,$YPos,60,$FontSize-1,number_format($InventoryValn['quantityord'],2), 'right');
$LeftOvers = $pdf->addTextWrap(260,$YPos,60,$FontSize-1,number_format($InventoryValn['amtord'],2), 'right');

$LeftOvers = $pdf->addTextWrap(320,$YPos,60,$FontSize-1,number_format($InventoryValn['quantityrecd'],2), 'right');
$LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize-1,number_format($InventoryValn['amtrecd'],2), 'right');

$LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize-1,number_format($InventoryValn['qtyinvoiced'],2), 'right');
$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize-1,number_format($InventoryValn['amtinvoiced'],2), 'right');
//$YPos -= $line_height;
}
if ($YPos < $Bottom_Margin + $line_height){
	include('includes/rh_PurchOrderInHeader.php');
}

} /*end inventory valn while loop */
$FontSize =10;
If ($_POST['DetailedReport']=='Yes'){
$YPos -=$line_height;
$YPos -=$line_height;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,_('Total for').': '.$Category);
$LeftOvers = $pdf->addTextWrap(200,$YPos,60,$FontSize,number_format($Catqtyord,2), 'right');
$LeftOvers = $pdf->addTextWrap(260,$YPos,60,$FontSize,number_format($Catamtord,2), 'right');
$LeftOvers = $pdf->addTextWrap(320,$YPos,60,$FontSize,number_format($Catqtyrec,2), 'right');
$LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,number_format($Catamtrec,2), 'right');
$LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize,number_format($Catqtyinv,2), 'right');
$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,number_format($Catamtinv,2), 'right');
$YPos -=$line_height;
$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
$TotCatqtyord += $Catqtyord;
$TotCatamtord += $Catamtord;
$TotCatqtyrec += $Catqtyrec;
$TotCatamtrec += $Catamtrec;
$TotCatqtyinv += $Catqtyinv;
$TotCatamtinv += $Catamtinv;
}
$YPos -=$line_height;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,_('Gran Total').': ');
$LeftOvers = $pdf->addTextWrap(200,$YPos,60,$FontSize,number_format($TotCatqtyord,2), 'right');
$LeftOvers = $pdf->addTextWrap(260,$YPos,60,$FontSize,number_format($TotCatamtord,2), 'right');
$LeftOvers = $pdf->addTextWrap(320,$YPos,60,$FontSize,number_format($TotCatqtyrec,2), 'right');
$LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,number_format($TotCatamtrec,2), 'right');
$LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize,number_format($TotCatqtyinv,2), 'right');
$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,number_format($TotCatamtinv,2), 'right');
} else { // RESUMEN
$SQL = "SELECT stockcategory.categoryid,
				stockcategory.categorydescription,
				SUM(purchorderdetails.qtyinvoiced) AS qtyinvoiced,
				SUM(purchorderdetails.qtyinvoiced*purchorderdetails.unitprice) AS amtinvoiced,
				
				SUM(purchorderdetails.quantityord) AS quantityord,
				SUM(purchorderdetails.quantityord*purchorderdetails.unitprice) AS amtord,
				
				SUM(purchorderdetails.quantityrecd) AS quantityrecd,
				SUM(purchorderdetails.quantityrecd*purchorderdetails.unitprice) AS amtrecd
				FROM
					stockcategory,
					stockmaster,
					purchorders,
					purchorderdetails
				WHERE stockmaster.stockid = purchorderdetails.itemcode
					AND stockmaster.categoryid = stockcategory.categoryid
					AND purchorderdetails.orderno = purchorders.orderno
					AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
					AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
					AND purchorders.orddate >= '".ConvertSQLDate($_POST['FromDate'])."'
					AND purchorders.orddate <= '".ConvertSQLDate($_POST['ToDate'])."'
					".$Location."
					GROUP BY stockmaster.categoryid
					ORDER BY stockmaster.categoryid";

//echo $SQL."<br>";
$InventoryResult = DB_query($SQL,$db,'','',false,true);

if (DB_error_no($db) !=0) {
$title = _('Purchase Order').' '._('Inquiry') . ' - ' . _('Problem Report');
include('includes/header.inc');
prnMsg( _('The inquiry could not be retrieved by the SQL because') . ' '  . DB_error_msg($db),'error');
echo "<BR><A HREF='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</A>';
if ($debug==1){
echo "<BR>$SQL";
}
include('includes/footer.inc');
exit;
}

include ('includes/rh_PurchOrderInHeader.php');

$Tot_Val=0;
$Category = '';
$CatTot_Val=0;
$CatTot_Qty=0;

// totales por categoria (se usan en caso de ser detalle)
$Catqtyord = 0;
$Catamtord = 0;
$Catqtyrec = 0;
$Catamtrec = 0;
$Catqtyinv = 0;
$Catamtinv = 0;

While ($InventoryValn = DB_fetch_array($InventoryResult,$db)){

$Catqtyord += $InventoryValn['quantityord'] ;
$Catamtord += $InventoryValn['amtord'] ;
$Catqtyrec += $InventoryValn['quantityrecd'] ;
$Catamtrec += $InventoryValn['amtrecd'] ;
$Catqtyinv += $InventoryValn['qtyinvoiced'] ;
$Catamtinv += $InventoryValn['amtinvoiced'] ;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,$InventoryValn['categoryid'] . ' - ' . $InventoryValn['categorydescription']);
$LeftOvers = $pdf->addTextWrap(200,$YPos,60,$FontSize,number_format($InventoryValn['quantityord'],2), 'right');
$LeftOvers = $pdf->addTextWrap(260,$YPos,60,$FontSize,number_format($InventoryValn['amtord'],2), 'right');

$LeftOvers = $pdf->addTextWrap(320,$YPos,60,$FontSize,number_format($InventoryValn['quantityrecd'],2), 'right');
$LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,number_format($InventoryValn['amtrecd'],2), 'right');

$LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize,number_format($InventoryValn['qtyinvoiced'],2), 'right');
$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,number_format($InventoryValn['amtinvoiced'],2), 'right');
$YPos -=$line_height;

if ($YPos < $Bottom_Margin + (3*$line_height)){
include('includes/rh_PurchOrderInHeader.php');
}

} /*end inventory valn while loop */
$FontSize =10;
$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
$YPos -=(2*$line_height);
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,_('Gran Total').': ');
$LeftOvers = $pdf->addTextWrap(200,$YPos,60,$FontSize,number_format($Catqtyord,2), 'right');
$LeftOvers = $pdf->addTextWrap(260,$YPos,60,$FontSize,number_format($Catamtord,2), 'right');
$LeftOvers = $pdf->addTextWrap(320,$YPos,60,$FontSize,number_format($Catqtyrec,2), 'right');
$LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,number_format($Catamtrec,2), 'right');
$LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize,number_format($Catqtyinv,2), 'right');
$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,number_format($Catamtinv,2), 'right');

}
// -------------- FIN RESUME REPORT ----------


$pdfcode = $pdf->output();
$len = strlen($pdfcode);

if ($len<=20){
$title = _('Print Inventory Valuation Error');
include('includes/header.inc');
prnMsg(_('There were no items with any value to print out for the location specified'),'error');
echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
include('includes/footer.inc');
exit;
} else {
header('Content-type: application/pdf');
header("Content-Length: " . $len);
header('Content-Disposition: inline; filename=Customer_trans.pdf');
header('Expires: 0');
header('Cache-Control: private, post-check=0, pre-check=0');
header('Pragma: public');

$pdf->Stream();

}
} else { /*The option to print PDF was not hit */

$title=_('Purchase Order').' '._('Inquiry');

echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy-M-d";

include('includes/header.inc');

if (strlen($_POST['FromCriteria'])<1 || strlen($_POST['ToCriteria'])<1) {

/*if $FromCriteria is not set then show a form to allow input	*/

echo '<FORM NAME="menu" ACTION=' . $_SERVER['PHP_SELF'] . " METHOD='POST'><CENTER><TABLE>";

echo '<TR><TD>' . _('From Inventory Category Code') . ':</FONT></TD><TD><SELECT name=FromCriteria>';

$sql='SELECT categoryid, categorydescription FROM stockcategory ORDER BY categoryid';
$CatResult= DB_query($sql,$db);
While ($myrow = DB_fetch_array($CatResult)){
echo "<OPTION VALUE='" . $myrow['categoryid'] . "'>" . $myrow['categoryid'] . ' - ' . $myrow['categorydescription'];
}
echo '</SELECT></TD></TR>';

echo '<TR><TD>' . _('To Inventory Category Code') . ':</TD><TD><SELECT name=ToCriteria>';

/*Set the index for the categories result set back to 0 */
DB_data_seek($CatResult,0);

While ($myrow = DB_fetch_array($CatResult)){
echo "<OPTION VALUE='" . $myrow['categoryid'] . "'>" . $myrow['categoryid'] . ' - ' . $myrow['categorydescription'];
}
echo '</SELECT></TD></TR>';

echo '<TR><TD>' . _('For Inventory in Location') . ":</TD><TD><SELECT name='Location'>";
$sql = 'SELECT loccode, locationname FROM locations';
$LocnResult=DB_query($sql,$db);

echo "<OPTION Value='All'>" . _('All Locations');

while ($myrow=DB_fetch_array($LocnResult)){
echo "<OPTION Value='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
}
echo '</SELECT></TD></TR>';

echo "<TR><TD>"._('Fecha').' '._('desde').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromDate' VALUE=''>
 			<a href=\"#\" onclick=\"menu.FromDate.value='';cal.select(document.forms['menu'].FromDate,'from_date_anchor','yyyy-M-d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>";
echo "</TD></TR>";
echo "<TR><TD>"._('Fecha').' '._('hasta').': '."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='ToDate' VALUE=''>
		<a href=\"#\" onclick=\"menu.ToDate.value='';cal.select(document.forms['menu'].ToDate,'from_date_anchor','yyyy-M-d');
		                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
		                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>
		</TD></TR>";

echo '<TR><TD>' . _('Summary or Detailed Report') . ":</TD><TD><SELECT name='DetailedReport'>";
echo "<OPTION SELECTED Value='No'>" . _('Summary Report');
echo "<OPTION Value='Yes'>" . _('Detailed Report');
echo '</SELECT></TD></TR>';

echo "</TABLE><INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') . "'></CENTER>";
}

?>

<script language="JavaScript">
	<!-- // create calendar object(s) just after form tag closed
					 // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
					 // note: you can have as many calendar objects as you need for your application
	var cal = new CalendarPopup();
					//-->
	</script>

<?php

include('includes/footer.inc');

} /*end of else not PrintPDF */

?>
