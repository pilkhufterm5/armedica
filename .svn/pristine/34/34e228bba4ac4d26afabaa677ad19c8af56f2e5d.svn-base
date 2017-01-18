<?php

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */

include('includes/SQL_CommonFunctions.inc');

$PageSecurity = 1;

include('includes/session.inc');
$title = _('Customer Inquiry');
include('includes/header.inc');

// always figure out the SQL required from the inputs available

if(!isset($_GET['CustomerID']) AND !isset($_SESSION['CustomerID'])){
	prnMsg(_('To display the enquiry a customer must first be selected from the customer selection screen'),'info');
	echo "<BR><CENTER><A HREF='". $rootpath . "/SelectCustomer.php?" . SID . "'>" . _('Select a Customer to Inquire On') . '</A><BR></CENTER>';
	include('includes/footer.inc');
	exit;
} else {
	if (isset($_GET['CustomerID'])){
		$_SESSION['CustomerID'] = $_GET['CustomerID'];
	}
	$CustomerID = $_SESSION['CustomerID'];
}
// 2007/02/08 bowikaxu - si se envio desde el reporte cliente y fecha

if(isset($_GET['FromDate'])){
	
	$_POST['TransAfterDate']=$_GET['FromDate'];
	
}

// bowikaxu realhost nov 2007
if(isset($_GET['ToDate'])){
	
	$_POST['TransBeforeDate']=$_GET['ToDate'];
	
}

if(isset($_GET['type'])){
	$_POST['type']=$_GET['type'];
}

if(isset($_GET['branch'])){
	$_POST['branch']=$_GET['branch'];
}

// fin bowikaxu

if (!isset($_POST['TransAfterDate'])) {
	$_POST['TransAfterDate'] = Date($_SESSION['DefaultDateFormat'],Mktime(0,0,0,Date('m')-6,Date('d'),Date('Y')));
}

// bowikaxu relahost nov 07
if (!isset($_POST['TransBeforeDate'])) {
	$_POST['TransBeforeDate'] = Date($_SESSION['DefaultDateFormat'],Mktime(0,0,0,Date('m'),Date('d'),Date('Y')));
}

if(!isset($_POST['type']) OR $_POST['type']=='all'){
	$SelType= "LIKE '%'";
}else {
	$SelType='= '.$_POST['type'];
}

if(!isset($_POST['branch']) OR $_POST['branch']=='all'){
	$SelBranch= "LIKE '%'";
}else {
	$SelBranch="= '".$_POST['branch']."' ";
}

$SQL = "SELECT debtorsmaster.name, 
		currencies.currency, 
		paymentterms.terms,
		debtorsmaster.creditlimit, 
		holdreasons.dissallowinvoices, 
		holdreasons.reasondescription,
		SUM(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount
- debtortrans.alloc) AS balance,
		SUM(CASE WHEN (paymentterms.daysbeforedue > 0) THEN
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate)) >= paymentterms.daysbeforedue
			THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
		ELSE
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= 0 THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END 
		END) AS due,
		SUM(CASE WHEN (paymentterms.daysbeforedue > 0) THEN
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue
			AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " .
		$_SESSION['PastDueDays1'] . ") 
			THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
		ELSE 
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ")
			THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount 
			- debtortrans.alloc ELSE 0 END
		END) AS overdue1,
		SUM(CASE WHEN (paymentterms.daysbeforedue > 0) THEN
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue
			AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ") THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
		ELSE
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1','MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= " . $_SESSION['PastDueDays2'] . ") THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
		END) AS overdue2
		FROM debtorsmaster,
     			paymentterms,
     			holdreasons,
     			currencies,
     			debtortrans
		WHERE  debtorsmaster.paymentterms = paymentterms.termsindicator
     		AND debtorsmaster.currcode = currencies.currabrev
     		AND debtorsmaster.holdreason = holdreasons.reasoncode
     		AND debtorsmaster.debtorno = '" . $CustomerID . "'
     		AND debtorsmaster.debtorno = debtortrans.debtorno
     		AND debtortrans.type ".$SelType."
     		AND debtortrans.branchcode ".$SelBranch."
		GROUP BY debtorsmaster.name,
			currencies.currency,
			paymentterms.terms,
			paymentterms.daysbeforedue,
			paymentterms.dayinfollowingmonth,
			debtorsmaster.creditlimit,
			holdreasons.dissallowinvoices,
			holdreasons.reasondescription";

