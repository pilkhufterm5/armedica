<?php
/* $Revision: 1.12 $ */
/* March 2007 bowikaxu - Impresion de Remisiones Grandes */

/*
 * iJPe
 * 2010-03-20
 * Se realizo modificacion sobre el formato de impresion de la remision,
 * ya que la impresion se realizaria directamente desde el sistema sin formato fisico impreso
 *
 * Solicitado por Sergio
 */



if (!$FirstPage){ /* only initiate a new page if its not the first */
	$pdf->newPage();
}

//$pdf->addJpegFromFile('companies/'.$_SESSION['DatabaseName'].'/Rem1.jpg',0,0,612,792);


//Eleazar
//Realhost
//17-ago-2009
//Comente la siguiente linea para que no se muestre el numero de pedido.
//transno
//$pdf->addTextWrap(85.5,631,72,8,$myrow['transno'], 'left');

//fecha expedicion
//$pdf->addTextWrap(477,689.5,30,$FontSize,$myrow['dd'], 'center');
//$pdf->addTextWrap(513,689.5,30,$FontSize,$myrow['mm'], 'center');
//$pdf->addTextWrap(551,689.5,33,$FontSize,$myrow['yy'], 'center');


//LOGO
$pdf->addJpegFromFile('companies/' . $_SESSION['DatabaseName'] . '/logo.jpg',9,734,0,55);
$pdf->addJpegFromFile('companies/' . $_SESSION['DatabaseName'] . '/logo.jpg',9,340,0,55);

//LINEA DIVISION
$pdf->line(612, 396, 0, 396);

//ORDEN NUMERO
$pdf->addTextWrap(0,765,612,14,_('Remisi&oacute;n No.'). ' ' . $FromTransNo, 'center');
$pdf->addTextWrap(0,369,612,14,_('Remisi&oacute;n No.'). ' ' . $FromTransNo, 'center');

//NUMERO DE PAGINA
$pdf->addTextWrap(519,775,72,10,_('Page'). ': ' . $PageNumber, 'left');
$pdf->addTextWrap(519,379,72,10,_('Page'). ': ' . $PageNumber, 'left');

//COPIA
$pdf->addTextWrap(504,755,120,9,'COPIA EMPRESA', 'left');
$pdf->addTextWrap(504,359,120,9,'COPIA CLIENTE', 'left');

//FECHA
$pdf->addTextWrap(504,740,120,8,'FECHA', 'left');
$pdf->addTextWrap(504,730,120,8,$myrow['dd']."-".$myrow['mm']."-".$myrow['yy'], 'left');

$pdf->addTextWrap(504,344,120,8,'FECHA', 'left');
$pdf->addTextWrap(504,334,120,8,$myrow['dd']."-".$myrow['mm']."-".$myrow['yy'], 'left');

//ATENDIO
$pdf->addTextWrap(504,715,120,8,'ATENDI&Oacute;', 'left');
$pdf->addTextWrap(504,705,120,7,$myrow['salesmanname'], 'left');

$pdf->addTextWrap(504,319,120,8,'ATENDI&Oacute;', 'left');
$pdf->addTextWrap(504,309,120,7,$myrow['salesmanname'], 'left');

//ATENDIO
$pdf->addTextWrap(504,690,120,8,'ORDEN DE TRABAJO', 'left');

$pdf->addTextWrap(504,294,120,8,'ORDEN DE TRABAJO', 'left');

