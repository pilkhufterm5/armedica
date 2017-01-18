<?php

/* $Revision: 14 $ */
/* $Revision: 14 $ */


$PageSecurity = 2;
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
include('includes/DefineSuppTransClass.php');

if(!isset($_GET['OrderNo']) && !isset($_POST['OrderNo'])){
	
        $title = _('Select a Purchase Order');
        include('includes/header.inc');
        echo '<div align=center><br><br><br>';
        prnMsg( _('Select an Puchase Order Number to Print before calling this page') , 'error');
        echo '<BR><BR><BR><table class="table_index">
		<tr><td class="menu_group_item">
                <li><a href="'. $rootpath . '/PO_SelectOSPurchOrder.php?'.SID .'">' . _('Outstanding Purchase Orders') . '</a></li>
                <li><a href="'. $rootpath . '/PO_SelectPurchOrder.php?'. SID .'">' . _('Purchase Order Inquiry') . '</a></li>
                </td></tr></table></DIV><BR><BR><BR>';
        include('includes/footer.inc');
        exit();

	echo '<CENTER><BR><BR><BR>' . _('This page must be called with a purchase order number to print');
	echo '<BR><A HREF="'. $rootpath . '/index.php?' . SID . '">' . _('Back to the menu') . '</A></CENTER>';
	exit;
}







if (isset( $_SESSION['SuppTrans'])){
		unset ( $_SESSION['SuppTrans']->GRNs);
		unset ( $_SESSION['SuppTrans']->GLCodes);
		unset ( $_SESSION['SuppTrans']);
	}

	 if (isset( $_SESSION['SuppTransTmp'])){
		unset ( $_SESSION['SuppTransTmp']->GRNs);
		unset ( $_SESSION['SuppTransTmp']->GLCodes);
		unset ( $_SESSION['SuppTransTmp']);
	}
//	 Session_register('SuppInv');
	 $_SESSION['SuppTrans'] = new SuppTrans;
	 

if (isset($_GET['OrderNo'])){
	$OrderNo = $_GET['OrderNo'];
	$OrderNo2= str_pad((int) $OrderNo,5,"0",STR_PAD_LEFT);
} elseif (isset($_POST['OrderNo'])){
	$OrderNo = $_POST['OrderNo'];
	$OrderNo2= str_pad((int) $OrderNo,5,"0",STR_PAD_LEFT);
}

include ('barcode2/barcode.inc.php');
$dirfile = "tmp/po_codigo_barras";
	if(!is_dir($dirfile))mkdir($dirfile);
        $file = "PO_" . $OrderNo2 . ".jpeg";
        if (!is_file($dirfile . "/" . $file)) {
            $bar = new BARCODE();
            $bar->setSymblogy("CODE39");
            $bar->setHeight(30);
            $bar->setScale(2);
            $bar->setHexColor("#00000", "#FFFFFF");         
            $return = $bar->genBarCode($OrderNo2, "jpeg", $dirfile . "/PO_" . $OrderNo2);
        }





$title = _('Print Purchase Order Number').' '. $OrderNo;

$ViewingOnly = 0;
if (isset($_GET['ViewingOnly']) && $_GET['ViewingOnly']!='') {
	$ViewingOnly = $_GET['ViewingOnly'];
} elseif (isset($_POST['ViewingOnly']) && $_POST['ViewingOnly']!='') {
	$ViewingOnly = $_POST['ViewingOnly'];
}


if (isset($_POST['DoIt'])  AND ($_POST['PrintOrEmail']=='Print' || $ViewingOnly==1) ){
	$MakePDFThenDisplayIt = True;
} elseif (isset($_POST['DoIt']) AND $_POST['PrintOrEmail']=='Email' AND strlen($_POST['EmailTo'])>6){
	$MakePDFThenEmailIt = True;
}

