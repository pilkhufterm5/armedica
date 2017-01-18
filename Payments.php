<?php

/* webERP Revision: 32 $ */
/**
 * REALHOST 2008
 * $LastChangedDate: 2011-11-17 12:58:00 -0600 (jue 17 de nov de 2011) $
 * $Rev: 387 $
 */
$PageSecurity = 5;

include('includes/DefinePaymentClass.php');
include('includes/session.inc');

$title = _('Payment Entry');

include('includes/header.inc');

include('includes/SQL_CommonFunctions.inc');

echo '<script type="text/javascript" src="javascript/jquery.js"></script>';

?>

<script language="JavaScript" type="text/javascript">

$(document).ready(function() {
	
	$("#TCambio").change(function(){
		if(this.value != 0 || this.value != ""){
			$.ajax({
				async: false,
				type: "POST",
				url: "ajax/tcambio.php",
				datatype: "json",
				data: "monto=" + $("#TCambio").val(),
				success: function(responseText){
					var json = eval("(" + responseText + ")");
					$("#ExRate").val(json['TCambio']);
					$("#FunctionalExRate").val(json['TCambio']);
				}
			});
		}
	});

	$("#form1").submit(function(){
		$("#TCambio").change();
	});
	
});

</script>

<?php

if (isset($_GET['Delete'])){
$_POST['BankAccount']=$_GET['BankAccount'];
$_POST['DatePaid']=$_GET['DatePaid'];
$_POST['Currency']=$_GET['Currency'];
$_POST['Paymenttype']=$_GET['Paymenttype'];
$_POST['ChequeNum']=$_GET['ChequeNum'];
$_POST['Narrative']=$_GET['Narrative'];
$_POST['ExRate']=$_GET['ExRate'];
}
if (isset($_POST['PaymentCancelled'])) {
  prnMsg(_('Payment Cancelled since cheque was not printed'), 'warning');
  include('includes/footer.inc');
  exit();
}

if (isset($_GET['NewPayment']) and $_GET['NewPayment']=='Yes'){
	unset($_SESSION['PaymentDetail']->GLItems);
	unset($_SESSION['PaymentDetail']);
}

if (!isset($_SESSION['PaymentDetail'])){
	$_SESSION['PaymentDetail'] = new Payment;
	$_SESSION['PaymentDetail']->GLItemCounter = 1;
}

echo "<a href='" . $rootpath . '/SelectSupplier.php?' . SID . "'>" . _('Back to Suppliers') . '</a><BR>';

if (isset($_GET['SupplierID'])){
	/*The page was called with a supplierID check it is valid and default the inputs for Supplier Name and currency of payment */

	unset($_SESSION['PaymentDetail']->GLItems);
	unset($_SESSION['PaymentDetail']);
	$_SESSION['PaymentDetail'] = new Payment;
	$_SESSION['PaymentDetail']->GLItemCounter = 1;


	$SQL= "SELECT suppname,
			address1,
			address2,
			address3,
			address4,
			address5,
			address6,
			currcode,
			factorcompanyid
		FROM suppliers
		WHERE supplierid='" . $_GET['SupplierID'] . "'";

	$Result = DB_query($SQL, $db);
	if (DB_num_rows($Result)==0){
		prnMsg( _('The supplier code that this payment page was called with is not a currently defined supplier code') . '. ' . _('If this page is called from the selectSupplier page then this assures that a valid supplier is selected'),'warn');
		include('includes/footer.inc');
		exit;
	} else {
		$myrow = DB_fetch_array($Result);
		if ($myrow['factorcompanyid'] == 1) {
			$_SESSION['PaymentDetail']->SuppName = $myrow['suppname'];
			$_SESSION['PaymentDetail']->Address1 = $myrow['address1'];
			$_SESSION['PaymentDetail']->Address2 = $myrow['address2'];
			$_SESSION['PaymentDetail']->Address3 = $myrow['address3'];
			$_SESSION['PaymentDetail']->Address4 = $myrow['address4'];
			$_SESSION['PaymentDetail']->Address5 = $myrow['address5'];
			$_SESSION['PaymentDetail']->Address6 = $myrow['address6'];
			$_SESSION['PaymentDetail']->SupplierID = $_GET['SupplierID'];
			$_SESSION['PaymentDetail']->Currency = $myrow['currcode'];
		} else {
			$factorsql= "select coyname,
			 					address1,
			 					address2,
			 					address3,
			 					address4,
			 					address5,
			 					address6
							FROM factorcompanies
							WHERE id='" . $myrow['factorcompanyid'] . "'";
			
			$FactorResult = DB_query($factorsql, $db);
			$myfactorrow = DB_fetch_array($FactorResult);
			$_SESSION['PaymentDetail']->SuppName = $myrow['suppname'] . _(' care of ') . $myfactorrow['coyname'];
			$_SESSION['PaymentDetail']->Address1 = $myfactorrow['address1'];
			$_SESSION['PaymentDetail']->Address2 = $myfactorrow['address2'];
			$_SESSION['PaymentDetail']->Address3 = $myfactorrow['address3'];
			$_SESSION['PaymentDetail']->Address4 = $myfactorrow['address4'];
			$_SESSION['PaymentDetail']->Address5 = $myfactorrow['address5'];
			$_SESSION['PaymentDetail']->Address6 = $myfactorrow['address6'];
			$_SESSION['PaymentDetail']->SupplierID = $_GET['SupplierID'];
			$_SESSION['PaymentDetail']->Currency = $myrow['currcode'];
			$_POST['Currency'] = $_SESSION['PaymentDetail']->Currency;		
		}
	}
}

