<?php
/**
 * 	REALHOST 17 DE ABRIL DEL 2010
 * 	POS DEL WEBERP
 * 	VERSION 1.0
 * 	RICARDO ABULARACH GARCIA
 * */

/*encargado de procesasr la venta al weberp*/
function get_ivas($taxcatid,$db){
    //$sql = "SELECT taxgrouptaxes.calculationorder, taxauthorities.description, taxgrouptaxes.taxauthid, taxauthorities.taxglcode, taxgrouptaxes.taxontax, taxauthrates.taxrate FROM taxauthrates INNER JOIN taxgrouptaxes ON taxauthrates.taxauthority=taxgrouptaxes.taxauthid INNER JOIN taxauthorities ON taxauthrates.taxauthority=taxauthorities.taxid WHERE taxgrouptaxes.taxgroupid=4 AND taxauthrates.dispatchtaxprovince=2 AND taxauthrates.taxcatid = ".$taxcatid." ORDER BY taxgrouptaxes.calculationorder";
    $sql='SELECT
    taxauthrates.taxrate,
    (taxauthrates.taxrate * 100) as iva
FROM
    custbranch
    INNER JOIN taxgroups
        ON (custbranch.taxgroupid = taxgroups.taxgroupid)
    INNER JOIN taxgrouptaxes
        ON (taxgrouptaxes.taxgroupid = taxgroups.taxgroupid)
    INNER JOIN taxauthrates
        ON (taxgrouptaxes.taxauthid = taxauthrates.taxauthority) where taxauthrates.taxcatid=6 and custbranch.branchcode="'.$_SESSION['rh_pos_principal']['sucursalC'].'" and taxauthrates.taxrate>0;';
    $ivass = DB_query($sql,$db);
    if(DB_num_rows($ivass) == 0) {
        $ivadd = 0;
        $ivaTasa=0;
    }else {
        $myrowd=DB_fetch_array($ivass);
        $ivadd[0] = $myrowd['taxrate'];
        $ivadd[1]=$myrowd['iva'];
    }
    return $ivadd;
}

