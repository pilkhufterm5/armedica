<?php

/* werbERP Revision: 1.10 $ */

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-09-15 09:28:12 -0500 (Mon, 15 Sep 2008) $
 * $Rev: 401 $
 */

$PageSecurity = 2;

include('includes/DefineCartClass2.php');
include('includes/DefineSerialItems.php');
include('includes/session.inc');

$title = _('Ver Remisiones');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
include('includes/FreightCalculation.inc');
include('includes/GetSalesTransGLCodes.inc');

//Jaime includes
include('XMLFacturacionElectronica/utils/File.php');
if($_SESSION['CFDIVersion']==32){
    require_once('Numbers/Words.php');
    require_once('CFD22Manager.php');
    require_once('CFD22.php');
    include('rh_cfdFunctions22.php');
 }else{
    include('rh_j_cfdFunctions.php');
 }

$_SESSION['metodoPago']=(isset($_POST['metodoPago'])?$_POST['metodoPago']:'No Identificado');
$_SESSION['cuentaPago']=(isset($_POST['cuentaPago'])?$_POST['cuentaPago']:'No Identificado');

//Termina Jaime includes

// SELECCIONAR EL CLIENTE PARA VER SUS REMISIONES
$msg="";
if (!isset($_SESSION['CustomerID'])){ //initialise if not already done
	$_SESSION['CustomerID']="";
}

if (!isset($_POST['PageOffset'])) {
  $_POST['PageOffset'] = 1;
} else {
  if ($_POST['PageOffset']==0) {
    $_POST['PageOffset'] = 1;
  }
}

