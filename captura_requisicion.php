<?php

/* $Revision: 1 $ */

/**************************************************************************
* Ruben Flores Barrios 05/Dic/2016
* Archivo creado para la Captura de Requisiciones
***************************************************************************/
//Seguridad de la pagina
//$PageSecurity = 1;

include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('Captura de Requisiciones');



// validamos que el usuario en sesion exista en solicitantes
$usuario=$_SESSION['UserID'];
$sqls_usuario = 'SELECT * FROM wrk_solicitantecc where solicitantecc_edo = 1 and solicitantecc="'.$usuario.'"';
$resultusuario = DB_query($sqls_usuario,$db);
	if(DB_num_rows($resultusuario)==0)
{
	prnMsg(_('No se encontro el usuario'),'error');
	$insert = false;	
}
$rowusuario = DB_fetch_array($resultusuario);

if(DB_num_rows($resultusuario)==0)
{
	include('includes/header.inc');
	// no existe en la tabla de solicitante, mostramos pantalla no autorizada
	prnMsg(_('No existe como solicitante'),'error');
	include ('includes/footer.inc');
    exit ();
}

echo '<script language="JavaScript" src="CalendarPopup.js"></script>';
?>
<script language="JavaScript">
var cal = new CalendarPopup();
</script>



<?php
if(isset($_POST['guardarrequisicion']))
{
	//print_r($_POST);
	$insert = true;
	// CAMPOS PARA INSERTAR
	$centrocosto_id = $_POST['centrocosto_id'];
	$subcentrocosto_id = $_POST['subcentrocosto_id'];
	$categoriacc_id = $_POST['categoriacc_id'];
	$enlacecc_edo = $_POST['enlacecc_edo'];
	$fecha_requisicion = date("Y-m-d", strtotime($_POST['fecha_requisicion']));
	// cambiamos el formato de fecha
	$fecha_requisicion = $_POST['fecha_requisicion'];
	$fecha_requisicion = str_replace('/', '-', $fecha_requisicion);
	$fecha_requisicion = date('Y-m-d', strtotime($fecha_requisicion));
	$ctacontable = trim(str_replace("-","",$_POST['ctacontable']));
	$usuario = $_SESSION['UserID'];
	$intostocklocation = $_POST['intostocklocation'];
	$comments = $_POST['comentarios'];

	// verificamos que existe en chartmaster el campo de ctacontable
	$sqls_ctacontable = 'SELECT * FROM chartmaster where accountcode="'.$ctacontable.'"';
	$resultctacontable = DB_query($sqls_ctacontable,$db);
	if(DB_num_rows($resultctacontable)==0)
	{
		prnMsg(_('La cuenta contable no existe, favor de intentarlo de nuevo '),'error');
		$insert = false;	
	}

	// Verificamos que exista en wrk_enlacecc y luego obtenemos los datos de wrk_enlacecc para los campos enlacecc_id,solicitantecc_id y autorizadorcc_id
	$sqls_enlacecc = 'SELECT * FROM wrk_enlacecc where ctacontable="'.$ctacontable.'" ';
	$resultenlacecc = DB_query($sqls_enlacecc,$db);
	if(DB_num_rows($resultenlacecc)==0)
	{
		prnMsg(_('El enlace contable no existe, favor de intentarlo de nuevo '),'error');
		$insert = false;	
	}
	$enlace = DB_fetch_array($resultenlacecc);
	// Verificamos que exista el almacen loccode y luego obtenemos los datos de la locations para obtener deladd1-deladd10, tel y contacto - tabla locstock campo loccode
	$sqls_locations = 'SELECT * FROM locations WHERE  loccode="'.$intostocklocation.'"';
	$resultlocations = DB_query($sqls_locations,$db);
	if(DB_num_rows($resultlocations)==0)
	{
		prnMsg(_('El almancen no existe, favor de intentarlo de nuevo '),'error');
		$insert = false;	
	}
	$location = DB_fetch_array($resultlocations);
	// si llegamos a este punto es por que ya podemos insertar.
	if($insert)
	{
		$sqlinsert = "INSERT INTO wrk_requisicion (
			centrocosto_id,
			subcentrocosto_id,
			categoriacc_id,
			ctacontable,
			reqdate,
			initiator,
			intostocklocation,
			reqno,
			status,
			deladd1,
			deladd2,
			deladd3,
			deladd4,
			deladd5,
			deladd6,
			deladd7,
			deladd8,
			deladd9,
			deladd10,
			tel,
			contact,
			comments,
			enlacecc_id,
			solicitantecc_id,
			autorizadorcc_id

		) 
		 VALUES (
		 	'" . $centrocosto_id . "',
		 	'" . $subcentrocosto_id . "',
		 	'" . $categoriacc_id . "',
		 	'" . $ctacontable . "',
		 	'" . $fecha_requisicion . "',
		 	'" . $usuario . "',
		 	'" . $intostocklocation . "',
		 	'0',
		 	'Nueva',
		 	'" . $location['deladd1'] . "',
		 	'" . $location['deladd2'] . "',
		 	'" . $location['deladd3'] . "',
		 	'" . $location['deladd4'] . "',
		 	'" . $location['deladd5'] . "',
		 	'" . $location['deladd6'] . "',
		 	'" . $location['deladd7'] . "',
		 	'" . $location['deladd8'] . "',
		 	'" . $location['deladd9'] . "',
		 	'" . $location['deladd10'] . "',
		 	'" . $location['tel'] . "',
		 	'" . $location['contact'] . "',
		 	'" . $comments . "',
		 	'" . $enlace['enlacecc_id'] . "',
		 	'" . $rowusuario['solicitantecc_id'] . "',
		 	'" . $rowusuario['autorizadorcc_id'] . "'
		 )";
		//echo $sqlinsert;
		//exit;
		$ErrMsg = _('La requisicion') . '  ' . _('no pudo ser agregada');
		$DbgMsg = _('Se intento insertar la requisicion pero hubo un fallo inesperado');
		//echo $sql;
		$result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
		// redireccionamos para agregar los productos
		$reqid = DB_Last_Insert_ID($db,'wrk_requisicion','reqid');
		header('location:requisiciones_detalle.php?reqid='.$reqid);
		prnMsg(_('Se agrego correctamente'),'success');
		unset($_GET['action']);
	}
}



