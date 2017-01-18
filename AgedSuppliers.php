<?php
/* $Revision: 112 $ */
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-03-07 09:07:48 -0600 (Fri, 07 Mar 2008) $
 * $Rev: 112 $
 */
$PageSecurity = 2;
include('includes/session.inc');

// bowikaxu - inital dates
if(!isset($_POST['FromDate'])){
	$_POST['FromDate']=Date($_SESSION["DefaultDateFormat"]);
}

if(!isset($_POST['ToDate'])){
	$_POST['ToDate']=Date($_SESSION["DefaultDateFormat"]);
}
ob_start();
// bowikaxu realhost january 2008 - EXCEL -----------
If (isset($_POST['Excel'])
AND isset($_POST['FromCriteria'])
AND strlen($_POST['FromCriteria'])>=1
AND isset($_POST['ToCriteria'])
AND strlen($_POST['ToCriteria'])>=1){

	require ("includes/class-excel-xml.inc.php");
	$ii=2;
	$xls = new Excel_XML;
	$doc = array(1=>array(_('Supplier'),_('Balance'),_('Current'),_('Due Now'),'> ' . $_SESSION['PastDueDays1'] . ' ' . _('Days Over'),'> ' . $_SESSION['PastDueDays2'] . ' ' . _('Days Over')));

	/*Now figure out the aged analysis for the Supplier range under review */
	if ($_POST['All_Or_Overdues']=='All'){
		$SQL = "SELECT suppliers.supplierid, suppliers.suppname, currencies.currency, paymentterms.terms,
		SUM(supptrans.ovamount + supptrans.ovgst  - supptrans.alloc) as balance,
		SUM(CASE WHEN paymentterms.daysbeforedue > 0 THEN
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(supptrans.trandate)) >= paymentterms.daysbeforedue  THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
		ELSE
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= 0 THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
		END) AS due,
		Sum(CASE WHEN paymentterms.daysbeforedue > 0 THEN
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
		ELSE
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') ."), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
		END) AS overdue1,
		Sum(CASE WHEN paymentterms.daysbeforedue > 0 THEN
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue	AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
		ELSE
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays2'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
		END) AS overdue2
		FROM suppliers, paymentterms, currencies, supptrans WHERE suppliers.paymentterms = paymentterms.termsindicator
		AND suppliers.currcode = currencies.currabrev
		AND suppliers.supplierid = supptrans.supplierno
		AND suppliers.supplierid >= '" .$_POST['FromCriteria'] . "'
		AND suppliers.supplierid <= '" . $_POST['ToCriteria'] . "'
		AND  suppliers.currcode ='" . $_POST['Currency'] . "'
		GROUP BY suppliers.supplierid,
			suppliers.suppname,
			currencies.currency,
			paymentterms.terms,
			paymentterms.daysbeforedue,
			paymentterms.dayinfollowingmonth
		HAVING Sum(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) > ".$rh_umbral_asignacion." OR
				Sum(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) < ".(-1*$rh_umbral_asignacion)."";
	
	} else {
	
		$SQL = "SELECT suppliers.supplierid,
		      		suppliers.suppname,
				currencies.currency,
				paymentterms.terms,
				SUM(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) AS balance,
				SUM(CASE WHEN paymentterms.daysbeforedue > 0 THEN
					CASE WHEN (TO_DAYS(Now()) - TO_DAYS(supptrans.trandate)) >= paymentterms.daysbeforedue  THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
				ELSE
					CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= 0 THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
				END) AS due,
				Sum(CASE WHEN paymentterms.daysbeforedue > 0 THEN
					CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
				ELSE
					CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
				END) AS overdue1,
				SUM(CASE WHEN paymentterms.daysbeforedue > 0 THEN
					CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue	AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
				ELSE
					CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays2'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
				END) AS overdue2
				FROM suppliers,
					paymentterms,
					currencies,
					supptrans
				WHERE suppliers.paymentterms = paymentterms.termsindicator
				AND suppliers.currcode = currencies.currabrev
				and suppliers.supplierid = supptrans.supplierno
				AND suppliers.supplierid >= '" . $_POST['FromCriteria'] . "'
				AND suppliers.supplierid <= '" . $_POST['ToCriteria'] . "'
				AND suppliers.currcode ='" . $_POST['Currency'] . "'
				GROUP BY suppliers.supplierid,
					suppliers.suppname,
					currencies.currency,
					paymentterms.terms,
					paymentterms.daysbeforedue,
					paymentterms.dayinfollowingmonth
				HAVING (Sum(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) > ".$rh_umbral_asignacion." OR
				Sum(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) < ".(-1*$rh_umbral_asignacion)."),
				Sum(IF (paymentterms.daysbeforedue > 0,
				CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END,
				CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END)) > 0";
	
	}

	$SupplierResult = DB_query($SQL,$db,'','',False,False); /*dont trap errors */

	if (DB_error_no($db) !=0) {
		$title = _('Aged Supplier Account Analysis') . ' - ' . _('Problem Report') ;
		include("includes/header.inc");
		prnMsg(_('The Supplier details could not be retrieved by the SQL because') .  ' ' . DB_error_msg($db),'error');
		echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
		if ($debug==1){
			echo "<BR>$SQL";
		}
		include('includes/footer.inc');
		exit;
	}

	While ($AgedAnalysis = DB_fetch_array($SupplierResult,$db)){

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
/*
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,220-$Left_Margin,$FontSize,$AgedAnalysis['supplierid'] . ' - ' . $AgedAnalysis['suppname'],'left');
		$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayBalance,'right');
		$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayCurrent,'right');
		$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayDue,'right');
		$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayOverdue1,'right');
		$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayOverdue2,'right');
*/
		// bowikaxu doc
		$doc[$ii] = array($AgedAnalysis['supplierid'] . ' - ' . $AgedAnalysis['suppname'],$DisplayBalance,$DisplayCurrent,$DisplayDue,$DisplayOverdue1,$DisplayOverdue2);
		$ii++;

		if ($_POST['DetailedReport']=='Yes'){
			$sql = "SELECT systypes.typename, supptrans.suppreference, supptrans.trandate,
						   (supptrans.ovamount + supptrans.ovgst - supptrans.alloc) as balance,
						   CASE WHEN paymentterms.daysbeforedue > 0 THEN
						   	CASE WHEN (TO_DAYS(Now()) - TO_DAYS(supptrans.trandate)) >= paymentterms.daysbeforedue  THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
						   ELSE
						   	CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= 0 THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
						   END AS due,
						   CASE WHEN paymentterms.daysbeforedue > 0 THEN
						   	CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue	   AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
						   ELSE
						   	CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1','MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
						   END AS overdue1,
						   CASE WHEN paymentterms.daysbeforedue > 0 THEN
						   	CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
						   ELSE
						   	CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1','MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays2'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
						   END AS overdue2
						   FROM suppliers,
						   	paymentterms,
							supptrans,
							systypes
						   WHERE systypes.typeid = supptrans.type
						   AND suppliers.paymentterms = paymentterms.termsindicator
						   AND suppliers.supplierid = supptrans.supplierno
						   AND ABS(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) >0.009
						   AND supptrans.settled = 0
						   AND supptrans.supplierno = '" . $AgedAnalysis["supplierid"] . "'
			               order by supptrans.trandate";
			$DetailResult = DB_query($sql,$db,'','',False,False); /*dont trap errors - trapped below*/
			if (DB_error_no($db) !=0) {
				$title = _('Aged Supplier Account Analysis - Problem Report');
				include('includes/header.inc');
				echo '<BR>' . _('The details of outstanding transactions for Supplier') . ' - ' . $AgedAnalysis['supplierid'] . ' ' . _('could not be retrieved because') . ' - ' . DB_error_msg($db);
				echo "<BR><A HREF='$rootpath/index.php'>" . _('Back to the menu') . '</A>';
				if ($debug==1){
					echo '<BR>' . _('The SQL that failed was') . '<BR>' . $sql;
				}
				include('includes/footer.inc');
				exit;
			}
			$doc[$ii] = array('_______________');
			$ii++;
			while ($DetailTrans = DB_fetch_array($DetailResult)){
				$DisplayTranDate = ConvertSQLDate($DetailTrans['trandate']);
/*
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,60,$FontSize,$DetailTrans['typename'],'left');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,50,$FontSize,$DetailTrans['suppreference'],'left');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+105,$YPos,70,$FontSize,$DisplayTranDate,'left');
*/
				$DisplayDue = number_format($DetailTrans['due']-$DetailTrans['overdue1'],2);
				$DisplayCurrent = number_format($DetailTrans['balance']-$DetailTrans['due'],2);
				$DisplayBalance = number_format($DetailTrans['balance'],2);
				$DisplayOverdue1 = number_format($DetailTrans['overdue1']-$DetailTrans['overdue2'],2);
				$DisplayOverdue2 = number_format($DetailTrans['overdue2'],2);
/*
				$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayBalance,'right');
				$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayCurrent,'right');
				$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayDue,'right');
				$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayOverdue1,'right');
				$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayOverdue2,'right');
*/
				$doc[$ii] = array($DetailTrans['typename'].' '.$DetailTrans['suppreference'].' '.$DisplayTranDate,$DisplayBalance,$DisplayCurrent,$DisplayDue,$DisplayOverdue1,$DisplayOverdue2);
				$ii++;

			} /*end while there are detail transactions to show */
			/*draw a line under the detailed transactions before the next Supplier aged analysis*/
			//$pdf->line($Page_Width-$Right_Margin, $YPos+10,$Left_Margin, $YPos+10);
		} /*Its a detailed report */
	} /*end Supplier aged analysis while loop */

	$DisplayTotBalance = number_format($TotBal,2);
	$DisplayTotDue = number_format($TotDue,2);
	$DisplayTotCurrent = number_format($TotCurr,2);
	$DisplayTotOverdue1 = number_format($TotOD1,2);
	$DisplayTotOverdue2 = number_format($TotOD2,2);
