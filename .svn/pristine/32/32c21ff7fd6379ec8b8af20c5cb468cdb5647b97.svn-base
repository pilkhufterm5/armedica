<?php
// TEMPLATE 1 REMISION GRANDE
// bowikaxu - realhost - june 2007

/*
 * iJPe
 * 2010-03-20
 * Se realizo modificacion sobre el formato de impresion de la remision,
 * ya que la impresion se realizaria directamente desde el sistema sin formato fisico impreso
 *
 * Solicitado por Sergio
 */

	include ('includes/class.pdf.php');
	require_once('Numbers/Words.php');
	//Juan
	//Realhost
	//04-Ago-09	
	include('includes/DefineCartClass.php');
	/*
	All this lot unnecessary if session.inc included at the start
	previously it was not possible to start a session before initiating a class
	include('config.php');
	include('includes/ConnectDB.inc');
	include('includes/GetConfig.php');
	include('includes/DateFunctions.inc');
	if (isset($SessionSavePath)){
		session_save_path($SessionSavePath);
	}
	ini_set('session.gc_Maxlifetime',$SessionLifeTime);
	ini_set('max_execution_time',$MaximumExecutionTime);

	session_start();
	*/
	
	$Page_Width=612; // ancho
	$Page_Height=792; // largo
	$Top_Margin=100;
	$Bottom_Margin=20;
	$Left_Margin=55;
	$Right_Margin=100;
	
	$y_y = -1*(15*2);

	$PageSize = array(0,0,$Page_Width,$Page_Height);
	$pdf =  new Cpdf($PageSize);       
        //$pdf->SetFont('Courier');
        //$pdf->SetFont('Times');
	$pdf->selectFont('helvetica');
	$pdf->addinfo('Author','webERP ' . $Version);
	$pdf->addinfo('Creator','webERP http://www.weberp.org');

	$FirstPage = true;
	/*
	if ($InvOrCredit=='Invoice'){
		$pdf->addinfo('Title',_('Sales Invoice') . ' ' . $FromTransNo . ' to ' . $_POST['ToTransNo']);
		$pdf->addinfo('Subject',_('Invoices from') . ' ' . $FromTransNo . ' ' . _('to') . ' ' . $_POST['ToTransNo']);
	} else {
		$pdf->addinfo('Title',_('Sales Credit Note') );
		$pdf->addinfo('Subject',_('Credit Notes from') . ' ' . $FromTransNo . ' ' . _('to') . ' ' . $_POST['ToTransNo']);
	}
	*/
	$line_height=14;

	//--------
        $IVA = new cart;

	while ($FromTransNo <= $_POST['ToTransNo']){

	/*retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */
		
	// obtener el numero de transaccion de la remision pedida
		
		if ($InvOrCredit=='Invoice') {
			$sql = 'SELECT debtortrans.trandate,		DATE_FORMAT(debtortrans.trandate, "%m") as mm,	DATE_FORMAT(debtortrans.trandate, "%d") as dd, 	DATE_FORMAT(debtortrans.trandate, "%Y") as yy,
				
				locations.taxprovinceid,
                               salesman.salesmanname,
				custbranch.taxgroupid,
				debtortrans.transno,
				debtortrans.ovamount,
				debtortrans.ovdiscount,
				debtortrans.ovfreight,
				debtortrans.ovgst,
				debtortrans.rate,
				debtortrans.invtext,
				debtortrans.consignment,
				debtorsmaster.name,
				debtorsmaster.address1,
				debtorsmaster.address2,
				debtorsmaster.address3,
				debtorsmaster.address4,
				debtorsmaster.address5,
				debtorsmaster.address6,
				debtorsmaster.currcode,
				debtorsmaster.invaddrbranch,
				debtorsmaster.taxref,
				paymentterms.terms,
				salesorders.deliverto,
				salesorders.deladd1,
				salesorders.deladd2,
				salesorders.deladd3,
				salesorders.deladd4,
				salesorders.deladd5,
				salesorders.deladd6,
				salesorders.customerref,
				salesorders.orderno,
				salesorders.orddate,
				locations.locationname,
                               locations.loccode,
                               locations.deladd1,
                               locations.deladd2,
                               locations.deladd3,
                               locations.deladd4,
                               locations.deladd5,
                               locations.deladd6,
                               locations.tel,
                               locations.fax,
                               locations.email,
				shippers.shippername,
				custbranch.brname,
				custbranch.braddress1,
				custbranch.braddress2,
				custbranch.braddress3,
				custbranch.braddress4,
				custbranch.braddress5,
				custbranch.braddress6,
				custbranch.brpostaddr1,
				custbranch.brpostaddr2,
				custbranch.brpostaddr3,
				custbranch.brpostaddr4,
				custbranch.brpostaddr5,
				custbranch.brpostaddr6,
				salesman.salesmanname,
				debtortrans.debtorno,
				debtortrans.branchcode
			FROM debtortrans,
				debtorsmaster,
				custbranch,
				salesorders,
				shippers,
				salesman,
				locations,
				paymentterms
			WHERE debtortrans.order_ = salesorders.orderno
			AND debtortrans.type=20000
			AND debtortrans.transno=' . $FromTransNo . '
			AND debtortrans.shipvia=shippers.shipper_id
			AND debtortrans.debtorno=debtorsmaster.debtorno
			AND debtorsmaster.paymentterms=paymentterms.termsindicator
			AND debtortrans.debtorno=custbranch.debtorno
			AND debtortrans.branchcode=custbranch.branchcode
			AND custbranch.salesman=salesman.salesmancode
			AND salesorders.fromstkloc=locations.loccode';

		if ($_POST['PrintEDI']=='No'){
			$sql = $sql . ' AND debtorsmaster.ediinvoices=0';
		}
	} else {

		$sql = 'SELECT debtortrans.trandate,		DATE_FORMAT(debtortrans.trandate, "%m") as mm,	DATE_FORMAT(debtortrans.trandate, "%d") as dd, 	DATE_FORMAT(debtortrans.trandate, "%Y") as yy,
				debtortrans.ovamount,
				debtortrans.ovdiscount,
				debtortrans.ovfreight,
				debtortrans.ovgst,
				debtortrans.rate,
				debtortrans.invtext,
				debtorsmaster.invaddrbranch,
				debtorsmaster.name,
				debtorsmaster.address1,
				debtorsmaster.address2,
				debtorsmaster.address3,
				debtorsmaster.address4,
				debtorsmaster.address5,
				debtorsmaster.address6,
				debtorsmaster.currcode,
				debtorsmaster.taxref,
                               salesman.salesmanname,
				custbranch.brname,
				custbranch.braddress1,
				custbranch.braddress2,
				custbranch.braddress3,
				custbranch.braddress4,
				custbranch.braddress5,
				custbranch.braddress6,
				custbranch.brpostaddr1,
				custbranch.brpostaddr2,
				custbranch.brpostaddr3,
				custbranch.brpostaddr4,
				custbranch.brpostaddr5,
				custbranch.brpostaddr6,
				salesman.salesmanname,
				debtortrans.debtorno,
				debtortrans.branchcode,
				paymentterms.terms
			FROM debtortrans,
				debtorsmaster,
				custbranch,
				salesman,
				paymentterms
			WHERE debtortrans.type=11
			AND debtorsmaster.paymentterms = paymentterms.termsindicator
			AND debtortrans.transno=' . $FromTransNo .'
			AND debtortrans.debtorno=debtorsmaster.debtorno
			AND debtortrans.debtorno=custbranch.debtorno
			AND debtortrans.branchcode=custbranch.branchcode
			AND custbranch.salesman=salesman.salesmancode';

		if ($_POST['PrintEDI']=='No'){
			$sql = $sql . ' AND debtorsmaster.ediinvoices=0';
		}
	   }
	   $result=DB_query($sql,$db,'','',false,false);

	   if (DB_error_no($db)!=0) {

		$title = _('Transaction Print Error Report');
		include ('includes/header.inc');

		prnMsg( _('There was a problem retrieving the invoice or credit note details for note number') . ' ' . $InvoiceToPrint . ' ' . _('from the database') . '. ' . _('To print an invoice, the sales order record, the customer transaction record and the branch record for the customer must not have been purged') . '. ' . _('To print a credit note only requires the customer, transaction, salesman and branch records be available'),'error');
		if ($debug==1){
		    prnMsg (_('The SQL used to get this information that failed was') . "<BR>" . $sql,'error');
		}
		include ('includes/footer.inc');
		exit;
	   }
	   if (DB_num_rows($result)==1){
		$myrow = DB_fetch_array($result);

		$year = $myrow['yy'];
		$ExchRate = $myrow['rate'];

                /*
                 * Juan Mtz 0.o
                 * realhost
                 * 31-Agosto-2009
                 *
                 * Se modifico la consulta para que se mostraran los productos del pedido por
                 * orden en como fueron agregados a la orden de compra
                 */

		if ($InvOrCredit=='Invoice'){

			 $sql = 'SELECT stockmoves.rh_orderline, stockmoves.stockid,
					stockmaster.taxcatid,
					stockmoves.rh_orderline,
					stockmaster.description,
					stockmaster.longdescription,					
					-stockmoves.qty as quantity,
					rh_remdetails.qty AS qty2,
					stockmoves.discountpercent,
					((1 - stockmoves.discountpercent) * stockmoves.price * ' . $ExchRate . '* rh_remdetails.qty) AS fxnet,
					(stockmoves.price * ' . $ExchRate . ') AS fxprice,
					stockmoves.narrative,
					stockmaster.units
				FROM stockmoves,
					stockmaster,
					rh_remdetails
				WHERE stockmoves.stockid = stockmaster.stockid
				AND stockmoves.type=20000
				AND rh_remdetails.stockid = stockmoves.stockid
				AND rh_remdetails.transno = ' . $FromTransNo . '
				AND rh_remdetails.reference = stockmoves.stkmoveno
				AND stockmoves.transno=' . $FromTransNo . '
				AND stockmoves.show_on_inv_crds=1 ORDER BY stockmoves.rh_orderline';
		} else {
		/* only credit notes to be retrieved */
			 $sql = 'SELECT stockmoves.rh_orderline, stockmoves.stockid,
			 		stockmaster.description,
					stockmaster.longdescription,					
					stockmoves.qty as quantity,
					rh_remdetails.qty as qty2,
					stockmoves.discountpercent,
					((1 - stockmoves.discountpercent) * stockmoves.price * ' . $ExchRate . ' * stockmoves.qty) AS fxnet,
					(stockmoves.price * ' . $ExchRate . ') AS fxprice,
					stockmoves.narrative,
					stockmaster.units
				FROM stockmoves,
					stockmaster
				WHERE stockmoves.stockid = stockmaster.stockid
				AND stockmoves.type=11
				AND stockmoves.transno=' . $FromTransNo . '
				AND stockmoves.show_on_inv_crds=1 ORDER BY stockmoves.rh_orderline';
		}

		$result=DB_query($sql,$db);
		if (DB_error_no($db)!=0) {
			$title = _('Transaction Print Error Report');
			include ('includes/header.inc');
			echo '<BR>' . _('There was a problem retrieving the remision stock movement details for remision number') . ' ' . $FromTransNo . ' ' . _('from the database');
			if ($debug==1){
			    echo '<BR>' . _('The SQL used to get this information that failed was') . "<BR>$sql";
			}
			include('includes/footer.inc');
			exit;
		}

		if (DB_num_rows($result)>0){

			$FontSize = 12;
			$PageNumber = 1;
//
			include('rh_remgdeheader1.inc.php');
			$FirstPage = False;
			//$pdf->SetTextColor(255,0,0);
			
			//Juan - Realhost - 05-Ago-2009
			//Inicializar la variable para que en caso de no aplicar algun tax aparezca 0
			$DisplayTax = 0;

                        $rh_conteo = 0;

		        while ($myrow2=DB_fetch_array($result)){

                                if($rh_conteo == 10){
                                    $rh_conteo = 1;
                                    $PageNumber++;
                                    include('rh_remgdeheader1.inc.php');
                                }else{
                                        $rh_conteo++;
                                }


				if ($YPos <= 162){
	   		        	include ('rh_remgdeheader1.inc.php');
				}
				//print_r($myrow2);
				$DisplayPrice = number_format($myrow2['fxprice'],2);
				//rleal
				//Ago 2009
				//We need to have a variable with the DisplayPrice * Discount				
//Eleazar Lara
//RealHost
//15-ago-2009
//Cambie el 4 por un 2 para que solo aparecerian dos decimales
				$rh_DisplayPrice = number_format($myrow2['fxprice']-($myrow2['fxprice']*$myrow2['discountpercent']),4);

				$DisplayQty = number_format($myrow2['qty2'],2);
				$DisplayNet = number_format($myrow2['fxnet'],2);				
				
				//Juan
				//Realhost
				//05-Ago-2009
				//Utilizamos la instancia a la clase Car, para obtener informacion de los productos con tax
				
				//$DisplayNetSF = floor(($myrow2['fxnet']*100)+1)/100;
                                $DisplayNetSF = $myrow2['fxnet'];

				$IVA->DispatchTaxProvince = $myrow['taxprovinceid'];			
				$IVA->TaxGroup = $myrow['taxgroupid'];
				$IVA->LineItems[$myrow2['rh_orderline']]->TaxCategory = $myrow2['taxcatid'];	
				$IVA->GetTaxes($myrow2['rh_orderline']);
							
				//En el siguiente If se verifica que existan productos con algun tax
				$TaxLineTotal =0; //initialise tax total for the line	
				//echo count($IVA->LineItems->Taxes);
				//print_r($IVA->LineItems->Taxes);
				
				if (count($IVA->LineItems[$myrow2['rh_orderline']]->Taxes) > 0){
					foreach ($IVA->LineItems[$myrow2['rh_orderline']]->Taxes AS $Tax) {
						
						/*
						 * iJPe		realhost	2010-01-04
						 * Modificacion a causa del cambio de IVA
						 */ 						 
						if ($year<=2009){
							$Tax->TaxRate = .15;
						}
						//	
							
						if ($Tax->TaxOnTax ==1){
							$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * ($DisplayNetSF + $TaxLineTotal));
							$TaxLineTotal += ($Tax->TaxRate * ($DisplayNetSF + $TaxLineTotal));
						} else {
							$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * $DisplayNetSF);
							$TaxLineTotal += ($Tax->TaxRate * $DisplayNetSF);
						}
						//echo $TaxLineTotal;
					}
					$DisplayTax += $TaxLineTotal; 
				}
				
				
				
				//cantidad
				$pdf->addTextWrap(415,$YPos,54,8,$DisplayQty, 'right');
				$pdf->addTextWrap(415,$YPos-396,54,8,$DisplayQty, 'right');

				//precio
				$pdf->addTextWrap(455,$YPos,72,8,$rh_DisplayPrice, 'right');
                                $pdf->addTextWrap(455,$YPos-396,72,8,$rh_DisplayPrice, 'right');

				//total
				$pdf->addTextWrap(514,$YPos,72,8,$DisplayNet, 'right');
                                $pdf->addTextWrap(514,$YPos-396,72,8,$DisplayNet, 'right');

				//Codigo
				$pdf->addTextWrap(30,$YPos,81,9-1,$myrow2['stockid'], 'left');
                                $pdf->addTextWrap(30,$YPos-396,81,9-1,$myrow2['stockid'], 'left');

//Eleazar Lara
//15-ago-2009
//Descripcion Larga, una sola linea.
			
				//descripcion
				if($myrow['rh_printnarrative']==0){
//					$pdf->y = $pdf->y - 10;
//					$pdf->x = 117;
//					$pdf->SetFontSize($FontSize);
////Eleazar Lara
////15-ago-2009
////Comente la siguiente linea
//					$pdf->MultiCell(240,$line_height,substr($myrow2['longdescription'],0,65),0,'L',0,15);
//					$YPos = ($Page_Height - $pdf->GetY()) - $line_height;

                                        $pdf->addTextWrap(117,$YPos,350,9-2,utf8_decode($myrow2['longdescription']), 'left');
                                        $pdf->addTextWrap(117,$YPos-396,350,9-2,utf8_decode($myrow2['longdescription']), 'left');
					
				}else{
//					$pdf->y = $pdf->y - 9.5;
//					$pdf->x = 117;
//					$pdf->SetFontSize($FontSize);
//					$pdf->MultiCell(240,$line_height,substr($myrow2['narrative'],0,65),0,'L',0,15);
//					$YPos = ($Page_Height - $pdf->GetY()) - $line_height;

                                        $pdf->addTextWrap(117,$YPos,350,9-2,utf8_decode($myrow2['narrative']), 'left');
                                        $pdf->addTextWrap(117,$YPos-396,350,9-2,utf8_decode($myrow2['narrative']), 'left');
				}

                                $YPos -= $line_height;
					


			} //end while there are line items to print out
		} /*end if there are stock movements to show on the invoice or credit note*/
		/*Now print out the footer and totals */


		     $DisplaySubTot = number_format($myrow['ovamount'],2);
		     $DisplayFreight = number_format($myrow['ovfreight'],2);
                     $DisplayTaxSF = $DisplayTax;
		     $DisplayTax = number_format($DisplayTax,2);
                     $DisplayTotalLetter = round($myrow['ovfreight']+$DisplayTaxSF+$myrow['ovamount'],2);
                     $DisplayTotal = number_format($DisplayTotalLetter,2);
		     $Display0Tax=number_format($CeroTax,2);

