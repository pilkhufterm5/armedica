<script type="text/javascript">
    $(document).on('ready', function() {
        $('#fecha_ultaum').datepicker({
            dateFormat : 'yy-mm-dd'
        });

        $("#identificacion").select2();
        $("#tipo_tarjeta").select2();
        $("#metodo_pago").select2();
        //$("#dias_cobro_pordia").select2();
        //$("#dias_cobro_dia").select2();
        //$("#dias_revision_pordia").select2();
        //$("#dias_revision_dia").select2();

        // $('.TarjetaC').hide();
        // $('#forma_pago').change(function(){
        //     if(($('#forma_pago').val() == 7) || ($('#forma_pago').val() == 9) || ($('#forma_pago').val() == 10)){
        //         $('.TarjetaC').show();
        //     }else{
        //         $('.TarjetaC').hide();
        //     }
        // });


        $('#dias_cobro_pordia').change(function(){
            if($('#dias_cobro_pordia').val() == 'Por Numero'){
                var input = "<input type='text' name='dias_cobro_dia' id='dias_cobro_dia' style='width: 120px; margin-bottom: 0px;' >";
                $('#DiaORNum1').html(input);
            }else{
                var input = '<select id="dias_cobro_dia" name="dias_cobro_dia" class="required" style="width: 100px; " ><option value="">Dia</option><option value="1">L</option><option value="2">M</option><option value="3">M</option><option value="4">J</option><option value="5">V</option></select>';
                var js ='';
                $('#DiaORNum1').html(input);
            }
        });

        $('#dias_revision_pordia').change(function(){
            if($('#dias_revision_pordia').val() == 'Por Numero'){
                var input = "<input type='text' name='dias_revision_dia' id='dias_revision_dia' style='width: 120px; margin-bottom: 0px;' >";
                $('#DiaORNum2').html(input);
            }else{
                var input = '<select id="dias_revision_dia" name="dias_revision_dia" class="required" style="width: 100px; " ><option value="">Dia</option><option value="1">L</option><option value="2">M</option><option value="3">M</option><option value="4">J</option><option value="5">V</option></select>';
                var js ='';
                $('#DiaORNum2').html(input);
            }
        });
    });
