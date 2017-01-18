<?php
/* webERP Revision: 1.19 $ */
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-04-18 13:28:12 -0500 (Fri, 18 Apr 2008) $
 * $Rev: 206 $
 */
$PageSecurity = 1;

include('includes/session.inc');
$title = _('Storage Bins Stocks');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');


if(isset($_GET['SelectedStorage'])){
	$SelectedStorage=$_GET['SelectedStorage'];
}else if(isset($_POST['SelectedStorage'])){
	$SelectedStorage=$_POST['SelectedStorage'];
}else{
  unset($SelectedStorage);
}

if(isset($_GET['StockID'])){
	$SelectedSArticle=$_GET['StockID'];
}else if(isset($_POST['StockID'])){
	$SelectedSArticle=$_POST['StockID'];
}else{
  unset($SelectedSArticle);
}

if(isset($SelectedStorage)){
    echo '<A HREF="'. $rootpath . '/rh_storagebins.php?' . SID . '">'. _('Regresar a Storagebins'). '</A><BR>';
}else if(isset($SelectedSArticle)){
    echo '<A HREF="'. $rootpath . '/SelectProduct.php?' . SID . '">'. _('Regresar a Stock'). '</A><BR>';
}

if (isset($_POST['submit'])) {

    //initialise no input errors assumed initially before we test
    $InputError = 0;
    $InputErrorFull=0;

    /* actions to take once the user has clicked the submit button
    ie the page has called itself with some user input */

    //first off validate inputs are sensible
    if(!isset($SelectedSArticle)){
	    $i=1;
	    $sql="SELECT *
			FROM rh_storagebins_stock WHERE stockid='".$_POST['articulo']."' and storageid=".$SelectedStorage;
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
    }else{
	    $i=1;
	    $sql="SELECT *
			FROM rh_storagebins_stock WHERE stockid='".$SelectedSArticle."' and storageid=".$_POST['storagebin'];
            //var_dump($_POST);
    	$result=DB_query($sql, $db);
	    $myrow=DB_fetch_row($result);

	    if ($myrow[0]!=0) {
		    $InputErrorFull = 1;
            if(!isset($SelectedStorage)){
		        prnMsg( _('El articulo ya tiene asignado este storagebins'),'error');
            }
		    $Errors[$i] = 'Descripcion';
		    $i++;
	    }
    }


   /* if (isset($SelectedStorage) AND $InputError !=1) {

        /*SelectedCurrency could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
   /*     $sql = "UPDATE rh_storagebins_stock SET
					storageid='" . $_POST['descripcion'] . "',
					stockid='". $_POST['stockid']. "',
					WHERE id = '" . $SelectedStorage . "'";

        $msg = _('El storagebins fue actualizado');
    } else*/
   if(!isset($SelectedSArticle)){
    if ($InputError !=1 && $InputErrorFull !=1) {

    /*Selected currencies is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new payment terms form */
    	$sql = "INSERT INTO rh_storagebins_stock (storageid, stockid)
                    VALUE('" . 	$SelectedStorage. "',
					      '" . $_POST['articulo'] . "')";

    	$msg = _('El articulo se ha agregado al storagebins');
    }
    //run the SQL from either of the above possibilites
    $result = DB_query($sql,$db);
    if ($InputErrorFull!=1){
    	prnMsg( $msg,'success');
    }else{
        prnMsg( _("El articulo ya esta agregado en este storagebins"),'error');
    }
    unset($_POST['articulo']);
    }else{
        if ($InputError !=1 && $InputErrorFull !=1) {
      	$sql = "INSERT INTO rh_storagebins_stock (stockid, storageid)
                    VALUE('" . 	$SelectedSArticle. "',
					      '" . $_POST['storagebin'] . "')";
    	$msg = _('El articulo se ha agregado al storagebins');
        }
        $result = DB_query($sql,$db);
        if ($InputErrorFull!=1){
    	    prnMsg( $msg,'success');
        }else{
            prnMsg( _("El articulo ya esta agregado en este storagebins"),'error');
        }
        unset($_POST['storagebin']);

    }
}

