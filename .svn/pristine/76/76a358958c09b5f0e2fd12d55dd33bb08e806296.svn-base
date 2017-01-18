<?php

/* $Revision: 14 $ */

$PageSecurity = 2;

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
require_once('Numbers/Words.php');
require_once("includes/MiscFunctions.php");

//Get Out if we have no order number to work with
If (!isset($_GET['QuotationNo']) || $_GET['QuotationNo']==""){
        $title = _('Select Quotation To Print');
        include('includes/header.inc');
        echo '<div align=center><br><br><br>';
        prnMsg( _('Select a Quotation to Print before calling this page') , 'error');
        echo '<BR><BR><BR><table class="table_index"><tr><td class="menu_group_item">
                <li><a href="'. $rootpath . '/SelectSalesOrder.php?'. SID .'&Quotations=Quotes_Only">' . _('Quotations') . '</a></li>
                </td></tr></table></DIV><BR><BR><BR>';
        include('includes/footer.inc');
        exit();
}

/*retrieve the order details from the database to print */
$ErrMsg = _('There was a problem retrieving the quotation header details for Order Number') . ' ' . $_GET['QuotationNo'] . ' ' . _('from the database');

$sql = "SELECT salesorders.customerref, 
		salesorders.comments,
		salesorders.orddate,
		salesorders.deliverto,
		salesorders.deladd1,
		salesorders.deladd2,
		salesorders.deladd3,
		salesorders.deladd4,
		salesorders.deladd5,
		salesorders.deladd6,
		debtorsmaster.name,
       debtorsmaster.debtorno,
		debtorsmaster.address1,
		debtorsmaster.address2,
		debtorsmaster.address3,
		debtorsmaster.address4,
		debtorsmaster.address5,
		debtorsmaster.address6,
       debtorsmaster.address7,
       debtorsmaster.address8,
       debtorsmaster.address9,
       debtorsmaster.address10,
       debtorsmaster.taxref,
       debtorsmaster.currcode,
       custbranch.brname,
       custbranch.phoneno,
       custbranch.email,
		shippers.shippername,
		salesorders.printedpackingslip,
		salesorders.datepackingslipprinted,
		locations.locationname,
		locations.deladd1 AS locadd1,
		locations.deladd2 AS locadd2,
		locations.deladd3 AS locadd3,
		locations.deladd4 AS locadd4,
		locations.deladd5 AS locadd5,
		locations.tel AS loctel,
		locations.fax AS locfax,
		locations.email AS locemail,
		locations.contact AS loccontact,
		buyername
	FROM salesorders,
		debtorsmaster,
		shippers,
		locations,custbranch
	WHERE salesorders.debtorno=debtorsmaster.debtorno
    AND salesorders.branchcode=custbranch.branchcode
	AND salesorders.shipvia=shippers.shipper_id
	AND salesorders.fromstkloc=locations.loccode 
	AND salesorders.quotation=1 
	AND salesorders.orderno=" . $_GET['QuotationNo'];

$result=DB_query($sql,$db, $ErrMsg);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
        $title = _('Print Quotation Error');
        include('includes/header.inc');
         echo '<div align=center><br><br><br>';
        prnMsg( _('Unable to Locate Quotation Number') . ' : ' . $_GET['QuotationNo'] . ' ', 'error');
        echo '<BR><BR><BR><table class="table_index"><tr><td class="menu_group_item">
                <li><a href="'. $rootpath . '/SelectSalesOrder.php?'. SID .'&Quotations=Quotes_Only">' . _('Outstanding Quotations') . '</a></li>
                </td></tr></table></DIV><BR><BR><BR>';
        include('includes/footer.inc');
        exit;
} elseif (DB_num_rows($result)==1){ /*There is only one order header returned - thats good! */

        $myrow = DB_fetch_array($result);
        
}

/*retrieve the order details from the database to print */

