<?php
/* $Revision: 1.7 $ */
if ($PageNumber>1){
	$pdf->newPage();
}

$YPos = $Page_Height - $Top_Margin - 50;

$pdf->addJpegFromFile('companies/' . $_SESSION['DatabaseName'] . '/logo.jpg',$Left_Margin,$YPos,0,50);

$FontSize=15;

$XPos = $Left_Margin;
$YPos -= 40;
$pdf->addText($Page_Width/2-30, $YPos,$FontSize, _('P&oacute;liza'));

if($myrow['type']==22){
	$pdf->addText($Page_Width/2-30, $YPos-12,10, _('Pago a Proveedores'));
}
if($myrow['type']==1){
	$pdf->addText($Page_Width/2-30, $YPos-12,10, _('Pago por Bancos'));
}

$FontSize=12;
$pdf->addText($XPos, $YPos-20,$FontSize, _('Printed').': '.date('d-m-Y'));

$XPos = $Page_Width-$Right_Margin-50;
$YPos -=20;
$pdf->addText($XPos, $YPos,$FontSize, _('Page') . ': ' . $PageNumber);
$YPos -=12;
$pdf->addText($Left_Margin, $YPos,$FontSize, _('User') . ': ' . $_SESSION['UserID']);

/*Now print out the company name and address */
$XPos = $Left_Margin;
$YPos -= $line_height;
$pdf->line($XPos, $YPos,$Page_Width-$Right_Margin, $YPos);
$YPos -= $line_height;
$YPos -= $line_height;
// DETALLES DEL CHEQUE
$pdf->addText($XPos, $YPos,$FontSize, _('Cheque').' '._('Number').': '.$myrow['rh_chequeno']);
$YPos -= $line_height;
$pdf->addText($XPos, $YPos,$FontSize, _('Trans.').' '._('Number').': '.$myrow['transno']);
$YPos -= $line_height;
$pdf->addText($XPos, $YPos,$FontSize, _('Amount').': '.number_format((-1*$myrow['amount']),2));
$YPos -= $line_height;
// con letra
//-1*$myrow['amount']
$tot = explode(".",(-1*$myrow['amount']));
$Letra = Numbers_Words::toWords($tot[0],"es");
if($tot[1]==0){
	$ConLetra = $Letra." pesos 00/100 M.N.";
}else if(strlen($tot[1])>=2){
	$ConLetra = $Letra.' pesos '.$tot[1]."/100 M.N.";
}else {
	$ConLetra = $Letra.' pesos '.$tot[1]."0/100 M.N.";
}
$pdf->addText($XPos, $YPos,$FontSize, '('.$ConLetra.')');
$YPos -= $line_height;
// con letra
if($tipo==22){
	$pdf->addText($XPos, $YPos,$FontSize, _('Supplier').': '.$myrow['suppname'].' - '.$myrow['supplierno']);
	$YPos -= $line_height;
}

$pdf->addText($XPos, $YPos,$FontSize, _('Date').': '.$myrow['transdate']);
$YPos -= $line_height;
$pdf->addText($XPos, $YPos,$FontSize, _('Account').': '.$myrow['accountname']);
$YPos -= $line_height;
$pdf->addText($XPos, $YPos,$FontSize, _('Narrative').': '.$myrow['ref']);
$YPos -= ($line_height*6);
$pdf->line(($Page_Width/2)-100, $YPos,($Page_Width/2)+100, $YPos);
$YPos -= ($line_height+5);
$pdf->addTextWrap(($Page_Width/2)-100, $YPos,200,$FontSize, _('FIRMA RECIBIDO'),'center');
// ASIENTOS CONTABLES
/*draw a square grid for entering line items */
$YPos = 400;
$pdf->line($XPos, $YPos,$Page_Width-$Right_Margin, $YPos);
$pdf->line($Page_Width-$Right_Margin, $YPos,$Page_Width-$Right_Margin, $Bottom_Margin+220); // linea derecha vertical
$pdf->line($Page_Width-$Right_Margin, $Bottom_Margin+220,$XPos, $Bottom_Margin+220); // abajo horizontal
$pdf->line($XPos, $Bottom_Margin+220,$XPos, $YPos); // linea izq. vertical

$YPos -= $line_height;
/*Set up headings */
$FontSize=9;

//$LeftOvers = $pdf->addTextWrap($Left_Margin+2,$YPos,60,$FontSize,_('Amount'), 'centre');
$LeftOvers = $pdf->addTextWrap($Left_Margin+2,$YPos,200,$FontSize,_('Account'), 'centre');
$LeftOvers = $pdf->addTextWrap($Left_Margin+210,$YPos,60,$FontSize,_('Amount'), 'right');
$LeftOvers = $pdf->addTextWrap($Left_Margin+330,$YPos,60,$FontSize,_('Reference'), 'centre');
$YPos-=$line_height;

/*draw a line */
$pdf->line($XPos, $YPos,$Page_Width-$Right_Margin, $YPos);

if($tipo==22){
	// IMPRIMIR LAS ASIGNACIONES DEL CHEQUE
	$sql = "SELECT supptrans.suppreference, supptrans.transno FROM supptrans WHERE id IN (SELECT transid_allocto FROM suppallocs WHERE transid_allocfrom = ".$myrow['banktransid'].")";
	$res3 = DB_query($sql,$db);
	while($alloc = DB_fetch_array($res3)){
		$pdf->addText($XPos, $YPos,$FontSize, _('Allocations').': '.$alloc['suppreference'].' - '.$myrow['transno']);
		$YPos -= $line_height;
	}
}
$YPos -= ($line_height);
//$YPos = $Boton_Margin + 200;
?>