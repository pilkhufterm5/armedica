<?php
/* $Revision: 1.13 $ */
/*
	R & OS PHP-PDF class code to set up a new page
	a new page is implicit on the establishment of a new pdf object so
	only for subsequent pages
*/

if ($PageNumber>1){
	$pdf->newPage();
}

//LOGO
//$pdf->addJpegFromFile('companies/' . $_SESSION['DatabaseName'] . '/logo.jpg',9,729,0,60);
//$pdf->addJpegFromFile('companies/' . $_SESSION['DatabaseName'] . '/logo.jpg',9,333,0,60);

//LINEA DIVISION
//$pdf->line(612, 396, 0, 396);

//ORDEN NUMERO
$TitlePO = utf8_decode("Orden de Compra Numero");
$pdf->addTextWrap(0,765,630,14,$TitlePO. ' ' . $OrderNo, 'center');
//$pdf->addTextWrap(0,369,612,14,_('Purchase Order Number'). ' ' . $OrderNo, 'center');

if($_SESSION['DatabaseName'] != "sme_erp_001"){
    $pdf->addJpegFromFile(dirname(__FILE__).'/../companies/' . $_SESSION['DatabaseName'] . '/logo.jpg',13.5,722,0,70);
}
//$pdf->addJpegFromFile(dirname(__FILE__).'/../companies/' . $_SESSION['DatabaseName'] . '/logo.jpg',13.5,722,0,70);


//NUMERO DE PAGINA
$pdf->addTextWrap(504,775,72,10,utf8_decode('Página'). ': ' . $PageNumber, 'left');
//$pdf->addTextWrap(504,379,72,10,_('Page'). ': ' . $PageNumber, 'left');


//DATOS EMPRESA    719,323
if($_SESSION['DatabaseName'] == "sme_erp_001"){
    $pdf->addTextWrap(13.5,725,216,12,utf8_decode($_SESSION['CompanyRecord']['coyname']), 'left');
}else{
    $pdf->addTextWrap(13.5,715,216,9,utf8_decode($_SESSION['CompanyRecord']['coyname']), 'left');
}

//$pdf->addTextWrap(13.5,323,216,9,$_SESSION['CompanyRecord']['coyname'], 'left');
//$pdf->addTextWrap(13.5,708,216,9,$_SESSION['TaxAuthorityReferenceName'] . ' ' . $_SESSION['CompanyRecord']['gstno'], 'left');
$pdf->addTextWrap(13.5,708,216,9,$_SESSION['CompanyRecord']['gstno'], 'left');
//$pdf->addTextWrap(13.5,314,216,9,$_SESSION['TaxAuthorityReferenceName'] . ' ' . $_SESSION['CompanyRecord']['gstno'], 'left');
$pdf->addTextWrap(13.5,698,216,9,$_SESSION['CompanyRecord']['regoffice1'].' '.$_SESSION['CompanyRecord']['regoffice2'], 'left');
//$pdf->addTextWrap(13.5,303,216,9,$_SESSION['CompanyRecord']['regoffice1'].' '.$_SESSION['CompanyRecord']['regoffice2'], 'left');
$pdf->addTextWrap(13.5,688,216,9,$_SESSION['CompanyRecord']['regoffice3'].' '.$_SESSION['CompanyRecord']['regoffice4']. ' ' .$_SESSION['CompanyRecord']['regoffice5'], 'left');
//$pdf->addTextWrap(13.5,293,216,9,$_SESSION['CompanyRecord']['regoffice3'].' '.$_SESSION['CompanyRecord']['regoffice4']. ' ' .$_SESSION['CompanyRecord']['regoffice5'], 'left');
$pdf->addTextWrap(13.5,678,216,9,_('Ph'). ': ' . $_SESSION['CompanyRecord']['telephone'] . ' ' ._('Fax').': ' . $_SESSION['CompanyRecord']['fax'], 'left');
//$pdf->addTextWrap(13.5,284,216,9,_('Ph'). ': ' . $_SESSION['CompanyRecord']['telephone'] . ' ' ._('Fax').': ' . $_SESSION['CompanyRecord']['fax'], 'left');
$pdf->addTextWrap(13.5,668,216,9,_('Email'). ': ' . $_SESSION['CompanyRecord']['email'], 'left');
//$pdf->addTextWrap(13.5,275,216,9,_('Email'). ': ' . $_SESSION['CompanyRecord']['email'], 'left');

