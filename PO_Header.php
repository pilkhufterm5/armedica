<?php
/* 	$_SESSION['ExistingPOOrder']: PO_ReadInOrder.inc 3604 2011-02-22 16:58:19Z realhost $*/
/* Esta modificacion es por causa de un error en la reescritura indevida de una variable de session  $_SESSION['ExistingOrder'] cambio a $_SESSION['ExistingPOOrder']
/* dentro del proceso de orden de compra, el conflicto se ocacionaba con la orden de venta.
/* $Id: PO_Header.php 3942 2010-09-30 15:19:03Z tim_schofield $ */

$PageSecurity = 4;
include('includes/DefinePOClass.php');
include('includes/session.inc');

/* Dummy for gettext */

$sDummy  = _('New Order') .
           _('Pending') .
           _('Authorised') .
           _('Rejected') .
           _('Cancelled') .
           _('Printed') .
           _('Completed');

unset($sDummy);

//$_SESSION['DatabaseName'] = "sme_erp_001";

if (isset($_GET['ModifyOrderNumber'])) {
	$title = _('Modify Purchase Order') . ' ' . $_GET['ModifyOrderNumber'];
} else {
	$title = _('Purchase Order Entry');
}

if (isset($_GET['SupplierID'])) {
	$_POST['Select']=$_GET['SupplierID'];
}

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

/*If the page is called is called without an identifier being set then
 * it must be either a new order, or the start of a modification of an
 * order, and so we must create a new identifier.
 *
 * The identifier only needs to be unique for this php session, so a
 * unix timestamp will be sufficient.
 */

if (empty($_GET['identifier'])) {
	$identifier=date('U');
} else {
	$identifier=$_GET['identifier'];
}

/*Page is called with NewOrder=Yes when a new order is to be entered
 * the session variable that holds all the PO data $_SESSION['PO'][$identifier]
 * is unset to allow all new details to be created */

if (isset($_GET['NewOrder']) and isset($_SESSION['PO'.$identifier])){
	unset($_SESSION['PO'.$identifier]);
	$_SESSION['ExistingPOOrder']=0;
}

if (isset($_POST['Select']) and empty($_POST['SupplierContact'])) {
	$sql = "SELECT contact
			FROM suppliercontacts
			WHERE supplierid='". $_POST['Select'] ."'";

	$SuppCoResult = DB_query($sql,$db);
	if (DB_num_rows($SuppCoResult)>0) {
		$myrow = DB_fetch_row($SuppCoResult);
		$_POST['SupplierContact'] = $myrow[0];
	} else {
		$_POST['SupplierContact']='';
	}
}

if (isset($_POST['UpdateStat']) AND $_POST['UpdateStat']!='') {
	/*The cancel button on the header screen - to delete order */
	$OK_to_updstat = 1;
	$OldStatus=$_SESSION['PO'.$identifier]->Stat;
	$NewStatus=$_POST['Stat'];
	$emailsql='SELECT email FROM www_users WHERE userid="'.$_SESSION['PO'.$identifier]->Initiator.'"';
	$emailresult=DB_query($emailsql, $db);
	$emailrow=DB_fetch_array($emailresult);
	$date = date($_SESSION['DefaultDateFormat']);
	if ($OldStatus!=$NewStatus) {
	/* assume this in the first instance */
		$authsql="SELECT authlevel
			FROM purchorderauth
			WHERE userid='".$_SESSION['UserID']."'
			AND currabrev='".$_SESSION['PO'.$identifier]->CurrCode."'";

		$authresult=DB_query($authsql,$db);
		$myrow=DB_fetch_array($authresult);
		$AuthorityLevel=$myrow['authlevel'];
		$OrderTotal=$_SESSION['PO'.$identifier]->Order_Value();
		if ($_POST['StatComments']!='') {
			$_POST['StatComments']=' - '.$_POST['StatComments'];
		}

		if ($NewStatus == PurchOrder::STATUS_AUTHORISED) {
			if ($AuthorityLevel>$OrderTotal) {
				$StatusComment=$date.' - Authorised by <a href="mailto:'.$emailrow['email'].'">'.$_SESSION['UserID'].
					'</a>'.$_POST['StatComments'].'<br>'.$_POST['statcommentscomplete'];
				$_SESSION['PO'.$identifier]->StatComments=$StatusComment;
				$_SESSION['PO'.$identifier]->Stat=$NewStatus;
				$_SESSION['PO'.$identifier]->AllowPrintPO=1;
			} else {
				$OK_to_updstat=0;
				prnMsg( _('You do not have permission to authorise this purchase order').'.<br>'. _('This order is for').' '.
					$_SESSION['PO'.$identifier]->CurrCode.' '.$OrderTotal.'. '.
					_('You can only authorise up to').' '.$_SESSION['PO'.$identifier]->CurrCode.' '.$AuthorityLevel.'.<br>'.
					_('If you think this is a mistake please contact the systems administrator') , 'warn');
			}
		}

		if ($NewStatus == PurchOrder::STATUS_CANCELLED and $OK_to_updstat==1) {
			if ($AuthorityLevel>$OrderTotal or $_SESSION['UserID']==$_SESSION['PO'.$identifier]->Initiator ) {
				$StatusComment=$date.' - Cancelled by <a href="mailto:'.$emailrow['email'].'">'.$_SESSION['UserID'].
					'</a>'.$_POST['StatComments'].'<br>'.$_POST['statcommentscomplete'];
				$_SESSION['PO'.$identifier]->StatComments=$StatusComment;
				$_SESSION['PO'.$identifier]->Stat=$NewStatus;
			} else {
				$OK_to_updstat=0;
				prnMsg( _('You do not have permission to cancel this purchase order').'.<br>'. _('This order is for').' '.
					$_SESSION['PO'.$identifier]->CurrCode.' '.$OrderTotal.'. '.
					_('Your authorisation limit is set at').' '.$_SESSION['PO'.$identifier]->CurrCode.' '.$AuthorityLevel.'.<br>'.
					_('If you think this is a mistake please contact the systems administrator') , 'warn');
			}
		}

		if ($NewStatus == PurchOrder::STATUS_REJECTED and $OK_to_updstat==1) {
			if ($AuthorityLevel>$OrderTotal) {
				$StatusComment=$date.' - Rejected by <a href="mailto:'.$emailrow['email'].'">'.$_SESSION['UserID'].
					'</a>'.$_POST['StatComments'].'<br>'.$_POST['statcommentscomplete'];
				$_SESSION['PO'.$identifier]->StatComments=$StatusComment;
				$_SESSION['PO'.$identifier]->Stat=$NewStatus;
			} else {
				$OK_to_updstat=0;
				prnMsg( _('You do not have permission to reject this purchase order').'.<br>'. _('This order is for').' '.
					$_SESSION['PO'.$identifier]->CurrCode.' '.$OrderTotal.'. '.
					_('Your authorisation limit is set at').' '.$_SESSION['PO'.$identifier]->CurrCode.' '.$AuthorityLevel.'.<br>'.
					_('If you think this is a mistake please contact the systems administrator') , 'warn');
			}
		}

		if ($NewStatus == PurchOrder::STATUS_PENDING and $OK_to_updstat==1) {
			if ($AuthorityLevel>$OrderTotal or $_SESSION['UserID']==$_SESSION['PO'.$identifier]->Initiator ) {
				$StatusComment=$date.' - Returned to Pending status by <a href="mailto:'.$emailrow['email'].'">'.$_SESSION['UserID'].
					'</a>'.$_POST['StatComments'].'<br>'.$_POST['statcommentscomplete'];
				$_SESSION['PO'.$identifier]->StatComments=$StatusComment;
				$_SESSION['PO'.$identifier]->Stat=$NewStatus;
			} else {
				$OK_to_updstat=0;
				prnMsg( _('You do not have permission to change the status of this purchase order').'.<br>'. _('This order is for').' '.
					$_SESSION['PO'.$identifier]->CurrCode.' '.$OrderTotal.'. '.
					_('Your authorisation limit is set at').' '.$_SESSION['PO'.$identifier]->CurrCode.' '.$AuthorityLevel.'.<br>'.
					_('If you think this is a mistake please contact the systems administrator') , 'warn');
			}
		}

		if ($OK_to_updstat==1){
//			unset($_SESSION['PO'.$identifier]->LineItems);
//			unset($_SESSION['PO'.$identifier]);
//			$_SESSION['PO'.$identifier] = new PurchOrder;
//			$_SESSION['RequireSupplierSelection'] = 1;

			if($_SESSION['ExistingPOOrder']!=0){

				if ($_SESSION['PO'.$identifier]->Stat==PurchOrder::STATUS_AUTHORISED) {
					$AllowPrint=1;
				} else {
					$AllowPrint=0;
				}
				$SQL = "UPDATE purchorders SET
				status='" . $_POST['Stat']. "',
				stat_comment='" . $StatusComment ."',
				allowprint='".$AllowPrint."'
				WHERE purchorders.orderno ='" . $_SESSION['ExistingPOOrder'] ."'";

				$ErrMsg = _('The order status could not be updated because');
				$DelResult=DB_query($SQL,$db,$ErrMsg);

//				$SQL = 'DELETE FROM purchorders WHERE purchorders.orderno=' . $_SESSION['ExistingPOOrder'];
//				$ErrMsg = _('The order header could not be deleted because');
//				$DelResult=DB_query($SQL,$db,$ErrMsg);
			} else {
				// Re-Direct to right place
				prnMsg( _('This is a new order. It must be created before you can change the status'), 'warn');
			}
		}
	}
}

