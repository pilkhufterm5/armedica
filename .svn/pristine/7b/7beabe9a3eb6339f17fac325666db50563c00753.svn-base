<?php

/* $Revision: 33 $ */
/**
 * bowikaxu realhost feb 2008
 * compare invoice cost details and sales details
 *
 */

$PageSecurity = 8;

include ('includes/session.inc');
$title = _('General Ledger Transaction Inquiry');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if (!isset($_GET['TypeID']) OR !isset($_GET['TransNo'])) { /*Script was not passed the correct parameters */

	prnMsg(_('The script must be called with a valid transaction type and transaction number to review the general ledger postings for'),'warn');
	echo "<P><A HREF='$rootpath/index.php?". SID ."'>" . _('Back to the menu') . '</A>';
	exit;
}


$SQL = "SELECT typename, typeno FROM systypes WHERE typeid=" . $_GET['TypeID'];

$ErrMsg =_('The transaction type') . ' ' . $_GET['TypeID'] . ' ' . _('could not be retrieved');
$TypeResult = DB_query($SQL,$db,$ErrMsg);

if (DB_num_rows($TypeResult)==0){
	prnMsg(_('No transaction type is defined for type') . ' ' . $_GET['TypeID'],'error');
	include('includes/footer.inc');
	exit;
}

$myrow = DB_fetch_row($TypeResult);
$TransName = $myrow[0];
if ($myrow[1]<$_GET['TransNo']){
	prnMsg(_('The transaction number the script was called with is requesting a') . ' ' . $TransName . ' ' . _('beyond the last one entered'),'error');
	include('includes/footer.inc');
	exit;
}

//echo '<BR><CENTER><FONT SIZE=4 COLOR=BLUE>'.$TransName.' ' . $_GET['TransNo'] . '</FONT>';

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
	WHERE gltrans.type= " . $_GET['TypeID'] . "
	AND gltrans.typeno = " . $_GET['TransNo'] . "
	ORDER BY counterindex";

$ErrMsg = _('The transactions for') . ' ' . $TransName . ' ' . _('number') . ' ' .  $_GET['TransNo'] . ' '. _('could not be retrieved');
$TransResult = DB_query($SQL,$db,$ErrMsg);

if (DB_num_rows($TransResult)==0){
	prnMsg(_('No general ledger transactions have been created for') . ' ' . $TransName . ' ' . _('number') . ' ' . $_GET['TransNo'],'info');
	include('includes/footer.inc');
	exit;
}

/*show a table of the transactions returned by the SQL */

