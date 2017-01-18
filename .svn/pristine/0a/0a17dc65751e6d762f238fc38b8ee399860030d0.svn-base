<?php
/* $Revision: 343 $ */
/* $Revision: 343 $ */
$PageSecurity = 2;
include('includes/session.inc');

If ((isset($_POST['PrintPDF'])||isset($_REQUEST['Excel']))
	AND isset($_POST['FromCriteria'])
	AND strlen($_POST['FromCriteria'])>=1
	AND isset($_POST['ToCriteria'])
	AND strlen($_POST['ToCriteria'])>=1){


	include('includes/PDFStarter.php');
	if(isset($_REQUEST['Excel']))
	{
		class pdfHTML extends Cpdf
		{
			private $Filas;
			private $maxRow;
			private $Pagina=0;
			private $maxCol;

			public function addTextWrap($XPos, $YPos, $Width, $Height, $Text, $Align='J', $border=0, $fill=0){
				if (!is_array($this->Filas)){
					$this->Filas=array();
					$this->maxRow=0;
					$this->maxCol=0;
				}
				{
					foreach($this->Filas as $colum=>$Col) if(!isset($Col[(int)($XPos)])) $this->Filas[$colum][(int)($XPos)]=array(false);
					$this->Filas[$YPos+$this->Pagina*$this->Page_Height]
					[(int)($XPos)]=html_entity_decode('"'.str_replace(',',',',(trim($Text))).'"',ENT_COMPAT | ENT_HTML401,'UTF-8');

					$this->maxCol=max($this->maxCol,count($this->Filas[$YPos+$this->Pagina*$this->Page_Height]));
					$this->maxRow=max($this->maxRow,count($this->Filas));
					return '';
				}
			}
			public function newPage(){
				$this->Pagina++;
			}
			public function output($d='S'){
				return $this->OutputD($d);
			}
			public function OutputD($d='S'){
				$Buffer='';
				switch ($d){
					case 'S':
					$Buffer.="<table>\n";
					$jj=0;
					foreach($this->Filas as $YPos=>$fila){
						ksort($fila);
						foreach($fila as $XPos=>$columna) if(is_array($columna)&&$columna[0]===false) unset($fila[$XPos]);
						if(count($fila)==1)
						{
							$columna=reset($fila);
							if($_SESSION['CompanyRecord']['coyname']==$columna)
								$jj=0;
						}
						$jj++;
						if($jj<2)
							$Buffer.= '<tr><td colspan="'.$this->maxCol.'" >&nbsp;</td></tr>';
						if($jj<3) continue;

						$Buffer.= "<tr";
						if($jj%2)
							$Buffer.= ' class="OddTableRows" ';
						else
							$Buffer.= ' class="EvenTableRows" '/**/;
						$Buffer.= ">\n";
						$ii=0;

						foreach($fila as $XPos=>$columna){
							if(_('Grand Total Value')==$columna){
								$Buffer.= "<td colspan=".$this->maxCol.">&nbsp;</td>";
								$Buffer.= "</tr>";
								$Buffer.= '<tr';
								if($jj%2)
									$Buffer.= ' class="OddTableRows" ';
								else
									$Buffer.= ' class="EvenTableRows" '/**/;
								$Buffer.= ">\n";
								$jj=3;
							}
							if($jj==3||count($fila)==1)
								$Buffer.= "<th";
							else
								$Buffer.= "<td";
							if($ii==0){
								if(count($fila)==1)
									$Buffer.= " colspan=".$this->maxCol." ";
								else
								if(count($fila)<$this->maxCol)
									$Buffer.= " colspan=".($this->maxCol-count($fila)+1)." ";
							}
							$Buffer.= ">";
							$ii++;
							$Buffer.= $columna;
							if($jj==3||count($fila)==1)
								$Buffer.= "</th>";
							else
								$Buffer.= "</td>";
						}
						$Buffer.= "</tr>\n";
					}
					$Buffer.= "</table>\n";
					break;
					case 'X':
						foreach($this->Filas as $YPos=>$fila){
							ksort($fila);
							foreach($fila as $XPos=>$columna) if(is_array($columna)&&$columna[0]===false) unset($fila[$XPos]);
							$Buffer.=implode(',',$fila)."\n";
						}
						return $Buffer;
						break;
					case 'X1':
						$jj=0;
						foreach($this->Filas as $YPos=>$fila){
							ksort($fila);
							foreach($fila as $XPos=>$columna) if(is_array($columna)&&$columna[0]===false) unset($fila[$XPos]);
							if(count($fila)==1)
							{
								$columna=reset($fila);
								if($_SESSION['CompanyRecord']['coyname']==$columna)
									$jj=0;
							}
							$jj++;
							if($jj<2){
								$Buffer.= "\n";
							}
							if($jj<3) continue;

							$Buffer.= "\n";
							$ii=0;

							foreach($fila as $XPos=>$columna){
								if(_('Grand Total Value')==$columna){
									$Buffer.= "\n";
									$jj=3;
								}
								$ii++;
								$Buffer.= '"'.($columna).'"';
								if($jj==3||count($fila)==1)
									$Buffer.= "|";
								else
									$Buffer.= "|";
							}
							$Buffer.= "\n";
						}
						$Buffer.= "\n";
					break;
				}
				return $Buffer;
			}
			public function __destruct(){

			}
			public function setPrintHeader($val=true) {
				$this->print_header = $val ? true : false;
			}
		}
		$pdf = new pdfHTML($DocumentOrientation, 'pt', $DocumentPaper);
		$pdf->SetAutoPageBreak(true, 0);
		$pdf->SetPrintHeader(false);
		$pdf->AddPage();
		$pdf->cMargin = 0;
		$pdf->Page_Width=$Page_Width;
		$pdf->Page_Height=$Page_Height;
	}
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

      /*Now figure out the inventory data to report for the category range under review */
	if ($_POST['Location']=='All'){
		$SQL = "SELECT stockmaster.categoryid,
				stockcategory.categorydescription,
				stockmaster.id_agrupador,
				stockmaster.barcode,
				stockmaster.stockid,
				stockmaster.description,
				stockserialitems.serialno,
				stockserialitems.expirationdate,
				stockserialitems.quantity AS qtyonhand,
				(rh_etiquetas.barcode) AS Etiqueta,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				stockserialitems.quantity *(stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost) AS itemtotal
			FROM stockmaster,
				stockcategory,
				stockserialitems
				LEFT JOIN rh_etiquetas ON stockserialitems.stockid = rh_etiquetas.stockid AND stockserialitems.serialno = rh_etiquetas.serialno
			WHERE stockmaster.stockid=stockserialitems.stockid
			AND stockmaster.categoryid=stockcategory.categoryid
			".$obsolete."
			AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
			AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
			AND stockserialitems.quantity!=0
			ORDER BY stockmaster.categoryid,
				expirationdate asc, stockmaster.stockid";
	} else {
		$SQL = "SELECT stockmaster.categoryid,
				stockcategory.categorydescription,
				stockmaster.id_agrupador,
				stockmaster.barcode,
				stockmaster.stockid,
				stockmaster.description,
				stockserialitems.serialno,
				stockserialitems.expirationdate,
				stockserialitems.quantity AS qtyonhand,
				(rh_etiquetas.barcode) AS Etiqueta,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				stockserialitems.quantity *(stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost) AS itemtotal
			FROM stockmaster,
				stockcategory,
				stockserialitems
				LEFT JOIN rh_etiquetas ON stockserialitems.stockid = rh_etiquetas.stockid AND stockserialitems.serialno = rh_etiquetas.serialno
			WHERE stockmaster.stockid=stockserialitems.stockid
			AND stockmaster.categoryid=stockcategory.categoryid
			AND stockserialitems.quantity!=0
			AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
			AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
			AND stockserialitems.loccode = '" . $_POST['Location'] . "'
			".$obsolete."
			ORDER BY stockmaster.categoryid,
				expirationdate asc, stockmaster.stockid";
	}
	// echo $SQL; exit;
	$InventoryResult = DB_query($SQL,$db,'','',false,true);

	if (DB_error_no($db) !=0) {
	  $title = _('Inventory Valuation') . ' - ' . _('Problem Report');
	  include('includes/header.inc');
	   prnMsg( _('The inventory could not be retrieved by the SQL because') . ' '  . DB_error_msg($db),'error');
	   echo "<BR><A HREF='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</A>';
	   if ($debug==1){
	      echo "<BR>$SQL";
	   }
	   include('includes/footer.inc');
	   exit;
	}
	$imprimirheader=true;
	include ('includes/rh_PDFexpirationdate.php');
	$imprimirheader=!isset($_REQUEST['Excel']);
        $Tot_Val=0;
	$Category = '';
	$CatTot_Val=0;
        $CatTot_Qty=0;
	$margen=0;
	While ($InventoryValn = DB_fetch_array($InventoryResult,$db)){

		if ($Category!=$InventoryValn['categoryid']){
			$FontSize=10;
			if ($Category!=''){ /*Then it's NOT the first time round */

				/* need to print the total of previous category */
				if ($_POST['DetailedReport']=='Yes'){
					$YPos -= (2*$line_height);
					if ($YPos < $Bottom_Margin + (3*$line_height)){
		 				  include('includes/rh_PDFexpirationdate.php');
					}
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,_('Total for') . ' ' . $Category . ' - ' . $CategoryName,'',$margen);
				}

				$DisplayCatTotVal = number_format($CatTot_Val,2);
				$DisplayCatTotQty = number_format($CatTot_Qty,0);
                                $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right',$margen);
	                        $LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayCatTotQty, 'right',$margen);
                                $YPos -=$line_height;

				If ($_POST['DetailedReport']=='Yes'){
				/*draw a line under the CATEGORY TOTAL*/
					$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
					$YPos -=(2*$line_height);
				}
				$CatTot_Val=0;
                                $CatTot_Qty=0;
			}
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,$InventoryValn['categoryid'] . ' - ' . $InventoryValn['categorydescription'],'',$margen);
			$Category = $InventoryValn['categoryid'];
			$CategoryName = $InventoryValn['categorydescription'];
		}


		if ($_POST['DetailedReport']=='Yes'){
			$YPos -=$line_height;
			$FontSize=8;
			$XPos=$Left_Margin;
			$LeftOvers = $pdf->addTextWrap($XPos,$YPos,50,$FontSize,$InventoryValn['stockid'],'',$margen);$XPos+=50;
			$LeftOvers = $pdf->addTextWrap($XPos,$YPos,40,$FontSize,$InventoryValn['id_agrupador'], 'right',$margen);$XPos+=40;
			$LeftOvers = $pdf->addTextWrap($XPos,$YPos,70,$FontSize,$InventoryValn['barcode'], 'right',$margen);$XPos+=70;
			$LeftOvers = $pdf->addTextWrap($XPos,$YPos,70,$FontSize,$InventoryValn['Etiqueta'], 'right',$margen);$XPos+=70;

			$LeftOvers = $pdf->addTextWrap($XPos,$YPos,140,$FontSize,$InventoryValn['description'],'',$margen);$XPos+=140;


			$DisplayUnitCost = number_format($InventoryValn['unitcost'],2);
			$DisplayQtyOnHand = number_format($InventoryValn['qtyonhand'],2);
			$DisplayItemTotal = number_format($InventoryValn['itemtotal'],2);

			$LeftOvers = $pdf->addTextWrap($XPos,$YPos,40,$FontSize,$DisplayQtyOnHand,'right',$margen);$XPos+=40;
			$LeftOvers = $pdf->addTextWrap($XPos,$YPos,60,$FontSize,$InventoryValn['serialno'], 'right',$margen);$XPos+=60;
			$LeftOvers = $pdf->addTextWrap($XPos,$YPos,70,$FontSize,$InventoryValn['expirationdate'], 'right',$margen);$XPos+=70;

		}

