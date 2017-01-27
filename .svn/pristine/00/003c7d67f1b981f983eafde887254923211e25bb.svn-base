<?php

/* $Revision: 390 $ */

/*
This is where the delivery details are confirmed/entered/modified and the order committed to the database once the place order/modify order button is hit.
*/

include('includes/DefineCartClass.php');

/* Session started in header.inc for password checking the session will contain the details of the order from the Cart class object. The details of the order come from SelectOrderItems.php 			*/

$PageSecurity=1;
include('includes/session.inc');
$title = _('Order Delivery Details');
include('includes/header.inc');
include('includes/FreightCalculation.inc');
include('includes/SQL_CommonFunctions.inc');


echo '<A HREF="'. $rootpath . '/SelectSalesOrder.php?' . SID . '">'. _('Back to Sales Orders'). '</A><BR>';

if (!isset($_SESSION['Items']) OR !isset($_SESSION['Items']->DebtorNo)){
	prnMsg(_('This page can only be read if an order has been entered') . '. ' . _('To enter an order select customer transactions then sales order entry'),'error');
	include('includes/footer.inc');
	exit;
}

If ($_SESSION['Items']->ItemsOrdered == 0){
	prnMsg(_('This page can only be read if an there are items on the order') . '. ' . _('To enter an order select customer transactions, then sales order entry'),'error');
	include('includes/footer.inc');
	exit;
}
//var_dump($_SESSION['Items']);
/*Calculate the earliest dispacth date in DateFunctions.inc */

$EarliestDispatch = CalcEarliestDispatchDate();

If (isset($_POST['ProcessOrder']) OR isset($_POST['MakeRecurringOrder'])) {

	/*need to check for input errors in any case before order processed */
	$_POST['Update']='Yes rerun the validation checks';

	/*store the old freight cost before it is recalculated to ensure that there has been no change - test for change after freight recalculated and get user to re-confirm if changed */

	$OldFreightCost = round($_POST['FreightCost'],2);

}

If (isset($_POST['Update'])
	OR isset($_POST['BackToLineDetails'])
	OR isset($_POST['MakeRecurringOrder']))   {

	$InputErrors =0;
	If (strlen($_POST['DeliverTo'])<=1){
		$InputErrors =1;
		prnMsg(_('You must enter the person or company to whom delivery should be made'),'error');
	}
	If (strlen($_POST['BrAdd1'])<=1){
		$InputErrors =1;
		prnMsg(_('You should enter the street address in the box provided') . '. ' . _('Orders cannot be accepted without a valid street address'),'error');
	}
	If (strpos($_POST['BrAdd1'],_('Box'))>0){
		prnMsg(_('You have entered the word') . ' "' . _('Box') . '" ' . _('in the street address') . '. ' . _('Items cannot be delivered to') . ' ' ._('box') . ' ' . _('addresses'),'warn');
	}
	If (!is_numeric($_POST['FreightCost'])){
		$InputErrors =1;
		prnMsg( _('The freight cost entered is expected to be numeric'),'error');
	}
	if (isset($_POST['MakeRecurringOrder']) AND $_POST['Quotation']==1){
		$InputErrors =1;
		prnMsg( _('A recurring order cannot be made from a quotation'),'error');
	}
	If (($_POST['DeliverBlind'])<=0){
		$InputErrors =1;
		prnMsg(_('You must select the type of packlist to print'),'error');
	}

/*	If (strlen($_POST['BrAdd3'])==0 OR !isset($_POST['BrAdd3'])){
		$InputErrors =1;
		echo "<BR>A region or city must be entered.<BR>";
	}

	Maybe appropriate in some installations but not here
	If (strlen($_POST['BrAdd2'])<=1){
		$InputErrors =1;
		echo "<BR>You should enter the suburb in the box provided. Orders cannot be accepted without a valid suburb being entered.<BR>";
	}

*/

	If(!Is_Date($_POST['DeliveryDate'])) {
		$InputErrors =1;
		prnMsg(_('An invalid date entry was made') . '. ' . _('The date entry for the despatch date must be in the format') . ' ' . $_SESSION['DefaultDateFormat'],'warn');
	}

	 /* This check is not appropriate where orders need to be entered in retrospectively in some cases this check will be appropriate and this should be uncommented

	 elseif (Date1GreaterThanDate2(Date($_SESSION['DefaultDateFormat'],$EarliestDispatch), $_POST['DeliveryDate'])){
		$InputErrors =1;
		echo '<BR><B>' . _('The delivery details cannot be updated because you are attempting to set the date the order is to be dispatched earlier than is possible. No dispatches are made on Saturday and Sunday. Also, the dispatch cut off time is') .  $_SESSION['DispatchCutOffTime']  . _(':00 hrs. Orders placed after this time will be dispatched the following working day.');
	}

	*/

	If ($InputErrors==0){

		if ($_SESSION['DoFreightCalc']==True){
		      list ($_POST['FreightCost'], $BestShipper) = CalcFreightCost($_SESSION['Items']->total, $_POST['BrAdd2'], $_POST['BrAdd3'], $_SESSION['Items']->totalVolume, $_SESSION['Items']->totalWeight, $_SESSION['Items']->Location, $db);
 		      $_POST['FreightCost'] = round($_POST['FreightCost'],2);
		      $_POST['ShipVia'] = $BestShipper;
		}

		$_SESSION['Items']->DeliverTo = $_POST['DeliverTo'];
		$_SESSION['Items']->DeliveryDate = $_POST['DeliveryDate'];
		$_SESSION['Items']->DelAdd1 = $_POST['BrAdd1'];
		$_SESSION['Items']->DelAdd2 = $_POST['BrAdd2'];
		$_SESSION['Items']->DelAdd3 = $_POST['BrAdd3'];
		$_SESSION['Items']->DelAdd4 = $_POST['BrAdd4'];
		$_SESSION['Items']->DelAdd5 = $_POST['BrAdd5'];
		$_SESSION['Items']->DelAdd6 = $_POST['BrAdd6'];
        $_SESSION['Items']->DelAdd7 = $_POST['BrAdd7'];
        $_SESSION['Items']->DelAdd8 = $_POST['BrAdd8'];
        $_SESSION['Items']->DelAdd9 = $_POST['BrAdd9'];
        $_SESSION['Items']->DelAdd10 = $_POST['BrAdd10'];
		$_SESSION['Items']->PhoneNo =$_POST['PhoneNo'];
		$_SESSION['Items']->Email =$_POST['Email'];
		$_SESSION['Items']->Location = $_POST['Location'];
		$_SESSION['Items']->CustRef = $_POST['CustRef'];
		$_SESSION['Items']->Comments = $_POST['Comments'];
		$_SESSION['Items']->FreightCost = round($_POST['FreightCost'],2);
		$_SESSION['Items']->ShipVia = $_POST['ShipVia'];
		$_SESSION['Items']->Quotation = $_POST['Quotation'];
		$_SESSION['Items']->DeliverBlind = $_POST['DeliverBlind'];

		/*$_SESSION['DoFreightCalc'] is a setting in the config.php file that the user can set to false to turn off freight calculations if necessary */


		/* What to do if the shipper is not calculated using the system
		- first check that the default shipper defined in config.php is in the database
		if so use this
		- then check to see if any shippers are defined at all if not report the error
		and show a link to set them up
		- if shippers defined but the default shipper is bogus then use the first shipper defined
		*/
		if ((!isset($BestShipper) and $BestShipper=='') AND ($_POST['ShipVia']=='' || !isset($_POST['ShipVia']))){
			$SQL =  "SELECT shipper_id FROM shippers WHERE shipper_id=" . $_SESSION['Default_Shipper'];
			$ErrMsg = _('There was a problem testing for the default shipper');
			$TestShipperExists = DB_query($SQL,$db,$ErrMsg);

			if (DB_num_rows($TestShipperExists)==1){

				$BestShipper = $_SESSION['Default_Shipper'];

			} else {

				$SQL =  'SELECT shipper_id FROM shippers';
				$TestShipperExists = DB_query($SQL,$db,$ErrMsg);

				if (DB_num_rows($TestShipperExists)>=1){
					$ShipperReturned = DB_fetch_row($TestShipperExists);
					$BestShipper = $ShipperReturned[0];
				} else {
					prnMsg(_('We have a problem') . ' - ' . _('there are no shippers defined'). '. ' . _('Please use the link below to set up shipping or freight companies') . ', ' . _('the system expects the shipping company to be selected or a default freight company to be used'),'error');
					echo "<A HREF='" . $rootpath . "Shippers.php'>". _('Enter') . '/' . _('Amend Freight Companies') .'</A>';
				}
			}
			if (isset($_SESSION['Items']->ShipVia) AND $_SESSION['Items']->ShipVia!=''){
				$_POST['ShipVia'] = $_SESSION['Items']->ShipVia;
			} else {
				$_POST['ShipVia']=$BestShipper;
			}
		}
	}
}


if(isset($_POST['MakeRecurringOrder']) AND ! $InputErrors){

	echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . '/RecurringSalesOrders.php?' . SID . "&NewRecurringOrder=Yes'>";
	prnMsg(_('You should automatically be forwarded to the entry of recurring order details page') . '. ' . _('If this does not happen') . '(' . _('if the browser does not support META Refresh') . ') ' ."<a href='" . $rootpath . '/RecurringOrders.php?' . SID . "&NewRecurringOrder=Yes'>". _('click here') .'</a> '. _('to continue'),'info');
	include('includes/footer.inc');
	exit;
}


if (isset($_POST['BackToLineDetails']) and $_POST['BackToLineDetails']==_('Modify Order Lines')){

	echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . '/SelectOrderItems.php?' . SID . "'>";
	prnMsg(_('You should automatically be forwarded to the entry of the order line details page') . '. ' . _('If this does not happen') . '(' . _('if the browser does not support META Refresh') . ') ' ."<a href='" . $rootpath . '/SelectOrderItems.php?' . SID . "'>". _('click here') .'</a> '. _('to continue'),'info');
	include('includes/footer.inc');
	exit;

}

