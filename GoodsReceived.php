<?php

/* $Id: GoodsReceived.php 3900 2010-09-30 14:59:30Z tim_schofield $*/

/* $Revision: 1.44 $ */

$PageSecurity = 11;

/* Session started in header.inc for password checking and authorisation level check */
include('includes/DefinePOClass.php');
include('includes/DefineSerialItems.php');
include('includes/DefinePedimentoItems.php');
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
if (empty($identifier)) {
	$identifier='';
}
$title = _('Receive Purchase Orders');
include('includes/header.inc');

echo '<a href="'. $rootpath . '/PO_SelectOSPurchOrder.php?' . SID . '">' . _('Back to Purchase Orders'). '</a><br>';

if (isset($_GET['PONumber']) and $_GET['PONumber']<=0 and !isset($_SESSION['PO'])) {
	/* This page can only be called with a purchase order number for invoicing*/
	echo '<div class="centre"><a href= "' . $rootpath . '/PO_SelectOSPurchOrder.php?' . SID . '">'.
		_('Select a purchase order to receive').'</a></div>';
	echo '<br>'. _('This page can only be opened if a purchase order has been selected') . '. ' . _('Please select a purchase order first');
	include ('includes/footer.inc');
	exit;
} elseif (isset($_GET['PONumber']) AND !isset($_POST['Update'])) {
/*Update only occurs if the user hits the button to refresh the data and recalc the value of goods recd*/

	$_GET['ModifyOrderNumber'] = (int)$_GET['PONumber'];
	include('includes/PO_ReadInOrder.inc');
} elseif (isset($_POST['Update']) OR isset($_POST['ProcessGoodsReceived'])) {

/* if update quantities button is hit page has been called and ${$Line->LineNo} would have be
 set from the post to the quantity to be received in this receival*/

	foreach ($_SESSION['PO']->LineItems as $Line) {
		$RecvQty = $_POST['RecvQty_' . $Line->LineNo];
		if (!is_numeric($RecvQty)){
			$RecvQty = 0;
		}
		$_SESSION['PO']->LineItems[$Line->LineNo]->ReceiveQty = $RecvQty;
		if(isset($_REQUEST['codigoBarra'][$Line->LineNo])&&$_REQUEST['codigoBarra'][$Line->LineNo]!='')
			$_SESSION['PO']->LineItems[$Line->LineNo]->CodigosBarras=$_REQUEST['codigoBarra'][$Line->LineNo];
	}
}

$statussql="SELECT status FROM purchorders WHERE orderno='".$_SESSION['PO']->OrderNo . "'";
$statusresult=DB_query($statussql, $db);
$mystatusrow=DB_fetch_array($statusresult);
$Status=$mystatusrow['status'];

//rleal
//Ene 27 2011
//Se agrega lo de Impresa


if (($Status != PurchOrder::STATUS_PRINTED) && ($Status != 'Impresa' ) ){
	prnMsg( _('Purchase orders must have a status of Printed before they can be received').'.<br>'.
		_('Order number') . ' ' . $_GET['PONumber'] . ' ' . _('has a status of') . ' ' . _($Status), 'warn');
	include('includes/footer.inc');
	exit;
}



/* Always display quantities received and recalc balance for all items on the order */


echo '<div class="centre"><p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/supplier.png" title="' .
	_('Receive') . '" alt="">' . ' ' . _('Receive Purchase Order') . '';

echo ' : '. $_SESSION['PO']->OrderNo .' '. _('from'). ' ' . $_SESSION['PO']->SupplierName . ' </u></b></font></div>';
echo '<form action="' . $_SERVER['PHP_SELF'] . '?' . SID . '" method=post>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

if (!isset($_POST['ProcessGoodsReceived'])) {
	if (!isset($_POST['DefaultReceivedDate'])){
		$_POST['DefaultReceivedDate'] = Date($_SESSION['DefaultDateFormat']);
	}

	echo '<table class=selection><tr><td>'. _('Date Goods/Service Received'). ':</td><td><input type=text class=date alt="'.
		$_SESSION['DefaultDateFormat'] .'" maxlength=10 size=10 onChange="return isDate(this, this.value, '."'".
			$_SESSION['DefaultDateFormat']."'".')" name=DefaultReceivedDate value="' . $_POST['DefaultReceivedDate'] .
				'"></td></tr>

				<tr>
				<td>C&oacute;digo de barras:</td>
				<td><input type=text name=codigoBarras id=codigoBarras></td>
				</tr>
				</table><br>';

	?>

<!--Al introducir el codigo de barras asigna al producto la cantidad recibida y lo coloca la inicio de la tabla-->
	<script>
	 $(function(){
	 $('#codigoBarras').keypress(function(event) {
	    if (event.keyCode == 13) {
			event.preventDefault();
			CB=$('[CodigosBarra*=",'+$(this).val()+',"]');
			if(CB.length){
				CB.find('input[name^="RecvQty_"]').val(CB.find('input[name=PorRecibir]').val());
				codigo=CB.find('.Codigobarras');
				//CodigoB=codigo.val();
				codigo.val(//CodigoB+
				$(this).val()+',');
				tr=CB.parent().find('tr').first();
				CB.prependTo(CB.parent());
				tr.prependTo(tr.parent());
				CB.find('a').map(function(){
					entidades=this.search.split('&');
					encontrado=false;
					for(i in entidades){
						if(entidades[i].split('=')[1]=='Barras'){
							entidades[i]='Barras='+codigo;
							encontrado=true;
						}
					}
					if(!encontrado)entidades.push('Barras='+$(codigo).val());
					this.search=entidades.join('&');
				});
			}
			$(this).val('');
	    }
	});
});
	</script>

<!--Actualiza enl color de fondo de la tabla-->

<style>
	table.selection tr{
		background-color: #CCCCCC;
	}
	table.selection tr:nth-child(2n+3){
		background-color: #EEEEEE;
	}
</style>

	<?php

	echo '<table cellpadding=2 class=selection>
		<tr><th>' . _('Item Code') . '</th>
		<th>' . _('Description') . '</th>
		<th>' . _('Quantity') . '<br>' . _('Ordered') . '</th>
		<th>' . _('Units') . '</th>
		<th>' . _('Already Received') . '</th>
		<th>' . _('This Delivery') . '<br>' . _('Quantity') . '</th>';

	if ($_SESSION['ShowValueOnGRN']==1) {
		echo '<th>' . _('Price') . '</th><th>' . _('Total Value') . '<br>' . _('Received') . '</th>';
	}

	echo '<td>&nbsp;</td>
		</tr>';
	/*show the line items on the order with the quantity being received for modification */

	$_SESSION['PO']->total = 0;
}

$k=0; //row colour counter

