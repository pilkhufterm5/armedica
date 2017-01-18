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

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,_('Del Almacen de:'  ) . $_REQUEST['StockLocation']);
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-10,300,$FontSize,_('Del Categoria:'  ) .$_REQUEST['StockCat']);
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-150,$YPos,300,$FontSize,_('Printed') . ': ' . Date('d M Y') . '   ' . _('Page') . ' ' . $PageNumber);

$YPos -=(2*$line_height);

/*Draw a rectangle to put the headings in     */

$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);
$pdf->line($Left_Margin, $YPos+$line_height,$Left_Margin, $YPos- $line_height);
$pdf->line($Left_Margin, $YPos- $line_height,$Page_Width-$Right_Margin, $YPos- $line_height);
$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos- $line_height);

/*set up the headings */
$Xpos = $Left_Margin+1;
	
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,60,$FontSize,_('ID'), 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+50,$YPos,55,$FontSize,_('ID Agrupador'), 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+50,$YPos-$FontSize,55,$FontSize,$LeftOvers, 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+110,$YPos,45,$FontSize,_('Código de barras'), 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+110,$YPos-$FontSize,45,$FontSize,$LeftOvers, 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+180,$YPos,35,$FontSize,_('Units') , 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+215,$YPos,100,$FontSize,_('Description'), 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+320,$YPos,50,$FontSize,_('fecha compra') , 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+320,$YPos-$FontSize,50,$FontSize,$LeftOvers, 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+365,$YPos,50,$FontSize,_('Existencia Sistema'), 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+365,$YPos-$FontSize,50,$FontSize, $LeftOvers, 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+420,$YPos,50,$FontSize,_('Existencia fisica'), 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+420,$YPos-$FontSize,50,$FontSize, $LeftOvers, 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+500,$YPos,50,$FontSize,_('Diferencia'), 'left');
	
//	$LeftOvers = $pdf->addTextWrap($Xpos+250,$YPos,100,$FontSize, _('Available'), 'left');
	
	
	//$LeftOvers = $pdf->addTextWrap(390,$YPos,60,$FontSize,_('Quantity'), 'centre');
	
	//$LeftOvers = $pdf->addTextWrap(510,$YPos,60,$FontSize,_('Item Value'), 'centre');
$FontSize=8;
$YPos =$YPos - (2*$line_height);

$PageNumber++;

?>