if (isset($_POST['BankAccount']) and $_POST['BankAccount']!=''){
	$_SESSION['PaymentDetail']->Account=$_POST['BankAccount'];
	/*Get the bank account currency and set that too */
	// bowikaxu realhost Feb 2008 - also get the complementary account fi any
	$result = DB_query('select currcode, rh_acctcomplementary FROM bankaccounts WHERE accountcode =' . $_POST['BankAccount'],$db);
	$myrow = DB_fetch_row($result);
	$_SESSION['PaymentDetail']->AccountCurrency=$myrow[0];
	// bowikaxu - set the complementary account
	$_SESSION['PaymentDetail']->CompAccount=$myrow[1];
}else {
	$_SESSION['PaymentDetail']->AccountCurrency =$_SESSION['CompanyRecord']['currencydefault'];
}
if (isset($_POST['DatePaid']) and $_POST['DatePaid']!='' AND Is_Date($_POST['DatePaid'])){
	$_SESSION['PaymentDetail']->DatePaid=$_POST['DatePaid'];
}
if (isset($_POST['ExRate']) and $_POST['ExRate']!=''){
	$_SESSION['PaymentDetail']->ExRate=$_POST['ExRate']; //ex rate between payment currency and account currency
}
if (isset($_POST['FunctionalExRate']) and $_POST['FunctionalExRate']!=''){
	$_SESSION['PaymentDetail']->FunctionalExRate=$_POST['FunctionalExRate']; //ex rate between payment currency and account currency
}
if (isset($_POST['Paymenttype']) and $_POST['Paymenttype']!=''){
	$_SESSION['PaymentDetail']->Paymenttype = $_POST['Paymenttype'];
}

if (isset($_POST['Currency']) and $_POST['Currency']!=''){
	$_SESSION['PaymentDetail']->Currency=$_POST['Currency']; //payment currency
	/*Get the exchange rate between the functional currecny and the payment currency*/
	$result = DB_query("SELECT rate FROM currencies WHERE currabrev='" . $_SESSION['PaymentDetail']->Currency . "'",$db);
	$myrow = DB_fetch_row($result);
	$tableExRate = $myrow[0]; //this is the rate of exchange between the functional currency and the payment currency
	
	if ($_POST['Currency']==$_SESSION['PaymentDetail']->AccountCurrency){
		$_POST['ExRate']=1;
		$_SESSION['PaymentDetail']->ExRate=$_POST['ExRate']; //ex rate between payment currency and account currency
		$_SESSION['PaymentDetail']->FunctionalExRate=$_POST['FunctionalExRate'];
		$SuggestedExRate=1;
	}
	if ($_SESSION['PaymentDetail']->AccountCurrency==$_SESSION['CompanyRecord']['currencydefault']){
		$_POST['FunctionalExRate']=1;
		$_SESSION['PaymentDetail']->FunctionalExRate=$_POST['FunctionalExRate'];
		$SuggestedFunctionalExRate =1;
		$SuggestedExRate = $tableExRate;
		
	} else {
		/*To illustrate the rates required
			Take an example functional currency NZD payment in USD from an AUD bank account 
			1 NZD = 0.80 USD
			1 NZD = 0.90 AUD
			The FunctionalExRate = 0.90 - the rate between the functional currency and the bank account currency
			The payment ex rate is the rate at which one can purchase the payment currency in the bank account currency
			or 0.8/0.9 = 0.88889
		*/
		
		/*Get suggested FunctionalExRate */	
		$result = DB_query("SELECT rate FROM currencies WHERE currabrev='" . $_SESSION['PaymentDetail']->AccountCurrency . "'",$db);
		$myrow = DB_fetch_row($result);
		$SuggestedFunctionalExRate = $myrow[0];
		
		/*Get the exchange rate between the functional currecny and the payment currency*/
		$result = DB_query("select rate FROM currencies WHERE currabrev='" . $_SESSION['PaymentDetail']->Currency . "'",$db);
		$myrow = DB_fetch_row($result);
		$tableExRate = $myrow[0]; //this is the rate of exchange between the functional currency and the payment currency
		/*Calculate cross rate to suggest appropriate exchange rate between payment currency and account currency */
		$SuggestedExRate = $tableExRate/$SuggestedFunctionalExRate;
		
	}
}


if (isset($_POST['Narrative']) and $_POST['Narrative']!=''){
	$_SESSION['PaymentDetail']->Narrative=$_POST['Narrative'];
}
if (isset($_POST['Amount']) and $_POST['Amount']!=""){
	$_SESSION['PaymentDetail']->Amount=$_POST['Amount'];
} else {
	if (!isset($_SESSION['PaymentDetail']->Amount)) {
	  $_SESSION['PaymentDetail']->Amount=0;
  }
}
if (isset($_POST['Discount']) and $_POST['Discount']!=''){
	$_SESSION['PaymentDetail']->Discount=$_POST['Discount'];
} else {
	if (!isset($_SESSION['PaymentDetail']->Discount)) {
	  $_SESSION['PaymentDetail']->Discount=0;
  }
}


$msg="";

