<?php

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */

$PageSecurity = 2;

include('includes/session.inc');
include('includes/class.pdf.php');
include('includes/SQL_CommonFunctions.inc');

include('includes/DefineCartClass.php');
include('includes/DefineSerialItems.php');

//Get Out if we have no order number to work with
If (!isset($_GET['TransNo']) || $_GET['TransNo']==""){
	$title = _('Select Order To Print');
	include('includes/header.inc');
	echo '<DIV ALIGN=CENTER><BR><BR><BR>';
	prnMsg( _('Select an Order Number to Print before calling this page') , 'error');
	echo '<BR><BR><BR><table class="table_index"><TR><TD CLASS="menu_group_item">
		<LI><A HREF="'. $rootpath . '/SelectSalesOrder.php?'. SID .'">' . _('Outstanding Sales Orders') . '</A></LI>
		<LI><A HREF="'. $rootpath . '/SelectCompletedOrder.php?'. SID .'">' . _('Completed Sales Orders') . '</A></LI>
		</TD></TR></TABLE></DIV><BR><BR><BR>';
	include('includes/footer.inc');
	exit;
}

//Session_register('Items');
$_SESSION['Items'] = new cart;

/*retrieve the order details from the database to print */
$ErrMsg = _('There was a problem retrieving the order header details for Order Number') . ' ' . $_GET['TransNo'] . ' ' . _('from the database');
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
		salesorders.debtorno,
		salesorders.branchcode,
		salesorders.rh_status,
		debtorsmaster.name,
		debtorsmaster.name2,
		debtorsmaster.taxref,
		debtorsmaster.address1,
		debtorsmaster.address2,
		debtorsmaster.address3,
		debtorsmaster.address4,
		debtorsmaster.address5,
		debtorsmaster.address6,
		debtorsmaster.rh_Tel,
		
		locations.taxprovinceid,
		custbranch.taxgroupid,
		custbranch.phoneno,
		shippers.shippername,
		salesorders.printedpackingslip,
		salesorders.datepackingslipprinted,
		locations.locationname
	FROM salesorders INNER JOIN debtorsmaster
		ON salesorders.debtorno=debtorsmaster.debtorno
	INNER JOIN shippers
		ON salesorders.shipvia=shippers.shipper_id
	INNER JOIN locations
		ON salesorders.fromstkloc=locations.loccode
	INNER JOIN custbranch
		ON salesorders.branchcode = custbranch.branchcode
		AND salesorders.debtorno = custbranch.debtorno
	WHERE salesorders.orderno=" . $_GET['TransNo']."";

$result=DB_query($sql,$db, $ErrMsg);

