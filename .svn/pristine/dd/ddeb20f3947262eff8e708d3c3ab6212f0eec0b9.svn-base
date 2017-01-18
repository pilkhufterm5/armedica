<script type="text/javascript">

    $(document).on('ready', function() {
        //$("#E-dproducto").select2();
        //$("#E-dformap").select2();
        //$("#E-frecuencia").select2();
        //$("#E-empresa").select2();

        $('#Create-Modal-EditarPrecioComis').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("precios/Updatepcomis"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Update:{
                          num: $("#E-ID").val(),
                          afiliados: $('#E-afiliados').val(),
                          dproducto: $('#E-dproducto').val(),
                          dformap: $('#E-dformap').val(),
                          dtipopago: $('#E-dtipopago').val(),
                          tarifains: $('#E-tarifains').val(),
                          tarifa: $('#E-tarifa').val(),
                          empresa: $('#E-empresa').val(),
                          comision1: $('#E-comision1').val(),
                          comision2: $('#E-comision2').val(),
                          comision3: $('#E-comision3').val(),
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

<div id="Modal_EditarPrecioComis" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>
    <div class="modal-body">
        <p>
            <table class="table">
                <tbody>
                    <tr><input type="hidden"  id="E-ID" >
                        <td><label class="control-label" style="margin-top: 10px;">Afiliados: </label></td>
                        <td><input type="text" id="E-afiliados" class="span12" /></td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Producto: </label></td>
                        <td>
                            <select id="E-dproducto" name="E-dproducto" class="span12" >
                                <option value="">&nbsp;</option>
                                <?php foreach($ListaPlanes as $id => $value){ ?>
                                    <option value="<?=$id?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                            <!-- <input type="text" id="E-dproducto" class="span12" /> -->
                        </td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Forma de Pago: </label></td>
                        <td>
                           <select id="E-dformap" name="E-dformap" class="span12" >
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
                            <select id="E-dtipopago" name="E-dtipopago" class="span12" >
                                <option value="">&nbsp;</option>
                                <?php foreach($ListaFrecuenciaPago as $id => $value){ ?>
                                    <option value="<?=$id?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Tarifa Inscripción: </label></td>
                        <td><input type="text" id="E-tarifains" class="span12" /></td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Tarifa: </label></td>
                        <td>
                            <input type="text" id="E-tarifa" class="span12" />
                        </td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Empresa: </label></td>
                        <td>
                            <select id="E-empresa" name="E-empresa" class="span12" >
                                <option value="">&nbsp;</option>
                                <?php foreach($ListaEmpresas as $id => $value){ ?>
                                    <option value="<?=$id?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Comision 1: </label></td>
                        <td><input type="text" id="E-comision1" class="span12" /></td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Comision 2: </label></td>
                        <td><input type="text" id="E-comision2" class="span12" /></td>
                    </tr>

                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Comision 3: </label></td>
                        <td><input type="text" id="E-comision3" class="span12" /></td>
                    </tr>

				</tbody>
            </table>

        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-EditarPrecioComis" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-EditarPrecioComis" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
    </div>
</div>