if (isset($_POST['Search']) OR isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])){
	if (isset($_POST['Search'])){
		$_POST['PageOffset'] = 1;
	}
	If ($_POST['Keywords'] AND $_POST['CustCode']) {
		$msg=_('Customer name keywords have been used in preference to the customer code extract entered') . '.';
		$_POST['Keywords'] = strtoupper($_POST['Keywords']);
	}
	If ($_POST['Keywords']=="" AND $_POST['CustCode']=="") {
		//$msg=_('At least one Customer Name keyword OR an extract of a Customer Code must be entered for the search');
		$SQL= "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno";
	} else {
		If (strlen($_POST['Keywords'])>0) {

			$_POST['Keywords'] = strtoupper($_POST['Keywords']);

			//insert wildcard characters in spaces

			$i=0;
			$SearchString = "%";
			while (strpos($_POST['Keywords'], " ", $i)) {
				$wrdlen=strpos($_POST['Keywords']," ",$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . "%";
				$i=strpos($_POST['Keywords']," ",$i) +1;
			}
			$SearchString = $SearchString . substr($_POST['Keywords'],$i)."%";
			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.name " . LIKE . " '$SearchString'";

		} elseif (strlen($_POST['CustCode'])>0){

			$_POST['CustCode'] = strtoupper($_POST['CustCode']);

			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.debtorno " . LIKE  . " '%" . $_POST['CustCode'] . "%'";
		}
	} //one of keywords or custcode was more than a zero length string
	$ErrMsg = _('The searched customer records requested cannot be retrieved because');
	$result = DB_query($SQL,$db,$ErrMsg);
	if (DB_num_rows($result)==1){
		$myrow=DB_fetch_array($result);
		$_POST['Select'] = $myrow['debtorno'];
		unset($result);
	} elseif (DB_num_rows($result)==0){
		prnMsg(_('No customer records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
	}$msg="";
if (!isset($_SESSION['CustomerID'])){ //initialise if not already done
	$_SESSION['CustomerID']="";
}

if (!isset($_POST['PageOffset'])) {
  $_POST['PageOffset'] = 1;
} else {
  if ($_POST['PageOffset']==0) {
    $_POST['PageOffset'] = 1;
  }
}

if (isset($_POST['Search']) OR isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])){
	if (isset($_POST['Search'])){
		$_POST['PageOffset'] = 1;
	}
	If ($_POST['Keywords'] AND $_POST['CustCode']) {
		$msg=_('Customer name keywords have been used in preference to the customer code extract entered') . '.';
		$_POST['Keywords'] = strtoupper($_POST['Keywords']);
	}
	If ($_POST['Keywords']=="" AND $_POST['CustCode']=="") {
		//$msg=_('At least one Customer Name keyword OR an extract of a Customer Code must be entered for the search');
		$SQL= "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno";
	} else {
		If (strlen($_POST['Keywords'])>0) {

			$_POST['Keywords'] = strtoupper($_POST['Keywords']);

			//insert wildcard characters in spaces

			$i=0;
			$SearchString = "%";
			while (strpos($_POST['Keywords'], " ", $i)) {
				$wrdlen=strpos($_POST['Keywords']," ",$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . "%";
				$i=strpos($_POST['Keywords']," ",$i) +1;
			}
			$SearchString = $SearchString . substr($_POST['Keywords'],$i)."%";
			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.name " . LIKE . " '$SearchString'";

} //end of if search
		}
	}
}

// TERMINA SELECCIONAR CLIENTE PARA VER REMISIONES

if(isset($_POST['Select'])){

	if(isset($_POST['todas'])){
		$estado = 1;
	}else $estado = 0;

		$SQL = "SELECT debtortrans.transno,					
					debtortrans.order_,
					debtortrans.ovamount AS ttot,
					debtortrans.debtorno,
					debtortrans.type,
					debtorsmaster.name,
					debtorsmaster.address1,
					custbranch.brname,
					rh_invoiceshipment.Invoice,
					rh_invoiceshipment.Shipment,
					rh_invoiceshipment.Fecha,
					rh_invoiceshipment.Facturado,
					rh_invoiceshipment.type AS RType,
					debtortrans.ovamount AS ordervalue,
					debtortrans.ovfreight AS orderfreight
				FROM debtortrans,
					debtorsmaster,
					custbranch,
					rh_invoiceshipment
				WHERE rh_invoiceshipment.Shipment = debtortrans.transno
				AND rh_invoiceshipment.Facturado = 0
				AND debtortrans.type = 20000
				AND debtorsmaster.debtorno = '".$_POST['Select']."'
				AND debtortrans.debtorno = '".$_POST['Select']."'
				AND debtortrans.rh_status != 'C'
				AND custbranch.debtorno = '".$_POST['Select']."'
				GROUP BY debtortrans.transno
				ORDER BY rh_invoiceshipment.Shipment";
				// debtortrans.transno = rh_invoiceshipment.Shipment
// bowikaxu realhost sept 2007 - no mostrar remisiones canceladas
		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Imposible obtener valores de remisiones');
		$DbgMsg = _('Fallo el query de la base de datos');
 		$RemOrders = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
/*show a table of the orders returned by the SQL */

	echo '<TABLE CELLPADDING=2 COLSPAN=6 WIDTH=100%>';

	$tableheader = "<TR><TD class='tableheader' size='2'>" . _('Facturar') . "</TD>
			<TD class='tableheader'>".('Rem')." #</TD>
			<TD class='tableheader'>" . _('Debtor') . "</TD>
			<TD class='tableheader'>" . _('Branch') . "</TD>
			<TD class='tableheader'>" . _('Address') . " #</TD>
			<TD class='tableheader'>" . _('Date') . "</TD>
			<TD class='tableheader'>" . _('Tipo') . "</TD>
			<TD class='tableheader'>" . _('Total') . "</TD></TR>";

	echo $tableheader;

	$j = 1;
	$k=0; //row colour counter
	while ($myrow=DB_fetch_array($RemOrders)) {


		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k=1;
		}

		$ViewPage = $rootpath . '/rh_PDFRemGde.php?' .SID . '&FromTransNo=' . $myrow['transno'].'&InvOrCredit=Invoice';
		$FomatedFecha = ConvertSQLDate($myrow['Fecha']);

		$Sub = $myrow['ordervalue'] + $myrow['orderfreight'];
		$FormatedOrderValue = number_format($Sub,2);
		if($estado==0){

			// verificar el tipo de remision

			if($myrow['RType']==0){
				$RType = 'Normal';
			}else if($myrow['RType']==1){
				$RType = 'Muestra';
			}else if($myrow['RType']==2){
				$RType = 'Punto de Venta';
			}

			// fin verificar tipo de remision
		//		printf("<td><input type='checkbox' name='%s'></input></td>
			echo "<FORM ACTION=".$_SERVER['PHP_SELF']. " METHOD=POST>";
	printf("<td><input type='checkbox' name='remisiones[]' value='%s'></input></td>
			<td><A target='_blank' HREF='%s'>%s</A></td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			</tr>",
			$myrow['transno'],
			$ViewPage,
			$myrow['transno'],
			$myrow['debtorno'],
			$myrow['brname'],
			$myrow['address1'],
			$myrow['Fecha'],
			$RType,
			"$ ".number_format($myrow['ttot'],2));
		} else {
			echo "<FORM ACTION=".$_SERVER['PHP_SELF']. " METHOD=POST>";

			printf("<td><input type='checkbox' name='remisiones[]' value='%s' CHECKED></input></td>
			<td><A target='_blank' HREF='%s'>%s</A></td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			</tr>",
			$myrow['transno'],
			$ViewPage,
			$myrow['transno'],
			$myrow['debtorno'],
			$myrow['brname'],
			$myrow['address1'],
			$myrow['Fecha'],
			"$ ".$FormatedOrderValue);
		}

		$j++;
		If ($j == 12){
			$j=1;
			echo $tableheader;
		}
//end of page full new headings if
	}
//end of while loop

	echo '</TABLE>';
        //Jaime, agregado evento onSubmit para verificar que se haya elegido una serie (en caso de poder escoger entre mas de 1)
	echo "<input type='submit' name ='facturar' value='Crear Factura' onSubmit=\"return isSerieSelected()\"></input></form>";
        //Termina Jaime, agregado evento onSubmit para verificar que se haya elegido una serie (en caso de poder escoger entre mas de 1)
	// para seleccionar todas las remisiones
	?>
	<FORM ACTION="<?php echo $_SERVER['PHP_SELF'] . '?' . SID; ?>" METHOD=POST>
	<input type="submit" name='todas' value="Seleccionar Todas"></input>
	<input type="hidden" name='Select' value="<?echo $_POST['Select']?>"></input>
	</FORM><BR><BR>
	<?
}

// CREAR LA FACTURA
if(isset($_POST['facturar'])||(isset($_POST['Update']))||isset($_POST['ProcessInvoice'])){
	//if(isset($_POST['facturar'])||(isset($_POST['Update']))||isset($_POST['ProcessInvoice'])){

	$remisiones = $_POST['remisiones'];
	$NoRem = count($remisiones);
	$ii = 0;

	$TOTTAX = 0;
	$TOTFINAL = 0;
	$TOTFREIGHT = 0;
	// GENERAR UNA BUSQUEDA PARA LOS PARAMETROS GENERALES

	// FIN DE BUSQUEDA DE PARAMETROS GENERALES

	// HACER LAS BUSQUEDAS DE CADA REMISION PARA OBTENER EL TOTAL, LOS TAXES, FREIGHT COST ETC...
	// Y ASI PODER CALCULAR LOS TOTALES PARA EL INSERT

	$prheader=1;
	while($ii < $NoRem){

		// bowikaxu2 - verificar que ninguna remision ya haya sido facturada
		$sql = "SELECT Shipment FROM rh_invoiceshipment WHERE Shipment='".$remisiones[$ii]."' AND Facturado = 1";
		$res = DB_query($sql,$db,'Imposible obtener numero de factura','Fallo: ');
	if(DB_num_rows($res)>0){

		prnMsg('La remision: '.$NoRem.' ya ha sido facturada !!','error');
		unset($_SESSION[$remisiones[$ii]]->LineItems);
		unset($_SESSION[$remisiones[$ii]]);
		unset($_SESSION['ProcessingOrder']);
		unset($_SESSION['Invoice']);
		include('includes/footer.inc'); exit;

	}

		// BUSCAR EL NUMERO DE ORDEN ACTUAL
		$Busqueda = "SELECT debtortrans.transno,
					debtortrans.debtorno,
					DATE(debtortrans.trandate) as fecha,
					YEAR(debtortrans.trandate) as year,
					debtortrans.branchcode,
					debtortrans.type,
					debtortrans.order_,
					rh_invoiceshipment.Invoice,
					rh_invoiceshipment.Shipment,
					salesorders.ordertype,
					salesorders.orderno,
					salesorders.shipvia,
					currencies.rate
					FROM rh_invoiceshipment, debtortrans, salesorders, currencies
					WHERE debtortrans.transno = rh_invoiceshipment.Shipment

					AND rh_invoiceshipment.Shipment = ".$remisiones[$ii]."
					AND debtortrans.type = 20000
					GROUP BY rh_invoiceshipment.Shipment";
		// AND rh_invoiceshipment.Invoice = debtortrans.order_
	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('IMPOSIBLE OBTENER DATOS DE LA REMISION');
	$DbgMsg = _('NO SE PUDIERON OBTENER DATOS DE LA REMISION ACTUAL');
 	$Result = DB_query($Busqueda,$db,$ErrMsg,$DbgMsg,true);

 	$myrow = DB_fetch_array($Result);

 	$Invoice = $myrow['order_'];
 	$RemNo = $myrow['Shipment'];
 	
 	//iJPe		realhost	2010-01-08
 	if (!isset($rh_fechaRemision)){
 		$rh_fechaRemision = $myrow['year'];	
	}else{
		if ($myrow['year'] < $rh_fechaRemision)
		{
			$rh_fechaRemision = $myrow['year'];		
		}
	}
	
	//iJPe		realhost	2010-01-08
 	if (!isset($rh_fechaCompRemision)){
 		$rh_fechaCompRemision = $myrow['fecha'];	 		
	}else{
		if ($myrow['fecha'] < $rh_fechaCompRemision)
		{
			$rh_fechaCompRemision = $myrow['fecha'];					
		}
	}	

	// COMIENZA CODIGO QUE PONE INFO DE LA REMISION

if($Invoice > 0){

	if(!isset($_POST['Update'])){

	unset($_SESSION[$remisiones[$ii]]->LineItems);
	unset ($_SESSION[$remisiones[$ii]]);
	Session_register($remisiones[$ii]);
	Session_register('ProcessingOrder');
	Session_register('Old_FreightCost');
	Session_Register('CurrencyRate');

	}

	$_SESSION['ProcessingOrder']=$Invoice;
	$_SESSION[$remisiones[$ii]] = new cart;

	$_SESSION['Invoice'] = new cart;

/*read in all the guff from the selected order into the Items cart  */

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
					salesorders.contactphone,
					salesorders.contactemail,
					salesorders.freightcost,
					salesorders.deliverydate,
					debtorsmaster.currcode,
					salesorders.fromstkloc,
                                       salesorders.fromstkloc_virtual,
					rh_locations.taxprovinceid,
					custbranch.taxgroupid,
					currencies.rate as currency_rate,
					custbranch.defaultshipvia,
                    custbranch.metodopago,
                    custbranch.cuentapago
			FROM salesorders,
				debtorsmaster,
				custbranch,
				currencies,
				rh_locations
			WHERE salesorders.debtorno = debtorsmaster.debtorno
			AND salesorders.branchcode = custbranch.branchcode
			AND salesorders.debtorno = custbranch.debtorno
			AND rh_locations.loccode=salesorders.fromstkloc
			AND debtorsmaster.currcode = currencies.currabrev
			AND salesorders.orderno = ' . $_SESSION['ProcessingOrder']."";

	$ErrMsg = _('The order cannot be retrieved because');
	$DbgMsg = _('The SQL to get the order header was');
	$GetOrdHdrResult = DB_query($OrderHeaderSQL,$db,$ErrMsg,$DbgMsg);

	if(isset($_SESSION['NewCustRem']) && isset($_SESSION['NewBranchRem'])){
		$sql_newcust = "SELECT taxgroupid
		braddress1,
		braddress2,
		braddress3,
		braddress4,
		braddress5,
		braddress6,
		brname,
		phoneno,
		email
		FROM custbranch WHERE debtorno = '".$_SESSION['NewCustRem']."' AND branchcode = '".$_SESSION['NewBranchRem']."'";
		$ErrMsg = _('Imposible to get the new client tax group id');
		$DbgMsg = _('The SQL to get it was');
		$ResultNew = DB_query($sql_newcust,$db,$ErrMsg,$DbgMsg);
		$res_new = DB_fetch_array($ResultNew);
	}

	if (DB_num_rows($GetOrdHdrResult)==1) {

		$myrow = DB_fetch_array($GetOrdHdrResult);

		$_SESSION['Invoice']->DebtorNo = $myrow['debtorno'];
		$_SESSION['Invoice']->OrderNo = $myrow['orderno'];
		$_SESSION['Invoice']->Branch = $myrow['branchcode'];
		$_SESSION['Invoice']->CustomerName = $myrow['name'];
		$_SESSION['Invoice']->CustRef = $myrow['customerref'];
		$_SESSION['Invoice']->Comments = $myrow['comments'];
		$_SESSION['Invoice']->DefaultSalesType =$myrow['ordertype'];
		$_SESSION['Invoice']->DefaultCurrency = $myrow['currcode'];
		$BestShipper = $myrow['shipvia'];
		$_SESSION['Invoice']->ShipVia = $myrow['shipvia'];
		if (is_null($BestShipper)){
		   $BestShipper=0;
		}
		$_SESSION['Invoice']->DeliverTo = $myrow['deliverto'];
		$_SESSION['Invoice']->DeliveryDate = ConvertSQLDate($myrow['deliverydate']);
		if(isset($_SESSION['NewCustRem']) && isset($_SESSION['NewBranchRem']) && $result_new){
			$_SESSION['Invoice']->BrAdd1 = $res_new['braddress1'];
			$_SESSION['Invoice']->BrAdd2 = $res_new['braddress2'];
			$_SESSION['Invoice']->BrAdd3 = $res_new['braddress3'];
			$_SESSION['Invoice']->BrAdd4 = $res_new['braddress4'];
			$_SESSION['Invoice']->BrAdd5 = $res_new['braddress5'];
			$_SESSION['Invoice']->BrAdd6 = $res_new['braddress6'];
			$_SESSION['Invoice']->PhoneNo = $res_new['phoneno'];
			$_SESSION['Invoice']->Email = $res_new['email'];
			$_SESSION['Invoice']->TaxGroup = $res_new['taxgroupid'];
			echo $res_new['taxgroupid']." :::<br>";
		}else {
			$_SESSION['Invoice']->BrAdd1 = $myrow['deladd1'];
			$_SESSION['Invoice']->BrAdd2 = $myrow['deladd2'];
			$_SESSION['Invoice']->BrAdd3 = $myrow['deladd3'];
			$_SESSION['Invoice']->BrAdd4 = $myrow['deladd4'];
			$_SESSION['Invoice']->BrAdd5 = $myrow['deladd5'];
			$_SESSION['Invoice']->BrAdd6 = $myrow['deladd6'];
			$_SESSION['Invoice']->PhoneNo = $myrow['contactphone'];
			$_SESSION['Invoice']->Email = $myrow['contactemail'];
			$_SESSION['Invoice']->TaxGroup = $myrow['taxgroupid'];
		}

                //iJPe
                //Sucursal a la cual se haran los movimientos, siempre sera una sucursal maestra
		$_SESSION['Invoice']->LocationMoves = $myrow['fromstkloc'];
                //Sucursal a la cual se haran las transacciones
                $_SESSION['Invoice']->Location = $myrow['fromstkloc_virtual'];
		
		$_SESSION['Invoice']->FreightCost = $myrow['freightcost'];
		$_SESSION['Old_FreightCost'] = $myrow['freightcost'];
		$_POST['ChargeFreightCost'] = $_SESSION['Old_FreightCost'];
		$_SESSION['Invoice']->Orig_OrderDate = $myrow['orddate'];
		$_SESSION['CurrencyRate'] = $myrow['currency_rate'];
        $_SESSION['metodoPago']=(strlen($myrow['metodopago'])>0?$myrow['metodopago']:'No Identificado');
        $_SESSION['cuentaPago']=(strlen($myrow['cuentapago'])>0?$myrow['cuentapago']:'No Identificado');

		$_SESSION['Invoice']->DispatchTaxProvince = $myrow['taxprovinceid'];
		$_SESSION['Invoice']->GetFreightTaxes();

		// bowikaxu2 - poner los datos en cada una de las remisiones tambien

		$_SESSION[$remisiones[$ii]]->DebtorNo = $myrow['debtorno'];
		$_SESSION[$remisiones[$ii]]->OrderNo = $myrow['orderno'];
		$_SESSION[$remisiones[$ii]]->Branch = $myrow['branchcode'];
		$_SESSION[$remisiones[$ii]]->CustomerName = $myrow['name'];
		$_SESSION[$remisiones[$ii]]->CustRef = $myrow['customerref'];
		$_SESSION[$remisiones[$ii]]->Comments = $myrow['comments'];
		$_SESSION[$remisiones[$ii]]->DefaultSalesType =$myrow['ordertype'];
		$_SESSION[$remisiones[$ii]]->DefaultCurrency = $myrow['currcode'];
		$BestShipper = $myrow['shipvia'];
		$_SESSION[$remisiones[$ii]]->ShipVia = $myrow['shipvia'];
		if (is_null($BestShipper)){
		   $BestShipper=0;
		}
		$_SESSION[$remisiones[$ii]]->DeliverTo = $myrow['deliverto'];
		$_SESSION[$remisiones[$ii]]->DeliveryDate = ConvertSQLDate($myrow['deliverydate']);
		$_SESSION[$remisiones[$ii]]->BrAdd1 = $myrow['deladd1'];
		$_SESSION[$remisiones[$ii]]->BrAdd2 = $myrow['deladd2'];
		$_SESSION[$remisiones[$ii]]->BrAdd3 = $myrow['deladd3'];
		$_SESSION[$remisiones[$ii]]->BrAdd4 = $myrow['deladd4'];
		$_SESSION[$remisiones[$ii]]->BrAdd5 = $myrow['deladd5'];
		$_SESSION[$remisiones[$ii]]->BrAdd6 = $myrow['deladd6'];
		$_SESSION[$remisiones[$ii]]->PhoneNo = $myrow['contactphone'];
		$_SESSION[$remisiones[$ii]]->Email = $myrow['contactemail'];
		$_SESSION[$remisiones[$ii]]->Location = $myrow['fromstkloc'];
		$_SESSION[$remisiones[$ii]]->FreightCost = $myrow['freightcost'];
		$_SESSION['Old_FreightCost'] = $myrow['freightcost'];
		$_POST['ChargeFreightCost'] = $_SESSION['Old_FreightCost'];
		$_SESSION[$remisiones[$ii]]->Orig_OrderDate = $myrow['orddate'];
		$_SESSION['CurrencyRate'] = $myrow['currency_rate'];
		$_SESSION[$remisiones[$ii]]->TaxGroup = $myrow['taxgroupid'];
		$_SESSION[$remisiones[$ii]]->DispatchTaxProvince = $myrow['taxprovinceid'];
		$_SESSION[$remisiones[$ii]]->GetFreightTaxes();

		// bowikaxu2 - fin de poner detalles en la remision

		DB_free_result($GetOrdHdrResult);
	if($prheader==1){
		$sql = "SELECT rh_serie, extinvoices FROM rh_locations WHERE loccode = '".$_SESSION['Invoice']->Location."'";
		$res = mysql_query($sql);
		$info = mysql_fetch_array($res);

		if(isset($_SESSION['NewCustRem'])){
			echo "<BR><CENTER><FONT SIZE=4 COLOR=red>"._('Location').': <STRONG>'.$_SESSION['Invoice']->Location.' - #'.$info['rh_serie'].$info['extinvoices']."</FONT><FONT SIZE=2 COLOR=black>(numero con fin informativo)</STRONG></FONT><CENTER><BR>";
			echo '<CENTER><FONT SIZE=2><B><U>' . $_SESSION['Invoice']->CustomerName . '</U></B></FONT><FONT SIZE=3> - ' .
			_('Shipment amounts stated in') . ' ' . $_SESSION['Invoice']->DefaultCurrency . '</CENTER>';
			echo "<CENTER><FONT SIZE=4><B>Cliente Final: ".$_SESSION['NewCustRem'].' - '.$_SESSION['NewBranchRem']."</CENTER></FONT SIZE=4></B>";

		}else {
			echo "<BR><CENTER><FONT SIZE=4 COLOR=red>"._('Location').': <STRONG>'.$_SESSION['Invoice']->Location.' - #'.$info['rh_serie'].$info['extinvoices']."</FONT><FONT SIZE=2 COLOR=black>(numero con fin informativo)</STRONG></FONT><CENTER><BR>";
			echo '<CENTER><FONT SIZE=4><B><U>' . $_SESSION['Invoice']->CustomerName . '</U></B></FONT><FONT SIZE=3> - ' .
			_('Shipment amounts stated in') . ' ' . $_SESSION['Invoice']->DefaultCurrency . '</CENTER>';
		}

		$prheader=2;
		echo "| ";
	}
	echo $remisiones[$ii]. " | ";

/*now populate the line items array with the sales order details records */
// bowikaxu - opcion de agrupar o no agrupar cambiar el query a sin el group by ...
/*
 * Juan Mtz 0.o
 * realhost
 * 31-Agosto-2009
 *
 * Se modifico el query para que al observar la remision se observara en el orden en
 * que los productos fueron solicitados en la orden de compra
 */
if($_SESSION['GroupItems']==1){
                //se documento el siguiente sql porque daba como resultado mal los datos al momento de ejecutar la consulta
                //12 de septiembre del 2009
                /*
		$LineItemsSQL = 'SELECT stkcode,
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
					SUM(salesorderdetails.unitprice) AS unitprice,
					SUM(salesorderdetails.quantity) AS quantity,
					SUM(salesorderdetails.discountpercent) AS discountpercent,
					salesorderdetails.actualdispatchdate,
					SUM(salesorderdetails.qtyinvoiced) AS qtyinvoiced,
					salesorderdetails.narrative,
					salesorderdetails.orderlineno,
					SUM(rh_remdetails.qty) AS qty2,
					SUM(rh_remdetails.price*rh_remdetails.qty) AS price2,
					stockmaster.materialcost +
						stockmaster.labourcost +
						stockmaster.overheadcost AS standardcost
				FROM salesorderdetails INNER JOIN stockmaster
				 	ON salesorderdetails.stkcode = stockmaster.stockid
				 	INNER JOIN rh_remdetails ON rh_remdetails.stockid = stockmaster.stockid
				 	INNER JOIN debtortrans ON debtortrans.transno = rh_remdetails.transno
				WHERE debtortrans.type=20000
				AND debtortrans.order_ = salesorderdetails.orderno
				AND rh_remdetails.transno IN (';
				$k=0;
				foreach($remisiones as $rm){
					if($k > 0)$LineItemsSQL .= ', ';
					$LineItemsSQL .= $rm;
					$k++;
				}
				$LineItemsSQL .= ') GROUP BY rh_remdetails.stockid
				ORDER BY  rh_remdetails.transno, salesorderdetails.orderlineno';
                 *
                 */

		$LineItemsSQL = 'SELECT stkcode,
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
					(salesorderdetails.unitprice) AS unitprice,
					(salesorderdetails.quantity) AS quantity,
					(rh_remdetails.discountpercent) AS discountpercent,
					salesorderdetails.actualdispatchdate,
					(salesorderdetails.qtyinvoiced) AS qtyinvoiced,
					salesorderdetails.narrative,
					salesorderdetails.orderlineno,
					(rh_remdetails.qty) AS qty2,
					(rh_remdetails.price*rh_remdetails.qty) AS price2,
					(stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost) AS standardcost
				FROM salesorderdetails INNER JOIN stockmaster
				 	ON salesorderdetails.stkcode = stockmaster.stockid
				 	INNER JOIN rh_remdetails ON rh_remdetails.stockid = stockmaster.stockid
				 	INNER JOIN debtortrans ON debtortrans.transno = rh_remdetails.transno
				WHERE debtortrans.type=20000
				AND debtortrans.order_ = salesorderdetails.orderno
                                AND salesorderdetails.orderlineno = rh_remdetails.line
				AND rh_remdetails.transno IN (';
				$k=0;
				foreach($remisiones as $rm){
					if($k > 0)$LineItemsSQL .= ', ';
					$LineItemsSQL .= $rm;
					$k++;
				}
				$LineItemsSQL .= ')
				ORDER BY  rh_remdetails.transno, salesorderdetails.orderlineno';
}else {
	$LineItemsSQL = 'SELECT stkcode,
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
					(salesorderdetails.unitprice) AS unitprice,
					(salesorderdetails.quantity) AS quantity,
					(salesorderdetails.discountpercent) AS discountpercent,
					salesorderdetails.actualdispatchdate,
					(salesorderdetails.qtyinvoiced) AS qtyinvoiced,
					salesorderdetails.narrative,
					salesorderdetails.orderlineno,
					rh_remdetails.qty AS qty2,
					rh_remdetails.price AS price2,
					stockmaster.materialcost +
						stockmaster.labourcost +
						stockmaster.overheadcost AS standardcost
				FROM salesorderdetails INNER JOIN stockmaster
				 	ON salesorderdetails.stkcode = stockmaster.stockid
				 	INNER JOIN rh_remdetails ON rh_remdetails.stockid = stockmaster.stockid
				 	INNER JOIN debtortrans ON debtortrans.transno = rh_remdetails.transno
				WHERE debtortrans.type=20000
				AND debtortrans.order_ = salesorderdetails.orderno
				AND rh_remdetails.transno IN (';
				$k=0;
				foreach($remisiones as $rm){
					if($k > 0)$LineItemsSQL .= ', ';
					$LineItemsSQL .= $rm;
					$k++;
				}
				$LineItemsSQL .= ') ORDER BY  rh_remdetails.transno, salesorderdetails.orderlineno';
}
				// AND salesorderdetails.quantity - salesorderdetails.qtyinvoiced >0
				//salesorderdetails.qtyinvoiced,
		$ErrMsg = _('The line items of the order cannot be retrieved because ');
		$DbgMsg = _('The SQL that failed was');
		$LineItemsResult = DB_query($LineItemsSQL,$db,$ErrMsg,$DbgMsg);

		// OBTENER LA CANTIDAD CORRECTA DE ESTA FACTURA

		//	DB_free_result($CantRes);
		// TERMINA DE OBTENER LA CANTIDAD CORRECTA
		$k=0;
		if (db_num_rows($LineItemsResult)>0) {
			$sql = "SELECT rh_maxitminv FROM rh_locations WHERE loccode ='".$_SESSION['Invoice']->Location."'";
			$itmmaxres = DB_query($sql,$db);
			$ItmMax = DB_fetch_array($itmmaxres);
			while ($myrow=db_fetch_array($LineItemsResult)) {

				if(/*count($_SESSION['Invoice']->LineItems) >= $ItmMax['rh_maxitminv']*/false){
					echo "<H2>Imposible agregar el articulo ".$myrow['stkcode']." se llego al limite de articulos</H2>".$ItmMax['rh_maxitminv']."";
				}else {
				// verificar si ya existe el producto en el carrito
				// si lo hay obtener costo, sumarlos y sacar un intermedio
				// Get_StockID_List() verificar que el objeto no este ahi, se puede usar en el SQL
				if($_SESSION['GroupItems']==1){
					if(($myrow['price2']/$myrow['qty2'])<=0){
						echo "<CENTER><H2><FONT COLOR=RED>ERROR: EL ARTICULO ".$myrow['stkcode']." TIENE PRECIO IGUAL O MENOR A CERO,<BR>
						VERIFIQUE QUE EL ARTICULO SEA MUESTRA, DE LO CONTRARIO<BR>
						MODIFIQUE EL PEDIDO Y VUELVA A INTENTAR FACTURAR</FONT></H2></CENTER>";
						//include('includes/footer.inc');
						//exit;
					}//else {

					$_SESSION['Invoice']->add_to_cart($myrow['stkcode'],
						$myrow['qty2'],	// quantity
						$myrow['qty2'],
						$myrow['description'],
						($myrow['price2']/$myrow['qty2']),
						$myrow['discountpercent'],
						$myrow['units'],	// units
						$myrow['volume'],
						$myrow['kgs'],
						0,
						$myrow['mbflag'],
						$myrow['actualdispatchdate'],
						$myrow['qtyinvoiced'],	// qtyinvoiced
						$myrow['discountcategory'],
						$myrow['controlled'],
						$myrow['serialised'],
						$myrow['decimalplaces'],
						$myrow['narrative'],
						'No',
						$k, // $myrow['orderlineno']
						$myrow['taxcatid']);	/*NB NO Updates to DB */
					//}
				}else {
					if(($myrow['price2'])<=0){
						echo "<CENTER><H2>ERROR: EL ARTICULO ".$myrow['stkcode']." TIENE COSTO IGUAL O MENOR A CERO,<BR>
						MODIFIQUE EL PEDIDO Y VUELVA A REMISIONAR</H2></CENTER>";
						include('includes/footer.inc');
						exit;
					}else {
					$_SESSION['Invoice']->add_to_cart($myrow['stkcode'],
						$myrow['qty2'],	// quantity
						$myrow['qty2'],
						$myrow['description'],
						($myrow['price2']),
						$myrow['discountpercent'],
						$myrow['units'],	// units
						$myrow['volume'],
						$myrow['kgs'],
						0,
						$myrow['mbflag'],
						$myrow['actualdispatchdate'],
						$myrow['qtyinvoiced'],	// qtyinvoiced
						$myrow['discountcategory'],
						$myrow['controlled'],
						$myrow['serialised'],
						$myrow['decimalplaces'],
						$myrow['narrative'],
						'No',
						$k, // $myrow['orderlineno']
						$myrow['taxcatid']);	/*NB NO Updates to DB */
					}
				}
				$_SESSION['Invoice']->LineItems[$k]->StandardCost = $myrow['standardcost'];
				//$_SESSION[$remisiones[$ii]]->LineItems[$myrow['orderlineno']]->QtyDispatched = $_SESSION[$remisiones[$ii]]->LineItems[$myrow['orderlineno']]->QtyInv;
				/*Calculate the taxes applicable to this line item from the customer branch Tax Group and Item Tax Category */

				//$_SESSION['Invoice']->LineItems[$k]->QtyDispatched = $myrow['qty2'];
				
				$_SESSION['Invoice']->GetTaxes($k);
				$k++;
				
				foreach ($_SESSION['Invoice']->LineItems as $Itm) {
					foreach ($Itm->Taxes as $TaxLine) {
						if ($rh_fechaRemision<=2009){
							$_SESSION['Invoice']->LineItems[$Itm->LineNumber]->Taxes[$TaxLine->TaxCalculationOrder]->TaxRate = .15;
						}										
					}				
				}

			}
			} /* line items from sales order details */
		} else { /* there are no line items that have a quantity to deliver */
			echo '<CENTER><A HREF="'. $rootpath. '/rh_SelectSalesOrder_Shipment.php?' . SID . '">' ._('Select a different sales order to shipment') .'</A></CENTER>';
			echo '<P>';
			prnMsg( _('There are no ordered items with a quantity left to deliver. There is nothing left to ship'));
			include('includes/footer.inc');
			exit;

		} //end of checks on returned data set
		DB_free_result($LineItemsResult);

	} else { /*end if the order was returned sucessfully */

		echo '<P>'.
		prnMsg( _('This shipment item could not be retrieved. Please select another shipment'), 'warn');
		echo '<CENTER><A HREF="'. $rootpath . '/rh_ViewRemisiones.php?' . SID . '">'. _('Select a different remision to invoice'). '</A></CENTER>';
		include ('includes/footer.inc');
		exit;
	} //valid order returned from the entered order number
	}
	$ii++;
} // bowikaxu2 - cerrar la busqueda de remisiones y ya se tiene el carrito Invoice

if (isset($_POST['Update'])||isset($_POST['ProcessInvoice'])) {
/* if processing, a dispatch page has been called and ${$StkItm->LineNumber} would have been set from the post
set all the necessary session variables changed by the POST  */
if (isset($_POST['ShipVia'])){

		$_SESSION['Invoice']->ShipVia = $_POST['ShipVia'];
	}
	if (isset($_POST['ChargeFreightCost'])){
		$_SESSION['Invoice']->FreightCost = $_POST['ChargeFreightCost'];
	}
	foreach ($_SESSION['Invoice']->FreightTaxes as $FreightTaxLine) {
		if (isset($_POST['FreightTaxRate'  . $FreightTaxLine->TaxCalculationOrder])){
			$_SESSION['Invoice']->FreightTaxes[$FreightTaxLine->TaxCalculationOrder]->TaxRate = $_POST['FreightTaxRate'  . $FreightTaxLine->TaxCalculationOrder]/100;
		}
	}

	foreach ($_SESSION['Invoice']->LineItems as $Itm) {
		if (is_numeric($_POST[$Itm->LineNumber .  '_QtyDispatched' ])AND $_POST[$Itm->LineNumber .  '_QtyDispatched'] <= ($_SESSION['Invoice']->LineItems[$Itm->LineNumber]->Quantity - $_SESSION['Invoice']->LineItems[$Itm->LineNumber]->QtyInv)){
			$_SESSION['Invoice']->LineItems[$Itm->LineNumber]->QtyDispatched = $_POST[$Itm->LineNumber  . '_QtyDispatched'];
		}

		/*
		 * iJPe
		 * realhost
		 * 2010-01-05
		 * 
		 * Modificacion realizada a error en codigo, el codigo que se tenia antes se dejo comentado, el problema era que se establecia un
		 * prefijo en los POST que nunca se le añadia a los campos
		 */
			/*foreach ($Itm->Taxes as $TaxLine) {
				if (isset($_POST['Invoice'.$Itm->LineNumber  . $TaxLine->TaxCalculationOrder . '_TaxRate'])){
					$_SESSION['Invoice']->LineItems[$Itm->LineNumber]->Taxes[$TaxLine->TaxCalculationOrder]->TaxRate = $_POST['Invoice'.$Itm->LineNumber  . $TaxLine->TaxCalculationOrder . '_TaxRate']/100;
				}
			}*/
		
		foreach ($Itm->Taxes as $TaxLine) {
			if (isset($_POST[$Itm->LineNumber  . $TaxLine->TaxCalculationOrder . '_TaxRate'])){
				$_SESSION['Invoice']->LineItems[$Itm->LineNumber]->Taxes[$TaxLine->TaxCalculationOrder]->TaxRate = $_POST[$Itm->LineNumber  . $TaxLine->TaxCalculationOrder . '_TaxRate']/100;
			}				
		}
	}


}

/* Always display dispatch quantities and recalc freight for items being dispatched */
/***************************************************************
	Line Item Display
***************************************************************/
echo '<FORM ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';
//echo "<input type='checkbox' name='remisiones[]' value='".$remisiones[$ii]."' CHECKED>"._('Uncheck para borrar')."</input>";
echo "<input type=hidden name='".$remisiones[$ii].$TaxTotals."[]' value='".$remisiones[$ii].$TaxTotals."'></input>";

echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7 BORDER=0>
	<TR>
		<TD class="tableheader">' . _('Item Code') . '</TD>
		<TD class="tableheader">' . _('Item Description' ) . '</TD>
		<TD class="tableheader">' . _('Ordered') . '</TD>
		<TD class="tableheader">' . _('Units') . '</TD>
		<TD class="tableheader">' . _('Already') . '<BR>' . _('Sent') . '</TD>
		<TD class="tableheader">' . _('This Dispatch') . '</TD>
		<TD class="tableheader">' . _('Price') . '</TD>
		<TD class="tableheader">' . _('Discount') . '</TD>
		<TD class="tableheader">' . _('Total') . '<BR>' . _('Excl Tax') . '</TD>
		<TD class="tableheader">' . _('Tax Authority') . '</TD>
		<TD class="tableheader">' . _('Tax %') . '</TD>
		<TD class="tableheader">' . _('Tax') . '<BR>' . _('Amount') . '</TD>
		<TD class="tableheader">' . _('Total') . '<BR>' . _('Incl Tax') . '</TD>
	</TR>';

$_SESSION['Invoice']->total = 0;
$_SESSION['Invoice']->totalVolume = 0;
$_SESSION['Invoice']->totalWeight = 0;
$TaxTotals = array();
$TaxGLCodes = array();
$TaxTotal =0;

/*show the line items on the order with the quantity being dispatched available for modification */

$k=0; //row colour counter

foreach ($_SESSION['Invoice']->LineItems as $LnItm) {

	if ($k==1){
		$RowStarter = '<tr bgcolor="#CCCCCC">';
		$k=0;
	} else {
		$RowStarter = '<tr bgcolor="#EEEEEE">';
		$k=1;
	}
	//$LnItm->QtyDispatched = $Cantidad;

	echo $RowStarter;

	$LineTotal = $LnItm->QtyDispatched * $LnItm->Price * (1 - $LnItm->DiscountPercent);

	//$_SESSION[$remisiones[$ii]]->LineItems[$LnItm->LineNumber]->QtyDispatched = $Cantidad;
	$_SESSION['Invoice']->total += $LineTotal;
	$_SESSION['Invoice']->totalVolume += ($LnItm->QtyDispatched * $LnItm->Volume);
	$_SESSION['Invoice']->totalWeight += ($LnItm->QtyDispatched * $LnItm->Weight);

	echo '<TD>'.$LnItm->StockID.'</TD>
		<TD>'.$LnItm->ItemDescription.'</TD>
		<TD ALIGN=RIGHT>' . number_format($LnItm->Quantity,$LnItm->DecimalPlaces) . '</TD>
		<TD>'.$LnItm->Units.'</TD>
		<TD ALIGN=RIGHT>' . number_format($LnItm->QtyInv,$LnItm->DecimalPlaces) . '</TD>';
		//<TD ALIGN=RIGHT>' . number_format($LnItm->Quantity-$LnItm->QtyInv,$LnItm->DecimalPlaces) . '</TD>';

	if ($LnItm->Controlled==1){

		//echo '<input type=hidden name="' . $LnItm->LineNumber . '_QtyDispatched"  value="' . $Cantidad . '">';
		//echo '<TD ALIGN=RIGHT>' . number_format($LnItm->QtyDispatched,$LnItm->DecimalPlaces) . '</TD>';

		//echo '<TD ALIGN=RIGHT>' . number_format($LnItm->QtyDispatched,$LnItm->DecimalPlaces) . '</TD>';
		echo '<TD ALIGN=RIGHT>' . number_format($LnItm->QtyDispatched,$LnItm->DecimalPlaces) . '</TD>';
	} else {

		//echo '<input type=hidden name="' . $LnItm->LineNumber .'_QtyDispatched" maxlength=5 SIZE=6 value="' . $Cantidad . '">';
		//echo '<TD ALIGN=RIGHT>' . number_format($LnItm->QtyDispatched,$LnItm->DecimalPlaces) . '</TD>';

		//echo '<TD ALIGN=RIGHT>' . number_format($LnItm->QtyDispatched,$LnItm->DecimalPlaces) . '</TD>';
		echo '<TD ALIGN=RIGHT>' . number_format($LnItm->QtyDispatched,$LnItm->DecimalPlaces) . '</TD>';

	}
	$DisplayDiscountPercent = number_format($LnItm->DiscountPercent*100,2) . '%';
	$DisplayLineNetTotal = number_format($LineTotal,2);
	$DisplayPrice = number_format($LnItm->Price,2);
	echo '<TD ALIGN=RIGHT>'.$DisplayPrice.'</TD>
		<TD ALIGN=RIGHT>'.$DisplayDiscountPercent.'</TD>
		<TD ALIGN=RIGHT>'.$DisplayLineNetTotal.'</TD>';

	/*Need to list the taxes applicable to this line */
	echo '<TD>';
	$i=0;
	foreach ($_SESSION['Invoice']->LineItems[$LnItm->LineNumber]->Taxes AS $Tax) {
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

	foreach ($LnItm->Taxes AS $Tax) {
		if ($i>0){
			echo '<BR>';
		}


			echo 		'<input type=text name="' .$LnItm->LineNumber . $Tax->TaxCalculationOrder . '_TaxRate" maxlength=4 SIZE=4 value="' . $Tax->TaxRate*100 . '">'; //$Tax->TaxRate*100

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

	echo '<TD ALIGN=RIGHT>'.$DisplayTaxAmount.'</TD><TD ALIGN=RIGHT>'.$DisplayGrossLineTotal.'</TD>';

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

if ($_SESSION['Invoice']->AnyAlreadyDelivered==1) {
	$_POST['ChargeFreightCost'] = 0;
} elseif(!isset($_SESSION['Invoice']->FreightCost)) {
	if ($_SESSION['DoFreightCalc']==True){
		list ($FreightCost, $BestShipper) = CalcFreightCost($_SESSION['Invoice']->total,
								$_SESSION['Invoice']->BrAdd2,
								$_SESSION['Invoice']->BrAdd3,
								$_SESSION['Invoice']->totalVolume,
								$_SESSION['Invoice']->totalWeight,
								$_SESSION['Invoice']->Location,
								$db);
		$_SESSION['Invoice']->ShipVia = $BestShipper;
	}
  	if (is_numeric($FreightCost)){
		$FreightCost = $FreightCost / $_SESSION['CurrencyRate'];
  	} else {
		$FreightCost =0;
  	}
  	if (!is_numeric($BestShipper)){
  		$SQL =  'SELECT shipper_id FROM shippers WHERE shipper_id=' . $_SESSION['Default_Shipper'];
		$ErrMsg = _('There was a problem testing for a the default shipper because');
		$TestShipperExists = DB_query($SQL,$db, $ErrMsg);
		if (DB_num_rows($TestShipperExists)==1){
			$BestShipper = $_SESSION['Default_Shipper'];
		} else {
			$SQL =  'SELECT shipper_id FROM shippers';
			$ErrMsg = _('There was a problem testing for a the default shipper');
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

echo '<TR>
	<TD COLSPAN=2 ALIGN=RIGHT>' . _('Order Freight Cost'). '</TD>
	<TD ALIGN=RIGHT>' . $_SESSION['Old_FreightCost'] . '</TD>';

if ($_SESSION['DoFreightCalc']==True){
	echo '<TD COLSPAN=2 ALIGN=RIGHT>' ._('Recalculated Freight Cost'). '</TD>
		<TD ALIGN=RIGHT>' . $FreightCost . '</TD>';
} else {
	echo '<TD COLSPAN=3></TD>';
}

// PRUEBA DE FREIGHT COST
if(isset($_POST['Update'])){

	$_SESSION['Invoice']->FreightCost = $_POST['ChargeFreightCost'];
}

echo '<TD COLSPAN=2 ALIGN=RIGHT>'. _('Charge Freight Cost').'</TD>
	<TD><INPUT TYPE=TEXT SIZE=10 MAXLENGTH=12 NAME=ChargeFreightCost VALUE=' . $_POST['ChargeFreightCost'] . '></TD>';
//	echo '<input type=hidden name= "'.$remisiones[$ii]."_FCost".'" value='.$_SESSION[$remisiones[$ii]]->FreightCost.' >';

$FreightTaxTotal =0; //initialise tax total

echo '<TD>';

$i=0; // initialise the number of taxes iterated through
foreach ($_SESSION['Invoice']->FreightTaxes as $FreightTaxLine) {
	if ($i>0){
		echo '<BR>';
	}
	echo  $FreightTaxLine->TaxAuthDescription;
	$i++;
}

echo '</TD><TD>';

$i=0;
foreach ($_SESSION['Invoice']->FreightTaxes as $FreightTaxLine) {
	if ($i>0){
		echo '<BR>';
	}

	echo  '<INPUT TYPE=TEXT NAME=FreightTaxRate' . $FreightTaxLine->TaxCalculationOrder . ' MAXLENGTH=4 SIZE=4 VALUE=' . $FreightTaxLine->TaxRate * 100 . '>';

	if ($FreightTaxLine->TaxOnTax ==1){
		$TaxTotals[$FreightTaxLine->TaxAuthID] += ($FreightTaxLine->TaxRate * ($_SESSION['Invoice']->FreightCost + $FreightTaxTotal));
		$FreightTaxTotal += ($FreightTaxLine->TaxRate * ($_SESSION['Invoice']->FreightCost + $FreightTaxTotal));
	} else {
		$TaxTotals[$FreightTaxLine->TaxAuthID] += ($FreightTaxLine->TaxRate * $_SESSION['Invoice']->FreightCost);
		$FreightTaxTotal += ($FreightTaxLine->TaxRate * $_SESSION['Invoice']->FreightCost);
	}
	$i++;
	$TaxGLCodes[$FreightTaxLine->TaxAuthID] = $FreightTaxLine->TaxGLCode;
}
echo '</TD>';

echo '<TD ALIGN=RIGHT>' . number_format($FreightTaxTotal,2) . '</TD>
	<TD ALIGN=RIGHT>' . number_format($FreightTaxTotal+ $_POST['ChargeFreightCost'],2) . '</TD>
	</TR>';

$TaxTotal += $FreightTaxTotal;

$DisplaySubTotal = number_format(($_SESSION['Invoice']->total + $_POST['ChargeFreightCost']),2);


/* round the totals to avoid silly entries */
$TaxTotal = round($TaxTotal,2);
$_SESSION['Invoice']->total = round($_SESSION['Invoice']->total,2);
$_POST['ChargeFreightCost'] = round($_POST['ChargeFreightCost'],2);

echo '<TR>
	<TD COLSPAN=8 ALIGN=RIGHT>' . _('Shipment Totals'). '</TD>
	<TD  ALIGN=RIGHT><HR><B>'.$DisplaySubTotal.'</B><HR></TD>
	<TD COLSPAN=2></TD>
	<TD ALIGN=RIGHT><HR><B>' . number_format($TaxTotal,2) . '</B><HR></TD>
	<TD ALIGN=RIGHT><HR><B>' . number_format($TaxTotal+($_SESSION['Invoice']->total + $_POST['ChargeFreightCost']),2) . '</B><HR></TD>
</TR>';



//if (!isset($_POST['DispatchDate']) OR  ! Is_Date($_POST['DispatchDate'])){
	//iJPe		realhost	2010-01-09
	//Modificacion para facturar el dia en que se realizo la remision
	//$DefaultDispatchDate = Date($_SESSION['DefaultDateFormat'],CalcEarliestDispatchDate());
	/*$DefaultDispatchDate = date($_SESSION['DefaultDateFormat'],strtotime($rh_fechaCompRemision));
	
} else {

	$DefaultDispatchDate = $_POST['DispatchDate'];
}*/

	$DefaultDispatchDate = date("Y-m-d H:i:s");

echo '</TABLE>';

	// TERMINA CODIGO QUE IMPRIME INFO DE LA REMISION
	$TOTTAX += $TaxTotal;
	$TOTFREIGHT += $_POST['ChargeFreightCost'];
	$TOTFINAL += $TaxTotal+($_SESSION['Invoice']->total + $TOTFREIGHT);
	//$TOTFINAL += $TaxTotal+($_SESSION[$remisiones[$ii]]->total + $_POST['ChargeFreightCost'.$remisiones[$ii]]);


	//echo "PROCESSING ORDER: ".$_SESSION['ProcessingOrder'];

	//$TOTFINAL = number_format($TOTFINAL,2);
	//$TOTTAX = number_format($TOTTAX,2);
 	echo '<STRONG><H3>' . _('TOTAL FINAL'). ': '.
		number_format($TOTFINAL,2).'</h3></strong>';
    //Jaime, addenda
   ?>
    <?php
    //Termina Jaime, addenda
    
	//SAINTS inhabilitar captura de la fecha de factura y detectar fecha actual 03/02/2011
 echo '<TABLE><TR>
		<TD>' ._('Date Of Dispatch'). ':</TD>
		<TD><INPUT type="text" maxlength="12" size="16" name="DispatchDate" value="'.$DefaultDispatchDate.'" readonly></TD>
	</TR>';
	//SAINTS fin
       echo '<TR>
                    <TD>' . _('Metodo de Pago'). ':</TD>
                    <TD><INPUT TYPE=text SIZE=65 name="metodoPago" value="' . $_SESSION['metodoPago'] . '"></TD>
    </TR>';
    echo '<TR>
                    <TD>' . _('Cuenta de Pago'). ':</TD>
                    <TD><INPUT TYPE=text SIZE=65 name="cuentaPago" value="' . $_SESSION['cuentaPago'] . '"></TD>
            </TR>';

	echo '<TR>
		<TD>' . _('Consignment Note Ref'). ':</TD>
		<TD><INPUT TYPE=text MAXLENGTH=15 SIZE=15 name=Consignment value="' . $_POST['Consignment'] . '"></TD>
	</TR>';

	echo '<TR>
		<TD>'.('Action For Balance'). ':</TD>
		<TD><SELECT name=BOPolicy><OPTION SELECTED Value="BO">'._('Automatically put balance on back order').'<OPTION Value="CAN">'._('Cancel any quantites not delivered').'</SELECT></TD>
	</TR>';
	echo '<TR>
		<TD>' ._('Shipment Text'). ':</TD>
		<TD><TEXTAREA NAME=InvoiceText COLS=31 ROWS=5>' . rh_impresion($_POST['InvoiceText']) . '</TEXTAREA></TD>
	</TR>';
        //Realhost Jaime Lun 22 Mar 2010 17:47, Se agrego el Select HTML para escoger el folio
        //Obtenemos las series de la location escogida para la factura (pedido)
        $sql = "select fromstkloc idLocation from salesorders where orderno = " . $_SESSION['ProcessingOrder'];
        $result = DB_query($sql,$db,'','',false,false);
        $row = mysql_fetch_array($result);
        $idLocation = $row['idLocation'];
        $sql = "select id_ws_csd, serie from rh_cfd__locations__systypes__ws_csd where id_locations = '$idLocation' and !isnull(id_ws_csd) and id_systypes = 10";
        $result = DB_query($sql,$db,'','',false,false);
        //Termina Obtenemos las series de la location escogida para la factura (pedido)
        //Creamos el Select con las series
        $tableRowSerieCfd = '<tr><td>' ._('Serie del CFD'). ':</td>';
        $selectIdFolio = '<select id="selectIdFolio" name="selectIdFolio"><option></option>';
        switch(mysql_num_rows($result)) {
            case 1:
            //se usa la unica serie que tiene un csd y esta asosiada a esta location
                $row = mysql_fetch_array($result, MYSQLI_NUM);
                $inputHiddenSerie = '<input type="hidden" value="' . $row[0] . '-' . $row[1] . '" id="selectIdFolio" name="selectIdFolio"/>';
                echo $inputHiddenSerie;
                break;
            default:
            //si hay mas de una serie o si no hay series asosiadas a esta location (y con un csd)
                while($row = mysql_fetch_array($result, MYSQLI_NUM))
                    $selectIdFolio .= '<option value="' . $row[0] . '-' . $row[1] . '">' . $row[1] . '</option>';
                $selectIdFolio .= '</select>';
                $tableRowSerieCfd.= '<td>' . $selectIdFolio . '</td></tr>';
                echo $tableRowSerieCfd;
                break;
        }
        //Termina Creamos el Select con las series
        //Termina Realhost Jaime Lun 22 Mar 2010 17:47, Se agrego el Select HTML para escoger el folio
        echo '</TABLE>';

	if(!isset($_POST['ProcessInvoice'])){
	echo '<CENTER>
	<INPUT TYPE=SUBMIT NAME=Update Value=' . _('Update'). '><P>';

	echo '<INPUT TYPE=SUBMIT NAME="ProcessInvoice" Value="'._('Process Invoice').' '._('from').' '.$_SESSION['Invoice']->Location.'"</CENTER>';

	echo '<INPUT TYPE=HIDDEN NAME= "TOTFINAL" VALUE="'.$TOTFINAL.'">';
	echo '<INPUT TYPE=HIDDEN NAME= "TOTTAX" VALUE="'.$TOTTAX.'">';
	echo '<INPUT TYPE=HIDDEN NAME= "TOTFREIGHT" VALUE="'.$TOTFREIGHT.'">';
	echo '<INPUT TYPE=HIDDEN NAME="ShipVia" VALUE="'.$_SESSION['Invoice']->ShipVia.'">';
	//echo '<INPUT TYPE=HIDDEN NAME="ShipVia" VALUE="' . $_SESSION[$remisiones[$ii]]->ShipVia . '">';
	$ii=0;
	while($ii < $NoRem){
		echo '<INPUT TYPE=HIDDEN NAME="remisiones[]" VALUE="'.$remisiones[$ii].'">';
		$ii++;
	}

}
	echo "</FORM>";

	// FIN DE CALCULO DE TOTALES, TAXES, ETC...
}

if(isset($_POST['ProcessInvoice'])){
	// HACER EL INSERT YA CON LOS TOTALES Y LOS TAXES CALCULADOS

	/* SQL to process the postings for sales invoices...

/*First check there are lines on the dipatch with quantities to invoice
invoices can have a zero amount but there must be a quantity to invoice */

	$TOTFINAL = $_POST['TOTFINAL'];
	$TOTFREIGHT = $_POST['TOTFREIGHT'];
	$TOTTAX = $_POST['TOTTAX'];
	$remisiones = $_POST['remisiones'];


	$ii = 0;
	$NoRem = count($remisiones);

	if (!isset($_POST['DispatchDate']) OR  ! Is_Date($_POST['DispatchDate'])){

	$DefaultDispatchDate = Date($_SESSION['DefaultDateFormat'],CalcEarliestDispatchDate());
} else {

	$DefaultDispatchDate = $_POST['DispatchDate'];
}

	$QuantityInvoicedIsPositive = false;
$ii=0;

	foreach ($_SESSION['Invoice']->LineItems as $OrderLine) {
		if ($OrderLine->QtyDispatched > 0){
			$QuantityInvoicedIsPositive =true;
		}
	}

	if (! $QuantityInvoicedIsPositive){
		prnMsg( _('There are no lines on this order with a quantity to shipment') . '. ' . _('No further processing has been done'),'error');
		include('includes/footer.inc');
		exit;
	}
/* Now Get the area where the sale is to from the branches table */
$ii=0;
	$SQL = "SELECT area,
			defaultshipvia
		FROM custbranch
		WHERE custbranch.debtorno ='". $_SESSION['Invoice']->DebtorNo . "'
		AND custbranch.branchcode = '" . $_SESSION['Invoice']->Branch . "'";

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

	$ii=0;
//while($ii < $NoRem){

if($_SESSION['GroupItems']==1){
	$SQL = "SELECT stockid,
			(qty) AS qty
		FROM rh_remdetails
		WHERE transno IN (";
		$k=0;
				foreach($remisiones as $rm){
					if($k > 0)$SQL .= ', ';
					$SQL .= $rm;
					$k++;
				}
			$SQL .= ')';
			$SQL .= "AND stockid IN (".$_SESSION['Invoice']->Get_StockID_List().")".
			//GROUP BY line
                        "ORDER BY transno,line";
}else {
	$SQL = "SELECT stockid,
			qty
		FROM rh_remdetails
		WHERE transno IN (";
		$k=0;
				foreach($remisiones as $rm){
					if($k > 0)$SQL .= ', ';
					$SQL .= $rm;
					$k++;
				}
			$SQL .= ')';
			$SQL .= "AND stockid IN (".$_SESSION['Invoice']->Get_StockID_List().") ORDER BY transno, line";
}
	$Result = DB_query($SQL,$db);

	if (DB_num_rows($Result) != count($_SESSION['Invoice']->LineItems)){

	/*there should be the same number of items returned from this query as there are lines on the invoice - if  not 	then someone has already invoiced or credited some lines */

		if ($debug==1){
			echo '<BR>'.$SQL;
			echo '<BR>' . _('Number of rows returned by SQL') . ':' . DB_num_rows($Result);
			echo '<BR>' . _('Count of items in the session') . ' ' . count($_SESSION['Invoice']->LineItems);
		}

		echo '<P>';
		prnMsg( _('This order has been changed or shiped since this delivery was started to be confirmed') . '. ' . _('Processing halted') . '. ' . _('To enter and confirm this dispatch') . '/' . _('ship the order must be re-selected and re-read again to update the changes made by the other user'), 'error');
		echo '<BR>';

		echo '<CENTER><A HREF="'. $rootpath/SelectSalesOrder.php.'?' . SID . '">'. _('Select a sales order for confirming deliveries and invoicing'). '</A></CENTER>';

		unset($_SESSION[$remisiones[$ii]]->LineItems);
		unset($_SESSION[$remisiones[$ii]]);
		unset($_SESSION['ProcessingOrder']);
		unset($_SESSION['Invoice']);
		include('includes/footer.inc'); exit;
	}
$ii++;
//}
	$Changes =0;

$ii=0;
// bowikaxu2 - no se ocupa irse remision por remision
//while($ii < $NoRem){

if($_SESSION['GroupItems']==1){
	$SQL = "SELECT stockid,
			(qty) AS qty
		FROM rh_remdetails
		WHERE transno IN (";
		$k=0;
				foreach($remisiones as $rm){
					if($k > 0)$SQL .= ', ';
					$SQL .= $rm;
					$k++;
				}
			$SQL .= ')';
			$SQL .= "AND stockid IN (".$_SESSION['Invoice']->Get_StockID_List().")".
			//GROUP BY line
                        "ORDER BY transno, line";
}else {
	$SQL = "SELECT stockid,
			qty
		FROM rh_remdetails
		WHERE transno IN (";
		$k=0;
				foreach($remisiones as $rm){
					if($k > 0)$SQL .= ', ';
					$SQL .= $rm;
					$k++;
				}
			$SQL .= ')';
			$SQL .= "AND stockid IN (".$_SESSION['Invoice']->Get_StockID_List().")";
}

	$Result = DB_query($SQL,$db);
	$k=0;
	while ($myrow = DB_fetch_array($Result)) {

            //if ($_SESSION['Invoice']->LineItems[$k]->Quantity != $myrow['qty'] OR $_SESSION['Invoice']->LineItems[$k]->QtyInv != $myrow['qtyinvoiced']) {
		if ($_SESSION['Invoice']->LineItems[$k]->Quantity != $myrow['qty']) {

			// echo '<BR>'. _('Orig order for'). ' ' . $myrow['orderlineno'] . ' '. _('has a quantity of'). ' ' .
			echo '<BR>'. _('Orig order for'). ' ' . 'Invoice' .' -- '.$_SESSION['Invoice']->LineItems[$k]->StockID . ' '. _('has a quantity of'). ' ' .
				$myrow['qty'] . ' '.
				_('the session shows quantity of'). ' ' . $_SESSION['Invoice']->LineItems[$k]->Quantity;

	                prnMsg( _('This remision has been changed or shiped since this delivery was started to be confirmed') . ' ' . _('Processing halted.') . ' ' . _('To enter and confirm this dispatch, it must be re-selected and re-read again to update the changes made by the other user'), 'error');
	                prnMsg( _('This remision has been changed or shiped since this delivery was started to be confirmed') . '. ' . _('Processing halted') . '. ' . _('To enter and confirm this dispatch') . '/' . _('ship the order must be re-selected and re-read again to update the changes made by the other user'), 'error');
        	        echo '<BR>';

                	echo '<CENTER><A HREF="'. $rootpath . '/SelectSalesOrder.php?' . SID . '">'. _('Select a sales order for confirming deliveries and invoicing'). '</A></CENTER>';

	                unset($_SESSION[$remisiones[$ii]]->LineItems);
        	        unset($_SESSION[$remisiones[$ii]]);
                	unset($_SESSION['ProcessingOrder']);
                	unset($_SESSION['Invoice']);
	                include('includes/footer.inc');
			exit;
		}
		$k++;
	} /*loop through all line items of the order to ensure none have been invoiced since started looking at this order*/
$ii++;
//}

	// bowikaxu - agrupar articulos
	/*
if($_SESSION['GroupItems']==1){
	if($NoRem > 1){
		$j = 0;
		$i = 1;
		$TOTFINAL = 0; // recalcular el total final
		$TOTTAX = 0; // recalcular el total de impuestos
			while($j < $NoRem){	// go trough all the shipments
				foreach ($_SESSION[$remisiones[$j]]->LineItems as $Itm2) { // primer carrito
					$i=$j+1;
					while($i < $NoRem){
						foreach ($_SESSION[$remisiones[$i]]->LineItems as $Itm) { // segundo carrito

							if($Itm2->StockID == $Itm->StockID){
								$mergeprice = ($Itm->Price + $Itm2->Price) / 2; // obtener el precio intermedio de los articulos
								$newqty = $Itm->Quantity + $Itm2->Quantity;
								$newdiscount = ($Itm->DiscountPercent + $Itm2->DiscountPercent) / 2;
								// update the already added item
								// update_cart_item( $UpdateLineNumber, $Qty, $Price, $Disc, $Narrative, $UpdateDB='No'){
								$_SESSION[$remisiones[$j]]->update_cart_item($Itm2->LineNumber, $newqty , $mergeprice, $newdiscount, $Itm->Narrative.' '.$Itm2->Narrative, $UpdateDB='No');
								$_SESSION[$remisiones[$i]]->remove_from_cart($Itm->LineNumber,$UpdateDB='No');

								$Itm2->QtyDispatched = $Itm2->Quantity;
								// bowikaxu - recalculate taxes
								$_SESSION[$remisiones[$j]]->GetTaxes($Itm2->LineNumber);

								//$_SESSION[$remisiones[$j-1]]->total += ($Itm2->QtyDispatched * $Itm2->Price * (1 - $Itm2->DiscountPercent));
								$_SESSION[$remisiones[$j]]->totalVolume += ($Itm2->QtyDispatched * $Itm2->Volume);
								$_SESSION[$remisiones[$j]]->totalWeight += ($Itm2->QtyDispatched * $Itm2->Weight);
								//bowikaxu - obtener el nuevo total despues de verificar duplicados
								//$TOTFINAL += ($newqty * $mergeprice * (1 - $newdiscount));

							}else {
								//echo "NO IGUAL ".$Itm2->StockID." y ".$Itm->StockID."<BR>";

							}
					}
					$i++;
				} // termina while de remisiones i

			} // termina el while de las remisiones j
			$j++;
	}
		$TOTFINAL += $TOTTAX;
	}
}
*/
	// fin verificar articulos repetidos
	DB_free_result($Result);
	/*Now Get the next invoice number - function in SQL_CommonFunctions*/

	$InvoiceNo = GetNextTransNo(10, $db);
	$PeriodNo = GetPeriod($DefaultDispatchDate, $db);

/*Start an SQL transaction */

    //Jaime, iniciamos transaccion
    //Inicia Transaccion
    if(!DB_query('begin',$db,'','',false,false)) {
        $msg .= '(SQL) Error al efectuar el begin';
        echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' . $msg . '<p></div>';
        include('includes/footer.inc');
        exit;
    }
    //Termina Jaime, iniciamos transaccion

	$Result = DB_query($SQL,$db);
	if(isset($_SESSION['NewCustRem'])){
		echo "<BR>CLIENTE FINAL: ".$_SESSION['NewCustRem'].' '.$_SESSION['NewBranchRem']."<BR>";
	}else {
		//echo "<BR>NO HAY CLIENTE NUEVO<BR>";
	}
$ii=0;
// bowikaxu - probar recalcular el total
$TOTFINAL = 0;
$TOTTAX = 0;
//while($ii < $NoRem){
	if ($DefaultShipVia != $_SESSION['Invoice']->ShipVia){
		$SQL = "UPDATE custbranch SET defaultshipvia ='" . $_SESSION['Invoice']->ShipVia . "' WHERE debtorno='" . $_SESSION['Invoice']->DebtorNo . "' AND branchcode='" . $_SESSION['Invoice']->Branch . "'";
		$ErrMsg = _('Could not update the default shipping carrier for this branch because');
		$DbgMsg = _('The SQL used to update the branch default carrier was');
		$result = DB_query($SQL,$db, $ErrMsg, $DbgMsg, true);

	}
	// bowikaxu realhost - 16 marzo 2007 - inserts invertidos a gltrans de remision
while($ii < $NoRem){
	$TranDate = FormatDateForSQL($DefaultDispatchDate);
	$sql = "INSERT INTO gltrans (type, typeno, chequeno, trandate, periodno, account, narrative, amount, posted, jobref)
			SELECT type, typeno, chequeno, NOW(),'".$PeriodNo."', account, CONCAT(narrative,'".'-F:'.$InvoiceNo."'), (-1*amount), 1, jobref FROM
			gltrans WHERE type=20000 AND typeno = '".$remisiones[$ii]."'";

	$ErrMsg = _('CRITICAL ERROR') . ' ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Imposible Actualizar los Asientos Contables de la Remision');
	$DbgMsg = _('El SQL para actualizar los asientos contables es');
	$Result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	$ii++;
}
	// bowikaxu - recalculate final total
	foreach ($_SESSION['Invoice']->LineItems as $Itm) {

		$TOTFINAL += ($Itm->QtyDispatched * $Itm->Price * (1 - $Itm->DiscountPercent));
		$TaxLineTotal = 0;
							foreach ($Itm->Taxes AS $Tax) {

								if ($Tax->TaxOnTax ==1){
									$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * ($Itm->QtyDispatched * $Itm->Price * (1 - $Itm->DiscountPercent) + $TaxLineTotal));
									$TaxLineTotal += ($Tax->TaxRate * ($Itm->QtyDispatched * $Itm->Price * (1 - $Itm->DiscountPercent) + $TaxLineTotal));
								} else {
									$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * $Itm->QtyDispatched * $Itm->Price * (1 - $Itm->DiscountPercent));
									$TaxLineTotal += ($Tax->TaxRate * $Itm->QtyDispatched * $Itm->Price * (1 - $Itm->DiscountPercent));
								}
									$TaxGLCodes[$Tax->TaxAuthID] = $Tax->TaxGLCode;
								}

							$TOTTAX += $TaxLineTotal;
	}

	//$ii++;
//

	$TOTFINAL += $TOTTAX;
	$TOTFINAL += $TOTFREIGHT;
	$DefaultDispatchDate = FormatDateForSQL($DefaultDispatchDate);

	$OldCust = $_SESSION['Invoice']->DebtorNo;
	$OldBranch = $_SESSION['Invoice']->Branch;
// bowikaxu realhost - nuevo cliente
	if(isset($_SESSION['NewCustRem']) && isset($_SESSION['NewBranchRem'])){

		$_SESSION['Invoice']->Branch = $_SESSION['NewBranchRem'];
		$_SESSION['Invoice']->DebtorNo = $_SESSION['NewCustRem'];

	}

/*Update order header for invoice charged on */
	$SQL = "UPDATE salesorders SET comments = CONCAT(salesorders.comments,' Inv " . $InvoiceNo . "') WHERE orderno = " . $_SESSION['ProcessingOrder'];
	// bowikaxu realhost - nuevo cliente
	if(isset($_SESSION['NewCustRem'])){
		$SQL = "UPDATE salesorders SET comments = 'Inv " . $InvoiceNo . "',
				debtorno = '".$_SESSION['NewCustRem']."',
				branchcode = '".$_SESSION['NewBranchRem']."'
		WHERE orderno = " . $_SESSION['ProcessingOrder'];
	}
	//echo $SQL."<BR>";
	$ErrMsg = _('CRITICAL ERROR') . ' ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales order header could not be updated with the shipment number');
	$DbgMsg = _('The following SQL to update the sales order was used');
	$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

	// bowikaxu - obtener listado de remisiones

	// bowikaxu - crear listado de remisiones a facturar
	$RemList = ' Rm: ';
	foreach($remisiones as $rem){

		$RemList = $RemList.$rem.', ';

	}

/*
 * rleal
 * Jul 1 2010
 * El ultimo stkmove antes de hacer cualquier insert
 */
//	$sqlUpdatSM = "select max(stkmoveno) as last from stockmoves";
//    $rowSM = DB_fetch_array(DB_query($sqlUpdatSM, $db));
//	$rh_stockmove1=$rowSM['last'];
							
							
/*Now insert the DebtorTrans */

$ii=0;
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
			rh_createdate			
			)
		VALUES (
			". $InvoiceNo . ",
			10,
			'" . $_SESSION['Invoice']->DebtorNo . "',
			'" . $_SESSION['Invoice']->Branch . "',
			NOW(),
			" . $PeriodNo . ",
			'',
			'" . $_SESSION['Invoice']->DefaultSalesType . "',
			" . $_SESSION['ProcessingOrder'] . ",
			" . ($TOTFINAL-$TOTTAX-$TOTFREIGHT) . ",
			" . $TOTTAX . ",
			" . $TOTFREIGHT . ",
			" . $_SESSION['CurrencyRate'] . ",
			'" . DB_escape_string($_POST['InvoiceText']). "',
			" . $_SESSION['Invoice']->ShipVia . ",
			'"  . $_POST['Consignment'] . "',
			NOW()			
		)";
	//
// bowikaxu2
// $TOTTAX por $TaxTotal y $TOTFINAL por $_SESSION[$remisiones[$ii]]->total
// $_POST['ChargeFreightCost'] por $TOTFREIGHT

	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction record could not be inserted because');
	$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
 	$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

	$DebtorTransID = DB_Last_Insert_ID($db,'debtortrans','id');

/*
	insert to relate internal invoice number with an external invoice number
	december 2006 - bowikaxu
*/
 	// bowikaxu - get the next external invoice number
	$sql = "SELECT extinvoices, rh_serie FROM rh_locations WHERE loccode = '".$_SESSION['Invoice']->Location."'";

	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not get the total external invoices');
	$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
 	$Result2 = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	$ExtInvoice = DB_fetch_array($Result2);
	if($ExtInvoice['extinvoices']<1)$ExtInvoice['extinvoices']=0;

	$sql = "SELECT id FROM rh_invoicesreference WHERE loccode = '".$_SESSION['Invoice']->Location."' AND extinvoice = '".$ExtInvoice['extinvoices']."'";
	//$res = DB_query($sql,$db);
	//if(DB_num_rows($res)>0){
        if (1==2) {
		prnMsg('El numero de factura externo ya existe !!!','error');
		DB_query('ROLLBACK',$db);
		include('includes/footer.inc');
		exit;
	}

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
			'".$_SESSION['Invoice']->Location."',
			'".$DebtorTransID."')";

	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The relation transaction record could not be inserted because');
	$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
 	$Result2 = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);

 	// bowikaxu - add 1 to the total quantity of external invoices of that location
        //iJPe
        if ($_SESSION['Invoice']->LocationMoves == $_SESSION['Invoice']->Location){
            $sql = "UPDATE locations SET extinvoices=".($ExtInvoice['extinvoices']+1)." WHERE loccode = '".$_SESSION['Invoice']->Location."'";
        }else{
            $sql = "UPDATE rh_locations_virtual SET extinvoices=".($ExtInvoice['extinvoices']+1)." WHERE loccode = '".$_SESSION['Invoice']->Location."'";
        }

	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not get the total external invoices');
	$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
 	$Result3 = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
// bowikaxu - end of relate internal and external invoices

/* Insert the tax totals for each tax authority where tax was charged on the invoice */

	foreach ($TaxTotals AS $TaxAuthID => $TaxAmount) {

		$SQL = 'INSERT INTO debtortranstaxes (debtortransid,
							taxauthid,
							taxamount)
				VALUES (' . $DebtorTransID . ',
					' . $TaxAuthID . ',
					' . $TOTTAX/$_SESSION['CurrencyRate'] . ')';
		// $TaxAmount/$_SESSION['CurrencyRate'] . ')'; por $TOTTAX
		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction taxes records could not be inserted because');
		$DbgMsg = _('The following SQL to insert the debtor transaction taxes record was used - TaxTotals');
 		$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
	}


/* If balance of the order cancelled update sales order details quantity. Also insert log records for OrderDeliveryDifferencesLog */
$ii=0;
//while($ii < $NoRem){
	foreach ($_SESSION['Invoice']->LineItems as $OrderLine) {

		if ($_POST['BOPolicy']=='CAN'){

			$SQL = "UPDATE salesorderdetails
				SET quantity = quantity - " . ($OrderLine->Quantity - $OrderLine->QtyDispatched) . " WHERE orderno = " . $_SESSION['ProcessingOrder'] . " AND stkcode = '" . $OrderLine->StockID . "'";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales order detail record could not be updated because');
			$DbgMsg = _('The following SQL to update the sales order detail record was used');
//			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);


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
						'" . $_SESSION['Invoice']->DebtorNo . "',
						'" . $_SESSION['Invoice']->Branch . "',
						'CAN'
						)";

				$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The order delivery differences log record could not be inserted because');
				$DbgMsg = _('The following SQL to insert the order delivery differences record was used');
				//$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			}



		} elseif (($OrderLine->Quantity - $OrderLine->QtyDispatched) >0 && DateDiff(ConvertSQLDate($DefaultDispatchDate),$_SESSION['Invoice']->DeliveryDate,'d') >0) {

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
					'" . $_SESSION['Invoice']->DebtorNo . "',
					'" . $_SESSION['Invoice']->Branch . "',
					'BO'
				)";

			$ErrMsg =  '<BR>' . _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The order delivery differences log record could not be inserted because');
			$DbgMsg = _('The following SQL to insert the order delivery differences record was used');
//			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
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
					AND stkcode = '" . $OrderLine->StockID . "'";
			} else {
				$SQL = "UPDATE salesorderdetails
					SET qtyinvoiced = qtyinvoiced + " . $OrderLine->QtyDispatched . ",
					actualdispatchdate = '" . $DefaultDispatchDate .  "'
					WHERE orderno = " . $_SESSION['ProcessingOrder'] . "
					AND stkcode = '" . $OrderLine->StockID . "'";

			}

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales order detail record could not be updated because');
			$DbgMsg = _('The following SQL to update the sales order detail record was used');
			//$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

			 /* Update location stock records if not a dummy stock item
			 need the MBFlag later too so save it to $MBFlag */
			$Result = DB_query("SELECT mbflag FROM stockmaster WHERE stockid = '" . $OrderLine->StockID . "'",$db,"<BR>Can't retrieve the mbflag",'Fallo el SQL',true);

			$myrow = DB_fetch_row($Result);
			$MBFlag = $myrow[0];

			if ($MBFlag=="B" OR $MBFlag=="M") {
				$Assembly = False;

				/* Need to get the current location quantity
				will need it later for the stock movement */
               			$SQL="SELECT locstock.quantity
					FROM locstock
					WHERE locstock.stockid='" . $OrderLine->StockID . "'
					AND loccode= '" . $_SESSION['Invoice']->LocationMoves . "'";
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
					AND loccode = '" . $_SESSION['Invoice']->LocationMoves . "'";
					
					/*
					 * rleal Jun 25 \2010
					 * Se agrega este codigo para reforzar el calculo de locstock
					 */		
                      //$rh_udtlocstock = "call update_locstock('" . $_SESSION['Invoice']->LocationMoves ."','" . $OrderLine->StockID . "')";
                      //DB_query($rh_udtlocstock, $db);
					

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated because');
				$DbgMsg = _('The following SQL to update the location stock record was used');
//				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

				// bowikaxu realhost 15 july 2008 -  ensamblado costo por componente
			} else if ($MBFlag=='A' OR $MBFlag=='E'){ /* its an assembly */
				/*Need to get the BOM for this part and make
				stock moves for the components then update the Location stock balances */
				$Assembly=True;
				$StandardCost =0; /*To start with - accumulate the cost of the comoponents for use in journals later on */
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
						AND loccode= '" . $_SESSION['Invoice']->LocationMoves . "'";

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
				
							
	//rleal
	//Jun 16 2010
	//Se agrega un movimiento igual pero qty contraria para no poner a 0 la(s) remision(es)							

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
                                                        rh_orderline, hidemovt
						) VALUES (
							'" . $AssParts['component'] . "',
							 -10,
							 " . $InvoiceNo . ",
							 '" . $_SESSION['Invoice']->LocationMoves . "',
							 NOW(),
							 '" . $_SESSION['Invoice']->DebtorNo . "',
							 '" . $_SESSION['Invoice']->Branch . "',
							 " . $PeriodNo . ",
							 '" . _('Assembly') . ': ' . $OrderLine->StockID . ' ' . _('Order') . ': ' . $_SESSION['ProcessingOrder'] .' Mov Escondido'.$RemList ."',
							 " . $AssParts['quantity'] * $dispatch . ",
							 " . $AssParts['standard'] . ",
							 0,
							 " . ($QtyOnHandPrior +($AssParts['quantity'] * $dispatch)) . ",
                                                         '".$OrderLine->LineNumber."', 2
						)";
					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records for the assembly components of'). ' '. $OrderLine->StockID . ' ' . _('could not be inserted because');
					$DbgMsg = _('The following SQL to insert the assembly components stock movement records was used');
					$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

//fin de modificacion					
							
							

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
							 10,
							 " . $InvoiceNo . ",
							 '" . $_SESSION['Invoice']->LocationMoves . "',
							 NOW(),
							 '" . $_SESSION['Invoice']->DebtorNo . "',
							 '" . $_SESSION['Invoice']->Branch . "',
							 " . $PeriodNo . ",
							 '" . _('Assembly') . ': ' . $OrderLine->StockID . ' ' . _('Order') . ': ' . $_SESSION['ProcessingOrder'] . ' No hay mov real'. $RemList ."',
							 " . -$AssParts['quantity'] * $dispatch . ",
							 " . $AssParts['standard'] . ",
							 0,
							 " . ($QtyOnHandPrior -($AssParts['quantity'] * $dispatch)) . ",
                                                         '".$OrderLine->LineNumber."'
						)";
					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records for the assembly components of'). ' '. $OrderLine->StockID . ' ' . _('could not be inserted because');
					$DbgMsg = _('The following SQL to insert the assembly components stock movement records was used');
					$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

/*
					$SQL = "UPDATE locstock1
						SET quantity = locstock.quantity - " . $AssParts['quantity'] * $OrderLine->QtyDispatched . "
						WHERE locstock.stockid = '" . $AssParts['component'] . "'
						AND loccode = '" . $_SESSION['Invoice']->LocationMoves . "'";
	*/
					$SQL = "UPDATE locstock SET quantity = locstock.quantity 
						WHERE locstock.stockid = '" . $AssParts['component'] . "'
						AND loccode = '" . $_SESSION['Invoice']->LocationMoves . "'";
						
					/*
					 * rleal Jun 25 \2010
					 * Se agrega este codigo para reforzar el calculo de locstock
					 */		
                    //  $rh_udtlocstock = "call update_locstock('" . $_SESSION['Invoice']->LocationMoves ."','" . $AssParts['component'] . "')";
                    //  DB_query($rh_udtlocstock, $db);
					

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated for an assembly component because');
					$DbgMsg = _('The following SQL to update the locations stock record for the component was used');
//					$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
				} /* end of assembly explosion and updates */

				/*Update the cart with the recalculated standard cost from the explosion of the assembly's components*/
				$_SESSION[$remisiones[$ii]]->LineItems[$OrderLine->LineNumber]->StandardCost = $StandardCost;
				$OrderLine->StandardCost = $StandardCost;
			} /* end of its an assembly */

			// Insert stock movements - with unit cost
			$LocalCurrencyPrice= ($OrderLine->Price / $_SESSION['CurrencyRate']);
			
				
	//rleal
	//Jun 16 2010
	//Se agrega un movimiento igual pero qty contraria para no poner a 0 la(s) remision(es)		
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
                                                rh_orderline,
												hidemovt )
					VALUES ('" . $OrderLine->StockID . "',
						-10,
						" . $InvoiceNo . ",
						'" . $_SESSION['Invoice']->LocationMoves . "',
						NOW(),
						'" . $_SESSION['Invoice']->DebtorNo . "',
						'" . $_SESSION['Invoice']->Branch . "',
						" . $LocalCurrencyPrice . ",
						" . $PeriodNo . ",
						'" . $_SESSION['ProcessingOrder'] .' Mov Escondido'.$RemList ."',
						" . $OrderLine->QtyDispatched . ",
						" . $OrderLine->DiscountPercent . ",
						" . $OrderLine->StandardCost . ",
						" . ($QtyOnHandPrior - $OrderLine->QtyDispatched) . ",
						'" . DB_escape_string($OrderLine->Narrative) . "',
                                                '".$OrderLine->LineNumber."',2)";



			}

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records could not be inserted because');
			$DbgMsg = _('The following SQL to insert the stock movement records was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			
	//fin modificacion
			
			
			
			
			//rleal Jun 15 2010 se agregó ' Rem:'.$RemList
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
						10,
						" . $InvoiceNo . ",
						'" . $_SESSION['Invoice']->LocationMoves . "',
						NOW(),
						'" . $_SESSION['Invoice']->DebtorNo . "',
						'" . $_SESSION['Invoice']->Branch . "',
						" . $LocalCurrencyPrice . ",
						" . $PeriodNo . ",
						'" . $_SESSION['ProcessingOrder'] . ' No hay mov real'. $RemList ."',
						" . -$OrderLine->QtyDispatched . ",
						" . $OrderLine->DiscountPercent . ",
						" . $OrderLine->StandardCost . ",
						" . ($QtyOnHandPrior + $OrderLine->QtyDispatched) . ",
						'" . DB_escape_string($OrderLine->Narrative) . "',
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
                                                rh_orderline )
					VALUES ('" . $OrderLine->StockID . "',
						10,
						" . $InvoiceNo . ",
						'" . $_SESSION['Invoice']->LocationMoves . "',
						NOW(),
						'" . $_SESSION['Invoice']->DebtorNo . "',
						'" . $_SESSION['Invoice']->Branch . "',
						" . $LocalCurrencyPrice . ",
						" . $PeriodNo . ",
						'" . $_SESSION['ProcessingOrder'] . ' Rem:'.$RemList ."',
						" . -$OrderLine->QtyDispatched . ",
						" . $OrderLine->DiscountPercent . ",
						" . $OrderLine->StandardCost . ",
						'" . addslashes($OrderLine->Narrative) . "',
                                                '".$OrderLine->LineNumber."')";
			}


			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records could not be inserted because');
			$DbgMsg = _('The following SQL to insert the stock movement records was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
	
			
			
			
			
			
			
			
			
			
			
			

/*Get the ID of the StockMove... */
			//$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');

			// GET LAS INSERT ID PORQUE LA FUNCION ANTERIOR NO REGRESABA VALOR CORRECTO
		$Last="SELECT LAST_INSERT_ID() AS ultimo
						FROM stockmoves";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Can not retrieve assembly components location stock quantities because ');
					$DbgMsg = _('The SQL that failed was');
					$LastQuery = DB_query($Last,$db,$ErrMsg,$DbgMsg,true);
					$ultimo = DB_fetch_array($LastQuery);
					$StkMoveNo = $ultimo['ultimo'];
					DB_free_result($LastQuery);


                                        /*
                                         * iJPe
                                         * 2010-04-28
                                         */
                                        $sqlUpdatSM = "select max(stkmoveno) as last from stockmoves";
                                        $rowSM = DB_fetch_array(DB_query($sqlUpdatSM, $db));

					
					

/*Insert the taxes that applied to this line */
	//print_r($OrderLine);
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

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Taxes and rates applicable to this shipment line item could not be inserted because');
				$DbgMsg = _('The following SQL to insert the stock movement tax detail records was used');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			}


