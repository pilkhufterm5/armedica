<?php
// TEMPLATE 1 REMISION GRANDE
// bowikaxu - realhost - june 2007<br>

	include ('includes/class.pdf.php');
	include('includes/DefineCartClass.php');
	include('includes/DefineSerialItems.php');
	
	Session_register('Items');
	$_SESSION['Items'] = new cart;
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

	$PageSize = array(0,0,$Page_Width,$Page_Height);
	$pdf =  new Cpdf($PageSize);
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
	$line_height=15;

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
				locations.taxprovinceid,
				shippers.shippername,
				custbranch.taxgroupid,
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

		$ExchRate = $myrow['rate'];

		if ($InvOrCredit=='Invoice'){

			 $sql = 'SELECT stockmoves.stockid,
					stockmaster.description,
					stockmoves.trandate as actualdispatchdate,
					stockmaster.controlled,
					stockmaster.serialised,
					stockmaster.longdescription,
					stockmaster.volume,
					stockmaster.kgs,
					stockmaster.units,
					stockmaster.decimalplaces,
					stockmaster.mbflag,
					stockmaster.discountcategory,
					stockmoves.price as unitprice,
					stockmoves.discountpercent,
					-stockmoves.qty as quantity,
					rh_remdetails.qty AS qty2,
					stockmoves.discountpercent,
					((1 - stockmoves.discountpercent) * stockmoves.price * ' . $ExchRate . '* rh_remdetails.qty) AS fxnet,
					(stockmoves.price * ' . $ExchRate . ') AS fxprice,
					stockmoves.narrative,
					stockmaster.taxcatid
				FROM stockmoves,
					stockmaster,
					rh_remdetails
				WHERE stockmoves.stockid = stockmaster.stockid
				AND stockmoves.type=20000
				AND rh_remdetails.stockid = stockmoves.stockid
				AND rh_remdetails.transno = ' . $FromTransNo . '
				AND rh_remdetails.reference = stockmoves.stkmoveno
				AND stockmoves.transno=' . $FromTransNo . '
				AND stockmoves.show_on_inv_crds=1';
		} else {
		/* only credit notes to be retrieved */
			 $sql = 'SELECT stockmoves.stockid,
					stockmaster.description,
					stockmoves.trandate as actualdispatchdate,
					stockmaster.controlled,
					stockmaster.serialised,
					stockmaster.longdescription,
					stockmaster.volume,
					stockmaster.kgs,
					stockmaster.units,
					stockmaster.decimalplaces,
					stockmaster.mbflag,
					stockmaster.discountcategory,
					stockmoves.price as unitprice,
					stockmoves.discountpercent,
					-stockmoves.qty as quantity,
					rh_remdetails.qty AS qty2,
					stockmoves.discountpercent,
					((1 - stockmoves.discountpercent) * stockmoves.price * ' . $ExchRate . '* rh_remdetails.qty) AS fxnet,
					(stockmoves.price * ' . $ExchRate . ') AS fxprice,
					stockmoves.narrative,
					stockmaster.taxcatid
				FROM stockmoves,
					stockmaster,
					rh_remdetails
				WHERE stockmoves.stockid = stockmaster.stockid
				AND stockmoves.type=20000
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

			$FontSize = 9;
			$PageNumber = 1;
			
			$j=0;
			$IVA = 0;

			include('rh_remgdeheader2.inc.php');
			$FirstPage = False;
			//$pdf->SetTextColor(255,0,0);

		        while ($myrow2=DB_fetch_array($result)){

		        	// TAXES
		        	$TaxLineTotal =0;
		        	$LineTotal = $myrow2['unitprice']*$myrow2['qty2'];
		        	$_SESSION['Items']->TaxGroup = $myrow['taxgroupid'];
					$_SESSION['Items']->DispatchTaxProvince = $myrow['taxprovinceid'];
					$_SESSION['Items']->add_to_cart($myrow2['stockid'],
						$myrow2['qty2'],
						$myrow2['description'],
						$myrow2['unitprice'],
						$myrow2['discountpercent'],
						$myrow2['units'],
						$myrow2['volume'],
						$myrow2['kgs'],
						0,
						$myrow2['mbflag'],
						$myrow2['actualdispatchdate'],
						0,
						$myrow2['discountcategory'],
						$myrow2['controlled'],
						$myrow2['serialised'],
						$myrow2['decimalplaces'],
						$myrow2['narrative'],
						'No',
						$j,
						$myrow2['taxcatid']);	/*NB NO Updates to DB */
						//print_r($_SESSION['Items']);
		$_SESSION['Items']->GetTaxes($j);
		
		foreach ($_SESSION['Items']->LineItems[$j]->Taxes AS $Tax) {
			
			
		
				if ($Tax->TaxOnTax ==1){
					$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * ($LineTotal + $TaxLineTotal));
					$TaxLineTotal += ($Tax->TaxRate * ($LineTotal + $TaxLineTotal));
				} else {
					$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * $LineTotal);
					$TaxLineTotal += ($Tax->TaxRate * $LineTotal);
				}
				$TaxGLCodes[$Tax->TaxAuthID] = $Tax->TaxGLCode;
			
			
		}
		$j++;
		$IVA += $TaxLineTotal;
		        	// END TAXES
		        	
		        	
				$DisplayPrice = number_format($myrow2['fxprice'],2);
				$DisplayQty = number_format($myrow2['qty2'],2);
				$DisplayNet = number_format($myrow2['fxnet'],2);

				if ($myrow2['discountpercent']==0){
					$DisplayDiscount ='';
				} else {
					$DisplayDiscount = number_format($myrow2['discountpercent']*100,2) . '%';
				}
				$FontSizeCode=9;
				
				$LeftOvers = $pdf->addTextWrap($XPos+8,$YPos+2,30,$FontSizeCode,$DisplayQty,'right');
				$LeftOvers = $pdf->addTextWrap($XPos+49,$YPos+2,70,$FontSize,$myrow2['stockid'],'left');
				$LeftOvers = $pdf->addTextWrap($XPos+108,$YPos+2,245,$FontSize,utf8_decode($myrow2['description']),'left');
				$LeftOvers = $pdf->addTextWrap($XPos+365,$YPos+2,40,$FontSize,$DisplayPrice,'right');
				$LeftOvers = $pdf->addTextWrap(490,$YPos+2,50,$FontSize,$DisplayNet,'right');
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+553,$YPos,35,$FontSize,$myrow2['units'],'centre');
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+590,$YPos,50,$FontSize,$DisplayDiscount,'right');
				

				$YPos -= ($line_height+5.8);

				$Narrative = $myrow2['narrative'];
				
				//if ($YPos <= $Bottom_Margin){
				if ($YPos <= 100){
					/* head up a new invoice/credit note page */
					/*draw the vertical column lines right to the bottom */
					PrintLinesToBottom ();
					include ('rh_remgdeheader2.inc.php');
				} //end if need a new page headed up
			} //end while there are line items to print out
			
		} /*end if there are stock movements to show on the invoice or credit note*/

		$YPos -= $line_height;

		/* check to see enough space left to print the 4 lines for the totals/footer */
		if (($YPos-$Bottom_Margin)<(2*$line_height)){

			PrintLinesToBottom ();
			include ('rh_remgdeheader2.inc.php');

		}
		/*Now print out the footer and totals */

		     $DisplaySubTot = number_format($myrow['ovamount'],2);
		     $DisplayFreight = number_format($myrow['ovfreight'],2);
		     $DisplayTotal = number_format($myrow['ovfreight']+$IVA+$myrow['ovamount'],2);
		     $DisplayTax = number_format($IVA,2);

		$LeftOvers = $pdf->addTextWrap(490,80,50,$FontSize,$DisplaySubTot, 'right'); // SUBTOTAL

		$LeftOvers = $pdf->addTextWrap(490,60,50, $FontSize,$DisplayTax, 'right'); // IMPUESTO
		
		$LeftOvers = $pdf->addTextWrap(490,40,50, $FontSize,$DisplayTotal, 'right'); // TOTAL
		
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
