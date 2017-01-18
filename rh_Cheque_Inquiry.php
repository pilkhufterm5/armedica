<?php

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */

/*
bowikaxu - realhost
View Recept transactions by date

*/

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=2;

include('includes/session.inc');
$title = _('Cheque Inquiry');
include('includes/header.inc');
echo "<BR><CENTER><B>"._('Cheque Inquiry')."</B></CENTER><BR>";


if(isset($_POST['submit'])){
	
	echo "<FORM NAME='menu' METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
	$error = 0;
	if(!isset($_POST['fromdate']) || $_POST['fromdate']==''){
		
		echo "<FONT COLOR=red>ERROR: "._('Invalid From Date')."</FONT><BR>";
		$error = 1;
		
	}
	if(!isset($_POST['todate']) || $_POST['todate']==''){
		
		echo "<FONT COLOR=red>ERROR: "._('Invalid To Date')."</FONT><BR>";		
		$error = 1;
		
	}
	
	if(!$error){
		
		$sql = "SELECT gltrans.*,
				banktrans.ref,
				systypes.typename
				FROM banktrans,gltrans, systypes
				WHERE gltrans.trandate >= '".$_POST['fromdate']."' AND gltrans.trandate <= '".$_POST['todate']."'
				AND gltrans.account =".$_POST['account']."
				AND banktrans.bankact =".$_POST['account']."
				AND banktrans.transno = gltrans.typeno
				AND systypes.typeid = gltrans.type
				AND banktrans.type = gltrans.type
				ORDER BY gltrans.typeno, gltrans.trandate ASC";
		
		$res = DB_query($sql, $db);
		
		echo "<CENTER><TABLE>";
		while ($info = DB_fetch_array($res)) {
			
			echo "<TR><TD COLSPAN=6 ALIGN=center><STRONG>".$info['typename']." ".$info['typeno']."</STRONG></TD></TR>";
			echo "<TR>";
			
			echo "<TD bgcolor=white>"._('Transaction')."</TD>";
			echo "<TD bgcolor=white>"._('Date')."</TD>";
			echo "<TD bgcolor=white>"._('Narrative')."</TD>";
			echo "<TD bgcolor=white>"._('Description')."</TD>";
			echo "<TD bgcolor=white>"._('Amount')."</TD>";
			
			echo "</TR>";
			echo "<TR>";
			
			echo "<TD>".$info['typeno']."</TD>";
			echo "<TD>".$info['trandate']."</TD>";
			echo "<TD>".$info['narrative']."</TD>";
			echo "<TD>".$info['ref']."</TD>";
			echo "<TD>".$info['amount']."</TD>";
			
			echo "</TR>";
			
		}
		echo ""; 
	}ELSE {
		echo "<INPUT TYPE=submit NAME='regresar' value='Regresar'>";
	}
	echo "</FORM>";
}else {

if(!isset($_POST['fromdate']) || !is_Date($_POST['fromdate']) || $_POST['fromdate']==''){
	$_POST['fromdate']=Date('Y/m/d');
}

if(!isset($_POST['todate']) || !is_Date($_POST['todate']) || $_POST['todate']==''){
	$_POST['todate']=Date('Y/m/d');
}
echo "<FORM NAME='menu' METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
echo "<CENTER><TABLE>";

$sql = "SELECT gltrans.account, chartmaster.accountname FROM gltrans, chartmaster WHERE gltrans.type=12 
		AND gltrans.account = chartmaster.accountcode GROUP BY gltrans.account";
$res = DB_query($sql,$db);

echo "<TR><TD>"._('Account')."</TD><TD><SELECT NAME='account'>";

while($account = DB_fetch_array($res)){
	
	echo "<OPTION VALUE='".$account['account']."'>".$account['accountname'];
	
}
echo "</SELECT></TD></TR>";

echo "<TR><TD>"._('From Date').": </TD><TD><INPUT TYPE=text NAME='fromdate' value='".$_POST['fromdate']."'></TD></TR>";
echo "<TR><TD>"._('To Date').": </TD><TD><INPUT TYPE=text NAME='todate' value='".$_POST['todate']."'></TD></TR>";

echo "<TR><TD COLSPAN=2 ALIGN=center><INPUT TYPE=submit name='submit'></TD></TR>";

echo "</TABLE></CENTER></FORM>";

}

include('includes/footer.inc');

?>