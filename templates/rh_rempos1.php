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
	$minux=(11.3*.2);	
	$Page_Width=590; // ancho
	$Page_Height=790; // largo
	$Top_Margin=50;
	$Bottom_Margin=10;
	$Left_Margin=40;
	$Right_Margin=20;

	$PageSize = array(0,0,$Page_Width,$Page_Height);
	$pdf = & new Cpdf($PageSize);
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
	$line_height=11;

	while ($FromTransNo <= $_POST['ToTransNo']){

	/*retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */
		
	// obtener el numero de transaccion de la remision pedida
		
		if ($InvOrCredit=='Invoice') {
			$sql = 'SELECT debtortrans.trandate,
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

		$sql = 'SELECT debtortrans.trandate,
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

		$ExchRate = $myrow['rate'];

		if ($InvOrCredit=='Invoice'){

		 	 $sql = 'SELECT stockmoves.stockid,
					stockmaster.description,
					-stockmoves.qty as quantity,
					rh_remdetails.qty AS qty2,
					stockmoves.discountpercent,
					((1 - stockmoves.discountpercent) * stockmoves.price * ' . $ExchRate . '* -stockmoves.qty) AS fxnet,
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
				AND stockmoves.transno=' . $FromTransNo . '
				AND stockmoves.show_on_inv_crds=1';
		} else {
		/* only credit notes to be retrieved */
			 $sql = 'SELECT stockmoves.stockid,
			 		stockmaster.description,
					stockmoves.qty as quantity,
					rh_remdetails.qty AS qty2,
					stockmoves.discountpercent,
					((1 - stockmoves.discountpercent) * stockmoves.price * ' . $ExchRate . ' * stockmoves.qty) AS fxnet,
					(stockmoves.price * ' . $ExchRate . ') AS fxprice,
					stockmoves.narrative,
					stockmaster.units
				FROM stockmoves,
					stockmaster,
					rh_remdetails
				WHERE stockmoves.stockid = stockmaster.stockid
				AND stockmoves.type=11
				AND rh_remdetails.stockid = stockmoves.stockid
				AND rh_remdetails.transno = ' . $FromTransNo . '
				AND stockmoves.transno=' . $FromTransNo . '
				AND stockmoves.show_on_inv_crds=1';
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

			$FontSize = 10;
			$FontSizeCode= 9;
			$PageNumber = 1;

			include('rh_remposheader1.inc.php');
			$FirstPage = False;
			//$pdf->SetTextColor(255,0,0);

		        while ($myrow2=DB_fetch_array($result)){

				$DisplayPrice = number_format($myrow2['fxprice'],2);
				$DisplayQty = number_format($myrow2['qty2'],2);
				$DisplayNet = number_format($myrow2['fxnet'],2);

				if ($myrow2['discountpercent']==0){
					$DisplayDiscount ='';
				} else {
					$DisplayDiscount = number_format($myrow2['discountpercent']*100,2) . '%';
				}

				$LeftOvers = $pdf->addTextWrap($XPos,$YPos-$minux,50,$FontSize,$DisplayQty,'left');
				$LeftOvers = $pdf->addTextWrap($XPos+26,$YPos-$minux,70,$FontSizeCode,$myrow2['stockid'],'left');
				$LeftOvers = $pdf->addTextWrap($XPos+100,$YPos-$minux,250,$FontSize,$myrow2['description'],'left');
				$LeftOvers = $pdf->addTextWrap($XPos+410,$YPos-$minux,50,$FontSize,$DisplayPrice,'right');
				$LeftOvers = $pdf->addTextWrap(530,$YPos-$minux,50,$FontSize,$DisplayNet,'right');
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+553,$YPos-$minux,35,$FontSize,$myrow2['units'],'centre');
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+590,$YPos,50,$FontSize,$DisplayDiscount,'right');
				
				$LeftOvers = $pdf->addTextWrap($XPos,$YPos-398-$minux,50,$FontSize,$DisplayQty,'left');
				$LeftOvers = $pdf->addTextWrap($XPos+26,$YPos-398-$minux,70,$FontSizeCode,$myrow2['stockid'],'left');
				$LeftOvers = $pdf->addTextWrap($XPos+100,$YPos-398-$minux,250,$FontSize,$myrow2['description'],'left');
				$LeftOvers = $pdf->addTextWrap($XPos+410,$YPos-398-$minux,50,$FontSize,$DisplayPrice,'right');
				$LeftOvers = $pdf->addTextWrap(530,$YPos-398-$minux,50,$FontSize,$DisplayNet,'right');
				

				$YPos -= ($line_height);

				$Narrative = $myrow2['narrative'];
				
				//if ($YPos <= $Bottom_Margin){
				
				//if ($YPos <= 100+398){
					/* head up a new invoice/credit note page */
					/*draw the vertical column lines right to the bottom */
					//PrintLinesToBottom ();
					//include ('rh_remposheader1.inc.php');
				//} //end if need a new page headed up


			} //end while there are line items to print out
		} /*end if there are stock PrintLinesToBottommovements to show on the invoice or credit note*/

		$YPos -= $line_height;

		/* check to see enough space left to print the 4 lines for the totals/footer */
		if (($YPos-$Bottom_Margin)<(2*$line_height)){

			PrintLinesToBottom ();
			include ('rh_remposheader1.inc.php');

		}
		/*Now print out the footer and totals */

		     $DisplaySubTot = number_format($myrow['ovamount'],2);
		     //$DisplayFreight = number_format($myrow['ovfreight'],2);
		     $DisplayTax = number_format($myrow['ovgst'],2);
		     $DisplayTotal = number_format($myrow['ovfreight']+$myrow['ovgst']+$myrow['ovamount'],2);

		//$pdf->addText($Page_Width-$Right_Margin-220, $YPos+5,$FontSize, _('Sub Total'));
		//$LeftOvers = $pdf->addTextWrap(525,40+390-$minux,50,$FontSize,$DisplaySubTot."HJ1", 'right');
		//$LeftOvers = $pdf->addTextWrap(525,40-$minux,50,$FontSize,$DisplaySubTot."HJ1", 'right');
		//$pdf->addText($Page_Width-$Right_Margin-220, $YPos-$line_height+5,$FontSize, _('Freight'));
		
