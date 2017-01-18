<?php
/* $Revision: 171 $ */

include('includes/DefineCartClass.php');
$PageSecurity = 1;
/* Session started in session.inc for password checking and authorisation level check
config.php is in turn included in session.inc*/

include('includes/session.inc');
include('includes/GetSalesTransGLCodes.inc');
include('includes/class.pdf.php');

$title = _('Reyna Garza POS');

include('includes/header.inc');
include('includes/GetPrice.inc');
include('includes/rh_GetDiscount.inc');
include('includes/SQL_CommonFunctions.inc');

if (isset($_POST['QuickEntry'])){
   unset($_POST['PartSearch']);
}

if (isset($_GET['NewItem'])){
	$NewItem = $_GET['NewItem'];
}

if (isset($_GET['NewOrder'])){
  /*New order entry - clear any existing order details from the Items object and initiate a newy*/
	 if (isset($_SESSION['Items'])){
		unset ($_SESSION['Items']->LineItems);
		$_SESSION['Items']->ItemsOrdered=0;
		unset ($_SESSION['Items']);
	}
	
	Session_register('Items');
	Session_register('RequireCustomerSelection');
	Session_register('CreditAvailable');
	Session_register('ExistingOrder');
	Session_register('PrintedPackingSlip');
	Session_register('DatePackingSlipPrinted');
	Session_register('ProcessingOrder');
	Session_Register('CurrencyRate');
		
	$_SESSION['ExistingOrder']=0;
	$_SESSION['Items'] = new cart;

	$_SESSION['Items']->DebtorNo='';
	$_SESSION['RequireCustomerSelection']=1;

}

if (isset($_GET['ModifyOrderNumber'])
	AND $_GET['ModifyOrderNumber']!=''){

/* The delivery check screen is where the details of the order are either updated or inserted depending on the value of ExistingOrder */

	if (isset($_SESSION['Items'])){
		unset ($_SESSION['Items']->LineItems);
		unset ($_SESSION['Items']);
	}

	Session_register('Items');
	Session_register('RequireCustomerSelection');
	Session_register('CreditAvailable');
	Session_register('ExistingOrder');
	Session_register('PrintedPackingSlip');
	Session_register('DatePackingSlipPrinted');
	Session_register('ProcessingOrder');
	Session_Register('CurrencyRate');

	$_SESSION['ExistingOrder']=$_GET['ModifyOrderNumber'];
	$_SESSION['RequireCustomerSelection'] = 0;
	$_SESSION['Items'] = new cart;

/*read in all the guff from the selected order into the Items cart  */


	$OrderHeaderSQL = 'SELECT salesorders.debtorno,
				debtorsmaster.name,
				salesorders.branchcode,
				salesorders.customerref,
				salesorders.comments,
				salesorders.orddate,
				salesorders.ordertype,
				salestypes.sales_type,
				salesorders.shipvia,
				salesorders.deliverto,
				salesorders.deladd1,
				salesorders.deladd2,
				salesorders.deladd3,
				salesorders.deladd4,
				salesorders.deladd5,
				salesorders.deladd6,
				salesorders.contactphone,
				salesorders.contactemail,
				salesorders.freightcost,
				salesorders.deliverydate,
				currencies.rate as currency_rate,
				salesorders.fromstkloc,
				salesorders.printedpackingslip,
				salesorders.datepackingslipprinted,
				salesorders.quotation,
				salesorders.deliverblind,
				locations.taxprovinceid,
				custbranch.taxgroupid
			FROM salesorders, 
				debtorsmaster,
				currencies,
				custbranch,
				locations,
				salestypes
			WHERE salesorders.ordertype=salestypes.typeabbrev
			AND salesorders.debtorno = debtorsmaster.debtorno
			AND salesorders.branchcode = custbranch.branchcode
			AND salesorders.debtorno = custbranch.debtorno
			AND debtorsmaster.currcode = currencies.currabrev
			AND locations.loccode=salesorders.fromstkloc
			AND salesorders.orderno = ' . $_GET['ModifyOrderNumber'];

	$ErrMsg =  _('The order cannot be retrieved because');
	$GetOrdHdrResult = DB_query($OrderHeaderSQL,$db,$ErrMsg);

	if (DB_num_rows($GetOrdHdrResult)==1) {

		$myrow = DB_fetch_array($GetOrdHdrResult);

		$_SESSION['Items']->DebtorNo = $myrow['debtorno'];
/*CustomerID defined in header.inc */
		$_SESSION['Items']->Branch = $myrow['branchcode'];
		$_SESSION['Items']->CustomerName = $myrow['name'];
		$_SESSION['Items']->CustRef = $myrow['customerref'];
		$_SESSION['Items']->Comments = $myrow['comments'];

		$_SESSION['Items']->DefaultSalesType =$myrow['ordertype'];
		$_SESSION['Items']->SalesTypeName =$myrow['sales_type'];
		$_SESSION['Items']->DefaultCurrency = $myrow['currcode'];
		$_SESSION['Items']->ShipVia = $myrow['shipvia'];
		$BestShipper = $myrow['shipvia'];
		$_SESSION['Items']->DeliverTo = $myrow['deliverto'];
		$_SESSION['Items']->DeliveryDate = ConvertSQLDate($myrow['deliverydate']);
		$_SESSION['Items']->DelAdd1 = $myrow['deladd1'];
		$_SESSION['Items']->DelAdd2 = $myrow['deladd2'];
		$_SESSION['Items']->DelAdd3 = $myrow['deladd3'];
		$_SESSION['Items']->DelAdd4 = $myrow['deladd4'];
		$_SESSION['Items']->DelAdd5 = $myrow['deladd5'];
		$_SESSION['Items']->DelAdd6 = $myrow['deladd6'];
		$_SESSION['Items']->PhoneNo = $myrow['contactphone'];
		$_SESSION['Items']->Email = $myrow['contactemail'];
		$_SESSION['Items']->Location = $myrow['fromstkloc'];
		$_SESSION['Items']->Quotation = $myrow['quotation'];
		$_SESSION['Items']->FreightCost = $myrow['freightcost'];
		$_SESSION['Items']->Orig_OrderDate = $myrow['orddate'];
		$_SESSION['CurrencyRate'] = $myrow['currency_rate'];
		//$_SESSION['CurrencyRate'] = 1;
		$_SESSION['PrintedPackingSlip'] = $myrow['printedpackingslip'];
		$_SESSION['DatePackingSlipPrinted'] = $myrow['datepackingslipprinted'];
		$_SESSION['Items']->DeliverBlind = $myrow['deliverblind'];
		$_SESSION['Items']->TaxGroup = $myrow['taxgroupid'];
		$_SESSION['Items']->DispatchTaxProvince = $myrow['taxprovinceid'];
		
/*need to look up customer name from debtors master then populate the line items array with the sales order details records */
		$LineItemsSQL = "SELECT salesorderdetails.stkcode,
				stockmaster.description,
				stockmaster.volume,
				stockmaster.kgs,
				stockmaster.units,
				salesorderdetails.unitprice,
				salesorderdetails.orderlineno,
				salesorderdetails.quantity,
				salesorderdetails.discountpercent,
				salesorderdetails.actualdispatchdate,
				salesorderdetails.qtyinvoiced,
				salesorderdetails.narrative,
				locstock.quantity as qohatloc,
				stockmaster.mbflag,
				stockmaster.discountcategory,
				stockmaster.decimalplaces,
				stockmaster.taxcatid
				FROM salesorderdetails INNER JOIN stockmaster
				ON salesorderdetails.stkcode = stockmaster.stockid
				INNER JOIN locstock ON locstock.stockid = stockmaster.stockid
				WHERE  locstock.loccode = '" . $myrow['fromstkloc'] . "'
				AND  salesorderdetails.completed=0
				AND salesorderdetails.orderno =" . $_GET['ModifyOrderNumber'];

		$ErrMsg = _('The line items of the order cannot be retrieved because');
		$LineItemsResult = db_query($LineItemsSQL,$db,$ErrMsg);
		if (db_num_rows($LineItemsResult)>0) {

			while ($myrow=db_fetch_array($LineItemsResult)) {
					$_SESSION['Items']->add_to_cart($myrow['stkcode'],
								$myrow['quantity'],
								$myrow['description'],
								$myrow['unitprice'],
								$myrow['discountpercent'],
								$myrow['units'],
								$myrow['volume'],
								$myrow['kgs'],
								$myrow['qohatloc'],
								$myrow['mbflag'],
								$myrow['actualdispatchdate'],
								$myrow['qtyinvoiced'],
								$myrow['discountcategory'],
								0,	/*Controlled*/
								0,	/*Serialised */
								$myrow['decimalplaces'],
								$myrow['narrative'],
								'No',
								$myrow['orderlineno'],
								$myrow['taxcatid']);
								
								$_SESSION['Items']->LineItems[$myrow['orderlineno']]->StandardCost = $myrow['standardcost'];
				/*Just populating with existing order - no DBUpdates */
				
				/*Calculate the taxes applicable to this line item from the customer branch Tax Group and Item Tax Category */
				$_SESSION['Items']->GetTaxes($myrow['orderlineno']);
			} /* line items from sales order details */
		} //end of checks on returned data set
	}
}

if (!isset($_SESSION['Items'])){
	/* It must be a new order being created $_SESSION['Items'] would be set up from the order
	modification code above if a modification to an existing order. Also $ExistingOrder would be
	set to 1. The delivery check screen is where the details of the order are either updated or
	inserted depending on the value of ExistingOrder */

	Session_register('Items');
	Session_register('RequireCustomerSelection');
	Session_register('CreditAvailable');
	Session_register('ExistingOrder');
	Session_register('PrintedPackingSlip');
	Session_register('DatePackingSlipPrinted');
	Session_register('ProcessingOrder');
	Session_Register('CurrencyRate');

	$_SESSION['ExistingOrder']=0;
	$_SESSION['Items'] = new cart;
	$_SESSION['PrintedPackingSlip'] =0; /*Of course cos the order aint even started !!*/

	if (in_array(2,$_SESSION['AllowedPageSecurityTokens']) AND ($_SESSION['Items']->DebtorNo=='' OR !isset($_SESSION['Items']->DebtorNo))){

	/* need to select a customer for the first time out if authorisation allows it and if a customer
	 has been selected for the order or not the session variable CustomerID holds the customer code
	 already as determined from user id /password entry  */
		$_SESSION['RequireCustomerSelection'] = 1;
	} else {
		$_SESSION['RequireCustomerSelection'] = 0;
	}
}

if (isset($_POST['ChangeCustomer']) AND $_POST['ChangeCustomer']!=''){

	if ($_SESSION['Items']->Any_Already_Delivered()==0){
		$_SESSION['RequireCustomerSelection']=1;
	} else {
		prnMsg(_('The customer the order is for cannot be modified once some of the order has been invoiced'),'warn');
	}
}

$msg='';

