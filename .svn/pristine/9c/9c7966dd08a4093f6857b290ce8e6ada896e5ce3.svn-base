<?php

$PageSecurity = 2;
include('includes/session.inc');

/*
*
* DESCARGAR EL EXCEL
*
*/
If (isset($_POST['PrintExcelx'])
	AND isset($_POST['FromCriteria'])
	AND strlen($_POST['FromCriteria'])>=1
	AND isset($_POST['ToCriteria'])
	AND strlen($_POST['ToCriteria'])>=1){


	// bowikaxu realhost - 18 july 2008 - show obsoletes?
	if($_POST['obsolete']=='on'){
		$obsolete = "";
	}else {
		$obsolete = " AND stockmaster.discontinued = 0 ";
	}

      /*Now figure out the inventory data to report for the category range under review
      need QOH, QOO, QDem, Sales Mth -1, Sales Mth -2, Sales Mth -3, Sales Mth -4*/
	if ($_POST['Location']=='All'){
		$SQL = "SELECT stockmaster.categoryid,
				stockmaster.description,
				stockcategory.categorydescription,
				locstock.stockid,
				SUM(locstock.quantity) AS qoh
			FROM locstock,
				stockmaster,
				stockcategory
			WHERE locstock.stockid=stockmaster.stockid
			AND stockmaster.categoryid=stockcategory.categoryid
			AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
			AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
			AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
			".$obsolete."
			GROUP BY stockmaster.categoryid,
				stockmaster.description,
				stockcategory.categorydescription,
				locstock.stockid,
				stockmaster.stockid
			ORDER BY stockmaster.categoryid,
				stockmaster.stockid";
	} else {
		$SQL = "SELECT stockmaster.categoryid,
					locstock.stockid,
					stockmaster.description,
					stockcategory.categorydescription,
					locstock.quantity  AS qoh
				FROM locstock,
					stockmaster,
					stockcategory
				WHERE locstock.stockid=stockmaster.stockid
				AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
				AND stockmaster.categoryid=stockcategory.categoryid
				AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
				AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
				AND locstock.loccode = '" . $_POST['Location'] . "'
				".$obsolete."
				ORDER BY stockmaster.categoryid,
					stockmaster.stockid";

	}
	$InventoryResult = DB_query($SQL,$db,'','',false,false);

	if (DB_error_no($db) !=0) {
	  $title = _('Inventory Planning') . ' - ' . _('Problem Report') . '....';
	  include('includes/header.inc');
	   prnMsg(_('The inventory quantities could not be retrieved by the SQL because') . ' - ' . DB_error_msg($db),'error');
	   echo "<BR><A HREF='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</A>';
	   if ($debug==1){
	      echo "<BR>$SQL";
	   }
	   include('includes/footer.inc');
	   exit;
	}
	$Period_0_Name = strftime('%b',mktime(0,0,0,Date('m'),Date('d'),Date('Y')));
	$Period_1_Name = strftime('%b',mktime(0,0,0,Date('m')-1,Date('d'),Date('Y')));
	$Period_2_Name = strftime('%b',mktime(0,0,0,Date('m')-2,Date('d'),Date('Y')));
	$Period_3_Name = strftime('%b',mktime(0,0,0,Date('m')-3,Date('d'),Date('Y')));
	$Period_4_Name = strftime('%b',mktime(0,0,0,Date('m')-4,Date('d'),Date('Y')));

	/*Libreria para exportar a excel */
	require 'includes/PHPExcel/Classes/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$i=3;
	/*Asignamos los encabezados al archivo*/
	$objPHPExcel->getActiveSheet()->    setCellValue('A1', "Inventory Planning Report". Date($_SESSION['DefaultDateFormat']));
	
	$objPHPExcel->getActiveSheet()->    setCellValue('A2', _('Item'))
									  ->setCellValue('B2', _('Description'))
									  ->setCellValue('C2',  _('Qty'))
									  ->setCellValue('D2',  _('Qty'))
									  ->setCellValue('E2',  _('Qty'))
									  ->setCellValue('F2',  _('Qty'))
									  ->setCellValue('G2',  _('Qty'))
									  ->setCellValue('H2', _('MTD'))
									  ->setCellValue('I2', _('ms stk'))
									  ->setCellValue('J2', _('QOH'))
									  ->setCellValue('K2', _('Cust Ords'))
									  ->setCellValue('L2', _('Splr Ords'))
									  ->setCellValue('M2', _('Sugg Ord'));

	$Category = '';

	$CurrentPeriod = GetPeriod(Date($_SESSION['DefaultDateFormat']),$db);
	$Period_1 = $CurrentPeriod -1;
	$Period_2 = $CurrentPeriod -2;
	$Period_3 = $CurrentPeriod -3;
	$Period_4 = $CurrentPeriod -4;

	While ($InventoryPlan = DB_fetch_array($InventoryResult,$db)){

		if ($Category!=$InventoryPlan['categoryid']){
			/*Asignamos el nombre de la categoria*/
			$objPHPExcel->getActiveSheet()->    setCellValue('A'.$i, $InventoryPlan['categoryid'] . ' - ' . $InventoryPlan['categorydescription']);
			$i++;
			}
			$Category = $InventoryPlan['categoryid'];
		

		


		if ($_POST['Location']=='All'){
   		   $SQL = "SELECT SUM(CASE WHEN prd=" . $CurrentPeriod . " THEN -qty ELSE 0 END) AS prd0,
		   		SUM(CASE WHEN prd=" . $Period_1 . " THEN -qty ELSE 0 END) AS prd1,
				SUM(CASE WHEN prd=" . $Period_2 . " THEN -qty ELSE 0 END) AS prd2,
				SUM(CASE WHEN prd=" . $Period_3 . " THEN -qty ELSE 0 END) AS prd3,
				SUM(CASE WHEN prd=" . $Period_4 . " THEN -qty ELSE 0 END) AS prd4
			FROM stockmoves
			WHERE stockid='" . $InventoryPlan['stockid'] . "'
			AND (type=10 OR type=11)
			AND stockmoves.hidemovt=0";
		} else {
  		   $SQL = "SELECT SUM(CASE WHEN prd=" . $CurrentPeriod . " THEN -qty ELSE 0 END) AS prd0,
		   		SUM(CASE WHEN prd=" . $Period_1 . " THEN -qty ELSE 0 END) AS prd1,
				SUM(CASE WHEN prd=" . $Period_2 . " THEN -qty ELSE 0 END) AS prd2,
				SUM(CASE WHEN prd=" . $Period_3 . " THEN -qty ELSE 0 END) AS prd3,
				SUM(CASE WHEN prd=" . $Period_4 . " THEN -qty ELSE 0 END) AS prd4
			FROM stockmoves
			WHERE stockid='" . $InventoryPlan['stockid'] . "'
			AND stockmoves.loccode ='" . $_POST['Location'] . "'
			AND (stockmoves.type=10 OR stockmoves.type=11)
			AND stockmoves.hidemovt=0";
		}

		$SalesResult=DB_query($SQL,$db,'','',FALSE,FALSE);

		if (DB_error_no($db) !=0) {
	 		 $title = _('Inventory Planning') . ' - ' . _('Problem Report') . '....';
	  		include('includes/header.inc');
	   		prnMsg( _('The sales quantities could not be retrieved by the SQL because') . ' - ' . DB_error_msg($db),'error');
	   		echo "<BR><A HREF='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</A>';
	   		if ($debug==1){
	      			echo "<BR>$SQL";
	   		}
	   		include('includes/footer.inc');
	   		exit;
		}

		$SalesRow = DB_fetch_array($SalesResult);

		if ($_POST['Location']=='All'){
			// bowikaxu realhost - may 2007 - rh_status
			$SQL = "SELECT SUM(salesorderdetails.quantity - salesorderdetails.qtyinvoiced) AS qtydemand
				FROM salesorderdetails,
					salesorders
				WHERE salesorderdetails.orderno=salesorders.orderno
				AND salesorderdetails.stkcode = '" . $InventoryPlan['stockid'] . "'
				AND salesorderdetails.completed = 0
				AND salesorders.rh_status = 0";
		} else {
			// bowikaxu realhost - may 2007 - rh_status
			$SQL = "SELECT SUM(salesorderdetails.quantity - salesorderdetails.qtyinvoiced) AS qtydemand
				FROM salesorderdetails,
					salesorders
				WHERE salesorderdetails.orderno=salesorders.orderno
				AND salesorders.fromstkloc ='" . $_POST['Location'] . "'
				AND salesorderdetails.stkcode = '" . $InventoryPlan['stockid'] . "'
				AND salesorderdetails.completed = 0
				AND salesorders.rh_status = 0";
		}

		$DemandResult = DB_query($SQL,$db,'','',FALSE,FALSE);

		if (DB_error_no($db) !=0) {
	 		 $title = _('Inventory Planning') . ' - ' . _('Problem Report') . '....';
	  		include('includes/header.inc');
	   		prnMsg( _('The sales order demand quantities could not be retrieved by the SQL because') . ' - ' . DB_error_msg($db),'error');
	   		echo "<BR><A HREF='" .$rootpath ."/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
	   		if ($debug==1){
	      			echo "<BR>$SQL";
	   		}
	   		include('includes/footer.inc');
	   		exit;
		}

//Also need to add in the demand as a component of an assembly items if this items has any assembly parents.

		if ($_POST['Location']=='All'){
			$SQL = "SELECT SUM((salesorderdetails.quantity-salesorderdetails.qtyinvoiced)*bom.quantity) AS dem
				FROM salesorderdetails,
					bom,
					stockmaster
				WHERE salesorderdetails.stkcode=bom.parent
				AND salesorderdetails.quantity-salesorderdetails.qtyinvoiced > 0
				AND bom.component='" . $InventoryPlan['stockid'] . "'
				AND stockmaster.stockid=bom.parent
				AND (stockmaster.mbflag='A' OR stockmaster.mbflag='E')
				AND salesorderdetails.completed=0";
		} else {
			$SQL = "SELECT SUM((salesorderdetails.quantity-salesorderdetails.qtyinvoiced)*bom.quantity) AS dem
				FROM salesorderdetails,
					salesorders,
					bom,
					stockmaster
				WHERE salesorderdetails.orderno=salesorders.orderno
				AND salesorderdetails.stkcode=bom.parent
				AND salesorderdetails.quantity-salesorderdetails.qtyinvoiced > 0
				AND bom.component='" . $InventoryPlan['stockid'] . "'
				AND stockmaster.stockid=bom.parent
				AND salesorders.fromstkloc ='" . $_POST['Location'] . "'
				AND (stockmaster.mbflag='A' OR stockmaster.mbflag = 'E')
				AND salesorderdetails.completed=0";
		}

		$BOMDemandResult = DB_query($SQL,$db,'','',false,false);

		if (DB_error_no($db) !=0) {
	 		$title = _('Inventory Planning') . ' - ' . _('Problem Report') . '....';
	  		include('includes/header.inc');
	   		prnMsg( _('The sales order demand quantities from parent assemblies could not be retrieved by the SQL because') . ' - ' . DB_error_msg($db),'error');
	   		echo "<BR><A HREF='" .$rootpath ."/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
	   		if ($debug==1){
	      			echo "<BR>$SQL";
	   		}
	   		include('includes/footer.inc');
	   		exit;
		}

		if ($_POST['Location']=='All'){
			$SQL = "SELECT SUM(purchorderdetails.quantityord - purchorderdetails.quantityrecd) as qtyonorder
				FROM purchorderdetails,
					purchorders
				WHERE purchorderdetails.orderno = purchorders.orderno
				AND purchorderdetails.itemcode = '" . $InventoryPlan['stockid'] . "'
				AND purchorderdetails.completed = 0";
		} else {
			$SQL = "SELECT SUM(purchorderdetails.quantityord - purchorderdetails.quantityrecd) AS qtyonorder
				FROM purchorderdetails,
					purchorders
				WHERE purchorderdetails.orderno = purchorders.orderno
				AND purchorderdetails.itemcode = '" . $InventoryPlan['stockid'] . "'
				AND purchorderdetails.completed = 0
				AND purchorders.intostocklocation=  '" . $_POST['Location'] . "'";
		}

		$DemandRow = DB_fetch_array($DemandResult);
		$BOMDemandRow = DB_fetch_array($BOMDemandResult);
		$TotalDemand = $DemandRow['qtydemand'] + $BOMDemandRow['dem'];

		$OnOrdResult = DB_query($SQL,$db,'','',false,false);
		if (DB_error_no($db) !=0) {
	 		 $title = _('Inventory Planning') . ' - ' . _('Problem Report') . '....';
	  		include('includes/header.inc');
	   		prnMsg( _('The purchase order quantities could not be retrieved by the SQL because') . ' - ' . DB_error_msg($db),'error');
	   		echo "<BR><A HREF='" .$rootpath ."/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
	   		if ($debug==1){
	      			echo "<BR>$SQL";
	   		}
	   		include('includes/footer.inc');
	   		exit;
		}

		$OnOrdRow = DB_fetch_array($OnOrdResult);
		$MaxMthSales = Max($SalesRow['prd1'], $SalesRow['prd2'], $SalesRow['prd3'], $SalesRow['prd4']);
		$IdealStockHolding = $MaxMthSales * $_POST['NumberMonthsHolding'];
		$SuggestedTopUpOrder = $IdealStockHolding - $InventoryPlan['qoh'] + $TotalDemand - $OnOrdRow['qtyonorder'];
		
		if ($SuggestedTopUpOrder <=0){


		} else {

			
		}

	/* Le ingresamos los datos*/
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $InventoryPlan['stockid'])
										  ->setCellValue('B'.$i, $InventoryPlan['description'])
										  ->setCellValue('C'.$i, number_format($SalesRow['prd4'],0))
										  ->setCellValue('D'.$i, number_format($SalesRow['prd3'],0))
										  ->setCellValue('E'.$i, number_format($SalesRow['prd2'],0))
										  ->setCellValue('F'.$i, number_format($SalesRow['prd1'],0))
										  ->setCellValue('G'.$i, number_format($SalesRow['prd0'],0))
										  ->setCellValue('H'.$i, number_format($IdealStockHolding,0))
										  ->setCellValue('I'.$i, number_format($InventoryPlan['qoh'],0))
										  ->setCellValue('J'.$i, number_format($TotalDemand,0))
										  ->setCellValue('K'.$i, number_format($OnOrdRow['qtyonorder'],0))
										  ->setCellValue('L'.$i, number_format($SuggestedTopUpOrder,0));
										  
										  
	$i++;


	} /*end inventory valn while loop */

	// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="reporte-'.date('YmdHis').'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;


}

