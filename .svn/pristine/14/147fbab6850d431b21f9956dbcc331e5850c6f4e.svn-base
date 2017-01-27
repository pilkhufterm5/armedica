<?php
if(!isset($External)){
    $PageSecurity = 2;
    include_once('includes/session.inc');
    $title = _('AgedDebtorshtml');
    include_once('includes/header.inc');
}
include_once('XMLFacturacionElectronica/utils/Php.php');
if (!isset($_SESSION['PastDueDays3']))
$_SESSION['PastDueDays3'] = 90;
if(!isset($External)){
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
}else ob_start();
/* $Revision: 385 $ */
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-08-08 12:46:27 -0500 (Fri, 08 Aug 2008) $
 * $Rev: 385 $
 */
// bowikaxu - inital dates

if(isset($External)){
    $rh_umbral_asignacion = 2;
}
if(!isset($_POST['FromDate'])){
	$_POST['FromDate']=Date($_SESSION["DefaultDateFormat"]);
}

//iJPe
if (isset($_POST['soloFact'])){
    $soloFact = " AND debtortrans.type = 10 ";
}else{
    $soloFact = "";
}

if(isset($_POST['CollectionPath'])&&($_POST['CollectionPath']!='%')){
    $rh_rutas_debtors= "join rh_rutas_debtors on rh_rutas_debtors.debtorno=debtortrans.debtorno and rh_rutas_debtors.branchcode =debtortrans.branchcode and rh_rutas_debtors.idrutas='".$_POST['CollectionPath']."'";
}else{
   $rh_rutas_debtors= "";
}

if(!isset($_POST['ToDate'])){
	$_POST['ToDate']=Date($_SESSION["DefaultDateFormat"]);
}

		echo '<FORM ACTION=' . $_SERVER['PHP_SELF'] . " METHOD='POST' onsubmit='valida();'><CENTER><TABLE>";
		echo '<TR><TD>' . _('From Customer Code') . ':' . "</FONT></TD><TD><input tabindex='1' Type=text maxlength=6 size=7 name=FromCriteria value='0'></TD></TR>";
		echo '<TR><TD>' . _('To Customer Code') . ':' . "</TD><TD><input tabindex='2' Type=text maxlength=6 size=7 name=ToCriteria value='zzzzzz'></TD></TR>";
	   echo '<TR><TD>'._('Ruta de Cobranza').':</TD>';
	   echo '<TD><SELECT tabindex=19 name="CollectionPath">';

	//DB_data_seek($result,0);

	$sql = 'SELECT id,codigo, descripcion FROM rh_rutas';
	$result = DB_query($sql,$db);
 		echo '<OPTION SELECTED=SELECTED value="%" > Todas las rutas';

	while ($myrow = DB_fetch_array($result)) {
	    if((strlen($_POST['CollectionPath'])>0)&&($_POST['CollectionPath']==$myrow['id'])){
		    echo "<OPTION Value='" . $myrow['id'] . "' selected=selected >" . $myrow['descripcion'];
        }else{
            echo "<OPTION Value='" .$myrow['id'] ."' >" . $myrow['descripcion'];
        }
	} //end while loop
		echo '</SELECT></TD></TR>';

		echo '<TR><TD>' . _('All balances or overdues only') . ':' . "</TD><TD><SELECT name='All_Or_Overdues'>";
		echo "<OPTION SELECTED Value='All'>" . _('All customers with balances');
		echo "<OPTION Value='OverduesOnly'>" . _('Overdue accounts only');
		echo "<OPTION Value='HeldOnly'>" . _('Held accounts only');
		echo '</SELECT></TD></TR>';
