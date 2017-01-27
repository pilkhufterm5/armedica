<script type="text/javascript">
    $(document).on('ready',function() {

        $("#pricelist option[value='<?=$AccountData['salestype']?>']").attr('selected', true);

        $("#taxref").blur(function(){
            vRfcs($(this));
        });

        $( "#EditarCliente" ).click(function( event ) {
            if(!vRfcs($('#taxref'))){
                return;
            }
            event.preventDefault();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl('cuentas/update'); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      editarCuenta:{
                          debtorno: $('#debtorno').val(),
                          nombre: $('#nombreCuenta').val(),
                          nombre2: $('#nombre2').val(),
                          apellidoPaterno: $('#apellidoPaternoCuenta').val(),
                          apellidoMaterno: $('#apellidoMaternoCuenta').val(),
                          telefono: $('#telefonoCuenta').val(),
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
                          pricelist: $('#pricelist').val(),
                          discount: $('#discount').val(),
                          creditlimit: $('#creditlimit').val(),
                          taxref: $('#taxref').val(),
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

<?php FB::INFO($AccountData, 'DATOS DEL PROSPECTO'); ?>
<!-- <form name="crearCuenta" id="crearCuenta"> -->


<div class="large-12 small-12 columns">


        <div class="row">
            <div class="large-9 small-12 columns">
                <h4>Datos de la cuenta: <?php echo $AccountData['name']; ?></h4>
            </div>
            <div class="large-3 samll-12 columns">
                <div class="nav-bar right">
                    <ul class="button-group radius">
                        <li><a href="<?php echo Yii::app()->createUrl('crm/cuentas/index');?>" class="small button">Listar Cuentas</a></li>
                    </ul>
                </div>
            </div>
        </div>
         <div class="row">
            <div class="large-4 columns">
                 <input type="hidden" id="debtorno" name="debtorno" value="<?=$AccountData['debtorno']?>">
                <h5>* Nombre: <input type="text" id="nombreCuenta" name="nombreCuenta" value="<?=$AccountData['name']?>"></h5>
            </div>
            <div class="large-4 columns">
                <h5>Nombre Comercial: <input type="text" id="nombre2" name="nombre2" value="<?=$AccountData['name2']?>"></h5>
            </div>
            <div class="large-4 columns">
                <h5>* Calle: <input type="text" id="direccion1Cuenta" name="direccion1Cuenta" value="<?=$AccountData['address1']?>"></h5>
            </div>
        </div>
        <div class="row">
            <div class="large-4 columns">
                <h5>* Número Exterior: <input type="text" id="direccion2Cuenta" name="direccion2Cuenta" value="<?=$AccountData['address2']?>"></h5>
            </div>
            <div class="large-4 columns">
                <h5>* Número Interior: <input type="text" id="direccion3Cuenta" name="direccion3Cuenta" value="<?=$AccountData['address3']?>"></h5>
            </div>
            <div class="large-4 columns">
                <h5>* Colonia: <input type="text" id="direccion4Cuenta" name="direccion4Cuenta" value="<?=$AccountData['address4']?>"></h5>
            </div>
        </div>
        <div class="row">
            <div class="large-4 columns">
                <h5>* Localidad: <input type="text" id="direccion5Cuenta" name="direccion5Cuenta" value="<?=$AccountData['address5']?>"></h5>
            </div>
            <div class="large-4 columns">
                <h5>* Referencia: <input type="text" id="direccion6Cuenta" name="direccion6Cuenta" value="<?=$AccountData['address6']?>"></h5>
            </div>
            <div class="large-4 columns">
                <h5>* Municipio: <input type="text" id="direccion7Cuenta" name="direccion7Cuenta" value="<?=$AccountData['address7']?>"></h5>
            </div>
        </div>
        <div class="row">
            <div class="large-4 columns">
                <h5>* Estado: <input type="text" id="direccion8Cuenta" name="direccion8Cuenta" value="<?=$AccountData['address8']?>"></h5>
            </div>
            <div class="large-4 columns">
                <h5>* País: <input type="text" id="direccion9Cuenta" name="direccion9Cuenta" value="<?=$AccountData['address9']?>"></h5>
            </div>
            <div class="large-4 columns">
                <h5>* Código Postal: <input type="text" id="direccion10Cuenta" name="direccion10Cuenta" value="<?=$AccountData['address10']?>"></h5>
            </div>
        </div>
        <div class="row">
            <div class="large-4 small-12 columns">
                <h5>Teléfono: <input type="text" id="telefonoCuenta" name="telefonoCuenta" value="<?=$AccountData['rh_tel']?>"></h5>
            </div>
            <div class="large-4 small-12 columns">
                <h5>Lista de Precios:
                    <select name="pricelist" id="pricelist">
                        <?php foreach($PriceList as $prices){ ?>
                            <option value="<?=$prices['typeabbrev']?>"><?=$prices['sales_type']?></option>
                        <?php } ?>
                    </select>
                </h5>
            </div>
            <div class="large-4 columns">
                <h5>Tipo de persona:
                <select name="tipopersona" id="tipopersona">
                    <option value="MORAL">MORAL</option>
                    <option value="FISICA">FISICA</option>
                </select>
                </h5>
            </div>
        </div>
        <div class="row">
            <div class="large-4 columns">
                <h5>* RFC: <input type="text" id="taxref" name="taxref" value="<?=$AccountData['taxref']?>"></h5>
            </div>
            <div class="large-4 columns">
                <h5>Descuento %: <input type="text" id="discount" name="discount" value="<?=$AccountData['discount']?>"></h5>
            </div>
            <div class="large-4 columns">
                <h5>Limite de Crédito: <input type="text" id="creditlimit" name="creditlimit" value="<?=$AccountData['creditlimit']?>"></h5>
            </div>
        </div>

        <div class="row">
            <div class="large-12 columns">
                <input type="button" class="button tiny input" value="Guardar" id="EditarCliente"/>
                <?php //echo CHtml::submitButton($model->isNewRecord ? 'Crear' : 'Guardar',array('class'=>'button')); ?>
            </div>
        </div>
</div>





