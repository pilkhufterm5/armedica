<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
/* RELATIONSHIP BETWEEN EXTERNAL AND INTERNAL CREDIT NOTES

Realhost	
Bowikaxu
sept 2007

*/

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Relacion de Notas de Credito Internas y Externas');
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

echo '<TR><TD>' ._('Credit Note').' ' ._('Desde') . ":</TD><TD><INPUT TYPE=TEXT NAME='FromInv' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['FromInv'] . '></TD>';
echo '<TD>' ._('Credit Note') .' '. _('Hasta') . ":</TD><TD><INPUT TYPE=TEXT NAME='ToInv' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['ToInv'] . '></TD></TR>';

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

echo '</SELECT></TD></TR>';


echo "</TR></TABLE><INPUT TYPE=SUBMIT NAME='ShowResults' VALUE='" . _('Ver Notas de Credito') . "'>";
echo '<HR>';

echo '</FORM></CENTER>';

if (isset($_POST['ShowResults'])){
   $SQL_FromDate = FormatDateForSQL($_POST['FromDate']);
   $SQL_ToDate = FormatDateForSQL($_POST['ToDate']);
   
   $sql = "SELECT transno,
   		trandate,
   		id,
		debtortrans.debtorno,
		branchcode,
		reference,
		invtext,
		order_,
		rate,
		ovamount,ovgst,
		ovamount+ovgst+ovfreight+ovdiscount as totalamt
	FROM debtortrans
	WHERE ";

   $sql = $sql . "trandate >='" . $SQL_FromDate . "' AND trandate <= '" . $SQL_ToDate . "' AND type = 11";
   $sql .= " AND id IN (SELECT ref FROM rh_crednotesreference WHERE loccode ='".$_POST['StockLocation']."'";
   
   if(isset($_POST['FromInv']) && $_POST['FromInv']>0){
   	$sql .= " AND extcn >= ".$_POST['FromInv']." AND extcn <= ".$_POST['ToInv']."";
   }
   
   $sql .=  ") ORDER BY id";
   
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
		$SQL = "SELECT rh_crednotesreference.extcn,rh_crednotesreference.loccode FROM rh_crednotesreference WHERE rh_crednotesreference.ref = ".$myrow['id']." AND rh_crednotesreference.loccode = '".$_POST['StockLocation']."'";
		
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

			printf("$format_base
				<td><a target='_blank' href='%s/rh_PrintCustTrans.php?%&FromTransNo=%s&InvOrCredit=Credit'><IMG SRC='%s' TITLE='" . _('Click to preview the credit note') . "'></a></td>
				</tr>",
				$myrow['transno'],
				$external['extcn'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['debtorno'],
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
				$rootpath.'/css/'.$theme.'/images/preview.gif');

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
