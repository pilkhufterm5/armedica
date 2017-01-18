<script type="text/javascript">
    $(document).on('ready',function() {
        $('#TipoActividadDTable').dataTable();

        $('#guardar').click(function(event){
           	event.preventDefault();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl('tipoactividad/create'); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                     Agregar:{
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

         $('#Editar').click(function(event){
            event.preventDefault();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl('tipoactividad/update'); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                     Actualizar:{
                            id: $('#E-id').val(),
                            descripcion: $('#E-descripcion').val(),
                      },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                        $('#id').val('');
                        $('#descripcion').val('');
                        $('#Editar_Tipo_Actividad').foundation('reveal', 'close');
                        $('#TipoActividadDTable #' + Response.id).html(Response.NewRow);
                    }else{
                        displayNotify('alert', Response.message);
                    }
                },
                error : ajaxError
            });
        });

    });

     function LoadForm(id){
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl('tipoactividad/LoadForm'); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                 LoadForm:{
                    id:id
                    // nombre: $('#E-nombre').val(),
                    // status: $('#status').val(),
                    // orden: $('#orden').val()
                  },
            },
            success : function(Response, newValue) {
                if (Response.requestresult == 'ok') {
                    $('#Editar_Tipo_Actividad').foundation('reveal', 'open');
                    $('#E-id').val(Response.LoadForm.id);
                    $('#E-descripcion').val(Response.LoadForm.descripcion);
                }else{
                    displayNotify('alert', Response.message);
                }
            },
            error : ajaxError
        });
    }
</script>

<div class="row">
	<div class="large-12 columns">
		<div class="nav-bar right">
			<ul class="button-group radius">
				<!--<li><a href="<?php echo Yii::app()->createUrl('crm/tipoactividad/create');?>" class="small button">Crear Tipo de Actividad</a></li>-->
				<li><a href="#" class="small button" data-reveal-id="myModal">Crear Tipo de Actividad</a></li>
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
					<th>Descripción</th>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($TipoActividadData as $Data) { ?>
                    <tr  id="<?=$Data['id']?>">
                        <td><?=$Data['id']?></td>
						<td><?=$Data['descripcion']?></td>
						<td>
                            <span data-tooltip class="has-tip radius" title="Editar información"><a onclick="LoadForm(<?=$Data['id']?>)" class="fi-pencil"></a></span>&nbsp;
                        </td>
                    </tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>


<div id="Editar_Tipo_Actividad" class="reveal-modal" data-reveal>
    <h2>Editar Tipo de Actividad</h2>

    <div class="large-12 columns">
        <div class="large-6 columns small-12 columns">
            <label>ID:
                <input readonly="readonly" type="text" name="E-id" id="E-id">
            </label>
        </div>
        <div class="large-6 columns small-12 columns">
            <label>Orden:
                <input type="text" name="E-descripcion" id="E-descripcion">
            </label>
        </div>
    </div>

    <div class="row">
        <div class="large-12 columns">
            <input type="button" class="button tiny small" value="Editar" id="Editar"/>
        </div>
    </div>
    <a class="close-reveal-modal">&#215;</a>
</div>


<div id="myModal" class="reveal-modal" data-reveal>
	<h2>Nuevo Tipo de Actividad</h2>

	<div class="row">
	    <div class="small-6 columns">
	        <label>Nombre:</label>
	        <input type="text" placeholder="Descripción" name="descripcion" id="descripcion" required />
	    </div>

	<div class="row">
	    <div class="small-12 columns">
	        <input type="button" class="button tiny small" value="Guardar" id="guardar"/>
	    </div>
	</div>

	<a class="close-reveal-modal">&#215;</a>
</div>