//$pdf->partEllipse(479,747,0,90,10,10);//Curva superior derecha
//$pdf->partEllipse(479,351,0,90,10,10);//Curva superior derecha
//$pdf->partEllipse(295,695,180,270,10,10);//Curva inferior derecha
//$pdf->partEllipse(295,299,180,270,10,10);//Curva inferior derecha
//$pdf->partEllipse(295,747,90,180,10,10);//Curva superior Izquierda
//$pdf->partEllipse(295,351,90,180,10,10);//Curva superior Izquierda
//$pdf->partEllipse(479,695,270,360,10,10);//Curva inferior Izquierda
//$pdf->partEllipse(479,299,270,360,10,10);//Curva inferior Izquierda
$pdf->line(500,685,590,685);//linea superior
$pdf->line(500,289,590,289);//linea superior
$pdf->line(500,675,590,675);//linea inferior
$pdf->line(500,279,590,279);//linea inferior
$pdf->line(590,685,590,675);//linea izquierda
$pdf->line(590,289,590,279);//linea izquierda
$pdf->line(500,685,500,675);//linea derecha
$pdf->line(500,289,500,279);//linea derecha

//DATOS EMPRESA
//$pdf->addTextWrap(13.5,739,216,9,$_SESSION['CompanyRecord']['coyname'], 'left');
//$pdf->addTextWrap(13.5,343,216,9,$_SESSION['CompanyRecord']['coyname'], 'left');
//$pdf->addTextWrap(13.5,727,216,9,$_SESSION['TaxAuthorityReferenceName'] . ' ' . $_SESSION['CompanyRecord']['gstno'], 'left');
//$pdf->addTextWrap(13.5,331,216,9,$_SESSION['TaxAuthorityReferenceName'] . ' ' . $_SESSION['CompanyRecord']['gstno'], 'left');
//$pdf->addTextWrap(13.5,715,216,9,$_SESSION['CompanyRecord']['regoffice1'].' '.$_SESSION['CompanyRecord']['regoffice2'], 'left');
//$pdf->addTextWrap(13.5,319,216,9,$_SESSION['CompanyRecord']['regoffice1'].' '.$_SESSION['CompanyRecord']['regoffice2'], 'left');
//$pdf->addTextWrap(13.5,703,216,9,$_SESSION['CompanyRecord']['regoffice3'].' '.$_SESSION['CompanyRecord']['regoffice4']. ' ' .$_SESSION['CompanyRecord']['regoffice5'], 'left');
//$pdf->addTextWrap(13.5,307,216,9,$_SESSION['CompanyRecord']['regoffice3'].' '.$_SESSION['CompanyRecord']['regoffice4']. ' ' .$_SESSION['CompanyRecord']['regoffice5'], 'left');
//$pdf->addTextWrap(13.5,691,216,9,_('Ph'). ': ' . $_SESSION['CompanyRecord']['telephone'] . ' ' ._('Fax').': ' . $_SESSION['CompanyRecord']['fax'], 'left');
//$pdf->addTextWrap(13.5,295,216,9,_('Ph'). ': ' . $_SESSION['CompanyRecord']['telephone'] . ' ' ._('Fax').': ' . $_SESSION['CompanyRecord']['fax'], 'left');
//$pdf->addTextWrap(13.5,679,216,9,_('Email'). ': ' . $_SESSION['CompanyRecord']['email'], 'left');
//$pdf->addTextWrap(13.5,283,216,9,_('Email'). ': ' . $_SESSION['CompanyRecord']['email'], 'left');