If (isset($_POST['ProcessOrder'])) {
	/*Default OK_to_PROCESS to 1 change to 0 later if hit a snag */
	if ($InputErrors ==0) {
		$OK_to_PROCESS = 1;
	}
	If ($_POST['FreightCost'] != $OldFreightCost && $_SESSION['DoFreightCalc']==True){
		$OK_to_PROCESS = 0;
		prnMsg(_('The freight charge has been updated') . '. ' . _('Please reconfirm that the order and the freight charges are acceptable and then confirm the order again if OK') .' <BR> '. _('The new freight cost is') .' ' . $_POST['FreightCost'] . ' ' . _('and the previously calculated freight cost was') .' '. $OldFreightCost,'warn');
	} else {

/*check the customer's payment terms */
		$sql = "SELECT daysbeforedue,
				dayinfollowingmonth
			FROM debtorsmaster,
				paymentterms
			WHERE debtorsmaster.paymentterms=paymentterms.termsindicator
			AND debtorsmaster.debtorno = '" . $_SESSION['Items']->DebtorNo . "'";

		$ErrMsg = _('The customer terms cannot be determined') . '. ' . _('This order cannot be processed because');
		$TermsResult = DB_query($sql,$db,$ErrMsg);


		$myrow = DB_fetch_array($TermsResult);
		if ($myrow['daysbeforedue']==0 && $myrow['dayinfollowingmonth']==0){

/* THIS IS A CASH SALE NEED TO GO OFF TO 3RD PARTY SITE SENDING MERCHANT ACCOUNT DETAILS AND CHECK FOR APPROVAL FROM 3RD PARTY SITE BEFORE CONTINUING TO PROCESS THE ORDER

UNTIL ONLINE CREDIT CARD PROCESSING IS PERFORMED ASSUME OK TO PROCESS

		NOT YET CODED     */

			$OK_to_PROCESS =1;


		} #end if cash sale detected

	} #end if else freight charge not altered
} #end if process order

