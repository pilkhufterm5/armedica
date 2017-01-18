<?php
//commit
/* $Revision: 362 $ */

/*
 * Esta pantalla es mandada a llamar desde otras partes del erp para poder crear facturas masivas, tener presente la variable
 * $MASIVA
 *
 *
 * */

//var_dump($_POST); exit;
if(!isset($MASIVA))$MASIVA=false;
/* Session started in session.inc for password checking and authorisation level check */

include_once("includes/DefineCartClass.php");
include_once('includes/DefineSerialItems.php');
include_once('includes/DefinePedimentoItems.php');
$PageSecurity = 2;
include_once("includes/session.inc");
$title = _('Confirm Dipatches and Invoice An Order');
if(!$MASIVA)
include_once('includes/header.inc');
include_once('includes/SQL_CommonFunctions.inc');
include_once('includes/FreightCalculation.inc');
include_once('includes/GetSalesTransGLCodes.inc');

//Jaime includes
include_once('XMLFacturacionElectronica/utils/File.php');
{
    require_once('Numbers/Words.php');
    include_once('CFDI32.php');
    include_once('rh_cfdiFunctions32.php');
 }

if(isset($_SESSION['metodoPago'])){
    $_SESSION['metodoPago']=(isset($_POST['metodoPago'])?$_POST['metodoPago']:$_SESSION['metodoPago']);
}else{
    $_SESSION['metodoPago']=(isset($_POST['metodoPago'])?$_POST['metodoPago']:'No Identificado');
}

if(isset($_SESSION['cuentaPago'])){
    $_SESSION['cuentaPago']=(isset($_POST['cuentaPago'])?$_POST['cuentaPago']:$_SESSION['cuentaPago']);
}else{
    $_SESSION['cuentaPago']=(isset($_POST['cuentaPago'])?$_POST['cuentaPago']:'No Identificado');
}


//Termina Jaime includes

echo '<A HREF="'. $rootpath . '/SelectSalesOrder.php?' . SID . '">'. _('Back to Sales Orders'). '</A><BR>';

if (!isset($_GET['OrderNumber']) && !isset($_SESSION['ProcessingOrder'])) {
	/* This page can only be called with an order number for invoicing*/
	echo '<CENTER><A HREF="' . $rootpath . '/SelectSalesOrder.php?' . SID . '">' . _('Select a sales order to invoice'). '</A></CENTER>';
	echo '<BR><BR>';
	prnMsg( _('This page can only be opened if an order has been selected Please select an order first from the delivery details screen click on Confirm for invoicing'), 'error' );
	if(!$MASIVA){
	include ('includes/footer.inc');
	exit;

	}else return false;
} elseif ($_GET['OrderNumber']>0) {


	unset($_SESSION['Items']->LineItems);
	unset ($_SESSION['Items']);

	$_SESSION['ProcessingOrder']=$_GET['OrderNumber'];
	$_SESSION['Items'] = new cart;

	/*read in all the guff from the selected order into the Items cart  */

	// bowikaxu realhost -  may2007 - rh_status
    //rleal Mar 4, 2010 Se agregaron las address7-10 para FE
	$OrderHeaderSQL = 'SELECT salesorders.orderno,
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
					salesorders.deladd7,
					salesorders.deladd8,
					salesorders.deladd9,
					salesorders.deladd10,
					salesorders.contactphone,
					salesorders.contactemail,
					salesorders.freightcost,
					salesorders.deliverydate,
					debtorsmaster.currcode,
                                       debtorsmaster.holdreason,
					salesorders.fromstkloc,
                                       salesorders.fromstkloc_virtual,
					locations.taxprovinceid,
					custbranch.taxgroupid,
					currencies.rate as currency_rate,
					custbranch.defaultshipvia,
					custbranch.specialinstructions,
                    custbranch.satid as metodopago,-- Se cambio custbranch.metodopago por custbranch.satid Angeles Perez 12-07-2016
                    custbranch.cuentapago
			FROM salesorders,
				debtorsmaster,
				custbranch,
				currencies,
				locations
			WHERE salesorders.debtorno = debtorsmaster.debtorno
			AND salesorders.branchcode = custbranch.branchcode
			AND salesorders.debtorno = custbranch.debtorno
			AND salesorders.rh_status = 0
			AND locations.loccode=salesorders.fromstkloc
			AND debtorsmaster.currcode = currencies.currabrev
			AND salesorders.orderno = ' . $_GET['OrderNumber'];

	$ErrMsg = _('The order cannot be retrieved because');
	$DbgMsg = _('The SQL to get the order header was');
	$GetOrdHdrResult = DB_query($OrderHeaderSQL,$db,$ErrMsg,$DbgMsg);

	if (DB_num_rows($GetOrdHdrResult)==1) {

		$myrow = DB_fetch_array($GetOrdHdrResult);
//print_r($myrow);
		$_SESSION['Items']->DebtorNo = $myrow['debtorno'];
		$_SESSION['Items']->OrderNo = $myrow['orderno'];
		$_SESSION['Items']->Branch = $myrow['branchcode'];
		$_SESSION['Items']->CustomerName = $myrow['name'];
		$_SESSION['Items']->CustRef = $myrow['customerref'];
		$_SESSION['Items']->Comments = $myrow['comments'];
		$_SESSION['Items']->DefaultSalesType =$myrow['ordertype'];
		$_SESSION['Items']->DefaultCurrency = $myrow['currcode'];
		$BestShipper = $myrow['shipvia'];
		$_SESSION['Items']->ShipVia = $myrow['shipvia'];

		if (is_null($BestShipper)){
			$BestShipper=0;
		}
		$_SESSION['Items']->DeliverTo = $myrow['deliverto'];
		$_SESSION['Items']->DeliveryDate = ConvertSQLDate($myrow['deliverydate']);
        //rleal Mar 4, 2010 Se agregaron las address7-10 para FE
		$_SESSION['Items']->BrAdd1 = $myrow['deladd1'];
		$_SESSION['Items']->BrAdd2 = $myrow['deladd2'];
		$_SESSION['Items']->BrAdd3 = $myrow['deladd3'];
		$_SESSION['Items']->BrAdd4 = $myrow['deladd4'];
		$_SESSION['Items']->BrAdd5 = $myrow['deladd5'];
		$_SESSION['Items']->BrAdd6 = $myrow['deladd6'];
        $_SESSION['Items']->BrAdd7 = $myrow['deladd7'];
        $_SESSION['Items']->BrAdd8 = $myrow['deladd8'];
        $_SESSION['Items']->BrAdd9 = $myrow['deladd9'];
        $_SESSION['Items']->BrAdd10 = $myrow['deladd10'];
		$_SESSION['Items']->PhoneNo = $myrow['contactphone'];
		$_SESSION['Items']->Email = $myrow['contactemail'];

                //iJPe
                //Sucursal a la cual se haran los movimientos, siempre sera una sucursal maestra
		$_SESSION['Items']->LocationMoves = $myrow['fromstkloc'];
                //Sucursal a la cual se haran las transacciones
                $_SESSION['Items']->Location = $myrow['fromstkloc_virtual'];

		$_SESSION['Items']->FreightCost = $myrow['freightcost'];
		$_SESSION['Old_FreightCost'] = $myrow['freightcost'];
		$_POST['ChargeFreightCost'] = $_SESSION['Old_FreightCost'];
		$_SESSION['Items']->Orig_OrderDate = $myrow['orddate'];
		$_SESSION['CurrencyRate'] = $myrow['currency_rate'];
        $_POST['rate']= 1/$_SESSION['CurrencyRate'];
		$_SESSION['Items']->TaxGroup = $myrow['taxgroupid'];
		$_SESSION['Items']->DispatchTaxProvince = $myrow['taxprovinceid'];
		$_SESSION['Items']->GetFreightTaxes();
		$_SESSION['Items']->SpecialInstructions = $myrow['specialinstructions'];
        $_SESSION['metodoPago']=(strlen($myrow['metodopago'])>0?$myrow['metodopago']:'No Identificado');
        $_SESSION['cuentaPago']=(strlen($myrow['cuentapago'])>0?$myrow['cuentapago']:'No Identificado');

                //iJPe 2010-04-14 holdreason
                $_SESSION['Items']->Holdreason = $myrow['holdreason'];

		DB_free_result($GetOrdHdrResult);

		/*now populate the line items array with the sales order details records */
		// bowikaxu realhost 9 july 2008 - get saved item description not actual
		$LineItemsSQL = 'SELECT stkcode,
					stockmaster.description AS description2,
					stockmaster.controlled,
					stockmaster.serialised,
					stockmaster.volume,
					stockmaster.kgs,
					stockmaster.units,
					stockmaster.decimalplaces,
					stockmaster.mbflag,
					stockmaster.taxcatid,
					stockmaster.discountcategory,
					salesorderdetails.unitprice,
					salesorderdetails.quantity,
					salesorderdetails.discountpercent,
					salesorderdetails.actualdispatchdate,
					salesorderdetails.qtyinvoiced,
					salesorderdetails.narrative,
					salesorderdetails.description,
					salesorderdetails.orderlineno,
					salesorderdetails.poline,
					salesorderdetails.itemdue,
					stockmaster.materialcost +
						stockmaster.labourcost +
						stockmaster.overheadcost AS standardcost,
                if(isnull(D1.descuento),0.00,D1.descuento*100) as discount1,
                if(isnull(D2.descuento),0.00,D2.descuento*100) as discount2
				FROM salesorderdetails INNER JOIN stockmaster ON salesorderdetails.stkcode = stockmaster.stockid
                    left join rh_descuentos D1 on salesorderdetails.orderno=D1.transno and salesorderdetails.orderlineno = D1.orderlineno and D1.type=30 and D1.tipo_descuento=1
                    left join rh_descuentos D2 on salesorderdetails.orderno=D2.transno and salesorderdetails.orderlineno = D2.orderlineno and D1.type=30 and D2.tipo_descuento=2
				WHERE salesorderdetails.orderno =' . $_GET['OrderNumber'] . '
				AND salesorderdetails.quantity - salesorderdetails.qtyinvoiced >0
				ORDER BY salesorderdetails.orderlineno';
        // echo  $LineItemsSQL;
		$ErrMsg = _('The line items of the order cannot be retrieved because');
		$DbgMsg = _('The SQL that failed was');
		$LineItemsResult = DB_query($LineItemsSQL,$db,$ErrMsg,$DbgMsg);

		$sql = "SELECT rh_maxitminv FROM rh_locations WHERE loccode ='".$_SESSION['Items']->Location."'";
		$itmmaxres = DB_query($sql,$db);
		$ItmMax = DB_fetch_array($itmmaxres);

		if (db_num_rows($LineItemsResult)>0) {
//print_r($myrow);
			while ($myrow=db_fetch_array($LineItemsResult)) {
				// bowikaxu realhost - verificar numero total de articulos

				if(count($_SESSION['Items']->LineItems) >= $ItmMax['rh_maxitminv']){

					echo "<H2>Imposible agregar el articulo ".$myrow['stkcode']." se llego al limite de </H2>";

				}else {

					if($myrow['unitprice']<0&&$myrow['mbflag']!='D'){
						echo "<CENTER><H2><FONT COLOR=RED>ERROR: EL ARTICULO ".$myrow['stkcode']." TIENE PRECIO IGUAL O MENOR A CERO,<BR>
						VERIFIQUE QUE EL ARTICULO SEA MUESTRA, DE LO CONTRARIO<BR>
						MODIFIQUE EL PEDIDO Y VUELVA A INTENTAR FACTURAR</FONT></H2></CENTER>";
						//include_once('includes/footer.inc');
						//exit;
						// bowikaxu realhost - quitar verificacion
					}//else {
						// si agregar el articulo

						$_SESSION['Items']->add_to_cart($myrow['stkcode'],
						$myrow['quantity'],
						$myrow['description'],
						$myrow['unitprice'],
						$myrow['discountpercent'],
						$myrow['units'],
						$myrow['volume'],
						$myrow['kgs'],
						0,
						$myrow['mbflag'],
						$myrow['actualdispatchdate'],
						$myrow['qtyinvoiced'],
						$myrow['discountcategory'],
						$myrow['controlled'],
						$myrow['serialised'],
						$myrow['decimalplaces'],
						$myrow['narrative'],
						'No',
						$myrow['orderlineno'],
						$myrow['taxcatid'],
						'',
						$myrow['itemdue'],
						$myrow['poline'],
                        1,
                        $myrow['discount1'],
                        $myrow['discount2']);	/*NB NO Updates to DB */

						$_SESSION['Items']->LineItems[$myrow['orderlineno']]->StandardCost = $myrow['standardcost'];

						/*Calculate the taxes applicable to this line item from the customer branch Tax Group and Item Tax Category */

						$_SESSION['Items']->GetTaxes($myrow['orderlineno']);
					//}// bowikaxu - fin comprobar cantidad de articulos
				}

			} /* line items from sales order details */
		} else { /* there are no line items that have a quantity to deliver */
			echo '<BR>';
			prnMsg( _('There are no ordered items with a quantity left to deliver. There is nothing left to invoice'));
			if(!$MASIVA){
			include_once('includes/footer.inc');
			exit;
			}else return false;

		} //end of checks on returned data set
		DB_free_result($LineItemsResult);

	} else { /*end if the order was returned sucessfully */

		echo '<BR>'.
		prnMsg( _('This order item could not be retrieved. Please select another order'), 'warn');
		if(!$MASIVA){
		include ('includes/footer.inc');
		exit;
		}else return false;
	} //valid order returned from the entered order number
} else {

	/* if processing, a dispatch page has been called and ${$StkItm->LineNumber} would have been set from the post
	set all the necessary session variables changed by the POST  */
	if (isset($_POST['ShipVia'])){
		$_SESSION['Items']->ShipVia = $_POST['ShipVia'];
	}
	if (isset($_POST['ChargeFreightCost'])){
		$_SESSION['Items']->FreightCost = $_POST['ChargeFreightCost'];
	}
	foreach ($_SESSION['Items']->FreightTaxes as $FreightTaxLine) {
		if (isset($_POST['FreightTaxRate'  . $FreightTaxLine->TaxCalculationOrder])){
			$_SESSION['Items']->FreightTaxes[$FreightTaxLine->TaxCalculationOrder]->TaxRate = $_POST['FreightTaxRate'  . $FreightTaxLine->TaxCalculationOrder]/100;
		}
	}

	foreach ($_SESSION['Items']->LineItems as $Itm) {
		if (is_numeric($_POST[$Itm->LineNumber .  '_QtyDispatched' ])AND $_POST[$Itm->LineNumber .  '_QtyDispatched'] <= ($_SESSION['Items']->LineItems[$Itm->LineNumber]->Quantity - $_SESSION['Items']->LineItems[$Itm->LineNumber]->QtyInv)){
			$_SESSION['Items']->LineItems[$Itm->LineNumber]->QtyDispatched = $_POST[$Itm->LineNumber  . '_QtyDispatched'];
		}

		foreach ($Itm->Taxes as $TaxLine) {
			if (isset($_POST[$Itm->LineNumber  . $TaxLine->TaxCalculationOrder . '_TaxRate'])){
				$_SESSION['Items']->LineItems[$Itm->LineNumber]->Taxes[$TaxLine->TaxCalculationOrder]->TaxRate = $_POST[$Itm->LineNumber  . $TaxLine->TaxCalculationOrder . '_TaxRate']/100;
			}
		}

	}

}

