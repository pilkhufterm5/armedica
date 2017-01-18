<?php

/* $Revision: 294 $ */
/* $Revision: 294 $ */

$PageSecurity = 3;

include('includes/session.inc');

$title = _('Customer Maintenance');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if (isset($Errors)) {
	unset($Errors);
}
$Errors = array();

if ($_POST['submit']) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;
	$i=1;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	$_POST['DebtorNo'] = strtoupper($_POST['DebtorNo']);

	// bowikaxu - april 2007 - increase customer name
	if (strlen($_POST['CustName']) > 100 OR strlen($_POST['CustName'])==0) {
		$InputError = 1;
		prnMsg( _('The customer name must be entered and be hundred characters or less long'),'error');
		$Errors[$i] = 'CustName';
		$i++;
	} elseif ($_SESSION['AutoDebtorNo']==0 AND strlen($_POST['DebtorNo']) ==0) {
		$InputError = 1;
		prnMsg( _('The debtor code cannot be empty'),'error');
		$Errors[$i] = 'DebtorNo';
		$i++;
	} elseif ($_SESSION['AutoDebtorNo']==0 AND ContainsIllegalCharacters($_POST['DebtorNo'])) {
		$InputError = 1;
		prnMsg( _('The customer code cannot contain any of the following characters') . " . - ' & + \" " . _('or a space'),'error');
		$Errors[$i] = 'DebtorNo';
		$i++;
//	} elseif (ContainsIllegalCharacters($_POST['Address1']) OR ContainsIllegalCharacters($_POST['Address2'])) {
//		$InputError = 1;
//		prnMsg( _('Lines of the address  must not contain illegal characters'),'error');
//rleal Mar 3, 2010 Se agregaron las address7-10 para FE
	} elseif (strlen($_POST['Address1']) >90) {
		$InputError = 1;
		prnMsg( _('The Line 1 of the address must be forty characters or less long'),'error');
		$Errors[$i] = 'Address1';
		$i++;
	} elseif (strlen($_POST['Address2']) >90) {
		$InputError = 1;
		prnMsg( _('The Line 2 of the address must be forty characters or less long'),'error');
		$Errors[$i] = 'Address2';
		$i++;
	} elseif (strlen($_POST['Address3']) >90) {
		$InputError = 1;
		prnMsg( _('The Line 3 of the address must be forty characters or less long'),'error');
		$Errors[$i] = 'Address3';
		$i++;
	} elseif (strlen($_POST['Address4']) >90) {
		$InputError = 1;
		prnMsg( _('The Line 4 of the address must be fifty characters or less long'),'error');
		$Errors[$i] = 'Address4';
		$i++;
	} elseif (strlen($_POST['Address5']) >90) {
		$InputError = 1;
		prnMsg( _('The Line 5 of the address must be forty characters or less long'),'error');
		$Errors[$i] = 'Address5';
		$i++;
	} elseif (strlen($_POST['Address6']) >90) {
		$InputError = 1;
		prnMsg( _('The Line 6 of the address must be fifteen characters or less long'),'error');
		$Errors[$i] = 'Address6';
		$i++;
	} elseif (strlen($_POST['Address7']) >90) {
		$InputError = 1;
		prnMsg( _('The Line 6 of the address must be fifteen characters or less long'),'error');
		$Errors[$i] = 'Address6';
		$i++;
	} elseif (strlen($_POST['Address8']) >90) {
		$InputError = 1;
		prnMsg( _('The Line 6 of the address must be fifteen characters or less long'),'error');
		$Errors[$i] = 'Address6';
		$i++;
	} elseif (strlen($_POST['Address9']) >90) {
		$InputError = 1;
		prnMsg( _('The Line 6 of the address must be fifteen characters or less long'),'error');
		$Errors[$i] = 'Address6';
		$i++;
	} elseif (strlen($_POST['Address10']) >20) {
		$InputError = 1;
		prnMsg( _('The Line 6 of the address must be fifteen characters or less long'),'error');
		$Errors[$i] = 'Address6';
		$i++;

	} elseif (!is_numeric($_POST['CreditLimit'])) {
		$InputError = 1;
		prnMsg( _('The credit limit must be numeric'),'error');
		$Errors[$i] = 'CreditLimit';
		$i++;
	} elseif (!is_numeric($_POST['PymtDiscount'])) {
		$InputError = 1;
		prnMsg( _('The payment discount must be numeric'),'error');
		$Errors[$i] = 'PymtDiscount';
		$i++;
	} elseif (!is_date($_POST['ClientSince'])) {
		$InputError = 1;
		prnMsg( _('The customer since field must be a date in the format') . ' ' . $_SESSION['DefaultDateFormat'],'error');
		$Errors[$i] = 'ClientSince';
		$i++;
	} elseif (!is_numeric($_POST['Discount'])) {
		$InputError = 1;
		prnMsg( _('The discount percentage must be numeric'),'error');
		$Errors[$i] = 'Discount';
		$i++;
	} elseif ((double) $_POST['CreditLimit'] <0) {
		$InputError = 1;
		prnMsg( _('The credit limit must be a positive number'),'error');
		$Errors[$i] = 'CreditLimit';
		$i++;
	} elseif (((double) $_POST['PymtDiscount']> 10) OR ((double) $_POST['PymtDiscount'] <0)) {
		$InputError = 1;
		prnMsg( _('The payment discount is expected to be less than 10% and greater than or equal to 0'),'error');
		$Errors[$i] = 'PymtDiscount';
		$i++;
	} elseif (((double) $_POST['Discount']> 100) OR ((double) $_POST['Discount'] <0)) {
		$InputError = 1;
		prnMsg( _('The discount is expected to be less than 100% and greater than or equal to 0'),'error');
		$Errors[$i] = 'Discount';
		$i++;
	}

	if ($InputError !=1){

		$SQL_ClientSince = FormatDateForSQL($_POST['ClientSince']);

		if (!isset($_POST['New'])) {

			// bowikaxu - verificar que no exista el RFC 15/02/2007

//			$sql = "SELECT taxref FROM debtorsmaster WHERE taxref ='".$_POST['TaxRef']."' AND debtorno != '".$_POST['DebtorNo']."'";
//			$res = DB_query($sql,$db,'ERROR: Imposible verificar el RFC');
//			if(DB_num_rows($res)>=1){
//
//				prnMsg( _('Error: El RFC ya Existe en la Base de Datos'),'error');
//
//			}else {

			$sql = "SELECT currcode FROM debtorsmaster WHERE debtorno = '".$_POST['DebtorNo']."'";
			$res2 = DB_query($sql,$db);
			$old_curr = DB_fetch_array($res2);
			if($old_curr['currcode'] != $_POST['CurrCode']){
				// verificar que no haya transacciones de este cliente
					$CancelDelete = 0;

				// PREVENT DELETES IF DEPENDENT RECORDS IN 'DebtorTrans'

					$sql= "SELECT COUNT(*) FROM debtortrans WHERE debtorno='" . $_POST['DebtorNo'] . "'";
					$result = DB_query($sql,$db);
					$myrow = DB_fetch_row($result);
					if ($myrow[0]>0) {
						$CancelDelete = 1;
						prnMsg( _('Este cliente no puede cambiar de moneda debido a que ya tiene transacciones en el sistema.'),'warn');
						echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('transactions against this customer');

					} else {
						$sql= "SELECT COUNT(*) FROM salesorders WHERE debtorno='" . $_POST['DebtorNo'] . "'";
						$result = DB_query($sql,$db);
						$myrow = DB_fetch_row($result);
						if ($myrow[0]>0) {
							$CancelDelete = 1;
							prnMsg( _('Este cliente no puede cambiar de moneda debido a que ya tiene transacciones en el sistema.'),'warn');
							echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('orders against this customer');
						} else {
							$sql= "SELECT COUNT(*) FROM salesanalysis WHERE cust='" . $_POST['DebtorNo'] . "'";
							$result = DB_query($sql,$db);
							$myrow = DB_fetch_row($result);
							if ($myrow[0]>0) {
								$CancelDelete = 1;
								prnMsg( _('Este cliente no puede cambiar de moneda debido a que ya tiene transacciones en el sistema.'),'warn');
								echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('sales analysis records against this customer');
							}
						}

					}
			}
			if($CancelDelete==0){
			// fin verificar RFC y currencies
			//rleal Mar 3, 2010 Se agregaron las address7-10 para FE
			$sql = "UPDATE debtorsmaster SET
					name='" . DB_escape_string($_POST['CustName']) . "',
					name2='" . DB_escape_string($_POST['CustName2']) . "',
					address1='" . DB_escape_string($_POST['Address1']) . "',
					address2='" . DB_escape_string($_POST['Address2']) . "',
					address3='" . DB_escape_string($_POST['Address3']) ."',
					address4='" . DB_escape_string($_POST['Address4']) . "',
					address5='" . DB_escape_string($_POST['Address5']) . "',
					address6='" . DB_escape_string($_POST['Address6']) . "',
					address7='" . DB_escape_string($_POST['Address7']) ."',
					address8='" . DB_escape_string($_POST['Address8']) . "',
					address9='" . DB_escape_string($_POST['Address9']) . "',
					address10='" . DB_escape_string($_POST['Address10']) . "',
					rh_Tel = '".$_POST['rh_Tel']."',
					currcode='" . $_POST['CurrCode'] . "',
					clientsince='$SQL_ClientSince',
					holdreason='" . $_POST['HoldReason'] . "',
					paymentterms='" . $_POST['PaymentTerms'] . "',
					discount=" . ($_POST['Discount'])/100 . ",
					discountcode='" . $_POST['DiscountCode'] . "',
					pymtdiscount=" . ($_POST['PymtDiscount'])/100 . ",
					creditlimit=" . $_POST['CreditLimit'] . ",
					salestype = '" . $_POST['SalesType'] . "',
					invaddrbranch='" . $_POST['AddrInvBranch'] . "',
					taxref='" . DB_escape_string($_POST['TaxRef']) . "',
					customerpoline='" . $_POST['CustomerPOLine'] . "'
				WHERE debtorno = '" . $_POST['DebtorNo'] . "'";

			$ErrMsg = _('The customer could not be updated because');
			if($result = DB_query($sql,$db,$ErrMsg)){
				$UpdateDebtorNo =
				"UPDATE rh_titular SET
                    name2 = '" . DB_escape_string($_POST['CustName2']) . "',
                    address1 = '" . DB_escape_string($_POST['Address1']) . "',
                    address2 = '" . DB_escape_string($_POST['Address2']) . "',
                    address3 = '" . DB_escape_string($_POST['Address3']) . "',
                    address4 = '" . DB_escape_string($_POST['Address4']) . "',
                    address5 = '" . DB_escape_string($_POST['Address5']) . "',
                    address6 = '" . DB_escape_string($_POST['Address6']) . "',
                    address7 = '" . DB_escape_string($_POST['Address7']) . "',
                    address8 = '" . DB_escape_string($_POST['Address8']) . "',
                    address9 = '" . DB_escape_string($_POST['Address9']) . "',
                    address10 = '" . DB_escape_string($_POST['Address10']) . "',
                    rh_tel = '" . $_POST['rh_Tel'] . "',
                    taxref = '" . $_POST['TaxRef'] . "',
                    fecha_ingreso = '" . $SQL_ClientSince . "'
                WHERE debtorno = '" . $_POST['DebtorNo'] . "'
                ";

                DB_query($UpdateDebtorNo,$db);
			}
			// bowikaxu realhost august 07 - update debtors bank accounts
			$SQL = "DELETE FROM rh_debtorsacts WHERE debtorno = '".$_POST['DebtorNo']."'";
				DB_query($SQL,$db);
			if(isset($_POST['Banks'])){
				foreach($_POST['Banks'] as $bank){
					$SQL = "INSERT INTO rh_debtorsacts (debtorno, accountcode) VALUES ('".$_POST['DebtorNo']."', '".$bank."')";
					DB_query($SQL,$db);
				}
			}
			prnMsg( _('Customer updated'),'success');
			}
//			}
		} else { //it is a new customer
			/* set the DebtorNo if $AutoDebtorNo in config.php has been set to
			something greater 0 */
			if ($_SESSION['AutoDebtorNo'] > 0) {
				/* system assigned, sequential, numeric */
				if ($_SESSION['AutoDebtorNo']== 1) {
					$_POST['DebtorNo'] = GetNextTransNo(500, $db);
                    $_POST['DebtorNo'] =str_pad($_POST['DebtorNo'], 5, "0", STR_PAD_LEFT);
				}
			}

			// bowikaxu - verificar que no exista el RFC 15/02/2007
			//rleal Ene 23 2011
			$sql = "SELECT taxref FROM debtorsmaster WHERE taxref ='".$_POST['TaxRef']."'";
			$res = DB_query($sql,$db,'ERROR: Imposible verificar el RFC');
			if(DB_num_rows($res)>=1 && $_POST['TaxRef'] != 'XAXX010101000' && $_POST['TaxRef'] != 'XEXX010101000' && 1 != 1){

				prnMsg( _('Error: El RFC ya Existe en la Base de Datos'),'error' . $_POST['TaxRef']);

			}else {

			// fin verificar RFC

			// bowikaxu realhost March 2008 - reemplazar espacios en los codigos de clientes
			$_POST['DebtorNo'] = str_replace(" ","",$_POST['DebtorNo']);
			//rleal Mar 3, 2010 Se agregaron las address7-10 para FE
			$sql = "INSERT INTO debtorsmaster (
							debtorno,
							name,
							name2,
							address1,
							address2,
							address3,
							address4,
							address5,
							address6,
							address7,
							address8,
							address9,
							address10,
							rh_Tel,
							currcode,
							clientsince,
							holdreason,
							paymentterms,
							discount,
							discountcode,
							pymtdiscount,
							creditlimit,
							salestype,
							invaddrbranch,
							taxref,
							customerpoline)
				VALUES ('" . $_POST['DebtorNo'] ."',
					'" . DB_escape_string($_POST['CustName']) ."',
					'" . DB_escape_string($_POST['CustName2']) ."',
					'" . DB_escape_string($_POST['Address1']) ."',
					'" . DB_escape_string($_POST['Address2']) ."',
					'" . DB_escape_string($_POST['Address3']) . "',
					'" . DB_escape_string($_POST['Address4']) . "',
					'" . DB_escape_string($_POST['Address5']) . "',
					'" . DB_escape_string($_POST['Address6']) . "',
					'" . DB_escape_string($_POST['Address7']) . "',
					'" . DB_escape_string($_POST['Address8']) . "',
					'" . DB_escape_string($_POST['Address9']) . "',
					'" . DB_escape_string($_POST['Address10']) . "',
					'".$_POST['rh_Tel']."',
					'" . $_POST['CurrCode'] . "',
					'" . $SQL_ClientSince . "',
					" . $_POST['HoldReason'] . ",
					'" . $_POST['PaymentTerms'] . "',
					" . ($_POST['Discount'])/100 . ",
					'" . $_POST['DiscountCode'] . "',
					" . ($_POST['PymtDiscount'])/100 . ",
					" . $_POST['CreditLimit'] . ",
					'" . $_POST['SalesType'] . "',
					'" . $_POST['AddrInvBranch'] . "',
					'" . DB_escape_string($_POST['TaxRef']) . "',
					'" . $_POST['CustomerPOLine'] . "'
					)";

			$ErrMsg = _('This customer could not be added because');
			$result = DB_query($sql,$db,$ErrMsg);
			// bowikaxu realhost august 07 - insert the debtors bank accounts
			foreach($_POST['Banks'] as $bank){
				//$SQL = "DELETE FROM rh_debtorsacts WHERE debtorno = '".$_POST['DebtorNo']."'";
				//$res = DB_query($SQL,$db);
				$SQL = "INSERT INTO rh_debtorsacts (debtorno, accountcode) VALUES ('".$_POST['DebtorNo']."', '".$bank."')";
				DB_query($SQL,$db);
			}

			$BranchCode = $_POST['DebtorNo'];
			//rleal Mar 3, 2010 Se agregaron las address7-10 para FE
			echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath ."/CustomerBranches.php?" . SID . "&DebtorNo=" . $_POST['DebtorNo'] ."&BrName=" . $_POST['CustName'] .'&BranchCode=' . $BranchCode . '&BrAddress1=' . $_POST['Address1'] . '&BrAddress2=' . $_POST['Address2'] . '&BrAddress3=' . $_POST['Address3'] . '&BrAddress4=' . $_POST['Address4'] . '&BrAddress5=' . $_POST['Address5'] . '&BrAddress6=' . $_POST['Address6'] . '&BrAddress7=' . $_POST['Address7'] . '&BrAddress8=' . $_POST['Address8'] . '&BrAddress9=' . $_POST['Address9'] . '&BrAddress10=' . $_POST['Address10'] . "'>";
			//rleal Mar 3, 2010 Se agregaron las address7-10 para FE
			echo '<P>' . _('You should automatically be forwarded to the entry of a new Customer Branch page') .
			'. ' . _('If this does not happen') .' (' . _('if the browser does not support META Refresh') . ') ' .
			"<A HREF='" . $rootpath . "/CustomerBranches.php?" . SID . "&DebtorNo=" . $_POST['DebtorNo'] ."&BrName=" . $_POST['CustName'] .'&BranchCode=' . $BranchCode . '&BrAddress1=' . $_POST['Address1'] . '&BrAddress2=' . $_POST['Address2'] . '&BrAddress3=' . $_POST['Address3'] . '&BrAddress4=' . $_POST['Address4'] . '&BrAddress5=' . $_POST['Address5'] . '&BrAddress6=' . $_POST['Address6'] . '&BrAddress7=' . $_POST['Address7'] . '&BrAddress8=' . $_POST['Address8'] . '&BrAddress9=' . $_POST['Address9'] . '&BrAddress10=' . $_POST['Address10'] . "'>" . _('click here') . '</a> ' . _('to continue') . '.<BR>';

			include('includes/footer.inc');
			exit;
			}
		}
	} else {
		prnMsg( _('Validation failed') . '. ' . _('No updates or deletes took place'),'error');
	}

} elseif (isset($_POST['delete'])) {

//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;

// PREVENT DELETES IF DEPENDENT RECORDS IN 'DebtorTrans'

	$sql= "SELECT COUNT(*) FROM debtortrans WHERE debtorno='" . $_POST['DebtorNo'] . "'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		$CancelDelete = 1;
		prnMsg( _('This customer cannot be deleted because there are transactions that refer to it'),'warn');
		echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('transactions against this customer');

	} else {
		$sql= "SELECT COUNT(*) FROM salesorders WHERE debtorno='" . $_POST['DebtorNo'] . "'";
		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);
		if ($myrow[0]>0) {
			$CancelDelete = 1;
			prnMsg( _('Cannot delete the customer record because orders have been created against it'),'warn');
			echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('orders against this customer');
		} else {
			$sql= "SELECT COUNT(*) FROM salesanalysis WHERE cust='" . $_POST['DebtorNo'] . "'";
			$result = DB_query($sql,$db);
			$myrow = DB_fetch_row($result);
			if ($myrow[0]>0) {
				$CancelDelete = 1;
				prnMsg( _('Cannot delete this customer record because sales analysis records exist for it'),'warn');
				echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('sales analysis records against this customer');
			} else {
				$sql= "SELECT COUNT(*) FROM custbranch WHERE debtorno='" . $_POST['DebtorNo'] . "'";
				$result = DB_query($sql,$db);
				$myrow = DB_fetch_row($result);
				if ($myrow[0]>0) {
					$CancelDelete = 1;
					prnMsg(_('Cannot delete this customer because there are branch records set up against it'),'warn');
					echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('branch records relating to this customer');
				}
			}
		}

	}
	if ($CancelDelete==0) { //ie not cancelled the delete as a result of above tests
		$sql="DELETE FROM debtorsmaster WHERE debtorno='" . $_POST['DebtorNo'] . "'";
		$result = DB_query($sql,$db);
		prnMsg( _('Customer') . ' ' . $_POST['DebtorNo'] . ' ' . _('has been deleted') . ' !','success');
		include('includes/footer.inc');
		exit;
	} //end if Delete Customer
}