if (count($_SESSION['PO']->LineItems)>0 and !isset($_POST['ProcessGoodsReceived'])){
	foreach ($_SESSION['PO']->LineItems as $LnItm) {


//selecciona el codigo de barras
		$barcode="select group_concat(barcode)barcode from stockmaster where stockid='".$LnItm->StockID."' or id_agrupador='".$LnItm->id_agrupador."' and id_agrupador<>'' ";
		$barcode_result=DB_query($barcode,$db);
		$barcode=DB_fetch_assoc($barcode_result,$db);

			echo '<tr class="" CodigosBarra=",'. htmlentities($barcode['barcode'] ).',">';

	/*  if ($LnItm->ReceiveQty==0){   /*If no quantities yet input default the balance to be received
			$LnItm->ReceiveQty = $LnItm->QuantityOrd - $LnItm->QtyReceived;
		}
	*/

	/*Perhaps better to default quantities to 0 BUT.....if you wish to have the receive quantities
	default to the balance on order then just remove the comments around the 3 lines above */

	//Setup & Format values for LineItem display

		$LineTotal = ($LnItm->ReceiveQty * $LnItm->Price );
		$_SESSION['PO']->total = $_SESSION['PO']->total + $LineTotal;
		$DisplayQtyOrd = number_format($LnItm->Quantity,$LnItm->DecimalPlaces);
		$DisplayQtyRec = number_format($LnItm->QtyReceived,$LnItm->DecimalPlaces);
		$DisplayLineTotal = number_format($LineTotal,2);
		$DisplayPrice = number_format($LnItm->Price,2);

		$uomsql="SELECT unitsofmeasure.unitname,
					conversionfactor,
					suppliersuom,
					max(effectivefrom)
				FROM purchdata
				LEFT JOIN unitsofmeasure
				ON purchdata.suppliersuom=unitsofmeasure.unitid
				WHERE supplierno='".$_SESSION['PO']->SupplierID."'
				AND stockid='".$LnItm->StockID."'
				GROUP BY unitsofmeasure.unitname";

		$uomresult=DB_query($uomsql, $db);
		if (DB_num_rows($uomresult)>0) {
			$uomrow=DB_fetch_array($uomresult);
			if (strlen($uomrow['unitname'])>0) {
				$uom=$uomrow['unitname'];
			} else {
				$uom=$LnItm->Units;
			}
			$conversionfactor=$uomrow['conversionfactor'];
		} else {
			$uom=$LnItm->Units;
			$conversionfactor=1;
		}



		//Now Display LineItem
		echo '<td>';
		$Codigo=$LnItm->StockID;
		if($LnItm->StockID==''&&$LnItm->id_agrupador!='')
			$Codigo='Id ('.$LnItm->id_agrupador.')'.
		'<input class="Codigobarras" type="hidden" name="codigoBarra['.$LnItm->LineNo.']" value="'.htmlentities($LnItm->CodigosBarras).',">';
		echo  $Codigo;
		echo '<input type=hidden name=PorRecibir value="'.($DisplayQtyOrd-$DisplayQtyRec).'">';
		echo '</td>';
		echo '<td>' . $LnItm->ItemDescription . '</td>';
		echo '<td class=number>' . $DisplayQtyOrd . '</td>';
		echo '<td>' . $LnItm->uom . '</td>';
		echo '<td class=number>' . $DisplayQtyRec . '</td>';
		echo '<td class=number>';
		$cons2="select rh_recepcion_scaneo.* "
              . " FROM"
              . " rh_recepcion_scaneo inner join rh_recepcion_dispositivos "
              . " ON"
              . " rh_recepcion_scaneo.macaddress_disp = rh_recepcion_dispositivos.macaddress "
              . " WHERE".
              " rh_recepcion_scaneo.quantity<>0 AND "
              . " podetailitem =".$LnItm->PODetailRec.
                ' order by grnno';
		$rescon=  DB_query($cons2, $db);
        if(DB_num_rows($rescon)>0){
        	echo '<a href="#" class="Abrir" value="'.$LnItm->PODetailRec.'" style="display: block; float:right;">+</a>';
		}
		if ($LnItm->Controlled == 1) {

			echo '<input type=hidden name="RecvQty_' . $LnItm->LineNo . '" value="' . $LnItm->ReceiveQty . '"><a href="GoodsReceivedControlled.php?' . SID . '&LineNo=' . $LnItm->LineNo . '">' . number_format($LnItm->ReceiveQty,$LnItm->DecimalPlaces) . '</a>';

		} else {
			echo '<input type=text class=number name="RecvQty_' . $LnItm->LineNo . '" maxlength=10 size=10 value="' . $LnItm->ReceiveQty . '">';
		}

            echo '</td>';
		if ($_SESSION['ShowValueOnGRN']==1) {
			echo '<td class=number>' . $DisplayPrice . '</td>';
			echo '<td class=number>' . $DisplayLineTotal . '</td>';
		}


		if ($LnItm->Controlled == 1) {
			if ($LnItm->Serialised==1){
				echo '<td><a href="GoodsReceivedControlled.php?' . SID . '&LineNo=' . $LnItm->LineNo . '">'.
					_('Enter Serial Nos'). '</a></td>';
			} else {
				echo '<td><a href="GoodsReceivedControlled.php?' . SID . '&LineNo=' . $LnItm->LineNo . '">'.
					_('Enter Batches'). '</a></td>';
			}
		}
        echo '<td><a href="GoodsReceivedPedimento.php?' . SID . '&LineNo=' . $LnItm->LineNo . '">'.
		_('Introducir Pedimentos Aduanales'). '</a></td>';
		echo '</tr>';

       if(DB_num_rows($rescon)>0){

            echo '<tr align="center" id="'.$LnItm->PODetailRec.'" style="display: none;">
                 <td colspan = 7>';
            echo'<table cellpadding="1" >';

            echo '<tr>';
            echo '<th>Id Usuario</th><th>Macaddress</th><th>Fecha Recibido</th><th>Codigo Barras</th><th>Estatus</th><th>Cantidad</th>';
            echo '</tr>';
            while($rowi= DB_fetch_assoc($rescon)){
				//$Series=unserialize($rowi['seriesDetalle']);
				echo'<tr class="OddTableRows">';
					echo '<td>'.$rowi['userid'].'</td>';
					echo '<td>'.$rowi['macaddress_disp'].'</td>';
					echo '<td>'.$rowi['datereceived'].'</td>';
					echo '<td>'.$rowi['barcode'].'</td>';
					echo '<td>'.(trim($rowi['grnno'])==''?'Pendiente':'Recibido').'</td>';
					echo '<td align="center">'.$rowi['quantity'].'</td>';
				echo'</tr>';
            }
            echo'</table>';
            echo '</td>
             </tr>' ;
	}
     }

	echo "<script>defaultControl(document.forms[0].RecvQty_$LnItm->LineNo);</script>";
$DisplayTotal = number_format($_SESSION['PO']->total,2);
if ($_SESSION['ShowValueOnGRN']==1) {
	echo '<tr><td colspan=7 class=number><b>' . _('Total value of goods received'). '</b></td>
		<td class=number><b>'. $DisplayTotal. '</b></td>
		</tr></table>';
} else {
	echo '</table>';
}
}//If count(LineItems) > 0
?>
<script>
    $(function(){
        $('.Abrir').click(function(){
            $('#'+$(this).attr('value')).toggle();
            return false;
        });
    })
