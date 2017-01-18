<?php

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-03-15 13:16:28 -0600 (Sat, 15 Mar 2008) $
 * $Rev: 123 $
 */

$PageSecurity = 2;
include('includes/session.inc');
include('includes/class.pdf.php');
include('includes/SQL_CommonFunctions.inc');
if (!isset($_POST['Show'])){

    $title = _('Estado de Posici&oacute;n Financiera');

include('includes/header.inc');

echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';

/*Dates in SQL format for the last day of last month*/
$DefaultPeriodDate = Date ('Y-m-d', Mktime(0,0,0,Date('m'),0,Date('Y')));

/*Show a form to allow input of criteria for TB to show */
echo '<CENTER><TABLE>
        <TR>
        <TD COLSPAN=2 ALIGN=CENTER><B>'._('Estado de Posici&oacute;n Financiera').'</TD>
        </TR>';    
echo '<TR>
         <TD>'._('Period').':</TD>
         <TD><SELECT Name=Period>';
     $sql = 'SELECT periodno, lastdate_in_period FROM periods ORDER BY periodno DESC';
     $Periods = DB_query($sql,$db);
         $id=0;
         while ($myrow=DB_fetch_array($Periods,$db)){

            if($myrow['periodno'] == $_POST['Period']){
              echo '<OPTION SELECTED VALUE=' . $myrow['periodno'] . '>' . _(MonthAndYearFromSQLDate($myrow['lastdate_in_period']));
            $id++;
            } else {
              echo '<OPTION VALUE=' . $myrow['periodno'] . '>' . _(MonthAndYearFromSQLDate($myrow['lastdate_in_period']));
            }

         }
         echo "</SELECT></TD>
        </TR>";
/*
echo '<TR>
         <TD>'._('To').':</TD>
         <TD><SELECT Name=LastPeriod>';
     $sql = 'SELECT periodno, lastdate_in_period FROM periods ORDER BY periodno DESC';
     $Periods = DB_query($sql,$db);
         $id=0;
         while ($myrow=DB_fetch_array($Periods,$db)){

            if($myrow['periodno'] == $_POST['LastPeriod']){
              echo '<OPTION SELECTED VALUE=' . $myrow['periodno'] . '>' . _(MonthAndYearFromSQLDate($myrow['lastdate_in_period']));
            $id++;
            } else {
              echo '<OPTION VALUE=' . $myrow['periodno'] . '>' . _(MonthAndYearFromSQLDate($myrow['lastdate_in_period']));
            }

         }
         echo "</SELECT></TD>
        </TR>";         
         */
         
echo "</TABLE><P>
<INPUT TYPE=SUBMIT NAME='Show' VALUE='"._('PDF')."'>
<INPUT TYPE=SUBMIT NAME='Excell' VALUE='"._('Show Account Transactions')."'>
</CENTER></FORM>";

// bowikaxu realhost March 2008 - do gl postings
/*Now do the posting while the user is thinking about the period to select */
	include ('includes/GLPostings.inc');

if(!isset($_POST['Excell'])){
    include('includes/footer.inc');
}

}
if(isset($_POST['Show'])){

    /*Set specifically for the stationery being used -needs to be modified for clients own
    packing slip 2 part stationery is recommended so storeman can note differences on and
    a copy retained */

    $Page_Width=612; // horizontal
    $Page_Height=792; // vertical
    $Top_Margin=10;
    $Bottom_Margin=20;
    $Left_Margin=10;
    $Right_Margin=10;
    
    $line_height = 11;
    $PeriodTotal = 0;
    $PeriodNo = -9999;
    $DebitTotal = 0;
    $CreditTotal = 0;
    $TotFinal = 0;
    $TotCtes = 0;
    
	// bowikaxu realhost March 2008 - cuenta para el ajuste
	$RetainedEarningsAct = $_SESSION['CompanyRecord']['retainedearnings'];
	
    //$YPos -= ($line_height);
    
    if (isset($_POST['Period'])){
        $SelectedPeriod = $_POST['Period'];
    } elseif (isset($_GET['Period'])){
        $SelectedPeriod = $_GET['Period'];
    }
    
    $LastPeriod = $_POST['Period'];
    
    $ACTIVO = 1; // ACTIVO
    $PASIVO = 2; // PASIVO
    $CAPITAL = 3; // CAPITAL

    if (!isset($SelectedPeriod) OR !isset($LastPeriod)){
        prnMsg(_('A period or range of periods must be selected from the list box'),'info');
        include('includes/footer.inc');
        exit;
    }

    $sql = "SELECT lastdate_in_period FROM periods WHERE periodno = ".$SelectedPeriod."";
    $dates = DB_fetch_array(DB_query($sql,$db));
    $Fecha2 = $dates['lastdate_in_period'];
    $Fecha = MonthAndYearFromSQLDate($dates['lastdate_in_period']);
    $De = $Fecha;
    
    $LastSelectedPeriod = $SelectedPeriod - 12;
    $sql = "SELECT lastdate_in_period FROM periods WHERE periodno = ".($LastSelectedPeriod)."";
    $res = DB_query($sql,$db);
    if(DB_num_rows($res)>0){
        $dates = DB_fetch_array($res);
        $Fecha2 = $dates['lastdate_in_period'];
        $Fecha = MonthAndYearFromSQLDate($dates['lastdate_in_period']);
        $LastYear = $Fecha;
    }else {
        $LastSelectedPeriod = $SelectedPeriod;
        $LastYear = $De;
    }
    
    $PageSize = array(0,0,$Page_Width,$Page_Height);
    $pdf = & new Cpdf($PageSize);
    $FontSize=12;
    $pdf->selectFont('./fonts/Helvetica.afm');
    $pdf->addinfo('Author','webERP ' . $Version);
    $pdf->addinfo('Creator','webERP http://www.weberp.org - R&OS PHP-PDF http://www.ros.co.nz');
    $pdf->addinfo('Title', _('Estado Financiero') );
    $pdf->addinfo('Subject', _('Estado Fincanciero') );

    $line_height=16;

    $PageNumber = 1;
    include('includes/rh_EdoFinanHeader.inc');
    $FontSize = 9;
    
    $TotalActivo =0;
    $TotalActivoLastY = 0;
    $TotalPasivo = 0;
    $TotalPasicoLastY = 0;
    $TotalCapital = 0;
    $TotalICapitalLastY = 0;
    
    // ACTIVO
    $LeftOvers = $pdf->addTextWrap(35,$YPos,50,10,_('ACTIVO'),'left');
    $YPos -= ($line_height);
    
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget,
            chartmaster.group_
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$ACTIVO."
        AND chartdetails.period = ".$SelectedPeriod."
        GROUP BY group_
        ORDER BY accountgroups.sequenceintb"; // ACTIVO
    
    $ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because');
    $TransResult = DB_query($sql,$db,$ErrMsg);
    //$CtasActivo = DB_num_rows($TransResult);
    
    // OBTENER EL TOTAL DEL PERIODO SELECCIONADO
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$ACTIVO."
        AND chartdetails.period = ".$SelectedPeriod."
        GROUP BY accountbase.baseid"; // ACTIVO
    
    $ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because');
    $TotTransResult = DB_query($sql,$db,$ErrMsg);
    $TotCtasActivo = DB_fetch_array($TotTransResult);
    // FIN TOTAL PERIODO SELECCIONADO
    
    // OBTENER EL TOTAL DEL PERIODO - 12
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$ACTIVO."
        AND chartdetails.period = ".$LastSelectedPeriod."
        GROUP BY accountbase.baseid"; // ACTIVO LAST YEAR
    
    $ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because');
    $TotTransResultLastY = DB_query($sql,$db,$ErrMsg);
    $TotCtasActivoLastY = DB_fetch_array($TotTransResultLastY);
    // FIN TOTAL PERIODO - 12 
    
    // validacion
    if($TotCtasActivo['actual']== 0)$TotCtasActivo['actual']=1;
    if($TotCtasActivoLastY['actual']== 0)$TotCtasActivoLastY['actual']=1;
    
    while($myrow = DB_fetch_array($TransResult)){
    
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$ACTIVO."
        AND chartdetails.period = ".($LastSelectedPeriod)."
        AND group_ = '".$myrow['group_']."'
        GROUP BY group_"; // ACTIVO LAST YEAR
    
    $ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because');
    $TransResultLastY = DB_query($sql,$db,$ErrMsg);
    $CtasActivoLastY = DB_num_rows($TransResultLastY);
    $myrowLastY = DB_fetch_array($TransResultLastY);
    
        $TotalActivo += $myrow['actual'];
        $TotalActivoLastY += $myrowLastY['actual'];
        //$RunningTotal = $myrow['amount'];

        $Amount = number_format($myrow['actual'],2);
        $AmountLastY = number_format($myrowLastY['actual'],2);
        
        //$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
        // bowikaxu - Se agrego la columna balance con el valor de $RunningTotal    
            if($SelectedPeriod == $LastPeriod){
	            $LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,'','left');
	            $LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,$myrow['group_'],'left');
	            $LeftOvers = $pdf->addTextWrap(335,$YPos,50,9,$Amount,'right');
	            $LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,number_format(abs($myrow['actual']/$TotCtasActivo['actual'])*100,0),'right');
	            $LeftOvers = $pdf->addTextWrap(435,$YPos,50,9,'--','right');
	            $LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,'--','right');
	            $YPos -= ($line_height);
			}else {
				$LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,'','left');
	            $LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,$myrow['group_'],'left');
	            $LeftOvers = $pdf->addTextWrap(335,$YPos,50,9,$Amount,'right');
	            $LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,number_format(abs($myrow['actual']/$TotCtasActivo['actual'])*100,0),'right');
	            $LeftOvers = $pdf->addTextWrap(435,$YPos,50,9,$AmountLastY,'right');
	            $LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,number_format(abs($myrowLastY['actual']/$TotCtasActivoLastY['actual'])*100,0),'right');
	            $YPos -= ($line_height);
			}
            
            if($YPos <= 20){
                $PageNumber++;
                include('includes/rh_EdoFinanHeader.inc');
            }
    }
            $pdf->line(5,$YPos,690,$YPos);
            $YPos -= ($line_height);
            
			if($SelectedPeriod == $LastPeriod){ 
	            $LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,'Total Activo','left');
	            $LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,'','left');
	            $LeftOvers = $pdf->addTextWrap(335,$YPos,50,9,number_format($TotalActivo,2),'right');
	            $LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,number_format(abs($TotalActivo/$TotCtasActivo['actual'])*100,0),'right');
	            $LeftOvers = $pdf->addTextWrap(435,$YPos,50,9,'--','right');
	            $LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,'--','right');
	            $YPos -= ($line_height);
            }else {
            	$LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,'Total Activo','left');
	            $LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,'','left');
	            $LeftOvers = $pdf->addTextWrap(335,$YPos,50,9,number_format($TotalActivo,2),'right');
	            $LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,number_format(abs($TotalActivo/$TotCtasActivo['actual'])*100,0),'right');
	            $LeftOvers = $pdf->addTextWrap(435,$YPos,50,9,number_format($TotalActivoLastY,2),'right');
	            $LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,number_format(abs($TotalActivoLastY/$TotCtasActivoLastY['actual'])*100,0),'right');
	            $YPos -= ($line_height);
            }
            $pdf->line(5,$YPos,690,$YPos);
            $YPos -= ($line_height);
            
            if($YPos <= 20){
                $PageNumber++;
                include('includes/rh_EdoFinanHeader.inc');
            }
    // FIN CUENTAS ACTIVO
    
    // PASIVO
    $LeftOvers = $pdf->addTextWrap(35,$YPos,50,10,_('PASIVO'),'left');
    $YPos -= ($line_height);
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget,
            chartmaster.group_
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$PASIVO."
        AND chartdetails.period = ".$SelectedPeriod."
        GROUP BY group_
        ORDER BY accountgroups.sequenceintb"; // PASIVO
    
    $ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because');
    $TransResult = DB_query($sql,$db,$ErrMsg);
    //$CtasPasivo = DB_num_rows($TransResult);
    
    // TOTAL PASIVO
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$PASIVO."
        AND chartdetails.period = ".$SelectedPeriod."
        GROUP BY accountbase.baseid"; // PASIVO
    
    $ErrMsg = _('The transactions for account') . ' ' . $PASIVO . ' ' . _('could not be retrieved because');
    $TotTransResult = DB_query($sql,$db,$ErrMsg);
    $TotCtasPasivo = DB_fetch_array($TotTransResult);
    // END TOTAL PASIVO
    // TOTAL PASIVO PERIODO - 12
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$PASIVO."
        AND chartdetails.period = ".$LastSelectedPeriod."
        GROUP BY accountbase.baseid"; // PASIVO
    
    $ErrMsg = _('The transactions for account') . ' ' . $PASIVO . ' ' . _('could not be retrieved because');
    $TotTransResultLastY = DB_query($sql,$db,$ErrMsg);
    $TotCtasPasivoLastY = DB_fetch_array($TotTransResultLastY);
    // END TOTAL PASIVO PERIODO - 12
    
     // validacion
    if($TotCtasPasivo['actual']== 0)$TotCtasPasivo['actual']=1;
    if($TotCtasPasivoLastY['actual']== 0)$TotCtasPasivoLastY['actual']=1;
    
    while($myrow = DB_fetch_array($TransResult)){
    
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$PASIVO."
        AND chartdetails.period = ".($LastSelectedPeriod)."
        AND chartmaster.group_ = '".$myrow['group_']."'
        GROUP BY group_"; // PASIVO LAST YEAR
    
    $ErrMsg = _('The transactions for account') . ' ' . $PASIVO . ' ' . _('could not be retrieved because');
    $TransResultLastY = DB_query($sql,$db,$ErrMsg);
    $CtasPasivoLastY = DB_num_rows($TransResultLastY);
    
    $myrowLastY = DB_fetch_array($TransResultLastY);
    
        $TotalPasivo += $myrow['actual'];
        $TotalPasivoLastY += $myrowLastY['actual'];
        //$RunningTotal = $myrow['amount'];

        $Amount = number_format($myrow['actual'],2);
        $AmountLastY = number_format($myrowLastY['actual'],2);
        
        //$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
        // bowikaxu - Se agrego la columna balance con el valor de $RunningTotal
            if($SelectedPeriod == $LastPeriod){         
	            $LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,'','left');
	            $LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,$myrow['group_'],'left');
	            $LeftOvers = $pdf->addTextWrap(335,$YPos,50,9,$Amount,'right');
	            $LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,number_format(abs($myrow['actual']/$TotCtasPasivo['actual']*100),0),'right');
	            $LeftOvers = $pdf->addTextWrap(435,$YPos,50,9,'--','right');
	            $LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,'--','right');
	            $YPos -= ($line_height);
            }else {
            	$LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,'','left');
	            $LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,$myrow['group_'],'left');
	            $LeftOvers = $pdf->addTextWrap(335,$YPos,50,9,$Amount,'right');
	            $LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,number_format(abs($myrow['actual']/$TotCtasPasivo['actual']*100),0),'right');
	            $LeftOvers = $pdf->addTextWrap(435,$YPos,50,9,$AmountLastY,'right');
	            $LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,number_format(abs($myrowLastY['actual']/$TotCtasPasivoLastY['actual']*100),0),'right');
	            $YPos -= ($line_height);
            }
            if($YPos <= 20){
                $PageNumber++;
                include('includes/rh_EdoFinanHeader.inc');
            }
    }
            $pdf->line(5,$YPos,690,$YPos);
            $YPos -= ($line_height);        
            if($SelectedPeriod == $LastPeriod){  
	            $LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,_('Total Pasivo'),'left');
	            $LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,'','left');
	            $LeftOvers = $pdf->addTextWrap(335,$YPos,50,9,number_format($TotalPasivo,2),'right');
	            $LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,number_format(($TotalPasivo/$TotCtasPasivo['actual'])*100,0),'right');
	            $LeftOvers = $pdf->addTextWrap(435,$YPos,50,9,'--','right');
	            $LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,'--','right');
	            $YPos -= ($line_height);
			}else {
				$LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,_('Total Pasivo'),'left');
	            $LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,'','left');
	            $LeftOvers = $pdf->addTextWrap(335,$YPos,50,9,number_format($TotalPasivo,2),'right');
	            $LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,number_format(($TotalPasivo/$TotCtasPasivo['actual'])*100,0),'right');
	            $LeftOvers = $pdf->addTextWrap(435,$YPos,50,9,number_format($TotalPasivoLastY,2),'right');
	            $LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,number_format(($TotalPasivoLastY/$TotCtasPasivoLastY['actual'])*100,0),'right');
	            $YPos -= ($line_height);
			}
            
            $pdf->line(5,$YPos,690,$YPos);
            $YPos -= ($line_height);
            
            if($YPos <= 20){
                $PageNumber++;
                include('includes/rh_EdoFinanHeader.inc');
            }
    // FIN CUENTAS PASIVO
    
    // CAPITAL
    $LeftOvers = $pdf->addTextWrap(35,$YPos,50,10,_('CAPITAL'),'left');
    $YPos -= ($line_height);
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget,
            chartmaster.group_
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$CAPITAL."
        AND chartdetails.period = ".$SelectedPeriod."
        GROUP BY group_
        ORDER BY accountgroups.sequenceintb"; // CAPITAL
    
    $ErrMsg = _('The transactions for account') . ' ' . $CAPITAL . ' ' . _('could not be retrieved because');
    $TransResult = DB_query($sql,$db,$ErrMsg);
    //$CtasCapital = DB_num_rows($TransResult);
    
    // TOTAL CAPITAL
    // CAPITAL
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$CAPITAL."
        AND chartdetails.period = ".$SelectedPeriod."
        GROUP BY accountbase.baseid"; // CAPITAL
    
    $ErrMsg = _('The transactions for account') . ' ' . $CAPITAL . ' ' . _('could not be retrieved because');
    $TotTransResult = DB_query($sql,$db,$ErrMsg);
    $TotCtasCapital = DB_fetch_array($TotTransResult);
    // END TOTAL CAPITAL
    // TOTAL CAPITAL PERIODO - 12
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$CAPITAL."
        AND chartdetails.period = ".$LastSelectedPeriod."
        GROUP BY accountbase.baseid"; // CAPITAL
    
    $ErrMsg = _('The transactions for account') . ' ' . $CAPITAL . ' ' . _('could not be retrieved because');
    $TotTransResultLastY = DB_query($sql,$db,$ErrMsg);
    $TotCtasCapitalLastY = DB_fetch_array($TotTransResultLastY);
    // TOTAL CAPIAL PERIODO -12
    
     // validacion
    if($TotCtasCapital['actual']== 0)$TotCtasCapital['actual']=1;
    if($TotCtasCapitalLastY['actual']== 0)$TotCtasCapitalLastY['actual']=1;
    
    while($myrow = DB_fetch_array($TransResult)){
    
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$PASIVO."
        AND chartdetails.period = ".($LastSelectedPeriod)."
        GROUP BY group_"; // PASIVO LAST YEAR
    
    $ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because');
    $TransResultLastY = DB_query($sql,$db,$ErrMsg);
    $myrowLastY = DB_fetch_array($TransResultLastY);
    $CtasCapitalLastY = DB_num_rows($TransResultLastY);
    
        $TotalCapital += $myrow['actual'];
        $TotalCapitalLastY += $myrowLastY['actual'];
        //$RunningTotal = $myrow['amount'];
        $Amount = $myrow['actual'];
		$AmountLastY = $myrowLastY['actual'];
        
		 if ($myrow['accountcode'] == $RetainedEarningsAct){
		 	
			/*Calculate B/Fwd retained earnings */

	$SQL = 'SELECT Sum(CASE WHEN chartdetails.period=' . $SelectedPeriod . ' THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS accumprofitbfwd,
			Sum(CASE WHEN chartdetails.period=' . ($LastSelectedPeriod) . " THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS lyaccumprofitbfwd
		FROM chartmaster INNER JOIN accountgroups
		ON chartmaster.group_ = accountgroups.groupname INNER JOIN chartdetails
		ON chartmaster.accountcode= chartdetails.accountcode
		WHERE accountgroups.pandl=1";

	$AccumProfitResult = DB_query($SQL,$db);
	if (DB_error_no($db) !=0) {
		$title = _('Balance Sheet') . ' - ' . _('Problem Report') . '....';
		include('includes/header.inc');
		prnMsg( _('The accumulated profits brought forward could not be calculated by the SQL because') . ' - ' . DB_error_msg($db) );
		echo '<BR><A HREF="' .$rootpath .'/index.php?' . SID . '">'. _('Back to the menu'). '</A>';
		if ($debug==1){
			echo '<BR>'. $SQL;
		}
		include('includes/footer.inc');
		exit;
	}
			$AccumProfitRow = DB_fetch_array($AccumProfitResult); /*should only be one row returned */
			$Amount += $AccumProfitRow['accumprofitbfwd'];
			$AmountLastY += $AccumProfitRow['lyaccumprofitbfwd'];
			$TotalCapital += $AccumProfitRow['accumprofitbfwd'];
        	$TotalCapitalLastY += $AccumProfitRow['lyaccumprofitbfwd'];
		}
		
		//$Amount = number_format($myrow['actual'],2);
        //$AmountLastY = number_format($myrowLastY['actual'],2);
		
        //$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
        // bowikaxu - Se agrego la columna balance con el valor de $RunningTotal
		 if($SelectedPeriod == $LastPeriod){      
            $LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,'','left');    
            $LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,$myrow['group_'],'right');
            $LeftOvers = $pdf->addTextWrap(335,$YPos,50,9,$Amount,'right');
            $LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,number_format(abs($myrow['actual']/$TotCtasCapital['actual'])*100,0),'right');
            $LeftOvers = $pdf->addTextWrap(435,$YPos,50,9,'--','right');
            $LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,'--','right');
            $YPos -= ($line_height);
			}else {
				$LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,'','left');    
	            $LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,$myrow['group_'],'right');
	            $LeftOvers = $pdf->addTextWrap(335,$YPos,50,9,$Amount,'right');
	            $LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,number_format(abs($myrow['actual']/$TotCtasCapital['actual'])*100,0),'right');
	            $LeftOvers = $pdf->addTextWrap(435,$YPos,50,9,$AmountLastY,'right');
	            $LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,number_format(abs($myrowLastY['actual']/$TotCtasCapitalLastY['actual'])*100,0),'right');
	            $YPos -= ($line_height);
			}
            
            if($YPos <= 20){
                $PageNumber++;
                include('includes/rh_EdoFinanHeader.inc');
            }
    }
            if($SelectedPeriod == $LastPeriod){
	            $LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,_('Total Capital'),'left');
	            $LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,'','left');
	            $LeftOvers = $pdf->addTextWrap(335,$YPos,50,9,number_format($TotalCapital,2),'right');
	            $LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,number_format(($TotalCapital/$TotCtasCapital['actual'])*100,0),'right');
	            $LeftOvers = $pdf->addTextWrap(435,$YPos,50,9,'--','right');
	            $LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,'--','right');
	            $YPos -= ($line_height);
			}else {
				 $LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,_('Total Capital'),'left');
	            $LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,'','left');
	            $LeftOvers = $pdf->addTextWrap(335,$YPos,50,9,number_format($TotalCapital,2),'right');
	            $LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,number_format(($TotalCapital/$TotCtasCapital['actual'])*100,0),'right');
	            $LeftOvers = $pdf->addTextWrap(435,$YPos,50,9,number_format($TotalCapitalLastY,2),'right');
	            $LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,number_format(($TotalCapitalLastY/$TotCtasCapitalLastY['actual'])*100,0),'right');
	            $YPos -= ($line_height);
			}
            
            if($YPos <= 20){
                $PageNumber++;
                include('includes/rh_EdoFinanHeader.inc');
            }
    // FIN CUENTAS CAPITAL
    
    // print totals
                    
            $pdf->line(5,$YPos,690,$YPos);
            $YPos -= ($line_height);
            if($SelectedPeriod == $LastPeriod){
	            $LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,_('Total Pasivo mas Capital'),'left');
	            $LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,'','left');
	            $LeftOvers = $pdf->addTextWrap(335,$YPos,50,9,number_format(($TotalPasivo+$TotalCapital),2),'right');
	            $LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,number_format((($TotalPasivo+$TotalCapital)/($TotCtasCapital['actual']+$TotCtasPasivo['actual']))*100,0),'right');
	            $LeftOvers = $pdf->addTextWrap(435,$YPos,50,9,'--','right');
	            $LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,'--','right');
	            $YPos -= ($line_height);
			}else {
				$LeftOvers = $pdf->addTextWrap(35,$YPos,150,9,_('Total Pasivo mas Capital'),'left');
	            $LeftOvers = $pdf->addTextWrap(185,$YPos,150,9,'','left');
	            $LeftOvers = $pdf->addTextWrap(335,$YPos,50,9,number_format(($TotalPasivo+$TotalCapital),2),'right');
	            $LeftOvers = $pdf->addTextWrap(385,$YPos,50,9,number_format((($TotalPasivo+$TotalCapital)/($TotCtasCapital['actual']+$TotCtasPasivo['actual']))*100,0),'right');
	            $LeftOvers = $pdf->addTextWrap(435,$YPos,50,9,number_format(($TotalPasivoLastY+$TotalCapitalLastY),2),'right');
	            $LeftOvers = $pdf->addTextWrap(485,$YPos,50,9,number_format((($TotalPasivoLastY+$TotalCapitalLastY)/($TotCtasPasivoLastY['actual']+$TotCtasCapitalLastY['actual']))*100,0),'right');
	            $YPos -= ($line_height);
			}
            
            $pdf->line(5,$YPos,690,$YPos);
            $YPos -= ($line_height);
            
            if($YPos <= 20){
                $PageNumber++;
                include('includes/rh_EdoFinanHeader.inc');
            }
    