include('includes/header.inc');

    echo "<CENTER><H1>Captura de Requisiciones</H1></CENTER>";

	echo "<script>
	$( document ).ready(function() {
		/*$('.select2').select2();*/
		$('#listarequisicion').dataTable();
	});
	</script>";

	echo "
	<CENTER>
	<span id='datagrid'>
	<form method='POST'>
	</form>
	<TABLE width='' id='registros' class='table table-bordered table-striped table-hover table-condensed'>
	<thead>
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
	</script>
	';


		// SE AGREGA UN NUEVO REGISTRO

	echo "

	<CENTER>

	<hr>
		<thead>
		</thead>
			<FORM NAME='altacapturarequisiciones' method='POST'>
				<TABLE BORDER=1  width='45%'>
					<tbody>
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
							"._('Fecha').":
						</TD>
						<TD>
							<INPUT type=text name='fecha_requisicion' MAXLENGTH =10 SIZE=11 value=" . date('Y-m-d') . ">
							<a href=\"#\" onclick=\"altacapturarequisiciones.fecha_requisicion.value='';cal.select(document.forms['altacapturarequisiciones'].fecha_requisicion,'from_date_anchor','d/M/yyyy');
				                return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
				            <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>
						</TD>
					</TR>
					<TR>
						<TD>
							"._('Cuenta Contable').":
						</TD>
						<TD>
							<input type='text' name='ctacontable' id='ctacontable' readonly='true'>
						</TD>
					</TR>
					<TR>
                        <td>Almacen</td>
                        <td>
                        <select name='intostocklocation' id='intostocklocation' class='select2'>
								<option value=''>-- Seleccione --</option>
                        ";
                           // hacemos una consulta para obtener todo  de la tabla de locations
							$sql_location = 'SELECT * FROM locations
									where loccode != 0';
							$result_location = DB_query($sql_location,$db);
							while ($rowlocation = DB_fetch_array($result_location)) {
								// hacemos los options
								echo '<option value="'.$rowlocation['loccode'].'">'.$rowlocation['locationname'].' </option>';
							}
							// termina
                           echo "  </select>
                            
                        </td>
                    </TR>
					<TR>
						<TD>
							"._('Comentarios').":
						</TD>
						<TD>
							<textarea style='width:80%;' NAME='comentarios'></textarea>
						</TD>
					</TR>
					</tbody>
				</TABLE>
				<INPUT TYPE='submit' VALUE='"._('Agregar Requisicion')."' NAME='guardarrequisicion'>
			</FORM>
	</CENTER>
";


include('includes/footer.inc');
?>
