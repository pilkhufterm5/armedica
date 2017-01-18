<?php
	/* 
	 * PDF page header for the trial balance report.
	 * Suren Naidu 18/08/2005
	 *
	 *	Bowikaxu Realhost Nov. 07
	 *
	 *	Formato ISEGROUP
	 *
	 */
	 
	$PageNumber++;
	if ($PageNumber>1){
		$pdf->newPage();
	}

	$FontSize = 8;
	$YPos = $Page_Height - $Top_Margin;
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->addTextWrap(($Page_Height/2)-250,$YPos,300,$FontSize+1,$_SESSION['CompanyRecord']['coyname'],'center');
	$YPos -= $line_height;
	
	$pdf->addText($Page_Width-$Right_Margin-120,$YPos,$FontSize, _('Page'). ' ' . $PageNumber);
	$YPos -= $line_height;
	$pdf->addText($Page_Width-$Right_Margin-120,$YPos,$FontSize, _('Printed'). ': ' . Date($_SESSION['DefaultDateFormat']));

	
	$YPos -= $line_height;
	$FontSize =10;
	$pdf->selectFont('./fonts/Helvetica-Bold.afm');
	//$Heading = _('Trial Balance for the month of ') . $PeriodToDate . _(' and for the ') . $NumberOfMonths . _(' months to ') . $PeriodToDate;
	$Heading = _('Balanza de Comprobaci&oacute;n Detallada al ') . $LastDay.' de '.$PeriodToDate;
	
	$pdf->addText($Left_Margin, $YPos, $FontSize, $Heading);
	
	$YPos -= $line_height;
	$pdf->addText($Left_Margin, $YPos, $FontSize, _('Cuenta Inicial').': '.$_POST['FromAccount']);
	$YPos -= $line_height;
	$pdf->addText($Left_Margin, $YPos, $FontSize, _('Cuenta Final').': '.$_POST['ToAccount']);
	
	$YPos -= (2 * $line_height);
	$FontSize = 7;
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,_('Num. Cuenta'),'left');
	//$LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,50,$FontSize,_('Code'),'left');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+50,$YPos,100,$FontSize,_('Account Name'),'left');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+165,$YPos,100,$FontSize,_('Date').'  '._('Narrative'),'left');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos,70,$FontSize,_('Saldo Inicial'),'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+320,$YPos,70,$FontSize,_('Debit'),'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+390,$YPos,70,$FontSize,_('Credit'),'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+460,$YPos,70,$FontSize,_('Balance').' '._('Final'),'right');
	$pdf->selectFont('./fonts/Helvetica.afm');
	
	$YPos -= (2 * $line_height);
?>