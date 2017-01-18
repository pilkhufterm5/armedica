<?php
/* $Revision: 1 $ */

/**************************************************************************
* Jorge Garcia
* 28/Ene/2009 Alta de Cuentas ref. especial
***************************************************************************/
//Seguridad de la pagina
$PageSecurity = 15;

include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('Account').'s '._('Special Ref');
include('includes/header.inc');

//Condiciones para checar si se selecciono alguna
if (isset($_POST['SelectedType'])){
	$SelectedType = ($_POST['SelectedType']);
} elseif (isset($_GET['SelectedType'])){
	$SelectedType = ($_GET['SelectedType']);
}
//Si se creo el arreglo de errores se borra
if (isset($Errors)) {
	unset($Errors);
}
//Declaramos el arreglo para los errores
$Errors = array();

if (isset($_POST['submit'])) {
	$InputError = 0;
	$i=1;

	//Querys para insertar
	$checkSql = "SELECT count(*) FROM rh_refaccounts WHERE account = '" . $_POST['account'] . "'";
	$checkresult = DB_query($checkSql,$db);
	$checkrow = DB_fetch_row($checkresult);
	if ($checkrow[0] > 0) {
		$InputError = 1;
		prnMsg( _('La cuenta') . $_POST['account'] . _(' already exist.'),'error');
	} else {
		$sql = "INSERT INTO rh_refaccounts (account) VALUES ('".$_POST['account']."')";
		$result = DB_query($sql,$db);
		$msg = _('La cuenta') . ' ' . $_POST['account'] .  ' ' . _('has been inserted');
	}

//Query para borrar	
} elseif (isset($_GET['delete']) ) {
	$sql="DELETE FROM rh_refaccounts WHERE account = '$SelectedType'";
	$ErrMsg = _('The record could not be deleted ');
	$result = DB_query($sql,$db,$ErrMsg);
	prnMsg(_('La cuenta') . ' ' . _('has been deleted') ,'success');
	unset ($SelectedType);
	unset($_GET['delete']);
}

if (!isset($SelectedType)){

	$sql = 'SELECT rh_refaccounts.account, chartmaster.accountname FROM rh_refaccounts, chartmaster WHERE rh_refaccounts.account = chartmaster.accountcode ORDER BY rh_refaccounts.account ASC';
	$result = DB_query($sql,$db);

	echo '<P><CENTER><TABLE BORDER=1>';
	echo "<tr>
		<TD CLASS='tableheader'>" . _('Account') . "</TD>
		<TD CLASS='tableheader'>" . _('Name') . "</TD>
	</TR>";

	$k=0;

	while ($myrow = DB_fetch_row($result)) {
		if ($k==1){
			echo "<TR BGCOLOR='#CCCCCC'>";
			$k=0;
		} else {
			echo "<TR BGCOLOR='#EEEEEE'>";
			$k=1;
		}

		printf("<td>%s</td>
			<td>%s</td>
			<td><a href='%sSelectedType=%s&delete=yes' onclick=\"return confirm('" . _('Are you sure you wish to delete this?') . "');\">" . _('Delete') . "</td>
			</tr>",
			$myrow[0],
			$myrow[1],
			$_SERVER['PHP_SELF'] . '?' . SID, $myrow[0]);
		}
		echo '</table></CENTER></P><P>';;
		echo '</P>';
}

if (isset($SelectedType)) {
	echo '<CENTER><P><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '">' . _('Show All') . '</A></CENTER><p>';
}
if (!isset($_GET['delete'])) {

	echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
	echo '<CENTER><FONT SIZE=4 COLOR=blue><B><U>' . _('Setup') . '</B></U></FONT>';
	echo '<P><TABLE BORDER=1>';
	echo '<TD><TABLE>';
	echo "<CENTER><TABLE><TR><TD>" . _('Account') . ":</TD><TD><SELECT name='account'>";
	$sql = 'SELECT accountcode, accountname FROM chartmaster WHERE accountcode NOT IN (SELECT account FROM rh_refaccounts) ORDER BY accountcode ASC';
	$result = DB_query($sql,$db);
	while ($myrow = DB_fetch_array($result)) {
		echo "<OPTION VALUE='".$myrow['accountcode']."'>[".$myrow['accountcode']."] ".$myrow['accountname'];
	}
	echo "</SELECT></TD></TR>";
   	echo '</TABLE>';
   	echo '</TD></TR></TABLE>';
	echo '<P><INPUT TYPE=submit NAME=submit VALUE="' . _('Accept') . '"><INPUT TYPE=submit NAME=Cancel VALUE="' . _('Cancel') . '"></CENTER>';
	echo '</FORM>';

} 
/**************************************************************************
* Jorge Garcia Fin Modificacion
***************************************************************************/
include('includes/footer.inc');
?>