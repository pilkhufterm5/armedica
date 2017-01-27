<?php

/**
 * REALHOST 15 DE FEBRERO DEL 2010
 * RICARDO ABULARACH GARCIA
 * SE AGREGO LA SECCION QUE ES EL REPORTE DEL VALE DE SALIDA
 */

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=2;

include('includes/session.inc');

$title = _('Recepci&oacute;n de Transferencia por Lote');

include('includes/header.inc');
echo "<a href='rh_listadoTransferLote.php?Search=1'>" . _('Regresar') . '</A><BR>';
$_POST['ShowMenu']=0;

?>

<script type="text/javascript">
	function fn_confirma(){
		if(confirm("Confirme la recepcion de la mercanc\u00eda?"))
			return true;
		else
			return false;
	}
	
	function fn_valida_numero(valor,maximo,id){
		if(valor > maximo)
			document.getElementById(id).value=maximo;
		else if(valor < 0)
			document.getElementById(id).value=maximo;
	}
</script>


<?php

function date_convert($date){
    $dividir = @explode("/", $date);
    $dia = $dividir[0];
    $mes = $dividir[1];
    $year = $dividir[2];
    $armada = $year.'-'.$mes.'-'.$dia;
    return $armada;
}

echo "<BR><CENTER><B>"._('Recepci&oacute;n de Transferencia por Lote')."</B></CENTER><BR>";

