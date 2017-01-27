<?php
/* $Revision: 1 $ */

/**************************************************************************
* Ruben Flores Barrios 28/Nov/2016
* Archivo creado para el catalogo de Centros de Costo
***************************************************************************/
//Seguridad de la pagina
//$PageSecurity = 1;

include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('Cat&aacute;logo de Motivos de Rechazo');
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
wrk_motivosrequis
- motivoreq_id
- motivoreq
- etaparequis
- usuario
- fechaultimomov
- motivoreq_edo
*/
// Al hacer clic en grabar motivo de rechazo
if(isset($_POST['guardarmotivorechazo']))
{
	$insert = true;
	// CAMPOS PARA INSERTAR
	$motivoreq = $_POST['motivoreq'];
	$etaparequis = $_POST['etaparequis'];
	$usuario = $_SESSION['UserID'];
	$fechaultimomov = date('Y-m-d H:i:s');
	$motivoreq_edo = 1;

	if($motivoreq==''){

		prnMsg(_('El campo se encuentra vacio, favor de intentarlo de nuevo '),'error');
		$insert = false;
	} 
	if($etaparequis==''){

		prnMsg(_('El campo se encuentra vacio, favor de intentarlo de nuevo '),'error');
		$insert = false;
	} 

	if($insert)
	{
		$sqlinsert = "INSERT INTO wrk_motivosrequis (
			motivoreq,
			etaparequis,
			usuario,
			fechaultimomov,
			motivoreq_edo
		) 
		 VALUES (
		 	'" . $motivoreq . "',
		 	'" . $etaparequis . "',
		 	'" . $usuario . "',
		 	'" . $fechaultimomov . "',
		 	'" . $motivoreq_edo . "'
		 )";

		$ErrMsg = _('El motivo de rechazo') . '  ' . _('no pudo ser agregado');
		$DbgMsg = _('Se intento insertar el nuevo motivo de rechazo pero hubo un fallo inesperado');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se agrego correctamente'),'success');
		unset($_GET['action']);
	}
}
// Al hacer clic en grabar motivo de rechazo
if(isset($_POST['actualizarmotivorechazo']))
{
	$UPDATE = true;
	$motivoreq = $_POST['motivoreq'];
	$etaparequis = $_POST['etaparequis'];
	$usuario = $_SESSION['UserID'];
	$fechaultimomov = date('Y-m-d H:i:s');
	$motivoreq_edo = 1;

	if($motivoreq==''){

		prnMsg(_('El campo esta vacio, favor de volver a intentarlo '),'error');
		$insert = false;
	} 

	if($etaparequis==''){

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
		$sqlinsert = "UPDATE wrk_motivosrequis 
			SET 
				motivoreq='$motivoreq',
				etaparequis='$etaparequis',
				usuario='$usuario',
				fechaultimomov='$fechaultimomov',
				motivoreq_edo='$motivoreq_edo' 
			WHERE motivoreq_id = " .$id ; 

		$ErrMsg = _('El motivo de rechazo') . '  ' . _('no pudo ser agregado');
		$DbgMsg = _('Se intento insertar el nuevo motivo de rechazo pero hubo un fallo inesperado');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se actualizo correctamente'),'success');
		unset($_GET['action']);
	}
}

// Al hacer clic en eliminar motivo de rechazo
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
		$sqlinsert = "UPDATE  wrk_motivosrequis SET motivoreq_edo = 0,usuario='$usuario',fechaultimomov='$fechaultimomov' WHERE motivoreq_id = " . $id;

		$ErrMsg = _('El motivo de rechazo') . ' ' . $id . ' ' . _('no se pudo eliminar');
		$DbgMsg = _('Se intento eliminar el motivo de rechazo pero hubo un fallo inesperado');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se elimino correctamente'),'success');
	}
}