//		$sql = "SELECT currency FROM currencies WHERE currabrev = '".$myrow['currcode2']."'";
//		$curr_res = DB_query($sql,$db);
//		$currencystr = DB_fetch_array($curr_res);
//
//		$tot = explode(".",$DisplayTotalLetter);
//		$Letra = Numbers_Words::toWords($tot[0],"es");
//		if($tot[1]==0){
//		$ConLetra = $Letra.' '.$currencystr['currency']." 00/100 ".$myrow['currcode2'];
//		}else if(strlen($tot[1])>=2){
//		$ConLetra = $Letra.' '.$currencystr['currency'].' '.$tot[1]."/100 ".$myrow['currcode2'];
//		}else {
//		$ConLetra = $Letra.' '.$currencystr['currency'].' '.$tot[1]."0/100 ".$myrow['currcode2'];
//		}
//		//total letra
//		//$pdf->addTextWrap(126,153,288,8,"(".$ConLetra.")", 'left');
//		$pdf->y = $Page_Height - 129;
//		$pdf->x = 45;
//		$pdf->SetFontSize($FontSize-1);
//		$pdf->MultiCell(432,$line_height,strtoupper($ConLetra),0,'L',0,15);

		//subtotal
                //$pdf->addTextWrap(502,95.5,72,$FontSize+1,$DisplaySubTot, 'right');