if (isset($_POST['SearchCust']) AND $_SESSION['RequireCustomerSelection']==1 AND in_array(2,$_SESSION['AllowedPageSecurityTokens'])){

	If ($_POST['Keywords']!='' AND $_POST['CustCode']!='') {
		$msg= _('Customer name keywords have been used in preference to the customer code extract entered');
	}
	If ($_POST['Keywords']=='' AND $_POST['CustCode']=='') {
		$msg=_('At least one Customer Name keyword OR an extract of a Customer Code must be entered for the search');
	} else {
		If (strlen($_POST['Keywords'])>0) {
		//insert wildcard characters in spaces
			$_POST['Keywords'] = strtoupper($_POST['Keywords']);
			$i=0;
			$SearchString = '%';
			while (strpos($_POST['Keywords'], ' ', $i)) {
				$wrdlen=strpos($_POST['Keywords'],' ',$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . '%';
				$i=strpos($_POST['Keywords'],' ',$i) +1;
			}
			$SearchString = $SearchString. substr($_POST['Keywords'],$i).'%';

			$SQL = "SELECT custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno,
					custbranch.branchcode,
					custbranch.debtorno
				FROM custbranch
				WHERE custbranch.brname " . LIKE . " '$SearchString'
				AND custbranch.disabletrans=0";

		} elseif (strlen($_POST['CustCode'])>0){

			$_POST['CustCode'] = strtoupper($_POST['CustCode']);

			$SQL = "SELECT custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno,
					custbranch.branchcode,
					custbranch.debtorno
				FROM custbranch
				WHERE custbranch.branchcode " . LIKE . " '%" . $_POST['CustCode'] . "%'
				AND custbranch.disabletrans=0";
		}

		$ErrMsg = _('The searched customer records requested cannot be retrieved because');
		$result_CustSelect = DB_query($SQL,$db,$ErrMsg);

		if (DB_num_rows($result_CustSelect)==1){
			$myrow=DB_fetch_array($result_CustSelect);
			$_POST['Select'] = $myrow['debtorno'] . ' - ' . $myrow['branchcode'];
		} elseif (DB_num_rows($result_CustSelect)==0){
			prnMsg(_('No customer records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
		}
	} /*one of keywords or custcode was more than a zero length string */
} /*end of if search for customer codes/names */


// will only be true if page called from customer selection form or set because only one customer
// record returned from a search so parse the $Select string into customer code and branch code */
if (isset($_POST['Select']) AND $_POST['Select']!='') {

	$_SESSION['Items']->Branch = substr($_POST['Select'],strpos($_POST['Select'],' - ')+3);

	$_POST['Select'] = substr($_POST['Select'],0,strpos($_POST['Select'],' - '));

	// Now check to ensure this account is not on hold */
	$sql = "SELECT debtorsmaster.name,
			holdreasons.dissallowinvoices,
			debtorsmaster.salestype,
			salestypes.sales_type,
			debtorsmaster.currcode
		FROM debtorsmaster,
			holdreasons,
			salestypes
		WHERE debtorsmaster.salestype=salestypes.typeabbrev
		AND debtorsmaster.holdreason=holdreasons.reasoncode
		AND debtorsmaster.debtorno = '" . $_POST['Select'] . "'";

	$ErrMsg = _('The details of the customer selected') . ': ' .  $_POST['Select'] . ' ' . _('cannot be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the customer details and failed was') . ':';
	$result =DB_query($sql,$db,$ErrMsg,$DbgMsg);

	$myrow = DB_fetch_row($result);
	if ($myrow[1] != 1){
		
		if ($myrow[1]==2){
			prnMsg(_('The') . ' ' . $myrow[0] . ' ' . _('account is currently flagged as an account that needs to be watched please contact the credit control personnel to discuss'),'warn');
		}
		
		$_SESSION['Items']->DebtorNo=$_POST['Select'];
		$_SESSION['RequireCustomerSelection']=0;
		$_SESSION['Items']->CustomerName = $myrow[0];

# the sales type determines the price list to be used by default the customer of the user is
# defaulted from the entry of the userid and password.

		$_SESSION['Items']->DefaultSalesType = $myrow[2];
		$_SESSION['Items']->SalesTypeName = $myrow[3];
		$_SESSION['Items']->DefaultCurrency = $myrow[4];

# the branch was also selected from the customer selection so default the delivery details from the customer branches table CustBranch. The order process will ask for branch details later anyway

		$sql = "SELECT custbranch.brname,
				custbranch.braddress1,
				custbranch.braddress2,
				custbranch.braddress3,
				custbranch.braddress4,
				custbranch.braddress5,
				custbranch.braddress6,
				custbranch.phoneno,
				custbranch.email,
				custbranch.defaultlocation,
				custbranch.defaultshipvia,
				custbranch.deliverblind,
				custbranch.taxgroupid,
				locations.taxprovinceid
			FROM custbranch, locations
			WHERE custbranch.branchcode='" . $_SESSION['Items']->Branch . "'
			AND custbranch.debtorno = '" . $_POST['Select'] . "'
			AND custbranch.defaultlocation = locations.loccode";

		$ErrMsg = _('The customer branch record of the customer selected') . ': ' . $_POST['Select'] . ' ' . _('cannot be retrieved because');
		$DbgMsg = _('SQL used to retrieve the branch details was') . ':';
		$result =DB_query($sql,$db,$ErrMsg,$DbgMsg);

		if (DB_num_rows($result)==0){

			prnMsg(_('The branch details for branch code') . ': ' . $_SESSION['Items']->Branch . ' ' . _('against customer code') . ': ' . $_POST['Select'] . ' ' . _('could not be retrieved') . '. ' . _('Check the set up of the customer and branch'),'error');

			if ($debug==1){
				echo '<BR>' . _('The SQL that failed to get the branch details was') . ':<BR>' . $sql;
			}
			include('includes/footer.inc');
			exit;
		}

		$myrow = DB_fetch_row($result);
		$_SESSION['Items']->DeliverTo = $myrow[0];
		$_SESSION['Items']->DelAdd1 = $myrow[1];
		$_SESSION['Items']->DelAdd2 = $myrow[2];
		$_SESSION['Items']->DelAdd3 = $myrow[3];
		$_SESSION['Items']->DelAdd4 = $myrow[4];
		$_SESSION['Items']->DelAdd5 = $myrow[5];
		$_SESSION['Items']->DelAdd6 = $myrow[6];
		$_SESSION['Items']->PhoneNo = $myrow[7];
		$_SESSION['Items']->Email = $myrow[8];
		$_SESSION['Items']->Location = $myrow[9];
		$_SESSION['Items']->ShipVia = $myrow[10];
		$_SESSION['Items']->DeliverBlind = $myrow[11];
		$_SESSION['Items']->TaxGroup = $myrow[12];
		$_SESSION['Items']->DispatchTaxProvince = $myrow[13];
		//$_SESSION['Items']->GetFreightTaxes();
		
		if ($_SESSION['CheckCreditLimits'] > 0){  /*Check credit limits is 1 for warn and 2 for prohibit sales */
			$_SESSION['Items']->CreditAvailable = GetCreditAvailable($_POST['Select'],$db);
			
			if ($_SESSION['CheckCreditLimits']==1 AND $_SESSION['Items']->CreditAvailable <=0){
				prnMsg(_('The') . ' ' . $myrow[0] . ' ' . _('account is currently at or over their credit limit'),'warn');
			} elseif ($_SESSION['CheckCreditLimits']==2 AND $_SESSION['Items']->CreditAvailable <=0){
				prnMsg(_('No more orders can be placed by') . ' ' . $myrow[0] . ' ' . _(' their account is currently at or over their credit limit'),'warn');
				include('includes/footer.inc');
				exit;
			}
		}

	} else {
		prnMsg(_('The') . ' ' . $myrow[0] . ' ' . _('account is currently on hold please contact the credit control personnel to discuss'),'warn');
	}

} elseif (!$_SESSION['Items']->DefaultSalesType OR $_SESSION['Items']->DefaultSalesType=='')	{

#Possible that the check to ensure this account is not on hold has not been done
#if the customer is placing own order, if this is the case then
#DefaultSalesType will not have been set as above

	$sql = "SELECT debtorsmaster.name,
			holdreasons.dissallowinvoices,
			debtorsmaster.salestype,
			debtorsmaster.currcode
		FROM debtorsmaster, holdreasons
		WHERE debtorsmaster.holdreason=holdreasons.reasoncode
		AND debtorsmaster.debtorno = '" . $_SESSION['Items']->DebtorNo . "'";

	$ErrMsg = _('The details for the customer selected') . ': ' . $_POST['Select'] . ' ' . _('cannot be retrieved because');
	$DbgMsg = _('SQL used to retrieve the customer details was') . ':<BR>' . $sql;
	$result =DB_query($sql,$db,$ErrMsg,$DbgMsg);

	$myrow = DB_fetch_row($result);
	if ($myrow[1] == 0){
		$_SESSION['Items']->CustomerName = $myrow[0];

# the sales type determines the price list to be used by default the customer of the user is
# defaulted from the entry of the userid and password.

		$_SESSION['Items']->DefaultSalesType = $myrow[2];
		$_SESSION['Items']->DefaultCurrency = $myrow[3];
		$_SESSION['Items']->Branch = $_SESSION['UserBranch'];

	// the branch would be set in the user data so default delivery details as necessary. However,
	// the order process will ask for branch details later anyway

		$sql = "SELECT custbranch.brname,
			custbranch.braddress1,
			custbranch.braddress2,
			custbranch.braddress3,
			custbranch.braddress4,
			custbranch.braddress5,
			custbranch.braddress6,
			custbranch.phoneno,
			custbranch.email,
			custbranch.defaultlocation,
			custbranch.deliverblind,
			custbranch.taxgroupid,
			locations.taxprovinceid
			FROM custbranch, locations
			WHERE custbranch.branchcode='" . $_SESSION['Items']->Branch . "'
			AND custbranch.debtorno = '" . $_SESSION['Items']->DebtorNo . "'
			AND locations.loccode = custbranch.defaultlocation";

		$ErrMsg = _('The customer branch record of the customer selected') . ': ' . $_POST['Select'] . ' ' . _('cannot be retrieved because');
		$DbgMsg = _('SQL used to retrieve the branch details was');
		$result =DB_query($sql,$db,$ErrMsg, $DbgMsg);

		$myrow = DB_fetch_row($result);
		$_SESSION['Items']->DeliverTo = $myrow[0];
		$_SESSION['Items']->DelAdd1 = $myrow[1];
		$_SESSION['Items']->DelAdd2 = $myrow[2];
		$_SESSION['Items']->DelAdd3 = $myrow[3];
		$_SESSION['Items']->DelAdd4 = $myrow[4];
		$_SESSION['Items']->DelAdd5 = $myrow[5];
		$_SESSION['Items']->DelAdd6 = $myrow[6];
		$_SESSION['Items']->PhoneNo = $myrow[7];
		$_SESSION['Items']->Email = $myrow[8];
		$_SESSION['Items']->Location = $myrow[9];
		$_SESSION['Items']->DeliverBlind = $myrow[10];
		$_SESSION['Items']->TaxGroup = $myrow['taxgroupid'];
		$_SESSION['Items']->DispatchTaxProvince = $myrow['taxprovinceid'];
		//$_SESSION['Items']->GetFreightTaxes();

	} else {
		prnMsg(_('Sorry, your account has been put on hold for some reason, please contact the credit control personnel.'),'warn');
		include('includes/footer.inc');
		exit;
	}
}

if ($_SESSION['RequireCustomerSelection'] ==1
	OR !isset($_SESSION['Items']->DebtorNo)
	OR $_SESSION['Items']->DebtorNo=='' ) {
	//print_r($_SESSION);
	echo "<FORM ACTION=".$_SERVER['PHP_SELF'] . '?' .SID." METHOD=POST>";
	//echo "<FONT SIZE=3><CENTER>"._('POS - webERP Beta 1')."</FONT><BR><BR>";
	
	?>

	<CENTER><FONT SIZE=3><B><?php echo '- ' . _('Reyna Garza POS -'); ?></B></FONT><BR>

	<FORM ACTION="<?php echo $_SERVER['PHP_SELF'] . '?' .SID; ?>" METHOD=POST>
	<B><?php echo '<BR>' . $msg; ?></B>
	<TABLE CELLPADDING=3 COLSPAN=4>
	<TR>
	<TD><FONT SIZE=1><?php echo _('Enter text in the customer name'); ?>:</FONT></TD>
	<TD><INPUT TYPE="Text" NAME="Keywords" SIZE=20	MAXLENGTH=25></TD>
	<TD><FONT SIZE=3><B><?php echo _('OR'); ?></B></FONT></TD>
	<TD><FONT SIZE=1><?php echo _('Enter text extract in the customer code'); ?>:</FONT></TD>
	<TD><INPUT TYPE="Text" NAME="CustCode" SIZE=15	MAXLENGTH=18></TD>
	</TR>
	</TABLE>
	<CENTER><INPUT TYPE=SUBMIT NAME="SearchCust" VALUE="<?php echo _('Search Now'); ?>">
	<INPUT TYPE=SUBMIT ACTION=RESET VALUE="<?php echo _('Reset'); ?>"></CENTER>

	<script language='JavaScript' type='text/javascript'>
    	//<![CDATA[
            <!--
            document.forms[0].CustCode.select();
            document.forms[0].CustCode.focus();
            //-->
    	//]]>
	</script>
	<?php
	if(!isset($_POST['SearchCust'])){
		$SQL = "SELECT custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno,
					custbranch.branchcode,
					custbranch.debtorno
				FROM custbranch
				WHERE custbranch.branchcode " . LIKE . " '%VARIOS%'
				AND custbranch.disabletrans=0";
	}
		$ErrMsg = _('The searched customer records requested cannot be retrieved because');
		$result_CustSelect = DB_query($SQL,$db,$ErrMsg);
	
	if (DB_num_rows($result_CustSelect)==1){
			$myrow=DB_fetch_array($result_CustSelect);
			$_POST['Select'] = $myrow['debtorno'] . ' - ' . $myrow['branchcode'];
			
	} elseif (DB_num_rows($result_CustSelect)==0){
			prnMsg(_('No customer records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
	}

	If (isset($result_CustSelect)) {

		echo '<TABLE CELLPADDING=2 COLSPAN=7 BORDER=2>';

		$TableHeader = '<TR>
				<TD class="tableheader">' . _('Codigo') . '</TD>
				<TD class="tableheader">' . _('Rama') . '</TD>
				<TD class="tableheader">' . _('Contacto') . '</TD>
				<TD class="tableheader">' . _('Telefono') . '</TD>
				<TD class="tableheader">' . _('Fax') . '</TD>
				</TR>';
		echo $TableHeader;

		$j = 1;
		$k = 0; //row counter to determine background colour

		while ($myrow=DB_fetch_array($result_CustSelect)) {

			if ($k==1){
				echo '<tr bgcolor="#CCCCCC">';
				$k=0;
			} else {
				echo '<tr bgcolor="#EEEEEE">';
				$k=1;
			}

			printf("<td><FONT SIZE=1><INPUT TYPE=SUBMIT NAME='Select' VALUE='%s - %s'</FONT></td>
				<td><FONT SIZE=1>%s</FONT></td>
				<td><FONT SIZE=1>%s</FONT></td>
				<td><FONT SIZE=1>%s</FONT></td>
				<td><FONT SIZE=1>%s</FONT></td>
				</tr>",
				$myrow['debtorno'],
				$myrow['branchcode'],
				$myrow['brname'],
				$myrow['contactname'],
				$myrow['phoneno'],
				$myrow['faxno']);

			$j++;
			If ($j == 11){
				$j=1;
				echo $TableHeader;
			}
//end of page full new headings if
		}
//end of while loop

		echo '</TABLE></CENTER>';

	}//end if results to show
	
//end if RequireCustomerSelection
} else { //dont require customer selection
// everything below here only do if a customer is selected
 	if (isset($_POST['CancelOrder'])) {
		$OK_to_delete=1;	//assume this in the first instance

		if($_SESSION['ExistingOrder']!=0) { //need to check that not already dispatched

			$sql = "SELECT qtyinvoiced
				FROM salesorderdetails
				WHERE orderno=" . $_SESSION['ExistingOrder'] . "
				AND qtyinvoiced>0";

			$InvQties = DB_query($sql,$db);

			if (DB_num_rows($InvQties)>0){

				$OK_to_delete=0;

				prnMsg( _('There are lines on this order that have already been invoiced. Please delete only the lines on the order that are no longer required') . '<P>' . _('There is an option on confirming a dispatch/invoice to automatically cancel any balance on the order at the time of invoicing if you know the customer will not want the back order'),'warn');
			}
		}


			unset($_SESSION['Items']->LineItems);
			$_SESSION['Items']->ItemsOrdered=0;
			unset($_SESSION['Items']);
			$_SESSION['Items'] = new cart;

			if (in_array(2,$_SESSION['AllowedPageSecurityTokens'])){
				$_SESSION['RequireCustomerSelection'] = 1;
			} else {
				$_SESSION['RequireCustomerSelection'] = 0;
			}
			echo '<BR><BR>';
			prnMsg(_('Esta Venta Ha Sido Cancelada'),'success');
			echo "<CENTER><FORM ACTION='POSEntry.php'>";
			echo "<INPUT TYPE=submit value='Otra Venta'>";
			echo "</FORM></CENTER>";
			include('includes/footer.inc');
			exit;
			
	} else { /*Not cancelling the order */
		
		echo '<CENTER><FONT SIZE=3><B>';
		
		if ($_SESSION['Items']->Quotation==1){
			echo _('Quotation for') . ' ';
		} else {
			echo _('Venta') . ' ';
		}
			
		echo _('Cliente') . ' : ' . $_SESSION['Items']->CustomerName;
		//echo ' -  ' . _('Deliver To') . ' : ' . $_SESSION['Items']->DeliverTo;
		echo '<BR>' . _('Precios ') . ' ' . $_SESSION['Items']->SalesTypeName .' </B><FONT></CENTER>';
	}

	If (isset($_POST['Search'])){

		If ($_POST['Keywords'] AND $_POST['StockCode']) {
			$msg='<BR>' . _('Stock description keywords have been used in preference to the Stock code extract entered') . '.';
		}
		If (strlen($_POST['Keywords'])>0) {
			//insert wildcard characters in spaces
			$_POST['Keywords'] = strtoupper($_POST['Keywords']);

			$i=0;
			$SearchString = '%';
			while (strpos($_POST['Keywords'], ' ', $i)) {
				$wrdlen=strpos($_POST['Keywords'],' ',$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . '%';
				$i=strpos($_POST['Keywords'],' ',$i) +1;
			}
			$SearchString = $SearchString. substr($_POST['Keywords'],$i).'%';

			if ($_POST['StockCat']=='All'){
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, 
						stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.description " . LIKE . " '$SearchString'
					AND stockmaster.discontinued=0
					ORDER BY stockmaster.stockid";
			} else {
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE  stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.discontinued=0
					AND stockmaster.description " . LIKE . " '" . $SearchString . "'
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					ORDER BY stockmaster.stockid";
			}

		} elseif (strlen($_POST['StockCode'])>0){
		
			$_POST['StockCode'] = strtoupper($_POST['StockCode']);
			$SearchString = '%' . $_POST['StockCode'] . '%';

			if ($_POST['StockCat']=='All'){
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND (stockmaster.stockid " . LIKE . " '" . $SearchString . "'
						OR stockmaster.barcode " . LIKE . " '" . $SearchString ." ') 
					AND stockmaster.discontinued=0
					ORDER BY stockmaster.stockid";
			} else {
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND (stockmaster.stockid " . LIKE . " '" . $SearchString . "'
						OR stockmaster.barcode " . LIKE . " '" . $SearchString ." ')
					AND stockmaster.discontinued=0
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					ORDER BY stockmaster.stockid";
			}

		} else {
			if ($_POST['StockCat']=='All'){
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE  stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.discontinued=0
					ORDER BY stockmaster.stockid";
			} else {
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.discontinued=0
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					ORDER BY stockmaster.stockid";
			  }
		}

		$SQL = $SQL . ' LIMIT ' . $_SESSION['DisplayRecordsMax'];

		$ErrMsg = _('There is a problem selecting the part records to display because');
		$DbgMsg = _('The SQL used to get the part selection was');
		$SearchResult = DB_query($SQL,$db,$ErrMsg, $DbgMsg);

		if (DB_num_rows($SearchResult)==0 ){
			prnMsg (_('No se encontro el producto'),'info');

			if ($debug==1){
				//prnMsg(_('The SQL statement used was') . ':<BR>' . $SQL,'info');
			}
		}
		if (DB_num_rows($SearchResult)==1){
			$myrow=DB_fetch_array($SearchResult);
			$NewItem = $myrow['stockid'];
			DB_data_seek($SearchResult,0);
		}

	} //end of if search

#Always do the stuff below if not looking for a customerid

	echo '<FORM NAME="forma" ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';

	/*Process Quick Entry */

	 If (isset($_POST['QuickEntry'])){
	     /* get the item details from the database and hold them in the cart object */
	     
	     /*Discount can only be set later on  -- after quick entry -- so default discount to 0 in the first place */
	     $Discount = 0;			
	     
	     $i=1;
	 
	     //do {
			if(isset($_POST['codrap']) && $_POST['codrap']!=''){
	     		$NewItem = $_POST['codrap'];
	     		$NewItemQty = 1;
	     	}else {
	     		$QuickEntryCode = 'part_' . $i;
				$QuickEntryQty = 'qty_' . $i;
				$i++;
		   
				$NewItem = strtoupper($_POST['part_1']);
				$NewItemQty = $_POST[$QuickEntryQty];
	     	}
			if (strlen($NewItem)==0){
				unset($NewItem);
				//break;    /* break out of the loop if nothing in the quick entry fields*/
			}else{
	
			/*Now figure out if the item is a kit set - the field MBFlag='K'*/
			$sql = "SELECT stockmaster.mbflag
					FROM stockmaster
					WHERE stockmaster.stockid='". $NewItem ."'";
					
			
			$ErrMsg = _('Could not determine if the part being ordered was a kitset or not because');
			$KitResult = DB_query($sql, $db,$ErrMsg,$DbgMsg);
	
	
			if (DB_num_rows($KitResult)==0){
				prnMsg( _('The item code') . ' ' . $NewItem . ' ' . _('could not be retrieved from the database and has not been added to the order'),'warn');
			} elseif ($myrow=DB_fetch_array($KitResult)){
				if ($myrow['mbflag']=='K'){	/*It is a kit set item */
					$sql = "SELECT bom.component,
							bom.quantity
							FROM bom
							WHERE bom.parent='" . $NewItem . "'
							AND bom.effectiveto > '" . Date("Y-m-d") . "'
							AND bom.effectiveafter < '" . Date('Y-m-d') . "'";
		
					$ErrMsg =  _('Could not retrieve kitset components from the database because') . ' ';
					$KitResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);
		
					$ParentQty = $NewItemQty;
					while ($KitParts = DB_fetch_array($KitResult,$db)){
						$NewItem = $KitParts['component'];
						$NewItemQty = $KitParts['quantity'] * $ParentQty;
						include('includes/SelectOrderItems_IntoCart.inc');

					}
		
				} else { /*Its not a kit set item*/
					include('includes/SelectOrderItems_IntoCart.inc');
				}
			}
	     } //while ($i<=$_SESSION['QuickEntries']); /*loop to the next quick entry record */

	     unset($NewItem);
	 } /* end of if quick entry */
	 
	 
	 /*Now do non-quick entry delete/edits/adds */

	If ((isset($_SESSION['Items'])) OR isset($NewItem)){

		If(isset($_GET['Delete'])){ 
			//page called attempting to delete a line - GET['Delete'] = the line number to delete
			if($_SESSION['Items']->Some_Already_Delivered($_GET['Delete'])==0){
				$_SESSION['Items']->remove_from_cart($_GET['Delete'], 'Yes');  /*Do update DB */
			} else {
				prnMsg( _('This item cannot be deleted because some of it has already been invoiced'),'warn');
			}
		}

		foreach ($_SESSION['Items']->LineItems as $OrderLine) {

			if (isset($_POST['Quantity_' . $OrderLine->LineNumber])){
				$Quantity = $_POST['Quantity_' . $OrderLine->LineNumber];
				$Price = $_POST['Price_' . $OrderLine->LineNumber];
				$DiscountPercentage = $_POST['Discount_' . $OrderLine->LineNumber];
				$Narrative = $_POST['Narrative_' . $OrderLine->LineNumber];

				If ($Quantity<0 OR $Price <0 OR $DiscountPercentage >100 OR $DiscountPercentage <0){
					prnMsg(_('The item could not be updated because you are attempting to set the quantity ordered to less than 0 or the price less than 0 or the discount more than 100% or less than 0%'),'warn');

				} elseif($_SESSION['Items']->Some_Already_Delivered($OrderLine->LineNumber)!=0 AND $_SESSION['Items']->LineItems[$OrderLine->LineNumber]->Price != $Price) {

					prnMsg(_('The item you attempting to modify the price for has already had some quantity invoiced at the old price the items unit price cannot be modified retrospectively'),'warn');

				} elseif($_SESSION['Items']->Some_Already_Delivered($OrderLine->LineNumber)!=0 AND $_SESSION['Items']->LineItems[$OrderLine->LineNumber]->DiscountPercent != ($DiscountPercentage/100)) {

					prnMsg(_('The item you attempting to modify has had some quantity invoiced at the old discount percent the items discount cannot be modified retrospectively'),'warn');

				} elseif ($_SESSION['Items']->LineItems[$OrderLine->LineNumber]->QtyInv > $Quantity){
					prnMsg( _('You are attempting to make the quantity ordered a quantity less than has already been invoiced') . '. ' . _('The quantity delivered and invoiced cannot be modified retrospectively'),'warn');
				} elseif ($OrderLine->Quantity !=$Quantity OR $OrderLine->Price != $Price OR ABS($OrderLine->Disc -$DiscountPercentage/100) >0.001 OR $OrderLine->Narrative != $Narrative) {

					$_SESSION['Items']->update_cart_item($OrderLine->LineNumber,
										$Quantity,
										$Price,
										($DiscountPercentage/100),
										$Narrative,
										'Yes' /*Update DB */);
				}
			} //page not called from itself - POST variables not set	
		
		}	

	}
	
	if (isset($_POST['DeliveryDetails'])){
		
		// bowikaxu - calcular taxes
	/*$TaxTotal = 0;
	foreach ($_SESSION['Items']->LineItems as $OrderLine) {
			$TaxLineTotal = 0; //initialise tax total for the line
			$LineTotal = $OrderLine->Quantity * $OrderLine->Price * (1 - $OrderLine->DiscountPercent);
			foreach ($OrderLine->Taxes AS $Tax) {
				//echo '<input type=text name="' . $OrderLine->LineNumber . $Tax->TaxCalculationOrder . '_TaxRate" maxlength=4 SIZE=4 value="' . $Tax->TaxRate*100 . '">';
				if ($Tax->TaxOnTax ==1){
					$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * ($LineTotal + $TaxLineTotal));
					$TaxLineTotal += ($Tax->TaxRate * ($LineTotal + $TaxLineTotal));
				} else {
					$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * $LineTotal);
					$TaxLineTotal += ($Tax->TaxRate * $LineTotal);
				}
				$TaxGLCodes[$Tax->TaxAuthID] = $Tax->TaxGLCode;
			}
			$TaxTotal += $TaxLineTotal;
			
	}*/
	// fin calcular taxes
		
		// VERIFICAR QUE EL IMPORTE SEA IGUAL O MAYOR QUE EL PAGO
	
		if($_POST['importe']<($_SESSION['Items']->total)){ // el total es mayor que el importe
			echo "<script language='javascript'>alert('El IMPORTE es menor que el TOTAL !!!')</script>";
			unset($_POST['DeliveryDetails']);
		}else {
			$cambio = $_POST['importe']-($_SESSION['Items']->total);
			echo "<script language='javascript'>alert('CAMBIO:".$cambio." !!!')</script>";
		}
		// FIN VERIFICAR EL IMPORTE Y EL TOTAL DE LA VENTA
	}
	
	if (isset($_POST['DeliveryDetails'])){
	/*
	Sept  2006 RealHost
	bowikaxu - if an items order price is less than the material cost is not possible
	*/
	if(isset($_POST['PriceLess'])){
		echo "<INPUT TYPE=HIDDEN NAME='PriceLess' VALUE=1>";
	}elseif (!isset($_POST['PriceLess'])) {

		foreach($_SESSION['Items']->LineItems as $OrderLine){
			if($OrderLine->Price < $OrderLine->StandardCost){ // el precio es menor
				
					if($_SESSION['AccessLevel']==8 && $PriceLessThanOrder==1){
						
					echo "<H1><STRONG>"._('The price for the article ').$OrderLine->StockID._(' is less than the material cost');
					echo "<BR>"._('you have authorization to do it, Do you want to proceed?');
					echo "<BR>"._('if not, click no and change the price')."</STRONG></H1>";
					//echo '<A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '&PriceLess=' ."1". '" onclick="return confirm(\'' . _('Are You Sure?') . '\');">' . _('Set Price Less than Material Cost?') . '</A>';
					echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . $SID . "' METHOD=POST>";
					echo "<BR><CENTER><INPUT TYPE=SUBMIT NAME='DeliveryDetails' VALUE='" . _('YES') . "'>";
					echo "<INPUT TYPE=SUBMIT NAME='PriceLessNo' VALUE= '"._('NO')."'>";
					echo "<INPUT TYPE=HIDDEN NAME='PriceLess' VALUE=1>";
					echo "<INPUT TYPE=HIDDEN NAME='importe' VALUE='".$_POST['importe']."'>";
					echo "<INPUT TYPE=HIDDEN NAME='pago' VALUE='".$_POST['pago']."'>";
					echo "</CENTER></FORM>";		
					include('includes/footer.inc');					
					exit;
					
					}elseif($_SESSION['AccessLevel']==8 && $PriceLessThanOrder==0){
						
						echo "<H1><STRONG>"._('The price for the article ').$OrderLine->StockID._(' is less than the material cost');
						echo "<BR>"._('you have authorization to do it, but the option is not availabe');
						echo "<BR>"._('check the config.php to do it')."</STRONG></H1>";
						echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . $SID . "' METHOD=POST>";
						echo "<INPUT TYPE=SUBMIT NAME='PriceLessNo' VALUE= '"._('Go Back')."'>";
						echo "</CENTER></FORM>";		
						include('includes/footer.inc');
						exit;
						
					}else{
						
						echo "<H1><STRONG>"._('The price for the article ').$OrderLine->StockID._(' is less than the material cost');
						echo "<BR>"._('you dont have authorization to change it sorry');
						echo "<BR>"._('Hit Go Back button and replace the Price')."</STRONG></H1>";
						echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . $SID . "' METHOD=POST>";
						echo "<INPUT TYPE=SUBMIT NAME='PriceLessNo' VALUE= '"._('Go Back')."'>";
						echo "</CENTER></FORM>";		
						include('includes/footer.inc');
						exit;
						
					}
				}
			}
		
	}
		
	   	/* bowikaxu - Se proceso la venta, agregar la orden a la base de datos */
	   	
	   	/* finally write the order header to the database and then the order line details - a transaction would	be good here */

	   	// bowikaxu Marzo 2007 - comenzar las transacciones
	$SQL = "BEGIN";
	$Result = DB_query($SQL,$db,'Fallo el comiienzo de las transacciones','Fallo el QUERY: BEGIN',true);
	   	
	//$DelDate = FormatDateforSQL($_SESSION['Items']->DeliveryDate);
	$DelDate=date("Y/m/d");

	$HeaderSQL = "INSERT INTO salesorders (
				debtorno,
				branchcode,
				customerref,
				comments,
				orddate,
				ordertype,
				shipvia,
				deliverto,
				deladd1,
				deladd2,
				deladd3,
				deladd4,
				deladd5,
				deladd6,
				contactphone,
				contactemail,
				freightcost,
				fromstkloc,
				deliverydate,
				quotation,
                		deliverblind)
			VALUES (
				'" . $_SESSION['Items']->DebtorNo . "',
				'" . $_SESSION['Items']->Branch . "',
				'". DB_escape_string($_SESSION['Items']->CustRef) ."',
				'',
				'" . Date("Y-m-d H:i") . "',
				'" . $_SESSION['Items']->DefaultSalesType . "',
				" . $_SESSION['Items']->ShipVia .",
				'" . DB_escape_string($_SESSION['Items']->DeliverTo) . "',
				'" . DB_escape_string($_SESSION['Items']->DelAdd1) . "',
				'" . DB_escape_string($_SESSION['Items']->DelAdd2) . "',
				'" . DB_escape_string($_SESSION['Items']->DelAdd3) . "',
				'" . DB_escape_string($_SESSION['Items']->DelAdd4) . "',
				'" . DB_escape_string($_SESSION['Items']->DelAdd5) . "',
				'" . DB_escape_string($_SESSION['Items']->DelAdd6) . "',
				'" . DB_escape_string($_SESSION['Items']->PhoneNo) . "',
				'" . DB_escape_string($_SESSION['Items']->Email) . "',
				" . $_SESSION['Items']->FreightCost .",
				'" . $_SESSION['Items']->Location ."',
				'" . $DelDate . "',
				" . 0 . ",
				" . $_SESSION['Items']->DeliverBlind ."
                )";

	$ErrMsg = _('The order cannot be added because');
	$InsertQryResult = DB_query($HeaderSQL,$db,$ErrMsg,'Fallo la creacion de la orden',true);

	$OrderNo = DB_Last_Insert_ID($db,'salesorders','orderno');
	$StartOf_LineItemsSQL = "INSERT INTO salesorderdetails (
						orderlineno,
						orderno,
						stkcode,
						unitprice,
						quantity,
						discountpercent,
						narrative)
					VALUES (";

	foreach ($_SESSION['Items']->LineItems as $StockItem) {

		$LineItemsSQL = $StartOf_LineItemsSQL .
					$StockItem->LineNumber . ",
					" . $OrderNo . ",
					'" . $StockItem->StockID . "',
					". $StockItem->Price . ",
					" . $StockItem->Quantity . ",
					" . floatval($StockItem->DiscountPercent) . ",
					'" . DB_escape_string($StockItem->Narrative) . "'
				)";
		$Ins_LineItemResult = DB_query($LineItemsSQL,$db,'Error al insertar detalles de la orden','Fallo el INSERT a salesorderdetails',true);
	} /* inserted line items into sales order details */

	// bowikaxu - april 2007 - insert user who created the order
	$date = FormatDateforSQL(date("d-m-Y"));
	$sql = "INSERT INTO rh_usertrans(type, user_, order_, date_) VALUES (30, '"
			.$_SESSION['UserID']."', ".$OrderNo.", '".$date."')";
	$res = DB_query($sql,$db,'Imposible insertar el usuario','');
		
	if ($_SESSION['Items']->Quotation==1){
		prnMsg(_('Quotation Number') . ' ' . $OrderNo . ' ' . _('has been entered'),'success');
	} else {
		prnMsg(_('Order Number') . ' ' . $OrderNo . ' ' . _('has been entered'),'success');
	}
	
	if (count($_SESSION['AllowedPageSecurityTokens'])>1){
		/* Only allow print of packing slip for internal staff - customer logon's cannot go here */
		
		if ($_POST['Quotation']==0) { /*then its not a quotation its a real order */
		
			
		} else {
			
		}
		
		echo "</FORM>";
		
	} else {
		/*its a customer logon so thank them */
		prnMsg(_('Thank you for your business'),'success');
	}

	//unset($_SESSION['Items']->LineItems);
	//unset($_SESSION['Items']);
	//echo '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=' . $rootpath . '/ConfirmDispatch_Invoice2.php?' . SID ."&OrderNumber=$OrderNo".'">';
	//include('includes/footer.inc');
	//exit;
	   	
	   	/* bowikaxu - Se termino de agregar la orden a la base de datos */
	}
	$_SESSION['ProcessingOrder'] = $OrderNo;

	/* bowikaxu - hacer los inserts de la remision */
	
	if (isset($_POST['DeliveryDetails']) && $_POST['DeliveryDetails'] != ""){

/* SQL to process the postings for sales invoices...

/*First check there are lines on the dipatch with quantities to invoice
invoices can have a zero amount but there must be a quantity to invoice */

	$QuantityInvoicedIsPositive = false;

	foreach ($_SESSION['Items']->LineItems as $OrderLine) {
		if ($OrderLine->QtyDispatched > 0){
			$QuantityInvoicedIsPositive =true;
		}
	}
	if (! $QuantityInvoicedIsPositive){
		prnMsg( _('There are no lines on this order with a quantity to invoice') . '. ' . _('No further processing has been done'),'error');
		include('includes/footer.inc');
		exit;
	}
/* Now Get the area where the sale is to from the branches table */

	$SQL = "SELECT area,
			defaultshipvia
		FROM custbranch
		WHERE custbranch.debtorno ='". $_SESSION['Items']->DebtorNo . "'
		AND custbranch.branchcode = '" . $_SESSION['Items']->Branch . "'";

	$ErrMsg = _('We were unable to load Area where the Sale is to from the BRANCHES table') . '. ' . _('Please remedy this');
	$Result = DB_query($SQL,$db, $ErrMsg);
	$myrow = DB_fetch_row($Result);
	$Area = $myrow[0];
	$DefaultShipVia = $myrow[1];
	DB_free_result($Result);

/*company record read in on login with info on GL Links and debtors GL account*/

	if ($_SESSION['CompanyRecord']==0){
		/*The company data and preferences could not be retrieved for some reason */
		prnMsg( _('The company infomation and preferences could not be retrieved') . ' - ' . _('see your system administrator'), 'error');
		include('includes/footer.inc');
		exit;
	}

/*Now need to check that the order details are the same as they were when they were read into the Items array. If they've changed then someone else may have invoiced them */

	$SQL = "SELECT stkcode,
			quantity,
			qtyinvoiced,
			orderlineno
		FROM salesorderdetails
		WHERE completed=0
		AND orderno = " . $_SESSION['ProcessingOrder'];

	$Result = DB_query($SQL,$db);

	if (DB_num_rows($Result) != count($_SESSION['Items']->LineItems)){

	/*there should be the same number of items returned from this query as there are lines on the invoice - if  not 	then someone has already invoiced or credited some lines */

		if ($debug==1){
			echo '<BR>'.$SQL;
			echo '<BR>' . _('Number of rows returned by SQL') . ':' . DB_num_rows($Result);
			echo '<BR>' . _('Count of items in the session') . ' ' . count($_SESSION['Items']->LineItems);
		}

		echo '<BR>';
		prnMsg( _('This order has been changed or invoiced since this delivery was started to be confirmed') . '. ' . _('Processing halted') . '. ' . _('To enter and confirm this dispatch') . '/' . _('invoice the order must be re-selected and re-read again to update the changes made by the other user'), 'error');

		unset($_SESSION['Items']->LineItems);
		unset($_SESSION['Items']);
		unset($_SESSION['ProcessingOrder']);
		include('includes/footer.inc'); exit;
	}

	$Changes =0;

	while ($myrow = DB_fetch_array($Result)) {

		if ($_SESSION['Items']->LineItems[$myrow['orderlineno']]->Quantity != $myrow['quantity'] OR $_SESSION['Items']->LineItems[$myrow['orderlineno']]->QtyInv != $myrow['qtyinvoiced']) {

			echo '<BR>'. _('Orig order for'). ' ' . $myrow['orderlineno'] . ' '. _('has a quantity of'). ' ' .
				$myrow['quantity'] . ' '. _('and an invoiced qty of'). ' ' . $myrow['qtyinvoiced'] . ' '.
				_('the session shows quantity of'). ' ' . $_SESSION['Items']->LineItems[$myrow['orderlineno']]->Quantity .
				' ' . _('and quantity invoice of'). ' ' . $_SESSION['Items']->LineItems[$myrow['orderlineno']]->QtyInv;

	                prnMsg( _('This order has been changed or invoiced since this delivery was started to be confirmed') . ' ' . _('Processing halted.') . ' ' . _('To enter and confirm this dispatch, it must be re-selected and re-read again to update the changes made by the other user'), 'error');
        	        echo '<BR>';

                	echo '<CENTER><A HREF="'. $rootpath . '/SelectSalesOrder.php?' . SID . '">'. _('Select a sales order for confirming deliveries and invoicing'). '</A></CENTER>';

	                unset($_SESSION['Items']->LineItems);
        	        unset($_SESSION['Items']);
                	unset($_SESSION['ProcessingOrder']);
	                include('includes/footer.inc');
			exit;
		}
	} /*loop through all line items of the order to ensure none have been invoiced since started looking at this order*/

	DB_free_result($Result);

/*Now Get the next invoice number - function in SQL_CommonFunctions*/
	$DefaultDispatchDate = date($_SESSION['DefaultDateFormat']);
	$InvoiceNo = GetNextTransNo(20000, $db);
	$PeriodNo = GetPeriod($DefaultDispatchDate, $db);

/*Start an SQL transaction */

	if ($DefaultShipVia != $_SESSION['Items']->ShipVia){
		$SQL = "UPDATE custbranch SET defaultshipvia ='" . $_SESSION['Items']->ShipVia . "' WHERE debtorno='" . $_SESSION['Items']->DebtorNo . "' AND branchcode='" . $_SESSION['Items']->Branch . "'";
		$ErrMsg = _('Could not update the default shipping carrier for this branch because');
		$DbgMsg = _('The SQL used to update the branch default carrier was');
		$result = DB_query($SQL,$db, $ErrMsg, $DbgMsg, true);
	}

	//$DefaultDispatchDate = FormatDateForSQL($DefaultDispatchDate);

/*Update order header for invoice charged on */
	$SQL = "UPDATE salesorders SET comments = CONCAT(comments,' Rem ','" . $InvoiceNo . "') WHERE orderno= " . $_SESSION['ProcessingOrder'];

	$ErrMsg = _('CRITICAL ERROR') . ' ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales order header could not be updated with the invoice number');
	$DbgMsg = _('The following SQL to update the sales order was used');
	$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

	// bowikaxu - calcular taxes
	/*$TaxTotal = 0;
	foreach ($_SESSION['Items']->LineItems as $OrderLine) {
			$TaxLineTotal = 0; //initialise tax total for the line
			foreach ($OrderLine->Taxes AS $Tax) {
				//echo '<input type=text name="' . $OrderLine->LineNumber . $Tax->TaxCalculationOrder . '_TaxRate" maxlength=4 SIZE=4 value="' . $Tax->TaxRate*100 . '">';
				if ($Tax->TaxOnTax ==1){
					$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * ($LineTotal + $TaxLineTotal));
					$TaxLineTotal += ($Tax->TaxRate * ($LineTotal + $TaxLineTotal));
				} else {
					$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * $LineTotal);
					$TaxLineTotal += ($Tax->TaxRate * $LineTotal);
				}
				$TaxGLCodes[$Tax->TaxAuthID] = $Tax->TaxGLCode;
			}
			$TaxTotal += $TaxLineTotal;
			
	}*/
	// fin calcular taxes
	
	/*Now insert the DebtorTrans */
	$_SESSION['CurrencyRate']=1;
	$TaxTotal = 0;
	$SQL = "INSERT INTO debtortrans (
			transno,
			type,
			debtorno,
			branchcode,
			trandate,
			prd,
			reference,
			tpe,
			order_,
			ovamount,
			alloc,
			ovgst,
			ovfreight,
			rate,
			invtext,
			shipvia,
			consignment,
			rh_createdate
			)
		VALUES (
			". $InvoiceNo . ",
			20000,
			'" . $_SESSION['Items']->DebtorNo . "',
			'" . $_SESSION['Items']->Branch . "',
			NOW(),
			" . $PeriodNo . ",
			'',
			'" . $_SESSION['Items']->DefaultSalesType . "',
			" . $_SESSION['ProcessingOrder'] . ",
			" . $_SESSION['Items']->total . ",
			" . $_SESSION['Items']->total . ",
			" . $TaxTotal . ",
			 0,
			 ".$_SESSION['CurrencyRate'].",
			'Venta de Mostrador',
			" . $_SESSION['Items']->ShipVia . ",
			'"  . $_POST['Consignment'] . "',
			NOW()
		)";
// en rate 1 hiba $_SESSION['CurrencyRate']
	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction record could not be inserted because');
	$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
 	$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
	
	$DebtorTransID = DB_Last_Insert_ID($db,'debtortrans','id');
	 			
/* Insert the tax totals for each tax authority where tax was charged on the invoice */
	/*foreach ($TaxTotals AS $TaxAuthID => $TaxAmount) {
	
		$SQL = 'INSERT INTO debtortranstaxes (debtortransid,
							taxauthid,
							taxamount)
				VALUES (' . $DebtorTransID . ',
					' . $TaxAuthID . ',
					' . $TaxAmount/$_SESSION['CurrencyRate'] . ')';
		
		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction taxes records could not be inserted because');
		$DbgMsg = _('The following SQL to insert the debtor transaction taxes record was used');
 		//$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
	}*/	
	

/* If balance of the order cancelled update sales order details quantity. Also insert log records for OrderDeliveryDifferencesLog */

	foreach ($_SESSION['Items']->LineItems as $OrderLine) {

		if ($_POST['BOPolicy']=='CAN'){

			$SQL = "UPDATE salesorderdetails
				SET quantity = quantity - " . ($OrderLine->Quantity - $OrderLine->QtyDispatched) . " WHERE orderno = " . $_SESSION['ProcessingOrder'] . " AND stkcode = '" . $OrderLine->StockID . "'";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales order detail record could not be updated because');
			$DbgMsg = _('The following SQL to update the sales order detail record was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);


			if (($OrderLine->Quantity - $OrderLine->QtyDispatched)>0){

				$SQL = "INSERT INTO orderdeliverydifferenceslog (
						orderno,
						invoiceno,
						stockid,
						quantitydiff,
						debtorno,
						branch,
						can_or_bo
						)
					VALUES (
						" . $_SESSION['ProcessingOrder'] . ",
						" . $InvoiceNo . ",
						'" . $OrderLine->StockID . "',
						" . ($OrderLine->Quantity - $OrderLine->QtyDispatched) . ",
						'" . $_SESSION['Items']->DebtorNo . "',
						'" . $_SESSION['Items']->Branch . "',
						'CAN'
						)";

				$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The order delivery differences log record could not be inserted because');
				$DbgMsg = _('The following SQL to insert the order delivery differences record was used');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			}



		} elseif (($OrderLine->Quantity - $OrderLine->QtyDispatched) >0 && DateDiff(ConvertSQLDate($DefaultDispatchDate),$_SESSION['Items']->DeliveryDate,'d') >0) {

		/*The order is being short delivered after the due date - need to insert a delivery differnce log */

			$SQL = "INSERT INTO orderdeliverydifferenceslog (
					orderno,
					invoiceno,
					stockid,
					quantitydiff,
					debtorno,
					branch,
					can_or_bo
				)
				VALUES (
					" . $_SESSION['ProcessingOrder'] . ",
					" . $InvoiceNo . ",
					'" . $OrderLine->StockID . "',
					" . ($OrderLine->Quantity - $OrderLine->QtyDispatched) . ",
					'" . $_SESSION['Items']->DebtorNo . "',
					'" . $_SESSION['Items']->Branch . "',
					'BO'
				)";

			$ErrMsg =  '<BR>' . _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The order delivery differences log record could not be inserted because');
			$DbgMsg = _('The following SQL to insert the order delivery differences record was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
		} /*end of order delivery differences log entries */

/*Now update SalesOrderDetails for the quantity invoiced and the actual dispatch dates. */
		$OrderLine->QtyDispatched = $OrderLine->Quantity;
		if ($OrderLine->QtyDispatched !=0 AND $OrderLine->QtyDispatched!="" AND $OrderLine->QtyDispatched) {

			// Test above to see if the line is completed or not
			if ($OrderLine->QtyDispatched>=($OrderLine->Quantity - $OrderLine->QtyInv) OR $_POST['BOPolicy']=="CAN"){
				
				// SET qtyinvoiced = qtyinvoiced + " . $OrderLine->QtyDispatched . ",
				$SQL = "UPDATE salesorderdetails
					SET qtyinvoiced = qtyinvoiced + " . $OrderLine->QtyDispatched . ",
					actualdispatchdate = '" . $DefaultDispatchDate .  "',
					completed=1
					WHERE orderno = " . $_SESSION['ProcessingOrder'] . "
					AND orderlineno = '" . $OrderLine->LineNumber . "'";
			} else {
				$SQL = "UPDATE salesorderdetails
					SET qtyinvoiced = qtyinvoiced + " . $OrderLine->QtyDispatched . ",
					actualdispatchdate = '" . $DefaultDispatchDate .  "'
					WHERE orderno = " . $_SESSION['ProcessingOrder'] . "
					AND orderlineno = '" . $OrderLine->LineNumber . "'";

			}

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales order detail record could not be updated because');
			$DbgMsg = _('The following SQL to update the sales order detail record was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

			 /* Update location stock records if not a dummy stock item
			 need the MBFlag later too so save it to $MBFlag */
			$Result = DB_query("SELECT mbflag FROM stockmaster WHERE stockid = '" . $OrderLine->StockID . "'",$db,"<BR>Can't retrieve the mbflag",'Fallo la transaccion',true);

			$myrow = DB_fetch_row($Result);
			$MBFlag = $myrow[0];

			if ($MBFlag=="B" OR $MBFlag=="M") {
				$Assembly = False;

				/* Need to get the current location quantity
				will need it later for the stock movement */
               			$SQL="SELECT locstock.quantity
					FROM locstock
					WHERE locstock.stockid='" . $OrderLine->StockID . "'
					AND loccode= '" . $_SESSION['Items']->Location . "'";
				$ErrMsg = _('WARNING') . ': ' . _('Could not retrieve current location stock');
               			$Result = DB_query($SQL, $db, $ErrMsg);

				if (DB_num_rows($Result)==1){
                       			$LocQtyRow = DB_fetch_row($Result);
                       			$QtyOnHandPrior = $LocQtyRow[0];
				} else {
					/* There must be some error this should never happen */
					$QtyOnHandPrior = 0;
				}

				$SQL = "UPDATE locstock
					SET quantity = locstock.quantity - " . $OrderLine->QtyDispatched . "
					WHERE locstock.stockid = '" . $OrderLine->StockID . "'
					AND loccode = '" . $_SESSION['Items']->Location . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated because');
				$DbgMsg = _('The following SQL to update the location stock record was used');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

			} else if ($MBFlag=='A'){ /* its an assembly */
				/*Need to get the BOM for this part and make
				stock moves for the components then update the Location stock balances */
				$Assembly=True;
				$StandardCost =0; /*To start with - accumulate the cost of the comoponents for use in journals later on */
				$SQL = "SELECT bom.component,
						bom.quantity,
						stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost AS standard
					FROM bom,
						stockmaster
					WHERE bom.component=stockmaster.stockid
					AND bom.parent='" . $OrderLine->StockID . "'
					AND bom.effectiveto > '" . Date("Y-m-d") . "'
					AND bom.effectiveafter < '" . Date("Y-m-d") . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not retrieve assembly components from the database for'). ' '. $OrderLine->StockID . _('because').' ';
				$DbgMsg = _('The SQL that failed was');
				$AssResult = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

				while ($AssParts = DB_fetch_array($AssResult,$db)){
				
					$StandardCost += ($AssParts['standard'] * $AssParts['quantity']) ;
					/* Need to get the current location quantity
					will need it later for the stock movement */
	                  		$SQL="SELECT locstock.quantity
						FROM locstock
						WHERE locstock.stockid='" . $AssParts['component'] . "'
						AND loccode= '" . $_SESSION['Items']->Location . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Can not retrieve assembly components location stock quantities because ');
					$DbgMsg = _('The SQL that failed was');
					$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
	                  		if (DB_num_rows($Result)==1){
	                  			$LocQtyRow = DB_fetch_row($Result);
	                  			$QtyOnHandPrior = $LocQtyRow[0];
					} else {
						/*There must be some error this should never happen */
						$QtyOnHandPrior = 0;
	                  		}

					$SQL = "INSERT INTO stockmoves (
							stockid,
							type,
							transno,
							loccode,
							trandate,
							debtorno,
							branchcode,
							prd,
							reference,
							qty,
							standardcost,
							show_on_inv_crds,
							newqoh
						) VALUES (
							'" . $AssParts['component'] . "',
							 20000,
							 " . $InvoiceNo . ",
							 '" . $_SESSION['Items']->Location . "',
							 '" . $DefaultDispatchDate . "',
							 '" . $_SESSION['Items']->DebtorNo . "',
							 '" . $_SESSION['Items']->Branch . "',
							 " . $PeriodNo . ",
							 '" . _('Assembly') . ': ' . $OrderLine->StockID . ' ' . _('Order') . ': ' . $_SESSION['ProcessingOrder'] . "',
							 " . -$AssParts['quantity'] * $OrderLine->QtyDispatched . ",
							 " . $AssParts['standard'] . ",
							 0,
							 " . ($QtyOnHandPrior -($AssParts['quantity'] * $OrderLine->QtyDispatched)) . "
						)";
					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records for the assembly components of'). ' '. $OrderLine->StockID . ' ' . _('could not be inserted because');
					$DbgMsg = _('The following SQL to insert the assembly components stock movement records was used');
					$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);


					$SQL = "UPDATE locstock
						SET quantity = locstock.quantity - " . $AssParts['quantity'] * $OrderLine->QtyDispatched . "
						WHERE locstock.stockid = '" . $AssParts['component'] . "'
						AND loccode = '" . $_SESSION['Items']->Location . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated for an assembly component because');
					$DbgMsg = _('The following SQL to update the locations stock record for the component was used');
					$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
				} /* end of assembly explosion and updates */

				/*Update the cart with the recalculated standard cost from the explosion of the assembly's components*/
				$_SESSION['Items']->LineItems[$OrderLine->LineNumber]->StandardCost = $StandardCost;
				$OrderLine->StandardCost = $StandardCost;
			} /* end of its an assembly */

			// Insert stock movements - with unit cost
			//$LocalCurrencyPrice= ($OrderLine->Price / $_SESSION['CurrencyRate']);
			$LocalCurrencyPrice= ($OrderLine->Price / $_SESSION['CurrencyRate']);

			if ($MBFlag=='B' OR $MBFlag=='M'){
            			$SQL = "INSERT INTO stockmoves (
						stockid,
						type,
						transno,
						loccode,
						trandate,
						debtorno,
						branchcode,
						price,
						prd,
						reference,
						qty,
						discountpercent,
						standardcost,
						newqoh,
						narrative )
					VALUES ('" . $OrderLine->StockID . "',
						20000,
						" . $InvoiceNo . ",
						'" . $_SESSION['Items']->Location . "',
						'" . $DefaultDispatchDate . "',
						'" . $_SESSION['Items']->DebtorNo . "',
						'" . $_SESSION['Items']->Branch . "',
						" . $LocalCurrencyPrice . ",
						" . $PeriodNo . ",
						'" . $_SESSION['ProcessingOrder'] . "',
						" . -$OrderLine->QtyDispatched . ",
						" . $OrderLine->DiscountPercent . ",
						" . $OrderLine->StandardCost . ",
						" . ($QtyOnHandPrior - $OrderLine->QtyDispatched) . ",
						'" . DB_escape_string($OrderLine->Narrative) . "' )";
			} else {
            // its an assembly or dummy and assemblies/dummies always have nil stock (by definition they are made up at the time of dispatch  so new qty on hand will be nil
				$SQL = "INSERT INTO stockmoves (
						stockid,
						type,
						transno,
						loccode,
						trandate,
						debtorno,
						branchcode,
						price,
						prd,
						reference,
						qty,
						discountpercent,
						standardcost,
						narrative )
					VALUES ('" . $OrderLine->StockID . "',
						20000,
						" . $InvoiceNo . ",
						'" . $_SESSION['Items']->Location . "',
						'" . $DefaultDispatchDate . "',
						'" . $_SESSION['Items']->DebtorNo . "',
						'" . $_SESSION['Items']->Branch . "',
						" . $LocalCurrencyPrice . ",
						" . $PeriodNo . ",
						'" . $_SESSION['ProcessingOrder'] . "',
						" . -$OrderLine->QtyDispatched . ",
						" . $OrderLine->DiscountPercent . ",
						" . $OrderLine->StandardCost . ",
						'" . addslashes($OrderLine->Narrative) . "')";
			}


			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records could not be inserted because');
			$DbgMsg = _('The following SQL to insert the stock movement records was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

			// bowikaxu realhost jan 2008 - insert the stockmoves reference into rh_remdetails
			$sql = "SELECT stkmoveno AS id FROM stockmoves ORDER BY id DESC LIMIT 1";
			$stockid_res = DB_query($sql,$db); 
			$stkid = DB_fetch_array($stockid_res);
			// andres amaya - insert a rh_remdetails
				
				$SQL = "INSERT INTO rh_remdetails (
							stockid,
							transno,
							loccode,
							trandate,
							debtorno,
							branchcode,
							qty,
							price,
							standardcost,
							reference
						) VALUES (
							'" . $OrderLine->StockID . "',
							 " . $InvoiceNo . ",
							 '" . $_SESSION['Items']->Location . "',
							 '" . $DefaultDispatchDate . "',
							 '" . $_SESSION['Items']->DebtorNo . "',
							 '" . $_SESSION['Items']->Branch . "',
							 " . $OrderLine->QtyDispatched . ",
							 ". $OrderLine->Price.",
							 ".$OrderLine->StandardCost.",
							 ".$stkid['id'].")";
					//" . -$AssParts['quantity'] * $OrderLine->QtyDispatched . ",
					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records for the assembly components of'). ' '. $OrderLine->StockID . ' ' . _('could not be inserted because');
					$DbgMsg = _('The following SQL to insert the assembly components rem details records was used');
					$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
						
