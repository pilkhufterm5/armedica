<?php
/* $Revision: 320 $ */
/* $Revision: 320 $ */

$PageSecurity = 2;

include('includes/DefineCartClass.php');
/* Session started in header.inc for password checking and authorisation level check */
include('includes/session.inc');

if (isset($_GET['OrderNumber'])) {
	$title = _('Reviewing Sales Order Number') . ' ' . $_GET['OrderNumber'];
} else {
	include('includes/header.inc');
	echo '<BR><BR><BR>';
	prnMsg(_('This page must be called with a sales order number to review') . '.<BR>' . _('i.e.') . ' http://????/OrderDetails.php?OrderNumber=<i>xyz</i><BR>' . _('Click on back') . '.','error');
	include('includes/footer.inc');
	exit;
}

include('includes/header.inc');

if (isset($_SESSION['Items'])){
	unset ($_SESSION['Items']->LineItems);
	unset ($_SESSION['Items']);
}

$_SESSION['Items'] = new cart;

/*read in all the guff from the selected order into the Items cart  */


$OrderHeaderSQL = 'SELECT
			salesorders.debtorno,
			debtorsmaster.name,
			salesorders.branchcode,
			salesorders.customerref,
			salesorders.comments,
			salesorders.orddate,
			salesorders.ordertype,
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
			salesorders.rh_status,
			debtorsmaster.currcode,
			salesorders.fromstkloc
		FROM
			salesorders,
			debtorsmaster
		WHERE
			salesorders.debtorno = debtorsmaster.debtorno
		AND salesorders.orderno = ' . $_GET['OrderNumber'];

$ErrMsg =  _('The order cannot be retrieved because');
$DbgMsg = _('The SQL that failed to get the order header was');
$GetOrdHdrResult = DB_query($OrderHeaderSQL,$db, $ErrMsg, $DbgMsg);

if (DB_num_rows($GetOrdHdrResult)==1) {

	$myrow = DB_fetch_array($GetOrdHdrResult);

	$_SESSION['CustomerID'] = $myrow['debtorno'];
/*CustomerID defined in header.inc */
	$_SESSION['Items']->Branch = $myrow['branchcode'];
	$_SESSION['Items']->CustomerName = $myrow['name'];
	$_SESSION['Items']->CustRef = $myrow['customerref'];
	$_SESSION['Items']->Comments = $myrow['comments'];

	$_SESSION['Items']->DefaultSalesType =$myrow['ordertype'];
	$_SESSION['Items']->DefaultCurrency = $myrow['currcode'];
	$BestShipper = $myrow['shipvia'];
	$_SESSION['Items']->DeliverTo = $myrow['deliverto'];
	$_SESSION['Items']->DeliveryDate = ConvertSQLDate($myrow['deliverydate']);
	$_SESSION['Items']->BrAdd1 = $myrow['deladd1'];
	$_SESSION['Items']->BrAdd2 = $myrow['deladd2'];
	$_SESSION['Items']->BrAdd3 = $myrow['deladd3'];
	$_SESSION['Items']->BrAdd4 = $myrow['deladd4'];
	$_SESSION['Items']->BrAdd5 = $myrow['deladd5'];
	$_SESSION['Items']->BrAdd6 = $myrow['deladd6'];
	$_SESSION['Items']->PhoneNo = $myrow['contactphone'];
	$_SESSION['Items']->Email = $myrow['contactemail'];
	$_SESSION['Items']->Location = $myrow['fromstkloc'];
	$FreightCost = $myrow['freightcost'];
	$_SESSION['Items']->Orig_OrderDate = $myrow['orddate'];
}

	/* SHOW ALL THE ORDER INFO IN ONE PLACE */
	// bowikaxu realhost - may 2007 - show visible alert if cancelled
	if($myrow['rh_status']==1){ // cancelado

		echo "<CENTER><H1><FONT COLOR=red>"._('This sales order has been cancelled as requested')."</FONT></H1></CENTER>";
		
	}
	
	echo '<BR><BR><CENTER><TABLE BGCOLOR="#CCCCCC">';
	echo '<TR>
		<TD class="tableheader">' . _('Customer Code') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B><A HREF="' . $rootpath . '/SelectCustomer.php?Select=' . $_SESSION['CustomerID'] . '">' . $_SESSION['CustomerID'] . '</A></B></TD>
		<TD class="tableheader">' . _('Customer Name') . ':</TD><TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . $myrow['name'] . '</B></TD>
	</TR>';
	echo '<TR>
		<TD class="tableheader">' . _('Customer Reference') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . $myrow['customerref'] . '</FONT></B></TD>
		<TD class="tableheader">' . _('Deliver To') . ':</TD><TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . $myrow['deliverto'] . '</B></TD>
	</TR>';
	echo '<TR>
		<TD class="tableheader">' . _('Ordered On') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . ConvertSQLDate($myrow['orddate']) . '</FONT></B></TD>
		<TD class="tableheader">' . _('Delivery Address 1') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . $myrow['deladd1'] . '</FONT></B></TD>
	</TR>';
	echo '<TR>
		<TD class="tableheader">' . _('Requested Delivery') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . ConvertSQLDate($myrow['deliverydate']) . '</FONT></B></TD>
		<TD class="tableheader">' . _('Delivery Address 2') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . $myrow['deladd2'] . '</FONT></B></TD>
	</TR>';
	echo '<TR>
		<TD class="tableheader">' . _('Order Currency') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . $myrow['currcode'] . '</FONT></B></TD>
		<TD class="tableheader">' . _('Delivery Address 3') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . $myrow['deladd3'] . '</FONT></B></TD>
	</TR>';
	echo '<TR>
		<TD class="tableheader">' . _('Deliver From Location') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . $myrow['fromstkloc'] . '</FONT></B></TD>
		<TD class="tableheader">' . _('Delivery Address 4') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . $myrow['deladd4'] . '</FONT></B></TD>
	</TR>';
	echo '<TR>
		<TD class="tableheader">' . _('Telephone') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . $myrow['contactphone'] . '</FONT></B></TD>
		<TD class="tableheader">' . _('Delivery Address 5') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . $myrow['deladd5'] . '</FONT></B></TD>
	</TR>';
	echo '<TR>
		<TD class="tableheader">' . _('Email') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B><A HREF="mailto:' . $myrow['contactemail'] . '">' . $myrow['contactemail'] . '</A></FONT></B></TD>
		<TD class="tableheader">' . _('Delivery Address 6') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . $myrow['deladd6'] . '</FONT></B></TD>
	</TR>';
	echo '<TR>
		<TD class="tableheader">' . _('Freight Cost') . ':</TD>
		<TD bgcolor="#CCCCCC"><FONT COLOR=BLUE><B>' . $myrow['freightcost'] . '</FONT></B></TD>
	</TR>';
	echo '</TABLE>';        

        //iJPe  2010-03-08  Modificacion en la expresion regular se añadio el OR para Rem, ya que no realizaba correctamente el obtener el numero de la remision
	$invoices = preg_split("/((Inv)|(Rem) \n*)/", $_SESSION['Items']->Comments);

	foreach($invoices as $inv){
		if (trim($inv)!=''){
			// bowikaxu realhost - june 2007 - show external invoice
			/*
			$sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations 
			WHERE rh_invoicesreference.intinvoice = ".$inv."
			AND locations.loccode = rh_invoicesreference.loccode";
			
			$res = DB_query($sql,$db);
			$info = DB_fetch_array($res);
			*/
			echo '<A HREF="' . $rootpath. '/rh_PrintCustTrans.php?FromTransNo=' . $inv . '&InvOrCredit=Invoice&PrintPDF=Yes" target=_blank>' . _('Inv') . '# '. $inv. '</A><BR>';
		}
	}
	echo _('Comments:').' '.$_SESSION['Items']->Comments . '<BR></CENTER>';

