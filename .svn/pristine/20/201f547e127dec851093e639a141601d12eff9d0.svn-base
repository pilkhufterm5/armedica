<?php

/**
 * REALHOST 2008
 * $LastChangedDate$
 * $Rev$
 */

// bowikaxu realhost March 2008 - cerrar el anyo y hacer ajustes necesarios.

$PageSecurity = 8;
include ('includes/session.inc');
include ('includes/SQL_CommonFunctions.inc');
$title = _('Close').' '._('Year');
include('includes/header.inc');

if(isset($_POST['submit']) AND isset($_POST['ClosePeriod'])){
	//echo "INICIANDO CIERRE ANUAL<br>";
	
	DB_query('BEGIN',$db); // BEGIN
	
	$sql = "SELECT lastdate_in_period, YEAR(lastdate_in_period) AS year_ 
			FROM periods
			WHERE periodno = ".$_POST['ClosePeriod']."
			GROUP BY year_";
	$res = DB_query($sql,$db);
	$info = DB_fetch_array($res);
	
	$sql = "SELECT periodno FROM periods WHERE MONTH(lastdate_in_period)=1 AND YEAR(lastdate_in_period)=".($info['year_']+1)."";
	$res = DB_query($sql,$db);
	$newyear = DB_fetch_array($res);
	
	$sql = "UPDATE config SET confvalue = '" . $newyear['periodno'] ."' WHERE confname = 'ProhibitPostingsBefore'";
	DB_query($sql,$db,'Error al cerrar el mes','ERROR: imposible cerrar el mes',true);
	
	$InitPeriod = ($newyear['periodno']-12);
	// bowikaxu - obtenerla utilidad o perdida del anyo
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
	$yearTotal = 0;

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
	
	$yearTotal = ($ene+$feb+$mar+$abr+$may+$jun+$jul+$ago+$sep+$oct+$nov+$dic);

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

	$sql = "INSERT INTO rh_closed_years (accountcode, period, trandate, amount, user_, narrative) VALUES(
	'".$_POST['Account']."',
	'".$_POST['ClosePeriod']."',
	'".date('Y-m-d')."',
	'".$yearTotal."',
	'".$_SESSION['UserID']."',
	'".DB_escape_string($_POST['comments'])."')";
	DB_query($sql,$db,'ERROR al insertar detalles del cierre','ERROR',true);
	
	$Trans = GetNextTransNo(1, $db);
	$DefaultDate = date($_SESSION['DefaultDateFormat']);
	$PeriodNo = GetPeriod($DefaultDate, $db);
	$sql = "INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount, posted) VALUES (
	1,
	".$Trans.",
	'".$DefaultDate."',
	".$PeriodNo.",
	'".$_POST['Account']."',
	'".$_POST['comments']."',
	".$yearTotal.",
	1)";
	DB_query($sql,$db,'ERROR al insertar asiento contable','ERROR',true);
	
	$sql = "SELECT accountcode, accountname, group_ 
			FROM chartmaster, accountgroups 
			WHERE chartmaster.group_=accountgroups.groupname
				AND pandl = 1
 			ORDER BY chartmaster.accountcode";
	$res = DB_query($sql,$db);
	while ($plact = DB_fetch_array($res)){
		$sql = "UPDATE chartdetails SET bfwd = 0 WHERE accountcode = '".$plact['accountcode']."'
				AND period = ".($newyear['periodno'])."";
		DB_query($sql,$db,'Imposible establecer saldos iniciales para el prox. a&ntilde;o.','ERROR:',true);
		
		// bowikaxu realhost - may 18 2008 - set posted to 1 on all gltrans between year periods on profit and loss accounts
		// so it wont be updated by gl postings script
		
		$sql = "UPDATE gltrans SET posted = 1 WHERE account = '".$plact['accountcode']."'
				AND (gltrans.periodno BETWEEN ".$InitPeriod." AND ".($InitPeriod+11).")";
		DB_query($sql,$db,'Imposible actualizar las transacciones del periodo.','ERROR:',true);
		
	}
	
	DB_query('COMMIT',$db); // COMMIT
	
	$_SESSION['ProhibitPostingsBefore'] = $_POST['ClosePeriod'];
	prnMsg('El a&ntilde;o se ha cerrado con &eacute;xito!','success');
	
}
echo "<FORM METHOD='POST' ACTION='rh_close_year.php'>";
echo "<TABLE ALIGN=center WIDTH=80%>";

echo '<TR><TD WIDTH=20%>' . _('Close').' '._('Year') . ':</TD>
	<TD WIDTH=20%><SELECT Name="ClosePeriod">';

$sql = 'SELECT periodno, lastdate_in_period, YEAR(lastdate_in_period) AS year_ FROM periods GROUP BY year_';
$Periods = DB_query($sql,$db);
while ($PeriodRow = DB_fetch_array($Periods)){
	if ($_POST['ClosePeriod']==$PeriodRow['periodno']){
		echo  '<OPTION SELECTED value="' . $PeriodRow['periodno'] . '">' . $PeriodRow['year_'];
	} else {
		echo  '<OPTION value="' . $PeriodRow['periodno'] . '">' . $PeriodRow['year_'];
	}
}
echo '</SELECT></TD WIDTH=20%>';

$sql = "SELECT accountcode, accountname FROM chartmaster";
$acts_res = DB_query($sql,$db);
echo "<TD><SELECT NAME='Account'>";
while($acts = DB_fetch_array($acts_res)){
	if($_POST['Account']==$acts['accountcode']){
		echo "<OPTION SELECTED VALUE='".$acts['accountcode']."'>".$acts['accountname'].' ['.$acts['accountcode'].']';
	}else {
		echo "<OPTION VALUE='".$acts['accountcode']."'>".$acts['accountname'].' ['.$acts['accountcode'].']';
	}
}
echo "</TD>";
echo "<TD WIDTH=20%><TEXTAREA NAME='comments' COLS=20 ROWS=10></TEXTAREA></TD>";

echo '<TD WIDTH=20%>' . _('Cierra el A&ntilde;o calculando y guardando su resultado de Utilidad o Perdida y Dejando Saldos Iniciales en ceros para el proximo a&ntilde;o.') . '</TD></TR>';
echo "<TR><TD></TD>
			<TD COLSPAN=2></TD></TR>";
echo "<TR><TD COLSPAN=3><INPUT TYPE=submit NAME='submit' VALUE='"._('Send')."' onclick=\"return confirm('" . _('Estas seguro que deseas realizar el Cierre Anual?') . "');\"></TD></TR>";
echo "</TABLE></FORM>";

include('includes/footer.inc');

?>