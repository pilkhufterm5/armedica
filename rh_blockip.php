<?php
/* $Revision: 1 $ */

/**************************************************************************
* Jorge Garcia 14/Nov/2008 
* Archivo creado para lbloqueo de ips
***************************************************************************/
//Seguridad de la pagina
$PageSecurity = 15;

include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('IP');
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
	//Verificacion del codigo y del nombre
	if ($_POST['ip'] == '' OR $_POST['ip'] == ' ' OR $_POST['ip'] == '   ' OR $_POST['ip'] == '    ') {
		$InputError = 1;
		prnMsg(_('La IP no puede estar vacia'),'error');
		$Errors[$i] = 'ErrorIP';
		$i++;		
	}
	$rh_ipex = array();
	$rh_ipex = explode('.',$_POST['ip']);
	foreach($rh_ipex as $valor){
		if (strlen($valor) > 3 OR strlen($valor) < 1) {
			$InputError = 1;
			prnMsg(_('La IP no es valida, Ejemplo: 255.255.255.255'),'error');
			$Errors[$i] = 'ErrorIP';
			$i++;
		}
	}
	$rh_cuentas = sizeof($rh_ipex);
	if($rh_cuentas < 4){
		$InputError = 1;
		prnMsg(_('La IP no es valida, Ejemplo: 255.255.255.255'),'error');
		$Errors[$i] = 'ErrorIP';
		$i++;
	}
	//Querys para insertar
	if ($InputError !=1) {
		$checkSql = "SELECT count(*) FROM rh_blockip WHERE ip = '" . $_POST['ip'] . "'";
		$checkresult = DB_query($checkSql,$db);
		$checkrow = DB_fetch_row($checkresult);
		if ($checkrow[0] > 0) {
			$InputError = 1;
			prnMsg( _('La IP') . $_POST['ip'] . _(' already exist.'),'error');
		} else {
			$sql = "INSERT INTO rh_blockip (ip, fechaalta , usuario) VALUES ('".$_POST['ip']."','".date('Y-m-d')."','".$_SESSION['UserID']."')";
			$result = DB_query($sql,$db);
			$msg = _('La IP') . ' ' . $_POST['ip'] .  ' ' . _('has been inserted');
		}
	}

//Query para borrar
} elseif (isset($_GET['delete']) ) {
	$sql="DELETE FROM rh_blockip WHERE ip = '$SelectedType'";
	$ErrMsg = _('The record could not be deleted ');
	$result = DB_query($sql,$db,$ErrMsg);
	prnMsg(_('La IP') . ' ' . _('has been deleted') ,'success');
	unset ($SelectedType);
	unset($_GET['delete']);
}

if (!isset($SelectedType)){

	$sql = 'SELECT * FROM rh_blockip';
	$result = DB_query($sql,$db);

	echo '<P><CENTER><TABLE BORDER=1>';
	echo "<tr>
		<TD CLASS='tableheader'>" . _('IP') . "</TD>
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
			<td><a href='%sSelectedType=%s&delete=yes' onclick=\"return confirm('" . _('Are you sure you wish to delete this?') . "');\">" . _('Delete') . "</td>
			</tr>",
			$myrow[0],
			$_SERVER['PHP_SELF'] . '?' . SID, $myrow[0]);
		}
		echo '</table></CENTER></P>';
}

if (isset($SelectedType)) {	
	echo '<CENTER><P><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '">' . _('Show All') . '</A></CENTER><p>';
}
if (!isset($_GET['delete'])) {

	echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
	echo '<CENTER><FONT SIZE=4 COLOR=blue><B><U>' . _('Setup') . '</B></U></FONT>';
	echo '<P><TABLE BORDER=1>';
	echo '<TD><TABLE>';
	echo "<CENTER><TABLE><TR><TD>" . _('IP') . ":</TD><TD><input type=text " . (in_array('ErrorIP',$Errors) ? 'class="inputerror"' : '' ) ." name='ip' SIZE=17 MAXLENGTH=15></TD></TR>";
   	echo '</TABLE>';
   	echo '</TD></TR></TABLE><br><FONT color=blue size=3><B>Tu IP: '.$_SERVER['REMOTE_ADDR'].'</B></font>';
	echo '<P><INPUT TYPE=submit NAME=submit VALUE="' . _('Accept') . '"><INPUT TYPE=submit NAME=Cancel VALUE="' . _('Cancel') . '"></CENTER>';
	echo '</FORM>';

} 
/**************************************************************************
* Jorge Garcia Fin Modificacion
***************************************************************************/
include('includes/footer.inc');
?>