//iJPe 2010-04-15 Modificacion pora mostrar leyenda acerca de la calificacion del cliente

switch ($_SESSION['Items']->Holdreason)
{
     case 0:
        $Msg='Excelente Historial';
        $Class = 'excelente';
        $colorHR = '#088A08';
        break;

     case 1:
        $Msg='Buen Historial';
        $Class = 'buen';
        $colorHR = '#088A08';
        break;

     case 20:
        $Msg='Precauci&oacute;n';
        $Class = 'precaucion';
        $colorHR = '#F4FA58';
        break;

     case 51:
        $Msg='No Vender M&aacute;s';
        $Class = 'noVender';
        $colorHR = '#FA5858';
        break;
}

echo '<DIV class="'.$Class.'"><B>' .$Msg . '</B></DIV>';


/* Always display dispatch quantities and recalc freight for items being dispatched */
$sql = "SELECT extinvoices FROM rh_locations WHERE loccode = '".$_SESSION['Items']->Location."'";
$res = mysql_query($sql);
$info = mysql_fetch_array($res);
//Jaime, se muestra el siguiente folio de la serie (cfd)
$idLocation = $_SESSION['Items']->Location;
$j_idSystypes = 10;
$sql = "select serie from rh_cfd__locations__systypes__ws_csd where id_locations = '$idLocation' and !isnull(id_ws_csd) and id_systypes = $j_idSystypes";
$result = DB_query($sql,$db,'','',false,false);
$row = DB_fetch_array($result);
$serie = $row[0];
$serieYFolio;
try{
/*    if($serie == null)
        throw new Exception('No ha seleccionado una serie para este tipo de comprobante');
    $serieYFolio = $serie.getWs()->getSiguienteFolio(array('serie'=>$serie))->return;
*/
}
catch(Exception $e){
    prnMsg($e->getMessage(), 'error');
    if(!$MASIVA){
    include_once('includes/footer.inc');
    exit;
    }else return false;
}
echo "<BR><CENTER><FONT SIZE=4 COLOR=red>"._('Location').': <STRONG>'.$_SESSION['Items']->Location.' '.$serieYFolio."</STRONG></FONT><CENTER>";
//\Jaime, se muestra el siguiente folio de la serie (cfd)
//print_r($_SESSION);

if ($_SESSION['Items']->SpecialInstructions) {
	prnMsg($_SESSION['Items']->SpecialInstructions,'warn');
}
echo '<BR><BR><CENTER><FONT SIZE=4><B>' . _('Customer No.') . ': ' . $_SESSION['Items']->DebtorNo;
echo '&nbsp;&nbsp;' . _('Customer Name') . ' : ' . $_SESSION['Items']->CustomerName. '</B></FONT><FONT SIZE=3>';
//echo "<BR><FONT SIZE=4 COLOR=red>"._('Location').': <STRONG>'.$_SESSION['Items']->Location."</STRONG></FONT><BR>";
//echo '<CENTER><FONT SIZE=4><B><U>' . $_SESSION['Items']->CustomerName . '</U></B></FONT><FONT SIZE=3> - ' .
//rleal Mar 4, 2010 Se agregaron las address7-10 para FE
$sql = "SELECT name,
			name2,
			currcode,
			address1,
			address2,
			address3,
			address4,
			address5,
			address6,
			address7,
			address8,
			address9,
			address10,
			taxref,
			rh_tel
			FROM debtorsmaster
			WHERE debtorno = '".$_SESSION['Items']->DebtorNo."'";
	$res_address = DB_query($sql,$db);

	if(DB_num_rows($res_address)<1){
		prnMsg('Imposible obtener la direccion fiscal del cliente','warn');
		DB_query('ROLLBACK',$db); // do a rollback
		if(!$MASIVA){
		include_once('includes/footer.inc');
		exit;
		}else return false;
	}
	$address = DB_fetch_array($res_address);

	//SAINTS formatear RFC quitar guiones y espacios 03/02/2011
	$errefece=$address['taxref'];
	$rfcFormateado=str_replace("-","",$errefece);
	$rfcFormateado=str_replace(" ","",$rfcFormateado);

echo"<table><tr><td class='tableheader'>"._('Invoice Addressing').":<br>
			<a href='Customers.php?".SID."DebtorNo=".$_SESSION['Items']->DebtorNo."'>"._('Modify')."</a>
			</td>
			<td>
			".$address['name'].' '.$address['name2']."<br>
			".$rfcFormateado."<br>
			".$address['rh_tel']."<br>
			".$address['address1']."<br>
			".$address['address2']."<br>
			".$address['address3']."<br>
			".$address['address4']."<br>
			".$address['address5']."<br>
			".$address['address6']."<br>
			".$address['address7']."<br>
			".$address['address8']."<br>
			".$address['address9']."<br>
			".$address['address10']."<br>
			</td></tr></table></center>";

	//SAINTS fin
echo '<BR>' . _('Invoice amounts stated in') . ' ' . $_SESSION['Items']->DefaultCurrency . '</CENTER>';
unset($address);
//Realhost (modificado) Jaime Lun 22 Mar 2010 18:28, se le agrego el evento onSubmit a la forma para validar con Js que se haya escogido una Serie de CFD
echo '<FORM onSubmit="return validateForm()" ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';
//Termina Realhost (modificado) Jaime Lun 22 Mar 2010 18:28, se le agrego el evento onSubmit a la forma para validar con Js que se haya escogido una Serie de CFD

echo '<FORM ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';

