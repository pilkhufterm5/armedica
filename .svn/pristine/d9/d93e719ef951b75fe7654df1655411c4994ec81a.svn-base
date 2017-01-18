<?php
/* $Revision: 1.12 $ */
/* March 2007 bowikaxu - Impresion de Remisiones Grandes */

if (!$FirstPage){ /* only initiate a new page if its not the first */
	$pdf->newPage();
}

$YPos = $Page_Height - $Top_Margin;

$pdf->addText(493, 498, $FontSize, $myrow['transno']); // numero de remision
//$pdf->addText($Page_Width-180, $YPos-13, $FontSize, $FromTransNo);
//$pdf->addText($Page_Width-268, $YPos-26, $FontSize, _('Customer Code'));

// cliente
$pdf->addText(100, 618, $FontSize, utf8_decode($myrow['brname']));
$pdf->addText(119, 598, $FontSize, utf8_decode($myrow['name']).'    '. utf8_decode($myrow['branchcode']));

$pdf->addText($Left_Margin+362, 625, $FontSize,$myrow['debtorno']); // codigo cliente
//$pdf->addText($Page_Width-268, $YPos-39, $FontSize, _('Date'));
$pdf->addText(485, 544, $FontSize, ConvertDate($myrow['trandate'],2));
///			$pdf->addTextWrap($Left_Margin+430,$YPos+185,295,$FontSize,$myrow['consignment']. '  .'.'.');

/*
if ($InvOrCredit=='Invoice') {

	$pdf->addText($Page_Width-268, $YPos-52, $FontSize, _('Order No'));
	$pdf->addText($Page_Width-180, $YPos-52, $FontSize, $myrow['orderno']);
	$pdf->addText($Page_Width-268, $YPos-65, $FontSize, _('Order Date'));
	$pdf->addText($Page_Width-180, $YPos-65, $FontSize, ConvertSQLDate($myrow['orddate']));
	$pdf->addText($Page_Width-268, $YPos-78, $FontSize, _('Dispatch Detail'));
	$pdf->addText($Page_Width-180, $YPos-78, $FontSize, $myrow['shippername'] . '-' . $myrow['consignment']);
	$pdf->addText($Page_Width-268, $YPos-91, $FontSize, _('Dispatched From'));
	$pdf->addText($Page_Width-180, $YPos-91, $FontSize, $myrow['locationname']);
}


$pdf->addText($Page_Width-268, $YPos-104, $FontSize, _('Page'));
$pdf->addText($Page_Width-180, $YPos-104, $FontSize, $PageNumber);
*/

/*Now the customer charged to details top left */

$XPos = $Left_Margin;
$YPos = $Page_Height - $Top_Margin;

//$FontSize=9;

//$pdf->addText($XPos, $YPos, $FontSize, _('Sold To') . ':');
//$XPos +=80;

//if ($myrow['invaddrbranch']==0){
	//$pdf->addText($XPos, $YPos, $FontSize, $myrow['name']);
//	$pdf->addText(110, 605, $FontSize, $myrow['address1'].' '.$myrow['address2']);
//	$pdf->addText(110, 585, $FontSize, $myrow['address5'].' '.$myrow['address4']);
//	$pdf->addText(110, 552, $FontSize, $myrow['taxref']);
//	$pdf->addText(110, 527, $FontSize, $myrow['salesmanname']);

	$pdf->addText(110, 580, $FontSize, utf8_decode($myrow['address1']).' '.utf8_decode($myrow['address2']) );
	$pdf->addText(110, 560, $FontSize, utf8_decode($myrow['address4']));
	$pdf->addText(417, 560, $FontSize, utf8_decode($myrow['address5']));
	$pdf->addText(110, 540, $FontSize, utf8_decode($myrow['taxref']));
	$pdf->addText(120, 520, $FontSize, utf8_decode($myrow['salesmanname']));

/* }else {
	//$pdf->addText($XPos, $YPos, $FontSize, $myrow['name']);
	$pdf->addText($XPos, $YPos-14, $FontSize, $myrow['brpostaddr1']);
	$pdf->addText($XPos, $YPos-28, $FontSize, $myrow['brpostaddr2']);
	$pdf->addText($XPos, $YPos-42, $FontSize, $myrow['brpostaddr3'] . ' ' . $myrow['brpostaddr4'] . ' ' . $myrow['brpostaddr5'] . ' ' . $myrow['brpostaddr6']);
}*/

$XPos = $Left_Margin;

$YPos = 453;

?>