$XValue = 5;
$YValue = -8;
//INICIADO POR
$pdf->addTextWrap(433,739,144,10, _('Initiator') . ': '.$POHeader['initiator'], 'left');
//$pdf->addTextWrap(280,255,144,10, _('Initiator') . ': '.$POHeader['initiator'], 'left');
//FECHA ORDEN
$pdf->addTextWrap(433,748,144,10, _('Order Date') . ': '.ConvertSQLDate($POHeader['orddate']), 'left');
//$pdf->addTextWrap(280,267,144,10, _('Order Date') . ': '.ConvertSQLDate($POHeader['orddate']), 'left');

//ATENCION A
$pdf->addTextWrap(433,730,180,10, utf8_decode('ATENCION A') . ':', 'left');
$pdf->addTextWrap(433,720,180,10, utf8_decode($POHeader['contact']), 'left');   ///ALMACEN

//ENTREGAR A
//$pdf->addTextWrap(280,719,144,10, _('Order Date') . ': '.ConvertSQLDate($POHeader['orddate']), 'left');
$pdf->addTextWrap(433,720 + $YValue,180,10, _('Deliver To') . ':', 'left');
//$pdf->addTextWrap(299,343,180,10, _('Deliver To') . ':', 'left');

if($_SESSION['DatabaseName'] == "armedica_erp_001"){
    
    $ErrMsg = _('There was a problem retrieving the purchase order header details for Order Number'). ' ' . $OrderNo .
			' ' . _('from the database');
    
    $direccion_entrega= "select * from rh_direcciones_entrega where loccode='".$POHeader['rh_location_entrega']."'";
								
		$result2=DB_query($direccion_entrega,$db, $ErrMsg);
		
		$POHeader2 = DB_fetch_array($result2);
    $pdf->addTextWrap(433,710 + $YValue,180,10, $POHeader2['locationname'], 'left');   ///ALMACEN ENTREGA SOLO armedica_erp_001
    
   $pdf->addTextWrap(433,698 + $YValue,180,10, $POHeader2['deladd1'].' '.$POHeader2['deladd2'], 'left');
//$pdf->addTextWrap(299,331,180,10, $POHeader['deladd1'].' '.$POHeader['deladd2'], 'left');
$pdf->addTextWrap(433,683 + $YValue,180,10, $POHeader2['deladd3']. ' ' .$POHeader2['deladd4'], 'left');
//$pdf->addTextWrap(299,319,180,10, $POHeader['deladd3']. ' ' .$POHeader['deladd4'], 'left');
$pdf->addTextWrap(433,671 + $YValue,180,10, $POHeader2['deladd5']. ' ' .$POHeader2['deladd6'], 'left');
//$pdf->addTextWrap(299,307,180,10, $POHeader['deladd5']. ' ' .$POHeader['deladd6'], 'left');
    
    
}else{
    $pdf->addTextWrap(433,710 + $YValue,180,10, $POHeader['intostocklocation'], 'left');   ///ALMACEN DEFAULT
$pdf->addTextWrap(433,698 + $YValue,180,10, $POHeader['deladd1'].' '.$POHeader['deladd2'], 'left');
//$pdf->addTextWrap(299,331,180,10, $POHeader['deladd1'].' '.$POHeader['deladd2'], 'left');
$pdf->addTextWrap(433,683 + $YValue,180,10, $POHeader['deladd3']. ' ' .$POHeader['deladd4'], 'left');
//$pdf->addTextWrap(299,319,180,10, $POHeader['deladd3']. ' ' .$POHeader['deladd4'], 'left');
$pdf->addTextWrap(433,671 + $YValue,180,10, $POHeader['deladd5']. ' ' .$POHeader['deladd6'], 'left');
//$pdf->addTextWrap(299,307,180,10, $POHeader['deladd5']. ' ' .$POHeader['deladd6'], 'left');
    
    
}