echo "<CENTER><H1>Cat&aacute;logo de Motivos de Rechazo</H1></CENTER>";

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
	<TD CLASS='tableheader' >Motivo</TD>
	<TD CLASS='tableheader' width='20%'>Etapa</TD>
	<TD CLASS='tableheader' width='8%'>Acciones</TD>
	<TD CLASS='tableheader' width='8%'></TD>
	</TR>
	</thead>
	<tbody>";

	$sql = 'SELECT * FROM wrk_motivosrequis where motivoreq_edo = 1';

	if(isset($_POST['search']) and $_POST['search']!=NULL)
	{
		$sql .= ' AND motivoreq LIKE "%'.$_POST['search'].'%" ';
	}
	$result = DB_query($sql,$db);
	$k=0;
	$etaparequis_array = array('1'=>'Autorizador','2'=>'Compras');
	while ($myrow = DB_fetch_array($result)) {
		
		echo "<TR>
		<TD align=right>".$myrow['motivoreq_id']."</TD>
		<TD>".$myrow['motivoreq']."</TD>
		<TD>".$etaparequis_array[$myrow['etaparequis']]."</TD>
		<TD align=right><a href='catalogo_motivosderechazo.php?id=".$myrow['motivoreq_id']."&action=editar' >"._('Edit')."</a></TD>";
		?>
		<TD align=right><a href='catalogo_motivosderechazo.php?id=<?php echo $myrow['motivoreq_id'] ?>&action=eliminar' onclick='return confirm("¿Estas Seguro de Eliminarlo?")' >Eliminar</a></TD>
		<?php echo "
		</TR>";
	}

	

	echo "</tbody></TABLE>
	</span>
	</CENTER>";

	if($_GET['action']!='editar')
	{
		// SE AGREGA UN NUEVO MOTIVO DE RECHAZO
	
	echo "
	<CENTER>

	<hr>
		<h3>Alta de Motivos de Rechazo</h3>
			<FORM NAME='altamotivorechazo' method='POST'>
				<TABLE BORDER=1  width='45%'>
					<TR>
						<TD>
							"._('Motivo de Rechazo').":
						</TD>
						<TD>
							<INPUT TYPE='text' style='width:50%;' MAXLENGTH=200 NAME='motivoreq'>
						</TD>
					</TR>
					<TR>
						<TD>
							"._('Etapa').":
						</TD>
						<TD>
							<select name='etaparequis' class='select2'>
							<option value=''>-- Seleccione --</option>
							<option value='1'>Autorizador</option>
  							<option value='2'>Compras</option>
						</TD>
					</TR>
				</TABLE>
				<INPUT TYPE='submit' VALUE="._('Agregar')." NAME='guardarmotivorechazo'>
			</FORM>
	</CENTER>";
	}
	else if($_GET['action']=='editar')
	{
	// Opcion para editar un motivo de rechazo
	$id = $_GET['id'];

	if($id=='')
	{
		prnMsg(_('Seleccione un registro y vuelva a intentarlo '),'error');
		$delete = false;
	}

	$sqleditar = 'SELECT * FROM wrk_motivosrequis WHERE motivoreq_id = '.$id;
	$result = DB_query($sqleditar,$db);
	$etaparequis_array = array('1'=>'Autorizador','2'=>'Compras');
	$datos = DB_fetch_array($result);
	echo "
	<hr>
	<CENTER>
		<h3>Editar Motivo de Rechazo</h3>
		<FORM NAME='editarmotivorechazo' method='POST'>
			<TABLE BORDER=1  width='45%'>
				<TR>
					<TD>
						"._('Motivo').":
					</TD>
					<TD>	
						<INPUT TYPE='text' style='width:75%;' MAXLENGTH=600 NAME='motivoreq' value='".$datos['motivoreq']."'>
					</TD>
				</TR>
				<TR>
					<TD>
						"._('Etapa').":
					</TD>
					<TD>	
						<select name='etaparequis' class='select2'>
							<option value=''>-- Seleccione --</option>";
							foreach($etaparequis_array as $key=>$value)
							{
								if($datos['etaparequis']==$key)
								{
									echo "<option value='".$key."' selected>".$value."</option>";
								}
								else{
									echo "<option value='".$key."' >".$value."</option>";
								}
							}
					echo "
					</TD>
				</TR>
			</TABLE>
			<INPUT TYPE='hidden' VALUE='".$datos['motivoreq_id']."' NAME='id'>
			<INPUT TYPE='submit' VALUE="._('Guardar Cambios')." NAME='actualizarmotivorechazo'>
		</FORM>
		<br>
		
	</CENTER>
		";

	
	}
	echo "<center><a href='catalogo_motivosderechazo.php'>Agregar Motivo de Rechazo</a></center>";
include('includes/footer.inc');
?>