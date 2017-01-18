<?php


/* $Revision: 343 $ */
/* Contributed by Chris Bice - gettext by Kitch*/
// bowikaxu - reporte valorizacion

$PageSecurity = 11;

include('includes/session.inc');
/**
 * fcarrizalest no aseguramos que este seteada marca como arreglo
 */
if(!isset($_REQUEST['marca']))
$_REQUEST['marca'] = array();

//Creamos el filtro para la marcar..
if (isset ( $_POST ['marca'] ) && !in_array('All',$_POST ['marca'] ) && count ( $_POST ['marca'] ) > 0) {
	
	$sqlMarca = "       AND  stockmaster.rh_marca IN ( " . implode ( ',', $_POST ['marca'] ) . ")";
	
}
/**
 *  impresion a excel
 */
if(isset($_REQUEST['excel']) && is_date($_POST['OnHandDate'])){
	$TotalQuantity = 0;
	if($_POST['obsolete']=='on'){
		$obsolete = "";
	}else {
		$obsolete = " AND stockmaster.discontinued = 0 ";
	}
    if($_POST['StockLocation']!='All'){
        $StockLocation="AND loccode = '" . $_POST['StockLocation'] ."'";
    }else{
        $StockLocation="";
    }

	if($_POST['StockCategory']!='All'){
		$sql = "SELECT stockid,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				description,
				decimalplaces
			FROM stockmaster
			WHERE categoryid = '" . $_POST['StockCategory'] . "'
			".$obsolete."
			$sqlMarca
			AND (mbflag='M' OR mbflag='B')";
	}else {
		$sql = "SELECT stockid,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				description,
				decimalplaces
			FROM stockmaster
			WHERE (mbflag='M' OR mbflag='B')
			$sqlMarca
			".$obsolete."";
	}

	$ErrMsg = _('The stock items in the category selected cannot be retrieved because');
	$DbgMsg = _('The SQL that failed was');

	$StockResult = DB_query($sql, $db, $ErrMsg, $DbgMsg);

	$SQLOnHandDate = FormatDateForSQL($_POST['OnHandDate']);
	
	/*Libreria para exportar a excel */
	require 'includes/PHPExcel/Classes/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$i=4;
	/*Asignamos los encabezados al archivo*/
	

		$objPHPExcel->getActiveSheet()
		->setCellValue('A1', html_entity_decode($_SESSION['CompanyRecord']['coyname'],ENT_COMPAT | ENT_HTML401,"UTF-8"));
		
		$objPHPExcel->getActiveSheet()
		->setCellValue('A2', html_entity_decode(_('Existencias por fecha:'  ),ENT_COMPAT | ENT_HTML401,"UTF-8") . $_REQUEST['OnHandDate'])
		->setCellValue('B2', html_entity_decode(_('Del Categoria:'  ) .$_REQUEST['StockCategory'],ENT_COMPAT | ENT_HTML401,"UTF-8"))
		->setCellValue('C2', html_entity_decode(_('Printed') . ': ' . Date('d M Y'),ENT_COMPAT | ENT_HTML401,"UTF-8") );

		
	$objPHPExcel->getActiveSheet()
		->setCellValue('A3', html_entity_decode(_('StockID'),ENT_COMPAT | ENT_HTML401,"UTF-8"))
		->setCellValue('B3', html_entity_decode(_('Description'),ENT_COMPAT | ENT_HTML401,"UTF-8"))
		->setCellValue('C3', html_entity_decode(_('Cantidad Disponible'),ENT_COMPAT | ENT_HTML401,"UTF-8"))
		->setCellValue('D3', html_entity_decode(_('Costo'),ENT_COMPAT | ENT_HTML401,"UTF-8"));
			if(isset($_POST['value'])){
		$objPHPExcel->getActiveSheet()->setCellValue('E3', html_entity_decode(_('Valor Total'),ENT_COMPAT | ENT_HTML401,"UTF-8"));
	}
	
	
		$TotalValue = 0;
		$TotalQuantity = 0;
	while ($myrows=DB_fetch_array($StockResult)) {


                //Jaime, Realhost, 20/Ene/10 10:29am
                /*
                                $sql = "SELECT stockid,
                                                newqoh
                                                FROM stockmoves
                                                WHERE stockmoves.trandate <= '". $SQLOnHandDate . "'
                                                AND stockid = '" . $myrows['stockid'] . "'
                                                AND loccode = '" . $_POST['StockLocation'] ."'
                                                ORDER BY stkmoveno DESC LIMIT 1";
                 */
                $sql = "SELECT stockid, sum(qty) as newqoh FROM stockmoves WHERE stockmoves.hidemovt in (0,2) and stockmoves.trandate <= '". $SQLOnHandDate . "' AND stockid = '" . $myrows['stockid'] . "' " . $StockLocation ." GROUP BY stockid";
                //Termina Jaime, Realhost, 20/Ene/10 10:29am
                $sql;

                
		$ErrMsg =  _('The stock held as at') . ' ' . $_POST['OnHandDate'] . ' ' . _('could not be retrieved because');

		$LocStockResult = DB_query($sql, $db, $ErrMsg);

		$NumRows = DB_num_rows($LocStockResult, $db);

		$j = 1;
		$k=0; //row colour counter

		while ($LocQtyRow=DB_fetch_array($LocStockResult)) {

			/** Calculamos el costo
			 * fcarrizalest
			 */
			
			$result = DB_query("SELECT description,
			units,
			lastcost,
			actualcost,
			materialcost,
			labourcost,
			overheadcost,
			mbflag,
			sum(quantity) as totalqoh
		FROM stockmaster INNER JOIN locstock
			ON stockmaster.stockid=locstock.stockid
		WHERE stockmaster.stockid='{$myrows['stockid']}'
		GROUP BY description,
			units,
			lastcost,
			actualcost,
			materialcost,
			labourcost,
			overheadcost,
			mbflag",
		$db,$ErrMsg,$DbgMsg);

$YPos -=($line_height);
$costo = DB_fetch_array($result);
	$costo =	number_format($costo['materialcost']+$costo['labourcost']+$costo['overheadcost'],2);
			if($NumRows == 0){
				if(isset($_POST['value'])){ //bowikaxu realhost calcular el valor
					$objPHPExcel->getActiveSheet()
					->setCellValue('A'.$i, strtoupper($myrows['stockid']))
					->setCellValue('B'.$i, $myrows['description'])
					->setCellValue('C'.$i, 0)
					->setCellValue('D'.$i, $costo)
					->setCellValue('E'.$i, 0);
					$i++;
					
					
				}else {
					$objPHPExcel->getActiveSheet()
					->setCellValue('A'.$i, strtoupper($myrows['stockid']))
					->setCellValue('B'.$i, $myrows['description'])
					->setCellValue('C'.$i, 0)
					->setCellValue('D'.$i, $costo)
					->setCellValue('E'.$i, ' ');
					$i++;
					
					
				}
			} else {
				if(isset($_POST['value'])){
					
					
					$objPHPExcel->getActiveSheet()
					->setCellValue('A'.$i, strtoupper($myrows['stockid']))
					->setCellValue('B'.$i, $myrows['description'])
					->setCellValue('C'.$i, number_format($LocQtyRow['newqoh'],2))
					->setCellValue('D'.$i, $costo)
					->setCellValue('E'.$i, number_format($LocQtyRow['newqoh']*$myrows['unitcost'],2,'.',''));
					$i++;
					
					
				}else {
					
					$objPHPExcel->getActiveSheet()
					->setCellValue('A'.$i, strtoupper($myrows['stockid']))
					->setCellValue('B'.$i, $myrows['description'])
					->setCellValue('C'.$i, number_format($LocQtyRow['newqoh'],2,'.',''))
					->setCellValue('D'.$i, $costo)
					->setCellValue('E'.$i, ' ');
					$i++;
					
					
	
				}
				$TotalQuantity += $LocQtyRow['newqoh'];
				$TotalValue += ($LocQtyRow['newqoh']*$myrows['unitcost']);
			}
			
		//end of page full new headings if
		}
		
	
	}//end of while loop
	$i++;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, html_entity_decode(_('Total Quantity'),ENT_COMPAT | ENT_HTML401,"UTF-8") . ": ");
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,  $TotalQuantity);
	$i++;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, html_entity_decode(_('Inventory Valuation Report'),ENT_COMPAT | ENT_HTML401,"UTF-8") . ": $");
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, number_format($TotalValue,2,'.',''));
	
	foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
        $objPHPExcel->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
    } 
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

