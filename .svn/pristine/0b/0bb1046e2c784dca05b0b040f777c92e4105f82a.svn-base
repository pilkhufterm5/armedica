<?php
/* $Revision: 343 $ */
/* $Revision: 343 $ */
$PageSecurity = 2;
include('includes/session.inc');

If (isset($_POST['PrintPDF'])
	AND isset($_POST['FromCriteria'])
	AND strlen($_POST['FromCriteria'])>=1
	AND isset($_POST['ToCriteria'])
	AND strlen($_POST['ToCriteria'])>=1){


	include('includes/PDFStarter.php');

	$FontSize=9;
	$pdf->addinfo('Title',_('Inventory Valuation Report'));
	$pdf->addinfo('Subject',_('Inventory Valuation'));

	$PageNumber=1;
	$line_height=12;
	
	// bowikaxu realhost - 18 july 2008 - show obsoletes?
	if($_POST['obsolete']=='on'){
		$obsolete = "";
	}else {
		$obsolete = " AND stockmaster.discontinued = 0 ";
	}

	if($_POST['Marca']=='All'){
		$sqlMarca = "";
	}else {
	    $sqlMarca = " AND stockmaster.rh_marca = ".$_POST['Marca'];
	}

      /*Now figure out the inventory data to report for the category range under review */
if($_POST["Order"]=='0'){
	if ($_POST['Location']=='All'){
		$SQL = "SELECT stockmaster.categoryid,
				stockcategory.categorydescription,
				stockmaster.stockid,
                rh_marca.nombre,
				stockmaster.description,
				SUM(locstock.quantity) AS qtyonhand,
                stockmaster.rh_marca,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				SUM(locstock.quantity) *(stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost) AS itemtotal
			FROM stockmaster join rh_marca on rh_marca.id=stockmaster.rh_marca,
				stockcategory,
				locstock
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.categoryid=stockcategory.categoryid
			".$obsolete." ".$sqlMarca."
			GROUP BY stockmaster.categoryid,
				stockcategory.categorydescription,
				unitcost,
				stockmaster.stockid,
				stockmaster.description
			HAVING SUM(locstock.quantity)!=0
			AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
			AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
			ORDER BY stockmaster.categoryid,stockmaster.rh_marca,
				stockmaster.stockid";
	} else {
		$SQL = "SELECT stockmaster.categoryid,
				stockcategory.categorydescription,
				stockmaster.stockid,
                rh_marca.nombre,
				stockmaster.description,
				locstock.quantity AS qtyonhand,
                stockmaster.rh_marca,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				locstock.quantity *(stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost) AS itemtotal
			FROM stockmaster join rh_marca on rh_marca.id=stockmaster.rh_marca,
				stockcategory,
				locstock
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.categoryid=stockcategory.categoryid
			AND locstock.quantity!=0
			AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
			AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
			AND locstock.loccode = '" . $_POST['Location'] . "'
			".$obsolete." ".$sqlMarca."
			ORDER BY stockmaster.categoryid,stockmaster.rh_marca,
				stockmaster.stockid";
	}
	$InventoryResult = DB_query($SQL,$db,'','',false,true);

	if (DB_error_no($db) !=0) {
	  $title = _('Inventory Valuation') . ' - ' . _('Problem Report');
	  include('includes/header.inc');
	   prnMsg( _('The inventory valuation could not be retrieved by the SQL because') . ' '  . DB_error_msg($db),'error');
	   echo "<BR><A HREF='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</A>';
	   if ($debug==1){
	      echo "<BR>$SQL";
	   }
	   include('includes/footer.inc');
	   exit;
	}

	include ('includes/PDFInventoryValnPageHeader.inc');

        $Tot_Val=0;
	$Category = '';
	$CatTot_Val=0;
    $CatTot_Qty=0;
	$Marca = '';
	$MarTot_Val=0;
    $MarTot_Qty=0;
     $i=0;
	While ($InventoryValn = DB_fetch_array($InventoryResult,$db)){
        $i++;
		if ($Category!=$InventoryValn['categoryid']){
			$FontSize=10;
			if ($Category!=''){ /*Then it's NOT the first time round */

				/* need to print the total of previous category */
				if ($_POST['DetailedReport']=='Yes'){
					$YPos -= $line_height;
					if ($YPos < $Bottom_Margin + (3*$line_height)){
		 				  include('includes/PDFInventoryValnPageHeader.inc');
					}
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,280-$Left_Margin,$FontSize,_('Total por Marca') . ' ' . $Marca . ' - ' . $MarcaName);
    				$DisplayCatTotVal = number_format($MarTot_Val,2);
				    $DisplayCatTotQty = number_format($MarTot_Qty,0);
                                $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right');
	                        $LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayCatTotQty, 'right');
                                $YPos -=$line_height;

				    If ($_POST['DetailedReport']=='Yes'){
				        /*draw a line under the CATEGORY TOTAL*/
					    $pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
					    $YPos -=$line_height;
				    }
                    //$Marca='';
					$YPos -= (2*$line_height);
					if ($YPos < $Bottom_Margin + (3*$line_height)){
		 				  include('includes/PDFInventoryValnPageHeader.inc');
					}
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,_('Total for') . ' ' . $Category . ' - ' . $CategoryName);
				}

				$DisplayCatTotVal = number_format($CatTot_Val,2);
				$DisplayCatTotQty = number_format($CatTot_Qty,0);
                                $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right');
	                        $LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayCatTotQty, 'right');
                                $YPos -=$line_height;

				If ($_POST['DetailedReport']=='Yes'){
				/*draw a line under the CATEGORY TOTAL*/
					$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
					$YPos -=(2*$line_height);
				}
				$CatTot_Val=0;
                $CatTot_Qty=0;
				$MarTot_Val=0;
                $MarTot_Qty=0;
			}
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,$InventoryValn['categoryid'] . ' - ' . $InventoryValn['categorydescription']);
			$Category = $InventoryValn['categoryid'];
			$CategoryName = $InventoryValn['categorydescription'];
            $Marca='';
		}

	   if ($Marca!=$InventoryValn['rh_marca']){
			$FontSize=10;
			if ($Marca!=''){ /*Then it's NOT the first time round */
				/* need to print the total of previous category */
	   			if ($_POST['DetailedReport']=='Yes'){
					$YPos -= $line_height;
					if ($YPos < $Bottom_Margin + (3*$line_height)){
		 				  include('includes/PDFInventoryValnPageHeader.inc');
					}
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,_('Total por Marca') . ' ' . $Marca . ' - ' . $MarcaName);
                    //$Marca='';
				}

				$DisplayCatTotVal = number_format($MarTot_Val,2);
				$DisplayCatTotQty = number_format($MarTot_Qty,0);
                                $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right');
	                        $LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayCatTotQty, 'right');
                                $YPos -=$line_height;

				If ($_POST['DetailedReport']=='Yes'){
				/*draw a line under the CATEGORY TOTAL*/
					$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
					$YPos -=$line_height;
				}
				$MarTot_Val=0;
                $MarTot_Qty=0;
			}
            $YPos -=$line_height;
			$LeftOvers = $pdf->addTextWrap($Left_Margin+20,$YPos,260-$Left_Margin,$FontSize,$InventoryValn['rh_marca'] . ' - ' . $InventoryValn['nombre']);
			$Marca = $InventoryValn['rh_marca'];
			$MarcaName = $InventoryValn['nombre'];
		}

		if ($_POST['DetailedReport']=='Yes'){
			$YPos -=$line_height;
			$FontSize=8;

			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,$InventoryValn['stockid']);
            $LeftOvers = $pdf->addTextWrap(130,$YPos,260,$FontSize,$InventoryValn['description']);
			$DisplayUnitCost = number_format($InventoryValn['unitcost'],2);
			$DisplayQtyOnHand = number_format($InventoryValn['qtyonhand'],2);
			$DisplayItemTotal = number_format($InventoryValn['itemtotal'],2);

			$LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayQtyOnHand,'right');
			$LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize,$DisplayUnitCost, 'right');
			$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayItemTotal, 'right');

		}
		$Tot_Val += $InventoryValn['itemtotal'];
		$CatTot_Val += $InventoryValn['itemtotal'];
        $CatTot_Qty += $InventoryValn['qtyonhand'];
		$MarTot_Val += $InventoryValn['itemtotal'];
        $MarTot_Qty += $InventoryValn['qtyonhand'];

		if ($YPos < $Bottom_Margin + $line_height){
		   include('includes/PDFInventoryValnPageHeader.inc');
		}

	} /*end inventory valn while loop */

	$FontSize =10;