/**
* 2008-08-19 ToPu at WAT
* add extra functionality:
* enter a purchase order with the item's stockid and maintained
* suppliers (in purchdata) form the screen "Search Inventory Items"
* of SelectProduct.php
*/
if (isset($_GET['NewOrder']) and isset($_GET['StockID']) and isset($_GET['SelectedSupplier'])) {
		/*
		* initialize a new order
		*/
		$_SESSION['ExistingPOOrder']=0;
		unset($_SESSION['PO'.$identifier]);
		/* initialize new class object */
		$_SESSION['PO'.$identifier] = new PurchOrder;
		/**
		* and fill it with essential data
		*/
		$_SESSION['PO'.$identifier]->AllowPrintPO = 1; /* Of course cos the
		* order aint even started !!*/
		$_SESSION['PO'.$identifier]->GLLink = $_SESSION['CompanyRecord']['gllink_stock'];
		/* set the SupplierID we got */
		$_SESSION['PO'.$identifier]->SupplierID = $_GET['SelectedSupplier'];
		/**/
		$_SESSION['RequireSupplierSelection'] = 0;
		/**/
		$_POST['Select'] = $_GET['SelectedSupplier'];

		/**
		* the item (its item code) that should be purchased
		*/
		$purch_item = $_GET['StockID'];

}

if (isset($_POST['EnterLines'])){
/*User hit the button to enter line items -
 *  ensure session variables updated then meta refresh to PO_Items.php*/

	$_SESSION['PO'.$identifier]->Location=$_POST['StkLocation'];
    $_SESSION['PO'.$identifier]->LocationSnd=$_POST['StkLocationSnd'];
	$_SESSION['PO'.$identifier]->SupplierContact=$_POST['SupplierContact'];
	$_SESSION['PO'.$identifier]->DelAdd1 = $_POST['DelAdd1'];
	$_SESSION['PO'.$identifier]->DelAdd2 = $_POST['DelAdd2'];
	$_SESSION['PO'.$identifier]->DelAdd3 = $_POST['DelAdd3'];
	$_SESSION['PO'.$identifier]->DelAdd4 = $_POST['DelAdd4'];
	$_SESSION['PO'.$identifier]->DelAdd5 = $_POST['DelAdd5'];
	$_SESSION['PO'.$identifier]->DelAdd6 = $_POST['DelAdd6'];
	$_SESSION['PO'.$identifier]->suppDelAdd1 = $_POST['suppDelAdd1'];
	$_SESSION['PO'.$identifier]->suppDelAdd2 = $_POST['suppDelAdd2'];
	$_SESSION['PO'.$identifier]->suppDelAdd3 = $_POST['suppDelAdd3'];
	$_SESSION['PO'.$identifier]->suppDelAdd4 = $_POST['suppDelAdd4'];
	$_SESSION['PO'.$identifier]->suppDelAdd5 = $_POST['suppDelAdd5'];
	$_SESSION['PO'.$identifier]->supptel= $_POST['supptel'];
	$_SESSION['PO'.$identifier]->Initiator = $_POST['Initiator'];
	$_SESSION['PO'.$identifier]->RequisitionNo = $_POST['Requisition'];
	$_SESSION['PO'.$identifier]->version = $_POST['version'];
	$_SESSION['PO'.$identifier]->deliverydate = $_POST['deliverydate'];
	$_SESSION['PO'.$identifier]->revised = $_POST['revised'];
	$_SESSION['PO'.$identifier]->ExRate = $_POST['ExRate'];
	$_SESSION['PO'.$identifier]->Comments = $_POST['Comments'];
	$_SESSION['PO'.$identifier]->deliveryby = $_POST['deliveryby'];
	$_SESSION['PO'.$identifier]->StatusMessage = $_POST['StatComments'];
	$_SESSION['PO'.$identifier]->paymentterms = $_POST['paymentterms'];
	$_SESSION['PO'.$identifier]->contact = $_POST['Contact'];
	$_SESSION['PO'.$identifier]->tel = $_POST['tel'];
	$_SESSION['PO'.$identifier]->port = $_POST['port'];

	if (isset($_POST['RePrint']) and $_POST['RePrint']==1){

		$_SESSION['PO'.$identifier]->AllowPrintPO=1;

		$sql = "UPDATE purchorders
			SET purchorders.allowprint=1
			WHERE purchorders.orderno='" . $_SESSION['PO'.$identifier]->OrderNo ."'";

		$ErrMsg = _('An error occurred updating the purchase order to allow reprints') . '. ' . _('The error says');
		$updateResult = DB_query($sql,$db,$ErrMsg);

	} else {
		$_POST['RePrint'] = 0;
	}

	echo "<meta http-equiv='Refresh' content='0; url=" . $rootpath . '/PO_Items.php?' . SID . 'identifier='.$identifier. "'>";
	echo '<p>';
	prnMsg(_('You should automatically be forwarded to the entry of the purchase order line items page') . '. ' .
		_('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ') ' .
		"<a href='$rootpath/PO_Items.php?" . SID. 'identifier='.$identifier . "'>" . _('click here') . '</a> ' . _('to continue'),'info');
		include('includes/footer.inc');
		exit;
} /* end of if isset _POST'EnterLines' */

echo '<a href="'. $rootpath . '/PO_SelectOSPurchOrder.php?' . SID . "identifier=".$identifier.'">'. _('Back to Purchase Orders'). '</a><br>';

/*The page can be called with ModifyOrderNumber=x where x is a purchase
 * order number. The page then looks up the details of order x and allows
 * these details to be modified */

if (isset($_GET['ModifyOrderNumber'])){
	include ('includes/PO_ReadInOrder.inc');
}

