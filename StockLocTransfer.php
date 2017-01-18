<?php
/* $Revision: 14 $ */
/* contributed by Chris Bice */

$PageSecurity = 11;
include('includes/session.inc');
$title = _('Inventory Location Transfer Shipment');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if(isset($_GET['transfer']) && $_GET['transfer']>=0){
	$_POST['transfer'] = $_GET['transfer'];
}

If (isset($_POST['Submit']) OR isset($_POST['EnterMoreItems'])){
/*Trap any errors in input */

	$InputError = False; /*Start off hoping for the best */
	$TotalItems = 0;
	//Make sure this Transfer has not already been entered... aka one way around the refresh & insert new records problem
	$result = DB_query("SELECT * FROM loctransfers WHERE reference='" . $_POST['Trf_ID'] . "'",$db);
	if (DB_num_rows($result)!=0){
		$InputError = true;
		$ErrorMessage = _('This transaction has already been entered') . '. ' . _('Please start over now').'<BR>';
		unset($_POST['submit']);
		unset($_POST['EnterMoreItems']);
		for ($i=$_POST['LinesCounter']-25;$i<$_POST['LinesCounter'];$i++){
			unset($_POST['StockID' . $i]);
			unset($_POST['StockQTY' . $i]);
		}
	}
	for ($i=$_POST['LinesCounter']-25;$i<$_POST['LinesCounter'];$i++){

		$_POST['StockID' . $i]=trim(strtoupper($_POST['StockID' . $i]));
		if ($_POST['StockID' . $i]!=''){
			$result = DB_query("SELECT COUNT(stockid) FROM stockmaster WHERE stockid='" . $_POST['StockID' . $i] . "'",$db);
			$myrow = DB_fetch_row($result);
			if ($myrow[0]==0){
				$InputError = True;
				$ErrorMessage .= _('The part code entered of'). ' ' . $_POST['StockID' . $i] . ' '. _('is not set up in the database') . '. ' . _('Only valid parts can be entered for transfers'). '<BR>';
				$_POST['LinesCounter'] -= 25;
			}
			DB_free_result( $result );
			if (!is_numeric($_POST['StockQTY' . $i])){
				$InputError = True;
				$ErrorMessage .= _('The quantity entered of'). ' ' . $_POST['StockQTY' . $i] . ' '. _('for part code'). ' ' . $_POST['StockID' . $i] . ' '. _('is not numeric') . '. ' . _('The quantity entered for transfers is expected to be numeric').'<BR>';
				$_POST['LinesCounter'] -= 25;
			}
			if ($_POST['StockQTY' . $i] <= 0){
				$InputError = True;
				$ErrorMessage .= _('The quantity entered for').' '. $_POST['StockID' . $i] . ' ' . _('is less than or equal to 0') . '. ' . _('Please correct this or remove the item').'<BR>';

			}
			// Only if stock exist at this location
			$result = DB_query("SELECT quantity FROM locstock WHERE stockid='" . $_POST['StockID' . $i] . "' and loccode='".$_POST['FromStockLocation']."'",$db);
			$myrow = DB_fetch_row($result);
			if ($myrow[0] <= 0){
				$InputError = True;
				$ErrorMessage .= _('The part code entered of'). ' ' . $_POST['StockID' . $i] . ' '. _('does not have stock available for transfer.') . '.<BR>';
				$_POST['LinesCounter'] -= 25;
			}else if($_POST['StockQTY' . $i] > $myrow[0]){
				$InputError = True;
				$ErrorMessage .= _('The part code entered of'). ' ' . $_POST['StockID' . $i] . ' '. _(' no tiene la cantidad suficiente para transferir del almacen seleccionado como origen') . '.<BR>';
				$_POST['LinesCounter'] -= 25;
			}
			DB_free_result( $result );
			$TotalItems++;
		}
	}//for all LinesCounter
	if ($TotalItems == 0){
		$InputError = True;
		$ErrorMessage .= _('You must enter at least 1 Stock Item to transfer').'<BR>';
	}

/*Ship location and Receive location are different */
	If ($_POST['FromStockLocation']==$_POST['ToStockLocation']){
		$InputError=True;
		$ErrorMessage .= _('The transfer must have a different location to receive into and location sent from');
	}
}

