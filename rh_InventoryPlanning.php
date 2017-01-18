<?php

$PageSecurity = 2;
include('includes/session.inc');
/*
if(isset($_POST['show_html'])){
echo "<pre>";
var_dump($_POST);
echo "</pre>"; exit;
}*/
$html.='';
If ( (isset($_POST['PrintPDF']) || isset($_POST['show_html'])||isset($_REQUEST['Excel']))
	AND isset($_POST['FromCriteria'])
	AND strlen($_POST['FromCriteria'])>=1
	AND isset($_POST['ToCriteria'])
	AND strlen($_POST['ToCriteria'])>=1){

        include ('includes/class.pdf.php');
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
					[(int)($XPos)]=$Text;
					
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
							$Buffer.=implode('|',$fila)."\n";
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
	}else{
	/* A4_Landscape */

	$Page_Width=842;
	$Page_Height=595;
	$Top_Margin=20;
	$Bottom_Margin=20;
	$Left_Margin=25;
	$Right_Margin=22;

	$PageSize = array(0,0,$Page_Width,$Page_Height);
	$pdf = & new Cpdf($PageSize);
	}
	$PageNumber = 0;

	$pdf->selectFont('./fonts/Helvetica.afm');

/* Standard PDF file creation header stuff */

	$pdf->addinfo('Author','webERP ' . $Version);
	$pdf->addinfo('Creator','webERP http://www.weberp.org');
	$pdf->addinfo('Title',_('Inventory Planning Report') . ' ' . Date($_SESSION['DefaultDateFormat']));
	

	$line_height=12;

	$pdf->addinfo('Subject',_('Inventory Planning'));
	 $html.="
<style>

table.reporte  {
   width: 100%;
   border: 1px solid #999;
   text-align: left;
   border-collapse: collapse;
   margin: 0 0 1em 0;
   caption-side: top;
}
.reporte caption, .reporte td, .reporte th {
   padding: 0.3em;
}
.reporte th, .reporte td {
   border-bottom: 1px solid #999;
   
}
.reporte caption {
   font-weight: bold;
   font-style: italic;
}
</style>
<table class='reporte TablaInventoryPlaning'>";
	 $CurrentPeriod = GetPeriod(Date($_SESSION['DefaultDateFormat']),$db);
	$prd_actual = $CurrentPeriod;
	$_sumaPerido =  array();
	$contador_prd = 0;
	for($i=0;$i<$_POST['NumberMonthsHolding'];$i++){
		$prd_actual-=1;
		$nameprd = $i+1;
		$_sumaPerido[] = "SUM(CASE WHEN prd=" . $prd_actual . " THEN -qty ELSE 0 END) AS prd".$nameprd;
	}//
