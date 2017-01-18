<?php
/* $Revision: 1.7 $ */
/*PDF page header for inventory valuation report */
if ($PageNumber>1){
	$pdf->newPage();
}

$FontSize=10;
$YPos= $Page_Height-$Top_Margin;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,utf8_decode($_SESSION['CompanyRecord']['coyname']));

$YPos -=$line_height;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,_('Inventory Valuation for Categories between') . ' ' . $_POST['FromCriteria'] . ' ' . _('and') . ' ' . $_POST['ToCriteria'] . ' ' . _('at') . ' ' . $_POST['Location'] . ' ' . _('location'));
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-120,$YPos,120,$FontSize,_('Printed') . ': ' . Date($_SESSION['DefaultDateFormat']) . '   ' . _('Page') . ' ' . $PageNumber);

$YPos -=(2*$line_height);

/*Draw a rectangle to put the headings in     */

$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);
$pdf->line($Left_Margin, $YPos+$line_height,$Left_Margin, $YPos- $line_height);
$pdf->line($Left_Margin, $YPos- $line_height,$Page_Width-$Right_Margin, $YPos- $line_height);
$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos- $line_height);

/*set up the headings */
$Xpos = $Left_Margin+1;

if ($_POST['DetailedReport']=='Yes'){
//  . '/' .
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,30,$FontSize, _('Stock ID'), 'centre');
    $LeftOvers = $pdf->addTextWrap(80,$YPos,60,$FontSize,_('ID Agrupador'), 'centre');
    $LeftOvers = $pdf->addTextWrap(150,$YPos,50,$FontSize,_('Código de Barras'), 'centre');
    $LeftOvers = $pdf->addTextWrap(150,$YPos-$FontSize,50,$FontSize,$LeftOvers, 'centre');
    $LeftOvers = $pdf->addTextWrap(230,$YPos,70,$FontSize,_('Category/Item'), 'centre');
	$LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,_('Quantity'), 'centre');
	$LeftOvers = $pdf->addTextWrap(450,$YPos,60,$FontSize,_('Unit Cost'), 'centre');
	$LeftOvers = $pdf->addTextWrap(510,$YPos,60,$FontSize,_('Item Value'), 'centre');
} else {
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,320-$Left_Margin,$FontSize,_('Category'), 'centre');
	$LeftOvers = $pdf->addTextWrap(510,$YPos,60,$FontSize,_('Value'), 'centre');
}

$FontSize=8;
$YPos =$YPos - (2*$line_height);

$PageNumber++;

?>