/*
	$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayTotBalance,'right');
	$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayTotCurrent,'right');
	$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayTotDue,'right');
	$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayTotOverdue1,'right');
	$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayTotOverdue2,'right');
*/

	$xls->addArray ( $doc );
	ob_end_clean();
	$xls->generateXML ("AgedSuppliers");
	exit;
}

	// bowikaxu realhost - PDF ------------
If (isset($_POST['PrintPDF'])
	AND isset($_POST['FromCriteria'])
	AND strlen($_POST['FromCriteria'])>=1
	AND isset($_POST['ToCriteria'])
	AND strlen($_POST['ToCriteria'])>=1){


	include('includes/PDFStarter.php');

	$FontSize=12;
	$pdf->addinfo('Title',_('Aged Supplier Listing'));
	$pdf->addinfo('Subject',_('Aged Suppliers'));

	$PageNumber=0;
	$line_height=12;

      /*Now figure out the aged analysis for the Supplier range under review */

	if ($_POST['All_Or_Overdues']=='All'){
		$SQL = "SELECT suppliers.supplierid, suppliers.suppname, currencies.currency, paymentterms.terms,
	SUM(supptrans.ovamount + supptrans.ovgst  - supptrans.alloc) as balance,
	SUM(CASE WHEN paymentterms.daysbeforedue > 0 THEN
		CASE WHEN (TO_DAYS(Now()) - TO_DAYS(supptrans.trandate)) >= paymentterms.daysbeforedue  THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
	ELSE
		CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= 0 THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
	END) AS due,
	Sum(CASE WHEN paymentterms.daysbeforedue > 0 THEN
		CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
	ELSE
		CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') ."), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
	END) AS overdue1,
	Sum(CASE WHEN paymentterms.daysbeforedue > 0 THEN
		CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue	AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
	ELSE
		CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays2'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
	END) AS overdue2
	FROM suppliers, paymentterms, currencies, supptrans WHERE suppliers.paymentterms = paymentterms.termsindicator
	AND suppliers.currcode = currencies.currabrev
	AND suppliers.supplierid = supptrans.supplierno
	AND suppliers.supplierid >= '" .$_POST['FromCriteria'] . "'
	AND suppliers.supplierid <= '" . $_POST['ToCriteria'] . "'
	AND  suppliers.currcode ='" . $_POST['Currency'] . "'
	GROUP BY suppliers.supplierid,
		suppliers.suppname,
		currencies.currency,
		paymentterms.terms,
		paymentterms.daysbeforedue,
		paymentterms.dayinfollowingmonth
	HAVING Sum(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) > ".$rh_umbral_asignacion." OR
			Sum(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) < ".(-1*$rh_umbral_asignacion)."";

	} else {

	      $SQL = "SELECT suppliers.supplierid,
	      		suppliers.suppname,
			currencies.currency,
			paymentterms.terms,
			SUM(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) AS balance,
			SUM(CASE WHEN paymentterms.daysbeforedue > 0 THEN
				CASE WHEN (TO_DAYS(Now()) - TO_DAYS(supptrans.trandate)) >= paymentterms.daysbeforedue  THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
			ELSE
				CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= 0 THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
			END) AS due,
			Sum(CASE WHEN paymentterms.daysbeforedue > 0 THEN
				CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
			ELSE
				CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
			END) AS overdue1,
			SUM(CASE WHEN paymentterms.daysbeforedue > 0 THEN
				CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue	AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
			ELSE
				CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays2'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
			END) AS overdue2
			FROM suppliers,
				paymentterms,
				currencies,
				supptrans
			WHERE suppliers.paymentterms = paymentterms.termsindicator
			AND suppliers.currcode = currencies.currabrev
			and suppliers.supplierid = supptrans.supplierno
			AND suppliers.supplierid >= '" . $_POST['FromCriteria'] . "'
			AND suppliers.supplierid <= '" . $_POST['ToCriteria'] . "'
			AND suppliers.currcode ='" . $_POST['Currency'] . "'
			GROUP BY suppliers.supplierid,
				suppliers.suppname,
				currencies.currency,
				paymentterms.terms,
				paymentterms.daysbeforedue,
				paymentterms.dayinfollowingmonth
			HAVING (Sum(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) > ".$rh_umbral_asignacion." OR
			Sum(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) < ".(-1*$rh_umbral_asignacion)."),
			Sum(IF (paymentterms.daysbeforedue > 0,
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END,
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END)) > 0";

	}

	$SupplierResult = DB_query($SQL,$db,'','',False,False); /*dont trap errors */

	if (DB_error_no($db) !=0) {
	  $title = _('Aged Supplier Account Analysis') . ' - ' . _('Problem Report') ;
	  include("includes/header.inc");
	  prnMsg(_('The Supplier details could not be retrieved by the SQL because') .  ' ' . DB_error_msg($db),'error');
	   echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
	   if ($debug==1){
		echo "<BR>$SQL";
	   }
	   include('includes/footer.inc');
	   exit;
	}

	include ('includes/PDFAgedSuppliersPageHeader.inc');
	$TotBal = 0;
	$TotDue = 0;
	$TotCurr = 0;
	$TotOD1 = 0;
	$TotOD2 = 0;

	While ($AgedAnalysis = DB_fetch_array($SupplierResult,$db)){

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

		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,220-$Left_Margin,$FontSize,$AgedAnalysis['supplierid'] . ' - ' . $AgedAnalysis['suppname'],'left');
		$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayBalance,'right');
		$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayCurrent,'right');
		$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayDue,'right');
		$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayOverdue1,'right');
		$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayOverdue2,'right');

		$YPos -=$line_height;
		if ($YPos < $Bottom_Margin + $line_height){
		      include('includes/PDFAgedSuppliersPageHeader.inc');
		}

		if ($_POST['DetailedReport']=='Yes'){

		   $FontSize=6;
		   /*draw a line under the Supplier aged analysis*/
		   $pdf->line($Page_Width-$Right_Margin, $YPos+10,$Left_Margin, $YPos+10);

		   $sql = "SELECT systypes.typename, supptrans.suppreference, supptrans.trandate,
			   (supptrans.ovamount + supptrans.ovgst - supptrans.alloc) as balance,
			   CASE WHEN paymentterms.daysbeforedue > 0 THEN
			   	CASE WHEN (TO_DAYS(Now()) - TO_DAYS(supptrans.trandate)) >= paymentterms.daysbeforedue  THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
			   ELSE
			   	CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= 0 THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
			   END AS due,
			   CASE WHEN paymentterms.daysbeforedue > 0 THEN
			   	CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue	   AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
			   ELSE
			   	CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1','MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
			   END AS overdue1,
			   CASE WHEN paymentterms.daysbeforedue > 0 THEN
			   	CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
			   ELSE
			   	CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1','MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays2'] . ") THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
			   END AS overdue2
			   FROM suppliers,
			   	paymentterms,
				supptrans,
				systypes
			   WHERE systypes.typeid = supptrans.type
			   AND suppliers.paymentterms = paymentterms.termsindicator
			   AND suppliers.supplierid = supptrans.supplierno
			   AND ABS(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) >0.009
			   AND supptrans.settled = 0
			   AND supptrans.supplierno = '" . $AgedAnalysis["supplierid"] . "'
               order by supptrans.trandate";

		    $DetailResult = DB_query($sql,$db,'','',False,False); /*dont trap errors - trapped below*/
		    if (DB_error_no($db) !=0) {
			$title = _('Aged Supplier Account Analysis - Problem Report');
			include('includes/header.inc');
			echo '<BR>' . _('The details of outstanding transactions for Supplier') . ' - ' . $AgedAnalysis['supplierid'] . ' ' . _('could not be retrieved because') . ' - ' . DB_error_msg($db);
			echo "<BR><A HREF='$rootpath/index.php'>" . _('Back to the menu') . '</A>';
			if ($debug==1){
			   echo '<BR>' . _('The SQL that failed was') . '<BR>' . $sql;
			}
			include('includes/footer.inc');
			exit;
		    }

		    while ($DetailTrans = DB_fetch_array($DetailResult)){

			    $LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,60,$FontSize,$DetailTrans['typename'],'left');
			    $LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,50,$FontSize,$DetailTrans['suppreference'],'left');
			    $DisplayTranDate = ConvertSQLDate($DetailTrans['trandate']);
			    $LeftOvers = $pdf->addTextWrap($Left_Margin+105,$YPos,70,$FontSize,$DisplayTranDate,'left');

			    $DisplayDue = number_format($DetailTrans['due']-$DetailTrans['overdue1'],2);
			    $DisplayCurrent = number_format($DetailTrans['balance']-$DetailTrans['due'],2);
			    $DisplayBalance = number_format($DetailTrans['balance'],2);
			    $DisplayOverdue1 = number_format($DetailTrans['overdue1']-$DetailTrans['overdue2'],2);
			    $DisplayOverdue2 = number_format($DetailTrans['overdue2'],2);

			    $LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayBalance,'right');
			    $LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayCurrent,'right');
			    $LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayDue,'right');
			    $LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayOverdue1,'right');
			    $LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayOverdue2,'right');

			    $YPos -=$line_height;
			    if ($YPos < $Bottom_Margin + $line_height){
				$PageNumber++;
				include('includes/PDFAgedSuppliersPageHeader.inc');
				$FontSize=6;
			    }
		    } /*end while there are detail transactions to show */
		    /*draw a line under the detailed transactions before the next Supplier aged analysis*/
		   $pdf->line($Page_Width-$Right_Margin, $YPos+10,$Left_Margin, $YPos+10);
		   $FontSize=8;
		} /*Its a detailed report */
	} /*end Supplier aged analysis while loop */

	$YPos -=$line_height;
	if ($YPos < $Bottom_Margin + (2*$line_height)){
		$PageNumber++;
		include('includes/PDFAgedSuppliersPageHeader.inc');
	} elseif ($_POST['DetailedReport']=='Yes') {
		//dont do a line if the totals have to go on a new page
		$pdf->line($Page_Width-$Right_Margin, $YPos+10 ,220, $YPos+10);
	}

	$DisplayTotBalance = number_format($TotBal,2);
	$DisplayTotDue = number_format($TotDue,2);
	$DisplayTotCurrent = number_format($TotCurr,2);
	$DisplayTotOverdue1 = number_format($TotOD1,2);
	$DisplayTotOverdue2 = number_format($TotOD2,2);

	$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayTotBalance,'right');
	$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayTotCurrent,'right');
	$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayTotDue,'right');
	$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayTotOverdue1,'right');
	$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayTotOverdue2,'right');

	$YPos -=$line_height;
	$pdf->line($Page_Width-$Right_Margin, $YPos ,220, $YPos);

	$buf = $pdf->output();
	$len = strlen($buf);
	header('Content-type: application/pdf');
	header("Content-Length: $len");
	header('Content-Disposition: inline; filename=AgedSuppliers.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	ob_end_clean();
	$pdf->stream();
	exit;

} 
{ /*The option to print PDF was not hit */

	$title = _('Aged Supplier Analysis');
	include('includes/header.inc');

	if (!isset($_POST['FromCriteria']) OR !isset($_POST['ToCriteria'])) {

	/*if $FromCriteria is not set then show a form to allow input	*/

		echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID . "' METHOD='POST'><CENTER><TABLE>";

		echo '<TR><TD>' . _('From Supplier Code') . ":</FONT></TD>
			<TD><input tabindex='1' Type=text maxlength=6 size=7 name=FromCriteria value='0'></TD>
		</TR>";
		echo '<TR><TD>' . _('To Supplier Code') . ":</TD>
			<TD><input tabindex='2' Type=text maxlength=12 size=12 name=ToCriteria value='zzzzzz'></TD>
		</TR>";

		// bowikaxu realhost january 2008 - from to date
		/*
		echo '<TR><TD>' . _('From Trans. Date') . ":</FONT></TD>
			<TD><input Type=text maxlength=10 size=10 name=FromDate value='".$_POST['FromDate']."'></TD>
		</TR>";
		echo '<TR><TD>' . _('To Trans. Date') . ":</TD>
			<TD><input Type=text maxlength=10 size=10 name=ToDate value='".$_POST['ToDate']."'></TD>
		</TR>";
		*/
		echo '<TR><TD>' . _('All balances or overdues only') . ':' . "</TD>
			<TD><SELECT tabindex='3' name='All_Or_Overdues'>";
		echo "<OPTION SELECTED Value='All'>" . _('All suppliers with balances');
		echo "<OPTION Value='OverduesOnly'>" . _('Overdue accounts only');
		echo '</SELECT></TD></TR>';

		echo '<TR><TD>' . _('For suppliers trading in') . ':' . "</TD>
			<TD><SELECT tabindex='4' name='Currency'>";

		$sql = 'SELECT currency, currabrev FROM currencies';
		$result=DB_query($sql,$db);

		while ($myrow=DB_fetch_array($result)){
		      if ($myrow['currabrev'] == $_SESSION['CompanyRecord']['currencydefault']){
				echo "<OPTION SELECTED Value='" . $myrow["currabrev"] . "'>" . $myrow['currency'];
		      } else {
			      echo "<OPTION Value='" . $myrow['currabrev'] . "'>" . $myrow['currency'];
		      }
		}
		echo '</SELECT></TD></TR>';

		echo '<TR><TD>' . _('Summary or Detailed Report') . ':' . "</TD>
			<TD><SELECT tabindex='5' name='DetailedReport'>";
		echo "<OPTION SELECTED Value='No'>" . _('Summary Report');
		echo "<OPTION Value='Yes'>" . _('Detailed Report');
		echo '</SELECT></TD></TR>';

		echo "</TABLE>
		<INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') . "'>
		<INPUT TYPE=Submit Name='Excel' Value='" . _('Excel') . "'>
		</CENTER>";
	}
	include('includes/footer.inc');
} /*end of else not PrintPDF */

?>