/***************************************************************
Line Item Display
***************************************************************/
echo '<CENTER><TABLE CELLPADDING=2 id="tableConceptos" COLSPAN=7 BORDER=0>
	<TR>
		<TD class="tableheader">' . _('Item Code') . '</TD>
		<TD class="tableheader">' . _('Item Description' ) . '</TD>
		<TD class="tableheader">' . _('Ordered') . '</TD>
		<TD class="tableheader">' . _('Units') . '</TD>
		<TD class="tableheader">' . _('Already') . '<BR>' . _('Sent') . '</TD>
		<TD class="tableheader">' . _('This Dispatch') . '</TD>
		<TD class="tableheader">' . _('Price') . '</TD>
		<TD class="tableheader">' . _('Discount') . '</TD>
        <TD class="tableheader">' . _('Discount') . '</TD>
		<TD class="tableheader">' . _('Total') . '<BR>' . _('Excl Tax') . '</TD>
		<TD class="tableheader">' . _('Tax Authority') . '</TD>
		<TD class="tableheader">' . _('Tax %') . '</TD>
		<TD class="tableheader">' . _('Tax') . '<BR>' . _('Amount') . '</TD>
		<TD class="tableheader">' . _('Total') . '<BR>' . _('Incl Tax') . '</TD>
	</TR>';

$_SESSION['Items']->total = 0;
$_SESSION['Items']->totalVolume = 0;
$_SESSION['Items']->totalWeight = 0;
$TaxTotals = array();
$TaxGLCodes = array();
$TaxTotal =0;

/*show the line items on the order with the quantity being dispatched available for modification */

$k=0; //row colour counter
foreach ($_SESSION['Items']->LineItems as $LnItm) {

/**** USAR pedimentos automaticos *********************************/
    $sql='select * from rh_pedimento join stockpedimentoitems on rh_pedimento.pedimentoid=stockpedimentoitems.pedimentoid and stockpedimentoitems.quantity>0 and stockpedimentoitems.stockid="'.$LnItm->StockID.'" order by fecha';
    //echo $sql;
    $rs = DB_query($sql,$db);
    $countX=0;
    $flagPedimento=false;
    $LnItm->PedimentoItems=array();
    $auxQty= $LnItm->Quantity;
    while($array=DB_fetch_array($rs)){
        $ExistingBundleQty = ValidBundleRefPedimento ($LnItm->StockID, $_SESSION['Items']->Location, $array['pedimentoid']);
        //echo  $ExistingBundleQty;
        if($ExistingBundleQty>0){
          if($auxQty<=$ExistingBundleQty && $auxQty>0){
            $LnItm->PedimentoItems[$countX] = new PedimentoItem($array['pedimentoid'],null,null,1*$auxQty,$LnItm->LineNumber);
            $countX++;
            $auxQty-=$ExistingBundleQty;
          }else if($auxQty>0){
                $LnItm->PedimentoItems[$countX] = new PedimentoItem($array['pedimentoid'],null,null, 1 * $ExistingBundleQty,$LnItm->LineNumber);
                $countX++;
                $auxQty-=$ExistingBundleQty;
                $flagPedimento=true;
          }
        }
    }
    /*var_dump($LnItm->PedimentoItems);    */
//**********************************************************************************
	if ($k==1){
		$RowStarter = '<tr bgcolor="#CCCCCC">';
		$k=0;
	} else {
		$RowStarter = '<tr bgcolor="#EEEEEE">';
		$k=1;
	}

	echo $RowStarter;

	$LineTotal = $LnItm->QtyDispatched * $LnItm->Price * (1 - $LnItm->DiscountPercent);

	$_SESSION['Items']->total += $LineTotal;
	$_SESSION['Items']->totalVolume += ($LnItm->QtyDispatched * $LnItm->Volume);
	$_SESSION['Items']->totalWeight += ($LnItm->QtyDispatched * $LnItm->Weight);

	echo '<TD>'.$LnItm->StockID.'</TD>
		<TD>'.$LnItm->ItemDescription.'</TD>
		<TD ALIGN=RIGHT>' . number_format($LnItm->Quantity,4) . '</TD>
		<TD>'.$LnItm->Units.'</TD>
		<TD ALIGN=RIGHT>' . number_format($LnItm->QtyInv,4) . '</TD>';

	if ($LnItm->Controlled==1){

		echo '<TD ALIGN=RIGHT><input type=hidden name="' . $LnItm->LineNumber . '_QtyDispatched"  value="' . $LnItm->QtyDispatched . '"><a href="' . $rootpath .'/ConfirmDispatchControlled_Invoice.php?' . SID . '&LineNo='. $LnItm->LineNumber.'">' .$LnItm->QtyDispatched . '</a></TD>';

	} else {

		echo '<TD ALIGN=RIGHT><input type=text name="' . $LnItm->LineNumber .'_QtyDispatched" maxlength=12 SIZE=12 value="' . $LnItm->QtyDispatched . '"></TD>';

	}

	$DisplayDiscountPercent = number_format($LnItm->DiscountPercent*100,2) . '%';
    $DisplayDiscount1 = number_format($LnItm->Discount1,2) . '%';
    $DisplayDiscount2 = number_format($LnItm->Discount2,2) . '%';
	$DisplayLineNetTotal = floor($LineTotal*100)/100;
	// bowikaxu realhost - april 2008 - view price with decimal positions configured in item properties
	$DisplayPrice = number_format($LnItm->Price,$LnItm->DecimalPlaces);
	echo '<TD ALIGN=RIGHT>'.$DisplayPrice.'</TD>
		<TD ALIGN=RIGHT>'.$DisplayDiscount1.'</TD>
        <TD ALIGN=RIGHT>'.$DisplayDiscount2.'</TD>
		<TD ALIGN=RIGHT>'.$DisplayLineNetTotal.'</TD>';

	/*Need to list the taxes applicable to this line */
	echo '<TD>';
	$i=0;
	foreach ($_SESSION['Items']->LineItems[$LnItm->LineNumber]->Taxes AS $Tax) {
		if ($i>0){
			echo '<BR>';
		}
		echo $Tax->TaxAuthDescription;
		$i++;
	}
	echo '</TD>';
	echo '<TD ALIGN=RIGHT>';

	$i=0; // initialise the number of taxes iterated through
	$TaxLineTotal =0; //initialise tax total for the line
//Eleazar Lara
//RealHost
//Comente las siguientes lineas para que no se imprimieran en pantalla.
//echo "<hr>";
//print_r($LnItm);
//echo "<hr>";
	foreach ($LnItm->Taxes AS $Tax) {
		if ($i>0){
			echo '<BR>';
		}
		//Restriccion de impuesto a 0 en caso de que sea negativo
// 		if($LnItm->Price<0)
// 			$Tax->TaxRate=0;
		echo '<input type=text name="' . $LnItm->LineNumber . $Tax->TaxCalculationOrder . '_TaxRate" maxlength=8 SIZE=4 value="' . $Tax->TaxRate*100 . '">';
		$i++;
		if ($Tax->TaxOnTax ==1){
			$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * ($LineTotal + $TaxLineTotal));
			$TaxLineTotal += ($Tax->TaxRate * ($LineTotal + $TaxLineTotal));
		} else {
			$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * $LineTotal);
			$TaxLineTotal += ($Tax->TaxRate * $LineTotal);
		}
		$TaxGLCodes[$Tax->TaxAuthID] = $Tax->TaxGLCode;
	}
	echo '</TD>';

	$TaxTotal += $TaxLineTotal;

	$DisplayTaxAmount = number_format($TaxLineTotal ,2);

	$DisplayGrossLineTotal = number_format($LineTotal+ $TaxLineTotal,2);
    echo '<TD ALIGN=RIGHT>'.$DisplayTaxAmount.'</TD><TD ALIGN=RIGHT>'.$DisplayGrossLineTotal.'</TD><!---<td><input type="button" value="Agregar" '. (isSet($_POST['ProcessInvoice'])?'disabled="true"':'') . ' onclick="agregarPedimento(this, ' . $LnItm->LineNumber . ')" /><input type="button" value="Quitar" ' . (isSet($_POST['ProcessInvoice'])?'disabled="true"':'') . ' onclick="quitarPedimento(this, ' . $LnItm->LineNumber . ')" /></td>-->';
  	//echo '<TD><a href="' . $rootpath . '/ConfirmDispatchPedimento_Invoice.php?' . SID . '&LineNo='. $LnItm->LineNumber.'">';
	//echo _("Pedimento");
	//echo '</A></TD>';
	if ($LnItm->Controlled==1){

		echo '<TD><a href="' . $rootpath . '/ConfirmDispatchControlled_Invoice.php?' . SID . '&LineNo='. $LnItm->LineNumber.'">';

		if ($LnItm->Serialised==1){
			echo _("Enter Serial Numbers");
		} else { /*Just batch/roll/lot control */
			echo _('Enter Batch/Roll/Lot #');
		}
		echo '</A></TD>';
	}
	echo '</TR>';
	if (strlen($LnItm->Narrative)>1){
		echo $RowStarter . '<TD COLSPAN=12>' . $LnItm->Narrative . '</TD></TR>';
	}
}//end foreach ($line)

/*Don't re-calculate freight if some of the order has already been delivered -
depending on the business logic required this condition may not be required.
It seems unfair to charge the customer twice for freight if the order
was not fully delivered the first time ?? */

if ($_SESSION['Items']->AnyAlreadyDelivered==1) {
	$_POST['ChargeFreightCost'] = 0;
} elseif(!isset($_SESSION['Items']->FreightCost)) {
	if ($_SESSION['DoFreightCalc']==True){
		list ($FreightCost, $BestShipper) = CalcFreightCost($_SESSION['Items']->total,
		$_SESSION['Items']->BrAdd2,
		$_SESSION['Items']->BrAdd3,
		$_SESSION['Items']->totalVolume,
		$_SESSION['Items']->totalWeight,
		$_SESSION['Items']->Location,
		$db);
		$_SESSION['Items']->ShipVia = $BestShipper;
	}
	if (is_numeric($FreightCost)){
		$FreightCost = $FreightCost / $_SESSION['CurrencyRate'];
	} else {
		$FreightCost =0;
	}
	if (!is_numeric($BestShipper)){
		$SQL =  'SELECT shipper_id FROM shippers WHERE shipper_id=' . $_SESSION['Default_Shipper'];
		$ErrMsg = _('There was a problem testing for a default shipper because');
		$TestShipperExists = DB_query($SQL,$db, $ErrMsg);
		if (DB_num_rows($TestShipperExists)==1){
			$BestShipper = $_SESSION['Default_Shipper'];
		} else {
			$SQL =  'SELECT shipper_id FROM shippers';
			$ErrMsg = _('There was a problem testing for a default shipper');
			$TestShipperExists = DB_query($SQL,$db, $ErrMsg);
			if (DB_num_rows($TestShipperExists)>=1){
				$ShipperReturned = DB_fetch_row($TestShipperExists);
				$BestShipper = $ShipperReturned[0];
			} else {
				prnMsg( _('There are no shippers defined') . '. ' . _('Please use the link below to set up shipping freight companies, the system expects the shipping company to be selected or a default freight company to be used'),'error');
				echo '<A HREF="' . $rootpath . 'Shippers.php">'. _('Enter') . '/' . _('Amend Freight Companies'). '</A>';
			}
		}
	}
}

