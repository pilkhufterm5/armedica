<?php
/* $Revision: 1.6 $ */
/* R&OS PHP-pdf class code to head up a new page */

if (!$FirstPage){ /* only initiate a new page if its not the first */
	$pdf->newPage();
}

$YPos = $Page_Height - $Top_Margin;

// variable ubicacion imagen de una factura
$DInvoice = 'companies/'.$DefaultCompany.'/factura.jpg';
if($PrintPDF=='Imprimir PDF (imagen)'){
	$pdf->addJpegFromFile($DInvoice,-2,-25,612,840);
}
/*
// Impresion de Cuadricula
$Hor = $Page_Width;
$Ver = $Page_Height;

// verticales
$pdf->line(1, 1,$Hor, 1);
$pdf->addText(1, 1, $FontSize, '(1,1)');
$pdf->line(1, 50,$Hor, 50);
$pdf->addText(1, 50, $FontSize, '(1,50)');
$pdf->line(1, 100,$Hor, 100);
$pdf->addText(1, 100, $FontSize, '(1,100)');
$pdf->line(1, 150,$Hor, 150);
$pdf->addText(1, 150, $FontSize, '(1,150)');
$pdf->line(1, 200,$Hor, 200);
$pdf->addText(1, 200, $FontSize, '(1,200)');
$pdf->addText(1, 250, $FontSize, '(1,250)');
$pdf->addText(1, 300, $FontSize, '(1,300)');
$pdf->addText(1, 350, $FontSize, '(1,350)');
$pdf->addText(1, 400, $FontSize, '(1,400)');
$pdf->addText(1, 450, $FontSize, '(1,450)');
$pdf->addText(1, 500, $FontSize, '(1,500)');
$pdf->addText(1, 550, $FontSize, '(1,550)');
$pdf->addText(1, 600, $FontSize, '(1,600)');
$pdf->addText(1, 650, $FontSize, '(1,650)');
$pdf->addText(1, 700, $FontSize, '(1,700)');
$pdf->addText(1, 750, $FontSize, '(1,750)');

// horizontales
$pdf->line(1, 1,1, $Ver);
$pdf->addText(1, 1, $FontSize, '(1,1)');
$pdf->line(50, 1,50, $Ver);
$pdf->addText(50, 1, $FontSize, '(50,1)');
$pdf->line(100, 1,100, $Ver);
$pdf->addText(100, 1, $FontSize, '(100,1)');
$pdf->line(150, 1,150, $Ver);
$pdf->addText(150, 1, $FontSize, '(150,1)');
$pdf->line(200, 1,200, $Ver);
$pdf->addText(200, 1, $FontSize, '(200,1)');
$pdf->line(250, 1,250, $Ver);
$pdf->addText(250, 1, $FontSize, '(250,1)');
$pdf->line(300, 1,300, $Ver);
$pdf->addText(300, 1, $FontSize, '(300,1)');
$pdf->line(350, 1,350, $Ver);
$pdf->addText(350, 1, $FontSize, '(350,1)');
$pdf->line(400, 1,400, $Ver);
$pdf->addText(400, 1, $FontSize, '(400,1)');
$pdf->line(450, 1,450, $Ver);
$pdf->addText(450, 1, $FontSize, '(450,1)');
$pdf->line(500, 1,500, $Ver);
$pdf->addText(500, 1, $FontSize, '(500,1)');
$pdf->line(550, 1,550, $Ver);
$pdf->addText(550, 1, $FontSize, '(550,1)');
$pdf->line(600, 1,600, $Ver);
$pdf->addText(600, 1, $FontSize, '(600,1)');

// Fin Impresion de Cuadricula
*/
//$pdf->addJpegFromFile('/home/rleal/Documents/fact309.jpg',10,610,125,65);
///$pdf->addJpegFromFile('logo.jpg',$Page_Width/2 -80,$YPos-40,125,65);
$FontSize =11.5;
/*
if ($InvOrCredit=='Invoice') {

        $pdf->addText($Page_Width - 200, $YPos, $FontSize, _('TAX INVOICE') . ' ');
} else {
	$pdf->addText($Page_Width - 200, $YPos, $FontSize, _('CREDIT NOTE') . ' ');
}
*/