/*echo '<tr>
		<td >Fecha inicial:</td>
		<td ><input type="text" name="fecha_ini" id="fecha_ini"
			style="width: 50%" value="'.$_POST ['fecha_ini'].'" /></td>
	    </tr> ';
    	echo '<tr>
		<td>Fecha Final:</td>
		<td><input type="text" name="fecha_fin" id="fecha_fin"
			style="width: 50%" value="'.$_POST ['fecha_fin'].'" /></td>
	    </tr>';  */

		echo '<TR><TD>' . _('Only Show Customers Of') . ':' . "</TD><TD><SELECT tabindex='4' name='Salesman'>";
		//$rh_ususql = "SELECT salesmancode, salesmanname FROM salesman WHERE rh_usuario = '".$_SESSION['UserID']."'";
		//$rh_result=DB_query($rh_ususql,$db);
		//if (DB_num_rows($rh_result) == 0) {
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
		//}else{
		//	while($rh_usurow = DB_fetch_array($rh_result)){
		//		echo "<OPTION Value='" . $rh_usurow[0] . "'>" . $rh_usurow[1];
		//	}
		//}

		echo '</SELECT></TD></TR>';

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
		echo "</TABLE><INPUT TYPE=submit onclick='this.form.action=\"AgedDebtors.php\"' Name='PrintPDF' Value='" . _('Print PDF') , "'>
		<INPUT TYPE=submit onclick='this.form.action=\"AgedDebtors.php\"' Name='Excel' Value='" . _('Excel') , "'>
		<INPUT TYPE=hidden name='Currency' value='".$_POST['Currency']."'>
		<INPUT TYPE=submit onclick='this.form.action=\"AgedDebtorshtml.php\"' Name='VerReporte' Value='" . _('Ver') , "' >
		</CENTER>";
		echo '<br><br>';
