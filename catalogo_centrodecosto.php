<?php
/* $Revision: 1 $ */

/**************************************************************************
* Ruben Flores Barrios 23/Nov/2016
* Archivo creado para el catalogo de Centros de Costo
***************************************************************************/
//Seguridad de la pagina
//$PageSecurity = 1;

include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('Cat&aacute;logo de Centros de Costo');
include('includes/header.inc');

// VERIFICAMOS QUE PUEDE ENTRAR AL MODULO - POR DANIEL VILLARREAL EL 16 DE ENERO DEL 2017
$sqlacceso = 'SELECT or_compras FROM www_users where userid ="'.$_SESSION['UserID'].'"';
$resultacceso = DB_query($sqlacceso,$db);
$rowacceso = DB_fetch_array($resultacceso);
if($rowacceso['or_compras']!=1)
{
	// no tiene acceso al modulo
	prnMsg(_('No cuenta con el acceso al catalogo.'),'warning');
	include('includes/footer.inc');
	exit;
}
// TERMINA - POR DANIEL VILLARREAL EL 16 DE ENERO DEL 2017

/* campos de la tabla
wrk_centrocosto
- centrocosto_id
- centrocosto
- subfijo
- usuario
- fechaultimomov
- centrocosto_edo
*/
// Al hacer clic en grabar nota de credito
if(isset($_POST['guardarcentrocosto']))
{
	$insert = true;
	// CAMPOS PARA INSERTAR
	$centrocosto = $_POST['centrocosto'];
	$subfijo = $_POST['subfijo'];
	$usuario = $_SESSION['UserID'];
	$fechaultimomov = date('Y-m-d H:i:s');
	$centrocosto_edo = 1;

	if($centrocosto==''){

		prnMsg(_('El campo se encuentra vacio, favor de intentarlo de nuevo '),'error');
		$insert = false;
	} 
	if($subfijo==''){

		prnMsg(_('El campo se encuentra vacio, favor de intentarlo de nuevo '),'error');
		$insert = false;
	} 

	if($insert)
	{
		$sqlinsert = "INSERT INTO wrk_centrocosto (
			centrocosto,
			subfijo,
			usuario,
			fechaultimomov,
			centrocosto_edo
		) 
		 VALUES (
		 	'" . $centrocosto . "',
		 	'" . $subfijo . "',
		 	'" . $usuario . "',
		 	'" . $fechaultimomov . "',
		 	'" . $centrocosto_edo . "'
		 )";

		$ErrMsg = _('El centro de costo') . '  ' . _('no pudo ser agregado');
		$DbgMsg = _('Se intento insertar el nuevo centro de costo pero hubo un fallo inesperado');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se agrego correctamente'),'success');
		unset($_GET['action']);
	}
}
// Al hacer clic en grabar centro de costo
if(isset($_POST['actualizarcentrocosto']))
{
	$UPDATE = true;
	$centrocosto = $_POST['centrocosto'];
	$subfijo = $_POST['subfijo'];
	$usuario = $_SESSION['UserID'];
	$fechaultimomov = date('Y-m-d H:i:s');
	$centrocosto_edo = 1;

	if($centrocosto==''){

		prnMsg(_('El campo esta vacio, favor de volver a intentarlo '),'error');
		$insert = false;
	} 

	if($subfijo==''){

		prnMsg(_('El campo esta vacio, favor de volver a intentarlo '),'error');
		$insert = false;
	}

$id = $_GET['id'];

	if($id==''){

		prnMsg(_('Seleccione un registro, vuelva a intentarlo '),'error');
		$insert = false;
	} 
	if($UPDATE)
	{
		$sqlinsert = "UPDATE wrk_centrocosto 
			SET 
				centrocosto='$centrocosto',
				subfijo='$subfijo',
				usuario='$usuario',
				fechaultimomov='$fechaultimomov',
				centrocosto_edo='$centrocosto_edo' 
			WHERE centrocosto_id = " .$id ; 

		$ErrMsg = _('El centro de costo') . '  ' . _('no pudo ser agregado');
		$DbgMsg = _('Se intent&oacute; insertar el nuevo centro de costo pero hubo un fallo inesperado');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se actualizo correctamente el nuevo centro de costo'),'success');
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
		$usuario = $_SESSION['UserID'];
		$fechaultimomov = date('Y-m-d H:i:s');
		$sqlinsert = "UPDATE  wrk_centrocosto SET centrocosto_edo = 0,usuario='$usuario',fechaultimomov='$fechaultimomov' WHERE centrocosto_id = " . $id;

		$ErrMsg = _('El centro de costo') . ' ' . $id . ' ' . _('no se pudo eliminar');
		$DbgMsg = _('Se intento eliminar el centro de costo pero hubo un fallo inesperado');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se elimino correctamente'),'success');
	}
}




