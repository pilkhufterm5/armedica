<?php

$PageSecurity = 2;
include('includes/session.inc');
/* $Revision: 385 $ */
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-08-08 12:46:27 -0500 (Fri, 08 Aug 2008) $
 * $Rev: 385 $
 */
// bowikaxu - inital dates
if(!isset($_POST['FromDate'])){
	$_POST['FromDate']=Date($_SESSION["DefaultDateFormat"]);
}

if(!isset($_POST['ToDate'])){
	$_POST['ToDate']=Date($_SESSION["DefaultDateFormat"]);
}

//iJPe
if (isset($_POST['soloFact']))
{
        $soloFact = " AND debtortrans.type = 10 ";
}
else
{
        $soloFact = "";
}

if(isset($_POST['CollectionPath'])&&($_POST['CollectionPath']!='%')){
    $rh_rutas_debtors= " join rh_rutas_debtors on rh_rutas_debtors.debtorno=debtortrans.debtorno and rh_rutas_debtors.branchcode =debtortrans.branchcode and rh_rutas_debtors.idrutas='".$_POST['CollectionPath']."'";
}else{
   $rh_rutas_debtors= "";
}

if(!isset($_SESSION['PastDueDays3']))
$_SESSION['PastDueDays3'] = 90;

if(isset($_POST['Excel'])AND isset($_POST['FromCriteria'])
AND strlen($_POST['FromCriteria'])>=1
AND isset($_POST['ToCriteria'])
AND strlen($_POST['ToCriteria'])>=1){
		$rhDue =' ' . 1 . ' ' ._('To') .' '.  29 .  " "._('Days');
					$rhDays1 = ' ' . 31 . ' ' . _('To') . '  '  . 60 . " "._('Days');
					$rhDays2 = ' ' . 61 . ' ' . _('To') . '  '  . 90 . " " ._('Days');
					$rhDays3 = ' ' . 91 . ' ' . _('Days');

	require ("includes/class-excel-xml.inc.php");
	$ii=2;
	$xls = new Excel_XML;
	$doc = array(1=>array(_('Customer'),_('Days'),_('Balance'),_('Current'),$rhDue,$rhDays1,$rhDays2,$rhDays3));

	if (trim($_POST['Salesman'])!=''){
		$SalesLimit = " and debtorsmaster.debtorno in (SELECT DISTINCT debtorno FROM custbranch where salesman = '".$_POST['Salesman']."') ";
	} else {
		$SalesLimit = "";
	}

	if ($_POST['All_Or_Overdues']=='All'){
		$SQL = "SELECT debtortrans.consignment, debtorsmaster.debtorno,
				debtorsmaster.name,
				currencies.currency,
				currencies.currabrev,
				currencies.rate,
				paymentterms.terms,
				debtorsmaster.creditlimit,
				holdreasons.dissallowinvoices,
				holdreasons.reasondescription,
				SUM(
					(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
				) as balance,
				SUM(
					CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate)) >= paymentterms.daysbeforedue
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
						ELSE 0 END
					ELSE
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') .")) >= 0
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate ELSE 0 END
					END
				) AS due,
				Sum(
					CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate)) > paymentterms.daysbeforedue AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ")
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate ELSE 0 END
					ELSE
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL ('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . "
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
						ELSE 0 END
					END
				) AS overdue1,
				Sum(
					CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate)) > paymentterms.daysbeforedue AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ")
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate ELSE 0 END
					ELSE
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL ('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays2'] . "
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
						ELSE 0 END
					END
				) AS overdue2,
							Sum(CASE WHEN (paymentterms.daysbeforedue > 0)
								THEN
									(
										CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND

										TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays3'] . ")
										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
									 	ELSE 0 END
									)
								ELSE
									(
										CASE WHEN (

											TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= " . $_SESSION['PastDueDays3'] . "

										)

										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate

										ELSE 0 END)
							END) AS overdue3
				FROM debtorsmaster,
					paymentterms,
					holdreasons,
					currencies,
					debtortrans
                     ".$rh_rutas_debtors."
				WHERE debtorsmaster.paymentterms = paymentterms.termsindicator
					AND debtorsmaster.currcode = currencies.currabrev
					AND debtorsmaster.holdreason = holdreasons.reasoncode
					AND debtorsmaster.debtorno = debtortrans.debtorno
					AND debtorsmaster.debtorno >= '" . $_POST['FromCriteria'] . "'
					AND debtorsmaster.debtorno <= '" . $_POST['ToCriteria'] . "'

					$soloFact
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
					ABS(Sum(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)) >= ".$rh_umbral_asignacion."";


	} elseif ($_POST['All_Or_Overdues']=='OverduesOnly') {

		$SQL = "SELECT debtortrans.consignment, debtorsmaster.debtorno,
	      		debtorsmaster.name,
	      		currencies.currency,
				currencies.currabrev,
				currencies.rate,
	      		paymentterms.terms,
			debtorsmaster.creditlimit,
	      		holdreasons.dissallowinvoices,
	      		holdreasons.reasondescription,
			SUM(
	      			(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      		) AS balance,
			SUM(
	      			CASE WHEN (paymentterms.daysbeforedue > 0)
	      				THEN
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= paymentterms.daysbeforedue
	      					THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      					ELSE 0 END
	      				ELSE
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= 0 )
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate ELSE 0 END
					END
	      		) AS due,
			SUM(
		      		CASE WHEN (paymentterms.daysbeforedue > 0)
	      				THEN
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ")
	      					THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      					ELSE 0 END
	      				ELSE
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ")
	      					THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      					ELSE 0 END
					END
	      		) AS overdue1,
			SUM(
		      		CASE WHEN (paymentterms.daysbeforedue > 0)
	      				THEN
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ")
	      					THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      					ELSE 0 END
	      				ELSE
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays2'] . ")
	      					THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      					ELSE 0 END
					END
	      		) AS overdue2,
							sum(CASE WHEN (paymentterms.daysbeforedue > 0)
								THEN
									(
										CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND

										TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays3'] . ")
										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
									 	ELSE 0 END
									)
								ELSE
									(
										CASE WHEN (

											TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= " . $_SESSION['PastDueDays3'] . "

										)

										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate

										ELSE 0 END)
							END) AS overdue3
			FROM debtorsmaster,
	      			paymentterms,
	      			holdreasons,
	      			currencies,
	      			debtortrans
                     ".$rh_rutas_debtors."
	      		WHERE debtorsmaster.paymentterms = paymentterms.termsindicator
	      		AND debtorsmaster.currcode = currencies.currabrev
	      		AND debtorsmaster.holdreason = holdreasons.reasoncode
	      		AND debtorsmaster.debtorno = debtortrans.debtorno
				AND debtorsmaster.debtorno >= '" . $_POST['FromCriteria'] . "'
	      		AND debtorsmaster.debtorno <= '" . $_POST['ToCriteria'] . "'

                                $soloFact
				$SalesLimit
			GROUP BY debtorsmaster.debtorno,
	      			debtorsmaster.name,
	      			currencies.currency,
					currencies.rate,
	      			paymentterms.terms,
	      			paymentterms.daysbeforedue,
	      			paymentterms.dayinfollowingmonth,
	      			debtorsmaster.creditlimit,
	      			holdreasons.dissallowinvoices,
	      			holdreasons.reasondescription
			HAVING ABS(Sum(
				CASE WHEN (paymentterms.daysbeforedue > 0)
	      				THEN
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ")
	      					THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      					ELSE 0 END
	      				ELSE
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ")
	      					THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      					ELSE 0 END
					END
	      			)) >= ".$rh_umbral_asignacion;

	} elseif ($_POST['All_Or_Overdues']=='HeldOnly'){

		$SQL = "SELECT debtortrans.consignment, debtorsmaster.debtorno,
					debtorsmaster.name,
					currencies.currency,
					currencies.currabrev,
					currencies.rate,
					paymentterms.terms,
					debtorsmaster.creditlimit,
					holdreasons.dissallowinvoices,
					holdreasons.reasondescription,
			SUM(debtortrans.ovamount +
				debtortrans.ovgst +
				debtortrans.ovfreight +
				debtortrans.ovdiscount -
				debtortrans.alloc) AS balance,
			SUM(
				CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= paymentterms.daysbeforedue
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
						ELSE 0 END
					ELSE
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate," . INTERVAL('1', 'MONTH') . ")," .  INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= 0)
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
						ELSE 0 END
				END
			) AS due,
			SUM(
				CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue
						AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ")
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate ELSE 0 END
					ELSE
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ")
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
					ELSE 0 END
				END
			) AS overdue1,
			SUM(
				CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue
						AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ")
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
						ELSE 0 END
					ELSE
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= ".$_SESSION['PastDueDays2'] . ")
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
					ELSE 0 END
				END
			) AS overdue2,
							Sum(CASE WHEN (paymentterms.daysbeforedue > 0)
								THEN
									(
										CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND

										TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays3'] . ")

										ght + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
									 	ELSE 0 END
									)
								ELSE
									(
										CASE WHEN (

											TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= " . $_SESSION['PastDueDays3'] . "

										)

										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate

										ELSE 0 END)
							END) AS overdue3
		FROM debtorsmaster,
		paymentterms,
		holdreasons,
		currencies,
		debtortrans
         ".$rh_rutas_debtors."
		WHERE debtorsmaster.paymentterms = paymentterms.termsindicator
		AND debtorsmaster.currcode = currencies.currabrev
		AND debtorsmaster.holdreason = holdreasons.reasoncode
		AND debtorsmaster.debtorno = debtortrans.debtorno
		AND holdreasons.dissallowinvoices=1
		AND debtorsmaster.debtorno >= '" . $_POST['FromCriteria'] . "'
		AND debtorsmaster.debtorno <= '" . $_POST['ToCriteria'] . "'

		$soloFact
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
			ABS(Sum(
				debtortrans.ovamount +
				debtortrans.ovgst +
				debtortrans.ovfreight +
				debtortrans.ovdiscount -
				debtortrans.alloc
			)) >= ".$rh_umbral_asignacion."";
	}

	// bowikaxu - order by
	if($_POST['OrderBy']=='Code'){

		$SQL .= " ORDER BY currencies.rate DESC, debtorsmaster.currcode";

	}else {

		$SQL .= " ORDER BY currencies.rate DESC, debtorsmaster.currcode";

	}

	$CustomerResult = DB_query($SQL,$db,'','',False,False); /*dont trap errors handled below*/

	if (DB_error_no($db) !=0) {
		$title = _('Aged Customer Account Analysis') . ' - ' . _('Problem Report') . '.... ';
		include('includes/header.inc');
		echo '<P>' . _('The customer details could not be retrieved by the SQL because') . ' ' . DB_error_msg($db);
		echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
		if ($debug==1){
			echo "<BR>$SQL";
		}
		include('includes/footer.inc');
		exit;
	}

	$CurrTotBal=0;
	$CurrTotDue=0;
	$CurrTotCurr=0;
	$CurrTotOD1=0;
	$CurrTotOD2=0;
	$CurrTotOD3 = 0;
	$showcurrtotals=false;

	$doc[$ii] = array(_('And Trading in').' '.$_POST['Currency']);
	$ii++;

	While ($AgedAnalysis = DB_fetch_array($CustomerResult,$db)){

		// bowikaxu realhost - april 2008 - show div line if new CURRENCY
		if($AgedAnalysis['currabrev']!=$_POST['Currency']){

				$doc[$ii] = array('____________________');
				$ii++;
				// bowikaxu doc
				$doc[$ii] = array('','',$CurrTotBal,$CurrTotDue,$CurrTotCurr,$CurrTotOD1,$CurrTotOD2,$CurrTotOD3);
				$ii++;

				$_POST['Currency']=$AgedAnalysis['currabrev'];
				$doc[$ii] = array(_('And Trading in').' '.$AgedAnalysis['currabrev']);
				$ii++;
				$doc[$ii] = array(_('Customer'),_('Days'),_('Balance'),_('Current'),$rhDue,$rhDays1,$rhDays2,$rhDays3);
				//$doc[$ii] = array(_('Customer'),_('Balance'),_('Current'),_('Due Now'),'> ' . $_SESSION['PastDueDays1'] . ' ' . _('Days Over'),'> ' . $_SESSION['PastDueDays2'] . ' ' . _('Days Over'));
				$ii++;

				$CurrTotBal=0;
				$CurrTotDue=0;
				$CurrTotCurr=0;
				$CurrTotOD1=0;
				$CurrTotOD2=0;
				$CurrTotOD3=0;
				$showcurrtotals=true;
		}

		$DisplayDue = ($AgedAnalysis['due']-$AgedAnalysis['overdue1']);
		$DisplayCurrent = ($AgedAnalysis['balance']-$AgedAnalysis['due']);
		$DisplayBalance = ($AgedAnalysis['balance']);
		//$DisplayOverdue1 = ($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2'] - $AgedAnalysis['overdue3']);
		$DisplayOverdue1 = ($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2']);
		$DisplayOverdue2 = ($AgedAnalysis['overdue2'] - $AgedAnalysis['overdue3']);
		$DisplayOverdue3 = ($AgedAnalysis['overdue3']);

		$TotBal += $AgedAnalysis['balance'];
		$TotDue += ($AgedAnalysis['due']-$AgedAnalysis['overdue1']);
		$TotCurr += ($AgedAnalysis['balance']-$AgedAnalysis['due']);
		//$TotOD1 += ($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2'] - $AgedAnalysis['overdue3']);
		$TotOD1 += ($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2']);
		$TotOD2 += $AgedAnalysis['overdue2'] - $AgedAnalysis['overdue3'];
		$TotOD2 += $AgedAnalysis['overdue3'];

		$CurrTotBal += $AgedAnalysis['balance'];
		$CurrTotDue += ($AgedAnalysis['due']-$AgedAnalysis['overdue1']);
		$CurrTotCurr += ($AgedAnalysis['balance']-$AgedAnalysis['due']);
		//$CurrTotOD1 += ($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2']) - $AgedAnalysis['overdue3'];
		$CurrTotOD1 += ($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2']);
		$CurrTotOD2 += $AgedAnalysis['overdue2'] - $AgedAnalysis['overdue3'] ;
		$CurrTotOD3 += $AgedAnalysis['overdue3'] ;
		// bowikaxu doc
		$doc[$ii] = array($AgedAnalysis['debtorno'] . ' - ' . $AgedAnalysis['name'],' ',$DisplayBalance,$DisplayCurrent,$DisplayDue,$DisplayOverdue1,$DisplayOverdue2,$DisplayOverdue3);
		$ii++;

		if ($_POST['DetailedReport']=='Yes'){



			//SAINTS
			$sql = "SELECT debtortrans.consignment, systypes.typename,
                        systypes.typeid,
			   			debtortrans.transno,
			   			debtortrans.trandate,
			   			(
								CASE WHEN (paymentterms.daysbeforedue > 0 )
								THEN
										( CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate)) >= paymentterms.daysbeforedue
										  THEN
										  	 (   TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate ) - paymentterms.daysbeforedue   )
										  ELSE
										  0
										  END
										)
								ELSE
									0
								END
							)  as diasVencidos ,
			   			c.serie,
						c.folio,
				   		(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate as balance,
						(CASE WHEN (paymentterms.daysbeforedue > 0)
							THEN
		   						(CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate)) >= paymentterms.daysbeforedue
		   						then (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
		   						ELSE 0 END)
							ELSE
		   						(CASE WHEN TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= 0
		   						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
		   						ELSE 0 END)
						END) AS due,
						(CASE WHEN (paymentterms.daysbeforedue > 0)
		   					THEN
								(CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ") THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate ELSE 0 END)
		   					ELSE
								(CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ")
		   						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
		   						ELSE 0 END)
						END) AS overdue1,
						(CASE WHEN (paymentterms.daysbeforedue > 0)
		   					THEN
								(CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ")
		   						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
		   						ELSE 0 END)
		 					ELSE
								(CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= " . $_SESSION['PastDueDays2'] . ")
		   						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
		   						ELSE 0 END)
						END) AS overdue2,
							(CASE WHEN (paymentterms.daysbeforedue > 0)
								THEN
									(
										CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND

										TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays3'] . ")
										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
									 	ELSE 0 END
									)
								ELSE
									(
										CASE WHEN (

											TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= " . $_SESSION['PastDueDays3'] . "

										)

										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate

										ELSE 0 END)
							END) AS overdue3

				   FROM debtorsmaster,
		   				paymentterms,
						currencies,
		   				systypes,debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id
                         ".$rh_rutas_debtors."

				   WHERE systypes.typeid = debtortrans.type
				   		AND currencies.currabrev = debtorsmaster.currcode
		   				AND debtorsmaster.paymentterms = paymentterms.termsindicator
		   				AND debtorsmaster.debtorno = debtortrans.debtorno
 				   		AND debtortrans.debtorno = '" . $AgedAnalysis['debtorno'] . "'
		   				AND ABS(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)>=".$rh_umbral_asignacion."$soloFact";

			$DetailResult = DB_query($sql,$db,'','',False,False); /*Dont trap errors */
			if (DB_error_no($db) !=0) {
				$title = _('Aged Customer Account Analysis') . ' - ' . _('Problem Report') . '....';
				include('includes/header.inc');
				echo '<BR><BR>' . _('The details of outstanding transactions for customer') . ' - ' . $AgedAnalysis['debtorno'] . ' ' . _('could not be retrieved because') . ' - ' . DB_error_msg($db);
				echo "<BR><A HREF='$rootpath/index.php'>" . _('Back to the menu') . '</A>';
				if ($debug==1){
					echo '<BR>' . _('The SQL that failed was') . '<P>' . $sql;
				}
				include('includes/footer.inc');
				exit;
			}

			$doc[$ii] = array('_______________');
			$ii++;
			while ($DetailTrans = DB_fetch_array($DetailResult)){

				if($DetailTrans['typeid']==10){
					$sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations
		    		WHERE rh_invoicesreference.intinvoice = ".$DetailTrans['transno']."
		    		AND locations.loccode = rh_invoicesreference.loccode";
					$res = DB_query($sql,$db);
					$ExtInvoice = DB_fetch_array($res);

					//SAINTS
					 $sql2="SELECT rh_invoicesreference.extinvoice, locations.rh_serie, c.serie, c.folio
            FROM rh_invoicesreference INNER JOIN rh_cfd__cfd c ON rh_invoicesreference.intinvoice=c.fk_transno, locations
            WHERE rh_invoicesreference.intinvoice = ".$DetailTrans['transno']." AND locations.loccode = rh_invoicesreference.loccode";

            //SAINTS
            $res2 = DB_query($sql2,$db);
            $ExtInvoice2 = DB_fetch_array($res2);

					//$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,60,$FontSize,$DetailTrans['typename'],'left');
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,60,$FontSize,$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].' ('.$DetailTrans['transno'].')','left');
			//SAINTS
			if($ExtInvoice2['serie']!="")
				$InvoiceNum = $ExtInvoice2['serie'].$ExtInvoice2['folio'].' ('.$DetailTrans['transno'].')';
			else
				$InvoiceNum = $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].' ('.$DetailTrans['transno'].')';

                                }else if ($DetailTrans['typeid']==11){
                                        $sql = "SELECT extcn FROM rh_crednotesreference
                                        WHERE intcn = ".$DetailTrans['transno']."";
                                        $res = DB_query($sql,$db);
                                        $ExtNC = DB_fetch_array($res);

                                        //SAINTS
                                        if($DetailTrans['folio']!="")
											$InvoiceNum = $DetailTrans['serie'].$DetailTrans['folio'].' ('.$DetailTrans['transno'].')';
                                        else
											$InvoiceNum = $ExtNC['extcn'].' ('.$DetailTrans['transno'].')';

                                }else if ($DetailTrans['typeid']==20001){
                                        $InvoiceNum = $DetailTrans['consignment'].' ('.$DetailTrans['transno'].')';
				}else{
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,60,$FontSize,$DetailTrans['typename'],'left');
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,60,$FontSize,$DetailTrans['transno'],'left');
					// bowikaxu doc
					$InvoiceNum = $DetailTrans['transno'];

				}
				$DisplayTranDate = ConvertSQLDate($DetailTrans['trandate']);
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+125,$YPos,75,$FontSize,$DisplayTranDate,'left');

				$DisplayDue = number_format($DetailTrans['due']-$DetailTrans['overdue1'],2);
				$DisplayCurrent = number_format($DetailTrans['balance']-$DetailTrans['due'],2);
				$DisplayBalance = number_format($DetailTrans['balance'],2);
				//$DisplayOverdue1 = number_format($DetailTrans['overdue1']-$DetailTrans['overdue2'] - $DetailTrans['overdue3'],2);
				$DisplayOverdue1 = number_format($DetailTrans['overdue1']-$DetailTrans['overdue2'],2);
				$DisplayOverdue2 = number_format($DetailTrans['overdue2'] - $DetailTrans['overdue3'],2);
				$DisplayOverdue3 = number_format($DetailTrans['overdue3'],2);
				/*
				$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayBalance,'right');
				$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayCurrent,'right');
				$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayDue,'right');
				$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayOverdue1,'right');
				$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayOverdue2,'right');
				*/
				// bowikaxu doc
				$doc[$ii] = array($DetailTrans['typename'].' '.$InvoiceNum.' '.$DisplayTranDate,$DetailTrans['diasVencidos'],$DisplayBalance,$DisplayCurrent,$DisplayDue,$DisplayOverdue1,$DisplayOverdue2,$DisplayOverdue3);
				$ii++;

			} /*end while there are detail transactions to show */
			$doc[$ii] = array('_______________');
			$ii++;
		} /*Its a detailed report */
	} /*end customer aged analysis while loop */


	// bowikaxu doc
	if($showcurrtotals){
		$doc[$ii] = array('','',$CurrTotBal,$CurrTotDue,$CurrTotCurr,$CurrTotOD1,$CurrTotOD2,$CurrTotOD3);
		$ii++;
	}

	$doc[$ii] = array('____________________');
	$ii++;
	$doc[$ii] = array('','',$TotBal,$TotDue,$TotCurr,$TotOD1,$TotOD2,$TotOD3);
	$ii++;

	$xls->addArray ( $doc );
	$xls->generateXML ("AgedDebtors");
	exit;
}