/*Get the ID of the StockMove... */
			$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');
			
/*Insert the taxes that applied to this line */
			/*
			foreach ($OrderLine->Taxes as $Tax) {
			
				$SQL = 'INSERT INTO stockmovestaxes (stkmoveno,
									taxauthid,
									taxrate,
									taxcalculationorder,
									taxontax)
						VALUES (' . $StkMoveNo . ',
							' . $Tax->TaxAuthID . ',
							' . $Tax->TaxRate . ',
							' . $Tax->TaxCalculationOrder . ',
							' . $Tax->TaxOnTax . ')';
							
				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Taxes and rates applicable to this invoice line item could not be inserted because');
				$DbgMsg = _('The following SQL to insert the stock movement tax detail records was used');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			}
			*/
/*Insert the StockSerialMovements and update the StockSerialItems  for controlled items*/

			if ($OrderLine->Controlled ==1){
				foreach($OrderLine->SerialItems as $Item){
                                /*We need to add the StockSerialItem record and
				The StockSerialMoves as well */

					$SQL = "UPDATE stockserialitems
							SET quantity= quantity - " . $Item->BundleQty . "
							WHERE stockid='" . $OrderLine->StockID . "'
							AND loccode='" . $_SESSION['Items']->Location . "'
							AND serialno='" . $Item->BundleRef . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be updated because');
					$DbgMsg = _('The following SQL to update the serial stock item record was used');
					$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

					/* now insert the serial stock movement */

					$SQL = "INSERT INTO stockserialmoves (stockmoveno, 
										stockid, 
										serialno, 
										moveqty) 
						VALUES (" . $StkMoveNo . ", 
							'" . $OrderLine->StockID . "', 
							'" . $Item->BundleRef . "', 
							" . -$Item->BundleQty . ")";
							
					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
					$DbgMsg = _('The following SQL to insert the serial stock movement records was used');
					$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
				}/* foreach controlled item in the serialitems array */
			} /*end if the orderline is a controlled item */

