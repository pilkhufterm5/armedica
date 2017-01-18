<?php
if(isset($_POST['ExportExcel'])){
    ob_start();
}
/* $Revision: 200 $ */

include('includes/SQL_CommonFunctions.inc');

//Jaime includes
include('XMLFacturacionElectronica/utils/Php.php');
//Termina Jaime includes

$PageSecurity = 1;

include('includes/session.inc');
if(isset($_POST['CustomerID']))$_POST['CustomerID']='-0-'; // Se quito '-0-' para dejar solo ' ' Angeles Perez 2016/01/29
$title = _('Customer Inquiry');
include('includes/header.inc');

/*
<script type="text/javascript" src="javascript/descargar/csvExporter.js"></script>
<script type="text/javascript" src="javascript/descargar/pdfExporter.js"></script>
<script type="text/javascript">
$(function(){
    $('csv, pdf').show();

})
</script>
<csv style="display:none" target="Transacciones(<?=date('Y-m-d')?>)" title=".ToExport"><button>Excel</button></csv>
<pdf style="display:none" target="Transacciones(<?=date('Y-m-d')?>)" title=".ToExport"><button>Pdf</button></pdf>
*/
?>
<!-- Peticion Ajax a Metodo de Afiliaciones Yii para envio de MailMasivo -->
<script type="text/javascript">
    $(document).on('ready', function(){

        $("#CheckAll").click(function(event) {
            if(this.checked){
                $('.SendEmail').attr('checked','checked')
            }else{
                $('.SendEmail').removeAttr('checked');
            }
        });

        $("#ProccessMailing").click(function(event){
            $.blockUI();
            var jqxhr = $.ajax({
                url: "<?=$rootpath?>/modulos/index.php?r=afiliaciones/SendInvoiceXML",
                type: "POST",
                dataType : "json",
                timeout : (120 * 100000),
                data: {
                    SendMail2:{
                        TransNo: $('.SendEmail').serialize(),
                        Tipo: 'InvoiceANDXML'
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
        $("#DownloadFiles").click(function(event){
            $.blockUI();
            var jqxhr = $.ajax({
                url: "<?=$rootpath?>/modulos/index.php?r=afiliaciones/ImpresionMasiva",
                type: "POST",
                dataType : "json",
                timeout : (120 * 100000),
                data: {
                    SendMail2:{
                        TransNo: $('.SendEmail').serialize(),
                        Tipo: 'GenerateZipPDF'
                    },
                },
                success : function(Response, newValue) {
                    $.unblockUI();
                    if (Response.requestresult == 'ok') {
                        displayNotify('successDownload', Response.message);
                        a=$('#ReDownloadFiles').closest('a');
                        a.attr('href',Response.url);
                        a.show();
                        window.location=Response.url;
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
$esconderContabilidad = false;

/*Recibimos Folio del Afiliado*/
if (isset($_REQUEST['CustomerID'])&&trim($_REQUEST['CustomerID'])!=''){
    $_SESSION['CustomerID'] = DB_escape_string($_REQUEST['CustomerID']);
    $CustomerID = $_SESSION['CustomerID'];

    $_2GetDebtorNo = "SELECT debtorno FROM rh_titular WHERE folio = '{$CustomerID}' ";
    $_2GetDebtorNo = DB_query($_2GetDebtorNo, $db);
    $GetDebtorNo = DB_fetch_assoc($_2GetDebtorNo);
    $CustomerID = $GetDebtorNo['debtorno'];
}else{
    $CustomerID = "%";
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
    $_POST['TransAfterDate'] = Date($_SESSION['DefaultDateFormat'],Mktime(0 ,0 ,0 ,Date('m'),Date('d'),Date('Y')));
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
     		AND debtorsmaster.debtorno LIKE '" . $CustomerID . "'
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
	     AND debtorsmaster.debtorno LIKE '" . $CustomerID . "'";

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
if ($NIL_BALANCE==True){
	$CustomerRecord['balance']=0;
	$CustomerRecord['due']=0;
	$CustomerRecord['overdue1']=0;
	$CustomerRecord['overdue2']=0;
}

 // Se agrego para el filtro por rango de facturas Angeles Perez 2016/01/29
     $FacturaDesde = $_POST['facturadesde'];
     $FacturaHasta = $_POST['facturahasta'];
// termina

    echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . "' METHOD=POST>";
        echo "<div style='height:30px;'></div>";
        echo _('AfilNo') . ": <INPUT type=text name='CustomerID' Value='" . $_POST['CustomerID'] . "' style='width:120px;' > &nbsp;&nbsp;";

// Se agrego para el filtro por rango de facturas Angeles Perez 2016/01/29  
        echo _('De Factura').":<input type=text name='facturadesde' Value='" . $_POST['facturadesde']. "' style='width:120px;' >&nbsp;&nbsp;";
        echo _('A Factura').":<input type=text name='facturahasta' Value='" . $_POST['facturahasta']. "' style='width:120px;' >&nbsp;&nbsp;";
// Termina

        echo _('Fecha Inicial') . ": <INPUT type=text name='TransAfterDate' Value='" . $_POST['TransAfterDate'] . "' style='width:120px;' >&nbsp;&nbsp;";
        echo _('Fecha Final') . ": <INPUT type=text name='TransBeforeDate' Value='" . $_POST['TransBeforeDate'] . "' style='width:120px;' > <br>";

        echo _('Tipo Documento') . ": <SELECT NAME='type'>
                <OPTION SELECTED VALUE='all'>"._('Show All')."</OPTION>";
                    $sql = "SELECT systypes.typeid, systypes.typename FROM systypes WHERE typeid IN(SELECT type FROM debtortrans GROUP BY type)";
                    $res = DB_query($sql,$db);
                    while($types = DB_fetch_array($res)){
                        if($_POST['type']==$types['typeid']){
                            echo "<OPTION VALUE=".$types['typeid']." SELECTED>".$types['typename']."</OPTION>";
                        }else{
                            echo "<OPTION VALUE=".$types['typeid'].">".$types['typename']."</OPTION>";
                        }
                    }
        echo "</SELECT>&nbsp;&nbsp;";

/*
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
*/

        echo 'Agrupar por documento <input name="bytype" id="bytype" type="checkbox" />&nbsp;&nbsp;';
        echo "<INPUT TYPE='SUBMIT' NAME='Refresh Inquiry' VALUE='" . _('Refresh Inquiry') . "' class='btn btn-success' > &nbsp;&nbsp;";
        echo "<INPUT TYPE='SUBMIT' NAME='ExportExcel' VALUE='" . _('Exportar a Excel') . "'    class='btn btn-success' > &nbsp;&nbsp;";
        echo "<INPUT TYPE='button' id='ProccessMailing' NAME='ProccessMailing' VALUE='" . _('Enviar Email') . "'    class='btn btn-danger' >
		<INPUT TYPE='button' id='DownloadFiles' NAME='DownloadFiles' VALUE='" . _('Zip Facturas') . "'    class='btn btn-success' >
	    <a style='display:none'><INPUT TYPE='button' id='ReDownloadFiles' NAME='ReDownloadFiles' VALUE='" . _('Descargar de nuevo') . "' class='btn btn-success' ></a>
    </FORM>";
    echo "<div style='height:20px;'></div>";

    $DateAfterCriteria = FormatDateForSQL($_POST['TransAfterDate']);
    // bowikaxu realhost nov 07
    $DateBeforeCriteria = FormatDateForSQL($_POST['TransBeforeDate']);

    // Se agrego para el filtro por rango de facturas Angeles Perez 2016/01/28
    $SQL="SELECT * FROM rh_cfd__cfd WHERE folio BETWEEN '$FacturaDesde' AND '$FacturaHasta'"; 
    $res = DB_query($sql,$db);
//Termina

    //Jaime (modificado) se altero el query para incluir una columna que nos dice si la factura es un CFD
    $SQL = "SELECT if(isnull(v.id_salesorders),if(isnull(cp.id_salesorders),systypes.typename,if(c.tipo_de_comprobante='ingreso','Carta Porte Ingreso', 'Carta Porte Traslado')),'Transportista') typename,
    		debtortrans.debtorno,
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
    		$SUM ((debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount)/debtortrans.rate) $SUMC AS totalamount,
    		debtortrans.alloc/debtortrans.rate AS allocated,
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
    	AND debtortrans.debtorno LIKE '" . $CustomerID . "'
    	AND (debtortrans.trandate >= '$DateAfterCriteria 00:00:00' and debtortrans.trandate <= '$DateBeforeCriteria 23:59:59' )";
        // $BYTYPE   ORDER BY  debtortrans.trandate, debtortrans.transno   "; se paso a la linea 368 despues y en la linea anterior se agrego " ;

        // Se agrego para el filtro por rango de Facturas Daniel Villarreal 2016/01/28
        if($FacturaDesde!=NULL)
        {
            $SQL .= " AND c.folio >= ".$FacturaDesde;
        }
         if($FacturaHasta!=NULL)
        {
            $SQL .= " AND c.folio <= ".$FacturaHasta;
        }
        
        $SQL .= " $BYTYPE   ORDER BY  debtortrans.trandate, debtortrans.transno   ";
// Termina
    	

        //Termina Jaime (modificado) se altero el query para incluir una columna que nos dice si la factura es un CFD
        $ErrMsg = _('No transactions were returned by the SQL because');
        $TransResult = DB_query($SQL,$db,$ErrMsg);

        if (DB_num_rows($TransResult)==0){
            echo _('There are no transactions to display since') . ' ' . $_POST['TransAfterDate'];
            include('includes/footer.inc');
            exit;
        }
        /*show a table of the invoices returned by the SQL */


         /*Obtengo Datos del Afiliado*/
    if(isset($_POST['ExportExcel'])){
        ob_clean();
        ob_start();
    }

    echo '<TABLE class="ToExport table table-striped table-bordered">';

    $tableheader = "
    <thead>
        <tr class='tableheader'>
            <th class='tableheader'>" . _('Código') . "</th>
            <th class='tableheader'>" . _('No. Afiliado') . "</th>
            <th class='tableheader'>" . _('Type') . "</th>
            <th class='tableheader'>" . _('Number') . "</th>
            <th class='tableheader'>" . _('Factura') . "</th>
            <th class='tableheader' style='width:150px;'>" . _('Factura/Deposito') . "</th>
            <th class='tableheader'>" . _('Date') . "</th>
            <th class='tableheader'>" . _('Branch') . "</th>
            <th class='tableheader'>" . _('Reference') . "</th>
            <th class='tableheader'>" . _('Comments') . "</th>
            <th class='tableheader'>" . _('Order') . "</th>
            <th class='tableheader'>" . _('Total') . "</th>
            <th class='tableheader'>" . _('Allocated') . "</th>
            <th class='tableheader'>" . _('Balance') . "</th>
            <th colspan='9' style='width:350px;' >Enviar por Correo &nbsp;<input type='checkbox' id='CheckAll' title='Seleccionar Todo'></th>
        </tr>
    </thead>";

    echo $tableheader;

    $Total = 0;
    $TotalAlloc = 0;
    $j = 1;
    $k=0; //row colour counter
    while ($myrow=DB_fetch_array($TransResult)) {
		if(!$myrow['is_cfd']&&$myrow['type']!=10){
			$myrow['is_cfd']=true;
		}
        $_2GetAfilData = "SELECT ti.folio, cobranza.cobrador, fa.tipo_membresia
                           FROM rh_titular ti
                           LEFT JOIN rh_cobranza cobranza ON cobranza.folio = ti.folio
                           LEFT JOIN rh_foliosasignados fa ON ti.folio = fa.folio
                           WHERE ti.debtorno = '{$myrow['debtorno']}'";
        $_GetAfilData=DB_query($_2GetAfilData,$db);
        $GetAfilData = DB_fetch_assoc($_GetAfilData);

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
            echo "<tr class=\"danger\" bgcolor='red'>";
        }else if (($myrow['type']==10 || $myrow['type']==11 || $myrow['type']==20001 || ($myrow['type']==20002)) && $myrow['rh_status']=='C'){
            echo "<tr class=\"danger\" bgcolor='red'>";
        }else if ($myrow['type']==11 && $myrow['rh_status']=='R'){ // nota de credito cancela remision
            echo "<tr class=\"warning\" bgcolor='orange'>";
        }else if ($myrow['type']==11 && $myrow['rh_status']=='F'){ // nota de credito cancela factura
            echo "<tr class=\"warning\" bgcolor='orange'>";
        }else {
            if ($k==1){
                echo "<tr bgcolor='#CCCCCC'>";
                $k=0;
            } else {
                echo "<tr bgcolor='#EEEEEE'>";
                $k=1;
            }
        }

        /****************AGREGADO QUE MUESTRE LAS FACTURAS QUE PAGO UN DEPOSITO ***************************/
        $FormatedTranDate = ConvertSQLDate($myrow['trandate']);
        $FolioFactura = "";
        $sql="select cfdi.folio, cfdi.serie,dt.transno from custallocns cal
            left join debtortrans dt on
            (dt.id=cal.transid_allocfrom or dt.id=cal.transid_allocto)
            and dt.id != {$myrow['id']}
            left join rh_cfd__cfd cfdi on dt.id = cfdi.id_debtortrans
            where
            (cal.transid_allocfrom={$myrow['id']} or cal.transid_allocto={$myrow['id']})
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
            <td>%s</td>
            <td>%s</td>
            <td>{$FolioFactura}</td>
            <td>%s</td>
            <td width='200'>%s</td>
            <td>%s</td>
            <td ALIGN=RIGHT>%s</td>
            <td ALIGN=RIGHT>%s</td>
            <td ALIGN=RIGHT>%s</td>
            <td ALIGN=RIGHT>%s</td>
            <td ALIGN=RIGHT>%s</td><td style='width:350px;' >";

        if($myrow['rh_status']=='C'){ // factura cancelada
            $credit_invoice_str = "
                <div class='icon' tag='%s%s%s'></div>
                <div><IMG SRC='%s/cancel_disable.gif' TITLE='" . _('Factura Cancelada') . "'></div>
                ";

            $credit_invoice_str2 = "

                    <a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the invoiceNC') . "'></a>
                ";

            //Notas de Cargo iJPe
            $credit_cargo_str = "

                    <a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the invoiceNC') . "'></a>
                ";

            $credit_cargo_str2 = "

                    <a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the invoiceNC') . "'></a>
                ";

            // remisiones
            // bowikaxu shipments links to preview and credit
            $credit_shipment_str = "

                    <a href='%s/rh_Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the shipment') . "'></a>
                    <IMG SRC='%s/cancel_disable.gif' TITLE='" . _('Click para cancelar remision') . "'>
                ";

            $credit_shipment_str2 = "

                    <a href='%s/rh_Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the shipment') . "'></a>
                    <IMG SRC='%s/cancel_disable.gif' TITLE='" . _('Click para cancelar remision') . "'>
                ";


            //iJPe 2010-03-23 Impresion de Remision
            $preview_shipment_str = "
                <a target='_blank' href='".$rootpath."/rh_PDFRemGde.php?&FromTransNo={$myrow['transno']}&InvOrCredit=Invoice'><IMG SRC='".$rootpath."/css/professional/images/preview.gif' TITLE='" . _('Click to preview the shipment') . "'></a>
                <a target='_blank' href='".$rootpath."/rh_PDFRemGde.php?&FromTransNo={$myrow['transno']}&InvOrCredit=Invoice&PrintPDF=True'><IMG SRC='".$rootpath."/css/professional/images/reports.png' TITLE='" . _('Imprimir Copia de Remision') . "'></a>
                <a target='_blank' href='rh_EmailCustTrans.php?FromTransNo={$myrow['transno']}&InvOrCredit=Invoice'><IMG SRC='".$rootpath."/css/professional/images/email.gif' TITLE='" . _('Click to email the shipment') . "'></a>";
        }else { // factura no cancelada
            //Jaime, si es carta porte se modifica el href para que apunte al script correcto
            $credit_invoice_str = "
                <div class='icon' tag='%s%s%s'>
                    <a href='" . (($myrow['is_carta_porte']?'rh_cartaPorte_Cancel_Invoice.php?InvoiceNumber='. $myrow['transno']:'rh_Cancel_Invoice.php?InvoiceNumber=' . $myrow['transno'])) . "'>
                        <IMG SRC='".$rootpath."/css/silverwolf/images/cancel.gif' TITLE='" . _('Click para cancelar factura') . "'>
                    </a>&nbsp;
                ";
            //\Jaime, si es carta porte se modifica el href para que apunte al script correcto
            $credit_invoice_str2 = "
                    <a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the invoiceNC') . "'></a>
                ";
                /*$credit_invoice_str2 = "<td tag='%s%s%s'>
                <a href='%s/" . (($myrow['is_carta_porte']?'rh_cartaPorte_Cancel_Invoice.php?InvoiceNumber=%s':'rh_Cancel_Invoice.php?InvoiceNumber=%s')) . "'><IMG SRC='%s/cancel.gif' TITLE='" . _('Click para cancelar factura') . "'></a>
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

            $credit_cargo_str2 = "
                <a href='%s/Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Ver nota de cargo') . "'></a>";


            /* CANCEL CREDIT NOTE */
            $cancel_credit = "
                <a href='%s/Z_DeleteCreditNote.php?CreditNoteNo=%s&CustInq=1'><IMG SRC='".$rootpath."/css/silverwolf/images/cancel.gif' TITLE='" . _('Clic para cancelar nota de credito') . "'></a>";


            /* REMISIONES */
            $credit_shipment_str = "<a href='%s/rh_Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the shipment') . "'></a>
                <a href='%s/rh_Cancel_Remision.php?InvoiceNumber=%s'><IMG SRC='%s/cancel.gif' TITLE='" . _('Click para cancelar remision') . "'></a>&nbsp;";

            $credit_shipment_str2 = "
                <a href='%s/rh_Credit_Invoice.php?InvoiceNumber=%s'><IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the shipment') . "'></a>
                <IMG SRC='%s/cancel.gif' TITLE='" . _('Click para cancelar remision') . "'>";

            //iJPe 2010-03-23 Impresion de Remision
            $preview_shipment_str = "<a target='_blank' href='%s/rh_PDFRemGde.php?&FromTransNo=%s&InvOrCredit=Invoice'><IMG SRC='%s/preview.gif' TITLE='" . _('Click to preview the shipment') . "'></a>
                <a target='_blank' href='rh_EmailCustTrans.php?FromTransNo={$myrow['transno']}&InvOrCredit=Invoice'><IMG SRC='".$rootpath."/css/professional/images/email.gif' TITLE='" . _('Click to email the shipment') . "'></a>
                <a target='_blank' href='%s/rh_PDFRemGde.php?&FromTransNo=%s&InvOrCredit=Invoice&PrintPDF=True'><IMG SRC='%s/reports.png' TITLE='" . _('Imprimir Copia de Remision') . "'></a>";
            /*REMISIONES*/
    }
            //Jaime (modificado) se agrego el atributo isCfd que permite saber si la factura es un CFD
            //Jaime (remodificado) se dejo como estaba antes y se le agrego la linea 423
            //Jaime (remodificado) se agrego el campo isCfdCancelado para que la impresion aparesca como CANCELADA
            //$preview_invoice_str = "<a target='_blank' href=" . (!$myrow['is_cfd']?("'%s/rh_PrintCustTrans.php?FromTransNo=%s&InvOrCredit=Invoice'"):("'%s/PHPJasperXML/sample1.php?transno=%s'")) . "><IMG SRC='%s/preview.gif' TITLE='" . _('Click to preview the invoice') . "'></a>
            $preview_invoice_str = "<a href='".$rootpath."/Credit_Invoice.php?InvoiceNumber=".$myrow['transno']."'><IMG SRC='".$rootpath.'/css/'.$theme."/images/credit.gif' TITLE='" . _('Click to credit the invoiceNC') . "'></a>
                                    <a target='_blank' href='rh_PrintCustTrans.php?idDebtortrans=".$myrow['id']."&FromTransNo={$myrow['transno']}&InvOrCredit=Invoice&isCfd=" . $myrow['is_cfd'] . "'><IMG SRC='".$rootpath."/css/professional/images/preview.gif' TITLE='" . _('Click to preview the invoice') . "'></a>&nbsp;".
                (strlen($myrow['uuid'])>0?
                                    ($myrow['is_cfd']?"<a target='_blank' href='EmailCustTrans_CFDI.php?FromTransNo={$myrow['transno']}&InvOrCredit=Invoice&isCfd=" . $myrow['is_cfd'] . ($myrow['is_carta_porte']?'&isCartaPorte=true':'') . ($myrow['rh_status']=='C'?'&isCfdCancelado=true':'') . "'><IMG SRC='".$rootpath."/css/professional/images/email.gif' TITLE='" . _('Click to email the invoice') . "'></a>":""):
                                    ($myrow['is_cfd']?"<a target='_blank' href='EmailCustTrans.php?FromTransNo={$myrow['transno']}&InvOrCredit=Invoice&isCfd=" . $myrow['is_cfd'] . ($myrow['is_carta_porte']?'&isCartaPorte=true':'') . ($myrow['rh_status']=='C'?'&isCfdCancelado=true':'') . "'><IMG SRC='".$rootpath."/css/professional/images/email.gif' TITLE='" . _('Click to email the invoice') . "'></a>":"")
                                ).

                                (strlen($myrow['uuid'])>0?
                                    ($myrow['is_cfd']?"<a target='_blank' href='rh_j_downloadFacturaElectronicaXML_CFDI.php?downloadPath=" . ('XMLFacturacionElectronica/xmlbycfdi/' . $myrow['uuid']. '.xml') . "'><IMG SRC='$rootpath/images/xml.gif' TITLE='" . _('Click to download the invoice') . _(' (XML)') . "'></a>":""):
                                    ($myrow['is_cfd']?"<a target='_blank' href='rh_j_downloadFacturaElectronicaXML.php?downloadPath=" . ('XMLFacturacionElectronica/facturasElectronicas/' . $myrow['no_certificado'] . '/' . $myrow['serie'] . $myrow['folio'] . '-' . $myrow['fk_transno'] . '.xml') . "'><IMG SRC='$rootpath/images/xml.gif' TITLE='" . _('Click to download the invoice') . _(' (XML)') . "'></a>":"")
                                )./*Factura N*/
                                ($myrow['is_cfd']&&$myrow['rh_status']!='C'?"<input type='checkbox' name='SendEmail[]' value='{$myrow['transno']}' title='Envia por Correo' class='SendEmail'>":" ").
                                ($myrow['is_cfd']?" <a target='_blank' href='$rootpath/PHPJasperXML/sample1.php?isTransportista=" . $myrow['is_transportista'] . "&transno=" . $myrow['transno'] . '&' . ($myrow['is_carta_porte']?'isCartaPorte=true':'') . ($myrow['rh_status']=='C'?'&isCfdCancelado=true':'') . "&afil=true'><IMG SRC='$rootpath/css/silverwolf/images/pdf.gif' TITLE='" . _('Click to preview the invoice') . _(' (PDF)') . "'></a>":"").
                                /* COPIA PDF */
                                ($myrow['is_cfd']?"<a target='_blank' href='$rootpath/PHPJasperXML/sample1.php?isTransportista=" . $myrow['is_transportista'] . "&copia=si&transno=" . $myrow['transno'] . '&' . ($myrow['is_carta_porte']?'isCartaPorte=true':'') . ($myrow['rh_status']=='C'?'&isCfdCancelado=true':'') . "&afil=true'><IMG SRC='$rootpath/css/silverwolf/images/pdf_copia.png' TITLE='" . _('Click para visualizar la Copia de la factura') . _(' (PDF)') . "'></a>":"").
                                (!$myrow['is_cfd']?"<a target='_blank' href='rh_recoverxml.php?id=" . $myrow['id']. "'><IMG SRC='$rootpath/recover.png' TITLE='" . _('Recuperar Factura') . "'></a>":"");
                                //termina jaime agregado
            //\Jaime (remodificado) se agrego el campo isCfdCancelado para que la impresion aparesca como CANCELADA
            //Termina Jaime (remodificado) se dejo como estaba antes y se le agrego el <td> para visualizar la factura en PDF
            //Termina Jaime (modificado) se agrego el atributo isCfd que permite saber si la factura es un CFD
            //Jaime (modificado) agregado un <td></td> al final
            $preview_credit_str = "<a target='_blank' href='rh_PrintCustTrans.php?idDebtortrans=".$myrow['id']."&FromTransNo={$myrow['transno']}&InvOrCredit=Credit'><IMG SRC='".$rootpath."/css/professional/images/preview.gif' TITLE='" . _('Click to preview the credit note') . "'></a>&nbsp;
				".(strlen($myrow['uuid'])>0?
                                    ($myrow['is_cfd']?"<a target='_blank' href='EmailCustTrans_CFDI.php?FromTransNo={$myrow['transno']}&InvOrCredit=Credit'><IMG SRC='".$rootpath."/css/professional/images/email.gif' TITLE='" . _('Click to email the credit note') . "'></a>":""):
                                    ($myrow['is_cfd']?"<a target='_blank' href='EmailCustTrans.php?FromTransNo={$myrow['transno']}&InvOrCredit=Credit'><IMG SRC='".$rootpath."/css/professional/images/email.gif' TITLE='" . _('Click to email the credit note') . "'></a>":"")
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
            $preview_shipment_str = str_replace(array("<td>","</td>"), "&nbsp;", $preview_shipment_str);
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
                            "<A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ></A></td>
                            </tr>",
                            $myrow['debtorno'],
                            $GetAfilData['folio'],
                            $myrow['typename'],
                            //$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
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
                            "<A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ></A></td>
                            </tr>",
                            $myrow['debtorno'],
                            $GetAfilData['folio'],
                            $myrow['typename'],
                            //$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
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
                            "<A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ></A></td>
                            </tr>",
                            $myrow['debtorno'],
                            $GetAfilData['folio'],
                            $myrow['typename'],
                            //$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
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
                    } else {
                        printf($base_formatstr .
                            $credit_invoice_str .
                            $credit_cargo_str.
                            $preview_invoice_str .
                            "<A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ></A></td>
                            </tr>",
                            $myrow['debtorno'],
                            $GetAfilData['folio'],
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
                        '</tr>',
                        $myrow['debtorno'],
                        $GetAfilData['folio'],
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
                        '</tr>',
                        $myrow['debtorno'],
                        $GetAfilData['folio'],
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
                        "<A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ></A></td>
                        </tr>",
                        $myrow['debtorno'],
                        $GetAfilData['folio'],
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
                        "<A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ></A></td>
                        </tr>",
                        $myrow['debtorno'],
                        $GetAfilData['folio'],
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
                        '</tr>',
                        $myrow['debtorno'],
                        $GetAfilData['folio'],
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
                        '</tr>',
                        $myrow['debtorno'],
                        $GetAfilData['folio'],
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
                    "<A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ></A></td>
                    </tr>",
                    $myrow['debtorno'],
                    $GetAfilData['folio'],
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
                    "<A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ></A></td>
                    </tr>",
                    $myrow['debtorno'],
                    $GetAfilData['folio'],
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
                    "<A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ></A></td>
                    </tr>",
                    $myrow['debtorno'],
                    $GetAfilData['folio'],
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
                    "<A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ></td>
                    </tr>",
                    $myrow['debtorno'],
                    $GetAfilData['folio'],
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
                    '</tr>',
                    $myrow['debtorno'],
                    $GetAfilData['folio'],
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
                    '</tr>',
                    $myrow['debtorno'],
                    $GetAfilData['folio'],
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
                '</tr>',
                $myrow['debtorno'],
                $GetAfilData['folio'],
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
            /**
             * Obtiene los Datos de la Factura
            */

            // $GetInvoiceData = "SELECT c.serie, c.folio FROM
            // custallocns ca1
            // LEFT JOIN rh_cfd__cfd c ON c.id_debtortrans=ca1.transid_allocto
            // WHERE c.id_systypes=10 AND ca1.transid_allocfrom = '{$myrow['id']}' ";
            // $ResultData1 = DB_query($GetInvoiceData, $db);
            // $_2GetInvoiceData = DB_fetch_assoc($ResultData1);
            // echo "<br><br>" . $_2GetInvoiceData['serie'] . $_2GetInvoiceData['folio'];

            if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND (in_array(8,$_SESSION['AllowedPageSecurityTokens'])||in_array(1,$_SESSION['AllowedPageSecurityTokens']))){
                //SAINTS series y folios de FE 28/01/2011
                if($myrow['folio']!=""){
                    printf($base_formatstr .
                        $cancel_credit.
                        $preview_credit_str .
                        "<a href='CustomerAllocations.php?AllocTrans=" . $myrow['transno'] . "'><IMG SRC='".$rootpath."/css/professional/images/conta.png' TITLE='" . _('Click to allocate funds') . "'></a>&nbsp;
                        <A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' >".
                        '</td></tr>',
                        $myrow['debtorno'],
                        $GetAfilData['folio'],
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
                        "<a href='CustomerAllocations.php?AllocTrans=" . $myrow['transno'] . "'><IMG SRC='".$rootpath."/css/professional/images/allocation.gif' TITLE='" . _('Click to allocate funds') . "'></a>
                        <A ".($esconderContabilidad?'style="display:none"':'')." HREF='$rootpath/GLTransInquiry.php?".SID."&TypeID=".$myrow['type']."&TransNo=".$myrow['transno']."'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' >"
                        . '</td></tr>',
                        $myrow['debtorno'],
                        $GetAfilData['folio'],
                        $myrow['typename'],
                        // $myrow['transno'].'('.$ext['extcn'].')',
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
                    "ok1<a href='CustomerAllocations.php?AllocTrans=" . $myrow['transno'] . "'><IMG SRC='".$rootpath."/css/professional/images/allocation.gif' TITLE='" . _('Click to allocate funds') . "'></a></td>
                    </tr>",
                    $myrow['debtorno'],
                    $GetAfilData['folio'],
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
                        "<a href='CustomerAllocations.php?AllocTrans=" . $myrow['transno'] . "'><img title='" . _('Allocation') . "' src='{$rootpath}/css/silverwolf/images/asignacion.png' ></a> &nbsp;
                        <A ".($esconderContabilidad?'style="display:none"':'')." HREF='GLTransInquiry.php?&TypeID=" . $myrow['type'] . "&TransNo=" . $myrow['transno'] . "'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ><A> &nbsp;
                        <a href='rh_reverseo.php?TypeID=". $myrow['type'] ."&TransNo=" . $myrow['transno'] . "&CustomerID=" . $myrow['debtorno'] . "&TransID=".$myrow['id']."'><IMG SRC='{$rootpath}/css/silverwolf/images/credit.gif' TITLE='" . _('Click to reverse') . "'></a> &nbsp;
                        </td>
                        </tr>",
                        $myrow['debtorno'],
                        $GetAfilData['folio'],
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
                    "<td><A ".($esconderContabilidad?'style="display:none"':'')." HREF='GLTransInquiry.php?&TypeID=" . $myrow['type'] . "&TransNo=" . $myrow['transno'] . "'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ></td></tr>",
                    $myrow['debtorno'],
                    $GetAfilData['folio'],
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
            } else { /* ASIGNADO 0*/
                $sqlrh = "SELECT * FROM rh_reverseo WHERE transid_reversfrom = ".$myrow['id']." AND type = ".$myrow['type']."";
                $resultrh = DB_query($sqlrh,$db);
                if (DB_num_rows($resultrh)==0){
                    printf($base_formatstr .
                    "<a href='CustomerAllocations.php?AllocTrans=%s'><IMG SRC='".$rootpath.'/css/'.$theme.'/images'. "/conta.png' TITLE='" . _('Allocation') . "'></a></td>
                    </tr>",
                    $myrow['debtorno'],
                    $GetAfilData['folio'],
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
                    "</tr>",
                    $myrow['debtorno'],
                    $GetAfilData['folio'],
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
                    "<td><A ".($esconderContabilidad?'style="display:none"':'')." HREF='GLTransInquiry.php?&TypeID=" . $myrow['type'] . "&TransNo=" . $myrow['transno'] . "'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ></A></td></tr>",
                    $myrow['debtorno'],
                    $GetAfilData['folio'],
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
                printf($base_formatstr . '</td></tr>ñoooooooo6',
                    $myrow['debtorno'],
                    $GetAfilData['folio'],
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
                    "<td><A ".($esconderContabilidad?'style="display:none"':'')." HREF='GLTransInquiry.php?&TypeID=" . $myrow['type'] . "&TransNo=" . $myrow['transno'] . "'><img title='" . _('GL') . "' src='{$rootpath}/css/silverwolf/images/conta.png' ></A></td></tr>",
                    $myrow['debtorno'],
                    $GetAfilData['folio'],
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
                    $myrow['debtorno'],
                    $GetAfilData['folio'],
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
            <th colspan=9 class=tableheader align=right><STRONG>"._('Total').'</th>
            <th class=tableheader>'.number_format($Total,2)."</th>
            <th class=tableheader>".number_format($TotalAlloc,2)."</th>"."
            <th class=tableheader>".number_format($Total-$TotalAlloc,2)."</th>"."</STRONG>
            <th colspan='10'></th>
        </tr>";

echo '</table>';


if(isset($_POST['ExportExcel'])){
    $htmlToExport = ob_get_contents();
    ob_clean();

    header("Content-type: application/octet-stream");
    header("Content-Disposition: filename=CustomerInquiry.xls");
    header("Content-length: " . strlen($htmlToExport));
    header("Cache-control: private");
    echo $htmlToExport;
    exit();
}


include('includes/footer.inc');
?>
