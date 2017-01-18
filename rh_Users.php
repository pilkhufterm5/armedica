<?php

/* webERP Revision: 1.9 $ 

bowikaxu - realhost
Deshabilitar usuarios de manera automatica
*/

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:39:36 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 14 $
 */

include('includes/SQL_CommonFunctions.inc');
$PageSecurity=8;
include('includes/session.inc');
$title = _('Deshabilitar Usuarios');
include('includes/header.inc');

if(isset($_POST['Submit'])){
	
	echo "<CENTER>";
	$sql = "UPDATE www_users SET blocked=0";
	DB_query($sql,$db);
	if(isset($_POST['users'])){
	foreach($_POST['users'] as $i => $usrid){
		$sql = "UPDATE www_users SET blocked=1 WHERE userid='".$usrid."'";
		DB_query($sql,$db);
		echo "Usuarios: <STRONG>".$usrid.", ";
	}
	echo "</STRONG>Deshabilitados";
	}
	echo "</CENTER>";
}

$sql = "SELECT * FROM www_users WHERE userid !='".$_SESSION['UserID']."'";
$res = DB_query($sql,$db);
echo "<FORM NAME='users' METHOD=POST ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . ">";
echo '<BR><CENTER><TABLE CELLPADDING=2 BORDER=2>';
echo "<TR>
		<TD>Usuario</TD>
		<TD>Deshabilitar</TD>";
while($user=DB_fetch_array($res)){
	
	echo '<TR>
			<TD>'.$user['realname']."</TD>
			<TD>";
	if($user['blocked']==0){
		echo "<INPUT TYPE='CHECKBOX' NAME=users[] VALUE='".$user['userid']."'></TD>";
	}else {
		echo "<INPUT TYPE='CHECKBOX' NAME=users[] VALUE='".$user['userid']."' CHECKED></TD>";
	}
			
}
echo "</TABLE>";
echo "<INPUT TYPE='Submit' NAME='Submit' VALUE='Actualizar'>";

echo "</FORM>";

include('includes/footer.inc');

?>