/**
 * fcarrizales impresion a pdf
 */
if(isset($_REQUEST['pdf']) && is_date($_POST['OnHandDate'])){
	$TotalQuantity = 0;
	if($_POST['obsolete']=='on'){
		$obsolete = "";
	}else {
		$obsolete = " AND stockmaster.discontinued = 0 ";
	}
    if($_POST['StockLocation']!='All'){
        $StockLocation="AND loccode = '" . $_POST['StockLocation'] ."'";
    }else{
        $StockLocation="";
    }

	if($_POST['StockCategory']!='All'){
		$sql = "SELECT stockid,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				description,
				decimalplaces
			FROM stockmaster
			WHERE categoryid = '" . $_POST['StockCategory'] . "'
			".$obsolete."
			$sqlMarca
			AND (mbflag='M' OR mbflag='B')";
	}else {
		$sql = "SELECT stockid,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				description,
				decimalplaces
			FROM stockmaster
			WHERE (mbflag='M' OR mbflag='B')
			$sqlMarca
			".$obsolete."";
	}

	$ErrMsg = _('The stock items in the category selected cannot be retrieved because');
	$DbgMsg = _('The SQL that failed was');

	$StockResult = DB_query($sql, $db, $ErrMsg, $DbgMsg);

	$SQLOnHandDate = FormatDateForSQL($_POST['OnHandDate']);
	
	include('includes/PDFStarter.php');
	$FontSize=10;
	$pdf->addinfo('Title',_('Historico x almacen'));
	
	
	$PageNumber=1;
	$line_height=12;
	
	require 'includes/rh_PDFStockQuantityByDate.php';
	
	
		$TotalValue = 0;
		$TotalQuantity = 0;
	while ($myrows=DB_fetch_array($StockResult)) {


                //Jaime, Realhost, 20/Ene/10 10:29am
                /*
                                $sql = "SELECT stockid,
                                                newqoh
                                                FROM stockmoves
                                                WHERE stockmoves.trandate <= '". $SQLOnHandDate . "'
                                                AND stockid = '" . $myrows['stockid'] . "'
                                                AND loccode = '" . $_POST['StockLocation'] ."'
                                                ORDER BY stkmoveno DESC LIMIT 1";
                 */
                $sql = "SELECT stockid, sum(qty) as newqoh FROM stockmoves WHERE stockmoves.hidemovt in (0,2) and stockmoves.trandate <= '". $SQLOnHandDate . "' AND stockid = '" . $myrows['stockid'] . "' " . $StockLocation ." GROUP BY stockid";
                //Termina Jaime, Realhost, 20/Ene/10 10:29am
                $sql;

                
		$ErrMsg =  _('The stock held as at') . ' ' . $_POST['OnHandDate'] . ' ' . _('could not be retrieved because');

		$LocStockResult = DB_query($sql, $db, $ErrMsg);

		$NumRows = DB_num_rows($LocStockResult, $db);

		$j = 1;
		$k=0; //row colour counter

		while ($LocQtyRow=DB_fetch_array($LocStockResult)) {

			/** Calculamos el costo
			 * fcarrizalest
			 */
			
			$result = DB_query("SELECT description,
			units,
			lastcost,
			actualcost,
			materialcost,
			labourcost,
			overheadcost,
			mbflag,
			sum(quantity) as totalqoh
		FROM stockmaster INNER JOIN locstock
			ON stockmaster.stockid=locstock.stockid
		WHERE stockmaster.stockid='{$myrows['stockid']}'
		GROUP BY description,
			units,
			lastcost,
			actualcost,
			materialcost,
			labourcost,
			overheadcost,
			mbflag",
		$db,$ErrMsg,$DbgMsg);

$YPos -=($line_height);
$costo = DB_fetch_array($result);
	$costo =	number_format($costo['materialcost']+$costo['labourcost']+$costo['overheadcost'],2);
			if($NumRows == 0){
				if(isset($_POST['value'])){ //bowikaxu realhost calcular el valor
					
					$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,60,$FontSize,strtoupper($myrows['stockid']), 'left');
					$LeftOvers = $pdf->addTextWrap($Xpos+70,$YPos,100,$FontSize,$myrows['description'], 'left');
					$LeftOvers = $pdf->addTextWrap($Xpos+120,$YPos,100,$FontSize,0 , 'right');
					$LeftOvers = $pdf->addTextWrap($Xpos+150,$YPos,150,$FontSize,$costo , 'right');
					$LeftOvers = $pdf->addTextWrap($Xpos+330,$YPos,100,$FontSize,0, 'left');
					
				}else {
					
					$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,60,$FontSize,strtoupper($myrows['stockid']), 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+70,$YPos,100,$FontSize,$myrows['description'], 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+120,$YPos,100,$FontSize,0 , 'right');
	$LeftOvers = $pdf->addTextWrap($Xpos+150,$YPos,150,$FontSize,$costo , 'right');
	//$LeftOvers = $pdf->addTextWrap($Xpos+330,$YPos,100,$FontSize, ' ' , 'left');
					
				}
			} else {
				if(isset($_POST['value'])){
					
						$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,60,$FontSize,strtoupper($myrows['stockid']), 'left');
						$LeftOvers = $pdf->addTextWrap($Xpos+70,$YPos,100,$FontSize,$myrows['description'], 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+120,$YPos,100,$FontSize,number_format($LocQtyRow['newqoh'],2) , 'right');
	$LeftOvers = $pdf->addTextWrap($Xpos+150,$YPos,150,$FontSize,$costo , 'right');
	$LeftOvers = $pdf->addTextWrap($Xpos+330,$YPos,100,$FontSize,number_format($LocQtyRow['newqoh']*$myrows['unitcost'],2), 'left');
	
	
					
				}else {
					$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,60,$FontSize,strtoupper($myrows['stockid']), 'left');
						$LeftOvers = $pdf->addTextWrap($Xpos+70,$YPos,100,$FontSize,$myrows['description'], 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+120,$YPos,100,$FontSize,number_format($LocQtyRow['newqoh'],2) , 'right');
	$LeftOvers = $pdf->addTextWrap($Xpos+150,$YPos,150,$FontSize,$costo , 'right');
	//$LeftOvers = $pdf->addTextWrap($Xpos+330,$YPos,100,$FontSize,number_format($LocQtyRow['newqoh']*$myrows['unitcost'],2), 'left');
	
				}
				$TotalQuantity += $LocQtyRow['newqoh'];
				$TotalValue += ($LocQtyRow['newqoh']*$myrows['unitcost']);
			}
			
		//end of page full new headings if
		}
		
	if ($YPos < $Bottom_Margin + $line_height){
				require 'includes/rh_PDFStockQuantityByDate.php';
			}

	}//end of while loop
	$YPos -=($line_height);
	if ($YPos < $Bottom_Margin + $line_height){
				require 'includes/rh_PDFStockQuantityByDate.php';
			}
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,300,$FontSize, _('Total Quantity') . ": " . $TotalQuantity , 'left');
	$YPos -=($line_height);
	if ($YPos < $Bottom_Margin + $line_height){
				require 'includes/rh_PDFStockQuantityByDate.php';
			}
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,300,$FontSize, _('Inventory Valuation Report') . ": $" . number_format($TotalValue,2), 'left');
  
	
	$buf = $pdf->output();
	$len = strlen($buf);
	
		header('Content-type: application/pdf');
		header('Content-Length: ' . $len);
		header('Content-Disposition: inline; filename=StockQuantityByDate.pdf');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	
	$pdf->Stream();
	
	
	
	
	exit();
}