if($reset){
	//rleal Mar 3, 2010 Se agregaron las address7-10 para FE
	unset($_POST['CustName']);
	unset($_POST['CustName2']);
	unset($_POST['Address1']);
	unset($_POST['Address2']);
	unset($_POST['Address3']);
	unset($_POST['Address4']);
	unset($_POST['Address5']);
	unset($_POST['Address6']);
	unset($_POST['Address7']);
	unset($_POST['Address8']);
	unset($_POST['Address9']);
	unset($_POST['Address10']);
	unset($_POST['rh_Tel']);
	unset($_POST['HoldReason']);
	unset($_POST['PaymentTerms']);
	unset($_POST['Discount']);
	unset($_POST['DiscountCode']);
	unset($_POST['PymtDiscount']);
	unset($_POST['CreditLimit']);
	unset($_POST['SalesType']);
	unset($_POST['DebtorNo']);
	unset($_POST['InvAddrBranch']);
	unset($_POST['TaxRef']);
	unset($_POST['CustomerPOLine']);
}

/*DebtorNo could be set from a post or a get when passed as a parameter to this page */

if (isset($_POST['DebtorNo'])){
	$DebtorNo = $_POST['DebtorNo'];
} elseif (isset($_GET['DebtorNo'])){
	$DebtorNo = $_GET['DebtorNo'];
}

