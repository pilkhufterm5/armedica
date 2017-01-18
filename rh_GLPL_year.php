<?php

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-04-01 09:33:18 -0600 (Tue, 01 Apr 2008) $
 * $Rev: 138 $
 */

$PageSecurity = 8;

include ('includes/session.inc');
$title = _('Profit and Loss by Year');
include('includes/SQL_CommonFunctions.inc');
include('includes/AccountSectionsDef.inc'); // This loads the $Sections variable

/*
if ($_POST['FromPeriod'] > $_POST['ToPeriod']){
	prnMsg(_('The selected period from is actually after the period to') . '! ' . _('Please reselect the reporting period'),'error');
	$_POST['SelectADifferentPeriod']='Select A Different Period';
}
*/
if (!isset($_POST['FromPeriod']) OR isset($_POST['SelectADifferentPeriod'])){

	include('includes/header.inc');
	echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
/*
	if (Date('m') > $_SESSION['YearEnd']){
		//Dates in SQL format 
		$DefaultFromDate = Date ('Y-m-d', Mktime(0,0,0,$_SESSION['YearEnd'] + 2,0,Date('Y')));
	} else {
		$DefaultFromDate = Date ('Y-m-d', Mktime(0,0,0,$_SESSION['YearEnd'] + 2,0,Date('Y')-1));
	}
*/
	$DefaultFromDate = date('Y');
	/*Show a form to allow input of criteria for profit and loss to show */
	echo '<CENTER><TABLE><TR><TD>'._('Select a Year').":</TD><TD><SELECT Name='FromPeriod'>";

	$sql = 'SELECT periodno, lastdate_in_period, YEAR(lastdate_in_period) AS year_ FROM periods GROUP BY year_';
	$Periods = DB_query($sql,$db);

	while ($myrow=DB_fetch_array($Periods,$db)){
		if(isset($_POST['FromPeriod']) AND $_POST['FromPeriod']!=''){
			if( $_POST['FromPeriod']== $myrow['periodno']){
				echo '<OPTION SELECTED VALUE=' . $myrow['periodno'] . '>' .$myrow['year_'];
			} else {
				echo '<OPTION VALUE=' . $myrow['periodno'] . '>' .$myrow['year_'];
			}
		} else {
			if($myrow['year_']==$DefaultFromDate){
				echo '<OPTION SELECTED VALUE=' . $myrow['periodno'] . '>' . $myrow['year_'];
			} else {
				echo '<OPTION VALUE=' . $myrow['periodno'] . '>' . $myrow['year_'];
			}
		}
	}

	echo '</SELECT></TD></TR>';

	echo '</TABLE>';

	echo "<INPUT TYPE=SUBMIT Name='ShowPL' Value='"._('Show Statement of Profit and Loss')."'></CENTER>";
	//echo "<CENTER><INPUT TYPE=SUBMIT Name='PrintPDF' Value='"._('PrintPDF')."'></CENTER>";

	/*Now do the posting while the user is thinking about the period to select */

	include ('includes/GLPostings.inc');

} else {

	include('includes/header.inc');
	echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
	echo "<INPUT TYPE=HIDDEN NAME='FromPeriod' VALUE=" . $_POST['FromPeriod'] . "><INPUT TYPE=HIDDEN NAME='ToPeriod' VALUE=" . $_POST['ToPeriod'] . '>';

	$NumberOfMonths = 12;

	$InitPeriod = $_POST['FromPeriod'];

$SQL = 'SELECT accountgroups.groupname,  
(Sum(CASE WHEN chartdetails.period="'.$InitPeriod.'" THEN chartdetails.bfwd ELSE 0 END))- (Sum(CASE WHEN chartdetails.period="'.$InitPeriod.'" THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END)) as balance_ene,
(Sum(CASE WHEN chartdetails.period="'.($InitPeriod+1).'" THEN chartdetails.bfwd ELSE 0 END))- (Sum(CASE WHEN chartdetails.period="'.($InitPeriod+1).'" THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END)) as balance_feb,
(Sum(CASE WHEN chartdetails.period="'.($InitPeriod+2).'" THEN chartdetails.bfwd ELSE 0 END))- (Sum(CASE WHEN chartdetails.period="'.($InitPeriod+2).'" THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END)) as balance_mar,
(Sum(CASE WHEN chartdetails.period="'.($InitPeriod+3).'" THEN chartdetails.bfwd ELSE 0 END))- (Sum(CASE WHEN chartdetails.period="'.($InitPeriod+3).'" THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END)) as balance_abr,
(Sum(CASE WHEN chartdetails.period="'.($InitPeriod+4).'" THEN chartdetails.bfwd ELSE 0 END))- (Sum(CASE WHEN chartdetails.period="'.($InitPeriod+4).'" THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END)) as balance_may,
(Sum(CASE WHEN chartdetails.period="'.($InitPeriod+5).'" THEN chartdetails.bfwd ELSE 0 END))- (Sum(CASE WHEN chartdetails.period="'.($InitPeriod+5).'" THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END)) as balance_jun,
(Sum(CASE WHEN chartdetails.period="'.($InitPeriod+6).'" THEN chartdetails.bfwd ELSE 0 END))- (Sum(CASE WHEN chartdetails.period="'.($InitPeriod+6).'" THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END)) as balance_jul,
(Sum(CASE WHEN chartdetails.period="'.($InitPeriod+7).'" THEN chartdetails.bfwd ELSE 0 END))- (Sum(CASE WHEN chartdetails.period="'.($InitPeriod+7).'" THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END)) as balance_ago,
(Sum(CASE WHEN chartdetails.period="'.($InitPeriod+8).'" THEN chartdetails.bfwd ELSE 0 END))- (Sum(CASE WHEN chartdetails.period="'.($InitPeriod+8).'" THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END)) as balance_sep,
(Sum(CASE WHEN chartdetails.period="'.($InitPeriod+9).'" THEN chartdetails.bfwd ELSE 0 END))- (Sum(CASE WHEN chartdetails.period="'.($InitPeriod+9).'" THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END)) as balance_oct,
(Sum(CASE WHEN chartdetails.period="'.($InitPeriod+10).'" THEN chartdetails.bfwd ELSE 0 END))- (Sum(CASE WHEN chartdetails.period="'.($InitPeriod+10).'" THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END)) as balance_nov,
(Sum(CASE WHEN chartdetails.period="'.($InitPeriod+11).'" THEN chartdetails.bfwd ELSE 0 END))- (Sum(CASE WHEN chartdetails.period="'.($InitPeriod+11).'" THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END)) as balance_dic
 FROM chartmaster INNER JOIN accountgroups ON chartmaster.group_ = accountgroups.groupname 
 INNER JOIN chartdetails ON chartmaster.accountcode= chartdetails.accountcode WHERE accountgroups.pandl=1 
 GROUP BY accountgroups.groupname, accountgroups.sectioninaccounts, accountgroups.groupname, 
 accountgroups.sequenceintb ORDER BY accountgroups.sectioninaccounts, accountgroups.sequenceintb, 
 chartdetails.accountcode';

	$AccountsResult = DB_query($SQL,$db,_('No general ledger accounts were returned by the SQL because'),_('The SQL that failed was'));

	echo '<CENTER><FONT SIZE=4 COLOR=BLUE><B>' . _('Statement of Profit and Loss for the'). ' ' . $NumberOfMonths . ' ' . _('months').'</B></FONT><BR>';

	/*show a table of the accounts info returned by the SQL
	Account Code ,   Account Name , Month Actual, Month Budget, Period Actual, Period Budget */

	echo '<TABLE CELLPADDING=2 width="98%">';

		$TableHeader = "<TR>
				<TD class='tableheader'>Nombre</TD>
				<TD class='tableheader'>Ene</TD>
				<TD class='tableheader'>Feb</TD>
				<TD class='tableheader'>Mar</TD>
				<TD class='tableheader'>Abr</TD>
				<TD class='tableheader'>May</TD>
				<TD class='tableheader'>Jun</TD>
				<TD class='tableheader'>Jul</TD>
				<TD class='tableheader'>Ago</TD>
				<TD class='tableheader'>Sep</TD>
				<TD class='tableheader'>Oct</TD>
				<TD class='tableheader'>Nov</TD>
				<TD class='tableheader'>Dic</TD>				
				</TR>";
	


	echo $TableHeader;
	$j = 1;
	$k=0; //row colour counter
	$Section='';
	$SectionPrdActual= 0;
	$SectionPrdLY 	 = 0;
	$SectionPrdBudget= 0;

	$ActGrp ='';
	$GrpPrdActual	= 0;
	$GrpPrdLY 	= 0;
	$GrpPrdBudget 	= 0;

	$ene = 0;
	$feb = 0;
	$mar = 0;
	$abr = 0;
	$may = 0;
	$jun = 0;
	$jul = 0;
	$ago = 0;
	$sep = 0;
	$oct = 0;
	$nov = 0;
	$dic = 0;

	while ($myrow=DB_fetch_array($AccountsResult)) {

	$ene += $myrow['balance_ene'];
	$feb += $myrow['balance_feb'];
	$mar += $myrow['balance_mar'];
	$abr += $myrow['balance_abr'];
	$may += $myrow['balance_may'];
	$jun += $myrow['balance_jun'];
	$jul += $myrow['balance_jul'];
	$ago += $myrow['balance_ago'];
	$sep += $myrow['balance_sep'];
	$oct += $myrow['balance_oct'];
	$nov += $myrow['balance_nov'];
	$dic += $myrow['balance_dic'];

	$TableHeader = "<TR>
				<TD class='tableheader'>".$myrow['groupname']."</TD>
				<TD align='right'>".number_format($myrow['balance_ene'])."</TD>
				<TD align='right'>".number_format($myrow['balance_feb'])."</TD>
				<TD align='right'>".number_format($myrow['balance_mar'])."</TD>
				<TD align='right'>".number_format($myrow['balance_abr'])."</TD>
				<TD align='right'>".number_format($myrow['balance_may'])."</TD>
				<TD align='right'>".number_format($myrow['balance_jun'])."</TD>
				<TD align='right'>".number_format($myrow['balance_jul'])."</TD>
				<TD align='right'>".number_format($myrow['balance_ago'])."</TD>
				<TD align='right'>".number_format($myrow['balance_sep'])."</TD>
				<TD align='right'>".number_format($myrow['balance_oct'])."</TD>
				<TD align='right'>".number_format($myrow['balance_nov'])."</TD>
				<TD align='right'>".number_format($myrow['balance_dic'])."</TD>				
				</TR>";
				
	echo $TableHeader;
	}
	//end of loop



	echo '<TR>
		<TD COLSPAN=2></TD>
		<TD COLSPAN=6><HR></TD>
		</TR>';

	echo "<tr bgcolor='#ffffff'>
		<td><FONT SIZE=4 COLOR=BLUE><B>"._('Profit').' - '._('Loss')."</B></FONT></td>
		<td ALIGN=RIGHT>".number_format($ene)."</td>
		<td ALIGN=RIGHT>".number_format($feb)."</td>
		<td ALIGN=RIGHT>".number_format($mar)."</td>
		<td ALIGN=RIGHT>".number_format($abr)."</td>
		<td ALIGN=RIGHT>".number_format($may)."</td>
		<td ALIGN=RIGHT>".number_format($jun)."</td>
		<td ALIGN=RIGHT>".number_format($jul)."</td>
		<td ALIGN=RIGHT>".number_format($ago)."</td>
		<td ALIGN=RIGHT>".number_format($sep)."</td>
		<td ALIGN=RIGHT>".number_format($oct)."</td>
		<td ALIGN=RIGHT>".number_format($nov)."</td>
		<td ALIGN=RIGHT>".number_format($dic)."</td>
		</tr>";

	echo '<TR>
		<TD COLSPAN=2></TD>
		<TD COLSPAN=6><HR></TD>
		</TR>';

	echo '</TABLE>';
	echo "<INPUT TYPE=SUBMIT Name='SelectADifferentPeriod' Value='"._('Select A Different Period')."'></CENTER>";
}
echo '</FORM>';
include('includes/footer.inc');

?>
