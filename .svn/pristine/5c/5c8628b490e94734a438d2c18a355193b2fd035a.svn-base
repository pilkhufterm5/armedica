<?php
/* $Revision: 1.4 $ */
/*PDF page header for inventory check report */
if ($PageNumber>1){
	$pdf->newPage();
}

$FontSize=12;
$YPos= $Page_Height-$Top_Margin;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,$_SESSION['CompanyRecord']['coyname']);
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-180,$YPos,180,$FontSize,_('Printed') . ': ' . Date('d M Y') . '   ' . _('Page') . ' ' . $PageNumber);

$YPos -=15;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,550,$FontSize,_('Check Sheets for Categories between') . ' ' . $_POST['FromCriteria'] . ' ' . _('and') . ' ' . $_POST['ToCriteria'] . ' ' . _('for stock at') . ' ' . $_POST['Location']);

$YPos -=20;
/*Draw a rectangle to put the headings in     */
$BoxHeight =15;

$pdf->line($Left_Margin, $YPos+$BoxHeight,$Page_Width-$Right_Margin, $YPos+$BoxHeight);
$pdf->line($Left_Margin, $YPos+$BoxHeight,$Left_Margin, $YPos- $BoxHeight);
$pdf->line($Left_Margin, $YPos-$BoxHeight,$Page_Width-$Right_Margin, $YPos-$BoxHeight);
$pdf->line($Page_Width-$Right_Margin, $YPos+$BoxHeight,$Page_Width-$Right_Margin, $YPos-$BoxHeight);

/*set up the headings */
$Xpos = $Left_Margin+1;

/*draw vertical lines */
$pdf->line(350+61, $YPos-$line_height,350+61, $Bottom_Margin+24);
$pdf->line(415+61, $YPos-$line_height,415+61, $Bottom_Margin+24);
$pdf->line(475+61, $YPos-$line_height,475+61, $Bottom_Margin+24);

$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,80-$Left_Margin,$FontSize,_('Stock ID'), 'centre');
$LeftOvers = $pdf->addTextWrap($Xpos+50,$YPos,100-$Left_Margin,$FontSize,_('ID Agrupador'), 'centre');
$LeftOvers = $pdf->addTextWrap($Xpos+50,$YPos-$FontSize,100-$Left_Margin,$FontSize,$LeftOvers, 'centre');
$LeftOvers = $pdf->addTextWrap($Xpos+120,$YPos,100-$Left_Margin,$FontSize,_('Código de barras'), 'centre');
$LeftOvers = $pdf->addTextWrap($Xpos+120,$YPos-$FontSize,100-$Left_Margin,$FontSize,$LeftOvers, 'centre');
$LeftOvers = $pdf->addTextWrap(230,$YPos,100-$Left_Margin,$FontSize,_('Item'), 'centre');
if ($_POST['ShowInfo']==true){
	$LeftOvers = $pdf->addTextWrap(341+65,$YPos,60,$FontSize,_('QOH'), 'centre');
	$LeftOvers = $pdf->addTextWrap(341+61+60,$YPos,80,$FontSize,_('Cust Ords'), 'centre');
	$LeftOvers = $pdf->addTextWrap(341+61+61+65,$YPos,80,$FontSize,_('Available'), 'centre');
	}
$FontSize=10;
$YPos -=($line_height);
?>