if($_POST['Procesar']){//Procesamos la recepción de los items
	
	//Primero validamos que las cantidades estén correctas.
	$procesar = true;
	$grupos = $_SESSION['RecepcionTransfer']['Grupos'];
	foreach($grupos as $grupo){
		
		foreach($grupo['items'] as $item){
			
			$item_qty = intval(trim($_POST['itmRecibir_'.$item['did']]));
			$motivo_recibo = trim($_POST['itmMotivo_'.$item['did']]);
			
			//Revisamos cantidad que se captura para recibir
			if($item_qty>$item['qty_por_recibir']){
				prnMsg(_('En el art&iacute;culo '.$item['stockid'].' no puede recibir mas de '.$item['qty_por_recibir'].' unidades.'), 'error');
				$procesar = false;
			}
			
			if(($item_qty==0 && $motivo_recibo=='') || ($item_qty<$item['qty_por_recibir'] && $motivo_recibo=='')){
				prnMsg(_('Capture el motivo por el cual no se recibe el total de la mercanc&iacute;a para el art&iacute;culo '.$item['stockid']), 'error');
				$procesar = false;
			}
			
			//Revisamos si el lote y la cantidad corresponde.
			//$sql_rev = 'select * from stockserialitems where stockid="'.$item['stockid'].'" and loccode="'.$_SESSION['RecepcionTransfer']['location_from'].'" and serialno="'.$item['serialno'].'"';
		}
	}
	
	if($procesar){
	
		//Se inicializa la transacción.
		$Result = DB_Txn_Begin($db);

		$AdjustmentNumber = GetNextTransNo(16,$db);
		$PeriodNo = GetPeriod (Date($_SESSION['DefaultDateFormat']), $db);
		$SQLAdjustmentDate = FormatDateForSQL(Date($_SESSION['DefaultDateFormat']));
		
		//Proceso principal para intercambiar los items entre almacenes
		$TransferNumber = $AdjustmentNumber;
		$SQLTransferDate = $SQLAdjustmentDate;
		
		$from_loc = $_SESSION['RecepcionTransfer']['location_from'];
		$to_loc = $_SESSION['RecepcionTransfer']['location_to'];
		
		$sql_ins  = 'insert into rh_transfer_lote_recepcion(fecha,userid_recibe,transfer_lote_id,transno)';
		$sql_ins .= 'values(NOW(),"'.$_SESSION['UserID'].'",'.$_SESSION['RecepcionTransfer']['TransferID'].','.$TransferNumber.')';
		DB_query($sql_ins,$db);
		$recepcionID = DB_Last_Insert_ID($db,'rh_transfer_lote_recepcion','id');
		
		$enviar_notificacion = false;
		$msg_email = array();
		
		{//Se realizan los movimientos
			
			$grupos = $_SESSION['RecepcionTransfer']['Grupos'];
			foreach($grupos as $grupo){
				
				foreach($grupo['items'] as $item){
					
					$item_qty = trim($_POST['itmRecibir_'.$item['did']]);
					$motivo_recibo = trim($_POST['itmMotivo_'.$item['did']]);
					$procesar = true;
					
					//Validaciones adicionales
					if($item_qty<=0){
						$procesar = false;
					}
					
					$sql_rev = 'select * from stockserialitems where stockid="'.$item['stockid'].'" and loccode="'.$_SESSION['RecepcionTransfer']['location_from'].'" and serialno="'.$item['serialno'].'"';
					$rs_rev  = DB_query($sql_rev,$db);
					if(!DB_num_rows($rs_rev)){
						$procesar = false;
						prnMsg(_('El art&iacute;culo '.$item['stockid'].' no existe en el Lote y Almac&eacute;n seleccionado como origen.'), 'error');
						
					}else{
						if($_SESSION['ProhibitNegativeStock']==1){
							//Comprobamos si hay suficiente stock para mandar.
							$rw_rev = DB_fetch_assoc($rs_rev);
							$cantidad_disponible = $rw_rev['quantity'];
							if($item_qty>$cantidad_disponible){
								$item_qty = 0;
								$procesar = false;
								prnMsg(_('El art&iacute;culo '.$item['stockid'].' no tiene la suficiente cantidad en el almacen y lote de origen para hacer la transferencia.'), 'error');
							}
						}
					}
					
					
					
					if($procesar){
						
						$sql_r  = 'insert into rh_transfer_lote_recepcion_details(recepcion_id,transfer_detail_id,stockid,serialno,qty_recibida, motivo_recibo) values';
						$sql_r .= '('.$recepcionID.','.$item['did'].',"'.$item['stockid'].'","'.$item['serialno'].'",'.$item_qty.',"'.DB_escape_string($motivo_recibo).'")';
						$ErrMsg =  _('Could not retrieve the QOH at the sending location because');
						$DbgMsg =  _('The SQL that failed was');
						$Result = DB_query($sql_r, $db, $ErrMsg, $DbgMsg, true);
						
						//Quitamos la cantidad
						{
							// Need to get the current location quantity will need it later for the stock movement
							$SQL="SELECT locstock.quantity
								FROM locstock
								WHERE locstock.stockid='" . $item['stockid'] . "'
								AND loccode= '" . $from_loc . "'";

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
							
							// Insert the stock movement for the stock going out of the from location
							$SQL = "INSERT INTO stockmoves (stockid,
										type,
										transno,
										loccode,
										trandate,
										prd,
										reference,
										qty,
										newqoh)
								VALUES ('" .
										$item['stockid'] . "',
										16,
										" . $TransferNumber . ",
										'" . $from_loc . "',
										'" . $SQLTransferDate . "'," . $PeriodNo . ",
										'',
										" . -$item_qty . ",
										" . ($QtyOnHandPrior - $item_qty) .
									")";

							$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock movement record cannot be inserted because');
							$DbgMsg =  _('The following SQL to insert the stock movement record was used');
							$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

							/*Get the ID of the StockMove... */
							$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');
							
							
							$SQL_existe = "SELECT *
							FROM stockserialitems
							WHERE
							stockid='" . $item['stockid'] . "'
							AND loccode='" . $from_loc . "'
							AND serialno='" . $item['serialno'] . "'";
							$rs_existe = DB_query($SQL_existe,$db);
							if(DB_num_rows($rs_existe)){
								
								$SQL = "UPDATE stockserialitems SET
								quantity= quantity - " . $item_qty . "
								WHERE
								stockid='" . $item['stockid'] . "'
								AND loccode='" . $from_loc . "'
								AND serialno='" . $item['serialno'] . "'";
								$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
								
							}else{
								
								/*Need to insert a new serial item record */
								$SQL = "INSERT INTO stockserialitems (stockid,
													loccode,
													serialno,
													quantity)
									VALUES ('" . $item['stockid'] . "',
									'" . $from_loc. "',
									'" . $item['serialno'] . "',
									" . -$item_qty . ")";

								$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be added because');
								$DbgMsg = _('The following SQL to insert the serial stock item record was used');
								$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
								
							}
							
							
							
							$SQL = "INSERT INTO stockserialmoves (
										stockmoveno,
										stockid,
										serialno,
										moveqty)
								VALUES (
									" . $StkMoveNo . ",
									'" . $item['stockid'] . "',
									'" . $item['serialno'] . "',
									-" . $item_qty . "
									)";

							$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
							$DbgMsg = _('The following SQL to insert the serial stock movement records was used');
							$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
						}
						
						{//La agregamos al otro almacen
							// Need to get the current location quantity will need it later for the stock movement
							$SQL="SELECT locstock.quantity
								FROM locstock
								WHERE locstock.stockid='" . $item['stockid'] . "'
								AND loccode= '" . $to_loc . "'";

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
							
							// Insert the stock movement for the stock going out of the from location
							$SQL = "INSERT INTO stockmoves (stockid,
										type,
										transno,
										loccode,
										trandate,
										prd,
										reference,
										qty,
										newqoh)
								VALUES ('" .
										$item['stockid'] . "',
										16,
										" . $TransferNumber . ",
										'" . $to_loc . "',
										'" . $SQLTransferDate . "'," . $PeriodNo . ",
										'',
										" . $item_qty . ",
										" . ($QtyOnHandPrior + $item_qty) .
									")";

							$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock movement record cannot be inserted because');
							$DbgMsg =  _('The following SQL to insert the stock movement record was used');
							$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

							/*Get the ID of the StockMove... */
							$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');
							
							
							$SQL_existe = "SELECT *
							FROM stockserialitems
							WHERE
							stockid='" . $item['stockid'] . "'
							AND loccode='" . $to_loc . "'
							AND serialno='" . $item['serialno'] . "'";
							$rs_existe = DB_query($SQL_existe,$db);
							if(DB_num_rows($rs_existe)){
								
								$SQL = "UPDATE stockserialitems SET
								quantity= quantity + " . $item_qty . "
								WHERE
								stockid='" . $item['stockid'] . "'
								AND loccode='" . $to_loc . "'
								AND serialno='" . $item['serialno'] . "'";
								$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
								
							}else{
								
								/*Need to insert a new serial item record */
								$SQL = "INSERT INTO stockserialitems (stockid,
													loccode,
													serialno,
													quantity)
									VALUES ('" . $item['stockid'] . "',
									'" . $to_loc. "',
									'" . $item['serialno'] . "',
									" . $item_qty . ")";

								$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be added because');
								$DbgMsg = _('The following SQL to insert the serial stock item record was used');
								$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
								
							}
							
							$SQL = "INSERT INTO stockserialmoves (
										stockmoveno,
										stockid,
										serialno,
										moveqty)
								VALUES (
									" . $StkMoveNo . ",
									'" . $item['stockid'] . "',
									'" . $item['serialno'] . "',
									" . $item_qty . "
									)";

							$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
							$DbgMsg = _('The following SQL to insert the serial stock movement records was used');
							$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
						}
						
						{//descontamos de los 2 almacenes
							$SQL = "UPDATE locstock
							SET quantity = quantity - " . $item_qty . "
							WHERE stockid='" . $item['stockid'] . "'
							AND loccode='" . $from_loc . "'";

							$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The location stock record could not be updated because');
							$DbgMsg = _('The following SQL to update the location stock record was used');
							$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

							$SQL = "UPDATE locstock
								SET quantity = quantity + " . $item_qty . "
								WHERE stockid='" . $item['stockid'] . "'
								AND loccode='" . $to_loc . "'";


							$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The location stock record could not be updated because');
							$DbgMsg = _('The following SQL to update the location stock record was used');
							$Result = DB_query($SQL,$db,$ErrMsg, $DbgMsg, true);
						}
						
						//Actualizamos el detalle del envìo.
						//rh_transfer_lote_preview_details
						
						$sql_recibida = 'update rh_transfer_lote_preview_details set qty_recibida=qty_recibida+'.$item_qty.' where id='.$item['did'];
						$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The location stock record could not be updated because');
						$DbgMsg = _('The following SQL to update the location stock record was used');
						$Result = DB_query($sql_recibida,$db,$ErrMsg, $DbgMsg, true);
						
						$sql_lt = 'update loctransfers set recqty=recqty+'.$item_qty.', rh_usrrecd="'.$_SESSION['UserID'].'", recdate=NOW() where id='.$item['ltid'];
						$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The location stock record could not be updated because');
						$DbgMsg = _('The following SQL to update the location stock record was used');
						$Result = DB_query($sql_lt,$db,$ErrMsg, $DbgMsg, true);
						
						
						//Validamos qty para saber si se envìa notificaciòn.
						if($item_qty<$item['qty_por_recibir']){
							
							$enviar_notificacion = true;
							
							$table  = '<table>';
							$table .= '<tr><td>Stockid</td><td>'.$item['stockid'].'</td></tr>';
							$table .= '<tr><td>C&oacute;digo de Barras</td><td>'.$item['barcode'].'</td></tr>';
							$table .= '<tr><td>Descripci&oacute;n</td><td>'.$item['descripcion'].'</td></tr>';
							$table .= '<tr><td>Lote</td><td>'.$item['serialno'].'</td></tr>';
							$table .= '<tr><td>Cantidad Enviada</td><td>'.$item['qty_por_recibir'].'</td></tr>';
							$table .= '<tr><td>Cantidad Recibida</td><td>'.$item_qty.'</td></tr>';
							$table .= '<tr><td>Comentario</td><td>'.DB_escape_string($motivo_recibo).'</td></tr>';
							$table .= '</table>';
							$msg_email[] = $table;
						}
					}else{
						$enviar_notificacion = true;
						
						$table  = '<table>';
						$table .= '<tr><td>Stockid</td><td>'.$item['stockid'].'</td></tr>';
						$table .= '<tr><td>C&oacute;digo de Barras</td><td>'.$item['barcode'].'</td></tr>';
						$table .= '<tr><td>Descripci&oacute;n</td><td>'.$item['descripcion'].'</td></tr>';
						$table .= '<tr><td>Lote</td><td>'.$item['serialno'].'</td></tr>';
						$table .= '<tr><td>Cantidad Enviada</td><td>'.$item['qty_por_recibir'].'</td></tr>';
						$table .= '<tr><td>Cantidad Recibida</td><td>'.$item_qty.'</td></tr>';
						$table .= '<tr><td>Comentario</td><td>'.DB_escape_string($motivo_recibo).'</td></tr>';
						$table .= '</table>';
						$msg_email[] = $table;
					}
				}
			}
		}
		
		$sql_check = 'select * from rh_transfer_lote_preview_details where (qty_envio-qty_recibida)>0 and transfer_lote_id='.$_SESSION['RecepcionTransfer']['TransferID'];
		$rs_check  = DB_query($sql_check,$db);
		if(!DB_num_rows($rs_check)>0){
			$sql = 'update rh_transfer_lote_preview set recibida=1 where id='.$_SESSION['RecepcionTransfer']['TransferID'];
			DB_query($sql,$db);
		}
		
		$Result = DB_query('COMMIT',$db);
		prnMsg(_('Se ha recibido correctamente la mercanc&iacute;a.'), 'success');
		
		$link  = 'Imprimir Recibo: <A target="_blank" HREF="' . $rootpath . '/rh_PDF_StockRecepcionLote.php?' . SID . '&RecepcionID=' . $recepcionID . '">' . 'Imprimir PDF' . '</A>';
		$link .= '<br>';
		$link .= 'Imprimir Recibo: <A target="_blank" HREF="' . $rootpath . '/rh_CSV_StockRecepcionLote.php?' . SID . '&RecepcionID=' . $recepcionID . '">' . 'Imprimir CSV' . '</A>';
		prnMsg( _($link), 'success');
		
		include('includes/footer.inc');
		unset($_SESSION['RecepcionTransfer']);
		
		
		if($enviar_notificacion){
			//Se envìa la notificaciòn de que se recibiò una cantidad menor a la esperada
			
			$header = '<table><tr><td>Se ha recibido una cantidad menor a lo enviado en la transferencia: '.$TransferNumber.'</td></tr></table><br>';
			
			$mensaje = $header;
			foreach($msg_email as $msg){
				$mensaje .= $msg.'<br>';
			}
			
			EnviarMail('test@realhost.com.mx',array('mhidalgo@realhost.com.mx','rleal@realhost.com.mx','nohemi@realhost.com.mx'),'Transferencia por Lote',$mensaje);
		}
		
		exit();
	}
}


