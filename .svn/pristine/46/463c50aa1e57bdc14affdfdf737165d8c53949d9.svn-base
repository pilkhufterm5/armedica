<?php

/* RELATIONSHIP BETWEEN EXTERNAL AND INTERNAL INVOICES

Realhost	
December 2006 - bowikaxu

*/

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Relacion de Facturas Internas y Externas');
include('includes/header.inc');


echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . "' METHOD=POST>";

echo '<CENTER><TABLE CELLPADDING=2><TR>';

if (!isset($_POST['FromDate'])){
	$_POST['FromDate']=Date($_SESSION['DefaultDateFormat'], mktime(0,0,0,Date('m'),1,Date('Y')));
}
if (!isset($_POST['ToDate'])){
	$_POST['ToDate'] = Date($_SESSION['DefaultDateFormat']);
}
echo '<TD>' . _('Desde') . ":</TD><TD><INPUT TYPE=TEXT NAME='FromDate' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['FromDate'] . '></TD>';
echo '<TD>' . _('Hasta') . ":</TD><TD><INPUT TYPE=TEXT NAME='ToDate' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['ToDate'] . '></TD></TR>';

echo '<TR><TD>' ._('Invoice').' ' ._('Desde') . ":</TD><TD><INPUT TYPE=TEXT NAME='FromInv' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['FromInv'] . '></TD>';
echo '<TD>' ._('Invoice') .' '. _('Hasta') . ":</TD><TD><INPUT TYPE=TEXT NAME='ToInv' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['ToInv'] . '></TD></TR>';

echo '<TR><TD>'. _('Almacen').':</TD><TD><SELECT name="StockLocation"> ';

$sql = 'SELECT loccode, locationname FROM locations';
$resultStkLocs = DB_query($sql,$db);
while ($myrow=DB_fetch_array($resultStkLocs)){
	if ($myrow['loccode']==$_POST['StockLocation']){
		 echo '<OPTION SELECTED Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		 $_POST['StockLocation']=$myrow['loccode'];
	} else {
		 echo '<OPTION Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
	}
}

echo '</SELECT></TD>';

echo "<TD>"._('Ordenar por')."</TD><TD>
<SELECT NAME=orderby>
<OPTION SELECTED VALUE=date>Fecha
<OPTION VALUE=int>Num. Interno
<OPTION VALUE=ext>Num. Externo
</SELECT></TD></TR>";

echo "</TR></TABLE><INPUT TYPE=SUBMIT NAME='ShowResults' VALUE='" . _('Ver Facturas') . "'>";
echo '<HR>';

echo '</FORM></CENTER>';

