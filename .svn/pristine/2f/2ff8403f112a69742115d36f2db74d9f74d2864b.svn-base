<?php
/* $Revision: 1.5 $ */
/* pdf-php by R&OS code to set up a new sales order page */
if ($PageNumber>1){
	$pdf->newPage();
}

$pdf->addTextWrap(10,750,600,11,$_SESSION['CompanyRecord']['coyname'],'center');

$XPos = 40;
$YPos = 720;

if($_POST['Location']!='All'){
	$sql = "SELECT rh_serie FROM locations WHERE loccode ".$_POST['Location']."";
	$ser_res = DB_query($sql,$db);
	$serie2 = DB_fetch_array($ser_res);
	$serie = $serie2['rh_serie'];
}else {
	$serie = '';
}

//$pdf->addText($XPos, $YPos,$FontSize, _('P&oacute;liza').' '._('Number').": ");
if($De == $Hasta){
	$concepto = _('Concepto').' Ventas de '.$De.' '.'Serie '.$serie;
}else {
	$concepto = _('Concepto').' Ventas de '.$De.' - '.$Hasta.' '.'Serie '.$serie;
}
$pdf->addText($XPos+10, $YPos,$FontSize, $concepto);

$XPos=450;
$YPos=710;

if (strlen($LeftOvers)>1){
	$LeftOvers = $pdf->addTextWrap($XPos,$YPos-14,170,$FontSize,$LeftOvers);
	if (strlen($LeftOvers)>1){
		$LeftOvers = $pdf->addTextWrap($XPos,$YPos-28,170,$FontSize,$LeftOvers);
		if (strlen($LeftOvers)>1){
			$LeftOvers = $pdf->addTextWrap($XPos,$YPos-42,170,$FontSize,$LeftOvers);
			if (strlen($LeftOvers)>1){
				$LeftOvers = $pdf->addTextWrap($XPos,$YPos-56,170,$FontSize,$LeftOvers);
			}
		}
	}
}

// NOMBRES COLUMNAS
	$LeftOvers = $pdf->addTextWrap(35,662,40,9,_('Account'),'left');
	
	$LeftOvers = $pdf->addTextWrap(80,662,40,9,_('C&oacute;digo'),'left');
	
	$LeftOvers = $pdf->addTextWrap(120,662,136,9,_('Nombre').'/'._('Concepto'),'left');
	$LeftOvers = $pdf->addTextWrap(150+110,662,48,9,_('Date'),'left');
	$LeftOvers = $pdf->addTextWrap(155+148,662,40,9,_('Invoice'),'left');
	$LeftOvers = $pdf->addTextWrap(153+196,662,40,9,_('N.C.'),'left');
	
	$LeftOvers = $pdf->addTextWrap(390,662,40,9,_('Parcial'),'right');
	
	$LeftOvers = $pdf->addTextWrap(430,662,80,9,_('Debit'),'right');
	$LeftOvers = $pdf->addTextWrap(500,662,80,9,_('Credit'),'right');

$YPos = 638;
$pdf->line(5,650,690,650);

?>
