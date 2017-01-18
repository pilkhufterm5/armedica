<?php
// bowikaxu realhost Feb 2008 - reporte transacciones contables
/* webERP Revision: 1.12 $ */
/**
 * REALHOST 2008
 * $LastChangedDate$
 * $Rev$
 */
$PageSecurity = 2;

include('includes/session.inc');
$title = _('Customer Transactions Inquiry');
include('includes/header.inc');

if (isset($_POST['Period'])){
	$SelectedPeriod = $_POST['Period'];
} elseif (isset($_GET['Period'])){
	$SelectedPeriod = $_GET['Period'];
}

echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . "' METHOD=POST>";

echo '<CENTER><TABLE CELLPADDING=2><TR>';

echo '<TD>' . _('Type') . ":</TD><TD><SELECT name='TransType'> ";
// bowikaxu - March 2007 - Se agrego el tipo 20000 de remisiones a las busquedas
$sql = 'SELECT typeid, typename FROM systypes WHERE typeno > 0';
$resultTypes = DB_query($sql,$db);
// tipos
echo "<OPTION Value='All'>"._('All');
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
echo '</SELECT></TD></TR>';
// bancos
$sql = 'SELECT * FROM bankaccounts';
$result = DB_query($sql,$db);
echo "<TR><TD>"._('Bank Account')."</TD><TD><SELECT NAME=Bank>";
echo "<OPTION Value='All'>"._('All')."</OPTION>";
$id=0;
while($bank = DB_fetch_array($result)){
	if($bank['accountcode'] == $_POST['Bank']){
		echo '<OPTION SELECTED VALUE=' . $bank['accountcode'] . '>'.$bank['bankaccountname'];
		$id++;
	} else {
		echo '<OPTION VALUE=' . $bank['accountcode'] . '>'.$bank['bankaccountname'];
	}
}
echo "</SELECT></TD></TR>";
// periodos
$sql = 'SELECT * FROM periods';
$result = DB_query($sql,$db);
echo "<TR><TD>"._('Period')."</TD><TD><SELECT MULTIPLE NAME=Period[]>";
//echo "<OPTION Value='All'>"._('All')."</OPTION>";
$id=0;
while($per = DB_fetch_array($result)){
	if($per['periodno'] == $SelectedPeriod[$id]){
		echo '<OPTION SELECTED VALUE=' . $per['periodno'] . '>' . _(MonthAndYearFromSQLDate($per['lastdate_in_period']));
		$id++;
	} else {
		echo '<OPTION VALUE=' . $per['periodno'] . '>' . _(MonthAndYearFromSQLDate($per['lastdate_in_period']));
	}
}
echo "</SELECT></TD></TR>";

echo "<TR><TD>"._('Ordenar por')."</TD><TD>
<SELECT NAME=orderby>
<OPTION SELECTED VALUE=date>Fecha
<OPTION VALUE=int>Num. Trans.
</SELECT></TD></TR>";
echo "</TR></TABLE><INPUT TYPE=SUBMIT NAME='ShowResults' VALUE='" . _('Show Transactions') . "'>";
echo '<HR>';

echo '</FORM></CENTER>';