/*Insert Sales Analysis records */

			$SQL="SELECT COUNT(*),
					salesanalysis.stockid,
					salesanalysis.stkcategory,
					salesanalysis.cust,
					salesanalysis.custbranch,
					salesanalysis.area,
					salesanalysis.periodno,
					salesanalysis.typeabbrev,
					salesanalysis.salesperson
				FROM salesanalysis,
					custbranch,
					stockmaster
				WHERE salesanalysis.stkcategory=stockmaster.categoryid
				AND salesanalysis.stockid=stockmaster.stockid
				AND salesanalysis.cust=custbranch.debtorno
				AND salesanalysis.custbranch=custbranch.branchcode
				AND salesanalysis.area=custbranch.area
				AND salesanalysis.salesperson=custbranch.salesman 
				AND salesanalysis.typeabbrev ='" . $_SESSION['Items']->DefaultSalesType . "' 
				AND salesanalysis.periodno=" . $PeriodNo . " 
				AND salesanalysis.cust " . LIKE . " '" . $_SESSION['Items']->DebtorNo . "' 
				AND salesanalysis.custbranch " . LIKE . " '" . $_SESSION['Items']->Branch . "' 
				AND salesanalysis.stockid " . LIKE . " '" . $OrderLine->StockID . "' 
				AND salesanalysis.budgetoractual=1 
				GROUP BY salesanalysis.stockid,
					salesanalysis.stkcategory,
					salesanalysis.cust,
					salesanalysis.custbranch,
					salesanalysis.area,
					salesanalysis.periodno,
					salesanalysis.typeabbrev,
					salesanalysis.salesperson";

			$ErrMsg = _('The count of existing Sales analysis records could not run because');
			$DbgMsg = '<BR>'. _('SQL to count the no of sales analysis records');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

			$myrow = DB_fetch_row($Result);

			if ($myrow[0]>0){  /*Update the existing record that already exists */
				
				$SQL = "UPDATE salesanalysis 
					SET amt=amt+" . ($OrderLine->Price * $OrderLine->QtyDispatched / $_SESSION['CurrencyRate']) . ", 
					cost=cost+" . ($OrderLine->StandardCost * $OrderLine->QtyDispatched) . ",
					qty=qty +" . $OrderLine->QtyDispatched . ",
					disc=disc+" . ($OrderLine->DiscountPercent * $OrderLine->Price * $OrderLine->QtyDispatched / $_SESSION['CurrencyRate']) . " 
					WHERE salesanalysis.area='" . $myrow[5] . "' 
					AND salesanalysis.salesperson='" . $myrow[8] . "'
					AND typeabbrev ='" . $_SESSION['Items']->DefaultSalesType . "' 
					AND periodno = " . $PeriodNo . "
					AND cust " . LIKE . " '" . $_SESSION['Items']->DebtorNo . "' 
					AND custbranch " . LIKE . " '" . $_SESSION['Items']->Branch . "' 
					AND stockid " . LIKE . " '" . $OrderLine->StockID . "' 
					AND salesanalysis.stkcategory ='" . $myrow[2] . "' 
					AND budgetoractual=1";

			} else { /* insert a new sales analysis record */

				$SQL = "INSERT INTO salesanalysis (
						typeabbrev, 
						periodno, 
						amt, 
						cost, 
						cust, 
						custbranch, 
						qty, 
						disc, 
						stockid, 
						area, 
						budgetoractual, 
						salesperson, 
						stkcategory
						) 
					SELECT '" . $_SESSION['Items']->DefaultSalesType . "', 
						" . $PeriodNo . ", 
						" . ($OrderLine->Price * $OrderLine->QtyDispatched / $_SESSION['CurrencyRate']) . ", 
						" . ($OrderLine->StandardCost * $OrderLine->QtyDispatched) . ",
						'" . $_SESSION['Items']->DebtorNo . "', 
						'" . $_SESSION['Items']->Branch . "',
						" . $OrderLine->QtyDispatched . ", 
						" . ($OrderLine->DiscountPercent * $OrderLine->Price * $OrderLine->QtyDispatched / $_SESSION['CurrencyRate']) . ", 
						'" . $OrderLine->StockID . "', 
						custbranch.area, 
						1, 
						custbranch.salesman, 
						stockmaster.categoryid 
					FROM stockmaster, 
						custbranch
					WHERE stockmaster.stockid = '" . $OrderLine->StockID . "' 
					AND custbranch.debtorno = '" . $_SESSION['Items']->DebtorNo . "' 
					AND custbranch.branchcode='" . $_SESSION['Items']->Branch . "'";
			}

			$ErrMsg = _('Sales analysis record could not be added or updated because');
			$DbgMsg = _('The following SQL to insert the sales analysis record was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

/* If GLLink_Stock then insert GLTrans to credit stock and debit cost of sales at standard cost*/

			if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND $OrderLine->StandardCost !=0){

/*first the cost of sales entry*/

				$SQL = "INSERT INTO gltrans (
							type, 
							typeno, 
							trandate, 
							periodno, 
							account, 
							narrative, 
							amount
							) 
					VALUES (
						20000, 
						" . $InvoiceNo . ", 
						'" . $DefaultDispatchDate . "', 
						" . $PeriodNo . ", 
						" . GetCOGSGLAccount($Area, $OrderLine->StockID, $_SESSION['Items']->DefaultSalesType, $db) . ", 
						'" . $_SESSION['Items']->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->QtyDispatched . " @ " . $OrderLine->StandardCost . "',
						" . $OrderLine->StandardCost * $OrderLine->QtyDispatched . "
					)";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The cost of sales GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

