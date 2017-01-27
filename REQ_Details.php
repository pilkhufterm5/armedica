<?php
/* $Revision: 1 $ */

/**************************************************************************
* Ruben Flores Barrios 19/Dic/2016
* Archivo creado para aceptar una req y hacer la orden de compra
***************************************************************************/
//Seguridad de la pagina
//$PageSecurity = 1;

include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('Opciones de la Requisicion');


include('includes/header.inc');


// obtenemos el id de la requisicion
$reqid = $_GET['reqid'];
if($reqid==''){ $reqid=$_GET['reqid'];}
$identifier = $reqid;

if(empty($reqid))
{
        // no existe en la tabla de solicitante, mostramos pantalla no autorizada
        prnMsg(_('El error esta en el id del identificador'),'error');
        include ('includes/footer.inc');
        exit();
}






$usuario=$_SESSION['UserID'];
$sql_usuario = "SELECT wrkr.*,
				wrkr.reqid,
				wrkr.trandatetime,
				wrkr.reqdate,
				wrka.autorizadorcc as autorizadorbase,
				wrks.solicitantecc as solicitantebase,
				wrkc.categoriacc as categoriabase,
				wrkcc.centrocosto as centrocostobase,
				wrkr.status
		FROM wrk_requisicion wrkr
		INNER JOIN wrk_autorizadorescc wrka ON wrka.autorizadorcc_id=wrkr.autorizadorcc_id
		INNER JOIN wrk_solicitantecc wrks ON wrks.solicitantecc_id=wrkr.solicitantecc_id
		INNER JOIN wrk_categoriascc wrkc ON wrkc.categoriacc_id=wrkr.categoriacc_id
		INNER JOIN wrk_centrocosto wrkcc ON wrkcc.centrocosto_id=wrkr.centrocosto_id
		WHERE wrkr.reqid='".$reqid."'";

// si el usuario es de compras, puede verla 
$sqlacceso = 'SELECT or_compras FROM www_users where userid ="'.$_SESSION['UserID'].'"';
$resultacceso = DB_query($sqlacceso,$db);
$rowacceso = DB_fetch_array($resultacceso);
if($rowacceso['or_compras']==1)
{
     $sql_usuario.= ' ';
}else{
   $sql_usuario.= '  AND  wrka.autorizadorcc = "'.$_SESSION['UserID'].'" OR 
   wrkr.reqid="'.$reqid.'" and wrks.solicitantecc = "'.$_SESSION['UserID'].'" ';
}

//echo $sql_usuario;
$resultusuario = DB_query($sql_usuario,$db);
$resultusuario2 = DB_query($sql_usuario,$db);
$k=0;

$rowreq =  DB_fetch_array($resultusuario2);
if(empty($rowreq)){
		prnMsg(_('No cuenta con el acceso a la requisicion'),'error');
	  include ('includes/footer.inc');
        exit();
}

// PROCESO PARA CANCELAR LA RQUISICION
if(isset($_POST['btncancelarreq']))
{
	$idrechazo = $_POST['motivorechazoid'];
	$textorechazo = $_POST['motivorechazotext'];
	$update = true;
	// verificamos que no esten vacios
	if(empty($idrechazo) || empty($textorechazo))
	{
		// no procede a cancelar, campos vacios	
		prnMsg(_('Para cancelar la requisicion es necesario seleccionar un motivo e ingresar el texto'),'error');
		$update = false;
	}
	if($update)
	{
		/*
		motivo_rechazo
		fecha_rechazo
		rechaza_compras
		status
		stat_comment
		*/
		$stat_comment=$textorechazo.' Cancel '.date('Y-m-d').'by user'.$_SESSION['UserID'];
		$sql="UPDATE wrk_requisicion
			SET 
			motivo_rechazo='".$idrechazo."',
			fecha_rechazo='".date('Y-m-d H:i:s')."',
			rechaza_compras='1',
			status='Cancel',
			stat_comment='$stat_comment'
			WHERE reqid='".$reqid."' and status in ('Nueva','Authorised','Pending')";
		$result=DB_query($sql, $db);		
		prnMsg(_('Requisicion cancelada con exito'),'success');
	}
}

