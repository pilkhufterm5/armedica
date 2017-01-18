<?php
/* $Revision: 1 $ */

/**************************************************************************
* Daniel Villarreal Barrios 25/Nov/2015
* Archivo creado para el catalogo de Motivos Notas Credito
***************************************************************************/
//Seguridad de la pagina
//$PageSecurity = 1;

include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('Catalogo de Motivos de Notas de Credito');


// Al hacer clic en grabar nota de credito
if(isset($_POST['guardarnota']))
{
	$insert = true;
	$descripcion = $_POST['descripcion'];

	if($descripcion==''){

		prnMsg(_('El campo esta vacio, vuelva a intentarlo '),'error');
		$insert = false;
	} 

	if($insert)
	{
		$sqlinsert = "INSERT INTO or_motivosnotascredito (descripcion) 
		 VALUES (
			 	'" . $descripcion . "')";

		$ErrMsg = _('El motivo') . ' ' . $descripcion . ' ' . _('could not be added because');
		$DbgMsg = _('Se intento insertar el nuevo motivo pero fallo');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('El nuevo motivo para nota de credito ') . ' ' . $descripcion . ' ' . _('Se agrego a la base de datos'),'success');
		unset($_GET['action']);
	}
}
// Al hacer clic en grabar nota de credito
if(isset($_POST['actualizarnota']))
{
	$insert = true;
	$descripcion = $_POST['descripcion'];

	if($descripcion==''){

		prnMsg(_('El campo esta vacio, vuelva a intentarlo '),'error');
		$insert = false;
	} 
$id = $_GET['id'];

	if($id==''){

		prnMsg(_('Seleccione un registro, vuelva a intentarlo '),'error');
		$insert = false;
	} 
	if($insert)
	{
		$sqlinsert = "UPDATE or_motivosnotascredito SET descripcion='$descripcion' WHERE id = " .$id ; 

		$ErrMsg = _('El motivo') . ' ' . $descripcion . ' ' . _('could not be added because');
		$DbgMsg = _('Se intento insertar el nuevo motivo pero fallo');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('El nuevo motivo para nota de credito ') . ' ' . $descripcion . ' ' . _('Se actualizo a la base de datos'),'success');
		unset($_GET['action']);
	}
}

// Al hacer clic en eliminar nota
if(isset($_GET['action']) and $_GET['action']=='eliminar')
{
	$delete = true;
	$id = $_GET['id'];

	if($id==''){

		prnMsg(_('Seleccione un registro, vuelva a intentarlo '),'error');
		$delete = false;
	} 

	if($delete)
	{
		$sqlinsert = "DELETE FROM or_motivosnotascredito WHERE id = " . $id;

		$ErrMsg = _('El motivo') . ' ' . $id . ' ' . _('No se pudo eliminar');
		$DbgMsg = _('Se intento eliminar el motivo pero fallo');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('El motivo para nota de credito ') . ' ' . $id . ' ' . _('Se ha eliminado con exito'),'success');
	}
}



include('includes/header.inc');

echo "<CENTER><H1>Cat&aacute;logo de Motivos de Notas de Credito</H1></CENTER>";



	echo "
	<CENTER>
	<span id='datagrid'>
	<form method='POST'>
		<input type='search' NAME='search' placeholder='Buscador' style='width: 45%;' value='".@$_POST['search']."'>
	</form>
	<TABLE width='45%'>
	<TR>
	<TD CLASS='tableheader' width='8%' >ID</TD>
	<TD CLASS='tableheader' >Descripcion</TD>
	<TD CLASS='tableheader' width='8%'>Editar</TD>
	<TD CLASS='tableheader' width='8%'>Eliminar</TD>
	</TR>";
	$sql = 'SELECT * FROM or_motivosnotascredito ';

	if(isset($_POST['search']) and $_POST['search']!=NULL)
	{
		$sql .= 'WHERE descripcion LIKE "%'.$_POST['search'].'%" ';
	}
	$result = DB_query($sql,$db);
	$k=0;
	while ($myrow = DB_fetch_array($result)) {
		if ($k==1){
			$color = "BGCOLOR='#CCCCCC'";
			$k=0;
		} else {
			$color = "BGCOLOR='#EEEEEE'";
			$k=1;
		}
		echo "<TR ".$color.">
		<TD align=right>".$myrow['id']."</TD>
		<TD>".$myrow['descripcion']."</TD>
		<TD align=right><a href='or_motivosnotascredito.php?id=".$myrow['id']."&action=editar' >"._('Edit')."</a></TD>";
		?>
		<TD align=right><a href='or_motivosnotascredito.php?id=<?php echo $myrow['id'] ?>&action=eliminar' onclick='return confirm("Estas Seguro de Eliminarlo?")' >Eliminar</a></TD>
		<?php echo "
		</TR>";
	}

	

	echo "</TABLE>
	</span>
	</CENTER>";

	if(!isset($_GET['action']) and $_GET['action']!='editar')
	{
	
	echo "
	<CENTER>

	<hr>
	<h3>Alta de Motivo Nota de Credito</h3>
	<FORM NAME='altanotascredito' method='POST'>
	<TABLE BORDER=1  width='45%'>
	<TR>
	<TD>
	"._('Descripcion').":</TD>
	<TD><INPUT TYPE='text' style='width:75%;' MAXLENGTH=600 NAME='descripcion'></TD>
	</TR>
	</TABLE>

	<INPUT TYPE='submit' VALUE="._('Accept')." NAME='guardarnota'>
	<INPUT TYPE='reset' VALUE="._('Cancel').">
	</FORM>
	</CENTER>";
	}
	else
	{
	// Opcion para editar un registro
	$id = $_GET['id'];

	if($id=='')
	{
		prnMsg(_('Seleccione un registro y vuelva a intentarlo '),'error');
		$delete = false;
	}

	$sqleditar = 'SELECT * FROM or_motivosnotascredito WHERE id = '.$id;
	$result = DB_query($sqleditar,$db);
	$datosmotivo = DB_fetch_array($result);
	echo "
	<hr>
	<CENTER>
		<h3>Editar Motivo Nota de Credito</h3>
		<FORM NAME='editarnotascredito' method='POST'>
			<TABLE BORDER=1  width='45%'>
			<TR>
			<TD>
			"._('Descripcion').":</TD>
			<TD><INPUT TYPE='text' style='width:75%;' MAXLENGTH=600 NAME='descripcion' value='".$datosmotivo['descripcion']."'></TD>
			</TR>
			</TABLE>
			<INPUT TYPE='hidden' VALUE='".$datosmotivo['id']."' NAME='id'>
			<INPUT TYPE='submit' VALUE="._('Guardar Cambios')." NAME='actualizarnota'>
		</FORM>
		<br>
		<a href='or_motivosnotascredito.php'>Agregar Nuevo Motivo</a>
	</CENTER>
		";

	
	}

include('includes/footer.inc');
?>
