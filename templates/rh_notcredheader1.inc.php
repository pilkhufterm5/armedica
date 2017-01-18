<?php
/* $Revision: 1.12 $ */
/* March 2007 bowikaxu - Impresion de Remisiones Grandes */

if (!$FirstPage){ /* only initiate a new page if its not the first */
	//$pdf->newPage();
}

$YPos = $Page_Height - $Top_Margin;
//$pdf->addText(536, 296, $FontSize, $myrow['transno']); // numero de remision
//$pdf->addText($Page_Width-180, $YPos-13, $FontSize, $FromTransNo);
//$pdf->addText($Page_Width-268, $YPos-26, $FontSize, _('Customer Code'));
$FontSize=9;
// cliente

//$pdf->addText($Page_Width-268, $YPos-39, $FontSize, _('Date'));
//$pdf->addText(498, 303, $FontSize, $myrow['dd'].'/'.$myrow['mm'].'/'.$myrow['yy']);

$pdf->addTextWrap(478-36+10+10,475.5+8-32-30+15+36-24,30,$FontSize,$myrow['dd'], 'right');
$pdf->addTextWrap(514-36+10+10,475.5+8-32-30+15+36-24,30,$FontSize,$myrow['mm'], 'right');
$pdf->addTextWrap(550-36+10+10,475.5+8-32-30+15+36-24,33,$FontSize,$myrow['yy'], 'right');
//$pdf->addText(543, 307, $FontSize, $myrow['mm']);
//$pdf->addText(570, 307, $FontSize, $myrow['yy']);

///			$pdf->addTextWrap($Left_Margin+430,$YPos+185,295,$FontSize,$myrow['consignment']. '  .'.'.');
//$pdf->addText(420, 260, $FontSize, $Usr['user_']);
/*
if ($InvOrCredit=='Invoice') {

	$pdf->addText($Page_Width-268, $YPos-52, $FontSize, _('Order No'));
	$pdf->addText($Page_Width-180, $YPos-52, $FontSize, $myrow['orderno']);
	$pdf->addText($Page_Width-268, $YPos-65, $FontSize, _('Order Date'));
	$pdf->addText($Page_Width-180, $YPos-65, $FontSize, ConvertSQLDate($myrow['orddate']));
	$pdf->addText($Page_Width-268, $YPos-78, $FontSize, _('Dispatch Detail'));
	$pdf->addText($Page_Width-180, $YPos-78, $FontSize, $myrow['shippername'] . '-' . $myrow['consignment']);
	$pdf->addText($Page_Width-268, $YPos-91, $FontSize, _('Dispatched From'));
	$pdf->addText($Page_Width-180, $YPos-91, $FontSize, $myrow['locationname']);
}


$pdf->addText($Page_Width-268, $YPos-104, $FontSize, _('Page'));
$pdf->addText($Page_Width-180, $YPos-104, $FontSize, $PageNumber);
*/

/*Now the customer charged to details top left */

$XPos = $Left_Margin;
$YPos = $Page_Height - $Top_Margin;

$FontSize=9;

//$pdf->addText($XPos, $YPos, $FontSize, _('Sold To') . ':');
//$XPos +=80;
$YPos = 290;

//if ($myrow['invaddrbranch']==0){
	//$pdf->addText($XPos, $YPos, $FontSize, $myrow['name']);
	if(strlen($myrow['name'])>3){
		//$pdf->addText($Left_Margin, $YPos, $FontSize, $myrow['name'].' - ('.$myrow['debtorno'] . ') ');
                $pdf->addTextWrap(81,467-40-30+15+36,360,$FontSize-1,$myrow['name'].' '.$myrow['name2'], 'left');
		$YPos -= 12;
	}
