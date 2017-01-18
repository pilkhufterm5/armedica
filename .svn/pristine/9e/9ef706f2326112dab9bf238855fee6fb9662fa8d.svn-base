<?php
/* $Revision: 14 $ */
/* $Revision: 14 $ */

$PageSecurity =1;
include('includes/session.inc');

If (isset($_GET['PrintExcel']) ){
	
$sql = "SELECT stockmaster.stockid,
               stockmaster.description,
               stockmaster.categoryid,
               locstock.loccode,
               locations.locationname,
               locstock.quantity
        FROM stockmaster INNER JOIN locstock ON stockmaster.stockid=locstock.stockid
        INNER JOIN locations ON locstock.loccode = locations.loccode
        WHERE locstock.quantity < 0
        ORDER BY locstock.loccode, stockmaster.categoryid, stockmaster.stockid";

$result = DB_query($sql,$db, $ErrMsg, $DbgMsg);

If (DB_num_rows($result)==0){
	include ('includes/header.inc');
	prnMsg(_('There are no negative stocks to list'),'error');
	include ('includes/footer.inc');
	exit;
}

$NegativesRow = DB_fetch_array($result);

/*Libreria para exportar a excel */
	require 'includes/PHPExcel/Classes/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$i=4;
	/*Asignamos los encabezados al archivo*/
	$objPHPExcel->getActiveSheet()->    setCellValue('A1', "Inventory Negatives Listing");
	
	$objPHPExcel->getActiveSheet()->    setCellValue('A2', $_SESSION['CompanyRecord']['coyname'])
									  ->setCellValue('B2', _('Printed'). ': ' . Date($_SESSION['DefaultDateFormat']))
									  ->setCellValue('C2', _('Negative Stocks Listing'));
									  
	$objPHPExcel->getActiveSheet()								  
									  ->setCellValue('A3', _('Location'))
									  ->setCellValue('B3', _('Item Description'))
									  ->setCellValue('C3', _('Quantity'));

do {
	$objPHPExcel->getActiveSheet()								  
									  ->setCellValue('A3', $NegativesRow['loccode'] . ' - ' . $NegativesRow['locationname'])
									  ->setCellValue('B3', $NegativesRow['stockid'] . ' - ' .$NegativesRow['description'])
									  ->setCellValue('C3', number_format($NegativesRow['quantity'],2));
	$i++;
	
} while ($NegativesRow = DB_fetch_array($result));

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


If (isset($_GET['PrintPDF']) ){
	
include('includes/PDFStarter.php');

$FontSize=10;
$pdf->addinfo('Title', _('Inventory Negatives Listing') );
$pdf->addinfo('Subject', _('Inventory Negatives Listing'));

$ErrMsg = _('An error occurred retrieving the negative quantities.');

$sql = "SELECT stockmaster.stockid,
               stockmaster.description,
               stockmaster.categoryid,
               locstock.loccode,
               locations.locationname,
               locstock.quantity
        FROM stockmaster INNER JOIN locstock ON stockmaster.stockid=locstock.stockid
        INNER JOIN locations ON locstock.loccode = locations.loccode
        WHERE locstock.quantity < 0
        ORDER BY locstock.loccode, stockmaster.categoryid, stockmaster.stockid";

$result = DB_query($sql,$db, $ErrMsg, $DbgMsg);

If (DB_num_rows($result)==0){
	include ('includes/header.inc');
	prnMsg(_('There are no negative stocks to list'),'error');
	include ('includes/footer.inc');
	exit;
}

$NegativesRow = DB_fetch_array($result);

$PageNumber=1;
include ('includes/PDFStockNegativesHeader.inc');
$line_height=15;
$FontSize=10;

do {

	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, $NegativesRow['loccode'] . ' - ' . $NegativesRow['locationname'], 'left');
	$LeftOvers = $pdf->addTextWrap(130,$YPos,250,$FontSize,$NegativesRow['stockid'] . ' - ' .$NegativesRow['description'], 'left');
	// bowikaxu august 07 - add decimal places
	$LeftOvers = $pdf->addTextWrap(400,$YPos,70,$FontSize,number_format($NegativesRow['quantity'],2), 'right');

	$pdf->line($Left_Margin, $YPos-2,$Page_Width-$Right_Margin, $YPos-2);

	$YPos -= $line_height;

	if ($YPos < $Bottom_Margin + $line_height) {
		$PageNumber++;
		include('includes/PDFStockNegativesHeader.inc');
	}

} while ($NegativesRow = DB_fetch_array($result));

$pdfcode = $pdf->output();
$len = strlen($pdfcode);


if ($len<=10){
	include('includes/header.inc');
	echo '<p>';
	prnMsg( _('There was no negative stocks to print out'), 'warn');
	echo '<BR><A HREF="' . $rootpath. '/index.php?' . SID . '">'. _('Back to the menu'). '</A>';
	include('includes/footer.inc');
	exit;
} else {
	header('Content-type: application/pdf');
	header('Content-Length: ' . $len);
	header('Content-Disposition: inline; filename=NegativeStocks.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	$pdf->Stream();
}

} else { /*The option to print PDF was not hit */

	
	include('includes/header.inc');

	echo '<FORM ACTION="' . $_SERVER['PHP_SELF'] . '" METHOD="GET"><CENTER>';
	echo '<p class="page_title_text">Inventory Comparison Report</p>';
	echo '<INPUT TYPE=Submit Name="PrintExcel" Value="' . _('Print Excel'). '"><INPUT TYPE=Submit Name="PrintPDF" Value="' . _('Print PDF'). '"></CENTER>';

	include('includes/footer.inc');

} /*end of else not PrintPDF */

?>