/*Now get the line items */
// bowikaxu realhost March 2008 - show item narrative
// bowikaxu realhost 9 july 2008 - get item decription from saved one, not actual
	$LineItemsSQL = 'SELECT
				stkcode,
				stockmaster.description AS description2,
				stockmaster.volume,
				stockmaster.kgs,
				stockmaster.decimalplaces,
				stockmaster.mbflag,
				stockmaster.units,
				stockmaster.discountcategory,
				stockmaster.controlled,
				stockmaster.serialised,
				
				salesorderdetails.narrative,
				salesorderdetails.description,
				
				unitprice,
				quantity,
				discountpercent,
				actualdispatchdate,
				qtyinvoiced
			FROM salesorderdetails, stockmaster
			WHERE salesorderdetails.stkcode = stockmaster.stockid AND orderno =' . $_GET['OrderNumber'];

	$ErrMsg =  _('The line items of the order cannot be retrieved because');
	$DbgMsg =  _('The SQL used to retrieve the line items, that failed was');
	$LineItemsResult = db_query($LineItemsSQL,$db, $ErrMsg, $DbgMsg);
                                                                                                              																																
	if (db_num_rows($LineItemsResult)>0) {
		
		$OrderTotal = 0;
		$OrderTotalVolume = 0;
		$OrderTotalWeight = 0;

		echo '</BR><CENTER><B>' . _('Line Details') . '</B>
			<TABLE CELLPADDING=2 COLSPAN=9 BORDER=1>
			<TR>
			<TD class="tableheader">' . _('Item Code') . '</TD>
			<TD class="tableheader">' . _('Item Description') . '</TD>
			<TD class="tableheader">' . _('Quantity') . '</TD>
			<TD class="tableheader">' . _('Unit') . '</TD>
			<TD class="tableheader">' . _('Price') . '</TD>
			<TD class="tableheader">' . _('Discount') . '</TD>
			<TD class="tableheader">' . _('Total') . '</TD>
			<TD class="tableheader">' . _('Qty Del') . '</TD>
			<TD class="tableheader">' . _('Last Del') . '</TD>
			<TD class="tableheader">' . _('Narrative') . '</TD>
			</TR>';

		while ($myrow=db_fetch_array($LineItemsResult)) {

			if ($k==1){
				echo '<tr bgcolor="#CCCCCC">';
				$k=0;
			} else {
				echo '<tr bgcolor="#EEEEEE">';
				$k=1;
			}

			if ($StockItem->QtyInv>0){
			  $DisplayActualDeliveryDate = ConvertSQLDate($myrow['actualdispatchdate']);
			} else {
		  	$DisplayActualDeliveryDate = _('N/A');
			}

			echo 	'<TD>' . $myrow['stkcode'] . '</TD>
				<TD>' . $myrow['description'] . '</TD>
				<TD ALIGN=RIGHT>' . $myrow['quantity'] . '</TD>
				<TD>' . $myrow['units'] . '</TD>
				<TD ALIGN=RIGHT>' . number_format($myrow['unitprice'],2) . '</TD>
				<TD ALIGN=RIGHT>' . number_format(($myrow['discountpercent'] * 100),2) . '%' . '</TD>
				<TD ALIGN=RIGHT>' . number_format($myrow['quantity'] * $myrow['unitprice'] * (1 - $myrow['discountpercent']),2) . '</TD>
				<TD ALIGN=RIGHT>' . number_format($myrow['qtyinvoiced'],2) . '</TD>
				<TD>' . $DisplayActualDeliveryDate . '</TD>
				<TD>' . $myrow['narrative'] . '</TD>
			</TR>';
			
			$OrderTotal = $OrderTotal + $myrow['quantity'] * $myrow['unitprice'] * (1 - $myrow['discountpercent']);
			$OrderTotalVolume = $OrderTotalVolume + $myrow['quantity'] * $myrow['volume'];
			$OrderTotalWeight = $OrderTotalWeight + $myrow['quantity'] * $myrow['kgs'];
			
		}
		$DisplayTotal = number_format($OrderTotal,2);
		$DisplayVolume = number_format($OrderTotalVolume,2);
		$DisplayWeight = number_format($OrderTotalWeight,2);
		
		echo '<TR>
			<TD COLSPAN=5 ALIGN=RIGHT><B>' . _('TOTAL Excl Tax/Freight') . '</B></TD>
			<TD COLSPAN=2 ALIGN=RIGHT>' . $DisplayTotal . '</TD>
			</TR>
		</TABLE>';
		
		echo '<TABLE BORDER=1>
			<TR>
				<TD>' . _('Total Weight') . ':</TD>
				<TD>' . $DisplayWeight . '</TD>
				<TD>' . _('Total Volume') . ':</TD>
				<TD>' . $DisplayVolume . '</TD>
			</TR>
		</TABLE>';
	}