if (isset($_POST['CommitBatch'])){

  /* once the GL analysis of the payment is entered (if the Creditors_GLLink is active),
  process all the data in the session cookie into the DB creating a banktrans record for
  the payment in the batch and SuppTrans record for the supplier payment if a supplier was selected
  A GL entry is created for each GL entry (only one for a supplier entry) and one for the bank
  account credit.

  NB allocations against supplier payments are a seperate exercice

  if GL integrated then
  first off run through the array of payment items $_SESSION['Payment']->GLItems and
  create GL Entries for the GL payment items
  */

  /*First off  check we have an amount entered as paid ?? */
  $TotalAmount =0;
  foreach ($_SESSION['PaymentDetail']->GLItems AS $PaymentItem) {
	  $TotalAmount += $PaymentItem->Amount;
  }

  if ($TotalAmount==0 AND 
	  ($_SESSION['PaymentDetail']->Discount + $_SESSION['PaymentDetail']->Amount)/$_SESSION['PaymentDetail']->ExRate ==0){
	  prnMsg( _('This payment has no amounts entered and will not be processed'),'warn');
	  include('includes/footer.inc');
	  exit;
  }
	
  /*Make an array of the defined bank accounts */
	$SQL = 'SELECT bankaccounts.accountcode
			FROM bankaccounts,
				chartmaster
		WHERE bankaccounts.accountcode=chartmaster.accountcode';
	$result = DB_query($SQL,$db);
	$BankAccounts = array();
	$i=0;

	while ($Act = DB_fetch_row($result)){
		$BankAccounts[$i]= $Act[0];
		$i++;
  	}
/******************************************************************************************************************************/
	//$checkperiod = rh_checkperiods($_SESSION['PaymentDetail']->DatePaid,$db);
	//echo $checkperiod;
	//exit;
	if($checkperiod == 'BLOQUEO'){
		prnMsg( _('The period is closed. The transaction can not be processed'),'error');
		include('includes/footer.inc');
		exit;
	}
/******************************************************************************************************************************/
  $PeriodNo = GetPeriod($_SESSION['PaymentDetail']->DatePaid,$db);

  // first time through commit if supplier cheque then print it first 
	if ((!isset($_POST['ChequePrinted']))
		  AND (!isset($_POST['PaymentCancelled']))
		  AND ($_SESSION['PaymentDetail']->Paymenttype == 'Cheque')) {
     // it is a supplier payment by cheque and haven't printed yet so print cheque 

    echo '<BR><A  HREF="' . $rootpath . '/PrintCheque.php?' . SID . '&ChequeNum=' . $_POST['ChequeNum'] . '">' . _('Print Cheque') . '</A><BR><BR>';
	
	  echo '<FORM id="form1" METHOD="post" action="' . $_SERVER['PHP_SELF'] . '">';
	  // bowikaxu realhost Feb 2008 - show payment warning not cheque warning
	  //echo _('Has the cheque been printed') . '?<BR>';
	  echo _('Are you sure to proceed with this transaction?')."<BR>";

	  echo '<input type="hidden" NAME="TCambio" VALUE="' . $_POST['TCambio'] . '">';
	  echo '<input type="hidden" NAME="CommitBatch" VALUE="' . $_POST['CommitBatch'] . '">';
	  echo '<input type="hidden" NAME="ChequeNum" VALUE="' . $_POST['ChequeNum'] . '">';
	  // bowikaxu - send again the bank account so it wont change the account currency for the company's default
	  echo '<input type="hidden" NAME="BankAccount" VALUE="' . $_POST['BankAccount'] . '">';
	  
	  // bowikaxu realhost august 07 - send the cheque number
	  
	  echo '<input type="submit" NAME="ChequePrinted" VALUE="' . _('Yes') . '">&nbsp;&nbsp;';
	  echo '<input type="submit" NAME="PaymentCancelled" VALUE="' . _('No') . '">';
  } else {

  //Start a transaction to do the whole lot inside 
  $SQL = 'BEGIN';
  $result = DB_query($SQL,$db);


  if ($_SESSION['PaymentDetail']->SupplierID=='') {

	  //its a nominal bank transaction type 1 

	  $TransNo = GetNextTransNo( 1, $db);
	  $Transtype = 1;

		if ($_SESSION['CompanyRecord']['gllink_creditors']==1){ /* then enter GLTrans */
			$TotalAmount=0;
			foreach ($_SESSION['PaymentDetail']->GLItems as $PaymentItem) {

			// bowikaxu realhost - sept 2008 - get the functional exchange rate
	  			if(isset($_SESSION['PaymentDetail']->FunctionalExRate)){
	  				$Frate = $_SESSION['PaymentDetail']->FunctionalExRate;
	  			}else {
	  				$sql = "SELECT rate FROM currencies WHERE currabrev = '".$_SESSION['PaymentDetail']->Currency."'";
	  				$rate = DB_fetch_array(DB_query($sql,$db));
	  				$Frate = $rate['rate'];
	  			}
			
	  		// bowikaxu realhost Feb 2008 - insert del total en moneda local
	  		if($_SESSION['PaymentDetail']->Currency == $_SESSION['PaymentDetail']->AccountCurrency AND $_SESSION['CompanyRecord']['currencydefault'] != $_SESSION['PaymentDetail']->AccountCurrency){
	  			
	  			$SQL = 'INSERT INTO gltrans (type,
				 				typeno,
								trandate,
								periodno,
								account,
								narrative,
								amount) ';
	  			$SQL= $SQL . "VALUES (1,
						" . $TransNo . ",
						'" . FormatDateForSQL($_SESSION['PaymentDetail']->DatePaid) . "',
						" . $PeriodNo . ",
						" . $PaymentItem->GLCode . ",
						'" . DB_escape_string($PaymentItem->Narrative) . "',
						" . ($PaymentItem->Amount/$Frate) . "
						)";
	  			$ErrMsg = _('Cannot insert a GL entry for the payment using the SQL');
	  			$result = DB_query($SQL,$db,$ErrMsg,_('The SQL that failed was'),true);
	  			
	  		}else {
	  			$SQL = 'INSERT INTO gltrans (type,
				 				typeno,
								trandate,
								periodno,
								account,
								narrative,
								amount) ';
	  			$SQL= $SQL . "VALUES (1,
						" . $TransNo . ",
						'" . FormatDateForSQL($_SESSION['PaymentDetail']->DatePaid) . "',
						" . $PeriodNo . ",
						" . $PaymentItem->GLCode . ",
						'" . $PaymentItem->Narrative . "',
						" . ($PaymentItem->Amount/ $Frate) . "
						)";
	  			$ErrMsg = _('Cannot insert a GL entry for the payment using the SQL');
	  			$result = DB_query($SQL,$db,$ErrMsg,_('The SQL that failed was'),true);
	  		}
	  		$TotalAmount += $PaymentItem->Amount;
	  	}
			$_SESSION['PaymentDetail']->Amount = $TotalAmount;
			$_SESSION['PaymentDetail']->Discount=0;
   	}

		//Run through the GL postings to check to see if there is a posting to another bank account (or the same one) if there is then a receipt needs to be created for this account too 

		foreach ($_SESSION['PaymentDetail']->GLItems as $PaymentItem) {

			if (in_array($PaymentItem->GLCode, $BankAccounts)) {

				/*Need to deal with the case where the payment from one bank account could be to a bank account in another currency */
				
				/*Get the currency and rate of the bank account transferring to*/
				$SQL = 'SELECT currcode, rate 
							FROM bankaccounts INNER JOIN currencies 
							ON bankaccounts.currcode = currencies.currabrev
							WHERE accountcode=' . $PaymentItem->GLCode;
				$TrfToAccountResult = DB_query($SQL,$db);
				$TrfToBankRow = DB_fetch_array($TrfToAccountResult) ;
				$TrfToBankCurrCode = $TrfToBankRow['currcode'];
				$TrfToBankExRate = $TrfToBankRow['rate'];
				
				if ($_SESSION['PaymentDetail']->AccountCurrency == $TrfToBankCurrCode){
					/*Make sure to use the same rate if the transfer is between two bank accounts in the same currency */
					$TrfToBankExRate = $_SESSION['PaymentDetail']->FunctionalExRate;
				}
				
				/*Consider an example
					 functional currency NZD
					 bank account in AUD - 1 NZD = 0.90 AUD (FunctionalExRate)
					 paying USD - 1 AUD = 0.85 USD  (ExRate)
					 to a bank account in EUR - 1 NZD = 0.52 EUR
					 
					 oh yeah - now we are getting tricky! 
					 Lets say we pay USD 100 from the AUD bank account to the EUR bank account
					 
					 To get the ExRate for the bank account we are transferring money to 
					 we need to use the cross rate between the NZD-AUD/NZD-EUR
					 and apply this to the 
					 
					 the payment record will read 
					 exrate = 0.85 (1 AUD = USD 0.85)
					 amount = 100 (USD)
					 functionalexrate = 0.90 (1 NZD = AUD 0.90)
					 
					 the receipt record will read
					 
					 amount 100 (USD)
					 exrate    (1 EUR =  (0.85 x 0.90)/0.52 USD)
					 					(ExRate x FunctionalExRate) / USD Functional ExRate
					 functionalexrate =     (1NZD = EUR 0.52)
					 
				*/
				
				$ReceiptTransNo = GetNextTransNo( 2, $db);
				$SQL="INSERT INTO banktrans (transno,
								type,
								bankact,
								ref,
								exrate,
								functionalexrate,
								transdate,
								banktranstype,
								amount,
								currcode) ";
				$SQL= $SQL . "VALUES (" . $ReceiptTransNo . ',
							2,
							' . $PaymentItem->GLCode . ", '" 
							. _('Act Transfer From ') . $_SESSION['PaymentDetail']->Account . ' - ' . $PaymentItem->Narrative . " ',
							" . (($_SESSION['PaymentDetail']->ExRate * $_SESSION['PaymentDetail']->FunctionalExRate)/$TrfToBankExRate). ",
							" . $TrfToBankExRate . ",
							'" . FormatDateForSQL($_SESSION['PaymentDetail']->DatePaid) . "',
							'" . $_SESSION['PaymentDetail']->Paymenttype . "',
							" . $PaymentItem->Amount . ",
							'" . $_SESSION['PaymentDetail']->Currency . "'
						)";
   				$ErrMsg = _('Cannot insert a bank transaction because');
				$DbgMsg =  _('Cannot insert a bank transaction with the SQL');
				$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

			}
   	}
  } else {
	  /*Its a supplier payment type 22 */
  if($_SESSION['PaymentDetail']->AccountCurrency!=$_SESSION['CompanyRecord']['currencydefault']){
			$_SESSION['PaymentDetail']->FunctionalExRate = number_format(1/$_POST['TCambio'],6);
		}
  	
   	$CreditorTotal = (($_SESSION['PaymentDetail']->Discount + $_SESSION['PaymentDetail']->Amount)/$_SESSION['PaymentDetail']->ExRate)/$_SESSION['PaymentDetail']->FunctionalExRate;
		
		$TransNo = GetNextTransNo(22, $db);
		$Transtype = 22;
		
		/* Create a SuppTrans entry for the supplier payment */
		$SQL = "INSERT INTO supptrans (transno,
						type,
						supplierno,
						trandate,
						suppreference,
						rate,
						ovamount,
						transtext) ";
		$SQL = $SQL . 'VALUES (' . $TransNo . ",
					22,
					'" . $_SESSION['PaymentDetail']->SupplierID . "',
					'" . FormatDateForSQL($_SESSION['PaymentDetail']->DatePaid) . "',
					'" . $_SESSION['PaymentDetail']->Paymenttype . "',
					" . ($_SESSION['PaymentDetail']->ExRate/$_SESSION['PaymentDetail']->FunctionalExRate) . ",
					" . (-$_SESSION['PaymentDetail']->Amount-$_SESSION['PaymentDetail']->Discount) . ",
					'" . DB_escape_string($_SESSION['PaymentDetail']->Narrative) . "'
				)";

		$ErrMsg =  _('Cannot insert a payment transaction against the supplier because');
		$DbgMsg = _('Cannot insert a payment transaction against the supplier using the SQL');
		$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

		/*Update the supplier master with the date and amount of the last payment made */
		$SQL = "UPDATE suppliers SET
		    lastpaiddate = '" . FormatDateForSQL($_SESSION['PaymentDetail']->DatePaid) . "',
		    lastpaid=" . $_SESSION['PaymentDetail']->Amount ."
			  WHERE suppliers.supplierid='" . $_SESSION['PaymentDetail']->SupplierID . "'";



		$ErrMsg = _('Cannot update the supplier record for the date of the last payment made because');
		$DbgMsg = _('Cannot update the supplier record for the date of the last payment made using the SQL');
		$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

		$_SESSION['PaymentDetail']->Narrative = $_SESSION['PaymentDetail']->SupplierID . "-" . $_SESSION['PaymentDetail']->Narrative;

		if ($_SESSION['CompanyRecord']['gllink_creditors']==1){ /* then do the supplier control GLTrans */
	      /* Now debit creditors account with payment + discount */
			
			$SQL="INSERT INTO gltrans ( type,
							typeno,
							trandate,
							periodno,
							account,
							narrative,
							amount) ";
			// $_SESSION['CompanyRecord']['rh_advcreditorsact']
			// bowikaxu realhost - Feb 2008 - Insert a cuenta anticipo no proveedores
			$SQL=$SQL . "VALUES (
						22,
						" . $TransNo . ",
						'" . FormatDateForSQL($_SESSION['PaymentDetail']->DatePaid) . "',
						" . $PeriodNo . ",
						" . $_SESSION['CompanyRecord']['creditorsact'] . ",
						'" . DB_escape_string($_SESSION['PaymentDetail']->Narrative) . "',
						" . $CreditorTotal . "
					)";
			$ErrMsg = _('Cannot insert a GL transaction for the creditors account debit because');
			$DbgMsg = _('Cannot insert a GL transaction for the creditors account debit using the SQL');
			$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

			if ($_SESSION['PaymentDetail']->Discount !=0){
				/* Now credit Discount received account with discounts */
				$SQL="INSERT INTO gltrans ( type,
								typeno,
								trandate,
								periodno,
								account,
								narrative,
								amount) ";
				$SQL=$SQL . "VALUES (22,
						" . $TransNo . ",
						'" . FormatDateForSQL($_SESSION['PaymentDetail']->DatePaid) . "',
						" . $PeriodNo . ",
						" . $_SESSION['CompanyRecord']["pytdiscountact"] . ",
						'" . DB_escape_string($_SESSION['PaymentDetail']->Narrative) . "',
						" . (-$_SESSION['PaymentDetail']->Discount/$_SESSION['PaymentDetail']->ExRate/$_SESSION['PaymentDetail']->FunctionalExRate) . "
					  )";
				$ErrMsg = _('Cannot insert a GL transaction for the payment discount credit because');
				$DbgMsg = _('Cannot insert a GL transaction for the payment discount credit using the SQL');
				$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			} // end if discount
		} // end if gl creditors
	} // end if supplier
  
	if ($_SESSION['CompanyRecord']['gllink_creditors']==1){ /* then do the common GLTrans */
		// bowikaxu debug
		//prnMsg($_SESSION['PaymentDetail']->Amount/$_SESSION['PaymentDetail']->ExRate.' <> '.$_SESSION['PaymentDetail']->Currency.' <> '.$_SESSION['PaymentDetail']->AccountCurrency.' <> '.$_SESSION['CompanyRecord']['currencydefault'],'warn');
	  if ($_SESSION['PaymentDetail']->Amount/$_SESSION['PaymentDetail']->ExRate !=0){
		  
		  // bowikaxu realhost Feb 2008 - insert a la cuenta complementaria 
		  if($_SESSION['PaymentDetail']->Currency != $_SESSION['CompanyRecord']['currencydefault'] AND $_SESSION['PaymentDetail']->Currency == $_SESSION['PaymentDetail']->AccountCurrency){
		  	/* bowikaxu - do the bank account complementary account insert */
			  if(isset($_SESSION['PaymentDetail']->FunctionalExRate)){
			  	$Frate = $_SESSION['PaymentDetail']->FunctionalExRate;
			  }else {
			  	$sql = "SELECT rate FROM currencies WHERE currabrev = '".$_SESSION['PaymentDetail']->Currency."'";
			  	$rate = DB_fetch_array(DB_query($sql,$db));
			  	$Frate = $rate['rate'];
			  }
		  	/* Banco en la Moneda Extranjera */
		  	$SQL = "INSERT INTO gltrans ( type,
							typeno,
							trandate,
							periodno,
							account,
							narrative,
							amount) ";
		  	$SQL = $SQL . "VALUES (" . $Transtype . ",
						" . $TransNo . ",
						'" . FormatDateForSQL($_SESSION['PaymentDetail']->DatePaid) . "',
						" . $PeriodNo . ",
						" . $_SESSION['PaymentDetail']->Account . ",
						'" . $_SESSION['PaymentDetail']->Narrative . "',
						" . -($_SESSION['PaymentDetail']->Amount) . "
					)";//   /$_SESSION['PaymentDetail']->ExRate

		  	$ErrMsg =  _('Cannot insert a GL transaction for the bank account credit because');
		  	$DbgMsg =  _('Cannot insert a GL transaction for the bank account credit using the SQL');
		  	$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
		  	
		  	$CompNarrative = $_SESSION['PaymentDetail']->Amount . '/' . $Frate . '-' . $_SESSION['PaymentDetail']->Amount;
		  	/* Banco en la Moneda Local */
		  	$SQL = "INSERT INTO gltrans ( type,
							typeno,
							trandate,
							periodno,
							account,
							narrative,
							amount) ";
		  	$SQL = $SQL . "VALUES (" . $Transtype . ",
						" . $TransNo . ",
						'" . FormatDateForSQL($_SESSION['PaymentDetail']->DatePaid) . "',
						" . $PeriodNo . ",
						" . $_SESSION['PaymentDetail']->CompAccount . ",
						'" . $CompNarrative . " CC',
						" . -(($_SESSION['PaymentDetail']->Amount/$Frate)-$_SESSION['PaymentDetail']->Amount) . "
					)";

		  	$ErrMsg =  _('Cannot insert a GL transaction for the bank account credit because');
		  	$DbgMsg =  _('Cannot insert a GL transaction for the bank account credit using the SQL');
		  	$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
		  	// bowikaxu debug
		  	//echo "<BR>".$SQL."<BR>";
		  }else {
		  	/* Bank account entry first */
		  	$SQL = "INSERT INTO gltrans ( type,
							typeno,
							trandate,
							periodno,
							account,
							narrative,
							amount) ";
		  	$SQL = $SQL . "VALUES (" . $Transtype . ",
						" . $TransNo . ",
						'" . FormatDateForSQL($_SESSION['PaymentDetail']->DatePaid) . "',
						" . $PeriodNo . ",
						" . $_SESSION['PaymentDetail']->Account . ",
						'" . $_SESSION['PaymentDetail']->Narrative . "',
						" . (-$_SESSION['PaymentDetail']->Amount/$_SESSION['PaymentDetail']->ExRate) . "
					)";

		  	$ErrMsg =  _('Cannot insert a GL transaction for the bank account credit because');
		  	$DbgMsg =  _('Cannot insert a GL transaction for the bank account credit using the SQL');
		  	$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
		  }

	  }
  }

  /*now enter the BankTrans entry */
  
  // bowikaxu realhost august 07 - add the chequeno insert 
  // '". $_POST['ChequeNum']."' into rh_chequeno

  $narrative = $_SESSION['PaymentDetail']->Narrative;
  
  $SQL="INSERT INTO banktrans (transno,
					type,
					bankact,
					ref,
					exrate,
					functionalexrate,
					transdate,
					banktranstype,
					amount,
					currcode,
					rh_chequeno) ";
  $SQL= $SQL . "VALUES (" . $TransNo . ",
				" . $Transtype . ",
				" . $_SESSION['PaymentDetail']->Account . ",
				'" . DB_escape_string($_SESSION['PaymentDetail']->Narrative) . "',
				" . $_SESSION['PaymentDetail']->ExRate . " ,
				" . $_SESSION['PaymentDetail']->FunctionalExRate . ",
				'" . FormatDateForSQL($_SESSION['PaymentDetail']->DatePaid) . "',
				'" . $_SESSION['PaymentDetail']->Paymenttype . "',
				" . -$_SESSION['PaymentDetail']->Amount . ",
				'" . $_SESSION['PaymentDetail']->Currency . "',
				'".DB_escape_string($_POST['ChequeNum'])."'
			)";

	$ErrMsg = _('Cannot insert a bank transaction because');
	$DbgMsg = _('Cannot insert a bank transaction using the SQL');
	$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

  $SQL = "COMMIT";
  $ErrMsg = _('Cannot commit the changes because');
	$DbgMsg = _('The commit of the database transaction failed');
	$result= DB_query($SQL,$db,$ErrMsg,$DbgMsg);

	prnMsg(_('Payment') . ' ' . $TransNo . ' ' . _('has been sucessfully entered'),'success');

	$lastSupplier = ($_SESSION['PaymentDetail']->SupplierID);

	unset($_POST['BankAccount']);
	unset($_POST['DatePaid']);
	unset($_POST['ExRate']);
	unset($_POST['Paymenttype']);
	unset($_POST['Currency']);
	unset($_POST['Narrative']);
	unset($_POST['Amount']);
	unset($_POST['Discount']);
	unset($_SESSION['PaymentDetail']->GLItems);
	unset($_SESSION['PaymentDetail']);

  /*Set up a newy in case user wishes to enter another */
	echo '<BR><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '">' . _('Enter a General Ledger Payment') . '</A>';
	echo '<BR><A HREF="' . $rootpath . '/SelectSupplier.php?' . SID . '">' . _('Enter a Supplier Payment') . '</A>';
	
	// bowikaxu realhost get last insert - send the supptrans -> id field to the get value
	//$res = DB_Last_Insert_ID($db,'supptrans','id');
	
					$Last="SELECT MAX(id) AS ultimo
						FROM supptrans";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Can not retrieve supplier id');
					$DbgMsg = _('The SQL that failed was');
					$LastQuery = DB_query($Last,$db,$ErrMsg,$DbgMsg,true);
					$ultimo = DB_fetch_array($LastQuery);
					$BMove = $ultimo['ultimo'];	
					DB_free_result($LastQuery);		
	// bowikaxu realhost august 07 - show transactions to allocate
	$_POST['SupplierID'] = $_SESSION["PaymentDetail"]->SupplierID;
	$_GET['AllocTrans'] = $TransNo;
	echo "<BR><A HREF='" . $rootpath . "/SupplierAllocations.php?" . SID ."&AllocTrans=".$BMove."'>" . _('Allocate') . '</A>';
	// bowikaxu realhost - sept 2008 - direct file upload
	echo "<BR><A HREF='" . $rootpath . "/rh_upload.php?" . SID ."&type=22&typeno=".$TransNo."&comments=".$narrative."'>" . _('File Upload') . '</A>';
	
  }

  include('includes/footer.inc');
  exit;

} elseif (isset($_GET['Delete'])){
  /* User hit delete the receipt entry from the batch */
	$_SESSION['PaymentDetail']->Remove_GLItem($_GET['Delete']);
} elseif (isset($_POST['Process'])){ //user hit submit a new GL Analysis line into the payment

   if ($_POST['GLManualCode']!="" AND is_numeric($_POST['GLManualCode'])){

	$SQL = "select accountname
			FROM chartmaster
			WHERE accountcode=" . $_POST['GLManualCode'];

	$Result=DB_query($SQL,$db);

	if (DB_num_rows($Result)==0){
		prnMsg( _('The manual GL code entered does not exist in the database') . ' - ' . _('so this GL analysis item could not be added'),'warn');
		unset($_POST['GLManualCode']);
	} else {
		$myrow = DB_fetch_array($Result);
		$_SESSION['PaymentDetail']->add_to_glanalysis($_POST['GLAmount'],
								$_POST['GLNarrative'],
								$_POST['GLManualCode'],
								$myrow['accountname']);
	}
   } else {
   	$SQL = "select accountname FROM chartmaster WHERE accountcode=" . $_POST['GLCode'];
	$Result=DB_query($SQL,$db);
	$myrow=DB_fetch_array($Result);
   	$_SESSION['PaymentDetail']->add_to_glanalysis($_POST['GLAmount'],
							$_POST['GLNarrative'],
							$_POST['GLCode'],
							$myrow['accountname']);
   }

   /*Make sure the same receipt is not double processed by a page refresh */
   $_POST['Cancel'] = 1;
}

