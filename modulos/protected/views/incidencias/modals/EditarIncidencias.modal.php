
<script type="text/javascript">
    $(document).on('ready', function() {

     $('#E-fecha_incidencia').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });  

     $('#E-fecha_cierre').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });   
 

        $('#Create-Modal-EditarIncidencias').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("incidencias/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    Update:{
                        folio: $("#E-folio").val(),
                        id: $("#E-id").val(),
                        fecha_incidencia: $('#E-fecha_incidencia').val(),
                        folio_socio: $('#E-folio_socio').val(),
                        asignado: $('#E-asignado').val(),
                        motivo_incidencia: $('#E-motivo_incidencia').val(),
                        descripcion_incidencia: $('#E-descripcion_incidencia').val(),
                        solucion_incidencia: $('#E-solucion_incidencia').val(),
                        cierre: $('#E-cierre').val(),
                        fecha_cierre: $('#E-fecha_cierre').val(),
                        usuario: $('#E-usuario').val(),
                        status: $('#E-status').attr("checked") ? 1 : 0, 
                        fecha_cancelacion: $('#E-fecha_cancelacion').val(),
                    },
                },

                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#ListIncidencias #' + data.id).html(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                
                error : ajaxError
            });
        });

    });

    function EditarIncidencias(id){
        $('#ModalLabelEdit').text('EDITAR INCIDENCIA DEL SERVICIO');
        $('#Modal_EditarIncidencias').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
    }
        
    function LoadForm(id){

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("incidencias/LoadForm"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                  GetData:{
                      id: id
                  },
            },

            success : function(data, newValue) {
                if (data.requestresult == 'ok') {
                    displayNotify('success', data.message);
                    $('#E-folio').val(data.GetData.folio);
                    $('#E-id').val(data.GetData.id);
                    $('#E-fecha_incidencia').val(data.GetData.fecha_incidencia);
                    $('#E-folio_socio').val(data.GetData.folio_socio);
                    $('#E-asignado').val(data.GetData.asignado);
                    $('#E-motivo_incidencia').val(data.GetData.motivo_incidencia);
                    $('#E-descripcion_incidencia').val(data.GetData.descripcion_incidencia);
                    $('#E-solucion_incidencia').val(data.GetData.solucion_incidencia);
                    $('#E-cierre').val(data.GetData.cierre);
                    $('#E-fecha_cierre').val(data.GetData.fecha_cierre);
                    $('#E-usuario').val(data.GetData.usuario);
                    if(data.GetData.status==1){
                        $('#E-status').attr('checked','checked');
                    }else{
                        $('#E-status').attr('checked',false);

                    }
                    $('#E-fecha_cancelacion').val(data.GetData.fecha_cancelacion);
                    
                }else{
                    displayNotify('alert', data.message);
                }
            },
            error : ajaxError
        });
    }
</script>

<div id="Modal_EditarIncidencias" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>
    <div class="modal-body">
        <p>
                <div class="control-group row-fluid">
                
                <div class="span2">
                        <label class="control-label"><?php echo _('Folio Incidencia:'); ?></label>
                </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-id" name="E-id" class="span12" readonly="readonly" />
                        </div>
                    </div>
                </div>
                <br>   
                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Fecha Incidencia:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-fecha_incidencia" name="E-fecha_incidencia" class="span12"  />
                        </div>
                    </div>
                </div> 
                <br>  
                <div class="control-group row-fluid">
                <!--Se agrego campo para ingresar y validar que un folio exista en Afiliaciones -->
                <div class="controls">
                    <input class="input-block-level SearchbyName" type="text" placeholder="INGRESE FOLIO PARA MOSTRAR EL NOMBRE DEL SOCIO" name="E-folio" id="E-folio">
                </div>
                <!--Termina-->
                    <div class="span2">
                        <label class="control-label"><?php echo _('Folio Socio:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-folio_socio" name="E-folio_socio" class="span12" readonly="readonly" />
                        </div>
                    </div>
                </div>
                <br>   
                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Asignación:'); ?></label>
                    </div>
                    <div class="span4">
                    <div class="controls">
                    <select id="E-asignado" name="E-asignado" class="required">
                        <option value="">SELECCIONE</option>
                                <?php foreach ($ListaAsignacionPersonal as $rows) { ?>
                                <option value="<?=$rows['id']?>">
                                    <?=$rows['nombre']?>
                                </option>
                                <?php } ?>
                            </select>
                    <script type="text/javascript">
                    $("#asignado option[value='<?=$_POST['asignado']?>']").attr("selected",true);
                    </script>
                        </div>
                    </div>
                </div> 
                    <div class="control-group row-fluid">
                             <div class="span2">
                        <label class="control-label"><?php echo _('Usuario:'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="text" id="E-usuario" name="E-usuario" value="<?php echo $_SESSION['UserID'];?>" readonly="readonly" />
                        </div>
                    </div>
                </div>

                 
                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Motivo Incidencias:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                             <textarea  id="E-motivo_incidencia" name="E-motivo_incidencia" class="form-control" style="width:400px; height:100px;" ></textarea>
                        </div>
                    </div>
                </div>

                <br>    
                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Descripción Incidencia:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <textarea  id="E-descripcion_incidencia" name="E-descripcion_incidencia" class="form-control" style="width:400px; height:100px;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Cierre Incidencia:'); ?></label>
                    </div>
                    <div class="span4">
                    <div class="controls">
                     <select id="E-cierre" name="E-cierre" >
                                <option value="">SELECCIONE</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                     </select>
                        </div>
                     </div>
                </div>
                <br>     
                    <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Solución Incidencia:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <textarea  id="E-solucion_incidencia" name="E-solucion_incidencia" class="form-control" style="width:400px; height:100px;"></textarea>
                        </div>
                    </div>
                </div>
                 <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Fecha Cierre:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-fecha_cierre" name="E-fecha_cierre" class="span12"  />
                        </div>
                    </div>
                </div>  
                <br> 
                <div class="control-group row-fluid">
                            <div class="span2">
                                <label class="control-label"><?php echo _('Status Activo'); ?></label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <input value="1" type="checkbox" id="E-status" name="E-status" checked="checked" readonly="readonly"/>
                                </div>
                            </div>
                
                    <div class="span2">
                        <label class="control-label"><?php echo _('Fecha Cancelación:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-fecha_cancelacion" name="E-fecha_cancelacion" class="span12" readonly="readonly" />
                        </div>
                    </div>
                </div>         
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-EditarIncidencias" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-EditarIncidencias" class="btn btn-success" data-dismiss="modal" aria-hidden="true" onclick="document.location.reload();">Aceptar</button>
    </div>
</div>

