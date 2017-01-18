<script type="text/javascript">
    $(document).on('ready',function() {

         $('#closed').datetimepicker({
            lang:'es',
            formatDate:'Y-m-d',
        });

        $( "#Update" ).click(function( event ) {
            event.preventDefault();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl('oportunidades/update'); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Update:{
                          id: $('#idOportunidad').val(),
                          nombre: $('#nombre').val(),
                          closed: $('#closed').val(),
                          monto: $('#monto').val(),
                          probability: $('#probability').val(),
                          descripcion: $('#descripcion').val(),
                          id_prospecto: $('#id_prospecto').val(),
                          id_fase_venta: $('#id_fase_venta').val()
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                    }else{
                        displayNotify('error', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });
</script>

<?php FB::INFO($ProspectoData, 'DATOS DEL PROSPECTO'); ?>
<?php FB::INFO($OportunidadesData, 'DATOS DE LA OPORTUNIDAD'); ?>

<div class="row">
    <div class="large-12 columns">
        <div class="nav-bar right">
            <ul class="button-group radius">
                <li><a href="<?php echo Yii::app()->createUrl('crm/oportunidades/index');?>" class="small button">Listar Oportunidades</a></li>
            </ul>
        </div>
        <h3><a href='<?php echo Yii::app()->createUrl("crm/oportunidades/index");?>'>Oportunidades</a></h3>
        <hr/>
    </div>
</div>

<div class="row">
    <form name="crearCuenta" id="crearCuenta">
        <fieldset>
            <legend>Datos de la oportunidad</legend>
            <input type="hidden" id="idOportunidad" name="idOportunidad" value="<?=$OportunidadesData['id']?>">
            <div class="large-12 columns">
                 <div class="row">
                    <div class="large-4 columns">
                        <h5>Nombre: <input type="text" id="nombre" name="nombre" value="<?=$OportunidadesData['nombre']?>"></h5>
                    </div>
                     <div class="large-4 columns">
                        <h5>Monto $: <input type="text" id="monto" name="monto" value="<?=$OportunidadesData['amount']?>"></h5>
                    </div>
                     <div class="large-4 columns">
                        <h5>Fecha Esperada de Cierre : <input type="text" id="closed" name="closed" value="<?=$OportunidadesData['closed']?>"></h5>
                    </div>
                </div>

                <div class="row">
                    <div class="large-4 columns">
                        <h5>Probabilidad %: <input type="text" id="probability" name="probability" value="<?=$OportunidadesData['probability']?>"></h5>
                    </div>
                    <div class="large-4 columns">
                        <h5>Prospecto:
                            <select name="id_prospecto" id="id_prospecto" style="width:100%;">
                                <?php foreach($ProspectoData as $Prospecto){ ?>
                                    <option value="<?=$Prospecto['idProspecto']?>"><?=$Prospecto['nombre']?></option>
                                <?php } ?>
                            </select>
                        </h5>
                    </div>
                    <div class="large-4 columns">
                        <h5> Fase de venta:
                            <select name="id_fase_venta" id="id_fase_venta">
                                <?php foreach($Fases_Venta as $fase){ ?>
                                    <option value="<?=$fase['id']?>"><?=$fase['nombre']?></option>
                                <?php } ?>
                            </select>
                        </h5>
                    </div>
                </div>

                <div class="row">
                     <div class="large-12 columns">
                        <h5>Descripción : <textarea id="descripcion" name="descripcion"><?=$OportunidadesData['descripcion']?></textarea> </h5>
                    </div>
                </div>
                <div class="row">
                    <div class="large-12 columns">
                        <input type="button" class="button tiny input" value="Guardar" id="Update"/>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
</div>
