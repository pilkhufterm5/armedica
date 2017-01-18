<?php
/* bowikaxu
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:39:36 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 14 $
 */

/* $Revision: 116 $ */

/*
 * FACTURAR ORDENES DE PRODUCCION
 * BOWIKAXU REALHOST
 * 
 */
$PageSecurity = 10;

include('includes/session.inc');
$title = _('Invoice').' '._('Work Orders');
// bowikaxu - archivo necesario para el calendar popup
echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
?>
<script language="JavaScript">
<!-- // create calendar object(s) just after form tag closed
				 
var cal = new CalendarPopup();
				//-->
</script>
<?php
include('includes/header.inc');

echo '<FORM NAME="form" ACTION=' . $_SERVER['PHP_SELF'] .'?' .SID . ' METHOD=POST>';

// bowikaxu -just view closed work orders
$ClosedOrOpen = 1;

If (isset($_POST['ResetPart'])){
     unset($_REQUEST['SelectedStockItem']);
}

if (!isset($_POST['requiredby']) OR $_POST['requiredby'] == ''){
	$_POST['requiredby'] = Date('Y-m-d',Mktime(0,0,0,Date('m')-1,Date('d'),Date('Y')));
}

if (!isset($_SESSION['CustomerID']) OR $_SESSION['CustomerID']==''){
	prnMsg('Debe seleccionar un cliente primero.<br>Puede hacerlo haciendo <a href="SelectCustomer.php?'.SID.'">click aqui</a>.','error');
	include('includes/footer.inc');
	exit;
}
//detalles del cliente
echo '<CENTER><FONT SIZE=3>' . _('Customer') . ' :<B> ' . $_SESSION['CustomerID'] . ' - ' . $CustomerName . '</B> ' . _('has been selected') . '.</FONT></CENTER><BR>';
// PROCESO DE FACTURACION
if(isset($_POST['INVOICE']) AND $_POST['INVOICE']!=''){

	$error = 0;
	if(count($_POST['invoice'])<=0){
		prnMsg ('No se ha seleccionado ninguna Orden de Produccion para Facturar.','error');
		$error++;
	}
	// no errors, continue
	if($error==0){
		
		$sql = "SELECT branchcode, debtorno, brname FROM custbranch WHERE
				debtorno = '".$_SESSION['CustomerID']."'
				ORDER BY brname";
		$br_res = DB_query($sql,$db);
		
		include('includes/DefineCartClass.php');
		include('includes/GetPrice.inc');
		include('includes/rh_GetDiscount.inc');

		echo "<INPUT TYPE=HIDDEN NAME='INVOICE' VALUE='"._('Invoice')."'>";
		echo _('Customer Branch').": <SELECT NAME='CustBranch'>";
		while($br_info = DB_fetch_array($br_res)){
		
			if($_POST['CustBranch']==$br_info['branchcode']){
				echo "<OPTION SELECTED VALUE='".$br_info['branchcode']."'>".$br_info['brname'];
			}else {
				echo "<OPTION VALUE='".$br_info['branchcode']."'>".$br_info['brname'];
			}
		}
		echo "</SELECT><BR>";
		// reset branches
		// if no branch selected, set first result
		mysql_data_seek($br_res,0);
		$br_info = DB_fetch_array($br_res);
		if(!isset($_POST['CustBranch'])){
			$_POST['CustBranch'] = $br_info['branchcode'];
		}
		
		// PREPARAR CLIENTE Y SUCURSAL DEL PEDIDO
		if (isset($_SESSION['Items'])){
			unset ($_SESSION['Items']->LineItems);
			$_SESSION['Items']->ItemsOrdered=0;
			unset ($_SESSION['Items']);
		}
		$_SESSION['ExistingOrder']=0;
		$_SESSION['Items'] = new cart;
		
		// OBTENER DETALLES DEL CLIENTE ---------------------
		$_POST['Select'] = $_SESSION['CustomerID'];
		
		// Now check to ensure this account is not on hold */
		$sql = "SELECT debtorsmaster.name,
				holdreasons.dissallowinvoices,
				debtorsmaster.salestype,
				salestypes.sales_type,
				debtorsmaster.currcode,
				debtorsmaster.customerpoline
			FROM debtorsmaster,
				holdreasons,
				salestypes
			WHERE debtorsmaster.salestype=salestypes.typeabbrev
			AND debtorsmaster.holdreason=holdreasons.reasoncode
			AND debtorsmaster.debtorno = '" . $_POST['Select'] . "'";
	
		$ErrMsg = _('The details of the customer selected') . ': ' .  $_POST['Select'] . ' ' . _('cannot be retrieved because');
		$DbgMsg = _('The SQL used to retrieve the customer details and failed was') . ':';
		$result =DB_query($sql,$db,$ErrMsg,$DbgMsg);
	
		$myrow = DB_fetch_row($result);
		if ($myrow[1] != 1){
			if ($myrow[1]==2){
				prnMsg(_('The') . ' ' . $myrow[0] . ' ' . _('account is currently flagged as an account that needs to be watched. Please contact the credit control personnel to discuss'),'warn');
			}
			$_SESSION['Items']->DebtorNo=$_POST['Select'];
			$_SESSION['RequireCustomerSelection']=0;
			$_SESSION['Items']->CustomerName = $myrow[0];
	
	# the sales type determines the price list to be used by default the customer of the user is
	# defaulted from the entry of the userid and password.
			$_SESSION['Items']->DefaultSalesType = $myrow[2];
			$_SESSION['Items']->SalesTypeName = $myrow[3];
			$_SESSION['Items']->DefaultCurrency = $myrow[4];
			$_SESSION['Items']->DefaultPOLine = $myrow[5];
	
			$_SESSION['Items']->Branch = $_POST['CustBranch'];
	# the branch was also selected from the customer selection so default the delivery details from the customer branches table CustBranch. The order process will ask for branch details later anyway
	
			$sql = "SELECT custbranch.brname,
					custbranch.braddress1,
					custbranch.braddress2,
					custbranch.braddress3,
					custbranch.braddress4,
					custbranch.braddress5,
					custbranch.braddress6,
					custbranch.phoneno,
					custbranch.email,
					custbranch.defaultlocation,
					custbranch.defaultshipvia,
					custbranch.deliverblind,
	                custbranch.specialinstructions,
	                custbranch.estdeliverydays
				FROM custbranch
				WHERE custbranch.branchcode='" . $_SESSION['Items']->Branch . "'
				AND custbranch.debtorno = '" . $_POST['Select'] . "'";
	
			$ErrMsg = _('The customer branch record of the customer selected') . ': ' . $_POST['Select'] . ' ' . _('cannot be retrieved because');
			$DbgMsg = _('SQL used to retrieve the branch details was') . ':';
			$result =DB_query($sql,$db,$ErrMsg,$DbgMsg);
	
			if (DB_num_rows($result)==0){
	
				prnMsg(_('The branch details for branch code') . ': ' . $_SESSION['Items']->Branch . ' ' . _('against customer code') . ': ' . $_POST['Select'] . ' ' . _('could not be retrieved') . '. ' . _('Check the set up of the customer and branch'),'error');
				if ($debug==1){
					echo '<BR>' . _('The SQL that failed to get the branch details was') . ':<BR>' . $sql;
				}
				include('includes/footer.inc');
				exit;
			}
	
			$myrow = DB_fetch_row($result);
			$_SESSION['Items']->DeliverTo = $myrow[0];
			$_SESSION['Items']->DelAdd1 = $myrow[1];
			$_SESSION['Items']->DelAdd2 = $myrow[2];
			$_SESSION['Items']->DelAdd3 = $myrow[3];
			$_SESSION['Items']->DelAdd4 = $myrow[4];
			$_SESSION['Items']->DelAdd5 = $myrow[5];
			$_SESSION['Items']->DelAdd6 = $myrow[6];
			$_SESSION['Items']->PhoneNo = $myrow[7];
			$_SESSION['Items']->Email = $myrow[8];
			$_SESSION['Items']->Location = $myrow[9];
			$_SESSION['Items']->ShipVia = $myrow[10];
			$_SESSION['Items']->DeliverBlind = $myrow[11];
			$_SESSION['Items']->SpecialInstructions = $myrow[12];
			$_SESSION['Items']->DeliveryDays = $myrow[13];
		}else {
			prnMsg(_('The') . ' ' . $myrow[0] . ' ' . _('account is currently on hold please contact the credit control personnel to discuss'),'warn');
			exit;
		}
		// FIN DETALLES DEL CLIENTE
		// -------------------------------------------------------------------------------------
		// ARTICULOS
		echo "<TABLE BORDER=0 CELLSPACING=2 ALIGN='CENTER'>
		<TR>
		<TD class='tableheader' COLSPAN=5 ALIGN='CENTER'><STRONG>"._('Detail')."</STRONG></TD>
		</TR>";
		$tableheader = "<TR>
						<TD class='tableheader'>"._('Item Code')."</TD>
						<TD class='tableheader'>"._('Quantity Required')."</TD>
						<TD class='tableheader'>"._('Quantity Received')."</TD>
						<TD class='tableheader'>"._('Standard Cost')."</TD>
						<TD class='tableheader'>"._('Total Received')."</TD>
						</TR>";
		echo $tableheader;
		$total_final = 0;
		foreach($_POST['invoice'] AS $key => $wonumber){
			// TODO: seria bueno poner verificacion que si este realmente cerrado el work order
			$sql = "SELECT stockid,
							qtyreqd,
							qtyrecd,
							stdcost
					FROM woitems
					WHERE wo = ".$wonumber."";
			$itm_res = DB_query($sql,$db,'Imposible obtener detalles del Work Order #'.$wonumber,'SQL:');
			while($itm_info = DB_fetch_array($itm_res)){
				echo "<TR>
					<TD ALIGN='LEFT'>".$itm_info['stockid']."</TD>
					<TD ALIGN='RIGHT'>".number_format($itm_info['qtyreqd'],2)."</TD>
					<TD ALIGN='RIGHT'>".number_format($itm_info['qtyrecd'],2)."</TD>
					<TD ALIGN='RIGHT'>".number_format($itm_info['stdcost'],3)."</TD>
					<TD ALIGN='RIGHT'>".number_format($itm_info['stdcost']*$itm_info['qtyrecd'],2)."</TD>
					</TR>";
				$total_final += ($itm_info['stdcost']*$itm_info['qtyrecd']);
				$NewItem = $itm_info['stockid'];
				$NewItemQty = $itm_info['qtyrecd'];
				$NewItemDue = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', $_SESSION['Items']->DeliveryDays);
				$NewPOLine = 0;
				
				if(!Is_Date($NewItemDue)) {
								prnMsg(_('An invalid date entry was made for ') . ' ' . $NewItem . ' ' . _('The date entry') . ' ' . $NewItemDue . ' ' . ('must be in the format') . ' ' . $_SESSION['DefaultDateFormat'],'warn');
							//Attempt to default the due date to something sensible?
							$NewItemDue = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', $_SESSION['Items']->DeliveryDays);
						}
						/*Now figure out if the item is a kit set - the field MBFlag='K'*/
						$sql = "SELECT stockmaster.mbflag
								FROM stockmaster
								WHERE stockmaster.stockid='". $NewItem ."'";
			
						$ErrMsg = _('Could not determine if the part being ordered was a kitset or not because');
						$KitResult = DB_query($sql, $db,$ErrMsg,$DbgMsg);
			
						// bowikaxu realhost - bug fix March 2008
						if (DB_num_rows($KitResult)==0 AND strlen($NewItem)>0){
							prnMsg( _('The item code') . ' ' . $NewItem . ' ' . _('could not be retrieved from the database and has not been added to the order'),'warn');
						} elseif ($myrow=DB_fetch_array($KitResult)){
							if ($myrow['mbflag']=='K'){	/*It is a kit set item */
								$sql = "SELECT bom.component,
										bom.quantity
										FROM bom
										WHERE bom.parent='" . $NewItem . "'
										AND bom.effectiveto > '" . Date("Y-m-d") . "'
										AND bom.effectiveafter < '" . Date('Y-m-d') . "'";
			
								$ErrMsg =  _('Could not retrieve kitset components from the database because') . ' ';
								$KitResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);
			
								$ParentQty = $NewItemQty;
								while ($KitParts = DB_fetch_array($KitResult,$db)){
									$NewItem = $KitParts['component'];
									$NewItemQty = $KitParts['quantity'] * $ParentQty;
									//$NewItemPrice = $price;
									$NewItemDescription = $narrative;
									include('includes/SelectOrderItems_IntoCart.inc');
									$_SESSION['Items']->LineItems[($_SESSION['Items']->LineCounter -1)]->StandardCost = $itm_info['stdcost'];
								}
							} else { /*Its not a kit set item*/
								$NewItemPrice = $price;
								$NewItemDescription = $narrative;
								include('includes/SelectOrderItems_IntoCart.inc');
								$_SESSION['Items']->LineItems[($_SESSION['Items']->LineCounter -1)]->StandardCost = $itm_info['stdcost'];
							}
					}
			}
		}
		echo "
		<TR>
		<TD COLSPAN=4></TD>
		<TD class='tableheader' align='right'><STRONG>".number_format($total_final,2)."</STRONG></TD>
		</TR>
		</TABLE>";
		
		//echo "<CENTER><INPUT TYPE='SUBMIT' NAME='INVOICE2' VALUE='"._('Invoice')."'></CENTER>";
		
		echo "<HR>";
		//print_r($_SESSION["Items"]);
		echo "<HR>";
		
		// FIN ARTICULOS
		echo "</FORM>";
		//prnMsg('Generando Pedido ... ','success');
		//echo "<META HTTP-EQUIV='refresh' content='1;URL=SelectOrderItems.php?".SID."'>";
		//print_r($_SESSION['Items']);
		prnMsg("<br>Verifique el detalle y despues<br>
		haga <a href='SelectOrderItems.php?".SID."'>click aqui</a> para generar el pedido.",'info');
		include('includes/footer.inc');
		exit;
	}
	unset($_POST['INVOICE']);
}
// TERMINA PROCESO DE FACTURACION