/* Then there's an order to print and its not been printed already (or its been flagged for reprinting/ge_Width=807;
)
LETS GO */
//$PaperSize = 'A4_Landscape';
$PaperSize = 'A4';
include('includes/PDFStarter.php');

$FontSize=9;
$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->addinfo('Title', _('Customer Quotation') );
$pdf->addinfo('Subject', _('Quotation') . ' ' . $_GET['QuotationNo']);


$line_height=14;

/* Now ... Has the order got any line items still outstanding to be invoiced */

$PageNumber = 1;

$ErrMsg = _('There was a problem retrieving the quotation line details for quotation Number') . ' ' .
	$_GET['QuotationNo'] . ' ' . _('from the database');

$sql = "SELECT salesorderdetails.stkcode, 
		stockmaster.description, 
		salesorderdetails.quantity, 
		salesorderdetails.qtyinvoiced, 
		salesorderdetails.unitprice,
		salesorderdetails.discountpercent,
		salesorderdetails.narrative
	FROM salesorderdetails INNER JOIN stockmaster
		ON salesorderdetails.stkcode=stockmaster.stockid
	WHERE salesorderdetails.orderno=" . $_GET['QuotationNo'];
$result=DB_query($sql,$db, $ErrMsg);


$TaxTotalLines = getTaxTotalLinesForSO($_GET['QuotationNo']);
//echo"<pre>";
//print_r($TaxTotalLines);
//echo"</pre>";

//exit();

