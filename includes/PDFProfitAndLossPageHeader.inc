<?php
	/* 
	 * PDF page header for the profit and loss report.
	 * Suren Naidu 28/08/2005
	 *
	 */
	 
	$PageNumber++;
	if ($PageNumber>1){
		$pdf->newPage();
	}

	$FontSize = 8;
	$YPos = $Page_Height - $Top_Margin;
	$pdf->addText($Left_Margin,$YPos,$FontSize,$_SESSION['CompanyRecord']['coyname']);

	// bowikaxu realhost March 2008 - traduccion, verificar
	$YPos -= $line_height;
	$FontSize =10;
	$pdf->selectFont('./fonts/Helvetica-Bold.afm');
	$Heading = _('Estado de Perdidad y Ganancias para los ') . $NumberOfMonths . _(' months to ') . $PeriodToDate;
	$pdf->addText($Left_Margin, $YPos, $FontSize, $Heading);

	$FontSize = 8;
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->addText($Page_Width-$Right_Margin-120,$YPos,$FontSize,
		_('Printed'). ': ' . Date($_SESSION['DefaultDateFormat'])
		. '   '. _('Page'). ' ' . $PageNumber);

	$YPos -= (2 * $line_height);
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize,_('Account Name'));
	//$LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,100,$FontSize,_('Account Name'));
	$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,_('Period Actual'),'right');
	// bowikaxu realhost March 2008 - show last month, not period budget
	$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,_('Month').' '._('Previous'),'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,_('Last Year'),'right');
	$YPos -= (2 * $line_height);
?>