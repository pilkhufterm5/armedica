<?php
/* webERP Revision: 1.19 $ */
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-04-18 13:28:12 -0500 (Fri, 18 Apr 2008) $
 * $Rev: 206 $
 */
$PageSecurity = 1;

include('includes/session.inc');
$title = _('Factor de Venta Mantenimiento');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

$rh_AllowSalesFactor = array('realhost','DIRECCIONGENERAL','GERENCIAGENERAL','tractoref','ADMINALLENDE');
if (!in_array($_SESSION['UserID'],$rh_AllowSalesFactor)){
    prnMsg('Usted no tiene permitido modificar los factores de venta','error');
    include("includes/footer.inc");
    exit;
}

if(isset($_GET['SelectedFactor'])){
	$SelectedFactor=$_GET['SelectedFactor'];
}else if(isset($_POST['SelectedFactor'])){
	$SelectedFactor=$_POST['SelectedFactor'];
}
if (isset($_POST['submit'])) {

    //initialise no input errors assumed initially before we test
    $InputError = 0;

    /* actions to take once the user has clicked the submit button
    ie the page has called itself with some user input */

    //first off validate inputs are sensible
	$i=1;

	$sql="SELECT *
			FROM rh_sales_factors WHERE name='".$_POST['name']."'";
	$result=DB_query($sql, $db);
	$myrow=DB_fetch_row($result);

	if ($myrow[1]!=0) {
		$InputError = 1;
		prnMsg( _('EL factor ya existe, favor de darle mantenimiento'),'error');
		$Errors[$i] = 'Nombre';
		$i++;
	}
    if (strlen($_POST['name']) < 3) {
        $InputError = 1;
        prnMsg(_('El nombre del factor de venta debe ser mayor a 3 caracteres'),'error');
		$Errors[$i] = 'Nombre';
		$i++;
    }
	if (!is_numeric($_POST['factor'])){
        $InputError = 1;
       prnMsg(_('The exchange rate must be numeric'),'error');
		$Errors[$i] = 'ExchangeRate';
		$i++;
    }
    if (isset($SelectedFactor) AND $InputError !=1) {

        /*SelectedCurrency could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
        $sql = "UPDATE rh_sales_factors SET
					factor='" . $_POST['factor'] . "',
					name='". $_POST['name']. "'
					WHERE id = '" . $SelectedFactor . "'";

        $msg = _('El factor ha sido actualizado');
    } else if ($InputError !=1) {

    /*Selected currencies is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new payment terms form */
    	$sql = "INSERT INTO rh_sales_factors (`name`,factor)  VALUE('" . $_POST['name'] . "',
					'" . $_POST['factor'] . "')";

    	$msg = _('El factor a sido agregado');
    }
    //run the SQL from either of the above possibilites
    $result = DB_query($sql,$db);
    if ($InputError!=1){
    	prnMsg( $msg,'success');
    }
    unset($SelectedFactor);
    unset($_POST['name']);
    unset($_POST['factor']);
}

if (!isset($SelectedFactor)||$SelectedFactor='') {

/* It could still be the second time the page has been run and a record has been selected for modification - SelectedCurrency will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
then none of the above are true and the list of payment termss will be displayed with
links to delete or edit each. These will call the same page again and allow update/input
or deletion of the records*/

    $sql = 'SELECT * FROM rh_sales_factors';
    $result = DB_query($sql, $db);

    echo '<CENTER><table border=1>';
    echo '<tr>
    		<th>' . _('Nombre') . '</th>
    		<th>' . _('Factor') . '</th>
			</tr>';

    $k=0; //row colour counter
    /*Get published currency rates from Eurpoean Central Bank */
    while ($myrow = DB_fetch_row($result)) {
        if ($myrow[1]==$FunctionalCurrency){
            echo '<tr bgcolor=#FFbbbb>';
        } elseif ($k==1){
            echo '<tr class="EvenTableRows">';
            $k=0;
        } else {
            echo  '<tr class="OddTableRows">';;
            $k++;
        }
		echo "<td>".$myrow[1]."</td><td>".number_format($myrow[2],4)."</td><td><a href='".$_SERVER['PHP_SELF'] . "?SelectedFactor=".$myrow[0]."'>Editar</a></td>";
    } //END WHILE LIST LOOP
    echo '</table></CENTER><BR>';
} //end of ifs and buts!

if (!isset($_GET['delete'])) {
    if(isset($_GET['SelectedFactor'])){
	$SelectedFactor=$_GET['SelectedFactor'];
}else if(isset($_POST['SelectedFactor'])){
	$SelectedFactor=$_POST['SelectedFactor'];
}

    echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
    if (isset($SelectedFactor) AND $SelectedFactor!='') {
        //editing an existing payment terms

        $sql = "SELECT *
				FROM rh_sales_factors
				WHERE id='" . $SelectedFactor . "'";
        echo $sql;
        $ErrMsg = _('Se produjo un error con los factores');;
		$result = DB_query($sql, $db, $ErrMsg);

        $myrow = DB_fetch_array($result);

        $_POST['name'] = $myrow[1];
        $_POST['factor']  = $myrow[2];

        echo '<input type="hidden" name="SelectedFactor" VALUE="' . $SelectedFactor . '">';
        echo '<center><table><tr>
			<td>' ._('Nombre Factor') . ':</td>
			<td><input  type="Text" name="name" value="' . $_POST['name'] . '" size=10 maxlength=15></td></tr>';
    echo '<TR><TD>'._('Factor').':</TD>';
    echo '<TD>';
	if (!isset($_POST['factor'])) {$_POST['factor']='';}
    echo '<INPUT  TYPE="text" name="factor" SIZE=20 MAXLENGTH=20 VALUE="' . $_POST['factor'] . '">';
    echo '</TD></TR>';
    echo '</TABLE>';

    echo '<CENTER><input type="Submit" name="submit" value='._('Enter Information').'>';

    echo '</FORM>';
    }else{
        echo '<center><table><tr>
			<td>' ._('Nombre Factor') . ':</td>
			<td><input  type="Text" name="name" value="" size=10 maxlength=15></td></tr>';
    echo '<TR><TD>'._('Factor').':</TD>';
    echo '<TD>';
    echo '<INPUT  TYPE="text" name="factor" SIZE=20 MAXLENGTH=20 VALUE="">';
    echo '</TD></TR>';
    echo '</TABLE>';

    echo '<CENTER><input type="Submit" name="submit" value='._('Enter Information').'>';

    echo '</FORM>';

} //end if record deleted no point displaying form to add record
}

include('includes/footer.inc');
?>
