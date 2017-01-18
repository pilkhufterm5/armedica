<script type="text/javascript">
    $(document).on('ready', function() {
        $("#Modal_CreateEmergencia").click(function(event) {
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/CreateEmergenciaData"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Save:{
                          folio: $("#Folio").val(),
                          nombre_familiar: $("#E-nombre_familiar").val(),
                          parentesco_id: $("#E-parentesco_id").val(),
                          parentesco_otro: $("#E-parentesco_otro").val(),
                          telefono_familiar: $("#E-telefono_familiar").val(),
                          telefono_celular: $("#E-telefono_celular").val(),
                          medico_cabecera: $("#E-medico_cabecera").val(),
                          medico_celular: $("#E-medico_celular").val(),
                          especialidad_id: $("#E-especialidad_id").val(),
                          especialidad_otro: $("#E-especialidad_otro").val()
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                    }else{
                        displayNotify('fail', data.message);
                    }
                },
                error : ajaxError
            });
        });

        $("#Modal_UpdateEmergencia").click(function(event) {
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/CreateEmergenciaData"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Update:{
                          folio: $("#Folio").val(),
                          nombre_familiar: $("#E-nombre_familiar").val(),
                          parentesco_id: $("#E-parentesco_id").val(),
                          parentesco_otro: $("#E-parentesco_otro").val(),
                          telefono_familiar: $("#E-telefono_familiar").val(),
                          telefono_celular: $("#E-telefono_celular").val(),
                          medico_cabecera: $("#E-medico_cabecera").val(),
                          medico_celular: $("#E-medico_celular").val(),
                          especialidad_id: $("#E-especialidad_id").val(),
                          especialidad_otro: $("#E-especialidad_otro").val()
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                    }else{
                        displayNotify('fail', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });
</script>

<div id="Modal_Contacto_Emergencia" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">En Caso de Emergencia llamar a:</h3>
    </div>
    <div class="modal-body" >
        <p>
            <div style="height: 20px;"></div>
            <div class="control-group row-fluid">
                <div class="span12">
                    <label class="control-label" >Nombre Familiar:</label>
                    <div class="controls">
                        <input type="text" id="E-nombre_familiar" name="Emergencia[nombre_familiar]" value="<?=$GetEmergenciaData['nombre_familiar']?>" >
                    </div>
                </div>
            </div>

            <div class="control-group row-fluid">
                <div class="span6">
                    <label class="control-label" >Parentésco:</label>
                    <div class="controls">
                        <select id="E-parentesco_id" name="Emergencia[parentesco_id]"  >
                            <option></option>
                            <?php foreach ($ListaParentescos as $key => $value) {
                                echo "<option value='{$key}' >{$value}</option>";
                            } ?>
                        </select>
                        <script type="text/javascript">
                            $("#E-parentesco_id option[value='<?=$GetEmergenciaData['parentesco_id']?>']").attr("selected",true);
                        </script>
                    </div>
                </div>
                <div class="span6">
                    <label class="control-label" >&nbsp;</label>
                    <div class="controls">
                        <input type="text" id="E-parentesco_otro" name="Emergencia[parentesco_otro]" value="<?=$GetEmergenciaData['parentesco_otro']?>" >
                    </div>
                </div>
            </div>

            <div class="control-group row-fluid">
                <div class="span6">
                    <label class="control-label" >Teléfono Familiar:</label>
                    <div class="controls">
                        <input type="text" id="E-telefono_familiar" name="Emergencia[telefono_familiar]" value="<?=$GetEmergenciaData['telefono_familiar']?>" >
                    </div>
                </div>
                <div class="span6">
                    <label class="control-label" >Teléfono Celular</label>
                    <div class="controls">
                        <input type="text" id="E-telefono_celular" name="Emergencia[telefono_celular]" value="<?=$GetEmergenciaData['telefono_celular']?>" >
                    </div>
                </div>
            </div>

            <div class="control-group row-fluid">
                <div class="span12">
                    <label class="control-label" >Medico de Cabecera:</label>
                    <div class="controls">
                        <input type="text" id="E-medico_cabecera" name="Emergencia[medico_cabecera]" value="<?=$GetEmergenciaData['medico_cabecera']?>" >
                    </div>
                </div>
            </div>
            <div class="control-group row-fluid">
                <div class="span12">
                    <label class="control-label" >Teléfono Célular Medico:</label>
                    <div class="controls">
                        <input type="text" id="E-medico_celular" name="Emergencia[medico_celular]" value="<?=$GetEmergenciaData['medico_celular']?>" >
                    </div>
                </div>
            </div>
            <div class="control-group row-fluid">
                <div class="span6">
                    <label class="control-label" >Especialidad:</label>
                    <div class="controls">
                        <select id="E-especialidad_id" name="Emergencia[especialidad_id]"  >
                            <option></option>
                            <?php foreach ($ListaEspecialidades as $key => $value) {
                                echo "<option value='{$key}' >{$value}</option>";
                            } ?>
                        </select>
                        <script type="text/javascript">
                            $("#E-especialidad_id option[value='<?=$GetEmergenciaData['especialidad_id']?>']").attr("selected",true)
                        </script>
                    </div>

                </div>
                <div class="span6">
                    <label class="control-label" >&nbsp;</label>
                    <div class="controls">
                        <input type="text" id="E-especialidad_otro" name="Emergencia[especialidad_otro]" value="<?=$GetEmergenciaData['especialidad_otro']?>" >
                    </div>
                </div>
            </div>
        </p>

    </div>
    <div class="modal-footer">
        <button id="Close-Modal-Emergencia" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <?php if(!empty($GetEmergenciaData)){ ?>
            <button id="Modal_UpdateEmergencia" name="Modal_UpdateEmergencia" class="btn btn-success" data-dismiss="modal" >Actualizar</button>
        <?php }else{ ?>
            <button id="Modal_CreateEmergencia" name="Modal_CreateEmergencia" class="btn btn-success" data-dismiss="modal" >Agregar</button>
        <?php } ?>
    </div>
</div>
