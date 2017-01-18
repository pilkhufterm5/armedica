 <script>

    $(document).on('ready', function(){
         $('#Fases_VentaDTable').dataTable({
            "scrollX": true
         });


        $('#Crear').click(function(event){
            event.preventDefault();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl('fases_venta/create'); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                     Agregar:{
                          nombre: $('#nombre').val(),
                          status: $('#status').val(),
                          orden: $('#orden').val()
                      },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                        $('#nombre').val('');
                        $('#orden').val('');
                        $('#Crear_Fase_Venta').foundation('reveal', 'close');
                        $('#Fases_VentaDTable').append(Response.NewRow);
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
                url: "<?php echo $this->createUrl('fases_venta/update'); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                     Actualizar:{
                            id: $('#E-id').val(),
                            nombre: $('#E-nombre').val(),
                            status: $('#E-status').val(),
                            orden: $('#E-orden').val()
                      },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                        $('#nombre').val('');
                        $('#orden').val('');
                        $('#Editar_Fase_Venta').foundation('reveal', 'close');
                        $('#Fases_VentaDTable #' + Response.id).html(Response.NewRow);
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
            url: "<?php echo $this->createUrl('fases_venta/LoadForm'); ?>",
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
                    $('#Editar_Fase_Venta').foundation('reveal', 'open');
                    $('#E-id').val(Response.LoadForm.id);
                    $('#E-nombre').val(Response.LoadForm.nombre);
                    $('#E-orden').val(Response.LoadForm.orden);
                    $('#E-status').val(Response.LoadForm.status);
                    // $('#Crear_Fase_Venta').foundation('reveal', 'close');
                    // $('#Fases_VentaDTable').append(Response.NewRow);

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
                <li><a href="#" class="small button" data-reveal-id="Crear_Fase_Venta">Crear Fase de Venta</a></li>
            </ul>
        </div>
        <h1><a href='<?php echo Yii::app()->createUrl("crm/fases_venta");?>'>Fases de Venta</a></h1>
        <hr/>
    </div>
</div>


<div id="Crear_Fase_Venta" class="small reveal-modal" data-reveal>
    <h2>Crear Fase de Venta</h2>

    <div class="large-12 columns">
        <div class="large-6 columns small-12 columns">
            <label>Nombre:
                <input type="text" name="nombre" id="nombre">
            </label>
        </div>
        <div class="large-6 columns small-12 columns">
            <label>Orden:
                <input type="text" name="orden" id="orden">
            </label>
        </div>



        <div class="large-6 columns small-12 columns">
            <label>Estatus:
                <select name="status" id="status">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </label>
        </div>

        <div class="large-12 columns" style="padding-top:5%;">
            <input type="button" class="button tiny small" value="Crear" id="Crear"/>
        </div>

   </div>
    <a class="close-reveal-modal">&#215;</a>
</div>

<div id="Editar_Fase_Venta" class="small reveal-modal" data-reveal>
    <h2>Editar Fase de Venta</h2>

    <div class="large-12 columns">
        <div class="large-6 columns small-12 columns">
            <label>Nombre:
                <input type="hidden" name="E-id" id="E-id">
                <input type="text" name="E-nombre" id="E-nombre">
            </label>
        </div>

        <div class="large-6 columns small-12 columns">
            <label>Orden:
                <input type="text" name="E-orden" id="E-orden">
            </label>
        </div>

        <div class="large-6 columns small-12 columns">
            <label>Estatus:
                <select name="E-status" id="E-status">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </label>
        </div>

        <div class="large-12 columns" style="padding-top:5%;">
            <input type="button" class="button tiny small" value="Editar" id="Editar"/>
        </div>
    </div>
    <a class="close-reveal-modal">&#215;</a>
</div>


 <div class="row">
    <div class="large-12 columns" role="content">
        <table id="Fases_VentaDTable" width="100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripcion</th>
                    <th>Orden</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($Fases_Venta as $Data) {
                    if($Data['status']==1){
                        $status='Activo';
                    }else{
                        $status='Inactivo';
                    } ?>
                    <tr id="<?=$Data['id']?>">
                        <td><?=$Data['id']?></td>
                        <td><?=$Data['nombre']?></td>
                        <td><?=$Data['orden']?></td>
                        <td><?=$status?></td>
                        <td>
                            <span data-tooltip class="has-tip radius" title="Editar información"><a onclick="LoadForm(<?=$Data['id']?>)" class="fi-pencil"></a></span>&nbsp;
                            <!--span data-tooltip class="has-tip radius" title="Eliminar contacto"><a href='.Yii::app()->createUrl("contactos/delete").'&id='.$value->id_contacto.' class="fi-x"></a></span>&nbsp;<!--a href="#" class="fi-x"></a></span-->&nbsp;
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
