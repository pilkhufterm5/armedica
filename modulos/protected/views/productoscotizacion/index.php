<script type="text/javascript">
$(document).on('ready',function() {
	$("#stockid").select2();

	$('#ListProductoscotizacion').dataTable( {
		"sPaginationType": "bootstrap",
		"sDom": 'T<"clear">lfrtip',
		"oTableTools": {
			"aButtons": [{
				"sExtends": "collection",
				"sButtonText": "Exportar",
				"aButtons": [ "print", "csv", "xls", "pdf" ]
			}]
		},
		"fnInitComplete": function(){
			$(".dataTables_wrapper select").select2({
				dropdownCssClass: 'noSearch'
			});
		},
		"aLengthMenu": [
			[10,25, 50, 100, -1],
			[10,25, 50, 100, "Todo"]
		],
	});
});
</script>
<div style="height: 20px;"></div>
<div style="width: 80%; margin-left:10%;">
<div class="container-fluid">
	<div class="control-group row-fluid">
		<div class="form-legend"><h3>Productos Cotizacion para el Cotizador Maestro</h3></div>
		<ul class="nav nav-tabs" id="myTab">
		<li class="active"><a data-toggle="tab" href="#Lista">Productos Cotización</a></li>
		</ul>
		<div class="tab-content"> <!-- Start TABS -->
			<div id="Lista" class="tab-pane fade active in">
				<?php include_once('modals/EditarProductoscotizacion.modal.php'); ?>
				<table id="ListProductoscotizacion" class="table table-hover table-condensed table-striped">
					<thead>
						<tr>
						<th><?php echo _('ID') ?></th>
						<th><?php echo _('PRODUCTO') ?></th>
						<th><?php echo _('COSTO INSCRIPCION') ?></th>
						<th><?php echo _('COSTO TOTAL') ?></th>
						<th><?php echo _('STATUS') ?></th>
						<th><?php echo _('Acciones') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($ProductoscotizacionData as $Productoscotizacion){ ?>
							<tr id='<?=$Productoscotizacion['id']?>' >
							<td><?=$Productoscotizacion['id']?></td>
							<td><?=$Productoscotizacion['stockid']?></td>
							<td><?=$Productoscotizacion['costo_inscripcion']?></td>
							<td><?=$Productoscotizacion['costo_total']?></td>
							<td><?php if($Productoscotizacion['status']==1) echo "Activo"; else echo "Inactivo"; ?></td>
							<td><td>
							<a onclick="EditarProductoscotizacion('<?=$Productoscotizacion['id']?>');" title="Editar Producto
							Cotización" ><i class="icon-edit"></i></a>
							<?php echo CHtml::link("<i class=\"icon-
							trash\"></i>",array("disable","id"=>$Productoscotizacion['id']),array('onclick'=>"javascript:if(confirm('¿E
							sta seguro de eliminar este producto?')) { return; }else{return false;};", "title"=>"Eliminar Producto
							Cotización")); ?>
							</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<div id="Create" class="tab-pane fade active">
				<form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("Productoscotizacion/create/"); ?>" >
					<div class="control-group row-fluid">
						<div class="span2">
							<label class="control-label"><?php echo _('PRODUCTO:'); ?></label>
						</div>
						<div class="controls">
							<select id="stockid" name="stockid" >
								<option value="">SELECCIONE</option>
								<?php foreach ($ProductoData as $Producto){ ?>
								<option value="<?=$Producto['stockid']?>"><?=$Producto['description'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="control-group row-fluid">
						<div class="span2">
							<label class="control-label"><?php echo _('COSTO INSCRIPCION'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<input type="text" id="costo_inscripcion" name="costo_inscripcion" />
							</div>
						</div>
						<div class="span2">
							<label class="control-label"><?php echo _('COSTO TOTAL'); ?></label>
						</div>
						<div class="controls">
							<input type="text" id="costo_total" name="costo_total" />
						</div>
					</div>
					<div class="control-group row-fluid">
						<div class="span2">
							<label class="control-label"><?php echo _('STATUS'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<input value="1" type="checkbox" id="status" name="status" checked="checked"/>
							</div>
						</div>
					</div>
					<div class="control-group row-fluid">
						<div class="span12">
							<input type="submit" id="Save" class="btn btn-large btn-success" value="Agregar" />
							<div style="height: 20px;"></div>
						</div>
					</div>
				</form>
			</div>
		</div> <!-- End Tabs -->
	</div>
</div>