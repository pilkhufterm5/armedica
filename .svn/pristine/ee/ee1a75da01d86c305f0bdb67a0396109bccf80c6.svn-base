<?php	

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
	
	/*This invoice is hard coded for A4 Landscape invoices or credit notes  so can't use PDFStarter.inc*/
	///$Page_Width=842;
	///$Page_Height=595;
	
	//bowikaxu
	//feb 22 2007
	//Cambio de Vertical a Horizontal y de a$ a Carta
	//$minux = -(11*4);
	$Page_Width=612; // horizontal
	$Page_Height=792; // vertical
	
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

	if ($InvOrCredit=='Invoice'){
		///$pdf->addinfo('Title',_('Sales Invoice') . ' ' . $FromTransNo . ' to ' . $_POST['ToTransNo']);
		///$pdf->addinfo('Subject',_('Invoices from') . ' ' . $FromTransNo . ' ' . _('to') . ' ' . $_POST['ToTransNo']);
	} else {
		///$pdf->addinfo('Title',_('Sales Credit Note') );
		///$pdf->addinfo('Subject',_('Credit Notes from') . ' ' . $FromTransNo . ' ' . _('to') . ' ' . $_POST['ToTransNo']);
	}

	$line_height=12;

	while ($FromTransNo <= $_POST['ToTransNo']){

	/*retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */

		if ($InvOrCredit=='Invoice') {
			$sql = 'SELECT debtortrans.trandate,		DATE_FORMAT(trandate, "%m") as mm,	DATE_FORMAT(trandate, "%d") as dd, 	DATE_FORMAT(trandate, "%y") as yy,
				debtortrans.id AS ID,
				debtortrans.ovamount,
				debtortrans.ovdiscount,
				debtortrans.ovfreight,
				debtortrans.order_,
				debtortrans.ovgst,
				debtortrans.rate,
				debtortrans.invtext,
				debtortrans.consignment,
				debtorsmaster.name,
				debtorsmaster.name2,
				debtorsmaster.address1,
				debtorsmaster.address2,
				debtorsmaster.address3,
				debtorsmaster.address4,
				debtorsmaster.address5,
				debtorsmaster.address6,
				debtorsmaster.currcode,
				debtorsmaster.invaddrbranch,
				debtorsmaster.taxref,
				debtorsmaster.rh_Tel,
				paymentterms.terms,
				salesorders.deliverto,
				salesorders.deladd1,
				salesorders.deladd2,
				salesorders.deladd3,
				salesorders.deladd4,
				salesorders.customerref,
				salesorders.orderno,
				salesorders.orddate,
				locations.locationname,
				shippers.shippername,
				custbranch.brname,
				custbranch.phoneno,
				custbranch.braddress1,
				custbranch.braddress2,
				custbranch.braddress3,
				custbranch.braddress4,
				custbranch.brpostaddr1,
				custbranch.brpostaddr2,
				custbranch.brpostaddr3,
				custbranch.brpostaddr4,
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
			AND debtortrans.type=10
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

		$sql = 'SELECT debtortrans.trandate,		SUBSTRING(DATE_FORMAT(trandate, "%M"),1,3) as mm,	DATE_FORMAT(trandate, "%d") as dd, 	DATE_FORMAT(trandate, "%Y") as yy,
				debtortrans.ovamount,
				debtortrans.id AS ID,
				debtortrans.ovdiscount,
				debtortrans.ovfreight,
				debtortrans.order_,
				debtortrans.ovgst,
				debtortrans.rate,
				debtortrans.invtext,
				debtorsmaster.invaddrbranch,
				debtorsmaster.name,
				debtorsmaster.name2,
				debtorsmaster.address1,
				debtorsmaster.address2,
				debtorsmaster.address3,
				debtorsmaster.address4,
				debtorsmaster.address5,
				debtorsmaster.address6,
				debtorsmaster.currcode,
				debtorsmaster.taxref,
				debtorsmaster.rh_Tel,
				custbranch.brname,
				custbranch.phoneno,
				custbranch.braddress1,
				custbranch.braddress2,
				custbranch.braddress3,
				custbranch.braddress4,
				custbranch.brpostaddr1,
				custbranch.brpostaddr2,
				custbranch.brpostaddr3,
				custbranch.brpostaddr4,
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
	   $result=DB_query($sql,$db);
	   
	   if (DB_error_no($db)!=0) {

		$title = _('Transaction Print Error Report');
		include ('includes/header.inc');

		echo '<BR>' . _('There was a problem retrieving the invoice or credit note details for note number') . ' ' . $InvoiceToPrint . ' ' . _('from the database') . '. ' . _('To print an invoice, the sales order record, the customer transaction record and the branch record for the customer must not have been purged') . '. ' . _('To print a credit note only requires the customer, transaction, salesman and branch records be available');
		if ($debug==1){
		    echo _('The SQL used to get this information that failed was') . "<BR>$sql";
		}
		break;
		include ('includes/footer.inc');
		exit;
	   }
	   if (DB_num_rows($result)==1){
		$myrow = DB_fetch_array($result);

		$ID = $myrow['ID'];
				$sql2 = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations WHERE rh_invoicesreference.ref = ".$ID." 
				AND locations.loccode = rh_invoicesreference.loccode";
				$Res = DB_query($sql2,$db);
				$ExtRes = DB_fetch_array($Res);
		
		$ExchRate = $myrow['rate'];

		if ($InvOrCredit=='Invoice'){

			 $sql = 'SELECT stockmoves.stockid,
					stockmaster.description,
					stockmaster.units,
					-stockmoves.qty as quantity,
					stockmoves.discountpercent,
					stockmoves.stkmoveno,
					((1 - stockmoves.discountpercent) * stockmoves.price * ' . $ExchRate . '* -stockmoves.qty) AS fxnet,
					(stockmoves.price * ' . $ExchRate . ') AS fxprice,
					stockmoves.narrative,
					stockmaster.units
				FROM stockmoves,
					stockmaster
				WHERE stockmoves.stockid = stockmaster.stockid
				AND stockmoves.type=10
				AND stockmoves.transno=' . $FromTransNo . '
				AND stockmoves.show_on_inv_crds=1';
		} else {
		/* only credit notes to be retrieved */
			 $sql = 'SELECT stockmoves.stockid,
			 		stockmaster.description,
			 		stockmaster.units,
					stockmoves.qty as quantity,
					stockmoves.stkmoveno,
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
				AND stockmoves.show_on_inv_crds=1';
		}

		$result=DB_query($sql,$db);
		if (DB_error_no($db)!=0) {
			$title = _('Transaction Print Error Report');
			include ('includes/header.inc');
			echo '<BR>' . _('There was a problem retrieving the invoice or credit note stock movement details for invoice number') . ' ' . $FromTransNo . ' ' . _('from the database');
			if ($debug==1){
			    echo '<BR>' . _('The SQL used to get this information that failed was') . "<BR>$sql";
			}
			include('includes/footer.inc');
			exit;
		}

		if (DB_num_rows($result)>0){

			$FontSize = 11.5;
			$PageNumber = 1;			

			include('rh_templateheaderb22.inc.php');

/*
bowikaxu
Feb 22 2007
I added this line to include the invoice number on the top right of the pdf document
*/			$pdf->SetTextColor(255,0,0);
///			$pdf->addTextWrap($Left_Margin+430,$YPos+185,295,$FontSize,$myrow['consignment']. '  .'.'.');
			$pdf->SetTextColor(0,0,0);
			
				$XPos = 0;
				$YPos = 513-10;
				$CeroTax = 0;
				
		        while ($myrow2=DB_fetch_array($result)){

				$DisplayPrice = number_format($myrow2['fxprice'],2);
				$DisplayQty = number_format($myrow2['quantity']);
				$DisplayNet = number_format($myrow2['fxnet'],2);
				$sql = "SELECT taxrate FROM stockmovestaxes WHERE stkmoveno = '".$myrow2['stkmoveno']."'";
				$res = DB_query($sql,$db);
				if(DB_num_rows($res)>=1){
					// si tiene tax no sumar a 0% IVA
					$trate = DB_fetch_array($res);
					if($trate['taxrate']!=0){
						// su tax no es cero
					}else {
						$CeroTax += $myrow2['fxnet'];
					}
				}else {
					// no tiene tax
					
					$CeroTax += $myrow2['fxnet'];
				}

				if ($myrow2['discountpercent']==0){
					$DisplayDiscount ='';
				} else {
					$DisplayDiscount = number_format($myrow2['discountpercent']*100,2) . '%';
				}
				
				///bowikaxu Feb 22 2007
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+3,$YPos-260,95,$FontSize,$myrow2['stockid']);
				///$LeftOvers = $pdf->addTextWrap($Left_Margin+100,$YPos,245,$FontSize,$myrow2['description']);
				$LeftOvers = $pdf->addText($XPos+20,$YPos-$minux,$FontSize,$DisplayQty,'right');
				$LeftOvers = $pdf->addText($XPos+75,$YPos-$minux,$FontSize,$myrow2['units']);
				$LeftOvers = $pdf->addText($XPos+100,$YPos-$minux,$FontSize,$myrow2['stockid']);				
				$LeftOvers = $pdf->addText($XPos+157,$YPos-$minux,$FontSize,$myrow2['description']);
				///$LeftOvers = $pdf->addTextWrap($Left_Margin+453,$YPos,96,$FontSize,$DisplayQty,'right');
				
				///$LeftOvers = $pdf->addTextWrap($Left_Margin+553,$YPos,35,$FontSize,$myrow2['units'],'centre');
				///$LeftOvers = $pdf->addTextWrap($Left_Margin+130,$YPos-250,50,$FontSize,$DisplayDiscount,'right');
				$LeftOvers = $pdf->addTextWrap($XPos+430,$YPos-$minux,60,$FontSize,$DisplayPrice,'right');
				$LeftOvers = $pdf->addTextWrap($XPos+520,$YPos-$minux,70,$FontSize,$DisplayNet,'right');
				//$Narrative = $myrow2['narrative'];

					if ($YPos-$line_height <= $Bottom_Margin){
						/* head up a new invoice/credit note page */
						/*draw the vertical column lines right to the bottom */
						PrintLinesToBottom ();
	   		        		include ('rh_templateheaderb22.inc.php');
			   		} //end if need a new page headed up
			   		/*increment a line down for the next line item */
			   		/*
			   		if (strlen($Narrative)>1){
						$Narrative = $pdf->addTextWrap($Left_Margin+58,$YPos-140,315,$FontSize,$Narrative);
					}
					*/
				if ($YPos <= $Bottom_Margin){

					/* head up a new invoice/credit note page */
					/*draw the vertical column lines right to the bottom */
					PrintLinesToBottom ();
					include ('rh_templateheaderb22.inc.php');
				} //end if need a new page headed up

				//$YPos -= 14;
				$YPos -= ($line_height);
			} //end while there are line items to print out
			$FirstPage = 0;
		} /*end if there are stock movements to show on the invoice or credit note*/

		//$YPos -= $line_height;

		/* check to see enough space left to print the 4 lines for the totals/footer */
		if (($YPos-$Bottom_Margin)<(2*$line_height)){

			PrintLinesToBottom ();
			include ('rh_templateheaderb22.inc.php');

		}
		/*Print a column vertical line  with enough space for the footer*/
		/*draw the vertical column lines to 4 lines shy of the bottom
		to leave space for invoice footer info ie totals etc*/
		///$pdf->line($Left_Margin+97, $TopOfColHeadings+12,$Left_Margin+97,$Bottom_Margin+(4*$line_height));

		/*Print a column vertical line */
		///$pdf->line($Left_Margin+350, $TopOfColHeadings+12,$Left_Margin+350,$Bottom_Margin+(4*$line_height));

		/*Print a column vertical line */
		///$pdf->line($Left_Margin+450, $TopOfColHeadings+12,$Left_Margin+450,$Bottom_Margin+(4*$line_height));

		/*Print a column vertical line */
		///$pdf->line($Left_Margin+550, $TopOfColHeadings+12,$Left_Margin+550,$Bottom_Margin+(4*$line_height));

		/*Print a column vertical line */
		///$pdf->line($Left_Margin+587, $TopOfColHeadings+12,$Left_Margin+587,$Bottom_Margin+(4*$line_height));

		///$pdf->line($Left_Margin+640, $TopOfColHeadings+12,$Left_Margin+640,$Bottom_Margin+(4*$line_height));

		/*Rule off at bottom of the vertical lines */
		///$pdf->line($Left_Margin, $Bottom_Margin+(4*$line_height),$Page_Width-$Right_Margin,$Bottom_Margin+(4*$line_height));

		/*Now print out the footer and totals */
		
		// impresion del numero de orden
		$pdf->addText(143, 180-$minux, $FontSize, 'RG'.$myrow['order_'].' ('.$FromTransNo.')');
		
		if ($InvOrCredit=='Invoice') {

		     $DisplaySubTot = number_format($myrow['ovamount'],2);
		     $DisplayFreight = number_format($myrow['ovfreight'],2);
		     $DisplayTax = number_format($myrow['ovgst'],2);
		     $DisplayTotal = number_format($myrow['ovfreight']+$myrow['ovgst']+$myrow['ovamount'],2);
		     $Total = ($myrow['ovfreight']+$myrow['ovgst']+$myrow['ovamount']);
		     $Display0Tax=number_format($CeroTax,2);

		} else {

		     $DisplaySubTot = number_format(-$myrow['ovamount'],2);
		     $DisplayFreight = number_format(-$myrow['ovfreight'],2);
		     $DisplayTax = number_format(-$myrow['ovgst'],2);
		     $DisplayTotal = number_format(-$myrow['ovfreight']-$myrow['ovgst']-$myrow['ovamount'],2);
		     $Total = ($myrow['ovfreight']+$myrow['ovgst']+$myrow['ovamount']);
		     $Display0Tax=number_format($CeroTax,2);
		}
	/*Print out the invoice text entered */
		$YPos = $Bottom_Margin+(3*$line_height);
	/* Print out the payment terms */

  		///$pdf->addTextWrap($Left_Margin+5,$YPos+3,280,$FontSize,_('Payment Terms') . ': ' . $myrow['terms']);
  		
  		//$pdf->addTextWrap(455,493,155,$FontSize, $myrow['terms'],'center');
  		
//bowikaxu
//Feb 23 2005
//numero a letra
$tot = explode(".",$Total);
$Letra = Numbers_Words::toWords($tot[0],"es");
if($tot[1]==0){
	$ConLetra = $Letra." pesos 00/100 M.N.";
}else if(strlen($tot[1])>=2){
	$ConLetra = $Letra.' pesos '.$tot[1]."/100 M.N.";
}else {
	$ConLetra = $Letra.' pesos '.$tot[1]."0/100 M.N.";
}
$LeftOvers = $pdf->addText(110,120-$minux,$FontSize,"(".$ConLetra.")");
// fin numero a letra
	
		$FontSize =11.5;
		//$LeftOvers = $pdf->addTextWrap($Left_Margin+5,148,280,$FontSize,$myrow['invtext']);
		if (strlen($LeftOvers)>0){
			$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-24-$minux,280,$FontSize,$LeftOvers);
			if (strlen($LeftOvers)>0){
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-36-$minux,280,$FontSize,$LeftOvers);
				/*If there is some of the InvText leftover after 3 lines 200 wide then it is not printed :( */
			}
		}
		$FontSize = 11.5;
		
