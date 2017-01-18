<script type="text/javascript">


 $(document).on('ready', function() {
        $('#Create-Modal-EditarCobrador').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("cobradores/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    Update:{
                        id: $("#E-ID").val(),
                        nombre: $('#E-nombre').val(),
                        comision: $('#E-comision').val(),
                        zona: $('#E-zona').val(),
                        activo: $('#E-activo').attr("checked") ? 1 : 0,
                        reasigna: $('#E-reasigna').val(),
                        transfe: $('#E-transfe').attr("checked") ? 1 : 0,
						cobori: $('#E-cobori').val(),
                        empresa: $('#E-empresa').attr("checked") ? 1 : 0,
                    },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#ListCobradores #' + data.id).html(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });


function EditarCobrador(id){
        $('#ModalLabelEdit').text('Editar Cobrador');
        $('#Modal_EditarCobrador').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
    }

function LoadForm(id){

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("cobradores/LoadForm"); ?>",
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
                    $('#E-comision').val(data.GetData.comision);
                    $('#E-zona').val(data.GetData.zona);

                    if(data.GetData.activo==1){
                        $('#E-activo').attr('checked','checked');
                    }else{
                        $('#E-activo').attr('checked',false);
                    }

                    $('#E-reasigna').val(data.GetData.reasigna);
                    $('#E-transfe').val(data.GetData.transfe);
                    if(data.GetData.transfe==1){
						$('#E-transfe').attr('checked','checked');
					}
                    $('#E-cobori').val(data.GetData.cobori);
                    $('#E-empresa').val(data.GetData.empresa);
                    if(data.GetData.empresa==1){
						$('#E-empresa').attr('checked','checked');
					}

                }else{
                    displayNotify('alert', data.message);
                }
            },
            error : ajaxError
        });
    }
</script>

<div id="Modal_EditarCobrador" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
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
                        <td><label class="control-label" style="margin-top: 10px;">Comisión: </label></td>
                        <td><input type="text" id="E-comision"  class="span12" /></td>
                    </tr>
                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Zona: </label></td>
                        <td><input type="text" id="E-zona" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">Activo: </label></td>
                        <td><input type="checkbox" id="E-activo"  value="1" /></td>
                    </tr>
                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Reasigna: </label></td>
                        <td><input type="text" id="E-reasigna" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">Transfe: </label></td>
                        <td><input type="checkbox" id="E-transfe" value="1" /></td>
                    </tr>
                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">cobori: </label></td>
                        <td><input type="text" id="E-cobori" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">Empresa: </label></td>
                        <td><input type="checkbox" id="E-empresa" value="1" /></td>
                    </tr>
				</tbody>
            </table>

        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-EditarCobrador" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-EditarCobrador" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
    </div>
</div>
