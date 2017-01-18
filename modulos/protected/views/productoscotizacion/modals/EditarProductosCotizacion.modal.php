<script type="text/javascript">
$(document).on('ready', function() {
	$('#Create-Modal-EditarProductoscotizacion').click(function(){
		var jqxhr = $.ajax({
			url: "<?php echo $this->createUrl("productoscotizacion/Update"); ?>",
			type: "POST",
			dataType : "json",
			timeout : (120 * 1000),
			data: {
				Update:{
					id: $("#E-ID").val(),
					stockid: $('#E-stockid').val(),
					costo_inscripcion: $('#E-costo_inscripcion').val(),
					costo_total: $('#E-costo_total').val(),
					status: $('#E-status').attr("checked") ? 1 : 0,
				},
			},
			success : function(data, newValue) {
				if (data.requestresult == 'ok') {
					displayNotify('success', data.message);
					$('#ListProductoscotizacion #' + data.id).html(data.NewRow);
					location.reload();
				}else{
					displayNotify('alert', data.message);
				}
			},
			error : ajaxError
		});
	});
});
function EditarProductoscotizacion(id){
	$('#ModalLabelEdit').text('Editar Productos Cotización');
	$('#Modal_EditarProductoscotizacion').modal('show');
	$("#E-ID").val(id);
	LoadForm(id);
}
function LoadForm(id){
	var jqxhr = $.ajax({
	url: "<?php echo $this->createUrl("productoscotizacion/LoadForm"); ?>",
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
			$('#E-stockid').val(data.GetData.stockid);
			$('#E-costo_inscripcion').val(data.GetData.costo_inscripcion);
			$('#E-costo_total').val(data.GetData.costo_total);

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
<div id="Modal_EditarProductoscotizacion" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;" >
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="ModalLabelEdit"> </h3>
	</div>
	<div class="modal-body">
		<p>
		<table class="table">
			<tbody>
				<tr><input type="hidden" value="" id="E-ID" >
				<td><label class="control-label" style="margin-top: 10px;">COSTO INSCRIPCION: </label></td>
				<td><input type="text" id="E-costo_inscripcion" class="span12" /></td>
				<td><label class="control-label" style="margin-top: 10px;">COSTO TOTAL: </label></td>
				<td><input type="text" id="E-costo_total" class="span12" /></td>
				</tr>
				<tr>
				<td><label class="control-label" style="margin-top: 10px;">STATUS: </label></td>
				<td>
				<input type="checkbox" id="E-status" name="E-status"/>
				</td>
				<td></td>
				<td></td>
				</tr>
			</tbody>
		</table>
		</p>
	</div>
	<div class="modal-footer">
		<button id="Close-Modal-EditarProductoscotizacion" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
		<button id="Create-Modal-EditarProductoscotizacion" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
	</div>
</div>