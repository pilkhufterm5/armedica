<?php
/* $Revision: 1.6 $ */
/* R&OS PHP-pdf class code to head up a new page */


if (!$FirstPage){ /* only initiate a new page if its not the first */
	$pdf->newPage();
}

// variable ubicacion imagen de una factura
$DInvoice = 'companies/'.$_SESSION['DatabaseName'].'/Inv1.jpg';
if($PrintPDF=='Imprimir PDF (imagen)'){
	//Imagen Factura
	$pdf->addJpegFromFile($DInvoice,0,0,612,554);
	//Color no factura
	$pdf->SetTextColor(255,0,0);
	//5 digitos no factura
	$extInvoice = '';
	for($k=strlen($ExtRes['extinvoice']);$k<5;$k++){
		$extInvoice .= '0';
	}
	//no factura
	$pdf->addTextWrap(481.5,491,72,12,$extInvoice.$ExtRes['extinvoice'], 'right');
}
	//color normal
	$pdf->SetTextColor(0,0,0);

//5 digitos no factura
$extInvoice = '';
	for($k=strlen($ExtRes['extinvoice']);$k<5;$k++){
		$extInvoice .= '0';
	}

//transno
$pdf->addTextWrap(81,402,72,8,$FromTransNo, 'left');

//fecha expedicion
$pdf->addTextWrap(482,459.5,30,9,$myrow['dd'], 'right');
$pdf->addTextWrap(518,459.5,30,9,$myrow['mm'], 'right');
$pdf->addTextWrap(554,459.5,33,9,$myrow['yy'], 'right');

//lugar expedicion
//$pdf->addTextWrap(468,652.5,108,7,$myrow['locationname'], 'right');

//terminos
$pdf->addTextWrap(481.5,396.5,108,8,$myrow['terms'], 'center');

//no cliente
$pdf->addTextWrap(395,412.5,72,10,$myrow['debtorno'], 'left');

//cliente
$pdf->addTextWrap(81,453,360,8,$myrow['name'].' '.$myrow['name2'], 'left');

//direccion
$pdf->addTextWrap(81,439,360,8,$myrow['address1'].' '.$myrow['address2'].' '.$myrow['address3'], 'left');//linea 1
$pdf->addTextWrap(81,428,360,8,$myrow['address4'].' '.$myrow['address5'].' '.$myrow['address6'], 'left');//linea 2

//rfc
$pdf->addTextWrap(90,414.5,144,8,$myrow['taxref'], 'left');

//Vendedor
$pdf->addTextWrap(409.5,402,54,8,$myrow['salesmanname'], 'left');

//posicion en y de los articulos
$YPos = 374 -$line_height;
?>