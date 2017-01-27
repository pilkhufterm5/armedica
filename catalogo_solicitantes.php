<?php
/* $Revision: 1 $ */

/**************************************************************************
* Ruben Flores Barrios 26/Nov/2016
* Archivo creado para el catalogo de Solicitantes
***************************************************************************/
//Seguridad de la pagina
//$PageSecurity = 1;

include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('Catalogo de Solicitantes');
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
wrk_solicitantecc
- solicitantecc_id
- autorizadorcc_id
- solicitantecc
- centrocosto_id
- usuario
- fechaultimomov
- solicitantecc_edo
*/

// Al hacer clic en grabar un nuevo solicitante
if(isset($_POST['guardarsolicitante']))
{
	/*print_r($_POST);
	exit;*/
	$insert = true;
	// CAMPOS PARA INSERTAR
	$autorizadorcc_id = $_POST['autorizadorcc_id'];
	$solicitantecc = $_POST['solicitantecc'];
	$centrocosto_id = $_POST['centrocosto_id'];
	$usuario = $_SESSION['UserID'];
	$fechaultimomov = date('Y-m-d H:i:s');
	$solicitantecc_edo = 1;
	
	if($autorizadorcc_id==''){

		prnMsg(_('El campo se encuentra vacio, favor de intentarlo de nuevo '),'error');
		$insert = false;
	} 
	if($solicitantecc==''){

		prnMsg(_('El campo se encuentra vacio, favor de intentarlo de nuevo '),'error');
		$insert = false;
	} 
	if($centrocosto_id==''){

		prnMsg(_('El campo se encuentra vacio, favor de intentarlo de nuevo '),'error');
		$insert = false;
	} 


	if($insert)
	{
		$sqlinsert = "INSERT INTO wrk_solicitantecc (
			autorizadorcc_id,
			solicitantecc,
			centrocosto_id,
			usuario,
			fechaultimomov,
			solicitantecc_edo
		)
		 VALUES (
			 	'" . $autorizadorcc_id . "',
			 	'" . $solicitantecc . "',
			 	'" . $centrocosto_id . "',
		 		'" . $usuario . "',
		 		'" . $fechaultimomov . "',
		 		'" . $solicitantecc_edo . "'
		)";

		$ErrMsg = _('El solicitante') . '  ' . _('no pudo ser ingresado debido');
		$DbgMsg = _('Se intento insertar el nuevo solicitante pero hubo un fallo');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se agrego correctamente'),'success');
		unset($_GET['action']);
	}
}
// Al hacer clic en grabar solicitante
if(isset($_POST['actualizarsolicitante']))
{
	/*print_r($_POST);
	exit;*/
	$UPDATE = true;
	$solicitantecc_id = $_POST['id'];
	$autorizadorcc_id = $_POST['autorizadorcc_id'];
	$solicitantecc = $_POST['solicitantecc'];
	$centrocosto_id = $_POST['centrocosto_id'];
	$usuario = $_SESSION['UserID'];
	$fechaultimomov = date('Y-m-d H:i:s');
	$solicitantecc_edo = 1;

	if($solicitantecc_id==''){

		prnMsg(_('Seleccione un registro, vuelva a intentarlo '),'error');
		$delete = false;
	} 

	if($autorizadorcc_id==''){

		prnMsg(_('El campo esta vacio, vuelva a intentarlo por favor'),'error');
		$insert = false;
	} 
	if($solicitantecc==''){

		prnMsg(_('El campo esta vacio, vuelva a intentarlo por favor'),'error');
		$insert = false;
	} 
	if($centrocosto_id==''){

		prnMsg(_('El campo esta vacio, vuelva a intentarlo por favor'),'error');
		$insert = false;
	}  	
	if($UPDATE)
	{
		$sqlinsert = "UPDATE wrk_solicitantecc 
			SET 
				autorizadorcc_id='$autorizadorcc_id',
				solicitantecc='$solicitantecc',
				centrocosto_id='$centrocosto_id',
				usuario='$usuario',
				fechaultimomov='$fechaultimomov',
				solicitantecc_edo='$solicitantecc_edo' 
			WHERE solicitantecc_id = " .$solicitantecc_id ; 

		$ErrMsg = _('El solicitante') . '  ' . _('no pudo ser ingresado debido');
		$DbgMsg = _('Se intento insertar el nuevo solicitante pero fallo');
		//echo $sqlinsert;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se actualizo correctamente'),'success');
		unset($_GET['action']);
	}
}

