<?php

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-03-15 13:16:28 -0600 (Sat, 15 Mar 2008) $
 * $Rev: 123 $
 */

$PageSecurity = 8;
include ('includes/session.inc');
$title = _('Account').' '._('Inquiry');
include('includes/header.inc');
include('includes/GLPostings.inc');

if (isset($_POST['Period'])){
	$SelectedPeriod = $_POST['Period'];
} elseif (isset($_GET['Period'])){
	$SelectedPeriod = $_GET['Period'];
}

echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';

/*Dates in SQL format for the last day of last month*/
$DefaultPeriodDate = Date ('Y-m-d', Mktime(0,0,0,Date('m'),0,Date('Y')));

/*
$SQL = "SELECT bankaccountname,
		bankaccounts.accountcode
	FROM bankaccounts,
		chartmaster
	WHERE bankaccounts.accountcode=chartmaster.accountcode";

$ErrMsg = _('The bank accounts could not be retrieved because');
$DbgMsg = _('The SQL used to retrieve the bank acconts was');
$AccountsResults = DB_query($SQL,$db,$ErrMsg,$DbgMsg);
*/
/*Show a form to allow input of criteria for TB to show */
echo '<CENTER><TABLE>';
      
         echo '<TR>
         <TD>'._('For Period range').':</TD>
         <TD><SELECT Name=Period>';
	 $sql = 'SELECT periodno, lastdate_in_period FROM periods ORDER BY periodno DESC';
	 $Periods = DB_query($sql,$db);
         $id=0;
         while ($myrow=DB_fetch_array($Periods,$db)){

            if($myrow['periodno'] == $SelectedPeriod){
              echo '<OPTION SELECTED VALUE=' . $myrow['periodno'] . '>' . _(MonthAndYearFromSQLDate($myrow['lastdate_in_period']));
            $id++;
            } else {
              echo '<OPTION VALUE=' . $myrow['periodno'] . '>' . _(MonthAndYearFromSQLDate($myrow['lastdate_in_period']));
            }

         }
         echo "</SELECT></TD>
        </TR>        
        
</TABLE><P>
<INPUT TYPE=SUBMIT NAME='Show' VALUE='"._('Show')."'></CENTER></FORM>";

/* End of the Form  rest of script is what happens if the show button is hit*/

