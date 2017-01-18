<?php

/* $Revision: 273 $ */

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Reporte de Ventas - Factura con Desgloce');
include('includes/header.inc');

if (isset($_POST['ShowResultsComp']))
{
	echo '<style type="text/css">
<!--
	table td{
		font-size:11px;
	}
-->
</style>';

}

//echo "<hr>POST";
//print_r($_POST);
//echo "<hr>";

if(isset($_GET['page']) AND $_GET['page']>=0){
	$pag = $_GET['page'];
	$_POST['page'] = $pag;
	$_POST['ShowResults'] = 1;
}else {
	$pag = $_POST['page'];
}
if (!isset($pag) OR $pag<0) $pag = 1; // Por defecto, pagina 1
$tampag = 50;

if(isset($_GET['FromDate'])){
	$_POST['FromDate'] = $_GET['FromDate'];
	$_POST['ShowResults'] = 1;
}
if(isset($_GET['ToDate'])){
	$_POST['ToDate'] = $_GET['ToDate'];
	$_POST['ShowResults'] = 1;
}
//if(isset($_GET['TransType'])){
//	$_POST['TransType'] = $_GET['TransType'];
//	$_POST['ShowResults'] = 1;
//}
if(isset($_GET['location'])){
	$_POST['location'] = $_GET['location'];
	$_POST['ShowResults'] = 1;
}
//if(isset($_GET['type'])){ // ver las transacciones
//
//	$_POST['ShowResults'] = 1;
//	$_POST['FromDate'] = $_GET['FromDate'];
//	$_POST['ToDate'] = $_GET['ToDate'];
//	$_POST['location'] = $_GET['Location'];
//	$_POST['TransType'] = $_GET['type'];
//
//}

echo "<FORM NAME='menu' ACTION='" . $_SERVER['PHP_SELF'] . "' METHOD=POST>";

echo '<CENTER><TABLE CELLPADDING=2><TR>';



echo "<TR><TD>"._('Location').": </TD><TD><SELECT NAME='location'>";
echo "<OPTION Value='All'>"._('Show All');

$sql = "SELECT loccode, locationname FROM rh_locations ORDER BY locationname";
$reslocations = DB_query($sql,$db);

while($loc = DB_fetch_array($reslocations)){

	if($_POST['location']==$loc['loccode']){
		echo "<OPTION SELECTED VALUE ='".$loc['loccode']."'>".$loc['locationname'];
	}else {
		echo "<OPTION VALUE ='".$loc['loccode']."'>".$loc['locationname'];
	}

}

echo "</SELECT></TD></TR>";

if (!isset($_POST['FromDate'])){
	$_POST['FromDate']=Date($_SESSION['DefaultDateFormat'], mktime(0,0,0,Date('m'),1,Date('Y')));
}
if (!isset($_POST['ToDate'])){
	$_POST['ToDate'] = Date($_SESSION['DefaultDateFormat']);
}
echo '<TD>' . _('From') . ":</TD><TD><INPUT TYPE=TEXT NAME='FromDate' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['FromDate'] . '></TD>';
echo '<TD>' . _('To') . ":</TD><TD><INPUT TYPE=TEXT NAME='ToDate' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['ToDate'] . '></TD>';
echo "</TR></TABLE><INPUT TYPE=SUBMIT NAME='ShowResults2' VALUE='" . _('Show Transactions') . "'>";
echo "<INPUT TYPE=SUBMIT NAME='ShowResultsComp' VALUE='" . _('Vista Impresi&oacute;n') . "'>";
echo '<HR>';

echo '</FORM></CENTER>';

