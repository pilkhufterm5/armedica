<?php
/* webERP Revision: 1.19 $ */
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-04-18 13:28:12 -0500 (Fri, 18 Apr 2008) $
 * $Rev: 206 $
 */
$PageSecurity = 1;

include('includes/session.inc');
$title = _('Storage Bins');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if(isset($_GET['SelectedStorage'])){
	$SelectedStorage=$_GET['SelectedStorage'];
}else if(isset($_POST['SelectedStorage'])){
	$SelectedStorage=$_POST['SelectedStorage'];
}else{
  unset($SelectedStorage);
}
if (isset($_POST['submit'])) {

    //initialise no input errors assumed initially before we test
    $InputError = 0;
    $InputErrorFull=0;

    /* actions to take once the user has clicked the submit button
    ie the page has called itself with some user input */

    //first off validate inputs are sensible
	$i=1;

	$sql="SELECT *
			FROM rh_storagebins WHERE description='".$_POST['descripcion']."'";
	$result=DB_query($sql, $db);
	$myrow=DB_fetch_row($result);

	if ($myrow[0]!=0) {
		$InputErrorFull = 1;
        if(!isset($SelectedStorage)){
		    prnMsg( _('La descripcion ya existe'),'error');
        }
		$Errors[$i] = 'Descripcion';
		$i++;
	}
    if (strlen($_POST['descripcion']) < 1) {
        $InputError = 1;
        prnMsg(_('La descripcion debe contener al menos 3 caracteres'),'error');
		$Errors[$i] = 'Descripcion';
		$i++;
    }

//     if (strlen($_POST['area']) < 1) {
//         $InputError = 1;
//         prnMsg(_('El area debe contener al menos 3 caracteres'),'error');
// 		$Errors[$i] = 'Area';
// 		$i++;
//     }
    
    if (strlen($_POST['pasillo']) < 1) {
        $InputError = 1;
        prnMsg(_('El pasillo debe contener al menos 1 caracter'),'error');
		$Errors[$i] = 'Pasillo';
		$i++;
    } 

    if (strlen($_POST['nivel']) < 1) {
        $InputError = 1;
        prnMsg(_('El nivel debe contener al menos 1 caracter'),'error');
		$Errors[$i] = 'Nivel';
		$i++;
    }
    /*
    if (strlen($_POST['seccion']) < 1) {
        $InputError = 1;
        prnMsg(_('La seccion debe contener al menos 3 caracteres'),'error');
		$Errors[$i] = 'Seccion';
		$i++;
    }*/

    if (strlen($_POST['posicion']) < 1) {
        $InputError = 1;
        prnMsg(_('La Posicion debe contener al menos 1 caracteres'),'error');
		$Errors[$i] = 'Posicion';
		$i++;
    }
    
    if (isset($SelectedStorage) AND $InputError !=1) {

        /*SelectedCurrency could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
        $sql = "UPDATE rh_storagebins SET
					description='" . $_POST['descripcion'] . "',
					area='". $_POST['area']. "',
                    way='". $_POST['pasillo']. "',
                    level='". $_POST['nivel']. "',
                    section='". $_POST['seccion']. "',
                    position='". $_POST['posicion']. "',
                    location='".$_POST['almacen']. "',
                    active='". $_POST['activo']. "'
					WHERE id = '" . $SelectedStorage . "'";

        $msg = _('El storagebins fue actualizado');
    } else if ($InputError !=1 && $InputErrorFull !=1) {

    /*Selected currencies is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new payment terms form */
    	$sql = "INSERT INTO rh_storagebins (description, area, way, level, section, position, location, active)
                    VALUE('" . $_POST['descripcion'] . "',
					      '" . $_POST['area'] . "',
                          '" . $_POST['pasillo'] . "',
                          '" . $_POST['nivel'] . "',
                          '" . $_POST['seccion'] . "',
                          '" . $_POST['posicion'] . "',
                          '" . $_POST['almacen'] . "',
                          '" . $_POST['activo'] . "')";

    	$msg = _('El storagebins a sido agregado');
    }
    //run the SQL from either of the above possibilites
    $result = DB_query($sql,$db);
    if ($InputError!=1){
    	prnMsg( $msg,'success');
    }
    unset($SelectedStorage);
    unset($_POST['descripcion']);
    unset($_POST['area']);
    unset($_POST['pasillo']);
    unset($_POST['nivel']);
    unset($_POST['seccion']);
    unset($_POST['posicion']);
    unset($_POST['almacen']);
    unset($_POST['activo']);
}

