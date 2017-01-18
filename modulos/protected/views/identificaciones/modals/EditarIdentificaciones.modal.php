<script type="text/javascript">
    $(document).on('ready', function() {
        $('#Create-Modal-EditarIdentificaciones').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("identificaciones/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    Update:{
                        id: $("#E-ID").val(),
                        nombre: $('#E-nombre').val(),
                        sucursal: $('#E-sucursal').val(),
                        status: $('#E-status').attr("checked") ? 1 : 0,
                    },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#ListIdentificaciones #' + data.id).html(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });


    function EditarIdentificaciones(id){
        $('#ModalLabelEdit').text('Editar Identificaciones');
        $('#Modal_EditarIdentificaciones').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
    }

    function LoadForm(id){

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("identificaciones/LoadForm"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                  GetData:{
                      id: id
                  },
            },
            success : function(data, newValue) {
                if (data.requestresult == 'ok') {
                    displayNotify('success', data.message);
                    $('#E-nombre').val(data.GetData.nombre);
                    $('#E-sucursal').val(data.GetData.sucursal);
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

<div id="Modal_EditarIdentificaciones" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>
    <div class="modal-body">
        <p>
            <table class="table">
                <tbody>
                    <tr><input type="hidden" value="" id="E-ID" >
                        <td><label class="control-label" style="margin-top: 10px;">Nombre: </label></td>
                        <td><input type="text" id="E-nombre" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">Sucursal: </label></td>
                        <td>
                            <select id="E-sucursal" name="E-sucursal" >
                                <option>Seleccione una opcion</option>
                                <option value="MTY">Monterrey</option>
                                <option value="CHIH">Chihuahua</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Status: </label></td>
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
        <button id="Close-Modal-EditarIdentificaciones" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-EditarIdentificaciones" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
    </div>
</div>