if (!is_numeric($_POST['ChargeFreightCost'])){
	$_POST['ChargeFreightCost'] =0;
}

echo '<TR style="display:none">
	<TD COLSPAN=2 ALIGN=RIGHT>' . _('Order Freight Cost'). '</TD>
	<TD ALIGN=RIGHT>' . $_SESSION['Old_FreightCost'] . '</TD>';

if ($_SESSION['DoFreightCalc']==True){
	echo '<TD COLSPAN=2 ALIGN=RIGHT>' ._('Recalculated Freight Cost'). '</TD>
		<TD ALIGN=RIGHT>' . $FreightCost . '</TD>';
} else {
	echo '<TD COLSPAN=3></TD>';
}

echo '<TD COLSPAN=2 ALIGN=RIGHT>'. _('Charge Freight Cost').'</TD>
	<TD><INPUT TYPE=TEXT SIZE=10 MAXLENGTH=12 NAME=ChargeFreightCost VALUE=' . $_SESSION['Items']->FreightCost . '></TD>';


$FreightTaxTotal =0; //initialise tax total

echo '<TD>';

$i=0; // initialise the number of taxes iterated through
foreach ($_SESSION['Items']->FreightTaxes as $FreightTaxLine) {
	if ($i>0){
		echo '<BR>';
	}
	echo  $FreightTaxLine->TaxAuthDescription;
	$i++;
}

echo '</TD><TD>';

$i=0;
foreach ($_SESSION['Items']->FreightTaxes as $FreightTaxLine) {
	if ($i>0){
		echo '<BR>';
	}

	echo  '<INPUT TYPE=TEXT NAME=FreightTaxRate' . $FreightTaxLine->TaxCalculationOrder . ' MAXLENGTH=4 SIZE=4 VALUE=' . $FreightTaxLine->TaxRate * 100 . '>';

	if ($FreightTaxLine->TaxOnTax ==1){
		$TaxTotals[$FreightTaxLine->TaxAuthID] += ($FreightTaxLine->TaxRate * ($_SESSION['Items']->FreightCost + $FreightTaxTotal));
		$FreightTaxTotal += ($FreightTaxLine->TaxRate * ($_SESSION['Items']->FreightCost + $FreightTaxTotal));
	} else {
		$TaxTotals[$FreightTaxLine->TaxAuthID] += ($FreightTaxLine->TaxRate * $_SESSION['Items']->FreightCost);
		$FreightTaxTotal += ($FreightTaxLine->TaxRate * $_SESSION['Items']->FreightCost);
	}
	$i++;
	$TaxGLCodes[$FreightTaxLine->TaxAuthID] = $FreightTaxLine->TaxGLCode;
}
echo '</TD>';

echo '<TD ALIGN=RIGHT>' . number_format($FreightTaxTotal,2) . '</TD>
	<TD ALIGN=RIGHT>' . number_format($FreightTaxTotal+ $_POST['ChargeFreightCost'],2) . '</TD>
	</TR>';

$TaxTotal += $FreightTaxTotal;

$DisplaySubTotal = number_format(($_SESSION['Items']->total + $_POST['ChargeFreightCost']),2);


/* round the totals to avoid silly entries */
$TaxTotal = round($TaxTotal,2);
$_SESSION['Items']->total = round($_SESSION['Items']->total,2);
$_POST['ChargeFreightCost'] = round($_POST['ChargeFreightCost'],2);

echo '<TR>
	<TD COLSPAN=8 ALIGN=RIGHT>' . _('Invoice Totals'). '</TD>
	<TD  ALIGN=RIGHT><HR><B>'.$DisplaySubTotal.'</B><HR></TD>
	<TD COLSPAN=2></TD>
	<TD ALIGN=RIGHT><HR><B>' . number_format($TaxTotal,2) . '</B><HR></TD>
	<TD ALIGN=RIGHT><HR><B>' . number_format($TaxTotal+($_SESSION['Items']->total + $_POST['ChargeFreightCost']),2) . '</B><HR></TD>
</TR>';

//SAINTS fecha actual en factura
/*if (! isset($_POST['DispatchDate']) OR  ! Is_Date($_POST['DispatchDate'])){
	$DefaultDispatchDate = Date($_SESSION['DefaultDateFormat'],CalcEarliestDispatchDate());
} else {
	$DefaultDispatchDate = $_POST['DispatchDate'];
}*/
	$DefaultDispatchDate = date("d-m-Y");

echo '</TABLE>';



if (isset($_POST['ProcessInvoice']) && $_POST['ProcessInvoice'] != ""){

	/* SQL to process the postings for sales invoices...

	/*First check there are lines on the dipatch with quantities to invoice
	invoices can have a zero amount but there must be a quantity to invoice */
        /*
         * iJPe
         * realhost
         * 2010-04-26
         *
         * Modificacion para establecer manual el tipo de cambio
         */

        if (!is_numeric($_POST['rate'])){
            $_SESSION['CurrencyRate'] = 1;
        }else{
            $_SESSION['CurrencyRate'] = 1/$_POST['rate'];
        }

	$QuantityInvoicedIsPositive = false;

	foreach ($_SESSION['Items']->LineItems as $OrderLine) {
		if ($OrderLine->QtyDispatched > 0){
			$QuantityInvoicedIsPositive =true;
		}
	}
	if (! $QuantityInvoicedIsPositive){
		prnMsg( _('There are no lines on this order with a quantity to invoice') . '. ' . _('No further processing has been done'),'error');
		if(!$MASIVA){
        include_once('includes/footer.inc');
        exit;
        }else return false;
  //       include_once('includes/footer.inc');
		// exit;
	}

	if ($_SESSION['ProhibitNegativeStock']==1){ // checks for negative stock after processing invoice
		//sadly this check does not combine quantities occuring twice on and order and each line is considered individually :-(
		$NegativesFound = false;
		foreach ($_SESSION['Items']->LineItems as $OrderLine) {
			$SQL = "SELECT stockmaster.description,
					   		locstock.quantity,
					   		stockmaster.mbflag
		 			FROM locstock
		 			INNER JOIN stockmaster
					ON stockmaster.stockid=locstock.stockid
					WHERE stockmaster.stockid='" . $OrderLine->StockID . "'
					AND locstock.loccode='" . $_SESSION['Items']->Location . "'";

			$ErrMsg = _('Could not retrieve the quantity left at the location once this order is invoiced (for the purposes of checking that stock will not go negative because)');
			$Result = DB_query($SQL,$db,$ErrMsg);
			$CheckNegRow = DB_fetch_array($Result);
			if ($CheckNegRow['mbflag']=='B' OR $CheckNegRow['mbflag']=='M'){
				if ($CheckNegRow['quantity'] < $OrderLine->QtyDispatched){
					prnMsg( _('Invoicing the selected order would result in negative stock. The system parameters are set to prohibit negative stocks from occurring. This invoice cannot be created until the stock on hand is corrected.'),'error',$OrderLine->StockID . ' ' . $CheckNegRow['description'] . ' - ' . _('Negative Stock Prohibited'));
					$NegativesFound = true;
				}
			} elseif ($CheckNegRow['mbflag']=='A' OR $CheckNegRow['mbflag']=='E') {

				/*Now look for assembly components that would go negative */
				$SQL = "SELECT bom.component,
							   stockmaster.description,
							   locstock.quantity-(" . DB_escape_string($OrderLine->QtyDispatched)  . "*bom.quantity) AS qtyleft
						FROM bom
						INNER JOIN locstock
						ON bom.component=locstock.stockid
						INNER JOIN stockmaster
						ON stockmaster.stockid=bom.component
						WHERE bom.parent='" . DB_escape_string($OrderLine->StockID) . "'
						AND locstock.loccode='" . DB_escape_string($_SESSION['Items']->Location) . "'
						AND effectiveafter <'" . Date('Y-m-d') . "'
						AND effectiveto >='" . Date('Y-m-d') . "'";

				$ErrMsg = _('Could not retrieve the component quantity left at the location once the assembly item on this order is invoiced (for the purposes of checking that stock will not go negative because)');
				$Result = DB_query($SQL,$db,$ErrMsg);
				while ($NegRow = DB_fetch_array($Result)){
					if ($NegRow['qtyleft']<0){
						prnMsg(_('Invoicing the selected order would result in negative stock for a component of an assembly item on the order. The system parameters are set to prohibit negative stocks from occurring. This invoice cannot be created until the stock on hand is corrected.'),'error',$NegRow['component'] . ' ' . $NegRow['description'] . ' - ' . _('Negative Stock Prohibited'));
						$NegativesFound = true;
					} // end if negative would result
				} //loop around the components of an assembly item
			}//end if its an assembly item - check component stock

		} //end of loop around items on the order for negative check

		if ($NegativesFound){
			echo '<CENTER>
					<INPUT TYPE=SUBMIT NAME=Update Value=' . _('Update'). '>';
			 if(!$MASIVA){
            include_once('includes/footer.inc');
            exit;
            }else return false;
   //          include_once('includes/footer.inc');
			// exit;
		}

	}//end of testing for negative stocks


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
		include_once('includes/footer.inc');
		exit;
	}

	/*Now need to check that the order details are the same as they were when they were read into the Items array. If they've changed then someone else may have invoiced them */

	$SQL = "SELECT stkcode,
			quantity,
			qtyinvoiced,
			orderlineno
		FROM salesorderdetails
		WHERE completed=0
		AND stkcode IN (".$_SESSION['Items']->Get_StockID_List().")
		AND orderno = " . $_SESSION['ProcessingOrder'];
	// bowikaxu realhost -7 july 2007 - check only the items in the cart AND stkcode IN (".$_SESSION['Items']->Get_StockID_List().")
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
		include_once('includes/footer.inc'); exit;
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
			include_once('includes/footer.inc');
			exit;
		}
	} /*loop through all line items of the order to ensure none have been invoiced since started looking at this order*/

	DB_free_result($Result);

	/*Now Get the next invoice number - function in SQL_CommonFunctions*/

	$InvoiceNo = GetNextTransNo(10, $db);
	$PeriodNo = GetPeriod($DefaultDispatchDate, $db);

	/*Start an SQL transaction */

    //Realhost Jaime lun 5 abr 2010 15:28
    //Inicia Transaccion
    if(!DB_query('begin',$db,'','',false,false)) {
        $msg .= '(SQL) Error al efectuar el begin';
        echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' . $msg . '<p></div>';
        include_once('includes/footer.inc');
        exit;
    }
    //Termina Realhost Jaime lun 5 abr 2010 15:28

	if ($DefaultShipVia != $_SESSION['Items']->ShipVia){
		$SQL = "UPDATE custbranch SET defaultshipvia ='" . $_SESSION['Items']->ShipVia . "' WHERE debtorno='" . $_SESSION['Items']->DebtorNo . "' AND branchcode='" . $_SESSION['Items']->Branch . "'";
		$ErrMsg = _('Could not update the default shipping carrier for this branch because');
		$DbgMsg = _('The SQL used to update the branch default carrier was');
		$result = DB_query($SQL,$db, $ErrMsg, $DbgMsg, true);
	}

	$DefaultDispatchDate = FormatDateForSQL($DefaultDispatchDate);

	/*Update order header for invoice charged on */
	$SQL = "UPDATE salesorders SET comments = CONCAT(comments,' Inv ','" . $InvoiceNo . "') WHERE orderno= " . $_SESSION['ProcessingOrder'];

	$ErrMsg = _('CRITICAL ERROR') . ' ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales order header could not be updated with the invoice number');
	$DbgMsg = _('The following SQL to update the sales order was used');
	$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

	/*Now insert the DebtorTrans */
    if($MASIVA && $EMISION == true){
        $_reference = 'EMISION-' . date("Y-m-d H:i:s");
    }else{
        $_reference = "";
    }
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
			consignment,
			rh_createdate,
            rh_printnarrative,
            tipo_factura
			)
		VALUES (
			". $InvoiceNo . ",
			10,
			'" . $_SESSION['Items']->DebtorNo . "',
			'" . $_SESSION['Items']->Branch . "',
			NOW(),
			" . $PeriodNo . ",
			'". $_reference ."',
			'" . $_SESSION['Items']->DefaultSalesType . "',
			" . $_SESSION['ProcessingOrder'] . ",
			" . $_SESSION['Items']->total . ",
			" . $TaxTotal . ",
			" . $_POST['ChargeFreightCost'] . ",
			" . $_SESSION['CurrencyRate'] . ",
			'" . DB_escape_string($_POST['InvoiceText']) . "',
			" . $_SESSION['Items']->ShipVia . ",
			'"  . $_POST['Consignment'] . "',
			NOW(),
            '".$_POST['descnarr']."',
            '".$_POST['tipo_factura']."'
		)";

	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction record could not be inserted because');
	$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
	$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

	$DebtorTransID = DB_Last_Insert_ID($db,'debtortrans','id');

	/*
	insert to relate internal invoice number with an external invoice number
	december 2006 - bowikaxu
	*/
	// bowikaxu - get the next external invoice number
	$sql = "SELECT extinvoices, rh_serie FROM rh_locations WHERE loccode = '".$_SESSION['Items']->Location."'";

	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not get the total external invoices');
	$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
	$Result2 = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	$ExtInvoice = DB_fetch_array($Result2);
	if($ExtInvoice['extinvoices']=='')$ExtInvoice['extinvoices']=0;