$ErrMsg = _('The customer details could not be retrieved by the SQL because');
$CustomerResult = DB_query($SQL,$db,$ErrMsg);

if (DB_num_rows($CustomerResult)==0){

	/*Because there is no balance - so just retrieve the header information about the customer - the choice is do one query to get the balance and transactions for those customers who have a balance and two queries for those who don't have a balance OR always do two queries - I opted for the former */

	$NIL_BALANCE = True;

	$SQL = "SELECT debtorsmaster.name, currencies.currency, paymentterms.terms,
	debtorsmaster.creditlimit, holdreasons.dissallowinvoices, holdreasons.reasondescription
	FROM debtorsmaster,
	     paymentterms,
	     holdreasons,
	     currencies
	WHERE
	     debtorsmaster.paymentterms = paymentterms.termsindicator
	     AND debtorsmaster.currcode = currencies.currabrev
	     AND debtorsmaster.holdreason = holdreasons.reasoncode
	     AND debtorsmaster.debtorno = '" . $CustomerID . "'";

	$ErrMsg =_('The customer details could not be retrieved by the SQL because');
	$CustomerResult = DB_query($SQL,$db,$ErrMsg);

} else {
	$NIL_BALANCE = False;
}

$CustomerRecord = DB_fetch_array($CustomerResult);

if ($NIL_BALANCE==True){
	$CustomerRecord['balance']=0;
	$CustomerRecord['due']=0;
	$CustomerRecord['overdue1']=0;
	$CustomerRecord['overdue2']=0;
}

echo '<CENTER><FONT SIZE=4>' . $CustomerRecord['name'] . ' </FONT></B> - (' . _('All amounts stated in') . ' ' . $CustomerRecord['currency'] . ')</CENTER><BR><B><FONT COLOR=BLUE>' . _('Terms') . ': ' . $CustomerRecord['terms'] . '<BR>' . _('Credit Limit') . ': </B></FONT> ' . number_format($CustomerRecord['creditlimit'],0) . '  <B><FONT COLOR=BLUE>' . _('Credit Status') . ':</B></FONT> ' . $CustomerRecord['reasondescription'];

if ($CustomerRecord['dissallowinvoices']!=0){
	echo '<BR><FONT COLOR=RED SIZE=4><B>' . _('ACCOUNT ON HOLD') . '</FONT></B><BR>';
}

echo "<TABLE WIDTH=100% BORDER=1>
	<TR>
		<td class='tableheader'>" . _('Total Balance') . "</TD>
		<td class='tableheader'>" . _('Current') . "</TD>
		<td class='tableheader'>" . _('Now Due') . "</TD>
		<td class='tableheader'>" . $_SESSION['PastDueDays1'] . "-" . $_SESSION['PastDueDays2'] . ' ' . _('Days Overdue') . "</TD>
		<td class='tableheader'>" . _('Over') . ' ' . $_SESSION['PastDueDays2'] . ' ' . _('Days Overdue') . '</TD></TR>';

echo '<TR><TD ALIGN=RIGHT>' . number_format($CustomerRecord['balance'],2) . '</TD>
	<TD ALIGN=RIGHT>' . number_format(($CustomerRecord['balance'] - $CustomerRecord['due']),2) . '</TD>
	<TD ALIGN=RIGHT>' . number_format(($CustomerRecord['due']-$CustomerRecord['overdue1']),2) . '</TD>
	<TD ALIGN=RIGHT>' . number_format(($CustomerRecord['overdue1']-$CustomerRecord['overdue2']) ,2) . '</TD>
	<TD ALIGN=RIGHT>' . number_format($CustomerRecord['overdue2'],2) . '</TD>
	</TR>
	</TABLE>';

echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . "' METHOD=POST>";
echo _('Show all transactions after') . ": <INPUT type=text name='TransAfterDate' Value='" . $_POST['TransAfterDate'] . "' MAXLENGTH =10 SIZE=12>";
// bowikaxu realhost - nov 2007 - end date
echo _('Show all transactions before') . ": <INPUT type=text name='TransBeforeDate' Value='" . $_POST['TransBeforeDate'] . "' MAXLENGTH =10 SIZE=12>";

// bowikaxu realhost - select transaction type
echo "<SELECT NAME='type'><OPTION SELECTED VALUE='all'>"._('Show All')."</OPTION>";
$sql = "SELECT systypes.typeid, systypes.typename FROM systypes WHERE typeid IN(SELECT type FROM debtortrans GROUP BY type)";
$res = DB_query($sql,$db);
while($types = DB_fetch_array($res)){
	
	if($_POST['type']==$types['typeid']){
		echo "<OPTION VALUE=".$types['typeid']." SELECTED>".$types['typename']."</OPTION>";
	}else{
		echo "<OPTION VALUE=".$types['typeid'].">".$types['typename']."</OPTION>";
	}
}
echo "</SELECT>";

// bowikaxu realhost - branches filter
echo "<SELECT NAME='branch'><OPTION SELECTED VALUE='all'>"._('Show All')."</OPTION>";
$sql = "SELECT brname, branchcode FROM custbranch WHERE debtorno ='".$CustomerID."'";
$res = DB_query($sql,$db);
while($branches = DB_fetch_array($res)){
	
	if($_POST['branch']==$branches['branchcode']){
		echo "<OPTION VALUE=".$branches['branchcode']." SELECTED>".$branches['brname']."</OPTION>";
	}else{
		echo "<OPTION VALUE=".$branches['branchcode'].">".$branches['brname']."</OPTION>";
	}
}
echo "</SELECT>";

echo "<INPUT TYPE=SUBMIT NAME='Refresh Inquiry' VALUE='" . _('Refresh Inquiry') . "'></FORM>";

$DateAfterCriteria = FormatDateForSQL($_POST['TransAfterDate']);
// bowikaxu realhost nov 07
$DateBeforeCriteria = FormatDateForSQL($_POST['TransBeforeDate']);

//SAINTS
$SQL = "SELECT systypes.typename,
		debtortrans.id,
		debtortrans.type,
		debtortrans.transno,
		debtortrans.branchcode,
		debtortrans.trandate,
		debtortrans.reference,
		debtortrans.invtext,
		debtortrans.order_,
		debtortrans.rate,
		debtortrans.rh_status,
		 c.serie,
         c.folio,
		(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount) AS totalamount,
		debtortrans.alloc AS allocated,
        (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount-debtortrans.alloc) as saldo
	FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id,
		systypes
	WHERE debtortrans.type = systypes.typeid
	AND debtortrans.type ".$SelType."
	AND debtortrans.branchcode ".$SelBranch."
	AND debtortrans.debtorno = '" . $CustomerID . "'
	AND debtortrans.trandate >= '$DateAfterCriteria'
	ORDER BY debtortrans.id";

$ErrMsg = _('No transactions were returned by the SQL because');
$TransResult = DB_query($SQL,$db,$ErrMsg);

if (DB_num_rows($TransResult)==0){
	echo _('There are no transactions to display since') . ' ' . $_POST['TransAfterDate'];
	include('includes/footer.inc');
	exit;
}
/*show a table of the invoices returned by the SQL */

echo '<TABLE CELLPADDING=2 COLSPAN=7>';

$tableheader = "<TR BGCOLOR =#800000>
		<TD class='tableheader'>" . _('Type') . "</TD>
		<TD class='tableheader'>" . _('Number') . "</TD>
		<TD class='tableheader'>" . _('Date') . "</TD>
		<TD class='tableheader'>" . _('Branch') . "</TD>
		<TD class='tableheader'>" . _('Reference') . "</TD>
		<TD class='tableheader'>" . _('Comments') . "</TD>
		<TD class='tableheader'>" . _('Order') . "</TD>
		<TD class='tableheader'>" . _('Total') . "</TD>
        <TD class='tableheader'>" . _('Saldo') . "</TD>
		</TR>";

