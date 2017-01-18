
<script type="text/javascript">
    $(document).on('ready', function() {

        $('#E-fecha_alerta').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });


        $('#Create-Modal-EditarSeguimiento').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("seguimiento/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    Update:{
                        id: $("#E-ID").val(),
                        folio: $('#E-folio').val(),
                        fecha_registro: $('#E-fecha_registro').val(),
                        fecha_alerta: $('#E-fecha_alerta').val(),
                        descripcion: $('#E-descripcion').val(),
                        usuario: $('#E-usuario').val(),
                          
                    },
                },

                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#ListSeguimiento #' + data.id).html(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                
                error : ajaxError
            });
        });

    });

    function EditarSeguimiento(id){
        $('#ModalLabelEdit').text('Editar Seguimiento');
        $('#Modal_EditarSeguimiento').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
    }
        
    function LoadForm(id){

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("seguimiento/LoadForm"); ?>",
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
                    $('#E-ID').val(data.GetData.id);
                    $('#E-folio').val(data.GetData.folio);
                    $('#E-fecha_registro').val(data.GetData.fecha_registro);
                    $('#E-fecha_alerta').val(data.GetData.fecha_alerta);
                    $('#E-descripcion').val(data.GetData.descripcion);
                    $('#E-usuario').val(data.GetData.usuario);
                    
                }else{
                    displayNotify('alert', data.message);
                }
            },
            error : ajaxError
        });
    }
</script>

<div id="Modal_EditarSeguimiento" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>
    <div class="modal-body">
        <p>
                    <div class="control-group row-fluid">
                    <input type="hidden" id="E-ID" name="E-ID" class="span12" />
                    <div class="span2">
                        <label class="control-label"><?php echo _('Folio:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-folio" name="E-folio" class="span12" />
                        </div>
                    </div>
                 </div>
                    <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Fecha Registro:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-fecha_registro" name="E-fecha_registro" class="span12" readonly="readonly" />
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label"><?php echo _('Fecha Alerta:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-fecha_alerta" name="E-fecha_alerta" class="span12" />
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Usuario:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-usuario" name="E-usuario" class="span12" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label"><?php echo _('Descripcion:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <textarea name="E-descripcion" id="E-descripcion" class="form-control" rows="5"></textarea>
                        </div>
                    </div>
                </div>

                
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-EditarSeguimiento" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-EditarSeguimiento" class="btn btn-success" data-dismiss="modal" aria-hidden="true" onclick="document.location.reload();">Aceptar</button>
    </div>
</div>

