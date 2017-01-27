<?php

/* $Revision: 200 $ */

include('includes/SQL_CommonFunctions.inc');

//Jaime includes
include('XMLFacturacionElectronica/utils/Php.php');
//Termina Jaime includes

$PageSecurity = 1;

include('includes/session.inc');
$title = _('Customer Inquiry');
include('includes/header.inc');

$esconderContabilidad = false;

//echo "<hr>";
//print_r($_SESSION);
//echo "<hr>";
// always figure out the SQL required from the inputs available
if(!isset($_GET['CustomerID']) AND !isset($_SESSION['CustomerID'])){
	prnMsg(_('To display the enquiry a customer must first be selected from the customer selection screen'),'info');
	echo "<BR><CENTER><A HREF='". $rootpath . "/SelectCustomer.php?" . SID . "'>" . _('Select a Customer to Inquire On') . '</A><BR></CENTER>';
	include('includes/footer.inc');
	exit;
} else {
	if (isset($_GET['CustomerID'])){
		$_SESSION['CustomerID'] = DB_escape_string($_GET['CustomerID']);
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

$SQL = 'SELECT debtorsmaster.name,
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
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, ' . INTERVAL('1', 'MONTH') . '), ' . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ')) >= 0 THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
		END) AS due,
		SUM(CASE WHEN (paymentterms.daysbeforedue > 0) THEN
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue
			AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + ' .
		$_SESSION['PastDueDays1'] . ')
			THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
		ELSE
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, ' . INTERVAL('1', 'MONTH') . '), ' . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ')) >= ' . $_SESSION['PastDueDays1'] . ')
			THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount
			- debtortrans.alloc ELSE 0 END
		END) AS overdue1,
		SUM(CASE WHEN (paymentterms.daysbeforedue > 0) THEN
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue
			AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + ' . $_SESSION['PastDueDays2'] . ') THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
		ELSE
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, ' . INTERVAL('1','MONTH') . '), ' . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ')) >= ' . $_SESSION['PastDueDays2'] . ") THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
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

if(isset($_REQUEST['bytype'])){

	$BYTYPE = "group by debtortrans.type ";
	$SUM = "SUM( ";
	$GROUP = TRUE;
	$SUMC = " )";
}

$CustomerRecord = DB_fetch_array($CustomerResult);
//
// echo "<pre>";
//     var_dump($CustomerRecord);
// echo "</pre>";