//SUCURSAL
$pdf->addTextWrap(13.5,725,216,9,"(".$myrow['loccode'].") ".utf8_decode($myrow['locationname']), 'left');
$pdf->addTextWrap(13.5,329,216,9,"(".$myrow['loccode'].") ".utf8_decode($myrow['locationname']), 'left');
$pdf->addTextWrap(13.5,713,216,9,utf8_decode($myrow['deladd1']) . ' ' . utf8_decode($myrow['deladd2']), 'left');
$pdf->addTextWrap(13.5,317,216,9,utf8_decode($myrow['deladd1']) . ' ' . utf8_decode($myrow['deladd2']), 'left');
$pdf->addTextWrap(13.5,701,216,9,utf8_decode($myrow['deladd3']) . ' ' . utf8_decode($myrow['deladd4']), 'left');
$pdf->addTextWrap(13.5,305,216,9,utf8_decode($myrow['deladd3']) . ' ' . utf8_decode($myrow['deladd4']), 'left');
$pdf->addTextWrap(13.5,689,216,9,"C.P. ".utf8_decode($myrow['deladd5']) . ' ' . utf8_decode($myrow['deladd6']), 'left');
$pdf->addTextWrap(13.5,293,216,9,"C.P. ".utf8_decode($myrow['deladd5']) . ' ' . utf8_decode($myrow['deladd6']), 'left');
$pdf->addTextWrap(13.5,677,216,9,_('Ph'). ': ' . $myrow['tel'] . ' ' ._('Fax').': ' . $myrow['fax'], 'left');
$pdf->addTextWrap(13.5,281,216,9,_('Ph'). ': ' . $myrow['tel'] . ' ' ._('Fax').': ' . $myrow['fax'], 'left');
$pdf->addTextWrap(13.5,665,216,9,_('Email'). ': ' . $myrow['email'], 'left');
$pdf->addTextWrap(13.5,269,216,9,_('Email'). ': ' . $myrow['email'], 'left');


//CLIENTE
$pdf->addTextWrap(299,739,180,9, _('Datos del Cliente') . ': '.$myrow['debtorno'], 'left');
$pdf->addTextWrap(299,343,180,9, _('Datos del Cliente') . ': '.$myrow['debtorno'], 'left');
$pdf->addTextWrap(299,727,180,9, utf8_decode($myrow['name']).' '.utf8_decode($myrow['name2']), 'left');
$pdf->addTextWrap(299,331,180,9, utf8_decode($myrow['name']).' '.utf8_decode($myrow['name2']), 'left');
$pdf->addTextWrap(299,715,180,9, utf8_decode($myrow['address1']). ' ' .utf8_decode($myrow['address2']), 'left');
$pdf->addTextWrap(299,319,180,9, utf8_decode($myrow['address1']). ' ' .utf8_decode($myrow['address2']), 'left');
$pdf->addTextWrap(299,703,180,9, utf8_decode($myrow['address3']). ' ' .utf8_decode($myrow['address4']), 'left');
$pdf->addTextWrap(299,307,180,9, utf8_decode($myrow['address3']). ' ' .utf8_decode($myrow['address4']), 'left');
$pdf->addTextWrap(299,691,180,9, utf8_decode($myrow['address5']). ' ' .utf8_decode($myrow['address6']), 'left');
$pdf->addTextWrap(299,295,180,9, utf8_decode($myrow['address5']). ' ' .utf8_decode($myrow['address6']), 'left');



//LINEAS
$pdf->partEllipse(479,747,0,90,10,10);//Curva superior derecha
$pdf->partEllipse(479,351,0,90,10,10);//Curva superior derecha
$pdf->partEllipse(295,695,180,270,10,10);//Curva inferior derecha
$pdf->partEllipse(295,299,180,270,10,10);//Curva inferior derecha
$pdf->partEllipse(295,747,90,180,10,10);//Curva superior Izquierda
$pdf->partEllipse(295,351,90,180,10,10);//Curva superior Izquierda
$pdf->partEllipse(479,695,270,360,10,10);//Curva inferior Izquierda
$pdf->partEllipse(479,299,270,360,10,10);//Curva inferior Izquierda
$pdf->line(295,757,479,757);//linea superior
$pdf->line(295,361,479,361);//linea superior
$pdf->line(295,685,479,685);//linea inferior
$pdf->line(295,289,479,289);//linea inferior
$pdf->line(285,695,285,747);//linea izquierda
$pdf->line(285,299,285,351);//linea izquierda
$pdf->line(489,695,489,747);//linea derecha
$pdf->line(489,299,489,351);//linea derecha