if (isset($_POST['CancelOrder']) AND $_POST['CancelOrder']!='') {
/*The cancel button on the header screen - to delete order */
	$OK_to_delete = 1;	 //assume this in the first instance

	if(!isset($_SESSION['ExistingPOOrder']) OR $_SESSION['ExistingPOOrder']!=0) {
		/* need to check that not already dispatched or invoiced
		 * by the supplier */

		if($_SESSION['PO'.$identifier]->Any_Already_Received()==1){
			$OK_to_delete =0;
			prnMsg( _('This order cannot be cancelled because some of it has already been received') . '. ' .
				_('The line item quantities may be modified to quantities more than already received') . '. ' .
				_('Prices cannot be altered for lines that have already been received') .' '.
				_('and quantities cannot be reduced below the quantity already received'),'warn');
		}

	}

	if ($OK_to_delete==1){
		$emailsql="SELECT email FROM www_users WHERE userid='".$_SESSION['PO'.$identifier]->Initiator."'";
		$emailresult=DB_query($emailsql, $db);
		$emailrow=DB_fetch_array($emailresult);
		$StatusComment=date($_SESSION['DefaultDateFormat']).
			' - Order Cancelled by <a href="mailto:'.$emailrow['email'].'">'.$_SESSION['UserID'].'</a><br>'.$_POST['statcommentscomplete'];
		unset($_SESSION['PO'.$identifier]->LineItems);
		unset($_SESSION['PO'.$identifier]);
		$_SESSION['PO'.$identifier] = new PurchOrder;
		$_SESSION['RequireSupplierSelection'] = 1;

		if($_SESSION['ExistingPOOrder']!=0){

			$sql = "UPDATE purchorderdetails
				SET completed=1
				WHERE purchorderdetails.orderno ='" . $_SESSION['ExistingPOOrder'] ."'";
			$ErrMsg = _('The order detail lines could not be deleted because');
			$DelResult=DB_query($sql,$db,$ErrMsg);

			$sql="UPDATE purchorders
				SET status='".PurchOrder::STATUS_CANCELLED."',
				stat_comment='".$StatusComment."'
				WHERE orderno='".$_SESSION['ExistingPOOrder']."'";

			$ErrMsg = _('The order header could not be deleted because');
			$DelResult=DB_query($sql,$db,$ErrMsg);
			prnMsg( _('Order number').' '.$_SESSION['ExistingPOOrder'].' '._('has been cancelled'), 'success');
			unset($_SESSION['PO'.$identifier]);
			unset($_SESSION['ExistingPOOrder']);
		} else {
		// Re-Direct to right place
			unset($_SESSION['PO'.$identifier]);
			prnMsg( _('The creation of the new order has been cancelled'), 'success');
		}
	}
}

if (!isset($_SESSION['PO'.$identifier])){
	/* It must be a new order being created
	 * $_SESSION['PO'.$identifier] would be set up from the order modification
	 * code above if a modification to an existing order. Also
	 * $ExistingPOOrder would be set to 1. The delivery check screen
	 * is where the details of the order are either updated or
	 * inserted depending on the value of ExistingPOOrder */

		$_SESSION['ExistingPOOrder']=0;
		$_SESSION['PO'.$identifier] = new PurchOrder;
		$_SESSION['PO'.$identifier]->AllowPrintPO = 1; /*Of course cos the order aint even started !!*/
		$_SESSION['PO'.$identifier]->GLLink = $_SESSION['CompanyRecord']['gllink_stock'];

		if ($_SESSION['PO'.$identifier]->SupplierID=='' OR !isset($_SESSION['PO'.$identifier]->SupplierID)){

/* a session variable will have to maintain if a supplier
 * has been selected for the order or not the session
 * variable supplierID holds the supplier code already
 * as determined from user id /password entry  */
			$_SESSION['RequireSupplierSelection'] = 1;
		} else {
			$_SESSION['RequireSupplierSelection'] = 0;
		}

}

if (isset($_POST['ChangeSupplier'])) {

/* change supplier only allowed with appropriate permissions -
 * button only displayed to modify is AccessLevel >10
 * (see below)*/
	if ($_SESSION['PO'.$identifier]->Stat == PurchOrder::STATUS_PENDING and $_SESSION['UserID']==$_SESSION['PO'.$identifier]->Initiator) {
		if ($_SESSION['PO'.$identifier]->Any_Already_Received()==0){
			$emailsql='SELECT email FROM www_users WHERE userid="'.$_SESSION['PO'.$identifier]->Initiator.'"';
			$emailresult=DB_query($emailsql, $db);
			$emailrow=DB_fetch_array($emailresult);
			$date = date($_SESSION['DefaultDateFormat']);
			$_SESSION['RequireSupplierSelection']=1;
			$_SESSION['PO'.$identifier]->Stat = PurchOrder::STATUS_PENDING;
			$StatusComment=$date.' - Supplier changed by <a href="mailto:'.$emailrow['email'].'">'.$_SESSION['UserID'].
				'</a> - '.$_POST['StatComments'].'<br>'.$_POST['statcommentscomplete'];
			$_SESSION['PO'.$identifier]->StatComments=$StatusComment;
		} else {
			echo '<br><br>';
			prnMsg(_('Cannot modify the supplier of the order once some of the order has been received'),'warn');
		}
	}
}

$msg='';
if (isset($_POST['SearchSuppliers'])){

	if (strlen($_POST['Keywords'])>0 AND strlen($_SESSION['PO'.$identifier]->SupplierID)>0) {
		$msg=_('Supplier name keywords have been used in preference to the supplier code extract entered');
	}
	if ($_POST['Keywords']=='' AND $_POST['SuppCode']=='') {
		$msg=_('At least one Supplier Name keyword OR an extract of a Supplier Code must be entered for the search');
	} else {
		if (strlen($_POST['Keywords'])>0) {
		//insert wildcard characters in spaces
			$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';

			$SQL = "SELECT suppliers.supplierid,
					suppliers.suppname,
					suppliers.address1,
					suppliers.address2,
					suppliers.address3,
					suppliers.address4,
					suppliers.address5,
					suppliers.address6,
					suppliers.currcode
				FROM suppliers
				WHERE suppliers.suppname LIKE '". $SearchString ."'
				ORDER BY suppliers.suppname";

		} elseif (strlen($_POST['SuppCode'])>0){
			$SQL = "SELECT suppliers.supplierid,
					suppliers.suppname,
					suppliers.address1,
					suppliers.address2,
					suppliers.address3,
					suppliers.address4,
					suppliers.address5,
					suppliers.address6,
					suppliers.currcode
				FROM suppliers
				WHERE suppliers.supplierid LIKE '%" . $_POST['SuppCode'] . "%'
				ORDER BY suppliers.supplierid";
		}

		$ErrMsg = _('The searched supplier records requested cannot be retrieved because');
		$result_SuppSelect = DB_query($SQL,$db,$ErrMsg);

		if (DB_num_rows($result_SuppSelect)==1){
			$myrow=DB_fetch_array($result_SuppSelect);
			$_POST['Select'] = $myrow['supplierid'];
		} elseif (DB_num_rows($result_SuppSelect)==0){
			prnMsg( _('No supplier records contain the selected text') . ' - ' .
				_('please alter your search criteria and try again'),'info');
		}
	} /*one of keywords or SuppCode was more than a zero length string */
} /*end of if search for supplier codes/names */


// added by Hudson
if((!isset($_POST['SearchSuppliers']) or $_POST['SearchSuppliers']=='' ) AND
	(isset($_SESSION['PO'.$identifier]->SupplierID) AND $_SESSION['PO'.$identifier]->SupplierID!='')){

	/*The session variables are set but the form variables have been lost
	 * need to restore the form variables from the session */
	$_POST['SupplierID']=$_SESSION['PO'.$identifier]->SupplierID;
	$_POST['SupplierName']=$_SESSION['PO'.$identifier]->SupplierName;
	$_POST['CurrCode'] = $_SESSION['PO'.$identifier]->CurrCode;
	$_POST['ExRate'] = $_SESSION['PO'.$identifier]->ExRate;
	$_POST['paymentterms'] = $_SESSION['PO'.$identifier]->paymentterms;
	$_POST['DelAdd1']=$_SESSION['PO'.$identifier]->DelAdd1;
	$_POST['DelAdd2']=$_SESSION['PO'.$identifier]->DelAdd2;
	$_POST['DelAdd3']=$_SESSION['PO'.$identifier]->DelAdd3;
	$_POST['DelAdd4']=$_SESSION['PO'.$identifier]->DelAdd4;
	$_POST['DelAdd5']=$_SESSION['PO'.$identifier]->DelAdd5;
	$_POST['DelAdd6']=$_SESSION['PO'.$identifier]->DelAdd6;
	$_POST['suppDelAdd1']=$_SESSION['PO'.$identifier]->suppDelAdd1;
	$_POST['suppDelAdd2']=$_SESSION['PO'.$identifier]->suppDelAdd2;
	$_POST['suppDelAdd3']=$_SESSION['PO'.$identifier]->suppDelAdd3;
	$_POST['suppDelAdd4']=$_SESSION['PO'.$identifier]->suppDelAdd4;
	$_POST['suppDelAdd5']=$_SESSION['PO'.$identifier]->suppDelAdd5;
	$_POST['suppDelAdd6']=$_SESSION['PO'.$identifier]->suppDelAdd6;

}

