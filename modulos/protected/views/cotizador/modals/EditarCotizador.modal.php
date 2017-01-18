<script type="text/javascript">
$(document).on('ready', function() {
	$('#Create-Modal-EditarCotizador').click(function(){
		
		$('#E-fecha_cotizacion').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			yearRange: '-100:+5'
		});

		var jqxhr = $.ajax({
		url: "<?php echo $this->createUrl("cotizador/Update"); ?>",
		type: "POST",
		dataType : "json",
		timeout : (120 * 1000),
		data: {
			Update:{
				id: $("#E-ID").val(),
				folio_socio: $('#E-folio_socio').val(),
				nombre: $('#E-nombre').val(),
				fecha_cotizacion: $('#E-fecha_cotizacion').val(),
				calle: $('#E-calle').val(),
				numero: $('#E-numero').val(),
				colonia: $('#E-colonia').val(),
				telefono: $('#E-telefono').val(),
				entrecalles: $('#E-entrecalles').val(),
				municipio: $('#E-municipio').val(),
				sucursal: $('#E-sucursal').val(),
				cuadrantes: $('#E-cuadrantes').val(),
				codigo_postal: $("#E-codigo_postal").val(),
				stockid: $('#E-stockid').val(),
				paymentid: $('#E-paymentid').val(),
				frecuencia_pago: $('#E-frecuencia_pago').val(),
				empresa: $('#E-empresa').val(),
				CNT_socios: $('#E-CNT_socios').val(),
				comentarios: $('#E-comentarios').val(),
				costo_inscripcion: $('#E-costo_inscripcion').val(),
				costo_total: $('#E-costo_total').val(),
				costo_inscripcion_libre: $('#E-costo_inscripcion_libre').val(),
				costo_total_libre: $('#E-costo_total_libre').val(),
				tipo: $('#E-tipo').val(),usuario: $('#E-usuario').val(),
				status: $('#E-status').attr("checked") ? 1 : 0,
			},
		},
		success : function(data, newValue) {
			if (data.requestresult == 'ok') {
				displayNotify('success', data.message);
			$('#ListCotizador #' + data.id).html(data.NewRow);
				location.reload();
			}else{
				displayNotify('alert', data.message);
			}
		},
		error : ajaxError
		});
	});
});
function EditarCotizador(id){
	$('#ModalLabelEdit').text('Editar Cotizacion');
	$('#Modal_EditarCotizador').modal('show');
	$("#E-ID").val(id);
	LoadForm(id);
}
function LoadForm(id){
	var jqxhr = $.ajax({
	url: "<?php echo $this->createUrl("cotizador/LoadForm"); ?>",
	type: "POST",
	dataType : "json",
	timeout : (120 * 1000),
	data: {
		GetData:{id: id},
	},
	success : function(data, newValue) {
		if (data.requestresult == 'ok') {
		displayNotify('success', data.message);
		$('#E-ID').val(data.GetData.id);
		$('#E-folio_socio').val(data.GetData.folio_socio);
		$('#E-nombre').val(data.GetData.nombre);
		$('#E-fecha_cotizacion').val(data.GetData.fecha_cotizacion);
		$('#E-calle').val(data.GetData.calle);
		$('#E-numero').val(data.GetData.numero);
		$('#E-colonia').val(data.GetData.colonia);
		$('#E-telefono').val(data.GetData.telefono);
		$('#E-entrecalles').val(data.GetData.entrecalles);
		$('#E-municipio').val(data.GetData.id_municipio);
		$('#E-sucursal').val(data.GetData.sucursal);
		$('#E-cuadrantes').val(data.GetData.cuadrantes);
		$('#E-codigo_postal').val(data.GetData.codigo_postal);
		$('#E-stockid').val(data.GetData.stockid);
		$('#E-paymentid').val(data.GetData.paymentid);
		$('#E-frecuencia_pago').val(data.GetData.frecuencia_pago);
		$('#E-empresa').val(data.GetData.empresa);
		$('#E-CNT_socios').val(data.GetData.CNT_socios);
		$('#E-comentarios').val(data.GetData.comentarios);
		$('#E-costo_inscripcion').val(data.GetData.costo_inscripcion);
		$('#E-costo_total').val(data.GetData.costo_total);
		$('#E-costo_inscripcion_libre').val(data.GetData.costo_inscripcion_libre);
		$('#E-costo_total_libre').val(data.GetData.costo_total_libre);
		$('#E-tipo').val(data.GetData.tipo);$('#E-usuario').val(data.GetData.usuario);
		
			if(data.GetData.status==1){
				$('#E-status').attr('checked','checked');
			}else{
				$('#E-status').attr('checked',false);
			}
		}else{
			displayNotify('alert', data.message);
		}
	},
	error : ajaxError
	});
}
</script>
<div id="Modal_EditarCotizador" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;" >
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="ModalLabelEdit"> </h3>
	</div>
	<div class="modal-body">
		<p>
		<div class="control-group row-fluid">
			<input type="hidden" id="E-ID" name="E-ID" class="span12" />
			<div class="span2">
				<label class="control-label"><?php echo _('Folio Socio:'); ?></label>
			</div>
			<div class="span3">
				<div class="controls">
					<input type="text" id="E-folio_socio" name="E-folio_socio" class="span12" />
				</div>
			</div>
			<div class="span2">
				<label class="control-label"><?php echo _('Nombre:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input type="text" id="E-nombre" name="E-nombre" class="span12" />
				</div>
			</div>
			<div class="span2">
				<label class="control-label"><?php echo _('Fecha:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input type="text" id="E-fecha_cotizacion" name="E-fecha_cotizacion" class="span12" />
				</div>
			</div>
		</div>
		<div class="span2">
			<label class="control-label"><?php echo _('Calle:'); ?></label>
		</div>
		<div class="span4">
			<div class="controls">
				<input type="text" id="E-calle" name="E-calle" class="span12" />
			</div>
		</div>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Número:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input type="text" id="E-numero" name="E-numero" class="span12"/>
				</div>
			</div>
			<div class="span2">
				<label class="control-label"><?php echo _('Colonia:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input type="text" id="E-colonia" name="E-colonia" class="span12"/>
				</div>
			</div>
		</div>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Teléfono:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input type="text" id="E-telefono" name="E-telefono" class="span12"/>
				</div>
			</div>
			<div class="span2">
				<label class="control-label"><?php echo _('Entrecalles:'); ?></label>
			</div>
			<br>
			<div class="span3">
				<div class="controls">
					<textarea id="E-entrecalles" name="E-entrecalles" class="form-control" rows="3"></textarea>
				</div>
			</div>
		</div>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Municipio:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
				<select id="E-municipio" name="E-municipio" class="span12">
					<option value="0">Seleccione una opcion</option>
					<?php foreach ($MunicipiosData as $Municipio){ ?>
					<option value="<?=$Municipio['id']?>"><?=$Municipio['municipio']?></option>
					<?php } ?>
				</select>
				</div>
			</div>
			<div class="span2">
				<label class="control-label"><?php echo _('Sucursal'); ?></label>
			</div>
			<div class="span3">
				<div class="controls">
					<select id="E-sucursal" name="E-sucursal" class="span12">
					<option value="MTY">Seleccione una opcion</option>
					<option value="MTY">MONTERREY</option>
					<option value="CHH">CHIHUAHUA</option>
					<option value="QRO">QUERETARO</option>
					<option value="AGS">AGUASCALIENTES</option>
					<option value="TAM">TAMPICO</option>
					<option value="TRN">TORREON</option>
					</select>
				</div>
			</div>
		</div>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Cuadrantes:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input type="text" id="E-cuadrantes" name="E-cuadrantes" class="span12"/>
				</div>
			</div>
			<div class="span2">
				<label class="control-label"><?php echo _('Codigo Postal:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input type="text" id="E-codigo_postal" name="E-codigo_postal" class="span12"/>
				</div>
			</div>
		</div>
		<br>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Producto:'); ?></label>
			</div>
			<div class="span3">
				<div class="controls">
					<select id="E-stockid" name="E-stockid" >
						<option value="0">Seleccione una opcion</option>
						<?php foreach ($ProductoData as $Producto){ ?>
						<option value="<?=$Producto['stockid']?>"><?=$Producto['description']?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Forma Pago:'); ?></label>
			</div>
			<div class="span3">
				<div class="controls">
					<select id="E-paymentid" name="E-paymentid" >
						<option value="0">Seleccione una opcion</option>
						<?php foreach ($FormapagoData as $Formapago){ ?>
						<option value="<?=$Formapago['paymentid']?>"><?=$Formapago['paymentname']?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<br>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Frecuencia Pago:'); ?></label>
			</div>
			<div class="span3">
				<div class="controls">
					<select id="E-frecuencia_pago" name="E-frecuencia_pago" >
						<option value="0">Seleccione una opcion</option>
						<?php foreach ($FrecuenciapagoData as $Frecuenciapago){ ?>
						<option value="<?=$Frecuenciapago['id']?>"><?=$Frecuenciapago['frecuencia']?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<br>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Empresa:'); ?></label>
			</div>
			<div class="span3">
				<div class="controls">
					<select id="E-empresa" name="E-empresa" >
						<option value="0">Seleccione una opcion</option>
						<?php foreach ($EmpresasData as $Empresas){ ?>
						<option value="<?=$Empresas['id']?>"><?=$Empresas['empresa']?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Socios:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input type="text" id="E-CNT_socios" name="E-CNT_socios" class="span12"/>
				</div>
			</div>
		</div>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Comentarios/Observaciones:'); ?></label>
			</div>
			<br>
			<div class="span3">
				<div class="controls">
					<textarea id="E-comentarios" name="E-comentarios" class="form-control" style="width:400px; height:100px;"></textarea>
				</div>
			</div>
		</div>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Costo Inscripcion Libre:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input type="text" id="E-costo_inscripcion_libre" name="E-costo_inscripcion_libre" class="span12"/>
				</div>
			</div>
			<div class="span2">
				<label class="control-label"><?php echo _('Costo Total Libre:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input type="text" id="E-costo_total_libre" name="E-costo_total_libre" class="span12"/>
				</div>
			</div>
		</div>
		<br>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Tipo Cotización'); ?></label>
			</div>
			<div class="span3">
				<div class="controls">
					<select id="E-tipo" name="E-tipo" required>
						<option value="">SELECCIONE</option>
						<option value="Afiliacion">AFILIACION</option>
						<option value="Enfermeria">ENFERMERIA</option>
					</select>
				</div>
			</div>
		</div>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Usuario:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input type="text" id="E-usuario" name="E-usuario" class="span12" readonly="readonly"/>
				</div>
			</div>
		</div>
		<br>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Estatus Activo:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input value="1" type="checkbox" id="E-status" name="E-status" checked="checked"/>
				</div>
			</div>
		</div>
		</p>
	</div>
	<div class="modal-footer">
		<button id="Close-Modal-EditarCotizador" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
		<button id="Create-Modal-EditarCotizador" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
	</div>
</div>