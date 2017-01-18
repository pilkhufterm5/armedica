<?php
/* $Revision: 269 $ */

$PageSecurity = 3;

include('includes/session.inc');
$title = _('Pedimentos de importación');
include('includes/header.inc');

if (isset($_GET['pedimento'])){
	$pedimento =strtoupper($_GET['pedimento']);
} elseif(isset($_POST['pedimento'])){
	$pedimento =strtoupper($_POST['pedimento']);
}

if (isset($Errors)) {
	unset($Errors);
}
	
$Errors = array();	

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */
	$i=1;

	//first off validate inputs sensible

	if (strlen($_POST['descripcion']) < 10) {
		$InputError = 1;
		prnMsg(_('La descripción del pedimento debe ser mayor a 10 caracteres para facilitar su identificación '),'error');
		$Errors[$i] = 'Decripcion';
		$i++;		
	} elseif (strlen($_POST['nopedimento'])!=14 OR $_POST['nopedimento']=='') {
		$InputError = 1;
		prnMsg(_('El numero de pedimento debe ser de 14 caracteres, si no los tiene rellanar con 0 para completarlos o bien colocar los ultimos 14'),'error');
		$Errors[$i] = 'No Pedimento';
		$i++;		
	} elseif (strlen($_POST['fecha']) != 10) {
		$InputError = 1;
		prnMsg(_('La fecha debe ser de 10 caracteres con el siguiente formato aaaa-mm-dd'),'error');
		$Errors[$i] = 'fecha';
		$i++;		
	} elseif (strlen($_POST['aduana']) > 20) {
		$InputError = 1;
		prnMsg(_('El nombre de la aduana es demaciado extenso'),'error');

	}
      var_dump($_POST);
	if (isset($pedimento) AND $InputError !=1) {

		/*SelectedSaleperson could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/

		$sql = "UPDATE rh_pedimento SET descripcion='" . $_POST['descripcion'] . "',
						nopedimento=" . $_POST['nopedimento'] . ",
						fecha='" . $_POST['fecha'] . "',
						aduana='" . $_POST['aduana'] . "'
				WHERE pedimentoid = '$pedimento'";

		$msg = _('el pedimento de aduana fue modificado');
	} elseif ($InputError !=1) {

	/*Selected group is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new Sales-person form */

		$sql = "INSERT INTO rh_pedimento (descripcion,
						nopedimento,
						fecha,
						aduana,
						userid,
						create_date)
				VALUES ('" .$_POST['descripcion'] . "',
					'" . $_POST['nopedimento'] . "',
					'" . $_POST['fecha'] . "',
					'" . $_POST['aduana'] . "',
                    '" . $_SESSION['UserID'] . "',now())";

		$msg = _('se ha agregado el ') . ' ' . $_POST['descripcion'];
	}
	if ($InputError !=1) {
		//run the SQL from either of the above possibilites
		$ErrMsg = _('The insert or update of the salesperson failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$result = DB_query($sql,$db,$ErrMsg, $DbgMsg);

		prnMsg($msg , 'success');

		unset($pedimento);
		unset($_POST['descripcion']);
		unset($_POST['nopedimento']);
		unset($_POST['fecha']);
		unset($_POST['aduana']);
		unset($_POST['pedimentoid']);

	}

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

// PREVENT DELETES IF DEPENDENT RECORDS IN 'DebtorsMaster'

	$sql= "SELECT COUNT(*) FROM rh_pedimento WHERE  pedimentoid=$pedimento";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_array($result);
	if ($myrow[0]>0) {
			$sql="DELETE FROM rh_pedimento WHERE  pedimentoid='$pedimento'";
			$ErrMsg = _('El pedimento no se pudo eliminar');
			$result = DB_query($sql,$db,$ErrMsg);

			prnMsg(_('Pedimento') . ' ' . $pedimento . ' ' . _('fue eliminado'),'success');
			unset ($pedimento);
			unset($delete);
	} else {
		prnMsg(_('Imposible eliminar pedimento porque este ya fue utilizado'),'error');  
	} //end if Sales-person used in GL accounts

}