</script>
<?php
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
					locstock WHERE locstock.stockid='" . $OrderLine->StockID . "'
					AND loccode= '" . $_SESSION['PO']->Location . "'";
			$CheckNegResult = DB_query($SQL,$db);
			$CheckNegRow = DB_fetch_row($CheckNegResult);
			if ($CheckNegRow[0]+$OrderLine->ReceiveQty<0){
				$NegativesFound=true;
				prnMsg(_('Receiving a negative quantity that results in negative stock is prohibited by the parameter settings. This delivery of stock cannot be processed until the stock of the item is corrected.'),'error',$OrderLine->StockID . ' Cannot Go Negative');
			}
		}

	}
}

if ($SomethingReceived==0 AND isset($_POST['ProcessGoodsReceived'])){ /*Then dont bother proceeding cos nothing to do ! */

	prnMsg(_('There is nothing to process') . '. ' . _('Please enter valid quantities greater than zero'),'warn');
	echo '<div class="centre"><input type=submit name=Update Value=' . _('Update') . '></div>';

} elseif ($NegativesFound){

	prnMsg(_('Negative stocks would result by processing a negative delivery - quantities must be changed or the stock quantity of the item going negative corrected before this delivery will be processed.'),'error');

	echo '<div class="centre"><input type=submit name=Update Value=' . _('Update') . '>';

}elseif ($DeliveryQuantityTooLarge==1 AND isset($_POST['ProcessGoodsReceived'])){

	prnMsg(_('Entered quantities cannot be greater than the quantity entered on the purchase invoice including the allowed over-receive percentage'). ' ' . '(' . $_SESSION['OverReceiveProportion'] .'%)','error');
	echo '<br>';
	prnMsg(_('Modify the ordered items on the purchase invoice if you wish to increase the quantities'),'info');
	echo '<div class="centre"><input type=submit name=Update Value=' . _('Update') . '>';

}  elseif (isset($_POST['ProcessGoodsReceived']) AND $SomethingReceived==1 AND $InputError == false){

/* SQL to process the postings for goods received... */
/* Company record set at login for information on GL Links and debtors GL account*/


	if ($_SESSION['CompanyRecord']==0){
		/*The company data and preferences could not be retrieved for some reason */
		prnMsg(_('The company information and preferences could not be retrieved') . ' - ' . _('see your system administrator') , 'error');
		include('includes/footer.inc');
		exit;
	}

/*Now need to check that the order details are the same as they were when they were read into the Items array. If they've changed then someone else must have altered them */
// Otherwise if you try to fullfill item quantities separately will give error.
	$SQL = "SELECT itemcode,
			glcode,
			quantityord,
			quantityrecd,
			qtyinvoiced,
			shiptref,
			jobref
		FROM purchorderdetails
		WHERE orderno='" . (int) $_SESSION['PO']->OrderNo . "'
		AND completed=0
		ORDER BY podetailitem";

	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not check that the details of the purchase order had not been changed by another user because'). ':';
	$DbgMsg = _('The following SQL to retrieve the purchase order details was used');
	$Result=DB_query($SQL,$db, $ErrMsg, $DbgMsg);

	$Changes=0;
	$LineNo=1;

	while ($myrow = DB_fetch_array($Result)) {

		if ($_SESSION['PO']->LineItems[$LineNo]->GLCode != $myrow['glcode'] OR
			$_SESSION['PO']->LineItems[$LineNo]->ShiptRef != $myrow['shiptref'] OR
			$_SESSION['PO']->LineItems[$LineNo]->JobRef != $myrow['jobref'] OR
			$_SESSION['PO']->LineItems[$LineNo]->QtyInv != $myrow['qtyinvoiced'] OR
			$_SESSION['PO']->LineItems[$LineNo]->StockID != $myrow['itemcode'] OR
			$_SESSION['PO']->LineItems[$LineNo]->Quantity != $myrow['quantityord'] OR
			$_SESSION['PO']->LineItems[$LineNo]->QtyReceived != $myrow['quantityrecd']) {


			prnMsg(_('This order has been changed or invoiced since this delivery was started to be actioned') . '. ' . _('Processing halted') . '. ' . _('To enter a delivery against this purchase order') . ', ' . _('it must be re-selected and re-read again to update the changes made by the other user'),'warn');

			if ($debug==1){
				echo '<table border=1>';
				echo '<tr><td>' . _('GL Code of the Line Item') . ':</td>
						<td>' . $_SESSION['PO']->LineItems[$LineNo]->GLCode . '</td>
						<td>' . $myrow['glcode'] . '</td></tr>';
				echo '<tr><td>' . _('ShiptRef of the Line Item') . ':</td>
					<td>' . $_SESSION['PO']->LineItems[$LineNo]->ShiptRef . '</td>
					<td>' . $myrow['shiptref'] . '</td></tr>';
				echo '<tr><td>' . _('Contract Reference of the Line Item') . ':</td>
					<td>' . $_SESSION['PO']->LineItems[$LineNo]->JobRef . '</td>
					<td>' . $myrow['jobref'] . '</td>
					</tr>';
				echo '<tr><td>' . _('Quantity Invoiced of the Line Item') . ':</td>
					<td>' . $_SESSION['PO']->LineItems[$LineNo]->QtyInv . '</td>
					<td>' . $myrow['qtyinvoiced'] . '</td></tr>';
				echo '<tr><td>' . _('Stock Code of the Line Item') . ':</td>
					<td>'. $_SESSION['PO']->LineItems[$LineNo]->StockID . '</td>
					<td>' . $myrow['itemcode'] . '</td></tr>';
				echo '<tr><td>' . _('Order Quantity of the Line Item') . ':</td>
					<td>' . $_SESSION['PO']->LineItems[$LineNo]->Quantity . '</td>
					<td>' . $myrow['quantityord'] . '</td></tr>';
				echo '<tr><td>' . _('Quantity of the Line Item Already Received') . ':</td>
					<td>' . $_SESSION['PO']->LineItems[$LineNo]->QtyReceived . '</td>
					<td>' . $myrow['quantityrecd'] . '</td></tr>';
				echo '</table>';
			}
			echo "<div class='centre'><a href='$rootpath/PO_SelectOSPurchOrder.php?" . SID . "'>".
				_('Select a different purchase order for receiving goods against').'</a></div>';

			echo "<div class='centre'><a href='$rootpath/GoodsReceived.php?" . SID . '&PONumber=' .
				$_SESSION['PO']->OrderNumber . '">'. _('Re-read the updated purchase order for receiving goods against'). '</a></div>';

			unset($_SESSION['PO']->LineItems);
			unset($_SESSION['PO']);
			unset($_POST['ProcessGoodsReceived']);
			include ("includes/footer.inc");
			exit;
		}
		$LineNo++;
	} /*loop through all line items of the order to ensure none have been invoiced */

	DB_free_result($Result);


/************************ BEGIN SQL TRANSACTIONS ************************/

	$Result = DB_Txn_Begin($db);
/*Now Get the next GRN - function in SQL_CommonFunctions*/
	$GRN = GetNextTransNo(25, $db);

	$PeriodNo = GetPeriod($_POST['DefaultReceivedDate'], $db);
	$_POST['DefaultReceivedDate'] = FormatDateForSQL($_POST['DefaultReceivedDate']);
	/*
	 * Recorremos el carrito para verificar que existan articulos con id_agrupador y meter cada
	 * articulo en una linea independiente
	 *
	 */
	$sqlx="SELECT stockmaster.description, ".
		"stockmaster.stockid, ".
		"stockmaster.units, ".
		"stockmaster.decimalplaces, ".
		"stockmaster.kgs, ".
		"stockmaster.netweight, ".
		"stockcategory.stockact, ".
		"chartmaster.accountname ".
		"FROM stockcategory, ".
		"chartmaster, ".
		"stockmaster ".
		"WHERE chartmaster.accountcode = stockcategory.stockact ".
		"AND stockcategory.categoryid = stockmaster.categoryid ".
		"AND stockmaster.stockid = '%s'";
	$Series=array();
	$Recepcion=array();
	//Creamos una linea nueva para el resto que no se va a recibir
	foreach ($_SESSION['PO']->LineItems as $linea=>$OrderLine){
		if($OrderLine->StockID==''&&isset($OrderLine->id_agrupador)&&$OrderLine->id_agrupador!=""){
			$Restante=($OrderLine->Quantity-$OrderLine->ReceiveQty);
			if($Restante>0&&$OrderLine->ReceiveQty>0){

				$_SESSION['PO']->LinesOnOrder++;
				$lineaNueva=$_SESSION['PO']->LinesOnOrder;

				$_SESSION['PO']->LineItems[$lineaNueva]=unserialize(serialize($_SESSION['PO']->LineItems[$OrderLine->LineNo]));
				$_SESSION['PO']->LineItems[$lineaNueva]->SerialItems=array();
				$_SESSION['PO']->LineItems[$lineaNueva]->StockID='';
				$_SESSION['PO']->LineItems[$lineaNueva]->Padre_PODetailRec=
				$_SESSION['PO']->LineItems[$lineaNueva]->PODetailRec;
				$_SESSION['PO']->LineItems[$lineaNueva]->PODetailRec=0;
				$_SESSION['PO']->LineItems[$lineaNueva]->ReceiveQty=0;
				$_SESSION['PO']->LineItems[$lineaNueva]->LineNo=$lineaNueva;
				$_SESSION['PO']->LineItems[$lineaNueva]->Quantity=$Restante;
			}
		}
	}

	//Buscamos la recepcion por numero de lote y lo acumulamos
	foreach ($_SESSION['PO']->LineItems as $linea=>$OrderLine){
		if($OrderLine->StockID==''&&isset($OrderLine->id_agrupador)&&$OrderLine->id_agrupador!=""){

			if(isset($OrderLine->SerialItems)&&is_array($OrderLine->SerialItems)&&count($OrderLine->SerialItems)>0)
			foreach($OrderLine->SerialItems as $lin=>$series){
				if(isset($series->stockid)&&$series->stockid!=''){
					if(!isset($Series[$series->stockid])){
						$Series[$series->stockid]=array();
						$Series[$series->stockid][$linea]=array();
						$Recepcion[$series->stockid]=array('qty'=>0);
					}

					$Series[$series->stockid][$linea][$lin]=$_SESSION['PO']->LineItems[$linea]->SerialItems[$lin];
					$Recepcion[$series->stockid]['qty']+=$_SESSION['PO']->LineItems[$linea]->SerialItems[$lin]->BundleQty;

					unset($_SESSION['PO']->LineItems[$linea]->SerialItems[$lin]);
				}
			}

		}
	}
	if(count($Series)>0){
		//Por cada serie encontrada lo asignamos a su linea correspondiente
		foreach($Series as $stockid=>$serie){
			$lineaNueva=false;
			foreach($serie as $linea=>$ser){
				if($_SESSION['PO']->LineItems[$linea]->StockID==''){
					foreach($ser as $lin=>$val)
						$_SESSION['PO']->LineItems[$linea]->SerialItems[$lin]=$val;
					$_SESSION['PO']->LineItems[$linea]->Quantity=
					$_SESSION['PO']->LineItems[$linea]->ReceiveQty=
						$Recepcion[$stockid]['qty'];
					$_SESSION['PO']->LineItems[$linea]->Padre_PODetailRec=0;
				}else{
					if(!$lineaNueva){
						$SQL=sprintf($sqlx,$stockid);
						$res=DB_Query($SQL,$db);
						$fila=DB_fetch_assoc($res);
						$_SESSION['PO']->LinesOnOrder++;
						$lineaNueva=$_SESSION['PO']->LinesOnOrder;

						$_SESSION['PO']->LineItems[$lineaNueva]=unserialize(serialize($_SESSION['PO']->LineItems[$linea]));
						$_SESSION['PO']->LineItems[$lineaNueva]->SerialItems=array();
						$_SESSION['PO']->LineItems[$lineaNueva]->StockID=$stockid;
						$_SESSION['PO']->LineItems[$lineaNueva]->Padre_PODetailRec=
						$_SESSION['PO']->LineItems[$lineaNueva]->PODetailRec;
						$_SESSION['PO']->LineItems[$lineaNueva]->PODetailRec=0;
						$_SESSION['PO']->LineItems[$lineaNueva]->GLCode= $fila['stockact'];
						$_SESSION['PO']->LineItems[$lineaNueva]->Units= $fila['units'];
						$_SESSION['PO']->LineItems[$lineaNueva]->LineNo=$lineaNueva;

						$_SESSION['PO']->LineItems[$lineaNueva]->Quantity=
						$_SESSION['PO']->LineItems[$lineaNueva]->ReceiveQty=
							$Recepcion[$stockid]['qty'];
					}
					foreach($ser as $lin=>$val)
						$_SESSION['PO']->LineItems[$lineaNueva]->SerialItems[$lin]=$val;
				}
			}
			if($_SESSION['PO']->LineItems[$linea]->StockID==''){
				$SQL=sprintf($sqlx,$stockid);
				$res=DB_Query($SQL,$db);
				$fila=DB_fetch_assoc($res);

				$_SESSION['PO']->LineItems[$linea]->Units= $fila['units'];
				$_SESSION['PO']->LineItems[$linea]->GLCode= $fila['stockact'];
				$_SESSION['PO']->LineItems[$linea]->StockID=$stockid;
				$SQL="update purchorderdetails set quantityord='".$_SESSION['PO']->LineItems[$linea]->Quantity.
					"' where podetailitem='".
					$_SESSION['PO']->LineItems[$linea]->PODetailRec."'";
				$res=DB_Query($SQL,$db);
				$_SESSION['PO']->LineItems[$linea]->CodigosBarras="";
			}
		}
	}
	foreach ($_SESSION['PO']->LineItems as $lineaNueva=>$OrderLine) 
		if(count($_SESSION['PO']->LineItems[$lineaNueva]->SerialItems)>0){
			$qty=0;
			foreach ($_SESSION['PO']->LineItems[$lineaNueva]->SerialItems as $lin => $value) {
				$qty=$value->BundleQty;
			}
			if($qty!=0&&$_SESSION['PO']->LineItems[$lineaNueva]->ReceiveQty!=$qty)
				$_SESSION['PO']->LineItems[$lineaNueva]->ReceiveQty=$qty;
	}
	foreach ($_SESSION['PO']->LineItems as $OrderLine) {
			//Si tiene movimientos tipo id agrupador pero no se ha definido el stockid continuamos con la siguiente linea
			if(
				isset($OrderLine->id_agrupador)
				&&$OrderLine->id_agrupador!=""
				&&$OrderLine->id_agrupador>0
				&&$OrderLine->ReceiveQty>0
				&&$OrderLine->StockID==''
			)continue;
			if($OrderLine->PODetailRec==0){//Si no tenemos podetailitem creamos una linea nueva
				$SQL="insert into purchorderdetails ";
				$SQL.="select '".
						$OrderLine->PODetailRec."' ".
						",orderno ".
						",'".
						$OrderLine->StockID."' ".
						",deliverydate ".
						",itemdescription ".
						",'".
						$OrderLine->GLCode."' ".
						",'0' ".
						",unitprice ".
						",actprice ".
						",stdcostunit ".
						",'".
						$OrderLine->Quantity."' ".
						",'0' ".
						",shiptref ".
						",jobref ".
						",0 ".
						",rh_comments ".
						",itemno ".
						",'".
						$OrderLine->Units."' ".
						",0 ".
						",package ".
						",pcunit ".
						",nw ".
						",suppliers_partno ".
						",gw ".
						",cuft ".
						",total_quantity ".
						",total_amount ".
						",assetid ".
						",conversionfactor ".
						",0 ".
						",'".$OrderLine->Padre_PODetailRec."' ".
						",descuento ".
						",rh_tax ".
						",id_agrupador ".
				" from purchorderdetails where podetailitem='".$OrderLine->Padre_PODetailRec."'";
				$res=DB_Query($SQL,$db);
				$OrderLine->PODetailRec=DB_Last_Insert_ID($db,'purchorderdetails','podetailitem');
				$OrderLine->CodigosBarras='';

			}
			if (isset($OrderLine->ReceiveQty)&& $OrderLine->ReceiveQty !=0 AND $OrderLine->ReceiveQty!='') {
				if(isset($OrderLine->id_agrupador)&&$OrderLine->id_agrupador!=''&&$OrderLine->PODetailRec!=0){
					$SQLUp="update purchorderdetails set itemcode ='".
					$OrderLine->StockID
					."', glcode='".
					$OrderLine->GLCode.
					"' where podetailitem='".
					$OrderLine->PODetailRec.
					"'";
					DB_Query($SQLUp,$db);
				}
			$LocalCurrencyPrice = ($OrderLine->Price / $_SESSION['PO']->ExRate);
/*Update SalesOrderDetails for the new quantity received and the standard cost used for postings to GL and recorded in the stock movements for FIFO/LIFO stocks valuations*/

			if ($OrderLine->StockID!='') { /*Its a stock item line */
				/*Need to get the current standard cost as it is now so we can process GL jorunals later*/
				$SQL = "SELECT materialcost + labourcost + overheadcost as stdcost
						FROM stockmaster
						WHERE stockid='" . $OrderLine->StockID . "'";
				$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The standard cost of the item being received cannot be retrieved because');
				$DbgMsg = _('The following SQL to retrieve the standard cost was used');
				$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

				$myrow = DB_fetch_row($Result);

				if ($OrderLine->QtyReceived==0){ //its the first receipt against this line
					$_SESSION['PO']->LineItems[$OrderLine->LineNo]->StandardCost = $myrow[0];
				}
				$CurrentStandardCost = $myrow[0];

				/*Set the purchase order line stdcostunit = weighted average / standard cost used for all receipts of this line
				 This assures that the quantity received against the purchase order line multiplied by the weighted average of standard
				 costs received = the total of standard cost posted to GRN suspense*/
				$_SESSION['PO']->LineItems[$OrderLine->LineNo]->StandardCost = (($CurrentStandardCost * $OrderLine->ReceiveQty) + ($_SESSION['PO']->LineItems[$OrderLine->LineNo]->StandardCost *$OrderLine->QtyReceived)) / ($OrderLine->ReceiveQty + $OrderLine->QtyReceived);

			} elseif ($OrderLine->QtyReceived==0 AND $OrderLine->StockID=="") {
				/*Its a nominal item being received */
				/*Need to record the value of the order per unit in the standard cost field to ensure GRN account entries clear */
				$_SESSION['PO']->LineItems[$OrderLine->LineNo]->StandardCost = $LocalCurrencyPrice;
			}

			if ($OrderLine->StockID=='') { /*Its a NOMINAL item line */
				$CurrentStandardCost = $_SESSION['PO']->LineItems[$OrderLine->LineNo]->StandardCost;
			}

			$SetPO="";
			$CodigosBarras=array();
			if(isset($OrderLine->CodigosBarras)&&$OrderLine->CodigosBarras!=''){
				if(isset($OrderLine->CodigosBarras)&&$OrderLine->CodigosBarras!=''){
					$CodigosBarras=array_flip(explode(",",$OrderLine->CodigosBarras));
					unset($CodigosBarras['']);
					$CodigosBarras=array_flip($CodigosBarras);
				}
				$sql="SELECT stockmaster.description,
							                stockmaster.stockid,
							                stockmaster.units,
							                stockmaster.decimalplaces,
							                stockmaster.kgs,
							                stockmaster.netweight,
							                stockcategory.stockact,
							                chartmaster.accountname
						                FROM stockcategory,
							                chartmaster,
							                stockmaster
						                WHERE chartmaster.accountcode = stockcategory.stockact
							                AND stockcategory.categoryid = stockmaster.categoryid
							                AND stockmaster.barcode in( '".(implode("','",$CodigosBarras)). "') and id_agrupador='".$OrderLine->id_agrupador."'";
					$ress=DB_query($sql,$db);
					$fila=DB_fetch_assoc($ress);
					if($fila['stockact']!='')
					$SetPO.=" glcode='".$fila['stockact']."', ";
					if($fila['stockid']!=''){
						$OrderLine->StockID=$fila['stockid'];
						$SetPO.=" itemcode='".DB_escape_string($fila['stockid'])."', ";
					}
					if($fila['units']!='')
					$SetPO.=" uom='".DB_escape_string($fila['units'])."', ";
			}
/*Now the SQL to do the update to the PurchOrderDetails */

			if ($OrderLine->ReceiveQty >= ($OrderLine->Quantity - $OrderLine->QtyReceived)){
				$SetPO.="quantityrecd = quantityrecd + '" . $OrderLine->ReceiveQty . "',";
				$SetPO.="completed=1,";
			} else {

				/*
				 * Recepcion incompleta, se crea una linea nueva por el restante, se actualiza la linea por el articulo
				 * que se recibe
				 *
				 */
				if(isset($OrderLine->CodigosBarras)&&$OrderLine->CodigosBarras!=''){
					//Creamos una linea nueva con el restante por recibir
					$SQL=
					"Insert into purchorderdetails select ".
					" 0".
					", orderno".
					", itemcode".
					", deliverydate".
					", itemdescription".
					", glcode ".
					", qtyinvoiced".
					", unitprice".
					", actprice".
					", '" . $_SESSION['PO']->LineItems[$OrderLine->LineNo]->StandardCost . "' ".
					", quantityord-".$OrderLine->ReceiveQty.
					", 0".
					", shiptref".
					", jobref".
					", 0 completed".
					", rh_comments".
					", itemno".
					", uom ".
					", subtotal_amount".
					", package".
					", pcunit".
					", nw".
					", suppliers_partno".
					", gw".
					", cuft".
					", total_quantity".
					", total_amount".
					", assetid".
					", conversionfactor".
					", 0 forzed".
					", detailsforzed".
					", descuento".
					", rh_tax".
					", id_agrupador ".
					" from purchorderdetails where podetailitem = '" . $OrderLine->PODetailRec . "'";
					$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The purchase order detail record could not be updated with the quantity received because');
					$DbgMsg = _('The following SQL to update the purchase order detail record was used');
					$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg, true);

					//Completamos la linea
					$SetPO.=" quantityrecd = '" . $OrderLine->ReceiveQty . "', ";
					$SetPO.=" quantityord = '" . $OrderLine->ReceiveQty . "', ";
					$SetPO.=" completed=1,";
				}else{
					$SetPO.=" quantityrecd = quantityrecd + '" . $OrderLine->ReceiveQty . "', ";
					$SetPO.=" completed=0,";
				}


			}

			$SQL = "UPDATE purchorderdetails SET
							$SetPO
							stdcostunit='" . $_SESSION['PO']->LineItems[$OrderLine->LineNo]->StandardCost . "'
					WHERE podetailitem = '" . $OrderLine->PODetailRec . "'";
			$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The purchase order detail record could not be updated with the quantity received because');
			$DbgMsg = _('The following SQL to update the purchase order detail record was used');
			$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg, true);
			$rh_recepcion_scaneo=false;

			$SQL="select count(*)t from rh_recepcion_scaneo where grnno is null and podetailitem='".$OrderLine->PODetailRec."'";
			$ress=DB_query($SQL,$db);
			$fila=DB_fetch_assoc($ress);
			$rh_recepcion_scaneo=$fila['t']>0;
			if ($OrderLine->StockID !=''){ /*Its a stock item so use the standard cost for the journals */
				$UnitCost = $CurrentStandardCost;
			} else {  /*otherwise its a nominal PO item so use the purchase cost converted to local currency */
				$UnitCost = $OrderLine->Price / $_SESSION['PO']->ExRate;
			}

/*Need to insert a GRN item */

			$SQL = "INSERT INTO grns (grnbatch,
						podetailitem,
						itemcode,
						itemdescription,
						deliverydate,
						qtyrecd,
						supplierid,
						stdcostunit)
				VALUES ('" . $GRN . "',
					'" . $OrderLine->PODetailRec . "',
					'" . $OrderLine->StockID . "',
					'" . DB_escape_string($OrderLine->ItemDescription) . "',
					'" . $_POST['DefaultReceivedDate'] . "',
					'" . $OrderLine->ReceiveQty . "',
					'" . $_SESSION['PO']->SupplierID . "',
					'" . $CurrentStandardCost *$OrderLine->ConversionFactor. "')";

			$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('A GRN record could not be inserted') . '. ' . _('This receipt of goods has not been processed because');
			$DbgMsg =  _('The following SQL to insert the GRN record was used');
			$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
			$grnno=DB_Last_Insert_ID($db,'grns','grnno');
			if(isset($OrderLine->CodigosBarras)&&$OrderLine->CodigosBarras!=''&& !$rh_recepcion_scaneo){
					$SQL="insert into rh_recepcion_scaneo(
				  `id`,
				  `podetailitem`,
				  `quantity`,
				  `userid`,
				  `longitud`,
				  `latitud`,
				  `macaddress_disp`,
				  `datereceived`,
				  `grnno`,
				  `barcode`
			) values(".
					"0".
					",".$OrderLine->PODetailRec.
					",".$OrderLine->ReceiveQty.
					",'".$_SESSION['UserID']."'".
					",0".
					",0".
					",''".
					",now()".
					','.$grnno.
					",'".DB_escape_string(implode(',',$CodigosBarras))."'".
					")";
			}else{
				$SQL="update rh_recepcion_scaneo set grnno='".$grnno."' where grnno is null and podetailitem='".$OrderLine->PODetailRec."'";
			}
			$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

			if ($OrderLine->StockID!=''){ /* if the order line is in fact a stock item */

/* Update location stock records - NB  a PO cannot be entered for a dummy/assembly/kit parts */

/* Need to get the current location quantity will need it later for the stock movement */
				$SQL="SELECT locstock.quantity
					FROM locstock
					WHERE locstock.stockid='" . $OrderLine->StockID . "'
					AND loccode= '" . $_SESSION['PO']->Location . "'";

				$Result = DB_query($SQL, $db);
				if (DB_num_rows($Result)==1){
					$LocQtyRow = DB_fetch_row($Result);
					$QtyOnHandPrior = $LocQtyRow[0];
				} else {
					/*There must actually be some error this should never happen */
					$QtyOnHandPrior = 0;
				}

				$sql="SELECT conversionfactor
					FROM purchdata
					WHERE supplierno='".$_SESSION['PO']->SupplierID."'
					AND stockid='".$OrderLine->StockID."'";
				$result=DB_query($sql, $db);
				if (DB_num_rows($result)>0) {
					$myrow=DB_fetch_array($result);
					$conversionfactor=$myrow['conversionfactor'];
				} else {
					$conversionfactor=1;
				}
				$OrderLine->ReceiveQty=$OrderLine->ReceiveQty*$conversionfactor;

				$SQL = "UPDATE locstock
					SET quantity = locstock.quantity + '" . $OrderLine->ReceiveQty . "'
					WHERE locstock.stockid = '" . $OrderLine->StockID . "'
					AND loccode = '" . $_SESSION['PO']->Location . "'";

				$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The location stock record could not be updated because');
				$DbgMsg =  _('The following SQL to update the location stock record was used');
				$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);


	/* If its a stock item still .... Insert stock movements - with unit cost */

				$SQL = "INSERT INTO stockmoves (stockid,
								type,
								transno,
								loccode,
								trandate,
								price,
								prd,
								reference,
								qty,
								standardcost,
								newqoh)
					VALUES (
						'" . $OrderLine->StockID . "',
						25,
						'" . $GRN . "',
						'" . $_SESSION['PO']->Location . "',
						'" . $_POST['DefaultReceivedDate'] . "',
						'" . $LocalCurrencyPrice / $conversionfactor . "',
						'" . $PeriodNo . "',
						'" . $_SESSION['PO']->SupplierID . " (" . $_SESSION['PO']->SupplierName . ") - " .$_SESSION['PO']->OrderNo . "',
						'" . $OrderLine->ReceiveQty . "',
						'" . $_SESSION['PO']->LineItems[$OrderLine->LineNo]->StandardCost . "',
						'" . ($QtyOnHandPrior + $OrderLine->ReceiveQty) . "'
						)";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('stock movement records could not be inserted because');
				$DbgMsg =  _('The following SQL to insert the stock movement records was used');
				$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

				/*Get the ID of the StockMove... */
				$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');
				/* Do the Controlled Item INSERTS HERE */


                //*****************************PEDIMENTOS ************************
                 //var_dump($OrderLine->SerialItems);
                if(count($OrderLine->PedimentoItems)>0){
                   	foreach($OrderLine->PedimentoItems as $Item){
                            $SQL = "SELECT COUNT(*) FROM stockpedimentoitems
									WHERE stockid='" . $OrderLine->StockID . "'
									AND loccode = '" . $_SESSION['PO']->Location . "'
									AND pedimentoid = '" . $Item->BundleRef . "'";
							$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('No se puede verificar la existencia de otros pedimentos');
							$DbgMsg =  _('Existen pedimentos pero no se han encontrado');
							$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
							$AlreadyExistsRow = DB_fetch_row($Result);
							if (strlen(trim($Item->BundleRef)) >0){
								if ($AlreadyExistsRow[0]>0){
									$SQL = "UPDATE stockpedimentoitems SET quantity = quantity + '" . $Item->BundleQty . "'";
									$SQL .= "WHERE stockid='" . $OrderLine->StockID . "'
											 AND loccode = '" . $_SESSION['PO']->Location . "'
											 AND pedimentoid = '" . $Item->BundleRef . "'";
								} else {
									$SQL = "INSERT INTO stockpedimentoitems (stockid,
												loccode,
												pedimentoid,".
												"quantity)
											VALUES ('" . $OrderLine->StockID . "',
												'" . $_SESSION['PO']->Location . "',
												'" . $Item->BundleRef . "',".
                                                "'" . $Item->BundleQty . "')";
								}
                                $ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
							    $DbgMsg = _('The following SQL to insert the serial stock movement records was used');
							    $Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
                        	}
							$SQL = "INSERT INTO stockpedimentomoves (stockmoveno,
											stockid,
											pedimentoid,
											moveqty)
									VALUES (
										'" . $StkMoveNo . "',
										'" . $OrderLine->StockID . "',
										'" . $Item->BundleRef . "',
										'" . $Item->BundleQty . "'
										)";
							$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
							$DbgMsg = _('The following SQL to insert the serial stock movement records was used');
							$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);


                }
                }
                //****************************************************************


				if ($OrderLine->Controlled ==1){
					foreach($OrderLine->SerialItems as $Item){
						if (trim($Item->BundleRef) != ""){
							$SQL = "SELECT expirationdate FROM stockserialitems
										WHERE stockid='" . $OrderLine->StockID . "'
										AND serialno = '" . $Item->BundleRef . "'";
								$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not check if a batch or lot stock item already exists because');
								$DbgMsg =  _('The following SQL to test for an already existing controlled but not serialised stock item was used');
								$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
								if($AlreadyExistsRow = DB_fetch_assoc($Result)){
									$expirationdate=$AlreadyExistsRow['expirationdate'];
									if(trim($expirationdate,'0-\\/ :')!='')
										$Item->BundleExpD=$expirationdate;
								}
							/* we know that StockItems return an array of SerialItem (s)
							 We need to add the StockSerialItem record and
							 The StockSerialMoves as well */
							//need to test if the controlled item exists first already
							$SQL = "SELECT COUNT(*) FROM stockserialitems
									WHERE stockid='" . $OrderLine->StockID . "'
									AND loccode = '" . $_SESSION['PO']->Location . "'
									AND serialno = '" . $Item->BundleRef . "'";
							$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not check if a batch or lot stock item already exists because');
							$DbgMsg =  _('The following SQL to test for an already existing controlled but not serialised stock item was used');
							$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
							$AlreadyExistsRow = DB_fetch_row($Result);

								if ($AlreadyExistsRow[0]>0){
									if ($OrderLine->Serialised == 1) {
										$SQL = "UPDATE stockserialitems SET quantity = '" . $Item->BundleQty . " ";
									} else {
										$SQL = "UPDATE stockserialitems SET quantity = quantity + '" . $Item->BundleQty . "'";
									}
									$SQL .= "WHERE stockid='" . $OrderLine->StockID . "'
											 AND loccode = '" . $_SESSION['PO']->Location . "'
											 AND serialno = '" . $Item->BundleRef . "'";
								} else {
									$SQL = "INSERT INTO stockserialitems (stockid,
												loccode,
												serialno,".
												//qualitytext,
												"quantity, expirationdate)
											VALUES ('" . $OrderLine->StockID . "',
												'" . $_SESSION['PO']->Location . "',
												'" . $Item->BundleRef . "',".
												//'',
                                                "'" . $Item->BundleQty . "',".
                                                "'".
												FormatDateForSQL($Item->BundleExpD)
											."')";
								}

								$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be inserted because');
								$DbgMsg =  _('The following SQL to insert the serial stock item records was used');
								$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

								$codigo=GenerarEtiqueta($OrderLine->id_agrupador,$OrderLine->StockID,$Item->BundleRef,FormatDateForSQL($Item->BundleExpD));
								AgregarImpresion($OrderLine->PODetailRec,$grnno,$codigo,$Item->BundleQty);

								/*Update fixed asset details */
								$sql="SELECT stocktype
										FROM stockcategory
										LEFT JOIN stockmaster
										ON stockcategory.categoryid=stockmaster.categoryid
										WHERE stockmaster.stockid='".$OrderLine->StockID."'";
								$result=DB_query($sql, $db);
								$myrow=DB_fetch_array($result);
								if ($myrow['stocktype']=='A') {
									$SQL = "INSERT INTO assetmanager
											VALUES (
												NULL,
												'" . $OrderLine->StockID . "',
												'" . $Item->BundleRef . "',
												'',
												'".$Item->BundleQty*$OrderLine->Price."',
												0,
												'". $_POST['DefaultReceivedDate']."',
												0)";
									$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be inserted because');
									$DbgMsg =  _('The following SQL to insert the serial stock item records was used');
									$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
								}

															/** end of handle stockserialitems records */

							/** now insert the serial stock movement **/
							$SQL = "INSERT INTO stockserialmoves (stockmoveno,
											stockid,
											serialno,
											moveqty)
									VALUES (
										'" . $StkMoveNo . "',
										'" . $OrderLine->StockID . "',
										'" . $Item->BundleRef . "',
										'" . $Item->BundleQty . "'
										)";
							$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
							$DbgMsg = _('The following SQL to insert the serial stock movement records was used');
							$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
						}//non blank BundleRef
					} //end foreach
				}
			} /*end of its a stock item - updates to locations and insert movements*/