function genera_venta_weberp(){
    global $db;
    /*variable de session*/
    unset($_SESSION['Items']);
    /*se da de alta la clase con la cual se va a trabajar los datos*/
    $carrito = new Cart;
    $Items = array('Items' => $carrito);
    $_SESSION['Items'] = $Items['Items'];
    /*ya tenemos lo que es el carrito*/
    /*se obtiene los datos del array general*/
    $principal = $_SESSION['rh_pos_principal'];
    /*clientes*/
    $sql_debtorsmasnter = "Select name, address1, address2, address3, address4, address5, address6, address7, address8, address9, address10, rh_tel FROM debtorsmaster where debtorno = '".$principal['cliente']."'";
    $res_debtorsmasnter = DB_query($sql_debtorsmasnter,$db);
    $myrow_debtorsmasnter = DB_fetch_array($res_debtorsmasnter);
    /*location*/
    $sql_location = "Select Sucursal FROM rh_pos_terminales where id = '".$principal['terminal']."'";
    $res_location = DB_query($sql_location,$db);
    $myrow_location = DB_fetch_array($res_location);
    $res_location = $myrow_location['Sucursal'];
    /*valores del cliente*/
    $_SESSION['Items']->DebtorNo = $principal['cliente']; //codigo del cliente que se esta vendiendo
    $_SESSION['Items']->Branch = $principal['sucursalC']; //sucursal del cliente al que le estoy vendiendo
    $_SESSION['Items']->DeliverTo = $myrow_debtorsmasnter['name']; //--> persona a quien se le esta vendiendo
    $_SESSION['Items']->DelAdd1 = $myrow_debtorsmasnter['address1']; //--> direccion del cliente 1
    $_SESSION['Items']->DelAdd2 = $myrow_debtorsmasnter['address2']; //--> direccion del cliente 2
    $_SESSION['Items']->DelAdd3 = $myrow_debtorsmasnter['address3']; //--> direccion del cliente 3
    $_SESSION['Items']->DelAdd4 = $myrow_debtorsmasnter['address4']; //--> direccion del cliente 4
    $_SESSION['Items']->DelAdd5 = $myrow_debtorsmasnter['address5']; //--> direccion del cliente 5
    $_SESSION['Items']->DelAdd6 = $myrow_debtorsmasnter['address6']; //--> direccion del cliente 6
    $_SESSION['Items']->DelAdd7 = $myrow_debtorsmasnter['address7']; //--> direccion del cliente 6
    $_SESSION['Items']->DelAdd8 = $myrow_debtorsmasnter['address8']; //--> direccion del cliente 6
    $_SESSION['Items']->DelAdd9 = $myrow_debtorsmasnter['address9']; //--> direccion del cliente 6
    $_SESSION['Items']->DelAdd10 = $myrow_debtorsmasnter['address10']; //--> direccion del cliente 6
    $_SESSION['Items']->PhoneNo = $myrow_debtorsmasnter['rh_tel']; //--> telefono del cliente
    /*valores dela venta*/
    $_SESSION['Items']->Location = $res_location;  //--> sucursal de donde sale la marcancia
    $_SESSION['Items']->Comments = "RealHost webErp"; //--> comentario de la venta
    /*valores por default*/
    $_SESSION['Items']->Email = "";
    $_SESSION['Items']->CustRef = "";
    $_SESSION['Items']->DefaultSalesType = "L1";
    $_SESSION['Items']->FreightCost = 0;
    $_SESSION['Items']->ShipVia = 1;
    $_SESSION['Items']->Quotation = 0;
    $_SESSION['Items']->DeliverBlind = 1;
    $_SESSION['Items']->DeliveryDate = date("Y-m-d");
    
    /*se complementan los datos del array*/
    /*-- se trabajan los articulos propios --*/
    $items_carrito = $_SESSION['ventas'];
    $total_items = count($items_carrito);
    if($total_items != 0){
        /*cuando si tiene articulos*/
        $el_conta = 0;
        $el_total = 0;
        for($c=0;$c<$total_items;$c++){
            $padres = $items_carrito[$c];
            $lineitems = new LineDetails;
            /*seccion para los articulos*/
            $sql_ite_ms = "Select * from stockmaster where stockid = '".$padres['item']."'";
            $res_ite_ms = DB_query($sql_ite_ms,$db);
            $myrow_ite_ms = DB_fetch_array($res_ite_ms);
            /*se consulta la informacion del articulo*/
            $lineitems->LineNumber = $c; //ok
            $lineitems->StockID = $padres['item']; //ok
            $lineitems->ItemDescription = $myrow_ite_ms['description']; //ok
            $lineitems->Quantity = $padres['cantidad'];
            $lineitems->Price = $padres['precio'];
            //$lineitems->DiscountPercent = $padres['descuento'];
            $lineitems->DiscountPercent = ($padres['descuento'] == "")? "0" : $padres['descuento'];
            $lineitems->Units = $myrow_ite_ms['units'];
            $lineitems->Volume = $myrow_ite_ms['volume'];
            $lineitems->Weight = $myrow_ite_ms['kgs'];
            $lineitems->ActDispDate = NULL;
            $lineitems->QtyInv = 0;
            $lineitems->QtyDispatched = $padres['cantidad']; /*se modifico esta variable para su funcion en el caso de la remision*/
            $lineitems->QOHatLoc = 0;
            $lineitems->MBflag = $myrow_ite_ms['mbflag'];
            $lineitems->DiscCat = $myrow_ite_ms['discountcategory'];
            $lineitems->Controlled = $myrow_ite_ms['controlled'];
            $lineitems->Serialised = $myrow_ite_ms['serialised'];
            $lineitems->DecimalPlaces = $myrow_ite_ms['decimalplaces'];
            $lineitems->SerialItems = array();
            $lineitems->Narrative = "";
            $lineitems->Taxes = array();
            $lineitems->TaxCategory = $myrow_ite_ms['taxcatid'];
            $lineitems->WorkOrderNo = 0;
            $lineitems->ItemDue = "";
            $lineitems->POLine = "";
            $lineitems->StandardCost = $myrow_ite_ms['actualcost'];
            $lineitems->EOQ = 1;
            $lineitems->NextSerialNo = 0;
            $ExRate = 1;
            $lineitems->GPPercent = ((($padres['precio'] * (1 - $padres['descuento'])) - ($myrow_ite_ms['actualcost'] * $ExRate))*100)/$padres['precio'];
            /*fin de la asignacion de los articulos items*/
            $arreglo_lineitems[$c] = $lineitems;
            /*operaciones*/
            $el_conta = $el_conta + 1;
            $operacion1 = $padres['cantidad'] * $padres['precio'];
            $operacion2 = $operacion1-($operacion1*$padres['descuento']);
            $el_total = $el_total + $operacion2;
        }

        /*se hace la asignacion de los datos*/
        $_SESSION['Items']->LineItems = $arreglo_lineitems; //--> arreglo de items
        $_SESSION['Items']->total = $el_total; //--> total de la venta SIN IVA
        $_SESSION['Items']->LineCounter = $el_conta; //--> total de articulos
        $_SESSION['Items']->ItemsOrdered = $el_conta; //--> me suena como que a cuantos articulos
        /*FIN DE LA ASGIANCION DEL ARREGLO*/

        /*************************/
        /*         PEDIDO        */
        /*************************/

        //1ER SQL
        DB_query("BEGIN",$db);

        //$OrderNo = GetNextTransNo(30, $db);

        //2DO SQL
        $DelDate = $_SESSION['Items']->DeliveryDate;
	    /*$HeaderSQL = "INSERT INTO salesorders(orderno, debtorno, branchcode, customerref, comments, orddate, ordertype, shipvia, deliverto, deladd1, deladd2, deladd3, deladd4, deladd5, deladd6, contactphone, contactemail, freightcost, fromstkloc, deliverydate, quotation, deliverblind)
                             VALUES ('".$OrderNo."', '" . $_SESSION['Items']->DebtorNo . "', '" . $_SESSION['Items']->Branch . "', '". DB_escape_string($_SESSION['Items']->CustRef) ."', '". DB_escape_string($_SESSION['Items']->Comments) ."', '" . Date("Y-m-d H:i") . "', '" . $_SESSION['Items']->DefaultSalesType . "', " . $_SESSION['Items']->ShipVia .", '" . DB_escape_string($_SESSION['Items']->DeliverTo) . "', '" . DB_escape_string($_SESSION['Items']->DelAdd1) . "', '" . DB_escape_string($_SESSION['Items']->DelAdd2) . "', '" . DB_escape_string($_SESSION['Items']->DelAdd3) . "', '" . DB_escape_string($_SESSION['Items']->DelAdd4) . "', '" . DB_escape_string($_SESSION['Items']->DelAdd5) . "', '" . DB_escape_string($_SESSION['Items']->DelAdd6) . "', '" . DB_escape_string($_SESSION['Items']->PhoneNo) . "', '" . DB_escape_string($_SESSION['Items']->Email) . "', " . $_SESSION['Items']->FreightCost .", '" . $_SESSION['Items']->Location ."', '" . $DelDate . "', " . $_SESSION['Items']->Quotation . ", " . $_SESSION['Items']->DeliverBlind .")";*/
        $OrderNo = GetNextTransNo(30, $db);
        $HeaderSQL = "INSERT INTO salesorders(orderno, debtorno, branchcode, customerref, comments, orddate, ordertype, shipvia, deliverto, deladd1, deladd2, deladd3, deladd4, deladd5, deladd6, contactphone, contactemail, freightcost, fromstkloc,fromstkloc_virtual, deliverydate, quotation, deliverblind)
                             VALUES ('". $OrderNo . "','" . $_SESSION['Items']->DebtorNo . "', '" . $_SESSION['Items']->Branch . "', '". DB_escape_string($_SESSION['Items']->CustRef) ."', '". DB_escape_string($_SESSION['Items']->Comments) ."', '" . Date("Y-m-d H:i") . "', '" . $_SESSION['Items']->DefaultSalesType . "', " . $_SESSION['Items']->ShipVia .", '" . DB_escape_string($_SESSION['Items']->DeliverTo) . "', '" . DB_escape_string($_SESSION['Items']->DelAdd1) . "', '" . DB_escape_string($_SESSION['Items']->DelAdd2) . "', '" . DB_escape_string($_SESSION['Items']->DelAdd3) . "', '" . DB_escape_string($_SESSION['Items']->DelAdd4) . "', '" . DB_escape_string($_SESSION['Items']->DelAdd5) . "', '" . DB_escape_string($_SESSION['Items']->DelAdd6) . "', '" . DB_escape_string($_SESSION['Items']->PhoneNo) . "', '" . DB_escape_string($_SESSION['Items']->Email) . "', " . $_SESSION['Items']->FreightCost .", '" . $_SESSION['Items']->Location ."','".$_SESSION['Items']->Location."','" . $DelDate . "', " . $_SESSION['Items']->Quotation . ", " . $_SESSION['Items']->DeliverBlind .")";
        $ErrMsg = _('The order cannot be added because');
        $InsertQryResult = DB_query($HeaderSQL,$db,$ErrMsg,'',true);
        //$OrderNo = DB_Last_Insert_ID($db,'salesorders','orderno');
        //$OrderNo = $_SESSION['LastInsertId'];
        
        //3CER SQL
        $date = FormatDateforSQL(date("d-m-Y"));
        $sql = "INSERT INTO rh_usertrans(type, user_, order_, date_) 
                       VALUES (30, '".$_SESSION['UserID']."', ".$OrderNo.", '".$date."')";
        $res = DB_query($sql,$db,'Imposible insertar el usuario','',true);

        //5TO SQL
   /*	$StartOf_LineItemsSQL = "INSERT INTO salesorderdetails(orderlineno, orderno, stkcode, unitprice, quantity, discountpercent, narrative, description, poline, rh_cost, itemdue)
					VALUES (";
        foreach ($_SESSION['Items']->LineItems as $StockItem) {
		$LineItemsSQL = $StartOf_LineItemsSQL .
				$StockItem->LineNumber . ",
				"  . $OrderNo . ",
				'" . $StockItem->StockID . "',
				"  . $StockItem->Price . ",
				"  . $StockItem->Quantity . ",
				"  . floatval($StockItem->DiscountPercent) . ",
				'" . DB_escape_string($StockItem->Narrative) . "',
				'" . DB_escape_string($StockItem->ItemDescription) . "',
				'" . DB_escape_string($StockItem->POLine) . "',
				" . $StockItem->StandardCost . ",
				'" . FormatDateForSQL($StockItem->ItemDue) . "'
				)";
                $Ins_LineItemResult = DB_query($LineItemsSQL,$db,'Imposible insertar articulo','',true);
        }*/
$StartOf_LineItemsSQL = "INSERT INTO salesorderdetails(orderlineno, orderno, stkcode, unitprice, quantity, discountpercent, narrative, rh_cost)
					VALUES (";
        foreach ($_SESSION['Items']->LineItems as $StockItem) {
		$LineItemsSQL = $StartOf_LineItemsSQL .
				$StockItem->LineNumber . ",
				"  . $OrderNo . ",
				'" . $StockItem->StockID . "',
				"  . $StockItem->Price . ",
				"  . $StockItem->Quantity . ",
				"  . floatval($StockItem->DiscountPercent) . ",
				'" . DB_escape_string($StockItem->Narrative) . "',
				" . $StockItem->StandardCost . "
				)";
                $Ins_LineItemResult = DB_query($LineItemsSQL,$db,'Imposible insertar articulo','',true);
        }

        //6TO SQL
        DB_query("COMMIT",$db);

        /*************************/
        /*       REMISION        */
        /*************************/

        $DefaultDispatchDate = date('d/m/Y');
	$InvoiceNo = GetNextTransNo(20000, $db);
	$PeriodNo = GetPeriod($DefaultDispatchDate, $db);

        $sql_cli_e = "Select currencies.rate as currency_rate FROM debtorsmaster, currencies WHERE debtorsmaster.currcode = currencies.currabrev AND debtorsmaster.debtorno = '".$principal['cliente']."'";
        $res_cli_e = DB_query($sql_cli_e,$db);
        $myrow_clie_e = DB_fetch_array($res_cli_e);

        /*prepara variables*/
        $_POST['ChargeFreightCost'] = 0;
        $_POST['InvoiceText'] = "";
        $_POST['Consignment'] = "";
        $_SESSION['ProcessingOrder'] = $OrderNo;
        $_SESSION['CurrencyRate'] = $myrow_clie_e['currency_rate'];
        $_POST['BOPolicy'] = "BO";
        $_POST['RemType'] = 0;  //variable para remisiones identifica el type
        $DefaultDispatchDate = FormatDateForSQL($DefaultDispatchDate);
        /*fin de la preparacion de variables*/

        $Result = DB_query("BEGIN",$db,'ERROR: Imposible comenzar transacciones con al base de datos','Fallo el QUERY: BEGIN',true);

        /*Update order header for invoice charged on */
        $SQL = "UPDATE salesorders SET comments = CONCAT(salesorders.comments,' Rem ','" . $InvoiceNo . "') WHERE orderno= " . $OrderNo;
        $ErrMsg = _('CRITICAL ERROR') . ' ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales order header could not be updated with the shipment number');
	$DbgMsg = _('The following SQL to update the sales order was used');
	$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

        /**/
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
			ovamount,"
	//ovgst,
	."ovfreight,
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
			'" . $DefaultDispatchDate . "',
			"  . $PeriodNo . ",
			'',
			'" . $_SESSION['Items']->DefaultSalesType . "',
			"  . $_SESSION['ProcessingOrder'] . ",
			"  . $_SESSION['Items']->total . ","
            //. $TaxTotal . ","
            . $_POST['ChargeFreightCost'] . ",
			"  . $_SESSION['CurrencyRate'] . ",
			'" . $_POST['InvoiceText'] . "',
			"  . $_SESSION['Items']->ShipVia . ",
			'" . $_POST['Consignment'] . "',
			NOW()
		)";

	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction record could not be inserted because');
	$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
	$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

        /*aki faltan los hijos y los asientos contables*/
        $DebtorTransID = DB_Last_Insert_ID($db,'debtortrans','id');

        /*se tiene que modificar la siguiente variable*/


	/* Insert the tax totals for each tax authority where tax was charged on the invoice */
	/*
	foreach ($TaxTotals AS $TaxAuthID => $TaxAmount) {

	$SQL = 'INSERT INTO debtortranstaxes (debtortransid,
	taxauthid,
	taxamount)
	VALUES (' . $DebtorTransID . ',
	' . $TaxAuthID . ',
	' . $TaxAmount/$_SESSION['CurrencyRate'] . ')';

	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction taxes records could not be inserted because');
	$DbgMsg = _('The following SQL to insert the debtor transaction taxes record was used');
	$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
	}
	*/

	/* If balance of the order cancelled update sales order details quantity. Also insert log records for OrderDeliveryDifferencesLog */

	foreach ($_SESSION['Items']->LineItems as $OrderLine) {


		if ($_POST['BOPolicy']=='CAN'){

			$SQL = "UPDATE salesorderdetails
				SET quantity = quantity - " . ($OrderLine->Quantity - $OrderLine->QtyDispatched) . " WHERE orderno = " . $_SESSION['ProcessingOrder'] . " AND stkcode = '" . $OrderLine->StockID . "' AND orderlineno = ".$OrderLine->LineNumber;

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

		if ($OrderLine->QtyDispatched !=0 AND $OrderLine->QtyDispatched!="" AND $OrderLine->QtyDispatched) {

			// Test above to see if the line is completed or not
			if ($OrderLine->QtyDispatched>=($OrderLine->Quantity - $OrderLine->QtyInv) OR $_POST['BOPolicy']=="CAN"){
				$SQL = "UPDATE salesorderdetails
					SET qtyinvoiced = qtyinvoiced + " . $OrderLine->QtyDispatched . ",
					actualdispatchdate = '" . $DefaultDispatchDate .  "',
					completed=1
					WHERE orderno = " . $_SESSION['ProcessingOrder'] . "
					AND stkcode = '" . $OrderLine->StockID . "' AND orderlineno = ".$OrderLine->LineNumber;
			} else {
				$SQL = "UPDATE salesorderdetails
					SET qtyinvoiced = qtyinvoiced + " . $OrderLine->QtyDispatched . ",
					actualdispatchdate = '" . $DefaultDispatchDate .  "'
					WHERE orderno = " . $_SESSION['ProcessingOrder'] . "
					AND stkcode = '" . $OrderLine->StockID . "' AND orderlineno = ".$OrderLine->LineNumber;

			}

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales order detail record could not be updated because');
			$DbgMsg = _('The following SQL to update the sales order detail record was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

			/* Update location stock records if not a dummy stock item
			need the MBFlag later too so save it to $MBFlag */
			$Result = DB_query("SELECT mbflag FROM stockmaster WHERE stockid = '" . $OrderLine->StockID . "'",$db,"<BR>Can't retrieve the mbflag",'Error: El SQL Fallo',true);

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
				$Result = DB_query($SQL, $db, $ErrMsg,'El SQL que fallo fue',true);

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
							newqoh,
							rh_orderline
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
							 " . ($QtyOnHandPrior -($AssParts['quantity'] * $OrderLine->QtyDispatched)) . ",
							'".$OrderLine->LineNumber."'
						)";
					//" . -$AssParts['quantity'] * $OrderLine->QtyDispatched . ",
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
			$LocalCurrencyPrice= round(($OrderLine->Price / $_SESSION['CurrencyRate']),2); //se modifica ya que no es igual esta variable
                        //$LocalCurrencyPrice= $OrderLine->Price / $_SESSION['CurrencyRate'];

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
						narrative,
							rh_orderline )
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
						'" . $OrderLine->DiscountPercent . "',
						" . $OrderLine->StandardCost . ",
						" . ($QtyOnHandPrior - $OrderLine->QtyDispatched) . ",
						'" . DB_escape_string($OrderLine->Narrative) . "',
							'".$OrderLine->LineNumber."' )";
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
						narrative,
							rh_orderline )
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
						'" . addslashes($OrderLine->Narrative) . "',
							'".$OrderLine->LineNumber."')";
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
                                                        discountpercent,
							price,
							standardcost,
							reference,
							line
						) VALUES (
							'" . $OrderLine->StockID . "',
							 " . $InvoiceNo . ",
							 '" . $_SESSION['Items']->Location . "',
							 '" . $DefaultDispatchDate . "',
							 '" . $_SESSION['Items']->DebtorNo . "',
							 '" . $_SESSION['Items']->Branch . "',
							 " . $OrderLine->QtyDispatched . ",
                                                         '" . $OrderLine->DiscountPercent . "',
							 ". $OrderLine->Price.",
							 ".$OrderLine->StandardCost.",
							 ".$stkid['id'].",
							".$OrderLine->LineNumber.")";
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
			$DbgMsg = '<P>'. _('SQL to count the no of sales analysis records');
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
			if($MBFlag == 'C'){
				// do inserts to the components not to the actual item
				$sql = "SELECT stockmaster.*, bom.rh_type, bom.quantity AS reqqty
						FROM stockmaster, bom WHERE stockmaster.stockid IN (SELECT component FROM bom WHERE parent = '".$OrderLine->StockID."')
							AND bom.component = stockmaster.stockid
							AND bom.parent  = '".$OrderLine->StockID."'
							AND bom.effectiveafter <= NOW()
							AND bom.effectiveto >= NOW()";
				$compres = DB_query($sql,$db);
				// BOWIKAXU INICIAN LOS INSERTS AL ARTICULO COMPONENTE
				while($Component = DB_fetch_array($compres)){

					if($Component['rh_type']==1){ // cantidad variable
						$CompQty = 	$Component['reqqty'] * $OrderLine->QtyDispatched;
					}else { // cantidad fija
						$CompQty = $Component['reqqty'];
					}
					$CompCost = $Component['materialcost']+$Component['labourcost']+$COmponent['overheadcost'];
					//$CompPrice = GetPrice($Component['stockid'],$_SESSION['Items']->DebtorNo,$_SESSION['Items']->Branch);
					/* If GLLink_Stock then insert GLTrans to credit stock and debit cost of sales at standard cost*/

					if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND $CompCost !=0){

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
						" . GetCOGSGLAccount($Area, $Component['stockid'], $_SESSION['Items']->DefaultSalesType, $db) . ",
						'" . $_SESSION['Items']->DebtorNo . " - " . $Component['stockid'] . " x " . $CompQty . " @ " . $CompCost . "',
						" . $CompCost * $CompQty . "
					)";

						$ErrMsg = _('CRITICAL ERROR') . '! 401 ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The cost of sales GL posting could not be inserted because');
						$DbgMsg = _('The following SQL to insert the GLTrans record was used');
						$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

						/*now the stock entry*/
						$StockGLCode = GetStockGLCode($Component['stockid'],$db);

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
						'" . $_SESSION['Items']->DebtorNo . " - " . $Component['stockid'] . " x " . $CompQty . " @ " . $CompCost . "',
						" . (-$CompCost * $CompQty) . "
					)";

						$ErrMsg = _('CRITICAL ERROR') . '! 402 ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock side of the cost of sales GL posting could not be inserted because');
						$DbgMsg = _('The following SQL to insert the GLTrans record was used');
						$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
					} /* end of if GL and stock integrated and standard cost !=0 */
				}

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
						" . $SalesGLAccounts['rh_invoiceshipmentcode'] . ",
						'" . $_SESSION['Items']->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->QtyDispatched . " @ " . $OrderLine->Price . "',
						" . (-$OrderLine->Price * $OrderLine->QtyDispatched/$_SESSION['CurrencyRate']) . "
					)";
					$ErrMsg = _('CRITICAL ERROR') . '! 403 ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales GL posting could not be inserted because');
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
						$ErrMsg = _('CRITICAL ERROR') . '! 404 ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales discount GL posting could not be inserted because');
						$DbgMsg = _('The following SQL to insert the GLTrans record was used');
						$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
					} /*end of if discount !=0 */
				} /*end of if sales integrated with debtors */
			}else {
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
					$ErrMsg = _('CRITICAL ERROR') . '! 405 ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The cost of sales GL posting could not be inserted because');
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
					$ErrMsg = _('CRITICAL ERROR') . '! 406 ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock side of the cost of sales GL posting could not be inserted because');
					$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
				} /* end of if GL and stock integrated and standard cost !=0 */

				if ($_SESSION['CompanyRecord']['gllink_debtors']==1 AND $OrderLine->Price !=0){

					//Post sales transaction to GL credit sales
					$SalesGLAccounts = GetSalesGLAccount($Area, $OrderLine->StockID, $_SESSION['Items']->DefaultSalesType, $db);
					//var_dump($SalesGLAccounts);
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
						" . $SalesGLAccounts['rh_invoiceshipmentcode'] . ",
						'" . $_SESSION['Items']->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->QtyDispatched . " @ " . $OrderLine->Price . "',
						" . (-$OrderLine->Price * $OrderLine->QtyDispatched/$_SESSION['CurrencyRate']) . "
					)";
					$ErrMsg = _('CRITICAL ERROR') . '! 407 ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales GL posting could not be inserted because');
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
						$ErrMsg = _('CRITICAL ERROR') . '! 408 ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales discount GL posting could not be inserted because');
						$DbgMsg = _('The following SQL to insert the GLTrans record was used');
						$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
					} /*end of if discount !=0 */

				}
			}
		} /*Quantity dispatched is more than 0 */
	} /*end of OrderLine loop */


	if ($_SESSION['CompanyRecord']['gllink_debtors']==1){

		/*Post debtors transaction to GL debit debtors, credit freight re-charged and credit sales */
        //var_dump($_SESSION['CompanyRecord']);
		if (($_SESSION['Items']->total + $_SESSION['Items']->FreightCost ) !=0) {
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
						" . $_SESSION['CompanyRecord']['rh_invoiceshipmentact'] . ",
						'" . $_SESSION['Items']->DebtorNo . "',
						" . (($_SESSION['Items']->total + $_SESSION['Items']->FreightCost )/$_SESSION['CurrencyRate']) . "
					)";
			$ErrMsg = _('CRITICAL ERROR') . '! 409 ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The total debtor GL posting could not be inserted because');
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
			$ErrMsg = _('CRITICAL ERROR') . '! 410 ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The freight GL posting could not be inserted because');
			$DbgMsg = _('The following SQL to insert the GLTrans record was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
		}
		/*
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

		$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The tax GL posting could not be inserted because');
		$DbgMsg = _('The following SQL to insert the GLTrans record was used');
		$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
		}
		}
		*/
	} /*end of if Sales and GL integrated */

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
	", '".$fecha."',0,".$_POST['RemType'].")";

	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('No se pudieron insertar los datos de la remision');
	$DbgMsg = _('Error al insertar valores en la tabla');
	$Result = DB_query($TBLRem,$db,$ErrMsg,$DbgMsg,true);

	// TERMINA INSERCION EN rh_invoiceshipment
	$SQL = "UPDATE systypes SET typeno = ".$InvoiceNo." WHERE typeid = 20000";
	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('No se pudo actualizar la cantidad de Remisiones en systypes');
	$DbgMsg = _('Error al actualizar systypes');
	$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

        DB_query("COMMIT",$db,'Fallo Insertar todos los cambios','Fallo el Query: COMMIT',true);
        /*termina los datos de la remision*/

        /*************************/
        /*         PAGOS         */
        /*************************/

        /*si todo esta bien y llega hasta aki se inserta el total pagado*/
        $padre = $_SESSION['pagos'];
        $elementos = count($padre);
        if($elementos != 0){
             for($x=0;$x<$elementos;$x++){
                 $individuales = $padre[$x];
                 $sql_insert = "INSERT INTO rh_pos_pagos(pedido, remision, usuario, fecha, tipo, monto, digitos, aprovacion, banco, nombre, numero) VALUES('".$OrderNo."', '".$InvoiceNo."', '".$_SESSION['UserID']."', '".date('Y-m-d')."', '".$individuales['tipo']."', '".$individuales['monto']."', '".$individuales['digitos']."', '".$individuales['aprovacion']."', '".$individuales['banco']."', '".$individuales['nombre']."', '".$individuales['numero']."')";
                 DB_query($sql_insert,$db,'Fallo Insertar todos el total de pago','Fallo el Query: '.$sql_insert,true);
             }
        }

        /*************************/
        /*         VENTA         */
        /*************************/

        $sq_items = "Select rm.stockid, rm.transno, rm.price, rm.reference, (rm.qty*-1) as qty, rm.discountpercent,  st.description, st.taxcatid FROM stockmoves rm, stockmaster st WHERE rm.transno = '".$InvoiceNo."' AND rm.type = 20000 AND rm.show_on_inv_crds=1 AND rm.stockid = st.stockid";
                       //$sq_items="SELECT stockmoves.stockid, stockmaster.description, -stockmoves.qty as quantity, stockmoves.discountpercent, ((1 - stockmoves.discountpercent) * stockmoves.price * 1* -stockmoves.qty) AS fxnet, (stockmoves.price * 1) AS fxprice, stockmoves.narrative, stockmaster.units FROM stockmoves, stockmaster WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type=20000 AND stockmoves.transno='".$InvoiceNo."' AND stockmoves.show_on_inv_crds=1 ORDER BY stockmoves.rh_orderline";
                       //echo $sq_items;

                        $itemsResult = DB_query($sq_items,$db);

                        $i=true;
                        $iva0 = 0;
                        $ivaX = 0;
                        $iva =0;
                        while ($myrow_i_ = DB_fetch_array($itemsResult)){
                            $iva_elemento = get_ivas($myrow_i_['taxcatid'],$db);
                            $totalpre = (($myrow_i_['qty']*$myrow_i_['price']));
                            $total = $totalpre-($totalpre*$myrow_i_['discountpercent']);
                            if(($myrow_i_['taxcatid']==8)||($myrow_i_['taxcatid']==11)){
                                $iva0 += $total;
                            }else if($myrow_i_['taxcatid']==6) {
                                $ivaX+=$total;
                                $iva+= $total*$iva_elemento[0];
                            }
                        }

        $SQL=" update debtortrans set ovgst=".$iva." where transno=".$InvoiceNo." and type=20000 ";
        DB_query($SQL,$db);


        //$_SESSION['rh_pos_principal'] = array('terminal' => $terminal, 'usuario' => $usuario, 'cliente' => $clientee, 'sucursalC' => $sucursal, 'IP' => $ip);
        $venta_general = $_SESSION['rh_pos_principal'];
        $sql_venta = "INSERT INTO rh_pos_guardar_venta(pedido, remision, terminal, usuario, cliente, sucursalC, IP, Fecha, vendedor) values('".$OrderNo."', '".$InvoiceNo."', '".$venta_general['terminal']."', '".$venta_general['usuario']."', '".$venta_general['cliente']."', '".$venta_general['sucursalC']."', '".$venta_general['IP']."', '".date('Y-m-d')."', '".$venta_general['ved_de_dor']."')";
        DB_query($sql_venta,$db,'Fallo Insertar todos el total de pago','Fallo el Query: '.$sql_venta,true);
        /*si todo correcto devuele los siguientes datos con los cuales se van a trabajar*/
        $normal_regresa = $OrderNo."***".$InvoiceNo;
        echo $normal_regresa;
        die();
    }else{
        /*cuando no tiene articulos*/
        return "no";
        die();
    }
}