$pdfcode = $pdf->output();
$len = strlen($pdfcode);

if ($len<=20){
    $title = _('Estado Financiero');
    include('includes/header.inc');
    echo '<p>'. _('No habia transacciones para ser mostradas').
        '<BR>'. '<A HREF="' . $rootpath . '/index.php?' . SID . '">' . _('Back to the menu') . '</A>';
    include('includes/footer.inc');
    exit;
} else {
    header('Content-type: application/pdf');
    header('Content-Length: ' . $len);
    header('Content-Disposition: inline; filename=EdoFinan.pdf');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');

    $pdf->Stream();
}        
} /*end if there are order details to show on the order*/
// EXCEL -----------------------------------------------
if(isset($_POST['Excell'])){
    
    if (isset($_POST['Period'])){
        $SelectedPeriod = $_POST['Period'];
    } elseif (isset($_GET['Period'])){
        $SelectedPeriod = $_GET['Period'];
    }
    
    $LastPeriod = $_POST['Period'];
    
    $ACTIVO = 1; // ACTIVO
    $PASIVO = 2; // PASIVO
    $CAPITAL = 3; // CAPITAL

    if (!isset($SelectedPeriod) OR !isset($LastPeriod)){
        prnMsg(_('A period or range of periods must be selected from the list box'),'info');
        include('includes/footer.inc');
        exit;
    }

	$RetainedEarningsAct = $_SESSION['CompanyRecord']['retainedearnings'];

    $sql = "SELECT lastdate_in_period, DAY(lastdate_in_period) as dia FROM periods WHERE periodno = ".$SelectedPeriod."";
    $dates = DB_fetch_array(DB_query($sql,$db));
    $Fecha2 = $dates['lastdate_in_period'];
    $Fecha = MonthAndYearFromSQLDate($dates['lastdate_in_period']);
    $De = $Fecha;
    //
    $LastDay = $dates['dia'];
    //
    $LastSelectedPeriod = $SelectedPeriod - 12;
    $sql = "SELECT lastdate_in_period, DAY(lastdate_in_period) as dia FROM periods WHERE periodno = ".($LastSelectedPeriod)."";
    $res = DB_query($sql,$db);
    if(DB_num_rows($res)>0){
        $dates = DB_fetch_array($res);
        $Fecha2 = $dates['lastdate_in_period'];
        $Fecha = MonthAndYearFromSQLDate($dates['lastdate_in_period']);
        $LastYear = $Fecha;
	//
	$LastDay = $dates['dia'];
	//
    }else {
        $LastSelectedPeriod = $SelectedPeriod;
        $LastYear = $De;
    }
    
    $TotalActivo =0;
    $TotalActivoLastY = 0;
    $TotalPasivo = 0;
    $TotalPasicoLastY = 0;
    $TotalCapital = 0;
    $TotalICapitalLastY = 0;
    
    //<TD class='tableheader' ALIGN=LEFT>"._('Account')."</TD>
    
    $TableHeader = "<TR>
                    <TD class='tableheader' ALIGN=LEFT>"._('Account Group')."</TD>
                    <TD class='tableheader' ALIGN=LEFT>"._($De)."</TD>
                    <TD class='tableheader' ALIGN=RIGHT>%</TD>
                    <TD class='tableheader' ALIGN=RIGHT>"._($LastYear)."</TD>
                    <TD class='tableheader' ALIGN=RIGHT>%</TD>
                    </TR>";
    echo "<CENTER><TABLE>";
/****************************************************************************************************************************
* Jorge Garcia
* 16/Ene/2009 Agregar periodo
****************************************************************************************************************************/
$LastYear2 = explode(" ", $LastYear);
echo "<CENTER><B><H2>".$_SESSION['CompanyRecord']['coyname']."<BR>"._('Estado de Posici&oacute;n Financiera
al ') . $LastDay." de ".$LastYear2[0]." de ".$LastYear2[1]." </H2><TABLE>";
/****************************************************************************************************************************
* Jorge Garcia Fin Modificacion
****************************************************************************************************************************/
    // ACTIVO
    echo "<TR><TD ALIGN=LEFT COLSPAN=5 class='tableheader'><STRONG>"._('ACTIVO')."</STRONG></TD></TR>".$TableHeader;
    // 
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget,
            chartmaster.group_
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$ACTIVO."
        AND chartdetails.period = ".$SelectedPeriod."
        GROUP BY group_
        ORDER BY accountgroups.sequenceintb"; // ACTIVO
    
    $ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because');
    $TransResult = DB_query($sql,$db,$ErrMsg);
    //$CtasActivo = DB_num_rows($TransResult);
    
    
    // OBTENER EL TOTAL DEL PERIODO SELECCIONADO
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$ACTIVO."
        AND chartdetails.period = ".$SelectedPeriod."
        GROUP BY accountbase.baseid"; // ACTIVO
    
    $ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because');
    $TotTransResult = DB_query($sql,$db,$ErrMsg);
    $TotCtasActivo = DB_fetch_array($TotTransResult);
    // FIN TOTAL PERIODO SELECCIONADO
    
    // OBTENER EL TOTAL DEL PERIODO - 12
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$ACTIVO."
        AND chartdetails.period = ".$LastSelectedPeriod."
        GROUP BY accountbase.baseid"; // ACTIVO LAST YEAR
    
    $ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because');
    $TotTransResultLastY = DB_query($sql,$db,$ErrMsg);
    $TotCtasActivoLastY = DB_fetch_array($TotTransResultLastY);
    // FIN TOTAL PERIODO - 12 
    /*
    // bowikaxu - set the div
    $group = '';
    echo "<div id='first'>";
    $i = -1;
    */
    
     // validacion
    if($TotCtasActivo['actual']== 0)$TotCtasActivo['actual']=1;
    if($TotCtasActivoLastY['actual']== 0)$TotCtasActivoLastY['actual']=1;
    
    while($myrow = DB_fetch_array($TransResult)){
    //$i++;
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$ACTIVO."
        AND chartdetails.period = ".($LastSelectedPeriod)."
        AND group_ = '".$myrow['group_']."'
        GROUP BY group_"; // ACTIVO LAST YEAR
    
    $ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because');
    $TransResultLastY = DB_query($sql,$db,$ErrMsg);
    $CtasActivoLastY = DB_num_rows($TransResultLastY);
    $myrowLastY = DB_fetch_array($TransResultLastY);
    
        $TotalActivo += $myrow['actual'];
        $TotalActivoLastY += $myrowLastY['actual'];
        //$RunningTotal = $myrow['amount'];

        $Amount = number_format($myrow['actual'],2);
        $AmountLastY = number_format($myrowLastY['actual'],2);
        
        // bowikaxu - set the div between groups
        /*
        if($group != $myrow['group_']){
            echo '</div>
            <TR><TD COLSPAN=6>
            <a href="javascript:ShowHide(\''.$i.'\')" title="'.$i.'" >Show Hide</a>
            </TD></TR>';
            echo "<div id='".$i."'>";
            $group = $myrow['group_'];
        }
        */
        //$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
        // bowikaxu - Se agrego la columna balance con el valor de $RunningTotal    
            if($SelectedPeriod == $LastPeriod){
            printf("<TR>
                    <TD ALIGN=LEFT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    </TR>",
                    $myrow['group_'],
                    $Amount,
                    number_format(abs($myrow['actual']/$TotCtasActivo['actual'])*100,0),
                    '--',
                    '--');
					}else {
						printf("<TR>
	                    <TD ALIGN=LEFT>%s</TD>
	                    <TD ALIGN=RIGHT>%s</TD>
	                    <TD ALIGN=RIGHT>%s</TD>
	                    <TD ALIGN=RIGHT>%s</TD>
	                    <TD ALIGN=RIGHT>%s</TD>
	                    </TR>",
	                    $myrow['group_'],
	                    $Amount,
	                    number_format(abs($myrow['actual']/$TotCtasActivo['actual'])*100,0),
	                    $AmountLastY,
	                    number_format(abs($myrowLastY['actual']/$TotCtasActivoLastY['actual'])*100,0));
					}
    }
	 if($SelectedPeriod == $LastPeriod){
    	printf("<TR>
                    <TD ALIGN=LEFT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    </TR>",
                    _('Total Activo'),
                    number_format($TotalActivo,2),
                    number_format(($TotalActivo/$TotCtasActivo['actual'])*100,0),
                    '--',
                    '--');
					}else {
						printf("<TR>
                    <TD ALIGN=LEFT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    </TR>",
                    _('Total Activo'),
                    number_format($TotalActivo,2),
                    number_format(($TotalActivo/$TotCtasActivo['actual'])*100,0),
                    number_format($TotalActivoLastY,2),
                    number_format(($TotalActivoLastY/$TotCtasActivoLastY['actual'])*100,0));
					}
    // FIN CUENTAS ACTIVO
    
    // PASIVO
    echo "<TR><TD ALIGN=LEFT COLSPAN=5 class='tableheader'><STRONG>"._('PASIVO')."</STRONG></TD></TR>".$TableHeader;
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget,
            chartmaster.group_
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$PASIVO."
        AND chartdetails.period = ".$SelectedPeriod."
        GROUP BY group_
        ORDER BY accountgroups.sequenceintb"; // PASIVO
    
    $ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because');
    $TransResult = DB_query($sql,$db,$ErrMsg);
    //$CtasPasivo = DB_num_rows($TransResult);
    
    // TOTAL PASIVO
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$PASIVO."
        AND chartdetails.period = ".$SelectedPeriod."
        GROUP BY accountbase.baseid"; // PASIVO
    
    $ErrMsg = _('The transactions for account') . ' ' . $PASIVO . ' ' . _('could not be retrieved because');
    $TotTransResult = DB_query($sql,$db,$ErrMsg);
    $TotCtasPasivo = DB_fetch_array($TotTransResult);
    // END TOTAL PASIVO
    // TOTAL PASIVO PERIODO - 12
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$PASIVO."
        AND chartdetails.period = ".$LastSelectedPeriod."
        GROUP BY accountbase.baseid"; // PASIVO
    
    $ErrMsg = _('The transactions for account') . ' ' . $PASIVO . ' ' . _('could not be retrieved because');
    $TotTransResultLastY = DB_query($sql,$db,$ErrMsg);
    $TotCtasPasivoLastY = DB_fetch_array($TotTransResultLastY);
    // END TOTAL PASIVO PERIODO - 12
    
    // validacion
    if($TotCtasPasivo['actual']== 0)$TotCtasPasivo['actual']=1;
    if($TotCtasPasivoLastY['actual']== 0)$TotCtasPasivoLastY['actual']=1;
    
    while($myrow = DB_fetch_array($TransResult)){
    
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$PASIVO."
        AND chartdetails.period = ".($LastSelectedPeriod)."
        AND chartmaster.group_ = '".$myrow['group_']."'
        GROUP BY group_"; // PASIVO LAST YEAR
    
    $ErrMsg = _('The transactions for account') . ' ' . $PASIVO . ' ' . _('could not be retrieved because');
    $TransResultLastY = DB_query($sql,$db,$ErrMsg);
    $CtasPasivoLastY = DB_num_rows($TransResultLastY);
    
    $myrowLastY = DB_fetch_array($TransResultLastY);
 	$TotalPasivo += $myrow['actual'];
    $TotalPasivoLastY += $myrowLastY['actual'];
    
        $Amount = number_format($myrow['actual'],2);
        $AmountLastY = number_format($myrowLastY['actual'],2);
        
        //$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
        // bowikaxu - Se agrego la columna balance con el valor de $RunningTotal    
            if($SelectedPeriod == $LastPeriod){
            	printf("<TR>
                    <TD ALIGN=LEFT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    </TR>",
                    $myrow['group_'],
                    $Amount,
                    number_format(abs($myrow['actual']/$TotCtasPasivo['actual'])*100,0),
                   '--',
                    '--');
					}else {
						printf("<TR>
                    <TD ALIGN=LEFT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    </TR>",
                    $myrow['group_'],
                    $Amount,
                    number_format(abs($myrow['actual']/$TotCtasPasivo['actual'])*100,0),
                    $AmountLastY,
                    number_format(abs($myrowLastY['actual']/$TotCtasPasivoLastY['actual'])*100,0));
					}
    }
	 if($SelectedPeriod == $LastPeriod){
    	printf("<TR>
                    <TD ALIGN=LEFT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    </TR>",
                    _('Total Pasivo'),
                    number_format($TotalPasivo,2),
                    number_format(($TotalPasivo/$TotCtasPasivo['actual'])*100,0),
                    '--',
                    '--');
					}else {
						printf("<TR>
                    <TD ALIGN=LEFT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    </TR>",
                    _('Total Pasivo'),
                    number_format($TotalPasivo,2),
                    number_format(($TotalPasivo/$TotCtasPasivo['actual'])*100,0),
                    number_format($TotalPasivoLastY,2),
                    number_format(($TotalPasivoLastY/$TotCtasPasivoLastY['actual'])*100,0));
					}
    // FIN CUENTAS PASIVO
    
    // CAPITAL
    echo "<TR><TD ALIGN=LEFT COLSPAN=5 class='tableheader'><STRONG>"._('CAPITAL')."</STRONG></TD></TR>".$TableHeader;
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget,
            chartmaster.group_
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$CAPITAL."
        AND chartdetails.period = ".$SelectedPeriod."
        GROUP BY group_
        ORDER BY accountgroups.sequenceintb"; // CAPITAL
    
    $ErrMsg = _('The transactions for account') . ' ' . $CAPITAL . ' ' . _('could not be retrieved because');
    $TransResult = DB_query($sql,$db,$ErrMsg);
    //$CtasCapital = DB_num_rows($TransResult);
    
    // TOTAL CAPITAL
    // CAPITAL
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$CAPITAL."
        AND chartdetails.period = ".$SelectedPeriod."
        GROUP BY accountbase.baseid"; // CAPITAL
    
    $ErrMsg = _('The transactions for account') . ' ' . $CAPITAL . ' ' . _('could not be retrieved because');
    $TotTransResult = DB_query($sql,$db,$ErrMsg);
    $TotCtasCapital = DB_fetch_array($TotTransResult);
    // END TOTAL CAPITAL
    // TOTAL CAPITAL PERIODO - 12
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$CAPITAL."
        AND chartdetails.period = ".$LastSelectedPeriod."
        GROUP BY accountbase.baseid"; // CAPITAL
    
    $ErrMsg = _('The transactions for account') . ' ' . $CAPITAL . ' ' . _('could not be retrieved because');
    $TotTransResultLastY = DB_query($sql,$db,$ErrMsg);
    $TotCtasCapitalLastY = DB_fetch_array($TotTransResultLastY);
    // TOTAL CAPIAL PERIODO -12
    
    // validacion
    if($TotCtasCapital['actual']== 0)$TotCtasCapital['actual']=1;
    if($TotCtasCapitalLastY['actual']== 0)$TotCtasCapitalLastY['actual']=1;
    
    while($myrow = DB_fetch_array($TransResult)){
    
    $sql = "select chartmaster.*, SUM(chartdetails.actual+chartdetails.bfwd) AS actual, 
            SUM(chartdetails.bfwd) AS bfwd,
            SUM(chartdetails.bfwdbudget) AS bfwdbudget
            FROM chartmaster 
            inner join chartdetails on chartdetails.accountcode = chartmaster.accountcode 
            inner join accountgroups on accountgroups.groupname = chartmaster.group_ 
            inner join accountsection on accountsection.sectionid = accountgroups.sectioninaccounts 
            inner join accountbase on accountbase.baseid = accountsection.baseid
            WHERE accountbase.baseid = ".$PASIVO."
        AND chartdetails.period = ".($LastSelectedPeriod)."
        GROUP BY group_"; // PASIVO LAST YEAR
    
    $ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because');
    $TransResultLastY = DB_query($sql,$db,$ErrMsg);
    $myrowLastY = DB_fetch_array($TransResultLastY);
    $CtasCapitalLastY = DB_num_rows($TransResultLastY);
    
        $TotalCapital += $myrow['actual'];
        $TotalCapitalLastY += $myrowLastY['actual'];
        //$RunningTotal = $myrow['amount'];

        $Amount = $myrow['actual'];
        $AmountLastY = $myrowLastY['actual'];
        
		if ($myrow['accountcode'] == $RetainedEarningsAct){
		 	
			/*Calculate B/Fwd retained earnings */

	$SQL = 'SELECT Sum(CASE WHEN chartdetails.period=' . $SelectedPeriod . ' THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS accumprofitbfwd,
			Sum(CASE WHEN chartdetails.period=' . ($LastSelectedPeriod) . " THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS lyaccumprofitbfwd
		FROM chartmaster INNER JOIN accountgroups
		ON chartmaster.group_ = accountgroups.groupname INNER JOIN chartdetails
		ON chartmaster.accountcode= chartdetails.accountcode
		WHERE accountgroups.pandl=1";

	$AccumProfitResult = DB_query($SQL,$db);
	if (DB_error_no($db) !=0) {
		$title = _('Balance Sheet') . ' - ' . _('Problem Report') . '....';
		include('includes/header.inc');
		prnMsg( _('The accumulated profits brought forward could not be calculated by the SQL because') . ' - ' . DB_error_msg($db) );
		echo '<BR><A HREF="' .$rootpath .'/index.php?' . SID . '">'. _('Back to the menu'). '</A>';
		if ($debug==1){
			echo '<BR>'. $SQL;
		}
		include('includes/footer.inc');
		exit;
	}
			$AccumProfitRow = DB_fetch_array($AccumProfitResult); /*should only be one row returned */
			$Amount += $AccumProfitRow['accumprofitbfwd'];
			$AmountLastY += $AccumProfitRow['lyaccumprofitbfwd'];
			$TotalCapital += $AccumProfitRow['accumprofitbfwd'];        	
			$TotalCapitalLastY += $AccumProfitRow['lyaccumprofitbfwd'];
		}
		
        //$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
        // bowikaxu - Se agrego la columna balance con el valor de $RunningTotal    
            if($SelectedPeriod == $LastPeriod){
            	printf("<TR>
                    <TD ALIGN=LEFT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    </TR>",
                    $myrow['group_'],
                    number_format($Amount,2),
                    number_format(abs($Amount/$TotCtasCapital['actual'])*100,0),
                    '--',
                    '--');
					}else {
						printf("<TR>
                    <TD ALIGN=LEFT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    </TR>",
                    $myrow['group_'],
                    number_format($Amount,2),
                    number_format(abs($Amount/$TotCtasCapital['actual'])*100,0),
                    number_format($AmountLastY,2),
                    number_format(abs($AmountLastY/$TotCtasCapitalLastY['actual'])*100,0));
					}
    }
	if($SelectedPeriod == $LastPeriod){
    	printf("<TR>
                    <TD ALIGN=LEFT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    </TR>",
                    _('Total Capital'),
                    number_format($TotalCapital,2),
                    number_format(abs($TotalCapital/$TotCtasCapital['actual'])*100,0),
                    '--',
                    '--');
					}else {
						printf("<TR>
                    <TD ALIGN=LEFT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT><STRONG>%s</STRONG></TD>
                    </TR>",
                    _('Total Capital'),
                    number_format($TotalCapital,2),
                    number_format(abs($TotalCapital/$TotCtasCapital['actual'])*100,0),
                    number_format($TotalCapitalLastY,2),
                    number_format(abs($TotalCapitalLastY/$TotCtasCapitalLastY['actual'])*100,0));
					}
    // FIN CUENTAS CAPITAL
    
    // print totals
	if($SelectedPeriod == $LastPeriod){
    	printf("<TR>
                    <TD ALIGN=LEFT class='tableheader'><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT class='tableheader'><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT class='tableheader'><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT class='tableheader'><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT class='tableheader'><STRONG>%s</STRONG></TD>
                    </TR>",
                    _('Total Pasivo mas Capital'),
                    number_format(($TotalPasivo+$TotalCapital),2),
                    number_format((($TotalPasivo+$TotalCapital)/($TotCtasCapital['actual']+$TotCtasPasivo['actual']))*100,0),
                    '--',
                    '--');
					}else {
						printf("<TR>
                    <TD ALIGN=LEFT class='tableheader'><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT class='tableheader'><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT class='tableheader'><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT class='tableheader'><STRONG>%s</STRONG></TD>
                    <TD ALIGN=RIGHT class='tableheader'><STRONG>%s</STRONG></TD>
                    </TR>",
                    _('Total Pasivo mas Capital'),
                    number_format(($TotalPasivo+$TotalCapital),2),
                    number_format((($TotalPasivo+$TotalCapital)/($TotCtasCapital['actual']+$TotCtasPasivo['actual']))*100,0),
                    number_format(($TotalPasivoLastY+$TotalCapitalLastY),2),
                    number_format((($TotalPasivoLastY+$TotalCapitalLastY)/($TotCtasPasivoLastY['actual']+$TotCtasCapitalLastY['actual']))*100,0));
					}
    
    echo "</TABLE></CENTER>";
    include('includes/footer.inc');
}

?>