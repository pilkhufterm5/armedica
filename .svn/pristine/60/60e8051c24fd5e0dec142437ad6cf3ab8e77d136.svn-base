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

$TransferID = $_GET['TransferID'];

if(!isset($_GET['TransferID']) || $_GET['TransferID']==''){
	prnMsg(_('La p&aacute;gina debe ser llamada con el n&uacute;mero de env&iacute;o'),'warn');
	include('includes/footer.inc');
	exit;
}

$pdf->addinfo('Title', _('Env&iacute;o') );
$pdf->addinfo('Subject', _('Env&iacute;o no.').' ' . $TransferID);


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
		
		{//Encabezado
			$YPos = $Page_Height - $Top_Margin - ($line_height*2);
			//$pdf->addJpegFromFile($_SESSION['LogoFile'],$Left_Margin,$YPos,0,60);
			$FontSize=15;
			$XPos = $Page_Width/2 - 30;
			$pdf->addText($XPos,$YPos+$line_height,$FontSize, _('Envio No.'). ' ' . $TransferID);
			
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
			
			$XPos = $Page_Width/2 + 25;
			$pdf->addText($XPos+18,$YPos, $FontSize, 'Comentarios:');
			$YPos -= 12;
			$desc = codeWrap($_SESSION['RecepcionTransfer']['comentario'],50,'@');
			$lineas = explode('@',$desc);
			$acum = 0;
			for($lns=0;$lns<count($lineas);$lns++){
				//if($lns == 0){
					//$LeftOvers = $pdf->addTextWrap($XPos+13,$YPos + $acum,5,$FontSize,'-');
				//}
				$LeftOvers = $pdf->addTextWrap($XPos+18,$YPos + $acum,282,$FontSize,strip_tags($lineas[$lns]));
				$acum -= 10;
			}
			
			
			$YPos -= $line_height*6;
			/*Set up headings */
			$FontSize=10;
			$pdf->addText($Left_Margin+1,$YPos, $FontSize, _('Code') );
			$pdf->addText($Left_Margin+85,$YPos, $FontSize, _('Barcode') );
			$pdf->addText($Left_Margin+85+85,$YPos, $FontSize, _('Lote') );
			$pdf->addText($Left_Margin+85+85+85,$YPos, $FontSize, _('Item Description') );
			$pdf->addText($Left_Margin+85+85+85+400,$YPos, $FontSize, _('Quantity') );
			
			$YPos-=$line_height*2;
		}


		$OrderTotal = 0;
		while ($POLine=DB_fetch_array($result)){

			$LeftOvers = $pdf->addTextWrap($Left_Margin+1,$YPos,94,$FontSize,$POLine['stockid'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+84,$YPos,270,$FontSize,$POLine['barcode'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+84+84,$YPos,270,$FontSize,$POLine['serialno'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+84+84+84,$YPos,270,$FontSize,$POLine['description'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+84+84+84+350,$YPos,85,$FontSize,$POLine['qty_envio'], 'right');
			
		//SAINTS
		/*if($POLine['materialcost']>0)
			$precio=$POLine['materialcost'];
		else{
			$sql_pd = "select price from purchdata where stockid='".$POLine['stockid']."' order by preferred desc limit 1";
			$rs_pd = DB_query($sql_pd,$db);
			$rw_pd = DB_fetch_array($rs_pd);
			$precio = $rw_pd['price'];	
		}*/
			
		
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
					$pdf->addText($XPos,$YPos+$line_height,$FontSize, _('Envio no.'). ' ' . $TransferID);
					
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
					
					$XPos = $Page_Width/2 + 25;
					$pdf->addText($XPos+18,$YPos, $FontSize, 'Comentarios:');
					$YPos -= 12;
					$desc = codeWrap($_SESSION['RecepcionTransfer']['comentario'],50,'@');
					$lineas = explode('@',$desc);
					$acum = 0;
					for($lns=0;$lns<count($lineas);$lns++){
						//if($lns == 0){
							//$LeftOvers = $pdf->addTextWrap($XPos+13,$YPos + $acum,5,$FontSize,'-');
						//}
						$LeftOvers = $pdf->addTextWrap($XPos+18,$YPos + $acum,282,$FontSize,strip_tags($lineas[$lns]));
						$acum -= 10;
					}
					
					$YPos -= $line_height*6;
					/*Set up headings */
					$FontSize=10;
					$pdf->addText($Left_Margin+1,$YPos, $FontSize, _('Code') );
					$pdf->addText($Left_Margin+85,$YPos, $FontSize, _('Barcode') );
					$pdf->addText($Left_Margin+85+85,$YPos, $FontSize, _('Lote') );
					$pdf->addText($Left_Margin+85+85+85,$YPos, $FontSize, _('Item Description') );
					$pdf->addText($Left_Margin+85+85+85+400,$YPos, $FontSize, _('Quantity') );
					
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
				$pdf->addText($XPos,$YPos+$line_height,$FontSize, _('Envio no.'). ' ' . $TransferID);
				
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
			
				$XPos = $Page_Width/2 + 25;
				$pdf->addText($XPos+18,$YPos, $FontSize, 'Comentarios:');
				$YPos -= 12;
				$desc = codeWrap($_SESSION['RecepcionTransfer']['comentario'],50,'@');
				$lineas = explode('@',$desc);
				$acum = 0;
				for($lns=0;$lns<count($lineas);$lns++){
					//if($lns == 0){
						//$LeftOvers = $pdf->addTextWrap($XPos+13,$YPos + $acum,5,$FontSize,'-');
					//}
					$LeftOvers = $pdf->addTextWrap($XPos+18,$YPos + $acum,282,$FontSize,strip_tags($lineas[$lns]));
					$acum -= 10;
				}
				
				$YPos -= $line_height*6;
				/*Set up headings */
				$FontSize=10;
				$pdf->addText($Left_Margin+1,$YPos, $FontSize, _('Code') );
				$pdf->addText($Left_Margin+85,$YPos, $FontSize, _('Barcode') );
				$pdf->addText($Left_Margin+85+85,$YPos, $FontSize, _('Lote') );
				$pdf->addText($Left_Margin+85+85+85,$YPos, $FontSize, _('Item Description') );
				$pdf->addText($Left_Margin+85+85+85+400,$YPos, $FontSize, _('Quantity') );
				
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
	header('Content-Disposition: inline; filename=Envio.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	$pdf->Output('Envio.pdf','I');

?>
