<?php
/* $Revision: 1.6 $ */
/*PDF page header for price list report */
if ($PageNumber>1){
	$pdf->newPage();
}

$FontSize=10;
$YPos= $Page_Height-$Top_Margin;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,$_SESSION['CompanyRecord']['coyname']);
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-140,$YPos,140,$FontSize, _('Printed').': ' . Date($_SESSION['DefaultDateFormat']) . '   '. _('Page'). ' ' . $PageNumber);

$YPos -=$line_height;
//Note, this is ok for multilang as this is the value of a Select, text in option is different
if ($_POST['CustomerSpecials']==_('Customer Special Prices Only')){

	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,550,$FontSize, _('Special Prices for Inventory Categories between').' ' . $_POST['FromCriteria'] . ' ' . _('and') . ' ' . $_POST['ToCriteria'] . ' ' . _('for') . ' ' . $CustomerName);

} else {
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,550,$FontSize, _('Price List for Inventory Categories between') . ' ' . $_POST['FromCriteria'] . ' ' . _('and'). ' ' . $_POST['ToCriteria'] . ' ' . _('for') . ' ' . $SalesTypeName);

}


$YPos -=(2*$line_height);

/*Draw a rectangle to put the headings in     */

$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);
$pdf->line($Left_Margin, $YPos+$line_height,$Left_Margin, $YPos- $line_height);
$pdf->line($Left_Margin, $YPos- $line_height,$Page_Width-$Right_Margin, $YPos- $line_height);
$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos- $line_height);

/*set up the headings */
$Xpos = $Left_Margin+1;

$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,300-$Left_Margin,$FontSize, _('Category') . '/' . _('Item'), 'centre');

if ($_POST['CustomerSpecials']==_('Customer Special Prices Only')){

	$LeftOvers = $pdf->addTextWrap(300,$YPos,120,$FontSize, _('Branch'), 'centre');

}

$LeftOvers = $pdf->addTextWrap(450,$YPos,60,$FontSize, _('Price') , 'centre');

if ($_POST['ShowGPPercentages']=='Yes'){
	$LeftOvers = $pdf->addTextWrap(510,$YPos,60,$FontSize, _('GP') .' %', 'centre');
}


$FontSize=8;
$YPos -= (1.5 * $line_height);

//rleal Jul 21 2011
$SQL = "SELECT * from taxcategories";
$rh_tax_result = DB_query($SQL,$db,'','',false,false);
$YPos-=2;
While ($rh_tax = DB_fetch_array($rh_tax_result,$db)){

			//$YPos -=(2*$line_height);
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300-$Left_Margin,$FontSize, $rh_tax['taxcatid'] . "  ".$rh_tax['taxcatname']);
			$CurrCode = $PriceList['currabrev'];
			$FontSize = 8;
			$YPos -= $line_height;
		}
	$YPos+=3;

$PageNumber++;

?>