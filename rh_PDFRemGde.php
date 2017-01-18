<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
/* 

Andres Amaya Diaz
Marzo 2007 bowikaxu - Impresion de Remision Grande Reyna Garza
Enero 2008 bowikaxu - corregir bug donde se duplican articulos

*/

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
	$SQL = "SELECT salesorders.fromstkloc,
			debtortrans.transno,
			locations.rh_template
			FROM salesorders, debtortrans, locations
			WHERE debtortrans.transno = ".$FromTransNo."
			AND type = 20000
			AND debtortrans.order_ = salesorders.orderno
			AND locations.loccode = salesorders.fromstkloc";
	$Res = DB_query($SQL,$db);
	$Template = DB_fetch_array($Res);
	switch($Template['rh_template']){
		
		case 1:	// MTY
			include('templates/rh_remgde1.php');
			break;
		case 2:	// cancun
			include('templates/rh_remgde2.php');
			break;
		case 'b22':
			include('templates/rh_remgde1.php');
			break;
		default:
			include('templates/rh_remgde1.php');
			break;
	}
		

} else { /*The option to print PDF was not hit */
	
	$title=_('Select Remisiones Print');
	include('includes/header.inc');

	if (!isset($FromTransNo) OR $FromTransNo=='') {


	/*if FromTransNo is not set then show a form to allow input of either a single invoice number or a range of invoices to be printed. Also get the last invoice number created to show the user where the current range is up to */

		echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID . "' METHOD='POST'><CENTER><TABLE>";

		echo '<TR><TD>' . _('Print Remisiones') . '</TD><TD><SELECT name=InvOrCredit>';
		if ($InvOrCredit=='Invoice' OR !isset($InvOrCredit)){

		   echo "<OPTION SELECTED VALUE='Invoice'>" . _('Remisiones');
		   //echo "<OPTION VALUE='Credit'>" . _('Credit Notes');

		} else {

		   //echo "<OPTION SELECTED VALUE='Credit'>" . _('Credit Notes');
		   echo "<OPTION VALUE='Invoice'>" . _('Remisiones');

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

		echo '</SELECT></TD></TR>';*/
		echo '<TR><TD>' . _('Start remision number to print') . '</TD><TD><input Type=text max=6 size=7 name=FromTransNo></TD></TR>';
		echo '<TR><TD>' . _('End remision number to print') . "</TD><TD><input Type=text max=6 size=7 name='ToTransNo'></TD></TR></TABLE></CENTER>";
		//echo "<CENTER><INPUT TYPE=Submit Name='Print' Value='" . _('Print') . "'><P>";
		echo "<CENTER><INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') . "'></CENTER>";

		$sql = 'SELECT typeno FROM systypes WHERE typeid=20000';

		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);

		echo '<P>' . _('The last remision created was number') . ' ' . $myrow[0] . '<BR>' . _('If only a single remision is required') . ', ' . _('enter the invoice number to print in the Start transaction number to print field and leave the End transaction number to print field blank') . '. ' . _('Only use the end remision to print field if you wish to print a sequential range of remisiones');

//		$sql = 'SELECT typeno FROM systypes WHERE typeid=11';

//		$result = DB_query($sql,$db);
//		$myrow = DB_fetch_row($result);

		//echo '<P>' . _('The last credit note created was number') . ' ' . $myrow[0] . '<BR>' . _('A sequential range can be printed using the same method as for invoices above') . '. ' . _('A single credit note can be printed by only entering a start transaction number');

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
					custbranch.taxgroupid,
					custbranch.brname, 
					custbranch.braddress1, 
					custbranch.braddress2,
					custbranch.braddress3, 
					custbranch.braddress4,
					custbranch.braddress5,
					custbranch.braddress6,
					salesman.salesmanname, 
					debtortrans.debtorno,
					locations.taxprovinceid
				FROM debtortrans, 
					debtorsmaster, 
					custbranch, 
					salesorders, 
					shippers, 
					salesman,
					locations
				WHERE debtortrans.order_ = salesorders.orderno 
				AND debtortrans.type=20000 
				AND debtortrans.transno=" . $FromTransNo . "
				AND debtortrans.shipvia=shippers.shipper_id 
				AND salesorders.fromstkloc=locations.loccode
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
				
				echo "<TABLE WIDTH=100%><TR><TD VALIGN=TOP WIDTH=10%><img src='companies/" . $_SESSION['DatabaseName'] . "/logo.jpg'></TD><TD BGCOLOR='#BBBBBB'><B>";

				if ($InvOrCredit=='Invoice') {
				   echo '<FONT SIZE=4>' . _('REMISION') . ' ';
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
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Our Order No') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Order Date') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Invoice Date') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Sales Person') . "</FONT></B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Shipper') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Consignment Ref') . "</B></TD>
						</TR>";
				   	echo "<TR>
							<TD BGCOLOR='#EEEEEE'>" . $myrow['customerref'] . "</TD>
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
						rh_remdetails.qty AS qty2,
						stockmoves.discountpercent, 
						((1 - stockmoves.discountpercent) * stockmoves.price * " . $ExchRate . '* rh_remdetails.qty) AS fxnet,
						(stockmoves.price * ' . $ExchRate . ') AS fxprice,
						stockmoves.narrative, 
						stockmaster.units 
					FROM stockmoves, 
						stockmaster,
						rh_remdetails 
					WHERE stockmoves.stockid = stockmaster.stockid 
					AND stockmoves.type=20000
					AND rh_remdetails.transno = '.$FromTransNo.'
					AND rh_remdetails.stockid = stockmoves.stockid
					AND rh_remdetails.reference = stockmoves.stkmoveno
					AND stockmoves.transno=' . $FromTransNo . '
					AND stockmoves.show_on_inv_crds=1 ORDER BY rh_remdetails.line';
				   

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
					AND stockmoves.show_on_inv_crds=1 ORDER BY rh_remdetails.line';
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
					      $DisplayQty = number_format($myrow2['qty2'],2);
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
							/*$myrow2['stockid']*/'',
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
							    echo '<FONT SIZE=4>' . _('REMISION') . ' ';
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
					      echo '<FONT SIZE=4>' . _('REMISION') .' ';
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
				//echo '<TR><TD ALIGN=RIGHT>' . _('Tax') . "</TD>
				//	<TD ALIGN=RIGHT BGCOLOR='#EEEEEE'>$DisplayTax</TD></TR>";
				if ($InvOrCredit=='Invoice'){
				     echo '<TR><TD Align=RIGHT><B>' . _('TOTAL') . "</B></TD>
				     	<TD ALIGN=RIGHT BGCOLOR='#EEEEEE'><U><B>$DisplayTotal</B></U></TD></TR>";
				} else {
				     echo '<TR><TD Align=RIGHT><FONT COLOR=RED><B>' . _('TOTAL CREDIT') . "</B></FONT></TD>
				     		<TD ALIGN=RIGHT BGCOLOR='#EEEEEE'><FONT COLOR=RED><U><B>$DisplayTotal</B></U></FONT></TD></TR>";
				}
                                echo '</TABLE>';

                                /*
                                 * iJPe
                                 * realhost
                                 * 2010-05-25
                                 *
                                 * Se realizo la modificacion para mostrar a que otro movimiento esta asignada esta factura
                                 */

                                echo "<BR><HR>";
                                echo "<center>";

                                $sqlGetTran = "SELECT systypes.typename, debtortrans.type, debtortrans.transno, debtortrans.trandate, ((debtortrans.ovamount+debtortrans.ovgst)*-1) AS total, custallocns.amt FROM custallocns INNER JOIN debtortrans ON custallocns.transid_allocfrom = debtortrans.id LEFT JOIN systypes ON debtortrans.type = systypes.typeid WHERE transid_allocto = ".$myrow['ID'];
                                $resGetTran = DB_query($sqlGetTran, $db);

                                if (DB_num_rows($resGetTran)){

                                    echo "<h4>"._('Transacciones asignadas a la Remision #'.$FromTransNo.'')."</h4>";

                                    echo "<table>";
                                    echo "<tr>";
                                    echo "<td class='tableheader'>Tipo Trans.</td>
                                        <td class='tableheader'>Trans. #</td>
                                        <td class='tableheader'>Fecha Trans.</td>
                                        <td class='tableheader'>Total de Trans.</td>
                                        <td class='tableheader'>Monto asignado</td>";
                                    echo "</tr>";

                                    while ($rowGetTrans = DB_fetch_array($resGetTran)){

                                        echo "<tr>";
                                        echo "<td>".$rowGetTrans['typename']."</td>";
                                        echo "<td>".$rowGetTrans['transno']."</td>";
                                        echo "<td>".ConvertSQLDate($rowGetTrans['trandate'])."</td>";
                                        echo "<td align='right'>$".number_format($rowGetTrans['total'],2)."</td>";
                                        echo "<td align='right'>$".number_format($rowGetTrans['amt'],2)."</td>";

                                        echo "</tr>";
                                    }

                                    echo "</table>";
                                }else{
                                    echo "<h4>"._('No hay transacciones asignadas a la Remision #'.$FromTransNo.'')."</h4>";
                                }
                                echo "<BR><HR><BR>";
                                echo "<center>";


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

	$pdf->newPage();
	$PageNumber++;

}

function ConvertDate($DateEntry, $tipo=1) {

if ($tipo==1) {
       // Regresa la fecha en formato d/m/Y

       if (strpos($DateEntry,'/')) {
               $Date_Array = explode('/',$DateEntry);
       } elseif (strpos ($DateEntry,'-')) {
               $Date_Array = explode('-',$DateEntry);
       }

       if (strlen($Date_Array[2])>4) {  /*chop off the time stuff */
               $Date_Array[2]= substr($Date_Array[2],0,2);
       }
               
       Return $Date_Array[2].'/'.$Date_Array[1].'/'.$Date_Array[0];
       }
if ($tipo==2) {
       // Regresa la fecha en formato d/m/Y

       if (strpos($DateEntry,'/')) {
               $Date_Array = explode('/',$DateEntry);
       } elseif (strpos ($DateEntry,'-')) {
               $Date_Array = explode('-',$DateEntry);
       }

       if (strlen($Date_Array[2])>4) {  /*chop off the time stuff */
               $Date_Array[2]= substr($Date_Array[2],0,2);
       }
               
       Return $Date_Array[2].'     '.$Date_Array[1].'     '.$Date_Array[0];
       }


} // end function

?>