//Inicializamos la info
if(isset($_GET['TransferID'])){
	$_SESSION['RecepcionTransfer'] = NULL;
	
	$sql_t = 'select * from rh_transfer_lote_preview where recibida=0 and id='.$_GET['TransferID'];
	$rs_t = DB_query($sql_t,$db);
	if(!DB_num_rows($rs_t)){
		prnMsg( _('No existe la transferencia que quiere recibir.'), 'error');
		include('includes/footer.inc');
	}
	
	$rw_t = DB_fetch_assoc($rs_t);
	$_SESSION['RecepcionTransfer']['TransferID'] = $_GET['TransferID'];
	$_SESSION['RecepcionTransfer']['fecha_envio'] = $rw_t['fecha_envio'];
	$_SESSION['RecepcionTransfer']['userid_envio'] = $rw_t['userid_envio'];
	$_SESSION['RecepcionTransfer']['location_from'] = $rw_t['location_from'];
	$_SESSION['RecepcionTransfer']['location_to'] = $rw_t['location_to'];
	
	$sql_alm1 = 'select locationname from locations where loccode="'.$rw_t['location_from'].'"';
	$rs_alm1  = DB_query($sql_alm1,$db);
	$rw_alm1  = DB_fetch_row($rs_alm1);
	$_SESSION['RecepcionTransfer']['location_from_text'] = $rw_alm1[0];
	
	$sql_alm2 = 'select locationname from locations where loccode="'.$rw_t['location_to'].'"';
	$rs_alm2  = DB_query($sql_alm2,$db);
	$rw_alm2  = DB_fetch_row($rs_alm2);
	$_SESSION['RecepcionTransfer']['location_to_text'] = $rw_alm2[0];
	
	$_SESSION['RecepcionTransfer']['comentario'] = $rw_t['comentario'];
	
	$grupo = array();
	$items_recepcion = array();
	
	$sql_grupos = 'select stockmaster.id_agrupador as id_grupo, rh_stock_grupo.clave, rh_stock_grupo.nombre, 
				   rh_transfer_lote_preview_details.id as did, rh_transfer_lote_preview_details.stockid, rh_transfer_lote_preview_details.barcode, rh_transfer_lote_preview_details.serialno, qty_envio, rh_transfer_lote_preview_details.qty_recibida, 
				   stockmaster.description, loctransfer_id 
				   from rh_transfer_lote_preview_details 
				   inner join stockmaster on rh_transfer_lote_preview_details.stockid=stockmaster.stockid 
				   inner join rh_stock_grupo on stockmaster.id_agrupador=rh_stock_grupo.clave
				   where transfer_lote_id='.$_GET['TransferID'].' and (qty_envio-qty_recibida)>0 
				   order by rh_stock_grupo.nombre asc';
	$rs_grupos = DB_query($sql_grupos,$db);
	while($rw_grupos = DB_fetch_assoc($rs_grupos)){
		if(!isset($grupo[$rw_grupos['id_grupo']])){
			$grupo[$rw_grupos['id_grupo']]['id'] = $rw_grupos['id_grupo'];
			$grupo[$rw_grupos['id_grupo']]['clave'] = $rw_grupos['clave'];
			$grupo[$rw_grupos['id_grupo']]['nombre'] = $rw_grupos['nombre'];
		}
		
		$item = array();
		$item['did'] = $rw_grupos['did'];
		$item['ltid'] = $rw_grupos['loctransfer_id'];
		$item['stockid'] = $rw_grupos['stockid'];
		$item['barcode'] = $rw_grupos['barcode'];
		$item['serialno'] = $rw_grupos['serialno'];
		$item['qty_envio'] = $rw_grupos['qty_envio'] - $rw_grupos['qty_recibida'];
		$item['qty_recepcion'] = $rw_grupos['qty_envio'] - $rw_grupos['qty_recibida'];
		$item['qty_por_recibir'] = $rw_grupos['qty_envio'] - $rw_grupos['qty_recibida'];
		$item['descripcion'] = $rw_grupos['description'];
		
		//Caducidad
		$sql_c = 'select expirationdate from stockserialitems where stockid="'.$item['stockid'].'" and serialno="'.$item['serialno'].'" and loccode="'.$_SESSION['RecepcionTransfer']['location_from'].'"';
		$rs_c  = DB_query($sql_c,$db);
		if(DB_num_rows($rs_c)){
			$rw_c  = DB_fetch_row($rs_c);
			$item['fecha_caducidad'] = $rw_c[0];
		}else{
			$item['fecha_caducidad'] = 'ND';
		}
		
		
		$grupo[$rw_grupos['id_grupo']]['items'][] = $item;
	}
	
	$_SESSION['RecepcionTransfer']['Grupos'] = $grupo;
	
}