/* If GLLink_Stock then insert GLTrans to debit the GL Code  and credit GRN Suspense account at standard cost*/
			if ($_SESSION['PO']->GLLink==1 AND $OrderLine->GLCode !=0){ /*GLCode is set to 0 when the GLLink is not activated this covers a situation where the GLLink is now active but it wasn't when this PO was entered */

/*first the debit using the GLCode in the PO detail record entry*/

				$SQL = "INSERT INTO gltrans (type,
								typeno,
								trandate,
								periodno,
								account,
								narrative,
								amount)
						VALUES (
							25,
							'" . $GRN . "',
							'" . $_POST['DefaultReceivedDate'] . "',
							'" . $PeriodNo . "',
							'" . $OrderLine->GLCode . "',
							'PO: " . DB_escape_string($_SESSION['PO']->OrderNo . " " . $_SESSION['PO']->SupplierID . " - " . $OrderLine->StockID
									. " - " . $OrderLine->ItemDescription . " x " . $OrderLine->ReceiveQty . " @ " .
										number_format($CurrentStandardCost,4)) . "',
							'" . $CurrentStandardCost * $OrderLine->ReceiveQty . "'
							)";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The purchase GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the purchase GLTrans record was used');
				$Result = DB_query($SQL,$db,$ErrMsg, $DbgMsg, true);

				/* If the CurrentStandardCost != UnitCost (the standard at the time the first delivery was booked in,  and its a stock item, then the difference needs to be booked in against the purchase price variance account */


	/*now the GRN suspense entry*/
				$SQL = "INSERT INTO gltrans (type,
								typeno,
								trandate,
								periodno,
								account,
								narrative,
								amount)
						VALUES (25,
							'" . $GRN . "',
							'" . $_POST['DefaultReceivedDate'] . "',
							'" . $PeriodNo . "',
							'" . $_SESSION['CompanyRecord']['grnact'] . "',
							'" . DB_escape_string(_('PO') . ': ' . $_SESSION['PO']->OrderNo . ' ' . $_SESSION['PO']->SupplierID . ' - ' .
										$OrderLine->StockID . ' - ' . $OrderLine->ItemDescription . ' x ' .
											$OrderLine->ReceiveQty . ' @ ' . number_format($UnitCost,4)) . "',
							'" . -$UnitCost * $OrderLine->ReceiveQty . "'
							)";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The GRN suspense side of the GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the GRN Suspense GLTrans record was used');
				$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg,true);

			} /* end of if GL and stock integrated and standard cost !=0 */
		} /*Quantity received is != 0 */
	} /*end of OrderLine loop */
	$completedsql="SELECT SUM(completed) as completedlines,
						COUNT(podetailitem) as alllines
					FROM purchorderdetails
					WHERE orderno='".$_SESSION['PO']->OrderNo . "'";
	$completedresult=DB_query($completedsql,$db);
	$mycompletedrow=DB_fetch_array($completedresult);
	$status=$mycompletedrow['alllines']-$mycompletedrow['completedlines'];

	if ($status==0) {
		$sql="SELECT stat_comment
				FROM purchorders
				WHERE orderno='".$_SESSION['PO']->OrderNo . "'";
		$result=DB_query($sql,$db);
		$myrow=DB_fetch_array($result);
		$comment=$myrow['stat_comment'];
		$date = date($_SESSION['DefaultDateFormat']);
		$StatusComment=$date.' - Order Completed'.'<br>'.$comment;
		$sql="UPDATE purchorders
				SET status='" . PurchOrder::STATUS_COMPLITED . "',
				stat_comment='".$StatusComment."'
				WHERE orderno='".$_SESSION['PO']->OrderNo . "'";
		$result=DB_query($sql,$db);
	}

