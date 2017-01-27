<?php
/* $Revision: 1 $ */

/**************************************************************************
* Ruben Flores Barrios 24/Nov/2016
* Archivo creado para el catalogo de Categorias
***************************************************************************/
//Seguridad de la pagina
//$PageSecurity = 1;

include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('Catalogo de Categorias');
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
wrk_categoriascc
- categoriacc_id
- categoriacc
- subfijo
- usuario
- fechaultimomov
- categoriacc_edo
*/

// Al hacer clic en grabar una categoria
if(isset($_POST['guardarcategoria']))
{
	$insert = true;
	// CAMPOS PARA INSERTAR
	$categoriacc = $_POST['categoriacc'];
	$subfijo = $_POST['subfijo'];
	$usuario = $_SESSION['UserID'];
	$fechaultimomov = date('Y-m-d H:i:s');
	$categoriacc_edo = 1;
	
	if($categoriacc==''){

		prnMsg(_('El campo se encuentra vacio, favor de intentarlo de nuevo '),'error');
		$insert = false;
	} 

	if($subfijo==''){

		prnMsg(_('El campo se encuentra vacio, favor de intentarlo de nuevo '),'error');
		$insert = false;
	} 


	if($insert)
	{
		$sqlinsert = "INSERT INTO wrk_categoriascc (
			categoriacc,
			subfijo,
			usuario,
			fechaultimomov,
			categoriacc_edo
		)
		 VALUES (
			 	'" . $categoriacc . "',
			 	'" . $subfijo . "',
		 		'" . $usuario . "',
		 		'" . $fechaultimomov . "',
		 		'" . $categoriacc_edo . "'
		)";

		$ErrMsg = _('La categoria') . '  ' . _('no pudo ser ingresada debido');
		$DbgMsg = _('Se intento insertar la nueva categoria pero fallo');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se agrego correctamente'),'success');
		unset($_GET['action']);
	}
}
// Al hacer clic en grabar categoria
if(isset($_POST['actualizarcategoria']))
{
	$UPDATE = true;
	$categoriacc = $_POST['categoriacc'];
	$subfijo = $_POST['subfijo'];
	$usuario = $_SESSION['UserID'];
	$fechaultimomov = date('Y-m-d H:i:s');
	$categoriacc_edo = 1;

	if($categoriacc==''){

		prnMsg(_('El campo esta vacio, vuelva a intentarlo por favor'),'error');
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
		$sqlinsert = "UPDATE wrk_categoriascc 
			SET 
				categoriacc='$categoriacc',
				subfijo='$subfijo',
				usuario='$usuario',
				fechaultimomov='$fechaultimomov',
				categoriacc_edo='$categoriacc_edo' 
			WHERE categoriacc_id = " .$id ; 

		$ErrMsg = _('La categoria') . '  ' . _('no pudo ser ingresada debido');
		$DbgMsg = _('Se intento insertar la nueva categoria pero fallo');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se inserto correctamente la nueva categoria'),'success');
		unset($_GET['action']);
	}
}

// Al hacer clic en eliminar
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
		$usuario = $_SESSION['UserID'];
		$fechaultimomov = date('Y-m-d H:i:s');
		$sqlinsert = "UPDATE wrk_categoriascc SET categoriacc_edo = 0,usuario='$usuario',fechaultimomov='$fechaultimomov' WHERE categoriacc_id = " . $id;

		$ErrMsg = _('La categoria') . ' ' . $id . ' ' . _('No se pudo eliminar');
		$DbgMsg = _('Se intento eliminar la categoria pero fallo');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se elimino correctamente'),'success');
	}
}




echo "<CENTER><H1>Cat&aacute;logo de Categorias</H1></CENTER>";

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

	$sql = 'SELECT * FROM wrk_categoriascc where categoriacc_edo = 1';

	if(isset($_POST['search']) and $_POST['search']!=NULL)
	{
		$sql .= 'WHERE categoriacc LIKE "%'.$_POST['search'].'%" ';
	}
	$result = DB_query($sql,$db);
	$k=0;
	while ($myrow = DB_fetch_array($result)) {
		
		echo "<TR>
		<TD align=right>".$myrow['categoriacc_id']."</TD>
		<TD>".$myrow['categoriacc']."</TD>
		<TD>".$myrow['subfijo']."</TD>
		<TD align=right><a href='catalogo_categorias.php?id=".$myrow['categoriacc_id']."&action=editar' >"._('Edit')."</a></TD>";
		?>
		<TD align=right><a href='catalogo_categorias.php?id=<?php echo $myrow['categoriacc_id'] ?>&action=eliminar' onclick='return confirm("¿Estas Seguro de Eliminarla?")' >Eliminar</a></TD>
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
		<h3>Alta de Categorias</h3>
			<FORM NAME='altacategoria' method='POST'>
				<TABLE BORDER=1  width='45%'>
					<TR>
						<TD>
							"._('Categorias').":
						</TD>
						<TD>
							<INPUT TYPE='text' style='width:75%;' MAXLENGTH=600 NAME='categoriacc'>
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

	<INPUT TYPE='submit' VALUE="._('Accept')." NAME='guardarcategoria'>
	<INPUT TYPE='reset' VALUE="._('Cancel').">
	</FORM>
	</CENTER>";
	}
	else if ($_GET['action']=='editar')
	{
	// Opcion para editar una categoria
	$id = $_GET['id'];

	if($id=='')
	{
		prnMsg(_('Seleccione un registro y vuelva a intentarlo '),'error');
		$delete = false;
	}

	$sqleditar = 'SELECT * FROM wrk_categoriascc WHERE categoriacc_id = '.$id;
	$result = DB_query($sqleditar,$db);
	$datos = DB_fetch_array($result);
	echo "
	<hr>
	<CENTER>
		<h3>Editar Categoria</h3>
		<FORM NAME='editarcategoria' method='POST'>
			<TABLE BORDER=1  width='45%'>
				<TR>
					<TD>
						"._('Categoria').":
					</TD>
					<TD>
						<INPUT TYPE='text' style='width:75%;' MAXLENGTH=600 NAME='categoriacc' value='".$datos['categoriacc']."'>
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
			<INPUT TYPE='hidden' VALUE='".$datos['categoriacc_id']."' NAME='id'>
			<INPUT TYPE='submit' VALUE="._('Guardar Cambios')." NAME='actualizarcategoria'>
		</FORM>
		<br>
		<a href='catalogo_categorias.php'>Agregar Nueva Categoria</a>
	</CENTER>
		";

	
	}

include('includes/footer.inc');
?>