If (isset($_REQUEST['WO']) AND $_REQUEST['WO']!='') {
	$_REQUEST['WO'] = trim($_REQUEST['WO']);
	if (!is_numeric($_REQUEST['WO'])){
		  prnMsg(_('The work order number entered MUST be numeric'),'warn');
		  unset ($_REQUEST['WO']);
		  include('includes/footer.inc');
		  exit;
	} else {
		echo _('Work Order Number') . ' - ' . $_REQUEST['WO'];
	}
} else {
	if (isset($_REQUEST['SelectedStockItem'])) {
		 echo _('for the item') . ': ' . $_REQUEST['SelectedStockItem'] . ' ' . _('and') . " <input type=hidden name='SelectedStockItem' value='" . $_REQUEST['SelectedStockItem'] . "'>";
	}
}

if (isset($_POST['SearchParts'])){

	If ($_POST['Keywords'] AND $_POST['StockCode']) {
		echo _('Stock description keywords have been used in preference to the Stock code extract entered');
	}
	If ($_POST['Keywords']) {
		//insert wildcard characters in spaces
		$i=0;
		$SearchString = '%';
		while (strpos($_POST['Keywords'], ' ', $i)) {
			$wrdlen=strpos($_POST['Keywords'],' ',$i) - $i;
			$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . '%';
			$i=strpos($_POST['Keywords'],' ',$i) +1;
		}
		$SearchString = $SearchString . substr($_POST['Keywords'],$i).'%';

		$SQL = "SELECT stockmaster.stockid,
				stockmaster.description,
				SUM(locstock.quantity) AS qoh,
				stockmaster.units
			FROM stockmaster,
				locstock
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.description " . LIKE . " '" . $SearchString . "'
			AND stockmaster.categoryid='" . $_POST['StockCat']. "'
			AND stockmaster.mbflag='M'
			GROUP BY stockmaster.stockid,
				stockmaster.description,
				stockmaster.units
			ORDER BY stockmaster.stockid";

	 } elseif (isset($_POST['StockCode'])){
		$SQL = "SELECT stockmaster.stockid,
				stockmaster.description,
				sum(locstock.quantity) as qoh,
				stockmaster.units
			FROM stockmaster,
				locstock
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.stockid " . LIKE . " '%" . $_POST['StockCode'] . "%'
			AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
			AND stockmaster.mbflag='M'
			GROUP BY stockmaster.stockid,
				stockmaster.description,
				stockmaster.units
			ORDER BY stockmaster.stockid";

	 } elseif (!isset($_POST['StockCode']) AND !isset($_POST['Keywords'])) {
		$SQL = "SELECT stockmaster.stockid,
				stockmaster.description,
				sum(locstock.quantity) as qoh,
				stockmaster.units
			FROM stockmaster,
				locstock
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.categoryid='" . $_POST['StockCat'] ."'
			AND stockmaster.mbflag='M'
			GROUP BY stockmaster.stockid,
				stockmaster.description,
				stockmaster.units
			ORDER BY stockmaster.stockid";
	 }

	$ErrMsg =  _('No items were returned by the SQL because');
	$DbgMsg = _('The SQL used to retrieve the searched parts was');
	$StockItemsResult = DB_query($SQL,$db,$ErrMsg,$DbgMsg);
}

