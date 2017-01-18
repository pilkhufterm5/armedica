<?php
/* $Revision: 14 $ */
ini_set("display_errors", 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include('includes/DefineCartClass.php');
include('includes/DefinePedimentoItems.php');
$PageSecurity = 11;
include('includes/session.inc');
$title = _('Especifica los # de pedimento');

/* Session started in header.inc for password checking and authorisation level check */
include('includes/header.inc');

if (isset($_GET['LineNo'])){
        $LineNo = $_GET['LineNo'];
} elseif (isset($_POST['LineNo'])){
        $LineNo = $_POST['LineNo'];
} else {
	echo '<CENTER><A HREF="' . $rootpath . '/ConfirmDispatch_Invoice.php?' . SID . '">'.
		_('Select a line item to invoice').'</A><br>';
	echo '<BR>';
	prnMsg( _('This page can only be opened if a line item on a sales order to be invoiced has been selected') . '. ' . _('Please do that first'),'error');
	echo '</CENTER>';
	include('includes/footer.inc');
	exit;
}

if (!isset($_SESSION['Items']) OR !isset($_SESSION['ProcessingOrder'])) {
	/* This page can only be called with a sales order number to invoice */
	echo '<CENTER><A HREF="' . $rootpath . '/SelectSalesOrder.php?' . SID . '">'. _('Select a sales order to invoice').
		'</A><br>';
	prnMsg( _('This page can only be opened if a sales order and line item has been selected Please do that first'),'error');
	echo '</CENTER>';
	include('includes/footer.inc');
	exit;
}


/*Save some typing by referring to the line item class object in short form */
$LineItem = &$_SESSION['Items']->LineItems[$LineNo];


//Make sure this item is really controlled
if ( $LineItem->Controlled != 1 && false ){
	echo '<CENTER><A HREF="' . $rootpath . '/ConfirmDispatch_Invoice.php?' . SID . '">'. _('Back to the Sales Order'). '</A></CENTER>';
	echo '<BR>';
	prnMsg( _('The line item must be defined as controlled to require input of the batch numbers or serial numbers being sold'),'error');
	include('includes/footer.inc');
	exit;
}

/********************************************
  Get the page going....
********************************************/
echo '<CENTER>';

echo '<BR><A HREF="'. $rootpath. '/ConfirmDispatch_Invoice.php?' . SID . '">'. _('Back to Confirmation of Dispatch') . '/' . _('Invoice'). '</a>';

echo '<br><FONT SIZE=2><B>'. _('Dispatch of up to').' '. number_format($LineItem->Quantity-$LineItem->QtyInv, $LineItem->DecimalPlaces). ' '. _('Controlled items').' ' . $LineItem->StockID  . ' - ' . $LineItem->ItemDescription . ' '. _('on order').' ' . $_SESSION['Items']->OrderNo . ' '. _('to'). ' ' . $_SESSION['Items']->CustomerName . '</B></FONT>';

/** vars needed by InputSerialItem : **/
$Invoice = true;
$StockID = $LineItem->StockID;
$RecvQty = $LineItem->Quantity-$LineItem->QtyInv;
$ItemMustExist = true;  /*Can only invoice valid batches/serial numbered items that exist */
$LocationOut = $_SESSION['Items']->Location;
$InOutModifier=1;
$ShowExisting=false;

include ('includes/InputPedimentoItems.php');

/*TotalQuantity set inside this include file from the sum of the bundles
of the item selected for dispatch */
$_SESSION['Items']->LineItems[$LineNo]->QtyDispatched = $TotalQuantity;

include('includes/footer.inc');
exit;
?>