if (isset($OrderNo) && $OrderNo != "" && $OrderNo > 0){
	//Check this up front. Note that the myrow recordset is carried into the actual make pdf section
	/*retrieve the order details from the database to print */
	$ErrMsg = _('There was a problem retrieving the purchase order header details for Order Number'). ' ' . $OrderNo .
			' ' . _('from the database');
	$sql = "SELECT
			purchorders.supplierno,
			suppliers.suppname,
			suppliers.address1,
			suppliers.address2,
			suppliers.address3,
			suppliers.address4,
			purchorders.comments,
			purchorders.orddate,
			purchorders.rate,
			purchorders.dateprinted,
			purchorders.intostocklocation,
			purchorders.deladd1,
			purchorders.deladd2,
			purchorders.deladd3,
			purchorders.deladd4,
			purchorders.deladd5,
			purchorders.deladd6,
			purchorders.allowprint,
			purchorders.contact,
			purchorders.requisitionno,
			purchorders.rh_location_entrega,
			www_users.realname initiator,
			suppliers.currcode,
			suppliers.taxgroupid,
			taxgroups.taxgroupdescription,
			purchorders.rh_autoriza
		FROM purchorders INNER JOIN suppliers
			ON purchorders.supplierno = suppliers.supplierid
			INNER JOIN taxgroups ON suppliers.taxgroupid=taxgroups.taxgroupid
            INNER JOIN www_users ON purchorders.initiator=www_users.userid
		WHERE purchorders.orderno = " . $OrderNo."";
	$result=DB_query($sql,$db, $ErrMsg);

	if (DB_num_rows($result)==0){ /*There is ony one order header returned */

		$title = _('Print Purchase Order Error');
		include('includes/header.inc');
		echo '<div align=center><br><br><br>';
		prnMsg( _('Unable to Locate Purchase Order Number') . ' : ' . $OrderNo . ' ', 'error');
		echo '<BR><BR><BR><table class="table_index">
			<tr><td class="menu_group_item">
	                <li><a href="'. $rootpath . '/PO_SelectOSPurchOrder.php?'.SID .'">' . _('Outstanding Purchase Orders') . '</a></li>
        	        <li><a href="'. $rootpath . '/PO_SelectPurchOrder.php?'. SID .'">' . _('Purchase Order Inquiry') . '</a></li>
                	</td></tr></table></DIV><BR><BR><BR>';
		include('includes/footer.inc');
		exit();

	} elseif (DB_num_rows($result)==1){ /*There is ony one order header returned */

	   $POHeader = DB_fetch_array($result);

	   // bowikaxu realhost - get the values to calculate tax<br>
		$_SESSION['SuppTrans']->SupplierName = $POHeader['suppname'];
		//$_SESSION['SuppTrans']->TermsDescription = $POHeader['terms'];
	    $_SESSION['SuppTrans']->CurrCode = $POHeader['currcode'];
		$_SESSION['SuppTrans']->ExRate = $POHeader['exrate'];
		$_SESSION['SuppTrans']->TaxGroup = $POHeader['taxgroupid'];
		$_SESSION['SuppTrans']->TaxGroupDescription = $POHeader['taxgroupdescription'];
	   
	   if ($ViewingOnly==0) {
	   	// bowikaxu realhost - july 17 2007
		   if ($POHeader['allowprint']==0){
			  $title = _('Purchase Order Already Printed');
			  include('includes/header.inc');
			  echo '<P>';
			  prnMsg( _('Purchase order number').' ' . $OrderNo . ' '.
				_('has previously been printed') . '. ' . _('It was printed on'). ' ' .
				ConvertSQLDate($POHeader['dateprinted']) . '<BR>'.
				_('To re-print the order it must be modified to allow a reprint'). '<BR>'.
				_('This check is there to ensure that duplicate purchase orders are not sent to the supplier	resulting in several deliveries of the same supplies'), 'warn');
           echo '<BR><TABLE class="table_index">
                <TR><TD class="menu_group_item">
 					 <LI><A HREF="' . $rootpath . '/PO_PDFPurchOrder.php?' . SID . 'OrderNo=' . $OrderNo . '&ViewingOnly=1">'.
				_('Print This Order as a Copy'). '</A>
 				<LI><A HREF="' . $rootpath . '/PO_Header.php?' . SID . 'ModifyOrderNumber=' . $OrderNo . '">'.
				_('Modify the order to allow a real reprint'). '</A>' .
			  	'<LI><A HREF="'. $rootpath .'/PO_SelectPurchOrder.php?' . SID . '">'.
				_('Select another order'). '</A>'.
			  	'<LI><A HREF="' . $rootpath . '/index.php?' . SID . '">'. _('Back to the menu').'</A>';
			  echo '</BODY></HTML>';
			  include('includes/footer.inc');
			  exit;
		   }//AllowedToPrint
	   }//not ViewingOnly
	}// 1 valid record
}//if there is a valid order number