//fcarrizalest 
//Buscamos las marcas.
$sql = "SELECT * FROM rh_marca ";
$resultMarca = DB_query($sql,$db);
while ($myrow=DB_fetch_array($resultMarca)){
	
	if(in_array($myrow['id'] , $_REQUEST['marca']))
	$optionMarca .= '<option selected value="'.$myrow['id'].'" > '.$myrow['nombre'].' </option>';
	else 	
	$optionMarca .= '<option  value="'.$myrow['id'].'" > '.$myrow['nombre'].' </option>';
	
}

$SelectM = '<select name="marca[]" size=5 MULTIPLE > <option value="All" >  '._('All').'</option>'.$optionMarca . '</select>';

// bowikaxu realhost EXCEL
if($_POST['ShowMoves']=='Excel' AND is_date($_POST['OnHandDate'])){
	
// bowikaxu realhost - 18 july 2008 - show obsoletes?
	if($_POST['obsolete']=='on'){
		$obsolete = "";
	}else {
		$obsolete = " AND stockmaster.discontinued = 0 ";
	}

	if($_POST['StockCategory']!='All'){
		$sql = "SELECT stockid,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				description,
				decimalplaces
			FROM stockmaster
			WHERE categoryid = '" . $_POST['StockCategory'] . "'
			$sqlMarca
			AND (mbflag='M' OR mbflag='B')";
	}else {
		$sql = "SELECT stockid,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				description,
				decimalplaces
			FROM stockmaster
			WHERE (mbflag='M' OR mbflag='B')
			$sqlMarca
			";
	}

	$ErrMsg = _('The stock items in the category selected cannot be retrieved because');
	$DbgMsg = _('The SQL that failed was');

	$StockResult = DB_query($sql, $db, $ErrMsg, $DbgMsg);

	$SQLOnHandDate = FormatDateForSQL($_POST['OnHandDate']);
	
}

