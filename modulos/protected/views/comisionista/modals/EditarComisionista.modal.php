<script type="text/javascript">
    $(document).on('ready', function() {
        $('#Create-Modal-EditarComisionista').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("comisionista/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    Update:{
                        id: $("#E-ID").val(),
                        comisionista: $('#E-comisionista').val(),
                        coordina_id: $('#E-coordina_id').val(),
                        coordinador: $('#E-coordinador').val(),
                        activo: $('#E-activo').attr("checked") ? 1 : 0,
                    },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#ListComisionistas #' + data.id).html(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });


    function EditarComisionista(id){
        $('#ModalLabelEdit').text('Editar Comisionista');
        $('#Modal_EditarComisionista').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
    }

    function LoadForm(id){

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("comisionista/LoadForm"); ?>",
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
                    $('#E-comisionista').val(data.GetData.comisionista);
                    $('#E-coordina_id').val(data.GetData.coordina_id);
                    $('#E-coordinador').val(data.GetData.coordinador);
                    if(data.GetData.activo==1){
                        $('#E-activo').attr('checked','checked');
                    }else{
                        $('#E-activo').attr('checked',false);
                    }
                }else{
                    displayNotify('alert', data.message);
                }
            },
            error : ajaxError
        });
    }
</script>

<div id="Modal_EditarComisionista" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>
    <div class="modal-body">
        <p>
            <table class="table">
                <tbody>
                    <tr><input type="hidden" value="" id="E-ID" >
                        <td><label class="control-label" style="margin-top: 10px;">Comisionista: </label></td>
                        <td><input type="text" id="E-comisionista" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">Coordinador: </label></td>
                        <td><select id="E-coordina_id" name="E-coordina_id" class="span12">
                        <?php
                            echo "<option>Seleccione una opcion</option>";
                            foreach ($CooordinadoresData as $Coordinador) {
                                echo "<option value='".$Coordinador['coordina_id']."'>".$Coordinador['coordinador']."</option>";
                            }
                            echo "<option>.</option>";
                        ?>
                    </select></td>
                    </tr>
                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Estado: </label></td>
                        <td><input type="checkbox" id="E-activo" value="1" /></td>
                    </tr>
				</tbody>
            </table>

        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-EditarComisionista" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-EditarComisionista" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
    </div>
</div>