/*Insert the StockSerialMovements and update the StockSerialItems  for controlled items*/

			if ($OrderLine->Controlled ==1){
				foreach($OrderLine->SerialItems as $Item){
                                /*We need to add the StockSerialItem record and
				The StockSerialMoves as well */

					$SQL = "UPDATE stockserialitems
							SET quantity= quantity - " . $Item->BundleQty . "
							WHERE stockid='" . $OrderLine->StockID . "'
							AND loccode='" . $_SESSION['Invoice']->Location . "'
							AND serialno='" . $Item->BundleRef . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be updated because');
					$DbgMsg = _('The following SQL to update the serial stock item record was used');
//					$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

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
//					$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
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
				AND salesanalysis.typeabbrev ='" . $_SESSION['Invoice']->DefaultSalesType . "'
				AND salesanalysis.periodno=" . $PeriodNo . "
				AND salesanalysis.cust " . LIKE . " '" . $OldCust . "'
				AND salesanalysis.custbranch " . LIKE . " '" . $OldBranch . "'
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
					disc=disc+" . ($OrderLine->DiscountPercent * $OrderLine->Price * $OrderLine->QtyDispatched / $_SESSION['CurrencyRate']) . ",
					cust = '" . $_SESSION['Invoice']->DebtorNo . "' ,
					custbranch = '" . $_SESSION['Invoice']->Branch . "'
					WHERE salesanalysis.area='" . $myrow[5] . "'
					AND salesanalysis.salesperson='" . $myrow[8] . "'
					AND typeabbrev ='" . $_SESSION['Invoice']->DefaultSalesType . "'
					AND periodno = " . $PeriodNo . "
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
					SELECT '" . $_SESSION['Invoice']->DefaultSalesType . "',
						" . $PeriodNo . ",
						" . ($OrderLine->Price * $OrderLine->QtyDispatched / $_SESSION['CurrencyRate']) . ",
						" . ($OrderLine->StandardCost * $OrderLine->QtyDispatched) . ",
						'" . $_SESSION['Invoice']->DebtorNo . "',
						'" . $_SESSION['Invoice']->Branch . "',
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
					AND custbranch.debtorno = '" . $OldCust . "'
					AND custbranch.branchcode='" . $OldBranch . "'";
			}

			$ErrMsg = _('Sales analysis record could not be added or updated because');
			$DbgMsg = _('The following SQL to insert the sales analysis record was used');
			//$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			// bowikaxu realhost - 16 marzo 2007 - no debe duplicar la cantidad en salesanalysis
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
							amount,
							posted
							)
					VALUES (
						10,
						" . $InvoiceNo . ",
						NOW(),
						" . $PeriodNo . ",
						" . GetCOGSGLAccount($Area, $OrderLine->StockID, $_SESSION['Invoice']->DefaultSalesType, $db) . ",
						'" . $_SESSION['Invoice']->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->QtyDispatched . " @ " . $OrderLine->StandardCost . "',
						" . $OrderLine->StandardCost * $OrderLine->QtyDispatched . ",1
					)";
				// $OrderLine->StandardCost * $OrderLine->QtyDispatched

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
							amount,posted)
					VALUES (
						10,
						" . $InvoiceNo . ",
						NOW(),
						" . $PeriodNo . ",
						" . $StockGLCode['stockact'] . ",
						'" . $_SESSION['Invoice']->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->QtyDispatched . " @ " . $OrderLine->StandardCost . "',
						" . (-$OrderLine->StandardCost * $OrderLine->QtyDispatched). ",1
					)";