if ($NIL_BALANCE==True){
	$CustomerRecord['balance']=0;
	$CustomerRecord['due']=0;
	$CustomerRecord['overdue1']=0;
	$CustomerRecord['overdue2']=0;
}

    /*Obtengo Datos del Afiliado*/
    $_2GetAfilData = "SELECT ti.folio, cobranza.cobrador, fa.tipo_membresia
                           FROM rh_titular ti
                           LEFT JOIN rh_cobranza cobranza ON cobranza.folio = ti.folio
                           LEFT JOIN rh_foliosasignados fa ON ti.folio = fa.folio
                           WHERE ti.debtorno = '{$CustomerID}'";
    $_GetAfilData=DB_query($_2GetAfilData,$db);
    $GetAfilData = DB_fetch_assoc($_GetAfilData);


    echo '<CENTER>
            <FONT SIZE=4>' . $GetAfilData['tipo_membresia'] . ' - ' . $GetAfilData['folio'] . ' - ' . $CustomerID . ' - ' . $CustomerRecord['name'] . ' </FONT></B> - (' . _('All amounts stated in') . ' ' . $CustomerRecord['currency'] . ')</CENTER><BR>
                <B><FONT COLOR=BLUE>' . _('Terms') . ': ' . $CustomerRecord['terms'] . '<BR>' . _('Credit Limit') . ': </B></FONT> ' . number_format($CustomerRecord['creditlimit'],0) . '  <B><FONT COLOR=BLUE>' . _('Credit Status') . ':</B></FONT> ' . $CustomerRecord['reasondescription'];

    if ($CustomerRecord['dissallowinvoices']!=0){
        echo '<BR><FONT COLOR=RED SIZE=4><B>' . _('ACCOUNT ON HOLD') . '</FONT></B><BR>';
    }

    echo "<TABLE class='table table-striped table-bordered'>
            <thead>
                <TR class='tableheader'>
                    <th class='tableheader'>" . _('Total Balance') . "</th>
                    <th class='tableheader'>" . _('Current') . "</th>
                    <th class='tableheader'>" . _('Now Due') . "</th>
                    <th class='tableheader'>" . $_SESSION['PastDueDays1'] . "-" . $_SESSION['PastDueDays2'] . ' ' . _('Days Overdue') . "</th>
                    <th class='tableheader'>" . _('Over') . ' ' . $_SESSION['PastDueDays2'] . ' ' . _('Days Overdue') . '</th>
                </TR>
            </thead>';

    echo '  <tbody>
                <TR>
                    <TD ALIGN=RIGHT>' . number_format($CustomerRecord['balance'],2) . '</TD>
                    <TD ALIGN=RIGHT>' . number_format(($CustomerRecord['balance'] - $CustomerRecord['due']),2) . '</TD>
                    <TD ALIGN=RIGHT>' . number_format(($CustomerRecord['due']-$CustomerRecord['overdue1']),2) . '</TD>
                    <TD ALIGN=RIGHT>' . number_format(($CustomerRecord['overdue1']-$CustomerRecord['overdue2']) ,2) . '</TD>
                    <TD ALIGN=RIGHT>' . number_format($CustomerRecord['overdue2'],2) . '</TD>
                </TR>
            </tbody>
        </TABLE>';
    echo "<div style='height:20px;'></div>";


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

        echo 'Agrupar por documento <input name="bytype" id="bytype" type="checkbox" />';
        echo "<INPUT TYPE=SUBMIT NAME='Refresh Inquiry' VALUE='" . _('Refresh Inquiry') . "'>
    </FORM>";
    echo "<div style='height:20px;'></div>";

    $DateAfterCriteria = FormatDateForSQL($_POST['TransAfterDate']);
    // bowikaxu realhost nov 07
    $DateBeforeCriteria = FormatDateForSQL($_POST['TransBeforeDate']);

    //Jaime (modificado) se altero el query para incluir una columna que nos dice si la factura es un CFD
    $SQL = "SELECT if(isnull(v.id_salesorders),if(isnull(cp.id_salesorders),systypes.typename,if(c.tipo_de_comprobante='ingreso','Carta Porte Ingreso', 'Carta Porte Traslado')),'Transportista') typename,
    		debtortrans.id,
            debtortrans.debtorno,
    		debtortrans.type,
    		debtortrans.transno,
    		debtortrans.branchcode,
    		debtortrans.trandate,
    		debtortrans.reference,
    		debtortrans.invtext,
    		debtortrans.order_,
    		debtortrans.rate,
    		debtortrans.rh_status,
    		$SUM (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount) $SUMC AS totalamount,
    		debtortrans.alloc AS allocated,
                    not isnull(c.id) is_cfd,
                    c.serie,
                    c.folio,
                    c.uuid,
                    c.no_certificado,
                    c.fk_transno,
                    not isnull(cp.id_salesorders) is_carta_porte,
                    not isnull(v.id_salesorders) is_transportista
    	FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id
            left join rh_carta_porte cp on debtortrans.order_ = cp.id_salesorders
            left join rh_vps__transportista v on debtortrans.order_ = v.id_salesorders,
    		systypes
    	WHERE debtortrans.type = systypes.typeid
    	AND debtortrans.type ".$SelType."
    	AND debtortrans.branchcode ".$SelBranch."
    	AND debtortrans.debtorno = '" . $CustomerID . "'
    	AND (debtortrans.trandate >= '$DateAfterCriteria 00:00:00' and debtortrans.trandate <= '$DateBeforeCriteria 23:59:59' )
    	$BYTYPE   ORDER BY  debtortrans.trandate, debtortrans.transno   ";

        //Termina Jaime (modificado) se altero el query para incluir una columna que nos dice si la factura es un CFD
        $ErrMsg = _('No transactions were returned by the SQL because');
        $TransResult = DB_query($SQL,$db,$ErrMsg);

        if (DB_num_rows($TransResult)==0){
            echo _('There are no transactions to display since') . ' ' . $_POST['TransAfterDate'];
            include('includes/footer.inc');
            exit;
        }
        /*show a table of the invoices returned by the SQL */

    echo '<TABLE style="width:100%" class="table table-bordered table-striped table-hover">';

    $tableheader = "
    <thead>
        <tr class='tableheader'>
            <th class='tableheader'>" . _('Type') . "</th>
            <th class='tableheader'>" . _('Number') . "</th>
            <th class='tableheader'>" . _('Factura') . "</th>
            <th class='tableheader'></th>
            <th class='tableheader'>" . _('Date') . "</th>
            <th class='tableheader'>" . _('Branch') . "</th>
            <th class='tableheader'>" . _('Reference') . "</th>
            <th class='tableheader'>" . _('Comments') . "</th>
            <th class='tableheader'>" . _('Order') . "</th>
            <th class='tableheader'>" . _('Total') . "</th>
            <th class='tableheader'>" . _('Allocated') . "</th>
            <th class='tableheader'>" . _('Balance') . "</th>
            <th colspan='10'></th>

        </tr>
    </thead>";

    echo $tableheader;

    $Total = 0;
    $TotalAlloc = 0;
    $j = 1;
    $k=0; //row colour counter
    while ($myrow=DB_fetch_array($TransResult)) {

        if($GROUP){
            $myrow['branchcode'] = null;
            $myrow['reference'] = null;
            $myrow['invtext'] = null;
            $myrow['transno'] = null;
            $myrow['trandate'] = null;
        }

        $Total += $myrow['totalamount'];
        $TotalAlloc += $myrow['allocated'];
        // bowikaxu realhost - june 30 2007 - change color on cancelled transactions
        if($myrow['type']==20000 && $myrow['rh_status']=='C'){
            echo "<tr class='danger'>";
        }else if (($myrow['type']==10 || $myrow['type']==11 || $myrow['type']==20001 || ($myrow['type']==20002)) && $myrow['rh_status']=='C'){
            echo "<tr class='danger'>";
        }else if ($myrow['type']==11 && $myrow['rh_status']=='R'){ // nota de credito cancela remision
            echo "<tr class='warning'>";
        }else if ($myrow['type']==11 && $myrow['rh_status']=='F'){ // nota de credito cancela factura
            echo "<tr class='warning'>";
        }else {
            if ($k==1){
                echo "<tr bgcolor='#CCCCCC'>";
                $k=0;
            } else {
                echo "<tr bgcolor='#EEEEEE'>";
                $k=1;
            }
        }

        //$FormatedTranDate = ConvertSQLDate($myrow['trandate']);
        /****************AGREGADO QUE MUESTRE LAS FACTURAS QUE PAGO UN DEPOSITO ***************************/
        $FormatedTranDate = ConvertSQLDate($myrow['trandate']);
        $FolioFactura = "";
        $sql="SELECT cfdi.folio, cfdi.serie,dt.transno FROM custallocns cal
            LEFT JOIN debtortrans dt ON
            (dt.id=cal.transid_allocfrom OR dt.id=cal.transid_allocto)
            and dt.id != {$myrow['id']}
            LEFT JOIN rh_cfd__cfd cfdi ON dt.id = cfdi.id_debtortrans
            WHERE
            (cal.transid_allocfrom={$myrow['id']} OR cal.transid_allocto={$myrow['id']})
            AND dt.debtorno = '{$myrow['debtorno']}'
        ";
        $ResCALR = DB_query($sql, $db);

        while ( $ResCAL = DB_fetch_assoc($ResCALR)) {
            if($ResCAL['serie'] != '' || $ResCAL['folio'] != ''){
                $FolioFactura .= $ResCAL['folio'] . $ResCAL['serie'] . " ({$ResCAL['transno']}) , ";
            }else{
                $FolioFactura .= $ResCAL['transno'] . ", ";
            }
        }
        /*****************************************************************************************************/


        $base_formatstr = "
            <td>%s</td>
            <td>%s</td>
            <td>%s</td>
            <td>{$FolioFactura}</td>
            <td>%s</td>
            <td>%s</td>
            <td>%s</td>
            <td width='200'>%s</td>
            <td>%s</td>
            <td ALIGN=RIGHT>%s</td>
            <td ALIGN=RIGHT>%s</td>
            <td ALIGN=RIGHT>%s</td><td>";
        if($myrow['rh_status']=='C'){ // factura cancelada
            $credit_invoice_str = "
                <div tag='%s%s%s'></div>
                <IMG SRC='%s/cancel_disable.gif' TITLE='" . _('Factura Cancelada') . "'>";

            $credit_invoice_str2 = "
                <div style='display:none'>
                    <a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the invoiceNC') . "'></a>
                   </div>
                ";

            //Notas de Cargo iJPe
            $credit_cargo_str = "
                <div style='display:none'>
                    <a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the invoiceNC') . "'></a>
                    		</div>
                ";

            $credit_cargo_str2 = "
            		<div style='display:none'>
                    <a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the invoiceNC') . "'></a></div>";

            // remisiones
            // bowikaxu shipments links to preview and credit
            $credit_shipment_str = "
                    <a href='%s/rh_Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the shipment') . "'></a>
                    <IMG SRC='%s/cancel_disable.gif' TITLE='" . _('Click para cancelar remision') . "'>";

            $credit_shipment_str2 = "<div style='display:none'>
                    <a href='%s/rh_Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the shipment') . "'></a></div>
                    <IMG SRC='%s/cancel_disable.gif' TITLE='" . _('Click para cancelar remision') . "'>";


            //iJPe 2010-03-23 Impresion de Remision
            $preview_shipment_str = "
                <a target='_blank' href='%s/rh_PDFRemGde.php?&FromTransNo=%s&InvOrCredit=Invoice'><IMG SRC='%s/preview.gif' TITLE='" . _('Click to preview the shipment') . "'></a>
                <a target='_blank' href='%s/rh_PDFRemGde.php?&FromTransNo=%s&InvOrCredit=Invoice&PrintPDF=True'><IMG SRC='%s/reports.png' TITLE='" . _('Imprimir Copia de Remision') . "'></a>
                <a target='_blank' href='%s/rh_EmailCustTrans.php?FromTransNo=%s&InvOrCredit=Invoice'><IMG SRC='%s/email.gif' TITLE='" . _('Click to email the shipment') . "'></a>";
        }else { // factura no cancelada
            //Jaime, si es carta porte se modifica el href para que apunte al script correcto
            $credit_invoice_str = "
                <div tag='%s%s%s'>
                    <a href='%s/" . (($myrow['is_carta_porte']?'rh_cartaPorte_Cancel_Invoice.php?InvoiceNumber=%s':'rh_Cancel_Invoice.php?InvoiceNumber=%s')) . "'>
                        <IMG SRC='%s/cancel.gif' TITLE='" . _('Click para cancelar factura') . "'>
                    </a></div>
                ";
            //\Jaime, si es carta porte se modifica el href para que apunte al script correcto
            if($myrow['is_carta_porte'])
            $credit_invoice_str2 = "
                    <a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the invoiceNC') . "'></a>
                ";
                /*$credit_invoice_str2 = "<div tag='%s%s%s'>
                <a href='%s/" . (($myrow['is_carta_porte']?'rh_cartaPorte_Cancel_Invoice.php?InvoiceNumber=%s':'rh_Cancel_Invoice.php?InvoiceNumber=%s')) . "'><IMG SRC='%s/cancel.gif' TITLE='" . _('Click para cancelar factura') . "'></a>
                </div>
                ";*/
                // iJPe	Notas de Cargo
                /* if ($myrow['type']==10 || $myrow['type']==20002){
                $credit_cargo_str = "<a href='%s/rh_NoteC_Invoice.php?CustomerID=".$_GET['CustomerID']."&InvoiceNumber=%s'><IMG " . ($myrow['type']!=10?'style="display:none"':'') . " SRC='%s/notaC.jpg' TITLE='" . _('Crear nota de cargo') . "'></a>
                ";
                }else{
                $credit_cargo_str = "<a href='%s/rh_Cancelar_NC.php?NCNumber=1&InvoiceNumber=%s'><IMG SRC='%s/cancel.gif' TITLE='" . _('Cancelar nota de cargo') . "'></a>
                ";
                } */
                //<a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the invoice') . "'></a>
			if($myrow['is_carta_porte'])
            $credit_cargo_str2 = "
                <a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Ver nota de cargo') . "'></a>";
            $cancel_credit = "
                <a href='%s/Z_DeleteCreditNote.php?CreditNoteNo=%s&CustInq=1'><IMG SRC='%s/cancel.gif' TITLE='" . _('Clic para cancelar nota de credito') . "'></a>";

            // remisiones
            // bowikaxu shipments links to preview and credit
            $credit_shipment_str = "<a href='%s/rh_Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the shipment') . "'></a>
                <a href='%s/rh_Cancel_Remision.php?InvoiceNumber=%s'><IMG SRC='%s/cancel.gif' TITLE='" . _('Click para cancelar remision') . "'></a>";

            $credit_shipment_str2 = "
                <a href='%s/rh_Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the shipment') . "'></a>
                <IMG SRC='%s/cancel.gif' TITLE='" . _('Click para cancelar remision') . "'>";

            //iJPe 2010-03-23 Impresion de Remision
            $preview_shipment_str = "<a target='_blank' href='%s/rh_PDFRemGde.php?&FromTransNo=%s&InvOrCredit=Invoice'><IMG SRC='%s/preview.gif' TITLE='" . _('Click to preview the shipment') . "'></a>
                <a target='_blank' href='%s/rh_EmailCustTrans.php?FromTransNo=%s&InvOrCredit=Invoice'><IMG SRC='%s/email.gif' TITLE='" . _('Click to email the shipment') . "'></a>
                <a target='_blank' href='%s/rh_PDFRemGde.php?&FromTransNo=%s&InvOrCredit=Invoice&PrintPDF=True'><IMG SRC='%s/reports.png' TITLE='" . _('Imprimir Copia de Remision') . "'></a>";

    }
            //Jaime (modificado) se agrego el atributo isCfd que permite saber si la factura es un CFD
            //Jaime (remodificado) se dejo como estaba antes y se le agrego la linea 423
            //Jaime (remodificado) se agrego el campo isCfdCancelado para que la impresion aparesca como CANCELADA
            //$preview_invoice_str = "<a target='_blank' href=" . (!$myrow['is_cfd']?("'%s/rh_PrintCustTrans.php?FromTransNo=%s&InvOrCredit=Invoice'"):("'%s/PHPJasperXML/sample1.php?transno=%s'")) . "><IMG SRC='%s/preview.gif' TITLE='" . _('Click to preview the invoice') . "'></a></td>
            $preview_invoice_str ="<div style='";
            if($myrow['rh_status']=='C')
            	$preview_invoice_str .="display:none";
            $preview_invoice_str .="'>";
            $preview_invoice_str .= "<a href='".$rootpath."/Credit_Invoice.php?InvoiceNumber=".$myrow['transno']."'><IMG SRC='".$rootpath.'/css/'.$theme."/images/credit.gif' TITLE='" . _('Click to credit the invoiceNC') . "'></a></div><a target='_blank' href='%s/rh_PrintCustTrans.php?idDebtortrans=".$myrow['id']."&FromTransNo=%s&InvOrCredit=Invoice&isCfd=" . $myrow['is_cfd'] . "'><IMG SRC='%s/preview.gif' TITLE='" . _('Click to preview the invoice') . "'></a>".
                (strlen($myrow['uuid'])>0?
                                    ($myrow['is_cfd']?"<a target='_blank' href='%s/EmailCustTrans_CFDI.php?FromTransNo=%s&InvOrCredit=Invoice&isCfd=" . $myrow['is_cfd'] . ($myrow['is_carta_porte']?'&isCartaPorte=true':'') . ($myrow['rh_status']=='C'?'&isCfdCancelado=true':'') . "'><IMG SRC='%s/email.gif' TITLE='" . _('Click to email the invoice') . "'></a>":""):
                                    ($myrow['is_cfd']?"<a target='_blank' href='%s/EmailCustTrans.php?FromTransNo=%s&InvOrCredit=Invoice&isCfd=" . $myrow['is_cfd'] . ($myrow['is_carta_porte']?'&isCartaPorte=true':'') . ($myrow['rh_status']=='C'?'&isCfdCancelado=true':'') . "'><IMG SRC='%s/email.gif' TITLE='" . _('Click to email the invoice') . "'></a>":"")
                                ).

                                (strlen($myrow['uuid'])>0?
                                    ($myrow['is_cfd']?"<a target='_blank' href='rh_j_downloadFacturaElectronicaXML_CFDI.php?downloadPath=" . ('XMLFacturacionElectronica/xmlbycfdi/' . $myrow['uuid']. '.xml') . "'><IMG SRC='$rootpath/images/xml.gif' TITLE='" . _('Click to download the invoice') . _(' (XML)') . "'></a>":""):
                                    ($myrow['is_cfd']?"<a target='_blank' href='rh_j_downloadFacturaElectronicaXML.php?downloadPath=" . ('XMLFacturacionElectronica/facturasElectronicas/' . $myrow['no_certificado'] . '/' . $myrow['serie'] . $myrow['folio'] . '-' . $myrow['fk_transno'] . '.xml') . "'><IMG SRC='$rootpath/images/xml.gif' TITLE='" . _('Click to download the invoice') . _(' (XML)') . "'></a>":"")
                                ).
                                ($myrow['is_cfd']?"<a target='_blank' href='$rootpath/PHPJasperXML/sample1.php?isTransportista=" . $myrow['is_transportista'] . "&transno=" . $myrow['transno'] . '&' . ($myrow['is_carta_porte']?'isCartaPorte=true':'') . ($myrow['rh_status']=='C'?'&isCfdCancelado=true':'') . "&afil=true'><IMG SRC='$rootpath/css/silverwolf/images/pdf.gif' TITLE='" . _('Click to preview the invoice') . _(' (PDF)') . "'></a>":"").
                                //SAINTS
                                ($myrow['is_cfd']?"<a target='_blank' href='$rootpath/PHPJasperXML/sample1.php?isTransportista=" . $myrow['is_transportista'] . "&copia=si&transno=" . $myrow['transno'] . '&' . ($myrow['is_carta_porte']?'isCartaPorte=true':'') . ($myrow['rh_status']=='C'?'&isCfdCancelado=true':'') . "&afil=true'><IMG SRC='$rootpath/css/silverwolf/images/pdf_copia.png' TITLE='" . _('Click para visualizar la Copia de la factura') . _(' (PDF)') . "'></a>":"").
                                (!$myrow['is_cfd']?"<a target='_blank' href='rh_recoverxml.php?id=" . $myrow['id']. "'><IMG SRC='$rootpath/recover.png' TITLE='" . _('Recuperar Factura') . "'></a>":"");

                                //termina jaime agregado
            //\Jaime (remodificado) se agrego el campo isCfdCancelado para que la impresion aparesca como CANCELADA
            //Termina Jaime (remodificado) se dejo como estaba antes y se le agrego el <td> para visualizar la factura en PDF
            //Termina Jaime (modificado) se agrego el atributo isCfd que permite saber si la factura es un CFD
            //Jaime (modificado) agregado un <td></td> al final
            $preview_credit_str = "<a target='_blank' href='%s/rh_PrintCustTrans.php?idDebtortrans=".$myrow['id']."&FromTransNo=%s&InvOrCredit=Credit'><IMG SRC='%s/preview.gif' TITLE='" . _('Click to preview the credit note') . "'></a>
				".(strlen($myrow['uuid'])>0?
                                    ($myrow['is_cfd']?"<a target='_blank' href='%s/EmailCustTrans_CFDI.php?FromTransNo=%s&InvOrCredit=Credit'><IMG SRC='%s/email.gif' TITLE='" . _('Click to email the credit note') . "'></a>":""):
                                    ($myrow['is_cfd']?"<a target='_blank' href='%s/EmailCustTrans.php?FromTransNo=%s&InvOrCredit=Credit'><IMG SRC='%s/email.gif' TITLE='" . _('Click to email the credit note') . "'></a>":"")
                                ).
                (strlen($myrow['uuid'])>0?
                ($myrow['is_cfd']?"<a target='_blank' href='rh_j_downloadFacturaElectronicaXML.php?downloadPath=" . ('XMLFacturacionElectronica/facturasElectronicas/' . $myrow['no_certificado'] . '/' . $myrow['serie'] . $myrow['folio'] . '-' . $myrow['fk_transno'] . '.xml') . "'><IMG SRC='$rootpath/images/xml.gif' TITLE='" . _('Click to download the invoice') . _(' (XML)') . "'></a>":""):
                ($myrow['is_cfd']?"<a target='_blank' href='rh_j_downloadFacturaElectronicaXML.php?downloadPath=" . ('XMLFacturacionElectronica/xmlbycfdi/' . $myrow['uuid']. '.xml') . "'><IMG SRC='$rootpath/images/xml.gif' TITLE='" . _('Click to download the invoice') . _(' (XML)') . "'></a>":"")
                ).
                ($myrow['is_cfd']?"<a target='_blank' href='$rootpath/PHPJasperXML/sample1.php?isTransportista=" . $myrow['is_transportista'] . "&isNotaDeCredito=true&transno=" . $myrow['transno'] . '&' . ($myrow['is_carta_porte']?'isCartaPorte=true':'') . ($myrow['rh_status']=='C'?'&isCfdCancelado=true':'') . "&afil=true'><IMG SRC='$rootpath/css/silverwolf/images/pdf.gif' TITLE='" . _('Click to preview the invoice') . _(' (PDF)') . "'></a>":"").
                (!$myrow['is_cfd']&&$myrow['rh_status']=='N'?"<a target='_blank' href='rh_recoverxml.php?id=" . $myrow['id']. "'><IMG SRC='$rootpath/recover.png' TITLE='" . _('Recuperar Factura') . "'></a>":"");

            //Termina Jaime (modificado) agregado un <td></td> al final

            // Sept 2006 RealHost
        if ((in_array(5,$_SESSION['AllowedPageSecurityTokens'])||in_array(1,$_SESSION['AllowedPageSecurityTokens'])) && (($myrow['type']==10) || ($myrow['type']==20002))){ /*Show a link to allow an invoice to be credited */
            // bowikaxu april 2007 - get external invoice number
            //rlea Jul 30 2011 se quita la llamada a rh_invoicesreference
            /*
			$sql = "SELECT rh_invoicesreference.extinvoice, rh_locations.rh_serie FROM rh_invoicesreference, rh_locations
			WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
			AND rh_locations.loccode = rh_invoicesreference.loccode";
		    $res = DB_query($sql,$db);
		    $ExtInvoice = DB_fetch_array($res);*/

            $credit_cargo_str2 = str_replace(array("<td>","</td>"), "&nbsp;", $credit_cargo_str2);
            $cancel_credit = str_replace(array("<td>","</td>"), "&nbsp;", $cancel_credit);
            //$preview_shipment_str = str_replace(array("<td>","</td>"), "&nbsp;", $preview_shipment_str);
            $credit_shipment_str2 = str_replace(array("<td>","</td>"), "&nbsp;", $credit_shipment_str2);
            $preview_credit_str = str_replace(array("<td>","</td>"), "&nbsp;", $preview_credit_str);
            $credit_invoice_str2 = str_replace(array("<td>","</td>"), "&nbsp;", $credit_invoice_str2);
            $preview_invoice_str = str_replace(array("<td>","</td>"), "&nbsp;", $preview_invoice_str);

            // si permitir cancelacion de factura
            if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND (in_array(8,$_SESSION['AllowedPageSecurityTokens'])||in_array(1,$_SESSION['AllowedPageSecurityTokens']))){

                if($myrow['rh_status']=='C'){ // cancelada
                    //SAINTS series y folios de FE 28/01/2011
                    if($myrow['folio']!=""){
                        printf($base_formatstr .
                            $credit_invoice_str .
                            $preview_invoice_str .
                            "<A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'>" . _('GL') . "<A>
                            </td></tr>",
                            $myrow['typename'],
                            //$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                            $myrow['transno'],
                            $myrow['serie'].$myrow['folio'],
                            ConvertSQLDate($myrow['trandate']),
                            $myrow['branchcode'],
                            $myrow['reference'],
                            $myrow['invtext'],
                            $myrow['order_'],
                            number_format($myrow['totalamount'],2),
                            number_format($myrow['allocated'],2),
                            number_format($myrow['totalamount']-$myrow['allocated'],2),
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images',
                            $rootpath.'/css/'.$theme.'/images', // cancelada
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images',
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images',
                            $rootpath,
                            SID,
                            $myrow['type'],
                            $myrow['transno']
                        );
                    } else {
                        printf($base_formatstr .
                            $credit_invoice_str .
                            $preview_invoice_str .
                            "<A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'>" . _('GL') . "<A>
                            </td></tr>",
                            $myrow['typename'],
                            $myrow['transno'],
                            $myrow['serie'].$myrow['folio'],
                            //$myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
                            ConvertSQLDate($myrow['trandate']),
                            $myrow['branchcode'],
                            $myrow['reference'],
                            $myrow['invtext'],
                            $myrow['order_'],
                            number_format($myrow['totalamount'],2),
                            number_format($myrow['allocated'],2),
                            number_format($myrow['totalamount']-$myrow['allocated'],2),
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images',
                            $rootpath.'/css/'.$theme.'/images', // cancelada
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images',
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images',
                            $rootpath,
                            SID,
                            $myrow['type'],
                            $myrow['transno']);
                        }
                        //SAINTS fin
                }else {
                    //SAINTS series y folios de FE 28/01/2011
                    if($myrow['folio']!=""){
                        /* Add Icons */
                        printf($base_formatstr .
                            $credit_invoice_str .
                            $credit_cargo_str.
                            $preview_invoice_str .
                            "<A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ><A>
                            </td></tr>",
                            $myrow['typename'],
                            //$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                            // $myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
                            $myrow['transno'],
                            $myrow['serie'].$myrow['folio'],
                            ConvertSQLDate($myrow['trandate']),
                            $myrow['branchcode'],
                            $myrow['reference'],
                            $myrow['invtext'],
                            $myrow['order_'],
                            number_format($myrow['totalamount'],2),
                            number_format($myrow['allocated'],2),
                            number_format($myrow['totalamount']-$myrow['allocated'],2),
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images',
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images', // imagen cancelada
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images',
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images',
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images',
                            $rootpath,
                            SID,
                            $myrow['type'],
                            $myrow['transno'],
                            $myrow['transno']
                        );
                    } else {
                        printf($base_formatstr .
                            $credit_invoice_str .
                            $credit_cargo_str.
                            $preview_invoice_str .
                            "<A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'>" . _('GL') . "<A>
                            </td></tr>",
                            $myrow['typename'],
                            // $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                            //$myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
                            $myrow['transno'],
                            $myrow['serie'].$myrow['folio'],
                            ConvertSQLDate($myrow['trandate']),
                            $myrow['branchcode'],
                            $myrow['reference'],
                            $myrow['invtext'],
                            $myrow['order_'],
                            number_format($myrow['totalamount'],2),
                            number_format($myrow['allocated'],2),
                            number_format($myrow['totalamount']-$myrow['allocated'],2),
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images',
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images', // imagen cancelada
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images',
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images',
                            $rootpath,
                            $myrow['transno'],
                            $rootpath.'/css/'.$theme.'/images',
                            $rootpath,
                            SID,
                            $myrow['type'],
                            $myrow['transno'],
                            $myrow['transno']
                        );
                    }
                    //SAINTS fin
                }
            } else {
                if($myrow['rh_status']=='C'){ // cancelada
                    printf($base_formatstr .
                        $credit_invoice_str2 .
                        $preview_invoice_str .
                        '</td></tr>',
                        $myrow['typename'],
                        // $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                        $myrow['transno'],
                        $myrow['serie'].$myrow['folio'],
                        ConvertSQLDate($myrow['trandate']),
                        $myrow['branchcode'],
                        $myrow['reference'],
                        $myrow['invtext'],
                        $myrow['order_'],
                        number_format($myrow['totalamount'],2),
                        number_format($myrow['allocated'],2),
                        number_format($myrow['totalamount']-$myrow['allocated'],2),
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images'
                    );
                }else {
                    printf($base_formatstr .
                        $credit_invoice_str2 .
                        $preview_invoice_str .
                        '</td></tr>',
                        $myrow['typename'],
                        // $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                        $myrow['transno'],
                        $myrow['serie'].$myrow['folio'],
                        ConvertSQLDate($myrow['trandate']),
                        $myrow['branchcode'],
                        $myrow['reference'],
                        $myrow['invtext'],
                        $myrow['order_'],
                        number_format($myrow['totalamount'],2),
                        number_format($myrow['allocated'],2),
                        number_format($myrow['totalamount']-$myrow['allocated'],2),
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images'
                    );
                }
            }

            // bowikaxu - permitir cancelar remision
        }// Sept 2006 RealHost
        elseif (in_array(5,$_SESSION['AllowedPageSecurityTokens']) && ($myrow['type']==20001)){ /*Show a link to allow an invoice to be credited */
            // bowikaxu april 2007 - get external invoice number
            //rlea Jul 30 2011 se quita la llamada a rh_invoicesreference
            /*
            $sql = "SELECT rh_invoicesreference.extinvoice, rh_locations.rh_serie FROM rh_invoicesreference, rh_locations
                WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
                AND rh_locations.loccode = rh_invoicesreference.loccode";
                $res = DB_query($sql,$db);
                $ExtInvoice = DB_fetch_array($res);*/
                // si permitir cancelacion de factura
            if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND (in_array(8,$_SESSION['AllowedPageSecurityTokens'])||in_array(1,$_SESSION['AllowedPageSecurityTokens']))){
                if($myrow['rh_status']=='C'){ // cancelada
                    printf($base_formatstr .
                        $credit_cargo_str .
                        $preview_cargo_str .
                        "<div ".($esconderContabilidad?'style="display:none"':'')."><A HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'>" . _('GL') . "<A></div>
                        </td></tr>",
                        $myrow['typename'],
                        // $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                        $myrow['transno'],
                        $myrow['serie'].$myrow['folio'],
                        ConvertSQLDate($myrow['trandate']),
                        $myrow['branchcode'],
                        $myrow['reference'],
                        $myrow['invtext'],
                        $myrow['order_'],
                        number_format($myrow['totalamount'],2),
                        number_format($myrow['allocated'],2),
                        number_format($myrow['totalamount']-$myrow['allocated'],2),
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        SID,
                        $myrow['type'],
                        $myrow['transno']
                    );
                } else {
                    printf($base_formatstr .
                        $credit_cargo_str .
                        $preview_cargo_str .
                        "<div ".($esconderContabilidad?'style="display:none"':'')."><A HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'>" . _('GL') . "<A></div>
                        </td></tr>",
                        $myrow['typename'],
                        // $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                        $myrow['transno'],
                        $myrow['serie'].$myrow['folio'],
                        ConvertSQLDate($myrow['trandate']),
                        $myrow['branchcode'],
                        $myrow['reference'],
                        $myrow['invtext'],
                        $myrow['order_'],
                        number_format($myrow['totalamount'],2),
                        number_format($myrow['allocated'],2),
                        number_format($myrow['totalamount']-$myrow['allocated'],2),
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images', // imagen cancelada
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        SID,
                        $myrow['type'],
                        $myrow['transno']
                    );
                }
            } else {
                if($myrow['rh_status']=='C'){ // cancelada
                    printf($base_formatstr .
                        $credit_cargo_str2 .
                        $preview_cargo_str .
                        '</td></tr>',
                        $myrow['typename'],
                        // $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                        $myrow['transno'],
                        $myrow['serie'].$myrow['folio'],
                        ConvertSQLDate($myrow['trandate']),
                        $myrow['branchcode'],
                        $myrow['reference'],
                        $myrow['invtext'],
                        $myrow['order_'],
                        number_format($myrow['totalamount'],2),
                        number_format($myrow['allocated'],2),
                        number_format($myrow['totalamount']-$myrow['allocated'],2),
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images'
                    );
                }else {
                    printf($base_formatstr .
                        $credit_cargo_str2 .
                        $preview_cargo_str .
                        '</td></tr>',
                        $myrow['typename'],
                        // $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                        $myrow['transno'],
                        $myrow['serie'].$myrow['folio'],
                        ConvertSQLDate($myrow['trandate']),
                        $myrow['branchcode'],
                        $myrow['reference'],
                        $myrow['invtext'],
                        $myrow['order_'],
                        number_format($myrow['totalamount'],2),
                        number_format($myrow['allocated'],2),
                        number_format($myrow['totalamount']-$myrow['allocated'],2),
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images'
                    );
                }
            }
        }else if(in_array(5,$_SESSION['AllowedPageSecurityTokens']) && $myrow['type']==20000){ /* Sept 2006 its a shipment realhost bowikaxu */
            if($myrow['rh_status']=='C'){ // cancelada
                printf($base_formatstr .
                    $credit_shipment_str .
                    $preview_shipment_str .
                    "<div ".($esconderContabilidad?'style="display:none"':'')."><A HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'>" . _('GL') . "<A></div>
                    </td></tr>",
                    $myrow['typename'],
                    $myrow['transno'],
                    ConvertSQLDate($myrow['trandate']),
                    $myrow['branchcode'],
                    $myrow['reference'],
                    $myrow['invtext'],
                    $myrow['order_'],
                    number_format($myrow['totalamount'],2),
                    number_format($myrow['allocated'],2),
                    number_format($myrow['totalamount']-$myrow['allocated'],2),
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images', // aqui inicia remision
                    $rootpath,
                    SID,
                    $myrow['type'],
                    $myrow['transno']
                );
            }else {
                printf($base_formatstr .
                    $credit_shipment_str .
                    $preview_shipment_str .
                    "<div ".($esconderContabilidad?'style="display:none"':'')."><A HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'>" . _('GL') . "<A></div>
                    </td></tr>",
                    $myrow['typename'],
                    $myrow['transno'],
                    ConvertSQLDate($myrow['trandate']),
                    $myrow['branchcode'],
                    $myrow['reference'],
                    $myrow['invtext'],
                    $myrow['order_'],
                    number_format($myrow['totalamount'],2),
                    number_format($myrow['allocated'],2),
                    number_format($myrow['totalamount']-$myrow['allocated'],2),
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images', // aqui inicia remision
                    $rootpath,
                    SID,
                    $myrow['type'],
                    $myrow['transno']
                );
            }
            // bowikaxu - no permitir cancelar remision
        }else if(!in_array(5,$_SESSION['AllowedPageSecurityTokens']) && $myrow['type']==20000){
            if($myrow['rh_status']=='C'){ // cancelada
                printf($base_formatstr .
                    $credit_shipment_str2 .
                    $preview_shipment_str .
                    "<div ".($esconderContabilidad?'style="display:none"':'')."><A HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'>" . _('GL') . "<A></div>
                    </td></tr>",
                    $myrow['typename'],
                    $myrow['transno'],
                    ConvertSQLDate($myrow['trandate']),
                    $myrow['branchcode'],
                    $myrow['reference'],
                    $myrow['invtext'],
                    $myrow['order_'],
                    number_format($myrow['totalamount'],2),
                    number_format($myrow['allocated'],2),
                    number_format($myrow['totalamount']-$myrow['allocated'],2),
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images', // aqui inicia remision
                    $rootpath,
                    SID,
                    $myrow['type'],
                    $myrow['transno']
                );
            }else {
                printf($base_formatstr .
                    $credit_shipment_str2 .
                    $preview_shipment_str .
                    "<div ".($esconderContabilidad?'style="display:none"':'')."><A HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'>" . _('GL') . "<A></div>
                    </td></tr>",
                    $myrow['typename'],
                    $myrow['transno'],
                    ConvertSQLDate($myrow['trandate']),
                    $myrow['branchcode'],
                    $myrow['reference'],
                    $myrow['invtext'],
                    $myrow['order_'],
                    number_format($myrow['totalamount'],2),
                    number_format($myrow['allocated'],2),
                    number_format($myrow['totalamount']-$myrow['allocated'],2),
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath.'/css/'.$theme.'/images', // cancelar remision
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images', // aqui inicia remision
                    $rootpath,
                    SID,
                    $myrow['type'],
                    $myrow['transno']
                );
            }
        }elseif($myrow['type']==10) { /*its an invoice but not high enough priveliges to credit it */
            // bowikaxu april 2007 - get external invoice number
            //rlea Jul 30 2011 se quita la llamada a rh_invoicesreference
            /*
            $sql = "SELECT rh_invoicesreference.extinvoice, rh_locations.rh_serie FROM rh_invoicesreference, rh_locations
            WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
            AND rh_locations.loccode = rh_invoicesreference.loccode";
            $res = DB_query($sql,$db);
            $ExtInvoice = DB_fetch_array($res);*/

            //SAINTS series y folios de FE 28/01/2011
            if($myrow['folio']!=""){
                printf($base_formatstr .
                    $preview_invoice_str .
                    '</td></tr>',
                    $myrow['typename'],
                    //$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                    // $myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
                    $myrow['transno'],
                    $myrow['serie'].$myrow['folio'],
                    ConvertSQLDate($myrow['trandate']),
                    $myrow['branchcode'],
                    $myrow['reference'],
                    $myrow['invtext'],
                    $myrow['order_'],
                    number_format($myrow['totalamount'],2),
                    number_format($myrow['allocated'],2),
                    number_format($myrow['totalamount']-$myrow['allocated'],2),
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images'
                );
            } else {
                printf($base_formatstr .
                    $preview_invoice_str .
                    '</td></tr>',
                    $myrow['typename'],
                    // $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                    $myrow['transno'],
                    $myrow['serie'].$myrow['folio'],
                    ConvertSQLDate($myrow['trandate']),
                    $myrow['branchcode'],
                    $myrow['reference'],
                    $myrow['invtext'],
                    $myrow['order_'],
                    number_format($myrow['totalamount'],2),
                    number_format($myrow['allocated'],2),
                    number_format($myrow['totalamount']-$myrow['allocated'],2),
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images'
                );
            } //SAINTS fin
        }elseif($myrow['type']==20001) { /*its an invoice but not high enough priveliges to credit it */

            // bowikaxu april 2007 - get external invoice number
            //rlea Jul 30 2011 se quita la llamada a rh_invoicesreference
            /*
            $sql = "SELECT rh_invoicesreference.extinvoice, rh_locations.rh_serie FROM rh_invoicesreference, rh_locations
            WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
            AND rh_locations.loccode = rh_invoicesreference.loccode";
            $res = DB_query($sql,$db);
            $ExtInvoice = DB_fetch_array($res);
            */

            printf($base_formatstr .
                $preview_invoice_str .
                '</td></tr>',
                $myrow['typename'],
                // $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                $myrow['transno'],
                $myrow['serie'].$myrow['folio'],
                ConvertSQLDate($myrow['trandate']),
                $myrow['branchcode'],
                $myrow['reference'],
                $myrow['invtext'],
                $myrow['order_'],
                number_format($myrow['totalamount'],2),
                number_format($myrow['allocated'],2),
                number_format($myrow['totalamount']-$myrow['allocated'],2),
                $rootpath,
                $myrow['transno'],
                $rootpath.'/css/'.$theme.'/images',
                $rootpath,
                $myrow['transno'],
                $rootpath.'/css/'.$theme.'/images'
            );
        } elseif ($myrow['type'] == 11) { /*its a credit note */
            // bowikaxu realhost - sept 2007 - nota de credito externa
            //rlea Jul 30 2011 se quita la llamada a rh_crednotesreference
            /*
            $sql = "SELECT extcn FROM rh_crednotesreference WHERE ref = '".$myrow['id']."'";
            $res = DB_query($sql,$db,'Imposible obtener nota de credito externa');
            $ext = DB_fetch_array($res);*/

            if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND (in_array(8,$_SESSION['AllowedPageSecurityTokens'])||in_array(1,$_SESSION['AllowedPageSecurityTokens']))){
                //SAINTS series y folios de FE 28/01/2011
                if($myrow['folio']!=""){
                    printf($base_formatstr .
                        $cancel_credit.
                        $preview_credit_str .
                        "<a href='%s/CustomerAllocations.php?AllocTrans=%s'><IMG SRC='%s/conta.png' TITLE='" . _('Click to allocate funds') . "'></a>
                        <div ".($esconderContabilidad?'style="display:none"':'')."><A HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'>" . _('GL') . '<A></div></td></tr>',
                        $myrow['typename'],
                        //$myrow['transno'].'('.$ext['extcn'].')',
                        // $myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
                        $myrow['transno'],
                        $myrow['serie'].$myrow['folio'],
                        ConvertSQLDate($myrow['trandate']),
                        $myrow['branchcode'],
                        $myrow['reference'],
                        $myrow['invtext'],
                        $myrow['order_'],
                        number_format($myrow['totalamount'],2),
                        number_format($myrow['allocated'],2),
                        number_format($myrow['totalamount']-$myrow['allocated'],2),
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['id'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        SID,
                        $myrow['type'],
                        $myrow['transno']
                    );
                } else {
                    printf($base_formatstr .
                        $cancel_credit.
                        $preview_credit_str .
                        "<a href='%s/CustomerAllocations.php?AllocTrans=%s'><IMG SRC='%s/allocation.gif' TITLE='" . _('Click to allocate funds') . "'></a>
                        <div ".($esconderContabilidad?'style="display:none"':'')."><A HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'>" . _('GL') . '<A></div></td></tr>',
                        $myrow['typename'],
/*AKI*/                 //$myrow['transno'].'('.$ext['extcn'].')',
                        $myrow['transno'],
                        $myrow['serie'].$myrow['folio'],
                        ConvertSQLDate($myrow['trandate']),
                        $myrow['branchcode'],
                        $myrow['reference'],
                        $myrow['invtext'],
                        $myrow['order_'],
                        number_format($myrow['totalamount'],2),
                        number_format($myrow['allocated'],2),
                        number_format($myrow['totalamount']-$myrow['allocated'],2),
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['transno'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        $myrow['id'],
                        $rootpath.'/css/'.$theme.'/images',
                        $rootpath,
                        SID,
                        $myrow['type'],
                        $myrow['transno']
                    );
                } //SAINTS fin
            } else {
                printf($base_formatstr .
                    $cancel_credit.
                    $preview_credit_str .
                    "<a href='%s/CustomerAllocations.php?AllocTrans=%s'><IMG SRC='%s/allocation.gif' TITLE='" . _('Click to allocate funds') . "'></a></td>
                    </tr>",
                    $myrow['typename'],
                    $myrow['transno'],
                    '',
                    ConvertSQLDate($myrow['trandate']),
                    $myrow['branchcode'],
                    $myrow['reference'],
                    $myrow['invtext'],
                    $myrow['order_'],
                    number_format($myrow['totalamount'],2),
                    number_format($myrow['allocated'],2),
                    number_format($myrow['totalamount']-$myrow['allocated'],2),
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['transno'],
                    $rootpath.'/css/'.$theme.'/images',
                    $rootpath,
                    $myrow['id'],
                    $rootpath.'/css/'.$theme.'/images'
                );
            }
        } elseif ($myrow['type']==12 AND $myrow['totalamount']<0) { /*its a receipt  which could have an allocation*/
            if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND (in_array(8,$_SESSION['AllowedPageSecurityTokens'])||in_array(1,$_SESSION['AllowedPageSecurityTokens'])) ){
                /****************************************************************\
                * Jorge Garcia                                                   *
                * 15/Dic/2008 Cambio para hacer revez de pagos type 12           *
                * 15/Dic/2008 Bloqueo de asignacion para reversos                *
                \****************************************************************/
                $sqlrh = "SELECT * FROM rh_reverseo WHERE transid_reversfrom = ".$myrow['id']." AND type = ".$myrow['type']."";
                $resultrh = DB_query($sqlrh,$db);
                if (DB_num_rows($resultrh)==0) {
                    /* Add Icons */
                    printf($base_formatstr .
                        "<a href='%s/CustomerAllocations.php?AllocTrans=%s'><img title='" . _('Allocation') . "' src='{$rootpath}/css/silverwolf/images/asignacion.png' ></a> &nbsp;
                        <A ".($esconderContabilidad?'style="display:none"':'')." HREF='%s/GLTransInquiry.php?%s&TypeID=%s&TransNo=%s'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ><A> &nbsp;
                        <a href='%s/rh_reverseo.php?%s&TypeID=%s&TransNo=%s&CustomerID=%s&TransID=".$myrow['id']."'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to reverse') . "'></a> &nbsp;
                        </td>
                        </tr>",
                        $myrow['typename'],
                        $myrow['transno'],
                        '',
                        ConvertSQLDate($myrow['trandate']),
                        $myrow['branchcode'],
                        $myrow['reference'],
                        $myrow['invtext'],
                        $myrow['order_'],
                        number_format($myrow['totalamount'],2),
                        number_format($myrow['allocated'],2),
                        number_format($myrow['totalamount']-$myrow['allocated'],2),
                        $rootpath,
                        $myrow['id'],
                        $rootpath,
                        SID,
                        $myrow['type'],
                        $myrow['transno'],
                        $rootpath,
                        SID,
                        $myrow['type'],
                        $myrow['transno'],
                        $CustomerID,
                        $rootpath.'/css/'.$theme.'/images'
                    );
                }else{
                    printf($base_formatstr .
                    "<div colspan=3  ".($esconderContabilidad?'style="display:none"':'')."><A HREF='%s/GLTransInquiry.php?%s&TypeID=%s&TransNo=%s'>" . _('GL') . "<A></div></td></tr>",
                    $myrow['typename'],
                    $myrow['transno'],
                    '',
                    ConvertSQLDate($myrow['trandate']),
                    $myrow['branchcode'],
                    $myrow['reference'],
                    $myrow['invtext'],
                    $myrow['order_'],
                    number_format($myrow['totalamount'],2),
                    number_format($myrow['allocated'],2),
                    number_format($myrow['totalamount']-$myrow['allocated'],2),
                    $rootpath,
                    SID,
                    $myrow['type'],
                    $myrow['transno']);
                }
            } else {
                $sqlrh = "SELECT * FROM rh_reverseo WHERE transid_reversfrom = ".$myrow['id']." AND type = ".$myrow['type']."";
                $resultrh = DB_query($sqlrh,$db);
                if (DB_num_rows($resultrh)==0){
                    printf($base_formatstr .
                    "<a href='%s/CustomerAllocations.php?AllocTrans=%s'>" . _('Allocation') . "</a></td>
                    </tr>",
                    $myrow['typename'],
                    $myrow['transno'],
                    '',
                    ConvertSQLDate($myrow['trandate']),
                    $myrow['branchcode'],
                    $myrow['reference'],
                    $myrow['invtext'],
                    $myrow['order_'],
                    number_format($myrow['totalamount'],2),
                    number_format($myrow['allocated'],2),
                    number_format($myrow['totalamount']-$myrow['allocated'],2),
                    $rootpath,
                    $myrow['id']);
                }else{
                    printf($base_formatstr .
                    "</td></tr>",
                    $myrow['typename'],
                    $myrow['transno'],
                    '',
                    ConvertSQLDate($myrow['trandate']),
                    $myrow['branchcode'],
                    $myrow['reference'],
                    $myrow['invtext'],
                    $myrow['order_'],
                    number_format($myrow['totalamount'],2),
                    number_format($myrow['allocated'],2),
                    number_format($myrow['totalamount']-$myrow['allocated'],2));
                }
                /*****************************************************************************************************************************
                * Jorge Garcia Fin Modificacion
                *****************************************************************************************************************************/
            }
        } elseif ($myrow['type']==12 AND $myrow['totalamount']>0) { /*its a negative receipt */
            if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND (in_array(8,$_SESSION['AllowedPageSecurityTokens'])||in_array(1,$_SESSION['AllowedPageSecurityTokens']))){
                printf($base_formatstr .
                    "<div ".($esconderContabilidad?'style="display:none"':'')."><A HREF='%s/GLTransInquiry.php?%s&TypeID=%s&TransNo=%s'>" . _('GL') . '<A></div></td></tr>',
                    $myrow['typename'],
                    $myrow['transno'],
                    '',
                    ConvertSQLDate($myrow['trandate']),
                    $myrow['branchcode'],
                    $myrow['reference'],
                    $myrow['invtext'],
                    $myrow['order_'],
                    number_format($myrow['totalamount'],2),
                    number_format($myrow['allocated'],2),
                    number_format($myrow['totalamount']-$myrow['allocated'],2),
                    $rootpath,
                    SID,
                    $myrow['type'],
                    $myrow['transno']
                );
            } else {
                printf($base_formatstr . '</td></tr>',
                    $myrow['typename'],
                    $myrow['transno'],
                    '',
                    ConvertSQLDate($myrow['trandate']),
                    $myrow['branchcode'],
                    $myrow['reference'],
                    $myrow['invtext'],
                    $myrow['order_'],
                    number_format($myrow['totalamount'],2),
                    number_format($myrow['allocated'],2),
                    number_format($myrow['totalamount']-$myrow['allocated'],2)
                );
            }
        } else {
            if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND (in_array(8,$_SESSION['AllowedPageSecurityTokens'])||in_array(1,$_SESSION['AllowedPageSecurityTokens']))){
                printf($base_formatstr .
                    "<div ".($esconderContabilidad?'style="display:none"':'')."><A HREF='%s/GLTransInquiry.php?%s&TypeID=%s&TransNo=%s'>" . _('GL') . '<A></div></td></tr>',
                    $myrow['typename'],
                    $myrow['transno'],
                    '',
                    ConvertSQLDate($myrow['trandate']),
                    $myrow['branchcode'],
                    $myrow['reference'],
                    $myrow['invtext'],
                    $myrow['order_'],
                    number_format($myrow['totalamount'],2),
                    number_format($myrow['allocated'],2),
                    number_format($myrow['totalamount']-$myrow['allocated'],2),
                    $rootpath,
                    SID,
                    $myrow['type'],
                    $myrow['transno']
                );
            } else {
                printf($base_formatstr . '</td></tr>',
                    $myrow['typename'],
                    $myrow['transno'],
                    '',
                    ConvertSQLDate($myrow['trandate']),
                    $myrow['branchcode'],
                    $myrow['reference'],
                    $myrow['invtext'],
                    $myrow['order_'],
                    number_format($myrow['totalamount'],2),
                    number_format($myrow['allocated'],2),
                    number_format($myrow['totalamount']-$myrow['allocated'],2)
                );
            }
        }

        $j++;
        if ($j == 12){
            $j=1;
            echo $tableheader;
        }//end of page full new headings if

    }//end of while loop

    // bowikaxu realhost - print transactions total
    echo "<tr>
            <th colspan=8 class=tableheader align=right><STRONG>"._('Total').'</th>
            <th class=tableheader>'.number_format($Total,2)."</th>
            <th class=tableheader>".number_format($TotalAlloc,2)."</th>"."
            <th class=tableheader>".number_format($Total-$TotalAlloc,2)."</th>"."</STRONG>
            <th colspan='10'></th>
        </tr>";

echo '</table>';
include('includes/footer.inc');
?>
