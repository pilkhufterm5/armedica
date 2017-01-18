<?php

/* $Revision: 1.30 $ */
/* $Revision: 1.33 $ */

/* Definition of the cart class
this class can hold all the information for:

i)   a sales order
ii)  an invoice
iii) a credit note

*/


Class Cart {

	var $LineItems; /*array of objects of class LineDetails using the product id as the pointer */
	var $total; /*total cost of the items ordered */
	var $totalVolume;
	var $totalWeight;
	var $LineCounter;
	var $ItemsOrdered; /*no of different line items ordered */
	var $DeliveryDate;
	var $DefaultSalesType;
	var $SalesTypeName;
	var $DefaultCurrency;
	var $DeliverTo;
	var $DelAdd1;
	var $DelAdd2;
	var $DelAdd3;
	var $DelAdd4;
	var $DelAdd5;
	var $DelAdd6;
    var $DelAdd7;
    var $DelAdd8;
    var $DelAdd9;
    var $DelAdd10;
	var $PhoneNo;
	var $Email;
	var $CustRef;
	var $Comments;
	var $Location;
	var $DebtorNo;
	var $CustomerName;
	var $Orig_OrderDate;
	var $Branch;
	var $TransID;
	var $ShipVia;
	var $FreightCost;
	var $FreightTaxes;
	Var $OrderNo;
	Var $Consignment;
	Var $Quotation;
	Var $DeliverBlind;
	Var $CreditAvailable; //in customer currency
	Var $TaxGroup;
	Var $DispatchTaxProvince;
	VAR $vtigerProductID;
	Var $DefaultPOLine;
	Var $DeliveryDays;
	var $isCredit;

	function Cart(){
	/*Constructor function initialises a new shopping cart */
		$this->LineItems = array();
		$this->total=0;
		$this->ItemsOrdered=0;
		$this->LineCounter=0;
		$this->DefaltSalesType="";
		$this->FreightCost =0;
		$this->FreightTaxes = array();
		$this->isCredit=false;
	}
	function add_to_cart($StockID,
				$Qty,
				$Descr,
				$Price,
				$Disc=0,
				$UOM,
				$Volume,
				$Weight,
				$QOHatLoc=0,
				$MBflag='B',
				$ActDispatchDate=NULL,
				$QtyInvoiced=0,
				$DiscCat='',
				$Controlled=0,
				$Serialised=0,
				$DecimalPlaces=0,
				$Narrative='',
				$UpdateDB='No',
				$LineNumber=-1,
				$TaxCategory=0,
				$vtigerProductID='',
				$ItemDue = '',
				$POLine='',
                $Factor=1,
                $D1=0,
                $D2=0,
                $MiniumPrice=0,
				$StandardCost=0){
		$isCredit=$this->isCredit;

		if (isset($StockID) AND $StockID!="" AND($isCredit||$Qty>0) AND isset($Qty)){

			if ($Price<0&&$MBflag!='D'){ /*madness check - use a credit note to give money away!*/
				$Price=0;
			}

			if ($LineNumber==-1){
				$LineNumber = $this->LineCounter;
			}
/*****************************************************************************************************************************
* Jorge Garcia
* 18/Dic/2008 Guarda Costo Standard
*****************************************************************************************************************************/
			// bowikaxu realhost - Set Standard Cost
			//Se mueve despues de crear la clase ya que genera un stdClass en el servidor nuevo
// 			if ($this->LineItems[($LineNumber)]->StandardCost <= 0){ /*madness check - use a credit note to give money away!*/
// 				$StandardCost = 0;
// 			}else{
// 				$StandardCost = $this->LineItems[($LineNumber)]->StandardCost;
// 			}
/*****************************************************************************************************************************
* Fin Jorge Garcia
*****************************************************************************************************************************/
			$this->LineItems[$LineNumber] = new LineDetails($LineNumber,
									$StockID,
									$Descr,
									$Qty,
									$Price,
									$Disc,
									$UOM,
									$Volume,
									$Weight,
									$QOHatLoc,
									$MBflag,
									$ActDispatchDate,
									$QtyInvoiced,
									$DiscCat,
									$Controlled,
									$Serialised,
									$DecimalPlaces,
									$Narrative,
									$TaxCategory,
									$ItemDue,
									$POLine,$Factor,$D1,$D2,$MiniumPrice);
			//Rafael Rojas 2014-11-28
			if ($this->LineItems[($LineNumber)]->StandardCost <= 0){ /*madness check - use a credit note to give money away!*/
				$StandardCost = 0;
			}else{
				$StandardCost = $this->LineItems[($LineNumber)]->StandardCost;
			}
			$this->ItemsOrdered++;

			if ($UpdateDB=='Yes'){
				/*ExistingOrder !=0 set means that an order is selected or created for entry
				of items - ExistingOrder is set to 0 in scripts that should not allow
				adding items to the order - New orders have line items added at the time of
				committing the order to the DB in DeliveryDetails.php
				 GET['ModifyOrderNumber'] is only set when the items are first
				being retrieved from the DB - dont want to add them again - would return
				errors anyway */

				global $db;
				$sql = "INSERT INTO salesorderdetails (orderlineno,
									orderno,
									stkcode,
									quantity,
									unitprice,
									discountpercent,
									itemdue,
									poline,
									rh_cost,
									description,
                                    pricefactor)
								VALUES(" . $this->LineCounter . ",
									" . $_SESSION['ExistingOrder'] . ",
									'" . trim(strtoupper($StockID)) ."',
									" . $Qty . ",
									" . $Price . ",
									" . $Disc . ",'
									" . $ItemDue . "','
									" . $POLine . "',
									" . $StandardCost . ",
									'" . $Descr . "',
                                    ".$Price*$Factor.")";
				$result = DB_query($sql,
							$db ,
							_('The order line for') . ' ' . strtoupper($StockID) . ' ' ._('could not be inserted'));
 //*****************Descuentos multiples ***********************************************
        $D1=(($D1)/100);
		$monto=($D1*$Price);
        $StartOf_LineItemsSQL = "INSERT INTO rh_descuentos (
						type,
						transno,
						orderlineno,
						tipo_descuento,
						descuento,
						monto,
						cant)
					VALUES (";
        $LineItemsSQL = $StartOf_LineItemsSQL .
					"30,
					" . $_SESSION['ExistingOrder'] . ",
					".$this->LineCounter."
					,1 ,
					'". $D1 . "',
					'" . $monto . "',
					".$Qty."
				)";
		$Ins_LineItemResult = DB_query($LineItemsSQL,$db,'Imposible insertar articulo','',true);

	   $StartOf_LineItemsSQL = "INSERT INTO rh_descuentos (
						type,
						transno,
						orderlineno,
						tipo_descuento,
						descuento,
						monto,
						cant)
					VALUES (";
		$D1=(($D1)/100);
		$monto_D1=($D1*$Price);

		$st=($Price)-$monto_D1;

		$D2=(($D2)/100);
		$monto=($D2*$st);

		$LineItemsSQL = $StartOf_LineItemsSQL .
					"30,
					" . $_SESSION['ExistingOrder'] . ",
					".$this->LineCounter."
					,2 ,
					'". $D2 . "',
					'" . $monto . "',
					".$Qty."
				)";
		$Ins_LineItemResult = DB_query($LineItemsSQL,$db,'Imposible insertar articulo','',true);
        //*************************************************************************************

			}

			$this->LineCounter = $LineNumber + 1;
			Return 1;
		}
		Return 0;
	}

	function update_cart_item( $UpdateLineNumber, $Qty, $Price, $Disc, $Narrative, $UpdateDB='No', $ItemDue='', $POLine='',$Factor=1,$D1=0,$D2=0){

		if ($Qty>0){
			$this->LineItems[$UpdateLineNumber]->Quantity = $Qty;
		}
		$FactorPrecioDescuento=1;
		if($Price<0)
			$FactorPrecioDescuento=-1;
		$Price2=abs($Price);
	if($_SESSION['Items']->LineItems[$UpdateLineNumber]->LineOrderExist == 1){
			if($Price2 < $_SESSION['Items']->LineItems[$UpdateLineNumber]->OldPrice){
				if($Price2 < $_SESSION['Items']->LineItems[$UpdateLineNumber]->MiniumPrice){
					$Price = $_SESSION['Items']->LineItems[$UpdateLineNumber]->OldPrice*$FactorPrecioDescuento;
				}
			}
		}
		
		// bowikaxu realhost Feb 2008 - verify due date
		if(strlen($ItemDue) <= 1){
			$ItemDue = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', 1);
		}
		$this->LineItems[$UpdateLineNumber]->Price = $Price;
        $this->LineItems[$UpdateLineNumber]->Factor = $Factor;
		$this->LineItems[$UpdateLineNumber]->DiscountPercent = $Disc;
		$this->LineItems[$UpdateLineNumber]->Narrative = $Narrative;
		$this->LineItems[$UpdateLineNumber]->ItemDue = $ItemDue;
		$this->LineItems[$UpdateLineNumber]->POLine = $POLine;
        $this->LineItems[$UpdateLineNumber]->Discount1 = $D1;
        $this->LineItems[$UpdateLineNumber]->Discount2 = $D2;
		// bowikaxu realhost
		// bowikaxu realhost - Set Standard Cost
		if ($this->LineItems[$UpdateLineNumber]->StandardCost<0){ 
			$this->LineItems[$UpdateLineNumber]->StandardCost=0;
		}

		if ($UpdateDB=='Yes'){
			global $db;
			$result = DB_query("UPDATE salesorderdetails
						SET quantity=" . $Qty . ",
						unitprice=" . $Price . ",
						discountpercent=" . $Disc . ",
						narrative ='" . DB_escape_string($Narrative) . "',
						itemdue = '" . $ItemDue . "',
						poline = '" . DB_escape_string($POLine) . "',
						rh_cost=" . $this->LineItems[$UpdateLineNumber]->StandardCost . ",
                        pricefactor= ".$Price*$Factor."
					WHERE orderno=" . $_SESSION['ExistingOrder'] . "
					AND orderlineno=" . $UpdateLineNumber
				, $db
				, _('The order line number') . ' ' . $UpdateLineNumber .  ' ' . _('could not be updated'));

        $D1=(($D1)/100);
		$monto=($D1*$Price);

			$result = DB_query("update rh_descuentos set
            descuento=".$D1.",
            monto=".$monto.",
            cant=".$Qty."
            where type=30 and tipo_descuento=1 and transno=" . $_SESSION['ExistingOrder'] . " and orderlineno =" . $UpdateLineNumber . ""
				, $db
				, _('The order line number') . ' ' . $UpdateLineNumber .  ' ' . _('could not be updated'));

		$D1=(($D1)/100);
		$monto_D1=($D1*$Price);

		$st=($Price)-$monto_D1;

		$D2=(($D2)/100);
		$monto=($D2*$st);

			$result = DB_query("update rh_descuentos set
            descuento=".$D2.",
            monto=".$monto.",
            cant=".$Qty."
            where type=30 and tipo_descuento=2 and transno=" . $_SESSION['ExistingOrder'] . " and orderlineno =" . $UpdateLineNumber . ""
				, $db
				, _('The order line number') . ' ' . $UpdateLineNumber .  ' ' . _('could not be updated'));
		}
	}

	function remove_from_cart($LineNumber, $UpdateDB='No'){

		if (!isset($LineNumber) || $LineNumber=='' || $LineNumber < 0){ /* over check it */
			prnMsg(_('No Line Number passed to remove_from_cart, so nothing has been removed.'), 'error');
			return;
		}
		if ($UpdateDB=='Yes'){
			global $db;
			if ($this->Some_Already_Delivered($LineNumber)==0){
				/* nothing has been delivered, delete it. */
				$result = DB_query("DELETE FROM salesorderdetails
							WHERE orderno=" . $_SESSION['ExistingOrder'] . "
							AND orderlineno=" . $LineNumber,
							$db,
							_('The order line could not be deleted because')
							);

  				$result = DB_query("DELETE FROM rh_descuentos
							WHERE  type=30 and tipo_descuento=2 and transno=" . $_SESSION['ExistingOrder'] . "
							AND orderlineno=" . $LineNumber,
							$db,
							_('The order line could not be deleted because')
							);
				prnMsg( _('Deleted Line Number'). ' ' . $LineNumber . ' ' . _('from existing Order Number').' ' . $_SESSION['ExistingOrder'], 'success');
			} else {
				/* something has been delivered. Clear the remaining Qty and Mark Completed */
				$result = DB_query("UPDATE salesorderdetails SET quantity=qtyinvoiced, completed=1
									WHERE orderno=".$_SESSION['ExistingOrder']." AND orderlineno=" . $LineNumber ,
									$db,
								   _('The order line could not be updated as completed because')
								   );
				prnMsg(_('Removed Remaining Quantity and set Line Number '). ' ' . $LineNumber . ' ' . _('as Completed for existing Order Number').' ' . $_SESSION['ExistingOrder'], 'success');
			}
		}
		/* Since we need to check the LineItem above and might affect the DB, don't unset until after DB is updates occur */
		unset($this->LineItems[$LineNumber]);
		$this->ItemsOrdered--;

	}//remove_from_cart()

	function Get_StockID_List(){
		/* Makes a comma seperated list of the stock items ordered
		for use in SQL expressions */

		$StockID_List="";
		foreach ($this->LineItems as $StockItem) {
			$StockID_List .= ",'" . $StockItem->StockID . "'";
		}

		return substr($StockID_List, 1);

	}

	function Any_Already_Delivered(){
		/* Checks if there have been deliveries of line items */

		foreach ($this->LineItems as $StockItem) {
			if ($StockItem->QtyInv !=0){
				return 1;
			}
		}

		return 0;

	}

	function Some_Already_Delivered($LineNumber){
		/* Checks if there have been deliveries of a specific line item */

		if ($this->LineItems[$LineNumber]->QtyInv !=0){
			return 1;
		}
		return 0;
	}

	function AllDummyLineItems(){
		foreach ($this->LineItems as $StockItem) {
			if($StockItem->MBflag !='D'){
				return false;
			}
		}
		return true;
	}

	function GetExistingTaxes($LineNumber, $stkmoveno){

		global $db;

		/*Gets the Taxes and rates applicable to this line from the TaxGroup of the branch and TaxCategory of the item
		and the taxprovince of the dispatch location */

		$sql = 'SELECT stockmovestaxes.taxauthid,
				taxauthorities.description,
				taxauthorities.taxglcode,
				stockmovestaxes.taxcalculationorder,
				stockmovestaxes.taxontax,
				stockmovestaxes.taxrate
			FROM stockmovestaxes INNER JOIN taxauthorities
				ON stockmovestaxes.taxauthid = taxauthorities.taxid
			WHERE stkmoveno = ' . $stkmoveno . '
			ORDER BY taxcalculationorder';

		$ErrMsg = _('The taxes and rates for this item could not be retreived because');
		$GetTaxRatesResult = DB_query($sql,$db,$ErrMsg);

		while ($myrow = DB_fetch_array($GetTaxRatesResult)){

			$this->LineItems[$LineNumber]->Taxes[$myrow['taxcalculationorder']] =
								  new Tax($myrow['taxcalculationorder'],
										$myrow['taxauthid'],
										$myrow['description'],
										$myrow['taxrate'],
										$myrow['taxontax'],
										$myrow['taxglcode']);
		}
	} //end method GetExistingTaxes

	function GetTaxes($LineNumber){

		global $db;

		/*Gets the Taxes and rates applicable to this line from the TaxGroup of the branch and TaxCategory of the item
		and the taxprovince of the dispatch location */

		$SQL = "SELECT taxgrouptaxes.calculationorder,
					taxauthorities.description,
					taxgrouptaxes.taxauthid,
					taxauthorities.taxglcode,
					taxgrouptaxes.taxontax,
					taxauthrates.taxrate
			FROM taxauthrates INNER JOIN taxgrouptaxes ON
				taxauthrates.taxauthority=taxgrouptaxes.taxauthid
				INNER JOIN taxauthorities ON
				taxauthrates.taxauthority=taxauthorities.taxid
			WHERE taxgrouptaxes.taxgroupid=" . $this->TaxGroup . "
			AND taxauthrates.dispatchtaxprovince=" . $this->DispatchTaxProvince . "
			AND taxauthrates.taxcatid = " . $this->LineItems[$LineNumber]->TaxCategory . "
			ORDER BY taxgrouptaxes.calculationorder";

		$ErrMsg = _('The taxes and rates for this item could not be retreived because');
		$GetTaxRatesResult = DB_query($SQL,$db,$ErrMsg);

		while ($myrow = DB_fetch_array($GetTaxRatesResult)){

			$this->LineItems[$LineNumber]->Taxes[$myrow['calculationorder']] = new Tax($myrow['calculationorder'],
													$myrow['taxauthid'],
													$myrow['description'],
													$myrow['taxrate'],
													$myrow['taxontax'],
													$myrow['taxglcode']);
		}
	} //end method GetTaxes

	function GetFreightTaxes () {

		global $db;
		/*Gets the Taxes and rates applicable to the freight based on the tax group of the branch combined with the tax category for this particular freight
		and SESSION['FreightTaxCategory'] the taxprovince of the dispatch location */

		$sql = "SELECT taxcatid FROM taxcategories WHERE taxcatname='Freight'";
		$TaxCatQuery = DB_query($sql, $db);

		if ($TaxCatRow = DB_fetch_array($TaxCatQuery)) {
		  $TaxCatID = $TaxCatRow['taxcatid'];
		} else {
  		  prnMsg( _('Cannot find tax category Freight which must always be defined'),'error');
		  exit();
		}

		$SQL = "SELECT taxgrouptaxes.calculationorder,
					taxauthorities.description,
					taxgrouptaxes.taxauthid,
					taxauthorities.taxglcode,
					taxgrouptaxes.taxontax,
					taxauthrates.taxrate
			FROM taxauthrates INNER JOIN taxgrouptaxes ON
				taxauthrates.taxauthority=taxgrouptaxes.taxauthid
				INNER JOIN taxauthorities ON
				taxauthrates.taxauthority=taxauthorities.taxid
			WHERE taxgrouptaxes.taxgroupid=" . $this->TaxGroup . "
			AND taxauthrates.dispatchtaxprovince=" . $this->DispatchTaxProvince . "
			AND taxauthrates.taxcatid = " . $TaxCatID . "
			ORDER BY taxgrouptaxes.calculationorder";

		$ErrMsg = _('The taxes and rates for this item could not be retreived because');
		$GetTaxRatesResult = DB_query($SQL,$db,$ErrMsg);

		while ($myrow = DB_fetch_array($GetTaxRatesResult)){

			$this->FreightTaxes[$myrow['calculationorder']] = new Tax($myrow['calculationorder'],
											$myrow['taxauthid'],
											$myrow['description'],
											$myrow['taxrate'],
											$myrow['taxontax'],
											$myrow['taxglcode']);
		}
	} //end method GetFreightTaxes()

} /* end of cart class defintion */

