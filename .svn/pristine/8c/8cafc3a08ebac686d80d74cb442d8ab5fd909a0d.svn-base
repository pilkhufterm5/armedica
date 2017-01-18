<?php
/* $Revision: 1.5 $ */
/*PDF page header for inventory bar code report */
if ($PageNumber>1){
	$pdf->newPage();
}

$FontSize=10;
$YPos= $Page_Height-$Top_Margin;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,$_SESSION['CompanyRecord']['coyname']);

$YPos -=$line_height;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,_('C&oacute;digos de Barras Para las Categorias entre') . ' ' . $_POST['FromCriteria'] . ' ' . _('and') . ' ' . $_POST['ToCriteria']);
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-120,$YPos,120,$FontSize,_('Printed') . ': ' . Date('d M Y') . '   ' . _('Page') . ' ' . $PageNumber);

$YPos -=(2*$line_height);

/*Draw a rectangle to put the headings in     */

$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);
$pdf->line($Left_Margin, $YPos+$line_height,$Left_Margin, $YPos- $line_height);
$pdf->line($Left_Margin, $YPos- $line_height,$Page_Width-$Right_Margin, $YPos- $line_height);
$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos- $line_height);

/*set up the headings */
$Xpos = $Left_Margin+1;
	
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,300-$Left_Margin,$FontSize,_('Category') . '/' . _('Item'), 'centre');
	//$LeftOvers = $pdf->addTextWrap(390,$YPos,60,$FontSize,_('Quantity'), 'centre');
	$LeftOvers = $pdf->addTextWrap(430,$YPos,150,$FontSize,_('C&oacute;digo de Barras'), 'centre');
	//$LeftOvers = $pdf->addTextWrap(510,$YPos,60,$FontSize,_('Item Value'), 'centre');
$FontSize=8;
$YPos =$YPos - (2*$line_height);

$PageNumber++;

?>
