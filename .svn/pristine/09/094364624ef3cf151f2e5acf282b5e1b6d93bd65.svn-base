<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-03-14 11:44:38 -0600 (Fri, 14 Mar 2008) $
 * $Rev: 121 $
 */
$PageSecurity = 2;

/*

bowikaxu realhost jun 2007
DUNNING LETTER (carta de cobranza)

*/

// bowikaxu - coments NOT OFICIAL PRINT
// $pdf->addText($Page_Width/2,$Page_Height/2,18,_('Not Legal Copy'),'right');

If (isset($_GET['DebtorNo']) AND strlen($_GET['DebtorNo'])>=1){
    include_once('includes/session.inc');
    //include('config.php');
    include('includes/PDFStarter.php');
    //include('includes/ConnectDB.inc');
    //include('includes/DateFunctions.inc');
    include_once('Numbers/Words.php');

    //chdir(dirname(__FILE__));
   // include_once ('PHPJasperXML/class/fpdf/fpdf.php');


	$FontSize=12;
	$pdf->addinfo('Title',_('Dunning Letter').': '.$_GET['DebtorNo']);
	$pdf->addinfo('Subject',_('Dunning Letter').': '.$_GET['DebtorNo']);

	$PageNumber=0;
	$line_height=11;
	$_POST['DetailedReport']='Yes';
      /*Now figure out the aged analysis for the customer range under review */

    $pdf->Image("companies/" . $_SESSION['DatabaseName'] . "/ARHeader.jpg", 0, 0, 610, 'L');

	$SQL = "SELECT debtorsmaster.debtorno,
				debtorsmaster.name,
				currencies.currency,
				paymentterms.terms,
				debtorsmaster.creditlimit,
				holdreasons.dissallowinvoices,
				holdreasons.reasondescription,
				SUM(
					debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc
				) as balance,
				SUM(
					CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate)) >= paymentterms.daysbeforedue
						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc
						ELSE 0 END
					ELSE
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') .")) >= 0
						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
					END
				) AS due,
				Sum(
					CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate)) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ")
						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
					ELSE
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL ('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . "
						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc
						ELSE 0 END
					END
				) AS overdue1,
				Sum(
					CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate)) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ")
						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
					ELSE
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL ('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays2'] . "
						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc
						ELSE 0 END
					END
				) AS overdue2
				FROM debtorsmaster
                    left join rh_titular on rh_titular.debtorno = debtorsmaster.debtorno,
					paymentterms,
					holdreasons,
					currencies,
					debtortrans
				WHERE debtorsmaster.paymentterms = paymentterms.termsindicator
					AND debtorsmaster.currcode = currencies.currabrev
					AND debtorsmaster.holdreason = holdreasons.reasoncode
					AND debtorsmaster.debtorno = debtortrans.debtorno
					AND rh_titular.movimientos_afiliacion = 'Activo'
					AND debtorsmaster.debtorno = '" . $_GET['DebtorNo'] . "'
					AND ABS(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)>0.05
				GROUP BY debtorsmaster.debtorno,
					debtorsmaster.name,
					currencies.currency,
					paymentterms.terms,
					paymentterms.daysbeforedue,
					paymentterms.dayinfollowingmonth,
					debtorsmaster.creditlimit,
					holdreasons.dissallowinvoices,
					holdreasons.reasondescription
				HAVING
					Sum(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc) <>0";

	$CustomerResult = DB_query($SQL,$db,'','',False,False); /*dont trap errors handled below*/

	if (DB_error_no($db) !=0) {
		$title = _('Dunning Letter') . ' - ' . _('Problem Report') . '.... ';
		include('includes/header.inc');
		echo '<P>' . _('The customer details could not be retrieved by the SQL because') . ' ' . DB_error_msg($db);
		echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
		if ($debug==1){
			echo "<BR>$SQL";
		}
		include('includes/footer.inc');
		exit;
	}

    // bowikaxu realhost - escoger el template june 2007
    include ('templates/rh_dunning1.php');


    $TotBal=0;
    $TotCur=0;
    $TotDue=0;
    $TotOD1=0;
    $TotOD2=0;
