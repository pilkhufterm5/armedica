<?php

/* $Revision: 1.12 $ */

/*Through deviousness and cunning, this system allows trial balances for any date range that recalcuates the p & l balances
and shows the balance sheets as at the end of the period selected - so first off need to show the input of criteria screen
while the user is selecting the criteria the system is posting any unposted transactions */

$PageSecurity = 8;

include ('includes/session.inc');
$title = _('Balanza General');

// bowikaxu realhost nov 07 - dont use from period
$_POST['ToPeriod']=$_POST['FromPeriod'];
// bowikaxu realhost Feb 2008 - get aaccounts
$sql = "SELECT accountcode FROM chartmaster ORDER BY accountcode ASC";
$min_acct = DB_fetch_array(DB_query($sql,$db));
$_POST['FromAccount'] = $min_acct['accountcode'];

$sql = "SELECT accountcode FROM chartmaster ORDER BY accountcode DESC";
$max_acct = DB_fetch_array(DB_query($sql,$db));
$_POST['ToAccount'] = $max_acct['accountcode'];

include('includes/SQL_CommonFunctions.inc');
include('includes/AccountSectionsDef.inc'); //this reads in the Accounts Sections array

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
	echo '<CENTER><TABLE><TR><TD>' . _('Period') . '</TD><TD><SELECT Name="FromPeriod">';
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
	/*
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
	*/
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
		<INPUT TYPE=SUBMIT Name='Excel' Value='"._('Show Balance Sheet')."'>
		</CENTER>";

