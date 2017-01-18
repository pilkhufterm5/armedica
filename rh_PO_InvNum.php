<?php
/* $Revision: 324 $ */
/* $Revision: 324 $ */

$PageSecurity = 11;

/* Session started in header.inc for password checking and authorisation level check */
include('includes/DefinePOClass.php');
include('includes/DefineSerialItems.php');
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

$title = _('Modificar Facturas Asignadas a Productos Recibidos');
include('includes/header.inc');

if ($_GET['PONumber']<=0 AND !isset($_SESSION['PO'])) {
	/* This page can only be called with a purchase order number for invoicing*/
	echo '<CENTER><A HREF="' . $rootpath . '/PO_SelectOSPurchOrder.php?' . SID . '">'.
		_('Seleccionar Orden de Compra').'</A></CENTER>';
	echo '<BR>'. _('This page can only be opened if a purchase order has been selected') . '. ' . _('Please select a purchase order first');
	include ('includes/footer.inc');
	exit;
} elseif (isset($_GET['PONumber']) AND !isset($_POST['Update'])) {
  /*Update only occurs if the user hits the button to refresh the data and recalc the value of goods recd*/

	  $_GET['ModifyOrderNumber'] = $_GET['PONumber'];
	  //include('includes/PO_ReadInOrder.inc');

          if (isset($_SESSION['PO'])){
              unset ($_SESSION['PO']->LineItems);
              unset ($_SESSION['PO']);
       }

       $_SESSION['ExistingOrder']=$_GET['ModifyOrderNumber'];
       $_SESSION['RequireSupplierSelection'] = 0;
       $_SESSION['PO'] = new PurchOrder;

       $_SESSION['PO']->GLLink = $_SESSION['CompanyRecord']['gllink_stock'];

/*read in all the guff from the selected order into the PO PurchOrder Class variable  */

       $OrderHeaderSQL = 'SELECT purchorders.supplierno,
       				suppliers.suppname,
				purchorders.comments,
				purchorders.orddate,
				purchorders.rate,
				purchorders.dateprinted,
				purchorders.deladd1,
				purchorders.deladd2,
				purchorders.deladd3,
				purchorders.deladd4,
				purchorders.deladd5,
				purchorders.deladd6,
				purchorders.allowprint,
				purchorders.requisitionno,
				purchorders.intostocklocation,
				purchorders.initiator,
				suppliers.currcode,
				locations.managed,
				purchorders.rh_autoriza
			FROM purchorders
				LEFT JOIN locations ON purchorders.intostocklocation=locations.loccode,
				suppliers
			WHERE purchorders.supplierno = suppliers.supplierid
			AND purchorders.orderno = ' . $_GET['ModifyOrderNumber'];

       $ErrMsg =  _('The order cannot be retrieved because');
       $DbgMsg =  _('The SQL statement that was used and failed was');
       $GetOrdHdrResult = DB_query($OrderHeaderSQL,$db,$ErrMsg,$DbgMsg);

	if (DB_num_rows($GetOrdHdrResult)==1) {

              $myrow = DB_fetch_array($GetOrdHdrResult);
              $_SESSION['PO']->OrderNo = $_GET['ModifyOrderNumber'];
              $_SESSION['PO']->SupplierID = $myrow['supplierno'];
              $_SESSION['PO']->SupplierName = $myrow['suppname'];
              $_SESSION['PO']->CurrCode = $myrow['currcode'];
              $_SESSION['PO']->Orig_OrderDate = $myrow['orddate'];
              $_SESSION['PO']->AllowPrintPO = $myrow['allowprint'];
              $_SESSION['PO']->DatePurchaseOrderPrinted = $myrow['dateprinted'];
              $_SESSION['PO']->Comments = $myrow['comments'];
              $_SESSION['PO']->ExRate = $myrow['rate'];
              $_SESSION['PO']->Location = $myrow['intostocklocation'];
              $_SESSION['PO']->Initiator = $myrow['initiator'];
              $_SESSION['PO']->RequisitionNo = $myrow['requisitionno'];
              $_SESSION['PO']->DelAdd1 = $myrow['deladd1'];
              $_SESSION['PO']->DelAdd2 = $myrow['deladd2'];
              $_SESSION['PO']->DelAdd3 = $myrow['deladd3'];
              $_SESSION['PO']->DelAdd4 = $myrow['deladd4'];
              $_SESSION['PO']->DelAdd5 = $myrow['deladd5'];
              $_SESSION['PO']->DelAdd6 = $myrow['deladd6'];
              $_SESSION['PO']->Managed = $myrow['managed'];
		$_SESSION['PO']->rh_autoriza = $myrow['rh_autoriza'];
/*now populate the line PO array with the purchase order details records */

// bowikaxu March 2007 - se modifico el query para obtener los rh_comments
              $LineItemsSQL = 'SELECT grns.grnno, grns.podetailitem, purchorderdetails.itemcode,
				stockmaster.description,
				purchorderdetails.deliverydate,
				purchorderdetails.itemdescription,
				glcode,
				accountname,
				qtyinvoiced,
				unitprice,
				units,
				quantityord,
				grns.qtyrecd AS quantityrecd,
				shiptref,
				jobref,
				rh_comments,
				purchorderdetails.stdcostunit,
				stockmaster.controlled,
				stockmaster.serialised,
				stockmaster.decimalplaces,
                                grns.rh_invNumber
				FROM purchorderdetails LEFT JOIN stockmaster
					ON purchorderdetails.itemcode=stockmaster.stockid
					LEFT JOIN chartmaster
					ON purchorderdetails.glcode=chartmaster.accountcode
                                        LEFT JOIN grns ON purchorderdetails.podetailitem = grns.podetailitem
				WHERE purchorderdetails.orderno =' . $_GET['ModifyOrderNumber'] . " AND (grns.qtyrecd - grns.quantityinv) > 0
				ORDER BY podetailitem";

//                $LineItemsSQL = "SELECT podetailitem, itemcode, stockmaster.description, deliverydate, itemdescription,
//                                glcode, accountname, qtyinvoiced, unitprice, units, quantityord, quantityrecd, shiptref, jobref,
//                                rh_comments, stdcostunit, stockmaster.controlled, stockmaster.serialised, stockmaster.decimalplaces
//                                FROM purchorderdetails LEFT JOIN stockmaster ON purchorderdetails.itemcode=stockmaster.stockid
//                                LEFT JOIN chartmaster ON purchorderdetails.glcode=chartmaster.accountcode WHERE
//                                purchorderdetails.completed=0 AND purchorderdetails.orderno =". $_GET['ModifyOrderNumber'] ." ORDER BY podetailitem";

	      $ErrMsg =  _('The lines on the purchase order cannot be retrieved because');
	      $DbgMsg =  _('The SQL statement that was used to retrieve the purchase order lines was');
              $LineItemsResult = db_query($LineItemsSQL,$db,$ErrMsg,$DbgMsg);

	      if (db_num_rows($LineItemsResult) > 0) {

                while ($myrow=db_fetch_array($LineItemsResult)) {

					 if (is_null($myrow['glcode'])){
						$GLCode = '';
					 } else {
						$GLCode = $myrow['glcode'];
					 }
					 if (is_null($myrow['units'])){
						$Units = _('each');
					 } else {
						$Units = $myrow['units'];
					 }
					 if (is_null($myrow['itemcode'])){
						$StockID = '';
					 } else {
						$StockID = $myrow['itemcode'];
					 }

					$_SESSION['PO']->add_to_order($_SESSION['PO']->LinesOnOrder+1,
							$StockID,
							$myrow['controlled'],
							$myrow['serialised'],
							$myrow['quantityord'],
							stripslashes($myrow['itemdescription']),
							$myrow['unitprice'],
							$Units,
							$GLCode,
							ConvertSQLDate($myrow['deliverydate']),
							$myrow['shiptref'],
							$myrow['jobref'],
							$myrow['qtyinvoiced'],
							$myrow['quantityrecd'],
							$myrow['accountname'],
							$myrow['decimalplaces']);

				    $_SESSION['PO']->LineItems[$_SESSION['PO']->LinesOnOrder]->PODetailRec = $myrow['podetailitem'];
				    $_SESSION['PO']->LineItems[$_SESSION['PO']->LinesOnOrder]->ItemComments = $myrow['rh_comments'];
	                $_SESSION['PO']->LineItems[$_SESSION['PO']->LinesOnOrder]->StandardCost = $myrow['stdcostunit'];  /*Needed for receiving goods and GL interface */
                                    $_SESSION['PO']->LineItems[$_SESSION['PO']->LinesOnOrder]->InvoiceNumber = $myrow['rh_invNumber'];
                                    $_SESSION['PO']->LineItems[$_SESSION['PO']->LinesOnOrder]->IDLine = $myrow['grnno'];
             } /* line PO from purchase order details */
      }else{//end is there were lines on the order
          prnMsg("La orden de compra ya ha sido facturada completamente","info");
          include('includes/footer.inc');
          exit;
      }
   } // end if there was a header for the order







} elseif (isset($_POST['Update']) OR isset($_POST['ProcessUpdate'])) {

/* if update quantities button is hit page has been called and ${$Line->LineNo} would have be
 set from the post to the quantity to be received in this receival*/

	foreach ($_SESSION['PO']->LineItems as $Line) {
		$RecvQty = $_POST['RecvQty_' . $Line->LineNo];
                $numFac = $_POST['numFac_' . $Line->LineNo];
//		if (!is_numeric($RecvQty)){
//			$RecvQty = 0;
//		}
		$_SESSION['PO']->LineItems[$Line->LineNo]->ReceiveQty = $RecvQty;
                $_SESSION['PO']->LineItems[$Line->LineNo]->NumFac = $numFac;
	}
}

