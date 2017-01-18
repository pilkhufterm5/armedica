<?php
/* $Revision: 1.6 $ */
/*PDF page header for aged analysis reports */
$PageNumber++;
if ($PageNumber>1){
	$pdf->newPage();
}

// bowikaxu realhost - june 2007 - get company info
	$sql = "SELECT coyname,
		gstno AS rfc,
		companynumber,
		regoffice1,
		regoffice2,
		regoffice3,
		regoffice4,
		regoffice5,
		regoffice6,
		telephone,
		fax,
		email
		FROM companies
	WHERE coycode=1";
	$res = DB_query($sql,$db,'','');
	$company = DB_fetch_array($res);

$FontSize=8;
$YPos= $Page_Height-$Top_Margin-10;

//$pdf->addText($Left_Margin, $YPos,$FontSize, $_SESSION['CompanyRecord']['coyname']);
$pdf->addJpegFromFile('companies/' . $_SESSION['DatabaseName'] . '/logo.jpg',$Left_Margin,$YPos-50,0,60);

$YPos -=$line_height;

$FontSize =10;
$numHeads=2;

$DatePrintedString = _('Printed') . ': ' . Date("d/m/Y") . '   ' . _('Page') . ' ' . $PageNumber;
$pdf->addText($Page_Width-$Right_Margin-120,$YPos,$FontSize, $DatePrintedString);

/* bowikaxu realhost - show the address*/
// ($Page_Width-$Right_Margin)/2-20

$pdf->addText(($Page_Width-$Right_Margin)/2-20,$YPos,12, $company['coyname']);
$YPos -= $line_height;
$pdf->addText(($Page_Width-$Right_Margin)/2-20,$YPos,$FontSize, $company['rfc']);
$YPos -= $line_height;
$pdf->addText(($Page_Width-$Right_Margin)/2-20,$YPos,$FontSize, $company['regoffice1']);
$YPos -= $line_height;
$pdf->addText(($Page_Width-$Right_Margin)/2-20,$YPos,$FontSize, $company['regoffice2']);
$YPos -= $line_height;
$pdf->addText(($Page_Width-$Right_Margin)/2-20,$YPos,$FontSize, $company['regoffice3'].' '.$company['regoffice5']);
$YPos -= $line_height;
$pdf->addText(($Page_Width-$Right_Margin)/2-20,$YPos,$FontSize, $company['regoffice4'].' '.$company['regoffice6']);
$YPos -= $line_height;
$pdf->addText(($Page_Width-$Right_Margin)/2-20,$YPos,$FontSize, _('Ph').': '.$company['telephone']);
$YPos -= $line_height;
$pdf->addText(($Page_Width-$Right_Margin)/2-20,$YPos,$FontSize, _('Fax').': '.$company['fax']);
$YPos -= $line_height;
$pdf->addText(($Page_Width-$Right_Margin)/2-20,$YPos,$FontSize, _('Email').': '.$company['email']);

/*****************************************************************************************************************************/
$YPos -= $line_height;
$sql2 = "SELECT name, address1, address2, address3, address4, address5, address6, taxref FROM debtorsmaster WHERE debtorno = '".$_GET['DebtorNo']."'";
$res2 = DB_query($sql2,$db);
$row2 = DB_fetch_array($res2);
$pdf->addText($Left_Margin,$YPos,10, _('At&acute;n:'));
$YPos -= $line_height;
$pdf->addText($Left_Margin,$YPos,10, $row2['name']);
$YPos -= $line_height;
$pdf->addText($Left_Margin,$YPos,10, $row2['address1']." ".$row2['address2']);
$YPos -= $line_height;
$pdf->addText($Left_Margin,$YPos,10, $row2['address3']." ".$row2['address4']);
$YPos -= $line_height;
$pdf->addText($Left_Margin,$YPos,10, $row2['address5']." ".$row2['address6']);
$YPos -= $line_height;
$pdf->addText($Left_Margin,$YPos,10, "RFC: ".$row2['taxref']);
$YPos -= $line_height;
/*****************************************************************************************************************************/

// bowikaxu - fin de impresion de empresa
$YPos= $Page_Height-$Top_Margin-212;
// TEXTO DE LA CARTA
	$text = "Por medio de la presente le informamos que su cuenta presenta un saldo vencido.";
	$text1 = "Solicitamos de la manera mas atenta pasar a liquidar a nuestras oficinas o a las cuentas de banco siguientes:";

$pdf->addText($Left_Margin,$YPos,$FontSize, $text);
$YPos -= ($line_height+$line_height);
$pdf->addText($Left_Margin,$YPos,$FontSize, $text1);
$YPos -= $line_height;

$sql = "SELECT rh_debtorsacts.accountcode, bankaccountname, bankaccountnumber, bankaddress 
	FROM bankaccounts, rh_debtorsacts WHERE bankaccounts.accountcode = rh_debtorsacts.accountcode
	AND rh_debtorsacts.debtorno = '".$_GET['DebtorNo']."'";
	$res4 = DB_query($sql,$db);
	$acc = "";
	while($accounts = DB_fetch_array($res4)){
		
		$acc = $accounts['bankaccountname'].' - '.$accounts['bankaccountnumber'];
		$pdf->addText($Left_Margin,$YPos,$FontSize, $acc);
		$YPos -= $line_height;
		
	}


$YPos -=(($numHeads+1)*$line_height);

/*Draw a rectangle to put the headings in     */
$pdf->line($Page_Width-$Right_Margin, $YPos-5,$Left_Margin, $YPos-5);
$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Left_Margin, $YPos+$line_height);
$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos-5);
$pdf->line($Left_Margin, $YPos+$line_height,$Left_Margin, $YPos-5);

/*set up the headings */
$Xpos = $Left_Margin+1;

$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,220 - $Left_Margin,$FontSize,_('Movement Details'),'centre');
$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,_('Balance'),'centre');
$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,_('Current'),'centre');
$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,_('Due Now'),'centre');
$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,'> ' . $_SESSION['PastDueDays1'] . ' ' . _('Days Over'),'centre');
$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,'> ' . $_SESSION['PastDueDays2'] . ' ' . _('Days Over'),'centre');

$YPos =$YPos - (2*$line_height);

?>