//	if(strlen($myrow['address1'].' '.$myrow['address2'])>5){
//		$pdf->addText($XPos, $YPos, $FontSize, $myrow['address1'].' '.$myrow['address2']);
//		$YPos -= 12;
//	}
//	if(strlen($myrow['address3'].' '.$myrow['address5'])>5){
//		$pdf->addText($XPos, $YPos, $FontSize, $myrow['address3'].' '.$myrow['address5']);
//		$YPos -= 12;
//	}
//	if(strlen($myrow['address4'].' '.$myrow['address6'])>5){
//		$pdf->addText($XPos, $YPos, $FontSize, $myrow['address4'].' '.$myrow['address6']);
//		$YPos -= 12;
//	}

        $rh_unirDir = $myrow['address1'].' '.$myrow['address2'].' '.$myrow['address3'];

	if (strlen($rh_unirDir) > 50)
	{
		$rh_strtmpI = substr($rh_unirDir, 0, 50);
		$rh_strtmpF = substr($rh_unirDir, 50, 100);

		//direccion
		$pdf->addTextWrap(81,467-40-30+15+36-21,360,$FontSize-1.5,$rh_strtmpI, 'left');//linea 1
		$pdf->addTextWrap(81,446-40-30+15+36-21,360,$FontSize-1.5,$rh_strtmpF, 'left');//linea 2
		$pdf->addTextWrap(81,438-40-30+15+36-21,360,$FontSize-1.5,$myrow['address4'].' '.$myrow['address5'].' C.P. '.$myrow['address6'], 'left');//linea 3
	}
	else
	{
		//direccion
		$pdf->addTextWrap(81,467-40-30+15+36-21,360,$FontSize-1,$rh_unirDir, 'left');//linea 1
		$pdf->addTextWrap(81,446-40-30+15+36-21,360,$FontSize-1,$myrow['address4'].' '.$myrow['address5'].' C.P. '.$myrow['address6'], 'left');//linea 2
	}

//	if(strlen($myrow['taxref'])>3){
		//$pdf->addText($XPos, $YPos, $FontSize, $myrow['taxref']);
                $pdf->addTextWrap(360-28,406-30+15+36-21,144,$FontSize-1,$myrow['taxref'], 'left');
		$YPos -= 12;
//	}

        $sqlNCe = "SELECT rh_locations.deladd4, rh_crednotesreference.loccode FROM rh_crednotesreference INNER JOIN rh_locations ON rh_crednotesreference.loccode = rh_locations.loccode WHERE intcn = ".$myrow['transno'];
        $resNCe = DB_query($sqlNCe, $db);

        if (DB_num_rows($resNCe)>0)
        {
            $rowNCe = DB_fetch_array($resNCe);
            $LocationNC = $rowNCe['deladd4'];
        }else{

            $sqlLoc = "SELECT rh_locations.deladd4, fromstkloc, fromstkloc_virtual FROM salesorders INNER JOIN rh_locations ON salesorders.fromstkloc_virtual = rh_locations.loccode WHERE orderno = ".$myrow['order_'];
            $resLoc = DB_query($sqlLoc, $db);
        
            if (DB_num_rows($resLoc)>0)
            {
                $rowLoc = DB_fetch_array($resLoc);
                $LocationNC = $rowLoc['deladd4'];
            }else{
                $sqlSM = "SELECT rh_locations.deladd4, stockmoves.loccode FROM stockmoves INNER JOIN rh_locations ON stockmoves.loccode = rh_locations.loccode WHERE type = 11 AND transno = ".$myrow['transno'];
                $resSM = DB_query($sqlSM, $db);
                               
                $rowSM = DB_fetch_array($resSM);
                $LocationNC = $rowSM['deladd4'];
            }
        }

        //$pdf->addTextWrap(485,430-24,72,$FontSize+1,$myrow['address5'], 'left');
        $pdf->addTextWrap(480+8,430-24-30+15+36-16,90,$FontSize+1,$LocationNC, 'left');

	//$pdf->addText(50, 205, $FontSize, $myrow['taxref'].' - Vendedor(a): '.$myrow['salesmanname']);
	//$pdf->addText(50, 220, $FontSize, $myrow['salesmanname']);
/* }else {
	//$pdf->addText($XPos, $YPos, $FontSize, $myrow['name']);
	$pdf->addText($XPos, $YPos-14, $FontSize, $myrow['brpostaddr1']);
	$pdf->addText($XPos, $YPos-28, $FontSize, $myrow['brpostaddr2']);
	$pdf->addText($XPos, $YPos-42, $FontSize, $myrow['brpostaddr3'] . ' ' . $myrow['brpostaddr4'] . ' ' . $myrow['brpostaddr5'] . ' ' . $myrow['brpostaddr6']);
}*/

$XPos = $Left_Margin;

//$YPos = 200;
$YPos = 374 -$line_height - 20 + 30 - 8;

?>