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
        
        $('#Send_Modal_ReactivarSocio').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/ReactivarSocio"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Reactivar:{
                          RFolio: '<?=$_POST['Folio']?>',
                          RBranchCode: $('#Reac_BranchCode').val(),
                          RDebtorNo: $('#Reac_DebtorNo').val(),
                          Rmonto_recibido: $('#Rmonto_recibido').val(),
                          Rtarifa_total: $('#Rtarifa_total').val(),
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        //$('#Modal_ReactivarSocio').modal("toggle");
                        if (confirm('¿Desea Factura Fiscal?')) {
                            // Genera la Factura
                            var jqxhr = $.ajax({
                                url: "<?php echo $this->createUrl("afiliaciones/FacturaReactivacion"); ?>",
                                type: "POST",
                                dataType : "json",
                                timeout : (120 * 1000),
                                data: {
                                      GetInvoice:{
                                          DebtorNo: data.DebtorNo,
                                          BranchCode: data.BranchCode,
                                          Tarifa_Total: data.Tarifa_Total
                                      },
                                },
                                success : function(GetInvoice, newValue) {
                                    if (GetInvoice.requestresult == 'ok') {
                                        displayNotify('success', GetInvoice.message);
                                        OpenInNewTab("../PHPJasperXML/sample1.php?isTransportista=0&transno=" + GetInvoice.transno);
                                    }else{
                                        displayNotify('alert', GetInvoice.message);
                                    }
                                },
                                error : ajaxError
                            });//End Genera Factura
                        } else {
                            // Do nothing!
                        };
                        
                        $('#SociosTable #' + data.BranchCode).removeClass('danger');
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

<div id="Modal_ReactivarSocio" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 600px;" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelReactivacion"> </h3>
    </div>
    <div class="modal-body" style="height: 200px;">
        <p>
            <div style="height: 20px;"></div>
            <input type="hidden" id="Reac_BranchCode" />
            <input type="hidden" id="Reac_DebtorNo" />
            <label>Tarifas a Registrar.</label>
            <div style="height: 20px;"></div>
            <label>Confirme el Monto Total Recibido. De lo contrario utilice la opcion de Modificar</label>
            <div style="height: 20px;"></div>
            <div class="row _span12">
                <div class="span6 controls">
                    <label class="control-label">Monto Recibido: </label>
                    <input type="text" id="Rmonto_recibido" value="<?=number_format($_POST['costo_total'],2)?>" class="span6" />
                </div>
                <div class="span6 controls">
                    <label class="control-label"> Tarifa Total: </label>
                    <input type="text" id="Rtarifa_total" value="<?=number_format($_POST['costo_total'],2)?>" class="span6" />
                </div>
            </div>
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-ReactivarSocio" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Send_Modal_ReactivarSocio" name="Send_Modal_ReactivarSocio" class="btn btn-success" data-dismiss="modal" >Reactivar</button>
    </div>
</div>

