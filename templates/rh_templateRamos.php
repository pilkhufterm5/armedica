<?php	
$minux = 16;
include ('includes/class.pdf.php');
	
$Page_Width=612; // horizontal
$Page_Height=554; // vertical
	
$Top_Margin=30;
$Bottom_Margin=30;
$Left_Margin=30;
$Right_Margin=30;

$PageSize = array(0,0,$Page_Width,$Page_Height);
$pdf = & new Cpdf($PageSize);
$pdf->selectFont('helvetica');
//$pdf->AddFont('rassett_Outline','','BRASSETO.php');
//$pdf->SetFont('rassett_Outline','',14);


$pdf->addinfo('Author','webERP ' . $Version);
$pdf->addinfo('Creator','webERP http://www.weberp.org');

$FirstPage = true;

$line_height = 12;

while ($FromTransNo <= $_POST['ToTransNo']){

/*retrieve the invoice details from the database to print
notice that salesorder record must be present to print the invoice purging of sales orders will
nobble the invoice reprints */

	if ($InvOrCredit=='Invoice' || $InvOrCredit=='notaC') {
				
		$sql = "SELECT debtorno FROM rh_transaddress WHERE type = $rh_tipo AND transno = ".$FromTransNo."";
		$res_address = DB_query($sql,$db);
		if(DB_num_rows($res_address)>0){
				
		$sql = 'SELECT debtortrans.trandate,		DATE_FORMAT(debtortrans.trandate, "%m") as mm,	DATE_FORMAT(debtortrans.trandate, "%d") as dd, 	DATE_FORMAT(debtortrans.trandate, "%Y") as yy,
			debtortrans.id AS ID,
			debtortrans.ovamount,
			debtortrans.ovdiscount,
			debtortrans.ovfreight,
			debtortrans.order_,
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
			debtorsmaster.rh_Tel,
			paymentterms.terms,
			salesorders.deliverto,
			salesorders.deladd1,
			salesorders.deladd2,
			salesorders.deladd3,
			salesorders.deladd4,
			salesorders.customerref,
			salesorders.orderno,
			salesorders.orddate,
			rh_locations.locationname,
			shippers.shippername,
			custbranch.brname,
			custbranch.phoneno,
			custbranch.braddress1,
			custbranch.braddress2,
			custbranch.braddress3,
			custbranch.braddress4,
			custbranch.brpostaddr1,
			custbranch.brpostaddr2,
			custbranch.brpostaddr3,
			custbranch.brpostaddr4,
			custbranch.taxgroupid,
			salesman.salesmanname,
			debtortrans.debtorno,
			debtortrans.branchcode,
			debtortrans.rh_printnarrative
		FROM debtortrans,
			debtorsmaster,
			custbranch,
			salesorders,
			shippers,
			salesman,
			rh_locations,
			paymentterms,
			rh_transaddress
		WHERE debtortrans.order_ = salesorders.orderno
		AND debtortrans.type='.$rh_tipo.'
		AND rh_transaddress.type = '.$rh_tipo.'
		AND rh_transaddress.transno = debtortrans.transno
		AND debtortrans.transno=' . $FromTransNo.'
		AND debtortrans.shipvia=shippers.shipper_id
		AND debtortrans.debtorno=debtorsmaster.debtorno
		AND debtorsmaster.paymentterms=paymentterms.termsindicator
		AND debtortrans.debtorno=custbranch.debtorno
		AND debtortrans.branchcode=custbranch.branchcode
		AND custbranch.salesman=salesman.salesmancode
		AND salesorders.fromstkloc_virtual=rh_locations.loccode';
	}else {
		$sql = 'SELECT debtortrans.trandate,		DATE_FORMAT(trandate, "%m") as mm,	DATE_FORMAT(trandate, "%d") as dd, 	DATE_FORMAT(trandate, "%Y") as yy,
			debtortrans.id AS ID,
			debtortrans.ovamount,
			debtortrans.ovdiscount,
			debtortrans.ovfreight,
			debtortrans.order_,
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
			debtorsmaster.invaddrbranch,
			debtorsmaster.taxref,
			debtorsmaster.rh_Tel,
			paymentterms.terms,
			salesorders.deliverto,
			salesorders.deladd1,
			salesorders.deladd2,
			salesorders.deladd3,
			salesorders.deladd4,
			salesorders.customerref,
			salesorders.orderno,
			salesorders.orddate,
			rh_locations.locationname,
			shippers.shippername,
			custbranch.brname,
			custbranch.phoneno,
			custbranch.braddress1,
			custbranch.braddress2,
			custbranch.braddress3,
			custbranch.braddress4,
			custbranch.brpostaddr1,
			custbranch.brpostaddr2,
			custbranch.brpostaddr3,
			custbranch.brpostaddr4,
			custbranch.taxgroupid,
			salesman.salesmanname,
			debtortrans.debtorno,
			debtortrans.branchcode,
			debtortrans.rh_printnarrative
		FROM debtortrans,
			debtorsmaster,
			custbranch,
			salesorders,
			shippers,
			salesman,
			rh_locations,
			paymentterms
		WHERE debtortrans.order_ = salesorders.orderno
		AND debtortrans.type='.$rh_tipo.'
		AND debtortrans.transno=' . $FromTransNo.'
		AND debtortrans.shipvia=shippers.shipper_id
		AND debtortrans.debtorno=debtorsmaster.debtorno
		AND debtorsmaster.paymentterms=paymentterms.termsindicator
		AND debtortrans.debtorno=custbranch.debtorno
		AND debtortrans.branchcode=custbranch.branchcode
		AND custbranch.salesman=salesman.salesmancode
		AND salesorders.fromstkloc_virtual=rh_locations.loccode';
	}
	
	if ($_POST['PrintEDI']=='No'){
		$sql = $sql . ' AND debtorsmaster.ediinvoices=0';
	}
} else {

	$sql = 'SELECT debtortrans.trandate,		SUBSTRING(DATE_FORMAT(trandate, "%M"),1,3) as mm,	DATE_FORMAT(trandate, "%d") as dd, 	DATE_FORMAT(trandate, "%Y") as yy,
			debtortrans.ovamount,
			debtortrans.id AS ID,
			debtortrans.ovdiscount,
			debtortrans.ovfreight,
			debtortrans.order_,0
			debtortrans.ovgst,
			debtortrans.rate,
			debtortrans.invtext,
			debtorsmaster.invaddrbranch,
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
			debtorsmaster.rh_Tel,
			custbranch.brname,
			custbranch.phoneno,
			custbranch.braddress1,
			custbranch.braddress2,
			custbranch.braddress3,
			custbranch.braddress4,
			custbranch.brpostaddr1,
			custbranch.brpostaddr2,
			custbranch.brpostaddr3,
			custbranch.brpostaddr4,
			custbranch.taxgroupid,
			salesman.salesmanname,
			debtortrans.debtorno,
			debtortrans.branchcode,
			paymentterms.terms,
			debtortrans.rh_printnarrative
		FROM debtortrans,
			debtorsmaster,
			custbranch,
			salesman,
			paymentterms
		WHERE debtortrans.type=11
		AND debtorsmaster.paymentterms = paymentterms.termsindicator
		AND debtortrans.transno=' . $FromTransNo .'
		AND debtortrans.debtorno=debtorsmaster.debtorno
		AND debtortrans.debtorno=custbranch.debtorno
		AND debtortrans.branchcode=custbranch.branchcode
		AND custbranch.salesman=salesman.salesmancode';

	if ($_POST['PrintEDI']=='No'){
		$sql = $sql . ' AND debtorsmaster.ediinvoices=0';
	}
}

$result=DB_query($sql,$db);
	   
if (DB_error_no($db)!=0){

	$title = _('Transaction Print Error Report');
	include ('includes/header.inc');

	echo '<BR>' . _('There was a problem retrieving the invoice or credit note details for note number') . ' ' . $InvoiceToPrint . ' ' . _('from the database') . '. ' . _('To print an invoice, the sales order record, the customer transaction record and the branch record for the customer must not have been purged') . '. ' . _('To print a credit note only requires the customer, transaction, salesman and branch records be available');
	if ($debug==1){
		echo _('The SQL used to get this information that failed was') . "<BR>$sql";
	}
	break;
	include ('includes/footer.inc');
	exit;
}
	   
if (DB_num_rows($result)==1){
	$myrow = DB_fetch_array($result);
	
	$extCN = $myrow['consignment'];

	$ID = $myrow['ID'];
	$sql2 = "SELECT rh_invoicesreference.extinvoice, rh_locations.rh_serie FROM rh_invoicesreference, rh_locations WHERE rh_invoicesreference.ref = ".$ID."
	AND rh_locations.loccode = rh_invoicesreference.loccode";
	$Res = DB_query($sql2,$db);
	$ExtRes = DB_fetch_array($Res);
		
	$ExchRate = $myrow['rate'];

        /*
         * Juan Mtz 0.o
         * realhost
         * 31-Agosto-2009
         * 
         * Se modifico la consulta para que se mostraran los productos del pedido por
         * orden en como fueron agregados a la orden de compra
         */

	if ($InvOrCredit=='Invoice' || $InvOrCredit=='notaC'){

		$sql = 'SELECT stockmoves.rh_orderline, stockmoves.stockid,
			stockmaster.description,
			stockmaster.longdescription,
			stockmaster.units,
			-stockmoves.qty as quantity,
			stockmoves.discountpercent,
			stockmoves.stkmoveno,
			((1 - stockmoves.discountpercent) * stockmoves.price * ' . $ExchRate . '* -stockmoves.qty) AS fxnet,
			(stockmoves.price * ' . $ExchRate . ') AS fxprice,
			stockmoves.narrative,
			stockmaster.units
		FROM stockmoves,
			stockmaster
		WHERE stockmoves.stockid = stockmaster.stockid
		AND stockmoves.type='.$rh_tipo.'
		AND stockmoves.transno=' . $FromTransNo . '
		AND stockmoves.show_on_inv_crds=1 ORDER BY stockmoves.rh_orderline';
	} else {
		/* only credit notes to be retrieved */
		$sql = 'SELECT stockmoves.rh_orderline, stockmoves.stockid,
			stockmaster.description,
			stockmaster.longdescription,
			stockmaster.units,
			stockmoves.qty as quantity,
			stockmoves.stkmoveno,
			stockmoves.discountpercent,
			((1 - stockmoves.discountpercent) * stockmoves.price * ' . $ExchRate . ' * stockmoves.qty) AS fxnet,
			(stockmoves.price * ' . $ExchRate . ') AS fxprice,
			stockmoves.narrative,
			stockmaster.units
		FROM stockmoves,
			stockmaster
		WHERE stockmoves.stockid = stockmaster.stockid
		AND stockmoves.type=11
		AND stockmoves.transno=' . $FromTransNo . '
		AND stockmoves.show_on_inv_crds=1 ORDER BY stockmoves.rh_orderline';
	}

	$result=DB_query($sql,$db);
	if (DB_error_no($db)!=0) {
		$title = _('Transaction Print Error Report');
		include ('includes/header.inc');
		echo '<BR>' . _('There was a problem retrieving the invoice or credit note stock movement details for invoice number') . ' ' . $FromTransNo . ' ' . _('from the database');
		if ($debug==1){
			echo '<BR>' . _('The SQL used to get this information that failed was') . "<BR>$sql";
		}
		include('includes/footer.inc');
		exit;
	}

	if (DB_num_rows($result)>0){

		$PageNumber = 1;			

		include('rh_templateheaderRamos.inc.php');

		//no factura
		if ($rh_tipo==10){		
			$pdf->addTextWrap(481.5,530,100,$rh_fontsize+1,$extInvoice.$ExtRes['extinvoice'], 'right');
			$YPos = 390 - $line_height + 8;
		}else{
			$pdf->addTextWrap(451.5,530-5,100,$rh_fontsize+1,$extInvoice.$extCN, 'right');
			$YPos = 410-35 - $line_height;
		}
		
		$CeroTax = 0;
			
$rh_fontsize=$rh_fontsize+2;
		while ($myrow2=DB_fetch_array($result)){
			
			if ($YPos <= 144){
	   		        include ('rh_templateheaderRamos.inc.php');
			}

			$DisplayPrice = number_format($myrow2['fxprice'],2);
			
			//Juan
			//Reallhost
			//06-Ago-2009
			//Variable a utilizar para que nos muestre el precio con el descuento ya aplicado

			//Eleazar
			//Realhost
			//15-Ago-2009
			//En lugar del numero 4 puse el numero 2 para que solo muestre dos decimales.
			//$rh_DisplayPrice = number_format($DisplayPrice-($DisplayPrice*$myrow2['discountpercent']),4);
			$rh_DisplayPrice = number_format($myrow2['fxprice']-($myrow2['fxprice']*$myrow2['discountpercent']),4);

			$DisplayQty = number_format($myrow2['quantity'],2);
			$DisplayNet = number_format($myrow2['fxnet'],2);
			$sql = "SELECT taxrate FROM stockmovestaxes WHERE stkmoveno = '".$myrow2['stkmoveno']."'";
			$res = DB_query($sql,$db);
			if(DB_num_rows($res)>=1){
				// si tiene tax no sumar a 0% IVA
				$trate = DB_fetch_array($res);
				if($trate['taxrate']!=0){
					//su tax no es cero
				}else {
					$CeroTax += $myrow2['fxnet'];
				}
			}else {
				// no tiene tax
				$CeroTax += $myrow2['fxnet'];
			}

			if ($myrow2['discountpercent']==0){
				$DisplayDiscount ='';
			} else {
				$DisplayDiscount = number_format($myrow2['discountpercent']*100,2) . '%';
			}
			
			if ($rh_tipo==10)
			{	
				//cantidad
				$pdf->addTextWrap(362-12,$YPos,54,$rh_fontsize-1,$DisplayQty, 'right');
				
				//precio
				$pdf->addTextWrap(423-12,$YPos,72,$rh_fontsize-1,$rh_DisplayPrice, 'right');

				//total
				$pdf->addTextWrap(504-15,$YPos,81,$rh_fontsize-1,$DisplayNet, 'right');

				//Codigo
				$pdf->addTextWrap(30-5,$YPos,81,$rh_fontsize-2.5,$myrow2['stockid'], 'left');
			}
			else
			{
				//total
				$pdf->addTextWrap(480,$YPos,81,$rh_fontsize-1,$DisplayNet, 'right');

				$numberFact = array();
				$numberFact = explode('-',$myrow2['narrative']);
				$numF = explode('.',$numberFact[0]);
	
				//Codigo
				//$pdf->addTextWrap(30+8,$YPos,81,$rh_fontsize-2.5,$myrow2['stockid'], 'left');
				$pdf->addTextWrap(30+8,$YPos,81,$rh_fontsize-2.5,$numF[1], 'left');				
			}

			//Descripcion
			$rh_arraydesc = array();
			$rh_arraydesc =  explode(' ', $myrow2['longdescription']);
			//$pdf->addTextWrap(30,120,81,7,count($rh_arraydesc),'left');
			$rh_sumcarac = 0;
			$rh_arraydesclines = array();
			$rh_lines = 0;
			$rh_arraydesclines[$rh_lines] = "";
			foreach($rh_arraydesc as $arraydesc){
				$rh_sumcarac = $rh_sumcarac + strlen($arraydesc);
				if($rh_sumcarac >= 70 ){
					//break;
					//$rh_lines ++;
					$rh_sumcarac = strlen($arraydesc);
					$rh_arraydesclines[$rh_lines] .= $arraydesc." ";
				}else{
					$rh_arraydesclines[$rh_lines] .= $arraydesc." ";
				}
			}
			
			if ($rh_tipo==10){	
				$showCar = 207;
			}else{
				$showCar = 307;
			}
			
			foreach($rh_arraydesclines as $rh_lines){
				$pdf->addTextWrap(105,$YPos,$showCar,$rh_fontsize-2,$rh_lines, 'left');
				$YPos -= $line_height;
			}
		
			if ($rh_tipo==10){
				//descripcion or narrative
				if($myrow['rh_printnarrative']==0){
					$pdf->y = $pdf->y - 9.5;
					$pdf->x = 117;
					//$pdf->addTextWrap(90,$YPos,81,7,$myrow2['longdescription'], 'left');
					//$pdf->MultiCell(240,$line_height,$myrow2['longdescription'],0,'L',0,15);
					//$YPos = ($Page_Height - $pdf->GetY()) - $line_height;
				}else{
					$pdf->y = $pdf->y - 9.5;
					$pdf->x = 117;
					$pdf->SetFontSize($rh_fontsize-1);
					$pdf->MultiCell(240,$line_height,$myrow2['narrative'],0,'L',0,15);
					//$YPos = ($Page_Height - $pdf->GetY()) - $line_height;
				}
			}else{
				$pdf->addTextWrap(105,$YPos,$showCar,$rh_fontsize-2,$myrow2['narrative'], 'left');
				$YPos -= $line_height;
			}
			//Eleazar
			//Realhost
			//17-Ago-2009
			//con esto los comentarios de la factura para que sea visible en la pantalla.
			//$pdf->addTextWrap(30, 125, 81,7,$myrow['invtext'], 'left');
			$rh_arraydesc1 = array();


		/*	if(ereg("\\\\\\\\r\\\\\\\\n",$myrow['invtext']))
			{
				$cadena = str_replace("\\\\\\\\r\\\\\\\\n", '\r\n', $myrow['invtext']);
			}
			else
				if(ereg("\\\\r\\\\n",$myrow['invtext']))
				{
					$cadena = str_replace("\\\\r\\\\n", '\r\n', $myrow['invtext']);
				}
				else*/

                                //iJPe  realhost    2010-02-11
                                //Condicional para evitar que se impriman los comentarios cuando sea una nota de cargo
                                if ($rh_tipo==10)
                                {
                                    if(ereg("\\r\\n",$myrow['invtext']))
                                    {
                                            $cadena = str_replace("\\r\\n", '\r\n', $myrow['invtext']);
                                    }
                                    else
                                    if(ereg("\\\\r\\\\n",$myrow['invtext']))
                                    {
                                            $cadena = str_replace("\\\\r\\\\n", '\r\n', $myrow['invtext']);
                                    }
                                    else
                                    {
                                            //$j = 0;
                                            $cadena = $myrow['invtext'];/*
                                            if(strlen($cadena) >= 110)
                                            {
                                                    $rh_conteo = strlen($cadena);
                                                    while($rh_conteo >= 110)
                                                    {
                                                            $nueva_cadena = substr($cadena, 0, 110);
                                                            $nueva_cadena2 = substr($cadena, 0, $rh_conteo);
                                                            $rh_conteo = strlen($nueva_cadena);
                                                            $pdf->addTextWrap(30,140-$j*10,345,7,$nueva_cadena, 'left');
                                                            $j ++;
                                                    }
                                                    $pdf->addTextWrap(30,140-$j*10,345,7,$nueva_cadena2, 'left');
                                            }*/

                                    }
                                }
				//else
				//$cadena = $myrow['invtext'];

				//	$cadena = str_replace("\\\\r\\\\n", '\r\n', $myrow['invtext']);
			
			
			//$cadena = str_replace("\\\\\\\\r\\\\\\\\n", '\r\n', $myrow['invtext']);
			$rh_arraydesc1 =  explode('\r\n', $cadena);
			unset($cadena);
			$rh_sumcarac1 = 0;
			$rh_arraydesclines1 = array();

			$rh_lines1 = 0;
			$rh_lines2 = 0;
			$rh_arraydesclines1[$rh_lines1] = "";
			$rh_nueva = "";
			$rh_sumar = 0;
			for($i = 0; $i < count($rh_arraydesc1); $i=$i + 1)
			{
				if(strlen($rh_arraydesc1[$i]) >= 115)
				{
					$nuevo = 105;
					while($nuevo >= 105)
					{
						$nueva_cadena = substr($rh_arraydesc1[$i], 0, 105);
						$nueva_cadena2 = substr($rh_arraydesc1[$i], 105, strlen($rh_arraydesc1[$i]));
						$nuevo = strlen($nueva_cadena2);
						$pdf->addTextWrap(30,156-$i*10-1,340,$rh_fontsize-2,$nueva_cadena, 'left');
						$pdf->addTextWrap(30,156-$i*10-12,340,$rh_fontsize-2,$nueva_cadena2, 'left');
						$rh_sumar = 12;
					}
				}
				else
				$pdf->addTextWrap(30,156-$i*10-$rh_sumar,340,$rh_fontsize-2,$rh_arraydesc1[$i], 'left');
			
			}

			/*$pdf->y = $pdf->y - 9.5;
				$pdf->x = 117;
				$pdf->SetFontSize(7);
				$pdf->MultiCell(240,$line_height,$myrow['invtext'],0,'L',0,15);
				$YPos = ($Page_Height - $pdf->GetY()) - $line_height;
			
			/*$pdf->y = $pdf->y - 9.5;
			$pdf->x = 117;
			$cadena = str_replace("\\\\\\\\r\\\\\\\\n", '\r\n', $myrow['invtext']);

			$pdf->MultiCell(240,$line_height,$cadena,0,'L',0,15);
			$YPos = ($Page_Height - $pdf->GetY()) - $line_height;*/

			//$pdf->addTextWrap(30,130,120,20,$rh_nueva, 'left');

			//foreach($rh_arraydesclines1 as $rh_lines2){
				//$rh_lines2++;
				//$pdf->addTextWrap(30,130,81,7,$rh_lines2, 'left');
				//$YPos -= $line_height;
			//}
			

			//Comentarios
			/*
			$pdf->x = 121.5;
			$pdf->SetFontSize(7);
			$pdf->MultiCell(288,$line_height,$myrow2['narrative'],0,'J',0,15);
			$YPos = ($Page_Height - $pdf->GetY()) - $line_height;
			*/
			/*$rh_arraydesc = array();
			$rh_arraydesc =  explode('\\r\\n', $myrow['invtext']);
			$rh_sumcarac = 0;
			$rh_arraydesclines = array();
			$rh_lines = 0;
			$rh_conteo = 110;
			$rh_arraydesclines[$rh_lines] = "";
			foreach($rh_arraydesc as $arraydesc){
				$rh_sumcarac = $rh_sumcarac + strlen($arraydesc);
				
				while($rh_sumcarac >= 110)
				{
					$rh_lines ++;
					$nueva_cadena = substr($rh_arraydesclines[$rh_lines], 0, 110);
					$rh_arraydesclines[$rh_lines] = $nueva_cadena." ";
				}
				
					$rh_lines ++;
					$rh_arraydesclines[$rh_lines] .= $arraydesc." ";
				
			}			
			foreach($rh_arraydesclines as $rh_lines){
				$pdf->addTextWrap(121.5,$YPos-$line_height,207,7,$rh_lines, 'left');
				$YPos -= $line_height;
			}*/
			$FirstPage = 0;
		}

		if ($InvOrCredit=='Invoice' || $InvOrCredit=='notaC') {

		     $DisplaySubTot = number_format($myrow['ovamount'],2);
		     $DisplayFreight = number_format($myrow['ovfreight'],2);
		     $DisplayTax = number_format($myrow['ovgst'],2);
		     $Total = round($myrow['ovfreight']+$myrow['ovgst']+$myrow['ovamount'],2);
                     $DisplayTotal = number_format($Total,2);
		     $Display0Tax=number_format($CeroTax,2);

		} else {

		     $DisplaySubTot = number_format(-$myrow['ovamount'],2);
		     $DisplayFreight = number_format(-$myrow['ovfreight'],2);
		     $DisplayTax = number_format(-$myrow['ovgst'],2);
                     $Total = round(-$myrow['ovfreight']-$myrow['ovgst']-$myrow['ovamount'],2);
                     $DisplayTotal = number_format($Total,2);
		     $Display0Tax=number_format($CeroTax,2);
		}	

		if($myrow['currcode']=='MN'){
			$curr = ' pesos ';
		}else if($myrow['currcode']=='USD'){
			
			$curr = ' dolares ';
		}
	
		$sql = "SELECT currency,currabrev2 FROM currencies WHERE currabrev = '".$myrow['currcode']."'";
		$curr_res = DB_query($sql,$db);
		$currencystr = DB_fetch_array($curr_res);
		
		
		$tot = explode(".",$Total);
		//$pdf->addTextWrap(250,81,81,10,$Total, 'right');
		$Letra = Numbers_Words::toWords($tot[0],"es");

		if($tot[1]==0){
		$ConLetra = $Letra.' '.$currencystr['currency']." 00/100 ".$currencystr['currabrev2'];
		}else if(strlen($tot[1])>=2){
		$ConLetra = $Letra.' '.$currencystr['currency'].' '.$tot[1]."/100 ".$currencystr['currabrev2'];
		}else {
		$ConLetra = $Letra.' '.$currencystr['currency'].' '.$tot[1]."0/100 ".$currencystr['currabrev2'];
		}

		if ($rh_tipo==10)
		{

                        /*
                         * iJPe
                         * 2010-03-24
                         * Solicitud realizada por Mayra de Mangueras
                         */
                        //Aparicion de remisiones en la impresion de factura

                        $sqlFromRem = "SELECT shipment FROM rh_invoiceshipment WHERE invoice = '".$FromTransNo."'";
                        $resFromRem = DB_query($sqlFromRem,$db);

                        $glue = '';
                        while($rowFromRem = DB_fetch_array($resFromRem))
                        {
                            $listRem .= $glue." ".$rowFromRem['shipment'];
                            $glue = ',';
                        }

                        $pdf->addTextWrap(36,110,280,$rh_fontsize-2,"Rem: ".$listRem, 'left');


			//total letra
			//$pdf->addTextWrap(126,153,288,8,"(".$ConLetra.")", 'left');
			$pdf->y = $Page_Height - 129 + 51 - 6;
			$pdf->x = 36;
			$pdf->SetFontSize($rh_fontsize-2);
			$pdf->MultiCell(432,$line_height,strtoupper($ConLetra),0,'J',0,15);

			//subtotal
			$pdf->addTextWrap(504-15,97-51,81,$rh_fontsize+1,$DisplaySubTot, 'right');
	/*
			//iva %
			if($myrow['taxgroupid']==8){
				$pdf->addTextWrap(360,144-51,72,10,'10%', 'right');	
			}else {
				$pdf->addTextWrap(360,144-51,72,10,'15%', 'right');
			}
	*/
			//iva
			$pdf->addTextWrap(504-15,75-51,81,$rh_fontsize+1,$DisplayTax, 'right');

			//total
			$pdf->addTextWrap(504-15,51-51,81,$rh_fontsize+1,$DisplayTotal, 'right');
		}
		else
		{
			//total letra
			//$pdf->addTextWrap(126,153,288,8,"(".$ConLetra.")", 'left');
			$pdf->y = $Page_Height - 400 + 51 + 49 + 11;
			$pdf->x = 36;
			$pdf->SetFontSize($rh_fontsize-2);
			$pdf->MultiCell(432,$line_height,strtoupper($ConLetra),0,'J',0,15);

			/*//subtotal
			$pdf->addTextWrap(490,353-51-20,81,$rh_fontsize+1,$DisplaySubTot, 'right');
	/*
			//iva %
			if($myrow['taxgroupid']==8){
				$pdf->addTextWrap(360,144-51,72,10,'10%', 'right');	
			}else {
				$pdf->addTextWrap(360,144-51,72,10,'15%', 'right');
			}
	*/
			//iva
			/*$pdf->addTextWrap(490,338-51-20,81,$rh_fontsize+1,$DisplayTax, 'right');*/

                        //iJPe  2010-01-30
                        //Modificacion fija para mangueras, no es lo ideal pero temporalmente esto sera fijo

                        //subtotal
			$pdf->addTextWrap(490,353-51-20,81,$rh_fontsize+1,'0.00', 'right');
                        //iva
			$pdf->addTextWrap(490,338-51-20,81,$rh_fontsize+1,$DisplaySubTot, 'right');

			//total
			$pdf->addTextWrap(490,323-51-20,81,$rh_fontsize+1,$DisplayTotal, 'right');
		}

	}
	}
	$FromTransNo++;
}