//(-$OrderLine->StandardCost * $OrderLine->QtyDispatched)
				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock side of the cost of sales GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			} /* end of if GL and stock integrated and standard cost !=0 */

			if ($_SESSION['CompanyRecord']['gllink_debtors']==1 AND $OrderLine->Price !=0){

	//Post sales transaction to GL credit sales
				$SalesGLAccounts = GetSalesGLAccount($Area, $OrderLine->StockID, $_SESSION['Invoice']->DefaultSalesType, $db);

				$SQL = "INSERT INTO gltrans (
							type,
							typeno,
							trandate,
							periodno,
							account,
							narrative,
							amount,
							posted
						)
					VALUES (
						10,
						" . $InvoiceNo . ",
						NOW(),
						" . $PeriodNo . ",
						" . $SalesGLAccounts['salesglcode'] . ",
						'" . $_SESSION['Invoice']->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->QtyDispatched . " @ " . $OrderLine->Price . "',
						" . (($OrderLine->Price*$OrderLine->QtyDispatched)*-1)/$_SESSION['CurrencyRate'] . ",1
					)";
// (-$OrderLine->Price * $OrderLine->QtyDispatched/$_SESSION['CurrencyRate'])
				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales GL posting could not be inserted because');
				$DbgMsg = '<BR>' ._('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

				if ($OrderLine->DiscountPercent !=0){

                                    /*
                                     * Juan Mtz 0.o
                                     * realhost
                                     * 25 Sept 2009
                                     *
                                     * Se modifico el monto a asignar en el asiento, ya que el monto asignaba una cantidad negativa
                                     * debido a que se estaba multiplicando por -1, la linea como estaba antes de la modificacion esta comentada
                                     * mas abajo
                                     */

					$SQL = "INSERT INTO gltrans (
							type,
							typeno,
							trandate,
							periodno,
							account,
							narrative,
							amount,
							posted
						)
						VALUES (
							10,
							" . $InvoiceNo . ",
							NOW(),
							" . $PeriodNo . ",
							" . $SalesGLAccounts['discountglcode'] . ",
							'" . $_SESSION['Invoice']->DebtorNo . " - " . $OrderLine->StockID . " @ " . ($OrderLine->DiscountPercent * 100) . "%',
							" . ($OrderLine->Price*$OrderLine->QtyDispatched)*$OrderLine->DiscountPercent/$_SESSION['CurrencyRate'] . ",1
						)";
// (($OrderLine->Price * $OrderLine->QtyDispatched)*-1) * $OrderLine->DiscountPercent/$_SESSION['CurrencyRate']
					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales discount GL posting could not be inserted because');
					$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
				} /*end of if discount !=0 */
			} /*end of if sales integrated with debtors */

		} /*Quantity dispatched is more than 0 */
	} /*end of OrderLine loop */