if (isset($_POST['ShowResults']) && $_POST['Period'] != ''){
	$FromPeriod = min($_POST['Period']);
	$sql = "SELECT lastdate_in_period FROM periods WHERE periodno = ".$FromPeriod."";
	$FP = DB_query($sql,$db);
	$FromDate = DB_fetch_array($FP);
	
	$ToPeriod = max($_POST['Period']);
	$sql = "SELECT lastdate_in_period FROM periods WHERE periodno = ".$ToPeriod."";
	$TP = DB_query($sql,$db);
	$ToDate = DB_fetch_array($TP);

	$SQL = "SELECT gltrans.counterindex, gltrans.type, gltrans.typeno, systypes.typename FROM gltrans, systypes WHERE
   			gltrans.periodno >= ".$FromPeriod."
   			AND gltrans.periodno <= ".$ToPeriod."
   			AND gltrans.type = systypes.typeid";

	echo "<CENTER>
	<B>
	"._('GL Trans Inquiry')." "._('Detailed')."<br>"._('Desde Mes').": ".MonthAndYearFromSQLDate($FromDate['lastdate_in_period'])."
	 / "._('Al Mes').": ".MonthAndYearFromSQLDate($ToDate['lastdate_in_period'])."";
	
	if($_POST['TransType'] != 'All'){
		$SQL .= " AND type = ".$_POST['TransType'];
		echo "<br>"._('Trans Type').": ".$_POST['TransType'];
	}
	if($_POST['Bank']!= 'All'){
		$SQL .= " AND account = ".$_POST['Bank'];
		echo "<br>"._('Bank Account').": ".$_POST['Bank'];
	}
	
	$SQL .= " GROUP BY gltrans.type, gltrans.typeno";
	
	if($_POST['orderby']=='date'){
		$SQL .= " ORDER BY trandate";
		echo "<br>"._('Order by').": "._('Date');
	}else {
		$SQL .= " ORDER by type, typeno";
		echo "<br>"._('Trans Type').": "._('Trans Num');
	}
	
	echo "</B></CENTER>";
	$Preres = DB_query($SQL,$db,'ERROR','');
	
	while ($myrow3=DB_fetch_array($Preres)){

		$_GET['TransNo'] = $myrow3['typeno'];
		$_GET['TypeID'] = $myrow3['type'];
		$TransName = $myrow3['typename'];
		
		echo "<HR>";
		
		$SQL = "SELECT trandate,
		account,
		type,
		typeno,
		periodno,
		accountcode,
		accountname,
		narrative,
		amount,
		posted
	FROM gltrans INNER JOIN chartmaster
	ON gltrans.account = chartmaster.accountcode
	WHERE gltrans.type= " . $myrow3['type'] . "
	AND gltrans.typeno = " . $myrow3['typeno'] . "
	ORDER BY counterindex";

		$ErrMsg = _('The transactions for') . ' ' . $myrow3['type'] . ' ' . _('number') . ' ' . $myrow3['typeno'] . ' '. _('could not be retrieved');
		$TransResult = DB_query($SQL,$db,$ErrMsg);

		if (DB_num_rows($TransResult)==0){
			prnMsg(_('No general ledger transactions have been created for') . ' ' . $myrow3['type'] . ' ' . _('number') . ' ' . $myrow3['typeno'],'info');
			include('includes/footer.inc');
			exit;
		}
		/*show a table of the transactions returned by the SQL */

		$TableHeader = '<TR><TD class="tableheader">' . _('Account Number') . '</TD>
			<TD class="tableheader">' . _('Account') .'</TD>
			<TD class="tableheader">' . _('Narrative') .'</TD>
			<TD class="tableheader">'. _('Debit') .'</TD>
			<TD class="tableheader">'. _('Credit') .'</TD>
			<TD class="tableheader">'. _('Posted') . '</TD></TR>';

		//echo $TableHeader;

		$j = 1;
		$i=0;
		$k=0; //row colour counter

		$TotDebit = 0;
		$TotCredit = 0;

		while ($myrow=DB_fetch_array($TransResult)) {

			if($i==0){
				// print header
				// debtor
				if($myrow['type']==10 OR $myrow['type']==11 OR $myrow['type']==12){

					$sql = "SELECT debtortrans.debtorno, debtorsmaster.name FROM debtortrans
					INNER JOIN debtorsmaster ON debtorsmaster.debtorno = debtortrans.debtorno
					WHERE debtortrans.type = ".$myrow['type']."
					AND debtortrans.transno =".$myrow['typeno']."";
					$res = DB_query($sql,$db);
					$debtor = DB_fetch_array($res);

					echo "
			<CENTER><TABLE CELLPADDING=4 CELLSPACING=3 width=80%>
			<TR><TD class='tableheader' width=10%>"._('Num. P&oacute;liza')."</td>
			<TD class='tableheader'>"._('General Ledger Transaction Details')."</td>
			<TD class='tableheader'>"._('Date')."</td>
			<TD class='tableheader'>"._('Customer Name')."</td>
			<TD class='tableheader'>"._('Period')."</td></tr>
			<TR><TD BGCOLOR=WHITE></TD><TD><FONT SIZE=+1>".$TransName.' ' . $_GET['TransNo']."</FONT></TD>
			<TD><FONT SIZE=+1>".$myrow['trandate']."</FONT></TD>
			<TD><FONT SIZE=+1>".$debtor['name'].' ['.$debtor['debtorno'].']'."</FONT></TD>
			<TD>".$myrow['periodno']."</TD></TR>
			</TABLE><BR>";
				}
				// supplier
				else if($myrow['type']==20 OR $myrow['type']==21 OR $myrow['type']==22){

					$sql = "SELECT supptrans.supplierno, suppliers.suppname FROM supptrans
					INNER JOIN suppliers ON suppliers.supplierid = supptrans.supplierno
					WHERE supptrans.type = ".$myrow['type']."
					AND supptrans.transno =".$myrow['typeno']."";
					$res = DB_query($sql,$db);
					$supp = DB_fetch_array($res);

					echo "
			<CENTER><TABLE CELLPADDING=4 CELLSPACING=3 width=80%>
			<TR><TD class='tableheader' width=10%>"._('Num. P&oacute;liza')."</td>
			<TD class='tableheader'>"._('General Ledger Transaction Details')."</td>
			<TD class='tableheader'>"._('Date')."</td>
			<TD class='tableheader'>"._('Supplier Name')."</td>
			<TD class='tableheader'>"._('Period')."</td></tr>
			<TR><TD BGCOLOR=WHITE></TD><TD><FONT SIZE=+1>".$TransName.' ' . $_GET['TransNo']."</FONT></TD>
			<TD><FONT SIZE=+1>".$myrow['trandate']."</FONT></TD>
			<TD><FONT SIZE=+1>".$supp['name'].' ['.$supp['supplierno'].']'."</FONT></TD>
			<TD>".$myrow['periodno']."</TD></TR>
			</TABLE><BR>";
				}
				// else
				else {
					echo "
			<CENTER><TABLE CELLPADDING=4 CELLSPACING=3 width=80%>
			<TR><TD class='tableheader' width=10%>"._('Num. P&oacute;liza')."</td>
			<TD class='tableheader'>"._('General Ledger Transaction Details')."</td>
			<TD class='tableheader'>"._('Date')."</td>
			<TD class='tableheader'>"._('Period')."</td></tr>
			<TR><TD BGCOLOR=WHITE></TD><TD><FONT SIZE=+1>".$TransName.' ' . $_GET['TransNo']."</FONT></TD>
			<TD><FONT SIZE=+1>".$myrow['trandate']."</FONT></TD>
			<TD>".$myrow['periodno']."</TD></TR>
			</TABLE><BR>";
				}

				echo "<TABLE CELLPADDING=3 CELLSPACING=2 width=80%>";
				echo $TableHeader;
				$i++;
			}

			if ($k==1){
				echo '<tr bgcolor="#CCCCCC">';
				$k=0;
			} else {
				echo '<tr bgcolor="#EEEEEE">';
				$k++;
			}

			if ($myrow['posted']==0){
				$Posted = _('No');
			} else {
				$Posted = _('Yes');
			}

			if($myrow['amount']>0){
				$Debit = number_format($myrow['amount'],2);
				$Credit = '';
				$TotDebit += $myrow['amount'];
			}else {
				$Credit = number_format(-$myrow['amount'],2);
				$Debit = '';
				$TotCredit += $myrow['amount'];
			}

			$FormatedTranDate = ConvertSQLDate($myrow["trandate"]);
			printf('<td ALIGN=LEFT><a href=GLAccountInquiry.php?%s&Account=%s>%s</a></td>
       		<td ALIGN=LEFT>%s</td>
       		<td ALIGN=LEFT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=LEFT>%s</td>
		</tr>',
			SID,
			$myrow['accountcode'],
			$myrow['accountcode'],
			$myrow['accountname'],
			$myrow['narrative'],
			$Debit,
			$Credit,
			$Posted);

			$j++;
			If ($j == 18){
				$j=1;
				echo $TableHeader;
			}
		}
		//end of while loop

		// bowikaxu realhost feb 2008 - check if transaction its correct else show totals in red
		//if(abs($TotDebit) == abs($TotCredit)){
			echo "<TR><TD></TD><TD class='tableheader' COLSPAN=2><STRONG><BIG>"._('Total')."</BIG></STRONG></TD>
			<TD class='tableheader' ALIGN=RIGHT><STRONG><BIG>".number_format(abs($TotDebit),2)."</BIG></STRONG></TD>
			<TD class='tableheader' ALIGN=RIGHT><STRONG><BIG>".number_format(abs($TotCredit),2)."</BIG></STRONG></TD>
			</TR>";
	/*	
		}else {
			echo "<TR><TD></TD><TD bgcolor=white COLSPAN=2><STRONG><BIG>"._('Total')."</BIG></STRONG></FONT></TD>
			<TD bgcolor=white  ALIGN=RIGHT><STRONG><BIG>".number_format(abs($TotDebit),2)."</BIG></STRONG></TD>
			<TD bgcolor=white ALIGN=RIGHT><STRONG><BIG>".number_format(abs($TotCredit),2)."</BIG></STRONG></TD>
			</TR>";
		}
		*/
		echo '</TABLE></CENTER>';
	}
	//end of while loop
}
include('includes/footer.inc');

?>
