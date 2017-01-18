<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-29 10:26:47 -0600 (Fri, 29 Feb 2008) $
 * $Rev: 97 $
 */
/* webERP Revision: 1.8 $ */

$PageSecurity = 10;

include('includes/session.inc');

$title = _('Bank Accounts Maintenance');

include('includes/header.inc');

// bowikaxu realhost - verify currency.
echo '<script language="javascript">
function verify_currency(seleccion){';
echo "if(seleccion != '".$_SESSION['CompanyRecord']['currencydefault']."'){
		// show the other account field
		document.forms[1].CompAccount.disabled = false;
	}else {
		document.forms[1].CompAccount.disabled = true;
	}
}";
echo "</script>";

if (isset($_GET['SelectedBankAccount'])) {
	$SelectedBankAccount=$_GET['SelectedBankAccount'];
} elseif (isset($_POST['SelectedBankAccount'])) {
	$SelectedBankAccount=$_POST['SelectedBankAccount'];
}

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	if (strlen($_POST['BankAccountName']) >50) {
		$InputError = 1;
		prnMsg(_('The bank account name must be fifty characters or less long'),'error');
	}  elseif ( trim($_POST['BankAccountName']) == '' ) {
		$InputError = 1;
		prnMsg(_('The bank account name may not be empty.'),'error');
	} elseif (strlen($_POST['BankAccountNumber']) >50) {
		$InputError = 1;
		prnMsg(_('The bank account number must be fifty characters or less long'),'error');
	}  elseif (strlen($_POST['BankAddress']) >50) {
		$InputError = 1;
		prnMsg(_('The bank address must be fifty characters or less long'),'error');
	}

	if (isset($SelectedBankAccount) AND $InputError !=1) {
		
		/*Check if there are already transactions against this account - cant allow change currency if there are*/
		
		$sql = 'SELECT * FROM banktrans WHERE bankact=' . $SelectedBankAccount;
		$BankTransResult = DB_query($sql,$db);
		if (DB_num_rows($BankTransResult)>0) {
			// bowikaxu realhost - january 2008 - update complemetary account if needed
			if(!isset($_POST['CompAccount']) OR $_POST['CompAccount']==''){
				$_POST['CompAccount']='';
			}
			
			$sql = "UPDATE bankaccounts
				SET bankaccountname='" . $_POST['BankAccountName'] . "',
				bankaccountnumber='" . $_POST['BankAccountNumber'] . "',
				bankaddress='" . $_POST['BankAddress'] . "',
				rh_acctcomplementary='" . $_POST['CompAccount'] . "'
			WHERE accountcode = '" . $SelectedBankAccount . "'";
			prnMsg(_('Note that it is not possible to change the currency of the account once there are transactions against it'),'warn');
		} else {
			$sql = "UPDATE bankaccounts
				SET bankaccountname='" . $_POST['BankAccountName'] . "',
				bankaccountnumber='" . $_POST['BankAccountNumber'] . "',
				bankaddress='" . $_POST['BankAddress'] . "',
				currcode ='" . $_POST['CurrCode'] . "',
				rh_acctcomplementary='" . $_POST['CompAccount'] . "'
				WHERE accountcode = '" . $SelectedBankAccount . "'";
		}

		$msg = _('The bank account details have been updated');
	} elseif ($InputError !=1) {

	/*Selectedbank account is null cos no item selected on first time round so must be adding a    record must be submitting new entries in the new bank account form */
// bowikaxu realhost january 2008 - insert the complementary account if needed
if(!isset($_POST['CompAccount']) OR $_POST['CompAccount']==''){
	$_POST['CompAccount']='';
}
		$sql = "INSERT INTO bankaccounts (
						accountcode,
						bankaccountname,
						bankaccountnumber,
						rh_acctcomplementary,
						bankaddress,
						currcode)
				VALUES ('" . $_POST['AccountCode'] . "',
					'" . $_POST['BankAccountName'] . "',
					'" . $_POST['BankAccountNumber'] . "',
					'" . $_POST['CompAccount'] . "', 
					'" . $_POST['BankAddress'] . "', 
					'" . $_POST['CurrCode'] . "'
					)";
		$msg = _('The new bank account has been entered');
	}

	//run the SQL from either of the above possibilites
	if( $InputError !=1 ) {
		$ErrMsg = _('The bank account could not be inserted or modified because');
		$Dbgmsg = _('The SQL used to insert/modify the bank account details was');
		$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
	
		prnMsg($msg,'success');
		unset($_POST['AccountCode']);
		unset($_POST['BankAccountName']);
		unset($_POST['BankAccountNumber']);
		unset($_POST['BankAddress']);
		unset($_POST['CurrCode']);
		// bowikaxu realhost january 2008 - unset complemetary account
		unset($_POST['CompAccount']);
		unset($SelectedBankAccount);
	}
	

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;