$XPos = $Page_Width - 265;
$YPos -= 111;


$YPos = $Page_Height - $Top_Margin - 10;

$FontSize = 11.5;
///bowikaxu Feb 22 2007 cambio de coordenadas (reynagarza)

//$pdf->addText(525,675,14,$ExtRes['rh_serie'].$ExtRes['extinvoice'], 'center');

///$pdf->addText($Page_Width-268, $YPos-13, $FontSize, _('Number'));
///$pdf->addText($Page_Width-180, $YPos-13, $FontSize, $FromTransNo);
///$pdf->addText($Page_Width-268, $YPos-26, $FontSize, _('Customer Code'));
///$pdf->addText(520, 645, $FontSize, $myrow['debtorno'] . ' ' . _('Branch') . ' ' . $myrow['branchcode']);
///$pdf->addText(520, 645, $FontSize, $myrow['debtorno'], 'center' );
$pdf->addText(370,612,$FontSize,$myrow['locationname'], 'center' );
$pdf->addText(520,612,$FontSize,$myrow['salesmanname'], 'center');	
///$pdf->addText($Page_Width-268, $YPos-39, $FontSize, _('Date'));
///$pdf->addText($Page_Width-180, $YPos-39, $FontSize, ConvertSQLDate($myrow['trandate']));
///$pdf->addTextWrap(345,595,140,$FontSize,ConvertSQLDate($myrow['trandate']), 'center');	
$pdf->addTextWrap(348,657,20,$FontSize,$myrow['dd'], 'center');	
$pdf->addTextWrap(378,657,20,$FontSize,$myrow['mm'], 'center');	
$pdf->addTextWrap(408,657,20,$FontSize,$myrow['yy'], 'center');	


if ($InvOrCredit=='Invoice') {

///	$pdf->addText($Page_Width-268, $YPos-52, $FontSize, _('Order No'));
///	$pdf->addText($Page_Width-180, $YPos-52, $FontSize, $myrow['orderno']);
///	$pdf->addText($Page_Width-268, $YPos-65, $FontSize, _('Order Date'));
///	$pdf->addText($Page_Width-180, $YPos-65, $FontSize, ConvertSQLDate($myrow['orddate']));
///	$pdf->addText($Page_Width-268, $YPos-78, $FontSize, _('Dispatch Detail'));
///	$pdf->addText(320, 307, $FontSize, $myrow['shippername'] . '-' . $myrow['consignment'],'center');
///	$pdf->addText($Page_Width-268, $YPos-91, $FontSize, _('Dispatched From'));
///	$pdf->addText($Page_Width-180, $YPos-91, $FontSize, $myrow['locationname']);
}


///$pdf->addText($Page_Width-268, $YPos-104, $FontSize, _('Page'));
///$pdf->addText($Page_Width-180, $YPos-104, $FontSize, $PageNumber);

/*End of the text in the right side box */

/*Now print out the company name and address in the middle under the logo */
///$XPos = $Page_Width/2 -90;
////$YPos = $Page_Height - $Top_Margin-60;
///$pdf->addText($XPos, $YPos, $FontSize, $_SESSION['CompanyRecord']['coyname']);

///$FontSize=8;
///$pdf->addText($XPos, $YPos-10, $FontSize, $_SESSION['TaxAuthorityReferenceName'] . ': ' . $_SESSION['CompanyRecord']['gstno']);
///$pdf->addText($XPos, $YPos-20, $FontSize, $_SESSION['CompanyRecord']['postaladdress']);
///$pdf->addText($XPos, $YPos-30, $FontSize, $_SESSION['CompanyRecord']['regoffice1'] . ' ' . $_SESSION['CompanyRecord']['regoffice2']);
///$pdf->addText($XPos, $YPos-40, $FontSize, $_SESSION['CompanyRecord']['regoffice3']);
///$pdf->addText($XPos, $YPos-50, $FontSize, _('Phone') . ':' . $_SESSION['CompanyRecord']['telephone'] . ' ' . _('Fax') . ': ' . $_SESSION['CompanyRecord']['fax']);
///$pdf->addText($XPos, $YPos-60, $FontSize, _('Email') . ': ' . $_SESSION['CompanyRecord']['email']);