//     echo "<BR>$SQL";
// // var_dump(DB_fetch_assoc($CustomerResult));
// echo "ÑOOOOOOOOOOOOOOOO";
// exit;
    while($AgedAnalysis = DB_fetch_array($CustomerResult)){

		$DisplayDue = number_format($AgedAnalysis['due']-$AgedAnalysis['overdue1'],2);
		$DisplayCurrent = number_format($AgedAnalysis['balance']-$AgedAnalysis['due'],2);
		$DisplayBalance = number_format($AgedAnalysis['balance'],2);
		$DisplayOverdue1 = number_format($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2'],2);
		$DisplayOverdue2 = number_format($AgedAnalysis['overdue2'],2);

		$TotBal += $AgedAnalysis['balance'];
		$TotDue += ($AgedAnalysis['due']-$AgedAnalysis['overdue1']);
		$TotCurr += ($AgedAnalysis['balance']-$AgedAnalysis['due']);
		$TotOD1 += ($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2']);
		$TotOD2 += $AgedAnalysis['overdue2'];

		//$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,220-$Left_Margin,$FontSize,$AgedAnalysis['debtorno'] . ' - ' . $AgedAnalysis['name'],'left');
		// $LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayBalance,'right');
		// $LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayCurrent,'right');
		// $LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayDue,'right');
		// $LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayOverdue1,'right');
		// $LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayOverdue2,'right');

		$YPos -=$line_height;
		if ($YPos < $Bottom_Margin + $line_height){
		      include('templates/rh_dunning1.php');
		}



        if ($_POST['DetailedReport']=='Yes'){
            /*draw a line under the customer aged analysis*/
            //$pdf->line($Page_Width-$Right_Margin, $YPos+10,$Left_Margin, $YPos+10);
            //SAINTS
				 $sql = "SELECT systypes.typename,
						c.serie,
						c.folio,
						debtortrans.type,
			   			debtortrans.transno,
			   			debtortrans.trandate,
				   		(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc) as balance,
						(CASE WHEN (paymentterms.daysbeforedue > 0)
							THEN
		   						(CASE WHEN (TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate)) >= paymentterms.daysbeforedue
		   						then debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc
		   						ELSE 0 END)
							ELSE
		   						(CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= 0
		   						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc
		   						ELSE 0 END)
						END) AS due,
						(CASE WHEN (paymentterms.daysbeforedue > 0)
		   					THEN
								(CASE WHEN TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ") THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END)
		   					ELSE
								(CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ")
		   						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc
		   						ELSE 0 END)
						END) AS overdue1,
						(CASE WHEN (paymentterms.daysbeforedue > 0)
		   					THEN
								(CASE WHEN TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ")
		   						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc
		   						ELSE 0 END)
		 					ELSE
								(CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= " . $_SESSION['PastDueDays2'] . ")
		   						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc
		   						ELSE 0 END)
						END) AS overdue2
				   FROM debtorsmaster,
		   				paymentterms,
		   				debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id,
		   				systypes
				   WHERE systypes.typeid = debtortrans.type
		   				AND debtorsmaster.paymentterms = paymentterms.termsindicator
		   				AND debtorsmaster.debtorno = debtortrans.debtorno
				   		AND debtortrans.debtorno = '" . $AgedAnalysis['debtorno'] . "'
		   				AND ABS(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)>0.05";


		    $DetailResult = DB_query($sql,$db,'','',False,False); /*Dont trap errors */
		    if (DB_error_no($db) !=0) {
			$title = _('Dunning Letter') . ' - ' . _('Problem Report') . '....';
			include('includes/header.inc');
			echo '<BR><BR>' . _('The details of outstanding transactions for customer') . ' - ' . $AgedAnalysis['debtorno'] . ' ' . _('could not be retrieved because') . ' - ' . DB_error_msg($db);
			echo "<BR><A HREF='$rootpath/index.php'>" . _('Back to the menu') . '</A>';
			if ($debug==1){
				echo '<BR>' . _('The SQL that failed was') . '<P>' . $sql;
			}
			include('includes/footer.inc');
			exit;
		    }

		    while ($DetailTrans = DB_fetch_array($DetailResult)){

				if($DetailTrans['type']==10){

		    		// $sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations
		    		// WHERE rh_invoicesreference.intinvoice = ".$DetailTrans['transno']."
		    		// AND locations.loccode = rh_invoicesreference.loccode";
		    		// $res2 = DB_query($sql,$db);
		    		// $ExtInvoice = DB_fetch_array($res2);

		    		 //SAINTS
                     $sql_fe="SELECT serie, folio FROM rh_cfd__cfd WHERE fk_transno='".$DetailTrans['transno']."'";
                     $res_fe=DB_query($sql_fe,$db);
                     $res_fe=DB_fetch_array($res_fe);

                     $GetAfilData = "SELECT (folio) as AfilNo FROM rh_titular WHERE debtorno = '{$AgedAnalysis['debtorno']}' ";
                     $GetAfilData=DB_query($GetAfilData,$db);
                     $GetAfilData=DB_fetch_array($GetAfilData);



                     /*@JotaOwen*/
                    if($res_fe['folio']!="") {
                        // $LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,60,$FontSize,$GetAfilData['AfilNo'],'left');
                        // //$LeftOvers = $pdf->addTextWrap($Left_Margin+20,$YPos,60,$FontSize,$DetailTrans['typename'],'left');
                        // $LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,60,$FontSize,$res_fe['serie'].$res_fe['folio'],'left');
                    }

				}else {//SAINTS
                    // $LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,60,$FontSize,$DetailTrans['typename'],'left');
                    // if($DetailTrans['folio']!=""){
                    //     $LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,60,$FontSize,$DetailTrans['serie'].$DetailTrans['folio'],'left');
                    // } else{
                    //     $LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,60,$FontSize,$DetailTrans['transno'],'left');
                    // }
                }

                $LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,60,$FontSize,$GetAfilData['AfilNo'],'left');

			    $DisplayTranDate = ConvertSQLDate($DetailTrans['trandate']);

			    $DisplayDue = number_format($DetailTrans['due']-$DetailTrans['overdue1'],2);
			    $DisplayCurrent = number_format($DetailTrans['balance']-$DetailTrans['due'],2);
			    $DisplayBalance = number_format($DetailTrans['balance'],2);
			    $DisplayOverdue1 = number_format($DetailTrans['overdue1']-$DetailTrans['overdue2'],2);
			    $DisplayOverdue2 = number_format($DetailTrans['overdue2'],2);

			    $LeftOvers = $pdf->addTextWrap(210,$YPos,60,$FontSize,$DisplayBalance,'right');
                $LeftOvers = $pdf->addTextWrap(310,$YPos,60,$FontSize,$res_fe['serie'].$res_fe['folio'],'left');
                $LeftOvers = $pdf->addTextWrap(390,$YPos,75,$FontSize,$DisplayTranDate,'left');
                $LeftOvers = $pdf->addTextWrap(470,$YPos,60,$FontSize,'Vencida','right');

                //$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayCurrent,'right');
                // $LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayDue,'right');
                // $LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayOverdue1,'right');
                // $LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayOverdue2,'right');

			    $YPos -=$line_height;
			    if ($YPos < $Bottom_Margin + $line_height){
				$PageNumber++;
					include('templates/rh_dunning1.php');
			    }

		    } /*end while there are detail transactions to show */
		    $FontSize=8;
		    /*draw a line under the detailed transactions before the next customer aged analysis*/
		    $pdf->line($Page_Width-$Right_Margin, $YPos+10,$Left_Margin, $YPos+10);
		} /*Its a detailed report */
	} /*end customer aged analysis while loop */

	$YPos -=$line_height;
	if ($YPos < $Bottom_Margin + (2*$line_height)){
		$PageNumber++;
		include('templates/rh_dunning1.php');
	} elseif ($_POST['DetailedReport']=='Yes') {
		//dont do a line if the totals have to go on a new page
		//$pdf->line($Page_Width-$Right_Margin, $YPos+10 ,220, $YPos+10);
	}

	// $DisplayTotBalance = number_format($TotBal,2);
	// $DisplayTotDue = number_format($TotDue,2);
	// $DisplayTotCurrent = number_format($TotCurr,2);
	// $DisplayTotOverdue1 = number_format($TotOD1,2);
	// $DisplayTotOverdue2 = number_format($TotOD2,2);

	// // $LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayTotBalance,'right');
	// // $LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayTotCurrent,'right');
	// // $LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayTotDue,'right');
	// // $LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayTotOverdue1,'right');
	// // $LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayTotOverdue2,'right');

	$text4 = "Reciba un Cordial Saludo.";
	$pdf->addText($Left_Margin,$YPos-($line_height*3),$FontSize, $text4, 'right');
	// $pdf->addText(($Page_Width-$Right_Margin)/2-50,$YPos-($line_height*6),$FontSize, 'Departamento de Cobranza');
	// $pdf->line(($Page_Width-$Right_Margin)/2-100, $YPos-($line_height*9),($Page_Width-$Right_Margin)/2+100,$YPos-($line_height*9) );


	// aqui van los datos de header(), $pdf->stream(), ...;

	$FromTransNo = $DetailTrans['transno'];
	$PrintPDF = "Imprimir PDF (imagen)";
	$InvOrCredit='Invoice';

	DB_data_seek($DetailResult,0);



	while ($DetailTrans = DB_fetch_array($DetailResult)){
		$FromTransNo = $DetailTrans['transno'];

		if($DetailTrans['type']==10  && ($_GET['Img'] == 1)){

			if ($InvOrCredit=='Invoice') {
			$sql = 'SELECT debtortrans.trandate,		DATE_FORMAT(trandate, "%m") as mm,	DATE_FORMAT(trandate, "%d") as dd, 	DATE_FORMAT(trandate, "%y") as yy,
				debtortrans.id AS ID,
				debtortrans.transno,
				debtortrans.ovamount,
				debtortrans.ovdiscount,
				<debtortrans class="ovfreight"></debtortrans>,
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
				debtortrans.branchcode,
				debtortrans.rh_printnarrative
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
				paymentterms.terms,
				debtortrans.rh_printnarrative
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

			$PageNumber = 1;

			include('templates/rh_dunningheader1.inc.php');

			$YPos = 374 - $line_height;
			$CeroTax = 0;

		        while ($myrow2=DB_fetch_array($result)){

				if ($YPos <= 144){
	   		       		include('templates/rh_dunningheader1.inc.php');
				}

				$DisplayPrice = number_format($myrow2['fxprice'],2);
				$DisplayQty = number_format($myrow2['quantity']);
				$DisplayNet = number_format($myrow2['fxnet'],2);
				$sql = "SELECT taxrate FROM stockmovestaxes WHERE stkmoveno = '".$myrow2['stkmoveno']."'";
				$res = DB_query($sql,$db);
				if(DB_num_rows($res)>=1){
					// si tiene tax no sumar a 0% IVA
					$trate = DB_fetch_array($res);
					if($trate['taxrate']!=0){
						//su tax no es cero
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

				//cantidad
				$pdf->addTextWrap(369,$YPos,54,8,$DisplayQty, 'right');

				//precio
				$pdf->addTextWrap(423,$YPos,72,8,$DisplayPrice, 'right');

				//total
				$pdf->addTextWrap(504,$YPos,81,8,$DisplayNet, 'right');

				//Codigo
				$pdf->addTextWrap(30,$YPos,81,7,$myrow2['stockid'], 'left');

				//descripcion or narrative
				if($myrow['rh_printnarrative']==0){
					$pdf->y = $pdf->y - 9.5;
					$pdf->x = 117;
					$pdf->SetFontSize(7);
					$pdf->MultiCell(240,$line_height,$myrow2['description'],0,'L',0,15);
					$YPos = ($Page_Height - $pdf->GetY()) - $line_height;
				}else{
					$pdf->y = $pdf->y - 9.5;
					$pdf->x = 117;
					$pdf->SetFontSize(7);
					$pdf->MultiCell(240,$line_height,$myrow2['narrative'],0,'L',0,15);
					$YPos = ($Page_Height - $pdf->GetY()) - $line_height;
				}

				//Comentarios
				/*
				$pdf->x = 121.5;
				$pdf->SetFontSize(7);
				$pdf->MultiCell(288,$line_height,$myrow2['narrative'],0,'J',0,15);
				$YPos = ($Page_Height - $pdf->GetY()) - $line_height;
				*/

				$FirstPage = 0;
			} //end while there are line items to print out
			$FirstPage = 0;
		} /*end if there are stock movements to show on the invoice or credit note*/

		if ($InvOrCredit=='Invoice') {

		     $DisplaySubTot = number_format($myrow['ovamount'],2);
		     $DisplayFreight = number_format($myrow['ovfreight'],2);
		     $DisplayTax = number_format($myrow['ovgst'],2);
		     $DisplayTotal = number_format($myrow['ovgst']+$myrow['ovamount'],2);
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

		if($myrow['currcode']=='MN'){
			$curr = ' pesos ';
		}else if($myrow['currcode']=='USD'){

			$curr = ' dolares ';
		}

		$sql = "SELECT currency FROM currencies WHERE currabrev = '".$myrow['currcode']."'";
		$curr_res = DB_query($sql,$db);
		$currencystr = DB_fetch_array($curr_res);

		$tot = explode(".",$Total);
		$Letra = Numbers_Words::toWords($tot[0],"es");
		if($tot[1]==0){
		$ConLetra = $Letra.' '.$currencystr['currency']." 00/100 ".$myrow['currcode'];
		}else if(strlen($tot[1])>=2){
		$ConLetra = $Letra.' '.$currencystr['currency'].' '.$tot[1]."/100 ".$myrow['currcode'];
		}else {
		$ConLetra = $Letra.' '.$currencystr['currency'].' '.$tot[1]."0/100 ".$myrow['currcode'];
		}

		//total letra
		//$pdf->addTextWrap(126,153,288,8,"(".$ConLetra.")", 'left');
		$pdf->y = $Page_Height - 113;
		$pdf->x = 36;
		$pdf->SetFontSize(8);
		$pdf->MultiCell(432,$line_height,strtoupper($ConLetra),0,'J',0,15);

		//subtotal
		$pdf->addTextWrap(504,81,81,10,$DisplaySubTot, 'right');
/*
		//iva %
		if($myrow['taxgroupid']==8){
			$pdf->addTextWrap(360,144,72,10,'10%', 'right');
		}else {
			$pdf->addTextWrap(360,144,72,10,'15%', 'right');
		}
*/
		//iva
		$pdf->addTextWrap(504,63,81,10,$DisplayTax, 'right');

		//total
		$pdf->addTextWrap(504,40.5,81,10,$DisplayTotal, 'right');

	    } /* end of check to see that there was an invoice record to print */

	    $FromTransNo++;
	} /* if type == 10 */

	}


	// if($_GET['SendMail'] == true){
 //        if(!empty($_GET['Ret'])){
 //            $Ret = $_GET['Ret'];
 //        }
 //        if(!empty($_GET['name'])){
 //            $name = $_GET['name'];
 //        }
 //        $pdfcode = $pdf->output('', $Ret);
 //        return $pdfcode;
	// }else
    {
    	$buf = $pdf->output('','S');
    	$len = strlen($buf);
        if($_GET['SendMail'] == true) return $buf;

    	if ($len < 1000) {
    		$title = _('Dunning Letter') . ' - ' . _('Problem Report') . '....';
    		include('includes/header.inc');
    		prnMsg(_('There are no customers meeting the critiera specified to list'),'info');
    		if ($debug==1){
    			prnMsg($SQL,'info');
    		}
    		echo "<BR><A HREF='$rootpath/index.php'>" . _('Back to the menu') . '</A>';
    		include('includes/footer.inc');
    		exit;
    	}
    }
/**************************************************************************
* Jorge Garcia
* 21/Nov/2008
***************************************************************************/
	if (isset($_GET['Email'])){ //email the invoice to address supplied
		$_SESSION['MAILBODY'] = str_replace('\\\\','',$_SESSION['MAILBODY']);
		$_SESSION['MAILBODY'] = stripslashes($_SESSION['MAILBODY']);
		include ('includes/htmlMimeMail.php');
		$mail = new htmlMimeMail();
		$filename = $_SESSION['reports_dir'] . '/DunningLetter.pdf';
		$fp = fopen($filename, 'wb');
		fwrite ($fp, $buf);
		fclose ($fp);

		$attachment = $mail->getFile($filename);
		$mail->setHtml($_SESSION['MAILBODY']);
		$mail->SetSubject($_GET['Subject']);
		$mail->addAttachment($attachment, $filename, 'application/pdf');
		$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
		$result = $mail->send(array($_GET['Email']));

		unlink($filename); //delete the temporary file

		$title = _('Emailing');
		include('includes/header.inc');
		echo "<P>". _('Has been emailed to') . ' ' . $_GET['Email'];
		include('includes/footer.inc');
		unset($_SESSION['MAILBODY']);
		exit;

	} else {
		header('Content-type: application/pdf');
		header("Content-Length: $len");
		header('Content-Disposition: inline; filename=DunningLetter.pdf');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');

		//$pdf->stream();
        echo $buf;
	}
/**************************************************************************
* Jorge Garcia Fin Modificacion
***************************************************************************/
	// do while loop for every invoice
	$sql = "INSERT INTO rh_dunninglts (user_,debtorno,printdate,narrative,total) VALUES ('".$_SESSION['UserID']."','".$_GET['DebtorNo']."',
	'".Date("Y-m-d")."','"._('Balance').': '.$DisplayTotBalance.' - '._('Current').': '.$DisplayTotCurrent.' - '._('Due Now').': '.$DisplayTotDue."',
	'".$TotDue."')";
	$r = DB_query($sql,$db);
	// end of while loop for every invoice

	// print the related invoies
	//echo '<A HREF="'.$rootpath.'/rh_PrintCustTrans.php?' . SID . 'FromTransNo='.$InvoiceNo.'&InvOrCredit=Invoice&PrintPDF=True">'. _('Print this invoice'). '</A><BR>';

// bowikaxu realhost - show debtors listing
} else if(isset($_POST['ShowTrans']) AND isset($_POST['FromCriteria'])
	AND strlen($_POST['FromCriteria'])>=1){ // SHOW CLIENT LISTING TO PRINT DESIRED DUNNING LETTERS

	include('includes/session.inc');
	$title = _('Dunning Letter - Results');
	include('includes/header.inc');
	//include('config.php');
	//include('includes/PDFStarter.php');
	include('includes/ConnectDB.inc');
	//include('includes/DateFunctions.inc');
	?>


<!-- Peticion Ajax a Metodo de Afiliaciones Yii para envio de MailMasivo -->
<script>
    $(document).on('ready', function(){

        $("#SendRecordatorioR").click(function(event) {
            if(this.checked){
                $('.SendMail').attr('checked','checked')
            }else{
                $('.SendMail').removeAttr('checked');
            }
        });

        $("#SendDunningL").click(function(event) {
            if(this.checked){
                $('.SendMailDunning').attr('checked','checked')
            }else{
                $('.SendMailDunning').removeAttr('checked');
            }
        });


        $("#ProccessMailing").click(function(event){
            $.blockUI();
            var jqxhr = $.ajax({
                url: "<?=$rootpath?>/modulos/index.php?r=afiliaciones/SendRecordatorioPago",
                type: "POST",
                dataType : "json",
                timeout : (120 * 100000),
                data: {
                    SendMail:{
                        Customers: $('.SendMail').serialize(),
                        Tipo: 'RecordatorioPago'
                    },
                },
                success : function(Response, newValue) {
                    $.unblockUI();
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                    }else{
                        displayNotify('error', Response.message);
                    }
                },
                error : ajaxError
            });
        });

        $("#ProccessMailingDunning").click(function(event){
            $.blockUI();
            var jqxhr = $.ajax({
                url: "<?=$rootpath?>/modulos/index.php?r=afiliaciones/Senddunningletter",
                type: "POST",
                dataType : "json",
                timeout : (120 * 100000),
                data: {
                    SendMail:{
                        Customers: $('.SendMailDunning').serialize(),
                        Tipo: 'DunningLetter'
                    },
                },
                success : function(Response, newValue) {
                    $.unblockUI();
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                    }else{
                        displayNotify('error', Response.message);
                    }
                },
                error : ajaxError
            });
        });


    });