if (isset($_POST['StockID'])){
	$StockID = trim(strtoupper($_POST['StockID']));
} elseif (isset($_GET['StockID'])){
	$StockID = trim(strtoupper($_GET['StockID']));
}

if (!isset($StockID)) {

     /* Not appropriate really to restrict search by date since may miss older
     ouststanding orders
	$OrdersAfterDate = Date('d/m/Y',Mktime(0,0,0,Date('m')-2,Date('d'),Date('Y')));
     */

	if ($_REQUEST['WO']=='' OR !$_REQUEST['WO']){

		echo _('Work Order number') . ": <INPUT type=text name='WO' MAXLENGTH =8 SIZE=9>&nbsp " . _('Processing at') . ":<SELECT name='StockLocation'> ";

		$sql = 'SELECT loccode, locationname FROM locations';

		$resultStkLocs = DB_query($sql,$db);

		while ($myrow=DB_fetch_array($resultStkLocs)){
			if (isset($_POST['StockLocation'])){
				if ($myrow['loccode'] == $_POST['StockLocation']){
				     echo "<OPTION SELECTED Value='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
				} else {
				     echo "<OPTION Value='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
				}
			} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
				 echo "<OPTION SELECTED Value='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
			} else {
				 echo "<OPTION Value='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
			}
		}

		echo '</SELECT> &nbsp;&nbsp;';
		
		// bowikaxu - search by required date
		echo _('Required By').' '._('After').": <INPUT TYPE='text' NAME='requiredby' VALUE='".$_POST['requiredby']."' size=12 maxlength=10>
		<a href=\"#\" onclick=\"form.requiredby.value='';cal.select(document.forms['form'].requiredby,'requiredby_anchor','yyyy-M-d');
                      return false;\" name=\"from_date_anchor\" id=\"requiredby_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>
		&nbsp;&nbsp;";
		
		echo "<INPUT TYPE=SUBMIT NAME='SearchOrders' VALUE='" . _('Search') . "'>";
    	echo '&nbsp;&nbsp;<a href="' . $rootpath . '/WorkOrderEntry.php?' . SID . '">' . _('New Work Order') . '</a>';
	}

	$SQL='SELECT categoryid,
			categorydescription
		FROM stockcategory
		ORDER BY categorydescription';

	$result1 = DB_query($SQL,$db);

	echo '<HR>
		<FONT SIZE=1>' . _('To search for work orders for a specific item use the item selection facilities below') . "</FONT>
		<INPUT TYPE=SUBMIT NAME='SearchParts' VALUE='" . _('Search Items Now') . "'>
		<INPUT TYPE=SUBMIT NAME='ResetPart' VALUE='" . _('Show All') . "'>
      <TABLE>
      	<TR>
      		<TD><FONT SIZE=1>" . _('Select a stock category') . ":</FONT>
      			<SELECT NAME='StockCat'>";

	while ($myrow1 = DB_fetch_array($result1)) {
		echo "<OPTION VALUE='". $myrow1['categoryid'] . "'>" . $myrow1['categorydescription'];
	}

      echo '</SELECT>
      		<TD><FONT SIZE=1>' . _('Enter text extract(s) in the description') . ":</FONT></TD>
      		<TD><INPUT TYPE='Text' NAME='Keywords' SIZE=20 MAXLENGTH=25></TD>
	</TR>
      	<TR><TD></TD>
      		<TD><FONT SIZE 3><B>" . _('OR') . ' </B></FONT><FONT SIZE=1>' . _('Enter extract of the Stock Code') . "</B>:</FONT></TD>
      		<TD><INPUT TYPE='Text' NAME='StockCode' SIZE=15 MAXLENGTH=18></TD>
      	</TR>
      </TABLE>
      <HR>";

