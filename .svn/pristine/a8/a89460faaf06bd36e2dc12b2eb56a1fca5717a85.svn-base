<?php

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-09-25 09:37:22 -0500 (Thu, 25 Sep 2008) $
 * $Rev: 413 $
 */

$PageSecurity = 8;
include ('includes/session.inc');
$title = _('General Ledger Account Inquiry');
include('includes/header.inc');
include('includes/GLPostings.inc');

if (isset($_POST['Account'])){
	$SelectedAccount = $_POST['Account'];
} elseif (isset($_GET['Account'])){
	$SelectedAccount = $_GET['Account'];
}

if (isset($_POST['Period'])){
	$SelectedPeriod = $_POST['Period'];
} elseif (isset($_GET['Period'])){
	$SelectedPeriod = $_GET['Period'];
}

echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';

/*Dates in SQL format for the last day of last month*/
$DefaultPeriodDate = Date ('Y-m-d', Mktime(0,0,0,Date('m'),0,Date('Y')));


$SQL = "SELECT bankaccountname,
		bankaccounts.accountcode
	FROM bankaccounts,
		chartmaster
	WHERE bankaccounts.accountcode=chartmaster.accountcode";

$ErrMsg = _('The bank accounts could not be retrieved because');
$DbgMsg = _('The SQL used to retrieve the bank acconts was');
$AccountsResults = DB_query($SQL,$db,$ErrMsg,$DbgMsg);

/*Show a form to allow input of criteria for TB to show */
echo '<CENTER><TABLE>
        <TR>
         <TD>'._('Account').":</TD><TD><SELECT name='Account'>";
		
         if (DB_num_rows($AccountsResults)==0){
	echo '</SELECT></TD></TR></TABLE><P>';
	prnMsg( _('Bank Accounts have not yet been defined. You must first') . ' <A HREF="' . $rootpath . '/BankAccounts.php">' . _('define the bank accounts') . '</A> ' . _('and general ledger accounts to be affected'),'warn');
	include('includes/footer.inc');
	exit;
} else {
	while ($myrow=DB_fetch_array($AccountsResults)){
	/*list the bank account names */
		if ($_POST['Account']==$myrow['accountcode']){
			echo '<OPTION SELECTED VALUE="' . $myrow['accountcode'] . '">' . $myrow['bankaccountname'];
		} else {
			echo '<OPTION VALUE="' . $myrow['accountcode'] . '">' . $myrow['bankaccountname'];
		}
	}
	echo '</SELECT></TD></TR>';
}
         echo '<TR>
         <TD>'._('For Period range').':</TD>
         <TD><SELECT Name=Period[] multiple>';
	 $sql = 'SELECT periodno, lastdate_in_period FROM periods ORDER BY periodno DESC';
	 $Periods = DB_query($sql,$db);
         $id=0;
         while ($myrow=DB_fetch_array($Periods,$db)){

            if($myrow['periodno'] == $SelectedPeriod[$id]){
              echo '<OPTION SELECTED VALUE=' . $myrow['periodno'] . '>' . _(MonthAndYearFromSQLDate($myrow['lastdate_in_period']));
            $id++;
            } else {
              echo '<OPTION VALUE=' . $myrow['periodno'] . '>' . _(MonthAndYearFromSQLDate($myrow['lastdate_in_period']));
            }

         }
         echo "</SELECT></TD>
        </TR>        
        <TR>           	
        	<TD>"._('Detail Or Summary Only')."</TD>
        	<TD><SELECT NAME='detail'>";
         if($_POST['detail']=='summary'){
         	echo "<OPTION SELECTED VALUE='summary'>"._('Summary Report')."
           		<OPTION VALUE='detailed'>"._('Detailed Report')."";
         }else {
         	echo "<OPTION VALUE='summary'>"._('Summary Report')."
           		<OPTION SELECTED VALUE='detailed'>"._('Detailed Report')."";
         }
        	
         echo  "</SELECT></TD>
        </TR>
        
</TABLE><P>
<INPUT TYPE=SUBMIT NAME='Show' VALUE='"._('Show Account Transactions')."'></CENTER></FORM>";

/* End of the Form  rest of script is what happens if the show button is hit*/