// PREVENT DELETES IF DEPENDENT RECORDS IN 'BankTrans'

	$sql= "SELECT COUNT(*) FROM banktrans WHERE banktrans.bankact='$SelectedBankAccount'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		$CancelDelete = 1;
		prnMsg(_('Cannot delete this bank account because transactions have been created using this account'),'warn');
		echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('transactions with this bank account code');

	}
	if (!$CancelDelete) {
		$sql="DELETE FROM bankaccounts WHERE accountcode='$SelectedBankAccount'";
		$result = DB_query($sql,$db);
		prnMsg(_('Bank account deleted'),'success');
	} //end if Delete bank account
	
	unset($_GET['delete']);
	unset($SelectedBankAccount);
}

/* Always show the list of accounts */
If (!isset($SelectedBankAccount)) {
	// bowikaxu realhost - january 2008 - get the complementary account
	$sql = "SELECT bankaccounts.accountcode,
			chartmaster.accountname,
			bankaccountname,
			bankaccountnumber,
			bankaddress,
			currcode,
			rh_acctcomplementary
		FROM bankaccounts,
			chartmaster
		WHERE bankaccounts.accountcode = chartmaster.accountcode";
	
	$ErrMsg = _('The bank accounts set up could not be retreived because');
	$Dbgmsg = _('The SQL used to retrieve the bank account details was') . '<BR>' . $sql;
	$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
	
	echo '<CENTER><table>';
	
	echo "<tr><td class='tableheader'>" . _('GL Account') . "</td>
		<td class='tableheader'>" . _('Account Name') . "</td>
		<td class='tableheader'>" . _('Account Number') . "</td>
		<td class='tableheader'>" . _('Complementary Account Number') . "</td>
		<td class='tableheader'>" . _('Bank Address') . "</td>
		<td class='tableheader'>" . _('Currency') . "</td>
	</tr>";
	
	$k=0; //row colour counter
	while ($myrow = DB_fetch_row($result)) {
		
		// bowikaxu realhost Feb 2008 - get the complementary account name
		$sql = "SELECT accountname FROM chartmaster WHERE accountcode = '".$myrow[6]."'";
		$res = DB_query($sql,$db);
		$CompName = DB_fetch_array($res);
		
	if ($k==1){
		echo "<tr bgcolor='#CCCCCC'>";
		$k=0;
	} else {
		echo "<tr bgcolor='#EEEEEE'>";
		$k++;
	}
	
	printf("<td>%s<BR><FONT SIZE=2>%s</FONT></td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td><a href=\"%s?SelectedBankAccount=%s\">" . _('Edit') . "</td>
		<td><a href=\"%s?SelectedBankAccount=%s&delete=1\">" . _('Delete') . "</td>
		</tr>",
		$myrow[0],
		$myrow[1],
		$myrow[2],
		$myrow[3],
		$CompName['accountname'].' ['.$myrow[6].']',
		$myrow[4],
		$myrow[5],
		$_SERVER['PHP_SELF'],
		$myrow[0],
		$_SERVER['PHP_SELF'],
		$myrow[0]);
	
	}
	//END WHILE LIST LOOP
	
	
	echo '</CENTER></table><p>';
}

if (isset($SelectedBankAccount)) {
	echo '<P>';
	echo '<CENTER><P><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '">' . _('Show All Bank Accounts Defined') . '</A></CENTER>';
	echo '<P>';
}

echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . ">";

