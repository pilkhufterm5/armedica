<script type="text/javascript">


     $(document).on('ready', function() {
        $('#Create-Modal-EditarFormapago').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("paymentmethod/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    Update:{
                        paymentid: $("#E-ID").val(),
                        paymentname: $('#E-paymentname').val(),
                        satid: $('#E-satid').val(), //Se agrego satid para el identificador que requiere el SAT Angeles Perez 2016-07-12
                        satname: $('#E-satname').val(),//Se agrego satid para la descripcion que requiere el SAT Angeles Perez 2016-07-12
                        paymenttype: $('#E-paymenttype').attr("checked") ? 1 : 0,
                        receipttype: $('#E-receipttype').attr("checked") ? 1 : 0,
                        activo: $('#E-activo').attr("checked") ? 1 : 0
                    },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#ListPaymentmethod #' + data.paymentid).html(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });


function EditarFormapago(paymentid){
        $('#ModalLabelEdit').text('Editar Formapago');
        $('#Modal_EditarFormapago').modal('show');
        $("#E-ID").val(paymentid);
        LoadForm(paymentid);
    }

function LoadForm(paymentid){

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("paymentmethod/LoadForm"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                  GetData:{
                      paymentid: paymentid
                  },
            },
            success : function(data, newValue) {
                if (data.requestresult == 'ok') {
                    displayNotify('success', data.message);
                    $('#E-paymentname').val(data.GetData.paymentname);
                    $('#E-satid').val(data.GetData.satid);//Se agrego satid para el identificador que requiere el SAT Angeles Perez 2016-07-12
                    $('#E-satname').val(data.GetData.satname);//Se agrego satid para la descripcion que requiere el SAT Angeles Perez 2016-07-12
                    if(data.GetData.paymenttype==1){
                        $('#E-paymenttype').attr('checked','checked');
                    }
                    if(data.GetData.receipttype==1){
                        $('#E-receipttype').attr('checked','checked');
                    }
                    if(data.GetData.activo==1){
                        $('#E-activo').attr('checked','checked');
                    }
                }else{
                    displayNotify('alert', data.message);
                }
            },
            error : ajaxError
        });
    }
</script>

<div id="Modal_EditarFormapago" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>
    <div class="modal-body">
        <p>
            <table class="table">
                <tbody>
                    <tr><input type="hidden" value="" id="E-ID" >
                        <td><label class="control-label" style="margin-top: 10px;">Forma pago: </label></td>
                        <td><input type="text" id="E-paymentname" /></td>
                    </tr>
                    <tr>
                        <!--Se agrego satid para la descripcion que requiere el SAT Angeles Perez 2016-07-12-->
                        <td><label class="control-label" style="margin-top: 10px;">Método pago SAT: </label></td>
                        <td><input type="text" id="E-satname" /></td>
                        <!--Termina-->
                        <!--Se agrego satid para el identificador que requiere el SAT Angeles Perez 2016-07-12-->
                        <td><label class="control-label" style="margin-top: 10px;">Identificador SAT: </label></td>
                        <td><input type="text" id="E-satid" /></td>
                        <!--Termina-->
                    </tr>
                    <tr>
                        <td><label class="control-label" >Utilizar para pagos: </label></td>
                        <td><input type="checkbox" id="E-paymenttype" value="1" style="margin-top: -5px;" /></td>
                        <td><label class="control-label" >Utilizar para recibos: </label></td>
                        <td><input type="checkbox" id="E-receipttype" value="1" style="margin-top: -5px;" /></td>
                    </tr>
                    <tr>
                        <td><label class="control-label" >Activo: </label></td>
                        <td><input type="checkbox" id="E-activo" value="1" style="margin-top: -5px;" /></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-EditarFormapago" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-EditarFormapago" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
    </div>
</div>