/* Always display quantities received and recalc balance for all items on the order */


echo '<CENTER><FONT SIZE=4><B><U>'. _('Productos Recibidos de Orden de Compra'). ' '. $_SESSION['PO']->OrderNo .' '. _('from'). ' ' . $_SESSION['PO']->SupplierName . ' </U></B></FONT></CENTER><BR>';
echo '<FORM ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';

echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7 BORDER=0>
<TR><TD class="tableheader">' . _('Item Code') . '</TD>
	<TD class="tableheader">' . _('Description') . '</TD>
	<TD class="tableheader">' . _('Quantity') . '<BR>' . _('Ordered') . '</TD>
	<TD class="tableheader">' . _('Units') . '</TD>
	<TD class="tableheader">' . _('Already Received') . '</TD>	
	<TD class="tableheader">' . _('Price') . '</TD>	
        <TD class="tableheader">' . _('# Factura') . '</TD>';

echo '<TD>&nbsp;</TD>
	</TR>';
/*show the line items on the order with the quantity being received for modification */

$_SESSION['PO']->total = 0;
$k=0; //row colour counter

if (count($_SESSION['PO']->LineItems)>0){
	foreach ($_SESSION['PO']->LineItems as $LnItm) {

		if ($k==1){
			echo '<tr bgcolor="#CCCCCC">';
			$k=0;
		} else {
			echo '<tr bgcolor="#EEEEEE">';
			$k=1;
		}

	/*  if ($LnItm->ReceiveQty==0){   /*If no quantites yet input default the balance to be received
			$LnItm->ReceiveQty = $LnItm->QuantityOrd - $LnItm->QtyReceived;
		}
	*/

	/*Perhaps better to default quantities to 0 BUT.....if you wish to have the receive quantites
	default to the balance on order then just remove the comments around the 3 lines above */

	//Setup & Format values for LineItem display
		
		$_SESSION['PO']->total = $_SESSION['PO']->total + $LineTotal;
		$DisplayQtyOrd = number_format($LnItm->Quantity,$LnItm->DecimalPlaces);
		$DisplayQtyRec = number_format($LnItm->QtyReceived,$LnItm->DecimalPlaces);
		$DisplayLineTotal = number_format($LineTotal,2);
		$DisplayPrice = number_format($LnItm->Price,2);


		
		//Now Display LineItem
		echo '<TD><FONT size=2>' . $LnItm->StockID . '</FONT></TD>';
		echo '<TD><FONT size=2>' . $LnItm->ItemDescription . '</TD>';
		echo '<TD ALIGN=RIGHT><FONT size=2>' . $DisplayQtyOrd . '</TD>';
		echo '<TD><FONT size=2>' . $LnItm->Units . '</TD>';
		echo '<TD ALIGN=RIGHT><FONT size=2>' . $DisplayQtyRec . '</TD>';		

		
		echo '<TD ALIGN=RIGHT><FONT size=2>' . $DisplayPrice . '</TD>';		

                if (strlen($_POST['numFac_'.$LnItm->LineNo])>0 && $_POST['numFac_'.$LnItm->LineNo]!='0'){
                    if ($_POST['numFac_'.$LnItm->LineNo] == $_POST['numFacGlobalANT'] && isset($_POST['updateCon'])){
                        $numFac = $_POST['numFacGlobal'];
                    }else{
                        $numFac = $_POST['numFac_'.$LnItm->LineNo];
                    }

                }else{
                    if (strlen($_POST['numFacGlobal']) && $_POST['numFacGlobal']!='0'){
                        $numFac = $_POST['numFacGlobal'];
                    }else{
                        $numFac = $LnItm->InvoiceNumber;
                    }
                }
                //iJPe
                echo '<TD ALIGN=RIGHT><FONT size=2><input type=text name="numFac_' . $LnItm->LineNo . '" maxlength=10 SIZE=10 value="' . $numFac . '"></TD></FONT></TD>';
	
		echo '</TR>';
	}//foreach(LineItem)        
}//If count(LineItems) > 0