// obtenemos los datos enviados por el formulario
if(isset($_POST['btncreatereq']) && isset($_POST['reqid']) && $_POST['tipodegeneracion']!='-9')
{
	unset($_SESSION['PO'.$identifier]);
	$reqid  = $_POST['reqid'];
	// obtenemos los datos de la requisicion y generamos un identificador
	$sql = "SELECT wrkr.*,
				wrkr.reqid,
				wrkr.trandatetime,
				wrkr.reqdate,
				wrka.autorizadorcc as autorizadorbase,
				wrks.solicitantecc as solicitantebase,
				wrkc.categoriacc as categoriabase,
				wrkcc.centrocosto as centrocostobase,
				wrkr.status
		FROM wrk_requisicion wrkr
		INNER JOIN wrk_autorizadorescc wrka ON wrka.autorizadorcc_id=wrkr.autorizadorcc_id
		INNER JOIN wrk_solicitantecc wrks ON wrks.solicitantecc_id=wrkr.solicitantecc_id
		INNER JOIN wrk_categoriascc wrkc ON wrkc.categoriacc_id=wrkr.categoriacc_id
		INNER JOIN wrk_centrocosto wrkcc ON wrkcc.centrocosto_id=wrkr.centrocosto_id
		WHERE wrkr.reqid='".$reqid."'";
	$resultsql = DB_query($sql,$db);
	$myrow = DB_fetch_array($resultsql);


	// datos del proveedor seleccionado
	$SQL = "SELECT * FROM suppliers WHERE suppliers.supplierid = '".$_POST['supplierid']."' ORDER BY suppliers.supplierid";
	$resultsql_sup = DB_query($SQL,$db);
	$rowsupp = DB_fetch_array($resultsql_sup);
	/*echo '<pre>';
	print_r($myrow);
	print_r($rowsupp);
	echo '</pre>';
	exit;*/
	include('includes/DefinePOClass.php');
	// generamos el identificador
	$identifier=date('U');
	// generamos la variable de sesion
	$_SESSION['PO'.$identifier] = new PurchOrder;
	$_SESSION['PO'.$identifier]->AllowPrintPO = 1; 
	$_SESSION['PO'.$identifier]->GLLink = $_SESSION['CompanyRecord']['gllink_stock'];
	$_SESSION['PO'.$identifier]->SupplierID = $_POST['supplierid'];
	$_SESSION['PO'.$identifier]->SupplierName = $rowsupp['suppname'];
	$_SESSION['PO'.$identifier]->Location=$myrow['intostocklocation'];
	$_SESSION['PO'.$identifier]->LocationSnd=$myrow['intostocklocation'];
	$_SESSION['PO'.$identifier]->DelAdd1 = $myrow['deladd1'];
	$_SESSION['PO'.$identifier]->DelAdd2 = $myrow['deladd2'];
	$_SESSION['PO'.$identifier]->DelAdd3 = $myrow['deladd3'];
	$_SESSION['PO'.$identifier]->DelAdd4 = $myrow['deladd4'];
	$_SESSION['PO'.$identifier]->DelAdd5 = $myrow['deladd5'];
	$_SESSION['PO'.$identifier]->DelAdd6 = $myrow['deladd6'];
	$_SESSION['PO'.$identifier]->suppDelAdd1 = $rowsupp['address1'];
	$_SESSION['PO'.$identifier]->suppDelAdd2 = $rowsupp['address2'];
	$_SESSION['PO'.$identifier]->suppDelAdd3 = $rowsupp['address3'];
	$_SESSION['PO'.$identifier]->suppDelAdd4 = $rowsupp['address4'];
	$_SESSION['PO'.$identifier]->suppDelAdd5 = $rowsupp['address5'];
	$_SESSION['PO'.$identifier]->Initiator = $myrow['initiator'];
	$_SESSION['PO'.$identifier]->RequisitionNo = $myrow['reqid'];

	// en base al tipo de generacion 
	$tipodegeneracion = $_POST['tipodegeneracion'];
	/*
		2 = por proveedor, realiza la oc al proveedor seleccionado
		1 = por precio mas barato, realiza la oc donde el proveedor seleccionado sea el del precio mas barato.
	*/
	if($tipodegeneracion==2)
	{
		// por proveedor
		// obtenemos los items de la requisicion que cuenten con ese proveedor y los insertamoss
		$linesql = "SELECT stockmaster.description,
							stockmaster.stockid,
							stockmaster.units,
							stockmaster.decimalplaces,
							stockmaster.kgs,
							stockmaster.netweight,
							stockcategory.stockact,
							chartmaster.accountname,
							wrk_requisiciondetalle.quantityreq,
							wrk_requisiciondetalle.quantityord,
							wrk_requisiciondetalle.completed,
							wrk_precioproveedor.price
						FROM stockcategory,
							chartmaster,
							stockmaster,
							wrk_requisiciondetalle,
							wrk_precioproveedor
						WHERE chartmaster.accountcode = stockcategory.stockact
							AND stockcategory.categoryid = stockmaster.categoryid
							AND stockmaster.stockid = wrk_requisiciondetalle.itemcode
							AND wrk_requisiciondetalle.reqno = '".$reqid."'
							AND wrk_precioproveedor.supplierid = '".$_POST['supplierid']."'
							AND wrk_precioproveedor.stockid = wrk_requisiciondetalle.itemcode
					";
	}
	elseif($tipodegeneracion==1)
	{
		// por precio mas barato
		// obtenemos los items de la requisicion que cuenten con ese proveedor y los insertamoss
		$linesql = "SELECT stockmaster.description,
						stockmaster.stockid,
						stockmaster.units,
						stockmaster.decimalplaces,
						stockmaster.kgs,
						stockmaster.netweight,
						stockcategory.stockact,
						chartmaster.accountname,
						wrk_requisiciondetalle.quantityreq,
						wrk_requisiciondetalle.quantityord,
						wrk_requisiciondetalle.completed,
						wrk_precioproveedor.price
					FROM stockcategory,
						chartmaster,
						stockmaster,
						wrk_requisiciondetalle,
						wrk_precioproveedor
					WHERE chartmaster.accountcode = stockcategory.stockact
						AND stockcategory.categoryid = stockmaster.categoryid
						AND stockmaster.stockid = wrk_requisiciondetalle.itemcode
						AND wrk_requisiciondetalle.reqno = '".$reqid."'
						AND wrk_precioproveedor.supplierid = '".$_POST['supplierid']."'
						AND wrk_precioproveedor.stockid = wrk_requisiciondetalle.itemcode
						AND wrk_precioproveedor.supplierid = (
							SELECT
								suppliers.supplierid
							FROM
								wrk_precioproveedor
							INNER JOIN suppliers ON suppliers.supplierid = wrk_precioproveedor.supplierid
							WHERE
								wrk_precioproveedor.stockid = wrk_requisiciondetalle.itemcode
							ORDER BY
								price,suppliers.suppname ASC
							LIMIT 1
						)  
				";
	}

	$lineresult=DB_query($linesql, $db);
	while ($myrow=DB_fetch_array($lineresult)) {
		$Quantity = $myrow['quantityreq'] - $myrow['quantityord'];
		
		if($Quantity>0)
		{
			$_SESSION['PO'.$identifier]->add_to_order ($_SESSION['PO'.$identifier]->LinesOnOrder+1,
			$myrow['stockid'],
			0, /*Serialised */
			0, /*Controlled */
			$Quantity, /* Qty */
			$myrow['description'],
			$myrow['price'],
			$myrow['units'],
			$myrow['stockact'],
			$_SESSION['PO'.$identifier]->deliverydate,
			0,
			0,
			0,
			0,
			0,
			$myrow['accountname'],
			$myrow['decimalplaces'],
			$myrow['stockid'],
			$myrow['unitname'],
			$myrow['conversionfactor'],
			$myrow['suppliers_partno'],
			$Quantity*$myrow['price'],
			$myrow['leadtime'],
			'',
			0,
			$myrow['netweight'],
			$myrow['kgs'],
			'',
			$Quantity,
			$Quantity*$myrow['price']
			);
		}
	}
	// verificamos que el proveedor tenga items
	$create = true;
	if(!$_SESSION['PO'.$identifier]->LinesOnOrder>0)
	{
		// no procede a cancelar, campos vacios	
		prnMsg(_('El proveedor no cuenta con precios para los productos, o ya fueron generados.'),'error');
		$create = false;
	}
	// termina la insertada de items y redireccionamos para que terminen la oc
	if($create)
	{
		echo "<meta http-equiv='Refresh' content='0; url=" . $rootpath . '/PO_Itemsfromreq.php?' . SID . 'identifier='.$identifier. "'>";
		echo '<p>';
		prnMsg(_('You should automatically be forwarded to the entry of the purchase order line items page') . '. ' .
			_('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ') ' .
		"<a href='$rootpath/PO_Itemsfromreq.php?" . SID. 'identifier='.$identifier . "'>" . _('click here') . '</a> ' . _('to continue'),'info');
		include('includes/footer.inc');
		exit;	
	}
}