/*Print out the category totals */
				if ($_POST['DetailedReport']=='Yes'){
					$YPos -= $line_height;
					if ($YPos < $Bottom_Margin + (3*$line_height)){
		 				  include('includes/PDFInventoryValnPageHeader.inc');
					}
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,_('Total por Marca') . ' ' . $Marca . ' - ' . $MarcaName);
    				$DisplayCatTotVal = number_format($MarTot_Val,2);
				    $DisplayCatTotQty = number_format($MarTot_Qty,0);
                                $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right');
	                        $LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayCatTotQty, 'right');
                                $YPos -=$line_height;

				    If ($_POST['DetailedReport']=='Yes'){
				        /*draw a line under the CATEGORY TOTAL*/
					    $pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
					    $YPos -=$line_height;
				    }
                    }

	if ($_POST['DetailedReport']=='Yes'){
		$YPos -= (2*$line_height);
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize, _('Total for') . ' ' . $Category . ' - ' . $CategoryName, 'left');
	}
	$DisplayCatTotVal = number_format($CatTot_Val,2);
        $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right');
	$DisplayCatTotQty = number_format($CatTot_Qty,0);
        $LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayCatTotQty, 'right');

	If ($_POST['DetailedReport']=='Yes'){
		/*draw a line under the CATEGORY TOTAL*/
		$YPos -= ($line_height);
		$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
	}

	$YPos -= (2*$line_height);

	if ($YPos < $Bottom_Margin + $line_height){
		   include('includes/PDFInventoryValnPageHeader.inc');
	}