echo $tableheader;

$Total = 0;
$TotalAlloc = 0;
$j = 1;
$k=0; //row colour counter
while ($myrow=DB_fetch_array($TransResult)) {

	$Total += $myrow['totalamount'];
    $TotalSaldo += $myrow['saldo'];
	$TotalAlloc += $myrow['allocated'];
	
	// bowikaxu realhost - june 30 2007 - change color on cancelled transactions
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

	$FormatedTranDate = ConvertSQLDate($myrow['trandate']);

	$base_formatstr = "<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td width='200'>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
                <td ALIGN=RIGHT>%s</td>";
	if($myrow['rh_status']=='C'){ // factura cancelada
		$credit_invoice_str = "<td><a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the invoice') . "'></a>
					<IMG SRC='%s/cancel_disable.gif' TITLE='" . _('Factura Cancelada') . "'>
					</td>";
		
		$credit_invoice_str2 = "<td><a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the invoice') . "'></a>
					</td>";
		
		// remisiones
		// bowikaxu shipments links to preview and credit
	$credit_shipment_str = "<td><a href='%s/rh_Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the shipment') . "'></a>
	<IMG SRC='%s/cancel_disable.gif' TITLE='" . _('Click para cancelar remision') . "'>
	</td>";
	
	$credit_shipment_str2 = "<td><a href='%s/rh_Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the shipment') . "'></a>
	<IMG SRC='%s/cancel_disable.gif' TITLE='" . _('Click para cancelar remision') . "'>
	</td>";
	
	$preview_shipment_str = "<td><a target='_blank' href='%s/rh_PDFRemGde.php?&FromTransNo=%s&InvOrCredit=Invoice'><IMG SRC='%s/preview.gif' TITLE='" . _('Click to preview the shipment') . "'></a></td>
				<td><a target='_blank' href='%s/rh_EmailCustTrans.php?FromTransNo=%s&InvOrCredit=Invoice'><IMG SRC='%s/email.gif' TITLE='" . _('Click to email the shipment') . "'></a></td>";

		
		
	}else { // factura no cancelada
		$credit_invoice_str = "<td><a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the invoice') . "'></a>
					<a href='%s/rh_Cancel_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/cancel.gif' TITLE='" . _('Click para cancelar factura') . "'></a>
					</td>";
		
		$credit_invoice_str2 = "<td><a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the invoice') . "'></a>
					
					</td>";
		
		// remisiones
		// bowikaxu shipments links to preview and credit
	$credit_shipment_str = "<td><a href='%s/rh_Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the shipment') . "'></a>
	<a href='%s/rh_Cancel_Remision.php?InvoiceNumber=%s'><IMG SRC='%s/cancel.gif' TITLE='" . _('Click para cancelar remision') . "'></a>
	</td>";
	
	$credit_shipment_str2 = "<td><a href='%s/rh_Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the shipment') . "'></a>
	<IMG SRC='%s/cancel.gif' TITLE='" . _('Click para cancelar remision') . "'>
	</td>";
	
	$preview_shipment_str = "<td><a target='_blank' href='%s/rh_PDFRemGde.php?&FromTransNo=%s&InvOrCredit=Invoice'><IMG SRC='%s/preview.gif' TITLE='" . _('Click to preview the shipment') . "'></a></td>
				<td><a target='_blank' href='%s/rh_EmailCustTrans.php?FromTransNo=%s&InvOrCredit=Invoice'><IMG SRC='%s/email.gif' TITLE='" . _('Click to email the shipment') . "'></a></td>";

	}
	$preview_invoice_str = "<td><a target='_blank' href='%s/rh_PrintCustTrans.php?FromTransNo=%s&InvOrCredit=Invoice'><IMG SRC='%s/preview.gif' TITLE='" . _('Click to preview the invoice') . "'></a></td>
				<td><a target='_blank' href='%s/EmailCustTrans.php?FromTransNo=%s&InvOrCredit=Invoice'><IMG SRC='%s/email.gif' TITLE='" . _('Click to email the invoice') . "'></a></td>";
	$preview_credit_str = "<td><a target='_blank' href='%s/rh_PrintCustTrans.php?FromTransNo=%s&InvOrCredit=Credit'><IMG SRC='%s/preview.gif' TITLE='" . _('Click to preview the credit note') . "'></a></td>
				<td><a target='_blank' href='%s/EmailCustTrans.php?FromTransNo=%s&InvOrCredit=Credit'><IMG SRC='%s/email.gif' TITLE='" . _('Click to email the credit note') . "'></a></td>";

	// Sept 2006 RealHost
	if (in_array(5,$_SESSION['AllowedPageSecurityTokens']) && $myrow['type']==10){ /*Show a link to allow an invoice to be credited */

		// bowikaxu april 2007 - get external invoice number
			//SAINTS
			/*$sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations 
			WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
			AND locations.loccode = rh_invoicesreference.loccode";
		    $res = DB_query($sql,$db);
		    $ExtInvoice = DB_fetch_array($res);*/
		//SAINTS
		    $sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations
            WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
            AND locations.loccode = rh_invoicesreference.loccode";
                                            
            /*$sql2="SELECT rh_invoicesreference.extinvoice, locations.rh_serie, c.serie, c.folio 
            FROM rh_invoicesreference INNER JOIN rh_cfd__cfd c ON rh_invoicesreference.intinvoice=c.fk_transno, locations 
            WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']." AND locations.loccode = rh_invoicesreference.loccode";*/
                                            
            $res = DB_query($sql,$db);
            $ExtInvoice = DB_fetch_array($res);
            //$res2 = DB_query($sql2,$db);
            //$ExtInvoice2 = DB_fetch_array($res2);
            if($myrow['folio']!=""){
                $Datos['serie']=$myrow['serie'];
                $Datos['folio']=$myrow['folio'];
            }else{
                $Datos['serie']=$ExtInvoice['rh_serie'];
                $Datos['folio']=$ExtInvoice['extinvoice'];
            }
			
		    // si permitir cancelacion de factura
		if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND in_array(8,$_SESSION['AllowedPageSecurityTokens'])){
			
			if($myrow['rh_status']=='C'){ // cancelada
			//SAINTS
			if($myrow['serie']!=""){
				printf($base_formatstr ."
				</tr>",
				$myrow['typename'],
				//$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
				$Datos['serie'].$Datos['folio'].'('.$myrow['transno'].')',
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));}
			//SAINTS
			else{
				printf($base_formatstr ."
				</tr>",
				$myrow['typename'],
				$Datos['serie'].$Datos['folio'].'('.$myrow['transno'].')',
				//$myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));}
				
			}else {
			//SAINTS
			if($myrow['folio']!=""){
				printf($base_formatstr ."
				</tr>",
				$myrow['typename'],
				//$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
				$Datos['serie'].$Datos['folio'].'('.$myrow['transno'].')',
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));}
			//SAINTS	
			else{
				printf($base_formatstr ."
				</tr>",
				$myrow['typename'],
				$Datos['serie'].$Datos['folio'].'('.$myrow['transno'].')',
				//$myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));}
                }
		} else {
			
			if($myrow['rh_status']=='C'){ // cancelada
				printf($base_formatstr .
				'</tr>',
				$myrow['typename'],
				$Datos['serie'].$Datos['folio'].'('.$myrow['transno'].')',
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));
			}else {

				printf($base_formatstr .
				'</tr>',
				$myrow['typename'],
				$Datos['serie'].$Datos['folio'].'('.$myrow['transno'].')',
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));
				
			}
		}
	
	// bowikaxu - permitir cancelar remision
	}else if(in_array(5,$_SESSION['AllowedPageSecurityTokens']) && $myrow['type']==20000){ /* Sept 2006 its a shipment realhost bowikaxu */
		
		if($myrow['rh_status']=='C'){ // cancelada
		
		printf($base_formatstr ."
				</tr>",
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));
		}else {
			printf($base_formatstr ."
				</tr>",
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));
		}

				// bowikaxu - no permitir cancelar remision
	}else if(!in_array(5,$_SESSION['AllowedPageSecurityTokens']) && $myrow['type']==20000){ 
	
		
		if($myrow['rh_status']=='C'){ // cancelada
		printf($base_formatstr ."
				</tr>",
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));
		}else {
			printf($base_formatstr ."
				</tr>",
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));
		}
				
		
	}elseif($myrow['type']==10) { /*its an invoice but not high enough priveliges to credit it */

		// bowikaxu april 2007 - get external invoice number   
		    //SAINTS
		    $sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations
            WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
            AND locations.loccode = rh_invoicesreference.loccode";
                                            
            /*$sql2="SELECT rh_invoicesreference.extinvoice, locations.rh_serie, c.serie, c.folio 
            FROM rh_invoicesreference INNER JOIN rh_cfd__cfd c ON rh_invoicesreference.intinvoice=c.fk_transno, locations 
            WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']." AND locations.loccode = rh_invoicesreference.loccode";*/
                                            
            $res = DB_query($sql,$db);
            $ExtInvoice = DB_fetch_array($res);
            //$res2 = DB_query($sql2,$db);
            //$ExtInvoice2 = DB_fetch_array($res2);
			
		//SAINTS    
		if($myrow['folio']!="")
			{printf($base_formatstr .
			 '</tr>',
			 $myrow['typename'],
			 //$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
			 $myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
			 ConvertSQLDate($myrow['trandate']),
			 $myrow['branchcode'],
			 $myrow['reference'],
			 $myrow['invtext'],
			 $myrow['order_'],
			 number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));}
		
		//SAINTS	 
		else{printf($base_formatstr .
			 '</tr>',
			 $myrow['typename'],
			 $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
			 ConvertSQLDate($myrow['trandate']),
			 $myrow['branchcode'],
			 $myrow['reference'],
			 $myrow['invtext'],
			 $myrow['order_'],
			 number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));}
			
	} elseif ($myrow['type']==11) { /*its a credit note */
		
		// bowikaxu realhost - sept 2007 - nota de credito externa
		$sql = "SELECT extcn FROM rh_crednotesreference WHERE ref = '".$myrow['id']."'";
		$res = DB_query($sql,$db,'Imposible obtener nota de credito externa');
		$ext = DB_fetch_array($res);
		
		if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND in_array(8,$_SESSION['AllowedPageSecurityTokens'])){
			
			//SAINTS
			if($myrow['serie']!=""){printf($base_formatstr .'</tr>',
				$myrow['typename'],
				$myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
				//$myrow['transno'].'('.$ext['extcn'].')',
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));}
			//SAINTS	
			else{printf($base_formatstr .'</tr>',
				$myrow['typename'],
				$myrow['transno'].'('.$ext['extcn'].')',
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));}

		} else {
			printf($base_formatstr ."
				</tr>",
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));
		}
	} elseif ($myrow['type']==12 AND $myrow['totalamount']<0) { /*its a receipt  which could have an allocation*/
		if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND in_array(8,$_SESSION['AllowedPageSecurityTokens'])){
			printf($base_formatstr ."
				</tr>",
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));
		} else {
			printf($base_formatstr ."
				</tr>",
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));
		}
	} elseif ($myrow['type']==12 AND $myrow['totalamount']>0) { /*its a negative receipt */
		if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND in_array(8,$_SESSION['AllowedPageSecurityTokens'])){
			printf($base_formatstr .'</tr>',
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));
		} else {
			printf($base_formatstr . '<td></tr>',
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));
		}
	} else {
		if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND in_array(8,$_SESSION['AllowedPageSecurityTokens'])){
			printf($base_formatstr .'</tr>',
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));
		} else {
			printf($base_formatstr . '</tr>',
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
                number_format($myrow['saldo'],2));
		}
	}

	$j++;
	If ($j == 12){
		$j=1;
		echo $tableheader;
	}
	//end of page full new headings if
}
//end of while loop

// bowikaxu realhost - print transactions total
echo "<tr><td colspan=7 class=tableheader align=right><STRONG>"._('Total').'</td><td class=tableheader>'.number_format($Total,2).
"</td><td class=tableheader>".number_format($TotalSaldo,2).
"</td>"."</STRONG></tr>";

echo '</table>';
include('includes/footer.inc');
?>