/*
	$sql = "SELECT id FROM rh_invoicesreference WHERE loccode = '".$_SESSION['Items']->Location."' AND extinvoice = '".$ExtInvoice['extinvoices']."'";
	$res = DB_query($sql,$db);
	if((DB_num_rows($res)>0)&&(false)){
		prnMsg('El numero de factura externo ya existe !!!','error');
		DB_query('ROLLBACK',$db);
		include_once('includes/footer.inc');
		exit;
	}
*/
	// bowikaxu - insert the reference of the internal and external invoices
	$sql = "INSERT INTO rh_invoicesreference (
			extinvoice,
			intinvoice,
			loccode,
			ref
			)
			VALUES (
			'".$ExtInvoice['extinvoices']."',
			'".$InvoiceNo."',
			'".$_SESSION['Items']->Location."',
			".$DebtorTransID.")";

	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The relation transaction record could not be inserted because');
	$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
	$Result2 = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);

	// bowikaxu - add 1 to the total quantity of external invoices of that location
        //iJPe
        if ($_SESSION['Items']->LocationMoves == $_SESSION['Items']->Location){
            $sql = "UPDATE locations SET extinvoices=".($ExtInvoice['extinvoices']+1)." WHERE loccode = '".$_SESSION['Items']->Location."'";
        }else{
            $sql = "UPDATE rh_locations_virtual SET extinvoices=".($ExtInvoice['extinvoices']+1)." WHERE loccode = '".$_SESSION['Items']->Location."'";
        }

	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not get the total external invoices');
	$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
	$Result2 = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);

	// bowikaxu - end of relate internal and external invoices

	// bowikaxu realhost - 20 june 2008 - save the invoice address of the client at this time
    //rleal Mar 4, 2010 Se agregaron las address7-10 para FE
	$sql = "SELECT name,
			name2,
			currcode,
			address1,
			address2,
			address3,
			address4,
			address5,
			address6,
			address7,
			address8,
			address9,
			address10,
			taxref,
			rh_tel
			FROM debtorsmaster
			WHERE debtorno = '".$_SESSION['Items']->DebtorNo."'";
	$res_address = DB_query($sql,$db);

	if(DB_num_rows($res_address)<1){
		prnMsg('Imposible obtener la direccion fiscal del cliente','warn');
		DB_query('ROLLBACK',$db); // do a rollback
		include_once('includes/footer.inc');
		exit;
	}
	$address = DB_fetch_array($res_address);
    //rleal Mar 4, 2010 Se agregaron las address7-10 para FE
	$SQL = "INSERT INTO rh_transaddress (
			transno,
			type,
			debtorno,
			custbranch,
			trandate,
			address1,
			address2,
			address3,
			address4,
			address5,
			address6,
			address7,
			address8,
			address9,
			address10,
			name,
			name2,
			rh_tel,
			taxref,
			currcode
			)
		VALUES (
			". $InvoiceNo . ",
			10,
			'" . $_SESSION['Items']->DebtorNo . "',
			'" . $_SESSION['Items']->Branch . "',
			NOW(),
			'" . $address['address1'] . "',
			'" . $address['address2'] . "',
			'" . $address['address3'] . "',
			'" . $address['address4'] . "',
			'" . $address['address5'] . "',
			'" . $address['address6'] . "',
			'" . $address['address7'] . "',
			'" . $address['address8'] . "',
			'" . $address['address9'] . "',
			'" . $address['address10'] . "',
			'" . DB_escape_string($address['name']) . "',
			'" . DB_escape_string($address['name2']) . "',
			'" . $address['rh_tel'] . "',
			'" . $address['taxref'] . "',
			'" . $address['currcode'] . "'
		)";

	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction record could not be inserted because');
	$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
	$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

	// bowikaxu realhost - 20 june 2008 - END save invoice address


	/* Insert the tax totals for each tax authority where tax was charged on the invoice */
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


	/* If balance of the order cancelled update sales order details quantity. Also insert log records for OrderDeliveryDifferencesLog */

	foreach ($_SESSION['Items']->LineItems as $OrderLine) {

		// bowikaxu realhost March 2008 - si existe incluid la orden de compra
		if($OrderLine->POLine > 0){
			//$OrderLine->Narrative .= ' '._('Purchase Order').' '.$OrderLine->POLine;
		}

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

		if ($OrderLine->QtyDispatched !=0 AND $OrderLine->QtyDispatched!="" AND $OrderLine->QtyDispatched) {

			// Test above to see if the line is completed or not
			if ($OrderLine->QtyDispatched>=($OrderLine->Quantity - $OrderLine->QtyInv) OR $_POST['BOPolicy']=="CAN"){
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
			$Result = DB_query("SELECT mbflag FROM stockmaster WHERE stockid = '" . $OrderLine->StockID . "'",$db,"<BR>Can't retrieve the mbflag");

			$myrow = DB_fetch_row($Result);
			$MBFlag = $myrow[0];

			if ($MBFlag=="B" OR $MBFlag=="M" OR $MBFlag=="C") {
				$Assembly = False;

				/* Need to get the current location quantity
				will need it later for the stock movement */
				$SQL="SELECT locstock.quantity
					FROM locstock
					WHERE locstock.stockid='" . $OrderLine->StockID . "'
					AND loccode = '" . $_SESSION['Items']->LocationMoves . "'";
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
					AND loccode = '" . $_SESSION['Items']->LocationMoves . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated because');
				$DbgMsg = _('The following SQL to update the location stock record was used');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

				// bowikaxu realhost 15 july 2008 -  ensamblado costo por componente
			} else if ($MBFlag=='A' OR $MBFlag=='E'){ /* its an assembly */
				/*Need to get the BOM for this part and make
				stock moves for the components then update the Location stock balances */
				$Assembly=True;
				$StandardCost =0; /*To start with - accumulate the cost of the comoponents for use in journals later on */
				// bowikaxu realhost - april 2008 - geth type of item, fixed or variable
				$SQL = "SELECT bom.component,
						bom.quantity,
						bom.rh_type,
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
						AND loccode = '" . $_SESSION['Items']->LocationMoves . "'";

					if($AssParts['rh_type']==1){ // fixed
						$dispatch = 1;
					}else {
						$dispatch = $OrderLine->QtyDispatched;
					}

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
// bowikaxu realhost March 2008 - concatenar PO de la orden a cada articulo.
// bowikaxu realhost - 9 july 2008 - save item description
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
							narrative,
							description,
							rh_orderline
						) VALUES (
							'" . $AssParts['component'] . "',
							 10,
							 " . $InvoiceNo . ",
							 '" . $_SESSION['Items']->LocationMoves . "',
							 NOW(),
							 '" . $_SESSION['Items']->DebtorNo . "',
							 '" . $_SESSION['Items']->Branch . "',
							 " . $PeriodNo . ",
							 '" . _('Assembly') . ': ' . $OrderLine->StockID . ' ' . _('Order') . ': ' . $_SESSION['ProcessingOrder'] . "',
							 " . -$AssParts['quantity'] * $dispatch . ",
							 " . $AssParts['standard'] . ",
							 0,
							 " . ($QtyOnHandPrior -($AssParts['quantity'] * $dispatch)) . ",
							 '".DB_escape_string($OrderLine->Narrative)."',
							 '".DB_escape_string($OrderLine->ItemDescription)."',
							'".$OrderLine->LineNumber."'
						)";
					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records for the assembly components of'). ' '. $OrderLine->StockID . ' ' . _('could not be inserted because');
					$DbgMsg = _('The following SQL to insert the assembly components stock movement records was used');
					$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

					$SQL = "UPDATE locstock
						SET quantity = locstock.quantity - " . $AssParts['quantity'] * $dispatch . "
						WHERE locstock.stockid = '" . $AssParts['component'] . "'
						AND loccode = '" . $_SESSION['Items']->LocationMoves . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated for an assembly component because');
					$DbgMsg = _('The following SQL to update the locations stock record for the component was used');
					$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
				} /* end of assembly explosion and updates */

				/*Update the cart with the recalculated standard cost from the explosion of the assembly's components*/
				$_SESSION['Items']->LineItems[$OrderLine->LineNumber]->StandardCost = $StandardCost;
				$OrderLine->StandardCost = $StandardCost;
			} /* end of its an assembly */

			// Insert stock movements - with unit cost
			$LocalCurrencyPrice= round(($OrderLine->Price / $_SESSION['CurrencyRate']),4);

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
						description,
							rh_orderline)
					VALUES ('" . $OrderLine->StockID . "',
						10,
						" . $InvoiceNo . ",
						'" . $_SESSION['Items']->LocationMoves . "',
						NOW(),
						'" . $_SESSION['Items']->DebtorNo . "',
						'" . $_SESSION['Items']->Branch . "',
						" . $LocalCurrencyPrice . ",
						" . $PeriodNo . ",
						'" . $_SESSION['ProcessingOrder'] . "',
						" . -$OrderLine->QtyDispatched . ",
						" . $OrderLine->DiscountPercent . ",
						" . $OrderLine->StandardCost . ",
						" . ($QtyOnHandPrior - $OrderLine->QtyDispatched) . ",
						'" . DB_escape_string($OrderLine->Narrative) . "',
						'" . DB_escape_string($OrderLine->ItemDescription) . "',
							'".$OrderLine->LineNumber."')";
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
						description,
							rh_orderline)
					VALUES ('" . $OrderLine->StockID . "',
						10,
						" . $InvoiceNo . ",
						'" . $_SESSION['Items']->LocationMoves . "',
						NOW(),
						'" . $_SESSION['Items']->DebtorNo . "',
						'" . $_SESSION['Items']->Branch . "',
						" . $LocalCurrencyPrice . ",
						" . $PeriodNo . ",
						'" . $_SESSION['ProcessingOrder'] . "',
						" . -$OrderLine->QtyDispatched . ",
						" . $OrderLine->DiscountPercent . ",
						" . $OrderLine->StandardCost . ",
						'" . addslashes($OrderLine->Narrative) . "',
						'" . addslashes($OrderLine->ItemDescription) . "',
							'".$OrderLine->LineNumber."')";
			}


			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records could not be inserted because');
			$DbgMsg = _('The following SQL to insert the stock movement records was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

			/*Get the ID of the StockMove... */
			$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');

            $SQL="update rh_descuentos set stkmoveno=".$StkMoveNo." where type=30 and tipo_descuento=1 and transno=".$_SESSION['Items']->OrderNo." and orderlineno=".$OrderLine->LineNumber;
            DB_query($SQL,$db);
            $SQL="update rh_descuentos set stkmoveno=".$StkMoveNo." where type=30 and tipo_descuento=2 and transno=".$_SESSION['Items']->OrderNo." and orderlineno=".$OrderLine->LineNumber;
            DB_query($SQL,$db);

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

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Taxes and rates applicable to this invoice line item could not be inserted because');
				$DbgMsg = _('The following SQL to insert the stock movement tax detail records was used');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			}


        //*****************************PEDIMENTOS ************************
                 //var_dump($OrderLine->SerialItems);
                if(count($OrderLine->PedimentoItems)>0){
                    //var_dump($OrderLine->PedimentoItems);
                   	foreach($OrderLine->PedimentoItems as $Item){
							if (strlen(trim($Item->BundleRef)) >0){
									$SQL = "UPDATE stockpedimentoitems SET quantity = quantity - '" . $Item->BundleQty . "'";
									$SQL .= "WHERE stockid='" . $OrderLine->StockID . "'
											 AND loccode = '" .$_SESSION['Items']->LocationMoves. "'
											 AND pedimentoid = '" . $Item->BundleRef . "'";
                                $ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
							    $DbgMsg = _('The following SQL to insert the serial stock movement records was used');
							    $Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

							$SQL = "INSERT INTO stockpedimentomoves (stockmoveno,
											stockid,
											pedimentoid,
											moveqty,rh_orderline)
									VALUES (
										'" . $StkMoveNo . "',
										'" . $OrderLine->StockID . "',
										'" . $Item->BundleRef . "',
										'" . -$Item->BundleQty . "',
                                        '" . -$Item->lineOrder . "'
										)";
							$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
							$DbgMsg = _('The following SQL to insert the serial stock movement records was used');
							$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);


                }
                }
                }
                //****************************************************************

			/*Insert the StockSerialMovements and update the StockSerialItems  for controlled items*/

			if ($OrderLine->Controlled ==1){
				foreach($OrderLine->SerialItems as $Item){
					/*We need to add the StockSerialItem record and
					The StockSerialMoves as well */

					$SQL = "UPDATE stockserialitems
							SET quantity= quantity - " . $Item->BundleQty . "
							WHERE stockid='" . $OrderLine->StockID . "'
							AND loccode='" . $_SESSION['Items']->LocationMoves . "'
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

			// bowikaxu realhost Feb 2008 - Descomponer Articulos en sus Componentes
			// bowikaxu realhost 15 july 2008 - Ensamblado Costo por Componente
			// TODO hacer un barrido de sus articulos en bom de parent = $OrderLine->StockID y se hacen los siguientes inserts a los articulos componentes
			if($MBFlag == 'C' OR $MBFlag == 'E'){
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

					$CompCost = $Component['materialcost']+$Component['labourcost']+$Component['overheadcost'];
					//$CompPrice = GetPrice($Component['stockid'],$_SESSION['Items']->DebtorNo,$_SESSION['Items']->Branch);

					if($Component['rh_type']==0){ // cantidad variable
						$CompQty = 	$Component['reqqty'] * $OrderLine->QtyDispatched;

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
								10,
								" . $InvoiceNo . ",
								NOW(),
								" . $PeriodNo . ",
								" . GetCOGSGLAccount($Area, $Component['stockid'], $_SESSION['Items']->DefaultSalesType, $db) . ",
								'" . $_SESSION['Items']->DebtorNo . " - " . $Component['stockid'] . " x " . $Component['reqqty']." x " . $OrderLine->QtyDispatched . " @ " . $CompCost . "',
								" . $CompCost * $CompQty . "
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
						10,
						" . $InvoiceNo . ",
						NOW(),
						" . $PeriodNo . ",
						" . $StockGLCode['stockact'] . ",
						'" . $_SESSION['Items']->DebtorNo . " - " .$OrderLine->StockID ." -> " .$Component['stockid'] . " x " . $Component['reqqty']." x " . $OrderLine->QtyDispatched . " @ " . $CompCost . "',
						" . (-$CompCost * $CompQty) . "
					)";

						$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock side of the cost of sales GL posting could not be inserted because');
						$DbgMsg = _('The following SQL to insert the GLTrans record was used');
						$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
					} /* end of if GL and stock integrated and standard cost !=0 */

					}else { // cantidad fija
						$CompQty = $Component['reqqty'];

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
						10,
						" . $InvoiceNo . ",
						NOW(),
						" . $PeriodNo . ",
						" . GetCOGSGLAccount($Area, $Component['stockid'], $_SESSION['Items']->DefaultSalesType, $db) . ",
						'" . $_SESSION['Items']->DebtorNo . " - " . $Component['stockid'] . " x " . $CompQty . " @ " . $CompCost . "',
						" . $CompCost * $CompQty . "
					)";

						$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The cost of sales GL posting could not be inserted because');
						$DbgMsg = _('The following SQL to insert the GLTrans record was used');
						$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

						/*now the stock entry*/
						// bowikaxu - debe ser la cuenta del producto que se vende
						//$StockGLCode = GetStockGLCode($Component['stockid'],$db);
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
						NOW(),
						" . $PeriodNo . ",
						" . $StockGLCode['stockact'] . ",
						'" . $_SESSION['Items']->DebtorNo . " - " . $OrderLine->StockID . " -> ".$Component['stockid']." x " . $CompQty . " @ " . $CompCost . "',
						" . (-$CompCost * $CompQty) . "
					)";

						$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock side of the cost of sales GL posting could not be inserted because');
						$DbgMsg = _('The following SQL to insert the GLTrans record was used');
						$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
					} /* end of if GL and stock integrated and standard cost !=0 */

					}

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
						10,
						" . $InvoiceNo . ",
						NOW(),
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
							10,
							" . $InvoiceNo . ",
							NOW(),
							" . $PeriodNo . ",
							" . $SalesGLAccounts['discountglcode'] . ",
							'" . $_SESSION['Items']->DebtorNo . " - " . $OrderLine->StockID . " @ " . ($OrderLine->DiscountPercent * 100) . "%',
							" . ($OrderLine->Price * $OrderLine->QtyDispatched * $OrderLine->DiscountPercent/$_SESSION['CurrencyRate']) . "
						)";

						$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales discount GL posting could not be inserted because');
						$DbgMsg = _('The following SQL to insert the GLTrans record was used');
						$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
					} /*end of if discount !=0 */
				} /*end of if sales integrated with debtors */

			}else {
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
						NOW(),
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
						10,
						" . $InvoiceNo . ",
						NOW(),
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
						10,
						" . $InvoiceNo . ",
						NOW(),
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
							10,
							" . $InvoiceNo . ",
							NOW(),
							" . $PeriodNo . ",
							" . $SalesGLAccounts['discountglcode'] . ",
							'" . $_SESSION['Items']->DebtorNo . " - " . $OrderLine->StockID . " @ " . ($OrderLine->DiscountPercent * 100) . "%',
							" . ($OrderLine->Price * $OrderLine->QtyDispatched * $OrderLine->DiscountPercent/$_SESSION['CurrencyRate']) . "
						)";

						$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales discount GL posting could not be inserted because');
						$DbgMsg = _('The following SQL to insert the GLTrans record was used');
						$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
					} /*end of if discount !=0 */
				} /*end of if sales integrated with debtors */
			}

                       /*
                         * iJPe
                         * 2010-04-28
                         */
                        $sqlUpdatSM = "select max(stkmoveno) as last from stockmoves";
                        $rowSM = DB_fetch_array(DB_query($sqlUpdatSM, $db));

                        $sqlUpdatSM = "call update_newqoh(".$rowSM['last'].")";
                        DB_query($sqlUpdatSM, $db);

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
						10,
						" . $InvoiceNo . ",
						NOW(),
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
					10,
					" . $InvoiceNo . ",
					NOW(),
					" . $PeriodNo . ",
					" . $_SESSION['CompanyRecord']['freightact'] . ",
					'" . $_SESSION['Items']->DebtorNo . "',
					" . (-($_SESSION['Items']->FreightCost)/$_SESSION['CurrencyRate']) . "
				)";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The freight GL posting could not be inserted because');
			$DbgMsg = _('The following SQL to insert the GLTrans record was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
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
						NOW(),
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
	} /*end of if Sales and GL integrated */

    //Factura Electronica
    /*
//if($_SESSION['CFDIVersion']==22){
    $idDebtortrans = $DebtorTransID;
    $sql = "select transno, type from debtortrans where id = $idDebtortrans";
    $result = DB_fetch_array(DB_query($sql, $db));
    $transno = $result['transno'];
    $type = $result['type'];
    $idCsdYSerie = explode('-', $_POST['selectIdFolio']);
    $idCsd = $idCsdYSerie[0];
    $serie = $idCsdYSerie[1];
    $idXsd = $_POST['idXsd'];
    $xmlXsd = $_POST['addenda'];
    $sql = "select not isnull(v.id_salesorders) is_transportista from debtortrans d join rh_vps__transportista v on d.order_ = v.id_salesorders and d.id = $idDebtortrans";
    $result = DB_query($sql, $db);
    $is_transportista = DB_num_rows($result);
    $datosSat;
    $datosSat = cfd($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $idXsd, $xmlXsd,$_POST['metodoPago'],$_POST['cuentaPago']);
    printDatosSat($datosSat);
    */
 //}elseif($_SESSION['CFDIVersion']==32) {
        $Letras = new Numbers_Words();

		$tot = explode(".",number_format($Comprobante_total,2,'.',''));
		$Letra = Numbers_Words::toWords($tot[0],"es");

		if($tot[1]==0){
		$ConLetra = $Letra.' '.$Comprobante_NombreMoneda." 00/100 ".$Comprobante_Moneda;
		}else if(strlen($tot[1])>=2){
		$ConLetra = $Letra.' '.$Comprobante_NombreMoneda.' '.$tot[1]."/100 ".$Comprobante_Moneda;
		}else {
		$ConLetra = $Letra.' '.$Comprobante_NombreMoneda.' '.$tot[1]."0/100 ".$Comprobante_Moneda;
		}
    $idDebtortrans = $DebtorTransID;
    $sql = "select transno, type from debtortrans where id = $idDebtortrans";
    $result = DB_fetch_array(DB_query($sql, $db));
    $transno = $result['transno'];
    $type = $result['type'];
    $idCsdYSerie = explode('-', $_POST['selectIdFolio']);
    $idCsd = $idCsdYSerie[0];
    $serie = getSerieByBranch($_SESSION['Items']->Location,10,$db);
    $idXsd = $_POST['idXsd'];
    $xmlXsd = $_POST['addenda'];
    $sql = "select not isnull(v.id_salesorders) is_transportista from debtortrans d join rh_vps__transportista v on d.order_ = v.id_salesorders and d.id = $idDebtortrans";
    $result = DB_query($sql, $db);
    $is_transportista = DB_num_rows($result);
    $datosSat;
    if(!$MASIVA){
	    $datosSat = cfdi($db, $idDebtortrans, $transno, $type, $serie, $idCsd,$_SESSION['metodoPago'],$_SESSION['cuentaPago']);
	    printDatosSat($datosSat);
    }
 //}

    //Termina Factura Electronica
	if(!$MASIVA){
		unset($_SESSION['Items']->LineItems);
		unset($_SESSION['Items']);
		unset($_SESSION['ProcessingOrder']);
	}
	DB_query('commit',$db,'','',false,false);
	echo _('F.I.'). ' '. $InvoiceNo .' '. _('processed'). '<BR>';

	// bowikaxu - mostrar el numero de factura externa
    echo _('Factura'). ' '. $datosSat['serie'].$datosSat['folio'] .' '. _('processed'). '<BR>';

	if ($_SESSION['InvoicePortraitFormat']==0){
		//echo '<A HREF="'.$rootpath.'/rh_PrintCustTrans.php?' . SID . 'FromTransNo='.$InvoiceNo.'&InvOrCredit=Invoice&PrintPDF=True">'. _('Print this invoice'). '</A><BR>';
	} else {
		//echo '<A target="_blank" HREF="'.$rootpath.'/PrintCustTransPortrait.php?' . SID . 'FromTransNo='.$InvoiceNo.'&InvOrCredit=Invoice&PrintPDF=True">'. _('Print this invoice'). '</A><BR>';
	}
	echo '<A HREF="'.$rootpath.'/SelectSalesOrder.php?' . SID . '">'. _('Select another order for invoicing'). '</A><BR>';
	echo '<A HREF="'.$rootpath.'/SelectOrderItems.php?' . SID . 'NewOrder=Yes">'._('Sales Order Entry').'</A><BR>';
    //Realhost Jaime Mier 24 Mar 2010 11:59, se agrego la liga para imprimir la factura electronica en formato PDF
    //Jaime, checamos si la factura es transportista
    if($is_transportista)
        echo '<A HREF="'.$rootpath."/rh_printFE.php?transno=$InvoiceNo&isTransportista=true" . SID . '">'._('Imprimir Factura Transportista en PDF').'</A><BR>';
    else
    //\Jaime, checamos si la factura es transportista
    echo '<A target="_blank" HREF="'.$rootpath."/PHPJasperXML/sample1.php?transno=$InvoiceNo&afil=true" . SID . '">'._('Imprimir Factura Electronica en PDF').'</A><BR>';
    //echo '<A target="_blank" HREF="'.$rootpath."/PHPJasperXML/sample1.php?transno=$InvoiceNo&copia=si&afil=true" . SID . '">'._('Imprimir Factura Electronica en PDF COPIA').'</A><BR>';

    //echo '<A target="_blank" HREF="'.$rootpath."/rh_printFE.php?transno=$InvoiceNo" . SID . '">'._('Imprimir Factura Electronica en PDF').'</A><BR>';
    //echo '<A target="_blank" HREF="'.$rootpath."/rh_printFE.php?copia=si&transno=$InvoiceNo" . SID . '">'._('Imprimir Factura Electronica en PDF COPIA').'</A><BR>';
    //Termina Realhost Jaime Mier 24 Mar 2010 11:59, se agrego la liga para imprimir la factura electronica en formato PDF
    //Realhost Jaime Mier 24 Mar 2010 11:59, se agrego la liga para descargar la Factura Electronica en formato XML
 if($_SESSION['CFDIVersion']==22){
   echo '<A target="_blank" HREF="'.$rootpath."/rh_j_downloadFacturaElectronicaXML.php?downloadPath=XMLFacturacionElectronica/facturasElectronicas/" . $datosSat['noCertificado'] . "/" . $datosSat['serie'] . $datosSat['folio'] . '-' . $datosSat['transno'] .'.xml'. SID . '">'._('Descargar Factura Electronica en formato XML').'</A><BR>';
 }elseif($_SESSION['CFDIVersion']==32) {
   echo '<A target="_blank" HREF="'.$rootpath."/rh_j_downloadFacturaElectronicaXML.php?downloadPath=XMLFacturacionElectronica/xmlbycfdi/" . $datosSat['UUID']. '.xml'. SID . '">'._('Descargar Factura Electronica en formato XML').'</A><BR>';
 }

 if($_SESSION['CFDIVersion']==22){
   echo "<a target='_blank' href='".$rootpath."/EmailCustTrans.php?FromTransNo=".$transno."&InvOrCredit=Invoice&isCfd=Yes'>Enviar esta factura  por Correo</a><br />";
 }elseif($_SESSION['CFDIVersion']==32) {
   echo "<a target='_blank' href='".$rootpath."/EmailCustTrans_CFDI.php?FromTransNo=".$transno."&InvOrCredit=Invoice&isCfd=Yes'>Enviar esta factura por Correo</a><BR />";
 }


    //Termina Realhost Jaime Mier 24 Mar 2010 11:59, se agrego la liga para descargar la Factura Electronica en formato XML
	/*end of process invoice */

} else { /*Process Invoice not set so allow input of invoice data */
    ?>
    <script type="text/javascript">
        function getHtmlAddenda(idAddenda){
            if(!idAddenda)
                window.location.href = 'ConfirmDispatch_Invoice.php?&OrderNumber=' + <?php echo $_GET['OrderNumber'] ?>;
            else
                window.location.href = 'ConfirmDispatch_Invoice.php?&OrderNumber=' + <?php echo $_GET['OrderNumber'] ?> + '&idAddenda=' + idAddenda;
        }
    </script>
    <?php
   /* $idAddenda = $_GET['idAddenda'];
    $ws = getWs();
    echo 'Addenda:';
    $xsds = $ws->addendas()->return;
    $selectAddendas = '<select name="idXsd" onchange="getHtmlAddenda(this.value)"><option></option>';
    for($i = 2; $i < count($xsds); $i++){
        $id = $xsds[$i]->item[0];
        $nombre = $xsds[$i]->item[1];
        $selectAddendas .= '<option value="' . $id . '" '. ($id == $idAddenda?'selected':'') .'>' . $nombre . '</option>';
    }
    $selectAddendas .= '</select>';
    echo $selectAddendas;
    if(v($idAddenda)){
        //echo $ws->getHtmlAddenda(array('idAddenda' => $idAddenda))->return;
        ?>
        <br/>
        <br/>
        Fecha de Remision: <input type="text" id="addendaSoriana_fechaRemision" name="addendaSoriana_fechaRemision"/>
        <br/>
        Folio de Nota de Entrada: <input type="text" id="addendaSoriana_folioNotaEntrada" name="addendaSoriana_folioNotaEntrada"/>
        <br/>
        Numero de Cajas: <input type="text" id="addendaSoriana_numeroDeCajas" name="addendaSoriana_numeroDeCajas"/>
        <br/>
        <?php
    }   */
    //SAINTS bloquear captura de fecha 03/02/2011


    echo "<TABLE>
            <TR>
                <TD>" ._('Tipo Factura'). ":</TD>
                <TD>
                    <select id='tipo_factura' name='tipo_factura' required='required' >";
                    echo "<option value=''>SELECCIONE</option>";
                    $ListTipoFacturas = "SELECT id, tipo FROM rh_tipofacturas WHERE activo = 1";
                    $_ListTipoFacturas = DB_query($ListTipoFacturas, $db);
                    while ($ListTipoFacturas = DB_fetch_assoc($_ListTipoFacturas)) {
                        echo "<option value='{$ListTipoFacturas['id']}'>{$ListTipoFacturas['tipo']}</option>";
                    }

            echo"   </select>
                </TD>
            </TR>"; ?>
            <script type="text/javascript">
                $("#tipo_factura option[value='<?=$_POST['tipo_factura']?>']").attr("selected",true);
            </script>
        <?php
        echo ' <TR>
                <TD>' ._('Date Of Dispatch'). ':</TD>
                <TD><input type="text" maxlength="12" size="16" name="DispatchDate" value="'.$DefaultDispatchDate.'" readonly></TD>
            </TR>';


	//SAINTS fin
/****************************************************************************************************************************
* Jorge Garcia
* 24/Feb/2009 Impresion de Descripcion o Narrative
****************************************************************************************************************************/
	echo "<TR>
	<TD>"._('Print')." :</TD>
	<TD>"._('Description')."<input  type=radio name='descnarr' value=0 checked >
	"._('Narrative')."<input type=radio name='descnarr' value=1 ></TD>
	</TR>";
/****************************************************************************************************************************
* Jorge Garcia Fin Modificacion
****************************************************************************************************************************/
	echo '<TR>
		<TD>' . _('Consignment Note Ref'). ':</TD>
		<TD><INPUT TYPE=text MAXLENGTH=15 SIZE=15 name=Consignment value="' . $_POST['Consignment'] . '"></TD>
	</TR>';
        /*
         * iJPe
         * realhost
         * 2010-04-26
         *
         * Modificacion para establecer manual el tipo de cambio
         */

          echo '<TR>
                    <TD>' . _('Metodo de Pago'). ':</TD>
                    <TD><INPUT TYPE=text SIZE=65 name="metodoPago" value="' . $_SESSION['metodoPago'] . '"></TD>
    </TR>';
    echo '<TR>
                    <TD>' . _('Cuenta de Pago'). ':</TD>
                    <TD><INPUT TYPE=text SIZE=65 name="cuentaPago" value="' . $_SESSION['cuentaPago'] . '"></TD>
            </TR>';

        if (!is_numeric($_POST['rate'])){
            $_POST['rate'] = 1;
        }
         //var_dump($_SESSION);
        if ($_SESSION['CurrencyRate'] != 1)
        {

            echo '<TR>
                    <TD>' . _('Tipo de Cambio'). ':</TD>
                    <TD><INPUT TYPE=text MAXLENGTH=15 SIZE=15 name="rate" value="' . $_POST['rate'] . '"></TD>
            </TR>';

        }

        //
	echo '<TR>
		<TD>'.('Action For Balance'). ':</TD>
		<TD><SELECT name=BOPolicy><OPTION SELECTED Value="BO">'._('Automatically put balance on back order').'<OPTION Value="CAN">'._('Cancel any quantites not delivered').'</SELECT></TD>
	</TR>';
	echo '<TR>
		<TD>' ._('Invoice Text'). ':</TD>';


		//$cadena = nl2br(html_entity_decode(str_replace('\r\n', '&lt;br /&gt;', $_POST['InvoiceText']),ENT_QUOTES,"cp1252"));

    if(($_SESSION['DatabaseName'] == 'prese_erp_001')){
        $_POST['InvoiceText'] = "Consumo de nucleos ---- Remision: Nucleos:";
    }

    echo '<TR>
		<TD>' ._('Invoice Text'). ':</TD>
		<TD><TEXTAREA NAME=InvoiceText COLS=31 ROWS=5>' . $_POST['InvoiceText'] . '</TEXTAREA></TD>
	    </TR>';

    //Realhost Jaime Lun 22 Mar 2010 17:47, Se agrego el Select HTML para escoger el folio
    //Obtenemos las series de la location escogida para la factura (pedido)
    $idLocation = $_SESSION['Items']->Location;
    $sql = "select id_ws_csd, serie from rh_cfd__locations__systypes__ws_csd where id_locations = '$idLocation' and !isnull(id_ws_csd) and id_systypes = 10";
    $result = DB_query($sql,$db,'','',false,false);
    //Termina Obtenemos las series de la location escogida para la factura (pedido)
    //Creamos el Select con las series
    $tableRowSerieCfd = '<tr><td>' ._('Serie del CFD'). ':</td>';
    $selectIdFolio = '<select id="selectIdFolio" name="selectIdFolio"><option></option>';
    switch(DB_num_rows($result)) {
        case 1:
        //se usa la unica serie que tiene un csd y esta asosiada a esta location
            $row = DB_fetch_array($result);
            $inputHiddenSerie = '<input type="hidden" value="' . $row[0] . '-' . $row[1] . '" id="selectIdFolio" name="selectIdFolio"/>';
            echo $inputHiddenSerie;
            break;
        default:
        //si hay mas de una serie o si no hay series asosiadas a esta location (y con un csd)
            while($row = DB_fetch_array($result))
                $selectIdFolio .= '<option value="' . $row[0] . '-' . $row[1] . '">' . $row[1] . '</option>';
            $selectIdFolio .= '</select>';
            $tableRowSerieCfd.= '<td>' . $selectIdFolio . '</td></tr>';
            echo $tableRowSerieCfd;
            break;
    }
    //Termina Creamos el Select con las series
    //Termina Realhost Jaime Lun 22 Mar 2010 17:47, Se agrego el Select HTML para escoger el folio
	echo '</TABLE>
	<CENTER>
	<INPUT TYPE=SUBMIT NAME=Update Value=' . _('Update'). '><BR>';
	echo '<INPUT TYPE=SUBMIT ';
	if($TaxTotal<0)
		echo ' disabled=disabled ';
	echo 'NAME="ProcessInvoice" Value="'._('Process Invoice').' '._('from').' '.$_SESSION['Items']->Location.'"/></CENTER>';

	echo '<INPUT TYPE=HIDDEN NAME="ShipVia" VALUE="' . $_SESSION['Items']->ShipVia . '">';
}

