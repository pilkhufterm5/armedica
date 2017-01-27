<?php

$PageSecurity = 2;

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

function codeWrap($code, $cutoff = null, $delimiter = "&raquo;\n"){
    $lines = explode("\n", $code);
    $count = count($lines);
    for ($i = 0; $i < $count; ++$i) {
        preg_match('/^\s*/', $lines[$i], $matches);
        $lines[$i] = wordwrap($lines[$i], $cutoff, ($delimiter . $matches[0]));
    }
    return implode("\n", $lines);
}

$RecepcionID = $_GET['RecepcionID'];

if(!isset($_GET['RecepcionID']) || $_GET['RecepcionID']==''){
	prnMsg(_('La p&aacute;gina debe ser llamada con el n&uacute;mero de env&iacute;o'),'warn');
	include('includes/footer.inc');
	exit;
}

$line_height=16;
   /* Then there's an order to print and its not been printed already (or its been flagged for reprinting)
   Now ... Has it got any line items */

   $PageNumber = 1;
   $ErrMsg = _('There was a problem retrieving the line details for order number') . ' ' . $RecepcionID . ' ' .
		_('from the database');
	
	$sql_lineas = 'select stockmaster.stockid,stockmaster.barcode,stockmaster.description, rh_transfer_lote_recepcion_details.qty_recibida, rh_transfer_lote_preview_details.qty_envio, rh_transfer_lote_recepcion_details.serialno, rh_transfer_lote_recepcion_details.motivo_recibo from rh_transfer_lote_recepcion 
			inner join rh_transfer_lote_recepcion_details on rh_transfer_lote_recepcion.id = rh_transfer_lote_recepcion_details.recepcion_id 
			inner join rh_transfer_lote_preview_details on rh_transfer_lote_recepcion_details.transfer_detail_id = rh_transfer_lote_preview_details.id 
			inner join stockmaster on rh_transfer_lote_recepcion_details.stockid=stockmaster.stockid 
			where rh_transfer_lote_recepcion.id='.$RecepcionID;
		
   $result=DB_query($sql_lineas,$db);

   if (DB_num_rows($result)>0){
	   /*Yes there are line items to start the ball rolling with a page header */
		
		$sql_t = 'select * from rh_transfer_lote_recepcion where id='.$RecepcionID;
		$rs_t = DB_query($sql_t,$db);
		if(!DB_num_rows($rs_t)){
			prnMsg( _('No existe la recepcion.'), 'error');
			include('includes/footer.inc');
		}
		
		$rw_t = DB_fetch_assoc($rs_t);
		$_SESSION['RecepcionTransfer']['RecepcionID'] = $_GET['RecepcionID'];
		$_SESSION['RecepcionTransfer']['fecha_recepcion'] = $rw_t['fecha'];
		$_SESSION['RecepcionTransfer']['userid_recibe'] = $rw_t['userid_recibe'];
		
		
		$sql_t = 'select rh_transfer_lote_preview.* from rh_transfer_lote_recepcion 
				  inner join rh_transfer_lote_preview on rh_transfer_lote_recepcion.transfer_lote_id = rh_transfer_lote_preview.id 
				  where rh_transfer_lote_recepcion.id='.$RecepcionID;
		$rs_t = DB_query($sql_t,$db);
		if(!DB_num_rows($rs_t)){
			prnMsg( _('No existe la recepcion.'), 'error');
			include('includes/footer.inc');
		}
		
		$rw_t = DB_fetch_assoc($rs_t);
		$_SESSION['RecepcionTransfer']['location_from'] = $rw_t['location_from'];
		$_SESSION['RecepcionTransfer']['location_to'] = $rw_t['location_to'];
		
		$sql_alm1 = 'select locationname from locations where loccode="'.$rw_t['location_from'].'"';
		$rs_alm1  = DB_query($sql_alm1,$db);
		$rw_alm1  = DB_fetch_row($rs_alm1);
		$_SESSION['RecepcionTransfer']['location_from'] = $rw_alm1[0];
		
		$sql_alm2 = 'select locationname from locations where loccode="'.$rw_t['location_to'].'"';
		$rs_alm2  = DB_query($sql_alm2,$db);
		$rw_alm2  = DB_fetch_row($rs_alm2);
		$_SESSION['RecepcionTransfer']['location_to'] = $rw_alm2[0];
		
		
		$csv = '';
		
		{//Encabezado
			$csv .= '"Envio No: "'.','.'"'.$RecepcionID.'"';
			$csv .= "\n\n";
			
			//Se imprime la información del vale
			$csv .= '"Almacen Origen"'.','.'"'.$_SESSION['RecepcionTransfer']['location_from'].'"';
			$csv .= "\n";
			$csv .= '"Almacen Destino"'.','.'"'.$_SESSION['RecepcionTransfer']['location_to'].'"';
			$csv .= "\n";
			$csv .= '"Fecha"'.','.'"'.ConvertSQLDate($_SESSION['RecepcionTransfer']['fecha_recepcion']).'"';
			$csv .= "\n\n";
			
			$csv .= '"'._('Codigo').'"'.','.'"'._('Codigo de Barras').'"'.','.'"'._('Lote').'"'.','.'"'._('Descripcion').'"'.','.'"'._('Cantidad Enviada').'"'.','.'"'._('Cantidad Recibida').'"'.','.'"'._('Motivo').'"';
			$csv .= "\n";
		}


		$OrderTotal = 0;
		while ($POLine=DB_fetch_array($result)){

			$csv .= '"'.$POLine['stockid'].'"'.','.'"'.$POLine['barcode'].'"'.','.'"'.$POLine['serialno'].'"'.','.'"'.$POLine['description'].'"'.','.'"'.$POLine['qty_envio'].'"'.','.'"'.$POLine['qty_recibida'].'"'.','.'"'.$POLine['motivo_recibo'].'"';
			$csv .= "\n";

		} //end while there are line items to print out

	} /*end if there are order details to show on the order*/
//} /* end of check to see that there was an order selected to print */


	$len = strlen($csv);
	header('Content-type: application/xls');
	header('Content-Length: ' . $len);
	header('Content-Disposition: inline; filename=RecepcionLote.csv');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	echo $csv;

?>
