<?php

// IMPRESION DE REMISIONES CHICAS
// bowikaxu - realhost - june 2007

	include ('includes/class.pdf.php');
	
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
	
////	$Page_Width=612; // ancho
////	$Page_Height=396; // largo
////	$Top_Margin=100;
////	$Bottom_Margin=10;
////	$Left_Margin=40;
////	$Right_Margin=20;
        $Page_Width=612; // horizontal
        $Page_Height=554; // vertical
        $Top_Margin=30;
        $Bottom_Margin=30;
        $Left_Margin=30;
        $Right_Margin=30;

	$PageSize = array(0,0,$Page_Width,$Page_Height);
	$pdf = & new Cpdf($PageSize);
	$pdf->selectFont('helvetica');
	$pdf->addinfo('Author','webERP ' . $Version);
	$pdf->addinfo('Creator','webERP http://www.weberp.org');

	$FirstPage = true;

	$line_height=12;

	while ($FromTransNo <= $_POST['ToTransNo']){

	/*retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */
		
	// obtener el numero de transaccion de la remision pedida
		
		if ($InvOrCredit=='Credit') {
/*
				salesorders.deliverto,
				salesorders.deladd1,
				salesorders.deladd2,
				salesorders.deladd3,
				salesorders.deladd4,
				salesorders.deladd5,
				salesorders.deladd6,
				salesorders.customerref,
*/
			$sql = 'SELECT debtortrans.trandate,		
			DATE_FORMAT(trandate, "%m") as mm,	DATE_FORMAT(trandate, "%d") as dd, 	DATE_FORMAT(trandate, "%y") as yy,
                                       debtortrans.transno,
                                       debtortrans.order_,
                                        debtortrans.ovamount,
			   		debtortrans.id as ID,
					debtortrans.ovdiscount, 
					debtortrans.ovfreight, 
					debtortrans.reference,
					debtortrans.ovgst, 
					debtortrans.rate, 
					debtortrans.invtext, 
					debtorsmaster.name, 
					debtorsmaster.name2, 
					debtorsmaster.taxref, 
					debtorsmaster.address1, 
					debtorsmaster.address2, 
					debtorsmaster.address3, 
					debtorsmaster.address4, 
					debtorsmaster.address5,
					debtorsmaster.address6,
					debtorsmaster.currcode, 
					custbranch.brname, 
					custbranch.braddress1, 
					custbranch.braddress2, 
					custbranch.braddress3, 
					custbranch.braddress4, 
					salesman.salesmanname, 
					debtortrans.debtorno 
				FROM debtortrans, 
					debtorsmaster, 
					custbranch, 
					salesman 
				WHERE debtortrans.type=11
				AND debtortrans.transno=' . $FromTransNo . '
				AND debtortrans.debtorno=debtorsmaster.debtorno
				AND debtortrans.debtorno=custbranch.debtorno 
				AND debtortrans.branchcode=custbranch.branchcode 
				AND custbranch.salesman=salesman.salesmancode';
		if ($_POST['PrintEDI']=='No'){
			$sql = $sql . ' AND debtorsmaster.ediinvoices=0';
		}
	} else {

		$sql = 'SELECT debtortrans.trandate,		
		DATE_FORMAT(trandate, "%m") as mm,	DATE_FORMAT(trandate, "%d") as dd, 	DATE_FORMAT(trandate, "%y") as yy,
                                       debtortrans.transno,
                                       debtortrans.order_,
                                        debtortrans.ovamount,
			   		debtortrans.id as ID,
					debtortrans.ovdiscount, 
					debtortrans.ovfreight, 
					debtortrans.reference,
					debtortrans.ovgst, 
					debtortrans.rate, 
					debtortrans.invtext, 
					debtorsmaster.name, 
					debtorsmaster.name2, 
					debtorsmaster.taxref,
					debtorsmaster.address1, 
					debtorsmaster.address2, 
					debtorsmaster.address3, 
					debtorsmaster.address4, 
					debtorsmaster.address5,
					debtorsmaster.address6,
					debtorsmaster.currcode, 
					custbranch.brname, 
					custbranch.braddress1, 
					custbranch.braddress2, 
					custbranch.braddress3, 
					custbranch.braddress4, 
					salesman.salesmanname, 
					debtortrans.debtorno 
				FROM debtortrans, 
					debtorsmaster, 
					custbranch, 
					salesman 
				WHERE debtortrans.type=11
				AND debtortrans.transno=' . $FromTransNo . '
				AND debtortrans.debtorno=debtorsmaster.debtorno
				AND debtortrans.debtorno=custbranch.debtorno 
				AND debtortrans.branchcode=custbranch.branchcode 
				AND custbranch.salesman=salesman.salesmancode';

	   }
	   $result=DB_query($sql,$db,'','',false,false);

	   if (DB_error_no($db)!=0) {

		$title = _('Transaction Print Error Report');
		include ('includes/header.inc');

		prnMsg( _('There was a problem retrieving the credit or credit note details for note number') . ' ' . $InvoiceToPrint . ' ' . _('from the database') . '. ' . _('To print an invoice, the sales order record, the customer transaction record and the branch record for the customer must not have been purged') . '. ' . _('To print a credit note only requires the customer, transaction, salesman and branch records be available'),'error');
		if ($debug==1){
		    prnMsg (_('The SQL used to get this information that failed was') . "<BR>" . $sql,'error');
		}
		include ('includes/footer.inc');
		exit;
	   }
	   if (DB_num_rows($result)==1){
		$myrow = DB_fetch_array($result);

                //iJPe
                //Realhost
                //2010-02-18
                //Modificacion para obtener el numero de factura
                $sqlNumFac = "SELECT rh_invoicesreference.extinvoice FROM debtortrans INNER JOIN rh_invoicesreference ON debtortrans.transno = rh_invoicesreference.intinvoice WHERE debtortrans.type = 10 AND debtortrans.order_=".$myrow['order_'];
                $resNumFac = DB_query($sqlNumFac, $db);
                $rowNumFac = DB_fetch_array($resNumFac);

                $numberFact = $rowNumFac['extinvoice'];

		$ExchRate = $myrow['rate'];

                $invText = $myrow['reference'];

		if ($InvOrCredit=='Credit'){

			 $sql ='SELECT stockmoves.stockid,
				   		stockmaster.description, 
				   		stockmoves.narrative,
						stockmoves.qty as quantity, 
						stockmoves.discountpercent, ((1 - stockmoves.discountpercent) * stockmoves.price * ' . $ExchRate . ' * stockmoves.qty) AS fxnet,
						(stockmoves.price * ' . $ExchRate . ') AS fxprice,
						stockmaster.units 
					FROM stockmoves, 
						stockmaster 
					WHERE stockmoves.stockid = stockmaster.stockid 
					AND stockmoves.type=11 
					AND stockmoves.transno=' . $FromTransNo . '
					AND stockmoves.show_on_inv_crds=1';
		} else {
		/* only credit notes to be retrieved */
			 $sql ='SELECT stockmoves.stockid,
				   		stockmaster.description, 
				   		stockmoves.narrative,
						stockmoves.qty as quantity, 
						stockmoves.discountpercent, ((1 - stockmoves.discountpercent) * stockmoves.price * ' . $ExchRate . ' * stockmoves.qty) AS fxnet,
						(stockmoves.price * ' . $ExchRate . ') AS fxprice,
						stockmaster.units 
					FROM stockmoves, 
						stockmaster 
					WHERE stockmoves.stockid = stockmaster.stockid 
					AND stockmoves.type=11 
					AND stockmoves.transno=' . $FromTransNo . '
					AND stockmoves.show_on_inv_crds=1';
		}

		$result=DB_query($sql,$db);
		if (DB_error_no($db)!=0) {
			$title = _('Transaction Print Error Report');
			include ('includes/header.inc');
			echo '<BR>' . _('There was a problem retrieving the remision stock movement details for credit note number') . ' ' . $FromTransNo . ' ' . _('from the database');
			if ($debug==1){
			    echo '<BR>' . _('The SQL used to get this information that failed was') . "<BR>$sql";
			}
			include('includes/footer.inc');
			exit;
		}

		if (DB_num_rows($result)>0){

			$FontSize = 10;
			$PageNumber = 1;
			//$invnum = substr($myrow['reference'],8);
			$invnum = $myrow['reference']; // FAC-123-Usr-usuario
			$user2 = explode("-",$invnum);
			
			//$sql = "SELECT user_ FROM rh_usertrans WHERE order_ IN (SELECT order_ FROM debtortrans WHERE type=10 AND transno =".$user2[1].")";
			$sql = "SELECT user_ FROM rh_usertrans WHERE order_ IN (SELECT order_ FROM debtortrans WHERE type=11 AND transno ='".$FromTransNo."')";
			$usrres = DB_query($sql,$db);
			$Usr = DB_fetch_array($usrres);
			include('rh_notcredheader1.inc.php');
			$FirstPage = False;
			//$pdf->SetTextColor(255,0,0);

		        while ($myrow2=DB_fetch_array($result)){

				$DisplayPrice = number_format($myrow2['fxprice'],2);
				$DisplayQty = number_format($myrow2['quantity'],2);
				$DisplayNet = number_format($myrow2['fxnet'],2);
				$DisplayFact = $myrow2['narrative'];

				if ($myrow2['discountpercent']==0){
					$DisplayDiscount ='';
				} else {
					$DisplayDiscount = number_format($myrow2['discountpercent']*100,2) . '%';
				}

                                //total
				$pdf->addTextWrap(480,$YPos,81,$FontSize-1,$DisplayNet, 'right');

				//$numberFact = array();
				//$numberFact = explode('-',$invText);
				//$numF = explode('.',$numberFact[1]);

				//Codigo
                                //$pdf->addTextWrap(30+8+8,$YPos,81,$FontSize-1,$myrow2['stockid'], 'left');
                                $pdf->addTextWrap(30+8,$YPos,81,$FontSize-1,$myrow2['narrative'], 'left');

                                //Descripcion
                                $pdf->addTextWrap(105,$YPos,307,$FontSize-1,$myrow2['stockid']." - ".$myrow2['description'], 'left');

				
				$LeftOvers = $pdf->addTextWrap($XPos+360,$YPos,20,$FontSize,$DisplayQty,'left');
////				//$LeftOvers = $pdf->addTextWrap($XPos+50,$YPos,30,$FontSize,$myrow2['stockid'],'left');
////				$LeftOvers = $pdf->addTextWrap($XPos+35,$YPos,250,$FontSize,$myrow2['description'],'left');
////
////				if(strlen($user2[1])<=0){
////					$LeftOvers = $pdf->addTextWrap($XPos+340,$YPos,50,$FontSize,$DisplayFact,'left');
////				}else {
////					$LeftOvers = $pdf->addTextWrap($XPos+340,$YPos,50,$FontSize,$user2[1],'left');
////				}
////
				$LeftOvers = $pdf->addTextWrap($XPos+400,$YPos,50,$FontSize,$DisplayPrice,'right');
////				$LeftOvers = $pdf->addTextWrap(520,$YPos,50,$FontSize,$DisplayNet,'right');
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+553,$YPos,35,$FontSize,$myrow2['units'],'centre');
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+590,$YPos,50,$FontSize,$DisplayDiscount,'right');
				

				$YPos -= ($line_height);

				//$Narrative = $myrow2['narrative'];
				
				//if ($YPos <= $Bottom_Margin){
				if ($YPos <= 295){
					/* head up a new invoice/credit note page */
					/*draw the vertical column lines right to the bottom */
					PrintLinesToBottom ();
					include ('rh_notcredheader1.inc.php');
				} //end if need a new page headed up


			} //end while there are line items to print out
		} /*end if there are stock PrintLinesToBottommovements to show on the invoice or credit note*/

		$YPos -= $line_height;

		/* check to see enough space left to print the 4 lines for the totals/footer */
		if (($YPos-$Bottom_Margin)<(2*$line_height)){

			PrintLinesToBottom ();
			include ('rh_notcredheader1.inc.php');

		}
		/*Now print out the footer and totals */

		$DisplaySubTotal = number_format(-1*$myrow['ovamount'],2);
		$DisplayTotal = number_format(-1*($myrow['ovgst']+$myrow['ovamount']),2);
		$DisplayIVA = number_format(-1*$myrow['ovgst'],2);
		
		$FontSize = 9;