Class LineDetails {
	Var $LineNumber;
	Var $StockID;
	Var $ItemDescription;
	Var $Quantity;
	Var $Price;
    var $Factor;
	Var $DiscountPercent;
    //** Descuentos multiples
    var $Discount1;
    var $Discount2;
    /**************************/
	Var $Units;
	Var $Volume;
	Var $Weight;
	Var $ActDispDate;
	Var $QtyInv;
	Var $QtyDispatched;
	Var $StandardCost;
	Var $QOHatLoc;
	Var $MBflag;	/*Make Buy Dummy, Assembly or Kitset */
	Var $DiscCat; /* Discount Category of the item if any */
	Var $Controlled;
	Var $Serialised;
	Var $DecimalPlaces;
	Var $SerialItems;
    Var $PedimentoItems; //Array de pedimentos
	Var $Narrative;
	Var $TaxCategory;
	Var $Taxes;
	Var $WorkOrderNo;
	Var $ItemDue;
	Var $POLine;
	Var $MiniumPrice;
	Var $OldPrice;
	Var $LineOrderExist;

	Var $rh_Sample; // bowikaxu realhost - item is a sample, bypass price verification

	function LineDetails ($LineNumber,
				$StockItem,
				$Descr,
				$Qty,
				$Prc,
				$DiscPercent,
				$UOM,
				$Volume,
				$Weight,
				$QOHatLoc,
				$MBflag,
				$ActDispatchDate,
				$QtyInvoiced,
				$DiscCat,
				$Controlled,
				$Serialised,
				$DecimalPlaces,
				$Narrative,
				$TaxCategory,
				$ItemDue,
				$POLine,$Factor=1,$D1,$D2,$MiniumPrice = 0 , $OldPrice = 0){

/* Constructor function to add a new LineDetail object with passed params */
		$this->LineNumber = $LineNumber;
		$this->StockID =$StockItem;
		$this->ItemDescription = $Descr;
		$this->Quantity = $Qty;
		$this->Price = $Prc;
		$this->DiscountPercent = $DiscPercent;
		$this->Units = $UOM;
		$this->Volume = $Volume;
		$this->Weight = $Weight;
		$this->ActDispDate = $ActDispatchDate;
		$this->QtyInv = $QtyInvoiced;
		if ($Controlled==1){
			$this->QtyDispatched = 0;
		} else {
			$this->QtyDispatched = $Qty - $QtyInvoiced;
		}
		$this->QOHatLoc = $QOHatLoc;
		$this->MBflag = $MBflag;
		$this->DiscCat = $DiscCat;
		$this->Controlled = $Controlled;
		$this->Serialised = $Serialised;
		$this->DecimalPlaces = $DecimalPlaces;
		$this->SerialItems = array();
        $this->PedimentoItems = array();//Array de pedimentos 
		$this->Narrative = $Narrative;
		$this->Taxes = array();
		$this->TaxCategory = $TaxCategory;
		$this->WorkOrderNo = 0;
		$this->ItemDue = $ItemDue;
		$this->POLine = $POLine;
        $this->Factor=$Factor;
        $this->Discount1=$D1;
        $this->Discount2=$D2;
        $this->MiniumPrice = $MiniumPrice;
        $this->OldPrice    = $OldPrice;
        

		// bowikaxu realhost - item is a sample, default no
		$this->rh_Sample = 0;
		
	} //end constructor function for LineDetails

}

Class Tax {
	Var $TaxCalculationOrder;  /*the index for the array */
	Var $TaxAuthID;
	Var $TaxAuthDescription;
	Var $TaxRate;
	Var $TaxOnTax;
	var $TaxGLCode;

	function Tax ($TaxCalculationOrder,
			$TaxAuthID,
			$TaxAuthDescription,
			$TaxRate,
			$TaxOnTax,
			$TaxGLCode){

		$this->TaxCalculationOrder = $TaxCalculationOrder;
		$this->TaxAuthID = $TaxAuthID;
		$this->TaxAuthDescription = $TaxAuthDescription;
		$this->TaxRate =  $TaxRate;
		$this->TaxOnTax = $TaxOnTax;
		$this->TaxGLCode = $TaxGLCode;
	}
}

?>
