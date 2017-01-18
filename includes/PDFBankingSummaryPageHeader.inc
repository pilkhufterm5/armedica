<?php
/* $Revision: 1.8 $ */
$PageNumber++;
if ($PageNumber>1){
	$pdf->newPage();

}

// $pdf->SetFont('Arial', 'I');
$YPos = $Page_Width-100;

$pdf->addJpegFromFile('companies/' . $_SESSION['DatabaseName'] . '/logo.jpg',$Left_Margin,$YPos-30,0,100);

$FontSize=12;
$LineHeight = 12;
$YPos += 65;
$XPos = $Page_Height/2 + 90;
// bowikaxu realhost March 2008 - show company address

$pdf->addText($XPos, $YPos-25,$FontSize, $_SESSION['CompanyRecord']['coyname']);
 $LineCount += 1;
if ($_SESSION['CompanyRecord']['regoffice1'] <> '') {
  $LineCount += 1;
  $pdf->addText($XPos, $YPos-35,$FontSize, $_SESSION['CompanyRecord']['regoffice1']);
}
if ($_SESSION['CompanyRecord']['regoffice2'] <> '') {
  $LineCount += 1;
  $pdf->addText($XPos, $YPos-45, $_SESSION['CompanyRecord']['regoffice2']);
}
if (($_SESSION['CompanyRecord']['regoffice3'] <> '') OR ($_SESSION['CompanyRecord']['regoffice4'] <> '') OR ($_SESSION['CompanyRecord']['regoffice5'] <> '')) {
  $LineCount += 1;
  $pdf->addText($XPos, $YPos-$LineCount*$LineHeight,$FontSize, $_SESSION['CompanyRecord']['regoffice3'] . ' ' . $_SESSION['CompanyRecord']['regoffice4'] . ' ' . $_SESSION['CompanyRecord']['regoffice5']);  // country in 6 not printed
}
$LineCount += 1;
$pdf->addText($XPos, $YPos-$LineCount*$LineHeight, $FontSize, _('Teléfono') . ':' . $_SESSION['CompanyRecord']['telephone']);
$LineCount += 1;
$pdf->addText($XPos, $YPos-$LineCount*$LineHeight,$FontSize, _('Fax') . ': ' . $_SESSION['CompanyRecord']['fax']);
$LineCount += 1;
$pdf->addText($XPos, $YPos-$LineCount*$LineHeight, $FontSize, _('E-mail') . ': ' . $_SESSION['CompanyRecord']['email']);

$YPos -= 130;
$FontSize=12;
$pdf->addText($XPos, $YPos-20,$FontSize,_('Recibo').' '._('de').' '._('Pago'). ' ' . _('Para el lote') . ' # ' . $_POST['BatchNo'] . ' ' . _('de') . ' ' . $BankTransType);


// $YPos -=40;
$pdf->addText($XPos, $YPos,$FontSize, _('Página') .': ' . $PageNumber);

/*Now print out the company name and address */
$XPos = $Left_Margin;
// $YPos -= $line_height;

//$pdf->addText($XPos, $YPos,$FontSize, $_SESSION['CompanyRecord']['coyname']);
$FontSize=10;

// $YPos -=$line_height;
$XPos = $Left_Margin;

$pdf->addText($XPos, $YPos,$FontSize, _('Fecha de depósito') .': ' . ConvertSQLDate($myrow['transdate']));
$YPos -= $line_height;
$pdf->addText($XPos, $YPos,$FontSize, _('Depositado en ') . ': ' . $BankActName . ' - ' . _('Número de cuenta') . ': ' . $BankActNumber);
$YPos -= $line_height;

/*
 * iJPe
 * realhost
 * 2010-05-28
 */

if(strlen($BankingReference)>92){
        $lines = strlen($BankingReference)/92;
        $lines  = round($lines);
        for($i=0;$i<=$lines;$i++){
            if ($i==0){
                $pdf->addText($XPos, $YPos,$FontSize, _('Referencia') . ': ' . substr($BankingReference,($i*92),92)." ...");
            }else{
                $pdf->addText($XPos, $YPos,$FontSize, substr($BankingReference,($i*92),92)." ...");
            }
            $YPos -= ($line_height);
        }
}else {
        $pdf->addText($XPos, $YPos,$FontSize, _('Referencia') . ': ' . $BankingReference);
        $YPos -= ($line_height);
}

$pdf->addText($XPos, $YPos,$FontSize, _('Moneda') . ': ' . $Currency);
$YPos -= $line_height;

/*draw a square grid for entering line items */
$pdf->line($XPos, $YPos,$Page_Height-$Right_Margin, $YPos);
$pdf->line($Page_Height-$Right_Margin, $YPos,$Page_Height-$Right_Margin, $Bottom_Margin);
$pdf->line($Page_Height-$Right_Margin, $Bottom_Margin,$XPos, $Bottom_Margin);
$pdf->line($XPos, $Bottom_Margin,$XPos, $YPos);

$YPos -= $line_height;
/*Set up headings */
$FontSize=8;

$LeftOvers = $pdf->addTextWrap($Left_Margin+10,$YPos,30,$FontSize,_('Folio Titular'), 'centre');
$LeftOvers = $pdf->addTextWrap($Left_Margin+10,$YPos-$FontSize,30,$FontSize,$LeftOvers, 'centre');
$LeftOvers = $pdf->addTextWrap(70,$YPos,40,$FontSize,_('Número de factura'), 'center');
$LeftOvers = $pdf->addTextWrap(70,$YPos-$FontSize,40,$FontSize,$LeftOvers, 'center');
$LeftOvers = $pdf->addTextWrap(120,$YPos,40,$FontSize,_('Fecha de factura'), 'center');
$LeftOvers = $pdf->addTextWrap(120,$YPos-$FontSize,40,$FontSize,$LeftOvers, 'center');
$LeftOvers = $pdf->addTextWrap(175,$YPos,40,$FontSize,_('Cobrador'), 'centre');
$LeftOvers = $pdf->addTextWrap(315,$YPos,40,$FontSize,_('Cliente'), 'centre');
$LeftOvers = $pdf->addTextWrap(400,$YPos,85,$FontSize,_('Detalles del depósito'), 'centre');
$LeftOvers = $pdf->addTextWrap(540,$YPos,45,$FontSize,_('Descripción'), 'centre');
$LeftOvers = $pdf->addTextWrap(620,$YPos,50,$FontSize,_('Subtotal'), 'left');
$LeftOvers = $pdf->addTextWrap(670,$YPos,50,$FontSize,_('Impuesto'), 'left');
$LeftOvers = $pdf->addTextWrap(730,$YPos,50,$FontSize,_('Total'), 'left');
$YPos-=$line_height;

$YPosTotales=$YPos-300;
$LeftOvers = $pdf->addTextWrap(600,$YPos-300,90,12,_('Subtotal:'), 'left');
$LeftOvers = $pdf->addTextWrap(600,$YPos-314,90,12,_('Impuesto Total:'), 'left');
$LeftOvers = $pdf->addTextWrap(600,$YPos-328,90,12,_('Total:'), 'left');
/*draw a line */
$pdf->line($XPos, $YPos,$Page_Height-$Right_Margin, $YPos);

$YPos -= ($line_height);
?>