/*****ENCABEZADOS*****/
//CODIGO
$pdf->addTextWrap(0,645,90,8, _('Code'), 'center');
$pdf->addTextWrap(0,249,90,8, _('Code'), 'center');
//DESCRIPCION
$pdf->addTextWrap(48,645,216,8, _('Item Description'), 'center');
$pdf->addTextWrap(48,249,216,8, _('Item Description'), 'center');
//CANTIDAD
$pdf->addTextWrap(427,645,54,8, _('Quantity'), 'center');
$pdf->addTextWrap(427,249,54,8, _('Quantity'), 'center');
//UNIDAD
//$pdf->addTextWrap(382,645,36,8, _('Unit'), 'center');
//$pdf->addTextWrap(382,249,36,8, _('Unit'), 'center');
//FECHA
//$pdf->addTextWrap(420,645,54,8, _('Date Reqd'), 'center');
//$pdf->addTextWrap(420,249,54,8, _('Date Reqd'), 'center');
//PRECIO
$pdf->addTextWrap(485,645,54,8, _('Price'), 'center');
$pdf->addTextWrap(485,249,54,8, _('Price'), 'center');
//TOTAL
$pdf->addTextWrap(542,645,54,8, _('Total'), 'center');
$pdf->addTextWrap(542,249,54,8, _('Total'), 'center');
//LINEAS
$pdf->partEllipse(589.5,650,0,90,10,10);//Curva superior derecha
$pdf->partEllipse(589.5,254,0,90,10,10);//Curva superior derecha
$pdf->partEllipse(22.5,474,180,270,10,10);//Curva inferior Izquierda
$pdf->partEllipse(22.5,78,180,270,10,10);//Curva inferior Izquierda
$pdf->partEllipse(22.5,650,90,180,10,10);//Curva superior Izquierda
$pdf->partEllipse(22.5,254,90,180,10,10);//Curva superior Izquierda
$pdf->partEllipse(589.5,474,270,360,10,10);//Curva inferior derecha
$pdf->partEllipse(589.5,78,270,360,10,10);//Curva inferior derecha
$pdf->line(22.5,660,589.5,660);//linea superior
$pdf->line(22.5,264,589.5,264);//linea superior
$pdf->line(22.5,464,589.5,464);//linea inferior
$pdf->line(22.5,68,589.5,68);//linea inferior
$pdf->line(12.5,474,12.5,650);//linea izquierda
$pdf->line(12.5,78,12.5,254);//linea izquierda
$pdf->line(599.5,474,599.5,650);//linea derecha
$pdf->line(599.5,78,599.5,254);//linea derecha
$pdf->line(12.5,640,599.5,640);//linea abajo encabezados
$pdf->line(12.5,244,599.5,244);//linea abajo encabezados

$pdf->line(246,22,366,22);//linea firma
$pdf->line(246,418,366,418);//linea firma
$pdf->addTextWrap(246,10,120,10, 'Recibe', 'center');
$pdf->addTextWrap(246,406,120,10, 'Recibe', 'center');


//terminos
//$pdf->addTextWrap(477,635.5,108,8,$myrow['terms'], 'center');

//no cliente
//$pdf->addTextWrap(306,649,72,$FontSize,$myrow['debtorno'], 'left');

//Eleazar Lara
//Realhost
//17-Ago-2009
//referencia del cliente
//$pdf->addTextWrap(350,439,360,8,"Referencia del cliente: ".$myrow['customerref'], 'left');

//cliente
//$pdf->addTextWrap(85.5,680.5,360,$FontSize-1,$myrow['name'].' '.$myrow['name2'], 'left');

//direccion
//$pdf->addTextWrap(85.5,667,360,$FontSize,$myrow['address1'].' '.$myrow['address2'].' '.$myrow['address3'].' '.$myrow['address4'], 'left');//linea 1
//$pdf->addTextWrap(81,649,360,$FontSize,$myrow['address5'], 'left');//linea 2

//posicion en y de los articulos
$YPos = 638 -$line_height;

?>