/*Print out the grand totals */
	$LeftOvers = $pdf->addTextWrap(80,$YPos,260-$Left_Margin,$FontSize,_('Grand Total Value'), 'right');
	$DisplayTotalVal = number_format($Tot_Val,2);
        $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayTotalVal, 'right');
 }else{
   //************************Reporte por marca**********************************
	if ($_POST['Location']=='All'){
		$SQL = "SELECT stockmaster.categoryid,
				stockcategory.categorydescription,
				stockmaster.stockid,
                rh_marca.nombre,
				stockmaster.description,
				SUM(locstock.quantity) AS qtyonhand,
                stockmaster.rh_marca,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				SUM(locstock.quantity) *(stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost) AS itemtotal
			FROM stockmaster join rh_marca on rh_marca.id=stockmaster.rh_marca,
				stockcategory,
				locstock
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.categoryid=stockcategory.categoryid
			".$obsolete." ".$sqlMarca."
			GROUP BY stockmaster.rh_marca,
                unitcost,
				stockmaster.stockid,
				stockmaster.description
			HAVING SUM(locstock.quantity)!=0
			AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
			AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
			ORDER BY stockmaster.rh_marca,stockmaster.categoryid,
				stockmaster.stockid";
	} else {
		$SQL = "SELECT stockmaster.categoryid,
				stockcategory.categorydescription,
				stockmaster.stockid,
                rh_marca.nombre,
				stockmaster.description,
				locstock.quantity AS qtyonhand,
                stockmaster.rh_marca,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				locstock.quantity *(stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost) AS itemtotal
			FROM stockmaster join rh_marca on rh_marca.id=stockmaster.rh_marca,
				stockcategory,
				locstock
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.categoryid=stockcategory.categoryid
			AND locstock.quantity!=0
			AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
			AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
			AND locstock.loccode = '" . $_POST['Location'] . "'
			".$obsolete." ".$sqlMarca."
			ORDER BY stockmaster.rh_marca,stockmaster.categoryid,
				stockmaster.stockid";
	}
	$InventoryResult = DB_query($SQL,$db,'','',false,true);

	if (DB_error_no($db) !=0) {
	  $title = _('Inventory Valuation') . ' - ' . _('Problem Report');
	  include('includes/header.inc');
	   prnMsg( _('The inventory valuation could not be retrieved by the SQL because') . ' '  . DB_error_msg($db),'error');
	   echo "<BR><A HREF='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</A>';
	   if ($debug==1){
	      echo "<BR>$SQL";
	   }
	   include('includes/footer.inc');
	   exit;
	}

	include ('includes/PDFInventoryValnPageHeader.inc');

    $Tot_Val=0;
	$Category = '';
	$CatTot_Val=0;
    $CatTot_Qty=0;
	$Marca = '';
	$MarTot_Val=0;
    $MarTot_Qty=0;
     $i=0;
	While ($InventoryValn = DB_fetch_array($InventoryResult,$db)){
        $i++;
		if ($Marca!=$InventoryValn['rh_marca']){
			$FontSize=10;
			if ($Marca!=''){ /*Then it's NOT the first time round */

				/* need to print the total of previous category */
				if ($_POST['DetailedReport']=='Yes'){
					$YPos -= $line_height;
					if ($YPos < $Bottom_Margin + (3*$line_height)){
		 				  include('includes/PDFInventoryValnPageHeader.inc');
					}
					//$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,280-$Left_Margin,$FontSize,_('Total por ') . ' ' . $Marca . ' - ' . $MarcaName);
                    $LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,_('Total por categoría') . ' ' . $Category . ' - ' . $CategoryName);
    				$DisplayCatTotVal = number_format($MarTot_Val,2);
				    $DisplayCatTotQty = number_format($MarTot_Qty,0);
                                $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right');
	                        $LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayCatTotQty, 'right');
                                $YPos -=$line_height;

				    If ($_POST['DetailedReport']=='Yes'){
				        /*draw a line under the CATEGORY TOTAL*/
					    $pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
					    $YPos -=$line_height;
				    }
                    //$Marca='';
					$YPos -= (2*$line_height);
					if ($YPos < $Bottom_Margin + (3*$line_height)){
		 				  include('includes/PDFInventoryValnPageHeader.inc');
					}
                    $LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,_('Total por ') . ' ' . $Marca . ' - ' . $MarcaName);
				}

				$DisplayCatTotVal = number_format($CatTot_Val,2);
				$DisplayCatTotQty = number_format($CatTot_Qty,0);
                                $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right');
	                        $LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayCatTotQty, 'right');
                                $YPos -=$line_height;

				If ($_POST['DetailedReport']=='Yes'){
				/*draw a line under the CATEGORY TOTAL*/
					$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
					$YPos -=(2*$line_height);
				}
				$CatTot_Val=0;
                $CatTot_Qty=0;
				$MarTot_Val=0;
                $MarTot_Qty=0;
			}
			$LeftOvers = $pdf->addTextWrap($Left_Margin+20,$YPos,260-$Left_Margin,$FontSize,$InventoryValn['rh_marca'] . ' - ' . $InventoryValn['nombre']);
			$Marca = $InventoryValn['rh_marca'];
			$MarcaName = $InventoryValn['nombre'];
            $Category='';
		}

	   if ($Category!=$InventoryValn['categoryid']){
			$FontSize=10;
			if ($Category!=''){ /*Then it's NOT the first time round */
				/* need to print the total of previous category */
	   			if ($_POST['DetailedReport']=='Yes'){
					$YPos -= $line_height;
					if ($YPos < $Bottom_Margin + (3*$line_height)){
		 				  include('includes/PDFInventoryValnPageHeader.inc');
					}
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,_('Total por categoría') . ' ' . $Category . ' - ' . $CategoryName);
                    //$Marca='';
				}

				$DisplayCatTotVal = number_format($MarTot_Val,2);
				$DisplayCatTotQty = number_format($MarTot_Qty,0);
                                $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right');
	                        $LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayCatTotQty, 'right');
                                $YPos -=$line_height;

				If ($_POST['DetailedReport']=='Yes'){
				/*draw a line under the CATEGORY TOTAL*/
					$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
					$YPos -=$line_height;
				}
				$MarTot_Val=0;
                $MarTot_Qty=0;
			}
            $YPos -=$line_height;
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,$InventoryValn['categoryid'] . ' - ' . $InventoryValn['categorydescription']);
			$Category = $InventoryValn['categoryid'];
			$CategoryName = $InventoryValn['categorydescription'];
		}

		if ($_POST['DetailedReport']=='Yes'){
			$YPos -=$line_height;
			$FontSize=8;

			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,$InventoryValn['stockid']);
            $LeftOvers = $pdf->addTextWrap(130,$YPos,260,$FontSize,$InventoryValn['description']);
			$DisplayUnitCost = number_format($InventoryValn['unitcost'],2);
			$DisplayQtyOnHand = number_format($InventoryValn['qtyonhand'],2);
			$DisplayItemTotal = number_format($InventoryValn['itemtotal'],2);

			$LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayQtyOnHand,'right');
			$LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize,$DisplayUnitCost, 'right');
			$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayItemTotal, 'right');

		}
		$Tot_Val += $InventoryValn['itemtotal'];
		$CatTot_Val += $InventoryValn['itemtotal'];
        $CatTot_Qty += $InventoryValn['qtyonhand'];
		$MarTot_Val += $InventoryValn['itemtotal'];
        $MarTot_Qty += $InventoryValn['qtyonhand'];

		if ($YPos < $Bottom_Margin + $line_height){
		   include('includes/PDFInventoryValnPageHeader.inc');
		}

	} /*end inventory valn while loop */

	$FontSize =10;