echo '</FORM>';
//Realhost Jaime Lun 22 Mar 2010 18:45, se agrego la siguiente funcion de Js para validar que se haya escogido una Serie de CFD
?>


<?php if(v($idAddenda)){ ?>
<script type="text/javascript" src="rh_j_globalFacturacionElectronica.js"></script>
<?php } ?>
<script type="text/javascript">
    function isSerieSelected(){
        var elementSelectSerieDelCfd = document.getElementById("selectIdFolio")
        if(!elementSelectSerieDelCfd.value){
            if(elementSelectSerieDelCfd.options.length == 1){
                alert('No ha asosiado una Serie con la localidad seleccionada, para hacerlo, vaya a la pagina de Administracion de Series')
                return false
            }
            alert('Favor de seleccionar una serie')
            elementSelectSerieDelCfd.focus()
            return false
        }
        else
            return true
    }

    function validateForm(){
        if(!isSerieSelected())
            return false;
        <?php
            if(v($idAddenda)){
                switch($idAddenda){
                    case 1:?>
                        var validateAddendaSoriana = function(){
                            var elementAddendaSoriana_fechaRemision = document.getElementById('addendaSoriana_fechaRemision')
                            var elementAddendaSoriana_folioNotaEntrada = document.getElementById('addendaSoriana_folioNotaEntrada')
                            var elementAddendaSoriana_numeroDeCajas = document.getElementById('addendaSoriana_numeroDeCajas')
                            if(!(elementAddendaSoriana_fechaRemision.value && elementAddendaSoriana_fechaRemision.value.toString().match(/^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/)))
                                throw new InvalidInputInHtmlInputException('La Fecha de Remision no es valida', elementAddendaSoriana_fechaRemision)
                            if(!(elementAddendaSoriana_folioNotaEntrada.value && isPositiveInteger(elementAddendaSoriana_folioNotaEntrada.value)))
                                throw new InvalidInputInHtmlInputException('El Folio de Nota de entrada no es valido', elementAddendaSoriana_folioNotaEntrada)
                            if(!(elementAddendaSoriana_numeroDeCajas.value && isPositiveInteger(elementAddendaSoriana_numeroDeCajas.value)))
                                throw new InvalidInputInHtmlInputException('El Numero de Cajas no es valido', elementAddendaSoriana_numeroDeCajas)
                        }
                        try{
                            validateAddendaSoriana()
                        }
                        catch(exception){
                            if(exception instanceof InvalidInputInHtmlInputException){
                                try{
                                    exception.element.focus()
                                    exception.element.select()
                                }
                                catch(e){}
                                alert(exception.message);
                            }
                            else
                                alert(exception)
                            return false;
                        }
        <?php
                    break;
                    default:
                        ?>
                        alert('Esta addenda aun no esta soportada')
                        return false;
                        <?php
                    break;
                }
            }
        ?>
    }
