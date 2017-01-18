<script type="text/javascript">
	$(document).on('ready', function() {
		$('#SimulacionPreciosAplicada').dataTable( {
			"sPaginationType": "bootstrap",
			"sDom": 'T<"clear">lfrtip',
			"oTableTools": {
				"aButtons": [{
					"sExtends": "collection",
					"sButtonText": "Exportar",
					"aButtons": [ "print", "csv", "xls", {
					"sExtends": "pdf",
					"sPdfOrientation": "landscape",
					"sTitle": "Simulación y Aplicación de Aumentos de Precio Aplicados - <?=date('Y-m-d') ?> ",}, ]
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
		$("#CheckAll").click(function(event) {
			if(this.checked){
				$('.EnviarCarta').attr('checked','checked')
			}else{
				$('.EnviarCarta').removeAttr('checked');
			}
		});

		$("#CartaAumentoPrecio").click(function(event) {
			var jqxhr = $.ajax({
				url: "<?php echo $this->createUrl("reportes/SendMail"); ?>",
				type: "POST",
				dataType : "json",
				timeout : (120 * 1000),
				data: {
					SendMail:{
						Tipo: 'CartaAumentoPrecio',
						Folio: $('.EnviarCarta').serialize()
					},
				},
				success : function(data, newValue) {
					if (data.requestresult == 'ok') {
						displayNotify('success', data.message);
					}else{
						displayNotify('fail', data.message);
					}
				},
				error : ajaxError
			});
		});
	});
</script>

<div class="container-fluid">
	<form method="POST" action="<?php echo $this->createUrl("reportes/simulacionpreciosaplicada");?>">
	<div class="row">
		<div class="form-legend"><h3>REPORTE DE AUMENTOS DE TARIFA APLICADOS</h3></div><br>
		<div class="span2">
			<label class="control-label" >Folio:</label>
			<div class="controls"><input type="text" id="Folio" name="Folio" value="<?=$_POST['Folio']?>" style="width:100px;">
			</div>
		</div>
		<div class="span3">
			<label>Fecha Ultimo Aumento Aplicada:</label>
			<input type="text" id="INICIO" name="INICIO" placeholder="Inicio"
			value="<?=$_POST['INICIO']?>" style="width:100px;" />
			<input type="text" id="FIN" name="FIN"
			placeholder="Fin"
			value="<?=$_POST['FIN']?>" style="width:100px;" />
			<!-- class="Date2"
			class="Date2" -->
		</div>
		<div class="span2">
			<label>&nbsp;</label>
			<input type="submit" class="btn btn-success" name="BUSCAR" value="BUSCAR" >
		</div>
		<div align="right">
			<label>&nbsp;</label>
			<a href="<?php echo $this->createUrl("simulaciones/index"); ?>"><input type="button"
			value="REGRESAR A SIMULACION Y APLICACION DE AUMENTOS DE PRECIO" class="btn btn-
			info"></a>
		</div>
	</div><br>
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
			$("#FRECUENCIA_PAGO
			option[value='<?=$_POST['FRECUENCIA_PAGO']?>']").attr("selected",true);
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
		<div class="span2">
		<label>&nbsp;</label>
		<input type="button" id="CartaAumentoPrecio" value="Carta Aviso De Aumento De Precio" class="btn btn-danger" onclick="window.location.reload()" >
		</div>
	</div>
	<div class="row">
		<div class="span12"><br>
			<div class="form-legend"><h3>NOTA: Muestra a los Socios que ya se les Aplicó el Aumento en su Tarifa</h3></div>
			<table id="SimulacionPreciosAplicada" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
					<th>Folio</th>
					<th>Nombre</th>
					<th>FechaIngreso</th>
					<th>Estatus</th>
					<th>FechaUltimoAumentoAnterior</th>
					<th>TarifaAnterior</th>
					<th>Plan</th>
					<th>FormaPago</th>
					<th>FrecuenciaPago</th>
					<th>%DeAumentoAplicado</th>
					<th>FechaUltimoAumentoAplicada</th>
					<th>TarifaAplicada</th>
					<th>Usuario</th>
					<th title="Enviar Carta">EnviarCarta<input type='checkbox' id='CheckAll'></th>
					<th>CartaEnviada</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($Simulacionpreciosaplicada as $Data) {
					?>
					<tr>
					<td><?=$Data['Folio']?></td>
					<td><?=$Data['Nombre']?></td>
					<td><?=$Data['fecha_ingreso']?></td><td><?=$Data['movimientos_afiliacion']?></td>
					<td><?=$Data['fecha_ultimo_aumento']?></td>
					<td style="text-align:right;"><?=number_format($Data['costo_actual'],2, '.', '')?></td>
					<td><?=$Data['stockid']?></td>
					<td><?=$Data['paymentid']?></td>
					<td><?=$Data['frecuencia_pago']?></td>
					<td><?=$Data['prc_aumento_tarifa']?></td>
					<td><?=$Data['fecha_aumento_tarifa']?></td>
					<td style="text-align:right;"><?=number_format($Data['nueva_tarifa'],2, '.', '')?></td>
					<td><?=$Data['usuario']?></td>
					<td style="text-align:center;">
					<input type="checkbox" id="EnviarCarta<?=$Data['id']?>" name="EnviarCarta[]"
					value="<?=$Data['Folio']?>" title="Enviar Carta Si/NO" class="success EnviarCarta" />
					</td>
					<td><?php if($Data['enviar_carta']==1) echo "SI"; else echo "NO"; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</form>
</div>