// Al hacer clic en eliminar un solicitante
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
		$sqlinsert = "UPDATE wrk_solicitantecc SET solicitantecc_edo = 0,usuario='$usuario',fechaultimomov='$fechaultimomov' WHERE solicitantecc_id = " . $id;

		$ErrMsg = _('El autorizador') . ' ' . $id . ' ' . _('no se pudo eliminar');
		$DbgMsg = _('Se intento eliminar el autorizador pero hubo un fallo');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		prnMsg(_('Se elimino correctamente'),'success');
	}
}




echo "<CENTER><H1>Cat&aacute;logo de Solicitantes</H1></CENTER>";

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
		<TD CLASS='tableheader' width='14%'>Solicitantes</TD>
		<TD CLASS='tableheader' width='10%'>Autorizadores</TD>
		<TD CLASS='tableheader' width='8%'>Prefijo Contable</TD>
		<TD CLASS='tableheader' width='8%'>Acciones</TD>
		<TD CLASS='tableheader' width='8%'></TD>
	</TR>
	</thead>
	<tbody>";

	$sql = 'SELECT scc.solicitantecc_id,us_solicitante.realname as realnamesolic,us_autorizadores.realname as realnameaut,cc.centrocosto,cc.subfijo FROM wrk_solicitantecc scc