/*
		if ($_POST['DetailedReport']=='Yes'){

			$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,300-$Left_Margin,$FontSize,_('Category') . '/' . _('Item'), 'center');
			$LeftOvers = $pdf->addTextWrap(390,$YPos,60,$FontSize,_('Quantity'), 'center');
			//$LeftOvers = $pdf->addTextWrap(450,$YPos,60,$FontSize,_('Unit Cost'), 'centre');
			//$LeftOvers = $pdf->addTextWrap(510,$YPos,60,$FontSize,_('Item Value'), 'centre');
			$LeftOvers = $pdf->addTextWrap(450,$YPos,60,$FontSize,_('serialno'), 'center');
			$LeftOvers = $pdf->addTextWrap(510,$YPos,60,$FontSize,_('expirationdate'), 'center');
		} else {
			$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,320-$Left_Margin,$FontSize,_('Category'), 'center');
			$LeftOvers = $pdf->addTextWrap(510,$YPos,60,$FontSize,_('Value'), 'centre');
		}
*/



		$Tot_Val += $InventoryValn['itemtotal'];
		$CatTot_Val += $InventoryValn['itemtotal'];
                $CatTot_Qty += $InventoryValn['qtyonhand'];

		if ($YPos < $Bottom_Margin + $line_height){
		    include('includes/rh_PDFexpirationdate.php');
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

	$YPos -= (2*$line_height);

	if ($YPos < $Bottom_Margin + $line_height){
		   include('includes/rh_PDFexpirationdate.php');
	}
/*Print out the grand totals */
	$LeftOvers = $pdf->addTextWrap(80,$YPos,260-$Left_Margin,$FontSize,_('Grand Total Value'), 'right');
	$DisplayTotalVal = number_format($Tot_Val,2);
        $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayTotalVal, 'right');

	if(isset($_REQUEST['Excel']))
		$pdfcode = $pdf->output('X');
	else
		$pdfcode = $pdf->output();
	$len = strlen($pdfcode);

      if ($len<=20){
		$title = _('Print Inventory Error');
		include('includes/header.inc');
		prnMsg(_('There were no items with any value to print out for the location specified'),'error');
		echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
		include('includes/footer.inc');
		exit;
      } else {
      		header('Content-type: application/pdf');
		header("Content-Length: " . $len);
		if(isset($_REQUEST['Excel']))
      		header('Content-type: application/xls');
      		else
      		header('Content-type: application/pdf');
		header("Content-Length: " . $len);
		if(isset($_REQUEST['Excel']))
			header('Content-Disposition: inline; filename=Customer_trans.csv');
			else
		header('Content-Disposition: inline; filename=Customer_trans.pdf');
		header('Expires: 0');
		header('Cache-Control: private, post-check=0, pre-check=0');
		header('Pragma: public');

		echo $pdfcode;

	}
} else { /*The option to print PDF was not hit */

	$title=_('Inventory Valuation Reporting');
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

		echo '<TR><TD>' . _('For Inventory in Location') . ":</TD><TD><SELECT name='Location'>";
		$sql = 'SELECT loccode, locationname FROM locations';
		$LocnResult=DB_query($sql,$db);

		echo "<OPTION Value='All'>" . _('All Locations');

		while ($myrow=DB_fetch_array($LocnResult)){
		          echo "<OPTION Value='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
		      		}
		echo '</SELECT></TD></TR>';

		echo '<TR><TD>' . _('Summary or Detailed Report') . ":</TD><TD><SELECT name='DetailedReport'>";
		//echo "<OPTION SELECTED Value='No'>" . _('Summary Report');
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

		echo "</TABLE><INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') . "'><INPUT TYPE=Submit Name='Excel' Value='" . _('Excel') . "'></CENTER>";
	}
	include('includes/footer.inc');

} /*end of else not PrintPDF */

?>