$title = _('Stock On Hand By Date');
include('includes/header.inc');

echo "<HR><FORM ACTION='" . $_SERVER['PHP_SELF'] . "?". SID . "' METHOD=POST>";

$sql = 'SELECT categoryid, categorydescription FROM stockcategory';
$resultStkLocs = DB_query($sql, $db);

echo '<CENTER><TABLE><TR>';
echo '<TD>' . _('For Stock Category') . ":</TD>
	<TD><SELECT NAME='StockCategory'> ";
echo "<OPTION SELECTED VALUE='All'>"._('All')."</OPTION>";
while ($myrow=DB_fetch_array($resultStkLocs)){
	if (isset($_POST['StockCategory']) AND $_POST['StockCategory']!='All'){
		if ($myrow['categoryid'] == $_POST['StockCategory']){
		     echo "<OPTION SELECTED VALUE='" . $myrow['categoryid'] . "'>" . $myrow['categorydescription'];
		} else {
		     echo "<OPTION VALUE='" . $myrow['categoryid'] . "'>" . $myrow['categorydescription'];
		}
	}else {
		 echo "<OPTION VALUE='" . $myrow['categoryid'] . "'>" . $myrow['categorydescription'];
	}
}
echo '</SELECT></TD></TR>';

echo '<tr>
			<td> '._('Marca').'</td>
			<td> '.$SelectM.'</td>
		</tr>';

