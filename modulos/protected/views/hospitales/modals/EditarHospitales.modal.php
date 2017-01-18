<!--
+--------------+--------------+------+-----+---------+----------------+
| Field        | Type         | Null | Key | Default | Extra          |
+--------------+--------------+------+-----+---------+----------------+
| id           | int(11)      | NO   | PRI | NULL    | auto_increment |
| nombre       | varchar(50)  | YES  |     | NULL    |                |
| calle        | varchar(50)  | YES  |     | NULL    |                |
| numero       | int(11)      | YES  |     | NULL    |                |
| colonia      | varchar(50)  | YES  |     | NULL    |                |
| telefono     | int(11)      | YES  |     | NULL    |                |
| entrecalles  | varchar(100) | YES  |     | NULL    |                |
| id_municipio | int(11)      | YES  |     | NULL    |                |
| cuadrante1   | varchar(50)  | YES  |     | NULL    |                |
| cuadrante2   | varchar(50)  | YES  |     | NULL    |                |
| cuadrante3   | varchar(50)  | YES  |     | NULL    |                |
| status       | tinyint(2)   | YES  |     | 1       |                |
| sucursal     | varchar(10)  | YES  |     | NULL    |                |
+--------------+--------------+------+-----+---------+----------------+
 -->
<script type="text/javascript">
    $(document).on('ready', function() {
        $('#Create-Modal-EditarHospitales').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("hospitales/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    Update:{
                        id: $("#E-ID").val(),
                        nombre: $('#E-nombre').val(),
                        calle: $('#E-calle').val(),
                        numero: $('#E-numero').val(),
                        colonia: $('#E-colonia').val(),
                        telefono: $('#E-telefono').val(),
                        entrecalles: $('#E-entrecalles').val(),
                        municipio: $('#E-municipio').val(),
                        cuadrante1: $('#E-cuadrante1').val(),
                        cuadrante2: $('#E-cuadrante2').val(),
                        cuadrante3: $('#E-cuadrante3').val(),
                        sucursal: $('#E-sucursal').val(),
                        status: $('#E-status').attr("checked") ? 1 : 0,
                    },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#ListHospitales #' + data.id).html(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });

    function EditarHospitales(id){
        $('#ModalLabelEdit').text('Editar Hospitales');
        $('#Modal_EditarHospitales').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
    }

    function LoadForm(id){

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("hospitales/LoadForm"); ?>",
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
                    $('#E-ID').val(data.GetData.id);
                    $('#E-nombre').val(data.GetData.nombre);
                    $('#E-calle').val(data.GetData.calle);
                    $('#E-numero').val(data.GetData.numero);
                    $('#E-colonia').val(data.GetData.colonia);
                    $('#E-telefono').val(data.GetData.telefono);
                    $('#E-entrecalles').val(data.GetData.entrecalles);
                    $('#E-municipio').val(data.GetData.id_municipio);
                    $('#E-cuadrante1').val(data.GetData.cuadrante1);
                    $('#E-cuadrante2').val(data.GetData.cuadrante2);
                    $('#E-cuadrante3').val(data.GetData.cuadrante3);
                    $('#E-sucursal').val(data.GetData.sucursal);
                    if(data.GetData.status==1){
                        $('#E-status').attr('checked','checked');
                    }else{
                        $('#E-status').attr('checked',false);
                    }
                }else{
                    displayNotify('alert', data.message);
                }
            },
            error : ajaxError
        });
    }
</script>

<div id="Modal_EditarHospitales" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>
    <div class="modal-body">
        <p>
            <div class="control-group row-fluid">
                    <input type="hidden" id="E-ID" name="E-ID" class="span12" />
                    <div class="span2">
                        <label class="control-label"><?php echo _('Nombre:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-nombre" name="E-nombre" class="span12" />
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label"><?php echo _('Calle:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-calle" name="E-calle" class="span12" />
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Número:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-numero" name="E-numero" class="span12"/>
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label"><?php echo _('Colonia:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-colonia" name="E-colonia" class="span12"/>
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Teléfono:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-telefono" name="E-telefono" class="span12"/>
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label"><?php echo _('Entre calles:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-entrecalles" name="E-entrecalles" class="span12"/>
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Municipio:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <select id="E-municipio" name="E-municipio" class="span12">
                                <option value="0">Seleccione una opcion</option>

                                <?php foreach ($MunicipiosData as $Municipio){ ?>

                                <option value="<?=$Municipio['id']?>"><?=$Municipio['municipio']?></option>
                               <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label"><?php echo _('Cuadrante 1:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-cuadrante1" name="E-cuadrante1" class="span12"/>
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Cuadrante 2:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-cuadrante2" name="E-cuadrante2" class="span12"/>
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label"><?php echo _('Cuadrante 3:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input type="text" id="E-cuadrante3" name="E-cuadrante3" class="span12"/>
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Sucursal:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <select id="E-sucursal" name="E-sucursal" class="span12">
                                <option value="MTY">Seleccione una opcion</option>
                                <option value="MTY">Monterrey</option>
                                <option value="CHIH">Chihuahua</option>
                                <option value="QRO">Queretaro</option>
                                <option value="MGA">Aguascalientes</option>
                                <option value="TAM">Tampico</option>
                                <option value="TRN">Torreon</option>

                            </select>
                        </div>
                    </div>

                    <div class="span2">
                        <label class="control-label"><?php echo _('Status:'); ?></label>
                    </div>
                    <div class="span4">
                        <div class="controls">
                            <input value="1" type="checkbox" id="E-status" name="E-status" checked="checked"/>
                        </div>
                    </div>
                </div>
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-EditarHospitales" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-EditarHospitales" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
    </div>
</div>