$ii++;
//}

	if ($_SESSION['CompanyRecord']['gllink_debtors']==1){

/*Post debtors transaction to GL debit debtors, credit freight re-charged and credit sales */
$ii=0;
	if ((($TOTFINAL-$TOTTAX) + $TOTFREIGHT + $TOTTAX) !=0) {
			$SQL = "INSERT INTO gltrans (
						type,
						typeno,
						trandate,
						periodno,
						account,
						narrative,
						amount,
						posted
						)
					VALUES (
						10,
						" . $InvoiceNo . ",
						NOW(),
						" . $PeriodNo . ",
						" . $_SESSION['CompanyRecord']['debtorsact'] . ",
						'" . $_SESSION['Invoice']->DebtorNo . "',
						" . ($TOTFINAL)/$_SESSION['CurrencyRate'] . ",1
					)";
// (($_SESSION[$remisiones[$ii]]->total + $_SESSION[$remisiones[$ii]]->FreightCost + $TaxTotal)/$_SESSION['CurrencyRate'])
			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The total debtor GL posting could not be inserted because');
			$DbgMsg = _('The following SQL to insert the total debtors control GLTrans record was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
		}

		/*Could do with setting up a more flexible freight posting schema that looks at the sales type and area of the customer branch to determine where to post the freight recovery */
$ii=0;
		if ($_SESSION['Invoice']->FreightCost !=0) {
			$SQL = "INSERT INTO gltrans (
						type,
						typeno,
						trandate,
						periodno,
						account,
						narrative,
						amount,
						posted
					)
				VALUES (
					10,
					" . $InvoiceNo . ",
					NOW(),
					" . $PeriodNo . ",
					" . $_SESSION['CompanyRecord']['freightact'] . ",
					'" . $_SESSION['Invoice']->DebtorNo . "',
					" . (-($_SESSION['Invoice']->FreightCost)/$_SESSION['CurrencyRate']) . ",
					1
				)";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The freight GL posting could not be inserted because');
			$DbgMsg = _('The following SQL to insert the GLTrans record was used');
			$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
		}
	// bowikaxu cambio de taxtotals

	foreach ( $TaxTotals as $TaxAuthID => $TaxAmount){
			if ($TOTTAX !=0 ){
				$SQL = "INSERT INTO gltrans (
						type,
						typeno,
						trandate,
						periodno,
						account,
						narrative,
						amount,
						posted
						)
					VALUES (
						10,
						" . $InvoiceNo . ",
						NOW(),
						" . $PeriodNo . ",
						" . $TaxGLCodes[$TaxAuthID] . ",
						'" . $_SESSION['Invoice']->DebtorNo . "',
						" . (-$TOTTAX/$_SESSION['CurrencyRate']) . ",
						1
					)";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The tax GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			}
		}

	} /*end of if Sales and GL integrated */