/*Print out the category totals */

	if ($_POST['DetailedReport']=='Yes'){
		$YPos -= (2*$line_height);
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize, _('Total for') . ' ' . $Category . ' - ' . $CategoryName, 'left');
	}
	$DisplayCatTotVal = number_format($CatTot_Val,2);
        $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right');
	$DisplayCatTotQty = number_format($CatTot_Qty,0);
        $LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayCatTotQty, 'right');

	If ($_POST['DetailedReport']=='Yes'){
		/*draw a line under the CATEGORY TOTAL*/
		$YPos -= ($line_height);
		$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
	}

				if ($_POST['DetailedReport']=='Yes'){
					$YPos -= $line_height;
					if ($YPos < $Bottom_Margin + (3*$line_height)){
		 				  include('includes/PDFInventoryValnPageHeader.inc');
					}
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,_('Total por Marca') . ' ' . $Marca . ' - ' . $MarcaName);
    				$DisplayCatTotVal = number_format($MarTot_Val,2);
				    $DisplayCatTotQty = number_format($MarTot_Qty,0);
                                $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right');
	                        $LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayCatTotQty, 'right');
                                $YPos -=$line_height;

				    If ($_POST['DetailedReport']=='Yes'){
				        /*draw a line under the CATEGORY TOTAL*/
					    $pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
					    $YPos -=$line_height;
				    }
                    }

	$YPos -= (2*$line_height);

	if ($YPos < $Bottom_Margin + $line_height){
		   include('includes/PDFInventoryValnPageHeader.inc');
	}
