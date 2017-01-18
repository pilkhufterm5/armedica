<?php
/* $Revision: 1.12 $ */
/* March 2007 bowikaxu - Impresion de Remisiones Grandes */
$minux= (11.3*.2);
if (!$FirstPage){ /* only initiate a new page if its not the first */
	$pdf->newPage();
}

$YPos = $Page_Height - $Top_Margin;

$pdf->addText(536, 296+405-$minux, $FontSize, $myrow['transno']); // numero de remision

$pdf->addText(536, 296-$minux, $FontSize, $myrow['transno']); // numero de remision
//$pdf->addText($Page_Width-180, $YPos-13, $FontSize, $FromTransNo);
//$pdf->addText($Page_Width-268, $YPos-26, $FontSize, _('Customer Code'));

// cliente
$pdf->addText(50, 280+405-$minux, $FontSize, $myrow['name'].' - '.$myrow['debtorno'] . ' ' . _('Sucursal') . ' ' . $myrow['branchcode']);

$pdf->addText(50, 280-$minux, $FontSize, $myrow['name'].' - '.$myrow['debtorno'] . ' ' . _('Sucursal') . ' ' . $myrow['branchcode']);
//$pdf->addText($Page_Width-268, $YPos-39, $FontSize, _('Date'));
$pdf->addText(418, 288+405-$minux, $FontSize, ConvertDate($myrow['trandate']));

$pdf->addText(418, 288-$minux, $FontSize, ConvertDate($myrow['trandate']));
///			$pdf->addTextWrap($Left_Margin+430,$YPos+185,295,$FontSize,$myrow['consignment']. '  .'.'.');

/*
if ($InvOrCredit=='Invoice') {

	$pdf->addText($Page_Width-268, $YPos-52-$minux, $FontSize, _('Order No'));
	$pdf->addText($Page_Width-180, $YPos-52-$minux, $FontSize, $myrow['orderno']);
	$pdf->addText($Page_Width-268, $YPos-65-$minux, $FontSize, _('Order Date'));
	$pdf->addText($Page_Width-180, $YPos-65-$minux, $FontSize, ConvertSQLDate($myrow['orddate']));
	$pdf->addText($Page_Width-268, $YPos-78-$minux, $FontSize, _('Dispatch Detail'));
	$pdf->addText($Page_Width-180, $YPos-78-$minux, $FontSize, $myrow['shippername'] . '-' . $myrow['consignment']);
	$pdf->addText($Page_Width-268, $YPos-91-$minux, $FontSize, _('Dispatched From'));
	$pdf->addText($Page_Width-180, $YPos-91-$minux, $FontSize, $myrow['locationname']);
}


$pdf->addText($Page_Width-268, $YPos-104-$minux, $FontSize, _('Page'));
$pdf->addText($Page_Width-180, $YPos-104-$minux, $FontSize, $PageNumber);
*/

/*Now the customer charged to details top left */

$XPos = $Left_Margin;
$YPos = $Page_Height - $Top_Margin;

$FontSize=10;

//$pdf->addText($XPos, $YPos, $FontSize, _('Sold To') . ':');
//$XPos +=80;

//if ($myrow['invaddrbranch']==0){
	//$pdf->addText($XPos, $YPos-$minux, $FontSize, $myrow['name']);
	$pdf->addText(50, 265+405-$minux, $FontSize, $myrow['address1'].' '.$myrow['address2']);
	$pdf->addText(50, 250+405-$minux, $FontSize, $myrow['address5'].' '.$myrow['address4']);
	$pdf->addText(50, 235+405-$minux, $FontSize, $myrow['taxref'].' - Vendedor(a): '.$myrow['salesmanname']);
	
	$pdf->addText(50, 265-$minux, $FontSize, $myrow['address1'].' '.$myrow['address2']);
	$pdf->addText(50, 250-$minux, $FontSize, $myrow['address5'].' '.$myrow['address4']);
	$pdf->addText(50, 235-$minux, $FontSize, $myrow['taxref'].' - Vendedor(a): '.$myrow['salesmanname']);
	//$pdf->addText(50, 220-$minux, $FontSize, $myrow['salesmanname']);
/* }else {
	//$pdf->addText($XPos, $YPos-$minux, $FontSize, $myrow['name']);
	$pdf->addText($XPos, $YPos-14-$minux, $FontSize, $myrow['brpostaddr1']);
	$pdf->addText($XPos, $YPos-28-$minux, $FontSize, $myrow['brpostaddr2']);
	$pdf->addText($XPos, $YPos-42-$minux, $FontSize, $myrow['brpostaddr3'] . ' ' . $myrow['brpostaddr4'] . ' ' . $myrow['brpostaddr5'] . ' ' . $myrow['brpostaddr6']);
}*/

$XPos = $Left_Margin;

$YPos = 190+405;

?>
