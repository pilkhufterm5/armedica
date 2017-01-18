<script type="text/javascript">
    $(document).on('ready', function() {
        
        $('#UpdateSalesOrder').click(function() {
            $.blockUI();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("facturacion/CheckFacturar"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    Check:{
                        BitacoraID: idBitacora,
                        Value: Value
                    },
                },
                success : function(Response, newValue) {
                    $.unblockUI();
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                    }else{
                        displayNotify('error', Response.message);
                    }
                },
                error : ajaxError
            });
        });
        
    });
</script>


<div id="Modal_EditSalesOrder" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabel-EditSalesOrder">Editar Pedido N°: <?=$_POST['Folio']?></h3>
    </div>
    <div class="modal-body">
        <p>
        <div class="row span12">
            <div class="span6">
                <label>Fecha Baja:</label>
                <input id="Fecha_Baja" type="text" class="span6" />
            </div>
            <div class="span6">
                <label>Cancelación Efectiva a partir de:</label>
                <input id="Fecha_Cancelacion_Efectiva" type="text" class="span6" />
            </div>
        </div>
        
        <div style="height: 80px;"></div>
        <div class="row span12">
            <label>Motivo Cancelación:</label>
            <select id="M_Cancelacion" class="span6" >
            <?php foreach($ListaMotivosCancelacion as $id => $Name){ ?>
                <option value="<?=$Name?>"><?=$Name?></option>
            <?php } ?>
            </select>
        </div>
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-UpdateSalesOrder" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="UpdateSalesOrder" class="btn btn-success">Aceptar</button>
    </div>
</div>