/********************************************************************/
/*                                                                  */
/*             funciones especiales para el post                    */
/*                                                                  */
/********************************************************************/

/*calse para crear la clase del carrito con la cual se va a trabajar*/
Class Cart {
	var $LineItems;
	var $total;
	var $totalVolume;
	var $totalWeight;
	var $LineCounter;
	var $ItemsOrdered;
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
	Var $CreditAvailable;
	Var $TaxGroup;
	Var $DispatchTaxProvince;
	VAR $vtigerProductID;
	Var $DefaultPOLine;
	Var $DeliveryDays;

	function Cart(){
	/*Constructor function initialises a new shopping cart */
		$this->LineItems = array();
		$this->total=0;
		$this->ItemsOrdered=0;
		$this->LineCounter=0;
		$this->DefaltSalesType="";
		$this->FreightCost =0;
		$this->FreightTaxes = array();
	}
}

/*clase para trabajar con el detalle de los hijos con los cuales se esta trabajando actualmente*/
Class LineDetails {
	Var $LineNumber;
	Var $StockID;
	Var $ItemDescription;
	Var $Quantity;
	Var $Price;
	Var $DiscountPercent;
	Var $Units;
	Var $Volume;
	Var $Weight;
	Var $ActDispDate;
	Var $QtyInv;
	Var $QtyDispatched;
	Var $StandardCost;
	Var $QOHatLoc;
	Var $MBflag;
	Var $DiscCat;
	Var $Controlled;
	Var $Serialised;
	Var $DecimalPlaces;
	Var $SerialItems;
	Var $Narrative;
	Var $TaxCategory;
	Var $Taxes;
	Var $WorkOrderNo;
	Var $ItemDue;
	Var $POLine;
	Var $rh_Sample;
	var $Disc;
        /*construccion para el detalle de los articulos*/
        function LineDetails(){
            $this->Narrative = "";
        }
}

?>