if (isset($OK_to_PROCESS) and $OK_to_PROCESS == 1 && $_SESSION['ExistingOrder']==0){

/* finally write the order header to the database and then the order line details - a transaction would	be good here */

        /*
         * iJPe
         * realhost
         * 2010-02-13
         *
         * Modificacion por sucursales virtuales
         */

        $sqlVerifyLoc = "SELECT rh_master_loccode FROM rh_locations_virtual WHERE loccode = '".$_SESSION['Items']->Location."'";
        $resVerifyLoc = DB_query($sqlVerifyLoc, $db);

        if (DB_num_rows($resVerifyLoc) > 0){
            $rowVerifyLoc = DB_fetch_array($resVerifyLoc);
            $locSM = $rowVerifyLoc['rh_master_loccode'];
        }else{
            $locSM = $_SESSION['Items']->Location;
        }


	$DelDate = FormatDateforSQL($_SESSION['Items']->DeliveryDate);
	DB_query("BEGIN",$db);
	$OrderNo = GetNextTransNo(30, $db);
	$HeaderSQL = "INSERT INTO salesorders (
				orderno,
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
                deladd7,
                deladd8,
                deladd9,
                deladd10,
				contactphone,
				contactemail,
				freightcost,
				fromstkloc_virtual,
				deliverydate,
				quotation,
                deliverblind,
                fromstkloc,
                salesman)
			VALUES (
				'". $OrderNo . "',
				'" . $_SESSION['Items']->DebtorNo . "',
				'" . $_SESSION['Items']->Branch . "',
				'". DB_escape_string($_SESSION['Items']->CustRef) ."',
				'". DB_escape_string($_SESSION['Items']->Comments) ."',
				'" . Date("Y-m-d H:i") . "',
				'" . $_SESSION['Items']->DefaultSalesType . "',
				" . $_POST['ShipVia'] .",
				'" . DB_escape_string($_SESSION['Items']->DeliverTo) . "',
				'" . DB_escape_string($_SESSION['Items']->DelAdd1) . "',
				'" . DB_escape_string($_SESSION['Items']->DelAdd2) . "',
				'" . DB_escape_string($_SESSION['Items']->DelAdd3) . "',
				'" . DB_escape_string($_SESSION['Items']->DelAdd4) . "',
				'" . DB_escape_string($_SESSION['Items']->DelAdd5) . "',
				'" . DB_escape_string($_SESSION['Items']->DelAdd6) . "',
                '" . DB_escape_string($_SESSION['Items']->DelAdd7) . "',
                '" . DB_escape_string($_SESSION['Items']->DelAdd8) . "',
                '" . DB_escape_string($_SESSION['Items']->DelAdd9) . "',
                '" . DB_escape_string($_SESSION['Items']->DelAdd10) . "',
				'" . DB_escape_string($_SESSION['Items']->PhoneNo) . "',
				'" . DB_escape_string($_SESSION['Items']->Email) . "',
				" . $_SESSION['Items']->FreightCost .",
				'" . $_SESSION['Items']->Location ."',
				'" . $DelDate . "',
				" . $_SESSION['Items']->Quotation . ",
				" . $_SESSION['Items']->DeliverBlind .",
				'".$locSM."',
                '".$_POST['SalesMan']."')";

	$ErrMsg = _('The order cannot be added because');
	$InsertQryResult = DB_query($HeaderSQL,$db,$ErrMsg,'',true);

    $sql = "INSERT INTO rh_translogs(type, typeno, date, user, realizo) VALUES (30, ".$OrderNo.", NOW(), '".$_SESSION['UserID']."','CP')";
    $res = DB_query($sql,$db,'Imposible insertar el usuario','',true);
	//$OrderNo = GetNextTransNo(30, $db);
	if(strlen($_SESSION['rh_comments'])>20){
			$sql = "INSERT INTO rh_priceauth (user_, date_, comments, order_) VALUES (
		'".$_SESSION['UserID']."',
		'".date('Y-m-d')."',
		'".$_SESSION['rh_comments']."',
		".$OrderNo.")";
		$Auth_Res = DB_query($sql,$db,'ERROR: Imposible insertar los datos de autorizacion','',true);
	}

	// bowikaxu - april 2007 - insert user who created the order
	$sql = "INSERT INTO rh_usertrans(type, user_, order_, date_) VALUES (30, '"
			.$_SESSION['UserID']."', ".$OrderNo.", now())";
	$res = DB_query($sql,$db,'Imposible insertar el usuario','',true);


	//$OrderNo = DB_Last_Insert_ID($db,'salesorders','orderno');
	// bowikaxu realhost - 9 july 2008 - save item description
	// bowikaxu reslhost - 4 august 2008 - save the actual cost
	$StartOf_LineItemsSQL = "INSERT INTO salesorderdetails (
						orderlineno,
						orderno,
						stkcode,
						unitprice,
						quantity,
						discountpercent,
						narrative,
						description,
						poline,
						rh_cost,
						itemdue)
					VALUES (";

	foreach ($_SESSION['Items']->LineItems as $StockItem) {

		$LineItemsSQL = $StartOf_LineItemsSQL .
					$StockItem->LineNumber . ",
					" . $OrderNo . ",
					'" . $StockItem->StockID . "',
					". $StockItem->Price*$StockItem->Factor . ",
					" . $StockItem->Quantity . ",
					" . floatval($StockItem->DiscountPercent) . ",
					'" . DB_escape_string($StockItem->Narrative) . "',
					'" . DB_escape_string($StockItem->ItemDescription) . "',
					'" . DB_escape_string($StockItem->POLine) . "',
					" . $StockItem->StandardCost . ",
					'" . FormatDateForSQL($StockItem->ItemDue) . "'
				)";
		$Ins_LineItemResult = DB_query($LineItemsSQL,$db,'Imposible insertar articulo','',true);
        //*****************Descuentos multiples ***********************************************
        $D1=(($StockItem->Discount1)/100);
		$monto=($D1*$StockItem->Price);
        $StartOf_LineItemsSQL2 = "INSERT INTO rh_descuentos (
						type,
						transno,
						orderlineno,
						tipo_descuento,
						descuento,
						monto,
						cant)
					VALUES (";
        $LineItemsSQL = $StartOf_LineItemsSQL2 .
					"30,
					" . $OrderNo . ",
					".$StockItem->LineNumber."
					,1 ,
					'". $D1 . "',
					'" . $monto . "',
					".$StockItem->Quantity."
				)";
		$Ins_LineItemResult = DB_query($LineItemsSQL,$db,'Imposible insertar articulo','',true);

	   $StartOf_LineItemsSQL2 = "INSERT INTO rh_descuentos (
						type,
						transno,
						orderlineno,
						tipo_descuento,
						descuento,
						monto,
						cant)
					VALUES (";
		$D1=(($StockItem->Discount1)/100);
		$monto_D1=($D1*$StockItem->Price);

		$st=($StockItem->Price)-$monto_D1;

		$D2=(($StockItem->Discount2)/100);
		$monto=($D2*$st);

		$LineItemsSQL = $StartOf_LineItemsSQL2 .
					"30,
					" . $OrderNo . ",
					".$StockItem->LineNumber."
					,2 ,
					'". $D2 . "',
					'" . $monto . "',
					".$StockItem->Quantity."
				)";
		$Ins_LineItemResult = DB_query($LineItemsSQL,$db,'Imposible insertar articulo','',true);
        //*************************************************************************************
	} /* inserted line items into sales order details */

        if(isset($_SESSION['isCartaPorte']) && $_SESSION['isCartaPorte']==true){
            $sql =  "insert into rh_carta_porte values(
                    $OrderNo,
                    '". $_POST['rh_cartaPorte__expedidaEn']."',
                    '". $_POST['rh_cartaPorte__contribuyenteRegimenReg']."',
                    '". $_POST['rh_cartaPorte__origen']."',
                    '". $_POST['rh_cartaPorte__origenSeRecogeraEn']."',
                    '". $_POST['rh_cartaPorte__destino']."',
                    '". $_POST['rh_cartaPorte__destinoSeEntregaraEn']."',
                    '". $_POST['rh_cartaPorte__destinoDestinatario']."',
                    '". $_POST['rh_cartaPorte__destinoRfcDestino']."',
                    '". $_POST['rh_cartaPorte__destinoDomicilioDestino']."',
                    '". $_POST['rh_cartaPorte__clienteRetenedor']."',
                    '". $_POST['rh_cartaPorte__clienteRetenedorRfc']."',
                    '". $_POST['rh_cartaPorte__clienteRetenedorDomicilio']."',
                    '". $_POST['rh_cartaPorte__clienteRetenedorValorDeclarado']."',
                    '". $_POST['rh_cartaPorte__polizaNombre']."',
                    '". $_POST['rh_cartaPorte__polizaNumero']."',
                    '". $_POST['rh_cartaPorte__remitenteNumero']."',
                    '". $_POST['rh_cartaPorte__remitenteEmbalaje']."',
                    '". $_POST['rh_cartaPorte__remitenteDiceContiene']."',
                    '". $_POST['rh_cartaPorte__remitentePeso']."',
                    '". $_POST['rh_cartaPorte__remitenteMetros']."',
                    '". $_POST['rh_cartaPorte__remitentePesoEstimado']."',
                    '". $_POST['rh_cartaPorte__remitenteRazonSocial']."',
                    '". $_POST['rh_cartaPorte__remitenteRfc']."',
                    '". $_POST['rh_cartaPorte__remitenteDireccion']."',
                    '". $_POST['rh_cartaPorte__mantenerTemperaturaA']."',
                    '". $_POST['rh_cartaPorte__selloNumero']."',
                    '". $_POST['rh_cartaPorte__operador']."',
                    '". $_POST['rh_cartaPorte__carro']."',
                    '". $_POST['rh_cartaPorte__remolque']."',
                    '". $_POST['rh_cartaPorte__numeroPedimento']."',
                    '". $_POST['rh_cartaPorte__numeroFactura']."',
                    '". $_POST['rh_cartaPorte__numeroRemision']."',
                    '". $_POST['rh_cartaPorte__numeroOrden']."',
                    '". $_POST['rh_cartaPorte__numeroProveedor']."',
                    '". $_POST['rh_cartaPorte__numeroTrip']."',
                    '". $_POST['rh_cartaPorte__observaciones']."',
                    '". $_POST['rh_cartaPorte__documento']."')";
            DB_query($sql,$db,'no se pudieron insertar los datos de la carta porte','',true);
        }
        if(isset($_SESSION['isTransportista']) && $_SESSION['isTransportista']==true){
            $sql = "insert into rh_vps__transportista values($OrderNo)";
            DB_query($sql,$db,'no se pudieron insertar los datos del trasportista','',true);
        }
	DB_query("COMMIT",$db);
	if ($_SESSION['Items']->Quotation==1){
		prnMsg(_('Quotation Number') . ' ' . $OrderNo . ' ' . _('has been entered'),'success');
	} else {
		prnMsg(_('Order Number') . ' ' . $OrderNo . ' ' . _('has been entered'),'success');
	}

	if (count($_SESSION['AllowedPageSecurityTokens'])>1){
		/* Only allow print of packing slip for internal staff - customer logon's cannot go here */

		if ($_POST['Quotation']==0) { /*then its not a quotation its a real order */

		   //	echo "<P><A  target='_blank' HREF='$rootpath/PrintCustOrder.php?" . SID . '&TransNo=' . $OrderNo . "'>". _('Print packing slip') . ' (' . _('Preprinted stationery') . ')' .'</A>';
		   //	echo "<P><A  target='_blank' HREF='$rootpath/PrintCustOrder_generic.php?" . SID . '&TransNo=' . $OrderNo . "'>". _('Print packing slip') .'</A>';

                        if(isset($_SESSION['isCartaPorte']) && $_SESSION['isCartaPorte']==true)
                            echo "<P><A HREF='$rootpath/rh_cartaPorte_ConfirmDispatch_Invoice.php?" . SID . "&OrderNumber=$OrderNo'>". _('Confirm Order Delivery Quantities and Produce Invoice') . "/Carta Porte" ."</A>";
                        else
                            echo "<P><A HREF='$rootpath/ConfirmDispatch_Invoice.php?" . SID . "&OrderNumber=$OrderNo'>". _('Confirm Order Delivery Quantities and Produce Invoice') ."</A>";
			// Sept 2006 RealHost
			// bowikaxu add a link to create directly a shipment
		   //	echo "<P><A HREF='$rootpath/rh_ConfirmDispatch_Shipment.php?" . SID . "&OrderNumber=$OrderNo'>". _('Confirm Order Delivery Quantities and Produce Shipment') ."</A>";

			/*
			 * iJPe
			 * realhost
			 * 2010-01-14
			 */
		   //	echo "<P><A HREF='$rootpath/rh_ConfirmDispatch_Invoice_NC.php?" . SID . "&OrderNumber=$OrderNo'>". _('Confirmar las Cantidades a Enviar y genere una Nota de Cargo') ."</A>";

		} else {
			/*link to print the quotation */
			echo "<P><A HREF='$rootpath/PDFQuotation.php?" . SID . "&QuotationNo=$OrderNo'>". _('Print Quotation') ."</A>";
		}
		echo "<P><A HREF='$rootpath/SelectOrderItems.php?" . SID . "&NewOrder=Yes'>". _('Add Sales Order') .'</A>';
	} else {
		/*its a customer logon so thank them */
		prnMsg(_('Thank you for your business'),'success');
	}

	unset($_SESSION['Items']->LineItems);
	unset($_SESSION['Items']);
	include('includes/footer.inc');
	exit;

} elseif (isset($OK_to_PROCESS) and $OK_to_PROCESS == 1 && $_SESSION['ExistingOrder']!=0){

/* update the order header then update the old order line details and insert the new lines */

	$DelDate = FormatDateforSQL($_SESSION['Items']->DeliveryDate);

	$Result = DB_query('BEGIN',$db);

        /*
         * iJPe
         * realhost
         * 2010-02-13
         *
         * Modificacion por sucursales virtuales
         */

        $sqlVerifyLoc = "SELECT rh_master_loccode FROM rh_locations_virtual WHERE loccode = '".$_SESSION['Items']->Location."'";
        $resVerifyLoc = DB_query($sqlVerifyLoc, $db);

        if (DB_num_rows($resVerifyLoc) > 0){
            $rowVerifyLoc = DB_fetch_array($resVerifyLoc);
            $locSM = $rowVerifyLoc['rh_master_loccode'];
        }else{
            $locSM = $_SESSION['Items']->Location;
        }


	$HeaderSQL = "UPDATE salesorders
			SET debtorno = '" . $_SESSION['Items']->DebtorNo . "',
				branchcode = '" . $_SESSION['Items']->Branch . "',
				customerref = '". DB_escape_string($_SESSION['Items']->CustRef) ."',
				comments = '". DB_escape_string($_SESSION['Items']->Comments) ."',
				ordertype = '" . $_SESSION['Items']->DefaultSalesType . "',
				shipvia = " . $_POST['ShipVia'] .",
				deliverto = '" . $_SESSION['Items']->DeliverTo . "',
				deladd1 = '" . DB_escape_string($_SESSION['Items']->DelAdd1) . "',
				deladd2 = '" . DB_escape_string($_SESSION['Items']->DelAdd2) . "',
				deladd3 = '" . DB_escape_string($_SESSION['Items']->DelAdd3) . "',
				deladd4 = '" . DB_escape_string($_SESSION['Items']->DelAdd4) . "',
				deladd5 = '" . DB_escape_string($_SESSION['Items']->DelAdd5) . "',
				deladd6 = '" . DB_escape_string($_SESSION['Items']->DelAdd6) . "',
                deladd7 = '" . DB_escape_string($_SESSION['Items']->DelAdd7) . "',
                deladd8 = '" . DB_escape_string($_SESSION['Items']->DelAdd8) . "',
                deladd9 = '" . DB_escape_string($_SESSION['Items']->DelAdd9) . "',
                deladd10 = '" . DB_escape_string($_SESSION['Items']->DelAdd10) . "',
				contactphone = '" . DB_escape_string($_SESSION['Items']->PhoneNo) . "',
				contactemail = '" . DB_escape_string($_SESSION['Items']->Email) . "',
				freightcost = " . $_SESSION['Items']->FreightCost .",
                                fromstkloc_virtual = '" . $_SESSION['Items']->Location ."',
				fromstkloc = '" . $locSM ."',
				deliverydate = '" . $DelDate . "',
				printedpackingslip = " . $_POST['ReprintPackingSlip'] . ",
				quotation = " . $_SESSION['Items']->Quotation . ",
				deliverblind = " . $_SESSION['Items']->DeliverBlind . ",
                salesman='".$_POST['SalesMan']."'
			WHERE salesorders.orderno=" . $_SESSION['ExistingOrder'];

	$DbgMsg = _('The SQL that was used to update the order and failed was');
	$ErrMsg = _('The order cannot be updated because');
	$InsertQryResult = DB_query($HeaderSQL,$db,$ErrMsg,$DbgMsg,true);

        if(isset($_SESSION['isCartaPorte']) && $_SESSION['isCartaPorte']==true){
            $cp = "update rh_carta_porte set
            rh_carta_porte__expedida_en = '". $_POST['rh_cartaPorte__expedidaEn']."',
            rh_carta_porte__contribuyente_regimen__reg = '". $_POST['rh_cartaPorte__contribuyenteRegimenReg']."',
            rh_carta_porte__origen = '". $_POST['rh_cartaPorte__origen']."',
            rh_carta_porte__origen__se_recogera_en = '". $_POST['rh_cartaPorte__origenSeRecogeraEn']."',
            rh_carta_porte__destino = '". $_POST['rh_cartaPorte__destino']."',
            rh_carta_porte__destino__se_entregara_en = '". $_POST['rh_cartaPorte__destinoSeEntregaraEn']."',
            rh_carta_porte__destino__destinatario = '". $_POST['rh_cartaPorte__destinoDestinatario']."',
            rh_carta_porte__destino__rfc_destino = '". $_POST['rh_cartaPorte__destinoRfcDestino']."',
            rh_carta_porte__destino__domicilio_destino = '". $_POST['rh_cartaPorte__destinoDomicilioDestino']."',
            rh_carta_porte__cliente_retenedor = '". $_POST['rh_cartaPorte__clienteRetenedor']."',
            rh_carta_porte__cliente_retenedor__rfc = '". $_POST['rh_cartaPorte__clienteRetenedorRfc']."',
            rh_carta_porte__cliente_retenedor__domicilio = '". $_POST['rh_cartaPorte__clienteRetenedorDomicilio']."',
            rh_carta_porte__cliente_retenedor__valor_declarado = '". $_POST['rh_cartaPorte__clienteRetenedorValorDeclarado']."',
            rh_carta_porte__poliza__nombre = '". $_POST['rh_cartaPorte__polizaNombre']."',
            rh_carta_porte__poliza__numero = '". $_POST['rh_cartaPorte__polizaNumero']."',
            rh_carta_porte__remitente__numero = '". $_POST['rh_cartaPorte__remitenteNumero']."',
            rh_carta_porte__remitente__embalaje = '". $_POST['rh_cartaPorte__remitenteEmbalaje']."',
            rh_carta_porte__remitente__dice_contiene = '". $_POST['rh_cartaPorte__remitenteDiceContiene']."',
            rh_carta_porte__remitente__peso = '". $_POST['rh_cartaPorte__remitentePeso']."',
            rh_carta_porte__remitente__metros = '". $_POST['rh_cartaPorte__remitenteMetros']."',
            rh_carta_porte__remitente__peso_estimado = '". $_POST['rh_cartaPorte__remitentePesoEstimado']."',
            rh_carta_porte__remitente__razon_social = '". $_POST['rh_cartaPorte__remitenteRazonSocial']."',
            rh_carta_porte__remitente__rfc = '". $_POST['rh_cartaPorte__remitenteRfc']."',
            rh_carta_porte__remitente__direccion = '". $_POST['rh_cartaPorte__remitenteDireccion']."',
            rh_carta_porte__mantener_temperatura_a = '". $_POST['rh_cartaPorte__mantenerTemperaturaA']."',
            rh_carta_porte__sello_numero = '". $_POST['rh_cartaPorte__selloNumero']."',
            rh_carta_porte__operador = '". $_POST['rh_cartaPorte__operador']."',
            rh_carta_porte__carro = '". $_POST['rh_cartaPorte__carro']."',
            rh_carta_porte__remolque = '". $_POST['rh_cartaPorte__remolque']."',
            rh_carta_porte__numero_pedimento = '". $_POST['rh_cartaPorte__numeroPedimento']."',
            rh_carta_porte__numero_factura = '". $_POST['rh_cartaPorte__numeroFactura']."',
            rh_carta_porte__numero_remision = '". $_POST['rh_cartaPorte__numeroRemision']."',
            rh_carta_porte__numero_orden = '". $_POST['rh_cartaPorte__numeroOrden']."',
            rh_carta_porte__numero_proveedor = '". $_POST['rh_cartaPorte__numeroProveedor']."',
            rh_carta_porte__numero_trip = '". $_POST['rh_cartaPorte__numeroTrip']."',
            rh_carta_porte__observaciones = '". $_POST['rh_cartaPorte__observaciones']."',
            rh_carta_porte__documento = '". $_POST['rh_cartaPorte__documento']."'
            where id_salesorders = " . $_SESSION['ExistingOrder'];
            DB_query($cp,$db,'no se pudieron modificar los datos de la Carta Porte','',true);
        }
	/**************************************************************************
	* Jorge Garcia
	* 19/Nov/2008 Usuario que modifico el pedido
	* rleal Jul 2010 Se agrega
	***************************************************************************/
	$sql = "INSERT INTO rh_translogs(type, typeno, date, user, realizo) VALUES (30, ".$_SESSION['ExistingOrder'].", NOW(), '".$_SESSION['UserID']."','MP')";
	$res = DB_query($sql,$db,'Imposible insertar el usuario','',true);
	/**************************************************************************
	* Jorge Garcia Fin Modificacion
	***************************************************************************/


	foreach ($_SESSION['Items']->LineItems as $StockItem) {

		/* Check to see if the quantity reduced to the same quantity
		as already invoiced - so should set the line to completed */
		if ($StockItem->Quantity == $StockItem->QtyInv){
			$Completed = 1;
		} else {  /* order line is not complete */
			$Completed = 0;
		}

		$LineItemsSQL = "UPDATE salesorderdetails SET unitprice="  . $StockItem->Price*$StockItem->Factor . ',
								quantity=' . $StockItem->Quantity . ',
								discountpercent=' . floatval($StockItem->DiscountPercent) . ',
								completed=' . $Completed . ",
								poline='" . DB_escape_string($StockItem->POLine) . "',
								rh_cost=" . $StockItem->StandardCost . ",
								itemdue='" . FormatDateForSQL($StockItem->ItemDue) . "'
					WHERE salesorderdetails.orderno=" . $_SESSION['ExistingOrder'] . "
					AND salesorderdetails.orderlineno='" . $StockItem->LineNumber . "'";

		$ErrMsg = _('The updated order line cannot be modified because');
		$Upd_LineItemResult = DB_query($LineItemsSQL,$db,$ErrMsg,$DbgMsg,true);

        //*****************Descuentos multiples ***********************************************
        $D1=(($StockItem->Discount1)/100);
		$monto=($D1*$StockItem->Price);
        $StartOf_LineItemsSQL = "update rh_descuentos set
            descuento=".$D1.",
            monto=".$monto.",
            cant=".$StockItem->Quantity."
            where type=30 and tipo_descuento=1 and transno=" . $_SESSION['ExistingOrder'] . " and orderlineno =" . $StockItem->LineNumber . "";

		$Ins_LineItemResult = DB_query($StartOf_LineItemsSQL,$db,'Imposible insertar articulo','',true);

		$D1=(($StockItem->Discount1)/100);
		$monto_D1=($D1*$StockItem->Price);

		$st=($StockItem->Price)-$monto_D1;

		$D2=(($StockItem->Discount2)/100);
		$monto=($D2*$st);

       $StartOf_LineItemsSQL = "update rh_descuentos set
            descuento=".$D2.",
            monto=".$monto.",
            cant=".$StockItem->Quantity."
            where type=30 and tipo_descuento=2 and transno=" . $_SESSION['ExistingOrder'] . " and orderlineno =" . $StockItem->LineNumber . "";

		$Ins_LineItemResult = DB_query($StartOf_LineItemsSQL,$db,'Imposible insertar articulo','',true);

        //*************************************************************************************

	} /* updated line items into sales order details */

	$Result=DB_query('COMMIT',$db);

    $QuotationType = $_SESSION['Items']->Quotation;
	unset($_SESSION['Items']->LineItems);
	unset($_SESSION['Items']);

	prnMsg(_('Order number') .' ' . $_SESSION['ExistingOrder'] . ' ' . _('has been updated'),'success');

   //	echo "<BR><A HREF='$rootpath/PrintCustOrder.php?" . SID . '&TransNo=' . $_SESSION['ExistingOrder'] . "'>". _('Print packing slip - pre-printed stationery') .'</A>';
        $sql = "select id_salesorders from rh_carta_porte where id_salesorders = " . $_SESSION['ExistingOrder'];
        if(DB_num_rows(DB_query($sql,$db,'no se pudo verificar si el pedido es Carta Porte','',false))>0)
            echo "<P><A HREF='$rootpath/rh_cartaPorte_ConfirmDispatch_Invoice.php?" . SID . "&OrderNumber=" . $_SESSION['ExistingOrder'] . "'>". _('Confirm Order Delivery Quantities and Produce Invoice') . "/Carta Porte" ."</A>";
        else{
           if($QuotationType != 3){
                echo "<P><A HREF='$rootpath/ConfirmDispatch_Invoice.php?" . SID . '&OrderNumber=' . $_SESSION['ExistingOrder'] . "'>". _('_Confirm Order Delivery Quantities and Produce Invoice') ."</A>";
             }
        }
	//echo "<BR><A  target='_blank' HREF='$rootpath/PrintCustOrder_generic.php?" . SID . '&TransNo=' . $_SESSION['ExistingOrder'] . "'>". _('Print packing slip') . ' (' . _('Laser') . ')' .'</A>';
	echo "<P><A HREF='$rootpath/SelectSalesOrder.php?" . SID  . "'>". _('Select A Different Order') .'</A>';
	include('includes/footer.inc');
	exit;
}


