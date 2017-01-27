<script type="text/javascript">
    $(document).on('ready',function() {

        $("#taxref").blur(function(){
            vRfcs($(this));
        });

        $( "#CrearCliente" ).click(function( event ) {
            if(!vRfcs($('#taxref'))){
                return;
            }
            event.preventDefault();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl('cuentas/create'); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      crearCuenta:{
                          idProspecto: $('#idProspecto').val(),
                          nombre: $('#nombreCuenta').val(),
                          apellidoPaterno: $('#apellidoPaternoCuenta').val(),
                          apellidoMaterno: $('#apellidoMaternoCuenta').val(),
                          telefono: $('#telefonoCuenta').val(),
                          fechaAlta:$('#fechaAltaCuenta').val(),
                          direccion1: $('#direccion1Cuenta').val(),
                          direccion2: $('#direccion2Cuenta').val(),
                          direccion3: $('#direccion3Cuenta').val(),
                          direccion4: $('#direccion4Cuenta').val(),
                          direccion5: $('#direccion5Cuenta').val(),
                          direccion6: $('#direccion6Cuenta').val(),
                          direccion7: $('#direccion7Cuenta').val(),
                          direccion8: $('#direccion8Cuenta').val(),
                          direccion9: $('#direccion9Cuenta').val(),
                          direccion10: $('#direccion10Cuenta').val(),
                          taxref: $('#taxref').val(),
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#crearCuenta').foundation('reveal', 'close');
                        $('#btn_crearCuenta').hide();
                        $('#ListUpdates').prepend(data.NewUpdate);
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
<form name="crearCuenta" id="crearCuenta">

     <fieldset>
        <legend>Datos de la cuenta</legend>
        <input type="hidden" id="idProspecto" name="idProspecto" value="<?=$ProspectoData['idProspecto']?>">
        <input type="hidden" id="fechaAltaCuenta" name="fechaAltaCuenta" value="<?=$ProspectoData['fechaAlta']?>">
        <div class="large-8 columns">
             <div class="row">
                <div class="large-6 columns">
                    <h5>* Nombre: <input type="text" id="nombreCuenta" name="nombreCuenta" value="<?=$ProspectoData['nombre']?>"></h5>
                </div>
                <div class="large-6 columns">
                    <h5>* Apellido Paterno: <input type="text" id="apellidoPaternoCuenta" name="apellidoPaternoCuenta" value="<?=$ProspectoData['apellidoPaterno']?>"></h5>
                </div>
            </div>
            <div class="row">
                <div class="large-6 columns">
                    <h5>* Apellido Materno: <input type="text" id="apellidoMaternoCuenta" name="apellidoMaternoCuenta" value="<?=$ProspectoData['apellidoMaterno']?>"></h5>
                </div>
                <div class="large-6 columns">
                    <h5>* Teléfono: <input type="text" id="telefonoCuenta" name="telefonoCuenta" value="<?=$ProspectoData['telefono']?>"></h5>
                </div>
            </div>
            <div class="row">
                <div class="large-6 columns">
                    <h5>* Calle: <input type="text" id="direccion1Cuenta" name="direccion1Cuenta" value="<?=$ProspectoData['direccion1']?>"></h5>
                </div>
                <div class="large-6 columns">
                    <h5>* Número Exterior: <input type="text" id="direccion2Cuenta" name="direccion2Cuenta" value="<?=$ProspectoData['direccion2']?>"></h5>
                </div>
            </div>
            <div class="row">
                <div class="large-6 columns">
                    <h5>* Número Interior: <input type="text" id="direccion3Cuenta" name="direccion3Cuenta" value="<?=$ProspectoData['direccion3']?>"></h5>
                </div>
                <div class="large-6 columns">
                    <h5>* Colonia: <input type="text" id="direccion4Cuenta" name="direccion4Cuenta" value="<?=$ProspectoData['direccion4']?>"></h5>
                </div>
            </div>
            <div class="row">
                <div class="large-6 columns">
                    <h5>* Localidad: <input type="text" id="direccion5Cuenta" name="direccion5Cuenta" value="<?=$ProspectoData['direccion5']?>"></h5>
                </div>
                <div class="large-6 columns">
                    <h5>* Referencia: <input type="text" id="direccion6Cuenta" name="direccion6Cuenta" value="<?=$ProspectoData['direccion6']?>"></h5>
                </div>
            </div>
            <div class="row">
                <div class="large-6 columns">
                    <h5>* Municipio: <input type="text" id="direccion7Cuenta" name="direccion7Cuenta" value="<?=$ProspectoData['direccion7']?>"></h5>
                </div>
                <div class="large-6 columns">
                    <h5>* Estado: <input type="text" id="direccion8Cuenta" name="direccion8Cuenta" value="<?=$ProspectoData['direccion8']?>"></h5>
                </div>
            </div>
            <div class="row">
                <div class="large-6 columns">
                    <h5>* País: <input type="text" id="direccion9Cuenta" name="direccion9Cuenta" value="<?=$ProspectoData['direccion9']?>"></h5>
                </div>
                <div class="large-6 columns">
                    <h5>* Código Postal: <input type="text" id="direccion10Cuenta" name="direccion10Cuenta" value="<?=$ProspectoData['direccion10']?>"></h5>
                </div>
            </div>
            <div class="row">
                <div class="large-6 columns">
                    <h5>Tipo de persona:
                    <select name="tipopersona" id="tipopersona">
                        <option value="MORAL">MORAL</option>
                        <option value="FISICA">FISICA</option>
                    </select>
                    </h5>
                </div>
                <div class="large-6 columns">
                    <h5>* RFC: <input type="text" id="taxref" name="taxref" value="<?=$ProspectoData['RFC']?>"></h5>
                </div>
            </div>
        </div>
        <div class="large-12 columns">
            <input type="button" class="button tiny input" value="Crear Cuenta" id="CrearCliente"/>
            <?php //echo CHtml::submitButton($model->isNewRecord ? 'Crear' : 'Guardar',array('class'=>'button')); ?>
        </div>
    </fieldset>
</form>