///		$pdf->addText($Page_Width-$Right_Margin-220, $YPos+5,$FontSize, _('Sub Total'));
		///$LeftOvers = $pdf->addTextWrap($Left_Margin+642,$YPos+5,120,$FontSize,$DisplaySubTot, 'right');
		$LeftOvers = $pdf->addTextWrap(505,173-$minux,80,$FontSize,$DisplaySubTot, 'right');
		
		///$pdf->addText($Page_Width-$Right_Margin-220, $YPos-(2*$line_height)+5,$FontSize, 'Tax');
		$LeftOvers = $pdf->addTextWrap(505,148-$minux,80,$FontSize,$Display0Tax, 'right');
		
///		$pdf->addText($Page_Width-$Right_Margin-220, $YPos-$line_height+5,$FontSize, _('Freight'));
		///$LeftOvers = $pdf->addTextWrap($Left_Margin+642,$YPos-$line_height+5,120,$FontSize,$DisplayFreight, 'right');
		$LeftOvers = $pdf->addTextWrap(505,123-$minux,80,$FontSize,$DisplayTax, 'right');
		
		$LeftOvers = $pdf->addTextWrap(505,98-$minux,80,$FontSize,$DisplayTotal, 'right');
		/*rule off for total */
		///$pdf->line($Page_Width-$Right_Margin-222, $YPos-(2*$line_height),$Page_Width-$Right_Margin,$YPos-(2*$line_height));

		/*vertical to seperate totals from comments and ROMALPA */
		///$pdf->line($Page_Width-$Right_Margin-222, $YPos+$line_height,$Page_Width-$Right_Margin-222,$Bottom_Margin);

		$YPos+=10;
		if ($InvOrCredit=='Invoice'){
///			$pdf->addText($Page_Width-$Right_Margin-220, $YPos - ($line_height*3)-6,$FontSize, _('TOTAL INVOICE'));
			$FontSize=11.5;
			///$LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos-2,245,$FontSize,$_SESSION['RomalpaClause']);
			while (strlen($LeftOvers)>0 AND $YPos > $Bottom_Margin){
				$YPos -=7;
				$LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos-$minux,245,$FontSize,$LeftOvers);
			}
			$FontSize=11.5;
		} else {
///			$pdf->addText($Page_Width-$Right_Margin-220, $YPos-($line_height*3),$FontSize, _('TOTAL CREDIT'));
 		}
		//$LeftOvers = $pdf->addTextWrap($Left_Margin+430,11,120, $FontSize,$DisplayTotal, 'right');

	    } /* end of check to see that there was an invoice record to print */

	    $FromTransNo++;
	    $FirstPage=false;
	} /* end loop to print invoices */


	$pdfcode = $pdf->output();
	$len = strlen($pdfcode);

	if ($len <1020){
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
