<?php

/* webERP Revision: 14 $ */

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-15 13:58:30 -0600 (Fri, 15 Feb 2008) $
 * $Rev: 51 $
 */

/*The supplier transaction uses the SuppTrans class to hold the information about the invoice
the SuppTrans class contains an array of GRNs objects - containing details of GRNs for invoicing
Also an array of GLCodes objects - only used if the AP - GL link is effective
Also an array of shipment charges for charges to shipments to be apportioned accross the cost of stock items */

$PageSecurity = 5;

include('includes/DefineSuppTransClass.php');
/* Session started in header.inc for password checking and authorisation level check */
include('includes/session.inc');

$title = _('Ver Factura del Proveedor');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

echo "<A HREF='" . $rootpath . '/SelectSupplier.php?' . SID . "'>" . _('Back to Suppliers') . '</A><BR>';

if (isset($_GET['Transno'])){

	/*Now retrieve supplier information - name, currency, default ex rate, terms, tax rate etc */

	// bowikaxu March 2007 - comenzar a desplegar informacion

	$SQL = "SELECT supptrans.transno,
			supptrans.suppreference,
			supptrans.supplierno,
			supptrans.trandate,
			supptrans.duedate,
			supptrans.rate,
			supptrans.rh_invdate,
			supptrans.transtext,
			supptrans.ovamount,
			supptrans.ovgst,
			supptrans.alloc,
			suppliers.suppname,
			suppliers.currcode,
			paymentterms.terms,
			taxgroups.taxgroupdescription
			FROM supptrans, suppliers, paymentterms, taxgroups WHERE
			supptrans.transno = ".$_GET['Transno']."
			AND supptrans.type = 20
			AND suppliers.paymentterms=paymentterms.termsindicator
			AND suppliers.taxgroupid=taxgroups.taxgroupid
			AND suppliers.supplierid = supptrans.supplierno";

	$TransRes = DB_query($SQL,$db);

	$Trans = DB_fetch_array($TransRes);

	$sql = "SELECT suppallocs.amt,
			suppallocs.datealloc,
			suppallocs.transid_allocfrom,
			 supptrans.* 
			 FROM suppallocs,supptrans 
			 WHERE
			supptrans.transno = ".$_GET['Transno']."
			AND supptrans.id = suppallocs.transid_allocto";

	$PayRes = DB_query($sql,$db);


	echo "<CENTER>
	<H2><STRONG>"._('Supplier Invoice').' '.$Trans['transno']."</STRONG></H2>
	<TABLE CELLPADDING=4 CELLSPACING=3 width=80% BORDER=1>";
	echo "<TR>
			<TD class='tableheader'><B>"._('Supplier').':</TD><TD>'.$Trans['suppname']."</B></TD>
			<TD class='tableheader'>"._('Reference').": </TD><TD>".$Trans['transno']."</TD>
			<TD class='tableheader'>"._('Supplier').' '._('Reference').": </TD><TD>".$Trans['suppreference']."</TD>
		</TR>";

	echo "<TR>
			<TD class='tableheader'><B>"._('Invoice').' '._('Date').':</TD><TD>'.$Trans['trandate']."</B></TD>
			<TD class='tableheader'><B>"._('Due Date').':</TD><TD>'.$Trans['duedate']."</B></TD>
			<TD class='tableheader'>"._('Currency').": </TD><TD>".$Trans['currcode']."</TD>
		</TR>";
	if(strlen($Trans['transtext'])){
	echo "<TR COLSPAN=3>
			<TD><B>".$Trans['transtext']."</B></TD>
		</TR>";
	}
	
	echo "</TABLE></CENTER>";

	// PAYMENTS
	// bowikaxu - show payments related to this invoice
	$SQL= "SELECT supptrans.supplierno,
		supptrans.suppreference,
		supptrans.trandate,
		supptrans.alloc
	FROM supptrans
	WHERE supptrans.id IN (SELECT suppallocs.transid_allocfrom
				FROM supptrans, suppallocs
				WHERE supptrans.supplierno = '".$Trans['supplierno']."'
				AND supptrans.suppreference = '".$Trans['suppreference']."'
				AND supptrans.id = suppallocs.transid_allocto)";
	$Result = DB_query($SQL, $db);
	if(DB_num_rows($Result)>0){
		echo '<CENTER>
	<H3><STRONG>'._('Payment').' '._('Reference').'</STRONG></H3><BR>
	<TABLE CELLPADDING=4 CELLSPACING=3 width=80% BORDER=0>';
		$TableHeader = "<TR>
<TD CLASS='tableheader'>" . _('Supplier Number') . '<BR>' . _('Reference') . "</TD>
<TD CLASS='tableheader'>" . _('Payment') .'<BR>' . _('Reference') . "</TD>
<TD CLASS='tableheader'>" . _('Payment') . '<BR>' . _('Date') . "</TD>
<TD CLASS='tableheader'>" . _('Total Payment') . '<BR>' . _('Amount') .	'</TD></TR>';

		echo $TableHeader;
	}
	$j=1;
	$k=0; //row colour counter
	while ($myrow = DB_fetch_array($Result)) {
		if ($k == 1){
			echo '<TR BGCOLOR="#CCCCCC">';
			$k = 0;
		} else {
			echo '<TR BGCOLOR="#EEEEEE">';
			$k++;
		}

		printf('<TD>%s</TD>
		<TD>%s</TD>
		<TD>%s</TD>
		<TD>%s</TD>
		</TR>',
		$myrow['supplierno'],
		$myrow['suppreference'],
		ConvertSQLDate($myrow['trandate']),
		$myrow['alloc']	);

		$j++;
		If ($j == 18){
			$j=1;
			echo $TableHeader;
		}

	}
	if(DB_num_rows($Result)>0){
		echo '</TABLE></CENTER>';
	}
	// INVOICE DETAILS (by purchase order)
	$SQL = "SELECT rh_suppinvdetails.*,ovamount as monto, ovgst
			FROM rh_suppinvdetails, supptrans
			WHERE  supptrans.type=20 and supptrans.transno=rh_suppinvdetails.transno
			AND rh_suppinvdetails.transno = '".$Trans['transno']."'
			AND rh_suppinvdetails.itemcode != ''";

	$Result = DB_query($SQL, $db);
	if(DB_num_rows($Result)>0){
		echo '<CENTER>
	<H3><STRONG>'._('Invoice').' '._('Detailed').'</STRONG></H3>
	<TABLE CELLPADDING=4 CELLSPACING=3 width=80% BORDER=0>';
		$TableHeader = "<TR>
<TD CLASS='tableheader'>" . _('Item Code') . "</TD>
<TD CLASS='tableheader'>" . _('Description'). "</TD>
<TD CLASS='tableheader'>" . _('Quantity') . "</TD>
<TD CLASS='tableheader'>" . _('Price') . "</TD>
<TD CLASS='tableheader'>" . _('Total') . '<BR>' . _('Amount') .	'</TD></TR>';

		echo $TableHeader;
	}

	$rh_subtotal = 0;
	$rh_ovgst = 0;
	
	$j=1;
	$k=0; //row colour counter
	while ($myrow = DB_fetch_array($Result)) {
		if ($k == 1){
			echo '<TR BGCOLOR="#CCCCCC">';
			$k = 0;
		} else {
			echo '<TR BGCOLOR="#EEEEEE">';
			$k++;
		}

		printf('<TD>%s</TD>
		<TD>%s</TD>
		<TD ALIGN=RIGHT>%s</TD>
		<TD ALIGN=RIGHT>%s</TD>
		<TD ALIGN=RIGHT>%s</TD>
		</TR>',
		$myrow['itemcode'],
		$myrow['itemdescription'],
		number_format($myrow['qty'],2),
		number_format($myrow['stdcostunit'],2),
		number_format($myrow['qty']*$myrow['stdcostunit'],2));
		
		$rh_ovgst = $myrow['ovgst'];
		$rh_subtotal = $myrow['monto'];

		$j++;
		If ($j == 18){
			$j=1;
			echo $TableHeader;
		}

	}
	
	if(DB_num_rows($Result)>0){
	
	$TableHeader = "<TR>
	<TD CLASS='tableheader'></TD>
	<TD CLASS='tableheader'></TD>
	<TD CLASS='tableheader'></TD>
	<TD CLASS='tableheader'>Subtotal</TD>
	<TD CLASS='tableheader' ALIGN=RIGHT>" .number_format($rh_subtotal,2) .	'</TD></TR>';

		echo $TableHeader;
	$TableHeader = "<TR>
	<TD CLASS='tableheader'></TD>
	<TD CLASS='tableheader'></TD>
	<TD CLASS='tableheader'></TD>
	<TD CLASS='tableheader'>Impuestos</TD>
	<TD CLASS='tableheader' ALIGN=RIGHT>" .number_format($rh_ovgst,2) .	'</TD></TR>';

		echo $TableHeader;
		
	$TableHeader = "<TR>
	<TD CLASS='tableheader'></TD>
	<TD CLASS='tableheader'></TD>
	<TD CLASS='tableheader'></TD>
	<TD CLASS='tableheader'>Total</TD>
	<TD CLASS='tableheader' ALIGN=RIGHT>" .number_format($rh_ovgst+$rh_subtotal,2) .	'</TD></TR>';

		echo $TableHeader;
		echo '</TABLE></CENTER>';
	}
	
	// GLTRANS
	$SQL = "SELECT trandate,
		account,
		type,
		typeno,
		periodno,
		accountcode,
		accountname,
		narrative,
		amount,
		posted
	FROM gltrans INNER JOIN chartmaster
	ON gltrans.account = chartmaster.accountcode
	WHERE gltrans.type= 20
	AND gltrans.typeno = " . $Trans['transno'] . "
	ORDER BY counterindex";
	$Result = DB_query($SQL, $db);
	
	$TableHeader = '<TR><TD class="tableheader">' . _('Account Number') . '</TD>
			<TD class="tableheader">' . _('Account') .'</TD>
			<TD class="tableheader">' . _('Narrative') .'</TD>
			<TD class="tableheader">'. _('Debit') .'</TD>
			<TD class="tableheader">'. _('Credit') .'</TD>
			<TD class="tableheader">'. _('Posted') . '</TD></TR>';

	//echo $TableHeader;

	$j = 1;
	$i=0;
	$k=0; //row colour counter

	$TotDebit = 0;
	$TotCredit = 0;

	while ($myrow=DB_fetch_array($Result)) {
		if($i==0){
			// print header
			// supplier
			echo "
			<CENTER>
			<H2><STRONG>"._('GL')."</STRONG></H2>
			<TABLE CELLPADDING=4 CELLSPACING=3 width=80%>
			<TR><TD class='tableheader'>"._('General Ledger Transaction Details')."</td>
			<TD class='tableheader'>"._('Date')."</td>
			<TD class='tableheader'>"._('Period')."</td>
			<TD class='tableheader'>"._('Supplier Name')."</td></tr>
			<TR><TD>".-('Invoice').' ' . $Trans['transno']."</TD>
			<TD>".$myrow['trandate']."</TD>
			<TD>".$myrow['periodno']."</TD>
			<TD>".$Trans['suppname'].' ['.$Trans['supplierno'].']'."</TD></TR>
			</TABLE><BR>";
			
			echo "<TABLE CELLPADDING=3 CELLSPACING=2 width=80%>";
			echo $TableHeader;
			$i++;
		}
		
		if ($k==1){
			echo '<tr bgcolor="#CCCCCC">';
			$k=0;
		} else {
			echo '<tr bgcolor="#EEEEEE">';
			$k++;
		}

		if ($myrow['posted']==0){
			$Posted = _('No');
		} else {
			$Posted = _('Yes');
		}

		if($myrow['amount']>0){
			$Debit = number_format($myrow['amount'],2);
			$Credit = '';
			$TotDebit += $myrow['amount'];
		}else {
			$Credit = number_format(-$myrow['amount'],2);
			$Debit = '';
			$TotCredit += $myrow['amount'];
		}

		$FormatedTranDate = ConvertSQLDate($myrow["trandate"]);
		printf('<td ALIGN=LEFT><a href=GLAccountInquiry.php?%s&Account=%s>%s</a></td>
       		<td ALIGN=LEFT>%s</td>
       		<td ALIGN=LEFT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=LEFT>%s</td>
		</tr>',
		SID,
		$myrow['accountcode'],
		$myrow['accountcode'],
		$myrow['accountname'],
		$myrow['narrative'],
		$Debit,
		$Credit,
		$Posted);

		$j++;
		If ($j == 18){
			$j=1;
			echo $TableHeader;
		}
	}
	//end of while loop

	if(DB_num_rows($Result)>0){
	// bowikaxu realhost feb 2008 - check if transaction its correct else show totals in red
		echo "<TR><TD></TD><TD class='tableheader' COLSPAN=2><STRONG><BIG>"._('Total')."</BIG></STRONG></TD>
	<TD class='tableheader' ALIGN=RIGHT><STRONG><BIG>".number_format(abs($TotDebit),2)."</BIG></STRONG></TD>
	<TD class='tableheader' ALIGN=RIGHT><STRONG><BIG>".number_format(abs($TotCredit),2)."</BIG></STRONG></TD>
	</TR>";
	echo '</TABLE></CENTER>';
	}

} elseif (!isset($_GET['Transno'])){

	prnMsg( _('To enter a supplier invoice the supplier must first be selected from the supplier selection screen'),'warn');
	echo "<BR><A HREF='$rootpath/SelectSupplier.php?" . SID ."'>" . _('Select A Supplier to Enter an Invoice For') . '</A>';
	include('includes/footer.inc');
	exit;

	/*It all stops here if there ain't no supplier selected */
}



include('includes/footer.inc');
?>