$ii=0;

while($ii < $NoRem){
	unset($_SESSION[$remisiones[$ii]]->LineItems);
	unset($_SESSION[$remisiones[$ii]]);
	$ii++;
}

/*end of process invoice */

// bowikaxu 18/08/06 INSERT A LA REMISION DE YA FACTURADA
	foreach($remisiones as $rem){

		$SQL = 'UPDATE rh_invoiceshipment SET Facturado=1, Invoice="'.$InvoiceNo.'" WHERE Shipment= '.$rem;

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('No se pudo actualizar el estado de la remision a ya facturada numero '.$rem);
				$DbgMsg = _('El error se encuentra en la tabla rh_invoiceshipment');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

				$SQL2 = "UPDATE debtortrans SET alloc = (ovamount+ovfreight+ovgst),
				debtorno = '".$_SESSION['Invoice']->DebtorNo."',
				branchcode = '".$_SESSION['Invoice']->Branch."',
				settled = 1
				WHERE type = 20000 AND transno = ".$rem;

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('No se pudo actualizar la cantidad provista de la remision # '.$rem);
				$DbgMsg = _('Error al actualizar la cantidad provista de la remision');
				$Result = DB_query($SQL2,$db,$ErrMsg,$DbgMsg,true);

                                //iJPe
                                //Se añadio la actualizacion de newqoh y de reference, newqoh se actualiza por la actualizacion que se hace a la cantidad del movimiento de inventario
                                //mientras que reference se actualiza para identificar en los movimientos en que factura se convirtio esa remision
		/* rleal
		 * Jun 15 21010
		 * Con el cambio arriba no hay necesidad de mover el valor de la remisión
		 
				$SQL3 = "UPDATE stockmoves SET newqoh = newqoh-qty, qty = 0,
				debtorno = '".$_SESSION['Invoice']->DebtorNo."',
				branchcode = '".$_SESSION['Invoice']->Branch."',
                               reference = CONCAT(reference, ' - Factura ". $ExtInvoice['rh_serie'].$ExtInvoice['extinvoices'] ." (".$InvoiceNo.")')
				WHERE type = 20000 AND transno = ".$rem;

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('No se pudo actualizar stockmoves a tippo 10 con remision # '.$rem);
				$DbgMsg = _('El error se encuentra en la tabla stockmoves');
				$Result = DB_query($SQL3,$db,$ErrMsg,$DbgMsg,true);
				*/
										/*
                                         * iJPe
                                         * 2010-04-28
                                        
                                        $sqlUpdatSM = "select max(stkmoveno) as last from stockmoves";
                                        $rowSM = DB_fetch_array(DB_query($sqlUpdatSM, $db));
                                        
                                         */
										
		

	}
	