if (isset($_POST['Cancel'])){
   unset($_POST['GLAmount']);
   unset($_POST['GLNarrative']);
   unset($_POST['GLCode']);
   unset($_POST['AccountName']);
}

/*set up the form whatever */
if (!isset($_POST['DatePaid'])) {
	$_POST['DatePaid'] = '';
}

if (isset($_POST['DatePaid']) and ($_POST['DatePaid']=="" OR !Is_Date($_SESSION['PaymentDetail']->DatePaid))){
	 $_POST['DatePaid']= Date($_SESSION['DefaultDateFormat']);
	 $_SESSION['PaymentDetail']->DatePaid = $_POST['DatePaid'];
}

if ($_SESSION['PaymentDetail']->Currency=='' AND $_SESSION['PaymentDetail']->SupplierID==''){
	$_SESSION['PaymentDetail']->Currency=$_SESSION['CompanyRecord']['currencydefault'];
}


if (isset($_POST['BankAccount']) AND $_POST['BankAccount']!='') {
	$SQL = "SELECT bankaccountname
			FROM bankaccounts,
				chartmaster
		WHERE bankaccounts.accountcode= chartmaster.accountcode
		AND chartmaster.accountcode=" . $_POST['BankAccount'];

	$ErrMsg = _('The bank account name cannot be retrieved because');
	$DbgMsg = _('SQL used to retrieve the bank account name was');

	$result= DB_query($SQL,$db,$ErrMsg,$DbgMsg);

	if (DB_num_rows($result)==1){
		$myrow = DB_fetch_row($result);
		$_SESSION['PaymentDetail']->BankAccountName = $myrow[0];
		unset($result);
	} elseif (DB_num_rows($result)==0){
		prnMsg( _('The bank account number') . ' ' . $_POST['BankAccount'] . ' ' . _('is not set up as a bank account with a valid general ledger account'),'error');
	}
}