if ($_SESSION['Items']->SpecialInstructions) {
  prnMsg($_SESSION['Items']->SpecialInstructions,'info');
}

/*Obtengo Datos del Afiliado*/
$_2GetAfilData = "SELECT ti.folio, cobranza.cobrador, fa.tipo_membresia
                       FROM rh_titular ti
                       LEFT JOIN rh_cobranza cobranza ON cobranza.folio = ti.folio
                       LEFT JOIN rh_foliosasignados fa ON ti.folio = fa.folio
                       WHERE ti.debtorno = '{$_SESSION['Items']->DebtorNo}'";
$_GetAfilData=DB_query($_2GetAfilData,$db);
$GetAfilData = DB_fetch_assoc($_GetAfilData);

echo '<CENTER><FONT SIZE=4><B>' . $GetAfilData['tipo_membresia'] . ' - ' . $GetAfilData['folio'] . ' - ' . $_SESSION['Items']->DebtorNo;
echo '&nbsp;&nbsp;' . $_SESSION['Items']->CustomerName . '</B></FONT></CENTER>';
//echo '<CENTER><FONT SIZE=4><B>'. _('Customer') .' : ' . $_SESSION['Items']->CustomerName . '</B></FONT></CENTER>';
echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID . "' METHOD=POST>";