echo "<CENTER><H1>Opciones de la Requisicion</H1></CENTER>";

	echo "<script>
	$( document ).ready(function() {
	  $('.select2').select2();
	});
	function Filtrartabla()
	{
		var proveedor = $('#supplierid').val();
		var tipodegeneracion = $('#tipodegeneracion').val();
		if(proveedor=='-9' || tipodegeneracion=='-9')
		{
			$('#reqitems tbody tr').show(); 
			return false;
		}
		/*alert(proveedor);
		alert(tipodegeneracion);*/
		if(tipodegeneracion==1)
		{
			$('#reqitems tbody tr').hide(); 
    		$('#reqitems tbody tr[data-proveedorid=\"'+proveedor+'\"]').show(); 	
		}else
		{
			$('#reqitems tbody tr').show(); 
		}
		/*alert('entra a la funcion');*/
	}
	</script>";


	echo "
	<CENTER>
	<span id='datagrid'>
	<form method='POST'>
	</form>
	<TABLE BORDER=0>
	<thead>
		<TR>
			<TD CLASS='tableheader' width='4%'># Req</TD>
			<TD CLASS='tableheader' width='6%'>Fecha Creada</TD>
			<TD CLASS='tableheader' width='6%'>Fecha Req</TD>
			<TD CLASS='tableheader' width='10%'>Autorizador</TD>
			<TD CLASS='tableheader' width='10%'>Solicitante</TD>
			<TD CLASS='tableheader' width='8%'>Categoria</TD>
			<TD CLASS='tableheader' width='8%'>Centro de Costo</TD>
			<TD CLASS='tableheader' width='8%'>Estatus</TD>
		</TR>
	</thead>
	<tbody>";

	
	
	while ($myrow = DB_fetch_array($resultusuario)) {
		$datosreq = $myrow;
		echo "<TR>
		<TD>".$myrow['reqid']."</TD>
		<TD>".$myrow['trandatetime']."</TD>
		<TD>".$myrow['reqdate']."</TD>
		<TD>".$myrow['autorizadorbase']."</TD>
		<TD>".$myrow['solicitantebase']."</TD>
		<TD>".$myrow['categoriabase']."</TD>
		<TD>".$myrow['centrocostobase']."</TD>
		<TD><strong>".$myrow['status']."</strong></TD>
		";
	}

	echo "</tbody></TABLE>
	</span>
	</CENTER>";



	echo "
	<hr>
	<center>
		<table width=100% cellpadding='10'>
			<tr>
				<td width=60% valign=top cellpadding='20px'>
					<h2>Items</h2>
					<table class='reqitems' width=100% BORDER=1 ID='reqitems'>
						<thead>
						<tr id='headtritems'>
							<th>StockID</th>
							<th>Name</th>
							<th>Qty req</th>
							<th>Qty orded</th>
							<th>Completado</th>
							<th>Proveedor</th>
							<th>Precio</th>
						</tr>
						</head>
						<tbody>";
					// obtenemos los items de la req seleccionada
					$linesql="SELECT
								wrk_requisiciondetalle.*, stockmaster.description,
								(
									SELECT
										concat_WS('|',suppliers.supplierid,suppliers.suppname,wrk_precioproveedor.price)
									FROM
										wrk_precioproveedor
									INNER JOIN suppliers ON suppliers.supplierid = wrk_precioproveedor.supplierid
									WHERE
										wrk_precioproveedor.stockid = wrk_requisiciondetalle.itemcode
									ORDER BY
										price,suppliers.suppname ASC
									LIMIT 1
								)  as proveedorbarato
							FROM
								wrk_requisiciondetalle
							LEFT JOIN stockmaster ON stockmaster.stockid = wrk_requisiciondetalle.itemcode
						WHERE reqno='".$reqid . "'";
					$lineresult=DB_query($linesql, $db);
					while ($linerow=DB_fetch_array($lineresult)) {
						// descomponemos el arreglo
						/*
						* Array ( [0] => P0246 [1] => Abasto Grafico, S.A de C.V. [2] => 50.0000 )
						*/
						$datosproveedor = explode("|", $linerow['proveedorbarato']);
						if($datosproveedor['1']==''){$datosproveedor['1']='Sin proveedor';}

						echo '<tr data-proveedorid="'.$datosproveedor['0'].'">';
						echo '<td>'.$linerow['itemcode'].'</td>';
						echo '<td>'.$linerow['description'].'</td>';
						echo '<td class="number" style="text-align: right;" >'.number_format($linerow['quantityreq'],2).'</td>';
						echo '<td class="number" style="text-align: right;" >'.number_format($linerow['quantityord'],2).'</td>';
						if($linerow['completed']==1)
						{
							echo '<td style="text-align:center;">Completado</td>';
						}else{
							echo '<td style="text-align:center;">Pendiente</td>';	
						}
						
						echo '<td style="text-align:center;">'.$datosproveedor['1'].'</td>';
						echo '<td style="text-align:center;">$ '.$datosproveedor['2'].'</td>';

						echo '</tr>';
						
					} // end while order line detail
					echo "</tbody>
					</table>
				</td>
				<td width=35% valign=top cellpadding='20px'>";
					/*print_r($datosreq);*/
					if(
						$datosreq['status']!='Complete' && $datosreq['autorizadorbase']==$_SESSION['UserID'] ||
						$datosreq['status']!='Complete' && $rowacceso['or_compras']==1
						){
						echo "
						<form id='cancelarreq' method='POST' action='REQ_Details.php?reqid=".$reqid."'>
							<h2>Cancelar Requisicion</h2>
							Seleccione Motivo de Rechazo: <br>";

							if($datosreq['status']=='Cancel'){
								echo "<select name='motivorechazoid' id='motivorechazoid' onchange='' class='' disabled>";
							}else{
								echo "<select name='motivorechazoid' id='motivorechazoid' onchange='' class=''>";
							}
							echo "
							<option value=''>-- Seleccione --</option>
							";
							// hacemos una consulta para obtener todo  de la tabla de centro de costo
							$sql_motivorechazo = 'SELECT * FROM wrk_motivosrequis
									where motivoreq_edo = 1';
							$result_motivorechazo = DB_query($sql_motivorechazo,$db);
							while ($rowmotivorechazo = DB_fetch_array($result_motivorechazo)) {
								// hacemos los options
								if($datosreq['motivo_rechazo']==$rowmotivorechazo['motivoreq_id']){
									echo '<option value="'.$rowmotivorechazo['motivoreq_id'].'" selected>'.$rowmotivorechazo['motivoreq'].'</option>';
								}else{
									echo '<option value="'.$rowmotivorechazo['motivoreq_id'].'" >'.$rowmotivorechazo['motivoreq'].'</option>';
								}
								
							}
							// termina
							echo "
						</select><br>
						Comentarios de cancelacion<br>";
						if($datosreq['status']=='Cancel'){
							echo "<textarea name='motivorechazotext' id='motivorechazotext' rows=5 style='width:400px;' disabled>".$datosreq['stat_comment']."</textarea><br>";
						}else{
							echo "<textarea name='motivorechazotext' id='motivorechazotext' rows=5 style='width:400px;'></textarea><br>";
							echo "<input type='submit' name='btncancelarreq' value='Cancelar Requisicion'>";
						}
					
						echo "
						</form><hr>";
					}
						if($datosreq['status']=='Authorised' && $rowacceso['or_compras']==1){
							echo "<h2>Generar O.C. de Requisicion</h2>";
							echo "
							<form id='creareq' method='POST' action='REQ_Details.php?reqid=".$reqid."'>
								<select name='supplierid' id='supplierid' onchange='Filtrartabla()'>
								<option value='-9'>-- Seleccione Proveedor --</option>
							";

								// hacemos una consulta para obtener todo  de la tabla de centro de costo
								$sql = 'select distinct 
									pp.supplierid,
									sup.suppname
									from wrk_requisiciondetalle rd
										join wrk_precioproveedor pp ON rd.itemcode=pp.stockid
										join suppliers sup ON sup.supplierid=pp.supplierid
									where rd.reqno = "'.$reqid.'"';
								$result = DB_query($sql,$db);
								while ($rows = DB_fetch_array($result)) {
									// hacemos los options
									echo '<option value="'.$rows['supplierid'].'" >'.$rows['suppname'].'</option>';
									
								}
							echo "</select><br>
								<input type='hidden' name='reqid' value='".$datosreq['reqid']."'>
								<label>Tipo de Orden de Compra</label>
								<select name='tipodegeneracion' id='tipodegeneracion' onchange='Filtrartabla()'>
									<option value='-9'>-- Seleccione --</option>
									<option value='1'>Proveedor mas barato</option>
									<option value='2'>Por Proveedor</option>
								</select><br>
								<input type='submit' name='btncreatereq' value='Generar O.C.'>
								
								</form>
							";
							//<a href='pdfprueb.php?reqid='".$datosreq['reqid']."'>Generar PDF</a>
						}
					echo "
				</td>
			</tr>
		</table>
	</center>		
	";
include('includes/footer.inc');
?>
