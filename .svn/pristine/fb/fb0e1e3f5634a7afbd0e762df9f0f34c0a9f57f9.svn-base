<?php

/* $Revision: 273 $ */

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Consulta Depositos para Facturas de Credito/Contado');
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
if(isset($_GET['TransType'])){
	$_POST['TransType'] = $_GET['TransType'];
	$_POST['ShowResults'] = 1;
}
if(isset($_GET['location'])){
	$_POST['location'] = $_GET['location'];
	$_POST['ShowResults'] = 1;
}
if(isset($_GET['type'])){ // ver las transacciones

	$_POST['ShowResults'] = 1;
	$_POST['FromDate'] = $_GET['FromDate'];
	$_POST['ToDate'] = $_GET['ToDate'];
	$_POST['location'] = $_GET['Location'];
	$_POST['TransType'] = $_GET['type'];

}

echo "<FORM NAME='menu' ACTION='" . $_SERVER['PHP_SELF'] . "' METHOD=POST>";

echo '<CENTER><TABLE CELLPADDING=2><TR>';

echo '<TD>' . _('Type') . ":</TD><TD><SELECT name='TransType'> ";
// bowikaxu - March 2007 - Se agrego el tipo 20000 de remisiones a las busquedas
//$sql = 'SELECT typeid, typename FROM systypes WHERE typeid >= 10 AND typeid <= 14 OR typeid=20000 OR typeid=20001';
//$resultTypes = DB_query($sql,$db);

if ($_POST['TransType'] == 'credito'){
    echo "<OPTION Value='credito' selected>"._('Deposito Credito');
    echo "<OPTION Value='contado'>"._('Deposito Contado');
}else{
    echo "<OPTION Value='credito'>"._('Deposito Credito');
    echo "<OPTION Value='contado' selected>"._('Deposito Contado');
}

//while ($myrow=DB_fetch_array($resultTypes)){
//	if (isset($_POST['TransType'])){
//		if ($myrow['typeid'] == $_POST['TransType']){
//		     echo "<OPTION SELECTED Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
//		} else {
//		     echo "<OPTION Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
//		}
//	} else {
//		     echo "<OPTION Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
//	}
//}
echo '</SELECT></TD>';

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
//echo "</TR></TABLE><INPUT TYPE=SUBMIT NAME='ShowResults2' VALUE='" . _('Show Transactions') . "'>";
echo "</TR></TABLE><INPUT TYPE=SUBMIT NAME='ShowResultsComp' VALUE='" . _('Vista Impresi&oacute;n') . "'>";
echo '<HR>';

echo '</FORM></CENTER>';

echo '<CENTER>';