/*
 * rleal
 * Jul 1 2010
 * Se actualiza locstock & newqoh
 */
							
 	$sqlUpdatSM = "call update_locstock_newqoh(10,". $InvoiceNo .")";
	DB_query($sqlUpdatSM, $db);

        //Jaime, codigo agregado
	// rleal realhost - 1 Oct 2010 - save the invoice address of the client at this time
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
			WHERE debtorno = '". $_SESSION['Invoice']->DebtorNo ."'";
	$res_address = DB_query($sql,$db);

	if(DB_num_rows($res_address)<1){
		prnMsg('Imposible obtener la direccion fiscal del cliente','warn');
		DB_query('ROLLBACK',$db); // do a rollback
		include('includes/footer.inc');
		exit;
	}
	$address = DB_fetch_array($res_address);
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
			'" . $_SESSION['Invoice']->DebtorNo . "',
			'" . $_SESSION['Invoice']->Branch . "',
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
			'" . $address['name'] . "',
			'" . $address['name2'] . "',
			'" . $address['rh_tel'] . "',
			'" . $address['taxref'] . "',
			'" . $address['currcode'] . "'
		)";

	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction record could not be inserted because');
	$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
	$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

	// rleal realhost - 1 OCt 2010 - END save invoice address
        //Termina Jaime, codigo agregado

	// FIN DE LOS INSERTS DE LOS TOTALES Y TAXES CALCULADOS
	unset($_SESSION['ProcessingOrder']);
	unset($_SESSION['Invoice']);

        //Factura Electronica
        $idDebtortrans = $DebtorTransID;
        $sql = "select transno, type from debtortrans where id = $idDebtortrans";
        $result = DB_fetch_array(DB_query($sql, $db));
        $transno = $result['transno'];
        $type = $result['type'];
        $idCsdYSerie = explode('-', $_POST['selectIdFolio']);
        $idCsd = $idCsdYSerie[0];
        $serie = $idCsdYSerie[1];
        //@hardcode soriana
        //$idXsd = 1;
        //$xmlXsd = $_POST['xmlXsd'];
        $datosSat = cfd($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $idXsd, $xmlXsd,$_POST['metodoPago'],$_POST['cuentaPago']);
        printDatosSat($datosSat);
        //Termina Factura Electronica

	echo _('F.I.'). ' '. $InvoiceNo .' '. _('procesada'). '<BR>';

	// bowikaxu - mostrar el numero de factura externa
	echo _('Factura'). ' '. $ExtInvoice['rh_serie'].$ExtInvoice['extinvoices'] .' '. _('procesada'). '<BR>';
	/*
	if($_POST['ExtInvoice'] > 0){
		echo _('Factura Externa'). ' '. $_POST['ExtInvoice'] .' '. _('procesada'). '<BR>';
	}*/
	unset($_SESSION['NewCustRem']);
	unset($_SESSION['NewBranchRem']);
	echo '<A HREF="'.$rootpath.'/rh_PrintCustTrans.php?' . SID . 'FromTransNo='.$InvoiceNo.'&InvOrCredit=Invoice&PrintPDF=True">'. _('Print this invoice'). '</A><BR>';
	echo '<A HREF="'.$rootpath.'/rh_SelectSalesOrder_Shipment.php?' . SID . '">'. _('Select another shipment for Invoice'). '</A><BR>';
	echo '<A HREF="'.$rootpath.'/SelectOrderItems.php?' . SID . 'NewOrder=Yes">'._('Sales Order Entry').'</A><BR>';


        //Realhost Jaime Mier 24 Mar 2010 11:59, se agrego la liga para imprimir la factura electronica en formato PDF
         echo '<A target="_blank" HREF="'.$rootpath."/PHPJasperXML/sample1.php?transno=$InvoiceNo" . SID . '">'._('Imprimir Factura Electronica en PDF').'</A><BR>';
        //Termina Realhost Jaime Mier 24 Mar 2010 11:59, se agrego la liga para imprimir la factura electronica en formato PDF
        //Realhost Jaime Mier 24 Mar 2010 11:59, se agrego la liga para descargar la Factura Electronica en formato XML
        echo '<A HREF="'.$rootpath."/rh_j_downloadFacturaElectronicaXML.php?downloadPath=XMLFacturacionElectronica/facturasElectronicas/" . $datosSat['noCertificado'] . "/" . $datosSat['serie'] . $datosSat['folio'] . '-' . $datosSat['transno'] .'.xml'. SID . '">'._('Descargar Factura Electronica en formato XML').'</A><BR>';
        //Termina Realhost Jaime Mier 24 Mar 2010 11:59, se agrego la liga para descargar la Factura Electronica en formato XML

