<?php
/**
 * REALHOST 2008
 * $LastChangedDate$
 * $Rev$
 */
/* $Revision: 14 $ */
$PageSecurity = 8;

include ('includes/session.inc');
$title = _('Profit and Loss');
include('includes/SQL_CommonFunctions.inc');
include('includes/AccountSectionsDef.inc'); // This loads the $Sections variable
$_POST['ToPeriod'] = $_POST['FromPeriod'];
if ($_POST['FromPeriod'] > $_POST['ToPeriod']){
	prnMsg(_('The selected period from is actually after the period to') . '! ' . _('Please reselect the reporting period'),'error');
	$_POST['SelectADifferentPeriod']='Select A Different Period';
}

if ((! isset($_POST['FromPeriod']) AND ! isset($_POST['ToPeriod'])) OR isset($_POST['SelectADifferentPeriod'])){

	include('includes/header.inc');
	echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
	
	if (Date('m') > $_SESSION['YearEnd']){
		/*Dates in SQL format */
		$DefaultFromDate = Date ('Y-m-d', Mktime(0,0,0,$_SESSION['YearEnd'] + 2,0,Date('Y')));
	} else {
		$DefaultFromDate = Date ('Y-m-d', Mktime(0,0,0,$_SESSION['YearEnd'] + 2,0,Date('Y')-1));
	}

	/*Show a form to allow input of criteria for profit and loss to show */
	echo '<CENTER><TABLE><TR><TD>'._('Select Period From').":</TD><TD><SELECT Name='FromPeriod'>";

	$sql = 'SELECT periodno, lastdate_in_period FROM periods ORDER BY periodno DESC';
	$Periods = DB_query($sql,$db);


	while ($myrow=DB_fetch_array($Periods,$db)){
		if(isset($_POST['FromPeriod']) AND $_POST['FromPeriod']!=''){
			if( $_POST['FromPeriod']== $myrow['periodno']){
				echo '<OPTION SELECTED VALUE=' . $myrow['periodno'] . '>' .MonthAndYearFromSQLDate($myrow['lastdate_in_period']);
			} else {
				echo '<OPTION VALUE=' . $myrow['periodno'] . '>' . MonthAndYearFromSQLDate($myrow['lastdate_in_period']);
			}
		} else {
			if($myrow['lastdate_in_period']==$DefaultFromDate){
				echo '<OPTION SELECTED VALUE=' . $myrow['periodno'] . '>' . MonthAndYearFromSQLDate($myrow['lastdate_in_period']);
			} else {
				echo '<OPTION VALUE=' . $myrow['periodno'] . '>' . MonthAndYearFromSQLDate($myrow['lastdate_in_period']);
			}
		}
	}

	echo '</SELECT></TD></TR>';
	if (!isset($_POST['ToPeriod']) OR $_POST['ToPeriod']==''){
		$sql = 'SELECT MAX(periodno) FROM periods';
		$MaxPrd = DB_query($sql,$db);
		$MaxPrdrow = DB_fetch_row($MaxPrd);

		$DefaultToPeriod = (int) ($MaxPrdrow[0]-1);
	} else {
		$DefaultToPeriod = $_POST['ToPeriod'];
	}
/*
	echo '<TR><TD>' . _('Select Period To') . ":</TD><TD><SELECT Name='ToPeriod'>";

	$RetResult = DB_data_seek($Periods,0);

	while ($myrow=DB_fetch_array($Periods,$db)){

		if($myrow['periodno']==$DefaultToPeriod){
			echo '<OPTION SELECTED VALUE=' . $myrow['periodno'] . '>' . MonthAndYearFromSQLDate($myrow['lastdate_in_period']);
		} else {
			echo '<OPTION VALUE =' . $myrow['periodno'] . '>' . MonthAndYearFromSQLDate($myrow['lastdate_in_period']);
		}
	}
	echo '</SELECT></TD></TR>';
*/
	/*
	echo '<TR><TD>'._('Detail Or Summary').":</TD><TD><SELECT Name='Detail'>";
		echo "<OPTION SELECTED VALUE='Summary'>"._('Summary');
		echo "<OPTION SELECTED VALUE='Detailed'>"._('All Accounts');
	echo '</SELECT></TD></TR>';
	*/
	echo "<INPUT TYPE=HIDDEN NAME=Detail VALUE='Summary'>";
	echo '</TABLE>';

	echo "<INPUT TYPE=SUBMIT Name='ShowPL' Value='"._('Show Statement of Profit and Loss')."'></CENTER>";
	echo "<CENTER><INPUT TYPE=SUBMIT Name='PrintPDF' Value='"._('PrintPDF')."'></CENTER>";

	/*Now do the posting while the user is thinking about the period to select */

	include ('includes/GLPostings.inc');

} else if (isset($_POST['PrintPDF'])) {
	
	include('includes/PDFStarter.php');
	$PageNumber = 0;
	$FontSize = 10;
	$pdf->addinfo('Title', _('Estado de Perdidad y Ganancias') );
	$pdf->addinfo('Subject', _('Estado de Perdidas y Ganancias') );
	$line_height = 12;
	$_POST['ToPeriod'] = $_POST['FromPeriod'];
	$NumberOfMonths = $_POST['ToPeriod'] - $_POST['FromPeriod'] + 1;

	if ($NumberOfMonths > 12){
		include('includes/header.inc');
		echo '<P>';
		prnMsg(_('A period up to 12 months in duration can be specified') . ' - ' . _('the system automatically shows a comparative for the same period from the previous year') . ' - ' . _('it cannot do this if a period of more than 12 months is specified') . '. ' . _('Please select an alternative period range'),'error');
		include('includes/footer.inc');
		exit;
	}

	
	
	$sql = 'SELECT lastdate_in_period FROM periods WHERE periodno=' . $_POST['ToPeriod'];
	$PrdResult = DB_query($sql, $db);
	$myrow = DB_fetch_row($PrdResult);
	$PeriodToDate = MonthAndYearFromSQLDate($myrow[0]);

	$sql = 'SELECT periodno FROM periods WHERE YEAR("'.$myrow[0].'")=YEAR(lastdate_in_period) ORDER BY lastdate_in_period ASC LIMIT 1';
	$PrdResult2 = DB_query($sql, $db);
	$yearperiod = DB_fetch_row($PrdResult2);

	$SQL = 'SELECT accountgroups.sectioninaccounts,
			accountgroups.parentgroupname,
			accountgroups.groupname,
			chartdetails.accountcode,
			chartmaster.accountname,
			Sum(CASE WHEN chartdetails.period>=' . ($yearperiod[0]) . ' AND chartdetails.period<='.($_POST['ToPeriod']).' THEN chartdetails.actual ELSE 0 END) AS firstprdbfwd,
			Sum(CASE WHEN chartdetails.period=' . $_POST['FromPeriod'] . ' THEN chartdetails.bfwdbudget ELSE 0 END) AS firstprdbudgetbfwd,
			Sum(CASE WHEN chartdetails.period=' . $_POST['FromPeriod'] . ' THEN chartdetails.actual ELSE 0 END) AS lastprdcfwd,
			
			Sum(CASE WHEN chartdetails.period>=' . ($yearperiod[0]-12) . ' AND chartdetails.period<='.($_POST['ToPeriod']-12).' THEN chartdetails.actual ELSE 0 END) AS lyfirstprdbfwd,
			Sum(CASE WHEN chartdetails.period=' . ($_POST['FromPeriod']-12) . ' THEN chartdetails.actual ELSE 0 END) AS lylastprdcfwd,
			
			Sum(CASE WHEN chartdetails.period>=' . ($yearperiod[0]-1) . ' AND chartdetails.period<='.($_POST['ToPeriod']-1).' THEN chartdetails.actual ELSE 0 END) AS lmfirstprdbfwd,
			Sum(CASE WHEN chartdetails.period=' . ($_POST['FromPeriod']-1) . ' THEN chartdetails.actual ELSE 0 END) AS lmlastprdcfwd,
			
			Sum(CASE WHEN chartdetails.period=' . $_POST['FromPeriod'] . ' THEN chartdetails.bfwdbudget + chartdetails.budget ELSE 0 END) AS lastprdbudgetcfwd
		FROM chartmaster INNER JOIN accountgroups
		ON chartmaster.group_ = accountgroups.groupname INNER JOIN chartdetails
		ON chartmaster.accountcode= chartdetails.accountcode
		WHERE accountgroups.pandl=1
		GROUP BY accountgroups.sectioninaccounts,
			accountgroups.parentgroupname,
			accountgroups.groupname,
			chartdetails.accountcode,
			chartmaster.accountname,
			accountgroups.sequenceintb
		ORDER BY accountgroups.sectioninaccounts,
			accountgroups.sequenceintb,
			accountgroups.groupname,
			accountgroups.sequenceintb,
			chartdetails.accountcode';


	$AccountsResult = DB_query($SQL,$db);
	if (DB_error_no($db) != 0) {
		$title = _('Profit and Loss') . ' - ' . _('Problem Report') . '....';
		include('includes/header.inc');
		prnMsg( _('No general ledger accounts were returned by the SQL because') . ' - ' . DB_error_msg($db) );
		echo '<BR><A HREF="' .$rootpath .'/index.php?' . SID . '">'. _('Back to the menu'). '</A>';
		if ($debug == 1){
			echo '<BR>'. $SQL;
		}
		include('includes/footer.inc');
		exit;
	}
	
	include('includes/PDFProfitAndLossPageHeader.inc');

	$Section = '';
	$SectionPrdActual = 0;
	$SectionPrdLY = 0;
	$SectionPrdBudget = 0;

	$ActGrp = '';
	$ParentGroups = array();
	$Level = 0;
	$ParentGroups[$Level]='';
	$GrpPrdActual = array(0);
	$GrpPrdLY = array(0);
	$GrpPrdBudget = array(0);

	while ($myrow = DB_fetch_array($AccountsResult)){

		// Print heading if at end of page
		if ($YPos < ($Bottom_Margin)){
			include('includes/PDFProfitAndLossPageHeader.inc');
		}
		
		if ($myrow['groupname']!= $ActGrp){
			if ($myrow['parentgroupname']!=$ActGrp AND $ActGrp!=''){
					while ($myrow['groupname']!=$ParentGroups[$Level] AND $Level>0) {
					if ($_POST['Detail']=='Detailed'){
							$ActGrpLabel = $ParentGroups[$Level] . ' ' . _('total');
						} else {
							$ActGrpLabel = $ParentGroups[$Level];
						}
						if ($Section ==$Seccion_Ventas){ /*Income */	
							$LeftOvers = $pdf->addTextWrap($Left_Margin +60,$YPos,200 -($Level*10),$FontSize,$ActGrpLabel); //($Level*10)
							$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format(-$GrpPrdActual[$Level]),'right');
							$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format(-$GrpPrdBudget[$Level]),'right');
							$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format(-$GrpPrdLY[$Level]),'right');
							$YPos -= (2 * $line_height);
						} else { /*Costs */
							$LeftOvers = $pdf->addTextWrap($Left_Margin +60,$YPos,200 -($Level*10),$FontSize,$ActGrpLabel); //($Level*10)
							$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format($GrpPrdActual[$Level]),'right');
							$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format($GrpPrdBudget[$Level]),'right');
							$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format($GrpPrdLY[$Level]),'right');
							$YPos -= (2 * $line_height);
						}
						$GrpPrdLY[$Level] = 0;
						$GrpPrdActual[$Level] = 0;
						$GrpPrdBudget[$Level] = 0;
						$ParentGroups[$Level] ='';
						$Level--;