if (isset($_POST['Select'])) {

/* will only be true if page called from supplier selection form
 * or set because only one supplier record returned from a search
 * so parse the $Select string into supplier code and branch code */
	$sql="SELECT currcode FROM suppliers where supplierid='".$_POST['Select']."'";
	$result=DB_query($sql,$db);
	$myrow=DB_fetch_array($result);
	$SupplierCurrCode=$myrow['currcode'];

	$authsql="SELECT cancreate
			FROM purchorderauth
			WHERE userid='".$_SESSION['UserID']."'
			AND currabrev='".$SupplierCurrCode."'";

	$authresult=DB_query($authsql,$db);

	$sql = "SELECT suppliers.suppname,
			suppliers.currcode,
			currencies.rate,
			suppliers.paymentterms,
			suppliers.address1,
			suppliers.address2,
			suppliers.address3,
			suppliers.address4,
			suppliers.address5,
			suppliers.address6,
			suppliers.phn,
			suppliers.port
		FROM suppliers INNER JOIN currencies
		ON suppliers.currcode=currencies.currabrev
		WHERE supplierid='" . $_POST['Select'] . "'";

	$ErrMsg = _('The supplier record of the supplier selected') . ': ' . $_POST['Select'] . ' ' .
		_('cannot be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the supplier details and failed was');
	$result =DB_query($sql,$db,$ErrMsg,$DbgMsg);
	$myrow = DB_fetch_row($result);
	$SupplierName = $myrow[0];
		// added for suppliers lookup fields

	if (($authmyrow=DB_fetch_array($authresult) and $authmyrow[0]==0 ) ) {
		$_POST['SupplierName'] = $myrow[0];
		$_POST['CurrCode'] = 	$myrow[1];
		$_POST['ExRate'] = 	$myrow[2];
		$_POST['paymentterms']=	$myrow[3];
		$_POST['suppDelAdd1'] = $myrow[4];
		$_POST['suppDelAdd2'] = $myrow[5];
		$_POST['suppDelAdd3'] = $myrow[6];
		$_POST['suppDelAdd4'] = $myrow[7];
		$_POST['suppDelAdd5'] = $myrow[8];
		$_POST['suppDelAdd6'] = $myrow[9];
		$_POST['supptel'] = $myrow[10];
		$_POST['port'] = $myrow[11];

		$_SESSION['PO'.$identifier]->SupplierID = $_POST['Select'];
		$_SESSION['RequireSupplierSelection'] = 0;
		$_SESSION['PO'.$identifier]->SupplierName = $_POST['SupplierName'];
		$_SESSION['PO'.$identifier]->CurrCode = $_POST['CurrCode'];
		$_SESSION['PO'.$identifier]->ExRate = $_POST['ExRate'];
		$_SESSION['PO'.$identifier]->paymentterms = $_POST['paymentterms'];
		$_SESSION['PO'.$identifier]->suppDelAdd1 = $_POST['suppDelAdd1'];
		$_SESSION['PO'.$identifier]->suppDelAdd2 = $_POST['suppDelAdd2'];
		$_SESSION['PO'.$identifier]->suppDelAdd3 = $_POST['suppDelAdd3'];
		$_SESSION['PO'.$identifier]->suppDelAdd4 = $_POST['suppDelAdd4'];
		$_SESSION['PO'.$identifier]->suppDelAdd5 = $_POST['suppDelAdd5'];
		$_SESSION['PO'.$identifier]->suppDelAdd6 = $_POST['suppDelAdd6'];
		$_SESSION['PO'.$identifier]->supptel = $_POST['supptel'];
		$_SESSION['PO'.$identifier]->port = $_POST['port'];
	} else {
		prnMsg( _('You do not have the authority to raise Purchase Orders for ').
			$SupplierName.'. '._('Please Consult your system administrator for more information').'. '
			._('You can setup authorisations ').'<a href=PO_AuthorisationLevels.php>'._('here').'.</a>', 'warn');
		include('includes/footer.inc');
		exit;
	}

	// end of added for suppliers lookup fields

} else {
	$_POST['Select'] = $_SESSION['PO'.$identifier]->SupplierID;
	$sql = "SELECT suppliers.suppname,
			suppliers.currcode,
			suppliers.paymentterms,
			suppliers.address1,
			suppliers.address2,
			suppliers.address3,
			suppliers.address4,
			suppliers.address5,
			suppliers.address6,
			suppliers.phn,
			suppliers.port
		FROM suppliers INNER JOIN currencies
		ON suppliers.currcode=currencies.currabrev
		WHERE supplierid='" . $_POST['Select'] . "'";

	$ErrMsg = _('The supplier record of the supplier selected') . ': ' . $_POST['Select'] . ' ' .
		_('cannot be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the supplier details and failed was');
	$result =DB_query($sql,$db,$ErrMsg,$DbgMsg);


	$myrow = DB_fetch_row($result);


	// added for suppliers lookup fields
	if (!isset($_SESSION['PO'.$identifier])) {
		$_POST['SupplierName'] = $myrow[0];
		$_POST['CurrCode'] = 	$myrow[1];
		$_POST['paymentterms']=	$myrow[2];
		$_POST['suppDelAdd1'] = $myrow[3];
		$_POST['suppDelAdd2'] = $myrow[4];
		$_POST['suppDelAdd3'] = $myrow[5];
		$_POST['suppDelAdd4'] = $myrow[6];
		$_POST['suppDelAdd5'] = $myrow[7];
		$_POST['suppDelAdd6'] = $myrow[8];
		$_POST['supptel'] = $myrow[9];
		$_POST['port'] = $myrow[10];

		$_SESSION['PO'.$identifier]->SupplierID = $_POST['Select'];
		$_SESSION['RequireSupplierSelection'] = 0;
		$_SESSION['PO'.$identifier]->SupplierName = $_POST['SupplierName'];
		$_SESSION['PO'.$identifier]->CurrCode = $_POST['CurrCode'];
		$_SESSION['PO'.$identifier]->ExRate = $_POST['ExRate'];
		$_SESSION['PO'.$identifier]->paymentterms = $_POST['paymentterms'];
		$_SESSION['PO'.$identifier]->suppDelAdd1 = $_POST['suppDelAdd1'];
		$_SESSION['PO'.$identifier]->suppDelAdd2 = $_POST['suppDelAdd2'];
		$_SESSION['PO'.$identifier]->suppDelAdd3 = $_POST['suppDelAdd3'];
		$_SESSION['PO'.$identifier]->suppDelAdd4 = $_POST['suppDelAdd4'];
		$_SESSION['PO'.$identifier]->suppDelAdd5 = $_POST['suppDelAdd5'];
		$_SESSION['PO'.$identifier]->suppDelAdd6 = $_POST['suppDelAdd6'];
		$_SESSION['PO'.$identifier]->supptel = $_POST['supptel'];
		$_SESSION['PO'.$identifier]->port = $_POST['port'];
	// end of added for suppliers lookup fields
	}
}

// MADE THE SUPPILERS BECOME SELECT MENU NOT BY SEARCHING By Hudson @2008/6/30