/*now the stock entry*/
				$StockGLCode = GetStockGLCode($OrderLine->StockID,$db);

				$SQL = "INSERT INTO gltrans (
							type, 
							typeno, 
							trandate, 
							periodno,
							account, 
							narrative,
							amount) 
					VALUES (
						20000, 
						" . $InvoiceNo . ", 
						'" . $DefaultDispatchDate . "', 
						" . $PeriodNo . ", 
						" . $StockGLCode['stockact'] . ", 
						'" . $_SESSION['Items']->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->QtyDispatched . " @ " . $OrderLine->StandardCost . "', 
						" . (-$OrderLine->StandardCost * $OrderLine->QtyDispatched) . "
					)";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock side of the cost of sales GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			} /* end of if GL and stock integrated and standard cost !=0 */

			if ($_SESSION['CompanyRecord']['gllink_debtors']==1 AND $OrderLine->Price !=0){

	//Post sales transaction to GL credit sales
				$SalesGLAccounts = GetSalesGLAccount($Area, $OrderLine->StockID, $_SESSION['Items']->DefaultSalesType, $db);

				$SQL = "INSERT INTO gltrans (
							type, 
							typeno,
							trandate, 
							periodno,
							account, 
							narrative, 
							amount
						) 
					VALUES (
						20000, 
						" . $InvoiceNo . ", 
						'" . $DefaultDispatchDate . "', 
						" . $PeriodNo . ", 
						" . $SalesGLAccounts['salesglcode'] . ",
						'" . $_SESSION['Items']->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->QtyDispatched . " @ " . $OrderLine->Price . "', 
						" . (-$OrderLine->Price * $OrderLine->QtyDispatched/$_SESSION['CurrencyRate']) . "
					)";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales GL posting could not be inserted because');
				$DbgMsg = '<BR>' ._('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

				if ($OrderLine->DiscountPercent !=0){

					$SQL = "INSERT INTO gltrans (
							type, 
							typeno, 
							trandate, 
							periodno, 
							account, 
							narrative, 
							amount
						) 
						VALUES (
							20000, 
							" . $InvoiceNo . ", 
							'" . $DefaultDispatchDate . "', 
							" . $PeriodNo . ", 
							" . $SalesGLAccounts['discountglcode'] . ", 
							'" . $_SESSION['Items']->DebtorNo . " - " . $OrderLine->StockID . " @ " . ($OrderLine->DiscountPercent * 100) . "%', 
							" . ($OrderLine->Price * $OrderLine->QtyDispatched * $OrderLine->DiscountPercent/$_SESSION['CurrencyRate']) . "
						)";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales discount GL posting could not be inserted because');
					$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
				} /*end of if discount !=0 */
				
				/*// bowikaxu - insert a gltrans de la cuenta del punto de venta
				// obtener la cuenta
				$sql = "SELECT posaccount FROM locations WHERE loccode = '".$_SESSION['']."'";
				$res = DB_query($sql);
				$POSAccount = DB_fetch_array($res);
				// fin obtener la cuenta
				$SQL = "INSERT INTO gltrans (
							type, 
							typeno, 
							trandate, 
							periodno, 
							account, 
							narrative, 
							amount
						) 
						VALUES (
							20000, 
							" . $InvoiceNo . ", 
							'" . $DefaultDispatchDate . "', 
							" . $PeriodNo . ", 
							" . $SalesGLAccounts['discountglcode'] . ", 
							'" . $_SESSION['Items']->DebtorNo . " - " . $OrderLine->StockID . " @ " . ($OrderLine->DiscountPercent * 100) . "%', 
							" . ($OrderLine->Price * $OrderLine->QtyDispatched * $OrderLine->DiscountPercent/$_SESSION['CurrencyRate']) . "
						)";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales discount GL posting could not be inserted because');
					$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
				
				// bowikaxu -  fin de insert a gltrans del punto de venta*/
				
			} /*end of if sales integrated with debtors */

		} /*Quantity dispatched is more than 0 */
	} /*end of OrderLine loop */


	if ($_SESSION['CompanyRecord']['gllink_debtors']==1){

/*Post debtors transaction to GL debit debtors, credit freight re-charged and credit sales */
		if (($_SESSION['Items']->total + $_SESSION['Items']->FreightCost + $TaxTotal) !=0) {
			$SQL = "INSERT INTO gltrans (
						type, 
						typeno, 
						trandate, 
						periodno, 
						account, 
						narrative, 
						amount
						) 
					VALUES (
						20000, 
						" . $InvoiceNo . ", 
						'" . $DefaultDispatchDate . "', 
						" . $PeriodNo . ", 
						" . $_SESSION['CompanyRecord']['debtorsact'] . ", 
						'" . $_SESSION['Items']->DebtorNo . "', 
						" . (($_SESSION['Items']->total + $_SESSION['Items']->FreightCost + $TaxTotal)/$_SESSION['CurrencyRate']) . "
					)";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The total debtor GL posting could not be inserted because');
			$DbgMsg = _('The following SQL to insert the total debtors control GLTrans record was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
		}

		/*Could do with setting up a more flexible freight posting schema that looks at the sales type and area of the customer branch to determine where to post the freight recovery */

		if ($_SESSION['Items']->FreightCost !=0) {
			$SQL = "INSERT INTO gltrans (
						type,
						typeno, 
						trandate, 
						periodno, 
						account, 
						narrative, 
						amount
					) 
				VALUES (
					20000, 
					" . $InvoiceNo . ",
					'" . $DefaultDispatchDate . "', 
					" . $PeriodNo . ", 
					" . $_SESSION['CompanyRecord']['freightact'] . ", 
					'" . $_SESSION['Items']->DebtorNo . "',
					" . (-($_SESSION['Items']->FreightCost)/$_SESSION['CurrencyRate']) . "
				)";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The freight GL posting could not be inserted because');
			$DbgMsg = _('The following SQL to insert the GLTrans record was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
		}
		/*foreach ( $TaxTotals as $TaxAuthID => $TaxAmount){	
			if ($TaxAmount !=0 ){
				$SQL = "INSERT INTO gltrans (
						type, 
						typeno, 
						trandate, 
						periodno, 
						account, 
						narrative, 
						amount
						) 
					VALUES (
						20000, 
						" . $InvoiceNo . ", 
						'" . $DefaultDispatchDate . "', 
						" . $PeriodNo . ", 
						" . $TaxGLCodes[$TaxAuthID] . ", 
						'" . $_SESSION['Items']->DebtorNo . "', 
						" . (-$TaxAmount/$_SESSION['CurrencyRate']) . "
					)";
	
				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The tax GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			}
		}*/
		
	} /*end of if Sales and GL integrated */

	
	// EMPIEZA INSERT PARA LA REMISION	
	
	$UltRem = "SELECT COUNT(Shipment) AS TotRem, CURDATE() AS Fech FROM rh_invoiceshipment";
	
	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('No se pudo obtener el valor de la ultima remision');
	$DbgMsg = _('The following SQL to insert the GLTrans record was used');
	$Result = DB_query($UltRem,$db,$ErrMsg,$DbgMsg,true);
	
	$myrow2 = db_fetch_array($Result);
	$totalremisiones = $myrow2['TotRem'];
	$fecha = $myrow2['Fech'];
	
	// INSERTAR A LA TABLA rh_invoiceshipment el numero de invoice, de shipment y la fecha

	$TBLRem = "INSERT INTO rh_invoiceshipment (
				invoice,
				shipment,
				fecha,
				facturado,
				type
				)
				VALUES (0,".
				$InvoiceNo.
				", '".$fecha."',0,2)";
				
	
	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('No se pudieron insertar los datos de la remision');
	$DbgMsg = _('Error al insertar valores en la tabla');
	$Result = DB_query($TBLRem,$db,$ErrMsg,$DbgMsg,true);
	
	// TERMINA INSERCION EN rh_invoiceshipment
	
	$SQL = "UPDATE systypes SET
			typeno = ".
			$InvoiceNo.
			" WHERE typeid = 20000";
				
	
	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('No se pudo actualizar la cantidad de Remisiones en systypes');
	$DbgMsg = _('Error al actualizar systypes');
	$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
	
	// FIN INSERT PARA LA REMISION
	
	// Verifica si la venta se pago completa o es una parte
		$pagado = $_POST['importe'];
		if($_POST['pago']=='cash'){
			
			$sql = "INSERT INTO rh_possales (trans, ip, user, cash, total, credcard, cheque, bono) VALUES ('".$_SESSION['ProcessingOrder']."', '".$_SERVER['REMOTE_ADDR']."',
				 '".$_SESSION['UserID']."', '".$_SESSION['Items']->total."', '".$_SESSION['Items']->total."',0,0,0)";
			$pago = DB_query($sql,$db,'Imposible Insertar la Transaccion','Fallo insert a venta al publico',true);
			
		}else if($_POST['pago']=='credcard'){
			$sql = "INSERT INTO rh_possales (trans, ip, user, credcard, total, cash, cheque, bono) VALUES ('".$_SESSION['ProcessingOrder']."', '".$_SERVER['REMOTE_ADDR']."',
				 '".$_SESSION['UserID']."', '".$_SESSION['Items']->total."', '".$_SESSION['Items']->total."',0,0,0)";
			$pago = DB_query($sql,$db,'Imposible Insertar la Transaccion','Fallo insert a venta al publico',true);
		}else if($_POST['pago']=='bono'){
			$sql = "INSERT INTO rh_possales (trans, ip, user, bono, total, cash, cheque, credcard) VALUES ('".$_SESSION['ProcessingOrder']."', '".$_SERVER['REMOTE_ADDR']."',
				 '".$_SESSION['UserID']."', '".$_SESSION['Items']->total."', '".$_SESSION['Items']->total."',0,0,0)";
			$pago = DB_query($sql,$db,'Imposible Insertar la Transaccion','Fallo insert a venta al publico',true);
		}else {
			$sql = "INSERT INTO rh_possales (trans, ip, user, cheque, total, cash, credcard, bono) VALUES ('".$_SESSION['ProcessingOrder']."', '".$_SERVER['REMOTE_ADDR']."',
				 '".$_SESSION['UserID']."', '".$_SESSION['Items']->total."', '".$_SESSION['Items']->total."',0,0,0)";
			$pago = DB_query($sql,$db,'Imposible Insertar la Transaccion','Fallo insert a venta al publico',true);
		}
		
		// fin verificar venta completa
	
	$SQL='COMMIT';
	$Result = DB_query($SQL,$db,'Imposible guardar transaccion en la base de datos','Fallo el QUERY COMMIT',true);
	
	unset($_SESSION['Items']->LineItems);
	unset($_SESSION['Items']);
	unset($_SESSION['ProcessingOrder']);
	
	
	
	prnMsg(_('Remision numero: ').$InvoiceNo,'success');
	
	// impresion del ticket
	echo "<BR><CENTER><FONT COLOR=blue><A HREF='rh_TicketPOS.php?FromTransNo=".($totalremisiones+1)."&InvOrCredit=Invoice&PrintPDF=True'>Imprimir Remision de Venta</A></CENTER></FONT>";
	// fin impresion ticket
	// rh_PrintCustTrans_Shipment.php?FromTransNo=50&InvOrCredit=Invoice&PrintPDF=True
	
	echo '</FORM>';

			unset($_SESSION['Items']->LineItems);
			$_SESSION['Items']->ItemsOrdered=0;
			unset($_SESSION['Items']);
			unset($_SESSION['ProcessingOrder']);
			$_SESSION['Items'] = new cart;

			if (in_array(2,$_SESSION['AllowedPageSecurityTokens'])){
				$_SESSION['RequireCustomerSelection'] = 1;
			} else {
				$_SESSION['RequireCustomerSelection'] = 0;
			}
			echo '<BR><BR>';
			prnMsg(_('Esta Venta Ha Sido Terminada'),'success');
			echo "<CENTER><FORM ACTION='POSEntry.php'>";
			echo "<INPUT TYPE=submit value='Otra Venta'>";
			echo "</FORM></CENTER>";
			include('includes/footer.inc');
			exit;
