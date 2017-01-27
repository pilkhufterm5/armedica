<?php
/* $Revision: 1 $ */

/**************************************************************************
* Ruben Flores Barrios 24/Nov/2016
* Archivo creado para el catalogo de Sub Centro de Costo
***************************************************************************/
//Seguridad de la pagina
//$PageSecurity = 1;

include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('Cat&aacute;logo de Sub Centros de Costo');
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
wrk_subcentrocosto
- centrocosto_id
- subcentrocosto
- subfijo
- usuario
- fechaultimomov
- subcentrocosto_edo
*/
// Al hacer clic en grabar nota de credito
if(isset($_POST['guardarsubcentrocosto']))
{
	//print_r($_POST);
	$insert = true;
	// CAMPOS PARA INSERTAR
	$centrocosto_id = $_POST['centrocosto_id'];
	$subcentrocosto = $_POST['subcentrocosto'];
	$subfijo = $_POST['subfijo'];
	$usuario = $_SESSION['UserID'];
	$fechaultimomov = date('Y-m-d H:i:s');
	$subcentrocosto_edo = 1;

	if($subcentrocosto==''){

		prnMsg(_('El campo se encuentra vacio, favor de intentarlo de nuevo '),'error');
		$insert = false;
	} 
	if($subfijo==''){

		prnMsg(_('El campo se encuentra vacio, favor de intentarlo de nuevo '),'error');
		$insert = false;
	} 

	if($insert)
	{
		$sqlinsert = "INSERT INTO wrk_subcentrocosto (
			centrocosto_id,
			subcentrocosto,
			subfijo,
			usuario,
			fechaultimomov,
			subcentrocosto_edo
		) 
		 VALUES (
		 	'" . $centrocosto_id . "',
		 	'" . $subcentrocosto . "',
		 	'" . $subfijo . "',
		 	'" . $usuario . "',
		 	'" . $fechaultimomov . "',
		 	'" . $subcentrocosto_edo . "'
		 )";
		//echo $sqlinsert;
		//exit;
		$ErrMsg = _('El sub centro de costo') . '  ' . _('no pudo ser agregado');
		$DbgMsg = _('Se intento insertar el nuevo sub centro de costo pero hubo un fallo inesperado');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se agrego correctamente'),'success');
		unset($_GET['action']);
	}
}
// Al hacer clic en grabar centro de costo
if(isset($_POST['actualizarsubcentrocosto']))
{
	$UPDATE = true;
	$subcentrocosto_id = $_POST['id'];
	$centrocosto_id = $_POST['centrocosto_id'];
	$subcentrocosto = $_POST['subcentrocosto'];
	$subfijo = $_POST['subfijo'];
	$usuario = $_SESSION['UserID'];
	$fechaultimomov = date('Y-m-d H:i:s');
	$subcentrocosto_edo = 1;

	if($subcentrocosto==''){

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
		$sqlinsert = "UPDATE wrk_subcentrocosto 
			SET 
				centrocosto_id='$centrocosto_id',
				subcentrocosto='$subcentrocosto',
				subfijo='$subfijo',
				usuario='$usuario',
				fechaultimomov='$fechaultimomov',
				subcentrocosto_edo='$subcentrocosto_edo' 
			WHERE subcentrocosto_id = " .$id ; 

		$ErrMsg = _('El sub centro de costo') . '  ' . _('no pudo ser agregado');
		$DbgMsg = _('Se intento; insertar el nuevo sub centro de costo pero hubo un fallo inesperado');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se actualizo correctamente el nuevo sub centro de costo'),'success');
		unset($_GET['action']);
	}
}

// Al hacer clic en eliminar nota
if(isset($_GET['action']) and $_GET['action']=='eliminar')
{
	$delete = true;
	$id = $_GET['id'];
	$usuario = $_SESSION['UserID'];
	$fechaultimomov = date('Y-m-d H:i:s');

	if($id==''){

		prnMsg(_('Seleccione un registro, vuelva a intentarlo '),'error');
		$delete = false;
	} 

	if($delete)
	{
			$sqlinsert = "UPDATE  wrk_subcentrocosto SET subcentrocosto_edo = 0,usuario='$usuario',fechaultimomov='$fechaultimomov' WHERE subcentrocosto_id = " . $id;

		$ErrMsg = _('El sub centro de costo') . ' ' . $id . ' ' . _('no se pudo eliminar');
		$DbgMsg = _('Se intento eliminar el sub centro de costo pero hubo un fallo inesperado');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se elimino correctamente'),'success');
	}
}