//LINEAS
//$pdf->partEllipse(479,747,0,90,10,10);//Curva superior derecha
//$pdf->partEllipse(479,351,0,90,10,10);//Curva superior derecha
//$pdf->partEllipse(295,695,180,270,10,10);//Curva inferior derecha
//$pdf->partEllipse(295,299,180,270,10,10);//Curva inferior derecha
//$pdf->partEllipse(295,747,90,180,10,10);//Curva superior Izquierda
//$pdf->partEllipse(295,351,90,180,10,10);//Curva superior Izquierda
//$pdf->partEllipse(479,695,270,360,10,10);//Curva inferior Izquierda
//$pdf->partEllipse(479,299,270,360,10,10);//Curva inferior Izquierda
//$pdf->line(295,757,479,757);//linea superior
//$pdf->line(295,361,479,361);//linea superior
//$pdf->line(295,685,479,685);//linea inferior
//$pdf->line(295,289,479,289);//linea inferior
//$pdf->line(285,695,285,747);//linea izquierda
//$pdf->line(285,299,285,351);//linea izquierda
//$pdf->line(489,695,489,747);//linea derecha
//$pdf->line(489,299,489,351);//linea derecha

//PROVEEDOR
//$pdf->addTextWrap(13.5,663,252,9, _('To').': ', 'left');
//$pdf->addTextWrap(13.5,267,252,9, _('To').': ', 'left');
$pdf->addTextWrap(13.5,651,252,9,  _('To')." ".$POHeader['supplierno'].' - '.$POHeader['suppname'], 'left');
//$pdf->addTextWrap(13.5,255,252,9, $POHeader['supplierno'].' - '.$POHeader['suppname'], 'left');
$pdf->addTextWrap(13.5,639,252,9, $POHeader['address1'].' '.$POHeader['address2'], 'left');
//$pdf->addTextWrap(13.5,243,252,9, $POHeader['address1'].' '.$POHeader['address2'], 'left');
$pdf->addTextWrap(13.5,627,252,9, $POHeader['address3'].' '.$POHeader['address4'], 'left');
//$pdf->addTextWrap(13.5,231,252,9, $POHeader['address3'].' '.$POHeader['address4'], 'left');



//INICIADO POR
$pdf->y = 135;
$pdf->x = 434;
$pdf->SetFontSize(8);
$pdf->MultiCell(258,8,_('Comments') . ': '.str_replace('\n',', ',str_replace('\r\n',', ',$POHeader['comments'])),0,'L',0,12);
/*$pdf->y = 505;
$pdf->x = 434;
$pdf->SetFontSize(8);
$pdf->MultiCell(258,8,_('Comments') . ': '.str_replace('\n',', ',str_replace('\r\n',', ',$POHeader['comments'])),0,'L',0,12);
 */
//MONEDA
$pdf->addTextWrap(13.5,611,288,10, _('All amounts stated in').' - ' . $POHeader['currcode'], 'left');
//$pdf->addTextWrap(13.5,215,288,10, _('All amounts stated in').' - ' . $POHeader['currcode'], 'left');

/*****ENCABEZADOS*****/

//CODIGO
$pdf->addTextWrap(-10,595,90,8, utf8_decode('Código'), 'center');
//$pdf->addTextWrap(16,199,90,8, _('Code'), 'center');

//DESCRIPCION
$pdf->addTextWrap(20,595,216,8, utf8_decode('Descripción'), 'center');
//$pdf->addTextWrap(108,199,216,8, _('Item Description'), 'center');