$pdfcode = $pdf->output();
$len = strlen($pdfcode);

if ($len <1020){
	include('includes/header.inc');
	echo '<P>' . _('There were no transactions to print in the range selected');
	include('includes/footer.inc');
	exit;
}

if (isset($_GET['Email'])){ //email the invoice to address supplied
	
	include ('includes/htmlMimeMail.php');

	$mail = new htmlMimeMail();
	$filename = $_SESSION['reports_dir'] . '/' . $InvOrCredit . $_GET['FromTransNo'] . '.pdf';
	$fp = fopen($filename, 'wb');
	fwrite ($fp, $pdfcode);
	fclose ($fp);

	$attachment = $mail->getFile($filename);
	$mail->setText(_('Please find attached') . ' ' . _($InvOrCredit) . ' ' . $ExtRes['rh_serie'].$ExtRes['extinvoice']);
	$mail->SetSubject(_($InvOrCredit) . ' ' . $ExtRes['rh_serie'].$ExtRes['extinvoice']);
	$mail->addAttachment($attachment, $filename, 'application/pdf');
	$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
	$result = $mail->send(array($_GET['Email']));

	unlink($filename); //delete the temporary file

	$title = _('Emailing') . ' ' .$InvOrCredit . ' ' . _('Number') . ' ' . $FromTransNo;
	include('includes/header.inc');
	echo "<P>$InvOrCredit " . _('number') . ' ' . $_GET['FromTransNo'] . ' ' . _('has been emailed to') . ' ' . $_GET['Email'];
	include('includes/footer.inc');
	exit;

} else {
	header('Content-type: application/pdf');
	header('Content-Length: ' . $len);
	header('Content-Disposition: inline; filename=Customer_trans.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	$pdf->Stream();
}
	
?>