if (!isset($SelectedStorage)||$SelectedStorage='') {

/* It could still be the second time the page has been run and a record has been selected for modification - SelectedCurrency will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
then none of the above are true and the list of payment termss will be displayed with
links to delete or edit each. These will call the same page again and allow update/input
or deletion of the records*/

    $sql = 'SELECT
                id
              , description'.
              //', area'.
              '
              , way
              , level
              , position
              , location
              , active FROM rh_storagebins';
    $result = DB_query($sql, $db);

    echo '<CENTER><table border=1>';
    echo '<tr>
            <th>' . _('id') . '</th>
    		<th>' . _('Descripcion') . '</th>'.
    		//'<th>' . _('Area') . '</th>'.
            '<th>' . _('Pasillo') . '</th>'.
            '<th>' . _('Anaquel') . '</th>'.
            '<th>' . _('Fila') . '</th>'.
            '<th>' . _('Almacen') . '</th>
            <th>' . _('Activo') . '</th>
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
		echo "<td>".$myrow[0]."</td><td>".$myrow[1]."</td><td>".$myrow[2]."</td><td>".$myrow[3]."</td><td>".$myrow[4]."</td><td>".$myrow[5]."</td><td><a href='".$_SERVER['PHP_SELF'] . "?SelectedStorage=".$myrow[0]."'>Editar</a></td><td><a href='".$_SERVER['PHP_SELF'] . "?SelectedStorage=".$myrow[0]."&delete=true'>Eliminar</a></td><td><a href='rh_storagebins_stock.php?SelectedStorage=".$myrow[0]."'>Articulos</a></td>";
    } //END WHILE LIST LOOP
    echo '</table></CENTER><BR>';
} //end of ifs and buts!