// PDF -----------------------
If (isset($_POST['PrintPDF'])
	AND isset($_POST['FromCriteria'])
	AND strlen($_POST['FromCriteria'])>=1
	AND isset($_POST['ToCriteria'])
	AND strlen($_POST['ToCriteria'])>=1){

	include('includes/PDFStarter.php');

	$FontSize=12;
	$pdf->addinfo('Title',_('Aged Customer Balance Listing'));
	$pdf->addinfo('Subject',_('Aged Customer Balances'));

	$PageNumber=0;
	$line_height=12;

      /*Now figure out the aged analysis for the customer range under review */
	if (trim($_POST['Salesman'])!=''){
		$SalesLimit = " and debtorsmaster.debtorno in (SELECT DISTINCT debtorno FROM custbranch where salesman = '".$_POST['Salesman']."') ";
	} else {
		$SalesLimit = "";
	}

	if ($_POST['All_Or_Overdues']=='All'){
		$SQL = "SELECT debtortrans.consignment, debtorsmaster.debtorno,
				debtorsmaster.name,
				currencies.currency,
				currencies.currabrev,
				currencies.rate,
				paymentterms.terms,
				debtorsmaster.creditlimit,
				holdreasons.dissallowinvoices,
				holdreasons.reasondescription,
				SUM(
					(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
				) as balance,
				SUM(
					CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate)) >= paymentterms.daysbeforedue
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
						ELSE 0 END
					ELSE
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') .")) >= 0
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate ELSE 0 END
					END
				) AS due,
				Sum(
					CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate)) > paymentterms.daysbeforedue AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ")
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate ELSE 0 END
					ELSE
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL ('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . "
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
						ELSE 0 END
					END
				) AS overdue1,
				Sum(
					CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate)) > paymentterms.daysbeforedue AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ")
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate ELSE 0 END
					ELSE
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL ('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays2'] . "
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
						ELSE 0 END
					END
				) AS overdue2,
							Sum(CASE WHEN (paymentterms.daysbeforedue > 0)
								THEN
									(
										CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND

										TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays3'] . ")
										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
									 	ELSE 0 END
									)
								ELSE
									(
										CASE WHEN (

											TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= " . $_SESSION['PastDueDays3'] . "

										)

										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate

										ELSE 0 END)
							END) AS overdue3
				FROM debtorsmaster,
					paymentterms,
					holdreasons,
					currencies,
					debtortrans
                     ".$rh_rutas_debtors."
				WHERE debtorsmaster.paymentterms = paymentterms.termsindicator
					AND debtorsmaster.currcode = currencies.currabrev
					AND debtorsmaster.holdreason = holdreasons.reasoncode
					AND debtorsmaster.debtorno = debtortrans.debtorno
					AND debtorsmaster.debtorno >= '" . $_POST['FromCriteria'] . "'
					AND debtorsmaster.debtorno <= '" . $_POST['ToCriteria'] . "'
					$soloFact

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
					ABS(Sum(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)) >= ".$rh_umbral_asignacion."";


	} elseif ($_POST['All_Or_Overdues']=='OverduesOnly') {

	      $SQL = "SELECT debtortrans.consignment, debtorsmaster.debtorno,
	      		debtorsmaster.name,
	      		currencies.currency,
				currencies.currabrev,
				currencies.rate,
	      		paymentterms.terms,
			debtorsmaster.creditlimit,
	      		holdreasons.dissallowinvoices,
	      		holdreasons.reasondescription,
			SUM(
	      			(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      		) AS balance,
			SUM(
	      			CASE WHEN (paymentterms.daysbeforedue > 0)
	      				THEN
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= paymentterms.daysbeforedue
	      					THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      					ELSE 0 END
	      				ELSE
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= 0 )
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate ELSE 0 END
					END
	      		) AS due,
			SUM(
		      		CASE WHEN (paymentterms.daysbeforedue > 0)
	      				THEN
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ")
	      					THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      					ELSE 0 END
	      				ELSE
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ")
	      					THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      					ELSE 0 END
					END
	      		) AS overdue1,
			SUM(
		      		CASE WHEN (paymentterms.daysbeforedue > 0)
	      				THEN
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ")
	      					THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      					ELSE 0 END
	      				ELSE
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays2'] . ")
	      					THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      					ELSE 0 END
					END
	      		) AS overdue2 ,
							Sum(CASE WHEN (paymentterms.daysbeforedue > 0)
								THEN
									(
										CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND

										TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays3'] . ")
										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
									 	ELSE 0 END
									)
								ELSE
									(
										CASE WHEN (

											TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= " . $_SESSION['PastDueDays3'] . "
										)

										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate

										ELSE 0 END)
							END) AS overdue3
			FROM debtorsmaster,
	      			paymentterms,
	      			holdreasons,
	      			currencies,
	      			debtortrans
                     ".$rh_rutas_debtors."
	      		WHERE debtorsmaster.paymentterms = paymentterms.termsindicator
	      		AND debtorsmaster.currcode = currencies.currabrev
	      		AND debtorsmaster.holdreason = holdreasons.reasoncode
	      		AND debtorsmaster.debtorno = debtortrans.debtorno
				AND debtorsmaster.debtorno >= '" . $_POST['FromCriteria'] . "'
	      		AND debtorsmaster.debtorno <= '" . $_POST['ToCriteria'] . "'
	      		$soloFact

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
			HAVING ABS(Sum(
				CASE WHEN (paymentterms.daysbeforedue > 0)
	      				THEN
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ")
	      					THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      					ELSE 0 END
	      				ELSE
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ")
	      					THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
	      					ELSE 0 END
					END
	      			)) >= ".$rh_umbral_asignacion;

	} elseif ($_POST['All_Or_Overdues']=='HeldOnly'){

		$SQL = "SELECT debtortrans.consignment, debtorsmaster.debtorno,
					debtorsmaster.name,
					currencies.currency,
					currencies.currabrev,
					currencies.rate,
					paymentterms.terms,
					debtorsmaster.creditlimit,
					holdreasons.dissallowinvoices,
					holdreasons.reasondescription,
			SUM(debtortrans.ovamount +
				debtortrans.ovgst +
				debtortrans.ovfreight +
				debtortrans.ovdiscount -
				debtortrans.alloc) AS balance,
			SUM(
				CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= paymentterms.daysbeforedue
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
						ELSE 0 END
					ELSE
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate," . INTERVAL('1', 'MONTH') . ")," .  INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= 0)
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
						ELSE 0 END
				END
			) AS due,
			SUM(
				CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue
						AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ")
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate ELSE 0 END
					ELSE
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ")
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
					ELSE 0 END
				END
			) AS overdue1,
			SUM(
				CASE WHEN (paymentterms.daysbeforedue > 0)
					THEN
						CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue
						AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ")
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
						ELSE 0 END
					ELSE
						CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= ".$_SESSION['PastDueDays2'] . ")
						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
					ELSE 0 END
				END
			) AS overdue2,
							Sum(CASE WHEN (paymentterms.daysbeforedue > 0)
								THEN
									(
										CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND

										TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays3'] . ")
										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
									 	ELSE 0 END
									)
								ELSE
									(
										CASE WHEN (

											TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= " . $_SESSION['PastDueDays3'] . "

										)

										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate

										ELSE 0 END)
							END) AS overdue3
		FROM debtorsmaster,
		paymentterms,
		holdreasons,
		currencies,
		debtortrans
         ".$rh_rutas_debtors."
		WHERE debtorsmaster.paymentterms = paymentterms.termsindicator
		AND debtorsmaster.currcode = currencies.currabrev
		AND debtorsmaster.holdreason = holdreasons.reasoncode
		AND debtorsmaster.debtorno = debtortrans.debtorno
		AND holdreasons.dissallowinvoices=1
		AND debtorsmaster.debtorno >= '" . $_POST['FromCriteria'] . "'
		AND debtorsmaster.debtorno <= '" . $_POST['ToCriteria'] . "'
		$soloFact

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
			ABS(Sum(
				debtortrans.ovamount +
				debtortrans.ovgst +
				debtortrans.ovfreight +
				debtortrans.ovdiscount -
				debtortrans.alloc
			)) >= ".$rh_umbral_asignacion."";
	}

		// bowikaxu - order by
		if($_POST['OrderBy']=='Code'){
			$SQL .= " ORDER BY currencies.currabrev DESC ,debtortrans.debtorno";
		}else {
			$SQL .= " ORDER BY currencies.currabrev DESC, debtorsmaster.name";
		}
		// AND debtorsmaster.currcode ='" . $_POST['Currency'] . "'

	$CustomerResult = DB_query($SQL,$db,'','',False,False); /*dont trap errors handled below*/

	if (DB_error_no($db) !=0) {
		$title = _('Aged Customer Account Analysis') . ' - ' . _('Problem Report') . '.... ';
		include('includes/header.inc');
		echo '<P>' . _('The customer details could not be retrieved by the SQL because') . ' ' . DB_error_msg($db);
		echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
		if ($debug==1){
			echo "<BR>$SQL";
		}
		include('includes/footer.inc');
		exit;
	}

	//include ('includes/PDFAgedDebtorsPageHeader.inc');

	$TotBal=0;
	$TotCurr=0;
	$TotDue=0;
	$TotOD1=0;
	$TotOD2=0;
	$TotOD3=0;

	// bowikaxu realhost - April 2008 - show all currencies, separated by a total
	$CurrTotBal=0;
	$CurrTotDue=0;
	$CurrTotCurr=0;
	$CurrTotOD1=0;
	$CurrTotOD2=0;
	$CurrTotOD3=0;
	$showcurrtotals=false;
	$first = true;

	While ($AgedAnalysis = DB_fetch_array($CustomerResult,$db)){
		if ($YPos < $Bottom_Margin + $line_height + 5){
		      include('includes/PDFAgedDebtorsPageHeader.inc');
		}
		/*if($first){

			$first = false;
			$DisplayDue = number_format($AgedAnalysis['due']-$AgedAnalysis['overdue1'],2);
			$DisplayCurrent = number_format($AgedAnalysis['balance']-$AgedAnalysis['due'],2);
			$DisplayBalance = number_format($AgedAnalysis['balance'],2);
			$DisplayOverdue1 = number_format($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2'],2);
			$DisplayOverdue2 = number_format($AgedAnalysis['overdue2'],2);
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,220-$Left_Margin,$FontSize,$AgedAnalysis['debtorno'] . ' - ' . $AgedAnalysis['name'],'left');
			$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayBalance,'right');
			$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayCurrent,'right');
			$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayDue,'right');
			$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayOverdue1,'right');
			$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayOverdue2,'right');

		}*/

		// bowikaxu realhost - april 2008 - show div line if new CURRENCY
		if($AgedAnalysis['currabrev']!=$_POST['Currency']){
				$pdf->line($Page_Width-$Right_Margin, $YPos+10 ,220, $YPos+10);
				$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize+1,_('Total').' '.$_POST['Currency'],'left');
				$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,number_format($CurrTotBal,2),'right');
				$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,number_format($CurrTotCurr,2),'right');
				$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,number_format($CurrTotDue,2),'right');
				$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,number_format($CurrTotOD1,2),'right');
				$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,number_format($CurrTotOD2,2),'right');
				$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,number_format($CurrTotOD3,2),'right');
				$YPos =$YPos - (2*$line_height);

				$_POST['Currency']=$AgedAnalysis['currabrev'];
				$pdf->addText($Left_Margin, $YPos,10, _('And Trading in').' '.$AgedAnalysis['currabrev']);
				$YPos =$YPos - (1.5*$line_height);
				/*Draw a rectangle to put the headings in     */
				$pdf->line($Page_Width-$Right_Margin, $YPos-5,$Left_Margin, $YPos-5);
				$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Left_Margin, $YPos+$line_height);
				$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos-5);
				$pdf->line($Left_Margin, $YPos+$line_height,$Left_Margin, $YPos-5);

				/*set up the headings */
				$Xpos = $Left_Margin+1;

				$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,220 - $Left_Margin,$FontSize,_('Customer'),'centre');
				$LeftOvers = $pdf->addTextWrap ( 210, $YPos, 20, $FontSize, _ ( 'Days' ), 'centre' );
				$LeftOvers = $pdf->addTextWrap ( 230, $YPos, 60, $FontSize, _ ( 'Balance' ), 'centre' );
				//$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,_('Balance'),'centre');
				$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,_('Current'),'centre');
				$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$rhDue,'centre');
				$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$rhDays1,'centre');
				$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$rhDays2,'centre');
				$LeftOvers = $pdf->addTextWrap(520,$YPos,60,$FontSize,$rhDays3,'centre');
				$YPos =$YPos - (2*$line_height);

				$CurrTotBal=0;
				$CurrTotDue=0;
				$CurrTotCurr=0;
				$CurrTotOD1=0;
				$CurrTotOD2=0;
				$CurrTotOD3=0;
				$showcurrtotals=true;
		}

		$DisplayDue = number_format($AgedAnalysis['due']-$AgedAnalysis['overdue1'],2);
		$DisplayCurrent = number_format($AgedAnalysis['balance']-$AgedAnalysis['due'],2);
		$DisplayBalance = number_format($AgedAnalysis['balance'],2);
		$DisplayOverdue1 = number_format($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2'],2);
		$DisplayOverdue2 = number_format($AgedAnalysis['overdue2']- $AgedAnalysis['overdue3'],2);
		$DisplayOverdue3 = number_format($AgedAnalysis['overdue3'],2);

		$TotBal += $AgedAnalysis['balance'];
		$TotDue += ($AgedAnalysis['due']-$AgedAnalysis['overdue1']);
		$TotCurr += ($AgedAnalysis['balance']-$AgedAnalysis['due']);
		//$TotOD1 += ($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2'] - $AgedAnalysis['overdue3']);
		$TotOD1 += ($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2']);
		$TotOD2 += $AgedAnalysis['overdue2'] - $AgedAnalysis['overdue3'];
		$TotOD3 += $AgedAnalysis['overdue3'];

		// bowikaxu realhost - April 2008 - current currency totals
		$CurrTotBal += $AgedAnalysis['balance'];
		$CurrTotDue += ($AgedAnalysis['due']-$AgedAnalysis['overdue1']);
		$CurrTotCurr += ($AgedAnalysis['balance']-$AgedAnalysis['due']);
		//$CurrTotOD1 += ($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2'] - $AgedAnalysis['overdue3']);
		$CurrTotOD1 += ($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2']);
		$CurrTotOD2 += $AgedAnalysis['overdue2'] - $AgedAnalysis['overdue3'];
		$CurrTotOD3 += $AgedAnalysis['overdue3'];

		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,220-$Left_Margin,$FontSize,$AgedAnalysis['debtorno'] . ' - ' . $AgedAnalysis['name'],'left');

		$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayBalance,'right');
		$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayCurrent,'right');
		$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayDue,'right');
		$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayOverdue1,'right');
		$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayOverdue2,'right');
		$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayOverdue3,'right');

		$YPos -=$line_height;


		if ($_POST['DetailedReport']=='Yes'){

		   /*draw a line under the customer aged analysis*/
		   $pdf->line($Page_Width-$Right_Margin, $YPos+10,$Left_Margin, $YPos+10);

				$sql = "SELECT debtortrans.consignment, systypes.typename,
						systypes.typeid,
			   			debtortrans.transno,
			   			debtortrans.trandate,
			   			(
								CASE WHEN (paymentterms.daysbeforedue > 0 )
								THEN
										( CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate)) >= paymentterms.daysbeforedue
										  THEN
										  	 (   TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate ) - paymentterms.daysbeforedue   )
										  ELSE
										  0
										  END
										)
								ELSE
									0
								END
							)  as diasVencidos ,
				   		((debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate) as balance,
						(CASE WHEN (paymentterms.daysbeforedue > 0)
							THEN
		   						(CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate)) >= paymentterms.daysbeforedue
		   						then (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
		   						ELSE 0 END)
							ELSE
		   						(CASE WHEN TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= 0
		   						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
		   						ELSE 0 END)
						END) AS due,
						(CASE WHEN (paymentterms.daysbeforedue > 0)
		   					THEN
								(CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ") THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate ELSE 0 END)
		   					ELSE
								(CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ")
		   						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
		   						ELSE 0 END)
						END) AS overdue1,
						(CASE WHEN (paymentterms.daysbeforedue > 0)
		   					THEN
								(CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ")
		   						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
		   						ELSE 0 END)
		 					ELSE
								(CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= " . $_SESSION['PastDueDays2'] . ")
		   						THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
		   						ELSE 0 END)
						END) AS overdue2 ,
							(CASE WHEN (paymentterms.daysbeforedue > 0)
								THEN
									(
										CASE WHEN TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND

										TO_DAYS(NOW()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays3'] . ")
										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate
									 	ELSE 0 END
									)
								ELSE
									(
										CASE WHEN (

											TO_DAYS(NOW()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= " . $_SESSION['PastDueDays3'] . "

										)

										THEN (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate

										ELSE 0 END)
							END) AS overdue3
				   FROM debtorsmaster,
		   				paymentterms,
						currencies,
                        systypes,
		   				debtortrans
                         ".$rh_rutas_debtors."
				   WHERE systypes.typeid = debtortrans.type
				   		AND currencies.currabrev = debtorsmaster.currcode
		   				AND debtorsmaster.paymentterms = paymentterms.termsindicator
		   				AND debtorsmaster.debtorno = debtortrans.debtorno
				   		AND debtortrans.debtorno = '" . $AgedAnalysis['debtorno'] . "'
		   				AND ABS(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)>=".$rh_umbral_asignacion."$soloFact";


		    $DetailResult = DB_query($sql,$db,'','',False,False); /*Dont trap errors */
		    if (DB_error_no($db) !=0) {
			$title = _('Aged Customer Account Analysis') . ' - ' . _('Problem Report') . '....';
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

				if($DetailTrans['typeid']==10){//SAINTS
                                    /*$sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations
                                    WHERE rh_invoicesreference.intinvoice = ".$DetailTrans['transno']."
                                    AND locations.loccode = rh_invoicesreference.loccode";
                                    $res = DB_query($sql,$db);
                                    $ExtInvoice = DB_fetch_array($res);*/

                                     $sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations
            WHERE rh_invoicesreference.intinvoice = ".$DetailTrans['transno']."
            AND locations.loccode = rh_invoicesreference.loccode";

            $sql2="SELECT rh_invoicesreference.extinvoice, locations.rh_serie, c.serie, c.folio
            FROM rh_invoicesreference INNER JOIN rh_cfd__cfd c ON rh_invoicesreference.intinvoice=c.fk_transno, locations
            WHERE rh_invoicesreference.intinvoice = ".$DetailTrans['transno']." AND locations.loccode = rh_invoicesreference.loccode";

            $res = DB_query($sql,$db);
            $ExtInvoice = DB_fetch_array($res);
            $res2 = DB_query($sql2,$db);
            $ExtInvoice2 = DB_fetch_array($res2);

                                    $LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,60,$FontSize,$DetailTrans['typename'],'left');

                     if($ExtInvoice2['serie']!="")
                         $LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,60,$FontSize,$ExtInvoice2['serie'].$ExtInvoice2['folio']."(".$DetailTrans['transno'].")",'left');
                         //$LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,60,$FontSize,$ExtInvoice2['serie'].$ExtInvoice2['folio']."(".$DetailTrans['transno'].")",'left');
                     else
						 $LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,60,$FontSize,$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice']."(".$DetailTrans['transno'].")",'left');
						 //$LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,60,$FontSize,$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice']."(".$DetailTrans['transno'].")",'left');

				}else if ($DetailTrans['typeid']==11){
                                    $sql = "SELECT extcn FROM rh_crednotesreference
                                    WHERE intcn = ".$DetailTrans['transno']."";
                                    $res = DB_query($sql,$db);
                                    $ExtNC = DB_fetch_array($res);

                                    $LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,60,$FontSize,$DetailTrans['typename'],'left');

                                //SAINTS
                                if($DetailTrans['folio']!="")
                                    $LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,60,$FontSize,$DetailTrans['serie'].$DetailTrans['folio']." (".$DetailTrans['transno'].")",'left');
                                else
									$LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,60,$FontSize,$ExtNC['extcn']." (".$DetailTrans['transno'].")",'left');

                                }else if ($DetailTrans['typeid']==20001){
                                    $LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,60,$FontSize,$DetailTrans['typename'],'left');
                                    $LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,60,$FontSize,$DetailTrans['consignment']." (".$DetailTrans['transno'].")",'left');
				}else{
                                    $LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,60,$FontSize,$DetailTrans['typename'],'left');
                                    $LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,60,$FontSize,$DetailTrans['transno'],'left');
				}


			    $DisplayTranDate = ConvertSQLDate($DetailTrans['trandate']);
			    $LeftOvers = $pdf->addTextWrap($Left_Margin+125,$YPos,75,$FontSize,$DisplayTranDate,'left');

			    $DisplayDue = number_format($DetailTrans['due']-$DetailTrans['overdue1'],2);
			    $DisplayCurrent = number_format($DetailTrans['balance']-$DetailTrans['due'],2);
			    $DisplayBalance = number_format($DetailTrans['balance'],2);
			    //$DisplayOverdue1 = number_format($DetailTrans['overdue1']-$DetailTrans['overdue2'] - $DetailTrans['overdue3'],2);
			    $DisplayOverdue1 = number_format($DetailTrans['overdue1']-$DetailTrans['overdue2'],2);
			    $DisplayOverdue2 = number_format($DetailTrans['overdue2'] - $DetailTrans['overdue3'],2);
			    $DisplayOverdue3 = number_format($DetailTrans['overdue3'],2);

				$LeftOvers = $pdf->addTextWrap ( 210, $YPos, 20, $FontSize, $DetailTrans['diasVencidos'], 'centre' );
			    $LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayBalance,'right');
			    $LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayCurrent,'right');
			    $LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayDue,'right');
			    $LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayOverdue1,'right');
			    $LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayOverdue2,'right');
			      $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayOverdue3,'right');

			    $YPos -=$line_height;
			    if ($YPos < $Bottom_Margin + $line_height){
					$PageNumber++;
					include('includes/PDFAgedDebtorsPageHeader.inc');
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
		include('includes/PDFAgedDebtorsPageHeader.inc');
	} elseif ($_POST['DetailedReport']=='Yes') {
		//dont do a line if the totals have to go on a new page
		$pdf->line($Page_Width-$Right_Margin, $YPos+10 ,220, $YPos+10);
	}

	// bowikaxu realhost april 2008 -  show last currency total
	if($showcurrtotals){
		$pdf->line($Page_Width-$Right_Margin, $YPos+10 ,220, $YPos+10);
		//$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize+1,_('Total').' '.$_POST['Currency'],'left');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize+1,_('Total'),'left');
		$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,number_format($CurrTotBal,2),'right');
		$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,number_format($CurrTotCurr,2),'right');
		$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,number_format($CurrTotDue,2),'right');
		$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,number_format($CurrTotOD1,2),'right');
		$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,number_format($CurrTotOD2,2),'right');
		$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,number_format($CurrTotOD3,2),'right');

		$YPos =$YPos - (2*$line_height);
	}

	$DisplayTotBalance = number_format($TotBal,2);
	$DisplayTotDue = number_format($TotDue,2);
	$DisplayTotCurrent = number_format($TotCurr,2);
	$DisplayTotOverdue1 = number_format($TotOD1,2);
	$DisplayTotOverdue2 = number_format($TotOD2,2);
	$DisplayTotOverdue3 = number_format($TotOD3,2);

	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize+2,_('Total').' '._('Final'),'left');
	$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayTotBalance,'right');
	$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayTotCurrent,'right');
	$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayTotDue,'right');
	$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayTotOverdue1,'right');
	$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayTotOverdue2,'right');
	$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayTotOverdue3,'right');

	$buf = $pdf->output();
	$len = strlen($buf);

	if ($len < 1000) {
		$title = _('Aged Customer Account Analysis') . ' - ' . _('Problem Report') . '....';
		include('includes/header.inc');
		prnMsg(_('There are no customers meeting the critiera specified to list'),'info');
		if ($debug==1){
			prnMsg($SQL,'info');
		}
		echo "<BR><A HREF='$rootpath/index.php'>" . _('Back to the menu') . '</A>';
		include('includes/footer.inc');
		exit;
	}

	header('Content-type: application/pdf');
	header("Content-Length: $len");
	header('Content-Disposition: inline; filename=AgedDebtors.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	$pdf->stream();

} else { /*The option to print PDF was not hit */

	$title=_('Aged Debtor Analysis');
	include('includes/header.inc');
 ?>
 <script src="jscalendar/src/js/jscal2.js"></script>
<script src="jscalendar/src/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css"
	href="jscalendar/src/css/jscal2.css" />
<link rel="stylesheet" type="text/css"
	href="jscalendar/src/css/border-radius.css" />
<link rel="stylesheet" type="text/css"
	href="jscalendar/src/css/steel/steel.css" />
 <?php
	if ((!isset($_POST['FromCriteria']) || !isset($_POST['ToCriteria']))) {

	/*if $FromCriteria is not set then show a form to allow input	*/

        //iJPe
        /*
         * realhost
         * 2010-02-11
         *
         * Modificacion para que el usuario pueda ingresar a este archivo desde Seleccionar Cliente
         */

                if (isset($_GET['Customer'])){
                    $valueFrom = $_GET['Customer'];
                    $valueTo = $_GET['Customer'];
                }else{
                    $valueFrom = '0';
                    $valueTo = 'zzzzzz';
                }

		echo '<FORM ACTION=' . $_SERVER['PHP_SELF'] . " METHOD='POST'><CENTER><TABLE>";

		echo '<TR><TD>' . _('From Customer Code') . ':' . "</FONT></TD><TD><input tabindex='1' Type=text maxlength=6 size=7 name=FromCriteria value='".$valueFrom."'></TD></TR>";
		echo '<TR><TD>' . _('To Customer Code') . ':' . "</TD><TD><input tabindex='2' Type=text maxlength=6 size=7 name=ToCriteria value='".$valueTo."'></TD></TR>";

	echo '<TR><TD>'._('Ruta de Cobranza').':</TD>';
	echo '<TD><SELECT tabindex=19 name="CollectionPath">';

	DB_data_seek($result,0);

	$sql = 'SELECT id,codigo, descripcion FROM rh_rutas';
	$result = DB_query($sql,$db);
 		echo '<OPTION SELECTED=SELECTED value="%" > Todas las rutas';

	while ($myrow = DB_fetch_array($result)) {
	    if((strlen($_POST['CollectionPath'])>0)&&($_POST['CollectionPath']==$myrow['id'])){
		    echo "<OPTION Value='" . $myrow['id'] . "' selected=selected >" . $myrow['descripcion'];
        }else{
            echo "<OPTION Value='" .$myrow['id'] . "' >" . $myrow['descripcion'];
        }
	}
 	echo '</SELECT></TD></TR>';

		// bowikaxu realhost january 2008 - from to date
		/*
		echo '<TR><TD>' . _('From Trans. Date') . ":</FONT></TD>
			<TD><input Type=text maxlength=10 size=10 name=FromDate value='".$_POST['FromDate']."'></TD>
		</TR>";
		echo '<TR><TD>' . _('To Trans. Date') . ":</TD>
			<TD><input Type=text maxlength=10 size=10 name=ToDate value='".$_POST['ToDate']."'></TD>
		</TR>";
		*/
		echo '<TR><TD>' . _('All balances or overdues only') . ':' . "</TD><TD><SELECT name='All_Or_Overdues'>";
		echo "<OPTION SELECTED Value='All'>" . _('All customers with balances');
		echo "<OPTION Value='OverduesOnly'>" . _('Overdue accounts only');
		echo "<OPTION Value='HeldOnly'>" . _('Held accounts only');
		echo '</SELECT></TD></TR>';
/* echo '<tr>
		<td >Fecha inicial:</td>
		<td ><input type="text" name="fecha_ini" id="fecha_ini"
			style="width: 50%" value="'.$_POST ['fecha_ini'].'" /></td>
	    </tr> ';
    	echo '<tr>
		<td>Fecha Final:</td>
		<td><input type="text" name="fecha_fin" id="fecha_fin"
			style="width: 50%" value="'.$_POST ['fecha_fin'].'" /></td>
	    </tr>';       */
		echo '<TR><TD>' . _('Only Show Customers Of') . ':' . "</TD><TD><SELECT tabindex='4' name='Salesman'>";

		$sql = 'SELECT salesmancode, salesmanname FROM salesman';

		$result=DB_query($sql,$db);
		echo "<OPTION Value=''>Todos</OPTION>";
		while ($myrow=DB_fetch_array($result)){
			   if((strlen($_POST['Salesman'])>0)&&($_POST['Salesman']==$myrow['salesmancode'])){
					echo "<OPTION Value='" . $myrow['salesmancode'] . "' selected=selected >" . $myrow['salesmanname'];
               }else{
                   echo "<OPTION Value='" . $myrow['salesmancode'] . "'>" . $myrow['salesmanname'];
               }
		}
		echo '</SELECT></TD></TR>';

/*
		echo '<TR><TD>' . _('Only show customers trading in') . ':' . "</TD><TD><SELECT tabindex='5' name='Currency'>";

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
*/

		echo '<TR><TD>' . _('Summary or detailed report') . ':' . "</TD>
			<TD><SELECT tabindex='6' name='DetailedReport'>";
		echo "<OPTION SELECTED Value='No'>" . _('Summary Report');
		echo "<OPTION Value='Yes'>" . _('Detailed Report');
		echo '</SELECT></TD></TR>';

		// bowikaxu august 07
		echo '<TR><TD>' . _('Ordenar Por') . ':' . "</TD>
			<TD><SELECT name='OrderBy'>";
		echo "<OPTION SELECTED Value='Code'>" . _('Customer Code');
		echo "<OPTION Value='Name'>" . _('Customer Name');
		echo '</SELECT></TD></TR>';

		//iJPe realhost 2009-12-21
		echo '<TR><TD>' . _('Solo Facturas').": <br>Nota: Incluyen facturas<br>posiblemente pagadas no asignadas". "</TD>";
		echo "<TD><INPUT type = 'CHECKBOX' name = 'soloFact'></TD>";

		// bowikaxu realhost january 2008 - excel report
                /*
		echo "</TABLE><INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') , "'>
		<INPUT TYPE=Submit Name='Excel' Value='" . _('Excel') , "'>
		<INPUT TYPE=hidden name='Currency' value='".$rowcurrency['currencydefault']."'>
		<INPUT TYPE=submit onclick='this.form.action=\"AgedDebtorshtml.php\"' Name='VerReporte' Value='" . _('Ver') , "' >
		</CENTER>";
                 * */


                echo "</TABLE><INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') , "'>
		<INPUT TYPE=hidden name='Currency' value='".$rowcurrency['currencydefault']."'>
		<INPUT TYPE=submit onclick='this.form.action=\"AgedDebtorshtml.php\"' Name='VerReporte' Value='" . _('Ver') , "' >
		</CENTER>";

	}
/*echo "<script type=\"text/javascript\">//<![CDATA[
      var cal2 = Calendar.setup({
          onSelect: function(cal2) { cal2.hide() },
          showTime: false
      });

      var cal = Calendar.setup({
          onSelect: function(cal) { cal.hide() },
          showTime: false
      });
      cal.setLanguage('es');
      cal.manageFields(\"fecha_ini\", \"fecha_ini\", \"%Y-%m-%d\");

      cal2.setLanguage('es');
      cal2.manageFields(\"fecha_fin\", \"fecha_fin\", \"%Y-%m-%d\");
    //]]>
</script>";   */
	include('includes/footer.inc');

} /*end of else not PrintPDF */


?>