/**************************************************************************
* Jorge Garcia
* 21/Nov/2008 Tabla de logs
* rleal Jul 2010 se agrega
***************************************************************************/
echo "<BR>";
echo "<STRONG>"._('Logs')."</STRONG><BR><TABLE BORDER=1>
<TR>
<TD class='tableheader'></TD>
<TD class='tableheader'>"._('Usuario')."</TD>
<TD class='tableheader'>"._('Fecha / Hora')."</TD>
<TD class='tableheader'>"._('Realiz&oacute;')."</TD>
</TR>";
$sqllogs = "SELECT user_, date_ FROM rh_usertrans WHERE order_ = ".$_GET['OrderNumber']." AND type = 30";
$resultlogs = DB_query($sqllogs,$db);
$rowlogs = DB_fetch_array($resultlogs);
echo "<TR bgcolor='#EEEEEE'>
<TD>"._('Cre&oacute;')."</TD>
<TD>".$rowlogs['user_']."</TD>
<TD>".$rowlogs['date_']."</TD>
<TD>"._('Cre&oacute; Pedido')."</TD>
</TR>";
$k=1;
$sqllogs = "SELECT user, date, realizo FROM rh_translogs WHERE typeno = ".$_GET['OrderNumber']." AND type = 30";
$resultlogs = DB_query($sqllogs,$db);
while($rowlogs = DB_fetch_array($resultlogs)){
	if ($k==1){
		echo "<TR bgcolor='#CCCCCC'>";
		$k=0;
	} else {
		echo "<TR bgcolor='#EEEEEE'>";
		$k=1;
	}
	if($rowlogs['realizo']=='MP'){$rh_realizo=_('Modific&oacute; Pedido');}
	if($rowlogs['realizo']=='IA'){$rh_realizo=_('Agreg&oacute; Art&iacute;culo');}
	if($rowlogs['realizo']=='UA'){$rh_realizo=_('Actualiz&oacute; Art&iacute;culo');}
	if($rowlogs['realizo']=='DA'){$rh_realizo=_('Borr&oacute; Art&iacute;culo');}
	echo "
	<TD>"._('Modific&oacute;')."</TD>
	<TD>".$rowlogs['user']."</TD>
	<TD>".$rowlogs['date']."</TD>
	<TD>".$rh_realizo."</TD>
	</TR>";
}
echo "</TABLE>";
/**************************************************************************
* Jorge Garcia Fin Modificacion
***************************************************************************/
	
include('includes/footer.inc');
?>