// Print heading if at end of page
						if ($YPos < ($Bottom_Margin + (2*$line_height))){
							include('includes/PDFProfitAndLossPageHeader.inc');
						}
					} //end of loop  
					//still need to print out the group total for the same level
					if ($_POST['Detail'] == 'Detailed'){
						$ActGrpLabel = $ParentGroups[$Level] . ' ' . _('total');
					} else {
						$ActGrpLabel = $ParentGroups[$Level];
					}
					// bowikaxu - changed section
					if ($Section == $Seccion_Ventas){ /*Income */	
						$LeftOvers = $pdf->addTextWrap($Left_Margin + ($Level*10),$YPos,200 -($Level*10),$FontSize,$ActGrpLabel); //($Level*10)
						$pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format(-$GrpPrdActual[$Level]),'right');
						$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format(-$GrpPrdBudget[$Level]),'right');
						$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format(-$GrpPrdLY[$Level]),'right');
						$YPos -= (2 * $line_height);
					} else if ($Section == '21'){ /* Pasivo Circulante */
						$LeftOvers = $pdf->addTextWrap($Left_Margin + ($Level*10),$YPos,200 -($Level*10),$FontSize,$ActGrpLabel);
						$pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format(-$GrpPrdActual[$Level]),'right');
						$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format(-$GrpPrdBudget[$Level]),'right');
						$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format(-$GrpPrdLY[$Level]),'right');
						$YPos -= (2 * $line_height);
					}else { /*Costs */
						$LeftOvers = $pdf->addTextWrap($Left_Margin + ($Level*10),$YPos,200 -($Level*10),$FontSize,$ActGrpLabel);//($Level*10)
						$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format($GrpPrdActual[$Level]),'right');
						$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format($GrpPrdBudget[$Level]),'right');
						$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format($GrpPrdLY[$Level]),'right');
						$YPos -= (2 * $line_height);
					}
					$GrpPrdLY[$Level] = 0;
					$GrpPrdActual[$Level] = 0;
					$GrpPrdBudget[$Level] = 0;
					$ParentGroups[$Level] ='';
				}
			}
		
		// Print heading if at end of page
		if ($YPos < ($Bottom_Margin +(2 * $line_height))){
			include('includes/PDFProfitAndLossPageHeader.inc');
		}

		if ($myrow['sectioninaccounts'] != $Section){

			$pdf->selectFont('./fonts/Helvetica-Bold.afm');
			$FontSize =10;
				// bowikaxu changed seccion
				if ($Section == $Seccion_Ventas) { /*Income*/

					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,$Sections[$Section]);
					$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format(-$SectionPrdActual),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+650,$YPos,70,$FontSize,number_format(-$SectionPrdBudget),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format(-$SectionPrdLY),'right');
					$YPos -= (2 * $line_height);
					
					$TotalIncome = -$SectionPrdActual;
					$TotalBudgetIncome = -$SectionPrdBudget;
					$TotalLYIncome = -$SectionPrdLY;
				}else if($Section == $Seccion_Gto_Admin || $Section == $Seccion_Gto_Venta){
					// do nothing, no llevan totales solo sumarlos para su total final
					$TotGtos += $SectionPrdActual;
					$TotBudgetGtos += $SectionPrdBudget;
					$TotLYGtos += $SectionPrdLY;
					
				} else {
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,$Sections[$Section]);
					$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format($SectionPrdActual),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format($SectionPrdBudget),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format($SectionPrdLY),'right');
					$YPos -= (2 * $line_height);
					
					// bowikaxu sum gto total
					if($Section == $Seccion_Gto_Admin || $Section == $Seccion_Gto_Venta){
						$TotalGto += -$SectionPrdActual;
						$TotalBudgetGto += -$SectionPrdBudget;
						$TotalLYGto += -$SectionPrdLY;
					}
				}
				if ($Section == $Seccion_Cto_Ventas){ /*Cost of Sales - need sub total for Gross Profit*/
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,_('Utilidad (-Perdida) Bruta'));
					$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format($TotalIncome - $SectionPrdActual),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format($TotalBudgetIncome - $SectionPrdBudget),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format($TotalLYIncome - $SectionPrdLY),'right');
					$pdf->line($Left_Margin+290, $YPos+$line_height,$Left_Margin+500, $YPos+$line_height);
					$pdf->line($Left_Margin+290, $YPos,$Left_Margin+500, $YPos);
					$YPos -= (2 * $line_height);

					$UtBruta = $TotalIncome - $SectionPrdActual;
					$UtBrutaBudget = $TotalBudgetIncome - $SectionPrdBudget;
					$UtBrutaLY = $TotalLYIncome - $SectionPrdLY;
					
					if ($TotalIncome != 0){
						$PrdGPPercent = 100 *($TotalIncome - $SectionPrdActual) / $TotalIncome;
					} else {
						$PrdGPPercent = 0;
					}
					if ($TotalBudgetIncome != 0){
						$BudgetGPPercent = 100 * ($TotalBudgetIncome - $SectionPrdBudget) / $TotalBudgetIncome;
					} else {
						$BudgetGPPercent = 0;
					}
					if ($TotalLYIncome != 0){
						$LYGPPercent = 100 * ($TotalLYIncome - $SectionPrdLY) / $TotalLYIncome;
					} else {
						$LYGPPercent = 0;
					}
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,_('Gross Profit Percent'));
					$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format($PrdGPPercent,1) . '%','right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format($BudgetGPPercent,1) . '%','right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format($LYGPPercent,1). '%','right');
					$YPos -= (2 * $line_height);
				}
				// bowikaxu - utilidad de operacion
				if ($Section == $Seccion_Gto_Venta){ /*Utilidad de Operacion - need sub total for Gross Profit*/
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,'Gastos de Operacion');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format($TotGtos),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format($TotBudgetGtos),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format($TotLYGtos),'right');
					$YPos -= (2 * $line_height);
					
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,'Utilidad (-Perdida) de Operacion');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format($UtBruta - $TotGtos),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format($UtBrutaBudget - $TotBudgetGtos),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format($UtBrutaLY - $TotLYGtos),'right');
					$pdf->line($Left_Margin+290, $YPos+$line_height,$Left_Margin+500, $YPos+$line_height);
					$pdf->line($Left_Margin+290, $YPos,$Left_Margin+500, $YPos);
					$YPos -= (2 * $line_height);

					if ($UtBruta != 0){
						$PrdGPPercent = abs(100 *($UtBruta - $SectionPrdActual) / $TotalIncome);
					} else {
						$PrdGPPercent = 0;
					}
					if ($UtBrutaBudget != 0){
						$BudgetGPPercent = abs(100 * ($UtBrutaBudget - $SectionPrdBudget) / $TotalBudgetIncome);
					} else {
						$BudgetGPPercent = 0;
					}
					if ($UtBrutaLY != 0){
						$LYGPPercent = abs(100 * ($UtBrutaLY - $SectionPrdLY) / $TotalLYIncome);
					} else {
						$LYGPPercent = 0;
					}
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,'Porcentaje de Utilidad de Operacion');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format($PrdGPPercent,1) . '%','right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format($BudgetGPPercent,1) . '%','right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format($LYGPPercent,1). '%','right');
					$YPos -= (2 * $line_height);
				}
				// bowikaxu end - procentaje de utilidad de operacion
				
			$SectionPrdLY = 0;
			$SectionPrdActual = 0;
			$SectionPrdBudget = 0;

			$Section = $myrow['sectioninaccounts'];

			if ($_POST['Detail'] == 'Detailed'){
				$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,$Sections[$myrow['sectioninaccounts']]);
				$YPos -= (2 * $line_height);
			}
			$FontSize =8;
			$pdf->selectFont('./fonts/Helvetica.afm');
		}

		if ($myrow['groupname'] != $ActGrp){
			if ($myrow['parentgroupname']==$ActGrp AND $ActGrp !=''){ //adding another level of nesting
					$Level++;
			}
			$ParentGroups[$Level] = $myrow['groupname'];
			$ActGrp = $myrow['groupname'];
			if ($_POST['Detail'] == 'Detailed'){
				$FontSize =10;
				$pdf->selectFont('./fonts/Helvetica-Bold.afm');
				$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,$myrow['groupname']);
				$YPos -= (2 * $line_height);
				$FontSize =8;
				$pdf->selectFont('./fonts/Helvetica.afm');
			}
		}

		$AccountPeriodActual = $myrow['lastprdcfwd'];
		$AccountPeriodLY = $myrow['lylastprdcfwd'];
		// bowikaxu realhost March 2008 - show last month not period budget
		$AccountPeriodBudget = $myrow['lmlastprdcfwd'];
		
		$PeriodProfitLoss += $AccountPeriodActual;
		$PeriodBudgetProfitLoss += $AccountPeriodBudget;
		$PeriodLYProfitLoss += $AccountPeriodLY;

		for ($i=0;$i<=$Level;$i++){
			$GrpPrdLY[$i] +=$AccountPeriodLY;
			$GrpPrdActual[$i] +=$AccountPeriodActual;
			$GrpPrdBudget[$i] +=$AccountPeriodBudget;
		}
		

		$SectionPrdLY +=$AccountPeriodLY;
		$SectionPrdActual +=$AccountPeriodActual;
		$SectionPrdBudget +=$AccountPeriodBudget;

		if ($_POST['Detail'] == _('Detailed')) {
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,$myrow['accountcode']);
			$LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,190,$FontSize,$myrow['accountname']);
			// bowikaxu changed section
			if ($Section == $Seccion_Ventas) { /*Income*/
				$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format(-$AccountPeriodActual),'right');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format(-$AccountPeriodBudget),'right');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format(-$AccountPeriodLY),'right');
			} else {
				$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format($AccountPeriodActual),'right');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format($AccountPeriodBudget),'right');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format($AccountPeriodLY),'right');
			}
			$YPos -= $line_height;
		}
		if($Show_UtBruta){
			$Show_UtBruta = 0;
		}
	}
	//end of loop

	if ($myrow['groupname']!= $ActGrp){
		if ($myrow['parentgroupname']!=$ActGrp AND $ActGrp!=''){
			while ($myrow['groupname']!=$ParentGroups[$Level] AND $Level>0) {
				if ($_POST['Detail']=='Detailed'){
					$ActGrpLabel = $ParentGroups[$Level] . ' ' . _('total');
				} else {
					$ActGrpLabel = $ParentGroups[$Level];
				}
				// bowikaxu changed section
				if ($Section ==1){ /*Income */	
					$LeftOvers = $pdf->addTextWrap($Left_Margin +($Level*10),$YPos,200 -($Level*10),$FontSize,$ActGrpLabel);
					$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format(-$GrpPrdActual[$Level]),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format(-$GrpPrdBudget[$Level]),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format(-$GrpPrdLY[$Level]),'right');
					$YPos -= (2 * $line_height);
				} else { /*Costs */
					$LeftOvers = $pdf->addTextWrap($Left_Margin +($Level*10),$YPos,200 -($Level*10),$FontSize,$ActGrpLabel);
					$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format($GrpPrdActual[$Level]),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format($GrpPrdBudget[$Level]),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format($GrpPrdLY[$Level]),'right');
					$YPos -= (2 * $line_height);
				}
				$GrpPrdLY[$Level] = 0;
				$GrpPrdActual[$Level] = 0;
				$GrpPrdBudget[$Level] = 0;
				$ParentGroups[$Level] ='';
				$Level--;
				// Print heading if at end of page
				if ($YPos < ($Bottom_Margin + (2*$line_height))){
					include('includes/PDFProfitAndLossPageHeader.inc');
				}
			} 
			//still need to print out the group total for the same level
			if ($_POST['Detail'] == 'Detailed'){
				$ActGrpLabel = $ParentGroups[$Level] . ' ' . _('total');
			} else {
				$ActGrpLabel = $ParentGroups[$Level];
			}
			// bowikaxu changed section
			if ($Section == $Seccion_Ventas){ /*Income */	
				$LeftOvers = $pdf->addTextWrap($Left_Margin +($Level*10),$YPos,200 -($Level*10),$FontSize,$ActGrpLabel); 
				$pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format(-$GrpPrdActual[$Level]),'right');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format(-$GrpPrdBudget[$Level]),'right');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format(-$GrpPrdLY[$Level]),'right');
				$YPos -= (2 * $line_height);
			} else { /*Costs */
				$LeftOvers = $pdf->addTextWrap($Left_Margin +($Level*10),$YPos,200 -($Level*10),$FontSize,$ActGrpLabel);
				$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format($GrpPrdActual[$Level]),'right');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format($GrpPrdBudget[$Level]),'right');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format($GrpPrdLY[$Level]),'right');
				$YPos -= (2 * $line_height);
			}
			$GrpPrdLY[$Level] = 0;
			$GrpPrdActual[$Level] = 0;
			$GrpPrdBudget[$Level] = 0;
			$ParentGroups[$Level] ='';
		}
	}
	// Print heading if at end of page
	if ($YPos < ($Bottom_Margin + (2*$line_height))){
		include('includes/PDFProfitAndLossPageHeader.inc');
	}
	if ($Section != ''){

		$pdf->selectFont('./fonts/Helvetica-Bold.afm');
		$pdf->line($Left_Margin+290, $YPos+10,$Left_Margin+500, $YPos+10);
		$pdf->line($Left_Margin+290, $YPos,$Left_Margin+500, $YPos);
		// bowikaxu changed section
		if ($Section == $Seccion_Ventas) { /*Income*/
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,$Sections[$Section]);
			$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format(-$SectionPrdActual),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format(-$SectionPrdBudget),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format(-$SectionPrdLY),'right');
			$YPos -= (2 * $line_height);
			
			$TotalIncome = -$SectionPrdActual;
			$TotalBudgetIncome = -$SectionPrdBudget;
			$TotalLYIncome = -$SectionPrdLY;
		} else {
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,$Sections[$Section]);
			$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format($SectionPrdActual),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format($SectionPrdBudget),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format($SectionPrdLY),'right');
			$YPos -= (2 * $line_height);
		}
		// bowikaxu changed section
		if ($Section == $Seccion_Cto_Ventas){ /*Cost of Sales - need sub total for Gross Profit*/
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,_('Gross Profitd'));
			$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format($TotalIncome - $SectionPrdActual),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format($TotalBudgetIncome - $SectionPrdBudget),'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format($TotalLYIncome - $SectionPrdLY),'right');
			$YPos -= (2 * $line_height);
			
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,_('Gross Profit Percent'));
			$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format(100*($TotalIncome - $SectionPrdActual)/$TotalIncome,1) . '%','right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format(100*($TotalBudgetIncome - $SectionPrdBudget)/$TotalBudgetIncome,1) . '%','right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format(100*($TotalLYIncome - $SectionPrdLY)/$TotalLYIncome,1). '%','right');
			$YPos -= (2 * $line_height);
		}
		$SectionPrdLY =0;
		$SectionPrdActual =0;
		$SectionPrdBudget =0;
		$Section = $myrow['sectioninaccounts'];
	}

	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize,_('Profit').' - '._('Loss'));
	$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,70,$FontSize,number_format(-$PeriodProfitLoss),'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,70,$FontSize,number_format(-$PeriodBudgetProfitLoss),'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+430,$YPos,70,$FontSize,number_format(-$PeriodLYProfitLoss),'right');
	
	$pdf->line($Left_Margin+290, $YPos+$line_height,$Left_Margin+500, $YPos+$line_height);
	$pdf->line($Left_Margin+290, $YPos,$Left_Margin+500, $YPos);	
	
	$pdfcode = $pdf->output();
	$len = strlen($pdfcode);
	
	if ($len <= 20){
		$title = _('Print Profit and Loss Error');
		include('includes/header.inc');
		echo '<p>';
		prnMsg( _('There were no entries to print out for the selections specified') );
		echo '<BR><A HREF="'. $rootpath.'/index.php?' . SID . '">'. _('Back to the menu'). '</A>';
		include('includes/footer.inc');
		exit;
	} else {
		header('Content-type: application/pdf');
		header('Content-Length: ' . $len);
		header('Content-Disposition: inline; filename=Profit_Loss.pdf');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		$pdf->Stream();
	}
	exit;
	
} else {

	$Show_UtBruta = 0;
	$Show_UtOperacion = 0;
	$Show_UtImpuestos = 0;
	
	// bowikaxu realhost - 24 june 2008 - BUG FIX print ut operacion
	$Print_UtOper = 1;
	
	include('includes/header.inc');
	echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
	echo "<INPUT TYPE=HIDDEN NAME='FromPeriod' VALUE=" . $_POST['FromPeriod'] . "><INPUT TYPE=HIDDEN NAME='ToPeriod' VALUE=" . $_POST['ToPeriod'] . '>';

	$NumberOfMonths = $_POST['ToPeriod'] - $_POST['FromPeriod'] + 1;
	
	if ($NumberOfMonths >12){
		echo '<P>';
		prnMsg(_('A period up to 12 months in duration can be specified') . ' - ' . _('the system automatically shows a comparative for the same period from the previous year') . ' - ' . _('it cannot do this if a period of more than 12 months is specified') . '. ' . _('Please select an alternative period range'),'error');
		include('includes/footer.inc');
		exit;
	}

	$sql = 'SELECT lastdate_in_period FROM periods WHERE periodno=' . $_POST['ToPeriod'];
	$PrdResult = DB_query($sql, $db);
	$myrow = DB_fetch_row($PrdResult);
	$PeriodToDate = MonthAndYearFromSQLDate($myrow[0]);
	
	$sql = 'SELECT periodno FROM periods WHERE YEAR("'.$myrow[0].'")=YEAR(lastdate_in_period) ORDER BY lastdate_in_period ASC LIMIT 1';
	$PrdResult2 = DB_query($sql, $db);
	$yearperiod = DB_fetch_row($PrdResult2);

	// bowikaxu realhost March 2008 - get last period not period budget
	/*
	 * $SQL = 'SELECT accountgroups.sectioninaccounts,
			accountgroups.parentgroupname,
			accountgroups.groupname,
			chartdetails.accountcode,
			chartmaster.accountname,
			Sum(CASE WHEN chartdetails.period=' . $_POST['FromPeriod'] . ' THEN chartdetails.bfwd ELSE 0 END) AS firstprdbfwd,
			Sum(CASE WHEN chartdetails.period=' . $_POST['FromPeriod'] . ' THEN chartdetails.bfwdbudget ELSE 0 END) AS firstprdbudgetbfwd,
			Sum(CASE WHEN chartdetails.period=' . $_POST['ToPeriod'] . ' THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS lastprdcfwd,
			Sum(CASE WHEN chartdetails.period=' . ($_POST['FromPeriod'] - 12) . ' THEN chartdetails.bfwd ELSE 0 END) AS lyfirstprdbfwd,
			Sum(CASE WHEN chartdetails.period=' . ($_POST['ToPeriod']-12) . ' THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS lylastprdcfwd,
			
			Sum(CASE WHEN chartdetails.period=' . ($_POST['FromPeriod'] - 1) . ' THEN chartdetails.bfwd ELSE 0 END) AS lmfirstprdbfwd,
			Sum(CASE WHEN chartdetails.period=' . ($_POST['ToPeriod']-1) . ' THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS lmlastprdcfwd,
			
			Sum(CASE WHEN chartdetails.period=' . $_POST['ToPeriod'] . ' THEN chartdetails.bfwdbudget + chartdetails.budget ELSE 0 END) AS lastprdbudgetcfwd
		FROM chartmaster INNER JOIN accountgroups
		ON chartmaster.group_ = accountgroups.groupname INNER JOIN chartdetails
		ON chartmaster.accountcode= chartdetails.accountcode
		WHERE accountgroups.pandl=1
		GROUP BY accountgroups.sectioninaccounts,
			accountgroups.parentgroupname,
			accountgroups.groupname,
			chartdetails.accountcode,
			chartmaster.accountname,
			accountgroups.sequenceintb
		ORDER BY accountgroups.sectioninaccounts,
			accountgroups.sequenceintb,
			accountgroups.groupname,
			accountgroups.sequenceintb,
			chartdetails.accountcode';
	 * 
	 */

	$SQL = 'SELECT accountgroups.sectioninaccounts,
			accountgroups.parentgroupname,
			accountgroups.groupname,
			chartdetails.accountcode,
			chartmaster.accountname,
			Sum(CASE WHEN chartdetails.period>=' . ($yearperiod[0]) . ' AND chartdetails.period<='.($_POST['ToPeriod']).' THEN chartdetails.actual ELSE 0 END) AS firstprdbfwd,
			Sum(CASE WHEN chartdetails.period=' . $_POST['FromPeriod'] . ' THEN chartdetails.bfwdbudget ELSE 0 END) AS firstprdbudgetbfwd,
			Sum(CASE WHEN chartdetails.period=' . $_POST['FromPeriod'] . ' THEN chartdetails.actual ELSE 0 END) AS lastprdcfwd,
			
			Sum(CASE WHEN chartdetails.period>=' . ($yearperiod[0]-12) . ' AND chartdetails.period<='.($_POST['ToPeriod']-12).' THEN chartdetails.actual ELSE 0 END) AS lyfirstprdbfwd,
			Sum(CASE WHEN chartdetails.period=' . ($_POST['FromPeriod']-12) . ' THEN chartdetails.actual ELSE 0 END) AS lylastprdcfwd,
			
			Sum(CASE WHEN chartdetails.period>=' . ($yearperiod[0]-1) . ' AND chartdetails.period<='.($_POST['ToPeriod']-1).' THEN chartdetails.actual ELSE 0 END) AS lmfirstprdbfwd,
			Sum(CASE WHEN chartdetails.period=' . ($_POST['FromPeriod']-1) . ' THEN chartdetails.actual ELSE 0 END) AS lmlastprdcfwd,
			
			Sum(CASE WHEN chartdetails.period=' . $_POST['FromPeriod'] . ' THEN chartdetails.bfwdbudget + chartdetails.budget ELSE 0 END) AS lastprdbudgetcfwd
		FROM chartmaster INNER JOIN accountgroups
		ON chartmaster.group_ = accountgroups.groupname INNER JOIN chartdetails
		ON chartmaster.accountcode= chartdetails.accountcode
		WHERE accountgroups.pandl=1
		GROUP BY accountgroups.sectioninaccounts,
			accountgroups.parentgroupname,
			accountgroups.groupname,
			chartdetails.accountcode,
			chartmaster.accountname,
			accountgroups.sequenceintb
		ORDER BY accountgroups.sectioninaccounts,
			accountgroups.sequenceintb,
			accountgroups.groupname,
			accountgroups.sequenceintb,
			chartdetails.accountcode';
	//echo $SQL."<BR>";
	$AccountsResult = DB_query($SQL,$db,_('No general ledger accounts were returned by the SQL because'),_('The SQL that failed was'));

	// bowikaxu realhost Feb 2008 - show company name
	echo "<CENTER><FONT SIZE=4><B>".$_SESSION['CompanyRecord']['coyname']."</B></FONT></CENTER>";
	//echo '<CENTER><FONT SIZE=4 COLOR=BLUE><B>' . _('Statement of Profit and Loss for the'). ' ' . $NumberOfMonths . ' ' . _('months to'). ' ' . $PeriodToDate . '</B></FONT><BR>';
	echo '<CENTER><FONT SIZE=4 COLOR=BLUE><B>' . _('Estado de Resultados para el periodo comprendido hasta '). ' ' . $PeriodToDate . '</B></FONT><BR>';

	/*show a table of the accounts info returned by the SQL
	Account Code ,   Account Name , Month Actual, Month Budget, Period Actual, Period Budget */

	echo '<TABLE CELLPADDING=3 CELLSPACING=3>'; //  BORDER=1
	
	// actual
	$sql = "SELECT lastdate_in_period
			FROM periods
			WHERE periodno = ".$_POST['FromPeriod']."";

	$per_result = DB_query($sql,$db);
	$periods = DB_fetch_array($per_result);
	
	$ActualText = MonthAndYearFromSQLDate($periods['lastdate_in_period']);
	// last month
	$sql = "SELECT lastdate_in_period
			FROM periods
			WHERE periodno = ".($_POST['FromPeriod']-1)."";

	$per_result = DB_query($sql,$db);
	$periods = DB_fetch_array($per_result);
	
	$LastMonthText = MonthAndYearFromSQLDate($periods['lastdate_in_period']);
	// last year
	$sql = "SELECT lastdate_in_period
			FROM periods
			WHERE periodno = ".($_POST['FromPeriod']-12)."";

	$per_result = DB_query($sql,$db);
	$periods = DB_fetch_array($per_result);
	
	$LastYearText = MonthAndYearFromSQLDate($periods['lastdate_in_period']);
	
	if ($_POST['Detail']=='Detailed'){
		$TableHeader = "<TR>
				<TD class='tableheader'>"._('Account')."</TD>
				<TD class='tableheader'>"._('Account Name')."</TD>
				<TABLE>
				<TR>
				<TD COLSPAN=2 class='tableheader'>".$ActualText."</TD>
				<TD COLSPAN=2 class='tableheader'>".$LastMonthText.' '. _('Month')."</TD>
				<TD COLSPAN=2 class='tableheader'>".$LastYearText.'</TD>
				</TR>
				</TABLE>
				</TR>';
	} else { /*summary */
		// bowikaxu reahost 2008 - show credit and debit columns title
		$TableHeader = "<TR>
				<TD COLSPAN=2></TD>
				<TD COLSPAN=3 ALIGN=center class='tableheader'><B>"._('Monthly')."</B></TD>
				<TD COLSPAN=2 ALIGN=center class='tableheader'><B>"._('Acumulated')."</B></TD>
				</TR><TR>
				<TD COLSPAN=2 class='tableheader'>"._('Account Name')."</TD>
				<TD class='tableheader'>".$ActualText."</TD>
				<TD class='tableheader'>".$LastMonthText."</TD>
				<TD class='tableheader'>".$LastYearText."</TD>
				
				<TD class='tableheader'>".$ActualText."</TD>
				<TD class='tableheader'>".$LastYearText."</TD>
				</TR>
				";
	}
	
	$j = 1;
	$k=0; //row colour counter
	$Section='';
	$SectionPrdActual= 0;
	$SectionPrdLY 	 = 0;
	$SectionPrdBudget= 0;

	$ActGrp ='';
	$ParentGroups = array();
	$Level = 0;
	$ParentGroups[$Level]='';
	$GrpPrdActual = array(0);
	$GrpPrdLY = array(0);
	$GrpPrdBudget = array(0);
	
	// bowikaxu forwards
	$FwdGrpPrdLY = array(0);
	$FwdGrpPrdActual = array(0);

	echo $TableHeader;
	
	while ($myrow=DB_fetch_array($AccountsResult)){

		if ($myrow['groupname']!= $ActGrp){
			if ($myrow['parentgroupname']!=$ActGrp AND $ActGrp!=''){
					while ($myrow['groupname']!=$ParentGroups[$Level] AND $Level>0) {
					if ($_POST['Detail']=='Detailed'){
						echo '<TR>
							<TD COLSPAN=2></TD>
							<TD COLSPAN=6><HR></TD>
						</TR>';
						$ActGrpLabel = str_repeat('___',$Level) . $ParentGroups[$Level] . ' ' . _('total');
					} else {
						$ActGrpLabel = str_repeat('___',$Level) . $ParentGroups[$Level];
					}
					// bowikaxu changed section
				if ($Section ==$Seccion_Ventas){ /*Income */
						printf('<TR>
							<TD COLSPAN=2><FONT SIZE=2><I>%s</I></FONT></td>
							<TD ALIGN=RIGHT>%s</TD>
							<TD ALIGN=RIGHT>%s</TD>
							<TD ALIGN=RIGHT>%s</TD>
							</TR>',
							$ActGrpLabel,
							number_format(-$GrpPrdActual[$Level]),
							number_format(-$GrpPrdBudget[$Level]),
							number_format(-$GrpPrdLY[$Level]));
					} else { /*Costs */
						printf('<TR>
							<TD COLSPAN=2><FONT SIZE=2><I>%s</I></FONT></td>
							<TD ALIGN=RIGHT>%s</TD>
							<TD ALIGN=RIGHT>%s</TD>
							<TD ALIGN=RIGHT>%s</TD>
							</TR>',
							$ActGrpLabel,
							number_format($GrpPrdActual[$Level]),
							number_format($GrpPrdBudget[$Level]),
							number_format($GrpPrdLY[$Level]));
					}
					$GrpPrdLY[$Level] = 0;
					$GrpPrdActual[$Level] = 0;
					$GrpPrdBudget[$Level] = 0;
					$ParentGroups[$Level] ='';
					$Level--;
				}//end while
				//still need to print out the old group totals
				if ($_POST['Detail']=='Detailed'){
						echo '<TR>
							<TD COLSPAN=2></TD>
							<TD COLSPAN=6><HR></TD>
						</TR>';
						$ActGrpLabel = str_repeat('___',$Level) . $ParentGroups[$Level] . ' ' . _('total');
					} else {
						$ActGrpLabel = str_repeat('___',$Level) . $ParentGroups[$Level];
					}
// bowikaxu changed section
				if ($Section == $Seccion_Ventas){ /*Income */
					printf('<TR>
						<TD COLSPAN=2><FONT SIZE=2><I>%s </I></FONT></td>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						</TR>',
						$ActGrpLabel,
						number_format(-$GrpPrdActual[$Level]),
						number_format(-$GrpPrdBudget[$Level]),
						number_format(-$GrpPrdLY[$Level]),
						number_format(-$FwdGrpPrdActual[$Level]),
						number_format(-$FwdGrpPrdLY[$Level]));
				} else if ($Section == '21'){ /* Pasivo Circulante */
					printf('<TR>
						<TD COLSPAN=2><FONT SIZE=2><I>%s </I></FONT></td>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						</TR>',
						$ActGrpLabel,
						number_format(-$GrpPrdActual[$Level]),
						number_format(-$GrpPrdBudget[$Level]),
						number_format(-$GrpPrdLY[$Level]),
						number_format(-$FwdGrpPrdActual[$Level]),
						number_format(-$FwdGrpPrdLY[$Level]));
				} else { /* ??? */ 
				
					printf('<TR>
						<TD COLSPAN=2><FONT SIZE=2><I>%s </I></FONT></td>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						</TR>',
						$ActGrpLabel,
						number_format($GrpPrdActual[$Level]),
						number_format($GrpPrdBudget[$Level]),
						number_format($GrpPrdLY[$Level]),
						number_format($FwdGrpPrdActual[$Level]),
						number_format($FwdGrpPrdLY[$Level]));
						
				}
				
				$GrpPrdLY[$Level] = 0;
				$GrpPrdActual[$Level] = 0;
				$GrpPrdBudget[$Level] = 0;
				$ParentGroups[$Level] ='';
				// bowikaxu forward
				$FwdGrpPrdActual[$Level]=0;
				$FwdGrpPrdLY[$Level]=0;
			}
			$j++;
		}

		if ($myrow['sectioninaccounts']!= $Section){

			//if ($SectionPrdLY+$SectionPrdActual+$SectionPrdBudget !=0){
				// bowikaxu changed section
				if ($Section==$Seccion_Ventas) { /*Income*/
/*
					echo '<TR>
						<TD COLSPAN=3></TD>
      						<TD><HR></TD>
						<TD></TD>
						<TD><HR></TD>
						<TD></TD>
						<TD><HR></TD>
					</TR>';
*/
					printf('<TR>
					<TD COLSPAN=2><FONT SIZE=3>%s</FONT></td>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					</TR>',
					$Sections[$Section].' '.'Netas',
					number_format(-$SectionPrdActual),
					number_format(-$SectionPrdBudget),
					number_format(-$SectionPrdLY),
					number_format(-$FwdSectionPrdActual),
					number_format(-$FwdSectionPrdLY));
					
					$TotalIncome = -$SectionPrdActual;
					$TotalBudgetIncome = -$SectionPrdBudget;
					$TotalLYIncome = -$SectionPrdLY;
					
					// bowikaxu - bfwd totals
					$FwdTotalIncome = -$FwdSectionPrdActual;
					$FwdTotalLYIncome = -$FwdSectionPrdLY;
					
					echo "<TR><TD COLSPAN=7><HR></TD></TR>";
					
				}else if($Section == $Seccion_Gto_Admin || $Section == $Seccion_Gto_Venta){
					// do nothing, no llevan totales solo sumarlos para su total final
					$TotGtos += $SectionPrdActual;
					$TotBudgetGtos += $SectionPrdBudget;
					$TotLYGtos += $SectionPrdLY;
					$FwdTotGtos += $FwdSectionPrdActual;
					$FwdTotLYGtos += $FwdSectionPrdLY;
					
					
				}else {
					
					echo '<TR>
					<TD COLSPAN=7><HR></TD>
					</TR>';
					
					printf('<TR>
					<TD COLSPAN=2><FONT SIZE=3>%s</FONT></td>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					</TR>',
					$Sections[$Section],
					number_format($SectionPrdActual),
					number_format($SectionPrdBudget),
					number_format($SectionPrdLY),
					number_format($FwdSectionPrdActual),
					number_format($FwdSectionPrdLY));
					
					// bowikaxu realhost Feb 2008 - sum utilidad de operacion
					if($Section == $Seccion_Gto_Venta){
						$TotalGto += -$SectionPrdActual;
						$TotalBudgetGto += -$SectionPrdBudget;
						$TotalLYGto += -$SectionPrdLY;
						
						// bowikaxu - bfwd totals
						$FwdTotalGto = $FwdSectionPrdActual;
						$FwdTotalLYGto = $FwdSectionPrdLY;
						
						/*
						DEBUG
						printf('<TR>
					<TD COLSPAN=2><FONT SIZE=3>%s</FONT></td>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					</TR>',
					'TEMPORALES',
					number_format($TotalGto),
					number_format($TotalBudgetGto),
					number_format($TotalLYGto));
					*/
					}
				}
				// bowikaxu changed section 2
				if ($Section==$Seccion_Cto_Ventas){ /*Cost of Sales - need sub total for Gross Profit*/
					/*
					echo '<TR>
						<TD COLSPAN=2></TD>
						<TD COLSPAN=6><HR></TD>
					</TR>';
					*/
					printf('<TR>
						<TD COLSPAN=2><FONT SIZE=3>'.'Utilidad (-Perdida) Bruta'.'</FONT></td>
						<TD ALIGN=RIGHT><B>%s</B></TD>
						<TD ALIGN=RIGHT><B>%s</B></TD>
						<TD ALIGN=RIGHT><B>%s</B></TD>
						<TD ALIGN=RIGHT><B>%s</B></TD>
						<TD ALIGN=RIGHT><B>%s</B></TD>
						</TR>',
					number_format($TotalIncome - $SectionPrdActual),
					number_format($TotalBudgetIncome - $SectionPrdBudget),
					number_format($TotalLYIncome - $SectionPrdLY),
					number_format($FwdTotalIncome - $FwdSectionPrdActual),
					number_format($FwdTotalLYIncome - $FwdSectionPrdLY));
					
					$UtBruta = $TotalIncome - $SectionPrdActual;
					$UtBrutaBudget = $TotalBudgetIncome - $SectionPrdBudget;
					$UtBrutaLY = $TotalLYIncome - $SectionPrdLY;
					
					$FwdUtBruta = $FwdTotalIncome - $FwdSectionPrdActual;
					$FwdBrutaLY = $FwdTotalLYIncome - $FwdSectionPrdLY;

					if ($TotalIncome !=0){
						$PrdGPPercent = 100*($TotalIncome - $SectionPrdActual)/$TotalIncome;
					} else {
						$PrdGPPercent =0;
					}
					if ($TotalBudgetIncome !=0){
						$BudgetGPPercent = 100*($TotalBudgetIncome - $SectionPrdBudget)/$TotalBudgetIncome;
					} else {
						$BudgetGPPercent =0;
					}
					if ($TotalLYIncome !=0){
						$LYGPPercent = 100*($TotalLYIncome - $SectionPrdLY)/$TotalLYIncome;
					} else {
						$LYGPPercent = 0;
					}
					if ($FwdTotalLYIncome !=0){
						$FwdLYGPPercent = 100*($FwdTotalLYIncome - $FwdSectionPrdLY)/$FwdTotalLYIncome;
					} else {
						$FwdLYGPPercent = 0;
					}
					if ($FwdTotalIncome !=0){
						$FwdPrdGPPercent = 100*($FwdTotalIncome - $FwdSectionPrdActual)/$FwdTotalIncome;
					} else {
						$FwdPrdGPPercent =0;
					}
					echo '<TR>
						<TD COLSPAN=7><HR></TD>
					</TR>';
					printf('<TR>
						<TD COLSPAN=2><FONT SIZE=2><I>'._('Gross Profit Percent').'</I></FONT></td>
						<TD ALIGN=RIGHT><I>%s</I></TD>
						<TD ALIGN=RIGHT><I>%s</I></TD>
						<TD ALIGN=RIGHT><I>%s</I></TD>
						<TD ALIGN=RIGHT><I>%s</I></TD>
						<TD ALIGN=RIGHT><I>%s</I></TD>
						</TR><TR><TD COLSPAN=7><HR></TD></TR>',
						number_format($PrdGPPercent,1) . '%',
						number_format($BudgetGPPercent,1) . '%',
						number_format($LYGPPercent,1). '%',
						number_format($FwdPrdGPPercent,1) . '%',
						number_format($FwdLYGPPercent,1) . '%');
					$j++;
				}
				
				// bowikaxu realhost Feb 2008 - Utilidad de Operacion
				
				if ($Section==$Seccion_Gto_Venta && $Print_UtOper==1){ /*Gasto de Venta - need sub total for Gross Profit*/
					$Print_UtOper = 0;
					//debe de ir primero el total de Gastos de Operacion
					printf('<TR>
					<TD COLSPAN=2><FONT SIZE=3>%s</FONT></td>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					<TD ALIGN=RIGHT><B>%s</B></TD>
					</TR>',
					'Gastos de Operacion',
					number_format($TotGtos),
					number_format($TotBudgetGtos),
					number_format($TotLYGtos),
					number_format($FwdTotGtos),
					number_format($FwdTotLYGtos));
					
					echo '<TR>
						<TD COLSPAN=7><HR></TD>
					</TR>';
					printf('<TR>
						<TD COLSPAN=2><FONT SIZE=3>'.'Utilidad (-Perdida) de Operacion'.'</FONT></td>
						<TD ALIGN=RIGHT><B>%s</B></TD>
						<TD ALIGN=RIGHT><B>%s</B></TD>
						<TD ALIGN=RIGHT><B>%s</B></TD>
						<TD ALIGN=RIGHT><B>%s</B></TD>
						<TD ALIGN=RIGHT><B>%s</B></TD>
						</TR>',
					number_format($UtBruta - $TotGtos),
					number_format($UtBrutaBudget - $TotBudgetGtos),
					number_format($UtBrutaLY - $TotLYGtos),
					number_format($FwdUtBruta - $FwdTotGtos),
					number_format($FwdUtBrutaLY - $FwdTotLYGtos));
					/*
					number_format($TotalGto - $SectionPrdActual),
					number_format($TotalBudgetGto - $SectionPrdBudget),
					number_format($TotalLYGto - $SectionPrdLY));
					*/

					if ($UtBruta !=0){
						$PrdGPPercent = abs(100*($UtBruta - $SectionPrdActual)/$TotalIncome);
					} else {
						$PrdGPPercent =0;
					}
					if ($UtBrutaBudget !=0){
						$BudgetGPPercent = abs(100*($UtBrutaBudget - $SectionPrdBudget)/$TotalBudgetIncome);
					} else {
						$BudgetGPPercent =0;
					}
					if ($UtBrutaLY !=0){
						$LYGPPercent = abs(100*($UtBrutaLY - $SectionPrdLY)/$TotalLYIncome);
					} else {
						$LYGPPercent =0;
					}
					if ($FwdUtBruta !=0){
						$FwdPrdGPPercent = abs(100*($FwdUtBruta - $FwdSectionPrdActual)/$FwdTotalIncome);
					} else {
						$FwdPrdGPPercent =0;
					}
					if ($FwdUtBrutaLY !=0){
						$FwdLYGPPercent = abs(100*($FwdUtBrutaLY - $FwdSectionPrdLY)/$FwdTotalLYIncome);
					} else {
						$FwdLYGPPercent =0;
					}
					echo '<TR>
						<TD COLSPAN=7><HR></TD>
					</TR>';
					printf('<TR>
						<TD COLSPAN=2><FONT SIZE=2><I>'.'Porcentaje de Utilidad (-Perdida) de Operacion'.'</I></FONT></td>
						<TD ALIGN=RIGHT><I>%s</I></TD>
						<TD ALIGN=RIGHT><I>%s</I></TD>
						<TD ALIGN=RIGHT><I>%s</I></TD>
						<TD ALIGN=RIGHT><I>%s</I></TD>
						<TD ALIGN=RIGHT><I>%s</I></TD>
						</TR><TR><TD COLSPAN=7><HR></TD></TR>',
						number_format($PrdGPPercent,1) . '%',
						number_format($BudgetGPPercent,1) . '%',
						number_format($LYGPPercent,1). '%',
						number_format($FwdPrdGPPercent,1) . '%',
						number_format($FwdLYGPPercent,1) . '%');
					$j++;
				}
			//}
			$SectionPrdLY =0;
			$SectionPrdActual =0;
			$SectionPrdBudget =0;
			
			$FwdSectionPrdActual =0;
			$FwdSectionPrdLY=0;

			$Section = $myrow['sectioninaccounts'];
			
			if ($_POST['Detail']=='Detailed'){
				printf('<TR>
					<td COLSPAN=3><FONT SIZE=3 COLOR=BLUE><B>%s</B></FONT></TD>
					</TR>',
					$Sections[$myrow['sectioninaccounts']]);
			}
			$j++;
		}

		if ($myrow['groupname']!= $ActGrp){

			if ($myrow['parentgroupname']==$ActGrp AND $ActGrp !=''){ //adding another level of nesting
				$Level++;
			}
			
			$ParentGroups[$Level] = $myrow['groupname'];
			$ActGrp = $myrow['groupname'];
			if ($_POST['Detail']=='Detailed'){
				printf('<TR>
					<td COLSPAN=3><FONT SIZE=2 COLOR=BLUE><B>%s</B></FONT></TD>
					</TR>',
					$myrow['groupname']);
					echo $TableHeader;
			}else {
				//echo $TableHeader;
			}
		}
		// - $myrow['firstprdbfwd']
		$AccountPeriodActual = $myrow['lastprdcfwd'];
		//- $myrow['lyfirstprdbfwd']
		$AccountPeriodLY = $myrow['lylastprdcfwd'];
		// bowikaxu realhost March 2008 - show last month not period budget
		//$AccountPeriodBudget = $myrow['lastprdbudgetcfwd'] - $myrow['firstprdbudgetbfwd'];
		// - $myrow['lmfirstprdbfwd'];
		$AccountPeriodBudget = $myrow['lmlastprdcfwd'];
		
		// bowikaxu - bfwd
		$FwdActual = $myrow['firstprdbfwd'];
		$FwdLY = $myrow['lyfirstprdbfwd'];
		
		$PeriodProfitLoss += $AccountPeriodActual;
		$PeriodBudgetProfitLoss += $AccountPeriodBudget;
		$PeriodLYProfitLoss += $AccountPeriodLY;
		
		$FwdPeriodProfitLoss += $FwdActual;
		$FwdPeriodLYProfitLoss += $FwdLY;

		for ($i=0;$i<=$Level;$i++){
			$GrpPrdLY[$i] +=$AccountPeriodLY;
			$GrpPrdActual[$i] +=$AccountPeriodActual;
			$GrpPrdBudget[$i] +=$AccountPeriodBudget;
			// bowikaxu forwrds
			$FwdGrpPrdActual[$i] += $FwdActual;
			$FwdGrpPrdLY[$i] += $FwdLY;
		}
		$SectionPrdLY +=$AccountPeriodLY;
		$SectionPrdActual +=$AccountPeriodActual;
		$SectionPrdBudget +=$AccountPeriodBudget;
		
		// bowikaxu forwards
		$FwdSectionPrdLY += $FwdLY;
		$FwdSectionPrdActual += $FwdActual;

		if ($_POST['Detail']=='Detailed'){

			if ($k==1){
				echo "<tr bgcolor='#CCCCCC'>";
				$k=0;
			} else {
				echo "<tr bgcolor='#EEEEEE'>";
				$k++;
			}

			$ActEnquiryURL = "<A HREF='$rootpath/GLAccountInquiry.php?" . SID . '&Period=' . $_POST['ToPeriod'] . '&Account=' . $myrow['accountcode'] . "&Show=Yes'>" . $myrow['accountcode'] . '<A>';
// bowikaxu changed section
			if ($Section ==$Seccion_Ventas){
				 printf('<td>%s</td>
					<td>%s</td>
					<td ALIGN=RIGHT>%s</td>
					<td ALIGN=RIGHT>%s</td>
					<td ALIGN=RIGHT>%s</td>
					<td ALIGN=RIGHT>%s</td>
					<td ALIGN=RIGHT>%s</td>
					</tr>',
					$ActEnquiryURL,
					$myrow['accountname'],
					number_format(-$AccountPeriodActual),
					number_format(-$AccountPeriodBudget),
					number_format(-$AccountPeriodLY));
			} else {
				printf('<td>%s</td>
					<td>%s</td>
					<td ALIGN=RIGHT>%s</td>
					<td ALIGN=RIGHT>%s</td>
					<td ALIGN=RIGHT>%s</td>
					<td ALIGN=RIGHT>%s</td>
					<td ALIGN=RIGHT>%s</td>
					</tr>',
					$ActEnquiryURL,
					$myrow['accountname'],
					number_format($AccountPeriodActual),
					number_format($AccountPeriodBudget),
					number_format($AccountPeriodLY));
			}

			$j++;
		}
		
		// bowikaxu realhost Feb 2008 - Show Utilidad Bruta
		if($Show_UtBruta){
			$Show_UtBruta = 0;
			
		}
		
	}
	//end of loop

	if ($myrow['groupname']!= $ActGrp){
		if ($myrow['parentgroupname']!=$ActGrp AND $ActGrp!=''){
			while ($myrow['groupname']!=$ParentGroups[$Level] AND $Level>0) {
				if ($_POST['Detail']=='Detailed'){
					echo '<TR>
						<TD COLSPAN=7><HR></TD>
					</TR>';
					$ActGrpLabel = str_repeat('___',$Level) . $ParentGroups[$Level] . ' ' . _('total');
				} else {
					$ActGrpLabel = str_repeat('___',$Level) . $ParentGroups[$Level];
				}
				if ($Section ==1){ /*Income */
					printf('<TR>
						<TD COLSPAN=2><FONT SIZE=2><I>%s </I></FONT></td>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						</TR>',
						$ActGrpLabel,
						number_format(-$GrpPrdActual[$Level]),
						number_format(-$GrpPrdBudget[$Level]),
						number_format(-$GrpPrdLY[$Level]));
				} else { /*Costs */
					printf('<TR>
						<TD COLSPAN=2><FONT SIZE=2><I>%s </I></FONT></td>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>
						</TR>',
						$ActGrpLabel,
						number_format($GrpPrdActual[$Level]),
						number_format($GrpPrdBudget[$Level]),
						number_format($GrpPrdLY[$Level]));
				}
				$GrpPrdLY[$Level] = 0;
				$GrpPrdActual[$Level] = 0;
				$GrpPrdBudget[$Level] = 0;
				$ParentGroups[$Level] ='';
				
				$FwdGrpPrdActual[$Level]=0;
				$FwdGrpPrdLY[$Level]=0;
				
				$Level--;
			}//end while
			//still need to print out the old group totals
			if ($_POST['Detail']=='Detailed'){
					echo '<TR>
						<TD COLSPAN=7><HR></TD>
					</TR>';
					$ActGrpLabel = str_repeat('___',$Level) . $ParentGroups[$Level] . ' ' . _('total');
				} else {
					$ActGrpLabel = str_repeat('___',$Level) . $ParentGroups[$Level];
				}
// bowikaxu changed section
			if ($Section ==$Seccion_Ventas){ /*Income */
				printf('<TR>
					<TD COLSPAN=2><FONT SIZE=2><I>%s </I></FONT></td>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					</TR>',
					$ActGrpLabel,
					number_format(-$GrpPrdActual[$Level]),
					number_format(-$GrpPrdBudget[$Level]),
					number_format(-$GrpPrdLY[$Level]),
					number_format(-$FwdGrpPrdActual[$Level]),
					number_format(-$FwdGrpPrdLY[$Level]));
			} else { /*Costs */
				printf('<TR>
					<TD COLSPAN=2><FONT SIZE=2><I>%s </I></FONT></td>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					</TR>',
					$ActGrpLabel,
					number_format($GrpPrdActual[$Level]),
					number_format($GrpPrdBudget[$Level]),
					number_format($GrpPrdLY[$Level]),
					number_format($FwdGrpPrdActual[$Level]),
					number_format($FwdGrpPrdLY[$Level]));
			}
			$GrpPrdLY[$Level] = 0;
			$GrpPrdActual[$Level] = 0;
			$GrpPrdBudget[$Level] = 0;
			$ParentGroups[$Level] ='';
			
			$FwdGrpPrdActual = 0;
			$FwdGrpPrdLY = 0;
		}
		$j++;
	}

	if ($myrow['sectioninaccounts']!= $Section){
// bowikaxu changed section
		if ($Section==$Seccion_Ventas) { /*Income*/

			echo '<TR>
						<TD COLSPAN=7><HR></TD>
					</TR>';

			printf('<TR>
			<TD COLSPAN=2><FONT SIZE=3>%s</FONT></td>
			<TD ALIGN=RIGHT>%s</TD>
			<TD ALIGN=RIGHT>%s</TD>
			<TD ALIGN=RIGHT>%s</TD>
			<TD ALIGN=RIGHT>%s</TD>
			<TD ALIGN=RIGHT>%s</TD>
			</TR>',
			$Sections[$Section],
			number_format(-$SectionPrdActual),
			number_format(-$SectionPrdBudget),
			number_format(-$SectionPrdLY),
			number_format(-$FwdSectionPrdActual),
			number_format(-$FwdSectionPrdLY));
			
			$TotalIncome = -$SectionPrdActual;
			$TotalBudgetIncome = -$SectionPrdBudget;
			$TotalLYIncome = -$SectionPrdLY;
			
			$FwdTotalIncome = -$FwdSectionPrdActual;
			$FwdTotalLYIncome = -$FwdSectionPrdLY;
			
		} else {
			echo '<TR>
						<TD COLSPAN=7><HR></TD>
					</TR>';
			printf('<TR>
			<TD COLSPAN=2><FONT SIZE=3>%s</FONT></td>
			<TD ALIGN=RIGHT>%s</TD>
			<TD ALIGN=RIGHT>%s</TD>
			<TD ALIGN=RIGHT>%s</TD>
			<TD ALIGN=RIGHT>%s</TD>
			<TD ALIGN=RIGHT>%s</TD>
			</TR>',
			$Sections[$Section],
			number_format($SectionPrdActual),
			number_format($SectionPrdBudget),
			number_format($SectionPrdLY),
			number_format($FwdSectionPrdActual),
			number_format($FwdSectionPrdLY));
		}
		// bowikaxu changed section
		
		if ($Section==$Seccion_Cto_Ventas){
			echo '<TR>
						<TD COLSPAN=7><HR></TD>
					</TR>';
			printf('<TR>
				<TD COLSPAN=2><FONT SIZE=10>'.'Utilidad (-Perdida) Bruta'.'</FONT></td>
				<TD ALIGN=RIGHT>%s</TD>
				<TD ALIGN=RIGHT>%s</TD>
				<TD ALIGN=RIGHT>%s</TD>
				<TD ALIGN=RIGHT>%s</TD>
				<TD ALIGN=RIGHT>%s</TD>
				</TR>',
			number_format($TotalIncome - $SectionPrdActual),
			number_format($TotalBudgetIncome - $SectionPrdBudget),
			number_format($TotalLYIncome - $SectionPrdLY),
			number_format($FwdTotalIncome - $FwdSectionPrdActual),
			number_format($FwdTotalLYIncome - $FwdSectionPrdLY));
			
			if ($TotalIncome !=0){
				$PrdGPPercent = 100*($TotalIncome - $SectionPrdActual)/$TotalIncome;
			} else {
				$PrdGPPercent =0;
			}
			if ($TotalBudgetIncome !=0){
				$BudgetGPPercent = 100*($TotalBudgetIncome - $SectionPrdBudget)/$TotalBudgetIncome;
			} else {
				$BudgetGPPercent =0;
			}
			if ($TotalLYIncome !=0){
				$LYGPPercent = 100*($TotalLYIncome - $SectionPrdLY)/$TotalLYIncome;
			} else {
				$LYGPPercent = 0;
			}
			if ($FwdTotalIncome !=0){
				$FwdPrdGPPercent = 100*($FwdTotalIncome - $FwdSectionPrdActual)/$FwdTotalIncome;
			} else {
				$FwdPrdGPPercent =0;
			}
			if ($FwdTotalLYIncome !=0){
				$FwdLYGPPercent = 100*($FwdTotalLYIncome - $FwdSectionPrdLY)/$FwdTotalLYIncome;
			} else {
				$FwdLYGPPercent = 0;
			}
			echo '<TR>
						<TD COLSPAN=7><HR></TD>
					</TR>';
					
			printf('<TR>
				<TD COLSPAN=2><FONT SIZE=2><I>'._('Gross Profit Percent').'</I></FONT></td>
				<TD ALIGN=RIGHT><I>%s</I></TD>
				<TD ALIGN=RIGHT><I>%s</I></TD>
				<TD ALIGN=RIGHT><I>%s</I></TD>
				<TD ALIGN=RIGHT><I>%s</I></TD>
				<TD ALIGN=RIGHT><I>%s</I></TD>
				</TR><TR><TD COLSPAN=6> </TD></TR>',
				number_format($PrdGPPercent,1) . '%',
				number_format($BudgetGPPercent,1) . '%',
				number_format($LYGPPercent,1). '%',
				number_format($FwdPrdGPPercent,1) . '%',
				number_format($FwdLYGPPercent,1) . '%');
			$j++;
		}
		
		$SectionPrdLY =0;
		$SectionPrdActual =0;
		$SectionPrdBudget =0;
		
		$FwdSectionPrdLY=0;
		$FwdSectionPrdActual=0;

		$Section = $myrow['sectioninaccounts'];

		if ($_POST['Detail']=='Detailed'){
			printf('<TR>
				<td COLSPAN=6><FONT SIZE=3 COLOR=BLUE><B>%s</B></FONT></TD>
				</TR>',
				$Sections[$myrow['sectioninaccounts']]);
		}
		$j++;

	}

	echo '<TR>
			<TD COLSPAN=7><HR></TD>
		</TR>';

	printf("<tr bgcolor='#ffffff'>
		<td COLSPAN=2><FONT SIZE=3 COLOR=BLUE><B>"._('Profit').' (- '._('Loss').")</B></FONT></td>
		<td ALIGN=RIGHT>%s</td>
		<td ALIGN=RIGHT>%s</td>
		<td ALIGN=RIGHT>%s</td>
		<td ALIGN=RIGHT>%s</td>
		<td ALIGN=RIGHT>%s</td>
		</tr>",
		number_format(-$PeriodProfitLoss),
		number_format(-$PeriodBudgetProfitLoss),
		number_format(-$PeriodLYProfitLoss),
		number_format(-$FwdPeriodProfitLoss),
		number_format(-$FwdPeriodLYProfitLoss)
		);

	echo '<TR>
						<TD COLSPAN=7><HR></TD>
					</TR>';

	echo '</TABLE>';
	echo "<INPUT TYPE=SUBMIT Name='SelectADifferentPeriod' Value='"._('Select A Different Period')."'></CENTER>";
}
echo '</FORM>';
include('includes/footer.inc');

?>
