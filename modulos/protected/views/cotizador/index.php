<script type="text/javascript">
$(document).on('ready',function() {
	$("#municipio").select2();
	$("#sucursal").select2();
	$("#empresa").select2();

	$(window).on('ready',function() {
		$(".SearchbyName").autocomplete({
			source: function( request, response ) {
				$.ajax({
				url: "<?php echo $this->createUrl("cotizador/Searchfolio"); ?>",
				type: "POST",
				dataType: "json",
				data: {
					Search: {
						string: request.term,
					},
				}
				}).complete( function( data ) {
					Response={};
					if(data.readyState==4){
						if (typeof(data.responseText)!='undefined'){
							Response=eval('('+data.responseText+')');
						}
						if (typeof(Response.requestresult)!='undefined'&&Response.requestresult == 'ok') {
							response(Response.DataList.slice(0, 10));
						

						}else{

						}
					}
				});
			},
			select: function( event, ui ) {
				
							console.log(ui.item.socio);
				$("#folio_socio_").val(ui.item.folio_socio);
							
				$("#nombre").val(ui.item.socio.nombre);
				$("#folio_socio").val(ui.item.socio.folio);
				$("#calle").val(ui.item.socio.calle);
				$("#numero").val(ui.item.socio.numero);
				$("#colonia").val(ui.item.socio.colonia);
				$("#entrecalles").val(ui.item.socio.entre_calles);
				$("#telefono").val(ui.item.socio.tel);
				$("#codigo_postal").val(ui.item.socio.codigo_postal);
				$("#cuadrantes").val(ui.item.socio.cuadrantes);
				
				$("#stockid").val(ui.item.socio.stockid);
				$("#frecuencia_pago").val(ui.item.socio.frecuencia_pago);
				$("#empresa").val(ui.item.socio.empresa).change();
				$("#paymentid").val(ui.item.socio.paymentid);
				//$("#municipio").val(ui.item.socio.municipio).change();
				


				return ui.item.value;
			},
			minLength: 2,
			messages: {
				noResults: '',
				results: function() {}
			}
		});
	});

	$('#fecha_cotizacion').datepicker({
		dateFormat : 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+5'
	});

	$('#ListCotizador').dataTable( {
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
<!--Se agrego para los scroll-->
<style>
body {
	margin-bottom: 200%;
}
.myBox {
	border: none;
	padding: 5px;font: 12px/18px sans-serif;
	width: 1400px;
	height: 1000px;
	overflow: scroll;
}
</style>
<!--Termina-->
<?php FB::info($CotizadorData); ?>
<div style="height: 20px;"></div>
<div class="container-fluid">
	<div class="control-group row-fluid">
		<div class="form-legend"><h3>COTIZADOR MAESTRO</h3></div>
		<ul class="nav nav-tabs" id="myTab">
			<li class="active"><a data-toggle="tab" href="#Lista">COTIZACIONES</a></li>
			<li class=""><a data-toggle="tab" href="#Create">AGREGAR NUEVA COTIZACION</a></li>
		</ul>
		<div class="tab-content"> <!-- Start TABS -->
			<div id="Lista" class="tab-pane fade active in">
			<?php include_once('modals/EditarCotizador.modal.php'); ?>
				<div class="myBox"><!--Se agrego para mostrar scroll-->
					<table id="ListCotizador" class="table table-hover table-condensed table-striped">
						<thead>
							<tr>
							<th><?php echo _('ID') ?></th>
							<th><?php echo _('Folio Socio') ?></th>
							<th><?php echo _('Nombre') ?></th>
							<th><?php echo _('FechaCotización') ?></th>
							<th><?php echo _('Calle') ?></th>
							<th><?php echo _('Numero') ?></th>
							<th><?php echo _('Colonia') ?></th><th><?php echo _('Teléfono') ?></th>
							<th><?php echo _('EntreCalles') ?></th>
							<th><?php echo _('Municipio') ?></th>
							<th><?php echo _('Sucursal') ?></th>
							<th><?php echo _('Cuadrantes') ?></th>
							<th><?php echo _('Codigo Postal') ?></th>
							<th><?php echo _('Producto') ?></th>
							<th><?php echo _('Forma Pago') ?></th>
							<th><?php echo _('Frecuencia Pago') ?></th>
							<th><?php echo _('Empresa') ?></th>
							<th><?php echo _('Socios') ?></th>
							<th><?php echo _('Comentarios/Observaciones') ?></th>
							<th><?php echo _('Costo Inscripcion') ?></th>
							<th><?php echo _('Costo Total') ?></th>
							<th><?php echo _('Costo Inscripcion Libre') ?></th>
							<th><?php echo _('Costo Total Libre') ?></th>
							<th><?php echo _('Tipo Cotización') ?></th>
							<th><?php echo _('Usuario') ?></th>
							<th><?php echo _('Estatus') ?></th>
							<th><?php echo _('Acciones') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($CotizadorData as $Cotizador){
								
								$CostoTotal=0;
								$CostoInscripcion=0;
								if((!empty($Cotizador['costo_total'])) && (!empty($Cotizador['costo_inscripcion']))){
									$CostoTotal = $Cotizador['costo_total'];
									$CostoInscripcion = $Cotizador['costo_inscripcion'];
								}elseif((!empty($Cotizador['costo_total_2'])) && (!empty($Cotizador['costo_inscripcion_2']))){
									$CostoTotal = ($Cotizador['costo_total_2'])*($Cotizador['CNT_socios']);
									$CostoInscripcion = ($Cotizador['costo_inscripcion_2'])*($Cotizador['CNT_socios']);
								}elseif((!empty($Cotizador['costo_total_3'])) &&
								(!empty($Cotizador['costo_inscripcion_3']))){
									$CostoTotal = ($Cotizador['costo_total_3'])*($Cotizador['CNT_socios']);
									$CostoInscripcion = ($Cotizador['costo_inscripcion_3'])*($Cotizador['CNT_socios']);
								}

								$RowColor = "";
								
								switch ($Cotizador['status']) {
									case '0':
										$RowColor = "class= 'danger'";
										$Cotizador['status'] ='Cancelado';
									break;
									
									default:
									break;
								}

								?>
								<tr <?=$RowColor?> id='<?=$Cotizador['id']?>' >
								<td><?=$Cotizador['id']?></td>
								<td><?=$Cotizador['folio_socio']?></td>
								<td><?=$Cotizador['nombre']?></td>
								<td><?=$Cotizador['fecha_cotizacion']?></td>
								<td><?=$Cotizador['calle']?></td>
								<td><?=$Cotizador['numero']?></td>
								<td><?=$Cotizador['colonia']?></td>
								<td><?=$Cotizador['telefono']?></td>
								<td><?=$Cotizador['entrecalles']?></td>
								<td><?=$Cotizador['municipio']?></td>
								<td><?=$Cotizador['sucursal']?></td>
								<td><?=$Cotizador['cuadrantes']?></td><td><?=$Cotizador['codigo_postal']?></td>
								<td><?=$Cotizador['stockid']?></td>
								<td><?=$Cotizador['paymentid']?></td>
								<td><?=$Cotizador['frecuencia_pago']?></td>
								<td><?=$Cotizador['empresa']?></td>
								<td><?=$Cotizador['CNT_socios']?></td>
								<td><?=$Cotizador['comentarios']?></td>
								<td style="text-align:center;"><?=number_format($CostoInscripcion,2, '.', '')?></td>
								<td style="text-align:center;"><?=number_format($CostoTotal,2, '.', '')?></td>
								<td><?=$Cotizador['costo_inscripcion_libre']?></td>
								<td><?=$Cotizador['costo_total_libre']?></td>
								<td><?=$Cotizador['tipo']?></td>
								<td><?=$Cotizador['usuario']?></td>
								<td><?php if($Cotizador['status']==1) echo "Activo"; else echo "Cancelado"; ?></td>
								<td>
									<a onclick="EditarCotizador('<?=$Cotizador['id']?>');" title="Editar cotizacion" ><i class="icon-edit"></i></a>
									<?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("disable","id"=>$Cotizador['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de Cancelar esta Cotización?')) { return; }else{return false;};", "title"=>"Cancelar cotizacion")); 
									?>
								</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div><!--cierra el div qie se agrego para el scroll-->
			</div>
			<div id="Create" class="tab-pane fade active">
				<form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("cotizador/create/"); ?>" >
					<!--Se agrego campo para ingresar y validar que un folio exista en Afiliaciones -->
					<div class="controls">
						<textarea name="folio_socio_" id="folio_socio_" class="input-block-level SearchbyName" type="text" placeholder="INGRESE FOLIO PARA VALIDAR SI EXISTE YA EN AFILIACIONES Y MOSTRAR SU INFORMACION PARA EL LLENADO DEL FORMULARIO" style="width:1330px; height:50px;"></textarea>
					</div>
					<!--Termina-->
					<div class="control-group row-fluid">
						<div class="span2">
							<label class="control-label"><?php echo _('Nombre:'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<input type="text" id="nombre" name="nombre" required/>
							</div>
						</div>
					</div>
					<div class="control-group row-fluid">
						<div class="span2">
							<label class="control-label"><?php echo _('En caso de Tratarse de un Socio Ingrese Folio:'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<input type="text" id="folio_socio" name="folio_socio"/>
							</div>
						</div>
					</div>
					<div class="control-group row-fluid">
						<div class="span2">
							<label class="control-label"><?php echo _('Fecha:'); ?></label>
						</div>
						<div class="span3">
							<div class="controls"><input type="date" id="fecha_cotizacion" name="fecha_cotizacion" value="<?php echo date("Y-m-
							d");?>" readonly="readonly"/>
							</div>
						</div>
						<div class="span2">
							<label class="control-label"><?php echo _('Calle'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<input type="text" id="calle" name="calle" />
							</div>
						</div>
					</div>
					<div class="control-group row-fluid">
						<div class="span2">
							<label class="control-label"><?php echo _('Número:'); ?></label>
						</div>
							<div class="span3">
								<div class="controls">
									<input type="text" id="numero" name="numero" />
								</div>
							</div>
							<div class="span2">
								<label class="control-label"><?php echo _('Colonia'); ?></label>
							</div>
						<div class="span3">
							<div class="controls">
								<input type="text" id="colonia" name="colonia" />
							</div>
						</div>
					</div>
					<div class="control-group row-fluid">
						<div class="span2">
							<label class="control-label"><?php echo _('Teléfono:'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<input type="text" id="telefono" name="telefono" />
							</div>
						</div>
						<div class="span2">
							<label class="control-label"><?php echo _('Entre calles'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<input type="text" id="entrecalles" name="entrecalles" />
							</div>
						</div>
					</div>
					<div class="control-group row-fluid">
						<div class="span2">
						<label class="control-label"><?php echo _('Municipio:'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<select id="municipio" name="municipio" >
									<option value="">SELECCIONE</option>
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
								<select id="sucursal" name="sucursal" >
									<option value="">SELECCIONE</option>
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
							<label class="control-label"><?php echo _('Cuadrantes'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<input type="text" id="cuadrantes" name="cuadrantes" />
							</div>
						</div>
						<div class="span2">
							<label class="control-label"><?php echo _('Codigo Postal'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<input type="text" id="codigo_postal" name="codigo_postal" />
							</div>
						</div>
					</div>
					<div class="control-group row-fluid">
						<div class="span2">
							<label class="control-label"><?php echo _('Producto:'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<select id="stockid" name="stockid" required>
									<option value="">SELECCIONE</option>
									<?php foreach ($ProductoData as $Producto){ ?>
									<option value="<?=$Producto['stockid']?>"><?=$Producto['description']?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="span2">
							<label class="control-label"><?php echo _('Forma Pago:'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<select id="paymentid" name="paymentid" required>
									<option value="">SELECCIONE</option>
									<?php foreach ($FormapagoData as $Formapago){ ?>
									<option value="<?=$Formapago['paymentid']?>"><?=$Formapago['paymentname']?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="control-group row-fluid">
						<div class="span2">
							<label class="control-label"><?php echo _('Frecuencia Pago:'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<select id="frecuencia_pago" name="frecuencia_pago" required >
									<option value="">SELECCIONE</option>
									<?php foreach ($FrecuenciapagoData as $Frecuenciapago){ ?>
									<option value="<?=$Frecuenciapago['id']?>"><?=$Frecuenciapago['frecuencia']?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="span2">
							<label class="control-label"><?php echo _('Empresa:'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<select id="empresa" name="empresa" >
									<option value="">SELECCIONE</option>
									<?php foreach ($EmpresasData as $Empresas){ ?>
									<option value="<?=$Empresas['id']?>"><?=$Empresas['empresa']?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="control-group row-fluid">
						<div class="span2">
							<label class="control-label"><?php echo _('Socios'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<input type="text" id="CNT_socios" name="CNT_socios" required/>
							</div>
						</div>
						<div class="span2">
							<label class="control-label"><?php echo _('Comentarios/Observaciones'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<textarea name="comentarios" id="comentarios" class="form-control" style="width:500px; height:100px;"></textarea>
							</div>
						</div>
					</div>
					<div class="control-group row-fluid">
						<div class="span2">
							<label class="control-label"><?php echo _('Costo Inscripcion Libre'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<input type="text" id="costo_inscripcion_libre" name="costo_inscripcion_libre" required/>
							</div>
						</div>
						<div class="span2">
							<label class="control-label"><?php echo _('Costo Total Libre'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
							<input type="text" id="costo_total_libre" name="costo_total_libre" required/>
							</div>
						</div>
					</div>
					<div class="control-group row-fluid">
						<div class="span2">
							<label class="control-label"><?php echo _('Usuario'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<input type="text" id="usuario" name="usuario" value="<?php echo $_SESSION['UserID'];?>" readonly="readonly" />
							</div>
						</div>
						<div class="span2">
							<label class="control-label"><?php echo _('Tipo Cotización'); ?></label>
						</div>
						<div class="span3">
							<div class="controls">
								<select id="tipo" name="tipo" required>
									<option value="">SELECCIONE</option>
									<option value="Afiliacion">AFILIACION</option>
									<option value="Enfermeria">ENFERMERIA</option>
								</select>
							</div>
						</div>
					</div>
					<div class="control-group row-fluid">
						<div class="span2">
							<label class="control-label"><?php echo _('Estatus Activo'); ?></label>
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
