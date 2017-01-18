<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
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
        
echo "</TABLE><P>
<INPUT TYPE=SUBMIT NAME='Show' VALUE='"._('Show Account Transactions')."'></CENTER></FORM>";

/* End of the Form  rest of script is what happens if the show button is hit*/

if (isset($_POST['Show'])){

	if (!isset($SelectedPeriod)){
		prnMsg(_('A period or range of periods must be selected from the list box'),'info');
		include('includes/footer.inc');
		exit;
	}

	$FirstPeriodSelected = min($SelectedPeriod)-1;
	$LastPeriodSelected = max($SelectedPeriod);

 	$sql= "SELECT banktrans.transno,
			banktrans.type,
			banktrans.rh_chequeno,
			systypes.typename,
			banktrans.ref,
			banktrans.transdate,
			banktrans.amount,
			banktrans.rh_chequeno
		FROM banktrans, systypes
		WHERE banktrans.bankact = '$SelectedAccount'
		AND systypes.typeid=banktrans.type
		AND banktrans.transdate >= (SELECT MIN(lastdate_in_period) FROM periods WHERE periods.periodno>=$FirstPeriodSelected)
		AND banktrans.transdate <= (SELECT lastdate_in_period FROM periods WHERE periods.periodno=$LastPeriodSelected)
		AND rh_chequeno != ''";

	$ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because') ;
	$TransResult = DB_query($sql,$db,$ErrMsg);

	echo '<table align=center>';
		
		$TableHeader = "<TR>
			<TD class='tableheader'>" . _('Type') . "</TD>
			<TD class='tableheader'>" . _('Number') . "</TD>
			<TD class='tableheader'>" . _('Date') . "</TD>
			<TD class='tableheader'>" . _('Cheque').' '._('Number') . "</TD>
			<TD class='tableheader'>" . _('Amount') . "</TD>
			<TD class='tableheader'>" . _('Narrative') . '</TD>
			</TR>';		
		
	echo $TableHeader;

	
	$PeriodTotal = 0;
	$ShowIntegrityReport = False;
	$j = 1;
	$k=0; //row colour counter

	while ($myrow=DB_fetch_array($TransResult)) {

		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k++;
		}
		
		$PeriodTotal += $myrow['amount'];

		$FormatedTranDate = ConvertSQLDate($myrow['transdate']);
		$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['transno'];
		// bowikaxu - Se agrego la columna balance con el valor de $RunningTotal
		printf("<td>%s</td>
			<td><A HREF='%s'>%s</A></td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			",
			$myrow['typename'],
			$URL_to_TransDetail,
			$myrow['transno'],
			$FormatedTranDate,
			$myrow['rh_chequeno'],
			number_format($myrow['amount'],2),
			$myrow['ref']);

		echo "</TR>";
		$j++;

		If ($j == 18){
			echo $TableHeader;
			$j=1;
		}
		
	}

	echo "<TR bgcolor='#FDFEEF'><TD COLSPAN=3><B>";
	
	echo _('Total');
	echo '</B></TD>';
	echo "<TD>".$PeriodTotal."</TD></TR>";

	echo '</table>';
} /* end of if Show button hit */

include('includes/footer.inc');
?>