/*
*
* PARA DESCARGAR EL PDF
*
*/
If ((isset($_POST['PrintPDF'])||isset($_POST['PrintExcel']))
	AND isset($_POST['FromCriteria'])
	AND strlen($_POST['FromCriteria'])>=1
	AND isset($_POST['ToCriteria'])
	AND strlen($_POST['ToCriteria'])>=1){

        include ('includes/class.pdf.php');

	/* A4_Landscape */

	$Page_Width=842;
	$Page_Height=595;
	$Top_Margin=20;
	$Bottom_Margin=20;
	$Left_Margin=25;
	$Right_Margin=22;

	$PageSize = array(0,0,$Page_Width,$Page_Height);
	if(isset($_REQUEST['PrintExcel']))
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
	}else{
		class pdfHTML extends Cpdf{
		}	
	}
		$pdf = & new pdfHTML($PageSize);

	$PageNumber = 0;

	$pdf->selectFont('./fonts/Helvetica.afm');

/* Standard PDF file creation header stuff */

	$pdf->addinfo('Author','webERP ' . $Version);
	$pdf->addinfo('Creator','webERP http://www.weberp.org');
	$pdf->addinfo('Title',_('Inventory Planning Report') . ' ' . Date($_SESSION['DefaultDateFormat']));

	$line_height=12;

	$pdf->addinfo('Subject',_('Inventory Planning'));

	$PageNumber=1;
	$line_height=12;
	
	// bowikaxu realhost - 18 july 2008 - show obsoletes?
	if($_POST['obsolete']=='on'){
		$obsolete = "";
	}else {
		$obsolete = " AND stockmaster.discontinued = 0 ";
	}

      /*Now figure out the inventory data to report for the category range under review
      need QOH, QOO, QDem, Sales Mth -1, Sales Mth -2, Sales Mth -3, Sales Mth -4*/
	if ($_POST['Location']=='All'){
		$SQL = "SELECT stockmaster.categoryid,
				stockmaster.description,
				stockcategory.categorydescription,
				locstock.stockid,
				SUM(locstock.quantity) AS qoh
			FROM locstock,
				stockmaster,
				stockcategory
			WHERE locstock.stockid=stockmaster.stockid
			AND stockmaster.categoryid=stockcategory.categoryid
			AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
			AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
			AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
			".$obsolete."
			GROUP BY stockmaster.categoryid,
				stockmaster.description,
				stockcategory.categorydescription,
				locstock.stockid,
				stockmaster.stockid
			ORDER BY stockmaster.categoryid,
				stockmaster.stockid";
	} else {
		$SQL = "SELECT stockmaster.categoryid,
					locstock.stockid,
					stockmaster.description,
					stockcategory.categorydescription,
					locstock.quantity  AS qoh
				FROM locstock,
					stockmaster,
					stockcategory
				WHERE locstock.stockid=stockmaster.stockid
				AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
				AND stockmaster.categoryid=stockcategory.categoryid
				AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
				AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
				AND locstock.loccode = '" . $_POST['Location'] . "'
				".$obsolete."
				ORDER BY stockmaster.categoryid,
					stockmaster.stockid";

	}
	$InventoryResult = DB_query($SQL,$db,'','',false,false);

	if (DB_error_no($db) !=0) {
	  $title = _('Inventory Planning') . ' - ' . _('Problem Report') . '....';
	  include('includes/header.inc');
	   prnMsg(_('The inventory quantities could not be retrieved by the SQL because') . ' - ' . DB_error_msg($db),'error');
	   echo "<BR><A HREF='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</A>';
	   if ($debug==1){
	      echo "<BR>$SQL";
	   }
	   include('includes/footer.inc');
	   exit;
	}
	$Period_0_Name = strftime('%b',mktime(0,0,0,Date('m'),Date('d'),Date('Y')));
	$Period_1_Name = strftime('%b',mktime(0,0,0,Date('m')-1,Date('d'),Date('Y')));
	$Period_2_Name = strftime('%b',mktime(0,0,0,Date('m')-2,Date('d'),Date('Y')));
	$Period_3_Name = strftime('%b',mktime(0,0,0,Date('m')-3,Date('d'),Date('Y')));
	$Period_4_Name = strftime('%b',mktime(0,0,0,Date('m')-4,Date('d'),Date('Y')));

	include ('includes/PDFInventoryPlanPageHeader.inc');

	$Category = '';

	$CurrentPeriod = GetPeriod(Date($_SESSION['DefaultDateFormat']),$db);
	$Period_1 = $CurrentPeriod -1;
	$Period_2 = $CurrentPeriod -2;
	$Period_3 = $CurrentPeriod -3;
	$Period_4 = $CurrentPeriod -4;

	While ($InventoryPlan = DB_fetch_array($InventoryResult,$db)){

		if ($Category!=$InventoryPlan['categoryid']){
			$FontSize=10;
			if ($Category!=''){ /*Then it's NOT the first time round */
				/*draw a line under the CATEGORY TOTAL*/
				$YPos -=$line_height;
		   		$pdf->line($Left_Margin, $YPos,$Page_Width-$Right_Margin, $YPos);
				$YPos -=(2*$line_height);
			}
			$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos, 260-$Left_Margin,$FontSize,$InventoryPlan['categoryid'] . ' - ' . $InventoryPlan['categorydescription'],'left');
			$Category = $InventoryPlan['categoryid'];
			$FontSize=8;
		}

		$YPos -=$line_height;


		if ($_POST['Location']=='All'){
   		   $SQL = "SELECT SUM(CASE WHEN prd=" . $CurrentPeriod . " THEN -qty ELSE 0 END) AS prd0,
		   		SUM(CASE WHEN prd=" . $Period_1 . " THEN -qty ELSE 0 END) AS prd1,
				SUM(CASE WHEN prd=" . $Period_2 . " THEN -qty ELSE 0 END) AS prd2,
				SUM(CASE WHEN prd=" . $Period_3 . " THEN -qty ELSE 0 END) AS prd3,
				SUM(CASE WHEN prd=" . $Period_4 . " THEN -qty ELSE 0 END) AS prd4
			FROM stockmoves
			WHERE stockid='" . $InventoryPlan['stockid'] . "'
			AND (type=10 OR type=11)
			AND stockmoves.hidemovt=0";
		} else {
  		   $SQL = "SELECT SUM(CASE WHEN prd=" . $CurrentPeriod . " THEN -qty ELSE 0 END) AS prd0,
		   		SUM(CASE WHEN prd=" . $Period_1 . " THEN -qty ELSE 0 END) AS prd1,
				SUM(CASE WHEN prd=" . $Period_2 . " THEN -qty ELSE 0 END) AS prd2,
				SUM(CASE WHEN prd=" . $Period_3 . " THEN -qty ELSE 0 END) AS prd3,
				SUM(CASE WHEN prd=" . $Period_4 . " THEN -qty ELSE 0 END) AS prd4
			FROM stockmoves
			WHERE stockid='" . $InventoryPlan['stockid'] . "'
			AND stockmoves.loccode ='" . $_POST['Location'] . "'
			AND (stockmoves.type=10 OR stockmoves.type=11)
			AND stockmoves.hidemovt=0";
		}

		$SalesResult=DB_query($SQL,$db,'','',FALSE,FALSE);

		if (DB_error_no($db) !=0) {
	 		 $title = _('Inventory Planning') . ' - ' . _('Problem Report') . '....';
	  		include('includes/header.inc');
	   		prnMsg( _('The sales quantities could not be retrieved by the SQL because') . ' - ' . DB_error_msg($db),'error');
	   		echo "<BR><A HREF='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</A>';
	   		if ($debug==1){
	      			echo "<BR>$SQL";
	   		}
	   		include('includes/footer.inc');
	   		exit;
		}

		$SalesRow = DB_fetch_array($SalesResult);

		if ($_POST['Location']=='All'){
			// bowikaxu realhost - may 2007 - rh_status
			$SQL = "SELECT SUM(salesorderdetails.quantity - salesorderdetails.qtyinvoiced) AS qtydemand
				FROM salesorderdetails,
					salesorders
				WHERE salesorderdetails.orderno=salesorders.orderno
				AND salesorderdetails.stkcode = '" . $InventoryPlan['stockid'] . "'
				AND salesorderdetails.completed = 0
				AND salesorders.rh_status = 0";
		} else {
			// bowikaxu realhost - may 2007 - rh_status
			$SQL = "SELECT SUM(salesorderdetails.quantity - salesorderdetails.qtyinvoiced) AS qtydemand
				FROM salesorderdetails,
					salesorders
				WHERE salesorderdetails.orderno=salesorders.orderno
				AND salesorders.fromstkloc ='" . $_POST['Location'] . "'
				AND salesorderdetails.stkcode = '" . $InventoryPlan['stockid'] . "'
				AND salesorderdetails.completed = 0
				AND salesorders.rh_status = 0";
		}

		$DemandResult = DB_query($SQL,$db,'','',FALSE,FALSE);

		if (DB_error_no($db) !=0) {
	 		 $title = _('Inventory Planning') . ' - ' . _('Problem Report') . '....';
	  		include('includes/header.inc');
	   		prnMsg( _('The sales order demand quantities could not be retrieved by the SQL because') . ' - ' . DB_error_msg($db),'error');
	   		echo "<BR><A HREF='" .$rootpath ."/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
	   		if ($debug==1){
	      			echo "<BR>$SQL";
	   		}
	   		include('includes/footer.inc');
	   		exit;
		}