if(isset($_POST['Submit']) AND $InputError==False){

	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('Unable to BEGIN Location Transfer transaction');
	DB_query('BEGIN',$db, $ErrMsg);
    $createTranfer=false;
	for ($i=0;$i < $_POST['LinesCounter'];$i++){

        /******* codugo para comprar inventario y evitar negativos */
		$SQL="SELECT locstock.quantity
			FROM locstock
			WHERE locstock.stockid='" . $_POST['StockID' . $i]. "'
			AND loccode= '" . $_POST['FromStockLocation'] . "'";

		$ErrMsg =  _('Could not retrieve the QOH at the sending location because');
		$DbgMsg =  _('The SQL that failed was');
		$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

		if (DB_num_rows($Result)==1){
			$LocQtyRow = DB_fetch_row($Result);
			$QtyOnHandPrior = $LocQtyRow[0];
		} else {
			// There must actually be some error this should never happen
			$QtyOnHandPrior = 0;
		}
        if ($QtyOnHandPrior < $_POST['StockQTY' . $i] /*&& ($_SESSION['ProhibitNegativeStock']==1)*/ ) {
            echo _('El articulo ').$_POST['StockID' . $i]._(' no se transfirio debido a que no hay suficientes existencias').'<br />';
        }else{
		if($_POST['StockID' . $i] != ""){
		    $createTranfer=true;  
			$sql = "INSERT INTO loctransfers (reference,
								stockid,
								shipqty,
								shipdate,
								shiploc,
								recloc,
								rh_usrsend)
						VALUES ('" . $_POST['Trf_ID'] . "',
							'" . $_POST['StockID' . $i] . "',
							'" . $_POST['StockQTY' . $i] . "',
							'" . Date('Y-m-d') . "',
							'" . $_POST['FromStockLocation']  ."',
							'" . $_POST['ToStockLocation'] . "',
							'".$_SESSION['UserID']."')";
			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('Unable to enter Location Transfer record for'). ' '.$_POST['StockID' . $i];
			$resultLocShip = DB_query($sql,$db, $ErrMsg);
		}
       }
	}
        $ErrMsg = _('CRITICAL ERROR') . '! ' . _('Unable to COMMIT Location Transfer transaction');
        DB_query('BEGIN',$db, $ErrMsg);
    if($createTranfer){
	    prnMsg( _('The inventory transfer records have been created successfully'),'success');
    	echo '<P><A HREF="'.$rootpath.'/PDFStockLocTransfer.php?' . SID . 'TransferNo=' . $_POST['Trf_ID'] . '">'.
		_('Print the Transfer Docket'). '</A>';
    }
	unset($_SESSION['DispatchingTransfer']);
	unset($_SESSION['Transfer']);


} else {
	//Get next Inventory Transfer Shipment Reference Number
	if (isset($_GET['Trf_ID'])){
		$Trf_ID = $_GET['Trf_ID'];
	} elseif (isset($_POST['Trf_ID'])){
		$Trf_ID = $_POST['Trf_ID'];
	}

	if(!isset($Trf_ID)){
		$Trf_ID = GetNextTransNo(16,$db);
	}

	If ($InputError==true){
		echo '<BR>';
		prnMsg($ErrorMessage, 'error');
		echo '<BR>';

	}

	echo '<HR><FORM ACTION="' . $_SERVER['PHP_SELF'] . '?'. SID . '" METHOD=POST>';

	echo '<input type=HIDDEN NAME="Trf_ID" VALUE="' . $Trf_ID . '"><h2>'. _('Inventory Location Transfer Shipment Reference').' # '. $Trf_ID. '</h2>';
	
	// bowikaxu realhost - sept 2007 - ver los envios pendientes
	echo "<input type=submit name='transfers' value='Ver Transferencias'><BR>";
	
	// bowikaxu realhost - apply transfer changes
	if(isset($_POST['TranChanges'])){
		
		$sql = "SELECT * FROM loctransfers WHERE reference = ".$_POST['ref']."";
		$res = DB_query($sql,$db);
		while ($res2 = DB_fetch_array($res)){
			if(isset($_POST['Itm_'.$_POST['ref'].'_'.$res2['stockid']]) && $_POST['Itm_'.$_POST['ref'].'_'.$res2['stockid']] >= $res2['recqty'] && $res2['recqty']>=0){
				$sql = "UPDATE loctransfers SET shipqty = ". $_POST['Itm_'.$_POST['ref'].'_'.$res2['stockid']].",
						rh_change = '".date('d-m-Y').' / '.$_SESSION['UserID']."',
						rh_usrsend = '".$_SESSION['UserID']."'
				WHERE reference = ".$_POST['ref']." 
					AND stockid = '".$res2['stockid']."'";
				DB_query($sql,$db,'Imposible Actualizar Transferencia','Fallo: '.$sql);
				prnMsg( _('Articulo ').$res2['stockid'].' '._('Actualizado'),'success');
			}else if(isset($_POST['Itm_'.$_POST['ref'].'_'.$res2['stockid']]) && $_POST['Itm_'.$_POST['ref'].'_'.$res2['stockid']] < $res2['recqty']) {
				//echo "ERROR: la cantidad es menor a la recibida o no se envio bien la transferencia<BR>";
				prnMsg("La cantidad es menor a la recibida o no se envio correctamente la Transferencia", 'error');
			}else {
				// nada es otro articulo
			}
		}
		
		$_POST['transfer'] = $_POST['ref'];
	}
	
	if(isset($_POST['transfers'])){
		
		$sql = "SELECT loctransfers.reference,loctransfers.shiploc,loctransfers.recloc, COUNT(loctransfers.stockid) AS articulos, loctransfers.rh_usrsend, loctransfers.rh_usrrecd, stockmaster.description
				 FROM loctransfers, stockmaster 
				 WHERE shipqty > recqty 
				 AND stockmaster.stockid = loctransfers.stockid
				 GROUP BY reference";
		$res = DB_query($sql,$db);
		echo "<TABLE align=center><TR>
			<TD CLASS='tableheader'>"._('Reference')."</TD>
			<TD CLASS='tableheader'>"._('Almacen de Salida')."</TD>
			<TD CLASS='tableheader'>"._('Almacen Destino')."</TD>
			<TD CLASS='tableheader'>"._('Usuario').' '._('Envia/Modifica')."</TD>
			<TD CLASS='tableheader'>"._('Usuario').' '._('Recibe')."</TD>
			<TD CLASS='tableheader'>"._('Items')."</TD>
			</TR>";
		$j = 1;
		$k = 0; //row colour counter
		while ($trans = DB_fetch_array($res)){
			
			if ($k == 1){
				echo "<TR BGCOLOR='#CCCCCC'>";
				$k = 0;
			} else {
				echo "<TR BGCOLOR='#EEEEEE'>";
				$k = 1;
			}
			echo "<TD><a href='StockLocTransfer.php?".SID."&transfer=".$trans['reference']."'>".$trans['reference']."</A></TD>";
			echo "<TD>".$trans['shiploc']."</TD>";
			echo "<TD>".$trans['recloc']."</TD>";
			echo "<TD>".$trans['rh_usrsend']."</TD>";
			echo "<TD>".$trans['rh_usrrecd']."</TD>";
			echo "<TD>".$trans['articulos']."</TD></TR>";
			
		}
		echo "</TABLE>";
	}
	if(isset($_POST['transfer'])){
		// 
		echo "<FORM METHOD=POST NAME=transfer>";
		echo "<INPUT TYPE=hidden name=ref value='".$_POST['transfer']."'>";
		$sql = "SELECT loctransfers.*, stockmaster.description FROM loctransfers, stockmaster WHERE loctransfers.reference = ".$_POST['transfer']."
		 AND stockmaster.stockid = loctransfers.stockid";
		
		$res = DB_query($sql,$db,'Imposible obtener transferencias');
		$header = "<TABLE align=center>
					<TR>
					<TD CLASS='tableheader'>"._('Reference')."</TD>
					<TD CLASS='tableheader'>"._('Item Code')."</TD>
					<TD CLASS='tableheader'>"._('Description')."</TD>
					<TD CLASS='tableheader'>"._('Cantidad Enviada')."</TD>
					<TD CLASS='tableheader'>"._('Cantidad Recibida')."</TD>
					<TD CLASS='tableheader'>"._('Fecha Envio')."</TD>
					<TD CLASS='tableheader'>"._('Almacen de Salida')."</TD>
					<TD CLASS='tableheader'>"._('Almacen Destino')."</TD>
					</TR>";
		echo $header;
		$j = 1;
		$k = 0; //row colour counter
		while($details = DB_fetch_array($res)){
			
			if ($k == 1){
				echo "<TR BGCOLOR='#CCCCCC'>";
				$k = 0;
			} else {
				echo "<TR BGCOLOR='#EEEEEE'>";
				$k = 1;
			}
			echo "<TD>".$details['reference']."</TD>";
			echo "<TD>".$details['stockid']."</TD>";
			echo "<TD>".$details['description']."</TD>";
			if($details['shipqty']>$details['recqty']){
				echo "<TD><INPUT TYPE=TEXT NAME='Itm_".$details['reference']."_".$details['stockid']."' VALUE='".$details['shipqty']."'></TD>";
			} else {
				echo "<TD>".$details['shipqty']."</TD>";
			}
			echo "<TD>".$details['recqty']."</TD>";
			echo "<TD>".$details['shipdate']."</TD>";
			echo "<TD>".$details['shiploc']."</TD>";
			echo "<TD>".$details['recloc']."</TD></TR>";
			
		}
		
		echo "</TABLE>";
		echo "<CENTER><INPUT TYPE=SUBMIT NAME=TranChanges VALUE='Aplicar Cambios'></CENTER>";
	}
	//echo "</FORM>";
	// fin ver transferencias
	
   /*	$sql = 'SELECT loccode, locationname FROM locations';
	$resultStkLocs = DB_query($sql,$db);
	echo _('From Stock Location').':<SELECT name="FromStockLocation">';
	while ($myrow=DB_fetch_array($resultStkLocs)){
		if (isset($_POST['FromStockLocation'])){
			if ($myrow['loccode'] == $_POST['FromStockLocation']){
				echo '<OPTION SELECTED Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
			} else {
				echo '<OPTION Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
			}
		} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
			echo '<OPTION SELECTED Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
			$_POST['FromStockLocation']=$myrow['loccode'];
		} else {
			echo '<OPTION Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		}
	}
	echo '</SELECT>'; */
//*************RH Seleccion de Almacenes permitidos para el usuario*************
echo _('From Stock Location').':<SELECT name="FromStockLocation">';

foreach($_SESSION['rh_permitionlocation'] as $key=>$value){
	if ($_POST['FromStockLocation']==$key){
		echo "<OPTION SELECTED Value='$key'>$value";
	} else {
		echo "<OPTION Value='$key'>$value";
	}
}
echo '</SELECT>';
//******************************************************************************

//*************RH Seleccion de Almacenes permitidos para el usuario*************
/*echo _('To Stock Location').':<SELECT name="ToStockLocation">';

foreach($_SESSION['rh_permitionlocation'] as $key=>$value){
	if ($_POST['ToStockLocation']==$key){
		echo "<OPTION SELECTED Value='$key'>$value";
	} else {
		echo "<OPTION Value='$key'>$value";
	}
}
echo '</SELECT><BR>'; */
//******************************************************************************
    $sql = 'SELECT loccode, locationname FROM locations';
	$resultStkLocs = DB_query($sql,$db);
	DB_data_seek($resultStkLocs,0); //go back to the start of the locations result
	echo _('To Stock Location').':<SELECT name="ToStockLocation">';
	while ($myrow=DB_fetch_array($resultStkLocs)){
		if (isset($_POST['ToStockLocation'])){
			if ($myrow['loccode'] == $_POST['ToStockLocation']){
				echo '<OPTION SELECTED Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
			} else {
				echo '<OPTION Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
			}
		} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
			echo '<OPTION SELECTED Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
			$_POST['ToStockLocation']=$myrow['loccode'];
		} else {
			echo '<OPTION Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		}
	}
	echo '</SELECT><BR>';

	echo '<CENTER><TABLE>';

	$tableheader = '<TR><TD class="tableheader">'. _('Item Code'). '</TD><TD class="tableheader">'. _('Quantity'). '</TD></TR>';
	echo $tableheader;

	$k=0; /* row counter */
	if(isset($_POST['LinesCounter'])){

		for ($i=0;$i < $_POST['LinesCounter'] AND $_POST['StockID' . $i] !='';$i++){

			if ($k==18){
				echo $tableheader;
				$k=0;
			}
			echo '<TR>
				<TD><input type=text name="StockID' . $i .'" size=21  maxlength=20 Value="' . $_POST['StockID' . $i] . '"></TD>
				<TD><input type=text name="StockQTY' . $i .'" size=5 maxlength=4 Value="' . $_POST['StockQTY' . $i] . '"></TD>
			</TR>';
		}
	}else {
		$i = 0;
	}
	// $i is incremented an extra time, so 9 to get 10...
	// bowikaxu realhost - add more item lines from 9 to 24 to get 25
	$z=($i + 24);

	while($i < $z) {
		echo '<TR>
			<td><input type=text name="StockID' . $i .'" size=21  maxlength=20 Value="' . $_POST['StockID' . $i] . '"></td>
			<td><input type=text name="StockQTY' . $i .'" size=5 maxlength=4 Value="' . $_POST['StockQTY' . $i] . '"></td>
		</tr>';
		$i++;
	}

	echo '</table><br>
		<input type=hidden name="LinesCounter" value='. $i .'><INPUT TYPE=SUBMIT NAME="EnterMoreItems" VALUE="'. _('Add More Items'). '"><INPUT TYPE=SUBMIT NAME="Submit" VALUE="'. _('Create Transfer Shipment'). '"><BR><HR>';
	echo '</FORM></CENTER>';
	include('includes/footer.inc');
}
?>