if ((isset($_POST['ShowResults']) && $_POST['TransType'] != '') OR ((isset($_POST['ShowResults2']) OR isset($_POST['ShowResultsComp'])) && $_POST['TransType']!='All')){
       $SQL_FromDate = FormatDateForSQL($_POST['FromDate']);
       $SQL_ToDate = FormatDateForSQL($_POST['ToDate']);

        $loccode = '';

        if ($_POST['location'] != 'All'){
            $loccode = " WHERE loccode = '".$_POST['location']."'";
        }


       switch($_POST['TransType']){

//            $sql = "SELECT
//                                    debtortrans.transno,
//                                    debtortrans.type,
//                                    debtortrans.id,
//                                    debtortrans.trandate,
//                                    debtortrans.debtorno,
//                                    debtortrans.rh_status,
//                                    debtorsmaster.name,
//                                    debtortrans.branchcode,
//                                    debtortrans.reference,
//                                    debtortrans.invtext,
//                                    debtortrans.order_,
//                                    debtortrans.rate,
//                                    debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount as totalamt,
//                                    debtortrans.alloc,
//                                    debtorsmaster.currcode,
//                                    systypes.typename
//                        FROM debtortrans
//                            INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
//                            INNER JOIN systypes ON debtortrans.type = systypes.typeid
//                            WHERE
//                                    debtortrans.type = 12";
//                            $sql = $sql . " AND trandate >='" . $SQL_FromDate . "' AND trandate <= '" . $SQL_ToDate . "'";
//                            $sql .=  " ORDER BY debtortrans.trandate";
//                            break;


                    case 'credito':
					//SAINTS
                      $sql = 'SELECT debtortrans.transno, debtortrans.type, debtortrans.id, debtortrans.trandate, debtortrans.debtorno,
							c.serie,c.folio,
                            debtortrans.rh_status, debtorsmaster.name, debtortrans.branchcode, debtortrans.reference, debtortrans.invtext,
                            debtortrans.order_, debtortrans.rate, debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount AS totalamt,
                            debtortrans.alloc, debtorsmaster.currcode, (SELECT typename FROM systypes WHERE typeid = 12) AS typename
                            FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id
                              INNER JOIN custallocns ON debtortrans.id = custallocns.transid_allocfrom INNER JOIN debtorsmaster
                            ON debtortrans.debtorno = debtorsmaster.debtorno WHERE type = 12 AND transid_allocto IN (SELECT * FROM(
                            (SELECT debtortrans.id
                            FROM debtortrans INNER JOIN rh_invoicesreference ON debtortrans.transno = rh_invoicesreference.intinvoice
                            WHERE rh_invoicesreference.loccode IN (SELECT loccode FROM locations'. $loccode.') AND debtortrans.type = 10)
                            UNION (SELECT debtortrans.id
                            FROM debtortrans INNER JOIN salesorders ON debtortrans.order_ = salesorders.orderno
                            WHERE salesorders.fromstkloc_virtual IN (SELECT loccode FROM locations'. $loccode.') AND debtortrans.type = 20001))a)';

                   $sql="SELECT debtortrans.transno, debtortrans.type, debtortrans.id, debtortrans.trandate, debtortrans.debtorno,
                            debtortrans.rh_status, debtorsmaster.name, debtortrans.branchcode, debtortrans.reference, debtortrans.invtext,
                            debtortrans.order_, debtortrans.rate, debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount AS totalamt,
                            debtortrans.alloc, debtorsmaster.currcode, (SELECT typename FROM systypes WHERE typeid = 12) AS typename
                            FROM debtortrans INNER JOIN custallocns ON debtortrans.id = custallocns.transid_allocfrom INNER JOIN debtorsmaster
                            ON debtortrans.debtorno = debtorsmaster.debtorno WHERE type = 12 AND transid_allocto IN (SELECT * FROM(
                            (SELECT debtortrans.id
                            FROM debtortrans INNER JOIN rh_invoicesreference ON debtortrans.transno = rh_invoicesreference.intinvoice
                            WHERE rh_invoicesreference.loccode IN (SELECT loccode FROM locations ". $loccode." ) AND debtortrans.type = 10)
                            UNION (SELECT debtortrans.id
                            FROM debtortrans INNER JOIN salesorders ON debtortrans.order_ = salesorders.orderno
                            WHERE salesorders.fromstkloc_virtual IN (SELECT loccode FROM locations ". $loccode." ) AND debtortrans.type = 20001)
			    UNION (Select debtortrans.id from debtortrans inner join rh_cfd__cfd on (debtortrans.transno = rh_cfd__cfd.fk_transno AND debtortrans.type = 10)inner join rh_cfd__locations__systypes__ws_csd on rh_cfd__locations__systypes__ws_csd.serie = rh_cfd__cfd.serie and rh_cfd__locations__systypes__ws_csd.id_locations='". $_POST['location']."' ))a)";


                    /*
                     * iJPe
                     * Query utilizado para obtener facturas, notas de credito y notas de cargo asignadas a depositos
                     */
//                    $sql = 'SELECT debtortrans.transno, debtortrans.type, debtortrans.id, debtortrans.trandate, debtortrans.debtorno,
//                                debtortrans.rh_status, debtorsmaster.name, debtortrans.branchcode, debtortrans.reference, debtortrans.invtext,
//                                debtortrans.order_, debtortrans.rate, debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount AS totalamt,
//                                debtortrans.alloc, debtorsmaster.currcode, (SELECT typename FROM systypes WHERE typeid = 12) AS typename
//                                FROM debtortrans INNER JOIN custallocns ON debtortrans.id = custallocns.transid_allocfrom INNER JOIN debtorsmaster
//                                ON debtortrans.debtorno = debtorsmaster.debtorno WHERE type = 12 AND transid_allocto IN (SELECT * FROM(
//                                (SELECT debtortrans.id
//                                FROM debtortrans INNER JOIN rh_invoicesreference ON debtortrans.transno = rh_invoicesreference.intinvoice
//                                WHERE rh_invoicesreference.loccode IN (SELECT loccode FROM locations'. $loccode.'))
//                                UNION (SELECT debtortrans.id
//                                FROM debtortrans INNER JOIN rh_crednotesreference ON debtortrans.transno = rh_crednotesreference.intcn
//                                WHERE rh_crednotesreference.loccode IN (SELECT loccode FROM locations'. $loccode.'))
//                                UNION (SELECT debtortrans.id
//                                FROM debtortrans INNER JOIN salesorders ON debtortrans.order_ = salesorders.orderno
//                                WHERE salesorders.fromstkloc_virtual IN (SELECT loccode FROM locations'. $loccode.') AND debtortrans.type = 20001))a)';


//                        $sql = 'SELECT debtortrans.transno,
//                                debtortrans.type,
//                                debtortrans.id,
//                                debtortrans.trandate,
//                                debtortrans.debtorno,
//                                debtortrans.rh_status,
//                                debtorsmaster.name,
//                                debtortrans.branchcode,
//                                debtortrans.reference,
//                                debtortrans.invtext,
//                                debtortrans.order_,
//                                debtortrans.rate,
//                                debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount as totalamt,
//                                debtortrans.alloc,
//                                debtorsmaster.currcode,
//                                (SELECT typename FROM systypes WHERE typeid = 12) as typename
//                                FROM debtortrans INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
//                                INNER JOIN (SELECT transid_allocto
//                                FROM debtortrans INNER JOIN custallocns ON debtortrans.id = custallocns.transid_allocfrom)a ON debtortrans.id = a.transid_allocto
//                                AND type=10 INNER JOIN rh_invoicesreference ON debtortrans.transno = rh_invoicesreference.intinvoice
//                                WHERE loccode IN (SELECT loccode FROM locations'. $loccode.')';
//
                                  $sql = $sql . " AND trandate >='" . $SQL_FromDate . "' AND trandate <= '" . $SQL_ToDate . "'";
                                  $sql .=  " GROUP BY debtortrans.id ORDER BY debtortrans.trandate,debtortrans.transno";

                                break;

                    case 'contado':
					//SAINTS
                        $sql = 'SELECT debtortrans.transno, debtortrans.type, debtortrans.id, debtortrans.trandate, debtortrans.debtorno,
							c.serie,c.folio,
                            debtortrans.rh_status, debtorsmaster.name, debtortrans.branchcode, debtortrans.reference, debtortrans.invtext,
                            debtortrans.order_, debtortrans.rate, debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount AS totalamt,
                            debtortrans.alloc, debtorsmaster.currcode, (SELECT typename FROM systypes WHERE typeid = 12) AS typename
                            FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id
                              INNER JOIN custallocns ON debtortrans.id = custallocns.transid_allocfrom INNER JOIN debtorsmaster
                            ON debtortrans.debtorno = debtorsmaster.debtorno WHERE type = 12 AND transid_allocto IN (SELECT * FROM(
                            (SELECT debtortrans.id
                            FROM debtortrans INNER JOIN rh_invoicesreference ON debtortrans.transno = rh_invoicesreference.intinvoice
                            WHERE rh_invoicesreference.loccode IN (SELECT loccode FROM rh_locations_virtual'. $loccode.') AND debtortrans.type = 10)
                            UNION (SELECT debtortrans.id
                            FROM debtortrans INNER JOIN salesorders ON debtortrans.order_ = salesorders.orderno
                            WHERE salesorders.fromstkloc_virtual IN (SELECT loccode FROM rh_locations_virtual'. $loccode.') AND debtortrans.type = 20001))a)';


//                        $sql = 'SELECT debtortrans.transno, debtortrans.type, debtortrans.id, debtortrans.trandate, debtortrans.debtorno,
//                                debtortrans.rh_status, debtorsmaster.name, debtortrans.branchcode, debtortrans.reference, debtortrans.invtext,
//                                debtortrans.order_, debtortrans.rate, debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount AS totalamt,
//                                debtortrans.alloc, debtorsmaster.currcode, (SELECT typename FROM systypes WHERE typeid = 12) AS typename
//                                FROM debtortrans INNER JOIN custallocns ON debtortrans.id = custallocns.transid_allocfrom INNER JOIN debtorsmaster
//                                ON debtortrans.debtorno = debtorsmaster.debtorno WHERE type = 12 AND transid_allocto IN (SELECT debtortrans.id
//                                FROM debtortrans INNER JOIN rh_invoicesreference ON debtortrans.transno = rh_invoicesreference.intinvoice
//                                WHERE rh_invoicesreference.loccode IN (SELECT loccode FROM rh_locations_virtual'. $loccode.'))';


//                        $sql = 'SELECT debtortrans.transno,
//                                debtortrans.type,
//                                debtortrans.id,
//                                debtortrans.trandate,
//                                debtortrans.debtorno,
//                                debtortrans.rh_status,
//                                debtorsmaster.name,
//                                debtortrans.branchcode,
//                                debtortrans.reference,
//                                debtortrans.invtext,
//                                debtortrans.order_,
//                                debtortrans.rate,
//                                debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount as totalamt,
//                                debtortrans.alloc,
//                                debtorsmaster.currcode,
//                                (SELECT typename FROM systypes WHERE typeid = 12) as typename
//                                FROM debtortrans INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
//                                INNER JOIN (SELECT transid_allocto
//                                FROM debtortrans INNER JOIN custallocns ON debtortrans.id = custallocns.transid_allocfrom)a ON debtortrans.id = a.transid_allocto
//                                AND type=10 INNER JOIN rh_invoicesreference ON debtortrans.transno = rh_invoicesreference.intinvoice
//                                WHERE loccode IN (SELECT loccode FROM rh_locations_virtual'.$loccode.')';

                                $sql = $sql . " AND trandate >='" . $SQL_FromDate . "' AND trandate <= '" . $SQL_ToDate . "'";
                                $sql .=  " GROUP BY debtortrans.id ORDER BY debtortrans.trandate";

                                break;
       }

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

   echo '<TABLE CELLPADDING=3 BORDER=2>';

   // bowikaxu realhost sept 07
   if($_POST['TransType']=='credito' || $_POST['TransType']=='contado'){
   	$tableheader = "<TR>
			<TD class='tableheader'>" . _('Type') . "</TD>
			<TD class='tableheader'>" . _('Number') . "</TD>
			<TD class='tableheader'>" . _('Invoice') . "</TD>
			<TD class='tableheader'>" . _('Date') . "</TD>
			<TD class='tableheader'>" . _('Customer') . "</TD>
			"//<TD class='tableheader'>" . _('Branch') . "</TD>
			."<TD class='tableheader'>" . _('Reference') . "</TD>
			<TD class='tableheader'>" . _('Comments') . "</TD>
			"//<TD class='tableheader'>" . _('Order') . "</TD>
			."<TD class='tableheader'>" . _('Ex Rate') . "</TD>
			<TD class='tableheader'>" . _('Amount') . "</TD>
			<TD class='tableheader'>" . _('Allocated') . "</TD>
			<TD class='tableheader'>" . _('Bank') . "</TD>
			<TD class='tableheader'>" . _('Currency') . '</TD></TR>';
   }else {
   	$tableheader = "<TR>
			<TD class='tableheader'>" . _('Type') . "</TD>
			<TD class='tableheader'>" . _('Number') . "</TD>
			<TD class='tableheader'>" . _('Date') . "</TD>
			<TD class='tableheader'>" . _('Customer') . "</TD>
			<TD class='tableheader'>" . _('Branch') . "</TD>
			<TD class='tableheader'>" . _('Reference') . "</TD>
			<TD class='tableheader'>" . _('Comments') . "</TD>
			<TD class='tableheader'>" . _('Order') . "</TD>
			<TD class='tableheader'>" . _('Ex Rate') . "</TD>
			<TD class='tableheader'>" . _('Amount') . "</TD>
			<TD class='tableheader'>" . _('Allocated') . "</TD>
			<TD class='tableheader'>" . _('Bank') . "</TD>
			<TD class='tableheader'>" . _('Currency') . '</TD></TR>';
   }
	echo $tableheader;

	$RowCounter = 1;
	$k = 0; //row colour counter

        if (DB_num_rows($TransResult) <= 0){
            prnMsg("Ningun resultado encontrado", "info");
        }


	while ($myrow=DB_fetch_array($TransResult)) {

            if ($_POST['TransType']=='credito' || $_POST['TransType']=='contado')
            {
                    $format_base = "<td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td width='20'>%s</td>
                                    <td ALIGN=RIGHT>%s</td>
                                    <td ALIGN=RIGHT>%s</td>
                                    <td ALIGN=RIGHT>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>";
            }else
            {
                    $format_base = "<td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td width='200'>%s</td>
                                    <td>%s</td>
                                    <td ALIGN=RIGHT>%s</td>
                                    <td ALIGN=RIGHT>%s</td>
                                    <td ALIGN=RIGHT>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>";
            }


                            if ($_POST['TransType']=='credito' || $_POST['TransType']=='contado'){ /* receipts */

                                $sql = "SELECT id FROM debtortrans WHERE transno = '".$myrow['transno']."' AND type = 12";
                                //$recpt_id = DB_query($sql,$db);

                                $sql = "SELECT transid_allocto FROM custallocns WHERE transid_allocfrom = ".$myrow['id']."";
                                $res2 = DB_query($sql,$db);
                                $Banks = '';
                                $fact = _('Invoice').' ';
                                while ($trans = DB_fetch_array($res2)){ //(SELECT debtortrans.type FROM debtortrans WHERE id = ".$trans['transid_allocfrom'].")
                                    $sql = "SELECT bankaccounts.bankaccountname, banktrans.transdate
                                                    FROM bankaccounts,banktrans
                                                    WHERE bankaccounts.accountcode = banktrans.bankact
                                                    AND banktrans.type = 12
                                                    AND banktrans.transno = (SELECT debtortrans.transno FROM debtortrans WHERE type = 12 AND id = ".$myrow['id'].")";
                                    $res3 = DB_query($sql,$db);
                                    $Bank = DB_fetch_array($res3);

                                    $sql = "SELECT transno FROM debtortrans WHERE id = ".$trans['transid_allocto']."";
                                    $res5 = DB_query($sql,$db);
                                    $trans_to = DB_fetch_array($res5);

                                    // bowikaxu sept 2007 - get external invoice number
                                    $sql = "SELECT rh_invoicesreference.extinvoice, rh_locations.rh_serie FROM rh_invoicesreference, rh_locations
                                            WHERE rh_invoicesreference.intinvoice = ".$trans_to['transno']." AND rh_locations.loccode = rh_invoicesreference.loccode";
                                    $res = DB_query($sql,$db);
                                    $ExtInvoice = DB_fetch_array($res);
                                    //SAINTS
                                    $sql_fe="SELECT serie, folio FROM rh_cfd__cfd WHERE fk_transno='".$trans_to['transno']."'";
                                    $res_fe=DB_query($sql_fe,$db);
                                    $res_fe=DB_fetch_array($res_fe);

                                  //SAINTS
                                  if($res_fe['serie']!="")
                                    $fact .= $res_fe['serie'].$res_fe['folio'].', ';
                                  else
                                    $fact .= $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].', ';

                                    if(DB_num_rows($res3)>=1)
                                            $Banks = $Bank['bankaccountname'].' '.$trans_to['transno'].'<BR>'.$Banks;
                            }
									//SAINTS
                                    printf("<TD>%s</TD>$format_base</tr>",
                                    $myrow['typename'],
                                    $myrow['transno'],
                                    $fact,
                                    ConvertSQLDate($myrow['trandate']),
                                    $myrow['name'].' ['.$myrow['debtorno'].']',
                                    //$myrow['branchcode'],
                                    $myrow['reference'],
                                    $myrow['invtext'],
                                    //$myrow['order_'],
                                    $myrow['rate'],
                                    number_format($myrow['totalamt'],2),
                                    number_format($myrow['alloc'],2),
                                    $Banks,
                                    $myrow['currcode']);

                    } else {  /* otherwise */

                            if($myrow['type']==10 && $print){

								//SAINTS series y folios de FE 27/01/2011
								if($myrow['folio']){printf("$format_base</tr>",
                                    $myrow['typename'],
                                    //$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                                    $myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
                                    ConvertSQLDate($myrow['trandate']),
                                    $myrow['name'].' ['.$myrow['debtorno'].']',
                                    $myrow['branchcode'],
                                    $myrow['reference'],
                                    $myrow['invtext'],
                                    $myrow['order_'],
                                    $myrow['rate'],
                                    number_format($myrow['totalamt'],2),
                                    number_format($myrow['alloc'],2),
                                    $Banks,
                                    $myrow['currcode']);}

                                else{printf("$format_base</tr>",
                                    $myrow['typename'],
                                    $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                                    ConvertSQLDate($myrow['trandate']),
                                    $myrow['name'].' ['.$myrow['debtorno'].']',
                                    $myrow['branchcode'],
                                    $myrow['reference'],
                                    $myrow['invtext'],
                                    $myrow['order_'],
                                    $myrow['rate'],
                                    number_format($myrow['totalamt'],2),
                                    number_format($myrow['alloc'],2),
                                    $Banks,
                                    $myrow['currcode']);}
                                //SAINTS fin

                            }else{

                                    if($myrow['type']==10 && $print){

                                        //SAINTS series y folios de FE 27/01/2011
										if($myrow['folio']){
                                            printf("$format_base</tr>",
                                            $myrow['typename'],
                                            //$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                                            $myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
                                            ConvertSQLDate($myrow['trandate']),
                                            $myrow['name'].' ['.$myrow['debtorno'].']',
                                            $myrow['branchcode'],
                                            $myrow['reference'],
                                            $myrow['invtext'],
                                            $myrow['order_'],
                                            $myrow['rate'],
                                            number_format($myrow['totalamt'],2),
                                            number_format($myrow['alloc'],2),
                                            $Banks,
                                            $myrow['currcode']);}

                                          else{printf("$format_base</tr>",
                                            $myrow['typename'],
                                            $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                                            ConvertSQLDate($myrow['trandate']),
                                            $myrow['name'].' ['.$myrow['debtorno'].']',
                                            $myrow['branchcode'],
                                            $myrow['reference'],
                                            $myrow['invtext'],
                                            $myrow['order_'],
                                            $myrow['rate'],
                                            number_format($myrow['totalamt'],2),
                                            number_format($myrow['alloc'],2),
                                            $Banks,
                                            $myrow['currcode']);}
                                         //SAINTS fin

                                    }else if($print) {

                                    printf("$format_base</tr>",
                                            $myrow['typename'],
                                            $myrow['transno'],
                                            ConvertSQLDate($myrow['trandate']),
                                            $myrow['name'].' ['.$myrow['debtorno'].']',
                                            $myrow['branchcode'],
                                            $myrow['reference'],
                                            $myrow['invtext'],
                                            $myrow['order_'],
                                            $myrow['rate'],
                                            number_format($myrow['totalamt'],2),
                                            number_format($myrow['alloc'],2),
                                            '',
                                            $myrow['currcode']);
                                    }

                            }

                    }
            //end of page full new headings if
	}
	//end of while loop

 echo '</TABLE>';
}

