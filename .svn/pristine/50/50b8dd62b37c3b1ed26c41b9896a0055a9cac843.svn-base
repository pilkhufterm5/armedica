<?php

$PageSecurity = 11;

include('includes/session.inc');

$title = _('All Stock Status By Location/Category');
if (isset($_GET['StockID'])){
	$StockID = trim(strtoupper($_GET['StockID']));
} elseif (isset($_POST['StockID'])){
	$StockID = trim(strtoupper($_POST['StockID']));
}

if(!isset($_REQUEST['marca']))
$_REQUEST['marca'] = array();

//Creamos el filtro para la marcar..
if (isset ( $_POST ['marca'] ) && !in_array('All',$_POST ['marca'] ) && count ( $_POST ['marca'] ) > 0) {
	
	$sqlMarca = "     AND  stockmaster.rh_marca IN ( " . implode ( ',', $_POST ['marca'] ) . ")";
	
}

/**Creamos accion para imprimir EXCEL */
if(isset($_POST['PrintExcel']))
{
	
	/*Libreria para exportar a excel */
	require 'includes/PHPExcel/Classes/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$i=4;
	/*Asignamos los encabezados al archivo*/
	
	

		$objPHPExcel->getActiveSheet()
		->setCellValue('A1', $_SESSION['CompanyRecord']['coyname']);
		
		$objPHPExcel->getActiveSheet()
		->setCellValue('A2', 'Del Almacen de:' . $_REQUEST['StockLocation'])
		->setCellValue('B2', 'Del Categoria: '   .$_REQUEST['StockCat'])
		->setCellValue('C2', 'Printed' . ': ' . Date('d M Y'));
		

	
	$objPHPExcel->getActiveSheet()
		->setCellValue('A3', _('StockID'))
		->setCellValue('B3', _('Description'))
		->setCellValue('C3', _('Units Of Measure'))
		->setCellValue('D3', _('Ultima fecha de compra'))
		->setCellValue('E3', _('Existencia Sistema'))
		->setCellValue('F3', _('Existencia fisica'))
		->setCellValue('G3', _('Diferencia'));
	
		
	
	if($_POST['obsolete']=='on'){
		$obsolete = "";
	}else {
		$obsolete = " AND stockmaster.discontinued = 0 ";
	}

	if ($_POST['StockCat']=='All') {
		$sql = "SELECT locstock.stockid,
				stockmaster.description,
				locstock.loccode,
				stockmaster.units,
				locations.locationname,
				locstock.quantity,
				locstock.reorderlevel,
				stockmaster.decimalplaces,
				stockmaster.serialised,
				stockmaster.controlled
			FROM locstock, 
				stockmaster, 
				locations
			WHERE locstock.stockid=stockmaster.stockid
			AND locstock.loccode = '$_POST[StockLocation]'
			AND locstock.loccode=locations.loccode
			AND locstock.quantity > 0
			$sqlMarca
			AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
			".$obsolete."
			ORDER BY locstock.stockid";
	} else {
		$sql = "SELECT locstock.stockid,
				stockmaster.description,
				locstock.loccode,
				stockmaster.units,
				locations.locationname,
				locstock.quantity,
				locstock.reorderlevel,
				stockmaster.decimalplaces,
				stockmaster.serialised,
				stockmaster.controlled
			FROM locstock, 
				stockmaster, 
				locations
			WHERE locstock.stockid=stockmaster.stockid
			AND locstock.loccode = '$_POST[StockLocation]'
			AND locstock.loccode=locations.loccode
			AND locstock.quantity > 0
			AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
			AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
			$sqlMarca
			".$obsolete."
			ORDER BY locstock.stockid";
	}

	$ErrMsg =  _('The stock held at each location cannot be retrieved because');
	$DbgMsg = _('The SQL that failed was');
	$LocStockResult = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	
while ($myrow=DB_fetch_array($LocStockResult)) {

		
		$StockID = $myrow['stockid'];

		// bowikaxu realhost - may 2007 - rh_status
		$sql = "SELECT Sum(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS dem
                   	FROM salesorderdetails,
                        	salesorders
                   	WHERE salesorders.orderno = salesorderdetails.orderno
			AND salesorders.fromstkloc='" . $myrow['loccode'] . "'
			AND salesorderdetails.completed=0
			AND salesorders.rh_status = 0
			AND salesorderdetails.stkcode='" . $StockID . "'";

		$ErrMsg = _('The demand for this product from') . ' ' . $myrow['loccode'] . ' ' . _('cannot be retrieved because');
		$DemandResult = DB_query($sql,$db,$ErrMsg);

		if (DB_num_rows($DemandResult)==1){
			$DemandRow = DB_fetch_row($DemandResult);
			$DemandQty =  $DemandRow[0];
		} else {
			$DemandQty =0;
		}

		//Also need to add in the demand as a component of an assembly items if this items has any assembly parents.
		// bowikaxu realhost - may 2007 - rh_status
		$sql = "SELECT Sum((salesorderdetails.quantity-salesorderdetails.qtyinvoiced)*bom.quantity) AS dem
                   	FROM salesorderdetails,
                        	salesorders,
                        	bom,
                        	stockmaster
                   	WHERE salesorderdetails.stkcode=bom.parent
			AND salesorders.orderno = salesorderdetails.orderno
			AND salesorders.fromstkloc='" . $myrow['loccode'] . "'
			AND salesorders.rh_status = 0
			AND salesorderdetails.quantity-salesorderdetails.qtyinvoiced > 0
			AND bom.component='" . $StockID . "'
			AND stockmaster.stockid=bom.parent
			AND (stockmaster.mbflag='A' OR stockmaster.mbflag='E')";

		$ErrMsg = _('The demand for this product from') . ' ' . $myrow['loccode'] . ' ' . _('cannot be retrieved because');
		$DemandResult = DB_query($sql,$db, $ErrMsg);

		if (DB_num_rows($DemandResult)==1){
			$DemandRow = DB_fetch_row($DemandResult);
			$DemandQty += $DemandRow[0];
		}

		$sql = "SELECT SUM(purchorderdetails.quantityord - purchorderdetails.quantityrecd) AS qoo
                   	FROM purchorderdetails
                   	INNER JOIN purchorders
                   		ON purchorderdetails.orderno=purchorders.orderno
                   	WHERE purchorders.intostocklocation='" . $myrow['loccode'] . "'
			AND purchorderdetails.itemcode='" . $StockID . "'";

		$ErrMsg = _('The quantity on order for this product to be received into') . ' ' . $myrow['loccode'] . ' ' . _('cannot be retrieved because');
		$QOOResult = DB_query($sql,$db,$ErrMsg);

		if (DB_num_rows($QOOResult)==1){
			$QOORow = DB_fetch_row($QOOResult);
			$QOO =  $QOORow[0];
		} else {
			$QOOQty = 0;
		}

		//fcarrizalest
		//buscamos la ultima fecha de compra
		$sqlUltimaC = "SELECT stockid, trandate FROM `stockmoves` WHERE  type = 25 AND `stockid` LIKE '{$myrow['stockid']}' order by trandate DESC limit 1";
		$UltimaCResult = DB_query($sqlUltimaC,$db,$ErrMsg);
		$ultimaC = DB_fetch_row($UltimaCResult);
		$ultima = (isset($ultimaC[1]))? ConvertSQLDate($ultimaC[1]): ' ';
		
		
		
		
	$objPHPExcel->getActiveSheet()
	->setCellValue('A'.$i, $myrow['stockid'])
	->setCellValue('B'.$i, $myrow['description'])
	->setCellValue('C'.$i, $myrow['units'])
	->setCellValue('D'.$i, $ultima)
	->setCellValue('E'.$i, number_format($myrow['quantity'],2))
	->setCellValue('F'.$i, '____________  Diferencia _________ ');
	
	
	$i++;
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
/**Creamos accion para imprimir pdf fcarrizalest */
if(isset($_REQUEST['pdf']))
{
	include('includes/PDFStarter.php');
	
	$FontSize=10;
	$pdf->addinfo('Title',_('All Stock Status By Location/Category'));
	$pdf->addinfo('Subject', ' ');

	$PageNumber=1;
	$line_height=12;

	require 'includes/rh_PDFStockLocStatusHeader.inc';
	
	
	if($_POST['obsolete']=='on'){
		$obsolete = "";
	}else {
		$obsolete = " AND stockmaster.discontinued = 0 ";
	}

	if ($_POST['StockCat']=='All') {
		$sql = "SELECT locstock.stockid,
				stockmaster.description,
				locstock.loccode,
				stockmaster.units,
				locations.locationname,
				locstock.quantity,
				locstock.reorderlevel,
				stockmaster.decimalplaces,
				stockmaster.serialised,
				stockmaster.controlled
			FROM locstock, 
				stockmaster, 
				locations
			WHERE locstock.stockid=stockmaster.stockid
			AND locstock.loccode = '$_POST[StockLocation]'
			AND locstock.loccode=locations.loccode
			AND locstock.quantity > 0
			$sqlMarca
			AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
			".$obsolete."
			ORDER BY locstock.stockid";
	} else {
		$sql = "SELECT locstock.stockid,
				stockmaster.description,
				locstock.loccode,
				stockmaster.units,
				locations.locationname,
				locstock.quantity,
				locstock.reorderlevel,
				stockmaster.decimalplaces,
				stockmaster.serialised,
				stockmaster.controlled
			FROM locstock, 
				stockmaster, 
				locations
			WHERE locstock.stockid=stockmaster.stockid
			AND locstock.loccode = '$_POST[StockLocation]'
			AND locstock.loccode=locations.loccode
			AND locstock.quantity > 0
			AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
			AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
			$sqlMarca
			".$obsolete."
			ORDER BY locstock.stockid";
	}

	$ErrMsg =  _('The stock held at each location cannot be retrieved because');
	$DbgMsg = _('The SQL that failed was');
	$LocStockResult = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	
while ($myrow=DB_fetch_array($LocStockResult)) {

		
		$StockID = $myrow['stockid'];

		// bowikaxu realhost - may 2007 - rh_status
		$sql = "SELECT Sum(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS dem
                   	FROM salesorderdetails,
                        	salesorders
                   	WHERE salesorders.orderno = salesorderdetails.orderno
			AND salesorders.fromstkloc='" . $myrow['loccode'] . "'
			AND salesorderdetails.completed=0
			AND salesorders.rh_status = 0
			AND salesorderdetails.stkcode='" . $StockID . "'";

		$ErrMsg = _('The demand for this product from') . ' ' . $myrow['loccode'] . ' ' . _('cannot be retrieved because');
		$DemandResult = DB_query($sql,$db,$ErrMsg);

		if (DB_num_rows($DemandResult)==1){
			$DemandRow = DB_fetch_row($DemandResult);
			$DemandQty =  $DemandRow[0];
		} else {
			$DemandQty =0;
		}

		//Also need to add in the demand as a component of an assembly items if this items has any assembly parents.
		// bowikaxu realhost - may 2007 - rh_status
		$sql = "SELECT Sum((salesorderdetails.quantity-salesorderdetails.qtyinvoiced)*bom.quantity) AS dem
                   	FROM salesorderdetails,
                        	salesorders,
                        	bom,
                        	stockmaster
                   	WHERE salesorderdetails.stkcode=bom.parent
			AND salesorders.orderno = salesorderdetails.orderno
			AND salesorders.fromstkloc='" . $myrow['loccode'] . "'
			AND salesorders.rh_status = 0
			AND salesorderdetails.quantity-salesorderdetails.qtyinvoiced > 0
			AND bom.component='" . $StockID . "'
			AND stockmaster.stockid=bom.parent
			AND (stockmaster.mbflag='A' OR stockmaster.mbflag='E')";

		$ErrMsg = _('The demand for this product from') . ' ' . $myrow['loccode'] . ' ' . _('cannot be retrieved because');
		$DemandResult = DB_query($sql,$db, $ErrMsg);

		if (DB_num_rows($DemandResult)==1){
			$DemandRow = DB_fetch_row($DemandResult);
			$DemandQty += $DemandRow[0];
		}

		$sql = "SELECT SUM(purchorderdetails.quantityord - purchorderdetails.quantityrecd) AS qoo
                   	FROM purchorderdetails
                   	INNER JOIN purchorders
                   		ON purchorderdetails.orderno=purchorders.orderno
                   	WHERE purchorders.intostocklocation='" . $myrow['loccode'] . "'
			AND purchorderdetails.itemcode='" . $StockID . "'";

		$ErrMsg = _('The quantity on order for this product to be received into') . ' ' . $myrow['loccode'] . ' ' . _('cannot be retrieved because');
		$QOOResult = DB_query($sql,$db,$ErrMsg);

		if (DB_num_rows($QOOResult)==1){
			$QOORow = DB_fetch_row($QOOResult);
			$QOO =  $QOORow[0];
		} else {
			$QOOQty = 0;
		}

		//fcarrizalest
		//buscamos la ultima fecha de compra
		$sqlUltimaC = "SELECT stockid, trandate FROM `stockmoves` WHERE  type = 25 AND `stockid` LIKE '{$myrow['stockid']}' order by trandate DESC limit 1";
		$UltimaCResult = DB_query($sqlUltimaC,$db,$ErrMsg);
		$ultimaC = DB_fetch_row($UltimaCResult);
		$ultima = (isset($ultimaC[1]))? ConvertSQLDate($ultimaC[1]): ' ';
		
		$YPos -=($line_height);
		$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,60,$FontSize,$myrow['stockid'], 'left');
		$LeftOvers = $pdf->addTextWrap($Xpos+70,$YPos,100,$FontSize,$myrow['description'], 'left');
		$LeftOvers = $pdf->addTextWrap($Xpos+110,$YPos,100,$FontSize,$myrow['units'] , 'right');
		$LeftOvers = $pdf->addTextWrap($Xpos+255,$YPos,150,$FontSize,$ultima , 'left');
		$LeftOvers = $pdf->addTextWrap($Xpos+290,$YPos,100,$FontSize,number_format($myrow['quantity'],2), 'right');
		$LeftOvers = $pdf->addTextWrap($Xpos+423,$YPos,300,$FontSize,'____________  Diferencia _________ ', 'left');
		
		
			if ($YPos < $Bottom_Margin + $line_height){
				require 'includes/rh_PDFStockLocStatusHeader.inc';
			}

		
	//end of page full new headings if
	}
	
		$buf = $pdf->output();
		$len = strlen($buf);
		header('Content-type: application/pdf');
		header('Content-Length: ' . $len);
		header('Content-Disposition: inline; filename=StockLocStatus.pdf');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	
		$pdf->Stream();
	
	exit;
}



include('includes/header.inc');



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




echo '<HR><FORM ACTION="' . $_SERVER['PHP_SELF'] . '?'. SID . '" METHOD=POST>';

$sql = "SELECT loccode,
		locationname
	FROM locations";

$resultStkLocs = DB_query($sql,$db);

echo '<TABLE><TR><TD>';

echo '<TABLE><TR><TD>' . _('From Stock Location') . ':</TD><TD><SELECT name="StockLocation"> ';
while ($myrow=DB_fetch_array($resultStkLocs)){
	if (isset($_POST['StockLocation']) AND $_POST['StockLocation']!='All'){
		if ($myrow['loccode'] == $_POST['StockLocation']){
		     echo '<OPTION SELECTED Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		} else {
		     echo '<OPTION Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		}
	} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
		 echo '<OPTION SELECTED Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		 $_POST['StockLocation']=$myrow['loccode'];
	} else {
		 echo '<OPTION Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
	}
}
echo '</SELECT></TD></TR>';

echo '<tr>
		<td> '._('Marca').' </td>
		<td>  '.$SelectM.'</td>
</tr>';

$SQL='SELECT categoryid, categorydescription FROM stockcategory ORDER BY categorydescription';
$result1 = DB_query($SQL,$db);
if (DB_num_rows($result1)==0){
	echo '</TABLE></TD></TR>
		</TABLE>
		<P>';
	prnMsg(_('There are no stock categories currently defined please use the link below to set them up'),'warn');
	echo '<BR><A HREF="' . $rootpath . '/StockCategories.php?' . SID .'">' . _('Define Stock Categories') . '</A>';
	include ('includes/footer.inc');
	exit;
}

echo '<TR><TD>' . _('In Stock Category') . ':</TD><TD><SELECT NAME="StockCat">';
if (!isset($_POST['StockCat'])){
	$_POST['StockCat']='All';
}
if ($_POST['StockCat']=='All'){
	echo '<OPTION SELECTED VALUE="All">' . _('All');
} else {
	echo '<OPTION VALUE="All">' . _('All');
}
while ($myrow1 = DB_fetch_array($result1)) {
	if ($myrow1['categoryid']==$_POST['StockCat']){
		echo '<OPTION SELECTED VALUE="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'];
	} else {
		echo '<OPTION VALUE="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'];
	}
}

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