INNER JOIN wrk_autorizadorescc  acc on acc.autorizadorcc_id=scc.autorizadorcc_id
INNER JOIN wrk_centrocosto  cc on cc.centrocosto_id=scc.centrocosto_id
INNER JOIN www_users  us_solicitante on us_solicitante.userid=scc.solicitantecc
INNER JOIN www_users  us_autorizadores on us_autorizadores.userid=acc.autorizadorcc
 where solicitantecc_edo = 1';

	if(isset($_POST['search']) and $_POST['search']!=NULL)
	{
		$sql .= ' AND CONCAT_WS (" ", us.realname,cc.subfijo,cc.centrocosto) LIKE "%'.$_POST['search'].'%" ';
	}
	$result = DB_query($sql,$db);
	$k=0;
	while ($myrow = DB_fetch_array($result)) {
		
		echo "<TR>
		<TD align=right>".$myrow['solicitantecc_id']."</TD>
		<TD>".$myrow['realnamesolic']."</TD>
		<TD>".$myrow['realnameaut']."</TD>
		<TD>".$myrow['subfijo']." ".$myrow['centrocosto']."</TD>
		<TD align=right><a href='catalogo_solicitantes.php?id=".$myrow['solicitantecc_id']."&action=editar' >"._('Edit')."</a></TD>";
		?>
		<TD align=right><a href='catalogo_solicitantes.php?id=<?php echo $myrow['solicitantecc_id'] ?>&action=eliminar' onclick='return confirm("¿Estas Seguro de Eliminarlo?")' >Eliminar</a></TD>
		<?php echo "
		</TR>";
	}

	

	echo "</tbody></TABLE>
	</span>
	</CENTER>";

	echo '
	<script type="text/javascript">
		function Mostrarcentro(autorizador,seleccionada)
		{
			autorizador = $("#autorizadorcc_id").find(":selected").attr("data-user");
			
			if(autorizador!="" && autorizador!=undefined){
				$("#centrocosto_id").removeAttr("disabled");
				$("#centrocosto_id").children("option").hide();
				$("#centrocosto_id").val("");
				$("#centrocosto_id").children("option[data-valor=0]").show();
    			$("#centrocosto_id").children("option[data-idautorizador=" + autorizador + "]").show();
    			if(seleccionada!=0 && seleccionada!="")
				{
					$("#centrocosto_id").val(seleccionada);
				}
			}else{
				$("#centrocosto_id").val("");
				$("#centrocosto_id").attr("disabled","true");
			}
			
		}
	</script>';

	if($_GET['action']!='editar')
	{
		// SE AGREGA UN NUEVO SOLICITANTE
	
	echo "
	<CENTER>

	<hr>
		<h3>Alta de Solicitante</h3>
			<FORM NAME='altasolicitante' method='POST'>
				<TABLE BORDER=1  width='45%'>
					<TR>
						<TD>
							"._('Solicitantes').":
						</TD>
						<TD>
							<select name='solicitantecc' class='select2'>

								<option value=''>-- Seleccione --</option>
						";
								// hacemos una consulta para obtener los campos userid,realname de la tabla de www_users
								$sql_usuarios = 'SELECT userid,realname from www_users where blocked = 0 order by realname';
								$result_usuarios = DB_query($sql_usuarios,$db);
								while ($rowcentro = DB_fetch_array($result_usuarios)) {
									// hacemos los options
									echo '<option value="'.$rowcentro['userid'].'">'.$rowcentro['realname'].'</option>';
								}
								// termina
								echo "
							</select>
						</TD>
					</TR>
					<TR>
						<TD>
							"._('Autorizadores').":
						</TD>
						<TD>
							<select name='autorizadorcc_id' id='autorizadorcc_id' onchange='Mostrarcentro(this.value,0);' class='select2'>
								<option value=''>-- Seleccione --</option>
								";
								// hacemos una consulta para obtener todo de la tabla de autorizadores
								$sql_autorizador = '
												SELECT acc.*,us.realname,us.userid from wrk_autorizadorescc acc
												INNER JOIN www_users us on us.userid=acc.autorizadorcc 
												where acc.autorizadorcc_edo = 1 group by autorizadorcc';
								$result_autorizador = DB_query($sql_autorizador,$db);
								while ($rowautorizador = DB_fetch_array($result_autorizador)) {
									// hacemos los options
									echo '<option value="'.$rowautorizador['autorizadorcc_id'].'" data-user="'.$rowautorizador['userid'].'">'.$rowautorizador['realname'].'</option>';
								}
								// termina
								echo "
							</select>
						</TD>
					</TR>
					<TR>
						<TD>
							"._('Centro de Costo').":
						</TD>
						<TD>
							<select name='centrocosto_id' id='centrocosto_id' disabled class='select2'>
								<option value='' data-valor=0>-- Seleccione --</option>
								";
								// hacemos una consulta para obtener todo de la tabla de centro de costo
								$sql_centrocosto = '
									SELECT acc.centrocosto_id,acc.autorizadorcc,cc.centrocosto FROM wrk_autorizadorescc acc
									INNER JOIN wrk_centrocosto cc on cc.centrocosto_id=acc.centrocosto_id
									where autorizadorcc_edo = 1';
								$result_centrocosto = DB_query($sql_centrocosto,$db);
								while ($rowcentro = DB_fetch_array($result_centrocosto)) {
									// hacemos los options
									echo '<option value="'.$rowcentro['centrocosto_id'].'" data-idautorizador="'.$rowcentro['autorizadorcc'].'">'.$rowcentro['centrocosto'].'</option>';
								}
								// termina
								echo "
							</select>
						</TD>
					</TR>
				</TABLE>

			<INPUT TYPE='submit' VALUE="._('Accept')." NAME='guardarsolicitante'>
			<INPUT TYPE='reset' VALUE="._('Cancel').">
		</FORM>
	</CENTER>";
	}
	else if ($_GET['action']=='editar')
	{
	// Opcion para editar un solicitante
	$id = $_GET['id'];

	if($id=='')
	{
		prnMsg(_('Seleccione un registro y vuelva a intentarlo '),'error');
		$delete = false;
	}

	$sqleditar = 'SELECT * FROM wrk_solicitantecc WHERE solicitantecc_id = '.$id;
	$result = DB_query($sqleditar,$db);
	$datos = DB_fetch_array($result);
	echo "
	<hr>
	<CENTER>
		<h3>Editar Solicitante</h3>
		<FORM NAME='editarsolicitante' method='POST'>
			<TABLE BORDER=1  width='45%'>
				<TR>
						<TD>
							"._('Solicitantes').":
						</TD>
						<TD>
							<select name='solicitantecc' class='select2'>
								<option value=''>-- Seleccione --</option>
						";
								// hacemos una consulta para obtener los campos userid,realname de la tabla de www_users
								$sql_usuarios = 'SELECT userid,realname from www_users where blocked = 0 order by realname';
								$result_usuarios = DB_query($sql_usuarios,$db);
								while ($rowcentro = DB_fetch_array($result_usuarios)) {
									// hacemos los options
								if($datos['solicitantecc']==$rowcentro['userid'])
								{
									echo '<option value="'.$rowcentro['userid'].'" selected>'.$rowcentro['realname'].'</option>';
								}else
								{
									echo '<option value="'.$rowcentro['userid'].'" >'.$rowcentro['realname'].'</option>';
								}
								}
								// termina
								echo "
							</select>
						</TD>
					</TR>
				<TR>
					<TD>
						"._('Autorizadores').":
					</TD>
					<TD>
						<select name='autorizadorcc_id' id='autorizadorcc_id' onchange='Mostrarcentro(this.value,0);' class='select2'>
							<option value=''>-- Seleccione --</option>
							";
							// hacemos una consulta para obtener todo de la tabla de autorizadores
							$sql_autorizador = '
											SELECT acc.*,us.realname,us.userid from wrk_autorizadorescc acc
											INNER JOIN www_users us on us.userid=acc.autorizadorcc 
											where acc.autorizadorcc_edo = 1 group by autorizadorcc';
							$result_autorizador = DB_query($sql_autorizador,$db);
							while ($rowautorizador = DB_fetch_array($result_autorizador)) {
								// hacemos los options
								if($datos['autorizadorcc_id']==$rowautorizador['autorizadorcc_id'])
								{
									echo '<option value="'.$rowautorizador['autorizadorcc_id'].'" data-user="'.$rowautorizador['userid'].'" selected>'.$rowautorizador['realname'].'</option>';
								}else
								{
									echo '<option value="'.$rowautorizador['autorizadorcc_id'].'" data-user="'.$rowautorizador['userid'].'" >'.$rowautorizador['realname'].'</option>';
								}
							}
							// termina
							echo "
						</select>
					</TD>
				</TR>
				<TR>
					<TD>
						"._('Centro de Costo').":</TD>
					<TD>
						<select name='centrocosto_id' id='centrocosto_id' disabled class='select2'>
							<option value='' data-valor=0>-- Seleccione --</option>
								";
								// hacemos una consulta para obtener todo  de la tabla de centro de costo
								$sql_centrocosto = 'SELECT acc.centrocosto_id,acc.autorizadorcc,cc.centrocosto FROM wrk_autorizadorescc acc
									INNER JOIN wrk_centrocosto cc on cc.centrocosto_id=acc.centrocosto_id
									where autorizadorcc_edo = 1';
								$result_centrocosto = DB_query($sql_centrocosto,$db);
								while ($rowcentro = DB_fetch_array($result_centrocosto)) {
								// hacemos los options
								if($datos['centrocosto_id']==$rowcentro['centrocosto_id'])
								{
									echo '<option value="'.$rowcentro['centrocosto_id'].'" data-idautorizador="'.$rowcentro['autorizadorcc'].'" selected>'.$rowcentro['subfijo'].' '.$rowcentro['centrocosto'].'</option>';
								}else
								{
									echo '<option value="'.$rowcentro['centrocosto_id'].'" data-idautorizador="'.$rowcentro['autorizadorcc'].'" >'.$rowcentro['subfijo'].' '.$rowcentro['centrocosto'].'</option>';
								}
							}
							// termina
							echo "
						</select>";
						echo '<script>Mostrarcentro("'.$datos['autorizadorcc_id'].'","'.$datos['centrocosto_id'].'")</script>';
						echo "
					</TD>
				</TR>
			</TABLE>
			<INPUT TYPE='hidden' VALUE='".$datos['solicitantecc_id']."' NAME='id'>
			<INPUT TYPE='submit' VALUE="._('Guardar Cambios')." NAME='actualizarsolicitante'>
		</FORM>
		<br>
		
	</CENTER>
		";

	
	}
	echo "<center><a href='catalogo_solicitantes.php'>Agregar Nuevo Solicitante</a></center>";

include('includes/footer.inc');
?>
