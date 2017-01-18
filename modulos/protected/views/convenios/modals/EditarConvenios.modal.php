<script type="text/javascript">


 $(document).on('ready', function() {
        $('#Create-Modal-EditarConvenios').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("convenios/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Update:{
						  id: $("#E-ID").val(),
                          convenio: $('#E-convenio').val(),
                          bcotrans: $('#E-bcotrans').val(),
                          activo: $('#E-activo').attr("checked") ? 1 : 0,
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#ListConvenios #' + data.id).html(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });


function EditarConvenios(id){
        $('#ModalLabelEdit').text('Editar Convenio');
        $('#Modal_EditarConvenios').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
    }

function LoadForm(id){

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("convenios/LoadForm"); ?>",
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
                    $('#E-convenio').val(data.GetData.convenio);
                    $('#E-bcotrans').val(data.GetData.bcotrans);
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

<div id="Modal_EditarConvenios" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>
    <div class="modal-body">
        <p>
            <table class="table">
                <tbody>
                    <tr><input type="hidden" value="" id="E-ID" >
                        <td><label class="control-label" style="margin-top: 10px;">Convenio: </label></td>
                        <td><input type="text" id="E-convenio" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">bcotrans: </label></td>
                        <td><input type="text" id="E-bcotrans"  class="span12" /></td>
                    </tr>
                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Activo: </label></td>
                        <td><input type="checkbox" id="E-activo" value="1"/></td>
                        <td>  <td></td></td>
                        <td></td>
                    </tr>
				</tbody>
            </table>

        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-EditarConvenios" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-EditarConvenios" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
    </div>
</div>