/*Display the order with or without discount depending on access level*/
if (in_array(2,$_SESSION['AllowedPageSecurityTokens'])){

	echo '<CENTER><B>';

	if ($_SESSION['Items']->Quotation==1){
		echo _('Quotation Summary');
	} else {
            if(!(isset($_SESSION['isCartaPorte']) && $_SESSION['isCartaPorte']==true))
		echo _('Order Summary');
	}
	echo "</B>
	<TABLE " . ((isset($_SESSION['isCartaPorte']) && $_SESSION['isCartaPorte']==true)?'style="display: none;"':'') . " id='tablaConceptos' CELLPADDING=2 COLSPAN=7 BORDER=1>
	<TR>
		<TD class='tableheader'>". _('Item Code') ."</TD>
		<TD class='tableheader'>". _('Item Description') ."</TD>
		<TD class='tableheader'>". _('Quantity') ."</TD>
		<TD class='tableheader'>". _('Unit') ."</TD>
		<TD class='tableheader'>". _('Price') ."</TD> ".
		//<TD class='tableheader'>". _('Discount') ." %</TD>
        "<TD class='tableheader'>". _('Discount') ." %</TD>
        <TD class='tableheader'>". _('Discount') ." %</TD>
		<TD class='tableheader'>". _('Total') ."</TD>
	</TR>";

	$_SESSION['Items']->total = 0;
	$_SESSION['Items']->totalVolume = 0;
	$_SESSION['Items']->totalWeight = 0;
	$k = 0; //row colour counter

	foreach ($_SESSION['Items']->LineItems as $StockItem) {

		$LineTotal = $StockItem->Quantity * $StockItem->Price * $StockItem->Factor* (1 - $StockItem->DiscountPercent);
		$DisplayLineTotal = number_format($LineTotal,2);
		$DisplayPrice = number_format($StockItem->Price* $StockItem->Factor,2);
		$DisplayQuantity = number_format($StockItem->Quantity,$StockItem->DecimalPlaces);
		$DisplayDiscount = number_format(($StockItem->DiscountPercent * 100),2);
        $DisplayDiscount1 = number_format(($StockItem->Discount1),2);
        $DisplayDiscount2 = number_format(($StockItem->Discount2),2);


		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}

		 echo "<TD>$StockItem->StockID</TD>
		 	<TD>$StockItem->ItemDescription</TD>
			<TD ALIGN=RIGHT>$DisplayQuantity</TD>
			<TD>$StockItem->Units</TD>
			<TD ALIGN=RIGHT>$DisplayPrice</TD>".
			//<TD ALIGN=RIGHT>$DisplayDiscount</TD>
            "<TD ALIGN=RIGHT>$DisplayDiscount1</TD>
            <TD ALIGN=RIGHT>$DisplayDiscount2</TD>
			<TD ALIGN=RIGHT>$DisplayLineTotal</TD>
		</TR>";

		$_SESSION['Items']->total = $_SESSION['Items']->total + $LineTotal;
		$_SESSION['Items']->totalVolume = $_SESSION['Items']->totalVolume + ($StockItem->Quantity * $StockItem->Volume);
		$_SESSION['Items']->totalWeight = $_SESSION['Items']->totalWeight + ($StockItem->Quantity * $StockItem->Weight);
	}

	$DisplayTotal = number_format($_SESSION['Items']->total,2);
	echo "<TR>
		<TD COLSPAN=6 ALIGN=RIGHT><B>". _('TOTAL Excl Tax/Freight') ."</B></TD>
		<TD ALIGN=RIGHT>$DisplayTotal</TD>
	</TR></TABLE>";

	$DisplayVolume = number_format($_SESSION['Items']->totalVolume,2);
	$DisplayWeight = number_format($_SESSION['Items']->totalWeight,2);
	echo "<TABLE style=\"display: none;\" BORDER=1><TR>
		<TD>". _('Total Weight') .":</TD>
		<TD>$DisplayWeight</TD>
		<TD>". _('Total Volume') .":</TD>
		<TD>$DisplayVolume</TD>
	</TR></TABLE>";

} else {

/*Display the order without discount */

	echo '<CENTER><B ' . ((isset($_SESSION['isCartaPorte']) && $_SESSION['isCartaPorte']==true)?'style="display: none;"':'') . '>' . _('Order Summary') . "</B>
	<TABLE " . ((isset($_SESSION['isCartaPorte']) && $_SESSION['isCartaPorte']==true)?'style="display: none;"':'') . " id='tablaConceptos' CELLPADDING=2 COLSPAN=7 BORDER=1><TR>
		<TD class='tableheader'>". _('Item Description') ."</TD>
		<TD class='tableheader'>". _('Quantity') ."</TD>
		<TD class='tableheader'>". _('Unit') ."</TD>
		<TD class='tableheader'>". _('Price') ."</TD>
		<TD class='tableheader'>". _('Total') ."</TD>
	</TR>";

	$_SESSION['Items']->total = 0;
	$_SESSION['Items']->totalVolume = 0;
	$_SESSION['Items']->totalWeight = 0;
	$k=0; // row colour counter
	foreach ($_SESSION['Items']->LineItems as $StockItem) {

		$LineTotal = $StockItem->Quantity * $StockItem->Price* $StockItem->Factor * (1 - $StockItem->DiscountPercent);
		$DisplayLineTotal = number_format($LineTotal,2);
		$DisplayPrice = number_format($StockItem->Price,2);
		$DisplayQuantity = number_format($StockItem->Quantity,$StockItem->DecimalPlaces);

		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k=1;
		}
		echo "<TD>$StockItem->ItemDescription</TD>
			<TD ALIGN=RIGHT>$DisplayQuantity</TD>
			<TD>$StockItem->Units</TD>
			<TD ALIGN=RIGHT>$DisplayPrice</TD>
			<TD ALIGN=RIGHT>" . $DisplayLineTotal . "</FONT></TD>
		</TR>";

		$_SESSION['Items']->total = $_SESSION['Items']->total + $LineTotal;
		$_SESSION['Items']->totalVolume = $_SESSION['Items']->totalVolume + $StockItem->Quantity * $StockItem->Volume;
		$_SESSION['Items']->totalWeight = $_SESSION['Items']->totalWeight + $StockItem->Quantity * $StockItem->Weight;

	}

	$DisplayTotal = number_format($_SESSION['Items']->total,2);
	echo "<TABLE><TR>
		<TD>". _('Total Weight') .":</TD>
		<TD>$DisplayWeight</TD>
		<TD>". _('Total Volume') .":</TD>
		<TD>$DisplayVolume</TD>
	</TR></TABLE>";

	$DisplayVolume = number_format($_SESSION['Items']->totalVolume,2);
	$DisplayWeight = number_format($_SESSION['Items']->totalWeight,2);
	echo '<TABLE BORDER=1><TR>
		<TD>'. _('Total Weight') .":</TD>
		<TD>$DisplayWeight</TD>
		<TD>". _('Total Volume') .":</TD>
		<TD>$DisplayVolume</TD>
	</TR></TABLE>";

}

echo '<TABLE id="tablaPrincipal"><TR>
	<TD>'. _('Deliver To') .":</TD>
	<TD><input type=text size=42 max=40 name='DeliverTo' value='" . $_SESSION['Items']->DeliverTo . "'></TD>
</TR>";

/*echo '<TR>
	<TD>'. _('Deliver from the warehouse at') .":</TD>
	<TD><Select name='Location'>";

if ($_SESSION['Items']->Location=='' OR !isset($_SESSION['Items']->Location)) {
	$_SESSION['Items']->Location = $DefaultStockLocation;
}

$StkLocsResult = DB_query('SELECT locationname,loccode FROM locations UNION SELECT locationname,loccode FROM rh_locations_virtual',$db);
while ($myrow=DB_fetch_row($StkLocsResult)){
	if ($_SESSION['Items']->Location==$myrow[1]){
		echo "<OPTION SELECTED Value='$myrow[1]'>$myrow[0]";
	} else {
		echo "<OPTION Value='$myrow[1]'>$myrow[0]";
	}
}

echo '</SELECT></TD></TR>';  */
//*************RH Seleccion de Almacenes permitidos para el usuario*************
echo '<TR>
	<TD>'. _('Deliver from the warehouse at') .":</TD>
	<TD><Select name='Location'>";
if ($_SESSION['Items']->Location=='' OR !isset($_SESSION['Items']->Location)) {
	$_SESSION['Items']->Location = $DefaultStockLocation;
}

foreach($_SESSION['rh_permitionlocation'] as $key=>$value){
	if ($_SESSION['Items']->Location==$key){
		echo "<OPTION SELECTED Value='$key'>$value";
	} else {
		echo "<OPTION Value='$key'>$value";
	}
}
echo '</SELECT></TD></TR>';
//******************************************************************************

if (!$_SESSION['Items']->DeliveryDate) {
	$_SESSION['Items']->DeliveryDate = Date($_SESSION['DefaultDateFormat'],$EarliestDispatch);
}

if($_SESSION['DefaultDateFormat']=='d/m/Y'){
	$jdf=0;
} else {
	$jdf=1;
}

echo '<TR>
	<TD>'. _('Dispatch Date') .":</TD>
	<TD><input type='Text' SIZE=15 MAXLENGTH=14 name='DeliveryDate' value='" . $_SESSION['Items']->DeliveryDate . "'></TD>
	</TR>";

echo '<TR>
	<TD>'. _('Calle') . ":</TD>
	<TD><input type=text size=42 max=40 name='BrAdd1' value='" . $_SESSION['Items']->DelAdd1 . "'></TD>
</TR>";

echo "<TR>
	<TD>". _('No. Interior') . ":</TD>
	<TD><input type=text size=42 max=40 name='BrAdd2' value='" . $_SESSION['Items']->DelAdd2 . "'></TD>
</TR>";

echo '<TR>
	<TD>'. _('No. Exterior') . ":</TD>
	<TD><input type=text size=42 max=40 name='BrAdd3' value='" . $_SESSION['Items']->DelAdd3 . "'></TD>
</TR>";

echo "<TR>
	<TD>". _('Colonia') . ":</TD>
	<TD><input type=text size=42 max=40 name='BrAdd4' value='" . $_SESSION['Items']->DelAdd4 . "'></TD>
</TR>";

echo "<TR>
	<TD>". _('Localidad') . ":</TD>
	<TD><input type=text size=42 max=20 name='BrAdd5' value='" . $_SESSION['Items']->DelAdd5 . "'></TD>
</TR>";

echo "<TR>
	<TD>". _('Referencia') . ":</TD>
	<TD><input type=text size=17 max=15 name='BrAdd6' value='" . $_SESSION['Items']->DelAdd6 . "'></TD>
