<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
/* bowikaxu - 19/02/2007 Impresion de Ticket */

$PageSecurity = 2;

include('includes/session.inc');
include('includes/class.pdf.php');
include('includes/SQL_CommonFunctions.inc');

If (!isset($_GET['Ticket']) || $_GET['Ticket']==""){
	
	$title = _('Impresion de Ticket');
	include('includes/header.inc');
	echo '<DIV ALIGN=CENTER><BR><BR><BR>';
	echo "<CENTER><H1>"._('ERROR: Imposible Imprimir Ticket')."</H1></CENTER>";
	include('includes/footer.inc');
	exit;
	
}else {
	$PageNumber = 1;

	$Page_Width=200; // x o y
	$Page_Height=100; // x o y
	$Top_Margin=10;
	$Bottom_Margin=10;
	$Left_Margin=5;
	$Right_Margin=5;

	$PageSize = array(0,0,$Page_Width,$Page_Height);
	$pdf = & new Cpdf($PageSize);
	$FontSize=10;
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->addinfo('Author','webERP - bowikaxu' . $Version);
	$pdf->addinfo('Creator','webERP http://www.weberp.org - R&OS PHP-PDF http://www.ros.co.nz');
	$pdf->addinfo('Title', _('Punto de Venta') );
	$pdf->addinfo('Subject', _('Ticket de Venta') . ' ' . $_SESSION['ProcessingOrder']);

	$line_height=10;
	include('includes/rh_PDFTicketHeader.inc');
	
	foreach ($_SESSION['Items']->LineItems as $OrderLine) {
		
		$LineTotal = $OrderLine->Quantity * $OrderLine->Price * (1 - $OrderLine->DiscountPercent);
		$pdf->addText($XPos, $YPos,$FontSize, $_SESSION['Items']->StockID.' - '.$_SESSION['Items']->Quantity.' - $'.$LineTotal);
		$YPos += $line_height;
		
	}
	
	$pdfcode = $pdf->output();
	$len = strlen($pdfcode);
	
	if ($len <= 5){
		
		echo "ERROR: Imposible Imprimir Tiket";
		
	} else {
		
		header('Content-type: application/pdf');
		header('Content-Length: ' . $len);
		header('Content-Disposition: inline; filename=Ticket.pdf');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');

	$pdf->Stream();
		
	}
}
	// fin impresion de ticket
	?>