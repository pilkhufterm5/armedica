<?php
/* $Revision: 1.5 $ */
/* pdf-php by R&OS code to set up a new sales order page */
/* Oct 2006 RealHost bowikaxu Apdtacion para impresion de Works Orders */

if ($PageNumber>1){
	$pdf->newPage();
}

$XPos = 55;
$YPos = 575;

$pdf->addText($XPos, $YPos,$FontSize, '#'.$PageNumber);
if($_GET['precio']!='')
$pdf->addText($XPos, $YPos-13,$FontSize, _('$').' '.number_format($myrow['costissued'],2));
$pdf->addText($XPos, $YPos-26,$FontSize, _('Creada').' : '.ConvertSQLDate($myrow['startdate']));

/*
if($myrow['closed']==1 && $myrow['released']==0 && $myrow['status']==2){	// terminada
	$pdf->addText($XPos, $YPos-39,$FontSize, _('Status') . ' : Terminada');

}else if($myrow['closed']==0 && $myrow['released']==1 && $myrow['status']==1){ // liberada
	$pdf->addText($XPos, $YPos-39,$FontSize, _('Status') . ' : Liberada');

}else if($myrow['released']==0 && $myrow['closed']==0 && $myrow['status']==0){ // no liberada
	$pdf->addText($XPos, $YPos-39,$FontSize, _('Status') . ' : No Liberada');

}else{	// incorrecto
	$pdf->addText($XPos, $YPos-39,$FontSize, _('Status') . ' : Incorrecto');
}
*/

if($myrow['closed']==1){	// terminada
	$pdf->addText($XPos, $YPos-39,$FontSize, _('Status') . ' : Terminada');

}else{ // liberada
	$pdf->addText($XPos, $YPos-39,$FontSize, _('Status') . ' : Abierta');

}

$YPos = 510;

//$pdf->addText($XPos, $YPos,$FontSize, $myrow['name']);
//$pdf->addText($XPos, $YPos-13,$FontSize, $myrow['address1']);
//$pdf->addText($XPos, $YPos-26,$FontSize, $myrow['address2']);
//$pdf->addText($XPos, $YPos-39,$FontSize, $myrow['address3'] . ' ' . $myrow['address4'] . ' ' . $myrow['address5']);


/*Print Dispatch Date - as current date
$XPos=50;
$YPos=98;
$pdf->addText($XPos, $YPos,$FontSize, Date($_SESSION['DefaultDateFormat']));

*/


/*Print the freight company to be used */
//$XPos=65;
//$YPos=48;
//$pdf->addText($XPos, $YPos,$FontSize, $myrow['shippername']);

$XPos=630;
$YPos=567;
$pdf->addText($XPos, $YPos,$FontSize, _('Work Order No') . ': ' . $CurrentWORef);
// cambiar por $CurrentWORef

//$pdf->addText($XPos, $YPos-14,$FontSize, _('Your Ref') . ': ' . $myrow['customerref']);


$XPos=687;
$YPos=539;
$pdf->addText($XPos, $YPos,$FontSize,  Date('Y-m-d'));

/*
$XPos=630;
$YPos=525;
$pdf->addText($XPos, $YPos,$FontSize, _('Cust') . ': ' . $myrow['debtorno']);
$pdf->addText($XPos, $YPos-14,$FontSize, _('Branch') . ': ' . $myrow['branchcode']);

$pdf->addText($XPos, $YPos-32,$FontSize, _('Page') . ': ' . $PageNumber);

$pdf->addText($XPos, $YPos-46,$FontSize,  _('From') . ': ' . $myrow['locationname']);
*/

/*Print the order number */
$XPos=510;
$YPos=96;
$pdf->addText($XPos, $YPos,$FontSize, '#'.$PageNumber);

/*
$XPos=609;
$YPos=96;
$LeftOvers = $pdf->addTextWrap($XPos,$YPos,170,$FontSize,$myrow['comments']);
*/

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


$YPos = 414;

?>
