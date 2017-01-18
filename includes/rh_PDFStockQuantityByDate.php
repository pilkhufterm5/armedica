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

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,_('Existencias por fecha:'  ) . $_REQUEST['OnHandDate']);
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-10,300,$FontSize,_('Del Categoria:'  ) .$_REQUEST['StockCategory']);
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-150,$YPos,300,$FontSize,_('Printed') . ': ' . Date('d M Y') . '   ' . _('Page') . ' ' . $PageNumber);

$YPos -=(2*$line_height);

/*Draw a rectangle to put the headings in     */

$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);
$pdf->line($Left_Margin, $YPos+$line_height,$Left_Margin, $YPos- $line_height);
$pdf->line($Left_Margin, $YPos- $line_height,$Page_Width-$Right_Margin, $YPos- $line_height);
$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos- $line_height);

/*set up the headings */
$Xpos = $Left_Margin+1;
	
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,40,$FontSize,_('StockID'), 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+50,$YPos,60,$FontSize,_('ID Agrupador'), 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+120,$YPos,80,$FontSize,_('Código de barras'), 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+210,$YPos,60,$FontSize,_('Description'), 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+380,$YPos,50,$FontSize,_('Disponible') , 'left');
	$LeftOvers = $pdf->addTextWrap($Xpos+440,$YPos,40,$FontSize,_('Costo') , 'left');
	if(isset($_POST['value']))
	{
	$LeftOvers = $pdf->addTextWrap($Xpos+500,$YPos,50,$FontSize,_('Valor Total'), 'left');
	}
//	$LeftOvers = $pdf->addTextWrap($Xpos+250,$YPos,100,$FontSize, _('Available'), 'left');
	
	
	//$LeftOvers = $pdf->addTextWrap(390,$YPos,60,$FontSize,_('Quantity'), 'centre');
	
	//$LeftOvers = $pdf->addTextWrap(510,$YPos,60,$FontSize,_('Item Value'), 'centre');
$FontSize=8;
$YPos =$YPos - (2*$line_height);

$PageNumber++;

?>
