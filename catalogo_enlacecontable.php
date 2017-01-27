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
$title = _('Cat&aacute;logo Enlace Contables');
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
wrk_enlacecc
- enlacecc_id
- centrocosto_id
- subcentrocosto_id
- categoriacc_id
- ctacontable
- usuario
- fechaultimomov
- enlacecc_edo
*/
// Al hacer clic en grabar cuenta contable
if(isset($_POST['guardarenlacecontable']))
{
	//print_r($_POST);
	$insert = true;
	// CAMPOS PARA INSERTAR
	$centrocosto_id = $_POST['centrocosto_id'];
	$subcentrocosto_id = $_POST['subcentrocosto_id'];
	$categoriacc_id = $_POST['categoriacc_id'];
	$ctacontable = $_POST['ctacontable'];
	$ctacontable = str_replace("-","",$_POST['ctacontable']);
	$usuario = $_SESSION['UserID'];
	$fechaultimomov = date('Y-m-d H:i:s');
	$enlacecc_edo = 1;

	if($ctacontable==''){

		prnMsg(_('El campo se encuentra vacio, favor de intentarlo de nuevo '),'error');
		$insert = false;
	} 

	if($insert)
	{
		$sqlinsert = "INSERT INTO wrk_enlacecc (
			centrocosto_id,
			subcentrocosto_id,
			categoriacc_id,
			ctacontable,
			usuario,
			fechaultimomov,
			enlacecc_edo
		) 
		 VALUES (
		 	'" . $centrocosto_id . "',
		 	'" . $subcentrocosto_id . "',
		 	'" . $categoriacc_id . "',
		 	'" . $ctacontable . "',
		 	'" . $usuario . "',
		 	'" . $fechaultimomov . "',
		 	'" . $enlacecc_edo . "'
		 )";
		//echo $sqlinsert;
		//exit;
		$ErrMsg = _('La cuenta contable') . '  ' . _('no pudo ser agregada');
		$DbgMsg = _('Se intento insertar la cuenta contable pero hubo un fallo inesperado');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se agrego correctamente'),'success');
		unset($_GET['action']);
	}
}




// Al hacer clic en grabar cuenta contable
if(isset($_POST['actualizarenlacecontable']))
{
	$UPDATE = true;
	$enlacecc_id = $_POST['enlacecc_id'];
	$centrocosto_id = $_POST['centrocosto_id'];
	$subcentrocosto_id = $_POST['subcentrocosto_id'];
	$categoriacc_id = $_POST['categoriacc_id'];
	$ctacontable = $_POST['ctacontable'];
	$ctacontable = str_replace("-","",$_POST['ctacontable']);
	$usuario = $_SESSION['UserID'];
	$fechaultimomov = date('Y-m-d H:i:s');
	$enlacecc_edo = 1;

	if($ctacontable==''){

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
		$sqlinsert = "UPDATE wrk_enlacecc 
			SET 
				centrocosto_id='$centrocosto_id',
				subcentrocosto_id='$subcentrocosto_id',
				categoriacc_id='$categoriacc_id',
				ctacontable='$ctacontable',
				usuario='$usuario',
				fechaultimomov='$fechaultimomov',
				enlacecc_edo='$enlacecc_edo' 
			WHERE enlacecc_id = " .$id ; 

		$ErrMsg = _('El enlace contable') . '  ' . _('no pudo ser agregado');
		$DbgMsg = _('Se intento; insertar el nuevo enlace contable pero hubo un fallo inesperado');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se actualizo correctamente'),'success');
		unset($_GET['action']);
	}
}

// Al hacer clic en eliminar un enlace contable
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
			$sqlinsert = "UPDATE  wrk_enlacecc SET enlacecc_edo = 0,usuario='$usuario',fechaultimomov='$fechaultimomov' WHERE enlacecc_id = " . $id;

		$ErrMsg = _('El enlace contable') . ' ' . $id . ' ' . _('no se pudo eliminar');
		$DbgMsg = _('Se intento eliminar el enlace contable pero hubo un fallo inesperado');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se elimino correctamente'),'success');
	}
}




