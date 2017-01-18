<?php

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-07-15 11:30:50 -0500 (mar, 15 jul 2008) $
 * $Rev: 330 $
 */
/* Oct 2006 RealHost bowikaxu Adaptacion para impresion de Works Orders */

$PageSecurity = 2;

include('includes/session.inc');
include('includes/class.pdf.php');
include('includes/SQL_CommonFunctions.inc');

//Get Out if we have no order number to work with
If (!isset($_GET['FromWORef']) || $_GET['FromWORef']==""){
	$title = _('Select Work Order To Print');
	include('includes/header.inc');
	echo '<DIV ALIGN=CENTER><BR><BR><BR>';
	echo '<FORM METHOD=GET>';
	//prnMsg( _('Select a Work Order Number to Print ') , 'error');
	//echo '<BR><BR><BR><table class="table_index"><TR><TD CLASS="menu_group_item">
	//	<LI><A HREF="'. $rootpath . '/SelectSalesOrder.php?'. SID .'">' . _('Select Works Orders') . '</A></LI>
	//	</TD></TR></TABLE></DIV><BR><BR><BR>';
	echo '<STRONG>'._('Impresion de Ordenes de Trabajo').'</STRONG>';
	
	echo '<BR><BR><BR><table class="table_index"><TR><TD CLASS="menu_group_item">
		<TD><LI>From WORef:</TD><TD><INPUT TYPE=text SIZE=4 NAME=FromWORef></TD>
		<TD><LI>To WORef:</TD><TD><INPUT TYPE=text SIZE=4 NAME=ToWORef></TD>
		<TD><INPUT TYPE=checkbox name=precio>'._('Imprimir Precio').'</TD>
		</TD></TR></TABLE>';

	
	echo '<BR><table class="table_index"><TR><TD CLASS="menu_group_item">
		<TD><INPUT TYPE=submit NAME="Process" VALUE= "'._('Print').'"></TD>
		</TD>';
	
	echo '</TR></TABLE></DIV><BR><BR><BR></FORM>';
	include('includes/footer.inc');
	exit;
}

// Si se envio tambien hasta que WORef
if(isset($_GET['ToWORef']) || $_GET['ToWORef']!=""){

	$FromWORef = $_GET['FromWORef'];
	$ToWORef = $_GET['ToWORef'];		
}else {	// No se envio hasta cual, sera solo una
	
	$FromWORef = $_GET['FromWORef'];
	$ToWORef = $_GET['FromWORef'];
}