//  	DB_Txn_Rollback($db);
	$Result = DB_Txn_Commit($db);
	$PONo = $_SESSION['PO']->OrderNo;
	unset($_SESSION['PO']->LineItems);
	unset($_SESSION['PO']);
	unset($_POST['ProcessGoodsReceived']);

	echo '<br><div class=centre>'. _('GRN number'). ' '. $GRN .' '. _('has been processed').'<br>';
	echo '<br><a href=PDFGrn.php?GRNNo='.$GRN .'&PONo='.$PONo.'>'. _('Print this Goods Received Note (GRN)').'</a><br><br>';
	echo "<a href='" . $rootpath . "/PO_SelectOSPurchOrder.php?" . SID . "'>" .
		_('Select a different purchase order for receiving goods against'). '</a></div>';
	echo '<span><a href="'.$rootpath.'/rh_imprimirCodigosBarras.php">'. _('Impresion Etiquetas'). '</a></span>';
/*end of process goods received entry */
	include('includes/footer.inc');
	exit;

} else { /*Process Goods received not set so show a link to allow mod of line items on order and allow input of date goods received*/

	echo "<br><div class='centre'><a href='" . $rootpath . "/PO_Items.php?=" . SID . "'>" . _('Modify Order Items'). '</a></div>';

	echo '<br><div class="centre"><input type=submit name=Update Value=' . _('Update') . '><p>';
	echo '<input type=submit name="ProcessGoodsReceived" Value="' . _('Process Goods Received') . '"></div>';
}

echo '</form>';

include('includes/footer.inc');

