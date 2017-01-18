<?php

/* $Revision: 176 $ */

$PageSecurity = 2;
include('includes/session.inc');

include('includes/class.pdf.php');
include('includes/SQL_CommonFunctions.inc');

if (isset($_POST['Period'])){
	$SelectedPeriod = $_POST['Period'];
} elseif (isset($_GET['Period'])){
	$SelectedPeriod = $_GET['Period'];
}

if (!isset($_POST['Show'])){
	$title = _('Poliza de Ventas');
	include('includes/header.inc');

	echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';

	/*Dates in SQL format for the last day of last month*/
	$DefaultPeriodDate = Date ('Y-m-d', Mktime(0,0,0,Date('m'),0,Date('Y')));

	/*Show a form to allow input of criteria for TB to show */
	echo '<CENTER><TABLE>
		<TR>
		<TD COLSPAN=2 ALIGN=CENTER><B>'._('Poliza de Ventas').'</TD>
		</TR>';	
	echo "<TR>
        <TD>"._('Location').":</TD>
        <TD><SELECT NAME=Location>";
	$sql = "SELECT loccode, locationname FROM locations";
	$res = DB_query($sql,$db);
	echo "<OPTION SELECTED VALUE='All'>"._('All');
	while($myrow=DB_fetch_array($res)){

		if($myrow['loccode'] == $_POST['Location']){
			echo "<OPTION SELECTED VALUE='".$myrow['loccode']."'>".$myrow['locationname']."";
		}else {
			echo "<OPTION VALUE='".$myrow['loccode']."'>".$myrow['locationname']."";
		}

	}
	echo "</SELECT></TD></TR>";

	echo '<TR>
         <TD>'._('Ver Facturas Canceladas').':</TD>
         <TD><SELECT Name=CanInvoices>';
	if($_POST['CanInvoices']==1){
		echo "<OPTION NAME=SI VALUE=1 SELECTED>"._('Yes')."</OPTION>";
		echo "<OPTION NAME=NO VALUE=0>"._('No')."</OPTION>";
	}else {
		echo "<OPTION NAME=SI VALUE=1>"._('Yes')."</OPTION>";
		echo "<OPTION NAME=NO VALUE=0 SELECTED>"._('No')."</OPTION>";
	}
	
	echo "</SELECT></TD>
        </TR>";
	
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
        </TR>";
	
	echo "</TABLE><P>
<INPUT TYPE=SUBMIT NAME='Show' VALUE='"._('PDF')."'>
<INPUT TYPE=SUBMIT NAME='Excell' VALUE='"._('Show Account Transactions')."'>
</CENTER></FORM>";

	if(!isset($_POST['Excell'])){
		include('includes/footer.inc');
	}
}
if(isset($_POST['Excell'])){
	
	if (isset($_POST['Period'])){
		$SelectedPeriod = $_POST['Period'];
	} elseif (isset($_GET['Period'])){
		$SelectedPeriod = $_GET['Period'];
	}

	$SelectedAccount = $_SESSION['CompanyRecord']['debtorsact'];
	
	if (!isset($SelectedPeriod)){
		prnMsg(_('A period or range of periods must be selected from the list box'),'info');
		include('includes/footer.inc');
		exit;
	}

	$SelectedAccount = $_SESSION['CompanyRecord']['debtorsact'];

	/*Is the account a balance sheet or a profit and loss account */
	$result = DB_query("SELECT pandl
				FROM accountgroups
				INNER JOIN chartmaster ON accountgroups.groupname=chartmaster.group_
				WHERE chartmaster.accountcode=$SelectedAccount",$db);
	$PandLRow = DB_fetch_row($result);
	if ($PandLRow[0]==1){
		$PandLAccount = True;
	}else{
		$PandLAccount = False; /*its a balance sheet account */
	}

	$FirstPeriodSelected = min($SelectedPeriod);
	$LastPeriodSelected = max($SelectedPeriod);

	if($_POST['Location']=='All'){
		$_POST['Location']="LIKE '%%' ";
	}else {
		$_POST['Location'] = " = '".$_POST['Location']."' ";
	}

	// obtener saldo inicial
	$sql = "SELECT SUM(gltrans.amount) AS vtas, chartmaster.accountname FROM gltrans, chartmaster, debtortrans, salesorders
			WHERE gltrans.type = 10 
			AND gltrans.account = chartmaster.accountcode
			AND gltrans.account ='".$SelectedAccount."'
			AND periodno >= ".$FirstPeriodSelected."
			AND periodno <= ".$LastPeriodSelected."
			AND debtortrans.type = 10
			AND debtortrans.transno = gltrans.typeno
			AND debtortrans.order_ = salesorders.orderno
			AND salesorders.fromstkloc ".$_POST['Location']."
			GROUP BY gltrans.account";
	$custbfw = DB_fetch_array(DB_query($sql,$db));

	$sql= "SELECT gltrans.type,
			systypes.typename,
			gltrans.typeno,
			gltrans.trandate,
			gltrans.narrative,
			gltrans.amount,
			gltrans.periodno,
			debtortrans.debtorno,
			debtorsmaster.name,
			debtortrans.id,
			debtortrans.rh_status
		FROM gltrans, systypes, debtortrans, debtorsmaster, salesorders
		WHERE gltrans.account = $SelectedAccount
		AND systypes.typeid=gltrans.type
		AND posted=1
		AND gltrans.type = 10
		AND periodno>=$FirstPeriodSelected
		AND periodno<=$LastPeriodSelected
		AND debtortrans.debtorno = debtorsmaster.debtorno
		AND debtortrans.transno = gltrans.typeno
		AND debtortrans.type = 10";
		if($_POST['CanInvoices']==0){
			$sql .= " AND debtortrans.rh_status != 'C'";
		}
		$sql .= " AND salesorders.orderno = debtortrans.order_
		AND salesorders.fromstkloc ".$_POST['Location']."
		ORDER BY periodno, gltrans.trandate, counterindex";

	//AND debtortrans.debtorno IN (SELECT debtorno FROM salesorders WHERE fromstkloc = '".$_POST['Location']."')
	// AND gltrans.type = 10
	// AND debtortrans.type = 10

	$ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because') ;
	$TransResult = DB_query($sql,$db,$ErrMsg);

	echo '<table>';

	$TableHeader = "<TR>
			<TD class='tableheader'>" . _('Account') . "</TD>
			<TD class='tableheader'>" . _('Customer Code') . "</TD>
			<TD class='tableheader'>" . _('Invoice').' '._('Number') . "</TD>
			<TD class='tableheader'>" . _('Nombre').' / '._('Concepto') . "</TD>
			<TD class='tableheader'>" ._('Fecha')."</TD>
			<TD class='tableheader'>" ._('N.C.')."</TD>
			<TD class='tableheader'>" ._('Parcial')."</TD>
			<TD class='tableheader'>" . _('Debit') . "</TD>
			<TD class='tableheader'>" . _('Credit') . '</TD>
			</TR>';

	echo $TableHeader;
	
	if ($PandLAccount==True) {
		$RunningTotal = 0;
	} else {
		// added to fix bug with Brought Forward Balance always being zero
		$sql = "SELECT bfwd,
						actual,
						period,
						chartmaster.accountname 
					FROM chartdetails, chartmaster
					WHERE chartdetails.accountcode= $SelectedAccount 
					AND chartdetails.period=" . $FirstPeriodSelected." 
					AND chartmaster.accountcode = chartdetails.accountcode"; 

		$ErrMsg = _('The chart details for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved');
		$ChartDetailsResult = DB_query($sql,$db,$ErrMsg);
		$ChartDetailRow = DB_fetch_array($ChartDetailsResult);
		// --------------------

	}
	
	if ($k==1){
		echo "<tr bgcolor='#CCCCCC'>";
		$k=0;
	} else {
		echo "<tr bgcolor='#EEEEEE'>";
		$k++;
	}
	printf("<td ALIGN=LEFT>%s</td>
			<td COLSPAN=5 ALIGN=CENTER>%s</td>
			<TD></TD>
			<td>%s</td>
			<td></td>
			</tr>",
	$SelectedAccount,
	$custbfw['accountname'],
	number_format($custbfw['vtas'],2));
	
	$PeriodTotal = 0;
	$PeriodNo = -9999;
	$ShowIntegrityReport = False;
	$j = 1;
	$i=0;
	$k=0; //row colour counter
	$DebitTotal = 0;
	$CreditTotal = 0;
	$TotFinal = 0;
	$TotCtes = 0;
	$trans = array();

	while ($myrow=DB_fetch_array($TransResult)) {

		if ($myrow['periodno']!=$PeriodNo){
			if ($PeriodNo!=-9999){ //ie its not the first time around
				/*Get the ChartDetails balance b/fwd and the actual movement in the account for the period as recorded in the chart details - need to ensure integrity of transactions to the chart detail movements. Also, for a balance sheet account it is the balance carried forward that is important, not just the transactions*/

				$sql = "SELECT bfwd,
						actual,
						period 
					FROM chartdetails 
					WHERE chartdetails.accountcode= $SelectedAccount 
					AND chartdetails.period=" . $PeriodNo; 

				$ErrMsg = _('The chart details for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved');
				$ChartDetailsResult = DB_query($sql,$db,$ErrMsg);
				$ChartDetailRow = DB_fetch_array($ChartDetailsResult);
/*
				echo "<TR bgcolor='#FDFEEF'>
					<TD COLSPAN=4><B>" . _('Total for period') . ' ' . $PeriodNo . '</B></TD>';
				if ($PeriodTotal < 0 ){ //its a credit balance b/fwd
					echo '<TD></TD><TD></TD>
						<TD ALIGN=RIGHT><B>'.number_format($DebitTotal,2).'</B></TD>
						<TD ALIGN=RIGHT><B>'.number_format($CreditTotal,2).'</B></TD>
						<TD ALIGN=RIGHT><B>' . number_format(-$PeriodTotal,2) . '</B></TD>
						<TD></TD>
						</TR>';
					$DebitTotal = 0;
					$CreditTotal = 0;
				} else { //its a debit balance b/fwd
					echo '<TD></TD>
					<TD ALIGN=RIGHT><B>'.number_format($DebitTotal,2).'</B></TD>
					<TD ALIGN=RIGHT><B>'.number_format($CreditTotal,2).'</B></TD>
					<TD ALIGN=RIGHT><B>' . number_format($PeriodTotal,2) . '</B></TD>
						<TD COLSPAN=3></TD>
						</TR>';
					$DebitTotal = 0;
					$CreditTotal = 0;
				}
				$IntegrityReport .= '<BR>' . _('Period') . ': ' . $PeriodNo  . _('Account movement per transaction') . ': '  . number_format($PeriodTotal,2) . ' ' . _('Movement per ChartDetails record') . ': ' . number_format($ChartDetailRow['actual'],2) . ' ' . _('Period difference') . ': ' . number_format($PeriodTotal -$ChartDetailRow['actual'],3);
*/
				if (ABS($PeriodTotal -$ChartDetailRow['actual'])>0.01){
					$ShowIntegrityReport = True;
				}
			}
			$PeriodNo = $myrow['periodno'];
			$PeriodTotal = 0;
		}

		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k++;
		}

		if($myrow['rh_status']=='C'){
			$color = "<FONT COLOR=RED>";
			$endcolor="</FONT>";
		}else {
			$color="";
			$endcolor="";
		}
		
		$RunningTotal += $myrow['amount'];
		$PeriodTotal += $myrow['amount'];

		if($myrow['amount']>=0){
			$DebitAmount = number_format($myrow['amount'],2);
			$CreditAmount = '';
			// bowikaxu realhost oct 07
			$DebitTotal += $myrow['amount'];
			$TotalCtes += $myrow['amount'];
		} else {
			$CreditAmount = number_format(-$myrow['amount'],2);
			$DebitAmount = '';
			// bowikaxu realhost oct 07
			$CreditTotal += (-$myrow['amount']);
			$TotalCtes += (-$myrow['amount']);
		}

		// bowikaxu nov 2007 - get external invoice number
		$sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations
			WHERE rh_invoicesreference.intinvoice = ".$myrow['typeno']."
			AND locations.loccode = rh_invoicesreference.loccode";
		$res = DB_query($sql,$db);
		$ExtInvoice = DB_fetch_array($res);

		$FormatedTranDate = ConvertSQLDate($myrow['trandate']);
		$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
		
		// get allocations
		$sql = "SELECT type,
				transno,
				trandate,
				debtortrans.debtorno,
				custallocns.amt
			FROM debtortrans
				INNER JOIN custallocns ON debtortrans.id=custallocns.transid_allocfrom
			WHERE custallocns.transid_allocto=". $myrow['id']."
			AND debtortrans.type = 11";

			$assig = DB_query($sql,$db);
			while($alloc = DB_fetch_array($assig)){
				if ($alloc['type']==10){
					$TransType = _('Invoice');
				} else if($alloc['type']==11) {
					$TransType = _('Credit Note');
				}else {
					$TransType = _('Receipt');
				}
				$allocs = $TransType.' '.$alloc['transno'];
				if($z>=1)$allocs .= ',';
				$z++;
			}
		
		// bowikaxu - Se agrego la columna balance con el valor de $RunningTotal
		printf("<td>%s</td>
			<td>%s</td>
			<td><A HREF='%s'>%s</A></td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			</tr>",
		$SelectedAccount,
		$myrow['debtorno'],
		$URL_to_TransDetail,
		$myrow['typename'].' '.$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['typeno'].')',
		$myrow['name'],
		$FormatedTranDate,
		$allocs,
		$color.$DebitAmount.$endcolor,
		'',
		'');

		$j++;

		$trans[$i] = $myrow['typeno'];
		$i++;

		If ($j == 18){
			echo $TableHeader;
			$j=1;
		}
		/*
		// bowikaxu realhost jan 4 2008 - remove allocations
		$sql = "SELECT type,
				transno,
				trandate,
				debtortrans.debtorno,
				custallocns.amt
			FROM debtortrans
				INNER JOIN custallocns ON debtortrans.id=custallocns.transid_allocfrom
			WHERE custallocns.transid_allocto=". $myrow['id'];

			$assig = DB_query($sql,$db);
			while($alloc = DB_fetch_array($assig)){
				if ($alloc['type']==10){
					$TransType = _('Invoice');
				} else if($alloc['type']==11) {
					$TransType = _('Credit Note');
				}else {
					$TransType = _('Receipt');
				}
				$allocs = $TransType.' '.$alloc['transno'];
				if($z>=1)$allocs .= ',';
				$z++;
			}
			if(DB_num_rows($assig)>=1){
				if ($k==1){
					echo "<tr bgcolor='#CCCCCC'>";
					$k=0;
				} else {
					echo "<tr bgcolor='#EEEEEE'>";
					$k++;
				}	
				printf("<td ALIGN=LEFT COLSPAN=2>%s</td>
						<td ALIGN=LEFT>%s</td>
						<td ALIGN=LEFT COLSPAN=4>%s</td>
						</tr>",
						_('Allocations'),
						$myrow['invtext'],
						$allocs);
			}
			*/
	}
	/*
	echo "<TR bgcolor='#FDFEEF'><TD COLSPAN=2><B>";
	echo '<TD></TD><TD></TD><TD></TD>
						<TD ALIGN=RIGHT><B>' . number_format($TotalCtes,2) . '</B></TD>
						<TD ALIGN=RIGHT><B>'.number_format($DebitTotal,2).'</B></TD>
						<TD ALIGN=RIGHT><B>'.number_format($CreditTotal,2).'</B></TD>
						<TD></TD>
						<TD></TD>
						</TR>';
	*/
	// -----------------
	// bowikaxu nov 07 - salesglpostings
	// bowikaxu realhost nov 07
	$sql = "SELECT SUM(amount) AS amount, chartmaster.accountname FROM gltrans, chartmaster WHERE
			chartmaster.accountcode = gltrans.account
			AND gltrans.periodno >= ".$FirstPeriodSelected."
			AND gltrans.periodno <= ".$LastPeriodSelected."
			AND gltrans.type = 10
			AND gltrans.typeno IN (";

	$sql .= "SELECT gltrans.typeno
			FROM gltrans, debtortrans, salesorders
		WHERE gltrans.account = $SelectedAccount
		AND posted=1
		AND gltrans.type = 10
		AND periodno>=$FirstPeriodSelected
		AND periodno<=$LastPeriodSelected
		AND debtortrans.transno = gltrans.typeno
		AND debtortrans.type = 10";
		if($_POST['CanInvoices']==0){
			$sql .= " AND debtortrans.rh_status != 'C'";
		}
		$sql .= " 
		AND salesorders.orderno = debtortrans.order_
		AND salesorders.fromstkloc ".$_POST['Location']."";
	/*
	$k = 0;
	foreach($trans as $value){
	if($k > 0)$sql .= ', ';
	$sql .= $value;
	$k++;
	}
	*/
	$sql .= ")
			AND account IN (SELECT salesglcode FROM salesglpostings) GROUP BY gltrans.account";
	$res = DB_query($sql,$db);

	$vtas_act_name = DB_fetch_array(DB_query("SELECT salesglcode FROM salesglpostings",$db));
	
	$TaxTotal = 0;
	while ($Vtas_act = DB_fetch_array($res)){

		echo "<TR bgcolor='#FDFEEF'>";
		echo '<TD>'.$vtas_act_name['salesglcode'].'</TD><TD COLSPAN=5 ALIGN=RIGHT><B>'.$Vtas_act['accountname'].':&nbsp;&nbsp;&nbsp;</B></TD><TD></TD>
						<TD ALIGN=RIGHT></TD>
						<TD ALIGN=RIGHT><B>' . number_format(-1*$Vtas_act['amount'],2) . '</B></TD>
						</TR>';	
		$TotVtas += (-1*$Vtas_act['amount']);
		$RunningTotal += $Vtas_act['amount'];

	}
	// bowikaxu nov 07 - impuestos
	// bowikaxu realhost nov 07
	$sql = "SELECT SUM(amount) AS amount, chartmaster.accountname FROM gltrans, chartmaster WHERE
			chartmaster.accountcode = gltrans.account
			AND gltrans.periodno >= ".$FirstPeriodSelected."
			AND gltrans.periodno <= ".$LastPeriodSelected."
			AND gltrans.type = 10
			AND gltrans.typeno IN (";

	$sql .= "SELECT gltrans.typeno
			FROM gltrans, debtortrans, salesorders
		WHERE gltrans.account = $SelectedAccount
		AND posted=1
		AND gltrans.type = 10
		AND periodno>=$FirstPeriodSelected
		AND periodno<=$LastPeriodSelected
		AND debtortrans.transno = gltrans.typeno
		AND debtortrans.type = 10";
		if($_POST['CanInvoices']==0){
			$sql .= " AND debtortrans.rh_status != 'C'";
		}
		$sql .= " 
		AND salesorders.orderno = debtortrans.order_
		AND salesorders.fromstkloc ".$_POST['Location']."";
	/*
	$k = 0;
	foreach($trans as $value){
	if($k > 0)$sql .= ', ';
	$sql .= $value;
	$k++;
	}
	*/
	$sql .= ")
			AND account IN (SELECT taxglcode FROM taxauthorities) GROUP BY gltrans.account";
	$res = DB_query($sql,$db);
	$TaxTotal = 0;
	$tax_act_name = DB_fetch_array(DB_query("SELECT taxglcode FROM taxauthorities",$db));
	while ($Tax_act = DB_fetch_array($res)){

		echo "<TR bgcolor='#FDFEEF'>";
		echo '<TD>'.$tax_act_name['taxglcode'].'</TD><TD COLSPAN=5 ALIGN=RIGHT><B>'.$Tax_act['accountname'].':&nbsp;&nbsp;&nbsp;</B></TD><TD></TD>
						<TD ALIGN=RIGHT></TD>
						<TD ALIGN=RIGHT><B>' . number_format(-1*$Tax_act['amount'],2) . '</B></TD>
						</TR>';	
		$TaxTotal += (-1*$Tax_act['amount']);
		$RunningTotal += $Tax_act['amount'];

	}

	echo "<TR bgcolor='#FDFEEF'><TD COLSPAN=5><B>"._('Totales').':';
	echo '</B></TD><TD></TD>';

	echo '<TD></TD><TD ALIGN=RIGHT><B>' . number_format(($TotalCtes),2) . '</B></TD><TD ALIGN=RIGHT><B>'.number_format($TotVtas+$TaxTotal,2).'</TD>
	</TR>';
	//<TD ALIGN=RIGHT><B>'.number_format($RunningTotal,2).'</B></TD>
	echo '</table>';


}else {

	if (isset($_POST['Period'])){
		$SelectedPeriod = $_POST['Period'];
	} elseif (isset($_GET['Period'])){
		$SelectedPeriod = $_GET['Period'];
	}

	if($_POST['Location']=='All'){
		$_POST['Location']="LIKE '%%' ";
	}else {
		$_POST['Location'] = " = '".$_POST['Location']."' ";
	}
	
	$SelectedAccount = $_SESSION['CompanyRecord']['debtorsact'];

	if (!isset($SelectedPeriod)){
		prnMsg(_('A period or range of periods must be selected from the list box'),'info');
		include('includes/footer.inc');
		exit;
	}

	/*Is the account a balance sheet or a profit and loss account */
	$result = DB_query("SELECT pandl
				FROM accountgroups
				INNER JOIN chartmaster ON accountgroups.groupname=chartmaster.group_
				WHERE chartmaster.accountcode=$SelectedAccount",$db);
	$PandLRow = DB_fetch_row($result);
	if ($PandLRow[0]==1){
		$PandLAccount = True;
	}else{
		$PandLAccount = False; /*its a balance sheet account */
	}

	$FirstPeriodSelected = min($SelectedPeriod);
	$LastPeriodSelected = max($SelectedPeriod);


	$sql = "SELECT lastdate_in_period FROM periods WHERE periodno  = ".$FirstPeriodSelected."";
	$dates = DB_fetch_array(DB_query($sql,$db));
	$De = MonthAndYearFromSQLDate($dates['lastdate_in_period']);

	//DB_free_result($dates);

	$sql = "SELECT lastdate_in_period FROM periods WHERE periodno  = ".$LastPeriodSelected."";
	$dates = DB_fetch_array(DB_query($sql,$db));
	$Hasta = MonthAndYearFromSQLDate($dates['lastdate_in_period']);

	//DB_free_result($dates);

	// obtener saldo inicial
	$sql = "SELECT SUM(gltrans.amount) AS vtas, chartmaster.accountname FROM gltrans, chartmaster, debtortrans, salesorders
			WHERE gltrans.type = 10 
			AND gltrans.account = chartmaster.accountcode
			AND gltrans.account ='".$SelectedAccount."'
			AND periodno >= ".$FirstPeriodSelected."
			AND periodno <= ".$LastPeriodSelected."
			AND debtortrans.type = 10
			AND debtortrans.transno = gltrans.typeno
			AND debtortrans.order_ = salesorders.orderno
			AND salesorders.fromstkloc ".$_POST['Location']."
			GROUP BY gltrans.account";
	$custbfw = DB_fetch_array(DB_query($sql,$db));

	$sql= "SELECT gltrans.type,
			systypes.typename,
			gltrans.typeno,
			gltrans.account,
			gltrans.trandate,
			gltrans.narrative,
			gltrans.amount,
			gltrans.periodno,
			debtortrans.debtorno,
			debtorsmaster.name,
			debtortrans.id,
			debtortrans.rh_status
		FROM gltrans, systypes, debtortrans, debtorsmaster, salesorders
		WHERE gltrans.account = $SelectedAccount
		AND systypes.typeid=gltrans.type
		AND posted=1
		AND gltrans.type = 10
		AND periodno>=$FirstPeriodSelected
		AND periodno<=$LastPeriodSelected
		AND debtortrans.debtorno = debtorsmaster.debtorno
		AND debtortrans.transno = gltrans.typeno
		AND debtortrans.type = 10";
		if($_POST['CanInvoices']==0){
			$sql .= " AND debtortrans.rh_status != 'C'";
		}
		$sql .= " 
		AND salesorders.orderno = debtortrans.order_
		AND salesorders.fromstkloc ".$_POST['Location']."
		ORDER BY periodno, gltrans.trandate, counterindex";

	// AND gltrans.type = 10
	// AND debtortrans.type = 10

	$ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because') ;
	$TransResult = DB_query($sql,$db,$ErrMsg);

	/*Yes there are line items to start the ball rolling with a page header */

	/*Set specifically for the stationery being used -needs to be modified for clients own
	packing slip 2 part stationery is recommended so storeman can note differences on and
	a copy retained */

	$Page_Width=612; // horizontal
	$Page_Height=792; // vertical
	$Top_Margin=10;
	$Bottom_Margin=20;
	$Left_Margin=10;
	$Right_Margin=10;


	$PageSize = array(0,0,$Page_Width,$Page_Height);
	$pdf = & new Cpdf($PageSize);
	$FontSize=12;
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->addinfo('Author','webERP ' . $Version);
	$pdf->addinfo('Creator','webERP http://www.weberp.org - R&OS PHP-PDF http://www.ros.co.nz');
	$pdf->addinfo('Title', _('Customer Packing Slip') );
	$pdf->addinfo('Subject', _('Packing slip for order') . ' ' . $_GET['TransNo']);

	$line_height=16;

	$PageNumber = 1;
	include('includes/rh_VtasPolicyHeader.inc');
	$FontSize = 9;

	$YPos -= ($line_height);
	$line_height = 11;
	$PeriodTotal = 0;
	$PeriodNo = -9999;
	$DebitTotal = 0;
	$CreditTotal = 0;
	$TotFinal = 0;
	$TotCtes = 0;
	
	// obtener saldo inicial cuenta clientes
	$sql = "SELECT SUM(gltrans.amount) AS vtas, chartmaster.accountname FROM gltrans, chartmaster, debtortrans, salesorders
			WHERE gltrans.type = 10 
			AND gltrans.account = chartmaster.accountcode
			AND gltrans.account ='".$SelectedAccount."'
			AND periodno >= ".$FirstPeriodSelected."
			AND periodno <= ".$LastPeriodSelected."
			AND debtortrans.type = 10
			AND debtortrans.transno = gltrans.typeno
			AND debtortrans.order_ = salesorders.orderno
			AND salesorders.fromstkloc ".$_POST['Location']."
			GROUP BY gltrans.account";
	$custbfw = DB_fetch_array(DB_query($sql,$db));

	$pdf->addTextWrap(35, $YPos,40,$FontSize, $SelectedAccount,'left');
	$pdf->addTextWrap(80, $YPos,40,$FontSize, $custbfw['accountname'],'left');
	//$pdf->addTextWrap(290, $YPos,110,$FontSize-1, $FormatedTranDate.substr($myrow['typename'],0,3).' '.$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'],'left');
	//$pdf->addTextWrap(390, $YPos,40,$FontSize, $DebitAmount,'right');
	$pdf->addTextWrap(430, $YPos,80,$FontSize, number_format($custbfw['vtas'],2),'right');
	$pdf->addTextWrap(500, $YPos,80,$FontSize, '','right');
	$YPos -= $line_height;
	
	while ($myrow=DB_fetch_array($TransResult)) {

		if ($myrow['periodno']!=$PeriodNo){
			if ($PeriodNo!=-9999){ //ie its not the first time around
				/*Get the ChartDetails balance b/fwd and the actual movement in the account for the period as recorded in the chart details - need to ensure integrity of transactions to the chart detail movements. Also, for a balance sheet account it is the balance carried forward that is important, not just the transactions*/

				$YPos -= ($line_height);

				$pdf->addTextWrap(320, $YPos,100,$FontSize, _('Total for period').' '.$PeriodNo,'left');
				$pdf->addTextWrap(430, $YPos,80,$FontSize+1, number_format($DebitTotal,2),'right');
				$pdf->addTextWrap(500, $YPos,80,$FontSize+1, number_format($CreditTotal,2),'right');

				$YPos -= ($line_height);
				$YPos -= ($line_height);

			}
			$PeriodNo = $myrow['periodno'];
			$PeriodTotal = 0;
		}

		$RunningTotal += $myrow['amount'];
		$PeriodTotal += $myrow['amount'];

		if($myrow['amount']>=0){
			$DebitAmount = number_format($myrow['amount'],2);
			$CreditAmount = '';
			// bowikaxu realhost oct 07
			$DebitTotal += $myrow['amount'];
			$TotalCtes += $myrow['amount'];
		} else {
			$CreditAmount = number_format(-$myrow['amount'],2);
			$DebitAmount = '';
			// bowikaxu realhost oct 07
			$CreditTotal += (-$myrow['amount']);
			$TotalCtes += (-$myrow['amount']);
		}

		// bowikaxu nov 2007 - get external invoice number
		$sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations
			WHERE rh_invoicesreference.intinvoice = ".$myrow['typeno']."
			AND locations.loccode = rh_invoicesreference.loccode";
		$res = DB_query($sql,$db);
		$ExtInvoice = DB_fetch_array($res);

		$FormatedTranDate = ConvertSQLDate($myrow['trandate']);
		//$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
		// bowikaxu - Se agrego la columna balance con el valor de $RunningTotal

		$pdf->addTextWrap(35, $YPos,40,$FontSize, $myrow['account'],'left');
		$pdf->addTextWrap(80, $YPos,40,$FontSize-1, $myrow['debtorno'],'left');
		
		$pdf->addTextWrap(120, $YPos,136,$FontSize-1,$myrow['name'],'left');
		$pdf->addTextWrap(150+110, $YPos,48,$FontSize-1, $FormatedTranDate,'left');
		$pdf->addTextWrap(155+148, $YPos,40,$FontSize-1, $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'],'left');
		
		$pdf->addTextWrap(390, $YPos,40,$FontSize-1, $DebitAmount,'right');
		$pdf->addTextWrap(430, $YPos,80,$FontSize-1, '','right');
		$pdf->addTextWrap(500, $YPos,80,$FontSize-1, '','right');

		$j++;

		if ($j == 18){
			$j=1;
		}
		
		// bowikaxu realhost jan 4 2008 - remove allocations
		$sql = "SELECT type,
				transno,
				trandate,
				debtortrans.debtorno,
				custallocns.amt
			FROM debtortrans
				INNER JOIN custallocns ON debtortrans.id=custallocns.transid_allocfrom
			WHERE custallocns.transid_allocto=". $myrow['id']."
			AND debtortrans.type = 11";

			$assig = DB_query($sql,$db);
			while($alloc = DB_fetch_array($assig)){
				if ($alloc['type']==10){
					$TransType = _('Invoice');
				} else if($alloc['type']==11) {
					$TransType = _('Credit Note');
				}else {
					$TransType = _('Receipt');
				}
				//$allocs = $TransType.' '.$alloc['transno'];
				$allocs = $alloc['transno'];
				if($z>=1)$allocs .= ',';
				$z++;
			}
			if(DB_num_rows($assig)>=1){
						//$YPos -= ($line_height);
						//$pdf->addTextWrap(150, $YPos,390-145,$FontSize-1,_('Allocations').' '.$myrow['invtext'].' '.$allocs,'left');
						$pdf->addTextWrap(153+196, $YPos,40,$FontSize-1, $allocs,'left');
			}
			
		$YPos -= ($line_height);
		if($YPos <= 20){
			$PageNumber++;
			include('includes/rh_VtasPolicyHeader.inc');

		}
		
	}
	$YPos -= ($line_height);
	// bowikaxu nov 07 - salesglpostings
	// bowikaxu realhost nov 07
	$sql = "SELECT SUM(amount) AS amount, chartmaster.accountcode, chartmaster.accountname FROM gltrans, chartmaster WHERE
			chartmaster.accountcode = gltrans.account
			AND gltrans.periodno >= ".$FirstPeriodSelected."
			AND gltrans.periodno <= ".$LastPeriodSelected."
			AND gltrans.type = 10
			AND gltrans.typeno IN (";

	$sql .= "SELECT gltrans.typeno
			FROM gltrans, debtortrans, salesorders
		WHERE gltrans.account = $SelectedAccount
		AND posted=1
		AND gltrans.type = 10
		AND periodno>=$FirstPeriodSelected
		AND periodno<=$LastPeriodSelected
		AND debtortrans.transno = gltrans.typeno
		AND debtortrans.type = 10";
		if($_POST['CanInvoices']==0){
			$sql .= " AND debtortrans.rh_status != 'C'";
		}
		$sql .= " 
		AND salesorders.orderno = debtortrans.order_
		AND salesorders.fromstkloc ".$_POST['Location']."";
	$sql .= ")
			AND account IN (SELECT salesglcode FROM salesglpostings) GROUP BY gltrans.account";
	$res = DB_query($sql,$db);

	$TaxTotal = 0;
	while ($Vtas_act = DB_fetch_array($res)){
		/*
		echo "<TR bgcolor='#FDFEEF'>";
		echo '<TD COLSPAN=4 ALIGN=RIGHT><B>'.$Vtas_act['accountname'].':&nbsp;&nbsp;&nbsp;</B></TD><TD></TD>
		<TD ALIGN=RIGHT><B>' . number_format(-1*$Vtas_act['amount'],2) . '</B></TD>
		<TD ALIGN=RIGHT><B>' . number_format($RunningTotal+$Vtas_act['amount'],2) . '</B></TD>
		<TD></TD>
		</TR>';
		*/
		$pdf->addTextWrap(35, $YPos,50,$FontSize, $Vtas_act['accountcode'],'left');
		$pdf->addTextWrap(100, $YPos,250,$FontSize, $Vtas_act['accountname'],'left');
		$pdf->addTextWrap(500, $YPos,80,$FontSize-1, number_format(-1*$Vtas_act['amount'],2),'right');

		$TotVtas += (-1*$Vtas_act['amount']);
		$RunningTotal += $Vtas_act['amount'];
		$CreditTotal += (-1*$Vtas_act['amount']);

		$YPos -= ($line_height);
		if($YPos <= 20){
			$PageNumber++;
			include('includes/rh_VtasPolicyHeader.inc');

		}

	}

	// bowikaxu nov 07 - impuestos
	// bowikaxu realhost nov 07
	$sql = "SELECT SUM(amount) AS amount, chartmaster.accountcode, chartmaster.accountname FROM gltrans, chartmaster WHERE
			chartmaster.accountcode = gltrans.account
			AND gltrans.periodno >= ".$FirstPeriodSelected."
			AND gltrans.periodno <= ".$LastPeriodSelected."
			AND gltrans.type = 10
			AND gltrans.typeno IN (";

	$sql .= "SELECT gltrans.typeno
			FROM gltrans, debtortrans, salesorders
		WHERE gltrans.account = $SelectedAccount
		AND posted=1
		AND gltrans.type = 10
		AND periodno>=$FirstPeriodSelected
		AND periodno<=$LastPeriodSelected
		AND debtortrans.transno = gltrans.typeno
		AND debtortrans.type = 10";
		if($_POST['CanInvoices']==0){
			$sql .= " AND debtortrans.rh_status != 'C'";
		}
		$sql .= " 
		AND salesorders.orderno = debtortrans.order_
		AND salesorders.fromstkloc ".$_POST['Location']."";
	$sql .= ")
			AND account IN (SELECT taxglcode FROM taxauthorities) GROUP BY gltrans.account";
	$res = DB_query($sql,$db);
	$TaxTotal = 0;

	while ($Tax_act = DB_fetch_array($res)){

		$pdf->addTextWrap(35, $YPos,50,$FontSize, $Tax_act['accountcode'],'left');
		$pdf->addTextWrap(100, $YPos,250,$FontSize, $Tax_act['accountname'],'left');
		$pdf->addTextWrap(500, $YPos,80,$FontSize-1, number_format(-1*$Tax_act['amount'],2),'right');
		//$pdf->addTextWrap(500, $YPos,80,$FontSize+1, number_format($RuningTotal+$Tax_act,2),'right');

		$TaxTotal += (-1*$Tax_act['amount']);
		$RunningTotal += $Tax_act['amount'];
		$CreditTotal += (-1*$Tax_act['amount']);

		$YPos -= ($line_height);
		if($YPos <= 20){
			$PageNumber++;
			include('includes/rh_VtasPolicyHeader.inc');

		}

	}
	$YPos -= ($line_height);

	$pdf->addTextWrap(100, $YPos,220,$FontSize, _('Sumas Iguales'),'left');
	$pdf->addTextWrap(430, $YPos,80,$FontSize+1, number_format($DebitTotal,2),'right');
	$pdf->addTextWrap(500, $YPos,80,$FontSize+1, number_format($CreditTotal,2),'right');

	$YPos -= ($line_height);
	$YPos -= ($line_height);
	if($YPos <= 40){
		$PageNumber++;
		$pdf->newPage();
		$pdf->addTextWrap(10,750,600,11,$_SESSION['CompanyRecord']['coyname'],'center');

		$pdf->addText(40, 720,$FontSize, _('P&oacute;liza').' '._('Number').": ");
		if($De == $Hasta){
			$concepto = _('Concepto').' Ventas de '.$De;
		}else {
			$concepto = _('Concepto').' Ventas de '.$De.' - '.$Hasta;
		}
		$pdf->addText(40+10, 720-$FontSize+1,$FontSize, $concepto);

		//$pdf->addText(450, 710,$FontSize,  _('Date').': '.date('Y-m-d'));
		//include('includes/rh_VtasPolicyHeader.inc');

		$YPos = 638;
	}
	// Print bottom fields
	$pdf->line(5,$YPos,690,$YPos);
	$YPos -= ($line_height);

	$pdf->addTextWrap(10, $YPos,50,$FontSize, '| '._('Hecho Por').':','left');

	$pdf->addTextWrap(100, $YPos,180,$FontSize, '| '._('Revisado Por').':','left');
	$pdf->addTextWrap(280, $YPos,170,$FontSize, '| '._('Autorizado Por').':','left');

	$pdf->addTextWrap(430, $YPos,80,$FontSize, '| '._('Diario No').'.','right');
	$pdf->addTextWrap(500, $YPos,80,$FontSize, '| '._('P&oacute;liza').' No.','right');

	$YPos -= ($line_height);
	$pdf->line(5,$YPos-$FontSize,690,$YPos-$FontSize);
	// End printing bottom fields

	$YPos -= ($line_height);
	//$pdf->addText($XPos, $YPos,$FontSize, _('Impreso').': '.date('Y-m-d'));


	$pdfcode = $pdf->output();
	$len = strlen($pdfcode);

	if ($len<=20){
		$title = _('Policy');
		include('includes/header.inc');
		echo '<p>'. _('No habia transacciones para ser mostradas').
		'<BR>'. '<A HREF="' . $rootpath . '/index.php?' . SID . '">' . _('Back to the menu') . '</A>';
		include('includes/footer.inc');
		exit;
	} else {
		header('Content-type: application/pdf');
		header('Content-Length: ' . $len);
		header('Content-Disposition: inline; filename=VtasPolicy.pdf');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');

		$pdf->Stream();

	}

} /*end if there are order details to show on the order*/

?>