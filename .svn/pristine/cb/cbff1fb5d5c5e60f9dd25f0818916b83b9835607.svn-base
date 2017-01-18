<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
/* webERP Revision: 14 $ */
$PageSecurity = 2;
include('includes/session.inc');
$staux="Precio";

If ((isset($_POST['PrintPDF'])|| isset($_REQUEST['Excel']))
	AND isset($_POST['FromCriteria'])
	AND strlen($_POST['FromCriteria'])>=1
	AND isset($_POST['ToCriteria'])
	AND strlen($_POST['ToCriteria'])>=1){

	//include('includes/session.inc');
	//include('config.php');
	include('includes/PDFStarter.php');
	//include('includes/ConnectDB.inc');
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
					[(int)($XPos)]=html_entity_decode('"'.str_replace(',','\\,',addslashes(trim($Text))).'"',ENT_COMPAT | ENT_HTML401,'UTF-8');
					
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
								$Buffer.= '"'.addslashes($columna).'"';
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
	$FontSize=10;
	$pdf->addinfo('Title',_('Inventory Valuation Report'));
	$pdf->addinfo('Subject',_('Inventory Valuation'));

	$PageNumber=1;
	$line_height=12;

      /*Now figure out the inventory data to report for the category range under review */
	if ($_POST['Location']=='All'){
		$SQL = "SELECT stockmaster.categoryid,
				stockcategory.categorydescription,
				stockmaster.stockid,
				stockmaster.description,
				stockmaster.id_agrupador,
				stockmaster.barcode,
				SUM(locstock.quantity) AS qtyonhand,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				SUM(locstock.quantity) *(stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost) AS itemtotal
				, prices.typeabbrev, prices.currabrev, prices.debtorno, prices.price
			FROM stockmaster,
				stockcategory,
				locstock, prices
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.categoryid=stockcategory.categoryid
			AND stockmaster.stockid=prices.stockid
			GROUP BY stockmaster.categoryid,
				stockcategory.categorydescription,
				unitcost,
				stockmaster.stockid,
				stockmaster.description
			HAVING stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
			AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
			AND prices.price=0
			ORDER BY stockmaster.categoryid,
				stockmaster.stockid";
	} else {
		$SQL = "SELECT stockmaster.categoryid,
				stockcategory.categorydescription,
				stockmaster.stockid,
				stockmaster.description,
				stockmaster.id_agrupador,
				stockmaster.barcode,
				locstock.quantity AS qtyonhand,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				locstock.quantity *(stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost) AS itemtotal
				, prices.typeabbrev, prices.currabrev, prices.debtorno, prices.price
			FROM stockmaster,
				stockcategory,
				locstock, prices
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.categoryid=stockcategory.categoryid
			AND locstock.quantity!=0
			AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
			AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
			AND locstock.loccode = '" . $_POST['Location'] . "'
			AND prices.price=0
			AND stockmaster.stockid=prices.stockid
			ORDER BY stockmaster.categoryid,
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
	While ($InventoryValn = DB_fetch_array($InventoryResult,$db)){

		if ($Category!=$InventoryValn['categoryid']){
			$FontSize=10;
			if ($Category!=''){ /*Then it's NOT the first time round */

				/* need to print the total of previous category */
				if ($_POST['DetailedReport']=='Yes'){
					$YPos -= (2*$line_height);
					if ($YPos < $Bottom_Margin + (3*$line_height)){
		 				  include('includes/PDFInventoryValnPageHeader.inc');
					}
					//$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,_('Total for') . ' ' . $Category . ' - ' . $CategoryName);
				}

				$DisplayCatTotVal = number_format($CatTot_Val,2);
				$LeftOvers = //$pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right');
				$YPos -=$line_height;

				If ($_POST['DetailedReport']=='Yes'){
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
			$YPos -=$line_height;
			$FontSize=8;


			

			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,40,$FontSize,$InventoryValn['stockid'], '');
			$LeftOvers = $pdf->addTextWrap(80,$YPos,60,$FontSize,$InventoryValn['id_agrupador'], 'right');
			$LeftOvers = $pdf->addTextWrap(150,$YPos,70,$FontSize,$InventoryValn['barcode'], 'right');						
			$LeftOvers = $pdf->addTextWrap(230,$YPos,140,$FontSize,$InventoryValn['description'], '');
			$LeftOvers = $pdf->addTextWrap(230,$YPos-$FontSize,140,$FontSize,$LeftOvers, '');
			$DisplayUnitCost = number_format($InventoryValn['price'],2);
			$DisplayQtyOnHand = number_format($InventoryValn['qtyonhand'],0);
			$DisplayItemTotal = $InventoryValn['typeabbrev'];

			$LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayQtyOnHand,'right');
			$LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize,$DisplayUnitCost, 'right');
			$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayItemTotal, 'right');


			// $LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,$InventoryValn['stockid']);				
			// $LeftOvers = $pdf->addTextWrap(120,$YPos,260,$FontSize,$InventoryValn['description']. ' ' .$InventoryValn['debtorno']);
			// $DisplayUnitCost = number_format($InventoryValn['price'],2);
			// $DisplayQtyOnHand = number_format($InventoryValn['qtyonhand'],0);
			// $DisplayItemTotal = $InventoryValn['typeabbrev'];

			// $LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayQtyOnHand,'right');
			// $LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize,$DisplayUnitCost, 'right');
			// $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayItemTotal, 'right');

		}
		$Tot_Val += $InventoryValn['itemtotal'];
		$CatTot_Val += $InventoryValn['itemtotal'];

		if ($YPos < $Bottom_Margin + $line_height){
		   include('includes/PDFInventoryValnPageHeader.inc');
		}

	} /*end inventory valn while loop */

	$FontSize =10;
