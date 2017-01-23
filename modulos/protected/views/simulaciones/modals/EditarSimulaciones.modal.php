<script type="text/javascript">
$(document).on('ready', function() {
	$('#E-fecha_aumento_tarifa').datepicker({
		dateFormat : 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+5'
	});
	$('#Create-Modal-EditarSimulaciones').click(function(){
		var jqxhr = $.ajax({
		url: "<?php echo $this->createUrl("simulaciones/Update"); ?>",
		type: "POST",
		dataType : "json",
		timeout : (120 * 1000),
		data: {
			Update:{
				id: $("#E-ID").val(),
				folio: $('#E-folio').val(),
				prc_aumento_tarifa: $('#E-prc_aumento_tarifa').val(),
				fecha_aumento_tarifa: $('#E-fecha_aumento_tarifa').val(),
				nueva_tarifa: $('#E-nueva_tarifa').val(),
				usuario: $('#E-usuario').val(),
			},
		},
		success : function(data, newValue) {
			if (data.requestresult == 'ok') {
				displayNotify('success', data.message);
			$('#ListSimulaciones #' + data.id).html(data.NewRow);
				location.reload();
			}else{
				displayNotify('alert', data.message);
			}
		},
		error : ajaxError
		});
	});
});

function EditarSimulaciones(id){
	$('#ModalLabelEdit').text('EDITAR SIMULACIÓN DE AUMENTOS DE PRECIO');
	$('#Modal_EditarSimulaciones').modal('show');
	$("#E-ID").val(id);
	LoadForm(id);
}
function LoadForm(id){
	var jqxhr = $.ajax({
	url: "<?php echo $this->createUrl("simulaciones/LoadForm"); ?>",
	type: "POST",
	dataType : "json",
	timeout : (120 * 1000),
	data: {
		GetData:{
			id: id
		},
	},success : function(data, newValue) {
		if (data.requestresult == 'ok') {
			displayNotify('success', data.message);
			$('#E-ID').val(data.GetData.id);
			$('#E-folio').val(data.GetData.folio);
			$('#E-prc_aumento_tarifa').val(data.GetData.prc_aumento_tarifa);
			$('#E-fecha_aumento_tarifa').val(data.GetData.fecha_aumento_tarifa);
			$('#E-nueva_tarifa').val(data.GetData.nueva_tarifa);
			$('#E-usuario').val(data.GetData.usuario);
		}else{
			displayNotify('alert', data.message);
		}
	},
	error : ajaxError
	});
}
</script>

<div id="Modal_EditarSimulaciones" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;" >
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="ModalLabelEdit"> </h3>
	</div>
	<div class="modal-body">
		<p>
		<div class="control-group row-fluid">
			<input type="hidden" id="E-ID" name="E-ID" class="span12" />
			<div class="span2">
				<label class="control-label"><?php echo _('Folio:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input type="text" id="E-folio" name="E-folio" class="span12" readonly="readonly" />
				</div>
			</div>
		</div>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('%DeAumento:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input type="text" id="E-prc_aumento_tarifa" name="E-prc_aumento_tarifa" class="span12" />
				</div>
			</div>
		</div>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('FechaAumento:'); ?></label>
			</div>
			<div class="span4">
				<div class="controls">
					<input type="text" id="E-fecha_aumento_tarifa" name="E-fecha_aumento_tarifa" class="span12" />
				</div>
			</div>
		</div>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('TarifaNueva:'); ?></label>
			</div>
			<div class="span3">
				<div class="controls">
					<input type="text" id="E-nueva_tarifa" name="E-nueva_tarifa" class="span12" readonly="readonly" />
				</div>
			</div>
		</div>
		<div class="control-group row-fluid">
			<div class="span2">
				<label class="control-label"><?php echo _('Usuario:'); ?></label>
			</div>
			<div class="span3">
				<div class="controls">
					<input type="text" id="E-usuario" name="E-usuario" value="<?php echo $_SESSION['UserID'];?>" readonly="readonly" />
				</div>
			</div>
		</div>
		</p>
	</div>
	<div class="modal-footer">
		<button id="Close-Modal-EditarSimulaciones" class="btn" data-dismiss="modal" aria-
		hidden="true">Cancelar</button>
		<button id="Create-Modal-EditarSimulaciones" class="btn btn-success" data-dismiss="modal" aria-hidden="true" >Aceptar</button>
	</div>
</div>