</script>

    <div class="container-fluid bootspin">
        <!--Spinners begin-->
        <div class="control-group row-fluid " >
            <div class="span2 TarjetaC">
                <label class="control-label" for="spin1">
                    <?php echo _('Cuenta'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Cuenta"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span3 TarjetaC" >
                <div class="controls">
                    <input type="text" id="cuenta" name="cuenta"  style="width: 150px;" value="<?=$_POST['cuenta']?>" />
                </div>
            </div>


            <div class="span4">
                <label class="control-label" for="spin2">
                    <?php echo _('Cobrador'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Cobrador"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <select id="cobrador" name="cobrador" class="required">
                        <option value="">SELECCIONE</option>
                        <?php foreach($ListaCobradores as $id => $Name){ ?>
                            <option value="<?=$id?>"><?=$Name?></option>
                        <?php } ?>
                    </select>
                    <script type="text/javascript">
                        $("#cobrador option[value='<?=$_POST['cobrador']?>']").attr("selected",true);
                    </script>
                </div>
            </div>

            <div class="span3">
                <label class="control-label" for="spin2">
                    <?php echo _('Zona'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Cobrador"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <input type="text" id="zona" name="zona" value="<?=$_POST['zona']?>" style="width: 150px;" />
                </div>
            </div>

            <!-- <div class="span6"></div> -->
        </div>

        <div class="control-group row-fluid">
            <div class="span2 TarjetaC">
                <label class="control-label" for="spin1">
                    <?php echo _('Vencimiento'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Vencimiento"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span2 TarjetaC">
                <div class="controls">
                    <input type="text" id="vencimiento" name="vencimiento" style="width: 150px;" value="<?=$_POST['vencimiento']?>" />
                </div>
            </div>

            <div class="span2">
                <label class="control-label" for="spin1">
                    <?php echo _('Cuenta SAT'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Cuenta SAT"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="cuenta_sat" name="cuenta_sat" style="width: 120px;" value="<?=$_POST['cuenta_sat']?>" />
                </div>
            </div>
            <div class="span3">
                <label class="control-label" for="spin1">
                    <?php echo _('Banco'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Banco"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <input type="text" id="rh_banco" name="rh_banco" style="width: 120px;" value="<?=$_POST['rh_banco']?>" />
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span4 TarjetaC">
                <label class="control-label" for="spin1">
                    <?php echo _('Número de Plastico'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Número de Plastico"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <input type="text" id="num_plastico" name="num_plastico" style="width: 150px;" value="<?=$_POST['num_plastico']?>" />
                </div>
            </div>
            <!--Se agrego para el cambio del metodo de pago SAT Angeles Perez, Daniel Villarreal 12/07/2016 -->
<script type='text/javascript'>
    $(document).ready(function(){
        
        $(document).on('change', '#metodo_pago', function() {
             
            // obtenemos el valor del data-satid
         
            var satid = $(this).find(':selected').attr('data-satid')
            // alert(satid);
            $("#satid").val(satid);
        });
    });  
</script>

        <div class="span4">
                <label class="control-label" for="spin2">
                    <?php echo _('Metodo de Pago'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Metodo de Pago"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <select name="metodo_pago"   id="metodo_pago" class="required">
                        <option value="">SELECCIONE</option>
                                <?php foreach ($ListaMetodoPago as $rows) { ?>
                                <option value="<?=$rows['satname']?>" data-satid="<?=$rows['satid']?>">
                                    <?=$rows['satname']?>
                                </option>
                                <?php } ?>
                            </select>
                    <script type="text/javascript">
                        $("#metodo_pago option[value='<?=$_POST['metodo_pago']?>']").attr("selected",true);
                    </script>
                </div>
            </div>
        </div>

         <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label" for="spin1">
                    <?php echo _('Método de pago SAT'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Método de pago SAT"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span4">
                <div class="controls">
                    <input type="text" id="satid" readonly="readonly" name="satid" style="width: 120px;" value="<?=$_POST['satid']?>" />
                </div>
            </div>
        </div>
<!--Termina-->
        <div class="control-group row-fluid TarjetaC">
            <div class="span2">
                <label class="control-label" for="spin1">
                    <?php echo _('Tipo de Tarjeta'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Tipo de Tarjeta"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span2">
                <div class="controls">
                    <select id="tipo_tarjeta" name="tipo_tarjeta" >
                        <option value="">SELECCIONE</option>
                        <?php foreach($ListaTipoTarjetas as $id => $Name){ ?>
                            <option value="<?=$id?>"><?=$Name?></option>
                        <?php } ?>
                    </select>
                    <script type="text/javascript">
                        $("#tipo_tarjeta option[value='<?=$_POST['tipo_tarjeta']?>']").attr("selected",true);
                    </script>
                </div>
            </div>
            <div class="span2">
                <label class="control-label" for="spin1">
                    <?php echo _('Tipo de Cuenta'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Tipo de Cuenta"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span6">
                <div class="controls">
                    <input type="text" id="tipo_cuenta" name="tipo_cuenta" value="<?=$_POST['tipo_cuenta']?>" />
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span12"></div>
        </div>

        <div class="control-group row-fluid">
            <div class="span6">
                <label class="control-label" for="spin1">
                    <?php echo _('Numero Empleado'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Numero de Empleado"><i class="icon-photon info-circle"></i></a>
                </label>
                <input type="text" id="num_empleado" name="num_empleado" value="<?=$_POST['num_empleado']?>" style="width: 120px;" />
            </div>

            <div class="span6">
                <label class="control-label" for="spin1">
                    <?php echo _('Identificación'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Identificación"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <select id="identificacion" name="identificacion" class="required" >
                        <option value="">SELECCIONE</option>
                        <?php foreach($ListaIdentificaciones as $Name){ ?>
                            <option value="<?=$Name['id']?>"><?=$Name['nombre']?></option>
                        <?php } ?>
                        <?php /*
                        <!-- <option value="IFE">IFE</option>
                        <option value="PASAPORTE">PASAPORTE</option>
                        <option value="ACTA">ACTA</option> -->*/ ?>
                    </select>
                    <script type="text/javascript">
                        $("#identificacion option[value='<?=$_POST['identificacion']?>']").attr("selected",true);
                    </script>
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label" for="spin2">
                    <?php echo _('Fecha Corte'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Fecha de Corte"><i class="icon-photon info-circle"></i></a>
                </label>

            </div>
            <div class="span2">
                <div class="controls">
                    <input type="text" id="fecha_corte" name="fecha_corte" readonly="readonly"  <?php (($TipoMembresia == "Socio"))?'class="required"':''; ?> value="<?=$_POST['fecha_corte']?>" style="width: 120px;" />
                </div>
            </div>
            <div class="span8"></div>
        </div>
            <?php
                if ($_POST['factura_fisica'] == 1) {
                    $EnvFF = "checked='checked' ";
                } else {
                    $EnvFF = "";
                }
            ?>
        <div class="control-group row-fluid">
            <div class="span6">
                <label class="control-label" for="spin1">
                    <?php echo _('Requiere Factura'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Requiere Factura Fisica?"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <input type="checkbox" id="factura_fisica" name="factura_fisica" <?=$EnvFF?> value="1" >
                </div>
            </div>

            <div class="span6">
                <label class="control-label" for="spin1">
                    <?php echo _('Folio Asociado'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Folio Asociado"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <input type="text" id="folio_asociado" name="folio_asociado" value="<?=$_POST['folio_asociado']?>" style="width: 120px;" />
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span6">
                <label class="control-label" for="spin2">
                    <?php echo _('Dias de Cobro'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="RFC"><i class="icon-photon info-circle"></i></a>
                </label>

                <select id="dias_cobro_pordia" name="dias_cobro" class="required" >
                    <option value="Por Dia">Por Día</option>
                    <option value="Por Numero">Por Número</option>
                </select>
                <script type="text/javascript">
                    $("#dias_cobro_pordia option[value='<?=$_POST['dias_cobro']?>']").attr("selected",true);
                </script>
                <ChangeElement id="DiaORNum1" >
                <?php if($_POST['dias_cobro'] =='Por Numero'){ ?>
                    <input type="text" name="dias_cobro_dia" id="dias_cobro_dia" style="width: 120px; margin-bottom: 0px;" value="<?=$_POST['dias_cobro_dia']?>" >
                <?php }else{ ?>
                <select id="dias_cobro_dia" name="dias_cobro_dia" >
                    <option value="">Dia</option>
                    <option value="1" >L</option>
                    <option value="2" >M</option>
                    <option value="3" >M</option>
                    <option value="4" >J</option>
                    <option value="5" >V</option>
                </select>
                <script type="text/javascript">
                    $("#dias_cobro_dia option[value='<?=$_POST['dias_cobro_dia']?>']").attr("selected",true);
                </script>
                <?php } ?>
                </ChangeElement>
            </div>

            <div class="span3">
                <label class="control-label">De:</label>
                <div class="controls">
                    <input type="text" id="cobro_datefrom" name="cobro_datefrom" value="<?=$_POST['cobro_datefrom']?>" style="width: 100px;" />
                </div>
            </div>
            <div class="span3">
                <label class="control-label">a:</label>
                <div class="controls">
                    <input type="text" id="cobro_dateto" name="cobro_dateto" value="<?=$_POST['cobro_dateto']?>" style="width: 100px;" />
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span6">
                <label class="control-label" for="spin2">
                    <?php echo _('Dias de Credito'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Dias de Credito"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <input type="text" id="dias_credito" name="dias_credito" value="<?=$_POST['dias_credito']?>" style="width: 100px;" />
                </div>
            </div>
            <div class="span6">
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span6">
                <label class="control-label" for="spin2">
                    <?php echo _('Dias de Revision'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Dias de Revision"><i class="icon-photon info-circle"></i></a>
                </label>
                <select id="dias_revision_pordia" name="dias_revision" >
                    <option value="Por Dia">Por Día</option>
                    <option value="Por Numero">Por Número</option>
                </select>
                <script type="text/javascript">
                    $("#dias_revision_pordia option[value='<?=$_POST['dias_revision']?>']").attr("selected",true);
                </script>

                <ChangeElement id="DiaORNum2" >
                <?php if($_POST['dias_revision'] =='Por Numero'){ ?>
                    <input type="text" name="dias_revision_dia" id="dias_revision_dia" style="width: 120px; margin-bottom: 0px;" value="<?=$_POST['dias_revision_dia']?>" >
                <?php }else{ ?>
                <select id="dias_revision_dia" name="dias_revision_dia" >
                    <option value="">Dia</option>
                    <option value="1" >L</option>
                    <option value="2" >M</option>
                    <option value="3" >M</option>
                    <option value="4" >J</option>
                    <option value="5" >V</option>
                </select>
                <script type="text/javascript">
                    $("#dias_revision_dia option[value='<?=$_POST['dias_revision_dia']?>']").attr("selected",true);
                </script>
                <?php } ?>
                </ChangeElement>
            </div>


            <div class="span3">
                <label class="control-label">De:</label>
                <div class="controls">
                    <input type="text" id="revision_datefrom" name="revision_datefrom" value="<?=$_POST['revision_datefrom']?>" style="width: 100px;" />
                </div>
            </div>
            <div class="span3">
                <label class="control-label">a:</label>
                <div class="controls">
                    <input type="text" id="revision_dateto" name="revision_dateto" value="<?=$_POST['revision_dateto']?>" style="width: 100px;" />
                </div>
            </div>
        </div>
    </div>
<script>
    if($('#forma_pago').val() == 8){
        $('.TarjetaC').hide();
    }
</script>
