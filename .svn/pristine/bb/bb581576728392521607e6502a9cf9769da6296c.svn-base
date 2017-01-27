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


$TransferID = $_GET['TransferID'];

if(!isset($_GET['TransferID']) || $_GET['TransferID']==''){
	prnMsg(_('La p&aacute;gina debe ser llamada con el n&uacute;mero de env&iacute;o'),'warn');
	include('includes/footer.inc');
	exit;
}

$line_height=16;
   /* Then there's an order to print and its not been printed already (or its been flagged for reprinting)
   Now ... Has it got any line items */

   $PageNumber = 1;
   $ErrMsg = _('There was a problem retrieving the line details for order number') . ' ' . $OrderNo . ' ' .
		_('from the database');
	
	$sql_grupos = 'select stockmaster.id_agrupador as id_grupo, rh_stock_grupo.clave, rh_stock_grupo.nombre, 
				   rh_transfer_lote_preview_details.id as did, rh_transfer_lote_preview_details.stockid, rh_transfer_lote_preview_details.barcode, rh_transfer_lote_preview_details.serialno, qty_envio, rh_transfer_lote_preview_details.qty_recibida, 
				   stockmaster.description, loctransfer_id 
				   from rh_transfer_lote_preview_details 
				   inner join stockmaster on rh_transfer_lote_preview_details.stockid=stockmaster.stockid 
				   inner join rh_stock_grupo on stockmaster.id_agrupador=rh_stock_grupo.clave
				   where transfer_lote_id='.$_GET['TransferID'].'  
				   order by rh_stock_grupo.nombre asc';
		
   $result=DB_query($sql_grupos,$db);

   if (DB_num_rows($result)>0){
	   /*Yes there are line items to start the ball rolling with a page header */
		
		$sql_t = 'select * from rh_transfer_lote_preview where id='.$_GET['TransferID'];
		$rs_t = DB_query($sql_t,$db);
		if(!DB_num_rows($rs_t)){
			prnMsg( _('No existe la transferencia que quiere recibir.'), 'error');
			include('includes/footer.inc');
		}
		
		$rw_t = DB_fetch_assoc($rs_t);
		$_SESSION['RecepcionTransfer']['TransferID'] = $_GET['TransferID'];
		$_SESSION['RecepcionTransfer']['fecha_envio'] = $rw_t['fecha_envio'];
		$_SESSION['RecepcionTransfer']['userid_envio'] = $rw_t['userid_envio'];
		
		$sql_alm1 = 'select locationname from locations where loccode="'.$rw_t['location_from'].'"';
		$rs_alm1  = DB_query($sql_alm1,$db);
		$rw_alm1  = DB_fetch_row($rs_alm1);
		$_SESSION['RecepcionTransfer']['location_from'] = $rw_alm1[0];
		
		$sql_alm2 = 'select locationname from locations where loccode="'.$rw_t['location_to'].'"';
		$rs_alm2  = DB_query($sql_alm2,$db);
		$rw_alm2  = DB_fetch_row($rs_alm2);
		$_SESSION['RecepcionTransfer']['location_to'] = $rw_alm2[0];
		
		$_SESSION['RecepcionTransfer']['comentario'] = $rw_t['comentario'];
		
		$csv = '';
		
		{//Encabezado
			$csv .= '"Envio No: "'.','.'"'.$TransferID.'"';
			$csv .= "\n\n";
			
			//Se imprime la información del vale
			$csv .= '"Almacen Origen"'.','.'"'.$_SESSION['RecepcionTransfer']['location_from'].'"';
			$csv .= "\n";
			$csv .= '"Almacen Destino"'.','.'"'.$_SESSION['RecepcionTransfer']['location_to'].'"';
			$csv .= "\n";
			$csv .= '"Fecha"'.','.'"'.ConvertSQLDate($_SESSION['RecepcionTransfer']['fecha_envio']).'"';
			$csv .= "\n\n";
			
			$csv .= '"Comentarios"'.','.'"'.$_SESSION['RecepcionTransfer']['comentario'].'"';
			$csv .= "\n\n";
			$csv .= '"'._('Codigo').'"'.','.'"'._('Codigo de Barras').'"'.','.'"'._('Lote').'"'.','.'"'._('Descripcion').'"'.','.'"'._('Quantity').'"';
			$csv .= "\n";
		}


		$OrderTotal = 0;
		while ($POLine=DB_fetch_array($result)){

			$csv .= '"'.$POLine['stockid'].'"'.','.'"'.$POLine['barcode'].'"'.','.'"'.$POLine['serialno'].'"'.','.'"'.$POLine['description'].'"'.','.'"'.$POLine['qty_envio'].'"';
			$csv .= "\n";

		} //end while there are line items to print out

	} /*end if there are order details to show on the order*/
//} /* end of check to see that there was an order selected to print */


	$len = strlen($csv);
	header('Content-type: application/xls');
	header('Content-Length: ' . $len);
	header('Content-Disposition: inline; filename=EnvioLote.csv');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	echo $csv;

?>
