<?php
/* $Revision: 14 $ */
/*Script to Delete all sales transactions*/

$PageSecurity=15;
include ('includes/session.inc');
$title = _('UTILITY PAGE To Change Supplier Code');
include('includes/header.inc');

if (isset($_POST['ProcessSupplierChange'])){
	
	/*First check the customer code exists */
	$result=DB_query("SELECT supplierid FROM suppliers WHERE supplierid='" . $_POST['OldSupplierNo'] . "'",$db);
	if (DB_num_rows($result)==0){
		prnMsg ('<BR><BR>' . _('The supplier code') . ': ' . $_POST['OldSupplierNo'] . ' ' . _('does not currently exist as a supplier code in the system'),'error');
		include('includes/footer.inc');
		exit;
	}
	
	if ($_POST['NewSupplierNo']==''){
		prnMsg(_('The new supplier code to change the old code to must be entered as well'),'error');
		include('includes/footer.inc');
		exit;
	}
/*Now check that the new code doesn't already exist */
	$result=DB_query("SELECT supplierid FROM suppliers WHERE supplierid='" . $_POST['NewSupplierNo'] . "'",$db);
	if (DB_num_rows($result)!=0){
		prnMsg(_('The replacement supplier code') .': ' . $_POST['NewSupplierNo'] . ' ' . _('already exists as a supplier code in the system') . ' - ' . _('a unique supplier code must be entered for the new code'),'error');
		include('includes/footer.inc');
		exit;
	}
	
	$result = DB_query('begin',$db);

	// bowikaxu - insert new record
	$sql = "INSERT INTO suppliers (supplierid, 
							suppname, 
							address1, 
							address2, 
							address3, 
							address4, 
							currcode, 
							suppliersince, 
							paymentterms, 
							bankpartics, 
							bankref, 
							bankact, 
							remittance, 
							taxgroupid,
							rh_taxref,
							rh_comments) 
					SELECT '".$_POST['NewSupplierNo']."',
							suppname, 
							address1, 
							address2, 
							address3, 
							address4, 
							currcode, 
							suppliersince, 
							paymentterms, 
							bankpartics, 
							bankref, 
							bankact, 
							remittance, 
							taxgroupid,
							CONCAT(rh_taxref,'-') AS rh_taxref,
							rh_comments
							FROM suppliers 
							WHERE supplierid = '".$_POST['OldSupplierNo']."'";
	
	$DbgMsg =_('The SQL that failed was');
	$ErrMsg = _('The SQL to insert the new suppliers master record failed') . ', ' . _('the SQL statement was');
	$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	
	// suppliercontacts
	prnMsg(_('Changing suppliercontacts transaction records'),'info');
	
	$sql = "UPDATE suppliercontacts SET supplierid='" . $_POST['NewSupplierNo'] . "' WHERE supplierid='" . $_POST['OldSupplierNo'] . "'";

	$ErrMsg = _('The SQL to update debtor transaction records failed');
	$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);

	// shipments
	prnMsg(_('Changing shipments transaction records'),'info');
	
	$sql = "UPDATE shipments SET supplierid='" . $_POST['NewSupplierNo'] . "' WHERE supplierid='" . $_POST['OldSupplierNo'] . "'";

	$ErrMsg = _('The SQL to update debtor transaction records failed');
	$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	
	// purchorders
	prnMsg(_('Changing purchorders transaction records'),'info');
	
	$sql = "UPDATE purchorders SET supplierno='" . $_POST['NewSupplierNo'] . "' WHERE supplierno='" . $_POST['OldSupplierNo'] . "'";

	$ErrMsg = _('The SQL to update debtor transaction records failed');
	$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	
	// purchdata
	prnMsg(_('Changing purchdata transaction records'),'info');
	
	$sql = "UPDATE purchdata SET supplierno='" . $_POST['NewSupplierNo'] . "' WHERE supplierno='" . $_POST['OldSupplierNo'] . "'";

	$ErrMsg = _('The SQL to update debtor transaction records failed');
	$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	
	// supptrans
	prnMsg(_('Changing supptrans transaction records'),'info');
	
	$sql = "UPDATE supptrans SET supplierno='" . $_POST['NewSupplierNo'] . "' WHERE supplierno='" . $_POST['OldSupplierNo'] . "'";

	$ErrMsg = _('The SQL to update debtor transaction records failed');
	$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	
	// grns
	prnMsg(_('Changing grns transaction records'),'info');
	
	$sql = "UPDATE grns SET supplierid='" . $_POST['NewSupplierNo'] . "' WHERE supplierid='" . $_POST['OldSupplierNo'] . "'";

	$ErrMsg = _('The SQL to update debtor transaction records failed');
	$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	
	// suppliers
	prnMsg(_('Changing suppliers transaction records'),'info');
	
	$sql = "UPDATE suppliers SET supplierid='" . $_POST['NewSupplierNo'] . "' WHERE supplierid='" . $_POST['OldSupplierNo'] . "'";

	$ErrMsg = _('The SQL to update debtor transaction records failed');
	//$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	
	// suppliers delete
	prnMsg(_('Deleting the supplier code from the suppliers table'),'info');
	$sql = "DELETE FROM suppliers WHERE supplierid='" . $_POST['OldSupplierNo'] . "'";

	$ErrMsg = _('The SQL to delete the old debtor record failed');
	$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	
	// COMMIT
	$result = DB_query('commit',$db);
	echo "<hr><CENTER>OK</CENTER><hr>";
	
}

echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . "?=" . $SID . "' METHOD=POST>";

echo '<P><CENTER><TABLE>
	<TR><TD>' . _('Existing Supplier Code') . ":</TD>
		<TD><INPUT TYPE=Text NAME='OldSupplierNo' SIZE=20 MAXLENGTH=20></TD>
	</TR>";
echo '<TR><TD> ' . _('New Supplier Code') . ":</TD>
	<TD><INPUT TYPE=Text NAME='NewSupplierNo' SIZE=20 MAXLENGTH=20></TD>
	</TR>
	</TABLE>";

echo "<INPUT TYPE=SUBMIT NAME='ProcessSupplierChange' VALUE='" . _('Process') . "'>";

echo '</FORM>';

include('includes/footer.inc');

?>