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

$PaperSize = 'A4_Landscape';
include('includes/PDFStarter.php');

$RecepcionID = $_GET['RecepcionID'];

if(!isset($_GET['RecepcionID']) || $_GET['RecepcionID']==''){
	prnMsg(_('La p&aacute;gina debe ser llamada con el n&uacute;mero de env&iacute;o'),'warn');
	include('includes/footer.inc');
	exit;
}

$pdf->addinfo('Title', _('Recepcion') );
$pdf->addinfo('Subject', _('Recepcion no.').' ' . $RecepcionID);


$line_height=16;
   /* Then there's an order to print and its not been printed already (or its been flagged for reprinting)
   Now ... Has it got any line items */

   $PageNumber = 1;
   $ErrMsg = _('There was a problem retrieving the line details for order number') . ' ' . $OrderNo . ' ' .
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
		
		{//Encabezado
			$YPos = $Page_Height - $Top_Margin - ($line_height*2);
			//$pdf->addJpegFromFile($_SESSION['LogoFile'],$Left_Margin,$YPos,0,60);
			$FontSize=15;
			$XPos = $Page_Width/2 - 30;
			$pdf->addText($XPos,$YPos+$line_height,$FontSize, _('Recepcion No.'). ' ' . $RecepcionID);
			
			$YPos -= ($line_height*3);
			$FontSize=10;
			$XPos = $Page_Width-$Right_Margin-50;
			$pdf->addText($XPos,$YPos +40, $FontSize, _('Page') . ': ' .$PageNumber);
			
			//Se imprime la información del vale
			$XPos = $Left_Margin;
			$YPos -= 1*$line_height;
			//$pdf->addText($Left_Margin,$YPos, $FontSize, 'Empleado:' . ' ' . $datos_enc['nombre'].' '.$datos_enc['apellido_paterno'],' '.$datos_enc['apellido_materno']);
			$pdf->addText($Left_Margin,$YPos-(0.8*$line_height), $FontSize, 'Almacen Origen:'. ' ' . $_SESSION['RecepcionTransfer']['location_from']);
			$pdf->addText($Left_Margin,$YPos-(1.6*$line_height), $FontSize, 'Almacen Destino:'. ' ' . $_SESSION['RecepcionTransfer']['location_to']);
			//$pdf->addText($Left_Margin,$YPos-(2.4*$line_height), $FontSize, 'Comentario:'. ' ' . $_SESSION['RecepcionTransfer']['comentario']);
			$pdf->addText($Left_Margin,$YPos-(3.2*$line_height), $FontSize, 'Fecha:'. ' ' . ConvertSQLDate($_SESSION['RecepcionTransfer']['fecha_recepcion']));
			
			$YPos -= 6;
			//$XPos = $Page_Width/2 + 25;
			
			$YPos -= $line_height*6;
			/*Set up headings */
			$FontSize=10;
			$pdf->addText($Left_Margin+1,$YPos, $FontSize, _('Code') );
			$pdf->addText($Left_Margin+75,$YPos, $FontSize, _('Barcode') );
			$pdf->addText($Left_Margin+75+85,$YPos, $FontSize, _('Lote') );
			$pdf->addText($Left_Margin+75+85+65,$YPos, $FontSize, _('Item Description') );
			$pdf->addText($Left_Margin+75+85+65+250,$YPos, $FontSize, _('Cant. Enviada') );
			$pdf->addText($Left_Margin+75+85+65+250+75,$YPos, $FontSize, _('Cant. Recibida') );
			$pdf->addText($Left_Margin+75+85+65+250+75+75,$YPos, $FontSize, _('Motivo') );
			
			$YPos-=$line_height*2;
		}


		$OrderTotal = 0;
		while ($POLine=DB_fetch_array($result)){

			$LeftOvers = $pdf->addTextWrap($Left_Margin+1,$YPos,94,$FontSize,$POLine['stockid'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+74,$YPos,270,$FontSize,$POLine['barcode'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+74+84,$YPos,270,$FontSize,$POLine['serialno'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+74+84+64,$YPos,270,$FontSize,$POLine['description'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+74+84+64+231,$YPos,85,$FontSize,$POLine['qty_envio'], 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+74+84+64+231+74,$YPos,85,$FontSize,$POLine['qty_recibida'], 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+74+84+64+231+74+34+62,$YPos,0,$FontSize,$POLine['motivo_recibo'], 'left');
		
			//$LeftOvers = $pdf->addTextWrap($Left_Margin+1+94+270+85+30,$YPos,37,$FontSize,number_format($precio,2), 'right');
			
			if (strlen($LeftOvers)>1){
				$LeftOvers = $pdf->addTextWrap($Left_Margin+1+94,$YPos-$line_height,270,$FontSize,$LeftOvers, 'left');
				$YPos-=$line_height;
			}

			if ($YPos-$line_height <= $Bottom_Margin){
				/* We reached the end of the page so finsih off the page and start a newy */
				$PageNumber++;
				{//Encabezado
					$YPos = $Page_Height - $Top_Margin - ($line_height*2);
					//$pdf->addJpegFromFile($_SESSION['LogoFile'],$Left_Margin,$YPos,0,60);
					$FontSize=15;
					$XPos = $Page_Width/2 - 30;
					$pdf->addText($XPos,$YPos+$line_height,$FontSize, _('Recepcion no.'). ' ' . $RecepcionID);
					
					$YPos -= ($line_height*3);
					$FontSize=10;
					$XPos = $Page_Width-$Right_Margin-50;
					$pdf->addText($XPos,$YPos +40, $FontSize, _('Page') . ': ' .$PageNumber);
					
					//Se imprime la información del vale
					$XPos = $Left_Margin;
					$YPos -= 1*$line_height;
					//$pdf->addText($Left_Margin,$YPos, $FontSize, 'Empleado:' . ' ' . $datos_enc['nombre'].' '.$datos_enc['apellido_paterno'],' '.$datos_enc['apellido_materno']);
					$pdf->addText($Left_Margin,$YPos-(0.8*$line_height), $FontSize, 'Almacen Origen:'. ' ' . $_SESSION['RecepcionTransfer']['location_from']);
					$pdf->addText($Left_Margin,$YPos-(1.6*$line_height), $FontSize, 'Almacen Destino:'. ' ' . $_SESSION['RecepcionTransfer']['location_to']);
					//$pdf->addText($Left_Margin,$YPos-(2.4*$line_height), $FontSize, 'Comentario:'. ' ' . $_SESSION['RecepcionTransfer']['comentario']);
					$pdf->addText($Left_Margin,$YPos-(3.2*$line_height), $FontSize, 'Fecha:'. ' ' . ConvertSQLDate($_SESSION['RecepcionTransfer']['fecha_recepcion']));
					
					$YPos -= 6;
					
					$YPos -= $line_height*6;
					/*Set up headings */
					$FontSize=10;
					$pdf->addText($Left_Margin+1,$YPos, $FontSize, _('Code') );
					$pdf->addText($Left_Margin+75,$YPos, $FontSize, _('Barcode') );
					$pdf->addText($Left_Margin+75+85,$YPos, $FontSize, _('Lote') );
					$pdf->addText($Left_Margin+75+85+65,$YPos, $FontSize, _('Item Description') );
					$pdf->addText($Left_Margin+75+85+65+250,$YPos, $FontSize, _('Cant. Enviada') );
					$pdf->addText($Left_Margin+75+85+65+250+75,$YPos, $FontSize, _('Cant. Recibida') );
					$pdf->addText($Left_Margin+75+85+65+250+75+75,$YPos, $FontSize, _('Motivo') );
					
					$YPos-=$line_height*2;
				}
			} //end if need a new page headed up

			/*increment a line down for the next line item */
			$YPos -= $line_height;

		} //end while there are line items to print out

		if ($YPos-$line_height <= $Bottom_Margin){ // need to ensure space for totals
				$PageNumber++;
			{//Encabezado
				$YPos = $Page_Height - $Top_Margin - ($line_height*2);
				//$pdf->addJpegFromFile($_SESSION['LogoFile'],$Left_Margin,$YPos,0,60);
				$FontSize=15;
				$XPos = $Page_Width/2 - 30;
				$pdf->addText($XPos,$YPos+$line_height,$FontSize, _('Recepcion no.'). ' ' . $RecepcionID);
				
				$YPos -= ($line_height*3);
				$FontSize=10;
				$XPos = $Page_Width-$Right_Margin-50;
				$pdf->addText($XPos,$YPos +40, $FontSize, _('Page') . ': ' .$PageNumber);
				
				//Se imprime la información del vale
				$XPos = $Left_Margin;
				$YPos -= 1*$line_height;
				//$pdf->addText($Left_Margin,$YPos, $FontSize, 'Empleado:' . ' ' . $datos_enc['nombre'].' '.$datos_enc['apellido_paterno'],' '.$datos_enc['apellido_materno']);
				$pdf->addText($Left_Margin,$YPos-(0.8*$line_height), $FontSize, 'Almacen Origen:'. ' ' . $_SESSION['RecepcionTransfer']['location_from']);
				$pdf->addText($Left_Margin,$YPos-(1.6*$line_height), $FontSize, 'Almacen Destino:'. ' ' . $_SESSION['RecepcionTransfer']['location_to']);
				//$pdf->addText($Left_Margin,$YPos-(2.4*$line_height), $FontSize, 'Comentario:'. ' ' . $_SESSION['RecepcionTransfer']['comentario']);
				$pdf->addText($Left_Margin,$YPos-(3.2*$line_height), $FontSize, 'Fecha:'. ' ' . ConvertSQLDate($_SESSION['RecepcionTransfer']['fecha_envio']));
				
				
				$YPos -= 6;
				
				$YPos -= $line_height*6;
				/*Set up headings */
				$FontSize=10;
				$pdf->addText($Left_Margin+1,$YPos, $FontSize, _('Code') );
				$pdf->addText($Left_Margin+75,$YPos, $FontSize, _('Barcode') );
				$pdf->addText($Left_Margin+75+85,$YPos, $FontSize, _('Lote') );
				$pdf->addText($Left_Margin+75+85+65,$YPos, $FontSize, _('Item Description') );
				$pdf->addText($Left_Margin+75+85+65+250,$YPos, $FontSize, _('Cant. Enviada') );
				$pdf->addText($Left_Margin+75+85+65+250+75,$YPos, $FontSize, _('Cant. Recibida') );
				$pdf->addText($Left_Margin+75+85+65+250+75+75,$YPos, $FontSize, _('Motivo') );
				
				$YPos-=$line_height*2;
			}
		} //end if need a new page headed up


		//$YPos = $Bottom_Margin + $line_height;
		$YPos = $Bottom_Margin + $line_height*2;

	} /*end if there are order details to show on the order*/
//} /* end of check to see that there was an order selected to print */


	$buf = $pdf->output();
	$len = strlen($buf);
	header('Content-type: application/pdf');
	header('Content-Length: ' . $len);
	header('Content-Disposition: inline; filename=Recepcion.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	$pdf->Output('Recepcion.pdf','I');

?>