/*Now the customer charged to details top left */

$XPos = $Left_Margin;
///$YPos = $Page_Height - $Top_Margin;
///$pdf->addText($XPos, $YPos, $FontSize, _('Sold To') . ':');
$FontSize=11.5;
$XPos = 20;
$YPos = 660;
$minus = 12;
// taxref
if ($myrow['invaddrbranch']==0){
	$pdf->addText($XPos, $YPos, $FontSize, $myrow['name']);
	if(strlen($myrow['name2'])>=1){
		$pdf->addText($XPos, $YPos-$minus, $FontSize, $myrow['name2']);
		$minus += 12;
	}
	$pdf->addText($XPos, $YPos-$minus, $FontSize, $myrow['address1'].' '.$myrow['address2']);
		$minus += 12;
	$pdf->addText($XPos, $YPos-$minus, $FontSize, $myrow['address3'].' '.$myrow['address5']);
		$minus += 12;
	$pdf->addText($XPos, $YPos-$minus, $FontSize, $myrow['address4'].' '.$myrow['address6']);
		$minus += 12;
	$pdf->addText($XPos, $YPos-$minus, $FontSize, $myrow['taxref']);
		$minus += 12;
	$pdf->addText($XPos, $YPos-$minus, $FontSize, $myrow['brname'].' - '.$myrow['debtorno']);
	
} else {
	$pdf->addText($XPos, $YPos, $FontSize, $myrow['name']);
	if(strlen($myrow['name2'])>=1){
		$pdf->addText($XPos, $YPos-$minus, $FontSize, $myrow['name2']);
		$minus += 12;
	}
	$pdf->addText($XPos, $YPos-$minus, $FontSize, $myrow['braddress1'].' '.$myrow['braddress2']);
		$minus += 12;
	$pdf->addText($XPos, $YPos-$minus, $FontSize, $myrow['braddress3'].' '.$myrow['braddress5']);
		$minus += 12;
	$pdf->addText($XPos, $YPos-$minus, $FontSize, $myrow['braddress4'].' '.$myrow['braddress6'].'     '.$myrow['debtorno']);
		$minus += 12;
	$pdf->addText($XPos, $YPos-$minus, $FontSize, $myrow['taxref']);
		$minus += 12;
	$pdf->addText($XPos, $YPos-$minus, $FontSize, $myrow['brname']);
}

$XPos =200;
$YPos =685;

if ($InvOrCredit=='Invoice') {

	//$pdf->addText($XPos+50, $YPos, $FontSize, _('Entregar En:') . ':');
	//$XPos +=80;
	//$pdf->addText($XPos, $YPos-14, $FontSize, $myrow['deliverto']);
	//$pdf->addText($XPos, $YPos-28, $FontSize, $myrow['deladd1']);
	//$pdf->addText($XPos, $YPos-42, $FontSize, $myrow['deladd2']);
	//$pdf->addText($XPos, $YPos-56, $FontSize, $myrow['deladd3'] . ' ' . $myrow['deladd4']);
	
}
if ($InvOrCredit=='Credit'){
/* then its a credit note */

///	$pdf->addText($XPos, $YPos, $FontSize, _('Charge Branch') . ':');
///	$XPos +=80;
	//$pdf->addText($XPos, $YPos, $FontSize, $myrow['brname'].' '.$myrow['branchcode']);
	//$pdf->addText($XPos, $YPos-14, $FontSize, $myrow['braddress1']);
	//$pdf->addText($XPos, $YPos-28, $FontSize, $myrow['braddress2']);
	//$pdf->addText($XPos, $YPos-42, $FontSize, $myrow['braddress3'] . ' ' . $myrow['braddress4']);
	
}

// impresion de numero de orden, termino de pago y telefono
$XPos = 100;
$YPos = 563;
$pdf->addText($XPos, $YPos, $FontSize, $myrow['customerref']);
$pdf->addText(240, $YPos, $FontSize, $myrow['terms']);
$pdf->addText(450, $YPos, $FontSize, $myrow['phoneno']);
// fin impresion de numero de orden, termino de pago y telefono

?>
