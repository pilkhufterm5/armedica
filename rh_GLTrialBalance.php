<?php

/* $Revision: 1.12 $ */

/*Through deviousness and cunning, this system allows trial balances for any date range that recalcuates the p & l balances
and shows the balance sheets as at the end of the period selected - so first off need to show the input of criteria screen
while the user is selecting the criteria the system is posting any unposted transactions */

$PageSecurity = 8;

include ('includes/session.inc');
$title = _('Balanza de Comprobaci&oacute;n Detallada');
include('includes/SQL_CommonFunctions.inc');
include('includes/AccountSectionsDef.inc'); //this reads in the Accounts Sections array

// bowikaxu realhost nov 07 - dont use from period
$_POST['ToPeriod']=$_POST['FromPeriod'];

// bowikaxu debug
//echo $_POST['FromPeriod']." - ".$_POST['ToPeriod']." - ".$_POST['SelectADifferentPeriod']."<BR>";

if ((!isset($_POST['FromPeriod']) AND !isset($_POST['ToPeriod'])) OR $_POST['SelectADifferentPeriod']==_('Select A Different Period') OR isset($_POST['Excel'])){
	include  ('includes/header.inc');
	echo '<FORM METHOD="POST" ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '">';
	
	if (Date('m') > $_SESSION['YearEnd']){
		/*Dates in SQL format */
		$DefaultFromDate = Date ('Y-m-d', Mktime(0,0,0,$_SESSION['YearEnd'] + 2,0,Date('Y')));
	} else {
		$DefaultFromDate = Date ('Y-m-d', Mktime(0,0,0,$_SESSION['YearEnd'] + 2,0,Date('Y')-1));
	}

	/*Show a form to allow input of criteria for TB to show */
	echo '<CENTER><TABLE><TR><TD>' . _('Select Period To:') . '</TD><TD><SELECT Name="FromPeriod">';
	$nextYear = date("Y-m-d",strtotime("+1 Year"));
	$sql = "SELECT periodno, lastdate_in_period FROM periods where lastdate_in_period < '$nextYear' ORDER BY periodno DESC";
	$Periods = DB_query($sql,$db);

	while ($myrow=DB_fetch_array($Periods,$db)){
		if(isset($_POST['FromPeriod']) AND $_POST['FromPeriod']!=''){
			if( $_POST['FromPeriod']== $myrow['periodno']){
				echo '<OPTION SELECTED VALUE="' . $myrow['periodno'] . '">' .MonthAndYearFromSQLDate($myrow['lastdate_in_period']);
			} else {
				echo '<OPTION VALUE="' . $myrow['periodno'] . '">' . MonthAndYearFromSQLDate($myrow['lastdate_in_period']);
			}
		} else {
			if($myrow['lastdate_in_period']==$DefaultFromDate){
				echo '<OPTION SELECTED VALUE="' . $myrow['periodno'] . '">' . MonthAndYearFromSQLDate($myrow['lastdate_in_period']);
			} else {
				echo '<OPTION VALUE="' . $myrow['periodno'] . '">' . MonthAndYearFromSQLDate($myrow['lastdate_in_period']);
			}
		}
	}
	echo '</SELECT></TD></TR>';
	// bowikaxu realhost nov 07 - desde cuenta
	echo '<TR><TD>' . _('Select Account From:') .'</TD><TD><SELECT Name="FromAccount">';

	$Accts = DB_query("SELECT accountcode, accountname FROM chartmaster ORDER BY accountcode ASC",$db);

	while ($myrow=DB_fetch_array($Accts,$db)){

		if($myrow['accountcode']==$_POST['FromAccount']){
			echo '<OPTION SELECTED VALUE="' . $myrow['accountcode'] . '">' . $myrow['accountcode'].' - '.$myrow['accountname'];
		} else {
			echo '<OPTION VALUE ="' . $myrow['accountcode'] . '">' . $myrow['accountcode'].' - '.$myrow['accountname'];
		}
	}
	echo '</SELECT></TD></TR>';
	
	// bowikaxu realhost nov 07 - hasta cuenta
	echo '<TR><TD>' . _('Select Account To:') .'</TD><TD><SELECT Name="ToAccount">';

	$Accts = DB_query("SELECT accountcode, accountname FROM chartmaster ORDER BY accountcode ASC",$db);

	while ($myrow=DB_fetch_array($Accts,$db)){

		if($myrow['accountcode']==$_POST['ToAccount']){
			echo '<OPTION SELECTED VALUE="' . $myrow['accountcode'] . '">' . $myrow['accountcode'].' - '.$myrow['accountname'];
		} else {
			echo '<OPTION VALUE ="' . $myrow['accountcode'] . '">' . $myrow['accountcode'].' - '.$myrow['accountname'];
		}
	}
	echo '</SELECT></TD></TR>';
	
	// bowikaxu realhost - filter results
	echo "<TR><TD>Saldo</TD><TD>
		<SELECT NAME='saldo'>";
	if($_POST['saldo']==1){
		echo "<OPTION SELECTED VALUE=1>"._("Con Movimientos y/o con Saldo")."
		<OPTION VALUE=0>"._("All Accounts")."";
	}else {
		echo "<OPTION VALUE=1>"._("Con Movimientos y/o con Saldo")."
		<OPTION SELECTED VALUE=0>"._("All Accounts")."";
	}
	echo "</TD></TR>";

	//echo '<INPUT TYPE=SUBMIT Name="ShowTB" Value="' . _('Show Trial Balance') .'"></CENTER>';
	echo "</TABLE><CENTER>
		<INPUT TYPE=SUBMIT Name='PrintPDF' Value='"._('PrintPDF')."'>
		<INPUT TYPE=SUBMIT Name='Excel' Value='"._('Show').' '._('Trial Balance').' '._('Detailed')."'>
		</CENTER>";

/*Now do the posting while the user is thinking about the period to select */
		//include ('includes/GLPostings.inc');
		
} else if (isset($_POST['PrintPDF'])) {
	
	include('includes/PDFStarter.php');
	$PageNumber = 0;
	$FontSize = 10;
	$pdf->addinfo('Title', _('Balanza de Comprobaci&oacute;n Detallada'));
	$pdf->addinfo('Subject', _('Balanza de Comprobaci&oacute;n Detallada'));
	$line_height = 12;
	$Bottom_Margin = 30;
	
	$NumberOfMonths = $_POST['ToPeriod'] - $_POST['FromPeriod'] + 1;

	$sql = 'SELECT lastdate_in_period, DAY(lastdate_in_period) AS day FROM periods WHERE periodno=' . $_POST['ToPeriod'];
	$PrdResult = DB_query($sql, $db);
	$myrow = DB_fetch_row($PrdResult);
	$LastDay = $myrow[1];
	$PeriodToDate = MonthAndYearFromSQLDate($myrow[0]);
	
	// bowikaxu realhost nov 07 - last period date
	$last_date = $myrow[0];
	
	$PeriodToDate = MonthAndYearFromSQLDate($myrow[0]);
	
	$RetainedEarningsAct = $_SESSION['CompanyRecord']['retainedearnings'];

	// bowikaxu realhost nov 2007 - filter initial and end account
	// bowikaxu reahost january 2008 - show only account with movements and balance
		$SQL = 'SELECT accountgroups.groupname,
			accountgroups.pandl,
			chartdetails.accountcode ,
			chartmaster.accountname,
			SUM(chartdetails.bfwd) AS fwdbalance,
			SUM(chartdetails.actual+chartdetails.bfwd) AS balance
		FROM chartmaster INNER JOIN accountgroups ON chartmaster.group_ = accountgroups.groupname
			INNER JOIN chartdetails ON chartmaster.accountcode= chartdetails.accountcode
		WHERE chartdetails.accountcode >= "'.$_POST['FromAccount'].'"
		AND chartdetails.accountcode <= "'.$_POST['ToAccount'].'"
		AND chartdetails.period = "'.$_POST['ToPeriod'].'"
		GROUP BY accountgroups.groupname,
				accountgroups.pandl,
				accountgroups.sequenceintb,
				chartdetails.accountcode,
				chartmaster.accountname
		ORDER BY chartdetails.accountcode,
			accountgroups.pandl desc,
			accountgroups.sequenceintb';
		
	$AccountsResult = DB_query($SQL,$db);
	if (DB_error_no($db) !=0) {
		$title = _('Trial Balance') . ' - ' . _('Problem Report') . '....';
		include('includes/header.inc');
		prnMsg( _('No general ledger accounts were returned by the SQL because') . ' - ' . DB_error_msg($db) );
		echo '<BR><A HREF="' .$rootpath .'/index.php?' . SID . '">'. _('Back to the menu'). '</A>';
		if ($debug==1){
			echo '<BR>'. $SQL;
		}
		include('includes/footer.inc');
		exit;
	}
	
	include('includes/rh_PDFTrialBalancePageHeader.inc');
	
	$j = 1;
	$ActGrp = '';

	$GrpActual = 0;
	$GrpBudget = 0;
	$GrpPrdActual = 0;
	$GrpPrdBudget = 0;
	
	// bowikaxu realhost nov 07
	$GrpDebe = 0;
	$GrpHaber = 0;
	$GrpFwd = 0;
	
	$TotFwd = 0;
	$TotDebe = 0;
	$TotHaber = 0;
	$TotSaldo = 0;

	while ($myrow=DB_fetch_array($AccountsResult)) {
		
		if ($myrow['groupname']!= $ActGrp){	
			// Print total at end of each account group
			
				// saldo anterior
				$sql = "SELECT SUM(gltrans.amount) AS amount
				FROM gltrans
				WHERE gltrans.periodno < '".$_POST['ToPeriod']."'
				AND gltrans.account IN (SELECT accountcode FROM chartmaster WHERE group_ ='".$myrow['groupname']."')";
				$fwbalance = DB_fetch_array(DB_query($sql,$db));
				// debe > 0
				$sql = "SELECT SUM(gltrans.amount) AS amount, chartmaster.group_
				FROM gltrans, chartmaster
				WHERE gltrans.periodno = '".$_POST['ToPeriod']."'
				AND gltrans.amount > 0
				AND chartmaster.group_ = '".$myrow['groupname']."'
				AND chartmaster.accountcode = gltrans.account
				GROUP BY chartmaster.group_";
				$debe = DB_fetch_array(DB_query($sql,$db));
				//AND gltrans.type = 10
				// haber < 0
				$sql = "SELECT SUM(gltrans.amount) AS amount, chartmaster.group_
				FROM gltrans, chartmaster
				WHERE gltrans.periodno = '".$_POST['ToPeriod']."'
				AND gltrans.amount < 0
				AND chartmaster.group_ = '".$myrow['groupname']."'
				AND chartmaster.accountcode = gltrans.account
				GROUP BY chartmaster.group_";
				$haber = DB_fetch_array(DB_query($sql,$db));
				
				//AND gltrans.type IN (11,12)
				//$pdf->line($Left_Margin+210, $YPos+$line_height,$Left_Margin+500, $YPos+$line_height);  
				$pdf->selectFont('./fonts/Helvetica-Bold.afm');
				
				//$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,_('Total'));
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,190,$FontSize,$ActGrp);
				if($_POST['saldo']==1 AND ($fwbalance['amount']!=0 OR $debe['amount']!=0 OR $haber['amount']!=0)){
					$YPos -= (1.5 * $line_height);
					$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos,70,9,number_format($fwbalance['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+320,$YPos,70,9,number_format($debe['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+390,$YPos,70,9,number_format($haber['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+460,$YPos,70,9,number_format(($debe['amount']+$haber['amount']+$fwbalance['amount']),2),'right');
				}else if ($_POST['saldo']==0){
					$YPos -= (1.5 * $line_height);
					$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos,70,9,number_format($fwbalance['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+320,$YPos,70,9,number_format($debe['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+390,$YPos,70,9,number_format($haber['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+460,$YPos,70,9,number_format(($debe['amount']+$haber['amount']+$fwbalance['amount']),2),'right');
				}else {
					// no hay saldos ni movimientos
				}
				//$pdf->line($Left_Margin+210, $YPos,$Left_Margin+500, $YPos);  /*Draw the bottom line */
				//$YPos -= (1 * $line_height);
				$pdf->selectFont('./fonts/Helvetica.afm');
				
				// bowikaxu realhost nov 07
				$GrpDebe = 0;
				$GrpHaber = 0;
				$GrpFwd = 0;
			
			$GrpActual = 0;
			$GrpBudget = 0;
			$GrpPrdActual = 0;
			$GrpPrdBudget = 0;
			

			// Print account group name
			$pdf->selectFont('./fonts/Helvetica-Bold.afm');
			$ActGrp = $myrow['groupname'];
			$FontSize = 10;
			
				$LeftOvers = $pdf->addTextWrap($Left_Margin+50,$YPos,120,$FontSize,$myrow['groupname']);
				$GrpYPos = $YPos;
				$pdf->selectFont('./fonts/Helvetica.afm');
				$YPos -= (1 * $line_height);
				$j++;
			$FontSize = 8;
		}
		
		// bowikaxu - get the account debe and haber
		$sql = "SELECT SUM(gltrans.amount) AS amount FROM gltrans 
				WHERE amount > 0
					AND account = '".$myrow['accountcode']."'
					AND periodno = '".$_POST['ToPeriod']."'";
		$debeAcct = DB_fetch_array(DB_query($sql,$db));
		
		$sql = "SELECT SUM(gltrans.amount) AS amount FROM gltrans 
				WHERE amount < 0
					AND account = '".$myrow['accountcode']."'
					AND periodno = '".$_POST['ToPeriod']."'";
		$haberAcct = DB_fetch_array(DB_query($sql,$db));
		
		// group totals
		$GrpActual +=$myrow['balance'];
		$GrpFwd +=$myrow['fwdbalance'];
		$GrpDebe += $debeAcct['amount'];
		$GrpHaber += $haberAcct['amount'];
		
		// final totals
		$TotFwd += $myrow['fwdbalance'];
		$TotDebe += $debeAcct['amount'];
		$TotHaber += $haberAcct['amount'];
		$TotSaldo += $myrow['balance'];
		
		// Print heading if at end of page
		if ($YPos <= ($Bottom_Margin)){
			include('includes/rh_PDFTrialBalancePageHeader.inc');
			//$YPos -= (2 * $line_height);
		}

		// Print total for each account
		if($_POST['saldo']==1 AND ($myrow['fwdbalance']!=0 OR $debeAcct['amount']!=0 OR $haberAcct['amount']!=0 OR $myrow['balance'])){
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,30,$FontSize,$myrow['accountcode'],'left');
			//$LeftOvers = $pdf->addTextWrap($Left_Margin+30,$YPos,50,$FontSize,'','left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+50,$YPos,120,$FontSize,$myrow['accountname'],'left');
			
			$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos,70,$FontSize,number_format($myrow['fwdbalance'],2),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+320,$YPos,70,$FontSize,number_format($debeAcct['amount'],2),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+390,$YPos,70,$FontSize,number_format($haberAcct['amount'],2),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+460,$YPos,70,$FontSize,number_format($myrow['balance'],2),'right');
			$YPos -= $line_height;
		}else if($_POST['saldo']==0){
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,30,$FontSize,$myrow['accountcode'],'left');
			//$LeftOvers = $pdf->addTextWrap($Left_Margin+30,$YPos,50,$FontSize,'','left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+50,$YPos,120,$FontSize,$myrow['accountname'],'left');
			
			$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos,70,$FontSize,number_format($myrow['fwdbalance'],2),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+320,$YPos,70,$FontSize,number_format($debeAcct['amount'],2),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+390,$YPos,70,$FontSize,number_format($haberAcct['amount'],2),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+460,$YPos,70,$FontSize,number_format($myrow['balance'],2),'right');
			$YPos -= $line_height;
		}else {
			// no hay saldos ni movimientos
		}
		// bowikaxu realhost Feb 2008 - Get Details
		
		// bowikaxu realhost nov 2007 - END DEBTORS
		
		// debtors list and initial balance
			$sql = "SELECT gltrans.*, systypes.typename
				FROM gltrans, systypes
				WHERE gltrans.periodno <= '".$_POST['ToPeriod']."'
				AND gltrans.periodno >= '".$_POST['FromPeriod']."'
				AND systypes.typeid = gltrans.type
				AND gltrans.account ='".$myrow['accountcode']."'
				ORDER BY gltrans.trandate ASC";
			
			$res = DB_query($sql,$db);
			//$YPos -= ($line_height*.5);
			while($debtor = DB_fetch_array($res)){
				
				$LeftOvers = $pdf->addTextWrap($Left_Margin+165,$YPos,150,$FontSize,$debtor['trandate'].' '.$debtor['typename'].' '.$debtor['typeno'],'left');
				if($debtor['amount']>0){
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos,70,$FontSize,number_format($debe['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+320,$YPos,70,$FontSize,number_format($debtor['amount'],2),'right');
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+390,$YPos,70,$FontSize,number_format($haberAcct['amount'],2),'right');
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+460,$YPos,70,$FontSize,number_format($myrow['balance'],2),'right');
				}else {
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos,70,$FontSize,number_format($debe['amount'],2),'right');
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+320,$YPos,70,$FontSize,number_format($debeAcct['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+390,$YPos,70,$FontSize,number_format($debtor['amount'],2),'right');
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+460,$YPos,70,$FontSize,number_format($myrow['balance'],2),'right');
				}
				$YPos -= $line_height;
			}
			if(DB_num_rows($res)){
				$YPos -= ($line_height*.5);
			}
	}  //end of while loop
	
	if ($YPos < ($Bottom_Margin+15)){
		include('includes/rh_PDFTrialBalancePageHeader.inc');
		//$YPos -= (2 * $line_height);
	}
	
	$YPos -= (2 * $line_height);
	$pdf->line($Left_Margin+260, $YPos+$line_height,$Left_Margin+550, $YPos+$line_height);  
	$FontSize += 1;
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,_('Total'));
	$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos,70,$FontSize,number_format($TotFwd,2),'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+320,$YPos,70,$FontSize,number_format(abs($TotDebe),2),'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+390,$YPos,70,$FontSize,number_format(abs($TotHaber),2),'right');
	 
	if($TotSaldo > $rh_umbral_asignacion AND $TotSaldo < (-1*$rh_umbral_asignacion)){ // dentro del umbral
		$LeftOvers = $pdf->addTextWrap($Left_Margin+460,$YPos,70,$FontSize,number_format($TotSaldo,2),'right');
	}else { // fuera del umbral
		$LeftOvers = $pdf->addTextWrap($Left_Margin+460,$YPos,70,$FontSize,number_format(0,2),'right');
	}
	
	$pdf->line($Left_Margin+260, $YPos,$Left_Margin+550, $YPos);  
	
	$pdfcode = $pdf->output();
	$len = strlen($pdfcode);
	
	if ($len<=20){
		$title = _('Print Trial Balance Error');
		include('includes/header.inc');
		echo '<p>';
		prnMsg( _('There were no entries to print out for the selections specified') );
		echo '<BR><A HREF="'. $rootpath.'/index.php?' . SID . '">'. _('Back to the menu'). '</A>';
		include('includes/footer.inc');
		exit;
	} else {
		header('Content-type: application/pdf');
		header('Content-Length: ' . $len);
		header('Content-Disposition: inline; filename=rh_TrialBalance.pdf');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');

		$pdf->Stream();

	}
	exit;
} 

if (isset($_POST['Excel'])) {
	//-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/ bowikaxu - balanza de comprobacion excel /-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/
	
	$NumberOfMonths = $_POST['ToPeriod'] - $_POST['FromPeriod'] + 1;

	$sql = 'SELECT lastdate_in_period, DAY(lastdate_in_period) AS day FROM periods WHERE periodno=' . $_POST['ToPeriod'];
	$PrdResult = DB_query($sql, $db);
	$myrow = DB_fetch_row($PrdResult);
	$LastDay = $myrow[1];
	$PeriodToDate = MonthAndYearFromSQLDate($myrow[0]);
	
	// bowikaxu realhost nov 07 - last period date
	$last_date = $myrow[0];
	
	$PeriodToDate = MonthAndYearFromSQLDate($myrow[0]);
	
	$RetainedEarningsAct = $_SESSION['CompanyRecord']['retainedearnings'];

	// bowikaxu realhost nov 2007 - filter initial and end account
	// bowikaxu realhost january 2008 - show accounts with movements and balance
	 // todas las cuentas
		$SQL = 'SELECT accountgroups.groupname,
			accountgroups.pandl,
			chartdetails.accountcode ,
			chartmaster.accountname,
			SUM(chartdetails.bfwd) AS fwdbalance,
			SUM(chartdetails.actual+chartdetails.bfwd) AS balance
		FROM chartmaster INNER JOIN accountgroups ON chartmaster.group_ = accountgroups.groupname
			INNER JOIN chartdetails ON chartmaster.accountcode= chartdetails.accountcode
		WHERE chartdetails.accountcode >= "'.$_POST['FromAccount'].'"
		AND chartdetails.accountcode <= "'.$_POST['ToAccount'].'"
		AND chartdetails.period = "'.$_POST['ToPeriod'].'"
		GROUP BY accountgroups.groupname,
				accountgroups.pandl,
				accountgroups.sequenceintb,
				chartdetails.accountcode,
				chartmaster.accountname
		ORDER BY chartdetails.accountcode,
			accountgroups.pandl desc,
			accountgroups.sequenceintb';

	$AccountsResult = DB_query($SQL,$db);
	if (DB_error_no($db) !=0) {
		prnMsg( _('No general ledger accounts were returned by the SQL because') . ' - ' . DB_error_msg($db) );
		echo '<BR><A HREF="' .$rootpath .'/index.php?' . SID . '">'. _('Back to the menu'). '</A>';
		if ($debug==1){
			echo '<BR>'. $SQL;
		}
		include('includes/footer.inc');
		exit;
	}
	$TableHeader = "<TR><TD class='tableheader'>"._('Account')."</TD>
					<TD class='tableheader'>"._('Account Name')."</TD>
					<TD class='tableheader'>"._('Date')."</TD>
					<TD class='tableheader'>"._('Narrative')."</TD>
					<TD class='tableheader'>"._('Saldo Inicial')."</TD>
					<TD class='tableheader'>"._('Debit')."</TD>
					<TD class='tableheader'>"._('Credit')."</TD>
					<TD class='tableheader'>"._('Balance').' '._('Final')."</TD></TR>";
	
	// HEADERS
	echo "<BR><CENTER>
			<B><H2>".$_SESSION['CompanyRecord']['coyname'].
			"<BR>"._('Balanza de Comprobaci&oacute;n Detallada al ') . $LastDay.' de '.$PeriodToDate."</H2>".
			"<BR>"._('Cuenta Inicial').': '.$_POST['FromAccount']."<BR>"._('Cuenta Final').': '.$_POST['ToAccount'].
			"<TABLE>";
	// FIN HEADERS
	echo $TableHeader;	
	
	$j = 1;
	$ActGrp = '';

	$GrpActual = 0;
	$GrpBudget = 0;
	$GrpPrdActual = 0;
	$GrpPrdBudget = 0;
	
	// bowikaxu realhost nov 07
	$GrpDebe = 0;
	$GrpHaber = 0;
	$GrpFwd = 0;
	
	$TotFwd = 0;
	$TotDebe = 0;
	$TotHaber = 0;
	$TotSaldo = 0;

	while ($myrow=DB_fetch_array($AccountsResult)) {
		
		if ($myrow['groupname']!= $ActGrp){	
			// Print total at end of each account group
			echo "<TR><TD COLSPAN=7 HEIGHT=20 BGCOLOR=white></TD></TR>";
				// saldo anterior
				$sql = "SELECT SUM(gltrans.amount) AS amount
				FROM gltrans
				WHERE gltrans.periodno < '".$_POST['ToPeriod']."'
				AND gltrans.account IN (SELECT accountcode FROM chartmaster WHERE group_ ='".$myrow['groupname']."')";
				$fwbalance = DB_fetch_array(DB_query($sql,$db));
				// debe > 0
				$sql = "SELECT SUM(gltrans.amount) AS amount, chartmaster.group_
				FROM gltrans, chartmaster
				WHERE gltrans.periodno = '".$_POST['ToPeriod']."'
				AND gltrans.amount > 0
				AND chartmaster.group_ = '".$myrow['groupname']."'
				AND chartmaster.accountcode = gltrans.account
				GROUP BY chartmaster.group_";
				$debe = DB_fetch_array(DB_query($sql,$db));
				//AND gltrans.type = 10
				// haber < 0
				$sql = "SELECT SUM(gltrans.amount) AS amount, chartmaster.group_
				FROM gltrans, chartmaster
				WHERE gltrans.periodno = '".$_POST['ToPeriod']."'
				AND gltrans.amount < 0
				AND chartmaster.group_ = '".$myrow['groupname']."'
				AND chartmaster.accountcode = gltrans.account
				GROUP BY chartmaster.group_";
				$haber = DB_fetch_array(DB_query($sql,$db));
				//$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,_('Total'));
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,190,$FontSize,$ActGrp);
				if($_POST['saldo']==1 AND($fwbalance['amount']!=0 OR $debe['amount']!=0 OR $haber['amount']!=0)){
					
					if ($k==1){
						echo "<tr bgcolor='#CCCCCC'>";
						$k=0;
					} else {
						echo "<tr bgcolor='#EEEEEE'>";
						$k++;
					}
						printf("<td ALIGN=LEFT><B>%s</B></td>
							<td ALIGN=LEFT><B>%s</B></td>
							<td ALIGN=LEFT><B>%s</B></td>
							<td ALIGN=LEFT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							</tr>",
							'',
							$myrow['groupname'],
							'',
							'',
							number_format($fwbalance['amount'],2),
							number_format($debe['amount'],2),
							number_format($haber['amount'],2),
							number_format(($debe['amount']+$haber['amount']+$fwbalance['amount']),2));	
							$j++;
				}else if($_POST['saldo']==0){
					if ($k==1){
						echo "<tr bgcolor='#CCCCCC'>";
						$k=0;
					} else {
						echo "<tr bgcolor='#EEEEEE'>";
						$k++;
					}
						printf("<td ALIGN=LEFT><B>%s</B></td>
							<td ALIGN=LEFT><B>%s</B></td>
							<td ALIGN=LEFT><B>%s</B></td>
							<td ALIGN=LEFT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							</tr>",
							'',
							$myrow['groupname'],
							'',
							'',
							number_format($fwbalance['amount'],2),
							number_format($debe['amount'],2),
							number_format($haber['amount'],2),
							number_format(($debe['amount']+$haber['amount']+$fwbalance['amount']),2));	
							$j++;
				}else {
					// no hay saldo ni movimientos
				}
				// bowikaxu realhost nov 07
			$GrpDebe = 0;
			$GrpHaber = 0;
			$GrpFwd = 0;
			
			$GrpActual = 0;
			$GrpBudget = 0;
			$GrpPrdActual = 0;
			$GrpPrdBudget = 0;

			$ActGrp = $myrow['groupname'];
		}
		
		// bowikaxu - get the account debe and haber
		$sql = "SELECT SUM(gltrans.amount) AS amount FROM gltrans 
				WHERE amount > 0
					AND account = '".$myrow['accountcode']."'
					AND periodno = '".$_POST['ToPeriod']."'";
		$debeAcct = DB_fetch_array(DB_query($sql,$db));
		
		$sql = "SELECT SUM(gltrans.amount) AS amount FROM gltrans 
				WHERE amount < 0
					AND account = '".$myrow['accountcode']."'
					AND periodno = '".$_POST['ToPeriod']."'";
		$haberAcct = DB_fetch_array(DB_query($sql,$db));
		
		// group totals
		$GrpActual +=$myrow['balance'];
		$GrpFwd +=$myrow['fwdbalance'];
		$GrpDebe += $debeAcct['amount'];
		$GrpHaber += $haberAcct['amount'];
		
		// final totals
		$TotFwd += $myrow['fwdbalance'];
		$TotDebe += $debeAcct['amount'];
		$TotHaber += $haberAcct['amount'];
		$TotSaldo += $myrow['balance'];
		
		// Print total for each account
		//if($_POST['saldo']==0 || ($_POST['saldo'] && ($debe['amount']!=0 || $haber['amount']!=0))){
		if($_POST['saldo']==1 AND($myrow['fwdbalance']!=0 OR $debeAcct['amount']!=0 OR $haberAcct['amount']!=0 OR $myrow['balance']!=0)){
					if ($k==1){
						echo "<tr bgcolor='#CCCCCC'>";
						$k=0;
					} else {
						echo "<tr bgcolor='#EEEEEE'>";
						$k++;
					}
					
					printf("<td ALIGN=LEFT><a href='GLAccountInquiry.php?".SID."&Account=%s&Period=%s'>%s</a></td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							</tr>",
							$myrow['accountcode'],
							$_POST['ToPeriod'],
							$myrow['accountcode'],
							$myrow['accountname'],
							'',
							'',
							number_format($myrow['fwdbalance'],2),
							number_format($debeAcct['amount'],2),
							number_format($haberAcct['amount'],2),
							number_format($myrow['balance'],2));
		}else if ($_POST['saldo']==0){
			if ($k==1){
						echo "<tr bgcolor='#CCCCCC'>";
						$k=0;
					} else {
						echo "<tr bgcolor='#EEEEEE'>";
						$k++;
					}
					
					printf("<td ALIGN=LEFT><a href='GLAccountInquiry.php?".SID."&Account=%s&Period=%s'>%s</a></td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							</tr>",
							$myrow['accountcode'],
							$_POST['ToPeriod'],
							$myrow['accountcode'],
							$myrow['accountname'],
							'',
							'',
							number_format($myrow['fwdbalance'],2),
							number_format($debeAcct['amount'],2),
							number_format($haberAcct['amount'],2),
							number_format($myrow['balance'],2));
		}else {
			// no hay saldo ni movimientos
		}
		// bowikaxu realhost Feb 2008 - Details
		//if($myrow['accountcode']==$_SESSION['CompanyRecord']['debtorsact']){ // yes, it is the debtors account, so print debtors balance
			
			// debtors list and initial balance
			$sql = "SELECT gltrans.*, systypes.typename
				FROM gltrans, systypes
				WHERE gltrans.periodno <= '".$_POST['ToPeriod']."'
				AND gltrans.periodno >= '".$_POST['FromPeriod']."'
				AND systypes.typeid = gltrans.type
				AND gltrans.account ='".$myrow['accountcode']."'
				ORDER BY gltrans.trandate ASC";
			
			$res = DB_query($sql,$db);
			while($debtor = DB_fetch_array($res)){
				
					if ($k==1){
						echo "<tr bgcolor='#CCCCCC'>";
						$k=0;
					} else {
						echo "<tr bgcolor='#EEEEEE'>";
						$k++;
					}
					
					if($debtor['amount']>=0){
						printf("<td ALIGN=LEFT>%s</td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							</tr>",
							'',
							'',
							$debtor['trandate'],
							$debtor['typename'].' <a href="GLTransInquiry.php?'.SID.'&TypeID='.$debtor['type'].'&TransNo='.$debtor['typeno'].'">'.$debtor['typeno'].'</a>',
							'',
							number_format($debtor['amount'],2),
							'',
							'');
					}else {
						printf("<td ALIGN=LEFT>%s</td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							</tr>",
							'',
							'',
							$debtor['trandate'],
							$debtor['typename'].' <a href="GLTransInquiry.php?'.SID.'&TypeID='.$debtor['type'].'&TransNo='.$debtor['typeno'].'">'.$debtor['typeno'].'</a>',
							'',
							'',
							number_format($debtor['amount'],2),
							'');
					}
					
				// Print heading if at end of page
			}
			DB_free_result($res);
		//}
		// bowikaxu realhost Feb 2008 - Details
		
		// bowikaxu realhost nov 2007 - START SUPPLIERS
		/*
		if($myrow['accountcode']==$SelectedAccount = $_SESSION['CompanyRecord']['creditorsact']){
			
			// debtors list and initial balance
			$sql = "SELECT SUM(gltrans.amount) AS balance, supptrans.supplierno, suppliers.suppname 
				FROM suppliers, gltrans INNER JOIN supptrans ON gltrans.type = supptrans.type AND gltrans.typeno = supptrans.transno
				WHERE suppliers.supplierid = supptrans.supplierno
				AND gltrans.periodno <= '".$_POST['ToPeriod']."'
				AND gltrans.account ='".$_SESSION['CompanyRecord']['creditorsact']."'
				GROUP BY supptrans.supplierno
				ORDER BY supptrans.supplierno ASC";
			
			$res = DB_query($sql,$db);
			while($supplier = DB_fetch_array($res)){
				
				// saldo anterior
				$sql = "SELECT SUM(gltrans.amount) AS amount, supptrans.supplierno
				FROM gltrans INNER JOIN supptrans ON gltrans.type = supptrans.type AND gltrans.typeno = supptrans.transno
				WHERE gltrans.periodno < '".$_POST['ToPeriod']."'
				AND supptrans.supplierno = '".$supplier['supplierno']."'
				AND gltrans.account ='".$_SESSION['CompanyRecord']['creditorsact']."'
				GROUP BY supptrans.supplierno";
				$fwbalance = DB_fetch_array(DB_query($sql,$db));
				// debe > 0
				$sql = "SELECT SUM(gltrans.amount) AS amount, supptrans.supplierno
				FROM gltrans INNER JOIN supptrans ON gltrans.type = supptrans.type AND gltrans.typeno = supptrans.transno
				WHERE gltrans.periodno = '".$_POST['ToPeriod']."'
				AND supptrans.supplierno = '".$supplier['supplierno']."'
				AND gltrans.amount > 0
				AND gltrans.account ='".$_SESSION['CompanyRecord']['creditorsact']."'
				GROUP BY supptrans.supplierno";
				$debe = DB_fetch_array(DB_query($sql,$db));
				// haber < 0
				$sql = "SELECT SUM(gltrans.amount) AS amount, supptrans.supplierno
				FROM gltrans INNER JOIN supptrans ON gltrans.type = supptrans.type AND gltrans.typeno = supptrans.transno
				WHERE gltrans.periodno = '".$_POST['ToPeriod']."'
				AND supptrans.supplierno = '".$supplier['supplierno']."'
				AND gltrans.amount < 0
				AND gltrans.account ='".$_SESSION['CompanyRecord']['creditorsact']."'
				GROUP BY supptrans.supplierno";
				$haber = DB_fetch_array(DB_query($sql,$db));
				
				if($_POST['saldo']==1 AND ($fwbalance['amount']!=0 OR $debe['amount']!=0 OR $haber['amount']!=0)){
					if ($k==1){
						echo "<tr bgcolor='#CCCCCC'>";
						$k=0;
					} else {
						echo "<tr bgcolor='#EEEEEE'>";
						$k++;
					}
					printf("<td ALIGN=LEFT>%s</td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							</tr>",
							'',
							$supplier['supplierno'],
							$supplier['suppname'],
							number_format($fwbalance['amount'],2),
							number_format($debe['amount'],2),
							number_format($haber['amount'],2),
							number_format(($debe['amount']+$haber['amount']+$fwbalance['amount']),2));
				}else  if($_POST['saldo']==0){
					if ($k==1){
						echo "<tr bgcolor='#CCCCCC'>";
						$k=0;
					} else {
						echo "<tr bgcolor='#EEEEEE'>";
						$k++;
					}
					printf("<td ALIGN=LEFT>%s</td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							</tr>",
							'',
							$supplier['supplierno'],
							$supplier['suppname'],
							number_format($fwbalance['amount'],2),
							number_format($debe['amount'],2),
							number_format($haber['amount'],2),
							number_format(($debe['amount']+$haber['amount']+$fwbalance['amount']),2));
				}else {
					//no hay saldo ni movimientos
				}
			}
			DB_free_result($res);
		}
		*/
		// bowikaxu realhost nov 2007 - END SUPPLIERS
	}  //end of while loop
	
					if($TotSaldo > $rh_umbral_asignacion AND $TotSaldo < (-1*$rh_umbral_asignacion)){ // dentro del umbral
						// do nothing					
					}else {
						$TotSaldo=0;
					}
					if ($k==1){
						echo "<tr bgcolor='#CCCCCC'>";
						$k=0;
					} else {
						echo "<tr bgcolor='#EEEEEE'>";
						$k++;
					}
					printf("<td ALIGN=LEFT COLSPAN=4><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							</tr>",
							_('Total'),
							number_format($TotFwd,2),
							number_format(abs($TotDebe),2),
							number_format(abs($TotHaber),2),
							number_format($TotSaldo,2));
	echo "</TABLE></CENTER>";
}
echo '</FORM>';
include('includes/footer.inc');

?>