$sql = "SELECT user_ FROM rh_usertrans WHERE order_=".$_GET['TransNo']."";
$res = DB_query($sql,$db);
$user_ = DB_fetch_array($res);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$title = _('Print Packing Slip Error');
        include('includes/header.inc');
        echo '<div align=center><br><br><br>';
	prnMsg( _('Unable to Locate Order Number') . ' : ' . $_GET['TransNo'] . ' ', 'error');
        echo '<BR><BR><BR><TABLE class="table_index"><TR><TD class="menu_group_item">
                <LI><A HREF="'. $rootpath . '/SelectSalesOrder.php?'. SID .'">' . _('Outstanding Sales Orders') . '</A></LI>
                <LI><A HREF="'. $rootpath . '/SelectCompletedOrder.php?'. SID .'">' . _('Completed Sales Orders') . '</A></LI>
                </TD></TR></TABLE></DIV><BR><BR><BR>';
        include('includes/footer.inc');
        exit();
} elseif (DB_num_rows($result)==1){ /*There is only one order header returned - thats good! */

	$myrow = DB_fetch_array($result);
	if ($myrow['printedpackingslip']==1 AND ($_GET['Reprint']!='OK' OR !isset($_GET['Reprint']))){
		$title = _('Print Packing Slip Error');
	      	include('includes/header.inc');
		echo '<P>';
		prnMsg( _('The packing slip for order number') . ' ' . $_GET['TransNo'] . ' ' .
			_('has previously been printed') . '. ' . _('It was printed on'). ' ' . ConvertSQLDate($myrow['datepackingslipprinted']) .
			'<br>' . _('This check is there toensure that duplicate packing slips are not produced and dispatched more than once to the customer'), 'warn' );

			echo '<P><A HREF="' . $rootpath . '/rh_PrintCustOrder.php?' . SID . 'TransNo=' . $_GET['TransNo'] . '&Reprint=OK">'
		. _('Re-Imprimir') . ' (' . _('Formato Vertical') . ') ' . '</A><P>' .
		'<A HREF="' . $rootpath. '/PrintCustOrder_generic.php?' . SID . 'TransNo=' . $_GET['TransNo'] . '&Reprint=OK">'. _('Do a Re-Print') . ' (' . _('Plain paper') . ' - ' . _('A4') . ' ' . _('landscape') . ') ' . _('Even Though Previously Printed'). '</A>';

		echo '<BR><BR><BR>';
		echo  _('Or select another Order Number to Print');
	        echo '<table class="table_index"><tr><td class="menu_group_item">
        	        <li><a href="'. $rootpath . '/SelectSalesOrder.php?'. SID .'">' . _('Outstanding Sales Orders') . '</a></li>
                	<li><a href="'. $rootpath . '/SelectCompletedOrder.php?'. SID .'">' . _('Completed Sales Orders') . '</a></li>
	                </td></tr></table></DIV><BR><BR><BR>';

      		include('includes/footer.inc');
		exit;
   	}//packing slip has been printed.
}
/* Then there's an order to print and its not been printed already (or its been flagged for reprinting)
LETS GO */


/* Now ... Has the order got any line items still outstanding to be invoiced */

$PageNumber = 1;
$ErrMsg = _('There was a problem retrieving the details for Order Number') . ' ' . $_GET['TransNo'] . ' ' . _('from the database');
/*
$sql = "SELECT salesorderdetails.stkcode,
		stockmaster.description,
		stockmaster.longdescription,
		salesorderdetails.orderlineno,
		salesorderdetails.quantity,
		salesorderdetails.qtyinvoiced,
		salesorderdetails.unitprice
	FROM salesorderdetails INNER JOIN stockmaster
		ON salesorderdetails.stkcode=stockmaster.stockid
	 WHERE salesorderdetails.orderno=" . $_GET['TransNo'];
*/
$sql = 'SELECT stkcode,
					stockmaster.description,
					stockmaster.controlled,
					stockmaster.serialised,
					stockmaster.longdescription,
					stockmaster.volume,
					stockmaster.kgs,
					stockmaster.units,
					stockmaster.decimalplaces,
					stockmaster.mbflag,
					stockmaster.taxcatid,
					stockmaster.discountcategory,
					salesorderdetails.unitprice,
					(salesorderdetails.quantity - salesorderdetails.qtyinvoiced) AS quantity,
					salesorderdetails.discountpercent,
					salesorderdetails.actualdispatchdate,
					salesorderdetails.qtyinvoiced,
					salesorderdetails.narrative,
					salesorderdetails.orderlineno,
					stockmaster.materialcost + 
						stockmaster.labourcost + 
						stockmaster.overheadcost AS standardcost
				FROM salesorderdetails INNER JOIN stockmaster
				 	ON salesorderdetails.stkcode = stockmaster.stockid
				WHERE salesorderdetails.orderno =' . $_GET['TransNo'] . '
				AND salesorderdetails.quantity - salesorderdetails.qtyinvoiced >0 
				ORDER BY salesorderdetails.orderlineno';

$result=DB_query($sql, $db, $ErrMsg);

