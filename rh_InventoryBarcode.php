<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
$PageSecurity = 2;
include ('includes/session.inc');
//include('includes/ConnectDB.inc');
//include('includes/SQL_CommonFunctions.inc');

If (isset($_POST['PrintPDF'])
AND isset($_POST['FromCriteria'])
AND strlen($_POST['FromCriteria'])>=1
AND isset($_POST['ToCriteria'])
AND strlen($_POST['ToCriteria'])>=1){

	include('config.php');
	include('includes/PDFStarter.php');
	include('barcode2/barcode.php');

	$FontSize=10;
	$pdf->addinfo('Title',_('Codigos de Barras Inventario'));
	$pdf->addinfo('Subject',_('Inventory Barcode'));

	$PageNumber=1;
	$line_height=12;

	/*Now figure out the inventory data to report for the category range under review */
	$SQL = "SELECT stockmaster.categoryid,
				stockcategory.categorydescription,
				stockmaster.stockid,
				stockmaster.barcode,
				stockmaster.description,
				SUM(locstock.quantity) AS qtyonhand,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				SUM(locstock.quantity) *(stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost) AS itemtotal
			FROM stockmaster,
				stockcategory,
				locstock
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.categoryid=stockcategory.categoryid
			GROUP BY stockmaster.categoryid,
				stockcategory.categorydescription,
				unitcost,
				stockmaster.stockid,
				stockmaster.description
			HAVING /*SUM(locstock.quantity)!=0
			AND*/ stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
			AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
			ORDER BY stockmaster.categoryid,
				stockmaster.stockid";

	$InventoryResult = DB_query($SQL,$db,'','',false,true);

	if (DB_error_no($db) !=0) {
		$title = _('C&oacute;digos de Barras') . ' - ' . _('Problem Report');
		include('includes/header.inc');
		prnMsg( _('The bar code could not be retrieved by the SQL because') . ' '  . DB_error_msg($db),'error');
		echo "<BR><A HREF='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</A>';
		if ($debug==1){
			echo "<BR>$SQL";
		}
		include('includes/footer.inc');
		exit;
	}

	include ('includes/rh_PDFInventoryCodBarHeader.inc');
	$Tot_Val=0;
	$Category = '';
	$CatTot_Val=0;
	While ($InventoryValn = DB_fetch_array($InventoryResult,$db)){

		if ($Category!=$InventoryValn['categoryid']){
			$FontSize=10;
			if ($Category!=''){ /*Then it's NOT the first time round */

				/* need to print the total of previous category */
				if ($_POST['DetailedReport']=='Yes'){
					$YPos -= (2*$line_height);
					if ($YPos < $Bottom_Margin + (3*$line_height)){
						include('includes/rh_PDFInventoryCodBarHeader.inc');
					}
				}

				if ($_POST['DetailedReport']=='Yes'){
					/*draw a line under the CATEGORY TOTAL*/
					$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
					$YPos -=(2*$line_height);
				}
				$CatTot_Val=0;
			}
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,$InventoryValn['categoryid'] . ' - ' . $InventoryValn['categorydescription']);
			$Category = $InventoryValn['categoryid'];
			$CategoryName = $InventoryValn['categorydescription'];
		}

		if ($_POST['DetailedReport']=='Yes'){
			// bowikaxu - minus 20
			$YPos -=($line_height+45);
			$FontSize=8;

			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,$InventoryValn['stockid']);
			$LeftOvers = $pdf->addTextWrap(100,$YPos,280,$FontSize,substr($InventoryValn['description'],0,40).' ['.$InventoryValn['barcode'].']');

			$DisplayUnitCost = number_format($InventoryValn['unitcost'],2);
			$DisplayQtyOnHand = number_format($InventoryValn['qtyonhand'],0);
			$DisplayItemTotal = number_format($InventoryValn['itemtotal'],2);

			// bowikaxu - print the barcode
			$bar= new BARCODE();

			//$barnumber=$FromTransNo;
			//$barnumber="200780";

			$bar->setSymblogy("CODE39");
			$bar->setHeight(30);
			//$bar->setSymblogy("EAN-8");
			if($InventoryValn['barcode']!=''){
				
				//	$bar->setFont("arial");
				$bar->setScale(1);
				$bar->setHexColor("#000000","#FFFFFF");
				if(!file_exists("barcode2/".$InventoryValn['barcode'].".jpg")){
					$bar->genBarCode($InventoryValn['barcode'],"jpg","barcode2/".$InventoryValn['barcode']);
				}

				$pdf->addJpegFromFile("barcode2/".$InventoryValn['barcode'].".jpg",390,$YPos-30,160,65);
				//$pdf->addText(440,$YPos-5, $FontSize-1, $InventoryValn['barcode']);

			}else {
				$pdf->addTextWrap(430,$YPos,150,$FontSize,_('Sin C&oacute;digo de Barras'), 'centre');
			}
			$Tot_Val += $InventoryValn['itemtotal'];
			$CatTot_Val += $InventoryValn['itemtotal'];

			if ($YPos < $Bottom_Margin + $line_height){
				include('includes/rh_PDFInventoryCodBarHeader.inc');
			}
		}

	} /*end inventory valn while loop */

	$FontSize =10;
	/*Print out the category totals */
	$YPos -= (2*$line_height);
	//$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize, _('') . ' ' . $Category . ' - ' . $CategoryName, 'left');
	//$DisplayCatTotVal = number_format($CatTot_Val,2);
	/*draw a line under the CATEGORY TOTAL*/
	$YPos -= ($line_height);
	$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);

	$YPos -= (2*$line_height);

	if ($YPos < $Bottom_Margin + $line_height){
		include('includes/rh_PDFInventoryCodBarHeader.inc');
	}

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

	//include('includes/session.inc');
	$title=_('Inventory Barcode');
	include('includes/header.inc');


	if (strlen($_POST['FromCriteria'])<1 || strlen($_POST['ToCriteria'])<1) {

		/*if $FromCriteria is not set then show a form to allow input	*/

		echo '<FORM ACTION=' . $_SERVER['PHP_SELF'] . " METHOD='POST'><CENTER><TABLE>";

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
		/*
		echo '<TR><TD>' . _('For Inventory in Location') . ":</TD><TD><SELECT name='Location'>";
		$sql = 'SELECT loccode, locationname FROM locations';
		$LocnResult=DB_query($sql,$db);

		echo "<OPTION Value='All'>" . _('All Locations');

		while ($myrow=DB_fetch_array($LocnResult)){
		echo "<OPTION Value='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
		}
		echo '</SELECT></TD></TR>';
		*/
		echo "<INPUT TYPE=HIDDEN NAME='Location' VALUE='All'>";
		echo "<INPUT TYPE=HIDDEN NAME='DetailedReport' VALUE='Yes'>";
		/*
		echo '<TR><TD>' . _('Summary or Detailed Report') . ":</TD><TD><SELECT name='DetailedReport'>";
		echo "<OPTION SELECTED Value='No'>" . _('Summary Report');
		echo "<OPTION Value='Yes'>" . _('Detailed Report');
		echo '</SELECT></TD></TR>';
		*/
		echo "</TABLE><INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') . "'></CENTER>";
	}
	include('includes/footer.inc');

} /*end of else not PrintPDF */

?>