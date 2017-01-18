<script type="text/javascript">


 $(document).on('ready', function() {
        $('#Create-Modal-EditarFrecuencia').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("frecuenciapago/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Update:{
						  id: $("#E-ID").val(),
                          frecuencia: $('#E-frecuencia').val(),
                          dias: $('#E-dias').val(),                         
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#ListFrecuencias #' + data.id).html(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });
        
    });
    

function EditarFrecuencia(id){
        $('#ModalLabelEdit').text('Editar Frecuencia');
        $('#Modal_EditarFrecuencia').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
    }

function LoadForm(id){
        
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("frecuenciapago/LoadForm"); ?>",
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
                    $('#E-frecuencia').val(data.GetData.frecuencia);
                    $('#E-dias').val(data.GetData.dias);                    
                    
                }else{
                    displayNotify('alert', data.message);
                }
            },
            error : ajaxError
        });
    }
</script>

<div id="Modal_EditarFrecuencia" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>
    <div class="modal-body">
        <p>
            <table class="table">
                <tbody>
                    <tr><input type="hidden" value="" id="E-ID" >
                        <td><label class="control-label" style="margin-top: 10px;">Coordinador ID: </label></td>
                        <td><input type="text" id="E-frecuencia" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">Coordinador: </label></td>
                        <td><input type="text" id="E-dias"  class="span12" /></td>                    
				</tbody>
            </table>
            
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-EditarFrecuencia" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-EditarFrecuencia" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
    </div>
</div>