</script>
<?php
//Termina Realhost Jaime Lun 22 Mar 2010 18:45, se agrego la siguiente funcion de Js para validar que se haya escogido una Serie de CFD
        if(!$MASIVA){
        include_once('includes/footer.inc');
        exit;
        }else return false;
//include_once('includes/footer.inc');
?>
<script type="text/javascript">
    var pedimentos = {}

    function agregarPedimento(cell, lineNumber){
        if(!pedimentos[lineNumber])
            pedimentos[lineNumber] = 0

        var idPedimento = pedimentos[lineNumber]++
        var row = cell.parentNode.parentNode
        var table = row.parentNode
        var newRow = table.insertRow(row.rowIndex+1)
        var cell
//        cell = newRow.insertCell(0)
//        cell.innerHTML = '<input type="button" value="Eliminar" onclick="this.parentNode.parentNode.parentNode.deleteRow(this.parentNode.parentNode.rowIndex)" />'
        cell = newRow.insertCell(0)
        cell.innerHTML = 'Aduana: <input type="text" size="10" name="' + 'aduana-' + lineNumber + '-' + (idPedimento) + '" value="" />'
        cell = newRow.insertCell(0)
        cell.innerHTML = 'Fecha: <input type="text" size="6" name="' + 'fecha-' + lineNumber + '-' + (idPedimento) + '" value="" />'
        cell = newRow.insertCell(0)
        cell.innerHTML = 'Numero: <input type="text" size="4" name="' + 'numero-' + lineNumber + '-' + (idPedimento) + '" value="" />'
    }

    function quitarPedimento(cell, lineNumber){
        var row = cell.parentNode.parentNode
        var rowIndexToRemove = row.rowIndex+1
        var table = row.parentNode
        var rowToRemove = table.rows[rowIndexToRemove]
        if(!rowToRemove.cells[0].childNodes[1].name)
            return
        table.deleteRow(rowIndexToRemove)
        pedimentos[lineNumber]--
    }
</script>