//Also need to add in the demand as a component of an assembly items if this items has any assembly parents.

		if ($_POST['Location']=='All'){
			$SQL = "SELECT SUM((salesorderdetails.quantity-salesorderdetails.qtyinvoiced)*bom.quantity) AS dem
				FROM salesorderdetails,
					bom,
					stockmaster
				WHERE salesorderdetails.stkcode=bom.parent
				AND salesorderdetails.quantity-salesorderdetails.qtyinvoiced > 0
				AND bom.component='" . $InventoryPlan['stockid'] . "'
				AND stockmaster.stockid=bom.parent
				AND (stockmaster.mbflag='A' OR stockmaster.mbflag='E')
				AND salesorderdetails.completed=0";
		} else {
			$SQL = "SELECT SUM((salesorderdetails.quantity-salesorderdetails.qtyinvoiced)*bom.quantity) AS dem
				FROM salesorderdetails,
					salesorders,
					bom,
					stockmaster
				WHERE salesorderdetails.orderno=salesorders.orderno
				AND salesorderdetails.stkcode=bom.parent
				AND salesorderdetails.quantity-salesorderdetails.qtyinvoiced > 0
				AND bom.component='" . $InventoryPlan['stockid'] . "'
				AND stockmaster.stockid=bom.parent
				AND salesorders.fromstkloc ='" . $_POST['Location'] . "'
				AND (stockmaster.mbflag='A' OR stockmaster.mbflag = 'E')
				AND salesorderdetails.completed=0";
		}

		$BOMDemandResult = DB_query($SQL,$db,'','',false,false);

		if (DB_error_no($db) !=0) {
	 		$title = _('Inventory Planning') . ' - ' . _('Problem Report') . '....';
	  		include('includes/header.inc');
	   		prnMsg( _('The sales order demand quantities from parent assemblies could not be retrieved by the SQL because') . ' - ' . DB_error_msg($db),'error');
	   		echo "<BR><A HREF='" .$rootpath ."/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
	   		if ($debug==1){
	      			echo "<BR>$SQL";
	   		}
	   		include('includes/footer.inc');
	   		exit;
		}

		if ($_POST['Location']=='All'){
			$SQL = "SELECT SUM(purchorderdetails.quantityord - purchorderdetails.quantityrecd) as qtyonorder
				FROM purchorderdetails,
					purchorders
				WHERE purchorderdetails.orderno = purchorders.orderno
				AND purchorderdetails.itemcode = '" . $InventoryPlan['stockid'] . "'
				AND purchorderdetails.completed = 0";
		} else {
			$SQL = "SELECT SUM(purchorderdetails.quantityord - purchorderdetails.quantityrecd) AS qtyonorder
				FROM purchorderdetails,
					purchorders
				WHERE purchorderdetails.orderno = purchorders.orderno
				AND purchorderdetails.itemcode = '" . $InventoryPlan['stockid'] . "'
				AND purchorderdetails.completed = 0
				AND purchorders.intostocklocation=  '" . $_POST['Location'] . "'";
		}

		$DemandRow = DB_fetch_array($DemandResult);
		$BOMDemandRow = DB_fetch_array($BOMDemandResult);
		$TotalDemand = $DemandRow['qtydemand'] + $BOMDemandRow['dem'];

		$OnOrdResult = DB_query($SQL,$db,'','',false,false);
		if (DB_error_no($db) !=0) {
	 		 $title = _('Inventory Planning') . ' - ' . _('Problem Report') . '....';
	  		include('includes/header.inc');
	   		prnMsg( _('The purchase order quantities could not be retrieved by the SQL because') . ' - ' . DB_error_msg($db),'error');
	   		echo "<BR><A HREF='" .$rootpath ."/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
	   		if ($debug==1){
	      			echo "<BR>$SQL";
	   		}
	   		include('includes/footer.inc');
	   		exit;
		}

		$OnOrdRow = DB_fetch_array($OnOrdResult);

		$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos, 60, $FontSize, $InventoryPlan['stockid'], 'left');
		$LeftOvers = $pdf->addTextWrap(100, $YPos, 150,6,$InventoryPlan['description'],'left');
		
		$Ancho;
		$XPos= $XPosIni;


		$LeftOvers = $pdf->addTextWrap($XPos, $YPos, 40,$FontSize,number_format($SalesRow['prd4'],0),'right');$XPos+=$Ancho;
		$LeftOvers = $pdf->addTextWrap($XPos, $YPos, 40,$FontSize,number_format($SalesRow['prd3'],0),'right');$XPos+=$Ancho;
		$LeftOvers = $pdf->addTextWrap($XPos, $YPos, 40,$FontSize,number_format($SalesRow['prd2'],0),'right');$XPos+=$Ancho;
		$LeftOvers = $pdf->addTextWrap($XPos, $YPos, 40,$FontSize,number_format($SalesRow['prd1'],0),'right');$XPos+=$Ancho;
		$LeftOvers = $pdf->addTextWrap($XPos, $YPos, 40,$FontSize,number_format($SalesRow['prd0'],0),'right');$XPos+=$Ancho;

		$MaxMthSales = Max($SalesRow['prd1'], $SalesRow['prd2'], $SalesRow['prd3'], $SalesRow['prd4']);
		$IdealStockHolding = $MaxMthSales * $_POST['NumberMonthsHolding'];
		$LeftOvers = $pdf->addTextWrap($XPos, $YPos, 40,$FontSize,number_format($IdealStockHolding,0),'right');$XPos+=$Ancho*2;
		$LeftOvers = $pdf->addTextWrap($XPos, $YPos, 40,$FontSize,number_format($InventoryPlan['qoh'],0),'right');$XPos+=$Ancho;
		$LeftOvers = $pdf->addTextWrap($XPos, $YPos, 40,$FontSize,number_format($TotalDemand,0),'right');$XPos+=$Ancho;

		$LeftOvers = $pdf->addTextWrap($XPos, $YPos, 40,$FontSize,number_format($OnOrdRow['qtyonorder'],0),'right');$XPos+=$Ancho;

		$SuggestedTopUpOrder = $IdealStockHolding - $InventoryPlan['qoh'] + $TotalDemand - $OnOrdRow['qtyonorder'];
		if ($SuggestedTopUpOrder <=0){
			$LeftOvers = $pdf->addTextWrap($XPos, $YPos, 40,$FontSize,_('Nil'),'right');$XPos+=$Ancho;

		} else {

			$LeftOvers = $pdf->addTextWrap($XPos, $YPos, 40,$FontSize,number_format($SuggestedTopUpOrder,0),'right');$XPos+=$Ancho;
		}



		if ($YPos < $Bottom_Margin + $line_height){
		   $PageNumber++;
		   include('includes/PDFInventoryPlanPageHeader.inc');
		}

	} /*end inventory valn while loop */

	$YPos -= (2*$line_height);

	$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);
	if(isset($_REQUEST['PrintExcel'])) 
		$pdfcode = $pdf->output('X');
	else 
		$pdfcode = $pdf->output();
	
	$len = strlen($pdfcode);

	if ($len<=20){
		$title = _('Print Inventory Planning Report Empty');
		include('includes/header.inc');
		prnMsg( _('There were no items in the range and location specified'),'error');
		echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
		include('includes/footer.inc');
		exit;
	} else {
		if(isset($_REQUEST['PrintExcel']))
      		header('Content-type: application/xls'); 
      	else
      		header('Content-type: application/pdf');
		header('Content-Length: ' . $len);
		
		if(isset($_REQUEST['PrintExcel']))
			header('Content-Disposition: inline; filename=InventoryPlanning.csv');
		else
			header('Content-Disposition: inline; filename=InventoryPlanning.pdf');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');

		echo $pdfcode;

	}

} else { /*The option to print PDF was not hit */

	$title=_('Inventory Planning Reporting');
	include('includes/header.inc');
	

	if (strlen($_POST['FromCriteria'])<1 || strlen($_POST['ToCriteria'])<1) {

	/*if $FromCriteria is not set then show a form to allow input	*/

		echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID . "' METHOD='POST'><CENTER><TABLE>";

		echo '<TR><TD>' . _('From Inventory Category Code') . ':</FONT></TD><TD><SELECT name=FromCriteria>';

		$sql='SELECT categoryid, categorydescription FROM stockcategory ORDER BY categoryid';
		$CatResult= DB_query($sql,$db);
		While ($myrow = DB_fetch_array($CatResult)){
			echo "<OPTION VALUE='" . $myrow['categoryid'] . "'>" . $myrow['categoryid'] . " - " . $myrow['categorydescription'];
		}
		echo "</SELECT></TD></TR>";

		echo '<TR><TD>' . _('To Inventory Category Code') . ':</TD><TD><SELECT name=ToCriteria>';

		/*Set the index for the categories result set back to 0 */
		DB_data_seek($CatResult,0);

		While ($myrow = DB_fetch_array($CatResult)){
			echo "<OPTION VALUE='" . $myrow['categoryid'] . "'>" . $myrow['categoryid'] . " - " . $myrow['categorydescription'];
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

		echo '<TR><TD>' . _('Maximum No Months Holding') . ":</TD><TD><SELECT name='NumberMonthsHolding'>";
		echo '<OPTION SELECTED Value=3>' . _('Three Months');
		echo '<OPTION Value=4>' . _('Four Months');
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

		echo "</TABLE><INPUT TYPE=Submit Name='PrintExcel' Value='" . _('Print Excel') . "'> <INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') . "'></CENTER>";
	}
	include('includes/footer.inc');

} /*end of else not PrintPDF */

?>
