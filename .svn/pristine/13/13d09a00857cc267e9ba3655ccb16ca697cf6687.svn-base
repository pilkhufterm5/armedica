<?php

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-10-02 12:30:15 -0500 (Thu, 02 Oct 2008) $
 * $Rev: 420 $
 */
// BOWIKAXU - REALHOST MARCH 2007 - IMPRESION DE FACTURAS

$PageSecurity = 1;

include('includes/session.inc');
require_once('Numbers/Words.php');
//include('includes/numero2letra.php');

if (isset($_GET['FromTransNo'])){
	$FromTransNo = $_GET['FromTransNo'];
} elseif (isset($_POST['FromTransNo'])){
	$FromTransNo = $_POST['FromTransNo'];
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

// bowikaxu realhost sept 07 - si se envio alguna factura externa tomarla como prioridad
if(isset($_POST['ExtTrans']) && $_POST['ExtTrans']>0){
	
//	if ($InvOrCredit=='notaC'){ //Notas de Cargo aunque por lo pronto las notas de cargo no tienen numero externo
//		$sql = "SELECT transno FROM debtortrans WHERE consignment = '".$_POST['ExtTrans']."'";
//		$res = DB_query($sql,$db,"ERROR: Imposible obtener datos de la factura");
//		$inv = DB_fetch_array($res);
//
//		$FromTransNo = $inv['transno'];
//		$_POST['ToTransNo'] = $inv['transno'];
//
//	}else{
            if ($InvOrCredit=='Credit') //Busqueda por numero externo de notas de credito
            {
		$sql = "SELECT intcn FROM rh_crednotesreference WHERE extcn = '".$_POST['ExtTrans']."' AND loccode = '".$_POST['loccode']."'";
		$res = DB_query($sql,$db,"ERROR: Imposible obtener datos de la Nota de Credito");
		$inv = DB_fetch_array($res);
		
		$FromTransNo = $inv['intcn'];
		$_POST['ToTransNo'] = $inv['intcn'];
            }
            else //Busqueda por numero externo de facturas
            {
                $sql = "SELECT intinvoice FROM rh_invoicesreference WHERE extinvoice = '".$_POST['ExtTrans']."' AND loccode = '".$_POST['loccode']."'";
		$res = DB_query($sql,$db,"ERROR: Imposible obtener datos de la factura");
		$inv = DB_fetch_array($res);

		$FromTransNo = $inv['intinvoice'];
		$_POST['ToTransNo'] = $inv['intinvoice'];
            }
	//}
}

// bowikaxu realhost - si es mail enviar con imagen
if (isset($_GET['Email'])){
	$PrintPDF = "Imprimir PDF (imagen)";
}

If (!isset($_POST['ToTransNo']) 
	OR trim($_POST['ToTransNo'])==''
	OR $_POST['ToTransNo'] < $FromTransNo){
	
	$_POST['ToTransNo'] = $FromTransNo;
}

$FirstTrans = $FromTransNo; /*Need to start a new page only on subsequent transactions */

/* iJPe
 * realhost
 * 2010-01-15
 */
if ($InvOrCredit=='Invoice'){
	$rh_tipo = 10;
}else{
	if ($InvOrCredit=='notaC'){
		$rh_tipo = 20001;
	}else{
		$rh_tipo = 11;
	}
}

If (isset($PrintPDF) 
	AND $PrintPDF!='' 
	AND isset($FromTransNo) 
	AND isset($InvOrCredit) 
	AND $FromTransNo!=''){

	if($InvOrCredit=='Invoice' || $InvOrCredit=='notaC'){
            // bowikaxu - seleccionar y buscar el template a usar
            $SQL = "SELECT salesorders.fromstkloc_virtual,
                            debtortrans.transno,
                            rh_locations.rh_template
                            FROM salesorders, debtortrans, rh_locations
                            WHERE debtortrans.transno = ".$FromTransNo."
                            AND type = $rh_tipo
                            AND debtortrans.order_ = salesorders.orderno
                            AND rh_locations.loccode = salesorders.fromstkloc_virtual";
            $Res = DB_query($SQL,$db);
            $Template = DB_fetch_array($Res);
            switch($Template['rh_template']){

                /*
                 * 1 Mty
                 * 2 Mty Contado
                 * 3 Saltillo
                 * 4 Saltillo Contado
                 * 5 Ramos
                 * 6 Ramos Contado
                 */


                    case 1:
                            include('templates/rh_template1.php');
                            break;
                    case 2:
                            include('templates/rh_template2.php');
                            break;
                    case 3:
                            include ('templates/rh_templateSaltillo.php');
                            break;
                    case 4:
                            include ('templates/rh_templateSaltilloContado.php');
                            break;
                    case 5:
                            include ('templates/rh_templateRamos.php');
                            break;
                    case 6:
                            include ('templates/rh_templateRamosContado.php');
                            break;
                    default:
                            include('templates/rh_template1.php');
                            break;
            }
        }else { // notas de credito
                    include('templates/rh_notcred1.php');
        }

} else { /*The option to print PDF was not hit */
	
	$title=_('Seleccionar Facturas/Notas de Credito/Notas de Cargo a Imprimir');
	include('includes/header.inc');

	if (!isset($FromTransNo) OR $FromTransNo=='') {


	/*if FromTransNo is not set then show a form to allow input of either a single invoice number or a range of invoices to be printed. Also get the last invoice number created to show the user where the current range is up to */

		echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID . "' METHOD='POST'><CENTER><TABLE>";

		echo '<TR><TD>' . _('Print Invoices or Credit Notes') . '</TD><TD><SELECT name=InvOrCredit>';
		if ($InvOrCredit=='Credit'){
			echo "<OPTION SELECTED VALUE='Credit'>" . _('Credit Notes');
			echo "<OPTION VALUE='Invoice'>" . _('Invoices');		   
			echo "<OPTION VALUE='notaC'>" . _('Notas de Cargo');		   

		} else {

			if ($InvOrCredit=='Invoice' OR !isset($InvOrCredit)){
				echo "<OPTION SELECTED VALUE='Invoice'>" . _('Invoices');
				echo "<OPTION VALUE='Credit'>" . _('Credit Notes');
				echo "<OPTION VALUE='notaC'>" . _('Notas de Cargo');	
			}else{
				echo "<OPTION VALUE='Credit'>" . _('Credit Notes');
				echo "<OPTION VALUE='Invoice'>" . _('Invoices');
				echo "<OPTION SELECTED VALUE='notaC'>" . _('Notas de Cargo');	
			}
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
		echo '<TR><TD>' . _('Start invoice/credit note number to print') . "</TD><TD><input Type=text max=6 size=7 name=FromTransNo></TD>
		
		<TD>"._('Location')."</TD><TD><SELECT NAME=loccode>";
		$sql = "SELECT loccode, locationname FROM rh_locations";
		$res = DB_query($sql,$db,'ERROR: Imposible obtener sucursales');
		while($loc = DB_fetch_array($res)){
			echo "<OPTION VALUE='".$loc['loccode']."'>".$loc['locationname'];
		}
		
		echo "</SELECT></TD>
		</TR>";
		echo '<TR><TD>' . _('End invoice/credit note number to print') . "</TD><TD><input Type=text max=6 size=7 name='ToTransNo'></TD>
		<TD>"._('Factura/Nota de Credito Externa').":</TD><TD><input Type=text max=6 size=7 name='ExtTrans'></TD>
		</TR>";
		
		echo "</TABLE></CENTER>";
		echo "<CENTER><INPUT TYPE=Submit Name='Print' Value='" . _('Print') . "'><P>";
		//echo "<INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') . "'><P>";
		//echo "<INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Imprimir PDF (imagen)') . "'>";
		echo '</CENTER>';
		$sql = 'SELECT typeno FROM systypes WHERE typeid=10';

		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);

		echo '<P>' . _('The last invoice created was number') . ' ' . $myrow[0] . '<BR>' . _('If only a single invoice is required') . ', ' . _('enter the invoice number to print in the Start transaction number to print field and leave the End transaction number to print field blank') . '. ' . _('Only use the end invoice to print field if you wish to print a sequential range of invoices');

		$sql = 'SELECT typeno FROM systypes WHERE typeid=11';

		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);

		echo '<P>' . _('The last credit note created was number') . ': ' . $myrow[0] . '<BR>' . _('A sequential range can be printed using the same method as for invoices above') . '. ' . _('A single credit note can be printed by only entering a start transaction number');
		
		$sql = 'SELECT typeno FROM systypes WHERE typeid=20001';
		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);

		echo '<P>' . _('La ultima nota de cargo fue:') . ' ' . $myrow[0] . '<BR>';
		

	} else {

		while ($FromTransNo <= $_POST['ToTransNo']){

	/*retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */

			if ($InvOrCredit=='Invoice' || $InvOrCredit=='notaC') {

				$sql = "SELECT debtorno FROM rh_transaddress WHERE type = $rh_tipo AND transno = ".$FromTransNo."";
				$res_address = DB_query($sql,$db);
				if(DB_num_rows($res_address)>0){
			//SAINTS modificación de consulta para obtener series y folios de FE 28/01/2011
			   $sql = "SELECT
			   		debtortrans.trandate,		SUBSTRING(DATE_FORMAT(debtortrans.trandate, '%M'),1,3) as mm,	DATE_FORMAT(debtortrans.trandate, '%d') as dd, 	DATE_FORMAT(debtortrans.trandate, '%Y') as yy,
					debtortrans.ovamount,
					c.serie,
					c.folio,
					debtortrans.id as ID,
					debtortrans.ovdiscount,
					debtortrans.ovfreight,
					debtortrans.ovgst,
					debtortrans.rate,
					debtortrans.invtext,
					debtortrans.consignment,
					debtorsmaster.name AS name_old,
					debtorsmaster.name2 AS name2_old,
					debtorsmaster.address1 AS address1_old,
					debtorsmaster.address2 AS address2_old,
					debtorsmaster.address3 AS address3_old,
					debtorsmaster.address4 AS address4_old,
					debtorsmaster.address5 AS address5_old,
					debtorsmaster.address6 AS address6_old,

					rh_transaddress.name,
					rh_transaddress.name2,
					rh_transaddress.address1,
					rh_transaddress.address2,
					rh_transaddress.address3,
					rh_transaddress.address4,
					rh_transaddress.address5,
					rh_transaddress.address6,
					rh_transaddress.taxref,

					debtorsmaster.currcode,
					debtorsmaster.invaddrbranch,
					debtorsmaster.taxref AS taxref_old,
					salesorders.deliverto,
					salesorders.deladd1,
					salesorders.deladd2,
					salesorders.deladd3,
					salesorders.deladd4,
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
				FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id,
					debtorsmaster,
					custbranch,
					salesorders,
					shippers,
					salesman,
					rh_transaddress
				WHERE debtortrans.order_ = salesorders.orderno
				AND debtortrans.type=$rh_tipo
				AND rh_transaddress.type = $rh_tipo
				AND rh_transaddress.transno = debtortrans.transno
				AND debtortrans.transno=" . $FromTransNo . "
				AND debtortrans.shipvia=shippers.shipper_id
				AND debtortrans.debtorno=debtorsmaster.debtorno
				AND debtortrans.debtorno=custbranch.debtorno
				AND debtortrans.branchcode=custbranch.branchcode
				AND custbranch.salesman=salesman.salesmancode";                                

			}else {
				 $sql = "SELECT
			   		debtortrans.trandate,		SUBSTRING(DATE_FORMAT(trandate, '%M'),1,3) as mm,	DATE_FORMAT(trandate, '%d') as dd, 	DATE_FORMAT(trandate, '%Y') as yy,
					debtortrans.ovamount, 
					c.serie,
					c.folio,
					debtortrans.id as ID,
					debtortrans.ovdiscount, 
					debtortrans.ovfreight, 
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
					debtorsmaster.taxref, 
					salesorders.deliverto, 
					salesorders.deladd1, 
					salesorders.deladd2, 
					salesorders.deladd3, 
					salesorders.deladd4, 
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
				FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id, 
					debtorsmaster, 
					custbranch, 
					salesorders, 
					shippers, 
					salesman 
				WHERE debtortrans.order_ = salesorders.orderno 
				AND debtortrans.type=$rh_tipo
				AND debtortrans.transno=" . $FromTransNo . "
				AND debtortrans.shipvia=shippers.shipper_id 
				AND debtortrans.debtorno=debtorsmaster.debtorno 
				AND debtortrans.debtorno=custbranch.debtorno 
				AND debtortrans.branchcode=custbranch.branchcode 
				AND custbranch.salesman=salesman.salesmancode";
			}
			} else {

			   $sql = 'SELECT debtortrans.trandate,		SUBSTRING(DATE_FORMAT(trandate, "%M"),1,3) as mm,	DATE_FORMAT(trandate, "%d") as dd, 	DATE_FORMAT(trandate, "%Y") as yy,
			   		debtortrans.ovamount, 
			   		c.serie,
					c.folio,
			   		debtortrans.id as ID,
					debtortrans.ovdiscount, 
					debtortrans.ovfreight, 
					debtortrans.ovgst, 
					debtortrans.rate, 
					debtortrans.invtext, 
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
					custbranch.braddress1, 
					custbranch.braddress2, 
					custbranch.braddress3, 
					custbranch.braddress4, 
					salesman.salesmanname, 
					debtortrans.debtorno
				FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id,
					debtorsmaster,
					custbranch,
					salesman 
				WHERE debtortrans.type=11
				AND debtortrans.transno=' . $FromTransNo . '
				AND debtortrans.debtorno=debtorsmaster.debtorno
				AND debtortrans.debtorno=custbranch.debtorno 
				AND debtortrans.branchcode=custbranch.branchcode 
				AND custbranch.salesman=salesman.salesmancode';
			//SAINTS fin
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
				
				/*$ID = $myrow['ID'];
				$sql2 = "SELECT rh_invoicesreference.extinvoice, rh_locations.rh_serie FROM rh_invoicesreference, rh_locations WHERE rh_invoicesreference.ref = ".$ID."
				AND rh_locations.loccode = rh_invoicesreference.loccode";
                */

                //*******************Cambio a FE electronica******************************************
                if ($InvOrCredit=='Invoice') {
                    $sql2 = "SELECT serie, folio, sello, cadena_original FROM rh_cfd__cfd,stockmoves WHERE stockmoves.transno = rh_cfd__cfd.fk_transno and rh_cfd__cfd.id_systypes=10 and stockmoves.transno='".$FromTransNo."'";
                }else{
                    $sql2 = "SELECT serie, folio, sello, cadena_original FROM rh_cfd__cfd,stockmoves WHERE stockmoves.transno = rh_cfd__cfd.fk_transno and rh_cfd__cfd.id_systypes=11 and stockmoves.transno='".$FromTransNo."'";
                }
				$Res = DB_query($sql2,$db);
				$ExtRes = DB_fetch_array($Res);

				echo "<TABLE WIDTH=100%><TR><TD VALIGN=TOP WIDTH=10%><img src='companies/" . $_SESSION['DatabaseName'] . "/logo.jpg'></TD><TD BGCOLOR='#BBBBBB'><B>";

				if ($InvOrCredit=='Invoice') {
				   echo '<FONT SIZE=4>' . _('TAX INVOICE') . ' ';
				} else {
					if ($InvOrCredit=='notaC') {
						echo '<FONT SIZE=4>' . _('NOTA DE CARGO') . ' ';
					}else{
						echo '<FONT COLOR=RED SIZE=4>' . _('TAX CREDIT NOTE') . ' ';
					}
				}
				echo '</B>' . _('Number') . ' ' . $ExtRes['serie'].' '.$ExtRes['folio'] . '('.$FromTransNo.') </FONT><BR><FONT SIZE=1>' . _('Tax Authority Ref') . '. ' . $_SESSION['CompanyRecord']['gstno'] . '</TD></TR></TABLE>';

	/*Now print out the logo and company name and address */
				echo "<TABLE WIDTH=100%><TR><TD><FONT SIZE=4 COLOR='#333333'><B>" . $_SESSION['CompanyRecord']['coyname'] . "</B></FONT><BR>";
				echo $_SESSION['CompanyRecord']['postaladdress'] . '<BR>';
				echo $_SESSION['CompanyRecord']['regoffice1'] . '<BR>';
				echo $_SESSION['CompanyRecord']['regoffice2'] . '<BR>';
				echo _('Telephone') . ': ' . $_SESSION['CompanyRecord']['telephone'] . '<BR>';
				echo _('Facsimile') . ': ' . $_SESSION['CompanyRecord']['fax'] . '<BR>';
				echo _('Email') . ': ' . $_SESSION['CompanyRecord']['email'] . '<BR>';

				echo '</TD><TD WIDTH=50% ALIGN=RIGHT>';

	/*Now the customer charged to details in a sub table within a cell of the main table*/

				echo "<TABLE WIDTH=100%><TR><TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Charge To') . ":</B></TD></TR><TR><TD BGCOLOR='#EEEEEE'>";
				echo $myrow['name'] . '<BR>' . $myrow['address1'] . '<BR>' . $myrow['address2'] . '<BR>' . $myrow['address3'] . '<BR>' . $myrow['address4'] . '<BR>' . $myrow['taxref'];
				echo '</TD></TR></TABLE>';
				/*end of the small table showing charge to account details */
				echo _('Page') . ': ' . $PageNumber;
				echo '</TD></TR></TABLE>';
				/*end of the main table showing the company name and charge to details */

				if ($InvOrCredit=='Invoice' || $InvOrCredit=='notaC') {

				   echo "<TABLE WIDTH=100%>
				   			<TR>
				   				<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Charge Branch') . ":</B></TD>
								<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Delivered To') . ":</B></TD>
							</TR>";
				   echo "<TR>
				   		<TD BGCOLOR='#EEEEEE'>" .$myrow['brname'] . '<BR>' . $myrow['braddress1'] . '<BR>' . $myrow['braddress2'] . '<BR>' . $myrow['braddress3'] . '<BR>' . $myrow['braddress4'] . '</TD>';

				   	echo "<TD BGCOLOR='#EEEEEE'>" . $myrow['deliverto'] . '<BR>' . $myrow['deladd1'] . '<BR>' . $myrow['deladd2'] . '<BR>' . $myrow['deladd3'] . '<BR>' . $myrow['deladd4'] . '</TD>';
				   echo '</TR>
				   </TABLE><HR>';
				   
				   echo "<TABLE WIDTH=100%>
				   		<TR>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Your Order Ref') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Factura Externa') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Our Order No') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Order Date') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Invoice Date') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Sales Person') . "</FONT></B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Shipper') . "</B></TD>
							<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Consignment Ref') . "</B></TD>
						</TR>";
				   	echo "<TR>
							<TD BGCOLOR='#EEEEEE'>" . $myrow['customerref'] . "</TD>
							<TD BGCOLOR='#EEEEEE'>" . $ExtRes['serie'].$ExtRes['folio'] . "</TD>
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
					AND stockmoves.type='.$rh_tipo.'
					AND stockmoves.transno=' . $FromTransNo . '
					AND stockmoves.show_on_inv_crds=1';

				} else { /* then its a credit note */

				   echo "<TABLE WIDTH=50%><TR>
				   		<TD ALIGN=LEFT BGCOLOR='#BBBBBB'><B>" . _('Branch') . ":</B></TD>
						</TR>";
				   echo "<TR>
				   		<TD BGCOLOR='#EEEEEE'>" .$myrow['brname'] . '<BR>' . $myrow['braddress1'] . '<BR>' . $myrow['braddress2'] . '<BR>' . $myrow['braddress3'] . '<BR>' . $myrow['braddress4'] . '</TD>
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
						   echo "</TABLE><TABLE WIDTH=100%><TR><TD VALIGN=TOP><img src='logo.jpg'></TD><TD BGCOLOR='#BBBBBB'><CENTER><B>";

						   if ($InvOrCredit=='Invoice') {
							    echo '<FONT SIZE=4>' . _('TAX INVOICE') . ' ';
						   } else {
								if ($InvOrCredit=='notaC') {
									echo '<FONT SIZE=4>' . _('NOTA DE CARGO') . ' ';
								}else{
									echo '<FONT COLOR=RED SIZE=4>' . _('TAX CREDIT NOTE') . ' ';
								}							    
						   }
						   echo '</B>' . _('Number') . ' ' . $FromTransNo . '</FONT><BR><FONT SIZE=1>' . _('GST Number') . ' - ' . $_SESSION['CompanyRecord']['gstno'] . '</TD></TR><TABLE>';

	/*Now print out company name and address */
						    echo "<TABLE WIDTH=100%><TR>
						    	<TD><FONT SIZE=4 COLOR='#333333'><B>" . $_SESSION['CompanyRecord']['coyname'] . '</B></FONT><BR>';
						    echo $_SESSION['CompanyRecord']['postaladdress'] . '<BR>';
						    echo $_SESSION['CompanyRecord']['regoffice1'] . '<BR>';
						    echo $_SESSION['CompanyRecord']['regoffice2'] . '<BR>';
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
					echo "<TABLE WIDTH=100%><TR><TD VALIGN=TOP><img src='logo.jpg'></TD><TD BGCOLOR='#BBBBBB'><CENTER><B>";

					if ($InvOrCredit=='Invoice') {
					      echo '<FONT SIZE=4>' . _('TAX INVOICE') .' ';
					} else {
						if ($InvOrCredit=='notaC') {
							echo '<FONT SIZE=4>' . _('NOTA DE CARGO') . ' ';
						}else{
							echo '<FONT COLOR=RED SIZE=4>' . _('TAX CREDIT NOTE') . ' ';
						}					    
					}
					echo '</B>' . _('Number') . ' ' . $FromTransNo . '</FONT><BR><FONT SIZE=1>' . _('GST Number') . ' - ' . $_SESSION['CompanyRecord']['gstno'] . '</TD></TR><TABLE>';

	/*Print out the logo and company name and address */
					echo "<TABLE WIDTH=100%><TR><TD><FONT SIZE=4 COLOR='#333333'><B>" . $_SESSION['CompanyRecord']['coyname'] . "</B></FONT><BR>";
					echo $_SESSION['CompanyRecord']['postaladdress'] . '<BR>';
					echo $_SESSION['CompanyRecord']['regoffice1'] . '<BR>';
					echo $_SESSION['CompanyRecord']['regoffice2'] . '<BR>';
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

				if ($InvOrCredit=='Invoice' || $InvOrCredit=='notaC') {

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
				if ($InvOrCredit=='Invoice' || $InvOrCredit=='notaC'){
				     echo '<TR><TD Align=RIGHT><B>' . _('TOTAL INVOICE') . "</B></TD>
				     	<TD ALIGN=RIGHT BGCOLOR='#EEEEEE'><U><B>$DisplayTotal</B></U></TD></TR>";
				} else {
				     echo '<TR><TD Align=RIGHT><FONT COLOR=RED><B>' . _('TOTAL CREDIT') . "</B></FONT></TD>
				     		<TD ALIGN=RIGHT BGCOLOR='#EEEEEE'><FONT COLOR=RED><U><B>$DisplayTotal</B></U></FONT></TD></TR>";
				}
				echo '</TABLE>';
                                        //Jaime agregado    // No servia
                                            echo "<table>
                                                    <tr>
                                                        <td bgcolor=\"#bbbbbb\">
                                                            <b>
                                                                Cadena original:
                                                            </b>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            ".$ExtRes['cadena_original']."
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td bgcolor=\"#bbbbbb\">
                                                            <b>
                                                                Sello digital:
                                                            </b>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            ".$ExtRes['sello']."
                                                        </td>
                                                    </tr>
                                                </table>";
                                        //termina Jaime agregado //No servia
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

                                    echo "<h4>"._('Transacciones asignadas a la Factura '.$ExtRes['rh_serie'].$ExtRes['extinvoice'].'('.$FromTransNo.')')."</h4>";

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
                                    echo "<h4>"._('No hay transacciones asignadas a la Factura '.$ExtRes['rh_serie'].$ExtRes['extinvoice'].'('.$FromTransNo.')')."</h4>";
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

	
/*draw the vertical column lines right to the bottom */
	///$pdf->line($Left_Margin+97, $TopOfColHeadings+12,$Left_Margin+97,$Bottom_Margin);

	/*Print a column vertical line */
	///$pdf->line($Left_Margin+350, $TopOfColHeadings+12,$Left_Margin+350,$Bottom_Margin);

	/*Print a column vertical line */
	///$pdf->line($Left_Margin+450, $TopOfColHeadings+12,$Left_Margin+450,$Bottom_Margin);

	/*Print a column vertical line */
	///$pdf->line($Left_Margin+550, $TopOfColHeadings+12,$Left_Margin+550,$Bottom_Margin);

	/*Print a column vertical line */
	///$pdf->line($Left_Margin+587, $TopOfColHeadings+12,$Left_Margin+587,$Bottom_Margin);

	///$pdf->line($Left_Margin+640, $TopOfColHeadings+12,$Left_Margin+640,$Bottom_Margin);
	
	$pdf->newPage();
	$PageNumber++;

}

?>
