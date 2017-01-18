<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-09-25 09:37:22 -0500 (Thu, 25 Sep 2008) $
 * $Rev: 413 $
 */

/* webERP: Revision: 14 $ */

$PageSecurity = 8;
include ('includes/session.inc');
$title = _('General Ledger Account Inquiry');
if(!isset($_POST['Excel'])){
	include('includes/header.inc');
	include('includes/GLPostings.inc');
}else {
	// bowikaxu realhost - March 2008 - export to excel
	include('includes/class-excel-xml.inc.php');
}

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

if(isset($_POST['Excel'])){
	
	$FirstPeriodSelected = min($SelectedPeriod);
	$LastPeriodSelected = max($SelectedPeriod);
	
	$xls = new Excel_XML;
	$ii=2;
	//$doc = array(1=>array('','','',_('Period').' '.$FirstPeriodSelected.' - '.$LastPeriodSelected,'','',''));
	$doc = array(1=>array(_('Narrative'),_('Type'),_('Number'),_('Date'),_('Debit'),_('Credit'),_('Balance')));
	
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

 	$sql= "SELECT type,
			typename,
			gltrans.typeno,
			trandate,
			narrative,
			amount,
			periodno
		FROM gltrans, systypes
		WHERE gltrans.account = $SelectedAccount
		AND systypes.typeid=gltrans.type
		AND posted=1
		AND periodno>=$FirstPeriodSelected
		AND periodno<=$LastPeriodSelected
		ORDER BY periodno, gltrans.trandate, counterindex";

	$ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because') ;
	$TransResult = DB_query($sql,$db,$ErrMsg);

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
				
				$doc[$ii]= array(_('Brought Forward Balance'),'','','','','','',-$RunningTotal);
				$ii++;
				
		} else { //its a debit balance b/fwd
			
				$doc[$ii]= array(_('Brought Forward Balance'),'','','','','',$RunningTotal);
				$ii++;
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
					
				if ($PeriodTotal < 0 ){ //its a credit balance b/fwd
				
					$doc[$ii]= array(_('Total for period').' '.$PeriodNo,'','','','',-$PeriodTotal);
					$ii++;
				} else { //its a debit balance b/fwd
				
					$doc[$ii]= array(_('Total for period').' '.$PeriodNo,'','','',$PeriodTotal);
					$ii++;
				}
				
				$IntegrityReport .= '<BR>' . _('Period') . ': ' . $PeriodNo  . _('Account movement per transaction') . ': '  . number_format($PeriodTotal,2) . ' ' . _('Movement per ChartDetails record') . ': ' . number_format($ChartDetailRow['actual'],2) . ' ' . _('Period difference') . ': ' . number_format($PeriodTotal -$ChartDetailRow['actual'],3);
				
				if (ABS($PeriodTotal -$ChartDetailRow['actual'])>0.01){
					$ShowIntegrityReport = True;
				}
			}
			$PeriodNo = $myrow['periodno'];
			$PeriodTotal = 0;
		}

		$RunningTotal += $myrow['amount'];
		$PeriodTotal += $myrow['amount'];

		if($myrow['amount']>=0){
			$DebitAmount = $myrow['amount'];
			$CreditAmount = '';
		} else {
			$CreditAmount = -$myrow['amount'];
			$DebitAmount = '';
		}

		$FormatedTranDate = ConvertSQLDate($myrow['trandate']);
		$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
		// bowikaxu - Se agrego la columna balance con el valor de $RunningTotal
		/*
		printf("<td>%s</td>
			<td>%s</td>
			<td><A HREF='%s'>%s</A></td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			</tr>",
			$myrow['narrative'],
			$myrow['typename'],
			$URL_to_TransDetail,
			$myrow['typeno'],
			$FormatedTranDate,
			$DebitAmount,
			$CreditAmount,
			number_format($RunningTotal,2));
			*/
			$doc[$ii]= array($myrow['narrative'],$myrow['typename'],$myrow['typeno'],$FormatedTranDate,$DebitAmount,$CreditAmount,$RunningTotal);
			$ii++;

		$j++;
		
	}

	//echo "<TR bgcolor='#FDFEEF'><TD COLSPAN=3><B>";
	if ($PandLAccount==True){
		//echo _('Total Period Movement');
	} else { /*its a balance sheet account*/
		//echo _('Balance C/Fwd');
	}
	//echo '</B></TD>';

	if ($RunningTotal >0){
		//echo '<TD ALIGN=RIGHT><B>' . number_format(($RunningTotal),2) . '</B></TD><TD COLSPAN=2></TD></TR>';
		$doc[$ii]= array(_('Total'),'','','','','',$RunningTotal);
		$ii++;
	}else {
		//echo '<TD></TD><TD ALIGN=RIGHT><B>' . number_format((-$RunningTotal),2) . '</B></TD><TD COLSPAN=2></TD></TR>';
		$doc[$ii]= array(_('Total'),'','','','','',-$RunningTotal);
		$ii++;
	}

	$xls->addArray ( $doc );
	$xls->generateXML ("AccountInquiry");
	exit;
	
}

echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';

/*Dates in SQL format for the last day of last month*/
$DefaultPeriodDate = Date ('Y-m-d', Mktime(0,0,0,Date('m'),0,Date('Y')));

/*Show a form to allow input of criteria for TB to show */
echo '<CENTER><TABLE>
        <TR>
         <TD>'._('Account').":</TD>
         <TD><SELECT Name='Account'>";
         $sql = 'SELECT accountcode, accountname FROM chartmaster ORDER BY accountcode';
         $Account = DB_query($sql,$db);
         while ($myrow=DB_fetch_array($Account,$db)){
            if($myrow['accountcode'] == $SelectedAccount){
   	        echo '<OPTION SELECTED VALUE=' . $myrow['accountcode'] . '>' . $myrow['accountcode'] . ' ' . $myrow['accountname'];
	    } else {
		echo '<OPTION VALUE=' . $myrow['accountcode'] . '>' . $myrow['accountcode'] . ' ' . $myrow['accountname'];
	    }
         }
         echo '</SELECT></TD></TR>
         <TR>
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
</TABLE><P>
<INPUT TYPE=SUBMIT NAME='Show' VALUE='"._('Show Account Transactions')."'>
<INPUT TYPE=SUBMIT NAME='Excel' VALUE='"._('Excel')."'>
</CENTER></FORM>";

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
			amount,
			periodno
		FROM gltrans, systypes
		WHERE gltrans.account = $SelectedAccount
		AND systypes.typeid=gltrans.type
		AND posted=1
		AND periodno>=$FirstPeriodSelected
		AND periodno<=$LastPeriodSelected
		ORDER BY periodno, gltrans.trandate, counterindex";

	$ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because') ;
	$TransResult = DB_query($sql,$db,$ErrMsg);
	
	$sql = 'SELECT lastdate_in_period, DAY(lastdate_in_period) AS day FROM periods WHERE periodno=' . $FirstPeriodSelected;
	$PrdResult = DB_query($sql, $db);
	$myrow = DB_fetch_row($PrdResult);
	$LastDay = $myrow[1];
	$PeriodToDate = MonthAndYearFromSQLDate($myrow[0]);
	
	$sql = 'SELECT lastdate_in_period, DAY(lastdate_in_period) AS day FROM periods WHERE periodno=' . $LastPeriodSelected;
	$PrdResult = DB_query($sql, $db);
	$myrow = DB_fetch_row($PrdResult);
	$LastLastDay = $myrow[1];
	$LastPeriodToDate = MonthAndYearFromSQLDate($myrow[0]);
	
	echo "<BR><CENTER>
			<B><H2>".$_SESSION['CompanyRecord']['coyname'].
			"<BR>"._('Account Inquiry') .' '._('From').' 1' . ' de '.$PeriodToDate.' '._('To').' '.$LastLastDay.' de '.$LastPeriodToDate.
			"<BR>"._('Account').': '.$SelectedAccount.'&nbsp;&nbsp;<A HREF="' . $rootpath . '/GLAccounts.php?' . SID . '&SelectedAccount=' . $SelectedAccount . '">[' . _('Edit Account') . "]</A><BR></H2></CENTER>";

	echo '<table align="center">';

	// bowikaxu realhost - sept 2008 - show related files
	$TableHeader = "<TR>
			<TD class='tableheader'>" . _('Type') . "</TD>
			<TD class='tableheader'>" . _('Date') . "</TD>
			<TD class='tableheader'>" . _('Number') . "</TD>
			<TD class='tableheader'>" . _('Narrative') . "</TD>
			<TD class='tableheader'>" . _('Debit') . "</TD>
			<TD class='tableheader'>" . _('Credit') . "</TD>
			<TD class='tableheader'>" ._('Balance').'</TD>
			</TR>';

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
				<TD COLSPAN=6><B>" . _('Brought Forward Balance') . '</B><TD>
				<TD ALIGN=RIGHT><B>' . number_format(-$RunningTotal,2) . '</B></TD>
				</TR>';
		} else { //its a debit balance b/fwd
			echo "<TR bgcolor='#FDFEEF'>
				<TD COLSPAN=6><B>" . _('Brought Forward Balance') . '</B></TD>
				<TD ALIGN=RIGHT><B>' . number_format($RunningTotal,2) . '</B></TD>
				</TR>';
		}
	}
	$PeriodTotal = 0;
	$PeriodNo = -9999;
	$ShowIntegrityReport = False;
	$j = 1;
	$k=0; //row colour counter
	
	// bowikaxu realhost - may 2008 credit/debtit totals
	$CreditTotal = 0;
	$DebitTotal = 0;

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
					<TD COLSPAN=6><B>" . _('Total for period') . ' ' . $PeriodNo . '</B></TD>';
				if ($PeriodTotal < 0 ){ //its a credit balance b/fwd
					echo '
						<TD ALIGN=RIGHT><B>' . number_format(-$PeriodTotal,2) . '</B></TD>
						</TR>';
				} else { //its a debit balance b/fwd
					echo '<TD ALIGN=RIGHT><B>' . number_format($PeriodTotal,2) . '</B></TD>
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
			$DebitAmount = number_format($myrow['amount'],2);
			$DebitTotal += $myrow['amount'];
			$CreditAmount = '';
		} else {
			$CreditAmount = number_format(-$myrow['amount'],2);
			$CreditTotal += (-$myrow['amount']);
			$DebitAmount = '';
		}

		$FormatedTranDate = ConvertSQLDate($myrow['trandate']);
		$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
		// bowikaxu - Se agrego la columna balance con el valor de $RunningTotal
		if($myrow['type']==10){ // bowikaxu realhost Feb 2008 - es factura cliente, ver link detalle
			printf("<td>%s</td>
					<td>%s</td>
				<td><A HREF='%s'>%s</A></td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<TD><A HREF='rh_PrintCustTrans.php?FromTransNo=%s&InvOrCredit=Invoice' TARGET=blank><IMG BORDER=0 SRC='%s'></A></TD>
				</tr>",
				$myrow['typename'],
				$FormatedTranDate,
				$URL_to_TransDetail,
				$myrow['typeno'],
				$myrow['narrative'],
				$DebitAmount,
				$CreditAmount,
				number_format($RunningTotal,2),
				$files_str,
				$myrow['typeno'],
				$rootpath.'/css/'.$theme.'/images/preview.gif');
		}else if($myrow['type']==20){ // factura de proveedor
			printf("<td>%s</td>
					<td>%s</td>
				<td><A HREF='%s'>%s</A></td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<TD><A HREF='rh_SuppInvoice_Details.php?&Transno=%s' TARGET=blank><IMG SRC='%s'></A></TD>
				</tr>",
				$myrow['typename'],
				$FormatedTranDate,
				$URL_to_TransDetail,
				$myrow['typeno'],
				$myrow['narrative'],
				$DebitAmount,
				$CreditAmount,
				number_format($RunningTotal,2),
				$files_str,
				$myrow['typeno'],
				$rootpath.'/css/'.$theme.'/images/preview.gif');
		}else {
			printf("<td>%s</td>
				<td>%s</td>
				<td><A HREF='%s'>%s</A></td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				</tr>",
				$myrow['typename'],
				$FormatedTranDate,
				$URL_to_TransDetail,
				$myrow['typeno'],
				$myrow['narrative'],
				$DebitAmount,
				$CreditAmount,
				number_format($RunningTotal,2),
				$files_str);
		}

		$j++;
		/*
		if ($j == 18){
			echo $TableHeader;
			$j=1;
		}
		*/
		
	}

	// bowikaxu realhost - may 2008 - show credit/debit totals and total balance
	echo "<TR bgcolor='#FDFEEF'>
				<TD ALIGN=LEFT><B>"._('Total')."</B></TD>
				<TD ALIGN=RIGHT COLSPAN=4><B>" . number_format(($DebitTotal),2) . '</B></TD>
				<TD ALIGN=RIGHT><B>' . number_format(($CreditTotal),2) . '</B></TD></TR>';
	
	echo "<TR bgcolor='#FDFEEF'><TD COLSPAN=5><B>";
	if ($PandLAccount==True){
		echo _('Total Period Movement');
	} else { /*its a balance sheet account*/
		echo _('Balance C/Fwd');
	}
	echo '</B></TD>';

	if ($RunningTotal >0){
		echo '<TD ALIGN=RIGHT><B>' . number_format(($RunningTotal),2) . '</B></TD></TR>';
	}else {
		echo '<TD></TD><TD ALIGN=RIGHT><B>' . number_format((-$RunningTotal),2) . '</B></TD></TR>';
	}
	echo '</table>';
} /* end of if Show button hit */



if ($ShowIntegrityReport){

	prnMsg( _('There are differences between the sum of the transactions and the recorded movements in the ChartDetails table') . '. ' . _('A log of the account differences for the periods report shows below'),'warn');
	echo '<P>'.$IntegrityReport;
}
include('includes/footer.inc');
?>
