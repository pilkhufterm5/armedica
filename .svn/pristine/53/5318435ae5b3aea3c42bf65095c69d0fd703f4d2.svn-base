<?php
/**
 * REALHOST 2008
 * $LastChangedDate$
 * $Rev$
 */

$PageSecurity = 11;

include('includes/session.inc');
$title = _('General Ledger').' '._('Work Order Status Inquiry');
include('includes/header.inc');

	$ErrMsg = _('Could not retrieve the details of the selected work order item');
	$WOResult = DB_query("SELECT workorders.loccode,
			 locations.locationname,
			 workorders.requiredby,
			 workorders.startdate,
			 workorders.closed,
			 stockmaster.description,
			 stockmaster.decimalplaces,
			 stockmaster.units,
			 woitems.qtyreqd,
			 woitems.qtyrecd
			FROM workorders INNER JOIN locations
			ON workorders.loccode=locations.loccode
			INNER JOIN woitems
			ON workorders.wo=woitems.wo
			INNER JOIN stockmaster
			ON woitems.stockid=stockmaster.stockid
			WHERE woitems.stockid='" . DB_escape_string($_REQUEST['StockID']) . "'
			AND woitems.wo =" . $_REQUEST['WO'],
			$db,
			$ErrMsg);

	if (DB_num_rows($WOResult)==0){
		prnMsg(_('The selected work order item cannot be retrieved from the database'),'info');
		include('includes/footer.inc');
		exit;
	}
	$WORow = DB_fetch_array($WOResult);
	
	echo '<A HREF="'. $rootpath . '/SelectWorkOrder.php?' . SID . '">' . _('Back to Work Orders'). '</A><BR>';

	echo '<center><table cellpadding=2 border=1>
		<tr><td class="label">' . _('Issue to work order') . ':</td><td>' . $_REQUEST['WO'] .'</td><td class="label">' . _('Item') . ':</td><td>' . $_REQUEST['StockID'] . ' - ' . $WORow['description'] . '</td></tr>
	 	<tr><td class="label">' . _('Manufactured at') . ':</td><td>' . $WORow['locationname'] . '</td><td class="label">' . _('Required By') . ':</td><td>' . ConvertSQLDate($WORow['requiredby']) . '</td></tr>
	 	<tr><td class="label">' . _('Quantity Ordered') . ':</td><td align=right>' . number_format($WORow['qtyreqd'],$WORow['decimalplaces']) . '</td><td colspan=2>' . $WORow['units'] . '</td></tr>
	 	<tr><td class="label">' . _('Already Received') . ':</td><td align=right>' . number_format($WORow['qtyrecd'],$WORow['decimalplaces']) . '</td><td colspan=2>' . $WORow['units'] . '</td></tr>
	 	<tr><td class="label">' . _('Date Material Issued') . ':</td><td>' . Date($_SESSION['DefaultDateFormat']) . '</td>
		<td class="label">' . _('Issued From') . ':</td><td>';

		if (!isset($_POST['FromLocation'])){
			$_POST['FromLocation']=$WORow['loccode'];
		}
		$LocResult = DB_query("SELECT loccode, locationname
				FROM locations
				WHERE loccode='" . $_POST['FromLocation'] . "'",
				$db);
		$LocRow = DB_fetch_array($LocResult);
		echo $LocRow['locationname'];
		echo '<tr><td colspan=4><hr></td></tr>';
		echo '</td></tr></table>';

		//set up options for selection of the item to be issued to the WO
		echo '<table border=1><tr><td colspan=5 class="tableheader">' . _('Material Requirements For this Work Order') . '</td></tr>';
		echo '<tr><td colspan=2 class="tableheader">' . _('Item') . '</td>
			<td class="tableheader">' . _('Qty Required') . '</td>
			<td class="tableheader">' . _('Qty Issued') . '</td></tr>';

		$RequirmentsResult = DB_query("SELECT worequirements.stockid,
						stockmaster.description,
						stockmaster.decimalplaces,
						autoissue,
						qtypu
					FROM worequirements INNER JOIN stockmaster
					ON worequirements.stockid=stockmaster.stockid
					WHERE wo=" . $_REQUEST['WO'],
					$db);

		while ($RequirementsRow = DB_fetch_array($RequirmentsResult)){
			if ($RequirementsRow['autoissue']==0){
				echo '<tr><td>' . _('Manual Issue') . '
				<td>' . $RequirementsRow['stockid'] . ' - ' . $RequirementsRow['description'] . '</td>';
			} else {
				echo '<tr><td class="notavailable">' . _('Auto Issue') . '<td class="notavailable">' .$RequirementsRow['stockid'] . ' - ' . $RequirementsRow['description'] .'</td>';
			}
			$IssuedAlreadyResult = DB_query("SELECT SUM(-qty) FROM stockmoves
							WHERE stockmoves.type=28
							AND stockid='" . $RequirementsRow['stockid'] . "'
							AND reference='" . DB_escape_string($_REQUEST['WO']) . "'",
						$db);
			$IssuedAlreadyRow = DB_fetch_row($IssuedAlreadyResult);
		
			// bowikaxu - get req qty if fixed
			$sql = "SELECT rh_type FROM bom WHERE component = '".$RequirementsRow['stockid']."' AND parent = '".DB_escape_string($_REQUEST['StockID'])."'";
			$rh_typeres = DB_query($sql,$db);
			$rh_type = DB_fetch_array($rh_typeres);
			if($rh_type['rh_type']==0){
				echo '<td align="right">' . number_format($WORow['qtyreqd']*$RequirementsRow['qtypu'],$RequirementsRow['decimalplaces']) . '</td>
					<td align="right">' . number_format($IssuedAlreadyRow[0],$RequirementsRow['decimalplaces']) . '</td></tr>';
			}else {
				//$WORow['qtyreqd']*
				echo '<td align="right">' . number_format($RequirementsRow['qtypu'],$RequirementsRow['decimalplaces']).'('._('Fixed').')'. '</td>
					<td align="right">' . number_format($IssuedAlreadyRow[0],$RequirementsRow['decimalplaces']) . '</td></tr>';
			}
		}

		echo '</table><br>';
		
		// bowikaxu realhost March 2008 - view work order gl costing
		//set up options for selection of the item to be issued to the WO
		echo '<table border=1><tr><td colspan=7 class="tableheader">' . _('General Ledger').' '._('Costing') . '</td></tr>';
		echo '<tr><td class="tableheader">' . _('Type') . '</td>
			<td class="tableheader">' . _('Num.') . '</td>
			<td class="tableheader">' . _('Account') . '</td>
			<td class="tableheader">' . _('Receipt') . '</td>
			<td class="tableheader">' . _('Issue') . '</td>
			<td class="tableheader">' . _('Variance') . '</td>
			<td class="tableheader">' . _('Narrative') . '</td>
			</tr>';

		$RequirmentsResult = DB_query("SELECT rh_wogl.*, gltrans.account, chartmaster.accountname FROM
					rh_wogl
					INNER JOIN gltrans ON rh_wogl.type = gltrans.type
					AND rh_wogl.transno = gltrans.typeno
					INNER JOIN chartmaster ON gltrans.account = chartmaster.accountcode
					WHERE rh_wogl.wo=" . $_REQUEST['WO'],
					$db);
		$totalrec = 0;
		$totalissue = 0;
		$totalvar = 0;
		while ($RequirementsRow = DB_fetch_array($RequirmentsResult)){
			$amount = $RequirementsRow['amount'];
			if($RequirementsRow['type']==26){ // receipt
				
				echo "<TR>
				<TD ALIGN=left>".$RequirementsRow['type']."</TD>
				<TD ALIGN=left>".$RequirementsRow['transno']."</TD>
				<TD ALIGN=left>".$RequirementsRow['accountname'].' ['.$RequirementsRow['accountname'].']'."</TD>
				<TD ALIGN=right>".number_format($amount,2)."</TD>
				<TD></TD>
				<TD></TD>
				<TD ALIGN=left>".$RequirementsRow['narrative']."</TD>
				</TR>";
				$totalrec += $amount;
			}else if($RequirementsRow['type']==28) { // issue
				
				echo "<TR>
				<TD ALIGN=left>".$RequirementsRow['type']."</TD>
				<TD ALIGN=left>".$RequirementsRow['transno']."</TD>
				<TD ALIGN=left>".$RequirementsRow['accountname'].' ['.$RequirementsRow['accountname'].']'."</TD>
				<TD></TD>
				<TD ALIGN=right>".number_format($amount,2)."</TD>
				<TD></TD>
				<TD ALIGN=left>".$RequirementsRow['narrative']."</TD>
				</TR>";
				$totalissue += $amount;
			}else { // variance
				
				echo "<TR>
				<TD ALIGN=left>".$RequirementsRow['type']."</TD>
				<TD ALIGN=left>".$RequirementsRow['transno']."</TD>
				<TD ALIGN=left>".$RequirementsRow['accountname'].' ['.$RequirementsRow['accountname'].']'."</TD>
				<TD></TD>
				<TD></TD>
				<TD ALIGN=right>".number_format($amount,2)."</TD>
				<TD ALIGN=left>".$RequirementsRow['narrative']."</TD>
				</TR>";
				$totalvar += $amount;
			}
			
		}
		echo "<TR><TD colspan=3 class='tableheader'><B>"._('Total')."</B></TD>
		<TD><B>".number_format($totalrec,2)."</B></TD>
		<TD><B>".number_format($totalissue,2)."</B></TD>
		<TD><B>".number_format($totalvar,2)."</B></TD>
		<TD><B>"._('Balance').': '.number_format($totalrec-$totalissue-$totalvar,2)."<B></TD>
		</TR>";
		echo '</table>';

include('includes/footer.inc');

?>