echo "<CENTER><H1>Cat&aacute;logo de Centros de Costo</H1></CENTER>";

	echo "
	<link rel='stylesheet' type='text/css' href='js/DataTables/datatables.min.css'>
	<script type='text/javascript' src='js/DataTables/datatables.min.js'></script>
	<script>
		$( document ).ready(function() {
		  $('#registros').DataTable({
		  	dom: 'Bfrtip',
	        buttons: [
	            {
	                extend: 'csv',
	                text: 'Exportar a Excel'
	            },{
	            	extend:'pageLength',
	            	text:'Paginacion'
	            }],
            aLengthMenu: [[10,15,25,50, -1], [10,15,25,50, 'All']],
	        });
		});
	</script>";

	echo "
	<CENTER>
	<span id='datagrid'>
	<form method='POST'>
	</form>
	<TABLE width='' id='registros' class='table table-bordered table-striped table-hover table-condensed'>
	<thead>
		<TR>
			<TD CLASS='tableheader' width='8%' >ID</TD>
			<TD CLASS='tableheader' >Descripcion</TD>
			<TD CLASS='tableheader' width='20%'>Subfijo Contable</TD>
			<TD CLASS='tableheader' width='8%'>Acciones</TD>
			<TD CLASS='tableheader' width='8%'></TD>
		</TR>
	</thead>
	<tbody>";
	$sql = 'SELECT * FROM wrk_centrocosto where centrocosto_edo = 1';

	if(isset($_POST['search']) and $_POST['search']!=NULL)
	{
		$sql .= ' AND CONCAT_WS (" ", subfijo, centrocosto) LIKE "%'.$_POST['search'].'%" ';
	}
	$result = DB_query($sql,$db);
	$k=0;
	while ($myrow = DB_fetch_array($result)) {
		
		echo "<TR>
		<TD align=right>".$myrow['centrocosto_id']."</TD>
		<TD>".$myrow['centrocosto']."</TD>
		<TD>".$myrow['subfijo']."</TD>
		<TD align=right><a href='catalogo_centrodecosto.php?id=".$myrow['centrocosto_id']."&action=editar' >"._('Edit')."</a></TD>";
		?>
		<TD align=right><a href='catalogo_centrodecosto.php?id=<?php echo $myrow['centrocosto_id'] ?>&action=eliminar' onclick='return confirm("¿Estas Seguro de Eliminarlo?")' >Eliminar</a></TD>
		<?php echo "
		</TR>";
	}

	

	echo "</tbody></TABLE>
	</span>
	</CENTER>";

	if($_GET['action']!='editar')
	{
		// SE AGREGA UN NUEVO REGISTRO
	
	echo "
	<CENTER>

	<hr>
	<h3>Alta de Centro de Costo</h3>
	<FORM NAME='altacentrocosto' method='POST'>
	<TABLE BORDER=1  width='45%'>
	<TR>
	<TD>
	"._('Centro de Costo').":</TD>
	<TD><INPUT TYPE='text' style='width:50%;' MAXLENGTH=200 NAME='centrocosto'></TD>
	</TR>
	<TR>
	<TD>
	"._('Subfijo Contable').":</TD>
	<TD><INPUT TYPE='text' style='width:50%;' MAXLENGTH=200 NAME='subfijo'></TD>
	</TR>
	</TABLE>

	<INPUT TYPE='submit' VALUE="._('Agregar')." NAME='guardarcentrocosto'>
	</FORM>
	</CENTER>";
	}
	else if ($_GET['action']=='editar')
	{
	// Opcion para editar un registro
	$id = $_GET['id'];

	if($id=='')
	{
		prnMsg(_('Seleccione un registro y vuelva a intentarlo '),'error');
		$delete = false;
	}

	$sqleditar = 'SELECT * FROM wrk_centrocosto WHERE centrocosto_id = '.$id;
	$result = DB_query($sqleditar,$db);
	$datos = DB_fetch_array($result);
	echo "
	<hr>
	<CENTER>
		<h3>Editar Centro de Costo</h3>
		<FORM NAME='editarcentrocosto' method='POST'>
			<TABLE BORDER=1  width='45%'>
				<TR>
					<TD>
						"._('Centro de Costo').":
					</TD>
					<TD>	
						<INPUT TYPE='text' style='width:75%;' MAXLENGTH=600 NAME='centrocosto' value='".$datos['centrocosto']."'>
					</TD>
				</TR>
				<TR>
					<TD>
						"._('Subfijo Contable').":
					</TD>
					<TD>	
						<INPUT TYPE='text' style='width:75%;' MAXLENGTH=600 NAME='subfijo' value='".$datos['subfijo']."'>
					</TD>
				</TR>
			</TABLE>
			<INPUT TYPE='hidden' VALUE='".$datos['centrocosto_id']."' NAME='id'>
			<INPUT TYPE='submit' VALUE="._('Guardar Cambios')." NAME='actualizarcentrocosto'>
		</FORM>
		<br>
		<a href='catalogo_centrodecosto.php'>Agregar Nuevo Centro de Costo</a>
	</CENTER>
		";

	
	}

include('includes/footer.inc');
?>