</script>


	<?php
	echo "<A HREF='rh_DunningLetter.php'>"._('Go Back')."</A>";

echo <<<table
<center>

<table>
    <tr>
        <td colspan='2' align='center'>
            <!-- <input type='submit' id='ver' name='ver' class='btn btn-info' value='VER'/>
            <input type='submit' id='XLS' name='XLS' class='btn btn-success' value='Excel'/>
            <input type='submit' id='PDF' name='PDF' class='btn btn-success' value='Imprime PDF'/> -->
            <!-- <input type='button' id='ProccessMailingDunning' name='ProccessMailingDunning' class='btn btn-danger' value='Enviar Dunning Letter a Clientes Seleccionados'/>-->
            <input type='button' id='ProccessMailing' name='ProccessMailing' class='btn btn-danger' value='Enviar Recordatorio de Pago a Clientes Seleccionados'/>

        </td>
    </tr>
</table>

</center>
table;


	if (trim($_POST['Salesman'])!=''){
		$SalesLimit = " and debtorsmaster.debtorno in (SELECT DISTINCT debtorno FROM custbranch where salesman = ".$_POST['Salesman'].") ";
	} else {
		$SalesLimit = "";
	}

    if(!empty($_POST['Afil_Status'])){
        $AfilStatus = "{$_POST['Afil_Status']}";
    }

    $DebtorsBetween = "";
    if(!empty($_POST['FromCriteria'])){
        $DebtorsBetween .= " AND rh_titular.folio >= " . $_POST['FromCriteria'] ." ";
    }

    if(!empty($_POST['ToCriteria'])){
        $DebtorsBetween .= " AND rh_titular.folio <= " . $_POST['ToCriteria'] ." ";
    }

	$SQL = "SELECT debtorsmaster.debtorno,
				debtorsmaster.name,
				currencies.currency,
				paymentterms.terms,
				debtorsmaster.creditlimit,
				holdreasons.dissallowinvoices,
				holdreasons.reasondescription,
                (rh_titular.folio) as AfilNo,
				SUM(
					debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc
				) as balance,
				SUM(
					CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate)) >= paymentterms.daysbeforedue
						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc
						ELSE 0 END
					ELSE
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') .")) >= 0
						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
					END
				) AS due,
				Sum(
					CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate)) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ")
						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
					ELSE
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL ('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . "
						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc
						ELSE 0 END
					END
				) AS overdue1,
				Sum(
					CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate)) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ")
						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
					ELSE
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL ('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays2'] . "
						THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc
						ELSE 0 END
					END
				) AS overdue2
				FROM debtorsmaster
                left join rh_titular on rh_titular.debtorno = debtorsmaster.debtorno,
					paymentterms,
					holdreasons,
					currencies,
					debtortrans
				WHERE debtorsmaster.paymentterms = paymentterms.termsindicator
					AND debtorsmaster.currcode = currencies.currabrev
					AND debtorsmaster.holdreason = holdreasons.reasoncode
					AND debtorsmaster.debtorno = debtortrans.debtorno
                    AND rh_titular.movimientos_afiliacion = '{$AfilStatus}'
                    $DebtorsBetween
					AND debtorsmaster.currcode ='" . $_POST['Currency'] . "'
					$SalesLimit
				GROUP BY debtorsmaster.debtorno,
					debtorsmaster.name,
					currencies.currency,
					paymentterms.terms,
					paymentterms.daysbeforedue,
					paymentterms.dayinfollowingmonth,
					debtorsmaster.creditlimit,
					holdreasons.dissallowinvoices,
					holdreasons.reasondescription
				HAVING
					Sum(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc) >0";


	$CustomerResult = DB_query($SQL,$db,'','',False,False); /*dont trap errors handled below*/
	if (DB_error_no($db) !=0) {
		$title = _('Dunning Letter') . ' - ' . _('Problem Report') . '.... ';
		include('includes/header.inc');
		echo '<P>' . _('The customer details could not be retrieved by the SQL because') . ' ' . DB_error_msg($db);
		echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
		if ($debug==1){
			echo "<BR>$SQL";
		}
		include('includes/footer.inc');
		exit;
	}
	//echo $SQL."<hr>";
	//echo "<INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') , "'>";
	//SAINTS 14/02/2011
	echo "<CENTER>
	<table class='table table-striped table-bordered table-hover' >
		<thead>
			<tr>
            <th CLASS='tableheader'>"._('AfilNo')."</th>
			<th CLASS='tableheader'>"._('Customer Code')."</th>
			<th CLASS='tableheader'>"._('Customer Name')."</th>
			<th CLASS='tableheader' ALIGN=RIGHT>"._('Total Balance')."</th>
			<th CLASS='tableheader' ALIGN=RIGHT>"._('Due Now')."</th>
			<th CLASS='tableheader' ALIGN=RIGHT>"._('30-60 Days Overdue')."</th>
			<th CLASS='tableheader' ALIGN=RIGHT>"._('Over 60 Days Overdue')."</th>
			<th CLASS='tableheader' ALIGN=RIGHT>"._('Last printed')."</th>
			<th CLASS='tableheader'>"._('Print PDF')."</th>
			<th></th>
            <!-- <th>Dunning Letter <input type='checkbox' id='SendDunningL'></th> -->
            <th>Recordatorio de Pago <input type='checkbox' id='SendRecordatorioR'></th>
			<!--<TD CLASS='tableheader'>"._('Print PDF')."</TD>-->
			</tr>
		</thead>
		<tbody>";
	$k = 0; //row colour counter
    echo $TotslClientes = DB_num_rows($CustomerResult,$db);
	while($AgedAnalysis = DB_fetch_array($CustomerResult,$db)){

		$sql = "SELECT MAX(printdate) AS max FROM rh_dunninglts WHERE debtorno = '".$AgedAnalysis['debtorno']."'";
		$res = DB_query($sql,$db);
		$prDate = DB_fetch_array($res);
//AKI
		echo "	<TD>".$AgedAnalysis['AfilNo']."</TD>
                <TD>".$AgedAnalysis['debtorno']."</TD>
                <TD>".$AgedAnalysis['name']."</TD>
                <TD ALIGN=RIGHT>".number_format($AgedAnalysis['balance'],2)."</TD>
                <TD ALIGN=RIGHT>".number_format($AgedAnalysis['due']-$AgedAnalysis['overdue1'],2)."</TD>
                <TD ALIGN=RIGHT>".number_format($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2'],2)."</TD>
                <TD ALIGN=RIGHT>".number_format($AgedAnalysis['overdue2'],2)."</TD>
                <TD>".$prDate['max']."</TD>
                <td><a href='{$rootpath}/modulos/index.php?r=afiliaciones/Recordatoriopagopdf&Folio={$AgedAnalysis['AfilNo']}' target='_blank' > "._('Print')." </a></td>
                <!-- <TD ALIGN=center><A HREF='rh_DunningLetter.php?DebtorNo=".$AgedAnalysis['debtorno']."'>"._('Print')."</A></TD> -->
				".
				/**************************************************************************
				* Jorge Garcia
				* 21/Nov/2008 Mandar por mail
				***************************************************************************/
				"<TD ALIGN=center>
					<!-- <A HREF='rh_EmailDunning.php?DebtorNo=".$AgedAnalysis['debtorno']."&mod=1'><IMG SRC='".$rootpath."/css/".$theme."/images/email.gif' border=0 TITLE='"._('Email').' '._('Dunning')."'></A> -->
				</TD>
				<!--
				<td>
                    <input name='SendMailDunning[]' class='SendMailDunning' value='{$AgedAnalysis['debtorno']}' type='checkbox' >
                </td> -->
                <td>
                    <input name='SendMail[]' class='SendMail' value='{$AgedAnalysis['debtorno']}' type='checkbox' >
                </td>
				</TR>";

	}

	//echo "<INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') , "'>";
	echo "</tbody></table></CENTER>";
} else { /*The option to print PDF was not hit */

	include('includes/session.inc');
	$title=_('Dunning Letter');
	include('includes/header.inc');

	echo "<CENTER><H2><STRONG>"._('Cartas de Adeudo')."</STRONG></H2></CENTER><BR>";

	if (strlen($_POST['FromCriteria'])<1 ) {

	/*if $FromCriteria is not set then show a form to allow input	*/

		echo '<FORM ACTION=' . $_SERVER['PHP_SELF'] . " METHOD='POST'><CENTER><TABLE>";

		echo '<TR><TD>' . _('Del Folio') . ':' . "</FONT></TD><TD><input Type=text maxlength=6 size=7 name=FromCriteria value='1'></TD></TR>";
		echo '<TR><TD>' . _('Al Folio') . ':' . "</TD><TD><input Type=text maxlength=6 size=7 name=ToCriteria value=''></TD></TR>";

		echo '<TR><TD>' . _('Only Show Customers Of') . ':' . "</TD><TD><SELECT name='Salesman'>";

		$sql = 'SELECT salesmancode, salesmanname FROM salesman';

		$result=DB_query($sql,$db);
		echo "<OPTION Value=''></OPTION>";
		while ($myrow=DB_fetch_array($result)){
				echo "<OPTION Value='" . $myrow['salesmancode'] . "'>" . $myrow['salesmanname'];
		}
		echo '</SELECT></TD></TR>';


		echo '<TR><TD>' . _('Only show customers trading in') . ':' . "</TD><TD><SELECT name='Currency'>";

		$sql = 'SELECT currency, currabrev FROM currencies';

		$result=DB_query($sql,$db);


		while ($myrow=DB_fetch_array($result)){
		      if ($myrow['currabrev'] == $_SESSION['CompanyRecord']['currencydefault']){
				echo "<OPTION SELECTED Value='" . $myrow['currabrev'] . "'>" . $myrow['currency'];
		      } else {
			      echo "<OPTION Value='" . $myrow['currabrev'] . "'>" . $myrow['currency'];
		      }
		}
		echo '</SELECT></TD></TR>';

        echo "<tr><td>Status</td><td>";
        echo "<SELECT name='Afil_Status'>
                <option value='Activo' >Activo</option>
                <option value='Suspendido' >Suspendido</option>
                <option value='Cancelado' >Cancelado</option>
            </SELECT>";
        echo "</td></tr>";
		/*
		echo '<TR><TD>' . _('Summary or detailed report') . ':' . "</TD>
			<TD><SELECT name='DetailedReport'>";
		echo "<OPTION Value='No'>" . _('Summary Report');
		echo "<OPTION SELECTED Value='Yes'>" . _('Detailed Report');
		echo '</SELECT></TD></TR>';
		*/
		echo "</TABLE><INPUT TYPE=Submit Name='ShowTrans' Value='" . _('Show') , "'></CENTER></FORM>";
	}
	include('includes/footer.inc');

} /*end of else not PrintPDF */

?>
