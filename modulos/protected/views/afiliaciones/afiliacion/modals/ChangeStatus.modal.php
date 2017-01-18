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

        $('#_Send_Modal_SuspenderSocio').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/SuspenderSocio"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Cancelar:{
                          SBranchCode: $('#Sus_BranchCode').val(),
                          SDebtorNo: $('#Sus_DebtorNo').val(),
                          SFecha_Inicial: $('#fecha_inicial').val(),
                          SFecha_Final: $('#fecha_final').val(),
                          SMotivos: $('#Motivo_Suspencion').val(),
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);

                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });
    });

</script>
<style>
    .field-annotation{
        top: 30px;
    }
</style>
<div id="Modal_CancelarSocio" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelCancelacion"> </h3>
    </div>
    <div class="modal-body" style="height: 200px;">
        <p>
            <input type="hidden" id="Cancel_BranchCode" />
            <input type="hidden" id="Cancel_DebtorNo" />

        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-CancelarSocio" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Send_Modal_CancelarSocio" name="Modal_CancelarSocio" class="btn btn-danger" data-dismiss="modal" >Suspender</button>
    </div>
</div>

