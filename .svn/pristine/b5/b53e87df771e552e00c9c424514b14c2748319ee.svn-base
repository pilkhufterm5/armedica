<?php

/* $Revision: 15 $ 
bowikaxu realhost may 2007 - reporte de impuestos
*/

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */

$PageSecurity = 8;
include ('includes/session.inc');
$title = _('Tax Inquiry');
include('includes/header.inc');
include('includes/GLPostings.inc');

if (isset($_POST['Account'])){
	$SelectedAccount = $_POST['Account'];
} elseif (isset($_GET['Account'])){
	$SelectedAccount = $_GET['Account'];
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
echo '<CENTER><TABLE>';

		
		echo "<TR><TD>"._('Tax')."</TD>";
		echo "<TD><SELECT Name=tax>";
		$sql = "SELECT taxid, description FROM taxauthorities ORDER BY taxid ASC";
		$TaxesRes = DB_query($sql, $db);
		while($Taxes = DB_fetch_array($TaxesRes)){
			
			echo "<OPTION VALUE='".$Taxes['taxid']."'>".$Taxes['description']."</OPTION>";
			
		}
		echo "</SELECT></TD></TR>";
         echo '<TR>
         <TD>'._('For Period range').':</TD>
         <TD><SELECT Name=Period[] multiple>';
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
        <TR>           	
        	<TD>"._('Detail Or Summary Only')."</TD>
        	<TD><SELECT NAME='detail'><OPTION VALUE='0'>"._('Summary Report')."
           	<OPTION VALUE='1'>"._('Detailed Report')."
           	</SELECT></TD>
        </TR>
        
</TABLE><P>
<INPUT TYPE=SUBMIT NAME='Show' VALUE='"._('Show Account Transactions')."'></CENTER></FORM>";
         
if (isset($_POST['Show'])){
	
	if (!isset($SelectedPeriod)){
		prnMsg(_('A period or range of periods must be selected from the list box'),'info');
		include('includes/footer.inc');
		exit;
	}
	
	$IVApagado = 0;
	$IVAcobrado = 0;
	
	$SimpleHeader = "<TABLE align=center>";
	
	$DetailedHeader = "<TABLE align=center><TR>
						<TD class=tableheader>"._('Date')."</TD>
						<TD class=tableheader>"._('Period')."</TD>
						<TD class=tableheader>"._('Account Number')."</TD>
						<TD class=tableheader>"._('Account')."</TD>
						<TD class=tableheader>"._('Amount')."</TD>
						<TD class=tableheader>"._('Narrative')."</TD>
						<TD class=tableheader>"._('Posted')."</TD>
					</TR>";
	
	$FirstPeriodSelected = min($SelectedPeriod);
	$LastPeriodSelected = max($SelectedPeriod);
	
	
	// query tax paid
	$SQLpaid = "SELECT gltrans.*, 
			chartmaster.accountname,
			taxauthorities.description, 
			taxauthorities.rh_taxglcodepaid
			FROM gltrans, taxauthorities, chartmaster
			WHERE taxauthorities.rh_taxglcodepaid = gltrans.account
			AND gltrans.account = chartmaster.accountcode
			AND gltrans.periodno >= ".$FirstPeriodSelected."
			AND gltrans.periodno <= ".$LastPeriodSelected."
			AND taxauthorities.taxid = ".$_POST['tax']."";
	
	// query tax rec
	$SQLrec = "SELECT gltrans.*, 
			chartmaster.accountname,
			taxauthorities.description, 
			taxauthorities.rh_purchtaxglcoderec
			FROM gltrans, taxauthorities, chartmaster
			WHERE taxauthorities.rh_purchtaxglcoderec = gltrans.account
			AND gltrans.account = chartmaster.accountcode
			AND gltrans.periodno >= ".$FirstPeriodSelected."
			AND gltrans.periodno <= ".$LastPeriodSelected."
			AND taxauthorities.taxid = ".$_POST['tax']."";
	
	$PaidRes = DB_query($SQLpaid,$db,'Imposible obtener iva pagado');
	
	if($_POST['detail']==0){
		echo $SimpleHeader;
	}else {
		echo $DetailedHeader;
	}
	
	// pagado
	while ($trans = DB_fetch_array($PaidRes)){			
			
			if($trans['posted']==1){
				$post = _('Yes');
			}else {
				$post = _('No');
			}
			$IVApagado += $trans['amount'];
	   		
			// detail
			if($_POST['detail']==1){
				if ($k==1){
             		 echo '<tr bgcolor="#CCCCCC">';
              		$k=0;
       			} else {
              		echo '<tr bgcolor="#EEEEEE">';
              		$k++;
       			}
						echo "
						<TD>".$trans['trandate']."</TD>
						<TD>".$trans['periodno']."</TD>
						<TD>".$trans['account']."</TD>
						<TD>".$trans['accountname']."</TD>
						<TD align=right>".number_format($trans['amount'],2)."</TD>
						<TD align=left>".$trans['narrative']."</TD>
						<TD>".$post."</TD></TR>";							
			}
	}
	echo "<tr><td align=right class=tableheader colspan=4>"._('Balance').' '._('Input tax GL Account Received')."</td><td class=tableheader align=right>".number_format($IVApagado,2)."</td><td class=tableheader colspan=2></td></tr>";
	// cobrado
	$RecRes = DB_query($SQLrec,$db,'Imposible obtener iva cobrado');
	while ($trans2 = DB_fetch_array($RecRes)){			
			
		if($trans2['posted']==1){
				$post = _('Yes');
			}else {
				$post = _('No');
			}
			$IVAcobrado += -$trans2['amount'];
	   		
			// detail
			if($_POST['detail']==1){
				
				if ($k==1){
              	  echo '<tr bgcolor="#CCCCCC">';
            	  $k=0;
       			} else {
    	          echo '<tr bgcolor="#EEEEEE">';
	              $k++;
       			}
						echo "
						<TD>".$trans2['trandate']."</TD>
						<TD>".$trans2['periodno']."</TD>
						<TD>".$trans2['account']."</TD>
						<TD>".$trans2['accountname']."</TD>
						<TD align=right>".number_format(-$trans2['amount'],2)."</TD>
						<TD align=left>".$trans2['narrative']."</TD>
						<TD>".$post."</TD></TR>";							
			}
		}
		echo "<tr><td align=right class=tableheader colspan=4>"._('Balance').' '._('Output tax GL Account Paid')."</td><td class=tableheader align=right>".number_format($IVAcobrado,2)."</td><td class=tableheader colspan=2></td></tr>";
		echo "<tr><TD colspan=4 class=tableheader align=right><STRONG>"._('Balance').' '._('Total').' '."</TD><TD align=right class=tableheader>".number_format($IVApagado-$IVAcobrado,2)."</STRONG></TD><td class=tableheader colspan=2></td></tr>";
		echo "</TABLE>";
		/*	
			echo $SimpleHeader;
			echo "<TR><TD>"._('IVA x PAGAR')."</TD>
			<TD>".number_format($IVAxPAGAR,2)."</TD></TR>";
			
		// impresion del iva total
		echo "</TABLE>";
		*/
}
         
include('includes/footer.inc');
?>