//CANTIDAD
$pdf->addTextWrap(276,595,54,8, utf8_decode('Cantidad'), 'center');
//$pdf->addTextWrap(326,199,54,8, _('Quantity'), 'center');

//UNIDAD
$pdf->addTextWrap(332,595,36,8, _('Unit'), 'center');
//$pdf->addTextWrap(382,199,36,8, _('Unit'), 'center');

//FECHA
$pdf->addTextWrap(370,595,54,8, _('Date Reqd'), 'center');
//$pdf->addTextWrap(420,199,54,8, _('Date Reqd'), 'center');

//PRECIO
$pdf->addTextWrap(426,595,54,8, _('Price'), 'center');
//$pdf->addTextWrap(476,199,54,8, _('Price'), 'center');

//TAX
$pdf->addTextWrap(482,595,54,8, _('Impuesto'), 'center');
//$pdf->addTextWrap(476,199,54,8, _('Price'), 'center');

//TOTAL
$pdf->addTextWrap(532,595,54,8, _('Importe'), 'center');
//$pdf->addTextWrap(532,199,54,8, _('Total'), 'center');

//LINEAS
$pdf->partEllipse(589.5,600,0,90,10,10);//Curva superior derecha
//$pdf->partEllipse(589.5,204,0,90,10,10);//Curva superior derecha
$pdf->partEllipse(22.5,130,180,270,10,10);//Curva inferior Izquierda
//$pdf->partEllipse(22.5,78,180,270,10,10);//Curva inferior Izquierda
$pdf->partEllipse(22.5,600,90,180,10,10);//Curva superior Izquierda
//$pdf->partEllipse(22.5,204,90,180,10,10);//Curva superior Izquierda
$pdf->partEllipse(589.5,130,270,360,10,10);//Curva inferior derecha
//$pdf->partEllipse(589.5,78,270,360,10,10);//Curva inferior derecha
$pdf->line(22.5,610,589.5,610);//linea superior
//$pdf->line(22.5,214,589.5,214);//linea superior
$pdf->line(22.5,120,589.5,120);//linea inferior 64
//$pdf->line(22.5,68,589.5,68);//linea inferior
$pdf->line(12.5,129,12.5,600);//linea izquierda
//$pdf->line(12.5,78,12.5,204);//linea izquierda
$pdf->line(599.5,129,599.5,600);//linea derecha
//$pdf->line(599.5,78,599.5,204);//linea derecha
$pdf->line(12.5,590,599.5,590);//linea abajo encabezados
//$pdf->line(12.5,194,599.5,194);//linea abajo encabezados

//NOTA
$TextNote = utf8_decode('NOTA: "En caso de no recibir comentarios sobre los precios y tiempos de entrega especificados en ésta Orden de Compra, se considerará que el proveedor está de acuerdo con ellos".');
$TextNote2 =utf8_decode('"Cada Factura deberá llevar Orden de Compra o Copia de la factura con sello de la empresa de recibo, de lo contrario no se programará su pago".
"Enviar Factura Electrónica a: gretta.valdes@armedica.com.mx"');
//$pdf->addTextWrap(16,100,400,8, "NOTA: {$TextNote}", 'left');

//$this->SetFont('Arial','',6);
$pdf->SetXY(16,680);
$pdf->multicell(400,7,$TextNote,0,'L');
$pdf->SetXY(16,700);
$pdf->multicell(440,7,$TextNote2,0,'L');
//$pdf->addTextWrap(16,54,288,8, 'NOTA: LAS FACTURAS DEBEN MOSTRAR EL NUMERO DE ESTA ORDEN PARA SER PAGADA', 'left');

//AUTORIZA
$pdf->line(246,30,366,30);//linea firma
//$pdf->line(246,22,366,22);//linea firma

$pdf->addTextWrap(246,26,120,10, $POHeader['rh_autoriza'], 'center');
//$pdf->addTextWrap(246,10,120,10, $POHeader['rh_autoriza'], 'center');



$YPos = 579;
$XPos = 13.5;

?>