if (!isset($_GET['delete'])) {
    if(isset($_GET['SelectedStorage'])){
	$SelectedStorage=$_GET['SelectedStorage'];
    }
}else if(isset($_GET['delete'])){
    if(isset($_GET['SelectedStorage'])){
	$SelectedStorage=$_GET['SelectedStorage'];

        $sql = "DELETE  from  rh_storagebins
					WHERE id = '" . $SelectedStorage . "'";

        $msg = _('El storagebins fue eliminado');
        $result = DB_query($sql,$db);
        echo "<META HTTP-EQUIV='refresh' CONTENT='0; URL=" . $_SERVER['PHP_SELF'] . "?" . SID . "'>"; 
    }
}else if(isset($_POST['SelectedStorage'])){
	$SelectedStorage=$_POST['SelectedStorage'];
}

    echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
    if ((isset($SelectedStorage)) && ($SelectedStorage!='') &&(!isset($_GET['delete']))) {
        //editing an existing payment terms

        $sql = "SELECT id, description
              , area
              , way
              , level
              , section
              , position
              , location
              , active
				FROM rh_storagebins
				WHERE id='" . $SelectedStorage. "'";
       // echo $sql;
        $ErrMsg = _('Se produjo un error con los factores');;
		$result = DB_query($sql, $db, $ErrMsg);

        $myrow = DB_fetch_array($result);

        $_POST['descripcion']= $myrow[1];
        $_POST['area']= $myrow[2];
        $_POST['pasillo']= $myrow[3];
        $_POST['nivel']= $myrow[4];
        $_POST['seccion']= $myrow[5];
        $_POST['posicion']= $myrow[6];
        $_POST['almacen']= $myrow[7];
        $_POST['activo']= $myrow[8];

        echo '<input type="hidden" name="SelectedStorage" VALUE="' . $SelectedStorage. '">';
       echo '<center><table><tr>
			<td>' ._('Descripcion') . ':</td>
			<td><input  type="Text" name="descripcion" value="'.$_POST['descripcion'].'" size=50 maxlength=50></td></tr>';
/*
    echo '<TR><TD>'._('Area').':</TD>';
    echo '<TD><INPUT  TYPE="text" name="area" SIZE=50 MAXLENGTH=50 VALUE="'.$_POST['area'].'">';
    echo '</TD></TR>';*/
   
    echo '<TR><TD>'._('Pasillo').':</TD>';
    echo '<TD><INPUT  TYPE="text" name="pasillo" SIZE=50 MAXLENGTH=50 VALUE="'.$_POST['pasillo'].'">';
    echo '</TD></TR>';     

    echo '<TR><TD>'._('Anaquel').':</TD>';
    echo '<TD><INPUT  TYPE="text" name="nivel" SIZE=50 MAXLENGTH=50 VALUE="'.$_POST['nivel'].'">';
    echo '</TD></TR>';
   /*
    echo '<TR><TD>'._('Seccion').':</TD>';
    echo '<TD><INPUT  TYPE="text" name="seccion" SIZE=50 MAXLENGTH=50 VALUE="'.$_POST['seccion'].'">';
    echo '</TD></TR>';
*/
     echo '<TR><TD>'._('Fila').':</TD>';
    echo '<TD><INPUT  TYPE="text" name="posicion" SIZE=50 MAXLENGTH=50 VALUE="'.$_POST['posicion'].'">';
    echo '</TD></TR>';
     
     echo '<TR><TD>'._('Almacen').':</TD>';
     echo '<td><select name="almacen" style="width:100%"> ';
                $sql="select loccode,locationname from locations;";
                $result = DB_query($sql,$db);
                while ($myrow = DB_fetch_array($result)) {
                    echo "<option ".(($_POST['almacen']==$_POST['almacen']?"selected='selected'":" "))."value='".$myrow['loccode']."'>".$myrow['locationname']."</option>";
                }
     echo '</select>';

    echo '</TD></TR>';

     echo '<TR><TD>'._('Activo').':</TD>';
     echo '<td><select name="activo" style="width:100%"> ';
     echo '<option '.(($_POST['activo']=='1'?"selected='selected'":" ")).' value="1">Si</option>';
     echo '<option '.(($_POST['activo']=='0'?"selected='selected'":" ")).'value="0">No</option>';
     echo '</select>';
    echo '</TD></TR>';
    echo '</TABLE>';

    echo '<CENTER><input type="Submit" name="submit" value='._('Enter Information').'><input type="button" name="btn" value='._('Nuevo').' onclick="document.location=\'rh_storagebins.php\'" >';

    echo '</FORM>';
    }else{
        echo '<center><table><tr>
			<td>' ._('Descripcion') . ':</td>
			<td><input  type="Text" name="descripcion" value="" size=50 maxlength=50></td></tr>';
/*
    echo '<TR><TD>'._('Area').':</TD>';
    echo '<TD><INPUT  TYPE="text" name="area" SIZE=50 MAXLENGTH=50 VALUE="">';
    echo '</TD></TR>';
   */
    echo '<TR><TD>'._('Pasillo').':</TD>';
    echo '<TD><INPUT  TYPE="text" name="pasillo" SIZE=50 MAXLENGTH=50 VALUE="">';
    echo '</TD></TR>'; 

    echo '<TR><TD>'._('Anaquel').':</TD>';
    echo '<TD><INPUT  TYPE="text" name="nivel" SIZE=50 MAXLENGTH=50 VALUE="">';
    echo '</TD></TR>';
    /*
    echo '<TR><TD>'._('Seccion').':</TD>';
    echo '<TD><INPUT  TYPE="text" name="seccion" SIZE=50 MAXLENGTH=50 VALUE="">';
    echo '</TD></TR>';
*/
     echo '<TR><TD>'._('Fila').':</TD>';
    echo '<TD><INPUT  TYPE="text" name="posicion" SIZE=50 MAXLENGTH=50 VALUE="">';
    echo '</TD></TR>'; 

     echo '<TR><TD>'._('Almacen').':</TD>';
     echo '<td><select name="almacen" style="width:100%"> ';
                $sql="select loccode,locationname from locations;";
                $result = DB_query($sql,$db);
                while ($myrow = DB_fetch_array($result)) {
                    echo "<option ".(($myrow['loccode']==$_POST['sucursal']?"selected='selected'":" "))."value='".$myrow['loccode']."'>".$myrow['locationname']."</option>";
                }
     echo '</select>';

    echo '</TD></TR>';

     echo '<TR><TD>'._('Activo').':</TD>';
     echo '<td><select name="activo" style="width:100%"> ';
     echo '<option value="1">Si</option>';
     echo '<option value="0">No</option>';
     echo '</select>';
    echo '</TD></TR>';

    echo '</TABLE>';

    echo '<CENTER><input type="Submit" name="submit" value='._('Enter Information').'>';

    echo '</FORM>';

} //end if record deleted no point displaying form to add record


include('includes/footer.inc');
?>
