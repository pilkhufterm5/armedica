<?php
/* $Revision: 1.7 $ */
/*PDF page header for inventory valuation report */
if ($PageNumber>1){
	$pdf->newPage();
}

$FontSize=10;
$YPos= $Page_Height-$Top_Margin;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,$_SESSION['CompanyRecord']['coyname']);
$YPos -=$line_height;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,_('Report').' '._('Purchase Orders'));

$YPos -=$line_height;
if ($_POST['Location']=='All'){
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,550,$FontSize-1,_('From Inventory Category Code') . ' ' . $_POST['FromCriteria'] . ' ' . _('To Inventory Category Code') . ' ' . $_POST['ToCriteria'] . ' ' . _('at') . ' ' .  _('location').' '._($_POST['Location']));
}else {
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,550,$FontSize-1,_('From Inventory Category Code') . ' ' . $_POST['FromCriteria'] . ' ' . _('To Inventory Category Code') . ' ' . $_POST['ToCriteria'] . ' ' . _('at') . ' ' . _('location').' '.$_POST['Location']);
}
$YPos -=$line_height;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,_('Date').' '._('From').': '.$_POST["FromDate"].' '._('To').': '.$_POST['ToDate']);
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-120,$YPos,120,$FontSize,_('Printed') . ': ' . Date($_SESSION['DefaultDateFormat']) . '   ' . _('Page') . ' ' . $PageNumber);

$YPos -=(2*$line_height);

/*Draw a rectangle to put the headings in     */

$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);
$pdf->line($Left_Margin, $YPos+$line_height,$Left_Margin, $YPos- $line_height- $line_height);
$pdf->line($Left_Margin, $YPos-$line_height-$line_height,$Page_Width-$Right_Margin, $YPos- $line_height-$line_height);
$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos- $line_height- $line_height);

/*set up the headings */
$Xpos = $Left_Margin+1;
$FontSize = 9;
if ($_POST['DetailedReport']=='Yes'){
	
	$LeftOvers = $pdf->addTextWrap(215,$YPos,120,$FontSize+1,_('Ordered'), 'center');
	$LeftOvers = $pdf->addTextWrap(335,$YPos,120,$FontSize+1,_('Received'), 'center');
	$LeftOvers = $pdf->addTextWrap(455,$YPos,120,$FontSize+1,_('Invoiced'), 'center');

	$YPos -= (2*$line_height);
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,200-$Left_Margin,$FontSize,_('Category') . '/' . _('Item'), 'centre');
	
	$LeftOvers = $pdf->addTextWrap(200,$YPos,60,$FontSize,_('Qty'), 'right');
	$LeftOvers = $pdf->addTextWrap(260,$YPos,60,$FontSize,_('Amount'), 'right');
	
	$LeftOvers = $pdf->addTextWrap(320,$YPos,60,$FontSize,_('Qty'), 'right');
	$LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,_('Amount'), 'right');
	
	$LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize,_('Qty'), 'right');
	$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,_('Amount'), 'right');
} else {

	$LeftOvers = $pdf->addTextWrap(215,$YPos,120,$FontSize+1,_('Ordered'), 'center');
	$LeftOvers = $pdf->addTextWrap(335,$YPos,120,$FontSize+1,_('Received'), 'center');
	$LeftOvers = $pdf->addTextWrap(455,$YPos,120,$FontSize+1,_('Invoiced'), 'center');

	$YPos -= (2*$line_height);
	$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,200-$Left_Margin,$FontSize,_('Category'), 'centre');
	$LeftOvers = $pdf->addTextWrap(200,$YPos,60,$FontSize,_('Qty'), 'right');
	$LeftOvers = $pdf->addTextWrap(260,$YPos,60,$FontSize,_('Amount'), 'right');
	
	$LeftOvers = $pdf->addTextWrap(320,$YPos,60,$FontSize,_('Qty'), 'right');
	$LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,_('Amount'), 'right');
	
	$LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize,_('Qty'), 'right');
	$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,_('Amount'), 'right');
}

$FontSize=8;
$YPos =$YPos - (2*$line_height);

$PageNumber++;

?>
