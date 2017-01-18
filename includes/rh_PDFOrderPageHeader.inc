<?php
/* $Revision: 1.5 $ */
/* pdf-php by R&OS code to set up a new sales order page */
if ($PageNumber>1){
	$pdf->newPage();
}


$pdf->addTextWrap(200,750,300,18,utf8_decode($_SESSION['CompanyRecord']['coyname']));
$pdf->addJpegFromFile(dirname(__FILE__).'/../companies/' . $_SESSION['DatabaseName'] . '/logo.jpg',0,735,0,40);

$XPos = 40;
$YPos = 720;

$pdf->addText($XPos, $YPos,$FontSize, _('Customer Name').":");
$pdf->addText($XPos, $YPos-13,$FontSize, utf8_decode($myrow['name']));
$pdf->addText($XPos, $YPos-26,$FontSize, utf8_decode($myrow['name2']));
$pdf->addText($XPos, $YPos-39,$FontSize, utf8_decode($myrow['address1']));
$pdf->addText($XPos, $YPos-52,$FontSize, utf8_decode($myrow['address2']).' '._('Ph').': '.$myrow['rh_Tel']);
$pdf->addText($XPos, $YPos-65,$FontSize, utf8_decode($myrow['address3']) . ' ' . utf8_decode($myrow['address4']) . ' ' . utf8_decode($myrow['address5']));
$pdf->addText($XPos, $YPos-78,$FontSize, 'R.F.C. '.$myrow['taxref']);


$YPos = 620;

$pdf->addText($XPos, $YPos,$FontSize, _('Customer branch').":");
$pdf->addText($XPos, $YPos-13,$FontSize, utf8_decode($myrow['deliverto']).' '.$myrow['phoneno']);
$pdf->addText($XPos, $YPos-26,$FontSize, utf8_decode($myrow['deladd1']));
$pdf->addText($XPos, $YPos-39,$FontSize, utf8_decode($myrow['deladd2']));
$pdf->addText($XPos, $YPos-52,$FontSize, utf8_decode($myrow['deladd3']) . ' ' . utf8_decode($myrow['deladd4']) . ' ' . utf8_decode($myrow['deladd5']));


/*Print Dispatch Date - as current date
$XPos=50;
$YPos=98;
$pdf->addText($XPos, $YPos,$FontSize, Date($_SESSION['DefaultDateFormat']));

*/


/*Print the freight company to be used */
$XPos=20;
$YPos=40;
$pdf->addText($XPos, $YPos,$FontSize, utf8_decode($myrow['shippername']));

$XPos=450;
$YPos=730;
$pdf->addText($XPos, $YPos,15, _('Pedido') . ': ' . $_GET['TransNo']);
$pdf->addText($XPos, $YPos-14,$FontSize, _('Usuario') . ': ' . $user_['user_']);
$pdf->addText($XPos, $YPos-28,$FontSize, _('Referencia') . ': ' . $myrow['customerref']);
$pdf->addText($XPos, $YPos-42,$FontSize,  $myrow['orddate']);

$pdf->addText($XPos, $YPos-56,$FontSize, _('Codigo') . ': ' . $myrow['debtorno']);
$pdf->addText($XPos, $YPos-70,$FontSize, _('Sucursal') . ': ' . $myrow['branchcode']);

$pdf->addText($XPos, $YPos-84,$FontSize, _('Hoja') . ': ' . $PageNumber);

$pdf->addText($XPos, $YPos-98,$FontSize,  _('Desde') . ': ' . utf8_decode($myrow['locationname']));
if($myrow['rh_status']==1){
	$pdf->addText($XPos-110, $YPos-112,$FontSize,  _('This sales order has been cancelled as requested'));
}

//$XPos=450;
//$YPos=600;

$XPos=400;
$YPos=600;
$LeftOvers = $pdf->addTextWrap($XPos,$YPos,170,$FontSize,utf8_decode($myrow['comments']) );

if (strlen($LeftOvers)>1){
	$LeftOvers = $pdf->addTextWrap($XPos,$YPos-14,170,$FontSize,$LeftOvers);
	if (strlen($LeftOvers)>1){
		$LeftOvers = $pdf->addTextWrap($XPos,$YPos-28,170,$FontSize,$LeftOvers);
		if (strlen($LeftOvers)>1){
			$LeftOvers = $pdf->addTextWrap($XPos,$YPos-42,170,$FontSize,$LeftOvers);
			if (strlen($LeftOvers)>1){
				$LeftOvers = $pdf->addTextWrap($XPos,$YPos-56,170,$FontSize,$LeftOvers);
			}
		}
	}
}


$YPos = 530;
$pdf->line(5,535,680,535);

?>