</TR>";
echo "<TR>
	<TD>". _('Municipio') . ":</TD>
	<TD><input type=text size=42 max=15 name='BrAdd7' value='" . $_SESSION['Items']->DelAdd7 . "'></TD>
</TR>";
echo "<TR>
	<TD>". _('Estado') . ":</TD>
	<TD><input type=text size=17 max=15 name='BrAdd8' value='" . $_SESSION['Items']->DelAdd8 . "'></TD>
</TR>";
echo "<TR>
	<TD>". _('Pa&iacute;s') . ":</TD>
	<TD><input type=text size=17 max=15 name='BrAdd9' value='" . $_SESSION['Items']->DelAdd9 . "'></TD>
</TR>";
echo "<TR>
	<TD>". _('C&oacute;digo Postal') . ":</TD>
	<TD><input type=text size=17 max=15 name='BrAdd10' value='" . $_SESSION['Items']->DelAdd10 . "'></TD>
</TR>";

echo '<TR>
	<TD>'. _('Contact Phone Number') .":</TD>
	<TD><input type=text size=25 max=25 name='PhoneNo' value='" . $_SESSION['Items']->PhoneNo . "'></TD>
</TR>";

echo '<TR><TD>' . _('Contact Email') . ":</TD><TD><input type=text size=40 max=38 name='Email' value='" . $_SESSION['Items']->Email . "'></TD></TR>";

echo '<TR><TD>'. _('Customer Reference') .":</TD>
	<TD><input type=text size=25 max=25 name='CustRef' value='" . $_SESSION['Items']->CustRef . "'></TD>
</TR>";

echo '<TR>
	<TD>'. _('Comments') .":</TD>
	<TD><TEXTAREA NAME=Comments COLS=31 ROWS=5>" . $_SESSION['Items']->Comments ."</TEXTAREA></TD>
</TR>";

	/* This field will control whether or not to display the company logo and
    address on the packlist */

	echo '<TR><TD>' . _('Packlist Type') . ":</TD><TD><SELECT NAME='DeliverBlind'>";
        for ($p = 1; $p <= 2; $p++) {
            echo '<OPTION VALUE=' . $p;
            if ($p == $_SESSION['Items']->DeliverBlind) {
                echo ' SELECTED>';
            } else {
                echo '>';
            }
            switch ($p) {
                case 2:
                    echo _('Hide Company Details/Logo');
		    break;
                default:
                    echo _('Show Company Details/Logo');
		    break;
            }
        }
    echo '</SELECT></TD></TR>';

if (isset($_SESSION['PrintedPackingSlip']) and $_SESSION['PrintedPackingSlip']==1){

    echo '<TR>
    	<TD>'. _('Reprint packing slip') .":</TD>
	<TD><SELECT name='ReprintPackingSlip'>";
    echo '<OPTION Value=0>' . _('Yes');
    echo '<OPTION SELECTED Value=1>' . _('No');
    echo '</SELECT>	'. _('Last printed') .': ' . ConvertSQLDate($_SESSION['DatePackingSlipPrinted']) . '</TD></TR>';

} else {

    echo "<INPUT TYPE=hidden name='ReprintPackingSlip' value=0>";

}

echo '<TR><TD>'. _('Freight Charge') .':</TD>';
echo "<TD><INPUT TYPE=TEXT SIZE=10 MAXLENGTH=12 NAME='FreightCost' VALUE=" . $_SESSION['Items']->FreightCost . '></TD>';

if ($_SESSION['DoFreightCalc']==True){
	echo "<TD><INPUT TYPE=SUBMIT NAME='Update' VALUE='" . _('Recalc Freight Cost') . "'></TD></TR>";
}

if ((!isset($_POST['ShipVia']) OR $_POST['ShipVia']=='') AND isset($_SESSION['Items']->ShipVia)){
	$_POST['ShipVia'] = $_SESSION['Items']->ShipVia;
}

echo '<TR><TD>'. _('Freight Company') .":</TD><TD><SELECT name='ShipVia'>";
$SQL = 'SELECT shipper_id, shippername FROM shippers';
$ShipperResults = DB_query($SQL,$db);
while ($myrow=DB_fetch_array($ShipperResults)){
	if ($myrow['shipper_id']==$_POST['ShipVia']){
			echo '<OPTION SELECTED VALUE=' . $myrow['shipper_id'] . '>' . $myrow['shippername'];
	}else {
		echo '<OPTION VALUE=' . $myrow['shipper_id'] . '>' . $myrow['shippername'];
	}
}

echo '</SELECT></TD></TR>';
//var_dump($_SESSION['SalesmanInquiry']);
if($_SESSION['SalesmanInquiry']=='0'){
    echo '<TR><TD>'. _('Vendedor') .":</TD><TD><SELECT name='SalesMan'>";
        $SQL = 'SELECT salesmancode, salesmanname FROM salesman';
        $ShipperResults = DB_query($SQL,$db);
        while ($myrow=DB_fetch_array($ShipperResults)){
	        if ($myrow['salesmancode']==$_POST['SalesMan']){
			    echo '<OPTION SELECTED VALUE=' . $myrow['salesmancode'] . '>' . $myrow['salesmanname'];
	        }else {
		        echo '<OPTION VALUE=' . $myrow['salesmancode'] . '>' . $myrow['salesmanname'];
	        }
         }
    echo '</SELECT></TD></TR>';
} else{
        $SQL = 'SELECT salesman from custbranch where branchcode="'. $_SESSION['Items']->Branch.'" and debtorno="'.$_SESSION['Items']->DebtorNo.'" ';
        $ShipperResults = DB_query($SQL,$db);
        while ($myrow=DB_fetch_array($ShipperResults)){
            echo '<input type="hidden" name="salesman" value="'.$myrow['salesman'].'" />';
        }
}


if($_SESSION['Items']->Quotation==3){
    echo "<tr>
            <td style='color:red; font-weight: bold;'>
            Factura Programada
            <input type='hidden' name='Quotation' value='3' />
            </td>
        </tr>";
}else{
    echo '<TR><TD>'. _('Quotation Only') .":</TD><TD><SELECT name='Quotation'>";
    if ($_SESSION['Items']->Quotation==1){
    	echo "<OPTION SELECTED VALUE=1>" . _('Yes');
    	echo "<OPTION VALUE=0>" . _('No');
    } else {
    	echo "<OPTION VALUE=1>" . _('Yes');
    	echo "<OPTION SELECTED VALUE=0>" . _('No');
    }
    echo '</SELECT></TD></TR>';
}

