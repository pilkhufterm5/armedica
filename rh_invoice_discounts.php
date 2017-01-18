<?php
/**
 * REALHOST 2008
 * $LastChangedDate$
 * $Rev$
 */

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Discount').' '._('Supplier');
include('includes/header.inc');

include('includes/Wiki.php');

$msg='';
/*
if (!isset($_POST['Search'])){
	$_POST['Search']='';
}
*/

if (!isset($_POST['PageOffset'])) {
  $_POST['PageOffset'] = 1;
} else {
  if ($_POST['PageOffset']==0) {
    $_POST['PageOffset'] = 1;
  }
}

If (isset($_POST['Select'])) { /*User has hit the button selecting a supplier */
	$_SESSION['SupplierID'] = $_POST['Select'];
	unset($_POST['Select']);
	unset($_POST['Keywords']);
	unset($_POST['SupplierCode']);
	unset($_POST['Search']);
	unset($_POST['Go']);
	unset($_POST['Next']);
	unset($_POST['Previous']);
}


if (isset($_POST['Search'])
		OR isset($_POST['Go'])
		OR isset($_POST['Next'])
		OR isset($_POST['Previous'])){

	If ( strlen($_POST['Keywords'])>0 AND strlen($_POST['SupplierCode'])>0) {
		$msg='<BR>' . _('Supplier name keywords have been used in preference to the Supplier code extract entered');
	}
	if ($_POST['Keywords']=='' AND $_POST['SupplierCode']=='') {
		$SQL = 'SELECT supplierid,
					suppname,
					currcode,
					address1,
					address2,
					address3,
					address4
				FROM suppliers
				ORDER BY suppname';
	} else {
		If (strlen($_POST['Keywords'])>0) {

			$_POST['Keywords'] = strtoupper($_POST['Keywords']);
			//insert wildcard characters in spaces

			$i=0;
			$SearchString = '%';
			while (strpos($_POST['Keywords'], ' ', $i)) {
				$wrdlen=strpos($_POST['Keywords'],' ',$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . '%';
				$i=strpos($_POST['Keywords'],' ',$i) +1;
			}
			$SearchString = $SearchString. substr($_POST['Keywords'],$i).'%';

			$SQL = "SELECT supplierid,
					suppname,
					currcode,
					address1,
					address2,
					address3,
					address4
				FROM suppliers
				WHERE suppname " . LIKE . " '$SearchString'
				ORDER BY suppname";

		} elseif (strlen($_POST['SupplierCode'])>0){
			$_POST['SupplierCode'] = strtoupper($_POST['SupplierCode']);
			$SQL = "SELECT supplierid,
					suppname,
					currcode,
					address1,
					address2,
					address3,
					address4
				FROM suppliers
				WHERE supplierid " . LIKE  . " '%" . $_POST['SupplierCode'] . "%'
				ORDER BY supplierid";
		}
	} //one of keywords or SupplierCode was more than a zero length string

	$result = DB_query($SQL,$db);
	if (DB_num_rows($result)==1){
	   $myrow = DB_fetch_row($result);
	   $SingleSupplierReturned = $myrow[0];
	}

} //end of if search

If (isset($SingleSupplierReturned)) { /*there was only one supplier returned */
	$_SESSION['SupplierID'] = $SingleSupplierReturned;
	unset($_POST['Keywords']);
	unset($_POST['SupplierCode']);
}

if (isset($_SESSION['SupplierID'])){
	
	$SupplierName = '';
	$SQL = "SELECT suppliers.suppname
		FROM suppliers
		WHERE suppliers.supplierid ='" . $_SESSION['SupplierID'] . "'";

	$SupplierNameResult = DB_query($SQL,$db);
	if (DB_num_rows($SupplierNameResult)==1){
	   $myrow = DB_fetch_row($SupplierNameResult);
	   $SupplierName = $myrow[0];
	}

	echo '<FONT SIZE=3><P>' . _('Supplier') . ' <B>' . $_SESSION['SupplierID']  . "-$SupplierName</B> " . _('is currently selected') . '.<BR>' . '<P></FONT>';

		if(!isset($_POST['filter'])){
			$_POST['filter']=0;
		}

		echo "<FORM METHOD=POST ACTON='rh_invoice_discounts.php'>";
		echo "<TABLE ALIGN=CENTER><TR><TD>"._('Filtro')."</TD>
								<TD><SELECT NAME='filter'>";
								if($_POST['filter']==1){
									echo "	<OPTION SELECTED VALUE=1>"._('Sin Descuento')."
										<OPTION VALUE=0>"._('Con Descuento')."</SELECT></TD></TR>";
								}else {
									echo "	<OPTION VALUE=1>"._('Sin Descuento')."
										<OPTION SELECTED VALUE=0>"._('Con Descuento')."</SELECT></TD></TR>";
								}
		echo "	<TR><TD COLSPAN=2><INPUT TYPE=SUBMIT NAME='OK' VALUE='OK'></TD></TR>
				</TABLE>";
		echo "<FORM>";

		// bowikaxu realhost - April 2008 - show filter results (con nota de credito o sin nota de credito)
		if($_POST['filter']==1){ // SIN DESCUENTO
			
			$sql = "SELECT supptrans.suppreference, supptrans.trandate, supptrans.transno,
							(supptrans.ovamount+supptrans.ovgst) AS total,
							supptrans.alloc, supptrans.transtext
					FROM supptrans WHERE type = 20 AND id NOT IN (SELECT supptrans_id FROM rh_suppdisc_details)";
			
			$res = DB_query($sql,$db);
			
			echo '<CENTER><TABLE WIDTH=90% COLSPAN=2 BORDER=2 CELLPADDING=4>';
			echo "<TR>
				<TD class='tableheader' WIDTH=33%>" . _('Supplier Invoice') . "</TD>
				<TD class='tableheader' WIDTH=33%>". _('Date') . "</TD>
				<TD class='tableheader' WIDTH=33%>" . _('Total') . "</TD>
				<TD class='tableheader' WIDTH=33%>" . _('Narrative') . "</TD>
				<TD class='tableheader' WIDTH=33%>" . _('Show') . "</TD>
			</TR>";
			
			$k=0;
			while($sindesc = DB_fetch_array($res)){
				
				if ($k==1){
					echo "<tr bgcolor='#CCCCCC'>";
					$k=0;
				} else {
					echo "<tr bgcolor='#EEEEEE'>";
					$k++;
				}
				
				printf("<TD>%s</TD>
						<TD>%s</TD>
						<TD ALIGN=right>%s</TD>
						<TD>%s</TD>
						<TD ALIGN=center>%s</TD></TR>",
						_('Supplier Invoice').': '.$sindesc['suppreference'].' ('.$sindesc['transno'].')',
						$sindesc['trandate'],
						number_format($sindesc['total'],2),
						$sindesc['transtext'],
						"<A TARGET=_blank HREF='rh_SuppInvoice_Details.php?&Transno=".$sindesc['transno']."'><IMG BORDER=0 SRC='".$rootpath.'/css/'.$theme.'/images'."/preview.gif' TITLE='" . _('Click to preview the invoice')."'>"."</A>");
				
			}
			
			echo '</TABLE>';
		}else { // CON DESCUENTO
			$sql = "SELECT supptrans.suppreference, supptrans.trandate, supptrans.transno,
							(supptrans.ovamount+supptrans.ovgst) AS total,
							supptrans.alloc, supptrans.transtext,
							rh_suppdisc_details.discount_total,
							rh_suppdisc_details.percent
					FROM supptrans
					INNER JOIN rh_suppdisc_details ON supptrans.id = rh_suppdisc_details.supptrans_id
					WHERE supptrans.type = 20";
			
			$res = DB_query($sql,$db);
			
			echo '<CENTER><TABLE WIDTH=90% COLSPAN=2 BORDER=2 CELLPADDING=4>';
			echo "<TR>
				<TD class='tableheader' WIDTH=33%>" . _('Supplier Invoice') . "</TD>
				<TD class='tableheader' WIDTH=33%>". _('Date') . "</TD>
				<TD class='tableheader' WIDTH=33%>" . _('Total') . "</TD>
				<TD class='tableheader' WIDTH=33%>" . _('Discount') . " (%)</TD>
				<TD class='tableheader' WIDTH=33%>" . _('Discount') .' '._('Total') . "</TD>
				<TD class='tableheader' WIDTH=33%>" . _('Balance') . "</TD>
				<TD class='tableheader' WIDTH=33%>" . _('Narrative') . "</TD>
				<TD class='tableheader' WIDTH=33%>" . _('Show') . "</TD>
				<TD class='tableheader' WIDTH=33%>" . _('Credit Note') . "</TD>
			</TR>";
			
			$k=0;
			while($sindesc = DB_fetch_array($res)){
				
				if ($k==1){
					echo "<tr bgcolor='#CCCCCC'>";
					$k=0;
				} else {
					echo "<tr bgcolor='#EEEEEE'>";
					$k++;
				}
				
				printf("<TD>%s</TD>
						<TD>%s</TD>
						<TD ALIGN=right>%s</TD>
						<TD ALIGN=right>%s</TD>
						<TD ALIGN=right>%s</TD>
						<TD ALIGN=right>%s</TD>
						<TD>%s</TD>
						<TD ALIGN=center>%s</TD>
						<TD>%s</TD></TR>",
						_('Supplier Invoice').': '.$sindesc['suppreference'].' ('.$sindesc['transno'].')',
						$sindesc['trandate'],
						number_format($sindesc['total'],2),
						number_format($sindesc['percent'],0).'%',
						number_format($sindesc['discount_total'],2),
						number_format($sindesc['total']-$sindesc['discount_total'],2),
						$sindesc['transtext'],
						"<A TARGET=_blank HREF='rh_SuppInvoice_Details.php?&Transno=".$sindesc['transno']."'><IMG BORDER=0 SRC='".$rootpath.'/css/'.$theme.'/images'."/preview.gif' TITLE='" . _('Click to preview the invoice')."'>"."</A>",
						"<A HREF='SupplierCredit.php?&DiscAmt=".$sindesc['discount_total']."&SupplierID=".$_SESSION['SupplierID']."'>"._('Create')."</A>");
			}
			
			echo '</TABLE>';
		}
}

echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID . "' METHOD=POST>";
echo '<B>' . $msg;

echo '</B><CENTER>
	<TABLE CELLPADDING=3 COLSPAN=4>
	<TR>
	<TD>' . _('Text in the NAME') . ':</FONT></TD>
	<TD>';

if (isset($_POST['Keywords'])) {

	echo "<INPUT TYPE='Text' NAME='Keywords' value='" . $_POST['Keywords'] . "' SIZE=20 MAXLENGTH=25>";

} else {

	echo "<INPUT TYPE='Text' NAME='Keywords' SIZE=20 MAXLENGTH=25>";
}

echo '</TD>
	<TD><B>' . _('OR') . '</B></FONT></TD>
	<TD>' . _('Text in CODE') . ':</FONT></TD>
	<TD>';

if (isset($_POST['SupplierCode'])) {

	echo "<INPUT TYPE='Text' NAME='SupplierCode' value='" . $_POST['SupplierCode'] . "' SIZE=15 MAXLENGTH=18>";

} else {

	echo "<INPUT TYPE='Text' NAME='SupplierCode' SIZE=15 MAXLENGTH=18>";

}

echo "</TD>
</TR>
</TABLE>
<CENTER>
<INPUT TYPE=SUBMIT NAME='Search' VALUE='" . _('Search Now') . "'>
</CENTER>";


If (isset($result) AND !isset($SingleSupplierReturned)) {
	$ListCount=DB_num_rows($result);
	$ListPageMax=ceil($ListCount/$_SESSION['DisplayRecordsMax']);
	
	if (isset($_POST['Next'])) {
		if ($_POST['PageOffset'] < $ListPageMax) {
			$_POST['PageOffset'] = $_POST['PageOffset'] + 1;
		}
	}

	if (isset($_POST['Previous'])) {
		if ($_POST['PageOffset'] > 1) {
			$_POST['PageOffset'] = $_POST['PageOffset'] - 1;
		}
	}

	if ($ListPageMax >1) {
		echo "<P>&nbsp;&nbsp;" . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';
		
		echo '<SELECT NAME="PageOffset">';
		
		$ListPage=1;
		while($ListPage <= $ListPageMax) {
			if ($ListPage == $_POST['PageOffset']) {
				echo '<OPTION VALUE=' . $ListPage . ' SELECTED>' . $ListPage . '</OPTION>';
			} else {
				echo '<OPTION VALUE=' . $ListPage . '>' . $ListPage . '</OPTION>';
			}
			$ListPage++;
		}
		echo '</SELECT>
			<INPUT TYPE=SUBMIT NAME="Go" VALUE="' . _('Go') . '">
			<INPUT TYPE=SUBMIT NAME="Previous" VALUE="' . _('Previous') . '">
			<INPUT TYPE=SUBMIT NAME="Next" VALUE="' . _('Next') . '">';
		echo '<P>';
	}


	echo "<INPUT TYPE=hidden NAME='Search' VALUE='" . _('Search Now') . "'>";

  	echo '<br><br>';

  	echo '<BR><TABLE CELLPADDING=2 COLSPAN=7 BORDER=1>';
  	$tableheader = "<TR>
  		<TD class='tableheader'>" . _('Code') . "</TD>
		<TD class='tableheader'>" . _('Supplier Name') . "</TD>
		<TD class='tableheader'>" . _('Currency') . "</TD>
		<TD class='tableheader'>" . _('Address 1') . "</TD>
		<TD class='tableheader'>" . _('Address 2') . "</TD>
		<TD class='tableheader'>" . _('Address 3') . "</TD>
		<TD class='tableheader'>" . _('Address 4') . "</TD>
		</TR>";
	echo $tableheader;

	$j = 1;

  	$RowIndex = 0;

  	if (DB_num_rows($result)<>0){
 		DB_data_seek($result, ($_POST['PageOffset']-1)*$_SESSION['DisplayRecordsMax']);
  	}

	while (($myrow=DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {

		printf("<tr>
			<td><INPUT TYPE=SUBMIT NAME='Select' VALUE='%s'</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			</tr>",
			$myrow['supplierid'],
			$myrow['suppname'],
			$myrow['currcode'],
			$myrow['address1'],
			$myrow['address2'],
			$myrow['address3'],
			$myrow['address4']);

    $RowIndex = $RowIndex + 1;
//end of page full new headings if
	}
//end of while loop

	echo '</TABLE></CENTER>';

}
//end if results to show


if (isset($ListPageMax) and $ListPageMax >1) {
	echo "<P>&nbsp;&nbsp;" . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';
	
	echo '<SELECT NAME="PageOffset">';
	
	$ListPage=1;
	while($ListPage <= $ListPageMax) {
		if ($ListPage == $_POST['PageOffset']) {
			echo '<OPTION VALUE=' . $ListPage . ' SELECTED>' . $ListPage . '</OPTION>';
		} else {
			echo '<OPTION VALUE=' . $ListPage . '>' . $ListPage . '</OPTION>';
		}
		$ListPage++;
	}
	echo '</SELECT>
		<INPUT TYPE=SUBMIT NAME="Go" VALUE="' . _('Go') . '">
		<INPUT TYPE=SUBMIT NAME="Previous" VALUE="' . _('Previous') . '">
		<INPUT TYPE=SUBMIT NAME="Next" VALUE="' . _('Next') . '">';
	echo '<P>';
}

echo '</FORM>';
include('includes/footer.inc');
?>

<script language='JavaScript' type='text/javascript'>
    //<![CDATA[
            <!--
            document.forms[0].SupplierCode.select();
            document.forms[0].SupplierCode.focus();
            //-->
    //]]>
</script>