echo '<FORM id="form1" ACTION=' . $_SERVER['PHP_SELF'] . '?' . SID . ' METHOD=POST>';


echo '<FONT SIZE=3 COLOR=BLUE>' . _('Payment');

if ($_SESSION['PaymentDetail']->SupplierID!=""){
	echo ' ' . _('to') . ' ' . $_SESSION['PaymentDetail']->SuppName;
}

if ($_SESSION['PaymentDetail']->BankAccountName!=""){
	echo ' ' . _('from the') . ' ' . $_SESSION['PaymentDetail']->BankAccountName;
}

echo ' ' . _('on') . ' ' . $_SESSION['PaymentDetail']->DatePaid . '</FONT>';

echo '<P><table>';

$SQL = 'SELECT bankaccountname,
		bankaccounts.accountcode,
		bankaccounts.currcode
		FROM bankaccounts,
		chartmaster
		WHERE bankaccounts.accountcode=chartmaster.accountcode';

$ErrMsg = _('The bank accounts could not be retrieved because');
$DbgMsg = _('The SQL used to retrieve the bank acconts was');
$AccountsResults = DB_query($SQL,$db,$ErrMsg,$DbgMsg);

echo '<tr><td>' . _('Bank Account') . ':</td><td><select name="BankAccount">';

