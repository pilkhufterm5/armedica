<?php 


// ARCHIVO CON FUNCIONES PARA LOS WEBSERVICES

/** 
		Create a sales order to the suppliers ERP based on a purchase order made on your ERP.
		Returns:
			@param string $exchange_id
			@param array $products
			@param double $total
			@return array
	*/
	function create_order($exchange_id, $products, /*$branchcode,*/ $total){
		
	global $db;

	$_SESSION['Items'] = new Cart;
	$today = Date("Y-m-d H:i");

	$OrderHeaderSQL = "SELECT
					custbranch.debtorno,
					debtorsmaster.name,
					debtorsmaster.salestype,
					custbranch.branchcode,
					custbranch.brname,
					custbranch.braddress1,
					custbranch.braddress2,
					custbranch.braddress3,
					custbranch.braddress4,
					custbranch.braddress5,
					custbranch.braddress6,
					custbranch.phoneno,
					custbranch.email,
					debtorsmaster.currcode,
					custbranch.defaultlocation,
					locations.taxprovinceid,
					custbranch.taxgroupid,
					currencies.rate as currency_rate,
					custbranch.defaultshipvia
			FROM
				debtorsmaster,
				custbranch,
				currencies,
				locations
			WHERE custbranch.debtorno = debtorsmaster.debtorno
			AND custbranch.branchcode ='".$customer_id."'
			AND custbranch.debtorno = '".$_SESSION['CustomerID']."'
			AND locations.loccode=custbranch.defaultlocation
			AND debtorsmaster.currcode = currencies.currabrev
			";

	$result = DB_query($OrderHeaderSQL, $db);
	$myrow = DB_fetch_array($result);

	$_SESSION['Items']->DebtorNo = $myrow['debtorno'];
	//$_SESSION['Items']->OrderNo = $myrow['orderno'];
	$_SESSION['Items']->Branch = $myrow['branchcode'];
	$_SESSION['Items']->CustomerName = $myrow['name'];
	$_SESSION['Items']->CustRef = '';
	$_SESSION['Items']->Comments = '';
	$_SESSION['Items']->DefaultSalesType =$myrow['salestype'];
	$_SESSION['Items']->DefaultCurrency = $myrow['currcode'];
	//$BestShipper = $myrow['defaultshipvia'];
	$_SESSION['Items']->ShipVia = $myrow['defaultshipvia'];

	//if (is_null($BestShipper)){
	//   $BestShipper=0;
	//}
	$_SESSION['Items']->DeliverTo = $myrow['brname'];
	$_SESSION['Items']->DeliveryDate = ConvertSQLDate($today);
	$_SESSION['Items']->BrAdd1 = $myrow['braddress1'];
	$_SESSION['Items']->BrAdd2 = $myrow['braddress2'];
	$_SESSION['Items']->BrAdd3 = $myrow['braddress3'];
	$_SESSION['Items']->BrAdd4 = $myrow['braddress4'];
	$_SESSION['Items']->BrAdd5 = $myrow['braddress5'];
	$_SESSION['Items']->BrAdd6 = $myrow['braddress6'];
	$_SESSION['Items']->PhoneNo = $myrow['phoneno'];
	$_SESSION['Items']->Email = $myrow['email'];
	$_SESSION['Items']->Location = $myrow['defaultlocation'];
	$_SESSION['Items']->FreightCost = 0;
	$_SESSION['Old_FreightCost'] = 0;
	//$_POST['ChargeFreightCost'] = $_SESSION['Old_FreightCost'];
	$_SESSION['Items']->Orig_OrderDate = $today;
	$_SESSION['CurrencyRate'] = $myrow['currency_rate'];
	$_SESSION['Items']->TaxGroup = $myrow['taxgroupid'];
	$_SESSION['Items']->DispatchTaxProvince = $myrow['taxprovinceid'];
	//$_SESSION['Items']->GetFreightTaxes();
	$_SESSION['Items']->Quotation = 0;
	$_SESSION['Items']->DeliverBlind = 1;

	for($i = 0; $i < count($products); $i++) {
		// check quantity
		$result = DB_query("SELECT SUM(locstock.quantity) AS quantity
								FROM locstock, custbranch, stockmaster
								WHERE locstock.stockid=stockmaster.stockid
								AND locstock.loccode =custbranch.defaultlocation
								AND stockmaster.stockid='".$products[$i]['ProductId']."'
								AND custbranch.branchcode='".$customer_id."'
								AND custbranch.debtorno='".$_SESSION['CustomerID']."'
								GROUP BY locstock.stockid",$db);
		$myrow = DB_fetch_row($result);

		if($myrow[0] < $products[$i]['Quantity']) {
			return false;
		}

		// add to cart
		$LineItemsSQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.controlled,
					stockmaster.serialised,
					stockmaster.volume,
					stockmaster.kgs,
					stockmaster.units,
					stockmaster.decimalplaces,
					stockmaster.mbflag,
					stockmaster.taxcatid,
					stockmaster.discountcategory,
					stockmaster.materialcost +
						stockmaster.labourcost +
						stockmaster.overheadcost AS standardcost
				FROM stockmaster
				WHERE stockmaster.stockid ='" . $products[$i]['ProductId'] . "'";

		$LineItemsResult = DB_query($LineItemsSQL,$db);

		if (db_num_rows($LineItemsResult)>0) {
			$myrow=db_fetch_array($LineItemsResult);
			$price = GetPrice ($myrow['stockid'], $_SESSION['CustomerID'] ,'', $db);
			$discount = 0;

			if ( $myrow['discountcategory'] != "" ){
				$result = DB_query("SELECT MAX(discountrate) AS discount
								FROM discountmatrix
								WHERE salestype='" .  $_SESSION['Items']->DefaultSalesType . "'
									AND discountcategory ='" . $myrow['discountcategory'] . "'
									AND quantitybreak < 1",$db);
				$myrow1 = DB_fetch_row($result);
				if ($myrow1[0] != "" && $myrow1[0] > 0) {
					$discount = $myrow1[0];
				}
			}

			$_SESSION['Items']->add_to_cart($myrow['stockid'],
			$products[$i]['Quantity'],
			$myrow['description'],
			$price,
			$discount,
			$myrow['units'],
			$myrow['volume'],
			$myrow['kgs'],
			0,
			$myrow['mbflag'],
			$today,
			$products[$i]['Quantity'],
			$myrow['discountcategory'],
			$myrow['controlled'],
			$myrow['serialised'],
			$myrow['decimalplaces'],
			'',
			'No',
			0,
			$myrow['taxcatid']);	/*NB NO Updates to DB */

			$_SESSION['Items']->LineItems[($_SESSION['Items']->LineCounter -1)]->StandardCost = $myrow['standardcost'];

			/*Calculate the taxes applicable to this line item from the customer branch Tax Group and Item Tax Category */

			$_SESSION['Items']->GetTaxes($_SESSION['Items']->LineCounter -1);
		}
	}

	$DelDate = FormatDateforSQL($_SESSION['Items']->DeliveryDate);

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
				'". DB_escape_string($_SESSION['Items']->Comments) .' - '.DB_escape_string($sale_num)."',
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
				" . $_SESSION['Items']->Quotation . ",
				" . $_SESSION['Items']->DeliverBlind ."
                )";

	$InsertQryResult = DB_query($HeaderSQL,$db);

	$OrderNo = DB_Last_Insert_ID($db,'salesorders','orderno');

	$_SESSION['Items']->total = 0;
	$_SESSION['Items']->totalVolume = 0;
	$_SESSION['Items']->totalWeight = 0;
	$TaxTotal =0;
	$TaxTotals = array();
	$TaxGLCodes = array();
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

		$Ins_LineItemResult = DB_query($LineItemsSQL, $db);

		$LineTotal = $StockItem->Quantity * $StockItem->Price * (1 - $StockItem->DiscountPercent);
		$_SESSION['Items']->total = $_SESSION['Items']->total + $LineTotal;
		$_SESSION['Items']->totalVolume = $_SESSION['Items']->totalVolume + ($StockItem->Quantity * $StockItem->Volume);
		$_SESSION['Items']->totalWeight = $_SESSION['Items']->totalWeight + ($StockItem->Quantity * $StockItem->Weight);
		$TaxLineTotal =0; //initialise tax total for the line

		foreach ($StockItem->Taxes AS $Tax) {

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
	} /* inserted line items into sales order details */
	/*Now Get the next invoice number - function in SQL_CommonFunctions*/
	$DefaultDispatchDate = Date("Y-m-d H:i");
	$InvoiceNo = GetNextTransNo(10, $db);
	$PeriodNo = GetPeriod($DefaultDispatchDate, $db);

	/*Start an SQL transaction */

	$SQL = "BEGIN";
	$Result = DB_query($SQL,$db);
	$DefaultDispatchDate = FormatDateForSQL($DefaultDispatchDate);
	/*Update order header for invoice charged on */
	$SQL = "UPDATE salesorders SET comments = CONCAT(comments,' Inv ','" . $InvoiceNo . "') WHERE orderno= " . $OrderNo;

	$Result = DB_query($SQL,$db);

	/*Now insert the DebtorTrans */

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
			ovgst,
			ovfreight,
			rate,
			invtext,
			shipvia,
			consignment
			)
		VALUES (
			". $InvoiceNo . ",
			10,
			'" . $_SESSION['CustomerID'] . "',
			'" . $customer_id . "',
			'" . $DefaultDispatchDate . "',
			" . $PeriodNo . ",
			'',
			'" . $_SESSION['Items']->DefaultSalesType . "',
			" . $OrderNo . ",
			" . $_SESSION['Items']->total . ",
			" . $TaxTotal . ",
			0,
			" . $_SESSION['CurrencyRate'] . ",
			'".$sale_num."',
			" . $_SESSION['Items']->ShipVia . ",
			''
		)";


	$Result = DB_query($SQL,$db);

	$DebtorTransID = DB_Last_Insert_ID($db,'debtortrans','id');

	/* Insert the tax totals for each tax authority where tax was charged on the invoice */
	foreach ($TaxTotals AS $TaxAuthID => $TaxAmount) {

		$SQL = 'INSERT INTO debtortranstaxes (debtortransid,
							taxauthid,
							taxamount)
				VALUES (' . $DebtorTransID . ',
					' . $TaxAuthID . ',
					' . $TaxAmount/$_SESSION['CurrencyRate'] . ')';

		$Result = DB_query($SQL,$db);
	}

	foreach ($_SESSION['Items']->LineItems as $OrderLine) {
		/* Update location stock records if not a dummy stock item
		need the MBFlag later too so save it to $MBFlag */
		$Result = DB_query("SELECT mbflag FROM stockmaster WHERE stockid = '" . $OrderLine->StockID . "'",$db);

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
			//$ErrMsg = _('WARNING') . ': ' . _('Could not retrieve current location stock');
			$Result = DB_query($SQL, $db);

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

			//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated because');
			//$DbgMsg = _('The following SQL to update the location stock record was used');
			$Result = DB_query($SQL,$db);

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

			//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not retrieve assembly components from the database for'). ' '. $OrderLine->StockID . _('because').' ';
			//$DbgMsg = _('The SQL that failed was');
			$AssResult = DB_query($SQL,$db);

			while ($AssParts = DB_fetch_array($AssResult,$db)){

				$StandardCost += ($AssParts['standard'] * $AssParts['quantity']) ;
				/* Need to get the current location quantity
				will need it later for the stock movement */
				$SQL="SELECT locstock.quantity
						FROM locstock
						WHERE locstock.stockid='" . $AssParts['component'] . "'
						AND loccode= '" . $_SESSION['Items']->Location . "'";

				//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Can not retrieve assembly components location stock quantities because ');
				//$DbgMsg = _('The SQL that failed was');
				$Result = DB_query($SQL,$db);
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
							 10,
							 " . $InvoiceNo . ",
							 '" . $_SESSION['Items']->Location . "',
							 '" . $DefaultDispatchDate . "',
							 '" . $_SESSION['Items']->DebtorNo . "',
							 '" . $_SESSION['Items']->Branch . "',
							 " . $PeriodNo . ",
							 '" . _('Assembly') . ': ' . $OrderLine->StockID . ' ' . _('Order') . ': ' . $OrderNo . "',
							 " . -$AssParts['quantity'] * $OrderLine->QtyDispatched . ",
							 " . $AssParts['standard'] . ",
							 0,
							 " . ($QtyOnHandPrior -($AssParts['quantity'] * $OrderLine->QtyDispatched)) . "
						)";
				//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records for the assembly components of'). ' '. $OrderLine->StockID . ' ' . _('could not be inserted because');
				//$DbgMsg = _('The following SQL to insert the assembly components stock movement records was used');
				$Result = DB_query($SQL,$db);


				$SQL = "UPDATE locstock
						SET quantity = locstock.quantity - " . $AssParts['quantity'] * $OrderLine->QtyDispatched . "
						WHERE locstock.stockid = '" . $AssParts['component'] . "'
						AND loccode = '" . $_SESSION['Items']->Location . "'";

				//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated for an assembly component because');
				//$DbgMsg = _('The following SQL to update the locations stock record for the component was used');
				$Result = DB_query($SQL,$db);
			} /* end of assembly explosion and updates */

			/*Update the cart with the recalculated standard cost from the explosion of the assembly's components*/
			$_SESSION['Items']->LineItems[$OrderLine->LineNumber]->StandardCost = $StandardCost;
			$OrderLine->StandardCost = $StandardCost;
		} /* end of its an assembly */

		// Insert stock movements - with unit cost
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
						10,
						" . $InvoiceNo . ",
						'" . $_SESSION['Items']->Location . "',
						'" . $DefaultDispatchDate . "',
						'" . $_SESSION['Items']->DebtorNo . "',
						'" . $_SESSION['Items']->Branch . "',
						" . $LocalCurrencyPrice . ",
						" . $PeriodNo . ",
						'" . $OrderNo . "',
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
						10,
						" . $InvoiceNo . ",
						'" . $_SESSION['Items']->Location . "',
						'" . $DefaultDispatchDate . "',
						'" . $_SESSION['Items']->DebtorNo . "',
						'" . $_SESSION['Items']->Branch . "',
						" . $LocalCurrencyPrice . ",
						" . $PeriodNo . ",
						'" . $OrderNO . "',
						" . -$OrderLine->QtyDispatched . ",
						" . $OrderLine->DiscountPercent . ",
						" . $OrderLine->StandardCost . ",
						'" . addslashes($OrderLine->Narrative) . "')";
		}


		//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records could not be inserted because');
		//$DbgMsg = _('The following SQL to insert the stock movement records was used');
		$Result = DB_query($SQL,$db);

		/*Get the ID of the StockMove... */
		$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');

		/*Insert the taxes that applied to this line */
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

			//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Taxes and rates applicable to this invoice line item could not be inserted because');
			//$DbgMsg = _('The following SQL to insert the stock movement tax detail records was used');
			$Result = DB_query($SQL,$db);
		}


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

				//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be updated because');
				//$DbgMsg = _('The following SQL to update the serial stock item record was used');
				$Result = DB_query($SQL, $db);

				/* now insert the serial stock movement */

				$SQL = "INSERT INTO stockserialmoves (stockmoveno,
										stockid,
										serialno,
										moveqty)
						VALUES (" . $StkMoveNo . ",
							'" . $OrderLine->StockID . "',
							'" . $Item->BundleRef . "',
							" . -$Item->BundleQty . ")";

				//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
				//$DbgMsg = _('The following SQL to insert the serial stock movement records was used');
				$Result = DB_query($SQL, $db);
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

		//$ErrMsg = _('The count of existing Sales analysis records could not run because');
		//$DbgMsg = '<P>'. _('SQL to count the no of sales analysis records');
		$Result = DB_query($SQL,$db);

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

		//$ErrMsg = _('Sales analysis record could not be added or updated because');
		//$DbgMsg = _('The following SQL to insert the sales analysis record was used');
		$Result = DB_query($SQL,$db);

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
						10,
						" . $InvoiceNo . ",
						'" . $DefaultDispatchDate . "',
						" . $PeriodNo . ",
						" . GetCOGSGLAccount($Area, $OrderLine->StockID, $_SESSION['Items']->DefaultSalesType, $db) . ",
						'" . $_SESSION['Items']->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->QtyDispatched . " @ " . $OrderLine->StandardCost . "',
						" . $OrderLine->StandardCost * $OrderLine->QtyDispatched . "
					)";

			//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The cost of sales GL posting could not be inserted because');
			//$DbgMsg = _('The following SQL to insert the GLTrans record was used');
			$Result = DB_query($SQL,$db);

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
						10,
						" . $InvoiceNo . ",
						'" . $DefaultDispatchDate . "',
						" . $PeriodNo . ",
						" . $StockGLCode['stockact'] . ",
						'" . $_SESSION['Items']->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->QtyDispatched . " @ " . $OrderLine->StandardCost . "',
						" . (-$OrderLine->StandardCost * $OrderLine->QtyDispatched) . "
					)";

			//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock side of the cost of sales GL posting could not be inserted because');
			//$DbgMsg = _('The following SQL to insert the GLTrans record was used');
			$Result = DB_query($SQL,$db);
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
						10,
						" . $InvoiceNo . ",
						'" . $DefaultDispatchDate . "',
						" . $PeriodNo . ",
						" . $SalesGLAccounts['salesglcode'] . ",
						'" . $_SESSION['Items']->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->QtyDispatched . " @ " . $OrderLine->Price . "',
						" . (-$OrderLine->Price * $OrderLine->QtyDispatched/$_SESSION['CurrencyRate']) . "
					)";

			//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales GL posting could not be inserted because');
			//$DbgMsg = '<BR>' ._('The following SQL to insert the GLTrans record was used');
			$Result = DB_query($SQL,$db);

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
							10,
							" . $InvoiceNo . ",
							'" . $DefaultDispatchDate . "',
							" . $PeriodNo . ",
							" . $SalesGLAccounts['discountglcode'] . ",
							'" . $_SESSION['Items']->DebtorNo . " - " . $OrderLine->StockID . " @ " . ($OrderLine->DiscountPercent * 100) . "%',
							" . ($OrderLine->Price * $OrderLine->QtyDispatched * $OrderLine->DiscountPercent/$_SESSION['CurrencyRate']) . "
						)";

				//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales discount GL posting could not be inserted because');
				//$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$db);
			} /*end of if discount !=0 */
		} /*end of if sales integrated with debtors */

	} /*Quantity dispatched is more than 0 */
	/*end of OrderLine loop */

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
						10,
						" . $InvoiceNo . ",
						'" . $DefaultDispatchDate . "',
						" . $PeriodNo . ",
						" . $_SESSION['CompanyRecord']['debtorsact'] . ",
						'" . $_SESSION['Items']->DebtorNo . "',
						" . (($_SESSION['Items']->total + $_SESSION['Items']->FreightCost + $TaxTotal)/$_SESSION['CurrencyRate']) . "
						)";

				//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The total debtor GL posting could not be inserted because');
				//$DbgMsg = _('The following SQL to insert the total debtors control GLTrans record was used');
				$Result = DB_query($SQL,$db);
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
					10,
					" . $InvoiceNo . ",
					'" . $DefaultDispatchDate . "',
					" . $PeriodNo . ",
					" . $_SESSION['CompanyRecord']['freightact'] . ",
					'" . $_SESSION['Items']->DebtorNo . "',
					" . (-($_SESSION['Items']->FreightCost)/$_SESSION['CurrencyRate']) . "
					)";

				//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The freight GL posting could not be inserted because');
				//$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$db);
			}
			foreach ( $TaxTotals as $TaxAuthID => $TaxAmount){
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
						10,
						" . $InvoiceNo . ",
						'" . $DefaultDispatchDate . "',
						" . $PeriodNo . ",
						" . $TaxGLCodes[$TaxAuthID] . ",
						'" . $_SESSION['Items']->DebtorNo . "',
						" . (-$TaxAmount/$_SESSION['CurrencyRate']) . "
						)";

					//$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The tax GL posting could not be inserted because');
					//$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$db);
				}
			}
		} /*end of if Sales and GL integrated */

		$SQL='COMMIT';
		$Result = DB_query($SQL,$db);

		if (DB_error_no($db) ==0) {
			return $InvoiceNo;
		} else {
			return false;
		}

		
	}
	
	/**
		Cancel a sales order on your suppliers ERP based on a cancelation on a purchase order made on your ERP.
		Returns:
		@param string $exchange_id
		@param integer $order_id
		@return array
	*/
	function cancel_order($exchange_id, $order_id){
		
	}

	/**
		Create a customers invoice on your suppliers ERP based on a supplier invoice made on your ERP.
		Returns:
			@param string $exchange_id
			@param array $products
			@param double $subtotal
			@param double $tax
			@param double $total
			@return array
	*/
	function create_invoice($exchange_id, $products, /*$branchcode*/ $subtotal, $tax, $total){

	}

	/**
		Cancel a cutomers invoice on your suppliers ERP based on a cancelaton on a purchase invoice made on your ERP.
		Returns:
			@param string $exchange_id
			@param string $invoice_id
			@return array
	*/
	function cancel_invoce($exchange_id, $invoice_id){
		
		return array("exchange_id"=>$exchange_id,"invoice_id"=>$invoice_id);
		
	}
?>