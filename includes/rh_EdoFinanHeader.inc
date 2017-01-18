<?php
/* $Revision: 1.5 $ */
/* Estado de Poscici&oacute;n Financiera */
// bowikaxu realhost january 2008
if ($PageNumber>1){
	$pdf->newPage();
}

$pdf->addTextWrap(10,750,600,11,$_SESSION['CompanyRecord']['coyname'],'center');

$XPos = 40;
$YPos = 720;

//$pdf->addText($XPos, $YPos,$FontSize, _('P&oacute;liza').' '._('Number').": ");
$concepto = 'Estado de Poscici&oacute;n Financiera a '.$De."";
$pdf->addTextWrap(10,730,600,11,$concepto,'center');

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
/*
	$LeftOvers = $pdf->addTextWrap(35,662,50,9,_('Account'),'left');
	
	$LeftOvers = $pdf->addTextWrap(100,662,220,9,_('Concepto'),'left');
	//$LeftOvers = $pdf->addTextWrap(320,662,50,9,_(''),'left');
	$LeftOvers = $pdf->addTextWrap(360,662,70,9,_('Parcial'),'right');
	
	$LeftOvers = $pdf->addTextWrap(430,662,80,9,_('Debit'),'right');
	$LeftOvers = $pdf->addTextWrap(500,662,80,9,_('Credit'),'right');
	*/
$YPos = 700;
	$LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,_('Account Base'),'left');
	$LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,_('Account Group'),'left');
	$LeftOvers = $pdf->addTextWrap(335,$YPos,65,9,_($De),'right');
	$LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,'%','right');
	$LeftOvers = $pdf->addTextWrap(440,$YPos,65,9,_($LastYear),'right');
	$LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,'%','right');
	$YPos -= ($line_height*1.5);
$YPos = 690;
$pdf->line(5,700,690,700);

?>