/*Print out the grand totals */
	$LeftOvers = $pdf->addTextWrap(80,$YPos,260-$Left_Margin,$FontSize,_('Grand Total Value'), 'right');
	$DisplayTotalVal = number_format($Tot_Val,2);
        $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayTotalVal, 'right');
   //***************************************************************************
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

	$title=_('Inventory Valuation Reporting');
	include('includes/header.inc');


	if (strlen($_POST['FromCriteria'])<1 || strlen($_POST['ToCriteria'])<1) {

	/*if $FromCriteria is not set then show a form to allow input	*/

		echo '<FORM ACTION=' . $_SERVER['PHP_SELF'] . " METHOD='POST'><CENTER><TABLE>";

        echo '<TR><TD>' . _('Ordenado principal') . ":</TD><TD><SELECT name='Order'>";
        echo "<OPTION Value='0'>" . _('Orden por Categoria').'</option>';
        echo "<OPTION Value='1' selected='selected' >" . _('Orden por Marca').'</option>';
        echo '</SELECT></TD></TR>';

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


		echo '<TR><TD>' . _('Por Marca') . ":</TD><TD><SELECT name='Marca'>";
		$sql = 'SELECT id,nombre FROM rh_marca';
		$LocnResult=DB_query($sql,$db);

		echo "<OPTION Value='All'>" . _('Todas la marcas').'</option>';

		while ($myrow=DB_fetch_array($LocnResult)){
		          echo "<OPTION Value='" . $myrow['id'] . "'>" . $myrow['nombre'].'</option>';
		      		}
		echo '</SELECT></TD></TR>';

		echo '<TR><TD>' . _('Summary or Detailed Report') . ":</TD><TD><SELECT name='DetailedReport'>";
		echo "<OPTION SELECTED Value='No'>" . _('Summary Report');
		echo "<OPTION Value='Yes'>" . _('Detailed Report');
		echo '</SELECT></TD></TR>';
		
		// bowikaxu realhost - 21 july 2008 - obsolete filter
		if($_POST['obsolete']=='on'){
			echo "<TR><TD colspan=2>
			<INPUT TYPE=checkbox NAME='obsolete' checked>"._('Show').' '._('Obsolete')."
			</TD></TR>";
		}else {
			echo "<TR><TD colspan=2>
			<INPUT TYPE=checkbox NAME='obsolete'>"._('Show').' '._('Obsolete')."
			</TD></TR>";
		}

		echo "</TABLE><INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') . "'></CENTER>";
	}
	include('includes/footer.inc');

} /*end of else not PrintPDF */

?>