echo "<A HREF='" . $rootpath . '/SelectCustomer.php?' . SID . "'>" . _('Back to Customers') . '</A><BR>';

if (!isset($DebtorNo)) {

/*If the page was called without $_POST['DebtorNo'] passed to page then assume a new customer is to be entered show a form with a Debtor Code field other wise the form showing the fields with the existing entries against the customer will show for editing with only a hidden DebtorNo field*/

	echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '>';

	echo "<input type='Hidden' name='New' value='Yes'>";

	$DataError =0;

	echo '<CENTER><TABLE BORDER=2 CELLSPACING=4><TR><TD><TABLE>';

	/* if $AutoDebtorNo in config.php has not been set or if it has been set to a number less than one,
	then provide an input box for the DebtorNo to manually assigned */
	if ($_SESSION['AutoDebtorNo']==0)  {
		echo '<TR><TD>' . _('Customer Code') . ":</TD><TD><input tabindex=1 type='Text' name='DebtorNo' SIZE=11 MAXLENGTH=10></TD></TR>";
	}
	// bowikaxu - april 2007 - increase customer name
	//rleal Mar 3, 2010 Se agregaron las address7-10 para FE
	echo '<TR><TD>' . _('Customer Name') . ":</TD>
		<TD><input tabindex=2 type='Text' name='CustName' SIZE=42 MAXLENGTH=150></TD></TR>";
		echo '<TR><TD>' . _('Nombre Comercial') . ":</TD>
		<TD><input tabindex=3 type='Text' name='CustName2' SIZE=42 MAXLENGTH=150></TD></TR>";
	echo '<TR><TD>' . _('Address Line 1') . ":</TD>
		<TD><input tabindex=4 type='Text' name='Address1' SIZE=42 MAXLENGTH=150></TD></TR>";
	echo '<TR><TD>' . _('Address Line 2') . ":</TD>
		<TD><input tabindex=5 type='Text' name='Address2' SIZE=42 MAXLENGTH=150></TD></TR>";
	echo '<TR><TD>' . _('Address Line 3') . ":</TD>
		<TD><input tabindex=6 type='Text' name='Address3' SIZE=42 MAXLENGTH=150></TD></TR>";
	echo '<TR><TD>' . _('Address Line 4') . ":</TD>
		<TD><input tabindex=7 type='Text' name='Address4' SIZE=42 MAXLENGTH=150></TD></TR>";
	echo '<TR><TD>' . _('Address Line 5') . ":</TD>
		<TD><input tabindex=8 type='Text' name='Address5' SIZE=42 MAXLENGTH=150></TD></TR>";
	echo '<TR><TD>' . _('Address Line 6') . ":</TD>
		<TD><input tabindex=9 type='Text' name='Address6' SIZE=22 MAXLENGTH=150></TD></TR>";
	echo '<TR><TD>' . _('Address Line 7') . ":</TD>";
    echo '<TD><SELECT tabindex=9 name="Address7">';
    echo '<option selected="selected" value="OTRO FUERA DE MEXICO" >OTRO FUERA DE MEXICO</option>';
    $sql="select * from rh_municipios order by municipio;";
    $rs = DB_query($sql,$db);
    while($Rw= DB_fetch_array($rs)){
		if (isset($_POST['Address7']) and iconv("utf-8", 'ASCII//TRANSLIT',mb_strtoupper($Rw['municipio'],'utf8'))==iconv("utf-8", 'ASCII//TRANSLIT',mb_strtoupper($_POST['Address7'],'utf8'))) {
			echo '<OPTION SELECTED VALUE="';
		} else {
			echo '<OPTION VALUE="';
		}
		echo mb_strtoupper($Rw['municipio'],'utf8') . '" >' . mb_strtoupper($Rw['municipio'],'utf8').'</option>';

	} //end while loop
	echo '</SELECT></TD></TR>';

	// echo "<TD><input tabindex=9 type='Text' name='Address7' SIZE=42 MAXLENGTH=150></TD></TR>";
	echo '<TR><TD>' . _('Address Line 8') . ":</TD>";
    echo '<TD><SELECT tabindex=9 name="Address8">';
    echo '<option selected="selected" value="OTRO FUERA DE MEXICO" >OTRO FUERA DE MEXICO</option>';
    $sql="select * from rh_estados order by estado;";
    $rs = DB_query($sql,$db);
    while($Rw= DB_fetch_array($rs)){
		if (isset($_POST['Address8']) and iconv("utf-8", 'ASCII//TRANSLIT',mb_strtoupper($Rw['estado'],'utf8'))==iconv("utf-8", 'ASCII//TRANSLIT',mb_strtoupper($_POST['Address8'],'utf8'))) {
			echo '<OPTION SELECTED VALUE="';
		} else {
			echo '<OPTION VALUE="';
		}
		echo mb_strtoupper($Rw['estado'],'utf8') . '" >' . mb_strtoupper($Rw['estado'],'utf8').'</option>';

	} //end while loop
	echo '</SELECT></TD></TR>';
	echo '<TR><TD>' . _('Address Line 9') . ":</TD>
		<TD><input tabindex=9 type='Text' name='Address9' SIZE=22 MAXLENGTH=150></TD></TR>";
	echo '<TR><TD>' . _('Address Line 10') . ":</TD>
		<TD><input tabindex=9 type='Text' name='Address10' SIZE=22 MAXLENGTH=30></TD></TR>";
        echo '<tr><td colspan="2">';
	?>
        <!--ALTA -->
            <fieldset>
                <legend>RFC:</legend>
                <table>
                    <tr>
                        <td> <?php echo _("Tax Reference"); ?>:</td>
                        <td>
                            <input tabindex=10 type='Text' name='TaxRef' id='TaxRef' SIZE=22 MAXLENGTH=20 value="<?php echo $_POST['TaxRef'] ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            Persona Moral:
                            <input type="radio" name="satPersona" id="satPersonaMoral" value="moral" />
                            <br/>
                            Persona Fisica:
                            <input type="radio" name="satPersona" id="satPersonaFisica" value="fisica" />
                            <br/>
                        </td>
                        <td align="center">
                            Publico en general:
                            <input type="radio" name="rfcs" id="rfcsPublicoEnGeneral" value="publicoEnGeneral" onclick="comboboxPublicoEnGeneralChecked()"/>
                            <br/>
                            Cliente en extranjero:
                            <input type="radio" name="rfcs" id="rfcsClienteEnExtranjero" value="clienteEnExtranjero" onclick="comboboxClienteEnExtranjeroChecked()"/>
                            <br/>
                            Especificar:
                            <input type="radio" name="rfcs" id="rfcsEspecificar" value="especificar" checked="checked" onclick="comboboxEspecificarChecked()" />
                        </td>
                    </tr>
                </table>
            </fieldset>
        <?php echo '</td><tr></TABLE></TD><TD><TABLE>';
	$result=DB_query('SELECT typeabbrev, sales_type FROM salestypes ',$db);
	if (DB_num_rows($result)==0){
		$DataError =1;
		echo '<TR><TD COLSPAN=2>' . prnMsg(_('No sales types/price lists defined'),'error') . '</TD></TR>';
	} else {
		echo '<TR><TD>' . _('Sales Type/Price List') . ":</TD>
			<TD><SELECT tabindex=11 name='SalesType'>";

		while ($myrow = DB_fetch_array($result)) {
			echo "<OPTION VALUE='". $myrow['typeabbrev'] . "'>" . $myrow['sales_type'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</SELECT></TD></TR>';
	}
	$DateString = Date($_SESSION['DefaultDateFormat']);
	echo '<TR><TD>' . _('Customer Since') . ' (' . $_SESSION['DefaultDateFormat'] . "):</TD><TD><input type='Text' name='ClientSince' value=$DateString SIZE=12 MAXLENGTH=10></TD></TR>";
	echo '<TR><TD>' . _('Discount Percent') . ":</TD>
		<TD><input type='Text' name='Discount' value=0 SIZE=5 MAXLENGTH=4></TD></TR>";
	echo '<TR><TD>' . _('Discount Code') . ":</TD>
		<TD><input type='Text' name='DiscountCode' SIZE=3 MAXLENGTH=2></TD></TR>";
	/*echo '<TR><TD>' . _('Payment Discount Percent') .":</TD>
		<TD><input type='hidden' name='PymtDiscount' value=0 SIZE=5 MAXLENGTH=4></TD></TR>";*/
    echo "<TR><TD></TD><TD><input type='hidden' name='PymtDiscount' value=0 SIZE=5 MAXLENGTH=4></TD></TR>";
	echo '<TR><TD>' . _('Credit Limit') . ":</TD>
		<TD><input type='Text' name='CreditLimit' value=" . $_SESSION['DefaultCreditLimit'] . " SIZE=16 MAXLENGTH=14></TD></TR>";
	// bowikaxu realhost - sept 07 - customer phone number
	echo '<TR><TD>' . _('Phone Number') . ":</TD>
		<TD><input type='Text' name='rh_Tel' SIZE=40 MAXLENGTH=90></TD></TR>";


	$result=DB_query('SELECT terms, termsindicator FROM paymentterms',$db);
	if (DB_num_rows($result)==0){
		$DataError =1;
		echo '<TR><TD COLSPAN=2>' . prnMsg(_('There are no payment terms currently defined - go to the setup tab of the main menu and set at least one up first'),'error') . '</TD></TR>';
	} else {

		echo '<TR><TD>' . _('Payment Terms') . ":</TD>
			<TD><SELECT name='PaymentTerms'>";

		while ($myrow = DB_fetch_array($result)) {
			echo "<OPTION VALUE='". $myrow['termsindicator'] . "'>" . $myrow['terms'];
		} //end while loop
		DB_data_seek($result,0);

		echo '</SELECT></TD></TR>';
	}
	echo '<TR><TD>' . _('Credit Status') . ":</TD><TD><SELECT name='HoldReason'>";

	$result=DB_query('SELECT reasoncode, reasondescription FROM holdreasons',$db);
	if (DB_num_rows($result)==0){
		$DataError =1;
		echo '<TR><TD COLSPAN=2>' . prnMsg(_('There are no credit statuses currently defined - go to the setup tab of the main menu and set at least one up first'),'error') . '</TD></TR>';
	} else {
		while ($myrow = DB_fetch_array($result)) {
			echo "<OPTION VALUE='". $myrow['reasoncode'] . "'>" . $myrow['reasondescription'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</SELECT></TD></TR>';
	}

	$result=DB_query('SELECT currency, currabrev FROM currencies',$db);
	if (DB_num_rows($result)==0){
		$DataError =1;
		echo '<TR><TD COLSPAN=2>' . prnMsg(_('There are no currencies currently defined - go to the setup tab of the main menu and set at least one up first'),'error') . '</TD></TR>';
	} else {
		if (!isset($_POST['CurrCode'])){
			$CurrResult = DB_query('SELECT currencydefault FROM companies WHERE coycode=1',$db);
			$myrow = DB_fetch_row($CurrResult);
			$_POST['CurrCode'] = $myrow[0];
		}
		echo '<TR><TD>' . _('Customer Currency') . ":</TD><TD><SELECT name='CurrCode'>";
		while ($myrow = DB_fetch_array($result)) {
			if ($_POST['CurrCode']==$myrow['currabrev']){
				echo '<OPTION SELECTED VALUE='. $myrow['currabrev'] . '>' . $myrow['currency'];
			} else {
				echo '<OPTION VALUE='. $myrow['currabrev'] . '>' . $myrow['currency'];
			}
		} //end while loop
		DB_data_seek($result,0);

		echo '</SELECT></TD></TR>';
	}

	/*added line 8/23/2007 by Morris Kelly to set po line parameter Y/N*/
	echo '<tr><td>' . _('Customer PO Line on SO') . ":</td><td><select name='CustomerPOLine'>";
		echo '<option selected value=0>' . _('No');
		echo '<option value=1>' . _('Yes');
	echo '</select></td></tr>';

	echo '<TR><TD>' . _('Invoice Addressing') . ":</TD><TD><SELECT NAME='AddrInvBranch'>";
		echo '<OPTION SELECTED VALUE=0>' . _('Address to HO');
		echo '<OPTION VALUE=1>' . _('Address to Branch');
	echo '</SELECT></TD></TR>';

	echo'</TABLE></TD></TR></TABLE></CENTER>';
	if ($DataError ==0){
		echo "<CENTER><input onclick='return vReceptor()' type='Submit' name='submit' value='" . _('Add New Customer') . "'><BR><INPUT TYPE=SUBMIT ACTION=RESET VALUE='" . _('Reset') . "'></CENTER>";
	}
	echo '</FORM>';

} else {

//DebtorNo exists - either passed when calling the form or from the form itself

	echo "<FORM METHOD='post' action='" . $_SERVER['PHP_SELF'] . '?' . SID ."'>";
	echo '<CENTER><TABLE BORDER=2 CELLSPACING=4><TR><TD><TABLE>';
//rleal Mar 3, 2010 Se agregaron las address7-10 para FE
	if (!isset($_POST['New'])) {
		$sql = "SELECT debtorno,
				name,
				name2,
				address1,
				address2,
				address3,
				address4,
				address5,
				address6,
				address7,
				address8,
				address9,
				address10,
				rh_Tel,
				currcode,
				salestype,
				clientsince,
				holdreason,
				paymentterms,
				discount,
				discountcode,
				pymtdiscount,
				creditlimit,
				invaddrbranch,
				taxref,
				customerpoline
				FROM debtorsmaster
			WHERE debtorno = '" . $DebtorNo . "'";

		$ErrMsg = _('The customer details could not be retrieved because');
		$result = DB_query($sql,$db,$ErrMsg);


		$myrow = DB_fetch_array($result);

		/* if $AutoDebtorNo in config.php has not been set or if it has been set to a number less than one,
		then display the DebtorNo */
		if ($_SESSION['AutoDebtorNo']== 0 )  {
			echo '<TR><TD>' . _('Customer Code') . ":</TD>
				<TD>" . $DebtorNo . "</TD></TR>";
		}
		//rleal Mar 3, 2010 Se agregaron las address7-10 para FE
		$_POST['CustName'] = $myrow['name'];
		$_POST['CustName2'] = $myrow['name2'];
		$_POST['Address1']  = $myrow['address1'];
		$_POST['Address2']  = $myrow['address2'];
		$_POST['Address3']  = $myrow['address3'];
		$_POST['Address4']  = $myrow['address4'];
		$_POST['Address5']  = $myrow['address5'];
		$_POST['Address6']  = $myrow['address6'];
		$_POST['Address7']  = $myrow['address7'];
		$_POST['Address8']  = $myrow['address8'];
		$_POST['Address9']  = $myrow['address9'];
		$_POST['Address10']  = $myrow['address10'];
		$_POST['SalesType'] = $myrow['salestype'];
		$_POST['CurrCode']  = $myrow['currcode'];
		$_POST['ClientSince'] = ConvertSQLDate($myrow['clientsince']);
		$_POST['HoldReason']  = $myrow['holdreason'];
		$_POST['PaymentTerms']  = $myrow['paymentterms'];
		$_POST['Discount']  = $myrow['discount'] * 100; // Sherifoz 21.6.03 convert to displayable percentage
		$_POST['DiscountCode']  = $myrow['discountcode'];
		$_POST['PymtDiscount']  = $myrow['pymtdiscount'] * 100; // Sherifoz 21.6.03 convert to displayable percentage
		$_POST['CreditLimit']	= $myrow['creditlimit'];
		$_POST['InvAddrBranch'] = $myrow['invaddrbranch'];
		$_POST['TaxRef'] = $myrow['taxref'];
		$_POST['rh_Tel'] = $myrow['rh_Tel']; // bowikaxu - telefono direccion de facturacion

		$_POST['CustomerPOLine'] = $myrow['customerpoline'];

		echo "<INPUT TYPE=HIDDEN NAME='DebtorNo' VALUE='" . $DebtorNo . "'>";

	} else {
	// its a new customer being added
		echo "<INPUT TYPE=HIDDEN NAME='New' VALUE='Yes'>";

		/* if $AutoDebtorNo in config.php has not been set or if it has been set to a number less than one,
		then provide an input box for the DebtorNo to manually assigned */
		if ($_SESSION['AutoDebtorNo']== 0 )  {
			echo '<TR><TD>' . _('Customer Code') . ':</TD>
				<TD><input ' . (in_array('DebtorNo',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="DebtorNo" value="' . $DebtorNo . '" SIZE=12 MAXLENGTH=10></TD></TR>';
		}
	}

	echo '<TR><TD>' . _('Customer Name') . ':</TD>
		<TD><input ' . (in_array('CustName',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="CustName" value="' . $_POST['CustName'] . '" SIZE=42 MAXLENGTH=150></TD></TR>';

	// bowikaxu realhost - nombre 2
	echo '<TR><TD>' . _('Nombre Comercial') . ':</TD>
		<TD><input ' . (in_array('CustName2',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="CustName2" value="' . $_POST['CustName2'] . '" SIZE=42 MAXLENGTH=150></TD></TR>';
	//rleal Mar 3, 2010 Se agregaron las address7-10 para FE
	echo '<TR><TD>' . _('Address Line 1') . ':</TD>
		<TD><input ' . (in_array('Address1',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address1" SIZE=42 MAXLENGTH=150 value="' . $_POST['Address1'] . '"></TD></TR>';
	echo '<TR><TD>' . _('Address Line 2') . ':</TD>
		<TD><input ' . (in_array('Address2',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address2" SIZE=42 MAXLENGTH=150 value="' . $_POST['Address2'] . '"></TD></TR>';
	echo '<TR><TD>' . _('Address Line 3') . ':</TD>
		<TD><input ' . (in_array('Address3',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address3" SIZE=42 MAXLENGTH=150 value="' . $_POST['Address3'] . '"></TD></TR>';
	echo '<TR><TD>' . _('Address Line 4') . ':</TD>
		<TD><input ' . (in_array('Address4',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address4" SIZE=42 MAXLENGTH=150 value="' . $_POST['Address4'] . '"></TD></TR>';
	echo '<TR><TD>' . _('Address Line 5') . ':</TD>
		<TD><input ' . (in_array('Address5',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address5" SIZE=42 MAXLENGTH=150 value="' . $_POST['Address5'] . '"></TD></TR>';
	echo '<TR><TD>' . _('Address Line 6') . ':</TD>
		<TD><input ' . (in_array('Address6',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address6" SIZE=42 MAXLENGTH=150 value="' . $_POST['Address6'] . '"></TD></TR>';

	echo '<TR><TD>' . _('Address Line 7') . ':</TD>';

	echo '<TD><SELECT tabindex=9 name="Address7">';
    echo '<option selected="selected" value="OTRO FUERA DE MEXICO" >OTRO FUERA DE MEXICO</option>';
    $sql="select * from rh_municipios order by municipio;";
    $rs = DB_query($sql,$db);
    while($Rw= DB_fetch_array($rs)){
		if (isset($_POST['Address7']) and iconv("utf-8", 'ASCII//TRANSLIT',mb_strtoupper($Rw['municipio'],'utf8'))==iconv("utf-8", 'ASCII//TRANSLIT',mb_strtoupper($_POST['Address7'],'utf8'))) {
			echo '<OPTION SELECTED VALUE="';
		} else {
			echo '<OPTION VALUE="';
		}
		echo mb_strtoupper($Rw['municipio'],'utf8') . '" >' . mb_strtoupper($Rw['municipio'],'utf8').'</option>';

	} //end while loop
	echo '</SELECT></TD></TR>';

	echo '<TR><TD>' . _('Address Line 8') . ':</TD>';

    echo '<TD>';
	echo'<SELECT tabindex=9 name="Address8" ' . (in_array('Address8',$Errors) ?  'class="inputerror"' : '' ) .' >';
    echo '<option selected="selected" vaue="OTRO FUERA DE MEXICO" >OTRO FUERA DE MEXICO</option>';
    $sql="select * from rh_estados order by estado;";
    $rs = DB_query($sql,$db);

    while($Rw= DB_fetch_array($rs)){
		if (isset($_POST['Address8']) and iconv("utf-8", 'ASCII//TRANSLIT',mb_strtoupper($Rw['estado'],'utf8'))==iconv("utf-8", 'ASCII//TRANSLIT',mb_strtoupper($_POST['Address8'],'utf8'))) {
			echo '<OPTION SELECTED VALUE="';
		} else {
			echo '<OPTION VALUE="';
		}
		echo mb_strtoupper($Rw['estado'],'utf8') . '" >' . mb_strtoupper($Rw['estado'],'utf8').'</option>';

	} //end while loop
	echo '</SELECT></TD></TR>';
	echo '<TR><TD>' . _('Address Line 9') . ':</TD>
		<TD><input ' . (in_array('Address9',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address9" SIZE=42 MAXLENGTH=150 value="' . $_POST['Address9'] . '"></TD></TR>';
	echo '<TR><TD>' . _('Address Line 10') . ':</TD>
		<TD><input ' . (in_array('Address10',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address10" SIZE=42 MAXLENGTH=150 value="' . $_POST['Address10'] . '"></TD></TR>';
        echo '<tr><td colspan="2">';
	?>
            <!-- CAMBIO -->
            <fieldset>
                <legend>RFC:</legend>
                <table>
                    <tr>
                        <td> <?php echo _("Tax Reference"); ?>:</td>
                        <td>
                            <input tabindex=10 type='Text' name='TaxRef' id='TaxRef' SIZE=22 MAXLENGTH=20 value="<?php echo $_POST['TaxRef'] ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            Persona Moral:
                            <input type="radio" name="satPersona" id="satPersonaMoral" value="moral" />
                            <br/>
                            Persona Fisica:
                            <input type="radio" name="satPersona" id="satPersonaFisica" value="fisica" />
                            <br/>
                        </td>
                        <td align="center">
                            Publico en general:
                            <input type="radio" name="rfcs" id="rfcsPublicoEnGeneral" value="publicoEnGeneral" onclick="comboboxPublicoEnGeneralChecked()"/>
                            <br/>
                            Cliente en extranjero:
                            <input type="radio" name="rfcs" id="rfcsClienteEnExtranjero" value="clienteEnExtranjero" onclick="comboboxClienteEnExtranjeroChecked()"/>
                            <br/>
                            Especificar:
                            <input type="radio" name="rfcs" id="rfcsEspecificar" value="especificar" checked="checked" onclick="comboboxEspecificarChecked()" />
                        </td>
                    </tr>
                </table>
            </fieldset>
  <?php
  //echo '</td></tr></TABLE></TD><TD><TABLE>';

//	echo '<TR><TD>' . _('Tax Reference') . ':</TD>
//		<TD><input type="Text" name="TaxRef" SIZE=42 MAXLENGTH=40 value="' . $_POST['TaxRef'] . '"></TD></TR>';

  echo '</TABLE></TD><TD><TABLE>';

	$result=DB_query('SELECT typeabbrev, sales_type FROM salestypes ',$db);

	echo '<TR><TD>' . _('Sales Type') . '/' . _('Price List') . ":</TD>
		<TD><SELECT name='SalesType'>";

	while ($myrow = DB_fetch_array($result)) {
		if ($_POST['SalesType']==$myrow['typeabbrev']){
			echo "<OPTION SELECTED VALUE='". $myrow['typeabbrev'] . "'>" . $myrow['sales_type'];
		} else {
			echo "<OPTION VALUE='". $myrow['typeabbrev'] . "'>" . $myrow['sales_type'];
		}
	} //end while loop
	DB_data_seek($result,0);

	echo '</SELECT></TD></TR>
	<TR><TD>' . _('Customer Since') . ' (' . $_SESSION['DefaultDateFormat'] . '):</TD>
			<TD><input ' . (in_array('ClientSince',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="ClientSince" SIZE=12 MAXLENGTH=10 value=' . $_POST['ClientSince'] . '></TD></TR>';
	echo '<TR><TD>' . _('Discount Percent') . ':</TD>
		<TD><input type="Text" name="Discount" SIZE=5 MAXLENGTH=4 value=' . $_POST['Discount'] . '></TD></TR>';
	echo '<TR><TD>' . _('Discount Code') . ':</TD>
		<TD><input ' . (in_array('DiscountCode',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="DiscountCode" SIZE=3 MAXLENGTH=2 value="' . $_POST['DiscountCode'] . '"></TD></TR>';
	/*echo '<TR><TD>' . _('Payment Discount Percent') . ':</TD>
		<TD><input ' . (in_array('PymtDiscount',$Errors) ?  'class="inputerror"' : '' ) .' type="hidden" name="PymtDiscount" SIZE=5 MAXLENGTH=4 value=' . $_POST['PymtDiscount'] . '></TD></TR>';*/
    echo '<TR><TD></TD><TD><input ' . (in_array('PymtDiscount',$Errors) ?  'class="inputerror"' : '' ) .' type="hidden" name="PymtDiscount" SIZE=5 MAXLENGTH=4 value=' . $_POST['PymtDiscount'] . '></TD></TR>';

	echo '<TR><TD>' . _('Credit Limit') . ':</TD>
		<TD><input ' . (in_array('CreditLimit',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="CreditLimit" SIZE=16 MAXLENGTH=14 value=' . $_POST['CreditLimit'] . '></TD></TR>';
	echo '<TR><TD>' . _('Phone Number') . ":</TD>
		<TD><input type='Text' name='rh_Tel' SIZE=40 MAXLENGTH=90 value='" . $_POST['rh_Tel'] . "'></TD></TR>";
	$result=DB_query('SELECT terms, termsindicator FROM paymentterms',$db);

	echo '<TR><TD>' . _('Payment Terms') . ":</TD>
		<TD><SELECT name='PaymentTerms'>";

	while ($myrow = DB_fetch_array($result)) {
		if ($_POST['PaymentTerms']==$myrow['termsindicator']){
		echo "<OPTION SELECTED VALUE=". $myrow['termsindicator'] . '>' . $myrow['terms'];
		} else {
		echo '<OPTION VALUE='. $myrow['termsindicator'] . '>' . $myrow['terms'];
		}
	} //end while loop
	DB_data_seek($result,0);

	$result=DB_query('SELECT reasoncode, reasondescription FROM holdreasons',$db);

	echo '</SELECT></TD></TR><TR><TD>' . _('Credit Status') . ":</TD>
		<TD><SELECT name='HoldReason'>";
	while ($myrow = DB_fetch_array($result)) {

		if ($_POST['HoldReason']==$myrow['reasoncode']){
			echo '<OPTION SELECTED VALUE='. $myrow['reasoncode'] . '>' . $myrow['reasondescription'];
		} else {
			echo '<OPTION VALUE='. $myrow['reasoncode'] . '>' . $myrow['reasondescription'];
		}

	} //end while loop
	DB_data_seek($result,0);

	$result=DB_query('SELECT currency, currabrev FROM currencies',$db);

	echo '</select></td></tr>
		<tr><td>' . _('Customers Currency') . ":</td>
		<td><select name='CurrCode'>";
	while ($myrow = DB_fetch_array($result)) {
		if ($_POST['CurrCode']==$myrow['currabrev']){
			echo '<option selected value='. $myrow['currabrev'] . '>' . $myrow['currency'];
		} else {
			echo '<option value='. $myrow['currabrev'] . '>' . $myrow['currency'];
		}
	} //end while loop
	DB_data_seek($result,0);
	echo '</select></td></tr>';

	/*added lines 8/23/2007 by Morris Kelly to get po line parameter Y/N*/
	echo '<tr><td>' . _('Require Customer PO Line on SO') . ":</TD>
		<TD><SELECT NAME='CustomerPOLine'>";
	if ($_POST['CustomerPOLine']==0){
		echo '<option selected value=0>' . _('No');
		echo '<option value=1>' . _('Yes');
	} else {
		echo '<option value=0>' . _('No');
		echo '<option selected value=1>' . _('Yes');
	}
	echo '</select></td></tr>';

	echo '<TR><TD>' . _('Invoice Addressing') . ":</TD>
		<TD><SELECT NAME='AddrInvBranch'>";
	if ($_POST['InvAddrBranch']==0){
		echo '<OPTION SELECTED VALUE=0>' . _('Address to HO');
		echo '<OPTION VALUE=1>' . _('Address to Branch');
	} else {
		echo '<OPTION VALUE=0>' . _('Address to HO');
		echo '<OPTION SELECTED VALUE=1>' . _('Address to Branch');
	}
	echo '</SELECT></TD></TR>';

	$sql = 'SELECT accountcode, bankaccountname FROM bankaccounts';
	$res = DB_query($sql,$db);

	// bowikaxu realhost august 07 - bank accounts select box
	echo "<TR><TD>"._('Bank Accounts')."</TD><TD><SELECT NAME='Banks[]' MULTIPLE='multiple' size='5'>";
	while ($banksacts = DB_fetch_array($res)){
		$sql = "SELECT accountcode FROM rh_debtorsacts WHERE accountcode = '".$banksacts['accountcode']."' AND debtorno = '".$DebtorNo."'";
		$res2 = DB_query($sql,$db);
		if(DB_num_rows($res2)>0){
			echo "<OPTION SELECTED VALUE='".$banksacts['accountcode']."'>".$banksacts['bankaccountname']."</OPTION>";
		}else {
			echo "<OPTION VALUE='".$banksacts['accountcode']."'>".$banksacts['bankaccountname']."</OPTION>";
		}

	}
	echo "</SELECT></TD></TR>";
	echo '</TABLE></TD></TR></TABLE></CENTER>';

	if ($_POST['New']) {
		echo "<CENTER><INPUT TYPE='Submit' onclick='return vReceptor()' NAME='submit' VALUE='" . _('Add New Customer') . "'><BR><INPUT TYPE=SUBMIT name='reset' VALUE='" . _('Reset') . "'></FORM>";
	} else {
		echo "<HR><CENTER><INPUT onclick='return vReceptor()' TYPE='Submit' NAME='submit' VALUE='" . _('Update Customer') . "'>";
		echo '<P><INPUT TYPE="Submit" NAME="delete" VALUE="' . _('Delete Customer') . '" onclick="return confirm(\'' . _('Are You Sure?') . '\');">';
	}
} // end of main ifs
?>
<script type="text/javascript">
    function vReceptor(){
        if(!vRfcs())
            return false
        if(!vDireccion())
            return false
        return true
    }

    function vRfcs(){
        var elementInputTextTaxRef = document.getElementById('TaxRef')
        var rfc = elementInputTextTaxRef.value
        if(!rfc){
            elementInputTextTaxRef.focus()
            alert('El campo RFC es obligatorio')
            return false
        }

        var pm = document.getElementById('satPersonaMoral').checked
        var pf = document.getElementById('satPersonaFisica').checked
        if(!pm && !pf){
            alert('Debe seleccionar un tipo de Persona')
            return false
        }

        if(pm){
            if(!rfc.match(/^[A-ZÑ&]{3}(\d\d(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01]))[0-9A-ZÑ]{3}$/)){
                alert('El RFC no es un RFC valido para Persona Moral')
                return false
            }
        }

        if(pf){
            if(rfc == 'XAXX010101000' || rfc == 'XEXX010101000')
                return true

            if(!rfc.match(/^[A-ZÑ&]{4}(\d\d(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01]))[0-9A-ZÑ]{3}$/)){
                alert('El RFC no es un RFC valido para Persona Fisica')
                return false
            }
        }

        return true
    }

    function vDireccion(){
        var add9 = document.getElementsByName('Address9').firstChild.innerHTML
        if(!add9){
            alert('Debe de especificar un pais')
        }
        return true
    }

    function comboboxPublicoEnGeneralChecked(){
        var rfc = document.getElementById('TaxRef')
        rfc.value = 'XAXX010101000'
        rfc.readOnly = true
        document.getElementById('satPersonaFisica').checked = true
    }

    function comboboxClienteEnExtranjeroChecked(){
        var rfc = document.getElementById('TaxRef')
        rfc.value = 'XEXX010101000'
        rfc.readOnly = true
        document.getElementById('satPersonaFisica').checked = true
    }

    function comboboxEspecificarChecked(){
        var rfc = document.getElementById('TaxRef')
        rfc.value = ''
        rfc.readOnly = false
        document.getElementById('satPersonaFisica').checked = false
        document.getElementById('satPersonaMoral').checked = false
    }

    var rfc = '<?php echo $_POST['TaxRef']?>'

    if(rfc == 'XAXX010101000'){
        var comboboxRfc = document.getElementById('rfcsPublicoEnGeneral')
        comboboxRfc.checked = true
        document.getElementById('TaxRef').readOnly = true
    }

    if(rfc == 'XEXX010101000'){
        var comboboxRfc = document.getElementById('rfcsClienteEnExtranjero')
        comboboxRfc.checked = true
        document.getElementById('TaxRef').readOnly = true
    }

    if(rfc.length == 12){
        var comboboxSatPersona = document.getElementById('satPersonaMoral')
        comboboxSatPersona.checked = true
    }

    if(rfc.length == 13){
        var comboboxSatPersona = document.getElementById('satPersonaFisica')
        comboboxSatPersona.checked = true
    }
</script>
<script type="text/javascript" src="rh_j_globalFacturacionElectronica.js"></script>
<?php
include('includes/footer.inc');
?>