/*echo '<CENTER><TABLE CELLPADDING=2 width=100%>';*/

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
			<TR><TD class='tableheader'>"._('General Ledger Transaction Details')."</td>
			<TD class='tableheader'>"._('Date')."</td>
			<TD class='tableheader'>"._('Customer Name')."</td>
			<TD class='tableheader'>"._('Period')."</td></tr>
			<TR><TD><FONT SIZE=+1>".$TransName.' ' . $_GET['TransNo']."</FONT></TD>
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
			<TR><TD class='tableheader'>"._('General Ledger Transaction Details')."</td>
			<TD class='tableheader'>"._('Date')."</td>
			<TD class='tableheader'>"._('Period')."</td>
			<TD class='tableheader'>"._('Supplier Name')."</td></tr>
			<TR><TD>".$TransName.' ' . $_GET['TransNo']."</TD>
			<TD>".$myrow['trandate']."</TD>
			<TD>".$myrow['periodno']."</TD>
			<TD>".$supp['name'].' ['.$supp['supplierno'].']'."</TD></TR>
			</TABLE><BR>";
		}
		// else
		else {
			echo "
			<CENTER><TABLE CELLPADDING=4 CELLSPACING=3 width=80%>
			<TR><TD class='tableheader'>"._('General Ledger Transaction Details')."</td>
			<TD class='tableheader'>"._('Date')."</td>
			<TD class='tableheader'>"._('Period')."</td></tr>
			<TR><TD>".$TransName.' ' . $_GET['TransNo']."</TD>
			<TD>".$myrow['trandate']."</TD>
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
if(abs($TotDebit) == abs($TotCredit)){
	echo "<TR><TD></TD><TD class='tableheader' COLSPAN=2><STRONG><BIG>"._('Total')."</BIG></STRONG></TD>
	<TD class='tableheader' ALIGN=RIGHT><STRONG><BIG>".number_format(abs($TotDebit),2)."</BIG></STRONG></TD>
	<TD class='tableheader' ALIGN=RIGHT><STRONG><BIG>".number_format(abs($TotCredit),2)."</BIG></STRONG></TD>
	</TR>";
}else {
	echo "<TR><TD></TD><TD bgcolor=white COLSPAN=2><FONT COLOR=red><STRONG><BIG>"._('Total')."</BIG></STRONG></FONT></TD>
	<TD bgcolor=white  ALIGN=RIGHT><FONT COLOR=red><STRONG><BIG>".number_format(abs($TotDebit),2)."</BIG></STRONG></FONT></TD>
	<TD bgcolor=white ALIGN=RIGHT><FONT COLOR=red><STRONG><BIG>".number_format(abs($TotCredit),2)."</BIG></STRONG></FONT></TD>
	</TR>";
}

echo '</TABLE></CENTER>';

// boiwkaxu realhost Feb 2008 --------------- SHOW COST DETAILS ---------------------

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
	WHERE gltrans.type= 25
	AND gltrans.typeno IN (SELECT consignment FROM debtortrans WHERE type = ".$_GET['TypeID']." AND transno  = ".$_GET['TransNo'].")
	ORDER BY counterindex";
echo $SQL;
$ErrMsg = _('The transactions for') . ' ' . $TransName . ' ' . _('number') . ' ' .  $_GET['TransNo'] . ' '. _('could not be retrieved');
$TransResult = DB_query($SQL,$db,$ErrMsg);

if (DB_num_rows($TransResult)==0){
	prnMsg(_('No general ledger transactions have been created for') . ' ' . $TransName . ' ' . _('number') . ' ' . $_GET['TransNo'],'info');
	include('includes/footer.inc');
	exit;
}

/*show a table of the transactions returned by the SQL */

/*echo '<CENTER><TABLE CELLPADDING=2 width=100%>';*/

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
			<TR><TD class='tableheader'>"._('General Ledger Transaction Details')."</td>
			<TD class='tableheader'>"._('Date')."</td>
			<TD class='tableheader'>"._('Customer Name')."</td>
			<TD class='tableheader'>"._('Period')."</td></tr>
			<TR><TD><FONT SIZE=+1>".$TransName.' ' . $_GET['TransNo']."</FONT></TD>
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
			<TR><TD class='tableheader'>"._('General Ledger Transaction Details')."</td>
			<TD class='tableheader'>"._('Date')."</td>
			<TD class='tableheader'>"._('Period')."</td>
			<TD class='tableheader'>"._('Supplier Name')."</td></tr>
			<TR><TD>".$TransName.' ' . $_GET['TransNo']."</TD>
			<TD>".$myrow['trandate']."</TD>
			<TD>".$myrow['periodno']."</TD>
			<TD>".$supp['name'].' ['.$supp['supplierno'].']'."</TD></TR>
			</TABLE><BR>";
		}
		// else
		else {
			echo "
			<CENTER><TABLE CELLPADDING=4 CELLSPACING=3 width=80%>
			<TR><TD class='tableheader'>"._('General Ledger Transaction Details')."</td>
			<TD class='tableheader'>"._('Date')."</td>
			<TD class='tableheader'>"._('Period')."</td></tr>
			<TR><TD>".$TransName.' ' . $_GET['TransNo']."</TD>
			<TD>".$myrow['trandate']."</TD>
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
if(abs($TotDebit) == abs($TotCredit)){
	echo "<TR><TD></TD><TD class='tableheader' COLSPAN=2><STRONG><BIG>"._('Total')."</BIG></STRONG></TD>
	<TD class='tableheader' ALIGN=RIGHT><STRONG><BIG>".number_format(abs($TotDebit),2)."</BIG></STRONG></TD>
	<TD class='tableheader' ALIGN=RIGHT><STRONG><BIG>".number_format(abs($TotCredit),2)."</BIG></STRONG></TD>
	</TR>";
}else {
	echo "<TR><TD></TD><TD bgcolor=white COLSPAN=2><FONT COLOR=red><STRONG><BIG>"._('Total')."</BIG></STRONG></FONT></TD>
	<TD bgcolor=white  ALIGN=RIGHT><FONT COLOR=red><STRONG><BIG>".number_format(abs($TotDebit),2)."</BIG></STRONG></FONT></TD>
	<TD bgcolor=white ALIGN=RIGHT><FONT COLOR=red><STRONG><BIG>".number_format(abs($TotCredit),2)."</BIG></STRONG></FONT></TD>
	</TR>";
}

echo '</TABLE></CENTER>';
// END COST GL DETAILS

include('includes/footer.inc');

?>
