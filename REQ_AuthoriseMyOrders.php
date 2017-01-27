<?php

/* $Id: PO_AuthoriseMyOrders.php 3942 * Ruben Flores Barrios 09/Dic/2016 tim_schofield $*/
$PageSecurity = 4;
include('includes/session.inc');

$title = _('Authorise Requisition');

include('includes/header.inc');

echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/transactions.png" title="' . $title .
	 '" alt="">' . ' ' . $title . '</p>';

$emailsql="SELECT email FROM www_users WHERE userid='".$_SESSION['UserID']."'";
$emailresult=DB_query($emailsql, $db);
$emailrow=DB_fetch_array($emailresult);

if (isset($_POST['updateall'])) {
	/*print_r($_POST);exit;*/
	foreach ($_POST as $key => $value) {
		if (substr($key,0,6)=='status') {

			$orderno=substr($key,6);
			$status=$_POST['status'.$orderno];


			$comment=date($_SESSION['DefaultDateFormat']).' - '._('Authorised by').' '.'<a href="mailto:'.
				$emailrow['email'].'">'.$_SESSION['UserID'].'</a><br>'.$_POST['comment'];
			$sql="UPDATE wrk_requisicion
				SET status='".$status."',
				stat_comment='".$comment."',
				fecha_autorizacion='".date('Y-m-d H:i:s')."',
				fecha_rechazo='".date('Y-m-d H:i:s')."',
				allowprint=1
				WHERE reqid='".$orderno."'";
			$result=DB_query($sql, $db);
		}
	}
}

/* Retrieve the purchase order header information
NECESITAMOS HACER UN INNER JOIN DE SOLICITANTE Y AUTORIZADOR
*/
$sql="SELECT wrkr.*,
			wrkr.reqdate,
			wrkr.deliverydate,
			wwwu.realname,
			wwwu.email,
			wrks.solicitantecc AS solicitantebase,
			wrka.autorizadorcc AS autorizadorbase
			FROM wrk_requisicion wrkr
		LEFT JOIN www_users wwwu
			ON wwwu.userid=wrkr.initiator
		INNER JOIN wrk_solicitantecc wrks
			ON wrkr.solicitantecc_id=wrks.solicitantecc_id
		INNER JOIN wrk_autorizadorescc wrka
			ON wrkr.autorizadorcc_id=wrka.autorizadorcc_id
	WHERE wrkr.status='Pending' AND wrka.autorizadorcc='".$_SESSION['UserID']."'";
$result=DB_query($sql, $db);

echo '<form method=post action="' . $_SERVER['PHP_SELF'] . '">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class="selection"><tr>';

/* Create the table for the purchase order header */
echo '<th>'._('Requisicion #').'</th>';
echo '<th>'._('Solicitante').'</th>';
echo '<th>'._('Autorizador').'</th>';
echo '<th>'._('Req Date').'</th>';
echo '<th>'._('Status').'</th>';
echo '</tr>';

while ($myrow=DB_fetch_array($result)) {
	echo '<tr>';
	echo '<td>'.$myrow['reqid'].'</td>';
	echo '<td>'.$myrow['solicitantebase'].'</td>';
	echo '<td>'.$myrow['autorizadorbase'].'</td>';
	echo '<td>'.$myrow['reqdate'].'</td>';
	echo '<td><select name=status'.$myrow['reqid'].'>';
		echo '<option selected value="Pending">'._('Pending').'</option>';
		echo '<option value="Authorised">'._('Authorised').'</option>';
		echo '<option value="Rejected">'._('Rejected').'</option>';
		echo '<option value="Cancelled">'._('Cancelled').'</option>';
		echo '</select></td>';
	echo '</tr>';	
	// en base a la requsicion obtenemos los productos
	$linesql="SELECT wrk_requisiciondetalle.*,
					stockmaster.description
				FROM wrk_requisiciondetalle
				LEFT JOIN stockmaster
				ON stockmaster.stockid=wrk_requisiciondetalle.itemcode
			WHERE reqno='".$myrow['reqid'] . "'";
		$lineresult=DB_query($linesql, $db);

		echo '<tr><td></td><td colspan=5 align=left>
		<table class="selection table table-bordered" align=left>';
		echo '<th>'._('Product').'</th>';
		echo '<th>'._('Quantity Req').'</th>';
		echo '</tr>';
        
		while ($linerow=DB_fetch_array($lineresult)) {
			echo '<tr>';
			echo '<td>'.$linerow['description'].'</td>';
			echo '<td class="number" style="text-align: right;" >'.number_format($linerow['quantityreq'],2).'</td>';
			
		} // end while order line detail
		echo "<tr> <td colspan='3'></td>";         
		echo "</tr>";
		echo '</table></td></tr>';
} //end while header loop
echo '</table>';
echo '<br><div class="centre"><input type="submit" name="updateall" value="' . _('Update'). '"></form>';

include('includes/footer.inc');
?>