if($FromWORef != "" && $ToWORef != ""){
/*retrieve the order details from the database to print */
$sql = "SELECT SUM(costissued) as total FROM workorders
		WHERE workorders.wo >= ".$FromWORef."
		AND workorders.wo  <=" . $ToWORef;
$restot = DB_query($sql,$db,'Imposible obtener los totales de las ordenes');
$res = DB_fetch_array($restot);
$TOTALWO = $res['total']; 

$ErrMsg = _('There was a problem retrieving the work order header details for work order Number') . ' ' . $FromWORef . ' To ' .$ToWORef . ' ' . _('from the database');
$sql = "SELECT *
		FROM workorders
		WHERE workorders.wo >= ".$FromWORef."
		AND workorders.wo  <=" . $ToWORef;

$resultord=DB_query($sql,$db, $ErrMsg);

//If there are no rows, there's a problem.
//echo "entro al if con las referencias";
if (DB_num_rows($resultord)==0){
	$title = _('Print Packing Slip Error');
        include('includes/header.inc');
        echo '<div align=center><br><br><br>';
	prnMsg( _('Unable to Locate Work Order Number') . ' : ' . $FromWORef . ' To ' . $ToWORef . '  ' , 'error');
        echo '<BR><BR><BR><TABLE class="table_index"><TR><TD class="menu_group_item">
                <LI><A HREF="'. $rootpath . '/SelectSalesOrder.php?'. SID .'">' . _('Outstanding Sales Orders') . '</A></LI>
                <LI><A HREF="'. $rootpath . '/SelectCompletedOrder.php?'. SID .'">' . _('Completed Sales Orders') . '</A></LI>
                </TD></TR></TABLE></DIV><BR><BR><BR>';
        include('includes/footer.inc');
        exit();
} else{ /*There is only one order header returned - thats good! */
//} elseif (DB_num_rows($result)>=1){ /*There is only one order header returned - thats good! */
$TotOrd = 0;
	// Ciclo para todas labrs worksorders regresadas
	
	while($myrow = DB_fetch_array($resultord)){
		$TotOrd++;
		$CurrentWORef = $myrow['wo'];
		/*
	if ($myrow['printedpackingslip']==1 AND ($_GET['Reprint']!='OK' OR !isset($_GET['Reprint']))){
		$title = _('Print Packing Slip Error');
	      	include('includes/header.inc');
		echo '<P>';
		prnMsg( _('The packing slip for order number') . ' ' . $_GET['TransNo'] . ' ' .
			_('has previously been printed') . '. ' . _('It was printed on'). ' ' . ConvertSQLDate($myrow['datepackingslipprinted']) .
			'<br>' . _('This check is there toensure that duplicate packing slips are not produced and dispatched more than once to the customer'), 'warn' );
	      echo '<P><A HREF="' . $rootpath . '/PrintCustOrder.php?' . SID . 'TransNo=' . $_GET['TransNo'] . '&Reprint=OK">'
		. _('Do a Re-Print') . ' (' . _('On Pre-Printed Stationery') . ') ' . _('Even Though Previously Printed') . '</A><P>' .
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
   	
   	*/
//}
/* Then there's an order to print and its not been printed already (or its been flagged for reprinting)
LETS GO */


/* Now ... Has the order got any line items still outstanding to be invoiced */
if($TotOrd==1){

	$PageNumber = 1;

	$Page_Width=807;
	$Page_Height=612;
	$Top_Margin=34;
	$Bottom_Margin=20;
	$Left_Margin=15;
	$Right_Margin=10;

	$PageSize = array(0,0,$Page_Width,$Page_Height);
	$pdf = & new Cpdf($PageSize);
	$FontSize=12;
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->addinfo('Author','webERP - bowikaxu' . $Version);
	$pdf->addinfo('Creator','webERP http://www.weberp.org - R&OS PHP-PDF http://www.ros.co.nz');
	$pdf->addinfo('Title', _('Works Orders Print ') );
	$pdf->addinfo('Subject', _('Work Order') . ' ' . $CurrentWORef);

	$line_height=16;

	include('includes/rh_PDFWOPageHeader.inc');


}else {
			$PageNumber++;
	      	include ('includes/rh_PDFWOPageHeader.inc');
}

$LeftOvers = $pdf->addTextWrap(148,$YPos,239,$FontSize,_('Work Order').' #'.$CurrentWORef,'center');
if($_GET['precio']!='')
$LeftOvers = $pdf->addTextWrap(387,$YPos,90,$FontSize,'$'.number_format($myrow['costissued'],2),'right');
//$LeftOvers = $pdf->addTextWrap(505,$YPos,90,$FontSize,$myrow2['unitsreqd'],'right');
$YPos -= ($line_height);

$ErrMsg = _('There was a problem retrieving the details for Work Order Number') . ' ' . $CurrentWORef . ' ' . _('from the database');
$sql = "SELECT *
	FROM woitems
	 WHERE woitems.wo =" . $CurrentWORef;
		
$result=DB_query($sql, $db, $ErrMsg);

if (DB_num_rows($result)>0){
/*Yes there are line items to start the ball rolling with a page header */

	/*Set specifically for the stationery being used -needs to be modified for clients own
	packing slip 2 part stationery is recommended so storeman can note differences on and
	a copy retained */
	
	while ($myrow2=DB_fetch_array($result)){
		
		$Trans = $myrow2['id'];
		$ErrMsg = _('There was a problem retrieving the details for Item ').$myrow2['stockid'];
		$ItemDetailsSQL = "SELECT * 
						FROM stockmaster
						WHERE stockmaster.stockid = '". $myrow2['stockid']."'"; 
		$result2=DB_query($ItemDetailsSQL, $db, $ErrMsg);		
		$ItemDetails = DB_fetch_array($result2);
		
		$DetailsCost = "$".number_format($myrow2['stdcost'],2);
		//$DisplayQty = number_format($myrow2['quantity'],2);
		//$DisplayPrevDel = number_format($myrow2['qtyinvoiced'],2);
		//$DisplayQtySupplied = number_format($myrow2['quantity'] - $myrow2['qtyinvoiced'],2);

		$LeftOvers = $pdf->addTextWrap(13,$YPos,135,$FontSize,$myrow2['stockid']);
		$LeftOvers = $pdf->addTextWrap(148,$YPos,239,$FontSize,$ItemDetails['description']);
		//$LeftOvers = $pdf->addTextWrap(387,$YPos,90,$FontSize,$myrow2['unitsreqd'],'right');
		$LeftOvers = $pdf->addTextWrap(505,$YPos,90,$FontSize,$myrow2['qtyreqd'],'right');
		if($_GET['precio']!='')
		$LeftOvers = $pdf->addTextWrap(604,$YPos,90,$FontSize,$DetailsCost,'right');
		
		// TERMINA DE IMPRIMIR LOS DETALLES
		
		if ($YPos-$line_height <= 136){
	   /* We reached the end of the page so finsih off the page and start a newy */

	      $PageNumber++;
	      include ('includes/rh_PDFWOPageHeader.inc');

	   } //end if need a new page headed up

	   /*increment a line down for the next line item */
	   $YPos -= ($line_height);

		
		
			// COMIENZA A IMPRIMIR LOS REQUERIMENTOS
	
$ErrMsg = _('There was a problem retrieving the requirements for Work Order Number') . ' ' . $CurrentWORef . ' ' . _('from the database');
// bowikaxu realhost january 2008- fixed the woref field to wo
$sql = "SELECT *
	FROM worequirements
	 WHERE worequirements.wo =" . $CurrentWORef."";
		
$resultreq=DB_query($sql, $db, $ErrMsg);

if (DB_num_rows($resultreq)>0){
/*Yes there are line items to start the ball rolling with a page header */

	/*Set specifically for the stationery being used -needs to be modified for clients own
	packing slip 2 part stationery is recommended so storeman can note differences on and
	a copy retained */
/*
	$Page_Width=807;
	$Page_Height=612;
	$Top_Margin=34;
	$Bottom_Margin=20;
	$Left_Margin=15;
	$Right_Margin=10;


	$PageSize = array(0,0,$Page_Width,$Page_Height);
	$pdf = & new Cpdf($PageSize);
	$FontSize=12;
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->addinfo('Author','webERP ' . $Version);
	$pdf->addinfo('Creator','webERP http://www.weberp.org - R&OS PHP-PDF http://www.ros.co.nz');
	$pdf->addinfo('Title', _('Works Orders Print - bowikaxu@gmail.com') );
	$pdf->addinfo('Subject', _('Work Order') . ' ' . $CurrentWORef);

	$line_height=16;

	include('includes/rh_PDFWOPageHeader.inc');
*/
	while ($myrowreq=DB_fetch_array($resultreq)){

		$ErrMsg = _('There was a problem retrieving the details for Item ').$myrowreq['stockid'];
		$ItemDetailsSQL = "SELECT * 
						FROM stockmaster
						WHERE stockmaster.stockid = '". $myrowreq['stockid']."'"; 
		$result2=DB_query($ItemDetailsSQL, $db, $ErrMsg);		
		$ItemDetails = DB_fetch_array($result2);
		
		$DetailsCost = "$".number_format(($myrowreq['stdcost']*$myrowreq['qtypu']*$myrow2['qtyreqd']),2);
		//$DisplayQty = number_format($myrow2['quantity'],2);
		//$DisplayPrevDel = number_format($myrow2['qtyinvoiced'],2);
		//$DisplayQtySupplied = number_format($myrow2['quantity'] - $myrow2['qtyinvoiced'],2);

		$LeftOvers = $pdf->addTextWrap(13,$YPos,135,$FontSize," -> ".$myrowreq['stockid']);
		$LeftOvers = $pdf->addTextWrap(148,$YPos,239,$FontSize,$ItemDetails['description']);
		//$LeftOvers = $pdf->addTextWrap(387,$YPos,90,$FontSize,$myrow2['unitsreqd'],'right');
		$LeftOvers = $pdf->addTextWrap(505,$YPos,90,$FontSize,number_format(($myrowreq['qtypu']*$myrow2['qtyreqd']),2),'right');
		if($_GET['precio']!='')
		$LeftOvers = $pdf->addTextWrap(604,$YPos,90,$FontSize,$DetailsCost,'right');
		
		// TERMINA DE IMPRIMIR LOS DETALLES
		
		
		if ($YPos-$line_height <= 136){
	   /* We reached the end of the page so finsih off the page and start a newy */

	      $PageNumber++;
	      include ('includes/rh_PDFWOPageHeader.inc');

	   } //end if need a new page headed up

	   /*increment a line down for the next line item */
	   $YPos -= ($line_height);

      } //end while there are line items to print out
	}
		$YPos -= ($line_height);
      } //end while there are line items to print out  

} /*end if there are order details to show on the order*/

	} // Ya no hay woref restantes en worksorders
} // Ya no existen woref restantes en worksorders

$pdfcode = $pdf->output();
$len = strlen($pdfcode);

if ($len<=20){
	$title = _('Print Packing Slip Error');
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

	//$sql = "UPDATE salesorders SET printedpackingslip=1, datepackingslipprinted='" . Date($_SESSION['DefaultDateFormat']) . "' WHERE salesorders.orderno=" .$_GET['TransNo'];
	//$result = DB_query($sql,$db);
}
}

?>