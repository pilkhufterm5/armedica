<?php
/* $Revision: 1.7 $ */
/*PDF page header for inventory valuation report */
if ($PageNumber>1){
	$pdf->newPage();
}


$FontSize=10;
$YPos= $Page_Height-$Top_Margin;
if($imprimirheader){
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,$_SESSION['CompanyRecord']['coyname']);

$YPos -=$line_height;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,_('Inventory for Categories between') . ' ' . $_POST['FromCriteria'] . ' ' . _('and') . ' ' . $_POST['ToCriteria'] . ' ' . _('at') . ' ' . $_POST['Location'] . ' ' . _('location'));
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
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,50,$FontSize,_('Stock ID'), 'center',$margen);$Xpos+=50;
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,40,$FontSize,_('IDAgrup'), 'center',$margen);$Xpos+=40;
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,70,$FontSize,_('C.Barras'), 'center',$margen);$Xpos+=70;

	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,70,$FontSize,_('Etiqueta'), 'center',$margen);$Xpos+=70;

	//$LeftOvers = $pdf->addTextWrap($Left_Margin+140,$YPos-$FontSize,70,$FontSize,$LeftOvers, 'center',$margen);
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,140,$FontSize,_('Category') . '/' . _('Item'), 'center',$margen);$Xpos+=140;
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,40,$FontSize,_('Cant.'), 'center',$margen);$Xpos+=40;
	//$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,60,$FontSize,_('Unit Cost'), 'centre',$margen);$Xpos+=60;
	//$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,60,$FontSize,_('Item Value'), 'centre',$margen);$Xpos+=60;
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,60,$FontSize,_('serialno'), 'center',$margen);$Xpos+=60;
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,70,$FontSize,_('expirationdate'), 'center',$margen);$Xpos+=70;
} else {
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,320-$Left_Margin,$FontSize,_('Category'), 'center',$margen);
	$LeftOvers = $pdf->addTextWrap(510,$YPos,60,$FontSize,_('Value'), 'centre',$margen);
}
}
$FontSize=8;
$YPos =$YPos - (2*$line_height);

$PageNumber++;