if (isset($_POST['ShowResults'])){
   $SQL_FromDate = FormatDateForSQL($_POST['FromDate']);
   $SQL_ToDate = FormatDateForSQL($_POST['ToDate']);
   
   $sql = "SELECT debtortrans.transno,
   		debtortrans.trandate,
   		debtortrans.order_,
   		debtortrans.id,
		debtortrans.debtorno,
		debtorsmaster.name,
		c.serie,
        c.folio,
		debtortrans.branchcode,
		debtortrans.reference,
		debtortrans.invtext,
		debtortrans.order_,
		debtortrans.rate,
		debtortrans.ovamount,
		debtortrans.ovgst,
		ovamount+ovgst+ovfreight+ovdiscount as totalamt,
		rh_invoicesreference.extinvoice,
		not isnull(v.id_salesorders) is_transportista
	FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id
	left join rh_vps__transportista v on debtortrans.order_ = v.id_salesorders,
	debtorsmaster, rh_invoicesreference
	WHERE 
	debtortrans.debtorno = debtorsmaster.debtorno
	AND ";

   $sql = $sql . "trandate >='" . $SQL_FromDate . "' AND trandate <= '" . $SQL_ToDate . "' AND type = 10";
   $sql .= " AND debtortrans.id = rh_invoicesreference.ref
   AND rh_invoicesreference.loccode ='".$_POST['StockLocation']."'";
   
   if(isset($_POST['FromInv']) && $_POST['FromInv']>0){
   	$sql .= " AND extinvoice >= ".$_POST['FromInv']." AND extinvoice <= ".$_POST['ToInv']."";
   }
   
   if($_POST['orderby']=='date'){
   	$sql .= " ORDER BY trandate ASC";
   }elseif($_POST['orderby']=='int'){
   	$sql .= " ORDER BY transno ASC";
   }else {
   	$sql .= " ORDER BY extinvoice ASC";
   }
   
   $TransResult = DB_query($sql, $db,$ErrMsg,$DbgMsg);
   $ErrMsg = _('The customer transactions for the selected criteria could not be retrieved because') . ' - ' . DB_error_msg($db);
   $DbgMsg =  _('The SQL that failed was');
	
   echo '<TABLE CELLPADDING=2 BORDER=2>';

   $tableheader = "<TR><TD class='tableheader'>" . _('Interna') . "</TD>
   			<TD class='tableheader'>" . _('Externa') . "</TD>
			<TD class='tableheader'>" . _('Fecha') . "</TD>
			<TD class='tableheader'>" . _('Cliente') . "</TD>
			<TD class='tableheader'>" . _('Branch') . "</TD>
			<TD class='tableheader'>" . _('Referencia') . "</TD>
			<TD class='tableheader'>" . _('Comentarios') . "</TD>
			<TD class='tableheader'>" . _('Orden') . "</TD>
			<TD class='tableheader'>" . _('Ex Rate') . "</TD>
			<TD class='tableheader'>" . _('Subtotal') . "</TD>
			<TD class='tableheader'>" . _('Tax') . "</TD>
			<TD class='tableheader'>" . _('Total') . "</TD>";
	echo $tableheader;

	$RowCounter = 1;
	$k = 0; //row colour counter

	while ($myrow=DB_fetch_array($TransResult)) {

		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k++;
		}

		//echo "<BR><BR>".$_POST['StockLocation'];
		$SQL = "SELECT rh_invoicesreference.extinvoice,rh_invoicesreference.loccode, locations.rh_serie FROM rh_invoicesreference, locations WHERE rh_invoicesreference.ref = ".$myrow['id']." AND rh_invoicesreference.loccode = '".$_POST['StockLocation']."' AND locations.loccode = rh_invoicesreference.loccode";
		
		$ExtResult = DB_query($SQL, $db,$ErrMsg,$DbgMsg);
   		$ErrMsg = _('The external invoice for the selected criteria could not be retrieved because') . ' - ' . DB_error_msg($db);
   		$DbgMsg =  _('The SQL that failed was');
		$external = DB_fetch_array($ExtResult);
   		
		$format_base = "<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>";
		
		//SAINTS
		if($myrow['serie']!=""){printf("$format_base	
				<td><a target='_blank' href='%s/rh_PrintCustTrans.php?%&FromTransNo=%s&InvOrCredit=Invoice'><IMG BORDER=0 SRC='%s' TITLE='" . _('Click to preview the invoice') . "'></a></td>
				<td><a target='_blank' href='%s/rh_printFE.php?isTransportista=" . $myrow['is_transportista'] . "&transno=" . $myrow['transno']."'><IMG BORDER=0 SRC='".$rootpath.'/css/'.$theme.'/images/pdf.gif'."' TITLE='" . _('Click para imprimir') . "'></a></td>
				</tr>",
				$myrow['transno'],
				//$external['rh_serie'].$external['extinvoice'],
				$myrow['serie'].$myrow['folio'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['name'],
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				$myrow['rate'],
				number_format($myrow['ovamount'],2),
				number_format($myrow['ovgst'],2),
				number_format($myrow['totalamt'],2),
				$rootpath,
				SID,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images/preview.gif',
				$rootpath,
				SID,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images/pdf.gif');}
		
		//SAINTS		
		else{printf("$format_base	
				<td><a target='_blank' href='%s/rh_PrintCustTrans.php?%&FromTransNo=%s&InvOrCredit=Invoice'><IMG BORDER=0 SRC='%s' TITLE='" . _('Click to preview the invoice') . "'></a></td>
				<td><a target='_blank' href='%s/rh_printFE.php?isTransportista=" . $myrow['is_transportista'] . "&transno=" . $myrow['transno']."'><IMG BORDER=0 SRC='".$rootpath.'/css/'.$theme.'/images/pdf.gif'."' TITLE='" . _('Click para imprimir') . "'></a></td>
				</tr>",
				$myrow['transno'],
				$external['rh_serie'].$external['extinvoice'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['name'],
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				$myrow['rate'],
				number_format($myrow['ovamount'],2),
				number_format($myrow['ovgst'],2),
				number_format($myrow['totalamt'],2),
				$rootpath,
				SID,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images/preview.gif',
				$rootpath,
				SID,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images/pdf.gif');}

		$RowCounter++;
		If ($RowCounter == 12){
			$RowCounter=1;
			echo $tableheader;
		}
	//end of page full new headings if
	}
	//end of while loop

 echo '</TABLE>';
}

include('includes/footer.inc');

?>