if (isset($_POST['Show'])){

	if (!isset($SelectedPeriod)){
		prnMsg(_('A period or range of periods must be selected from the list box'),'info');
		include('includes/footer.inc');
		exit;
	}

/****************************************************************************************************************************
* Jorge Garcia
* 28/Ene/2009 Consulta de las cuentas de ref espeacial
****************************************************************************************************************************/
	$sql = 'SELECT MIN(periodno) as per FROM periods';
	$Periods = DB_query($sql,$db);
        $myrow = DB_fetch_array($Periods,$db);
	if($SelectedPeriod == $myrow['per']){
		$PrevPeriod = 'NO';
		$LastYSPeriod = 'NO';
		$LastYPPeriod = 'NO';
	}else{
		if(($SelectedPeriod-$myrow['per'])>12){
			$PrevPeriod = $SelectedPeriod-1;
			$LastYSPeriod = $SelectedPeriod-12;
			$LastYPPeriod = $PrevPeriod-12;
		}else{	
			$PrevPeriod = $SelectedPeriod-1;
			$LastYSPeriod = 'NO';
			$LastYPPeriod = 'NO';
		}
	}
	$ErrMsg = _('The transactions for the accounts') .' ' . _('could not be retrieved because') ;
	//$SelectedResult = DB_query($sql,$db,$ErrMsg);

	echo '<CENTER><TABLE>';

		//MonthAndYearFromSQLDate($myrow['lastdate_in_period'])
		echo "<TR>";
		
		echo "<TD CLASS='tableheader'>"._('Account')."</TD>";
		
		// heading selected month
		$sql = 'SELECT periodno, lastdate_in_period FROM periods WHERE periodno = '.($SelectedPeriod).'';
		$res = DB_query($sql,$db);
		$SelP = DB_fetch_array($res);
		$Tableheader .= "<TD>".MonthAndYearFromSQLDate($SelP['lastdate_in_period'])."</TD>";
		echo "<TD CLASS='tableheader'>".MonthAndYearFromSQLDate($SelP['lastdate_in_period'])."</TD>";
	if(is_numeric($PrevPeriod)){
		// heading previous month
		$sql = 'SELECT periodno, lastdate_in_period FROM periods WHERE periodno = '.($PrevPeriod).'';
		$res = DB_query($sql,$db);
		$PrevP = DB_fetch_array($res);
		$Tableheader .= "<TD>".MonthAndYearFromSQLDate($PrevP['lastdate_in_period'])."</TD>";
		echo "<TD CLASS='tableheader'>".MonthAndYearFromSQLDate($PrevP['lastdate_in_period'])."</TD>";
	}
	if(is_numeric($LastYSPeriod)){
		// heading last year selected
		$sql = 'SELECT periodno, lastdate_in_period FROM periods WHERE periodno = '.($LastYSPeriod).'';
		$res = DB_query($sql,$db);
		$LYSelP = DB_fetch_array($res);
		$Tableheader .= "<TD>".MonthAndYearFromSQLDate($LYSelP['lastdate_in_period'])."</TD>";
		echo "<TD CLASS='tableheader'>".MonthAndYearFromSQLDate($LYSelP['lastdate_in_period'])."</TD>";
	}
	if(is_numeric($LastYPPeriod)){
		// heading last year previous period
		$sql = 'SELECT periodno, lastdate_in_period FROM periods WHERE periodno = '.($LastYPPeriod).'';
		$res = DB_query($sql,$db);
		$LYPrevP = DB_fetch_array($res);
		$Tableheader .= "<TD>".MonthAndYearFromSQLDate($LYPrevP['lastdate_in_period'])."</TD>";
		echo "<TD CLASS='tableheader'>".MonthAndYearFromSQLDate($LYPrevP['lastdate_in_period'])."</TD>";
	}
		echo "<TR>";

		$SelTotal = 0;
		$PrevTotal = 0;
		$LYSTotal = 0;
		$LYPTotal = 0;

		$sqlref = 'SELECT account FROM rh_refaccounts ORDER BY account ASC';
		$resref = DB_query($sqlref,$db);
		while($rh_refaccounts = DB_fetch_array($resref)){
		//foreach($rh_Accounts as $Act){
			$sql = "SELECT accountname FROM chartmaster WHERE accountcode = '".$rh_refaccounts['account']."'";
			$res = DB_query($sql,$db);
			$ActName = DB_fetch_array($res);
			
			echo "<TR>";			
			echo "<TD ALIGN=right CLASS='tableheader'>".$ActName['accountname']."</TD>";
			
			// selected
			$sql = "SELECT bfwd FROM chartdetails WHERE accountcode = '".$rh_refaccounts['account']."' AND period = ".$SelectedPeriod;
			$res2 = DB_query($sql,$db);
			$Res2 = DB_fetch_array($res2);
			DB_free_result($res2);
			
			$sql = "SELECT SUM(amount) AS Saldo FROM gltrans WHERE account = '".$rh_refaccounts['account']."'
					AND periodno = ".$SelectedPeriod."";
			$res = DB_query($sql,$db);
			$Res = DB_fetch_array($res);
			DB_free_result($res);
			echo "<TD ALIGN=right>".number_format($Res['Saldo']+$Res2['bfwd'],2)."</TD>";
			$SelTotal += ($Res['Saldo']+$Res2['bfwd']);
		if(is_numeric($PrevPeriod)){
			// previous
			$sql = "SELECT bfwd FROM chartdetails WHERE accountcode = '".$rh_refaccounts['account']."' AND period = ".$PrevPeriod;
			$res2 = DB_query($sql,$db);
			$Res2 = DB_fetch_array($res2);
			DB_free_result($res2);
			
			$sql = "SELECT SUM(gltrans.amount) AS Saldo FROM gltrans WHERE account = '".$rh_refaccounts['account']."'
					AND periodno = ".$PrevPeriod."";
			$res = DB_query($sql,$db);
			$Res = DB_fetch_array($res);
			DB_free_result($res);
			echo "<TD ALIGN=right>".number_format($Res['Saldo']+$Res2['bfwd'],2)."</TD>";
			$PrevTotal += ($Res['Saldo']+$Res2['bfwd']);
		}
		if(is_numeric($LastYSPeriod)){
			// last year selected
			$sql = "SELECT bfwd FROM chartdetails WHERE accountcode = '".$rh_refaccounts['account']."' AND period = ".$LastYSPeriod;
			$res2 = DB_query($sql,$db);
			$Res2 = DB_fetch_array($res2);
			DB_free_result($res2);
			
			$sql = "SELECT SUM(amount) AS Saldo FROM gltrans WHERE account = '".$rh_refaccounts['account']."'
					AND periodno = ".$LastYSPeriod."";
			$res = DB_query($sql,$db);
			$Res = DB_fetch_array($res);
			DB_free_result($res);
			echo "<TD ALIGN=right>".number_format($Res['Saldo']+$Res2['bfwd'],2)."</TD>";
			$LYSTotal += ($Res['Saldo']+$Res2['bfwd']);
		}
		if(is_numeric($LastYPPeriod)){
			// last year previous
			$sql = "SELECT bfwd FROM chartdetails WHERE accountcode = '".$rh_refaccounts['account']."' AND period = ".$LastYPPeriod;
			$res2 = DB_query($sql,$db);
			$Res2 = DB_fetch_array($res2);
			DB_free_result($res2);
			
			$sql = "SELECT SUM(amount) AS Saldo FROM gltrans WHERE account = '".$rh_refaccounts['account']."' 
					AND periodno = ".$LastYPPeriod."";
			$res = DB_query($sql,$db);
			$Res = DB_fetch_array($res);
			DB_free_result($res);
			echo "<TD ALIGN=right>".number_format($Res['Saldo']+$Res2['bfwd'],2)."</TD>";
			$LYPTotal += ($Res['Saldo']+$Res2['bfwd']);
		}	
			echo "</TR>";

		}
		
		echo "<TR>";
		echo "<TD CLASS='tableheader'>"._('Total')."</TD>";
		echo "<TD style='border-top:1px solid black' ALIGN=right>".number_format($SelTotal,2)."</TD>";
	if(is_numeric($PrevPeriod)){
		echo "<TD style='border-top:1px solid black' ALIGN=right>".number_format($PrevTotal,2)."</TD>";
	}
	if(is_numeric($LastYSPeriod)){
		echo "<TD style='border-top:1px solid black' ALIGN=right>".number_format($LYSTotal,2)."</TD>";
	}
	if(is_numeric($LastYPPeriod)){
		echo "<TD style='border-top:1px solid black' ALIGN=right>".number_format($LYPTotal,2)."</TD>";
	}
		echo "</TR>";
		
		echo "</TABLE></CENTER>";
/****************************************************************************************************************************
* Jorge Garcia Fin Modificacion
****************************************************************************************************************************/
}
include('includes/footer.inc');
?>