//		$LeftOvers = $pdf->addTextWrap(526,80+390-$minux,50,$FontSize,$DisplayFreight, 'right');
//		$LeftOvers = $pdf->addTextWrap(526,80-$minux,50,$FontSize,$DisplayFreight, 'right');

		$LeftOvers = $pdf->addTextWrap(376,15+390-$minux,50,$FontSize,$DisplaySubTot, 'right');
		$LeftOvers = $pdf->addTextWrap(376,25-$minux,50,$FontSize,$DisplaySubTot, 'right');
		
		//$pdf->addText($Page_Width-$Right_Margin-220, $YPos-(2*$line_height)+5,$FontSize, _('Tax'));
		
		//$LeftOvers = $pdf->addTextWrap(526,60+390-$minux,50, $FontSize,$DisplayTax, 'right');
		//$LeftOvers = $pdf->addTextWrap(526,60-$minux,50, $FontSize,$DisplayTax, 'right');
		
		$LeftOvers = $pdf->addTextWrap(456,15+390-$minux,50, $FontSize,$DisplayTax, 'right');
		$LeftOvers = $pdf->addTextWrap(456,25-$minux,50, $FontSize,$DisplayTax, 'right');
		
		//530
		$LeftOvers = $pdf->addTextWrap(526,15+390-$minux,50, $FontSize,$DisplayTotal, 'right');
		
		$LeftOvers = $pdf->addTextWrap(526,25-$minux,50, $FontSize,$DisplayTotal, 'right');
		/*rule off for total */
		//$pdf->line($Page_Width-$Right_Margin-222, $YPos-(2*$line_height),$Page_Width-$Right_Margin,$YPos-(2*$line_height));

		/*vertical to seperate totals from comments and ROMALPA */
		//$pdf->line($Page_Width-$Right_Margin-222, $YPos+$line_height,$Page_Width-$Right_Margin-222,$Bottom_Margin);
		
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
