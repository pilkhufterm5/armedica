<script type="text/javascript">
    $(document).on('ready', function() {
        $("#E-deladd7").select2();
        $("#E-deladd8").select2();
        
        $('#Create-Modal-EditAddress').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("deliveryaddress/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Update:{
                          loccode: $('#E-loccode').val(),
                          locationname: $('#E-locationname').val(),
                          deladd1: $('#E-deladd1').val(),
                          deladd2: $('#E-deladd2').val(),
                          deladd3: $('#E-deladd3').val(),
                          deladd4: $('#E-deladd4').val(),
                          deladd5: $('#E-deladd5').val(),
                          deladd6: $('#E-deladd6').val(),
                          deladd7: $('#E-deladd7').val(),
                          deladd8: $('#E-deladd8').val(),
                          deladd9: $('#E-deladd9').val(),
                          deladd10: $('#E-deladd10').val(),
                          tel: $('#E-tel').val(),
                          fax: $('#E-fax').val(),
                          email: $('#E-email').val(),
                          contact: $('#E-contact').val(),
                          
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#ListDeliveryAddress #' + data.loccode).html(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });
        
    });
    
    
    function EditAddress(loccode){
        $('#ModalLabelEdit').text('Editar Dirección');
        $('#Modal_EditAddress').modal('show');
        LoadForm(loccode);
    }
    
    function LoadForm(loccode){
        
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("deliveryaddress/LoadForm"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                  GetData:{
                      loccode: loccode
                  },
            },
            success : function(data, newValue) {
                if (data.requestresult == 'ok') {
                    displayNotify('success', data.message);
                    $('#E-loccode').val(data.GetData.loccode);
                    $('#E-locationname').val(data.GetData.locationname);
                    $('#E-deladd1').val(data.GetData.deladd1);
                    $('#E-deladd2').val(data.GetData.deladd2);
                    $('#E-deladd3').val(data.GetData.deladd3);
                    $('#E-deladd4').val(data.GetData.deladd4);
                    $('#E-deladd5').val(data.GetData.deladd5);
                    $('#E-deladd6').val(data.GetData.deladd6);
                    $("#E-deladd7 option[value='"+ data.GetData.deladd7 +"']").attr("selected",true);
                    $("#E-deladd8 option[value='"+ data.GetData.deladd8 +"']").attr("selected",true);
                    $('#E-deladd9').val(data.GetData.deladd9);
                    $('#E-deladd10').val(data.GetData.deladd10);
                    $('#E-tel').val(data.GetData.tel);
                    $('#E-fax').val(data.GetData.fax);
                    $('#E-email').val(data.GetData.email);
                    $('#E-contact').val(data.GetData.contact);
                    
                }else{
                    displayNotify('alert', data.message);
                }
            },
            error : ajaxError
        });
    }
    
    
    
    
</script>

<div id="Modal_EditAddress" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>
    <div class="modal-body">
        <p>
            <table class="table">
                <tbody>
                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Codigo: </label></td>
                        <td><input type="text" id="E-loccode" readonly="readonly" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">Nombre: </label></td>
                        <td><input type="text" id="E-locationname"  class="span12" /></td>
                    </tr>
                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Calle: </label></td>
                        <td><input type="text" id="E-deladd1" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">Num.Exterior: </label></td>
                        <td><input type="text" id="E-deladd2"  class="span12" /></td>
                    </tr>
                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Num. Interior: </label></td>
                        <td><input type="text" id="E-deladd3" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">Colonia: </label></td>
                        <td><input type="text" id="E-deladd4"  class="span12" /></td>
                    </tr>
                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Localidad: </label></td>
                        <td><input type="text" id="E-deladd5" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">Referencia: </label></td>
                        <td><input type="text" id="E-deladd6"  class="span12" /></td>
                    </tr>
                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Municipio: </label></td>
                        <td>
                            <select id="E-deladd7" >
                                <option value="">&nbsp;</option>
                                <?php foreach($ListaMunicipios as $id => $Municipio){ ?>
                                    <option value="<?=$Municipio?>"><?=$Municipio?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td><label class="control-label" style="margin-top: 10px;">Estado: </label></td>
                        <td>
                            <select id="E-deladd8" >
                                <option value="">&nbsp;</option>
                                <?php foreach($ListaEstados as $id => $Estado){ ?>
                                    <option value="<?=$Estado?>"><?=$Estado?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Pais: </label></td>
                        <td><input type="text" id="E-deladd9" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">Cod. Postal: </label></td>
                        <td><input type="text" id="E-deladd10"  class="span12" /></td>
                    </tr>
                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">Telefono: </label></td>
                        <td><input type="text" id="E-tel" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">Fax: </label></td>
                        <td><input type="text" id="E-fax"  class="span12" /></td>
                    </tr>
                    <tr>
                        <td><label class="control-label" style="margin-top: 10px;">E-Mail: </label></td>
                        <td><input type="text" id="E-email" class="span12" /></td>
                        <td><label class="control-label" style="margin-top: 10px;">Contacto: </label></td>
                        <td><input type="text" id="E-contact"  class="span12" /></td>
                    </tr>
                </tbody>
            </table>
            
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-EditAddress" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-EditAddress" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
    </div>
</div>