<script type="text/javascript">
$(document).on('ready', function() {
	$('#SimulacionPrecios').dataTable( {
		"sPaginationType": "bootstrap",
		"sDom": 'T<"clear">lfrtip',
		"oTableTools": {
			"aButtons": [{
				"sExtends": "collection",
				"sButtonText": "Exportar",
				"aButtons": [ "print", "csv", "xls", {
				"sExtends": "pdf",
				"sPdfOrientation": "landscape",
				"sTitle": "Simulación y Aplicación de Aumentos de Precio - <?=date('Y-m-d') ?> ",
				}, ]
			}]
		},
		"aLengthMenu": [
			[10,25, 50, 100, 200, -1],
			[10,25, 50, 100, 200, "Todos"]
		],
		"fnInitComplete": function(){
			$(".dataTables_wrapper select").select2({
				dropdownCssClass: 'noSearch'
			});
		}
	});
	$(".Select2").select2();
	$(".Date2").datepicker({
		dateFormat : 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+5'
	});
});
</script>
<div class="container-fluid">
	<form method="POST" action="<?php echo $this->createUrl("reportes/simulacionprecios"); ?>">
		<div class="row">
			<div class="form-legend"><h3>SIMULACIÓN DE AUMENTOS DE PRECIO</h3></div>
			<br>
			<div class="span2">
				<label class="control-label" >Folio:</label>
				<div class="controls">
				<input type="text" id="Folio" name="Folio" value="<?=$_POST['Folio']?>" style="width:100px;">
				</div>
			</div>
			<div class="span3">
				<label>Fecha de Ingreso:</label>
				<input type="text" id="INICIO" name="INICIO" placeholder="Inicio" class="Date2" value="<?=$_POST['INICIO']?>" style="width:100px;" />
				 <input type="text" id="FIN" name="FIN" placeholder="Fin" class="Date2" value="<?=$_POST['FIN']?>" style="width:100px;" />
				
			</div>
			<div class="span2">
				<label>&nbsp;</label>
				<input type="submit" class="btn btn-success" name="BUSCAR" value="BUSCAR" >
			</div>
			<div align="right">
				<label>&nbsp;</label>
				<a href="<?php echo $this->createUrl("simulaciones/index"); ?>">
					<input type="button" value="IR SIMULACIÓN Y APLICACIÓN DE AUMENTOS DE PRECIO" class="btn btn-info">
				</a>
				<label>&nbsp;</label>
				<a href="<?php echo $this->createUrl("reportes/simulacionpreciosaplicada"); ?>">
					<input type="button"
				value="IR A REPORTE DE AUMENTOS DE TARIFA APLICADOS" class="btn btn-info">
				</a>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="span3">
				<label>FormaPago:</label>
				<select class="Select2" id="FORMA_PAGO" name="FORMA_PAGO" style="width:100px;">
					<option value="">SELECCIONE</option>
					<?php foreach ($ListaFormasPago as $key => $value) {
						echo "<option value='{$key}'>{$value}</option>";
					} ?>
				</select>
				<script type="text/javascript">
				$("#FORMA_PAGO option[value='<?=$_POST['FORMA_PAGO']?>']").attr("selected",true);
				</script>
			</div>
			<div class="span3">
				<label>FrecuenciaPago:</label>
				<select class="Select2" id="FRECUENCIA_PAGO" name="FRECUENCIA_PAGO"
				style="width:100px;" >
				<option value="">SELECCIONE</option>
				<?php foreach ($ListaFrecuenciaPago as $key => $value) {
					echo "<option value='{$key}'>{$value}</option>";
				} ?>
				</select>
				<script type="text/javascript">
				$("#FRECUENCIA_PAGO option[value='<?=$_POST['FRECUENCIA_PAGO']?>']").attr("selected",true);
				</script>
			</div>
			<div class="span3">
				<label>Plan:</label>
				<select class="Select2" id="PLAN" name="PLAN" style="width:100px;" >
					<option value="">SELECCIONE</option>
					<?php foreach ($ListaPlanes as $key => $value) {
						echo "<option value='{$key}'>{$value}</option>";
					} ?>
				</select>
				<script type="text/javascript">
				$("#PLAN option[value='<?=$_POST['PLAN']?>']").attr("selected",true);
				</script>
			</div>
		</div>
		<div class="row">
			<div class="span12">
				<br>
				<div class="form-legend">
					<h3>NOTA: Muestra a los Socios Activos que tengan mas de 1 Año sin aumentarles la Tarifa</h3>
				</div>
				<table id="SimulacionPrecios" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
						<th>Folio</th>
						<th>Nombre</th>
						<th>FechaIngreso</th>
						<th>Estatus</th>
						<th>#Socios</th>
						<th>Empresa</th>
						<th>Plan</th>
						<th>FormaPago</th>
						<th>FrecuenciaPago</th>
						<th>ServiciosMes</th>
						<th>ServiciosAcumulados</th>
						<th>FechaUltimoAumento</th>
						<th>TarifaActual</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($Simulacionprecios as $Data) {
						?>
						<tr>
						<td><?=$Data['Folio']?></td>
						<td><?=$Data['NOMBRE']?></td>
						<td><?=$Data['FECHA_INSCIPCION']?></td>
						<td><?=$Data['ESTATUS_TITULAR']?></td>
						<td><?=$Data['NumSocios']?></td>
						<td><?=$Data['Empresa']?></td>
						<td><?=$Data['PLAN']?></td>
						<td><?=$Data['FORMA_PAGO']?></td>
						<td><?=$Data['FRECUENCIA_PAGO']?></td>
						<td><?=$Data['ServiciosMes']?></td>
						<td><?=$Data['ServiciosAcum']?></td>
						<td><?=$Data['FECHA_ULTIMO_AUMENTO']?></td>
						<td style="text-align:right;"><?=number_format($Data['CostoAfiliacion'],2, '.', '')?></td>
						</tr>
						<?php }
						?>
					</tbody>
				</table>
			</div>
		</div>
	</form>
</div>
