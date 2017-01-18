<script type="text/javascript">
    $(document).on('ready', function() {
        $('#SAfecha_inicial').datepicker({
            dateFormat : 'yy-mm-dd'
        });

        $('#SAfecha_final').datepicker({
            dateFormat : 'yy-mm-dd'
        });

        $('#Send_Modal_SuspenderAfiliado').click(function(){
            Folio = $('#Folio').val();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/SuspenderAfiliado"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Suspension:{
                          folio: Folio,
                          SFecha_Inicial: $('#SAfecha_inicial').val(),
                          SFecha_Final: $('#SAfecha_final').val(),
                          SMotivos: $('#SAMotivo_Suspension').val(),
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $("#AFStatus").text("Afiliado Suspendido");
                        $("#AFStatus").removeClass("badge-success");
                        $("#AFStatus").addClass("badge-warning");
                        $('#Cancelar').hide();
                        $('#Suspender').hide();
                        $("#UpdateData").hide();
                        if (confirm('El Folio: <?=$_POST['Folio']?> ha sido Suspendido Correctamente. \n ¿Desea realizar la impresion del Reporte de Suspensión?')) {
                            OpenInNewTab("<?php echo $this->createUrl('afiliaciones/viewsuspendpdf'); ?>&Folio=" + Folio + "&SuspendNo=" + data.SuspendNo);
                            //OpenInNewTab("../tmp/suspensiones/SuspendNo-" + data.SuspendNo + ".pdf");
                        } else {
                            // Do nothing!
                        };
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


<style>
    .field-annotation{
        top: 0px;
    }
</style>
<div id="Modal_SuspenderAfiliado" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelSuspensionA"> </h3>
    </div>
    <div class="modal-body" style="height: 200px;">
        <p>
            <div class="row span12">
                <div class="span6 controls">
                    <label class="control-label">Fecha Inicial: </label>
                    <input type="text" id="SAfecha_inicial" class="span6" />
                </div>
                <div class="span6 controls">
                    <label class="control-label">Fecha Final: </label>
                    <input type="text" id="SAfecha_final" class="span6" />
                </div>
            </div>

            <div class="span12" style="margin-top: 20px;">
                    <label class="control-label">Motivo de Suspención: </label>
                    <textarea id="SAMotivo_Suspension"></textarea>
                    <span class="field-annotation"></span>
                    <script>
                        $("#SAMotivo_Suspension").charCount({
                            allowed: 200,
                            warning: 50,
                            counterText: 'Caracteres restantes: '
                        });
                    </script>
            </div>
            <div style="height: 20px;"></div>
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-SuspenderAfiliado" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Send_Modal_SuspenderAfiliado" name="Send_Modal_SuspenderAfiliado" class="btn btn-danger" data-dismiss="modal" >Suspender</button>
    </div>
</div>