if (isset($_POST['ShowResults']) OR isset($_POST['ShowResults2']) OR isset($_POST['ShowResultsComp'])){
   $SQL_FromDate = FormatDateForSQL($_POST['FromDate']);
   $SQL_ToDate = FormatDateForSQL($_POST['ToDate']);

	// PAGINATE QUERY
   $sql = "SELECT COUNT(debtortrans.id) as total
    FROM debtortrans";

	$sql .= " WHERE debtortrans.type = 10";	
	$sql = $sql . " AND date(trandate) >='" . $SQL_FromDate . "' AND date(trandate) <= '" . $SQL_ToDate . "'";
	$sql .=  " ORDER BY debtortrans.id";

   $ErrMsg = _('The customer transactions for the selected criteria could not be retrieved because') . ' - ' . DB_error_msg($db);
   $DbgMsg =  _('The SQL that failed was');
   $TotalResult = DB_query($sql, $db,$ErrMsg,$DbgMsg);
   $num_res = DB_fetch_array($TotalResult);
	$reg1 = ($pag-1) * $tampag;
	// FIN PAGINATE QUERY

       //switch($_POST['TransType']){

                    //case 10:
                    //SAINTS modificación de consulta para obtener series y folios de FE 28/01/2011
                            $sql = "SELECT
                                    debtortrans.transno,
                                    debtortrans.type,
                                    debtortrans.id,
                                    debtortrans.trandate,
                                    debtortrans.debtorno,
                                    debtortrans.rh_status,
                                    c.serie,
									c.folio,
                                    debtorsmaster.name,
                                    debtortrans.branchcode,
                                    debtortrans.reference,
                                    debtortrans.invtext,
                                    debtortrans.order_,
                                    debtortrans.rate,
                                debtortrans.ovamount,
                                debtortrans.ovgst,
                                debtorsmaster.paymentterms,
                                ADDDATE(debtortrans.trandate,debtorsmaster.paymentterms) AS fechaVen,
                                    debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount as totalamt,
                                    debtortrans.alloc,
                                    debtorsmaster.currcode,
                                    systypes.typename
                        FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id
                            INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
                            INNER JOIN systypes ON debtortrans.type = systypes.typeid
                                    INNER JOIN salesorders ON salesorders.orderno = debtortrans.order_
                                    INNER JOIN rh_locations ON rh_locations.loccode = salesorders.fromstkloc_virtual
                            WHERE
                                    debtortrans.type = 10 and debtortrans.rh_status != 'C'";
                            $sql = $sql . " AND date(trandate) >='" . $SQL_FromDate . "' AND date(trandate) <= '" . $SQL_ToDate . "'";
                            if  ($_POST['location']!='All')  {
                                    $sql .= " AND rh_locations.loccode = '" . $_POST['location']."'";
                            }
                            $sql .=  " ORDER BY debtortrans.transno";                          
					//SAINTS fin


	if (!isset($_POST['ShowResultsComp']))
	{
		$sql .=  " LIMIT ".$reg1.", ".$tampag;
	}

   $ErrMsg = _('The customer transactions for the selected criteria could not be retrieved because') . ' - ' . DB_error_msg($db);
   $DbgMsg =  _('The SQL that failed was');
   $TransResult = DB_query($sql, $db,$ErrMsg,$DbgMsg);

   //iJPe realhost	2010-01-14
   //Se agrego la opcion Vista Impresion (La cual es sin paginacion)
   if (!isset($_POST['ShowResultsComp']))
	{
	   // bowikaxu realhost - june 2008 - view pages as links not as selectbox
	   echo "<FORM METHOD=POST ACTION=CustomerTransInquiry.php><CENTER>"._('Page').": ";
		if($pag>1){
			echo "<A HREF='CustomerTransInquiry.php?".SID."page=".($pag-1)."&FromDate=".$_POST['FromDate']."&ToDate=".$_POST['ToDate']."&TransType=".$_POST['TransType']."&location=".$_POST['location']."'> << </A> | ";
	   }
	   for($i=1;$i<=ceil($num_res['total']/$tampag) AND $i<=7;$i++){
			if($pag == $i){
				echo $i." | ";
			}else {
				echo "<A HREF='CustomerTransInquiry.php?".SID."page=".$i."&FromDate=".$_POST['FromDate']."&ToDate=".$_POST['ToDate']."&TransType=".$_POST['TransType']."&location=".$_POST['location']."'>".$i."</A> | ";
			}
	   }
	   if($pag<ceil($num_res['total']/$tampag)){
			echo "<A HREF='CustomerTransInquiry.php?".SID."page=".($pag+1)."&FromDate=".$_POST['FromDate']."&ToDate=".$_POST['ToDate']."&TransType=".$_POST['TransType']."&location=".$_POST['location']."'> >> </A>";
	   }

	   echo "</CENTER>
	   <INPUT TYPE=HIDDEN NAME='TransType' VALUE='".$_POST['TransType']."'>
	   <INPUT TYPE=HIDDEN NAME='location' VALUE='".$_POST['location']."'>
	   <INPUT TYPE=HIDDEN NAME='FromDate' VALUE='".$_POST['FromDate']."'>
	   <INPUT TYPE=HIDDEN NAME='ToDate' VALUE='".$_POST['ToDate']."'>
	   <INPUT TYPE=HIDDEN NAME='ShowResults' VALUE='".$_POST['ShowResults']."'>
	   </FORM><BR>";
	}

        echo "<CENTER>";
   echo '<TABLE CELLPADDING=3 BORDER=2>';

   // bowikaxu realhost sept 07


   $tableheader = "<TR>
			<TD class='tableheader'>" . _('# Factura') . "</TD>
			<TD class='tableheader'>" . _('Fecha Factura') . "</TD>
			<TD class='tableheader'>" . _('Fecha Vencimiento') . "</TD>
			<TD class='tableheader'>" . _('Subtotal') . "</TD>
                        <TD class='tableheader'>" . _('IVA') . "</TD>
			<TD class='tableheader'>" . _('Total') . "</TD>
			";


	echo $tableheader;

	$RowCounter = 1;
	$k = 0; //row colour counter
	
	/*
	 * rleal
	 * Jun 27 2011
	 * se agregan variables para sumatorias
	 */
	
	$rh_subtotal=0;
	$rh_iva=0;
	$rh_total=0;

	while ($myrow=DB_fetch_array($TransResult)) {

	if($myrow['type']==10){
		// bowikaxu april 2007 - get external invoice number
			$sql = "SELECT rh_invoicesreference.extinvoice, rh_locations.rh_serie FROM rh_invoicesreference, rh_locations
			WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
			AND rh_locations.loccode = rh_invoicesreference.loccode";
		    $res = DB_query($sql,$db);
		    $ExtInvoice = DB_fetch_array($res);
	}

	//iJPe	notas de cargo
	if($myrow['type']==20001){
		// bowikaxu april 2007 - get external invoice number
			$sql = "SELECT rh_invoicesreference.extinvoice, rh_locations.rh_serie FROM rh_invoicesreference, rh_locations
			WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
			AND rh_locations.loccode = rh_invoicesreference.loccode";
		    $res = DB_query($sql,$db);
		    $ExtInvoice = DB_fetch_array($res);
	}

	$print = 1;
	//rleal feb 16,2010 se agrego la nota de credito 11
	if(($myrow['type']!=12) AND ($myrow['type']!=11)){
		$sql = "SELECT debtortrans.order_ FROM debtortrans
				INNER JOIN salesorders ON salesorders.orderno = debtortrans.order_
				INNER JOIN rh_locations ON rh_locations.loccode = salesorders.fromstkloc_virtual
				WHERE debtortrans.id = ".$myrow['id']."";
		if  ($_POST['location']!='All')  {
			$sql .= " AND rh_locations.loccode = '" . $_POST['location']."'";
		}
				$res = DB_query($sql,$db);
				if(DB_num_rows($res)>0){
					$orderarray = DB_fetch_array($res);
					$order = $orderarray['order_'];
				}else {
					$print = 0;
				}
	}else {
		$order = '-';
	}
		// bowikaxu realhost - june 30 2007 - change color on cancelled transactions
	if (($myrow['type']==10) && $myrow['rh_status']=='C'){

		echo "<tr bgcolor='#ea6060'>";
                $myrow['totalamt'] = 0;
                $myrow['alloc'] = 0;

	
	}else {

		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k=1;
		}

	}

	
		$format_base = "<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
                                <td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>";
	

		

		    $sql = "SELECT custallocns.transid_allocfrom, debtortrans.*, systypes.typename FROM custallocns
		    	INNER JOIN debtortrans ON debtortrans.id = custallocns.transid_allocfrom
		    	INNER JOIN systypes ON systypes.typeid = debtortrans.type
		    	WHERE transid_allocto = ".$myrow['id']."";
		    $res2 = DB_query($sql,$db);
		    $Banks = '';
		    $Alloc = '';
		    while ($trans = DB_fetch_array($res2)){ //(SELECT debtortrans.type FROM debtortrans WHERE id = ".$trans['transid_allocfrom'].")
		    	$sql = "SELECT bankaccounts.bankaccountname, banktrans.transdate FROM bankaccounts,banktrans
		    		WHERE bankaccounts.accountcode = banktrans.bankact
		    		AND banktrans.transno = ".$trans['transno']."
		    		AND banktrans.type = ".$trans['type']."";

		    	$res3 = DB_query($sql,$db);
		    	$Bank = DB_fetch_array($res3);
		    	if(DB_num_rows($res3)>=1){
		    		$Banks = $Bank['bankaccountname'].' '.$Bank['transdate'].'<BR>'.$Banks;
		   		}else {
		   			$Alloc = $trans['typename'].' '.$trans['transno']."<BR>".$Alloc;
		   		}
		}

		    //"SELECT bankaccounts.bankaccountname FROM bankaccounts WHERE bankaccounts.accountcode =
//		    		(SELECT bankact FROM banktrans WHERE type = (SELECT debtortrans.type FROM debtortrans WHERE id =
//					(SELECT transid_allocfrom FROM custallocns WHERE transid_allocto = ".$myrow['id'].")) AND transno =
//					(SELECT debtortrans.transno FROM debtortrans WHERE id = (SELECT transid_allocfrom FROM custallocns WHERE transid_allocto = ".$myrow['id'].")))";
                

                        if ($myrow['paymentterms'] == 'CA'){
                            $fechaVen = 'CONTADO';
                        }else{
                            $fechaVen = $myrow['fechaVen'];
                            $fechaVen = ConvertSQLDate($fechaVen);
                        }

		//SAINTS series y folios de FE 28/01/2011
		if($myrow['folio']!="")
			{printf($format_base,				
				//$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
				$myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
				ConvertSQLDate($myrow['trandate']),
				$fechaVen,
                                number_format($myrow['ovamount'],2),
				number_format($myrow['ovgst'],2),
				number_format($myrow['totalamt'],2)
				);}
				
		else{printf($format_base,				
				$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
				ConvertSQLDate($myrow['trandate']),
				$fechaVen,
                                number_format($myrow['ovamount'],2),
				number_format($myrow['ovgst'],2),
				number_format($myrow['totalamt'],2)
				);}
		//SAINTS fin
		
		$rh_subtotal+=$myrow['ovamount'];
		$rh_iva+=$myrow['ovgst'];
		$rh_total+=$myrow['totalamt'];
		 
	//end of page full new headings if
	}
	//end of while loop

	echo "<TR><TD></TD><TD></TD><TD></TD><TD align='right'>".number_format($rh_subtotal,2)."</TD><TD align='right'>".number_format($rh_iva,2)."</TD><TD align='right'>".number_format($rh_total,2)."</TD><TR>";
 echo '</TABLE>';
 echo "</CENTER>";
}



include('includes/footer.inc');

?>
