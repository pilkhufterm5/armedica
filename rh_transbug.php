<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
// bowikaxu - script to test GetNextTransNo() function

$PageSecurity = 2;
include('includes/session.inc');
$title = _('REALHOST SCRIPT - TEST GetNextTransNo()');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if(isset($_POST['OK'])){
$transno = 0;
$transno2= 0;
for($i=0;$i<=15;$i++){
	
	$transno = GetNextTransNo(10,$db);
	echo $transno." -- ".$i."<HR>";
	if($transno == $transno2){
		echo "ANTERIOR DUPLICADA"."<HR>";
	}
	$transno2 = $transno;
	
}
}else {

	echo "<FORM NAME=form METHOD=POST ACTION=rh_transbug.php>";
	echo "<BR><CENTER><INPUT TYPE=SUBMIT NAME=OK VALUE=OK></CENTER>";
	echo "</FORM>";
	
}
include('includes/footer.inc');

?>