<script type="text/javascript">
    $(document).on('ready', function() {

        $('#_fecha_ultaum').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

        $('#fecha_nacimiento').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $("#sexo").select2();
        $("#braddress7").select2();
        $("#braddress8").select2();
        $("#braddress11").select2();// Se agrego para que la lista de hospitales tenga buscador Angeles Perez 2016-06-09
    });

    function IsNumeric(input){
        if((input - 0) == input && (''+input).replace(/^\s+|\s+$/g, "").length > 0){
            //alert('OK')
        }else{
            alert('Solo se admiten numeros');
        }
    }
</script>

    <div class="container-fluid bootspin">
        <!--Spinners begin-->
        <div class="control-group row-fluid">

            <div class="span6">
                <label class="control-label" for="spin2">
                    <?php echo _('Nombre(s)'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Nombre(s)"><i class="icon-photon info-circle"></i></a>
                </label>
                <input type="text" id="name" name="brname" value=""  style="width: 300pX;" />
            </div>

            <div class="span6">
                <label class="control-label" for="spin2">
                    <?php echo _('Sexo'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Sexo"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <select id="sexo" name="sexo" >
                        <option>MASCULINO</option>
                        <option>FEMENINO</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">

            <div class="span12">
                <label class="control-label" for="spin1">
                    <?php echo _('Nombre Comercial'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Nombre Comercial"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <input type="text" id="nombre_empresa" name="nombre_empresa" style="width: 300pX;" >
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span4">
                <label class="control-label" for="spin1">
                    <?php echo _('Fecha Nacimiento'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Fecha de Nacimiento"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <input type="text" id="fecha_nacimiento" name="fecha_nacimiento" style="width: 120px;"  />
                </div>
            </div>
                    <?php
                    if(empty($_POST['fecha_ingreso'])){
                        $_POST['fecha_ingreso'] = date('Y-m-d');
                    }
                    ?>
            <div class="span4">
                <label class="control-label" for="spin1">
                    <?php echo _('Fecha de Ingreso'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Fecha de Ingreso"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <input type="text" id="fecha_ingreso" readonly="readonly" name="fecha_ingreso" class="required" style="width: 120px;" value="<?=$_POST['fecha_ingreso']?>" />
                </div>
            </div>
            <div class="span4">
                <label class="control-label" for="spin1">
                    <?php echo _('Fecha Ult Aum.'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Fecha ult Aum"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <input type="text" id="fecha_ultaum" name="fecha_ultaum" readonly="readonly" style="width: 120px;" />
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span1">
                <label class="control-label" for="spin1">
                    <?php echo ('Calle'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Calle"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="braddress1" name="braddress1" class="required" style="width: 200px;" value="" />
                </div>
            </div>
            <div class="span1">
                <label class="control-label" for="spin1">
                    <?php echo ('Número'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Numero"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="braddress2" name="braddress2" class="required" style="width: 200px;" value="" />
                </div>
            </div>

            <div class="span2">
                <label class="control-label">
                    <?php echo ('Codigo Postal'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Codigo Postal"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span2">
                <div class="controls">
                    <input type="text" id="braddress10" name="braddress10" class="required" style="width: 80px;" value="" />
                </div>
            </div>

        </div>

        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label" for="spin2">
                    <?php echo ('Colonia'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Colonia"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span4">
                <div class="controls">
                    <input type="text" id="braddress4" name="braddress4" class="required" value="" />
                </div>
            </div>
            <div class="span2">
                <label class="control-label" for="spin2">
                    <?php echo ('Sector'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Sector"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span4">
                <div class="controls">
                    <input type="text" id="braddress5" name="braddress5" class="required" value="" />
                </div>
            </div>
        </div>


        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label" for="spin3">
                    <?php echo _('Entre Calles'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Entre Calles"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span10">
                <div class="controls">
                    <input type="text" id="braddress6" name="braddress6" class="required" value="" />
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label" for="spin3">
                    <?php echo _('Cuadrante'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Cuadrante"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span10">
                <div class="controls">
                    <input type="text" id="cuadrante1" name="cuadrante1" style="width: 80px;" value="" onBlur="IsNumeric(this.value)" />
                    <input type="text" id="cuadrante2" name="cuadrante2" class="mayusculas" style="width: 80px;" value="" />
                    <input type="text" id="cuadrante3" name="cuadrante3" style="width: 80px;" value="" onBlur="IsNumeric(this.value)" />
                    <script>
                        $(document).ready(function(){
                            $('#cuadrante2').mask('?a');
                        });
                    </script>
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label">
                    <?php echo ('Municipio'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Municipio"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span4">
                <div class="controls">
                    <select id="braddress7" name="braddress7" >
                        <option value="">&nbsp;</option>
                        <?php foreach($ListaMunicipios as $id => $Municipio){
                            if(!empty($_POST['braddress7'])){
                                //echo '<option selected="selected" value="'. $_POST['braddress7'] .'">'. $_POST['braddress7'] .'</option>';
                            }
                            ?>
                            <option value="<?=$Municipio?>"><?=$Municipio?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="span2">
                <label class="control-label">
                    <?php echo ('Estado'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Estado"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span4">
                <div class="controls">
                    <select id="braddress8" name="braddress8" class="required" >
                        <option value="">&nbsp;</option>
                        <?php foreach($ListaEstados as $id => $Estado){
                            if(!empty($_POST['braddress8'])){
                                //echo '<option selected="selected" value="'. $_POST['braddress8'] .'">'. $_POST['braddress8'] .'</option>';
                            }
                            ?>
                            <option value="<?=$Estado?>"><?=$Estado?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        <!-- Se agrego para mostrar la lista de Hospitales Angeles Perez 2016-06-09-->
        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label">
                    <?php echo ('Hospital'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Hospital"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span4">
                <div class="controls">
                    <select id="braddress11" name="braddress11" >
                        <option value="">&nbsp;</option>
                        <?php foreach($ListaHospitales as $id => $Hospital){
                            if(!empty($_POST['braddress11'])){
                            //echo '<option selected="selected" value="'. $_POST['braddress11'] .'">'. $_POST['braddress11'] .'</option>';
                            }
                            ?>
                            <option value="<?=$Hospital?>"><?=$Hospital?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <!--Termina-->
       <!--</div>-->

        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label" for="spin3">
                    <?php echo _('Telefono'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Telefono"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span4">
                <div class="controls">
                      <input type="text" id="phoneno" name="phoneno" value="" class="required" />
                </div>
            </div>
        </div>

    </div>