if (isset($SelectedBankAccount) AND !isset($_GET['delete'])) {
	//editing an existing bank account  - not deleting

	// bowikaxu realhost - january 2008 - select the complementary account
	$sql = "SELECT accountcode,
			bankaccountname,
			bankaccountnumber,
			bankaddress,
			currcode,
			rh_acctcomplementary
		FROM bankaccounts
		WHERE bankaccounts.accountcode='$SelectedBankAccount'";

	$result = DB_query($sql, $db);
	$myrow = DB_fetch_array($result);

	$_POST['AccountCode'] = $myrow['accountcode'];
	$_POST['BankAccountName']  = $myrow['bankaccountname'];
	$_POST['BankAccountNumber'] = $myrow['bankaccountnumber'];
	$_POST['BankAddress'] = $myrow['bankaddress'];
	$_POST['CurrCode'] = $myrow['currcode'];
	// bowikaxu realhost january 2008 - set the post to its value
	$_POST['CompAccount'] = $myrow['rh_acctcomplementary']; 

	echo '<INPUT TYPE=HIDDEN NAME=SelectedBankAccount VALUE=' . $SelectedBankAccount . '>';
	echo '<INPUT TYPE=HIDDEN NAME=AccountCode VALUE=' . $_POST['AccountCode'] . '>';
	echo '<CENTER><TABLE> <TR><TD>' . _('Bank Account GL Code') . ':</TD><TD>';
	echo $_POST['AccountCode'] . '</TD></TR>';
} else { //end of if $Selectedbank account only do the else when a new record is being entered
	echo '<CENTER><TABLE><TR><TD>' . _('Bank Account GL Code') . ":</TD><TD><Select name='AccountCode'>";

	$sql = "SELECT accountcode,
			accountname
		FROM chartmaster,
			accountgroups
		WHERE chartmaster.group_ = accountgroups.groupname
		AND accountgroups.pandl = 0
		ORDER BY accountcode";

	$result = DB_query($sql,$db);
	while ($myrow = DB_fetch_array($result)) {
		if ($myrow['accountcode']==$_POST['AccountCode']) {
			echo '<OPTION SELECTED VALUE=';
		} else {
			echo '<OPTION VALUE=';
		}
		echo $myrow['accountcode'] . '>' . $myrow['accountname'];

	} //end while loop

	echo '</SELECT></TD></TR>';
}

echo '<TR><TD>' . _('Bank Account Name') . ': </TD>
			<TD><input type="Text" name="BankAccountName" value="' . $_POST['BankAccountName'] . '" SIZE=40 MAXLENGTH=50></TD></TR>
		<TR><TD>' . _('Bank Account Number') . ': </TD>
			<TD><input type="Text" name="BankAccountNumber" value="' . $_POST['BankAccountNumber'] . '" SIZE=40 MAXLENGTH=50></TD></TR>
		<TR><TD>' . _('Bank Address') . ': </TD>
			<TD><input type="Text" name="BankAddress" value="' . $_POST['BankAddress'] . '" SIZE=40 MAXLENGTH=50></TD></TR>
		<TR><TD>' . _('Currency Of Account') . ': </TD><TD><select name="CurrCode"  onchange="verify_currency(this.value)">';

if (!isset($_POST['CurrCode']) OR $_POST['CurrCode']==''){
	$_POST['CurrCode'] = $_SESSION['CompanyRecord']['currencydefault'];
}
$result = DB_query('SELECT currabrev, currency FROM currencies',$db);
while ($myrow = DB_fetch_array($result)) {
	if ($myrow['currabrev']==$_POST['CurrCode']) {
		echo '<OPTION SELECTED VALUE=';
	} else {
		echo '<OPTION VALUE=';
	}
	echo $myrow['currabrev'] . '>' . $myrow['currabrev'];
} //end while loop

echo '</SELECT></TD></TR>';

if($_POST['CurrCode']==$_SESSION['CompanyRecord']['currencydefault']){
	$disabled = "disabled = 'true'";
}else {
	$disables = "disabled='false'";
}

// bowikaxu realhost january 2008 - if selected currency is different to the company default, select another account for gltrans
	echo '<TR><TD>' . _('Complementary Account') . ":</TD><TD><Select name='CompAccount' ".$disabled.">";

	$sql = "SELECT accountcode,
			accountname
		FROM chartmaster,
			accountgroups
		WHERE chartmaster.group_ = accountgroups.groupname
		AND accountgroups.pandl = 0
		ORDER BY accountcode";

	$result = DB_query($sql,$db);
	while ($myrow = DB_fetch_array($result)) {
		if ($myrow['accountcode']==$_POST['CompAccount']) {
			echo '<OPTION SELECTED VALUE=';
		} else {
			echo '<OPTION VALUE=';
		}
		echo $myrow['accountcode'] . '>' . $myrow['accountname'];

	} //end while loop
	echo '</SELECT></TD></TR>';

// bowikaxu realhost - end select complementary account

echo '</TABLE>
		<CENTER><input type="Submit" name="submit" value="'. _('Enter Information') .'"></CENTER>';

echo '</FORM>';
include('includes/footer.inc');
?>