if (isset($_POST['Show'])){

	if (!isset($SelectedPeriod)){
		prnMsg(_('A period or range of periods must be selected from the list box'),'info');
		include('includes/footer.inc');
		exit;
	}
	/*Is the account a balance sheet or a profit and loss account */
	$result = DB_query("SELECT pandl
				FROM accountgroups
				INNER JOIN chartmaster ON accountgroups.groupname=chartmaster.group_
				WHERE chartmaster.accountcode=$SelectedAccount",$db);
	$PandLRow = DB_fetch_row($result);
	if ($PandLRow[0]==1){
		$PandLAccount = True;
	}else{
		$PandLAccount = False; /*its a balance sheet account */
	}

	$FirstPeriodSelected = min($SelectedPeriod);
	$LastPeriodSelected = max($SelectedPeriod);

 	$sql= "SELECT type,
			typename,
			gltrans.typeno,
			trandate,
			narrative,
			chequeno,
			amount,
			periodno
		FROM gltrans, systypes
		WHERE gltrans.account = $SelectedAccount
 		AND gltrans.type IN(1,2,12,22)
		AND systypes.typeid=gltrans.type
		AND posted=1
		AND periodno>=$FirstPeriodSelected
		AND periodno<=$LastPeriodSelected
		ORDER BY periodno, gltrans.trandate, counterindex";

	$ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because') ;
	$TransResult = DB_query($sql,$db,$ErrMsg);

	echo '<table align=center>';

	// bowikaxu realhost - sept 2008 - show related files	
	if(isset($_POST['detail']) && $_POST['detail']=='detailed'){
		
		$TableHeader = "<TR>
			<TD class='tableheader'>" . _('Type') . "</TD>
			<TD class='tableheader'>" . _('Number') . "</TD>
			<TD class='tableheader'>" . _('Date') . "</TD>
			<TD class='tableheader'>" . _('Debit') . "</TD>
			<TD class='tableheader'>" . _('Credit') . "</TD>
			<TD class='tableheader'>" ._('Balance')."</TD>
			<TD class='tableheader'>" ._('Asignacion')."</TD>
			<TD class='tableheader'>" . _('Narrative') . "</TD>
			<TD class='tableheader'>" . _('Cleared') . "</TD>
			<TD class='tableheader'>" . _('Detailed Report') . '</TD>
			</TR>';		
	}else {
		$TableHeader = "<TR>
			<TD class='tableheader'>" . _('Type') . "</TD>
			<TD class='tableheader'>" . _('Number') . "</TD>
			<TD class='tableheader'>" . _('Date') . "</TD>
			<TD class='tableheader'>" . _('Debit') . "</TD>
			<TD class='tableheader'>" . _('Credit') . "</TD>
			<TD class='tableheader'>" ._('Balance')."</TD>
			<TD class='tableheader'>" . _('Narrative') . "</TD>
			<TD class='tableheader'>" . _('Cleared') . '</TD>
			</TR>';
	}
	echo $TableHeader;

	if ($PandLAccount==True) {
		$RunningTotal = 0;
	} else {
	       // added to fix bug with Brought Forward Balance always being zero
					$sql = "SELECT bfwd, 
						actual,
						period 
					FROM chartdetails 
					WHERE chartdetails.accountcode= $SelectedAccount 
					AND chartdetails.period=" . $FirstPeriodSelected; 
					
				$ErrMsg = _('The chart details for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved');
				$ChartDetailsResult = DB_query($sql,$db,$ErrMsg);
				$ChartDetailRow = DB_fetch_array($ChartDetailsResult);
				// --------------------
				
		$RunningTotal =$ChartDetailRow['bfwd'];
		if ($RunningTotal < 0 ){ //its a credit balance b/fwd
			echo "<TR bgcolor='#FDFEEF'>
				<TD COLSPAN=5><B>" . _('Brought Forward Balance') . '</B><TD>
				</TD></TD>
				<TD ALIGN=RIGHT><B>' . number_format(-$RunningTotal,2) . '</B></TD>
				<TD></TD>
				</TR>';
		} else { //its a debit balance b/fwd
			echo "<TR bgcolor='#FDFEEF'>
				<TD COLSPAN=5><B>" . _('Brought Forward Balance') . '</B></TD>
				<TD ALIGN=RIGHT><B>' . number_format($RunningTotal,2) . '</B></TD>
				<TD COLSPAN=2></TD>
				</TR>';
		}
	}
	$PeriodTotal = 0;
	$PeriodNo = -9999;
	$ShowIntegrityReport = False;
	$j = 1;
	$k=0; //row colour counter

	while ($myrow=DB_fetch_array($TransResult)) {

		if ($myrow['periodno']!=$PeriodNo){
			if ($PeriodNo!=-9999){ //ie its not the first time around
				/*Get the ChartDetails balance b/fwd and the actual movement in the account for the period as recorded in the chart details - need to ensure integrity of transactions to the chart detail movements. Also, for a balance sheet account it is the balance carried forward that is important, not just the transactions*/

				$sql = "SELECT bfwd, 
						actual,
						period 
					FROM chartdetails 
					WHERE chartdetails.accountcode= $SelectedAccount 
					AND chartdetails.period=" . $PeriodNo; 
					
				$ErrMsg = _('The chart details for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved');
				$ChartDetailsResult = DB_query($sql,$db,$ErrMsg);
				$ChartDetailRow = DB_fetch_array($ChartDetailsResult);
				
				echo "<TR bgcolor='#FDFEEF'>
					<TD COLSPAN=5><B>" . _('Total for period') . ' ' . $PeriodNo . '</B></TD>';
				if ($PeriodTotal < 0 ){ //its a credit balance b/fwd
					echo '<TD></TD>
						<TD ALIGN=RIGHT><B>' . number_format(-$PeriodTotal,2) . '</B></TD>
						<TD></TD>
						</TR>';
				} else { //its a debit balance b/fwd
					echo '<TD ALIGN=RIGHT><B>' . number_format($PeriodTotal,2) . '</B></TD>
						<TD COLSPAN=2></TD>
						</TR>';
				}
				$IntegrityReport .= '<BR>' . _('Period') . ': ' . $PeriodNo  . _('Account movement per transaction') . ': '  . number_format($PeriodTotal,2) . ' ' . _('Movement per ChartDetails record') . ': ' . number_format($ChartDetailRow['actual'],2) . ' ' . _('Period difference') . ': ' . number_format($PeriodTotal -$ChartDetailRow['actual'],3);
				
				if (ABS($PeriodTotal -$ChartDetailRow['actual'])>0.01){
					$ShowIntegrityReport = True;
				}
			}
			$PeriodNo = $myrow['periodno'];
			$PeriodTotal = 0;
		}

		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k++;
		}
		

		$RunningTotal += $myrow['amount'];
		$PeriodTotal += $myrow['amount'];

		if($myrow['amount']>=0){
			$DebitAmount = '';
			$CreditAmount = number_format($myrow['amount'],2);
		} else {
			$CreditAmount = '';
			$DebitAmount = number_format(-$myrow['amount'],2);
		}

		// bowikaxu realhost - obtener las asignaciones
		$sql = "SELECT id, ovamount+ovgst AS totamt FROM debtortrans WHERE type='".$myrow['type']."' AND transno = '".$myrow['typeno']."'";
		$AsRes = DB_query($sql,$db);
		$Assign = "";
		while($Ass = DB_fetch_array($AsRes)){
			$sql = "SELECT debtortrans.type, debtortrans.transno, debtortrans.trandate, debtortrans.debtorno, 
					debtortrans.reference, debtortrans.rate, 
					ovamount+ovgst+ovfreight+ovdiscount as totalamt, custallocns.amt,
					systypes.typename
					FROM debtortrans INNER JOIN custallocns ON debtortrans.id=custallocns.transid_allocto
					INNER JOIN systypes ON debtortrans.type = systypes.typeid
					WHERE custallocns.transid_allocfrom='".$Ass['id']."'";
			$AsRes2 = DB_query($sql,$db);
			while ($Ass2 = DB_fetch_array($AsRes2)){
				
				if($myrow['type']==10){
					$sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations
					 WHERE rh_invoicesreference.intinvoice = ".$Ass2['transno']." AND locations.loccode = rh_invoicesreference.loccode";
		    		$res = DB_query($sql,$db);
		    		$ExtInvoice = DB_fetch_array($res);
		    		$Assign .= ', '._('Invoice').' '.$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$Ass2['transno'].')';
				}else {
					$Assign .= ', '.$Ass2['typename'].' '.$Ass2['transno'];
				}
			}
			DB_free_result($AsRes2);
		}
		
		DB_free_result($AsRes);
		
		$FormatedTranDate = ConvertSQLDate($myrow['trandate']);
		$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
		// bowikaxu - Se agrego la columna balance con el valor de $RunningTotal
		
			// bowikaxu realhost - sept 2008 - get related files
			$sql = "SELECT count(id) AS files FROM rh_files WHERE type = ".$myrow['type']."
					AND transno =".$myrow['typeno']."";
			$f_res = DB_query($sql,$db);
			$finfo = DB_fetch_array($f_res);
			if($finfo['files']>0){ // there is at least one attachment
				$files_str = "<TD><A HREF='".$rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno']."' TARGET=blank><IMG width=17 height=17 BORDER=0 SRC='".$rootpath.'/css/'.$theme.'/images/attachment.gif'."'></A></TD>";
			}else {
				$files_str = '';
			}
			// bowikaxu - end show related files
		
			if(isset($_POST['detail']) && $_POST['detail']=='detailed'){

				printf("<td>%s</td>
			<td><A HREF='%s'>%s</A></td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			",
			$myrow['typename'],
			$URL_to_TransDetail,
			$myrow['typeno'],
			$FormatedTranDate,
			$DebitAmount,
			$CreditAmount,
			number_format($RunningTotal,2),
			$Assign,
			$myrow['narrative'],
			$myrow['amountcleared']);
				
					$sql = "SELECT banktrans.amount,
						banktrans.amountcleared,
						banktrans.banktranstype,
						banktrans.currcode,
						banktrans.ref
						FROM banktrans
						WHERE
						banktrans.type = " . $myrow['type']."
						AND banktrans.transno = ".$myrow['typeno']."";
				$res = DB_query($sql,$db);
				echo "<TD>";
				while($cheqinfo = DB_fetch_array($res)){
					
					if($myrow['type']==12){
						echo _('Cheque').': '.$myrow['chequeno']." ";
					}
					echo $cheqinfo['amount']." ".$cheqinfo['currcode']." ".$cheqinfo['banktranstype']." - ".$cheqinfo['ref'];
					echo "<BR>";
					
				}
				DB_free_result($res);
				echo "</TD>";
				echo "<TD>".$files_str."</TD>";				
			}else {
				
				printf("<td>%s</td>
			<td><A HREF='%s'>%s</A></td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			",
			$myrow['typename'],
			$URL_to_TransDetail,
			$myrow['typeno'],
			$FormatedTranDate,
			$DebitAmount,
			$CreditAmount,
			number_format($RunningTotal,2),
			$myrow['narrative'],
			$myrow['amountcleared'],
			$files_str);
				
			}

		echo "</TR>";
		$j++;

		If ($j == 18){
			echo $TableHeader;
			$j=1;
		}
		
	}

	echo "<TR bgcolor='#FDFEEF'><TD COLSPAN=5><B>";
	if ($PandLAccount==True){
		echo _('Total Period Movement');
	} else { /*its a balance sheet account*/
		echo _('Balance C/Fwd');
	}
	echo '</B></TD>';

	if ($RunningTotal >0){
		echo '<TD ALIGN=RIGHT><B>' . number_format(($RunningTotal),2) . '</B></TD><TD COLSPAN=2></TD></TR>';
	}else {
		echo '<TD></TD><TD ALIGN=RIGHT><B>' . number_format((-$RunningTotal),2) . '</B></TD><TD COLSPAN=2></TD></TR>';
	}
	echo '</table>';
} /* end of if Show button hit */



if ($ShowIntegrityReport){

	prnMsg( _('There are differences between the sum of the transactions and the recorded movements in the ChartDetails table') . '. ' . _('A log of the account differences for the periods report shows below'),'warn');
	echo '<P>'.$IntegrityReport;
}
include('includes/footer.inc');
?>
