<script type="text/javascript">
    $(document).on('ready',function() {
        $("#address7").select2();
        $("#address8").select2();
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
            <div class="span1">
                <label class="control-label" for="spin1">
                    <?php echo ('Calle'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Calle"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="address1" name="address1" class="required" style="width: 200px;" value="<?=$_POST['address1']?>" />
                </div>
            </div>
            <div class="span1">
                <label class="control-label" for="spin1">
                    <?php echo ('Número'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Numero"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="address2" name="address2" class="required" style="width: 200px;" value="<?=$_POST['address2']?>" />
                </div>
            </div>

            <div class="span2">
                <label class="control-label">
                    <?php echo ('Codigo Postal'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Codigo Postal"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span2">
                <div class="controls">
                    <input type="text" id="address10" name="address10" class="required" style="width: 80px;" value="<?=$_POST['address10']?>" />
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
                    <input type="text" id="address4" name="address4" class="required span12" value="<?=$_POST['address4']?>"  />
                </div>
            </div>
            <div class="span2">
                <label class="control-label" for="spin2">
                    <?php echo ('Sector'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Sector"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span4">
                <div class="controls">
                    <input type="text" id="address5" name="address5" class="required" value="<?=$_POST['address5']?>" />
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
                    <input type="text" id="address6" name="address6" class="required span6" value="<?=$_POST['address6']?>" />
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
                    <input type="text" id="cuadrante1" name="cuadrante1" class="required" style="width: 80px;" value="<?=$_POST['cuadrante1']?>" onBlur="IsNumeric(this.value)" />
                    <input type="text" id="cuadrante2" name="cuadrante2" class="required mayusculas" style="width: 80px;" value="<?=$_POST['cuadrante2']?>" />
                    <input type="text" id="cuadrante3" name="cuadrante3" class="required" style="width: 80px;" value="<?=$_POST['cuadrante3']?>" onBlur="IsNumeric(this.value)" />
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
                    <select id="address7" name="address7" class="required" >
                        <option value="">SELECCIONE</option>
                        <?php foreach($ListaMunicipios as $id => $Municipio){ ?>
                            <option value="<?=$Municipio?>"><?=$Municipio?></option>
                        <?php } ?>
                    </select>
                    <script type="text/javascript">
                        $("#address7 option[value='<?=$_POST['address7']?>']").attr("selected",true);
                    </script>
                </div>
            </div>

            <div class="span2">
                <label class="control-label">
                    <?php echo ('Estado'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Estado"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span4">
                <div class="controls">
                    <select id="address8" name="address8" class="required" >
                        <option value="">SELECCIONE</option>
                        <?php foreach($ListaEstados as $id => $Estado){ ?>
                            <option value="<?=$Estado?>"><?=$Estado?></option>
                        <?php } ?>
                    </select>
                    <script type="text/javascript">
                        $("#address8 option[value='<?=$_POST['address8']?>']").attr("selected",true);
                    </script>

                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span6">
                <label class="control-label" for="spin2">
                    <?php echo _('Teléfono'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Teléfono"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <input type="text" id="rh_tel" name="rh_tel" class="required" style="width: 150px;" value="<?=$_POST['rh_tel']?>" />
                </div>
            </div>

            <div class="span6">
                <label class="control-label" for="spin2">
                    <?php echo _('Tel. Alterno'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Telefono Alterno"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <input type="text" id="rh_tel2" name="rh_tel2" class="required" style="width: 150px;" value="<?=$_POST['rh_tel2']?>" />
                </div>
            </div>
        </div>

        <style>
            #email{
                width: 250px;
                height: 40px;
            }
            #email:hover {
                position:absolute;
                background-color:#DFF0D8;
                height: 150px;
                width: 250px;
                z-index: 2;
            }
            #contacto{
                z-index: -1;
            }
        </style>

        <div class="control-group row-fluid">
            <div class="span4">
                <label class="control-label" for="spin2">
                    <?php echo _('E-mail'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="E-mail"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <textarea id="email" name="email" class="span8 required" rows="1" ><?=$_POST['email']?></textarea>
                </div>
            </div>
            <?php
                if ($_POST['enviar_factura'] == 1) {
                    $EnvF = "checked='checked' ";
                } else {
                    $EnvF = "";
                }
            ?>
            <div class="span3">
                <label class="control-label" for="spin2">
                    <?php echo _('Enviar Factura'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Enviar Factura"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <input type="checkbox" id="enviar_factura" name="enviar_factura" <?=$EnvF?> value="1" />
                </div>
            </div>

            <div class="span5">
                <label class="control-label" for="spin2">
                    <?php echo _('Encargado de  Pagos'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Encargado de  Pagos"><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <input type="text" id="encargado_pagos" name="encargado_pagos" style="width: 150px;" value="<?=$_POST['encargado_pagos']?>" />
                </div>
            </div>
        </div>

    </div>