echo "<CENTER><H1>Cat&aacute;logo de Sub Centros de Costo</H1></CENTER>";

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
	<TD CLASS='tableheader' >Centro de Costo</TD>
	<TD CLASS='tableheader' >Descripcion</TD>
	<TD CLASS='tableheader' width='20%'>Subfijo Contable</TD>
	<TD CLASS='tableheader' width='8%'>Acciones</TD>
	<TD CLASS='tableheader' width='8%'></TD>
	</TR>
	</thead>
	<tbody>";

	$sql = 'SELECT scc.*,cc.centrocosto centrodecosto_nombre FROM wrk_subcentrocosto scc
			INNER JOIN wrk_centrocosto  cc on cc.centrocosto_id=scc.centrocosto_id 
			where subcentrocosto_edo = 1';

	if(isset($_POST['search']) and $_POST['search']!=NULL)
	{
		$sql .= ' AND CONCAT_WS (" ", scc.subfijo, scc.subcentrocosto,cc.centrocosto) LIKE "%'.$_POST['search'].'%" ';
	}
	$result = DB_query($sql,$db);
	$k=0;
	while ($myrow = DB_fetch_array($result)) {

		echo "<TR>
		<TD align=right>".$myrow['subcentrocosto_id']."</TD>
		<TD>".$myrow['centrodecosto_nombre']."</TD>
		<TD>".$myrow['subcentrocosto']."</TD>
		<TD>".$myrow['subfijo']."</TD>
		<TD align=right><a href='catalogo_subcentrodecosto.php?id=".$myrow['subcentrocosto_id']."&action=editar' >"._('Edit')."</a></TD>";
		?>
		<TD align=right><a href='catalogo_subcentrodecosto.php?id=<?php echo $myrow['subcentrocosto_id'] ?>&action=eliminar' onclick='return confirm("¿Estas Seguro de Eliminarlo?")' >Eliminar</a></TD>
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
	<h3>Alta de Sub Centros de Costo</h3>
	<FORM NAME='altasubcentrocosto' method='POST'>
	<TABLE BORDER=1  width='45%'>
	<TR>
		<TD>
			"._('Centro de Costo').":</TD>
		<TD>
			<select name='centrocosto_id' class='select2'>
				<option value=''>-- Seleccione --</option>
		";
	// hacemos una consulta para obtener todo  de la tabla de centro de costo
	$sql_centrocosto = 'SELECT * FROM wrk_centrocosto
			where centrocosto_edo = 1';
	$result_centrocosto = DB_query($sql_centrocosto,$db);
	while ($rowcentro = DB_fetch_array($result_centrocosto)) {
		// hacemos los options
		echo '<option value="'.$rowcentro['centrocosto_id'].'">'.$rowcentro['centrocosto'].'</option>';
	}
	// termina
	echo "
			</select>
		</TD>
	</TR>
	<TR>
		<TD>
			"._('Sub Centro de Costo').":</TD>
		<TD>
			<INPUT TYPE='text' style='width:50%;' MAXLENGTH=200 NAME='subcentrocosto'>
		</TD>
	</TR>
	<TR>
		<TD>
			"._('Subfijo Contable').":
		</TD>
		<TD>
			<INPUT TYPE='text' style='width:50%;' MAXLENGTH=200 NAME='subfijo'>
		</TD>
	</TR>
	</TABLE>

	<INPUT TYPE='submit' VALUE="._('Agregar')." NAME='guardarsubcentrocosto'>
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

	$sqleditar = 'SELECT * FROM wrk_subcentrocosto WHERE subcentrocosto_id = '.$id;
	$result = DB_query($sqleditar,$db);
	$datos = DB_fetch_array($result);
	echo "
	<hr>
	<CENTER>
		<h3>Editar Sub Centro de Costo</h3>
		<FORM NAME='editarsubcentrocosto' method='POST'>
			<TABLE BORDER=1  width='45%'>
				<TR>
					<TD>
						"._('Centro de Costo').":</TD>
					<TD>
						<select name='centrocosto_id' class='select2'>
							<option value=''>-- Seleccione --</option>
					";
				// hacemos una consulta para obtener todo  de la tabla de centro de costo
				$sql_centrocosto = 'SELECT * FROM wrk_centrocosto
						where centrocosto_edo = 1';
				$result_centrocosto = DB_query($sql_centrocosto,$db);
				while ($rowcentro = DB_fetch_array($result_centrocosto)) {
					// hacemos los options
					if($datos['centrocosto_id']==$rowcentro['centrocosto_id'])
					{
						echo '<option value="'.$rowcentro['centrocosto_id'].'" selected>'.$rowcentro['centrocosto'].'</option>';
					}else
					{
						echo '<option value="'.$rowcentro['centrocosto_id'].'" >'.$rowcentro['centrocosto'].'</option>';
					}
				}
				// termina
				echo "
						</select>
					</TD>
				</TR>
				<TR>
					<TD>
						"._('Sub Centro de Costo').":
					</TD>
					<TD>	
						<INPUT TYPE='text' style='width:75%;' MAXLENGTH=600 NAME='subcentrocosto' value='".$datos['subcentrocosto']."'>
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
			<INPUT TYPE='hidden' VALUE='".$datos['subcentrocosto_id']."' NAME='id'>
			<INPUT TYPE='submit' VALUE="._('Guardar Cambios')." NAME='actualizarsubcentrocosto'>
		</FORM>
		<br>
		<a href='catalogo_subcentrodecosto.php'>Agregar Nuevo Sub Centro de Costo</a>
	</CENTER>
		";

	
	}

include('includes/footer.inc');
?>