echo '</TABLE>';



echo '</TD><TD VALIGN=CENTER>';

echo '</TD></TR><tr><td><input type="submit" name="PrintExcel" value="'._('Ver Excel').'"/> <input type="submit" name="pdf" value="'._('Ver pdf').'"/>  <INPUT TYPE=SUBMIT NAME="ShowStatus" VALUE="' . _('Show Stock Status') . '"></td> </tr></TABLE>';
echo '<HR>';


if (isset($_POST['ShowStatus'])){

// bowikaxu realhost - 18 july 2008 - show obsoletes?
	if($_POST['obsolete']=='on'){
		$obsolete = "";
	}else {
		$obsolete = " AND stockmaster.discontinued = 0 ";
	}

	if ($_POST['StockCat']=='All') {
		$sql = "SELECT locstock.stockid,
				stockmaster.description,
				locstock.loccode,
				stockmaster.units,
				locations.locationname,
				locstock.quantity,
				locstock.reorderlevel,
				stockmaster.decimalplaces,
				stockmaster.serialised,
				stockmaster.controlled
			FROM locstock, 
				stockmaster, 
				locations
			WHERE locstock.stockid=stockmaster.stockid
			AND locstock.loccode = '$_POST[StockLocation]'
			AND locstock.loccode=locations.loccode
			AND locstock.quantity > 0
			$sqlMarca
			AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
			".$obsolete."
			ORDER BY locstock.stockid";
	} else {
		$sql = "SELECT locstock.stockid,
				stockmaster.description,
				locstock.loccode,
				stockmaster.units,
				locations.locationname,
				locstock.quantity,
				locstock.reorderlevel,
				stockmaster.decimalplaces,
				stockmaster.serialised,
				stockmaster.controlled
			FROM locstock, 
				stockmaster, 
				locations
			WHERE locstock.stockid=stockmaster.stockid
			AND locstock.loccode = '$_POST[StockLocation]'
			AND locstock.loccode=locations.loccode
			AND locstock.quantity > 0
			AND (stockmaster.mbflag='B' OR stockmaster.mbflag='M')
			AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
			$sqlMarca
			".$obsolete."
			ORDER BY locstock.stockid";
	}

	$ErrMsg =  _('The stock held at each location cannot be retrieved because');
	$DbgMsg = _('The SQL that failed was');
	$LocStockResult = DB_query($sql, $db, $ErrMsg, $DbgMsg);

	echo '<TABLE CELLPADDING=5 CELLSPACING=4 BORDER=0>';

	
	$tableheader = '<TR>
			<TD class="tableheader">' . _('StockID') . '</TD>
			<TD class="tableheader">' . _('Description') . '</TD>
			<TD class="tableheader">' ._('Units Of Measure') . '</TD>
			<TD class="tableheader">' ._('Ultima fecha de compra') . '</TD>
			<TD class="tableheader">' . _('Quantity On Hand') . '</TD>
			<TD class="tableheader">' . _('Re-Order Level') . '</FONT></TD>
			<TD class="tableheader">' . _('Demand') . '</TD>
			<TD class="tableheader">' . _('Available') . '</TD>
			<TD class="tableheader">' . _('On Order') . '</TD>
			</TR>';
	echo $tableheader;
	$j = 1;
	$k=0; //row colour counter

	while ($myrow=DB_fetch_array($LocStockResult)) {

		if ($k==1){
			echo '<tr bgcolor="#CCCCCC">';
			$k=0;
		} else {
			echo '<tr bgcolor="#EEEEEE">';
			$k=1;
		}

		$StockID = $myrow['stockid'];

		// bowikaxu realhost - may 2007 - rh_status
		$sql = "SELECT Sum(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS dem
                   	FROM salesorderdetails,
                        	salesorders
                   	WHERE salesorders.orderno = salesorderdetails.orderno
			AND salesorders.fromstkloc='" . $myrow['loccode'] . "'
			AND salesorderdetails.completed=0
			AND salesorders.rh_status = 0
			AND salesorderdetails.stkcode='" . $StockID . "'";

		$ErrMsg = _('The demand for this product from') . ' ' . $myrow['loccode'] . ' ' . _('cannot be retrieved because');
		$DemandResult = DB_query($sql,$db,$ErrMsg);

		if (DB_num_rows($DemandResult)==1){
			$DemandRow = DB_fetch_row($DemandResult);
			$DemandQty =  $DemandRow[0];
		} else {
			$DemandQty =0;
		}

		//Also need to add in the demand as a component of an assembly items if this items has any assembly parents.
		// bowikaxu realhost - may 2007 - rh_status
		$sql = "SELECT Sum((salesorderdetails.quantity-salesorderdetails.qtyinvoiced)*bom.quantity) AS dem
                   	FROM salesorderdetails,
                        	salesorders,
                        	bom,
                        	stockmaster
                   	WHERE salesorderdetails.stkcode=bom.parent
			AND salesorders.orderno = salesorderdetails.orderno
			AND salesorders.fromstkloc='" . $myrow['loccode'] . "'
			AND salesorders.rh_status = 0
			AND salesorderdetails.quantity-salesorderdetails.qtyinvoiced > 0
			AND bom.component='" . $StockID . "'
			AND stockmaster.stockid=bom.parent
			AND (stockmaster.mbflag='A' OR stockmaster.mbflag='E')";

		$ErrMsg = _('The demand for this product from') . ' ' . $myrow['loccode'] . ' ' . _('cannot be retrieved because');
		$DemandResult = DB_query($sql,$db, $ErrMsg);

		if (DB_num_rows($DemandResult)==1){
			$DemandRow = DB_fetch_row($DemandResult);
			$DemandQty += $DemandRow[0];
		}

		$sql = "SELECT SUM(purchorderdetails.quantityord - purchorderdetails.quantityrecd) AS qoo
                   	FROM purchorderdetails
                   	INNER JOIN purchorders
                   		ON purchorderdetails.orderno=purchorders.orderno
                   	WHERE purchorders.intostocklocation='" . $myrow['loccode'] . "'
			AND purchorderdetails.itemcode='" . $StockID . "'";

		$ErrMsg = _('The quantity on order for this product to be received into') . ' ' . $myrow['loccode'] . ' ' . _('cannot be retrieved because');
		$QOOResult = DB_query($sql,$db,$ErrMsg);

		if (DB_num_rows($QOOResult)==1){
			$QOORow = DB_fetch_row($QOOResult);
			$QOO =  $QOORow[0];
		} else {
			$QOOQty = 0;
		}

		//fcarrizalest
		//buscamos la ultima fecha de compra
		$sqlUltimaC = "SELECT stockid, trandate FROM `stockmoves` WHERE  type = 25 AND `stockid` LIKE '{$myrow['stockid']}' order by trandate DESC limit 1";
		$UltimaCResult = DB_query($sqlUltimaC,$db,$ErrMsg);
		$ultimaC = DB_fetch_row($UltimaCResult);
		$ultima = (isset($ultimaC[1]))? ConvertSQLDate($ultimaC[1]): ' ';
		
		printf("<td><a target='_blank' href='StockStatus.php?StockID=%s'>%s</td>
			<td>%s</td>
			<td ALIGN=RIGHT> %s</td>
			<td ALIGN=RIGHT> %s </td>
			
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>",
			strtoupper($myrow['stockid']),
			strtoupper($myrow['stockid']),
			$myrow['description'],
			$myrow['units'],
			$ultima,
			number_format($myrow['quantity'],2),
			number_format($myrow['reorderlevel'],2),
			number_format($DemandQty,2),
			number_format($myrow['quantity'] - $DemandQty,2),
			number_format($QOO,2));

		if ($myrow['serialised'] ==1){ /*The line is a serialised item*/

			echo '<TD><A target="_blank" HREF="' . $rootpath . '/StockSerialItems.php?' . SID . '&Serialised=Yes&Location=' . $myrow['loccode'] . '&StockID=' . $StockID . '">' . _('Serial Numbers') . '</A></TD></TR>';
		} elseif ($myrow['controlled']==1){
			echo '<TD><A target="_blank" HREF="' . $rootpath . '/StockSerialItems.php?' . SID . '&Location=' . $myrow['loccode'] . '&StockID=' . $StockID . '">' . _('Batches') . '</A></TD></TR>';
		}

		$j++;
		If ($j == 12){
			$j=1;
			echo $tableheader;
		}
	//end of page full new headings if
	}
	//end of while loop

	echo '</TABLE><HR>';
	echo '</form>';
} /* Show status button hit */
include('includes/footer.inc');

?>