/*end of process invoice */

	}	
	/* bowikaxu fin de inserts de la remision */
	
	If (isset($NewItem)){
/* get the item details from the database and hold them in the cart object make the quantity 1 by default then add it to the cart */
/*Now figure out if the item is a kit set - the field MBFlag='K'*/
		$sql = "SELECT stockmaster.mbflag
		   		FROM stockmaster
				WHERE stockmaster.stockid='". $NewItem ."'";

		$ErrMsg =  _('Could not determine if the part being ordered was a kitset or not because');

		$KitResult = DB_query($sql, $db,$ErrMsg);

		$NewItemQty = 1; /*By Default */
		$Discount = 0; /*By default - can change later or discount category overide */
		
		if ($myrow=DB_fetch_array($KitResult)){
		   	if ($myrow['mbflag']=='K'){	/*It is a kit set item */
				$sql = "SELECT bom.component,
			    		bom.quantity
					FROM bom
					WHERE bom.parent='" . $NewItem . "'
					AND bom.effectiveto > '" . Date('Y-m-d') . "'
					AND bom.effectiveafter < '" . Date('Y-m-d') . "'";

				$ErrMsg = _('Could not retrieve kitset components from the database because');
				$KitResult = DB_query($sql,$db,$ErrMsg);

				$ParentQty = $NewItemQty;
				while ($KitParts = DB_fetch_array($KitResult,$db)){
					$NewItem = $KitParts['component'];
					$NewItemQty = $KitParts['quantity'] * $ParentQty;
					include('includes/SelectOrderItems_IntoCart.inc');
				}

			} else { /*Its not a kit set item*/
				
			     include('includes/SelectOrderItems_IntoCart.inc');
			}

		} /* end of if its a new item */
		
	} /*end of if its a new item */
		
	/* Run through each line of the order and work out the appropriate discount from the discount matrix */
	$DiscCatsDone = array();
	$counter =0;
	foreach ($_SESSION['Items']->LineItems as $OrderLine) {

		if ($OrderLine->DiscCat !="" AND ! in_array($OrderLine->DiscCat,$DiscCatsDone)){
			$DiscCatsDone[$Counter]=$OrderLine->DiscCat;
			$QuantityOfDiscCat =0;

			foreach ($_SESSION['Items']->LineItems as $StkItems_2) {
				/* add up total quantity of all lines of this DiscCat */
				if ($StkItems_2->DiscCat==$OrderLine->DiscCat){
					$QuantityOfDiscCat += $StkItems_2->Quantity;
				}
			}
			$result = DB_query("SELECT MAX(discountrate) AS discount
						FROM discountmatrix
						WHERE salestype='" .  $_SESSION['Items']->DefaultSalesType . "'
						AND discountcategory ='" . $OrderLine->DiscCat . "'
						AND quantitybreak <" . $QuantityOfDiscCat,$db);
			$myrow = DB_fetch_row($result);
			if ($myrow[0]!=0){ /* need to update the lines affected */
				foreach ($_SESSION['Items']->LineItems as $StkItems_2) {
					/* add up total quantity of all lines of this DiscCat */
					if ($StkItems_2->DiscCat==$OrderLine->DiscCat AND $StkItems_2->DiscountPercent < $myrow[0]){
						$_SESSION['Items']->LineItems[$StkItems_2->LineNumber]->DiscountPercent = $myrow[0];
					}
				}
			}
		}
		/*8
		foreach ($OrderLine->Taxes as $TaxLine) {
				if (isset($_POST[$OrderLine->LineNumber  . $TaxLine->TaxCalculationOrder . '_TaxRate'])){
					$_SESSION['Items']->LineItems[$OrderLine->LineNumber]->Taxes[$TaxLine->TaxCalculationOrder]->TaxRate = $_POST[$OrderLine->LineNumber  . $TaxLine->TaxCalculationOrder . '_TaxRate']/100;
				}
			}*/
		
	} /* end of discount matrix lookup code */

	if (count($_SESSION['Items']->LineItems)>0){ /*only show order lines if there are any */

/* This is where the order as selected should be displayed  reflecting any deletions or insertions*/

		echo '
			<TABLE CELLPADDING=2 COLSPAN=8 BORDER=1 align=right width=55%>
			<TR BGCOLOR=#800000>
			<TD class="tableheader">' . _('Codigo Articulo') . '</TD>
			<TD class="tableheader">' . _('Descripcion') . '</TD>
			<TD class="tableheader">' . _('Cantidad') . '</TD>
			<TD class="tableheader">' . _('Unidad') . '</TD>
			<TD class="tableheader">' . _('Precio') . '</TD>
			<TD class="tableheader">' . _('Descuento') . '</TD>
			
			<TD class="tableheader">' . _('Total') . '</TD>
			</TR>';

		$_SESSION['Items']->total = 0;
		$_SESSION['Items']->totalVolume = 0;
		$_SESSION['Items']->totalWeight = 0;
		$TaxTotals = array();
		$TaxGLCodes = array();
		$TaxTotal =0;
		$k =0;  //row colour counter
		foreach ($_SESSION['Items']->LineItems as $OrderLine) {

			$LineTotal = $OrderLine->Quantity * $OrderLine->Price * (1 - $OrderLine->DiscountPercent);
			$DisplayLineTotal = number_format($LineTotal,2);
			$DisplayDiscount = number_format(($OrderLine->DiscountPercent * 100),2);

			if ($OrderLine->QOHatLoc < $OrderLine->Quantity AND ($OrderLine->MBflag=='B' OR $OrderLine->MBflag=='M')) {
				/*There is a stock deficiency in the stock location selected */
				$RowStarter = '<tr bgcolor="#EEAABB">';
			} elseif ($k==1){
				$RowStarter = '<tr bgcolor="#CCCCCC">';
				$k=0;
			} else {
				$RowStarter = '<tr bgcolor="#EEEEEE">';
				$k=1;
			}

			echo $RowStarter;

			echo '<TD><A target="_blank" HREF="' . $rootpath . '/StockStatus.php?' . SID . 'StockID=' . $OrderLine->StockID . '">' . $OrderLine->StockID . '</A></TD>
				<TD>' . $OrderLine->ItemDescription . '</TD>
				<TD><INPUT TYPE=TEXT NAME="Quantity_' . $OrderLine->LineNumber . '" SIZE=4 MAXLENGTH=4 VALUE=' . $OrderLine->Quantity . '></TD>
				<TD>' . $OrderLine->Units . '</TD>';

					echo '<TD><INPUT TYPE=HIDDEN NAME="Price_' . $OrderLine->LineNumber . '" SIZE=16 MAXLENGTH=16 VALUE=' . $OrderLine->Price . '>'.$OrderLine->Price.'</TD>
					<TD><INPUT TYPE=TEXT NAME="Discount_' . $OrderLine->LineNumber . '" SIZE=4 MAXLENGTH=4 VALUE=' . ($OrderLine->DiscountPercent * 100) . '>%</TD>';
			// 2007-02-07 bowikaxu - aplicar taxes
			/*Need to list the taxes applicable to this line */
			//echo "<TD>";
			//$i=0;
			/*foreach ($_SESSION['Items']->LineItems[$OrderLine->LineNumber]->Taxes AS $Tax) {
				$i++;
			}*/
			//echo '</TD>';
			//echo '<TD ALIGN=RIGHT>';			
			$i=0; // initialise the number of taxes iterated through
			$TaxLineTotal = 0; //initialise tax total for the line
			/*foreach ($OrderLine->Taxes AS $Tax) {
				if ($i>0){
					echo '<BR>';
				}
				//echo '<input type=text name="' . $OrderLine->LineNumber . $Tax->TaxCalculationOrder . '_TaxRate" maxlength=4 SIZE=4 value="' . $Tax->TaxRate*100 . '">';
				$i++;
				if ($Tax->TaxOnTax ==1){
					$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * ($LineTotal + $TaxLineTotal));
					$TaxLineTotal += ($Tax->TaxRate * ($LineTotal + $TaxLineTotal));
				} else {
					$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * $LineTotal);
					$TaxLineTotal += ($Tax->TaxRate * $LineTotal);
				}
				$TaxGLCodes[$Tax->TaxAuthID] = $Tax->TaxGLCode;
			}*/
			//echo number_format($TaxLineTotal,2)."</TD>";
			//$TaxTotal += $TaxLineTotal;
			$DisplayLineTotal = number_format(($LineTotal),2);
			// bowikaxu - fin aplicar taxes
			echo '<TD ALIGN=RIGHT>' . $DisplayLineTotal . '</FONT></TD><TD><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '&Delete=' . $OrderLine->LineNumber . '" onclick="return confirm(\'' . _('Estas Seguro?') . '\');">' . _('Borrar') . '</A></TD></TR>';
			echo $RowStarter;
			echo '<INPUT TYPE=hidden  NAME="Narrative_' . $OrderLine->LineNumber . '">';

			$_SESSION['Items']->total = $_SESSION['Items']->total + $LineTotal;
			$_SESSION['Items']->totalVolume = $_SESSION['Items']->totalVolume + $OrderLine->Quantity * $OrderLine->Volume;
			$_SESSION['Items']->totalWeight = $_SESSION['Items']->totalWeight + $OrderLine->Quantity * $OrderLine->Weight;

		} /* end of loop around items */

		$DisplayTotal = number_format(($_SESSION['Items']->total),2);
		echo '<TR><TD></TD><TD><B>' . _('TOTAL') . '</B></TD><TD COLSPAN=5 ALIGN=RIGHT>' . $DisplayTotal . '</TD></TR>';
		echo "<TR><TD HEIGHT=15></TD></TR>";
		
		echo "<TR><TD COLSPAN=5 ALIGN=CENTER>
				<INPUT TYPE='radio' name='pago' value='cash' CHECKED>Efectivo
				<INPUT TYPE='radio' name='pago' value='credcard'>Tarjeta
				<INPUT TYPE='radio' name='pago' value='cheque'>Cheque
				<INPUT TYPE='radio' name='pago' value='bono'>Bono
				</TD><TD COLSPAN>	Importe $<INPUT TYPE=text NAME='importe' SIZE=10>				</TR>";
		
		echo '<TR><TD COLSPAN=7 ALIGN=CENTER><INPUT TYPE=SUBMIT NAME="Recalculate" Value="' . _('Re-Calcular') . '">';
		echo '<INPUT TYPE=SUBMIT NAME="DeliveryDetails" VALUE="' . _('Confirmar Venta') . '">';
		echo '<INPUT TYPE=SUBMIT Name="ChangeCustomer" VALUE="' . _('Cambiar Cliente') . '">';
		echo '<INPUT TYPE=SUBMIT NAME="CancelOrder" VALUE="' . _('Cancelar la Venta') . '" onclick="return confirm(\'' . _('Estas seguro que deseas cancelar la venta?') . '\');"></TD>';
		//$DisplayVolume = number_format($_SESSION['Items']->totalVolume,2);
		//$DisplayWeight = number_format($_SESSION['Items']->totalWeight,2);
		echo "</TABLE>";
		
	} # end of if lines

/* Now show the stock item selection search stuff below */

	 if (isset($_POST['QuickEntry']) && $_POST['QuickEntry']!=''){

	 		/*FORM VARIABLES TO POST TO THE ORDER 8 AT A TIME WITH PART CODE AND QUANTITY */
		  
	     echo '<FONT SIZE=4 COLOR=BLUE ><B>' . _('Entrada Rapida:') . '</B></FONT><BR>
	     	<TABLE BORDER=1 align=left width=20% height=100%>';

	     // bowikaxu - seleccionar todos los articulos no descontinuados
	     $SQL = "SELECT * FROM stockmaster WHERE discontinued=0";
	     $result = DB_query($SQL,$db,"Imposible Obtener la lista de Articulos");
		echo "<TR bgcolor='#CCCCCC'><TD><SELECT SIZE=10 name='part_1'>";
		$i=2;
		while($articulo = DB_fetch_array($result)){
			echo "<OPTION id='part_".$i."' value='".$articulo['stockid']."'>".$articulo['stockid']." - ".substr($articulo['description'],0,25)."</OPTION>";
			$i++;
			
		}
	     echo "</SELECT></TD>";
	     echo "<INPUT TYPE=hidden name='qty_1' value='1'>
	     		</TR>";
	     
	     //for ($i=1;$i<=$_SESSION['QuickEntries'];$i++){
				    	
	     	echo '<tr bgcolor="#CCCCCC">
				<TD width=20%>Codigo: <INPUT TYPE="text" name="codrap" size=18 maxlength=18>
				<INPUT TYPE="submit" name="QuickEntry" value="' . _('OK') . '"></TD>
			</TR>';
	   //}
	   
	     echo '<TR><INPUT TYPE="submit" name="QuickEntry" value="' . _('Entrada Rapida') . '">
                     <INPUT TYPE="submit" name="PartSearch" value="' . _('Buscar Articulo') . '"></TR></TABLE>';


?>
	     <script language='JavaScript' type='text/javascript'>
    //<![CDATA[
            <!--
            
            document.forma.codrap.focus();
            document.forma.codrap.select();
            //-->
    //]]>
	    </script>
<?php
		
	} /*end of PartSearch options to be displayed */
	   else { /* show the quick entry form variable */
		  
	   	echo '<input type="hidden" name="PartSearch" value="' .  _('Yes Please') . '">';
		
		$SQL="SELECT categoryid,
				categorydescription
			FROM stockcategory
			WHERE stocktype='F' OR stocktype='D' 
			ORDER BY categorydescription";
		$result1 = DB_query($SQL,$db);

		echo '<TABLE width=18%><TR><TD><FONT SIZE=2>' . _('Seleccionar categoria') . ':</FONT><SELECT NAME="StockCat">';

		if (!isset($_POST['StockCat'])){
			echo "<OPTION SELECTED VALUE='All'>" . _('Todas');
			$_POST['StockCat'] ='All';
		} else {
			echo "<OPTION VALUE='All'>" . _('Todas');
		}

		while ($myrow1 = DB_fetch_array($result1)) {

			if ($_POST['StockCat']==$myrow1['categoryid']){
				echo '<OPTION SELECTED VALUE=' . $myrow1['categoryid'] . '>' . $myrow1['categorydescription'];
			} else {
				echo '<OPTION VALUE='. $myrow1['categoryid'] . '>' . $myrow1['categorydescription'];
			}
		}

		?>

		</SELECT>
		</TR><TR align='center'>
		<TD><FONT SIZE=2><?php echo _('Descripcion'); ?></B>:</FONT>
		<INPUT TYPE="Text" NAME="Keywords" SIZE=20 MAXLENGTH=25 VALUE="<?php if (isset($_POST['Keywords'])) echo $_POST['Keywords']; ?>"></TD></TR>
		</TR><TR align='center'>
		<TD><FONT SIZE=2><?php echo _('Codigo'); ?></B>:</FONT>
		<INPUT TYPE="Text" NAME="StockCode" SIZE=15 MAXLENGTH=18 VALUE="<?php if (isset($_POST['StockCode'])) echo $_POST['StockCode']; ?>"></TD>
		</TR>
		<TR><TD>
		<CENTER><INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Buscar'); ?>">
		<INPUT TYPE=SUBMIT Name="QuickEntry" VALUE="<?php echo _('Usar Entrada Rapida'); ?>">
		</TD></TR>
		</TABLE>
		


		<script language='JavaScript' type='text/javascript'>

            	document.forms[0].StockCode.select();
            	document.forms[0].StockCode.focus();

		</script>

		<?php
		if (in_array(2,$_SESSION['AllowedPageSecurityTokens'])){
			//echo '<INPUT TYPE=SUBMIT Name="ChangeCustomer" VALUE="' . _('Cambiar Cliente') . '">';
			echo '<BR><BR><a target="_blank" href="' . $rootpath . '/Stocks.php?' . SID . '"><B>' . _('Nuevo Articulo') . '</B></a>';
		}

		echo '</CENTER>';

		if (isset($SearchResult)) {

			echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7 BORDER=1>';
			$TableHeader = '<TR><TD class="tableheader">' . _('Code') . '</TD>
                          			<TD class="tableheader">' . _('Description') . '</TD>
                          			<TD class="tableheader">' . _('Units') . '</TD></TR>';
			echo $TableHeader;
			$j = 1;
			$k=0; //row colour counter

			while ($myrow=DB_fetch_array($SearchResult)) {

				$ImageSource = $rootpath. '/' . $_SESSION['part_pics_dir'] . '/' . $myrow['stockid'] . '.jpg';

				if ($k==1){
					echo '<tr bgcolor="#CCCCCC">';
					$k=0;
				} else {
					echo '<tr bgcolor="#EEEEEE">';
					$k=1;
				}
				
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $ImageSource)){
					printf("<TD><FONT SIZE=1>%s</FONT></TD>
						<TD><FONT SIZE=1>%s</FONT></TD>
						<TD><FONT SIZE=1>%s</FONT></TD>
						<TD><IMG SRC=%s WIDTH=250></TD>
						<TD><FONT SIZE=1><A HREF='%s/POSEntry.php.php?%s&NewItem=%s'>" . _('Agregar') . "</A></FONT></TD>
						</TR>",
						$myrow['stockid'],
						$myrow['description'],
						$myrow['units'],
						$ImageSource,
						$rootpath,
						SID,
						$myrow['stockid']);
				} else { /*no picture to display */
					printf("<td><FONT SIZE=1>%s</FONT></td>
						<td><FONT SIZE=1>%s</FONT></td>
						<td><FONT SIZE=1>%s</FONT></td>
						<td ALIGN=CENTER><i>NO PICTURE</i></td>
						<td><FONT SIZE=1><a href='%s/POSEntry.php?%s&NewItem=%s'>" . _('Agregar') . "</a></FONT></td>
						</tr>",
						$myrow['stockid'],
						$myrow['description'],
						$myrow['units'],
						$rootpath,
						SID,
						$myrow['stockid']);
				}

				$j++;
				If ($j == 25){
					$j=1;
					echo $TableHeader;
				}
	#end of page full new headings if
			}
	#end of while loop
			echo '</TABLE>';

		}#end if SearchResults to show
	   	
	   }   
	
}#end of else not selecting a customer

echo '</FORM>';

include('includes/footer.inc');
?>