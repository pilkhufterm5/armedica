<?php
/**
 * REALHOST 2008
 * $LastChangedDate$
 * $Rev$
 */

/* webERP: Revision: 14 $ */

$PageSecurity = 8;
include ('includes/session.inc');
$title = _('Expenses Inquiry');
include('includes/header.inc');
include('includes/GLPostings.inc');

if (isset($_POST['Section'])){
	$SelectedSection = $_POST['Section'];
} elseif (isset($_GET['Section'])){
	$SelectedSection = $_GET['Section'];
}

if (isset($_POST['Period'])){
	$SelectedPeriod = $_POST['Period'];
} elseif (isset($_GET['Period'])){
	$SelectedPeriod = $_GET['Period'];
}

echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';

/*Dates in SQL format for the last day of last month*/
$DefaultPeriodDate = Date ('Y-m-d', Mktime(0,0,0,Date('m'),0,Date('Y')));

/*Show a form to allow input of criteria for TB to show */
echo '<CENTER><TABLE>
        <TR>
         <TD>'._('Account').":</TD>
         <TD><SELECT Name='Section'>";
$sql = 'SELECT accountcode, accountname FROM chartmaster
         		INNER JOIN accountgroups ON chartmaster.group_ = accountgroups.groupname
         		INNER JOIN accountsection ON accountgroups.sectioninaccounts = accountsection.sectionid
         WHERE accountsection.baseid IN (5,6)
         ORDER BY accountcode';

$sql = "SELECT sectionid as accountcode, sectionname as accountname FROM accountsection WHERE baseid IN (5,6)";

$Account = DB_query($sql,$db);
while ($myrow=DB_fetch_array($Account,$db)){
	if($myrow['accountcode'] == $SelectedSection){
		echo '<OPTION SELECTED VALUE=' . $myrow['accountcode'] . '>' . $myrow['accountname'];
	} else {
		echo '<OPTION VALUE=' . $myrow['accountcode'] . '>' . $myrow['accountname'];
	}
}
echo '</SELECT></TD></TR>
         <TR>
         <TD>'._('For Period range').':</TD>
         <TD><SELECT Name=Period[]>';
$sql = 'SELECT periodno, lastdate_in_period FROM periods ORDER BY periodno DESC';
$Periods = DB_query($sql,$db);
$id=0;
while ($myrow=DB_fetch_array($Periods,$db)){

	if($myrow['periodno'] == $SelectedPeriod[$id]){
		echo '<OPTION SELECTED VALUE=' . $myrow['periodno'] . '>' . _(MonthAndYearFromSQLDate($myrow['lastdate_in_period']));
		$id++;
	} else {
		echo '<OPTION VALUE=' . $myrow['periodno'] . '>' . _(MonthAndYearFromSQLDate($myrow['lastdate_in_period']));
	}

}
echo "</SELECT></TD>
        </TR>
</TABLE><P>
<INPUT TYPE=SUBMIT NAME='Show' VALUE='"._('Show Account Transactions')."'></CENTER></FORM>";

/* End of the Form  rest of script is what happens if the show button is hit*/