if (DB_num_rows($result)>0){
/*Yes there are line items to start the ball rolling with a page header */

	/*Set specifically for the stationery being used -needs to be modified for clients own
	packing slip 2 part stationery is recommended so storeman can note differences on and
	a copy retained */

	$Page_Width=612; // horizontal
	$Page_Height=792; // vertical
	$Top_Margin=10;
	$Bottom_Margin=20;
	$Left_Margin=10;
	$Right_Margin=10;


	$PageSize = array(0,0,$Page_Width,$Page_Height);
	$pdf =  new Cpdf($PageSize);
	$FontSize=12;
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->addinfo('Author','webERP ' . $Version);
	$pdf->addinfo('Creator','webERP http://www.weberp.org - R&OS PHP-PDF http://www.ros.co.nz');
	$pdf->addinfo('Title', _('Customer Packing Slip') );
	$pdf->addinfo('Subject', _('Packing slip for order') . ' ' . $_GET['TransNo']);

	$line_height=16;

	include('includes/rh_PDFOrderPageHeader.inc');
	$FontSize = 9;
	// NOMBRES COLUMNAS
	$LeftOvers = $pdf->addTextWrap(35,540,50,$FontSize,_('CANT.'),'left');
	$LeftOvers = $pdf->addTextWrap(100,540,50,$FontSize,_('CODIGO'),'left');
	$LeftOvers = $pdf->addTextWrap(150,540,250,$FontSize,_('DESCRIPCION'),'left');
	$LeftOvers = $pdf->addTextWrap(450,540,50,$FontSize,_('PRECIO'),'right');
	$LeftOvers = $pdf->addTextWrap(500,540,50,$FontSize,_('TOTAL'),'right');
	$YPos -= ($line_height);
	$line_height = 14;
	$TOTFINAL = 0;
	$IVA = 0;
	
	while ($myrow2=DB_fetch_array($result)){
		
		$TaxLineTotal =0;
		$LineTotal = $myrow2['unitprice']*$myrow2['quantity'];
		// get taxes for this article
		$_SESSION['Items']->TaxGroup = $myrow['taxgroupid'];
		$_SESSION['Items']->DispatchTaxProvince = $myrow['taxprovinceid'];
		
		$_SESSION['Items']->add_to_cart($myrow2['stkcode'],
						$myrow2['quantity'],
						$myrow2['description'],
						$myrow2['unitprice'],
						$myrow2['discountpercent'],
						$myrow2['units'],
						$myrow2['volume'],
						$myrow2['kgs'],
						0,
						$myrow2['mbflag'],
						$myrow2['actualdispatchdate'],
						$myrow2['qtyinvoiced'],
						$myrow2['discountcategory'],
						$myrow2['controlled'],
						$myrow2['serialised'],
						$myrow2['decimalplaces'],
						$myrow2['narrative'],
						'No',
						$myrow2['orderlineno'],
						$myrow2['taxcatid']);	/*NB NO Updates to DB */
		$_SESSION['Items']->GetTaxes($myrow2['orderlineno']);	
		
		foreach ($_SESSION['Items']->LineItems[$myrow2['orderlineno']]->Taxes AS $Tax) {
			
			
		
				if ($Tax->TaxOnTax ==1){
					$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * ($LineTotal + $TaxLineTotal));
					$TaxLineTotal += ($Tax->TaxRate * ($LineTotal + $TaxLineTotal));
				} else {
					$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * $LineTotal);
					$TaxLineTotal += ($Tax->TaxRate * $LineTotal);
				}
				$TaxGLCodes[$Tax->TaxAuthID] = $Tax->TaxGLCode;
			
			
		}
		
		// Actualizar el total final
		$TOTFINAL += ($myrow2['unitprice']*$myrow2['quantity']);
		$IVA += $TaxLineTotal;
		
		if(intval($myrow2['quantity'])==$myrow2['quantity']){
			$DisplayQty = number_format(floor($myrow2['quantity']*100)/100,0);
		}else {
			$DisplayQty = number_format(floor($myrow2['quantity']*100)/100,2);
		}
		
		
		$DisplayUnitPrice = number_format(floor($myrow2['unitprice']*100)/100,2);
		
		$DisplayLineTotal = number_format(floor(($myrow2['unitprice']*$myrow2['quantity'])*100)/100,2);

		$LeftOvers = $pdf->addTextWrap(35,$YPos,50,$FontSize,$DisplayQty,'left'); // primero
		$LeftOvers = $pdf->addTextWrap(100,$YPos,50,$FontSize,$myrow2['stkcode'],'left'); // segundo
		$LeftOvers = $pdf->addTextWrap(150,$YPos,250,$FontSize,utf8_decode($myrow2['longdescription']),'left'); // tercero
		$LeftOvers = $pdf->addTextWrap(450,$YPos,50,$FontSize,$DisplayUnitPrice,'right'); // cuarto
		$LeftOvers = $pdf->addTextWrap(500,$YPos,50,$FontSize,$DisplayLineTotal,'right'); // quinto

		if ($YPos-$line_height <= 70){
	   /* We reached the end of the page so finsih off the page and start a newy */

	      $PageNumber++;
	      include ('includes/rh_PDFOrderPageHeader.inc');

	   } //end if need a new page headed up

	   /*increment a line down for the next line item */
	   $YPos -= ($line_height);
	   if(isset($_SESSION['StorageBins'])&&$_SESSION['StorageBins']==1){
		   $SBins=getStorageBins($myrow2['stkcode'],$myrow['loccode']);
		   if(count($SBins)>0){
		   	$Almacenes='';
		   	foreach($SBins as $d){
		   		unset($d['id']);
		   		unset($d['stockid']);
		   		unset($d['active']);
		   		unset($d['location']);
		   		//$d['description'].=' ';
		   		//$d=implode("",$d);
		   		$d=$d['description'];
		   		$Almacenes="[".$d."]";
		   
		   		$LeftOvers = $pdf->addTextWrap(150,$YPos,250,$FontSize,$Almacenes,'left'); // tercero
		   
		   		if ($YPos-$line_height <= 70){
		   			/* We reached the end of the page so finsih off the page and start a newy */
		   				
		   			$PageNumber++;
		   			include ('includes/rh_PDFOrderPageHeader.inc');
		   
		   		} //end if need a new page headed up
		   		$YPos -= ($line_height);
		   	}
		   }
	   }
      } //end while there are line items to print out
      
      $DisplayTot = number_format(floor(($IVA+$TOTFINAL)*100)/100,2);
      $IVA = number_format(floor($IVA*100)/100,2);
      $TOTFINAL = number_format(floor($TOTFINAL*100)/100,2);
      
      // Print subtotal, iva, total
      	$pdf->addText(450,70,11,'Total: $ ');
      	$pdf->addText(450,70-13,11,'IVA: $ ');
      	$pdf->addText(450,70-26,11,'Neto: $ ');
      	
		$pdf->addTextWrap(480, 70,100,11, $TOTFINAL,'right');
		$pdf->addTextWrap(480, 70-13,100,11, $IVA,'right');
		$pdf->addTextWrap(480, 70-26,100,11, $DisplayTot,'right');

} /*end if there are order details to show on the order*/

$pdfcode = $pdf->output();
$len = strlen($pdfcode);

if ($len<=20){
	$title = _('Pedido').$_GET['TransNo'];
	include('includes/header.inc');
	echo '<p>'. _('There were no oustanding items on the order to deliver. A dispatch note cannot be printed').
		'<BR><A HREF="' . $rootpath . '/SelectSalesOrder.php?' . SID . '">'. _('Print Another Packing Slip/Order').
		'</A>' . '<BR>'. '<A HREF="' . $rootpath . '/index.php?' . SID . '">' . _('Back to the menu') . '</A>';
	include('includes/footer.inc');
	exit;
} else {
	header('Content-type: application/pdf');
	header('Content-Length: ' . $len);
	header('Content-Disposition: inline; filename=PackingSlip.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	$pdf->Stream();

	$sql = "UPDATE salesorders SET printedpackingslip=1, datepackingslipprinted='" . Date('Y-m-d') . "' WHERE salesorders.orderno=" .$_GET['TransNo'];
	$result = DB_query($sql,$db);
}

?>