if (DB_num_rows($AccountsResults)==0){
	echo '</select></td></tr></table><P>';
	prnMsg( _('Bank Accounts have not yet been defined. You must first') . ' <A HREF="' . $rootpath . '/BankAccounts.php">' . _('define the bank accounts') . '</A> ' . _('and general ledger accounts to be affected'),'warn');
	include('includes/footer.inc');
	exit;
} else {
	while ($myrow=DB_fetch_array($AccountsResults)){
	/*list the bank account names */
		if (!isset($_POST['BankAccount']) AND $myrow['currcode']==$_SESSION['CompanyRecord']['currencydefault']){
			$_POST['BankAccount']=$myrow['accountcode'];
		}
		if ($_POST['BankAccount']==$myrow['accountcode']){
			echo '<OPTION selectED VALUE="' . $myrow['accountcode'] . '">' . $myrow['bankaccountname'] . ' - ' . $myrow['currcode'];
		} else {
			echo '<OPTION VALUE="' . $myrow['accountcode'] . '">' . $myrow['bankaccountname'] . ' - ' . $myrow['currcode'];
		}
	}
	echo '</select></td></tr>';
}


echo '<tr><td>' . _('Date Paid') . ':</td>
	<td><input type="text" name="DatePaid" maxlength=10 size=11 value="' . $_SESSION['PaymentDetail']->DatePaid . '"></td>
	</tr>';