echo "<CENTER><H1>Cat&aacute;logo de Enlace Contable</H1></CENTER>";

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
	<TD CLASS='tableheader' >Sub Centro de Costos</TD>
	<TD CLASS='tableheader' width='20%'>Categoria </TD>
	<TD CLASS='tableheader' width='20%'>Cuenta Contable </TD>
	<TD CLASS='tableheader' width='8%'>Acciones</TD>
	<TD CLASS='tableheader' width='8%'></TD>
	</TR>
	</thead>
	<tbody>";
	
	$sql = 'SELECT 
				ecc.*,
				ccc.categoriacc AS categorianombre,
				ccc.subfijo AS categoriasubfijo,
				cco.centrocosto AS centrocostonombre,
				cco.subfijo AS centrocostosubfijo,
				scc.subcentrocosto AS subcentrocostonombre,
				scc.subfijo AS subcentrocostosubfijo,
				concat_ws("-",cco.subfijo,scc.subfijo,ccc.subfijo) as cuentacontable_resultado	
			FROM wrk_enlacecc ecc
			INNER JOIN wrk_categoriascc ccc ON ecc.categoriacc_id=ccc.categoriacc_id
			INNER JOIN wrk_centrocosto cco ON ecc.centrocosto_id=cco.centrocosto_id
			INNER JOIN wrk_subcentrocosto scc ON ecc.subcentrocosto_id=scc.subcentrocosto_id
			WHERE ecc.enlacecc_edo=1
		';		

	if(isset($_POST['search']) and $_POST['search']!=NULL)
	{
		$sql .= ' AND CONCAT_WS (" ", ecc.ctacontable) LIKE "%'.$_POST['search'].'%" ';
	}
	$result = DB_query($sql,$db,$sqls);
	$k=0;
	while ($myrow = DB_fetch_array($result)) {

		echo "<TR>
		<TD align=right>".$myrow['enlacecc_id']."</TD>
		<TD>".$myrow['centrocostosubfijo']." ".$myrow['centrocostonombre']."</TD>
		<TD>".$myrow['subcentrocostosubfijo']." ".$myrow['subcentrocostonombre']."</TD>
		<TD>".$myrow['categoriasubfijo']." ".$myrow['categorianombre']."</TD>
		<TD>".$myrow['cuentacontable_resultado']."</TD>
		<TD align=right><a href='catalogo_enlacecontable.php?id=".$myrow['enlacecc_id']."&action=editar' >"._('Edit')."</a></TD>";
		?>
		<TD align=right><a href='catalogo_enlacecontable.php?id=<?php echo $myrow['enlacecc_id'] ?>&action=eliminar' onclick='return confirm("¿Estas Seguro de Eliminarlo?")' >Eliminar</a></TD>
		<?php echo "
		</TR>";
	}

	

	echo "</tbody></TABLE>
	</span>
	</CENTER>";

	echo '
	<script type="text/javascript">
		function Mostrarsubcentro(idcentro,seleccionada)
		{
			
			if(idcentro!="" || idcentro==undefined){
				$("#subcentrocosto_id").removeAttr("disabled");
				$("#subcentrocosto_id").children("option").hide();
				$("#subcentrocosto_id").val("");
				$("#subcentrocosto_id").children("option[data-valor=0]").show();
    			$("#subcentrocosto_id").children("option[data-idcentro=" + idcentro + "]").show();
    			if(seleccionada!=0 && seleccionada!="")
				{
					$("#subcentrocosto_id").val(seleccionada);
				}
			}else{
				$("#subcentrocosto_id").val("");
				$("#subcentrocosto_id").attr("disabled","true");
			}
			
		}
		function Ctacontable()
		{
			var subfijo1 = $("#centrocosto_id").find(":selected").attr("data-subfijo1");
			var subfijo2 = $("#subcentrocosto_id").find(":selected").attr("data-subfijo2");
			var subfijo3 = $("#categoriacc_id").find(":selected").attr("data-subfijo3");

			if(subfijo1=="" || subfijo1==undefined ||  subfijo2=="" || subfijo2==undefined ||  subfijo3=="" || subfijo3==undefined){
				$("#ctacontable").val("");
				return false;
			}

			var ctacontable = subfijo1 +"-"+ subfijo2 +"-"+ subfijo3;
			$("#ctacontable").val(ctacontable);
		}
	</script>';
	if($_GET['action']!='editar')
	{
		// SE AGREGA UN NUEVO REGISTRO
	
	echo "
	<CENTER>

	<hr>
	<h3>Alta de Cuentas Contables</h3>
	<FORM NAME='altasubcentrocosto' method='POST'>
	<TABLE BORDER=1  width='45%'>
	
	<TR>
		<TD>
			"._('Centro de Costos').":</TD>
		<TD>
			<select name='centrocosto_id' id='centrocosto_id' onchange='Mostrarsubcentro(this.value,0);Ctacontable();' class='select2'>
				<option value=''>-- Seleccione --</option>
		";
	// hacemos una consulta para obtener todo  de la tabla de centro de costo
	$sqls_centrocosto = 'SELECT * FROM wrk_centrocosto
			where centrocosto_edo = 1';
	$result_centrocosto = DB_query($sqls_centrocosto,$db);
	while ($rowcentrocosto = DB_fetch_array($result_centrocosto)) {
		// hacemos los options
		echo '<option value="'.$rowcentrocosto['centrocosto_id'].'" data-subfijo1="'.$rowcentrocosto['subfijo'].'">'.$rowcentrocosto['subfijo'].' '.$rowcentrocosto['centrocosto'].'</option>';
	}
	// termina
	echo "
			</select>
		</TD>
	</TR>
	<TR>
		<TD>
			"._('Sub Centro de Costos').":</TD>
		<TD>
			<select name='subcentrocosto_id' id='subcentrocosto_id' onchange='Ctacontable()' disabled class='select2'>
				<option value='' data-valor=0>-- Seleccione --</option>
		";
	// hacemos una consulta para obtener todo  de la tabla de centro de costo
	$sqlsub = 'SELECT * FROM wrk_subcentrocosto	
			where subcentrocosto_edo = 1 ORDER BY subcentrocosto';
	$result_sub = DB_query($sqlsub,$db);
	while ($rowsub = DB_fetch_array($result_sub)) {
		// hacemos los options
		echo '<option value="'.$rowsub['subcentrocosto_id'].'" data-idcentro="'.$rowsub['centrocosto_id'].'" data-subfijo2="'.$rowsub['subfijo'].'">'.$rowsub['subfijo'].' '.$rowsub['subcentrocosto'].'</option>';
	}
	// termina
	echo "
			</select>
		</TD>
	</TR>
	<TR>
		<TD>
			"._('Categorias').":</TD>
		<TD>
			<select name='categoriacc_id' onchange='Ctacontable()' id='categoriacc_id' class='select2'>
				<option value=''>-- Seleccione --</option>
		";
	// hacemos una consulta para obtener todo  de la tabla de centro de costo
	$sql_categoria = 'SELECT * FROM wrk_categoriascc
			where categoriacc_edo = 1';
	$result_categoria = DB_query($sql_categoria,$db);
	while ($rowcategoria = DB_fetch_array($result_categoria)) {
		// hacemos los options
		echo '<option value="'.$rowcategoria['categoriacc_id'].'" data-subfijo3="'.$rowcategoria['subfijo'].'">'.$rowcategoria['subfijo'].' '.$rowcategoria['categoriacc'].'</option>';
	}
	// termina
	echo "
			</select>
		</TD>
	</TR>
	<TR>
		<TD>
			"._('Centro Contable').":</TD>
		<TD>
			<INPUT TYPE='text' style='width:50%;' MAXLENGTH=200 NAME='ctacontable' readonly id='ctacontable'>
		</TD>
	</TR>
	</TABLE>

	<INPUT TYPE='submit' VALUE="._('Agregar')." NAME='guardarenlacecontable'>
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

	$sqleditar = 'SELECT * FROM wrk_enlacecc WHERE enlacecc_id = '.$id;
	$result = DB_query($sqleditar,$db);
	$datos = DB_fetch_array($result);

	echo "
	<hr>
	<CENTER>
		<h3>Editar Enlace Contable</h3>
		<FORM NAME='editarenlacecontable' method='POST'>
			<TABLE BORDER=1  width='45%'>
				
				<TR>
					<TD>
						"._('Centro de Costos').":</TD>
					<TD>
						<select name='centrocosto_id' id='centrocosto_id' onchange='Mostrarsubcentro(this.value,0);Ctacontable()' class='select2'>
							<option value=''>-- Seleccione --</option>
					";
				// hacemos una consulta para obtener todo  de la tabla de centro de costo
				$sqls_centrocosto = 'SELECT * FROM wrk_centrocosto where centrocosto_edo = 1 ORDER BY centrocosto_id';
				$result_centrocosto = DB_query($sqls_centrocosto,$db);
				while ($rowcentrocosto = DB_fetch_array($result_centrocosto)) {
					if($datos['centrocosto_id']==$rowcentrocosto['centrocosto_id'])
						{
						echo '<option value="'.$rowcentrocosto['centrocosto_id'].'" data-subfijo1="'.$rowcentrocosto['subfijo'].'" selected>'.$rowcentrocosto['subfijo'].' '.$rowcentrocosto['centrocosto'].'</option>';
					}else
					{
						echo '<option value="'.$rowcentrocosto['centrocosto_id'].'" data-subfijo1="'.$rowcentrocosto['subfijo'].'" >'.$rowcentrocosto['subfijo'].' '.$rowcentrocosto['centrocosto'].'</option>';
					}
				}
				echo "
						</select>
					</TD>
				</TR>
				<TR>
					<TD>
						"._('Sub Centro de Costos').":</TD>
					<TD>
						<select name='subcentrocosto_id' id='subcentrocosto_id' onchange='Ctacontable()' class='select2'>
							<option value='' data-valor=0>-- Seleccione --</option>
					";
					// hacemos una consulta para obtener todo  de la tabla de centro de costo
					$sqlsub = 'SELECT * FROM wrk_subcentrocosto	where subcentrocosto_edo = 1 ORDER BY subcentrocosto';
					$result_sub = DB_query($sqlsub,$db);
					while ($rowsub = DB_fetch_array($result_sub)) {
						if($datos['subcentrocosto_id']==$rowsub['subcentrocosto_id'])
						{
							echo '<option value="'.$rowsub['subcentrocosto_id'].'" data-idcentro="'.$rowsub['centrocosto_id'].'" data-subfijo2="'.$rowsub['subfijo'].'" selected>'.$rowsub['subfijo'].' '.$rowsub['subcentrocosto'].' </option>';
						}else
						{
							echo '<option value="'.$rowsub['subcentrocosto_id'].'" data-idcentro="'.$rowsub['centrocosto_id'].'" data-subfijo2="'.$rowsub['subfijo'].'">'.$rowsub['subfijo'].' '.$rowsub['subcentrocosto'].' </option>';
						}
					}
					// termina
					echo "
							</select>";
					echo '<script>Mostrarsubcentro("'.$datos['centrocosto_id'].'","'.$datos['subcentrocosto_id'].'")</script>';
					echo "
						</TD>
					</TR>
				<TR>
					<TD>
						"._('Categorias').":</TD>
					<TD>
						<select name='categoriacc_id' id='categoriacc_id' onchange='Ctacontable()' class='select2'>
							<option value=''>-- Seleccione --</option>
					";
				// hacemos una consulta para obtener todo  de la tabla de centro de costo
				$sql_categoria = 'SELECT * FROM wrk_categoriascc where categoriacc_edo = 1 Order BY categoriacc_id asc';
				$result_categoria = DB_query($sql_categoria,$db);
				while ($rowcategoria = DB_fetch_array($result_categoria)) {
					// hacemos los options
					if($datos['categoriacc_id']==$rowcategoria['categoriacc_id'])
					{
						echo '<option value="'.$rowcategoria['categoriacc_id'].'" data-subfijo3="'.$rowcategoria['subfijo'].'" selected>'.$rowcategoria['subfijo'].' '.$rowcategoria['categoriacc'].'</option>';
					}else
					{
						echo '<option value="'.$rowcategoria['categoriacc_id'].'" data-subfijo3="'.$rowcategoria['subfijo'].'" >'.$rowcategoria['subfijo'].' '.$rowcategoria['categoriacc'].'</option>';
					}
				}
				// termina
				echo "
						</select>
					</TD>
				</TR>
				<TR>
					<TD>
						"._('Centro Contable').":</TD>
					<TD>
						<INPUT TYPE='text' style='width:50%;' MAXLENGTH=200 NAME='ctacontable' id='ctacontable' value='".$datos['ctacontable']."' readonly>
					</TD>
				</TR>
			</TABLE>
			<INPUT TYPE='hidden' VALUE='".$datos['enlacecc_id']."' NAME='enlacecc_id'>
			<INPUT TYPE='submit' VALUE="._('Guardar Cambios')." NAME='actualizarenlacecontable'>
		</FORM>
		<br>
		<a href='catalogo_enlacecontable.php'>Agregar Nuevo Enlace Contable</a>
	</CENTER>
		";

	
	}

include('includes/footer.inc');
?>
