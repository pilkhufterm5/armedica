<script type="text/javascript">
    $(document).on('ready',function() {
        $('#TipoActividadDTable').dataTable();

        $('#guardar').click(function(event){
       	event.preventDefault();
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl('tipoactividad/update'); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                 Editar:{
                      descripcion: $('#descripcion').val(),
                  },
            },
            success : function(Response, newValue) {
                if (Response.requestresult == 'ok') {
                    displayNotify('success', Response.message);
                    $('#myModal').foundation('reveal', 'close');
                    $('#TipoActividadDTable').append(Response.NewRow);
                }else{
                    displayNotify('alert', Response.message);
                }
            },
            error : ajaxError
        });
    });

    });
</script>

<div class="row">
	<div class="large-12 columns">
		<div class="nav-bar right">
			<ul class="button-group radius">
				<!--<li><a href="<?php echo Yii::app()->createUrl('crm/tipoactividad/create');?>" class="small button">Crear Tipo de Actividad</a></li>-->
				<li><a href="#" class="small button" data-reveal-id="myModal">Crear Tipo de Actividad Rapido</a></li>
			</ul>
		</div>
		<h1><a href='<?php echo Yii::app()->createUrl("crm/tipoactividad/index");?>'>Tipos de Activiad</a></h1>
		<hr/>
	</div>
</div>
 <div class="row">
 	<div class="large-12 columns" role="content">
		<table id="TipoActividadDTable">
			<thead>
				<tr>
					<th>ID</th>
					<th>Descripcion</th>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($TipoActividadData as $Data) { ?>
                    <tr>
                        <td><?=$Data['id']?></td>
						<td><?=$Data['descripcion']?></td>
						<td>
                            <span data-tooltip class="has-tip radius" title="Ver informacion a detalle"><a href="<?php echo Yii::app()->createUrl("crm/tipoactividad/view"); ?>&id=<?=$Data['id']?>" class="fi-info"></a></span>&nbsp;
							<span data-tooltip class="has-tip radius" title="Editar información"><a href="<?php echo Yii::app()->createUrl("crm/tipoactividad/update"); ?>&id=<?=$Data['id']?>" class="fi-pencil"></a></span>&nbsp;
							<!--span data-tooltip class="has-tip radius" title="Eliminar contacto"><a href='.Yii::app()->createUrl("contactos/delete").'&id='.$value->id_contacto.' class="fi-x"></a></span>&nbsp;<!--a href="#" class="fi-x"></a></span-->&nbsp;
                        </td>
                    </tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>

<div id="myModal" class="reveal-modal" data-reveal>
	<h2>Nuevo Tipo de Actividad</h2>

	<div class="row">
	    <div class="small-6 columns">
	        <label>Nombre</label>
	        <input type="text" placeholder="Descripción" name="descripcion" id="descripcion" required />
	    </div>

	<div class="row">
	    <div class="small-12 columns">
	        <input type="button" class="button tiny small" value="Guardar" id="guardar"/>
	    </div>
	</div>

	<a class="close-reveal-modal">&#215;</a>
</div>