if ($_SESSION['PaymentDetail']->SupplierID==''){
	echo '<tr><td>' . _('Currency of Payment') . ':</td><td><select name="Currency">';
	$SQL = 'SELECT currency, currabrev, rate FROM currencies';
	$result=DB_query($SQL,$db);

	if (DB_num_rows($result)==0){
		echo '</select></td></tr>';
		prnMsg( _('No currencies are defined yet') . '. ' . _('Payments cannot be entered until a currency is defined'),'error');
	} else {
		while ($myrow=DB_fetch_array($result)){
		if ($_SESSION['PaymentDetail']->Currency==$myrow['currabrev']){
			echo '<option selected value=' . $myrow['currabrev'] . '>' . $myrow['currency'];
		} else {
			echo '<option value=' . $myrow['currabrev'] . '>' . $myrow['currency'];
		}
		}
		echo '</select></td><td><i>' . _('The transaction currency does not need to be the same as the bank account currency') . '</i></td></tr>';
	}
} else { /*its a supplier payment so it must be in the suppliers currency */
	echo '<tr><td>' . _('Supplier Currency') . ':</td><td>' . $_SESSION['PaymentDetail']->Currency . '</td></tr>';
	echo '<input type="hidden" name="Currency" value="' . $_SESSION['PaymentDetail']->Currency . '">';
	/*get the default rate from the currency table if it has not been set */
	if (!isset($_POST['ExRate']) OR $_POST['ExRate']==''){
		$SQL = "SELECT rate FROM currencies WHERE currabrev='" . $_SESSION['PaymentDetail']->Currency ."'";
		$Result=DB_query($SQL,$db);
		$myrow=DB_fetch_row($Result);
		$_POST['ExRate']=$myrow[0];
	}

}

if (!isset($_POST['ExRate'])){
	$_POST['ExRate']=1;
}

if (!isset($_POST['FunctionalExRate'])){ 
	$_POST['FunctionalExRate']=1;
}
if ($_SESSION['PaymentDetail']->AccountCurrency!=$_SESSION['PaymentDetail']->Currency AND isset($_SESSION['PaymentDetail']->AccountCurrency)){
	if (isset($SuggestedExRate)){
		$SuggestedExRateText = '<b>' . _('Suggested rate:') . ' ' . number_format($SuggestedExRate,4) . '</b>';
	} else {
		$SuggestedExRateText ='';
	}
	if ($_POST['ExRate']==1 AND isset($SuggestedExRate)){
		$_POST['ExRate'] = $SuggestedExRate;
	}
	echo '<tr><td>' . _('Payment Exchange Rate') . ':</td>
				<td><input type="text" size=6 name="TCambio" id="TCambio" value="'.$_POST['TCambio'].'" > <input type="text" name="ExRate" id="ExRate" readonly maxlength=10 size=12 value="' . $_POST['ExRate'] . '"></td>
			<td>' . $SuggestedExRateText . ' <i>' . _('The exchange rate between the currency of the bank account currency and the currency of the payment') . '. 1 ' . $_SESSION['PaymentDetail']->AccountCurrency . ' = ? ' . $_SESSION['PaymentDetail']->Currency . '</i></td></tr>';
}

if ($_SESSION['PaymentDetail']->AccountCurrency!=$_SESSION['CompanyRecord']['currencydefault'] 
												AND isset($_SESSION['PaymentDetail']->AccountCurrency)){
	if (isset($SuggestedFunctionalExRate)){
		$SuggestedFunctionalExRateText = '<b>' . _('Suggested rate:') . ' ' . number_format($SuggestedFunctionalExRate,4) . '</b>';
	} else {
		$SuggestedFunctionalExRateText ='';
	}
	if ($_POST['FunctionalExRate']==1 AND isset($SuggestedFunctionalExRate)){
		$_POST['FunctionalExRate'] = $SuggestedFunctionalExRate;
		
		if(isset($_POST['TCambio']) and $_POST['TCambio'] != ""){
			$_POST['FunctionalExRate'] = number_format(1/$_POST['TCambio'],6);
		}
	}
	echo '<tr><td>' . _('Functional Exchange Rate') . ':</td><td><input type="text" size=6 name="TCambio" id="TCambio" value="'.$_POST['TCambio'].'" > <input type="text" name="FunctionalExRate" id="FunctionalExRate" maxlength=10 size=12 value="' . $_POST['FunctionalExRate'] . '"></td>
			<td>' . ' ' . $SuggestedFunctionalExRateText . ' <i>' . _('The exchange rate between the currency of the business (the functional currency) and the currency of the bank account') .  '. 1 ' . $_SESSION['CompanyRecord']['currencydefault'] . ' = ? ' . $_SESSION['PaymentDetail']->AccountCurrency . '</i></td></tr>';
}
echo '<tr><td>' . _('Payment type') . ':</td><td><select name="Paymenttype">';