/*	echo "<pre>";
	var_dump($_sumaPerido);
	echo "</pre>";
	exit;*/
	$str_sumaprds = '';	
	$totalprod = count($_sumaPerido);
	if( $totalprod>0 ){
		$str_sumaprds = ",".implode(",",$_sumaPerido);
	}
	$cols = $totalprod + 8;	 

	//echo $html;
	//exit;

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

	include ('includes/rh_PDFInventoryPlanPageHeader.inc');

	$Category = '';

	/*$Period_1 = $CurrentPeriod -1;
	$Period_2 = $CurrentPeriod -2;
	$Period_3 = $CurrentPeriod -3;
	$Period_4 = $CurrentPeriod -4;*/
	//SUM(CASE WHEN prd=" . $Period_1 . " THEN -qty ELSE 0 END) AS prd1
	

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

			$html.="<tr><td colspan='".$cols."' style='background-color:#ccc'>".$InventoryPlan['categoryid'] . ' - ' . $InventoryPlan['categorydescription']."</td></tr>";
				
			$Category = $InventoryPlan['categoryid'];
			$FontSize=8;
		}

		$YPos -=$line_height;


		if ($_POST['Location']=='All'){
				/*,
		   		SUM(CASE WHEN prd=" . $Period_1 . " THEN -qty ELSE 0 END) AS prd1,
				SUM(CASE WHEN prd=" . $Period_2 . " THEN -qty ELSE 0 END) AS prd2,
				SUM(CASE WHEN prd=" . $Period_3 . " THEN -qty ELSE 0 END) AS prd3,
				SUM(CASE WHEN prd=" . $Period_4 . " THEN -qty ELSE 0 END) AS prd4*/
   		   $SQL = "SELECT SUM(CASE WHEN prd=" . $CurrentPeriod . " THEN -qty ELSE 0 END) AS prd0
			{$str_sumaprds}
			FROM stockmoves
			WHERE stockid='" . $InventoryPlan['stockid'] . "'
			AND (type=10 OR type=11)
			AND stockmoves.hidemovt=0"; 
		} else {
  		   $SQL = "SELECT SUM(CASE WHEN prd=" . $CurrentPeriod . " THEN -qty ELSE 0 END) AS prd0
			{$str_sumaprds}
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

		$InventoryPlan['stockid']=htmlentities($InventoryPlan['stockid']);
		$InventoryPlan['description']=htmlentities($InventoryPlan['description']);

		$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos, 60, $FontSize, $InventoryPlan['stockid'], 'left');
		$LeftOvers = $pdf->addTextWrap(100, $YPos, 150,6,$InventoryPlan['description'],'left');

		$LeftOvers = $pdf->addTextWrap(251, $YPos, 40,$FontSize,number_format($SalesRow['prd4'],0),'right');
		$LeftOvers = $pdf->addTextWrap(292, $YPos, 40,$FontSize,number_format($SalesRow['prd3'],0),'right');
		$LeftOvers = $pdf->addTextWrap(333, $YPos, 40,$FontSize,number_format($SalesRow['prd2'],0),'right');
		$LeftOvers = $pdf->addTextWrap(374, $YPos, 40,$FontSize,number_format($SalesRow['prd1'],0),'right');
		$LeftOvers = $pdf->addTextWrap(415, $YPos, 40,$FontSize,number_format($SalesRow['prd0'],0),'right');
		$html.="<tr>";
		$html.="<td>{$InventoryPlan['stockid']}</td>";
		$html.="<td>{$InventoryPlan['description']}</td>";
		

		//$MaxMthSales = Max($SalesRow['prd1'], $SalesRow['prd2'], $SalesRow['prd3'], $SalesRow['prd4']);
		$_productosSales = array();
		$_content = array();
		//for($i=0;$i<$_POST['NumberMonthsHolding'];$i++){
		for($i=$_POST['NumberMonthsHolding'];$i>=0;$i--){
			$nameprd = $i;
			
			$_productosSales[] = $SalesRow['prd'.$nameprd];
			$_content[] ="<td align='center'>{$SalesRow['prd'.$nameprd]}</td>";
		}

		$html.= implode("",$_content);
		
		
	
		$MaxMthSales = Max($_productosSales);

		$IdealStockHolding = $MaxMthSales * $_POST['NumberMonthsHolding'];

		$LeftOvers = $pdf->addTextWrap(456, $YPos, 40,$FontSize,number_format($IdealStockHolding,0),'right');
		$html.="<td align='center'>".number_format($IdealStockHolding,0)."</td>";
		$LeftOvers = $pdf->addTextWrap(597, $YPos, 40,$FontSize,number_format($InventoryPlan['qoh'],0),'right');
		$html.="<td align='center'>".number_format($InventoryPlan['qoh'],0)."</td>";
		$LeftOvers = $pdf->addTextWrap(638, $YPos, 40,$FontSize,number_format($TotalDemand,0),'right');
		$html.="<td align='center'>".number_format($TotalDemand,0)."</td>";

		$LeftOvers = $pdf->addTextWrap(679, $YPos, 40,$FontSize,number_format($OnOrdRow['qtyonorder'],0),'right');
		$html.="<td align='center'>".number_format($OnOrdRow['qtyonorder'],0)."</td>";

		$SuggestedTopUpOrder = $IdealStockHolding - $InventoryPlan['qoh'] + $TotalDemand - $OnOrdRow['qtyonorder'];
		if ($SuggestedTopUpOrder <=0){
			$LeftOvers = $pdf->addTextWrap(720, $YPos, 40,$FontSize,_('Nil'),'centre');
			$html.="<td align='center'>"._('Nil')."</td>";

		} else {

			$LeftOvers = $pdf->addTextWrap(720, $YPos, 40,$FontSize,number_format($SuggestedTopUpOrder,0),'right');
			$html.="<td align='center'>".number_format($SuggestedTopUpOrder,0)."</td>";
		}
		
		$html.="</tr>";
		

		if ($YPos < $Bottom_Margin + $line_height){
		   $PageNumber++;
		   include('includes/PDFInventoryPlanPageHeader.inc');
		}

	} /*end inventory valn while loop */
	$html.="</table>";		
	if(!isset($_POST['show_html'])){
	
	
		$YPos -= (2*$line_height);

		$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);

		if(isset($_REQUEST['Excel'])) 
			$pdfcode = $pdf->output('X');
		else 
			$pdfcode = $pdf->output();
		$pdfcode=$html;
		$len = strlen($pdfcode);

		if ($len<=20){
			$title = _('Print Inventory Planning Report Empty');
			include('includes/header.inc');
			prnMsg( _('There were no items in the range and location specified'),'error');
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
				header('Content-Disposition: inline; filename=InventoryPlanning.xls');
			else	
				header('Content-Disposition: inline; filename=InventoryPlanning.pdf');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			echo $pdfcode;
			//$pdf->Stream();
			
		}
	
	}

}/*The option to print PDF was not hit */
if( !isset($_POST['PrintPDF']) || (isset($_POST['show_html']) && $html!='') ){
	$title=_('Inventory Planning Reporting');
	include('includes/header.inc');
	

	if (strlen($_POST['FromCriteria'])<1 || strlen($_POST['ToCriteria'])<1 || $html!='') {

	/*if $FromCriteria is not set then show a form to allow input	*/

		echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID . "' METHOD='POST'><CENTER><TABLE>";

		echo '<TR><TD>' . _('From Inventory Category Code') . ':</FONT></TD><TD><SELECT name=FromCriteria>';

		$sql='SELECT categoryid, categorydescription FROM stockcategory ORDER BY categoryid';
		$CatResult= DB_query($sql,$db);
		While ($myrow = DB_fetch_array($CatResult)){
			echo "<OPTION VALUE='" . $myrow['categoryid'] . "'";
			if($myrow['categoryid']==$_REQUEST['FromCriteria']) echo ' selected=selected ';
			echo ">" . $myrow['categoryid'] . " - " . $myrow['categorydescription'];
		}
		echo "</SELECT></TD></TR>";

		echo '<TR><TD>' . _('To Inventory Category Code') . ':</TD><TD><SELECT name=ToCriteria>';

		/*Set the index for the categories result set back to 0 */
		DB_data_seek($CatResult,0);

		While ($myrow = DB_fetch_array($CatResult)){
			echo "<OPTION VALUE='" . $myrow['categoryid'] . "'";
			if($myrow['categoryid']==$_REQUEST['ToCriteria']) echo ' selected=selected ';
			echo ">" . $myrow['categoryid'] . " - " . $myrow['categorydescription'];
		}
		echo '</SELECT></TD></TR>';

		echo '<TR><TD>' . _('For Inventory in Location') . ":</TD><TD><SELECT name='Location'>";
		$sql = 'SELECT loccode, locationname FROM locations';
		$LocnResult=DB_query($sql,$db);

		echo "<OPTION Value='All'>" . _('All Locations');

		while ($myrow=DB_fetch_array($LocnResult)){
		          echo "<OPTION Value='" . $myrow['loccode'] . "'";
		          if($myrow['loccode']==$_REQUEST['Location']) echo ' selected=selected ';
		          echo ">" . $myrow['locationname'];
		      		}
		echo '</SELECT></TD></TR>';

		/*echo '<TR><TD>' . _('Maximum No Months Holding') . ":</TD><TD><SELECT name='NumberMonthsHolding'>";
		echo '<OPTION SELECTED Value=3>' . _('Three Months');
		echo '<OPTION Value=4>' . _('Four Months');
		echo '</SELECT></TD></TR>';*/
		echo '<TR><TD>' . _('Maximum No Months Holding') . ":</TD><TD><input name='NumberMonthsHolding' value=''>";
		/*echo '<OPTION SELECTED Value=3>' . _('Three Months');
		echo '<OPTION Value=4>' . _('Four Months');
		echo '</SELECT>';*/
		echo '</TD></TR>';

		
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

		echo "</TABLE><!--<INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') . "'>&nbsp;&nbsp;-->
		<INPUT TYPE=Submit Name='show_html' Value='" . _('Html') . "'>";?>
		<INPUT TYPE=Submit Name='Excel' Value='Excel'>
		<?php 
		echo "
		<br><br><br>
		<div style='padding:10px 10px 10px 10px;'>
		{$html}
		<div>
		</CENTER>";
	}
	include('includes/footer.inc');
}
 /*end of else not PrintPDF */

?>
