<script type="text/javascript">

    $(document).on('ready', function() {
        //$("#E-stockid").select2();
        //$("#E-paymentid").select2();
        //$("#E-frecpagoid").select2();

        $('#Create-Modal-EditarCombinacion').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("precios/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    Update:{
                        id: $("#E-ID").val(),
                        nafiliados: $('#E-nafiliados').val(),
                        stockid: $('#E-stockid').val(),
                        paymentid: $('#E-paymentid').val(),
                        frecpagoid: $('#E-frecpagoid').val(),
                        porcdesc: $('#E-porcdesc').val(),
                        aplicadesc: $('#E-aplicadesc').attr("checked") ? 1 : 0,
                        costouno: $('#E-costouno').val(),
                        costodos: $('#E-costodos').val(),
                    },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#ListaPreciosTable #' + data.Rowid).html(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });
</script>


<div id="Modal_EditarCombinacion" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>
    <div class="modal-body">
        <p>
            <table class="table">
                <tbody>
                    <tr><input type="hidden" value="" id="E-ID" >
                        <td><label class="control-label" style="margin-top: 10px;">N Afiliados: </label></td>
                        <td><input type="text" id="E-nafiliados" class="span12" /></td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Producto: </label></td>
                        <td>
                            <select id="E-stockid" name="E-stockid" class="span12" >
                                <option value="">&nbsp;</option>
                                <?php foreach($ListaPlanes as $id => $value){ ?>
                                    <option value="<?=$id?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                            <!-- <input type="text" id="E-stockid" class="span12" /> -->
                        </td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Forma de Pago: </label></td>
                        <td>
                           <select id="E-paymentid" name="E-paymentid" class="span12" >
                                <option value="">&nbsp;</option>
                                <?php foreach($ListaFormasPago as $id => $value){ ?>
                                    <option value="<?=$id?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Frecuencia de Pago: </label></td>
                        <td>
                            <select id="E-frecpagoid" name="E-frecpagoid" class="span12" >
                                <option value="">&nbsp;</option>
                                <?php foreach($ListaFrecuenciaPago as $id => $value){ ?>
                                    <option value="<?=$id?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Porcentaje Descuento: </label></td>
                        <td><input type="text" id="E-porcdesc" class="span12" /></td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Aplica Descuento: </label></td>
                        <td>
                            <!-- <input type="text" id="E-aplicadesc" class="span12" /> -->
                            <input type="checkbox" id="E-aplicadesc"  class="span12" value="1" />
                        </td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Costo uno: </label></td>
                        <td><input type="text" id="E-costouno" class="span12" /></td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Costo dos: </label></td>
                        <td><input type="text" id="E-costodos" class="span12" /></td>
                    </tr>
                </tbody>
            </table>
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-EditarCombinacion" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-EditarCombinacion" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
    </div>
</div>