/*
		//iva %
		if($myrow['taxgroupid']==8){
			$pdf->addTextWrap(360,144,72,10,'10%', 'right');	
		}else {
			$pdf->addTextWrap(360,144,72,10,'15%', 'right');
		}
*/
 		//$pdf->addTextWrap(504,64,72,10,$DisplayFreight, 'right');
		//iva
		//$pdf->addTextWrap(502,73,72,$FontSize+1,$DisplayTax, 'right');

		//total
		//$pdf->addTextWrap(502,50.5,72,$FontSize+1,$DisplayTotal, 'right');


                //SUBTOTAL
		$pdf->addTextWrap(476,450,54,8, _('SubTotal').':', 'right');
		$pdf->addTextWrap(476,54,54,8, _('SubTotal').':', 'right');
		$pdf->addTextWrap(532,450,54,8, $DisplaySubTot, 'right');
		$pdf->addTextWrap(532,54,54,8, $DisplaySubTot, 'right');
		//IVA
		$pdf->addTextWrap(476,436,54,8, _('Tax').':', 'right');
		$pdf->addTextWrap(476,40,54,8, _('Tax').':', 'right');
		$pdf->addTextWrap(532,436,54,8, $DisplayTax, 'right');
		$pdf->addTextWrap(532,40,54,8, $DisplayTax, 'right');
		//TOTAL
		$pdf->addTextWrap(476,422,54,8, _('Total').':', 'right');
		$pdf->addTextWrap(476,26,54,8, _('Total').':', 'right');
		$pdf->addTextWrap(532,422,54,8, $DisplayTotal, 'right');
		$pdf->addTextWrap(532,26,54,8, $DisplayTotal, 'right');
		//LINEAS
		$pdf->partEllipse(589.5,454,0,90,10,10);//Curva superior derecha
		$pdf->partEllipse(589.5,58,0,90,10,10);//Curva superior derecha
		$pdf->partEllipse(486,428,180,270,10,10);//Curva inferior izquierda
		$pdf->partEllipse(486,32,180,270,10,10);//Curva inferior izquierda
		$pdf->partEllipse(486,454,90,180,10,10);//Curva superior izquierda
		$pdf->partEllipse(486,58,90,180,10,10);//Curva superior izquierda
		$pdf->partEllipse(589.5,428,270,360,10,10);//Curva inferior derecha
		$pdf->partEllipse(589.5,32,270,360,10,10);//Curva inferior derecha
		$pdf->line(486,464,589.5,464);//linea superior
		$pdf->line(486,68,589.5,68);//linea superior
		$pdf->line(486,418,589.5,418);//linea inferior
		$pdf->line(486,22,589.5,22);//linea inferior
		$pdf->line(476,428,476,454);//linea izquierda
		$pdf->line(476,32,476,58);//linea izquierda
		$pdf->line(599.5,428,599.5,454);//linea derecha
		$pdf->line(599.5,32,599.5,58);//linea derecha

		
	    } /* end of check to see that there was an invoice record to print */

	    $FromTransNo++;
	} /* end loop to print invoices */


	$pdfcode = $pdf->output();
	$len = strlen($pdfcode);

	if ($len <400){
		include('includes/header.inc');
		echo '<P>' . _('There were no transactions to print in the range selected');
		include('includes/footer.inc');
		exit;
	}

	if (isset($_GET['Email'])){ //email the invoice to address supplied
		
		include ('includes/htmlMimeMail.php');

		$mail = new htmlMimeMail();
		$filename = $_SESSION['reports_dir'] . '/' . $InvOrCredit . $_GET['FromTransNo'] . '.pdf';
		$fp = fopen($filename, 'wb');
		fwrite ($fp, $pdfcode);
		fclose ($fp);

		$attachment = $mail->getFile($filename);
		$mail->setText(_('Please find attached') . ' ' . $InvOrCredit . ' ' . $_GET['FromTransNo'] );
		$mail->SetSubject($InvOrCredit . ' ' . $_GET['FromTransNo']);
		$mail->addAttachment($attachment, $filename, 'application/pdf');
		$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
		$result = $mail->send(array($_GET['Email']));

		unlink($filename); //delete the temporary file

		$title = _('Emailing') . ' ' .$InvOrCredit . ' ' . _('Number') . ' ' . $FromTransNo;
		include('includes/header.inc');
		echo "<P>$InvOrCredit " . _('number') . ' ' . $_GET['FromTransNo'] . ' ' . _('has been emailed to') . ' ' . $_GET['Email'];
		include('includes/footer.inc');
		exit;

	} else {
		header('Content-type: application/pdf');
		header('Content-Length: ' . $len);
		header('Content-Disposition: inline; filename=Customer_trans.pdf');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');

		$pdf->Stream();
	}
	
	?>
