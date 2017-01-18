<script type="text/javascript">


 $(document).on('ready', function() {
        $('#Create-Modal-EditarEmpresa').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("empresas/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Update:{
						  id: $("#E-ID").val(),
                          empresa: $('#E-empresa').val(),
                          folio: $('#E-folio').val(),                         
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#ListPaymentmethod #' + data.id).html(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });
        
    });
    

function EditarEmpresa(id){
        $('#ModalLabelEdit').text('Editar Empresa');
        $('#Modal_EditarEmpresa').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
    }

function LoadForm(id){
        
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("empresas/LoadForm"); ?>",
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
                    $('#E-empresa').val(data.GetData.empresa);
                    $('#E-folio').val(data.GetData.folio);                    
                    
                }else{
                    displayNotify('alert', data.message);
                }
            },
            error : ajaxError
        });
    }
</script>

<div id="Modal_EditarEmpresa" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>
    <div class="modal-body">
        <p>
            <table class="table">
                <tbody>
                    <tr><input type="hidden" value="" id="E-ID" >
                        <td><label class="control-label" style="margin-top: 10px;">Empresa: </label></td>
                        <td><input type="text" id="E-empresa" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">Folio: </label></td>
                        <td><input type="text" id="E-folio"  class="span12" /></td>                    
				</tbody>
            </table>
            
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-EditarEmpresa" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-EditarEmpresa" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
    </div>
</div>
