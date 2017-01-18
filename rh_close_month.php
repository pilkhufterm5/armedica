<?php

/**
 * REALHOST 2008
 * $LastChangedDate$
 * $Rev$
 */

// bowikaxu realhost March 2008 - cerrar el mes y prohibir transacciones con fechas anteriores al mes cerrado.

$PageSecurity = 8;
include ('includes/session.inc');
$title = _('Close').' '._('Month');
include('includes/header.inc');

if(isset($_POST['submit']) AND isset($_POST['ClosePeriod'])){
	
	$sql = "UPDATE config SET confvalue = '" . $_POST['ClosePeriod']."' WHERE confname = 'ProhibitPostingsBefore'";
	
	DB_query($sql,$db,'Error al cerrar el mes','ERROR: imposible cerrar el mes');
	$_SESSION['ProhibitPostingsBefore'] = $_POST['ClosePeriod'];
	$repost = 0;
	// bowikaxu realhost March 2008 - recalcular las transacciones en el registro contable
	if($repost == 1){
		/* Make the posted flag on all GL entries including and after the period selected = 0 */
		$sql = 'UPDATE gltrans SET posted=0 WHERE periodno >='. $_POST['ClosePeriod'];
		$UpdGLTransPostedFlag = DB_query($sql,$db);
	
		/* Now make all the actuals 0 for all periods including and after the period from */
		$sql = 'UPDATE chartdetails SET actual =0 WHERE period >= ' . $_POST['ClosePeriod'];
		$UpdActualChartDetails = DB_query($sql,$db);
	
		$ChartDetailBFwdResult = DB_query('SELECT accountcode, bfwd FROM chartdetails WHERE period=' . $_POST['ClosePeriod'],$db);	
		while ($ChartRow=DB_fetch_array($ChartDetailBFwdResult)){
			$sql = 'UPDATE chartdetails SET bfwd =' . $ChartRow['bfwd'] . ' WHERE period > ' . $_POST['ClosePeriod'] . ' AND accountcode=' . $ChartRow['accountcode'];
			$UpdActualChartDetails = DB_query($sql,$db);
		}
		
		/*Now repost the lot */
	
		include('includes/GLPostings.inc');
		prnMsg(_('All general ledger postings have been reposted from period') . ' ' . $_POST['ClosePeriod'],'success');
	}
	
	prnMsg('El mes se ha cerrado con exito!','success');
}else {
	
	prnMsg('Si usted cierra un mes, ya no podra realizar transacciones en los meses previos a ese mes.','warn');
	echo "<FORM METHOD='POST' ACTION='rh_close_month.php'>";
	echo "<TABLE ALIGN=center WIDTH=70%>";
	
	echo '<TR><TD WIDTH=30%>' . _('Close').' '._('Month') . ':</TD>
		<TD WIDTH=30%><SELECT Name="ClosePeriod">';
		
	$sql = 'SELECT lastdate_in_period FROM periods WHERE lastdate_in_period >= "'.$_SESSION['ProhibitPostingsBefore'].'" ORDER BY periodno DESC';	
	$ErrMsg = _('Could not load periods table');
	$result = DB_query($sql,$db,$ErrMsg);
	while ($PeriodRow = DB_fetch_row($result)){
		if ($_SESSION['ProhibitPostingsBefore']==$PeriodRow[0]){
			echo  '<OPTION SELECTED value="' . $PeriodRow[0] . '">' . ConvertSQLDate($PeriodRow[0]);
		} else {
			echo  '<OPTION value="' . $PeriodRow[0] . '">' . ConvertSQLDate($PeriodRow[0]);
		}
	}
	echo '</SELECT></TD><TD WIDTH=40%>' . _('This allows all periods before the selected date to be locked from postings. All postings for transactions dated prior to this date will be posted in the period following this date.') . '</TD></TR>';
	echo "<TR><TD COLSPAN=3><INPUT TYPE=submit NAME='submit' VALUE='"._('Send')."'></TD></TR>";
	echo "</TABLE></FORM>";
}
include('includes/footer.inc');

?>