////		$LeftOvers = $pdf->addTextWrap(520,$Bottom_Margin+80,50,$FontSize,$DisplaySubTotal,'right');
////		$LeftOvers = $pdf->addTextWrap(520,$Bottom_Margin+53,50,$FontSize,$DisplayIVA,'right');
////		$LeftOvers = $pdf->addTextWrap(520,$Bottom_Margin+24,50,$FontSize,$DisplayTotal,'right');

                //subtotal
                $pdf->addTextWrap(490,323-51-20+32,81,$FontSize+1,$DisplaySubTotal, 'right');
                //iva
                $pdf->addTextWrap(490,308-51-20+32,81,$FontSize+1,$DisplayIVA, 'right');
                //total
                $pdf->addTextWrap(490,293-51-20+32,81,$FontSize+1,$DisplayTotal, 'right');

                /*
                 * iJPe
                 * 2010-04-06
                 *
                 * Modificacion para mostrar leyenda de tipo de nota de credito
                 */

                $resVerSMT = DB_query('SELECT transno FROM stockmoves WHERE stockmoves.hidemovt = 1 AND stockmoves.type=11 AND stockmoves.transno=' . $FromTransNo, $db);

                //iJPe 2010-04-09 Modificacion para imprimir texto en nota de credito
                $pdf->addTextWrap(105,313,450,$FontSize-2,$myrow['invtext'], 'left');

                //iJPe 2010-04-09 Anteriormente se imprimia leyenda a base de criterios
