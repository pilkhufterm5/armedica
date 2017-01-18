<script type="text/javascript">
    $(document).on('ready', function() {
        $('#fecha_inicial').datepicker({
            dateFormat : 'yy-mm-dd', 
            changeMonth: true, 
            changeYear: true, 
            yearRange: '-100:+5'
        });

        $('#fecha_final').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true, 
            changeYear: true, 
            yearRange: '-100:+5'
        });
        
        $('#Send_Modal_ReactivarAfiliado').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/ReactivarAfiliado"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Reactivasion:{
                          RFolio: $('#Reac_Folio').val(),
                          RDebtorNo: $('#Reac_DebtorNo').val(),
                          Rmonto_recibido: $('#Rmonto_recibido').val(),
                          Rtarifa_total: $('#Rtarifa_total').val(),
                          RTipo: $('#Reac_Tipo').val(),
                          
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $("#AFStatus").text("Afiliado Activo");
                        $("#AFStatus").removeClass("badge-important");
                        $("#AFStatus").addClass("badge-success");
                        $('#LSuspension').hide();
                        $("#UpdateData").show();
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });
        
        function OpenInNewTab(url ){
            var win = window.open(url, '_blank');
            win.focus();
        }
        
    });
    
</script>

<div id="Modal_ReactivarAfiliado" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"  >
    <div class="modal-header"> 
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Tarifas a Registrar</h3>
    </div>
    <div class="modal-body" style="height: 200px;">
        <p>
            <input type="hidden" id="Reac_Folio" value="<?=$_POST['Folio']?>" />
            <input type="hidden" id="Reac_DebtorNo" />
            <input type="hidden" id="Reac_Tipo" />
            <label>Confirme el Monto Total Recibido.</label>
            <div style="height: 20px;"></div>
            
            <div class="control-group">
                <label class="control-label" for="monto_recibido">Monto Recibido:</label>
                <div class="controls">
                    <div class="input-prepend">
                        <span class="add-on" style="margin-top: 5px;"><i class=""><b>$</b></i></span>
                        <input type="text" id="Rmonto_recibido" name="Rmonto_recibido" value="<?=number_format($_POST['costo_total'],2)?>" class="span4" >
                    </div>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="tarifa_total">Tarifa Total:</label>
                <div class="controls">
                    <div class="input-prepend">
                        <span class="add-on" style="margin-top: 5px;"><i class=""><b>$</b></i></span>
                        <input type="text" id="Rtarifa_total" name="tarifa_total" value="<?=number_format($_POST['costo_total'],2)?>" class="span4" >
                    </div>
                </div>
            </div>
        </p>
        
        
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-ReactivarAfiliado" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Send_Modal_ReactivarAfiliado" name="Send_Modal_ReactivarAfiliado" class="btn btn-success" data-dismiss="modal" >Reactivar</button>
    </div>
</div>

