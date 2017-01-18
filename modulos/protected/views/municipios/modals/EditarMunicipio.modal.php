<script type="text/javascript">


 $(document).on('ready', function() {
        $('#Create-Modal-EditarMunicipio').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("municipios/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Update:{
						  id: $("#E-ID").val(),
						  municipio: $('#E-municipio').val(),
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#List #' + data.id).html(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });
        
    });
    

function EditarMunicipio(id){
        $('#ModalLabelEdit').text('Editar Municipio');
        $('#Modal_EditarMunicipio').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
    }

function LoadForm(id){
        
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("municipios/LoadForm"); ?>",
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
                    $('#E-municipio').val(data.GetData.municipio);
                    
                }else{
                    displayNotify('alert', data.message);
                }
            },
            error : ajaxError
        });
    }
</script>

<div id="Modal_EditarMunicipio" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>
    <div class="modal-body">
        <p>
            <table class="table">
                <tbody>
                    <tr><input type="hidden" value="" id="E-ID" >
                        <td><label class="control-label" style="margin-top: 10px;">Municipio: </label></td>
                        <td><input type="text" id="E-municipio" class="span12" /></td>
				</tbody>
            </table>
            
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-EditarMunicioio" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-EditarMunicipio" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
    </div>
</div>
