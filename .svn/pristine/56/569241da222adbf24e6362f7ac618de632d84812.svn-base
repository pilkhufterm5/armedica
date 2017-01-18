<?php

/*PDF page header for inventory check report */
if ($PageNumber>1){
	$pdf->newPage();
}

$FontSize=12;
$YPos= $Page_Height-$Top_Margin;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,$_SESSION['CompanyRecord']['coyname']);
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-120,$YPos,120,$FontSize, _('Printed'). ': ' . Date($DefaultDateFormat) . '   ' . _('Page'). ' ' . $PageNumber);

$YPos -=15;
$FontSize=12;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,550,$FontSize, _('Negative Stocks Listing'));

$YPos -=25;
/*Draw a rectangle to put the headings in     */
$BoxHeight =20;
$FontSize=12;
$pdf->line($Left_Margin, $YPos+$BoxHeight,$Page_Width-$Right_Margin, $YPos+$BoxHeight);
$pdf->line($Left_Margin, $YPos+$BoxHeight,$Left_Margin, $YPos);
$pdf->line($Left_Margin, $YPos,$Page_Width-$Right_Margin, $YPos);
$pdf->line($Page_Width-$Right_Margin, $YPos+$BoxHeight,$Page_Width-$Right_Margin, $YPos);

$YPos +=5;

/*set up the headings */
$Xpos = $Left_Margin+1;
$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,100, $FontSize,_('Location'), 'centre');
$LeftOvers = $pdf->addTextWrap($Xpos+110,$YPos,80, $FontSize,_('ID Agrupador'), 'centre');
$LeftOvers = $pdf->addTextWrap($Xpos+200,$YPos,100, $FontSize,_('Código de barras'), 'centre');
$LeftOvers = $pdf->addTextWrap(345,$YPos,125,$FontSize,_('Item Description'), 'centre');
$LeftOvers = $pdf->addTextWrap(500,$YPos,70,$FontSize,_('Quantity'), 'centre');

$FontSize=10;
$YPos -= 30;

?>