if (!isset($pedimento)) {

/* It could still be the second time the page has been run and a record has been selected for modification - SelectedSaleperson will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
then none of the above are true and the list of Sales-persons will be displayed with
links to delete or edit each. These will call the same page again and allow update/input
or deletion of the records*/

	$sql = "SELECT *
		FROM rh_pedimento";
	$result = DB_query($sql,$db);

	echo '<CENTER><TABLE BORDER=1>';
	echo "<tr><td class='tableheader'>" . _('Code') . "</td>
		<td class='tableheader'>" . _('Descripci&oacute;n') . "</td>
		<td class='tableheader'>" . _('No. Pedimento') . "</td>
		<td class='tableheader'>" . _('Fecha') . "</td>
		<td class='tableheader'>" . _('Aduana') . "</td>
        </tr>";

	while ($myrow=DB_fetch_row($result)) {


	printf("<tr>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td><a href=\"%spedimento=%s\">". _('Edit') . "</a></td>
		<td><a href=\"%spedimento=%s&delete=1\">" . _('Delete') . "</a></td>
		</tr>",
		$myrow[0],
		$myrow[1],
		$myrow[2],
		$myrow[3],
		$myrow[4],
		$_SERVER['PHP_SELF'] . '?' . SID . '&',
		$myrow[0],
		$_SERVER['PHP_SELF'] . '?' . SID . '&',
		$myrow[0]);

	} //END WHILE LIST LOOP
	echo '</table></CENTER>';
} //end of ifs and buts!

if (isset($pedimento)) {
	echo "<CENTER><A HREF='" . $_SERVER['PHP_SELF'] . '?' . SID . "'>" . _('Ver todos los pedimentos') . "</A></CENTER>";
}

if (! isset($_GET['delete'])) {

	echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';

	if (isset($pedimento)) {
		//editing an existing Sales-person

		$sql = "SELECT *
			FROM rh_pedimento
			WHERE pedimentoid='$pedimento'";

		$result = DB_query($sql, $db);
		$myrow = DB_fetch_array($result);

		$_POST['descripcion'] = $myrow['descripcion'];
		$_POST['nopedimento'] = $myrow['nopedimento'];
		$_POST['fecha'] = $myrow['fecha'];
		$_POST['aduana'] = $myrow['aduana'];
		$_POST['pedimentoid']  = $myrow['pedimentoid'];


		echo "<INPUT TYPE=HIDDEN NAME='pedimento' VALUE='" . $pedimento . "'>";
		//echo "<INPUT TYPE=HIDDEN NAME='SalesmanCode' VALUE='" . $_POST['SalesmanCode'] . "'>";
		echo '<CENTER><TABLE> <TR><TD>' . _('id') . ':</TD><TD>';
		echo $_POST['pedimentoid'] . '</TD></TR>';

	} else { //end of if $SelectedSaleperson only do the else when a new record is being entered

		echo '<CENTER><TABLE><TR><TD>' . _('Id') . ":</TD>
			<TD></TD></TR>";
	}
	if (!isset($_POST['descripcion'])){
	  $_POST['descripcion']='';
	}
	if (!isset($_POST['nopedimento'])){
	  $_POST['nopedimento']='';
	}
	if (!isset($_POST['fecha'])){
	  $_POST['fecha']='';
	}
	if (!isset($_POST['aduana'])){
	  $_POST['aduana']=0;
	}
	if (!isset($_POST['pedimentoid'])){
	  $_POST['pedimentoid']=0;
	}


	echo '<TR><TD>' . _('Descripci&oacute;n') . ":</TD><TD><INPUT TYPE='Text' name='descripcion' SIZE=50 MAXLENGTH=50 VALUE='" . $_POST['descripcion'] . "'></TD></TR>";
	echo '<TR><TD>' . _('No Pedimento') . ":</TD><TD><INPUT TYPE='Text' name='nopedimento' SIZE=50 MAXLENGTH=15 VALUE='" . $_POST['nopedimento'] . "'></TD></TR>";
	echo '<TR><TD>' . _('Fecha') . ":</TD><TD><INPUT TYPE='Text' name='fecha' SIZE=50 MAXLENGTH=10 VALUE=" . $_POST['fecha'] . '></TD></TR>';
	echo '<TR><TD>' . _('Aduana') . ":</TD><TD><INPUT TYPE='Text' name='aduana' SIZE=50 MAXLENGTH=15 VALUE=" . $_POST['aduana'] . '></TD></TR>';


	echo '</TABLE>';

	echo "<CENTER><input type='Submit' name='submit' value='" . _('Enter Information') . "'></CENTER>";

	echo '</FORM>';

} //end if record deleted no point displaying form to add record


include('includes/footer.inc');
?>