if (isset($_POST['Show'])){

	// TODO - generate a while wiht the account part of the sended gastos base

	if (!isset($SelectedPeriod)){
		prnMsg(_('A period or range of periods must be selected from the list box'),'info');
		include('includes/footer.inc');
		exit;
	}

	$FirstPeriodSelected = min($SelectedPeriod);
	$LastPeriodSelected = max($SelectedPeriod);
	//AND chartdetails.period <= '.$LastPeriodSelected.'

	$sql = 'SELECT chartmaster.accountcode, chartmaster.accountname, 
				SUM(chartdetails.actual) AS actual,
				SUM(chartdetails.bfwd) AS bfwd
				
				FROM chartmaster
         		INNER JOIN accountgroups ON chartmaster.group_ = accountgroups.groupname
				INNER JOIN chartdetails ON chartmaster.accountcode = chartdetails.accountcode
         WHERE accountgroups.sectioninaccounts = '.$SelectedSection.'
		 AND chartdetails.period = '.$FirstPeriodSelected.'
		 
		 GROUP BY chartmaster.accountcode
         ORDER BY accountcode';

	$sectres =DB_query($sql,$db);

	echo "<TABLE ALIGN=CENTER>
	<TR class='tableheader'>
	<TD>"._('Account Code')."</TD>
	<TD>"._('Account Name')."</TD>
	<TD>"._('Month Actual')."</TD>
	<TD>"._('Prior Month')."</TD>
	<TD>"._('Last Year')."</TD>
	<TD>"._('Acumulated').' '._('Month Actual')."</TD>
	<TD>"._('Acumulated').' '._('Last Year')."</TD>
	</TR>";
	
	$TotalMonth = 0;
	$TotalPriorMonth = 0;
	$TotalMonthbfwd = 0 ;
	$TotalLastYearbfwd = 0;
	
	while($res = DB_fetch_array($sectres)){
		$SelectedAccount = $res['accountcode'];
		/*Is the account a balance sheet or a profit and loss account */

		$sql = 'SELECT chartmaster.accountcode, chartmaster.accountname,
				SUM(chartdetails.actual) AS actual,
				SUM(chartdetails.bfwd) AS bfwd
				
				FROM chartmaster
         		INNER JOIN accountgroups ON chartmaster.group_ = accountgroups.groupname
				INNER JOIN chartdetails ON chartmaster.accountcode = chartdetails.accountcode
         WHERE accountgroups.sectioninaccounts = '.$SelectedSection.'
		 AND chartdetails.accountcode = '.$res['accountcode'].'
		 AND chartdetails.period = '.($FirstPeriodSelected-1).'
		 GROUP BY chartmaster.accountcode
         ORDER BY accountcode';
		$LastMonthres =DB_query($sql,$db);
		$LastMonth = DB_fetch_array($LastMonthres);
		
		$sql = 'SELECT chartmaster.accountcode, chartmaster.accountname,
				SUM(chartdetails.actual) AS actual,
				SUM(chartdetails.bfwd) AS bfwd
				
				FROM chartmaster
         		INNER JOIN accountgroups ON chartmaster.group_ = accountgroups.groupname
				INNER JOIN chartdetails ON chartmaster.accountcode = chartdetails.accountcode
         WHERE accountgroups.sectioninaccounts = '.$SelectedSection.'
		 AND chartdetails.accountcode = '.$res['accountcode'].'
		 AND chartdetails.period = '.($FirstPeriodSelected-12).'
		 
		 GROUP BY chartmaster.accountcode
         ORDER BY accountcode';
		$Lastquery = DB_query($sql,$db);
		$LastYear = DB_fetch_array($Lastquery); // last year bfwd and actual

		echo "
		<TR>
		<TD><a href='GLAccountInquiry.php?".SID."&Account=".$res['accountcode']."'>".$res['accountcode']."</a></TD>
		<TD>".$res['accountname']."</TD>
		<TD ALIGN=RIGHT>".number_format($res['actual'],2)."</TD>
		<TD ALIGN=RIGHT>".number_format($LastMonth['actual'],2)."</TD>
		<TD ALIGN=RIGHT>".number_format($LastYear['actual'],2)."</TD>
		<TD ALIGN=RIGHT>".number_format($res['bfwd'],2)."</TD>
		<TD ALIGN=RIGHT>".number_format($LastYear['bfwd'],2)."</TD>
		</TR>";
		
		$TotalMonth += $res['actual'];
		$TotalPriorMonth += $LastMonth['actual'];
		$TotalLastYear += $LastYear['actual'];
		$TotalMonthbfwd += $res['bfwd'];
		$TotalLastYearbfwd += $LastYear['bfwd'];
		unset($LastMonth);
		unset($LastYear);
	}
	
	echo "<TR class='tableheader'>
	<TD bgcolor=white></TD>
	<TD bgcolor=white></TD>
	<TD ALIGN=RIGHT><B>".number_format($TotalMonth,2)."</B></TD>
	<TD ALIGN=RIGHT><B>".number_format($TotalPriorMonth,2)."</B></TD>
	<TD ALIGN=RIGHT><B>".number_format($TotalLastYear,2)."</B></TD>
	<TD ALIGN=RIGHT><B>".number_format($TotalMonthbfwd,2)."</B></TD>
	<TD ALIGN=RIGHT><B>".number_format($TotalLastYearbfwd,2)."</B></TD>
	</TR>";
	
	echo "</TABLE>";
} /* end of if Show button hit */

include('includes/footer.inc');
?>
