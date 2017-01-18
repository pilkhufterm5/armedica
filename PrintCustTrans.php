<?php

/* $Revision: 14 $ */

$PageSecurity = 1;

include('includes/session.inc');

if (isset($_GET['FromTransNo'])){
	$FromTransNo = trim($_GET['FromTransNo']);
} elseif (isset($_POST['FromTransNo'])){
	$FromTransNo = trim($_POST['FromTransNo']);
}


if (isset($_GET['InvOrCredit'])){
	$InvOrCredit = $_GET['InvOrCredit'];
} elseif (isset($_POST['InvOrCredit'])){
	$InvOrCredit = $_POST['InvOrCredit'];
}
if (isset($_GET['PrintPDF'])){
	$PrintPDF = $_GET['PrintPDF'];
} elseif (isset($_POST['PrintPDF'])){
	$PrintPDF = $_POST['PrintPDF'];
}


If (!isset($_POST['ToTransNo']) 
	OR trim($_POST['ToTransNo'])==''
	OR $_POST['ToTransNo'] < $FromTransNo){
	
	$_POST['ToTransNo'] = $FromTransNo;
}

$FirstTrans = $FromTransNo; /*Need to start a new page only on subsequent transactions */

If (isset($PrintPDF) 
	AND $PrintPDF!='' 
	AND isset($FromTransNo) 
	AND isset($InvOrCredit) 
	AND $FromTransNo!=''){
	// bowikaxu - seleccionar y buscar el template a usar
	$SQL = "SELECT debtortrans.transno, debtortrans.order_
			FROM debtortrans
			WHERE debtortrans.transno = ".$FromTransNo."
			AND type = 11";
	$Res = DB_query($SQL,$db);
	$Template = DB_fetch_array($Res);


        /*
         * iJPe
         * realhost
         * 2010-02-23
         *
         * Modificacion para obtener el templete de la impresion, ya que en ocasiones no hay pedido
         * relacionado a la nota de credito
         */
        $sqlNCe = "SELECT rh_locations.rh_template FROM rh_crednotesreference INNER JOIN rh_locations ON rh_crednotesreference.loccode = rh_locations.loccode WHERE intcn = ".$Template['transno'];
        $resNCe = DB_query($sqlNCe, $db);

        if (DB_num_rows($resNCe)>0)
        {
            $rowNC = DB_fetch_array($resNCe);

        }else{

            $sqlLoc = "SELECT rh_locations.rh_template FROM salesorders INNER JOIN rh_locations ON salesorders.fromstkloc_virtual = rh_locations.loccode WHERE orderno = ".$Template['order_'];
            $resLoc = DB_query($sqlLoc, $db);

            if (DB_num_rows($resLoc)>0)
            {
                $rowNC = DB_fetch_array($resLoc);

            }else{
                $sqlSM = "SELECT rh_locations.rh_template FROM stockmoves INNER JOIN rh_locations ON stockmoves.loccode = rh_locations.loccode WHERE type = 11 AND transno = ".$Template['transno'];
                $resSM = DB_query($sqlSM, $db);

                $rowNC = DB_fetch_array($resSM);

            }
        }

	switch($rowNC['rh_template']){
		
		case 1:	// b22
			include('templates/rh_notcred1.php');
			break;
		case 2:	// cancun
			include('templates/rh_notcred1.php');
			break;
		
	}
} else { /*The option to print PDF was not hit */
	
	$title=_('Select Invoices/Credit Notes To Print');
	include('includes/header.inc');

	if (!isset($FromTransNo) OR $FromTransNo=='') {


	/*if FromTransNo is not set then show a form to allow input of either a single invoice number or a range of invoices to be printed. Also get the last invoice number created to show the user where the current range is up to */

		echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID . "' METHOD='POST'><CENTER><TABLE>";

		echo '<TR><TD>' . _('Print Invoices or Credit Notes') . '</TD><TD><SELECT name=InvOrCredit>';
		if ($InvOrCredit=='Invoice' OR !isset($InvOrCredit)){

		   //echo "<OPTION SELECTED VALUE='Invoice'>" . _('Invoices');
		   echo "<OPTION SELECTED VALUE='Credit'>" . _('Credit Notes');

		} else {

		   echo "<OPTION SELECTED VALUE='Credit'>" . _('Credit Notes');
		   //echo "<OPTION VALUE='Invoice'>" . _('Invoices');

		}

		echo '</SELECT></TD></TR>';
/*
		echo '<TR><TD>' . _('Print EDI Transactions') . '</TD><TD><SELECT name=PrintEDI>';
		if ($InvOrCredit=='Invoice' OR !isset($InvOrCredit)){

		   echo "<OPTION SELECTED VALUE='No'>" . _('Do not Print PDF EDI Transactions');
		   echo "<OPTION VALUE='Yes'>" . _('Print PDF EDI Transactions Too');

		} else {

		   echo "<OPTION VALUE='No'>" . _('Do not Print PDF EDI Transactions');
		   echo "<OPTION SELECTED VALUE='Yes'>" . _('Print PDF EDI Transactions Too');

		}

		echo '</SELECT></TD></TR>';
		*/
		echo '<TR><TD>' . _('Start credit note number to print') . '</TD><TD><input Type=text max=6 size=7 name=FromTransNo></TD></TR>';
		echo '<TR><TD>' . _('End credit note number to print') . "</TD><TD><input Type=text max=6 size=7 name='ToTransNo'></TD></TR></TABLE></CENTER>";
		echo "<CENTER><INPUT TYPE=Submit Name='Print' Value='" . _('Print') . "'><P>";
		echo "<INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') . "'></CENTER>";

		$sql = 'SELECT typeno FROM systypes WHERE typeid=11';

		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);

		echo '<P>' . _('The last credit note created was number') . ' ' . $myrow[0] . '<BR>' . _('A sequential range can be printed using the same method as for invoices above') . '. ' . _('A single credit note can be printed by only entering a start transaction number');

	} else {

		while ($FromTransNo <= $_POST['ToTransNo']){

	/*retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */

			if ($InvOrCredit=='Invoice') {

			   $sql = "SELECT
			   		debtortrans.trandate,
					debtortrans.ovamount, 
					debtortrans.id as ID,
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
					shippers.shippername, 
					custbranch.brname, 
					custbranch.braddress1, 
					custbranch.braddress2,
					custbranch.braddress3, 
					custbranch.braddress4,
					custbranch.braddress5,
					custbranch.braddress6,
					salesman.salesmanname, 
					debtortrans.debtorno 
				FROM debtortrans, 
					debtorsmaster, 
					custbranch, 
					salesorders, 
					shippers, 
					salesman 
				WHERE debtortrans.order_ = salesorders.orderno 
				AND debtortrans.type=10 
				AND debtortrans.transno=" . $FromTransNo . "
				AND debtortrans.shipvia=shippers.shipper_id 
				AND debtortrans.debtorno=debtorsmaster.debtorno 
				AND debtortrans.debtorno=custbranch.debtorno 
				AND debtortrans.branchcode=custbranch.branchcode 
				AND custbranch.salesman=salesman.salesmancode";
			} else {

			   $sql = 'SELECT debtortrans.trandate,
			   		debtortrans.ovamount, 
			   		debtortrans.id as ID,
					debtortrans.ovdiscount, 
					debtortrans.ovfreight, 
					debtortrans.ovgst, 
					debtortrans.rate, 
					debtortrans.invtext, 
					debtorsmaster.name, 
					debtorsmaster.address1, 
					debtorsmaster.address2, 
					debtorsmaster.address3,
					debtorsmaster.address4,
					debtorsmaster.address5,
					debtorsmaster.address6,
					debtorsmaster.currcode, 
					custbranch.brname, 
					custbranch.braddress1, 
					custbranch.braddress2, 
					custbranch.braddress3, 
					custbranch.braddress4, 
					custbranch.braddress5, 
					custbranch.braddress6, 
					salesman.salesmanname, 
					debtortrans.debtorno 
				FROM debtortrans, 
					debtorsmaster, 
					custbranch, 
					salesman 
				WHERE debtortrans.type=11
				AND debtortrans.transno=' . $FromTransNo . '
				AND debtortrans.debtorno=debtorsmaster.debtorno
				AND debtortrans.debtorno=custbranch.debtorno 
				AND debtortrans.branchcode=custbranch.branchcode 
				AND custbranch.salesman=salesman.salesmancode';

			}

			$result=DB_query($sql,$db);
			if (DB_num_rows($result)==0 OR DB_error_no($db)!=0) {
				echo '<P>' . _('There was a problem retrieving the invoice or credit note details for note number') . ' ' . $InvoiceToPrint . ' ' . _('from the database') . '. ' . _('To print an invoice, the sales order record, the customer transaction record and the branch record for the customer must not have been purged') . '. ' . _('To print a credit note only requires the customer, transaction, salesman and branch records be available');
				if ($debug==1){
					echo _('The SQL used to get this information that failed was') . "<BR>$sql";
				}
				break;
				include('includes/footer.inc');
				exit;
			} elseif (DB_num_rows($result)==1){

				$myrow = DB_fetch_array($result);
	/* Then there's an invoice (or credit note) to print. So print out the invoice header and GST Number from the company record */
				if (count($_SESSION['AllowedPageSecurityTokens'])==1 AND in_array(1, $_SESSION['AllowedPageSecurityTokens']) AND $myrow['debtorno'] != $_SESSION['CustomerID']){
					echo '<P><FONT COLOR=RED SIZE=4>' . _('This transaction is addressed to another customer and cannot be displayed for privacy reasons') . '. ' . _('Please select only transactions relevant to your company');
					exit;
				}

				$ExchRate = $myrow['rate'];
				$PageNumber = 1;

				$ID = $myrow['ID'];
				$sql2 = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations 
				WHERE rh_invoicesreference.ref = ".$ID." AND locations.loccode = rh_invoicesreference.loccode";
				$Res = DB_query($sql2,$db);
				$ExtRes = DB_fetch_array($Res);
				
				echo "<TABLE WIDTH=100%><TR><TD VALIGN=TOP WIDTH=10%><img src='companies/" . $_SESSION['DatabaseName'] . "/logo.jpg'></TD><TD BGCOLOR='#BBBBBB'><B>";

				if ($InvOrCredit=='Invoice') {
				   echo '<FONT SIZE=4>' . _('TAX INVOICE') . ' ';
				} else {
				   echo '<FONT COLOR=RED SIZE=4>' . _('TAX CREDIT NOTE') . ' ';
				}
				echo '</B>' . _('Number') . ' ' . $FromTransNo . '</FONT><BR><FONT SIZE=1>' . _('Tax Authority Ref') . '. ' . $_SESSION['CompanyRecord']['gstno'] . '</TD></TR></TABLE>';

	/*Now print out the logo and company name and address */
				echo "<TABLE WIDTH=100%><TR><TD><FONT SIZE=4 COLOR='#333333'><B>" . $_SESSION['CompanyRecord']['coyname'] . "</B></FONT><BR>";
				echo $_SESSION['CompanyRecord']['regoffice1'] . '<BR>';
				echo $_SESSION['CompanyRecord']['regoffice2'] . '<BR>';
				echo $_SESSION['CompanyRecord']['regoffice3'] . '<BR>';
				echo $_SESSION['CompanyRecord']['regoffice4'] . '<BR>';
				echo $_SESSION['CompanyRecord']['regoffice5'] . '<BR>';
				echo $_SESSION['CompanyRecord']['regoffice6'] . '<BR>';
				echo _('Telephone') . ': ' . $_SESSION['CompanyRecord']['telephone'] . '<BR>';
				echo _('Facsimile') . ': ' . $_SESSION['CompanyRecord']['fax'] . '<BR>';
				echo _('Email') . ': ' . $_SESSION['CompanyRecord']['email'] . '<BR>';

				echo '</TD><TD WIDTH=50% ALIGN=RIGHT>';

	/*Now the customer charged to details in a sub table within a cell of the main table*/

				echo "<TABLE WIDTH=100%><TR><TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Charge To') . ":</B></TD></TR><TR><TD BGCOLOR='#EEEEEE'>";
				echo $myrow['name'] . '<BR>' . $myrow['address1'] . '<BR>' . $myrow['address2'] . '<BR>' . $myrow['address3'] . '<BR>' . $myrow['address4'] . '<BR>' . $myrow['address5'] . '<BR>' . $myrow['address6'];
				echo '</TD></TR></TABLE>';
				/*end of the small table showing charge to account details */
				echo _('Page') . ': ' . $PageNumber;
				echo '</TD></TR></TABLE>';
				/*end of the main table showing the company name and charge to details */

				if ($InvOrCredit=='Invoice') {

				   echo "<TABLE WIDTH=100%>
				   			<TR>
				   				<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Charge Branch') . ":</B></TD>
								<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Delivered To') . ":</B></TD>
							</TR>";
				   echo "<TR>
				   		<TD BGCOLOR='#EEEEEE'>" .$myrow['brname'] . '<BR>' . $myrow['braddress1'] . '<BR>' . $myrow['braddress2'] . '<BR>' . $myrow['braddress3'] . '<BR>' . $myrow['braddress4'] . '<BR>' . $myrow['braddress5'] . '<BR>' . $myrow['braddress6'] . '</TD>';

				   	echo "<TD BGCOLOR='#EEEEEE'>" . $myrow['deliverto'] . '<BR>' . $myrow['deladd1'] . '<BR>' . $myrow['deladd2'] . '<BR>' . $myrow['deladd3'] . '<BR>' . $myrow['deladd4'] . '<BR>' . $myrow['deladd5'] . '<BR>' . $myrow['deladd6'] . '</TD>';
				   echo '</TR>
				   </TABLE><HR>';
				   // se agrego el campo Externa para el numero de Factura Externa
				   echo "<TABLE WIDTH=100%>
				   		<TR>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Your Order Ref') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Externa') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Our Order No') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Order Date') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Invoice Date') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Sales Person') . "</FONT></B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Shipper') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Consignment Ref') . "</B></TD>
						</TR>";
				   	echo "<TR>
							<TD BGCOLOR='#EEEEEE'>" . $myrow['customerref'] . "</TD>
							<TD BGCOLOR='#EEEEEE'>" . $ExtRes['rh_serie'].$ExtRes['extinvoice'] . "</TD>
							<TD BGCOLOR='#EEEEEE'>" .$myrow['orderno'] . "</TD>
							<TD BGCOLOR='#EEEEEE'>" . ConvertSQLDate($myrow['orddate']) . "</TD>
							<TD BGCOLOR='#EEEEEE'>" . ConvertSQLDate($myrow['trandate']) . "</TD>
							<TD BGCOLOR='#EEEEEE'>" . $myrow['salesmanname'] . "</TD>
							<TD BGCOLOR='#EEEEEE'>" . $myrow['shippername'] . "</TD>
							<TD BGCOLOR='#EEEEEE'>" . $myrow['consignment'] . "</TD>
						</TR>
					</TABLE>";
					
				   $sql ="SELECT stockmoves.stockid,
				   		stockmaster.description, 
						-stockmoves.qty as quantity, 
						stockmoves.discountpercent, 
						((1 - stockmoves.discountpercent) * stockmoves.price * " . $ExchRate . '* -stockmoves.qty) AS fxnet,
						(stockmoves.price * ' . $ExchRate . ') AS fxprice,
						stockmoves.narrative, 
						stockmaster.units 
					FROM stockmoves, 
						stockmaster 
					WHERE stockmoves.stockid = stockmaster.stockid 
					AND stockmoves.type=10 
					AND stockmoves.transno=' . $FromTransNo . '
					AND stockmoves.show_on_inv_crds=1';

				} else { /* then its a credit note */

				   echo "<TABLE WIDTH=50%><TR>
				   		<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Branch') . ":</B></TD>
						</TR>";
				   echo "<TR>
				   		<TD BGCOLOR='#EEEEEE'>" .$myrow['brname'] . '<BR>' . $myrow['braddress1'] . '<BR>' . $myrow['braddress2'] . '<BR>' . $myrow['braddress3'] . '<BR>' . $myrow['braddress4'] . '<BR>' . $myrow['braddress5'] . '<BR>' . $myrow['braddress6'] . '</TD>
					</TR></TABLE>';
				   echo "<HR><TABLE WIDTH=100%><TR>
				   		<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Date') . "</B></TD>
						<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Sales Person') . "</FONT></B></TD>
					</TR>";
				   echo "<TR>
				   		<TD BGCOLOR='#EEEEEE'>" . ConvertSQLDate($myrow['trandate']) . "</TD>
						<TD BGCOLOR='#EEEEEE'>" . $myrow['salesmanname'] . '</TD>
					</TR></TABLE>';
				   
				   $sql ='SELECT stockmoves.stockid,
				   		stockmaster.description, 
						stockmoves.qty as quantity, 
						stockmoves.discountpercent, ((1 - stockmoves.discountpercent) * stockmoves.price * ' . $ExchRate . ' * stockmoves.qty) AS fxnet,
						(stockmoves.price * ' . $ExchRate . ') AS fxprice,
						stockmaster.units 
					FROM stockmoves, 
						stockmaster 
					WHERE stockmoves.stockid = stockmaster.stockid 
					AND stockmoves.type=11 
					AND stockmoves.transno=' . $FromTransNo . '
					AND stockmoves.show_on_inv_crds=1';
				}

				echo '<HR>';
				echo '<CENTER><FONT SIZE=2>' . _('All amounts stated in') . ' ' . $myrow['currcode'] . '</FONT></CENTER>';

				$result=DB_query($sql,$db);
				if (DB_error_no($db)!=0) {
					echo '<BR>' . _('There was a problem retrieving the invoice or credit note stock movement details for invoice number') . ' ' . $FromTransNo . ' ' . _('from the database');
					if ($debug==1){
						 echo '<BR>' . _('The SQL used to get this information that failed was') . "<BR>$sql";
					}
					exit;
				}

				if (DB_num_rows($result)>0){
					echo "<TABLE WIDTH=100% CELLPADDING=5>
						<TR><TD class='tableheader'>" . _('Item Code') . "</TD>
						<TD class='tableheader'>" . _('Item Description') . "</TD>
						<TD class='tableheader'>" . _('Quantity') . "</TD>
						<TD class='tableheader'>" . _('Unit') . "</TD>
						<TD class='tableheader'>" . _('Price') . "</TD>
						<TD class='tableheader'>" . _('Discount') . "</TD>
						<TD class='tableheader'>" . _('Net') . '</TD></TR>';

					$LineCounter =17;
					$k=0;	//row colour counter

					while ($myrow2=DB_fetch_array($result)){

					      if ($k==1){
						  $RowStarter = "<tr bgcolor='#BBBBBB'>";
						  $k=0;
					      } else {
						  $RowStarter = "<tr bgcolor='#EEEEEE'>";
						  $k=1;
					      }
					      
					      echo $RowStarter;
					      
					      $DisplayPrice = number_format($myrow2['fxprice'],2);
					      $DisplayQty = number_format($myrow2['quantity'],2);
					      $DisplayNet = number_format($myrow2['fxnet'],2);

					      if ($myrow2['discountpercent']==0){
						   $DisplayDiscount ='';
					      } else {
						   $DisplayDiscount = number_format($myrow2['discountpercent']*100,2) . '%';
					      }

					      printf ('<TD>%s</TD>
					      		<TD>%s</TD>
							<TD ALIGN=RIGHT>%s</TD>
							<TD ALIGN=RIGHT>%s</TD>
							<TD ALIGN=RIGHT>%s</TD>
							<TD ALIGN=RIGHT>%s</TD>
							<TD ALIGN=RIGHT>%s</TD>
							</TR>',
							$myrow2['stockid'],
							$myrow2['description'],
							$DisplayQty, 
							$myrow2['units'],
							$DisplayPrice, 
							$DisplayDiscount, 
							$DisplayNet);

					      if (strlen($myrow2['narrative'])>1){
					      		echo $RowStarter . '<TD></TD><TD COLSPAN=6>' . $myrow2['narrative'] . '</TD></TR>';
							$LineCounter++;
					      }
						
					      $LineCounter++;

					      if ($LineCounter == ($_SESSION['PageLength'] - 2)){

						/* head up a new invoice/credit note page */

						   $PageNumber++;
						   echo "</TABLE><TABLE WIDTH=100%><TR><TD VALIGN=TOP><img src='companies/" . $_SESSION['DatabaseName'] . "/logo.jpg'></TD><TD BGCOLOR='#BBBBBB'><CENTER><B>";

						   if ($InvOrCredit=='Invoice') {
							    echo '<FONT SIZE=4>' . _('TAX INVOICE') . ' ';
						   } else {
							    echo '<FONT COLOR=RED SIZE=4>' . _('TAX CREDIT NOTE') . ' ';
						   }
						   echo '</B>' . _('Number') . ' ' . $FromTransNo . '</FONT><BR><FONT SIZE=1>' . _('GST Number') . ' - ' . $_SESSION['CompanyRecord']['gstno'] . '</TD></TR><TABLE>';

	/*Now print out company name and address */
						    echo "<TABLE WIDTH=100%><TR>
						    	<TD><FONT SIZE=4 COLOR='#333333'><B>" . $_SESSION['CompanyRecord']['coyname'] . '</B></FONT><BR>';
						    echo $_SESSION['CompanyRecord']['regoffice1'] . '<BR>';
						    echo $_SESSION['CompanyRecord']['regoffice2'] . '<BR>';
						    echo $_SESSION['CompanyRecord']['regoffice3'] . '<BR>';
						    echo $_SESSION['CompanyRecord']['regoffice4'] . '<BR>';
						    echo $_SESSION['CompanyRecord']['regoffice5'] . '<BR>';
						    echo $_SESSION['CompanyRecord']['regoffice6'] . '<BR>';
						    echo _('Telephone') . ': ' . $_SESSION['CompanyRecord']['telephone'] . '<BR>';
						    echo _('Facsimile') . ': ' . $_SESSION['CompanyRecord']['fax'] . '<BR>';
						    echo _('Email') . ': ' . $_SESSION['CompanyRecord']['email'] . '<BR>';
						    echo '</TD><TD ALIGN=RIGHT>' . _('Page') . ": $PageNumber</TD></TR></TABLE>";
						    echo "<TABLE WIDTH=100% CELLPADDING=5><TR>
						    	<TD class='tableheader'>" . _('Item Code') . "</TD>
							<TD class='tableheader'>" . _('Item Description') . "</TD>
							<TD class='tableheader'>" . _('Quantity') . "</TD>
							<TD class='tableheader'>" . _('Unit') . "</TD>
							<TD class='tableheader'>" . _('Price') . "</TD>
							<TD class='tableheader'>" . _('Discount') . "</TD>
							<TD class='tableheader'>" . _('Net') . "</TD></TR>";

						    $LineCounter = 10;

					      } //end if need a new page headed up
					} //end while there are line items to print out
					echo '</TABLE>';
				} /*end if there are stock movements to show on the invoice or credit note*/

				/* check to see enough space left to print the totals/footer */
				$LinesRequiredForText = floor(strlen($myrow['invtext'])/140);

				if ($LineCounter >= ($_SESSION['PageLength'] - 8 - $LinesRequiredFortext)){

					/* head up a new invoice/credit note page */

					$PageNumber++;
					echo "<TABLE WIDTH=100%><TR><TD VALIGN=TOP><img src='companies/" . $_SESSION['DatabaseName'] . "/logo.jpg'></TD><TD BGCOLOR='#BBBBBB'><CENTER><B>";

					if ($InvOrCredit=='Invoice') {
					      echo '<FONT SIZE=4>' . _('TAX INVOICE') .' ';
					} else {
					      echo '<FONT COLOR=RED SIZE=4>' . _('TAX CREDIT NOTE') . ' ';
					}
					echo '</B>' . _('Number') . ' ' . $FromTransNo . '</FONT><BR><FONT SIZE=1>' . _('GST Number') . ' - ' . $_SESSION['CompanyRecord']['gstno'] . '</TD></TR><TABLE>';

	/*Print out the logo and company name and address */
					echo "<TABLE WIDTH=100%><TR><TD><FONT SIZE=4 COLOR='#333333'><B>" . $_SESSION['CompanyRecord']['coyname'] . "</B></FONT><BR>";
					echo $_SESSION['CompanyRecord']['regoffice1'] . '<BR>';
					echo $_SESSION['CompanyRecord']['regoffice2'] . '<BR>';
					echo $_SESSION['CompanyRecord']['regoffice3'] . '<BR>';
					echo $_SESSION['CompanyRecord']['regoffice4'] . '<BR>';
					echo $_SESSION['CompanyRecord']['regoffice5'] . '<BR>';
					echo $_SESSION['CompanyRecord']['regoffice6'] . '<BR>';
					echo _('Telephone') . ': ' . $_SESSION['CompanyRecord']['telephone'] . '<BR>';
					echo _('Facsimile') . ': ' . $_SESSION['CompanyRecord']['fax'] . '<BR>';
					echo _('Email') . ': ' . $_SESSION['CompanyRecord']['email'] . '<BR>';
					echo '</TD><TD ALIGN=RIGHT>' . _('Page') . ": $PageNumber</TD></TR></TABLE>";
					echo "<TABLE WIDTH=100% CELLPADDING=5><TR>
						<TD class='tableheader'>" . _('Item Code') . "</TD>
						<TD class='tableheader'>" . _('Item Description') . "</TD>
						<TD class='tableheader'>" . _('Quantity') . "</TD>
						<TD class='tableheader'>" . _('Unit') . "</TD>
						<TD class='tableheader'>" . _('Price') . "</TD>
						<TD class='tableheader'>" . _('Discount') . "</TD>
						<TD class='tableheader'>" . _('Net') . '</TD></TR>';

					$LineCounter = 10;
				}

	/*Space out the footer to the bottom of the page */

				echo '<BR><BR>' . $myrow['invtext'];

				$LineCounter=$LineCounter+2+$LinesRequiredForText;
				while ($LineCounter < ($_SESSION['PageLength'] -6)){
					echo '<BR>';
					$LineCounter++;
				}

	/*Now print out the footer and totals */

				if ($InvOrCredit=='Invoice') {

				   $DisplaySubTot = number_format($myrow['ovamount'],2);
				   $DisplayFreight = number_format($myrow['ovfreight'],2);
				   $DisplayTax = number_format($myrow['ovgst'],2);
				   $DisplayTotal = number_format($myrow['ovfreight']+$myrow['ovgst']+$myrow['ovamount'],2);
				} else {
				   $DisplaySubTot = number_format(-$myrow['ovamount'],2);
				   $DisplayFreight = number_format(-$myrow['ovfreight'],2);
				   $DisplayTax = number_format(-$myrow['ovgst'],2);
				   $DisplayTotal = number_format(-$myrow['ovfreight']-$myrow['ovgst']-$myrow['ovamount'],2);
				}
	/*Print out the invoice text entered */
				echo '<TABLE WIDTH=100%><TR>
					<TD ALIGN=RIGHT>' . _('Sub Total') . "</TD>
					<TD ALIGN=RIGHT BGCOLOR='#EEEEEE' WIDTH=15%>$DisplaySubTot</TD></TR>";
				echo '<TR><TD ALIGN=RIGHT>' . _('Freight') . "</TD>
					<TD ALIGN=RIGHT BGCOLOR='#EEEEEE'>$DisplayFreight</TD></TR>";
				echo '<TR><TD ALIGN=RIGHT>' . _('Tax') . "</TD>
					<TD ALIGN=RIGHT BGCOLOR='#EEEEEE'>$DisplayTax</TD></TR>";
				if ($InvOrCredit=='Invoice'){
				     echo '<TR><TD Align=RIGHT><B>' . _('TOTAL INVOICE') . "</B></TD>
				     	<TD ALIGN=RIGHT BGCOLOR='#EEEEEE'><U><B>$DisplayTotal</B></U></TD></TR>";
				} else {
				     echo '<TR><TD Align=RIGHT><FONT COLOR=RED><B>' . _('TOTAL CREDIT') . "</B></FONT></TD>
				     		<TD ALIGN=RIGHT BGCOLOR='#EEEEEE'><FONT COLOR=RED><U><B>$DisplayTotal</B></U></FONT></TD></TR>";
				}
				echo '</TABLE>';
			} /* end of check to see that there was an invoice record to print */
			$FromTransNo++;
		} /* end loop to print invoices */
	} /*end of if FromTransNo exists */
	include('includes/footer.inc');

} /*end of else not PrintPDF */



function PrintLinesToBottom () {

	global $pdf;
	global $PageNumber;
	global $TopOfColHeadings;
	global $Left_Margin;
	global $Bottom_Margin;
	global $line_height;

	
/*draw the vertical column lines right to the bottom */
	$pdf->line($Left_Margin+97, $TopOfColHeadings+12,$Left_Margin+97,$Bottom_Margin);

	/*Print a column vertical line */
	$pdf->line($Left_Margin+350, $TopOfColHeadings+12,$Left_Margin+350,$Bottom_Margin);

	/*Print a column vertical line */
	$pdf->line($Left_Margin+450, $TopOfColHeadings+12,$Left_Margin+450,$Bottom_Margin);

	/*Print a column vertical line */
	$pdf->line($Left_Margin+550, $TopOfColHeadings+12,$Left_Margin+550,$Bottom_Margin);

	/*Print a column vertical line */
	$pdf->line($Left_Margin+587, $TopOfColHeadings+12,$Left_Margin+587,$Bottom_Margin);

	$pdf->line($Left_Margin+640, $TopOfColHeadings+12,$Left_Margin+640,$Bottom_Margin);

	$pdf->newPage();
	$PageNumber++;

}

?>