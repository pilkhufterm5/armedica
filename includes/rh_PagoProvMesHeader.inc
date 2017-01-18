<?php
/* $Revision: 1.5 $ */
/* pdf-php by R&OS code to set up a new sales order page */
if ($PageNumber>1){
	$pdf->newPage();
}

$pdf->addTextWrap(10,750,600,11,$_SESSION['CompanyRecord']['coyname'],'center');

$XPos = 40;
$YPos = 720;

//$pdf->addText($XPos, $YPos,$FontSize, _('P&oacute;liza').' '._('Number').": ");

if($De == $Hasta){
				$concepto = _('Concepto').' Pago a Proveedores '.$De;
			}else {
				$concepto = _('Concepto').' Pago a Proveedores '.$De.' - '.$Hasta;
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
	$LeftOvers = $pdf->addTextWrap(35,662,50,9,_('Account'),'left');
	
	$LeftOvers = $pdf->addTextWrap(100,662,220,9,_('Concepto'),'left');
	//$LeftOvers = $pdf->addTextWrap(320,662,50,9,_(''),'left');
	//$LeftOvers = $pdf->addTextWrap(370,662,80,9,_(''),'left');
	
	$LeftOvers = $pdf->addTextWrap(430,662,80,9,_('Debit'),'right');
	$LeftOvers = $pdf->addTextWrap(500,662,80,9,_('Credit'),'right');

$YPos = 638;
$pdf->line(5,650,690,650);

?>
