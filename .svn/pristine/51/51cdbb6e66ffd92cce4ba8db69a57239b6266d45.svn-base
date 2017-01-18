<?php
/* $Revision: 112 $ */

/* bowikaxu realhost - get the notifications send to this supplier or view pending notifications */

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Notifications');
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

	echo '<FONT SIZE=3><P>' . _('Supplier') . ' <B>' . $_SESSION['SupplierID']  . "-$SupplierName</B> " . _('is currently selected') . '.<BR>' . _('Select a menu option to operate using this supplier') . '<P></FONT>';
	echo "<FORM METHOD=POST ACTION='rh_PayNotifications_Inquiry.php'>";
	echo '<CENTER><SELECT NAME="notified">';
	if($_POST['notified'] == 1){
		echo "<OPTION SELECTED VALUE = 1>Notificados
				<OPTION VALUE=0>No Notificados";
		$SQL = "SELECT suppliers.suppname, rh_suppnotifications.*, supptrans.trandate, (supptrans.ovamount+supptrans.ovgst) AS total
		FROM suppliers
			INNER JOIN supptrans ON supptrans.supplierno = suppliers.supplierid
			INNER JOIN rh_suppnotifications ON supptrans.type = rh_suppnotifications.type
						AND rh_suppnotifications.transno = supptrans.transno
		WHERE suppliers.supplierid ='" . $_SESSION['SupplierID'] . "'";
	}else {
		echo "<OPTION VALUE = 1>Notificados
				<OPTION SELECTED VALUE=0>No Notificados";
		$SQL = "SELECT suppliers.suppname, supptrans.trandate, (supptrans.ovamount+supptrans.ovgst) AS total,
				supptrans.type, supptrans.transno
		FROM suppliers
			INNER JOIN supptrans ON supptrans.supplierno = suppliers.supplierid
		WHERE suppliers.supplierid ='" . $_SESSION['SupplierID'] . "'
		AND supptrans.transno NOT IN (SELECT transno FROM rh_suppnotifications)
		AND supptrans.type = 22";
	}
	echo '</SELECT>
	<INPUT TYPE=SUBMIT NAME="go" VALUE="'._('Go').'">
	</CENTER></FORM><BR><BR>';
	
	echo "<CENTER><FONT COLOR=BLUE SIZE=+2>"._('Supplier').' '.$SupplierName."</FONT></CENTER><BR>";
	
	$transres = DB_query($SQL,$db);
	
	echo '<CENTER><TABLE WIDTH=80% COLSPAN=1 BORDER=1 CELLPADDING=2>';
	echo "<TR>
		<TH WIDTH=16%>" . _('Type') . "</TH>
		<TH WIDTH=16%>". _('Trans No') . "</TH>
		<TH WIDTH=16%>". _('Total') . "</TH>
		<TH WIDTH=16%>" . _('Trans Date') . "</TH>
		<TH WIDTH=16%>" . _('Notify Date') . "</TH>
		<TH WIDTH=17%>" . _('Email') . "</TH>
	</TR>";

	$RowCounter = 1;
	$k = 0; //row colour counter
	
	while($trans = DB_fetch_array($transres)){
		
	if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k++;
		}
		
		echo '
			<TD align="right">'.$trans['type'].'</TD>
			<TD align="right">'.$trans['transno'].'</TD>
			<TD align="right">'.number_format($trans['total'],2).'</TD>
			<TD align="right">'.$trans['trandate'].'</TD>
			<TD align="right">'.$trans['date'].'</TD>
			<TD align="right">'.$trans['emails'].'</TD>';
		// bowikaxu realhost - sept 2008 -  always show mail option
		//if($_POST['notified'] != 1){
			echo '
				<TD align="right"><a href="rh_PaymentEmail.php?'.SID.'&TypeID='.$trans['type'].'&TransNo='.$trans['transno'].'">'._('Send').' '._('Notification').'</a></TD>';
		//}else {
			// si notificados
		//}
		echo '</TR>';    /* Inquiry Options */

	}
	echo '</TABLE><BR>';
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
  		<TH>" . _('Code') . "</TH>
		<TH>" . _('Supplier Name') . "</TH>
		<TH>" . _('Currency') . "</TH>
		<TH>" . _('Address 1') . "</TH>
		<TH>" . _('Address 2') . "</TH>
		<TH>" . _('Address 3') . "</TH>
		<TH>" . _('Address 4') . "</TH>
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