//                if (DB_num_rows($resVerSMT) > 0){
//                    //Leyenda para notas de credito de Diferencia en Precio
//                    $pdf->addTextWrap(105,313,150,$FontSize-2,"Diferencia en Precio", 'left');
//                }
//
//                $resVerSMT = DB_query('SELECT SUM(show_on_inv_crds-1) as suma FROM stockmoves WHERE stockmoves.hidemovt = 0 AND stockmoves.type=11 AND stockmoves.transno=' . $FromTransNo, $db);
//		if (DB_num_rows($resVerSMT) > 0){
//                    $rowSMT = DB_fetch_array($resVerSMT);
//                    //Leyenda para notas de credito por Devolucion de Mercancia
//                    if ($rowSMT['suma'] == 0){
//                        $pdf->addTextWrap(105,313,150,$FontSize-2,"Devoluci&oacute;n de Mercancia", 'left');
//                    }
//                }

                $tot = explode(".",round((-1*($myrow['ovgst']+$myrow['ovamount'])),2));
                require_once('Numbers/Words.php');
                $Letra = Numbers_Words::toWords($tot[0],"es");
                if($tot[1]==0){
                        $ConLetra = $Letra." pesos 00/100 M.N.";
                }else if(strlen($tot[1])>=2){
                        $ConLetra = $Letra.' pesos '.$tot[1]."/100 M.N.";
                }else {
                        $ConLetra = $Letra.' pesos '.$tot[1]."0/100 M.N.";
                }

                //$LeftOvers = $pdf->addText($Left_Margin+153,$Bottom_Margin+78,$FontSize,"(".strtoupper($ConLetra).")");
                //total letra
                //$pdf->addTextWrap(126,153,288,8,"(".$ConLetra.")", 'left');
                $pdf->y = $Page_Height - 400 + 51 + 49 + 11 + 30 - 32;
                $pdf->x = 36;
                $pdf->SetFontSize($FontSize-2);
                $pdf->MultiCell(432,$line_height,strtoupper($ConLetra),0,'J',0,15);

		
		/*rule off for total */
		//$pdf->line($Page_Width-$Right_Margin-222, $YPos-(2*$line_height),$Page_Width-$Right_Margin,$YPos-(2*$line_height));

		/*vertical to seperate totals from comments and ROMALPA */
		//$pdf->line($Page_Width-$Right_Margin-222, $YPos+$line_height,$Page_Width-$Right_Margin-222,$Bottom_Margin);
		
	    } /* end of check to see that there was an invoice record to print */

	    $FromTransNo++;
	} /* end loop to print invoices */


	$pdfcode = $pdf->output();
	$len = strlen($pdfcode);

	if ($len <200){
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