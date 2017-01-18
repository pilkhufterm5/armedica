<?php
/* $Revision: 1.6 $ */
/* R&OS PHP-pdf class code to head up a new page */

/*
 * Variable para setear el tamaño de la letra
 */

if ($rh_tipo==10){
	$rh_fontsize = 9;
}else{
	$rh_fontsize = 8.5;
}

if (!$FirstPage){ /* only initiate a new page if its not the first */
	$pdf->newPage();
}

// variable ubicacion imagen de una factura
$DInvoice = 'companies/'.$_SESSION['DatabaseName'].'/Inv2.jpg';
if($PrintPDF=='Imprimir PDF (imagen)' && $rh_tipo!=20001){
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
	if ($rh_tipo!=20001){
		$pdf->addTextWrap(481.5,507,72,12,$extInvoice.$extCN, 'right');
	}else{
		$pdf->addTextWrap(481.5,507,72,12,$extInvoice.$ExtRes['extinvoice'], 'right');
	}
}
	//color normal
		$pdf->SetTextColor(0,0,0);

	//5 digitos no factura
	$extInvoice = '';
		for($k=strlen($ExtRes['extinvoice']);$k<5;$k++){
			$extInvoice .= '0';
		}


if ($rh_tipo==10)
{
	//Eleazar
	//Realhost
	//17-ago-2009
	//Comente la siguiente linea para que no despliegue el numero interno de la factura
	//transno
	//$pdf->addTextWrap(81,402,72,8,$FromTransNo, 'left');

	//fecha expedicion
	$pdf->addTextWrap(473,479.5-5,30,$rh_fontsize,$myrow['dd'], 'right');
	$pdf->addTextWrap(509,479.5-5,30,$rh_fontsize,$myrow['mm'], 'right');
	$pdf->addTextWrap(545,479.5-5,33,$rh_fontsize,$myrow['yy'], 'right');

	//lugar expedicion
	$pdf->addTextWrap(460,425,108,9,'Monterrey', 'right');

	//terminos
	//$pdf->addTextWrap(481.5,412.5,108,$rh_fontsize-1,$myrow['terms'], 'center');

	//no cliente
	//$pdf->addTextWrap(395,428.5,72,$rh_fontsize+1,$myrow['debtorno'], 'left');

	//cliente
	$pdf->addTextWrap(95,469-20-5,360,$rh_fontsize-1,$myrow['name'].' '.$myrow['name2'], 'left');

	/*
	 * iJPe
	 * realhost
	 * 07 Oct 2009
	 */
	$rh_unirDir = $myrow['address1'].' '.$myrow['address2'].' '.$myrow['address3'];

	if (strlen($rh_unirDir) > 50)
	{
		$rh_strtmpI = substr($rh_unirDir, 0, 50);
		$rh_strtmpF = substr($rh_unirDir, 50, 100);

		//direccion
		$pdf->addTextWrap(95,457-29-5,360,$rh_fontsize-1.5,$rh_strtmpI, 'left');//linea 1
		$pdf->addTextWrap(95,449-37-5,360,$rh_fontsize-1.5,$rh_strtmpF, 'left');//linea 2
		$pdf->addTextWrap(95,442-38-5,360,$rh_fontsize-1.5,$myrow['address4'].' '.$myrow['address5'].' C.P. '.$myrow['address6'], 'left');//linea 3
	}
	else
	{
		//direccion
		$pdf->addTextWrap(95,455-29-5,360,$rh_fontsize-1,$rh_unirDir, 'left');//linea 1
		$pdf->addTextWrap(95,444-38-5,360,$rh_fontsize-1,$myrow['address4'].' '.$myrow['address5'].' C.P. '.$myrow['address6'], 'left');//linea 2
	}

	//Eleazar Lara
	//Realhost
	//17-Ago-2009
	//referencia del cliente
	//$pdf->addTextWrap(350,455,360,$rh_fontsize-1,"Referencia del cliente: ".$myrow['customerref'], 'left');

	//rfc
	$pdf->addTextWrap(360,444-38-5,144,$rh_fontsize-1,$myrow['taxref'], 'left');

	//Vendedor
	//$pdf->addTextWrap(409.5,418,54,$rh_fontsize-1,$myrow['salesmanname'], 'left');

	//posicion en y de los articulos
	$YPos = 374 -$line_height;
}
else //IMPRESION DE NOTA DE CARGO
{
	//Eleazar
	//Realhost
	//17-ago-2009
	//Comente la siguiente linea para que no despliegue el numero interno de la factura
	//transno
	//$pdf->addTextWrap(81,402,72,8,$FromTransNo, 'left');

	//fecha expedicion
	$pdf->addTextWrap(478-36+10,475.5+8-32,30,$rh_fontsize,$myrow['dd'], 'right');
	$pdf->addTextWrap(514-36+10,475.5+8-32,30,$rh_fontsize,$myrow['mm'], 'right');
	$pdf->addTextWrap(550-36+10,475.5+8-32,33,$rh_fontsize,$myrow['yy'], 'right');

	//lugar expedicion
	//$pdf->addTextWrap(468,652.5,108,7,$myrow['locationname'], 'right');

	//terminos
	//$pdf->addTextWrap(481.5,412.5,108,$rh_fontsize-1,$myrow['terms'], 'center');

	//no cliente
	$pdf->addTextWrap(480+20,430-24,72,$rh_fontsize+1,$myrow['debtorno'], 'left');

	//cliente
	$pdf->addTextWrap(81+9,448,360,$rh_fontsize-1,$myrow['name'].' '.$myrow['name2'], 'left');

	/*
	 * iJPe
	 * realhost
	 * 07 Oct 2009
	 */
	$rh_unirDir = $myrow['address1'].' '.$myrow['address2'].' '.$myrow['address3'];

	if (strlen($rh_unirDir) > 50)
	{
		$rh_strtmpI = substr($rh_unirDir, 0, 50);
		$rh_strtmpF = substr($rh_unirDir, 50, 100);

		//direccion
		$pdf->addTextWrap(81+9,467-40,360,$rh_fontsize-1.5,$rh_strtmpI, 'left');//linea 1
		$pdf->addTextWrap(81+9,446-40,360,$rh_fontsize-1.5,$rh_strtmpF, 'left');//linea 2
		$pdf->addTextWrap(81+9,438-40,360,$rh_fontsize-1.5,$myrow['address4'].' '.$myrow['address5'].' C.P. '.$myrow['address6'], 'left');//linea 3
	}
	else
	{
		//direccion
		$pdf->addTextWrap(81+9,467-40,360,$rh_fontsize-1,$rh_unirDir, 'left');//linea 1
		$pdf->addTextWrap(81+9,446-40,360,$rh_fontsize-1,$myrow['address4'].' '.$myrow['address5'].' C.P. '.$myrow['address6'], 'left');//linea 2
	}

	//Eleazar Lara
	//Realhost
	//17-Ago-2009
	//referencia del cliente
	//$pdf->addTextWrap(350,455,360,$rh_fontsize-1,"Referencia del cliente: ".$myrow['customerref'], 'left');

	//rfc
	$pdf->addTextWrap(360-48,406,144,$rh_fontsize-1,$myrow['taxref'], 'left');

	//Vendedor
	//$pdf->addTextWrap(409.5,418,54,$rh_fontsize-1,$myrow['salesmanname'], 'left');

	//posicion en y de los articulos
	$YPos = 374 -$line_height;
}
?>