if (DB_num_rows($result)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('includes/PDFQuotationPageHeader.inc');
	
	$QuotationTotal =0;
	
    $LineNoTax = 0;
	while ($myrow2=DB_fetch_array($result)){

		if ((strlen($myrow2['narrative']) >200 AND $YPos-$line_height <= 75) 
			OR (strlen($myrow2['narrative']) >1 AND $YPos-$line_height <= 62) 
			OR $YPos-$line_height <= 210){
		/* We reached the end of the page so finsih off the page and start a newy */
            $pdf->line($Page_Width-$Right_Margin, 50,$Left_Margin, 50);
            //$pdf->addText(46, 53,6, 'MATRIZ: Carretera Nacional # 1525 Nte , Col centro C.P 67350 Allende N.L           SUCURSAL: Bonifacio Salinas #410 esquina con Antonio Plaza, Fracc. La Luz,C.P. 67129 Guadalupe N.L.');
            //$pdf->addText(46, 43,6, 'Tel. Y Fax. 01(826) 2682299                                                                                       Tel. y Fax.  01(81) 83948927 y 28');
            //$pdf->addText(230, 33,6, 'R.F.C TRA9001086M0  www.tractoref.com.mx');
			//$PageNumber++;
			include ('includes/PDFQuotationPageHeader.inc');

		} //end if need a new page headed up
		
		$DisplayQty = number_format($myrow2['quantity'],2);
		$DisplayPrevDel = number_format($myrow2['qtyinvoiced'],2);
		$DisplayPrice = number_format($myrow2['unitprice'],2);
		$DisplayDiscount = number_format($myrow2['discountpercent']*100,2) . '%';
		$LineTotal = $myrow2['unitprice']*$myrow2['quantity']*(1-$myrow2['discountpercent']);
        
        $TAXTotalLine = $TaxTotalLines[$LineNoTax];
        
		$DisplayTotal = number_format($LineTotal + $TAXTotalLine,2);

		$LeftOvers = $pdf->addTextWrap($XPos,$YPos,100,$FontSize,$myrow2['stkcode']);
		$LeftOvers = $pdf->addTextWrap(140,$YPos,170,$FontSize,$myrow2['description']);
		$LeftOvers = $pdf->addTextWrap(310,$YPos,60,$FontSize,$DisplayQty,'right');
		$LeftOvers = $pdf->addTextWrap(370,$YPos,60,$FontSize,$DisplayPrice,'right');
		$LeftOvers = $pdf->addTextWrap(430,$YPos,60,$FontSize,number_format($TAXTotalLine,2),'right');
		$LeftOvers = $pdf->addTextWrap(490,$YPos,65,$FontSize,$DisplayTotal,'right');
		if (strlen($myrow2['narrative'])>1){
			$YPos -= 10;
			$LeftOvers = $pdf->addTextWrap($XPos+1,$YPos,170,10,$myrow2['narrative']);
			if (strlen($LeftOvers>1)){
				$YPos -= 10;
				$LeftOvers = $pdf->addTextWrap($XPos+1,$YPos,170,10,$LeftOvers);
			}
		}
		$QuotationTotal +=$LineTotal;
		
		/*increment a line down for the next line item */
		$YPos -= ($line_height);
        $LineNoTax ++;
	} //end while there are line items to print out
	if ((strlen($myrow['comments']) >200 AND $YPos-$line_height <= 75) 
			OR (strlen($myrow['comments']) >1 AND $YPos-$line_height <= 62) 
			OR $YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			$PageNumber++;
			include ('includes/PDFQuotationPageHeader.inc');

	} //end if need a new page headed up
	
	$LeftOvers = $pdf->addTextWrap($XPos,$YPos,290,10,$myrow['comments']);

	if (strlen($LeftOvers)>1){
		$YPos -= 10;
		$LeftOvers = $pdf->addTextWrap($XPos,$YPos,290,10,$LeftOvers);
		if (strlen($LeftOvers)>1){
			$YPos -= 10;
			$LeftOvers = $pdf->addTextWrap($XPos,$YPos,290,10,$LeftOvers);
			if (strlen($LeftOvers)>1){
				$YPos -= 10;
				$LeftOvers = $pdf->addTextWrap($XPos,$YPos,290,10,$LeftOvers);
				if (strlen($LeftOvers)>1){
					$YPos -= 10;
					$LeftOvers = $pdf->addTextWrap($XPos,$YPos,10,$FontSize,$LeftOvers);
				}
			}
		}
	}
	$YPos -= ($line_height);
    $Letras = new Numbers_Words();
		if($myrow['currcode']=='MN'){
			$curr = ' pesos ';
		}else if($myrow['currcode']=='USD'){
			$curr = ' dolares ';
		}

		$sql = "SELECT currency,currabrev2 FROM currencies WHERE currabrev = '".$myrow['currcode']."'";
		$curr_res = DB_query($sql,$db);
		$currencystr = DB_fetch_array($curr_res);

		$tot = explode(".",number_format($QuotationTotal*1.16,2,'.',''));
		//$pdf->addTextWrap(250,81,81,10,$Total, 'right');
		$Letra = Numbers_Words::toWords($tot[0],"es");

		if($tot[1]==0){
		  $ConLetra = $Letra.' '.$currencystr['currency']." 00/100 ".$currencystr['currabrev2'];
		}else if(strlen($tot[1])>=2){
		  $ConLetra = $Letra.' '.$currencystr['currency'].' '.$tot[1]."/100 ".$currencystr['currabrev2'];
		}else {
		  $ConLetra = $Letra.' '.$currencystr['currency'].' '.$tot[1]."0/100 ".$currencystr['currabrev2'];
		}

    $LeftOvers = $pdf->addTextWrap(80,$YPos,250,$FontSize,'('.$ConLetra.')');
	$LeftOvers = $pdf->addTextWrap(430,$YPos,50,$FontSize,_('Subtotal'));
	$LeftOvers = $pdf->addTextWrap(505,$YPos,50,$FontSize,number_format($QuotationTotal,2),'right');
 	$LeftOvers = $pdf->addTextWrap(430,$YPos-10,50,$FontSize,_('IVA'));
	$LeftOvers = $pdf->addTextWrap(505,$YPos-10,50,$FontSize,number_format($QuotationTotal*0.16,2),'right');
    $LeftOvers = $pdf->addTextWrap(430,$YPos-20,50,$FontSize,_('Total'));
	$LeftOvers = $pdf->addTextWrap(505,$YPos-20,50,$FontSize,number_format($QuotationTotal*1.16,2),'right');


    $sqllogs = "SELECT realname,email FROM rh_usertrans join www_users on rh_usertrans.user_ = www_users.userid  WHERE order_ = ".$_GET['QuotationNo']." AND type = 30 order by date_ limit 1";
    $resultlogs = DB_query($sqllogs,$db);
    $rowlogs = DB_fetch_array($resultlogs);
    
    //Obtengo el el Ultimo Usuario en Modificar este Pedido
    $_2rh_translogs = "SELECT tl.*,us.realname FROM rh_translogs as tl 
    left join www_users us on us.userid = tl.user 
    WHERE typeno = ".$_GET['QuotationNo']." AND type = 30 order by date limit 1";
    $_rh_translogs = DB_query($_2rh_translogs,$db);
    $rh_translogs = DB_fetch_assoc($_rh_translogs);
    
    //print_r($_2rh_translogs);
    //exit;
    $YPos -= 80;
    $pdf->SetFont('Arial','',10);
    $pdf->addText(46, $YPos,$FontSize, utf8_decode('Me despido quedando a sus servicio para cualquier duda o aclaración'));
    $pdf->addText(46, $YPos-20,$FontSize, 'Atentamente');
    $pdf->addText(46, $YPos-40,$FontSize,  $rh_translogs['realname']);
    //$pdf->addText(46, $YPos-50,$FontSize,  $rowlogs['email']);
    $pdf->SetFont('Arial','B',10);
    $pdf->addText(46, $YPos-70,$FontSize, 'NOTA:');
    $pdf->SetFont('Arial','',10);
    $pdf->addText(46, $YPos-80,$FontSize, utf8_decode('Esta cotización está sujeta a cambio sin previo aviso'));
    $pdf->addText(46, $YPos-90,$FontSize, utf8_decode('Favor de enviar orden de compra aceptando la cotización'));
    $pdf->addText(46, $YPos-100,$FontSize, utf8_decode('La Garantía de los productos está sujeta a las póliticas del fabricante.'));
    $YPos -= 80;
    $pdf->line($Page_Width-$Right_Margin, 50,$Left_Margin, 50);
    //$pdf->addText(46, 53,6, 'MATRIZ: Carretera Nacional # 1525 Nte , Col centro C.P 67350 Allende N.L           SUCURSAL: Bonifacio Salinas #410 esquina con Antonio Plaza, Fracc. La Luz,C.P. 67129 Guadalupe N.L.');
    //$pdf->addText(46, 43,6, 'Tel. Y Fax. 01(826) 2682299                                                                                       Tel. y Fax.  01(81) 83948927 y 28');
    //$pdf->addText(230, 33,6, 'R.F.C TRA9001086M0  www.tractoref.com.mx');
} /*end if there are line details to show on the quotation*/


$pdfcode = $pdf->output();
$len = strlen($pdfcode);
if ($len<=20){
        $title = _('Print Quotation Error');
        include('includes/header.inc');
        echo '<p>'. _('There were no items on the quotation') . '. ' . _('The quotation cannot be printed').
                '<BR><A HREF="' . $rootpath . '/SelectSalesOrder.php?' . SID . '&Quotation=Quotes_only">'. _('Print Another Quotation').
                '</A>' . '<BR>'. '<A HREF="' . $rootpath . '/index.php?' . SID . '">' . _('Back to the menu') . '</A>';
        include('includes/footer.inc');
	exit;
} else {
	header('Content-type: application/pdf');
	header('Content-Length: ' . $len);
	header('Content-Disposition: inline; filename=Quotation.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
//echo 'here';
	$pdf->Stream();

}

?>