// Motrar resumen por transaccion
if ((isset($_POST['ShowResults2']) || isset($_POST['ShowResultsComp']) && $_POST['TransType'] != '') || isset($_GET['page']) || isset($_GET['ShowResults'])){


    if (isset($_GET['TransType']))
    {
        $_POST['TransType'] = $_GET['TransType'];

        $_POST['FromDate'] = $_GET['FromDate'];
        $_POST['ToDate'] = $_GET['ToDate'];
    }

     $SQL_FromDate = FormatDateForSQL($_POST['FromDate']);
     $SQL_ToDate = FormatDateForSQL($_POST['ToDate']);


        if ($_POST['TransType']=='credito' || $_POST['TransType']=='contado')
        {

            switch($_POST['TransType']){

                    case 'credito':
                        $sql = 'SELECT transno,
		   		type,
				SUM(amnt) as amnt,
				SUM(alloc) AS alloc,
				currcode, (SELECT typename FROM systypes WHERE typeid = 12) AS typename
                                FROM
                                (SELECT debtortrans.transno,
		   		debtortrans.type,
				(debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount) as amnt,
				(debtortrans.alloc) AS alloc,
				debtorsmaster.currcode, (SELECT typename FROM systypes WHERE typeid = 12) AS typename
                                FROM debtortrans INNER JOIN custallocns ON debtortrans.id = custallocns.transid_allocfrom INNER JOIN debtorsmaster
                                ON debtortrans.debtorno = debtorsmaster.debtorno WHERE type = 12 AND transid_allocto IN (SELECT * FROM(
                                (SELECT debtortrans.id
                                FROM debtortrans INNER JOIN rh_invoicesreference ON debtortrans.transno = rh_invoicesreference.intinvoice
                                WHERE rh_invoicesreference.loccode IN (SELECT loccode FROM locations'. $loccode.') AND debtortrans.type = 10)
                                UNION (SELECT debtortrans.id
                                FROM debtortrans INNER JOIN salesorders ON debtortrans.order_ = salesorders.orderno
                                WHERE salesorders.fromstkloc_virtual IN (SELECT loccode FROM locations'. $loccode.') AND debtortrans.type = 20001))a)';

                                $sql = $sql . " AND trandate >='" . $SQL_FromDate . "' AND trandate <= '" . $SQL_ToDate . "'";

                                $sql .= ' GROUP BY debtortrans.transno) listDep GROUP BY listDep.type';

                                break;

                    case 'contado':

                        $sql = 'SELECT transno,
		   		type,
				SUM(amnt) as amnt,
				SUM(alloc) AS alloc,
				currcode, (SELECT typename FROM systypes WHERE typeid = 12) AS typename
                                FROM
                                (SELECT debtortrans.transno,
		   		debtortrans.type,
				(debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount) as amnt,
				(debtortrans.alloc) AS alloc,
				debtorsmaster.currcode, (SELECT typename FROM systypes WHERE typeid = 12) AS typename
                                FROM debtortrans INNER JOIN custallocns ON debtortrans.id = custallocns.transid_allocfrom INNER JOIN debtorsmaster
                                ON debtortrans.debtorno = debtorsmaster.debtorno WHERE type = 12 AND transid_allocto IN (SELECT * FROM(
                                (SELECT debtortrans.id
                                FROM debtortrans INNER JOIN rh_invoicesreference ON debtortrans.transno = rh_invoicesreference.intinvoice
                                WHERE rh_invoicesreference.loccode IN (SELECT loccode FROM rh_locations_virtual'. $loccode.') AND debtortrans.type = 10)
                                UNION (SELECT debtortrans.id
                                FROM debtortrans INNER JOIN salesorders ON debtortrans.order_ = salesorders.orderno
                                WHERE salesorders.fromstkloc_virtual IN (SELECT loccode FROM rh_locations_virtual'. $loccode.') AND debtortrans.type = 20001))a)';

                                $sql = $sql . " AND trandate >='" . $SQL_FromDate . "' AND trandate <= '" . $SQL_ToDate . "'";

                                $sql .= ' GROUP BY debtortrans.transno) listDep GROUP BY listDep.type';

                                break;

            }

//            $sql = "SELECT
//				debtortrans.transno,
//		   		debtortrans.type,
//				SUM(debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount) as amnt,
//				SUM(debtortrans.alloc) AS alloc,
//				debtorsmaster.currcode,
//				systypes.typename
//		    FROM debtortrans
//		      	INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
//		        INNER JOIN systypes ON debtortrans.type = systypes.typeid
//			WHERE
//				debtortrans.type = '".$_POST['TransType']."' ";
//			$sql = $sql . " AND trandate >='" . $SQL_FromDate . "' AND trandate <= '" . $SQL_ToDate . "'";
			//$sql .=  " GROUP BY debtortrans.type AND debtortrans.id";



            $res = DB_query($sql,$db);
        }


	// tableheader
	$tableheader = "<TR>
			<TD class='tableheader'>" . _('Type') . "</TD>
			<TD class='tableheader'>" . _('Amount') . "</TD>
			<TD class='tableheader'>" . _('Allocated') . "</TD>
			<TD></TD>
			</TR>";

        echo "<br>";
	echo "<FORM NAME='resume' METHOD='GET' ACTION='CustomerTransInquiry.php'>
			<TABLE ALIGN=CENTER>\n";
	echo $tableheader;
	$k=0;
	while($myrow = DB_fetch_array($res)){

		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k=1;
		}

		echo "<TD>".$myrow['typename']."</TD><TD>".number_format($myrow['amnt'],2)."</TD><TD>".number_format($myrow['alloc'],2)."</TD></TR>";
	}
	echo "</TABLE></FORM>";

}

include('includes/footer.inc');

?>