echo '</TABLE>';

if (isset($_POST['numFacGlobal'])){
    $numFacG = $_POST['numFacGlobal'];
}else{
    $numFacG = '0';
}

//iJPe
echo '<br><CENTER>'._('# Factura Global:').'<INPUT TYPE=text NAME=numFacGlobal maxlength=10 SIZE=10 Value="'.$numFacG.'" >';
echo '<INPUT TYPE=hidden NAME=numFacGlobalANT maxlength=10 SIZE=10 Value="'.$numFacG.'" >';
echo '<br>Actualizar coincidencias (# '.$numFacG.')<input type="checkbox" name="updateCon"></CENTER>';

$SomethingReceived = 0;
if (count($_SESSION['PO']->LineItems)>0){
   foreach ($_SESSION['PO']->LineItems as $OrderLine) {
	  if ($OrderLine->ReceiveQty>0){
		$SomethingReceived =1;
	  }
   }
}

/************************* LINE ITEM VALIDATION ************************/

/* Check whether trying to deliver more items than are recorded on the purchase order
(+ overreceive allowance) */

$DeliveryQuantityTooLarge = 0;
$NegativesFound = false;
$InputError = false;

if (count($_SESSION['PO']->LineItems)>0){

   foreach ($_SESSION['PO']->LineItems as $OrderLine) {

	  if ($OrderLine->ReceiveQty+$OrderLine->QtyReceived > $OrderLine->Quantity * (1+ ($_SESSION['OverReceiveProportion'] / 100))){
		$DeliveryQuantityTooLarge =1;
		$InputError = true;
	  }
	  if ($OrderLine->ReceiveQty < 0 AND $_SESSION['ProhibitNegativeStock']==1){

		  	$SQL = "SELECT locstock.quantity FROM
		  			locstock WHERE locstock.stockid='" . DB_escape_string($OrderLine->StockID) . "'
					AND loccode= '" . DB_escape_string($_SESSION['PO']->Location) . "'";
			$CheckNegResult = DB_query($SQL,$db);
			$CheckNegRow = DB_fetch_row($CheckNegResult);
			if ($CheckNegRow[0]+$OrderLine->ReceiveQty<0){
				$NegativesFound=true;
				prnMsg(_('Receiving a negative quantity that results in negative stock is prohibited by the parameter settings. This delivery of stock cannot be processed until the stock of the item is corrected.'),'error',$OrderLine->StockID . ' Cannot Go Negative');
			}
	  }

   }
}

