<?php
/* $Revision: 14 $ */
$PageSecurity = 2;

include('includes/session.inc');
$title = _('Cheque Paid Inquiry');
include('includes/header.inc');

echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . "' METHOD=POST>";

echo '<CENTER><TABLE CELLPADDING=2><TR>';
/*
echo '<TD>' . _('Type') . ":</TD><TD><SELECT name='TransType'> ";

$sql = 'SELECT typeid, typename FROM systypes WHERE typeid = 10 OR typeid=12';
$resultTypes = DB_query($sql,$db);
while ($myrow=DB_fetch_array($resultTypes)){
    if (isset($_POST['TransType'])){
        if ($myrow['typeid'] == $_POST['TransType']){
             echo "<OPTION SELECTED Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
        } else {
             echo "<OPTION Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
        }
    } else {
             echo "<OPTION Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
    }
}
echo '</SELECT></TD>';
*/
echo "<TD>"._('Type').":</TD><TD>"._('Cheque')."</TD>";

echo '<TD>'._('Cheque Number').":</TD>
	<TD><INPUT TYPE=TEXT NAME='ChequeNo' MAXLENGTH=10 SIZE=10 VALUE=". $_POST['ChequeNo'] . '></TD>';

echo "</TR></TABLE>
	<INPUT TYPE=SUBMIT NAME='ShowResults' VALUE="._('Show How Allocated').'>';
echo '<HR>';

if (isset($_POST['ShowResults']) AND  $_POST['ChequeNo']==''){
	prnMsg(_('The cheque number to be queried must be entered first'),'warn');
}

if (isset($_POST['ShowResults']) AND  $_POST['ChequeNo']!=''){


/*First off get the BankTransID of the transaction selected */
    $sql = 'SELECT supptrans.id,
    		(supptrans.ovamount+supptrans.ovgst) AS totamt
		FROM supptrans
		INNER JOIN banktrans ON supptrans.type = banktrans.type
					AND banktrans.transno = supptrans.transno
		WHERE banktrans.type IN (1, 22) 
		AND banktrans.rh_chequeno = "'.$_POST['ChequeNo'].'"';

    $result = DB_query($sql , $db);

    while($myrow = DB_fetch_array($result)){
        $AllocFromID = $myrow['id'];

        echo '<CENTER><FONT SIZE=3><B><BR>'._('Allocations made against cheque number') . ' ' . $_POST['TransNo'] . ' '._('Cheque Total').': '. number_format($myrow['totamt'],2) . '</FONT></B>';

        $sql = "SELECT type,
			transno,
			trandate,
			supptrans.supplierno,
			suppliers.suppname,
			supptrans.suppreference,
			rate,
			ovamount+ovgst as totalamt,
			suppallocs.amt
		FROM supptrans
			INNER JOIN suppallocs ON supptrans.id=suppallocs.transid_allocto
			INNER JOIN suppliers ON suppliers.supplierid = supptrans.supplierno
		WHERE suppallocs.transid_allocfrom=". $AllocFromID;

        $ErrMsg = _('The cheque transactions for the selected criteria could not be retrieved because');

        $TransResult = DB_query($sql, $db, $ErrMsg);
	
	if (DB_num_rows($TransResult)==0){
		prnMsg(_('There are no allocations made against this transaction'),'info');
	} else {
		
			echo '<TABLE CELLPADDING=2 BORDER=2>';
		
			$tableheader = "<TR><TD class='tableheader'>"._('Type')."</TD>
						<TD class='tableheader'>"._('Number')."</TD>
						<TD class='tableheader'>"._('Supplier')."</TD>
						<TD class='tableheader'>"._('Reference')."</TD>
						<TD class='tableheader'>"._('Ex Rate')."</TD>
						<TD class='tableheader'>"._('Amount')."</TD>
						<TD class='tableheader'>"._('Alloc').'</TD>
					</TR>';
			echo $tableheader;
		
			$RowCounter = 1;
			$k = 0; //row colour counter
			$AllocsTotal = 0;
		
			while ($myrow=DB_fetch_array($TransResult)) {
		
			if ($k==1){
				echo "<tr bgcolor='#CCCCCC'>";
				$k=0;
			} else {
				echo "<tr bgcolor='#EEEEEE'>";
				$k++;
			}
		
			if ($myrow['type']==20){
				$TransType = _('Invoice');
			} else if($myrow['type']==21) {
				$TransType = _('Debit Note');
			}else {
				$TransType = _('Pago a Proveedores');
			}
			printf( "<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td ALIGN=RIGHT>%s</td>
					<td ALIGN=RIGHT>%s</td>
					</tr>",
					$TransType,
					$myrow['transno'],
					$myrow['suppname'],
					$myrow['suppreference'],
					$myrow['rate'],
					$myrow['totalamt'],
					$myrow['amt']);
		
			$RowCounter++;
			If ($RowCounter == 12){
				$RowCounter=1;
				echo $tableheader;
			}
			//end of page full new headings if
			$AllocsTotal +=$myrow['amt'];
			}
			//end of while loop
			echo '<TR><TD COLSPAN = 6 ALIGN=RIGHT>' . number_format($AllocsTotal,2) . '</TD></TR>';
			echo '</TABLE>';
	}
    }
}

echo '</FORM></CENTER>';
include('includes/footer.inc');

?>