include('includes/GetPaymentMethods.php');
/* The array Payttypes is set up in includes/GetPaymentMethods.php 
payment methods can be modified from the setup tab of the main menu under payment methods*/

foreach ($PaytTypes as $PaytType) {

	if (isset($_POST['Paymenttype']) and $_POST['Paymenttype']==$PaytType){
		echo '<option selected value="' . $PaytType . '">' . $PaytType;
	} else {
		echo '<option Value="' . $PaytType . '">' . $PaytType;
	}
} //end foreach
echo '</select></td></tr>';

if (!isset($_POST['ChequeNum'])) {
	$_POST['ChequeNum']='';
}

echo '<tr><td>' . _('Cheque Number') . ':</td>
			<td><input type="text" name="ChequeNum" maxlength=8 size=10 value="' . $_POST['ChequeNum'] . '"></td></tr>';

if (!isset($_POST['Narrative'])) {
	$_POST['Narrative']='';
}

echo '<tr><td>' . _('Ref') . ':</td>
			<td colspan=2><input type="text" name="Narrative" maxlength=80 size=82 value="' . $_POST['Narrative'] . '"></td></tr>';
echo '<tr><td colspan=3><center><input type="submit" name="UpdateHeader" value="' . _('Update'). '"></center></td></tr>';
			
			
echo '</table>';


if ($_SESSION['CompanyRecord']['gllink_creditors']==1 AND $_SESSION['PaymentDetail']->SupplierID==''){
/* Set upthe form for the transaction entry for a GL Payment Analysis item */

	echo '<table width=100% border=1><tr>
			<th>' . _('Amount') . ' (' . $_SESSION['PaymentDetail']->Currency . ')</th>
			<th>' . _('GL Account') . '</th>
			<th>' . _('Narrative') . '</th>
		</tr>';

	$PaymentTotal = 0;
   	foreach ($_SESSION['PaymentDetail']->GLItems as $PaymentItem) {
	    	    echo '<tr>
		    		<td ALIGN=RIGHT>' . number_format($PaymentItem->Amount,2) . '</td>
				<td>' . $PaymentItem->GLCode . ' - ' . $PaymentItem->GLActName . '</td>
				<td>' . $PaymentItem->Narrative  . '</td>
				<td><a href="' . $_SERVER['PHP_SELF'] . '?' . SID . '&Delete=' . $PaymentItem->ID.'&BankAccount='.$_POST['BankAccount'].'&DatePaid='.$_POST['DatePaid'].'&Currency='.$_POST['Currency'].'&ChequeNum='.$_POST['ChequeNum'].'&Narrative='.$_POST['Narrative'].'&Paymenttype='.$_POST['Paymenttype'].'&ExRate='.$_POST['ExRate']. '">' . _('Delete') . '</a></td>
				</tr>';
	    $PaymentTotal += $PaymentItem->Amount;

   	}
   	echo '<tr><td ALIGN=RIGHT><B>' . number_format($PaymentTotal,2) . '</B></td></tr></table>';


	echo '<BR><CENTER>' . _('General Ledger Payment Analysis Entry') . '<table>';

	/*now set up a GLCode field to select from avaialble GL accounts */
	echo '<tr><td>' . _('Enter GL Account Manually') . ':</td>
		<td><input type=Text Name="GLManualCode" Maxlength=12 SIZE=12 VALUE=' . $_POST['GLManualCode'] . '></td></tr>';
	echo '<tr><td>' . _('select GL Account') . ':</td>
		<td><select name="GLCode">';

	$SQL = 'SELECT accountcode, 
					accountname 
			FROM chartmaster 
			ORDER BY accountcode';
			
	$result=DB_query($SQL,$db);
	if (DB_num_rows($result)==0){
	   echo '</select></td></tr>';
	   prnMsg(_('No General ledger accounts have been set up yet') . ' - ' . _('payments cannot be analysed against GL accounts until the GL accounts are set up'),'error');
	} else {
		while ($myrow=DB_fetch_array($result)){
		    if ($_POST['GLCode']==$myrow["accountcode"]){
			echo '<OPTION selectED value=' . $myrow['accountcode'] . '>' . $myrow['accountcode'] . ' - ' . $myrow['accountname'];
		    } else {
			echo '<OPTION value=' . $myrow['accountcode'] . '>' . $myrow['accountcode'] . ' - ' . $myrow['accountname'];
		    }
		}
		echo '</select></td></tr>';
	}
	echo '<tr><td>' . _('GL Narrative') . ':</td><td><input type="text" name="GLNarrative" maxlength=50 size=52 value="' . $_POST['GLNarrative'] . '"></td></tr>';
	echo '<tr><td>' . _('Amount') . ' (' . $_SESSION['PaymentDetail']->Currency . '):</td><td><input type=Text Name="GLAmount" Maxlength=12 SIZE=12 VALUE=' . $_POST['GLAmount'] . '></td></tr>';
	echo '</table>';
	echo '<center><input type=submit name="Process" value="' . _('Accept') . '"><input type=submit name="Cancel" value="' . _('Cancel') . '"></center>';

} else {
/*a supplier is selected or the GL link is not active then set out
the fields for entry of receipt amt and disc */


	echo '<table><tr><td>' . _('Amount of Payment') . ' ' . $_SESSION['PaymentDetail']->Currency . ':</td>
					<td><input type="text" name="Amount" maxlength=12 size=13 value=' . $_SESSION['PaymentDetail']->Amount . '></td></tr>';

	if (isset($_SESSION['PaymentDetail']->SupplierID)){ /*So it is a supplier payment so show the discount entry item */
		echo '<tr><td>' . _('Amount of Discount') . ':</td>
					<td><input type="text" name="Discount" maxlength=12 size=13 value=' . $_SESSION['PaymentDetail']->Discount . '></td></tr>';
		echo '<input type="hidden" name="SuppName" value="' . $_SESSION['PaymentDetail']->SuppName . '">';
	} else {
		echo '<input type="hidden" name="discount" Value=0>';
	}
	echo '</table>';

}

echo '<BR><BR><input type=submit name="CommitBatch" value="' . _('Accept and Process Payment') . '">';
echo '</form>';

include('includes/footer.inc');
?>