/*end of process invoice */

	include('includes/footer.inc');
}

// TERMINA CREAR LA FACTURA

?>

<FORM ACTION="<?php echo $_SERVER['PHP_SELF'] . '?' . SID; ?>" METHOD=POST>
<CENTER>
<B><?php echo "<H2><B>Codigo Clientes Varios </B></H2>".$msg; ?></B>
<TABLE CELLPADDING=3 COLSPAN=4>
<TR>
<TD><?php echo _('Text in the'); ?> <B><?php echo _('name'); ?></B>:</TD>
<TD>
<?php
if (isset($_POST['Keywords'])) {
?>
<INPUT TYPE="Text" NAME="Keywords" value="<?php echo $_POST['Keywords']?>" SIZE=20 MAXLENGTH=25>
<?php
} else {
?>
<INPUT TYPE="Text" NAME="Keywords" SIZE=20 MAXLENGTH=25>
<?php
}
?>
</TD>
<TD><FONT SIZE=3><B><?php echo _('OR'); ?></B></FONT></TD>
<TD><?php echo _('Text extract in the customer'); ?> <B><?php echo _('code'); ?></B>:</TD>
<TD>
<?php
if (isset($_POST['CustCode'])) {
?>
<INPUT TYPE="Text" NAME="CustCode" value="<?php echo $_POST['CustCode'] ?>" SIZE=15 MAXLENGTH=18>
<?php
} else {
?>
<INPUT TYPE="Text" NAME="CustCode" SIZE=15 MAXLENGTH=18>
<?php
}
?>
</TD>
</TR>
</TABLE>
<INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Show All'); ?>">
<INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Search Now'); ?>">
<INPUT TYPE=SUBMIT ACTION=RESET VALUE="<?php echo _('Reset'); ?>"></CENTER>
<?php
If (isset($result)) {
  $ListCount=DB_num_rows($result);
  $ListPageMax=ceil($ListCount/$_SESSION['DisplayRecordsMax']);

  if (isset($_POST['Next'])) {
    if ($_POST['PageOffset'] < $ListPageMax) {
	    $_POST['PageOffset'] = $_POST['PageOffset'] + 1;
    }
	}

  if (isset($_POST['Previous'])) {
    if ($_POST['PageOffset'] > 1) {
	    $_POST['PageOffset'] = $_POST['PageOffset'] - 1;
    }
  }

  echo "&nbsp;&nbsp;" . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';
?>

  <select name="PageOffset">

<?php
  $ListPage=1;
  while($ListPage<=$ListPageMax) {
	  if ($ListPage==$_POST['PageOffset']) {
?>

  		<option value=<?php echo($ListPage); ?> selected><?php echo($ListPage); ?></option>

<?php
	  } else {
?>

		  <option value=<?php echo($ListPage); ?>><?php echo($ListPage); ?></option>

<?php
	  }
	  $ListPage=$ListPage+1;
  }
?>

  </select>
  <INPUT TYPE=SUBMIT NAME="Go" VALUE="<?php echo _('Go'); ?>">
  <INPUT TYPE=SUBMIT NAME="Previous" VALUE="<?php echo _('Previous'); ?>">
  <INPUT TYPE=SUBMIT NAME="Next" VALUE="<?php echo _('Next'); ?>">

<?php

  echo '<BR><BR>';

	echo '<TABLE CELLPADDING=2 COLSPAN=7 BORDER=2>';
	$TableHeader = '<TR>
				<TD Class="tableheader">' . _('Code') . '</TD>
				<TD Class="tableheader">' . _('Customer Name') . '</TD>
				<TD Class="tableheader">' . _('Branch') . '</TD>
				<TD Class="tableheader">' . _('Contact') . '</TD>
				<TD Class="tableheader">' . _('Phone') . '</TD>
				<TD Class="tableheader">' . _('Fax') . '</TD>
			</TR>';

	echo $TableHeader;
	$j = 1;
	$k = 0; //row counter to determine background colour
  $RowIndex = 0;

  if (DB_num_rows($result)<>0){
  	DB_data_seek($result, ($_POST['PageOffset']-1)*$_SESSION['DisplayRecordsMax']);
  }

	while (($myrow=DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {

		if ($k==1){
			echo '<tr bgcolor="#CCCCCC">';
			$k=0;
		} else {
			echo '<tr bgcolor="#EEEEEE">';
			$k=1;
		}

		printf("<td><FONT SIZE=1><INPUT TYPE=SUBMIT NAME='Select' VALUE='%s'</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td></tr>",
			$myrow["debtorno"],
			$myrow["name"],
			$myrow["brname"],
			$myrow["contactname"],
			$myrow["phoneno"],
			$myrow["faxno"]);

		$j++;
		If ($j == 11 AND ($RowIndex+1 != $_SESSION['DisplayRecordsMax'])){
			$j=1;
			echo $TableHeader;
		}

    $RowIndex = $RowIndex + 1;
//end of page full new headings if
	}
//end of while loop

	echo '</TABLE>';

}
//end if results to show
echo '</FORM></CENTER>';
//Realhost Jaime Lun 22 Mar 2010 18:45, se agrego la siguiente funcion de Js para validar que se haya escogido una Serie de CFD
?>
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
</script>
<?php
//Termina Realhost Jaime Lun 22 Mar 2010 18:45, se agrego la siguiente funcion de Js para validar que se haya escogido una Serie de CFD
// </FORM>
echo "<br>";

$ii = 0;
$NoRem = count($remisiones);

while($ii < $NoRem){

//print_r($_SESSION[$remisiones[$ii]]);
//echo "<br>";

$ii++;

}

include('includes/footer.inc');

?>

<script language="JavaScript" type="text/javascript">
    //<![CDATA[
            <!--
            document.forms[0].CustCode.select();
            document.forms[0].CustCode.focus();
            //-->
    //]]>
</script>