if(isset($External))
    ob_end_clean();
	If (isset($_POST['VerReporte'])
	AND isset($_POST['FromCriteria'])
	AND strlen($_POST['FromCriteria'])>=1
	AND isset($_POST['ToCriteria'])
	AND strlen($_POST['ToCriteria'])>=1){

	/*Now figure out the aged analysis for the customer range under review */
		if (trim($_POST['Salesman'])!=''){
			$SalesLimit = " and debtorsmaster.debtorno in (SELECT DISTINCT debtorno FROM custbranch where salesman = '".$_POST['Salesman']."') ";
		} else {
			$SalesLimit = "";
		}
	/****************************************************************************************************************************/
		//$rh_ususql = "SELECT salesmancode, salesmanname FROM salesman WHERE rh_usuario = '".$_SESSION['UserID']."'";
//                $rh_ususql = "SELECT salesmancode, salesmanname FROM salesman";
//		$rh_result = DB_query($rh_ususql,$db);
//		if (DB_num_rows($rh_result) == 0) {
//			$WhereBranch = "";
//		}else{
//			$rh_usurow = DB_fetch_array($rh_result);
//			$rh_first = 1;
//			$sql4 = "SELECT branchcode FROM custbranch WHERE salesman = '".$rh_usurow['salesmancode']."'";
//			$res4 = DB_query($sql4,$db);
//			while($branches4 = DB_fetch_array($res4)){
//				if($rh_first == 1){
//					$WhereBranch = "AND debtortrans.branchcode IN ('".$branches4[0]."'";
//				}else{
//					$WhereBranch .= ",'".$branches4[0]."'";
//				}
//			}
//			$WhereBranch .= ")";
//		}
/****************************************************************************************************************************/
		if($_POST['All_Or_Overdues']=='All'){
			$SQL = "SELECT debtortrans.consignment,debtorsmaster.debtorno,
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
						$SalesLimit
					".$soloFact."
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
						ABS(Sum(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)) > ".$rh_umbral_asignacion."";


		} elseif ($_POST['All_Or_Overdues']=='OverduesOnly') {
		$SQL = "SELECT debtortrans.consignment,debtorsmaster.debtorno,
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
					$SalesLimit
				".$soloFact."
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
					)) > ".$rh_umbral_asignacion;

		} elseif ($_POST['All_Or_Overdues']=='HeldOnly'){

			$SQL = "SELECT debtortrans.consignment,debtorsmaster.debtorno,
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

			$SalesLimit
			".$soloFact."
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
				)) > ".$rh_umbral_asignacion."";
		}

		// bowikaxu - order by
		if($_POST['OrderBy']=='Code'){
			$SQL .= " ORDER BY currencies.currabrev DESC ,debtortrans.debtorno";
		}else {
			$SQL .= " ORDER BY currencies.currabrev DESC, debtorsmaster.name";
		}
		// AND debtorsmaster.currcode ='" . $_POST['Currency'] . "'

		$CustomerResult = DB_query($SQL,$db,'','',False,False); /*dont trap errors handled below*/
         //echo $SQL;
		if (DB_error_no($db) !=0) {
			$title = _('Aged Customer Account Analysis') . ' - ' . _('Problem Report') . '.... ';
			//include('includes/header.inc');
			echo '<P>' . _('The customer details could not be retrieved by the SQL because') . ' ' . DB_error_msg($db);
			echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
			if ($debug==1){
				//echo "<BR>$SQL";
			}
			//include('includes/footer.inc');
			if(isset($External))
			 return;
			exit;
		}

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


					$HeadingLine1 = _('Aged Customer Balances For Customers from') . ' ' . $_POST['FromCriteria'] . ' ' .  _('to') . ' ' . $_POST['ToCriteria'];

					$HeadingLine2 = _('And Trading in') . ' ' . $_POST['Currency'];

					if (trim($_POST['Salesman'])!=''){
						$SQL = 'SELECT salesmanname FROM salesman WHERE salesmancode="'.$_POST['Salesman'].'"';
						$rs = DB_query($SQL,$db,'','',False,False);
						$row = DB_fetch_array($rs,$db);
						$HeadingLine3 = _('And Has at Least 1 Branch Serviced By Sales Person #'). ' '. $_POST['Salesman'] . ' - ' . $row['salesmanname'];
						$numHeads++;
					}

					echo '<CENTER><TABLE BORDER=2 CELLPADDING=3><TR>';

					echo '<TD colspan=5>'.$_SESSION['CompanyRecord']['coyname'].' </TD><TD colspan=4>'._('Printed') . ': ' . Date("d M Y") . '</TD></TR>';
					echo '<TR><TD colspan=5>'.$HeadingLine1.'</TD><TD colspan=4>'._('Entre parentesis factura interna').'</TD></TR>';
					echo '<TR><TD colspan=9>'.$HeadingLine2.'</TD></TR>';
					if ($HeadingLine3 != ''){
					echo '<TR><TD colspan=7>'.$HeadingLine3.'</TD></TR>';
					}

					$rhCustomer = _('Customer');
					$rhBalance = _('Balance');
					$rhCurrent = _('Current');
					$rhDue =' ' . 1 . ' ' ._('To') .' '.  29 .  " "._('Days');
					$rhDays1 = ' ' . 31 . ' ' . _('To') . '  '  . 60 . " "._('Days');
					$rhDays2 = ' ' . 61 . ' ' . _('To') . '  '  . 90 . " " ._('Days');
					$rhDays3 = ' ' . 91 . ' ' . _('Days');
					//titles report
					echo '<TR  class=tableheader><TD>'.$rhCustomer.'</TD><TD>'.$rhBalance.'</TD><TD>'.$rhCurrent.'</TD><TD>'.$rhDue.'</TD><TD>'.$rhDays1.'</TD><TD>'.$rhDays2.'</TD><TD> '.$rhDays3.'</TD> <TD> </TD><TD></TD></TR>';

		While ($AgedAnalysis = DB_fetch_array($CustomerResult,$db)){


					$_SESSION['CompanyRecord']['coyname'];

			// bowikaxu realhost - april 2008 - show div line if new CURRENCY
			if($AgedAnalysis['currabrev']==$_POST['Currency']){

					//echo '<TR><TD ALIGN=RIGHT >'._('Total').' '.$_POST['Currency'].'</TD><TD ALIGN=RIGHT >'.number_format($CurrTotBal,2).'</TD><TD ALIGN=RIGHT >'.number_format($CurrTotCurr,2).'</TD><TD ALIGN=RIGHT >'.number_format($CurrTotDue,2).'</TD><TD ALIGN=RIGHT >'.number_format($CurrTotOD1,2).'</TD><TD ALIGN=RIGHT >'.number_format($CurrTotOD2,2).'</TD></TR>';
					//echo '<TR><TD colspan=6></TD></TR>';

					//$_POST['Currency']=$AgedAnalysis['currabrev'];
					//$rhcurrabrev = _('And Trading in').' '.$AgedAnalysis['currabrev'];

					//echo '<TR  class=tableheader><TD>'.$rhCustomer.'</TD><TD>'.$rhBalance.'</TD><TD>'.$rhCurrent.'</TD><TD>'.$rhDue.'</TD><TD>'.$rhDays1.'</TD><TD>'.$rhDays2.'</TD></TR>';

					//echo '<TR><TD colspan=6>'.$rhcurrabrev.'</TD><TR>';

					$rh_Customer = _('Customer');
					$rh_Balance = _('Balance');
					$rh_Current = _('Current');
					$rh_due = _('Due Now');
					$rh_PastDueDays1 = '> ' . $_SESSION['PastDueDays1'] . ' ' . _('Days Over');
					$rh_PastDueDays2 = '> ' . $_SESSION['PastDueDays2'] . ' ' . _('Days Over');

					$CurrTotBal=0;
					$CurrTotDue=0;
					$CurrTotCurr=0;
					$CurrTotOD1=0;
					$CurrTotOD2=0;
					$CurrTotOD3=0;
					//$showcurrtotals=true;
				}


			$DisplayDue = number_format($AgedAnalysis['due']-$AgedAnalysis['overdue1'],2);
			$DisplayCurrent = number_format($AgedAnalysis['balance']-$AgedAnalysis['due'],2);
			$DisplayBalance = number_format($AgedAnalysis['balance'],2);
			//$DisplayOverdue1 = number_format($AgedAnalysis['overdue1'] - $AgedAnalysis['overdue2'] - $AgedAnalysis['overdue3'],2);
			$DisplayOverdue1 = number_format($AgedAnalysis['overdue1'] - $AgedAnalysis['overdue2'],2);
			$DisplayOverdue2 = number_format($AgedAnalysis['overdue2'] - $AgedAnalysis['overdue3'] ,2);
			$DisplayOverdue3 = number_format($AgedAnalysis['overdue3'] ,2);

			$TotBal += $AgedAnalysis['balance'];
			$TotDue += ($AgedAnalysis['due']-$AgedAnalysis['overdue1']);
			$TotCurr += ($AgedAnalysis['balance']-$AgedAnalysis['due']);
			//$TotOD1 += ($AgedAnalysis['overdue1'] - $AgedAnalysis['overdue2'] - $AgedAnalysis['overdue3'] );
			$TotOD1 += ($AgedAnalysis['overdue1'] - $AgedAnalysis['overdue2'] );
			$TotOD2 +=  $AgedAnalysis['overdue2'] - $AgedAnalysis['overdue3']   ;
			$TotOD3 +=  $AgedAnalysis['overdue3']  ;

			// bowikaxu realhost - April 2008 - current currency totals
			$CurrTotBal += $AgedAnalysis['balance'];
			$CurrTotDue += ($AgedAnalysis['due']-$AgedAnalysis['overdue1']);
			$CurrTotCurr += ($AgedAnalysis['balance']-$AgedAnalysis['due']);
			//$CurrTotOD1 += ($AgedAnalysis['overdue1'] - $AgedAnalysis['overdue2']  - $AgedAnalysis['overdue3']);
			$CurrTotOD1 += ($AgedAnalysis['overdue1'] - $AgedAnalysis['overdue2']);
			$CurrTotOD2 += ( $AgedAnalysis['overdue2'] - $AgedAnalysis['overdue3']  );
			$CurrTotOD3 += ($AgedAnalysis['overdue3']);
			if ($k==1){
				echo "<tr bgcolor='#CCCCCC'>";
				$k=0;
			} else {
				echo "<tr bgcolor='#EEEEEE'>";
				$k=1;
			}
			echo '<TD>'.$AgedAnalysis['debtorno']. ' - ' . $AgedAnalysis['name'].'</TD><TD ALIGN=RIGHT >'.$DisplayBalance.'</TD><TD ALIGN=RIGHT >'.$DisplayCurrent.'</TD><TD ALIGN=RIGHT >'.$DisplayDue.'</TD><TD ALIGN=RIGHT >'.$DisplayOverdue1.'</TD><TD ALIGN=RIGHT >'.$DisplayOverdue2.'</TD> <td ALIGN=RIGHT> '.$DisplayOverdue3.' </td></TR>';



			if ($_POST['DetailedReport']=='Yes'){

			/*draw a line under the customer aged analysis*/
			// $pdf->line($Page_Width-$Right_Margin, $YPos+10,$Left_Margin, $YPos+10);
				//SAINTS
					$sql = "SELECT DISTINCT debtortrans.consignment, systypes.typename,
							systypes.typeid,
							debtortrans.transno,
                            debtortrans.id,
							c.serie,
							c.folio,
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
					FROM    debtorsmaster,							paymentterms,
							currencies,
							systypes,debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id
                     ".((strlen($rh_rutas_debtors)==0)?'':$rh_rutas_debtors)."
 					WHERE systypes.typeid = debtortrans.type
							AND currencies.currabrev = debtorsmaster.currcode
							AND debtorsmaster.paymentterms = paymentterms.termsindicator
							AND debtorsmaster.debtorno = debtortrans.debtorno
							AND debtortrans.debtorno = '" . $AgedAnalysis['debtorno'] . "'
							AND ABS(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)>".$rh_umbral_asignacion."
							".$soloFact."";

			$DetailResult = DB_query($sql,$db,'','',False,False); /*Dont trap errors */
			if (DB_error_no($db) !=0) {
				$title = _('Aged Customer Account Analysis') . ' - ' . _('Problem Report') . '....';
				//include('includes/header.inc');
				echo '<BR><BR>' . _('The details of outstanding transactions for customer') . ' - ' . $AgedAnalysis['debtorno'] . ' ' . _('could not be retrieved because') . ' - ' . DB_error_msg($db);
				echo "<BR><A HREF='$rootpath/index.php'>" . _('Back to the menu') . '</A>';
				if ($debug==1){
					echo '<BR>' . _('The SQL that failed was') . '<P>' . $sql;
				}
				//include('includes/footer.inc');
				if(isset($External))
				    return;
				exit;
			}
			$k=0;
			while ($DetailTrans = DB_fetch_array($DetailResult)){

				if($myrow['type']==20000 && $myrow['rh_status']=='C'){

						echo "<tr bgcolor='#ea6060'>";

					}else if (($myrow['type']==10 || $myrow['type']==11) && $myrow['rh_status']=='C'){

						echo "<tr bgcolor='#ea6060'>";

					}else if ($myrow['type']==11 && $myrow['rh_status']=='R'){ // nota de credito cancela remision

						echo "<tr bgcolor='#f3cb85'>";

					}else if ($myrow['type']==11 && $myrow['rh_status']=='F'){ // nota de credito cancela factura

						echo "<tr bgcolor='#e4f369'>";

					}else {

						if ($k==1){
							echo "<tr bgcolor='#CCCCCC'>";
							$k=0;
						} else {
							echo "<tr bgcolor='#EEEEEE'>";
							$k=1;
						}

					}

					$DisplayTranDate = ConvertSQLDate($DetailTrans['trandate']);
					$DisplayDue = number_format($DetailTrans['due']-$DetailTrans['overdue1'],2);
					$DisplayCurrent = number_format($DetailTrans['balance']-$DetailTrans['due'],2);
					$DisplayBalance = number_format($DetailTrans['balance'],2);
					//$DisplayOverdue1 = number_format($DetailTrans['overdue1'] - $DetailTrans['overdue2'] - $DetailTrans['overdue3'] ,2);
					$DisplayOverdue1 = number_format($DetailTrans['overdue1'] - $DetailTrans['overdue2'] ,2);
					$DisplayOverdue2 = number_format( $DetailTrans['overdue2']- $DetailTrans['overdue3']    ,2);
					$DisplayOverdue3 = number_format($DetailTrans['overdue3']   ,2);

					if($DetailTrans['typeid']==10){//SAINTS
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

                            if(isset($External)){
                                $AfilTemplate = "afil=true&";
                            }
                             if($ExtInvoice2['serie']!="")
                                echo '<TD> <table style="background-color:transparent">
                                        <tr>
                                            <td> '.$DetailTrans['typename'].' </td>
                                            <td> '.$ExtInvoice2['serie'].' </td>
                                            <td> '.$ExtInvoice2['folio'].'('.$DetailTrans['transno'].')</td>
                                            <td> '.$DisplayTranDate.' </td>
                                            <td>  Dias Vencidos - '.$DetailTrans['diasVencidos'] .'</td>
                                        <tr>
                                        </table>
                                      </TD>
                                      <TD ALIGN=RIGHT >'.$DisplayBalance.'</TD>
                                      <TD ALIGN=RIGHT >'.$DisplayCurrent.'</TD>
                                      <TD ALIGN=RIGHT >'.$DisplayDue.'</TD>
                                      <TD ALIGN=RIGHT >'.$DisplayOverdue1.'</TD>
                                      <TD ALIGN=RIGHT >'.$DisplayOverdue2.'</TD>
                                      <td ALIGN=RIGHT > '.$DisplayOverdue3.' </td>
                                      '.($ExtInvoice2['folio']?"
                                      <td><a target='_blank' href='$rootpath/PHPJasperXML/sample1.php?{$AfilTemplate}isTransportista=" . $myrow['is_transportista'] . "&transno=" . $DetailTrans['transno'] . '&' . ($myrow['is_carta_porte']?'isCartaPorte=true':'') . ($myrow['rh_status']=='C'?'&isCfdCancelado=true':'') . "'><IMG SRC='$rootpath/css/silverwolf/images/pdf.gif' TITLE='" . _('Click to preview the invoice') . _(' (PDF)') . "'></a></td>":"<td></td>")."<td><a target='_blank' href='".$rootpath."/rh_PrintCustTrans.php?idDebtortrans=".$DetailTrans['id']."&FromTransNo=".$DetailTrans['transno']."&InvOrCredit=Invoice&isCfd=" . $myrow['is_cfd'] . "'><IMG SRC='$rootpath/css/silverwolf/images/preview.gif' TITLE='" . _('Click to preview the invoice') . "'></a></td></TR>";
							 else
                                echo '<TD><table style="background-color:transparent">
                                    <tr>
                                        <td> '.$DetailTrans['typename'].' </td>
                                        <td> '.$ExtInvoice['rh_serie']."-".$ExtInvoice['extinvoice'].' </td>
                                        <td> ('.$DetailTrans['transno'].')</td>
                                        <td> '.$DisplayTranDate.' </td>
                                        <td>  Dias Vencidos - '.$DetailTrans['diasVencidos'] .'</td>
                                    <tr>
                                </table>
                                </TD>
                                <TD ALIGN=RIGHT >'.$DisplayBalance.'</TD>
                                <TD ALIGN=RIGHT >'.$DisplayCurrent.'</TD>
                                <TD ALIGN=RIGHT >'.$DisplayDue.'</TD>
                                <TD ALIGN=RIGHT >'.$DisplayOverdue1.'</TD>
                                <TD ALIGN=RIGHT >'.$DisplayOverdue2.'</TD>
                                <td ALIGN=RIGHT >  '.$DisplayOverdue3.'</td>'.($ExtInvoice2['folio']?"<td>
                                <a target='_blank' href='$rootpath/PHPJasperXML/sample1.php?{$AfilTemplate}isTransportista=" . $myrow['is_transportista'] . "&transno=" . $DetailTrans['transno'] . '&' . ($myrow['is_carta_porte']?'isCartaPorte=true':'') . ($myrow['rh_status']=='C'?'&isCfdCancelado=true':'') . "'><IMG SRC='$rootpath/css/silverwolf/images/pdf.gif' TITLE='" . _('Click to preview the invoice') . _(' (PDF)') . "'></a></td>":"<td></td>").'<TD></TD></TR>';

					}else if($DetailTrans['typeid']==11){
                                            $sql = "SELECT extcn FROM rh_crednotesreference
                                            WHERE intcn = ".$DetailTrans['transno']."";
                                            $res = DB_query($sql,$db);
                                            $ExtCN = DB_fetch_array($res);
                                       //SAINTS
                                       if($DetailTrans['folio']!="")

                                            echo '<TD><table style="background-color:transparent"> <tr> <td> '.$DetailTrans['typename'].' </td><td> '.$DetailTrans['serie']."-".$DetailTrans['folio'].' </td>  <td> '.$DetailTrans['transno'].'</td> <td> '.$DisplayTranDate.' </td>  <td>  Dias Vencidos - '.$DetailTrans['diasVencidos'] .'</td>     <tr> </table></TD><TD ALIGN=RIGHT >'.$DisplayBalance.'</TD><TD ALIGN=RIGHT >'.$DisplayCurrent.'</TD><TD ALIGN=RIGHT >'.$DisplayDue.'</TD><TD ALIGN=RIGHT >'.$DisplayOverdue1.'</TD><TD ALIGN=RIGHT >'.$DisplayOverdue2.'</TD><td ALIGN=RIGHT >  '.$DisplayOverdue3.'</td><TD></TD><TD></TD></TR>';
                                       //SAINTS
                                       else
											echo '<TD><table style="background-color:transparent"> <tr> <td> '.$DetailTrans['typename'].' </td><td> '.$ExtCN['extcn'].' </td>  <td> '.$DetailTrans['transno'].'</td> <td> '.$DisplayTranDate.' </td>  <td>  Dias Vencidos - '.$DetailTrans['diasVencidos'] .'</td>     <tr> </table></TD><TD ALIGN=RIGHT >'.$DisplayBalance.' </TD><TD ALIGN=RIGHT >'.$DisplayCurrent.'</TD><TD ALIGN=RIGHT >'.$DisplayDue.'</TD><TD ALIGN=RIGHT >'.$DisplayOverdue1.'</TD><TD ALIGN=RIGHT >'.$DisplayOverdue2.'</TD><td ALIGN=RIGHT >  '.$DisplayOverdue3.'</td><TD></TD><TD></TD></TR>';


					}else if ($DetailTrans['typeid']==20001){

                                            echo '<TD><table style="background-color:transparent" > <tr> <td> '.$DetailTrans['typename'].' </td><td> '.$DetailTrans['consignment'].' </td>  <td> '.$DetailTrans['transno'].'</td> <td> '.$DisplayTranDate.' </td>  <td>  Dias Vencidos -  '.$DetailTrans['diasVencidos'] .'</td>     <tr> </table></TD><TD ALIGN=RIGHT >'.$DisplayBalance.'</TD><TD ALIGN=RIGHT >'.$DisplayCurrent.'</TD><TD ALIGN=RIGHT >'.$DisplayDue.'</TD><TD ALIGN=RIGHT >'.$DisplayOverdue1.'</TD><TD ALIGN=RIGHT >'.$DisplayOverdue2.'</TD><td ALIGN=RIGHT >  '.$DisplayOverdue3.'</td></TR>';
					}else{
                                            echo '<TD><table style="background-color:transparent" > <tr> <td> '.$DetailTrans['typename'].' </td><td>  </td>  <td> '.$DetailTrans['transno'].'</td> <td> '.$DisplayTranDate.' </td>  <td> Dias Vencidos -  '.$DetailTrans['diasVencidos'] .'</td>     <tr> </table></TD><TD ALIGN=RIGHT >'.$DisplayBalance.'</TD><TD ALIGN=RIGHT >'.$DisplayCurrent.'</TD><TD ALIGN=RIGHT >'.$DisplayDue.'</TD><TD ALIGN=RIGHT >'.$DisplayOverdue1.'</TD><TD ALIGN=RIGHT >'.$DisplayOverdue2.'</TD><td ALIGN=RIGHT >  '.$DisplayOverdue3.'</td></TR>';
                                        }
			} /*end while there are detail transactions to show */
			} /*Its a detailed report */
		} /*end customer aged analysis while loop */

		// bowikaxu realhost april 2008 -  show last currency total
		if($showcurrtotals){
			echo '<TR ALIGN=RIGHT ><TD>'._('Total').' '.$_POST['Currency'].'</TD><TD>'.number_format($CurrTotBal,2).'</TD><TD>'.number_format($CurrTotCurr,2).'</TD><TD>'.number_format($CurrTotDue,2).'</TD><TD>'.number_format($CurrTotOD1,2).'</TD><TD>'.number_format($CurrTotOD2,2).'</TD> <td ALIGN=RIGHT > '.number_format($CurrTotOD3,2).' </td> </TR>';
		echo '<TR><TD colspan=6></TD></TR>';

		}

		$DisplayTotBalance = number_format($TotBal,2);
		$DisplayTotDue = number_format($TotDue,2);
		$DisplayTotCurrent = number_format($TotCurr,2);
		$DisplayTotOverdue1 = number_format($TotOD1,2);
		$DisplayTotOverdue2 = number_format($TotOD2,2);
		$DisplayTotOverdue3 = number_format($TotOD3,2);

		echo '<TR><TD colspan=6></TD></TR>';
		echo '<TR ALIGN=RIGHT><TD><b>'._('Total').' '._('Final').'</b></TD><TD><b>'.$DisplayTotBalance.'</b></TD><TD><b>'.$DisplayTotCurrent.'</b></TD><TD><b>'.$DisplayTotDue.'</b></TD><TD><b>'.$DisplayTotOverdue1.'</b></TD><TD><b>'.$DisplayTotOverdue2.'</b></TD> <td> '.$DisplayTotOverdue3.'</td></TR>';
	echo '</TABLE>';
	echo '</CENTER>';
	//echo 'xx'.$DisplayTotOverdue3.'xx';
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
</script>";  */
    if(!isset($External))
	   include('includes/footer.inc');
?>