echo '<center>';

echo '<table>';
echo '<tr><th>Fecha de env&iacute;o</th><td>'.$_SESSION['RecepcionTransfer']['fecha_envio'].'</td></tr>';
echo '<tr><th>Usuario que env&iacute;a</th><td>'.$_SESSION['RecepcionTransfer']['userid_envio'].'</td></tr>';
echo '<tr><th>Almacen Origen</th><td>'.$_SESSION['RecepcionTransfer']['location_from_text'].'</td></tr>';
echo '<tr><th>Almacen Destino</th><td>'.$_SESSION['RecepcionTransfer']['location_to_text'].'</td></tr>';
echo '<tr><th>comentario</th><td><textarea>'.$_SESSION['RecepcionTransfer']['fecha_envio'].'</textarea></td></tr>';
echo '</table>';

echo '</center>';


{//Desglosamos la info
	echo '<CENTER>';
	echo "<FORM NAME='Regreso' METHOD='POST' onsubmit='return fn_confirma()' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
	
	$grupos = $_SESSION['RecepcionTransfer']['Grupos'];
	foreach($grupos as $grupo){
		
		echo '<br>';
		echo "<table cellpadding='5' CELLSPACING='5' width='100%'>";
		echo "<tr>";
			//echo "<td width='10%' CLASS='tableheader' align='center'>Clave del Grupo</td>";
			echo "<td width='10%' CLASS='tableheader' align='center'>Grupo</td>";
			echo "<td class='tableheader' align='center' colspan='8'>Articulos</td>";
		echo "</tr>";
		
		echo "<tr>";
			//echo "<td width='10%' align='center'>".$grupo['clave']."</td>";
			echo "<td width='10%' align='center'>".$grupo['nombre']."</td>";
			//echo "<td CLASS='tableheader' align='center'>&nbsp;</td>";
			echo "<td CLASS='tableheader' align='center'><small>Stockid</small></td>";
			echo "<td CLASS='tableheader' align='center'><small>Barcode</small></td>";
			echo "<td CLASS='tableheader' align='center'>Descripci&oacute;n</td>";
			echo "<td CLASS='tableheader' align='center'><small>Lote</small></td>";
			echo "<td CLASS='tableheader' align='center'><small>Fecha de Caducidad</small></td>";
			echo "<td CLASS='tableheader' align='center'><small>Cantidad a recibir</small></td>";
			echo "<td CLASS='tableheader' align='center'><small>Cantidad</small></td>";
			echo "<td CLASS='tableheader' align='center'><small>Motivo</small></td>";
			echo '</tr>';
		
		
		foreach($grupo['items'] as $item){
			
			
			echo "<tr>";
				//echo "<td width='10%' align='center'>&nbsp;</td>";
				echo "<td width='10%' align='center'>&nbsp;</td>";
				//echo '<td align="center"><input type="checkbox" id="FullReception_'.$item['did'].'"></td>';
				echo "<td align='center'>".$item['stockid']."</td>";
				echo "<td align='center'>".$item['barcode']."</td>";
				echo "<td align='center'><small>".$item['descripcion']."</small></td>";
				echo "<td align='center'>".$item['serialno']."</td>";
				echo "<td align='center'>".$item['fecha_caducidad']."</td>";
				echo "<td align='center' class=number>".abs($item['qty_envio'])."</td>";
				//onchange='fn_valida_numero(this.value,".$item['qty_por_recibir'].",this.id)'
				$post = isset($_POST['itmRecibir_'.$item['did']]) ? intval(trim($_POST['itmRecibir_'.$item['did']])) : $item['qty_recepcion'];
				$post_motivo = (trim($_POST['itmMotivo_'.$item['did']]));
				echo "<td align='center'><input type='text' name='itmRecibir_".$item['did']."' style='width: 40px !important;' class='number' value='".$post."' id='itmRecibir_".$item['did']."' ></td>";
				echo "<td align='center'><input type='text' name='itmMotivo_".$item['did']."' size=5 value='".$post_motivo."' id='itmMotivo_".$item['did']."' ></td>";
			echo "</tr>";
			
			
			
		}		
		echo '</table>';
		
	}
	echo "<input type='submit' name='Procesar' value='Procesar'>";
	echo '</FORM>';
	echo '</CENTER>';
}

//include('includes/footer.inc');

?>