/*Print out the category totals */
	if ($_POST['DetailedReport']=='Yes'){
		$YPos -= (2*$line_height);
		//$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize, _('Total for') . ' ' . $Category . ' - ' . $CategoryName, 'left');
	}
	$DisplayCatTotVal = number_format($CatTot_Val,2);
	//$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right');
	
	
	If ($_POST['DetailedReport']=='Yes'){
		/*draw a line under the CATEGORY TOTAL*/
		$YPos -= ($line_height);
		//$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
	}
	
	$YPos -= (2*$line_height);

	if ($YPos < $Bottom_Margin + $line_height){
		   include('includes/PDFInventoryValnPageHeader.inc');
	}
/*Print out the grand totals */
	//$LeftOvers = $pdf->addTextWrap(80,$YPos,260-$Left_Margin,$FontSize,_('Grand Total Value'), 'right');
	$DisplayTotalVal = number_format($Tot_Val,2);
	//$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayTotalVal, 'right');
	

	if(isset($_REQUEST['Excel'])) 
		$pdfcode = $pdf->output('X');
	else 
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
      	if(isset($_REQUEST['Excel']))
      		header('Content-type: application/xls');
      	else
      		header('Content-type: application/pdf');
		header("Content-Length: " . $len);
		if(isset($_REQUEST['Excel']))
			header('Content-Disposition: inline; filename=PrecioCero.csv');
		else
		header('Content-Disposition: inline; filename=PrecioCero.pdf');
		header('Expires: 0');
		header('Cache-Control: private, post-check=0, pre-check=0');
		header('Pragma: public');
		echo $pdfcode;
		

	}
} else { /*The option to print PDF was not hit */

	//include('includes/session.inc');
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
		echo "<OPTION SELECTED Value='No'>" . _('Summary Report');
		echo "<OPTION Value='Yes'>" . _('Detailed Report');
		echo '</SELECT></TD></TR>';

		echo "</TABLE><INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') . "'>";
		echo "<INPUT TYPE=Submit Name='Excel' Value='" . _('Excel') . "'>";
		echo "</CENTER>";
	}
	include('includes/footer.inc');

} /*end of else not PrintPDF */

?>