If (isset($StockItemsResult)) {

	echo '<TABLE CELLPADDING=2 COLSPAN=7 BORDER=2>';
	$TableHeader = "<TR>
				<TD class='tableheader'>" . _('Code') . "</TD>
				<TD class='tableheader'>" . _('Description') . "</TD>
				<TD class='tableheader'>" . _('On Hand') . "</TD>
				<TD class='tableheader'>" . _('Units') . "</TD>
			</TR>";
	echo $TableHeader;

	$j = 1;
	$k=0; //row colour counter

	while ($myrow=DB_fetch_array($StockItemsResult)) {

		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k++;
		}

		printf("<td><INPUT TYPE=SUBMIT NAME='SelectedStockItem' VALUE='%s'</td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td>%s</td>
			</tr>",
			$myrow['stockid'],
			$myrow['description'],
			$myrow['qoh'],
			$myrow['units']);

		$j++;
		If ($j == 12){
			$j=1;
			echo $TableHeader;
		}
//end of page full new headings if
	}
//end of while loop

	echo '</TABLE>';

}
//end if stock search results to show
  else {

	if (isset($_REQUEST['WO']) && $_REQUEST['WO'] !='') {
			$SQL = "SELECT workorders.wo,
					woitems.stockid,
					stockmaster.description,
					woitems.qtyreqd,
					woitems.qtyrecd,
					workorders.startdate
					FROM workorders
					INNER JOIN woitems ON workorders.wo=woitems.wo
					INNER JOIN stockmaster ON woitems.stockid=stockmaster.stockid
					WHERE workorders.closed=" . $ClosedOrOpen . "
					AND workorders.wo=". $_REQUEST['WO'] ."
					AND requiredby >= '".$_POST['requiredby']."'
					ORDER BY workorders.wo,
							 woitems.stockid";
	} else {
	      /* $DateAfterCriteria = FormatDateforSQL($OrdersAfterDate); */

			if (isset($_REQUEST['SelectedStockItem'])) {
				$SQL = "SELECT workorders.wo,
					woitems.stockid,
					stockmaster.description,
					woitems.qtyreqd,
					woitems.qtyrecd,
					workorders.startdate
					FROM workorders
					INNER JOIN woitems ON workorders.wo=woitems.wo
					INNER JOIN stockmaster ON woitems.stockid=stockmaster.stockid
					WHERE workorders.closed=" . $ClosedOrOpen . "
					AND woitems.stockid='". $_REQUEST['SelectedStockItem'] ."'
					AND workorders.loccode='" . $_POST['StockLocation'] . "'
					AND requiredby >= '".$_POST['requiredby']."'
					ORDER BY workorders.wo,
							 woitems.stockid";
			} else {
				$SQL = "SELECT workorders.wo,
					woitems.stockid,
					stockmaster.description,
					woitems.qtyreqd,
					woitems.qtyrecd,
					workorders.startdate
					FROM workorders
					INNER JOIN woitems ON workorders.wo=woitems.wo
					INNER JOIN stockmaster ON woitems.stockid=stockmaster.stockid
					WHERE workorders.closed=" . $ClosedOrOpen . "
					AND workorders.loccode='" . $_POST['StockLocation'] . "'
					AND requiredby >= '".$_POST['requiredby']."'
					ORDER BY workorders.wo,
							 woitems.stockid";
			}
	} //end not order number selected

	$ErrMsg = _('No works orders were returned by the SQL because');
	$WorkOrdersResult = DB_query($SQL,$db,$ErrMsg);

	/*show a table of the orders returned by the SQL */

	echo '<TABLE CELLPADDING=2 COLSPAN=7 WIDTH=100%>';


	$tableheader = "<TR>
				<TD class='tableheader'>" . _('Invoice') . "</TD>
				<TD class='tableheader'>" . _('Status') . "</TD>
				<TD class='tableheader'>" . _('Costing') . "</TD>
				<TD class='tableheader'>" . _('Item') . "</TD>
				<TD class='tableheader'>" . _('Quantity Required') . "</TD>
				<TD class='tableheader'>" . _('Quantity Received') . "</TD>
				<TD class='tableheader'>" . _('Quantity Outstanding') . "</TD>
				<TD class='tableheader'>" . _('Date') . "</TD>
				</TR>";
	echo "<TR>
		<TD COLSPAN=2>
			<INPUT TYPE=SUBMIT NAME='INVOICE' VALUE='"._('Invoice')."'>
		</TD>
		</TR>";
	echo $tableheader;

	$j = 1;
	$k=0; //row colour counter
	while ($myrow=DB_fetch_array($WorkOrdersResult)) {

		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k++;
		}

		$Invoice_WO = "[".$myrow['wo']."]<INPUT TYPE='checkbox' NAME='invoice[]' VALUE='".$myrow['wo']."'>";
		$Status_WO = $rootpath . '/WorkOrderStatus.php?' . SID . '&WO=' .$myrow['wo'] . '&StockID=' . $myrow['stockid'];
		$Costing_WO = $rootpath . '/WorkOrderCosting.php?' . SID . '&WO=' .$myrow['wo'];

		$FormatedDate = ConvertSQLDate($myrow['startdate']);

		printf("<td>%s</td>
				<td><A TARGET='_blank' HREF='%s'>" . _('Status') . "</A></td>
				<td><A TARGET='_blank' HREF='%s'>" . _('Costing') . "</A></td>
				<td>%s - %s</td>
				<td align=right>%s</td>
				<td align=right>%s</td>
				<td align=right>%s</td>
				<td>%s</td>
				</tr>",
				$Invoice_WO,
				$Status_WO,
				$Costing_WO,
				$myrow['stockid'],
				$myrow['description'],
				$myrow['qtyreqd'],
				$myrow['qtyrecd'],
				$myrow['qtyreqd']-$myrow['qtyrecd'],
				$FormatedDate);

		$j++;
		If ($j == 12){
			$j=1;
			echo $tableheader;
		}
	//end of page full new headings if
	}
	//end of while loop
	echo "<TR>
		<TD COLSPAN=2>
			<INPUT TYPE=SUBMIT NAME='INVOICE' VALUE='"._('Invoice')."'>
		</TD>
		</TR>";
	echo '</TABLE>';
}

?>
</FORM>

<?php }

include('includes/footer.inc');
?>