// part of step 1
if ($_SESSION['RequireSupplierSelection'] ==1 OR !isset($_SESSION['PO'.$identifier]->SupplierID) OR
		$_SESSION['PO'.$identifier]->SupplierID=='' ) {
//if (true) {
	echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/supplier.png" title="' .
		_('Purchase Order') . '" alt="">' . ' ' . _('Purchase Order: Select Supplier') . '';
	echo "<form action='" . $_SERVER['PHP_SELF'] . '?' . SID . "identifier=".$identifier."' method=post name='choosesupplier'>";
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	if (strlen($msg)>1){
		prnMsg($msg,'warn');
	}

	echo '<table cellpadding=3 colspan=4 class=selection>
	<tr>
	<td><font size=1>' . _('Enter text in the supplier name') . ":</font></td>
	<td><input type='Text' name='Keywords' size=20	maxlength=25></td>
	<td><font size=3><b>" . _('OR') . '</b></font></td>
	<td><font size=1>' . _('Enter text extract in the supplier code') . ":</font></td>
	<td><input type='text' name='SuppCode' size=15	maxlength=18></td>
	</tr>
	</table><br><div class='centre'>
	<input type=submit name='SearchSuppliers' value=" . _('Search Now') . ">
	<input type=submit action=reset value='" . _('Reset') . "'></div>";

	echo '<script  type="text/javascript">defaultControl(document.forms[0].Keywords);</script>';

// UPDATED BY HUDSON 30/6/2008

	if (isset($result_SuppSelect)) {

		echo '<br><table cellpadding=3 colspan=7 class=selection>';

		$tableheader = "<tr>
				<th>" . _('Code') . "</th>
				<th>" . _('Supplier Name') . "</th>
				<th>" . _('Address') . "</th>
				<th>" . _('Currency') . '</th>
				</tr>';

		echo $tableheader;

		$j = 1;
		$k = 0; /*row counter to determine background colour */

		while ($myrow=DB_fetch_array($result_SuppSelect)) {

			if ($k==1){
				echo '<tr class="EvenTableRows">';
				$k=0;
			} else {
				echo '<tr class="OddTableRows">';
				$k++;
			}

			echo "<td><input type='submit' style='width:100%' name='Select' value='".$myrow['supplierid']."' ></td>
				<td>".$myrow['suppname']."</td><td>";

			for ($i=1; $i<=6; $i++) {
				if ($myrow['address'.$i] != '') {
					echo $myrow['address'.$i].'<br>';
				}
			}
			echo "</td><td>".$myrow['currcode']."</td></tr>";

			//end of page full new headings if
		}
//end of while loop

		echo '</table>';

	}
//end if results to show

//end if RequireSupplierSelection
} else {
/* everything below here only do if a supplier is selected */

	echo "<form name='form1' action='" . $_SERVER['PHP_SELF'] . '?' . SID . "identifier=".$identifier. "' method=post>";
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

// Be careful not made confused by orderno and realorderno
//	$orderno = previous_id("purchorders","orderno");
//    	$_SESSION['PO'.$identifier]->OrderNo2 = $orderno;
	echo '<p class="page_title_text">
            <img src="'.$rootpath.'/css/'.$theme.'/images/supplier.png" title="' . _('Purchase Order') . '" alt="">
	        ' . $_SESSION['PO'.$identifier]->SupplierName . ' - ' . _('All amounts stated in') . '
            ' . $_SESSION['PO'.$identifier]->CurrCode . '<br />';
	if ($_SESSION['ExistingPOOrder']) {
		echo  _(' Modify Purchase Order Number') . ' ' . $_SESSION['PO'.$identifier]->OrderNo;

    echo '</p>';

	}

/* 2008-08-19 ToPu -- debugging purpose */
	if (isset($purch_item)) {
		prnMsg(_('Purchase Item(s) with this code') . ': ' .  $purch_item,'info');

		/**
		 * 2008-08-21 ToPu
		 * Now go ahead to PO_Items.php
		 * with NewItem=$purch_item
		 */
		/* a somewhat nice outfit for that link */
		echo "<div class='centre'>";
		echo '<br><table class="table_index"><tr><td class="menu_group_item">';

		/* the link */
		echo '<li><a href="'.$rootpath.'/PO_Items.php?' . SID . 'NewItem=' . $purch_item . "&identifier=".$identifier. '">' .
			_('Enter Line Item to this purchase order') . '</a></li>';
		/**/
		echo "</td></tr></table></div><br>";

		if (isset($_GET['Quantity'])) {
			$Qty=$_GET['Quantity'];
		} else {
			$Qty=1;
		}

		$sql="SELECT
					controlled,
					serialised,
					description,
					units ,
					decimalplaces
				FROM stockmaster
				WHERE stockid='".$purch_item."'";
		$result=DB_query($sql, $db);
		$stockmasterrow=DB_fetch_array($result);

		$sql="SELECT
					price,
					suppliersuom,
					suppliers_partno,
					conversionfactor
				FROM purchdata
				WHERE supplierno='".$_GET['SelectedSupplier'] ."'
				AND stockid='".$purch_item ."'";
		$result=DB_query($sql, $db);
		$purchdatarow=DB_fetch_array($result);
		if (!isset($purchdatarow['conversionfactor'])) {
			$purchdatarow['conversionfactor']=1;
		}
		$sql="SELECT
					stockact
				FROM stockcategory
				LEFT JOIN stockmaster ON stockmaster.categoryid=stockcategory.categoryid
				WHERE stockid='".$purch_item. "'";
		$result=DB_query($sql, $db);
		$categoryrow=DB_fetch_array($result);

		$_SESSION['PO'.$identifier]->add_to_order(
				1,
				$purch_item,
				$stockmasterrow['serialised'],
				$stockmasterrow['controlled'],
				$Qty,
				$stockmasterrow['description'],
				$purchdatarow['price'],
				$stockmasterrow['units'],
				$categoryrow['stockact'],
				date($_SESSION['DefaultDateFormat']),
				0,
				0,
				'',
				0,
				0,
				'',
				$stockmasterrow['decimalplaces'],
				$purch_item,
				$purchdatarow['suppliersuom'],
				$purchdatarow['conversionfactor'],
				$purchdatarow['suppliers_partno'],
				$Qty*$purchdatarow['price'],
				'',
				0,
				0,
				0,
				0,
				$Qty,
				$Qty*$purchdatarow['price']);
		echo "<meta http-equiv='Refresh' content='0; url=" . $rootpath . '/PO_Items.php?' . SID . 'identifier='.$identifier. "'>";
	}

	/*Set up form for entry of order header stuff */

	if (!isset($_POST['LookupDeliveryAddress']) and (!isset($_POST['StkLocationSnd']) or $_POST['StkLocationSnd'])
		AND (isset($_SESSION['PO'.$identifier]->Location) AND $_SESSION['PO'.$identifier]->Location != '')) {
		/* The session variables are set but the form variables have
	     * been lost --
	     * need to restore the form variables from the session */
	    $_POST['StkLocation']=$_SESSION['PO'.$identifier]->Location;
        $_POST['StkLocationSnd']=$_SESSION['PO'.$identifier]->LocationSnd;
		$_POST['SupplierContact']=$_SESSION['PO'.$identifier]->SupplierContact;
	    $_POST['DelAdd1']=$_SESSION['PO'.$identifier]->DelAdd1;
	    $_POST['DelAdd2']=$_SESSION['PO'.$identifier]->DelAdd2;
	    $_POST['DelAdd3']=$_SESSION['PO'.$identifier]->DelAdd3;
	    $_POST['DelAdd4']=$_SESSION['PO'.$identifier]->DelAdd4;
	    $_POST['DelAdd5']=$_SESSION['PO'.$identifier]->DelAdd5;
	    $_POST['DelAdd6']=$_SESSION['PO'.$identifier]->DelAdd6;
	    $_POST['Initiator']=$_SESSION['PO'.$identifier]->Initiator;
	    $_POST['Requisition']=$_SESSION['PO'.$identifier]->RequisitionNo;
	    $_POST['version']=$_SESSION['PO'.$identifier]->version;
	    $_POST['deliverydate']=$_SESSION['PO'.$identifier]->deliverydate;
	    $_POST['revised']=$_SESSION['PO'.$identifier]->revised;
	    $_POST['ExRate']=$_SESSION['PO'.$identifier]->ExRate;
	    $_POST['Comments']=$_SESSION['PO'.$identifier]->Comments;
	    $_POST['deliveryby']=$_SESSION['PO'.$identifier]->deliveryby;
	    $_POST['paymentterms']=$_SESSION['PO'.$identifier]->paymentterms;
	}

// move apart by Hudson
	echo '<br><table colspan=1 width=80%>
		<tr>
			<th><font color=blue size=4><b>' . _('Order Initiation Details') . '</b></font></th>

			<th><font color=blue size=4><b>' . _('Order Status') . '</b></font></th>
		</tr>		<tr><td style="width:50%">';

	echo '<table class=selection width=100%>';
	echo '<tr><td>' . _('PO Date') . ':</td><td>';
	if ($_SESSION['ExistingPOOrder']!=0){
		echo ConvertSQLDate($_SESSION['PO'.$identifier]->Orig_OrderDate);
	} else {
		/* DefaultDateFormat defined in config.php */
		echo Date($_SESSION['DefaultDateFormat']);
	}
	echo '</td></tr>';

	$date = date($_SESSION['DefaultDateFormat']);

	if (isset($_GET['ModifyOrderNumber']) && $_GET['ModifyOrderNumber'] != '') {
		$_SESSION['PO'.$identifier]->version += 1;
		$_POST['version'] =  $_SESSION['PO'.$identifier]->version;
	} elseif (isset($_SESSION['PO'.$identifier]->version) and $_SESSION['PO'.$identifier]->version != '') {
		$_POST['version'] =  $_SESSION['PO'.$identifier]->version;
	} else {
		$_POST['version']='1';
	}

	if (!isset($_POST['deliverydate'])) {
		$_POST['deliverydate']= date($_SESSION['DefaultDateFormat']);
	}

	echo '<tr><td>' . _('Version'). ' #' . ":</td><td><input type='hidden' name='version' size=16 maxlength=15
		value='" . $_POST['version'] . "'>".$_POST['version']."</td></tr>";
	echo '<tr><td>' . _('Revised') . ":</td><td><input type='hidden' name='revised' size=11 maxlength=15 value=" .
		$date . '>'.$date.'</td></tr>';

	echo '<tr><td>' . _('Delivery Date') . ":</td><td><input type='text' class=date alt='".$_SESSION['DefaultDateFormat'].
		"' name='deliverydate' size=11 value=" . $_POST['deliverydate'] . '>'."</td></tr>";

	if (!isset($_POST['Initiator'])) {
		$_POST['Initiator'] = $_SESSION['UserID'];
		$_POST['Requisition'] = '';
	}

	echo '<tr><td>' . _('Initiated By') . ":</td>
			<td><input type='hidden' name='Initiator' size=11 maxlength=10 value=" .
			$_POST['Initiator'] . ">".$_POST['Initiator']."</td></tr>";
	echo '<tr><td>' . _('Requisition Ref') . ":</td><td><input type='text' name='Requisition' size=16
		maxlength=15 value=" . $_POST['Requisition'] . '></td></tr>';

//	echo '<tr><td>' . _('Exchange Rate') . ":</td>
//			<td><input type=TEXT name='ExRate' size=16 maxlength=15 VALUE=" . $_POST['ExRate'] . '></td>
//	echo "<input type='hidden' name='ExRate' size=16 maxlength=15 value=" . $_POST['ExRate'] . "></td>";
//		</tr>';
	echo '<tr><td>' . _('Date Printed') . ':</td><td>';

	if (isset($_SESSION['PO'.$identifier]->DatePurchaseOrderPrinted) AND strlen($_SESSION['PO'.$identifier]->DatePurchaseOrderPrinted)>6){
		echo ConvertSQLDate($_SESSION['PO'.$identifier]->DatePurchaseOrderPrinted);
		$Printed = True;
	} else {
		$Printed = False;
		echo _('Not yet printed');
	}

	if (isset($_POST['AllowRePrint'])) {
		$sql="UPDATE purchorders SET allowprint=1 WHERE orderno='".$_SESSION['PO'.$identifier]->OrderNo . "'";
		$result=DB_query($sql, $db);
	}

	if ($_SESSION['PO'.$identifier]->AllowPrintPO==0 AND empty($_POST['RePrint'])){
		echo '<tr><td>' . _('Allow Reprint') . ":</td><td><select name='RePrint' onChange='ReloadForm(form1.AllowRePrint)'><option selected value=0>" .
			_('No') . "<option value=1>" . _('Yes') . '</select></td>';
		echo '<td><input type=submit name="AllowRePrint" value="Update"></td></tr>';
	} elseif ($Printed) {
		echo "<tr><td colspan=2><a target='_blank'  href='$rootpath/PO_PDFPurchOrder.php?" .
			SID . "OrderNo=" . $_SESSION['ExistingPOOrder'] . "&identifier=".$identifier. "'>" . _('Reprint Now') . '</a></td></tr>';
	}

	echo '</table>';

	echo '<td style="width:50%" valign=top><table class=selection width=100%>';
	if($_SESSION['ExistingPOOrder'] != 0 and $_SESSION['PO'.$identifier]->Stat == PurchOrder::STATUS_PRINTED){
		echo '<tr><td><a href="' .$rootpath . "/GoodsReceived.php?" . SID . "&PONumber=" .
			$_SESSION['PO'.$identifier]->OrderNo . "&identifier=".$identifier.'">'._('Receive this order').'</a></td></tr>';
	}
	echo '<td>' . _('Status') . ' :  </td><td><select name=Stat onChange="ReloadForm(form1.UpdateStat)">';

	switch ($_SESSION['PO'.$identifier]->Stat) {
		case '':
			$StatusList = array(PurchOrder::STATUS_NEW_ORDER);
			break;
		case PurchOrder::STATUS_PENDING:
			$StatusList = array(PurchOrder::STATUS_PENDING, PurchOrder::STATUS_AUTHORISED,
                                PurchOrder::STATUS_REJECTED, PurchOrder::STATUS_CANCELLED);
			break;
		case PurchOrder::STATUS_AUTHORISED:
			$StatusList = array(PurchOrder::STATUS_PENDING, PurchOrder::STATUS_AUTHORISED,
                                PurchOrder::STATUS_CANCELLED);
			break;
		case PurchOrder::STATUS_REJECTED:
			$StatusList = array(PurchOrder::STATUS_PENDING, PurchOrder::STATUS_AUTHORISED,
                                PurchOrder::STATUS_REJECTED, PurchOrder::STATUS_CANCELLED);
			break;
		case PurchOrder::STATUS_CANCELLED:
			$StatusList = array(PurchOrder::STATUS_PENDING, PurchOrder::STATUS_CANCELLED);
			break;
		case PurchOrder::STATUS_PRINTED:
			$StatusList = array(PurchOrder::STATUS_PENDING, PurchOrder::STATUS_PRINTED,
                                PurchOrder::STATUS_CANCELLED);
			break;
		case PurchOrder::STATUS_COMPLITED:
			$StatusList = array(PurchOrder::STATUS_COMPLITED);
			break;
		default:
			$StatusList = array(PurchOrder::STATUS_NEW_ORDER, PurchOrder::STATUS_PENDING,
                                PurchOrder::STATUS_AUTHORISED, PurchOrder::STATUS_REJECTED,
                                PurchOrder::STATUS_CANCELLED);
			break;
	}

	foreach ($StatusList as $Status) {
		if ($_SESSION['PO'.$identifier]->Stat == $Status){
			echo '<option selected value="' . $Status . '">' . _($Status) . '</option>';
		} else {
			echo '<option value="'.$Status.'">' . _($Status) . '</option>';
		}
	}
	echo '</select></td></tr>';

	echo '<tr><td>' . _('Status Comment');
	echo ":</td><td><input type=text name='StatComments' size=50></td></tr><tr><td colspan=2><b>" . html_entity_decode($_SESSION['PO'.$identifier]->StatComments) .'</b></td></tr>';
	echo "<input type=hidden name='statcommentscomplete' value='".$_SESSION['PO'.$identifier]->StatComments."'>";
	echo '<tr>
	       <td><input type="submit" name=UpdateStat value="' . _("Status Update") .'"></td>';
	        echo "<td><input type='submit' name='CancelOrder' value='" . _("Cancel and Delete Order") . "'></td>
	     </tr>";
    if($_SESSION['DatabaseName'] == "armedica_erp_001"){ //Solo se Usa para armedica_erp_001
        echo "<tr>";
        echo "<td>" . _('Facturar desde:') . "</td>";
        echo "<td>";
    ?>
    <select name='StkLocation' >
        <?php
        foreach($_SESSION['rh_permitionlocation'] as $key=>$value){
            if (($_POST['StkLocation']==$key)){
                echo "<OPTION SELECTED Value='$key'>$value";
            } else {
                echo "<OPTION Value='$key'>$value";
            }
        } ?>
    </select>
    <?php
        echo "</td>";
        echo "</tr>";
    } //Solo se Usa para armedica_erp_001
	echo "</table></td>";

// end of move by Hudson

	echo '<table width=80%>
		<tr>
		<th><font color=blue size=4><b>' . _('Direccion de Entrega') . '</b></font></th>
		<!--	<th><font color=blue size=4><b>' . _('Delivery To') . '</b></font></th> -->
			<th><font color=blue size=4><b>' . _('Supplier Info') . '</b></font></th>
		</tr>
		<tr><td valign=top>';
	/*nested table level1 */

	if($_SESSION['DatabaseName'] == "armedica_erp_001"){
	    $SelectLocationName = "StkLocationSnd";
	}else{
	    $SelectLocationName = "StkLocation";
	}
	echo '<table class=selection width=100%><tr><td>' . _('Warehouse') . ':</td>
			<td><select name="' . $SelectLocationName . '" onChange="ReloadForm(form1.LookupDeliveryAddress)">';


    if($_SESSION['DatabaseName'] == "armedica_erp_001"){
        //*************RH Para armedica_erp_001 se seleccionan Direcciones de Entrega*************
        $sql = "SELECT loccode,
                    locationname
            FROM rh_direcciones_entrega";
        $LocnResult = DB_query($sql,$db);
    
        while ($LocnRow=DB_fetch_array($LocnResult)){
            if (isset($_POST['StkLocationSnd']) and ($_POST['StkLocationSnd'] == $LocnRow['loccode'] OR
                    ($_POST['StkLocationSnd']=='' AND $LocnRow['loccode']==$_SESSION['UserStockLocation'])) ){
                echo "<option selected value='" . $LocnRow['loccode'] . "'>" . $LocnRow['locationname'];
            } else {
                echo "<option value='" . $LocnRow['loccode'] . "'>" . $LocnRow['locationname'];
            }
        }
    }else{
        //*************RH Seleccion de Almacenes permitidos para el usuario*************
        foreach($_SESSION['rh_permitionlocation'] as $key=>$value){
            if (($_POST['StkLocation']==$key)){
                echo "<OPTION SELECTED Value='$key'>$value";
            } else {
                echo "<OPTION Value='$key'>$value";
            }
        }
    }


	echo '</select>
		<input type="submit" name="LookupDeliveryAddress" value="' ._('Select') . '"></td>
		</tr>';

/* If this is the first time
 * the form loaded set up defaults */

	if (!isset($_POST[$SelectLocationName]) OR $_POST[$SelectLocationName]==''){

		$_POST[$SelectLocationName] = $_SESSION['UserStockLocation'];

		$sql = "SELECT deladd1,
	 			deladd2,
				deladd3,
				deladd4,
				deladd5,
				deladd6,
				tel,
				contact
			FROM locations
			WHERE loccode='" . $_POST[$SelectLocationName] . "'";

		$LocnAddrResult = DB_query($sql,$db);
		if (DB_num_rows($LocnAddrResult)==1){
			$LocnRow = DB_fetch_row($LocnAddrResult);
			$_POST['DelAdd1'] = $LocnRow[0];
			$_POST['DelAdd2'] = $LocnRow[1];
			$_POST['DelAdd3'] = $LocnRow[2];
			$_POST['DelAdd4'] = $LocnRow[3];
			$_POST['DelAdd5'] = $LocnRow[4];
			$_POST['DelAdd6'] = $LocnRow[5];
			$_POST['tel'] = $LocnRow[6];
			$_POST['Contact'] = $LocnRow[7];

			$_SESSION['PO'.$identifier]->Location= $_POST['StkLocation'];
//			$_SESSION['PO'.$identifier]->SupplierContact= $_POST['SupplierContact'];
			$_SESSION['PO'.$identifier]->DelAdd1 = $_POST['DelAdd1'];
			$_SESSION['PO'.$identifier]->DelAdd2 = $_POST['DelAdd2'];
			$_SESSION['PO'.$identifier]->DelAdd3 = $_POST['DelAdd3'];
			$_SESSION['PO'.$identifier]->DelAdd4 = $_POST['DelAdd4'];
			$_SESSION['PO'.$identifier]->DelAdd5 = $_POST['DelAdd5'];
			$_SESSION['PO'.$identifier]->DelAdd6 = $_POST['DelAdd6'];
			$_SESSION['PO'.$identifier]->tel = $_POST['tel'];
			$_SESSION['PO'.$identifier]->contact = $_POST['Contact'];

		} else {
			 /*The default location of the user is crook */
			prnMsg(_('The default stock location set up for this user is not a currently defined stock location') .
				'. ' . _('Your system administrator needs to amend your user record'),'error');
		}


	} elseif (isset($_POST['LookupDeliveryAddress'])){
        ////Para armedica_erp_001 se usa la Tabla rh_direcciones_entrega
        if($_SESSION['DatabaseName'] == "armedica_erp_001"){
            $AddressTable = "rh_direcciones_entrega";
        }else{
            $AddressTable = "locations";
        }
        
		$sql = "SELECT deladd1,
				deladd2,
				deladd3,
				deladd4,
				deladd5,
				deladd6,
				tel,
				contact
			FROM " . $AddressTable . "
			WHERE loccode ='" . $_POST[$SelectLocationName] . "'";

		$LocnAddrResult = DB_query($sql,$db);
		if (DB_num_rows($LocnAddrResult)==1){
			$LocnRow = DB_fetch_row($LocnAddrResult);
			$_POST['DelAdd1'] = $LocnRow[0];
			$_POST['DelAdd2'] = $LocnRow[1];
			$_POST['DelAdd3'] = $LocnRow[2];
			$_POST['DelAdd4'] = $LocnRow[3];
			$_POST['DelAdd5'] = $LocnRow[4];
			$_POST['DelAdd6'] = $LocnRow[5];
			$_POST['tel'] = $LocnRow[6];
			$_POST['Contact'] = $LocnRow[7];

			$_SESSION['PO'.$identifier]->Location= $_POST['StkLocation'];
			$_SESSION['PO'.$identifier]->DelAdd1 = $_POST['DelAdd1'];
			$_SESSION['PO'.$identifier]->DelAdd2 = $_POST['DelAdd2'];
			$_SESSION['PO'.$identifier]->DelAdd3 = $_POST['DelAdd3'];
			$_SESSION['PO'.$identifier]->DelAdd4 = $_POST['DelAdd4'];
			$_SESSION['PO'.$identifier]->DelAdd5 = $_POST['DelAdd5'];
			$_SESSION['PO'.$identifier]->DelAdd6 = $_POST['DelAdd6'];
			$_SESSION['PO'.$identifier]->tel = $_POST['tel'];
			$_SESSION['PO'.$identifier]->contact = $_POST['Contact'];
		}
	}


	echo '<tr><td>' . _('Delivery Contact') . ":</td>
		<td><input type='text' name=Contact size=41  value='" . $_SESSION['PO'.$identifier]->contact . "'></td>
		</tr>";
	echo '<tr><td>' . _('Address') . " 1 :</td>
		<td><input type='text' name=DelAdd1 size=41 maxlength=40 value='" . $_POST['DelAdd1'] . "'></td>
		</tr>";
	echo '<tr><td>' . _('Address') . " 2 :</td>
		<td><input type='text' name=DelAdd2 size=41 maxlength=40 value='" . $_POST['DelAdd2'] . "'></td>
		</tr>";
	echo '<tr><td>' . _('Address') . " 3 :</td>
		<td><input type='text' name=DelAdd3 size=41 maxlength=40 value='" . $_POST['DelAdd3'] . "'></td>
		</tr>";
	echo '<tr><td>' . _('Address') . " 4 :</td>
		<td><input type='text' name=DelAdd4 size=21 maxlength=20 value='" . $_POST['DelAdd4'] . "'></td>
		</tr>";
	echo '<tr><td>' . _('Address') . " 5 :</td>
		<td><input type='text' name=DelAdd5 size=16 maxlength=15 value='" . $_POST['DelAdd5'] . "'></td>
		</tr>";
	echo '<tr><td>' . _('Address') . " 6 :</td>
		<td><input type='text' name=DelAdd6 size=16 maxlength=15 value='" . $_POST['DelAdd6'] . "'></td>
		</tr>";
	echo '<tr><td>' . _('Phone') . ":</td>
		<td><input type='text' name=tel size=31 maxlength=30 value='" . $_SESSION['PO'.$identifier]->tel . "'></td>
		</tr>";

	echo '<tr><td>' . _('Delivery By') . ':</td><td><select name=deliveryby>';

	$sql = "SELECT shipper_id, shippername FROM shippers";
	$shipperResult = DB_query($sql,$db);

	while ($shipperRow=DB_fetch_array($shipperResult)){
		if (isset($_POST['deliveryby']) and ($_POST['deliveryby'] == $shipperRow['shipper_id'])) {
			echo "<option selected value='" . $shipperRow['shipper_id'] . "'>" . $shipperRow['shippername'];
		} else {
			echo "<option value='" . $shipperRow['shipper_id'] . "'>" . $shipperRow['shippername'];
		}
	}

	echo '</select></tr></table>';
	  /* end of sub table */

	echo '</td><td>'; /*sub table nested */
	echo '<table class=selection width=100%><tr><td>' . _('Supplier Selection') . ':</td><td>
		<select name=Keywords onChange="ReloadForm(form1.SearchSuppliers)">';

	$sql = "SELECT supplierid,suppname FROM suppliers ORDER BY suppname";
	$SuppCoResult = DB_query($sql,$db);

	while ( $SuppCoRow=DB_fetch_array($SuppCoResult)){
		if ($SuppCoRow['suppname'] == $_SESSION['PO'.$identifier]->SupplierName) {
			echo "<option selected value='" . $SuppCoRow['suppname'] . "'>" . $SuppCoRow['suppname'];
		} else {
			echo "<option value='" . $SuppCoRow['suppname'] . "'>" . $SuppCoRow['suppname'];
		}
	}

	echo '</select> ';
	echo '<input type="submit" name="SearchSuppliers" value=' . _('Select Now') . '"></td></tr>';

// END of added <input type=submit action=RESET VALUE="' . _('Reset')

	echo '</td></tr><tr><td>' . _('Supplier Contact') . ':</td><td>
		<select name=SupplierContact>';

	$sql = "SELECT contact FROM suppliercontacts WHERE supplierid='". $_POST['Select'] ."'";

	$SuppCoResult = DB_query($sql,$db);

	while ( $SuppCoRow=DB_fetch_array($SuppCoResult)){
		if ($_POST['SupplierContact'] == $SuppCoRow['contact'] OR ($_POST['SupplierContact']==''
			AND $SuppCoRow['contact']==$_SESSION['PO'.$identifier]->SupplierContact)){
			//if (1) {
			echo "<option selected value='" . $SuppCoRow['contact'] . "'>" . $SuppCoRow['contact'];
		} else {
			echo "<option value='" . $SuppCoRow['contact'] . "'>" . $SuppCoRow['contact'];
		}
	}

	echo '</select> ';
	echo '</td></tr>';

	echo '<tr><td>' . _('Address') . " 1 :</td>
		</td><td><input type='text' name=suppDelAdd1 size=41 maxlength=40 value='" . $_POST['suppDelAdd1'] . "'></td>
		</tr>";
	echo '<tr><td>' . _('Address') . " 2 :</td>
		</td><td><input type='text' name=suppDelAdd2 size=41 maxlength=40 value='" . $_POST['suppDelAdd2'] . "'></td>
		</tr>";
	echo '<tr><td>' . _('Address') . " 3 :</td>
		</td><td><input type='text' name=suppDelAdd3 size=41 maxlength=40 value='" . $_POST['suppDelAdd3'] . "'></td>
		</tr>";
	echo '<tr><td>' . _('Address') . " 4 :</td>
		</td><td><input type='text' name=suppDelAdd5 size=21 maxlength=20 value='" . $_POST['suppDelAdd5'] . "'></td>
		</tr>";
	echo '<tr><td>' . _('Address') . " 5 :</td>
		</td><td><input type='text' name=suppDelAdd4 size=41 maxlength=40 value='" . $_POST['suppDelAdd4'] . "'></td>
		</tr>";
	echo '<tr><td>' . _('Phone') . ":
		</td><td><input type='text' name=supptel size=31 maxlength=30 value='" . $_SESSION['PO'.$identifier]->supptel  . "'></td>
		</tr>";

	$result=DB_query("SELECT terms, termsindicator FROM paymentterms", $db);

	echo '<tr><td>' . _('Payment Terms') . ":</td><td><select name='paymentterms'>";

	while ($myrow = DB_fetch_array($result)) {
		if ($myrow['termsindicator']==$_SESSION['PO'.$identifier]->paymentterms) {
			echo "<option selected value='". $myrow['termsindicator'] . "'>" . $myrow['terms'];
		} else {
			echo "<option value='". $myrow['termsindicator'] . "'>" . $myrow['terms'];
		} //end while loop
	}
	DB_data_seek($result, 0);
	echo '</select></td></tr>';

	$result=DB_query("SELECT loccode, locationname FROM locations WHERE loccode='" . $_SESSION['PO'.$identifier]->port."'", $db);
	$myrow = DB_fetch_array($result);
	$_POST['port'] = $myrow['locationname'];

	echo '<tr><td>' . _('Delivery To') . ":
		</td><td><input type='text' name=port size=31 value='" . $_POST['port'] . "'></td>
		</tr>";

	if ($_SESSION['PO'.$identifier]->CurrCode != $_SESSION['CompanyRecord']['currencydefault']) {
		echo '<tr><td>'. _('Exchange Rate').':'.'</td><td><input type=text name="ExRate"
		value='.$_POST['ExRate'].' class=number size=11></td></tr>';
	} else {
		echo '<input type=hidden name="ExRate" value="1">';
	}
	echo '</td></tr></table>'; /*end of sub table */

	echo '</td></tr><tr><th colspan=4><font color=blue size=4><b>' . _('Comments');

	$Default_Comments = '';

	if (!isset($_POST['Comments'])) {
		$_POST['Comments']=$Default_Comments;
	}

	echo ":</b></font></th></tr><tr><td colspan=4><textarea name='Comments' style='width:100%' rows=5>" . $_POST['Comments'] . '</textarea>';

	echo '</table>';

	echo '</td></tr></table><br>'; /* end of main table */
	// discard change supplier submit buttom
	// kept enter line item but remove Delete button by Hudson 11/16,and added status field
	echo "<div class='centre'>
  		<input type=submit name='EnterLines' value='" . _('Enter Line Items') . "'></div>";
	// Delete PO when necessrary

} /*end of if supplier selected */

echo '</form>';
include('includes/footer.inc');
?>