if (isset($_POST['ProcessUpdate'])){ 


	if ($_SESSION['CompanyRecord']==0){
		/*The company data and preferences could not be retrieved for some reason */
		prnMsg(_('The company infomation and preferences could not be retrieved') . ' - ' . _('see your system administrator') , 'error');
		include('includes/footer.inc');
		exit;
	}

/*Now need to check that the order details are the same as they were when they were read into the Items array. If they've changed then someone else must have altered them */
// Otherwise if you try to fullfill item quantities separately will give error.

        $SQL = 'SELECT grns.podetailitem, purchorderdetails.itemcode,
				stockmaster.description,
				purchorderdetails.deliverydate,
				purchorderdetails.itemdescription,
				glcode,
				accountname,
				qtyinvoiced,
				unitprice,
				units,
				quantityord,
				quantityrecd,
				shiptref,
				jobref,
				rh_comments,
				purchorderdetails.stdcostunit,
				stockmaster.controlled,
				stockmaster.serialised,
				stockmaster.decimalplaces,
                                grns.rh_invNumber
				FROM purchorderdetails LEFT JOIN stockmaster
					ON purchorderdetails.itemcode=stockmaster.stockid
					LEFT JOIN chartmaster
					ON purchorderdetails.glcode=chartmaster.accountcode
                                        INNER JOIN grns ON purchorderdetails.podetailitem = grns.podetailitem
				WHERE purchorderdetails.orderno =' . $_SESSION['PO']->OrderNo . "
				ORDER BY podetailitem";


	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not check that the details of the purchase order had not been changed by another user because'). ':';
	$DbgMsg = _('The following SQL to retrieve the purchase order details was used');
	$Result=DB_query($SQL,$db, $ErrMsg, $DbgMsg);

	$Changes=0;
	$LineNo=1;

	DB_free_result($Result);


/************************ BEGIN SQL TRANSACTIONS ************************/

	$Result = DB_query('BEGIN',$db);

        $error = 0;
        foreach ($_SESSION['PO']->LineItems as $OrderLineVer) {

            if (strlen($OrderLineVer->NumFac) <= 0){
                $error = 1;
                //break;
            }
        }

        if ($error != 1){            
            foreach ($_SESSION['PO']->LineItems as $OrderLine) {
                    
                           $SQL = "UPDATE grns SET rh_invNumber = '".$OrderLine->NumFac."' WHERE grnno = ".$OrderLine->IDLine;

                           $ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('A GRN record could not be inserted') . '. ' . _('This receipt of goods has not been processed because');
                           $DbgMsg =  _('The following SQL to insert the GRN record was used');
                           $Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

            } /*end of OrderLine loop */
        }

	$SQL='COMMIT';
	$Result = DB_query($SQL,$db);

	unset($_SESSION['PO']->LineItems);
	unset($_SESSION['PO']);
	unset($_POST['ProcessUpdate']);

	echo '<BR>'. _('Modificacion realizada correctamente').'<BR>';
	
/*end of process goods received entry */
	include('includes/footer.inc');
	exit;

} else { /*Process Goods received not set so show a link to allow mod of line items on order and allow input of date goods received*/
	
	
	echo '</TABLE><CENTER><INPUT TYPE=SUBMIT NAME=Update Value=' . _('Update') . '><P>';
	echo '<INPUT TYPE=SUBMIT NAME="ProcessUpdate" Value="' . _('Procesar Cambios') . '"></CENTER>';
}

echo '</FORM>';

include('includes/footer.inc');
?>
