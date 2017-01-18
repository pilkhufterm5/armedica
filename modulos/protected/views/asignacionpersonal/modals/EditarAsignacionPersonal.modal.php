<script type="text/javascript">
    $(document).on('ready', function() {
        $('#Create-Modal-EditarAsignacionPersonal').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("asignacionpersonal/Update"); ?>",
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
                        $('#ListAsignacionPersonal #' + data.id).html(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });


    function EditarAsignacionPersonal(id){
        $('#ModalLabelEdit').text('Editar Personal para Asignación');
        $('#Modal_EditarAsignacionPersonal').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
    }
 
    function LoadForm(id){

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("asignacionpersonal/LoadForm"); ?>",
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

<div id="Modal_EditarAsignacionPersonal" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>
        <div class="modal-body">
            <p>

                <table class="table">
                    <tbody>
                        <tr>
                            <input type="hidden" value="" id="E-ID" >
                            <td><label class="control-label" style="margin-top: 10px;">Nombre: </label></td>
                            <td><input type="text" id="E-nombre" class="span12" required /></td>
                       </tr>
                       <tr>
                            <td><label class="control-label" style="margin-top: 10px;">Sucursal: </label></td>
                            <td>
                                <select id="E-sucursal" name="E-sucursal" >
                                    <option>SELECCIONE</option>
                                    <option value="MTY">MONTERREY</option>
                                    <option value="CHH">CHIHUAHUA</option>
                                    <option value="TAM">TAMPICO</option>
                                    <option value="AGS">AGUASCALIENTES</option>
                                    <option value="QRO">QUERETARO</option>
                                    <option value="TRN">TORREÓN</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label" style="margin-top: 10px;">Status: </label></td>
                            <td>
                                <input type="checkbox" id="E-status" name="E-status"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </p>
        </div>
        <div class="modal-footer">
            <button id="Close-Modal-EditarAsignacionPersonal" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button id="Create-Modal-EditarAsignacionPersonal" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
        </div>
</div>