echo '</TABLE></CENTER>';
function loadSelect($sql, $name, $selectedValue){
    global $db;
    $result = DB_query($sql, $db);
    $select = "<select name=\"$name\"><option></option>";
    while($row = DB_fetch_array($result))
        $select .= "<option value=\"$row[0]\" " . ($selectedValue==$row[0]?'selected="true"':'') . ">$row[1]</option>";
    $select .= '</select>';
    return $select;
}
if(isset($_SESSION['isCartaPorte']) && $_SESSION['isCartaPorte']==true){
?>
<?php
    if(isset($_SESSION['ExistingOrder'])){
        $cp = "select * from rh_carta_porte where id_salesorders = " . $_SESSION['ExistingOrder'];
        $cp = DB_query($cp,$db,'No se pudieron conseguir los datos de la Carta Porte', '',true);
        $cp = DB_fetch_array($cp);
    }
    $fs = 3;
?>
<table>



<table width="100%" cellpadding="0" cellspacing="0" border="0" >
<tr><td width="50%">&nbsp;</td><td align="center">

<a name="JR_PAGE_ANCHOR_0_1"></a>
<table style="width: 595px; border-collapse: collapse; empty-cells: show" cellpadding="0" cellspacing="0" border="0" bgcolor="white">

<tr>
  <td style="width: 19px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 33px; height: 1px;"></td>
  <td style="width: 10px; height: 1px;"></td>
  <td style="width: 9px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 2px; height: 1px;"></td>
  <td style="width: 21px; height: 1px;"></td>

  <td style="width: 21px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 10px; height: 1px;"></td>
  <td style="width: 10px; height: 1px;"></td>
  <td style="width: 42px; height: 1px;"></td>
  <td style="width: 10px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 5px; height: 1px;"></td>

  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 10px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 16px; height: 1px;"></td>
  <td style="width: 14px; height: 1px;"></td>
  <td style="width: 42px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 27px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>

  <td style="width: 33px; height: 1px;"></td>
  <td style="width: 8px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 10px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 6px; height: 1px;"></td>
  <td style="width: 10px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>

  <td style="width: 5px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 9px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 10px; height: 1px;"></td>
  <td style="width: 12px; height: 1px;"></td>
  <td style="width: 11px; height: 1px;"></td>
  <td style="width: 12px; height: 1px;"></td>
  <td style="width: 11px; height: 1px;"></td>

  <td style="width: 15px; height: 1px;"></td>
  <td style="width: 3px; height: 1px;"></td>
  <td style="width: 3px; height: 1px;"></td>
  <td style="width: 11px; height: 1px;"></td>
  <td style="width: 21px; height: 1px;"></td>
  <td style="width: 6px; height: 1px;"></td>
  <td style="width: 19px; height: 1px;"></td>
  <td style="width: 41px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>

  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 1px; height: 1px;"></td>
  <td style="width: 19px; height: 1px;"></td>
</tr>
<tr valign="top">
  <td colspan="57" style="width: 595px; height: 20px;"></td>
</tr>
<tr valign="top">
  <td colspan="47" style="width: 474px; height: 10px;"></td>
  <td colspan="3"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 4+$fs?>px;">EXPEDIDA EN:</span></td>

  <td colspan="5"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 4+$fs?>px;"><input type="text" class="cp" name="rh_cartaPorte__expedidaEn" value="<?php echo $cp['rh_carta_porte__expedida_en']; ?>"/></span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="57" style="width: 595px; height: 9px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="14"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">CONTRIBUYENTE DE REGIMEN SIMPLIFICADO</span></td>
  <td colspan="4" style="width: 17px; height: 10px;"></td>

  <td colspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">R.F.C.</span></td>
  <td colspan="15"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" disabled="true"/></span></td>
  <td colspan="2" style="width: 10px; height: 10px;"></td>
  <td colspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">REG</span></td>
  <td colspan="14"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" name="rh_cartaPorte__contribuyenteRegimenReg" value="<?php echo $cp['rh_carta_porte__contribuyente_regimen__reg']; ?>"/></span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>
</tr>

<tr valign="top">
  <td colspan="57" style="width: 595px; height: 6px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">ORIGEN:</span></td>
  <td colspan="20"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><?php echo loadSelect("select concat(a.nombre,', ',b.nombre), concat(a.nombre,', ',b.nombre) from rh_carta_porte__catalogo a, rh_carta_porte__catalogo b where a.id = b.pid and a.tipo = 1 and b.tipo = 2", 'rh_cartaPorte__origen', $cp['rh_carta_porte__origen']) ?></span></td>
  <td colspan="2" style="width: 28px; height: 10px;"></td>
  <td colspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">DESTINO:</span></td>

  <td style="width: 1px; height: 10px;"></td>
  <td colspan="26"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><?php echo loadSelect("select concat(a.nombre,', ',b.nombre), concat(a.nombre,', ',b.nombre) from rh_carta_porte__catalogo a, rh_carta_porte__catalogo b where a.id = b.pid and a.tipo = 1 and b.tipo = 2", 'rh_cartaPorte__destino', $cp['rh_carta_porte__destino']) ?></span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="4"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">REMITENTE:</span></td>
  <td style="width: 2px; height: 10px;"></td>
  <td colspan="17"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" name="rh_cartaPorte__clienteRetenedor" value="<?php echo $cp['rh_carta_porte__cliente_retenedor']; ?>"/></span></td>

  <td colspan="2" style="width: 28px; height: 10px;"></td>
  <td colspan="5"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">DESTINATARIO:</span></td>
  <td colspan="24"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__destino__destinatario']; ?>" name="rh_cartaPorte__destinoDestinatario"/></span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">R.F.C.</span></td>

  <td colspan="21"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__cliente_retenedor__rfc']; ?>" name="rh_cartaPorte__clienteRetenedorRfc"/></span></td>
  <td colspan="2" style="width: 28px; height: 10px;"></td>
  <td><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">R.F.C.</span></td>
  <td colspan="28"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__destino__rfc_destino']; ?>" name="rh_cartaPorte__destinoRfcDestino"/></span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>

  <td colspan="4"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">DOMICILIO:</span></td>
  <td colspan="18"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__cliente_retenedor__domicilio']; ?>" name="rh_cartaPorte__clienteRetenedorDomicilio"/></span></td>
  <td colspan="2" style="width: 28px; height: 10px;"></td>
  <td colspan="5"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">DOMICILIO:</span></td>
  <td colspan="24"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__destino__domicilio_destino']; ?>" name="rh_cartaPorte__destinoDomicilioDestino"/></span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>
</tr>

<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="22"></td>
  <td colspan="2" style="width: 28px; height: 10px;"></td>
  <td colspan="29"></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="6"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">SE RECOGERA EN:</span></td>

  <td colspan="16"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__origen__se_recogera_en']; ?>" name="rh_cartaPorte__origenSeRecogeraEn"/></span></td>
  <td colspan="2" style="width: 28px; height: 10px;"></td>
  <td colspan="11"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">SE ENTREGARA EN:</span></td>
  <td colspan="18"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__destino__se_entregara_en']; ?>" name="rh_cartaPorte__destinoSeEntregaraEn"/></span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="57" style="width: 595px; height: 12px;"></td>

</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="53"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">CLIENTE/RETENEDOR</span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="4"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">REMITENTE:</span></td>
  <td style="width: 2px; height: 10px;"></td>

  <td colspan="48"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" disabled="true" /></span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="4"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">R.F.C.</span></td>
  <td style="width: 2px; height: 10px;"></td>
  <td colspan="48"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" disabled="true" /></span></td>

  <td colspan="2" style="width: 20px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="4"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">DOMICILIO:</span></td>
  <td style="width: 2px; height: 10px;"></td>
  <td colspan="48"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" disabled="true" /></span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>
</tr>

<tr valign="top">
  <td colspan="57" style="width: 595px; height: 15px;"></td>
</tr>
<tr valign="top">
  <td colspan="35" style="width: 381px; height: 1px;"></td>
  <td colspan="8" rowspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">PESO</span></td>
  <td style="width: 11px; height: 1px;"></td>
  <td colspan="11" rowspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">VOLUMEN</span></td>
  <td colspan="2" style="width: 20px; height: 1px;"></td>

</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 9px;"></td>
  <td colspan="8"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">BULTOS</span></td>
  <td style="width: 10px; height: 9px;"></td>
  <td colspan="21"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">QUE EL REMITENTEN DICE CONTIENE</span></td>
  <td colspan="3" style="width: 12px; height: 9px;"></td>
  <td style="width: 11px; height: 9px;"></td>
  <td colspan="2" style="width: 20px; height: 9px;"></td>

</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">NUM</span></td>
  <td colspan="6"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">EMBALAJE</span></td>
  <td style="width: 10px; height: 10px;"></td>
  <td colspan="21"></td>
  <td colspan="3" style="width: 12px; height: 10px;"></td>
  <td colspan="8"></td>

  <td style="width: 11px; height: 10px;"></td>
  <td colspan="5"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">MTS</span></td>
  <td colspan="7"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">PESO ESTIMADO</span></td>
  <td style="width: 19px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 11px;"></td>
  <td colspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__remitente__numero']; ?>" name="rh_cartaPorte__remitenteNumero"/></span></td>

  <td colspan="6"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__remitente__embalaje']; ?>" name="rh_cartaPorte__remitenteEmbalaje"/></span></td>
  <td style="width: 10px; height: 11px;"></td>
  <td colspan="21"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" size="38" value="<?php echo $cp['rh_carta_porte__remitente__dice_contiene']; ?>" name="rh_cartaPorte__remitenteDiceContiene"/></span></td>
  <td colspan="3" style="width: 12px; height: 11px;"></td>
  <td colspan="8"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__remitente__peso']; ?>" name="rh_cartaPorte__remitentePeso"/></span></td>
  <td style="width: 11px; height: 11px;"></td>
  <td colspan="5"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__remitente__metros']; ?>" name="rh_cartaPorte__remitenteMetros"/></span></td>

  <td colspan="7"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__remitente__peso_estimado']; ?>" name="rh_cartaPorte__remitentePesoEstimado"/></span></td>
  <td style="width: 19px; height: 11px;"></td>
</tr>
<tr valign="top">
  <td colspan="57" style="width: 595px; height: 3px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="53"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" size="150" value="<?php echo $cp['rh_carta_porte__remitente__razon_social']; ?>" name="rh_cartaPorte__remitenteRazonSocial"/></span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>

</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="53"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" size="150" value="<?php echo $cp['rh_carta_porte__remitente__rfc']; ?>" name="rh_cartaPorte__remitenteRfc"/></span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="53"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" size="150" value="<?php echo $cp['rh_carta_porte__remitente__direccion']; ?>" name="rh_cartaPorte__remitenteDireccion"/></span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>

</tr>
<tr valign="top">
  <td colspan="57" style="width: 595px; height: 13px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="35"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">POLIZA ABIERTA DE TRANSPORTES DE CARGA:</span></td>
  <td colspan="2" style="width: 10px; height: 10px;"></td>
  <td colspan="16"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">VALOR DECLARADO</span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>

</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">NOMBRE:</span></td>
  <td colspan="14"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__poliza__nombre']; ?>" name="rh_cartaPorte__polizaNombre"/></span></td>
  <td colspan="2" style="width: 11px; height: 10px;"></td>
  <td colspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">NUM:</span></td>
  <td colspan="15"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__poliza__numero']; ?>" name="rh_cartaPorte__polizaNumero"/></span></td>

  <td colspan="2" style="width: 10px; height: 10px;"></td>
  <td><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">$</span></td>
  <td colspan="15" style="text-align: right;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__cliente_retenedor__valor_declarado']; ?>" name="rh_cartaPorte__clienteRetenedorValorDeclarado"/></span></td>
  <td colspan="2" style="width: 20px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="57" style="width: 595px; height: 7px;"></td>
</tr>

<tr><td colspan="61" align="center"><table id="nuevaTablaConceptos"></table></td></tr>

<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>

  <td colspan="11"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">NUM PEDIMENTO:</span></td>
  <td colspan="2" style="width: 11px; height: 10px;"></td>
  <td colspan="13"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">NUM. ORDEN:</span></td>
  <td colspan="6" style="width: 29px; height: 10px;"></td>
  <td colspan="11"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">SUMA</span></td>
  <td style="width: 3px; height: 10px;"></td>
  <td colspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">$</span></td>

  <td colspan="5" style="text-align: right;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" disabled="true"/></span></td>
  <td colspan="4" style="width: 22px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="11"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__numero_pedimento']; ?>" name="rh_cartaPorte__numeroPedimento"/></span></td>
  <td colspan="2" style="width: 11px; height: 10px;"></td>
  <td colspan="13"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__numero_orden']; ?>" name="rh_cartaPorte__numeroOrden"/></span></td>

  <td colspan="6" style="width: 29px; height: 10px;"></td>
  <td colspan="11"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">16% I.V.A.</span></td>
  <td style="width: 3px; height: 10px;"></td>
  <td colspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">$</span></td>
  <td colspan="5" style="text-align: right;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" disabled="true"/></span></td>
  <td colspan="4" style="width: 22px; height: 10px;"></td>
</tr>
<tr valign="top">

  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="11"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">NUM. FACTURA:</span></td>
  <td colspan="2" style="width: 11px; height: 10px;"></td>
  <td colspan="13"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">NUM. PROVEEDOR:</span></td>
  <td colspan="6" style="width: 29px; height: 10px;"></td>
  <td colspan="11"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">SUB-TOTAL</span></td>
  <td style="width: 3px; height: 10px;"></td>

  <td colspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">$</span></td>
  <td colspan="5" style="text-align: right;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" disabled="true"/></span></td>
  <td colspan="4" style="width: 22px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="11"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__numero_factura']; ?>" name="rh_cartaPorte__numeroFactura"/></span></td>
  <td colspan="2" style="width: 11px; height: 10px;"></td>

  <td colspan="13"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__numero_proveedor']; ?>" name="rh_cartaPorte__numeroProveedor"/></span></td>
  <td colspan="6" style="width: 29px; height: 10px;"></td>
  <td colspan="11"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">RETENCION</span></td>
  <td style="width: 3px; height: 10px;"></td>
  <td colspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">$</span></td>
  <td colspan="5" style="text-align: right;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" disabled="true"/></span></td>
  <td colspan="4" style="width: 22px; height: 10px;"></td>

</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="11"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">NUM: REMISION:</span></td>
  <td colspan="2" style="width: 11px; height: 10px;"></td>
  <td colspan="13"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">NUM. TRIP:</span></td>
  <td colspan="6" style="width: 29px; height: 10px;"></td>
  <td colspan="11"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">-4% DEL FLETE</span></td>

  <td style="width: 3px; height: 10px;"></td>
  <td colspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">$</span></td>
  <td colspan="5" style="text-align: right;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" disabled="true"/></span></td>
  <td colspan="4" style="width: 22px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 10px;"></td>
  <td colspan="11"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__numero_remision']; ?>" name="rh_cartaPorte__numeroRemision"/></span></td>

  <td colspan="2" style="width: 11px; height: 10px;"></td>
  <td colspan="13"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__numero_trip']; ?>" name="rh_cartaPorte__numeroTrip"/></span></td>
  <td colspan="6" style="width: 29px; height: 10px;"></td>
  <td colspan="11"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">TOTAL</span></td>
  <td style="width: 3px; height: 10px;"></td>
  <td colspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">$</span></td>
  <td colspan="5" style="text-align: right;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" disabled="true"/></span></td>

  <td colspan="4" style="width: 22px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="57" style="width: 595px; height: 6px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 9px;"></td>
  <td colspan="10" rowspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">MANTENER TEMPERATURA A:</span></td>
  <td colspan="11" rowspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__mantener_temperatura_a']; ?>" name="rh_cartaPorte__mantenerTemperaturaA"/></span></td>
  <td colspan="2" style="width: 28px; height: 9px;"></td>

  <td colspan="5" rowspan="2"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">SELLO No.</span></td>
  <td colspan="24"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__sello_numero']; ?>" name="rh_cartaPorte__selloNumero"/></span></td>
  <td colspan="3" style="width: 21px; height: 9px;"></td>
</tr>
<tr valign="top">
  <td colspan="2" style="width: 20px; height: 1px;"></td>
  <td colspan="2" style="width: 28px; height: 1px;"></td>
  <td colspan="27" style="width: 233px; height: 1px;"></td>
</tr>

<tr valign="top">
  <td colspan="57" style="width: 595px; height: 2px;"></td>
</tr>
<tr valign="top">
  <td style="width: 19px; height: 10px;"></td>
  <td colspan="4"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">OPERADOR:</span></td>
  <td colspan="8"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><?php echo loadSelect('select nombre, nombre from rh_carta_porte__catalogo where tipo = 3', 'rh_cartaPorte__operador', $cp['rh_carta_porte__operador']) ?></span></td>
  <td style="width: 10px; height: 10px;"></td>
  <td colspan="7"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">CARRO:</span></td>

  <td colspan="7"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><?php echo loadSelect('select nombre, nombre from rh_carta_porte__catalogo where tipo = 4', 'rh_cartaPorte__carro', $cp['rh_carta_porte__carro']) ?></span></td>
  <td colspan="5" style="width: 28px; height: 10px;"></td>
  <td colspan="9"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">REMOLQUE:</span></td>
  <td colspan="12"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><?php echo loadSelect('select nombre, nombre from rh_carta_porte__catalogo where tipo = 5', 'rh_cartaPorte__remolque', $cp['rh_carta_porte__remolque']) ?></span></td>
  <td colspan="3" style="width: 21px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td style="width: 19px; height: 10px;"></td>

  <td colspan="50" style="text-align: center;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">CADENA ORIGINAL</span></td>
  <td colspan="6" style="width: 64px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td style="width: 19px; height: 10px;"></td>
  <td colspan="51"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" size="150" disabled="true"/></span></td>
  <td colspan="5" style="width: 23px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="57" style="width: 595px; height: 46px;"></td>

</tr>
<tr valign="top">
  <td style="width: 19px; height: 10px;"></td>
  <td colspan="53"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">SELLO</span></td>
  <td colspan="3" style="width: 21px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td style="width: 19px; height: 10px;"></td>
  <td colspan="53"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" size="150" disabled="true"/></span></td>
  <td colspan="3" style="width: 21px; height: 10px;"></td>

</tr>
<tr valign="top">
  <td colspan="57" style="width: 595px; height: 20px;"></td>
</tr>
<tr valign="top">
  <td style="width: 19px; height: 10px;"></td>
  <td colspan="53"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">OBSERVACIONES:</span></td>
  <td colspan="3" style="width: 21px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td style="width: 19px; height: 10px;"></td>

  <td colspan="53"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" size="150" value="<?php echo $cp['rh_carta_porte__observaciones']; ?>" name="rh_cartaPorte__observaciones"/></span></td>
  <td colspan="3" style="width: 21px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="57" style="width: 595px; height: 14px;"></td>
</tr>
<tr valign="top">
  <td style="width: 19px; height: 10px;"></td>
  <td colspan="8"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">CANTIDAD EN LETRA:</span></td>
  <td colspan="45"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" disabled="true"/></span></td>

  <td colspan="3" style="width: 21px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="57" style="width: 595px; height: 6px;"></td>
</tr>
<tr valign="top">
  <td style="width: 19px; height: 10px;"></td>
  <td colspan="16" style="text-align: center;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">PAGO EN UNA SOLA EXHIBICION</span></td>
  <td colspan="2" style="width: 11px; height: 10px;"></td>
  <td colspan="17" style="text-align: center;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">RECIBI DE CONFORMIDAD</span></td>

  <td colspan="2" style="width: 10px; height: 10px;"></td>
  <td colspan="16"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">DOCUMENTO:</span></td>
  <td colspan="3" style="width: 21px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td style="width: 19px; height: 10px;"></td>
  <td colspan="16" style="text-align: center;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">&quot;EFECTOS FISCALES AL PAGO&quot;</span></td>
  <td colspan="2" style="width: 11px; height: 10px;"></td>
  <td colspan="17"></td>

  <td colspan="2" style="width: 10px; height: 10px;"></td>
  <td colspan="16" style="text-align: center;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;"><input type="text" class="cp" value="<?php echo $cp['rh_carta_porte__documento']; ?>" name="rh_cartaPorte__documento"/></span></td>
  <td colspan="3" style="width: 21px; height: 10px;"></td>
</tr>
<tr valign="top">
  <td colspan="19" style="width: 208px; height: 4px;"></td>
  <td colspan="17" rowspan="2"></td>
  <td colspan="2" style="width: 10px; height: 4px;"></td>
  <td colspan="16" rowspan="2"></td>

  <td colspan="3" style="width: 21px; height: 4px;"></td>
</tr>
<tr valign="top">
  <td style="width: 19px; height: 6px;"></td>
  <td colspan="16" rowspan="2" style="text-align: center;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 4+$fs?>px;">IMPUESTO AL VALOR AGREGADO</span></td>
  <td colspan="2" style="width: 11px; height: 6px;"></td>
  <td colspan="2" style="width: 10px; height: 6px;"></td>
  <td colspan="3" style="width: 21px; height: 6px;"></td>
</tr>
<tr valign="top">

  <td style="width: 19px; height: 4px;"></td>
  <td colspan="2" style="width: 11px; height: 4px;"></td>
  <td colspan="17" rowspan="2" style="text-align: center;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 4+$fs?>px;">NOMBRE Y FIRMA</span></td>
  <td colspan="2" style="width: 10px; height: 4px;"></td>
  <td colspan="16" rowspan="2"></td>
  <td colspan="3" style="width: 21px; height: 4px;"></td>
</tr>
<tr valign="top">
  <td colspan="19" style="width: 208px; height: 6px;"></td>

  <td colspan="2" style="width: 10px; height: 6px;"></td>
  <td colspan="3" style="width: 21px; height: 6px;"></td>
</tr>
<tr valign="top">
  <td style="width: 19px; height: 10px;"></td>
  <td colspan="53" style="text-align: center;"><span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: <?php echo 7+$fs?>px;">ESTA ES UNA IMPRESION DE UN COMPROBANTE FISCAL DIGITAL</span></td>
  <td colspan="3" style="width: 21px; height: 10px;"></td>
</tr>
</table>

</td><td width="50%">&nbsp;</td></tr>
</table>



</table>
<?php
}