$sql = 'SELECT loccode, locationname FROM locations';
$resultStkLocs = DB_query($sql, $db);


echo '<TR><TD>' . _('For Stock Location') . ":</TD>
	<TD><SELECT NAME='StockLocation'> ";
echo "<OPTION SELECTED VALUE='All'>"._('All')."</OPTION>";
while ($myrow=DB_fetch_array($resultStkLocs)){
	if (isset($_POST['StockLocation']) AND $_POST['StockLocation']!='All'){
		if ($myrow['loccode'] == $_POST['StockLocation']){
		     echo "<OPTION SELECTED VALUE='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
		} else {
		     echo "<OPTION VALUE='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
		}
	} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
		 echo "<OPTION VALUE='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
		 //$_POST['StockLocation']=$myrow['loccode'];
	} else {
		 echo "<OPTION VALUE='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
	}
}
echo '</SELECT></TD>';

if (!isset($_POST['OnHandDate'])){
	$_POST['OnHandDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,Date("m"),0,Date("y")));
}

echo '<TD>' . _("On-Hand On Date") . ":</TD>
	<TD><INPUT TYPE=TEXT NAME='OnHandDate' ".
	(isset($_REQUEST['OnHandDate'])?' checked=checked ':'').
	"SIZE=12 MAXLENGTH=12 VALUE='" . $_POST['OnHandDate'] . "'></TD></TR>";

// bowikaxu realhost - july 3 07 - view inventory cost
echo "<TR><TD>";
echo "<INPUT TYPE='CHECKBOX' ".
	(isset($_REQUEST['value'])?' checked=checked ':'').
	"NAME='value'> "._('Inventory Valuation')."</TD>";

// bowikaxu realhost - 21 july 2008 - obsolete filter
		if(isset($_REQUEST['obsolete'])){
			echo "<TD colspan=2>
			<INPUT TYPE=checkbox NAME='obsolete' checked>"._('Show').' '._('Obsolete')."
			</TD></TR>";
		}else {
			echo "<TD colspan=2>
			<INPUT TYPE=checkbox NAME='obsolete'>"._('Show').' '._('Obsolete')."
			</TD></TR>";
		}

// bowikaxu realhost january 2008 - excel report
// <INPUT TYPE=SUBMIT NAME='ShowStatus' VALUE='" . _('Excel') ."'>
echo "<TR>
<TD COLSPAN=6 ALIGN=CENTER>
<INPUT TYPE=SUBMIT NAME='ShowStatus' VALUE='" . _('Show Stock Status') ."'>
<INPUT TYPE=SUBMIT NAME='pdf' VALUE='" . _('Ver pdf') ."'>
<INPUT TYPE=SUBMIT NAME='excel' VALUE='" . _('Ver Excel') ."'>
</TD>
</TR></TABLE>";
echo '</FORM><HR>';

$TotalQuantity = 0;

if(isset($_POST['ShowStatus']) AND is_date($_POST['OnHandDate']))
{
	
// bowikaxu realhost - 18 july 2008 - show obsoletes?
	if(isset($_REQUEST['obsolete'])){
		$obsolete = "";
	}else {
		$obsolete = " AND stockmaster.discontinued = 0 ";
	}
    if($_POST['StockLocation']!='All'){
        $StockLocation="AND loccode = '" . $_POST['StockLocation'] ."'";
    }else{
        $StockLocation="";
    }

	if($_POST['StockCategory']!='All'){
		$sql = "SELECT stockid,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				description,
				decimalplaces
			FROM stockmaster
			WHERE categoryid = '" . $_POST['StockCategory'] . "'
			".$obsolete."
			$sqlMarca
			AND (mbflag='M' OR mbflag='B')";
	}else {
		$sql = "SELECT stockid,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				description,
				decimalplaces
			FROM stockmaster
			WHERE (mbflag='M' OR mbflag='B')
			$sqlMarca
			".$obsolete."";
	}

	$ErrMsg = _('The stock items in the category selected cannot be retrieved because');
	$DbgMsg = _('The SQL that failed was');

	$StockResult = DB_query($sql, $db, $ErrMsg, $DbgMsg);

	$SQLOnHandDate = FormatDateForSQL($_POST['OnHandDate']);

	echo '<TABLE CELLPADDING=5 CELLSPACING=4 BORDER=0>';
	if(isset($_POST['value'])){
		$tableheader = "<TR>
				<TD CLASS='tableheader'>" . _('Item Code') . "</TD>
				<TD CLASS='tableheader'>" . _('Description') . "</TD>
				<TD CLASS='tableheader'>" . _('Quantity On Hand') . "</TD>
				<TD CLASS='tableheader'>" . _('Cost') . "</TD>
				<TD CLASS='tableheader'>" . _('Total Value') . "</TD></TR>";
	}else {
		$tableheader = "<TR>
				<TD CLASS='tableheader'>" . _('Item Code') . "</TD>
				<TD CLASS='tableheader'>" . _('Description') . "</TD>
				<TD CLASS='tableheader'>" . _('Quantity On Hand') . "</TD><TD CLASS='tableheader'>" . _('Cost') . "</TD></TR>";
	}
	echo $tableheader;
	$TotalValue = 0;
	while ($myrows=DB_fetch_array($StockResult)) {


                //Jaime, Realhost, 20/Ene/10 10:29am
                /*
                                $sql = "SELECT stockid,
                                                newqoh
                                                FROM stockmoves
                                                WHERE stockmoves.trandate <= '". $SQLOnHandDate . "'
                                                AND stockid = '" . $myrows['stockid'] . "'
                                                AND loccode = '" . $_POST['StockLocation'] ."'
                                                ORDER BY stkmoveno DESC LIMIT 1";
                 */
                $sql = "SELECT stockid, sum(qty) as newqoh FROM stockmoves WHERE stockmoves.hidemovt in (0,2) and stockmoves.trandate <= '". $SQLOnHandDate . "' AND stockid = '" . $myrows['stockid'] . "' " . $StockLocation ." GROUP BY stockid";
                //Termina Jaime, Realhost, 20/Ene/10 10:29am
                $sql;

                
		$ErrMsg =  _('The stock held as at') . ' ' . $_POST['OnHandDate'] . ' ' . _('could not be retrieved because');

		$LocStockResult = DB_query($sql, $db, $ErrMsg);

		$NumRows = DB_num_rows($LocStockResult, $db);

		$j = 1;
		$k=0; //row colour counter

		while ($LocQtyRow=DB_fetch_array($LocStockResult)) {

			if ($k==1){
				echo "<TR BGCOLOR='#CCCCCC'>";
				$k=0;
			} else {
				echo "<TR BGCOLOR='#EEEEEE'>";
				$k=1;
			}
			/** Calculamos el costo
			 * fcarrizalest
			 */
			
			$result = DB_query("SELECT description,
			units,
			lastcost,
			actualcost,
			materialcost,
			labourcost,
			overheadcost,
			mbflag,
			sum(quantity) as totalqoh
		FROM stockmaster INNER JOIN locstock
			ON stockmaster.stockid=locstock.stockid
		WHERE stockmaster.stockid='{$myrows['stockid']}'
		GROUP BY description,
			units,
			lastcost,
			actualcost,
			materialcost,
			labourcost,
			overheadcost,
			mbflag",
		$db,$ErrMsg,$DbgMsg);


$costo = DB_fetch_array($result);
	$costo =	number_format($costo['materialcost']+$costo['labourcost']+$costo['overheadcost'],2);
			if($NumRows == 0){
				if(isset($_POST['value'])){ //bowikaxu realhost calcular el valor
					printf("<TD><A TARGET='_blank' HREF='StockStatus.php?%s'>%s</TD>
						<TD>%s</TD>
						<TD ALIGN=RIGHT>%s</TD> <td ALIGN=RIGHT>  ".$costo."</td>
						<TD ALIGN=RIGHT>%s</TD>",
						SID . '&StockID=' . strtoupper($myrows['stockid']),
						strtoupper($myrows['stockid']),
						$myrows['description'],
						0,0);
				}else {
					printf("<TD><A TARGET='_blank' HREF='StockStatus.php?%s'>%s</TD>
						<TD>%s</TD>
						<TD ALIGN=RIGHT>%s</TD> <td ALIGN=RIGHT>  $costo</td>",
						SID . '&StockID=' . strtoupper($myrows['stockid']),
						strtoupper($myrows['stockid']),
						$myrows['description'],
						0);
				}
			} else {
				if(isset($_POST['value'])){
					printf("<TD><A TARGET='_blank' HREF='StockStatus.php?%s'>%s</TD>
						<TD>%s</TD>
						<TD ALIGN=RIGHT>%s</TD><td ALIGN=RIGHT> $costo</td>
						<TD ALIGN=RIGHT>$%s</TD>",
						SID . '&StockID=' . strtoupper($myrows['stockid']),
						strtoupper($myrows['stockid']),
						$myrows['description'],
						number_format($LocQtyRow['newqoh'],2),
						number_format($LocQtyRow['newqoh']*$myrows['unitcost'],2));
				}else {
					printf("<TD><A TARGET='_blank' HREF='StockStatus.php?%s'>%s</TD>
						<TD>%s</TD>
						<TD ALIGN=RIGHT>%s</TD> <td ALIGN=RIGHT>  $costo</td>",
						SID . '&StockID=' . strtoupper($myrows['stockid']),
						strtoupper($myrows['stockid']),
						$myrows['description'],
						number_format($LocQtyRow['newqoh'],2));
				}
				$TotalQuantity += $LocQtyRow['newqoh'];
				$TotalValue += ($LocQtyRow['newqoh']*$myrows['unitcost']);
			}
			$j++;
			if ($j == 12){
				$j=1;
				echo $tableheader;
			}
		//end of page full new headings if
		}

	}//end of while loop
	echo '<TR><TD>' . _('Total Quantity') . ": " . $TotalQuantity . '</TD></TR>';
	echo '<TR><TD COLSPAN=3 ALIGN=CENTER><STRONG><H2>' . _('Inventory Valuation Report') . ": $" . number_format($TotalValue,2) . '</H2></STRONG></TD></TR></TABLE>';
}

include('includes/footer.inc');
?>