If ($MakePDFThenDisplayIt OR $MakePDFThenEmailIt){

	$PaperSize = 'letter';

	include('includes/PDFStarter.php');

	$pdf->addinfo('Title', _('Purchase Order') );
	$pdf->addinfo('Subject', _('Purchase Order Number').' ' . $_GET['OrderNo']);

	$line_height=12;
	   /* Then there's an order to print and its not been printed already (or its been flagged for reprinting)
	   Now ... Has it got any line items */

	   $PageNumber = 1;
	   $ErrMsg = _('There was a problem retrieving the line details for order number') . ' ' . $OrderNo . ' ' .
			_('from the database');
		// bowikaxu realhost - june 07 - retrieve comments
		$sql = "SELECT itemcode,
	   			deliverydate,
				longdescription as itemdescription,
				unitprice,
				units,
				quantityord,
				decimalplaces,
				rh_comments,
				taxcatid,
				itemdescription as itemdescription2,
				uom,
				rh_tax,
				purchorderdetails.id_agrupador
			FROM purchorderdetails LEFT JOIN stockmaster
				ON purchorderdetails.itemcode=stockmaster.stockid
			WHERE orderno =" . $OrderNo;
	   $result=DB_query($sql,$db);

	   if (DB_num_rows($result)>0){
	   /*Yes there are line items to start the ball rolling with a page header */
		include('includes/PO_PDFOrderPageHeader.inc');

		$OrderTotal = 0;
		$rh_conteo = 0;
		$OrderTotalIVA = 0;
        $TotalTax = 0;
        $New_DisplayLineTotal = 0;
		while ($POLine=DB_fetch_array($result)){

			$sql = "SELECT supplierdescription 
				FROM purchdata 
				WHERE stockid='" . $POLine['itemcode'] . "'
				AND supplierno ='" . $POHeader['supplierno'] . "'";
			$SuppDescRslt = DB_query($sql,$db);
	
			$ItemDescription='';

			if (DB_error_no($db)==0){
				if (DB_num_rows($SuppDescRslt)==1){
					$SuppDescRow = DB_fetch_array($SuppDescRslt);
					if (strlen($SuppDescRow[0])>2){
						$ItemDescription = $SuppDescRow[0];
					}
				}
			}
			if (strlen($ItemDescription)<2){
				$ItemDescription = $POLine['itemdescription'];
			}else{
                $ItemDescription .=' '.$POLine['itemdescription'];   
			}
            
            if(empty($ItemDescription)){
                $ItemDescription =$POLine['itemdescription2'];
            }
            

			$DisplayQty = number_format($POLine['quantityord'],$POLine['decimalplaces']);
			if ($_POST['ShowAmounts']=='Yes'){
				$DisplayPrice = number_format($POLine['unitprice'],2);
			} else {
				$DisplayPrice = "----";
			}
			$DisplayDelDate = ConvertSQLDate($POLine['deliverydate'],2);
			if ($_POST['ShowAmounts']=='Yes'){
				$DisplayLineTotal = $POLine['unitprice']*$POLine['quantityord'];
			} else {
				$DisplayLineTotal = 0.00;
			}
			
			if ($POLine['rh_tax'] > 0){
                $DisplayTAX = $POLine['rh_tax'];
                $TotalTax = $TotalTax + ($DisplayLineTotal *($DisplayTAX/100));
            }else{
                $DisplayTAX = 0.00;
            }
			
			if($rh_conteo == 30 || $YPos<135 ){
				$rh_conteo = 1;
				$PageNumber++;
				include("includes/PO_PDFOrderPageHeader.inc");
			}else{
				$rh_conteo++;
			}

			$OrderTotal += ($POLine['unitprice']*$POLine['quantityord']);
            if($POLine['taxcatid ']==6){
			    $OrderTotalIVA += ($POLine['unitprice']*$POLine['quantityord']);
            }
            
			//CODIGO
			$codigo=$POLine['itemcode'];
			if($codigo==''&&$POLine['id_agrupador']!='')
				$codigo='{'.$POLine['id_agrupador'].'}';
			$pdf->addTextWrap(20,$YPos,90,8, $codigo, 'left');
			//$pdf->addTextWrap(16,$YPos-396,90,8, $POLine['itemcode'], 'left');
			
			//DESCRIPCION
			//$pdf->addTextWrap(108,$YPos-396,216,8, $ItemDescription, 'left');
			
			//CANTIDAD
			$pdf->addTextWrap(256,$YPos,54,8, $DisplayQty, 'right');
			//$pdf->addTextWrap(326,$YPos-396,54,8, $DisplayQty, 'right');
			
			//UNIDAD
			if(empty($POLine['units'])){
			    $POLine['units'] = $POLine['uom'];
			}
			
			$pdf->addTextWrap(332,$YPos,36,8, $POLine['units'], 'center');
			//$pdf->addTextWrap(382,$YPos-396,36,8, $POLine['units'], 'center');
			
			//FECHA
			$pdf->addTextWrap(370,$YPos,54,8, $DisplayDelDate, 'center');
			//$pdf->addTextWrap(420,$YPos-396,54,8, $DisplayDelDate, 'center');
			
			//PRECIO
			$pdf->addTextWrap(415,$YPos,54,8, $DisplayPrice, 'right');
			//$pdf->addTextWrap(476,$YPos-396,54,8, $DisplayPrice, 'right');
			
			//TAX
			$pdf->addTextWrap(475,$YPos,54,8, number_format($DisplayLineTotal *($DisplayTAX/100),2), 'right');
            //$pdf->addTextWrap(476,$YPos-396,54,8, $DisplayPrice, 'right');
            
			//TOTAL
			$New_DisplayLineTotal = $DisplayLineTotal *(1+$DisplayTAX/100);
			
            
			
			$pdf->addTextWrap(522,$YPos,54,8, number_format($New_DisplayLineTotal,2), 'right');
			//$pdf->addTextWrap(522,$YPos,54,8, $DisplayLineTotal, 'right');
			//$pdf->addTextWrap(532,$YPos-396,54,8, $DisplayLineTotal, 'right');
			$ItemDescription = utf8_decode($ItemDescription);
			
			$ItemDescription = $pdf->addTextWrap(58,$YPos,216,8, $ItemDescription, 'left');
			
			$YPos -= $line_height;
			while($ItemDescription!=""){
			$YPos -= $line_height;
				if($rh_conteo == 30 || $YPos<65 ){
					$rh_conteo = 1;
					$PageNumber++;
					include('includes/PO_PDFOrderPageHeader.inc');
				}
				$ItemDescription = $pdf->addTextWrap(58,$YPos,216,8, $ItemDescription, 'left');	//Descripcion
			}
        
		} //end while there are line items to print out
		//end if need a new page headed up
		// bowikaxu - get txes

		$_SESSION['SuppTrans']->OvAmount = $OrderTotal;

		$LocalTaxProvinceResult = DB_query("SELECT taxprovinceid
						FROM locations
						WHERE loccode = '" . $_SESSION['UserStockLocation'] . "'", $db);

		$LocalTaxProvinceRow = DB_fetch_row($LocalTaxProvinceResult);
		$_SESSION['SuppTrans']->LocalTaxProvince = $LocalTaxProvinceRow[0];

		$_SESSION['SuppTrans']->GetTaxes();
		$TaxTotal = $OrderTotalIVA*0.16;
         /*
		foreach ($_SESSION['SuppTrans']->Taxes as $Tax) {
			if (isset($_POST['TaxRate'  . $Tax->TaxCalculationOrder])){
				$_SESSION['SuppTrans']->Taxes[$Tax->TaxCalculationOrder]->TaxRate = $_POST['TaxRate'  . $Tax->TaxCalculationOrder]/100;
			}
			if ($Tax->TaxOnTax ==1){
				$_SESSION['SuppTrans']->Taxes[$Tax->TaxCalculationOrder]->TaxOvAmount = $_SESSION['SuppTrans']->Taxes[$Tax->TaxCalculationOrder]->TaxRate * ($_SESSION['SuppTrans']->OvAmount + $TaxTotal);
			} else { /*Calculate tax without the tax on tax */

		 /*		$_SESSION['SuppTrans']->Taxes[$Tax->TaxCalculationOrder]->TaxOvAmount = $_SESSION['SuppTrans']->Taxes[$Tax->TaxCalculationOrder]->TaxRate * $_SESSION['SuppTrans']->OvAmount;
			}
			$TaxTotal += $_SESSION['SuppTrans']->Taxes[$Tax->TaxCalculationOrder]->TaxOvAmount;
		}   */
		// bowikaxu - end get taxes

		if ($_POST['ShowAmounts']=='Yes'){
			// bowikaxu - add the taxes
			//$DisplayOrderTotal = number_format($OrderTotal+$TaxTotal,2);
			$DisplayOrderTotal = number_format($OrderTotal,2);
			$DisplayTax = number_format($TaxTotal,2);
			$DisplayTotal = number_format($OrderTotal+$TotalTax,2);
		} else {
			$DisplayOrderTotal = "----";
			$DisplayTax = "----";
			$DisplayTotal = "----";
		}


        
		//SUBTOTAL
		$pdf->addTextWrap(476,45,54,8, _('SubTotal').':', 'right');
		//$pdf->addTextWrap(476,54,54,8, _('SubTotal').':', 'right');
		$pdf->addTextWrap(532,45,54,8, $DisplayOrderTotal, 'right');
		//$pdf->addTextWrap(532,54,54,8, $DisplayOrderTotal, 'right');
		//IVA
		$pdf->addTextWrap(476,35,54,8, _('Tax').':', 'right');
		//$pdf->addTextWrap(476,40,54,8, _('Tax').':', 'right');  $TotalTax
		$pdf->addTextWrap(532,35,54,8, number_format($TotalTax,2), 'right');
		//$pdf->addTextWrap(532,35,54,8, $DisplayTax, 'right');
		//$pdf->addTextWrap(532,40,54,8, $DisplayTax, 'right');
		//TOTAL
		$GTotal = $OrderTotal + $TotalTax;
		
		$pdf->addTextWrap(476,25,54,8, _('Total').':', 'right');
		//$pdf->addTextWrap(476,26,54,8, _('Total').':', 'right');
		$pdf->addTextWrap(532,25,54,8, number_format($GTotal,2), 'right');
		//$pdf->addTextWrap(532,25,54,8, $DisplayTotal, 'right');
		//$pdf->addTextWrap(532,26,54,8, $DisplayTotal, 'right');
		//LINEAS
		$pdf->partEllipse(589.5,49,0,90,10,10);//Curva superior derecha
		//$pdf->partEllipse(589.5,58,0,90,10,10);//Curva superior derecha
		$pdf->partEllipse(486,20,180,270,10,10);//Curva inferior izquierda
		//$pdf->partEllipse(486,32,180,270,10,10);//Curva inferior izquierda
		$pdf->partEllipse(486,49,90,180,10,10);//Curva superior izquierda
		//$pdf->partEllipse(486,58,90,180,10,10);//Curva superior izquierda
		$pdf->partEllipse(589.5,20,270,360,10,10);//Curva inferior derecha
		//$pdf->partEllipse(589.5,32,270,360,10,10);//Curva inferior derecha
		$pdf->line(486,59,589.5,59);//linea superior
		//$pdf->line(486,68,589.5,68);//linea superior
		$pdf->line(486,10,589.5,10);//linea inferior
		//$pdf->line(486,22,589.5,22);//linea inferior
		$pdf->line(476,49,476,20);//linea izquierda
		//$pdf->line(476,32,476,58);//linea izquierda
		$pdf->line(599.5,49,599.5,20);//linea derecha
		//$pdf->line(599.5,32,599.5,58);//linea derecha
        
        //OVSERVACIONES
        $pdf->addTextWrap(16,50,65,7, _('OBSERVACIONES').':', 'left');
        $pdf->Line(16,20,220,20);  //LINEA ORIZON ABAJO.
        $pdf->Line(16,48,220,48);  //
        $pdf->Line(16,20,16,48);   //LINEA VERT IZQ.
        $pdf->Line(220,20,220,48); //
        
        $pdf->Image($dirfile . "/" . $file, 500, 680, 100, 'L');
        $pdf->addTextWrap(525,75,40,8, "PO_".$OrderNo2, 'center');
	} /*end if there are order details to show on the order*/
    //} /* end of check to see that there was an order selected to print */

    //failed var to allow us to print if the email fails.
    $failed = false;
    if ($MakePDFThenDisplayIt){

    	$buf = $pdf->output();
    	$len = strlen($buf);
    	header('Content-type: application/pdf');
    	header('Content-Length: ' . $len);
    	header('Content-Disposition: inline; filename=PurchaseOrder.pdf');
    	header('Expires: 0');
    	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    	header('Pragma: public');

    	$pdf->stream();

    } else { /* must be MakingPDF to email it */

    	$pdfcode = $pdf->output();
	$fp = fopen( $_SESSION['reports_dir'] . '/PurchOrder.pdf','wb');
	fwrite ($fp, $pdfcode);
	fclose ($fp);

	include('includes/htmlMimeMail.php');

	$mail = new htmlMimeMail();
	$attachment = $mail->getFile($_SESSION['reports_dir'] . '/PurchOrder.pdf');
	$mail->setText( _('Please find herewith our purchase order number').' ' . $OrderNo);
	$mail->setSubject( _('Purchase Order Number').' ' . $OrderNo);
	$mail->addAttachment($attachment, 'PurchOrder.pdf', 'application/pdf');
	$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . "<" . $_SESSION['CompanyRecord']['email'] .">");
	$result = $mail->send(array($_POST['EmailTo']));
	if ($result==1){
		$failed = false;
		echo '<P>';
		prnMsg( _('Purchase order'). ' ' . $OrderNo.' ' . _('has been emailed to') .' ' . $_POST['EmailTo'] . ' ' . _('as directed'), 'success');
	} else {
		$failed = true;
		echo '<P>';
		prnMsg( _('Emailing Purchase order'). ' ' . $OrderNo.' ' . _('to') .' ' . $_POST['EmailTo'] . ' ' . _('failed'), 'error');
	}

    }

    if ($ViewingOnly==0 && !$failed) {
	$sql = "UPDATE purchorders 
			SET allowprint=0,
			status='"._('Printed')."',
			stat_comment='".$StatusComment."',
				dateprinted='" . Date('Y-m-d') . "' 
			WHERE purchorders.orderno=" .$OrderNo;
	$result = DB_query($sql,$db);
    }

} /* There was enough info to either print or email the purchase order */
 else { /*the user has just gone into the page need to ask the question whether to print the order or email it to the supplier */

	include ('includes/header.inc');
	echo '<FORM ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';

	if ($ViewingOnly==1){
		echo '<INPUT TYPE=HIDDEN NAME="ViewingOnly" VALUE=1>';
	}
	echo '<BR><BR>';
	echo '<INPUT TYPE=HIDDEN NAME="OrderNo" VALUE="'. $OrderNo. '">';
	echo '<TABLE><TR><TD>'. _('Print or Email the Order'). '</TD><TD>
		<SELECT NAME="PrintOrEmail">';

	if (!isset($_POST['PrintOrEmail'])){
		$_POST['PrintOrEmail'] = 'Print';
	}

	if ($_POST['PrintOrEmail']=='Print'){
		echo '<OPTION SELECTED VALUE="Print">'. _('Print');
		echo '<OPTION VALUE="Email">' . _('Email');
	} else {
		echo '<OPTION VALUE="Print">'. _('Print');
		echo '<OPTION SELECTED VALUE="Email">'. _('Email');
	}
	echo '</SELECT></TD></TR>';

	echo '<TR><TD>'. _('Show Amounts on the Order'). '</TD><TD>
		<SELECT NAME="ShowAmounts">';
		
	if (!isset($_POST['ShowAmounts'])){
		$_POST['ShowAmounts'] = 'Yes';
	}

	if ($_POST['ShowAmounts']=='Yes'){
		echo '<OPTION SELECTED VALUE="Yes">'. _('Yes');
		echo '<OPTION VALUE="No">' . _('No');
	} else {
		echo '<OPTION VALUE="Yes">'. _('Yes');
		echo '<OPTION SELECTED VALUE="No">'. _('No');
	}
	
	echo '</SELECT></TD></TR>';
	if ($_POST['PrintOrEmail']=='Email'){
		$ErrMsg = _('There was a problem retrieving the contact details for the supplier');
		$SQL = "SELECT suppliercontacts.contact,
				suppliercontacts.email
			FROM suppliercontacts INNER JOIN purchorders
			ON suppliercontacts.supplierid=purchorders.supplierno
			WHERE purchorders.orderno=$OrderNo";
		$ContactsResult=DB_query($SQL,$db, $ErrMsg);

		if (DB_num_rows($ContactsResult)>0){
			echo '<TR><TD>'. _('Email to') .':</TD><TD><SELECT NAME="EmailTo">';
			while ($ContactDetails = DB_fetch_array($ContactsResult)){
				if (strlen($ContactDetails['email'])>2 AND strpos($ContactDetails['email'],'@')>0){
					if ($_POST['EmailTo']==$ContactDetails['email']){
						echo '<OPTION SELECTED VALUE="' . $ContactDetails['email'] . '">' . $ContactDetails['Contact'] . ' - ' . $ContactDetails['email'];
					} else {
						echo '<OPTION VALUE="' . $ContactDetails['email'] . '">' . $ContactDetails['contact'] . ' - ' . $ContactDetails['email'];
					}
				}
			}
			echo '</SELECT></TD></TR></TABLE>';
		} else {
			echo '</TABLE><BR>';
			prnMsg ( _('There are no contacts defined for the supplier of this order') . '. ' .
				_('You must first set up supplier contacts before emailing an order'), 'error');
			echo '<BR>';
		}
	} else {
		echo '</TABLE>';
	}
	echo '<BR><INPUT TYPE=SUBMIT NAME="DoIt" VALUE="' . _('OK') . '">';
	echo '</FORM>';
	include('includes/footer.inc');
}
?>
