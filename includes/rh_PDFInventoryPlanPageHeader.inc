<?php

/*PDF page header for inventory planning report */

if ($PageNumber>1){
	$pdf->newPage();
}

$FontSize=10;
$YPos= $Page_Height-$Top_Margin;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,$_SESSION['CompanyRecord']['coyname']);
$html.= "<tr><td colspan='{$cols}'>".$_SESSION['CompanyRecord']['coyname']."</td></tr>";

$YPos -=$line_height;

$FontSize=10;
if ($_POST['Location']=='All'){

	$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos,450,$FontSize,_('Inventory Planning for Categories Between') . ' ' . $_POST['FromCriteria'] . ' ' . _('and') . ' ' . $_POST['ToCriteria'] . ' ' . _('for all stock locations'));
	$html.= "<tr><td colspan='{$cols}'>"._('Inventory Planning for Categories Between') . ' ' . $_POST['FromCriteria'] . ' ' . _('and') . ' ' . $_POST['ToCriteria'] . ' ' . _('for all stock locations')."</td></tr>";
	
} else {

	$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos,450,$FontSize,_('Inventory Planning for Categories Between') . ' ' . $_POST['FromCriteria'] . ' ' . _('and') . ' ' . $_POST['ToCriteria'] . ' ' . _('for stock at') . ' ' . $_POST['Location']);
	$html.= "<tr><td colspan='{$cols}'>"._('Inventory Planning for Categories Between') . ' ' . $_POST['FromCriteria'] . ' ' . _('and') . ' ' . $_POST['ToCriteria'] . ' ' . _('for stock at') . ' ' . $_POST['Location']."</td></tr>";
}

$FontSize=8;
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-120,$YPos,120,$FontSize,_('Printed') . ': ' . Date($_SESSION['DefaultDateFormat']) . '   ' . _('Page') . ' ' . $PageNumber);

$YPos -=(2*$line_height);

/*Draw a rectangle to put the headings in     */

$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);
$pdf->line($Left_Margin, $YPos+$line_height,$Left_Margin, $YPos- $line_height);
$pdf->line($Left_Margin, $YPos- $line_height,$Page_Width-$Right_Margin, $YPos- $line_height);
$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos- $line_height);

/*set up the headings */
$XPos = $Left_Margin+1;

$LeftOvers = $pdf->addTextWrap($XPos,$YPos,180,$FontSize,_('Item'),'centre');

$LeftOvers = $pdf->addTextWrap(270,$YPos,40,$FontSize,$Period_4_Name . ' ' . _('Qty'),'centre');
$LeftOvers = $pdf->addTextWrap(307,$YPos,40,$FontSize,$Period_3_Name . ' ' . _('Qty'),'centre');
$LeftOvers = $pdf->addTextWrap(348,$YPos,40,$FontSize,$Period_2_Name . ' ' . _('Qty'),'centre');
$LeftOvers = $pdf->addTextWrap(389,$YPos,40,$FontSize,$Period_1_Name . ' ' . _('Qty'),'centre');
$LeftOvers = $pdf->addTextWrap(430,$YPos,40,$FontSize,$Period_0_Name . ' ' . _('MTD'),'centre');

$content_head = array();
$html.= "<tr><td colspan='2'>"._('Item')."</td>";
for($i=$_POST['NumberMonthsHolding'];$i>=0;$i--){
	if($i==0){
		$content_head[]= "<td >".strftime('%b',mktime(0,0,0,Date('m'),Date('d'),Date('Y')))." " ._('MTD')."</td>";
	}else{
		$content_head[]= "<td >".strftime('%b',mktime(0,0,0,Date('m')-$i,Date('d'),Date('Y')))." "._('Qty')."</td>";
	}

}
$html.= implode("",$content_head);
$html.= "<td >".$_POST['NumberMonthsHolding'] . ' ' . _('ms stk')."</td>";
$html.= "<td >". _('QOH')."</td>";
$html.= "<td >". _('Cust Ords')."</td>";
$html.= "<td >". _('Splr Ords')."</td>";
$html.= "<td >". _('Sugg Ord')."</td></tr>";

$LeftOvers = $pdf->addTextWrap(471,$YPos,40,$FontSize,$_POST['NumberMonthsHolding'] . ' ' . _('ms stk'),'centre');
$LeftOvers = $pdf->addTextWrap(617,$YPos,40,$FontSize,_('QOH'),'centre');
$LeftOvers = $pdf->addTextWrap(648,$YPos,40,$FontSize,_('Cust Ords'),'centre');
$LeftOvers = $pdf->addTextWrap(694,$YPos,40,$FontSize,_('Splr Ords'),'centre');
$LeftOvers = $pdf->addTextWrap(735,$YPos,40,$FontSize,_('Sugg Ord'),'centre');

$YPos =$YPos - (2*$line_height);
$FontSize=8;
?>