echo "<BR><CENTER><INPUT TYPE=SUBMIT NAME='BackToLineDetails' VALUE='" . _('Modify Order Lines') . "'>";

if ($_SESSION['ExistingOrder']==0){
	echo "<BR><INPUT TYPE=SUBMIT NAME='ProcessOrder' VALUE='" . _('Place Order') . "'>";
	//echo "<BR><BR><BR><INPUT TYPE=SUBMIT NAME='MakeRecurringOrder' VALUE='" . _('Create Reccurring Order') . "'>";
} else {
	echo "<BR><INPUT TYPE=SUBMIT NAME='ProcessOrder' VALUE='" . _('Commit Order Changes') . "'>";
}

echo '</FORM>';
include('includes/footer.inc');
?>
<?php
if(isSet($_SESSION['isCartaPorte']) && $_SESSION['isCartaPorte']==true){
?>
<script type="text/javascript">
	var t = document.getElementById('tablaConceptos').innerHTML;
	document.getElementById('nuevaTablaConceptos').innerHTML = t;
	var r = document.getElementById('tablaPrincipal').rows;
	for(var i = 0; i < r.length; i++){
		r[i].style.display = 'none';
	}
	r[1].style.display = '';
</script>
<?php
}
?>
<script type="text/javascript" src="rh_j_globalFacturacionElectronica.js"></script>
<script type="text/javascript">
    setInputTextToUpper()
</script>
