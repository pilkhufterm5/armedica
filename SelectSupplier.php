<?php
/* $Revision: 324 $ */
/* $Revision: 324 $ */

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Search Suppliers');
include('includes/header.inc');

include('includes/Wiki.php');

$msg='';
/*
if (!isset($_POST['Search'])){
	$_POST['Search']='';
}
*/

echo '<a href=Suppliers.php>'. _('Create new supplier').'</a>';

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

	echo '<CENTER><TABLE WIDTH=90% COLSPAN=2 BORDER=2 CELLPADDING=4>';
	echo "<TR>
		<TH WIDTH=33%>" . _('Supplier Inquiries') . "</TH>
		<TH WIDTH=33%>". _('Supplier Transactions') . "</TH>
		<TH WIDTH=33%>" . _('Supplier Maintenance') . "</TH>
	</TR>";

	echo '<TR><TD VALIGN=TOP>';    /* Inquiry Options */

	echo "<A HREF=\"$rootpath/SupplierInquiry.php?" . SID . '&SupplierID=' . $_SESSION['SupplierID'] . "\">" . _('Supplier Account Inquiry') . '</A><BR>';

	echo '<BR>';

	echo "<BR><A HREF='$rootpath/PO_SelectOSPurchOrder.php?" . SID . '&SelectedSupplier=' . $_SESSION['SupplierID'] . "'>" . _('Receive / Outstanding Purchase Orders') . '</A>';
	echo "<BR><A HREF='$rootpath/PO_SelectPurchOrder.php?" . SID . '&SelectedSupplier=' . $_SESSION['SupplierID'] . "'>" . _('View All Purchase Orders') . '</A><BR>';

	wikiLink('Supplier', $_SESSION['SupplierID']);	
	
	echo '<BR>';

	echo "<BR><A HREF='$rootpath/Shipt_Select.php?" . SID . '&SelectedSupplier=' . $_SESSION['SupplierID'] . "'>" . _('Search Shipments') . '</A>';

	echo '</TD><TD VALIGN=TOP>'; /* Supplier Transactions */

	echo "<A HREF=\"$rootpath/SupplierInvoice.php?" . SID . '&SupplierID=' . $_SESSION['SupplierID'] . "\">" . _('Enter a Suppliers Invoice') . '</A><BR>';

        //iJPe
        echo "<A HREF=\"$rootpath/rh_Search_PO_InvNum.php?" . SID . '&SelectedSupplier=' . $_SESSION['SupplierID'] . "&SupplierInv=1\">" . _('Factura a partir de Factura Externa') . '</A><BR>';

	echo "<A HREF=\"$rootpath/SupplierCredit.php?" . SID . '&SupplierID=' . $_SESSION['SupplierID'] . "\">" . _('Enter a Suppliers Credit Note') . '</A><BR>';
	echo "<A HREF=\"$rootpath/Payments.php?" . SID . '&SupplierID=' . $_SESSION['SupplierID'] . "\">" . _('Enter a Payment to the Supplier') . '</A><BR>';
	//echo "<A HREF=\"$rootpath/rh_cheques_libera.php?" . SID . "\">" . _('Liberar Cheques') . '</A><BR>';

	echo '<BR>';
    //echo "<BR><A HREF='$rootpath/ReverseGRN_old.php?" . SID . '&SupplierID=' . $_SESSION['SupplierID'] . "'>" . _('Reverse an Outstanding GRN').' '._('WebERP') . '</A>';
	echo "<BR><A HREF='$rootpath/ReverseGRN.php?" . SID . '&SupplierID=' . $_SESSION['SupplierID'] . "'>" . _('Reverse an Outstanding GRN') . '</A>';

	echo '</TD><TD VALIGN=TOP>'; /* Supplier Maintenance */

        echo '<a href="' . $rootpath . '/Suppliers.php?">' . _('Add a New Supplier') . '</a><br>';
	echo "<A HREF=\"$rootpath/Suppliers.php?" . SID . '&SupplierID=' . $_SESSION['SupplierID'] . "\">" . _('Modify Or Delete Supplier Details') . '</A>';
	echo "<BR><A HREF=\"$rootpath/SupplierContacts.php?" . SID . '&SupplierID=' . $_SESSION['SupplierID'] . "\">" . _('Add/Modify/Delete Supplier Contacts') . '</A>';

	echo '<BR>';

	echo "<BR><A HREF='$rootpath/Shipments.php?" . SID . "&NewShipment=Yes'>" . _('Set Up A New Shipment') . '</A>';

	echo '</TD></TR></TABLE>';
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
		
		echo "<SELECT NAME='PageOffset' id='PageOffset' onchange=document.getElementById('PageOffset2').value=this.value>";
		
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
	
	echo "<SELECT NAME='PageOffset' id='PageOffset2' onchange=document.getElementById('PageOffset').value=this.value>";
	
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