if(isset($SelectedStorage)){
    $sql = 'SELECT
                id,
                stockmaster.stockid,
                stockmaster.description
            FROM
                rh_storagebins_stock
                INNER JOIN stockmaster
                    ON (rh_storagebins_stock.stockid = stockmaster.stockid AND storageid='.$SelectedStorage.');';
                   // echo $sql;
    $result = DB_query($sql, $db);

    echo '<CENTER><table border=1>';
    echo '<tr>
    		<th>' . _('Codigo') . '</th>
            <th>' . _('Articulo') . '</th>
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
		echo "<td>".$myrow[1]."</td><td>".$myrow[2]."</td><td><a href='".$_SERVER['PHP_SELF'] . "?SelectedStorage=".$SelectedStorage."&SelectedStorageArticle=".$myrow[0]."&delete=true'>Eliminar</a></td>";
    } //END WHILE LIST LOOP
    echo '</table></CENTER><BR>';
}else  if(isset($SelectedSArticle)){
    $sql = 'SELECT
                rh_storagebins.id, description
              , area
              , level
              , location
              , active
            FROM
                rh_storagebins_stock
                INNER JOIN rh_storagebins
                    ON (rh_storagebins_stock.storageid = rh_storagebins.id AND stockid="'.$SelectedSArticle.'");';
                   // echo $sql;
    $result = DB_query($sql, $db);

    echo '<CENTER><table border=1>';
    echo '<tr>
    		<th>' . _('Descripcion') . '</th>
    		<th>' . _('Area') . '</th>'.
            '<th>' . _('Nivel') . '</th>'.
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
		echo "<td>".$myrow[1]."</td><td>".$myrow[2]."</td><td>".$myrow[3]."</td><td>".$myrow[4]."</td><td>".$myrow[5]."</td><td><a href='".$_SERVER['PHP_SELF'] . "?StockID=".$SelectedSArticle."&SelectedStoragebins=".$myrow[0]."&delete=true'>Eliminar</a></td>";
    } //END WHILE LIST LOOP
    echo '</table></CENTER><BR>';
}
if(!isset($SelectedSArticle)){
    if (!isset($_GET['delete'])) {
        if(isset($_GET['SelectedStorage'])){
	        $SelectedStorage=$_GET['SelectedStorage'];
        }
    }else if(isset($_GET['delete'])){
        if(isset($_GET['SelectedStorageArticle'])){
	    $SelectedStorageArticle =$_GET['SelectedStorageArticle'];

            $sql = "DELETE  from  rh_storagebins_stock
					WHERE id = '" . $SelectedStorageArticle . "'";

            $msg = _('El articulo fue eliminado del storagebins');
            $result = DB_query($sql,$db);
            prnMsg( $msg,'success');
            echo "<META HTTP-EQUIV='refresh' CONTENT='0; URL=" . $_SERVER['PHP_SELF'] . "?" . SID . "&SelectedStorage=".$SelectedStorage."'>";
        }
    }else if(isset($_POST['SelectedStorage'])){
	    $SelectedStorage=$_POST['SelectedStorage'];
    }
}else{
    if(isset($_GET['delete'])){
        if(isset($_GET['StockID'])){
	    $SelectedStorageArticle =$_GET['StockID'];

            $sql = "DELETE  from  rh_storagebins_stock
					WHERE stockid = '" . $SelectedStorageArticle . "' and storageid=".$_GET['SelectedStoragebins'];

            $msg = _('El articulo fue eliminado del storagebins');
            $result = DB_query($sql,$db);
            prnMsg( $msg,'success');
            echo "<META HTTP-EQUIV='refresh' CONTENT='0; URL=" . $_SERVER['PHP_SELF'] . "?" . SID . "&StockID=". $SelectedStorageArticle ."' >";
        }
    }
}
    if(!isset($SelectedSArticle)){
        echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
        echo '<input type="hidden" name="SelectedStorage" VALUE="' . $SelectedStorage. '">';
        echo '<center><table><tr>';
        echo '<TR><TD>'._('Art&iacute;culo').':</TD>';
        echo '<td><select name="articulo" style="width:100%"> ';
                $sql="select stockid,description from stockmaster order by description;";
                $result = DB_query($sql,$db);
                while ($myrow = DB_fetch_array($result)) {
                    echo "<option ".(($myrow['stockid']==$_POST['sucursal']?"selected='selected'":" "))."value='".$myrow['stockid']."'>".$myrow['stockid']." - ".$myrow['description']."</option>";
                }
        echo '</select>';
        echo '</TD></TR>';
        echo '</TABLE>';
        echo '<CENTER><input type="Submit" name="submit" value='._('Enter Information').'>';
        echo '</FORM>';
    }else{
         echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
        echo '<input type="hidden" name="StockID" VALUE="' . $SelectedSArticle. '">';
        echo '<center><table><tr>';
        echo '<TR><TD>'._('Storagebins').':</TD>';
        echo '<td><select name="storagebin" style="width:100%"> ';
                $sql="select id,description,location,area,way,level,section,position from rh_storagebins order by description;";
                $result = DB_query($sql,$db);
                while ($myrow = DB_fetch_array($result)) {
                    echo "<option ".(($myrow['id']==$_POST['sucursal']?"selected='selected'":" "))."value='".$myrow['id']."'>".$myrow['id']." - ".$myrow['description']." ".$myrow['2']."-".$myrow['3']."-".$myrow['5']."</option>";
                }
        echo '</select>';
        echo '</TD></TR>';
        echo '</TABLE>';
        echo '<CENTER><input type="Submit" name="submit" value='._('Enter Information').'>';
        echo '</FORM>';
    }
//end if record deleted no point displaying form to add record


include('includes/footer.inc');
?>