/*Now do the posting while the user is thinking about the period to select */
		//include ('includes/GLPostings.inc');
		
} else if (isset($_POST['PrintPDF'])) {
	
	include('includes/PDFStarter.php');
	$PageNumber = 0;
	$FontSize = 10;
	$pdf->addinfo('Title', _('Balanza General') );
	$pdf->addinfo('Subject', _('Balanza General') );
	$line_height = 12;
	$Bottom_Margin = 36;
	
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
		$title = _('Balanza General') . ' - ' . _('Problem Report') . '....';
		include('includes/header.inc');
		prnMsg( _('No general ledger accounts were returned by the SQL because') . ' - ' . DB_error_msg($db) );
		echo '<BR><A HREF="' .$rootpath .'/index.php?' . SID . '">'. _('Back to the menu'). '</A>';
		if ($debug==1){
			echo '<BR>'. $SQL;
		}
		include('includes/footer.inc');
		exit;
	}
	
	include('includes/rh_PDFBalCompHeader.inc');
	
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
					$LeftOvers = $pdf->addTextWrap($Left_Margin+288,$YPos,63,8,number_format($fwbalance['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+351,$YPos,63,8,number_format($debe['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+414,$YPos,63,8,number_format($haber['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+477,$YPos,63,8,number_format(($debe['amount']+$haber['amount']+$fwbalance['amount']),2),'right');
				}else if ($_POST['saldo']==0){
					$YPos -= (1.5 * $line_height);
					$LeftOvers = $pdf->addTextWrap($Left_Margin+288,$YPos,63,8,number_format($fwbalance['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+351,$YPos,63,8,number_format($debe['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+414,$YPos,63,8,number_format($haber['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+477,$YPos,63,8,number_format(($debe['amount']+$haber['amount']+$fwbalance['amount']),2),'right');
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
			$FontSize = 8;
			
				$LeftOvers = $pdf->addTextWrap($Left_Margin+72,$YPos,216,$FontSize,$myrow['groupname']);
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
			include('includes/rh_PDFBalCompHeader.inc');
			//$YPos -= (2 * $line_height);
		}

		// Print total for each account
		if($_POST['saldo']==1 AND ($myrow['fwdbalance']!=0 OR $debeAcct['amount']!=0 OR $haberAcct['amount']!=0 OR $myrow['balance']!=0)){
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,30,$FontSize,$myrow['accountcode'],'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+30,$YPos,50,$FontSize,'','left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos,216,$FontSize,$myrow['accountname'],'left');
			
			$LeftOvers = $pdf->addTextWrap($Left_Margin+288,$YPos,63,$FontSize,number_format($myrow['fwdbalance'],2),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+351,$YPos,63,$FontSize,number_format($debeAcct['amount'],2),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+414,$YPos,63,$FontSize,number_format($haberAcct['amount'],2),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+477,$YPos,63,$FontSize,number_format($myrow['balance'],2),'right');
			$YPos -= $line_height;
		}else if($_POST['saldo']==0){
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,30,$FontSize,$myrow['accountcode'],'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+30,$YPos,50,$FontSize,'','left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos,216,$FontSize,$myrow['accountname'],'left');
			
			$LeftOvers = $pdf->addTextWrap($Left_Margin+288,$YPos,63,$FontSize,number_format($myrow['fwdbalance'],2),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+351,$YPos,63,$FontSize,number_format($debeAcct['amount'],2),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+414,$YPos,63,$FontSize,number_format($haberAcct['amount'],2),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+477,$YPos,63,$FontSize,number_format($myrow['balance'],2),'right');
			$YPos -= $line_height;
		}else {
			// no hay saldos ni movimientos
		}
		// bowikaxu realhost nov 2007 - DEBTORS
		if($myrow['accountcode']==$_SESSION['CompanyRecord']['debtorsact']){ // yes, it is the debtors account, so print debtors balance
			
			// debtors list and initial balance
			$sql = "SELECT SUM(gltrans.amount) AS balance, debtortrans.debtorno, debtorsmaster.name 
				FROM debtorsmaster, gltrans INNER JOIN debtortrans ON gltrans.type = debtortrans.type AND gltrans.typeno = debtortrans.transno
				WHERE debtorsmaster.debtorno = debtortrans.debtorno
				AND gltrans.periodno <= '".$_POST['ToPeriod']."'
				AND gltrans.account ='".$_SESSION['CompanyRecord']['debtorsact']."'
				GROUP BY debtortrans.debtorno
				ORDER BY debtortrans.debtorno ASC";
			
			$res = DB_query($sql,$db);
			while($debtor = DB_fetch_array($res)){
				
				// saldo anterior
				$sql = "SELECT SUM(gltrans.amount) AS amount, debtortrans.debtorno
				FROM gltrans INNER JOIN debtortrans ON gltrans.type = debtortrans.type AND gltrans.typeno = debtortrans.transno
				WHERE gltrans.periodno < '".$_POST['ToPeriod']."'
				AND debtortrans.debtorno = '".$debtor['debtorno']."'
				AND gltrans.account ='".$_SESSION['CompanyRecord']['debtorsact']."'
				GROUP BY debtortrans.debtorno";
				$fwbalance = DB_fetch_array(DB_query($sql,$db));
				// debe > 0
				$sql = "SELECT SUM(gltrans.amount) AS amount, debtortrans.debtorno
				FROM gltrans INNER JOIN debtortrans ON gltrans.type = debtortrans.type AND gltrans.typeno = debtortrans.transno
				WHERE gltrans.periodno = '".$_POST['ToPeriod']."'
				AND debtortrans.debtorno = '".$debtor['debtorno']."'
				AND gltrans.amount > 0
				AND gltrans.account ='".$_SESSION['CompanyRecord']['debtorsact']."'
				GROUP BY debtortrans.debtorno";
				//AND gltrans.type = 10
				$debe = DB_fetch_array(DB_query($sql,$db));
				// haber < 0
				$sql = "SELECT SUM(gltrans.amount) AS amount, debtortrans.debtorno
				FROM gltrans INNER JOIN debtortrans ON gltrans.type = debtortrans.type AND gltrans.typeno = debtortrans.transno
				WHERE gltrans.periodno = '".$_POST['ToPeriod']."'
				AND debtortrans.debtorno = '".$debtor['debtorno']."'
				AND gltrans.amount < 0
				AND gltrans.account ='".$_SESSION['CompanyRecord']['debtorsact']."'
				GROUP BY debtortrans.debtorno";
				//AND gltrans.type IN (11,12)
				$haber = DB_fetch_array(DB_query($sql,$db));
				
				if($_POST['saldo']==1 AND ($fwbalance['amount']!=0 OR $debe['amount']!=0 OR $haber['amount']!=0)){
					$LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos,216,$FontSize-1,"[ ".$debtor['debtorno']." ] ".$debtor['name'],'left');
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+130,$YPos,166,$FontSize-1,$debtor['name'],'left');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+288,$YPos,63,$FontSize-1,number_format($fwbalance['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+351,$YPos,63,$FontSize-1,number_format($debe['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+414,$YPos,63,$FontSize-1,number_format($haber['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+477,$YPos,63,$FontSize-1,number_format(($debe['amount']-$haber['amount']+$fwbalance['amount']),2),'right');
					$YPos -= $line_height;
				}else if($_POST['saldo']==0){
					$LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos,216,$FontSize-1,"[ ".$debtor['debtorno']." ] ".$debtor['name'],'left');
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+130,$YPos,166,$FontSize-1,$debtor['name'],'left');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+288,$YPos,63,$FontSize-1,number_format($fwbalance['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+351,$YPos,63,$FontSize-1,number_format($debe['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+414,$YPos,63,$FontSize-1,number_format($haber['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+477,$YPos,63,$FontSize-1,number_format(($debe['amount']-$haber['amount']+$fwbalance['amount']),2),'right');
					$YPos -= $line_height;
				}else {
					//no hay saldos ni movimientos
				}
				// Print heading if at end of page
				if ($YPos < ($Bottom_Margin)){
					include('includes/rh_PDFBalCompHeader.inc');
					//$YPos -= (2 * $line_height);
				}
			}
			DB_free_result($res);
		}
		// bowikaxu realhost nov 2007 - END DEBTORS
		
		// bowikaxu realhost nov 2007 - START SUPPLIERS
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
				AND gltrans.type = 20
				AND gltrans.account ='".$_SESSION['CompanyRecord']['creditorsact']."'
				GROUP BY supptrans.supplierno";
				$debe = DB_fetch_array(DB_query($sql,$db));
				// haber < 0
				$sql = "SELECT SUM(gltrans.amount) AS amount, supptrans.supplierno
				FROM gltrans INNER JOIN supptrans ON gltrans.type = supptrans.type AND gltrans.typeno = supptrans.transno
				WHERE gltrans.periodno = '".$_POST['ToPeriod']."'
				AND supptrans.supplierno = '".$supplier['supplierno']."'
				AND gltrans.type IN (21,22)
				AND gltrans.account ='".$_SESSION['CompanyRecord']['creditorsact']."'
				GROUP BY supptrans.supplierno";
				$haber = DB_fetch_array(DB_query($sql,$db));
				
				if($_POST['saldo']==1 AND ($fwbalance['amount']!=0 OR $debe['amount']!=0 OR $haber['amount']!=0)){
					$LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos,216,$FontSize-1,"[ ".$supplier['supplierno']." ] ".$supplier['suppname'],'left');
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+130,$YPos,166,$FontSize-1,$supplier['suppname'],'left');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+288,$YPos,63,$FontSize-1,number_format($fwbalance['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+351,$YPos,63,$FontSize-1,number_format($debe['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+414,$YPos,63,$FontSize-1,number_format($haber['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+477,$YPos,63,$FontSize-1,number_format(($debe['amount']+$haber['amount']+$fwbalance['amount']),2),'right');
					$YPos -= $line_height;
				}else if ($_POST['saldo']==0){
					$LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos,216,$FontSize-1,"[ ".$supplier['supplierno']." ] ".$supplier['suppname'],'left');
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+130,$YPos,166,$FontSize-1,$supplier['suppname'],'left');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+288,$YPos,63,$FontSize-1,number_format($fwbalance['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+351,$YPos,63,$FontSize-1,number_format($debe['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+414,$YPos,63,$FontSize-1,number_format($haber['amount'],2),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+477,$YPos,63,$FontSize-1,number_format(($debe['amount']+$haber['amount']+$fwbalance['amount']),2),'right');
					$YPos -= $line_height;
				}else {
					// no hay saldo ni movimientos
				}
				// Print heading if at end of page
				if ($YPos < ($Bottom_Margin)){
					include('includes/rh_PDFBalCompHeader.inc');
					//$YPos -= (2 * $line_height);
				}
			}
			DB_free_result($res);
			
		}
		// bowikaxu realhost nov 2007 - END SUPPLIERS
		
	}  //end of while loop
	
	if ($YPos < ($Bottom_Margin+15)){
		include('includes/rh_PDFBalCompHeader.inc');
		//$YPos -= (2 * $line_height);
	}
	
	$YPos -= (2 * $line_height);
	$pdf->line($Left_Margin+288, $YPos+$line_height,$Left_Margin+540, $YPos+$line_height);  
	//$FontSize += 1;
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,72,$FontSize,_('Total'));
	$LeftOvers = $pdf->addTextWrap($Left_Margin+288,$YPos,63,$FontSize,number_format($TotFwd,2),'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+351,$YPos,63,$FontSize,number_format(abs($TotDebe),2),'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+414,$YPos,63,$FontSize,number_format(abs($TotHaber),2),'right');
	$TotSaldo = $TotDebe + $TotHaber;
	if($TotSaldo > $rh_umbral_asignacion AND $TotSaldo < (-1*$rh_umbral_asignacion)){ // dentro del umbral
		$LeftOvers = $pdf->addTextWrap($Left_Margin+477,$YPos,63,$FontSize,number_format($TotSaldo,2),'right');
	}else { // fuera del umbral
		$LeftOvers = $pdf->addTextWrap($Left_Margin+477,$YPos,63,$FontSize,number_format($TotSaldo,2),'right');
	}
	
	$pdf->line($Left_Margin+288, $YPos,$Left_Margin+540, $YPos);  
	
	$pdfcode = $pdf->output();
	$len = strlen($pdfcode);
	
	if ($len<=20){
		$title = _('Print Bal. Comp. Error');
		include('includes/header.inc');
		echo '<p>';
		prnMsg( _('There were no entries to print out for the selections specified') );
		echo '<BR><A HREF="'. $rootpath.'/index.php?' . SID . '">'. _('Back to the menu'). '</A>';
		include('includes/footer.inc');
		exit;
	} else {
		header('Content-type: application/pdf');
		header('Content-Length: ' . $len);
		header('Content-Disposition: inline; filename=rh_BalComp.pdf');
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
	//<TD class='tableheader'>"._('Code')."</TD>
	$TableHeader = "<TR><TD class='tableheader'>"._('Account')."</TD>
					<TD class='tableheader'>"._('Account Name')."</TD>
					<TD class='tableheader'>"._('Saldo Inicial')."</TD>
					<TD class='tableheader'>"._('Debit')."</TD>
					<TD class='tableheader'>"._('Credit')."</TD>
					<TD class='tableheader'>"._('Balance').' '._('Final')."</TD></TR>";
	
	// HEADERS
	//"<BR>"._('Cuenta Inicial').': '.$_POST['FromAccount']."<BR>"._('Cuenta Final').': '.$_POST['ToAccount'].
/****************************************************************************************************************************
* Jorge Garcia
* 16/Ene/2009 Cambio en el periodo
****************************************************************************************************************************/
	$PeriodToDate2 = explode(" ", $PeriodToDate);
	echo "<BR><CENTER>
			<B><H2>".$_SESSION['CompanyRecord']['coyname'].
			"<BR>"._('Balanza General al ') . $LastDay.' de '.$PeriodToDate2[0]." de ".$PeriodToDate2[1]."</H2>".
			"<TABLE>";
/****************************************************************************************************************************
* Jorge Garcia Fin Modificacion
****************************************************************************************************************************/
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
						//<td ALIGN=LEFT><B>%s</B></td>
						printf("<td ALIGN=LEFT><B>%s</B></td>
							<td ALIGN=LEFT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							</tr>",
							'',
							$myrow['groupname'],
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
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							<td ALIGN=RIGHT><B>%s</B></td>
							</tr>",
							'',
							$myrow['groupname'],
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
					
					printf("<td ALIGN=LEFT><a href='GLAccountInquiry.php?&Account=%s'>%s</a></td>
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							</tr>",
							$myrow['accountcode'],
							$myrow['accountcode'],
							$myrow['accountname'],
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
					
					printf("<td ALIGN=LEFT><a href='GLAccountInquiry.php?&Account=%s'>%s</a></td>
							
							<td ALIGN=LEFT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							<td ALIGN=RIGHT>%s</td>
							</tr>",
							$myrow['accountcode'],
							$myrow['accountcode'],
							$myrow['accountname'],
							number_format($myrow['fwdbalance'],2),
							number_format($debeAcct['amount'],2),
							number_format($haberAcct['amount'],2),
							number_format($myrow['balance'],2));
		}else {
			// no hay saldo ni movimientos
		}
		// bowikaxu realhost nov 2007 - DEBTORS
		/*
		if($myrow['accountcode']==$_SESSION['CompanyRecord']['debtorsact']){ // yes, it is the debtors account, so print debtors balance
			
			// debtors list and initial balance
			$sql = "SELECT SUM(gltrans.amount) AS balance, debtortrans.debtorno, debtorsmaster.name 
				FROM debtorsmaster, gltrans INNER JOIN debtortrans ON gltrans.type = debtortrans.type AND gltrans.typeno = debtortrans.transno
				WHERE debtorsmaster.debtorno = debtortrans.debtorno
				AND gltrans.periodno <= '".$_POST['ToPeriod']."'
				AND gltrans.account ='".$_SESSION['CompanyRecord']['debtorsact']."'
				GROUP BY debtortrans.debtorno
				ORDER BY debtortrans.debtorno ASC";
			
			$res = DB_query($sql,$db);
			while($debtor = DB_fetch_array($res)){
				
				// saldo anterior
				$sql = "SELECT SUM(gltrans.amount) AS amount, debtortrans.debtorno
				FROM gltrans INNER JOIN debtortrans ON gltrans.type = debtortrans.type AND gltrans.typeno = debtortrans.transno
				WHERE gltrans.periodno < '".$_POST['ToPeriod']."'
				AND debtortrans.debtorno = '".$debtor['debtorno']."'
				AND gltrans.account ='".$_SESSION['CompanyRecord']['debtorsact']."'
				GROUP BY debtortrans.debtorno";
				$fwbalance = DB_fetch_array(DB_query($sql,$db));
				// debe > 0
				$sql = "SELECT SUM(gltrans.amount) AS amount, debtortrans.debtorno
				FROM gltrans INNER JOIN debtortrans ON gltrans.type = debtortrans.type AND gltrans.typeno = debtortrans.transno
				WHERE gltrans.periodno = '".$_POST['ToPeriod']."'
				AND debtortrans.debtorno = '".$debtor['debtorno']."'
				AND gltrans.amount > 0
				AND gltrans.account ='".$_SESSION['CompanyRecord']['debtorsact']."'
				GROUP BY debtortrans.debtorno";
				$debe = DB_fetch_array(DB_query($sql,$db));
				// haber < 0
				$sql = "SELECT SUM(gltrans.amount) AS amount, debtortrans.debtorno
				FROM gltrans INNER JOIN debtortrans ON gltrans.type = debtortrans.type AND gltrans.typeno = debtortrans.transno
				WHERE gltrans.periodno = '".$_POST['ToPeriod']."'
				AND debtortrans.debtorno = '".$debtor['debtorno']."'
				AND gltrans.amount < 0
				AND gltrans.account ='".$_SESSION['CompanyRecord']['debtorsact']."'
				GROUP BY debtortrans.debtorno";
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
							$debtor['debtorno'],
							$debtor['name'],
							number_format($fwbalance['amount'],2),
							number_format($debe['amount'],2),
							number_format($haber['amount'],2),
							number_format(($debe['amount']+$haber['amount']+$fwbalance['amount']),2));
				}else if($_POST['saldo']==0){
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
							$debtor['debtorno'],
							$debtor['name'],
							number_format($fwbalance['amount'],2),
							number_format($debe['amount'],2),
							number_format($haber['amount'],2),
							number_format(($debe['amount']+$haber['amount']+$fwbalance['amount']),2));
				}else{
					// no hay saldos ni movimientos
				}
				// Print heading if at end of page
			}
			DB_free_result($res);
		}
		// bowikaxu realhost nov 2007 - END DEBTORS
		*/
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
					$TotSaldo = $TotDebe + $TotHaber;
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
					printf("<td ALIGN=LEFT COLSPAN=2><B>%s</B></td>
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